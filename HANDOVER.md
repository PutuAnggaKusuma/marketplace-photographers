# 📋 Dokumen Handover & Panduan Pengembang — LensMatch Platform

Dokumen ini merupakan panduan serah terima konteks (*handover context*) resmi untuk pengembang dan AI assistant yang akan melanjutkan pengembangan proyek **LensMatch**.

---

## 1. Arsitektur & Tech Stack
* **Nama Platform**: LensMatch (Marketplace Booking Fotografer Profesional & Komunitas Fotografi).
* **Framework**: Laravel 11/12 (PHP 8.4).
* **Database**: MySQL (`db_marketplace_freelancers`).
* **Frontend**: Blade + Tailwind CSS + Alpine.js (desain bersih, konsisten, anti-AI slop).
* **Paket Utama**:
  * `laravolt/indonesia`: Manajemen wilayah Indonesia (Provinsi, Kota, Kecamatan, Kelurahan).
  * `spatie/laravel-activitylog`: Audit trail aktivitas sistem.
  * `laravel/reverb`: Real-time chat WebSocket (Echo.js).
* **3 Role Resmi Platform**:
  1. `admin` (dengan `super_admin` sebagai root administrator yang protected).
  2. `photographer` (Dashboard Studio mitra).
  3. `client` (Antarmuka publik universal marketplace).

---

## 2. Status Lingkungan Git & Branching
* **Branch Resmi Aktif**: **`development`** (satu branch bersama untuk seluruh komputer pengerjaan).
* **Repository Remote**: `https://github.com/PutuAnggaKusuma/marketplace-photographers.git`
* **Aturan Git Wajib**:
  * **DILARANG KERAS menjalankan `git push` otomatis** tanpa konfirmasi/perintah langsung dari user.
  * Sebelum mulai bekerja di komputer mana pun, selalu jalankan: `git pull origin development`.

---

## 3. Dokumen Acuan Wajib
1. [`README.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/README.md): Sumber kebenaran skema database, arsitektur modul, dan standar UI wajib (Rule #1 s/d Rule #93).
2. [`TRACKING_PERUBAHAN_FILE.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/TRACKING_PERUBAHAN_FILE.md): Log audit trail perubahan. **Setiap ada file yang diubah/dibuat, WAJIB dicatat di file ini**.

---

