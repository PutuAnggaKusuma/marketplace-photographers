@extends('layouts.admin')

@section('title', 'Moderasi Ulasan & Rating Pelanggan Admin - LensMatch')

@section('content')
<div class="space-y-6">

    <!-- Page Title Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <span class="text-[11px] font-extrabold uppercase tracking-widest text-amber-600 dark:text-amber-400">Pusat Kepuasan Pelanggan</span>
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white tracking-tight">Moderasi Ulasan & Rating</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Pantau seluruh testimoni ulasan pelanggan, kelola status publikasi, dan bersihkan ulasan spam</p>
        </div>
    </div>

    <!-- 3 Stat Cards Summary -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Card 1: Total Ulasan -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Ulasan Masuk</span>
                <span class="p-2.5 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                </span>
            </div>
            <div class="mt-2">
                <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">{{ $totalReviews }} Testimoni</h2>
                <p class="text-[11px] text-amber-600 dark:text-amber-400 font-semibold mt-1">Ulasan Pelanggan Terdaftar</p>
            </div>
        </div>

        <!-- Card 2: Rata-Rata Rating Platform -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rata-Rata Rating</span>
                <span class="p-2.5 rounded-xl bg-amber-100 dark:bg-amber-900/60 text-amber-500 shrink-0">
                    <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                </span>
            </div>
            <div class="mt-2">
                <h2 class="text-2xl font-black text-amber-500 tracking-tight">⭐ {{ $avgRating }} / 5.0</h2>
                <p class="text-[11px] text-gray-500 dark:text-gray-400 font-medium mt-1">Skor Kepuasan Platform</p>
            </div>
        </div>

        <!-- Card 3: Total Ulasan Bintang 5 -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ulasan ⭐ 5 Sempurna</span>
                <span class="p-2.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 10h47M4 14h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </span>
            </div>
            <div class="mt-2">
                <h2 class="text-2xl font-black text-emerald-600 dark:text-emerald-400 tracking-tight">{{ $fiveStarReviews }} Ulasan</h2>
                <p class="text-[11px] text-gray-500 dark:text-gray-400 font-medium mt-1">Sangat Memuaskan</p>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm overflow-hidden space-y-4">
        
        <!-- Search Toolbar & Filter -->
        <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row items-center justify-between gap-4">
            <form method="GET" action="{{ route('admin.reviews') }}" class="relative w-full sm:w-80">
                <input type="text" 
                       name="search" 
                       value="{{ $search }}" 
                       placeholder="Cari ulasan, nama klien, atau studio..." 
                       class="w-full pl-9 pr-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </form>

            <div class="flex items-center gap-2 w-full sm:w-auto">
                <a href="{{ route('admin.reviews', ['rating' => 'all']) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $ratingFilter === 'all' ? 'bg-amber-500 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">Semua Rating</a>
                <a href="{{ route('admin.reviews', ['rating' => '5']) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $ratingFilter === '5' ? 'bg-amber-500 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">⭐ 5 Bintang</a>
                <a href="{{ route('admin.reviews', ['rating' => '4']) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $ratingFilter === '4' ? 'bg-amber-500 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">⭐ 4 Bintang</a>
                <a href="{{ route('admin.reviews', ['rating' => 'low']) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $ratingFilter === 'low' ? 'bg-rose-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">⭐ 1-3 Bintang</a>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-[11px] font-extrabold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        <th class="py-3.5 px-4 sm:px-6 w-3/12">Pemberi & Penerima</th>
                        <th class="py-3.5 px-4 sm:px-6 w-1/12 text-center">Rating</th>
                        <th class="py-3.5 px-4 sm:px-6 w-4/12">Isi Pesan Testimoni</th>
                        <th class="py-3.5 px-4 sm:px-6 w-2/12 text-center">Status Publikasi</th>
                        <th class="py-3.5 px-4 sm:px-6 w-2/12 text-center">Aksi Moderasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60 text-xs">
                    @forelse($reviews as $rev)
                        <tr class="hover:bg-amber-50/30 dark:hover:bg-amber-950/20 transition">
                            <td class="py-4 px-4 sm:px-6 space-y-1">
                                <div class="font-extrabold text-gray-900 dark:text-white flex items-center gap-1.5">
                                    <span>Klien: {{ $rev->client->nama ?? 'Klien' }}</span>
                                </div>
                                <div class="text-[10px] text-amber-600 dark:text-amber-400 font-bold">
                                    Studio: {{ $rev->photographer->nama ?? 'Studio' }}
                                </div>
                            </td>

                            <td class="py-4 px-4 sm:px-6 text-center whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-black bg-amber-50 text-amber-600 dark:bg-amber-950/60 dark:text-amber-400 border border-amber-200/60">
                                    ⭐ {{ $rev->rating }}.0
                                </span>
                            </td>

                            <td class="py-4 px-4 sm:px-6 text-gray-700 dark:text-gray-300 leading-relaxed">
                                <p class="line-clamp-2">"{{ $rev->deskripsi_review }}"</p>
                                <span class="text-[10px] text-gray-400 block mt-1">{{ $rev->created_at->diffForHumans() }}</span>
                            </td>

                            <td class="py-4 px-4 sm:px-6 text-center whitespace-nowrap">
                                @if($rev->is_hidden)
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-rose-50 text-rose-600 dark:bg-rose-950/60 dark:text-rose-400 border border-rose-200/60">
                                        Disembunyikan
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-400 border border-emerald-200/60">
                                        Dipublikasikan
                                    </span>
                                @endif
                            </td>

                            <td class="py-4 px-4 sm:px-6 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <form action="{{ route('admin.reviews.toggle_hide', $rev->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $rev->is_hidden ? 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs' : 'bg-gray-100 hover:bg-rose-100 text-gray-700 hover:text-rose-700 dark:bg-gray-700 dark:hover:bg-rose-950/60 dark:text-gray-300' }}">
                                            {{ $rev->is_hidden ? 'Tampilkan' : 'Sembunyikan' }}
                                        </button>
                                    </form>

                                    <button type="button" 
                                            @click="$dispatch('open-confirm-modal', {
                                                title: 'Hapus Ulasan Testimoni?',
                                                message: 'Apakah Anda yakin ingin menghapus ulasan ini?',
                                                confirmText: 'Ya, Hapus Ulasan',
                                                type: 'danger',
                                                actionUrl: '{{ route('admin.reviews.destroy', $rev->id) }}',
                                                method: 'POST'
                                            })"
                                            class="px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 dark:bg-rose-950/60 dark:text-rose-300 font-bold text-xs transition">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-gray-400 font-bold text-xs">
                                Belum ada ulasan testimoni ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reviews->hasPages())
            <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>

</div>
@endsection