<?php

use App\Models\PhotographerWorkingHour;
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
        Schema::table('role_photographers', function (Blueprint $table) {
            $table->enum('status_etalase', ['belum_diajukan', 'menunggu', 'disetujui', 'ditolak'])
                ->default('belum_diajukan')
                ->after('catatan_penolakan');
            $table->text('catatan_penolakan_etalase')
                ->nullable()
                ->after('status_etalase');
            $table->boolean('jadwal_dikonfirmasi')
                ->default(false)
                ->after('catatan_penolakan_etalase');
            $table->timestamp('etalase_diajukan_at')
                ->nullable()
                ->after('jadwal_dikonfirmasi');
            $table->timestamp('etalase_disetujui_at')
                ->nullable()
                ->after('etalase_diajukan_at');
        });

        // Backfill demo seeded photographers supaya etalase publik tetap memiliki data valid
        $demoConfigs = [
            'alex@lensmatch.com' => [
                'foto_sampul' => 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=1200&q=80',
                'portfolio' => null,
            ],
            'budi@lensmatch.com' => [
                'foto_sampul' => 'https://images.unsplash.com/photo-1501386761578-eac5c94b800a?auto=format&fit=crop&w=1200&q=80',
                'portfolio' => null,
            ],
            'clara@lensmatch.com' => [
                'foto_sampul' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=1200&q=80',
                'portfolio' => null,
            ],
            'dewa@lensmatch.com' => [
                'foto_sampul' => 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?w=1200&q=80',
                'portfolio' => [
                    'judul' => 'Portrait Wisuda & Keluarga Taman Sari',
                    'deskripsi' => 'Dokumentasi potret wisuda dan keluarga di Yogyakarta dengan pencahayaan alami dan komposisi klasik.',
                    'medias' => [
                        'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?w=1200&q=80',
                        'https://images.unsplash.com/photo-1511895426328-dc8714191300?w=1200&q=80',
                        'https://images.unsplash.com/photo-1768818928341-3b31b8965c6d?q=80&w=1200&auto=format&fit=crop',
                        'https://images.unsplash.com/photo-1450133064473-71024230f91b?w=1200&q=80',
                    ],
                ],
            ],
            'balilens@lensmatch.com' => [
                'foto_sampul' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1200&q=80',
                'portfolio' => [
                    'judul' => 'Exploring Nusa Penida & Uluwatu Cliff',
                    'deskripsi' => 'Sesi foto liburan santai di pesisir Bali dengan komposisi lanskap tropis dan warna natural.',
                    'medias' => [
                        'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1200&q=80',
                        'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?w=1200&q=80',
                        'https://images.unsplash.com/photo-1506744038136-46273834b3fb?w=1200&q=80',
                        'https://images.unsplash.com/photo-1542038784456-1ea8e935640e?w=1200&q=80',
                    ],
                ],
            ],
        ];

        $now = now();

        foreach ($demoConfigs as $email => $cfg) {
            $user = DB::table('users')->where('email', $email)->whereNull('deleted_at')->first();
            if (!$user) {
                continue;
            }

            $photographer = DB::table('role_photographers')
                ->where('id_user', $user->id)
                ->whereNull('deleted_at')
                ->first();

            if (!$photographer) {
                continue;
            }

            // Pastikan memiliki portofolio + foto bila belum ada
            if ($cfg['portfolio']) {
                $existingPortfolioCount = DB::table('photographer_portofolios')
                    ->where('id_photographer', $photographer->id)
                    ->whereNull('deleted_at')
                    ->count();

                if ($existingPortfolioCount === 0) {
                    $portfolioId = DB::table('photographer_portofolios')->insertGetId([
                        'id_photographer' => $photographer->id,
                        'judul' => $cfg['portfolio']['judul'],
                        'deskripsi' => $cfg['portfolio']['deskripsi'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                    foreach ($cfg['portfolio']['medias'] as $mediaUrl) {
                        DB::table('portofolio_medias')->insert([
                            'id_portofolio' => $portfolioId,
                            'media' => $mediaUrl,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);
                    }
                }
            }

            // Pastikan memiliki jam kerja mingguan
            PhotographerWorkingHour::initializeDefaults((int) $photographer->id, true);

            $updateData = [
                'is_onboarded' => true,
                'is_verified' => true,
                'status_verifikasi' => 'terverifikasi',
                'jadwal_dikonfirmasi' => true,
                'status_etalase' => 'disetujui',
                'etalase_diajukan_at' => $now,
                'etalase_disetujui_at' => $now,
                'updated_at' => $now,
            ];

            if (empty(trim((string) $photographer->foto_sampul))) {
                $updateData['foto_sampul'] = $cfg['foto_sampul'];
            }

            DB::table('role_photographers')
                ->where('id', $photographer->id)
                ->update($updateData);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('role_photographers', function (Blueprint $table) {
            $table->dropColumn([
                'status_etalase',
                'catatan_penolakan_etalase',
                'jadwal_dikonfirmasi',
                'etalase_diajukan_at',
                'etalase_disetujui_at',
            ]);
        });
    }
};

