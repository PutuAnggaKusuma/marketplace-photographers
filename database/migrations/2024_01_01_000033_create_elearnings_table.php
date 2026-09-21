<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('elearnings', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 255);
            $table->string('slug', 255)->unique();
            $table->string('kategori', 255)->default('Teknik Fotografi');
            $table->enum('level', ['Pemula', 'Menengah', 'Mahir'])->default('Pemula');
            $table->string('durasi', 255)->default('30 Menit');
            $table->unsignedBigInteger('harga')->default(0);
            $table->boolean('is_premium')->default(false);
            $table->string('thumbnail_url', 255)->nullable();
            $table->text('ringkasan')->nullable();
            $table->longText('konten')->nullable();
            $table->integer('view_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('elearnings');
    }
};