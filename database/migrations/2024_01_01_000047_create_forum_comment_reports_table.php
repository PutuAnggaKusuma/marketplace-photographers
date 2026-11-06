<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forum_comment_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_comment')->constrained('forum_comments')->onDelete('cascade');
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $table->string('kategori', 50)->default('spam');
            $table->text('alasan')->nullable();
            $table->enum('status', ['pending', 'reviewed', 'dismissed'])->default('pending');
            $table->timestamps();

            $table->unique(['id_comment', 'id_user']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_comment_reports');
    }
};
