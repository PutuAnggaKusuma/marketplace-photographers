@extends('layouts.app')

@section('title', 'Pesan & Obrolan — LensMatch')

@section('content')
<main class="max-w-6xl mx-auto px-4 py-10 min-h-screen">
  <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm grid grid-cols-1 md:grid-cols-3 min-h-[600px]">
    <div class="border-r border-gray-200 dark:border-gray-700 p-4 space-y-3">
      <h3 class="font-bold text-sm">Obrolan Saya</h3>
      <div class="p-3 bg-brand-50 dark:bg-gray-700 rounded-xl cursor-pointer flex items-center gap-3">
        <span class="w-9 h-9 rounded-full bg-brand-500 text-white flex items-center justify-center font-bold text-xs">AV</span>
        <div>
          <h4 class="font-bold text-xs">Alex Visual Studio</h4>
          <p class="text-[10px] text-gray-500">Tentu, tanggal 15 masih kos...</p>
        </div>
      </div>
    </div>

    <div class="md:col-span-2 p-6 flex flex-col justify-between">
      <div class="space-y-3">
        <div class="bg-brand-50 dark:bg-gray-700/50 p-3 rounded-xl max-w-sm text-xs ml-auto">
          Halo kak Alex, mau konfirmasi ketersediaan untuk tanggal 15 Oktober?
        </div>
        <div class="bg-gray-100 dark:bg-gray-700 p-3 rounded-xl max-w-sm text-xs">
          Halo! Tentu, tanggal 15 masih kosong kak. Silakan buat form booking melalui tombol di profil ya.
        </div>
      </div>

      <div class="pt-4 border-t border-gray-200 dark:border-gray-700 flex gap-2">
        <input type="text" placeholder="Ketik pesan..." class="w-full px-4 py-2.5 text-xs rounded-xl border dark:bg-gray-900 dark:border-gray-700" />
        <button class="px-6 py-2.5 bg-brand-500 text-white text-xs font-bold rounded-xl">Kirim</button>
      </div>
    </div>
  </div>
</main>
@endsection
