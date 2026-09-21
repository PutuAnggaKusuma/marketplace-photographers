@extends('layouts.photographer')

@section('title', 'Tambah Paket Layanan Baru — Fotografer')

@section('content')
<div class="space-y-6">

    <!-- Standard Golden Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <span class="text-[11px] font-extrabold uppercase tracking-widest text-amber-600 dark:text-amber-500 block mb-1">
                Katalog & Penawaran Jasa
            </span>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">
                Tambah Paket Layanan Baru
            </h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-1">
                Lengkapi rincian paket pemotretan, tarif harga penawaran, dan fasilitas yang didapat klien.
            </p>
        </div>
        <div>
            <a href="{{ route('photographer.services') }}" class="px-4 py-2 rounded-xl bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-750 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 font-bold text-xs transition inline-flex items-center gap-1.5 shadow-2xs shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"></path></svg>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <!-- Main Vertical Form Card (Rule #23 & Rule #22) -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm p-6 sm:p-8"
         x-data="{ 
            fiturs: ['Durasi Sesi 3 Jam', '25 Foto Retouched & Full Editing', 'Semua File Original RAW/JPEG'],
            addFitur() { this.fiturs.push(''); },
            removeFitur(index) { if (this.fiturs.length > 1) this.fiturs.splice(index, 1); }
         }">
        
        <form action="{{ route('photographer.services.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- 1. Nama Paket Layanan -->
            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                    Nama Paket Layanan <span class="text-rose-500">*</span>
                </label>
                <input type="text" 
                       name="nama_layanan" 
                       value="{{ old('nama_layanan') }}"
                       required 
                       placeholder="Contoh: Paket Prewedding Romantic Sunset" 
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition @error('nama_layanan') border-rose-500 @enderror">
                @error('nama_layanan')
                    <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 2. Tarif Harga (Rp) -->
            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                    Tarif Harga (Rp) <span class="text-rose-500">*</span>
                </label>
                <input type="number" 
                       name="tarif_harga" 
                       value="{{ old('tarif_harga') }}"
                       required 
                       min="0"
                       step="1000"
                       placeholder="Contoh: 3500000" 
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition @error('tarif_harga') border-rose-500 @enderror">
                <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1">Masukkan nominal angka murni tanpa titik atau koma.</p>
                @error('tarif_harga')
                    <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 3. Deskripsi Layanan -->
            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                    Deskripsi Lengkap Paket <span class="text-rose-500">*</span>
                </label>
                <textarea name="deskripsi_layanan" 
                          rows="4" 
                          required 
                          placeholder="Tuliskan cakupan pemotretan, konsep lokasi, dan ketentuan umum sesi foto..." 
                          class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition resize-none @error('deskripsi_layanan') border-rose-500 @enderror">{{ old('deskripsi_layanan') }}</textarea>
                @error('deskripsi_layanan')
                    <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 4. Rincian Fasilitas / Fitur Poin -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">
                        Rincian Benefit & Fasilitas yang Didapat Klien
                    </label>
                    <button type="button" 
                            @click="addFitur()" 
                            class="text-xs font-extrabold text-amber-600 dark:text-amber-400 hover:text-amber-700 flex items-center gap-1 transition mr-12">
                        <span>+ Tambah Benefit</span>
                    </button>
                </div>
                
                <div class="space-y-2">
                    <template x-for="(fitur, index) in fiturs" :key="index">
                        <div class="flex items-center gap-2">
                            <input type="text" 
                                   name="fitur[]" 
                                   x-model="fiturs[index]" 
                                   placeholder="Contoh: 20 Foto Retouch & Full Color Grading" 
                                   class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">
                            <button type="button" 
                                    @click="removeFitur(index)" 
                                    class="p-2.5 rounded-xl border border-rose-200 dark:border-rose-900/60 bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 hover:bg-rose-100 transition shrink-0" 
                                    title="Hapus Benefit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </template>
                </div>
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