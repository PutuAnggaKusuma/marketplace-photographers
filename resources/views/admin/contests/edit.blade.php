@extends('layouts.admin')

@section('title', 'Edit Lomba Foto — Admin')

@section('content')
<div class="space-y-6">

    <!-- Standard Golden Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <span class="text-[11px] font-extrabold uppercase tracking-widest text-amber-600 dark:text-amber-500 block mb-1">
                Kompetisi & Event Kreatif
            </span>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">
                Edit Event Lomba Foto
            </h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-1">
                Perbarui rincian hadiah, batas waktu pendaftaran, poster, atau deskripsi kompetisi foto.
            </p>
        </div>
        <div>
            <a href="{{ route('admin.contests') }}" class="px-4 py-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200/80 dark:border-amber-900/60 font-extrabold text-xs transition inline-flex items-center justify-center shrink-0">Kembali ke Daftar Lomba</a>
        </div>
    </div>

    <!-- Main Vertical Form Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm p-6 sm:p-8"
         x-data="{
            previewUrl: '{{ $contest->banner ? asset('storage/' . $contest->banner) : '' }}',
            fileChosen(event) {
                const file = event.target.files[0];
                if (file) this.previewUrl = URL.createObjectURL(file);
            }
         }">
        <form action="{{ route('admin.contests.update', $contest->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <!-- 1. Judul Lomba -->
            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                    Judul Event Lomba Foto <span class="text-rose-500">*</span>
                </label>
                <input type="text" 
                       name="judul" 
                       value="{{ old('judul', $contest->judul) }}"
                       required 
                       placeholder="Contoh: LensMatch National Photography Contest: Pesona Budaya Nusantara" 
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition @error('judul') border-rose-500 @enderror">
                @error('judul')
                    <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 2. Hadiah Total (Rp) -->
            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                    Total Hadiah (Rp) <span class="text-rose-500">*</span>
                </label>
                <input type="number" 
                       name="hadiah" 
                       value="{{ old('hadiah', (int)$contest->hadiah) }}"
                       required 
                       min="0"
                       step="50000"
                       placeholder="Contoh: 15000000" 
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition @error('hadiah') border-rose-500 @enderror">
                <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1">Masukkan nominal angka murni tanpa titik atau koma.</p>
                @error('hadiah')
                    <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 3. Tanggal Mulai -->
            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                    Tanggal Mulai Pendaftaran / Submission <span class="text-rose-500">*</span>
                </label>
                <input type="date" 
                       name="tanggal_mulai" 
                       value="{{ old('tanggal_mulai', $contest->tanggal_mulai ? date('Y-m-d', strtotime($contest->tanggal_mulai)) : '') }}"
                       required 
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition @error('tanggal_mulai') border-rose-500 @enderror">
                @error('tanggal_mulai')
                    <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 4. Tanggal Selesai -->
            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                    Tanggal Batas Akhir (Deadline Submission) <span class="text-rose-500">*</span>
                </label>
                <input type="date" 
                       name="tanggal_selesai" 
                       value="{{ old('tanggal_selesai', $contest->tanggal_selesai ? date('Y-m-d', strtotime($contest->tanggal_selesai)) : '') }}"
                       required 
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition @error('tanggal_selesai') border-rose-500 @enderror">
                @error('tanggal_selesai')
                    <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 5. Deskripsi & Ketentuan Lomba -->
            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                    Deskripsi, Tema, & Ketentuan Lomba <span class="text-rose-500">*</span>
                </label>
                <textarea name="deskripsi" 
                          rows="6" 
                          required 
                          placeholder="Tuliskan tema kompetisi, syarat partisipasi peserta, kriteria penjurian, dan rincian hadiah juara 1, 2, 3..." 
                          class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition resize-y @error('deskripsi') border-rose-500 @enderror">{{ old('deskripsi', $contest->deskripsi) }}</textarea>
                @error('deskripsi')
                    <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 6. Banner / Poster Lomba -->
            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                    Poster / Banner Event Lomba
                </label>
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                    <div class="w-48 h-28 rounded-2xl bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 flex items-center justify-center overflow-hidden shrink-0 shadow-xs">
                        <template x-if="previewUrl">
                            <img :src="previewUrl" onerror="this.onerror=null;this.src=window.SVG_IMAGE_FALLBACK;" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!previewUrl">
                            <div class="text-center p-3 text-gray-400">
                                <svg class="w-8 h-8 mx-auto mb-1 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
                                <span class="text-[10px] font-bold">Poster Lomba</span>
                            </div>
                        </template>
                    </div>

                    <div class="space-y-2">
                        <label class="cursor-pointer inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs shadow-xs transition">
                            <span>Ganti Poster Lomba</span>
                            <input type="file" name="banner" accept="image/png, image/jpeg, image/webp" @change="fileChosen" class="hidden">
                        </label>
                        <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">Format: JPG, PNG, WEBP. Maksimal 10MB.</p>
                    </div>
                </div>
                @error('banner')
                    <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Button Footer -->
            <div class="flex items-center justify-end pt-4">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-xs transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection