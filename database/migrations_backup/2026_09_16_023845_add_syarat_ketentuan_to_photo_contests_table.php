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
        Schema::table('photo_contests', function (Blueprint $table) {
            $table->text('syarat_ketentuan')->nullable()->after('deskrpisi_lomba');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('photo_contests', function (Blueprint $table) {
            $table->dropColumn('syarat_ketentuan');
        });
    }
};
