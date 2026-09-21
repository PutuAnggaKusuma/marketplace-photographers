@extends('layouts.photographer')

@section('title', 'Blokir Tanggal / Libur — Fotografer')

@section('content')
<div class="space-y-6">

    <!-- Standard Golden Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <span class="text-[11px] font-extrabold uppercase tracking-widest text-amber-600 dark:text-amber-500 block mb-1">
                Kalender & Manajemen Jadwal
            </span>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">
                Blokir Tanggal Libur Pemotretan
            </h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-1">
                Tandai tanggal di mana Anda berhalangan atau libur agar tidak dapat dibooking oleh klien.
            </p>
        </div>
        <div>
            <a href="{{ route('photographer.availability') }}" class="px-4 py-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200/80 dark:border-amber-900/60 font-extrabold text-xs transition inline-flex items-center justify-center shrink-0">Kembali ke Kalender</a>
        </div>
    </div>

    <!-- Main Vertical Form Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm p-6 sm:p-8">
        
        <form action="{{ route('photographer.availability.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- 1. Tanggal Mulai -->
            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                    Tanggal Mulai Libur <span class="text-rose-500">*</span>
                </label>
                <input type="date" 
                       name="tanggal_mulai" 
                       value="{{ old('tanggal_mulai', date('Y-m-d')) }}"
                       required 
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition @error('tanggal_mulai') border-rose-500 @enderror">
                @error('tanggal_mulai')
                    <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 2. Tanggal Selesai -->
            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                    Tanggal Selesai Libur <span class="text-rose-500">*</span>
                </label>
                <input type="date" 
                       name="tanggal_selesai" 
                       value="{{ old('tanggal_selesai', date('Y-m-d')) }}"
                       required 
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition @error('tanggal_selesai') border-rose-500 @enderror">
                @error('tanggal_selesai')
                    <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 3. Alasan / Keterangan Libur -->
            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                    Alasan / Keterangan Libur (Opsional)
                </label>
                <textarea name="alasan" 
                          rows="3" 
                          placeholder="Contoh: Pemeliharaan kamera & lensa, libur luar kota, atau acara keluarga..." 
                          class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition resize-none @error('alasan') border-rose-500 @enderror">{{ old('alasan') }}</textarea>
                @error('alasan')
                    <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Button Footer -->
            <div class="flex items-center justify-end pt-4">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-xs transition">
                    Tambah Data
                </button>
            </div>

        </form>

    </div>

</div>
@endsection