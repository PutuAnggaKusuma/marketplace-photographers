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
            <a href="{{ route('admin.categories') }}" class="px-4 py-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200/80 dark:border-amber-900/60 font-extrabold text-xs transition inline-flex items-center justify-center shrink-0">Kembali ke Daftar Kategori</a>
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
                    Simpan Kategori Baru
                </button>
            </div>
        </form>
    </div>

</div>
@endsection