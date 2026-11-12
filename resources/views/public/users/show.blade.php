@extends('layouts.app')

@section('title', $clientName . ' — Informasi Profil Pengguna LensMatch')

@section('content')
<div class="py-8 lg:py-12 bg-gray-50/50 dark:bg-gray-900/50 min-h-[calc(100vh-160px)]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- Breadcrumb Navigation -->
        <nav class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
            <a href="{{ url('/') }}" class="hover:text-amber-500 transition">Beranda</a>
            <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            <span class="text-gray-900 dark:text-white font-bold">Profil {{ $clientName }}</span>
        </nav>

        <!-- Top Navigation / Back Button -->
        <div class="flex items-center justify-between">
            <button type="button" 
                    onclick="window.history.length > 1 ? window.history.back() : window.location.href='/'" 
                    class="inline-flex items-center gap-2 text-xs font-bold text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                <span>Kembali</span>
            </button>

            @auth
                @if(Auth::user()->isPhotographer())
                    <a href="{{ route('photographer.chat') }}" 
                       class="text-xs font-bold text-amber-500 hover:text-amber-600 dark:hover:text-amber-400 transition flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        <span>Ke Ruang Chat Fotografer</span>
                    </a>
                @endif
            @endauth
        </div>

        <!-- Main Profile Header Card -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 sm:p-8 border border-gray-100 dark:border-gray-700/80 shadow-xs relative overflow-hidden">
            <!-- Subtle Top Decorative Banner Accent -->
            <div class="absolute top-0 inset-x-0 h-2 bg-gradient-to-r from-amber-400 via-amber-500 to-amber-600"></div>

            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6 text-center sm:text-left pt-2">
                
                <!-- Avatar with Initials Fallback -->
                <div class="relative shrink-0">
                    <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-full bg-amber-500 flex items-center justify-center text-white font-black text-2xl sm:text-3xl shadow-lg ring-4 ring-amber-100 dark:ring-amber-950/50 overflow-hidden">
                        @if($avatar)
                            <img src="{{ $avatar }}" 
                                 alt="{{ $clientName }}" 
                                 class="w-full h-full object-cover"
                                 onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        @endif
                        <div class="w-full h-full flex items-center justify-center" @if($avatar) style="display: none;" @endif>
                            <span>{{ $initials }}</span>
                        </div>
                    </div>
                </div>

                <!-- Bio & Public Details -->
                <div class="flex-1 space-y-2">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white tracking-tight">
                                {{ $clientName }}
                            </h1>
                            <div class="flex items-center justify-center sm:justify-start gap-2 mt-1">
                                <!-- Rule #11 Role Color: Klien = Emerald Green Pure Text -->
                                <span class="text-emerald-600 dark:text-emerald-400 font-bold text-xs tracking-wide">
                                    Klien Terdaftar
                                </span>
                                <span class="text-gray-300 dark:text-gray-600">•</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                                    Bergabung sejak {{ $joinedAt }}
                                </span>
                            </div>
                        </div>

                        <!-- Status Badge -->
                        <div class="inline-flex items-center justify-center px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-800/40 text-xs font-semibold self-center sm:self-start">
                            <span>Akun Aktif & Terverifikasi</span>
                        </div>
                    </div>

                    <!-- Client Trust & Engagement Highlights -->
                    <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300 leading-relaxed pt-2">
                        Pengguna resmi marketplace fotografi LensMatch. Terverifikasi untuk memesan sesi fotografi, bertransaksi aman melalui sistem garansi LensMatch, dan berpartisipasi dalam diskusi komunitas fotografi.
                    </p>

                    <!-- Action Row -->
                    @auth
                        @if(Auth::user()->isPhotographer() && $client)
                            <div class="pt-4 flex flex-wrap items-center justify-center sm:justify-start gap-3">
                                <a href="{{ route('photographer.chat.client', $client->id) }}" 
                                   class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-extrabold text-xs shadow-xs transition flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                    <span>Buka Percakapan Chat</span>
                                </a>
                            </div>
                        @endif
                    @endauth

                </div>

            </div>
        </div>

        <!-- Metric Cards (Rule #64 Anti-AI Slop Clean Minimalist: Uniform Size) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-100 dark:border-gray-700/80 shadow-xs space-y-1.5">
                <p class="text-xs font-black uppercase tracking-wider text-gray-500 dark:text-gray-400">
                    Sesi Foto Selesai
                </p>
                <p class="text-xs font-black text-gray-900 dark:text-white tracking-tight">
                    {{ $totalCompletedBookings }} Sesi
                </p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-100 dark:border-gray-700/80 shadow-xs space-y-1.5">
                <p class="text-xs font-black uppercase tracking-wider text-gray-500 dark:text-gray-400">
                    Ulasan Terverifikasi
                </p>
                <p class="text-xs font-black text-gray-900 dark:text-white tracking-tight">
                    {{ $reviewsCount }} Ulasan
                </p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-100 dark:border-gray-700/80 shadow-xs space-y-1.5">
                <p class="text-xs font-black uppercase tracking-wider text-gray-500 dark:text-gray-400">
                    Diskusi Komunitas
                </p>
                <p class="text-xs font-black text-gray-900 dark:text-white tracking-tight">
                    {{ $forumPostsCount }} Topik
                </p>
            </div>

        </div>

        <!-- Trust & Security Commitment Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700/80 shadow-xs space-y-4">
            <h3 class="text-sm font-black text-gray-900 dark:text-white flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                <span>Standar Keamanan Transaksi & Privasi LensMatch</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs text-gray-600 dark:text-gray-300">
                <div class="p-3.5 rounded-xl bg-gray-50 dark:bg-gray-850 border border-gray-100 dark:border-gray-700">
                    <p class="font-extrabold text-gray-900 dark:text-white mb-1">Proteksi Pembayaran Aman</p>
                    <p class="text-gray-500 dark:text-gray-400 leading-relaxed">
                        Dana pembayaran booking klien diamankan di rekening penampung resmi LensMatch hingga sesi foto tuntas dikonfirmasi kedua pihak.
                    </p>
                </div>
                <div class="p-3.5 rounded-xl bg-gray-50 dark:bg-gray-850 border border-gray-100 dark:border-gray-700">
                    <p class="font-extrabold text-gray-900 dark:text-white mb-1">Privasi Kontak Terjaga</p>
                    <p class="text-gray-500 dark:text-gray-400 leading-relaxed">
                        Informasi sensitif seperti nomor rekening dan alamat tempat tinggal dilindungi dan tidak ditampilkan secara publik demi keamanan pengguna.
                    </p>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

