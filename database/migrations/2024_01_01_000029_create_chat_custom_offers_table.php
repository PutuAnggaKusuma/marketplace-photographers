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
        Schema::create('chat_custom_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_chat_booking')->constrained('chat_bookings')->onDelete('cascade');
            $table->foreignId('id_photographer')->constrained('role_photographers')->onDelete('cascade');
            $table->foreignId('id_client')->constrained('role_clients')->onDelete('cascade');
            $table->string('judul_penawaran');
            $table->text('deskripsi')->nullable();
            $table->date('booking_date')->nullable();
            $table->time('booking_time')->nullable();
            $table->integer('durasi_jam')->default(2);
            $table->string('lokasi')->nullable();
            $table->decimal('harga', 14, 2);
            $table->enum('status', ['pending', 'accepted', 'rejected', 'expired'])->default('pending');
            $table->foreignId('id_contract')->nullable()->constrained('contracts')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_custom_offers');
    }
};
