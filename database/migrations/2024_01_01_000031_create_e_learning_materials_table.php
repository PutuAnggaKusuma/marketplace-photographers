<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('e_learning_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_admin')->nullable()->constrained('role_admins')->onDelete('cascade');
            $table->string('judul', 100)->nullable();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('e_learning_materials');
    }
};