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
