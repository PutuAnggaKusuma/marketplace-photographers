<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $table->string('title', 100)->nullable();
            $table->text('message')->nullable();
            $table->enum('type', ['booking', 'payment', 'system', 'contest', 'elearning', 'forum'])->default('system');
            $table->boolean('is_read')->default(false);
            $table->string('link', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};