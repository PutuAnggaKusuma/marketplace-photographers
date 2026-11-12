<?php

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\ContractBooking;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\PhotographerAvailability;
use App\Models\PhotographerService;
use App\Models\RoleClient;
use App\Models\RolePhotographer;
use App\Models\Testimonial;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookingContractSeeder extends Seeder
{
    public function run(): void
    {
        // Nonaktifkan foreign key checks sementara untuk reset data kontrak agar bersih
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Testimonial::truncate();
        Payment::truncate();
        ContractBooking::truncate();
        Contract::truncate();
        Notification::whereIn('type', ['booking', 'payment'])->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Pastikan masing-masing klien terisolasi di akun miliknya sendiri (tanpa fallback silang ke Dian)
        $resolveClient = function (string $email, string $nama, string $phone, string $alamat): RoleClient {
            $user = \App\Models\User::firstOrCreate(
                ['email' => $email],
                [
                    'nama' => $nama,
                    'password' => \Illuminate\Support\Facades\Hash::make('password123'),
                    'role' => 'client',
                    'is_protected' => false,
                    'email_verified_at' => now(),
                ]
            );

            return RoleClient::firstOrCreate(
                ['id_user' => $user->id],
                [
                    'nama' => $nama,
                    'nomor_telepon' => $phone,
                    'alamat' => $alamat,
                ]
            );
        };

        $clientDian = $resolveClient('dian@gmail.com', 'Dian & Febri', '081234567899', 'Jakarta Selatan');
        $clientMahkota = $resolveClient('mahkota@event.com', 'PT Mahkota Event Organizer', '081999888777', 'Semarang');
        $clientLilik = $resolveClient('bulilik@kuliner.com', 'Bu Lilik Kuliner Nusantara', '081567891234', 'Bandung');
        $clientReza = $resolveClient('reza.itb@gmail.com', 'Reza Pratama', '081387654321', 'Yogyakarta');
        $clientSiti = $resolveClient('siti.rahma@gmail.com', 'Siti Rahmawati', '085712345678', 'Surabaya');
        $clientResort = $resolveClient('finance@baliluxury.com', 'PT Bali Luxury Resort', '08113987654', 'Seminyak, Bali');

        // Ambil data Fotografer yang ada di database
        $photoAlex = RolePhotographer::where('nama', 'LIKE', '%Alex%')->first() ?? RolePhotographer::find(1);
        $photoBudi = RolePhotographer::where('nama', 'LIKE', '%Budi%')->first() ?? RolePhotographer::find(2);
        $photoClara = RolePhotographer::where('nama', 'LIKE', '%Clara%')->first() ?? RolePhotographer::find(3);
        $photoDewa = RolePhotographer::where('nama', 'LIKE', '%Dewa%')->first() ?? RolePhotographer::find(4);
        $photoBali = RolePhotographer::where('nama', 'LIKE', '%Bali Lens%')->first() ?? RolePhotographer::find(5);

        // Layanan masing-masing fotografer
        $srvAlex = PhotographerService::where('id_photographer', $photoAlex->id)->first();
        $srvBudi = PhotographerService::where('id_photographer', $photoBudi->id)->first();
        $srvClara = PhotographerService::where('id_photographer', $photoClara->id)->first();
        $srvDewa = PhotographerService::where('id_photographer', $photoDewa->id)->first();
        $srvBali = PhotographerService::where('id_photographer', $photoBali->id)->first();

        $dataset = [
            // Bulan 1: Jan 2026 - Completed
            [
                'client' => $clientReza,
                'photographer' => $photoDewa,
                'service' => $srvDewa,
                'amount' => 2200000,
                'created_at' => Carbon::create(2026, 1, 15, 10, 30),
                'session_date' => Carbon::create(2026, 1, 20),
                'location' => 'Auditorium Grha Sabha Pramana UGM, Yogyakarta',
                'status' => 'completed',
                'payout_status' => 'paid',
                'drive_url' => 'https://drive.google.com/drive/folders/demo-wisuda-reza-ugm',
                'catatan_fotografer' => 'Selamat atas kelulusannya Mas Reza! Seluruh foto momen wisuda bersama keluarga sudah selesai di-retouch warna hangat khas Jogja. File hi-res siap dicetak!',
                'review' => 'Hasil foto wisuda saya sangat memuaskan! Fotografer ramah, datang tepat waktu, dan proses serah terima galeri foto sangat cepat.',
                'rating' => 5,
            ],
            // Bulan 2: Feb 2026 - Completed
            [
                'client' => $clientLilik,
                'photographer' => $photoClara,
                'service' => $srvClara,
                'amount' => 3000000,
                'created_at' => Carbon::create(2026, 2, 10, 14, 15),
                'session_date' => Carbon::create(2026, 2, 14),
                'location' => 'Studio Foto Kuliner Bandung, Jawa Barat',
                'status' => 'completed',
                'payout_status' => 'paid',
                'drive_url' => 'https://drive.google.com/drive/folders/demo-katalog-sambal-bu-lilik',
                'catatan_fotografer' => 'Halo Bu Lilik! Foto katalog menu sambal dan frozen food sudah siap. Komposisi lighting difokuskan pada detail tekstur cabai dan kemasan agar menggugah selera pembeli.',
                'review' => 'Bagus sekali fotonya, omzet jualan sambal kami langsung naik setelah pasang foto katalog baru di marketplace dan Instagram. Terima kasih Clara Studio!',
                'rating' => 5,
            ],
            // Bulan 3: Mar 2026 - Completed
            [
                'client' => $clientResort,
                'photographer' => $photoBali,
                'service' => $srvBali,
                'amount' => 3600000,
                'created_at' => Carbon::create(2026, 3, 5, 9, 0),
                'session_date' => Carbon::create(2026, 3, 12),
                'location' => 'Infinity Pool & Private Villa, Seminyak, Bali',
                'status' => 'completed',
                'payout_status' => 'paid',
                'drive_url' => 'https://drive.google.com/drive/folders/demo-resort-seminyak-bali',
                'catatan_fotografer' => 'Foto dokumentasi fasilitas resort dan sesi lifestyle tamu sudah selesai diedit dengan tone tropis cerah. Semoga membantu promosi seasonal resort!',
                'review' => 'Fotografer sangat profesional mengarahkan tamu dan menangkap estetika villa resort kami. Sangat direkomendasikan.',
                'rating' => 5,
            ],
            // Bulan 4: Apr 2026 - Completed
            [
                'client' => $clientMahkota,
                'photographer' => $photoBudi,
                'service' => $srvBudi,
                'amount' => 5600000,
                'created_at' => Carbon::create(2026, 4, 18, 16, 45),
                'session_date' => Carbon::create(2026, 4, 22),
                'location' => 'Stadion Madya GBK Senayan, Jakarta Pusat',
                'status' => 'completed',
                'payout_status' => 'paid',
                'drive_url' => 'https://drive.google.com/drive/folders/demo-konser-music-fest-jkt',
                'catatan_fotografer' => 'Momen panggung festival musik GBK super energik! Lighting panggung dan crowd penonton tertangkap tajam tanpa blur. File RAW dan JPEG High-Res tersedia.',
                'review' => 'Sudah langganan pakai jasa Mas Budi untuk event EO kami di Jakarta. Hasil foto stage selalu luar biasa.',
                'rating' => 5,
            ],
            // Bulan 5: Mei 2026 - Completed
            [
                'client' => $clientSiti,
                'photographer' => $photoAlex,
                'service' => $srvAlex,
                'amount' => 3500000,
                'created_at' => Carbon::create(2026, 5, 8, 11, 20),
                'session_date' => Carbon::create(2026, 5, 15),
                'location' => 'Tanah Lot & Pantai Canggu, Bali',
                'status' => 'completed',
                'payout_status' => 'paid',
                'drive_url' => 'https://drive.google.com/drive/folders/demo-liburan-bali-siti',
                'catatan_fotografer' => 'Halo Mbak Siti! Senang sekali bisa mendokumentasikan liburan keluarga di Tanah Lot. 30 foto terbaik sudah siap diunduh dan dipajang!',
                'review' => 'Sistem kontrak digitalnya bikin tenang banget. Hak cipta foto dan tanggal sesi foto tercatat resmi. Pembayaran DP aman via sistem escrow.',
                'rating' => 5,
            ],
            // Bulan 6: Jun 2026 - Completed (Dian & Febri)
            [
                'client' => $clientDian,
                'photographer' => $photoAlex,
                'service' => $srvAlex,
                'amount' => 3500000,
                'created_at' => Carbon::create(2026, 6, 12, 10, 15),
                'session_date' => Carbon::create(2026, 6, 20),
                'location' => 'Sunset Beach Canggu, Bali',
                'status' => 'completed',
                'payout_status' => 'paid',
                'drive_url' => 'https://drive.google.com/drive/folders/demo-lensmatch-photos-canggu',
                'catatan_fotografer' => 'Halo Dian & Febri! Seluruh 50 foto hasil retouching resolusi tinggi sudah siap diunduh melalui tautan Google Drive di atas. Terima kasih telah mempercayakan momen indah Anda bersama Alex Visual Studio!',
                'review' => 'Cari fotografer prewedding di Bali jadi cepat banget. Portofolio langsung kelihatan jelas beserta pilihan paket jam fotonya.',
                'rating' => 5,
            ],
            // Bulan 7: Jul 2026 - Completed
            [
                'client' => $clientReza,
                'photographer' => $photoDewa,
                'service' => $srvDewa,
                'amount' => 4400000,
                'created_at' => Carbon::create(2026, 7, 4, 13, 0),
                'session_date' => Carbon::create(2026, 7, 10),
                'location' => 'Studio Foto Dewa Art & Heritage, Kotagede, Yogyakarta',
                'status' => 'completed',
                'payout_status' => 'paid',
                'drive_url' => 'https://drive.google.com/drive/folders/demo-portrait-keluarga-dewa',
                'catatan_fotografer' => 'Sesi foto keluarga adat Jawa klasik sudah selesai diolah. Warna busana lurik dan batik terlihat sangat kaya dan elegan.',
                'review' => 'Pelayanan ramah, studio nyaman dan ber-AC, fotografer sabar mengarahkan pose orang tua kami.',
                'rating' => 4,
            ],
            // Bulan 8: Agu 2026 - Completed (Dian & Febri)
            [
                'client' => $clientDian,
                'photographer' => $photoAlex,
                'service' => $srvAlex,
                'amount' => 3500000,
                'created_at' => Carbon::create(2026, 8, 14, 15, 30),
                'session_date' => Carbon::create(2026, 8, 24),
                'location' => 'Tebing Pantai Melasti & Sunset Beach, Bali',
                'status' => 'completed',
                'payout_status' => 'paid',
                'drive_url' => 'https://drive.google.com/drive/folders/demo-lensmatch-photos-melasti',
                'catatan_fotografer' => 'Momen sunset di Pantai Melasti sungguh magis! 45 file hi-res retouching warna sinematik siap diunduh. Terima kasih banyak Dian & Febri.',
                'review' => 'Keren banget hasil fotonya, pencahayaan alami sunset bener-bener dapet feel romantisnya!',
                'rating' => 5,
            ],
            // Bulan 8: Agu 2026 - Completed
            [
                'client' => $clientMahkota,
                'photographer' => $photoBudi,
                'service' => $srvBudi,
                'amount' => 8400000,
                'created_at' => Carbon::create(2026, 8, 20, 11, 0),
                'session_date' => Carbon::create(2026, 8, 28),
                'location' => 'Jakarta International Expo Kemayoran, Jakarta Pusat',
                'status' => 'completed',
                'payout_status' => 'paid',
                'drive_url' => 'https://drive.google.com/drive/folders/demo-jiexpo-festival-stage',
                'catatan_fotografer' => 'Liputan panggung 3 hari festival musik selesai. Lebih dari 200 foto highlight konser sudah dirapikan per performer.',
                'review' => 'Kualitas kerja tim Mas Budi sangat cepat dan tepat tenggat waktu. Sangat puas.',
                'rating' => 5,
            ],
            // Bulan 9: Sep 2026 - Sesi Dikonfirmasi / Active (Klien: Dian & Febri, Fotografer: Alex Visual Studio)
            // INI SESI AKTIF DI MANA FOTOGRAFER BISA KLIK "KIRIM GALERI"
            [
                'client' => $clientDian,
                'photographer' => $photoAlex,
                'service' => $srvAlex,
                'amount' => 3500000,
                'created_at' => Carbon::create(2026, 9, 10, 9, 30),
                'session_date' => Carbon::create(2026, 9, 25),
                'location' => 'Hutan Pinus Bedugul & Danau Beratan, Bali',
                'status' => 'confirmed',
                'payout_status' => 'holding',
                'drive_url' => null, // Belum dikirim fotografer
                'catatan_fotografer' => null,
                'review' => null,
                'rating' => null,
            ],
            // Bulan 9: Sep 2026 - Sesi Dikonfirmasi / Active (Klien: Siti Rahmawati, Fotografer: Budi Event)
            [
                'client' => $clientSiti,
                'photographer' => $photoBudi,
                'service' => $srvBudi,
                'amount' => 2800000,
                'created_at' => Carbon::create(2026, 9, 18, 14, 0),
                'session_date' => Carbon::create(2026, 9, 30),
                'location' => 'Balai Sarbini Concert Hall, Jakarta Selatan',
                'status' => 'confirmed',
                'payout_status' => 'holding',
                'drive_url' => null,
                'catatan_fotografer' => null,
                'review' => null,
                'rating' => null,
            ],
            // Bulan 10: Okt 2026 - Sesi Menunggu Konfirmasi / Pending (Klien: Dian & Febri, Fotografer: Alex Visual Studio)
            // INI SESI DI MANA FOTOGRAFER BISA KLIK "KONFIRMASI" ATAU "TOLAK SESI"
            [
                'client' => $clientDian,
                'photographer' => $photoAlex,
                'service' => $srvAlex,
                'amount' => 3500000,
                'created_at' => Carbon::create(2026, 10, 22, 16, 20),
                'session_date' => Carbon::create(2026, 11, 5),
                'location' => 'Taman Ujung Sukasada Karangasem, Bali',
                'status' => 'pending',
                'payout_status' => 'holding',
                'drive_url' => null,
                'catatan_fotografer' => null,
                'review' => null,
                'rating' => null,
            ],
            // Bulan 10: Okt 2026 - Sesi Menunggu Konfirmasi / Pending (Klien: PT Bali Luxury Resort)
            [
                'client' => $clientResort,
                'photographer' => $photoBali,
                'service' => $srvBali,
                'amount' => 1800000,
                'created_at' => Carbon::create(2026, 10, 25, 11, 40),
                'session_date' => Carbon::create(2026, 11, 12),
                'location' => 'Private Beach Nusa Dua, Bali',
                'status' => 'pending',
                'payout_status' => 'holding',
                'drive_url' => null,
                'catatan_fotografer' => null,
                'review' => null,
                'rating' => null,
            ],
            // Bulan 11: Nov 2026 - Sesi Dikonfirmasi / Active (Klien: Dian & Febri, Fotografer: Budi Event Captures)
            [
                'client' => $clientDian,
                'photographer' => $photoBudi,
                'service' => $srvBudi,
                'amount' => 2800000,
                'created_at' => Carbon::create(2026, 11, 1, 10, 0),
                'session_date' => Carbon::create(2026, 11, 10),
                'location' => 'Istora Senayan, Jakarta Pusat',
                'status' => 'confirmed',
                'payout_status' => 'holding',
                'drive_url' => null,
                'catatan_fotografer' => null,
                'review' => null,
                'rating' => null,
            ],
            // Bulan 11: Nov 2026 - Sesi Dikonfirmasi / Active (Klien: PT Mahkota Event, Fotografer: Budi Event Captures)
            [
                'client' => $clientMahkota,
                'photographer' => $photoBudi,
                'service' => $srvBudi,
                'amount' => 3500000,
                'created_at' => Carbon::create(2026, 11, 2, 14, 0),
                'session_date' => Carbon::create(2026, 11, 18),
                'location' => 'JIExpo Kemayoran Hall D2, Jakarta Pusat',
                'status' => 'confirmed',
                'payout_status' => 'holding',
                'drive_url' => null,
                'catatan_fotografer' => null,
                'review' => null,
                'rating' => null,
            ],
            // Bulan 11: Nov 2026 - Sesi Dikonfirmasi / Active (Klien: Siti Rahmawati, Fotografer: Alex Visual Studio)
            [
                'client' => $clientSiti,
                'photographer' => $photoAlex,
                'service' => $srvAlex,
                'amount' => 3500000,
                'created_at' => Carbon::create(2026, 11, 3, 9, 0),
                'session_date' => Carbon::create(2026, 11, 9),
                'location' => 'Kebun Raya Bogor, Jawa Barat',
                'status' => 'confirmed',
                'payout_status' => 'holding',
                'drive_url' => null,
                'catatan_fotografer' => null,
                'review' => null,
                'rating' => null,
            ],
        ];

        $invoiceCounter = 1;

        foreach ($dataset as $row) {
            $fee = $row['amount'] * 0.10; // 10% komisi marketplace
            $payout = $row['amount'] - $fee; // 90% hak fotografer

            $contractStatus = match($row['status']) {
                'completed' => 'completed',
                'confirmed' => 'active',
                default => 'pending_payment',
            };

            $contract = Contract::create([
                'id_client' => $row['client']->id,
                'id_photographer' => $row['photographer']->id,
                'jumlah' => $row['amount'],
                'payment_type' => 'full',
                'jumlah_dp' => null,
                'fee_marketplace' => $fee,
                'payout_amount' => $payout,
                'status_payout' => $row['payout_status'],
                'payout_at' => $row['payout_status'] === 'paid' ? $row['session_date']->copy()->addDays(2) : null,
                'payout_notes' => $row['payout_status'] === 'paid' ? 'Dana 90% berhasil ditransfer ke rekening fotografer.' : null,
                'is_validated_photographer' => in_array($row['status'], ['confirmed', 'completed']),
                'is_validated_client' => true,
                'expired_at' => $row['created_at']->copy()->addDays(2),
                'status_contract' => $contractStatus,
                'created_at' => $row['created_at'],
                'updated_at' => $row['created_at'],
            ]);

            ContractBooking::create([
                'id_contract' => $contract->id,
                'id_service' => $row['service'] ? $row['service']->id : null,
                'booking_date' => $row['session_date']->toDateString(),
                'jam_mulai' => '08:00:00',
                'jam_selesai' => '12:00:00',
                'lokasi' => $row['location'],
                'catatan_khusus' => 'Sesi pemotretan resmi dipesan via portal LensMatch.',
                'hasil_foto_url' => $row['drive_url'],
                'catatan_fotografer' => $row['catatan_fotografer'],
                'durasi' => '4 Jam',
                'status_booking' => $row['status'],
                'created_at' => $row['created_at'],
                'updated_at' => $row['created_at'],
            ]);

            // Buat Record Payment Resmi
            $invNumber = 'INV-LM-2026-' . str_pad($invoiceCounter++, 3, '0', STR_PAD_LEFT);
            $paymentStatus = in_array($row['status'], ['confirmed', 'completed']) ? 'completed' : 'pending';

            Payment::create([
                'id_contract' => $contract->id,
                'payment_amount' => $row['amount'],
                'payment_status' => $paymentStatus,
                'contract_payment_type' => 'full',
                'payment_link' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/' . $invNumber,
                'external_id' => $invNumber,
                'payment_type' => 'payment gateway',
                'created_at' => $row['created_at'],
                'updated_at' => $row['created_at'],
            ]);

            // Jika Sesi Selesai dan Ada Ulasan, Buat Testimonial
            if ($row['status'] === 'completed' && $row['review']) {
                Testimonial::create([
                    'id_client' => $row['client']->id,
                    'id_photographer' => $row['photographer']->id,
                    'id_contract' => $contract->id,
                    'deskripsi_review' => $row['review'],
                    'rating' => $row['rating'],
                    'is_hidden' => false,
                    'created_at' => $row['session_date']->copy()->addDays(1),
                    'updated_at' => $row['session_date']->copy()->addDays(1),
                ]);
            }

            // Tambahkan Notifikasi Nyata untuk User (Rule #89: Bebas Simbol #)
            if ($row['client']->user) {
                if ($row['status'] === 'completed') {
                    Notification::create([
                        'id_user' => $row['client']->user->id,
                        'title' => 'Galeri Hasil Foto Siap Diunduh',
                        'message' => "Fotografer {$row['photographer']->nama} telah mengunggah link galeri foto untuk sesi KTRK-" . str_pad($contract->id, 5, '0', STR_PAD_LEFT) . ".",
                        'type' => 'booking',
                        'is_read' => true,
                        'data' => ['url' => '/client/galleries'],
                        'created_at' => $row['session_date']->copy()->addDay(),
                    ]);
                } elseif ($row['status'] === 'confirmed') {
                    Notification::create([
                        'id_user' => $row['client']->user->id,
                        'title' => 'Reservasi Sesi Foto Dikonfirmasi',
                        'message' => "Jadwal sesi foto Anda dengan {$row['photographer']->nama} pada {$row['session_date']->format('d M Y')} telah dikonfirmasi fotografer.",
                        'type' => 'booking',
                        'is_read' => false,
                        'data' => ['url' => '/client/bookings'],
                        'created_at' => $row['created_at']->copy()->addHours(2),
                    ]);
                }
            }

            if ($row['photographer']->user) {
                if ($row['status'] === 'pending') {
                    Notification::create([
                        'id_user' => $row['photographer']->user->id,
                        'title' => 'Pesanan Booking Baru Masuk',
                        'message' => "Klien {$row['client']->nama} mengajukan reservasi sesi foto baru (KTRK-" . str_pad($contract->id, 5, '0', STR_PAD_LEFT) . "). Silakan konfirmasi ketersediaan jadwal Anda.",
                        'type' => 'booking',
                        'is_read' => false,
                        'data' => ['url' => '/photographer/bookings'],
                        'created_at' => $row['created_at'],
                    ]);
                }
            }
        }

        // Seed Real Availability / Libur untuk Kalender Ketersediaan (Anti-AI Slop Data Riil)
        $availabilities = [
            // Budi Event Captures (ID 2)
            [
                'id_photographer' => $photoBudi->id,
                'tanggal' => '2026-11-14',
                'status' => 'blocked',
                'keterangan' => 'Libur Operasional Studio & Maintenance Alat Kamera',
            ],
            [
                'id_photographer' => $photoBudi->id,
                'tanggal' => '2026-11-22',
                'status' => 'blocked',
                'keterangan' => 'Cuti Pribadi Akhir Pekan',
            ],
            // Alex Visual Studio (ID 1)
            [
                'id_photographer' => $photoAlex->id,
                'tanggal' => '2026-11-20',
                'status' => 'blocked',
                'keterangan' => 'Workshop Fotografi Outdoor',
            ],
            // Clara Commercial (ID 3)
            [
                'id_photographer' => $photoClara->id,
                'tanggal' => '2026-11-26',
                'status' => 'blocked',
                'keterangan' => 'Upgrade Lighting & Studio Renovation',
            ],
        ];

        foreach ($availabilities as $av) {
            PhotographerAvailability::firstOrCreate(
                ['id_photographer' => $av['id_photographer'], 'tanggal' => $av['tanggal']],
                [
                    'status' => $av['status'],
                    'keterangan' => $av['keterangan'],
                    'created_at' => Carbon::create(2026, 11, 1),
                    'updated_at' => Carbon::create(2026, 11, 1),
                ]
            );
        }
    }
}
