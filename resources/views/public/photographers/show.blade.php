@extends('layouts.app')

@section('title', 'Alex Visual Studio — Detail Profil Fotografer')

@section('content')
<main class="max-w-7xl mx-auto px-4 py-10 space-y-10 min-h-screen">
  <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-xs flex flex-col md:flex-row gap-6 items-center">
    <img src="{{ asset('images/user/owner.jpg') }}" class="w-32 h-32 rounded-full object-cover border-4 border-amber-400" alt="Fotografer" />
    <div class="space-y-2 text-center md:text-left flex-1">
      <div class="flex flex-wrap items-center justify-center md:justify-start gap-2">
        <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white">Alex Visual Studio</h1>
        <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-bold flex items-center gap-1">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
          Terverifikasi
        </span>
      </div>
      <p class="text-sm text-gray-500">Fotografer Spesialis Wedding & Commercial Portrait (Pengalaman 8+ Tahun)</p>
      <div class="flex gap-4 text-xs font-semibold text-gray-600 dark:text-gray-400 justify-center md:justify-start items-center">
        <span class="flex items-center gap-1">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
          Jakarta Selatan
        </span>
        <span class="flex items-center gap-1">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" class="text-amber-500"><path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/></svg>
          4.9 (42 Ulasan)
        </span>
      </div>
    </div>

    <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
      <a href="{{ url('/chat') }}" class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-white rounded-xl text-xs font-bold text-center flex items-center justify-center gap-2">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
        Chat Sekarang
      </a>
      <a href="{{ url('/booking/create') }}" class="px-6 py-3 bg-amber-400 hover:bg-amber-500 text-gray-900 rounded-xl text-xs font-bold text-center transition flex items-center justify-center gap-2">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
        Buat Booking & Kontrak
      </a>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2 space-y-8">
      <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-xs space-y-4">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Galeri Portofolio</h3>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
          <div class="h-36 bg-gray-200 rounded-xl overflow-hidden">
            <img src="{{ asset('images/user/owner.jpg') }}" class="w-full h-full object-cover" />
          </div>
        </div>
      </div>
    </div>

    <div class="space-y-6">
      <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-xs space-y-4">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Pilihan Paket Layanan</h3>
        <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-700 space-y-2">
          <h4 class="font-bold text-sm text-gray-900 dark:text-white">Paket Full-Day Wedding</h4>
          <p class="text-xs text-gray-500">12 Jam Liputan, 2 Fotografer, Album Cetak Exclusif.</p>
          <div class="text-amber-500 font-extrabold text-lg">Rp 7.500.000</div>
          <a href="{{ url('/booking/create?service=1') }}" class="block text-center py-2.5 bg-[#222222] text-white hover:bg-amber-400 hover:text-gray-900 rounded-xl text-xs font-semibold transition">Pilih Paket Ini</a>
        </div>
      </div>
    </div>
  </div>
</main>
@endsection
