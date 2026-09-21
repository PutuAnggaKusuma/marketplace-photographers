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
        // 1. Tambah kolom harga & is_premium pada tabel elearnings jika belum ada
        if (Schema::hasTable('elearnings')) {
            Schema::table('elearnings', function (Blueprint $table) {
                if (!Schema::hasColumn('elearnings', 'harga')) {
                    $table->unsignedBigInteger('harga')->default(0)->after('durasi');
                }
                if (!Schema::hasColumn('elearnings', 'is_premium')) {
                    $table->boolean('is_premium')->default(false)->after('harga');
                }
            });
        }

        // 2. Tabel elearning_modules (Topik / Modul Kursus)
        if (!Schema::hasTable('elearning_modules')) {
            Schema::create('elearning_modules', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_elearning');
                $table->string('judul_modul');
                $table->text('deskripsi')->nullable();
                $table->integer('urutan')->default(1);
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('id_elearning')
                      ->references('id')
                      ->on('elearnings')
                      ->onDelete('cascade');
            });
        }

        // 3. Tabel elearning_lessons (Tahapan Materi Belajar)
        if (!Schema::hasTable('elearning_lessons')) {
            Schema::create('elearning_lessons', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_elearning');
                $table->unsignedBigInteger('id_module')->nullable();
                $table->string('judul');
                $table->string('tipe')->default('reading'); // video, reading, audio, foto
                $table->longText('konten')->nullable();
                $table->string('media_type')->nullable(); // youtube, external, upload
                $table->string('media_url', 1000)->nullable();
                $table->string('media_path')->nullable();
                $table->integer('durasi_menit')->default(5);
                $table->integer('urutan')->default(1);
                $table->boolean('is_free')->default(true); // true = gratis (free preview), false = berbayar (locked)
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('id_elearning')
                      ->references('id')
                      ->on('elearnings')
                      ->onDelete('cascade');

                $table->foreign('id_module')
                      ->references('id')
                      ->on('elearning_modules')
                      ->onDelete('cascade');
            });
        }

        // 4. Tabel elearning_lesson_progress (Tracking Progress per Tahapan)
        if (!Schema::hasTable('elearning_lesson_progress')) {
            Schema::create('elearning_lesson_progress', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_user');
                $table->unsignedBigInteger('id_elearning');
                $table->unsignedBigInteger('id_lesson');
                $table->boolean('is_completed')->default(true);
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();

                $table->foreign('id_user')
                      ->references('id')
                      ->on('users')
                      ->onDelete('cascade');

                $table->foreign('id_elearning')
                      ->references('id')
                      ->on('elearnings')
                      ->onDelete('cascade');

                $table->foreign('id_lesson')
                      ->references('id')
                      ->on('elearning_lessons')
                      ->onDelete('cascade');

                $table->unique(['id_user', 'id_lesson']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('elearning_lesson_progress');
        Schema::dropIfExists('elearning_lessons');
        Schema::dropIfExists('elearning_modules');

        if (Schema::hasTable('elearnings')) {
            Schema::table('elearnings', function (Blueprint $table) {
                if (Schema::hasColumn('elearnings', 'is_premium')) {
                    $table->dropColumn('is_premium');
                }
                if (Schema::hasColumn('elearnings', 'harga')) {
                    $table->dropColumn('harga');
                }
            });
        }
    }
};
