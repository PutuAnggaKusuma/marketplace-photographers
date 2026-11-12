<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('photographer_working_hours', function (Blueprint $table) {
            $table->boolean('is_open')->default(false)->change();
        });

        // Reset baris jadwal bawaan yang sebelumnya otomatis terbuka untuk fotografer yang belum mengonfirmasi jadwal
        $unconfirmedIds = DB::table('role_photographers')
            ->where('jadwal_dikonfirmasi', false)
            ->pluck('id');

        if ($unconfirmedIds->isNotEmpty()) {
            DB::table('photographer_working_hours')
                ->whereIn('id_photographer', $unconfirmedIds)
                ->update([
                    'is_open' => false,
                    'is_24_hours' => false,
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('photographer_working_hours', function (Blueprint $table) {
            $table->boolean('is_open')->default(true)->change();
        });
    }
};

