@extends('layouts.app')

@section('title', 'Kebijakan Privasi & Syarat Ketentuan - LensMatch')

@section('content')
<section class="py-12 bg-white dark:bg-gray-900/50 min-h-screen">
  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

    <!-- Header -->
    <div class="space-y-2 text-center">
      <span class="px-3.5 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-amber-400 text-gray-900">
        Dokumen Legal Resmi
      </span>
      <h1 class="text-3xl sm:text-4xl font-black text-gray-900 dark:text-white tracking-tight">Kebijakan Privasi & Syarat Ketentuan</h1>
      <p class="text-xs text-gray-500 dark:text-gray-400">Terakhir diperbarui: 15 Agustus 2026</p>
    </div>

    <!-- Main Legal Content -->
    <div class="bg-white dark:bg-gray-800 p-8 sm:p-12 rounded-3xl border border-gray-200 dark:border-gray-700 shadow-sm space-y-8 text-xs sm:text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
      
      <div class="space-y-3">
        <h2 class="text-base font-extrabold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">1. Pendahuluan</h2>
        <p>LensMatch berkomitmen penuh untuk melindungi privasi dan keamanan data pribadi pengguna platform kami. Kebijakan ini menjelaskan bagaimana data Anda dikumpulkan, disimpan, dan dilindungi saat menggunakan layanan marketplace fotografi LensMatch.</p>
      </div>

      <div class="space-y-3">
        <h2 class="text-base font-extrabold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">2. Sistem Rekening Bersama (Escrow Guarantee)</h2>
        <p>Seluruh transaksi pemesanan jasa fotografi wajib melalui sistem Rekening Bersama Escrow LensMatch. Dana pembayaran disimpan dengan aman di sistem platform dan hanya akan dicairkan ke rekening fotografer setelah sesi pemotretan serta penyerahan file foto diselesaikan secara sah.</p>
      </div>

      <div class="space-y-3">
        <h2 class="text-base font-extrabold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">3. Hak Cipta Karya & Portofolio Fotografi</h2>
        <p>Hak cipta intelektual (*intellectual property rights*) foto hasil pemotretan tetap menjadi milikFotografer, sementara Klien mendapatkan lisensi penuh untuk penggunaan pribadi maupun komersial sesuai kesepakatan kontrak booking.</p>
      </div>

      <div class="space-y-3">
        <h2 class="text-base font-extrabold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">4. Perlindungan Data Pribadi</h2>
        <p>Data pribadi seperti alamat email, nomor telepon, dan lokasi tidak akan pernah dijual atau disebarluaskan kepada pihak ketiga di luar keperluan transaksi platform LensMatch.</p>
      </div>

    </div>

  </div>
</section>
@endsection