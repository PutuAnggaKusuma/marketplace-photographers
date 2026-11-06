<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_chat_booking')->nullable()->constrained('chat_bookings')->onDelete('cascade');
            $table->foreignId('id_sender')->nullable()->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('reply_to_id')->nullable();
            $table->foreignId('id_custom_offer')->nullable()->constrained('chat_custom_offers')->nullOnDelete();
            $table->text('message')->nullable();
            $table->string('attachment_url')->nullable();
            $table->string('attachment_type')->nullable();
            $table->boolean('is_read')->default(false);
            $table->boolean('is_edited')->default(false);
            $table->timestamp('edited_at')->nullable();
            $table->boolean('deleted_for_all')->default(false);
            $table->json('deleted_by_users')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
