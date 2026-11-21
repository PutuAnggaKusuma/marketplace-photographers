<?php

$viewsPath = realpath(__DIR__ . '/../resources/views');
$errors = [];

function checkBladeFile($filePath) {
    global $errors;
    $content = file_get_contents($filePath);
    $relPath = str_replace(realpath(__DIR__ . '/../') . DIRECTORY_SEPARATOR, '', $filePath);
    $lines = explode("\n", $content);

    foreach ($lines as $lineNum => $line) {
        $num = $lineNum + 1;

        // 1. Check dot status badge on status indicators (Rule #67 Item 4 & Rule #14)
        // e.g. <span class="...rounded-full bg-emerald-500"></span> beside status words
        if (preg_match('/<span[^>]*class="[^"]*rounded-full\s+bg-(?:emerald|amber|rose|blue|green|red)-\d+[^"]*"[^>]*>\s*<\/span>\s*(?:Terverifikasi|Belum Verifikasi|Aktif|Dibekukan|Pending|Approved|Completed|Cancelled|Tersedia|Terisi Sesi|Libur)/i', $line)) {
            $errors[] = "[$relPath:$num] Melanggar Rule #67 (Bulatan Status / Dot Badge pada status): " . trim($line);
        }

        // 2. Check forbidden button texts in table row actions or headers (Rule #80)
        if (preg_match('/>\s*(Edit Data|Lihat Detail|Lihat Data|Hapus Data|Hapus Akun)\s*<\/(?:span|button|a)>/i', $line, $m)) {
            if (preg_match('/resources[\/\\\\]views[\/\\\\]admin[\/\\\\]admins/i', $filePath) && preg_match('/Edit Data|Lihat Detail|Lihat Data/i', $m[1])) {
                $errors[] = "[$relPath:$num] Melanggar Rule #80 (Teks tombol aksi dilarang berobjek '{$m[1]}'): " . trim($line);
            }
        }
    }
}

function scanDirRecursive($dir) {
    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $path = $dir . '/' . $item;
        if (is_dir($path)) {
            scanDirRecursive($path);
        } elseif (str_ends_with($path, '.blade.php')) {
            checkBladeFile($path);
        }
    }
}

scanDirRecursive($viewsPath);

if (!empty($errors)) {
    echo "========================================================\n";
    echo "GAGAL: Ditemukan " . count($errors) . " pelanggaran aturan UI / Anti-AI Slop!\n";
    echo "========================================================\n";
    foreach ($errors as $err) {
        echo "- $err\n";
    }
    exit(1);
} else {
    echo "LULUS: Seluruh berkas Blade mematuhi standar UI Anti-AI Slop (0 pelanggaran).\n";
    exit(0);
}
