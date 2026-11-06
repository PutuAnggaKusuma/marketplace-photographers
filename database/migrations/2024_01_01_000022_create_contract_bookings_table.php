<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contract_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_contract')->nullable()->constrained('contracts')->onDelete('cascade');
            $table->foreignId('id_service')->nullable()->constrained('photographer_services')->onDelete('cascade');
            $table->date('booking_date')->nullable();
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();
            $table->string('lokasi', 100)->nullable();
            $table->char('province_code', 2)->nullable();
            $table->char('city_code', 4)->nullable();
            $table->text('catatan_khusus')->nullable();
            $table->text('hasil_foto_url')->nullable();
            $table->text('catatan_fotografer')->nullable();
            $table->string('durasi', 100)->nullable();
            $table->enum('reschedule_by', ['client', 'photographer'])->nullable();
            $table->date('reschedule_date')->nullable();
            $table->time('reschedule_time')->nullable();
            $table->text('reschedule_reason')->nullable();
            $table->enum('reschedule_status', ['none', 'pending', 'accepted', 'rejected'])->default('none');
            $table->enum('status_booking', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_bookings');
    }
};
