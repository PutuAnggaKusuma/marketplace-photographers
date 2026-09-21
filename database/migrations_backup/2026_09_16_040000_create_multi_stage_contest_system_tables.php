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
        // 1. Update photo_contests table with multi-stage configurations
        Schema::table('photo_contests', function (Blueprint $table) {
            if (!Schema::hasColumn('photo_contests', 'biaya_pendaftaran')) {
                $table->decimal('biaya_pendaftaran', 12, 2)->default(0)->after('hadiah');
            }
            if (!Schema::hasColumn('photo_contests', 'bank_name')) {
                $table->string('bank_name')->nullable()->after('biaya_pendaftaran');
            }
            if (!Schema::hasColumn('photo_contests', 'bank_account_number')) {
                $table->string('bank_account_number')->nullable()->after('bank_name');
            }
            if (!Schema::hasColumn('photo_contests', 'bank_account_holder')) {
                $table->string('bank_account_holder')->nullable()->after('bank_account_number');
            }
            if (!Schema::hasColumn('photo_contests', 'max_submissions')) {
                $table->integer('max_submissions')->default(1)->after('bank_account_holder');
            }
            if (!Schema::hasColumn('photo_contests', 'allow_link')) {
                $table->boolean('allow_link')->default(false)->after('max_submissions');
            }
            if (!Schema::hasColumn('photo_contests', 'registration_start')) {
                $table->dateTime('registration_start')->nullable()->after('allow_link');
            }
            if (!Schema::hasColumn('photo_contests', 'registration_end')) {
                $table->dateTime('registration_end')->nullable()->after('registration_start');
            }
            if (!Schema::hasColumn('photo_contests', 'submission_start')) {
                $table->dateTime('submission_start')->nullable()->after('registration_end');
            }
            if (!Schema::hasColumn('photo_contests', 'submission_end')) {
                $table->dateTime('submission_end')->nullable()->after('submission_start');
            }
            if (!Schema::hasColumn('photo_contests', 'judging_start')) {
                $table->dateTime('judging_start')->nullable()->after('submission_end');
            }
            if (!Schema::hasColumn('photo_contests', 'judging_end')) {
                $table->dateTime('judging_end')->nullable()->after('judging_start');
            }
            if (!Schema::hasColumn('photo_contests', 'announcement_date')) {
                $table->dateTime('announcement_date')->nullable()->after('judging_end');
            }
        });

        // 2. Create contest_registrations table for participant qualifications & review
        if (!Schema::hasTable('contest_registrations')) {
            Schema::create('contest_registrations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_contest');
                $table->unsignedBigInteger('id_user');
                $table->string('nama_lengkap');
                $table->string('email');
                $table->string('no_wa');
                $table->string('asal_kota')->nullable();
                $table->string('instansi')->nullable();
                $table->text('bio_singkat')->nullable();
                $table->text('motivasi')->nullable();
                $table->string('cv_portfolio_file')->nullable();
                $table->string('portfolio_link')->nullable();
                $table->enum('status_pendaftaran', ['menunggu_verifikasi', 'disetujui', 'ditolak'])->default('menunggu_verifikasi');
                $table->enum('status_pembayaran', ['gratis', 'menunggu_pembayaran', 'menunggu_konfirmasi', 'lunas'])->default('gratis');
                $table->string('bukti_transfer')->nullable();
                $table->dateTime('waktu_batas_pembayaran')->nullable();
                $table->text('catatan_admin')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('id_contest')->references('id')->on('photo_contests')->onDelete('cascade');
                $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // 3. Update contest_submissions table
        Schema::table('contest_submissions', function (Blueprint $table) {
            if (!Schema::hasColumn('contest_submissions', 'id_registration')) {
                $table->unsignedBigInteger('id_registration')->nullable()->after('id_user');
                $table->foreign('id_registration')->references('id')->on('contest_registrations')->onDelete('set null');
            }
            if (!Schema::hasColumn('contest_submissions', 'media_link')) {
                $table->string('media_link')->nullable()->after('image_url');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contest_submissions', function (Blueprint $table) {
            if (Schema::hasColumn('contest_submissions', 'id_registration')) {
                $table->dropForeign(['id_registration']);
                $table->dropColumn('id_registration');
            }
            if (Schema::hasColumn('contest_submissions', 'media_link')) {
                $table->dropColumn('media_link');
            }
        });

        Schema::dropIfExists('contest_registrations');

        Schema::table('photo_contests', function (Blueprint $table) {
            $cols = [
                'biaya_pendaftaran', 'bank_name', 'bank_account_number', 'bank_account_holder',
                'max_submissions', 'allow_link', 'registration_start', 'registration_end',
                'submission_start', 'submission_end', 'judging_start', 'judging_end', 'announcement_date'
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('photo_contests', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
