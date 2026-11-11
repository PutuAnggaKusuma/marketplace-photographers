<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\PhotographerPortfolio;
use App\Models\PhotographerService;
use App\Models\PhotographerWorkingHour;
use App\Models\PortfolioMedia;
use App\Models\RolePhotographer;
use App\Models\ServiceDetail;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\Province;

class PhotographerSeeder extends Seeder
{
    public function run(): void
    {
        $catWedding = Category::firstOrCreate(['nama_kategori' => 'Wedding & Prewedding']);
        $catTravel  = Category::firstOrCreate(['nama_kategori' => 'Vacation & Travel']);
        $catEvent   = Category::firstOrCreate(['nama_kategori' => 'Event & Konser']);
        $catPortrait= Category::firstOrCreate(['nama_kategori' => 'Studio & Portrait'], ['icon_url' => 'https://images.unsplash.com/photo-1768818928341-3b31b8965c6d?q=80&w=627&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=NHwxMjA3fDA%3D', 'slug' => 'studio-portrait']);
        $catProduct = Category::firstOrCreate(['nama_kategori' => 'Commercial & Produk']);

        $provBali = Province::where('name', 'LIKE', '%BALI%')->first();
        $cityDenpasar = City::where('name', 'LIKE', '%DENPASAR%')->first();
        $provJakarta = Province::where('name', 'LIKE', '%JAKARTA%')->first();
        $cityJaksel = City::where('name', 'LIKE', '%JAKARTA SELATAN%')->first();
        $provJabar = Province::where('name', 'LIKE', '%JAWA BARAT%')->first();
        $cityBandung = City::where('name', 'LIKE', '%BANDUNG%')->first();
        $provYogya = Province::where('name', 'LIKE', '%YOGYAKARTA%')->first();
        $cityYogya = City::where('name', 'LIKE', '%YOGYAKARTA%')->first();

        // 1. Alex Visual Studio
        $userAlex = User::firstOrCreate(
            ['email' => 'alex@lensmatch.com'],
            [
                'nama' => 'Alex Visual Studio',
                'password' => Hash::make('password123'),
                'role' => 'photographer',
                'is_protected' => false,
                'email_verified_at' => now(),
            ]
        );

        $alexPhoto = RolePhotographer::updateOrCreate(
            ['id_user' => $userAlex->id],
            [
                'nama' => 'Alex Visual Studio',
                'nomor_telepon' => '081234567890',
                'link_sosmed' => 'https://instagram.com/alexvisual',
                'foto' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=600&q=80',
                'foto_sampul' => 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=1200&q=80',
                'alamat' => 'Jl. Sunset Road No. 88, Kuta, Bali',
                'province_code' => $provBali ? $provBali->code : '51',
                'city_code' => $cityDenpasar ? $cityDenpasar->code : '5171',
                'deskripsi_bio' => 'Fotografer profesional berlisensi di Bali dengan pengalaman lebih dari 8 tahun menangani sesi Prewedding outdoor, dokumentasi pernikahan impian, dan portrait bertema alam.',
            ]
        );
        $alexPhoto->categories()->sync([$catWedding->id, $catPortrait->id]);

        $srvAlex1 = PhotographerService::updateOrCreate(
            ['id_photographer' => $alexPhoto->id, 'nama_layanan' => 'Prewedding Outdoor Sesi Sunset Beach'],
            [
                'tarif_harga' => 3500000,
                'deskripsi_layanan' => 'Paket sesi foto prewedding durasi 4 jam di lokasi pantai pilihan di Bali dengan 20 foto ter-retouch halus & semua file mentah.',
            ]
        );
        ServiceDetail::firstOrCreate(['id_p_service' => $srvAlex1->id, 'nama_fitur' => 'Durasi 4 Jam Fotografi', 'tarif_harga' => 0]);
        ServiceDetail::firstOrCreate(['id_p_service' => $srvAlex1->id, 'nama_fitur' => '20 Hi-Res Edited Photos', 'tarif_harga' => 0]);
        ServiceDetail::firstOrCreate(['id_p_service' => $srvAlex1->id, 'nama_fitur' => 'Termasuk Cetak Canvas 40x60cm', 'tarif_harga' => 500000]);

        $portAlex1 = PhotographerPortfolio::updateOrCreate(
            ['id_photographer' => $alexPhoto->id, 'judul' => 'Romantic Sunset at Melasti Beach'],
            ['deskripsi' => 'Sesi prewedding momen matahari terbenam dengan gaun putih dan pemandangan tebing Melasti Bali.']
        );
        PortfolioMedia::firstOrCreate(['id_portofolio' => $portAlex1->id, 'media' => 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=1200&q=80']);
        PortfolioMedia::firstOrCreate(['id_portofolio' => $portAlex1->id, 'media' => 'https://images.unsplash.com/photo-1511285560929-80b456fea0bc?auto=format&fit=crop&w=800&q=80']);
        PortfolioMedia::firstOrCreate(['id_portofolio' => $portAlex1->id, 'media' => 'https://images.unsplash.com/photo-1583939003579-730e3918a45a?auto=format&fit=crop&w=800&q=80']);
        PortfolioMedia::firstOrCreate(['id_portofolio' => $portAlex1->id, 'media' => 'https://images.unsplash.com/photo-1519225429980-715cb0215aed?auto=format&fit=crop&w=800&q=80']);
        PortfolioMedia::firstOrCreate(['id_portofolio' => $portAlex1->id, 'media' => 'https://images.unsplash.com/photo-1465495976277-4387d4b0b4c6?auto=format&fit=crop&w=800&q=80']);

        // 2. Budi Event Captures
        $userBudi = User::firstOrCreate(
            ['email' => 'budi@lensmatch.com'],
            [
                'nama' => 'Budi Event Captures',
                'password' => Hash::make('password123'),
                'role' => 'photographer',
                'is_protected' => false,
                'email_verified_at' => now(),
            ]
        );

        $budiPhoto = RolePhotographer::updateOrCreate(
            ['id_user' => $userBudi->id],
            [
                'nama' => 'Budi Event Captures',
                'nomor_telepon' => '082198765432',
                'link_sosmed' => 'https://instagram.com/budievent',
                'foto' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=600&q=80',
                'foto_sampul' => 'https://images.unsplash.com/photo-1501386761578-eac5c94b800a?auto=format&fit=crop&w=1200&q=80',
                'alamat' => 'Jl. Kemang Raya No. 12, Jakarta Selatan',
                'province_code' => $provJakarta ? $provJakarta->code : '31',
                'city_code' => $cityJaksel ? $cityJaksel->code : '3174',
                'deskripsi_bio' => 'Spesialis fotografi event, panggung musik/konser, dan gathering perusahaan. Merekam momen dramatis dan energi panggung dengan lensa sudut lebar & telephoto.',
            ]
        );
        $budiPhoto->categories()->sync([$catEvent->id, $catProduct->id]);

        $srvBudi1 = PhotographerService::updateOrCreate(
            ['id_photographer' => $budiPhoto->id, 'nama_layanan' => 'Dokumentasi Konser Musik & Festival Stage'],
            [
                'tarif_harga' => 2800000,
                'deskripsi_layanan' => 'Liputan panggung musik dari soundcheck hingga main performer dengan kamera dual-body.',
            ]
        );
        ServiceDetail::firstOrCreate(['id_p_service' => $srvBudi1->id, 'nama_fitur' => 'Durasi Liputan Sesi Konser hingga 6 Jam (Soundcheck s/d Main Show)', 'tarif_harga' => 0]);
        ServiceDetail::firstOrCreate(['id_p_service' => $srvBudi1->id, 'nama_fitur' => 'Fotografer Dual-Body Camera (Lensa Wide 16-35mm & Tele 70-200mm f/2.8)', 'tarif_harga' => 0]);
        ServiceDetail::firstOrCreate(['id_p_service' => $srvBudi1->id, 'nama_fitur' => '50 Foto Retouched & Color Graded Resolusi Tinggi', 'tarif_harga' => 0]);
        ServiceDetail::firstOrCreate(['id_p_service' => $srvBudi1->id, 'nama_fitur' => 'Semua File Original Mentah via Google Drive Cloud Max 48 Jam', 'tarif_harga' => 0]);
        ServiceDetail::firstOrCreate(['id_p_service' => $srvBudi1->id, 'nama_fitur' => 'Hak Cipta Penggunaan Komersial & Promosi Media Sosial', 'tarif_harga' => 0]);

        $portBudi1 = PhotographerPortfolio::updateOrCreate(
            ['id_photographer' => $budiPhoto->id, 'judul' => 'Jakarta Live Music Festival 2025'],
            ['deskripsi' => 'Aksi panggung musisi papan atas Indonesia dalam konser musik outdoor skala besar.']
        );
        PortfolioMedia::firstOrCreate(['id_portofolio' => $portBudi1->id, 'media' => 'https://images.unsplash.com/photo-1501386761578-eac5c94b800a?auto=format&fit=crop&w=1200&q=80']);
        PortfolioMedia::firstOrCreate(['id_portofolio' => $portBudi1->id, 'media' => 'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?auto=format&fit=crop&w=800&q=80']);
        PortfolioMedia::firstOrCreate(['id_portofolio' => $portBudi1->id, 'media' => 'https://images.unsplash.com/photo-1459749411175-04bf5292ceea?auto=format&fit=crop&w=800&q=80']);
        PortfolioMedia::firstOrCreate(['id_portofolio' => $portBudi1->id, 'media' => 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?auto=format&fit=crop&w=800&q=80']);

        $portBudi2 = PhotographerPortfolio::updateOrCreate(
            ['id_photographer' => $budiPhoto->id, 'judul' => 'Nusantara Tech Summit dan Gala Dinner 2025'],
            ['deskripsi' => 'Dokumentasi konferensi teknologi nasional dan malam apresiasi tahunan di Jakarta Convention Center.']
        );
        PortfolioMedia::firstOrCreate(['id_portofolio' => $portBudi2->id, 'media' => 'https://images.unsplash.com/photo-1511578314322-379afb476865?auto=format&fit=crop&w=1200&q=80']);
        PortfolioMedia::firstOrCreate(['id_portofolio' => $portBudi2->id, 'media' => 'https://images.unsplash.com/photo-1475721027785-f74eccf877e2?auto=format&fit=crop&w=800&q=80']);
        PortfolioMedia::firstOrCreate(['id_portofolio' => $portBudi2->id, 'media' => 'https://images.unsplash.com/photo-1528605248644-14dd04022da1?auto=format&fit=crop&w=800&q=80']);
        PortfolioMedia::firstOrCreate(['id_portofolio' => $portBudi2->id, 'media' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?auto=format&fit=crop&w=800&q=80']);

        $portBudi3 = PhotographerPortfolio::updateOrCreate(
            ['id_photographer' => $budiPhoto->id, 'judul' => 'Soundwave Indie Showcase Senayan'],
            ['deskripsi' => 'Sorotan aksi panggung intim musisi indie dan ekspresi otentik penonton di Senayan Park.']
        );
        PortfolioMedia::firstOrCreate(['id_portofolio' => $portBudi3->id, 'media' => 'https://images.unsplash.com/photo-1429962714451-bb934ecdc4ec?auto=format&fit=crop&w=1200&q=80']);
        PortfolioMedia::firstOrCreate(['id_portofolio' => $portBudi3->id, 'media' => 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?auto=format&fit=crop&w=800&q=80']);
        PortfolioMedia::firstOrCreate(['id_portofolio' => $portBudi3->id, 'media' => 'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?auto=format&fit=crop&w=800&q=80']);
        PortfolioMedia::firstOrCreate(['id_portofolio' => $portBudi3->id, 'media' => 'https://images.unsplash.com/photo-1465847899084-d164df4dedc6?auto=format&fit=crop&w=800&q=80']);
        PortfolioMedia::firstOrCreate(['id_portofolio' => $portBudi3->id, 'media' => 'https://images.unsplash.com/photo-1506157786151-b8491531f063?auto=format&fit=crop&w=800&q=80']);

        // 3. Clara Commercial
        $userClara = User::firstOrCreate(
            ['email' => 'clara@lensmatch.com'],
            [
                'nama' => 'Clara Commercial',
                'password' => Hash::make('password123'),
                'role' => 'photographer',
                'is_protected' => false,
                'email_verified_at' => now(),
            ]
        );

        $claraPhoto = RolePhotographer::updateOrCreate(
            ['id_user' => $userClara->id],
            [
                'nama' => 'Clara Commercial Studio',
                'nomor_telepon' => '085712345678',
                'link_sosmed' => 'https://instagram.com/claracommercial',
                'foto' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=600&q=80',
                'foto_sampul' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=1200&q=80',
                'alamat' => 'Jl. Riau No. 45, Bandung, Jawa Barat',
                'province_code' => $provJabar ? $provJabar->code : '32',
                'city_code' => $cityBandung ? $cityBandung->code : '3273',
                'deskripsi_bio' => 'Fotografer spesialis katalog produk komersial UMKM, kuliner, dan fashion hijab. Menghasilkan foto berestetika bersih yang siap meningkatkan penjualan toko online Anda.',
            ]
        );
        $claraPhoto->categories()->sync([$catProduct->id, $catPortrait->id]);

        $srvClara1 = PhotographerService::updateOrCreate(
            ['id_photographer' => $claraPhoto->id, 'nama_layanan' => 'Katalog Foto Produk UMKM Kuliner & Fashion'],
            [
                'tarif_harga' => 1500000,
                'deskripsi_layanan' => 'Sesi foto 15 varian produk dengan lighting studio & props pendukung.',
            ]
        );
        ServiceDetail::firstOrCreate(['id_p_service' => $srvClara1->id, 'nama_fitur' => 'Sesi Foto Studio / On-Location hingga 15 Varian Produk', 'tarif_harga' => 0]);
        ServiceDetail::firstOrCreate(['id_p_service' => $srvClara1->id, 'nama_fitur' => 'Termasuk Lighting Studio Lengkap & Background Props Estetik', 'tarif_harga' => 0]);
        ServiceDetail::firstOrCreate(['id_p_service' => $srvClara1->id, 'nama_fitur' => '30 Foto High-Resolution Siap Pakai Marketplace & Katalog', 'tarif_harga' => 0]);
        ServiceDetail::firstOrCreate(['id_p_service' => $srvClara1->id, 'nama_fitur' => 'Retouching Warna & Pembersihan Noda Produk Digital', 'tarif_harga' => 0]);

        $portClara1 = PhotographerPortfolio::updateOrCreate(
            ['id_photographer' => $claraPhoto->id, 'judul' => 'Culinary Product Shoot for Bu Lilik'],
            ['deskripsi' => 'Pencahayaan makanan natural untuk kemasan kuliner tradisional Indonesia.']
        );
        PortfolioMedia::firstOrCreate(['id_portofolio' => $portClara1->id, 'media' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=1200&q=80']);
        PortfolioMedia::firstOrCreate(['id_portofolio' => $portClara1->id, 'media' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=800&q=80']);
        PortfolioMedia::firstOrCreate(['id_portofolio' => $portClara1->id, 'media' => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&w=800&q=80']);
        PortfolioMedia::firstOrCreate(['id_portofolio' => $portClara1->id, 'media' => 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?auto=format&fit=crop&w=800&q=80']);

        // 4. Dewa Portrait Studio
        $userDewa = User::firstOrCreate(
            ['email' => 'dewa@lensmatch.com'],
            [
                'nama' => 'Dewa Portrait Studio',
                'password' => Hash::make('password123'),
                'role' => 'photographer',
                'is_protected' => false,
                'email_verified_at' => now(),
            ]
        );

        $dewaPhoto = RolePhotographer::updateOrCreate(
            ['id_user' => $userDewa->id],
            [
                'nama' => 'Dewa Portrait Studio',
                'nomor_telepon' => '087812349988',
                'link_sosmed' => 'https://instagram.com/dewaportrait',
                'foto' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=600&q=80',
                'foto_sampul' => 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?w=1200&q=80',
                'alamat' => 'Jl. Kaliurang Km 5.5, Yogyakarta',
                'province_code' => $provYogya ? $provYogya->code : '34',
                'city_code' => $cityYogya ? $cityYogya->code : '3471',
                'deskripsi_bio' => 'Fotografer portrait klasik & modern di Yogyakarta. Fokus pada pencahayaan emosional untuk sesi wisuda, kelulusan, dan foto keluarga berkelas.',
            ]
        );
        $dewaPhoto->categories()->sync([$catPortrait->id, $catWedding->id]);

        $srvDewa1 = PhotographerService::updateOrCreate(
            ['id_photographer' => $dewaPhoto->id, 'nama_layanan' => 'Sesi Portrait Wisuda & Keluarga Exclusive'],
            [
                'tarif_harga' => 2200000,
                'deskripsi_layanan' => 'Sesi foto indoor studio 2 jam untuk keluarga & wisudawan dengan 10 foto cetak 10R.',
            ]
        );
        ServiceDetail::firstOrCreate(['id_p_service' => $srvDewa1->id, 'nama_fitur' => 'Sesi Studio Indoor Eksklusif Durasi 2 Jam', 'tarif_harga' => 0]);
        ServiceDetail::firstOrCreate(['id_p_service' => $srvDewa1->id, 'nama_fitur' => 'Maksimal hingga 8 Anggota Keluarga', 'tarif_harga' => 0]);
        ServiceDetail::firstOrCreate(['id_p_service' => $srvDewa1->id, 'nama_fitur' => '10 Foto Pilihan Cetak Ukuran 10R Berkualitas Premium', 'tarif_harga' => 0]);
        ServiceDetail::firstOrCreate(['id_p_service' => $srvDewa1->id, 'nama_fitur' => 'Semua File Foto Master Resolusi Penuh via Cloud Drive', 'tarif_harga' => 0]);

        $portDewa1 = PhotographerPortfolio::updateOrCreate(
            ['id_photographer' => $dewaPhoto->id, 'judul' => 'Portrait Wisuda & Keluarga Taman Sari'],
            ['deskripsi' => 'Dokumentasi potret wisuda dan keluarga di Yogyakarta dengan pencahayaan alami dan komposisi klasik.']
        );
        PortfolioMedia::firstOrCreate(['id_portofolio' => $portDewa1->id, 'media' => 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?w=1200&q=80']);
        PortfolioMedia::firstOrCreate(['id_portofolio' => $portDewa1->id, 'media' => 'https://images.unsplash.com/photo-1511895426328-dc8714191300?w=1200&q=80']);
        PortfolioMedia::firstOrCreate(['id_portofolio' => $portDewa1->id, 'media' => 'https://images.unsplash.com/photo-1768818928341-3b31b8965c6d?q=80&w=1200&auto=format&fit=crop']);
        PortfolioMedia::firstOrCreate(['id_portofolio' => $portDewa1->id, 'media' => 'https://images.unsplash.com/photo-1450133064473-71024230f91b?w=1200&q=80']);

        // 5. Bali Lens Team
        $userBaliLens = User::firstOrCreate(
            ['email' => 'balilens@lensmatch.com'],
            [
                'nama' => 'Bali Lens Team',
                'password' => Hash::make('password123'),
                'role' => 'photographer',
                'is_protected' => false,
                'email_verified_at' => now(),
            ]
        );

        $baliPhoto = RolePhotographer::updateOrCreate(
            ['id_user' => $userBaliLens->id],
            [
                'nama' => 'Bali Lens Holiday Photography',
                'nomor_telepon' => '081399887766',
                'link_sosmed' => 'https://instagram.com/balilens',
                'foto' => 'https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?auto=format&fit=crop&w=600&q=80',
                'foto_sampul' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1200&q=80',
                'alamat' => 'Jl. Pantai Sanur No. 22, Denpasar, Bali',
                'province_code' => $provBali ? $provBali->code : '51',
                'city_code' => $cityDenpasar ? $cityDenpasar->code : '5171',
                'deskripsi_bio' => 'Jasa dokumentasi foto liburan keliling Bali untuk wisatawan lokal & mancanegara. Pilihan lokasi fleksibel: Canggu, Ubud, Sanur, atau Uluwatu.',
            ]
        );
        $baliPhoto->categories()->sync([$catTravel->id, $catEvent->id]);

        $srvBali1 = PhotographerService::updateOrCreate(
            ['id_photographer' => $baliPhoto->id, 'nama_layanan' => 'Holiday Vacation Photo Session'],
            [
                'tarif_harga' => 1800000,
                'deskripsi_layanan' => 'Foto jalan-jalan santai 2.5 jam di spot ikonik Bali dengan proses edit warna cepat di hari yang sama.',
            ]
        );
        ServiceDetail::firstOrCreate(['id_p_service' => $srvBali1->id, 'nama_fitur' => 'Sesi Foto Santai Durasi 2.5 Jam di 2 Spot Ikonik Bali', 'tarif_harga' => 0]);
        ServiceDetail::firstOrCreate(['id_p_service' => $srvBali1->id, 'nama_fitur' => 'Bebas Ganti Kostum (Maksimal 2 Pakaian)', 'tarif_harga' => 0]);
        ServiceDetail::firstOrCreate(['id_p_service' => $srvBali1->id, 'nama_fitur' => '40 Foto Edit Warna Khas Liburan (Selesai dalam 24 Jam)', 'tarif_harga' => 0]);
        ServiceDetail::firstOrCreate(['id_p_service' => $srvBali1->id, 'nama_fitur' => 'Semua File Original Dikirim via Cloud Drive', 'tarif_harga' => 0]);

        $portBali1 = PhotographerPortfolio::updateOrCreate(
            ['id_photographer' => $baliPhoto->id, 'judul' => 'Exploring Nusa Penida & Uluwatu Cliff'],
            ['deskripsi' => 'Sesi foto liburan santai di pesisir Bali dengan komposisi lanskap tropis dan warna natural.']
        );
        PortfolioMedia::firstOrCreate(['id_portofolio' => $portBali1->id, 'media' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1200&q=80']);
        PortfolioMedia::firstOrCreate(['id_portofolio' => $portBali1->id, 'media' => 'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?w=1200&q=80']);
        PortfolioMedia::firstOrCreate(['id_portofolio' => $portBali1->id, 'media' => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?w=1200&q=80']);
        PortfolioMedia::firstOrCreate(['id_portofolio' => $portBali1->id, 'media' => 'https://images.unsplash.com/photo-1542038784456-1ea8e935640e?w=1200&q=80']);

        // Tandai 5 fotografer demo sebagai terverifikasi profil, jadwal terkonfirmasi, dan disetujui tayang
        foreach ([$alexPhoto, $budiPhoto, $claraPhoto, $dewaPhoto, $baliPhoto] as $seededPhoto) {
            PhotographerWorkingHour::initializeDefaults($seededPhoto->id);
            $seededPhoto->update([
                'is_onboarded' => true,
                'is_verified' => true,
                'status_verifikasi' => 'terverifikasi',
                'status_etalase' => 'disetujui',
                'jadwal_dikonfirmasi' => true,
                'etalase_diajukan_at' => now(),
                'etalase_disetujui_at' => now(),
                'nama_bank' => $seededPhoto->nama_bank ?: 'BCA',
                'nomor_rekening' => $seededPhoto->nomor_rekening ?: '8830129841',
                'atas_nama_rekening' => $seededPhoto->atas_nama_rekening ?: $seededPhoto->nama,
            ]);
        }

        // Seed professional FAQs for each photographer in database (Rule #94 Compliant)
        $defaultFaqs = [
            [
                'pertanyaan' => 'Bagaimana alur reservasi sesi foto di LensMatch?',
                'jawaban' => 'Pilih paket layanan yang sesuai di profil fotografer ini, tentukan tanggal yang tersedia pada kalender, lalu kirimkan rincian reservasi. Fotografer akan mengonfirmasi jadwal pemotretan Anda maksimal 1x24 jam.',
                'urutan' => 1,
            ],
            [
                'pertanyaan' => 'Bagaimana sistem pembayaran dan keamanan transaksi?',
                'jawaban' => 'Pembayaran dilakukan secara resmi melalui transfer bank yang diverifikasi otomatis oleh sistem. Dana pembayaran Anda diamankan oleh sistem platform LensMatch dan baru diteruskan ke fotografer setelah sesi pemotretan selesai.',
                'urutan' => 2,
            ],
            [
                'pertanyaan' => 'Kapan jadwal sesi foto saya dinyatakan resmi terkonfirmasi?',
                'jawaban' => 'Jadwal pemotretan resmi terkonfirmasi setelah fotografer menyetujui tanggal reservasi Anda dan pembayaran uang muka (DP 30%) atau pembayaran penuh berhasil terverifikasi oleh sistem.',
                'urutan' => 3,
            ],
            [
                'pertanyaan' => 'Apakah saya bisa berdiskusi mengenai konsep atau tema pemotretan sebelum booking?',
                'jawaban' => 'Tentu saja. Anda dapat menekan tombol Chat pada profil ini untuk mendiskusikan konsep visual, referensi moodboard, busana, maupun rekomendasi spot foto secara langsung sebelum melakukan pemesanan.',
                'urutan' => 4,
            ],
            [
                'pertanyaan' => 'Bagaimana jika saya perlu mengajukan jadwal ulang (reschedule) sesi foto?',
                'jawaban' => 'Anda dapat mengajukan permohonan reschedule melalui menu Reservasi Saya dengan memilih tanggal baru yang masih tersedia pada kalender fotografer. Jadwal baru berlaku setelah disepakati kedua belah pihak.',
                'urutan' => 5,
            ],
            [
                'pertanyaan' => 'Berapa lama proses retouching dan bagaimana saya menerima hasil foto?',
                'jawaban' => 'Proses retouching umumnya memerlukan waktu 3 hingga 7 hari kerja tergantung paket layanan. Setelah selesai, fotografer akan mengunggah tautan album resolusi tinggi yang dapat Anda akses di Galeri Hasil Foto Saya.',
                'urutan' => 6,
            ],
            [
                'pertanyaan' => 'Bagaimana jika terjadi kendala cuaca buruk pada hari pemotretan outdoor?',
                'jawaban' => 'Jika terjadi hujan lebat atau cuaca ekstrem pada lokasi outdoor, Anda dan fotografer dapat menyepakati penyesuaian jam pemotretan di hari yang sama atau penjadwalan ulang sesi (reschedule) tanpa biaya tambahan.',
                'urutan' => 7,
            ],
        ];

        foreach (RolePhotographer::all() as $photo) {
            foreach ($defaultFaqs as $faq) {
                \App\Models\PhotographerFaq::firstOrCreate(
                    [
                        'id_photographer' => $photo->id,
                        'pertanyaan' => $faq['pertanyaan']
                    ],
                    [
                        'jawaban' => $faq['jawaban'],
                        'urutan' => $faq['urutan'],
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}