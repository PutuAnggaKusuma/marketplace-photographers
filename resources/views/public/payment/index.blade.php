@extends('layouts.app')

@section('title', 'Halaman Pembayaran — LensMatch')

@section('content')
<main class="max-w-3xl mx-auto px-4 py-12 space-y-8 min-h-screen">
  <div class="space-y-2 text-center">
    <h1 class="text-3xl font-extrabold">Selesaikan Pembayaran Kontrak</h1>
    <p class="text-xs text-gray-500">Nomor Kontrak: #CTR-2026-08129</p>
  </div>

  <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-xl space-y-6">
    <div class="flex justify-between items-center pb-4 border-b border-gray-200 dark:border-gray-700">
      <div>
        <h3 class="font-bold text-sm">Pembayaran DP Kontrak Booking</h3>
        <p class="text-xs text-gray-500">Alex Visual Studio — Wedding Photography</p>
      </div>
      <div class="text-2xl font-extrabold text-brand-500">Rp 2.250.000</div>
    </div>

    <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-xl text-center space-y-4">
      <p class="text-xs text-gray-600 dark:text-gray-300">Pembayaran via Midtrans Snap / Transfer Manual</p>
      <button class="px-8 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-sm rounded-xl shadow-md transition">
        Bayar via Midtrans Snap
      </button>
    </div>
  </div>
</main>
@endsection
