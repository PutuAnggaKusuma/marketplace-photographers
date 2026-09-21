<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_contract')->nullable()->constrained('contracts')->onDelete('cascade');
            $table->string('payment_amount', 100)->nullable();
            $table->enum('payment_status', ['pending', 'completed', 'failed'])->nullable();
            $table->enum('contract_payment_type', ['dp', 'full'])->nullable();
            $table->string('payment_link', 100)->nullable();
            $table->string('bukti_transfer', 100)->nullable();
            $table->enum('payment_type', ['payment gateway', 'transfer'])->nullable();
            $table->string('external_id', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};