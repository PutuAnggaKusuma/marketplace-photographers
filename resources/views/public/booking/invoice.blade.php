@extends('layouts.app')

@section('title', 'Invoice Pembayaran Booking #' . $payment->external_id . ' — LensMatch')

@section('content')
<section class="py-16 sm:py-20 lg:py-24 bg-white dark:bg-gray-900 min-h-screen">
  <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10" x-data="{ showModal: false }">

    <!-- Flash Alert Success -->
    @if(session('success'))
      <div class="p-5 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 text-xs sm:text-sm text-emerald-800 dark:text-emerald-300 font-bold flex items-center gap-3">
        <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
        <span>{{ session('success') }}</span>
      </div>
    @endif

    <!-- Invoice Header Card -->
    <div class="bg-white dark:bg-gray-800 p-8 sm:p-10 lg:p-12 rounded-3xl border border-gray-100 dark:border-gray-700/80 shadow-xs space-y-8">
      
      <!-- Top Invoice Row -->
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 border-b border-gray-100 dark:border-gray-700 pb-8">
        <div class="space-y-1">
          <span class="text-[11px] uppercase font-black tracking-widest text-amber-600 dark:text-amber-400">Tagihan Resmi LensMatch Escrow</span>
          <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white mt-1">{{ $payment->external_id }}</h1>
          <p class="text-xs sm:text-sm text-gray-500">Dibuat pada: {{ $contract->created_at->format('d M Y, H:i') }} WIB</p>
        </div>

        <div class="text-left sm:text-right space-y-2">
          <span class="px-4 py-2 rounded-full text-xs font-black uppercase tracking-wider bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300 inline-flex items-center gap-2">
            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>Menunggu Pembayaran</span>
          </span>
          <p class="text-xs text-gray-400">Batas waktu: {{ \Carbon\Carbon::parse($contract->expired_at)->format('d M Y, H:i') }} WIB</p>
        </div>
      </div>

      <!-- Photographer & Client Info Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 text-xs sm:text-sm border-b border-gray-100 dark:border-gray-700 pb-8">
        <div class="space-y-1.5 p-5 rounded-2xl bg-gray-50/70 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-700/60">
          <span class="font-black text-gray-400 uppercase text-[10px] tracking-wider block mb-1">Fotografer:</span>
          <h3 class="font-black text-base text-gray-900 dark:text-white">{{ $contract->photographer->nama }}</h3>
          <p class="text-gray-500 flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-amber-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>  {{ $contract->photographer->city->name ?? $contract->photographer->alamat }}</p>
          <p class="text-gray-500">{{ $contract->photographer->nomor_telepon }}</p>
        </div>

        <div class="space-y-1.5 p-5 rounded-2xl bg-gray-50/70 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-700/60">
          <span class="font-black text-gray-400 uppercase text-[10px] tracking-wider block mb-1">Pemesan / Klien:</span>
          <h3 class="font-black text-base text-gray-900 dark:text-white">{{ $contract->client->nama }}</h3>
          <p class="text-gray-500">Email: {{ $contract->client->user->email ?? auth()->user()->email }}</p>
          <p class="text-gray-500">Lokasi: {{ $contract->bookingDetail->lokasi }}</p>
        </div>
      </div>

      <!-- Booking Details Summary -->
      <div class="space-y-4">
        <h4 class="text-xs sm:text-sm font-black text-gray-900 dark:text-white uppercase tracking-wider">Rincian Sesi Foto</h4>

        <div class="bg-gray-50/70 dark:bg-gray-900/60 p-6 rounded-2xl space-y-3 text-xs sm:text-sm border border-gray-100 dark:border-gray-700/60">
          <div class="flex justify-between">
            <span class="text-gray-500 font-medium">Paket Layanan</span>
            <span class="font-bold text-gray-900 dark:text-white">{{ $contract->bookingDetail->service->nama_layanan }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-500 font-medium">Tanggal Pelaksanaan</span>
            <span class="font-bold text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($contract->bookingDetail->booking_date)->format('d F Y') }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-500 font-medium">Jam Sesi</span>
            <span class="font-bold text-gray-900 dark:text-white">{{ $contract->bookingDetail->jam_mulai }} WIB</span>
          </div>
        </div>
      </div>

      <!-- Total Price Banner -->
      <div class="p-8 rounded-3xl bg-amber-400 text-gray-900 flex flex-col sm:flex-row items-center justify-between gap-6 shadow-lg shadow-amber-400/20">
        <div>
          <span class="text-xs font-black uppercase tracking-wider block opacity-80 mb-1">Total Tagihan Yang Harus Dibayar</span>
          <span class="text-3xl sm:text-4xl font-black">Rp {{ number_format($contract->jumlah, 0, ',', '.') }}</span>
        </div>

        <button type="button" @click="showModal = true" class="w-full sm:w-auto px-8 py-4 bg-gray-900 hover:bg-gray-800 text-white font-black text-xs sm:text-sm rounded-2xl transition shadow-lg shrink-0 flex items-center justify-center gap-2.5 cursor-pointer">
          <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
          <span>Bayar Sekarang (Simulasi QRIS/Transfer)</span>
        </button>
      </div>

    </div>

    <!-- Modal Simulasi Bayar -->
    <div x-show="showModal" @click.away="showModal = false" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-white dark:bg-gray-800 max-w-md w-full p-8 rounded-3xl border border-gray-200 dark:border-gray-700 shadow-2xl space-y-6 text-center">
        <div class="space-y-2">
          <div class="w-14 h-14 rounded-2xl bg-amber-100 dark:bg-amber-900/60 text-amber-600 dark:text-amber-400 mx-auto flex items-center justify-center">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
          </div>
          <h3 class="text-xl font-black text-gray-900 dark:text-white">Simulasi Instan QRIS & Transfer Bank</h3>
          <p class="text-xs sm:text-sm text-gray-500">Silakan gunakan kode QRIS dummy atau transfer ke rekening penampungan resmi LensMatch Escrow.</p>
        </div>

        <div class="p-5 bg-gray-50 dark:bg-gray-900/80 border border-gray-100 dark:border-gray-700 rounded-2xl space-y-2 text-xs sm:text-sm">
          <p class="font-extrabold text-gray-800 dark:text-gray-200">Bank Central Asia (BCA)</p>
          <p class="text-lg font-black text-amber-600 dark:text-amber-400">8840-1928-3746</p>
          <p class="text-xs text-gray-500">a.n. PT LensMatch Indonesia Escrow</p>
        </div>

        <div class="space-y-3 pt-2">
          <button type="button" @click="showModal = false" class="w-full py-3.5 bg-amber-400 hover:bg-amber-500 text-gray-900 font-black text-xs sm:text-sm rounded-xl shadow-md transition cursor-pointer">
            Konfirmasi Pembayaran Selesai
          </button>
          <button type="button" @click="showModal = false" class="w-full py-2 text-xs sm:text-sm font-bold text-gray-500 hover:text-gray-800 dark:hover:text-gray-300 cursor-pointer">
            Tutup
          </button>
        </div>
      </div>
    </div>

  </div>
</section>
@endsection