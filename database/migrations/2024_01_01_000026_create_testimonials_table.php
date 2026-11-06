<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_client')->nullable()->constrained('role_clients')->onDelete('cascade');
            $table->foreignId('id_photographer')->nullable()->constrained('role_photographers')->onDelete('cascade');
            $table->foreignId('id_contract')->nullable()->constrained('contracts')->onDelete('cascade');
            $table->string('deskripsi_review', 255)->nullable();
            $table->tinyInteger('rating')->nullable();
            $table->boolean('is_hidden')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
