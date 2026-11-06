# Tracking Perubahan File — LensMatch Platform

Dokumen ini mencatat seluruh riwayat dan log perubahan file (*audit trail & change tracking*) pada proyek LensMatch. **Setiap kali terjadi perubahan pada kode program, file baru, penyesuaian tampilan (UI), skema database, atau perbaikan bug, wajib dicatat di dalam dokumen ini.**

---

## Format Standar Pencatatan (Panduan Penambahan Log)

Setiap entri log perubahan wajib mencantumkan:
1. **Tanggal & Waktu Perubahan**: (Format: `YYYY-MM-DD HH:mm WITA/WIB`)
2. **Modul / Fitur Terkait**: Nama modul atau halaman yang disesuaikan.
3. **Daftar File yang Diubah**: Path lengkap file relatif dari root project.
4. **Rincian Perubahan (Before vs After)**: Penjelasan teknis apa yang diubah dan mengapa diubah.
5. **Standar / Rule README yang Dipatuhi**: Nomor aturan dari `README.md` (misal Rule #66, Rule #78, dsb).
6. **Hasil Pengujian & Verifikasi**: Status pengujian (PASS/FAIL).

---

## Riwayat Log Perubahan

### [2026-11-06 17:35 WITA] — Perbaikan Bug Scope Tab Detail Fotografer & Kepatuhan Standar UI README

* **Modul / Fitur**: Profil Publik Fotografer (`/fotografer/{id}`)
* **File yang Diubah**:
  * [`resources/views/public/photographers/show.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/public/photographers/show.blade.php)

* **Rincian Perubahan**:
  1. **Perbaikan Critical Bug Alpine.js Scope (Penyebab Halaman Putih Kosong di Bawah Tab)**:
     * *Sebelumnya*: Tag pembuka `<section x-data="{ activeTab: 'services', ... }">` pada kartu profil atas sudah tertutup (`</section>`) sebelum navigasi tab dimulai. Seksi navigasi tab dan kelima panel konten tab berada di luar jangkauan scope Alpine.js, menyebabkan `ReferenceError: activeTab is not defined` saat tab diklik dan seluruh area konten tab menjadi kosong melompong.
     * *Sesudahnya*: Kedua seksi dan modal lightbox dibungkus ke dalam container terpadu `<div x-data="...">` sehingga state `activeTab`, perpindahan tab, dan fungsi lightbox tersinkronisasi sempurna.
  2. **Eliminasi Kotak Hitam Fokus Tombol Tab**:
     * *Sebelumnya*: Tombol tab menampilkan kotak garis hitam tebal (*default browser focus ring*) ketika diklik.
     * *Sesudahnya*: Ditambahkan kelas `focus:outline-none focus:ring-0` pada seluruh tombol tab dan akordeon FAQ sehingga transisi antar tab bersih dan bebas artefak outline hitam.
  3. **Penghapusan Simbol Pagar `#` pada ID Mitra (Mandat Pengguna)**:
     * *Sebelumnya*: Menggunakan `#FTR-00001`.
     * *Sesudahnya*: Menggunakan `FTR-00001` murni tanpa tanda pagar.
  4. **Pembersihan Foto Potret dari Badge Overlay (Kepatuhan Rule #66 Point 2)**:
     * *Sebelumnya*: Terdapat badge *"Verified Mitra"* yang menempel di sudut atas foto potret fotografer.
     * *Sesudahnya*: Badge yang menutupi gambar dihapus sesuai larangan mutlak Rule #66 Point 2. Status verifikasi dipindahkan dalam bentuk ikon centang amber elegan di samping nama fotografer.
  5. **Penghapusan Teks Header Redundan (Kepatuhan Rule #66 Point 2)**:
     * *Sebelumnya*: Terdapat teks kapital redundan *"MITRA FOTOGRAFER PROFESIONAL"* di atas nama fotografer.
     * *Sesudahnya*: Dihapus agar hierarki langsung menampilkan nama fotografer dan spesialisasi kategori.
  6. **Penyesuaian Ukuran Teks Tarif & Eliminasi Jargon Escrow (Kepatuhan Rule #66 Point 3 & Rule #89 Point 2)**:
     * *Sebelumnya*: Teks tarif menggunakan warna kuning raksasa *AI-slop* dan memuat teks `(Termasuk proteksi escrow)`.
     * *Sesudahnya*: Teks tarif berukuran proporsional (`text-base sm:text-lg font-bold text-gray-900 dark:text-white`) tersusun rapi dalam grid 2-kolom bersama Lokasi Studio, serta jargon teknis escrow dihilangkan dari antarmuka klien.
  7. **Penyesuaian Lebar Tombol Aksi Berbasis Konten (Kepatuhan Rule #66 Point 4)**:
     * *Sebelumnya*: Tombol primer *"Reservasi Sesi Foto Sekarang"* melebar penuh (`w-full` / `flex-1`) melintang 1200px.
     * *Sesudahnya*: Menggunakan lebar alami berbasis konten (`w-full sm:w-auto px-8 py-3.5`).
  8. **Data Dinamis Statistik 4-Kolom (Kepatuhan Rule #66 Point 5 & Rule #15 Dynamic Data)**:
     * *Sebelumnya*: Pengalaman hardcoded pasif (`5+ Tahun`), dan kotak kedua menduplikasi ID mitra (`ID Registrasi Mitra #FTR-00001`).
     * *Sesudahnya*: Pengalaman dihitung dinamis dari tahun bergabung (`{{ max(1, (int) now()->diffInYears($photographer->created_at)) }}+ Tahun`), dan kotak kedua diganti dengan jumlah portofolio riil (`{{ $photographer->portfolios->count() }} Karya`).
  9. **Standarisasi Kalender Bebas Dot Badge & Warna Blok Keras (Kepatuhan Rule #67)**:
     * *Sebelumnya*: Kotak tanggal diwarnai hijau, merah, atau kuning menyeluruh dan memiliki bulatan status (*dot badge*).
     * *Sesudahnya*: Seluruh kotak tanggal menggunakan latar netral (putih/abu-abu), bulatan dot badge dihapus total, dan indikator status murni mengandalkan *Pure Colored Text* (`Tersedia` hijau emerald, `Terisi Sesi` biru, `Libur` merah rose).
  10. **Standarisasi Navigasi Tab Horizontal (Kepatuhan Rule #78)**:
      * *Sebelumnya*: Terdapat garis pembatas horizontal bawah yang memotong layar.
      * *Sesudahnya*: Menggunakan navigasi tab teks horizontal dengan active text underline (`underline underline-offset-8 decoration-2 decoration-amber-500`) dan counter pill badge bundar tanpa border pembatas yang kaku.

* **Hasil Pengujian**:
  * Render view Blade: **PASS** (100% bebas sintaks error).
  * Pengujian 5 Tab fungsional: **PASS** (Paket Layanan, Portofolio, Kalender, Ulasan, FAQ tampil sempurna).
  * Ketiadaan simbol `#` dan teks jargon escrow: **PASS**.

---

### [2026-11-06 14:30 WITA] — Konsolidasi 49 Migrasi Database & Seeder Wilayah Indonesia

* **Modul / Fitur**: Database Schema, Migrations & Seeders
* **File yang Diubah**:
  * `database/migrations/2024_01_01_000001_create_users_table.php` s/d `2024_01_01_000049_create_activity_logs_table.php` (49 file migration konsolidasi).
  * [`database/seeders/DatabaseSeeder.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/database/seeders/DatabaseSeeder.php)
  * Seluruh file alter migration lama (`add_*_to_*_table.php`) dihapus karena sudah di-merge langsung ke tabel asal.

* **Rincian Perubahan**:
  1. **Konsolidasi 59 Migrasi Menjadi 49 Migrasi Murni `Schema::create`**:
     * Menggabungkan 11 file alter migration (seperti penambahan `province_code`, `city_code`, `is_onboarded`, `is_protected`, dsb) langsung ke skema tabel induknya masing-masing.
     * Mengurutkan seluruh 49 migrasi secara kronologis dan aman secara referensial (Foreign Key dependency-safe).
  2. **Integrasi Seeder Otomatis Wilayah Indonesia**:
     * Memanggil `\Laravolt\Indonesia\Seeds\DatabaseSeeder::class` di dalam `DatabaseSeeder.php` untuk men-seed 38 Provinsi, 514 Kota/Kabupaten, Kecamatan, dan Kelurahan se-Indonesia secara otomatis.
  3. **Penataan Urutan Topological Seeder Inti**:
     * Mengurutkan eksekusi seeder: `RoleAdminSeeder`, `RoleClientSeeder`, `RolePhotographerSeeder`, `CategorySeeder`, `PhotographerCategorySeeder`, `PhotographerServiceSeeder`, `PhotographerPortfolioSeeder`, `PhotographerFaqSeeder`, dan `TestimonialSeeder`.

* **Hasil Pengujian**:
  * `php artisan migrate:fresh --seed`: **PASS (100% selesai tanpa error FK atau deadlock)**.

---

### [2026-11-06 11:15 WITA] — Refaktorisasi Fitur Wishlist / Favorit Fotografer dengan Live Feedback

* **Modul / Fitur**: Wishlist & Favorit Fotografer (`/client/favorites`)
* **File yang Diubah**:
  * `app/Http/Controllers/Client/FavoriteController.php`
  * `resources/views/client/favorites/index.blade.php`
  * `resources/views/public/photographers/katalog.blade.php`
  * `resources/views/public/photographers/show.blade.php`
  * `routes/web.php`

* **Rincian Perubahan**:
  1. **Penerapan Standar Rule #93 (Anti-Silent Click)**:
     * Menyediakan live visual feedback via *Universal Toast Notification* (`show-toast` event) saat tombol bookmark diklik:
       * Berhasil disimpan: pesan notifikasi hijau sukses.
       * Berhasil dihapus: pesan notifikasi hijau sukses.
       * Role bukan Klien: peringatan amber bahwa fitur khusus akun Klien.
       * Belum login: pengalihan langsung ke halaman login dengan parameter intended URL.
  2. **Peletakan Posisi Tombol Bookmark**:
     * Pada kartu katalog, tombol diletakkan di sudut kiri atas foto cover dalam wadah bulat semi-transparan (`w-8 h-8 rounded-full bg-white/95 dark:bg-gray-900/95 shadow-md`).

* **Hasil Pengujian**:
  * Bookmark Toggle AJAX: **PASS**.
  * Sinkronisasi halaman `/client/favorites`: **PASS**.

