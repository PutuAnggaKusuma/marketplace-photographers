<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role_photographers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('nama', 100)->nullable();
            $table->string('nomor_telepon', 20)->nullable();
            $table->string('link_sosmed', 100)->nullable();
            $table->string('foto', 100)->nullable();
            $table->string('foto_sampul', 255)->nullable();
            $table->text('alamat')->nullable();
            $table->char('province_code', 2)->nullable();
            $table->char('city_code', 4)->nullable();
            $table->text('deskripsi_bio')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_photographers');
    }
};