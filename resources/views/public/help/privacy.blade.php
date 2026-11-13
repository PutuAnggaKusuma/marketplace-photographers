@extends('layouts.app')

@section('title', 'Kebijakan Privasi — LensMatch')

@section('content')
<section class="pt-10 pb-16 lg:pt-12 lg:pb-24 bg-white dark:bg-gray-900 min-h-screen">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 lg:space-y-10">

    <!-- Header Section -->
    <div class="space-y-2">
      <h1 class="text-3xl sm:text-4xl lg:text-[40px] font-black text-gray-900 dark:text-white tracking-tight leading-tight">
        Kebijakan Privasi
      </h1>
      <p class="text-sm text-gray-500 dark:text-gray-400 max-w-2xl leading-relaxed">
        Ketentuan pengumpulan, penyimpanan, dan perlindungan data pengguna di platform LensMatch.
      </p>
    </div>

    <!-- Main Legal Content -->
    <div class="bg-white dark:bg-gray-800 p-6 sm:p-8 lg:p-10 rounded-2xl border border-gray-200/80 dark:border-gray-700/80 space-y-6 text-xs sm:text-sm text-gray-600 dark:text-gray-300 leading-relaxed">

      <div class="space-y-2">
        <h2 class="text-base sm:text-lg font-extrabold text-gray-900 dark:text-white">1. Data yang Dikumpulkan</h2>
        <p>Sistem mencatat data identitas akun (nama, alamat email, peran pengguna), data profil (nomor telepon, wilayah domisili, biodata, foto profil), serta informasi layanan, portofolio, dan jadwal kerja yang Anda isi pada platform.</p>
      </div>

      <div class="space-y-2">
        <h2 class="text-base sm:text-lg font-extrabold text-gray-900 dark:text-white">2. Penggunaan Data</h2>
        <p>Data digunakan untuk memproses pendaftaran akun, verifikasi mitra fotografer oleh Admin, pengelolaan reservasi pemotretan, penerbitan tagihan pembayaran, komunikasi obrolan antara Klien dan Fotografer, serta pengiriman notifikasi transaksi.</p>
      </div>

      <div class="space-y-2">
        <h2 class="text-base sm:text-lg font-extrabold text-gray-900 dark:text-white">3. Keamanan Akun</h2>
        <p>Setiap alamat email hanya berlaku untuk satu akun dan satu peran. Data sensitif seperti kata sandi tersimpan dalam bentuk terenkripsi, sedangkan data kontak maupun rekening bank tidak dipublikasikan atau dibagikan ke pihak di luar keperluan operasional transaksi.</p>
      </div>

      <div class="space-y-2">
        <h2 class="text-base sm:text-lg font-extrabold text-gray-900 dark:text-white">4. Arsip Transaksi Pembayaran</h2>
        <p>Rincian reservasi, invoice, bukti transfer pembayaran, dan konfirmasi penerimaan hasil foto disimpan secara terstruktur sebagai arsip transaksi resmi antara Klien, Fotografer, dan pengelola platform.</p>
      </div>

    </div>

  </div>
</section>
@endsection
