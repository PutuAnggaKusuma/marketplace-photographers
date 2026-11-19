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
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('activation_requested_at')->nullable()->after('email_verified_at');
            $table->text('activation_request_note')->nullable()->after('activation_requested_at');
            $table->boolean('is_suspended')->default(false)->after('is_protected');
            $table->string('suspension_reason')->nullable()->after('is_suspended');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'activation_requested_at',
                'activation_request_note',
                'is_suspended',
                'suspension_reason',
            ]);
        });
    }
};
