<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('elearning_lesson_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $table->foreignId('id_elearning')->constrained('elearnings')->onDelete('cascade');
            $table->foreignId('id_lesson')->constrained('elearning_lessons')->onDelete('cascade');
            $table->boolean('is_completed')->default(true);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['id_user', 'id_lesson']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('elearning_lesson_progress');
    }
};
