@extends('layouts.app')

@section('title', 'Cari Fotografer — LensMatch')

@section('content')
<main class="max-w-7xl mx-auto px-4 py-10 space-y-8 min-h-screen">
  <div class="space-y-2">
    <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white">Pencarian & Katalog Fotografer</h1>
    <p class="text-sm text-gray-500">Filter fotografer berdasarkan lokasi, tarif harga, dan spesialisasi kategori.</p>
  </div>

  <div class="p-4 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-xs flex flex-col md:flex-row gap-4">
    <input type="text" placeholder="Cari nama fotografer atau kota..." class="w-full px-4 py-2.5 text-xs rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900" />
    <select class="px-4 py-2.5 text-xs rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900">
      <option value="">Semua Kategori</option>
      <option value="wedding">Wedding</option>
      <option value="event">Event</option>
      <option value="portrait">Portrait</option>
    </select>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden shadow-xs hover:shadow-md transition">
      <div class="h-48 bg-gray-200 dark:bg-gray-700 relative">
        <img src="{{ asset('images/user/user-01.jpg') }}" class="w-full h-full object-cover" alt="Fotografer" />
      </div>
      <div class="p-5 space-y-3">
        <h3 class="font-bold text-lg text-gray-900 dark:text-white">Alex Visual Studio</h3>
        <p class="text-xs text-gray-500">Spesialis Wedding & Prewedding Studio — Jakarta</p>
        <div class="text-amber-500 font-extrabold text-lg">Mulai Rp 3.500.000</div>
        <a href="{{ url('/fotografer/1') }}" class="block text-center py-2.5 bg-[#222222] text-white hover:bg-amber-400 hover:text-gray-900 rounded-xl text-xs font-semibold transition">Lihat Detail & Booking</a>
      </div>
    </div>
  </div>
</main>
@endsection
