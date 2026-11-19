<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE contracts MODIFY COLUMN status_payout ENUM('holding', 'ready', 'paid', 'refunded') NULL DEFAULT NULL;");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE contracts MODIFY COLUMN status_payout ENUM('holding', 'ready', 'paid', 'refunded') NOT NULL DEFAULT 'holding';");
    }
};
