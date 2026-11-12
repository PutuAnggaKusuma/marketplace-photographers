# Platform Booking Fotografer — Project Context

Dokumen ini adalah ringkasan konteks proyek untuk skripsi: platform marketplace booking fotografer dengan fitur komunitas (forum, lomba foto, e-learning). Database final sudah di-running (skema awal + revisi + tabel tambahan). Dokumen ini jadi acuan utama sebelum mulai coding.

---

## 1. Arsitektur Aplikasi

Aplikasi menggunakan **role-based UI**, bukan satu tampilan generik:

- **Admin** → dashboard terpisah (layout manajerial/backoffice), wajib login.
- **Fotografer** → dashboard terpisah (layout manajerial untuk kelola layanan, portofolio, booking masuk), wajib login.
- **Client** → tampilan **universal/publik** (bukan dashboard backoffice). Client bisa browse fotografer, lihat portofolio, forum, lomba foto, dan e-learning tanpa perlu masuk ke area dashboard tertutup — layoutnya lebih seperti website marketplace pada umumnya (mirip e-commerce), bukan panel admin. Login tetap diperlukan untuk aksi transaksional (booking, chat, bayar, kasih ulasan), tapi bukan berupa "dashboard" bergaya backoffice.

Jadi ada **2 dashboard** (admin, fotografer) dan **1 tampilan universal** (client + guest/publik).

---

## 2. Tech Stack

| Kebutuhan | Teknologi |
|---|---|
| Backend Framework | Laravel |
| Database | MySQL (`db_marketplace_freelancers`) |
| Log Aktivitas | Spatie Activity Log |
| Payment Gateway | Midtrans (Sandbox Mode) — mendukung transfer manual & payment gateway otomatis |
| Notifikasi | Laravel Notifications (bawaan) |
| Chat Real-time | Laravel Reverb (WebSocket) |
| Verifikasi & Kirim Email | Brevo |
| Reset Password | Laravel default (`password_reset_tokens`) |

Seluruh stack dipilih karena gratis untuk skala skripsi (sandbox/self-hosted, tanpa biaya).

---

## 3. Skema Database Final

Database sudah final (skema awal + seluruh revisi + tabel tambahan sudah dijalankan). Berikut daftar lengkap tabel dikelompokkan per modul.

### 3.1 User & Role
- **users** — `id`, `nama`, `email` (UNIQUE), `email_verified_at`, `password`, `role` enum(`super_admin`,`admin`,`client`,`photographer`), `is_protected` (boolean, default false), timestamps + `deleted_at`
  - **Ketentuan Role Super Admin**: Akun `super_admin` bertindak sebagai Root Administrator yang berhak membuat/mengelola akun `admin`. Akun `super_admin` memiliki status **Protected** (`is_protected = true`) sehingga **TIDAK BISA DIHAPUS** oleh siapapun di dalam sistem.
  - **Aturan Absolut 1 Email 1 Akun**: Kolom `email` pada tabel `users` bersifat `UNIQUE` secara mutlak di seluruh sistem. 1 alamat email hanya boleh digunakan oleh 1 akun & 1 role saja (jika email A sudah terdaftar sebagai Client, email tersebut tidak dapat digunakan untuk mendaftar sebagai Fotografer atau Admin).
- **role_admins** — `id`, `id_user` (FK→users), `nama`, `no_telepon`, `alamat`, `foto`, timestamps + `deleted_at`
- **role_clients** — `id`, `id_user` (FK→users), `nama`, `nomor_telepon`, `alamat`, `foto`, timestamps + `deleted_at`
- **role_photographers** — `id`, `id_user` (FK→users), `nama`, `nomor_telepon`, `link_sosmed`, `foto`, `alamat`, `deskripsi_bio`, timestamps + `deleted_at`
  - *(field `spesialisasi` sudah dihapus, digantikan relasi many-to-many lewat `categories`)*

### 3.2 Profil Fotografer (Kategori, Portofolio, Layanan, Availability)
- **categories** — `id`, `nama_kategori` (unique), timestamps + `deleted_at`
- **photographer_categories** (pivot) — `id`, `id_photographer` (FK), `id_category` (FK), timestamps + `deleted_at`
- **photographer_portofolios** — `id`, `id_photographer` (FK), `judul`, `deskripsi`, timestamps + `deleted_at`
- **portofolio_medias** — `id`, `id_portofolio` (FK), `media`, timestamps + `deleted_at`
- **photographer_services** — `id`, `id_photographer` (FK), `nama_layanan`, `tarif_harga`, `deskripsi_layanan`, timestamps + `deleted_at`
- **service_details** — `id`, `id_p_service` (FK→photographer_services), `nama fitur`, `tarif_harga`, timestamps + `deleted_at`
- **photographer_availability** — `id`, `id_photographer` (FK), `tanggal`, `jam_mulai`, `jam_selesai` (nullable, untuk blokir sebagian jam), `status` enum(`blocked`), `keterangan`, timestamps + `deleted_at`
  - Fungsi murni untuk **blokir manual** oleh fotografer, bukan mencatat booking.

### 3.3 Booking, Kontrak & Pembayaran
- **contracts** — `id`, `id_client` (FK), `id_photographer` (FK), `jumlah`, `payment_type` enum(`full`,`dp`), `jumlah_dp`, `is_validated_photographer`, `is_validated_client`, `expired_at`, `status_contract` enum(`draft`,`approved`,`pending_payment`,`active`,`completed`,`cancelled`), timestamps + `deleted_at`
  - *(field `is_multi_booking` sudah dihapus — 1 kontrak = 1 fotografer)*
- **contract_bookings** — `id`, `id_contract` (FK), `id_service` (FK→photographer_services), `booking_date`, `jam_mulai`, `jam_selesai`, `lokasi`, `durasi`, `status_booking` enum(`pending`,`confirmed`,`completed`), timestamps + `deleted_at`
- **contract_cancellations** — `id`, `id_contract` (FK), `alasan`, `dibatalkan_oleh` enum(`client`,`photographer`,`admin`), `refund_amount`, timestamps
- **payments** — `id`, `id_contract` (FK), `payment_amount`, `payment_status` enum(`pending`,`completed`,`failed`), `contract_payment_type` enum(`dp`,`full`), `payment_link`, `bukti_transfer`, `payment_type` enum(`payment gateway`,`transfer`), `external_id`, timestamps + `deleted_at`
- **payment_logs** — `id`, `id_payment` (FK), `provider` enum(`midtrans`,`xendit`,`manual`), `event_type`, `raw_response` (json), `created_at`

### 3.4 Chat & Testimonial
- **chat_bookings** — `id`, `id_photographer` (FK), `id_client` (FK), timestamps + `deleted_at`
  - Berfungsi sebagai tabel **conversation/thread** saja (pesan sudah dipindah ke `chat_messages`)
- **chat_messages** — `id`, `id_chat_booking` (FK), `id_sender` (FK→users), `message`, `is_read`, timestamps + `deleted_at`
- **testimonials** — `id`, `id_client` (FK), `id_photographer` (FK), `id_contract` (FK), `deskripsi_review`, `rating`, timestamps + `deleted_at`

### 3.5 Forum Komunitas
- **forum_posts** — `id`, `id_user` (FK), `judul`, `deskripsi`, timestamps + `deleted_at`
- **forum_comments** — `id`, `id_forum_post` (FK), `id_user` (FK), `comment`, timestamps + `deleted_at`
- **forum_post_medias** — `id`, `id_forum_post` (FK), `foto`, timestamps + `deleted_at`

### 3.6 Lomba Foto
- **photo_contests** — `id`, `id_admin` (FK), `judul_lomba`, `deskrpisi_lomba`, `start_date`, `end_date`, `hadiah`, `penyelenggara`, timestamps + `deleted_at`
- **contest_medias** — `id`, `id_photo_contest` (FK), `foto`, timestamps + `deleted_at` (banner/media dari admin)
- **contest_participants** — `id`, `id_photo_contest` (FK), `id_user` (FK), `media`, `deskripsi`, `status` enum(`pending`,`lolos`,`ditolak`), timestamps + `deleted_at` (karya submission peserta)

### 3.7 E-Learning
- **e_learning_materials** — `id`, `id_admin` (FK), `judul`, `deskripsi`, timestamps + `deleted_at`
- **e_learning_medias** — `id`, `id_e_learning` (FK), `foto`, `video`, timestamps + `deleted_at`
- **e_learning_progress** — `id`, `id_user` (FK), `id_e_learning` (FK), `status` enum(`belum_mulai`,`sedang_belajar`,`selesai`), `progress_percentage`, `completed_at`, timestamps + `deleted_at`
  - Progress dihitung per materi (bukan per media), karena materi bisa kombinasi teks/gambar/video.

### 3.8 Sistem Pendukung
- **notifications** — `id`, `id_user` (FK), `type`, `title`, `message`, `data` (json), `is_read`, timestamps
- **activity_logs** — `id`, `id_user` (FK), `log_name`, `description`, `subject_type`, `subject_id`, `properties` (json), timestamps
- **password_reset_tokens** — `email` (PK), `token`, `created_at`

### 3.9 Data Wilayah Indonesia (Package `laravolt/indonesia`)
- Seluruh data Wilayah Administratif Indonesia (Provinsi, Kabupaten/Kota, Kecamatan, Kelurahan/Desa) di-seed dan dikelola menggunakan package **`laravolt/indonesia`**.
- **Tabel Database**:
  - `indonesia_provinces` — 38 Provinsi di Indonesia
  - `indonesia_cities` — 514 Kabupaten / Kota di Indonesia
  - `indonesia_districts` — ~7.200+ Kecamatan di Indonesia
  - `indonesia_villages` — ~83.000+ Kelurahan / Desa di Indonesia
- **Seeder Command**: `php artisan laravolt:indonesia:seed`
- **Eloquent Models**: `Laravolt\Indonesia\Models\Province`, `City`, `District`, `Village`


---

## 4. Daftar Modul & Fitur

1. **Autentikasi & Profil Pengguna** — register, login, verifikasi email, lupa password, kelola profil.
2. **Profil & Pencarian Fotografer** — cari/filter fotografer (kategori, lokasi, harga), lihat portofolio & paket layanan; fotografer kelola profil, kategori, portofolio, layanan.
3. **Chat** — real-time antara client dan fotografer.
4. **Jadwal & Ketersediaan** — fotografer blokir tanggal/jam; client lihat kalender sebelum booking.
5. **Booking & Kontrak** — buat booking, validasi dua pihak, pembatalan booking.
6. **Pembayaran** — DP & pelunasan, transfer manual atau payment gateway (Midtrans).
7. **Ulasan & Rating** — client beri ulasan ke fotografer setelah acara selesai.
8. **Forum Komunitas** — posting & komentar antar pengguna.
9. **Lomba Foto** — admin buat lomba, user submit karya sebagai peserta.
10. **E-Learning** — admin upload materi (teks/gambar/video), user belajar dengan progress tracking.
11. **Notifikasi** — otomatis untuk event penting (booking baru, pembayaran, validasi kontrak, dll).
12. **Manajemen Admin** — verifikasi fotografer, kelola kategori, monitoring transaksi & log, moderasi forum.

---

## 5. Langkah Setup — Backend (Laravel)

1. Install PHP, Composer, MySQL, Node.js/NPM. Siapkan Git untuk version control.
2. `composer create-project laravel/laravel` untuk project baru. Konfigurasi `.env` untuk koneksi ke database `db_marketplace_freelancers`.
3. Convert skema database final (lihat Bagian 3 di atas) menjadi Laravel migration files per tabel (bukan import raw SQL langsung), supaya versioning-nya rapi.
4. Buat Eloquent Model untuk setiap tabel, definisikan relasi (`belongsTo`, `hasMany`, `belongsToMany` untuk pivot seperti `photographer_categories`).
5. Install & konfigurasi Laravel Breeze/Jetstream sebagai starting point autentikasi, lalu kustomisasi untuk 3 role (admin, client, photographer) dengan middleware role-based.
6. Install package pendukung: `spatie/laravel-activitylog`, Midtrans PHP SDK, Laravel Reverb, driver mail untuk Brevo (SMTP).
7. Bangun modul inti dulu secara berurutan: **auth & role → profil fotografer & layanan → chat → booking/kontrak → pembayaran**. Modul pelengkap (forum, lomba foto, e-learning, notifikasi) dikerjakan setelah alur inti stabil.
8. Buat API endpoint atau route/controller terpisah per role area: `/admin/*`, `/photographer/*` (dashboard, perlu auth + role check), dan route publik/universal untuk client (`/`, `/fotografer/{id}`, `/forum`, dst — sebagian perlu auth hanya untuk aksi transaksional).

## 6. Langkah Setup — Frontend

1. Tentukan pendekatan: Blade + Livewire/Alpine.js (lebih simpel, satu stack dengan backend Laravel) atau SPA terpisah (React/Vue + API Laravel). Pilih salah satu di awal supaya konsisten.
2. Siapkan **3 layout dasar**:
   - `layout admin` — dashboard sidebar/topbar khas backoffice.
   - `layout photographer` — dashboard sama gayanya dengan admin tapi menu berbeda (fokus kelola layanan, availability, booking masuk).
   - `layout public/universal` — dipakai client dan guest, gaya website marketplace biasa (navbar/footer publik), bukan dashboard.
3. Bangun komponen shared dulu (dipakai lintas role): chat widget, notifikasi, form profil — supaya tidak duplikasi kerja.
4. Halaman public/universal diprioritaskan lebih dulu (landing page, browse fotografer, detail fotografer, forum, lomba foto) karena ini yang pertama dilihat calon client/dosen penguji.
5. Baru bangun halaman dashboard fotografer (kelola layanan, portofolio, availability, kontrak masuk) dan dashboard admin (kelola user, kategori, monitoring) setelah alur client selesai.
6. Integrasikan Laravel Reverb di sisi frontend untuk chat real-time (Echo.js) di halaman chat client & fotografer.
7. Integrasikan Midtrans Snap.js di halaman pembayaran client.

---

## 7. Desain & Konsistensi Frontend (Wajib Dibaca Sebelum Ngoding UI)

Tujuan bagian ini: supaya hasil UI **tidak terasa seperti "AI slop"** — yaitu tampilan generik khas hasil AI (kombinasi gradient ungu-biru, shadow tebal berlebihan, rounded-corner besar di semua elemen tanpa alasan, ikon emoji ditempel sembarangan, spacing tidak konsisten antar halaman, tiap halaman punya gaya sendiri-sendiri). Semua UI harus konsisten satu sama lain, seolah dibuat oleh satu desainer, bukan digenerate ulang tiap kali diminta.

**Aturan wajib sebelum membuat komponen/halaman baru:**

1. **Cek dulu folder frontend yang sudah ada** (struktur komponen, layout, partial, atau file CSS/Tailwind config yang sudah dibuat) sebelum bikin komponen baru dari nol. Kalau sudah ada pola untuk card, button, form input, navbar, dsb — **pakai ulang pola yang sama**, jangan bikin varian baru dengan gaya berbeda.
2. **Ambil design token yang sudah ada** (warna, font, spacing scale, border-radius, shadow) dari konfigurasi yang sudah ada (misal `tailwind.config.js` atau file variabel CSS yang sudah dibuat sebelumnya) — jangan menambahkan warna/token baru di luar itu kecuali benar-benar belum ada token yang cocok.
3. **Konsisten lintas halaman**: halaman baru harus terlihat senada dengan halaman yang sudah dibuat sebelumnya di modul yang sama (spacing, ukuran heading, gaya tombol, gaya card) — bukan didesain ulang dari nol setiap kali membuat halaman baru.
4. **Hindari pola khas "AI slop"**, kecuali memang sudah jadi bagian dari desain existing yang mau diikuti:
   - Gradient warna-warni tanpa alasan fungsional
   - Shadow berlebihan di banyak elemen sekaligus
   - Emoji dipakai sebagai pengganti ikon di UI production
   - Border-radius besar diterapkan ke semua elemen secara seragam tanpa hierarki visual
   - Layout landing page generik ala template SaaS ("hero besar + 3 kolom fitur + testimoni + CTA besar") tanpa penyesuaian ke konteks marketplace fotografer
5. **Standar Border-Radius System (Acuan Desain UI Wajib)**:
   - **Kartu, Widget Metric, & Kontainer Utama**: Wajib menggunakan `rounded-2xl` (`16px / 1rem`) sebagai acuan standar kelengkungan tepi kotak pada seluruh sistem (seperti pada rancangan Dashboard Admin).
   - **Form Input, Dropdown, & Tombol**: Gunakan `rounded-xl` (`12px`) atau `rounded-2xl` (`16px`).
   - **Badge & Pill Tag**: Gunakan `rounded-full`.
 6. **Standar Ikon UI System (Vektor SVG Asli Wajib)**:
    - Dilarang keras menggunakan emoji Unicode (seperti 💒, ✈️, 🎤, 📸, 🔍, ✨) atau panah teks unicode (`→`) sebagai pengganti ikon UI production.
    - Semua ikon wajib menggunakan sumber Vektor SVG (`<svg>`) asli bawaan TailAdmin Dashboard / Feather Vector Icons agar tampak bersih, profesional, dan presisi.
 7. **Standar Spacing & Hierarki Tipografi System (SweetEscape Layout Standard)**:
    - **Jarak Antar Seksi (Section Spacing)**: Wajib menggunakan jarak vertikal yang luas & lapang (`py-20 lg:py-24` / `space-y-20 lg:space-y-24` / 80px - 96px) di seluruh halaman sistem agar antar konten tidak mepet, memiliki ruang bernapas (*breathing room*), dan mata pengguna lebih rileks saat membaca.
    - **Skala Hirarki Teks (Typography Hierarchy Scale)**:
      - **Kicker / Sub-Badge**: `text-[11px] font-extrabold uppercase tracking-widest text-amber-600 dark:text-amber-400`.
      - **Judul Seksi Utama (H2)**: `text-3xl sm:text-4xl lg:text-[40px] font-black tracking-tight leading-[1.15] text-gray-900 dark:text-white`.
      - **Deskripsi Sub-Judul**: `text-sm sm:text-base text-gray-500 dark:text-gray-400 leading-relaxed max-w-2xl` (panjang kalimat dibatasi `max-w-2xl` agar mata nyaman memindai).
      - **Judul Kartu (H3)**: `text-lg sm:text-xl font-bold leading-snug`.
    - **Internal Card Padding**: Gunakan `p-6 lg:p-8` (24px - 32px) dengan jarak elemen internal `space-y-3` hingga `space-y-4` agar konten dalam kartu tidak padat atau menempel ke tepi.
 8. Kalau **belum ada folder frontend/desain existing sama sekali** (project baru dari nol), tetapkan design system dasar (warna, tipografi, spacing) di awal sebelum mulai bikin halaman satu-satu, lalu konsisten pakai itu untuk semua halaman berikutnya — jangan biarkan tiap prompt menghasilkan gaya visual yang berbeda-beda.
  9. **Standar Komponen Form, Dropdown, & Search Bar UI System**:
      - **Pemberian Padding Right pada Select Dropdown**: Dilarang membuat ikon chevron/panah pada boks dropdown (`<select>`) menempel atau mepet ke garis border sebelah kanan. Gunakan `appearance-none` dengan `pl-3.5 pr-9` serta tempatkan wrapper ikon SVG khusus (`pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5`).
      - **Standar Ikon Kaca Pembesar Search Bar (Position Right Wajib)**: Seluruh boks input pencarian (*search bar*) di seluruh sistem WAJIB menempatkan ikon Vektor SVG Kaca Pembesar di sebelah **KANAN** boks input (`absolute right-4 top-1/2 -translate-y-1/2` atau `right-3.5`). Input menggunakan `pl-5` untuk teks pencarian dan `pr-11` / `pr-12` untuk memberikan ruang tombol ikon di sisi kanan.
 10. **Rangkuman Catatan Revisi UI/UX Penting Pengembangan Kedepannya**:
     - **Background Seksi Full-Width**: Warna latar belakang alternatif (seperti `bg-amber-50` atau `bg-gray-50`) wajib dibuat *full-width* (`w-full`) dari ujung kiri ke ujung kanan layar, bukan dikotaki di tengah (*boxed border*).
     - **Ritme Alignment Layout (Selang-Seling)**: Alur bacaan antar seksi wajib memiliki variasi alignment teks yang alami (misalnya *Center → Left → Right → Center*) agar pandangan pengguna tidak monoton saat di-scroll.
     - **Kualitas Foto & Asset HD**: Seluruh foto profil fotografer dan karya portofolio wajib menggunakan gambar beresolusi tinggi (HD Unsplash / Tajam) yang relevan dengan konteks fotografi.
     - **Pelonggaran Padding Kartu & Deskripsi Profil**: Kartu fotografer dan testimoni wajib memiliki padding internal yang lega (`p-7 sm:p-8`) dan deskripsi yang cukup panjang/jelas agar tidak terkesan sesak.
     - **Boks Testimoni & Logika Slider Non-Looping**: Testimoni pelanggan menggunakan 3 boks kartu utama berukuran besar & lapang dengan kartu tengah yang menonjol (`scale-105 shadow-2xl border-2 border-amber-400`). Slider wajib bersifat *bounded/non-looping* (tombol panah mati saat di ujung slide ke-5 atau ke-1) dan animasi scroll reveal dapat terpicu ulang (*repeatable*) saat di-scroll naik-turun.
     - **Animasi Melayang (Hero Floating Cards)**: Galeri foto pada Hero Section dilengkapi animasi melayang (*organic float effect*) dengan ritme *staggered delay* agar web tampak modern dan dinamis.
  11. **Standar Komponen Custom Dropdown (`Alpine.js Custom Select`) Modern**:
      - **WAJIB Menggunakan Alpine.js Custom Floating Dropdown**: Dilarang menggunakan tag HTML native `<select><option>` untuk UI dropdown publik maupun dashboard, karena kontrol native OS Windows mengabaikan CSS spacing dan border.
      - **Format Struktur Komponen Dropdown**:
        - Menggunakan `x-data="{ open: false, selectedCode: '...', selectedText: '...' }"` dengan hidden `<input type="hidden">`.
        - Setiap item pilihan wajib menggunakan padding internal yang lega (`px-4 py-2.5`), garis bawah pembatas tipis (`border-b border-gray-100 dark:border-gray-700/60`), serta efek hover sorotan (`hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600`).
        - Boks container melayang dilengkapi sudut melengkung `rounded-xl`, bayangan `shadow-xl`, `max-h-60`, dan `overflow-y-auto` agar tampilan seluruh dropdown di sistem 100% seragam, rapi, dan modern.
  15. **Standar Penggunaan Data Dinamis & Seeder Database (DILARANG HARDCODED HTML TEXT)**:
      - **WAJIB MENGGUNAKAN DATA DINAMIS DATABASE MYSQL**: Seluruh pengembangan modul, halaman, tabel, kartu, list, statistik, dan fitur interaksi (seperti menyukai/tidak menyukai komentar, tayangan, balasan) DILARANG KERAS MENGGUNAKAN data pasif atau dummy hardcoded text belaka di dalam HTML.
      - **Pembuatan Kolom Database & Seeder Otomatis**: Setiap kali mengimplementasikan fitur yang berhubungan dengan data, WAJIB langsung membuat kolom/field penampung di tabel database MySQL (seperti `likes`, `dislikes`, `views`) jika belum ada, lalu mengisinya dengan data dinamis riil menggunakan Seeder Database.
  16. **Standar Proteksi Hak Akses Middleware Berdasarkan Role (`Strict Role-Based Protection`)**:
      - **Proteksi Rute Wajib Ketat**: SELURUH rute proteksi wajib menggunakan middleware `auth` DAN middleware `role` yang sesuai (`role:super_admin,admin` untuk `/admin/*`, `role:photographer` untuk `/photographer/*`, dan `role:client` untuk `/client/*`).
      - **Mencegah Akses Silang Pengguna**: Pengguna yang mencoba mengakses rute milik role lain secara manual via URL bar (misal Admin/Client mencoba membuka `/photographer/bookings` tanpa login sebagai Fotografer) WAJIB ditolak dan dialihkan kembali ke dashboard masing-masing secara otomatis dengan pesan peringatan 'Akses ditolak!'.
  17. **Standar Alur Redirection Logout Pengguna (Direct to Home `/`)**:
      - **Wajib Diarahkan ke Beranda**: Ketika pengguna (Client, Fotografer, maupun Admin) menekan tombol Logout, setelah sesi berhasil dihancurkan, pengguna WAJIB langsung diarahkan kembali ke **Beranda / Halaman Utama (`/`)**, BUKAN ke halaman Login atau Register.
      - **Pesan Konfirmasi**: Dilengkapi notifikasi alert hijau sukses 'Anda telah berhasil keluar dari akun.' pada halaman beranda.
  18. **Standar Komponen Template Modal Konfirmasi / Validasi Aksi Vital (`Universal Confirm Modal`)**:
      - **Wajib Digunakan pada Aksi Vital & CRUD**: Seluruh tindakan vital seperti Logout, Hapus Data (Delete), Pembatalan Pesanan, atau Pengubahan Status Kontrak WAJIB melalui dialog modal konfirmasi validasi terlebih dahulu (`@open-confirm-modal.window`).
      - **Format Komponen Shared**: Menggunakan partial `@include('partials.shared.confirm-modal')` yang seragam di seluruh 3 layout (App, Photographer, Admin) dengan sudut `rounded-2xl`, latar blur `backdrop-blur-sm`, dan ikon Vektor SVG sesuai jenis aksi (`logout`, `danger`, `warning`, `info`). Dilarang menggunakan dialog `confirm()` bawaan browser.
  19. **Standar Komponen Toast Notification Shared (`Universal Toast Notification`)**:
      - **Desain Minimalis Modern dengan Animated Shrinking Bottom Progress Line**: Komponen Toast Notifikasi melayang menggunakan latar belakang putih bersih (`bg-white dark:bg-gray-800`), sudut `rounded-2xl`, bayangan `shadow-xl`, dan **garis indikator bawah beranimasi (`h-[3.5px]`)** yang bergerak menyusut (*shrinking countdown progress bar*) secara halus dari 100% ke 0% mengikuti sisa waktu/durasi aktif toast (Hijau `#10b981` untuk Success, Merah `#ef4444` untuk Error, Amber `#f59e0b` untuk Warning, Biru `#3b82f6` untuk Info).
      - **Ikon Outlined Circular Vektor SVG**: Menggunakan ikon lingkaran vektor stroke tipis (Circle dengan X, Checkmark, Exclamation, dan i) tanpa warna background fill yang tebal.
      - **Mendukung Laravel Flash Session & JS Events**: Otomatis membaca `session('success')`, `session('error')`, `session('warning')`, `session('info')` serta mendukung pemicuan via `@show-toast.window` dengan auto-dismiss 4.5 detik.
  22. **Standar Waktu Chat Real-Time Berdasarkan Device User (`Client-Side Local Timezone`)**:
      - **Format Waktu Device Pengguna**: Waktu jam & menit pada setiap gelembung obrolan chat (`created_at`) WAJIB dikonversi secara otomatis di sisi klien (*client-side JavaScript*) menggunakan zona waktu & jam aktual device pengakses website (`new Date(isoStr).toLocaleTimeString(navigator.language, { hour: '2-digit', minute: '2-digit', hour12: false })`).
      - **Kesesuaian Lokasi Pengguna**: Memastikan pengguna di zona WIB, WITA, WIT, maupun internasional melihat jam pengiriman pesan yang presisi sesuai jam di perangkat (device) mereka masing-masing.

  21. **Standar Strict Role System & Static Top-Right Header Badge**:
      - **3 Role Resmi Platform (Strict & Tanpa Kompromi)**: Hanya ada 3 role di dalam sistem: **klien**, **fotograper**, dan **admin**. Jangan membuat aneh-aneh di UI (dilarang menambah istilah buatan seperti 'Studio Fotografer', 'Administrator', dll).
      - **Header Badge Pojok Kanan Atas Dashboard**: Header kanan atas di dashboard (`partials/shared/header.blade.php`) bersifat STATIS TANPA TRIGGER POPUP DROPDOWN REDUNDAN. Hanya menampilkan avatar, nama user, dan role resmi (Klien, Fotografer, atau Admin). Seluruh menu navigasi & tombol Logout berada secara terpusat di Sidebar kiri.

  20. **Standar Indikator Online & Last Seen Real-Time (Shopee Style)**:
      - **Pengukuran Aktif Berdasarkan Database (`users.last_seen_at`)**: Dilarang keras menggunakan teks 'Online' pasif di HTML. Status `Online` / `Last Seen` WAJIB dihitung secara kalkulatif dari kolom `last_seen_at` pada tabel `users`.
      - **Aturan Kalkulasi Waktu (Shopee Standard)**:
        - Jika `last_seen_at` < 3 menit ➔ Tampilkan badge hijau `Online`.
        - Jika offline < 60 menit ➔ Tampilkan `Aktif X menit lalu`.
        - Jika offline 1-24 jam ➔ Tampilkan `Aktif X jam lalu`.
        - Jika offline 1-7 hari ➔ Tampilkan `Aktif X hari lalu`.
        - Jika offline > 7 hari / NULL ➔ Tampilkan `Aktif 7+ hari lalu`.

---


