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
      <img onerror="this.onerror=null;this.src=window.SVG_IMAGE_FALLBACK||'data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='800' height='600' viewBox='0 0 800 600' fill='none'%3E%3Crect width='800' height='600' fill='%23F3F4F6'/%3E%3Cpath d='M360 260C360 248.954 368.954 240 380 240H420C431.046 240 440 248.954 440 260V265H450C466.569 265 480 278.431 480 295V345C480 361.569 466.569 375 450 375H350C333.431 375 320 361.569 320 345V295C320 278.431 333.431 265 350 265H360V260Z' stroke='%239CA3AF' stroke-width='12' stroke-linecap='round' stroke-linejoin='round'/%3E%3Ccircle cx='400' cy='320' r='30' stroke='%239CA3AF' stroke-width='12'/%3E%3Cline x1='310' y1='240' x2='490' y2='390' stroke='%23EF4444' stroke-width='10' stroke-linecap='round'/%3E%3Ctext x='400' y='450' font-family='sans-serif' font-size='22' font-weight='700' fill='%236B7280' text-anchor='middle'%3EGambar Tidak Dapat Dimuat%3C/text%3E%3C/svg%3E';" src="{{ asset('images/user/owner.jpg') }}" class="w-full h-full object-cover" />
    </div>
    <h4 class="font-bold text-sm">Album Prewedding Bali</h4>
  </div>
</div>
@endsection
