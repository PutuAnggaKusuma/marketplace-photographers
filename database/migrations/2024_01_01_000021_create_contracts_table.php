<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_client')->nullable()->constrained('role_clients')->onDelete('cascade');
            $table->foreignId('id_photographer')->nullable()->constrained('role_photographers')->onDelete('cascade');
            $table->string('jumlah', 100)->nullable();
            $table->enum('payment_type', ['full', 'dp'])->nullable();
            $table->string('jumlah_dp', 100)->nullable();
            $table->decimal('fee_marketplace', 12, 2)->default(0.00);
            $table->decimal('payout_amount', 12, 2)->default(0.00);
            $table->enum('status_payout', ['holding', 'ready', 'paid', 'refunded'])->default('holding');
            $table->timestamp('payout_at')->nullable();
            $table->timestamp('payout_requested_at')->nullable();
            $table->text('payout_notes')->nullable();
            $table->boolean('is_validated_photographer')->default(false);
            $table->boolean('is_validated_client')->default(false);
            $table->timestamp('expired_at')->nullable();
            $table->enum('status_contract', ['draft', 'approved', 'pending_payment', 'active', 'completed', 'cancelled'])->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
