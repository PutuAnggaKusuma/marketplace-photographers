<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE photo_contests MODIFY COLUMN status ENUM('pending_review', 'buka', 'ditutup', 'selesai', 'ditolak') NOT NULL DEFAULT 'pending_review'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE photo_contests MODIFY COLUMN status ENUM('buka', 'ditutup', 'selesai') NOT NULL DEFAULT 'buka'");
    }
};
