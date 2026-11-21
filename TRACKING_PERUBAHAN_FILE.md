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

---

### [2026-11-21 11:30 WITA] — Penyelarasan Tipografi Netral & Bebas Warna Mencolok pada Box Info Role Formulir Admin

* **Modul / Fitur**:
  - Manajemen Admin: Tipografi Netral Bersih Form Role (`admin/admins/create.blade.php`, `admin/admins/edit.blade.php`)
* **File yang Diubah**:
  * `resources/views/admin/admins/create.blade.php`
  * `resources/views/admin/admins/edit.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  - **Before**: Teks judul peran pada boks informasi role formulir menggunakan warna biru menyala (`text-blue-600 dark:text-blue-400`) pada Admin Operasional dan kuning amber mencolok (`text-amber-900`) pada Super Admin, sehingga tampak kontras dan tidak selaras dengan input form lainnya.
  - **After**: Mengubah seluruh teks judul peran dan subteks di dalam boks formulir menjadi palet warna monokrom netral standar LensMatch:
    - Judul Peran: `text-xs font-bold text-gray-900 dark:text-white` (bersih dan tidak alay).
    - Subteks: `text-[11px] text-gray-400 dark:text-gray-500`.
    - Badge Keterangan Kanan: `text-xs font-semibold text-gray-400 dark:text-gray-500`.
    - Container Boks: `border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900` (identik dengan field input netral lainnya).
* **Hasil Pengujian & Verifikasi**:
  - `php artisan view:cache ; php artisan view:clear`: **PASS (0 errors)**.
  - `php scripts/audit-ui.php`: **PASS (0 pelanggaran)**.
  - `npm run build`: **PASS (Vite assets compiled)**.

---

### [2026-11-21 11:25 WITA] — Penguncian Status Super Admin Tunggal (Root Administrator) & Eliminasi Opsi Radio Role

* **Modul / Fitur**:
  - Manajemen Admin: Penguncian Role Super Admin Tunggal & Staf Admin Operasional (`AdminStaffController.php`, `create.blade.php`, `edit.blade.php`)
* **File yang Diubah**:
  * `app/Http/Controllers/Admin/AdminStaffController.php`
  * `resources/views/admin/admins/create.blade.php`
  * `resources/views/admin/admins/edit.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  - **Before**: Form tambah admin baru (`create.blade.php`) dan edit admin (`edit.blade.php`) menyediakan opsi radio button untuk memilih peran `Super Admin` vs `Admin Operasional`. Hal ini bertentangan dengan arsitektur sistem di mana Super Admin merupakan Root Administrator tunggal platform yang tidak boleh dibuat ganda atau diduplikasi.
  - **After**:
    - **Form Tambah Admin Baru (`create.blade.php`)**: Menghilangkan radio button pilihan role. Seluruh akun yang ditambah melalui form ini otomatis ber-role `admin` (Admin Operasional). Tampilan role berupa informasi statis terproteksi `Admin Operasional (Staf Admin)`.
    - **Form Edit Admin (`edit.blade.php`)**: Menghilangkan radio button pilihan role. Role bersifat terkunci dan tidak dapat diubah (Super Admin tetap `Super Admin (Terkunci)` dengan badge Protected; Staf Admin tetap `Admin Operasional (Terkunci)`).
    - **Backend Controller (`AdminStaffController.php`)**: Pada `store()`, role secara mutlak disetel `admin` (`is_protected = false`). Pada `update()`, pembaruan role diabaikan sehingga role pengguna tetap mengacu ke role asli tanpa celah eskalasi hak akses menjadi Super Admin kedua.
* **Hasil Pengujian & Verifikasi**:
  - `php artisan view:cache ; php artisan view:clear`: **PASS (0 errors)**.
  - `php scripts/audit-ui.php`: **PASS (0 pelanggaran)**.
  - `npm run build`: **PASS (Vite assets compiled)**.

---

### [2026-11-21 11:20 WITA] — Penyeragaman Tombol Aksi Tabel Manajemen Admin (Kebab Dropdown untuk Seluruh Role Termasuk Super Admin)

* **Modul / Fitur**:
  - Manajemen Admin: Penyeragaman Kolom Aksi Tabel (`admin/admins/index.blade.php`)
* **File yang Diubah**:
  * `resources/views/admin/admins/index.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  - **Before**: Baris akun Super Admin menampilkan tombol `[Lihat]` dan `[Edit]` inline berdampingan, sementara baris Admin Operasional menampilkan tombol `[Lihat]` dan `[...]` (Kebab dropdown). Hal ini membuat susunan tombol di kolom aksi tabel tampak tidak simetris antar baris.
  - **After**: Menyeragamkan seluruh baris tabel (termasuk Super Admin) menggunakan format baku **`[Lihat]` (Soft Blue) + `[...]` (Kebab Dropdown)**.
    - Pada baris Super Admin: Kebab dropdown memuat opsi **`Edit`** (tanpa tombol Hapus karena dilindungi sistem).
    - Pada baris Admin Operasional: Kebab dropdown memuat opsi **`Edit`** dan **`Hapus`**.
    - Teks tombol di dalam dropdown mematuhi Rule #80 (`Edit` dan `Hapus` murni tanpa kata objek).
* **Standar / Rule README yang Dipatuhi**:
  - Rule #69: Maksimal 2 elemen horizontal di kolom aksi (1 tombol utama + 1 kebab)
  - Rule #80: Teks bersuku kata tunggal murni (`Lihat`, `Edit`, `Hapus`)
* **Hasil Pengujian & Verifikasi**:
  - `php artisan view:cache ; php artisan view:clear`: **PASS (0 errors)**.
  - `php scripts/audit-ui.php`: **PASS (0 pelanggaran)**.
  - `npm run build`: **PASS (Vite assets compiled)**.

---

### [2026-11-21 10:50 WITA] — Penyelarasan Penuh Standar Desain README (Rule #11, #14, #22, #62, #64, #67, #69, #80) pada Seluruh Halaman Manajemen Admin

* **Modul / Fitur**:
  - Manajemen Admin: Audit & Penyelarasan Desain UI Menyeluruh (`admin/admins/index.blade.php`, `show.blade.php`, `edit.blade.php`, `create.blade.php`)
* **File yang Diubah**:
  * `resources/views/admin/admins/index.blade.php`
  * `resources/views/admin/admins/show.blade.php`
  * `resources/views/admin/admins/edit.blade.php`
  * `resources/views/admin/admins/create.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  - **Daftar Admin (`index.blade.php`)**:
    - **Rule #64 & Rule #7**: Mengeliminasi kartu metrik ke-4 yang menduplikasi total pengelola (`ADMIN TERDAFTAR: 3 Akun`). Kini hanya 3 kartu metrik ringkas: Total Pengelola, Super Admin, Admin Operasional dengan tipografi seragam `text-xs`.
    - **Rule #11**: Mengubah teks kolom role menjadi *Pure Colored Text* (`text-amber-600 font-extrabold` untuk Super Admin, `text-blue-600 font-bold` untuk Admin Operasional), tanpa teks hitam polos.
    - **Rule #69**: Mengeliminasi 3 tombol berderet horizontal (`Lihat` + `Edit` + `...`). Untuk admin yang memiliki hak hapus, aksi menggunakan 1 tombol utama `Lihat` (Soft Blue) + 1 Kebab dropdown `...` yang memuat `Edit` dan `Hapus Akun`. Untuk baris dengan 2 aksi (Super Admin / akun terlindungi), menampilkan `Lihat` + `Edit` saja (maksimal 2 tombol inline).
  - **Detail Admin (`show.blade.php`)**:
    - **Rule #67 Item 4 & Rule #14**: Menghapus seluruh bulatan hijau / dot status (`<span class="w-2 h-2 rounded-full bg-emerald-500"></span>`) dan menghilangkan badge pill. Mengubah status verifikasi menjadi *Pure Colored Text* murni.
    - **Rule #11**: Menghilangkan kartu badge box di sekeliling role `Super Admin` dan menggantinya dengan teks warna murni.
    - **Rule #80 & Rule #22**: Mengubah tombol aksi header dari `"Edit Data"` menjadi teks tunggal baku `"Edit"` dan menghapus icon SVG pensil serta icon tong sampah pada tombol Hapus.
    - **Rule #62**: Menyelaraskan teks aktif breadcrumb menjadi abu-abu netral seragam `text-gray-400 font-bold`.
  - **Edit Admin (`edit.blade.php`) & Tambah Admin (`create.blade.php`)**:
    - **Rule #80**: Mengubah tombol aksi header dari `"Lihat Detail"` menjadi teks tunggal baku `"Lihat"`.
    - **Rule #62**: Menyelaraskan teks aktif breadcrumb menjadi abu-abu netral seragam `text-gray-400 font-bold`.
* **Hasil Pengujian & Verifikasi**:
  - `php artisan view:cache ; php artisan view:clear`: **PASS (0 errors)**.
  - `npm run build`: **PASS (Vite assets compiled)**.

---

### [2026-11-21 10:30 WITA] — Perbaikan Fatal Raw JavaScript Leaking pada Halaman Kurikulum E-Learning

* **Modul / Fitur**:
  - Admin E-Learning: Refaktor Komponen Alpine.js Kurikulum dari Inline Attribute ke Dedicated Script Block (`admin/elearning/curriculum.blade.php`)
* **File yang Diubah**:
  * `resources/views/admin/elearning/curriculum.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  - **Before**: Objek Alpine.js berukuran ~500 baris diletakkan langsung di dalam atribut HTML inline `x-data="{ ... }"`. Ketika logika validasi duplikasi menambahkan fungsi panah `m => m.urutan`, token parser HTML browser menganggap karakter `>` sebagai penutup tag pembuka `<div>`. Hal ini menyebabkan tag `<div>` tertutup prematur dan ratusan baris kode JavaScript mentah bocor serta tercetak sebagai teks biasa di atas halaman web seperti pada tangkapan layar user.
  - **After**: Memindahkan seluruh fungsi dan state Alpine.js ke dalam blok JavaScript fungsi terpisah `<script>function curriculumApp() { return { ... }; }</script>` di bagian bawah berkas, dan memanggilnya secara bersih via `<div x-data="curriculumApp()" class="space-y-6">`. Tag HTML menjadi 100% valid, tidak ada karakter yang bentrok dengan parser HTML browser, dan halaman ter-render sempurna tanpa kebocoran teks kode.
* **Standar / Rule README yang Dipatuhi**:
  - Rule #49: Form Validation & UX State
  - Rule #51: Clean Code & High-Standard Script Architecture
* **Hasil Pengujian & Verifikasi**:
  - `php -l`: **PASS (0 syntax errors)**.
  - `php artisan view:cache ; php artisan view:clear`: **PASS (0 compilation errors)**.

---

### [2026-11-21 11:30 WITA] — Penyelarasan Tipografi Netral & Bebas Warna Mencolok pada Box Info Role Formulir Admin

* **Modul / Fitur**:
  - Manajemen Admin: Tipografi Netral Bersih Form Role (`admin/admins/create.blade.php`, `admin/admins/edit.blade.php`)
* **File yang Diubah**:
  * `resources/views/admin/admins/create.blade.php`
  * `resources/views/admin/admins/edit.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  - **Before**: Teks judul peran pada boks informasi role formulir menggunakan warna biru menyala (`text-blue-600 dark:text-blue-400`) pada Admin Operasional dan kuning amber mencolok (`text-amber-900`) pada Super Admin, sehingga tampak kontras dan tidak selaras dengan input form lainnya.
  - **After**: Mengubah seluruh teks judul peran dan subteks di dalam boks formulir menjadi palet warna monokrom netral standar LensMatch:
    - Judul Peran: `text-xs font-bold text-gray-900 dark:text-white` (bersih dan tidak alay).
    - Subteks: `text-[11px] text-gray-400 dark:text-gray-500`.
    - Badge Keterangan Kanan: `text-xs font-semibold text-gray-400 dark:text-gray-500`.
    - Container Boks: `border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900` (identik dengan field input netral lainnya).
* **Hasil Pengujian & Verifikasi**:
  - `php artisan view:cache ; php artisan view:clear`: **PASS (0 errors)**.
  - `php scripts/audit-ui.php`: **PASS (0 pelanggaran)**.
  - `npm run build`: **PASS (Vite assets compiled)**.

---

### [2026-11-21 11:25 WITA] — Penguncian Status Super Admin Tunggal (Root Administrator) & Eliminasi Opsi Radio Role

* **Modul / Fitur**:
  - Manajemen Admin: Penguncian Role Super Admin Tunggal & Staf Admin Operasional (`AdminStaffController.php`, `create.blade.php`, `edit.blade.php`)
* **File yang Diubah**:
  * `app/Http/Controllers/Admin/AdminStaffController.php`
  * `resources/views/admin/admins/create.blade.php`
  * `resources/views/admin/admins/edit.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  - **Before**: Form tambah admin baru (`create.blade.php`) dan edit admin (`edit.blade.php`) menyediakan opsi radio button untuk memilih peran `Super Admin` vs `Admin Operasional`. Hal ini bertentangan dengan arsitektur sistem di mana Super Admin merupakan Root Administrator tunggal platform yang tidak boleh dibuat ganda atau diduplikasi.
  - **After**:
    - **Form Tambah Admin Baru (`create.blade.php`)**: Menghilangkan radio button pilihan role. Seluruh akun yang ditambah melalui form ini otomatis ber-role `admin` (Admin Operasional). Tampilan role berupa informasi statis terproteksi `Admin Operasional (Staf Admin)`.
    - **Form Edit Admin (`edit.blade.php`)**: Menghilangkan radio button pilihan role. Role bersifat terkunci dan tidak dapat diubah (Super Admin tetap `Super Admin (Terkunci)` dengan badge Protected; Staf Admin tetap `Admin Operasional (Terkunci)`).
    - **Backend Controller (`AdminStaffController.php`)**: Pada `store()`, role secara mutlak disetel `admin` (`is_protected = false`). Pada `update()`, pembaruan role diabaikan sehingga role pengguna tetap mengacu ke role asli tanpa celah eskalasi hak akses menjadi Super Admin kedua.
* **Hasil Pengujian & Verifikasi**:
  - `php artisan view:cache ; php artisan view:clear`: **PASS (0 errors)**.
  - `php scripts/audit-ui.php`: **PASS (0 pelanggaran)**.
  - `npm run build`: **PASS (Vite assets compiled)**.

---

### [2026-11-21 11:20 WITA] — Penyeragaman Tombol Aksi Tabel Manajemen Admin (Kebab Dropdown untuk Seluruh Role Termasuk Super Admin)

* **Modul / Fitur**:
  - Manajemen Admin: Penyeragaman Kolom Aksi Tabel (`admin/admins/index.blade.php`)
* **File yang Diubah**:
  * `resources/views/admin/admins/index.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  - **Before**: Baris akun Super Admin menampilkan tombol `[Lihat]` dan `[Edit]` inline berdampingan, sementara baris Admin Operasional menampilkan tombol `[Lihat]` dan `[...]` (Kebab dropdown). Hal ini membuat susunan tombol di kolom aksi tabel tampak tidak simetris antar baris.
  - **After**: Menyeragamkan seluruh baris tabel (termasuk Super Admin) menggunakan format baku **`[Lihat]` (Soft Blue) + `[...]` (Kebab Dropdown)**.
    - Pada baris Super Admin: Kebab dropdown memuat opsi **`Edit`** (tanpa tombol Hapus karena dilindungi sistem).
    - Pada baris Admin Operasional: Kebab dropdown memuat opsi **`Edit`** dan **`Hapus`**.
    - Teks tombol di dalam dropdown mematuhi Rule #80 (`Edit` dan `Hapus` murni tanpa kata objek).
* **Standar / Rule README yang Dipatuhi**:
  - Rule #69: Maksimal 2 elemen horizontal di kolom aksi (1 tombol utama + 1 kebab)
  - Rule #80: Teks bersuku kata tunggal murni (`Lihat`, `Edit`, `Hapus`)
* **Hasil Pengujian & Verifikasi**:
  - `php artisan view:cache ; php artisan view:clear`: **PASS (0 errors)**.
  - `php scripts/audit-ui.php`: **PASS (0 pelanggaran)**.
  - `npm run build`: **PASS (Vite assets compiled)**.

---

### [2026-11-21 10:50 WITA] — Penyelarasan Penuh Standar Desain README (Rule #11, #14, #22, #62, #64, #67, #69, #80) pada Seluruh Halaman Manajemen Admin

* **Modul / Fitur**:
  - Manajemen Admin: Audit & Penyelarasan Desain UI Menyeluruh (`admin/admins/index.blade.php`, `show.blade.php`, `edit.blade.php`, `create.blade.php`)
* **File yang Diubah**:
  * `resources/views/admin/admins/index.blade.php`
  * `resources/views/admin/admins/show.blade.php`
  * `resources/views/admin/admins/edit.blade.php`
  * `resources/views/admin/admins/create.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  - **Daftar Admin (`index.blade.php`)**:
    - **Rule #64 & Rule #7**: Mengeliminasi kartu metrik ke-4 yang menduplikasi total pengelola (`ADMIN TERDAFTAR: 3 Akun`). Kini hanya 3 kartu metrik ringkas: Total Pengelola, Super Admin, Admin Operasional dengan tipografi seragam `text-xs`.
    - **Rule #11**: Mengubah teks kolom role menjadi *Pure Colored Text* (`text-amber-600 font-extrabold` untuk Super Admin, `text-blue-600 font-bold` untuk Admin Operasional), tanpa teks hitam polos.
    - **Rule #69**: Mengeliminasi 3 tombol berderet horizontal (`Lihat` + `Edit` + `...`). Untuk admin yang memiliki hak hapus, aksi menggunakan 1 tombol utama `Lihat` (Soft Blue) + 1 Kebab dropdown `...` yang memuat `Edit` dan `Hapus Akun`. Untuk baris dengan 2 aksi (Super Admin / akun terlindungi), menampilkan `Lihat` + `Edit` saja (maksimal 2 tombol inline).
  - **Detail Admin (`show.blade.php`)**:
    - **Rule #67 Item 4 & Rule #14**: Menghapus seluruh bulatan hijau / dot status (`<span class="w-2 h-2 rounded-full bg-emerald-500"></span>`) dan menghilangkan badge pill. Mengubah status verifikasi menjadi *Pure Colored Text* murni.
    - **Rule #11**: Menghilangkan kartu badge box di sekeliling role `Super Admin` dan menggantinya dengan teks warna murni.
    - **Rule #80 & Rule #22**: Mengubah tombol aksi header dari `"Edit Data"` menjadi teks tunggal baku `"Edit"` dan menghapus icon SVG pensil serta icon tong sampah pada tombol Hapus.
    - **Rule #62**: Menyelaraskan teks aktif breadcrumb menjadi abu-abu netral seragam `text-gray-400 font-bold`.
  - **Edit Admin (`edit.blade.php`) & Tambah Admin (`create.blade.php`)**:
    - **Rule #80**: Mengubah tombol aksi header dari `"Lihat Detail"` menjadi teks tunggal baku `"Lihat"`.
    - **Rule #62**: Menyelaraskan teks aktif breadcrumb menjadi abu-abu netral seragam `text-gray-400 font-bold`.
* **Hasil Pengujian & Verifikasi**:
  - `php artisan view:cache ; php artisan view:clear`: **PASS (0 errors)**.
  - `npm run build`: **PASS (Vite assets compiled)**.

---

### [2026-11-21 10:30 WITA] — Perbaikan Fatal Raw JavaScript Leaking pada Halaman Kurikulum E-Learning

* **Modul / Fitur**:
  - Admin E-Learning: Refaktor Komponen Alpine.js Kurikulum dari Inline Attribute ke Dedicated Script Block (`admin/elearning/curriculum.blade.php`)
* **File yang Diubah**:
  * `resources/views/admin/elearning/curriculum.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  - **Before**: Objek Alpine.js berukuran ~500 baris diletakkan langsung di dalam atribut HTML inline `x-data="{ ... }"`. Ketika logika validasi duplikasi menambahkan fungsi panah `m => m.urutan`, token parser HTML browser menganggap karakter `>` sebagai penutup tag pembuka `<div>`. Hal ini menyebabkan tag `<div>` tertutup prematur dan ratusan baris kode JavaScript mentah bocor serta tercetak sebagai teks biasa di atas halaman web seperti pada tangkapan layar user.
  - **After**: Memindahkan seluruh fungsi dan state Alpine.js ke dalam blok JavaScript fungsi terpisah `<script>function curriculumApp() { return { ... }; }</script>` di bagian bawah berkas, dan memanggilnya secara bersih via `<div x-data="curriculumApp()" class="space-y-6">`. Tag HTML menjadi 100% valid, tidak ada karakter yang bentrok dengan parser HTML browser, dan halaman ter-render sempurna tanpa kebocoran teks kode.
* **Standar / Rule README yang Dipatuhi**:
  - Rule #49: Form Validation & UX State
  - Rule #51: Clean Code & High-Standard Script Architecture
* **Hasil Pengujian & Verifikasi**:
  - `php -l`: **PASS (0 syntax errors)**.
  - `php artisan view:cache ; php artisan view:clear`: **PASS (0 compilation errors)**.

---

### [2026-11-21 10:25 WITA] — Implementasi CRUD Lengkap Manajemen Admin & Standarisasi Safe Min-Height Seluruh Tabel Bebas Scrollbar

* **Modul / Fitur**:
  - Manajemen Admin: Implementasi CRUD Lengkap (Detail, Edit, Update, Tambah, Hapus Akun Pengelola) Sesuai Standar Rule #53, #62, #69, #70, #74, #76
  - Manajemen Admin: Halaman Detail Akun Pengelola (`resources/views/admin/admins/show.blade.php`) & Riwayat Aktivitas
  - Manajemen Admin: Halaman Edit Akun Pengelola (`resources/views/admin/admins/edit.blade.php`) & Proteksi Role Akun Terlindungi
  - Manajemen Admin: Pembenahan Form Tambah Admin (`resources/views/admin/admins/create.blade.php`) dengan Input Nomor Telepon & Pilihan Role
  - Manajemen Admin: Standarisasi Tombol Aksi Tabel Admin (`resources/views/admin/admins/index.blade.php`) dengan `Lihat`, `Edit`, & Kebab Menu `Hapus` (Rule #69)
  - Seluruh Tabel Sistem: Penambahan Standarisasi Safe Minimum Height (`style="min-height: 300px;"`) pada Seluruh Container Tabel untuk Menghilangkan Scrollbar Vertikal pada Data 1-2 Baris
* **File yang Diubah**:
  * `app/Http/Controllers/Admin/AdminStaffController.php`
  * `routes/web.php`
  * `resources/views/admin/admins/create.blade.php`
  * `resources/views/admin/admins/edit.blade.php`
  * `resources/views/admin/admins/show.blade.php`
  * `resources/views/admin/admins/index.blade.php`
  * `resources/views/admin/users/index.blade.php`
  * `resources/views/admin/categories/index.blade.php`
  * `resources/views/admin/contests/index.blade.php`
  * `resources/views/admin/elearning/index.blade.php`
  * `resources/views/client/bookings/index.blade.php`
  * `resources/views/photographer/bookings/index.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  1. **CRUD Lengkap Manajemen Admin**:
     - **Before**: Manajemen Admin hanya memiliki fitur Tambah (`create`/`store`) dan Hapus (`destroy`). Tidak ada tombol/halaman Detail (`show`) maupun Edit (`edit`/`update`). Form tambah juga tidak memiliki input nomor telepon dan pilihan role pengelola.
     - **After**: Menambahkan `show`, `edit`, dan `update` di `AdminStaffController.php` serta mendaftarkan rutenya di `routes/web.php`. Menyediakan tampilan `show.blade.php` (detail profil pengelola & riwayat log aktivitas) dan `edit.blade.php` (penyuntingan nama, email, no telepon, role, serta ganti kata sandi opsional dengan proteksi akun terlindungi). Pada `create.blade.php`, ditambahkan input nomor telepon/WhatsApp, pilihan role dinamis, serta label tombol standar *"Tambah Data"*. Kolom aksi di `index.blade.php` diperbarui mengikuti Rule #69 (`Lihat` tombol biru lembut, `Edit` tombol amber lembut, dan `Hapus` pada kebab menu).
  2. **Penyelesaian Tuntas Bug Scrollbar Vertikal Tabel (Gambar 2)**:
     - **Before**: Pada tabel dengan 1 atau 2 baris data (seperti Manajemen Klien pada tangkapan layar user), tinggi kontainer tabel kolaps mengikuti tinggi 1 baris (~100px). Ketika menu aksi dibuka ke bawah, popover menu meluap melewati kontainer tabel sehingga browser otomatis memunculkan scrollbar vertikal dan menggulung header kolom tabel ke atas. Selain itu, utility arbitrary Tailwind `min-h-[240px]` tidak ter-compile pada asset Vite sebelumnya.
     - **After**: Menambahkan inline style `style="min-height: 300px;"` pada seluruh pembungkus `overflow-x-auto` di 7 tabel sistem (`users/index.blade.php`, `categories/index.blade.php`, `contests/index.blade.php`, `elearning/index.blade.php`, `admins/index.blade.php`, `client/bookings/index.blade.php`, `photographer/bookings/index.blade.php`). Menjalankan `npm run build` untuk mengompilasi ulang aset Vite. Ruang tinggi 300px menjamin popover menu (~85-120px) selalu muat sepenuhnya di dalam kontainer tanpa memicu scrollbar vertikal, menjaga header tabel tetap berada di posisinya.
* **Standar / Rule README yang Dipatuhi**:
  - Rule #51 & Rule #70: Copywriting Bersih, Proporsional & Anti-AI Slop (Bebas Jargon & Simbol `&` Berlebih)
  - Rule #53 & Rule #76: Standarisasi Teks Tombol Aksi (`Tambah Data`, `Perbaharui Data`)
  - Rule #62: Standarisasi Breadcrumb Global
  - Rule #69: Standarisasi Tombol Kolom Aksi Tabel (Maksimal 2 Tombol Inline + Kebab Menu)
  - Rule #74: Standar Tata Letak Tombol Formulir (Grouping Rata Kanan)
* **Hasil Pengujian & Verifikasi**:
  - `php -l` controller & routes: **PASS (0 syntax errors)**.
  - `php artisan view:cache ; php artisan view:clear`: **PASS (0 Blade compilation errors)**.
  - `npm run build`: **PASS (Vite production build completed)**.

---

### [2026-11-21 10:10 WITA] — Implementasi Validasi Real-time Kurikulum, Restrukturisasi Tombol Modul, dan Perbaikan 5 Bug Sistem

* **Modul / Fitur**:
  - Admin E-Learning: Validasi Real-time Nomor Urutan Modul & Tahapan Pembelajaran (`admin/elearning/curriculum.blade.php`, `Admin/ElearningController.php`)
  - Admin E-Learning: Restrukturisasi Tombol Aksi Kurikulum (Single Primary `Tambah Modul` di Header, Tombol `Tambah Tahapan` di Setiap Kartu Modul)
  - Admin E-Learning: Standarisasi Teks Tombol Modal (`Tambah Data` / `Perbaharui Data` Sesuai Rule #53 & Rule #59)
  - Admin Kelola Pengelola/Staff: Perbaikan Route 500 (`AdminStaffController.php`)
  - Booking & Transaksi: Perbaikan Route 500 pada Tombol Kembali Checkout & Invoice (`public/booking/checkout.blade.php`, `public/booking/invoice.blade.php`)
  - E-Learning: Perbaikan SQLSTATE 42S22 Kolom `id_e_learning` pada Penghapusan Kursus (`Admin/ElearningController.php`)
  - Monitoring Transaksi: Penerapan Operator Nullsafe PHP 8 pada Detail Transaksi Kontrak (`Admin/TransactionController.php`)
  - Basis Data Sistem: Migrasi Pelebaran Kolom Teks & Varchar Pencegah Error Truncation 1406 (`2026_11_21_000001_widen_text_and_varchar_columns_for_truncation_safety.php`)
* **File yang Diubah**:
  * `app/Http/Controllers/Admin/AdminStaffController.php`
  * `app/Http/Controllers/Admin/ElearningController.php`
  * `app/Http/Controllers/Admin/TransactionController.php`
  * `database/migrations/2026_11_21_000001_widen_text_and_varchar_columns_for_truncation_safety.php`
  * `resources/views/admin/elearning/curriculum.blade.php`
  * `resources/views/public/booking/checkout.blade.php`
  * `resources/views/public/booking/invoice.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  1. **Validasi Real-time Urutan Modul & Tahapan (Rule #49 & Rule #51)**:
     - **Before**: Tidak ada pengecekan duplikasi urutan saat admin menginput angka urutan. Duplikasi angka menyebabkan tumpang tindih urutan materi atau error di sistem.
     - **After**: Menambahkan `validateUrutanModul()` dan `validateUrutanLesson()` di Alpine.js yang mengecek array `existingModules` dan `existingLessons` secara langsung saat input (`@input` dan `@blur`). Jika nomor sudah terpakai, border input berubah merah dan pesan informatif muncul seketika (misal: *"Urutan 5 sudah digunakan oleh tahapan 'Video Masterclass'..."*), serta mencegah submit form. Di sisi backend, menambahkan `Rule::unique` pada `storeModule`, `updateModule`, `storeLesson`, dan `updateLesson`.
  2. **Restrukturisasi Tombol Aksi Kurikulum**:
     - **Before**: Di header atas terdapat dua tombol kembar berdampingan (`+ Tambah Modul` dan `+ Tambah Tahapan`), membuat alur penambahan tahapan membingungkan karena tidak otomatis terhubung ke modul terkait.
     - **After**: Header atas kini hanya menampilkan satu tombol utama tunggal: `Tambah Modul` (`bg-amber-400 text-gray-900 font-extrabold`). Penambahan tahapan dilakukan melalui tombol `Tambah Tahapan` di masing-masing modul yang otomatis mengisi pilihan modul bersangkutan.
  3. **Standarisasi Teks Tombol Aksi Modal (Rule #53 & Rule #59)**:
     - **Before**: Teks tombol submit modal masih menggunakan label generik *"Simpan Data"*.
     - **After**: Distandarisasi secara dinamis menjadi *"Tambah Data"* saat membuat record baru dan *"Perbaharui Data"* saat menyunting record.
  4. **Perbaikan RouteNotFoundException pada Staff Admin**:
     - **Before**: `AdminStaffController.php:124` memanggil route `admin.admins.index` yang tidak terdaftar di route provider.
     - **After**: Diarahkan ke route resmi `admin.admins`.
  5. **Perbaikan RouteNotFoundException pada Checkout & Invoice**:
     - **Before**: `checkout.blade.php:81` dan `invoice.blade.php:50` memanggil `admin.transactions.index` saat parameter `from=admin`.
     - **After**: Diarahkan ke route resmi `admin.transactions`.
  6. **Perbaikan SQL Column Error pada Penghapusan Materi E-Learning**:
     - **Before**: `Admin/ElearningController.php:177` memanggil kolom salah `id_elearning` pada tabel `elearning_transactions` sehingga menghasilkan `SQLSTATE[42S22] Column not found`.
     - **After**: Diperbaiki menjadi `id_e_learning` sesuai skema database.
  7. **Pencegahan Fatal Null Pointer Exception pada Transaksi (PHP 8)**:
     - **Before**: Pemanggilan chained property relasi di `Admin/TransactionController.php:192-205` tanpa operator nullsafe rawan fatal error jika data relasi terhapus atau kosong.
     - **After**: Diberikan operator nullsafe `?->` menyeluruh (`client?->user?->email`, `bookingDetail?->service?->nama_layanan`, dsb).
  8. **Migrasi Pelebaran Kolom MySQL (Pencegah Error 1406 Truncation)**:
     - **Before**: Kolom `deskripsi_review` (ulasan), `comment` (forum), `keterangan` (ketersediaan jadwal), dan `media_link` (submisi lomba) bertipe `VARCHAR(255)`, memicu fatal error 500 saat divalidasi hingga 500-1000 karakter di form.
     - **After**: Melebarkan kolom-kolom teks panjang menjadi `TEXT` dan memperlebar kolom nama/judul menjadi `VARCHAR(255)`.
* **Standar / Rule README yang Dipatuhi**:
  - Rule #49: Form Validation & UX State
  - Rule #51: Anti-AI Slop & Clean Copywriting
  - Rule #53: Standard Action Button Text (`Tambah Data`, `Perbaharui Data`)
  - Rule #59: Modal Layout & Footer Standards
* **Hasil Pengujian & Verifikasi**:
  - `php artisan migrate`: **PASS (Migration 2026_11_21_000001 executed successfully)**.
  - `php -l` seluruh berkas: **PASS (0 syntax errors)**.
  - `php artisan view:clear`: **PASS (0 view compilation errors)**.

---

### [2026-11-20 14:25 WITA] — Standarisasi Tata Letak Unggah Foto & Dokumen (Pratinjau di ATAS, Kotak Dropzone di BAWAH) di Seluruh Sistem

* **Modul / Fitur**:
  - Admin E-Learning: Tambah & Edit Materi Modul (`admin/elearning/create.blade.php`, `admin/elearning/edit.blade.php`, `admin/elearning/curriculum.blade.php`)
  - Admin Master Kategori: Tambah & Edit Kategori Layanan (`admin/categories/create.blade.php`, `admin/categories/edit.blade.php`)
  - Admin Manajemen Lomba Foto: Tambah & Edit Event Lomba (`admin/contests/create.blade.php`, `admin/contests/edit.blade.php`)
  - Admin Monitoring Transaksi: Struk Bukti Transfer Payout Fotografer (`admin/transactions/index.blade.php`)
  - Mitra Fotografer Portofolio: Tambah & Edit Album Foto (`photographer/portfolio/create.blade.php`, `photographer/portfolio/edit.blade.php`)
  - Klien & Publik Booking: Unggah Bukti Transfer Invoice & Checkout (`public/booking/checkout.blade.php`, `public/booking/invoice.blade.php`)
  - Publik Lomba Foto: Proposal Pengajuan Lomba, Bukti Pembayaran & Submisi Karya Foto Peserta (`public/contests/propose.blade.php`, `public/contests/show.blade.php`)
  - Publik Forum Komunitas: Unggah Lampiran Foto Topik Diskusi (`public/forum/index.blade.php`)
* **File yang Diubah**:
  * `resources/views/admin/categories/create.blade.php`
  * `resources/views/admin/categories/edit.blade.php`
  * `resources/views/admin/contests/create.blade.php`
  * `resources/views/admin/contests/edit.blade.php`
  * `resources/views/admin/elearning/create.blade.php`
  * `resources/views/admin/elearning/curriculum.blade.php`
  * `resources/views/admin/elearning/edit.blade.php`
  * `resources/views/admin/transactions/index.blade.php`
  * `resources/views/photographer/portfolio/create.blade.php`
  * `resources/views/photographer/portfolio/edit.blade.php`
  * `resources/views/public/booking/checkout.blade.php`
  * `resources/views/public/booking/invoice.blade.php`
  * `resources/views/public/contests/propose.blade.php`
  * `resources/views/public/contests/show.blade.php`
  * `resources/views/public/forum/index.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  - **Before**:
    1. Pada 15 formulir unggah gambar/media di atas, kotak upload (*dropzone* putus-putus *"Seret dan lepas di sini"*) diletakkan di bagian atas, mendahului kotak pratinjau (*preview image*).
    2. Ketika pengguna membuka formulir edit atau memilih berkas foto baru, foto aktif/baru justru terlempar jauh ke bawah di bawah kotak dropzone kosong yang besar, merusak hierarki visual alami form dan menyulitkan pengguna memeriksa foto saat ini.
  - **After**:
    1. Mengubah struktur vertikal secara seragam mengikuti tata letak standar pada `photographer/profile/index.blade.php` dan `public/profile/index.blade.php`:
       - **Pratinjau Foto (Preview Box / Lightbox Clickable)**: Ditempatkan di **ATAS** tepat di bawah label input, sehingga foto saat ini atau berkas yang baru dipilih langsung terlihat jelas secara utuh.
       - **Kotak Unggah (Dropzone Area)**: Ditempatkan di **BAWAH** pratinjau dengan fungsi interaktif drag-and-drop dan klik picker untuk mengganti berkas dengan foto baru.
    2. Menjaga 100% fungsionalitas interaktif: Lightbox resolusi penuh, penghapusan berkas baru via tombol *"Hapus File Baru"*, validasi ukuran dan format, serta fallback SVG.
    3. Memastikan copywriting tetap to the point, bebas dari bahasa berbelit-belit, dan tanpa simbol `&` yang menduplikasi informasi sesuai Rule #70.
* **Standar / Rule README yang Dipatuhi**:
  - Rule #21: Standarisasi Manajemen Upload Media & Batas Ukuran File.
  - Rule #49: Fokus Ketat Terhadap Instruksi User (Strict Scope & Zero Unrequested Side-Effects).
  - Rule #70: Larangan Terminologi Teknis, Kata Kaku & Pemborosan Simbol "&" (Anti-AI Slop Terminology & Natural Human Text Standard).
  - Rule #73: Standar Tata Letak Form Hybrid Responsif.
  - Rule #96: Standarisasi Proporsi Card Media & Larangan Pemanipulasian Tinggi Seragam.
  - Rule #97: Standarisasi Pratinjau Modal (Lightbox) Interaktif pada Seluruh Input Field & Kotak Pratinjau Media Gambar.
* **Hasil Pengujian & Verifikasi**:
  - `php artisan view:clear; php artisan view:cache; php artisan view:clear`: **PASS (Semua Blade views berhasil dikompilasi tanpa galat)**.

---

### [2026-11-20 14:05 WITA] — Standarisasi Mekanisme Smart Dropup pada Seluruh Kebab Menu Tabel & Pencegahan Scrollbar Vertikal

* **Modul / Fitur**:
  - Tabel Admin Materi E-Learning (`resources/views/admin/elearning/index.blade.php`)
  - Tabel Admin Master Kategori Layanan (`resources/views/admin/categories/index.blade.php`)
  - Tabel Admin Manajemen User & Klien (`resources/views/admin/users/index.blade.php`)
  - Tabel Admin Manajemen Lomba Foto (`resources/views/admin/contests/index.blade.php`)
  - Tabel Mitra Fotografer Manajemen Pesanan / Booking (`resources/views/photographer/bookings/index.blade.php`)
  - Tabel Klien Riwayat Pesanan / Booking (`resources/views/client/bookings/index.blade.php`)
* **File yang Diubah**:
  * `resources/views/admin/elearning/index.blade.php`
  * `resources/views/admin/categories/index.blade.php`
  * `resources/views/admin/users/index.blade.php`
  * `resources/views/admin/contests/index.blade.php`
  * `resources/views/photographer/bookings/index.blade.php`
  * `resources/views/client/bookings/index.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  - **Before**:
    1. Kebab menu aksi sekunder (`•••`) pada seluruh baris tabel menggunakan posisi statis membuka ke bawah (`top-full mt-1.5`).
    2. Pada baris-baris terbawah tabel (terutama baris terakhir atau saat data tabel sedikit), menu yang menjulur melewati batas bawah kontainer `overflow-x-auto` memicu aturan browser `overflow-y: auto`, sehingga memunculkan *vertical scrollbar* abu-abu panjang yang merusak tampilan kartu tabel dan memotong menu opsi.
  - **After**:
    1. Mengimplementasikan logika cerdas **Smart Dropup**: `$isDropup = ($loop->count > 2 && ($loop->last || $loop->remaining < 2)) || ($loop->count === 2 && $loop->last);`.
    2. Baris atas dan tengah tetap membuka ke bawah (`top-full mt-1.5 origin-top-right`), sedangkan baris-baris bawah otomatis beralih membuka ke atas (`bottom-full mb-1.5 origin-bottom-right`) memanfaatkan ruang kosong di atasnya.
    3. Menambahkan batas aman ketinggian minimum `min-h-[240px]` pada seluruh pembungkus `overflow-x-auto` agar tabel dengan 1-2 data tetap memiliki ruang gerak bebas tanpa *clipping*.
    4. Menghilangkan potensi kemunculan scrollbar vertikal di seluruh tabel dan memastikan kelancaran interaksi animasi transisi popover.
* **Standar / Rule README yang Dipatuhi**:
  - Rule #49: Strict Scope & Zero Unrequested Side-Effects.
  - Rule #69: Standarisasi Tombol Kolom Aksi Tabel (Maksimal 2 Tombol Inline & Kebab Menu Dropdown).
  - Rule #70: Anti-AI Slop Terminology & Natural Human Text Standard.
  - Rule #96: Standarisasi Proporsi Card Media & Komposisi Bebas Sesak.
  - Rule #106: Tampilan UI Rapi, Responsif & Tipografi Seragam.
* **Hasil Pengujian & Verifikasi**:
  - `php artisan view:clear; php artisan view:cache; php artisan view:clear`: **PASS (Semua Blade views berhasil dikompilasi tanpa galat)**.

---

### [2026-11-20 11:45 WITA] — Implementasi Penuh Modul E-Learning Berbasis Database Dinamis & Aset Foto Lokal

* **Modul / Fitur**:
  - Landing Page E-Learning Hero Section (`resources/views/public/elearning/index.blade.php`)
  - Model & Accessor E-Learning (`app/Models/Elearning.php`)
  - Controller Publik E-Learning (`app/Http/Controllers/Public/ElearningController.php`)
  - Seeder Kurikulum & Modul E-Learning (`database/seeders/ElearningCurriculumSeeder.php`)
  - Aset Gambar Lokal E-Learning (`public/images/e-learning/`)
* **File yang Diubah**:
  * `app/Models/Elearning.php`
  * `app/Http/Controllers/Public/ElearningController.php`
  * `database/seeders/ElearningCurriculumSeeder.php`
  * `resources/views/public/elearning/index.blade.php`
  * `public/images/e-learning/lighting-portrait.jpg` (Aset Baru)
  * `public/images/e-learning/color-grading.jpg` (Aset Baru)
  * `public/images/e-learning/bisnis-studio.jpg` (Aset Baru)
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  - **Before**:
    1. Tiga kartu melayang (floating cards) pada hero section landing page e-learning menggunakan fallback tautan gambar eksternal Unsplash secara statis / hardcode.
    2. Data e-learning di database belum memiliki modul dan pelajaran kurikulum terstruktur.
    3. Gambar kartu melayang belum tersimpan lokal di dalam folder `public/images/e-learning/`.
  - **After**:
    1. Mengunduh dan menempatkan foto lokal resolusi tinggi di folder `public/images/e-learning/`: `lighting-portrait.jpg`, `color-grading.jpg`, dan `bisnis-studio.jpg` melengkapi `main-foto-elearning.jpg`.
    2. Menambahkan `getThumbnailUrlAttribute(?string $value): string` accessor pada model `Elearning.php` sehingga setiap akses thumbnail secara otomatis menyelesaikan path lokal menjadi URL aset yang valid (`asset(...)`) tanpa hardcode domain.
    3. Memperbarui `ElearningCurriculumSeeder.php` untuk men-seed 6 kursus resmi lengkap dengan modul (`ElearningModule`) dan bab materi (`ElearningLesson`), serta membersihkan terminologi asing/escrow sesuai Rule #70.
    4. Menyesuaikan controller `ElearningController.php` agar `$heroCourses` mengambil seluruh koleksi kursus sehingga hero section dapat memetakan kartu berdasarkan tingkat kesulitan (`pemula`, `menengah`, `mahir`) secara dinamis 100% dari database.
    5. Menghubungkan kartu melayang pada hero section menjadi komponen interaktif dengan efek hover halus yang mengarah langsung ke halaman detail kursus (`route('public.elearning.show', $slug)`).
* **Standar / Rule README yang Dipatuhi**:
  - Rule #49: Strict Scope & Zero Unrequested Side-Effects.
  - Rule #70: Larangan Terminologi Teknis, Kata Kaku & Pemborosan Simbol "&".
  - Rule #94: Standarisasi Integritas Basis Data & Eliminasi Rekord Duplikat/Orphan.
  - Rule #106: Tampilan UI Rapi & Responsive.
* **Hasil Pengujian & Verifikasi**:
  - `php artisan db:seed --class=Database\Seeders\ElearningCurriculumSeeder`: **PASS**.
  - `php artisan view:clear; php artisan view:cache; php artisan view:clear`: **PASS**.
  - Render test untuk halaman Index, Katalog, dan Detail Kursus: **PASS (100% data dinamis dari DB dan thumbnail lokal ter-render sempurna)**.

---

### [2026-11-20 11:06 WITA] — Pemindahan Relasi Kategori Fotografer & Penghapusan Kategori Duplikat Wedding & Vacation oleh Admin

* **Modul / Fitur**:
  - Manajemen Master Kategori Admin (`/admin/categories`)
  - Profil Spesialisasi Fotografer & Portofolio Karya
  - Pill Kategori Populer Halaman Utama (`resources/views/public/photographers/index.blade.php`)
* **File yang Diubah**:
  * `resources/views/public/photographers/index.blade.php`
  * Basis data: Tabel `categories`, `photographer_categories`, dan `photographer_portofolios`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  - **Tahap 1 (Sisi Fotografer)**:
    1. *Alex Visual Studio* (ID: 1): Portofolio *"Romantic Sunset at Melasti Beach"* dialihkan ke kategori aktif `Studio & Portrait` (ID: 12), dan spesialisasi akun disinkronkan ke `Studio & Portrait`.
    2. *Dewa Portrait Studio* (ID: 4): Spesialisasi akun disinkronkan ke `Studio & Portrait` (ID: 12), melepaskan kategori duplikat ID: 9.
    3. *Bali Lens Holiday Photography* (ID: 5): Portofolio *"Exploring Nusa Penida & Uluwatu Cliff"* dialihkan ke kategori aktif `Event & Konser` (ID: 11), dan spesialisasi akun disinkronkan ke `Event & Konser`.
  - **Tahap 2 (Sisi Admin)**:
    1. Melakukan verifikasi relasi pada kategori `Wedding & Prewedding` (ID: 9) dan `Vacation & Travel` (ID: 10) yang kini telah mencapai 0 fotografer dan 0 portofolio.
    2. Admin berhasil menghapus kedua kategori duplikat tersebut secara permanen dari basis data tanpa error proteksi.
    3. Memperbarui tautan chip filter populer pada hero banner beranda agar mengarah ke kategori aktif sistem (`Studio & Portrait`, `Event & Konser`, `Commercial & Produk`).
* **Standar / Rule README yang Dipatuhi**:
  - Rule #49: Strict Scope & Zero Unrequested Side-Effects.
  - Rule #70: Larangan Terminologi Teknis, Kata Kaku & Pemborosan Simbol "&".
  - Rule #94: Standarisasi Integritas Basis Data & Eliminasi Rekord Duplikat/Orphan.
* **Hasil Pengujian & Verifikasi**:
  - `php artisan view:clear; php artisan view:cache; php artisan view:clear`: **PASS (Zero Blade compilation errors)**.
  - Relasi database diverifikasi: ID 9 dan ID 10 terhapus 100%, seluruh fotografer dan portofolio tetap aktif dan utuh.

---

### [2026-11-20 10:05 WITA] — Implementasi Perbaikan Bug Riil 2 s/d 5: Pembersihan Jargon Payout/Escrow (Rule #70), Standarisasi Tab Teks Horizontal Lomba Foto (Rule #78), Tipografi Seragam text-xs Sel Tabel (Rule #106), dan Sinkronisasi Server-Side Search Galeri Klien

* **Modul / Fitur**:
  - Wizard Onboarding Mitra Fotografer (`resources/views/partials/photographer/onboarding-wizard.blade.php`)
  - Laporan Keuangan Fotografer (`resources/views/photographer/reports/index.blade.php`)
  - Notifikasi Selesai Foto Booking Klien (`app/Http/Controllers/Client/ClientBookingController.php`)
  - Manajemen Lomba Foto Admin (`resources/views/admin/contests/index.blade.php`, `manage.blade.php`, `submissions.blade.php`)
  - Galeri Foto Klien (`resources/views/client/galleries/index.blade.php`)
* **File yang Diubah**:
  * `resources/views/partials/photographer/onboarding-wizard.blade.php`
  * `resources/views/photographer/reports/index.blade.php`
  * `app/Http/Controllers/Client/ClientBookingController.php`
  * `resources/views/admin/contests/index.blade.php`
  * `resources/views/admin/contests/manage.blade.php`
  * `resources/views/admin/contests/submissions.blade.php`
  * `resources/views/client/galleries/index.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  - **Bug 2 (Pembersihan Jargon Asing Payout & Escrow - Rule #70 Point 3 & 4)**:
    - *Before*: Teks UI dan notifikasi in-app menggunakan istilah asing: *"Rekening Payout"*, *"Ajukan Pencairan Dana Payout"*, dan *"Dana escrow Anda kini siap dicairkan (ready for payout)"*.
    - *After*: Diganti menjadi istilah padanan bahasa Indonesia yang alami dan lugas: *"Rekening Bank"*, *"Ajukan Pencairan Dana"*, dan *"Dana hasil pemotretan Anda kini siap dicairkan."*.
  - **Bug 3 (Standarisasi Navigasi Tab Rule #78 pada Lomba Foto Admin)**:
    - *Before*: Filter status pada halaman indeks lomba, detail peserta lomba, dan submisi lomba menggunakan tombol kotak pill tebal (`rounded-xl bg-gray-900 text-white`).
    - *After*: Diubah 100% mengikuti standar **Rule #78 Horizontal Active Text Underline Standard**: teks tab aktif bergaris bawah aksen amber (`underline underline-offset-8 decoration-2 decoration-amber-500`) didampingi badge counter data bundar kecil (`rounded-full bg-amber-100 text-amber-700`), bebas dari tombol box/pill yang kaku.
  - **Bug 4 (Standarisasi Tipografi text-xs Sel Tabel Admin - Rule #106 Point 1)**:
    - *Before*: Sel email dan nama peserta pada tabel manajemen peserta dan submisi lomba tidak memiliki kelas `text-xs`, sehingga mewarisi ukuran 16px (text-base) yang jomplang dari sel lainnya.
    - *After*: Ditambahkan kelas `text-xs` pada seluruh sel bersangkutan sehingga tipografi tabel seragam dan proporsional.
  - **Bug 5 (Sinkronisasi Server-Side Search Galeri Klien)**:
    - *Before*: Pencarian galeri hanya memfilter kartu DOM di halaman aktif via JavaScript, menyebabkan pencarian gagal pada halaman 2+, serta hasil pencarian terhapus saat filter status dropdown diubah.
    - *After*: Fungsi `handleSearch` diperbarui dengan debouncing 350ms yang memperbarui parameter URL `q` via `pushState` dan memanggil backend AJAX `fetchTable()`. Pencarian kini berjalan menyeluruh di seluruh halaman basis data dan tetap sinkron dengan filter status.
* **Standar / Rule README yang Dipatuhi**:
  - Rule #49: Strict Scope & Zero Unrequested Side-Effects.
  - Rule #70: Larangan Terminologi Teknis, Kata Kaku & Istilah Asing (Anti-AI Slop).
  - Rule #78: Standarisasi Tampilan Navigasi Tab Teks Horizontal (Active Text Underline).
  - Rule #106: Standarisasi Tipografi Seragam text-xs Sel Tabel.
* **Hasil Pengujian & Verifikasi**:
  - `php -l app/Http/Controllers/Client/ClientBookingController.php`: **PASS (No syntax errors)**.
  - `php artisan view:clear; php artisan view:cache; php artisan view:clear`: **PASS (Zero template errors)**.

---

### [2026-11-20 09:55 WITA] — Penyiapan Struktur Hardcode Foto Kurasi Unsplash pada Hero Showcase Eksplorasi Fotografer

* **Modul / Fitur**:
  - Halaman Eksplorasi Publik Fotografer (`resources/views/public/photographers/index.blade.php`)
* **File yang Diubah**:
  * `resources/views/public/photographers/index.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  - **Before**: 4 kartu foto melayang (*floating showcase collage*) di kolom kanan hero banner mengambil foto pertama dari portofolio database secara dinamis. Jika portofolio di database belum memiliki media atau foto seeder kurang representatif, tampilan hero dapat terlihat kurang konsisten.
  - **After**:
    1. Menyediakan blok variabel array `$curatedHeroPhotos` yang terisolasi dan terdokumentasi rapi di bagian atas komponen hero.
    2. Format array memuat 4 slot (Slot 0: Kiri Atas, Slot 1: Kanan Atas, Slot 2: Tengah Utama, Slot 3: Kanan Bawah) dengan pasangan key `'title'` dan `'image'` siap pakai.
    3. User dapat langsung menyalin dan menempelkan tautan URL gambar Unsplash pilihan beserta judulnya secara mudah tanpa perlu menyentuh markup HTML/CSS kartu.
    4. Dilengkapi mekanisme fallback gambar default agar view tetap aman dan tidak pernah mengalami crash/error rendering.
* **Standar / Rule README yang Dipatuhi**:
  - Rule #49: Strict Scope & Zero Unrequested Side-Effects.
  - Rule #89: Presisi Spasial 2D dan Komposisi Visual Hero Showcase.
* **Hasil Pengujian & Verifikasi**:
  - `php artisan view:clear; php artisan view:cache; php artisan view:clear`: **PASS (Zero template errors)**.

---

### [2026-11-19 17:50 WITA] — Implementasi Perbaikan Bug 31 s/d 35: Eliminasi Fatal TypeError Profil Fotografer Tanpa Review, Sinkronisasi Tagihan DP & Pelunasan, Status Booking Penawaran Khusus Chat, Navigasi Form Admin, dan Filter Metrik Booking Klien

* **Modul / Fitur**:
  - Halaman Profil Publik Fotografer (`app/Http/Controllers/Public/PhotographerController.php` & `resources/views/public/photographers/show.blade.php`)
  - Manajemen Tagihan Klien (`app/Http/Controllers/Client/ClientBookingController.php`)
  - Verifikasi Transaksi Admin (`app/Http/Controllers/Admin/TransactionController.php`)
  - Penawaran Khusus Chat Fotografer (`app/Http/Controllers/Public/ChatController.php`)
  - Manajemen Staf Admin (`app/Http/Controllers/Admin/AdminStaffController.php`)
  - Halaman Reservasi Booking Klien (`app/Http/Controllers/Client/ClientBookingController.php`)
* **File yang Diubah**:
  * `app/Http/Controllers/Public/PhotographerController.php`
  * `resources/views/public/photographers/show.blade.php`
  * `app/Http/Controllers/Client/ClientBookingController.php`
  * `app/Http/Controllers/Admin/TransactionController.php`
  * `app/Http/Controllers/Public/ChatController.php`
  * `app/Http/Controllers/Admin/AdminStaffController.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  1. **Bug 31: Fatal TypeError (HTTP 500) Saat Membuka Profil Fotografer Tanpa Ulasan & Variabel Star Distribution (`PhotographerController.php` & `photographers/show.blade.php`)**:
     - **Before**: Jika fotografer baru belum memiliki ulasan (`$totalReviews === 0`), `$avgRating` diatur sebagai string `'-'`. Di view `show.blade.php:941`, Blade mengeksekusi `round($avgRating)`. Di PHP 8+, `round('-')` memicu fatal `TypeError: round(): Argument #1 ($num) must be of type int|float, string given`, sehingga setiap profil fotografer baru mengalami server crash (HTTP 500). Selain itu, controller mengirim `'starCounts'` tetapi view memanggil `$starDistribution[$star]`, menyebabkan grafik distribusi bintang selalu 0%.
     - **After**: Mengonversi nilai ke numerik aman di Blade `@php $numericRating = is_numeric($avgRating) ? (float) $avgRating : 0; @endphp` sebelum diproses `round()`, serta meneruskan dan menyinkronkan kedua variabel `'starDistribution'` dan `'starCounts'`. Profil fotografer baru kini dapat dibuka mulus tanpa crash dan grafik bintang terhitung akurat.
  2. **Bug 32: Salah Kategori Tagihan DP pada Filter Invoice Klien & Penerbitan Tagihan Pelunasan (`ClientBookingController.php` & `TransactionController.php`)**:
     - **Before**: Pesanan DP yang baru membayar DP 30% keliru masuk ke filter tagihan "Lunas" (`status=completed`) karena query mencari kontrak yang tidak memiliki pembayaran selain completed. Pada saat yang sama, tagihan pelunasan (70%) belum digenerate di database sehingga pesanan tidak muncul di filter "DP Terbayar" (`dp_paid`).
     - **After**: Di `ClientBookingController::invoices`, filter `dp_paid` secara ketat menyaring kontrak DP yang belum selesai (`status_contract != 'completed'`) dan belum memiliki pelunasan lunas. Filter `completed` mewajibkan pelunasan penuh terbayar. Di `TransactionController::verifyPayment`, sistem otomatis membuat record tagihan pelunasan (pending) seketika setelah pembayaran DP diverifikasi oleh admin.
  3. **Bug 33: Penawaran Khusus Chat Menampilkan Tombol "Terima" Redundan pada Fotografer (`ChatController.php`)**:
     - **Before**: Saat klien menyetujui tawaran khusus yang diajukan fotografer di chat, `acceptCustomOffer` menyetel `status_booking = 'pending'`. Di view pesanan fotografer, hal ini memicu tombol "Terima Booking Sesi Foto?", membingungkan fotografer karena ia yang menginisiasi tawaran tersebut.
     - **After**: Menyetel `'status_booking' => 'confirmed'` pada penawaran khusus yang disetujui klien, sehingga fotografer langsung melihat status "Menunggu Pembayaran Klien" dengan tombol aksi "Chat Klien" (selaras dengan alur pesanan reguler).
  4. **Bug 34: Formulir Tambah Admin Mengarahkan Super Admin Kembali ke Form Kosong (`AdminStaffController.php`)**:
     - **Before**: Setelah menyimpan akun staf admin baru, controller mengeksekusi `redirect()->back()`, mengarahkan pengguna kembali ke form kosong (`admin/admins/create`).
     - **After**: Mengubah redirect menjadi `redirect()->route('admin.admins.index')`, membawa Super Admin kembali ke tabel manajemen admin dengan notifikasi sukses.
  5. **Bug 35: Metrik Ringkasan Booking Klien Menghitung Pesanan Dibatalkan / Kadaluarsa (`ClientBookingController.php`)**:
     - **Before**: Query ringkasan KPI booking klien (`pending`, `confirmed`, `completed`) tidak mengecualikan kontrak yang dibatalkan, menyebabkan pesanan batal tetap terhitung di kartu metrik.
     - **After**: Menambahkan `whereNotIn('status_contract', ['cancelled'])` pada ketiga metrik tersebut agar selalu sinkron dengan data riil pada tabel pesanan.
* **Standar / Rule README yang Dipatuhi**:
  - Rule #14: Standarisasi Tampilan Status Transaksi Berbasis Pure Colored Text.
  - Rule #51: Standarisasi Copywriting Bersih, Proporsional & Anti-AI Slop.
  - Rule #64: Standarisasi KPI Metric Cards di Atas Tabel Fitur.
  - Rule #70: Larangan Terminologi Teknis & Karakter Redundan.
  - Rule #90: Standarisasi Sinkronisasi Status Booking Klien & Fotografer.
* **Hasil Pengujian & Verifikasi**:
  - `php -l` seluruh controller yang diubah: **PASS (0 syntax errors)**.
  - `php artisan view:clear; php artisan view:cache`: **PASS (Zero error)**.

---

### [2026-11-19 16:50 WITA] — Implementasi Perbaikan Bug 28, Bug 29, dan Bug 30: Validasi Ulasan Selesai Klien, Pencarian Nomor Kontrak Transaksi Admin, dan Submisi Karya Lomba Tautan Eksternal

* **Modul / Fitur**:
  - Penulisan Ulasan Klien (`app/Http/Controllers/Public/ReviewController.php` & `resources/views/client/bookings/index.blade.php`)
  - Pencarian Transaksi Admin (`app/Http/Controllers/Admin/TransactionController.php`)
  - Pengelolaan Submisi Lomba Admin (`database/migrations`, `resources/views/admin/contests/submissions.blade.php`)
  - Catatan Pengecualian Khusus Bug 26 & 27 (Testimoni & Statistik Beranda Tetap Dipertahankan)
* **File yang Diubah**:
  * `database/migrations/2026_11_19_000003_make_image_url_nullable_on_contest_submissions_table.php` (File Baru)
  * `app/Http/Controllers/Public/ReviewController.php`
  * `resources/views/client/bookings/index.blade.php`
  * `app/Http/Controllers/Admin/TransactionController.php`
  * `resources/views/admin/contests/submissions.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  1. **Pengecualian Bug 26 & Bug 27 (Instruksi Khusus Pengguna)**:
     - Bug 26 (Testimoni hardcoded pada halaman beranda) dan Bug 27 (Metrik statistik 514 kota pada beranda) secara eksplisit dikecualikan dari perbaikan dan dipertahankan apa adanya sesuai arahan pengguna.
  2. **Bug 28: Klien Dapat Menulis Ulasan Sebelum Kontrak Selesai & Valid (`ReviewController.php` & `client/bookings/index.blade.php`)**:
     - **Before**: Tombol ulasan pada menu aksi pesanan klien muncul setiap kali `status_contract === 'completed'`, tanpa memeriksa apakah klien sudah memvalidasi galeri (`is_validated_client`). Selain itu, validasi di backend controller `ReviewController::store` tidak mengecek status kontrak secara ketat, memungkinkan celah penulisan ulasan fiktif atau prematur.
     - **After**: Di `ReviewController::store`, ditambahkan validasi ketat `if ($contract->status_contract !== 'completed' || !$contract->is_validated_client)` yang menolak ulasan dengan pesan informatif jika pesanan belum selesai penuh atau belum divalidasi. Di tampilan `client/bookings/index.blade.php`, tombol `Tulis Ulasan` hanya dirender jika kedua kondisi (`completed` dan `is_validated_client`) terpenuhi (Rule #90).
  3. **Bug 29: Pencarian Nomor Kontrak Tidak Berfungsi pada Monitoring Transaksi Admin (`Admin/TransactionController.php`)**:
     - **Before**: Fitur pencarian transaksi admin hanya mengekstrak angka dari format invoice via regex `preg_match('/INV-LM-\d+-0*(\d+)/i', $search, $matches)`. Ketika admin menyalin dan mencari nomor kontrak seperti `#KTRK-00019` atau `KTRK-00019`, regex gagal menangkap ID kontrak sehingga hasil pencarian kosong.
     - **After**: Regex diperluas menjadi `preg_match('/(?:INV-LM-\d+-0*|#?KTRK-0*)(\d+)/i', $search, $matches)` sehingga pencarian berbasis nomor kontrak `#KTRK-00019` maupun `KTRK-00019` langsung menangkap ID `19` dan mencocokkan kontrak dengan akurat.
  4. **Bug 30: Database Crash Saat Peserta Lomba Mengirim Karya Berupa Tautan Eksternal (`contest_submissions` Migration & `admin/contests/submissions.blade.php`)**:
     - **Before**: Formulir pendaftaran lomba foto mengizinkan pengiriman karya via tautan eksternal (Google Drive / portofolio online) tanpa mengunggah berkas gambar langsung. Namun, kolom `image_url` pada tabel `contest_submissions` tidak disetel `nullable`. Saat peserta mengirim tautan saja, database melempar error fatal SQL (*Column 'image_url' cannot be null*). Selain itu, antarmuka kurasi admin mengalami *broken image* jika `image_url` bernilai null.
     - **After**: Dibuat dan dijalankan migrasi `2026_11_19_000003_make_image_url_nullable_on_contest_submissions_table.php` untuk mengubah kolom `image_url` menjadi nullable. Pada antarmuka `admin/contests/submissions.blade.php`, sistem memeriksa keberadaan `image_url`; jika null dan memiliki `media_link`, sistem menampilkan kartu tautan eksternal yang rapi dengan tombol akses berkas Google Drive langsung (Rule #15 & #51).
* **Standar / Rule README yang Dipatuhi**:
  - Rule #14: Standarisasi Tampilan Status Transaksi Berbasis Pure Colored Text.
  - Rule #15: Standar Penggunaan Data Dinamis & Validitas Tautan Karya.
  - Rule #51: Standarisasi Copywriting Bersih, Proporsional & Anti-AI Slop.
  - Rule #70: Larangan Karakter Ampersand Redundan.
  - Rule #90: Standarisasi Sinkronisasi Status Booking Klien & Fotografer.
* **Hasil Pengujian & Verifikasi**:
  - `php -l` controller: **PASS (0 syntax errors)**.
  - `php artisan migrate`: **PASS (Migrasi kolom nullable sukses)**.
  - `php artisan view:clear; php artisan view:cache`: **PASS (Zero error)**.

---

### [2026-11-19 16:30 WITA] — Implementasi Perbaikan 5 Bug Sistem: Enum Diskualifikasi Lomba, Integritas Status Lomba, Proteksi Escrow Pelunasan DP, Retensi Status Selesai, dan Sinkronisasi Refund Klien

* **Modul / Fitur**:
  - Skema Enum & Otoritas Penjurian Submisi Lomba Foto (`database/migrations`, `app/Http/Controllers/Admin/ContestController.php`)
  - Logika Pencabutan Juara & Status Aktif Lomba Foto (`app/Http/Controllers/Admin/ContestController.php`)
  - Proteksi Keuangan Escrow pada Konfirmasi Galeri DP (`ClientBookingController.php` & `client/galleries/index.blade.php`)
  - Pencegahan Demosi Status Selesai Saat Verifikasi Pelunasan DP Admin (`TransactionController.php`)
  - Sinkronisasi Alur Refund Pembatalan Klien & Update Nominal Refund Riil (`ClientBookingController.php` & `TransactionController.php`)
* **File yang Diubah**:
  * `database/migrations/2026_11_19_000002_add_disqualified_to_contest_submissions_table.php` (File Baru)
  * `app/Http/Controllers/Admin/ContestController.php`
  * `app/Http/Controllers/Client/ClientBookingController.php`
  * `resources/views/client/galleries/index.blade.php`
  * `app/Http/Controllers/Admin/TransactionController.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  1. **Bug 21: Database Schema Crash Saat Admin Mendiskualifikasi Karya Lomba Foto (`ContestController.php` & Migration)**:
     - **Before**: Antarmuka juri dan validasi controller menyediakan opsi status `'disqualified'`, namun kolom database `contest_submissions.status_submission` hanya bertipe `enum('pending','verified','winner_1','winner_2','winner_3','rejected')`. Saat admin memilih "Diskualifikasi", database MySQL melempar error fatal (*Data truncated* / HTTP 500).
     - **After**: Membuat dan menjalankan file migrasi `2026_11_19_000002_add_disqualified_to_contest_submissions_table.php` untuk menambahkan nilai `'disqualified'` ke enum database, sehingga status diskualifikasi berjalan lancar tanpa error database.
  2. **Bug 22: Event Lomba Foto Dipaksa Tutup Prematur Saat Admin Mencabut Status Juara (`Admin/ContestController.php`)**:
     - **Before**: Pada method `setWinner`, ketika semua pemenang dicabut kembali ke status `verified`, sistem mengeksekusi `$contest->update(['status' => 'ditutup'])` secara buta, meskipun deadline pendaftaran lomba sebenarnya masih berlangsung di masa depan (`now() < end_date`).
     - **After**: Menambahkan pengecekan tenggat waktu: `$isPastDeadline = $contest->end_date && \Carbon\Carbon::parse($contest->end_date)->endOfDay()->isPast()`. Jika batas waktu lomba belum lewat, status dikembalikan ke `'buka'` sehingga peserta lain tetap dapat mendaftar.
  3. **Bug 23: Kerugian Kas Platform — Konfirmasi Selesai Sebelum Pelunasan DP 70% Terbayar (`ClientBookingController.php` & `client/galleries/index.blade.php`)**:
     - **Before**: Klien pesanan DP dapat mengonfirmasi selesai hasil foto pada galeri meskipun sisa pelunasan 70% belum dibayar. Controller langsung menandai `status_payout = 'ready'`, dan Admin mencairkan payout 90% penuh (`jumlah * 0.90`) ke fotografer, mengakibatkan platform menanggung kerugian 60% sisa biaya yang belum dibayar klien.
     - **After**: Di `ClientBookingController::confirmGallery`, sistem mewajibkan pembayaran pelunasan berstatus `completed` sebelum klien diizinkan mengonfirmasi selesai. Di UI galeri klien, jika pelunasan masih tertunda, tombol aksi berubah menjadi tombol amber `"Selesaikan Pelunasan"` yang mengarahkan klien langsung ke lembar tagihan resmi invoice (Rule #51, #90).
  4. **Bug 24: Status Pesanan Selesai Terhapus Menjadi Active/Confirmed Saat Verifikasi Pelunasan DP (`Admin/TransactionController.php`)**:
     - **Before**: Saat Admin memverifikasi pembayaran transfer pelunasan, controller mengeksekusi `$contract->update(['status_contract' => 'active'])` dan `$bookingDetail->update(['status_booking' => 'confirmed'])`. Pada pesanan yang fotonya sudah selesai diunggah, hal ini merusak alur penyelesaian dan mendemosi status kembali ke aktif belum selesai.
     - **After**: Memeriksa status penyelesaian sebelumnya (`$alreadyCompleted`). Jika foto atau pesanan sudah selesai, status tetap dipertahankan sebagai `'completed'`, dan `status_booking` tidak diturunkan ke `'confirmed'`.
  5. **Bug 25: Pemalsuan Status "Refunded" Dini & Deadlock Tombol Refund Admin pada Pembatalan Klien (`ClientBookingController.php` & `Admin/TransactionController.php`)**:
     - **Before**: Saat klien membatalkan pesanan, controller langsung menyetel `status_payout = 'refunded'`. Padahal Admin belum memproses pengembalian dana riil. Akibatnya, saat Admin menekan tombol "Proses Refund", sistem menolak dengan pesan *"Transaksi ini sudah direfund sebelumnya"*, sehingga refund terkunci selamanya dan nominal refund di `contract_cancellations` tidak pernah diperbarui.
     - **After**: Di `ClientBookingController`, status dana pembatalan disetel ke `'holding'` (Dana Ditahan menunggu refund Admin) selaras dengan alur pembatalan fotografer. Di `TransactionController::processRefund`, jika record pembatalan sudah ada, sistem memperbarui nilai `refund_amount` dengan nominal riil yang dibayarkan dan mencatat catatan admin.
* **Standar / Rule README yang Dipatuhi**:
  - Rule #14: Standarisasi Tampilan Status Transaksi Berbasis Pure Colored Text.
  - Rule #15: Standar Penggunaan Data Dinamis & Seeder Database.
  - Rule #51: Standarisasi Copywriting Bersih, Proporsional & Anti-AI Slop.
  - Rule #55: Standarisasi Tampilan Status Event Lomba Foto.
  - Rule #70: Larangan Terminologi Teknis Kaku & Pemborosan Simbol "&".
  - Rule #90: Standarisasi Sinkronisasi Status Booking Klien & Fotografer.
* **Hasil Pengujian & Verifikasi**:
  - `php artisan migrate`: **PASS (Migration 2026_11_19_000002 executed successfully)**.
  - `php -l` seluruh file yang diubah: **PASS (0 syntax errors)**.
  - `php artisan view:clear` & `php artisan view:cache`: **PASS (0 error)**.

---

### [2026-11-19 16:00 WITA] — Implementasi Perbaikan 5 Bug Logika Sistem: Invoice DP, Proteksi IDOR, Siklus Revisi, Stale Expiry Reschedule, dan Format 24:00 Tengah Malam

* **Modul / Fitur**:
  - Logika Status Invoice DP Klien (`resources/views/client/invoices/index.blade.php`)
  - Otorisasi Akses Tagihan & Proteksi IDOR (`BookingController.php` & `routes/web.php`)
  - Siklus Alur Status Permintaan Revisi Foto (`ClientBookingController.php`, `client/bookings/index.blade.php`, `photographer/bookings/index.blade.php`, `client/galleries/index.blade.php`)
  - Sinkronisasi Batas Waktu Bayar (`expired_at`) Saat Persetujuan Reschedule (`ClientBookingController.php` & `PhotographerBookingController.php`)
  - Koreksi Pemotongan Durasi Jam Booking Tengah Malam 24:00 (`BookingScheduleService.php`)
* **File yang Diubah**:
  * `resources/views/client/invoices/index.blade.php`
  * `app/Http/Controllers/Public/BookingController.php`
  * `routes/web.php`
  * `app/Http/Controllers/Client/ClientBookingController.php`
  * `app/Http/Controllers/Photographer/PhotographerBookingController.php`
  * `resources/views/client/bookings/index.blade.php`
  * `resources/views/photographer/bookings/index.blade.php`
  * `resources/views/client/galleries/index.blade.php`
  * `app/Services/BookingScheduleService.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  1. **Bug 16: Label Invoice DP Klien Muncul "Lunas" Padahal Pelunasan 70% Belum Terbayar (`client/invoices/index.blade.php`)**:
     - **Before**: Pengecekan `$isFullySettled` menyertakan `in_array($c->status_contract, ['active', 'completed'])`. Saat pesanan DP disetujui, `status_contract` menjadi `active`, menyebabkan `$isFullySettled` selalu bernilai `true` (muncul teks hijau "Lunas" 100%) dan cabang evaluasi `@elseif($isDp && $isDpPaid && !$isFullPaid)` ("DP Terbayar") tidak pernah tercapai.
     - **After**: Mengharuskan secara ketat `$isDp ? ($isDpPaid && $isFullPaid) : ...` dan mengecualikan kontrak berstatus `cancelled`. Pesanan DP yang baru membayar uang muka kini tampil presisi sebagai teks biru "DP Terbayar", dan baru berubah menjadi teks hijau "Lunas" jika pelunasan telah selesai (Rule #14).
  2. **Bug 17: Celah Akses IDOR & Buka Invoice Publik Tanpa Login (`BookingController.php` & `routes/web.php`)**:
     - **Before**: `showInvoice` mengambil `$clientProfile` via `auth()->id()` dan menggunakan `->when($clientProfile, fn($q) => $q->where('id_client', $clientProfile->id))`. Apabila diakses oleh tamu atau user tanpa RoleClient, `when()` diabaikan sehingga rute `findOrFail($id)` membocorkan data identitas, alamat, kontak, dan bukti transfer kontrak siapapun.
     - **After**: Memindahkan rute `booking.invoice` ke grup proteksi middleware `auth` dan mengimplementasikan pengecekan otorisasi ketat multi-role (`$isAdmin || $isClientOwner || $isPhotographerOwner`). Akses yang tidak sah ditolak dengan HTTP 403 Forbidden. Pada `confirmPayment`, kepemilikan klien juga divalidasi secara absolut.
  3. **Bug 18: Deadlock Status Revisi Foto "Menunggu Konfirmasi Klien" (`ClientBookingController.php`, `client/bookings/index.blade.php`, `photographer/bookings/index.blade.php`, `client/galleries/index.blade.php`)**:
     - **Before**: Saat klien mengajukan revisi (`requestRevision`), controller membiarkan `status_contract = 'completed'` dan `status_booking = 'completed'`. Karena `hasil_foto_url` tetap ada dan `is_validated_client = false`, tabel Klien dan Fotografer tetap menampilkan teks "Menunggu Konfirmasi Klien". Klien dan fotografer saling menunggu tanpa kepastian.
     - **After**: Di dalam `requestRevision`, kontrak dikembalikan ke `status_contract = 'active'`, `status_booking = 'confirmed'`, dan catatan revisi dicatat. Tabel pemesanan klien dan fotografer menyajikan indikator teks kuning amber "Revisi Diminta", fotografer mendapatkan tombol upload ulang hasil foto, dan halaman galeri klien menampilkan pesan bahwa revisi sedang diproses (Rule #51, #90).
  4. **Bug 19: Pembatalan Otomatis Kontrak Pasca Reschedule Akibat `expired_at` Usang (`ClientBookingController.php` & `PhotographerBookingController.php`)**:
     - **Before**: Saat klien atau fotografer menyetujui tanggal/jam baru di `respondReschedule`, tanggal pemotretan diperbarui namun `expired_at` pada kontrak tidak dihitung ulang berdasarkan jadwal baru (atau pada fotografer membaca relasi lama yang belum di-refresh). Akibatnya, `BookingExpirationService` membatalkan pesanan karena batas waktu mengacu ke sesi lama.
     - **After**: Memanggil `$contract->refresh()` dan menghitung ulang `expired_at` via `$contract->calculatePaymentDeadline()` pada status kontrak non-settled (`pending_payment`, `draft`, `approved`), sehingga tenggat waktu pembayaran selaras 100% dengan jadwal baru (Rule #79).
  5. **Bug 20: Kehilangan 1 Jam pada Booking Tengah Malam (24:00) Format MySQL (`BookingScheduleService.php`)**:
     - **Before**: `formatMinutesToDbTime(1440)` menghasilkan format `'23:59:00'`. Namun, `parseEndTimeToMinutes($time)` hanya mencocokkan `23:59:59`, `23:59`, `24:00`, `24:00:00`. Nilai `'23:59:00'` dari kolom `TIME` MySQL gagal cocok dan jatuh ke `parseTimeToMinutes` = 1439 menit, memotong durasi 4 jam menjadi 3 jam pada pembulatan jam.
     - **After**: Menambahkan format `'23:59:00'` ke dalam kondisi tengah malam `parseEndTimeToMinutes` dan seluruh deteksi blokir libur satu hari penuh, sehingga nilai 1440 menit (24:00) terjaga dengan presisi sempurna.
* **Standar / Rule README yang Dipatuhi**:
  - Rule #14: Standarisasi Tampilan Status Transaksi Berbasis Pure Colored Text.
  - Rule #16: Standar Proteksi Hak Akses Middleware Berdasarkan Role.
  - Rule #51: Standarisasi Copywriting Bersih, Proporsional & Anti-AI Slop (No Buzzwords / Fluff).
  - Rule #63: Standarisasi Alur Login & Intended Redirection.
  - Rule #70: Larangan Terminologi Teknis, Kata Kaku & Pemborosan Simbol "&".
  - Rule #79: Standarisasi Fitur Pengajuan & Persetujuan Jadwal Ulang Sesi Foto (Reschedule).
  - Rule #89: Standarisasi Anti-AI Slop Dokumen Faktur / Invoice.
  - Rule #90: Standarisasi Sinkronisasi Status Booking Klien & Fotografer (100% Simetris).
* **Hasil Pengujian & Verifikasi**:
  - `php -l` seluruh controller, service, dan route: **PASS (0 syntax errors)**.
  - `php artisan view:clear` & `php artisan view:cache`: **PASS (Blade templates cached successfully)**.
  - Runtime tinker verification `parseEndTimeToMinutes('23:59:00') === 1440` dan `formatMinutesToDbTime(1440) === '23:59:00'`: **PASS**.

---

### [2026-11-19 15:30 WITA] — Implementasi Perbaikan 5 Bug Logika Antrean Dashboard, Sorting Terpopuler, dan Laporan Keuangan

* **Modul / Fitur**:
  - Antrean Operasional Dashboard Fotografer (`Photographer/DashboardController`)
  - Sorting Terpopuler / Paling Banyak Dipesan pada Katalog Publik (`Public/PhotographerController`)
  - Kebocoran Dana Batal pada Laporan Keuangan Fotografer (`Photographer/ReportController`)
  - Counter Sesi Selesai Beranda Publik (`Public/PhotographerController`)
  - Accessor Rating Model Fotografer & Filter Review Tersembunyi (`RolePhotographer` & `PhotographerVerificationController`)
* **File yang Diubah**:
  * `app/Http/Controllers/Photographer/DashboardController.php`
  * `app/Http/Controllers/Public/PhotographerController.php`
  * `app/Http/Controllers/Photographer/ReportController.php`
  * `app/Models/RolePhotographer.php`
  * `app/Http/Controllers/Admin/PhotographerVerificationController.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  1. **Bug 11: Koreksi Antrean Operasional "Kirim Hasil Foto" (`Photographer/DashboardController.php`)**:
     - **Before**: `where('status_contract', 'active')->count()` menghitung seluruh sesi aktif, sehingga pemotretan yang baru akan berlangsung berminggu-minggu ke depan langsung memicu badge dan tombol peringatan kuning "Kirim Hasil Foto (1)".
     - **After**: Membatasi antrean hanya untuk sesi yang jadwalnya sudah tiba atau terlewati (`booking_date <= today`) dan file galeri foto belum diunggah (`hasil_foto_url IS NULL` atau kosong).
  2. **Bug 12: Koreksi Pengurutan Terpopuler / Paling Banyak Dipesan (`Public/PhotographerController.php`)**:
     - **Before**: Menggunakan `withCount('contracts')` murni yang turut menghitung booking draft, dibatalkan, dan kadaluarsa.
     - **After**: Mengubah menjadi `withCount(['contracts as contracts_count' => fn($q) => $q->whereIn('status_contract', ['active', 'completed'])])` sehingga fotografer dinilai murni dari pesanan sah yang aktif atau berhasil diselesaikan.
  3. **Bug 13: Proteksi Kontrak Dibatalkan pada Laporan Pendapatan Fotografer (`Photographer/ReportController.php`)**:
     - **Before**: Query default `index` dan kartu `totalEscrowPending` menyertakan `status_payout in ('holding', 'ready')` tanpa mengecualikan kontrak yang dibatalkan, menyebabkan pesanan batal yang menunggu refund admin bocor ke laporan fotografer.
     - **After**: Menambahkan filter `where('status_contract', '!=', 'cancelled')` pada query tabel, settled contracts, dan kalkulasi `totalEscrowPending`.
  4. **Bug 14: Counter Sesi Selesai Beranda Publik Sesuai Standar Rule #90 (`Public/PhotographerController.php`)**:
     - **Before**: Menghitung `whereHas('bookingDetail', fn($q) => $q->where('status_booking', 'completed'))` tanpa mengecek status kontrak dan validasi klien.
     - **After**: Menghitung kontrak yang sah selesai dan divalidasi klien: `where('status_contract', 'completed')->where('is_validated_client', true)`.
  5. **Bug 15: Konsistensi Accessor Rating & Filter Ulasan Tersembunyi (`RolePhotographer.php` & `PhotographerVerificationController.php`)**:
     - **Before**: Accessor rating fotografer dan eager loading di controller verifikasi fotografer tidak memfilter `where('is_hidden', false)`.
     - **After**: Menambahkan `getRatingAttribute` sebagai alias aman ke `getRatingAverageAttribute` yang memfilter `visibleTestimonials()`, serta menambahkan closure `where('is_hidden', false)` pada eager loading `PhotographerVerificationController`.
* **Hasil Pengujian & Verifikasi**:
  - `php -l` seluruh file yang diubah: **PASS (0 syntax errors)**.
  - `php artisan view:clear; php artisan view:cache`: **PASS (0 compilation errors)**.

---

### [2026-11-19 15:05 WITA] — Implementasi Perbaikan 10 Bug Logika Transaksi, Filter Data, & Anti-AI Slop

* **Modul / Fitur**:
  - Logika Validasi Hasil Foto Klien (`is_validated_client`)
  - Sinkronisasi Dana Ditahan & Proteksi Dana Batal pada Monitoring Transaksi & Dashboard Admin
  - Harmonize Dropdown Filter & Controller Query Tagihan Klien (`client/invoices`)
  - Harmonize Dropdown Filter & Controller Query Reservasi Klien (`client/bookings`)
  - Harmonize Dropdown Filter & Controller Query Order Fotografer (`photographer/bookings`)
  - Presedensi Operator SQL Pencarian Kategori (`Admin/CategoryController`)
  - Netralisasi Atribusi Pembatalan Otomatis (`BookingExpirationService`)
  - Pengurutan Numerik Hadiah Lomba Foto (`Admin/ContestController`)
  - Eliminasi Jargon Bahasa Asing & AI Slop pada Tab Transaksi Admin (`admin/transactions/index.blade.php`)
* **File yang Diubah**:
  * `app/Http/Controllers/Public/BookingController.php`
  * `app/Http/Controllers/Public/ChatController.php`
  * `app/Http/Controllers/Admin/TransactionController.php`
  * `app/Http/Controllers/Admin/DashboardController.php`
  * `app/Http/Controllers/Client/ClientBookingController.php`
  * `resources/views/client/invoices/index.blade.php`
  * `resources/views/client/bookings/index.blade.php`
  * `app/Http/Controllers/Photographer/PhotographerBookingController.php`
  * `resources/views/photographer/bookings/index.blade.php`
  * `app/Http/Controllers/Admin/CategoryController.php`
  * `app/Services/BookingExpirationService.php`
  * `app/Http/Controllers/Admin/ContestController.php`
  * `resources/views/admin/transactions/index.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  1. **Bug 1: Pembenahan Prematur `is_validated_client` (Rule #90)**:
     - Mengubah inisialisasi `is_validated_client` dari `true` menjadi `false` pada `BookingController.php:298`, `ChatController.php:1000`, dan `TransactionController.php:247`.
     - Mereset 4 record kontrak aktif yang terdampak di database agar tidak salah dihitung dalam statistik reputasi fotografer.
  2. **Bug 2 & 3: Sinkronisasi Dana Ditahan & Pengecualian Kontrak Batal**:
     - Menambahkan filter `where('status_contract', '!=', 'cancelled')` pada perhitungan `totalEscrowHolding` di `TransactionController.php:121` dan `DashboardController.php:39-42`.
     - Menyelaraskan nominal Dana Ditahan antara Dashboard dan Monitoring Transaksi menggunakan nilai neto `payout_amount`.
  3. **Bug 4: Filter Tagihan Klien Lengkap & Sinkron**:
     - Memperbarui dropdown `resources/views/client/invoices/index.blade.php` dengan opsi riil: `Semua`, `Menunggu Pembayaran`, `DP Terbayar`, `Lunas`, `Dibatalkan`, `Kadaluarsa`.
     - Memperbaiki query backend di `ClientBookingController.php:199-245` agar membedakan tagihan DP dan lunas penuh secara akurat.
  4. **Bug 5: Filter Reservasi Klien Lengkap 7 Status**:
     - Menyinkronkan dropdown `resources/views/client/bookings/index.blade.php` dengan 7 status tabel (`pending_confirmation`, `pending_payment`, `confirmed`, `pending_client`, `completed`, `cancelled`, `expired`).
     - Memperbarui query backend di `ClientBookingController.php:83-126`.
  5. **Bug 6: Filter Order Fotografer Lengkap & Label Selaras**:
     - Menyelaraskan label dropdown filter dengan baris tabel (`Perlu Direview`, `Menunggu Pembayaran Klien`, `Jadwal Terkonfirmasi`, `Menunggu Konfirmasi Klien`, `Selesai`, `Dibatalkan`, `Kadaluarsa`).
     - Memperbaiki query backend di `PhotographerBookingController.php:75-99` agar filter `confirmed` tidak mencampur pesanan yang belum dibayar.
  6. **Bug 7: Grouping Closure pada Pencarian Kategori**:
     - Membungkus klausa `orWhere` dalam closure function `$query->where(function($q) use ($search) { ... })` di `CategoryController.php:40-43` untuk mencegah SQL precedence leak pada soft deletes.
  7. **Bug 8: Netralisasi Atribusi Pembatalan Otomatis**:
     - Mengubah `'dibatalkan_oleh' => 'admin'` menjadi `'dibatalkan_oleh' => null` pada pembatalan otomatis di `BookingExpirationService.php:121`.
  8. **Bug 9: Pengurutan Numerik Hadiah Lomba**:
     - Mengubah `orderBy('hadiah', 'desc')` menjadi `orderByRaw("CAST(NULLIF(REGEXP_REPLACE(hadiah, '[^0-9]', ''), '') AS UNSIGNED) DESC")` di `Admin/ContestController.php:114` agar pengurutan berbasis angka riil, bukan alfabet.
  9. **Bug 10: Eliminasi Buzzword Bahasa Asing & AI Slop (Rule #51 & Rule #70)**:
     - Mengganti teks `100% Platform Revenue` menjadi `Pendapatan Platform` pada `admin/transactions/index.blade.php:865, 1037`.
     - Mengganti header `Invoice & Tanggal` menjadi `Invoice dan Tanggal` per Rule #70.
* **Hasil Pengujian & Verifikasi**:
  - `php -l` seluruh controller: **PASS (0 syntax errors)**.
  - `php artisan view:clear; php artisan view:cache`: **PASS (0 compilation errors)**.

---

### [2026-11-19 14:15 WITA] — Pembersihan Total AI Slop: Eliminasi Card Toolbar & Anti-Code Glorification pada Detail Transaksi serta Penataan Ulang Modal Detail Klien

* **Modul / Fitur**:
  - Halaman Detail Transaksi Admin (`resources/views/admin/transactions/show.blade.php`).
  - Manajemen Klien Admin (`resources/views/admin/users/index.blade.php`).
  - Controller Manajemen Klien Admin (`app/Http/Controllers/Admin/UserController.php`).
  - Dokumen Pelacakan Perubahan (`TRACKING_PERUBAHAN_FILE.md`).
* **File yang Diubah**:
  * `resources/views/admin/transactions/show.blade.php`
  * `resources/views/admin/users/index.blade.php`
  * `app/Http/Controllers/Admin/UserController.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **1. Restrukturisasi Header Halaman Detail Transaksi (Rule #72, Rule #74, & Rule #89 Point 1)**:
    - **Before**: Toolbar atas dibungkus dalam kartu putih melayang (`bg-white rounded-2xl p-4 sm:p-5 border shadow-xs`), menempelkan *pill badge* kotak abu-abu yang mengistimewakan kode invoice `INV-20261118-SZX1RR` di samping tombol aksi cetak (*code glorification / AI slop*), serta mencantumkan teks `Audit Operasional Transaksi Escrow`.
    - **After**: Mengeliminasi total kartu pembungkus putih dan menggantinya dengan **Flat Page Header** standar LensMatch yang menyatu langsung dengan latar halaman (selaras dengan halaman verifikasi fotografer dan formulir admin):
      - Sisi Kiri: Breadcrumb netral (`Monitoring Transaksi > Detail Transaksi`) dan judul H1 `Detail Transaksi`.
      - Sisi Kanan: Tombol aksi utama `[ Cetak / Simpan PDF ]` dan tombol sekunder netral `[ Kembali ]` (menggunakan ikon modern single chevron `<` per Rule #72).
      - Menghapus total *pill badge* kode invoice di samping tombol (kepatuhan mutlak Rule #89 Point 1).
  * **2. Eliminasi Total Terminologi Asing / Jargon "Escrow" (Rule #89 Point 2)**:
    - **Before**: Judul panel menggunakan terminologi `Panel Kontrol Transaksi Escrow` dan subjudul status `Status Operasional Escrow`.
    - **After**: Mengubah judul panel menjadi bahasa Indonesia baku, formal, dan profesional: `Panel Pengawasan Transaksi` serta `Status Operasional` (nol kata "Escrow").
    - Menghapus badge kode redundan di sebelah kanan judul panel pengawasan.
  * **3. Penataan Bersih Modal Detail Profil Klien (Anti-AI Slop & Rule #70)**:
    - **Before**:
      - Status akun dan status verifikasi email digabung secara horizontal menggunakan dua teks warna hijau terang yang dipisahkan tanda titik bullet (`Email Terverifikasi • Akun Aktif`).
      - Terdapat tautan teks hijau `Chat WhatsApp` di samping nomor HP klien.
      - Header kolom riwayat pemesanan menggunakan tanda ampersand: `FOTOGRAFER & PAKET` dan kolom aksi bertuliskan `FAKTUR`.
      - Subteks baris pesanan menempelkan kode kontrak `#KTRK-00019`.
      - Empat kotak metrik klien menggunakan label SaaS template generik.
    - **After**:
      - Menghapus total tautan `Chat WhatsApp`. Nomor HP klien kini berstatus data kontak murni teks biasa yang netral.
      - Menghapus tumpukan horizontal warna hijau. Status akun (`Aktif` / `Dibekukan`) dan verifikasi email (`Terverifikasi` / `Belum Terverifikasi`) kini disusun secara struktural dalam baris key-value vertikal yang tenang dan teratur di sebelah kanan.
      - Mengubah header tabel menjadi bahasa Indonesia baku tanpa simbol `&` redundan: `Layanan Fotografi` dan kolom aksi baku `Aksi` (Rule #70 & Rule #80).
      - Menghapus teks kode `#KTRK-xxxxx` dari subteks riwayat pemesanan.
      - Menyelaraskan proporsi 4 ringkasan informasi personal klien (`Total Booking`, `Total Belanja`, `Ulasan Ditulis`, dan `Partisipasi`).
* **Kepatuhan Standar / Rule**:
  - **Rule #51**: Standarisasi Copywriting Bersih, Proporsional & Anti-AI Slop.
  - **Rule #61**: Standarisasi Hierarki Warna Sistem.
  - **Rule #67**: Larangan Mutlak Bulatan Status / Dot Badge & Tumpukan Status Warna-Warni.
  - **Rule #70**: Eliminasi Total Simbol "&" Redundan dan Jargon Asing.
  - **Rule #72**: Standar Single Chevron `<` untuk Navigasi Kembali.
  - **Rule #89**: Larangan Mengistimewakan Kode (Anti-Code Glorification) & Eliminasi Jargon Escrow.
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear; php artisan view:cache`: **PASS** (Zero error).
  * `php artisan route:clear; php artisan config:clear`: **PASS**.

---

### [2026-11-19 13:40 WITA] — Implementasi Halaman Audit Detail Transaksi Internal Admin (@extends('layouts.admin')) & Pembersihan Bug Teks Melayang "Tutup" pada Monitoring Transaksi

* **Modul / Fitur**:
  - Monitoring Transaksi Admin (`resources/views/admin/transactions/index.blade.php`).
  - Halaman Audit Transaksi Internal Admin (`resources/views/admin/transactions/show.blade.php`).
  - Controller Transaksi Admin (`app/Http/Controllers/Admin/TransactionController.php`).
  - Controller Manajemen Klien Admin (`app/Http/Controllers/Admin/UserController.php`).
  - Cetak Pratinjau Dokumen Invoice (`resources/views/public/booking/invoice-print.blade.php`).
  - Dokumen Pelacakan Perubahan (`TRACKING_PERUBAHAN_FILE.md`).
* **File yang Diubah / Dibuat**:
  * `resources/views/admin/transactions/show.blade.php` (Baru)
  * `resources/views/admin/transactions/index.blade.php` (Dimodifikasi)
  * `app/Http/Controllers/Admin/TransactionController.php` (Dimodifikasi)
  * `app/Http/Controllers/Admin/UserController.php` (Dimodifikasi)
  * `resources/views/public/booking/invoice-print.blade.php` (Dimodifikasi)
  * `TRACKING_PERUBAHAN_FILE.md` (Dimodifikasi)
* **Rincian Perubahan (Before vs After)**:
  * **1. Pembersihan Bug Teks Melayang "Tutup" pada Tabel Transaksi**:
    - **Penyebab**: Di `resources/views/admin/transactions/index.blade.php` baris 1142–1268 terdapat sisa kode yatim (*orphaned markup*) dari modal yang tag pembukanya `<template>` telah terhapus, tetapi blok penutup beserta tombol footer `<button @click="detailModalOpen = false">Tutup</button>` tertinggal di luar template dalam aliran HTML reguler. Akibatnya, tombol tersebut merender teks telanjang "Tutup" melayang di tepi kanan tabel monitoring transaksi.
    - **Perbaikan**: Menghapus 129 baris kode yatim tersebut secara bersih sehingga struktur DOM kembali valid dan bersih tanpa ada teks melayang.
  * **2. Implementasi Opsi 2: Halaman Audit Transaksi Internal Admin (`@extends('layouts.admin')`)**:
    - **Before**: Tombol `[ Lihat ]` pada tabel monitoring transaksi admin dan riwayat transaksi pada modal klien mengarahkan admin ke halaman cetak printer `http://127.0.0.1:8000/pembayaran/{id}/cetak` (`invoice-print.blade.php`). Halaman cetak tersebut sengaja berformat `<!DOCTYPE html>` murni tanpa layout dashboard admin (tanpa Sidebar kiri dan tanpa Topbar kanan atas) agar printer tidak mencetak navigasi website.
    - **After**: Dibuat halaman internal khusus admin `resources/views/admin/transactions/show.blade.php` yang mewarisi layout `@extends('layouts.admin')`. Admin kini tetap berada di dalam ekosistem internal dashboard dengan **Sidebar kiri** dan **Topbar kanan atas** yang utuh dan konsisten.
    - **Fitur Halaman Audit Transaksi Internal Admin**:
      - Toolbar navigasi atas: Tombol `[ Kembali ke Monitoring Transaksi ]` dan tombol `[ Cetak / Simpan PDF ]` yang membuka `/pembayaran/{id}/cetak` pada tab baru (`target="_blank"`).
      - Panel Kontrol Transaksi Escrow:
        - Finansial Platform: Total pembayaran, Komisi Platform 10%, dan Pendapatan Bersih Mitra 90%.
        - Rekening Bank Fotografer: Nama bank, nomor rekening, dan nama pemilik rekening.
        - Status Operasional: Status dana escrow, status sesi pemotretan, catatan/alasan pembatalan (jika ada), dan riwayat waktu transfer (jika ada).
        - Bukti Transfer Klien: Thumbnail berkas transfer klien dengan modal lightbox resolusi penuh.
        - Tombol Aksi Operasional Admin: `[ Verifikasi Bayar ]` (POST `admin.transactions.verify-payment`), `[ Tolak Bukti Bayar ]` (modal input alasan penolakan), `[ Transfer Dana Fotografer ]` (modal input rekening, dropzone unggah bukti struk bank transfer, dan catatan transfer), serta `[ Proses Refund ]` (modal input catatan refund jika pesanan dibatalkan).
      - Penyematan Lembar Dokumen Invoice Resmi: Seluruh rincian tagihan invoice resmi disematkan langsung di dalam kartu dokumen internal agar admin dapat membaca rincian pemesanan klien tanpa berpindah halaman.
  * **3. Sinkronisasi Controller**:
    - `TransactionController@show`: Menyiapkan relasi lengkap (`client.user`, `photographer.user`, `photographer.province`, `photographer.city`, `bookingDetail.service`, `bookingDetail.province`, `bookingDetail.city`, `payments`, `testimonial`, `cancellation`), menghitung potongan komisi 10% dan pendapatan bersih 90%, serta merender `view('admin.transactions.show')`.
    - `UserController@show`: Mengubah `invoice_url` pada riwayat transaksi booking klien dari `booking.invoice.print` menjadi `route('admin.transactions.show', $c->id)`.
    - `resources/views/admin/transactions/index.blade.php`: Mengubah tautan tombol `[ Lihat ]` dari `route('booking.invoice.print', $c->id)` menjadi `route('admin.transactions.show', $c->id)`.
    - `resources/views/public/booking/invoice-print.blade.php`: Menyesuaikan tautan tombol kembali khusus admin agar mengarah ke `route('admin.transactions.show', $contract->id)`.
* **Kepatuhan Standar / Rule**:
  - **Rule #51 & Rule #70**: Bahasa Indonesia baku, to-the-point, tanpa kata-kata alay, tanpa AI slop, dan nol simbol `&` redundan.
  - **Rule #59**: Modal konfirmasi diletakkan di tengah (*center*), judul modal bersih tanpa ikon hiasan, maksimal 2 tombol aksi di bagian bawah modal (`Batal` di kiri, aksi submit di kanan).
  - **Rule #80**: Tombol aksi menggunakan teks murni yang jelas (`Lihat`, `Batal`, `Verifikasi Bayar`, `Tolak Bukti Bayar`, `Cetak / Simpan PDF`).
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear; php artisan view:cache`: **PASS** (Zero error).
  * `php artisan route:clear; php artisan config:clear`: **PASS**.
  * Navigasi tabel ke detail audit transaksi: Berjalan mulus di dalam layout Admin dengan sidebar dan topbar persisten.

---

### [2026-11-19 13:25 WITA] — Perbaikan Bug Popover Dropdown Kebab Menu pada Tabel Manajemen Klien (Penanganan Clipping Overflow-X-Auto & Standardisasi Ikon SVG)

* **Modul / Fitur**:
  - Manajemen Klien Admin (`resources/views/admin/users/index.blade.php`).
* **File yang Diubah**:
  * `resources/views/admin/users/index.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Penyebab Bug**:
    - Kontainer pembungkus tabel memiliki kelas `overflow-x-auto` tanpa batas tinggi minimum (`min-height`). Ketika data tabel hanya memiliki 1 baris, tinggi tabel hanya ~60px.
    - Saat tombol titik tiga (`[ ⋮ ]`) diklik, popover menu melayang (`w-44`, tinggi ~85px) berhasil terbuka, tetapi posisinya melebihi batas bawah tabel yang hanya 60px. Akibatnya, menu tersembunyi (*clipped*) di bawah lipatan container tabel dan memicu munculnya scrollbar vertikal di tepi kanan tanpa menampilkan menu ke layar pandang pengguna.
    - Selain itu, tombol menggunakan karakter teks mentah `⋮` alih-alih ikon Vektor SVG asli bawaan sistem, serta penempatan `@click.away` berada pada wrapper induk tombol.
  * **Solusi Perbaikan (Rule #6 & Rule #69)**:
    - Menambahkan `min-h-[220px]` pada kontainer `<div class="overflow-x-auto min-h-[220px]">`, sehingga tabel memiliki ruang bernapas vertikal yang cukup lapang untuk membuka dropdown popover menu ke bawah secara bebas tanpa terpotong dan tanpa memicu scrollbar vertikal pada data 1-2 baris.
    - Mengganti teks mentah `⋮` dengan Vektor SVG lingkaran 3 titik (`<svg class="w-4 h-4 pointer-events-none" ...>`) sesuai **Rule #6**.
    - Memindahkan `@click.away="menuOpen = false"` secara presisi ke kontainer popover melayang dengan kelas `top-full mt-1.5` dan transisi Alpine.js standar sistem selaras dengan modul Lomba dan E-Learning.
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear; php artisan view:cache`: **PASS** (Zero error).
  * Uji klik interaktif: Dropdown popover kini membuka dengan transisi halus langsung di bawah tombol titik tiga tanpa terpotong (*zero vertical clipping*).

---

### [2026-11-19 13:15 WITA] — Implementasi Pola B Cerdas: Bantuan Aktivasi Akun Klien & Peningkatan Fitur Komprehensif Manajemen Klien Admin

* **Modul / Fitur**:
  - Halaman Verifikasi Email Klien (`resources/views/public/auth/verify-email.blade.php`).
  - Controller Verifikasi & Bantuan Aktivasi (`app/Http/Controllers/Auth/VerificationController.php`).
  - Manajemen Klien Admin (`resources/views/admin/users/index.blade.php`).
  - Controller Manajemen Klien Admin (`app/Http/Controllers/Admin/UserController.php`).
  - Model Pengguna (`app/Models/User.php`).
  - Skema Database Pengguna (`database/migrations/2026_11_19_000001_add_activation_and_suspension_to_users_table.php`).
  - Sidebar Navigasi Admin (`resources/views/partials/admin/sidebar.blade.php`).
  - Rute Aplikasi (`routes/web.php`).
  - Pengendali Autentikasi (`app/Http/Controllers/Auth/AuthController.php`).
* **File yang Diubah / Dibuat**:
  * `database/migrations/2026_11_19_000001_add_activation_and_suspension_to_users_table.php` (Baru)
  * `app/Http/Controllers/Auth/VerificationController.php` (Baru)
  * `resources/views/public/auth/verify-email.blade.php` (Baru)
  * `app/Models/User.php`
  * `app/Http/Controllers/Admin/UserController.php`
  * `resources/views/admin/users/index.blade.php`
  * `resources/views/partials/admin/sidebar.blade.php`
  * `routes/web.php`
  * `app/Http/Controllers/Auth/AuthController.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Pola B "Versi Cerdas & Terfokus" untuk Verifikasi Email Klien (Rule #51, #59, #70)**:
    - **Before**: Sistem belum memiliki halaman resmi notifikasi verifikasi email (`/email/verify`) maupun alur pengaduan jika email OTP/tautan aktivasi tidak masuk ke inbox klien. Klien terdampar tanpa saluran komunikasi langsung ke admin.
    - **After**:
      1. Menghadirkan tampilan bersih `resources/views/public/auth/verify-email.blade.php` dengan kartu otentikasi LensMatch berjarak lapang.
      2. Tombol aksi `[ Butuh Bantuan Aktivasi? ]` memicu modal pop-up interaktif (Rule #59: judul tengah tanpa ikon, formulir pesan kendala singkat, dan maksimal 2 tombol tengah: `Batal` dan `Kirim Permintaan Bantuan`).
      3. Saat klien mengirim permintaan bantuan, sistem mencatat `activation_requested_at` dan `activation_request_note` pada tabel `users`, serta otomatis mengirimkan **Notifikasi Sistem Real-Time ke seluruh Admin**: *"Permintaan Bantuan: [Nama] ([Email]) meminta verifikasi akun manual."*
      4. Klien melihat kartu notifikasi bahwa permintaannya sedang ditinjau admin, dan cukup menyegarkan (*refresh*) halaman setelah diverifikasi untuk langsung masuk ke sistem.
  * **Peningkatan Fitur Komprehensif Manajemen Klien Admin (Rule #64, #68, #69, #80)**:
    - **Penyelarasan Nama Menu**: Mengubah judul halaman dan menu sidebar dari "Manajemen User" menjadi **"Manajemen Klien"** agar selaras dan tidak rancu dengan Manajemen Fotografer dan Manajemen Admin Staf.
    - **4 Kartu Metrik KPI (Rule #64 Compliant)**: Terdiri dari 2 baris murni font `text-xs`: `Total Klien Terdaftar`, `Email Terverifikasi`, `Menunggu Aktivasi Manual`, dan `Total Belanja Klien`.
    - **Indikator Status Akun & Badge Menunggu Aktivasi**:
      - Pada kolom Klien, akun yang meminta bantuan aktivasi langsung memunculkan penanda badge: `Menunggu Aktivasi Manual`.
      - Menampilkan status fungsional teks murni (**Rule #14 & Rule #68**): Status Email (`Terverifikasi` vs `Belum Verifikasi`) dan Status Akun Moderasi (`Aktif` vs `Dibekukan`).
    - **Tombol Aksi Baku (Rule #80 & Rule #69)**:
      1. **Tombol `[ Lihat ]` (Soft Blue)**: Membuka modal profil komprehensif yang memuat biodata klien, nomor WhatsApp dengan tautan langsung `https://wa.me/...`, domisili, status akun, catatan kendala aktivasi, 4 kartu ringkasan personal, serta tabel 5 riwayat booking terakhir lengkap dengan tautan cetak faktur invoice.
      2. **Tombol `[ Verifikasi Email ]` (Soft Emerald)**: Tampil satu-klik pada akun yang belum terverifikasi untuk memverifikasi akun klien secara instan (`email_verified_at = now()`), membersihkan antrean permintaan aktivasi, mencatat `ActivityLog`, dan mengirim notifikasi selamat datang ke akun klien.
      3. **Menu Kebab `⋮`**: Memuat aksi moderasi `[ Bekukan Akun ]` (dengan modal alasan pembekuan) / `[ Aktifkan Akun ]`, serta aksi `[ Hapus Akun ]` yang diproteksi ketat hanya untuk klien dengan 0 riwayat transaksi (Rule #2).
    - **Proteksi Moderasi pada Login (`AuthController.php`)**:
      - Akun yang dibekukan (`is_suspended = true`) otomatis ditolak saat login dengan pesan peringatan informatif beserta alasan pembekuannya.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #51**: Copywriting Bersih, Proporsional & Anti-AI Slop (No Buzzwords / Fluff).
  * **Rule #59**: Standarisasi Global Modal / Pop-Up (Judul Center Tanpa Ikon, Center Buttons, Maksimal 2 Tombol Bawah, Tanpa 'X' jika ada Batal).
  * **Rule #64**: Standarisasi KPI Metric Cards (2 Baris Murni Ukuran Seragam `text-xs`, Tanpa Teks Ketiga Redundan).
  * **Rule #67 & #68**: Larangan Bulatan Dot Badge & Pewarnaan Teks Non-Status (Pure Colored Text pada kolom status).
  * **Rule #69**: Standarisasi Tombol Kolom Aksi Tabel (Maksimal 2 Tombol Horizontal + Kebab Dropdown untuk Aksi Sekunder).
  * **Rule #70**: Larangan Terminologi Teknis, Kata Kaku & Pemborosan Simbol "&".
  * **Rule #80**: Standarisasi Teks & Warna Tombol Aksi Tabel (Trio Baku: `Lihat`, `Edit`, `Hapus` + Aksi Khusus `Verifikasi Email`).
* **Hasil Pengujian & Verifikasi**:
  * Database Migration: **PASS** (`2026_11_19_000001_add_activation_and_suspension_to_users_table`).
  * `php artisan view:clear; php artisan route:clear; php artisan view:cache`: **PASS** (Zero error).
  * Backend Unit Test Script (`test_client_management.php`): **PASS** (Semua alur aktivasi manual, modal show payload, verifikasi email, kirim notifikasi in-app, penangguhan suspend/unsuspend, dan proteksi hapus berjalan 100% sukses).

---

### [2026-11-19 11:45 WITA] — Sinkronisasi Opsi Dropdown Filter Status Transaksi Admin & Fotografer (Penambahan Filter Dibatalkan & Kadaluarsa 1:1 dengan Tabel)

* **Modul / Fitur**:
  - Monitoring Transaksi Admin (`resources/views/admin/transactions/index.blade.php`).
  - Controller Transaksi Admin (`app/Http/Controllers/Admin/TransactionController.php`).
  - Kelola Booking Fotografer (`resources/views/photographer/bookings/index.blade.php`).
* **File yang Diubah**:
  * `resources/views/admin/transactions/index.blade.php`
  * `app/Http/Controllers/Admin/TransactionController.php`
  * `resources/views/photographer/bookings/index.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Sinkronisasi Dropdown Filter Monitoring Transaksi Admin (Rule #70 & Anti-AI Slop)**:
    - **Before**: Dropdown filter status transaksi hanya menyediakan opsi pencairan dana (`all`, `holding`, `ready`, `paid`, `pending_payment`, `refunded`), sehingga opsi `Dibatalkan` dan `Kadaluarsa` hilang dari filter meskipun datanya tampil di baris tabel (TD). Controller juga belum memiliki handler query untuk `cancelled` dan `expired`.
    - **After**: Menambahkan opsi `cancelled` (`Dibatalkan`) dan `expired` (`Kadaluarsa`) ke dalam array `$statusLabels` pada `admin/transactions/index.blade.php`. Pada `TransactionController.php`, query filter diperbarui untuk menyaring transaksi `cancelled` (dibatalkan pengguna/fotografer) dan `expired` (dibatalkan otomatis oleh sistem karena batas waktu habis). Hasil audit pengujian: 17 total transaksi terbagi presisi menjadi 4 Dana Ditahan, 9 Sudah Ditransfer, 1 Dibatalkan, dan 3 Kadaluarsa.
  * **Penyelarasan Filter Kelola Booking Fotografer**:
    - **Before**: Dropdown filter pada `photographer/bookings/index.blade.php` hanya menampilkan `all`, `pending`, `confirmed`, dan `completed`. Opsi `cancelled` (`Dibatalkan`) hilang dari tampilan dropdown meskipun controller `PhotographerBookingController.php` sudah memiliki penanganan filternya.
    - **After**: Menambahkan `'cancelled' => 'Dibatalkan'` ke dalam array `$statusLabels`, menyelaraskan 100% opsi filter dengan baris tabel status pesanan fotografer.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #51**: Standarisasi Copywriting Bersih, Proporsional & Anti-AI Slop (No Buzzwords / Fluff).
  * **Rule #70**: Anti-AI Slop Terminology & Natural Human Text Standard (Teks to-the-point tanpa tanda `&` redundan).
  * **Integrasi Status 1:1**: Konsistensi mutlak antara kolom tabel (TD) dengan opsi dropdown penyaring (*Filter Toolbar*).
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear` & `php artisan view:cache`: **PASS** (Zero error).
  * Query Unit Test: **PASS** (Filter `cancelled`: 1 kontrak, Filter `expired`: 3 kontrak, Filter `all`: 17 kontrak).

---

### [2026-11-19 11:30 WITA] — Konsolidasi Solusi Pilihan A: Eliminasi Redundansi Modal Transaksi, Penyatuan Tampilan Invoice Resmi & Integrasi Panel Audit Admin

* **Modul / Fitur**:
  - Monitoring Transaksi Admin (`resources/views/admin/transactions/index.blade.php`).
  - Invoice Resmi Cetak (`resources/views/public/booking/invoice-print.blade.php`).
  - Controller Booking Publik (`app/Http/Controllers/Public/BookingController.php`).
* **File yang Diubah**:
  * `resources/views/admin/transactions/index.blade.php`
  * `resources/views/public/booking/invoice-print.blade.php`
  * `app/Http/Controllers/Public/BookingController.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Konsolidasi Kolom Aksi Tabel Monitoring Transaksi (Rule #80 & Rule #69)**:
    - **Before**: Baris tabel monitoring transaksi memiliki dua aksi redundan: tombol pop-up modal `[ Lihat ]` dan kebab dropdown menu dengan pilihan `[ Lihat Invoice ]`. Modal `[ Lihat ]` memiliki bug header terpotong (*cropped header bug*) akibat kontainer scrollbar sempit `max-h-[85vh] overflow-y-auto`, desain klaustrofobik bertumpuk ("box-in-box" sempit 576px), serta menduplikasi 80% isi dokumen invoice.
    - **After**: Mengeliminasi modal sempit `detailModalOpen` dan menu kebab redundan pada kolom aksi tabel. Baris tabel kini hanya memiliki **satu tombol tunggal** yang bersih dan elegan: `[ Lihat ]` (warna Soft Blue: `bg-blue-50 text-blue-600 border-blue-200` per **Rule #80**) yang mengarahkan admin langsung dalam tab yang sama ke pratinjau dokumen invoice resmi (`route('booking.invoice.print')`).
  * **Penambahan Panel Audit Admin pada Invoice Resmi (Rule #51 & Rule #70)**:
    - **Before**: Halaman invoice resmi (`invoice-print.blade.php`) hanya menampilkan lembar invoice format kertas A4 tanpa informasi internal admin (komisi platform 10%, pendapatan bersih fotografer, nomor rekening bank fotografer, foto bukti transfer, status pencairan, dan kontrol verifikasi/tolak).
    - **After**: Khusus saat diakses oleh pengguna dengan role Admin / Super Admin (`$isAdminUser`), halaman invoice resmi menampilkan **Panel Audit Admin** (`.no-print`) yang lapang dan proporsional di atas lembar dokumen cetak:
      1. **Finansial Platform**: Menampilkan Total Nilai Kontrak, Komisi Platform (10%), dan Pendapatan Bersih Fotografer dengan tipografi bersih (*non box-in-box*).
      2. **Rekening Bank Fotografer**: Menampilkan Nama Bank, Nomor Rekening, dan Nama Pemilik Rekening sesuai profil mitra.
      3. **Status Operasional**: Menampilkan status dana (Dana Ditahan / Siap Ditransfer / Sudah Ditransfer / Dana Dikembalikan), status sesi, serta riwayat pembatalan (jika ada) tanpa terminologi asing seperti "Escrow" atau "Payout" (**Rule #70**).
      4. **Bukti Transfer & Aksi Cepat**: Menampilkan miniatur thumbnail bukti transfer dengan modal Lightbox pembesar resolusi penuh, serta tombol aksi operasional terintegrasi: `[ Verifikasi Bayar ]`, `[ Tolak Bukti Bayar ]`, `[ Transfer Dana Fotografer ]`, dan `[ Proses Refund ]` yang langsung memproses dan me-refresh status halaman dengan alert flash notification.
  * **Integritas Dokumen Cetak (@media print)**:
    - Seluruh elemen Panel Audit Admin dan tombol navigasi menggunakan kelas `.no-print` (`display: none !important`), memastikan dokumen yang dicetak atau disimpan sebagai PDF melalui tombol `[ Cetak / Simpan PDF ]` tetap 100% berupa lembaran resmi invoice LensMatch yang bersih, profesional, dan bebas dari panel kontrol admin.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #51**: Standarisasi Copywriting Bersih, Proporsional & Anti-AI Slop (No Buzzwords / Fluff).
  * **Rule #61**: Standarisasi Hierarki Warna Sistem (Aksen Amber Brand, Soft Blue untuk Lihat, Emerald untuk Sukses/Verifikasi, Rose untuk Tolak/Batal).
  * **Rule #69**: Standarisasi Tombol Kolom Aksi Tabel (Eliminasi Redundansi & Tombol Bertumpuk).
  * **Rule #70**: Larangan Terminologi Asing & Pemborosan Simbol "&" (Anti-AI Slop Terminology: Penggantian istilah Escrow & Payout, penghilangan tanda & redundan).
  * **Rule #80**: Standarisasi Teks & Warna Tombol Aksi Tabel (Trio Baku: Tombol Soft Blue `Lihat`).
  * **Anti-AI Slop Design Principles**: Penghapusan penyakit "Box-in-Box", eliminasi "Amnesia Skala Spasial" dan "Bias Statistik Dataset".
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear` & `php artisan view:cache`: **PASS** (Zero error).

---

### [2026-11-19 11:10 WITA] — Standarisasi Kebab Action Dropdown Menu, Eliminasi Kebocoran Padding Putih Hover, Penyelarasan Ikon Amber & Koreksi Aksi Invoice

* **Modul / Fitur**:
  - Monitoring Transaksi Admin (`resources/views/admin/transactions/index.blade.php`).
  - Kelola Lomba Admin (`resources/views/admin/contests/index.blade.php`).
  - Materi E-Learning Admin (`resources/views/admin/elearning/index.blade.php`).
  - Kelola Booking Mitra Fotografer (`resources/views/photographer/bookings/index.blade.php`).
  - Kelola Booking Klien (`resources/views/client/bookings/index.blade.php`).
  - Riwayat Pembayaran Invoice Klien (`resources/views/client/invoices/index.blade.php`).
  - Dropdown Profil Pengguna Navbar Publik (`resources/views/partials/public/navbar.blade.php`).
* **File yang Diubah**:
  * `resources/views/admin/transactions/index.blade.php`
  * `resources/views/admin/contests/index.blade.php`
  * `resources/views/admin/elearning/index.blade.php`
  * `resources/views/photographer/bookings/index.blade.php`
  * `resources/views/client/bookings/index.blade.php`
  * `resources/views/client/invoices/index.blade.php`
  * `resources/views/partials/public/navbar.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Koreksi Penamaan Aksi & Eliminasi Tab Baru pada Invoice Transaksi (Rule #69, Rule #70, Rule #80)**:
    - **Before**: Tombol aksi invoice di tabel transaksi admin tertulis `Cetak Invoice` dan memiliki atribut `target="_blank"`. Padahal rute yang dibuka adalah halaman pratinjau invoice yang di dalamnya sudah memiliki tombol khusus `Cetak / Simpan PDF`. Akibat `target="_blank"`, browser dipaksa membuka tab baru alih-alih navigasi langsung di tab yang sama.
    - **After**: Mengubah teks tombol menjadi ringkas dan tepat sasaran: `Lihat Invoice` (tanpa kata tambahan berlebihan), serta menghapus `target="_blank"` sehingga transisi membuka dokumen langsung di tab yang sama dengan alur kembali yang mulus via tombol back yang sudah tersedia.
  * **Eliminasi Kebocoran Padding Putih pada Efek Hover Dropdown Kebab (Rule #61, Rule #70, Micro-Interaction Integrity)**:
    - **Before**: Kontainer dropdown kebab menu pada tabel transaksi admin, lomba, e-learning, booking fotografer, dan booking klien menggunakan padding vertikal `py-1.5` atau `py-1` tanpa `overflow-hidden`. Hal ini menyebabkan efek sorotan latar `hover:bg-amber-50` pada item terpotong 4px–6px sebelum tepi kartu, menyisakan garis/celah putih di tepi atas dan bawah. Selain itu, pada baris transaksi tanpa aksi persetujuan sebelumnya, divider `<div class="border-t ... my-1">` tetap ter-render di atas item satu-satunya sehingga memperparah celah putih.
    - **After**: Menghapus `py-1.5`/`py-1` dari kontainer kartu, menambahkan `overflow-hidden` pada kartu sudut `rounded-xl`, membungkus divider dalam kondisi `$hasPriorAction`, dan menerapkan padding item yang presisi `px-4 py-2.5`. Latar hover kini mengisi penuh sudut lengkung kartu (*full-bleed edge-to-edge*) secara solid tanpa kebocoran warna putih, sementara jarak ruang baca konten ke tepi kartu tetap terjaga 100% konsisten dan nyaman dipandang mata.
  * **Sinkronisasi Ikon SVG Mengikuti Warna Hover Amber (Rule #61 & Visual Harmony)**:
    - **Before**: Ikon SVG dokumen pada menu invoice admin memiliki kelas warna statis `text-gray-400` tanpa pendeteksi hover grup, sehingga saat item di-hover dan teks berubah menjadi amber (`text-amber-600`), ikon tetap berwarna abu-abu mati.
    - **After**: Menambahkan kelas `group` pada link dan menerapkan kelas warna dinamis pada ikon: `text-gray-400 group-hover:text-amber-600 dark:text-gray-400 dark:group-hover:text-amber-400 transition-colors`. Ikon dan teks kini berubah warna serempak secara harmonis.
  * **Pembersihan Copywriting Anti-AI Slop pada Navbar (Rule #70)**:
    - **Before**: Tombol logout pada navbar publik menggunakan teks bergaya ganda dengan tanda garis miring: `Keluar / Logout`.
    - **After**: Merapikan teks menjadi ringkas, natural, dan to-the-point: `Keluar Akun` sesuai standar bahasa Indonesia platform LensMatch.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #61**: Standarisasi Hierarki Warna Brand LensMatch & Interactive Color Harmony.
  * **Rule #69**: Standarisasi Tombol Kolom Aksi Tabel & Kebab Dropdown Menu Popover.
  * **Rule #70**: Larangan Terminologi Teknis, Kata Kaku & Pemborosan Simbol (Anti-AI Slop Terminology & Natural Human Text Standard).
  * **Rule #80**: Standarisasi Teks & Warna Tombol Aksi Tabel (Prinsip Kesederhanaan & Eliminasi Redundansi).
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear` & `php artisan view:cache`: **PASS** (Zero error).

---

### [2026-11-17 17:15 WITA] — Sinkronisasi Warna Ikon Aktif & Hover pada Tab Navigasi Aktivitas Komunitas

* **Modul / Fitur**:
  - Halaman Aktivitas Komunitas Klien (`resources/views/client/activities/index.blade.php`).
* **File yang Diubah**:
  * `resources/views/client/activities/index.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Sinkronisasi Warna Ikon dengan Status Aktif Tab (Rule #61 & Visual Harmony)**:
    - **Before**: Ikon SVG di samping judul tab memiliki kelas warna statis `text-gray-400`, sehingga saat tab aktif dan teksnya berwarna amber, ikon tetap berwarna abu-abu.
    - **After**: Menambahkan `group` pada tombol tab dan binding kelas dinamis pada elemen SVG: `:class="activeTab === '...' ? 'text-amber-500 dark:text-amber-400' : 'text-gray-400 group-hover:text-amber-500 dark:group-hover:text-amber-400'"`.
    - **Hasil Akhir**: Ikon, teks, garis bawah, dan lencana hitungan kini berubah warna menjadi amber secara serentak dan harmonis 100%.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #61**: Standarisasi Hierarki Warna Brand LensMatch.
  * **Rule #70**: Visual Consistency & Interactive Color Harmony.
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear` & `php artisan view:cache`: **PASS** (Zero error).


* **Modul / Fitur**:
  - Halaman Aktivitas Komunitas Klien (`resources/views/client/activities/index.blade.php`).
* **File yang Diubah**:
  * `resources/views/client/activities/index.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Eliminasi Divider Garis Penuh Membelah Layar (Rule #58 & Rule #41)**:
    - **Before**: Navigasi tab dibungkus dalam kontainer bergaris `border-b border-gray-200 dark:border-gray-700` yang membelah halaman secara horizontal penuh, serta tombol aktif menggunakan `border-b-2`.
    - **After**: Menghapus seluruh garis divider panjang tersebut selaras dengan standar `client/invoices/index.blade.php` dan `admin/transactions/index.blade.php`. Menggunakan penanda aktif teks berjarak murni (*text underline with offset*): `underline underline-offset-8 decoration-2 decoration-amber-500 dark:decoration-amber-400` dan pill hitungan badge lembut (`bg-amber-100 text-amber-700`).
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #41**: Larangan Mutlak Menambahkan UI Garis Pembatas (Divider / Border-B) Sembarangan.
  * **Rule #58**: Standarisasi Tampilan Konten & Elemen Aktif (Eliminasi Divider Panjang & Garis Bawah Menempel).
  * **Rule #61**: Standarisasi Aksen Amber LensMatch.
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear` & `php artisan view:cache`: **PASS** (Zero error).


* **Modul / Fitur**:
  - Halaman Aktivitas Komunitas Klien (`resources/views/client/activities/index.blade.php`).
  - Controller Aktivitas Klien (`app/Http/Controllers/Client/ClientActivityController.php`).
  - Dropdown Profil Navbar (`resources/views/partials/public/navbar.blade.php`).
  - Rute Web Aplikasi (`routes/web.php`).
* **File yang Diubah / Dibuat**:
  * `app/Http/Controllers/Client/ClientActivityController.php` (Baru)
  * `resources/views/client/activities/index.blade.php` (Baru)
  * `resources/views/partials/public/navbar.blade.php`
  * `routes/web.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Halaman Terpadu 1-Pintu untuk Aktivitas Klien (Opsi 1)**:
    - **Before**: Klien tidak memiliki tempat untuk memantau progres materi kursus yang sedang dipelajari di e-learning, status pendaftaran dan submisi karya di lomba foto, maupun riwayat topik diskusi di forum komunitas. Klien harus mencari secara manual di halaman publik.
    - **After**: Membangun halaman terpadu `/client/activities` yang memiliki **3 Tab Interaktif**:
      1. **Tab 1: E-Learning Saya**: Menampilkan kursus yang sedang diikuti, bar progres penyelesaian materi, tombol 1-klik *Lanjutkan Belajar*, serta tombol *Unduh Sertifikat* (jika sudah 100% selesai).
      2. **Tab 2: Lomba Foto**: Menampilkan kompetisi yang didaftari, status verifikasi pendaftaran, status pelunasan tiket masuk, status submisi foto karya dengan modal lightbox resolusi penuh (**Rule #97**), serta tautan langsung ke detail kompetisi.
      3. **Tab 3: Diskusi Forum**: Menampilkan daftar topik yang dibuat klien di forum komunitas, jumlah tanggapan balasan, tanggal posting, dan tautan langsung ke ruang diskusi.
  * **Preservasi Skala Spasial & Standar Anti-AI Slop**:
    - **Dropdown Navbar Ramping**: Menambahkan 1 tautan bersih `Aktivitas Komunitas` dengan ikon buku/pembelajaran minimalis tanpa membengkakkan tinggi dropdown.
    - **4 Kartu Ringkasan Metrik (Rule #64)**: Terdiri dari 2 baris murni dengan font seragam `text-xs font-bold uppercase` dan `text-xs font-black` (*E-Learning Aktif, E-Learning Tuntas, Lomba Diikuti, Diskusi Forum*).
    - **Zero Dummy Data (Rule #94)**: Menampilkan empty state bersih dengan ajakan aksi (*Call-to-Action*) to the point, bebas dari data buatan atau terminologi alay/kaku (**Rule #70**).
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #61**: Standarisasi Hierarki Warna Brand LensMatch (Aksen Amber).
  * **Rule #62**: Standar Breadcrumb Navigation.
  * **Rule #64**: Standarisasi KPI Metric Cards (Ukuran Seragam `text-xs` & Eliminasi Teks Ketiga).
  * **Rule #70**: Anti-AI Slop Terminology & Natural Human Text Standard (Bebas simbol `&` redundan).
  * **Rule #72**: Modern Single Chevron Standard (`M8.25 4.5l...`).
  * **Rule #94**: Zero Dummy Fallback & Dynamic Data Integrity.
  * **Rule #97**: Standarisasi Pratinjau Modal (Lightbox) Interaktif pada Field Media.
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear` & `php artisan view:cache`: **PASS** (Zero error).
  * Eksekusi controller dan rendering HTML penuh: **PASS** (78.178 bytes ter-render sempurna).


* **Modul / Fitur**:
  - Dropdown Profil Pengguna pada Navbar (`resources/views/partials/public/navbar.blade.php`).
* **File yang Diubah**:
  * `resources/views/partials/public/navbar.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Eliminasi Kebocoran Padding Putih pada Efek Hover Logout**:
    - **Before**: Terdapat celah strip putih di bawah tombol logout saat di-hover karena adanya `py-2` pada kontainer kartu dropdown dan pembungkus `<div class="pt-1">` di atas tombol. Latar merah muda hover (`hover:bg-red-50`) hanya mewarnai elemen button dan tidak mencapai tepi dasar kartu.
    - **After**: Menghapus `py-2` pada kontainer dropdown dan menghapus pembungkus `<div class="pt-1">`. Jarak ruang bernapas teks tetap dipertahankan 100% lapang dengan memindahkan ruang luar ke dalam padding tombol (`pt-4 pb-5`).
    - **Hasil Akhir**: Latar hover merah muda kini mengalir mulus mengisi 100% sudut lengkung bawah kartu (`rounded-b-2xl`) secara solid tanpa kebocoran warna putih, dan jarak baca teks tetap nyaman persis seperti sebelumnya.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #61**: Visual Consistency & Interactive Color Harmony.
  * **Rule #70**: Micro-Interaction Integrity & Natural Layout Balance.
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear` & `php artisan view:cache`: **PASS** (Zero error).


* **Modul / Fitur**:
  - Halaman Pengaturan Profil Pengguna / Klien (`resources/views/public/profile/index.blade.php`).
* **File yang Diubah**:
  * `resources/views/public/profile/index.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Perbaikan Skala Spasial & Batasan Lebar Kartu Avatar**:
    - **Before**: Kartu pembungkus dan area dropzone garis putus-putus (*dashed box*) avatar membentang selebar 100% kontainer (~1000px). Hal ini menyebabkan anomali visual di mana foto mikro berukuran 100x100px memiliki area seret seluas landasan pacu.
    - **After**: Menerapkan tata letak proporsional vertikal (Pilihan B) menggunakan `grid grid-cols-1 md:grid-cols-2 gap-6 items-start`. Kartu unggah avatar kini menempati tepat 1 kolom di sisi kiri (sejajar lurus di bawah field input `Nama Lengkap` dan `Nomor Telepon / WhatsApp`), membatasi lebar dropzone secara natural di kisaran ~380–450px.
  * **Preservasi Standar Fungsionalitas & Kepatuhan Rule**:
    - Seluruh standar fungsionalitas dipertahankan 100%: pratinjau avatar persegi/squircle (`w-24 h-24 sm:w-28 sm:h-28`), modal *lightbox* resolusi penuh saat foto diklik (**Rule #97**), ikon outline kamera kosong (**Rule #94**), teks instruksi seret berkas, validasi ukuran maks 10 MB, serta feedback hover/drag border amber (**Rule #61**).
    - Menghilangkan anomali "Amnesia Skala Spasial", menyelaraskan tampilan profil publik dengan kerapian dasbor mitra fotografer tanpa merusak struktur visual.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #61**: Standarisasi Hierarki Warna Brand LensMatch.
  * **Rule #70**: Anti-AI Slop Terminology & Natural Human Text Standard.
  * **Rule #94**: Zero Dummy Fallback & Dynamic Data Integrity.
  * **Rule #96**: Standarisasi Proporsi Card Media & Larangan Pemanipulasian Tinggi Seragam.
  * **Rule #97**: Standarisasi Pratinjau Modal (Lightbox) Interaktif pada Field Media.
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear` & `php artisan view:cache`: **PASS** (Zero error).


* **Modul / Fitur**:
  - Halaman Beranda Publik (`resources/views/public/home.blade.php`).
* **File yang Diubah**:
  * `resources/views/public/home.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Tombol Mengambang (Floating Action Button) Kembali ke Atas**:
    - **Before**: Tidak ada tombol pintasan untuk kembali ke posisi teratas halaman saat pengguna menjelajahi konten beranda yang panjang ke bawah, sehingga pengguna harus melakukan scrolling manual.
    - **After**: Menambahkan tombol melayang (*floating button*) di sudut kanan bawah (`fixed bottom-6 right-6 sm:bottom-8 sm:right-8 z-40`) khusus pada halaman beranda (`home.blade.php`).
    - **Mekanisme & Animasi Ringan Native (Zero Bloat Libraries)**:
      - Menggunakan state reaktif Alpine.js: `@scroll.window="showTopBtn = (window.pageYOffset || document.documentElement.scrollTop) > 400"`.
      - Tombol otomatis muncul dengan transisi fade & slide lembut saat posisi scroll melebihi 400px.
      - Aksi klik mengeksekusi `window.scrollTo({ top: 0, behavior: 'smooth' })` bawaan browser tanpa library eksternal yang membebani memori atau bundle aplikasi.
    - **Desain & Kepatuhan Visual**:
      - Ikon single upward chevron minimalis (`d="M4.5 15.75l7.5-7.5 7.5 7.5"`) sesuai **Rule #72**.
      - Bentuk lingkaran bulat sempurna (`rounded-full`) berukuran proporsional `w-11 h-11 sm:w-12 sm:h-12` dengan warna amber khas LensMatch (`bg-amber-400 hover:bg-amber-500 text-gray-950 font-black shadow-lg hover:shadow-xl hover:-translate-y-1 active:translate-y-0`) sesuai **Rule #61**.
      - Dilengkapi atribut aksesibilitas `aria-label="Kembali ke atas"` dan `title="Kembali ke atas"`.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #61**: Standarisasi Hierarki Warna Brand LensMatch (Aksen Amber).
  * **Rule #70**: Anti-AI Slop Terminology & Natural Human Text Standard.
  * **Rule #72**: Modern Single Chevron Standard (Ikon navigasi bersih tanpa panah batang berekor).
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear` & `php artisan view:cache`: **PASS** (Zero error).


* **Modul / Fitur**:
  - Navbar Publik & Klien (`resources/views/partials/public/navbar.blade.php`).
* **File yang Diubah**:
  * `resources/views/partials/public/navbar.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Sinkronisasi Hover & Status Buka Chevron Dropdown Profil**:
    - **Before**: Saat tombol profil pengguna di-hover atau dalam kondisi terbuka (*open*), teks nama pengguna berubah menjadi kuning amber (`text-amber-400`), tetapi ikon chevron panah di kanannya tetap berwarna abu-abu gelap (`text-gray-500`) karena kelas warnanya di-hardcode pada elemen SVG tanpa `group-hover`.
    - **After**: Menambahkan utilitas `group` pada tombol pemicu dropdown dan `group-hover:text-amber-500 dark:group-hover:text-amber-400` pada ikon SVG chevron, serta `!text-amber-500 dark:!text-amber-400` saat status `open` aktif. Teks nama dan panah chevron kini berubah warna secara serempak dan selaras 100%.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #61**: Standarisasi Hierarki Warna Brand LensMatch (Aksen Amber).
  * **Rule #70**: Visual Harmony & Micro-Interaction Integrity.
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear` & `php artisan view:cache`: **PASS** (Zero error).

---

### [2026-11-17 15:45 WITA] — Implementasi Standar Card Grid Bertoolbar pada Galeri Klien (4 KPI Metric Cards, Dropdown Filter Status, dan Real-Time Search Bar)

* **Modul / Fitur**:
  - Halaman Galeri Hasil Foto Klien (`resources/views/client/galleries/index.blade.php`).
  - Controller Reservasi Klien (`app/Http/Controllers/Client/ClientBookingController.php`).
* **File yang Diubah**:
  * `resources/views/client/galleries/index.blade.php`
  * `app/Http/Controllers/Client/ClientBookingController.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Penerapan 4 KPI Metric Cards Riil Database (Rule #64)**:
    - **Before**: Halaman galeri foto langsung menampilkan card grid tanpa ringkasan eksekutif, tidak seimbang dengan halaman Reservasi dan Tagihan.
    - **After**: Menambahkan 4 kartu metrik statistik ringkas di atas toolbar (`Total Galeri`, `Menunggu Konfirmasi`, `Selesai Disetujui`, `Belum Diulas`) yang diambil 100% dari data riil database klien. Tipografi seragam `text-xs font-bold uppercase text-gray-500` dan angka `text-xs font-black text-gray-900`, bebas dari angka raksasa maupun teks baris ketiga redundan (*anti-AI slop*).
  * **Wadah Toolbar Standar Sistem (Rule #107 & Rule #70)**:
    - **Before**: Tidak ada wadah toolbar, pencarian, maupun penyaringan status.
    - **After**: Menambahkan toolbar card bersih (`rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-2xs p-4 flex flex-col md:flex-row items-center justify-between gap-4`) tanpa divider ganda.
      - **Kiri**: Dropdown mengambang Alpine.js untuk Filter Status (`Semua Status Galeri`, `Menunggu Konfirmasi Klien`, `Selesai Disetujui`, `Belum Diulas`, `Sudah Diulas`) dengan indikator chevron aktif.
      - **Kanan**: Input pencarian dengan ikon kaca pembesar dan placeholder to the point: *"Cari fotografer, layanan, lokasi..."*.
  * **Pencarian Real-Time Client-Side & Dukungan Server-Side**:
    - Komponen Alpine.js `handleSearch(e)` menyaring kartu `.gallery-card` secara instan tanpa reload halaman yang berat.
    - Dilengkapi *empty state* real-time (`#search-empty-state`) jika pencarian tidak menemukan kartu yang cocok.
    - Controller `ClientBookingController@galleries` juga mendukung parameter server-side `status` dan `q` untuk konsistensi pagination.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #64**: Standarisasi KPI Metric Cards 2-elemen teks seragam `text-xs`.
  * **Rule #107**: Standar Toolbar Card tanpa garis divider ganda (`0 border-b`).
  * **Rule #90**: Standarisasi Sinkronisasi Status Booking dan Card Layout (`items-start`, `h-auto`).
  * **Rule #70 & Rule #51**: Copywriting to the point, eliminasi simbol `&` redundan, bebas jargon AI slop.
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear` & `php artisan view:cache`: **PASS** (Zero error).
  * Standalone test script: **PASS** (Filter `Alex` return 3, filter `pending` return 0, filter `completed` return 9).

---

### [2026-11-17 15:30 WITA] — Implementasi Navigasi Dinamis Adaptif 2-Lapis pada Halaman Invoice, Checkout Pembayaran, Profil Fotografer, dan Event Lomba (Eliminasi Hardcoded Back Navigation)

* **Modul / Fitur**:
  - Halaman Dokumen Invoice Publik (`resources/views/public/booking/invoice.blade.php`).
  - Halaman Checkout Pembayaran Sesi Foto (`resources/views/public/booking/checkout.blade.php`).
  - Cetak Pratinjau Dokumen Invoice (`resources/views/public/booking/invoice-print.blade.php`).
  - Tabel Riwayat Pembayaran Klien (`resources/views/client/invoices/index.blade.php`).
  - Tabel Reservasi Saya Klien (`resources/views/client/bookings/index.blade.php`).
  - Controller Chat Ruang Obrolan (`app/Http/Controllers/Public/ChatController.php`).
  - Halaman Profil Fotografer Publik (`resources/views/public/photographers/show.blade.php`).
  - Halaman Detail Event Lomba Foto (`resources/views/public/contests/show.blade.php`).
  - Halaman Kelola Peserta & Karya Lomba Admin (`resources/views/admin/contests/participants.blade.php`, `submissions.blade.php`).
* **File yang Diubah**:
  * `resources/views/public/booking/invoice.blade.php`
  * `resources/views/public/booking/checkout.blade.php`
  * `resources/views/public/booking/invoice-print.blade.php`
  * `resources/views/client/invoices/index.blade.php`
  * `resources/views/client/bookings/index.blade.php`
  * `app/Http/Controllers/Public/ChatController.php`
  * `resources/views/public/photographers/show.blade.php`
  * `resources/views/public/contests/show.blade.php`
  * `resources/views/admin/contests/participants.blade.php`
  * `resources/views/admin/contests/submissions.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Navigasi Dinamis 2-Lapis pada Dokumen Invoice (`invoice.blade.php`)**:
    - **Before**: Tombol kembali di-hardcode ke `route('client.bookings')` (*Kembali ke Reservasi Saya*), sehingga saat klien membuka invoice dari *Riwayat Pembayaran Saya* (`/client/invoices`), klien malah terlempar ke *Reservasi Saya*.
    - **After**: Menerapkan 2-Layer Adaptive Navigation: Membaca parameter eksplisit `?from=` (`invoices`, `bookings`, `checkout`, `chat`, `admin`), dengan smart fallback deteksi `url()->previous()`. Label tombol kembali otomatis adaptif dan kontekstual (`Kembali ke Riwayat Pembayaran`, `Kembali ke Reservasi Saya`, `Kembali ke Selesaikan Pembayaran`, `Kembali ke Ruang Chat`, `Kembali ke Kelola Transaksi`).
  * **Navigasi Dinamis pada Checkout Pembayaran (`checkout.blade.php`)**:
    - **Before**: Tombol kembali di-hardcode ke `client.bookings`.
    - **After**: Menggunakan resolusi adaptif yang sama: Jika datang dari Riwayat Pembayaran, tombol kembali mengarah ke `client.invoices` (*Kembali ke Riwayat Pembayaran*). Link *"Lihat Pratinjau Invoice"* menyertakan parameter `from=checkout` agar alur bolak-balik invoice-checkout presisi.
  * **Penyelarasan Tautan Pemanggil Invoice**:
    - Menambahkan `from=invoices` pada tabel pembayaran (`client/invoices/index.blade.php`).
    - Menambahkan `from=bookings` pada tombol Bayar dan dropdown invoice (`client/bookings/index.blade.php`).
    - Menambahkan `from=chat` pada tautan invoice penawaran khusus chat (`ChatController.php`).
  * **Audit & Perbaikan Seluruh Sistem pada Halaman Multi-Entry**:
    - **Profil Fotografer (`photographers/show.blade.php`)**: Menerapkan deteksi asal dinamis: jika dibuka dari Beranda -> *Kembali ke Beranda*, dari Chat -> *Kembali ke Ruang Chat*, dari Reservasi -> *Kembali ke Reservasi Saya*, dari Katalog -> *Kembali ke Katalog*.
    - **Detail Lomba Foto (`contests/show.blade.php`)**: Menerapkan deteksi asal dinamis: jika dibuka dari Riwayat Pembayaran Klien (`/client/invoices`) -> *Kembali ke Riwayat Pembayaran*, jika dari Pengajuan Saya -> *Kembali ke Riwayat Pengajuan Saya*, fallback ke Katalog Lomba.
    - **Kelola Peserta & Karya Lomba Admin (`participants.blade.php` & `submissions.blade.php`)**: Jika admin mengakses dari halaman *Kelola Event Lomba* (`admin.contests.manage`), tombol kembali mengarah ke kelola lomba terkait (*Kembali ke Kelola Lomba*), bukan terlempar ke daftar lomba umum.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #72**: Modern Back Navigation Standard, konsistensi single chevron dan contextual return.
  * **Rule #51 & #89**: Human-Centric Communication & Navigation, anti-broken context.
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear` & `php artisan view:cache`: **PASS** (Zero error).

---

### [2026-11-17 11:40 WITA] — Audit Lanjutan & Pembersihan Sisa AI-Slop (Normalisasi Total Form Booking, Bahasa Forum, dan Eliminasi Settlement/Escrow)

* **Modul / Fitur**:
  - Kolom Ringkasan Biaya Form Booking (`resources/views/public/booking/create.blade.php`).
  - Verifikasi Pembayaran Admin (`resources/views/admin/transactions/index.blade.php`).
  - Laporan Ledger Fotografer (`resources/views/photographer/reports/export.blade.php`).
  - Diskusi Forum Komunitas (`resources/views/public/forum/show.blade.php`).
* **File yang Diubah**:
  * `resources/views/public/booking/create.blade.php`
  * `resources/views/admin/transactions/index.blade.php`
  * `resources/views/photographer/reports/export.blade.php`
  * `resources/views/public/forum/show.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Normalisasi Tipografi Ringkasan Form Booking (`create.blade.php`)**:
    - **Before**: Angka Total Biaya Sesi di kolom kanan menggunakan font raksasa kuning (`text-lg font-black text-amber-600`), bergaya template SaaS promosi.
    - **After**: Dinormalkan menjadi tipografi formal hitam netral proporsional `text-xs font-black text-gray-900 dark:text-white` dengan garis pembatas halus di atasnya (Rule #106 & Rule #89).
  * **Eliminasi Sisa Jargon Escrow pada Dialog Verifikasi Admin (`transactions/index.blade.php`)**:
    - **Before**: Pesan konfirmasi bertuliskan *"Apakah Anda yakin pembayaran transfer untuk pesanan ini sudah valid dan dana telah masuk ke rekening bersama escrow?"*.
    - **After**: Diubah menjadi bahasa resmi platform: *"Apakah Anda yakin pembayaran transfer untuk pesanan ini sudah valid dan dana telah masuk ke rekening resmi LensMatch?"*.
  * **Penyelarasan Bahasa Indonesia pada Forum Diskusi (`forum/show.blade.php`)**:
    - **Before**: Teks toggle balasan bertuliskan bahasa Inggris berikon panah ganjil: *"▾ See Replies / ▴ Hide Replies"*.
    - **After**: Diubah menjadi bahasa Indonesia yang tenang dan baku: *"▾ Lihat Balasan / ▴ Sembunyikan Balasan"*.
  * **Pembersihan Simbol `&` dan Jargon Settlement pada Laporan (`export.blade.php`)**:
    - **Before**: Header ledger bertuliskan *"Rincian Transaksi Pemotretan & Pencairan Settlement"*.
    - **After**: Diubah menjadi *"Rincian Transaksi Pemotretan dan Pencairan Dana"*.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #89 Point 2**: Eliminasi kata Escrow / Rekber.
  * **Rule #70**: Anti-AI Slop Terminology, konsistensi bahasa Indonesia dan anti-simbol `&` redundan.
  * **Rule #106**: Keseragaman ukuran teks dan nominal proporsional.
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear` & `php artisan view:cache`: **PASS** (Zero error).

---

### [2026-11-17 11:35 WITA] — Pembersihan Menyeluruh Glorifikasi Kode Database, Penerapan Modal Manusiawi (Opsi A ala Airbnb/SweetEscape), dan Eliminasi Jargon Teknis (Rule #89, Rule #70, Rule #106)

* **Modul / Fitur**:
  - Modal Pembatalan & Jadwal Ulang Reservasi Klien (`resources/views/client/bookings/index.blade.php`).
  - Modal Detail Transaksi & Baris Invoice Admin (`resources/views/admin/transactions/index.blade.php`).
  - Modal Pengajuan Payout Fotografer (`resources/views/photographer/reports/index.blade.php`).
  - Halaman Pembayaran Fallback (`resources/views/public/payment/index.blade.php`).
  - Modal Penawaran Khusus Chat (`resources/views/public/chat/index.blade.php`).
  - Halaman Invoice & Menu Navbar Klien (`client/invoices/index.blade.php`, `navbar.blade.php`, `services/index.blade.php`, `activity_logs/index.blade.php`).
* **File yang Diubah**:
  * `resources/views/client/bookings/index.blade.php`
  * `resources/views/admin/transactions/index.blade.php`
  * `resources/views/photographer/reports/index.blade.php`
  * `resources/views/public/payment/index.blade.php`
  * `resources/views/public/chat/index.blade.php`
  * `resources/views/client/invoices/index.blade.php`
  * `resources/views/partials/public/navbar.blade.php`
  * `resources/views/photographer/services/index.blade.php`
  * `resources/views/admin/activity_logs/index.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Penerapan Modal Pembatalan & Reschedule Manusiawi Opsi A (Rule #89 Point 1)**:
    - **Before**: Modal pembatalan dan reschedule klien menodongkan kode transaksi database (*"Apakah Anda yakin ingin membatalkan pesanan KTRK-00018?"* dan *"Pilih tanggal dan jam mulai pengganti untuk pesanan KTRK-00018"*), membingungkan klien karena tidak menyebut identitas sesi yang dipesan.
    - **After**: Mengubah teks modal pembatalan menjadi **Opsi A ala Airbnb/SweetEscape**: *"Apakah Anda yakin ingin membatalkan sesi [Nama Layanan] bersama [Nama Fotografer]? Jika sudah melakukan pembayaran, dana akan ditinjau Admin untuk proses refund."*, serta modal reschedule menjadi *"Pilih tanggal dan jam mulai pengganti untuk sesi [Nama Layanan] bersama [Nama Fotografer]."* tanpa menyebutkan kode acak database.
  * **Pemberantasan Glorifikasi Kode & Aksen Warna Norak pada Admin Transaksi (Rule #89 Point 1)**:
    - **Before**: Judul modal `<h3>` detail transaksi admin diisi kode kontrak raksasa `KTRK-00018`, dan nomor invoice diwarnai aksen kuning amber mencolok (`text-amber-600 font-bold`).
    - **After**: Judul modal dikembalikan ke entitas formal **"Detail Transaksi"** dengan kode kontrak sebagai metadata pendukung monokrom di bawahnya. Nomor invoice diubah menjadi monokrom netral `font-mono text-gray-700 dark:text-gray-300`. Header kolom `Kode Reg & Tanggal` diubah menjadi `Registrasi Peserta`.
  * **Restrukturisasi Modal Payout Fotografer (Rule #89)**:
    - **Before**: Modal tarik saldo payout menempatkan baris pertama `Nomor Kontrak: KTRK-00018` tebal di atas nominal pencairan.
    - **After**: Mengedepankan nama sesi pemotretan, nama klien, dan nominal bersih (90%) di urutan teratas, sementara kode transaksi diletakkan sebagai referensi sekunder tenang di baris terbawah.
  * **Pembersihan Halaman Pembayaran Fallback (`payment/index.blade.php`)**:
    - **Before**: Judul halaman *"Selesaikan Pembayaran Kontrak"*, kode kontrak dipajang di header atas, nominal harga raksasa kuning amber (`text-2xl text-amber-500`), copywriting jargon *"Pembayaran via Midtrans Snap / Rekening Bersama Escrow"*, dan emoji `💳` di empty state.
    - **After**: Judul diubah menjadi *"Pembayaran Reservasi Sesi Foto"*, kode diletakkan sebagai referensi sekunder, nominal harga dinormalkan ke tipografi formal proporsional `text-xs font-black text-gray-900`, jargon dihapus menjadi *"Pembayaran resmi diproses secara aman melalui sistem LensMatch"*, dan emoji digantikan ikon SVG bersih.
  * **Eliminasi Jargon Teknis "Escrow" pada Antarmuka Publik (Rule #89 Point 2)**:
    - **Before**: Radio button pilihan pembayaran di modal penawaran chat bertuliskan *"Langsung lunas via Escrow"*.
    - **After**: Diubah menjadi bahasa manusiawi yang lugas: *"Pembayaran lunas langsung"*.
  * **Pembersihan Redundansi Simbol `&` (Rule #70)**:
    - Mengubah judul halaman invoice klien dari *"Invoice & Pembayaran Saya"* menjadi *"Riwayat Pembayaran Saya"*.
    - Mengubah menu dropdown navbar dari *"Invoice & Pembayaran"* menjadi *"Riwayat Pembayaran"*, dan *"Pesan & Chat Fotografer"* menjadi *"Ruang Chat"*.
    - Mengubah title kelola paket fotografer dari *"Kelola Paket Layanan & Tarif"* menjadi *"Kelola Paket Layanan"*.
    - Mengubah kartu metrik admin dari *"Transaksi & Rekber"* menjadi *"Transaksi Pembayaran"*, dan *"Booking & Sesi Foto"* menjadi *"Sesi Reservasi"*.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #89 Point 1**: Anti-Code Glorification di seluruh dialog dan antarmuka.
  * **Rule #89 Point 2**: Eliminasi total jargon teknis "Escrow / Rekber" dari UI publik.
  * **Rule #70**: Anti-AI Slop Terminology dan eliminasi simbol `&` redundan.
  * **Rule #106**: Keseragaman tipografi nominal proporsional.
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear` & `php artisan view:cache`: **PASS** (Zero error).
  * Seluruh modal dan halaman tervalidasi bersih.

---

### [2026-11-17 11:25 WITA] — Refaktor Total Tipografi, Spacing, dan Eliminasi Boxed Card pada Lembar Faktur (Kepatuhan Penuh Rule #106, Rule #89, Rule #7, Rule #70)

* **Modul / Fitur**:
  - Halaman Faktur Pembayaran / Invoice (`resources/views/public/booking/invoice.blade.php`).
* **File yang Diubah**:
  * `resources/views/public/booking/invoice.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Pemisahan Terstruktur Judul Dokumen & Status Faktur (Rule #89)**:
    - **Before**: `<h1>Invoice</h1>` dan `<span>Menunggu Konfirmasi</span>` berada di dalam flexbox horizontal berdampingan tanpa pemisah yang jelas, sehingga terbaca janggal seperti satu kalimat: *"Invoice Menunggu Konfirmasi"*.
    - **After**: Memisahkan `Invoice` sebagai tajuk formal independen (`text-2xl font-black text-gray-900 dark:text-white tracking-tight`). Seluruh metadata diletakkan secara terstruktur di bawahnya dalam 3 baris tenang: Nomor Invoice (`font-mono text-gray-600`), Tanggal Faktur, dan baris `Status: Menunggu Konfirmasi` (atau Lunas / Menunggu Pembayaran) dengan label status yang jelas dan rapi.
  * **Keseragaman Ukuran Font `text-xs` pada Tabel dan Angka Nominal (Kepatuhan Absolut Rule #106)**:
    - **Before**: Angka `Total Tagihan` melonjak menjadi `text-lg sm:text-xl font-black`, meniru pola template e-commerce SaaS generik yang norak dan melanggar Rule #106. Identitas klien/fotografer juga melonjak ke `text-base`.
    - **After**: Menyeragamkan seluruh teks dokumen faktur (tabel rincian layanan, identitas penagihan, subtotal, dan total tagihan) ke ukuran patokan **`text-xs`**. Hierarki visual untuk `Total Tagihan` dan nominal harga Rp 3.000.000 murni dibangun menggunakan ketebalan huruf `font-black` dan kontras teks monokrom pekat (`text-gray-900 dark:text-white`).
  * **Harmonisasi Spacing, Padding, dan Eliminasi Boks Kartu/Teks Redundan (Rule #7 & Rule #89)**:
    - **Before**: Menggunakan `space-y-8` dan `py-4` yang menciptakan rongga hampa kosong di tengah dokumen. Di bagian bawah faktur terdapat boks kartu abu-abu kaku (`p-5 rounded-2xl bg-gray-50 border border-gray-200`) dengan teks narasi panjang *"Menunggu Konfirmasi Fotografer. Fotografer sedang memeriksa detail jadwal..."* yang menduplikasi status yang sudah ada di header faktur.
    - **After**: Memperbaiki ritme vertikal menjadi `p-6 sm:p-8 lg:p-10 space-y-6 sm:space-y-7` dengan baris tabel `py-3.5`. Menghapus total teks narasi dan card *"Menunggu Konfirmasi Fotografer"* di bawah tabel kalkulasi tagihan. Ketika status masih draft / menunggu konfirmasi mitra, lembar faktur berakhir bersih dan profesional tepat di bawah rincian Total Tagihan tanpa card atau teks tambahan apapun. Instruksi pembayaran hanya muncul saat status kontrak sudah disetujui / siap bayar (`pending_payment` / `approved`).
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #106**: Keseragaman ukuran font `text-xs` di seluruh sel dan nominal tabel/dokumen faktur.
  * **Rule #89**: Standarisasi faktur bersih standar industri, anti-kotak kaku terpisah, dan tata letak editorial datar.
  * **Rule #7**: Proporsionalitas padding dan margin (*comfortable whitespace rhythm*).
  * **Rule #70**: Anti-AI slop copywriting dan terminologi manusiawi.
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear` dan `php artisan view:cache`: **PASS** (Zero error).
  * Tampilan Blade template tervalidasi bersih dan proporsional.

---

### [2026-11-17 11:15 WITA] — Implementasi Alur Reservasi Opsi C (Standar Emas Airbnb / SweetEscape) dan Pembersihan AI-Slop pada Lembar Faktur (Rule #14, Rule #70, Rule #89, Rule #90)

* **Modul / Fitur**:
  - Alur Pengajuan Reservasi (`app/Http/Controllers/Public/BookingController.php`).
  - Halaman Faktur Pembayaran (`resources/views/public/booking/invoice.blade.php`).
  - Halaman Reservasi Saya Klien (`resources/views/client/bookings/index.blade.php`).
* **File yang Diubah**:
  * `app/Http/Controllers/Public/BookingController.php`
  * `resources/views/public/booking/invoice.blade.php`
  * `resources/views/client/bookings/index.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Penerapan Alur Opsi C Pasca Ajukan Reservasi (Standar Airbnb & SweetEscape)**:
    - **Before**: Setelah klien menekan tombol "Ajukan Reservasi", sistem me-redirect klien ke lembar invoice (`/pembayaran/{id}`) dengan status "Menunggu Persetujuan" di mana tombol bayarnya dinonaktifkan. Hal ini membingungkan klien karena dibawa ke lembar pembayaran padahal belum bisa membayar.
    - **After**: Mengubah redirect `BookingController@store` langsung ke halaman **Reservasi Saya (`/client/bookings`)** dengan notifikasi sukses *"Permintaan reservasi berhasil diajukan. Menunggu konfirmasi fotografer."*. Pesanan baru langsung tampil di baris teratas dengan status baku `"Menunggu Konfirmasi"` dan tombol aksi `"Chat"`. Lembar invoice aktif baru dibuka melalui tombol `"Bayar"` setelah fotografer menyetujui pesanan.
  * **Pembersihan AI-Slop pada Lembar Faktur (`invoice.blade.php`)**:
    - **Penyelarasan Status Pure Colored Text (Rule #14 & #67)**: Mengganti pill kapsul berwarna tebal (`rounded-full bg-amber-100 text-amber-800`) menjadi teks warna murni yang elegan tanpa wadah badge (`text-xs font-bold text-amber-600 dark:text-amber-400`).
    - **Eliminasi Boks Alert Kuning Tebal (Rule #89)**: Mengganti kotak peringatan kuning raksasa menjadi panel pemberitahuan netral abu-abu yang tenang (`bg-gray-50 border border-gray-200`) dengan tombol standar netral.
    - **Eliminasi Redundansi Teks Layanan (Rule #70)**: Menghapus teks duplikat *"Sesi foto bersama [Nama Fotografer]"* di bawah nama layanan karena identitas penyedia jasa sudah tercantum jelas pada header faktur.
    - **Penyederhanaan Baris Navigasi Atas (Rule #62 & #72)**: Menghilangkan tumpukan breadcrumb ganda, mempertahankan satu tautan kembali yang bersih dan elegan ke Reservasi Saya.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #14 & #67**: Status teks murni tanpa pill/badge kapsul.
  * **Rule #70**: Anti-AI Slop Terminology, anti-redundansi teks.
  * **Rule #89**: Standarisasi faktur bersih standar industri.
  * **Rule #90**: Sinkronisasi status booking antara peran Klien dan Fotografer.
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear` dan `php artisan view:cache`: **PASS** (Zero error).
  * Pengujian alur redirect form reservasi: **PASS**.

---

### [2026-11-17 11:05 WITA] — Implementasi Banner Pengingat Sesi Minimalis 1-Baris (Alternatif A ala Airbnb/Uber) dan Integrasi Notifikasi In-App (Rule #70, Rule #85, Rule #41)

* **Modul / Fitur**:
  - Banner Pengingat Sesi Foto H-1 & Hari-H (`resources/views/partials/shared/upcoming-session-banner.blade.php`).
  - Layanan Jadwal Reservasi (`app/Services/BookingScheduleService.php`).
  - Controller Reservasi Klien & Fotografer (`ClientBookingController.php`, `PhotographerBookingController.php`, `DashboardController.php`).
* **File yang Diubah**:
  * `resources/views/partials/shared/upcoming-session-banner.blade.php`
  * `app/Services/BookingScheduleService.php`
  * `app/Http/Controllers/Client/ClientBookingController.php`
  * `app/Http/Controllers/Photographer/PhotographerBookingController.php`
  * `app/Http/Controllers/Photographer/DashboardController.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Penyederhanaan UI Banner Pengingat Sesi (Alternatif A: Compact Minimalist Bar)**:
    - **Before**: Banner menggunakan latar gradient kuning-putih tipis, border amber neon, penumpukan badge berwarna-warni (*PENGINGAT SESI H-1*, *ID Kontrak*, *Mitra Fotografer*), serta stopwatch countdown berdetik per detik (`28j : 03m : 12d`) yang terasa seperti promo flash sale / scarcity AI slop.
    - **After**: Mengubah seluruh tampilan banner menjadi bar horizontal 1-baris minimalis ala Airbnb/Uber berlatar putih bersih (`bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700/80 rounded-2xl p-4 sm:p-5 shadow-xs`). Sisi kiri memuat label waktu murni (`BESOK • 14:00 WIB` / `HARI INI • 14:00 WIB`), judul layanan & mitra fotografer mengalir alami, dan sub-info lokasi acara. Sisi kanan memuat dua tombol kompak: `Petunjuk Arah` (border abu-abu tipis) dan `Chat` (solid amber brand LensMatch).
  * **Integrasi Notifikasi In-App Pengingat Sesi ke Lonceng Header**:
    - **Before**: Pengingat sesi hanya berupa banner di halaman reservasi, belum terhubung ke sistem notifikasi lonceng header real-time.
    - **After**: Menambahkan `BookingScheduleService::dispatchUrgentSessionNotification()` yang otomatis mengirimkan notifikasi in-app ke akun pengguna (Klien & Fotografer) saat sesi mendekati H-1 atau Hari-H, dengan pengaman filter harian agar notifikasi untuk sesi yang sama hanya dikirim 1 kali per hari (bebas spam duplikat).
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #70**: Anti-AI Slop Terminology, teks natural to the point tanpa tanda `&` redundan.
  * **Rule #85**: Penyesuaian visual smart reminder sesi H-1 & Hari-H yang tenang dan bebas polusi visual.
  * **Rule #41**: Zero layout shift, tanpa garis pembatas divider horizontal berlebih.
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear` dan `php artisan view:cache`: **PASS** (Zero error).
  * Pengujian pembuatan notifikasi database: **PASS** (Notifikasi pengingat sesi berhasil terkirim dan tercatat di database).

---

### [2026-11-16 15:55 WITA] — Animasi Loading Deteksi Lokasi dan Otomatisasi Input Field Pencarian Peta (Rule #70, Rule #51, Rule #41)

* **Modul / Fitur**:
  - Formulir Reservasi Publik — Bagian Lokasi Acara & Geolocation (`resources/views/public/booking/create.blade.php`).
* **File yang Diubah**:
  * `resources/views/public/booking/create.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Animasi Loading Saat Klik "Gunakan Lokasi Saat Ini"**:
    - **Before**: Saat tombol "Gunakan Lokasi Saat Ini" diklik, ada jeda sekitar 1-2 detik (proses pembacaan GPS perangkat, reverse geocoding via Photon/Nominatim, dan sinkronisasi dropdown kota) tanpa feedback visual apapun pada input field, sehingga pengguna mengira sistem macet atau tidak merespons.
    - **After**: Menambahkan state `isDetectingLocation` di Alpine.js. Selama proses deteksi berlangsung:
      1. Tombol "Gunakan Lokasi Saat Ini" menampilkan icon spinner berputar dan teks berubah menjadi `"Mencari Lokasi..."`.
      2. Dropdown Provinsi dan Kota/Kabupaten masuk ke status pulse dengan icon spinner berputar dan teks `"Mencari provinsi..."` serta `"Mencari kota..."`.
      3. Input field pencarian peta (`mapQuery`) menampilkan animasi pulse, icon spinner berputar di sisi kanan, serta placeholder dinamis `"Mendeteksi titik lokasi perangkat..."`.
      4. Tombol "Cari" beralih label menjadi `"Mencari..."` dan berstatus disabled.
      5. Textarea Alamat Lengkap menampilkan placeholder dinamis `"Mengambil alamat lengkap dari titik lokasi..."` dengan efek pulse lembut.
  * **Otomatisasi Input Field Pencarian Peta (`mapQuery`)**:
    - **Before**: Setelah titik lokasi berhasil dideteksi dari GPS, pin tertancap di peta dan alamat lengkap terisi di textarea bawah, tetapi kolom input pencarian di bawah "Pin Titik Lokasi di Peta (Opsional)" tetap kosong melompong.
    - **After**: Fungsi `reverseGeocodeAndMatch()` mengekstrak nama tempat/jalan (`venueTitle` atau penggalan pertama nama jalan) dan otomatis mengisi input field pencarian peta (`this.mapQuery = detectedQuery;`). Saat tombol "Hapus Pin" diklik, kolom pencarian juga otomatis direset bersih (`this.mapQuery = '';`).
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #70**: Anti-AI Slop Terminology, kata-kata to the point, ringkas, natural, tanpa simbol `&` redundan.
  * **Rule #51**: Penanganan formulir dan interaksi peta yang intuitif, informatif, dan andal.
  * **Rule #41**: Tidak ada pergeseran tata letak visual (*zero layout shift*) selama proses loading.
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear` dan `php artisan view:cache`: **PASS** (Zero error).
  * Pengujian state dan reaktivitas Alpine.js: **PASS**.

---

### [2026-11-16 15:40 WITA] — Implementasi Formulir Pemesanan Bertahap Sekuensial (Sequential Step-by-Step Form Progression) (Rule #70, Rule #51)

* **Modul / Fitur**:
  - Formulir Reservasi Publik — Alur Validasi Antar-Langkah (`resources/views/public/booking/create.blade.php`).
* **File yang Diubah**:
  * `resources/views/public/booking/create.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Penerapan Formulir Terkunci Bertahap (Sequential Locking)**:
    - **Before**: Semua section formulir (Paket, Tanggal, Lokasi, Jam Sesi, Data Pemesan) terbuka sekaligus, memungkinkan pengguna melompati langkah atau mengisi bagian bawah terlebih dahulu meskipun paket atau tanggal belum dipilih.
    - **After**: Mengimplementasikan getter `step1Done`, `step2Unlocked`, `step2Done`, `step3Unlocked`, `step3Done`, `step4Unlocked`, `step4Done`, dan `step5Unlocked` pada state Alpine.js:
      - **Langkah 1 (Pilih Paket)**: Wajib dipilih pertama kali.
      - **Langkah 2 (Pilih Tanggal)**: Terkunci (`opacity-50 pointer-events-none`) sampai paket layanan dipilih di Langkah 1.
      - **Langkah 3 (Lokasi Acara)**: Terkunci sampai tanggal sesi dipilih di Langkah 2.
      - **Langkah 4 (Pilih Jam Sesi)**: Terkunci dengan placeholder edukatif yang jelas sampai provinsi, kota, dan alamat acara terisi lengkap di Langkah 3.
      - **Langkah 5 (Data Pemesan & Catatan)**: Terbuka setelah jam sesi valid.
      - **Tombol Konfirmasi Booking**: Hanya aktif saat seluruh tahapan telah lengkap dan terverifikasi.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #70**: Anti-AI Slop, pesan panduan langkah singkat, jelas, tanpa kalimat bertele-tele.
  * **Rule #51**: Validasi formulir bertahap yang mencegah kesalahan input dan data inkonsisten.
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear` dan `php artisan view:cache`: **PASS** (Zero error).

---

### [2026-11-16 15:20 WITA] — Perapian Label Kalender: Eliminasi Tumpang Tindih Teks "Hari Ini" dan "Tutup" (Rule #70, Rule #41)

* **Modul / Fitur**:
  - Formulir Reservasi Publik — Kalender Mini (`resources/views/public/booking/create.blade.php`).
* **File yang Diubah**:
  * `resources/views/public/booking/create.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Penyelarasan Label Status Sel Kalender**:
    - **Before**: Jika tanggal hari ini sudah kehabisan slot atau berstatus tutup, teks oranye `"Hari Ini"` dan teks merah `"Tutup"` muncul bersamaan di satu sel tanggal, mengakibatkan tabrakan warna dan tampilan yang padat/membingungkan.
    - **After**: Menambahkan kondisi `!day.isFullDayBlocked` pada teks `"Hari Ini"`. Jika tanggal hari ini berstatus tutup/penuh, label oranye `"Hari Ini"` otomatis disembunyikan dan sel hanya menampilkan satu label status tunggal `"Tutup"` dengan warna merah/rose yang konsisten dan rapi.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #70**: Anti-AI Slop, tampilan to-the-point dan bersih.
  * **Rule #41**: UI rapi tanpa glitch visual atau tabrakan teks status.
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear` dan `php artisan view:cache`: **PASS** (Zero error).

---

### [2026-11-16 15:15 WITA] — Penerapan Konsep C: Fasilitas Layanan Inklusif Murni Bebas Angka Biaya Tambahan (Rule #70, Rule #51)

* **Modul / Fitur**:
  - Halaman Detail Profil Fotografer (`resources/views/public/photographers/show.blade.php`).
  - Database Layanan Fotografer (`service_details`).
* **File yang Diubah**:
  * `resources/views/public/photographers/show.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Penyelarasan Tampilan Fasilitas Layanan Inklusif**:
    - **Before**: Pada paket Prewedding, item *"Termasuk Cetak Canvas 40x60cm"* memunculkan teks oranye `+Rp 500.000` di samping centang hijau karena kolom `tarif_harga = 500000`, menimbulkan kebingungan apakah ada tagihan ekstra di luar tarif per jam.
    - **After**: Menghapus blok pengecekan `@if($det->tarif_harga > 0)` pada view Blade dan mengupdate nilai `tarif_harga` record terkait di database menjadi `0`. Semua fasilitas yang dicentang kini murni berstatus benefit inklusif bawaan paket, sehingga selaras dengan judul *"FASILITAS LAYANAN TERMASUK"* dan menjaga transparansi tarif per jam tanpa biaya tersembunyi.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #70**: Anti-AI Slop, copywriting to-the-point, bersih, dan natural.
  * **Rule #51**: Tampilan rincian layanan rapi, transparan, dan tidak membingungkan pengguna.
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear` dan `php artisan view:cache`: **PASS** (Zero error).
  * Verifikasi database `service_details`: **PASS** (`tarif_harga` seragam bernilai 0).

---

### [2026-11-16 15:10 WITA] — Perbaikan Logika Waktu Booking Hari-H: Sinkronisasi Timezone Indonesia (WIB), Jeda Persiapan 1 Jam, dan Filter Dropdown Jam Lewat (Rule #70, Rule #51)

* **Modul / Fitur**:
  - Konfigurasi Timezone Aplikasi (`config/app.php`, `.env`, `.env.example`).
  - Layanan Jadwal Reservasi (`app/Services/BookingScheduleService.php`).
  - Formulir Reservasi Publik (`resources/views/public/booking/create.blade.php`).
  - Controller Booking Fotografer (`app/Http/Controllers/Photographer/PhotographerBookingController.php`).
* **File yang Diubah**:
  * `config/app.php`
  * `.env.example`
  * `app/Services/BookingScheduleService.php`
  * `resources/views/public/booking/create.blade.php`
  * `app/Http/Controllers/Photographer/PhotographerBookingController.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Sinkronisasi Timezone Nasional (`Asia/Jakarta`)**:
    - **Before**: Timezone sistem masih `UTC` (GMT+0). Saat klien mengakses sistem pada pukul 14:55 WIB, backend mengira waktu masih pukul 07:55 pagi, sehingga jam 08:00 pagi tetap ditandai tersedia.
    - **After**: Menetapkan default timezone aplikasi ke `Asia/Jakarta` (WIB) sehingga sinkron dengan waktu nyata di Indonesia.
  * **Penerapan Jeda Persiapan Minimal 1 Jam untuk Booking Hari-H**:
    - **Before**: Tidak ada batas persiapan waktu. Pengguna bisa memesan jam yang sudah lewat atau jam yang terlalu mepet dengan waktu saat ini.
    - **After**: Pada pemesanan hari ini, slot paling awal yang diizinkan memiliki buffer persiapan minimal 1 jam dari waktu saat ini (contoh: akses jam 14:03, slot mulai paling awal adalah 16:00 WIB). Validasi backend menolak keras jika ada manipulasi jam yang sudah lewat.
  * **Penyembunyian Jam yang Sudah Lewat dari Dropdown (Opsi Tampilan 1)**:
    - **Before**: Jam-jam pagi yang sudah lewat tetap muncul di dropdown jam mulai.
    - **After**: Jam yang sudah lewat atau masih berada dalam jeda 1 jam otomatis dihilangkan dari daftar dropdown, sehingga klien hanya melihat jam yang benar-benar bisa dipesan. Jika sisa jam operasional hari ini sudah habis, kalender otomatis menandai tanggal hari ini dengan status "Tutup".
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #70**: Anti-AI Slop, copywriting to-the-point dan natural.
  * **Rule #51**: Logika validasi waktu formulir bersih, aman, dan dapat diandalkan.
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear` dan `php artisan view:cache`: **PASS** (Zero error).
  * Pengujian slot `2026-11-16` pada pukul 14:06 WIB: **PASS** (08:00 - 15:00 terfilter otomatis, hanya 16:00 yang tersedia).

---

### [2026-11-16 12:00 WITA] — Implementasi Mesin Rekomendasi Pencarian Pintar (Photon Komoot Geocoder) dan Otomasi Auto-Fill Dropdown Provinsi serta Kota (Rule #70, Rule #51, Rule #41)

* **Modul / Fitur**:
  - Formulir Reservasi Publik (`resources/views/public/booking/create.blade.php`).
* **File yang Diubah**:
  * `resources/views/public/booking/create.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Upgrade Mesin Pencari Pintar Peta (100% Gratis)**:
    - **Before**: Pencarian lokasi hanya menggunakan OpenStreetMap Nominatim biasa dengan pencarian teks kaku (tidak toleran salah ketik, lambat, dan sering gagal menampilkan nama tempat komersial seperti hotel, kafe, villa, atau pantai).
    - **After**: Mengintegrasikan **Photon by Komoot** (mesin pencari Elasticsearch OpenStreetMap) dengan toleransi salah ketik (*fuzzy search dan typo tolerance*), pencarian berbasis koordinat aktif peta (*location bias*), penanda kategori ramah pengguna (*Hotel*, *Pantai*, *Kuliner*, *Wisata*, *Wilayah*), serta fallback otomatis ke Nominatim jika diperlukan. Mengubah `z-index` dropdown rekomendasi ke `z-[1050]` agar tidak tertutup oleh layer Leaflet map.
  * **Otomasi Sinkronisasi Provinsi dan Kota (Auto-Fill serta Auto-Select)**:
    - **Before**: Saat pengguna memilih lokasi rekomendasi, mengklik "Gunakan Lokasi Saat Ini", atau mengklik/menggeser pin di peta, hanya titik koordinat yang bergeser. Input dropdown Provinsi dan Kota/Kabupaten tetap kosong dan harus diisi manual, menyebabkan error validasi jika terlewat.
    - **After**: Mengimplementasikan `reverseGeocodeAndMatch(lat, lng)` dan pencocokan wilayah otomatis:
      1. Begitu pin peta berubah (via tombol lokasi saat ini, klik rekomendasi pencarian, klik peta, atau geser pin), sistem otomatis mengekstrak data wilayah via reverse geocoder cepat (Photon/Nominatim).
      2. Sistem mencocokkan nama provinsi ke database 38 provinsi Indonesia dan mengirimkan event `@booking-province-set.window` untuk memperbarui tombol dropdown dan input tersembunyi `province_code`.
      3. Sistem memuat daftar kota (`/api/cities/{provinceCode}`) dan langsung mencocokkan kota/kabupaten yang sesuai, lalu memilihnya otomatis via `selectBookingCity` tanpa menggeser posisi pin presisi pengguna (`skipPan: true`).
      4. Validasi real-time untuk Provinsi dan Kota langsung terpenuhi (`errors.province = ''`, `errors.city = ''`), dan nama venue/alamat lengkap di textarea otomatis terisi jika belum ada.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #70**: Anti-AI Slop, copywriting to-the-point, natural, tanpa redundant `&` atau hiperbola.
  * **Rule #51**: Copywriting formulir bersih, to-the-point, dan profesional.
  * **Rule #41**: UI elegan dan responsif tanpa glitch z-index.
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear` dan `php artisan view:cache`: **PASS** (Zero error).

---

### [2026-11-16 11:18 WITA] — Implementasi Model Hybrid (Inisialisasi Chat Otomatis + Review Reservasi Fotografer) & Perbaikan Real-Time Validation Auto-Scroll (Rule #70, Rule #51)

* **Modul / Fitur**:
  - Formulir Reservasi Publik (`resources/views/public/booking/create.blade.php`).
  - Backend Reservasi Jasa (`app/Http/Controllers/Public/BookingController.php`).
  - Dashboard Mitra Fotografer (`app/Http/Controllers/Photographer/PhotographerBookingController.php` & `resources/views/photographer/bookings/index.blade.php`).
* **File yang Diubah**:
  * `resources/views/public/booking/create.blade.php`
  * `app/Http/Controllers/Public/BookingController.php`
  * `app/Http/Controllers/Photographer/PhotographerBookingController.php`
  * `resources/views/photographer/bookings/index.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Real-time Validation & Auto-Scroll Terfokus pada Form Reservasi**:
    - **Before**: Jika klien mengklik "Ajukan Reservasi Jadwal" di bawah saat Bagian 3 (Provinsi/Kota/Alamat) belum terisi, form menahan pengiriman namun layar tetap diam di bawah tanpa menggulir ke atas karena target dropdown button berada di dalam scope child Alpine yang tidak terjangkau parent `$refs`.
    - **After**: Menambahkan `x-ref="locationSection"` dan `id="sectionLocation"` pada kontainer Bagian 3, serta `id="bookingProvinceBtn"` dan `id="bookingCityBtn"` pada tombol dropdown. Jika validasi gagal, layar secara otomatis meluncur halus (*smooth auto-scroll*) langsung ke elemen yang belum lengkap dan memberikan fokus. Styling pesan error dan border diubah ke palet standar rose (`border-rose-400`, `text-rose-500`) sesuai sistem desain.
    - Sinkronisasi data kota diperkuat dengan mengikat `@booking-city-set.window` langsung ke variabel induk `cityCode` dan menutup menu pilihan kota secara otomatis saat opsi diklik.
  * **Penerapan Model Hybrid (Airbnb / Bridestory Standard)**:
    - Klien tetap bisa langsung memesan instan via form booking tanpa dipaksa membuka ruang chat terlebih dahulu.
    - Begitu form diajukan, sistem secara otomatis menginisialisasi ruang obrolan (*chat thread*) antara Klien dan Fotografer serta mengirim pesan pembuka terstruktur yang merangkum kode kontrak, paket, tanggal, rentang jam, alamat venue, dan catatan klien.
    - Pada Dashboard Fotografer, baris pesanan yang menunggu review kini menyajikan tombol aksi **"Terima"** (1 klik untuk menyetujui langsung) dan **"Chat Klien"** (untuk berdiskusi sebelum menyetujui).
    - Ketika fotografer menekan **"Terima"** atau **"Tolak"**, sistem mengirimkan pesan status resmi otomatis ke dalam ruang obrolan tersebut agar kedua pihak tetap sinkron.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #70**: Anti-AI Slop, tanpa redundansi kata atau simbol `&`, copywriting to-the-point dan natural.
  * **Rule #51**: Copywriting formulir bersih dan profesional.
  * **Rule #41**: Whitespace bersih tanpa sekat garis divider artifisial.
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear` & `php artisan view:cache`: **PASS** (Zero error).

---

### [2026-11-15 01:38 WITA] — Implementasi Alur Reservasi Skema B (Review & Persetujuan Fotografer Dahulu Sebelum Tagihan Invoice, Rule #70, Rule #51)

* **Modul / Fitur**:
  - Alur Pemesanan Jasa Fotografi (`BookingController`, `PhotographerBookingController`, `ClientBookingController`, `BookingScheduleService`, dan view Blade terkait).
* **File yang Diubah**:
  * `resources/views/public/booking/create.blade.php`
  * `app/Http/Controllers/Public/BookingController.php`
  * `resources/views/public/booking/invoice.blade.php`
  * `app/Http/Controllers/Photographer/PhotographerBookingController.php`
  * `resources/views/photographer/bookings/index.blade.php`
  * `resources/views/client/bookings/index.blade.php`
  * `app/Services/BookingScheduleService.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Alur Pemesanan Klien**:
    - **Before**: Klien langsung diarahkan membayar invoice sesaat setelah mengisi formulir pemesanan, sebelum fotografer meninjau kelayakan jadwal dan lokasi acara.
    - **After**: Mengubah teks tombol formulir menjadi "Ajukan Reservasi Jadwal" dengan catatan edukatif: "Belum ada pembayaran. Fotografer akan memeriksa jadwal terlebih dahulu sebelum tagihan diterbitkan." Kontrak dibuat dengan status awal `draft` dan `is_validated_photographer = false`.
  * **Halaman Invoice & Keamanan Transaksi**:
    - **Before**: Halaman invoice langsung menampilkan instruksi pembayaran dan tombol unggah struk transfer.
    - **After**: Jika status reservasi masih `draft`, halaman invoice menampilkan status "Menunggu Persetujuan" dan notifikasi informasional bahwa fotografer sedang meninjau jadwal & venue acara. Instruksi pembayaran dan fungsi konfirmasi pembayaran dikunci (*guarded*) hingga fotografer menyetujui reservasi.
  * **Dashboard & Aksi Mitra Fotografer**:
    - **Before**: Tombol aksi langsung "Upload Hasil Foto" bahkan ketika pembayaran belum terkonfirmasi aktif.
    - **After**: Pesanan yang masuk berstatus "Perlu Direview" dengan tombol aksi utama "Terima" atau opsi "Tolak Sesi". Ketika fotografer mengklik "Terima", status kontrak berpindah ke `pending_payment` dengan batas waktu bayar 24 jam untuk klien, dan notifikasi otomatis dikirim ke akun klien beserta link invoice.
  * **Dashboard & Daftar Reservasi Klien**:
    - **Before**: Hanya menampilkan status umum dan tombol "Chat".
    - **After**: Menampilkan status "Menunggu Persetujuan" saat awal pengajuan, dan berubah menjadi "Menunggu Pembayaran" dengan tombol aksi utama "Bayar" segera setelah fotografer menyetujui pesanan.
  * **Proteksi Jadwal & Bentrok Slot Waktu (`BookingScheduleService`)**:
    - Query ketersediaan jadwal memperhitungkan reservasi `draft` yang belum kedaluwarsa sehingga mencegah dua klien berbeda memesan jam pemotretan yang bertabrakan saat fotografer sedang meninjau permintaan awal.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #70**: Standarisasi teks alami tanpa pemborosan simbol `&`, tanpa bahasa alay/bertele-tele, to-the-point, serta penggunaan istilah lokal terpercaya.
  * **Rule #51**: Copywriting proporsional, bersih, anti-fluff marketing.
  * **Rule #41**: Tanpa garis pembatas (*border-t / border-b*) artifisial tambahan.
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear` & `php artisan view:cache`: **PASS** (Zero error).
  * `git status`: Semua file terverifikasi bersih dan rapi.

---

### [2026-11-15 01:15 WITA] — Implementasi Live Typeahead Autocomplete & Smart Viewbox Biasing pada Kolom Pencarian Peta (Opsi B, Rule #93, Rule #70)

* **Modul / Fitur**:
  - Halaman Pemesanan / Reservasi Booking Klien (`resources/views/public/booking/create.blade.php`).
* **File yang Diubah**:
  * `resources/views/public/booking/create.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Live Autocomplete Typeahead Real-time**:
    - **Before**: Pencarian lokasi peta hanya berjalan jika pengguna menekan tombol "Cari" atau tombol Enter. Hasil pencarian memanjang ke bawah dan menggeser posisi peta (*layout shifting*).
    - **After**: Menerapkan pencarian otomatis saat mengetik (`@input.debounce.350ms`) tanpa perlu menekan tombol Cari. Hasil rekomendasi ditampilkan dalam bentuk *floating dropdown* mengambang modern ala Google Maps tepat di bawah input bar dengan efek bayangan tajam (*shadow-2xl*), ikon pin lokasi, judul tempat tebal, dan alamat administratif pelengkap.
  * **Smart Viewbox Biasing Berdasarkan Area Peta**:
    - Query pencarian otomatis menyertakan parameter `viewbox` dinamis sesuai pusat koordinat peta/wilayah yang sedang aktif (misalnya jika pengguna memilih Bali/Badung, pencarian jalan dan tempat di sekitar wilayah tersebut diprioritaskan di baris teratas).
    - Status loading interaktif dilengkapi indikator pemutar (*animated spinner*) di dalam sisi kanan input bar saat pencarian berlangsung.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #93 & Rule #70** (Standarisasi Estetika Modern Bersih, Anti-AI Slop, Autocomplete Ergonomis, Bebas Biaya Layanan Eksternal).
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear` & `php artisan view:cache`: **PASS** (Zero error).

---

### [2026-11-15 01:00 WITA] — Perluasan Dimensi Tinggi Kontainer Peta Reservasi (Rule #70, Rule #93)

* **Modul / Fitur**:
  - Halaman Pemesanan / Reservasi Booking Klien (`resources/views/public/booking/create.blade.php`).
* **File yang Diubah**:
  * `resources/views/public/booking/create.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Perluasan Tinggi Kontainer Peta**:
    - **Before**: Kontainer peta hanya memiliki tinggi `h-56` (224px) yang terasa sempit, terpotong, dan membatasi pandangan pengguna saat mencari lokasi dan menancapkan pin.
    - **After**: Memperbesar tinggi kontainer menjadi `h-80 sm:h-96 md:h-[420px]` dengan inline fallback `min-height: 380px;`. Peta kini tampil luas, lapang, dan proporsional untuk eksplorasi area acara dan penempatan pin marker. Menambahkan `invalidateSize()` setelah peta dimuat agar seluruh ubin render mulus tanpa celah abu-abu.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #70 & Rule #93** (Proporsionalitas UI, Ergonomi Pengguna, Presisi Titik Lokasi Acara).
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear` & `php artisan view:cache`: **PASS** (Zero error).

---

### [2026-11-15 00:55 WITA] — Penerapan Tampilan Google Maps Roadmap & Layer Switcher Satelit pada Peta Booking (Rule #93, Rule #70)

* **Modul / Fitur**:
  - Halaman Pemesanan / Reservasi Booking Klien (`resources/views/public/booking/create.blade.php`).
* **File yang Diubah**:
  * `resources/views/public/booking/create.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Penerapan Tampilan Google Maps Asli**:
    - **Before**: Peta menggunakan tile layer default OpenStreetMap standar yang tampak kurang familiar dan bernuansa visual kaku bagi pengguna umum Indonesia.
    - **After**: Mengganti tile base layer dengan **Google Maps Roadmap asli** (`https://{s}.google.com/vt/lyrs=m...`) dengan batas zoom hingga level 20, visual jalan raya, taman, serta tipografi khas Google Maps yang sangat bersih dan familiar.
  * **Layer Switcher Satelit Modern**:
    - Menambahkan kontrol layer di pojok kanan atas peta (`topright`) yang memungkinkan pengguna beralih antara tampilan **Peta Jalan (*Google Roadmap*)** dan tampilan foto **Satelit (*Google Hybrid*)**.
    - Kontrol layer dan bar zoom Leaflet disesuaikan menggunakan gaya rounded modern (`border-radius: 0.75rem`) dan bayangan lembut agar selaras dengan desain LensMatch.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #93 & Rule #70** (Standarisasi Estetika Modern Bersih, Anti-AI Slop, Peningkatan UX Interaktif).
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear` & `php artisan view:cache`: **PASS** (Zero error).

---

### [2026-11-15 00:50 WITA] — Implementasi Auto-Geolocation & Real-time Map Pan Berdasarkan Pilihan Provinsi & Kota (Rule #93, Rule #94)

* **Modul / Fitur**:
  - Halaman Pemesanan / Reservasi Booking Klien (`resources/views/public/booking/create.blade.php`).
  - Endpoint API Data Wilayah Kota (`app/Http/Controllers/Public/PhotographerController.php`).
* **File yang Diubah**:
  * `app/Http/Controllers/Public/PhotographerController.php`
  * `resources/views/public/booking/create.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Default Lokasi Peta Otomatis (Auto Geolocation)**:
    - **Before**: Peta Leaflet selalu default membuka koordinat statis Jakarta (`-6.2, 106.81`) terlepas dari siapa fotografer yang dipesan atau dari mana user mengakses web.
    - **After**: Peta Leaflet kini otomatis memanggil Browser Geolocation API (`navigator.geolocation.getCurrentPosition`). Jika diizinkan, peta langsung bergeser (*pan/flyTo*) ke koordinat lokasi pengguna. Jika izin ditolak/tidak tersedia, peta menggunakan fallback cerdas ke koordinat kota domisili fotografer yang sedang dibooking.
  * **Real-time Map Pan Saat Provinsi & Kota Dipilih**:
    - **Before**: Memilih dropdown Provinsi atau Kota tidak mempengaruhi tampilan peta, sehingga peta tetap berada di koordinat awal dan tidak relevan dengan wilayah yang dipilih.
    - **After**: 
      1. Endpoint `getCities` kini menyertakan kolom `meta` (`lat` dan `long` dari database `indonesia_cities`).
      2. Saat Provinsi dipilih (misal: BALI), peta otomatis melakukan animasi `flyTo` ke pusat provinsi pada zoom level 9.
      3. Saat Kota/Kabupaten dipilih (misal: KABUPATEN BADUNG), peta otomatis melakukan `flyTo` ke pusat kota pada zoom level 12 secara real-time tanpa delay.
      4. Sesuai kesepakatan, proses ini murni menggeser viewport peta tanpa menancapkan pin marker palsu. Pin marker baru ditancapkan saat pengguna secara sadar mengklik titik lokasi acaranya di peta atau memilih hasil pencarian venue.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #93 & Rule #94** (Standarisasi Data Dinamis Riil Database, Zero Dummy Fallback, Presisi Lokasi Sesi Pemesanan).
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear` & `php artisan view:cache`: **PASS** (Zero error).

---

### [2026-11-15 00:35 WITA] — Penghapusan Subteks Judul Redundan pada Tab Tanya Jawab Profil Fotografer (Rule #70, Rule #51)

* **Modul / Fitur**:
  - Halaman Detail Profil Fotografer Publik (`resources/views/public/photographers/show.blade.php`).
* **File yang Diubah**:
  * `resources/views/public/photographers/show.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Penghapusan Subteks Redundan / Fluff**:
    - **Before**: Di bawah judul `"Tanya Jawab Fotografer"` terdapat subteks `"Informasi layanan langsung dari [Nama Fotografer]."` dan di bawah judul `"Ketentuan Sistem LensMatch"` terdapat subteks `"Ketentuan pemesanan, pembayaran, jadwal ulang, dan penerimaan hasil foto di LensMatch."`. Subteks ini bertele-tele dan tidak menambah nilai fungsional.
    - **After**: Menghapus kedua paragraf subteks tersebut sehingga judul tampil bersih, padat, profesional, dan seragam dengan tab lainnya (*Paket Layanan*, *Galeri Portofolio*, *Kalender Ketersediaan*, dan *Ulasan Klien*). Tautan `"Lihat Pusat Bantuan"` kini disejajarkan secara vertikal di tengah (`items-center`) dengan judul section.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #70 & Rule #51** (Anti-AI Slop, Copywriting To-The-Point, Eliminasi Fluff/Deskripsi Redundan).
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear` & `php artisan view:cache`: **PASS** (Zero error).

---

### [2026-11-15 00:30 WITA] — Implementasi Auto-Disappearing Text Bubble Umpan Balik Visual Tombol Save & Unsave Favorit (Rule #70, Rule #51, Rule #93)

* **Modul / Fitur**:
  - Halaman Detail Profil Fotografer Publik (`resources/views/public/photographers/show.blade.php`)
  - Halaman Katalog Pencarian Fotografer Publik (`resources/views/public/photographers/katalog.blade.php`)
  - Halaman Fotografer Favorit Klien (`resources/views/client/favorites/index.blade.php`)
* **File yang Diubah**:
  * `resources/views/public/photographers/show.blade.php`
  * `resources/views/public/photographers/katalog.blade.php`
  * `resources/views/client/favorites/index.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Text Bubble Umpan Balik Kontekstual Langsung**:
    - **Before**: Umpan balik aksi simpan/hapus hanya mengandalkan toast notification di sudut layar atas serta perubahan warna ikon tombol. Pengguna tidak mendapatkan umpan balik langsung (*text bubble*) tepat pada titik klik tombol bookmark.
    - **After**: Menambahkan elemen *speech bubble* melayang (`x-show="bubbleVisible"`) dengan ekor panah SVG presisi yang menunjuk langsung ke tombol bookmark. Balon teks memunculkan teks to the point tanpa embel-embel alay: `"Tersimpan di Favorit"` saat aksi save berhasil, dan `"Dihapus dari Favorit"` saat aksi unsave berhasil. Balon teks ini beranimasi pop-in halus dan otomatis menghilang (*auto-disappear*) secara mulus setelah 2.2 detik menggunakan timeout Alpine.js tanpa menghalangi klik berikutnya (`pointer-events-none`).
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #70 & Rule #51** (Anti-AI Slop, Copywriting To-The-Point, Tanpa Simbol Redundan/Alay, Teks Alami Bersih).
  * **Rule #93** (Standarisasi Tombol Wishlist / Favorit Fotografer & Wajib Feedback Visual).
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear` & `php artisan view:cache`: **PASS** (Zero error).
  * `npm run build`: **PASS** (Compiled Vite bundle successfully).

---

### [2026-11-15 00:10 WITA] — Implementasi Modal Konfirmasi Validasi Pop-up Saat Unsave Fotografer di Seluruh Sistem (Rule #70, Rule #59, Rule #18)

* **Modul / Fitur**:
  - Halaman Fotografer Favorit Klien (`resources/views/client/favorites/index.blade.php`)
  - Katalog Pencarian Fotografer Publik (`resources/views/public/photographers/katalog.blade.php`)
  - Detail Profil Fotografer Publik (`resources/views/public/photographers/show.blade.php`)
* **File yang Diubah**:
  * `resources/views/client/favorites/index.blade.php`
  * `resources/views/public/photographers/katalog.blade.php`
  * `resources/views/public/photographers/show.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Modal Konfirmasi Saat Unsave**:
    - **Before**: Mengklik tombol bookmark/favorit saat fotografer berstatus tersimpan/favorit langsung mengeksekusi request AJAX toggle dan menghapus fotografer dari daftar tanpa konfirmasi apapun, yang berisiko memicu penghapusan tidak sengaja.
    - **After**: Menambahkan modal validasi pop-up konfirmasi sistem (`open-confirm-modal`) dengan teks lugas dan tegas anti-AI slop: Judul `"Hapus dari Favorit?"`, pesan `"Apakah Anda yakin ingin menghapus [Nama Fotografer] dari daftar fotografer favorit?"`, tombol konfirmasi bahaya merah `"Ya, Hapus Favorit"`, dan tombol netral `"Batal"`. Aksi un-save hanya dieksekusi setelah pengguna menyetujui dialog konfirmasi. Operasi penambahan ke favorit (save) tetap berlangsung instan tanpa modal untuk menjaga alur browsing cepat dan mulus.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #70** (Anti-AI Slop, Bahasa Tegas, Lugas, Tanpa Frasa Berbunga-bunga/Alay/Karakter Redundan)
  * **Rule #59** (Desain Komponen Modal Konfirmasi Terpadu dengan Aksi Destruktif Berwarna Merah)
  * **Rule #18** (Standar Penanganan Aksi Kritis Pengguna & Validasi Eksplisit)
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear` & `php artisan view:cache`: **PASS** (100% blade templates cached successfully).

---

### [2026-11-15 00:00 WITA] — Penegasan Pemusatan Vertikal Blok Kanan (Tarif & Tombol) dengan `self-center`, `my-auto`, dan Inline Style (Rule #70)

* **Modul / Fitur**:
  - Halaman Fotografer Favorit Klien (`resources/views/client/favorites/index.blade.php`).
* **File yang Diubah**:
  * `resources/views/client/favorites/index.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Pemusatan Vertikal Blok Kanan**:
    - **Before**: Blok kanan menggunakan utility `md:self-center` yang tidak terkompilasi pada bundle CSS pre-built, sehingga browser mengabaikannya dan elemen tetap bertengger di bagian atas container kartu.
    - **After**: Mengganti class menjadi `self-center my-auto` (yang didukung penuh di bundle CSS) serta menambahkan atribut fallback eksplisit `style="align-self: center; margin-top: auto; margin-bottom: auto;"`. Sekarang blok kanan (tarif dan tombol aksi) dijamin 100% berada tepat di tengah-tengah kartu secara vertikal (*vertical center*).
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #70** (Anti-AI Slop, Layout Proporsional & Rapi), **Rule #91** (Hierarki Aksi Fungsional Transaksional Nyata).
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear` & `php artisan view:cache`: **PASS** (Zero error).

---

### [2026-11-14 23:56 WITA] — Pemusatan Vertikal Blok Tarif & Tombol Aksi Kanan (Pasted Image 2) pada Card Favorit (Kepatuhan Rule #70)

* **Modul / Fitur**:
  - Halaman Fotografer Favorit Klien (`resources/views/client/favorites/index.blade.php`).
* **File yang Diubah**:
  * `resources/views/client/favorites/index.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Pemusatan Vertikal Blok Kanan (Tarif & Tombol)**:
    - **Before**: Blok kanan menggunakan `md:self-stretch justify-between` yang memisahkan teks tarif di batas paling atas dan tombol di batas paling bawah kartu.
    - **After**: Mengubah container blok kanan menjadi `w-full md:w-64 shrink-0 flex flex-col justify-center md:self-center space-y-3.5`. Seluruh blok tarif ("Mulai Dari" + Rp ...) dan tombol aksi ("Reservasi" + "Chat") kini berada tepat di tengah secara vertikal (*vertically centered*) di dalam kartu fotografer, selaras dengan highlight area pada screenshot DevTools pengguna (Pasted Image 2).
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #70** (Anti-AI Slop, Layout Proporsional & Rapi), **Rule #91** (Hierarki Aksi Fungsional Transaksional Nyata).
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear` & `php artisan view:cache`: **PASS** (Zero error).

---

### [2026-11-14 23:52 WITA] — Penyelarasan Presisi Baseline Tombol Kanan dengan Deskripsi Teks Bio di Kiri (Kepatuhan Rule #70 & Rule #91)

* **Modul / Fitur**:
  - Halaman Fotografer Favorit Klien (`resources/views/client/favorites/index.blade.php`).
* **File yang Diubah**:
  * `resources/views/client/favorites/index.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Penyelarasan Baseline Tombol Kanan vs Deskripsi Teks Bio**:
    - **Before**: Kolom tengah berdimensi tinggi alami (~150px) tanpa `justify-between`, sedangkan kolom kanan dan cover foto sebelah kiri membentang dengan tinggi 192px (`h-48`). Akibatnya baris tombol `Reservasi` dan `Chat` terdorong ke posisi terbawah kolom kanan (192px), sehingga posisinya turun ~40px lebih rendah dari baris akhir teks deskripsi/bio di kolom tengah.
    - **After**: Mengubah kolom tengah menjadi `flex flex-col justify-between md:self-stretch`, cover foto kiri menjadi `md:self-stretch min-h-[160px]`, dan kolom kanan menjadi `md:self-stretch flex flex-col justify-between`. Menghilangkan offset `pt-2` pada baris tombol. Sekarang baris tombol `[ Reservasi ] [ Chat ]` berada pada satu garis lurus horizontal (*perfect baseline alignment*) yang sejajar presisi dengan teks deskripsi bio di sebelah kiri.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #70** (Anti-AI Slop, Human-Crafted Horizontal Rhythm & Alignment), **Rule #91** (Hierarki Aksi Fungsional Transaksional Nyata).
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear` & `php artisan view:cache`: **PASS** (Zero error).

---

### [2026-11-14 23:45 WITA] — Implementasi Penataan Tombol Berdampingan Horizontal (Opsi A) pada Card Favorit (Kepatuhan Rule #70 & Rule #91)

* **Modul / Fitur**:
  - Halaman Fotografer Favorit Klien (`resources/views/client/favorites/index.blade.php`).
* **File yang Diubah**:
  * `resources/views/client/favorites/index.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Penataan Tombol Aksi Kolom Kanan (Opsi A)**:
    - **Before**: Tombol `Reservasi` dan `Chat` ditumpuk secara vertikal (`space-y-2`) di dalam kolom kanan selebar `w-56`. Penumpukan ini memakan tinggi ~80px, membuat tombol `Reservasi` memotong di samping baris pertama bio (`...momen dramatis dan energi`), dan tombol `Chat` menggantung sendirian di bawahnya di area kosong.
    - **After**: Mengubah tata letak tombol menjadi berdampingan secara horizontal dalam 1 baris (`flex items-center gap-2 pt-2`) di dalam kolom kanan selebar `md:w-64`. Tombol `Reservasi` menempati porsi utama (`flex-1`) dan tombol `Chat` bersanding rapi di sampingnya (`shrink-0`). Tinggi area tombol berkurang menjadi ~38px dan sejajar rapi (*bottom-aligned*) dengan baris akhir bio serta batas bawah foto portofolio.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #70** (Anti-AI Slop, Tata Letak Proporsional Human-Crafted), **Rule #91** (Hierarki Aksi Fungsional Transaksional Nyata).
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear` & `php artisan view:cache`: **PASS** (Zero error).

---

### [2026-11-14 23:40 WITA] — Penyelarasan Kesejajaran Horizontal Lokasi vs Tarif & Penerapan Opsi 1 Profil Fotografer Favorit (Anti-AI Slop & Rule #70)

* **Modul / Fitur**:
  - Halaman Fotografer Favorit Klien (`resources/views/client/favorites/index.blade.php`).
* **File yang Diubah**:
  * `resources/views/client/favorites/index.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Kesejajaran Horizontal Lokasi vs "MULAI DARI"**:
    - **Before**: Card menggunakan `md:items-center` yang membuat kolom tengah melayang di posisi tengah vertikal, sementara kolom kanan dengan `self-stretch justify-between` menarik teks `MULAI DARI` ke batas paling atas kartu. Akibatnya teks lokasi studio (`KOTA ADMINISTRASI JAKARTA SELATAN`) berada lebih rendah dan tidak sejajar secara horizontal dengan `MULAI DARI`.
    - **After**: Mengubah pembungkus menjadi `md:items-start` dan menyelaraskan tipografi header baris atas kolom tengah (`leading-none text-[10px] sm:text-[11px] font-bold tracking-wider`) dengan kolom kanan (`leading-none text-[10px] sm:text-[11px] font-bold tracking-wider`). Sekarang kedua teks berada di satu garis lurus horizontal yang presisi (*top-aligned*).
  * **Penerapan Opsi 1 (Eliminasi Tumpukan Link Profil di Kolom Aksi)**:
    - Menghapus teks link `"Lihat Profil Lengkap >"` di bawah tombol Chat pada kolom kanan.
    - Menghubungkan tautan profil fotografer secara natural ke **Cover Portofolio Foto** dan **Avatar Profil** di samping nama fotografer.
    - Kolom kanan kini bersih, tegas, dan fokus murni pada 2 tombol aksi nyata: **Reservasi** dan **Chat**.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #70** (Anti-AI Slop, Zero Clutter), **Rule #91** (Hierarki Aksi Fungsional Transaksional Nyata).
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear` & `php artisan view:cache`: **PASS** (Zero error).

---

### [2026-11-14 23:26 WITA] — Perluasan Padding Card Fotografer Favorit & Eliminasi Sub-Text Judul Seluruh Fitur Akun Klien (Kepatuhan Anti-AI Slop & Rule #70)

* **Modul / Fitur**:
  - Halaman Fotografer Favorit Klien (`resources/views/client/favorites/index.blade.php`).
  - Halaman Reservasi Sesi Foto Klien (`resources/views/client/bookings/index.blade.php`).
  - Halaman Invoice & Tagihan Pembayaran Klien (`resources/views/client/invoices/index.blade.php`).
  - Halaman Galeri Hasil Foto Klien (`resources/views/client/galleries/index.blade.php`).
  - Halaman Pesan & Chat Fotografer (`resources/views/public/chat/index.blade.php`).
* **File yang Diubah**:
  * `resources/views/client/favorites/index.blade.php`
  * `resources/views/client/bookings/index.blade.php`
  * `resources/views/client/invoices/index.blade.php`
  * `resources/views/client/galleries/index.blade.php`
  * `resources/views/public/chat/index.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Perluasan Padding Card Fotografer Favorit**:
    - **Before**: Card fotografer favorit menggunakan padding `p-5 sm:p-6`, jeda baris `space-y-4 sm:space-y-5`, dan jarak `gap-6`. Ruang interior kartu tampak padat dan sempit di layar lebar.
    - **After**: Padding card diperbesar menjadi `p-6 sm:p-8 md:p-8`, jeda antar kartu `space-y-5 sm:space-y-6`, jarak antar bagian diperluas menjadi `gap-6 md:gap-8`, dan dimensi cover foto portofolio ditingkatkan menjadi `w-full md:w-64 h-52 md:h-48`. Seluruh konten terasa lega, elegan, dan proporsional.
  * **Eliminasi Sub-Text Judul Bertele-tele (Anti-AI Slop Standard)**:
    - Menghapus paragraf sub-teks di bawah H1 pada seluruh halaman fitur dropdown akun klien:
      - `client/favorites/index.blade.php`: Menghapus sub-teks `"Koleksi mitra fotografer pilihan yang Anda simpan untuk perbandingan portofolio dan pemesanan mendatang."`.
      - `client/bookings/index.blade.php`: Menghapus sub-teks `"Pantau status konfirmasi fotografer, rincian jadwal, dan akses galeri hasil foto Anda."`.
      - `client/invoices/index.blade.php`: Menghapus sub-teks `"Daftar seluruh tagihan pembayaran resmi LensMatch."`.
      - `client/galleries/index.blade.php`: Menghapus sub-teks `"Akses seluruh tautan album hasil retouching foto resolusi tinggi dari fotografer mitra LensMatch."`.
      - `public/chat/index.blade.php`: Menghapus sub-teks `"Pesan langsung dengan fotografer"` pada header sidebar chat.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #70** (Anti-AI Slop, Zero Verbose Text, Eliminasi Simbol & Kata Redundan), **Prinsip Desain Umum Anti-AI Slop** (Judul tegas to-the-point tanpa penjelasan berulang/alay).
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear` & `php artisan view:cache`: **PASS** (Zero error).

---

### [2026-11-13 22:42 WITA] — Perbaikan Visibilitas Komponen Pagination Tabel Laporan Fotografer dan Lomba Admin (Kepatuhan Rule #37, Rule #70, & Rule #78)

* **Modul / Fitur**:
  - Halaman Laporan Finansial Fotografer (`resources/views/photographer/reports/index.blade.php`).
  - Controller Laporan Fotografer (`app/Http/Controllers/Photographer/ReportController.php`).
  - Halaman Manajemen Lomba Admin (`resources/views/admin/contests/manage.blade.php`).
  - Halaman Submisi Lomba Admin (`resources/views/admin/contests/submissions.blade.php`).
* **File yang Diubah**:
  * `resources/views/photographer/reports/index.blade.php`
  * `app/Http/Controllers/Photographer/ReportController.php`
  * `resources/views/admin/contests/manage.blade.php`
  * `resources/views/admin/contests/submissions.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Penyebab Pagination Hilang**:
    - **Before**: Di tabel `Riwayat Pencairan Dana` (`photographer/reports/index.blade.php`) dan tabel pendaftar/submisi lomba, blok pagination dibungkus dengan `@if($contracts->hasPages())`. Karena jumlah data transaksi saat ini berjumlah 5 data ($\le 10$), method `hasPages()` mengembalikan `false` sehingga barisan pagination (Showing page, nomor halaman, dan dropdown *Rows per page*) tidak pernah dirender ke layar sama sekali.
    - **After**: Mengubah kondisi pembungkus menjadi `@if($contracts->total() > 0)` sesuai **Rule #37** (`README.md`). Sekarang baris pagination selalu tampil selama ada data (`total > 0`), sehingga pengguna tetap dapat melihat status halaman aktif serta mengganti pilihan *Rows per page* (10, 20, 50, 100) kapan saja.
  * **Penyelarasan Anti-AI Slop pada Tabel Riwayat Pencairan Dana**:
    - **Header Kolom (Rule #70)**: Menghapus simbol `&` pada kolom `Klien & Layanan` menjadi `Klien dan Layanan`.
    - **Tab Status Filter (Rule #78 & Rule #45)**: Mengganti tombol pil kotak AI-slop (`bg-amber-500 text-white` vs `bg-gray-100`) menjadi Navigasi Tab Teks Murni Horizontal dengan penanda aktif *Active Text Underline* amber dan *counter badge* bundar di sampingnya (`Semua Transaksi`, `Pencairan Selesai`, `Dana Tertahan`).
    - **Integrasi AJAX (Rule #18 & Rule #38)**: Menambahkan `id="dashboard-table-container"` pada kartu tabel agar pagination asinkron (*Zero Full Page Refresh*) berjalan mulus.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #18** (Seamless AJAX Pagination), **Rule #37** (Visibilitas Selalu Tampak Pagination `total() > 0`), **Rule #45** (No White Text on Amber), **Rule #70** (Anti-Ampersand Duplication), **Rule #78** (Active Text Underline Tabs Standard).
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear` & `php artisan view:cache`: **PASS** (Zero errors).
  * Controller simulation rendering:
    - `Photographer Reports`: **PASS** (124,544 bytes, `Pagination found: SUCCESS`, `Rows per page found: SUCCESS`).

---

### [2026-11-13 22:42 WITA] — Perbaikan Visibilitas Komponen Pagination Tabel Laporan Fotografer dan Lomba Admin (Kepatuhan Rule #37, Rule #70, & Rule #78)

* **Modul / Fitur**:
  - Halaman Laporan Finansial Fotografer (`resources/views/photographer/reports/index.blade.php`).
  - Controller Laporan Fotografer (`app/Http/Controllers/Photographer/ReportController.php`).
  - Halaman Manajemen Lomba Admin (`resources/views/admin/contests/manage.blade.php`).
  - Halaman Submisi Lomba Admin (`resources/views/admin/contests/submissions.blade.php`).
* **File yang Diubah**:
  * `resources/views/photographer/reports/index.blade.php`
  * `app/Http/Controllers/Photographer/ReportController.php`
  * `resources/views/admin/contests/manage.blade.php`
  * `resources/views/admin/contests/submissions.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Penyebab Pagination Hilang**:
    - **Before**: Di tabel `Riwayat Pencairan Dana` (`photographer/reports/index.blade.php`) dan tabel pendaftar/submisi lomba, blok pagination dibungkus dengan `@if($contracts->hasPages())`. Karena jumlah data transaksi saat ini berjumlah 5 data ($\le 10$), method `hasPages()` mengembalikan `false` sehingga barisan pagination (Showing page, nomor halaman, dan dropdown *Rows per page*) tidak pernah dirender ke layar sama sekali.
    - **After**: Mengubah kondisi pembungkus menjadi `@if($contracts->total() > 0)` sesuai **Rule #37** (`README.md`). Sekarang baris pagination selalu tampil selama ada data (`total > 0`), sehingga pengguna tetap dapat melihat status halaman aktif serta mengganti pilihan *Rows per page* (10, 20, 50, 100) kapan saja.
  * **Penyelarasan Anti-AI Slop pada Tabel Riwayat Pencairan Dana**:
    - **Header Kolom (Rule #70)**: Menghapus simbol `&` pada kolom `Klien & Layanan` menjadi `Klien dan Layanan`.
    - **Tab Status Filter (Rule #78 & Rule #45)**: Mengganti tombol pil kotak AI-slop (`bg-amber-500 text-white` vs `bg-gray-100`) menjadi Navigasi Tab Teks Murni Horizontal dengan penanda aktif *Active Text Underline* amber dan *counter badge* bundar di sampingnya (`Semua Transaksi`, `Pencairan Selesai`, `Dana Tertahan`).
    - **Integrasi AJAX (Rule #18 & Rule #38)**: Menambahkan `id="dashboard-table-container"` pada kartu tabel agar pagination asinkron (*Zero Full Page Refresh*) berjalan mulus.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #18** (Seamless AJAX Pagination), **Rule #37** (Visibilitas Selalu Tampak Pagination `total() > 0`), **Rule #45** (No White Text on Amber), **Rule #70** (Anti-Ampersand Duplication), **Rule #78** (Active Text Underline Tabs Standard).
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear` & `php artisan view:cache`: **PASS** (Zero errors).
  * Controller simulation rendering:
    - `Photographer Reports`: **PASS** (124,544 bytes, `Pagination found: SUCCESS`, `Rows per page found: SUCCESS`).

---

### [2026-11-13 22:30 WITA] — Standardisasi Tabel Pratinjau Dashboard Admin & Fotografer serta Refaktor Kolom Aksi Tabel Bookings (Anti-AI Slop, Anti-Stacked Buttons, Pure Text)

* **Modul / Fitur**:
  - Dashboard Admin — Pratinjau Tabel Transaksi (`resources/views/partials/table/table-01.blade.php`).
  - Dashboard Fotografer — Pratinjau Tabel Order Masuk Terkini (`resources/views/photographer/dashboard.blade.php`).
  - Halaman Bookings Fotografer — Tabel Aktual Reservasi Masuk (`resources/views/photographer/bookings/index.blade.php`).
* **File yang Diubah**:
  * `resources/views/partials/table/table-01.blade.php`
  * `resources/views/photographer/dashboard.blade.php`
  * `resources/views/photographer/bookings/index.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Dashboard Admin Pratinjau (`table-01.blade.php`)**:
    - **Before**: Tombol aksi `Lihat` menggunakan warna kuning amber (`bg-amber-50 text-amber-700`) bertentangan dengan tabel aktual Monitoring Transaksi (`bg-blue-50 text-blue-600`) dan terdapat scrollbar horizontal kaku akibat `whitespace-nowrap` pada nama klien dan fotografer.
    - **After**: Tombol `Lihat` diseragamkan 100% menggunakan Soft Blue (`bg-blue-50 text-blue-600 border-blue-200/80 rounded-lg min-w-[60px] text-center`) sesuai **Rule #80** dan tabel aktual. Sel nama klien & fotografer diberikan `max-w-[130px]` / `max-w-[140px]` dan `truncate` sehingga scrollbar horizontal hilang dan tabel pas di kontainer 8-kolom.
  * **Dashboard Fotografer Pratinjau (`photographer/dashboard.blade.php`)**:
    - **Before**: Header kolom tabel bertuliskan `Klien & ID Kontrak` (melanggar **Rule #70** karena ada `&`), hanya memiliki 4 kolom (`Klien & ID Kontrak`, `Paket Layanan`, `Pendapatan Bersih`, `Status`), tidak sinkron dengan tabel aktual `/photographer/bookings`, dan TIDAK MEMILIKI kolom `Aksi`. Penanganan status hanya 3 kondisi (`completed`, `active`, dan fallback `Menunggu`).
    - **After**: Kolom tabel pratinjau disinkronkan 100% dengan tabel aktual:
      `ID Kontrak dan Klien` | `Layanan dan Jadwal` | `Nominal Kontrak` | `Status Order` | `Aksi`
      Lengkap dengan tombol `Lihat` Soft Blue (`bg-blue-50 text-blue-600`) yang mengarahkan ke `route('photographer.bookings')`, serta dukungan status lengkap (`Dibatalkan`, `Selesai`, `Menunggu Konfirmasi Klien`, `Dikonfirmasi`, `Menunggu Pembayaran`, `Menunggu Konfirmasi`) dalam teks murni berwarna sesuai **Rule #14 & Rule #90**.
  * **Tabel Aktual Bookings Fotografer (`photographer/bookings/index.blade.php`)**:
    - **Before**: Header tabel menggunakan simbol `&` (`ID Kontrak & Klien` dan `Layanan & Jadwal Sesi`). Filter status memuat kata ganda AI-slop (`Selesai & Dikirim`, `Jadwal Dikonfirmasi`). Kolom `Aksi` menggunakan `<div class="flex flex-col items-center gap-1.5">` yang menumpuk 3 hingga 5 tombol secara vertikal ke bawah (pelanggaran fatal **Rule #69**), tombol ulasan memuat ikon SVG bintang (pelanggaran **Rule #10 & Rule #22**), dan tombol unggah foto menggunakan teks kabur `"Simpan Data"` (pelanggaran **Rule #51**).
    - **After**:
      1. Menghilangkan simbol `&` pada header tabel menjadi `ID Kontrak dan Klien` dan `Layanan dan Jadwal` sesuai **Rule #70**.
      2. Merapikan filter status menjadi ringkas to-the-point: `Menunggu Konfirmasi`, `Dikonfirmasi`, `Selesai`.
      3. Menerapkan arsitektur **Rule #69**: Maksimal 2 elemen inline horizontal (`flex items-center justify-center gap-1.5`):
         - **1 Tombol Utama (Primary Action)**:
           - Status `pending`: `Konfirmasi` (Soft Emerald).
           - Status `confirmed`: `Upload Hasil Foto` (Soft Amber, menggantikan "Simpan Data").
           - Status `completed`: `Nilai Klien` (Soft Amber, jika belum diulas) atau `Buka Link Drive` (Soft Blue, jika sudah diulas) atau `Ubah Ulasan`.
           - Status `cancelled`: `Chat Klien` (Soft Gray).
         - **1 Tombol Kebab Menu (`⋮`)**: Tombol tiga titik minimalis dengan Floating Dropdown Popover (`z-50`) untuk aksi sekunder (`Chat Klien`, `Ajukan Jadwal Ulang`, `Perbarui Link`) dan aksi destruktif (`Tolak Sesi` / `Batalkan Sesi` berteks rose).
      4. Menghapus seluruh ikon SVG bintang dari tombol `Nilai Klien` dan `Ubah Ulasan` sehingga 100% berupa teks murni (*pure text*) sesuai **Rule #10**.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #10** (Pure Text Action Buttons), **Rule #14** (Pure Colored Text Status), **Rule #22** (No Icons in Table Actions), **Rule #51** (Anti-AI Slop Clean Copywriting), **Rule #68** (Table Data Standardization), **Rule #69** (Anti-Stacked Buttons & Kebab Menu Architecture), **Rule #70** (Anti-Ampersand Duplication & Clean Terms), **Rule #80** (Standard Table Action Button Colors: Soft Blue Lihat, Soft Amber Edit/Upload/Rate, Soft Rose Cancel/Delete).
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear` & `php artisan view:cache`: **PASS** (Zero errors).
  * Controller simulation rendering:
    - `Photographer Dashboard`: **PASS** (113,123 bytes).
    - `Photographer Bookings Index`: **PASS** (186,390 bytes).
    - `Admin Table-01 Preview`: **PASS** (17,422 bytes).

---

### [2026-11-13 22:04 WITA] — Penyeragaman Warna Teks Angka dan Tanda Kurung pada Status Chips Dashboard Admin & Fotografer

* **Modul / Fitur**:
  - Dashboard Fotografer (`resources/views/photographer/dashboard.blade.php`).
  - Dashboard Admin (`resources/views/admin/dashboard.blade.php`).
* **File yang Diubah**:
  * `resources/views/photographer/dashboard.blade.php`
  * `resources/views/admin/dashboard.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Before**:
    - Teks label seperti `Konfirmasi Order` berwarna gelap (`text-gray-800`), tetapi teks angka dan tanda kurungnya `(1)` menggunakan warna abu-abu pudar berbeda (`text-gray-400 font-bold`).
  * **After**:
    - **Warna Teks 100% Selaras & Menyatu**: Teks label, tanda kurung, dan angka kini menggunakan warna yang sama persis (`text-gray-800 dark:text-gray-200 font-bold`) secara utuh:
      `Konfirmasi Order (1)`, `Kirim Hasil Foto (1)`, `Pencairan Dana (2)`
      serta pada admin: `Verifikasi Fotografer (0)`, `Dana Siap Tarik (0)`, `Submisi Lomba (0)`.
    - Menghilangkan diskoneksi visual antara label dan angka antrean.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #22** (Pure Text Default), **Rule #51** (Clean UI), serta **Rule #68** (Anti-AI Slop Typography Standard).
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear` & `php artisan view:cache`: **PASS** (Zero errors).

### [2026-11-13 22:01 WITA] — Eliminasi Boks Abu Pratinjau Katalog Publik dan Teks Redundan pada Dashboard Fotografer (Anti-AI Slop Box)

* **Modul / Fitur**:
  - Dashboard Fotografer (`resources/views/photographer/dashboard.blade.php`).
* **File yang Diubah**:
  * `resources/views/photographer/dashboard.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Before**:
    - Pratinjau metrik reputasi katalog publik (`★ 5.0 (3 ulasan) • 3 Sesi Selesai`) dibungkus dalam boks kartu abu-abu (`p-4 rounded-xl bg-gray-50/70 border border-gray-100`).
    - Terdapat teks keterangan redundan di sebelah kanan: `Tampil otomatis di profil dan kartu katalog publik`.
  * **After**:
    - **Zero Card Box & Pure Clean Layout**: Menghapus kartu pembungkus boks abu (`bg-gray-50/70` dan `border-gray-100`) sehingga teks pratinjau berdiri secara alami dan bersih di atas latar kartu utama tanpa sekat artifisial.
    - **Eliminasi Teks Redundan**: Menghapus teks penjelas `Tampil otomatis di profil dan kartu katalog publik` sehingga informasi menjadi padat, to-the-point, dan elegan.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #51** (Anti-AI Slop Microcopy & Fluff Elimination), **Rule #58** (Eliminasi Pembatas & Boks Artifisial), serta **Rule #101** (Zero Badge, Zero Box, Murni Tipografi).
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear` & `php artisan view:cache`: **PASS** (Zero errors).
  * Uji Render Controller Fotografer: **PASS**.

### [2026-11-13 21:58 WITA] — Standarisasi Monokrom Bersih Compact Status Chips (Eliminasi Warna-Warni Latar Belakang / Anti-AI Slop Pelangi)

* **Modul / Fitur**:
  - Dashboard Fotografer (`resources/views/photographer/dashboard.blade.php`).
  - Dashboard Admin (`resources/views/admin/dashboard.blade.php`).
* **File yang Diubah**:
  * `resources/views/photographer/dashboard.blade.php`
  * `resources/views/admin/dashboard.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Before**:
    - Ketika jumlah antrean > 0, chips berubah warna-warni ala "AI slop pelangi": chip 1 berwarna kuning (`bg-amber-50 text-amber-900 border-amber-300`), chip 2 berwarna biru (`bg-blue-50 text-blue-900 border-blue-300`), dan chip 3 berwarna hijau (`bg-emerald-50 text-emerald-900 border-emerald-300`).
    - Hal ini membuat tampilan dashboard fotografer terlihat alay, mencolok, dan tidak profesional saat terdapat antrean aktif.
  * **After**:
    - **Monokrom & Netral 100%**: Menghilangkan seluruh warna-warni latar belakang, border, dan teks pada status chips.
    - Semua chips kini seragam menggunakan gaya bersih monokrom seperti pada dashboard admin:
      - Latar: `bg-white dark:bg-gray-800`
      - Border: `border-gray-200 dark:border-gray-700/80`
      - Teks label: `text-gray-700 dark:text-gray-300`
      - Angka antrean: `text-gray-400 dark:text-gray-500 font-bold`
    - Tampilan tetap tenang, elegan, minimalis, dan profesional tanpa efek pelangi terlepas dari apakah antrean bernilai 0 atau > 0.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #22** (Strict Icon Constraint: Pure Text Default), **Rule #51** (Anti-AI Slop Microcopy & Clean UI), **Rule #61** (Hierarki Warna Sistem), serta **Rule #68** (Anti-AI Slop Table & Card Color Standard / Zero Rainbow Tint).
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear` & `php artisan view:cache`: **PASS** (Zero errors).
  * Uji Render Controller Fotografer & Admin: **PASS**.

### [2026-11-12 22:28 WITA] — Perubahan Fundamental Tarif Per Jam, Spare Time Opsional Per Paket, Auto-Bypass 500m, dan Booking Custom Multi-Hari via Chat

* **Modul / Fitur**:
  - Skema Database (`photographer_services`, `contract_bookings`, `chat_custom_offers`), Mesin Validasi Jadwal (`BookingScheduleService`), Paket Layanan Fotografer & Onboarding, Form Reservasi Klien (`/booking/create`), Etalase Katalog & Profil Fotografer, Penawaran Khusus Chat (Multi-Hari, Jam Fleksibel, DP 30%), Reschedule, Invoice, serta Seeder Layanan Demo.
* **File yang Diubah**:
  * `database/migrations/2026_11_12_000002_add_hourly_spare_time_and_multiday_fields.php` *(baru)*
  * `app/Services/BookingScheduleService.php` *(baru)*
  * `app/Models/PhotographerService.php`
  * `app/Models/ContractBooking.php`
  * `app/Models/ChatCustomOffer.php`
  * `app/Http/Controllers/Photographer/ServiceController.php`
  * `app/Http/Controllers/Photographer/OnboardingController.php`
  * `app/Http/Controllers/Photographer/AvailabilityController.php`
  * `app/Http/Controllers/Photographer/PhotographerBookingController.php`
  * `app/Http/Controllers/Client/ClientBookingController.php`
  * `app/Http/Controllers/Public/BookingController.php`
  * `app/Http/Controllers/Public/PhotographerController.php`
  * `app/Http/Controllers/Public/ChatController.php`
  * `routes/web.php`
  * `resources/views/layouts/app.blade.php`
  * `resources/views/photographer/services/create.blade.php`
  * `resources/views/photographer/services/edit.blade.php`
  * `resources/views/photographer/services/index.blade.php`
  * `resources/views/photographer/bookings/index.blade.php`
  * `resources/views/photographer/dashboard.blade.php`
  * `resources/views/photographer/chat/index.blade.php`
  * `resources/views/partials/photographer/onboarding-wizard.blade.php`
  * `resources/views/partials/shared/upcoming-session-banner.blade.php`
  * `resources/views/public/booking/create.blade.php`
  * `resources/views/public/booking/invoice.blade.php`
  * `resources/views/public/booking/invoice-print.blade.php`
  * `resources/views/public/photographers/index.blade.php`
  * `resources/views/public/photographers/katalog.blade.php`
  * `resources/views/public/photographers/show.blade.php`
  * `resources/views/public/home.blade.php`
  * `resources/views/public/chat/index.blade.php`
  * `resources/views/client/bookings/index.blade.php`
  * `resources/views/client/invoices/index.blade.php`
  * `resources/views/client/favorites/index.blade.php`
  * `resources/views/admin/photographers/show.blade.php`
  * `resources/views/admin/transactions/index.blade.php`
  * `database/seeders/PhotographerSeeder.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Before**:
    - Harga paket fotografer dihitung flat per paket tanpa opsi rentang jam klien, daftar jam memakai slot tetap terbatas, belum ada jeda persiapan (*spare time*) per paket, belum ada pengecualian jarak lokasi, serta penawaran khusus di chat hanya mendukung satu hari dengan opsi bayar penuh dan validasi jadwal reschedule masih mematok `+4 hours` secara hardcoded.
  * **After**:
    - **Tarif Per Jam & Slot Jam Bulat**: Harga `photographer_services.tarif_harga` ditetapkan per 1 jam (`/ jam`). Klien memilih `Jam Mulai` dan `Jam Selesai` (minimal 1 jam, kelipatan `:00`) lewat dua Custom Alpine Floating Dropdown dinamis dari `/booking/available-slots` dengan total `tarif_harga * (jam_selesai - jam_mulai)`, menutup jam lewat pada hari ini, tetap mengizinkan jam buka kosong maupun sesi yang berakhir tepat di jam tutup operasional.
    - **Spare Time Opsional Per Paket & Bypass 500m**: Fotografer dapat mengaktifkan jeda sebelum/sesudah (`0, 30, 60, 90, 120, 180` menit) serta opsi lewati jeda untuk lokasi berjarak ≤ 500 meter (Haversine via pin peta Leaflet OSM opsional; koordinat kosong aman dianggap lokasi berbeda). Jeda hanya aktif di sekitar pesanan yang sudah masuk dan dicek dua arah via `BookingScheduleService`.
    - **Booking Custom Multi-Hari & Chat**: Modal `Ajukan Booking Custom` mengirim pesan pembuka terstruktur ke ruang chat (`/booking/custom-request`). Fotografer mengirim Penawaran Khusus beruntun (`booking_date` sampai `booking_end_date`) dengan jam mulai/selesai fleksibel melewati jam tutup reguler, klien dapat menyetujui dengan `Bayar Penuh (100%)` atau `Uang Muka (DP 30%)`, dan seluruh tanggal dalam rentang otomatis terkunci dari booking reguler.
    - **Reschedule, UI & Seeder**: Reschedule di sisi klien dan fotografer memakai `BookingScheduleService::validateReschedule()` agar durasi asli, multi-hari, dan aturan spare/bypass tetap konsisten. Seluruh tampilan jadwal, invoice, dan seeder demo disinkronkan ke format rentang tanggal/jam serta satuan `/ jam`.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #41, #46, #58, #59, #67, #68, #70, #73, #74, #88, #89, #90, #91, #94, #100–#108**.
* **Hasil Pengujian & Verifikasi**:
  * Lint PHP (`php -l`) seluruh 15 file PHP terkait: **PASS**.
  * `php artisan view:clear`, `php artisan view:cache`, `npm run build` (`vite v8.2.1`, `815ms`): **PASS**.
  * Suite pengujian integrasi Laravel HTTP, `BookingScheduleService`, dan transaksi database rollback-safe (`26/26` skenario termasuk `/booking/available-slots`, `/booking/store` hitung per jam + blokir spare + bypass ≤ 500m, `/booking/custom-request`, `/chat/custom-offer` multi-hari fleksibel + `/accept` DP 30%, kunci kalender multi-hari, serta HTTP `200` rute Public/Client/Fotografer/Admin): **PASS (26/26)**.

---

### [2026-11-12 20:06 WITA] — Penjajaran Vertikal Daftar Fasilitas Paket Layanan dengan Judul Paket pada Form Reservasi

* **Modul / Fitur**:
  - Seksi `Pilih Paket Layanan` pada Form Reservasi (`/booking/create`, [create.blade.php](resources/views/public/booking/create.blade.php)).
* **File yang Diubah**:
  * `resources/views/public/booking/create.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Before**:
    - Grid daftar fasilitas (`✓ Durasi Sesi 1 Jam`, dll.) dimulai tepat di bawah bulatan radio button (`padding-left: 0`) sehingga tidak sejajar vertikal dengan judul dan deskripsi paket (`w-4` + `gap-3` = `1.75rem`).
  * **After**:
    - Menambahkan `pl-7` pada grid fasilitas paket (`pt-2 pl-7 grid grid-cols-1 sm:grid-cols-2 gap-1.5`) sehingga ikon centang dan teks fasilitas sejajar vertikal tepat dengan awal judul nama paket di atasnya.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #49** dan **Rule #98**.
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear`, `php artisan view:cache`, dan `npm run build` (`vite v8.2.1`, 857ms): **PASS**.
  * Validasi keseimbangan tag `<div>`, render HTTP `200` `/booking/create`, dan pengecekan kelas `pl-7`: **PASS** (`3/3 PASSED`).

---

### [2026-11-12 20:01 WITA] — Penghapusan Blok & Kalkulasi `Fitur Tambahan (Opsional)` pada Form Reservasi (`ServiceDetail` Murni Fasilitas Paket)

* **Modul / Fitur**:
  - Form Reservasi Sesi Foto (`/booking/create`, [create.blade.php](resources/views/public/booking/create.blade.php) & [BookingController.php](app/Http/Controllers/Public/BookingController.php)), seeder [PhotographerSeeder.php](database/seeders/PhotographerSeeder.php), FAQ [lensmatch_faqs.php](config/lensmatch_faqs.php), serta dokumentasi Rule #98 di [README.md](README.md).
* **File yang Diubah**:
  * `resources/views/public/booking/create.blade.php`
  * `app/Http/Controllers/Public/BookingController.php`
  * `database/seeders/PhotographerSeeder.php`
  * `config/lensmatch_faqs.php`
  * `README.md`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Before**:
    - Kolom kanan `Ringkasan Pesanan` pada `booking/create.blade.php` mengambil seluruh `selectedService.details` (padahal berisi rincian fasilitas inklusi paket yang dibuat fotografer dengan `tarif_harga = 0`) lalu menampilkannya sebagai checkbox `FITUR TAMBAHAN (OPSIONAL):` beserta baris kalkulasi `Tarif Paket Dasar`, `Fitur Tambahan: Rp 0`, dan `Total Nilai Sesi`.
    - `BookingController::store()` ikut memvalidasi `selected_features` dan menghitung ulang `ServiceDetail::sum('tarif_harga')`, sementara `PhotographerSeeder.php` masih menyisipkan `500000` pada item `Termasuk Cetak Canvas 40x60cm`.
  * **After**:
    - Menghapus blok checkbox `FITUR TAMBAHAN (OPSIONAL)`, state/input `selectedFeatures`, dan baris `Fitur Tambahan: Rp 0` di `booking/create.blade.php` serta meringkas baris tarif menjadi `Tarif Paket`.
    - Membersihkan parameter `selected_features` dan import `ServiceDetail` dari `BookingController.php` sehingga `$totalAmount` langsung memakai `$service->tarif_harga`.
    - Menyeragamkan `tarif_harga => 0` di `PhotographerSeeder.php` (serta menormalisasi 1 baris lama di DB live), menyesuaikan kalimat FAQ di `config/lensmatch_faqs.php`, dan memperbarui Rule #98 di `README.md`.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #49**, **Rule #51**, **Rule #94**, serta **Rule #98**.
* **Hasil Pengujian & Verifikasi**:
  * `php -l` pada `BookingController.php`, `PhotographerSeeder.php`, dan `lensmatch_faqs.php`, `php artisan config:clear`, `php artisan view:clear`, `php artisan view:cache`, serta `npm run build` (`vite v8.2.1`, 811ms): **PASS**.
  * Normalisasi DB live, validasi tag `<div>`, serta render HTTP `200` `/booking/create` dan `/faq` via HTTP Kernel: **PASS** (`7/7 PASSED`).

---

### [2026-11-12 19:54 WITA] — Perbaikan Pre-Selected Tanggal Libur Kalender, Warna Rincian DP/Jadwal, dan Penghapusan Subteks Seksi Form Reservasi

* **Modul / Fitur**:
  - Form Reservasi Sesi Foto (`/booking/create`, [BookingController.php](app/Http/Controllers/Public/BookingController.php) dan [create.blade.php](resources/views/public/booking/create.blade.php)).
* **File yang Diubah**:
  * `app/Http/Controllers/Public/BookingController.php`
  * `resources/views/public/booking/create.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Before**:
    - `BookingController@create` dan `create.blade.php` memaksa tanggal terpilih awal (`selectedDate`) menjadi `date('Y-m-d', strtotime('+2 days'))` tanpa mengecek jadwal libur fotografer, dan kelas sorot kuning tanggal terpilih belum menyaring `!day.isPast && !day.isFullDayBlocked` sehingga tanggal `Libur` (misal tanggal 14) tertampil memiliki border kuning.
    - Baris `Tagihan DP (30%) Sekarang` memakai warna primary amber (`text-amber-600`) dan `Sisa Pelunasan Nanti (70%)` memakai teks kecil abu-abu (`text-[11px]` `font-medium`), tidak seragam hitam/tebal seperti `Total Nilai Sesi` di atasnya.
    - Teks `Jadwal: ...` di atas slot jam memakai `text-amber-600 dark:text-amber-400`.
    - Setiap judul seksi kartu (`Pilih Paket Layanan`, `Jadwal Pelaksanaan Sesi Foto`, `Lokasi Pemotretan`, `Catatan Khusus`, `Ringkasan Pesanan`) masih menampilkan subteks penjelas `<p>` di bawahnya.
  * **After**:
    - `BookingController@create` mengatur `$preselectedDate = $request->input('tanggal', '')` sehingga tidak memaksa tanggal awal; `create.blade.php` menambahkan inisialisasi aman bulan kalender, fungsi `init()` untuk membersihkan tanggal libur/lampau, penjagaan `!day.isPast && !day.isFullDayBlocked` pada class tanggal terpilih, serta pesan ajakan saat tanggal belum dipilih.
    - Baris `Tagihan DP (30%) Sekarang` dan `Sisa Pelunasan Nanti (70%)` diseragamkan memakai label netral `text-xs text-gray-500 dark:text-gray-400 font-medium` dan nominal hitam tegas (`font-bold text-gray-900 dark:text-white` / `text-gray-800 dark:text-gray-200`).
    - Teks tanggal `Jadwal:` diubah ke warna normal `font-bold text-gray-900 dark:text-white`.
    - Seluruh subteks `<p>` di bawah judul kelima seksi kartu dihapus.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #49**, **Rule #51**, **Rule #98**, dan **Rule #99**.
* **Hasil Pengujian & Verifikasi**:
  * `php -l BookingController.php`, `php artisan view:clear`, `php artisan view:cache`, dan `npm run build` (`vite v8.2.1`, 818ms): **PASS**.
  * Validasi keseimbangan tag `<div>` `booking/create.blade.php` serta render HTTP `200` `/booking/create` terautentikasi via HTTP Kernel: **PASS** (`9/9 PASSED`).

---

### [2026-11-12 19:46 WITA] — Toggle Uncheck Radio Paket Layanan & Penyeragaman Ikon Lokasi Netral pada Form Reservasi

* **Modul / Fitur**:
  - Form Reservasi Sesi Foto (`/booking/create`, [create.blade.php](resources/views/public/booking/create.blade.php)).
* **File yang Diubah**:
  * `resources/views/public/booking/create.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Before**:
    - `selectService(id)` selalu langsung menimpa `this.selectedServiceId = id;` sehingga paket layanan yang sudah dipilih (tampilan radio button lingkaran) tidak dapat di-uncheck ketika diklik ulang.
    - Ikon pin lokasi fotografer di kartu `Ringkasan Pesanan` memakai warna kuning `text-amber-500` dengan SVG berbeda dari halaman publik lain, serta belum memiliki fallback `'-'`.
  * **After**:
    - Tampilan radio button lingkaran dipertahankan utuh, sementara `selectService(id)` diubah menjadi `this.selectedServiceId = (this.selectedServiceId === id ? null : id);` sehingga klik ulang pada paket aktif akan meng-uncheck pilihan, mereset `selectedFeatures`, mengembalikan kolom kanan ke state `Pilih Paket Layanan`, dan menjalankan validasi wajib pilih paket (`:value="selectedServiceId || ''"`).
    - Ikon pin lokasi di `Ringkasan Pesanan` diseragamkan memakai SVG Heroicons v2 abu-abu netral (`w-3.5 h-3.5 text-gray-400 dark:text-gray-400 shrink-0`) serta fallback `'-'` agar selaras dengan beranda, katalog, profil publik, dan halaman favorit.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #49**, **Rule #98**, serta **Rule #104**.
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear`, `php artisan view:cache`, dan `npm run build` (`vite v8.2.1`, 880ms): **PASS**.
  * Validasi keseimbangan tag `<div>` `booking/create.blade.php` dan render HTTP `200` `/booking/create` terautentikasi sebagai klien via HTTP Kernel: **PASS** (`6/6 PASSED`).

---

### [2026-11-12 19:38 WITA] — Penyederhanaan Kolom `Nama Paket` Tanpa Rincian Fasilitas pada Tabel Paket Layanan Fotografer

* **Modul / Fitur**:
  - Tabel Paket Layanan pada dashboard fotografer (`/photographer/services`, [services/index.blade.php](resources/views/photographer/services/index.blade.php)) beserta pembaruan standar dokumentasi pada [README.md](README.md) (Rule #106 poin 2).
* **File yang Diubah**:
  * `resources/views/photographer/services/index.blade.php`
  * `README.md`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Before**:
    - Header kolom pertama memakai `Nama Paket & Fasilitas` dan sel `<td>` menampilkan daftar teks fasilitas (`$srv->details->pluck('nama_fitur')->implode(' • ')` / `Belum ada rincian fasilitas`) di bawah nama layanan.
  * **After**:
    - Header `<th>` disederhanakan menjadi `Nama Paket` dan sel `<td>` hanya merender `{{ $srv->nama_layanan }}` agar tampilan tabel bersih dan terfokus pada nama paket. Standar pada `README.md` (Rule #106 poin 2) ikut diselaraskan.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #49**, **Rule #70**, dan **Rule #106**.
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear`, `php artisan view:cache`, dan `npm run build` (`vite v8.2.1`, 953ms): **PASS**.
  * Validasi keseimbangan tag `<div>` `services/index.blade.php` dan render HTTP `200` `/photographer/services` terautentikasi via HTTP Kernel: **PASS** (`6/6 PASSED`).

---

### [2026-11-12 19:33 WITA] — Penambahan Tinggi & Ruang Napas Card Empty State di Profil Publik Fotografer

* **Modul / Fitur**:
  - Card empty state (`Belum Ada Paket Layanan`, `Belum Ada Portofolio`, `Belum Ada Ulasan Klien`, `Belum Ada Tanya Jawab`, dan empty state album tanpa foto) pada halaman profil publik fotografer (`/fotografer/{id}`, [show.blade.php](resources/views/public/photographers/show.blade.php)).
* **File yang Diubah**:
  * `resources/views/public/photographers/show.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Before**:
    - Card `Belum Ada Tanya Jawab` memakai `p-6 sm:p-7` dan card empty state lain (`Belum Ada Paket Layanan`, `Belum Ada Portofolio`, `Belum Ada Ulasan Klien`) memakai `p-8 sm:p-10` tanpa tinggi minimum sehingga tampak pipih dan sempit secara vertikal pada lebar kontainer penuh `max-w-7xl`.
  * **After**:
    - Seluruh keempat card empty state tab diseragamkan memakai `px-6 py-14 sm:px-10 sm:py-16 min-h-[190px] sm:min-h-[210px] flex flex-col items-center justify-center` serta tipografi seimbang (`text-sm sm:text-base font-bold` untuk judul dan `text-xs sm:text-sm max-w-lg mx-auto` untuk subteks), sedangkan empty state album tanpa foto memakai `px-6 py-12 text-xs sm:text-sm`.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #51**, **Rule #70**, dan **Rule #100**.
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear`, `php artisan view:cache`, dan `npm run build` (`vite v8.2.1`, 834ms): **PASS**.
  * Validasi keseimbangan tag `<div>` `show.blade.php` dan HTTP `200` pada `/fotografer/6` via HTTP Kernel: **PASS** (`5/5 PASSED`).

---

### [2026-11-12 19:31 WITA] — Penyeragaman Lebar Kontainer & Anti-Slop pada Halaman Pusat Bantuan, Kebijakan Privasi, Syarat Ketentuan, serta Footer

* **Modul / Fitur**:
  - Halaman `/faq` ([faq.blade.php](resources/views/public/help/faq.blade.php)), `/kebijakan-privasi` ([privacy.blade.php](resources/views/public/help/privacy.blade.php)), `/syarat-ketentuan` ([terms.blade.php](resources/views/public/help/terms.blade.php)), controller [HelpController.php](app/Http/Controllers/Public/HelpController.php), routing [routes/web.php](routes/web.php), serta footer publik ([footer.blade.php](resources/views/partials/public/footer.blade.php)).
* **File yang Diubah**:
  * `routes/web.php`
  * `app/Http/Controllers/Public/HelpController.php`
  * `resources/views/partials/public/footer.blade.php`
  * `resources/views/public/help/faq.blade.php`
  * `resources/views/public/help/privacy.blade.php`
  * `resources/views/public/help/terms.blade.php` *(baru)*
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Before**:
    - `public/help/faq.blade.php` memakai `max-w-5xl` dan `public/help/privacy.blade.php` memakai `max-w-4xl` sehingga tidak sejajar dengan lebar navbar dan footer (`max-w-7xl mx-auto px-4 sm:px-6 lg:px-8`).
    - Rute `/syarat-ketentuan` (`public.terms`) masih diarahkan ke `HelpController@privacy` sehingga belum memiliki halaman terpisah.
    - `faq.blade.php` memuat banner gradien gelap, blok dukungan fiktif (`24/7`, `support@lensmatch.com`, tombol WhatsApp yang mengarah ke `/forum`), tab terpusat, serta garis pembatas `border-b`/`border-t` internal.
    - `privacy.blade.php` memuat tanggal statis (`15 Agustus 2026`), istilah `Escrow`/`Rekber`, teks markdown mentah `(*intellectual property*)`, typo `milikFotografer`, dan `border-b` pada judul.
    - `footer.blade.php` kolom ke-4 menggunakan judul `Bantuan & Syarat` serta label bertumpuk (`Pusat Bantuan & FAQ`, `Kebijakan Privasi & Keamanan Pembayaran`, `Syarat & Ketentuan Platform`).
  * **After**:
    - Ketiga halaman (`/faq`, `/kebijakan-privasi`, `/syarat-ketentuan`) diseragamkan menggunakan `max-w-7xl mx-auto px-4 sm:px-6 lg:px-8` agar sejajar penuh dengan navbar dan footer publik.
    - `HelpController::terms()` dan `resources/views/public/help/terms.blade.php` ditambahkan serta dihubungkan ke `route('public.terms')` (`/syarat-ketentuan`).
    - `faq.blade.php` dibersihkan menjadi header kiri ringkas, tab horizontal bergaris bawah dengan badge jumlah item (Rule #78), dan akordion murni dari `config('lensmatch_faqs.help_categories')` tanpa banner gradien, tanpa blok kontak/24/7 fiktif, dan tanpa divider internal.
    - `privacy.blade.php` dan `terms.blade.php` ditulis ringkas sesuai perilaku nyata sistem (1 email 1 akun 1 peran, verifikasi mitra, pembayaran DP 30% atau penuh, pencairan 90%/10%, pembatalan 24 jam, hak cipta karya, moderasi komunitas) tanpa `Escrow`/`Rekber`, tanggal fiktif, maupun `border-b`.
    - `footer.blade.php` kolom ke-4 disederhanakan menjadi `Bantuan` dengan tautan `Pusat Bantuan`, `Kebijakan Privasi`, dan `Syarat Ketentuan`.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #41**, **Rule #49**, **Rule #50**, **Rule #51**, **Rule #58**, **Rule #70**, **Rule #72**, **Rule #78**, **Rule #89**, **Rule #100**, dan **Rule #104**.
* **Hasil Pengujian & Verifikasi**:
  * `php -l` pada `routes/web.php` dan `HelpController.php`, `php artisan route:clear`, `php artisan view:clear`, `php artisan view:cache`, serta `npm run build` (`vite v8.2.1`, 1.04s): **PASS**.
  * Validasi keseimbangan tag `<div>` (4 view), HTTP `200` dan asersi DOM/anti-slop via HTTP Kernel pada `/faq`, `/kebijakan-privasi`, `/syarat-ketentuan`, dan `/`: **PASS** (`47/47 PASSED`).

---

### [2026-11-12 21:45 WITA] — Penyelarasan Penuh Tabel Pratinjau Dashboard Admin dengan Monitoring Transaksi (Sinkronisasi TH, TD, Sorting Deterministik, dan Status Anti-AI Slop)

* **Modul / Fitur**:
  - Tabel Pratinjau Dashboard Admin (`resources/views/partials/table/table-01.blade.php`, `resources/views/admin/dashboard.blade.php`, `app/Http/Controllers/Admin/DashboardController.php`).
  - Halaman Monitoring Transaksi Penuh (`resources/views/admin/transactions/index.blade.php`, `app/Http/Controllers/Admin/TransactionController.php`).
* **File yang Diubah**:
  * `app/Http/Controllers/Admin/TransactionController.php`
  * `app/Http/Controllers/Admin/DashboardController.php`
  * `resources/views/partials/table/table-01.blade.php`
  * `resources/views/admin/transactions/index.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Before**:
    - **Perbedaan Kolom TH & TD**: Tabel pratinjau dashboard (`table-01.blade.php`) menggunakan struktur kolom yang sama sekali tidak serasi dengan tabel aslinya (`admin/transactions/index.blade.php`). Di preview terdapat kolom `Paket & Fotografer` (menggunakan simbol `&` alay), `Kategori Sesi`, `Total Biaya`, dan `Status Transaksi`, sementara tabel aslinya memiliki kolom `Invoice`, `Klien`, `Fotografer`, `Rincian Nominal`, `Status Pembayaran`, dan `Aksi`.
    - **Inkonsistensi Urutan Data (Sorting Flaw)**: Kontrak ID 16 dan ID 17 memiliki timestamp `created_at` yang identik (`2026-11-06 13:09:27`). Tanpa kolom penentu sekunder (`tie-breaker`), MySQL mengembalikan ID 17 duluan di preview dashboard, namun memunculkan ID 16 duluan di halaman Monitoring Transaksi.
    - **Inkonsistensi Status Pembayaran**: Kontrak ID 12 dan 13 yang belum dibayar (`status_contract = pending_payment`) tampil sebagai "Menunggu Pembayaran" di preview dashboard, tetapi tampil sebagai "Dana Ditahan" di tabel monitoring transaksi karena tabel monitoring hanya memeriksa status payout tanpa mengecek status kontrak `pending_payment`.
    - **Judul Berpola AI Slop**: Judul preview tabel menggunakan kata redundan bersimbol `&` (`Transaksi Booking & Verifikasi Terkini`) dan subtitle menggunakan jargon buzzword AI (`Monitoring pemesanan sesi foto & status pembayaran real-time`).
  * **After**:
    - **Penyelarasan Mutlak TH & TD**: Struktur kolom tabel preview pada dashboard diselaraskan 100% dengan tabel aslinya:
      `Invoice` | `Klien` | `Fotografer` | `Rincian Nominal` | `Status Pembayaran` | `Aksi`
      Lengkap dengan format TD yang identik (kode invoice & tanggal, nama & email klien, fotografer & nama layanan, rincian nominal total/komisi/pendapatan bersih, dan tombol aksi `Lihat` murni teks amber).
    - **Pengurutan Deterministik (Tie-Breaker ID)**: Menambahkan `->orderBy('id', 'desc')` setelah `orderBy('created_at', 'desc')` pada `TransactionController.php`, `DashboardController.php`, dan query `table-01.blade.php`, memastikan urutan transaksi selalu konsisten dan deterministik di seluruh sistem.
    - **Koreksi & Sinkronisasi Logika Status**: Memperbaiki pengecekan status pada `admin/transactions/index.blade.php` dan modal detailnya untuk memprioritaskan `pending_payment` sebagai teks kuning amber `"Menunggu Pembayaran"` dan membersihkan teks label `"Refund Transaksi"` menjadi murni `"Refund"` sesuai Rule #14 & Rule #70.
    - **Pembersihan Copywriting Anti-AI Slop**: Judul tabel preview disederhanakan menjadi `"Transaksi Booking Terkini"` dan deskripsi menjadi `"Monitoring pemesanan sesi foto dan pembayaran terbaru"` tanpa simbol `&` dan tanpa kata alay (Rule #51 & Rule #70).
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #10 & #13** (Tombol Aksi Murni Teks Tanpa Icon), **Rule #14 & #68** (Status Pembayaran Teks Murni Berwarna Netral), **Rule #51** (Anti-AI Slop Copywriting), **Rule #69** (Standarisasi Aksi Tabel), **Rule #70** (Larangan Simbol '&', No Buzzwords / Payout Jargon), dan **Rule #94** (100% Data Dinamis).
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear` & `php artisan view:cache`: **PASS** (100% compiled & cached).
  * Uji Tinker Sorting Konsistensi: Row 1-6 terbukti 100% identik antara preview table query dan pagination controller (ID 17 -> 16 -> 13 -> 12 -> 11 -> 10).

---

### [2026-11-12 21:05 WITA] — Standarisasi Compact Status Chips, Koreksi Sinkronisasi Data KPI, dan Eliminasi Visual Dummy pada Dashboard Fotografer

* **Modul / Fitur**:
  - Dashboard Mitra Fotografer (`/photographer/dashboard`, [DashboardController.php](app/Http/Controllers/Photographer/DashboardController.php), [resources/views/photographer/dashboard.blade.php](resources/views/photographer/dashboard.blade.php)).
* **File yang Diubah**:
  * `app/Http/Controllers/Photographer/DashboardController.php`
  * `resources/views/photographer/dashboard.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Before**:
    - Bagian atas 4 KPI card menampilkan 3 kartu horizontal besar pola AI slop lama dengan icon kuning pastel dan istilah terlarang Rule #70 (`Payout Dalam Proses`).
    - Label pada KPI Card ke-4 tertulis `Galeri Portofolio Aktif`, namun menampilkan nilai `$totalServices Paket` (Paket Layanan), menciptakan ketidaksinkronan data dan label.
    - Grafik batang omzet fotografer memaksakan tinggi bar minimal 12% (`max(12, ...)`) sehingga bulan yang omzetnya Rp 0 tetap digambar memiliki penjualan secara fiktif (*fake visual fallback*).
    - Persentase pesanan paket layanan di-hardcode dibagi rata (misal 50% - 50%) saat belum ada booking sama sekali.
    - Judul dan heading memuat penggabungan kata dengan simbol `&` redundan (`Reputasi & Metrik Keunggulan`, `Order Booking Masuk Terkini`, `Daftar tarif & porsi penawaran aktif`).
  * **After**:
    - **Compact Status Chips**: Mengubah 3 kartu besar menjadi baris ringkas teks murni `Antrean Kerja`:
      `[ Konfirmasi Order (count) ]` `[ Kirim Hasil Foto (count) ]` `[ Pencairan Dana (count) ]`
      tanpa boks abu pada angka dan mengganti kata *Payout* menjadi *Pencairan Dana* sesuai Rule #70.
    - **Sinkronisasi Data KPI**: Memperbaiki label KPI Card ke-4 menjadi `Total Paket Layanan` dengan nilai `{{ $totalServices }}` murni angka tanpa akhiran redundan (Rule #64).
    - **Eliminasi Batang Grafik Fiktif**: Skala grafik diselaraskan dengan `$chartMax = max($maxMonthlyRevenue, 1)`. Bulan dengan omzet Rp 0 murni bernilai 2% (base stub), tidak lagi dipalsukan setinggi 12% (Rule #94 & #104).
    - **Porsi Layanan 100% Riil**: Menghilangkan pembagian rata fiktif saat 0 booking (murni bernilai 0%).
    - **Pembersihan Copywriting Anti-AI Slop**: Judul seksi disederhanakan menjadi `Pencapaian Reputasi`, `Tren Pendapatan Bulanan`, `Paket Layanan`, dan `Order Masuk Terkini` (Rule #51 & #70).
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #22** (Strict Icon Constraint), **Rule #49** (Strict Scope), **Rule #51** (Anti-AI Slop Copywriting), **Rule #64** (KPI Metric Cards Preserved), **Rule #70** (Natural Human Text, No '&', No Payout Jargon), **Rule #94** (100% Dynamic Data), serta **Rule #104** (Nullable Safe Data).
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear` & `php artisan view:cache`: **PASS** (Zero errors).
  * Uji Render Controller Fotografer: `Photographer\DashboardController::index()` **PASS** (`RENDER SUCCESS: 108348` bytes).

---

### [2026-11-12 20:15 WITA] — Eliminasi Total Hardcoded Data, Skala Dinamis Grafik Batang, dan Pemurnian Tipografi Status Chips Dashboard Admin

* **Modul / Fitur**:
  - Dashboard Utama Administrator (`/admin`, [DashboardController.php](app/Http/Controllers/Admin/DashboardController.php), [resources/views/admin/dashboard.blade.php](resources/views/admin/dashboard.blade.php)).
* **File yang Diubah**:
  * `app/Http/Controllers/Admin/DashboardController.php`
  * `resources/views/admin/dashboard.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Before**:
    - Angka antrean kerja pada status chips dibungkus kotak abu-abu kaku (`bg-gray-100`) di dalam pill.
    - Seksi *Kategori Sesi Terlaris* memuat badge hardcoded statis `Wedding & Prewedding (54%)` dengan border dan background, padahal data pesanan riil database saat ini dimenangkan oleh kategori `Studio & Portrait (53%)`.
    - Grafik batang omzet membagi nilai dengan angka statis `75`, membuat visual grafik tampak kerdil/ceper (~15% canvas) karena omzet tertinggi riil adalah Rp 11,9 Juta.
    - Judul grafik memuat penggabungan sinonim AI-slop `Tren Omzet & Transaksi Bulanan`, jargon asing `Gross Merchandise Value (GMV)`, serta legend abu-abu fiktif `Komisi Platform (10%)` tanpa adanya visual bar batang di diagram.
  * **After**:
    - **Anti-Card Inner Numbers**: Menghapus kotak abu-abu pembungkus nomor pada chips antrean kerja, menampilkan angka murni dalam kurung `Verifikasi Fotografer (0)` yang bersih dan to-the-point.
    - **Pure Dynamic Text Pangsa Pasar**: Menghapus badge boks pada kategori terlaris, menggantikannya dengan teks murni dinamis `Kategori terbanyak: Studio & Portrait (53%)` yang dihitung langsung dari urutan kontrak tertinggi di database (Rule #94 & #104).
    - **Dynamic Chart Scale**: Skala tinggi batang grafik dihitung proporsional terhadap nilai omzet bulanan tertinggi riil di database (`$chartMax = max($maxMonthlyRevenue, 1)`), sehingga visual grafik tampil proporsional dan dinamis.
    - **Copywriting Bersih & Anti-AI Slop**: Judul disederhanakan menjadi `Tren Omzet Bulanan` tanpa simbol `&` yang mubazir, subtitle diubah ke bahasa Indonesia wajar `Total transaksi per bulan dalam juta rupiah` tanpa jargon GMV, serta menyelaraskan legend murni pada `Total Omzet` (Rule #51 & #70).
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #22** (Strict Icon Constraint), **Rule #49** (Strict Scope), **Rule #51** (Anti-AI Slop Copywriting), **Rule #70** (Natural Human Text & No '&'), **Rule #94** (100% Dynamic Data), serta **Rule #104** (Nullable Safe Data).
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear` & `php artisan view:cache`: **PASS** (Zero errors).
  * Simulasi render `DashboardController::index()`: **PASS** (`RENDER SUCCESS: 107271` bytes, `Kategori terbanyak: Studio & Portrait (53%)` ter-render dinamis dan akurat).

---

### [2026-11-12 20:05 WITA] — Refaktorisasi UI Antrean Operasional Dashboard Admin Menjadi Compact Status Chips (Shopify Polaris Inspired)

* **Modul / Fitur**:
  - Dashboard Utama Administrator (`/admin`, [resources/views/admin/dashboard.blade.php](resources/views/admin/dashboard.blade.php)).
* **File yang Diubah**:
  * `resources/views/admin/dashboard.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Before**:
    - Bagian atas 4 KPI card menampilkan 3 kartu horizontal berukuran besar dengan icon lingkaran kuning pastel, font kecil, dan panah chevron kanan (`>`) statis (`0 Verifikasi Fotografer`, `0 Dana Siap Tarik`, `0 Submisi Lomba`).
    - Desain tersebut memakan ruang vertikal secara berlebihan, terkesan template generik/AI slop, serta tidak fungsional saat angka antrean `0`.
  * **After**:
    - Merefaktor 3 kartu menjadi baris horizontal ringkas berisi **Compact Status Chips / Action Pills** (terinspirasi oleh Shopify Polaris):
      - `[ Antrean Kerja: ]`
      - `[ Verifikasi Fotografer (count) ]`
      - `[ Dana Siap Tarik (count) ]`
      - `[ Submisi Lomba (count) ]`
    - Menggunakan teks murni (*pure text*) tanpa icon bloat sesuai **Rule #22**, badge jumlah antrean ber-kontras tinggi, serta status dinamis (netral abu-abu saat `0`, beraksen warna semantik amber/emerald/blue saat antrean `> 0`).
    - Menghemat ruang vertikal layar sehingga 4 KPI Cards utama (`Total User Registrasi`, `Fotografer Terverifikasi`, `Total Kontrak Booking`, `Total Omzet Transaksi`) langsung terlihat jelas dan proporsional.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #22** (Strict Icon Constraint: Pure Text Default), **Rule #49** (Strict Scope), **Rule #51** (Anti-AI Slop & Human-Crafted Microcopy), **Rule #61** (Hierarki Warna Sistem), serta **Rule #64** (KPI Metric Cards Preserved).
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear` & `php artisan view:cache`: **PASS** (Zero errors).
  * Struktur Blade dan dark mode classes terverifikasi rapi dan responsif.

---

### [2026-11-12 16:50 WITA] — Pembatasan Visibilitas Tombol Preview (`Lihat Lebih Banyak` / `Lihat Semua`) Berdasarkan Batas Jumlah Data

* **Modul / Fitur**:
  - Preview katalog modul pada `/elearning` ([elearning/index.blade.php](resources/views/public/elearning/index.blade.php)), preview fotografer pada `/fotografer` ([photographers/index.blade.php](resources/views/public/photographers/index.blade.php)), beranda `/` ([home.blade.php](resources/views/public/home.blade.php)), dashboard fotografer ([photographer/dashboard.blade.php](resources/views/photographer/dashboard.blade.php)), serta tabel transaksi terbaru dashboard admin ([table-01.blade.php](resources/views/partials/table/table-01.blade.php)).
* **File yang Diubah**:
  * `resources/views/public/elearning/index.blade.php`
  * `resources/views/public/photographers/index.blade.php`
  * `resources/views/public/home.blade.php`
  * `resources/views/photographer/dashboard.blade.php`
  * `resources/views/partials/table/table-01.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Before**:
    - Tombol CTA preview (`Lihat Lebih Banyak Modul` di `/elearning`, `Lihat Lebih Banyak Fotografer` di `/fotografer`, `Lihat Katalog Lengkap (150+)` beserta kartu `Lihat Semua Kategori` di `/`, `Lihat Semua` di `Order Booking Masuk Terkini` dashboard fotografer, serta `Lihat Semua` di `partials/table/table-01.blade.php`) dirender tanpa syarat sehingga tetap tampil meskipun data kosong atau kurang dari batas maksimal preview UI.
    - Tautan katalog di beranda `/` masih memuat angka hardcoded palsu `(150+)` serta mengarah ke `/fotografer` alih-alih `route('public.photographers.katalog')`, dan grid fotografer di `/` maupun `/fotografer` belum memiliki empty state.
  * **After**:
    - `/elearning`: tombol `Lihat Lebih Banyak Modul` hanya dirender saat `@if($totalCourses > 6)`.
    - `/fotografer`: tombol `Lihat Lebih Banyak Fotografer` hanya dirender saat `@if($totalPhotographers > 4)` dan loop kartu menggunakan `@forelse` dengan empty state ringkas `Belum Ada Fotografer`.
    - `/` (Beranda): kartu `Lihat Semua Kategori` hanya dirender saat `@if($categories->count() > 7)`; tautan `Lihat Katalog Lengkap` hanya dirender saat `@if($totalPhotographers > 4)` tanpa angka palsu `(150+)`, mengarah ke `route('public.photographers.katalog')`, disertai empty state `@forelse` pada grid fotografer.
    - Dashboard Fotografer serta `table-01.blade.php`: tautan `Lihat Semua` masing-masing dibungkus kondisi `@if(($totalBookingsCount ?? 0) > 5)` serta `@if($totalContractsCount > 6)`.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #49**, **Rule #51**, **Rule #70**, **Rule #94**, serta **Rule #104**.
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear`, `php artisan view:cache` (PHP 8.4.24), serta `npm run build` (`vite v8.2.1`, 801ms): **PASS**.
  * Validasi keseimbangan tag `<div>` pada kelima view serta 30 asersi HTTP Kernel (kondisi live DB 0/6/13/16 serta simulasi batas rolled-back `6` vs `7` modul E-Learning, `4` vs `0` fotografer, `7` kategori, dan `6` kontrak admin): **PASS** (`30/30 PASSED`).

---

### [2026-11-12 16:43 WITA] — Isolasi Data Klien Seeder & Logika Otomatis "Fotografer Pilihan Bulan Ini" Berbasis Performa

* **Modul / Fitur**:
  - Seeder akun pengguna, kontrak booking, invoice, ulasan, notifikasi, & submisi lomba ([UserSeeder.php](database/seeders/UserSeeder.php), [BookingContractSeeder.php](database/seeders/BookingContractSeeder.php), [ContestSubmissionSeeder.php](database/seeders/ContestSubmissionSeeder.php), [ActivityLogSeeder.php](database/seeders/ActivityLogSeeder.php)).
  - Seksi `Fotografer Pilihan Bulan Ini` pada halaman Beranda (`/`, [routes/web.php](routes/web.php)) serta halaman utama Fotografer (`/fotografer`, [PhotographerController.php](app/Http/Controllers/Public/PhotographerController.php), [index.blade.php](resources/views/public/photographers/index.blade.php)).
* **File yang Diubah**:
  * `database/seeders/UserSeeder.php`
  * `database/seeders/BookingContractSeeder.php`
  * `database/seeders/ContestSubmissionSeeder.php`
  * `database/seeders/ActivityLogSeeder.php`
  * `app/Models/RolePhotographer.php`
  * `app/Http/Controllers/Public/PhotographerController.php`
  * `routes/web.php`
  * `resources/views/public/photographers/index.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Before**:
    - `UserSeeder.php` hanya membuat 1 akun klien (`dian@gmail.com`) sedangkan `BookingContractSeeder.php` menulis fallback `?? $clientDian` untuk 5 klien fiktif lain (`mahkota@event.com`, `bulilik@kuliner.com`, `reza.itb@gmail.com`, `siti.rahma@gmail.com`, `finance@baliluxury.com`), sehingga seluruh kontrak, tagihan invoice, ulasan, galeri, dan notifikasi klien lain masuk ke akun Dian & Febri (selain itu terdapat bug kelebihan kurung kurawal di akhir `BookingContractSeeder.php`).
    - `ContestSubmissionSeeder.php` memakai ID user hardcode (`9`, `11`, `12`) yang berisiko menempel ke ID akun keliru di database (ID `9` di live DB adalah fotografer `kenji`).
    - `Fotografer Pilihan Bulan Ini` pada `routes/web.php` serta `PhotographerController::index()` hanya memanggil `RolePhotographer::published()->...->take(4)->get()` tanpa pengurutan performa.
  * **After**:
    - Menambahkan seluruh 6 akun klien terpisah di `UserSeeder.php`, menghapus fallback silang `?? $clientDian` di `BookingContractSeeder.php` menggunakan helper `$resolveClient()` terisolasi per email (sekarang `dian@gmail.com` hanya memiliki 5 kontrak, 2 galeri selesai, serta 4 notifikasi miliknya sendiri), memperbaiki kurung kurawal penutup di `BookingContractSeeder.php`, serta mengganti lookup ID hardcode di `ContestSubmissionSeeder.php` menjadi lookup `email` dan membersihkan terminologi Rule #70/#89 di `BookingContractSeeder.php` / `ActivityLogSeeder.php`.
    - Menambahkan `RolePhotographer::scopeFeaturedThisMonth()` yang mengurutkan fotografer `published()` secara otomatis berdasarkan: (1) sesi `completed` tervalidasi klien pada bulan berjalan terbanyak, (2) ulasan positif (`rating >= 4`) bulan berjalan terbanyak, lalu fallback otomatis ke (3) `testimonials_avg_rating` tertinggi, (4) `testimonials_count` terbanyak, dan (5) total sesi `completed` tervalidasi klien terbanyak. Diterapkan seragam di `routes/web.php` maupun `PhotographerController::index()`, sekaligus merapikan subjudul seksi pada `public/photographers/index.blade.php`.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #70**, **Rule #89**, **Rule #94**, **Rule #103**, dan **Rule #104**.
* **Hasil Pengujian & Verifikasi**:
  * `php -l` seluruh file PHP & seeder terkait: **PASS**.
  * Eksekusi `UserSeeder`, `BookingContractSeeder`, `ContestSubmissionSeeder` serta `php artisan view:clear`, `php artisan view:cache`, `npm run build` (`vite v8.2.1`, 808ms): **PASS**.
  * 34 asersi isolasi data klien di `/client/bookings`, `/client/invoices`, `/client/galleries`, notifikasi Dian, keutuhan akun `kenji`, urutan otomatis `featuredThisMonth()` (baik mode fallback maupun simulasi transaksi rolled-back bulan berjalan), serta keseimbangan tag `<div>`: **PASS** (`34/34 PASSED`).

---

### [2026-11-12 16:34 WITA] — Penyeragaman Metrik Numerik `0` Fotografer Tanpa Ulasan/Sesi (Eliminasi Label `Mitra Baru` & `Baru`)

* **Modul / Fitur**:
  - Model metrik editorial fotografer (`RolePhotographer::getEditorialMetricsAttribute()`), badge rating pojok kanan atas kartu katalog/beranda/admin, halaman detail profil fotografer (`/fotografer/{id}`), daftar favorit klien (`/client/favorites`), serta pratinjau metrik di Dashboard Fotografer.
* **File yang Diubah**:
  * `app/Models/RolePhotographer.php`
  * `app/Http/Controllers/Public/PhotographerController.php`
  * `resources/views/public/photographers/katalog.blade.php`
  * `resources/views/public/photographers/index.blade.php`
  * `resources/views/public/photographers/show.blade.php`
  * `resources/views/public/home.blade.php`
  * `resources/views/client/favorites/index.blade.php`
  * `resources/views/photographer/dashboard.blade.php`
  * `resources/views/admin/dashboard.blade.php`
  * `README.md`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Before**: Fotografer yang belum memiliki ulasan maupun sesi selesai (misalnya `kenji`) menampilkan teks `Mitra Baru` di baris metrik kartu katalog/favorit, badge `Baru` di pojok kanan atas cover foto, menyembunyikan jumlah sesi selesai, serta menampilkan angka rating kosong (`null`) di samping bintang pada `/fotografer/{id}`.
  * **After**:
    - `RolePhotographer::getEditorialMetricsAttribute()` selalu menghasilkan struktur metrik seragam berbasis data nyata, yakni `0.0 (0 ulasan)` dan `0 Sesi Selesai` ketika belum ada ulasan maupun kontrak selesai (menggantikan `Mitra Baru` serta pengosongan sesi).
    - Badge rating pojok kanan atas kartu (`katalog.blade.php`, `photographers/index.blade.php`, `home.blade.php`) dan `admin/dashboard.blade.php` kini seragam menampilkan `★ 0 (0)` / `★ 0` dengan ikon bintang abu-abu redup (`text-gray-300 dark:text-gray-600`) ketika belum ada ulasan, menggantikan teks `Baru`.
    - `PhotographerController::show()` mengembalikan `'0.0'` ketika total ulasan `0` agar baris metrik atas (`★ 0.0 (0 ulasan) • 0 Sesi Selesai`) maupun tab Ulasan Klien di `show.blade.php` menampilkan angka `0.0` yang konsisten dengan bintang abu-abu redup.
    - Menyelaraskan dokumentasi **Rule #94 Poin 3** di `README.md` agar standar metrik nol konsisten di seluruh sistem.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #70**, **Rule #92**, **Rule #94**, serta **Rule #103**.
* **Hasil Pengujian & Verifikasi**:
  * `php -l` (`RolePhotographer.php`, `PhotographerController.php`), `php artisan view:clear`, `php artisan view:cache`, serta `npm run build` (`vite v8.2.1`, 886ms): **PASS**.
  * Audit grep lintas `app/` dan `resources/views/` (nol sisa `Mitra Baru` / `>Baru<`), keseimbangan tag `<div>` pada 7 Blade view, serta 38 asersi HTTP Kernel & model (termasuk perbandingan langsung `kenji` vs `Dewa Portrait Studio`): **PASS** (`38/38 PASSED`).

---

### [2026-11-12 16:22 WITA] — Penghapusan Ikon Centang Kuning (`Mitra Terverifikasi`) di Samping Nama Fotografer

* **Modul / Fitur**:
  - Header profil publik fotografer (`/fotografer/{id}`, [show.blade.php](resources/views/public/photographers/show.blade.php)) serta kartu daftar fotografer favorit klien (`/client/favorites`, [index.blade.php](resources/views/client/favorites/index.blade.php)).
* **File yang Diubah**:
  * `resources/views/public/photographers/show.blade.php`
  * `resources/views/client/favorites/index.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Before**: Nama fotografer pada `/fotografer/{id}` serta `/client/favorites` didampingi ikon centang kuning (`✓`, `title="Mitra Terverifikasi ..."`) padahal seluruh fotografer yang tayang di katalog publik memang wajib sudah terverifikasi Admin, sehingga ikon tersebut tidak memberi informasi pembeda.
  * **After**: Menghapus elemen ikon centang kuning (`@if($photographer->is_verified)`) beserta pembungkus flex tambahan di samping nama fotografer pada `public/photographers/show.blade.php` maupun `client/favorites/index.blade.php` agar judul nama tampil bersih.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #22**, **Rule #49**, **Rule #51**, **Rule #67**, dan **Rule #103**.
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear`, `php artisan view:cache` (PHP 8.4.24), serta `npm run build` (`vite v8.2.1`, 814ms): **PASS**.
  * Validasi keseimbangan tag `<div>` serta uji render HTTP Kernel pada `/fotografer/{id}` dan `/client/favorites`: **PASS** (`ALL_CHECKS_PASSED`, `14` asersi sukses).

---

### [2026-11-12 16:20 WITA] — Penyeragaman Ikon `Dashboard` pada Dropdown Navbar Publik Seluruh Role (`Admin`, `Super Admin`, serta `Fotografer`)

* **Modul / Fitur**:
  - Dropdown akun pengguna pada navbar publik ([navbar.blade.php](resources/views/partials/public/navbar.blade.php)) lintas role `admin`, `super_admin`, `photographer`, serta pemeriksaan keselarasan role `client`.
* **File yang Diubah**:
  * `resources/views/partials/public/navbar.blade.php`
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Before**:
    - Menu `Dashboard` untuk role `admin` serta `super_admin` di `partials/public/navbar.blade.php` memakai ikon perisai centang (`M9 12l2 2 4-4...`), berbeda dari ikon grid `Dashboard` 4 kotak bersudut membulat (`squares-2x2`) pada `partials/admin/sidebar.blade.php`.
    - Menu `Dashboard` untuk role `photographer` di `partials/public/navbar.blade.php` masih memakai variasi Heroicons v1 (`M4 6a2 2...`, `stroke-width="2"`), sedikit berbeda bentuk sudut dengan `partials/photographer/sidebar.blade.php` (`M3.75 6A2.25...`, `stroke-width="1.75"`).
  * **After**:
    - Menyeragamkan ikon SVG `Dashboard` pada cabang `photographer` serta cabang `admin`/`super_admin` di `partials/public/navbar.blade.php` menjadi SVG `squares-2x2` identik dengan `partials/admin/sidebar.blade.php` maupun `partials/photographer/sidebar.blade.php` (`M3.75 6A2.25...`, `stroke-width="1.75"`).
    - Memeriksa menu role `client` pada `partials/public/navbar.blade.php`: klien menggunakan layout publik tanpa link `Dashboard` di dropdown, seluruh 6 menu klien serta tombol keluar sudah memakai ikon SVG yang sesuai fungsinya.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #21**, **Rule #49**, **Rule #51**, serta **Section 7 Poin 6** (Standar Ikon Vektor SVG Sistem).
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear`, `php artisan view:cache` (PHP 8.4.24), serta `npm run build` (`vite v8.2.1`, 883ms): **PASS**.
  * Audit grep lintas seluruh `resources/views/**/*.blade.php` memastikan nol sisa ikon perisai centang (`M9 12l2 2 4-4`) maupun ikon `M4 6a2 2` di navbar: **PASS**.
  * Uji render langsung halaman `/` via HTTP Kernel untuk keempat role (`client`, `photographer`, `admin`, `super_admin`) termasuk pengecekan keseimbangan tag `<div>` header ter-render: **PASS** (`ALL_CHECKS_PASSED`, `26` asersi sukses).

---

### [2026-11-12 16:16 WITA] — Penggantian Klaim `1+ Tahun Pengalaman` Menjadi `Sesi Selesai` Nyata pada Profil Publik Fotografer

* **Modul / Fitur**:
  - Halaman detail profil publik fotografer (`/fotografer/{id}`, [show.blade.php](resources/views/public/photographers/show.blade.php) serta [PhotographerController.php](app/Http/Controllers/Public/PhotographerController.php)).
* **File yang Diubah**:
  * `app/Http/Controllers/Public/PhotographerController.php`
  * `resources/views/public/photographers/show.blade.php`
  * `README.md` (`Rule #103`)
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Before**: Baris informasi di bawah spesialisasi profil publik fotografer menampilkan klaim `1+ Tahun Pengalaman` yang dipaksa dari `max(1, (int) now()->diffInYears($photographer->created_at))`, sehingga fotografer yang baru mendaftar tetap dianggap berpengalaman 1 tahun ke atas meski bukan data asli.
  * **After**:
    - Menghitung `$completedSessionsCount` di `PhotographerController::show()` dari kontrak yang benar-benar selesai serta sudah dikonfirmasi klien (`status_contract === 'completed'` dengan `is_validated_client === true`).
    - Menampilkan **`{jumlah} Sesi Selesai`** pada [show.blade.php](resources/views/public/photographers/show.blade.php) menggantikan `1+ Tahun Pengalaman`, serta memperbarui **Rule #103** di `README.md` selaras **Rule #90** dan **Rule #104**.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #49**, **Rule #51**, **Rule #70**, **Rule #90**, **Rule #103**, dan **Rule #104**.
* **Hasil Pengujian & Verifikasi**:
  * `php -l app/Http/Controllers/Public/PhotographerController.php`, `php artisan view:clear`, `php artisan view:cache` (PHP 8.4.24), serta `npm run build` (`vite v8.2.1`, 870ms): **PASS**.
  * Validasi keseimbangan tag `<div>` (`public/photographers/show.blade.php`: `121/121`): **PASS**.
  * Uji render HTTP Kernel pada `/fotografer/{id}` dengan `DB::beginTransaction` + `DB::rollBack()` (verifikasi metrik `Sesi Selesai` hanya bertambah untuk order `status_contract = 'completed'` dengan `is_validated_client = true`, tidak menghitung order belum tervalidasi maupun masih aktif): **PASS** (`5/5` asersi sukses).

---

### [2026-11-12 15:47 WITA] — Penyederhanaan Ringkasan Status Akun pada Detail Verifikasi Fotografer Admin (`/admin/photographers/{id}`)

* **Modul / Fitur**:
  - Kartu ringkasan status di atas tab pada halaman detail verifikasi mitra fotografer Admin ([show.blade.php](resources/views/admin/photographers/show.blade.php)).
* **File yang Diubah**:
  * `resources/views/admin/photographers/show.blade.php`
  * `README.md` (`Rule #95`)
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Before**: Kartu ringkasan status mengulang informasi kelengkapan profil hingga tiga kali dalam area yang berdekatan (`Kelengkapan Profil (4/4 bagian)` di kiri, `100% Lengkap` di kanan, serta pill `100%` pada tab `Profil Mitra` tepat di bawahnya), mencampurkan tanggal `Terdaftar` ke deretan status, memunculkan pembatas `border-t` di atas daftar field kurang, serta tidak konsisten memberi awalan `Profil:` vs `Etalase:`.
  * **After**:
    - Menyederhanakan baris utama menjadi dua status jelas di sisi kiri (**`Profil:`** `Terverifikasi` / `Menunggu` / `Ditolak` `/` **`Etalase:`** `Tayang` / `Menunggu` / `Ditolak` / `Belum Diajukan`) dengan tanggal **`Terdaftar`** di sisi kanan.
    - Menghapus teks duplikat `Kelengkapan Profil (X/4 bagian)` serta `100% Lengkap` (termasuk variabel `$sectionStatus` yang tidak lagi terpakai) karena persentase kelengkapan sudah tampil di tab `Profil Mitra`.
    - Saat data profil belum 100%, menampilkan satu baris ringkas tanpa garis pembatas (`Belum diisi (X data): ...`). Memperbarui **Rule #95** di `README.md`.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #41**, **Rule #49**, **Rule #51**, **Rule #61**, **Rule #67**, **Rule #70**, dan **Rule #95**.
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear`, `php artisan view:cache` (PHP 8.4.24), serta `npm run build` (`vite v8.2.1`, 868ms): **PASS**.
  * Validasi keseimbangan tag `<div>` (`admin/photographers/show.blade.php`: `129/129`): **PASS**.
  * Uji render langsung via HTTP Kernel (`DB::beginTransaction` dengan `DB::rollBack()`) untuk status `Terverifikasi`/`Tayang`, `Menunggu`, serta `Ditolak` dengan field kurang: **PASS** (`11/11` asersi sukses).

---

### [2026-11-12 14:51 WITA] — Penambahan Indikator Teks `Tayang di Katalog` pada Header Dashboard serta Profil Fotografer

* **Modul / Fitur**:
  - Header halaman `Dashboard Fotografer` (`/photographer/dashboard`) serta halaman `Kelola Profil` (`/photographer/profile`).
* **File yang Diubah**:
  * `resources/views/photographer/dashboard.blade.php`
  * `resources/views/photographer/profile/index.blade.php`
  * `README.md` (`Rule #108`)
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Before**: Setelah Admin menyetujui penayangan etalase (`isPublishedToCatalog() === true`), `stage-stepper` maupun kartu `Persiapan Tayang Etalase` disembunyikan secara otomatis sehingga di sisi kanan judul `Kelola Profil` kosong tanpa keterangan tayang maupun tautan ke halaman publik, serta di header kanan `Dashboard` hanya muncul chip `Tahun Operasional`.
  * **After**:
    - Menambahkan teks hijau murni **`Tayang di Katalog`** (`text-xs font-bold text-emerald-600 dark:text-emerald-400`) di sisi kanan header `Dashboard` di samping `Tahun Operasional` ketika `$photographer->isPublishedToCatalog()` bernilai `true`.
    - Menambahkan teks **`Tayang di Katalog`** beserta pemisah tipis `/` dan tautan **`Lihat Profil Publik`** (`route('public.photographers.show', $photographer->id)`) di sisi kanan header `Kelola Profil` pada kondisi yang sama, tanpa kotak banner tambahan, tanpa ikon ekstra (**Rule #22**), tanpa dot badge (**Rule #67**), serta tanpa simbol `&` redundan (**Rule #70**).
    - Mendokumentasikan standar tersebut ke **Rule #108 Point 4** di `README.md`.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #22**, **Rule #49**, **Rule #51**, **Rule #61**, **Rule #67**, **Rule #70**, dan **Rule #108**.
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear`, `php artisan view:cache` (PHP 8.4.24), serta `npm run build` (`vite v8.2.1`, 867ms): **PASS**.
  * Validasi keseimbangan tag `<div>` (`dashboard.blade.php`: `100/100`; `profile/index.blade.php`: `69/69`): **PASS**.
  * Uji render langsung via HTTP Kernel (`DB::beginTransaction` dengan `DB::rollBack()`) pada `/photographer/dashboard` serta `/photographer/profile` saat status tayang aktif dan saat dicabut (`belum_diajukan`): **PASS** (`16/16` asersi sukses).

---

### [2026-11-12 13:47 WITA] — Perbaikan Radio Button Amber Global, Pembersihan Halaman `Atur Tanggal Libur`, serta Penyederhanaan Stepper Fotografer

* **Modul / Fitur**:
  - Formulir `Atur Tanggal Libur` (`/photographer/availability/create`), partial `stage-stepper.blade.php`, master layout fotografer (`layouts/photographer.blade.php`), gaya dasar CSS (`resources/css/app.css`), serta input radio/checkbox pada `public/forum/show.blade.php`, `public/booking/create.blade.php`, `public/auth/signin.blade.php`, `public/auth/signup.blade.php`.
* **File yang Diubah**:
  * `resources/css/app.css`
  * `resources/views/photographer/availability/create.blade.php`
  * `resources/views/layouts/photographer.blade.php`
  * `resources/views/partials/photographer/stage-stepper.blade.php`
  * `resources/views/public/forum/show.blade.php`
  * `resources/views/public/booking/create.blade.php`
  * `resources/views/public/auth/signin.blade.php`
  * `resources/views/public/auth/signup.blade.php`
  * `README.md` (`Rule #108`)
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Before**:
    1. Tanpa `@tailwindcss/forms` pada Tailwind CSS v4, kelas `text-amber-500` pada `<input type="radio">` maupun `<input type="checkbox">` diabaikan sehingga browser menampilkan warna biru bawaan OS Windows (`#0075FF`), khususnya pada kartu pilihan `Libur Seharian` di `/photographer/availability/create`.
    2. `stage-stepper.blade.php` tetap dirender di atas halaman form sekunder (`create`/`edit`), menampilkan dua baris breadcrumb bertumpuk di atas formulir `Atur Tanggal Libur`, memuat baris ceklist mikro duplikat (`Paket Layanan: ... Portofolio: ... Jadwal: ...`), menampilkan paragraf merah mentah `Catatan Admin`, serta menganggap rute `photographer.availability.create` sebagai tab mingguan sehingga memunculkan teks `"Tentukan hari buka lalu simpan jam operasional"`.
    3. Halaman `availability/create.blade.php` memakai tombol `Kembali ke Kalender` berwarna amber lembut yang melanggar **Rule #71**, breadcrumb berwarna aktif yang melanggar **Rule #62**, pembatas horizontal `border-t` di bawah kartu pilihan, placeholder contoh panjang di textarea, serta tautan kembali yang tidak membawa query `tab=overrides`.
  * **After**:
    1. Menambahkan `accent-color: #f59e0b;` secara global di `@layer base` `resources/css/app.css`, menambahkan kelas `accent-amber-500` pada radio/checkbox asli di `public/forum/show.blade.php`, `public/booking/create.blade.php`, `public/auth/signin.blade.php`, `public/auth/signup.blade.php`, serta mengganti radio kartu pada `photographer/availability/create.blade.php` dengan `sr-only peer` + lingkaran indikator Tailwind beraksen Amber (`border-amber-500` + `bg-amber-500`) sesuai `public/booking/create.blade.php`.
    2. Mengecualikan rute `photographer.*.create` maupun `photographer.*.edit` dari `stage-stepper` pada `layouts/photographer.blade.php`, memperbaiki logika `$isWeeklyTab` pada `stage-stepper.blade.php`, dan mengembalikan tampilan `stage-stepper.blade.php` menjadi satu baris horizontal ringkas tanpa baris ceklist duplikat, tanpa kalimat instruksi mengambang, maupun paragraf catatan mentah.
    3. Menyelaraskan `photographer/availability/create.blade.php`: breadcrumb netral **Rule #62**, judul `text-lg sm:text-xl`, tombol netral `Kembali` dengan single chevron **Rule #71** / **Rule #72**, penghapusan garis `border-t` (**Rule #41**), textarea `rows="4" resize-y` berplaceholder `"Masukkan keterangan libur jika diperlukan"`, serta tautan kembali mengarah tepat ke `tab=overrides`. Mendokumentasikan standar ini pada **Rule #108** di `README.md`.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #22**, **Rule #41**, **Rule #49**, **Rule #51**, **Rule #58**, **Rule #61**, **Rule #62**, **Rule #71**, **Rule #72**, **Rule #73**, **Rule #104**, serta **Rule #108**.
* **Hasil Pengujian & Verifikasi**:
  * `npm run build` (`vite v8.2.1`): **PASS** (1.18s; bundel CSS terverifikasi memuat `accent-color:#f59e0b` serta utilitas `accent-amber-500`).
  * `php artisan view:clear` & `php artisan view:cache` (PHP 8.4.24): **PASS** (*Blade templates cached successfully*).
  * Uji render langsung via HTTP Kernel (`kenji`, `id=6`) pada 5 rute (`/photographer/availability/create`, `/photographer/availability?tab=weekly`, `/photographer/availability?tab=overrides`, `/photographer/services/create`, `/photographer/dashboard`) termasuk pengecekan keseimbangan tag `<div>` serta asersi elemen DOM: **PASS** (`HTTP 200`, `ALL_VIEW_AND_ROUTE_CHECKS_PASS`).

---

### [2026-11-12 13:24 WITA] — Penegakan Alur Ketat Persetujuan dan Penolakan Verifikasi Profil serta Etalase Fotografer Admin

* **Modul / Fitur**:
  - Halaman detail verifikasi fotografer Admin (`/admin/photographers/{id}`) pada Tab `Profil Mitra` serta Tab `Etalase Tayang`, beserta alur pengajuan ulang profil fotografer.
* **File yang Diubah**:
  * `resources/views/admin/photographers/show.blade.php`
  * `app/Http/Controllers/Admin/PhotographerVerificationController.php`
  * `app/Http/Controllers/Public/ProfileController.php`
  * `README.md` (`Rule #95`)
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Before**: Setelah Admin menolak pendaftaran profil (`status_verifikasi === 'ditolak'`) atau menolak penayangan etalase (`status_etalase === 'ditolak'`), tombol `Verifikasi Fotografer` dan `Setujui Tayang` tetap tampil serta bisa diklik langsung oleh Admin meskipun fotografer belum memperbarui data atau mengajukan ulang. Selain itu, saat `status_etalase === 'belum_diajukan'` dengan checklist 3/3 sudah lengkap, tombol `Setujui Tayang` sudah aktif walau fotografer belum menekan `Ajukan Tayang`, dan `catatan_penolakan` lama belum dibersihkan saat fotografer menyimpan ulang profil.
  * **After**:
    - **Tab 1 (`Profil Mitra`)**: Saat `status_verifikasi === 'ditolak'`, tombol `Verifikasi Fotografer` dan `Tolak Pendaftaran` disembunyikan (hanya tampil teks status `Menunggu fotografer memperbarui profil.` beserta tombol `Hapus`), dan backend `toggleVerification()` serta `reject()` memblokir permintaan hingga fotografer menyimpan ulang profilnya (`ProfileController::updateProfile()` mengembalikan status ke `menunggu` dan membersihkan `catatan_penolakan`). Saat `menunggu` menampilkan `Tolak Pendaftaran` dengan `Verifikasi Fotografer`; saat `terverifikasi` menampilkan `Tolak Pendaftaran` dengan `Batalkan Verifikasi`.
    - **Tab 2 (`Etalase Tayang`)**: Saat `status_etalase === 'belum_diajukan'` maupun `ditolak`, tombol `Setujui Tayang` dan `Tolak Etalase` disembunyikan (menampilkan teks status netral sesuai kondisi), dan backend `approveStorefront()`, `rejectStorefront()`, serta `revokeStorefront()` menegakkan transisi status secara ketat hingga fotografer menekan `Ajukan Tayang` / `Ajukan Ulang Tayang` (`status_etalase === 'menunggu'`). Menyelaraskan `$initialTab` agar `tab=profile` atau error validasi penolakan profil tetap bertahan di Tab 1, serta memperbarui **Rule #95** di `README.md`.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #49**, **Rule #51**, **Rule #67**, **Rule #70**, **Rule #74**, **Rule #91**, dan **Rule #95**.
* **Hasil Pengujian & Verifikasi**:
  * `php -l` (`PhotographerVerificationController.php`, `ProfileController.php`, PHP 8.4.24): **PASS** (*No syntax errors detected*).
  * `php artisan route:list --path=admin/photographers`: **PASS** (8 rute aktif terverifikasi).
  * `php artisan view:clear` & `php artisan view:cache`: **PASS** (*Blade templates cached successfully*).
  * `npm run build` (`vite v8.2.1`): **PASS** (893ms).
  * Pengujian transaksional (rolled-back) controller guards + render HTML `admin.photographers.show` di seluruh skenario status Tab 1 (`menunggu`, `ditolak`, `terverifikasi`) dan Tab 2 (`belum_diajukan`, `menunggu`, `ditolak`, `disetujui`) serta pengajuan ulang profil (`catatan_penolakan` ter-reset `null`): **PASS** (*ALL_ASSERTIONS_PASSED*, *DB_STATE_VERIFIED_UNCHANGED*).

---

### [2026-11-12 12:10 WITA] — Penghapusan Divider Toolbar (`border-b`) & Celah Jarak (`space-y-4` / `space-y-0`) pada Seluruh Tampilan Card Tabel Sistem

* **Modul / Fitur**:
  - Seluruh tampilan Data Table Card pada panel Admin, Fotografer, dan Klien (*20 file view tabel / 21 kartu tabel*).
* **File yang Diubah**:
  * `resources/views/admin/activity_logs/index.blade.php`
  * `resources/views/admin/admins/index.blade.php`
  * `resources/views/admin/categories/index.blade.php`
  * `resources/views/admin/contests/index.blade.php`
  * `resources/views/admin/contests/manage.blade.php` *(Tabel Pendaftar & Tabel Submisi)*
  * `resources/views/admin/contests/participants.blade.php`
  * `resources/views/admin/contests/submissions.blade.php`
  * `resources/views/admin/elearning/index.blade.php`
  * `resources/views/admin/forum/index.blade.php`
  * `resources/views/admin/photographers/index.blade.php`
  * `resources/views/admin/reviews/index.blade.php`
  * `resources/views/admin/transactions/index.blade.php`
  * `resources/views/admin/users/index.blade.php`
  * `resources/views/client/bookings/index.blade.php`
  * `resources/views/client/invoices/index.blade.php`
  * `resources/views/photographer/availability/index.blade.php`
  * `resources/views/photographer/bookings/index.blade.php`
  * `resources/views/photographer/faqs/index.blade.php`
  * `resources/views/photographer/portfolio/index.blade.php`
  * `resources/views/photographer/reports/index.blade.php`
  * `resources/views/photographer/services/index.blade.php`
  * `README.md` (`Rule #107`)
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Before**: Kontainer toolbar/header di bagian atas setiap Data Table Card memiliki garis pembatas `border-b border-gray-100 dark:border-gray-700` (atau `/80`, `/60`) dan mayoritas pembungkus card memakai `space-y-4` (atau `space-y-0` di `client/invoices/index.blade.php`), sehingga muncul garis pembatas tambahan beserta celah putih 16px tepat di atas pita `<thead>`.
  * **After**: Menghapus seluruh `border-b border-gray-100 ...` pada toolbar/header kartu tabel serta menghapus `space-y-4` (`space-y-0`) pada pembungkus luar Data Table Card agar toolbar filter & pencarian langsung menyatu mulus ke `<thead>` tanpa divider ganda. Mendokumentasikan standar baru tersebut di **Rule #107** pada `README.md`.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #41**, **Rule #49**, **Rule #58**, dan **Rule #107**.
* **Hasil Pengujian & Verifikasi**:
  * Audit regex across `resources/views/**/*.blade.php`: 0 sisa `overflow-hidden space-y-4`/`space-y-0` dan 0 sisa toolbar/header `border-b` di seluruh tampilan card tabel (**PASS**).
  * `php artisan view:clear` & `php artisan view:cache` (PHP 8.4.24): **PASS** (*Blade templates cached successfully*).
  * `npm run build` (`vite v8.2.1`): **PASS** (797ms).

---

### [2026-11-12 11:59 WITA] — Perbaikan Default Switch Jadwal Libur (`is_open = false`), Audit Alur Verifikasi Mitra, dan Pembaruan UI `x-time-dropdown` (Menit `00`–`59`, Lebar Card, & Zero Dividers)

* **Modul / Fitur**:
  - Komponen pemilih jam [`x-time-dropdown`](resources/views/components/time-dropdown.blade.php), Jadwal & Ketersediaan Fotografer (`/photographer/availability`, `/photographer/availability/create`), modal penawaran chat (`/photographer/chat`, `/chat`), serta alur verifikasi profil dan penayangan etalase fotografer (`/photographer/profile`, `/photographer/dashboard`, `/admin/photographers/{id}`).
* **File yang Diubah**:
  * `resources/views/components/time-dropdown.blade.php`
  * `resources/views/photographer/availability/index.blade.php`
  * `resources/views/photographer/availability/create.blade.php`
  * `resources/views/photographer/chat/index.blade.php`
  * `resources/views/public/chat/index.blade.php`
  * `resources/views/photographer/dashboard.blade.php`
  * `resources/views/partials/photographer/stage-stepper.blade.php`
  * `resources/views/admin/photographers/show.blade.php`
  * `app/Models/PhotographerWorkingHour.php`
  * `app/Http/Controllers/Photographer/AvailabilityController.php`
  * `app/Http/Controllers/Admin/PhotographerVerificationController.php`
  * `app/Http/Controllers/Public/ProfileController.php`
  * `database/migrations/2024_01_01_000014_create_photographer_working_hours_table.php`
  * `database/migrations/2026_11_11_000001_add_storefront_review_fields_to_role_photographers_table.php`
  * `database/migrations/2026_11_12_000001_set_default_is_open_false_on_photographer_working_hours_table.php` *(file baru)*
  * `database/seeders/PhotographerSeeder.php`
  * `README.md` (`Rule #95`, `Rule #105`)
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Before**:
    * Saat fotografer pertama kali membuka `/photographer/availability`, `PhotographerWorkingHour::initializeDefaults()` dan skema `photographer_working_hours.is_open` langsung menyalakan semua 7 hari (`is_open = true`, Senin–Minggu berstatus `(Buka)`) seolah fotografer buka setiap hari padahal belum pernah mengatur jadwal.
    * Menyimpan jadwal dengan seluruh hari libur (0 hari buka) tetap mengeset `jadwal_dikonfirmasi = true` dan memunculkan pesan sukses meski `hasConfiguredSchedule()` bernilai `false`.
    * Admin dapat menekan `Verifikasi Fotografer` walau kelengkapan profil belum 100%, tidak ada notifikasi Admin saat profil baru selesai dilengkapi pertama kali, serta terdapat sisa kata `Studio` pada pesan hapus `PhotographerVerificationController::destroy()`.
    * Panel menu `x-time-dropdown` terlalu sempit (`min-w-[136px]`), memakai garis pembatas `divide-x` dan `border-b` berulang, serta kolom Menit hanya menyediakan opsi `00` dan `30`.
  * **After**:
    * `PhotographerWorkingHour::initializeDefaults(int $photographerId, bool $defaultOpen = false)` dan skema database kini mewajibkan default `is_open = false` (**Libur / OFF**) bagi fotografer yang belum mengatur jadwal, sementara `PhotographerSeeder` dan migrasi demo mengoper `true` agar akun demo tetap tayang. Migrasi baru `2026_11_12_000001` mengubah default kolom dan mereset jadwal belum terkonfirmasi.
    * `AvailabilityController::updateWorkingHours()` memvalidasi format `HH:mm`, menormalkan opsi 24 jam agar nonaktif bila `is_open = false`, menyetel `jadwal_dikonfirmasi = $hasOpenDay`, memberi notifikasi `warning` bila 0 hari aktif, serta meringkas copywriting tanpa tanda `&`/`/` redundan.
    * `PhotographerVerificationController::toggleVerification()` dan `admin/photographers/show.blade.php` memblokir verifikasi bila profil belum 100% lengkap; istilah `Studio` di `destroy()` diubah menjadi `Fotografer`; `ProfileController::updateProfile()` mengirim log & notifikasi ke Admin saat profil pertama kali selesai 100%.
    * `x-time-dropdown` diperlebar ke `w-[224px] sm:w-[240px]` dengan dukungan prop `align` (`left`, `right`, `md-right`), seluruh garis pembatas internal (`divide-x` dan `border-b`) dihapus, serta daftar Menit diperluas penuh menjadi **`00`–`59`** ber-scroll `.custom-scrollbar` yang otomatis menengahkan jam dan menit saat dibuka.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #19, Rule #41, Rule #46, Rule #49, Rule #58, Rule #70, Rule #94, Rule #95, Rule #104, Rule #105**.
* **Hasil Pengujian & Verifikasi**:
  * `php artisan migrate --force`: **PASS** (`2026_11_12_000001_set_default_is_open_false_on_photographer_working_hours_table` `50.23ms DONE`, `DEFAULT` `is_open = '0'`).
  * Pengujian skrip PHP 8.4 (DB transaction rollback): `initializeDefaults()` membuat 7 hari OFF default, akun demo `id 1..5` tetap `isPublishedToCatalog()`, `toggleVerification()` menolak profil belum lengkap, `updateWorkingHours()` menjaga `jadwal_dikonfirmasi = false` bila 0 hari buka dan menyimpan jam presisi menit (`09:15`–`18:45`): **PASS**.
  * `php -l` seluruh file PHP terubah, `php artisan view:clear`, `php artisan view:cache`, dan `npm run build` (`vite v8.2.1`, `857ms`): **PASS**.

---

### [2026-11-12 11:37 WITA] — Refaktor Menu `x-time-dropdown` ke Grid 2 Kolom (Jam | Menit) Sesuai Standar Custom Dropdown `README.md`

* **Modul / Fitur**:
  - Komponen Pemilih Jam Standar [`x-time-dropdown`](resources/views/components/time-dropdown.blade.php) serta seluruh pemanggilnya (`/photographer/availability`, `/photographer/availability/create`, modal penawaran `/photographer/chat`, dan modal penawaran `/chat`).
* **File yang Diubah**:
  * `resources/views/components/time-dropdown.blade.php`
  * `resources/views/photographer/availability/index.blade.php`
  * `README.md` (`Rule #105`)
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Before**:
    * Menu melayang `x-time-dropdown` menampilkan satu daftar panjang 48 baris (`00:00`–`23:30`) dengan scrollbar bawaan OS yang panjang untuk digulir, dan `scrollIntoView()` sebelumnya kurang akurat saat pembukaan animasi `x-transition`.
  * **After**:
    * Mempertahankan tombol trigger identik (`rounded-xl border text-xs font-bold flex items-center justify-between gap-2 shadow-2xs` dengan ikon chevron berotasi dan ring fokus amber) serta hidden input / Alpine binding `HH:mm`.
    * Mengubah panel dropdown melayang (`bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700/80`) menjadi **grid 2 kolom (`grid-cols-2 divide-x`)**: **Kolom Kiri `Jam` (`00`–`23`)** memakai scroll ramping `.custom-scrollbar` (`max-h-48`) dan **Kolom Kanan `Menit` (`00`, `30`)**, masing-masing berheader label minimalis `text-[10px] font-extrabold uppercase tracking-wider text-gray-400 border-b` serta item `Rule #46` (`hover:bg-amber-50 hover:text-amber-600`, aktif `bg-amber-50/70 text-amber-600 font-extrabold`, `border-b border-gray-50 last:border-0`).
    * Menambahkan koordinasi `pickHour` / `pickMin` (memilih jam otomatis melengkapi menit `'00'` jika masih kosong dan membiarkan menu terbuka sampai menit dikonfirmasi; memilih menit menutup menu otomatis jika jam sudah terisi) serta perhitungan `box.scrollTop` terkurung container lewat `$nextTick` + `requestAnimationFrame` dengan jangkar `'08'` saat masih kosong.
    * Menyesuaikan `menu-class="min-w-[136px] w-full"` di `resources/views/photographer/availability/index.blade.php` agar proporsi kedua kolom seimbang dengan trigger `w-[104px]`.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #46, Rule #49, Rule #105**.
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear`, `php artisan view:cache` (PHP 8.4.24), `npm run build` (`vite v8.2.1`, 882ms, utilitas `.custom-scrollbar` terkompilasi pada `public/build/assets/app-B8eyXLyY.css`): **PASS**.

---

### [2026-11-12 11:24 WITA] — Penyatuan Sebaris Label Status Hari Operasional `(Buka)` / `(Libur)` di Halaman Jadwal Fotografer

* **Modul / Fitur**:
  - Halaman Kelola Jam Kerja Mingguan Fotografer (`/photographer/availability`).
* **File yang Diubah**:
  * `resources/views/photographer/availability/index.blade.php`
  * `README.md` (`Rule #105`)
  * `TRACKING_PERUBAHAN_FILE.md`
* **Rincian Perubahan (Before vs After)**:
  * **Before**:
    * Nama hari (`Senin`, `Selasa`, dst.) dan status (`Aktif` / `Libur`, `text-[11px]`) ditumpuk vertikal 2 baris di bawah satu sama lain.
  * **After**:
    * Status diposisikan sebaris tepat di samping nama hari dengan format **`Senin (Buka)`** (hijau emerald tebal) dan **`Senin (Libur)`** (abu-abu netral) dalam ukuran seragam `text-sm` disertai fallback SSR Blade sebelum hidrasi Alpine.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #49, Rule #67, Rule #105**.
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear`, `php artisan view:cache` (PHP 8.4.24), dan `npm run build` (`vite v8.2.1`, 871ms): **PASS**.

---

### [2026-11-12 11:18 WITA] — Penyeragaman Tipografi KPI Cards dan Isi Tabel (`<tbody>`) ke `text-xs`, Teks Ringkas Fasilitas Paket Layanan, dan Perbaikan Proporsi `<th>` Tabel FAQ

* **Modul / Fitur**:
  - Standarisasi KPI Metric Cards pada Dashboard Utama, Halaman Tabel Fitur (Admin, Fotografer, Klien), dan Profil Akun Publik, Tipografi Seragam `text-xs` pada Sel `<tbody>`, Daftar Ringkas Fasilitas Paket Layanan Fotografer, serta Proporsi Kolom `<th>` Tabel Tanya Jawab Fotografer.
* **File yang Diubah**:
  * **Prioritas Utama & Dokumentasi**:
    * `resources/views/photographer/faqs/index.blade.php`
    * `resources/views/photographer/services/index.blade.php`
    * `README.md` (`Rule #64` dan `Rule #106`)
    * `TRACKING_PERUBAHAN_FILE.md`
  * **Dashboard Utama, Profil Publik, dan Partial Tabel**:
    * `resources/views/admin/dashboard.blade.php`
    * `resources/views/photographer/dashboard.blade.php`
    * `resources/views/public/users/show.blade.php`
    * `resources/views/partials/table/table-01.blade.php`
  * **Panel Admin**:
    * `resources/views/admin/photographers/index.blade.php`
    * `resources/views/admin/photographers/show.blade.php`
    * `resources/views/admin/users/index.blade.php`
    * `resources/views/admin/categories/index.blade.php`
    * `resources/views/admin/elearning/index.blade.php`
    * `resources/views/admin/forum/index.blade.php`
    * `resources/views/admin/activity_logs/index.blade.php`
    * `resources/views/admin/admins/index.blade.php`
    * `resources/views/admin/reviews/index.blade.php`
    * `resources/views/admin/contests/index.blade.php`
    * `resources/views/admin/contests/manage.blade.php`
    * `resources/views/admin/contests/participants.blade.php`
    * `resources/views/admin/contests/submissions.blade.php`
    * `resources/views/admin/transactions/index.blade.php`
  * **Panel Fotografer, Klien, dan Faktur Online**:
    * `resources/views/photographer/portfolio/index.blade.php`
    * `resources/views/photographer/availability/index.blade.php`
    * `resources/views/photographer/bookings/index.blade.php`
    * `resources/views/photographer/reports/index.blade.php`
    * `resources/views/client/bookings/index.blade.php`
    * `resources/views/client/invoices/index.blade.php`
    * `resources/views/public/booking/invoice.blade.php`
    * `resources/views/public/booking/invoice-print.blade.php`
    * `resources/views/public/elearning/invoice-print.blade.php`
* **Rincian Perubahan (Before vs After)**:
  * **Before**:
    * Seluruh KPI Cards di halaman dashboard dan tabel memakai label `text-[11px]` dengan nilai `text-lg sm:text-xl` (atau `text-2xl`), serta pada `admin/forum/index` bernilai `text-base sm:text-lg`, sehingga angka nilai tampak menonjol sendiri dibanding label di atasnya.
    * Isi sel `<tbody>` di seluruh tabel aplikasi mencampur ukuran font tidak seragam: judul nama, layanan, dan nominal memakai `text-sm`, `text-base`, `text-xs sm:text-sm`, atau `text-theme-sm`, sedangkan subteks atau kode memakai `text-[10px]` dan `text-[11px]`.
    * Rincian fasilitas pada `photographer/services/index.blade.php` dirender sebagai tumpukan pill kapsul `bg-gray-100 rounded-md text-[10px]`.
    * Header `<th>` pada `photographer/faqs/index.blade.php` dipaksa memakai pecahan lebar timpang (`w-1/12`, `w-5/12`, `w-4/12`) dan menerapkan `line-clamp-2` langsung pada tag `<td>`, sehingga saat tabel kosong kolom header tampak timpang dan tidak proporsional dibanding tabel lain (seperti `admin/photographers/index`).
  * **After**:
    * Menyeragamkan seluruh label dan nilai KPI Cards pada halaman tabel fitur, dashboard utama, rangkuman grafik dashboard, kartu ringkasan kesiapan (`admin/photographers/show`), dan statistik akun `public/users/show` ke **`text-xs`**, dibedakan murni lewat ketebalan (`font-black` vs `font-bold`) dan warna kontras tanpa mengubah ukuran hero marketing atau harga katalog komersial.
    * Menyeragamkan seluruh sel `<tbody>` pada tabel Admin, Fotografer, Klien, `table-01.blade.php`, dan invoice online ke **`text-xs`** (kecuali cetak A4 PDF `photographer/reports/export.blade.php`), sehingga hierarki data murni ditentukan oleh ketebalan huruf dan warna teks.
    * Mengubah daftar pill fasilitas di `photographer/services/index.blade.php` menjadi teks ringkas satu baris/paragraf dipisah `•` (`text-xs text-gray-500 dark:text-gray-400 leading-relaxed`).
    * Menghapus fraksi `w-*/12` pada `<th>` di `photographer/faqs/index.blade.php` disertai `whitespace-nowrap`, memberi `align-top` pada sel, memindahkan `line-clamp-2` ke `<p>` di dalam sel jawaban, serta membiarkan proporsi `<th>` tabel lain tetap utuh tanpa modifikasi.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #49, Rule #64, Rule #67, Rule #68, Rule #70, Rule #80, Rule #106**.
* **Hasil Pengujian & Verifikasi**:
  * Scan PowerShell lintas seluruh view non-demo terhadap `<tbody>` non-`text-xs` (0 temuan sisa), audit `git diff` header `<th>` terkonfirmasi hanya menyentuh `photographer/faqs/index.blade.php`, serta kompilasi Blade (`php artisan view:clear`, `php artisan view:cache`) dan aset produksi (`npm run build`, `vite v8.2.1`, 905ms) via PHP 8.4.24: **PASS**.

---

### [2026-11-12 10:36 WITA] — Custom Alpine.js Time Dropdown (`x-time-dropdown`), Pembersihan UI Baris Flat Jadwal Mingguan, dan Perbaikan Empty State Sidebar Chat

* **Modul / Fitur**:
  - Komponen Pemilih Jam Standar (`x-time-dropdown`), Halaman Kelola Jadwal & Ketersediaan Fotografer (`/photographer/availability`), Tambah Blokir Tanggal (`/photographer/availability/create`), serta Sidebar Obrolan & Modal Penawaran Khusus (`/chat` dan `/photographer/chat`).
* **File yang Ditambahkan / Diubah**:
  * `resources/views/components/time-dropdown.blade.php` (baru)
  * `resources/views/photographer/availability/index.blade.php`
  * `resources/views/photographer/availability/create.blade.php`
  * `resources/views/photographer/chat/index.blade.php`
  * `resources/views/public/chat/index.blade.php`
  * `README.md` (`Rule #105`)
* **Rincian Perubahan (Before vs After)**:
  * **Before**:
    * Seluruh pemilih jam masih memakai `<input type="time">` bawaan browser sehingga memunculkan popup biru standar OS Windows yang tidak selaras dengan Custom Alpine.js Floating Dropdown sistem (**Rule #46**).
    * Jadwal mingguan di `photographer/availability/index.blade.php` membungkus tiap hari dalam kotak abu-abu bertumpuk (`bg-gray-50` card-in-card), menyisakan ruang kosong lebar di tengah baris (`justify-between`), menumpuk garis pemisah `border-b` di bawah tajuk kartu, dan memakai label repetitif (*"Buka Operasional"*, *"24 Jam / Fleksibel Kapan Saja"*).
    * Empty state daftar percakapan pada `public/chat/index.blade.php` dan `photographer/chat/index.blade.php` menempel di bagian atas sidebar serta menumpuk teks terpotong vertikal ("gepeng") saat panel sidebar diperkecil (*collapse*).
  * **After**:
    * Menambahkan komponen Blade standar [`x-time-dropdown`](resources/views/components/time-dropdown.blade.php) berinterval 30 menit (`00:00`–`23:30`) yang meniru desain trigger dropdown sistem (`rounded-xl`, chevron berotasi, focus ring amber, auto-scroll item aktif), menggantikan total `<input type="time">` di 4 halaman produksi dan mengatur modal penawaran chat menjadi `overflow-visible`.
    * Merombak jadwal 7 hari pada `photographer/availability/index.blade.php` menjadi baris flat putih dengan garis pemisah tipis (`divide-y`), grid 3-kolom sejajar (`md:grid-cols-12`), tanpa garis pemisah sub-heading (**Rule #41 & #58**), dan copywriting ringkas (*"Jadwal"*, *"Tanggal Libur"*, *"Aktif"* / *"Libur"*, *"24 Jam"*).
    * Memusatkan posisi vertikal empty state sidebar chat (`h-full min-h-full flex flex-col items-center justify-center`) dan menyembunyikannya dengan aman saat `chatListCollapsed` aktif agar teks tidak tergencet menjadi vertikal.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #41, Rule #46, Rule #49, Rule #58, Rule #70, Rule #105**.
* **Hasil Pengujian & Verifikasi**:
  * **PASS** — Seluruh template Blade terkompilasi bersih via PHP 8.4 (`artisan view:cache`), `npm run build` selesai tanpa error, serta audit grep memastikan tidak ada sisa `<input type="time">` pada view produksi non-demo.

---

### [2026-11-12 09:57 WITA] — Implementasi Arsitektur FAQ Kombinasi Opsi A (Tanya Jawab Fotografer + Ketentuan Sistem Hardcoded Terpusat) & Audit Kepatuhan README

* **Modul / Fitur**:
  - Konfigurasi FAQ Terpusat, Halaman Pusat Bantuan (`/faq`), Tab Tanya Jawab Profil Publik Fotografer (`/fotografer/{id}`), Halaman Kelola Tanya Jawab Fotografer (`/photographer/faqs`), dan Seeder Demo Fotografer.
* **File yang Ditambahkan / Diubah**:
  * `config/lensmatch_faqs.php` (baru)
  * `app/Http/Controllers/Public/HelpController.php`
  * `app/Http/Controllers/Public/PhotographerController.php`
  * `app/Http/Controllers/Photographer/FaqController.php`
  * `resources/views/public/photographers/show.blade.php`
  * `resources/views/photographer/faqs/index.blade.php`
  * `database/seeders/PhotographerSeeder.php`
  * `README.md` (`Rule #86`)
* **Rincian Perubahan (Before vs After)**:
  * **Before**:
    * Halaman `/faq` menyimpan array FAQ secara terpisah di `HelpController`, masih memakai istilah `"Escrow"`, menyebut metode `"QRIS"` yang tidak ada di invoice, serta menyebut syarat `"minimal 3 portofolio"`.
    * `PhotographerSeeder` mengisi 7 pertanyaan bertopik aturan sistem (DP 30%, pembayaran platform, alur booking) ke akun demo fotografer, sehingga membingungkan batas kewenangan antara sistem dan fotografer.
    * Tab Tanya Jawab di `public/photographers/show.blade.php` hanya membaca `$photographer->faqs` sehingga fotografer baru terlihat kosong total di tab tersebut.
    * `photographer/faqs/index.blade.php` menggunakan istilah `'Studio'` untuk peran fotografer, simbol `+` ganda pada tombol header, tombol duplikat pada empty state tabel, judul memakai `&`, `json_encode($faq)` pada atribut HTML, kelas dark-mode non-standar (`dark:bg-gray-850`, `dark:hover:bg-gray-750`, serta input modal `dark:bg-gray-800`), validasi min/max backend-frontend belum selaras, serta belum menampilkan referensi ketentuan sistem.
  * **After**:
    * Menambahkan `config/lensmatch_faqs.php` sebagai satu sumber hardcoded terpusat untuk `help_categories` (`/faq`) dan `profile_system_faqs` (4 ketentuan baku platform), menggunakan copywriting to-the-point berbahasa Indonesia alami tanpa istilah `'Studio'`, `'Escrow'`, `'Rekening Bersama'`, atau `'QRIS'` (menyebut transfer bank ke rekening resmi PT LensMatch Indonesia sesuai `public/booking/invoice.blade.php`).
    * Memperbaharui `HelpController@faq` dan `Public\PhotographerController@show` untuk membaca konfigurasi terpusat tersebut dan menyelaraskan informasi Pembayaran Penuh / DP 30%, pembatalan H-24 jam, serta jadwal ulang dengan logika aplikasi aktual.
    * Memperbaharui Tab 5 **Tanya Jawab** pada `resources/views/public/photographers/show.blade.php` dengan 2 blok terpisah: **Tanya Jawab Fotografer** (diatur fotografer) dan **Ketentuan Sistem LensMatch** (otomatis tampil dari kode) memakai ikon accordion `?` yang konsisten, kartu putih bebas shadow internal (`Rule #100`), hover dark-mode valid (`dark:hover:bg-gray-700/50`), serta badge counter gabungan.
    * Memperbaharui `Photographer\FaqController` dan `resources/views/photographer/faqs/index.blade.php`: menyelaraskan penamaan **Tanya Jawab (FAQ)** (konsisten dengan sidebar & Rule #19), tabel **Daftar Tanya Jawab**, panel referensi *read-only* **Ketentuan Sistem LensMatch**, validasi backend-frontend (`pertanyaan` `min:5|max:255`, `jawaban` `min:10|max:2500`, `urutan` `min:1|max:999`), menghapus simbol `+` ganda dan tombol CTA duplikat empty state, mengganti `json_encode` dengan `Js::from`, menyamakan dark-mode modal/input (`dark:bg-gray-800` card, `dark:bg-gray-900` input, `dark:hover:bg-gray-700/50`), layout grid penuh `Urutan Tampil` pada mode Tambah, serta menghapus elemen pembantu tambahan tanpa persetujuan (Rule #42).
    * Memperbaharui `PhotographerSeeder.php` agar men-seed contoh tanya jawab layanan fotografer (estimasi edit, tiket lokasi, transport luar kota, revisi warna, ketentuan hujan).
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #19, Rule #22, Rule #42, Rule #51, Rule #59, Rule #64, Rule #70, Rule #71, Rule #76, Rule #80, Rule #86, Rule #89, Rule #100**.
* **Hasil Pengujian & Verifikasi**:
  * **PASS** — `php -l` seluruh file PHP/konfigurasi terkait tanpa syntax error; `php artisan config:clear`, `view:cache`, `view:clear`, dan `npm run build` (`vite v8.2.1`, 759ms) selesai bersih; serta uji integrasi runtime (`verify_faq.php` dalam transaksi rollback DB) memverifikasi `/faq`, profil publik (`/fotografer/{id}`), validasi/CRUD `/photographer/faqs` + escape `Js::from`, penyembunyian FAQ nonaktif, dan konsistensi 4 ketentuan sistem.

---

### [2026-11-11 21:37 WITA] — Standardisasi Padding Vertikal Modal Popup Lintas Role (Migrasi `space-y-*` ke `flex flex-col gap-*`)

* **Modul / Fitur**:
  - Komponen modal dan dialog konfirmasi lintas role (Shared Partials, Fotografer, Klien, Admin, dan Publik).
* **File yang Diubah**:
  * **Shared Partials**:
    * `resources/views/partials/shared/confirm-modal.blade.php`
    * `resources/views/partials/shared/access-notice-modal.blade.php`
    * `resources/views/partials/client/review-modal.blade.php`
    * `resources/views/partials/photographer/onboarding-wizard.blade.php`
  * **Fotografer dan Klien**:
    * `resources/views/photographer/bookings/index.blade.php`
    * `resources/views/photographer/faqs/index.blade.php`
    * `resources/views/photographer/reports/index.blade.php`
    * `resources/views/photographer/chat/index.blade.php`
    * `resources/views/client/bookings/index.blade.php`
    * `resources/views/client/galleries/index.blade.php`
  * **Admin**:
    * `resources/views/admin/contests/index.blade.php`
    * `resources/views/admin/contests/manage.blade.php`
    * `resources/views/admin/contests/submissions.blade.php`
    * `resources/views/admin/contests/participants.blade.php`
    * `resources/views/admin/transactions/index.blade.php`
    * `resources/views/admin/elearning/curriculum.blade.php`
    * `resources/views/admin/photographers/index.blade.php`
    * `resources/views/admin/photographers/show.blade.php`
    * `resources/views/admin/activity_logs/index.blade.php`
  * **Publik**:
    * `resources/views/public/chat/index.blade.php`
    * `resources/views/public/booking/invoice.blade.php`
    * `resources/views/public/forum/index.blade.php`
    * `resources/views/public/forum/show.blade.php`
    * `resources/views/public/elearning/show.blade.php`
    * `resources/views/public/elearning/index.blade.php`
    * `resources/views/public/elearning/katalog.blade.php`
    * `resources/views/public/contests/index.blade.php`
    * `resources/views/public/contests/show.blade.php`
    * `resources/views/public/photographers/katalog.blade.php`
    * `resources/views/public/photographers/show.blade.php`
* **Rincian Perubahan (Before vs After)**:
  * **Before**:
    * Container modal, `<form>`, dan wrapper internal yang memakai `space-y-*` di atas saudara `<template>`, `<input type="hidden">`, elemen `x-show`, maupun tombol close `absolute` tetap memberi `margin-bottom` pada child visual terakhir, sehingga padding bawah modal tampak lebih tinggi daripada padding atas.
    * Sejumlah footer tombol aksi modal tanpa divider masih menumpuk `pt-1` hingga `pt-4` di atas `space-y-*`, judul awal modal memiliki `mt-1` atau `mb-4`, serta beberapa modal scrollable memiliki padding header dan footer yang tidak simetris.
  * **After**:
    * Mengganti `space-y-*` menjadi `flex flex-col gap-*` pada container kartu modal, elemen `<form>`, dan wrapper internal (CSS Flexbox `gap` otomatis mengabaikan `<template>`, elemen `display: none`, `<input type="hidden">`, dan elemen `absolute`).
    * Menyesuaikan wrapper dinamis pada modal peserta lomba di `admin/contests/manage.blade.php` (`grid grid-cols-1 sm:grid-cols-2` vs `flex flex-col` dengan `gap-4`).
    * Menghapus `pt-1`/`pt-2`/`pt-3`/`pt-4` bertumpuk pada footer tombol tanpa border, menghapus `mt-1`/`mb-4` pada `<h3>` judul awal modal, serta menyamakan padding header dan footer pada modal scrollable dan modal ber-divider (`onboarding-wizard`, `submissions`, `transactions`, `public/forum/index`, `activity_logs`).
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #49**: Perubahan fokus pada kelas layout/spacing tanpa mengubah teks copy, warna, ikon, alignment horizontal tombol, maupun logika JS.
  * **Rule #59, Rule #70, dan Rule #74**: Posisi tengah/kanan tombol aksi yang ada dipertahankan, tanpa tambahan ornamen maupun duplikasi istilah.
* **Hasil Pengujian & Verifikasi**:
  * Audit grep lintas seluruh view non-demo (`role="dialog"`, `aria-modal`, `fixed inset-0`, dan inspeksi diff 30 file Blade): **PASS**.
  * `php artisan view:cache` dan `php artisan view:clear` (via PHP 8.4.24): **PASS** (seluruh template Blade terkompilasi tanpa error).
  * `npm run build` (`vite v8.2.1`): **PASS** (aset CSS/JS produksi ter-build bersih dalam 801ms).

---

### [2026-11-11 21:22 WITA] — Hirarki Kategori Portofolio Berbasis Profil Fotografer, Penguncian Opsi B, dan Penyimpanan `id_kategori`

* **Modul / Fitur**:
  - Skema & Seeder `photographer_portofolios.id_kategori`, Form Tambah/Edit & Kelola Portofolio Fotografer, Penguncian Kategori Profil Fotografer (Opsi B), Detail Verifikasi Admin, serta Etalase Publik.
* **File yang Diubah / Ditambahkan**:
  * `database/migrations/2026_11_11_000002_add_id_kategori_to_photographer_portofolios_table.php` (baru)
  * `database/seeders/PhotographerSeeder.php`
  * `app/Models/Category.php`
  * `app/Models/PhotographerPortfolio.php`
  * `app/Models/RolePhotographer.php`
  * `app/Http/Controllers/Photographer/PortfolioController.php`
  * `app/Http/Controllers/Public/ProfileController.php`
  * `app/Http/Controllers/Photographer/OnboardingController.php`
  * `app/Http/Controllers/Admin/PhotographerVerificationController.php`
  * `app/Http/Controllers/Public/PhotographerController.php`
  * `resources/views/photographer/portfolio/create.blade.php`
  * `resources/views/photographer/portfolio/edit.blade.php`
  * `resources/views/photographer/portfolio/index.blade.php`
  * `resources/views/photographer/profile/index.blade.php`
  * `resources/views/admin/photographers/show.blade.php`
  * `resources/views/public/photographers/show.blade.php`
* **Rincian Perubahan (Before vs After)**:
  * **Before**:
    * Dropdown kategori di form Tambah/Edit Portofolio menampilkan seluruh `Category::all()`, tidak dipilih otomatis saat kategori profil hanya 1, dan tidak pernah menyimpan `id_kategori` ke database.
    * Tabel `photographer_portofolios` belum memiliki kolom `id_kategori`, sehingga halaman `Kelola Portofolio` menampilkan fallback `'Karya Foto'`, salah membaca `$port->foto`, serta metrik tema/pencarian tidak akurat; `Admin Detail` dan `Etalase Publik` belum menampilkan kategori per album.
    * Fotografer dapat melepas centang kategori di `Profil` meski kategori tersebut masih dipakai portofolio.
  * **After**:
    * **Migrasi, Backfill, & Seeder**: Menambahkan kolom nullable FK `id_kategori` (`nullOnDelete`) pada `photographer_portofolios` beserta backfill otomatis berbasis kategori profil dan kata kunci judul/deskripsi, serta menyertakan `id_kategori` pada `PhotographerSeeder`.
    * **Model & Relasi**: Menambah `PhotographerPortfolio::category()`, `Category::portfolios()`, dan `RolePhotographer::getUsedPortfolioCategoryCounts()` (mengabaikan portofolio maupun kategori global yang sudah dihapus agar tidak terjadi kebuntuan).
    * **Cakupan & Auto-Select Kategori Portofolio**: `PortfolioController@create` dan `@edit` membatasi opsi hanya kategori milik `$photographer->categories` (urut abjad), memvalidasi `Rule::in($allowedCategoryIds)` di `store`/`update`, otomatis memilih kategori jika profil hanya memiliki 1 kategori, menonaktifkan dropdown dengan tautan ke `Profil` jika masih 0 kategori, serta menampilkan catatan netral di halaman Edit bila kategori lama sudah dihapus.
    * **Penguncian Ketat Opsi B di Profil**: `ProfileController@updateProfile` memeriksa secara atomik sebelum menyimpan apakah ada kategori terpakai portofolio yang dilepas, `OnboardingController@complete` mempertahankan kategori terpakai secara defensif, dan `photographer/profile/index.blade.php` mengunci tombol hapus/chip kategori yang terpakai beserta indikator jumlah albumnya.
    * **Tampilan Kategori Asli & Perbaikan `Kelola Portofolio`**: Menampilkan `$port->category->nama_kategori` pada `Kelola Portofolio` (menggantikan fallback `'Karya Foto'`, memperbaiki thumbnail via `$port->medias->first()?->media`, metrik `$topCategoryName`/`$uniqueCategoryCount`, serta pencarian nama kategori & urutan `title_asc`/`title_desc`), `Admin Detail` (`admin/photographers/show.blade.php`), dan `Etalase Publik` (`public/photographers/show.blade.php`). `PhotographerService` (`Paket Layanan`) tetap dipertahankan fleksibel tanpa kategori.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #15 (Bagian 7), Rule #94, & Rule #104**: Data kategori 100% dinamis dari database (`id_kategori`) tanpa fallback statis `'Karya Foto'` serta ikon kamera outline/strip netral saat data kosong.
  * **Rule #46**: Menggunakan Alpine.js Custom Floating Dropdown tanpa `<select>` native.
  * **Rule #49**: Perubahan fokus ketat sesuai cakupan (mempertahankan `PhotographerService` tanpa ikatan kategori).
  * **Rule #64, Rule #67, Rule #68, & Rule #70**: Metric cards 2 baris, bebas bulatan dot badge, kolom kategori tabel teks netral, serta copywriting ringkas tanpa tanda `&` redundan.
  * **Rule #73, Rule #75, Rule #95, & Rule #100**: Konsistensi form portofolio, kartu galeri portofolio publik, validasi real-time profil, serta master card etalase.
* **Hasil Pengujian & Verifikasi**:
  * `php artisan migrate`: **PASS** (kolom `id_kategori` ditambahkan dan seluruh portofolio aktif ter-backfill).
  * `php -l` via PHP 8.4.24 pada seluruh 10 file PHP yang dibuat/diubah: **PASS** (0 syntax errors).
  * `php artisan view:cache` & `php artisan view:clear`: **PASS** (seluruh template Blade terkompilasi tanpa error).
  * `npm run build` (`vite v8.2.1`): **PASS** (aset CSS/JS produksi ter-build bersih dalam 868ms).
  * Uji integrasi Laravel end-to-end (rollback transaksi + `Storage::fake('public')`): **PASS** (`25/25` assertion lolos — mencakup backfill, form 0/1/multi kategori, validasi `store`, pencarian/tampilan `Kelola Portofolio` bebas `'Karya Foto'`, penguncian Opsi B profil & pengecualian kategori terhapus, catatan kategori legacy di `edit`, serta rendering kategori di `Admin Detail` & `Etalase Publik`).

---

### [2026-11-11 20:44 WITA] — Penggantian Teks "Pilih ..." dengan Ikon Unggah pada Seluruh Dropzone Upload Lintas Role

* **Modul / Fitur**:
  - Area dropzone unggah foto, media, dan berkas pada seluruh formulir role Fotografer, Klien/Publik, dan Admin (17 dropzone di 14 file Blade).
* **File yang Diubah**:
  * `resources/views/photographer/portfolio/create.blade.php`
  * `resources/views/photographer/portfolio/edit.blade.php`
  * `resources/views/photographer/profile/index.blade.php`
  * `resources/views/public/profile/index.blade.php`
  * `resources/views/public/forum/index.blade.php`
  * `resources/views/public/booking/invoice.blade.php`
  * `resources/views/public/contests/propose.blade.php`
  * `resources/views/public/contests/show.blade.php`
  * `resources/views/admin/contests/create.blade.php`
  * `resources/views/admin/contests/edit.blade.php`
  * `resources/views/admin/elearning/create.blade.php`
  * `resources/views/admin/elearning/edit.blade.php`
  * `resources/views/admin/elearning/curriculum.blade.php`
  * `resources/views/admin/transactions/index.blade.php`
* **Rincian Perubahan (Before vs After)**:
  * **Before**:
    * Seluruh 17 dropzone menampilkan teks tautan bergaris bawah `"Pilih ... atau seret dan lepas di sini"` (atau `"Pilih ... atau seret ke sini"`).
  * **After**:
    * Mengganti teks tautan bergaris bawah `"Pilih ..."` dengan ikon standar *tray upload* Heroicons outline (`arrow-up-tray`, `aria-hidden="true"`) di bagian tengah atas (`text-amber-500` saat state drag aktif, `text-gray-400 dark:text-gray-500` saat normal), diikuti teks ringkas `"Seret dan lepas di sini"` dan teks ketentuan format/ukuran di bawahnya.
    * Menyesuaikan tinggi minimal dropzone avatar dan sampul di `photographer/profile/index.blade.php` dari `min-h-[96px]` menjadi `min-h-[112px]` (`py-5 gap-1.5`) agar susunan vertikal ikon dan dua baris teks tetap proporsional.
* **Standar / Rule README yang Dipatuhi**:
  * Rule #22 (konfirmasi jenis dan tata letak ikon unggah dari pengguna sebelum implementasi).
  * Rule #49, Rule #70, Rule #97 (konsisten di seluruh role tanpa mengubah handler JS, validasi, maupun pratinjau/lightbox).
* **Hasil Pengujian & Verifikasi**:
  * Audit teks residu dropzone lintas `resources/views` (kecuali `demo/`): **0** sisa pola lama (`atau seret` / `Pilih ...dari perangkat`), **17/17** dropzone terverifikasi menggunakan ikon upload + `"Seret dan lepas di sini"`.
  * `npm run build` (**PASS**, utilitas Tailwind termasuk `min-h-[112px]`, `transition-colors`, `w-6`/`w-7`/`sm:w-8`, `text-amber-500`, `text-gray-700` terkompilasi ke CSS produksi).
  * Kompilasi seluruh **154** non-demo Blade views + `php -l` syntax check via PHP 8.4.24 (**PASS**, Failed: 0) dan `php artisan view:clear` (**PASS**).

---

### [2026-11-11 13:51 WITA] — Perbaikan Inisialisasi Alpine.js (`x-data`) dan Tombol `+ Tambah Benefit` pada Form Paket Layanan

* **Modul / Fitur**:
  - Form Tambah & Edit Paket Layanan Fotografer (`photographer/services/create.blade.php`, `photographer/services/edit.blade.php`).
* **File yang Diubah**:
  * `resources/views/photographer/services/create.blade.php`
  * `resources/views/photographer/services/edit.blade.php`
* **Rincian Perubahan (Before vs After)**:
  * **Before**:
    * Atribut `x-data="serviceFormApp({ ... nama_layanan: {!! json_encode(...) !!}, ... })"` memakai kutip ganda mentah dari `json_encode` di dalam atribut HTML berkutip ganda `x-data="..."`, sehingga parser HTML langsung memutus nilai atribut pada kutip ganda pertama.
    * Akibatnya inisialisasi Alpine.js gagal total: daftar input benefit default (`<template x-for="(fitur, index) in fiturs">`) tidak ter-render di DOM dan tombol `+ Tambah Benefit` (`@click="addFitur()"`) tidak berfungsi saat diklik.
  * **After**:
    * Mengubah pemanggilan menjadi `x-data="serviceFormApp()"` tanpa parameter JSON mentah di atribut HTML, selaras pola aman formulir portofolio/jadwal/profil fotografer.
    * Memindahkan seluruh nilai awal formulir, `old('fitur')`, serta status/pesan validasi (`nama_layanan`, `tarif_harga`, `deskripsi_layanan`, `fitur` / `fitur.*`) ke dalam `serviceFormApp()` di blok `<script>` menggunakan direktif `@js(...)` Laravel.
* **Standar / Rule README yang Dipatuhi**:
  * Rule #49 (Fokus ketat pada perbaikan komponen bermasalah tanpa mengubah layout, warna, maupun teks lain di luar instruksi).
* **Hasil Pengujian & Verifikasi**:
  * `php artisan optimize:clear` & `php artisan view:cache` (**PASS**).
  * `scan_xdata.php` & `check_service_forms.php` (**PASS**, 0 kutip rusak di `services/create` & `services/edit`, HTTP 200, keseimbangan `<div>` 62/62 & 55/55).
  * Uji E2E real browser headless Chrome (`e2e_service_benefit.mjs` via CDP di `http://127.0.0.1:8000` menggunakan data berisi tanda kutip & `<Test>`): **PASS** (`0` runtime JS exceptions, 3 input benefit default tampil, klik `+ Tambah Benefit` menambah baris 3→5, hapus baris bekerja 5→4, submit Create & Edit tersimpan presisi ke MySQL, `cleanup` selesai bersih).

---

### [2026-11-11 13:07 WITA] — Penyesuaian Posisi Tombol `+ Tambah Benefit` pada Form Paket Layanan Fotografer

* **Modul / Fitur**:
  - Form Tambah & Edit Paket Layanan Fotografer (`photographer/services/create.blade.php`, `photographer/services/edit.blade.php`).
* **File yang Diubah**:
  * `resources/views/photographer/services/create.blade.php`
  * `resources/views/photographer/services/edit.blade.php`
* **Rincian Perubahan (Before vs After)**:
  * **Before**:
    * Tombol `+ Tambah Benefit` berada sejajar di sisi kanan (`flex items-center justify-between` dengan `mr-12`) terpisah jauh dari label `Rincian Benefit & Fasilitas yang Didapat Klien *`.
  * **After**:
    * Memindahkan tombol `+ Tambah Benefit` ke posisi tepat di bawah label `Rincian Benefit & Fasilitas yang Didapat Klien *` menggunakan tata letak vertikal rata kiri (`flex flex-col items-start gap-1.5 mb-2.5`) tanpa mengubah warna, tipografi, maupun atribut tombol lainnya.
* **Standar / Rule README yang Dipatuhi**:
  * Rule #49 (Fokus ketat terhadap instruksi posisi/layout tanpa mengubah styling lain yang tidak diperintahkan).
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:cache` (**PASS**).
  * Render HTTP & keseimbangan tag HTML untuk `/photographer/services/create` dan `/photographer/services/{id}/edit` (**PASS**).

---

### [2026-11-11 12:00 WITA] — Penambahan Indikator Tahapan Global Fotografer (Opsi A Strip Stepper Lintas Halaman)

* **Modul / Fitur**:
  - Layout utama dashboard fotografer (`layouts/photographer.blade.php`), partial komponen indikator tahapan global (`partials/photographer/stage-stepper.blade.php`), dan perapian kartu kelengkapan profil (`photographer/profile/index.blade.php`).
* **File yang Diubah / Ditambahkan**:
  * `resources/views/partials/photographer/stage-stepper.blade.php` (baru)
  * `resources/views/layouts/photographer.blade.php`
  * `resources/views/photographer/profile/index.blade.php`
* **Rincian Perubahan (Before vs After)**:
  * **Before**:
    * Setelah profil fotografer lolos verifikasi Admin tahap 1, kartu `Kelengkapan Profil` di halaman `Profil` disembunyikan sehingga halaman `Profil`, `Paket Layanan`, `Portofolio`, dan `Jadwal` tampil polos tanpa petunjuk bahwa akun masih berada di tahap persiapan etalase dan belum tayang di katalog.
    * Petunjuk persiapan etalase hanya tampil di halaman `Dashboard`.
    * Kartu `Kelengkapan Profil` di `photographer/profile/index.blade.php` masih menggunakan 4 bulatan titik status (`w-2 h-2 rounded-full`) dan bullet karakter (`•`), serta tetap muncul saat profil sudah 100% menunggu verifikasi.
  * **After**:
    * **Global Stage Stepper (`partials/photographer/stage-stepper.blade.php`)**: Ditampilkan konsisten di bagian atas konten (`layouts/photographer.blade.php`) pada seluruh halaman fotografer (kecuali `photographer/chat*` dan halaman `Dashboard` setelah tahap 1 lolos agar tidak menduplikasi kartu `Persiapan Tayang Etalase`). Menampilkan 3 tahap linear (`1. Profil`, `2. Etalase`, `3. Review Tayang`) dengan nilai progres (`X%` dan `0/3`–`3/3 Lengkap`) menggunakan teks netral standar (`text-gray-500 dark:text-gray-400`, selaras Dashboard), tautan langkah berikutnya yang belum selesai, daftar ringkas status 3 syarat etalase saat tahap 2 berjalan, catatan penolakan etalase bila ditolak, serta tombol langsung `Ajukan Tayang` / `Ajukan Ulang Tayang` ketika syarat `3/3` lengkap. Komponen otomatis hilang menyeluruh setelah `isPublishedToCatalog()` terpenuhi.
    * **Perapian `photographer/profile/index.blade.php`**: Menghapus 4 bulatan dot status dan divider kartu pada `Kelengkapan Profil` sesuai Rule #41 dan Rule #67, serta membatasi kartu tersebut hanya muncul saat biodata profil masih kurang (`!$isComplete`) agar tidak berduplikasi dengan strip tahapan global.
* **Standar / Rule README yang Dipatuhi**:
  * Rule #14 & #61 (teks status berwarna murni tanpa box/pill), Rule #41 (whitespace tanpa divider berlebih), Rule #47, Rule #49, Rule #67 (bebas dot badge `rounded-full` / `•`), Rule #70 (copywriting to-the-point bebas `&` redundan), Rule #72 (single chevron SVG minimal), Rule #88 (konfirmasi modal `Ajukan Tayang`), Rule #90 (anti-double UI dan tersembunyi otomatis setelah tayang).
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:cache` & `npm run build` (**PASS**, zero errors).
  * `verify_stage_stepper.php` (`72/72` assertions **PASS** mencakup seluruh halaman fotografer published vs unpublished dan simulasi transisi tahap 1 & tahap 2).
  * `verify_two_stage_flow.php` (`74/74` assertions **PASS**) & `audit_rendered_views.php` (`12/12` views **PASS**, keseimbangan tag HTML + nol dot badge).

---

### [2026-11-11 11:06 WITA] — Implementasi Alur Verifikasi Dua Tahap Fotografer (Profil Mitra dan Review Etalase Tayang Publik)

* **Modul / Fitur**:
  - Skema & model kelayakan etalase (`RolePhotographer`), alur pengajuan tayang fotografer (`DashboardController`, `AvailabilityController`, `ServiceController`, `PortfolioController`), panel audit Admin 2 tahap (`Admin/DashboardController`, `PhotographerVerificationController`, `admin/photographers/show.blade.php`, `admin/photographers/index.blade.php`), serta proteksi keterlihatan katalog dan transaksi publik (`routes/web.php`, `PhotographerController`, `BookingController`, `ChatController`, `ProfileController`, `ClientFavoriteController`).
* **File yang Diubah / Ditambahkan**:
  * `database/migrations/2026_11_11_000001_add_storefront_review_fields_to_role_photographers_table.php` (baru)
  * `database/seeders/PhotographerSeeder.php`
  * `app/Models/RolePhotographer.php`
  * `app/Http/Controllers/Auth/AuthController.php`
  * `app/Http/Controllers/Public/PhotographerController.php`
  * `app/Http/Controllers/Public/BookingController.php`
  * `app/Http/Controllers/Public/ChatController.php`
  * `app/Http/Controllers/Public/ProfileController.php`
  * `app/Http/Controllers/Client/ClientFavoriteController.php`
  * `app/Http/Controllers/Photographer/OnboardingController.php`
  * `app/Http/Controllers/Photographer/DashboardController.php`
  * `app/Http/Controllers/Photographer/ServiceController.php`
  * `app/Http/Controllers/Photographer/PortfolioController.php`
  * `app/Http/Controllers/Photographer/AvailabilityController.php`
  * `app/Http/Controllers/Admin/DashboardController.php`
  * `app/Http/Controllers/Admin/PhotographerVerificationController.php`
  * `routes/web.php`
  * `resources/views/photographer/dashboard.blade.php`
  * `resources/views/photographer/availability/index.blade.php`
  * `resources/views/admin/dashboard.blade.php`
  * `resources/views/admin/photographers/index.blade.php`
  * `resources/views/admin/photographers/show.blade.php`
* **Rincian Perubahan (Before vs After)**:
  * **Before**:
    * Persetujuan profil tahap 1 oleh Admin (`status_verifikasi = 'terverifikasi'`) otomatis membuat akun fotografer langsung tampil di beranda, daftar fotografer, katalog, dan endpoint booking/chat publik meski fotografer belum memiliki paket layanan, portofolio, maupun konfirmasi jadwal kerja.
    * Katalog dan halaman beranda hanya menyaring `where('is_onboarded', true)` dan `status_verifikasi`, tanpa kolom status review etalase ataupun pemeriksaan kelengkapan layanan, media portofolio, serta jadwal kerja mingguan.
    * Halaman detail verifikasi fotografer Admin hanya menampilkan data biodata, tanpa panel audit paket layanan, album portofolio, maupun jadwal sebelum tayang.
  * **After**:
    * **Migrasi & Seeder**: Menambahkan kolom `status_etalase` (`belum_diajukan`, `menunggu`, `disetujui`, `ditolak`), `catatan_penolakan_etalase`, `jadwal_dikonfirmasi`, `etalase_diajukan_at`, dan `etalase_disetujui_at` pada `role_photographers`; melakukan backfill otomatis hanya bagi fotografer existing yang sudah memenuhi syarat etalase lengkap (`#1–#5`), sedangkan fotografer tanpa paket/portofolio/konfirmasi jadwal (`#6 kenji`) otomatis berada di `status_etalase = 'belum_diajukan'`. `PhotographerSeeder` juga diperbarui agar meng-seed 5 akun demo secara eksplisit dan tidak menimpa seluruh baris `RolePhotographer`.
    * **Model `RolePhotographer`**: Menambahkan helper `hasValidServices()`, `hasValidPortfolios()`, `hasConfiguredSchedule()`, `isStorefrontComplete()`, `getStorefrontChecklist()`, `isStorefrontPending()`, `isStorefrontApproved()`, `isStorefrontRejected()`, `isPublishedToCatalog()`, `scopePublished()`, serta `syncStorefrontIntegrity()`.
    * **Alur Tahap 2 Fotografer**: `DashboardController@index` menyuplai `$storefrontChecklist`; panel `Persiapan Tayang Etalase` pada `photographer/dashboard.blade.php` memandu pemenuhan 3 syarat (`Paket Layanan`, `Portofolio`, `Jadwal`), tombol `Ajukan Tayang` / `Ajukan Ulang Tayang` menuju `POST /photographer/storefront/submit`, serta tautan `Lihat Profil Publik` disembunyikan sampai `isPublishedToCatalog()` bernilai benar (dan panel checklist otomatis disembunyikan saat etalase sudah live sesuai Rule #90). `AvailabilityController@updateWorkingHours()` menandai `jadwal_dikonfirmasi = true`, sementara `ServiceController@destroy`, `PortfolioController@destroy`, dan penutupan seluruh 7 hari kerja di `AvailabilityController@updateWorkingHours` otomatis memanggil `syncStorefrontIntegrity()` untuk menonaktifkan tayang jika syarat etalase tidak lagi terpenuhi.
    * **Audit & Keputusan Admin**: `PhotographerVerificationController` dan `admin/photographers/show.blade.php` menambahkan navigasi tab teks horizontal (`Profil Mitra` & `Etalase Tayang` sesuai Rule #78), kartu audit 3 seksi (`1. Paket Layanan`, `2. Portofolio` terhubung ke Lightbox `openImagePreview`, `3. Jadwal` 7 hari beserta tanggal ditutup), serta aksi `Setujui Tayang`, `Tolak Etalase` (modal Rule #59), dan `Batalkan Tayang`. Antrean `DashboardController@index` serta filter/kartu `admin/photographers/index.blade.php` memperhitungkan antrean review tahap 1 maupun tahap 2 dengan status teks murni (`Menunggu Profil`, `Lengkapi Etalase`, `Review Etalase`, `Tayang`, `Profil Ditolak`, `Etalase Ditolak`).
    * **Guard Keterlihatan Publik & Transaksional**: Halaman `/`, `/fotografer`, `/fotografer/katalog`, `/fotografer/{id}`, `/user/{id}`, `/booking/create`, `/booking/store`, `/chat/start/{photographerId}`, `/chat?photographer=`, dan `/client/favorites/{id}/toggle` seluruhnya menyaring dan memblokir fotografer yang belum lolos tahap 1 dan tahap 2 (`published()` / `isPublishedToCatalog()`).
* **Standar / Rule README yang Dipatuhi**:
  * Rule #22, #41, #49, #59, #67, #70, #74, #78, #80, #90, #91, #94, #97, #104.
* **Hasil Pengujian & Verifikasi**:
  * `php -l` seluruh file PHP baru dan ubahan: **PASS** (0 error sintaks).
  * `php artisan migrate`, `php artisan optimize:clear`, `php artisan view:cache`, dan `npm run build` (`vite v8.2.1`): **PASS**.
  * Skrip uji E2E terisolasi transaksi (`verify_two_stage_flow.php`): **PASS** (`74/74 assertions`, `0 failures` — memverifikasi ketersembunyian `#6 kenji` pada beranda, katalog, detail profil, user profile, booking create/store, chat start, dan favorit; simulasi penuh pengajuan tahap 2, penolakan etalase + catatan, persetujuan tayang hingga `#6` tampil HTTP 200 & muncul di katalog, pencabutan tayang oleh Admin, serta degradasi otomatis via `syncStorefrontIntegrity()` saat paket terakhir dihapus atau seluruh 7 hari kerja ditutup).
  * Audit keseimbangan tag HTML/Blade + nihil dot badges (`audit_rendered_views.php`) lintas 12 render halaman Admin & Fotografer: **PASS**.

---

### [2026-11-11 10:33 WITA] — Penyeragaman Tampilan Field Instagram dan Penghapusan Tautan Chat WhatsApp pada Verifikasi Fotografer Admin

* **Modul / Fitur**:
  - Bagian `1. Kontak` pada halaman Verifikasi Fotografer Admin (`resources/views/admin/photographers/show.blade.php`).
* **File yang Diubah**:
  * `resources/views/admin/photographers/show.blade.php`
* **Rincian Perubahan (Before vs After)**:
  * **Before**:
    * Nilai pada field **Instagram** dibungkus tag tautan `<a>` dengan kelas warna kuning (`text-amber-600 dark:text-amber-400`), sehingga terlihat kontras dan tidak seragam dengan field `Nama Lengkap`, `Email`, serta `Nomor WhatsApp` yang memakai warna teks netral.
    * Di sisi kanan label **Nomor WhatsApp** terdapat teks tautan `Chat WhatsApp` berwarna hijau (`wa.me/...`) yang tidak diperlukan dalam konteks verifikasi data.
    * Variabel bantu `$waClean`, `$rawIg`, dan `$igUrl` terpasang di blok `@php` atas untuk merender kedua elemen tersebut.
  * **After**:
    * Menghapus tautan `Chat WhatsApp` dari label **Nomor WhatsApp** agar sejajar bersih dengan label field lainnya.
    * Menghapus pembungkus tautan `<a href="...">` beserta kelas warna kuning (`text-amber-600 dark:text-amber-400`) pada nilai **Instagram**, dan menyeragamkan gayanya ke teks netral (`font-bold text-gray-900 dark:text-white` saat terisi atau `font-medium text-gray-400 dark:text-gray-500` saat kosong `-`) sama persis dengan `Nama Lengkap`, `Email`, dan `Nomor WhatsApp`.
    * Menghapus blok variabel PHP `$waClean`, `$rawIg`, dan `$igUrl` yang sudah tidak terpakai.
* **Standar / Rule README yang Dipatuhi**:
  * Rule #42 (Strict Scope), Rule #70 (Copywriting Ringkas Tanpa Teks atau Simbol Berlebihan), Konsistensi Read-Only Profil Fotografer (`photographer/profile/index.blade.php`).
* **Hasil Pengujian & Verifikasi**:
  * Skrip uji HTTP 5 skenario (`verify_admin_photographer_show.php`): **PASS** (memastikan nihil `Chat WhatsApp`, nihil URL `wa.me/`, nihil warna `text-amber-`/`text-emerald-` pada `1. Kontak`, tag balance 73 `<div>` dan 11 `<button>` seimbang).
  * Render langsung Blade untuk data fotografer aktif (`RolePhotographer #6`) + `artisan view:cache` + `artisan optimize:clear`: **PASS**.

---

### [2026-11-11 10:31 WITA] — Perbaikan Keterlambatan Submit Notifikasi Reverb Offline dan Hilangnya Toast Flash Session Akibat Background Polling

* **Modul / Fitur**:
  - Broadcast real-time notifikasi dan chat (`RealtimeBroadcastService`), perlindungan session flash middleware (`SafeStartSession`), heartbeat polling header/layout (`chatNotifier`, `headerNotifApp`), inisialisasi Toast Notification pasca-preloader, serta null-guard shortcut pencarian (`resources/js/index.js`).
* **File yang Diubah / Ditambahkan**:
  * `app/Services/RealtimeBroadcastService.php` (baru)
  * `app/Http/Middleware/SafeStartSession.php` (baru)
  * `config/broadcasting.php`
  * `bootstrap/app.php`
  * `app/Models/Notification.php`
  * `app/Http/Controllers/Public/ChatController.php`
  * `resources/views/layouts/photographer.blade.php`
  * `resources/views/partials/shared/header.blade.php`
  * `resources/views/partials/public/navbar.blade.php`
  * `resources/views/partials/shared/preloader.blade.php`
  * `resources/views/partials/shared/toast-notification.blade.php`
  * `resources/js/index.js`
* **Rincian Perubahan (Before vs After)**:
  * **Before**:
    * Ketika server Reverb (`localhost:8080`) sedang offline, setiap pemanggilan `Notification::send()` (misalnya pengajuan ulang profil fotografer yang ditolak menuju N admin di `ProfileController::updateProfile()`) memblokir proses PHP secara sinkron ~2,25 detik per admin (~4,5+ detik) sehingga browser menggantung di ikon loading tab sebelum menerima redirect.
    * Selama form submit tertahan lebih dari 3 detik, polling `chatNotifier` (`GET /chat/unread-count` tiap 3 detik) dan `headerNotifApp` (`GET /notifications/unread` tiap 30 detik) berjalan melalui `StartSession::saveSession()`, memanggil `ageFlashData()` serta mengonsumsi `session('success')` sebelum halaman tujuan `GET /photographer/profile` sempat me-render Toast Notification. Selain itu `x-data="headerNotifApp()" x-init="init()"` memicu inisialisasi ganda di Alpine v3, `fetchNotifs()` belum mengirim header `X-Requested-With`, dan `resources/js/index.js` melempar `TypeError` karena `#search-button` tidak ada di DOM.
  * **After**:
    * `RealtimeBroadcastService::broadcastSafely()` mengecek ketersediaan port Reverb via TCP preflight non-blocking (1x probe cepat dengan cache proses 2 detik; saat Reverb aktif terdeteksi dalam ~1 ms dan tetap broadcast normal) serta `config/broadcasting.php` menetapkan `connect_timeout: 0.4` dan `timeout: 1.5` agar `POST /profile/update` tidak lagi hang berdetik-detik saat Reverb mati.
    * `SafeStartSession` menggantikan `StartSession` pada grup middleware `web` di `bootstrap/app.php` sehingga endpoint polling read-only dan `GET` JSON (`chat/unread-count`, `notifications/unread`, `chat/messages/*`, `chat/typing`, `broadcasting/auth`, `api/*`) tetap membaca identitas `Auth` dari session tanpa memanggil `saveSession()` / `ageFlashData()` atau menimpa `_previous.url`.
    * Komponen `chatNotifier` dan `headerNotifApp` mengirimkan header `Accept: application/json` dan `X-Requested-With: XMLHttpRequest`, membersihkan timer/request via `AbortController` saat `beforeunload`/`pagehide`, serta menghilangkan duplikasi pemanggilan `init()` ganda pada `header.blade.php` dan `navbar.blade.php`.
    * `toast-notification.blade.php` menunggu event `app:preloader-hidden` dari `preloader.blade.php` (dengan fallback 1500 ms) saat preloader masih aktif agar animasi masuk dan durasi tampil 4,5 detik Toast dimulai penuh sesudah layar kuning preloader selesai.
    * Null-guard ditambahkan untuk `searchButton` dan `searchInput` di `resources/js/index.js`.
* **Standar / Rule README yang Dipatuhi**:
  * Rule #19 (Toast Notification Shared 4.5 detik & Laravel Flash Session), Konsistensi Layout Dashboard & Real-time Reverb Compatibility.
* **Hasil Pengujian & Verifikasi**:
  * `php -l` seluruh file PHP baru dan ubahan: **PASS**.
  * Bench server offline/online probe (`RealtimeBroadcastService`): Offline probe non-blocking, Online loopback probe `1.12 ms`, waktu eksekusi `POST /profile/update` turun dari `4.533 s` menjadi `0.189 s`: **PASS**.
  * Uji E2E real browser headless Chrome (`http://127.0.0.1:8000`) dengan skenario fotografer `ditolak` memperbaiki Bio, menembak `GET /chat/unread-count` + `GET /notifications/unread` secara konkuren bersamaan dengan `POST /profile/update`: Toast sukses tampil penuh pasca-preloader hilang (`toastCount: 1`), polling notifikasi ganda hilang, `0` JS console exceptions: **PASS**.
  * `npm run build` (`1.16 s`), `artisan optimize:clear`, dan `artisan view:cache`: **PASS**.

---

### [2026-11-10 17:37 WITA] — Penyelarasan Tata Letak Halaman Verifikasi Fotografer Admin (`/admin/photographers/{id}`) dengan Form Profil Fotografer

* **Modul / Fitur**:
  - Halaman detail audit dan verifikasi mitra fotografer Admin (`resources/views/admin/photographers/show.blade.php`).
* **File yang Diubah**:
  * `resources/views/admin/photographers/show.blade.php`
* **Rincian Perubahan (Before vs After)**:
  * **Before**:
    * Versi awal memecah informasi ke dalam 5 kartu terpisah pada grid 2 kolom sehingga kartu `3. BIO` menyisakan ruang kosong panjang di bawahnya, menduplikasi tampilan data identitas dan tombol `Kembali`, memakai bingkai kuning mencolok di sekeliling foto, serta menampilkan 4 titik hijau indikator kelengkapan.
    * Tautan Instagram berbentuk username polos (misal `kenjiro`) terhubung ke URL `https://kenjiro`, dan modal konfirmasi `Verifikasi Fotografer` memakai `type: 'info'` sehingga tombolnya berwarna merah alih-alih hijau emerald.
  * **After**:
    * Tata letak diselaraskan penuh mengikuti struktur kartu tunggal pada `resources/views/photographer/profile/index.blade.php` (`rounded-3xl p-6 sm:p-8 space-y-6`):
      * Bilah status ringkas di atas kartu induk memuat progres bagian `(X/4 bagian)`, status verifikasi, tanggal daftar, persentase kelengkapan, serta daftar field yang belum diisi (tanpa 4 titik status sesuai Rule #67).
      * Banner merah penolakan (`Pendaftaran Ditolak`) tampil tepat di atas bilah kelengkapan saat status `ditolak`, mengikuti posisi di halaman profil fotografer.
      * **1. Kontak** (grid 2 kolom): `Nama Lengkap` (beserta info `Akun:` hanya jika nama akun berbeda), `Email`, `Nomor WhatsApp` (disertai link `Chat WhatsApp` di sisi kanan label), dan `Instagram` (URL resolver mendukung format handle `@username` / `username`).
      * **2. Domisili & Kategori** (grid 2 kolom): `Provinsi`, `Kota / Kabupaten`, `Alamat`, dan `Kategori` (menampilkan hitungan `N dipilih` dan daftar kategori sejajar tinggi field).
      * **3. Bio**: kontainer deskripsi lebar penuh (`min-h-[110px]`) atau teks redup `Belum diisi`.
      * **4. Foto Profil & Sampul** (grid 2 kolom seimbang): `Foto Profil` persegi (`w-28 h-28`) di kiri dan `Foto Sampul` lanskap (`w-full h-44 sm:h-48`) di kanan dengan bingkai netral, overlay lightbox pratinjau penuh saat gambar ada, serta kotak dashed ikon kamera + teks `Belum diunggah` saat kosong (tanpa memicu lightbox).
      * **5. Rekening Bank** (grid 3 kolom): `Nama Bank`, `Nomor Rekening` monospace, dan `Nama Pemilik Rekening`.
    * Tombol `Lihat Etalase Publik` (kondisional jika terverifikasi dan onboarded) serta `< Kembali` berada di kanan atas judul, sedangkan tombol keputusan (`Hapus`, `Tolak Pendaftaran`, `Verifikasi Fotografer` / `Batalkan Verifikasi`) tersusun rata kanan di bagian bawah kartu induk (`Rule #74`).
    * Perbaikan teknis: modal `Verifikasi Fotografer` memakai `type: 'success'` dan `bg-emerald-600` (`Rule #91`), modal tolak dan hapus tetap otomatis terbuka kembali bila ada error validasi server dengan `old()`, kelas Tailwind v4 non-eksisten `dark:hover:bg-gray-750` diperbaiki ke `dark:hover:bg-gray-700`, dan teks bebas kata ganda atau simbol `&` redundan (`Rule #70`).
* **Standar / Rule README yang Dipatuhi**:
  * Rule #22, #41, #42, #51, #59, #61, #62, #67, #70, #72, #74, #91, #94, #95, #96, #97, #104.
* **Hasil Pengujian & Verifikasi**:
  * Skrip uji HTTP render 5 skenario (`verify_admin_photographer_show.php`: menunggu 100%, menunggu belum lengkap + foto null + nama akun berbeda, ditolak dengan catatan, terverifikasi + onboarded, POST `verify`/`unverify`/`reject` + re-open modal error validasi + keseimbangan tag Blade 74 `<div>` & 11 `<button>`): **PASS**.
  * `artisan view:cache` dan `artisan optimize:clear` (PHP 8.4): **PASS**.
  * `npm run build` (Vite production bundle, built in 779ms) + verifikasi token CSS Tailwind v4 + live server `http://127.0.0.1:8000` (HTTP 200): **PASS**.

---

### [2026-11-10 16:58 WITA] — Visibilitas Kartu Kelengkapan Profil Fotografer Berdasarkan Status Verifikasi

* **Modul / Fitur**:
  - Indikator `Kelengkapan Profil` di halaman Kelola Profil Fotografer (`resources/views/photographer/profile/index.blade.php`).
* **File yang Diubah**:
  * `resources/views/photographer/profile/index.blade.php`
* **Rincian Perubahan (Before vs After)**:
  * **Before**: Kartu `Kelengkapan Profil` tampil permanen untuk seluruh status verifikasi fotografer, termasuk saat status sudah `terverifikasi`. Selain itu, saat status `ditolak` dengan isian 100%, label kanan kartu masih menampilkan `Menunggu verifikasi admin`.
  * **After**:
    * Kartu `Kelengkapan Profil` dibungkus kondisi `@if(!$isVerified)` menggunakan `$photographer->isVerified()` sehingga otomatis disembunyikan apabila akun fotografer sudah berstatus `terverifikasi`.
    * Kartu tetap tampil untuk status di luar `terverifikasi` (`menunggu` maupun `ditolak`) dengan tiga label status langsung: `Kurang N data` jika belum 100%, `Ditolak admin` jika status `ditolak`, serta `Menunggu verifikasi admin` jika sudah lengkap menanti peninjauan admin.
* **Standar / Rule README yang Dipatuhi**:
  * Rule #42 (Strict Scope), Rule #70 (Copywriting Ringkas tanpa simbol `&`), Rule #90 (Anti-Double UI).
* **Hasil Pengujian & Verifikasi**:
  * Simulasi render Blade lintas 5 skenario status DB (transaksi rollback via `verify_profile_progress_visibility.php`): **PASS** (`menunggu` belum lengkap = VISIBLE, `menunggu` 100% = VISIBLE, `ditolak` 100% = VISIBLE + `Ditolak admin`, `terverifikasi` = HIDDEN, batal verifikasi = VISIBLE).
  * `artisan view:cache` lalu `artisan optimize:clear` (PHP 8.4): **PASS**.
  * `npm run build`: **PASS** (built in 759ms).

---

### [2026-11-10 16:47 WITA] — Penyeragaman Feedback Pasca-Simpan Berbasis Toast Notification (Eliminasi Kotak Alert Inline)

* **Modul / Fitur**:
  - Penghapusan kotak alert flash inline (`session('success')`, `session('error')`, `session('warning')`, `session('info')`) serta kotak ringkasan `$errors->any()` pada 16 view di modul Fotografer, Admin, serta Publik yang sudah menggunakan layout utama bertumpu pada `partials/shared/toast-notification.blade.php`.
* **File yang Diubah**:
  * `resources/views/photographer/profile/index.blade.php`
  * `resources/views/photographer/availability/index.blade.php`
  * `resources/views/photographer/bookings/index.blade.php`
  * `resources/views/photographer/portfolio/index.blade.php`
  * `resources/views/photographer/services/index.blade.php`
  * `resources/views/photographer/faqs/index.blade.php`
  * `resources/views/admin/admins/index.blade.php`
  * `resources/views/admin/categories/index.blade.php`
  * `resources/views/admin/elearning/index.blade.php`
  * `resources/views/admin/photographers/show.blade.php`
  * `resources/views/admin/contests/index.blade.php`
  * `resources/views/public/profile/index.blade.php`
  * `resources/views/public/booking/invoice.blade.php`
  * `resources/views/public/contests/show.blade.php`
  * `resources/views/public/contests/propose.blade.php`
  * `resources/views/public/contests/my-proposals.blade.php`
* **Rincian Perubahan (Before vs After)**:
  * **Before**: Halaman yang mewarisi master layout (`layouts.app`, `layouts.photographer`, `layouts.admin`) memunculkan dua notifikasi sekaligus saat menyimpan data: Toast melayang di pojok kanan atas ditambah kotak alert penuh di bagian atas konten. Form tertentu juga masih memakai kotak ringkasan `$errors->any()` di atas form.
  * **After**:
    * Seluruh kotak alert flash inline dihapus di 16 halaman agar umpan balik simpan/ubah/hapus murni memakai `partials/shared/toast-notification.blade.php`.
    * Kotak ringkasan `$errors->any()` dihapus pada form yang sudah memiliki validasi inline per field.
    * Ditambahkan fallback `@error('banner_url')` inline pada `public/contests/propose.blade.php`, `@error('alamat')` serta `@error('deskripsi_bio')` pada `public/profile/index.blade.php`, serta pemicuan `@dispatch('show-toast')` saat ada error validasi server pada form modal/lipat di `photographer/faqs/index.blade.php` maupun `public/contests/show.blade.php`.
    * Banner status permanen berbasis database (alasan penolakan profil fotografer pada `photographer/profile/index.blade.php` serta status menunggu verifikasi transfer pada `public/contests/show.blade.php`) dan halaman auth mandiri (`public/auth/*`) tetap dipertahankan.
* **Standar / Rule README yang Dipatuhi**:
  * Rule #19 (Toast Notification Shared), Rule #42 (Strict Scope), Rule #70 (Copywriting Ringkas tanpa simbol `&`).
* **Hasil Pengujian & Verifikasi**:
  * Scan audit seluruh view (`audit_flash_boxes.php` + `find_all_session.php`): **PASS** (0 kotak flash duplikat tersisa di seluruh halaman ber-layout).
  * `artisan view:cache` + `artisan optimize:clear` (PHP 8.4): **PASS**.
  * `npm run build` (Vite production bundle): **PASS** (built in 859ms).

---

### [2026-11-10 16:22 WITA] — Validasi Ukuran dan Format File Real-Time Lintas Sistem (19 Input Upload)

* **Modul / Fitur**:
  - Helper JS Terpusat (`resources/views/partials/shared/file-upload-validator-js.blade.php`) dan integrasi di seluruh master layout (`layouts/app.blade.php`, `layouts/photographer.blade.php`, `layouts/admin.blade.php`).
  - Seluruh 19 `<input type="file">` aktif di modul Fotografer, Publik/Klien, dan Admin, termasuk pembaruan pesan validasi berbahasa Indonesia pada 9 controller backend.
* **File yang Diubah**:
  * `resources/views/partials/shared/file-upload-validator-js.blade.php` *(File Baru)*
  * `resources/views/layouts/app.blade.php`
  * `resources/views/layouts/photographer.blade.php`
  * `resources/views/layouts/admin.blade.php`
  * `resources/views/photographer/profile/index.blade.php`
  * `resources/views/photographer/portfolio/create.blade.php`
  * `resources/views/photographer/portfolio/edit.blade.php`
  * `resources/views/photographer/chat/index.blade.php`
  * `resources/views/public/profile/index.blade.php`
  * `resources/views/public/booking/invoice.blade.php`
  * `resources/views/public/chat/index.blade.php`
  * `resources/views/public/forum/index.blade.php`
  * `resources/views/public/contests/propose.blade.php`
  * `resources/views/public/contests/show.blade.php`
  * `resources/views/admin/contests/create.blade.php`
  * `resources/views/admin/contests/edit.blade.php`
  * `resources/views/admin/elearning/create.blade.php`
  * `resources/views/admin/elearning/edit.blade.php`
  * `resources/views/admin/elearning/curriculum.blade.php`
  * `resources/views/admin/transactions/index.blade.php`
  * `app/Http/Controllers/Public/ProfileController.php`
  * `app/Http/Controllers/Photographer/PortfolioController.php`
  * `app/Http/Controllers/Public/ContestProposalController.php`
  * `app/Http/Controllers/Public/ContestController.php`
  * `app/Http/Controllers/Public/BookingController.php`
  * `app/Http/Controllers/Public/ChatController.php`
  * `app/Http/Controllers/Admin/ContestController.php`
  * `app/Http/Controllers/Admin/ElearningController.php`
  * `app/Http/Controllers/Admin/TransactionController.php`
* **Rincian Perubahan (Before vs After)**:
  * **Before**: Pemilihan file melebihi batas ukuran (misalnya `foto_sampul_file` > 20 MB di profil fotografer) maupun format non-gambar tidak ditolak seketika di sisi klien. File besar tetap menempel di input, memicu loading unggahan panjang saat submit, dan berakhir dengan pesan error bahasa Inggris Laravel (`The ... must not be greater than ... kilobytes.`) di banner atas.
  * **After**:
    * Dibuat helper global `window.formatFileSizeMB(bytes)` dan `window.validateUploadFiles(inputOrFiles, options)` di `partials/shared/file-upload-validator-js.blade.php` serta dimuat di ketiga master layout.
    * Input file tunggal menolak file berukuran melebihi batas (`5 MB` chat/proposal/bukti transfer lomba, `10 MB` avatar/thumbnail/poster admin/invoice/payout/CV, `20 MB` sampul profil/portofolio, `50 MB` media kurikulum) maupun format tidak sesuai langsung saat `@change`, mengosongkan nilai input (`input.value = ''`), menjaga pratinjau sebelumnya agar tidak tertimpa, memberi border merah pada kontainer, dan menampilkan pesan error inline berbahasa Indonesia persis di bawah field.
    * Input multi-file (`foto_files[]` pada tambah/edit portofolio serta modal forum) menyaring file melalui `DataTransfer` agar file yang valid tetap masuk sedangkan file yang kebesaran/tidak valid ditolak sambil menampilkan nama file penolakan secara inline.
    * Aturan validasi serta pesan error backend pada controller terkait diselaraskan ke bahasa Indonesia yang singkat dan konsisten (`mimes:jpeg,png,jpg,webp`, ekstensi CV `pdf,doc,docx,zip,rar`, serta batas MB sesuai frontend).
* **Standar / Rule README yang Dipatuhi**:
  * Rule #51, #70, #94, #95, #97, #104 (Copywriting berbahasa Indonesia ringkas, to-the-point, tanpa simbol `&` redundan maupun teks Inggris).
* **Hasil Pengujian & Verifikasi**:
  * Scan seluruh Blade & cek kelengkapan validasi client-side + server-side (`verify_realtime_file_validation.php`): **44/44 PASS** (seluruh 19 `<input type="file">` aktif tercover, zero pesan `"kilobytes"`).
  * `php -l` seluruh controller termodifikasi (PHP 8.4): **PASS**.
  * `artisan view:cache` & `artisan view:clear` (PHP 8.4): **PASS**.
  * `npm run build` (Vite production bundle): **PASS** (built in 849ms).

---

### [2026-11-10 14:44 WITA] — Pemisahan Tab Data Profil dengan Ubah Kata Sandi pada Profil Fotografer

* **Modul / Fitur**:
  - Halaman Kelola Profil Fotografer (`resources/views/photographer/profile/index.blade.php`) dan Handler Pembaruan Kata Sandi (`app/Http/Controllers/Public/ProfileController.php`).
* **File yang Diubah**:
  * `resources/views/photographer/profile/index.blade.php`
  * `app/Http/Controllers/Public/ProfileController.php`
* **Rincian Perubahan (Before vs After)**:
  * **Before**: Form `Kata Sandi` diletakkan tepat di bawah `5. Rekening Bank` dengan judul bernomor `6. Kata Sandi` serta tombol `Perbaharui Data` terpisah dan placeholder `••••••••`. Fotografer baru yang sudah membuat kata sandi saat pendaftaran mengira bagian ini langkah wajib ke-6 untuk melengkapi profil verifikasi, sehingga terblokir pesan `"Kata sandi saat ini wajib diisi."` saat mencoba menyimpan profil.
  * **After**:
    * Memisahkan halaman `Kelola Profil` menjadi dua tab horizontal (`Data Profil` | `Ubah Kata Sandi`) bergaya active text underline tanpa garis pembatas penuh.
    * Tab `Data Profil` hanya menampilkan indikator kelengkapan profil serta bagian wajib verifikasi nomor `1` hingga `5` tanpa input kata sandi sama sekali.
    * Tab `Ubah Kata Sandi` bersifat opsional dengan judul `Ubah Kata Sandi` (tanpa nomor `6.`), teks penjelas bahwa kata sandi sudah aktif sejak pendaftaran, placeholder instruksional `"Masukkan kata sandi saat ini"` (menggantikan `••••••••`), atribut `autocomplete`, banner alert `session('error')`, serta sinkronisasi error validasi server dengan state Alpine JS dan fallback autofill DOM.
    * `ProfileController::updatePassword()` kini menyertakan `with('active_tab', 'password')`, `withErrors(['current_password' => ...])`, serta `with('error', ...)` agar tab `Ubah Kata Sandi` tetap terbuka otomatis setelah submit ubah kata sandi maupun ketika validasi gagal.
* **Standar / Rule README yang Dipatuhi**:
  * Rule #20 (Visual Hierarchy & Card Spacing), Rule #22 (Anti Decorative Icon Slop), Rule #49 (Strict Scope Compliance), Rule #51 & #70 (Concise Copywriting tanpa simbol `&`), Rule #73 (Layout Card Password Horizontal 3 Kolom), Rule #76 (`Perbaharui Data`), Rule #78 (Tab Navigasi Horizontal Underline), Rule #104 (Placeholder Instruksional).
* **Hasil Pengujian & Verifikasi**:
  * `php -l app/Http/Controllers/Public/ProfileController.php` (PHP 8.4): **PASS** (No syntax errors detected).
  * `artisan view:cache` & `artisan view:clear` (PHP 8.4): **PASS** (Blade compiled & cleared cleanly).
  * Verifikasi end-to-end (`GET /photographer/profile`, `?tab=password`, `POST /profile/update` tanpa password, `POST /profile/password` salah/kosong/valid, HTML `<div>` balance 73/73 Blade & 114/114 HTML): **PASS** (24/24 checks passed, 0 failed).
  * `npm run build`: **PASS** (Vite bundle compiled in 931ms).

---

### [2026-11-10 11:45 WITA] — Implementasi Status-Aware Access Notice Modal untuk Fotografer HOLD serta Role Guard Lintas Halaman Publik

* **Modul / Fitur**:
  - Model Pengguna serta Payload Status Fotografer (`User.php`), Middleware Verifikasi Fotografer (`EnsurePhotographerVerified.php`), dan Definisi Rute Publik Terproteksi (`routes/web.php`).
  - Komponen Shared Modal (`partials/shared/access-notice-modal.blade.php` serta integrasi di `layouts/app.blade.php`).
  - Halaman Publik Komunitas Forum (`forum/index.blade.php`, `forum/show.blade.php`), Lomba Foto (`contests/index.blade.php`, `contests/show.blade.php`, `contests/my-proposals.blade.php`), E-Learning (`elearning/index.blade.php`, `elearning/katalog.blade.php`, `elearning/show.blade.php`), dan Profil maupun Katalog Fotografer (`photographers/show.blade.php`, `photographers/katalog.blade.php`).
* **File yang Diubah**:
  * `app/Models/User.php`
  * `app/Http/Middleware/EnsurePhotographerVerified.php`
  * `routes/web.php`
  * `resources/views/partials/shared/access-notice-modal.blade.php` *(File Baru)*
  * `resources/views/layouts/app.blade.php`
  * `resources/views/public/forum/index.blade.php`
  * `resources/views/public/forum/show.blade.php`
  * `resources/views/public/contests/index.blade.php`
  * `resources/views/public/contests/show.blade.php`
  * `resources/views/public/contests/my-proposals.blade.php`
  * `resources/views/public/elearning/index.blade.php`
  * `resources/views/public/elearning/katalog.blade.php`
  * `resources/views/public/elearning/show.blade.php`
  * `resources/views/public/photographers/show.blade.php`
  * `resources/views/public/photographers/katalog.blade.php`
* **Rincian Perubahan**:
  1. **Deteksi Status HOLD serta Payload Popup Dinamis (`User.php`)**:
     - **Before**: Belum ada helper terpusat untuk mengecek status HOLD fotografer dan menyusun pesan status secara seragam.
     - **After**: Ditambahkan `isHoldPhotographer()` dan `getPhotographerHoldPopupPayload()` dengan prioritas pengecekan `ditolak` -> profil belum lengkap (`missingCount`) -> menunggu verifikasi Admin, lengkap dengan target redirect `route('photographer.profile')`.
  2. **Hardening Middleware dan Rute Terproteksi (`EnsurePhotographerVerified.php`, `routes/web.php`)**:
     - **Before**: Beberapa endpoint forum (like, dislike, store, comment, report, delete), lomba (daftar, bayar, submit, ajukan lomba, riwayat), dan e-learning (katalog, show, checkout, sertifikat, toggle progress) hanya dijaga `auth` atau tanpa `photographer.verified`, sehingga fotografer HOLD masih bisa menembus rute aksi. Selain itu pengecekan `!isProfileComplete()` didahulukan di atas status `ditolak` dan request AJAX masih mengembalikan redirect 302.
     - **After**: Seluruh rute aksi Forum, Lomba, serta modul E-Learning kini wajib melewati `photographer.verified`; prioritas cek diperbaiki (`ditolak` lebih dulu daripada belum lengkap); request JSON/AJAX dari fotografer HOLD kini menerima respons HTTP `403` terstruktur (`hold_state`, `message`, `redirect`).
  3. **Komponen Universal Access Notice Modal (`access-notice-modal.blade.php`, `layouts/app.blade.php`)**:
     - **Before**: Fotografer HOLD yang mengklik `Reservasi`, `Chat`, atau tombol terproteksi langsung diredirect paksa ke dashboard tanpa penjelasan kontekstual di halaman saat itu.
     - **After**: Menambahkan dialog modal global selaras desain `Login Diperlukan` (`max-w-[420px] rounded-2xl`, close `X` polos kanan atas, judul centered tanpa ikon, copy ringkas anti-AI-slop tanpa simbol `&`, serta tombol CTA penuh `bg-amber-400`). Fungsi global `openPhotographerHoldModal(actionPhrase)` menampilkan pesan kontekstual (`Profil Belum Lengkap` -> `Lengkapi Profil`, `Pendaftaran Akun Ditolak` plus catatan Admin -> `Perbaiki Profil`, `Menunggu Verifikasi` -> `Lihat Profil`) yang mengarahkan ke Menu Profil Dashboard Fotografer hanya saat CTA diklik. Fungsi `openClientOnlyModal(actionPhrase)` menampilkan modal `Khusus Akun Klien` dengan tombol `Mengerti` tanpa redirect.
  4. **Gating Trigger Aksi Frontend Lintas Modul (`forum/*`, `contests/*`, `elearning/*`, `photographers/*`)**:
     - **Before**: Akun fotografer HOLD masih dapat membuka modal `#newPostModal`, form komentar/balasan/lapor/like/dislike forum, form pendaftaran/unggah lomba, tautan `Mulai Belajar Gratis` / `Lihat Lebih Banyak Modul` ke `/elearning/katalog`, maupun menembak endpoint `Reservasi`, `Chat`, dan `Simpan` yang berujung redirect paksa; serta akun Fotografer terverifikasi atau Admin di profil fotografer langsung terlempar saat menekan fitur khusus Klien.
     - **After**: Seluruh trigger aksi tersebut kini dicegat di tempat oleh `openPhotographerHoldModal(...)` bagi fotografer HOLD (termasuk menyembunyikan DOM form sensitif seperti `#newPostModal`, form komentar/reply, serta form pendaftaran/unggah karya lomba), dan `openClientOnlyModal(...)` bagi Fotografer terverifikasi serta Admin pada aksi khusus Klien (`Reservasi`, `Chat`, bookmark `Simpan`). Alur Tamu (`Login Diperlukan`) dan Klien tetap berjalan normal tanpa regresi.
* **Standar / Rule README yang Dipatuhi**:
  * Rule #16 (Strict Role-Based Protection Middleware).
  * Rule #19 (Terminologi Fotografer serta Arah Menu Profil Dashboard Fotografer).
  * Rule #22 & #59 (Strict Icon Constraint, Desain Square Modal Standar, Close `X` SVG Polos, Judul Centered Tanpa Ikon).
  * Rule #49 (Fokus Ketat Instruksi serta Nol Efek Samping ke Tamu dan Klien).
  * Rule #51 & #70 (Anti-AI Slop Copywriting Ringkas, Tegas, Tanpa Simbol `&`).
  * Rule #61 (Hierarki Warna Tombol CTA Amber Brand LensMatch).
  * Rule #93 (Intersepsi Bookmark Wishlist Fotografer Berbasis Role dan Status).
  * Rule #104 (Null-Safe Handling pada `catatan_penolakan`).
* **Hasil Pengujian & Verifikasi**:
  * Kompilasi Blade Templates (`php artisan view:cache` & `view:clear`): **PASS** (0 error sintaks Blade).
  * Keseimbangan Tag HTML (`<div` vs `</div>`) pada 12 File Blade Statis maupun 10 Halaman Terender: **100% PASS** (seluruh file `diff=0`).
  * Pengujian Fungsional End-to-End Lintas Role & Status Verifikasi via HTTP Kernel (`DB::beginTransaction` + `rollBack`): **76/76 PASS** (Kenji HOLD `incomplete`, simulasi `pending`, simulasi `rejected` dengan catatan, Fotografer `terverifikasi`, Admin, Tamu, serta Klien).

---

### [2026-11-09 15:15 WITA] — Implementasi Standar Validasi Real-Time 5-Pilar Alpine.js pada Seluruh Form Input Sistem

* **Modul / Fitur**:
  - Profil Publik & Klien: Profil Pengguna & Ganti Password (`public/profile/index.blade.php`), Lupa Password (`public/auth/forgot-password.blade.php`), Reset Password (`public/auth/reset-password.blade.php`), Pengajuan Lomba Publik (`public/contests/propose.blade.php`), Modal Buat Topik Forum (`public/forum/index.blade.php`).
  - Dashboard Fotografer: Tambah & Edit Layanan (`photographer/services/create.blade.php`, `edit.blade.php`), Tutup Jadwal/Libur (`photographer/availability/create.blade.php`), Tambah & Edit Portofolio (`photographer/portfolio/create.blade.php`, `edit.blade.php`), Modal Tambah & Edit FAQ (`photographer/faqs/index.blade.php`).
  - Dashboard Admin: Tambah Admin/Staff (`admin/admins/create.blade.php`), Tambah & Edit Kategori (`admin/categories/create.blade.php`, `edit.blade.php`), Tambah & Edit Lomba Foto (`admin/contests/create.blade.php`, `edit.blade.php`), Tambah & Edit Materi E-Learning (`admin/elearning/create.blade.php`, `edit.blade.php`).
* **File yang Diubah**:
  * `resources/views/public/profile/index.blade.php`
  * `resources/views/public/auth/forgot-password.blade.php`
  * `resources/views/public/auth/reset-password.blade.php`
  * `resources/views/public/contests/propose.blade.php`
  * `resources/views/public/forum/index.blade.php`
  * `resources/views/photographer/services/create.blade.php`
  * `resources/views/photographer/services/edit.blade.php`
  * `resources/views/photographer/availability/create.blade.php`
  * `resources/views/photographer/portfolio/create.blade.php`
  * `resources/views/photographer/portfolio/edit.blade.php`
  * `resources/views/photographer/faqs/index.blade.php`
  * `resources/views/admin/admins/create.blade.php`
  * `resources/views/admin/categories/create.blade.php`
  * `resources/views/admin/categories/edit.blade.php`
  * `resources/views/admin/contests/create.blade.php`
  * `resources/views/admin/contests/edit.blade.php`
  * `resources/views/admin/elearning/create.blade.php`
  * `resources/views/admin/elearning/edit.blade.php`
* **Rincian Perubahan**:
  1. **Penerapan Standar 5-Pilar Validasi Real-Time (Komputer B standard)**:
     - **Pilar 1 (3-Pillar Reactive State)**: Inisialisasi reaktif `form`/`formData`, `touched` (boolean per field), dan `errors` (pesan kesalahan string) sinkron dengan session error Laravel `$errors->first(...)`.
     - **Pilar 2 (Dual-Trigger Interactivity)**: Menghubungkan event `@input="touched.x = true; validateX()"` (live keystroke) dan `@blur="touched.x = true; validateX()"` (loss of focus) pada setiap input control.
     - **Pilar 3 (Dynamic Visual Styling & Feedback)**: Border field berubah dinamis `:class="touched.x && errors.x ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : '...'"` disertai paragraf pesan error `<p x-show="touched.x && errors.x" x-text="errors.x" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>`.
     - **Pilar 4 (Strict Numeric & Phone Handling)**: Pada field nomor WhatsApp / telepon menggunakan `type="tel" inputmode="numeric" pattern="[0-9]*"`, keypress filter regex `/[0-9]/`, auto-strip regex non-digit, dan verifikasi panjang 9-20 digit.
     - **Pilar 5 (Pre-Submit Interception & Auto-Focus)**: Mengaktifkan atribut `novalidate` pada form dan event handler `@submit="submitForm($event)"` yang menandai seluruh field sebagai `touched = true`, menjalankan seluruh fungsi validator, membatalkan submit jika ada kesalahan, memfokuskan kursor pada field pertama yang error (`$refs.fieldX?.focus()`), dan melakukan smooth scrolling ke elemen terkait.
  2. **Penegakan Rule #104 (Nullable-First Principle)**:
     - Field opsional (seperti alamat, bio, keterangan jadwal, URL video, dsb.) tidak dipaksa memiliki validasi teks wajib, menjaga integritas data nullable di database.
  3. **Penegakan Rule #73 & #74 (Layout Footer Form & Tombol Aksi Baku)**:
     - Mempertahankan posisi tombol `Batal` di sebelah kiri tombol submit (`Simpan Data` / `Perbaharui Data`), diratakan ke kanan (`justify-end gap-3`) tanpa garis pembatas `border-t` (Rule #41).
* **Hasil Pengujian & Verifikasi**:
  * Kompilasi Seluruh View Blade: **PASS** (`php artisan view:cache` & `php artisan view:clear` 100% sukses tanpa error).
  * Tag Balance HTML (`<div>`): **PASS** (100% div open/close seimbang di seluruh 18 file).

### [2026-11-09 13:50 WITA] — Standardisasi Universal Login Modal pada Seluruh Kasus Serupa di Sistem (Lomba Foto, E-Learning, dan Komunitas Forum)

* **Modul / Fitur**: Halaman Indeks Lomba Foto (`public/contests/index.blade.php`), Detail Lomba Foto (`public/contests/show.blade.php`), Detail Kursus E-Learning (`public/elearning/show.blade.php`), dan Detail Topik Forum (`public/forum/show.blade.php`).
* **File yang Diubah**:
  * `resources/views/public/contests/index.blade.php`
  * `resources/views/public/contests/show.blade.php`
  * `resources/views/public/elearning/show.blade.php`
  * `resources/views/public/forum/show.blade.php`

* **Rincian Perubahan**:
  1. **Tombol "Ajukan Lomba Foto" pada Indeks Lomba (`contests/index.blade.php`)**:
     - Mengganti tautan langsung `<a>` dengan tombol interaktif bertaut modal login untuk tamu (`@guest`).
     - Mencegah *forced redirect* mendadak oleh middleware `auth` Laravel saat tamu mengklik tombol tersebut.
     - Membuka Universal Login Requirement Modal dengan tombol CTA yang mengarahkan ke halaman login dengan redirect otomatis ke form pengajuan lomba (`public.contests.propose.create`).
  2. **Tombol "Selesaikan Materi" & "Beli Akses Kursus" pada Detail E-Learning (`elearning/show.blade.php`)**:
     - Mengeliminasi script `window.location.href = ...` pada fungsi `completeCurrentLesson()` untuk tamu, digantikan pemanggilan `openLoginModal()`.
     - Menyediakan Universal Modal Login pada tombol "Beli Akses Kursus" pada modul berbayar yang terkunci (*locked lesson*).
  3. **Kotak Pendaftaran Calon Peserta Lomba (`contests/show.blade.php`)**:
     - Mengubah tautan login statis menjadi tombol interaktif "Daftar Lomba Foto Ini" yang memicu Universal Modal Login terpusat di layar yang sama.
  4. **Kotak Balasan Komentar Forum (`forum/show.blade.php`)**:
     - Mengubah tautan langsung login pada container komentar tamu menjadi pemicu modal `handleGuestClick()`, selaras dengan tombol interaksi Like, Dislike, Balas, dan Lapor.

* **Standar / Rule README yang Dipatuhi**:
  * Rule #6 (Modal Dialog Backdrop & Sizing).
  * Rule #59 (Standarisasi Modal Dialog Form & Aksi).
  * Rule #70 (Anti-AI Slop: Micro-Typography & Simplicity).
  * Rule #89 (Zero Hash `#` Prefix in System Identifiers).

* **Hasil Pengujian & Verifikasi**:
  * Kompilasi View Blade: **PASS** (`php artisan view:clear`).
  * Tag Balance (`<div>`): **100% PASS** di keempat file view yang diperbarui.
  * Status Rute HTTP: **PASS** (Semua endpoint publik merespons 200 OK).
  * Git Commit: `b3595db` pada branch `development-2`.

---

### [2026-11-09 13:28 WITA] — Standardisasi Universal Login Modal pada Tombol Reservasi, Chat, dan Simpan Profil Fotografer serta Pembersihan Residu Data Statis

* **Modul / Fitur**: Halaman Profil Publik Fotografer (`public/photographers/show.blade.php`), Katalog Fotografer (`public/photographers/katalog.blade.php`), Partial Tabel Kontrak (`partials/table/table-01.blade.php`), dan Demo Profile (`demo/profile.blade.php`).
* **File yang Diubah**:
  * `resources/views/public/photographers/show.blade.php`
  * `resources/views/public/photographers/katalog.blade.php`
  * `resources/views/partials/table/table-01.blade.php`
  * `resources/views/demo/profile.blade.php`

* **Rincian Perubahan**:
  1. **Standardisasi Universal Login Modal pada Tombol Aksi Tamu**:
     - Memastikan tombol **Reservasi**, **Chat**, dan **Simpan/Favorit (Bookmark)** pada profil fotografer saat diklik oleh pengguna non-login (tamu) tidak langsung melakukan redirect paksa ke halaman `/login`.
     - Menggunakan mekanisme decoupled event dispatch Alpine.js `$dispatch('open-login-modal', { message: '...', redirectUrl: '...' })` yang memunculkan pop-up modal instruksi ramah pengguna (*Universal Login Requirement Modal*) sesuai standar Rule #6, #59, dan #93.
     - Menyediakan tombol CTA **Masuk ke Akun** pada pop-up modal dengan parameter redirect otomatis kembali ke halaman/tindakan sebelumnya setelah berhasil masuk.
  2. **Pembersihan Residu Data Statis & Penegakan Standar Data Nullable (Rule #104)**:
     - Merapikan placeholder input angka pada katalog fotografer (`Contoh: 500000`).
     - Membersihkan fallback statis teks demo pada `table-01.blade.php` menjadi null-safe dynamic data dari database (`-`).
     - Mengeliminasi data dummy mockup template kit luar negeri (`randomuser@pimjo.com`, `Testing Chowdhury`, `Arizona`) pada `demo/profile.blade.php`.

* **Standar / Rule README yang Dipatuhi**:
  * Rule #6 (Modal Dialog Backdrop & Sizing).
  * Rule #59 (Standarisasi Modal Dialog Form & Aksi).
  * Rule #70 (Anti-AI Slop: Micro-Typography & Simplicity).
  * Rule #89 (Zero Hash `#` Prefix in System Identifiers).
  * Rule #93 (Live Toast & Favorite State Management).
  * Rule #104 (Standar Penanganan Data Nullable-First & Larangan Rekayasa Fallback Fiktif).

* **Hasil Pengujian & Verifikasi**:
  * Kompilasi View Blade: **PASS** (`php artisan view:clear`).
  * Tag Balance (`<div>`): **100% PASS** (113 open / 113 close).
  * Status Rute HTTP: **PASS** (`/fotografer/1` -> 200 OK, `/fotografer/katalog` -> 200 OK).
  * Git Commit: `5987a3b` pada branch `development-2`.

---

### [2026-11-07 14:05 WITA] — Perbaikan Dropdown Kategori Pelanggaran Modal Hapus Mengikuti Standar Rule #11 & Rule #46 (Alpine.js Custom Floating Dropdown)

* **Modul / Fitur**: Modal Hapus Fotografer pada Tabel Manajemen Fotografer (`admin/photographers/index.blade.php`) & Detail Audit (`admin/photographers/show.blade.php`)
* **File yang Diubah**:
  * `resources/views/admin/photographers/index.blade.php`
  * `resources/views/admin/photographers/show.blade.php`

* **Rincian Perubahan**:
  1. **Penghapusan Tag HTML Native `<select><option>`**:
     - *Masalah*: Form modal Hapus Fotografer sebelumnya menggunakan tag native `<select><option>` yang menampilkan kontrol OS default kaku (persegi abu-abu tanpa border konsisten), melanggar **Rule #11** dan **Rule #46**.
     - *Solusi*: Mengganti seluruh native `<select>` dengan **Alpine.js Custom Floating Dropdown** (`x-data="{ categoryOpen: false, selectedCategory: '' }"`) dengan styling standar LensMatch:
       - Input hidden `<input type="hidden" name="kategori_penghapusan" :value="selectedCategory" required>`.
       - Tombol trigger kustom `rounded-xl`, latar `bg-gray-50 dark:bg-gray-900`, teks terpotong rapi dengan status placeholder abu-abu saat belum dipilih, dan ikon chevron SVG berotasi halus.
       - Floating container melayang berbayang `shadow-xl rounded-xl`, batas scroll `max-h-56 overflow-y-auto`, dan item opsi dengan hover rose lembut (`hover:bg-rose-50 text-rose-600`) serta ikon centang (checkmark) pada kategori terpilih.
       - Validasi form interaktif: tombol submit `Hapus Fotografer` otomatis berstatus disabled (`:disabled="!selectedCategory || !alasanText.trim() || alasanText.trim().length < 5"`) hingga kategori dipilih dan alasan diisi.

* **Standar / Rule README yang Dipatuhi**:
  * Rule #11 (Standar Komponen Custom Dropdown Alpine.js Modern).
  * Rule #46 (Larangan Mutlak Tag Native `<select>` & Standar Custom Floating Dropdown).
  * Rule #59 (Standarisasi Modal Dialog Form & Aksi).

* **Hasil Pengujian & Verifikasi**:
  * Kompilasi View Blade: **PASS** (`artisan view:cache`).
  * Uji Render Modal Dropdown: **PASS** (Zero native select tags remain).

---

### [2026-11-07 11:35 WITA] — Implementasi Alur Tolak Pendaftaran Mitra, Notifikasi Catatan Penolakan, Auto Re-Apply Profil, & Form Alasan/Kategori Pelanggaran Hapus Fotografer

* **Modul / Fitur**: Manajemen Fotografer Admin (`/admin/photographers`), Detail Audit Verifikasi (`/admin/photographers/{id}`), Dashboard & Profil Fotografer (`/photographer/*`)
* **File yang Diubah**:
  * `database/migrations/2026_11_07_032601_add_status_verifikasi_and_rejection_notes_to_role_photographers_table.php` (File Baru)
  * `app/Models/RolePhotographer.php`
  * `routes/web.php`
  * `app/Http/Controllers/Admin/PhotographerVerificationController.php`
  * `app/Http/Controllers/Public/ProfileController.php`
  * `app/Http/Middleware/EnsurePhotographerVerified.php`
  * `resources/views/admin/photographers/index.blade.php`
  * `resources/views/admin/photographers/show.blade.php`
  * `resources/views/photographer/dashboard.blade.php`
  * `resources/views/photographer/profile/index.blade.php`
  * `README.md`

* **Rincian Perubahan**:
  1. **Migrasi Database `role_photographers`**:
     - Menambahkan kolom `status_verifikasi` enum (`'menunggu'`, `'terverifikasi'`, `'ditolak'`) default `'menunggu'`.
     - Menambahkan kolom `catatan_penolakan` (text, nullable).
     - Menambahkan kolom `kategori_penghapusan` (string, nullable) dan `alasan_penghapusan` (text, nullable).
     - Migrasi otomatis menyelaraskan data lama berdasarkan `is_verified`.
  2. **Tabel Manajemen Fotografer Admin (`/admin/photographers`)**:
     - Memperbarui 4 kartu metrik status pendaftaran: Total Fotografer, Terverifikasi, Menunggu, dan Pendaftaran Ditolak.
     - Memperbarui dropdown filter status verifikasi (Semua Status, Menunggu, Terverifikasi, Ditolak).
     - Menampilkan badge status 3 kondisi: `Terverifikasi` (Hijau Emerald), `Menunggu` (Kuning Amber), `Ditolak` (Merah Rose).
     - Mengubah aksi tombol `Hapus` agar membuka dialog modal yang mewajibkan input **Kategori Pelanggaran** dan **Deskripsi Alasan Penghapusan**.
  3. **Halaman Detail Audit Verifikasi (`/admin/photographers/{id}`)**:
     - Menampilkan lencana status 3 kondisi serta boks peringatan merah berisi catatan penolakan jika status mitra ditolak.
     - Menyediakan bilah aksi keputusan lengkap:
       - Tombol `Verifikasi Fotografer` (Hijau Emerald) / `Batalkan Verifikasi` (Amber soft).
       - Tombol `Tolak Pendaftaran` (Merah Rose soft) dengan dialog modal untuk menginputkan catatan perbaikan (wajib diisi).
       - Tombol `Hapus` (Merah Rose soft) dengan dialog modal untuk menginputkan kategori pelanggaran dan deskripsi alasan.
  4. **Pemberitahuan & Auto Re-Apply Sisi Fotografer**:
     - Dashboard (`/photographer/dashboard`) dan profil fotografer (`/photographer/profile`) menampilkan banner merah yang jelas memuat catatan admin saat pendaftaran ditolak.
     - Saat fotografer memperbarui data profilnya dan menyimpan ulang, status otomatis kembali ke `menunggu`, notifikasi in-app otomatis dikirimkan ke Admin, dan tercatat di `ActivityLog`.
  5. **Form Alasan & Kategori Pelanggaran Penghapusan**:
     - Menghindari penghapusan instan tanpa alasan. Pilihan kategori pelanggaran meliputi: *Data Palsu atau Identitas Fiktif*, *Pelanggaran Hak Cipta Portofolio*, *Transaksi Terlarang di Luar Sistem*, *Penipuan atau Penggelapan Dana*, *Konten Melanggar Asusila atau Hukum*, *Permintaan Penutupan oleh Mitra*, dan *Lainnya*.
     - Alasan dan kategori tersimpan di database sebelum soft-delete serta tercatat pada `ActivityLog`.

* **Standar / Rule README yang Dipatuhi**:
  * Rule #51 (Copywriting Bersih, Proporsional & Anti-AI Slop).
  * Rule #59 (Standarisasi Modal Dialog Form & Aksi).
  * Rule #74 (Standard Posisi & Grouping Tombol Aksi).
  * Rule #91 (Standarisasi Skema Warna Semantik Emerald/Amber/Rose).
  * Rule #95 (Standarisasi Alur Detail & Verifikasi Mitra Fotografer Admin).

* **Hasil Pengujian & Verifikasi**:
  * Migrasi Database: **PASS**.
  * Kompilasi View Blade: **PASS** (`artisan view:cache`).
  * Uji Penolakan Kenji dengan Catatan: **PASS**.
  * Uji Notifikasi & Log Aktivitas: **PASS**.
  * Uji Banner Dashboard & Profil: **PASS**.
  * Uji Auto Re-apply Simpan Profil: **PASS**.
  * Uji Verifikasi Ulang: **PASS**.
  * Uji Validasi Modal Hapus Fotografer: **PASS**.

* **Modul / Fitur**: Profil Fotografer, Verifikasi Admin, Profil Klien, Lomba Foto, & E-Learning
* **File yang Diubah**:
  * `app/Models/User.php`
  * `resources/views/photographer/profile/index.blade.php`
  * `resources/views/admin/photographers/show.blade.php`
  * `resources/views/public/profile/index.blade.php`
  * `resources/views/admin/contests/create.blade.php`
  * `resources/views/admin/contests/edit.blade.php`
  * `resources/views/admin/elearning/create.blade.php`
  * `resources/views/admin/elearning/edit.blade.php`
  * `resources/views/public/photographers/show.blade.php`
  * `resources/views/client/favorites/index.blade.php`
  * `resources/views/public/home.blade.php`
  * `resources/views/public/photographers/index.blade.php`
  * `resources/views/public/photographers/katalog.blade.php`

* **Rincian Perubahan**:
  1. **Standardisasi Ikon Kamera Abu-Abu Simpel pada Seluruh Kotak Preview/Upload Foto Kosong**:
     - *Masalah*: Kotak pratinjau media yang belum diisi foto di beberapa halaman sebelumnya menampilkan teks verbal redundan (*"Belum ada foto"*, *"Kosong"*, inisial huruf, atau bahkan kotak abu-abu polos tanpa icon).
     - *Solusi*: Seluruh container upload/preview foto yang belum memiliki data gambar kini secara konsisten dan seragam menampilkan icon kamera outline minimalis berwarna abu-abu redup (`text-gray-400 dark:text-gray-500`) tanpa label teks berlebih, diletakkan di tengah kotak bergaris putus-putus (`border-dashed border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-800`).
     - *Area yang Diperbarui*:
       - Foto Profil & Foto Sampul pada formulir Profil Fotografer (`photographer/profile/index.blade.php`).
       - Foto Profil & Foto Sampul pada Kartu Audit Admin (`admin/photographers/show.blade.php`).
       - Foto Avatar pada formulir Profil Pengguna/Klien (`public/profile/index.blade.php`).
       - Banner Poster pada Tambah & Edit Lomba Foto Admin (`admin/contests/create.blade.php` & `edit.blade.php`).
       - Gambar Thumbnail pada Tambah & Edit Modul E-Learning Admin (`admin/elearning/create.blade.php` & `edit.blade.php`).
  2. **Penambahan Method `hasRole()` pada Model `User`**:
     - *Masalah*: Terjadi `BadMethodCallException: Call to undefined method App\Models\User::hasRole()` ketika route controller publik mengecek otorisasi peran user.
     - *Solusi*: Menambahkan method `public function hasRole(string|array $roles): bool` pada `App\Models\User`, sehingga pengecekan role berbasis string tunggal maupun array berjalan tangguh di seluruh sistem.
  3. **Pembersihan Warning Deprecations PHP 8.4 (`trim(null)`)**:
     - Mengamankan seluruh pemanggilan `trim()` pada `$photographer->deskripsi_bio` dan portofolio dengan null-coalescing operator `?? ''` pada 5 file view Blade publik.

* **Standar / Rule README yang Dipatuhi**:
  * Rule #51 (Copywriting Bersih, Proporsional & Anti-AI Slop).
  * Rule #70 (Standarisasi Ikon Antarmuka & Zero Verbose Labels).
  * Rule #94 (Standarisasi Representasi Data Kosong & Zero Dummy Fallback).
  * Rule #95 (Standarisasi Detail & Verifikasi Mitra Fotografer).

* **Hasil Pengujian & Verifikasi**:
  * Kompilasi View Blade: **PASS** (`artisan view:cache`).
  * Uji Render Admin Verification Show (ID: 6 - Kenji): **PASS**.
  * Uji Render Public Photographer Show (ID: 6 - Kenji): **PASS** (Admin dapat melihat preview, publik diarahkan abort 404 bersih).

* **Modul / Fitur**: Detail Verifikasi Admin (`/admin/photographers/{id}`) & Formulir Profil Fotografer (`/photographer/profile`)
* **File yang Diubah**:
  * `resources/views/admin/photographers/show.blade.php`
  * `resources/views/photographer/profile/index.blade.php`

* **Rincian Perubahan**:
  1. **Penyelarasan Padding Kanan-Kiri Detail Admin (Penghapusan `max-w-7xl mx-auto`)**:
     - *Masalah*: Kontainer `admin/photographers/show.blade.php` sebelumnya menggunakan `max-w-7xl mx-auto` sehingga lebarnya menyempit dan menyisakan margin kosong lebar di sisi kanan-kiri yang tidak konsisten dengan layout halaman admin lainnya (seperti `admin/users/index.blade.php` dan `admin/photographers/index.blade.php`).
     - *Solusi*: Menghapus `max-w-7xl mx-auto` dan menggunakan `space-y-6 pb-12` standar sehingga kartu-kartu audit memenuhi lebar kontainer utama secara seragam dengan seluruh halaman dashboard admin lainnya.
  2. **Perbaikan Bug Kebocoran Raw JS pada Tampilan Profil Fotografer**:
     - *Masalah*: Kode validasi JavaScript yang ditulis langsung di dalam atribut HTML `x-data="{ ... }"` terpotong oleh karakter pembanding (`> 20`) yang dianggap sebagai tag penutup HTML oleh browser, menyebabkan potongan kode JS tumpah ke layar sebagai teks mentah.
     - *Solusi*: Memindahkan seluruh objek data dan metode Alpine.js ke dalam fungsi skrip terpisah (`photographerProfileForm()`) di bagian bawah file Blade. Template HTML kini memanggil `x-data="photographerProfileForm()"`, 100% aman dari truncating tag HTML.
  3. **Transformasi Pilihan Kategori Menjadi Dropdown Multi-Select yang Ringkas**:
     - *Masalah*: 13 pilihan kategori sebelumnya dijejerkan sebagai grid tombol raksasa 4 kolom yang memakan ruang vertikal layar secara berlebihan.
     - *Solusi*: Mengubah pilihan kategori menjadi komponen **Dropdown Multi-select** modern yang ringkas sejajar dengan input lainnya dalam grid 2 kolom. Dilengkapi fitur toggle checkmark, indikator jumlah terpilih, serta chip tag kompak yang dapat dihapus sewaktu-waktu.

* **Standar / Rule README yang Dipatuhi**:
  * Rule #51 (Copywriting Bersih, Proporsional & Anti-AI Slop).
  * Rule #73 (Hybrid Responsive & Form Layout Standards).
  * Rule #95 (Standarisasi Detail & Verifikasi Mitra Fotografer).

* **Hasil Pengujian & Verifikasi**:
  * Kompilasi View Blade: **PASS** (`artisan view:cache`).
  * Uji Bebas Kebocoran Raw JS: **PASS**.
  * Uji Render Dropdown Kategori: **PASS**.

---

### [2026-11-07 11:08 WITA] — Standardisasi Warna Teks Muted Abu-Abu untuk Seluruh Nilai Kosong/Belum Diisi

* **Modul / Fitur**: Detail Verifikasi Fotografer Admin (`/admin/photographers/{id}`) & Tabel Manajemen Fotografer (`/admin/photographers`)
* **File yang Diubah**:
  * `resources/views/admin/photographers/show.blade.php`
  * `resources/views/admin/photographers/index.blade.php`

* **Rincian Perubahan**:
  1. **Standardisasi Tipografi State Kosong (Muted Gray `text-gray-400 font-medium`)**:
     - *Masalah*: Teks status data kosong pada beberapa field (khususnya *Bio* yang menampilkan *"Belum diisi"*, serta *Provinsi*, *Kota*, *Alamat*, *Nama Bank*, *Nomor Rekening*, dan *Nama Pemilik Rekening* yang menampilkan *"-")* dirender menggunakan warna gelap terang (`text-gray-800` / `font-bold text-gray-900`), sehingga terlihat seolah-olah data tersebut sudah terisi.
     - *Solusi*: Seluruh representasi data kosong (*empty state*) kini wajib menggunakan styling seragam abu-abu redup: `text-gray-400 font-medium`. Hanya data riil database yang terisi yang menggunakan warna kontras primer `font-bold text-gray-900 dark:text-white`.
  2. **Penerapan Seragam pada Tabel Admin**:
     - Kolom domisili pada tabel `admin/photographers/index.blade.php` yang kosong kini menampilkan `-` dengan kelas abu-abu `text-gray-400 font-medium`.

* **Standar / Rule README yang Dipatuhi**:
  * Rule #51 (Copywriting Bersih, Proporsional & Anti-AI Slop).
  * Rule #94 (Standarisasi Representasi Data Kosong & Zero Dummy Fallback).
  * Rule #95 (Standarisasi Detail & Verifikasi Mitra Fotografer).

* **Hasil Pengujian & Verifikasi**:
  * Kompilasi View Blade: **PASS** (`artisan view:cache`).

---

### [2026-11-07 11:05 WITA] — Pembersihan Inisial Avatar pada Kotak Audit Media Kosong & Penataan Aksi Tabel Fotografer

* **Modul / Fitur**: Audit Media Admin (`/admin/photographers/{id}`) & Tabel Manajemen Fotografer (`/admin/photographers`)
* **File yang Diubah**:
  * `resources/views/admin/photographers/show.blade.php`
  * `resources/views/admin/photographers/index.blade.php`
  * `resources/views/photographer/profile/index.blade.php`

* **Rincian Perubahan**:
  1. **Penghapusan Inisial Nama (KE) pada Kotak Pratinjau Foto Profil Kosong**:
     - *Masalah*: Kotak pratinjau audit berkas *Foto Profil* sebelumnya menampilkan inisial 2 huruf nama fotografer (`KE`) di atas teks *"Kosong"*, menimbulkan kebingungan bagi admin yang sedang mengaudit file fisik foto.
     - *Solusi*: Menghilangkan pemanggilan `$initials` pada kotak audit file. Jika foto profil belum diunggah, kotak bergaris putus-putus secara murni dan tegas hanya menampilkan teks `Kosong`. Hal yang sama diterapkan pada *Foto Sampul*.
  2. **Pemusatan Aksi Verifikasi Hanya di Halaman Rincian Detail**:
     - Menghapus tombol *Verifikasi* langsung dari baris tabel daftar fotografer (`/admin/photographers`).
     - Kolom aksi pada tabel hanya menyisakan tombol **`Lihat`** (dan **`Hapus`**), memastikan verifikasi hanya bisa diputuskan di dalam halaman rincian detail audit setelah admin memeriksa data fotografer.

* **Standar / Rule README yang Dipatuhi**:
  * Rule #51 (Copywriting Bersih, To-the-Point & Anti-AI Slop).
  * Rule #94 (Standarisasi Representasi Data Kosong).
  * Rule #95 (Standarisasi Alur Detail & Verifikasi Mitra Fotografer Admin).

* **Hasil Pengujian & Verifikasi**:
  * Kompilasi View Blade: **PASS** (`artisan view:cache`).

---

### [2026-11-07 11:00 WITA] — Sinkronisasi Label Validasi Admin & Fotografer, Validasi Real-time Alpine.js & Halaman Verifikasi Admin

* **Modul / Fitur**: Verifikasi Mitra Fotografer Admin (`/admin/photographers/{id}`) & Kelola Profil Fotografer (`/photographer/profile`)
* **File yang Diubah**:
  * `resources/views/photographer/profile/index.blade.php`
  * `resources/views/admin/photographers/show.blade.php`
  * `resources/views/admin/photographers/index.blade.php`
  * `app/Models/RolePhotographer.php`
  * `README.md`

* **Rincian Perubahan**:
  1. **Keselarasan 100% Label Data Antara Detail Admin & Form Profil Fotografer**:
     - Menstandarisasi 14 data field profil yang identik kata-per-kata antara formulir profil fotografer (`/photographer/profile`), tampilan audit verifikasi admin (`/admin/photographers/{id}`), dan indikator missing fields (`RolePhotographer::getMissingProfileFields()`):
       - `Nama Lengkap`
       - `Email`
       - `Nomor WhatsApp`
       - `Instagram`
       - `Provinsi`
       - `Kota / Kabupaten`
       - `Alamat`
       - `Kategori`
       - `Bio`
       - `Foto Profil`
       - `Foto Sampul`
       - `Nama Bank`
       - `Nomor Rekening`
       - `Nama Pemilik Rekening`
     - Menghapus seluruh terminologi asing dan kata berlebih seperti *"Payout"*, *"Foto Avatar Profil"*, *"Kategori Spesialisasi Foto"*, dan *"Alamat Lengkap"*.
     - Menyelaraskan urutan 5 kartu audit administratif pada halaman Admin agar berurutan tepat sama dengan formulir profil fotografer (`1. Kontak`, `2. Domisili & Kategori`, `3. Bio`, `4. Foto Profil & Sampul`, `5. Rekening Bank`).
  2. **Implementasi Validasi Real-time Alpine.js pada Profil Fotografer**:
     - Mengadopsi arsitektur validasi `signup.blade.php` dengan model reaktif `formData`, `touched`, dan `errors`.
     - Validasi interaktif langsung saat `@input` dan `@blur`: auto-strip karakter non-angka pada WhatsApp dan Nomor Rekening, validasi format regex email, pengecekan panjang minimal teks dan kategori.
     - Penanda visual error real-time: border merah (`border-red-400 focus:ring-red-400`) dan pesan error merah (`text-rose-500 font-bold text-[11px]`).
     - Pre-submit interception: jika terdapat input yang belum valid saat tombol submit diklik, Alpine.js menghentikan submit, menandai seluruh field sebagai `touched`, lalu secara otomatis mengarahkan fokus (*focus*) dan melakukan scroll halus (*smooth scroll*) ke field yang bermasalah.
     - Menambahkan validasi real-time pada formulir kata sandi (`6. Kata Sandi`).

* **Standar / Rule README yang Dipatuhi**:
  * Rule #51 (Standarisasi Copywriting Bersih, Proporsional & Anti-AI Slop).
  * Rule #70 (Larangan Terminologi Kaku, Payout & Pemborosan Simbol "&").
  * Rule #73 (Hybrid Responsive Layout & Form Standards).
  * Rule #94 (Standarisasi 100% Data Dinamis & Zero Dummy Fallback).
  * Rule #95 (Standarisasi Alur Detail & Verifikasi Mitra Fotografer Admin).

* **Hasil Pengujian & Verifikasi**:
  * Kompilasi View Blade: **PASS** (`artisan view:cache`).
  * Uji Render View: **PASS** (Semua route `/admin/photographers/{id}` dan `/photographer/profile` berhasil dirender tanpa error).

---

### [2026-11-06 21:15 WITA] — Perbaikan State Foto Avatar & Sampul Kosong pada Profil Fotografer Baru

* **Modul / Fitur**: Profil Fotografer & Profil Pengguna (`/photographer/profile` & `/profile`)
* **File yang Diubah**:
  * `resources/views/photographer/profile/index.blade.php`
  * `resources/views/public/profile/index.blade.php`

* **Rincian Perubahan**:
  1. **Penghapusan Fallback Gambar Dummy Unsplash**:
     * *Penyebab*: Variabel `$avatarUrl` dan `$sampulUrl` sebelumnya memiliki nilai fallback hardcoded ke link gambar eksternal Unsplash (`photo-1534528741775` dan `photo-1537633552985`). Akibatnya fotografer baru yang belum pernah mengunggah foto profil/sampul langsung melihat foto wajah orang lain dan foto pantai terisi di form preview mereka.
     * *Solusi*:
       - Menghapus URL dummy Unsplash. Jika user belum mengunggah file (`foto` / `foto_sampul` masih `null`), variabel preview diinisialisasi sebagai string kosong `''`.
  2. **Implementasi UI Empty State (Placeholder Kosong Bergaris Putus-putus)**:
     * Dibuat visual state placeholder:
       - **Avatar Kosong**: Kotak bergaris putus-putus (`border-dashed border-gray-300 dark:border-gray-600`) dengan siluet ikon avatar pengguna (`SVG`).
       - **Sampul Kosong**: Kotak bergaris putus-putus dengan siluet ikon pemandangan/foto (`SVG`) dan label *"Belum ada foto"*.
     * Begitu fotografer memilih file melalui tombol upload, Alpine.js reaktif menampilkan pratinjau file terpilih dan mengubah border menjadi solid amber (`border-amber-400`).

* **Standar / Rule README yang Dipatuhi**:
  * Rule #46 (Anti AI-Slop & Larangan Konten Palsu/Gambar Dummy Tanpa Izin).
  * Rule #73 (Hybrid Responsive & Form Media Handling Standard).

* **Hasil Pengujian & Verifikasi**:
  * Kompilasi View Blade: **PASS** (`artisan view:cache` & `artisan view:clear`).
  * Empty State Rendering: **PASS** (Kotak placeholder avatar dan sampul tampil bersih dalam keadaan kosong).

---

---

### [2026-11-06 21:07 WITA] — Perbaikan Tombol Konfirmasi Keluar Akun (Logout) yang Macet pada Modal

* **Modul / Fitur**: Modal Konfirmasi Global & Tombol Logout (`confirm-modal.blade.php`)
* **File yang Diubah**:
  * `resources/views/partials/shared/confirm-modal.blade.php`

* **Rincian Perubahan**:
  1. **Perbaikan Macet "Memproses..." pada Action URL Submission (Case 2)**:
     * *Penyebab*: Tombol konfirmasi pada Case 2 menggunakan `<button type="submit" @click="isSubmitting = true" :disabled="isSubmitting">`. Ketika tombol diklik, Alpine.js langsung mengubah `isSubmitting` menjadi `true` sehingga atribut `disabled` terpasang sebelum browser sempat mengeksekusi aksi submit native. Sesuai spesifikasi HTML browser, tombol yang menjadi disabled di tengah siklus klik akan membatalkan (*abort*) proses pengiriman form. Akibatnya form tidak pernah terkirim ke server dan UI macet pada status *"Memproses..."*.
     * *Solusi*:
       - Mengubah tombol konfirmasi menjadi `<button type="button" @click="executeConfirm($el)" :disabled="isSubmitting">`.
       - Memperbarui method `executeConfirm(btnEl)` agar mendeteksi form secara presisi melalui `btnEl.closest('form')` atau `$refs.actionUrlForm`, memastikan atribut `action` dan `method` tersinkronisasi, dan memanggil native JavaScript `formEl.submit()`. Form submit via JavaScript langsung dieksekusi browser tanpa terblokir oleh status disabled pada tombol.

* **Standar / Rule README yang Dipatuhi**:
  * Rule #42 (Pencegahan Bug / Error Fatal pada Sistem).
  * Rule #74 & #76 (Keamanan Sesi, Penanganan Otentikasi & Modal Konfirmasi Universal).

* **Hasil Pengujian & Verifikasi**:
  * Eksekusi Backend Logout (`POST /logout`): **PASS** (HTTP 302 Redirect ke `/`).
  * Kompilasi View Blade: **PASS** (`artisan view:clear`).

---

---

### [2026-11-06 20:58 WITA] — Pengalihan Langsung Pendaftaran Fotografer ke Halaman Profil

* **Modul / Fitur**: Autentikasi & Registrasi Fotografer (`/register` -> `/photographer/profile`)
* **File yang Diubah**:
  * `app/Http/Controllers/Auth/AuthController.php`

* **Rincian Perubahan**:
  1. **Redirection Logic Setelah Registrasi Akun Fotografer**:
     * *Sebelumnya*: Setelah fotografer mendaftar, method `register()` memanggil `$this->redirectBasedOnRole($user)` yang secara default mengarahkan fotografer ke dashboard (`/photographer/dashboard`).
     * *Sesudahnya*: Ditambahkan logika spesifik pada `register()`: jika akun yang didaftarkan memiliki role `photographer`, session `url.intended` dibersihkan dan pengguna langsung diarahkan (`redirect()->route('photographer.profile')`) ke halaman profil dengan flash message *'Akun Anda berhasil dibuat. Silakan lengkapi profil Anda.'*.

* **Standar / Rule README yang Dipatuhi**:
  * Rule #20 (Penulisan Bahasa Indonesia baku & pesan user yang profesional).
  * Rule #49 (Instruksi spesifik pengalihan langsung setelah pendaftaran fotografer).

* **Hasil Pengujian & Verifikasi**:
  * Resolusi Route `photographer.profile`: **PASS** (`http://localhost:8000/photographer/profile`).
  * Unit Route Resolution & Compilation: **PASS**.

---

---

### [2026-11-06 20:45 WITA] — Refaktorisasi Form Kelengkapan Profil Fotografer, Proteksi Sidebar & Pembersihan AI Slop

* **Modul / Fitur**: Profil Fotografer & Dashboard (`/photographer/profile`, `/photographer/dashboard`, Sidebar)
* **File yang Diubah**:
  * `app/Models/RolePhotographer.php`
  * `app/Http/Controllers/Public/ProfileController.php`
  * `app/Http/Middleware/EnsurePhotographerVerified.php` (File Baru)
  * `bootstrap/app.php`
  * `routes/web.php`
  * `resources/views/partials/photographer/sidebar.blade.php`
  * `resources/views/photographer/dashboard.blade.php`
  * `resources/views/photographer/profile/index.blade.php`

* **Rincian Perubahan**:
  1. **Penghapusan Modal Onboarding & Banner Heboh/Alay**:
     * *Sebelumnya*: Terdapat pop-up modal onboarding yang sempit dan memanjang saat fotografer masuk ke dashboard, serta banner notifikasi kuning heboh yang mendominasi dashboard.
     * *Sesudahnya*: Modal onboarding dan banner kuning berlebihan dihapus sepenuhnya dari dashboard (Anti AI-Slop & Anti Redundant Text). Dashboard fotografer kembali bersih, minimalis, dan elegan.
  2. **Form Kelengkapan Profil Melebar (Full-Width Hybrid Responsive)**:
     * Seluruh isian data profil (domisili provinsi/kota bertingkat, alamat lengkap, bio, spesialisasi kategori foto, tautan media sosial, rekening payout, avatar, dan banner sampul) dipindahkan ke halaman Profil Fotografer (`/photographer/profile`).
     * Layout form didesain melebar secara proporsional sesuai Rule #73 (desktop 2-3 kolom, mobile 1 kolom).
  3. **Indikator Progres Kelengkapan Profil Minimalis**:
     * Pada bagian atas form profil fotografer, disematkan bar indikator ringkas 1 baris yang menghitung persentase terisi serta mencantumkan secara presisi sisa data yang belum dilengkapi (*"Kurang X data lagi"*).
  4. **Penguncian Fitur Operasional di Sidebar & Middleware Guard**:
     * Menu operasional fotografer (Paket Layanan, Portofolio, FAQ, Jadwal, Booking, Pesan, Laporan Keuangan, Pengajuan Lomba) dikunci (berwarna abu-abu, kursor `not-allowed`, icon gembok, dan toast alert) jika profil belum 100% lengkap dan belum disetujui Admin.
     * Dibuat middleware `photographer.verified` untuk memproteksi seluruh rute operasional backend.

* **Standar / Rule README yang Dipatuhi**:
  * Rule #20 (Penamaan & standarisasi UI bahasa Indonesia).
  * Rule #42 (Clean code & pencegahan error/bug fatal).
  * Rule #46 (Anti-AI Slop & larangan teks duplikasi/banner heboh).
  * Rule #51 (Konsistensi arsitektur data & Eloquent relationship).
  * Rule #73 (Hybrid Responsive Form & Layout Full-Width).
  * Rule #74 (Navigasi & status ketersediaan fitur).
  * Rule #76 (Keamanan otorisasi & role-based middleware).

* **Hasil Pengujian & Verifikasi**:
  * Kompilasi View Blade: **PASS** (`php artisan view:clear` & `view:cache`).
  * Middleware Route Protection: **PASS** (Mengalihkan akses fitur operasional yang terkunci ke halaman profil).
  * Render Halaman Profil: **PASS** (Cascading dropdown wilayah, selector kategori, dan kalkulasi indikator berjalan normal).

---

---

### [2026-11-06 20:02 WITA] — Perbaikan Kebocoran Teks Alpine.js & Penataan Handler Validasi Real-Time

* **Modul / Fitur**: Autentikasi (`/login` & `/register`)
* **File yang Diubah**:
  * `resources/views/public/auth/signin.blade.php`
  * `resources/views/public/auth/signup.blade.php`

* **Rincian Perubahan**:
  1. **Isolasi Logika JavaScript ke Tag `<script>` (`authHandler()`)**:
     * *Sebelumnya*: Seluruh objek fungsi dan ekspresi regex validasi real-time ditaruh langsung secara inline di dalam atribut HTML `<body x-data="{ ... }">`. Karakter kutip dan tanda perbandingan (`>`) menyebabkan atribut HTML tertutup secara prematur sehingga sisa teks kode JavaScript tumpah dan tampil sebagai teks mentah di layar browser.
     * *Sesudahnya*: Memindahkan seluruh fungsi logika validasi dan state ke fungsi global `authHandler()` di dalam blok `<script>` terisolasi yang aman dari konflik parsing HTML, dan memanggilnya secara bersih via `<body x-data="authHandler()">`.
  2. **Verifikasi Validasi Klien Real-Time**:
     * Memastikan validasi instan pada saat mengetik (`@input`) dan saat keluar kolom (`@blur`) bekerja 100% mulus tanpa kebocoran teks apapun di layar.

* **Standar / Rule README yang Dipatuhi**:
  * Rule #49 (Fokus Ketat Terhadap Instruksi User & Zero Unrequested Side-Effects).
  * Bagian 7 Point 9 & 18 (Pengalaman Pengguna Responsif & Real-Time Tanpa Reload Halaman Penuh).

* **Hasil Pengujian & Verifikasi**:
  * Render view Blade: **PASS** (Zero code leakage, teks bersih 100%).
  * Uji Endpoint HTTP `/login` dan `/register`: **PASS**.

---

---

### [2026-11-06 19:56 WITA] — Implementasi Validasi Real-Time Input Field pada Login dan Registrasi

* **Modul / Fitur**: Autentikasi (`/login` & `/register`)
* **File yang Diubah**:
  * `resources/views/public/auth/signin.blade.php`
  * `resources/views/public/auth/signup.blade.php`

* **Rincian Perubahan**:
  1. **Validasi Klien Real-Time Langsung Saat Mengetik (`@input`) dan Saat Keluar Bidang (`@blur`)**:
     * *Sebelumnya*: Pesan kesalahan validasi hanya muncul setelah tombol submit ditekan dan halaman di-reload oleh server, atau terpotong oleh tooltip bawaan browser native.
     * *Sesudahnya*: Mengimplementasikan validasi reaktif real-time menggunakan Alpine.js:
       * **Nama Lengkap**: Wajib diisi dan minimal 3 karakter.
       * **Nomor WhatsApp**: Wajib diisi, hanya angka/karakter telepon valid, minimal 9 digit dan maksimal 20 digit.
       * **Email**: Wajib diisi dan format email valid (`regex`).
       * **Kata Sandi**: Wajib diisi dan minimal 8 karakter (pesan error otomatis hilang seketika saat karakter ke-8 diketik).
       * **Login Email & Password**: Pengecekan format email dan keharusan isi secara instan.
  2. **Interaktivitas Visual Real-Time**:
     * Garis tepi (*border*) input otomatis berubah menjadi merah (`border-red-400 focus:ring-red-400`) saat nilai tidak valid, dan kembali netral (`border-gray-200 focus:ring-amber-400`) segera setelah nilai diperbaiki.
     * Pesan error merah (`text-[11px] font-bold text-red-500`) muncul dan hilang seketika tanpa perlu reload halaman.
  3. **Penonaktifan Validasi Bawaan Browser Native (`novalidate`)**:
     * Menambahkan atribut `novalidate` pada form dan mengarahkan submit ke interceptor `submitLogin` / `submitRegister` yang secara otomatis memfokuskan kursor ke input pertama yang bermasalah jika ada field yang belum valid.

* **Standar / Rule README yang Dipatuhi**:
  * Rule #49 (Fokus Ketat Terhadap Instruksi User & Zero Unrequested Side-Effects).
  * Bagian 7 Point 9 & 18 (Pengalaman Pengguna Responsif & Real-Time Tanpa Reload Halaman Penuh).

* **Hasil Pengujian & Verifikasi**:
  * Render view Blade: **PASS**.
  * Input typing real-time validation: **PASS** (Pesan kesalahan muncul dan hilang secara instan per ketikan).

---

---

### [2026-11-06 19:46 WITA] — Penyesuaian Ketebalan Font Dropdown "Daftar Sebagai" Menjadi Standar

* **Modul / Fitur**: Autentikasi / Registrasi (`/register` & `/login`)
* **File yang Diubah**:
  * `resources/views/public/auth/signup.blade.php`
  * `resources/views/public/auth/signin.blade.php`

* **Rincian Perubahan**:
  1. **Penyesuaian Font Weight Value & Pilihan Dropdown**:
     * *Sebelumnya*: Teks value pada tombol pemicu dropdown "Daftar Sebagai" (`selectedRoleLabel`) dan item pilihan dropdown menggunakan kelas `font-bold`, sehingga tampak terlalu tebal dan tidak selaras dengan input form lainnya.
     * *Sesudahnya*: Mengubah `font-bold` menjadi `font-medium` agar selaras dan proporsional dengan input teks lain pada formulir registrasi (`nama`, `nomor_telepon`, `email`).

* **Standar / Rule README yang Dipatuhi**:
  * Rule #49 (Fokus Ketat Terhadap Instruksi User & Zero Unrequested Side-Effects).
  * Bagian 7 Point 3 & 7 (Konsistensi Tipografi & Hirarki Visual Form).

* **Hasil Pengujian & Verifikasi**:
  * Render view Blade: **PASS** (Teks value tampil proporsional dengan bobot `font-medium` standar).

---

---

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

---

---

### [2026-11-06 20:00 WITA] — Perbaikan Dropdown Role, Validasi Real-time Input Auth, dan Pengetatan Input WhatsApp

* **Modul / Fitur**: Otentikasi Pengguna (`/login` & `/register`)
* **File yang Diubah**:
  * `resources/views/public/auth/signin.blade.php`
  * `resources/views/public/auth/signup.blade.php`

* **Rincian Perubahan**:
  1. **Penyesuaian Bobot Font Dropdown "Daftar Sebagai"**:
     * Mengubah kelas styling button dropdown dari `font-bold` menjadi `font-medium` agar tampilan nilai terpilih lebih seimbang, tidak terlalu mencolok/tebal, dan selaras dengan input field lainnya.
  2. **Implementasi Real-Time Input Validation (Login & Register)**:
     * Mengisolasi logika state dan validasi Alpine.js ke dalam fungsi global `authHandler()` pada blok `<script>` terpisah di dalam `<head>`, mengeliminasi risiko benturan tanda kutip HTML / code leak pada atribut `x-data`.
     * Menerapkan validasi langsung saat pengguna mengetik (`@input`) dan saat kursor meninggalkan kolom (`@blur`) untuk seluruh input field:
       * **Login**: Email (wajib & format email valid), Password (wajib).
       * **Register**: Nama lengkap (minimal 3 karakter), Nomor WhatsApp (wajib, min 9 - max 20 angka), Email (wajib & format email), Kata Sandi (wajib, min 8 karakter).
     * Memberikan visual border merah (`border-red-400`) serta pesan error dinamis secara real-time sebelum tombol submit diklik.
  3. **Pengetatan Input Nomor WhatsApp (Strict Numeric Input)**:
     * Mempertahankan tipe data HTML `type="tel"` agar kompatibel secara semantik dan konsisten dengan skema database.
     * Menambahkan atribut `inputmode="numeric"` dan `pattern="[0-9]*"` untuk memunculkan keyboard angka pada perangkat mobile.
     * Memasang event interceptor `@keypress="if (!/[0-9]/.test($event.key)) $event.preventDefault()"` sehingga penekanan tombol huruf maupun simbol langsung diblokir.
     * Menerapkan sanitasi regex otomatis `replace(/\D/g, '')` pada event `@input` dan fungsi `validateRegisterPhone()` untuk membersihkan karakter non-angka seketika (termasuk jika hasil copy-paste).

* **Hasil Pengujian**:
  * Kompilasi view Blade (`artisan view:cache` & `artisan view:clear`): **PASS (0 error)**.
  * Uji validasi real-time form Login & Register: **PASS**.
  * Pengetatan input nomor WhatsApp hanya menerima angka murni: **PASS**.

---

---

### [2026-11-06 20:45 WITA] — Refaktorisasi Alur Onboarding Fotografer: Eliminasi Modal Pop-up, Form Profil Melebar (Hybrid Responsive), Indikator Kelengkapan Data & Fitur Lock Sidebar

* **Modul / Fitur**: Profil Fotografer & Onboarding (`/photographer/profile`, `/photographer/dashboard`, Sidebar Navigasi)
* **File yang Diubah**:
  * `app/Models/RolePhotographer.php`
  * `app/Http/Controllers/Public/ProfileController.php`
  * `app/Http/Middleware/EnsurePhotographerVerified.php` (Baru)
  * `bootstrap/app.php`
  * `routes/web.php`
  * `resources/views/photographer/dashboard.blade.php`
  * `resources/views/photographer/profile/index.blade.php`
  * `resources/views/partials/photographer/sidebar.blade.php`

* **Rincian Perubahan**:
  1. **Eliminasi Pop-up Modal Onboarding yang Mengunci Layar**:
     * Menghapus modal onboarding bertumpuk dari Dashboard Fotografer secara permanen.
     * Mengganti modal yang memanjang sempit dengan banner ringkasan informatif di Dashboard yang bebas diabaikan (tidak memblokir interaksi fotografer).
  2. **Form Profil Melebar & Terstruktur (Hybrid Responsive Form Standard - Rule #73)**:
     * Menyatukan seluruh data operasional (Nama, Email, WhatsApp, Sosmed, Domisili Provinsi/Kota, Alamat, Kategori Spesialisasi, Bio, Avatar, Sampul 16:9, Rekening Payout) ke dalam halaman profil fotografer (`/photographer/profile`).
     * Menerapkan layout hybrid: grid horizontal 2-3 kolom untuk field bernilai pendek dan vertikal full-width untuk field deskripsi/media.
     * Menggunakan Alpine.js Custom Floating Cascading Dropdown (Rule #11 & Rule #46) untuk Provinsi dan Kota/Kabupaten se-Indonesia (meniadakan tag native `<select>`).
     * Menyediakan tag selector kategori fotografi interaktif dengan counter dinamis.
     * Menjaga kepatuhan Rule #20 (tanda bintang merah `<span class="text-rose-500">*</span>`), Rule #76 (tombol "Perbaharui Data"), dan Rule #91 (warna brand amber solid).
  3. **Indikator Kelengkapan Profil & Tracker Data yang Kurang**:
     * Menambahkan bar persentase kelengkapan profil (0 - 100%) di bagian atas halaman profil dan widget status di dashboard.
     * Mendeteksi secara kalkulatif 14 field wajib dan mendaftar data yang masih kosong secara dinamis dalam bentuk chips/pills informatif (contoh: *"Kurang 2 data lagi: [Foto Sampul], [Rekening Payout]"*).
     * Transisi status visual:
       * *Belum Lengkap (<100%)*: Bar amber + daftar data yang kurang.
       * *Lengkap 100%*: Bar biru/amber + status *"Menunggu Tinjauan & Verifikasi Admin"*.
       * *Terverifikasi*: Bar hijau + status *"Akun Terverifikasi & Seluruh Fitur Terbuka"*.
  4. **Penguncian Fitur (*Feature Lock*) & Sidebar Abu-Abu**:
     * Menu operasional di sidebar (*Paket Layanan*, *Portofolio*, *FAQ*, *Jadwal*, *Booking*, *Pesan*, *Laporan*, *Pengajuan Lomba*) otomatis di-abu-abukan (`opacity-40 text-gray-400 cursor-not-allowed`) dan dilengkapi icon gembok serta notifikasi toast jika diklik.
     * Menu yang tetap aktif bebas diakses: *Dashboard*, *Profil*, dan *Keluar*.
  5. **Proteksi Backend Rute (*Strict Middleware Guard*)**:
     * Membuat middleware `EnsurePhotographerVerified` yang dialiaskan ke `'photographer.verified'` di `bootstrap/app.php`.
     * Memproteksi seluruh rute fitur operasional fotografer di `routes/web.php`. Jika fotografer mencoba mengakses langsung via URL sebelum 100% lengkap dan di-ACC admin, sistem otomatis mengalihkan (302) ke halaman profil dengan flash warning.

* **Hasil Pengujian**:
  * Kompilasi view Blade (`artisan view:cache` & `artisan view:clear`): **PASS (0 error)**.
  * Uji unit model `getMissingProfileFields()`, `getProfileCompletionPercentage()`, `isProfileComplete()`, `isFeatureUnlocked()`: **PASS**.
  * Uji simulasi kernel HTTP (`/dashboard` status 200, `/profile` status 200, `/services` redirect 302): **PASS**.

---

---

### [2026-11-06 20:50 WITA] — Refaktorisasi Anti-AI Slop: Eliminasi Banner Dashboard Heboh & Penyederhanaan Indikator Profil Bersih

* **Modul / Fitur**: Dashboard Fotografer & Halaman Profil (`/photographer/dashboard`, `/photographer/profile`)
* **File yang Diubah**:
  * `resources/views/photographer/dashboard.blade.php`
  * `resources/views/photographer/profile/index.blade.php`

* **Rincian Perubahan**:
  1. **Eliminasi Total Banner Peringatan Dashboard (Anti-AI Slop)**:
     * Menghapus boks kartu kuning besar peringatan *"Lengkapi Data Profil Fotografer Anda untuk Membuka Fitur"* yang dominan dan memicu kesan heboh/alay di Dashboard.
     * Mengembalikan tampilan Dashboard fotografer menjadi bersih, tenang, dan fokus pada analitik antrean operasional tanpa distraksi banner berulang.
  2. **Penyederhanaan Indikator Kelengkapan Profil (Minimalist Standard)**:
     * Mengganti kartu banner bertumpuk 78 baris di halaman profil dengan bar indikator satu baris yang tenang dan elegan (`h-2` slim progress bar).
     * Format teks ringkas to-the-point: `Kelengkapan Profil • Kurang X data lagi` dan persentase di sisi kanan (`XX%`), tanpa kalimat bertele-tele atau kartu badge chip bertumpuk.
  3. **Pembersihan Teks Duplikasi & Fluff (Rule #51 & Rule #42)**:
     * Menghapus seluruh kalimat pembuka/penjelas redundan (seperti *"Isi data profil di bawah ini secara bebas..."*, *"Minimal 15 karakter..."*, *"Pilih kategori sesi foto yang Anda kuasai..."*, *"Digunakan oleh Admin untuk mentransfer hak pendapatan..."*).
     * Menyederhanakan penamaan sub-heading seksi menjadi teks murni profesional (*Akun & Kontak*, *Domisili & Spesialisasi*, *Deskripsi Bio*, *Media Profil*, *Rekening Bank*).

* **Hasil Pengujian**:
  * Kompilasi view Blade (`artisan view:cache` & `artisan view:clear`): **PASS (0 error)**.
  * Tampilan dashboard bersih tanpa banner heboh: **PASS**.
  * Indikator profil minimalis elegan: **PASS**.






---

---

### [2026-11-06 22:15 WITA] — Pembersihan Menyeluruh Data Dummy / Fallback Statis (Zero Dummy Fallback Standard & Kepatuhan Rule #94)

* **Modul / Fitur**: Seluruh Sistem (Model, Admin Views, Public Views, Client Views, Controller, & Readme)
* **File yang Diubah**:
  * `README.md`
  * `app/Models/RolePhotographer.php`
  * `app/Models/RoleClient.php`
  * `app/Http/Controllers/Public/ChatController.php`
  * `app/Http/Controllers/Admin/CategoryController.php`
  * `app/Http/Controllers/Admin/ContestController.php`
  * `app/Http/Controllers/Admin/ElearningController.php`
  * `app/Http/Controllers/Public/ContestController.php`
  * `app/Http/Controllers/Public/ContestProposalController.php`
  * `resources/views/admin/photographers/index.blade.php`
  * `resources/views/admin/dashboard.blade.php`
  * `resources/views/admin/contests/index.blade.php`
  * `resources/views/admin/elearning/index.blade.php`
  * `resources/views/admin/elearning/curriculum.blade.php`
  * `resources/views/admin/elearning/edit.blade.php`
  * `resources/views/client/favorites/index.blade.php`
  * `resources/views/public/photographers/index.blade.php`
  * `resources/views/public/photographers/katalog.blade.php`
  * `resources/views/public/photographers/show.blade.php`
  * `resources/views/public/home.blade.php`
  * `resources/views/public/profile/index.blade.php`
  * `resources/views/photographer/profile/index.blade.php`
  * `resources/views/photographer/dashboard.blade.php`
  * `resources/views/partials/table/table-01.blade.php`
  * `resources/views/partials/photographer/onboarding-wizard.blade.php`

* **Rincian Perubahan (Before vs After)**:
  1. **Eliminasi Fallback Gambar Stok Unsplash pada Pengguna & Mitra Baru**:
     * *Before*: Pengguna yang belum memiliki foto diarahkan ke gambar statis wanita Unsplash `https://images.unsplash.com/photo-1534528741775...`.
     * *After*: Seluruh fallback Unsplash dihapus total. Model mengembalikan `null` jika tidak ada file di database. Lapisan tampilan (Blade) merender avatar inisial 2 huruf kapital sesuai **Rule #44** (contoh: Kenji -> `KE`).
  2. **Eliminasi Fallback Wilayah / Lokasi Palsu ("Indonesia")**:
     * *Before*: Fotografer yang belum mengisi domisili kota otomatis diberi label `"Indonesia"`.
     * *After*: Jika relasi `city` atau kolom `alamat` kosong, tampilan secara jujur menampilkan strip `-`.
  3. **Eliminasi Fallback Kategori Palsu ("Semua Kategori")**:
     * *Before*: Pivot relasi `categories` yang kosong diberi label pengganti `"Semua Kategori"`.
     * *After*: Ditampilkan teks netral informatif `"Belum diatur"`.
  4. **Eliminasi Fallback Rating Palsu (4.9 / 5.0) & Bintang pada Akun Baru**:
     * *Before*: Accessor `rating_average` mengembalikan `4.9` atau `5.0` jika `avg('rating')` bernilai null. Tampilan dashboard dan kartu katalog otomatis memajang bintang emas `★ 4.9`.
     * *After*: `rating_average` mengembalikan `null` jika belum ada ulasan. Metrik editorial mengembalikan status `"Mitra Baru"` tanpa simbol bintang `★`. Tampilan kartu katalog menampilkan badge netral `"Baru"` atau `"Belum ada ulasan"`.
  5. **Eliminasi Fallback Harga Palsu (Rp 1.500.000 & Rp 149.000)**:
     * *Before*: Kartu katalog menggunakan fallback `min('tarif_harga') ?? 1500000`, e-learning menggunakan `harga ?? 149000`.
     * *After*: Jika fotografer belum memiliki paket layanan, kartu menampilkan teks murni `"Belum ada paket"`. Form edit e-learning dan tabel materi menampilkan harga riil database (`0` / `"Gratis"`).
  6. **Eliminasi Default Form Fiktif & Placeholder yang Menyerupai Data Terisi**:
     * *Before*: Form rekening bank profil fotografer default ke `'BCA'`, input nomor rekening memiliki placeholder `'1234567890'`, dan nama pemilik rekening memiliki placeholder `'Budi Santoso'`.
     * *After*: Variabel `selectedBank` diinisialisasi kosong `''` jika data di DB null, dan placeholder diubah menjadi kalimat instruksi yang jelas: `placeholder="Masukkan nomor rekening"` dan `placeholder="Masukkan nama pemilik rekening"`.
  7. **Pembersihan Grafik Analitik Omzet Admin Dashboard**:
     * *Before*: `$val = $monthlyRevenue[$idx] ?? 30;` dan `$fee = $monthlyCommission[$idx] ?? 3;` memanipulasi grafik dengan omzet fiktif Rp 30 Juta per bulan.
     * *After*: Menggunakan nilai riil transaksi database `$val = $monthlyRevenue[$idx] ?? 0;` dan `$fee = $monthlyCommission[$idx] ?? 0;` dengan bar dasar minimal 3% jika omzet 0.
  8. **Dokumentasi Baku pada README.md**:
     * Menambahkan **Rule #94: Standarisasi 100% Data Dinamis & Larangan Mutlak Data Palsu/Dummy/Fallback Statis (Zero Dummy Fallback Standard)**.

* **Standar / Rule README yang Dipatuhi**:
  * Point 15 (Standar Penggunaan Data Dinamis & Seeder Database - DILARANG HARDCODED HTML TEXT).
  * Rule #44 (Standarisasi Penanganan Foto Profil / Avatar Pengguna & Fallback Inisial).
  * Rule #46 (Anti AI-Slop & Larangan Konten Palsu/Gambar Dummy Tanpa Izin).
  * Rule #94 (Standarisasi 100% Data Dinamis & Larangan Mutlak Data Palsu/Dummy/Fallback Statis).

* **Hasil Pengujian & Verifikasi**:
  * Kompilasi View Blade: **PASS** (`artisan view:clear` & `artisan view:cache`).
  * Sintaks Kode PHP: **PASS** (100% valid tanpa syntax error).
  * Tampilan Admin `/admin/photographers`: Fotografer baru (Kenji) tampil dengan avatar inisial `KE`, wilayah operasional `-`, kategori `Belum diatur`, dan status `Menunggu`.


---

---

### [2026-11-07 10:35 WITA] — Pembuatan Halaman Detail Audit Verifikasi Admin & Perbaikan Bug hasRole

* **Modul / Fitur**: Manajemen Fotografer Admin & Etalase Publik (`/admin/photographers`, `/admin/photographers/{id}`, `/fotografer/{id}`)
* **File yang Diubah / Dibuat**:
  * `README.md` (Penambahan Rule #95)
  * `routes/web.php`
  * `app/Http/Controllers/Admin/PhotographerVerificationController.php`
  * `app/Http/Controllers/Public/PhotographerController.php`
  * `resources/views/admin/photographers/index.blade.php`
  * `resources/views/admin/photographers/show.blade.php` (File Baru)

* **Rincian Perubahan (Before vs After)**:
  1. **Perbaikan Bug 500 Fatal `hasRole()`**:
     * *Before*: `PhotographerController.php` memanggil `$user->hasRole(['admin', 'super_admin'])` yang tidak ada pada model `User`, memicu exception 500 saat admin mengecek profil fotografer yang belum selesai onboarding.
     * *After*: Diperbaiki menjadi `in_array(Auth::user()->role, ['admin', 'super_admin'])` dan rating average fallback dinetralkan menjadi `null` sesuai Rule #94.
  2. **Penyelarasan Bisnis Proses Manajemen Fotografer Admin**:
     * *Before*: Tombol aksi `Lihat` pada tabel `/admin/photographers` mengarahkan Admin ke halaman reservasi/etalase publik klien (`/fotografer/{id}`). Akibatnya Admin melihat tombol booking klien dan tidak bisa memverifikasi rekening payout atau kelengkapan data pendaftaran.
     * *After*: Tombol `Lihat` kini mengarahkan Admin ke halaman khusus audit verifikasi backoffice: `route('admin.photographers.show', $id)`.
  3. **Implementasi Halaman Khusus Audit Verifikasi (`/admin/photographers/{id}`)**:
     * Dibuat halaman detail komprehensif berstandar **Rule #73** dan **Rule #95**:
       - Ringkasan Identitas: Nama, email, tanggal daftar, lencana status, dan bar kelengkapan profil dengan daftar data yang masih kurang.
       - Seksi 1: Akun & Kontak (termasuk tombol pintas chat WhatsApp untuk verifikasi nomor).
       - Seksi 2: Domisili Operasional (Provinsi, Kota/Kabupaten, Alamat) & Kategori Spesialisasi.
       - Seksi 3: Rekening Bank Payout (Nama Bank, Nomor Rekening, Atas Nama Pemilik) untuk audit pencairan dana 90%.
       - Seksi 4: Media Visual (Avatar inisial, Foto Sampul, Deskripsi Bio).
       - Seksi 5: Kesiapan Paket Layanan & Portofolio.
       - Bilah Aksi Keputusan Admin: Tombol `Kembali`, tombol `Hapus Registrasi` (Rose soft dengan dialog modal konfirmasi), dan tombol `Verifikasi Mitra Fotografer` (Hijau Emerald sesuai Rule #91).
  4. **Pencatatan Dokumen Resmi Proyek**:
     * Dicatat aturan baku **Rule #95: Standarisasi Alur Detail & Audit Verifikasi Mitra Fotografer Admin (`/admin/photographers/{id}`)** pada `README.md`.

* **Standar / Rule README yang Dipatuhi**:
  * Bagian 1 (Arsitektur Aplikasi: Pemisahan Backoffice Admin vs Universal Client).
  * Rule #44 (Standarisasi Penanganan Foto Profil / Avatar Pengguna & Fallback Inisial).
  * Rule #73 (Standar Tata Letak Form Hybrid Responsif).
  * Rule #74 (Standar Tata Letak Tombol Aksi Formulir).
  * Rule #80 (Standarisasi Teks & Warna Tombol Aksi Tabel).
  * Rule #91 (Standarisasi Warna Tombol Aksi Formulir vs Aksi Persetujuan Transaksi - Emerald untuk Verifikasi).
  * Rule #94 (Standarisasi 100% Data Dinamis & Zero Dummy Fallback).
  * Rule #95 (Standarisasi Alur Detail & Audit Verifikasi Mitra Fotografer Admin).

* **Hasil Pengujian & Verifikasi**:
  * Kompilasi View Blade: **PASS** (`artisan view:clear` & `artisan view:cache`).
  * Uji Render Admin Show Kenji (id: 6 - data belum lengkap): **PASS** (Inisial `KE`, Status `Menunggu Verifikasi`, Progress `21%`, Tombol `Verifikasi Mitra Fotografer` aktif).
  * Uji Render Admin Show Clara (id: 3 - data lengkap): **PASS** (Data lengkap, Status `Terverifikasi`, Tombol `Batalkan Verifikasi` aktif).
  * Uji Public Show Bebas Error 500: **PASS** (100% bebas dari `hasRole()` bug).

---

### Perubahan Tanggal: 2026-11-07 14:42 WIB
* **File yang Ditambahkan / Dimodifikasi**:
  1. `app/Models/RolePhotographer.php`
  2. `resources/views/admin/photographers/show.blade.php`
  3. `resources/views/photographer/profile/index.blade.php`

* **Deskripsi Perubahan Teknis (Eliminasi AI Slop & Implementasi Dot Step Indicator 3B)**:
  1. **Penambahan Helper Seksi Profil di Model `RolePhotographer`**:
     * Menambahkan method `getProfileSectionsStatus()` untuk memetakan kelengkapan 4 seksi esensial fotografer (Kontak, Domisili & Kategori, Rekening Bank, Foto & Bio).
     * Menghitung `completed_count`, `total_sections` (4 bagian), dan daftar nama seksi yang belum lengkap.
  2. **Eliminasi Progress Bar Horizontal & AI Slop Red Badge Pills di Sisi Admin (`admin/photographers/show.blade.php`)**:
     * *Before*: Bar kuning panjang memanjang horizontal yang terkesan gamified template slop, serta belasan badge pink/merah berjejer untuk daftar data yang belum diisi.
     * *After*: Diganti dengan **Gaya 3B: Dot Step Indicator** ringkas (`Kelengkapan: 29% • ● ○ ○ ○ (1 dari 4 bagian)`) dan daftar field yang belum diisi berupa teks bersih netral dengan pemisah koma: `Belum diisi: Instagram, Domisili (Provinsi & Kota), Kategori, Bio, Foto Profil, Foto Sampul, Rekening Bank.`
  3. **Standardisasi di Sisi Fotografer (`photographer/profile/index.blade.php`)**:
     * Menghapus progress bar kuning horizontal melar.
     * Menerapkan Dot Step Indicator (Gaya 3B) yang responsif dan teks daftar data belum diisi yang to-the-point tanpa chip/tag yang berisik.

* **Standar / Rule README yang Dipatuhi**:
  * Rule #51 (Anti AI Slop Copywriting & UI Cleanliness).
  * Rule #70 (Standarisasi Tipografi & Copywriting Ringkas To The Point).
  * Rule #94 (Standarisasi 100% Data Dinamis & Audit Traceability).
  * Rule #95 (Standarisasi Alur Detail & Audit Verifikasi Mitra Fotografer Admin).

* **Hasil Pengujian**:
  * `php artisan view:clear` & `view:cache` via PHP 8.4: **0 Errors / PASS**.

---

### Perubahan Tanggal: 2026-11-07 17:15 WIB
* **File yang Ditambahkan / Dimodifikasi**:
  1. `resources/views/photographer/profile/index.blade.php`
  2. `resources/views/admin/photographers/show.blade.php`
  3. `resources/views/public/profile/index.blade.php`
  4. `README.md` (Menambahkan Rule #97)

* **Deskripsi Perubahan Teknis (Implementasi Standar Baru Rule #97: Pratinjau Modal Lightbox Interaktif pada Seluruh Input Field & Kotak Media Gambar)**:
  1. **Interaktivitas Pratinjau Modal pada Formulir Profil Fotografer (`photographer/profile/index.blade.php`)**:
     * Menambahkan state Alpine.js (`imagePreviewModalOpen`, `imagePreviewSrc`, `imagePreviewTitle`) dan method pembuka/penutup modal (`openImagePreview`, `closeImagePreview`).
     * Pada kotak pratinjau **Foto Profil** dan **Foto Sampul**: jika gambar telah ada (baik foto yang sudah tersimpan di database maupun file lokal baru yang dipilih fotografer via FileReader), kotak pratinjau menjadi interaktif dengan kursor pointer (`cursor-pointer group`), efek perbesaran halus (`group-hover:scale-105`), dan *hover overlay* elegan bertuliskan ikon kaca pembesar dan teks pratinjau.
     * Mengklik kotak pratinjau membuka dialog modal *fullscreen lightbox* berlatar belakang *backdrop-blur* gelap (`bg-black/90 backdrop-blur-md`) dengan gambar resolusi penuh yang tajam dan proporsional.
     * Jika field masih kosong (empty state bergaris putus-putus dengan ikon kamera abu-abu), logika *lightbox* tidak aktif sehingga klik tetap fokus pada unggah berkas.
  2. **Interaktivitas Pratinjau Modal pada Halaman Audit Admin (`admin/photographers/show.blade.php`)**:
     * Mengintegrasikan state dan modal *lightbox* pratinjau foto resolusi penuh pada kartu audit Admin Card 4 (Foto Profil dan Foto Sampul).
     * Memungkinkan Administrator memeriksa foto profil atau foto sampul fotografer dalam resolusi tinggi dan tajam sebelum mengambil keputusan verifikasi atau penolakan.
  3. **Penerapan Standar pada Formulir Profil Publik (`public/profile/index.blade.php`)**:
     * Menambahkan fitur pratinjau modal *lightbox* pada pratinjau foto avatar profil pengguna.
  4. **Pencatatan Dokumen Resmi Proyek**:
     * Mendokumentasikan **Rule #97: Standarisasi Pratinjau Modal (Lightbox) Interaktif pada Seluruh Input Field & Kotak Pratinjau Media Gambar (Interactive Image Field Lightbox Standard)** ke dalam `README.md`.

* **Standar / Rule README yang Dipatuhi**:
  * Rule #51 (Anti AI Slop Copywriting & UI Cleanliness).
  * Rule #73 (Standar Tata Letak Form Hybrid Responsif).
  * Rule #94 (Standarisasi 100% Data Dinamis & Audit Traceability).
  * Rule #96 (Standarisasi Proporsi Card Media & Larangan Pemanipulasian Tinggi Seragam).
  * Rule #97 (Standarisasi Pratinjau Modal Lightbox Interaktif pada Media Gambar).

* **Hasil Pengujian**:
  * `php artisan view:clear` & `view:cache` via PHP 8.4: **0 Errors / PASS**.

---

### Perubahan Tanggal: 2026-11-07 15:22 WIB
* **File yang Ditambahkan / Dimodifikasi**:
  1. `resources/views/photographer/profile/index.blade.php`

* **Deskripsi Perubahan Teknis (Perbaikan Dropdown Alpine.js & Ikon Kamera Empty State)**:
  1. **Perbaikan Syntax Error JavaScript pada Dropdown (`photographerProfileForm`)**:
     * *Penyebab*: Penggunaan `{{ json_encode(...) }}` pada `allCategories`, `cities`, dan `selectedCategories` di dalam tag `<script>` menyebabkan Blade meng-escape karakter tanda kutip menjadi entitas HTML `&quot;` dan `&amp;` (contoh: `[{&quot;id&quot;:1...}]`).
     * *Dampak*: Terjadi `SyntaxError: Unexpected token '&'` di console browser sehingga runtime Alpine.js mogok (*crash*) dan seluruh event `@click` pada dropdown (Provinsi, Kota, Kategori, Bank) tidak merespons.
     * *Solusi*: Mengganti seluruh format serialization menjadi `{!! json_encode(...) !!}` tanpa escape HTML sehingga JSON valid dan seluruh dropdown bekerja responsif seketika.
  2. **Standardisasi Ikon Kamera pada Kotak Upload Foto Profil & Sampul (Seksi 4)**:
     * *Before*: Kotak preview hanya menampilkan background abu-abu polos tanpa garis batas dashed dan tanpa ikon kamera jika belum ada foto yang diunggah.
     * *After*: Diselaraskan dengan standar tampilan admin: menggunakan container `border-2 border-dashed border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-800` dan ikon kamera SVG berwarna abu-abu (`text-gray-400 dark:text-gray-500`) yang muncul secara default saat data foto masih kosong. Saat berkas foto dipilih, kotak bertransisi menjadi `border-solid border-amber-400` dengan preview foto penuh.

* **Standar / Rule README yang Dipatuhi**:
  * Rule #44 (Standarisasi Penanganan Foto Profil / Avatar Pengguna & Fallback Inisial/Ikon).
  * Rule #46 (Konsistensi Dropdown Custom Alpine.js).
  * Rule #94 (Standarisasi 100% Data Dinamis & Audit Traceability).

* **Hasil Pengujian**:
  * Evaluasi Blade & JavaScript Output: **PASS** (JSON valid, tanpa entitas `&quot;`).
  * `php artisan view:clear` & `view:cache`: **0 Errors / PASS**.

---

### Perubahan Tanggal: 2026-11-07 16:29 WIB
* **File yang Ditambahkan / Dimodifikasi**:
  1. `database/seeders/DatabaseSeeder.php`
  2. `resources/views/photographer/profile/index.blade.php`

* **Deskripsi Perubahan Teknis (Pemulihan Data Wilayah Indonesia & UI Fallback)**:
  1. **Investigasi & Pemulihan Data Tabel Wilayah (`indonesia_provinces` & `indonesia_cities`)**:
     * *Temuan*: Dropdown berhasil terbuka (status `provinceDropdownOpen = true` dan chevron berputar), namun menu terlihat pipih kosong karena tabel `indonesia_provinces` di database berisi **0 baris data**.
     * *Penyebab di Seeder*: `DatabaseSeeder.php` sebelumnya memanggil class `Laravolt\Indonesia\Seeds\IndonesiaSeeder` yang sudah tidak ada di versi package terpasang.
     * *Perbaikan*: Memperbarui `DatabaseSeeder.php` untuk memanggil `ProvincesSeeder` dan `CitiesSeeder`. Menjalankan seeding wilayah sehingga kini tersedia **38 Provinsi** dan **514 Kota/Kabupaten** di database.
  2. **Penyempurnaan Blade Template Dropdown**:
     * Mengubah `@foreach` pada dropdown provinsi menjadi `@forelse` dengan fallback teks bersih (`Data provinsi belum tersedia.`) jika terjadi kegagalan muat data.

* **Standar / Rule README yang Dipatuhi**:
  * Rule #46 (Konsistensi Dropdown Custom Alpine.js).
  * Rule #94 (Standarisasi 100% Data Dinamis & Zero Dummy Fallback).

* **Hasil Pengujian**:
  * Query Database: **PASS** (`indonesia_provinces` = 38 rows, `indonesia_cities` = 514 rows).
  * Uji Render View: **PASS** (38 tombol opsi provinsi ter-render sempurna di dalam dropdown).
  * `php artisan view:clear` & `view:cache`: **0 Errors / PASS**.

---

### Perubahan Tanggal: 2026-11-07 16:31 WIB
* **File yang Ditambahkan / Dimodifikasi**:
  1. `resources/views/photographer/profile/index.blade.php`

* **Deskripsi Perubahan Teknis (Eliminasi Divider Border Tombol Formulir)**:
  1. Menghapus garis pembatas horizontal (`border-t border-gray-100 dark:border-gray-700/60`) tepat di atas tombol submit **Perbaharui Data** pada:
     - Form Profil Fotografer (setelah Seksi 5: Rekening Bank).
     - Form Ganti Kata Sandi (setelah Seksi 6: Kata Sandi).
  2. Tampilan tombol kini menyatu secara elegan dan bersih (*clean spacing*) tanpa ada garis divider yang memotong ruang kartu.

* **Standar / Rule README yang Dipatuhi**:
  * Rule #51 (Anti AI Slop Copywriting & UI Cleanliness).
  * Rule #74 (Standar Tata Letak Tombol Aksi Formulir).

* **Hasil Pengujian**:
  * `php artisan view:clear` & `view:cache`: **0 Errors / PASS**.

---

### Perubahan Tanggal: 2026-11-07 16:58 WIB
* **File yang Ditambahkan / Dimodifikasi**:
  1. `resources/views/admin/photographers/show.blade.php`
  2. `resources/views/photographer/profile/index.blade.php`
  3. `README.md` (Menambahkan Rule #96)

* **Deskripsi Perubahan Teknis (Implementasi Standar Baru Rule #96: Proporsi Card Media & Larangan Pemanipulasian Tinggi Seragam)**:
  1. **Layout Vertikal & Dimensi Proporsional Card 4 (Foto Profil & Sampul) di Admin Show**:
     * Mengubah penempatan foto dari horizontal 3 kolom yang membuat banner sampul pipih gepeng menjadi **layout bertumpuk vertikal**:
       - Foto Profil: `w-24 h-24` rounded-2xl dengan ikon kamera outline abu-abu di tengah.
       - Foto Sampul: Banner lanskap proporsional rasio **16:9** yang tinggi dan jelas (`w-full h-48 sm:h-56`), sehingga foto sampul atau kotak kamera tidak tertekan (*gepeng*).
     * Card 4 ditinggikan dengan padding lega (`p-6 space-y-5`).
  2. **Pemberlakuan `items-start` pada Grid Multi-Kolom**:
     * Memberikan class `items-start` pada grid container utama.
     * Card 3 (Bio) kini mempertahankan tinggi naturalnya secara ramping dan rapi, **TIDAK LAGI** dipaksa melar/molor dengan ruang kosong bawah hanya demi menyamakan tinggi Card 4 di sebelahnya.
  3. **Penyempurnaan Seksi 4 pada Profil Fotografer (`photographer/profile/index.blade.php`)**:
     * Card ditinggikan dengan `p-6 sm:p-7 space-y-4` sehingga tidak terlihat sumpek.
     * Foto Profil diatur dengan avatar `w-28 h-28` dan tombol aksi vertikal.
     * Foto Sampul diatur dengan rasio 16:9 (`w-full h-44 sm:h-48`) yang tinggi dan proporsional.
  4. **Pencatatan Dokumen Resmi Proyek**:
     * Mendokumentasikan **Rule #96: Standarisasi Proporsi Card Media & Larangan Pemanipulasian Tinggi Seragam (Anti-Artificial Equal Height on Asymmetric Cards)** ke dalam `README.md`.

* **Standar / Rule README yang Dipatuhi**:
  * Rule #51 (Anti AI Slop Copywriting & UI Cleanliness).
  * Rule #73 (Standar Tata Letak Form Hybrid Responsif).
  * Rule #94 (Standarisasi 100% Data Dinamis & Audit Traceability).
  * Rule #95 (Standarisasi Alur Detail & Audit Verifikasi Mitra Fotografer Admin).
  * Rule #96 (Standarisasi Proporsi Card Media & Larangan Pemanipulasian Tinggi Seragam).

* **Hasil Pengujian**:
  * `php artisan view:clear` & `view:cache` via PHP 8.4: **0 Errors / PASS**.

---

### Perubahan Tanggal: 2026-11-07 16:36 WIB
* **File yang Ditambahkan / Dimodifikasi**:
  1. `resources/views/photographer/profile/index.blade.php`

* **Deskripsi Perubahan Teknis (Penambahan Indikator Wajib '*' pada Judul Seksi Bio)**:
  1. Menambahkan tanda bintang merah `<span class="text-rose-500">*</span>` pada judul **3. Bio** di formulir profil fotografer, selaras dengan status validasi wajib (*required min. 15 karakter*) dan konsisten dengan seluruh seksi wajib lainnya.

* **Standar / Rule README yang Dipatuhi**:
  * Rule #51 (Anti AI Slop Copywriting & UI Cleanliness).
  * Rule #94 (Standarisasi 100% Data Dinamis & Audit Traceability).

* **Hasil Pengujian**:
  * `php artisan view:clear` & `view:cache`: **0 Errors / PASS**.

---

### Perubahan Tanggal: 2026-11-07 16:58 WIB
* **File yang Ditambahkan / Dimodifikasi**:
  1. `resources/views/admin/photographers/show.blade.php`
  2. `resources/views/photographer/profile/index.blade.php`
  3. `README.md` (Menambahkan Rule #96)

* **Deskripsi Perubahan Teknis (Implementasi Standar Baru Rule #96: Proporsi Card Media & Larangan Pemanipulasian Tinggi Seragam)**:
  1. **Layout Vertikal & Dimensi Proporsional Card 4 (Foto Profil & Sampul) di Admin Show**:
     * Mengubah penempatan foto dari horizontal 3 kolom yang membuat banner sampul pipih gepeng menjadi **layout bertumpuk vertikal**:
       - Foto Profil: `w-24 h-24` rounded-2xl dengan ikon kamera outline abu-abu di tengah.
       - Foto Sampul: Banner lanskap proporsional rasio **16:9** yang tinggi dan jelas (`w-full h-48 sm:h-56`), sehingga foto sampul atau kotak kamera tidak tertekan (*gepeng*).
     * Card 4 ditinggikan dengan padding lega (`p-6 space-y-5`).
  2. **Pemberlakuan `items-start` pada Grid Multi-Kolom**:
     * Memberikan class `items-start` pada grid container utama.
     * Card 3 (Bio) kini mempertahankan tinggi naturalnya secara ramping dan rapi, **TIDAK LAGI** dipaksa melar/molor dengan ruang kosong bawah hanya demi menyamakan tinggi Card 4 di sebelahnya.
  3. **Penyempurnaan Seksi 4 pada Profil Fotografer (`photographer/profile/index.blade.php`)**:
     * Card ditinggikan dengan `p-6 sm:p-7 space-y-4` sehingga tidak terlihat sumpek.
     * Foto Profil diatur dengan avatar `w-28 h-28` dan tombol aksi vertikal.
     * Foto Sampul diatur dengan rasio 16:9 (`w-full h-44 sm:h-48`) yang tinggi dan proporsional.
  4. **Pencatatan Dokumen Resmi Proyek**:
     * Mendokumentasikan **Rule #96: Standarisasi Proporsi Card Media & Larangan Pemanipulasian Tinggi Seragam (Anti-Artificial Equal Height on Asymmetric Cards)** ke dalam `README.md`.

* **Standar / Rule README yang Dipatuhi**:
  * Rule #51 (Anti AI Slop Copywriting & UI Cleanliness).
  * Rule #73 (Standar Tata Letak Form Hybrid Responsif).
  * Rule #94 (Standarisasi 100% Data Dinamis & Audit Traceability).
  * Rule #95 (Standarisasi Alur Detail & Audit Verifikasi Mitra Fotografer Admin).
  * Rule #96 (Standarisasi Proporsi Card Media & Larangan Pemanipulasian Tinggi Seragam).

* **Hasil Pengujian**:
  * `php artisan view:clear` & `view:cache` via PHP 8.4: **0 Errors / PASS**.

---

### Perubahan Tanggal: 2026-11-07 17:15 WIB
* **File yang Ditambahkan / Dimodifikasi**:
  1. `resources/views/photographer/profile/index.blade.php`
  2. `resources/views/admin/photographers/show.blade.php`
  3. `resources/views/public/profile/index.blade.php`
  4. `README.md` (Menambahkan Rule #97)

* **Deskripsi Perubahan Teknis (Implementasi Standar Baru Rule #97: Pratinjau Modal Lightbox Interaktif pada Seluruh Input Field & Kotak Media Gambar)**:
  1. **Interaktivitas Pratinjau Modal pada Formulir Profil Fotografer (`photographer/profile/index.blade.php`)**:
     * Menambahkan state Alpine.js (`imagePreviewModalOpen`, `imagePreviewSrc`, `imagePreviewTitle`) dan method pembuka/penutup modal (`openImagePreview`, `closeImagePreview`).
     * Pada kotak pratinjau **Foto Profil** dan **Foto Sampul**: jika gambar telah ada (baik foto yang sudah tersimpan di database maupun file lokal baru yang dipilih fotografer via FileReader), kotak pratinjau menjadi interaktif dengan kursor pointer (`cursor-pointer group`), efek perbesaran halus (`group-hover:scale-105`), dan *hover overlay* elegan bertuliskan ikon kaca pembesar dan teks pratinjau.
     * Mengklik kotak pratinjau membuka dialog modal *fullscreen lightbox* berlatar belakang *backdrop-blur* gelap (`bg-black/90 backdrop-blur-md`) dengan gambar resolusi penuh yang tajam dan proporsional.
     * Jika field masih kosong (empty state bergaris putus-putus dengan ikon kamera abu-abu), logika *lightbox* tidak aktif sehingga klik tetap fokus pada unggah berkas.
  2. **Interaktivitas Pratinjau Modal pada Halaman Audit Admin (`admin/photographers/show.blade.php`)**:
     * Mengintegrasikan state dan modal *lightbox* pratinjau foto resolusi penuh pada kartu audit Admin Card 4 (Foto Profil dan Foto Sampul).
     * Memungkinkan Administrator memeriksa foto profil atau foto sampul fotografer dalam resolusi tinggi dan tajam sebelum mengambil keputusan verifikasi atau penolakan.
  3. **Penerapan Standar pada Formulir Profil Publik (`public/profile/index.blade.php`)**:
     * Menambahkan fitur pratinjau modal *lightbox* pada pratinjau foto avatar profil pengguna.
  4. **Pencatatan Dokumen Resmi Proyek**:
     * Mendokumentasikan **Rule #97: Standarisasi Pratinjau Modal (Lightbox) Interaktif pada Seluruh Input Field & Kotak Pratinjau Media Gambar (Interactive Image Field Lightbox Standard)** ke dalam `README.md`.

* **Standar / Rule README yang Dipatuhi**:
  * Rule #51 (Anti AI Slop Copywriting & UI Cleanliness).
  * Rule #73 (Standar Tata Letak Form Hybrid Responsif).
  * Rule #94 (Standarisasi 100% Data Dinamis & Audit Traceability).
  * Rule #96 (Standarisasi Proporsi Card Media & Larangan Pemanipulasian Tinggi Seragam).
  * Rule #97 (Standarisasi Pratinjau Modal Lightbox Interaktif pada Media Gambar).

* **Hasil Pengujian**:
  * `php artisan view:clear` & `view:cache` via PHP 8.4: **0 Errors / PASS**.

---

### [2026-11-07 14:20 WITA] — Implementasi Mini Kalender Interaktif & Time-Slot Availability Matrix pada Formulir Reservasi (Rule #96)

* **Modul / Fitur**: Seksi 2 Jadwal Pemotretan pada Formulir Reservasi Klien (`/booking/create`) & Backend Booking Validation
* **File yang Diubah**:
  * [`app/Http/Controllers/Public/BookingController.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/app/Http/Controllers/Public/BookingController.php)
  * [`resources/views/public/booking/create.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/public/booking/create.blade.php)
  * [`README.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/README.md)
  * [`HANDOVER.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/HANDOVER.md)
  * [`TRACKING_PERUBAHAN_FILE.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/TRACKING_PERUBAHAN_FILE.md)

* **Rincian Perubahan**:
  1. **Transformasi Pemilihan Tanggal Menjadi Mini Kalender Bulan Interaktif**:
     * Mengganti dropdown dan input tanggal kaku dengan **Mini Kalender Interaktif** yang proporsional di dalam Seksi 2.
     * Klien dapat melihat status tanggal dalam 1 bulan kalender (navigasi bulan dengan single chevron `<` dan `>`).
     * Tanggal lampau dan tanggal libur seharian penuh fotografer secara otomatis diabukan (*grayed out*) dengan label mikro `Libur`. Tanggal yang aktif langsung bisa diklik dan disorot dengan aksen amber LensMatch.
  2. **Implementasi Time-Slot Matrix Chips (Multi-Booking & Jeda Istirahat / Sesi Estafet)**:
     * Tepat di bawah kalender mini, menyajikan kandidat jam mulai sesi (`08:00`, `10:00`, `12:00`, `14:00`, `16:00`, `18:00`, `20:00` WIB) dalam bentuk kartu chip grid yang tenang dan terstruktur.
     * Menghitung status ketersediaan setiap jam secara real-time via Alpine.js tanpa reload:
       - **Tersedia**: Kartu putih bersih yang dapat diklik (label hijau `Tersedia`).
       - **Terpilih**: Solid amber LensMatch (`bg-amber-400 text-gray-950 font-black shadow-xs`).
       - **Terisi**: Jika slot jam beririsan dengan pesanan booking klien lain yang aktif di tanggal tersebut (`opacity-50 border-dashed cursor-not-allowed`, label `Terisi`).
       - **Istirahat / Tutup**: Jika slot jam beririsan dengan penutupan jadwal / jeda istirahat yang diatur manual oleh fotografer (label `Istirahat` atau `Tutup`).
  3. **Penyesuaian Durasi Sesi Dinamis di Backend (`BookingController.php`)**:
     * Pada method `create`: Memuat data `workingHours`, `availabilities`, dan `bookedSlots` secara lengkap untuk evaluasi slot jam di frontend.
     * Pada method `store`: Menghitung `$bookingEnd` secara dinamis berbasis durasi paket (`$durasi = (int) $request->input('durasi', 2)`), menyimpan durasi riil dan jam selesai yang presisi ke tabel `contract_bookings`.
  4. **Kepatuhan Anti-AI Slop & Zero Dividers**:
     * Menghilangkan warna merah mencolok pada slot tidak tersedia; diganti abu-abu redup tenang (*soft grayed-out*).
     * Nol garis divider (`0 border-t / border-b`) pemotong kartu.
     * Bebas dari simbol `&` redundan dan teks alay berkepanjangan.

* **Hasil Pengujian**:
  * Render Test View: **PASS (113,493 bytes)**.
  * Tag Div Balance: **172 Open / 172 Close (100% Sempurna)**.
  * Vite Asset Build: **PASS (2.52s)**.

---

---

### [2026-11-07 11:45 WITA] — Restrukturisasi Alur Formulir Reservasi Sesi Foto: Seksi 1 Pemilihan Paket Layanan & Real-Time Alpine.js Calculation (Anti-AI Slop & Zero Dividers)

* **Modul / Fitur**: Formulir Reservasi Sesi Foto Publik (`/booking/create?photographer_id={id}`)
* **File yang Diubah**:
  * [`app/Http/Controllers/Public/BookingController.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/app/Http/Controllers/Public/BookingController.php)
  * [`resources/views/public/booking/create.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/public/booking/create.blade.php)
  * [`README.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/README.md)
  * [`HANDOVER.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/HANDOVER.md)
  * [`TRACKING_PERUBAHAN_FILE.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/TRACKING_PERUBAHAN_FILE.md)

* **Rincian Perubahan**:
  1. **Eliminasi Pemaksaan Paket Otomatis (*Forced Pre-Selection Bug*)**:
     * *Sebelumnya*: Di `BookingController@create`, jika parameter `service_id` tidak ada di URL saat klien mengklik tombol *"Reservasi"*, sistem memaksakan paket pertama (`$service = $allServices->first()`), sehingga paket langsung terkunci otomatis di sisi kanan tanpa pilihan eksplisit oleh klien.
     * *Sesudahnya*: Mengubah `$serviceId = $request->input('service_id', old('service_id'))` dan `$service = $serviceId ? $allServices->firstWhere('id', $serviceId) : null`. Jika tidak membawa `service_id`, formulir terbuka dalam kondisi bersih tanpa paket terpilih.
  2. **Restrukturisasi Urutan Langkah Formulir Kiri (Urutan Logis & Alami)**:
     * **Seksi 1 (Paling Atas)**: **Pilih Paket Layanan**. Menampilkan kartu interaktif radio untuk seluruh paket yang disediakan oleh fotografer (Nama Paket, Tarif Harga, Deskripsi, dan cuplikan hingga 4 benefit fasilitas). Klien dapat mengklik kartu mana saja untuk memilih paket.
     * **Seksi 2**: **Jadwal Pelaksanaan Sesi Foto** (Pemilihan tanggal sesi dan jam mulai sesi dengan dropdown preset).
     * **Seksi 3**: **Lokasi Pemotretan** (Provinsi, Kota/Kabupaten via AJAX dynamic fetch, dan textarea alamat lengkap venue).
     * **Seksi 4**: **Catatan Khusus** (Tema busana, konsep, preferensi).
  3. **Reaktivitas Penuh Alpine.js pada Kolom Kanan (Zero Reload)**:
     * Sebelum paket dipilih (`!selectedServiceId`): Kolom kanan menampilkan prompt ramah *"Pilih Paket Layanan: Silakan tentukan paket layanan sesi foto pada formulir di sebelah kiri terlebih dahulu"*, total tagihan Rp 0, dan tombol submit dinonaktifkan (`disabled`) dengan teks *"Pilih Paket Layanan Terlebih Dahulu"*.
     * Setelah paket dipilih: Rincian paket langsung terisi, daftar add-on fitur tambahan (opsional) muncul otomatis sesuai paket terpilih, dan total tagihan (Bayar Penuh vs DP 30%) terkalkulasi secara real-time via Alpine.js tanpa reload halaman.
  4. **Kepatuhan Anti-AI Slop & Zero Dividers**:
     * Menghapus custom breadcrumb navigasi palsu di atas judul halaman sesuai **Rule #72**.
     * Menghilangkan seluruh garis pembatas horizontal (`0 border-t / border-b`) pemotong kartu di header seksi dan footer ringkasan tagihan sesuai **Rule #67**.
     * Menghilangkan penggunaan tanda `&` redundan pada copywriting judul (*"Ringkasan Pesanan"*, bukan *"Ringkasan Pesanan & Tarif"* atau *"Rincian & Total"*) sesuai **Rule #70**.
     * Memastikan tombol submit menggunakan standar brand amber LensMatch (`bg-amber-400 hover:bg-amber-500 font-extrabold text-gray-950`) sesuai **Rule #91**.

* **Hasil Pengujian**:
  * Render Test View (Without `service_id`): **PASS (100,288 bytes - State unselected)**.
  * Render Test View (With `service_id = 2`): **PASS (100,285 bytes - State selected)**.
  * Div Balance Test: **168 Open / 168 Close (100% Sempurna)**.
  * Vite Asset Build: **PASS (1.84s)**.

---

---

### [2026-11-07 11:10 WITA] — Transformasi Unified Master Profile Card: Membentang Penuh Menyeluruh Sampai ke Bawah (Rule #66)

* **Modul / Fitur**: Halaman Detail Profil Fotografer Publik (`/fotografer/{id}`)
* **File yang Diubah**:
  * [`resources/views/public/photographers/show.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/public/photographers/show.blade.php)
  * [`README.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/README.md)
  * [`HANDOVER.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/HANDOVER.md)
  * [`TRACKING_PERUBAHAN_FILE.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/TRACKING_PERUBAHAN_FILE.md)

* **Rincian Perubahan**:
  1. **Eliminasi UI Terfragmentasi (*Disconnected Floating Islands*)**:
     * *Sebelumnya*: Kartu profil atas dibungkus dalam container kecil terpisah yang langsung ditutup tepat setelah tombol aksi. Akibatnya, baris tab navigasi dan seksi konten di bawahnya (seperti kartu paket layanan) melayang terpisah di luar kartu pada background abu-abu, menciptakan kesan visual yang terpotong dan kosong.
     * *Sesudahnya*: Mengubah kartu profil menjadi **Unified Master Profile Card** (`bg-white dark:bg-gray-800 rounded-3xl p-6 sm:p-8 lg:p-10 border border-gray-200/80 dark:border-gray-700/80 shadow-xs space-y-8 sm:space-y-10`) yang membentang penuh ke bawah mencakup seluruh tata letak halaman:
       - Bagian Atas: Header Profil Fotografer (Foto Potret 320px + Detail Info + 3 Baris Statistik + Tombol `Reservasi` & `Chat`).
       - Bagian Tengah: Baris Tab Navigasi (`Paket Layanan`, `Galeri Portofolio`, `Kalender Ketersediaan`, `Ulasan Klien`, `Tanya Jawab`).
       - Bagian Bawah: Seluruh Konten Tab yang Terpilih.
  2. **Elevasi Kartu Anak (Nested Card Contrast)**:
     * Seluruh kartu anak di dalam tab (kartu paket pemotretan, album portofolio, container kalender, kartu breakdown ulasan, kartu testimoni klien, dan kartu akordeon FAQ) kini menggunakan latar lembut **`bg-gray-50/70 dark:bg-gray-750/70 rounded-2xl border border-gray-200/80 dark:border-gray-700/80`**, memberikan hierarki visual yang solid, terstruktur rapi, dan menyatu alami tanpa divider kaku.

* **Hasil Pengujian**:
  * Tag Div Balance: **95 Open / 95 Close (100% Balanced)**.
  * Render Test View `public.photographers.show`: **PASS (116,444 bytes)**.
  * Vite Asset Build: **PASS (1.50s)**.

---

---

### [2026-11-07 11:05 WITA] — Penyederhanaan Teks Tombol Aksi Menjadi Murni 1 Kata (Pure Anti-AI Slop: "Reservasi" & "Chat")

* **Modul / Fitur**: Halaman Detail Profil Fotografer (`/fotografer/{id}`) & Daftar Favorit Klien (`/client/favorites`)
* **File yang Diubah**:
  * [`resources/views/public/photographers/show.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/public/photographers/show.blade.php)
  * [`resources/views/client/favorites/index.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/client/favorites/index.blade.php)
  * [`README.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/README.md)
  * [`HANDOVER.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/HANDOVER.md)
  * [`TRACKING_PERUBAHAN_FILE.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/TRACKING_PERUBAHAN_FILE.md)

* **Rincian Perubahan**:
  1. **Eliminasi Kata Ekstra Berlebihan (*Pure Anti-AI Slop*)**:
     * Menghapus kata redundan pada tombol aksi yang sebelumnya menambahkan *"Sesi"* dan *"Fotografer"*. Pada konteks halaman profil atau kartu fotografer, konteks sudah jelas sehingga penambahan kata tersebut tidak memiliki urgensi dan justru merupakan karakteristik AI slop.
  2. **Penyederhanaan Menjadi 1 Kata To The Point**:
     * Tombol Utama (Amber LensMatch): Diubah dari `"Reservasi Sesi"` menjadi **`"Reservasi"`**.
     * Tombol Sekunder (Abu-abu Netral): Diubah dari `"Chat Fotografer"` menjadi **`"Chat"`**.
  3. **Konsistensi Lintas Halaman**:
     * Menerapkan standar 1 kata ini secara seragam pada header detail profil fotografer (`show.blade.php`) dan kartu baris horizontal favorit klien (`client/favorites/index.blade.php`).

* **Hasil Pengujian**:
  * Render Test View `public.photographers.show`: **PASS (Tombol "Reservasi" & "Chat")**.
  * Render Test View `client.favorites.index`: **PASS (Tombol "Reservasi" & "Chat")**.
  * Vite Asset Build: **PASS (1.47s)**.

---

---

### [2026-11-07 11:00 WITA] — Penyelarasan Judul Seksi Konten dengan Label Tab Navigasi (Rule #78 Point 5 & Anti-AI Slop)

* **Modul / Fitur**: Halaman Detail Profil Fotografer Publik (`/fotografer/{id}`)
* **File yang Diubah**:
  * [`resources/views/public/photographers/show.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/public/photographers/show.blade.php)
  * [`README.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/README.md)
  * [`HANDOVER.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/HANDOVER.md)
  * [`TRACKING_PERUBAHAN_FILE.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/TRACKING_PERUBAHAN_FILE.md)

* **Rincian Perubahan**:
  1. **Inkonsistensi Judul Konten (*Before*)**:
     * Pada tombol tab navigasi atas, seluruh label konsisten terdiri dari 2 kata yang to the point: *Paket Layanan*, *Galeri Portofolio*, *Kalender Ketersediaan*, *Ulasan Klien*, dan *Tanya Jawab*.
     * Namun pada Tab 1 (Paket Layanan), judul konten di bawahnya diribetkan dan bertele-tele (*AI slop*): `<h2>Pilihan Paket Layanan Sesi Foto</h2>` (5 kata), padahal 4 tab lainnya menggunakan judul yang identik persis dengan tombol tabnya.
  2. **Penyelarasan 100% Identik (*After*)**:
     * Mengubah `<h2>Pilihan Paket Layanan Sesi Foto</h2>` menjadi **`<h2>Paket Layanan</h2>`** (2 kata).
     * Kelima tab kini seragam dan sinkron 1:1 antara tombol tab dan judul konten yang terbuka:
       - Tab 1: **Paket Layanan** ➔ Judul: **Paket Layanan**
       - Tab 2: **Galeri Portofolio** ➔ Judul: **Galeri Portofolio**
       - Tab 3: **Kalender Ketersediaan** ➔ Judul: **Kalender Ketersediaan**
       - Tab 4: **Ulasan Klien** ➔ Judul: **Ulasan Klien**
       - Tab 5: **Tanya Jawab** ➔ Judul: **Tanya Jawab**

* **Hasil Pengujian**:
  * Render Test View `public.photographers.show`: **PASS (116,308 bytes)**.
  * Audit Keselarasan 5 Tab: **100% SINKRON (Exact 2-Word Matching)**.
  * Vite Asset Build: **PASS (1.51s)**.

---

---

### [2026-11-07 10:55 WITA] — Implementasi Opsi A: Horizontal Row Cards Halaman Fotografer Favorit Klien (Upwork / HoneyBook Style, Anti-AI Slop, Zero Dividers)

* **Modul / Fitur**: Halaman Daftar Fotografer Favorit Klien (`/client/favorites`)
* **File yang Diubah**:
  * [`resources/views/client/favorites/index.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/client/favorites/index.blade.php)
  * [`README.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/README.md)
  * [`HANDOVER.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/HANDOVER.md)
  * [`TRACKING_PERUBAHAN_FILE.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/TRACKING_PERUBAHAN_FILE.md)

* **Rincian Perubahan**:
  1. **Transformasi Tata Letak dari Grid 3-Kolom Menjadi Kartu Baris Horizontal (Opsi A)**:
     * Menyelesaikan kendala ruang kosong janggal jika klien hanya menyimpan 1 fotografer pada grid 3-kolom.
     * Menggunakan kontainer kartu horizontal memanjang (`space-y-4 sm:space-y-5`, `flex flex-col md:flex-row items-start md:items-center justify-between gap-6 p-5 sm:p-6 rounded-3xl border border-gray-200/80 dark:border-gray-700/80 shadow-xs hover:shadow-md`).
  2. **Struktur Tiga Komponen Kartu Horizontal Sesuai Spesifikasi Prompter**:
     * **Sisi Kiri**: Cover foto portofolio format landscape kompak (`w-full md:w-60 h-48 md:h-44 rounded-2xl overflow-hidden shrink-0 relative`). Tombol un-save bookmark bulat di sudut kiri atas (`w-8 h-8 rounded-full bg-white/95 dark:bg-gray-900/95`) dengan AJAX fetch, Alpine fade-out, dan auto-reload bersih saat daftar kosong. **Tanpa badge rating di atas foto** sesuai aturan Rule #66 & Rule #93.
     * **Sisi Tengah**: Lokasi kota (ikon pin), baris avatar mitra + tautan nama studio (`font-extrabold text-base sm:text-lg`), kategori spesialisasi tipografi murni amber (*comma-separated*, tanpa badge pill), metrik rating bintang (`★ 5.0`) & sesi selesai inline teks, serta cuplikan ringkas bio studio (max 2 baris `line-clamp-2`).
     * **Sisi Kanan**: Rincian tarif ("Mulai Dari Rp ... / sesi") berdampingan dengan 2 tombol aksi nyata to the point:
       - Tombol Utama: **`"Reservasi Sesi"`** (`bg-amber-400 hover:bg-amber-500 font-extrabold text-gray-950`).
       - Tombol Sekunder: **`"Chat Fotografer"`** (`bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 font-bold text-gray-800 dark:text-gray-100`).
       - Tautan Teks: **`"Lihat Profil Lengkap >"`** dengan single chevron halus.
  3. **Kepatuhan Ketat Anti-AI Slop & Zero Dividers**:
     * **Nol Garis Pembatas (Zero Dividers)**: Dipastikan `0 border-t / border-b / border-l` yang memotong seksi kartu.
     * **Anti-Alay & No Redundant `&` (Rule #70)**: Penamaan tombol to the point tanpa kata berbelit (*"Reservasi Sesi"*, bukan *"Pesan & Reservasi Sekarang"*; *"Chat Fotografer"*, bukan *"Tanya & Chat Fotografer"*).

* **Hasil Pengujian**:
  * Render Test View `client.favorites.index`: **PASS (72,197 bytes)**.
  * Audit Garis Pembatas (`border-t / border-b` internal card): **0 divider (100% Bersih)**.
  * Vite Asset Build: **PASS (1.76s)**.

---

---

### [2026-11-07 10:35 WITA] — Perbaikan Bug Logika Paginasi & Penyelarasan Presets Rows Per Page (Universal Pagination Fix)

* **Modul / Fitur**: Komponen Paginasi Universal (`partials.public.pagination` & `partials.dashboard.pagination`) serta Controller Terkait
* **File yang Diubah**:
  * [`app/Http/Controllers/Client/ClientFavoriteController.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/app/Http/Controllers/Client/ClientFavoriteController.php)
  * [`resources/views/partials/public/pagination.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/partials/public/pagination.blade.php)
  * [`resources/views/partials/dashboard/pagination.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/partials/dashboard/pagination.blade.php)
  * [`TRACKING_PERUBAHAN_FILE.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/TRACKING_PERUBAHAN_FILE.md)

* **Rincian Perubahan**:
  1. **Akar Masalah Bug (*Mismatch Presets*)**:
     * Pada `ClientFavoriteController`, `$perPage` di-hardcode ke angka `9` dengan opsi validasi `[9, 18, 36]`. Namun pada view pagination (`partials.public.pagination`), daftar pilihan dropdown di-hardcode `[10, 20, 50, 100]`. Akibatnya tombol menampilkan angka `9` padahal angka 9 tidak ada di dropdown, dan memilih 10 pun ditolak oleh controller.
  2. **Penyelarasan & Proteksi Sistem Menyeluruh**:
     * Menyelaraskan `$perPage` pada `ClientFavoriteController` mengikuti standar seluruh controller sistem lainnya: default `10` dengan whitelist `[10, 20, 50, 100]`.
     * Menambahkan proteksi logika dinamis pada view `partials.public.pagination` dan `partials.dashboard.pagination`: jika controller mengirim nilai `perPage` kustom di luar `[10, 20, 50, 100]`, nilai tersebut otomatis disisipkan ke dalam opsi dropdown dan diurutkan secara dinamis sehingga tombol dan opsi selalu sinkron 100%.

* **Hasil Pengujian**:
  * Status Kompilasi: **PASS**.
  * Dropdown Rows per Page: **PASS** (Nilai aktif sinkron dengan daftar opsi).

---

---

### [2026-11-07 10:30 WITA] — Standarisasi Kartu Fotografer Favorit Sesuai Katalog & Section 43 README (Anti-AI Slop)

* **Modul / Fitur**: Halaman Daftar Fotografer Favorit Klien (`/client/favorites`)
* **File yang Diubah**:
  * [`resources/views/client/favorites/index.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/client/favorites/index.blade.php)
  * [`README.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/README.md)
  * [`TRACKING_PERUBAHAN_FILE.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/TRACKING_PERUBAHAN_FILE.md)

* **Rincian Perubahan**:
  1. **Keseragaman Penuh Struktur Kartu dengan Katalog (`/fotografer/katalog`)**:
     * *Sebelumnya*: Kartu favorit menggunakan tata letak kustom non-standar: cover `h-52`, tombol bookmark lingkaran merah `text-rose-500`, kategori dipadatkan di dalam baris avatar/nama, harga dan tombol aksi disandingkan secara horizontal dengan tombol kuning kecil `bg-amber-400`.
     * *Sesudahnya*: Mengadopsi 100% **Standar UI Kartu Fotografer (Section 43 README.md)** yang identik dengan kartu katalog fotografer:
       - Frame cover portofolio proporsional `h-56`.
       - Tombol bookmark wishlist standar bulat minimalis (`w-8 h-8 rounded-full bg-white/95 dark:bg-gray-900/95`) di sudut kiri atas dengan icon bookmark amber terisi (dan outline bila belum disimpan).
       - Badge rating `★ {rating} ({count})` di sudut kanan atas.
       - Lokasi studio di atas avatar & nama dengan teks kapital abu-abu redup `mb-2.5`.
       - Avatar + Nama fotografer berdampingan secara lapang (`font-extrabold text-base sm:text-lg`).
       - Kategori spesialisasi sebagai teks tipografi murni amber (`mt-2`) di baris tersendiri di bawah profil.
       - Metrik ulasan & kecepatan respons inline dengan separator dot tipografi (`•`).
       - Bio deskripsi singkat dengan limit 180 karakter (`mt-4 pt-1`).
       - Seksi bawah: Label *MULAI DARI* + Nominal tarif tebal netral, diikuti tombol aksi **Full-Width Black Button** `Lihat Profil >` (`py-3 bg-[#222222] dark:bg-gray-700 text-white hover:bg-amber-400 hover:text-gray-900 rounded-xl text-xs font-extrabold transition duration-200 shadow-xs`) dengan ikon single chevron `>`.
  2. **Interaktivitas Asinkron Terpadu**:
     * Operasi penghapusan favorit tetap berjalan mulus melalui AJAX fetch dengan Alpine.js fade-out dan live universal toast feedback.

* **Hasil Pengujian**:
  * Kompilasi Blade View: **PASS**.
  * Keselarasan Visual dengan `/fotografer/katalog`: **100% MATCH**.

---

---

### [2026-11-07 10:25 WITA] — Eliminasi Badge Pill Header "Disimpan" pada Judul Halaman Favorit (Anti-AI Slop)

* **Modul / Fitur**: Header Halaman Daftar Fotografer Favorit Klien (`/client/favorites`)
* **File yang Diubah**:
  * [`resources/views/client/favorites/index.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/client/favorites/index.blade.php)
  * [`README.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/README.md)
  * [`TRACKING_PERUBAHAN_FILE.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/TRACKING_PERUBAHAN_FILE.md)

* **Rincian Perubahan**:
  1. **Eliminasi Badge Pill Berwarna di Samping Judul Halaman**:
     * *Sebelumnya*: Judul `<h1>` menampilkan badge pill kapsul berwarna kuning amber `[ 1 Disimpan ]` di samping teks `Fotografer Favorit Saya`.
     * *Sesudahnya*: Menghapus badge pill tersebut secara total sehingga judul halaman tampil murni, bersih, dan konsisten dengan seluruh halaman dasbor klien lainnya (seperti *Reservasi Saya*, *Galeri Hasil Foto Saya*, dan *Invoice & Pembayaran Saya*).
  2. **Pembaruan Panduan di `README.md`**:
     * Menegaskan larangan penggunaan badge pill kapsul di header halaman klien.

* **Hasil Pengujian**:
  * Kompilasi Blade View: **PASS**.
  * Visual Tampilan Header: Bersih tanpa badge kapsul.

---

---

### [2026-11-06 22:15 WITA] — Standarisasi Feedback Asinkron Toast Favorit & Eliminasi Banner Statis Permanen (Rule #93 Point 4)

* **Modul / Fitur**: Halaman Daftar Fotografer Favorit Klien (`/client/favorites`) & Galeri Foto (`/client/galleries`)
* **File yang Diubah**:
  * [`resources/views/client/favorites/index.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/client/favorites/index.blade.php)
  * [`resources/views/client/galleries/index.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/client/galleries/index.blade.php)
  * [`README.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/README.md)
  * [`TRACKING_PERUBAHAN_FILE.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/TRACKING_PERUBAHAN_FILE.md)

* **Rincian Perubahan**:
  1. **Eliminasi Banner Alert Statis Permanen**:
     * *Sebelumnya*: Setelah pengguna menghapus fotografer favorit melalui kartu di `/client/favorites`, sistem me-redirect dengan flash session yang menampilkan kotak pesan hijau statis (`✓ Fotografer Budi Event Captures telah dihapus dari daftar favorit.`) menempel permanen di atas konten.
     * *Sesudahnya*: Menghapus blok statis `@if(session('success'))` dan `@if(session('warning'))` dari halaman. Mengandalkan Universal Toast Notification bawaan aplikasi yang otomatis mengambang di pojok kanan atas dengan animasi progress bar menyusut dan otomatis menghilang (*auto-dismiss*).
  2. **Interaktivitas Asinkron Tombol Hapus Favorit (Alpine.js + AJAX Fetch)**:
     * Mengubah form submission sinkron menjadi request AJAX asinkron dengan event dispatch `show-toast` instan.
     * Menambahkan animasi fade-out Alpine.js (`x-transition:leave`) saat kartu dihapus, dan melakukan sinkronisasi auto-reload bersih jika item yang dihapus adalah item terakhir sehingga empty state muncul tanpa ada kotak banner yang tersisa.
  3. **Pembersihan Garis Divider (Zero Divider Rule #67 / Anti-AI Slop)**:
     * Menghapus `border-t border-gray-100 dark:border-gray-700/60` pada seksi bawah tombol kartu fotografer favorit.
  4. **Pembaruan Rule #93 Point 4 di `README.md`**:
     * Menetapkan aturan wajib notifikasi toast mengambang dan larangan permanen kotak alert statis pada aksi wishlist / favorit.

* **Hasil Pengujian**:
  * Kompilasi Blade & View Clear: **PASS**.
  * Vite Asset Build: **PASS**.
  * Eliminasi Banner Statis: **PASS** (Zero permanent flash alerts).

---

---

### [2026-11-06 20:08 WITA] — Standarisasi FAQ Sistem Universal & Kurasi Otomatis Pertanyaan Pencari Jasa (Rule #86)

* **Modul / Fitur**: Tanya Jawab (FAQ) Profil Fotografer (`/fotografer/{id}`)
* **File yang Diubah**:
  * [`app/Http/Controllers/Public/PhotographerController.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/app/Http/Controllers/Public/PhotographerController.php)
  * [`resources/views/public/photographers/show.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/public/photographers/show.blade.php)
  * [`README.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/README.md)

* **Rincian Perubahan**:
  1. **Konsep FAQ Sistem Universal (Bukan Input Mandiri Fotografer)**:
     * *Sebelumnya*: FAQ dibebankan per fotografer melalui relasi database yang sering kosong, sehingga profil menampilkan kartu kosong ("Belum Ada Tanya Jawab Khusus / Fotografer belum menambahkan FAQ").
     * *Sesudahnya*: FAQ dikurasi langsung oleh sistem merangkum seluruh pertanyaan umum yang sering ditanyakan oleh pencari jasa (alur booking, keamanan transfer bank platform, konfirmasi jadwal, konsultasi konsep via chat, reschedule, penyerahan tautan Google Drive, dan penanganan cuaca buruk).
  2. **Konsistensi Universal untuk Seluruh Mitra Fotografer**:
     * Seluruh profil fotografer menyajikan daftar FAQ yang seragam dan identik.
     * Counter badge tab Tanya Jawab kini menampilkan jumlah riil pertanyaan kurasi sistem (`7`).
  3. **Pembaruan Rule #86 di `README.md`**:
     * Mendokumentasikan standarisasi FAQ sistem universal (*System-Curated Universal FAQ*).

* **Hasil Pengujian**:
  * Render view Blade: **PASS** (118 KB output HTML, 0 empty state).
  * Interaktivitas akordeon Alpine.js: **PASS**.

---

---

### [2026-11-06 20:02 WITA] — Standarisasi Keseragaman Kartu Kalender Ketersediaan & Grayed-Out Tanggal Lampau (Rule #67)

* **Modul / Fitur**: Kalender Ketersediaan Fotografer (`/fotografer/{id}`)
* **File yang Diubah**:
  * [`resources/views/public/photographers/show.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/public/photographers/show.blade.php)
  * [`README.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/README.md)

* **Rincian Perubahan**:
  1. **Keseragaman Kontainer Kartu Tanggal Kalender (Uniform Card Matrix)**:
     * *Sebelumnya*: Tanggal lampau dan tanggal bulan sebelum/sesudah kehilangan border (`border-transparent`) dan tampak melayang tanpa kontainer kartu, menciptakan inkonsistensi visual dengan tanggal aktif.
     * *Sesudahnya*: Seluruh sel tanggal (35-42 sel) dibuat seragam 100% menggunakan kontainer kartu (`min-h-[70px] sm:min-h-[85px] p-2.5 rounded-2xl border`).
  2. **Styling Tanggal Lampau (*Grayed-Out*)**:
     * Tanggal yang sudah lewat diabukan secara elegan: latar abu-abu netral (`bg-gray-50 dark:bg-gray-900/50`), border abu-abu bersih (`border-gray-200/80 dark:border-gray-700/60`), dan angka tanggal redup (`text-gray-400 dark:text-gray-500`).
     * Tanggal aktif menggunakan latar putih (`bg-white dark:bg-gray-800`), teks angka kontras (`text-gray-900 dark:text-white`), badge hari ini (`bg-amber-400 text-gray-950`), dan indikator status teks murni (*pure colored text*).
  3. **Pembaruan Rule #67 di `README.md`**:
     * Menegaskan kewajiban keseragaman kontainer kartu seluruh sel tanggal (*Uniform Card Matrix*).

* **Hasil Pengujian**:
  * Render view Blade: **PASS**.
  * Navigasi bulan & seleksi tanggal interaktif: **PASS**.

---

---

### [2026-11-06 19:58 WITA] — Penyesuaian Tata Letak Kartu Portofolio (Foto Utama di KIRI + 3 Thumbnail di KANAN) Sesuai Rule #75

* **Modul / Fitur**: Tab Galeri Portofolio Fotografer (`/fotografer/{id}`)
* **File yang Diubah**:
  * [`resources/views/public/photographers/show.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/public/photographers/show.blade.php)
  * [`README.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/README.md)

* **Rincian Perubahan**:
  1. **Koreksi Tata Letak Kartu Album Portofolio (Rule #75)**:
     * *Sebelumnya*: Foto utama diletakkan di atas melebar penuh (*full width*), dan 3 thumbnail berjejer horizontal di bawahnya.
     * *Sesudahnya*: Tata letak diubah menjadi 2 kolom responsif (`grid grid-cols-1 md:grid-cols-12 gap-3 sm:gap-4`):
       - **Kiri (`md:col-span-8`)**: Foto utama paling besar (*hero photo*) dengan rasio proporsional dan tinggi seimbang (`md:h-[420px] lg:h-[480px]`).
       - **Kanan (`md:col-span-4`)**: 3 thumbnail kecil bertumpuk vertikal (`grid md:grid-rows-3 gap-2.5 sm:gap-3 h-full`), dengan thumbnail ke-3 menampilkan overlay `+X Foto Lainnya` jika album memuat lebih dari 4 foto.
  2. **Sinkronisasi Dokumen Acuan**:
     * Spesifikasi Rule #75 pada `README.md` diperbarui agar selaras dengan standar 2-kolom (Foto Utama di Kiri + 3 Thumbnail di Kanan).

* **Hasil Pengujian**:
  * Render view Blade: **PASS**.
  * Lightbox modal viewer & interaktivitas foto: **PASS**.

---

---

### [2026-11-06 19:50 WITA] — Pembersihan Menyeluruh Larangan Mengistimewakan Kode Sistem / ID & Penghapusan Simbol Pagar (#) Sesuai Rule #89

* **Modul / Fitur**: Global View Template (Profil Fotografer, Invoice, Transaksi, Banner & Booking)
* **File yang Diubah**:
  * [`resources/views/public/photographers/show.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/public/photographers/show.blade.php)
  * [`resources/views/public/booking/invoice-print.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/public/booking/invoice-print.blade.php)
  * [`resources/views/public/booking/invoice.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/public/booking/invoice.blade.php)
  * [`resources/views/client/bookings/index.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/client/bookings/index.blade.php)
  * [`resources/views/client/invoices/index.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/client/invoices/index.blade.php)
  * [`resources/views/partials/shared/upcoming-session-banner.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/partials/shared/upcoming-session-banner.blade.php)
  * [`resources/views/photographer/bookings/index.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/photographer/bookings/index.blade.php)
  * [`resources/views/photographer/dashboard.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/photographer/dashboard.blade.php)
  * [`resources/views/photographer/faqs/index.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/photographer/faqs/index.blade.php)
  * [`resources/views/photographer/reports/index.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/photographer/reports/index.blade.php)
  * [`resources/views/admin/transactions/index.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/admin/transactions/index.blade.php)
  * [`resources/views/public/contests/show.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/public/contests/show.blade.php)
  * [`resources/views/public/elearning/invoice-print.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/public/elearning/invoice-print.blade.php)
  * [`resources/views/public/payment/index.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/public/payment/index.blade.php)

* **Rincian Perubahan**:
  1. **Penghapusan Total Tag Kode Mitra `FTR-00002` pada Header Profil Fotografer**:
     * *Sebelumnya*: Terdapat tag kode mitra `FTR-00002` yang menempel di sudut kanan atas sebaris dengan tombol kembali katalog. Menempatkan kode acak/ID internal sebagai elemen header publik adalah pola *AI Slop* yang salah memahami hierarki informasi.
     * *Sesudahnya*: Tag ID `FTR-00002` dihapus total dari tampilan. Area atas kini hanya menyisakan navigasi tautan kembali yang bersih dan natural.
  2. **Audit & Eliminasi Menyeluruh Simbol Pagar `#` pada Kode & Nomor Urut Sistem (Kepatuhan Rule #89 Point 1 & Mandat User)**:
     * Menghapus awalan `#` pada seluruh kode kontrak (`#KTRK-00001` &rarr; `KTRK-00001`), invoice pembayaran (`#INV-...` &rarr; `INV-...`), registrasi lomba (`#REG-LMB-...` &rarr; `REG-LMB-...`), dan urutan FAQ (`#1` &rarr; `1`).
  3. **Penghapusan Badge Stempel AI Slop (`VERIFIED INVOICE ID`) pada Dokumen Faktur**:
     * *Sebelumnya*: Dokumen cetak invoice menyertakan kotak stempel verifikasi palsu dengan hash MD5 acak.
     * *Sesudahnya*: Dihapus dan diganti dengan catatan formal digital resmi LensMatch Indonesia yang bersih dan profesional.

* **Hasil Pengujian**:
  * Pencarian `FTR-` di seluruh views: **0 Temuan (100% GONE)**.
  * Pencarian awalan `#[A-Z]+` pada kode ID: **0 Temuan (100% Bersih)**.
  * Render view Blade: **PASS**.

---

---

### [2026-11-06 19:40 WITA] — Pembersihan Total Elemen Visual AI-Slop Halaman Profil Fotografer (Background Wavy SVG, Breadcrumb, Spasi Kartu Profil, & Struktur Kontainer Tab)

* **Modul / Fitur**: Profil Publik Fotografer (`/fotografer/{id}`)
* **File yang Diubah**:
  * [`resources/views/public/photographers/show.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/public/photographers/show.blade.php)

* **Rincian Perubahan (Menuntaskan Temuan Screenshot Feedback User)**:
  1. **Eliminasi SVG Wave Bergelombang & Latar Belakang Bersih (Pasted Image 1)**:
     * *Sebelumnya*: Menggunakan ornamen SVG bergelombang warna-warni (`wave-grad-1`, `wave-grad-2`) di bagian header atas yang terkesan murahan (*AI-slop*).
     * *Sesudahnya*: Seluruh ornamen SVG gradient wave dihapus total. Latar belakang disatukan secara elegan menggunakan palette netral konsisten `bg-gray-50/70 dark:bg-gray-900 min-h-screen py-8 lg:py-10`.
  2. **Penghapusan Breadcrumb & Header Redundan (Pasted Image 1)**:
     * *Sebelumnya*: Terdapat navigasi breadcrumb bertingkat (`Katalog Fotografer > ...`) dan blok judul redundan `<h1>Detail Profil Fotografer</h1>` bersubtitle panjang yang memakan ruang vertikal.
     * *Sesudahnya*: Breadcrumb dan judul generik dihapus total. Tombol navigasi kembali menggunakan single chevron modern `<` (`d="M15.75 19.5L8.25 12l7.5-7.5"`) tanpa stem arrow panah panjang `←` (mematuhi Rule #72).
  3. **Perbaikan Spasi & Margin Kartu Profil (Pasted Image 2)**:
     * *Sebelumnya*: Kolom informasi kanan menerapkan `flex-1 flex flex-col justify-between`. Hal ini memaksa bio terlempar ke paling atas dan lokasi/harga ke paling bawah mengikuti tinggi foto potret kiri, menyisakan rongga kosong putih raksasa yang tidak wajar di tengah kartu profil.
     * *Sesudahnya*: Diterapkan aliran spasi vertikal alami (`space-y-4` dan `pt-2`). Nama, kategori, bio, lokasi, dan tarif tersusun rapat dan proporsional.
  4. **Eliminasi Garis Pembatas Alay (Pasted Image 2)**:
     * *Sebelumnya*: Terdapat garis putus-putus (*dashed line border*) `border-dashed` di bawah bio yang norak.
     * *Sesudahnya*: Garis putus-putus dihapus total; pemisahan seksi menggunakan grid 2-kolom bersih dengan label hierarki tipografi modern.
  5. **Perbaikan Tag HTML Penutup & Margin Kontainer Melebar Edge-to-Edge (Pasted Image 3)**:
     * *Sebelumnya*: Terdapat tag penutup ganda `</div>` yang menutup kontainer pembungkus utama `<div class="max-w-7xl mx-auto px-4 ...">` terlalu awal di tengah halaman. Akibatnya seluruh konten di bawahnya (tab panel dan kartu jam kerja) melebar 100% tanpa margin kiri-kanan (*edge-to-edge*).
     * *Sesudahnya*: Struktur penutupan tag HTML distandarisasi ulang secara presisi. Seluruh kartu profil, tab navigasi, paket layanan, kalender, ulasan, hingga FAQ kini berada 100% aman di dalam kontainer `max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8`.
  6. **Penataan Kartu Standar Jam Kerja Operasional Studio (Pasted Image 3)**:
     * *Sebelumnya*: Kartu *"Standar Jam Kerja Operasional Studio"* bocor dan muncul di bawah kartu *"Pilihan Paket Layanan Sesi Foto"* karena penutupan tag kontainer tab kalender yang salah tempat.
     * *Sesudahnya*: Kartu jam kerja operasional studio dikurung secara tegas di dalam seksi tab Kalender Ketersediaan (`x-show="activeTab === 'availability'"`), sehingga tidak akan muncul di tab Paket Layanan, Portofolio, Ulasan, maupun FAQ.

* **Hasil Pengujian**:
  * Kompilasi & Render Blade: **PASS** (102 KB output HTML bersih tanpa error).
  * Verifikasi ketiadaan elemen `wave-grad`, `breadcrumb`, dan `border-dashed`: **PASS (100% bersih)**.
  * Margin kontainer kiri-kanan responsif (`max-w-7xl mx-auto px-4 sm:px-6 lg:px-8`): **PASS**.

---

---

### [2026-11-06 19:35 WITA] — Pembuatan Dokumen Handover Resmi untuk AI / Pengembang Lain

* **Modul / Fitur**: Dokumentasi & Handover Proyek
* **File yang Dibuat**:
  * [`HANDOVER.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/HANDOVER.md)

* **Rincian Perubahan**:
  1. Merangkum seluruh arsitektur proyek, tech stack, 3 role platform, dan struktur UI (2 dashboard + 1 publik universal).
  2. Mendokumentasikan status Git resmi (branch `development`), aturan push/pull multi-komputer, dan larangan mutlak push otomatis.
  3. Mencantumkan progres terakhir yang selesai: konsolidasi 49 migrasi murni database, perbaikan bug scope tab profil fotografer, serta fitur favorit fotografer dengan toast live feedback.
  4. Menetapkan aturan baku dan pantangan mutlak AI (larangan `#`, larangan dummy data, larangan *AI slop*, standar tombol formulir & aksi tabel).
  5. Menjelaskan status modul saat ini (modul booking HOLD) dan rencana fokus pengembangan selanjutnya.

* **Hasil Pengujian**:
  * Dokumen valid dan tersimpan di root project: **PASS**.

---

---

### [2026-11-06 20:15 WITA] — Standarisasi Proporsi Card Foto Profil & Reorganisasi Statistik Studio (Rule #66)

* **Modul / Fitur**: Halaman Detail Profil Fotografer (`/fotografer/{id}`)
* **File yang Diubah**:
  * [`resources/views/public/photographers/show.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/public/photographers/show.blade.php)
  * [`README.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/README.md)
  * [`HANDOVER.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/HANDOVER.md)

* **Rincian Perubahan**:
  1. **Standarisasi Lebar Card Foto Diperlebar & Fixed (`w-[320px] h-[340px]`)**:
     * Mengganti dimensi fleksibel foto `w-56 md:w-64 aspect-[3/4]` menjadi ukuran pasti dan proporsional: `w-full sm:w-[320px] h-[320px] sm:h-[340px] shrink-0 rounded-2xl overflow-hidden`.
     * Menghilangkan kesan kurus/timpang dan menyeimbangkan komposisi visual terhadap kartu informasi di sebelah kanan.
  2. **Reorganisasi 4 Statistik Studio ke Dalam 2 Seksi Horizontal Sejajar**:
     * Memindahkan 4 metrik dari baris bawah terpisah langsung ke dalam kolom kanan informasi tepat di bawah grid Lokasi & Tarif:
       * **Seksi 1 (Kiri - Di Bawah Lokasi & Basis Studio)**: *Pengalaman Studio* (`1+ Tahun`) dan *Portofolio* (`X Karya`).
       * **Seksi 2 (Kanan - Di Bawah Tarif Layanan Sesi Foto)**: *Bergabung Sejak* (`d M Y`) dan *Rating & Ulasan* (`★ 5.0 (N)`).
  3. **Penataan Tombol Aksi di Bawah Card Profil**:
     * Menghapus baris statistik terpisah di bawah foto, menyisakan deretan tombol aksi dengan pemisah halus (*border-t border-gray-100 dark:border-gray-700/80*).
     * Tombol aksi menggunakan label natural: `Reservasi Sesi Foto Sekarang`, `Tanya & Chat Fotografer`, dan `Simpan ke Favorit`.

* **Hasil Pengujian**:
  * Blade Compilation & Syntax Check: **PASS (100% clean, 0 error)**.
  * Tampilan responsif & proporsi seimbang: **PASS**.

---

---

### [2026-11-06 20:25 WITA] — Pemurnian Tab Paket Layanan Menjadi Murni Informasional (Eliminasi Tombol Booking di Kartu Paket)

* **Modul / Fitur**: Halaman Detail Profil Fotografer (`/fotografer/{id}`) — Tab Paket Layanan
* **File yang Diubah**:
  * [`resources/views/public/photographers/show.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/public/photographers/show.blade.php)
  * [`database/seeders/PhotographerSeeder.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/database/seeders/PhotographerSeeder.php)
  * [`README.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/README.md)
  * [`HANDOVER.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/HANDOVER.md)

* **Rincian Perubahan**:
  1. **Eliminasi Tombol Booking di Kartu Paket**:
     * Menghapus seluruh elemen tombol `[Pilih Paket & Booking]` dari kartu paket layanan di dalam Tab 1 (`resources/views/public/photographers/show.blade.php`).
     * Tab Paket Layanan kini murni berfungsi sebagai katalog informasi spesifikasi paket dan rincian fasilitas & benefit yang disediakan oleh fotografer.
     * Pemilihan paket dilakukan nanti pada tahapan formulir reservasi setelah klien menekan tombol utama *"Reservasi Sesi Foto Sekarang"* di kartu profil atas.
  2. **Penambahan Rincian Benefit Riil pada Seluruh Fotografer Seed**:
     * Melengkapi seeder seluruh paket fotografer (`PhotographerSeeder.php`) dengan entri `ServiceDetail` autentik (durasi liputan, spesifikasi gear/kamera dual-body, resolusi & kuota foto retouch, media penyimpanan cloud, dan hak cipta komersial).
  3. **Dokumentasi Aturan Resmi (Rule #66 Point 7)**:
     * Menyinkronkan aturan bahwa kartu paket profil fotografer murni informasional ke dalam `README.md` dan `HANDOVER.md`.

* **Hasil Pengujian**:
  * Render HTML Paket Layanan & Rincian Benefit: **PASS (100% informatif, 0 tombol booking di kartu)**.
  * Blade Compilation & Syntax Check: **PASS**.

---

---

### [2026-11-06 20:30 WITA] — Perbaikan Tata Letak Galeri Portofolio: 3 Thumbnail Vertikal Sejajar & Penambahan Padding Card (Rule #75)

* **Modul / Fitur**: Halaman Detail Profil Fotografer (`/fotografer/{id}`) — Tab Galeri Portofolio
* **File yang Diubah**:
  * [`resources/views/public/photographers/show.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/public/photographers/show.blade.php)
  * [`README.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/README.md)
  * [`HANDOVER.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/HANDOVER.md)

* **Rincian Perubahan**:
  1. **Tata Letak 3 Thumbnail Vertikal ke Bawah di Kanan (`md:flex-col`)**:
     * Memperbaiki tata letak 3 thumbnail kecil di sisi kanan (`md:col-span-4`) yang sebelumnya berjajar secara horizontal di atas menjadi **tersusun bertingkat vertikal ke bawah (`flex-row md:flex-col`)** dengan tinggi container pasti `h-28 sm:h-36 md:h-[480px] lg:h-[520px]`.
     * Menggunakan `flex-1 min-w-0 min-h-0` pada setiap thumbnail sehingga membagi tinggi secara presisi dan sejajar penuh dari tepi atas hingga tepi bawah foto utama di kiri.
  2. **Penambahan Padding dan Spacing Card Portofolio**:
     * Memperlebar padding kontainer kartu dari `p-4 sm:p-6` menjadi **`p-6 sm:p-8 lg:p-9 rounded-3xl space-y-6`** untuk menghilangkan kesan sempit dan memberikan ruang visual yang nyaman.
     * Mengatur pembatas footer meta info dengan `pt-5 border-t border-gray-100 dark:border-gray-700/80`, tipografi judul tebal `text-lg sm:text-xl font-black`, serta badge jumlah foto terstruktur.

* **Hasil Pengujian**:
  * Render HTML Album Portofolio: **PASS (Thumbnail tersusun vertikal di desktop, padding card lapang & proporsional)**.
  * Blade Compilation & Syntax Check: **PASS**.

---

---

### [2026-11-06 20:38 WITA] — Perbaikan Kritis: Locking Dimensi Foto Profil (320px x 340px) & Penggantian Icon FAQ

* **Modul / Fitur**: Halaman Detail Profil Fotografer (`/fotografer/{id}`)
* **File yang Diubah**:
  * [`resources/views/public/photographers/show.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/public/photographers/show.blade.php)
  * `public/build/assets/*` (Vite production build)
  * [`TRACKING_PERUBAHAN_FILE.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/TRACKING_PERUBAHAN_FILE.md)

* **Rincian Perubahan**:
  1. **Locking Mutlak Dimensi Foto Profil (`320px x 340px`)**:
     * Menambahkan locking dimensi fisik `style="width: 320px; min-width: 320px; max-width: 320px; height: 340px;"` serta class standar `sm:w-80 shrink-0` pada kontainer foto profil fotografer.
     * Mengatasi insiden di mana class arbitrer uncompiled sempat menyebabkan browser menerapkan `w-full` yang membesarkan foto wajah dan menghimpit kolom informasi kanan.
     * Menambahkan `min-w-0` pada kolom informasi kanan (`flex-1 min-w-0 space-y-4 w-full`) sehingga fleksibel mengisi seluruh ruang horizontal desktop.
  2. **Penggantian Badge Bulat Huruf "Q" dan "A" pada Akordeon FAQ**:
     * Menghapus badge bulat kaku bertuliskan huruf "Q" dan "A".
     * Mengganti penanda pertanyaan dengan icon resmi **QuestionMarkCircle SVG** tipis berwarna amber yang modern dan profesional.
     * Jawaban FAQ kini tampil murni dengan indentasi rapi (`pl-8`) tanpa badge huruf kaku.
  3. **Rebuild Aset Vite & View Cache**:
     * Menjalankan `npm run build` untuk menghasilkan asset bundle CSS/JS terbaru.
     * Menjalankan `php artisan view:clear`.

* **Hasil Pengujian**:
  * Render Kartu Profil: **PASS (Foto terkunci 320px x 340px, kolom kanan terbentang proporsional)**.
  * Render Akordeon FAQ: **PASS (Bebas badge bulat "Q"/"A", diganti icon QuestionMarkCircle)**.

---

---

### [2026-11-06 20:45 WITA] — Pembersihan Total Seluruh Garis Divider Horizontal (Anti-AI Slop Enforcement)

* **Modul / Fitur**: Halaman Detail Profil Fotografer (`/fotografer/{id}`)
* **File yang Diubah**:
  * [`resources/views/public/photographers/show.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/public/photographers/show.blade.php)
  * [`README.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/README.md)
  * [`HANDOVER.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/HANDOVER.md)

* **Rincian Perubahan**:
  1. **Pembersihan Total Divider Garis Horizontal pada Seluruh Halaman**:
     * Menghapus seluruh garis pembatas `border-t` / `border-b` / divider internal yang memotong komponen secara kaku:
       * **Kartu Profil Utama**: Menghapus garis di atas 4 statistik studio (`border-t`) dan garis di atas baris tombol aksi (`border-t`). Seluruh elemen kini menyatu mengalir alami murni mengandalkan whitespace natural.
       * **Kartu Paket Layanan (Tab 1)**: Menghapus 2 garis `border-t` pembatas harga dan rincian benefit di dalam setiap kartu paket.
       * **Kartu Portofolio (Tab 2)**: Menghapus garis `border-t` di atas footer judul/deskripsi album.
       * **Kalender Ketersediaan (Tab 3)**: Menghapus garis `border-b` pada header bulan dan `border-t` pada legend status.
       * **Tanya Jawab (Tab 5)**: Menghapus garis `border-t` pemotong teks jawaban akordeon.
  2. **Hasil Visual Anti-AI Slop**:
     * Seluruh kartu antarmuka kini tampil bersih, elegan, dan menyatu mulus tanpa garis-garis tipis kaku pemotong layar khas AI template.

* **Hasil Pengujian**:
  * Audit grep `border-[tb]`: **0 garis divider tersisa (bersih 100%)**.
  * Blade Compilation & Syntax Check: **PASS**.

---

---

### [2026-11-06 20:55 WITA] — Penerapan Pendekatan 1 Anti-AI Slop: Teks Metadata Foto Sejajar Judul Album

* **Modul / Fitur**: Halaman Detail Profil Fotografer (`/fotografer/{id}`) — Tab Galeri Portofolio
* **File yang Diubah**:
  * [`resources/views/public/photographers/show.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/public/photographers/show.blade.php)
  * [`README.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/README.md)
  * [`HANDOVER.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/HANDOVER.md)

* **Rincian Perubahan**:
  1. **Eliminasi Floating Pill Badge `[ 🖼️ 4 Foto ]`**:
     * Menghapus pill badge berkontur (`bg-gray-50 border shadow-2xs`) yang sebelumnya mengapung terisolasi di sudut kanan bawah kartu album portofolio.
  2. **Integrasi Teks Metadata Sejajar Judul (Pendekatan 1)**:
     * Menyusun counter total foto langsung berdampingan secara alami dengan judul album (`Judul Album · X Foto`) menggunakan tipografi sekunder yang tenang (`text-xs sm:text-sm font-semibold text-gray-400 dark:text-gray-500`).
     * Deskripsi album mengalir rapi di bawah judul dengan lebar maksimum yang nyaman dibaca (`max-w-4xl`).

* **Hasil Pengujian**:
  * Render HTML Album Portofolio: **PASS (Bebas badge pill mengapung, metadata menyatu elegan dengan judul)**.
  * Blade Compilation & Syntax Check: **PASS**.

---

---

### [2026-11-06 21:00 WITA] — Standarisasi Kalender "Hari Ini": Penghapusan Header Badge & Penempatan Teks di Samping Nomor Tanggal (Rule #67 Point 5)

* **Modul / Fitur**: Halaman Detail Profil Fotografer (`/fotografer/{id}`) — Tab Kalender Ketersediaan
* **File yang Diubah**:
  * [`resources/views/public/photographers/show.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/public/photographers/show.blade.php)
  * [`README.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/README.md)
  * [`HANDOVER.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/HANDOVER.md)

* **Rincian Perubahan**:
  1. **Eliminasi Tombol/Badge Header `[ Hari Ini ]`**:
     * Menghapus elemen tombol/pill badge `[ Hari Ini ]` yang sebelumnya melayang di samping nama bulan (`November 2026 [Hari Ini]`). Header navigasi bulan kini tampil bersih dan profesional hanya menampilkan judul bulan/tahun dan tombol panah bulan sebelumnya/sesudahnya.
  2. **Penghapusan Bulatan Kuning / Dot Badge**:
     * Mengeliminasi bulatan kuning dan styling dot badge pada angka tanggal sesuai kaidah Anti-AI Slop Rule #67.
  3. **Penempatan Teks "(Hari Ini)" Langsung di Samping Angka Tanggal**:
     * Menambahkan label teks bersih `(Hari Ini)` berdampingan langsung dengan nomor tanggal:
       `<span class="text-[10px] sm:text-[11px] font-extrabold text-amber-600 dark:text-amber-400">(Hari Ini)</span>`
     * Memberikan aksen border kartu amber halus (`border-amber-400 dark:border-amber-500`) pada sel kartu tanggal hari ini agar mudah dikenali tanpa clutter visual.

* **Hasil Pengujian**:
  * Render Kalender Ketersediaan: **PASS (Bebas tombol badge di header, teks `(Hari Ini)` berdampingan presisi dengan nomor tanggal)**.
  * Blade Compilation & Syntax Check: **PASS**.

---

---

### [2026-11-06 21:12 WITA] — Standarisasi Kalender Ketersediaan Murni Informasional Preview (Rule #67 Point 6)

* **Modul / Fitur**: Halaman Detail Profil Fotografer (`/fotografer/{id}`) — Tab Kalender Ketersediaan
* **File yang Diubah**:
  * [`resources/views/public/photographers/show.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/public/photographers/show.blade.php)
  * [`database/seeders/BookingContractSeeder.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/database/seeders/BookingContractSeeder.php)
  * [`README.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/README.md)
  * [`HANDOVER.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/HANDOVER.md)

* **Rincian Perubahan**:
  1. **Eliminasi Total Tombol Booking di Seluruh Tab Kalender Ketersediaan**:
     * Menghapus tombol *"Booking Langsung"* (`<a href="/booking/create...">`) pada header section kalender.
     * Menghapus tombol *"Pilih Tanggal Ini & Booking"* (`<a :href="...">`) pada drawer rincian tanggal terpilih.
     * Tab Kalender Ketersediaan kini berfungsi 100% sebagai **informational preview murni**: pengguna melihat status hari (*Tersedia*, *Terisi Sesi*, *Libur*) dan mengklik tanggal untuk melihat ringkasan sesi/libur tanpa ada tombol booking aksi.
  2. **Penegasan Alur Reservasi Resmi**:
     * Pemilihan tanggal, paket, jam, dan lokasi pemotretan hanya terjadi di halaman formulir reservasi resmi (`/booking/create?photographer_id=...`) setelah klien menekan tombol utama *"Reservasi Sesi Foto Sekarang"* pada header profil.
  3. **Penyediaan Data Riil Kalender (Anti-AI Slop Blank Slate)**:
     * Menambahkan data dummy/seeder jadwal sesi aktif dan hari libur pada bulan berjalan (November 2026) untuk Budi Event Captures dan mitra fotografer lainnya sehingga kalender tidak kosong melompong dan merefleksikan tanggal yang terisi vs yang kosong secara riil.

* **Hasil Pengujian**:
  * Audit Tombol: **0 tombol booking di dalam Tab Kalender (100% Bersih)**.
  * Render Test Budi Event Captures (`show(2)`): **PASS (115,977 bytes)**.
  * Data Jadwal & Libur: **PASS (Terisi Sesi 10 & 18 Nov, Libur 14 & 22 Nov ter-render presisi)**.

---

---

### [2026-11-06 21:18 WITA] — Penataan Ulang Statistik Studio: 3 Baris Vertikal Berpasangan 2-Kolom (Rule #66)

* **Modul / Fitur**: Halaman Detail Profil Fotografer (`/fotografer/{id}`) — Header Card Profil Fotografer
* **File yang Diubah**:
  * [`resources/views/public/photographers/show.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/public/photographers/show.blade.php)
  * [`README.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/README.md)
  * [`HANDOVER.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/HANDOVER.md)

* **Rincian Perubahan**:
  1. **Struktur Grid 3 Baris Vertikal x 2 Kolom**:
     * Mengatur ulang 6 item informasi profil ke dalam susunan 3 seksi berpasangan yang mengalir ke bawah (`kebawah`):
       * **Row 1**: *Lokasi & Basis Studio* (Kiri) | *Tarif Layanan Sesi Foto* (Kanan)
       * **Row 2**: *Pengalaman Studio* (Kiri) | *Portofolio Karya* (Kanan) — sejajar vertikal tepat di bawah Row 1
       * **Row 3**: *Bergabung Sejak* (Kiri) | *Rating & Ulasan* (Kanan) — sejajar vertikal tepat di bawah Row 2
  2. **Keseimbangan Visual dengan Foto Profil**:
     * Kolom kanan kini mengisi ruang secara proporsional dan seimbang penuh dengan tinggi foto profil potret (340px) di sebelah kiri, menghilangkan ruang kosong tanpa harus memadatkan 4 statistik dalam satu baris horizontal sempit.

* **Hasil Pengujian**:
  * Render Test Budi Event Captures (`show(2)`): **PASS (115,885 bytes)**.
  * Blade Compilation & Syntax Check: **PASS**.

---

---

### [2026-11-06 22:00 WITA] — Implementasi Opsi A: Relokasi Tombol Aksi ke Kolom Kanan & Teks Simpel Anti-Alay (Rule #66 Point 4)

* **Modul / Fitur**: Halaman Detail Profil Fotografer (`/fotografer/{id}`) — Header Card Profil Fotografer
* **File yang Diubah**:
  * [`resources/views/public/photographers/show.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/public/photographers/show.blade.php)
  * [`README.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/README.md)
  * [`HANDOVER.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/HANDOVER.md)

* **Rincian Perubahan**:
  1. **Relokasi Tombol Aksi ke Dalam Kolom Kanan (Opsi A)**:
     * Memindahkan baris tombol aksi ke dalam kolom kanan informasi, diposisikan tepat di bawah Baris 3 (*Bergabung Sejak & Rating/Ulasan*).
     * Foto potret di sebelah kiri (320px x 340px) kini berdiri bersih dan anggun tanpa ada tombol yang mengganjal di bawahnya.
  2. **Penyederhanaan Teks Tombol (Anti-Alay & Bersih)**:
     * Tombol Utama: Diubah dari *"Reservasi Sesi Foto Sekarang"* menjadi **`"Reservasi Sesi"`** (`bg-amber-400 font-extrabold`).
     * Tombol Sekunder: Diubah dari *"Tanya & Chat Fotografer"* menjadi **`"Chat Fotografer"`** (`bg-gray-100 font-bold`).
     * Tombol Favorit: Ditransformasi menjadi **Icon Button Bookmark** minimalis (`w-10 h-10 sm:w-11 sm:h-11`) dengan responsif live toast notification.
  3. **Penegakan Larangan Divider**:
     * Dipastikan **0 garis divider horizontal** (`border-t`, `border-b`) di antara data dan tombol. Seluruh susunan menyatu alami murni menggunakan whitespace (`pt-2 gap-2.5 sm:gap-3`).

* **Hasil Pengujian**:
  * Audit Divider: **0 garis divider tersisa (100% Bersih)**.
  * Render Test Budi Event Captures (`show(2)`): **PASS (116,124 bytes)**.
  * Vite Asset Build: **PASS**.

---

---

### [2026-11-06 22:05 WITA] — Penyesuaian Tinggi Card Foto Profil: Diperpanjang Sejajar Tombol Aksi (Rule #66)

* **Modul / Fitur**: Halaman Detail Profil Fotografer (`/fotografer/{id}`) — Header Card Profil Fotografer
* **File yang Diubah**:
  * [`resources/views/public/photographers/show.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/public/photographers/show.blade.php)
  * [`README.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/README.md)
  * [`HANDOVER.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/HANDOVER.md)

* **Rincian Perubahan**:
  1. **Tinggi Card Foto Membujur ke Bawah (Flush Alignment)**:
     * Menghapus batasan tinggi kaku `height: 340px` pada inline style card foto.
     * Menerapkan `sm:items-stretch` pada flex kontainer induk dan `sm:self-stretch min-h-[340px] relative` pada kartu foto potret, serta `sm:absolute sm:inset-0 w-full h-full object-cover` pada elemen `<img>`.
     * Menambahkan `mt-auto` pada baris tombol aksi kolom kanan.
     * Hasilnya: Tepi bawah card foto potret di sebelah kiri kini membujur ke bawah dan **sejajar presisi secara horizontal dengan tepi bawah tombol aksi** di sebelah kanan.

* **Hasil Pengujian**:
  * Render Test Budi Event Captures (`show(2)`): **PASS (116,326 bytes)**.
  * Blade Compilation & Syntax Check: **PASS**.
  * Vite Asset Build: **PASS**.














---

---

### [2026-11-07 14:48 WITA] — Standarisasi Master Card Profil Fotografer & Harmonitas Warna Kartu Dalam (Rule #97)

* **Modul / Fitur**: Halaman Detail Profil Fotografer (`/fotografer/{id}`) — Header Profil, Tab Navigation & Seluruh Kartu Konten Tab
* **File yang Diubah**:
  * [`resources/views/public/photographers/show.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/public/photographers/show.blade.php)
  * [`README.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/README.md)
  * [`HANDOVER.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/HANDOVER.md)

* **Rincian Perubahan**:
  1. **Margin & Breathing Room yang Lega**:
     * Padding master card diperbesar dari `p-6 sm:p-8 lg:p-10` menjadi `p-8 sm:p-10 lg:p-12`.
     * Spasi vertikal antar seksi diperbesar (`space-y-10 sm:space-y-12`, `gap-8 sm:gap-10 lg:gap-12`).
     * Jeda antar kartu dan padding dalam kartu diperlebar (`p-7 sm:p-9`) sehingga layout terasa lapang, berwibawa, dan tidak berdesakan.
  2. **Harmonitas Warna Kartu Dalam (Anti-Gray Tint)**:
     * Menghapus seluruh warna abu-abu latar belakang (`bg-gray-50/70`, `bg-gray-750/70`) pada kartu anak di dalam tab.
     * Mengganti warna kartu anak menjadi persis sama dengan kartu induk luar: **`bg-white dark:bg-gray-800`** dengan garis tepi halus `border border-gray-200 dark:border-gray-700`.
  3. **Pemberantasan Shadow Internal (Zero Shadow)**:
     * Menghapus seluruh shadow (`shadow-xs`, `shadow-sm`, `shadow-md`, `shadow-2xs`) pada kartu paket layanan, album galeri portofolio, kontainer kalender ketersediaan, kartu ulasan klien, dan accordion FAQ, menggantikannya dengan `shadow-none`.
  4. **Peningkatan Skala Tipografi Proporsional**:
     * Nama Fotografer: `text-3xl sm:text-4xl font-black`.
     * Spesialisasi: `text-sm sm:text-base font-bold text-amber-600 dark:text-amber-400`.
     * Bio Studio: `text-sm sm:text-base text-gray-600 dark:text-gray-300 leading-relaxed max-w-3xl`.
     * 6 Metrik Studio (Lokasi, Tarif, Pengalaman, Portofolio, Bergabung, Rating Ulasan): Nilai diperbesar menjadi `text-base sm:text-lg lg:text-xl font-black`, label `text-xs sm:text-sm font-semibold`.
     * Tombol Aksi: Reservasi (`px-8 py-3.5 text-sm sm:text-base font-extrabold`), Chat (`px-7 py-3.5 text-sm sm:text-base font-bold`), Bookmark Favorit (`w-12 h-12`).
     * Judul Tab `<h2>`: `text-2xl sm:text-3xl font-black`.
     * Kartu Paket: Nama paket `text-xl sm:text-2xl font-black`, tarif `text-2xl sm:text-3xl font-black`, benefit `text-sm leading-relaxed`.
     * Galeri: Judul album `text-xl sm:text-2xl font-black`.
     * Kalender & Jam Operasional: Navigasi dan teks deskripsi diperbesar ke `text-sm sm:text-base`.
     * Ulasan: Skor rata-rata `text-6xl font-black`, nama klien `text-base font-extrabold`, ulasan `text-sm sm:text-base`.
     * FAQ: Pertanyaan `text-base sm:text-lg font-extrabold`, jawaban `text-sm sm:text-base leading-relaxed`.
  5. **Kepatuhan Anti-AI Slop**:
     * Menghilangkan simbol `&` yang redundan (misal "Rating Ulasan", "Fasilitas Layanan").
     * Menjaga 0 garis pemisah horizontal (`0 border-t / border-b`).

* **Hasil Pengujian**:
  * Div Tag Balance: **94 open / 94 close (100% Match)**.
  * Render Test: **PASS (105,388 bytes)**.
  * Vite Asset Build: **PASS (2.43s)**.


---

---

### [2026-11-07 16:33 WITA] — Penambahan Seeder Galeri Portofolio (3 Album Penuh Budi Event Captures)

* **Modul / Fitur**: Database Seeder & Tab Galeri Portofolio Profil Fotografer (`/fotografer/{id}`)
* **File yang Diubah**:
  * [`database/seeders/PhotographerSeeder.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/database/seeders/PhotographerSeeder.php)
  * [`TRACKING_PERUBAHAN_FILE.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/TRACKING_PERUBAHAN_FILE.md)
  * [`HANDOVER.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/HANDOVER.md)

* **Rincian Perubahan**:
  1. **Seeder Album 2 (Nusantara Tech Summit dan Gala Dinner 2025)**:
     * Menambahkan album galeri portofolio kedua untuk Budi Event Captures (`id_photographer = 2`) dengan deskripsi dokumentasi konferensi teknologi dan malam apresiasi di JCC.
     * Dilengkapi 4 foto resolusi tinggi (1 foto panggung utama + 3 thumbnail vertikal).
  2. **Seeder Album 3 (Soundwave Indie Showcase Senayan)**:
     * Menambahkan album galeri portofolio ketiga dengan deskripsi sorotan konser musik indie di Senayan Park.
     * Dilengkapi 5 foto (1 foto utama + 3 thumbnail vertikal + 1 foto ekstra sehingga memicu badge `+1 Foto Lainnya`).
  3. **Kepatuhan Anti-AI Slop**:
     * Judul album lugas dan bebas dari simbol `&` redundan (Rule #70).
     * Layout album mengikuti standar Rule #75 (Foto utama Kiri + 3 Thumbnail Vertikal Kanan).

* **Hasil Pengujian**:
  * Seeder Execution (`php artisan db:seed --class=PhotographerSeeder`): **PASS**.
  * Total Portofolio Budi Event Captures: **3 Album (100% Sesuai Permintaan)**.
  * Render Test: **PASS (128,038 bytes, metrik menampilkan '3 Karya')**.


---

---

### [2026-11-07 16:56 WITA] — Standarisasi Watermark Hak Cipta Murni Tipografi (Rule #98 Anti-AI Slop)

* **Modul / Fitur**: Proteksi Hak Cipta Karya Fotografer — `WatermarkService` & Tab Galeri Portofolio Profil (`/fotografer/{id}`)
* **File yang Diubah**:
  * [`app/Services/WatermarkService.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/app/Services/WatermarkService.php)
  * [`resources/views/public/photographers/show.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/public/photographers/show.blade.php)
  * [`README.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/README.md)
  * [`HANDOVER.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/HANDOVER.md)

* **Rincian Perubahan**:
  1. **Eliminasi Total Kotak Badge / Box Kapsul Watermark (Anti-AI Slop)**:
     * Menghapus perintah menggambar kotak persegi panjang `imagefilledrectangle` dan border `imagerectangle` pada `WatermarkService.php`.
     * Menggantikannya dengan **Tipografi Murni Bersahaja (*Pure Typography*)**: teks semi-transparan putih dengan bayangan halus (1px shadow offset) langsung pada piksel gambar.
  2. **Watermark Overlay Murni Tipografi di Tampilan Publik (Blade & Lightbox Modal)**:
     * Menambahkan lapisan watermark teks murni pada setiap foto portofolio di `show.blade.php` dan modal Fullscreen Lightbox: `© LensMatch • {{ $photographer->nama }}`.
     * Menggunakan styling tipografi murni tanpa badge/box: `select-none pointer-events-none text-xs sm:text-sm font-semibold tracking-wider text-white/75 drop-shadow-[0_1px_2px_rgba(0,0,0,0.85)] z-10`.
     * Menambahkan proteksi anti-unduh sembarangan: `draggable="false"` dan `oncontextmenu="return false;"` pada elemen gambar.
  3. **Kepatuhan Anti-AI Slop**:
     * Bebas dari simbol `&` redundan (menggunakan bullet pemisah `•`).
     * Nol badge/pill melayang di atas foto (mematuhi Rule #66 & Rule #98).

* **Hasil Pengujian**:
  * Div Tag Balance: **99 open / 99 close (100% Match)**.
  * Render Test: **PASS**.
  * Vite Asset Build: **PASS (1.53s)**.


---

---

### [2026-11-07 17:02 WITA] — Standarisasi Kartu Paket Layanan Full-Width (Rule #99 Anti-Narrow Void)

* **Modul / Fitur**: Halaman Detail Profil Fotografer (`/fotografer/{id}`) — Tab 1 (Paket Layanan)
* **File yang Diubah**:
  * [`resources/views/public/photographers/show.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/public/photographers/show.blade.php)
  * [`README.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/README.md)
  * [`HANDOVER.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/HANDOVER.md)

* **Rincian Perubahan**:
  1. **Pemberantasan Grid Sempit 3-Kolom yang Sesak**:
     * Menghapus kontainer sempit `grid-cols-1 md:grid-cols-2 lg:grid-cols-3` yang sebelumnya memaksa kartu paket pemotretan terjepit di sudut kiri sebesar ~350px dengan 66% ruang kosong di kanan.
  2. **Kartu Membentang Penuh (*Full-Width Stacked Cards*)**:
     * Kartu paket layanan kini membentang penuh (**`w-full bg-white dark:bg-gray-800 p-8 sm:p-10 lg:p-12 rounded-3xl border border-gray-200 dark:border-gray-700 shadow-none`**), selaras dengan gaya kartu album di Tab Galeri Portofolio dan Kalender Ketersediaan.
  3. **Struktur Internal 2-Kolom Seimbang (5:7)**:
     * **Kolom Kiri (`lg:col-span-5`)**: Kicker paket sesi, nama paket (`text-2xl sm:text-3xl font-black`), deskripsi narasi lengkap, dan tarif sesi (`text-3xl sm:text-4xl font-black`).
     * **Kolom Kanan (`lg:col-span-7`)**: Tajuk fasilitas layanan dan daftar rincian benefit yang disajikan dalam **grid 2-kolom (`grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5`)**, memberikan ruang horizontal lapang sehingga teks panjang tidak terdesak.
  4. **Kepatuhan Anti-AI Slop**:
     * 0 garis divider pembatas (`0 border-t / border-b`).
     * 0 badge mengambang.
     * Tanpa simbol `&` redundan.
     * Murni informasional sesuai Rule #66 Point 7.

* **Hasil Pengujian**:
  * Div Tag Balance: **104 open / 104 close (100% Match)**.
  * Render Test: **PASS**.
  * Vite Asset Build: **PASS (1.51s)**.


---

---

### [2026-11-07 17:18 WITA] — Standarisasi Header Editorial Profil Kreator (Rule #100 Anti-AI Slop)

* **Modul / Fitur**: Halaman Detail Profil Fotografer (`/fotografer/{id}`) — Header Profil Kreator
* **File yang Diubah**:
  * [`resources/views/public/photographers/show.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/public/photographers/show.blade.php)
  * [`README.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/README.md)
  * [`HANDOVER.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/HANDOVER.md)

* **Rincian Perubahan**:
  1. **Eliminasi Total Matriks Formulir Spek Kaku 6-Box**:
     * Menghapus tabel data 3x2 kaku yang sebelumnya menumpuk 6 pasang label abu-abu kecil dan angka tebal (Lokasi, Tarif, Pengalaman, Portofolio, Bergabung, Rating).
  2. **Penerapan Baris Reputasi & Kepercayaan Terpadu (*Inline Trust Signals Bar*)**:
     * Merangkum sinyal reputasi dalam satu baris horisontal yang bersih dan elegan khas desainer Dribbble:
       `📍 Kota Jakarta Selatan • ★ 5.0 (2 ulasan) • 1+ Tahun Pengalaman • 3 Portofolio Karya`.
  3. **Hierarki Typografi Editorial Berjiwa Manusia**:
     * Nama Fotografer tampil gagah dan berwibawa (`text-3xl sm:text-4xl lg:text-5xl font-black leading-none`).
     * Tagline spesialisasi tipografi murni amber.
     * Bio naratif yang lega mengalir tanpa terhalang kotak-kotak spek.
  4. **Penyatuan Komersial Tarif & Tombol Aksi**:
     * Tarif awal sesi (`Rp ... / sesi pemotretan`) ditempatkan sejajar di baris bawah bersama tombol aksi utama (`Reservasi`, `Chat`, dan `Bookmark Favorit`), memperkuat konversi pemesanan secara wajar dan profesional.
  5. **Proporsi Foto Potret Alami**:
     * Menggunakan rasio potret ideal 4:5 (`aspect-[4/5] rounded-3xl`) yang natural dan berestetika tinggi, tanpa distorsi `self-stretch`.

* **Hasil Pengujian**:
  * Div Tag Balance: **105 open / 105 close (100% Match)**.
  * Render Test: **PASS**.
  * Vite Asset Build: **PASS (1.76s)**.


---

---

### [2026-11-07 17:35 WITA] — Eksperimen Harmonitas Tipografi Mikro & Skala Proporsional Halaman Profil (Selaras dengan Form Reservasi)

* **Modul / Fitur**: Halaman Detail Profil Fotografer (`/fotografer/{id}`) — Eksperimen Micro-Typography & Scaled Layout
* **File yang Diubah**:
  * [`resources/views/public/photographers/show.blade.php`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/resources/views/public/photographers/show.blade.php)
  * [`TRACKING_PERUBAHAN_FILE.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/TRACKING_PERUBAHAN_FILE.md)
  * [`HANDOVER.md`](file:///c:/Portofolio/LARAVEL/Marketplace%20Fotographers/marketplace_photographers/HANDOVER.md)

* **Titik Pengaman Rollback (Revert Safety Point)**:
  * File Backup Otomatis: `scratch/show.blade.php.pre_micro_typography_backup` (Identik dengan kondisi sebelum eksperimen, 957 baris).
  * Commit Terakhir: `51109d162593a747ffca7e059ae57b20c361cc7d`
  * Perintah Revert Seketika jika Diminta Pengguna:
    `Copy-Item 'scratch\show.blade.php.pre_micro_typography_backup' 'resources\views\public\photographers\show.blade.php' -Force; npm run build`

* **Rincian Perubahan (Menyelaraskan dengan Estetika Form Reservasi `/booking/create` & Navbar)**:
  1. **Harmonisasi Master Container & Padding**:
     * Padding master card luar diturunkan dari `p-8 sm:p-10 lg:p-12 rounded-3xl` menjadi `p-6 sm:p-8 lg:p-9 rounded-2xl sm:rounded-3xl border-gray-200/80` yang lebih proporsional, menghilangkan ruang kosong berlebih di kanan header.
  2. **Proporsi Foto Profil Kompak (Refined Aspect 4:5)**:
     * Ukuran foto dioptimalkan dari `w-[320px]` raksasa menjadi `w-full sm:w-[200px] md:w-[220px] lg:w-[240px] aspect-[4/5] rounded-2xl`, memberikan ruang horizontal yang seimbang dan anggun bagi konten identitas di kanannya.
  3. **Hierarki Tipografi Mikro pada Identitas Kreator**:
     * Nama Fotografer: Dari skala monster `text-5xl font-black` disesuaikan menjadi `text-xl sm:text-2xl lg:text-3xl font-black tracking-tight leading-tight`, selaras dengan ukuran judul di navbar dan form reservasi.
     * Baris Trust Signals & Bio: Menggunakan font `text-xs sm:text-[13px]`, lebih padat, rapi, dan mature.
     * Tarif Awal: Menggunakan `text-xl sm:text-2xl font-black` dengan label mikro `text-[10px]`.
     * Tombol Aksi: Tombol Reservasi (`px-6 py-2.5 text-xs sm:text-sm font-extrabold rounded-xl`), Chat (`px-4.5 py-2.5 text-xs sm:text-sm rounded-xl`), dan Bookmark Favorit (`w-10 h-10 rounded-xl`).
  4. **Skala Tab Navigasi & Judul Seksi**:
     * Tab item: `text-xs sm:text-sm font-bold` dengan badge `text-[10px] font-black`.
     * Judul seksi tab (`<h2>`): Disesuaikan menjadi `text-base sm:text-lg font-black tracking-tight` (menggantikan `text-3xl` yang sebelumnya terlalu berteriak).
  5. **Penyesuaian Skala di Tab Konten**:
     * **Tab Paket Layanan**: Card padding `p-6 sm:p-7 lg:p-8 rounded-2xl`, nama paket `text-base sm:text-lg font-black`, tarif paket `text-xl sm:text-2xl font-black`, item rincian fasilitas `text-xs`.
     * **Tab Galeri Portofolio**: Foto album `h-[260px] sm:h-[340px] md:h-[400px] lg:h-[440px] rounded-xl`, judul album `text-base sm:text-lg font-black`, deskripsi `text-xs sm:text-[13px]`. Watermark murni tipografi tetap terjaga rapi.
     * **Tab Kalender**: Judul bulan `text-sm sm:text-base font-black`, cell hari `min-h-[64px] sm:min-h-[76px] p-2 rounded-xl`, tanggal `text-xs sm:text-sm font-black`.
     * **Tab Ulasan Klien**: Skor angka rata-rata `text-4xl sm:text-5xl font-black`, kartu testimoni `p-5 sm:p-6 rounded-2xl`, teks ulasan `text-xs sm:text-sm`.
     * **Tab Tanya Jawab**: Accordion card `rounded-xl`, tombol pertanyaan `p-4 sm:p-5 text-xs sm:text-sm font-bold`, teks jawaban `text-xs sm:text-sm`.

* **Hasil Pengujian**:
  * Div Tag Balance: **106 open / 106 close (100% Match)**.
  * Render Test: **PASS (25 Lightbox reference, 0 Syntax Error)**.
  * Vite Asset Build: **PASS (1.51s)**.
  * HTTP Endpoint: **200 OK (`http://127.0.0.1:8000/fotografer/2`)**.

---

### [2026-11-09] Implementasi Universal Pop-up Login Diperlukan pada Tombol Reservasi, Chat, dan Simpan
* **File Diubah**:
  1. `resources/views/public/photographers/show.blade.php`:
     * Menambahkan Alpine state `loginModal`, `loginModalMessage`, `loginRedirectUrl`, `openLoginModal(msg, url)`, dan event listener `@open-login-modal.window`.
     * Mengubah tombol **Reservasi**, **Chat**, dan **Simpan** (Bookmark):
       - Jika pengguna telah login (`@auth`), fungsi berjalan langsung ke rute tujuan tanpa hambatan.
       - Jika pengguna belum login (tamu / `@guest`), sistem tidak lagi me-redirect paksa secara instan, melainkan memunculkan pop-up modal "Login Diperlukan" dengan penjelasan kontekstual yang sopan dan ramah.
     * Mengintegrasikan markup Universal Login Modal standar LensMatch (Rule #6 & #59: header tanpa ikon, tombol silang SVG di kanan atas, pesan kontekstual, dan tombol CTA `"Masuk ke Akun"` berwarna `amber-400`).
  2. `resources/views/public/photographers/katalog.blade.php`:
     * Menambahkan state Alpine dan modal yang sama pada etalase katalog utama.
     * Mengubah tombol Simpan/Bookmark di setiap kartu fotografer agar memicu modal "Login Diperlukan" melalui custom event saat diklik oleh tamu.
* **Hasil Pengujian & Verifikasi**:
  * HTML Tag Balance `show.blade.php`: **div opens: 112, closes: 112 (Match)**.
  * HTML Tag Balance `katalog.blade.php`: **div opens: 74, closes: 74 | section opens: 2, closes: 2 (Match)**.
  * Blade Cache: Cleared successfully (`php artisan view:clear`).
  * Vite Production Build: **PASS (`built in 4.60s`, Exit code 0)**.
  * HTTP Test: **200 OK (`/fotografer/2` & `/fotografer`)**.

---

### [2026-11-09] Eliminasi Total Data Statis, Hardcoded Fallback & Standarisasi 100% Data Dinamis Database (Rule #15, #70, #89, #94)
* **Kategori 1: Standarisasi Kode Sistem & Eliminasi Dummy Codes**:
  * `app/Models/Contract.php` & `app/Models/Payment.php`: Menambahkan accessor resmi `contract_code` (`KTRK-00001` tanpa `#`) dan `invoice_code` (`INV-LM-YYYY-XXXX`).
  * `app/Http/Controllers/Admin/TransactionController.php` & `resources/views/admin/transactions/index.blade.php`: Menghilangkan kode dummy fiktif `'INV-XXXXX'`, digantikan dengan `$c->invoice_code`.
  * Seluruh Controller (`TransactionController`, `ClientBookingController`, `PhotographerBookingController`, `AvailabilityController`, `ReportController`, `BookingController`, `ChatController`, `ReviewController`): Menghapus simbol pagar `#` dari seluruh perakitan string kode kontrak (`#KTRK-` menjadi `KTRK-` sesuai Rule #89).
  * `resources/views/client/invoices/index.blade.php`, `resources/views/photographer/reports/index.blade.php`, `export.blade.php`: Menghapus perakitan string manual di Blade, kini menggunakan accessor Eloquent `$c->invoice_code`.
* **Kategori 2: Dinamisasi Kumpulan Konten & Dataset dari Database**:
  * `database/seeders/PhotographerSeeder.php`: Memasukkan data seeder resmi 7 butir Tanya Jawab profesional ke tabel `photographer_faqs` untuk setiap fotografer di database.
  * `app/Http/Controllers/Public/PhotographerController.php` & `resources/views/public/photographers/show.blade.php`: Menghapus hardcoded array 7 FAQ di controller; kini mengambil dari relasi database `$photographer->faqs()->where('is_active', true)->orderBy('urutan')->get()` lengkap dengan empty state `@forelse` jika kosong.
  * `routes/web.php` & `resources/views/public/home.blade.php`:
    - Menghapus 5 foto stok Unsplash statis di hero; kini mengambil media portofolio riil fotografer dari tabel `portfolio_medias`.
    - Menghapus angka statis counter `150+`, `35+`, `2.400+`, `4.9 / 5.0`; kini dihitung secara dinamis dari database (`totalPhotographers`, `totalCities`, `totalSessions`, `avgRating`).
  * `resources/views/public/photographers/index.blade.php` & `PhotographerController@index`:
    - Mengganti teks statis `150+` dengan `{{ $totalPhotographers }}+`.
    - Mengganti 4 foto statis showcase kartu dengan karya portofolio dinamis dari tabel `photographer_portfolios`.
    - Mengganti rating statis `4.9 / 5.0` dan pesanan statis dengan nilai dinamis database.
  * `resources/views/public/elearning/index.blade.php` & `ElearningController.php`:
    - Mengganti 3 kartu mengambang statis Unsplash di hero dengan 3 kursus unggulan riil dari tabel `elearnings` (`$heroCourses`).
* **Kategori 3: Penghapusan Fallback Statis Palsu**:
  * `resources/views/public/photographers/show.blade.php`: Menghapus penetapan harga fiktif `$lowestPrice = 1500000;`. Jika layanan kosong, menampilkan teks *"Belum ada paket layanan"*. Menghapus URL fallback avatar wanita Unsplash.
  * `resources/views/client/favorites/index.blade.php`: Menghapus fallback domisili `'Indonesia'` (diganti `-`).
  * `resources/views/admin/elearning/create.blade.php`: Menghapus default value `149000` dan contoh angka, diganti placeholder instruksi bersih.
  * `app/Http/Controllers/Admin/ReviewController.php`: Menghapus fallback rating palsu `?: 5.0`.
* **Hasil Pengujian**:
  * 10 File Blade: **100% MATCH Div Open & Close**.
  * View Cache: **Compiled views cleared**.
  * Vite Asset Build: **PASS (built in 2.53s)**.
  * Endpoints: **`200 OK` on `/`, `/fotografer`, `/fotografer/katalog`, `/fotografer/2`, `/elearning`, `/elearning/katalog`, `/forum`**.

### [2026-11-09] Penetapan Standar Baru Rule #104: Penanganan Data Kosong (Nullable-First) & Pembersihan Menyeluruh Data Statis Front-End
* **Rule #104 Ditambahkan ke `README.md`**:
  * Menetapkan prinsip arsitektur bahwa sebagian besar data di platform LensMatch diperbolehkan kosong (*nullable*). Dilarang keras mengarang nilai fallback fiktif. Jika data kosong, tampilkan tanda hubung (`-`), placeholder instruksional kosong, atau empty state yang jujur.
* **Pembersihan Residu Template Mockup Bawaan**:
  * `resources/views/partials/profile/profile-info-modal.blade.php`: Menghapus seluruh data statis template Pimjo (`Testing Chowdhury`, `randomuser@pimjo.com`, `+09 363 398 46`, link Facebook PimjoHQ) dan menggantinya dengan binding dinamis `auth()->user()`.
  * `resources/views/partials/profile/profile-address-modal.blade.php`: Menghapus data alamat statis luar negeri (`United States`, `Arizona`, `ERT 2489`, `AS4568384`) dan menggantinya dengan input bersih bernilai awal kosong.
  * `resources/views/partials/header.blade.php`: Mengganti nama statis `Testing Chowdhury` dan email `randomuser@pimjo.com` dengan `{{ auth()->user()->nama ?? 'Pengguna' }}` dan `{{ auth()->user()->email ?? '-' }}`.
  * `resources/views/public/payment/index.blade.php`: Menghapus mockup statis lama (`CTR-2026-08129`, `Alex Visual Studio`, `Rp 2.250.000`) dan menggantinya dengan tampilan dinamis berbasis `$contract` serta empty state informasional.
* **Pemberantasan Nilai Default Fiktif pada Form (Zero Pre-Filled Defaults)**:
  * `resources/views/admin/contests/create.blade.php`: Menghapus default value `Landscape & Alam`, `LensMatch Official`, `Rp 15.000.000 + E-Sertifikat`, dan `BCA`.
  * `resources/views/admin/contests/edit.blade.php`: Menghapus fallback default `'BCA'` saat data nama bank lomba bernilai null.
  * `resources/views/photographer/availability/create.blade.php`: Menghapus default jam mulai `15:00` dan jam selesai `20:00`.
  * `resources/views/public/booking/create.blade.php`: Menghapus default jam mulai `14:00`, memaksa pemilihan slot jadwal ketersediaan nyata oleh pengguna.
* **Pemberantasan Fallback String Fiktif di View & Controller**:
  * `resources/views/photographer/reports/export.blade.php`: Menghapus fallback domisili `'Indonesia'` dan nama klien `'Klien LensMatch'`.
  * `resources/views/public/forum/index.blade.php`: Menghapus fallback author `'Admin LensMatch'` (diganti `'Pengguna'`).
  * `resources/views/public/elearning/invoice-print.blade.php`: Menghapus fallback `'Instruktur LensMatch'` (diganti `-`).
  * `resources/views/client/invoices/index.blade.php`: Menghapus fallback penyelenggara lomba `'LensMatch Official'` (diganti `-`).
  * `resources/views/public/contests/show.blade.php`: Menghapus nomor rekening fallback dummy `'8271928391'` dan nama `'PT LensMatch Indonesia'`.
  * `app/Http/Controllers/Public/BookingController.php`: Menghapus suntikan nomor telepon dummy `'08123456789'` pada saat `firstOrCreate` profil klien baru.
  * `app/Http/Controllers/Client/ClientBookingController.php` & `ClientFavoriteController.php`: Menghapus fallback nama dummy `'Client LensMatch'`.
  * `app/Http/Controllers/Public/PhotographerController.php` & `routes/web.php`: Menghapus fallback rating palsu `'5.0'` saat database ulasan kosong (diganti `'-'`).
* **Pembersihan Dummy Placeholder & URL Eksternal**:
  * `resources/views/admin/transactions/index.blade.php`: Mengganti placeholder catatan ref transfer `TRX123456` dengan panduan instruksional.
  * `resources/views/photographer/services/create.blade.php` & `edit.blade.php`: Mengganti `placeholder="Contoh: 3500000"` dengan `placeholder="Masukkan nominal tarif paket (min. 10000)"`.
  * `resources/views/public/chat/index.blade.php`: Mengganti dummy placeholder `2500000` dan durasi `3` dengan placeholder instruksional.
  * `resources/views/admin/elearning/edit.blade.php`: Mengganti placeholder `"0"` dengan `"Masukkan nominal harga kursus"`.
  * `resources/views/partials/photographer/onboarding-wizard.blade.php`: Mengganti `placeholder="Contoh: 1234567890"` dan `placeholder="500000"` dengan placeholder instruksional.
  * `resources/views/photographer/profile/index.blade.php`, `signin.blade.php`, `signup.blade.php`, `contests/show.blade.php`: Mengganti placeholder nomor dummy `081234567890` dengan `Contoh: 08xxxxxxxxxx`.
  * `resources/views/public/auth/signin.blade.php` & `signup.blade.php`: Mengganti foto stok Unsplash eksternal dengan aset lokal resmi `asset('images/prewedding/prewedding-2.jpg')` dan `prewedding-holding-hands.jpg`.
  * `resources/views/public/help/faq.blade.php`: Mengganti nomor WhatsApp fiktif `6281234567890` dengan variabel konfigurasi aplikasi `config('app.support_whatsapp')`.
  * `database/seeders/ActivityLogSeeder.php`: Menghapus simbol pagar `#` dari 5 entri seeder `#KTRK-00001` menjadi `KTRK-00001` (Rule #89).
* **Verifikasi Mutu & Keseimbangan Elemen**:
  * 30 File Blade Terkait: **100% MATCH Div Open & Close (0 mismatch)**.
  * Artisan Route List: **185 routes compiled successfully with 0 syntax errors**.
  * Vite Asset Build: **PASS (built in 4.84s)**.
  * HTTP Endpoint Verification: **`200 OK` across all main routes (`/`, `/fotografer`, `/katalog`, `/elearning`, `/forum`, `/lomba`, `/faq`, `/login`, `/register`)**.




### [2026-11-09] Finalisasi Standar Validasi Real-Time 5 Pilar pada Seluruh Input Field Wajib (*) & Modal Aksi Sistem
* **Prinsip Validasi Real-Time 5 Pilar**:
  1. *Dynamic Reactive State*: State `form`, `touched`, `errors` mandiri di Alpine.js.
  2. *Dual Event Triggers*: Pemicu validasi instan pada `@input` (saat mengetik) dan `@blur` (saat kehilangan fokus).
  3. *Border / Ring Visual Feedback*: Reaktif berubah menjadi `border-red-400 focus:ring-red-400 focus:border-red-400` saat invalid, atau `border-gray-200 ... focus:ring-amber-500` saat valid.
  4. *Inline Error Message*: Teks pesan kesalahan berwarna merah (`text-[11px] font-bold text-red-500 mt-1`) tepat di bawah elemen input terkait (`x-show="touched.x && errors.x"`).
  5. *Intelligent Submission & Auto-Focus Trap*: Validasi menyeluruh saat `@submit`, mencegah reload jika ada field tidak valid (`$event.preventDefault()`), mengarahkan scroll dan fokus kursor otomatis ke field pertama yang bermasalah (`$refs.fieldX?.focus()`), dan **DILARANG menonaktifkan submit button (`:disabled` forbidden)** agar pengguna tidak bingung.
* **Kepatuhan Ketat Tanda Asterisk (*) & Rule #104**:
  - Validasi real-time HANYA diterapkan pada input field yang memiliki tanda bintang merah (`*`) atau bersifat wajib di backend.
  - Seluruh field opsional/nullable (catatan khusus, bio singkat, tautan portofolio eksternal, direktori sosial media, dll) tetap dipertahankan tanpa validasi wajib untuk menjaga fleksibilitas input pengguna.
* **Implementasi Menyeluruh pada 20 File View**:
  1. `resources/views/partials/client/review-modal.blade.php`: Rating (1-5 bintang) & `deskripsi_review` dengan reactive state, `@input`/`@blur`, red borders, auto-focus trap, dan zero disabled-button blockage.
  2. `resources/views/partials/photographer/onboarding-wizard.blade.php`: Mengganti dialog browser `alert()` dengan inline 5-pillar validation pada Step 1 (`province`, `city`, `alamat`, `categories`), Step 2 (`link_sosmed`, `deskripsi_bio`, `nama_layanan`, `tarif_harga`, `deskripsi_layanan`), dan Step 3 (`bank`, `nomor_rekening` [sanitasi numerik], `atas_nama_rekening`).
  3. `resources/views/public/forum/show.blade.php`: Form komentar utama, form balasan bertingkat inline, dan modal laporan pelanggaran (`kategori` wajib, `alasan` opsional).
  4. `resources/views/public/booking/create.blade.php`: Form reservasi sesi foto klien (`lokasi_acara`, `province_code`, `city_code`, `service_id`, `tanggal_sesi`, `jam_mulai`) dengan auto-scroll dan focus trap. `catatan_khusus` dibiarkan nullable.
  5. `resources/views/public/contests/show.blade.php`: Form registrasi event lomba (`nama_lengkap`, `email`, `no_wa`, `asal_kota`, `motivasi`), upload bukti transfer pembayaran lomba, dan upload karya foto (`judul_karya`, `foto_file`).
  6. `resources/views/admin/contests/create.blade.php`: Validasi real-time `biaya_pendaftaran` (saat tipe berbayar) dan `max_submissions` (1-10).
  7. `resources/views/admin/contests/edit.blade.php`: Validasi real-time `biaya_pendaftaran` dan `max_submissions`.
  8. `resources/views/admin/elearning/curriculum.blade.php`: Modal tambah/edit modul (`judul_modul`) dan modal pelajaran (`judul`, `id_module`, `tipe`).
  9. `resources/views/client/bookings/index.blade.php`: Modal pembatalan reservasi klien (`alasan` wajib) dan modal ajukan jadwal ulang (`reschedule_date` & `reschedule_reason` wajib).
  10. `resources/views/client/galleries/index.blade.php`: Modal permintaan revisi hasil foto (`catatan_revisi` wajib).
  11. `resources/views/photographer/bookings/index.blade.php`: Modal serah terima galeri hasil foto (`hasil_foto_url` wajib), modal pembatalan (`alasan` wajib), dan modal ajukan jadwal ulang (`reschedule_date` & `reschedule_reason` wajib).
  12. `resources/views/photographer/services/create.blade.php`: Benefit fasilitas layanan (`fitur[]` wajib minimal 1 dan minimal 3 karakter), serta sinkronisasi label asterisk `*`.
  13. `resources/views/photographer/services/edit.blade.php`: Benefit fasilitas layanan (`fitur[]` wajib minimal 1 dan minimal 3 karakter), serta sinkronisasi label asterisk `*`.
  14. `resources/views/admin/contests/index.blade.php`: Modal review penolakan pengajuan lomba (`catatan_admin` wajib) dan modal quick reject proposal (`catatan_admin` wajib, menghapus atribut `:disabled` pada tombol submit).
  15. `resources/views/admin/contests/manage.blade.php`: Modal verifikasi peserta lomba (saat status ditolak, `catatan_admin` wajib divalidasi secara real-time).
  16. `resources/views/admin/contests/participants.blade.php`: Modal penolakan pendaftaran calon peserta (`catatan_admin` wajib).
  17. `resources/views/admin/photographers/index.blade.php`: Modal penghapusan akun fotografer (`kategori_penghapusan` dropdown & `alasan_penghapusan` textarea wajib, menghapus `:disabled` pada tombol submit).
  18. `resources/views/admin/photographers/show.blade.php`: Modal penolakan verifikasi fotografer (`catatan_penolakan` wajib) dan modal penghapusan fotografer (`kategori_penghapusan` & `alasan_penghapusan` wajib).
  19. `resources/views/admin/transactions/index.blade.php`: Modal refund transaksi ke klien (`refund_notes` wajib).
  20. `resources/views/public/booking/invoice.blade.php`: Modal upload struk bukti transfer pembayaran (`bukti_transfer` wajib dengan validasi ukuran file maks 10MB dan tipe gambar).
* **Verifikasi Mutu & Keseimbangan**:
  - Scanning AST seluruh 129 input wajib (*) di 60+ file Blade: **100% tervalidasi real-time**.
  - Pemeriksaan Keseimbangan Tag: **20 File Termodifikasi: 100% MATCH Div Open & Close (Diff = 0)**.
  - Template Compilation: `php artisan view:cache` **100% PASS tanpa error syntax**.

---

### [2026-11-12] Pembaruan Reputasi & Metrik Keunggulan Fotografer (Opsi 2 + Validasi Berbasis Klien)
* **Modul / Fitur**: Indikator Reputasi Fotografer (`Top Rated`, `Jam Terbang Tinggi`, `Respon Cepat`) pada Model, Dashboard Fotografer, Katalog Publik, Favorit Klien, dan Dashboard Admin.
* **File yang Dimodifikasi**:
  * `app/Models/RolePhotographer.php`
  * `app/Http/Controllers/Photographer/DashboardController.php`
  * `resources/views/photographer/dashboard.blade.php`
  * `app/Http/Controllers/Public/PhotographerController.php`
  * `app/Http/Controllers/Client/ClientFavoriteController.php`
  * `app/Http/Controllers/Admin/DashboardController.php`
  * `resources/views/admin/dashboard.blade.php`
  * `resources/views/client/favorites/index.blade.php`
  * `routes/web.php`
  * `README.md` (Pembaruan `Rule #84`)
* **Rincian Perubahan (Before vs After)**:
  1. **Kecepatan Respon (`Respon Cepat`) -> Opsi 2 (Interaksi Klien)**:
     * *Before*: Menggunakan `users.last_seen_at` dalam 24 jam terakhir sehingga fotografer baru yang baru membuka dashboard admin verifikasi langsung mendapat status `Tercapai` dan badge `Respon Cepat` tanpa pernah merespons klien.
     * *After*: Menambahkan `RolePhotographer::getClientResponseStats()` yang menghitung kecepatan rata-rata balasan chat klien (`chat_messages`, `deleted_for_all = false`) <= 24 jam (minimal 1 balasan nyata, mengabaikan pesan sapaan sistem otomatis sebelum klien mengirim pesan), memastikan tidak ada pesan klien terbengkalai > 24 jam, serta menggugurkan lencana `Respon Cepat` bila ada order booking `pending` (`is_validated_photographer = false`, `status_booking = 'pending'`) yang dibiarkan > 24 jam.
  2. **Jam Terbang Sesi (`Jam Terbang Tinggi`) -> Validasi Konfirmasi Klien**:
     * *Before*: Menghitung semua `contracts` dengan `status_contract = 'completed'`, termasuk pesanan yang baru diunggah link Google Drive oleh fotografer tetapi masih berstatus *"Menunggu Konfirmasi Klien"* (`is_validated_client = false`).
     * *After*: Diperketat menjadi `status_contract = 'completed'` dan `is_validated_client = true` di `RolePhotographer`, `Photographer/DashboardController`, dan `Admin/DashboardController` / `admin/dashboard.blade.php`.
  3. **Rating Fotografer (`Top Rated`) -> Filter Ulasan Aktif (`is_hidden = false`)**:
     * *Before*: Relasi `testimonials` dan query statistik rating belum menyaring kolom `is_hidden = false`, sehingga ulasan yang disembunyikan oleh moderasi admin masih ikut terhitung di rata-rata rating dan jumlah ulasan.
     * *After*: Menambahkan relasi `visibleTestimonials()` (`where('is_hidden', false)`) pada `RolePhotographer` dan menyaring `is_hidden = false` di seluruh query rating (`DashboardController`, `Public/PhotographerController`, `ClientFavoriteController`, `Admin/DashboardController`, `routes/web.php`), serta menjaga ikon bintang pada `client/favorites/index.blade.php` agar hanya tampil jika rating ada.
* **Hasil Pengujian**:
  * Kompilasi Blade (`php artisan view:clear` & `view:cache` via PHP 8.4.24): **PASS (0 error)**.
  * Uji HTTP Render Routes (`/` `200`, `/fotografer` `200`, `/fotografer/katalog` `200`, `/fotografer/1` `200`, `/client/favorites` `200`, `/photographer/dashboard` `200` untuk Kenji & Alex, `/admin/dashboard` `200`): **PASS**.
  * Uji Perilaku Transaksional (DB Rollback 7 Skenario: sapaan otomatis diabaikan, balasan chat cepat, gugur saat booking pending > 24 jam, gugur saat chat terbengkalai > 24 jam, gugur saat rata-rata > 24 jam, ulasan tersembunyi dikecualikan, sesi butuh `is_validated_client = true`): **ALL 7 SCENARIOS PASS**.

---

### [2026-11-12] Penyeragaman Toolbar Filter, Live Search, Pagination, dan Siklus Tanggal Lewat pada Tabel Tanggal Libur (`photographer/availability`)
* **Modul / Fitur**: Halaman Kelola Jadwal Fotografer Tab `Tanggal Libur` (`/photographer/availability?tab=overrides`).
* **File yang Dimodifikasi**:
  * `app/Http/Controllers/Photographer/AvailabilityController.php`
  * `resources/views/photographer/availability/index.blade.php`
  * `README.md` (Penambahan poin 4 pada `Rule #105`)
* **Rincian Perubahan**:
  1. Menambahkan filter `periode` (`all`, `upcoming`, `past`), filter `tipe` (`all`, `all_day`, `time_slot`), dan urutan `sort` pada `AvailabilityController::index()`, mendukung pencarian format tanggal tampilan (`DATE_FORMAT` `%d %b %Y` serta `%d/%m/%Y` untuk driver MySQL/MariaDB), serta memisahkan metrik `$totalAllBlocked` (badge tab) dengan `$totalBlocked` (`Libur Aktif`, `tanggal >= today`).
  2. Mengintegrasikan `x-data="dashboardLiveTable()"` dengan toolbar 3 dropdown filter Alpine.js di kiri, live search di kanan, `#dashboard-stats-container`, `#dashboard-table-container`, dan partial pagination standar `partials.dashboard.pagination` saat `$availabilities->total() > 0`.
  3. Menangani tanggal libur yang sudah lewat (`tanggal < hari ini`): data tidak dihapus otomatis dari database, tampil dengan status `Sudah Lewat` dan tombol `Hapus` (soft delete), sementara tanggal aktif (`>= hari ini`) tetap berstatus `Ditutup` dengan tombol `Buka Jadwal`.
* **Hasil Pengujian**:
  * `artisan view:clear` lalu `view:cache` via PHP 8.4.24 serta keseimbangan tag HTML (`40/40` `<div>`): **PASS**.
  * Uji render HTTP (`/photographer/availability?tab=weekly` serta `?tab=overrides` status `200`) dan simulasi transaksional DB Rollback (30/30 asersi lulus: seluruh filter `periode`/`tipe`/`sort`/`per_page`, pencarian format tanggal tampilan, pagination standar, pemisahan hitungan aktif vs riwayat, pengecualian tanggal lewat di kalender publik, serta pesan flash `destroy()` untuk riwayat vs jadwal aktif): **PASS**.

---

### [2026-11-12] Catatan Penolakan Inline pada Stepper Tahap Fotografer serta Eliminasi Banner Ganda (`Cara A`)
* **Modul / Fitur**: Partial `stage-stepper.blade.php`, Profil Fotografer, dan Dashboard Fotografer.
* **File yang Dimodifikasi**:
  * `resources/views/partials/photographer/stage-stepper.blade.php`
  * `resources/views/photographer/profile/index.blade.php`
  * `resources/views/photographer/dashboard.blade.php`
  * `README.md` (Pembaruan `Rule #95` poin 5 serta `Rule #108` poin 2)
* **Rincian Perubahan**:
  1. Menampilkan tepat satu baris teks lembut `Catatan Admin: <isi catatan>` (`text-xs text-gray-600 dark:text-gray-300` dengan label `text-rose-600 font-bold`) tepat di bawah baris utama stepper pada `stage-stepper.blade.php` ketika `status_verifikasi === 'ditolak'` (Tahap 1) atau `status_etalase === 'ditolak'` (Tahap 3), tanpa garis pembatas (`0 border-t`) serta tanpa kotak berlapis (`0 bg-rose-50` / `0 border`).
  2. Menghapus tautan redundan `Lihat Catatan Admin` di sisi kanan stepper ketika berada pada langkah aktif (`Atur Jadwal`), serta menampilkan CTA `Perbaiki Profil` pada Dashboard ketika pendaftaran Tahap 1 ditolak.
  3. Menghapus banner penolakan profil ganda pada `photographer/profile/index.blade.php` dan `photographer/dashboard.blade.php`, sekaligus merapikan tampilan catatan penolakan etalase di kartu `Persiapan Tayang Etalase` pada `dashboard.blade.php` agar memakai satu baris teks lembut yang seragam tanpa kotak merah bersarang maupun judul berulang.
* **Hasil Pengujian**:
  * Kompilasi Blade (`artisan view:clear` lalu `view:cache` via PHP 8.4.24) dan keseimbangan `<div>` (`stage-stepper` `7/7`, `profile/index` `68/68`, `dashboard` `100/100`): **PASS**.
  * Uji render HTTP (`dashboard`, `profile`, `availability`, `services`) dan simulasi transaksional DB Rollback 4 skenario (tanpa penolakan, profil ditolak Tahap 1, etalase ditolak 2/3, etalase ditolak 3/3 siap ajukan ulang): **27/27 asersi PASS** (catatan muncul tepat sekali per halaman, nol divider garis, nol kotak berlapis, nol `&` berlebih).


---

### [2026-11-13] Standardisasi Warna Ikon Kalender Tahun Operasional Dashboard
* **Modul / Fitur**: Dashboard Fotografer & Dashboard Admin.
* **File yang Dimodifikasi**:
  * `resources/views/photographer/dashboard.blade.php`
  * `resources/views/admin/dashboard.blade.php`
* **Rincian Perubahan**:
  * Mengganti warna ikon kalender pada chip/badge "Tahun Operasional: {{ date('Y') }}" dari warna amber (`text-amber-500`) menjadi warna hitam netral (`text-gray-900 dark:text-white`) di kedua dashboard (Fotografer dan Admin) agar konsisten dan rapi (anti AI slop).
* **Hasil Pengujian**:
  * `php artisan view:clear`: **PASS**.


---

### [2026-11-13] Standardisasi Warna Ikon Kalender Tahun Operasional Dashboard
* **Modul / Fitur**: Dashboard Fotografer & Dashboard Admin.
* **File yang Dimodifikasi**:
  * `resources/views/photographer/dashboard.blade.php`
  * `resources/views/admin/dashboard.blade.php`
* **Rincian Perubahan**:
  * Mengganti warna ikon kalender pada chip/badge "Tahun Operasional: {{ date('Y') }}" dari warna amber (`text-amber-500`) menjadi warna hitam netral (`text-gray-900 dark:text-white`) di kedua dashboard (Fotografer dan Admin) agar konsisten dan rapi (anti AI slop).
* **Hasil Pengujian**:
  * `php artisan view:clear`: **PASS**.


---

### [2026-11-14] Standardisasi Posisi Center Pratinjau Foto pada Seluruh Komponen Input Upload Sistem
* **Modul / Fitur**: Profil Fotografer, Audit Profil Admin, Profil Klien Publik, Event Lomba Foto (Admin & Publik), E-Learning (Admin), Transaksi (Admin), dan Invoice Booking.
* **File yang Dimodifikasi**:
  * `resources/views/photographer/profile/index.blade.php`
  * `resources/views/admin/photographers/show.blade.php`
  * `resources/views/public/profile/index.blade.php`
  * `resources/views/admin/contests/create.blade.php`
  * `resources/views/admin/contests/edit.blade.php`
  * `resources/views/public/contests/propose.blade.php`
  * `resources/views/admin/elearning/create.blade.php`
  * `resources/views/admin/elearning/edit.blade.php`
  * `resources/views/admin/elearning/curriculum.blade.php`
  * `resources/views/admin/transactions/index.blade.php`
  * `resources/views/public/contests/show.blade.php`
  * `resources/views/public/booking/invoice.blade.php`
* **Rincian Perubahan**:
  * Mengeliminasi posisi rata kiri (*left-aligned*) pada seluruh pratinjau media/foto tunggal di atas maupun di bawah dropzone.
  * Memberikan perataan tengah horizontal (`flex justify-center`, `flex flex-col items-center`, dan `mx-auto`) sehingga kotak pratinjau foto profil 1:1, banner lomba, thumbnail materi, dan bukti transfer berada persis di tengah (*center*), seimbang, dan proporsional dengan dropzone di bawahnya.
  * Mematuhi kaidah copywriting bersih anti-AI slop: tanpa kata berbunga-bunga, tanpa simbol `&` berlebih, tanpa garis pembatas buatan, dan tanpa manipulasi logika data input.
* **Hasil Pengujian**:
  * `php artisan view:cache`: **PASS (100% template compiled successfully)**.
  * `php artisan view:clear`: **PASS**.


---

### [2026-11-14] Pemulihan 3 Kartu Floating Hero Showcase pada Halaman Utama E-Learning
* **Modul / Fitur**: Halaman Utama E-Learning Publik.
* **File yang Dimodifikasi**:
  * `resources/views/public/elearning/index.blade.php`
* **Rincian Perubahan**:
  * Menghapus kondisi pembatas `@if($h0)`, `@if($h1)`, dan `@if($h2)` yang sebelumnya menyebabkan Card 1 (Teknik Lighting Portrait), Card 3 (Mastering Color Grading), dan Card 4 (Bisnis Studio & Keuangan) hilang dari tampilan hero saat tabel kursus database belum memiliki data.
  * Menerapkan fallback pintar pada ketiga kartu: jika database memiliki data kursus unggulan, sistem akan menampilkan data database; jika database kosong, sistem menampilkan kartu showcase modul asli yang lengkap, rapi, dan simetris sehingga layout hero tidak lagi pincang.
* **Hasil Pengujian**:
  * `php artisan view:cache`: **PASS**.
  * `php artisan view:clear`: **PASS**.


---

### [2026-11-14] Penghapusan Floating Chat Widget Dummy
* **Modul / Fitur**: Layout Utama Aplikasi Publik (`layouts/app.blade.php`).
* **File yang Dimodifikasi / Dihapus**:
  * `resources/views/layouts/app.blade.php` (menghapus `@include('partials.shared.chat-widget')`)
  * `resources/views/partials/shared/chat-widget.blade.php` (dihapus dari repository)
* **Rincian Perubahan**:
  * Mengeliminasi widget melayang kuning "Real-time Chat LensMatch" yang merupakan sisa template statis lama/mockup mati (tidak terhubung ke database maupun controller).
  * Komunikasi obrolan real-time antar pengguna tetap berpusat pada halaman sistem chat resmi yang berjalan penuh menggunakan Laravel Reverb (`/chat` untuk Klien dan `/photographer/chat` untuk Fotografer).
* **Hasil Pengujian**:
  * `php artisan view:cache`: **PASS**.
  * `php artisan view:clear`: **PASS**.

---

### [2026-11-16] Implementasi Form Reservasi Bertahap Berantai (Sequential Cascading Step Locking)
* **Modul / Fitur**: Halaman Reservasi Booking Sesi Foto Klien (`booking.create`).
* **File yang Dimodifikasi**:
  * `resources/views/public/booking/create.blade.php`
* **Rincian Perubahan**:
  * Mengimplementasikan sistem penguncian berurutan (*sequential cascading step locking*) untuk 5 langkah formulir reservasi:
    1. **Langkah 1 (Pilih Paket Layanan)**: Aktif sejak awal. Memilih paket membuka Langkah 2 dan menggulir (*smooth scroll*) otomatis ke kalender jadwal.
    2. **Langkah 2 (Pilih Tanggal Sesi)**: Terkunci (`opacity-50 pointer-events-none select-none`) hingga paket dipilih pada Langkah 1. Memilih tanggal yang buka membuka Langkah 3 dan menggulir otomatis ke lokasi acara.
    3. **Langkah 3 (Lokasi Acara)**: Terkunci hingga tanggal sesi dipilih pada Langkah 2. Mengharuskan pengisian provinsi, kota/kabupaten, dan alamat lengkap (minimal 5 karakter) sebelum membuka Langkah 4.
    4. **Langkah 4 (Pilih Jam Mulai dan Selesai)**: Terkunci hingga data lokasi pada Langkah 3 lengkap. Menampilkan daftar slot jam yang tersedia dan memperbarui durasi sesi.
    5. **Langkah 5 (Catatan Khusus) & Tombol Submit**: Input catatan terkunci hingga jam mulai dan selesai valid. Tombol pengajuan reservasi di kolom ringkasan pesanan aktif hanya ketika seluruh prasyarat langkah 1–4 telah terpenuhi, dengan label instruksional dinamis (*Pilih Paket Layanan Terlebih Dahulu* -> *Pilih Tanggal Sesi* -> *Lengkapi Lokasi Acara* -> *Pilih Jam Mulai dan Selesai* -> *Ajukan Reservasi Jadwal*).
  * Kepatuhan penuh terhadap **Rule #70 (Anti-AI Slop Terminology & Natural Human Text Standard)** dan **Rule #41**:
    - Seluruh label status dan pesan panduan menggunakan bahasa Indonesia natural, ringkas, dan to the point tanpa pemborosan simbol `&`, tanpa jargon kaku, dan tanpa kalimat bertele-tele.
    - Menjaga integritas interaksi: pengguna tetap dapat menggulir ke atas kapan saja untuk mengganti pilihan tanpa merusak state formulir.
* **Hasil Pengujian**:
  * `php artisan view:clear`: **PASS**.
  * `php artisan view:cache`: **PASS (100% template compiled successfully)**.
  * Uji rendering HTTP terautentikasi: **PASS (Status 200 OK, seluruh direktif Alpine step lock terpasang presisi)**.


---

### [2026-11-17] Implementasi Opsi 2: Halaman Khusus Selesaikan Pembayaran (Checkout Standar Traveloka / Tiket.com) & Audit Anti-AI Slop
* **Modul / Fitur**: Alur Pembayaran Booking Klien (`/pembayaran/{id}` & `/client/bookings`).
* **File yang Dimodifikasi / Dibuat**:
  * `resources/views/public/booking/checkout.blade.php` (file baru: Halaman Checkout Pembayaran Aktif)
  * `resources/views/public/booking/invoice.blade.php` (lembar dokumen Invoice formal A4 murni)
  * `app/Http/Controllers/Public/BookingController.php` (`showInvoice` & `confirmPayment`)
  * `resources/views/client/bookings/index.blade.php` (modal pembatalan dan reschedule manusiawi)
* **Rincian Perubahan**:
  1. **Halaman Khusus Selesaikan Pembayaran / Payment Checkout (Standar Traveloka / Tiket.com)**:
     - Tombol `[Bayar]` pada `/client/bookings` mengarahkan ke `/pembayaran/{id}` yang secara cerdas me-render `public.booking.checkout` saat status reservasi `pending_payment` / aktif bayar.
     - **Batas Waktu Pembayaran (Countdown Timer)**: Baris penghitung waktu mundur yang tenang dan elegan via Alpine.js (`jam : menit : detik`), menghitung mundur batas waktu transfer 24 jam tanpa animasi neon ticker alay.
     - **Kartu Instruksi Transfer Bank Resmi**: Menampilkan rekening resmi Bank BCA `8840-1928-3746` a.n. `PT LensMatch Indonesia` lengkap dengan tombol interaktif **[Salin]** (otomatis feedback teks "Tersalin!" via `navigator.clipboard`), serta nominal transfer tepat dengan tombol **[Salin Nominal]**.
     - **Form Unggah Bukti Transfer Interaktif**: Dropzone area native menerima JPG, PNG, WEBP (maksimal 10 MB per Rule #21), required field asterisk merah `<span class="text-rose-500">*</span>` per Rule #20, pratinjau thumbnail instan dengan modal lightbox interaktif per Rule #97, dan tombol aksi utama `Kirim Bukti Pembayaran` berwarna amber per Rule #91.
     - **Status Menunggu Verifikasi Admin**: Jika struk pembayaran telah dikirimkan oleh klien, kartu menampilkan thumbnail struk dengan tombol pratinjau lightbox dan opsi `Ganti Berkas`.
     - **Link Pratinjau Dokumen Invoice**: Di pojok kanan atas disediakan tautan tenang `Lihat Pratinjau Invoice >` (`/pembayaran/{id}?view=invoice`) untuk melihat lembar faktur formal A4 kapan pun dibutuhkan.
  2. **Perbaikan Logika Status & Sinkronisasi Database**:
     - Memperbaiki bug status pada kontrak 13 di mana kontrak berstatus `pending_payment` secara keliru terbaca sebagai `Menunggu Konfirmasi` akibat pengecekan `!is_validated_photographer`.
     - Menyinkronkan logic dan database: kontrak dengan `status_contract === 'pending_payment'` murni berstatus **"Menunggu Pembayaran"** dan membuka form pembayaran aktif.
  3. **Pembersihan Glorifikasi Kode & Terminologi Escrow/Rekber**:
     - Modal Batal Klien: Berubah dari *"Apakah Anda yakin ingin membatalkan pesanan KTRK-00018?"* menjadi *"Apakah Anda yakin ingin membatalkan sesi [Nama Layanan] bersama [Nama Fotografer]?"* (Opsi A ala Airbnb/SweetEscape).
     - Menghilangkan jargon teknis "Escrow", "Rekber", simbol `&` redundan, serta warna-warni kontras tinggi pada kode database di seluruh view terkait.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #20** (Simbol Asterisk Wajib Isi Merah), **Rule #21** (Maksimal Upload 10 MB JPG/PNG/WEBP), **Rule #22** (Strict Icon Constraint), **Rule #47** (Standarisasi Konsistensi Navbar & Tombol Primer), **Rule #49** (Strict Scope), **Rule #50** (Container Width Consistency `max-w-4xl`), **Rule #51** (Copywriting Bersih, Proporsional & Anti-AI Slop), **Rule #70** (Anti-AI Slop Terminology & No Redundant `&`), **Rule #89** (Anti-Code Glorification & Eliminasi Jargon Escrow), **Rule #91** (Tombol Aksi Form Amber Primer), **Rule #97** (Lightbox Modal Interaktif), **Rule #106** (Tipografi Seragam `text-xs`).
* **Hasil Pengujian & Verifikasi**:
  * `php artisan view:clear` & `php artisan view:cache`: **PASS** (*Blade templates cached successfully*).
  * Uji rendering HTTP terautentikasi:
    - `GET /pembayaran/13` -> Status **200 OK**, me-render view `public.booking.checkout`.
    - `GET /pembayaran/13?view=invoice` -> Status **200 OK**, me-render view `public.booking.invoice`.


---

### [2026-11-17] Implementasi Logika Batas Waktu Pembayaran Dinamis & Adaptif (Dynamic Deadline ala SweetEscape / Traveloka)
* **Modul / Fitur**: Logika Batas Waktu Pembayaran Booking Klien (`calculatePaymentDeadline`).
* **File yang Dimodifikasi**:
  * `app/Models/Contract.php` (menambahkan method `calculatePaymentDeadline` dan accessor `payment_deadline_description`)
  * `app/Http/Controllers/Photographer/PhotographerBookingController.php` (menerapkan batas waktu adaptif saat fotografer menyetujui booking, notifikasi dinamis, dan pesan chat in-thread)
  * `app/Http/Controllers/Public/BookingController.php` (auto-heal `expired_at` dinamis dan konfirmasi draft adaptif)
  * `app/Http/Controllers/Public/ChatController.php` (menerapkan batas waktu dinamis saat penawaran khusus chat disetujui)
  * `resources/views/public/booking/checkout.blade.php` (tampilan batas waktu dengan penanda `(Hari Ini)` dan `(Besok)`)
* **Rincian Perubahan**:
  1. **Logika Batas Waktu Pembayaran Dinamis (Dynamic Deadline)**:
     - Menggantikan batas mati 24 jam kaku dengan sistem bertingkat sesuai sisa waktu menuju jadwal sesi foto:
       - **Sesi Normal (> 24 jam / H-2 ke atas)**: Batas bayar 24 jam (maksimal H-12 jam sebelum sesi).
       - **Sesi H-1 (12 - 24 jam ke sesi)**: Batas bayar 6 jam (maksimal H-3 jam sebelum sesi).
       - **Sesi Hari Ini / Same-Day (3 - 12 jam ke sesi)**: Batas bayar 1 jam / 60 menit (dengan buffer persiapan fotografer minimal 1.5 jam sebelum sesi).
       - **Sesi Sangat Mepet (< 3 jam ke sesi)**: Batas bayar 15 - 30 menit (maksimal H-15 menit sebelum sesi).
       - **Sesi Telah Dimulai / Terlewat**: Batas bayar kilat 15 menit.
  2. **Copywriting & Notifikasi Manusiawi**:
     - Notifikasi dan chat kini menampilkan batas waktu yang akurat (contoh: *"dalam waktu 59 menit (sebelum pukul 14:17 WIB)"*, *"hari ini, sebelum pukul 16:00 WIB"*, atau *"besok, sebelum pukul 19:00 WIB"*).
     - Mengeliminasi kode kontrak dari pesan chat pembuka fotografer ke klien, fokus pada nama sesi pemotretan.
* **Hasil Pengujian**:
  * Unit test simulasi skenario (H-5, H-1, Same-day 6 jam, Same-day 2 jam, Same-day 45 menit): **PASS (100% perhitungan akurat dan teruji)**.
  * `php artisan view:clear` & `php artisan view:cache`: **PASS**.


---

### [2026-11-17] Implementasi Pre-filled Draft Pesan Chat Pengingat Sesi (Smart Reminder Chat Prefill)
* **Modul / Fitur**: Ruang Obrolan Chat & Banner Pengingat Sesi (`/chat` & `upcoming-session-banner`).
* **File yang Dimodifikasi**:
  * `resources/views/partials/shared/upcoming-session-banner.blade.php` (menambahkan parameter `prefill` pada tautan tombol `[Chat]` dengan template teks dinamis H-1 & Hari-H)
  * `app/Http/Controllers/Public/ChatController.php` (`startChat` & `photographerStartChat` meneruskan parameter query `prefill`)
  * `resources/views/public/chat/index.blade.php` (mengikat `newMessage` dari parameter `prefill` dan auto-focus ke input)
  * `resources/views/photographer/chat/index.blade.php` (sinkronisasi prefill dan auto-focus pada chat fotografer)
* **Rincian Perubahan**:
  1. Tombol `[Chat]` pada kartu pengingat sesi H-1 dan Hari-H kini membawa pesan template kontekstual yang ditahan (*draft/hold*) di kotak input chat pengguna tanpa terkirim secara otomatis.
  2. Klien memiliki kendali penuh: teks langsung tampil di input, tombol `[Kirim]` aktif berwarna amber, kursor berada di akhir teks, dan klien dapat langsung mengirim, mengedit, atau menghapus teks tersebut.
* **Hasil Pengujian**:
  * `php artisan view:clear` & `php artisan view:cache`: **PASS**.


---

### [2026-11-17] Penambahan Tautan Google Maps Presisi pada Pesan Pembuka Reservasi Chat & Auto-Link Chat Bubbles
* **Modul / Fitur**: Pesan Otomatis Reservasi Baru & Rendering Link Interaktif pada Chat (`BookingController@store` & `chat.index`).
* **File yang Dimodifikasi**:
  * `app/Http/Controllers/Public/BookingController.php` (menambahkan tautan Google Maps `https://maps.google.com/?q={lat},{lng}` pada pesan pembuka reservasi chat dan membuang glorifikasi kode `• Kode: KTRK-` di urutan pertama)
  * `resources/views/public/chat/index.blade.php` (menambahkan helper `renderMessageContent` untuk auto-link URL interaktif dan XSS sanitization)
  * `resources/views/photographer/chat/index.blade.php` (sinkronisasi rendering link URL interaktif pada sisi fotografer)
* **Rincian Perubahan**:
  1. **Tautan Google Maps Presisi**:
     - Saat klien mengajukan reservasi, koordinat GPS (`latitude` dan `longitude`) yang dipilih klien pada peta Leaflet otomatis dibentuk menjadi tautan Google Maps: `• Petunjuk Arah: https://maps.google.com/?q={lat},{lng}`.
     - Jika koordinat tidak tersedia, tautan menggunakan query pencarian nama alamat: `https://www.google.com/maps/search/?api=1&query=...`.
  2. **Hierarki Pesan Manusiawi (Anti-AI Slop - Rule #89)**:
     - Mengeliminasi `• Kode: KTRK-XXXXX` dari baris paling atas pesan. Urutan informasi disusun alami: Layanan, Tanggal, Jam, Lokasi Acara, Petunjuk Arah (Maps), dan Catatan Khusus.
  3. **Auto-Link Interaktif pada Bubble Chat**:
     - Seluruh URL (termasuk link Google Maps) yang dikirim dalam obrolan kini otomatis terformat menjadi tautan klik (*clickable link*) dengan atribut `target="_blank"` dan `rel="noopener noreferrer"`.
     - Tautan di-escape dari karakter berbahaya terlebih dahulu untuk menjamin keamanan dari serangan XSS.
     - Fotografer maupun klien kini cukup mengetuk 1-klik tautan peta di obrolan untuk langsung membuka navigasi rute di Google Maps atau Waze.
* **Hasil Pengujian**:
  * `php artisan view:clear` & `php artisan view:cache`: **PASS**.


---

### [2026-11-17] Penataan Ulang Input Field Modal Penilaian Performa Klien Menjadi Vertikal Stack
* **Modul / Fitur**: Modal Penilaian Performa Klien Fotografer (`/photographer/bookings` - `rateClientModal`).
* **File yang Dimodifikasi**:
  * `resources/views/photographer/bookings/index.blade.php` (mengubah layout 3 aspek penilaian: Ketepatan Waktu, Komunikasi, Kerjasama & Sikap dari grid 3-kolom horizontal sempit menjadi vertical stack lapang).
* **Rincian Perubahan (Before vs After)**:
  * **Before**: Ketiga aspek penilaian diletakkan berdampingan dalam grid 3-kolom (`grid grid-cols-1 sm:grid-cols-3 gap-2.5`). Pada modal `sm:max-w-lg`, lebar per kolom sangat sempit (~120px) sehingga label `Kerjasama & Sikap *` dan opsi `5 - Sangat Ramah` terbungkus bertingkat menjadi 2-3 baris yang sesak (*cramped / awkward wrapping*).
  * **After**:
    1. Mengubah pembungkus menjadi tata letak vertikal `space-y-3`.
    2. Setiap aspek kini memiliki lebar penuh kartu (~460px) dengan padding `p-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50`.
    3. Label teks (`text-xs font-bold`) dan tombol dropdown (`py-2 px-3 text-xs sm:text-sm`) duduk nyaman dalam 1 baris utuh tanpa ada teks yang terpotong atau terbungkus.
    4. Menu dropdown interaktif Alpine.js tetap responsif dengan styling amber LensMatch dan transisi halus.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #59** (Modal Center & Buttons), **Rule #61** (Amber LensMatch), **Rule #70** (Anti-AI Slop Terminology), **Rule #102** (Anti-Narrow Void & Spacious Layout).
* **Hasil Pengujian**:
  * `php artisan view:clear` & `php artisan view:cache`: **PASS**.


---

### [2026-11-18] Sinkronisasi Label Status Booking Fotografer Menjadi 'Jadwal Terkonfirmasi'
* **Modul / Fitur**: Tabel Reservasi Fotografer (`/photographer/bookings`) & Dashboard Fotografer (`/photographer/dashboard`).
* **File yang Dimodifikasi**:
  * `resources/views/photographer/bookings/index.blade.php` (menyelaraskan teks status kontrak `active` dari `Jadwal Aktif` menjadi `Jadwal Terkonfirmasi`).
  * `resources/views/photographer/dashboard.blade.php` (menyelaraskan teks status kontrak `active` menjadi `Jadwal Terkonfirmasi`).
* **Rincian Perubahan (Before vs After)**:
  * **Before**: Status kontrak `active` pada tabel pesanan fotografer berlabel `Jadwal Aktif`. Istilah ini ambigu karena terkesan sesi foto sedang berlangsung saat ini (*live*), tidak menegaskan bahwa klien telah melunasi pembayaran, serta tidak konsisten dengan sisi klien yang menampilkan `Jadwal Terkonfirmasi`.
  * **After**:
    1. Mengubah label teks status kontrak `active` menjadi **`Jadwal Terkonfirmasi`** (`text-xs font-bold text-blue-600 dark:text-blue-400`).
    2. Konsisten 100% dengan sisi Klien ([client/bookings/index.blade.php](resources/views/client/bookings/index.blade.php)), menegaskan secara lugas bahwa pesanan telah disetujui kedua pihak, pembayaran telah lunas/masuk ke sistem, dan jadwal resmi terkunci di kalender.
    3. Mempertahankan standar tipografi murni tanpa badge/dot/divider berlebih (Rule #14, Rule #67, Rule #68, dan Rule #70).
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #14** & **Rule #68** (Pure Colored Text Status), **Rule #49** (Strict Scope), **Rule #70** (Anti-AI Slop Terminology).
* **Hasil Pengujian**:
  * `php artisan view:clear` & `php artisan view:cache`: **PASS**.


---

### [2026-11-18] Pewarnaan Angka & Kurung Dinamis pada Chip Antrean Kerja Dashboard Fotografer & Admin
* **Modul / Fitur**: Chip Antrean Kerja (*Operational Queue*) pada Dashboard Fotografer (`/photographer/dashboard`) & Dashboard Admin (`/admin/dashboard`).
* **File yang Dimodifikasi**:
  * `resources/views/photographer/dashboard.blade.php` (menyelaraskan warna kurung dan angka menjadi satu kesatuan kuning brand sistem jika bernilai > 0 pada chip antrean kerja).
  * `resources/views/admin/dashboard.blade.php` (menyelaraskan warna kurung dan angka menjadi satu kesatuan kuning brand sistem jika bernilai > 0 pada chip antrean kerja).
* **Rincian Perubahan (Before vs After)**:
  * **Before**: Hanya digit angka yang diwarnai kuning saat > 0, sedangkan kurung buka dan kurung tutup tetap hitam sehingga terlihat terpisah dan kurang seragam.
  * **After**:
    1. **Saat Nilai = 0**: Format `(0)` seluruhnya tetap menggunakan warna hitam/default netral (`text-gray-800 dark:text-gray-200`).
    2. **Saat Nilai > 0**: Format `(1)`, `(2)`, dst. secara seragam dan utuh (baik kurung buka, angka, maupun kurung tutup) diwarnai kuning brand LensMatch (`text-amber-500 dark:text-amber-400`).
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #49** (Strict Scope & Zero Unrequested Side-Effects), **Rule #61** (Amber Brand LensMatch Color Hierarchy).
* **Hasil Pengujian**:
  * `php artisan view:clear` & `php artisan view:cache`: **PASS**.


---

### [2026-11-18] Implementasi Pre-filled Draft Template Chat Kontekstual pada Kelola Booking Fotografer
* **Modul / Fitur**: Tabel Kelola Booking Fotografer (`/photographer/bookings`) & Ruang Chat (`/photographer/chat`).
* **File yang Dimodifikasi**:
  * `resources/views/photographer/bookings/index.blade.php` (menambahkan parameter `prefill` kontekstual pada tombol utama dan menu kebab `Chat Klien` untuk status `Menunggu Pembayaran Klien` dan `Menunggu Konfirmasi Klien`).
* **Rincian Perubahan (Before vs After)**:
  * **Before**: Mengklik tombol `Chat Klien` membuka ruang obrolan dengan kolom input teks kosong sehingga mitra fotografer harus mengetik pesan pengingat pembayaran atau konfirmasi foto dari awal secara manual.
  * **After**:
    1. **Saat Status `Menunggu Pembayaran Klien`**: Tombol `Chat Klien` membawa draf pesan otomatis: *"Halo Kak [Nama Klien], jadwal sesi [Layanan] pada [Tanggal] sudah saya setujui. Silakan menyelesaikan pembayaran invoice agar jadwal pemotretan resmi terkunci. Jika ada hal yang ingin didiskusikan terlebih dahulu, silakan kabari saya ya!"*.
    2. **Saat Status `Menunggu Konfirmasi Klien`**: Tombol `Chat Klien` membawa draf pesan otomatis: *"Halo Kak [Nama Klien], hasil foto untuk sesi [Layanan] sudah selesai saya unggah dan dapat diakses di menu reservasi. Silakan diperiksa ya Kak, dan jika sudah sesuai mohon bantuannya untuk mengonfirmasi penerimaan hasil foto. Terima kasih!"*.
    3. **Mekanisme Draft Hold**: Pesan ditahan di kotak input chat (*draft hold*) dengan auto-focus dan tombol kirim aktif tanpa terkirim secara otomatis, memberikan kendali 100% bagi fotografer untuk meninjau, mengedit, atau mengirim.
* **Standar / Rule README yang Dipatuhi**:
  * **Rule #49** (Strict Scope & Zero Unrequested Side-Effects), **Rule #70** (Anti-AI Slop Terminology & Natural Human Text Standard).
* **Hasil Pengujian**:
  * `php artisan view:clear` & `php artisan view:cache`: **PASS**.
