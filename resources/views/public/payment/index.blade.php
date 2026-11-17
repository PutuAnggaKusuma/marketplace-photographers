@extends('layouts.app')

@section('title', 'Pembayaran Reservasi — LensMatch')

@section('content')
<main class="max-w-2xl mx-auto px-4 py-12 space-y-6 min-h-screen">
  @if(isset($contract))
    <div class="space-y-1 text-center">
      <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Pembayaran Reservasi Sesi Foto</h1>
      <p class="text-xs text-gray-500">Silakan periksa rincian sesi dan lanjutkan ke invoice resmi.</p>
    </div>

    <div class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-3xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-6">
      <div class="flex justify-between items-start pb-4 border-b border-gray-100 dark:border-gray-700">
        <div class="space-y-0.5">
          <h3 class="font-bold text-xs text-gray-900 dark:text-white">{{ $contract->bookingDetail?->service?->nama_layanan ?? 'Sesi Fotografi' }}</h3>
          <p class="text-xs text-gray-500">{{ $contract->photographer->nama ?? 'Mitra Fotografer' }}</p>
          <p class="text-[11px] font-mono text-gray-400">Ref: {{ $contract->contract_code ?? ('KTRK-' . str_pad($contract->id, 5, '0', STR_PAD_LEFT)) }}</p>
        </div>
        <div class="text-right">
          <span class="text-[11px] text-gray-400 block">Total Tagihan</span>
          <span class="text-xs font-black text-gray-900 dark:text-white">
            Rp {{ number_format($contract->jumlah ?? 0, 0, ',', '.') }}
          </span>
        </div>
      </div>

      <div class="p-5 bg-gray-50/70 dark:bg-gray-900/40 rounded-2xl border border-gray-200/80 dark:border-gray-700/80 text-center space-y-3">
        <p class="text-xs text-gray-600 dark:text-gray-300">Pembayaran resmi diproses secara aman melalui sistem LensMatch.</p>
        <a href="{{ route('booking.invoice', $contract->id) }}" class="inline-block px-6 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs rounded-xl shadow-xs transition">
          Buka Invoice Pembayaran
        </a>
      </div>
    </div>
  @else
    <div class="bg-white dark:bg-gray-800 p-8 sm:p-10 rounded-3xl border border-gray-200 dark:border-gray-700 text-center space-y-3 max-w-md mx-auto shadow-sm">
      <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 flex items-center justify-center mx-auto">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/></svg>
      </div>
      <h2 class="text-lg font-black text-gray-900 dark:text-white">Tidak Ada Pembayaran Aktif</h2>
      <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
        Silakan jelajahi katalog fotografer untuk memilih paket sesi pemotretan dan melakukan reservasi resmi.
      </p>
      <div class="pt-2">
        <a href="{{ route('public.photographers.katalog') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs rounded-xl shadow-xs transition">
          Jelajahi Katalog Fotografer
        </a>
      </div>
    </div>
  @endif
</main>
@endsection
