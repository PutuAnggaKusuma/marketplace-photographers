<?php

namespace Database\Seeders;

use App\Models\PhotoContest;
use App\Models\ContestRegistration;
use App\Models\ContestSubmission;
use App\Models\User;
use Illuminate\Database\Seeder;

class ContestSubmissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Data untuk Lomba yang masih Buka (Contest ID 9: Kompetisi Street Photography Urban Stories 2026)
        $streetContest = PhotoContest::find(9);
        if ($streetContest) {
            // Bersihkan data lama untuk contest 9
            ContestSubmission::where('id_contest', 9)->forceDelete();
            ContestRegistration::where('id_contest', 9)->forceDelete();

            // Pendaftar 1: Dian & Febri (Disetujui & Lunas)
            $reg1 = ContestRegistration::create([
                'id_contest' => 9,
                'id_user' => 5,
                'nama_lengkap' => 'Dian & Febri',
                'email' => 'dian@gmail.com',
                'no_wa' => '081298765432',
                'asal_kota' => 'Jakarta Pusat',
                'instansi' => 'Komunitas Street Jakarta',
                'bio_singkat' => 'Penggemar street photography & human interest perkotaan.',
                'motivasi' => 'Ingin berbagi perspektif visual sudut kota yang terabaikan.',
                'portfolio_link' => 'https://instagram.com/dian_streetshots',
                'status_pendaftaran' => 'disetujui',
                'status_pembayaran' => 'lunas',
                'bukti_transfer' => 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?w=600&q=80',
                'created_at' => now()->subDays(3),
            ]);

            // Pendaftar 2: Reza Pratama (Disetujui & Lunas)
            $reg2 = ContestRegistration::create([
                'id_contest' => 9,
                'id_user' => 11,
                'nama_lengkap' => 'Reza Pratama',
                'email' => 'reza.itb@gmail.com',
                'no_wa' => '081387654321',
                'asal_kota' => 'Bandung',
                'instansi' => 'Institut Teknologi Bandung',
                'bio_singkat' => 'Mahasiswa desain & fotografer jalanan aktif.',
                'motivasi' => 'Menambah portofolio dan mengasah kepekaan menangkap momen spontan.',
                'portfolio_link' => 'https://behance.net/rezapratama',
                'status_pendaftaran' => 'disetujui',
                'status_pembayaran' => 'lunas',
                'bukti_transfer' => 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?w=600&q=80',
                'created_at' => now()->subDays(2),
            ]);

            // Pendaftar 3: Siti Rahmawati (Menunggu Verifikasi)
            ContestRegistration::create([
                'id_contest' => 9,
                'id_user' => 12,
                'nama_lengkap' => 'Siti Rahmawati',
                'email' => 'siti.rahma@gmail.com',
                'no_wa' => '085712345678',
                'asal_kota' => 'Surabaya',
                'instansi' => 'Universitas Airlangga',
                'bio_singkat' => 'Pecinta fotografi dokumenter dan human interest.',
                'motivasi' => 'Mengabadikan dinamika pasar dan kehidupan para pekerja jalanan.',
                'portfolio_link' => 'https://instagram.com/siti_snaps',
                'status_pendaftaran' => 'menunggu_verifikasi',
                'status_pembayaran' => 'menunggu_pembayaran',
                'bukti_transfer' => 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?w=600&q=80',
                'created_at' => now()->subHours(12),
            ]);

            // Pendaftar 4: Mahkota Event (Ditolak)
            ContestRegistration::create([
                'id_contest' => 9,
                'id_user' => 9,
                'nama_lengkap' => 'Mahkota Event Organizer',
                'email' => 'mahkota@event.com',
                'no_wa' => '081999888777',
                'asal_kota' => 'Semarang',
                'instansi' => 'Mahkota EO',
                'bio_singkat' => 'Dokumentasi event & panggung pertunjukan.',
                'motivasi' => 'Mencoba kompetisi fotografi umum.',
                'portfolio_link' => 'https://mahkotaevent.com',
                'status_pendaftaran' => 'ditolak',
                'status_pembayaran' => 'menunggu_pembayaran',
                'catatan_admin' => 'Portofolio yang dilampirkan tidak sesuai dengan tema street photography.',
                'created_at' => now()->subDays(4),
            ]);

            // Submisi Karya Masuk (Status Masih Terverifikasi / Belum Ada Juara karena Lomba Masih Buka)
            ContestSubmission::create([
                'id_contest' => 9,
                'id_user' => 5,
                'id_registration' => $reg1->id,
                'judul_karya' => 'Dinamika Kehidupan Senja Kota Tua Jakarta',
                'deskripsi_karya' => 'Potret aktivitas para pesepeda ontel dan pedagang jalanan saat matahari terbenam di kawasan Kota Tua.',
                'image_url' => 'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?w=1200&q=80',
                'media_link' => 'https://drive.google.com/drive/folders/sample-street-1',
                'status_submission' => 'verified',
                'catatan_juri' => null,
                'created_at' => now()->subDays(1),
            ]);

            ContestSubmission::create([
                'id_contest' => 9,
                'id_user' => 11,
                'id_registration' => $reg2->id,
                'judul_karya' => 'Langkah Bergegas di Sudirman',
                'deskripsi_karya' => 'Interaksi para komuter pejalan kaki di trotoar Sudirman saat jam sibuk pulang kerja sore hari.',
                'image_url' => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?w=1200&q=80',
                'media_link' => 'https://drive.google.com/drive/folders/sample-street-2',
                'status_submission' => 'verified',
                'catatan_juri' => null,
                'created_at' => now()->subHours(8),
            ]);
        }

        // 2. Data untuk Lomba yang Selesai (Contest ID 4: National Wedding & Prewedding Photography Award)
        $completedContest = PhotoContest::find(4);
        if ($completedContest) {
            ContestSubmission::where('id_contest', 4)->forceDelete();
            ContestRegistration::where('id_contest', 4)->forceDelete();

            $r1 = ContestRegistration::create([
                'id_contest' => 4,
                'id_user' => 3,
                'nama_lengkap' => 'Alex Visual Studio',
                'email' => 'alex@lensmatch.com',
                'no_wa' => '081234567890',
                'asal_kota' => 'Denpasar',
                'instansi' => 'Alex Visual Works',
                'bio_singkat' => 'Spesialis wedding & prewedding bernuansa sinematik.',
                'status_pendaftaran' => 'disetujui',
                'status_pembayaran' => 'lunas',
                'created_at' => now()->subDays(20),
            ]);

            $r2 = ContestRegistration::create([
                'id_contest' => 4,
                'id_user' => 7,
                'nama_lengkap' => 'Dewa Portrait Studio',
                'email' => 'dewa@lensmatch.com',
                'no_wa' => '081234567891',
                'asal_kota' => 'Gianyar',
                'instansi' => 'Dewa Art Project',
                'bio_singkat' => 'Fotografi adat & tradisi pernikahan Bali.',
                'status_pendaftaran' => 'disetujui',
                'status_pembayaran' => 'lunas',
                'created_at' => now()->subDays(21),
            ]);

            $r3 = ContestRegistration::create([
                'id_contest' => 4,
                'id_user' => 8,
                'nama_lengkap' => 'Bali Lens Team',
                'email' => 'balilens@lensmatch.com',
                'no_wa' => '081234567892',
                'asal_kota' => 'Kuta',
                'instansi' => 'Bali Lens Creative',
                'bio_singkat' => 'Dokumentasi outdoor prewedding & drone cinematography.',
                'status_pendaftaran' => 'disetujui',
                'status_pembayaran' => 'lunas',
                'created_at' => now()->subDays(22),
            ]);

            // Submisi Pemenang pada Lomba yang Sudah Selesai
            ContestSubmission::create([
                'id_contest' => 4,
                'id_user' => 3,
                'id_registration' => $r1->id,
                'judul_karya' => 'Janji Abadi di Tepi Tebing Uluwatu',
                'deskripsi_karya' => 'Dokumentasi momen sakral prewedding dengan latar tebing samudera hindia dan pencahayaan matahari senja yang dramatis.',
                'image_url' => 'https://images.unsplash.com/photo-1519741497674-611481863552?w=1200&q=80',
                'media_link' => 'https://drive.google.com/drive/folders/sample-wedding-1',
                'status_submission' => 'winner_1',
                'catatan_juri' => 'Komposisi luar biasa, ekspresi natural pasangan menyatu sempurna dengan pencahayaan golden hour.',
                'created_at' => now()->subDays(15),
            ]);

            ContestSubmission::create([
                'id_contest' => 4,
                'id_user' => 7,
                'id_registration' => $r2->id,
                'judul_karya' => 'Keagungan Busana Adat Pengantin Puri',
                'deskripsi_karya' => 'Potret pasangan pengantin Bali mengenakan busana Payas Agung dengan detail tata rias dan pencahayaan studio klasik.',
                'image_url' => 'https://images.unsplash.com/photo-1583939003579-730e3918a45a?w=1200&q=80',
                'media_link' => 'https://drive.google.com/drive/folders/sample-wedding-2',
                'status_submission' => 'winner_2',
                'catatan_juri' => 'Detail ornamen adat sangat tajam dengan kontras warna yang kaya dan artistik.',
                'created_at' => now()->subDays(16),
            ]);

            ContestSubmission::create([
                'id_contest' => 4,
                'id_user' => 8,
                'id_registration' => $r3->id,
                'judul_karya' => 'Romansa Kabut Pagi Danau Tamblingan',
                'deskripsi_karya' => 'Sesi prewedding di atas perahu kayu dengan siluet kabut pagi dan pantulan air yang tenang.',
                'image_url' => 'https://images.unsplash.com/photo-1511285560929-80b456fea0bc?w=1200&q=80',
                'media_link' => 'https://drive.google.com/drive/folders/sample-wedding-3',
                'status_submission' => 'winner_3',
                'catatan_juri' => 'Suasana mistis dan atmosfer kabut pagi terekam dengan sangat lembut dan berkarakter.',
                'created_at' => now()->subDays(17),
            ]);
        }
    }
}
