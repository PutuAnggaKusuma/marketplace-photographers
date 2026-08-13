@extends('layouts.photographer')

@section('title', 'Paket Layanan & Tarif — Fotografer')

@section('content')
<div class="flex justify-between items-center">
  <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Kelola Paket Layanan & Service Details</h1>
  <button class="px-4 py-2 bg-brand-500 text-white rounded-lg text-sm font-semibold">+ Buat Paket Layanan</button>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
  <div class="p-5 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm space-y-3">
    <span class="px-3 py-1 text-xs font-semibold bg-brand-50 text-brand-600 rounded-full">Wedding Photography</span>
    <h3 class="text-lg font-bold text-gray-800 dark:text-white">Paket Premium Wedding</h3>
    <p class="text-sm text-gray-500">Liputan penuh 12 jam, 2 fotografer, cetak album 20x30, flashdisk 64GB.</p>
    <div class="text-xl font-extrabold text-gray-900 dark:text-white">Rp 7.500.000</div>
    <div class="pt-3 border-t border-gray-100 dark:border-gray-700 flex gap-2">
      <button class="px-3 py-1.5 bg-gray-100 dark:bg-gray-700 text-xs font-medium rounded-lg">Edit Detail</button>
      <button class="px-3 py-1.5 bg-red-50 text-red-600 text-xs font-medium rounded-lg">Hapus</button>
    </div>
  </div>
</div>
@endsection
