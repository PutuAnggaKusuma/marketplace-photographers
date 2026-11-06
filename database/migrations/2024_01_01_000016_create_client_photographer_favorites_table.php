<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('client_photographer_favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_client')->constrained('role_clients')->onDelete('cascade');
            $table->foreignId('id_photographer')->constrained('role_photographers')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['id_client', 'id_photographer'], 'client_photo_fav_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_photographer_favorites');
    }
};
