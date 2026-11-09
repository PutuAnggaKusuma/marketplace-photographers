@extends('layouts.admin')

@section('title', 'Verifikasi Fotografer — Admin')

@section('content')
<div x-data="{ rejectModalOpen: false, deleteModalOpen: false, imagePreviewModalOpen: false, imagePreviewSrc: '', imagePreviewTitle: '' }" class="space-y-6 pb-12">

    <!-- 1. BREADCRUMB & HEADER SECTION -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <!-- Breadcrumb Navigation (Rule #62) -->
            <nav class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 mb-1.5">
                <a href="{{ route('admin.photographers') }}" class="hover:text-amber-500 transition">Manajemen Fotografer</a>
                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                <span class="text-gray-900 dark:text-white font-bold">{{ $photographer->nama }}</span>
            </nav>
            <h1 class="text-xl sm:text-2xl font-black text-gray-900 dark:text-white tracking-tight">
                Verifikasi Fotografer
            </h1>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.photographers') }}" 
               class="px-4 py-2 rounded-xl bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-750 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 font-bold text-xs transition inline-flex items-center gap-1.5 shadow-2xs shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <!-- FLASH MESSAGES -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 text-xs text-emerald-800 dark:text-emerald-300 font-bold flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 text-xs text-rose-800 dark:text-rose-300 font-bold flex items-center gap-2">
            <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @php
        $rawAvatar = $photographer->foto;
        $avatarUrl = $rawAvatar ? (str_starts_with($rawAvatar, 'http') ? $rawAvatar : asset('storage/' . $rawAvatar)) : null;
        $initials = strtoupper(substr($photographer->nama ?: ($photographer->user->nama ?? 'F'), 0, 2));

        $rawSampul = $photographer->foto_sampul;
        $sampulUrl = $rawSampul ? (str_starts_with($rawSampul, 'http') ? $rawSampul : asset('storage/' . $rawSampul)) : null;

        $missingCount = count($missingFields);
    @endphp

    <!-- 2. STATUS & PROGRESS CARD -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-xs p-6 sm:p-7 space-y-5">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <!-- Left: Identity Overview -->
            <div class="flex items-center gap-4">
                @if($avatarUrl)
                    <img src="{{ $avatarUrl }}" alt="{{ $photographer->nama }}" class="w-16 h-16 rounded-2xl object-cover border-2 border-amber-400 shadow-xs shrink-0">
                @else
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-amber-500 to-amber-300 text-white font-black text-xl flex items-center justify-center shrink-0 shadow-xs">
                        {{ $initials }}
                    </div>
                @endif
                <div class="space-y-1">
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h2 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white leading-tight">
                            {{ $photographer->nama }}
                        </h2>
                        @if($photographer->status_verifikasi === 'terverifikasi')
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-900/60">
                                Terverifikasi
                            </span>
                        @elseif($photographer->status_verifikasi === 'ditolak')
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200/80 dark:border-rose-900/60">
                                Pendaftaran Ditolak
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200/80 dark:border-amber-900/60">
                                Menunggu Verifikasi
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Akun: <span class="font-bold text-gray-700 dark:text-gray-300">{{ $photographer->user->nama ?? '-' }}</span> ({{ $photographer->user->email ?? '-' }})
                    </p>
                    <p class="text-[11px] text-gray-400">
                        Terdaftar: {{ $photographer->created_at ? $photographer->created_at->format('d M Y, H:i') : '-' }}
                    </p>

                    @if($photographer->status_verifikasi === 'ditolak' && $photographer->catatan_penolakan)
                        <div class="mt-2.5 p-3 rounded-xl bg-rose-50/80 dark:bg-rose-950/50 border border-rose-200/80 dark:border-rose-900/60 text-xs">
                            <span class="font-bold text-rose-800 dark:text-rose-300 block mb-0.5">Catatan Penolakan:</span>
                            <p class="text-rose-700 dark:text-rose-400 leading-relaxed">{{ $photographer->catatan_penolakan }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right: Metric Kelengkapan (Gaya 3B: Dot Step Indicator - Anti AI Slop) -->
            @php
                $sectionStatus = $photographer->getProfileSectionsStatus();
            @endphp
            <div class="lg:w-80 p-4 rounded-2xl bg-gray-50 dark:bg-gray-900/60 border border-gray-100 dark:border-gray-700/60 space-y-1.5">
                <div class="flex items-center justify-between text-xs">
                    <span class="font-bold text-gray-700 dark:text-gray-300">Kelengkapan</span>
                    <div class="flex items-center gap-2">
                        <div class="flex items-center gap-1.5" title="{{ $sectionStatus['completed_count'] }} dari {{ $sectionStatus['total_sections'] }} bagian lengkap">
                            @foreach($sectionStatus['sections'] as $sec)
                                <span class="w-2 h-2 rounded-full inline-block {{ $sec['is_complete'] ? 'bg-emerald-500' : 'bg-gray-300 dark:bg-gray-600' }}" title="{{ $sec['name'] }}: {{ $sec['is_complete'] ? 'Lengkap' : 'Belum lengkap' }}"></span>
                            @endforeach
                        </div>
                        <span class="font-black text-gray-900 dark:text-white">{{ $completionPercentage }}%</span>
                    </div>
                </div>
                <div class="text-[11px] text-gray-500 dark:text-gray-400">
                    @if($isComplete)
                        <span class="text-emerald-600 dark:text-emerald-400 font-bold">Semua data lengkap • Siap diverifikasi</span>
                    @else
                        <span>{{ $sectionStatus['completed_count'] }} dari {{ $sectionStatus['total_sections'] }} bagian lengkap</span>
                        <span class="text-rose-600 dark:text-rose-400 font-semibold">• Kurang {{ $missingCount }} data</span>
                    @endif
                </div>
            </div>
        </div>

        @if(!$isComplete && count($missingFields) > 0)
            <div class="pt-3 border-t border-gray-100 dark:border-gray-700/60 text-xs text-gray-600 dark:text-gray-400">
                <span class="font-bold text-gray-700 dark:text-gray-300">Belum diisi:</span>
                <span class="text-gray-500 dark:text-gray-400 ml-1 leading-relaxed">{{ implode(', ', array_values($missingFields)) }}.</span>
            </div>
        @endif
    </div>

    <!-- 3. DATA AUDIT (Hybrid Responsive Layout - Rule #73 & Rule #96) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">

        <!-- KARTU 1: Kontak -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-xs p-6 space-y-4">
            <h3 class="text-xs font-black uppercase tracking-wider text-gray-400 dark:text-gray-500">
                1. Kontak
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <span class="text-gray-400 block text-[11px]">Nama Lengkap</span>
                    <span class="font-bold text-gray-900 dark:text-white text-sm">{{ $photographer->nama }}</span>
                </div>

                <div>
                    <span class="text-gray-400 block text-[11px]">Email</span>
                    @if($photographer->user?->email)
                        <span class="font-bold text-gray-900 dark:text-white">{{ $photographer->user->email }}</span>
                    @else
                        <span class="font-medium text-gray-400">-</span>
                    @endif
                </div>

                <div>
                    <span class="text-gray-400 block text-[11px]">Nomor WhatsApp</span>
                    @if($photographer->nomor_telepon)
                        @php
                            $waClean = preg_replace('/[^0-9]/', '', $photographer->nomor_telepon);
                            if (str_starts_with($waClean, '0')) {
                                $waClean = '62' . substr($waClean, 1);
                            }
                        @endphp
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="font-bold text-gray-900 dark:text-white">{{ $photographer->nomor_telepon }}</span>
                            <a href="https://wa.me/{{ $waClean }}" target="_blank" class="text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 hover:bg-emerald-100 dark:bg-emerald-950/60 dark:text-emerald-300 transition">
                                Chat
                            </a>
                        </div>
                    @else
                        <span class="font-medium text-gray-400">-</span>
                    @endif
                </div>

                <div>
                    <span class="text-gray-400 block text-[11px]">Instagram</span>
                    @if($photographer->link_sosmed)
                        <a href="{{ str_starts_with($photographer->link_sosmed, 'http') ? $photographer->link_sosmed : 'https://' . $photographer->link_sosmed }}" 
                           target="_blank" 
                           class="font-bold text-amber-600 dark:text-amber-400 hover:underline truncate block max-w-[200px]">
                            {{ $photographer->link_sosmed }}
                        </a>
                    @else
                        <span class="font-medium text-gray-400">-</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- KARTU 2: Domisili & Kategori -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-xs p-6 space-y-4">
            <h3 class="text-xs font-black uppercase tracking-wider text-gray-400 dark:text-gray-500">
                2. Domisili & Kategori
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <span class="text-gray-400 block text-[11px]">Provinsi</span>
                    @if($photographer->province)
                        <span class="font-bold text-gray-900 dark:text-white">{{ $photographer->province->name }}</span>
                    @else
                        <span class="font-medium text-gray-400">-</span>
                    @endif
                </div>

                <div>
                    <span class="text-gray-400 block text-[11px]">Kota / Kabupaten</span>
                    @if($photographer->city)
                        <span class="font-bold text-gray-900 dark:text-white">{{ $photographer->city->name }}</span>
                    @else
                        <span class="font-medium text-gray-400">-</span>
                    @endif
                </div>

                <div class="sm:col-span-2">
                    <span class="text-gray-400 block text-[11px]">Alamat</span>
                    @if($photographer->alamat)
                        <p class="font-medium text-gray-800 dark:text-gray-200 leading-relaxed mt-0.5">
                            {{ $photographer->alamat }}
                        </p>
                    @else
                        <span class="font-medium text-gray-400 block mt-0.5">-</span>
                    @endif
                </div>

                <div class="sm:col-span-2">
                    <span class="text-gray-400 block text-[11px] mb-1.5">Kategori</span>
                    @if($photographer->categories->count() > 0)
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($photographer->categories as $cat)
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200/80 dark:border-amber-900/60">
                                    {{ $cat->nama_kategori }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <span class="font-medium text-gray-400">Belum diatur</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- KARTU 3: Bio -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-xs p-6 space-y-4">
            <h3 class="text-xs font-black uppercase tracking-wider text-gray-400 dark:text-gray-500">
                3. Bio
            </h3>

            <div>
                <span class="text-gray-400 block text-[11px] mb-1.5">Bio</span>
                <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-900/60 border border-gray-100 dark:border-gray-700/60 min-h-[110px]">
                    @if($photographer->deskripsi_bio)
                        <p class="text-xs text-gray-800 dark:text-gray-200 leading-relaxed whitespace-pre-line">
                            {{ $photographer->deskripsi_bio }}
                        </p>
                    @else
                        <p class="text-xs text-gray-400 font-medium leading-relaxed">
                            Belum diisi
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <!-- KARTU 4: Foto Profil & Sampul (Layout Vertikal & Proporsional - Rule #96) -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-xs p-6 space-y-5">
            <h3 class="text-xs font-black uppercase tracking-wider text-gray-400 dark:text-gray-500">
                4. Foto Profil & Sampul
            </h3>

            <div class="space-y-5 text-xs">
                <!-- Foto Profil Preview (Vertikal & Interaktif - Rule #97) -->
                <div>
                    <span class="text-gray-400 block text-[11px] mb-2 font-bold">Foto Profil</span>
                    <div class="w-24 h-24 rounded-2xl overflow-hidden bg-gray-100 dark:bg-gray-800 border-2 {{ $avatarUrl ? 'border-amber-400 border-solid cursor-pointer group' : 'border-dashed border-gray-300 dark:border-gray-600' }} shadow-2xs flex items-center justify-center relative transition"
                         @if($avatarUrl)
                             @click="imagePreviewSrc = '{{ $avatarUrl }}'; imagePreviewTitle = 'Foto Profil — {{ addslashes($photographer->nama) }}'; imagePreviewModalOpen = true"
                         @endif>
                        @if($avatarUrl)
                            <img src="{{ $avatarUrl }}" alt="{{ $photographer->nama }}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                            <div class="absolute inset-0 bg-black/45 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center text-white text-[10px] font-bold">
                                <svg class="w-5 h-5 text-white mb-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607zM10.5 7.5v6m3-3h-6"/></svg>
                                <span>Pratinjau</span>
                            </div>
                        @else
                            <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                            </svg>
                        @endif
                    </div>
                </div>

                <!-- Foto Sampul Preview (Vertikal & Proporsional 16:9 & Interaktif - Rule #96 & Rule #97) -->
                <div>
                    <span class="text-gray-400 block text-[11px] mb-2 font-bold">Foto Sampul (Rasio 16:9)</span>
                    <div class="w-full h-48 sm:h-56 rounded-2xl overflow-hidden bg-gray-100 dark:bg-gray-800 border-2 {{ $sampulUrl ? 'border-amber-400 border-solid cursor-pointer group' : 'border-dashed border-gray-300 dark:border-gray-600' }} shadow-2xs flex items-center justify-center relative transition"
                         @if($sampulUrl)
                             @click="imagePreviewSrc = '{{ $sampulUrl }}'; imagePreviewTitle = 'Foto Sampul — {{ addslashes($photographer->nama) }}'; imagePreviewModalOpen = true"
                         @endif>
                        @if($sampulUrl)
                            <img src="{{ $sampulUrl }}" alt="Sampul {{ $photographer->nama }}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-102">
                            <div class="absolute inset-0 bg-black/45 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white p-2">
                                <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-black/60 backdrop-blur-xs text-xs font-bold text-white shadow-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607zM10.5 7.5v6m3-3h-6"/></svg>
                                    <span>Klik untuk Pratinjau Foto Resolusi Penuh</span>
                                </span>
                            </div>
                        @else
                            <svg class="w-10 h-10 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                            </svg>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- KARTU 5: Rekening Bank -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-xs p-6 space-y-4 lg:col-span-2">
            <h3 class="text-xs font-black uppercase tracking-wider text-gray-400 dark:text-gray-500">
                5. Rekening Bank
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs p-4 rounded-xl bg-gray-50 dark:bg-gray-900/60 border border-gray-200/80 dark:border-gray-700/60">
                <div>
                    <span class="text-gray-400 block text-[11px]">Nama Bank</span>
                    @if($photographer->nama_bank)
                        <span class="font-bold text-gray-900 dark:text-white text-sm">{{ $photographer->nama_bank }}</span>
                    @else
                        <span class="font-medium text-gray-400">-</span>
                    @endif
                </div>

                <div>
                    <span class="text-gray-400 block text-[11px]">Nomor Rekening</span>
                    @if($photographer->nomor_rekening)
                        <span class="font-mono font-extrabold text-gray-900 dark:text-white text-sm tracking-wide">{{ $photographer->nomor_rekening }}</span>
                    @else
                        <span class="font-medium text-gray-400">-</span>
                    @endif
                </div>

                <div>
                    <span class="text-gray-400 block text-[11px]">Nama Pemilik Rekening</span>
                    @if($photographer->atas_nama_rekening)
                        <span class="font-bold text-gray-900 dark:text-white text-sm">{{ $photographer->atas_nama_rekening }}</span>
                    @else
                        <span class="font-medium text-gray-400">-</span>
                    @endif
                </div>
            </div>
        </div>

    </div>

    <!-- 4. BOTTOM ACTION BAR (Rule #74 & Rule #91) -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 p-5 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.photographers') }}" 
               class="px-5 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300 font-bold text-xs transition inline-flex items-center gap-1.5 cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                <span>Kembali</span>
            </a>

            @if($photographer->is_onboarded && $photographer->status_verifikasi === 'terverifikasi')
                <a href="{{ route('public.photographers.show', $photographer->id) }}" 
                   target="_blank"
                   class="px-4 py-2.5 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-750 font-bold text-xs inline-flex items-center gap-1.5 transition">
                    <span>Lihat Etalase Publik</span>
                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                </a>
            @endif
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <!-- Tombol Hapus (Rose Soft) -->
            <button type="button" 
                    @click="deleteModalOpen = true"
                    class="px-4 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200/80 dark:border-rose-900/60 font-bold text-xs transition cursor-pointer">
                Hapus
            </button>

            <!-- Tombol Tolak Pendaftaran (Tampil jika belum berstatus ditolak) -->
            @if($photographer->status_verifikasi !== 'ditolak')
                <button type="button" 
                        @click="rejectModalOpen = true"
                        class="px-4 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200/80 dark:border-rose-900/60 font-bold text-xs transition cursor-pointer">
                    Tolak Pendaftaran
                </button>
            @endif

            <!-- Tombol Verifikasi / Batalkan Verifikasi -->
            @if($photographer->status_verifikasi === 'terverifikasi')
                <button type="button" 
                        @click="$dispatch('open-confirm-modal', {
                            title: 'Batalkan Verifikasi Fotografer?',
                            message: 'Apakah Anda yakin ingin membatalkan status verifikasi untuk {{ addslashes($photographer->nama) }}?',
                            confirmText: 'Ya, Batalkan Verifikasi',
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
                            message: 'Apakah Anda yakin ingin menyetujui dan memverifikasi pendaftaran {{ addslashes($photographer->nama) }}?',
                            confirmText: 'Ya, Verifikasi Fotografer',
                            type: 'info',
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
                    Berikan catatan perbaikan agar fotografer dapat memperbaiki dan melengkapi datanya.
                </p>
            </div>

            <form action="{{ route('admin.photographers.reject', $photographer->id) }}" 
                  method="POST" 
                  class="space-y-4"
                  x-data="{
                    catatan: {!! json_encode(old('catatan_penolakan', $photographer->catatan_penolakan ?? '')) !!},
                    touched: false,
                    error: '',
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
                              placeholder="Contoh: Nomor rekening tidak sesuai dengan nama pemilik, silakan perbaiki..." 
                              class="w-full px-4 py-3 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 resize-y leading-relaxed transition"
                              :class="touched && error ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-rose-500'"></textarea>
                    <p x-show="touched && error" x-text="error" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
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

    <!-- Modal Hapus Fotografer dengan Kategori & Alasan Pelanggaran (Rule #59) -->
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
                    Studio: <span class="font-bold text-gray-800 dark:text-gray-200">{{ $photographer->nama }}</span>
                </p>
            </div>

            <form action="{{ route('admin.photographers.destroy', $photographer->id) }}" method="POST" class="space-y-4" x-data="{
                categoryOpen: false,
                selectedCategory: '',
                alasanText: '',
                touched: {
                    category: false,
                    alasan: false
                },
                errors: {
                    category: '',
                    alasan: ''
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

                <div class="flex items-center justify-end gap-3 pt-2">
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

    <!-- 6. MODAL PRATINJAU FOTO RESOLUSI PENUH (Rule #97: Standarisasi Pratinjau Modal Lightbox Media) -->
    <div x-show="imagePreviewModalOpen" 
         x-cloak 
         @keydown.escape.window="imagePreviewModalOpen = false; imagePreviewSrc = ''; imagePreviewTitle = ''"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 backdrop-blur-md p-4 sm:p-6"
         tabindex="-1">
      
        <!-- Tombol Tutup -->
        <button type="button" 
                @click="imagePreviewModalOpen = false; imagePreviewSrc = ''; imagePreviewTitle = ''" 
                title="Tutup (Esc)" 
                class="absolute top-4 right-4 sm:top-6 sm:right-6 w-11 h-11 rounded-full bg-white/10 hover:bg-white/25 text-white flex items-center justify-center transition cursor-pointer z-50">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        <!-- Container Gambar Utama -->
        <div class="relative max-w-5xl max-h-[90vh] flex flex-col items-center justify-center space-y-3" @click.outside="imagePreviewModalOpen = false; imagePreviewSrc = ''; imagePreviewTitle = ''">
            <img :src="imagePreviewSrc" 
                 :alt="imagePreviewTitle" 
                 draggable="false"
                 class="max-w-full max-h-[80vh] object-contain rounded-2xl shadow-2xl select-none border border-white/10">
            
            <div x-show="imagePreviewTitle" class="px-4 py-1.5 rounded-full bg-black/60 text-white/90 text-xs font-semibold backdrop-blur-sm border border-white/10 flex items-center gap-2">
                <span x-text="imagePreviewTitle"></span>
                <span class="text-white/40">•</span>
                <span class="text-white/60 text-[11px]">Tekan Esc atau klik di luar untuk menutup</span>
            </div>
        </div>
    </div>

</div>
@endsection
