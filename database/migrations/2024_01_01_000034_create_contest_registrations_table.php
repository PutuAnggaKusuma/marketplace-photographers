<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contest_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_contest')->constrained('photo_contests')->onDelete('cascade');
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $table->string('nama_lengkap', 255);
            $table->string('email', 255);
            $table->string('no_wa', 255);
            $table->string('asal_kota', 255)->nullable();
            $table->string('instansi', 255)->nullable();
            $table->text('bio_singkat')->nullable();
            $table->text('motivasi')->nullable();
            $table->string('cv_portfolio_file', 255)->nullable();
            $table->string('portfolio_link', 255)->nullable();
            $table->enum('status_pendaftaran', ['menunggu_verifikasi', 'disetujui', 'ditolak'])->default('menunggu_verifikasi');
            $table->enum('status_pembayaran', ['gratis', 'menunggu_pembayaran', 'menunggu_konfirmasi', 'lunas'])->default('gratis');
            $table->string('bukti_transfer', 255)->nullable();
            $table->dateTime('waktu_batas_pembayaran')->nullable();
            $table->text('catatan_admin')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contest_registrations');
    }
};
