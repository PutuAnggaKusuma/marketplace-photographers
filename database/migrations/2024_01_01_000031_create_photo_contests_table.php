<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('photo_contests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_admin')->nullable()->constrained('role_admins')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('judul_lomba', 100)->nullable();
            $table->string('kategori', 255)->default('Umum');
            $table->text('deskrpisi_lomba')->nullable();
            $table->text('syarat_ketentuan')->nullable();
            $table->text('catatan_admin')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('hadiah', 100)->nullable();
            $table->decimal('biaya_pendaftaran', 12, 2)->default(0);
            $table->string('bank_name', 255)->nullable();
            $table->string('bank_account_number', 255)->nullable();
            $table->string('bank_account_holder', 255)->nullable();
            $table->integer('max_submissions')->default(1);
            $table->boolean('allow_link')->default(false);
            $table->dateTime('registration_start')->nullable();
            $table->dateTime('registration_end')->nullable();
            $table->dateTime('submission_start')->nullable();
            $table->dateTime('submission_end')->nullable();
            $table->dateTime('judging_start')->nullable();
            $table->dateTime('judging_end')->nullable();
            $table->dateTime('announcement_date')->nullable();
            $table->enum('status', ['pending_review', 'buka', 'ditutup', 'selesai', 'ditolak'])->default('pending_review');
            $table->integer('view_count')->default(0);
            $table->string('penyelenggara', 100)->nullable();
            $table->string('banner_url', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('photo_contests');
    }
};
