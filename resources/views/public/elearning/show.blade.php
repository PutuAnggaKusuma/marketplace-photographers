@extends('layouts.app')

@section('title', 'Materi E-Learning — LensMatch')

@section('content')
<main class="max-w-4xl mx-auto px-4 py-10 space-y-8 min-h-screen">
  <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm space-y-4">
    <h1 class="text-2xl font-bold">Mastering Exposure Triangle: Shutter, Aperture, ISO</h1>

    <div class="aspect-video bg-black rounded-xl overflow-hidden flex items-center justify-center text-white text-sm">
      <span>[Video Player Media E-Learning]</span>
    </div>

    <div class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
      <p>Exposure Triangle adalah dasar utama dari ilmu fotografi modern. Ketiga elemen ini saling melengkapi satu sama lain...</p>
    </div>

    <button class="px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs rounded-xl">✓ Tandai Materi Selesai</button>
  </div>
</main>
@endsection
