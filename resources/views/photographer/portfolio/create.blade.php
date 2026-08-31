@extends('layouts.photographer')

@section('title', 'Tambah Portofolio Karya — Fotografer')

@section('content')
<div class="space-y-6">

    <!-- Standard Golden Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <span class="text-[11px] font-extrabold uppercase tracking-widest text-amber-600 dark:text-amber-500 block mb-1">
                Karya & Galeri Hasil Foto
            </span>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">
                Tambah Portofolio Karya Baru
            </h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-1">
                Unggah hasil jepretan terbaik Anda untuk memukau calon klien di etalase publik.
            </p>
        </div>
        <div>
            <a href="{{ route('photographer.portfolio') }}" class="px-4 py-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200/80 dark:border-amber-900/60 font-extrabold text-xs transition inline-flex items-center justify-center shrink-0">Kembali ke Portofolio</a>
        </div>
    </div>

    <!-- Main Vertical Form Card (Rule #23 & Rule #21) -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm p-6 sm:p-8"
         x-data="{ 
            previewUrl: null,
            fileChosen(event) {
                const file = event.target.files[0];
                if (file) {
                    this.previewUrl = URL.createObjectURL(file);
                }
            }
         }">
        
        <form action="{{ route('photographer.portfolio.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <!-- 1. Judul Portofolio -->
            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                    Judul Portofolio Karya <span class="text-rose-500">*</span>
                </label>
                <input type="text" 
                       name="judul" 
                       value="{{ old('judul') }}"
                       required 
                       placeholder="Contoh: Golden Hour Sunset Session di Melasti Beach" 
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition @error('judul') border-rose-500 @enderror">
                @error('judul')
                    <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 2. Kategori Fotografi (Alpine.js Custom Floating Dropdown - Rule #16) -->
            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                    Kategori Fotografi <span class="text-rose-500">*</span>
                </label>
                @php
                    $selectedCatId = old('id_kategori', '');
                    $selectedCatName = '';
                    foreach($categories as $cat) {
                        if ($cat->id == $selectedCatId) {
                            $selectedCatName = $cat->nama_kategori;
                            break;
                        }
                    }
                    if (!$selectedCatName) {
                        $selectedCatName = '-- Pilih Kategori Sesi Foto --';
                    }
                @endphp
                <div class="relative w-full" x-data="{ open: false, selectedId: '{{ $selectedCatId }}', selectedName: '{{ $selectedCatName }}' }">
                    <input type="hidden" name="id_kategori" :value="selectedId" required>
                    <button @click="open = !open" type="button" 
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs font-bold flex items-center justify-between gap-2 shadow-2xs hover:border-amber-400 dark:hover:border-amber-500 cursor-pointer transition @error('id_kategori') border-rose-500 @enderror">
                        <span x-text="selectedName" :class="selectedId ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-500'" class="truncate"></span>
                        <svg class="w-4 h-4 text-gray-400 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" @click.away="open = false" x-transition 
                         class="absolute z-50 left-0 mt-2 w-full bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700/80 py-1 max-h-60 overflow-y-auto" style="display: none;">
                        @foreach($categories as $cat)
                            <button type="button" 
                                    @click="selectedId = '{{ $cat->id }}'; selectedName = '{{ addslashes($cat->nama_kategori) }}'; open = false" 
                                    class="w-full px-4 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition flex items-center justify-between text-left cursor-pointer border-b border-gray-50 dark:border-gray-700/40 last:border-0"
                                    :class="selectedId == '{{ $cat->id }}' ? 'bg-amber-50/70 dark:bg-amber-950/30 text-amber-600 font-extrabold' : ''">
                                <span>{{ $cat->nama_kategori }}</span>
                                <span x-show="selectedId == '{{ $cat->id }}'" class="text-amber-600 font-bold">✓</span>
                            </button>
                        @endforeach
                    </div>
                </div>
                @error('id_kategori')
                    <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 3. Deskripsi Karya -->
            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                    Deskripsi / Cerita di Balik Karya <span class="text-rose-500">*</span>
                </label>
                <textarea name="deskripsi" 
                          rows="4" 
                          required 
                          placeholder="Ceritakan konsep pemotretan, tone warna, peralatan kamera/lensa yang digunakan, atau latar belakang momen..." 
                          class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition resize-none @error('deskripsi') border-rose-500 @enderror">{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                    <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 4. Upload Foto Utama (Native Pure Device Upload - Rule #21) -->
            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                    File Foto Portofolio <span class="text-rose-500">*</span>
                </label>
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                    <div class="w-48 h-32 rounded-2xl bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 flex items-center justify-center overflow-hidden shrink-0 shadow-xs">
                        <template x-if="previewUrl">
                            <img :src="previewUrl" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!previewUrl">
                            <div class="text-center p-3 text-gray-400">
                                <svg class="w-8 h-8 mx-auto mb-1 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
                                <span class="text-[10px] font-bold">Preview Foto</span>
                            </div>
                        </template>
                    </div>

                    <div class="space-y-2">
                        <label class="cursor-pointer inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs shadow-xs transition">
                            <span>Upload Foto Portofolio</span>
                            <input type="file" name="foto" required accept="image/png, image/jpeg, image/webp" @change="fileChosen" class="hidden">
                        </label>
                        <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">Format: JPG, PNG, WEBP. Maksimal ukuran file 10MB.</p>
                    </div>
                </div>
                @error('foto')
                    <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Button Footer -->
            <div class="flex items-center justify-end pt-4">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-xs transition">
                    Simpan Portofolio Baru
                </button>
            </div>

        </form>

    </div>

</div>
@endsection