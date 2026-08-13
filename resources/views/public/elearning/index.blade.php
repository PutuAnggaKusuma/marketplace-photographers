@extends('layouts.app')

@section('title', 'Modul E-Learning Fotografi — LensMatch')

@section('content')
<main class="max-w-7xl mx-auto px-4 py-10 space-y-8 min-h-screen">
  <div class="space-y-2">
    <h1 class="text-3xl font-extrabold">Akademi E-Learning Fotografi</h1>
    <p class="text-xs text-gray-500">Pelajari teknik fotografi, manajemen pencahayaan, dan bisnis freelance dari profesional.</p>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm p-6 space-y-3">
      <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-bold">Modul Dasar</span>
      <h3 class="font-bold text-lg">Mastering Exposure Triangle: Shutter, Aperture, ISO</h3>
      <p class="text-xs text-gray-500">Kombinasi teks, gambar, dan video tutorial interaktif.</p>
      <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
        <div class="bg-brand-500 h-2 rounded-full" style="width: 60%"></div>
      </div>
      <div class="flex justify-between text-xs text-gray-500">
        <span>Progress Belajar</span>
        <span class="font-bold">60% Selesai</span>
      </div>
      <a href="{{ url('/e-learning/1') }}" class="block text-center py-2.5 bg-brand-500 text-white rounded-xl text-xs font-bold">Lanjutkan Belajar</a>
    </div>
  </div>
</main>
@endsection
