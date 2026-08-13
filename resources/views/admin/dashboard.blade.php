@extends('layouts.admin')

@section('title', 'Admin Dashboard — Marketplace Fotografer')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 dark:text-white">Admin Dashboard</h1>

<!-- Quick Metrics Grid with Standard rounded-2xl -->
<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
  <div class="p-6 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-xs space-y-2">
    <span class="text-[11px] text-gray-500 font-bold uppercase tracking-wider">Total User Registrasi</span>
    <p class="text-2xl font-black text-gray-900 dark:text-white">1,248</p>
  </div>
  <div class="p-6 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-xs space-y-2">
    <span class="text-[11px] text-gray-500 font-bold uppercase tracking-wider">Fotografer Terverifikasi</span>
    <p class="text-2xl font-black text-amber-500">342</p>
  </div>
  <div class="p-6 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-xs space-y-2">
    <span class="text-[11px] text-gray-500 font-bold uppercase tracking-wider">Total Kontrak Booking</span>
    <p class="text-2xl font-black text-emerald-500">895</p>
  </div>
  <div class="p-6 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-xs space-y-2">
    <span class="text-[11px] text-gray-500 font-bold uppercase tracking-wider">Total Omzet Transaksi</span>
    <p class="text-2xl font-black text-purple-600">Rp 485.000.000</p>
  </div>
</div>

<div class="p-6 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-xs">
  <h3 class="font-bold text-gray-800 dark:text-white mb-4">Verifikasi Fotografer Pending & Booking Terbaru</h3>
  @include('partials.table.table-01')
</div>
@endsection
