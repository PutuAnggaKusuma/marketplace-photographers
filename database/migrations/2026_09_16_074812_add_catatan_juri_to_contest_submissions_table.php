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
        Schema::table('contest_submissions', function (Blueprint $table) {
            if (!Schema::hasColumn('contest_submissions', 'catatan_juri')) {
                $table->text('catatan_juri')->nullable()->after('status_submission');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contest_submissions', function (Blueprint $table) {
            if (Schema::hasColumn('contest_submissions', 'catatan_juri')) {
                $table->dropColumn('catatan_juri');
            }
        });
    }
};