## 4. Progres Penting yang Telah Selesai
1. **Konsolidasi Database (49 Migrasi Murni)**:
   * 59 migrasi lama telah dirapikan menjadi 49 migrasi murni `Schema::create` dalam urutan topologis aman foreign key.
   * Seeder wilayah Indonesia otomatis terpanggil di [`DatabaseSeeder.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/database/seeders/DatabaseSeeder.php). Status `php artisan migrate:fresh --seed` 100% lulus.
2. **Pembersihan Halaman Detail Fotografer ([`resources/views/public/photographers/show.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/public/photographers/show.blade.php))**:
   * **Fix Tab Blank**: Membungkus seksi profil dan tab dalam satu container terpadu `<div x-data="...">`. Tab Paket Layanan, Portofolio, Kalender, Ulasan, dan FAQ berjalan normal.
   * **Fix Outline Kotak Hitam**: Menambahkan `focus:outline-none focus:ring-0` pada seluruh tombol tab dan akordeon FAQ.
   * **Eliminasi AI-Slop Visual**:
     * Menghapus seluruh ornamen SVG wave bergelombang warna-warni (`wave-grad`) di latar belakang atas; diganti latar netral bersih `bg-gray-50/70 dark:bg-gray-900`.
     * Menghapus navigasi breadcrumb bertingkat dan judul generik redundan `<h1>Detail Profil Fotografer</h1>`.
     * Mengganti tombol kembali panah bertangkai `←` dengan modern single chevron `<` (Rule #72).
     * Memperbaiki spasi kartu profil (`space-y-4` natural flow) untuk menghilangkan celah kosong putih menganga di antara bio dan tarif harga.
     * Menghapus garis putus-putus pembatas (*dashed divider alay*).
     * Memperbaiki penutupan tag HTML kontainer `max-w-7xl mx-auto px-4 sm:px-6 lg:px-8` yang sebelumnya tertutup prematur, sehingga konten tidak lagi melebar *edge-to-edge* tanpa margin kiri-kanan.
     * Mengurung kartu *"Standar Jam Kerja Operasional Studio"* secara ketat di dalam seksi Tab 3 (Kalender Ketersediaan) agar tidak bocor ke bawah Paket Layanan Sesi Foto.
   * **Kepatuhan Standar UI**:
     * Menghapus awalan `#` pada ID mitra (`FTR-00001`).
     * Menghapus badge overlay di atas foto potret (Rule #66 Point 2).
     * Menghapus teks kapital redundan *"MITRA FOTOGRAFER PROFESIONAL"*.
     * Menyesuaikan tarif harga dalam grid 2-kolom seimbang & menghapus jargon escrow (Rule #66.3 & Rule #89.2).
     * Mengubah tombol CTA menjadi berbasis konten (`w-auto`), bukan melintang 1200px (Rule #66.4).
     * Menjadikan 4 kotak statistik 100% data dinamis riil (Rule #66.5 & Rule #15).
     * Kalender ketersediaan seragam (*Uniform Card Matrix*): seluruh sel memiliki kontainer kartu ber-border konsisten, tanggal lampau diabukan (*grayed-out*), bebas dari bulatan status / *dot badges* (*pure colored text* sesuai Rule #67). Tombol/badge `[ Hari Ini ]` di header bulan dieliminasi total, indikator hari ini ditempatkan berupa teks bersih `(Hari Ini)` tepat di samping angka nomor tanggal dengan border kartu aksen amber halus (Rule #67 Point 5). Seluruh garis divider internal dieliminasi total untuk kemurnian Anti-AI Slop.
     * **Tab Kalender Ketersediaan Murni Informasional Preview (Rule #67 Point 6)**: Seluruh tombol booking di dalam tab kalender (seperti *"Booking Langsung"* dan *"Pilih Tanggal Ini & Booking"*) DIELIMINASI TOTAL. Tab kalender murni sebagai preview ketersediaan (status *Tersedia*, *Terisi Sesi*, *Libur*). Pemilihan tanggal untuk reservasi foto hanya dilakukan di formulir pemesanan (`/booking/create`) setelah klien menekan tombol utama *"Reservasi Sesi Foto Sekarang"*. Data ketersediaan bulan berjalan disuplai jadwal riil.
     * Navigasi tab teks horizontal dengan underline aktif (Rule #78).
     * Tata letak album portofolio Rule #75: Foto utama paling besar di KIRI (`md:col-span-8`), dan 3 thumbnail kecil bertumpuk VERTIKAL ke bawah di KANAN (`md:col-span-4 md:flex-col`) tepat sejajar tinggi foto kiri. Card memiliki padding lapang (`p-6 sm:p-8 lg:p-9 rounded-3xl space-y-6`).
     * Tanya Jawab (FAQ) Rule #86: Menggunakan FAQ sistem universal yang merangkum pertanyaan umum pencari jasa (booking, pembayaran, konfirmasi, chat, reschedule, penyerahan hasil foto, cuaca), seragam dan identik untuk seluruh fotografer (bebas *empty state*).
     * **Unified Master Profile Card (Rule #66)**: Halaman profil kini dibungkus dalam **Satu Kartu Induk Tunggal** (`rounded-3xl p-6 sm:p-8 lg:p-10 border border-gray-200/80 shadow-xs bg-white dark:bg-gray-800 space-y-8 sm:space-y-10`) yang membentang penuh ke bawah mencakup Header Profil (Foto Potret 320px + Detail Info + 3 Baris Statistik + Tombol `Reservasi` & `Chat`), Baris Navigasi Tab Teks Horizontal (Rule #78), dan Seluruh Konten Tab Terpilih. Seluruh kartu anak di dalam tab (paket layanan, portofolio, kalender, ulasan, FAQ) menggunakan latar bersahaja `bg-gray-50/70 dark:bg-gray-750/70 rounded-2xl border` yang menyatu bersih dan elegan tanpa garis divider horizontal pemotong (*zero dividers*). Foto potret kiri dan tombol aksi kanan sejajar horizontal sempurna di bagian bawah header profil.
     * **Tab Paket Layanan Murni Informasional (Rule #66 Point 7)**: Kartu paket pemotretan pada Tab 1 murni menampilkan informasi spesifikasi paket dan rincian fasilitas & benefit. Dilarang meletakkan tombol booking seperti *"Pilih Paket & Booking"* di dalam kartu; pemilihan paket dilakukan pada alur reservasi setelah klien menekan tombol utama *"Reservasi Sesi Foto Sekarang"*.
     * **Konsistensi Judul Seksi Konten Tab (Rule #78 Point 5 — Anti-AI Slop)**: Seluruh 5 judul seksi konten (`<h2>`) diselaraskan 100% identik (2 kata) dengan tombol tab di atasnya (*Paket Layanan*, *Galeri Portofolio*, *Kalender Ketersediaan*, *Ulasan Klien*, *Tanya Jawab*), mengeliminasi judul bertele-tele *"Pilihan Paket Layanan Sesi Foto"*.
3. **Fitur Wishlist / Favorit Fotografer dengan Live Feedback Toast (Rule #93)**:
   * **Wajib Toast Notification Mengambang (Anti-AI Slop)**: Seluruh aksi favorit (simpan maupun hapus) di `/client/favorites` dan katalog fotografer menggunakan Universal Toast Notification otomatis (*auto-dismiss* dengan progress bar menyusut).
   * **Eliminasi Banner Alert Statis Permanen**: Menghapus seluruh kotak banner alert hijau/amber permanen (`@if(session('success')) ... @endif`) pada halaman `/client/favorites` dan `/client/galleries` yang sebelumnya menempel secara permanen di layar setelah penghapusan item.
   * **Interaktivitas Asinkron Asli (Alpine.js + AJAX Fetch)**: Tombol hapus favorit pada kartu di `/client/favorites` menghapus data secara asinkron dengan animasi fade-out (`x-transition:leave`) dan memicu event `show-toast`. Ketika item terakhir dihapus, halaman melakukan refresh bersih untuk memunculkan empty state tanpa meninggalkan pesan banner statis apa pun.
   * **Zero Dividers & Zero Header Badges**: Menghapus garis divider pembatas (`border-t`) pada seksi tombol kartu favorit serta mengeliminasi badge pill kapsul `[ 1 Disimpan ]` pada judul utama header `<h1>Fotografer Favorit Saya</h1>`.
   * **Standarisasi Kartu Baris Horizontal Halaman Favorit (Opsi A - Upwork / HoneyBook Style - Rule #93 Point 6)**: Mengubah tata letak kartu favorit klien dari grid 3-kolom menjadi kartu baris horizontal memanjang (`space-y-4 sm:space-y-5`, `flex flex-col md:flex-row items-start md:items-center justify-between gap-6 p-5 sm:p-6 rounded-3xl border border-gray-200/80 dark:border-gray-700/80 shadow-xs hover:shadow-md`) agar terlihat solid, gagah, dan proporsional tanpa ruang kosong janggal saat klien hanya memiliki 1 item tersimpan. Terbagi atas 3 komponen:
     - **Sisi Kiri**: Cover portofolio landscape kompak (`w-full md:w-60 h-48 md:h-44 rounded-2xl overflow-hidden shrink-0 relative`) dilengkapi tombol un-save bookmark bulat di sudut kiri atas (`w-8 h-8 rounded-full bg-white/95 dark:bg-gray-900/95`). Bebas dari badge rating di atas foto (Rule #66 & #93).
     - **Sisi Tengah**: Lokasi kota (ikon pin), baris avatar mitra + tautan nama studio, kategori spesialisasi tipografi murni amber (*plain text comma-separated* tanpa badge pill), metrik ulasan rating bintang (`★ 5.0`) & sesi selesai inline teks, serta cuplikan bio studio (max 2 baris `line-clamp-2`).
     - **Sisi Kanan**: Rincian tarif ("Mulai Dari Rp ... / sesi") dan 2 tombol aksi to the point: tombol utama `"Reservasi"` (`bg-amber-400 font-extrabold text-gray-950`), tombol sekunder `"Chat"` (`bg-gray-100 dark:bg-gray-700 font-bold`), dan tautan navigasi halus `"Lihat Profil Lengkap >"`. Seluruh kartu bebas dari garis pemisah internal (*zero dividers*) dan bebas tanda `&` redundan.
   * **Universal Pagination Fix (Rule #94)**: Menyelaraskan `$perPage` di `ClientFavoriteController` ke standar `10, 20, 50, 100` serta menambahkan proteksi dinamis pada `partials.public.pagination` dan `partials.dashboard.pagination` sehingga nilai per-page selalu sinkron dengan opsi dropdown.
4. **Restrukturisasi Alur Formulir Reservasi Sesi Foto (Rule #95 — Anti-AI Slop & Zero-Reload Alpine.js Calculation)**:
   * **Eliminasi Bug Pemaksaan Paket Otomatis**: Memperbaiki `BookingController@create` agar tidak memaksa memilih paket pertama secara sepihak jika klien datang langsung menekan tombol `"Reservasi"`. `$service` bernilai `null` secara default saat halaman dimuat tanpa query parameter `service_id`.
   * **Urutan 4 Seksi Formulir Kiri yang Logis & Alami**:
     - **Seksi 1 (Paling Atas)**: **Pilih Paket Layanan**. Kartu interaktif radio untuk semua paket fotografer (Nama Paket, Tarif, Deskripsi, dan benefit).
     - **Seksi 2**: **Jadwal Pelaksanaan Sesi Foto**. Tanggal dan waktu mulai sesi (dengan alert real-time jika tanggal libur / terisi).
     - **Seksi 3**: **Lokasi Pemotretan**. Wilayah administrasi dinamis (Provinsi, Kota/Kabupaten via AJAX) dan alamat lengkap venue.
     - **Seksi 4**: **Catatan Khusus**. Catatan tema, busana, atau preferensi klien.
   * **Reaktivitas Kolom Kanan (Ringkasan Pesanan)**:
     - Sebelum paket dipilih: Menampilkan instruksi ramah, total biaya Rp 0, dan tombol submit dinonaktifkan (`disabled`).
     - Setelah paket dipilih: Rincian paket terisi, opsi fitur tambahan (*add-ons*) otomatis muncul sesuai paket, dan total biaya terhitung secara real-time via Alpine.js tanpa reload.
   * **Kepatuhan Anti-AI Slop**: Menghapus breadcrumb navigasi palsu (Rule #72), zero dividers (`0 border-t / border-b` memotong kartu sesuai Rule #67), eliminasi `&` redundan pada copywriting judul (Rule #70), dan tombol brand amber LensMatch (`bg-amber-400 font-extrabold` sesuai Rule #91).
5. **Mini Kalender Interaktif & Time-Slot Availability Matrix (Rule #96 — Multi-Booking & Sesi Estafet)**:
   * **Mini Kalender Bulan Interaktif**: Klien memilih tanggal sesi pemotretan pada kalender mini visual di dalam Seksi 2 formulir booking. Tanggal libur penuh dan tanggal lampau otomatis diabukan.
   * **Time-Slot Matrix Chips**: Tepat di bawah kalender mini, kandidat jam mulai sesi disajikan dalam bentuk kartu chip grid yang tenang (08:00, 10:00, 12:00, 14:00, 16:00, 18:00, 20:00).
   * **Multi-Booking & Jeda Istirahat**: Menggunakan algoritma interval overlap (`slotStart < otherEnd && slotEnd > otherStart`). Jika Klien A memesan jam 09:00 - 11:00 dan fotografer istirahat jam 12:00 - 14:00, slot 14:00 tetap berwarna hijau/tersedia dan dapat dipesan Klien B tanpa reload halaman.
   * **Kepatuhan Anti-AI Slop**: Bebas dari warna merah menyala yang agresif pada slot tidak tersedia; digantikan abu-abu redup tenang (*soft grayed-out*), nol garis divider pembatas (*zero dividers*), dan tanpa simbol `&` redundan.

---

6. **Standarisasi Master Card Profil Fotografer & Harmonitas Warna Kartu Dalam (Rule #97)**:
   * **Margin & Breathing Room yang Lega**: Master card profil memiliki padding lega (\p-8 sm:p-10 lg:p-12 space-y-10 sm:space-y-12\), grid info studio (\gap-y-5 gap-x-8 sm:gap-x-10\), dan jeda antar seksi lapang sehingga layout berwibawa dan tidak berdesakan.
   * **Harmonitas Warna Kartu Dalam (Anti-Gray Tint)**: Menghilangkan seluruh warna abu-abu (\g-gray-50/70\, \g-gray-750/70\) pada kartu anak di dalam tab. Seluruh kartu anak (paket layanan, album portofolio, kalender ketersediaan, ulasan klien, FAQ) kini memiliki warna latar belakang yang **PERSIS SAMA DENGAN KARTU LUAR**: putih bersih \g-white dark:bg-gray-800\ dengan border halus \order border-gray-200 dark:border-gray-700\.
   * **Zero Shadow pada Kartu Dalam**: Menghapus seluruh shadow (\shadow-none\) pada kartu anak di dalam tab untuk menciptakan estetika datar modern yang rapi.
   * **Peningkatan Skala Tipografi Proporsional**: Nama fotografer (\	ext-3xl sm:text-4xl font-black\), bio & spesialisasi (\	ext-sm sm:text-base\), 6 metrik studio (\	ext-base sm:text-lg lg:text-xl font-black\), tombol aksi (\px-8 py-3.5\), judul tab \<h2>\ (\	ext-2xl sm:text-3xl font-black\), serta rincian paket, galeri, kalender, dan ulasan diperbesar secara proporsional.
   * **Anti-AI Slop**: Bebas simbol \&\ redundan (misal 'Rating Ulasan', 'Fasilitas Layanan') dan 0 garis pembatas horizontal pemotong kartu.

---

7. **Seeder Galeri Portofolio Lengkap (3 Album Riil Budi Event Captures)**:
   * Menambahkan album ke-2 (*Nusantara Tech Summit dan Gala Dinner 2025* - 4 foto) dan album ke-3 (*Soundwave Indie Showcase Senayan* - 5 foto) pada seeder [`PhotographerSeeder.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/database/seeders/PhotographerSeeder.php).
   * Total portofolio fotografer ID 2 kini berjumlah 3 album karya utuh sesuai standar Rule #75 (Foto utama kiri + 3 thumbnail vertikal kanan, dengan overlay `+1 Foto Lainnya` pada album ke-3).
   * Metrik *"Portofolio: 3 Karya"* dan counter badge pada tab Galeri Portofolio otomatis terupdate secara dinamis.

---

8. **Standarisasi Watermark Hak Cipta Murni Tipografi (Rule #98 Anti-AI Slop)**:
   * **Eliminasi Total Kotak Badge**: Menghapus seluruh logika penggambaran kotak/badge (`imagefilledrectangle` dan `imagerectangle`) pada [`WatermarkService.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/app/Services/WatermarkService.php). Watermark murni berupa teks tipografi semi-transparan dengan drop shadow halus 1px.
   * **Overlay Tipografi Murni di Blade**: Menambahkan teks watermark hak cipta `© LensMatch • {{ $photographer->nama }}` pada foto galeri portofolio dan modal Lightbox di [`show.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/public/photographers/show.blade.php) tanpa wadah kotak (*zero box, zero badge*), disertai proteksi anti-drag dan disable klik kanan.

---

9. **Standarisasi Kartu Paket Layanan Full-Width (Rule #99 Anti-Narrow Void)**:
   * **Eliminasi Grid Sempit 3-Kolom**: Mengubah tata letak kartu paket layanan di Tab 1 [`show.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/public/photographers/show.blade.php) dari grid 3-kolom sempit menjadi **Full-Width Stacked Cards** (`w-full bg-white dark:bg-gray-800 p-8 sm:p-10 lg:p-12 rounded-3xl border border-gray-200 dark:border-gray-700 shadow-none`).
   * **Struktur Internal 2-Kolom (5:7)**: Kolom kiri (`lg:col-span-5`) menampilkan identitas paket, deskripsi lengkap, dan tarif sesi besar; kolom kanan (`lg:col-span-7`) menyajikan daftar fasilitas dalam grid 2-kolom (`sm:grid-cols-2 gap-4 sm:gap-5`) yang sangat lapang, berwibawa, dan seragam dengan kartu album di Tab Galeri Portofolio.

---

10. **Standarisasi Header Editorial Profil Kreator (Rule #100 Anti-AI Slop)**:
   * **Eliminasi Formulir 6 Spek Database**: Mengganti tabel 3x2 kaku pada header profil [`show.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/public/photographers/show.blade.php) dengan **Inline Trust Signals Bar** (`📍 Lokasi • ★ Rating • Pengalaman • Portofolio`).
   * **Hierarki Typografi & Commercial Anchor**: Nama fotografer tampil dominan, bio mengalir santai, tarif awal sesi diposisikan bersama tombol aksi utama (`Reservasi`, `Chat`, `Bookmark`), dan foto potret menggunakan rasio natural 4:5 (`aspect-[4/5] rounded-3xl`).

---

11. **Eksperimen Harmonitas Tipografi Mikro & Skala Proporsional (Selaras dengan Form Reservasi `/booking/create` & Navbar)**:
   * **Latar Belakang Eksperimen**: Pengguna membandingkan tampilan form reservasi (`/booking/create`) dengan profil fotografer (`/fotografer/{id}`), mengamati bahwa form reservasi terlihat jauh lebih rapi, berkelas, tenang, dan *anti-AI slop* karena proporsi tipografi mikro yang harmonis dengan ukuran teks pada navbar (`text-xs` / `text-sm font-bold`).
   * **Titik Pengaman Revert (Rollback Guarantee)**:
     - File Backup: `scratch/show.blade.php.pre_micro_typography_backup` (Identik dengan kondisi sebelum eksperimen, 957 baris).
     - Commit Hash Terakhir: `51109d162593a747ffca7e059ae57b20c361cc7d`.
     - Perintah Revert Instan: `Copy-Item 'scratch\show.blade.php.pre_micro_typography_backup' 'resources\views\public\photographers\show.blade.php' -Force; npm run build`.
   * **Penyelarasan Skala & Proporsi Tipografi**:
     - **Padding Master Card**: Disesuaikan dari `p-8 sm:p-10 lg:p-12` menjadi `p-6 sm:p-8 lg:p-9 rounded-2xl sm:rounded-3xl` yang seragam dengan card reservasi.
     - **Foto Potret**: Dioptimalkan dari `w-[320px]` raksasa menjadi `w-full sm:w-[200px] md:w-[220px] lg:w-[240px] aspect-[4/5] rounded-2xl`, menghilangkan rongga kosong raksasa di kanan header.
     - **Tipografi Mikro Identitas**: Nama fotografer disesuaikan menjadi `text-xl sm:text-2xl lg:text-3xl font-black` (bukan `text-5xl`), baris sinyal kepercayaan dan bio berukuran `text-xs sm:text-[13px]`, harga awal `text-xl sm:text-2xl font-black`, dan tombol aksi `px-6 py-2.5 rounded-xl`.
     - **Tab & Judul Seksi**: Judul seksi `<h2>` ditenangkan menjadi `text-base sm:text-lg font-black` (bukan `text-3xl`), tab navigasi `text-xs sm:text-sm font-bold`.
     - **Seluruh Tab Konten (Paket Layanan, Portofolio, Kalender, Ulasan, FAQ)**: Mengadopsi skala tipografi mikro yang padat, rapi, dan mature tanpa memotong informasi, dengan tetap mempertahankan latar putih murni (`bg-white dark:bg-gray-800`), *zero internal shadow*, dan watermark murni tipografi tanpa badge.
   * **Eliminasi Formulir 6 Spek Database**: Mengganti tabel 3x2 kaku pada header profil [`show.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/public/photographers/show.blade.php) dengan **Inline Trust Signals Bar** (`📍 Lokasi • ★ Rating • Pengalaman • Portofolio`).
   * **Hierarki Typografi & Commercial Anchor**: Nama fotografer tampil dominan (`text-4xl lg:text-5xl font-black`), bio mengalir santai, tarif awal sesi diposisikan bersama tombol aksi utama (`Reservasi`, `Chat`, `Bookmark`), dan foto potret menggunakan rasio natural 4:5 (`aspect-[4/5] rounded-3xl`).

---

12. **Sinkronisasi Multi-Komputer & Penggabungan Cabang `development-2`**:
   * **Cabang Aktif**: `development-2` (Commit: `76ad04a`).
   * **Sumber Penggabungan**:
     - **Komputer A (Lokal)**: Commit `5327ddc` (Tipografi mikro profil fotografer, penyelarasan skala halaman reservasi, seeder 3 album portofolio riil, watermark hak cipta murni tipografi tanpa badge, layout horizontal kartu favorit dengan live toast un-save).
     - **Komputer B (Remote `origin/development`)**: Commit `4cc64b3` (Fitur alur verifikasi fotografer oleh Admin, migrasi database `status_verifikasi` dan `catatan_penolakan`, middleware `EnsurePhotographerVerified`, detail audit `/admin/photographers/{id}`, dan penanganan representasi media kosong).
   * **Status Migrasi Database**: `2026_11_07_032601_add_status_verifikasi_and_rejection_notes_to_role_photographers_table` telah dieksekusi dengan sukses.
   * **Status Dependensi Wilayah**: 38 Provinsi dan 514 Kota telah terverifikasi aktif di database lokal.
   * **Status Aset**: `npm run build` sukses dikompilasi (3.97s), cache tampilan (`php artisan view:clear`) telah dibersihkan.

13. **Universal Pop-up Login Diperlukan pada Tombol Aksi Tamu (Rule #6 & #59)**:
   * **Pencegahan Forced Redirect**: Mengeliminasi redirect seketika tanpa peringatan saat tamu (guest / unauthenticated) mengklik tombol **Reservasi**, **Chat**, dan **Simpan** (Bookmark) pada profil fotografer ([`resources/views/public/photographers/show.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/public/photographers/show.blade.php)) maupun katalog ([`resources/views/public/photographers/katalog.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/public/photographers/katalog.blade.php)).
   * **Modal Interaktif Standar**: Menampilkan Universal Modal "Login Diperlukan" berlatar blur (`bg-gray-900/60 backdrop-blur-sm`), header terpusat tanpa ikon, tombol silang SVG murni di kanan atas, pesan kontekstual sesuai aksi yang diklik, dan tombol brand LensMatch (`bg-amber-400 font-extrabold`) yang mengarahkan ke formulir login dengan parameter `?redirect=...` ke target yang dituju.
   * **Pengguna Terotentikasi**: Untuk pengguna yang telah login (`@auth`), ketiga tombol tetap berfungsi langsung secara normal tanpa interupsi modal.

---

## 5. Pedoman & Pantangan Mutlak untuk Pengembang / AI
* ❌ **Larangan Mutlak Mengistimewakan Kode / ID (Rule #89 Point 1)**: Dilarang keras menampilkan kode sistem atau ID internal database (seperti `FTR-00001`) di area navigasi atas, header, atau kartu publik. Pengunjung/klien tidak berkepentingan melihat ID database fotografer. Kode atau nomor kontrak/invoice hanyalah metadata sekunder yang diletakkan pada tabel atau dokumen cetak resmi secara netral dan tenang.
* ❌ **Dilarang keras memakai tanda pagar `#`** untuk urutan/nomor ID data apa pun (Gunakan `KTRK-00001`, bukan `#KTRK-00001`; gunakan `1`, bukan `#1`).
* ❌ **Dilarang keras menggunakan data pasif/dummy hardcoded text di HTML (Rule #15)**. Semua data harus dihitung atau diambil dari relasi Eloquent database.
* ❌ **Dilarang membuat tampilan "AI Slop"**: Dilarang pakai emoji di UI (wajib SVG asli), dilarang teks harga kuning raksasa, dilarang memberi bulatan status (*dot badge* `rounded-full bg-*`), dilarang membuat stempel/badge palsu seperti "VERIFIED INVOICE MD5", dan dilarang tombol melintang penuh 1200px tanpa alasan.
* ⚠️ **Standar Tombol Formulir (Rule #76, #91)**: Menggunakan warna kuning amber identitas brand LensMatch (`bg-amber-400 font-extrabold`). Label teks: `Simpan Data` atau `Perbaharui Data`. Warna hijau emerald khusus untuk approval transaksi/finansial.
* ⚠️ **Standar Tombol Tabel AKSI (Rule #10, #80)**: Teks murni tanpa icon: `Lihat`, `Edit` (amber lembut), `Hapus` (rose lembut).

---

## 6. Status Modul Saat Ini & Rencana Selanjutnya
* **Modul Booking**: Form reservasi (`/booking/create`) dan invoice pembayaran (`/pembayaran/{id}`) saat ini berstatus **HOLD**.
* **Fokus Selanjutnya**: Integrasi alur persetujuan kontrak 2-pihak, integrasi payment gateway Midtrans Sandbox, dan review dua arah.

