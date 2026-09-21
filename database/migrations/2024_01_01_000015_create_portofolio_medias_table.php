<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portofolio_medias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_portofolio')->nullable()->constrained('photographer_portofolios')->onDelete('cascade');
            $table->string('media', 100)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portofolio_medias');
    }
};