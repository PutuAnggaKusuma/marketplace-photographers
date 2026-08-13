@extends('layouts.app')

@section('title', 'Lomba Foto Komunitas — LensMatch')

@section('content')
<main class="max-w-7xl mx-auto px-4 py-10 space-y-8 min-h-screen">
  <div class="space-y-2">
    <h1 class="text-3xl font-extrabold">Kompetisi & Lomba Fotografi</h1>
    <p class="text-xs text-gray-500">Ikuti kompetisi foto berhadiah jutaan rupiah & dapatkan sertifikat resmi.</p>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm space-y-4 p-6">
      <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-xs font-bold">Prize Pool Rp 15.000.000</span>
      <h3 class="text-xl font-bold">Lomba Foto Human Interest Nusantara 2026</h3>
      <p class="text-xs text-gray-500">Penyelenggara: Admin LensMatch • Deadline: 30 Agustus 2026</p>
      <a href="{{ url('/lomba/1') }}" class="block text-center py-2.5 bg-brand-500 text-white rounded-xl text-xs font-bold">Lihat Detail & Submit Karya</a>
    </div>
  </div>
</main>
@endsection
