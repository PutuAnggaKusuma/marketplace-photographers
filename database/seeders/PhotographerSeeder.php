<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\PhotographerPortfolio;
use App\Models\PhotographerService;
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
        $catPortrait= Category::firstOrCreate(['nama_kategori' => 'Studio & Portrait']);
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
        PortfolioMedia::firstOrCreate(['id_portofolio' => $portAlex1->id, 'media' => 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=800&q=80']);
        PortfolioMedia::firstOrCreate(['id_portofolio' => $portAlex1->id, 'media' => 'https://images.unsplash.com/photo-1511285560929-80b456fea0bc?auto=format&fit=crop&w=800&q=80']);

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

        $portBudi1 = PhotographerPortfolio::updateOrCreate(
            ['id_photographer' => $budiPhoto->id, 'judul' => 'Jakarta Live Music Festival 2025'],
            ['deskripsi' => 'Aksi panggung musisi papan atas Indonesia dalam konser musik outdoor skala besar.']
        );
        PortfolioMedia::firstOrCreate(['id_portofolio' => $portBudi1->id, 'media' => 'https://images.unsplash.com/photo-1501386761578-eac5c94b800a?auto=format&fit=crop&w=800&q=80']);

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

        $portClara1 = PhotographerPortfolio::updateOrCreate(
            ['id_photographer' => $claraPhoto->id, 'judul' => 'Culinary Product Shoot for Bu Lilik'],
            ['deskripsi' => 'Pencahayaan makanan natural untuk kemasan kuliner tradisional Indonesia.']
        );
        PortfolioMedia::firstOrCreate(['id_portofolio' => $portClara1->id, 'media' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=800&q=80']);

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
    }
}