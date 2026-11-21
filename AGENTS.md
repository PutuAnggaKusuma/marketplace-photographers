# STANDAR KONSISTENSI & PROTOKOL ANTI-AI SLOP (LENSMATCH)

File ini adalah aturan wajib (**Always-On Workspace Rule**) yang secara otomatis dimuat oleh Antigravity pada setiap sesi percakapan. Dilarang keras mengabaikan atau menggeneralisasi aturan ini dengan template SaaS luar.

---

## 1. Protokol Validasi Wajib Sebelum Lapor Selesai (Zero Tolerance Checklist)
Sebelum menyelesaikan tugas atau melaporkan hasil kerja kepada pengguna, AI WAJIB menjalankan perintah validasi berikut di shell:
```powershell
php artisan view:cache ; php artisan view:clear ; php scripts/audit-ui.php ; npm run build
```
Jika ada pelanggaran aturan atau error sintaks, perbaiki SEMUA sebelum merespons pengguna.

---

## 2. Aturan Baku Kolom Aksi Tabel (Rule #69 & Rule #80)
1. **Maksimal 2 Tombol Inline Horizontal**:
   - Jika sebuah baris tabel memiliki 1 atau 2 aksi, tombol wajib diletakkan berdampingan secara horizontal (`gap-1.5`).
   - Jika sebuah baris memiliki **3 aksi atau lebih**, DILARANG KERAS menaruh 3 tombol inline sekaligus. Wajib menggunakan format:
     - **1 Tombol Utama**: `Lihat` (`bg-blue-50 text-blue-600 rounded-lg text-xs font-bold`)
     - **1 Tombol Kebab Dropdown (`...`)**: Memuat aksi sekunder (`Edit`) dan aksi destruktif (`Hapus`).
2. **Teks Bersuku Kata Tunggal Murni (Rule #80)**:
   - Wajib: `Lihat`, `Edit`, `Hapus`.
   - DILARANG: `Lihat Detail`, `Edit Data`, `Hapus Akun`, `Lihat Profil`, `Hapus Data`.
3. **Tanpa Icon SVG di Dalam Tombol Tabel / Header (Rule #10 & Rule #22)**:
   - Dilarang menyelipkan ikon SVG (seperti ikon pensil, tong sampah, panah) di dalam tombol aksi tabel maupun tombol header KECUALI diminta eksplisit oleh pengguna.

---

## 3. Aturan Tampilan Status & Tipe Akun (Rule #11, Rule #14, Rule #67 Item 4)
1. **Larangan Mutlak Bulatan / Titik Status (Dot Badge) - Rule #67 Item 4**:
   - DILARANG KERAS menggunakan `<span class="w-* h-* rounded-full bg-*"></span>` atau karakter `●` / `&bull;` di samping teks status pada kalender, tabel, badge, kartu, profil, atau elemen UI mana pun.
2. **Status Teks Murni Berwarna (*Pure Colored Text*) - Rule #14**:
   - Status wajib berupa teks murni tanpa kotak kartu/badge box/pill (`bg-*`, `border-*`, `px-* py-*`).
   - Terverifikasi / Selesai: `text-emerald-600 dark:text-emerald-400 font-bold text-xs`
   - Belum Verifikasi / Pending: `text-amber-600 dark:text-amber-400 font-bold text-xs`
   - Dibekukan / Ditolak / Dibatalkan: `text-rose-600 dark:text-rose-400 font-bold text-xs`
3. **Tipe Akun / Role Teks Murni Berwarna - Rule #11**:
   - Dilarang membungkus role ke dalam kartu/badge box/pill.
   - Super Admin: `text-amber-600 dark:text-amber-400 font-extrabold text-xs`
   - Staf Admin: `text-blue-600 dark:text-blue-400 font-bold text-xs`
   - Fotografer: `text-purple-600 dark:text-purple-400 font-bold text-xs`
   - Klien: `text-emerald-600 dark:text-emerald-400 font-bold text-xs`

---

## 4. Aturan Tombol Formulir (Rule #53 & Rule #76)
1. Form Tambah Data Baru (`create`/`store`): Tombol submit wajib bertuliskan **`Simpan Data`**.
2. Form Ubah/Edit Data (`edit`/`update`): Tombol submit wajib bertuliskan **`Perbaharui Data`**.
3. Tata Letak Tombol Formulir (Rule #74): Tombol `Batal` WAJIB berdampingan di sebelah kiri tombol submit utama dengan penyelarasan rata kanan (`justify-end gap-3`). Dilarang memakai `justify-between`.

---

## 5. Aturan Breadcrumb & Kartu Metrik (Rule #62 & Rule #64)
1. **Breadcrumb Global (Rule #62)**: Seluruh teks breadcrumb (termasuk halaman aktif terakhir) wajib menggunakan warna abu-abu netral seragam: `text-gray-400 dark:text-gray-500 font-bold`. DILARANG menggunakan highlight hitam/putih pekat (`text-gray-900`) atau warna aksen lainnya.
2. **Kartu Ringkasan Metrik KPI (Rule #64 & Rule #7)**:
   - Terdiri dari 2 baris murni berukuran seragam `text-xs`: Label judul (`text-xs font-bold text-gray-500 uppercase`) dan angka nilai (`text-xs font-black text-gray-900`).
   - Dilarang menambahkan baris teks ketiga atau menduplikasi metrik data antar kartu.
