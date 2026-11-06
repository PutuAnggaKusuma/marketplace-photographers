<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('elearning_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_elearning')->constrained('elearnings')->onDelete('cascade');
            $table->string('judul_modul', 255);
            $table->text('deskripsi')->nullable();
            $table->integer('urutan')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('elearning_modules');
    }
};
