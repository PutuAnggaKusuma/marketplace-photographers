@extends('layouts.photographer')

@section('title', 'Jadwal & Availability — Fotografer')

@section('content')
<div class="flex justify-between items-center">
  <div>
    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Jadwal & Availability Kalender</h1>
    <p class="text-xs text-gray-500">Blokir tanggal atau jam secara manual agar client tidak bisa membooking pada waktu tersebut.</p>
  </div>
  <button class="px-4 py-2 bg-red-500 text-white rounded-lg text-sm font-semibold">+ Blokir Jadwal Manual</button>
</div>

<div class="p-5 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 min-h-[400px]">
  <p class="text-xs text-gray-500">[Tampilan Kalender FullCalendar / Availability Block]</p>
</div>
@endsection
