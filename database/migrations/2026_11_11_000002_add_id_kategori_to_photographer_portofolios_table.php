<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom id_kategori pada photographer_portofolios & backfill data lama secara aman.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('photographer_portofolios', 'id_kategori')) {
            Schema::table('photographer_portofolios', function (Blueprint $table) {
                $table->foreignId('id_kategori')
                    ->nullable()
                    ->after('id_photographer')
                    ->constrained('categories')
                    ->nullOnDelete();
            });
        }

        $allCategories = DB::table('categories')
            ->whereNull('deleted_at')
            ->orderBy('id')
            ->get(['id', 'nama_kategori']);

        if ($allCategories->isEmpty()) {
            return;
        }

        $portfolios = DB::table('photographer_portofolios')
            ->whereNull('id_kategori')
            ->orderBy('id')
            ->get(['id', 'id_photographer', 'judul', 'deskripsi']);

        foreach ($portfolios as $portfolio) {
            $profileCategories = DB::table('photographer_categories')
                ->join('categories', 'categories.id', '=', 'photographer_categories.id_category')
                ->where('photographer_categories.id_photographer', $portfolio->id_photographer)
                ->whereNull('categories.deleted_at')
                ->orderBy('photographer_categories.id')
                ->select('categories.id', 'categories.nama_kategori')
                ->get();

            $candidates = $profileCategories->isNotEmpty() ? $profileCategories : $allCategories;
            $haystack = strtolower(trim(($portfolio->judul ?? '') . ' ' . ($portfolio->deskripsi ?? '')));

            $bestCategoryId = null;
            $bestScore = 0;

            foreach ($candidates as $category) {
                $score = $this->scoreCategoryMatch($haystack, strtolower($category->nama_kategori));
                if ($score > $bestScore) {
                    $bestScore = $score;
                    $bestCategoryId = (int) $category->id;
                }
            }

            if ($bestCategoryId === null && $profileCategories->isNotEmpty()) {
                $bestCategoryId = (int) $profileCategories->first()->id;
            }

            if ($bestCategoryId !== null) {
                DB::table('photographer_portofolios')
                    ->where('id', $portfolio->id)
                    ->update(['id_kategori' => $bestCategoryId]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('photographer_portofolios', 'id_kategori')) {
            Schema::table('photographer_portofolios', function (Blueprint $table) {
                $table->dropConstrainedForeignId('id_kategori');
            });
        }
    }

    private function scoreCategoryMatch(string $haystack, string $categoryName): int
    {
        if ($haystack === '') {
            return 0;
        }

        $score = 0;
        $tokens = preg_split('/[\s,&\/-]+/', $categoryName, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($tokens as $token) {
            if (strlen($token) >= 3 && str_contains($haystack, $token)) {
                $score += 3;
            }
        }

        $keywordGroups = [
            'wedding' => ['nikah', 'pernikahan', 'wedding', 'prewedding', 'marriage', 'bride', 'groom', 'gaun', 'melasti'],
            'prewedding' => ['nikah', 'pernikahan', 'wedding', 'prewedding', 'marriage', 'gaun'],
            'marriage' => ['nikah', 'pernikahan', 'wedding', 'prewedding', 'marriage'],
            'event' => ['event', 'acara', 'festival', 'konser', 'concert', 'music', 'musik', 'summit', 'konferensi', 'showcase', 'stage', 'panggung'],
            'konser' => ['konser', 'concert', 'music', 'musik', 'festival', 'showcase', 'panggung'],
            'concert' => ['konser', 'concert', 'music', 'musik', 'festival', 'showcase', 'panggung'],
            'commercial' => ['commercial', 'produk', 'product', 'kuliner', 'culinary', 'katalog', 'kemasan', 'makanan', 'umkm', 'fashion'],
            'produk' => ['produk', 'product', 'kuliner', 'culinary', 'katalog', 'kemasan', 'makanan', 'umkm'],
            'portrait' => ['portrait', 'potret', 'studio', 'personal', 'klasik'],
            'studio' => ['portrait', 'potret', 'studio', 'indoor'],
            'travel' => ['travel', 'holiday', 'vacation', 'liburan', 'pesisir', 'penida', 'uluwatu', 'exploring', 'wisata'],
            'vacation' => ['travel', 'holiday', 'vacation', 'liburan', 'pesisir', 'penida', 'uluwatu', 'exploring', 'wisata'],
            'holiday' => ['travel', 'holiday', 'vacation', 'liburan', 'pesisir', 'penida', 'uluwatu', 'exploring', 'wisata'],
            'graduation' => ['wisuda', 'graduation', 'kelulusan'],
            'wisuda' => ['wisuda', 'graduation', 'kelulusan'],
            'family' => ['keluarga', 'family'],
            'birthday' => ['ulang tahun', 'birthday'],
            'baby' => ['bayi', 'baby', 'maternity', 'hamil', 'newborn'],
            'maternity' => ['bayi', 'baby', 'maternity', 'hamil'],
        ];

        foreach ($tokens as $token) {
            if (!isset($keywordGroups[$token])) {
                continue;
            }
            foreach ($keywordGroups[$token] as $kw) {
                if (str_contains($haystack, $kw)) {
                    $score += 2;
                }
            }
        }

        return $score;
    }
};

