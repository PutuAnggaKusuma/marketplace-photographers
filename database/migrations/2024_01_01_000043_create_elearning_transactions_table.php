<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('elearning_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $table->foreignId('id_e_learning')->constrained('elearnings')->onDelete('cascade');
            $table->string('invoice_code', 100)->unique();
            $table->decimal('nominal', 12, 2)->default(0.00);
            $table->enum('payment_method', ['qris', 'bank', 'ewallet'])->default('qris');
            $table->enum('payment_status', ['pending', 'completed', 'failed'])->default('completed');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('elearning_transactions');
    }
};
