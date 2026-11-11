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
    $storefrontChecklist = $storefrontChecklist ?? $photographer->getStorefrontChecklist();

    $requestedTab = request()->query('tab');
    $initialTab = 'profile';
    if ($requestedTab === 'storefront'
        || session('active_tab') === 'storefront'
        || $errors->has('catatan_penolakan_etalase')
        || ($photographer->isVerified() && $photographer->status_etalase === 'menunggu')) {
        $initialTab = 'storefront';
    }

    $accountName = trim((string) ($photographer->user?->nama ?? ''));
    $studioName = trim((string) ($photographer->nama ?? ''));
    $showDistinctAccountName = $accountName !== '' && strcasecmp($accountName, $studioName) !== 0;
@endphp

<div x-data="{
    activeTab: '{{ $initialTab }}',
    rejectModalOpen: {{ $errors->has('catatan_penolakan') ? 'true' : 'false' }},
    rejectStorefrontModalOpen: {{ $errors->has('catatan_penolakan_etalase') ? 'true' : 'false' }},
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
            @if($photographer->isPublishedToCatalog())
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
                @if($photographer->isPublishedToCatalog())
                    <span class="font-bold text-emerald-600 dark:text-emerald-400">Etalase Tayang</span>
                @elseif($photographer->status_etalase === 'menunggu')
                    <span class="font-bold text-blue-600 dark:text-blue-400">Review Etalase</span>
                @elseif($photographer->status_etalase === 'ditolak')
                    <span class="font-bold text-rose-600 dark:text-rose-400">Etalase Ditolak</span>
                @else
                    <span class="font-bold text-gray-500 dark:text-gray-400">Etalase Belum Diajukan</span>
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

    <!-- Navigasi Tab Audit 2 Tahap (Rule #78: Active Text Underline Standard & Counter Pill Badge) -->
    <div class="flex items-center gap-6 overflow-x-auto pb-1">
        <button type="button"
                @click="activeTab = 'profile'"
                class="py-2 text-xs sm:text-sm transition flex items-center gap-2 cursor-pointer shrink-0 focus:outline-none"
                :class="activeTab === 'profile' ? 'text-amber-600 dark:text-amber-400 font-black' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-bold'">
            <span :class="activeTab === 'profile' ? 'underline underline-offset-8 decoration-2 decoration-amber-500 dark:decoration-amber-400' : ''">Profil Mitra</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold"
                  :class="activeTab === 'profile' ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300'">
                {{ $completionPercentage }}%
            </span>
        </button>

        <button type="button"
                @click="activeTab = 'storefront'"
                class="py-2 text-xs sm:text-sm transition flex items-center gap-2 cursor-pointer shrink-0 focus:outline-none"
                :class="activeTab === 'storefront' ? 'text-amber-600 dark:text-amber-400 font-black' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-bold'">
            <span :class="activeTab === 'storefront' ? 'underline underline-offset-8 decoration-2 decoration-amber-500 dark:decoration-amber-400' : ''">Etalase Tayang</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold"
                  :class="activeTab === 'storefront' ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300'">
                {{ $storefrontChecklist['completed_count'] }}/{{ $storefrontChecklist['total_count'] }}
            </span>
        </button>
    </div>

    <!-- Main Profile Card (Layout 1:1 Mirror dengan Form Profil Fotografer) -->
    <div x-show="activeTab === 'profile'" @if($initialTab !== 'profile') x-cloak @endif class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-3xl border border-gray-200 dark:border-gray-700/80 shadow-xs space-y-6">

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
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                        Nomor WhatsApp
                    </label>
                    <div class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs {{ $photographer->nomor_telepon ? 'font-bold text-gray-900 dark:text-white' : 'font-medium text-gray-400 dark:text-gray-500' }}">
                        {{ $photographer->nomor_telepon ?: '-' }}
                    </div>
                </div>

                <!-- Instagram -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                        Instagram
                    </label>
                    <div class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs {{ $photographer->link_sosmed ? 'font-bold text-gray-900 dark:text-white' : 'font-medium text-gray-400 dark:text-gray-500' }}">
                        {{ $photographer->link_sosmed ?: '-' }}
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

    <!-- Main Storefront Audit Card (Tab 2: Etalase Tayang) -->
    <div x-show="activeTab === 'storefront'" @if($initialTab !== 'storefront') x-cloak @endif class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-3xl border border-gray-200 dark:border-gray-700/80 shadow-xs space-y-6">
        @if($photographer->isStorefrontRejected())
            <div class="p-5 rounded-2xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-900/60 space-y-1.5">
                <div class="text-rose-800 dark:text-rose-300 font-extrabold text-sm">
                    Pengajuan Etalase Ditolak
                </div>
                <p class="text-xs text-rose-700 dark:text-rose-400 leading-relaxed">
                    Catatan Admin: <span class="font-bold">{{ $photographer->catatan_penolakan_etalase ?: 'Etalase belum memenuhi persyaratan penayangan.' }}</span>
                </p>
            </div>
        @endif

        @if(!$photographer->isVerified() || !$isComplete)
            <div class="p-5 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900/60 space-y-1">
                <div class="text-amber-800 dark:text-amber-300 font-extrabold text-sm">
                    Profil Mitra Belum Terverifikasi
                </div>
                <p class="text-xs text-amber-700 dark:text-amber-400 leading-relaxed">
                    Verifikasi profil pada tab Profil Mitra harus disetujui terlebih dahulu sebelum etalase dapat ditayangkan.
                </p>
            </div>
        @endif

        <!-- Ringkasan Kesiapan 3 Komponen Etalase -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-4 sm:p-5 rounded-2xl bg-gray-50 dark:bg-gray-900/60 border border-gray-200 dark:border-gray-700/80 flex items-center justify-between gap-3">
                <div>
                    <span class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Paket Layanan</span>
                    <span class="text-sm font-black text-gray-900 dark:text-white">{{ $photographer->services->count() }} paket</span>
                </div>
                @if($storefrontChecklist['items']['services']['done'])
                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">Lengkap</span>
                @else
                    <span class="text-xs font-bold text-gray-400 dark:text-gray-500">Belum</span>
                @endif
            </div>

            <div class="p-4 sm:p-5 rounded-2xl bg-gray-50 dark:bg-gray-900/60 border border-gray-200 dark:border-gray-700/80 flex items-center justify-between gap-3">
                <div>
                    <span class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Portofolio</span>
                    <span class="text-sm font-black text-gray-900 dark:text-white">{{ $photographer->portfolios->count() }} album</span>
                </div>
                @if($storefrontChecklist['items']['portfolios']['done'])
                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">Lengkap</span>
                @else
                    <span class="text-xs font-bold text-gray-400 dark:text-gray-500">Belum</span>
                @endif
            </div>

            <div class="p-4 sm:p-5 rounded-2xl bg-gray-50 dark:bg-gray-900/60 border border-gray-200 dark:border-gray-700/80 flex items-center justify-between gap-3">
                <div>
                    <span class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jadwal</span>
                    <span class="text-sm font-black text-gray-900 dark:text-white">
                        {{ $photographer->jadwal_dikonfirmasi ? $photographer->workingHours->where('is_open', true)->count() . ' hari aktif' : 'Belum diatur' }}
                    </span>
                </div>
                @if($storefrontChecklist['items']['schedule']['done'])
                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">Lengkap</span>
                @else
                    <span class="text-xs font-bold text-gray-400 dark:text-gray-500">Belum</span>
                @endif
            </div>
        </div>

        <!-- SECTION 1: Paket Layanan -->
        <div class="space-y-4">
            <div class="flex items-center justify-between gap-2">
                <h2 class="text-sm font-black text-gray-900 dark:text-white">
                    1. Paket Layanan
                </h2>
                <span class="text-[11px] text-gray-400">{{ $photographer->services->count() }} paket</span>
            </div>

            @if($photographer->services->isEmpty())
                <div class="p-6 rounded-2xl bg-gray-50 dark:bg-gray-900/60 border border-dashed border-gray-300 dark:border-gray-700 text-xs font-medium text-gray-400 dark:text-gray-500 text-center">
                    Belum ada paket layanan.
                </div>
            @else
                <div class="space-y-4">
                    @foreach($photographer->services as $service)
                        @php
                            $serviceValid = (int) $service->tarif_harga > 0 && !empty(trim((string) $service->deskripsi_layanan));
                        @endphp
                        <div class="p-5 sm:p-6 rounded-2xl bg-gray-50 dark:bg-gray-900/60 border border-gray-200 dark:border-gray-700/80 space-y-3">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                <div class="flex flex-wrap items-center gap-2.5">
                                    <h3 class="text-sm font-black text-gray-900 dark:text-white">{{ $service->nama_layanan }}</h3>
                                    @if(!$serviceValid)
                                        <span class="text-[11px] font-bold text-rose-600 dark:text-rose-400">Data belum lengkap</span>
                                    @endif
                                </div>
                                <span class="text-sm font-black text-gray-900 dark:text-white shrink-0">
                                    Rp {{ number_format((int) $service->tarif_harga, 0, ',', '.') }}
                                </span>
                            </div>

                            <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed whitespace-pre-line">{{ $service->deskripsi_layanan ?: '-' }}</p>

                            @if($service->details->isNotEmpty())
                                <div class="pt-1 space-y-2">
                                    <span class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Fasilitas</span>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                        @foreach($service->details as $detail)
                                            <div class="px-3.5 py-2 rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-xs flex items-center justify-between gap-2">
                                                <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $detail->nama_fitur }}</span>
                                                @if((int) $detail->tarif_harga > 0)
                                                    <span class="font-bold text-gray-500 dark:text-gray-400 shrink-0">+Rp {{ number_format((int) $detail->tarif_harga, 0, ',', '.') }}</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- SECTION 2: Portofolio -->
        <div class="space-y-4 pt-2">
            <div class="flex items-center justify-between gap-2">
                <h2 class="text-sm font-black text-gray-900 dark:text-white">
                    2. Portofolio
                </h2>
                <span class="text-[11px] text-gray-400">{{ $photographer->portfolios->count() }} album</span>
            </div>

            @if($photographer->portfolios->isEmpty())
                <div class="p-6 rounded-2xl bg-gray-50 dark:bg-gray-900/60 border border-dashed border-gray-300 dark:border-gray-700 text-xs font-medium text-gray-400 dark:text-gray-500 text-center">
                    Belum ada portofolio.
                </div>
            @else
                <div class="space-y-4">
                    @foreach($photographer->portfolios as $portfolio)
                        <div class="p-5 sm:p-6 rounded-2xl bg-gray-50 dark:bg-gray-900/60 border border-gray-200 dark:border-gray-700/80 space-y-4">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                <div class="space-y-1">
                                    <h3 class="text-sm font-black text-gray-900 dark:text-white">{{ $portfolio->judul }}</h3>
                                    <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed">{{ $portfolio->deskripsi ?: '-' }}</p>
                                </div>
                                <span class="text-xs font-bold {{ $portfolio->medias->isEmpty() ? 'text-rose-600 dark:text-rose-400' : 'text-gray-500 dark:text-gray-400' }} shrink-0">
                                    {{ $portfolio->medias->count() }} foto
                                </span>
                            </div>

                            @if($portfolio->medias->isEmpty())
                                <div class="p-4 rounded-xl bg-white dark:bg-gray-900 border border-dashed border-gray-300 dark:border-gray-700 text-xs text-rose-600 dark:text-rose-400 font-semibold text-center">
                                    Album ini belum memiliki foto.
                                </div>
                            @else
                                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                                    @foreach($portfolio->medias as $mIdx => $mediaItem)
                                        @php
                                            $rawMedia = trim((string) $mediaItem->media);
                                            if ($rawMedia === '') {
                                                $mediaUrl = null;
                                            } elseif (str_starts_with($rawMedia, 'http')) {
                                                $mediaUrl = $rawMedia;
                                            } elseif (str_starts_with($rawMedia, 'storage/') || str_starts_with($rawMedia, '/storage/')) {
                                                $mediaUrl = asset(ltrim($rawMedia, '/'));
                                            } else {
                                                $mediaUrl = asset('storage/' . ltrim($rawMedia, '/'));
                                            }
                                        @endphp
                                        @if($mediaUrl)
                                            <div @click="openImagePreview({{ \Illuminate\Support\Js::from($mediaUrl) }}, {{ \Illuminate\Support\Js::from($portfolio->judul . ' — Foto ' . ($mIdx + 1)) }})"
                                                 class="relative aspect-[4/3] rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:border-amber-400 cursor-pointer group transition">
                                                <img src="{{ $mediaUrl }}"
                                                     alt="{{ $portfolio->judul }}"
                                                     class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                                                <div class="absolute inset-0 bg-black/45 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center text-white p-1">
                                                    <svg class="w-5 h-5 text-white mb-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607zM10.5 7.5v6m3-3h-6"/></svg>
                                                    <span class="text-[10px] font-bold">Pratinjau</span>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- SECTION 3: Jadwal -->
        <div class="space-y-4 pt-2">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h2 class="text-sm font-black text-gray-900 dark:text-white">
                    3. Jadwal
                </h2>
                @if($photographer->jadwal_dikonfirmasi && $photographer->workingHours->where('is_open', true)->isNotEmpty())
                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">
                        Dikonfirmasi ({{ $photographer->workingHours->where('is_open', true)->count() }} hari aktif)
                    </span>
                @elseif($photographer->jadwal_dikonfirmasi)
                    <span class="text-xs font-bold text-rose-600 dark:text-rose-400">
                        Semua hari ditandai libur
                    </span>
                @else
                    <span class="text-xs font-bold text-amber-600 dark:text-amber-400">
                        Belum dikonfirmasi fotografer
                    </span>
                @endif
            </div>

            @php
                $workingHours = $photographer->workingHours->sortBy('day_of_week');
                $upcomingBlocks = $photographer->availabilities->where('tanggal', '>=', date('Y-m-d'))->sortBy('tanggal')->values();
            @endphp

            @if($workingHours->isEmpty())
                <div class="p-6 rounded-2xl bg-gray-50 dark:bg-gray-900/60 border border-dashed border-gray-300 dark:border-gray-700 text-xs font-medium text-gray-400 dark:text-gray-500 text-center">
                    Jadwal mingguan belum diatur oleh fotografer.
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    @foreach($workingHours as $wh)
                        <div class="p-4 rounded-2xl bg-gray-50 dark:bg-gray-900/60 border border-gray-200 dark:border-gray-700/80 space-y-1">
                            <div class="text-xs font-black text-gray-900 dark:text-white">{{ $wh->day_name }}</div>
                            @if(!$wh->is_open)
                                <div class="text-xs font-bold text-gray-400 dark:text-gray-500">Libur</div>
                            @elseif($wh->is_24_hours)
                                <div class="text-xs font-bold text-emerald-600 dark:text-emerald-400">24 Jam</div>
                            @else
                                <div class="text-xs font-bold text-gray-800 dark:text-gray-200">
                                    {{ substr($wh->jam_buka, 0, 5) }} - {{ substr($wh->jam_tutup, 0, 5) }} WIB
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            @if($upcomingBlocks->isNotEmpty())
                <div class="p-5 rounded-2xl bg-gray-50 dark:bg-gray-900/60 border border-gray-200 dark:border-gray-700/80 space-y-3">
                    <span class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal Ditutup Mendatang</span>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        @foreach($upcomingBlocks as $block)
                            @php
                                $isFullDay = (!$block->jam_mulai && !$block->jam_selesai) || ($block->jam_mulai === '00:00:00' && $block->jam_selesai === '23:59:59');
                                $blockTimeLabel = $isFullDay
                                    ? 'Seharian penuh'
                                    : substr($block->jam_mulai, 0, 5) . ' - ' . substr($block->jam_selesai, 0, 5) . ' WIB';
                            @endphp
                            <div class="px-4 py-2.5 rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-xs flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="font-bold text-gray-900 dark:text-white">{{ date('d M Y', strtotime($block->tanggal)) }}</div>
                                    @if(!empty($block->keterangan))
                                        <div class="text-[11px] text-gray-500 dark:text-gray-400 truncate">{{ $block->keterangan }}</div>
                                    @endif
                                </div>
                                <span class="text-[11px] font-bold text-rose-600 dark:text-rose-400 shrink-0">{{ $blockTimeLabel }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Bottom Action Bar (Rule #74 & Rule #91: Rata Kanan, Emerald Setujui Tayang) -->
        <div class="flex flex-wrap items-center justify-end gap-3 pt-2">
            @if($photographer->isVerified() && in_array($photographer->status_etalase, ['menunggu', 'disetujui'], true))
                <button type="button"
                        @click="rejectStorefrontModalOpen = true"
                        class="px-5 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200/80 dark:border-rose-900/60 font-bold text-xs transition cursor-pointer">
                    Tolak Etalase
                </button>
            @endif

            @if($photographer->isStorefrontApproved())
                <button type="button"
                        @click="$dispatch('open-confirm-modal', {
                            title: 'Batalkan Tayang Etalase?',
                            message: {{ \Illuminate\Support\Js::from('Apakah Anda yakin ingin menonaktifkan penayangan etalase publik untuk ' . $photographer->nama . '?') }},
                            buttonText: 'Ya, Batalkan Tayang',
                            buttonClass: 'bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold shadow-xs',
                            type: 'warning',
                            actionUrl: '{{ route('admin.photographers.storefront.revoke', $photographer->id) }}',
                            method: 'POST'
                        })"
                        class="px-5 py-2.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200/80 dark:border-amber-900/60 font-bold text-xs transition cursor-pointer">
                    Batalkan Tayang
                </button>
            @elseif($photographer->isVerified() && $isComplete && $storefrontChecklist['is_ready'])
                <button type="button"
                        @click="$dispatch('open-confirm-modal', {
                            title: 'Setujui Tayang Etalase?',
                            message: {{ \Illuminate\Support\Js::from('Etalase ' . $photographer->nama . ' akan tampil di katalog publik setelah disetujui.') }},
                            buttonText: 'Ya, Setujui Tayang',
                            buttonClass: 'bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold shadow-xs',
                            type: 'success',
                            actionUrl: '{{ route('admin.photographers.storefront.approve', $photographer->id) }}',
                            method: 'POST'
                        })"
                        class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-xs transition cursor-pointer">
                    Setujui Tayang
                </button>
            @else
                <span class="text-xs text-gray-400 dark:text-gray-500">
                    @if(!$photographer->isVerified() || !$isComplete)
                        Verifikasi profil terlebih dahulu.
                    @else
                        Kurang {{ implode(', ', $storefrontChecklist['missing_labels']) }}.
                    @endif
                </span>
                <button type="button"
                        disabled
                        class="px-6 py-2.5 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500 font-bold text-xs cursor-not-allowed">
                    Setujui Tayang
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
                  novalidate
                  class="space-y-4"
                  x-data="adminRejectProfileForm()"
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

    <!-- Modal Tolak Pengajuan Etalase (Rule #59) -->
    <div x-show="rejectStorefrontModalOpen"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
         @keydown.escape.window="rejectStorefrontModalOpen = false">
        <div @click.away="rejectStorefrontModalOpen = false"
             class="w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/80 shadow-2xl overflow-hidden p-6 sm:p-7 space-y-5">

            <div class="text-center space-y-1">
                <h3 class="text-base sm:text-lg font-black text-gray-900 dark:text-white">
                    Tolak Pengajuan Etalase
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Berikan catatan perbaikan paket layanan, portofolio, atau jadwal agar fotografer dapat mengajukannya kembali.
                </p>
            </div>

            <form action="{{ route('admin.photographers.storefront.reject', $photographer->id) }}"
                  method="POST"
                  novalidate
                  class="space-y-4"
                  x-data="adminRejectStorefrontForm()"
                  @submit="submitForm($event)">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                        Catatan Alasan Penolakan <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="catatan_penolakan_etalase"
                              x-ref="catatanEtalaseInput"
                              x-model="catatan"
                              @input="touched = true; validate()"
                              @blur="touched = true; validate()"
                              rows="4"
                              placeholder="Tuliskan catatan perbaikan paket layanan, portofolio, atau jadwal."
                              class="w-full px-4 py-3 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 resize-y leading-relaxed transition"
                              :class="touched && error ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-rose-500'"></textarea>
                    <p x-show="touched && error" x-text="error" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
                </div>

                <div class="flex items-center justify-center gap-3 pt-2">
                    <button type="button" @click="rejectStorefrontModalOpen = false; error = ''; touched = false" class="px-5 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300 font-bold text-xs transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs shadow-xs transition cursor-pointer">
                        Tolak Etalase
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

            <form action="{{ route('admin.photographers.destroy', $photographer->id) }}" method="POST" novalidate class="space-y-4" x-data="adminDeletePhotographerForm()" @submit="submitForm($event)">
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

<script>
function adminRejectProfileForm() {
    return {
        catatan: @js(old('catatan_penolakan', $photographer->catatan_penolakan ?? '')),
        touched: {{ $errors->has('catatan_penolakan') ? 'true' : 'false' }},
        error: @js($errors->first('catatan_penolakan') ?? ''),
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
    };
}

function adminRejectStorefrontForm() {
    return {
        catatan: @js(old('catatan_penolakan_etalase', $photographer->catatan_penolakan_etalase ?? '')),
        touched: {{ $errors->has('catatan_penolakan_etalase') ? 'true' : 'false' }},
        error: @js($errors->first('catatan_penolakan_etalase') ?? ''),
        validate() {
            if (!this.catatan || !this.catatan.trim()) {
                this.error = 'Catatan alasan penolakan etalase wajib diisi.';
                return false;
            }
            if (this.catatan.trim().length < 5) {
                this.error = 'Catatan alasan penolakan etalase minimal 5 karakter.';
                return false;
            }
            this.error = '';
            return true;
        },
        submitForm(e) {
            this.touched = true;
            if (!this.validate()) {
                e.preventDefault();
                this.$refs.catatanEtalaseInput?.focus();
                return false;
            }
        }
    };
}

function adminDeletePhotographerForm() {
    return {
        categoryOpen: false,
        selectedCategory: @js(old('kategori_penghapusan', '')),
        alasanText: @js(old('alasan_penghapusan', '')),
        touched: {
            category: {{ $errors->has('kategori_penghapusan') ? 'true' : 'false' }},
            alasan: {{ $errors->has('alasan_penghapusan') ? 'true' : 'false' }}
        },
        errors: {
            category: @js($errors->first('kategori_penghapusan') ?? ''),
            alasan: @js($errors->first('alasan_penghapusan') ?? '')
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
    };
}
</script>
@endsection
