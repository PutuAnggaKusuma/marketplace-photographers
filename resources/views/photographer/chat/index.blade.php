@extends('layouts.photographer')

@section('title', 'Pesan & Chat Real-time — Fotografer')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 dark:text-white">Pesan & Conversations Client (Reverb Real-time)</h1>

<div class="grid grid-cols-1 md:grid-cols-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 min-h-[500px]">
  <div class="border-r border-gray-200 dark:border-gray-700 p-4 space-y-3">
    <h3 class="font-bold text-sm text-gray-700 dark:text-gray-300">Daftar Obrolan</h3>
    <div class="p-3 bg-brand-50 dark:bg-gray-700 rounded-lg cursor-pointer flex gap-3 items-center">
      <span class="w-8 h-8 rounded-full bg-brand-500 text-white flex items-center justify-center text-xs font-bold">CL</span>
      <div>
        <h4 class="text-sm font-semibold text-gray-800 dark:text-white">Client Sarah</h4>
        <p class="text-xs text-gray-500">Tanya ketersediaan tanggal 15...</p>
      </div>
    </div>
  </div>

  <div class="md:col-span-2 flex flex-col justify-between p-4">
    <div class="space-y-3">
      <div class="bg-gray-100 dark:bg-gray-700 p-3 rounded-lg max-w-md text-sm">
        Halo mas fotografer, untuk tanggal 15 Oktober paket weddingnya masih ready?
      </div>
    </div>

    <div class="pt-3 border-t border-gray-200 dark:border-gray-700 flex gap-2">
      <input type="text" placeholder="Ketik balasan pesan..." class="w-full text-sm px-4 py-2 border rounded-lg dark:bg-gray-900 dark:border-gray-700 dark:text-white" />
      <button class="px-4 py-2 bg-brand-500 text-white text-sm font-semibold rounded-lg">Kirim</button>
    </div>
  </div>
</div>
@endsection
