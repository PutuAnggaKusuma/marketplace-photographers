@extends('layouts.app')

@section('title', 'Detail Thread Forum — LensMatch')

@section('content')
<main class="max-w-4xl mx-auto px-4 py-10 space-y-8 min-h-screen">
  <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm space-y-4">
    <div class="flex items-center gap-3 text-xs text-gray-500">
      <span class="font-bold text-gray-800 dark:text-white">@rizky_photo</span>
      <span>• 2 jam lalu</span>
    </div>
    <h1 class="text-2xl font-bold">Rekomendasi Lensa Fix untuk Prewedding Outoor di Siang Terik?</h1>
    <p class="text-sm leading-relaxed text-gray-700 dark:text-gray-300">
      Halo teman-teman fotografer, ada saran lensa 85mm atau 50mm f/1.4 yang kontrasnya tidak terlalu flare saat outdoor di siang bolong? Mohon pengalamannya ya.
    </p>
  </div>

  <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm space-y-6">
    <h3 class="font-bold text-lg">Komentar (18)</h3>
    <div class="space-y-4 text-xs">
      <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl space-y-1">
        <span class="font-bold">@alex_studio:</span>
        <p>Saran pakai 85mm f/1.8 + ND Filter CPL mas, bokeh tetep dapet flare aman!</p>
      </div>
    </div>
  </div>
</main>
@endsection
