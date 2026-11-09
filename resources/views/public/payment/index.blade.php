@extends('layouts.app')

@section('title', 'Halaman Pembayaran — LensMatch')

@section('content')
<main class="max-w-3xl mx-auto px-4 py-12 space-y-8 min-h-screen">
  @if(isset($contract))
    <div class="space-y-2 text-center">
      <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white">Selesaikan Pembayaran Kontrak</h1>
      <p class="text-xs text-gray-500">Nomor Kontrak: {{ $contract->contract_code ?? ('KTRK-' . str_pad($contract->id, 5, '0', STR_PAD_LEFT)) }}</p>
    </div>

    <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-xl space-y-6">
      <div class="flex justify-between items-center pb-4 border-b border-gray-200 dark:border-gray-700">
        <div>
          <h3 class="font-bold text-sm text-gray-900 dark:text-white">{{ $contract->bookingDetail->service->nama_layanan ?? 'Pembayaran Kontrak Booking' }}</h3>
          <p class="text-xs text-gray-500">{{ $contract->photographer->nama ?? 'Mitra Fotografer' }}</p>
        </div>
        <div class="text-2xl font-extrabold text-amber-500">
          Rp {{ number_format($contract->jumlah ?? 0, 0, ',', '.') }}
        </div>
      </div>

      <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-xl text-center space-y-4">
        <p class="text-xs text-gray-600 dark:text-gray-300">Pembayaran via Midtrans Snap / Rekening Bersama Escrow</p>
        <a href="{{ route('booking.invoice', $contract->id) }}" class="inline-block px-8 py-3 bg-amber-400 hover:bg-amber-500 text-gray-900 font-bold text-sm rounded-xl shadow-md transition">
          Buka Invoice Pembayaran
        </a>
      </div>
    </div>
  @else
    <div class="bg-white dark:bg-gray-800 p-12 rounded-3xl border border-gray-200 dark:border-gray-700 text-center space-y-4 max-w-lg mx-auto shadow-sm">
      <div class="w-16 h-16 rounded-2xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 flex items-center justify-center mx-auto text-2xl font-black">
        💳
      </div>
      <h2 class="text-xl font-black text-gray-900 dark:text-white">Tidak Ada Pembayaran Aktif</h2>
      <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
        Silakan jelajahi katalog fotografer untuk memilih paket sesi pemotretan dan melakukan reservasi resmi.
      </p>
      <div class="pt-2">
        <a href="{{ route('public.photographers.katalog') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-950 font-bold text-xs rounded-xl shadow-xs transition">
          Jelajahi Katalog Fotografer
        </a>
      </div>
    </div>
  @endif
</main>
@endsection
