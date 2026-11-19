<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE contest_submissions MODIFY COLUMN status_submission ENUM('pending','verified','winner_1','winner_2','winner_3','rejected','disqualified') NOT NULL DEFAULT 'verified'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE contest_submissions MODIFY COLUMN status_submission ENUM('pending','verified','winner_1','winner_2','winner_3','rejected') NOT NULL DEFAULT 'verified'");
    }
};
