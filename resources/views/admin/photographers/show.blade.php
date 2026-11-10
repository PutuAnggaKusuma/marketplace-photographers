@extends('layouts.admin')

@section('title', 'Verifikasi Fotografer — Admin')

@section('content')
@php
    $errors = $errors ?? new \Illuminate\Support\ViewErrorBag();

    $rawAvatar = $photographer->foto;
    $avatarUrl = $rawAvatar ? (str_starts_with($rawAvatar, 'http') ? $rawAvatar : asset('storage/' . $rawAvatar)) : null;

    $rawSampul = $photographer->foto_sampul;
    $sampulUrl = $rawSampul ? (str_starts_with($rawSampul, 'http') ? $rawSampul : asset('storage/' . $rawSampul)) : null;

    $missingCount = count($missingFields);
    $sectionStatus = $photographer->getProfileSectionsStatus();

    $waClean = null;
    if ($photographer->nomor_telepon) {
        $waDigits = preg_replace('/[^0-9]/', '', $photographer->nomor_telepon);
        if ($waDigits !== '') {
            $waClean = str_starts_with($waDigits, '0') ? '62' . substr($waDigits, 1) : $waDigits;
        }
    }

    $rawIg = trim((string) ($photographer->link_sosmed ?? ''));
    $igUrl = null;
    if ($rawIg !== '') {
        if (str_starts_with($rawIg, 'http://') || str_starts_with($rawIg, 'https://')) {
            $igUrl = $rawIg;
        } elseif (str_contains($rawIg, 'instagram.com')) {
            $igUrl = 'https://' . ltrim($rawIg, '/');
        } else {
            $igUrl = 'https://instagram.com/' . ltrim($rawIg, '@/ ');
        }
    }

    $accountName = trim((string) ($photographer->user?->nama ?? ''));
    $studioName = trim((string) ($photographer->nama ?? ''));
    $showDistinctAccountName = $accountName !== '' && strcasecmp($accountName, $studioName) !== 0;
@endphp

