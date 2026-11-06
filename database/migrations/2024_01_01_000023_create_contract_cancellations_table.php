<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contract_cancellations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_contract')->nullable()->constrained('contracts')->onDelete('cascade');
            $table->text('alasan')->nullable();
            $table->enum('dibatalkan_oleh', ['client', 'photographer', 'admin'])->nullable();
            $table->string('refund_amount', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_cancellations');
    }
};
