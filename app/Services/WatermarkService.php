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

            $margin = 24;
            $textX = $width - $textWidth - $margin;
            $textY = $height - $textHeight - $margin;

            if ($textX < 0 || $textY < 0) {
                imagedestroy($image);
                return false;
            }

            // Aktifkan alpha blending
            imagealphablending($image, true);
            imagesavealpha($image, true);

            // Murni tipografi anti-AI slop (NO BADGE, NO RECTANGLE, NO BOX)
            $shadowColor = imagecolorallocatealpha($image, 0, 0, 0, 60);
            $textColor = imagecolorallocatealpha($image, 255, 255, 255, 30);

            // Bayangan halus 1px
            imagestring($image, $fontSize, $textX + 1, $textY + 1, $text, $shadowColor);
            // Teks utama watermark
            imagestring($image, $fontSize, $textX, $textY, $text, $textColor);

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

