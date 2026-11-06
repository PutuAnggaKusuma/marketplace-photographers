<?php

namespace App\Services;

class WatermarkService
{
    /**
     * Terapkan watermark semi-transparan elegan pada berkas gambar yang tersimpan.
     *
     * @param string $fullPath Absolute path ke file gambar di storage
     * @param string $authorName Nama fotografer mitra
     * @return bool
     */
    public static function applyWatermark(string $fullPath, string $authorName = 'LensMatch Verified'): bool
    {
        if (!file_exists($fullPath) || !extension_loaded('gd')) {
            return false;
        }

        try {
            $info = getimagesize($fullPath);
            if (!$info) {
                return false;
            }

            $mime = $info['mime'];
            switch ($mime) {
                case 'image/jpeg':
                case 'image/jpg':
                    $image = @imagecreatefromjpeg($fullPath);
                    break;
                case 'image/png':
                    $image = @imagecreatefrompng($fullPath);
                    break;
                case 'image/webp':
                    $image = @imagecreatefromwebp($fullPath);
                    break;
                default:
                    return false;
            }

            if (!$image) {
                return false;
            }

            $width = imagesx($image);
            $height = imagesy($image);

            // Teks watermark
            $text = "© LensMatch • " . trim($authorName);
            $fontSize = max(3, min(5, (int) round($width / 350))); // GD built-in font size 3 to 5

            $fontWidth = imagefontwidth($fontSize);
            $fontHeight = imagefontheight($fontSize);
            $textWidth = strlen($text) * $fontWidth;
            $textHeight = $fontHeight;

            $paddingX = 14;
            $paddingY = 8;
            $badgeWidth = $textWidth + ($paddingX * 2);
            $badgeHeight = $textHeight + ($paddingY * 2);

            $margin = 20;
            $badgeX = $width - $badgeWidth - $margin;
            $badgeY = $height - $badgeHeight - $margin;

            if ($badgeX < 0 || $badgeY < 0) {
                imagedestroy($image);
                return false;
            }

            // Aktifkan alpha blending
            imagealphablending($image, true);
            imagesavealpha($image, true);

            // Buat badge semi-transparan gelap di pojok kanan bawah
            $badgeBg = imagecolorallocatealpha($image, 17, 24, 39, 45); // Dark slate rgba with ~65% opacity
            $badgeBorder = imagecolorallocatealpha($image, 251, 191, 36, 60); // Amber-400 accent border
            $textColor = imagecolorallocate($image, 255, 255, 255); // White text

            // Gambar rounded / rectangle badge
            imagefilledrectangle($image, $badgeX, $badgeY, $badgeX + $badgeWidth, $badgeY + $badgeHeight, $badgeBg);
            imagerectangle($image, $badgeX, $badgeY, $badgeX + $badgeWidth, $badgeY + $badgeHeight, $badgeBorder);

            // Tulis teks watermark di dalam badge
            imagestring($image, $fontSize, $badgeX + $paddingX, $badgeY + $paddingY, $text, $textColor);

            // Simpan gambar kembali
            switch ($mime) {
                case 'image/jpeg':
                case 'image/jpg':
                    imagejpeg($image, $fullPath, 92);
                    break;
                case 'image/png':
                    imagepng($image, $fullPath, 8);
                    break;
                case 'image/webp':
                    imagewebp($image, $fullPath, 90);
                    break;
            }

            imagedestroy($image);
            return true;
        } catch (\Throwable $e) {
            // Jika ada kendala memori atau format, jangan gagalkan proses utama
            return false;
        }
    }
}

