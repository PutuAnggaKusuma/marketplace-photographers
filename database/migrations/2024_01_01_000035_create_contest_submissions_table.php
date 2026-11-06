<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contest_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_contest')->constrained('photo_contests')->onDelete('cascade');
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $table->foreignId('id_registration')->nullable()->constrained('contest_registrations')->nullOnDelete();
            $table->string('judul_karya', 255);
            $table->text('deskripsi_karya')->nullable();
            $table->string('image_url', 255);
            $table->string('media_link', 255)->nullable();
            $table->enum('status_submission', ['pending', 'verified', 'winner_1', 'winner_2', 'winner_3', 'rejected'])->default('verified');
            $table->text('catatan_juri')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contest_submissions');
    }
};
