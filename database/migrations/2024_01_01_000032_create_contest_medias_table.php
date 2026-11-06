<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contest_medias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_photo_contest')->nullable()->constrained('photo_contests')->onDelete('cascade');
            $table->string('foto', 100)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contest_medias');
    }
};
