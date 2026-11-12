<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('photographer_working_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_photographer')->constrained('role_photographers')->onDelete('cascade');
            $table->tinyInteger('day_of_week'); // 1 = Senin, 2 = Selasa, ..., 7 = Minggu (ISO-8601)
            $table->string('day_name', 20); // 'Senin', 'Selasa', dll.
            $table->boolean('is_open')->default(false);
            $table->boolean('is_24_hours')->default(false);
            $table->time('jam_buka')->default('08:00:00');
            $table->time('jam_tutup')->default('17:00:00');
            $table->timestamps();

            $table->unique(['id_photographer', 'day_of_week'], 'photo_day_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('photographer_working_hours');
    }
};