### 7.1 Standarisasi Khusus Dashboard & Seluruh Tabel Sistem:

8. **Warna Teks Keterangan Subtitle Stat Card Wajib Netral Gelap / Abu-Abu**:
   - Dilarang keras mewarnai teks keterangan subtitle kartu metrik statistik dengan warna mencolok (seperti hijau, merah, atau kuning amber).
   - Seluruh teks keterangan kartu statistik di seluruh sistem wajib menggunakan warna netral gelap atau abu-abu standar (`text-gray-400 dark:text-gray-500 font-medium`).
   - Apabila terdapat angka atau data penjelas di dalam teks keterangan subtitle, angka tersebut wajib berwarna hitam/gelap netral (`font-bold text-gray-700 dark:text-gray-300`).

9. **Larangan Simbol / Icon / Karakter Centang pada Teks Subtitle Card**:
   - Dilarang keras menaruh simbol karakter seperti centang (`✓`), emoji, atau icon karakter unicode apa pun di dalam teks keterangan (*subtitle*) kartu metrik statistik (*Metric Stat Cards*).
   - Teks keterangan wajib berupa teks murni yang bersih, profesional, dan ringkas tanpa ornamen simbolik.

10. **Standarisasi Tombol Kolom AKSI pada Seluruh Tabel Dashboard**:
    - DILARANG menggunakan icon / SVG / emoji / simbol panah di dalam tombol-tombol aksi pada kolom tabel `AKSI` di seluruh dashboard (Admin, Fotografer, atau role lainnya).
    - Seluruh tombol aksi pada tabel wajib hanya berupa teks bersih (*pure text*) yang jelas, ringkas, dan profesional (misalnya: `Lihat`, `Edit`, `Hapus`, `Cairkan Payout`, `Proses Refund`, `Review Karya`, `Verifikasi`, `Batal Verifikasi`, `Reset Password`, `Tampilkan`, `Sembunyikan`).

11. **Standarisasi Tampilan Tipe Akun (Role) di Seluruh Tabel & Sistem**:
    - **DILARANG** membungkus nama tipe akun/role ke dalam bentuk kartu/badge box/pill (tanpa background `bg-*`, tanpa border `border-*`, dan tanpa padding box `px-* py-*`).
    - Tipe Akun wajib ditampilkan sebagai **Teks Murni (*Pure Text*)** dengan pembeda warna teks (*font color*) yang spesifik dan konsisten untuk masing-masing role:
      1. **Super Admin**: Warna **Kuning Emas / Amber Brand** (`text-amber-600 dark:text-amber-400 font-extrabold text-xs`)
      2. **Staf Admin**: Warna **Biru Profesional** (`text-blue-600 dark:text-blue-400 font-bold text-xs`)
      3. **Fotografer**: Warna **Ungu Kreatif** (`text-purple-600 dark:text-purple-400 font-bold text-xs`)
      4. **Klien**: Warna **Hijau Emerald** (`text-emerald-600 dark:text-emerald-400 font-bold text-xs`)

12. **Standarisasi Desain Tombol Aksi Hapus (Delete Action Button)**:
    - Tombol Hapus pada seluruh tabel dan dashboard (Admin, Fotografer, Klien) wajib menggunakan standar styling:
      - **Classes**: `px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200/80 dark:border-rose-900/60 font-bold text-xs transition min-w-[60px] text-center`
      - **Warna & Latar**: Background merah rose lembut (`bg-rose-50`), teks merah rose tajam (`text-rose-600`), serta outline border merah tipis (`border border-rose-200/80`).
      - **Bentuk & Ukuran**: `rounded-lg` dengan padding `px-3 py-1.5` dan font `text-xs font-bold` (murni teks tanpa icon).

