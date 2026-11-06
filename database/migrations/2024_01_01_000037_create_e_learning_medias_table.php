<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('e_learning_medias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_e_learning')->nullable()->constrained('e_learning_materials')->onDelete('cascade');
            $table->string('foto', 100)->nullable();
            $table->string('video', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('e_learning_medias');
    }
};
