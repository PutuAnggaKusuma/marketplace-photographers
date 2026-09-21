<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('e_learning_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('id_e_learning')->nullable()->constrained('elearnings')->onDelete('cascade');
            $table->enum('status', ['belum_mulai', 'sedang_belajar', 'selesai'])->default('belum_mulai');
            $table->unsignedTinyInteger('progress_percentage')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('e_learning_progress');
    }
};