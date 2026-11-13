@extends('layouts.app')

@section('title', 'Syarat Ketentuan — LensMatch')

@section('content')
<section class="pt-10 pb-16 lg:pt-12 lg:pb-24 bg-white dark:bg-gray-900 min-h-screen">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 lg:space-y-10">

    <!-- Header Section -->
    <div class="space-y-2">
      <h1 class="text-3xl sm:text-4xl lg:text-[40px] font-black text-gray-900 dark:text-white tracking-tight leading-tight">
        Syarat Ketentuan
      </h1>
      <p class="text-sm text-gray-500 dark:text-gray-400 max-w-2xl leading-relaxed">
        Aturan penggunaan platform, reservasi sesi foto, pembayaran, pembatalan, dan hak cipta karya di LensMatch.
      </p>
    </div>

    <!-- Main Legal Content -->
    <div class="bg-white dark:bg-gray-800 p-6 sm:p-8 lg:p-10 rounded-2xl border border-gray-200/80 dark:border-gray-700/80 space-y-6 text-xs sm:text-sm text-gray-600 dark:text-gray-300 leading-relaxed">

      <div class="space-y-2">
        <h2 class="text-base sm:text-lg font-extrabold text-gray-900 dark:text-white">1. Akun Pengguna</h2>
        <p>Satu alamat email hanya dapat digunakan untuk satu akun dengan satu peran (Klien atau Fotografer). Mitra fotografer wajib melengkapi profil serta melewati verifikasi dan peninjauan etalase Admin sebelum layanan tayang di katalog publik.</p>
      </div>

      <div class="space-y-2">
        <h2 class="text-base sm:text-lg font-extrabold text-gray-900 dark:text-white">2. Reservasi Sesi</h2>
        <p>Pemesanan hanya dapat diajukan pada hari kerja, jam operasional, dan tanggal yang tersedia sesuai jadwal fotografer. Kontrak reservasi berlaku setelah pembayaran diverifikasi dan fotografer menyetujui pesanan.</p>
      </div>

      <div class="space-y-2">
        <h2 class="text-base sm:text-lg font-extrabold text-gray-900 dark:text-white">3. Pembayaran dan Pencairan Dana</h2>
        <p>Klien dapat memilih Pembayaran Penuh atau Uang Muka (DP 30%) sesuai tagihan resmi ke rekening PT LensMatch Indonesia. Dana ditahan sistem selama sesi berlangsung dan diteruskan ke fotografer setelah dikurangi biaya platform 10% saat klien mengonfirmasi penerimaan hasil foto.</p>
      </div>

      <div class="space-y-2">
        <h2 class="text-base sm:text-lg font-extrabold text-gray-900 dark:text-white">4. Pembatalan dan Jadwal Ulang</h2>
        <p>Pembatalan mandiri oleh klien dapat diajukan maksimal 24 jam sebelum jadwal pemotretan. Klien maupun fotografer dapat mengajukan perubahan jadwal ke slot tersedia, yang berlaku setelah disetujui kedua pihak.</p>
      </div>

      <div class="space-y-2">
        <h2 class="text-base sm:text-lg font-extrabold text-gray-900 dark:text-white">5. Hak Cipta Karya</h2>
        <p>Hak cipta foto hasil pemotretan tetap menjadi milik fotografer, sementara klien memperoleh hak penggunaan hasil foto sesuai paket layanan yang disepakati. Karya portofolio yang diunggah ke platform dilindungi penandaan hak cipta sistem.</p>
      </div>

      <div class="space-y-2">
        <h2 class="text-base sm:text-lg font-extrabold text-gray-900 dark:text-white">6. Komunitas Forum dan Lomba</h2>
        <p>Topik diskusi forum, komentar, serta karya peserta lomba foto wajib menjaga etika komunitas dan dapat dimoderasi atau disembunyikan oleh Admin apabila melanggar ketentuan.</p>
      </div>

    </div>

  </div>
</section>
@endsection

