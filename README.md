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
 9. **Standar Komponen Form & Dropdown UI**:
     - **Pemberian Padding Right pada Select Dropdown**: Dilarang membuat ikon chevron/panah pada boks dropdown (`<select>`) menempel atau mepet ke garis border sebelah kanan.
     - **Solusi Implementasi**: Gunakan `appearance-none` pada tag `<select>` dengan tambahan `pl-3.5 pr-9` serta tempatkan wrapper ikon SVG khusus (`pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5`) agar jarak ikon panah dengan border kanan selalu presisi dan memiliki ruang bernapas yang cukup.
 10. **Rangkuman Catatan Revisi UI/UX Penting Pengembangan Kedepannya**:
     - **Background Seksi Full-Width**: Warna latar belakang alternatif (seperti `bg-amber-50` atau `bg-gray-50`) wajib dibuat *full-width* (`w-full`) dari ujung kiri ke ujung kanan layar, bukan dikotaki di tengah (*boxed border*).
     - **Ritme Alignment Layout (Selang-Seling)**: Alur bacaan antar seksi wajib memiliki variasi alignment teks yang alami (misalnya *Center → Left → Right → Center*) agar pandangan pengguna tidak monoton saat di-scroll.
     - **Kualitas Foto & Asset HD**: Seluruh foto profil fotografer dan karya portofolio wajib menggunakan gambar beresolusi tinggi (HD Unsplash / Tajam) yang relevan dengan konteks fotografi.
     - **Pelonggaran Padding Kartu & Deskripsi Profil**: Kartu fotografer dan testimoni wajib memiliki padding internal yang lega (`p-7 sm:p-8`) dan deskripsi yang cukup panjang/jelas agar tidak terkesan sesak.
     - **Boks Testimoni & Logika Slider Non-Looping**: Testimoni pelanggan menggunakan 3 boks kartu utama berukuran besar & lapang dengan kartu tengah yang menonjol (`scale-105 shadow-2xl border-2 border-amber-400`). Slider wajib bersifat *bounded/non-looping* (tombol panah mati saat di ujung slide ke-5 atau ke-1) dan animasi scroll reveal dapat terpicu ulang (*repeatable*) saat di-scroll naik-turun.
     - **Animasi Melayang (Hero Floating Cards)**: Galeri foto pada Hero Section dilengkapi animasi melayang (*organic float effect*) dengan ritme *staggered delay* agar web tampak modern dan dinamis.

---

## 8. Catatan untuk AI IDE

- Ikuti penamaan kolom & tabel persis seperti pada Bagian 3 (konvensi Bahasa Indonesia snake_case, contoh: `id_photographer`, `booking_date`) — jangan diterjemahkan ke Bahasa Inggris supaya konsisten dengan database yang sudah di-running.
- Semua tabel utama pakai soft delete (`deleted_at`) kecuali `activity_logs`, `payment_logs`, `notifications`, `contract_cancellations`, dan `password_reset_tokens`.
- Prioritaskan membangun modul sesuai urutan di Bagian 5 & 6 — jangan mulai dari fitur pelengkap (forum/lomba/e-learning) sebelum alur booking-kontrak-pembayaran selesai dan stabil.
- Sebelum membuat komponen/halaman UI apa pun, ikuti aturan di Bagian 7 — cek desain yang sudah ada dulu, jangan generate gaya baru yang tidak konsisten.