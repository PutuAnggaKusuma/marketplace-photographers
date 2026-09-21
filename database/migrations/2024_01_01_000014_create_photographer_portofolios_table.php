<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('photographer_portofolios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_photographer')->nullable()->constrained('role_photographers')->onDelete('cascade');
            $table->string('judul', 100)->nullable();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('photographer_portofolios');
    }
};