<div x-data="{
    rejectModalOpen: {{ $errors->has('catatan_penolakan') ? 'true' : 'false' }},
    deleteModalOpen: {{ ($errors->has('kategori_penghapusan') || $errors->has('alasan_penghapusan')) ? 'true' : 'false' }},
    imagePreviewModalOpen: false,
    imagePreviewSrc: '',
    imagePreviewTitle: '',
    openImagePreview(src, title) {
        if (!src) return;
        this.imagePreviewSrc = src;
        this.imagePreviewTitle = title || 'Pratinjau Foto';
        this.imagePreviewModalOpen = true;
    },
    closeImagePreview() {
        this.imagePreviewModalOpen = false;
        this.imagePreviewSrc = '';
        this.imagePreviewTitle = '';
    }
}" class="space-y-6 w-full pb-10">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <nav class="flex items-center gap-1.5 text-xs text-gray-400 dark:text-gray-500 mb-1.5">
                <a href="{{ route('admin.photographers') }}" class="hover:text-amber-500 transition">Manajemen Fotografer</a>
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                <span>{{ $photographer->nama }}</span>
            </nav>
            <h1 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">
                Verifikasi Fotografer
            </h1>
        </div>

        <div class="flex items-center gap-2.5 shrink-0">
            @if($photographer->is_onboarded && $photographer->status_verifikasi === 'terverifikasi')
                <a href="{{ route('public.photographers.show', $photographer->id) }}"
                   target="_blank"
                   class="px-4 py-2 rounded-xl bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 font-bold text-xs transition shadow-2xs">
                    Lihat Etalase Publik
                </a>
            @endif

            <a href="{{ route('admin.photographers') }}"
               class="px-4 py-2 rounded-xl bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 font-bold text-xs transition inline-flex items-center gap-1.5 shadow-2xs">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <!-- Banner Catatan Penolakan (Jika Status Ditolak) -->
    @if($photographer->status_verifikasi === 'ditolak')
        <div class="p-5 rounded-2xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-900/60 space-y-1.5">
            <div class="text-rose-800 dark:text-rose-300 font-extrabold text-sm">
                Pendaftaran Ditolak
            </div>
            <p class="text-xs text-rose-700 dark:text-rose-400 leading-relaxed">
                Catatan Admin: <span class="font-bold">{{ $photographer->catatan_penolakan ?: 'Data profil belum memenuhi persyaratan.' }}</span>
            </p>
        </div>
    @endif

    <!-- Indikator Status & Kelengkapan Profil (Selaras dengan Halaman Profil Fotografer) -->
    <div class="bg-white dark:bg-gray-800 p-4 sm:p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-xs space-y-2">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
            <div class="flex flex-wrap items-center gap-x-2.5 gap-y-1">
                <span class="font-bold text-gray-900 dark:text-white">Kelengkapan Profil</span>
                <span class="text-gray-400 dark:text-gray-500">({{ $sectionStatus['completed_count'] }}/{{ $sectionStatus['total_sections'] }} bagian)</span>
                <span class="text-gray-300 dark:text-gray-600">/</span>
                @if($photographer->status_verifikasi === 'terverifikasi')
                    <span class="font-bold text-emerald-600 dark:text-emerald-400">Terverifikasi</span>
                @elseif($photographer->status_verifikasi === 'ditolak')
                    <span class="font-bold text-rose-600 dark:text-rose-400">Ditolak</span>
                @else
                    <span class="font-bold text-amber-500 dark:text-amber-400">Menunggu Verifikasi</span>
                @endif
                <span class="text-gray-300 dark:text-gray-600">/</span>
                <span class="text-gray-500 dark:text-gray-400">Terdaftar {{ $photographer->created_at ? $photographer->created_at->format('d M Y, H:i') : '-' }}</span>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <span class="font-black text-gray-900 dark:text-white">{{ $completionPercentage }}%</span>
                @if(!$isComplete)
                    <span class="text-rose-600 dark:text-rose-400 font-semibold">Kurang {{ $missingCount }} data</span>
                @elseif($photographer->status_verifikasi === 'terverifikasi')
                    <span class="text-emerald-600 dark:text-emerald-400 font-bold">Lengkap</span>
                @elseif($photographer->status_verifikasi === 'ditolak')
                    <span class="text-gray-600 dark:text-gray-300 font-bold">Data lengkap</span>
                @else
                    <span class="text-emerald-600 dark:text-emerald-400 font-bold">Siap diverifikasi</span>
                @endif
            </div>
        </div>

        @if(!$isComplete && count($missingFields) > 0)
            <div class="pt-2 border-t border-gray-100 dark:border-gray-700/60 text-xs text-gray-600 dark:text-gray-400">
                <span class="font-bold text-gray-700 dark:text-gray-300">Belum diisi:</span>
                <span class="text-gray-500 dark:text-gray-400 ml-1 leading-relaxed">{{ implode(', ', array_values($missingFields)) }}.</span>
            </div>
        @endif
    </div>

    <!-- Main Profile Card (Layout 1:1 Mirror dengan Form Profil Fotografer) -->
    <div class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-3xl border border-gray-200 dark:border-gray-700/80 shadow-xs space-y-6">

        <!-- SECTION 1: Kontak -->
        <div class="space-y-4">
            <h2 class="text-sm font-black text-gray-900 dark:text-white">
                1. Kontak
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama Lengkap -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">
                            Nama Lengkap
                        </label>
                        @if($showDistinctAccountName)
                            <span class="text-[11px] text-gray-400">Akun: {{ $accountName }}</span>
                        @endif
                    </div>
                    <div class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs {{ $photographer->nama ? 'font-bold text-gray-900 dark:text-white' : 'font-medium text-gray-400 dark:text-gray-500' }}">
                        {{ $photographer->nama ?: '-' }}
                    </div>
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                        Email
                    </label>
                    <div class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs {{ $photographer->user?->email ? 'font-bold text-gray-900 dark:text-white' : 'font-medium text-gray-400 dark:text-gray-500' }}">
                        {{ $photographer->user?->email ?? '-' }}
                    </div>
                </div>

                <!-- Nomor WhatsApp -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">
                            Nomor WhatsApp
                        </label>
                        @if($waClean)
                            <a href="https://wa.me/{{ $waClean }}" target="_blank" class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
                                Chat WhatsApp
                            </a>
                        @endif
                    </div>
                    <div class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs font-bold {{ $photographer->nomor_telepon ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-500' }}">
                        {{ $photographer->nomor_telepon ?: '-' }}
                    </div>
                </div>

                <!-- Instagram -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                        Instagram
                    </label>
                    <div class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs">
                        @if($igUrl)
                            <a href="{{ $igUrl }}"
                               target="_blank"
                               class="font-bold text-amber-600 dark:text-amber-400 hover:underline truncate block">
                                {{ $photographer->link_sosmed }}
                            </a>
                        @else
                            <span class="font-medium text-gray-400 dark:text-gray-500">-</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 2: Domisili & Kategori -->
        <div class="space-y-4 pt-2">
            <h2 class="text-sm font-black text-gray-900 dark:text-white">
                2. Domisili & Kategori
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Provinsi -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                        Provinsi
                    </label>
                    <div class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs {{ $photographer->province ? 'font-bold text-gray-900 dark:text-white' : 'font-medium text-gray-400 dark:text-gray-500' }}">
                        {{ $photographer->province?->name ?? '-' }}
                    </div>
                </div>

                <!-- Kota / Kabupaten -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                        Kota / Kabupaten
                    </label>
                    <div class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs {{ $photographer->city ? 'font-bold text-gray-900 dark:text-white' : 'font-medium text-gray-400 dark:text-gray-500' }}">
                        {{ $photographer->city?->name ?? '-' }}
                    </div>
                </div>

                <!-- Alamat -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                        Alamat
                    </label>
                    <div class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs {{ $photographer->alamat ? 'font-medium text-gray-900 dark:text-white' : 'font-medium text-gray-400 dark:text-gray-500' }}">
                        {{ $photographer->alamat ?: '-' }}
                    </div>
                </div>

                <!-- Kategori -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">
                            Kategori
                        </label>
                        <span class="text-[11px] text-gray-400">{{ $photographer->categories->count() }} dipilih</span>
                    </div>
                    <div class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs">
                        @if($photographer->categories->count() > 0)
                            <span class="font-bold text-gray-900 dark:text-white">
                                {{ $photographer->categories->pluck('nama_kategori')->join(', ') }}
                            </span>
                        @else
                            <span class="font-medium text-gray-400 dark:text-gray-500">Belum diatur</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 3: Bio -->
        <div class="space-y-2 pt-2">
            <h2 class="text-sm font-black text-gray-900 dark:text-white">
                3. Bio
            </h2>
            <div class="w-full min-h-[110px] px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs leading-relaxed">
                @if(!empty(trim((string) $photographer->deskripsi_bio)))
                    <p class="text-gray-800 dark:text-gray-200 whitespace-pre-line">{{ $photographer->deskripsi_bio }}</p>
                @else
                    <p class="text-gray-400 dark:text-gray-500 font-medium">Belum diisi</p>
                @endif
            </div>
        </div>

        <!-- SECTION 4: Foto Profil & Sampul (2 Kolom Seimbang, Mirror Form Profil Fotografer) -->
        <div class="space-y-4 pt-2">
            <h2 class="text-sm font-black text-gray-900 dark:text-white">
                4. Foto Profil & Sampul
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                <!-- Foto Profil -->
                <div class="p-6 sm:p-7 rounded-2xl bg-gray-50 dark:bg-gray-900/60 border border-gray-200 dark:border-gray-700/80 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="block text-xs font-bold text-gray-700 dark:text-gray-300">
                            Foto Profil
                        </span>
                        <span class="text-[11px] text-gray-400">Rasio 1:1 Persegi</span>
                    </div>

                    <div class="space-y-3">
                        <div class="relative w-28 h-28 rounded-2xl overflow-hidden bg-gray-100 dark:bg-gray-800 {{ $avatarUrl ? 'border border-gray-200 dark:border-gray-700 hover:border-amber-400 cursor-pointer group' : 'border-2 border-dashed border-gray-300 dark:border-gray-600' }} shadow-2xs flex items-center justify-center transition"
                             @if($avatarUrl)
                                 @click="openImagePreview({{ \Illuminate\Support\Js::from($avatarUrl) }}, {{ \Illuminate\Support\Js::from('Foto Profil — ' . $photographer->nama) }})"
                             @endif>
                            @if($avatarUrl)
                                <img src="{{ $avatarUrl }}"
                                     alt="{{ $photographer->nama }}"
                                     class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                                <div class="absolute inset-0 bg-black/45 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center text-white p-1">
                                    <svg class="w-5 h-5 text-white mb-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607zM10.5 7.5v6m3-3h-6"/></svg>
                                    <span class="text-[10px] font-bold">Pratinjau</span>
                                </div>
                            @else
                                <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                                </svg>
                            @endif
                        </div>

                        <p class="text-[11px] text-gray-400">
                            {{ $avatarUrl ? 'Klik foto untuk pratinjau ukuran penuh' : 'Belum diunggah' }}
                        </p>
                    </div>
                </div>

                <!-- Foto Sampul -->
                <div class="p-6 sm:p-7 rounded-2xl bg-gray-50 dark:bg-gray-900/60 border border-gray-200 dark:border-gray-700/80 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="block text-xs font-bold text-gray-700 dark:text-gray-300">
                            Foto Sampul
                        </span>
                        <span class="text-[11px] text-gray-400">Rasio Lanskap 16:9</span>
                    </div>

                    <div class="space-y-3">
                        <div class="relative w-full h-44 sm:h-48 rounded-2xl overflow-hidden bg-gray-100 dark:bg-gray-800 {{ $sampulUrl ? 'border border-gray-200 dark:border-gray-700 hover:border-amber-400 cursor-pointer group' : 'border-2 border-dashed border-gray-300 dark:border-gray-600' }} shadow-2xs flex items-center justify-center transition"
                             @if($sampulUrl)
                                 @click="openImagePreview({{ \Illuminate\Support\Js::from($sampulUrl) }}, {{ \Illuminate\Support\Js::from('Foto Sampul — ' . $photographer->nama) }})"
                             @endif>
                            @if($sampulUrl)
                                <img src="{{ $sampulUrl }}"
                                     alt="Sampul {{ $photographer->nama }}"
                                     class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-102">
                                <div class="absolute inset-0 bg-black/45 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white p-2">
                                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-black/60 backdrop-blur-xs text-xs font-bold text-white shadow-lg">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607zM10.5 7.5v6m3-3h-6"/></svg>
                                        <span>Klik untuk Pratinjau Foto</span>
                                    </span>
                                </div>
                            @else
                                <svg class="w-10 h-10 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                                </svg>
                            @endif
                        </div>

                        <p class="text-[11px] text-gray-400">
                            {{ $sampulUrl ? 'Klik foto untuk pratinjau ukuran penuh' : 'Belum diunggah' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 5: Rekening Bank -->
        <div class="p-5 sm:p-6 rounded-2xl bg-gray-50 dark:bg-gray-900/60 border border-gray-200 dark:border-gray-700/80 space-y-4">
            <h2 class="text-sm font-black text-gray-900 dark:text-white">
                5. Rekening Bank
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-1">
                <!-- Nama Bank -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                        Nama Bank
                    </label>
                    <div class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs {{ $photographer->nama_bank ? 'font-bold text-gray-900 dark:text-white' : 'font-medium text-gray-400 dark:text-gray-500' }}">
                        {{ $photographer->nama_bank ?: '-' }}
                    </div>
                </div>

                <!-- Nomor Rekening -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                        Nomor Rekening
                    </label>
                    <div class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs {{ $photographer->nomor_rekening ? 'font-mono font-extrabold text-gray-900 dark:text-white tracking-wide' : 'font-medium text-gray-400 dark:text-gray-500' }}">
                        {{ $photographer->nomor_rekening ?: '-' }}
                    </div>
                </div>

                <!-- Nama Pemilik Rekening -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                        Nama Pemilik Rekening
                    </label>
                    <div class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs {{ $photographer->atas_nama_rekening ? 'font-bold text-gray-900 dark:text-white' : 'font-medium text-gray-400 dark:text-gray-500' }}">
                        {{ $photographer->atas_nama_rekening ?: '-' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Action Bar (Rule #74: Rata Kanan, Selaras Posisi Submit Form Profil) -->
        <div class="flex flex-wrap items-center justify-end gap-3 pt-2">
            <button type="button"
                    @click="deleteModalOpen = true"
                    class="px-5 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200/80 dark:border-rose-900/60 font-bold text-xs transition cursor-pointer">
                Hapus
            </button>

            @if($photographer->status_verifikasi !== 'ditolak')
                <button type="button"
                        @click="rejectModalOpen = true"
                        class="px-5 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200/80 dark:border-rose-900/60 font-bold text-xs transition cursor-pointer">
                    Tolak Pendaftaran
                </button>
            @endif

            @if($photographer->status_verifikasi === 'terverifikasi')
                <button type="button"
                        @click="$dispatch('open-confirm-modal', {
                            title: 'Batalkan Verifikasi Fotografer?',
                            message: {{ \Illuminate\Support\Js::from('Apakah Anda yakin ingin membatalkan status verifikasi untuk ' . $photographer->nama . '?') }},
                            buttonText: 'Ya, Batalkan Verifikasi',
                            buttonClass: 'bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold shadow-xs',
                            type: 'warning',
                            actionUrl: '{{ route('admin.photographers.verify', $photographer->id) }}',
                            method: 'POST'
                        })"
                        class="px-5 py-2.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200/80 dark:border-amber-900/60 font-bold text-xs transition cursor-pointer">
                    Batalkan Verifikasi
                </button>
            @else
                <button type="button"
                        @click="$dispatch('open-confirm-modal', {
                            title: 'Verifikasi Fotografer?',
                            message: {{ \Illuminate\Support\Js::from('Apakah Anda yakin ingin memverifikasi akun ' . $photographer->nama . '?') }},
                            buttonText: 'Ya, Verifikasi',
                            buttonClass: 'bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold shadow-xs',
                            type: 'success',
                            actionUrl: '{{ route('admin.photographers.verify', $photographer->id) }}',
                            method: 'POST'
                        })"
                        class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-xs transition cursor-pointer">
                    Verifikasi Fotografer
                </button>
            @endif
        </div>
    </div>

    <!-- Modal Tolak Pendaftaran Mitra (Rule #59) -->
    <div x-show="rejectModalOpen"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
         @keydown.escape.window="rejectModalOpen = false">
        <div @click.away="rejectModalOpen = false"
             class="w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/80 shadow-2xl overflow-hidden p-6 sm:p-7 space-y-5">

            <div class="text-center space-y-1">
                <h3 class="text-base sm:text-lg font-black text-gray-900 dark:text-white">
                    Tolak Pendaftaran Mitra
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Berikan catatan perbaikan agar fotografer dapat memperbarui profilnya.
                </p>
            </div>

            <form action="{{ route('admin.photographers.reject', $photographer->id) }}"
                  method="POST"
                  class="space-y-4"
                  x-data="{
                    catatan: {!! json_encode(old('catatan_penolakan', $photographer->catatan_penolakan ?? '')) !!},
                    touched: {{ $errors->has('catatan_penolakan') ? 'true' : 'false' }},
                    error: {!! json_encode($errors->first('catatan_penolakan') ?? '') !!},
                    validate() {
                        if (!this.catatan || !this.catatan.trim()) {
                            this.error = 'Catatan alasan penolakan wajib diisi.';
                            return false;
                        }
                        if (this.catatan.trim().length < 5) {
                            this.error = 'Catatan alasan penolakan minimal 5 karakter.';
                            return false;
                        }
                        this.error = '';
                        return true;
                    },
                    submitForm(e) {
                        this.touched = true;
                        if (!this.validate()) {
                            e.preventDefault();
                            this.$refs.catatanInput?.focus();
                            return false;
                        }
                    }
                  }"
                  @submit="submitForm($event)">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                        Catatan Alasan Penolakan <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="catatan_penolakan"
                              x-ref="catatanInput"
                              x-model="catatan"
                              @input="touched = true; validate()"
                              @blur="touched = true; validate()"
                              rows="4"
                              placeholder="Contoh: Nomor rekening tidak sesuai dengan nama pemilik."
                              class="w-full px-4 py-3 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 resize-y leading-relaxed transition"
                              :class="touched && error ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-rose-500'"></textarea>
                    <p x-show="touched && error" x-text="error" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
                </div>

                <div class="flex items-center justify-center gap-3 pt-2">
                    <button type="button" @click="rejectModalOpen = false; error = ''; touched = false" class="px-5 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300 font-bold text-xs transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs shadow-xs transition cursor-pointer">
                        Tolak Pendaftaran
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Hapus Fotografer (Rule #59) -->
    <div x-show="deleteModalOpen"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
         @keydown.escape.window="deleteModalOpen = false">
        <div @click.away="deleteModalOpen = false"
             class="w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/80 shadow-2xl overflow-hidden p-6 sm:p-7 space-y-5">

            <div class="text-center space-y-1">
                <h3 class="text-base sm:text-lg font-black text-gray-900 dark:text-white">
                    Hapus Fotografer
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Fotografer: <span class="font-bold text-gray-800 dark:text-gray-200">{{ $photographer->nama }}</span>
                </p>
            </div>

            <form action="{{ route('admin.photographers.destroy', $photographer->id) }}" method="POST" class="space-y-4" x-data="{
                categoryOpen: false,
                selectedCategory: {!! json_encode(old('kategori_penghapusan', '')) !!},
                alasanText: {!! json_encode(old('alasan_penghapusan', '')) !!},
                touched: {
                    category: {{ $errors->has('kategori_penghapusan') ? 'true' : 'false' }},
                    alasan: {{ $errors->has('alasan_penghapusan') ? 'true' : 'false' }}
                },
                errors: {
                    category: {!! json_encode($errors->first('kategori_penghapusan') ?? '') !!},
                    alasan: {!! json_encode($errors->first('alasan_penghapusan') ?? '') !!}
                },
                validateCategory() {
                    if (!this.selectedCategory) {
                        this.errors.category = 'Kategori pelanggaran wajib dipilih.';
                        return false;
                    }
                    this.errors.category = '';
                    return true;
                },
                validateAlasan() {
                    if (!this.alasanText || !this.alasanText.trim()) {
                        this.errors.alasan = 'Deskripsi alasan penghapusan wajib diisi.';
                        return false;
                    }
                    if (this.alasanText.trim().length < 5) {
                        this.errors.alasan = 'Deskripsi alasan minimal 5 karakter.';
                        return false;
                    }
                    this.errors.alasan = '';
                    return true;
                },
                submitForm(e) {
                    this.touched.category = true;
                    this.touched.alasan = true;
                    const validCat = this.validateCategory();
                    const validAlasan = this.validateAlasan();
                    if (!validCat) {
                        e.preventDefault();
                        this.$refs.categoryBtn?.focus();
                        return false;
                    }
                    if (!validAlasan) {
                        e.preventDefault();
                        this.$refs.alasanInput?.focus();
                        return false;
                    }
                }
            }" @submit="submitForm($event)">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                        Kategori Pelanggaran <span class="text-rose-500">*</span>
                    </label>
                    <input type="hidden" name="kategori_penghapusan" :value="selectedCategory">

                    <div class="relative w-full">
                        <button @click="categoryOpen = !categoryOpen"
                                x-ref="categoryBtn"
                                type="button"
                                class="w-full px-4 py-2.5 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs font-bold flex items-center justify-between gap-2 shadow-2xs cursor-pointer transition focus:ring-2 focus:ring-rose-500 text-left outline-none"
                                :class="touched.category && errors.category ? 'border-red-400 focus:ring-red-400' : 'border-gray-200 dark:border-gray-700 hover:border-rose-400 dark:hover:border-rose-500'">
                            <span x-text="selectedCategory || 'Pilih Kategori Pelanggaran'"
                                  :class="!selectedCategory ? 'text-gray-400 font-medium' : 'text-gray-900 dark:text-white font-bold'"
                                  class="truncate"></span>
                            <svg class="w-4 h-4 text-gray-400 shrink-0 transition-transform duration-200"
                                 :class="categoryOpen ? 'rotate-180 text-rose-500' : ''"
                                 fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="categoryOpen"
                             @click.away="categoryOpen = false"
                             x-transition
                             class="absolute z-50 left-0 mt-2 w-full bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700/80 py-1 max-h-56 overflow-y-auto"
                             style="display: none;">
                            @php
                                $violations = [
                                    'Data Palsu atau Identitas Fiktif',
                                    'Pelanggaran Hak Cipta Portofolio',
                                    'Transaksi Terlarang di Luar Sistem',
                                    'Penipuan atau Penggelapan Dana',
                                    'Konten Melanggar Asusila atau Hukum',
                                    'Permintaan Penutupan oleh Mitra',
                                    'Lainnya'
                                ];
                            @endphp
                            @foreach($violations as $v)
                                <button type="button"
                                        @click="selectedCategory = '{{ addslashes($v) }}'; categoryOpen = false; touched.category = true; validateCategory()"
                                        class="w-full px-4 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-rose-50 dark:hover:bg-rose-950/40 hover:text-rose-600 transition flex items-center justify-between text-left cursor-pointer border-b border-gray-50 dark:border-gray-700/40 last:border-0"
                                        :class="selectedCategory === '{{ addslashes($v) }}' ? 'bg-rose-50/80 dark:bg-rose-950/40 text-rose-600 font-extrabold' : ''">
                                    <span>{{ $v }}</span>
                                    <span x-show="selectedCategory === '{{ addslashes($v) }}'" class="shrink-0">
                                        <svg class="w-4 h-4 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                    <p x-show="touched.category && errors.category" x-text="errors.category" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                        Deskripsi Alasan Penghapusan <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="alasan_penghapusan"
                              x-ref="alasanInput"
                              x-model="alasanText"
                              @input="touched.alasan = true; validateAlasan()"
                              @blur="touched.alasan = true; validateAlasan()"
                              rows="4"
                              placeholder="Tuliskan rincian alasan penghapusan akun fotografer ini..."
                              class="w-full px-4 py-3 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 resize-y leading-relaxed transition"
                              :class="touched.alasan && errors.alasan ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-rose-500'"></textarea>
                    <p x-show="touched.alasan && errors.alasan" x-text="errors.alasan" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
                </div>

                <div class="flex items-center justify-center gap-3 pt-2">
                    <button type="button" @click="deleteModalOpen = false; selectedCategory = ''; alasanText = ''; touched.category = false; touched.alasan = false; errors.category = ''; errors.alasan = ''" class="px-5 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300 font-bold text-xs transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs shadow-xs transition cursor-pointer">
                        Hapus Fotografer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Pratinjau Foto Resolusi Penuh (Rule #97) -->
    <div x-show="imagePreviewModalOpen"
         x-cloak
         @keydown.escape.window="closeImagePreview()"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 backdrop-blur-md p-4 sm:p-6"
         tabindex="-1">

        <button type="button"
                @click="closeImagePreview()"
                title="Tutup (Esc)"
                class="absolute top-4 right-4 sm:top-6 sm:right-6 w-11 h-11 rounded-full bg-white/10 hover:bg-white/25 text-white flex items-center justify-center transition cursor-pointer z-50">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        <div class="relative max-w-5xl max-h-[90vh] flex flex-col items-center justify-center space-y-3" @click.outside="closeImagePreview()">
            <img :src="imagePreviewSrc"
                 :alt="imagePreviewTitle"
                 draggable="false"
                 class="max-w-full max-h-[80vh] object-contain rounded-2xl shadow-2xl select-none border border-white/10">

            <div x-show="imagePreviewTitle" class="px-4 py-1.5 rounded-full bg-black/60 text-white/90 text-xs font-semibold backdrop-blur-sm border border-white/10 flex items-center gap-2">
                <span x-text="imagePreviewTitle"></span>
                <span class="text-white/40">|</span>
                <span class="text-white/60 text-[11px]">Tekan Esc atau klik di luar untuk menutup</span>
            </div>
        </div>
    </div>

</div>
@endsection
