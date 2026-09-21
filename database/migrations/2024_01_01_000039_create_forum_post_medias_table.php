<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forum_post_medias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_forum_post')->nullable()->constrained('forum_posts')->onDelete('cascade');
            $table->string('foto', 100)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_post_medias');
    }
};