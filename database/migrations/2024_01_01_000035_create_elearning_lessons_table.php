<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('elearning_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_elearning')->constrained('elearnings')->onDelete('cascade');
            $table->foreignId('id_module')->nullable()->constrained('elearning_modules')->onDelete('cascade');
            $table->string('judul', 255);
            $table->string('tipe', 50)->default('reading'); // video, reading, audio, foto
            $table->longText('konten')->nullable();
            $table->string('media_type', 50)->nullable(); // youtube, external, upload
            $table->string('media_url', 1000)->nullable();
            $table->string('media_path', 255)->nullable();
            $table->integer('durasi_menit')->default(5);
            $table->integer('urutan')->default(1);
            $table->boolean('is_free')->default(true); // true = gratis (free preview), false = berbayar
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('elearning_lessons');
    }
};