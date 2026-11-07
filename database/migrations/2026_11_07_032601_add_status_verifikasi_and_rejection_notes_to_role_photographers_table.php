<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('role_photographers', function (Blueprint $table) {
            $table->enum('status_verifikasi', ['menunggu', 'terverifikasi', 'ditolak'])->default('menunggu')->after('is_verified');
            $table->text('catatan_penolakan')->nullable()->after('status_verifikasi');
            $table->string('kategori_penghapusan')->nullable()->after('catatan_penolakan');
            $table->text('alasan_penghapusan')->nullable()->after('kategori_penghapusan');
        });

        // Sinkronisasi data lama berdasarkan is_verified
        DB::table('role_photographers')->where('is_verified', true)->update(['status_verifikasi' => 'terverifikasi']);
        DB::table('role_photographers')->where('is_verified', false)->update(['status_verifikasi' => 'menunggu']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('role_photographers', function (Blueprint $table) {
            $table->dropColumn(['status_verifikasi', 'catatan_penolakan', 'kategori_penghapusan', 'alasan_penghapusan']);
        });
    }
};
