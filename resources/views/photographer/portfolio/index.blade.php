@extends('layouts.photographer')

@section('title', 'Portofolio & Media — Fotografer')

@section('content')
<div class="flex justify-between items-center">
  <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Portofolio & Galeri Media</h1>
  <button class="px-4 py-2 bg-brand-500 text-white rounded-lg text-sm font-semibold">+ Upload Portofolio Baru</button>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
  <div class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 space-y-2">
    <div class="h-40 bg-gray-200 dark:bg-gray-700 rounded-lg overflow-hidden">
      <img src="{{ asset('images/user/owner.jpg') }}" class="w-full h-full object-cover" />
    </div>
    <h4 class="font-bold text-sm">Album Prewedding Bali</h4>
  </div>
</div>
@endsection