13. **Standarisasi Desain Tombol Aksi Edit (Edit Action Button)**:
    - Tombol Edit pada seluruh tabel dan dashboard (Admin, Fotografer, Klien, atau role mana pun di masa mendatang) wajib selalu menggunakan standar UI ini agar selaras dan proporsional 100% dengan tombol Hapus (Rule #12):
      - **Tailwind Classes**: `px-3 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200/80 dark:border-amber-900/60 font-bold text-xs transition min-w-[60px] text-center`
      - **Warna & Latar**: Background kuning amber lembut (`bg-amber-50`), teks kuning amber tajam (`text-amber-700`), serta outline border kuning tipis (`border border-amber-200/80`).
      - **Bentuk & Proporsi**: Menggunakan `rounded-lg`, padding seragam `px-3 py-1.5`, ukuran font `text-xs font-bold`, dan lebar minimum seimbang `min-w-[60px]` (murni teks tanpa icon).

14. **Standarisasi Tampilan Status Transaksi & Escrow/Payout di Seluruh Halaman**:
    - **DILARANG** menggunakan badge/pill box (tanpa background `bg-*`, tanpa border `border-*`, tanpa padding box `px-* py-*`, dan tanpa icon/bullet dot `w-1.5 h-1.5 rounded-full`).
    - Status wajib ditampilkan sebagai **Teks Murni Berwarna (*Pure Colored Text*)** dengan teks label yang seragam di seluruh dashboard & halaman sistem:
      1. **Payout Dicairkan**: Teks Hijau Emerald (`text-xs font-bold text-emerald-600 dark:text-emerald-400`)
      2. **Siap Dicairkan**: Teks Biru Profesional (`text-xs font-bold text-blue-600 dark:text-blue-400`)
      3. **Holding Escrow**: Teks Kuning Amber (`text-xs font-bold text-amber-600 dark:text-amber-400`)
      4. **Menunggu Pembayaran**: Teks Kuning Amber (`text-xs font-bold text-amber-600 dark:text-amber-400`)
      5. **Refund**: Teks Merah Rose (`text-xs font-bold text-rose-600 dark:text-rose-400`)
      6. **Dibatalkan**: Teks Abu-Abu Netral (`text-xs font-bold text-gray-400 dark:text-gray-500`)

---

## 8. Catatan untuk AI IDE

- Ikuti penamaan kolom & tabel persis seperti pada Bagian 3 (konvensi Bahasa Indonesia snake_case, contoh: `id_photographer`, `booking_date`) — jangan diterjemahkan ke Bahasa Inggris supaya konsisten dengan database yang sudah di-running.
- Semua tabel utama pakai soft delete (`deleted_at`) kecuali `activity_logs`, `payment_logs`, `notifications`, `contract_cancellations`, dan `password_reset_tokens`.
- Prioritaskan membangun modul sesuai urutan di Bagian 5 & 6 — jangan mulai dari fitur pelengkap (forum/lomba/e-learning) sebelum alur booking-kontrak-pembayaran selesai dan stabil.
- Sebelum membuat komponen/halaman UI apa pun, ikuti aturan di Bagian 7 — cek desain yang sudah ada dulu, jangan generate gaya baru yang tidak konsisten.
---

## 9. Status Modul Booking (Hold) & Rencana Alur Data (Data Flow)

### 📌 Status Pengembangan Modul Booking (Saat Ini):
- **Form Reservasi Sesi Foto (`/booking/create`)**: Berhasil dibuat dengan live calculator, date/time picker, & Alpine.js custom location dropdowns.
- **Invoice Pembayaran Escrow (`/pembayaran/{id}`)**: Berhasil digenerate dengan kode transaksi unik (contoh: `INV-20260815-XXXX`), status awal kontrak `pending_payment`, serta simulasi pembayaran QRIS/Transfer BCA Escrow.
- **Status Modul**: **HOLD** (Pengembangan form & invoice sudah stabil; penyesuaian UI & integrasi payment gateway Midtrans akan dilanjutkan pada tahap berikutnya).

---

### 🔄 Alur Kemunculan Data Booking ke Berbagai Role:

1. **Role Photographer (Dashboard Studio)**:
   - Data booking yang baru diajukan oleh Klien secara otomatis masuk ke **Dashboard Studio ➔ Order Booking Masuk (`/photographer/bookings`)**.
   - Fotografer menerima notifikasi & dapat melihat detail sesi (tanggal, jam, lokasi, catatan khusus klien) untuk melakukan aksi **Konfirmasi / Setujui Sesi Foto** atau **Tolak/Jadwal Ulang**.
   - Setelah sesi foto selesai, Fotografer mengunggah tautan galeri foto ke pesanan ini.

2. **Role Admin (Dashboard Utama)**:
   - Data kontrak & invoice baru muncul secara otomatis di **Dashboard Admin ➔ Monitoring Transaksi (`/admin/transactions`)**.
   - Admin memantau status dana escrow yang mengendap (pending / dibayar / diteruskan ke fotografer) serta statistik total transaksi platform.

3. **Role Client (Website Utama / Navbar)**:
   - Klien yang telah mengajukan booking dapat mengakses seluruh histori pemesanan kapan saja melalui menu navbar profil:
     - 📅 **Reservasi Saya (`/client/bookings`)**: Melihat daftar booking, status konfirmasi studio, dan mengunduh hasil foto.
     - 💳 **Invoice & Pembayaran (`/client/invoices`)**: Melihat status tagihan & melakukan pembayaran ulang jika belum lunas.

---

## 10. Rekomendasi Progress Selanjutnya (Next Progress Roadmap)

Berdasarkan prioritas arsitektur marketplace, berikut adalah urutan rekomendasi progress berikutnya yang siap untuk diimplementasikan:

### 🏆 Rekomendasi 1 (Sangat Direkomendasikan / Prioritas Inti):
- **Modul Kelola Booking Studio (`/photographer/bookings`) & Kelola Reservasi Klien (`/client/bookings`)**:
  - Membangun halaman antarmuka bagi **Fotografer** untuk melihat order booking masuk, mengubah status dari `pending` ➔ `confirmed` / `completed`, dan mengunggah hasil foto.
  - Membangun halaman **Reservasi Saya** bagi **Klien** untuk memantau progres pesanan & mengakses galeri hasil foto secara transparan.

### 💬 Rekomendasi 2 (Prioritas Komunikasi):
- **Modul Fitur Chat Real-time Studio & Klien (`/chat`)**:
  - Menghubungkan komunikasi langsung antara Klien dan Studio Fotografer sebelum atau sesudah booking diajukan untuk negosiasi tema foto/lokasi.

### 🛡️ Rekomendasi 3 (Prioritas Backoffice Admin):
- **Modul Monitoring Transaksi & Escrow Admin (`/admin/transactions`)**:
  - Halaman pengawasan penuh bagi Admin untuk menyetujui pencairan dana (*payout*) dari Escrow ke rekening fotografer setelah sesi foto selesai.


   20. **Standar Indikator Online & Last Seen Real-Time (Shopee Style)**:
       - **Pengukuran Aktif Berdasarkan Database (`users.last_seen_at`)**: Dilarang keras menggunakan teks 'Online' pasif di HTML. Status `Online` / `Last Seen` WAJIB dihitung secara kalkulatif dari kolom `last_seen_at` pada tabel `users`.
       - **Aturan Kalkulasi Waktu (Shopee Standard)**:
         - Jika `last_seen_at` < 3 menit ➔ Tampilkan badge hijau `Online`.
         - Jika offline < 60 menit ➔ Tampilkan `Aktif X menit lalu`.
         - Jika offline 1-24 jam ➔ Tampilkan `Aktif X jam lalu`.
         - Jika offline 1-7 hari ➔ Tampilkan `Aktif X hari lalu`.
         - Jika offline > 7 hari / NULL ➔ Tampilkan `Aktif 7+ hari lalu`.
   23. **Standar Monitoring Transaksi & Escrow Admin (`/admin/transactions`)**:
       - **Monitoring Dana Escrow**: Mengawasi arus dana yang masuk dari Klien ke Rekening Bersama Platform sebelum dicairkan ke Fotografer.
       - **Formulir Payout & Fee Calculation**: Menghitung komisi marketplace 10% dan pencairan payout fotografer 90% secara otomatis tanpa biaya API disbursement eksternal (Zero-Cost Integration).
   24. **Standar Modul Ulasan & Rating Pelanggan (Testimonials / Reviews)**:
       - **Aturan Single Review per Completed Booking**: Ulasan hanya dapat diberikan 1 kali per kontrak yang berstatus `completed`.
       - **Rating Star Breakdown**: Rating bintang (1-5) dihitung secara rata-rata (misal ⭐ 4.9 / 5.0) dan ditampilkan di halaman profil studio fotografer & slider testimoni beranda.
   25. **Standar Modul Laporan Keuangan & Statistik Studio (`/photographer/reports`)**:
       - **Laporan Finansial Studio**: Menyajikan 4 kartu statistik (Pendapatan Bersih 90%, Escrow Pending, Sesi Selesai, Rata-Rata/Sesi) dan grafik batang SVG omzet bulanan.
       - **Export Cetak PDF**: Menyediakan fitur ekspor laporan keuangan format siap cetak / simpan PDF (`window.print()`).
   26. **Standar Modul Notifikasi In-App Lonceng Header & Alert Pembayaran/Booking**:
       - **Lonceng Header Real-Time**: Lonceng di header (`navbar.blade.php`, `admin/header.blade.php`, `photographer/header.blade.php`) menampilkan badge angka merah menyala `unreadCount` dengan animasi pulse.
       - **Format UI Dropdown Notifikasi Bersih & To-the-Point**:
         - Format konten berupa **satu kalimat langsung (*to-the-point sentence*)** tanpa pemisahan judul/deskripsi tebal berlebihan.
         - Latar belakang murni putih bersih (`bg-white dark:bg-gray-800`), tanpa latar abu-abu/tinted.
         - Garis pemisah (*divider line*) hanya berada di antara baris notifikasi (`divide-y divide-gray-100 dark:divide-gray-700/60`).
       - **Navigasi Otomatis & Trigger Helper**: Menggunakan `Notification::send()` untuk auto-dispatch pesan booking, gallery, payment, dan payout. Mengeklik notifikasi menandai status dibaca (`is_read = true`) dan mengarahkan pengguna ke halaman transaksi terkait.
   27. **Standar Modul Moderasi Forum Diskusi & E-Learning Admin (`/admin/elearning`, `/admin/forum`)**:
       - **Akademi E-Learning Admin (`/admin/elearning`)**: Pengelolaan materi edukasi fotografi lengkap dengan stat card total modul & total pembaca (`view_count`).
       - **Moderasi Forum Komunitas Admin (`/admin/forum`)**: Monitoring postingan diskusi publik & balasan komentar dengan aksi pembersihan spam menggunakan modal konfirmasi (`@open-confirm-modal`).
   28. **Standar Modul Manajemen Lomba Foto Admin (`/admin/contests`, `/lomba`)**:
       - **Manajemen Event Lomba Foto Admin (`/admin/contests`)**: Pengelolaan event kompetisi fotografi (Judul, Kategori, Hadiah Total, Periode Mulai & Deadline, Penyelenggara, Status Buka/Selesai, & Banner Image).
       - **Review Submisi & Penetapan Juara (`/admin/contests/{id}/submissions`)**: Peninjauan foto karya peserta dan penetapan status Pemenang (Juara 1 🥇, Juara 2 🥈, Juara 3 🥉).
   38. **Standar Smooth Seamless AJAX Pagination (Tanpa Refresh Halaman Utuh)**:
      - **Zero Full Page Refresh**: Seluruh tombol pagination (`<<`, `<`, `1`, `2`, `>`, `>>`), perubahan `Rows per page` (`10`, `20`, `50`, `100`), filter kategori, dan form pencarian WAJIB menggunakan **AJAX Fetch Fetching** secara mulus tanpa memuat ulang halaman (*page reload*).
      - **Transisi Halus & History PushState**: Kontainer konten diperbarui dengan transisi fading halus (`opacity-40 transition-opacity duration-150`), scroll halus otomatis ke posisi awal section, serta menjaga URL di address bar browser via `history.pushState`.
  37. **Standar Komprehensif Custom Pagination UI, Backend `per_page`, & Smooth AJAX Loading**:
      - **A. Standar Tampilan UI Pagination (Borderless & Individual Element Buttons)**:
        - **Dilarang Memakai Card Pembungkus Luar**: Seluruh komponen pagination TIDAK BOLEH dibungkus dalam 1 boks card border besar. Elemen kontrol pagination berdiri sendiri secara bersih di atas background halaman.
        - **Boks Tombol Mandiri**:
          - Sisi Kiri: `Showing page [ X ] of Y` (Nomor halaman aktif berada dalam boks tombol mandiri `[ X ]`).
          - Sisi Tengah: Tombol navigasi mandiri `<<`, `<`, `[ 1 ]` (latar amber solid untuk halaman aktif), `2`, `3`, `...`, `>`, `>>` menggunakan ikon Vektor SVG murni.
          - Sisi Kanan: Boks dropdown mandiri `Rows per page [ 10 v ]`.
        - **Komponen Reusable**: Dikelola secara terpusat pada file Blade `resources/views/partials/public/pagination.blade.php`.
        - **Visibilitas Selalu Tampak (`total() > 0`)**: Barisan kontrol pagination dan tombol `Rows per page` WAJIB TETAP TAMPAK selama ada data (`@if($posts->total() > 0)`), meskipun jumlah data muat dalam 1 halaman (`total <= per_page`), agar pengguna tetap dapat beralih opsi `Rows per page` kapan saja.

      - **B. Standar Logika Backend Controller (`per_page`)**:
        - **Fixed Default 10 Items**: Secara default membatasi tampilan data sebesar **10 data per halaman** (`paginate(10)`).
        - **Parameter Query Dinamis**: Controller (`ForumController`, `ContestController`, `ElearningController`) membaca parameter `$perPage = (int) $request->query('per_page', 10)`.
        - **Opsi Validasi Terkunci**: Pilihan `$perPage` hanya diizinkan pada nilai `[10, 20, 50, 100]` (fallback otomatis ke 10 jika nilai tidak sesuai).
        - **Preservasi Query String**: Seluruh kueri pencarian & kategori wajib dipertahankan melalui `$query->paginate($perPage)->withQueryString()`.

      - **C. Standar Cara Loading Smooth Seamless AJAX (Tanpa Refresh Halaman Utuh)**:
        - **Zero Full Page Reload**: Interaksi tombol pagination (`<<`, `<`, `1`, `2`, `>`, `>>`), perubahan `Rows per page` (`10`, `20`, `50`, `100`), filter kategori/urutkan, dan pencarian WAJIB menggunakan metode **AJAX Fetch Async** tanpa me-refresh seluruh halaman web (`loadAjaxPage()`).
        - **Efek Visual Transisi Fading**: Kontainer data (`#forum-ajax-container`) memberikan efek transisi redup halus (`opacity-40 transition-opacity duration-150`) saat fetching data berlangsung.
        - **DOM Swapping & Smooth Scroll**: Mengganti isi HTML kontainer secara presisi via `DOMParser()` dan melakukan *smooth scrolling* otomatis ke bagian awal section.
        - **Preservasi URL Browser**: Alamat URL di address bar browser wajib diperbarui secara otomatis menggunakan `history.pushState(null, '', url)` dan menangani tombol Back/Forward browser via listener `popstate`.

  39. **Standar Desain UI Proporsional & Anti-Mepet Kartu Pembungkus (Proportional Card Padding Rule)**:
      - **Dilarang Teks/Konten Mepet**: SELURUH elemen teks, judul, deskripsi, gambar, ikon, dan footer dalam boks kartu pembungkus (*card container*) DILARANG KERAS dibuat mepet atau menempel ke garis border tepi (atas, bawah, kiri, maupun kanan).
      - **Minimal Internal Padding (`p-7` hingga `p-9`)**: Seluruh boks kartu publik maupun dashboard WAJIB memiliki padding internal yang sangat lega (`p-7 sm:p-8 lg:p-9` / 28px - 36px di semua sisi).
      - **Jarak Proporsional Pembatas (Tight Divider Spacing)**: Garis pembatas (*border-t*) untuk footer meta di dalam kartu harus menggunakan jarak atas yang proporsional (`pt-3.5 sm:pt-4 mt-3` atau 14px - 16px) agar area penulis/avatar tidak mengambang terlalu tinggi dari garis pembatas.
      - **Proporsi Ikon & Teks**: Ikon pendukung (seperti ikon tayangan mata & balasan komentar) harus berukuran proporsional (`w-4 h-4 sm:w-4.5 sm:h-4.5`) dan selaras dengan hirarki font sekitar.


  40. **Status Pembekuan Fitur & Mekanisme Detail Forum (`/forum/{id}` - HOLD Status)**:
      - **Status Fitur 100% Selesai & Di-Hold**: Seluruh alur mekanisme, tata letak UI proporsional, seksi balasan komentar, sistem menyukai/tidak menyukai (*likes/dislikes*), penghitung tayangan dinamis (*views*), serta alur modal login guest senyap di belakang layar (*2s delay & 5s cooldown loop*) dengan *intended URL redirection* pada halaman Detail Forum (`/forum/{id}`) telah **100% SELESAI DIKEMBANGKAN DAN DIBEKUKAN SEMENTARA (ON HOLD)** atas instruksi pengguna sebelum melanjutkan ke modul/fitur selanjutnya.
  41. **Larangan Mutlak Menambahkan UI Garis Pembatas (Divider / Border-T / Border-B) Sembarangan**:
      - **DILARANG KERAS MENAMBAHKAN GARIS PEMBATAS/DIVIDER**: Dilarang menambahkan garis horizontal (`border-t`, `border-b`, `<hr>`) secara sembarangan di atas atau di bawah teks, judul, lokasi, atau elemen kartu KECUALI secara eksplisit diminta oleh pengguna. Garis berlebihan membuat UI terkesan kaku, penuh sekat artifisial, dan terindikasi sebagai pola "AI Slop".
      - **Gunakan Ruang Spacing Bersih (*Whitespace*)**: Seluruh pemisahan antar blok konten di dalam kartu wajib mengandalkan whitespace / margin alami yang proporsional (`space-y-2` / `space-y-3` / `pt-2`), bukan menyisipkan garis divider.
  42. **Larangan Keras Berinisiatif Menambahkan Elemen / Teks / Desain Tanpa Instruksi Prompter**:
      - **DILARANG KERAS BERINISIATIF MENAMBAH ELEMEN UI SENDIRI**: Dilarang keras mengada-ada atau menambahkan elemen teks, metrik angka, label pembantu, atau ornamen visual apa pun yang TIDAK secara eksplisit diminta oleh pengguna/prompter.
      - **Kepatuhan Mutlak Terhadap Prompt**: Kerjakan HANYA apa yang diperintahkan. Jika diminta menghapus harga atau mengubah teks tertentu, hapus/ubah elemen tersebut saja tanpa menambahkan pengganti teks atau desain lain di luar instruksi.

  43. **Standar UI Kartu Fotografer (LensMatch Photographer Card Standard) — WAJIB DIGUNAKAN DI SELURUH PROYEK**:
      - **Referensi Standar**: Seluruh tampilan kartu fotografer (card kotak panjang) di manapun dalam proyek ini — beranda (`home.blade.php`), eksplorasi fotografer (`photographers/index.blade.php`), dan katalog fotografer (`photographers/katalog.blade.php`) — WAJIB identik secara visual mengikuti standar tipografi murni & hierarki modern berikut:
      - **Struktur Wajib Kartu** (urutan dari atas ke bawah):
        1. **Cover Foto Portfolio** (`relative h-56 w-full overflow-hidden bg-gray-100 dark:bg-gray-700 shrink-0`, `group-hover:scale-105 transition duration-500`): gambar portofolio teratas dari database.
        2. **Badge Rating** (pojok kanan atas cover, `bg-white/95 dark:bg-gray-900/95`, bintang amber `text-amber-400`, rating font-black + jumlah review `font-medium text-gray-400`).
        3. **Lokasi Studio** (ikon pin `w-3.5 h-3.5`, teks `text-[11px] text-gray-400 dark:text-gray-400 font-light mb-2.5 truncate uppercase`): berada tepat di atas baris avatar & nama.
        4. **Avatar + Nama** (avatar `w-10 h-10 rounded-xl border-2 border-amber-400 shadow-xs shrink-0`, nama `font-extrabold text-base sm:text-lg text-gray-900 dark:text-white leading-snug`, side by side `flex items-center gap-3`).
        5. **Kategori Spesialisasi (Plain Text Tipografi Murni)** (`mt-2`, `text-xs font-light text-amber-700 dark:text-amber-300 leading-snug`, koma-separated: `Wedding & Prewedding Sesi, Birthday & Party, Aerial Drone`). DILARANG memakai badge/pill box kapsul berwarna sesuai Rule #67!
        6. **Bio Deskripsi Singkat** (`mt-4 pt-1`, `text-xs text-gray-500 dark:text-gray-400 leading-relaxed`, limit 180 karakter dengan fallback aman fotografer terverifikasi).
        7. **Label "MULAI DARI"** (`text-[10px] font-bold text-gray-400 uppercase tracking-wider block`).
        8. **Harga Terendah** (`font-black text-base sm:text-lg text-gray-900 dark:text-white leading-tight`, wajib warna netral hitam/putih solid, DILARANG memakai warna primary amber alay!). Dihitung dari `tarif_harga > 0`.
        9. **Tombol "Lihat Profil" Full-Width** (`py-3 bg-[#222222] dark:bg-gray-700 text-white hover:bg-amber-400 hover:text-gray-900 rounded-xl text-xs font-extrabold transition duration-200 shadow-xs`), icon navigasi WAJIB Single Chevron `>` (`d="M8.25 4.5l7.5 7.5-7.5 7.5"`) sesuai Rule #72.
      - **Padding Container**: Body atas `px-6 sm:px-7 pt-4 pb-0`; Footer `px-6 sm:px-7 pt-3.5 pb-6 sm:pb-7 space-y-3 mt-auto`.
      - **Halaman yang Telah Menerapkan Standar Ini**:
        - `resources/views/public/home.blade.php` (seksi Fotografer Pilihan)
        - `resources/views/public/photographers/index.blade.php` (Cari Fotografer)
        - `resources/views/public/photographers/katalog.blade.php` (Katalog Fotografer)
      - **DILARANG** membuat card fotografer dengan struktur berbeda dari standar ini tanpa persetujuan eksplisit dari prompter.

  44. **Standar Avatar User — Foto Profil atau Inisial Nama (LensMatch User Avatar Standard) — WAJIB DI SELURUH PROYEK**:
      - **Mekanisme Utama**: Setiap tampilan lingkaran avatar untuk user (bukan cover foto fotografer) di manapun dalam proyek ini WAJIB mengikuti mekanisme berikut:
        1. **Jika user memiliki foto profil** (`role_photographers.foto` via relasi `user->rolePhotographer->foto`): Tampilkan foto tersebut sebagai `<img>` dalam lingkaran `rounded-full object-cover`.
        2. **Jika TIDAK ada foto profil**: Tampilkan lingkaran dengan **HURUF INISIAL NAMA USER** (maksimal 2 huruf dari kata pertama dan kedua nama, `strtoupper`) sebagai fallback.
      - **Implementasi Teknis**:
        - **User Model** (`app/Models/User.php`): Terdapat dua accessor:
          - `$user->avatar_url` → mengembalikan URL foto jika ada, atau `null` jika tidak ada.
          - `$user->initials` → mengembalikan 1-2 huruf inisial dari `user->nama`.
        - **Blade Component** (`resources/views/components/user-avatar.blade.php`): Gunakan `<x-user-avatar>` untuk semua tampilan avatar user.
          ```blade
          <x-user-avatar :user="$post->user" />                                  — default size w-9 h-9
          <x-user-avatar :user="$comment->user" size="w-10 h-10" />              — custom size
          <x-user-avatar :user="auth()->user()" size="w-9 h-9 sm:w-10 sm:h-10" bg="bg-amber-400" textColor="text-gray-900" />
          ```
        - **Props `<x-user-avatar>`**: `user`, `size` (default `w-9 h-9`), `textSize` (default `text-xs`), `bg` (default abu-abu), `textColor`, `class`.
      - **Catatan Database**: Tabel `users` **TIDAK memiliki kolom `foto`**. Foto profil HANYA tersimpan di tabel `role_photographers.foto` (hanya untuk user berprofil fotografer). Admin dan Client tidak memiliki foto profil — maka SELALU menampilkan inisial.
      - **Halaman yang Telah Menerapkan Standar Ini**:
        - `resources/views/public/forum/index.blade.php` (avatar penulis postingan forum)
        - `resources/views/public/forum/show.blade.php` (avatar penulis & penulis komentar)
        - `resources/views/partials/public/navbar.blade.php` (avatar user login di navbar)
      - **DILARANG** membuat lingkaran avatar user dengan teks inisial statis hardcoded (tanpa cek foto profil dari database) di seluruh proyek ini.

  45. **Standar Pagination Active Page (LensMatch Pagination Standard) — WAJIB DI SELURUH PROYEK**:
      - **Mekanisme & Komposisi Warna**:
        - **Halaman Aktif (Current Page)**: WAJIB menggunakan background solid **amber-400** (`bg-amber-400`) dengan teks gelap/hitam (`text-gray-900 font-black` / `font-extrabold`) dan `shadow-xs`.
        - **DILARANG** menggunakan teks putih di atas background kuning/amber (`bg-amber-500 text-white` atau `bg-amber-400 text-white`) untuk nomor halaman aktif maupun menu active rows-per-page, karena kontras tidak ideal dan bertentangan dengan standar tombol utama LensMatch.
        - **Halaman Tidak Aktif / Navigasi**: Background transparan atau putih/dark slate dengan border subtle (`hover:border-amber-500 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-950/40`).
      - **Lokasi Template Utama**:
        - `resources/views/partials/public/pagination.blade.php` (komponen pagination kustom LensMatch dengan Showing Page, kontrol nomor halaman, dan dropdown Rows Per Page).
        - `resources/views/vendor/pagination/tailwind.blade.php` (template default Laravel Tailwind pagination).
      - **Halaman yang Telah Menerapkan Standar Ini**:
        - `resources/views/public/photographers/katalog.blade.php` (Katalog Fotografer)
        - `resources/views/public/forum/index.blade.php` (Forum Diskusi)
        - `resources/views/public/contests/index.blade.php` (Informasi Lomba)
        - `resources/views/public/elearning/katalog.blade.php` (E-Learning / Kursus)
        - Seluruh tabel Admin & Client yang menggunakan pagination Laravel.
   46. **Standar Custom Alpine.js Floating Dropdown (LensMatch Dropdown Standard) — WAJIB DI SELURUH PROYEK**:
       - **Larangan Mutlak Tag Native `<select>`**: Dilarang keras menggunakan tag HTML native `<select><option>` di seluruh sistem (publik, auth, fotografer, maupun admin), karena kontrol native OS memunculkan kotak biru persegi kaku tanpa styling.
       - **Struktur Standar UI**: Menggunakan tombol pemicu kustom Alpine.js (`x-data="{ open: false, selectedValue: '...' }"`) dengan border halus `rounded-xl`, latar putih bersih `bg-white`, ikon chevron SVG dinamis berotasi, dan floating menu melayang (`shadow-xl rounded-xl border border-gray-100 py-1`) dengan item hover amber lembut (`hover:bg-amber-50 hover:text-amber-600`).
       - **Halaman yang Telah Menerapkan**: `public/home.blade.php` (Hero Search Kategori), `public/auth/signup.blade.php` (Daftar Sebagai Role), `public/forum/index.blade.php` (Filter Kategori & Urutkan), `photographer/reports/index.blade.php` (Filter Tahun), dan seluruh form modal di sistem.

7. **Standarisasi Metric Stat Cards (Ringkasan Statistik Dashboard & Table Views)**:
   - DILARANG menaruh icon kecil di sudut kanan atas pada kartu ringkasan metrik statistik (*Metric Stat Cards*).
   - Tampilan kartu ringkasan metrik pada halaman fitur bertabel (Non-Dashboard) wajib bersih, ringkas, dan hanya terdiri dari 2 elemen (lihat **Rule #64**):
     1. Label teks di atas (`text-[11px] font-bold uppercase text-gray-500`)
     2. Angka nilai metrik di bawah (`text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight`)
   - Khusus pada halaman dashboard utama (`admin/dashboard` dan `photographer/dashboard`), baris ke-3 (sub-teks keterangan pendukung) tetap dipertahankan (`text-[11px] text-gray-400 font-medium`).

8. **Warna Teks Keterangan Subtitle Metric Stat Cards**:
   - Teks keterangan di bagian bawah kartu metrik statistik (*Metric Stat Cards*) wajib seragam menggunakan warna **Abu-Abu Netral** (`text-gray-400 dark:text-gray-500 font-medium text-[11px]`).
   - Seluruh teks angka, nominal, atau persentase yang berada di dalam baris keterangan subtitle **wajib bernuansa gelap/hitam netral** (`font-bold text-gray-700 dark:text-gray-300` atau `text-gray-800`), **DILARANG** menggunakan highlight warna orange/kuning (amber) agar tampilan tetap tenang, profesional, dan elegan.

9. **Larangan Simbol / Icon / Karakter Centang pada Teks Subtitle Card**:
   - Dilarang keras menaruh simbol karakter seperti centang (`✓`), emoji, atau icon karakter unicode apa pun di dalam teks keterangan (*subtitle*) kartu metrik statistik (*Metric Stat Cards*).
   - Teks keterangan wajib berupa teks murni yang bersih, profesional, dan ringkas tanpa ornamen simbolik.

10. **Standarisasi Tombol Kolom AKSI pada Seluruh Tabel Dashboard**:
   - DILARANG menggunakan icon / SVG / emoji / simbol panah di dalam tombol-tombol aksi pada kolom tabel `AKSI` di seluruh dashboard (Admin, Fotografer, atau role lainnya).
   - Seluruh tombol aksi pada tabel wajib hanya berupa teks bersih (*pure text*) yang jelas, ringkas, dan profesional (misalnya: `Lihat`, `Edit`, `Hapus`, `Cairkan Payout`, `Proses Refund`, `Review Karya`, `Verifikasi`, `Batal Verifikasi`, `Reset Password`, `Tampilkan`, `Sembunyikan`).

11. **Standarisasi Tampilan Tipe Akun (Role) di Seluruh Tabel & Sistem**:
   - **DILARANG** membungkus nama tipe akun/role ke dalam bentuk kartu/badge box/pill (tanpa background `bg-*`, tanpa border `border-*`, dan tanpa padding box `px-* py-*`).
   - Tipe Akun wajib ditampilkan sebagai **Teks Murni (*Pure Text*)** dengan pembeda warna teks (*font color*) yang spesifik dan konsisten untuk masing-masing role:
     1. **Super Admin**: Warna **Kuning Emas / Amber Brand** (`text-amber-600 dark:text-amber-400 font-extrabold text-xs`)
     2. **Staf Admin**: Warna **Biru Profesional** (`text-blue-600 dark:text-blue-400 font-bold text-xs`)
     3. **Fotografer**: Warna **Ungu Kreatif** (`text-purple-600 dark:text-purple-400 font-bold text-xs`)
     4. **Klien**: Warna **Hijau Emerald** (`text-emerald-600 dark:text-emerald-400 font-bold text-xs`)

12. **Standarisasi Desain Tombol Aksi Hapus (Delete Action Button)**:
   - Tombol Hapus pada seluruh tabel dan dashboard (Admin, Fotografer, Klien) wajib menggunakan standar styling:
     - **Classes**: `px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200/80 dark:border-rose-900/60 font-bold text-xs transition`
     - **Warna & Latar**: Background merah rose lembut (`bg-rose-50`), teks merah rose tajam (`text-rose-600`), serta outline border merah tipis (`border border-rose-200/80`).
     - **Bentuk & Ukuran**: `rounded-lg` dengan padding `px-3 py-1.5` dan font `text-xs font-bold` (murni teks tanpa icon).

13. **Standarisasi Desain Tombol Aksi Edit (Edit Action Button)**:
    - Tombol Edit pada seluruh tabel dan dashboard (Admin, Fotografer, Klien, atau role mana pun di masa mendatang) wajib selalu menggunakan standar UI ini agar selaras dan proporsional 100% dengan tombol Hapus (Rule #12):
      - **Tailwind Classes**: `px-3 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200/80 dark:border-amber-900/60 font-bold text-xs transition min-w-[60px] text-center`
      - **Warna & Latar**: Background kuning amber lembut (`bg-amber-50`), teks kuning amber tajam (`text-amber-700`), serta outline border kuning tipis (`border border-amber-200/80`).
      - **Bentuk & Proporsi**: Menggunakan `rounded-lg`, padding seragam `px-3 py-1.5`, ukuran font `text-xs font-bold`, dan lebar minimum seimbang `min-w-[60px]` (murni teks tanpa icon).

14. **Standarisasi Tampilan Status Transaksi & Escrow/Payout di Seluruh Halaman**:
    - **DILARANG** menggunakan badge/pill box (tanpa background `bg-*`, tanpa border `border-*`, tanpa padding box `px-* py-*`, dan tanpa icon/bullet dot `w-1.5 h-1.5 rounded-full`).
    - Status wajib ditampilkan sebagai **Teks Murni Berwarna (*Pure Colored Text*)** dengan teks label yang seragam di seluruh dashboard & halaman sistem:
      1. **Payout Dicairkan**: Teks Hijau Emerald (`text-xs font-bold text-emerald-600 dark:text-emerald-400`)
      2. **Siap Dicairkan**: Teks Biru Profesional (`text-xs font-bold text-blue-600 dark:text-blue-400`)
      3. **Holding Escrow**: Teks Kuning Amber (`text-xs font-bold text-amber-600 dark:text-amber-400`)
      4. **Menunggu Pembayaran**: Teks Kuning Amber (`text-xs font-bold text-amber-600 dark:text-amber-400`)
      5. **Refund**: Teks Merah Rose (`text-xs font-bold text-rose-600 dark:text-rose-400`)
      6. **Dibatalkan**: Teks Abu-Abu Netral (`text-xs font-bold text-gray-400 dark:text-gray-500`)

15. **Standarisasi Tata Letak Toolbar Tabel (Filter di Kiri, Pencarian di Kanan)**:
    - Pada seluruh tabel dashboard di setiap modul (Admin, Fotografer, Klien), tata letak *Toolbar* wajib seragam dan konsisten:
      - **Sisi Kiri (*Left Section*)**: Tombol Filter Status / Tab Kategori / Opsi Penyaringan data.
      - **Sisi Kanan (*Right Section*)**: Boks Input Pencarian (*Search Bar*) dengan ikon pencarian / tombol aksi cari.
    - Dilarang menempatkan search bar di sisi kiri jika terdapat tombol filter di sisi kanan agar alur pemindaian pengguna (*visual reading flow*) selalu konsisten.

16. **Standarisasi Komponen Filter Dropdown & Opsi Urutan (Alpine.js Custom Floating Dropdown)**:
    - **DILARANG** menggunakan filter bergaya tombol pil/tab horizontal berjejer (`Semua`, `Pending`, `5 Bintang`) di seluruh tabel dashboard karena memakan ruang dan membatasi opsi penyaringan.
    - Seluruh tabel dashboard (Admin, Fotografer, Klien) WAJIB menggunakan **Alpine.js Custom Floating Dropdown** (seperti pada filter Beranda):
      - **Posisi Tata Letak**: Ditempatkan di **Sisi Kiri (*Left Section*)** toolbar tabel, berdampingan secara rapi dengan Search Bar di **Sisi Kanan (*Right Section*)**.
      - **Dropdown Filter Utama**: Menyaring berdasarkan status/kategori/tipe akun/rating/tingkat kesulitan sesuai konteks modul.
      - **Dropdown Urutan Data (*Sorting*)**: Wajib menyediakan opsi urutan yang beragam dan relevan di setiap modul:
        - `Urutan: Terbaru (Default)` / `Urutan: Terlama`
        - `Nominal Tertinggi` / `Nominal Terendah` (Modul Transaksi & Laporan Keuangan)
        - `Nama A-Z` / `Nama Z-A` (Modul Pengguna, Fotografer, Kategori, Admin)
        - `Rating Tertinggi` / `Rating Terendah` (Modul Ulasan & Testimoni)
        - `Paling Banyak Dilihat` / `Paling Banyak Komentar` / `Hadiah Terbesar` (Modul E-Learning, Forum, Lomba Foto)
      - **Interaktivitas (*Auto-Submit*)**: Setiap klik pada item dropdown otomatis memicu submit form secara instan (`@click="selectedId = '...'; selectedName = '...'; open = false; $nextTick(() => $el.closest('form').submit())"`).

17. **Standarisasi Filter & Pencarian Live Real-Time Tanpa Reload Halaman (Live Dynamic Table)**:
    - **DILARANG** menggunakan tombol 'Cari' terpisah di samping search bar dashboard. Pencarian harus bersifat real-time otomatis saat pengguna mengetik dengan *debounce* halus (300ms).
    - Ketika opsi filter dropdown dipilih atau teks pencarian diketik, sistem **DILARANG melakukan reload/refresh halaman penuh (*full page reload*)**.
    - Pembaruan data tabel wajib menggunakan mekanisme **Hidden Refresh (*Asynchronous Dynamic DOM Replacement*)**:
      - Data tabel diperbarui seketika secara dinamis (*SPA feel*) dengan efek transisi kelembutan (*smooth opacity transition*).
      - Status URL di address bar browser otomatis diperbarui via `history.pushState` sehingga tautan tetap dapat dibagikan atau di-bookmark tanpa mengorbankan pengalaman real-time.

18. **Standarisasi Rows Per Page & Pagination Asinkron Tanpa Reload Halaman**:
    - Seluruh perubahan **Rows Per Page (10, 20, 50, 100)** maupun perpindahan halaman pagination di **SELURUH PROYEK** (Katalog Fotografer, E-Learning, Forum, Lomba, dan Dashboard) **DILARANG** melakukan reload/refresh halaman browser secara terlihat.
    - Sistem WAJIB menggunakan engine **`window.loadAjaxPage(url)`** (*Asynchronous Dynamic Content Replacement*):
      - Konten kartu / tabel berganti secara seketika (*real-time feel*) dengan efek transisi kelembutan (*fade opacity*).
      - Status URL di address bar browser otomatis diperbarui via `history.pushState` sehingga tautan halaman dan ukuran baris per halaman tetap dapat dibagikan atau di-bookmark dengan presisi.

19. **Standarisasi Terminologi Fotografer & Arsitektur Menu Profil Dashboard**:
    - **Dilarang Menggunakan Istilah 'Studio' untuk Role Fotografer**:
      - Sistem LensMatch menggunakan peran **Fotografer** (bukan Studio), karena fotografer mencakup freelancer individu, fotografer lepas, maupun tim. Seluruh teks label, badge, kartu metrik, header, dan laporan keuangan wajib menggunakan istilah **'Fotografer'** (misal: *Dashboard Fotografer*, *Mitra Fotografer*, *Omzet Bersih Fotografer*, *Alamat / Domisili Kota*, *Deskripsi Bio Fotografer*).
    - **Akses Menu Profil Fotografer Tetap di Dashboard**:
      - Menu **'Profil & Bio'** pada sidebar dashboard fotografer wajib mengarahkan ke halaman profil internal dashboard (`@extends('layouts.photographer')`), bukan halaman publik beranda.
    - **Penyederhanaan User Dropdown Navbar Header**:
      - User dropdown di navbar header untuk role Fotografer hanya menyisakan **2 menu**: **Dashboard Fotografer** dan **Keluar / Logout** (seluruh modul operasional lainnya berada di sidebar dashboard fotografer).

### Rule #20: Standarisasi Simbol Wajib Isi (Required Field Asterisk) Merah
- **Aturan**: Seluruh form input yang memiliki field wajib diisi (*required field*) **WAJIB** menyertakan tanda bintang (\`*\`) dengan format pewarnaan merah/rose eksplisit: \`<span class="text-rose-500">*</span>\` atau \`<span class="text-red-500">*</span>\`.
- **Konsistensi Tampilan**: Tanda bintang tidak boleh menggunakan teks polos standar bawaan teks label abu/hitam agar pengguna langsung mengetahui secara intuitif bahwa field tersebut wajib diisi sebelum menyimpan form.
- **Cakupan**: Berlaku di semua modul (Profil Fotografer/Klien/Admin, Kelola Kategori, E-Learning, Event Lomba, Forum Diskusi, Booking & Kontrak).

### Rule #21: Standarisasi Manajemen Upload Media & Batas Ukuran File (Avatar, Sampul, Portofolio & Galeri Klien)
- **Foto Profil / Avatar Pengguna (Semua Role)**: Batas maksimal **10 MB** (`max:10240`), mendukung format JPG, PNG, WEBP langsung dari penyimpanan perangkat.
- **Foto Sampul / Banner Profil Fotografer**: Batas maksimal **20 MB** (`max:20480`), format landscape rasio 16:9 langsung dari perangkat untuk menjaga ketajaman visual cover banner katalog.
- **Portofolio Karya & Pengiriman Galeri Foto Klien**: Batas maksimal **200 MB** (`max:204800`) atau melalui tautan penyimpanan cloud (Google Drive, Dropbox, OneDrive) untuk menampung album hasil jepretan resolusi tinggi (*High-Res Export*) secara leluasa.
- **Pure Device Upload**: Antarmuka formulir profil mitra fotografer dan klien mengutamakan upload murni dari perangkat (*Native File Input* dengan instant preview), meniadakan input URL teks kaku agar data media terpusat aman di storage sistem.

### Rule #22: Kewajiban Diskusi & Persetujuan Peletakan Icon di UI (Strict Icon Constraint)
- **Larangan Penambahan Icon Tanpa Konfirmasi**: AI dilarang keras menambahkan icon / SVG / emoji / simbol visual secara sembarangan atau sepihak pada elemen UI apa pun (judul, label, form, header, badge, tab, keterangan, dsb).
- **Wajib Diskusi Terlebih Dahulu**: Setiap kali ingin mengimplementasikan atau menambahkan icon di UI, AI **WAJIB mendiskusikan dan meminta persetujuan eksplisit kepada User terlebih dahulu** mengenai apakah icon tersebut diperlukan dan di mana posisi peletakannya.
- **Default Pure Text**: Jika belum ada instruksi atau persetujuan peletakan icon, tampilan UI wajib mengutamakan teks murni (*pure text*) yang bersih, ringkas, dan profesional.

### Rule #47: Kewajiban Membaca & Mematuhi Seluruh Aturan README Sebelum Memulai Implementasi (Mandatory Pre-Implementation Scan)
- **Kewajiban Membaca & Memahami**: Sebelum membuat atau mengubah kode/UI apa pun di seluruh proyek ini, AI **WAJIB membaca dan memindai file `README.md` terlebih dahulu** guna memahami standar arsitektur, layout, tipografi, palet warna, konsistensi navbar, komponen tombol, dan seluruh batasan desain yang berlaku.
- **Konsistensi Lintas Halaman**: Seluruh halaman di sistem (termasuk halaman checkout, transaksi, e-learning, katalog, maupun dashboard) **WAJIB menjaga konsistensi 100%** dengan halaman-halaman sebelumnya:
  - Menggunakan navbar standar aplikasi (`partials/public/navbar.blade.php`).
  - Menggunakan tombol primer standar (`bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold rounded-xl shadow-xs`).
  - DILARANG menambahkan icon/panah tanpa persetujuan eksplisit (Rule #22).
  - DILARANG menambahkan card/frame atau elemen asing yang merusak hierarki visual.
- **Larangan Modifikasi Tanpa Acuan**: Dilarang keras melakukan implementasi sepihak tanpa merujuk pada ketentuan yang telah didokumentasikan di `README.md`.

### Rule #48: Standarisasi Tipe Materi, UI Template, dan Logika Penyelesaian E-Learning
- **3 Standarisasi Format Media Utama & Template UI Pembelajaran**:
  1. **Video (`tipe: video`)**:
     - **Tampilan UI**: Frame pemutar video di panggung atas (`bg-black`, mendukung MP4 HTML5 & YouTube API Embed) + Judul, Tab Deskripsi (Word-style rich content), Lampiran, & Catatan di bawahnya.
     - **Mekanisme Selesai**: **Otomatis (*Auto-Complete*)** ketika video diputar hingga selesai (mencapai 100%). **TIDAK ADA tombol manual 'Tandai Selesai'**.
     - **Badge**: `Video {X} Menit` (Sidebar: `Video {X} mnt`).
  2. **Audio (`tipe: audio`)**:
     - **Tampilan UI**: Pemutar audio ringkas rata kiri (*left-aligned*) dengan latar putih bersih (`bg-white dark:bg-gray-900`) dan jarak atas-bawah proporsional (`py-8 sm:py-12 my-2`). Menggunakan bar kontroler sederhana (tombol Play/Pause amber, progress scrubber interaktif, timestamp real-time, dan tombol volume/mute).
     - **Mekanisme Selesai**: **Otomatis (*Auto-Complete*)** ketika audio diputar hingga selesai (mencapai 100%). **TIDAK ADA tombol manual 'Tandai Selesai'**.
     - **Badge**: `Audio {X} Menit` (Sidebar: `Audio {X} mnt`).
  3. **Materi Teks / Dokumen (`tipe: reading`)**:
     - **Tampilan UI**: Langsung menampilkan area bacaan dokumen kaya ala Word pada tab Deskripsi (tanpa kotak pemutar media di atasnya). Gambar/foto studi kasus disisipkan secara alami di dalam isi teks artikel.
     - **Mekanisme Selesai**: Memiliki tombol manual **'Tandai Selesai'** di navigasi bawah yang memunculkan pop-up modal konfirmasi validasi (setelah dikonfirmasi, status selesai tersimpan permanen dan tidak dapat di-revert/dibatalkan).
     - **Badge**: `Materi Teks` (Sidebar: `Materi Teks`).
- **Penyelarasan Navigasi Bawah**: Tautan tombol **`< Sebelumnya`** wajib sejajar lurus secara vertikal (*left-aligned*) dengan margin teks konten di atasnya.
- **Format Badge Seragam**: Seluruh badge tipe materi wajib seragam menggunakan badge amber (`bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200/80 dark:border-amber-800/60 rounded-lg`).

### Rule #49: Fokus Ketat Terhadap Instruksi User (Strict Scope & Zero Unrequested Side-Effects)
- **Prinsip Fokus 100% Terhadap Suruhan**: Setiap kali mengimplementasikan instruksi dari User, AI **WAJIB fokus murni hanya pada aspek yang diperintahkan secara spesifik**.
- **Larangan Keras Memodifikasi Elemen di Luar Perintah**:
  - **Instruksi Posisi/Layout**: Jika User meminta memindahkan posisi (misalnya memindahkan tombol ke samping judul), AI **DILARANG KERAS** mengubah warna teks, warna background, jenis icon, atau styling lainnya yang tidak diperintahkan. Cukup ubah posisi/tata letaknya saja dengan mempertahankan warna dan atribut aslinya.
  - **Instruksi Teks/Label**: Jika User meminta mengubah teks, jangan mengubah ukuran atau styling warna komponen.
  - **Instruksi Warna**: Jika User meminta mengubah warna, jangan mengubah tata letak, padding, atau margin yang sudah ada.
- **Kepatuhan Mutlak**: Seluruh styling dan properti yang tidak tercantum dalam suruhan User **wajib dipertahankan persis seperti aslinya** tanpa improvisasi atau kreasi sepihak.

### Rule #50: Standarisasi Keselarasan Margin/Padding Horizontal Navbar Khusus (Container Width Consistency)
- **Keselarasan Garis Batas Kiri & Kanan**: Pada seluruh halaman bertipe non-fullscreen yang memiliki batasan container standar (seperti halaman checkout dengan `max-w-7xl mx-auto px-4 sm:px-6 lg:px-8` dan footer standar), **Navbar Khusus (misal: Navbar Ruang Belajar / E-Learning Top Bar) WAJIB mengikuti container horizontal yang sama (`max-w-7xl mx-auto px-4 sm:px-6 lg:px-8`)**, BUKAN menggunakan `w-full px-4 sm:px-8`.
- **Tujuan Presisi Visual**: Memastikan elemen sisi kiri (seperti logo LensMatch dan tombol kembali) serta elemen sisi kanan (seperti lonceng notifikasi dan avatar pengguna) selalu sejajar lurus vertikal (*aligned*) secara presisi dengan batas kiri-kanan konten halaman dan footer di bawahnya.
- **Halaman Ruang Belajar Kelas (`public.elearning.show`)**:
  - **Saat Silabus Terbuka (`sidebarOpen: true`)**: Navbar menggunakan lebar penuh `w-full px-4 sm:px-8` yang membentang dari silabus hingga ujung kanan panggung pembelajaran.
  - **Saat Silabus Ditutup (`sidebarOpen: false`)**: Sidebar menutup dengan animasi transisi halus (`transition-all duration-300 ease-in-out`), dan container Navbar secara reaktif ikut bertransisi halus menyesuaikan ukuran batas container konten pembelajaran (`max-w-[1380px] mx-auto px-4 sm:px-6 lg:px-8`), sehingga logo di sisi kiri dan profil pengguna di sisi kanan tetap sejajar lurus secara vertikal dengan batas konten materi di bawahnya.
  - **Tombol Buka Silabus**: Tombol mengambang (*floating button*) di kiri bawah muncul dengan animasi halus (*fade & slide transition*) untuk membuka kembali silabus.

### Rule #51: Standarisasi Copywriting Bersih, Proporsional & Anti-AI Slop (No Buzzwords / Fluff)
- **Copywriting To-the-Point**: Seluruh teks judul, label, deskripsi, dan tombol pada antarmuka pengguna (UI) wajib menggunakan bahasa Indonesia yang **ringkas, lugas, padat makna (*to-the-point*), dan profesional** sebagaimana standar desain modern Dribbble.
- **Larangan Keras Bahasa Marketing Berlebihan / Embel-embel AI Slop**:
  - **DILARANG** menggunakan kalimat pembuka/penutup panjang bertele-tele (misal: *"Selesaikan pembayaran sekali untuk membuka akses seumur hidup ke seluruh modul pembelajaran..."*, *"Konfirmasi Pembayaran Akses Penuh"*, *"100% Escrow Aman Terpercaya"*). Cukup gunakan label singkat seperti *"Checkout"*, *"Item Kursus"*, *"Metode Pembayaran"*, *"Ringkasan Pesanan"*, dan *"Bayar Sekarang"*.
  - **DILARANG** menambahkan kartu/boks promosi atau janji berlebihan yang tidak diminta oleh pengguna.
- **Proporsionalitas Tipografi (*Typography Scale*)**:
  - Ukuran heading pada halaman formulir/transaksi wajib proporsional dengan konten di sekitarnya (`text-xl font-bold` hingga `text-2xl font-bold`), bukan ukuran raksasa (`text-4xl`/`text-5xl`) yang membuat UI terasa tidak seimbang dan memakan ruang kosong secara tidak perlu.

### Rule #52: Standarisasi Navbar Khusus Ruang Chat Klien (`/chat` Dedicated Top Bar)
- **Fokus & Bebas Distraksi**: Ketika pengguna membuka ruang obrolan chat klien (`/chat` atau `client.chat*`), navbar publik generik digantikan dengan **Navbar Khusus (*Dedicated Chat Top Bar*)** berukuran tinggi `h-16` dan lebar penuh `w-full px-4 sm:px-8`.
- **Komposisi Elemen Navbar Khusus Chat**:
  1. **Sisi Kiri (*Left Section*)**: Logo LensMatch transparan + pembatas tipis `|` + tautan navigasi dinamis **'Kembali'** (`window.history.back()` / `url()->previous()`) dengan ikon Vektor SVG panah kiri tipis murni (`<svg><path d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>`) yang otomatis mengembalikan pengguna ke halaman asal (misal dari Reservasi Saya, Katalog Fotografer, atau halaman lainnya).
  2. **Sisi Tengah (*Center Section*)**: Label judul ruang terfokus: **'Ruang Diskusi & Chat Fotografer'** (`text-xs font-bold text-gray-700 dark:text-gray-200 truncate`).
  3. **Sisi Kanan (*Right Section*)**: Lonceng notifikasi real-time in-app + avatar profil pengguna standar LensMatch (`<x-user-avatar>`).

### Rule #53: Standarisasi Teks Tombol Aksi Form Utama (Create = Tambah Data, Update = Perbaharui Data, Delete = Hapus Data)
- **Penyederhanaan Teks Tombol Formulir (Simple & Consistent Action Buttons)**:
  - **Aksi Create / Buat / Simpan Baru**: Wajib menggunakan teks baku **`Tambah Data`** (dilarang menggunakan varian bertele-tele seperti *"Simpan Perubahan Profil"*, *"Publikasikan Materi"*, *"Simpan Layanan Baru"*, *"Simpan Paket"*, *"Luncurkan Event"*).
  - **Aksi Update / Edit / Ubah**: Wajib menggunakan teks baku **`Perbaharui Data`** (dilarang menggunakan varian panjang seperti *"Simpan Perubahan"*, *"Perbarui Kata Sandi"*, *"Simpan Perubahan Profil"*).
  - **Aksi Delete / Hapus**: Wajib menggunakan teks baku **`Hapus Data`** (pada modal konfirmasi validasi) atau **`Hapus`** (pada tombol kolom aksi tabel ringkas).
- **Pengecualian Khusus (*Contextual Exceptions*)**:
  - Tombol dengan konteks transaksional atau interaksional unik yang tidak cocok dengan terminologi data (contoh: *"Bayar Sekarang"*, *"Checkout"*, *"Tandai Selesai"*, *"Kirim"*, *"Masuk"*, *"Daftar"*).

### Rule #54: Standarisasi Penerbitan Sertifikat Kelulusan E-Learning (Certificate of Completion System)
- **Mekanisme Penerbitan Otomatis (100% Course Completion)**:
  - Sertifikat kelulusan digital diterbitkan secara otomatis tepat saat pengguna telah menyelesaikan seluruh materi kurikulum (`progress = 100%`).
  - Menyediakan modal selebrasi kelulusan (*Celebration Modal*) interaktif yang memunculkan ucapan selamat dan tombol langsung **`Unduh Sertifikat Kelulusan (PDF)`**.
  - Menyediakan tombol unduh permanen di bagian atas silabus ruang belajar saat progres mencapai 100%.
- **Komposisi Desain Sertifikat Standar A4 Landscape**:
  - Menggunakan tata letak formal rasio A4 Landscape yang didukung CSS cetak presisi (`@media print` bebas header/footer browser).
  - Dilengkapi ornamen sudut pita gelombang geometris (*Geometric Ribbon Waves*), bingkai ganda elegan (*Double Inset Borders*), Tipografi Judul (*Cinzel & Playfair Display*), Medali Stempel Emas Otentik (*Official Verified Golden Seal*), serta Tanda Tangan Digital Ganda (*Lead Photography Instructor & Head of LensMatch Academy*).
  - Dilengkapi Nomor Kredensial Unik (`LM-CERT-XXXX-TAHUN`), tanggal kelulusan riil, dan tautan verifikasi keaslian.

### Rule #55: Standarisasi Tampilan 3 Indikator Status Event Lomba Foto (buka, tutup, selesai)
- **Kepatuhan Mutlak 3 Status Lomba**:
  1. **Status `buka` (Pendaftaran Terbuka)**:
     - **Wajib Tampil**: Formulir pendaftaran karya foto bagi peserta (fitur unggah foto langsung dari perangkat + *live preview* kotak).
     - **DILARANG KERAS MENAMPILKAN**: Section panggung juara / foto pemenang.
     - **DILARANG KERAS MENAMPILKAN**: Section galeri karya peserta (*community submissions showcase*).
  2. **Status `tutup` (Pendaftaran Ditutup / Tahap Penjurian)**:
     - **Wajib Tampil**: Boks informasi netral bahwa pendaftaran telah ditutup dan karya sedang dalam tahap kurasi/penjurian oleh dewan juri.
     - **DILARANG KERAS MENAMPILKAN**: Section panggung juara / foto pemenang.
     - **DILARANG KERAS MENAMPILKAN**: Section galeri karya peserta.
  3. **Status `selesai` (Event Telah Selesai & Ditetapkan Pemenang)**:
     - **HANYA PADA STATUS INI**: Section **Panggung Kehormatan (Juara 1, 2, 3)** ditampilkan.
     - **HANYA PADA STATUS INI**: Section **Galeri Karya Peserta Lainnya (*Community Submissions Showcase*)** ditampilkan.
- **Standar UI & Estetika**: Menjaga tata letak tetap simpel, proporsional (`space-y-6` / `space-y-8`), metrik 4-kolom ringkas, dan bebas dari ikon/ornamen berlebihan.

### Rule #56: Standarisasi Alur Kurasi Proposal Pengajuan Lomba Foto Admin (`/lomba-pengajuan/*` & `/admin/contests`)
- **Struktur Status Database**:
  - Kolom `status` pada tabel `photo_contests` resmi mendukung 5 status: `pending_review`, `buka`, `ditutup`, `selesai`, dan `ditolak`.
- **Alur Kerja & Antarmuka Admin (`/admin/contests`)**:
  - **Tombol Aksi Baris Tabel**: Menampilkan tombol teks murni **`Tinjau Proposal`** (`bg-amber-50 text-amber-700 rounded-xl font-bold text-xs`) untuk setiap pengajuan yang berstatus `pending_review`.
  - **Modal Review Proposal Lomba Terpadu**:
    1. **Identitas Pengaju**: Menampilkan avatar/inisial, nama, role pengaju (dengan pembeda warna role murni), email, dan waktu pengajuan.
    2. **Banner Visual**: Thumbnail cover yang dapat diklik untuk membuka foto resolusi penuh.
    3. **4 Metrik Ringkas**: Kategori lomba, total nominal hadiah, nama penyelenggara/komunitas, dan periode mulai-selesai.
    4. **Detail Konten**: Konsep deskripsi lomba dan syarat ketentuan teknis lengkap.
    5. **Aksi Keputusan Admin**:
       - **Setujui Lomba**: Tombol **`Simpan Data`** (`bg-amber-400 text-gray-900 font-extrabold`) mengubah status menjadi `buka` (resmi tayang di katalog publik `/lomba`) dan mengirimkan notifikasi in-app sukses ke akun pengaju.
       - **Tolak Pengajuan**: Tombol **`Tolak Pengajuan`** memunculkan textarea input catatan alasan penolakan/revisi (`catatan_admin`), mengubah status menjadi `ditolak`, dan mengirimkan notifikasi in-app ke akun pengaju.
- **Riwayat Pengajuan Publik (`/lomba-pengajuan/riwayat`)**:
  - Menampilkan kartu proposal dengan badge status terpadu (`Menunggu Verifikasi Admin`, `Disetujui & Tayang di Publik`, `Ditolak / Perlu Revisi`) serta boks catatan revisi admin jika ditolak.

---

### Rule #57: Standarisasi Alur Penyerahan Galeri Hasil Foto & Ulasan Klien (`/photographer/bookings`, `/client/galleries`, & `ReviewController`)
- **Alur Penyerahan Galeri oleh Fotografer (`/photographer/bookings`)**:
  - Fotografer mengunggah tautan Google Drive / Cloud Album resolusi tinggi dan catatan/petunjuk unduh melalui modal **Unggah Link Galeri Hasil Foto**.
  - Menyimpan URL ke `contract_bookings.hasil_foto_url` dan `catatan_fotografer`, serta mengubah status booking dan kontrak menjadi `completed`.
  - Sistem otomatis mengirimkan **Notifikasi In-App** ke Klien (`gallery_delivered`) dengan tautan langsung ke halaman galeri klien.
- **Halaman Galeri Hasil Foto Klien (`/client/galleries`)**:
  - Menggunakan layout kartu galeri modern: Info fotografer, nomor kontrak `#KTRK-XXXXX`, nama paket layanan, tanggal sesi pemotretan, boks tautan Google Drive, pesan catatan fotografer, dan tombol aksi utama **`Buka & Unduh Galeri di Google Drive`**.
  - **Integrasi Tombol Ulasan Langsung**: Jika pesanan belum diulas, menampilkan tombol **`Beri Ulasan & Rating Fotografer`** yang langsung memicu `@open-review-modal`. Jika sudah diulas, menampilkan rating bintang ⭐ beserta teks ulasan klien.
- **Standarisasi Modal Ulasan Klien (`partials/client/review-modal.blade.php`)**:
  - Pemilih rating 1-5 bintang interaktif, textarea ulasan pengalaman, dan tombol simpan baku **`Kirim Ulasan`** (`bg-amber-400 text-gray-900 font-extrabold`).
  - Sistem otomatis mengirimkan **Notifikasi In-App** ke Fotografer (`new_review`) saat ulasan baru tersimpan.

---

### Rule #58: Standarisasi Tampilan Konten & Elemen Aktif (Dilarang Garis Kanan / Garis Bawah Menempel & Eliminasi Divider Panjang)
- **Larangan Mutlak Divider Panjang & Garis Menempel**:
  - **DILARANG KERAS** menambahkan garis pemisah horizontal panjang yang membelah layar (`border-b border-gray-200`) di bawah deretan tab navigasi.
  - Untuk setiap elemen UI yang sedang dalam keadaan **Aktif / Terpilih**, **DILARANG** menggunakan border bawah kaku yang menempel pada container/divider (`border-b-2` dengan `border-gray-200`).
- **Standar Pengganti yang Wajib Dipatuhi**:
  1. **Active Text Underline murni**: Pada navigasi tab teks (misal: Transaksi Admin, Invoice Klien), penanda aktif hanya diberikan garis bawah (*underline*) pada teks aktifnya saja (`underline underline-offset-8 decoration-2 decoration-amber-500 dark:decoration-amber-400`), tanpa memberi garis bawah pada badge hitungan atau padding container, serta meniadakan garis divider panjang di bawahnya.
  2. **Soft Pill / Container Background Tint**: Untuk tab bergaya pill (misal: Pendaftar/Submisi Lomba, Topik/Laporan Forum), gunakan latar belakang lembut proporsional (`bg-amber-400 text-gray-900 font-extrabold rounded-xl shadow-xs`).
  3. **Text Weight & Color Clarity**: Teks aktif dibuat lebih tebal/pekat secara elegan (`font-black text-amber-600 dark:text-amber-400`).
  4. **No Colored Border Leaks**: Jangan menerapkan class border warna (misal `border-amber-500` atau `border-l-4`) yang merembes mewarnai garis pemisah (`divide-y`) bagian bawah atau kanan item.

---

### Rule #59: Standarisasi Global Modal / Pop-Up (Center Buttons, Larangan 3 Tombol Berderet Horizontal, Logika Tombol Close 'X', Judul Tanpa Icon & Center)
- **Judul Modal (Modal Title)**:
  - **Dilarang memasang icon pada judul modal** (termasuk icon bintang, icon peringatan, icon checkmark, atau ilustrasi icon validasi).
  - Teks judul modal dan deskripsinya wajib selalu berada di tengah (`text-center`).
- **Tombol Aksi Modal (Modal Action Buttons)**:
  - Seluruh tombol aksi pada modal (misal: *Batal*, *Kirim Ulasan*, *Simpan Perubahan*, *Hapus*, *Ya, Lanjutkan*) **WAJIB selalu berada di posisi tengah (`justify-center gap-3`)**, bukan di sudut kanan atau `justify-between`.
- **Dilarang Keras 3 Tombol Berderet Horizontal (Maksimal 2 Tombol Aksi Bawah)**:
  - Di seluruh sistem proyek ini, **TIDAK BOLEH ADA 3 tombol berderet secara horizontal** pada pop-up atau modal.
  - Jika sebuah modal memiliki lebih dari 2 pilihan aksi (misalnya kasus edit ulasan yang memiliki aksi *Batal*, *Hapus Ulasan*, dan *Simpan Perubahan*):
    - **Tombol "Batal" WAJIB dihilangkan** dari deretan tombol bawah.
    - **Wajib memberikan icon tombol 'X' di pojok kanan atas modal** sebagai pengganti fungsi Batal.
    - Tombol di bagian bawah modal hanya boleh berisi maksimal 2 tombol aksi utama yang tetap berada di posisi tengah (misal: `[Hapus Ulasan] [Simpan Perubahan]`).
- **Ketentuan Keberadaan Tombol Close 'X' di Pojok Kanan Atas**:
  - Jika modal hanya memiliki maksimal 2 tombol aksi dan **sudah terdapat tombol "Batal"**, maka **TIDAK BOLEH ada tombol 'X'** di pojok kanan atas modal.
  - Tombol 'X' di pojok kanan atas **wajib disediakan jika tombol "Batal" tidak ada / dihilangkan** (termasuk ketika tombol Batal dieliminasi untuk mencegah munculnya 3 tombol berderet horizontal).

---

### Rule #60: Standarisasi Interaksi Ulasan & Rating Galeri Klien (`/client/galleries`)
- **Tampilan Kartu Galeri**:
  - Tidak menampilkan kotak/kartu preview ulasan di bagian bawah kartu galeri agar UI tetap bersih dan rapi.
- **Indikator & Tombol Ulasan di Pojok Kanan Atas**:
  - **Belum Diulas**: Menampilkan teks link amber dengan underline standar berjarak **`Beri Ulasan`** (`text-amber-500 dark:text-amber-400 underline underline-offset-4 decoration-amber-400`).
  - **Sudah Diulas**: Menampilkan teks link abu-abu dengan underline standar berjarak **`Sudah Direview`** (`text-gray-400 dark:text-gray-500 hover:text-gray-600 underline underline-offset-4 decoration-gray-400/60`).
- **Interaksi Edit & Hapus Ulasan**:
  - Mengklik **`Sudah Direview`** membuka pop-up yang otomatis memuat rating dan komentar sebelumnya.
  - Klien dapat mengubah bintang/ulasan lalu memilih **`Simpan Perubahan`**, atau menghapus testimoni dengan tombol **`Hapus Ulasan`** (menjalankan soft-delete dan mengembalikan status kartu menjadi `Beri Ulasan`).

---

### Rule #61: Standarisasi Hierarki Warna Sistem (Kuning Brand LensMatch untuk Aksi/Link/Download, Eliminasi Warna Orange, & Pemisahan Warna Status)
- **Eliminasi Warna Orange pada Elemen Aksi**:
  - Dilarang menggunakan warna orange (`orange-*`) atau amber tua yang menyerupai orange/cokelat kemerahan pada teks aksi atau link interaktif.
- **Warna Aksi Utama (Kuning Brand LensMatch)**:
  - **Tombol Solid**: Wajib menggunakan `bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold shadow-xs transition`.
  - **Teks Link Aksi / Redirect / Download (Text Underline)**:
    - Wajib menggunakan warna kuning cerah sistem murni (bukan amber tua/orange) dengan underline berjarak: `text-amber-400 dark:text-amber-400 font-extrabold underline underline-offset-4 decoration-amber-400 hover:text-amber-300 hover:decoration-amber-300 transition`.
    - Contoh penggunaan: tombol link *"Beri Ulasan"*, tautan download Google Drive, aksi *"Tandai dibaca"*, dan link navigasi aksi.
- **Warna Aksi Netral / Pasif**:
  - Teks aksi sekunder atau yang telah selesai wajib menggunakan warna abu-abu: `text-gray-400 dark:text-gray-500 hover:text-gray-600 underline underline-offset-4 decoration-gray-400/60 font-bold text-xs`.
  - Contoh: *"Sudah Direview"*.
  - Tombol Batal/Tutup: `border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold`.
- **Warna Status Fungsional (Non-Badge / Murni Teks)**:
  - **Selesai / Lunas**: `text-emerald-600 dark:text-emerald-400 font-bold`
  - **Menunggu / Pending**: `text-amber-500 dark:text-amber-400 font-bold`
  - **Batal / Gagal / Ditolak**: `text-rose-600 dark:text-rose-400 font-bold`
  - **Draft / Netral**: `text-gray-500 dark:text-gray-400 font-bold`
- **Warna Aksi Destruktif**:
  - Khusus tombol hapus/batal permanen menggunakan aksen rose: `border border-rose-200 bg-rose-50 text-rose-600 hover:bg-rose-100 font-bold`.

---

### Rule #62: Standarisasi Breadcrumb Global (Tanpa Penanda / Highlight Warna Aktif)
- **Larangan Warna Aktif pada Breadcrumb**:
  - Di seluruh sistem proyek ini, **dilarang memberikan warna aktif/highlight** (seperti warna kuning, amber, orange, atau warna aksen lainnya) pada item breadcrumb yang sedang aktif/halaman saat ini.
- **Format Pewarnaan Standar Breadcrumb**:
  - Seluruh elemen teks breadcrumb, termasuk pemisah (`/`) dan item terakhir/aktif, wajib menggunakan warna netral abu-abu seragam: `text-gray-400 dark:text-gray-500` (atau `text-gray-400 font-bold`).
  - Tautan navigasi breadcrumb (link yang bisa diklik) hanya diberi efek interaktif saat di-hover (`hover:text-amber-500 transition`), sedangkan teks halaman aktif tidak memiliki warna pembeda tersendiri (`<span>Halaman Aktif</span>` tanpa kelas warna khusus).

---

### Rule #63: Standarisasi Alur Login & Redirection Berdasarkan Role Pengguna (Default Redirection & Intended URL Standard)
- **1. Standar Redirection Login Default (Normal Flow / Tanpa Intended URL)**:
  - **Klien**: WAJIB diarahkan kembali ke **Beranda / Halaman Utama (`/`)** dengan toast/alert sukses. DILARANG mengarahkan klien secara default ke halaman reservasi (`/client/bookings`) atau dashboard fotografer/admin karena klien adalah pengguna publik marketplace.
  - **Fotografer**: WAJIB diarahkan ke **Dashboard Fotografer (`/photographer/dashboard`)**.
  - **Admin / Super Admin**: WAJIB diarahkan ke **Dashboard Admin (`/admin/dashboard`)**.
- **2. Standar Intended URL Redirection (Login via Modal Validasi / Halaman Terproteksi)**:
  - Ketika pengguna role apapun diminta login saat mengakses halaman atau aksi terproteksi (seperti materi E-Learning, Forum Diskusi, atau Lomba Foto), tombol login wajib menyertakan parameter `?redirect={targetUrl}`.
  - Setelah autentikasi berhasil, sistem **WAJIB memvalidasi kesesuaian hak akses role**:
    - Jika role berhak (misal Klien/Fotografer mengakses E-learning atau Klien mengakses form booking), pengguna dikembalikan secara presisi ke intended URL tersebut.
    - Jika role tidak berhak (misal Fotografer/Admin mencoba membuka form booking klien `/booking/create`), pengguna dialihkan ke dashboard masing-masing.
- **3. Kebersihan Residu Session (`session('url.intended')`)**:
  - Halaman form login (`/login`) dan registrasi (`/register`) WAJIB membersihkan residu session lama (`session()->forget('url.intended')`) ketika diakses secara langsung tanpa query parameter `?redirect=`.
  - Input form tersembunyi (`<input type="hidden" name="redirect">`) HANYA dirender jika request memuat parameter eksplisit `request()->filled('redirect')`, guna mencegah login biasa terbajak oleh riwayat session sebelumnya.
- **4. Penyelarasan Rute Universal `/dashboard` dan `RoleMiddleware`**:
  - Rute universal `/dashboard` dan fallback hak akses pada `RoleMiddleware` untuk role Klien wajib selalu mengarahkan ke Beranda (`/`), bukan ke `/client/bookings`.

---

### Rule #64: Standarisasi KPI Metric Cards di Atas Tabel Fitur & Dashboard Utama (Ukuran Font Seragam `text-xs` & Eliminasi Teks Ketiga)
- **1. Struktur Kartu Metrik (2 Baris Murni & Ukuran Seragam `text-xs`)**:
  - Pada seluruh halaman fitur yang memiliki tabel di bawahnya (Admin Staf, Kategori, Lomba Foto, Submisi Lomba, Peserta Lomba, E-Learning, Forum, Mitra Fotografer, Log Aktivitas, Ulasan, Transaksi, Pengguna Klien, Kalender Fotografer, Portofolio Fotografer, Tanya Jawab Fotografer, Laporan Fotografer, Layanan Fotografer, Reservasi Klien, Tagihan Klien), halaman Dashboard Utama (`admin/dashboard.blade.php` dan `photographer/dashboard.blade.php`), serta kartu statistik akun pada profil pengguna publik (`public/users/show.blade.php`):
  - Kartu ringkasan metrik (KPI Cards) **HANYA terdiri dari 2 elemen teks murni dengan ukuran font yang SERAGAM yaitu `text-xs`** (dibedakan murni lewat ketebalan dan warna kontras):
    1. **Label Judul Metrik**: `<span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Judul Metrik</span>`
    2. **Angka Nilai Metrik**: `<h2/div/p class="text-xs font-black text-gray-900 dark:text-white tracking-tight">Nilai Data</h2/div/p>`
  - **DILARANG** memperbesar angka nilai metrik dengan kelas berukuran lebih besar (`text-lg sm:text-xl`, `text-2xl`, `text-3xl`, `text-base`) maupun mengecilkan label (`text-[11px]`, `text-[10px]`), agar tidak tampil mencolok sebelah (*anti-oversized metric slop*).
  - **DILARANG** menambahkan baris teks ketiga deskriptif redundan (`<p class="text-[11px] text-gray-400 ...">...</p>` seperti *"Terdaftar di database"*) di atas kartu KPI tabel fitur.
- **2. Penyeragaman Rangkuman Statistik Grafik Dashboard**:
  - Kotak rangkuman statistik pendukung di bawah grafik analitik pada `admin/dashboard.blade.php` dan `photographer/dashboard.blade.php`, serta kartu ringkasan kesiapan tayang pada `admin/photographers/show.blade.php`, turut menyeragamkan ukuran label dan angkanya ke `text-xs`.
  - Pengecualian ukuran komersial/marketing tetap berlaku untuk angka statistik hero landing publik (`public/home.blade.php`, `public/elearning/index.blade.php`) dan nominal harga paket komersial pada kartu katalog/profil publik.

---

### Rule #66: Standarisasi Tampilan Halaman Detail Profil Fotografer (Eclips Doctor Profile Inspired, Full-Width Wrapper & Anti-AI Slop)
- **1. Konsep Lebar Card Pembungkus (Sejajar Penuh dengan Konten & Navbar)**:
  - Card pembungkus profil fotografer (`/fotografer/{id}`) **WAJIB memenuhi batas kanan-kiri container standar LensMatch**: **`w-full` di dalam `max-w-7xl mx-auto px-4 sm:px-6 lg:px-8`**.
  - Garis batas luar tepi kiri dan kanan kartu profil atas **wajib sejajar persis (*aligned*)** dengan baris navigasi tab, kartu-kartu grid layanan/portofolio di bawahnya, serta logo dan menu navbar di atasnya.
- **2. Tata Letak Internal yang Lapang, Tanpa Garis Divider Antar Seksi & Bersih**:
  - **Bebas Garis Divider Horizontal Antar Seksi**: Seksi profil atas dan seksi paket layanan di bawahnya menyatu bersih secara mulus tanpa garis batas bawah (*border-b*) yang memotong layar.
  - Menggunakan padding internal yang lega: `p-6 sm:p-8 lg:p-10 rounded-3xl`.
  - Tata letak dua kolom desktop (`flex flex-col lg:flex-row items-center lg:items-start gap-8 lg:gap-10`):
    - **Kolom Kiri (Foto Potret)**: Foto potret bersih (`w-56 sm:w-64 lg:w-72 aspect-[3/4] rounded-2xl overflow-hidden bg-sky-50 dark:bg-gray-750 shrink-0 border border-gray-100 dark:border-gray-700/80 shadow-xs`). **DILARANG KERAS MENEMPELKAN BADGE DI ATAS GAMBAR/FOTO**.
    - **Kolom Kanan (Konten & Detail)**:
      - Nama Fotografer dan Spesialisasi Kategori secara langsung dan bersih (tanpa teks redundan semacam *"Mitra Fotografer LensMatch"*).
      - Garis Dashed, Grid 2-Kolom (Lokasi & Tarif), Garis Dashed, Baris Statistik 4-Boks, hingga Tombol Aksi di bagian bawah.
- **3. Larangan Highlight Kuning Raksasa pada Teks Harga (Sensible & Proportional Pricing)**:
  - **DILARANG KERAS** mewarnai teks harga dengan kuning/amber terang berukuran raksasa (*AI slop giant yellow price*).
  - Teks harga wajib menggunakan ukuran yang wajar dan proporsional: **`text-base sm:text-lg font-bold text-gray-900 dark:text-white`** yang tersusun rapi berdampingan dengan blok Lokasi & Basis Studio dalam grid 2-kolom seimbang.
- **4. Larangan Tombol Melebar Mengikuti Panjang Card (Content-Based Button Width)**:
  - **DILARANG** memaksa tombol aksi melebar memenuhi seluruh panjang card pembungkus (`w-full` / `flex-1` di desktop melintang 1200px khas AI).
  - Tombol aksi wajib menggunakan lebar alami berdasarkan isi konten (*content-based width* / `w-auto px-8 py-3.5` untuk primer dan `px-6 py-3.5` untuk sekunder) yang tersusun rapi di bawah kartu statistik.
- **5. Baris Statistik 4 Kolom (100% Data Riil Database & Anti-AI Slop - Rule #64 Compliant)**:
  - Terdiri dari 4 boks kartu berlatar lembut (`bg-gray-50 dark:bg-gray-750/70 p-3.5 sm:p-4 rounded-xl border border-gray-100 dark:border-gray-700/60`):
    1. **Pengalaman**: `5+ Tahun`.
    2. **Portofolio**: Total portofolio karya riil di database (`{{ $photographer->portfolios->count() }} Karya`). **TIDAK ADA ID FIKTIF #FTR-0000X**.
    3. **Bergabung**: Tanggal bergabung riil (`d M Y`).
    4. **Rating & Ulasan**: Bintang amber, rating rata-rata, dan jumlah ulasan klien (`★ 5.0 (X)`).
- **6. Penyelarasan Tab Detail & Price List (Clean Text Pills)**:
  - Kontainer seluruh tab fungsional di bawahnya (Paket Layanan & Price List, Galeri Portofolio, Kalender Ketersediaan, dan Ulasan Klien) menggunakan kontainer standar: **`max-w-7xl mx-auto px-4 sm:px-6 lg:px-8`**.
  - Tombol navigasi tab berbentuk pill teks bersih bebas dari ornamen SVG berulang (*no icon clutter*).

---

### Rule #67: Standarisasi Tampilan Kalender & Larangan Mutlak Bulatan Status / Dot Badge (Anti-AI Slop)
- **1. Warna Netral Kartu Sel Tanggal (Latar Putih/Abu-Abu)**:
  - Seluruh kartu tanggal pada kalender wajib menggunakan warna netral standar: latar putih (`bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700/80`) untuk tanggal aktif dan abu-abu (`bg-gray-50 dark:bg-gray-850/50`) untuk tanggal lampau.
  - **DILARANG KERAS** mewarnai seluruh kartu kotak tanggal dengan warna hijau, biru, atau merah menyeluruh.
- **2. Pembeda Status Murni dari Warna Teks (Pure Colored Text)**:
  - Indikator ketersediaan tanggal murni berasal dari pembeda **warna teks status**:
    - **Tersedia**: `text-emerald-600 dark:text-emerald-400 font-bold`
    - **Terisi Sesi**: `text-blue-600 dark:text-blue-400 font-bold`
    - **Libur / Blokir**: `text-rose-600 dark:text-rose-400 font-bold`
    - **Lewat / Lampau**: `text-gray-300 dark:text-gray-600 font-medium`
  - Angka tanggal tetap menggunakan warna teks gelap/terang netral (`text-gray-900 dark:text-white`).
- **3. Larangan Boks Legend Status Kalender**:
  - **DILARANG** menampilkan boks pill legend status (boks *"Tersedia"*, *"Terisi Sesi"*, *"Libur/Blokir"*) di atas kalender.
- **4. Larangan Mutlak Bulatan / Titik Status (Dot Badges) di Seluruh Sistem**:
  - **DILARANG KERAS** mengimplementasikan bulatan atau titik status (`<span class="w-* h-* rounded-full bg-*"></span>` atau `●` atau `&bull;`) di samping teks status pada kalender, tabel, badge, kartu, profil, atau elemen UI mana pun di seluruh sistem.
  - Bulatan status diklasifikasikan sebagai artefak *"AI Slop"* yang mengotori estetika UI. Seluruh indikator status wajib berupa teks murni yang bersih, rapi, dan profesional.

---

### Rule #68: Standarisasi Pewarnaan Teks pada Seluruh Tabel Sistem (Larangan Teks Berwarna-Warni di Kolom Non-Status / Anti-AI Slop Table Data Standard)
- **1. Pewarnaan Fungsional Khusus Kolom Status**:
  - Warna fungsional (`text-emerald-600`, `text-blue-600`, `text-amber-600`, `text-rose-600`, `text-gray-500`) **HANYA DIPERBOLEHKAN** pada kolom status transaksi, status pendaftaran, status verifikasi, atau status operasional.
- **2. Larangan Mutlak Teks Berwarna-Warni pada Kolom Non-Status**:
  - **DILARANG KERAS** memberi warna khusus (seperti kuning amber, hijau, biru, ungu) pada sel tabel (`<td>`) selain kolom status.
  - Kolom-kolom non-status seperti:
    - **Kode Invoice / Registrasi**: Wajib tipografi monokrom netral (`font-mono font-bold text-gray-900 dark:text-white`).
    - **Nama Paket Layanan**: Wajib abu-abu netral (`text-gray-500 dark:text-gray-400 font-medium`).
    - **Fee / Komisi Platform**: Wajib abu-abu netral (`font-medium text-gray-600 dark:text-gray-300`).
    - **Net Payout / Total Nominal**: Wajib teks tebal netral (`font-black text-gray-900 dark:text-white`).
    - **Kategori & Metadata**: Wajib teks polos netral (`text-gray-700 dark:text-gray-300 font-bold`). Dilarang dibungkus badge kartu/pill berwarna warni.
    - **Kontak / Nomor Telepon**: Wajib abu-abu netral (`text-gray-500 dark:text-gray-400 font-medium`).
    - **Tautan Eksternal / File CV**: Wajib teks link netral dengan underline (`text-gray-900 dark:text-white underline`).
- **3. Tujuan & Dampak Estetika**:
  - Menghilangkan kesan *"AI Slop pelangi"* di mana dalam satu baris tabel terdapat 4 warna berbeda sekaligus (kuning, hijau, biru, amber). Tabel harus tampak tenang, rapi, proporsional, dan fokus pada data esensial.

---

### Rule #69: Standarisasi Tombol Kolom Aksi Tabel (Maksimal 2 Tombol Inline Horizontal & Kebab Dropdown Menu untuk >2 Aksi / Anti-Stacked Buttons Standard)
- **1. Layout Maksimal 2 Tombol**:
  - Jika sebuah baris tabel hanya memiliki 1 atau 2 aksi, tombol wajib diletakkan secara **horizontal berdampingan** (`flex items-center gap-1.5` atau `gap-2`).
- **2. Larangan Mutlak Tombol Bertumpuk Vertikal (No Vertical Stacked Buttons)**:
  - **DILARANG KERAS** menumpuk 3 tombol secara vertikal ke bawah di dalam sel tabel (`<td>`) seperti yang terjadi sebelumnya di Monitoring Transaksi (`flex-col items-center`). Hal tersebut merusak proporsi ketinggian baris tabel dan tampak sesak/berantakan.
- **3. Standar Kebab Dropdown Menu (`⋮`) untuk >2 Aksi**:
  - Jika suatu tabel memiliki 3 aksi atau lebih, wajib menggunakan arsitektur:
    - **1 Tombol Utama (Primary Action)**: Menggunakan tombol amber LensMatch (`px-3 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200/80 dark:border-amber-900/60 font-bold text-xs transition shadow-2xs`).
    - **1 Tombol Kebab Menu (`⋮`)**: Menggunakan tombol icon tiga titik (`w-8 h-8 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800`).
    - **Floating Dropdown Popover (`z-50`)**: Memuat aksi-aksi sekunder dan aksi destruktif (warna merah `text-rose-600 hover:bg-rose-50`).
- **4. Keseragaman Tampilan Antar Status**:
  - Tombol utama pada kolom aksi wajib konsisten dan seragam antar status dalam tabel yang sama (misal selalu menampilkan `Lihat Detail` atau `Lihat Profil`), sementara aksi kondisional yang berubah-ubah (seperti verifikasi, pembatalan, atau refund) dimasukkan ke dalam menu dropdown dengan label yang jelas dan deskriptif.

---

### Rule #70: Larangan Terminologi Teknis, Kata Kaku & Pemborosan Simbol "&" (Anti-AI Slop Terminology & Natural Human Text Standard)
- **1. Larangan Kata Menggantung dengan Simbol "&"**:
  - **DILARANG KERAS** menggabungkan dua kata sinonim dengan simbol `&` untuk hal-hal sederhana (contoh AI-slop: *"Pemeriksaan & Review"*, *"Verifikasi & Kualitas"*, *"Pemeriksaan & Persetujuan"*).
  - Gunakan **satu kata aksi/konsep tunggal** yang padat, singkat, dan alami bagi pengguna (contoh: cukup *"Review Proposal"*, *"Penjurian Karya"*, atau *"Verifikasi Akun"*).
- **2. Larangan Kata "Kurasi" pada UI Sistem**:
  - Hindari kata *"Kurasi"* yang terkesan kaku/akademis. Ganti dengan kata aksi fungsional nyata:
    - Kurasi Lomba / Proposal ➔ **Review Proposal** atau **Seleksi Portofolio**.
    - Kurasi Karya ➔ **Penjurian Karya**.
    - Kurasi Mitra / Platform ➔ Dihapus atau gunakan **Verifikasi Akun**.
- **3. Larangan Istilah Keuangan Asing "Escrow"**:
  - Kata *"Escrow"* asing bagi pengguna umum Indonesia. Ganti dengan istilah lokal yang sudah terpercaya dan mudah dipahami:
    - Escrow Holding / Holding Escrow ➔ **Dana Ditahan** (menunggu sesi selesai).
    - Garansi Escrow / Proteksi Escrow ➔ **Garansi Pembayaran Aman** atau **Rekber LensMatch**.
    - Status Escrow / Payout ➔ **Status Pembayaran**.
- **4. Larangan Istilah Asing "Payout"**:
  - Ganti kata *"Payout"* dengan padanan bahasa Indonesia yang wajar:
    - Cairkan Payout / Release Payout ➔ **Transfer Dana** atau **Cairkan Dana**.
    - Ajukan Payout ➔ **Tarik Saldo**.
    - Net Payout ➔ **Pendapatan Bersih**.
    - Payout Dicairkan ➔ **Sudah Ditransfer**.
    - Rekening Payout ➔ **Rekening Bank** atau **Rekening Tujuan**.
- **5. Larangan Kata Serapan Ganjil "Terlisting"**:
  - Total Layanan Terlisting ➔ **Total Paket Layanan**.
  - Aktif & terlisting katalog ➔ **Aktif di katalog**.

---

### Rule #71: Standar Pasangan Dua Tombol Berbeda Fitur (Dual-Feature Action Buttons Standard)
- **1. Penerapan Gaya Amber Soft Tint untuk Tombol Sekunder**:
  - Pada area Header atau Card Actions yang menampilkan **2 tombol untuk 2 fitur berbeda** (contoh: `[Riwayat Pengajuan]` + `[Ajukan Lomba Foto]`, atau `[Tambah Modul]` + `[Tambah Tahapan]`):
    - **Tombol Primer (Aksi Utama / CTA)**: Menggunakan gaya LensMatch Solid Amber (`bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs rounded-xl shadow-xs`).
    - **Tombol Sekunder (Aksi Pendukung / Arah Berbeda)**: Menggunakan gaya **Amber Soft Tint** (`bg-amber-50 hover:bg-amber-100 text-amber-900 dark:bg-amber-950/50 dark:text-amber-300 border border-amber-200/80 dark:border-amber-900/60 font-bold text-xs rounded-xl shadow-2xs`). Ikon di dalamnya menggunakan `text-amber-600 dark:text-amber-400`.
    - **DILARANG** menggunakan warna putih polos dengan border abu-abu tipis dingin untuk tombol sekunder yang berdampingan dengan tombol amber solid, karena menimbulkan kesan tidak harmonis, hampa, dan seperti input mentah (*unstyled*).
- **2. Pengecualian Ketat untuk Tombol Utilitas Dialog/Form**:
  - Standar Amber Soft Tint di atas **TIDAK BERLAKU** untuk tombol utilitas dialog/modal/form seperti tombol `Batal`, `Kembali`, atau `Tutup`.
  - Tombol utilitas tersebut harus tetap mempertahankan gaya netral fungsionalnya (*neutral gray*).
- **3. Eliminasi Tombol Duplikat pada Empty State (Opsi B Standard)**:
  - Pada halaman daftar dengan tombol tambah permanen di header, kotak status kosong (*Empty State*) **DILARANG** menambahkan tombol CTA duplikat yang memiliki tujuan sama persis.
  - Cukup pertahankan **1 tombol permanen di kanan atas header**, sehingga tampilan halaman bersih dan tidak redundan.

---

### Rule #72: Standar Ikon Navigasi Panah / Kembali (Modern Single Chevron Standard)
- **1. Larangan Ikon Panah Berekor / Bergaris Batang (`←`)**:
  - DILARANG menggunakan ikon panah dengan garis horizontal batang (stem arrow seperti `d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"`). Gaya ini kaku, terlihat jadul, dan tidak selaras dengan UI modern LensMatch.
- **2. Larangan Ikon Chevron Ganda Bertumpuk (`«`)**:
  - DILARANG menggunakan ikon sudut ganda bertumpuk (double chevron seperti `«`), karena terlihat padat dan kuno.
- **3. Wajib Menggunakan Single Chevron Minimalis Modern (`<`)**:
  - Seluruh tombol kembali (*back navigation*), pagination, dan navigasi arah wajib menggunakan **Single Chevron Minimalis Modern**:
    ```html
    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
    </svg>
    ```
    Untuk arah kanan (maju/detail), gunakan pasangan simetrisnya: `d="M8.25 4.5l7.5 7.5-7.5 7.5"`.













### Rule #73: Standar Tata Letak Form Hybrid Responsif (Hybrid Responsive Form Standard — Kombinasi Horizontal Grid untuk Field Pendek & Vertikal Full-Width untuk Field Panjang)
1. **Latar Belakang & Filosofi Desain**:
   - Menggantikan aturan lama yang menumpuk seluruh form secara 100% vertikal 1 kolom. Setelah kartu container form diperlebar ke standar lebar penuh (`max-w-7xl` / mendekati tepi atas dan samping), input-input yang bernilai teks pendek (Nama, Email, Nomor Telepon/WA, Tanggal, Jam, Tarif/Harga, Kategori, Password) menjadi terlalu melar (*stretched out*) dan canggung jika dipaksa 1 baris penuh sendiri-sendiri.
   - Standar baru menetapkan tata letak **Hybrid Responsive**: bagian atas mengombinasikan field pendek secara horizontal (2 atau 3 kolom), dan bagian bawahnya dilanjutkan secara vertikal penuh untuk field panjang atau komponen interaktif.

2. **Aturan Tata Letak Form Hybrid Responsif**:
   - **A. Field Bernilai Pendek (Wajib Horizontal Grid)**:
     - Seluruh input dengan nilai pendek atau saling berkaitan (Nama Lengkap, Alamat Email, Nomor WhatsApp, Tanggal, Jam Mulai, Jam Selesai, Tarif Harga, Dropdown Kategori, Password & Konfirmasi Password, Rekening Bank) **WAJIB dikombinasikan secara horizontal** menggunakan grid responsif (`grid grid-cols-1 md:grid-cols-2 gap-6` atau `grid grid-cols-1 md:grid-cols-3 gap-6` pada layar desktop/tablet, dan otomatis 1 kolom pada layar mobile).
   - **B. Field Bernilai Panjang & Komponen Visual (Wajib Vertikal Full-Width)**:
     - Seluruh input dengan nilai panjang, deskriptif, atau berupa komponen interaktif (seperti Alamat Lengkap/Domisili, Textarea Deskripsi Bio / Deskripsi Layanan / Catatan, Box Unggah Foto Avatar/Sampul dengan Preview, Daftar Fasilitas/Fitur Tambahan) **WAJIB diletakkan di bawah baris grid horizontal dalam format vertikal full-width (`w-full`)**.
   - **C. Standar Ketinggian Textarea Deskripsi (Lapang & Proporsional, Minimal rows=6 / min-h-[140px])**:
     - Seluruh input textarea untuk deskripsi bio, uraian paket layanan, cerita portofolio, dan konsep kompetisi **WAJIB memiliki tinggi yang memadai (minimal `rows="6"` atau `min-h-[140px]` hingga `min-h-[150px]`)** dengan padding `px-4 py-3`, teks yang mudah dibaca `leading-relaxed`, dan pembesaran vertikal fleksibel `resize-y`.
     - DILARANG menggunakan textarea ceper (`rows="2"` atau `rows="3"`) pada kontainer yang lebar, karena membuat form tampak pipih/gepeng dan menyulitkan pengguna saat menulis teks panjang.

3. **Daftar Formulir yang Telah Menerapkan Standar Baru**:
   - `resources/views/public/profile/index.blade.php`: Row 1 Nama + Email (2 kolom), Row 2 No Telp + Domisili/Role (2 kolom), Bio (vertikal textarea), Box Foto Avatar (vertikal box), Card Keamanan Password (3 kolom horizontal).
   - `resources/views/photographer/profile/index.blade.php`: Row 1 Nama + Email (2 kolom), Row 2 No Telp + Domisili (2 kolom), Bio (vertikal textarea), Box Foto Avatar (vertikal), Box Banner Sampul (vertikal), Box Rekening Bank (3 kolom horizontal: Bank, No Rek, Atas Nama), Card Keamanan Password (3 kolom horizontal).
   - `resources/views/photographer/services/create.blade.php` & `edit.blade.php`: Row 1 Nama Layanan + Tarif Harga (2 kolom), Deskripsi Layanan (vertikal textarea), Fitur/Fasilitas (vertikal list).
   - `resources/views/photographer/portfolio/create.blade.php` & `edit.blade.php`: Row 1 Judul Portofolio + Kategori Fotografi (2 kolom), Deskripsi Cerita (vertikal textarea), Multi-Upload Foto Bento Preview (vertikal).
   - `resources/views/admin/admins/create.blade.php`: Row 1 Nama Lengkap + Email (2 kolom), Row 2 Kata Sandi + Konfirmasi Kata Sandi (2 kolom).
   - `resources/views/admin/elearning/create.blade.php` & `edit.blade.php`: Row 1 Judul Materi + Kategori (2 kolom), Row 2 Tipe Akses + Harga Kursus (2 kolom), Deskripsi Lengkap (vertikal textarea), Upload Media (vertikal).
   - `resources/views/photographer/availability/create.blade.php`: Tanggal Mulai + Tanggal Selesai (2 kolom) atau Tanggal + Jam Mulai + Jam Selesai (3 kolom).


---

### Rule #74: Standar Tata Letak Tombol Aksi Formulir (Grouping Rata Kanan, Batal Berdampingan di Kiri Tombol Utama — Tanpa Space-Between)
1. **Larangan Mutlak Pemisahan Tombol (`justify-between` / `space-between`)**:
   - DILARANG memisahkan tombol `Batal` (atau `Kembali`) dengan tombol aksi submit utama menggunakan `justify-between` atau `space-between`, di mana tombol Batal terlempar jauh ke tepi kiri ujung form sementara tombol aksi berada di tepi kanan ujung form. Pemisahan jarak ekstrem ini menyulitkan pengguna dan melanggar prinsip kedekatan visual (*Law of Proximity*).
2. **Aturan Tata Letak & Posisi Tombol**:
   - **Batal Berdampingan di Samping Kiri Tombol Utama**: Tombol `Batal` WAJIB diletakkan tepat di sebelah kiri tombol submit/aksi utama (tombol kuning/emerald).
   - **Margin / Gap Proporsional**: Berikan jarak margin proporsional antar tombol menggunakan `gap-3` (atau `gap-3.5`).
   - **Penyelarasan Rata Kanan (`justify-end`)**: Seluruh kelompok tombol aksi ditempatkan rata kanan di bagian bawah kartu formulir:
     ```blade
     <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700/80">
         <a href="{{ route('...') }}" class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold text-xs transition text-center shadow-2xs cursor-pointer">
             Batal
         </a>
         <button type="submit" class="px-6 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs rounded-xl shadow-xs transition inline-flex items-center justify-center gap-2 cursor-pointer">
             <span>Simpan Data / Kirim Pengajuan</span>
         </button>
     </div>
     ```
3. **Daftar Formulir yang Telah Menerapkan Standar Baru**:
   - `resources/views/public/contests/propose.blade.php`: Formulir Pengajuan Event Lomba Foto (Batal berdampingan di kiri Kirim Pengajuan Lomba, rata kanan gap-3).
   - `resources/views/public/forum/index.blade.php`: Modal Buat Topik Forum Baru (Batal di kiri Terbitkan Topik, rata kanan gap-3).
   - `resources/views/admin/contests/index.blade.php`: Modal Review Pengajuan & Modal Tolak Lomba (Tutup / Kembali di kiri tombol aksi, rata kanan gap-3).
   - `resources/views/photographer/services/create.blade.php` & `edit.blade.php`: Tambah & Edit Paket Layanan Fotografer.
   - `resources/views/photographer/portfolio/create.blade.php` & `edit.blade.php`: Tambah & Edit Album Portofolio Fotografer.
   - `resources/views/photographer/availability/create.blade.php`: Penutupan Jadwal / Kalender Libur Fotografer.
   - `resources/views/admin/admins/create.blade.php`: Formulir Tambah Staf Admin Baru.
   - `resources/views/admin/categories/create.blade.php` & `edit.blade.php`: Tambah & Edit Kategori Fotografi.
   - `resources/views/admin/contests/create.blade.php` & `edit.blade.php`: Tambah & Edit Lomba Foto Admin.
   - `resources/views/admin/elearning/create.blade.php` & `edit.blade.php`: Tambah & Edit Materi Kursus E-Learning.

---

### Rule #75: Standar Tata Letak Galeri Portofolio Fotografer (Full-Width Vertical Stacked Album Card)
1. **Struktur Kontainer Induk (Vertical Stacked Cards)**:
   - Koleksi album portofolio ditampilkan secara vertikal membujur ke bawah per album koleksi:
     `<div class="space-y-8 sm:space-y-10">`
   - Setiap kartu album portofolio mengambil lebar penuh (`w-full`), menyajikan karya fotografi secara megah, lapang, dan tidak terhimpit.
2. **Spesifikasi Kartu Album (Rotasi Vertikal: Foto Utama Full di Atas + 3 Thumbnail Horizontal di Bawah)**:
   - **Card Outer**:
     `bg-white dark:bg-gray-800 rounded-[26px] p-4 sm:p-6 border border-gray-200/80 dark:border-gray-700/80 shadow-xs hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300 space-y-4`
   - **Foto Pertama Utama (Hero Photo - Full Width di Atas)**:
     - Mengambil lebar penuh kartu dengan rasio layar lebar sinematik:
       `relative w-full overflow-hidden bg-gray-100 dark:bg-gray-900 rounded-[20px] aspect-[16/9] sm:aspect-[2.2/1] cursor-pointer group`
     - Gambar: `w-full h-full object-cover group-hover:scale-102 transition-transform duration-500 ease-out`.
     - Hover overlay: Tombol halus transparan *"Buka Foto Penuh"* untuk interaksi visual yang jelas.
   - **3 Thumbnail Horizontal Berjejer di Bawah**:
     - `grid grid-cols-3 gap-3 sm:gap-4`
     - Setiap thumbnail: `relative w-full aspect-[16/10] sm:aspect-[16/9] overflow-hidden bg-gray-100 dark:bg-gray-900 rounded-[14px] cursor-pointer group/thumb`
     - Gambar: `w-full h-full object-cover group-hover/thumb:scale-105 transition-transform duration-500 ease-out`.
     - Jika album memiliki lebih dari 4 foto (`$pCount > 4`), thumbnail ke-3 menampilkan badge overlay `+{{ $pCount - 4 }} Foto Lainnya`.
   - **Meta Footer Bawah**:
     - `flex flex-col sm:flex-row sm:items-center justify-between gap-2 pt-2 px-1 border-t border-gray-100 dark:border-gray-700/60`
     - Kiri: Judul album tebal (`text-base sm:text-lg font-bold text-gray-900 dark:text-white`) beserta deskripsi/cerita singkat (`text-xs text-gray-500 line-clamp-2`).
     - Kanan: Ikon bingkai kamera SVG asli + angka total foto album (`text-xs font-semibold text-gray-500 dark:text-gray-400`).
3. **Interaktivitas Lightbox Viewer**:
   - Mengklik foto utama maupun thumbnail mana pun memicu modal Lightbox interaktif layar penuh dengan navigasi panah kiri/kanan dan pintasan keyboard (`Esc`, Panah Kanan/Kiri).

---

### Rule #76: Standar Baku Label Teks Tombol Aksi Formulir ("Simpan Data" vs "Perbaharui Data")
1. **Aturan Baku Penamaan Teks Tombol**:
   - **Formulir Tambah Data Baru (`create` / `store` / `insert`)**:
     - Label teks pada tombol submit aksi utama **WAJIB bertuliskan `"Simpan Data"`**.
     - DILARANG menggunakan variasi teks seperti `"Tambah Data"`, `"Tambah Album"`, `"Kirim"`, atau sejenisnya pada formulir penambahan data.
   - **Formulir Ubah / Edit Data (`edit` / `update`)**:
     - Label teks pada tombol submit aksi utama **WAJIB bertuliskan `"Perbaharui Data"`**.
     - DILARANG menggunakan variasi teks seperti `"Simpan Perubahan"`, `"Update"`, atau `"Edit Data"`.

2. **Daftar Formulir yang Telah Menerapkan Standar Baku Rule #76**:
   - **Formulir Tambah Data (Submit Button: `"Simpan Data"`)**:
     - `resources/views/photographer/services/create.blade.php`: Tambah Paket Layanan Fotografer.
     - `resources/views/photographer/portfolio/create.blade.php`: Tambah Album Portofolio Fotografer.
     - `resources/views/photographer/availability/create.blade.php`: Penutupan Jadwal / Libur Fotografer.
     - `resources/views/admin/admins/create.blade.php`: Tambah Staf Admin Baru.
     - `resources/views/admin/categories/create.blade.php`: Tambah Kategori Layanan.
     - `resources/views/admin/contests/create.blade.php`: Tambah Lomba Foto Baru.
     - `resources/views/admin/elearning/create.blade.php`: Tambah Kursus E-Learning.
     - `resources/views/admin/elearning/curriculum.blade.php`: Modal Tambah Modul & Materi Pelajaran (`moduleMode === 'create' ? 'Simpan Data' : 'Perbaharui Data'`).
     - `resources/views/partials/client/review-modal.blade.php`: Modal Ulasan Klien (`isEdit ? 'Perbaharui Data' : 'Simpan Data'`).
   - **Formulir Edit Data (Submit Button: `"Perbaharui Data"`)**:
     - `resources/views/photographer/services/edit.blade.php`: Edit Paket Layanan Fotografer.
     - `resources/views/photographer/portfolio/edit.blade.php`: Edit Album Portofolio Fotografer.
     - `resources/views/admin/categories/edit.blade.php`: Edit Kategori Layanan.
     - `resources/views/admin/contests/edit.blade.php`: Edit Lomba Foto.
     - `resources/views/admin/elearning/edit.blade.php`: Edit Kursus E-Learning.
     - `resources/views/photographer/profile/index.blade.php`: Edit Biodata, Kontak, Rekening & Kata Sandi Fotografer.
     - `resources/views/public/profile/index.blade.php`: Edit Profil Pengguna & Kata Sandi.
     - `resources/views/public/auth/reset-password.blade.php`: Reset & Pembaharuan Kata Sandi Baru.

---

### Rule #77: Standarisasi Sistem Pelaporan Komentar Forum & Moderasi Admin (Hybrid Moderation & Soft-Hide Standard)
1. **Opsi Kontekstual Komentar Publik (`resources/views/public/forum/show.blade.php`)**:
   - Tombol titik tiga (kebab menu) pada setiap komentar forum memicu dropdown Alpine.js minimalis dengan opsi:
     - **Salin Teks Komentar**: Menyalin isi komentar ke clipboard perangkat disertai feedback notifikasi toast universal.
     - **Laporkan Komentar**: Membuka modal pelaporan pelanggaran (hanya tampil jika pengguna sedang login dan bukan pemilik komentar). Pengguna guest yang mengklik opsi ini akan diarahkan secara aman ke modal masuk akun.
     - **Hapus Komentar**: Form aksi hapus komentar permanen (hanya tampil untuk pemilik komentar atau pengguna ber-role Admin / Super Admin).
   - Teks statis `"Translate"` dihapus permanen agar antarmuka komentar bebas dari elemen dummy/placeholders.
2. **Modal Pelaporan Pelanggaran Komentar (Kepatuhan Rule #59 & Anti-AI Slop)**:
   - Judul modal: `"Laporkan Komentar"` di posisi tengah (`text-center`) tanpa icon apa pun.
   - Preview kutipan ringkas komentar yang dilaporkan dalam boks abu-abu netral.
   - Pilihan kategori pelanggaran berbasis radio button murni: `spam` (Spam atau Promosi Tidak Relevan), `harassment` (Pelecehan atau Ujaran Kebencian), `inappropriate` (Konten Tidak Pantas atau Asusila), `misinformation` (Informasi Menyesatkan atau Penipuan), dan `other` (Lainnya).
   - Input opsional catatan/alasan tambahan (`textarea`, `max:500`).
   - Tombol aksi bawah berpusat di tengah (`justify-center gap-3`): Tombol **Batal** (`bg-gray-50 border border-gray-200`) dan Tombol **Kirim Laporan** (`bg-amber-400 font-extrabold`). Sesuai Rule #59, **DILARANG** menambahkan tombol close 'X' di pojok kanan atas modal jika tombol Batal sudah tersedia.
3. **Mekanisme Otomatis Auto-Flagging & Soft-Hide**:
   - Proteksi ganda: Sistem mencegah pengguna melaporkan komentar milik sendiri (*self-report prevention*) serta mencegah laporan duplikat dari pengguna yang sama pada komentar yang sama (didukung indeks database `UNIQUE(id_comment, id_user)`).
   - Ketika total laporan unik dari komunitas mencapai batas ambang $\ge 3$ laporan (`count >= 3`):
     - Sistem otomatis menyembunyikan komentar (`is_hidden = true`).
     - Tampilan komentar di publik digantikan dengan pemberitahuan netral: *"Komentar ini disembunyikan sementara karena menerima banyak laporan dari komunitas dan sedang ditinjau oleh admin."*
     - Sistem otomatis mengirimkan notifikasi peringatan in-app ke seluruh akun Admin & Super Admin.
4. **Panel Moderasi Laporan Komentar Admin (`/admin/forum?tab=reports`)**:
   - Tab navigasi atas: **Topik Diskusi** (`{{ $totalPosts }}`) dan **Laporan Komentar** (`{{ $pendingReportsCount }} Menunggu`). Menggunakan tombol tab soft pill sesuai Rule #58 tanpa garis bawah/kanan menempel.
   - Tabel laporan menyajikan: Kutipan Komentar beserta tautan topik diskusi, Penulis Komentar, Jumlah & Rincian Laporan Komunitas, Status Komentar (`Disembunyikan Otomatis` vs `Tayang` dalam format teks warna murni tanpa bulatan status / dot badge sesuai Rule #67), dan Tombol Aksi Moderasi:
     - **Abaikan**: Mengabaikan seluruh laporan, mengubah status laporan menjadi `dismissed`, dan memulihkan komentar kembali tayang (`is_hidden = false`).
     - **Hapus Komentar**: Menghapus komentar secara permanen dari basis data dan menandai laporan sebagai `reviewed`.

---

### Rule #78: Standarisasi Tampilan Navigasi Tab Teks Horizontal (Active Text Underline Standard & Counter Pill Badge — Eliminasi Tombol Box / Pill Border)
1. **Latar Belakang & Filosofi Desain**:
   - Menghilangkan inkonsistensi di mana beberapa navigasi tab menggunakan kotak pill tebal berwarna kuning solid (`px-4 py-2 rounded-xl bg-amber-400 text-gray-950 font-extrabold shadow-xs`) berdampingan dengan kotak ber-border abu-abu. Gaya boxy/pill button membuat UI terkesan kaku, berat, dan memecah hierarki visual.
   - Menetapkan satu standar resmi yang seragam di seluruh sistem: **Navigasi Tab Teks Murni Horizontal** dengan penanda aktif berupa **Active Text Underline** dan pill badge counter bundar kecil di sampingnya.
2. **Spesifikasi Aturan & Tailwind Classes**:
   - **Kontainer Baris**: `flex items-center gap-6 overflow-x-auto pb-1` (bebas dari garis pembatas horizontal panjang/divider yang memotong layar sesuai Rule #58).
   - **Item Tab Aktif**:
     - Wrapper tombol/tautan: `py-2 text-xs sm:text-sm transition flex items-center gap-2 cursor-pointer shrink-0 text-amber-600 dark:text-amber-400 font-black`
     - Teks judul tab: `underline underline-offset-8 decoration-2 decoration-amber-500 dark:decoration-amber-400` (garis bawah HANYA menempel pada teks judul, tidak merembes ke badge angka).
     - Badge hitungan data (*counter badge*): `px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400`
   - **Item Tab Tidak Aktif**:
     - Wrapper tombol/tautan: `py-2 text-xs sm:text-sm transition flex items-center gap-2 cursor-pointer shrink-0 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-bold`
     - Teks judul tab: tanpa underline (`class=""`).
     - Badge hitungan data: `px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300`
3. **Larangan Keras**:
   - DILARANG menggunakan box border button, pill button (`rounded-xl bg-amber-400 text-gray-950` / `border border-gray-200`), atau divider border-b penuh yang memotong container.
4. **Halaman yang Telah Menerapkan Standar Rule #78**:
   - `resources/views/admin/transactions/index.blade.php`: Tab Transaksi Booking, Penjualan E-Learning, Pendaftaran Lomba.
   - `resources/views/client/invoices/index.blade.php`: Tab Sesi Booking Fotografer, Kursus E-Learning, Pendaftaran Lomba Foto.
   - `resources/views/admin/forum/index.blade.php`: Tab Topik Diskusi & Laporan Komentar.
   - `resources/views/admin/contests/manage.blade.php`: Tab Pendaftar & Submisi Karya Peserta.
   - `resources/views/public/photographers/show.blade.php`: Tab Paket Layanan, Portofolio, Jadwal Sesi, dan Ulasan Klien.
   - `resources/views/public/elearning/show.blade.php`: Tab Deskripsi, Lampiran, dan Catatan.
   - `resources/views/public/help/faq.blade.php`: Tab Kategori Pertanyaan FAQ.

---

### Rule #79: Standarisasi Fitur Pengajuan & Persetujuan Jadwal Ulang Sesi Foto (Reschedule Booking Standard)
1. **Prinsip Simetris Dua Pihak**:
   - Baik **Klien** maupun **Fotografer** berhak mengajukan permohonan jadwal ulang (*reschedule request*) jika terjadi kendala tak terduga (misal cuaca ekstrem, sakit, pergeseran rundown acara) tanpa harus membatalkan kontrak pemesanan.
2. **Validasi Ketersediaan Ketat (Zero Schedule Conflict)**:
   - Tanggal dan jam mulai baru wajib divalidasi di backend:
     1. Minimal hari ini (`after_or_equal:today`).
     2. Tidak boleh jatuh pada tanggal/jam libur fotografer (`photographer_availability`).
     3. Tidak boleh berbentrok dengan pesanan aktif klien lain pada jam yang sama di tanggal tersebut.
3. **Modal Formulir Pengajuan (Kepatuhan Rule #59, #73, #74, #76)**:
   - Judul modal: `"Ajukan Jadwal Ulang Sesi"` di posisi tengah (`text-center`) tanpa icon.
   - Grid 2-kolom horizontal: Tanggal Baru (`input[type="date"]`) + Jam Mulai Sesi Baru (`select`).
   - Vertikal full-width: Alasan Perubahan Jadwal (`textarea`, `required`).
   - Tombol aksi berpusat di tengah (`justify-center gap-3`): Tombol **Batal** (`border border-gray-200`) dan Tombol **Kirim Permintaan** (`bg-amber-400 font-extrabold`). Tanpa tombol close 'X' di pojok kanan atas karena tombol Batal sudah tersedia.
4. **Persetujuan & Integrasi Notifikasi In-App**:
   - Pihak penerima melihat kartu pengajuan jadwal baru dengan opsi **`Setujui Jadwal Baru`** (memperbarui `booking_date`, `jam_mulai`, dan `jam_selesai` secara otomatis) atau **`Tolak`** (jadwal tetap mengacu ke tanggal lama).
   - Seluruh tahapan pengajuan, persetujuan, dan penolakan otomatis mengirimkan `Notification::send()` dan tercatat di `activity_logs`.



---

### Rule #80: Standarisasi Teks & Warna Tombol Aksi Tabel (Trio Baku: Lihat, Edit, Hapus)
1. **Prinsip Kesederhanaan & Eliminasi Redundansi Teks**:
   - Tombol aksi pada kolom aksi tabel (`Aksi`) maupun dropdown aksi baris WAJIB menggunakan teks ringkas bersuku kata tunggal yang baku:
     - **`Lihat`** (DILARANG menambahkan kata objek, misal: *"Lihat Detail"*, *"Lihat Data"*, *"Lihat di Publik"*, *"Buka Profil"*).
     - **`Edit`** (DILARANG menambahkan kata objek, misal: *"Edit Data"*, *"Edit Lomba"*, *"Edit Materi"*, *"Edit Album"*).
     - **`Hapus`** (DILARANG menambahkan kata objek, misal: *"Hapus Data"*, *"Hapus Spam"*, *"Hapus Lomba"*, *"Hapus Komentar"*, *"Hapus User"*).
2. **Spesifikasi Warna Baku & Tailwind Classes**:
   - **Tombol `Lihat` (Soft Blue)**:
     - Background & Text: `bg-blue-50 hover:bg-blue-100 text-blue-600 dark:bg-blue-950/60 dark:text-blue-300`
     - Border: `border border-blue-200/80 dark:border-blue-900/60`
     - Dimensi & Tipografi: `px-3 py-1.5 rounded-lg font-bold text-xs transition min-w-[60px] text-center`
   - **Tombol `Edit` (Soft Amber)**:
     - Background & Text: `bg-amber-50 hover:bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300`
     - Border: `border border-amber-200/80 dark:border-amber-900/60`
     - Dimensi & Tipografi: `px-3 py-1.5 rounded-lg font-bold text-xs transition min-w-[60px] text-center inline-block`
   - **Tombol `Hapus` (Soft Rose / Red)**:
     - Background & Text: `bg-rose-50 hover:bg-rose-100 text-rose-600 dark:bg-rose-950/60 dark:text-rose-300`
     - Border: `border border-rose-200/80 dark:border-rose-900/60`
     - Dimensi & Tipografi: `px-3 py-1.5 rounded-lg font-bold text-xs transition min-w-[60px] text-center`
3. **Pengecualian Aksi Khusus non-CRUD**:
   - Aksi fungsional domain khusus yang bukan CRUD (seperti *"Abaikan"*, *"Verifikasi Akun"*, *"Buka Jadwal"*, atau *"Kurikulum"*) diperbolehkan menggunakan kata kerja fungsional spesifik dengan tetap mematuhi kesederhanaan satu hingga dua kata maksimal.


---

### Rule #81: Standarisasi Sistem Jam Kerja Mingguan & Pengecualian Jadwal Fotografer (Weekly Schedule, Date Overrides, & Zero-Break Guard)
1. **Arsitektur Dua Lapisan Ketersediaan**:
   - **Layer 1: Jam Kerja Mingguan Rutin (*Weekly Recurring Hours*)**:
     - Fotografer memiliki kontrol mandiri atas jadwal rutin 7 hari (Senin s/d Minggu).
     - Tiap hari dapat diset: **Buka Operasional** vs **Libur Rutin**, serta opsi **24 Jam Fleksibel** vs **Rentang Jam Spesifik** (misal 06:00 - 18:00 WIB untuk fotografer *outdoor sunrise/sunset*).
   - **Layer 2: Pengecualian & Libur Khusus (*Date Overrides*)**:
     - Fotografer dapat menutup tanggal tertentu secara insidental (libur seharian penuh atau penutupan slot jam tertentu) melalui tabel `photographer_availability`.
2. **Prinsip Integritas Pesanan Aktif (*Zero-Break Guard*)**:
   - Sistem **MELARANG KERAS** fotografer mematikan hari operasional, memperpendek rentang jam kerja, atau menandai tanggal libur jika **pada slot tersebut telah terdapat pesanan booking aktif klien** (`contracts` status `active`, `approved`, atau `pending_payment`).
   - Sistem akan memblokir perubahan dan memunculkan pesan validasi: *"Gagal menutup jadwal: Terdapat pesanan aktif (#KTRK-XXXXX) pada jadwal tersebut. Anda harus mengajukan Jadwal Ulang (Reschedule) terlebih dahulu kepada Klien terkait."*
3. **Validasi Reservasi Klien & Reschedule (*Conflict Prevention*)**:
   - Saat Klien memesan layanan atau saat salah satu pihak mengajukan reschedule, backend memvalidasi:
     1. Apakah hari sesi tersebut aktif dibuka oleh fotografer.
     2. Apakah jam mulai sesi berada dalam batas jam operasional fotografer.
     3. Apakah tanggal/jam tersebut terkena blokir pengecualian libur (*blocked schedule*).
     4. Apakah slot jam tersebut sudah diisi oleh pesanan klien lain (*Zero Double Booking*).


---

### Rule #82: Standarisasi Fitur Wishlist & Favorit Fotografer (Saved Photographers)
1. **Ikon & Aksi Simpan Instan (Bookmark Toggle Button)**:
   - Diletakkan di pojok kartu fotografer (`/fotografer`), header profil publik (`/fotografer/{id}`), dan halaman favorit klien.
   - Desain minimalis: ikon bookmark outline netral (`text-gray-400 hover:text-amber-500`) jika belum disimpan, dan bookmark solid terisi penuh (`fill-amber-500 text-amber-500`) jika sudah disimpan.
   - Operasi Asinkron (Zero Reload): Aksi toggle berjalan melalui AJAX endpoint `/client/favorites/{id}/toggle` dengan respon JSON instan dan notifikasi feedback toast.
2. **Halaman Khusus Favorit Klien (`/client/favorites`)**:
   - Terintegrasi di menu navigasi utama dropdown Klien (*"Fotografer Favorit"*).
   - Dilengkapi kartu KPI ringkasan (Total Disimpan, Studio Top Rated, Siap Booking).
   - Menampilkan kartu profil lengkap dengan tombol aksi cepat *"Lihat Profil"*, *"Chat"*, dan aksi hapus dari favorit.
   - Desain empty state informatif jika klien belum menyimpan fotografer, dilengkapi tombol eksplorasi etalase publik.

---

### Rule #83: Standarisasi Penawaran Khusus via Chat (Custom Offer / Quote in Chat)
1. **Formulir Modal Penawaran Khusus Fotografer (Kepatuhan Rule #59, #73, #74, #76)**:
   - Fotografer dapat membuka modal penawaran melalui tombol dokumen penawaran di bilah input obrolan (`/chat` atau `/photographer/chat`).
   - Judul modal: *"Kirim Penawaran Khusus"* di posisi tengah (`text-center`) tanpa icon dekoratif (Rule #59).
   - Layout Formulir:
     - Baris 1: Judul Penawaran Sesi (`input[type="text"]`, full-width).
     - Baris 2: Grid 2-kolom horizontal: Total Biaya (Rp) + Durasi Sesi (Jam).
     - Baris 3: Grid 2-kolom horizontal: Tanggal Sesi + Jam Mulai Sesi.
     - Baris 4: Lokasi Pemotretan (`input[type="text"]`, full-width).
     - Baris 5: Rincian Layanan & Catatan Khusus (`textarea`, full-width).
   - Tombol Aksi: Dua tombol di tengah (`justify-center gap-3`): Tombol **Batal** di sebelah kiri dan Tombol **Kirim Penawaran** di sebelah kanan (`bg-amber-400 font-extrabold`). Tanpa tombol close 'X' di pojok kanan atas.
2. **Kartu Penawaran Interaktif di Room Chat**:
   - Ditampilkan simetris di bubble pesan obrolan baik untuk Fotografer maupun Klien.
   - Menampilkan metadata kesepakatan: Judul, Nominal Biaya Terformat (Rp), Jadwal Sesi & Jam Mulai, Durasi Jam, Lokasi, dan Catatan Rincian.
   - Badge Status: *"Menunggu Respon Klien"* (kuning/amber), *"Disetujui Klien"* (hijau emerald), atau *"Ditolak Klien"* (merah rose).
3. **Persetujuan & Penerbitan Kontrak Transaksi Otomatis**:
   - Klien memiliki tombol aksi cepat langsung di dalam pesan chat: **`Tolak`** atau **`Setujui & Bayar`**.
   - Saat Klien menekan *"Setujui & Bayar"*, sistem otomatis:
     1. Mengubah status penawaran menjadi `accepted`.
     2. Menerbitkan data `Contract` resmi dengan pembagian omzet 90% Fotografer dan 10% Platform Fee.
     3. Menerbitkan data `ContractBooking` terintegrasi.
     4. Mengarahkan Klien langsung ke halaman tagihan/invoice pembayaran (`/booking/{id}/invoice`).

---

### Rule #84: Standarisasi Lencana Reputasi Otomatis (Badges of Excellence)
1. **Perhitungan Otomatis 100% Berbasis Interaksi Riil Klien**:
   - **`Top Rated`**: Diberikan secara otomatis jika fotografer memiliki rating rata-rata $\ge 4.8$ dari minimal 5 ulasan resmi klien aktif (`testimonials` dengan `is_hidden = false`). Rating hanya dapat dibuat/diubah oleh klien pemilik kontrak selesai.
   - **`Jam Terbang Tinggi`**: Diberikan secara otomatis jika fotografer telah menyelesaikan minimal 10 sesi pemotretan berbayar yang telah dikonfirmasi hasilnya oleh klien (`contracts` dengan `status_contract = 'completed'` dan `is_validated_client = true`).
   - **`Respon Cepat` (Opsi 2 — Gabungan Chat Klien & Booking Pending)**: Diberikan secara otomatis berdasarkan respon fotografer terhadap klien (`RolePhotographer::getClientResponseStats()`):
     - Rata-rata waktu balas pesan klien pada `chat_messages` (`deleted_for_all = false`) $\le 24$ jam dengan minimal 1 balasan nyata (sapaan otomatis sistem saat membuka chat sebelum klien mengirim pesan tidak dihitung).
     - Tidak ada pesan klien terakhir yang dibiarkan tidak terjawab $> 24$ jam.
     - Tidak ada order booking `pending` pada `contracts` (`is_validated_photographer = false`, `status_booking = 'pending'`) yang dibiarkan menunggu konfirmasi/penolakan $> 24$ jam. Jika ada tunggakan, lencana `Respon Cepat` otomatis gugur.
2. **Kepatuhan Anti-AI Slop & Desain Muted Pill**:
   - DILARANG menggunakan bulatan status berkedip (`●` status dot).
   - Teks lencana lugas, bersih, dan menggunakan skema warna lembut yang tenang (*muted soft tone*):
     - *Top Rated Studio*: `bg-amber-50 text-amber-800 border-amber-200/80 dark:bg-amber-950/40 dark:text-amber-300`
     - *Jam Terbang Tinggi*: `bg-blue-50 text-blue-700 border-blue-200/80 dark:bg-blue-950/40 dark:text-blue-300`
     - *Respon Cepat*: `bg-emerald-50 text-emerald-800 border-emerald-200/80 dark:bg-emerald-950/40 dark:text-emerald-300`
3. **Penyebaran Konsisten di Seluruh Sistem**:
   - Lencana reputasi tampil terpadu di:
     - Kartu katalog fotografer publik (`/fotografer`).
     - Header profil detail fotografer (`/fotografer/{id}`).
     - Kartu fotografer tersimpan klien (`/client/favorites`).
     - Panel Reputasi & Lencana Keunggulan Studio di Dashboard Fotografer (`/photographer/dashboard`).


---

### Rule #85: Standarisasi Smart Banner Pengingat Sesi H-1 & Hari-H (Upcoming Session Countdown & Smart Reminders)
1. **Logika Deteksi Otomatis Jadwal Mendesak (Window <= 24-36 Jam)**:
   - Sistem secara otomatis mendeteksi pesanan sesi foto aktif (`status_booking = 'confirmed'` dan kontrak bukan `cancelled` atau `completed`) yang tanggal pemotretannya jatuh pada:
     - **Hari Ini (`today`)**: Berlangsung pada hari yang sama.
     - **Besok (`tomorrow` / H-1)**: Berlangsung dalam rentang 24 jam ke depan.
2. **Kepatuhan Anti-AI Slop & Visual Muted Banner**:
   - DILARANG menggunakan teks neon mencolok, bulatan status berkedip (`●`), atau efek pop-up yang mengganggu (*intrusive*).
   - Menggunakan kartu beraksen lembut tenang: `border border-amber-300/80 dark:border-amber-800/70 bg-gradient-to-r from-amber-500/10 via-amber-500/5 to-white dark:from-amber-950/40 dark:via-gray-800/80 dark:to-gray-800 rounded-2xl sm:rounded-3xl p-5 sm:p-6 shadow-xs`.
   - Lencana status teks murni:
     - *Sesi Hari Ini*: `bg-amber-400 text-gray-950 font-black text-[11px] px-2.5 py-1 rounded-full`
     - *Pengingat Sesi H-1 (Besok)*: `bg-blue-100 dark:bg-blue-950/70 text-blue-700 dark:text-blue-300 border border-blue-200/70 font-extrabold text-[11px] px-2.5 py-1 rounded-full`
     - *Sedang Berlangsung Hari Ini*: `bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 font-bold px-2.5 py-0.5 rounded-lg border border-emerald-200`
3. **Hitung Mundur Real-Time (Live Dynamic Countdown)**:
   - Dilengkapi penghitung waktu mundur otomatis per detik menggunakan Alpine.js (`hours + 'j : ' + minutes + 'm : ' + seconds + 'd'`) dengan fallback teks terformat dari backend jika JavaScript dinonaktifkan.
4. **Dua Tombol Aksi Cepat (One-Click Maps & Direct Chat)**:
   - **Petunjuk Arah (Google Maps)**: Tautan langsung membuka koordinat / alamat pemotretan di Google Maps (`https://www.google.com/maps/search/?api=1&query=...`).
   - **Buka Chat Sesi**: Tautan satu-klik membuka obrolan langsung antara Klien dan Fotografer terkait untuk koordinasi teknis, cuaca, dan pakaian di lapangan.
5. **Penempatan Konsisten di 3 Titik Kunci**:
   - Panel Dashboard Fotografer (`/photographer/dashboard`).
   - Panel Kelola Order Booking Fotografer (`/photographer/bookings`).
   - Panel Reservasi Sesi Foto Klien (`/client/bookings`).

---

### Rule #86: Standarisasi Fitur Tanya Jawab Kombinasi (Tanya Jawab Fotografer + Ketentuan Sistem Terpusat — Opsi A)
1. **Tujuan & Arsitektur Modul (Opsi A)**:
   - Memisahkan tanggung jawab antara informasi layanan fotografer dan ketentuan platform pada Tab **Tanya Jawab** etalase publik (`/fotografer/{id}`):
     1. **Tanya Jawab Fotografer**: Fotografer mengelola pertanyaan khusus layanannya melalui `/photographer/faqs` (estimasi waktu editing dan pengiriman hasil, tiket lokasi, transport luar kota, batas revisi warna, penanganan cuaca).
     2. **Ketentuan Sistem LensMatch (Hardcoded Terpusat)**: Ketentuan platform (alur pemesanan, Pembayaran Penuh atau DP 30%, penahanan dana, pembatalan H-24 jam, jadwal ulang, penerimaan hasil foto) dikelola terpusat di kode pada `config/lensmatch_faqs.php` sebagai sumber tunggal untuk `/faq` dan tab profil fotografer.
   - Menghindari pengulangan pertanyaan umum di obrolan dan menjaga profil fotografer baru tetap informatif sejak awal aktif.
2. **Kepatuhan Antarmuka & Standar CRUD**:
   - **Dashboard Fotografer (`/photographer/faqs`)**:
     - Tabel **Daftar Tanya Jawab** dengan tombol aksi standar Rule #80: **Edit** dan **Hapus** teks murni tanpa ikon. Empty state mengikuti Rule #71 Point 3 (tanpa tombol CTA duplikat; hanya 1 tombol `Tambah Pertanyaan` di header).
     - Panel referensi *read-only* **Ketentuan Sistem LensMatch** menampilkan ketentuan sistem yang otomatis tayang di profil publik.
     - Modal Tambah/Edit mematuhi **Rule #59** & **Rule #73**: judul rata tengah tanpa ikon (`Tambah Pertanyaan` / `Edit Pertanyaan`), tombol aksi bawah rata tengah maksimal 2 (`Batal` dan `Simpan Data` / `Perbaharui Data` amber brand), tanpa tombol close `X` kanan atas, serta menghindari penambahan elemen pembantu di luar instruksi (Rule #42) dan melarang penggunaan istilah `'Studio'` untuk peran fotografer (Rule #19).
3. **Penyajian pada Profil Publik (`/fotografer/{id}`)**:
   - Ditempatkan sebagai Tab ke-5 **Tanya Jawab** di bawah sistem navigasi tab Rule #78 dengan counter gabungan.
   - Menampilkan blok **Tanya Jawab Fotografer** dan **Ketentuan Sistem LensMatch** dengan kartu putih seragam bebas shadow internal (**Rule #100**) serta ikon accordion konsisten (**Rule #22**).

---

### Rule #87: Standarisasi Penilaian Dua Arah: Fotografer Menilai Klien (Two-Way Rating System)
1. **Tujuan & Arsitektur Reputasi Dua Arah**:
   - Menyediakan perlindungan bagi komunitas fotografer mitra dan mempromosikan ekosistem marketplace yang sehat dengan memberikan hak bagi fotografer untuk memberikan ulasan internal kepada klien setelah sesi pemotretan berstatus `completed`.
   - Mengukur 3 dimensi objektif selain penilaian umum:
     1. **Ketepatan Waktu**: Kepatuhan hadir di lokasi pemotretan sesuai jam yang disepakati.
     2. **Komunikasi**: Kecepatan respon dan kejelasan instruksi/kebutuhan klien.
     3. **Kerjasama & Sikap**: Sikap kooperatif selama pengarahan pose dan sesi pemotretan.
2. **Kepatuhan Antarmuka & Modal Formulir**:
   - Tombol pemicu terdapat pada tabel pesanan selesai fotografer (`/photographer/bookings`):
     - Jika belum dinilai: tombol amber lembut *"Beri Nilai Klien"*.
     - Jika sudah dinilai: tombol amber lembut *"★ X.0 (Ubah Nilai Klien)"* yang memungkinkan fotografer memperbaharui ulasan sewaktu-waktu.
   - Modal penilaian mematuhi **Rule #59** & **Rule #73**: Judul rata tengah *"Nilai Pengalaman Bersama Klien"*, 3 dropdown dimensi dalam grid 3 kolom horizontal, bintang kepuasan interaktif (1-5), dan textarea catatan pengalaman. Tombol tengah maksimal 2: **Batal** dan **Simpan Data**.
3. **Penyajian Transparan pada Dashboard Klien (`/client/bookings`)**:
   - Klien dapat melihat ulasan yang diberikan oleh fotografer secara transparan pada baris pesanan selesai dengan kartu muted beraksen amber lembut (`bg-amber-50/70 dark:bg-amber-950/30 border-amber-200/70 dark:border-amber-900/50`), menampilkan skor bintang, rincian 3 aspek penilaian, serta kutipan catatan fotografer.
   - Sistem otomatis mengirimkan notifikasi in-app bertipe `client_rated` kepada akun Klien saat ulasan diterbitkan.

---

### Rule #88: Standarisasi Konfirmasi Pengiriman Form & Pre-Validasi Otomatis (Pre-Validated Form Confirmation Modal)
1. **Latar Belakang & Prinsip UX Bebas "False Confirmation"**:
   - DILARANG memunculkan modal konfirmasi jika masih ada field input pada form yang kosong, belum diisi sesuai ketentuan `required`, atau tidak sesuai format/tipe datanya.
   - Modal konfirmasi hanya boleh muncul apabila seluruh input field di dalam form telah lolos uji validasi awal (*pre-validation check* via `form.checkValidity() === true`).
2. **Perilaku Ketika Ada Input Tidak Valid / Bermasalah**:
   - Jika pengguna menekan tombol "Simpan Data", "Perbaharui Data", atau menekan tombol Enter pada form dan ada input yang gagal validasi:
     1. Modal konfirmasi **DILARANG MUNCUL**.
     2. Layar antarmuka melakukan *smooth scroll* otomatis ke input field paling atas yang bermasalah (`firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' })`).
     3. Kursor otomatis memfokuskan elemen tersebut (`firstInvalid.focus()`) dan memicu tampilan pesan galat validasi bawaan (`reportValidity()`).
3. **Kepatuhan Tampilan Modal Konfirmasi (Rule #59, #74, #76, #80)**:
   - Apabila seluruh input telah valid:
     - Judul modal wajib rata tengah tanpa ikon (`Konfirmasi Simpan Data` atau `Konfirmasi Perubahan Data`).
     - Deskripsi singkat konfirmasi berada di tengah.
     - Bagian bawah hanya berisi maksimal 2 tombol aksi rata tengah:
       - **Batal** di sebelah kiri (abu-abu netral).
       - **Ya, Simpan Data** / **Ya, Perbaharui Data** di sebelah kanan (`bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold shadow-xs`).
     - DILARANG menggunakan tombol silang (X) di pojok kanan atas modal.
4. **Cakupan Universal Sistem**:
   - Berjalan otomatis melalui interceptor terpusat pada file komponen [`confirm-modal.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/partials/shared/confirm-modal.blade.php) yang di-include pada seluruh master layout (`layouts/app.blade.php`, `layouts/admin.blade.php`, dan `layouts/photographer.blade.php`).
   - Pengecualian otomatis (*opt-out*):
     - Form pencarian / filter dengan metode `GET`.
     - Form pesan chat obrolan (`#chat-form`, `chat-send-form`).
     - Form yang secara eksplisit memiliki atribut `data-no-confirm="true"`.


---

### Rule #89: Standarisasi Anti-AI Slop Dokumen Faktur / Invoice (Larangan Mengistimewakan Kode & Eliminasi Jargon Escrow)
1. **Larangan Mutlak Mengistimewakan Kode / ID (Anti-Code Glorification)**:
   - **DILARANG KERAS** menjadikan kode acak sistem, nomor invoice, nomor kontrak, atau ID data apa pun (contoh: INV-20261106-8OKWFS, #KTRK-00014) sebagai tajuk utama dokumen (H1) atau teks paling besar di halaman. Pola ini adalah ciri khas *'AI Slop'* yang salah memahami hierarki informasi.
   - **Kode Hanyalah Metadata Sekunder**: Kode atau nomor invoice wajib berposisi sebagai metadata pendukung dengan tipografi monokrom netral yang tenang (ont-mono text-xs sm:text-sm text-gray-500 font-medium), diletakkan di bawah judul atau di area metadata referensi.
   - **Judul Dokumen yang Benar**: Tajuk utama dokumen keuangan/faktur SELALU bertuliskan entitas formal yang jelas dan ringkas: **'Invoice'** atau **'Faktur Pembayaran'** (	ext-2xl sm:text-3xl font-black text-gray-900 dark:text-white tracking-tight).
   - **DILARANG** memberi warna primer/kontras tinggi (amber, ungu, neon) pada teks kode sistem.

2. **Eliminasi Total Jargon Teknis Finansial ('Escrow' / 'Rekber') dari Antarmuka Klien**:
   - Istilah 'Escrow' atau 'Rekber' adalah terminologi teknis/arsitektur backend, BUKAN copywriting untuk pengguna publik. DILARANG mengobral kata 'Escrow' pada antarmuka faktur/invoice (contoh AI-slop: *'Tagihan Resmi LensMatch Escrow'*, *'100% Dana Garansi Ditahan Aman di Escrow'*, *'Rekening Bersama Escrow'*).
   - Seluruh copywriting wajib menggunakan bahasa manusia yang lugas, terpercaya, dan profesional:
     - Header: Cukup **'Invoice'** atau **'Faktur Pembayaran'**.
     - Rekening Pembayaran: **'Transfer Bank'** dengan nama rekening resmi **'a.n. PT LensMatch Indonesia'** (tanpa embel-embel 'Rekber/Escrow').
     - Status: Cukup **'Lunas'** atau **'Menunggu Pembayaran'** (tanpa slogan marketing klise).

3. **Tata Letak Faktur Standar Industri (Clean Document Sheet & Line-Item Table)**:
   - **Container Lebar Dokumen**: Menggunakan container terstandar **max-w-4xl mx-auto px-4 sm:px-6 lg:px-8** dengan background kartu putih bersih (g-white dark:bg-gray-800 rounded-2xl sm:rounded-3xl border border-gray-200 dark:border-gray-700/80 shadow-sm p-6 sm:p-10).
   - **Flat Editorial 2-Column Info**: Identitas Klien ('Ditagihkan Kepada') dan Fotografer ('Penyedia Jasa') disusun berdampingan secara datar mengandalkan *whitespace* murni, DILARANG menumpuk boks-boks kartu abu-abu terpisah yang kaku.
   - **Tabel Rincian Layanan (*Line-Item Table*)**: Rincian pemesanan disajikan dalam bentuk tabel transaksi horizontal yang rapi (Deskripsi Layanan & Jadwal, Tipe Pembayaran, Durasi, Subtotal).
   - **Rangkuman Total Kanan Bawah**: Rangkuman harga (Subtotal, Biaya Layanan, Total) diletakkan rata kanan di bawah tabel dengan tipografi angka netral tegas (hitam/putih), DILARANG membuat banner blok warna neon raksasa (blok hijau pekat / kuning mencolok) yang memotong layar.
   - **Instruksi Pembayaran Ringkas**: Jika pembayaran belum lunas, sajikan modul instruksi transfer bank yang bersih dan elegan di bawah rincian tagihan.


---

### Rule #90: Standarisasi Sinkronisasi Status Booking, Logika Rating Bintang & Desain Galeri (Anti-AI Slop)
1. **Standarisasi Sinkronisasi Status Antara Role Klien & Fotografer (100% Simetris)**:
   - Status pemesanan sesi pemotretan pada antarmuka Klien dan Fotografer **WAJIB 100% konsisten, simetris, dan identik**:
     - **`Menunggu Konfirmasi`** (`text-amber-500 font-bold`): Menunggu persetujuan jadwal/order fotografer.
     - **`Dikonfirmasi`** (`text-blue-600 font-bold`): Jadwal sesi pemotretan telah disepakati kedua belah pihak.
     - **`Menunggu Konfirmasi Klien`** (`text-blue-600 font-bold`): Fotografer telah mengunggah link album hasil foto, sistem menunggu klien memeriksa dan menyetujui hasil.
     - **`Selesai`** (`text-emerald-600 font-bold`): Klien telah menekan konfirmasi terima hasil pemotretan (`is_validated_client = true`).
     - **`Dibatalkan`** (`text-rose-600 font-bold`): Sesi dibatalkan.
   - **DILARANG KERAS** menampilkan status 'Selesai' di bagian atas kartu jika Klien belum mengonfirmasi penerimaan hasil foto (`is_validated_client == false`).

2. **Logika Bintang Modal Ulasan (Zero Pre-Filled Stars on New Review)**:
   - Ketika modal ulasan dibuka untuk pertama kali (*new review* / `isEdit: false`), seluruh 5 bintang **WAJIB dalam kondisi kosong / abu-abu outline** (`rating: 0, hoverRating: 0`).
   - **DILARANG KERAS** melakukan pra-isi (*pre-filled*) 5 bintang emas langsung saat UI pertama kali terbuka, karena ini merusak objektivitas penilaian dan memicu kebingungan psikologis pengguna.
   - Teks panduan di bawah bintang menampilkan: *'Pilih jumlah bintang kepuasan (1 - 5)'*. Bintang baru berubah menjadi kuning emas dan memunculkan label deskripsi kepuasan saat kursor diarahkan (*hover*) atau bintang diklik (*active*).
   - Tombol simpan data dinonaktifkan (`disabled`) sampai pengguna memilih minimal 1 bintang dan mengetikkan ulasan minimal 5 karakter.

3. **Tata Letak Kartu Galeri Mengikuti Konten (Natural Hug Content & Anti-Dead Space)**:
   - Kontainer grid galeri (`/client/galleries`) wajib menggunakan **`items-start`** (bukan `items-stretch`).
   - Kartu pembungkus galeri wajib menggunakan tinggi alami **`h-auto`** (bukan `h-full flex-col justify-between`) dengan padding proporsional (`p-6 sm:p-7`). Kartu tidak boleh tertarik memanjang secara artifisial mengikuti kartu tetangga yang lebih tinggi.
   - Kotak boks *"Pesan Pengantar Fotografer"* dan *"Konfirmasi Hasil Pemotretan"* wajib menggunakan warna latar abu-abu netral (`bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700`), DILARANG menggunakan warna kuning/amber terang yang memberi kesan *banner promosi/peringatan*.

4. **Eliminasi Banner Status Ganda (Anti-Double UI)**:
   - Apabila pesanan telah selesai dan disetujui (`is_validated_client == true`), **DILARANG KERAS** menampilkan banner hijau tambahan (*'Pesanan Selesai & Dikonfirmasi - Selesai 100%'*) di bawah kartu, karena status *'Selesai'* sudah tercantum jelas pada baris metadata di atasnya. Menampilkan kembali banner tersebut adalah redundansi UI yang mengotori halaman.

5. **Standarisasi Status Pembayaran Tunggal (Eliminasi Teks Persentase Fluff)**:
   - Status pembayaran di seluruh sistem wajib menggunakan istilah baku tunggal: **`Lunas`**, **`Menunggu Pembayaran`**, **`Gagal`**, atau **`DP Terbayar`**.
   - DILARANG mencampurkan status pekerjaan dan keuangan dengan embel-embel matematika (seperti: *'Selesai (Lunas 100%)'*, *'Lunas & Akses Aktif'*, *'Lunas & Terverifikasi'*).


---

### Rule #91: Standarisasi Warna Tombol Aksi Formulir vs Aksi Persetujuan Transaksi (Opsi A - Brand Amber Standard)
1. **Tombol Formulir Simpan & Perbaharui Data (Warna Brand Amber Primer)**:
   - Seluruh formulir di sistem (Profil Pengguna, Ganti Kata Sandi, Admin CRUD, Fotografer Portofolio, Layanan, Profil Studio, dsb.) **WAJIB menggunakan warna Kuning Amber identitas brand LensMatch**:
     - **Tailwind Classes**: `bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs rounded-xl shadow-xs transition cursor-pointer`
     - **Teks Baku**: `Simpan Data` (untuk aksi simpan baru) atau `Perbaharui Data` (untuk pembaruan data yang sudah ada).
   - **Tujuan Arsitektur**: Menjaga kesatuan identitas visual brand LensMatch (kuning-hitam) yang khas di seluruh halaman aplikasi dan modal konfirmasi simpan data.

2. **Pengecualian Khusus Warna Hijau Emerald (Aksi Semantik Persetujuan & Transaksi Positif)**:
   - Warna **Hijau Emerald** (`bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold`) **HANYA dan KHUSUS digunakan** untuk aksi fungsional semantik persetujuan (*approval*), penyelesaian pesanan, dan transaksi keuangan:
     - **`Konfirmasi Terima & Selesaikan`**: Klien menyetujui hasil foto dan merilis dana pembayaran ke mitra fotografer.
     - **`Verifikasi`**: Admin meloloskan verifikasi identitas resmi mitra fotografer.
     - **`Transfer Dana`**: Admin memproses pencairan dana (*payout*) ke rekening bank fotografer.
     - **`Tandai Selesai`**: Siswa menandai penyelesaian materi pembelajaran e-learning.
   - **DILARANG KERAS** menggunakan warna hijau untuk tombol formulir biasa (*input text*, edit profil, edit kategori) guna mencegah kerancuan makna dengan aksi persetujuan finansial/transaksional.

---

### Rule #92: Standarisasi Integritas Encoding Karakter UTF-8 (Larangan Byte-Slicing pada Simbol Multibyte)
1. **Pemisahan Logika Ikon UI vs Data Model**:
   - Atribut teks pada Model Eloquent (seperti `editorial_metrics`) **DILARANG memuat karakter ikon grafis mentah** seperti `★`, `✓`, atau emoji di dalam string data.
   - String teks model hanya boleh berisi data murni (contoh: `'5.0 (6 ulasan)'`). Ikon dekoratif (seperti `<span class="text-amber-500">★</span>`) wajib dirender secara murni di lapisan view Blade.
2. **Larangan Penggunaan `substr()` pada String UTF-8**:
   - Fungsi `substr()` bawaan PHP beroperasi berdasarkan byte, bukan karakter UTF-8. Simbol seperti `★` berukuran 3 byte (`0xE2 0x98 0x85`). Memotong dengan `substr($str, 2)` akan membelah byte di tengah karakter dan menyisakan byte liar (`0x85`), yang memicu browser menampilkan karakter rusak pengganti (*Unicode Replacement Character* ).
   - Seluruh view Blade wajib langsung menampilkan string bersih `{{ $m['text'] }}` tanpa manipulasi pemotongan substring byte.

---

### Rule #93: Standarisasi Tombol Wishlist / Favorit Fotografer & Wajib Feedback Notifikasi
1. **Fungsi & Nilai Kegunaan**:
   - Tombol bookmark pada kartu fotografer adalah fitur **Wishlist / Simpan Favorit** yang menghubungkan klien ke halaman manajerial `Fotografer Favorit Saya` (`/client/favorites`). Tujuannya agar klien yang sedang mengeksplorasi puluhan portofolio dapat menandai mitra yang diminati untuk dibandingkan atau dipesan kemudian tanpa harus masuk ke profil satu per satu.
2. **Peletakan Posisi Kartu (Standar Industri)**:
   - Sesuai konvensi industri marketplace global (Airbnb, Shopee, Tokopedia, Upwork), tombol simpan diletakkan pada **sudut media cover foto kartu** menggunakan kontainer bulat semi-transparan (`w-8 h-8 rounded-full bg-white/95 dark:bg-gray-900/95 shadow-md`).
   - Karena sudut kanan atas telah dialokasikan untuk badge rating `★ 5.0 (6)`, penempatan tombol bookmark di sudut kiri atas adalah posisi yang paling seimbang, ergonomis, dan tidak memakan ruang vertikal kartu.
3. **Wajib Umpan Balik Visual (Anti-Silent Click)**:
   - Setiap kali tombol diklik, sistem **WAJIB memunculkan umpan balik notifikasi instan** (*Toast Notification*) ke layar:
     - Jika berhasil disimpan: *"Fotografer [Nama] berhasil disimpan ke daftar favorit!"* (warna hijau/sukses).
     - Jika dihapus: *"Fotografer [Nama] telah dihapus dari daftar favorit."* (warna hijau/sukses).
     - Jika pengguna bukan berstatus role Klien (misal Admin atau Fotografer): *"Fitur ini khusus untuk akun Klien."* (warna amber/peringatan).
     - Jika belum login: dialihkan langsung ke halaman masuk (*login redirect*).
   - **DILARANG KERAS** membiarkan tombol tertekan secara pasif tanpa feedback visual atau menyembunyikan error HTTP di dalam console secara diam-diam (*silent catch*).


---

### Rule #94: Standarisasi 100% Data Dinamis & Larangan Mutlak Data Palsu/Dummy/Fallback Statis (Zero Dummy Fallback Standard)
1. **Latar Belakang & Masalah Data Fiktif (Anti-Fabricated Data)**:
   - Pengguna baru (misalnya fotografer atau klien yang baru mendaftar) belum mengunggah foto profil, belum menentukan domisili operasional, belum membuat paket layanan, dan belum memiliki ulasan dari klien.
   - Sebelumnya terjadi pelanggaran serius di mana sistem menampilkan data statis buatan / fallback fiktif:
     - Foto avatar otomatis menampilkan foto stok wanita dari Unsplash.
     - Wilayah operasional default menjadi `"Indonesia"`.
     - Kategori spesialisasi default menjadi `"Semua Kategori"`.
     - Rating otomatis menampilkan `★ 4.9` atau `★ 5.0` padahal ulasan masih 0.
     - Harga layanan menampilkan `Mulai Rp 1.500.000` padahal belum ada paket.
     - Formulir rekening payout terisi otomatis nama bank `'BCA'`, nomor `'1234567890'`, dan nama `'Budi Santoso'`.
   - Hal tersebut **DILARANG KERAS SECARA MUTLAK** karena melanggar prinsip keaslian data database dinamis, menimbulkan kebingungan fatal bagi pengguna, dan merusak kredibilitas sistem marketplace.

2. **Larangan Mutlak Fallback Statis (Zero Dummy Fallback Tolerance)**:
   - **DILARANG** menggunakan URL foto pihak ketiga (Unsplash, Pexels, dsb.) sebagai fallback gambar avatar, foto sampul, maupun thumbnail ketika kolom database bernilai `null` atau kosong.
   - **DILARANG** mengembalikan nilai rating numerik palsu (`?? 4.9`, `?? 5.0`, dsb.) pada accessor model atau view Blade untuk entitas yang belum memiliki ulasan.
   - **DILARANG** memasang harga default fiktif (`?? 1500000`, `?? 149000`, dsb.) jika data harga layanan atau kursus belum ditentukan.
   - **DILARANG** mengisi teks wilayah/kota default (`?? 'Indonesia'`, `?? 'Jakarta'`) jika domisili pengguna belum disetel di database.
   - **DILARANG** memberikan nilai awal fiktif pada variabel input formulir (`'BCA'`, dsb.) atau menggunakan teks placeholder yang menyerupai data asli yang telah terisi (`placeholder="1234567890"` atau `placeholder="Budi Santoso"`). Placeholder formulir WAJIB berupa kalimat instruksi yang jelas (misalnya: `placeholder="Masukkan nomor rekening"`, `placeholder="Masukkan nama pemilik rekening"`).
   - **DILARANG** memanipulasi grafik statistik atau metrik bulanan dengan nilai dasar fiktif (seperti `$val = $monthlyRevenue[$idx] ?? 30`). Jika tidak ada omzet, nilai wajib `0` dengan tinggi bar dasar minimum (`3%`).

3. **Standarisasi Representasi Data Kosong (Empty-State Standards)**:
   - **Avatar Pengguna / Fotografer Kosong**: Wajib merender lencana lingkaran inisial nama 2 karakter kapital sesuai **Rule #44** menggunakan komponen `<x-user-avatar>` atau kontainer inisial (`{{ strtoupper(substr($user->nama, 0, 2)) }}`).
   - **Kotak Pratinjau / Input Upload Foto Kosong (Empty State Media Preview Standard)**: Di seluruh sistem (baik profil fotografer, kartu audit verifikasi admin, profil klien, banner lomba, maupun thumbnail materi modul), jika foto belum diunggah atau data gambar kosong, kotak preview wajib menampilkan **ikon kamera outline abu-abu simpel** (`text-gray-400 dark:text-gray-500`) di dalam kontainer bergaris putus-putus netral (`border-dashed border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-800`) tanpa label teks verbal berlebih (seperti 'Kosong', 'Belum ada foto', atau inisial huruf).
   - **Foto Sampul / Portofolio Kosong**: Menampilkan kontainer abu-abu netral dengan ikon kamera outline abu-abu simpel standar.
   - **Wilayah Operasional Kosong**: Wajib menampilkan tanda strip `-` dengan kelas teks redup `text-gray-400 font-medium` (bukan string lokasi umum atau teks hitam tebal).
   - **Spesialisasi Kategori Kosong**: Wajib menampilkan teks murni `Belum diatur` dengan warna redup `text-gray-400 font-medium` (bukan "Semua Kategori").
   - **Rating Fotografer Kosong (0 Ulasan)**: Wajib menampilkan format metrik numerik nol yang konsisten dengan kartu lainnya (`★ 0.0 (0 ulasan) • 0 Sesi Selesai` serta badge ringkas `★ 0 (0)` dengan ikon bintang abu-abu redup `text-gray-300`, bukan label teks `Mitra Baru` / `Baru` maupun rating palsu). Bintang amber aktif hanya ditampilkan bila memiliki minimal 1 ulasan nyata di database.
   - **Paket Layanan Kosong**: Wajib menampilkan teks murni `Belum ada paket` atau `Belum ada paket layanan` (bukan nominal harga default).
   - **Thumbnail Kursus / Materi Kosong**: Menampilkan kontainer netral dengan ikon kamera outline abu-abu simpel standar.


---

### Rule #95: Standarisasi Alur Detail & Verifikasi Mitra Fotografer Admin (`/admin/photographers/{id}`)
1. **Pemisahan Tegas Antara Backoffice Admin vs Etalase Universal Klien**:
   - Modul `Manajemen Fotografer` pada panel Admin (`/admin/photographers`) adalah area operasional backoffice manajerial.
   - **DILARANG KERAS** mengarahkan tombol aksi `Lihat` pada tabel Admin ke halaman publik klien (`/fotografer/{id}`). Halaman publik klien dirancang khusus untuk calon pemesan jasa foto (memuat tombol reservasi, chat, wishlist, dan menyembunyikan data rahasia seperti nomor rekening).
   - Tombol `Lihat` pada tabel admin **WAJIB mengarahkan ke halaman internal Admin**: `route('admin.photographers.show', $id)` dengan layout manajerial `@extends('layouts.admin')`.

2. **Cakupan Informasi Audit Administratif Satu Pintu & Keselarasan Field Profil**:
   - Halaman detail audit `/admin/photographers/{id}` wajib menyajikan data pendaftaran fotografer secara komprehensif mengikuti **Rule #73 (Hybrid Responsive Layout)** dan **WAJIB 100% selaras dengan formulir profil fotografer**:
     1. **Ringkasan Status Akun**: Baris ringkas non-redundan berisi status **`Profil:`** (`Terverifikasi` / `Menunggu` / `Ditolak`), **`Etalase:`** (`Tayang` / `Menunggu` / `Ditolak` / `Belum Diajukan`), tanggal **`Terdaftar`** di sisi kanan, serta daftar **`Belum diisi`** tanpa garis divider hanya bila data profil belum 100% sesuai `RolePhotographer::getMissingProfileFields()` (tanpa mengulang label `Kelengkapan Profil (X/4 bagian)` maupun `100% Lengkap` yang sudah terwakili di tab `Profil Mitra`).
     2. **1. Kontak**: `Nama Lengkap`, `Email`, `Nomor WhatsApp` (dengan tombol chat), `Instagram`.
     3. **2. Domisili & Kategori**: `Provinsi`, `Kota / Kabupaten`, `Alamat`, `Kategori` (badge atau teks `Belum diatur`).
     4. **3. Bio**: Konten deskripsi bio pengalaman mitra (atau teks `Belum diisi`).
     5. **4. Foto Profil & Sampul**: Pratinjau foto profil dan foto sampul rasio 16:9. Jika belum diunggah, kotak audit wajib menampilkan ikon kamera outline abu-abu simpel (`text-gray-400 dark:text-gray-500`) di dalam kotak border dashed netral tanpa inisial huruf teks 'KE' atau kata 'Kosong'.
     6. **5. Rekening Bank**: `Nama Bank`, `Nomor Rekening`, `Nama Pemilik Rekening` (tanpa terminologi asing "Payout" sesuai Rule #70).

3. **Validasi Formulir Profil Fotografer Real-time (Alpine.js Client-Side Standard)**:
   - Mengikuti standar UX pada `signup.blade.php`, formulir profil fotografer (`/photographer/profile`) wajib menggunakan validasi interaktif real-time dengan Alpine.js (`formData`, `touched`, `errors`) pada event `@input` dan `@blur`.
   - Menampilkan teks error merah di bawah input (`text-rose-500 font-bold text-[11px]`) dan border input merah saat invalid.
   - Saat submit, sistem memvalidasi seluruh field sekaligus, membatalkan submit jika ada yang tidak valid, lalu secara otomatis mengarahkan fokus (*focus*) dan menggulir (*smooth scroll*) ke field bermasalah pertama.

4. **Standar Bilah Aksi Keputusan Admin & Form Pelanggaran (Bottom Action Bar - Rule #74 & Rule #91)**:
   - Sisi Kiri: Tombol `Kembali` (abu-abu netral) dan tombol opsional `Lihat Etalase Publik` (hanya muncul jika etalase telah tayang).
   - **Tab 1 (`Profil Mitra` — `status_verifikasi`)**:
     - **Tombol `Hapus`**: Membuka dialog modal form penghapusan fotografer (Rule #59) yang mewajibkan admin memilih **Kategori Pelanggaran** dan menuliskan **Deskripsi Alasan Penghapusan**. Data pelanggaran tersimpan pada database dan tercatat permanen pada `ActivityLog`.
     - **Saat `status_verifikasi === 'menunggu'`**: Menampilkan `Tolak Pendaftaran` (mengubah status ke `ditolak` dengan catatan alasan) dan `Verifikasi Fotografer` (aktif bila `isProfileComplete()`; dinonaktifkan di UI dan diblokir di backend bila belum 100%).
     - **Saat `status_verifikasi === 'terverifikasi'`**: Menampilkan `Tolak Pendaftaran` dan `Batalkan Verifikasi` (mengembalikan status ke `menunggu`).
     - **Saat `status_verifikasi === 'ditolak'` (Alur Ketat Pasca Penolakan)**: Tombol `Verifikasi Fotografer` dan `Tolak Pendaftaran` disembunyikan di UI dan diblokir di backend (`toggleVerification()` & `reject()`). Bilah aksi cukup menampilkan `Menunggu fotografer memperbarui profil.` di samping tombol `Hapus` sampai fotografer menyimpan ulang profilnya.
   - **Tab 2 (`Etalase Tayang` — `status_etalase`)**:
     - **Saat `status_etalase === 'belum_diajukan'`**: Tombol `Setujui Tayang` dan `Tolak Etalase` disembunyikan meskipun 3 syarat etalase sudah 3/3 lengkap; bilah aksi menampilkan status teks netral sampai fotografer menekan `Ajukan Tayang`.
     - **Saat `status_etalase === 'menunggu'`**: Menampilkan `Tolak Etalase` (ketika profil terverifikasi) dan `Setujui Tayang` (aktif bila profil terverifikasi dan 3 syarat etalase lengkap; dinonaktifkan jika syarat belum terpenuhi).
     - **Saat `status_etalase === 'disetujui'`**: Menampilkan `Tolak Etalase` dan `Batalkan Tayang` (mengembalikan status etalase menjadi `belum_diajukan`).
     - **Saat `status_etalase === 'ditolak'` (Alur Ketat Pasca Penolakan)**: Tombol `Setujui Tayang`, `Tolak Etalase`, dan `Batalkan Tayang` disembunyikan di UI dan diblokir di backend (`approveStorefront()` & `rejectStorefront()`). Bilah aksi menampilkan `Menunggu fotografer mengajukan ulang tayang.` sampai fotografer menekan `Ajukan Ulang Tayang`.

5. **Alur Pengajuan Ulang Pasca Penolakan (Auto Re-apply)**:
   - Jika pendaftaran profil ditolak, fotografer langsung melihat satu baris `Catatan Admin: <isi catatan>` di dalam `stage-stepper.blade.php` pada seluruh halaman fotografer tanpa banner ganda. Begitu fotografer memperbaiki profil dan menekan **Perbaharui Data**, sistem mengembalikan `status_verifikasi` ke `menunggu`, membersihkan `catatan_penolakan`, mencatat log aktivitas pengajuan ulang, dan mengirim notifikasi in-app ke Administrator.
   - Jika pengajuan etalase ditolak, fotografer langsung melihat satu baris `Catatan Admin: <isi catatan>` di dalam `stage-stepper.blade.php` serta kartu `Persiapan Tayang Etalase` pada Dashboard. Setelah memperbaiki layanan, portofolio, atau jadwal dan menekan **Ajukan Ulang Tayang**, sistem mengembalikan `status_etalase` ke `menunggu`, membersihkan `catatan_penolakan_etalase`, mencatat log pengajuan ulang, dan mengirim notifikasi in-app ke Administrator.

---

### Rule #96: Standarisasi Proporsi Card Media & Larangan Pemanipulasian Tinggi Seragam (Anti-Artificial Equal Height on Asymmetric Cards)
1. **Penerapan `items-start` pada Grid Multi-Kolom Kartu Asimetris**:
   - Pada tata letak grid kartu dasbor/audit (seperti pada halaman verifikasi `/admin/photographers/{id}` dan formulir profil mitra), baris grid **WAJIB menggunakan `items-start`**.
   - **DILARANG KERAS** memaksakan perataan tinggi seragam (*stretch equal-height*) secara artifisial antar kartu yang memiliki kepadatan konten berbeda. Contoh: **Card 3 (Bio)** memiliki teks deskripsi ringkas, sedangkan **Card 4 (Foto Profil & Sampul)** memuat preview visual vertikal yang tinggi. Card 3 (Bio) **TIDAK BOLEH** dipaksa melar/molor dengan ruang kosong bawah hanya demi menyamakan tinggi Card 4 di sebelahnya. Biarkan Card Bio tampil natural dan ramping.

2. **Tata Letak Vertikal untuk Card Media (Foto Profil & Sampul)**:
   - Penempatan elemen media visual (Foto Profil dan Foto Sampul) di dalam Card Media **WAJIB menggunakan tata letak VERTIKAL (*stacked layout*)**, bukan horizontal berhimpitan yang membuat preview tampak gepeng (*squished*).
   - **Foto Profil**: Menampilkan pratinjau persegi (1:1) minimal `w-24 h-24` (atau `w-28 h-28`).
   - **Foto Sampul**: Menampilkan pratinjau lanskap proporsional rasio **16:9** yang tinggi dan jelas (minimal `h-44 sm:h-52` pada admin audit, dan `h-44 sm:h-48` pada profil fotografer), sehingga foto lanskap atau kotak kosong kamera tidak tertekan (*gepeng*).

3. **Standar Ruang Bernapas Kartu Pembungkus (*Comfortable Card Padding & Height*)**:
   - Kartu pembungkus media foto wajib memiliki padding yang lega (`p-6` atau `p-6 sm:p-7`) dan ruang vertikal yang lapang (*spacious*), menghindari kesan sumpek (*cramped*) antar elemen foto dan tombol upload/aksi.

---

### Rule #97: Standarisasi Pratinjau Modal (Lightbox) Interaktif pada Seluruh Input Field & Kotak Pratinjau Media Gambar (Interactive Image Field Lightbox Standard)
1. **Prinsip Utama & Validasi Kejelasan Gambar Satu Pintu**:
   - Setiap elemen formulir atau kartu audit yang menampilkan unggahan media visual (seperti Foto Profil, Foto Sampul, Banner Lomba, Bukti Transfer, dan sejenisnya) **WAJIB menyediakan fitur pratinjau modal (*lightbox dialog*) interaktif yang dapat diklik langsung**.
   - Tujuannya adalah agar pengguna (Fotografer, Klien, maupun Admin Audit) dapat memeriksa keaslian, ketajaman, dan kesesuaian berkas resolusi penuh yang baru saja dipilih/diunggah sebelum menekan tombol simpan, atau memeriksa berkas yang telah tersimpan di database secara mendalam.

2. **Kondisi Aktivasi Logika Interaktif (*Trigger Condition*)**:
   - **Kondisi Aktif (Terdapat Berkas/Foto)**:
     - Jika field telah memiliki foto (baik foto yang sudah tersimpan di database maupun file lokal baru yang dipilih via *client-side FileReader base64/data-URL*):
       - Kotak pratinjau wajib menampilkan kursor pointer/zoom (`cursor-pointer group`).
       - Memiliki efek interaktif saat di-hover (efek perbesaran halus `group-hover:scale-105` dan *overlay* gelap elegan dengan teks/ikon kaca pembesar `Pratinjau` atau `Klik untuk Pratinjau Foto`).
       - Saat diklik, sistem membuka modal pratinjau resolusi tinggi di tengah layar.
   - **Kondisi Kosong (*Empty State*)**:
     - Jika field masih kosong (menampilkan ikon kamera outline abu-abu netral sesuai Rule #94):
       - Kotak **DILARANG KERAS** memicu modal *lightbox* kosong.
       - Interaksi klik diarahkan murni untuk memicu *file picker* (pemilihan file) atau tombol unggah.

3. **Standar Desain, UX & Aksesibilitas Modal Lightbox**:
   - **Latar Belakang (*Backdrop*)**: Menggunakan lapisan gelap elegan dengan blur (`bg-black/90 backdrop-blur-md`).
   - **Kontrol Penutupan (*Dismissibility*)**:
     - Tombol silang (*close button*) bulat di pojok kanan atas dengan kontras tinggi (`w-11 h-11 rounded-full bg-white/10 hover:bg-white/25 text-white`).
     - Menutup otomatis saat tombol `Escape` ditekan (`@keydown.escape.window`).
     - Menutup saat pengguna mengklik area luar gambar (`@click.outside`).
   - **Proporsi Gambar**: Menampilkan gambar resolusi penuh secara proporsional tanpa distorsi (`max-w-full max-h-[80vh] object-contain rounded-2xl shadow-2xl border border-white/10 select-none`).
   - **Keterangan (*Caption Badge*)**: Menyertakan lencana keterangan semitransparan di bawah gambar yang memuat judul field (misal: "Foto Profil", "Foto Sampul") dan petunjuk penutupan modal.

---



---

### Rule #98: Standarisasi Formulir Reservasi Sesi Foto (Urutan Langkah Logis, Anti-Forced Selection & Real-Time Alpine Calculation)
1. **Larangan Pemaksaan Paket Otomatis (Anti-Forced Package Selection)**:
   - Ketika calon klien menekan tombol utama **"Reservasi"** dari profil fotografer atau daftar favorit tanpa membawa parameter `service_id`, controller (`BookingController@create`) **DILARANG memaksakan paket pertama secara sepihak** (`$service = $allServices->first()`).
   - Variabel `$service` wajib bernilai `null` apabila `service_id` tidak disertakan di URL, memberikan keleluasaan penuh bagi klien untuk memilih paket yang diminati pada formulir.
2. **Urutan 4 Seksi Formulir Kiri yang Logis & Terstruktur**:
   - **Seksi 1 (Paling Atas)**: **Pilih Paket Layanan**. Menampilkan seluruh paket pemotretan yang disediakan fotografer dalam bentuk kartu seleksi interaktif (radio button, nama paket, tarif harga, deskripsi, dan rincian fasilitas).
   - **Seksi 2**: **Jadwal Pelaksanaan Sesi Foto**. Pemilihan tanggal sesi foto dan jam mulai sesi.
   - **Seksi 3**: **Lokasi Pemotretan**. Pemilihan wilayah administrasi (Provinsi, Kota/Kabupaten via AJAX dinamis) dan alamat lengkap venue acara.
   - **Seksi 4**: **Catatan Khusus**. Catatan tema, busana, preferensi konsep, atau instruksi khusus untuk fotografer.
3. **Reaktivitas Kolom Kanan (Ringkasan Pesanan Tanpa Reload / Zero-Reload Reactive State)**:
   - Sebelum paket dipilih (`!selectedServiceId`): Kolom kanan menampilkan panduan ramah (*"Pilih Paket Layanan: Silakan tentukan paket layanan sesi foto pada formulir di sebelah kiri terlebih dahulu"*), total bayar Rp 0, dan tombol aksi dinonaktifkan (*disabled*).
   - Setelah paket dipilih: Rincian paket terisi otomatis, fitur tambahan opsional (*add-ons*) sesuai paket langsung muncul, dan simulasi skema pembayaran (Bayar Penuh vs DP 30%) terkalkulasi secara instan via Alpine.js.
4. **Kepatuhan Anti-AI Slop & Zero Dividers**:
   - **Tanpa Breadcrumb Palsu (Rule #72)**: Menghapus custom breadcrumb navigasi palsu di atas judul halaman.
   - **Zero Dividers (Rule #67)**: Tidak ada garis pembatas horizontal (`0 border-t / border-b`) yang membelah kartu di dalam seksi input maupun ringkasan tagihan.
   - **Eliminasi Simbol `&` Redundan (Rule #70)**: Menggunakan tajuk ringkas **`"Ringkasan Pesanan"`** (bukan *"Ringkasan Pesanan & Tarif"*).
   - **Tombol Brand Amber LensMatch (Rule #91)**: Menggunakan `bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold shadow-md`.

---

---

### Rule #99: Standarisasi Mini Kalender Interaktif & Time-Slot Matrix Chips (Multi-Booking & Jeda Istirahat / Sesi Estafet)
1. **Urutan Alur Alami Pemilihan Jadwal (Tanggal Dahulu $\rightarrow$ Baru Jam)**:
   - Pemilihan slot ketersediaan waktu mutlak mengikuti alur: **Klien memilih tanggal sesi terlebih dahulu**, baru kemudian sistem mengevaluasi ketersediaan jam operasional fotografer pada tanggal tersebut.
2. **Mini Kalender Bulan Interaktif (Seksi 2 Formulir)**:
   - Menggunakan mini kalender bulan berjalan (Senin s/d Minggu) yang proporsional di dalam Seksi 2:
     - Navigasi bulan menggunakan single chevron modern minimalis (`<` dan `>`) sesuai Rule #72.
     - Tanggal lampau dan tanggal libur seharian penuh fotografer secara otomatis diabukan (*grayed out*) dengan label mikro `Libur`.
     - Tanggal hari ini ditandai teks bersih `(Hari Ini)` tanpa bulatan dot badge (Rule #67).
     - Tanggal aktif dapat diklik dan disorot dengan highlight amber LensMatch (`ring-2 ring-amber-400 bg-amber-400 text-gray-950 font-black`).
3. **Time-Slot Matrix Chips (Penanganan Multi-Booking & Istirahat Siang)**:
   - Tepat di bawah kalender mini, kandidat jam mulai sesi disajikan dalam bentuk kartu chip grid yang tenang dan terstruktur (bukan dropdown sempit).
   - Setiap chip jam memiliki 4 status visual yang bersih dan bebas dari warna pelangi (*anti-AI slop*):
     - **Tersedia**: Kartu putih bersih yang dapat diklik (label hijau `Tersedia`).
     - **Terpilih**: Solid amber LensMatch (`bg-amber-400 text-gray-950 font-extrabold shadow-xs`).
     - **Terisi**: Jika slot jam berbenturan dengan pesanan klien lain yang sudah aktif di hari tersebut (`opacity-50 border-dashed cursor-not-allowed`, label abu-abu `Terisi`).
     - **Istirahat / Tutup**: Jika slot jam berbenturan dengan jam istirahat atau penutupan parsial fotografer (label abu-abu `Istirahat` atau `Tutup`).
4. **Perhitungan Durasi Dinamis di Backend**:
   - Backend (`BookingController@store`) menghitung `$bookingEnd` secara dinamis sesuai durasi paket (`+$durasi hours`), dan memeriksa benturan interval waktu (`slotStart < otherEnd && slotEnd > otherStart`) baik terhadap booking klien lain maupun terhadap jam istirahat manual fotografer.





---

---

### Rule #100: Standarisasi Master Card Profil Fotografer & Harmonitas Warna Kartu Dalam (Anti-Gray Tint, Zero Shadow & Skala Tipografi Proporsional)
1. **Struktur Master Card Terpadu (Unified Master Profile Card)**:
   - Profil fotografer (`/fotografer/{id}` / `resources/views/public/photographers/show.blade.php`) dibungkus oleh satu kartu induk besar (*single master card*) yang menaungi seluruh informasi profil atas (foto, biodata, spesialisasi, 6 metrik studio, tombol aksi) serta navigasi tab dan seluruh konten di bawahnya (Paket Layanan, Galeri Portofolio, Kalender Ketersediaan, Ulasan Klien, Tanya Jawab).
   - Card induk memiliki margin dan padding yang lega (`p-8 sm:p-10 lg:p-12 space-y-10 sm:space-y-12`) agar tampilan memiliki ruang napas visual yang nyaman dan berwibawa.
2. **Harmonitas Warna Kartu Dalam (Anti-Gray Tint & Warna Seragam dengan Card Luar)**:
   - Seluruh kartu anak di dalam tab (kartu paket layanan, album portofolio, kontainer kalender ketersediaan, ringkasan dan item ulasan klien, serta accordion FAQ) **DILARANG MENGGUNAKAN LATAR BELAKANG ABU-ABU** (`bg-gray-50`, `bg-gray-50/70`, `bg-gray-750/70`).
   - Warna latar belakang kartu anak **WAJIB DISAMAKAN PERSIS DENGAN KARTU INDUK DI LUARNYA**: menggunakan warna putih bersih `bg-white dark:bg-gray-800`.
   - Garis tepi (*border*) menggunakan warna halus yang seragam (`border border-gray-200 dark:border-gray-700`) sehingga kartu anak menyatu secara elegan tanpa efek kotak kotor bertumpuk.
3. **Pemberantasan Shadow Internal (Zero Shadow pada Kartu Dalam)**:
   - Seluruh kartu anak di dalam tab **DILARANG MENGGUNAKAN SHADOW** (`shadow-none`, menghapus `shadow-xs`, `shadow-sm`, `shadow-md`, `shadow-2xs`).
4. **Skala Tipografi Proporsional & Berani (Proportional Bold Typography)**:
   - Nama fotografer diperbesar menjadi `text-3xl sm:text-4xl font-black text-gray-900 dark:text-white tracking-tight`.
   - Spesialisasi dan bio menggunakan skala `text-sm sm:text-base font-bold text-amber-600 dark:text-amber-400` dan `text-sm sm:text-base text-gray-600 dark:text-gray-300 leading-relaxed`.
   - 6 metrik profil studio (Lokasi, Tarif, Pengalaman, Portofolio, Bergabung, Rating Ulasan) memiliki label `text-xs sm:text-sm font-semibold text-gray-400 dark:text-gray-500` dan angka/nilai tebal `text-base sm:text-lg lg:text-xl font-black text-gray-900 dark:text-white`.
   - Tombol Reservasi (`px-8 py-3.5 text-sm sm:text-base font-extrabold`), Chat (`px-7 py-3.5 text-sm sm:text-base font-bold`), dan Bookmark Favorit (`w-12 h-12`).
   - Judul tab `<h2>` diperbesar menjadi `text-2xl sm:text-3xl font-black text-gray-900 dark:text-white tracking-tight mb-6 sm:mb-8`.
   - Elemen teks pada kartu paket, galeri, kalender, ulasan, dan FAQ ditingkatkan proporsinya agar mudah dibaca dan tegas.
5. **Kepatuhan Anti-AI Slop & Zero Redundant Symbols (Rule #70 & Rule #67)**:
   - Tanpa simbol `&` redundan (misal: "Rating Ulasan", "Fasilitas Layanan").
   - Nol garis pemisah internal (`0 border-t / border-b`) yang memotong kartu.


---

---

### Rule #101: Standarisasi Proteksi Hak Cipta & Watermark Karya Fotografer (Anti-AI Slop: Zero Badge, Zero Box & Murni Tipografi)
1. **Larangan Mutlak Menempelkan Badge / Box Kapsul di Atas Foto Karya**:
   - Menempelkan elemen kotak badge atau pill kapsul mengambang dengan latar belakang gelap dan border di atas foto karya fotografi adalah karakteristik buruk (*AI slop*) yang merusak estetika karya seni fotografer dan melanggar prinsip platform (*Dilarang Keras Menempelkan Badge di Atas Gambar/Foto*).
   - Watermark hak cipta mutlak berwujud **Tipografi Murni Bersahaja (*Pure Typography*)** tanpa wadah kotak (*zero background box*), tanpa garis border kotak (*zero border*), dan tanpa pill kapsul (*zero pill badge*).
2. **Standar Watermark Fisik Backend (`WatermarkService`)**:
   - Pada saat fotografer mengunggah berkas portofolio asli (`PortfolioController@store` & `@update`), `WatermarkService` membakar stempel teks tipografi semi-transparan (`© LensMatch • [Nama Fotografer]`) langsung ke dalam piksel foto dengan bayangan halus (1px shadow offset) di sudut kanan bawah, tanpa menggambar kotak atau border persegi panjang (`no imagefilledrectangle` dan `no imagerectangle`).
3. **Standar Watermark Overlay Frontend (Blade & Lightbox Modal)**:
   - Pada galeri portofolio publik (`show.blade.php`), setiap foto portofolio (baik foto unggahan lokal maupun sampel seeder) dilindungi oleh teks hak cipta tipografi murni:
     `© LensMatch • {{ $photographer->nama }}`
   - Menggunakan kelas teks elegan: `select-none pointer-events-none text-xs sm:text-sm font-semibold tracking-wider text-white/75 drop-shadow-[0_1px_2px_rgba(0,0,0,0.85)] z-10`.
   - Dilengkapi proteksi interaksi gambar: `draggable="false"` dan `oncontextmenu="return false;"` untuk mencegah pengunduhan sembarangan via drag atau klik kanan.
4. **Pencegahan Tanda `&` Redundan (Rule #70)**:
   - Format pemisah nama menggunakan bullet bersih `•` atau `&bull;`, bebas dari tanda `&` redundan.


---

---

### Rule #102: Standarisasi Kartu Paket Layanan Profil Fotografer (Full-Width Spacious Card Layout & Anti-Narrow Void)
1. **Pemberantasan Grid Sempit 3-Kolom yang Terasa Sesak (*Anti-Narrow Void*)**:
   - Menempatkan kartu paket layanan pemotretan ke dalam grid 3-kolom kaku (`lg:grid-cols-3`) ketika fotografer memiliki 1 atau 2 paket layanan membuat kartu terjepit di sudut sempit (~350px) dengan ruang kosong menganga sebesar 66% di sebelah kanan. Teks benefit yang panjang terpaksa terbungkus berkali-kali sehingga terlihat seperti struk belanja mini yang sesak, sempit, dan tidak profesional.
2. **Tata Letak Kartu Utuh Membentang Penuh (*Full-Width Stacked Cards*)**:
   - Selaras dengan standar kartu album di Tab Galeri Portofolio (Rule #75), Tab Kalender (Rule #67), dan Tab Ulasan Klien, setiap paket layanan disajikan dalam kartu besar yang membentang penuh (**`w-full bg-white dark:bg-gray-800 p-8 sm:p-10 lg:p-12 rounded-3xl border border-gray-200 dark:border-gray-700 shadow-none hover:border-amber-400 dark:hover:border-amber-500 transition`**).
   - Jika terdapat lebih dari 1 paket layanan, kartu-kartu tersusun secara vertikal (*stacked*) dengan jeda lapang `space-y-8 sm:space-y-10`.
3. **Struktur Internal Dua Kolom Seimbang (5:7 Ratio Grid)**:
   - **Kolom Kiri (`lg:col-span-5 space-y-5`)**:
     - Kicker kategori paket (`PAKET SESI`).
     - Nama paket pemotretan (`text-2xl sm:text-3xl font-black text-gray-900 dark:text-white leading-tight`).
     - Deskripsi narasi paket (`text-sm sm:text-base text-gray-600 dark:text-gray-300 leading-relaxed`).
     - Tarif harga sesi (`text-3xl sm:text-4xl font-black text-gray-900 dark:text-white` dengan label `/ sesi pemotretan`).
   - **Kolom Kanan (`lg:col-span-7 space-y-4`)**:
     - Tajuk ringkas: `Fasilitas Layanan Termasuk:`.
     - Daftar benefit/fasilitas disusun rapi dalam **grid 2-kolom (`grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5`)**, memberikan ruang horizontal yang sangat lega sehingga nama fitur panjang tidak terpotong atau terdesak.
4. **Kepatuhan Anti-AI Slop**:
   - Nol garis divider pembatas (`0 border-t / border-b`).
   - Nol badge/box tambahan yang melayang.
   - Tanpa tanda `&` yang redundan.
   - Tetap mematuhi Rule #66 Point 7 (murni informasional tanpa tombol booking internal di dalam kartu paket).


---

---

### Rule #103: Standarisasi Header Editorial Profil Kreator (Anti-AI Slop: Eliminasi Formulir Spek Kaku 6-Box & Hierarki Typografi Human-Crafted)
1. **Pemberantasan Formulir Spek Database Kaku (Anti-Spec Sheet)**:
   - Pola AI slop yang menyajikan identitas kreator dalam bentuk matriks 6-kotak data bertingkat (`Lokasi Studio`, `Tarif Layanan`, `Pengalaman Studio`, `Portofolio`, `Bergabung Sejak`, `Rating Ulasan`) dengan label abu-abu kecil dan angka tebal seragam dihapus total. Pola tersebut merusak citra seniman visual dan menyerupai formulir pendaftaran barang atau tabel database ERP.
2. **Hierarki Typografi Editorial (Human-Crafted Hierarchy)**:
   - **Nama Studio / Fotografer**: Ditampilkan dominan dan berwibawa (`text-3xl sm:text-4xl lg:text-5xl font-black text-gray-900 dark:text-white tracking-tight leading-none`).
   - **Tagline Spesialisasi**: Teks tipografi murni amber yang tegas (`text-sm sm:text-base font-bold text-amber-600 dark:text-amber-400`).
   - **Baris Reputasi Terpadu (*Inline Trust Signals Bar*)**:
     Seluruh sinyal reputasi penting (Kota Lokasi, Rating Bintang Ulasan, Sesi Selesai yang benar-benar terkonfirmasi `status_contract === 'completed'` serta `is_validated_client === true`, dan Jumlah Portofolio Karya) disatukan dalam satu baris horisontal yang tenang, dipisahkan bullet murni `•` tanpa kotak kaku (dilarang menghitung klaim pengalaman dari tanggal daftar `created_at` per **Rule #104**):
     `Kota Jakarta Selatan • ★ 5.0 (2 ulasan) • 12 Sesi Selesai • 3 Portofolio Karya`
   - **Narasi Bio Studio**: Mengalir alami dengan font proporsional (`text-sm sm:text-base text-gray-600 dark:text-gray-300 leading-relaxed max-w-3xl`).
3. **Penyatuan Komersial Tarif & Tombol Aksi (The Commercial Anchor)**:
   - Tarif harga awal (`Mulai Dari Rp ... / sesi pemotretan`) disatukan berdampingan secara seimbang di baris bawah dengan tombol aksi utama: `Reservasi` (amber brand), `Chat` (tombol netral berikon), dan `Bookmark Favorit`.
4. **Proporsi Foto Potret Alami**:
   - Foto potret menggunakan rasio potret ideal **4:5** (`w-full sm:w-[280px] lg:w-[320px] aspect-[4/5] rounded-3xl object-cover border border-gray-200 dark:border-gray-700 shadow-none`), tidak lagi ditarik melar secara artifisial dengan CSS `self-stretch`.

---

### Rule #104: Standar Penanganan Data Nullable (Sebagian Besar Data Boleh Kosong & Larangan Keras Rekayasa Data Fallback Fiktif)
1. **Prinsip Utama Ketersediaan Data (Data Boleh Kosong / Nullable)**:
   - Sebagian besar data di sistem LensMatch diperbolehkan kosong (*nullable*). Tidak semua entitas pengguna atau mitra memiliki nomor telepon, kota domisili, tautan sosial media, ulasan, atau foto profil sejak awal pendaftaran.
   - Sistem wajib menangani data kosong dengan aman (*null-safe*) tanpa menyebabkan error, dan **DILARANG KERAS** memaksakan nilai rekaan/palsu hanya untuk mengisi kekosongan tampilan.
2. **Larangan Keras Mengarang Nilai Fallback Fiktif**:
   - **Dilarang keras** mengarang nama fiktif seperti `'Client LensMatch'`, `'Admin LensMatch'`, `'Instruktur LensMatch'`, atau sejenisnya jika nama asli tidak ada. Jika nama pengguna kosong, gunakan fallback netral berbasis akun (misalnya `auth()->user()->nama ?? auth()->user()->email` atau `'Pengguna Komunitas'`).
   - **Dilarang keras** mengarang lokasi default seperti `'Indonesia'` atau `'Wilayah'` jika kota/provinsi belum diisi. Tampilkan tanda hubung (`-`).
   - **Dilarang keras** menyuntikkan nomor telepon dummy (seperti `'08123456789'` atau `'081234567890'`) ke database atau placeholder formulir.
   - **Dilarang keras** memaksakan rating agregat `'5.0'` atau sejenisnya jika sistem belum memiliki data ulasan di database. Jika belum ada ulasan, tampilkan tanda hubung (`-`) atau keterangan jujur (*"Belum ada ulasan"*).
3. **Standar Formulir Input Bersih (Zero Pre-Filled Fictitious Defaults)**:
   - Pada halaman pembuatan data baru (*create form*), seluruh input wajib berawal kosong (`value="{{ old('field') }}"`), bukan terisi otomatis teks fiktif (seperti `'Landscape & Alam'`, `'LensMatch Official'`, `'Rp 15.000.000'`, `'BCA'`, atau jam `'15:00'`).
   - Placeholder input wajib bersifat instruksional (misalnya `placeholder="Masukkan nama lengkap"`, `placeholder="Contoh: 08xxxxxxxxxx"`), dilarang mensimulasikan data dummy yang tampak nyata.
4. **Pembersihan Residu Template Mockup**:
   - Seluruh kode statis bawaan template (seperti mockup `randomuser@pimjo.com`, `Testing Chowdhury`, nomor kontrak statis `CTR-2026-08129`, atau harga hardcoded) wajib dibersihkan total dari basis kode produksi.

---

### Rule #105: Standar Custom Alpine.js Time Dropdown, Tata Letak Baris Flat, dan Default Switch Jadwal Libur
1. **Larangan Mutlak `<input type="time">` Bawaan Browser**:
   - Seluruh pemilih jam (`<input type="time">`) pada halaman produksi wajib digantikan oleh komponen standar [`x-time-dropdown`](file:///d:/IT%20DEPT%20AMAYI/LARAVEL/marketplace-photographers/resources/views/components/time-dropdown.blade.php), agar tampilan trigger dan daftar pilihan seragam total dengan Custom Alpine.js Floating Dropdown LensMatch (**Rule #46**) dan terbebas dari popup biru bawaan OS Windows.
   - **Spesifikasi Komponen**:
     - Panel dropdown memiliki lebar lapang **`w-[224px] sm:w-[240px]`** dengan dukungan prop `align` (`left`, `right`, `md-right`) dan grid 2 kolom berdampingan (**Kolom Kiri: Jam `00`–`23`** | **Kolom Kanan: Menit `00`–`59`**), masing-masing memakai scroll ramping `.custom-scrollbar` (`max-h-56`) untuk membentuk nilai 24 jam format `HH:mm`.
     - Tombol trigger menggunakan `rounded-xl border text-xs font-bold flex items-center justify-between gap-2` dengan ikon chevron berotasi dan focus/open ring amber.
     - Menu dropdown melayang mengikuti **Rule #46** (`bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700/80`) dan **bebas dari garis pembatas/divider internal** (`0 divide-x`, `0 border-b` pada header kolom maupun antar-opsi; opsi memakai `rounded-lg`). Saat menu terbuka, scroll kolom Jam memposisikan jam terpilih (`08` saat kosong) dan kolom Menit memposisikan menit terpilih (`00` saat kosong) di tengah tanpa menggeser scroll halaman, disertai efek hover dan sorotan aktif amber lembut (`bg-amber-50 text-amber-600 font-extrabold`).
     - Mengirim nilai `HH:mm` ke backend melalui hidden `<input>` atau Alpine model binding (misal `form.jam_mulai`, `offerForm.booking_time`). Untuk form di dalam modal, kontainer modal wajib memakai `overflow-visible` agar dropdown jam tidak terpotong batas modal.
   - **Cakupan Halaman Produksi**:
     - `resources/views/photographer/availability/index.blade.php` (Jam Buka & Jam Tutup mingguan).
     - `resources/views/photographer/availability/create.blade.php` (Dari Jam & Sampai Jam).
     - `resources/views/photographer/chat/index.blade.php` & `resources/views/public/chat/index.blade.php` (Jam Mulai pada modal penawaran khusus).
2. **Standar Tata Letak Baris Flat & Copywriting Ringkas Jadwal Mingguan**:
   - Daftar 7 hari pada `photographer/availability/index.blade.php` **dilarang** memakai kotak kartu bertumpuk abu-abu (`card-in-card` `bg-gray-50`) maupun pola `justify-between` penuh yang menciptakan ruang kosong lebar di tengah baris.
   - Wajib disajikan sebagai baris flat putih bersih dengan pemisah tipis (`divide-y divide-gray-100 dark:divide-gray-700/60`) dan grid 3-kolom seimbang (`md:grid-cols-12`), tanpa garis `border-b` di bawah tajuk kartu (**Rule #41 & #58**), status ditempatkan sebaris di samping nama hari (*"Senin (Buka)"* atau *"Senin (Libur)"* dengan ukuran font seragam), serta menggunakan label ringkas tanpa duplikasi simbol (`"Jadwal"`, `"Tanggal Libur"`, `"Jam Operasional"`, `"24 Jam"`).
3. **Standar Default Switch Jadwal Mingguan (`is_open = false` / Libur)**:
   - Seluruh switch hari operasional bawaan pada `PhotographerWorkingHour::initializeDefaults()` dan skema `photographer_working_hours.is_open` **wajib default `false` (Libur / OFF)** sampai fotografer mengaktifkan sendiri hari bukanya (*opt-in*, sesuai **Rule #94 & #104**), kecuali pada akun demo seeder (`initializeDefaults(..., true)`).
   - Menyimpan formulir jadwal dengan seluruh 7 hari nonaktif (`0` hari buka) menyimpan `jadwal_dikonfirmasi = false`, menampilkan pesan peringatan `warning`, serta menonaktifkan etalase sementara lewat `syncStorefrontIntegrity()`.
4. **Standar Tabel Tanggal Libur, Siklus Riwayat Tanggal Lewat, Filter, dan Pagination (`photographer/availability/index.blade.php` Tab `overrides`)**:
   - Tabel `Tanggal Libur` wajib mematuhi standar toolbar seragam (**Rule #15–#18, #45, #46, #107**): dropdown filter periode (`Semua Periode`, `Aktif (Mendatang)`, `Riwayat (Sudah Lewat)`), dropdown tipe penutupan (`Semua Tipe Libur`, `Libur Seharian`, `Tutup Jam Tertentu`), dropdown urutan (`Tanggal Terdekat`, `Tanggal Terjauh`, `Terbaru Ditambahkan`, `Terlama Ditambahkan`) di sisi kiri, serta live search di sisi kanan dengan engine `dashboardLiveTable()` (`#dashboard-table-container` dan `#dashboard-stats-container`), serta menggunakan partial pagination standar `partials.dashboard.pagination` setiap `$availabilities->total() > 0`.
   - Data tanggal libur di database (`photographer_availability`) tidak terhapus otomatis ketika tanggal telah terlewati agar riwayat penutupan tetap tercatat; seluruh query pemesanan publik otomatis hanya mempertimbangkan `tanggal >= today`, sedangkan di tabel kelola fotografer baris bertanggal `< hari ini` otomatis berstatus **`Sudah Lewat`** dengan tombol **`Hapus`** (soft delete) agar fotografer dapat membersihkan riwayat lama.

---

### Rule #106: Standarisasi Tipografi Seragam `text-xs` Isi Tabel (`<tbody>` / `<td>`), Daftar Ringkas Fasilitas Layanan, dan Proporsi Header `<th>` Tabel FAQ
1. **Keseragaman Ukuran Font `text-xs` di Seluruh Sel Isi Tabel (`<tbody>` / `<td>`)**:
   - Seluruh teks judul sel, nama layanan, nominal harga/kontrak, kode referensi, subteks keterangan, status teks, tombol teks inline, serta teks empty state di dalam `<tbody>` pada seluruh tabel antarmuka aplikasi (Admin, Fotografer, Klien, partial `table-01.blade.php`, dan faktur online `public/booking/invoice.blade.php`) **WAJIB menggunakan ukuran font seragam `text-xs`**.
   - **DILARANG** memberi ukuran yang melonjak lebih besar pada salah satu kolom (`text-sm`, `text-base`, `text-xs sm:text-sm`, `text-theme-sm`) maupun memperkecil subteks (`text-[10px]`, `text-[11px]`). Hierarki visual antarbaris di dalam sel tabel murni dibangun menggunakan ketebalan huruf (`font-bold`, `font-extrabold`, `font-black`) dan kontras warna (`text-gray-900 dark:text-white` vs `text-gray-500 dark:text-gray-400`).
   - Pengecualian terbatas pada template dokumen cetak A4 PDF (`photographer/reports/export.blade.php`) yang memang memerlukan tipografi terkompresi agar 8 kolom muat dalam satu lebar kertas cetak.
2. **Daftar Fasilitas Layanan Ringkas tanpa Pill Kapsul (`photographer/services/index.blade.php`)**:
   - Rincian fasilitas pada tabel Paket Layanan disajikan sebagai baris teks ringkas dipisah pemisah titik tengah (`•`) berukuran `text-xs text-gray-500 dark:text-gray-400 leading-relaxed` (`{{ $srv->details->pluck('nama_fitur')->implode(' • ') }}`), menggantikan tumpukan pill badge kapsul abu-abu di dalam kolom tabel.
3. **Proporsi Alami Header `<th>` pada Tabel Tanya Jawab Fotografer (`photographer/faqs/index.blade.php`)**:
   - Menghapus fraksi lebar kolom paksa yang timpang (`w-1/12`, `w-5/12`, `w-4/12`) pada header `<th>` halaman `photographer/faqs/index.blade.php` sehingga kelima kolom (*Urutan*, *Pertanyaan*, *Jawaban Singkat*, *Status*, *Aksi*) membagi ruang horizontal secara alami dan proporsional selaras patokan tabel sistem (`admin/photographers/index.blade.php`), dilengkapi `whitespace-nowrap` pada header dan `<p class="line-clamp-2">` di dalam sel jawaban agar batas baris tidak mematahkan layout `<td>`. Proporsi `<th>` pada tabel halaman lain yang sudah seimbang dibiarkan utuh tanpa modifikasi.

---

### Rule #107: Standar Card Tabel Tanpa Garis Divider Toolbar (`0 border-b` Toolbar & `0 space-y-4`)
- Selaras dengan **Rule #41 & Rule #58** (prinsip anti-divider berlebih), seluruh tampilan card tabel data (*Data Table Card*) di panel Admin, Fotografer, dan Klien **dilarang** menampilkan garis pembatas `border-b` pada kontainer toolbar/judul kartu di atas tabel serta **dilarang** memberi celah jarak vertikal `space-y-4` (maupun `space-y-0` yang tidak diperlukan) pada pembungkus luar card tabel.
- Toolbar (`p-4` / `p-5`) menyatu langsung dengan pita `<thead>` agar tampilan card tabel bersih, utuh tanpa garis ganda, dan konsisten di seluruh sistem (20 file view tabel / 21 kartu tabel). Garis pemisah tetap hanya digunakan secara minimal pada batas bawah `<thead>` (`border-b border-gray-200 dark:border-gray-700`) dan pemisah baris `<tbody>` (`divide-y`).

---

### Rule #108: Standar Kontrol Radio/Checkbox Amber serta Stepper Satu Baris Fotografer
1. **Pelarangan Warna Biru Bawaan Browser pada Radio/Checkbox (`#0075FF` -> Amber `#F59E0B`)**:
   - Karena proyek memakai Tailwind CSS v4 tanpa `@tailwindcss/forms`, seluruh input `radio`, `checkbox`, serta `range` pada `resources/css/app.css` (`@layer base`) wajib menetapkan `accent-color: #f59e0b;` beserta utilitas `accent-amber-500`.
   - Kartu pilihan opsi pada formulir (seperti `Libur Seharian` maupun `Tutup Jam Tertentu` di `photographer/availability/create.blade.php`, selaras `public/booking/create.blade.php`) menggunakan `<input type="radio" class="sr-only peer">` dengan indikator lingkaran Tailwind murni beraksen `border-amber-500` serta titik tengah `bg-amber-500`.
2. **Stepper Tahap Fotografer Satu Baris dan Catatan Penolakan Inline (`stage-stepper.blade.php`)**:
   - Stepper tahap hanya tampil pada halaman utama modul fotografer (dikecualikan dari `photographer.chat*`, `photographer.*.create`, serta `photographer.*.edit` agar formulir tambah/ubah data tidak ditumpuki breadcrumb ganda).
   - Baris atas wajib mempertahankan satu baris horizontal ringkas (`1. Profil > 2. Etalase > 3. Review Tayang` di kiri dengan tautan CTA kontekstual atau tombol `Ajukan Tayang` di kanan), bebas dari baris ceklist duplikat serta kalimat instruksi menggantung di sisi kanan.
   - Ketika `status_verifikasi === 'ditolak'` (Tahap 1) atau `status_etalase === 'ditolak'` (Tahap 3), tepat di bawah baris atas ditampilkan tepat satu baris teks lembut **tanpa garis pembatas** (`0 border-t`) dan **tanpa kotak berlapis** (`0 bg-rose-50` / `0 border`): `Catatan Admin: <isi catatan penolakan>`, sehingga fotografer tetap langsung membaca instruksi perbaikan di halaman mana pun (`Jadwal`, `Profil`, `Paket Layanan`, `Portofolio`). Banner penolakan ganda yang bertumpuk pada `profile/index.blade.php` serta `dashboard.blade.php` ditiadakan agar tidak terjadi duplikasi tampilan.
3. **Kepatuhan Halaman `Atur Tanggal Libur` (`photographer/availability/create.blade.php`)**:
   - Menggunakan breadcrumb netral **Rule #62** (`Jadwal > Atur Tanggal Libur`), tombol `Kembali` netral bersimbol single chevron (**Rule #71 Point 2** serta **Rule #72**), tanpa pembatas `border-t` di atas input tanggal (**Rule #41**), serta placeholder instruksional `Masukkan keterangan libur jika diperlukan` pada textarea `rows="4"` (**Rule #73** serta **Rule #104**).
4. **Indikator Teks Status Tayang Katalog Fotografer (`photographer/dashboard.blade.php` serta `photographer/profile/index.blade.php`)**:
   - Saat `$photographer->isPublishedToCatalog()` bernilai `true` dan `stage-stepper` otomatis disembunyikan, header kanan `Dashboard` menampilkan teks hijau murni **`Tayang di Katalog`** (`text-xs font-bold text-emerald-600 dark:text-emerald-400`) di samping `Tahun Operasional`, sementara header kanan `Kelola Profil` menampilkan **`Tayang di Katalog`** beserta tautan **`Lihat Profil Publik`**. Tampilan wajib berupa teks murni tanpa kotak banner hijau raksasa, tanpa ikon tambahan (**Rule #22**), tanpa bulatan dot badge (**Rule #67**), serta tanpa simbol `&` redundan (**Rule #70**).


