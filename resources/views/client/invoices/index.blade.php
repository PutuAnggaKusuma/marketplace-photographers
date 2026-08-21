@extends('layouts.app')

@section('title', 'Daftar Invoice & Tagihan Saya — LensMatch')

@section('content')
<section class="py-12 bg-gray-50/50 dark:bg-gray-900/50 min-h-screen">
  <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

    <div class="space-y-1">
      <span class="px-3.5 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300">
        Manajemen Pembayaran & Escrow
      </span>
      <h1 class="text-3xl font-black text-gray-900 dark:text-white pt-1">Invoice & Pembayaran Saya</h1>
      <p class="text-xs text-gray-500">Daftar seluruh tagihan pembayaran resmi LensMatch Escrow.</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
      <div class="divide-y divide-gray-100 dark:divide-gray-700">
        @forelse($contracts as $c)
          <div class="p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 hover:bg-gray-50/60 dark:hover:bg-gray-900/40 transition">
            <div class="space-y-1">
              <span class="font-mono text-xs font-black text-amber-600 dark:text-amber-400">
                {{ $c->payments->first()->external_id ?? 'INV-20260815-XXXX' }}
              </span>
              <h3 class="text-sm font-extrabold text-gray-900 dark:text-white">{{ $c->bookingDetail->service->nama_layanan ?? 'Sesi Foto' }}</h3>
              <p class="text-xs text-gray-500">Studio: {{ $c->photographer->nama }} • Dibuat {{ $c->created_at->format('d M Y') }}</p>
            </div>

            <div class="flex items-center gap-4 shrink-0">
              <div class="text-right">
                <span class="text-sm font-black text-gray-900 dark:text-white block">Rp {{ number_format($c->jumlah, 0, ',', '.') }}</span>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300">
                  {{ strtoupper($c->payments->first()->payment_status ?? 'pending') }}
                </span>
              </div>

              <a href="{{ url('/pembayaran/' . $c->id) }}" class="px-4 py-2 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs rounded-xl shadow-xs transition">
                Rincian Tagihan →
              </a>
            </div>
          </div>
        @empty
          <div class="p-8 text-center text-xs text-gray-500">Belum ada daftar invoice pembayaran.</div>
        @endforelse
      </div>
    </div>

  </div>
</section>
@endsection