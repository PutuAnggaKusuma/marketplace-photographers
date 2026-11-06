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
        Schema::create('photographer_client_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_contract')->unique()->constrained('contracts')->onDelete('cascade');
            $table->foreignId('id_photographer')->constrained('role_photographers')->onDelete('cascade');
            $table->foreignId('id_client')->constrained('role_clients')->onDelete('cascade');
            $table->unsignedTinyInteger('rating')->default(5); // 1-5 bintang
            $table->text('ulasan')->nullable();
            $table->unsignedTinyInteger('ketepatan_waktu')->default(5); // 1-5
            $table->unsignedTinyInteger('komunikasi')->default(5);     // 1-5
            $table->unsignedTinyInteger('kerjasama')->default(5);      // 1-5
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photographer_client_reviews');
    }
};
