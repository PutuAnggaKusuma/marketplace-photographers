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
            if (!Schema::hasColumn('photo_contests', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id_admin')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('photo_contests', 'catatan_admin')) {
                $table->text('catatan_admin')->nullable()->after('syarat_ketentuan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('photo_contests', function (Blueprint $table) {
            if (Schema::hasColumn('photo_contests', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
            if (Schema::hasColumn('photo_contests', 'catatan_admin')) {
                $table->dropColumn('catatan_admin');
            }
        });
    }
};
