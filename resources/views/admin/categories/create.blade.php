@extends('layouts.admin')

@section('title', 'Tambah Kategori Layanan — Admin')

@section('content')
<div class="space-y-6">

    <!-- Standard Golden Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <span class="text-[11px] font-extrabold uppercase tracking-widest text-amber-600 dark:text-amber-500 block mb-1">
                Katalog & Taksonomi Platform
            </span>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">
                Tambah Kategori Layanan Baru
            </h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-1">
                Buat kategori sesi foto baru untuk memfilter penawaran jasa dan portofolio fotografer di etalase publik.
            </p>
        </div>
        <div>
            <a href="{{ route('admin.categories') }}" class="px-4 py-2 rounded-xl bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-750 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 font-bold text-xs transition inline-flex items-center gap-1.5 shadow-2xs shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"></path></svg>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <!-- Main Vertical Form Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm p-6 sm:p-8">
        <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- 1. Nama Kategori -->
            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                    Nama Kategori Layanan <span class="text-rose-500">*</span>
                </label>
                <input type="text" 
                       name="nama_kategori" 
                       value="{{ old('nama_kategori') }}"
                       required 
                       placeholder="Contoh: Wedding & Pernikahan, Prewedding Outdoor, Commercial Product" 
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition @error('nama_kategori') border-rose-500 @enderror">
                @error('nama_kategori')
                    <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 2. Deskripsi Kategori -->
            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                    Deskripsi Kategori Layanan <span class="text-rose-500">*</span>
                </label>
                <textarea name="deskripsi" 
                          rows="4" 
                          required 
                          placeholder="Jelaskan cakupan layanan, target konsumen, dan kriteria foto pada kategori ini..." 
                          class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition resize-none @error('deskripsi') border-rose-500 @enderror">{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
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