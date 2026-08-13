@extends('layouts.app')

@section('title', 'Forum Diskusi Komunitas — LensMatch')

@section('content')
<main class="max-w-7xl mx-auto px-4 py-10 space-y-8 min-h-screen">
  <div class="flex justify-between items-center">
    <div>
      <h1 class="text-3xl font-extrabold">Forum Komunitas Fotografi</h1>
      <p class="text-xs text-gray-500">Diskusi seputar kamera, lighting, teknik editing, dan pengadaan proyek.</p>
    </div>
    <button class="px-4 py-2 bg-brand-500 text-white text-xs font-bold rounded-xl">+ Buat Thread Diskusi</button>
  </div>

  <div class="space-y-4">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm space-y-3">
      <div class="flex justify-between text-xs text-gray-500">
        <span>Posted by @rizky_photo • 2 jam lalu</span>
        <span>💬 18 Komentar</span>
      </div>
      <h3 class="text-lg font-bold">
        <a href="{{ url('/forum/1') }}" class="hover:text-brand-500">Rekomendasi Lensa Fix untuk Prewedding Outoor di Siang Terik?</a>
      </h3>
      <p class="text-xs text-gray-600 dark:text-gray-300">Halo teman-teman fotografer, ada saran lensa 85mm atau 50mm f/1.4 yang kontrasnya tidak terlalu flare saat outdoor?</p>
    </div>
  </div>
</main>
@endsection
