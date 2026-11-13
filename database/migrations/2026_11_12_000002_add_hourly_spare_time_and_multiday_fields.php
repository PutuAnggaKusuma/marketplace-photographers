<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('photographer_services', function (Blueprint $table) {
            if (!Schema::hasColumn('photographer_services', 'spare_time_enabled')) {
                $table->boolean('spare_time_enabled')->default(false)->after('deskripsi_layanan');
            }
            if (!Schema::hasColumn('photographer_services', 'spare_before_minutes')) {
                $table->unsignedSmallInteger('spare_before_minutes')->default(0)->after('spare_time_enabled');
            }
            if (!Schema::hasColumn('photographer_services', 'spare_after_minutes')) {
                $table->unsignedSmallInteger('spare_after_minutes')->default(0)->after('spare_before_minutes');
            }
            if (!Schema::hasColumn('photographer_services', 'allow_same_location_bypass')) {
                $table->boolean('allow_same_location_bypass')->default(false)->after('spare_after_minutes');
            }
        });

        Schema::table('contract_bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('contract_bookings', 'booking_end_date')) {
                $table->date('booking_end_date')->nullable()->after('booking_date');
            }
            if (Schema::hasColumn('contract_bookings', 'lokasi')) {
                $table->string('lokasi', 500)->nullable()->change();
            }
            if (!Schema::hasColumn('contract_bookings', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable()->after('lokasi');
            }
            if (!Schema::hasColumn('contract_bookings', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            }
            if (!Schema::hasColumn('contract_bookings', 'spare_before_minutes')) {
                $table->unsignedSmallInteger('spare_before_minutes')->default(0)->after('durasi');
            }
            if (!Schema::hasColumn('contract_bookings', 'spare_after_minutes')) {
                $table->unsignedSmallInteger('spare_after_minutes')->default(0)->after('spare_before_minutes');
            }
            if (!Schema::hasColumn('contract_bookings', 'allow_same_location_bypass')) {
                $table->boolean('allow_same_location_bypass')->default(false)->after('spare_after_minutes');
            }
        });

        Schema::table('chat_custom_offers', function (Blueprint $table) {
            if (!Schema::hasColumn('chat_custom_offers', 'booking_end_date')) {
                $table->date('booking_end_date')->nullable()->after('booking_date');
            }
            if (!Schema::hasColumn('chat_custom_offers', 'jam_selesai')) {
                $table->time('jam_selesai')->nullable()->after('booking_time');
            }
            if (Schema::hasColumn('chat_custom_offers', 'lokasi')) {
                $table->string('lokasi', 500)->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('chat_custom_offers', function (Blueprint $table) {
            foreach (['jam_selesai', 'booking_end_date'] as $col) {
                if (Schema::hasColumn('chat_custom_offers', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('contract_bookings', function (Blueprint $table) {
            foreach ([
                'allow_same_location_bypass',
                'spare_after_minutes',
                'spare_before_minutes',
                'longitude',
                'latitude',
                'booking_end_date',
            ] as $col) {
                if (Schema::hasColumn('contract_bookings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('photographer_services', function (Blueprint $table) {
            foreach ([
                'allow_same_location_bypass',
                'spare_after_minutes',
                'spare_before_minutes',
                'spare_time_enabled',
            ] as $col) {
                if (Schema::hasColumn('photographer_services', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

