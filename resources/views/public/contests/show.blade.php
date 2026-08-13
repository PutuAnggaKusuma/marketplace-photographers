@extends('layouts.app')

@section('title', 'Submit Karya Lomba Foto — LensMatch')

@section('content')
<main class="max-w-4xl mx-auto px-4 py-10 space-y-8 min-h-screen">
  <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm space-y-4">
    <h1 class="text-2xl font-bold">Lomba Foto Human Interest Nusantara 2026</h1>
    <p class="text-xs text-gray-500">Batas Pengiriman: 30 Agustus 2026 | Total Hadiah: Rp 15.000.000</p>
  </div>

  <form class="bg-white dark:bg-gray-800 p-8 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm space-y-4">
    <h3 class="font-bold text-lg">Form Submit Karya Peserta</h3>
    <div>
      <label class="block text-xs font-semibold mb-1">Deskripsi Karya Foto</label>
      <textarea rows="3" placeholder="Ceritakan kisah dibalik foto..." class="w-full px-4 py-2 text-xs border rounded-xl dark:bg-gray-900 dark:border-gray-700"></textarea>
    </div>
    <div>
      <label class="block text-xs font-semibold mb-1">Upload File Foto High-Res (Max 10MB)</label>
      <input type="file" class="w-full text-xs p-2 border rounded-xl dark:bg-gray-900 dark:border-gray-700" />
    </div>
    <button type="submit" class="w-full py-3 bg-brand-500 text-white font-bold text-sm rounded-xl">Kirim Karya Peserta</button>
  </form>
</main>
@endsection
