@extends('layouts.photographer')

@section('title', 'Dashboard Fotografer — Marketplace Fotografer')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 dark:text-white">Dashboard Manajerial Fotografer</h1>

<!-- Quick Metrics Grid with Standard rounded-2xl -->
<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
  <div class="p-6 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-xs space-y-2">
    <span class="text-[11px] text-gray-500 font-bold uppercase tracking-wider">Booking Masuk Bulan Ini</span>
    <p class="text-2xl font-black text-gray-900 dark:text-white">14</p>
  </div>
  <div class="p-6 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-xs space-y-2">
    <span class="text-[11px] text-gray-500 font-bold uppercase tracking-wider">Pending Validasi Kontrak</span>
    <p class="text-2xl font-black text-amber-500">3</p>
  </div>
  <div class="p-6 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-xs space-y-2">
    <span class="text-[11px] text-gray-500 font-bold uppercase tracking-wider">Total Layanan Aktif</span>
    <p class="text-2xl font-black text-amber-500">6 Paket</p>
  </div>
  <div class="p-6 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-xs space-y-2">
    <span class="text-[11px] text-gray-500 font-bold uppercase tracking-wider">Estimasi Pendapatan</span>
    <p class="text-2xl font-black text-emerald-500">Rp 24.500.000</p>
  </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
  <div class="p-6 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-xs">
    <h3 class="font-bold text-gray-800 dark:text-white mb-4">Booking Masuk Terbaru</h3>
    @include('partials.table.table-01')
  </div>
  <div>
    @include('partials.upcoming-schedule')
  </div>
</div>
@endsection
