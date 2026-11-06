@extends('layouts.admin')

@section('title', 'Moderasi Ulasan Klien Pelanggan Admin - LensMatch')

@section('content')
<div x-data="dashboardLiveTable()" class="space-y-6">

    <!-- Page Title Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">Ulasan Klien</h1>
        </div>
    </div>

    <!-- 4 Stat Cards Summary -->
    <div id="dashboard-stats-container">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Ulasan -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
            <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Total Ulasan Masuk</span>
            <h2 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ $totalReviews }} Ulasan</h2>
        </div>

        <!-- Card 2: Rata-Rata Rating Platform -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
            <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Rata-Rata Rating</span>
            <h2 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight flex items-center gap-1.5">
                <span>{{ $avgRating }}</span>
                <svg class="w-5 h-5 fill-amber-400 text-amber-400 shrink-0" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
            </h2>
        </div>

        <!-- Card 3: Total Ulasan Bintang 5 -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
            <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Testimoni Bintang 5</span>
            <h2 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ $fiveStarReviews }} Ulasan</h2>
        </div>

        <!-- Card 4: Ulasan Disembunyikan -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
            <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Ulasan Disembunyikan</span>
            <h2 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ $hiddenReviews }} Ulasan</h2>
        </div>
    </div>

    </div>

    <!-- Data Table Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm overflow-hidden space-y-4">
        
        <!-- Search Toolbar & Filter (Alpine.js Custom Floating Dropdown) -->
        <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex flex-col md:flex-row items-center justify-between gap-4">
            <!-- Filter Dropdowns di Kiri -->
            <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                <!-- 1. Dropdown Filter Rating -->
                @php
                    $ratingLabels = [
                        'all' => 'Semua Rating Bintang',
                        '5' => '5 Bintang (Sempurna)',
                        '4' => '4 Bintang (Puas)',
                        'low' => '1-3 Bintang (Kritik)'
                    ];
                    $curRatingName = $ratingLabels[$ratingFilter] ?? 'Semua Rating Bintang';
                @endphp
                <div class="relative w-full sm:w-auto min-w-[180px]" x-data="{ open: false, selectedId: '{{ $ratingFilter }}', selectedName: '{{ $curRatingName }}' }">
                    <input type="hidden" name="rating" :value="selectedId">
                    <button @click="open = !open" type="button" 
                            class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs font-bold text-gray-800 dark:text-white flex items-center justify-between gap-2 shadow-2xs hover:border-amber-400 dark:hover:border-amber-500 cursor-pointer transition">
                        <span x-text="selectedName" class="truncate"></span>
                        <svg class="w-3.5 h-3.5 text-gray-400 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" @click.away="open = false" x-transition 
                         class="absolute z-50 left-0 mt-2 min-w-[190px] w-full bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700/80 py-1 max-h-60 overflow-y-auto" style="display: none;">
                        @foreach($ratingLabels as $rtKey => $rtVal)
                            <button type="button" @click="selectedId = '{{ $rtKey }}'; selectedName = '{{ $rtVal }}'; open = false; applyFilter('rating', '{{ $rtKey }}')" 
                                    class="w-full px-3.5 py-2 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition flex items-center justify-between text-left cursor-pointer border-b border-gray-50 dark:border-gray-700/40 last:border-0"
                                    :class="selectedId === '{{ $rtKey }}' ? 'bg-amber-50/70 dark:bg-amber-950/30 text-amber-600 font-extrabold' : ''">
                                <span>{{ $rtVal }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- 2. Dropdown Filter Status Publikasi -->
                @php
                    $statusPubLabels = [
                        'all' => 'Semua Status Publikasi',
                        'published' => 'Dipublikasikan Aktif',
                        'hidden' => 'Disembunyikan (Hidden)'
                    ];
                    $curPubName = $statusPubLabels[$statusFilter ?? 'all'] ?? 'Semua Status Publikasi';
                @endphp
                <div class="relative w-full sm:w-auto min-w-[180px]" x-data="{ open: false, selectedId: '{{ $statusFilter ?? 'all' }}', selectedName: '{{ $curPubName }}' }">
                    <input type="hidden" name="status" :value="selectedId">
                    <button @click="open = !open" type="button" 
                            class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs font-bold text-gray-800 dark:text-white flex items-center justify-between gap-2 shadow-2xs hover:border-amber-400 dark:hover:border-amber-500 cursor-pointer transition">
                        <span x-text="selectedName" class="truncate"></span>
                        <svg class="w-3.5 h-3.5 text-gray-400 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" @click.away="open = false" x-transition 
                         class="absolute z-50 left-0 mt-2 min-w-[190px] w-full bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700/80 py-1 max-h-60 overflow-y-auto" style="display: none;">
                        @foreach($statusPubLabels as $spKey => $spVal)
                            <button type="button" @click="selectedId = '{{ $spKey }}'; selectedName = '{{ $spVal }}'; open = false; applyFilter('status', '{{ $spKey }}')" 
                                    class="w-full px-3.5 py-2 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition flex items-center justify-between text-left cursor-pointer border-b border-gray-50 dark:border-gray-700/40 last:border-0"
                                    :class="selectedId === '{{ $spKey }}' ? 'bg-amber-50/70 dark:bg-amber-950/30 text-amber-600 font-extrabold' : ''">
                                <span>{{ $spVal }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- 3. Dropdown Urutan / Sort -->
                @php
                    $sortLabels = [
                        'latest' => 'Urutan: Ulasan Terbaru',
                        'oldest' => 'Urutan: Ulasan Terlama',
                        'highest_rating' => 'Rating Tertinggi',
                        'lowest_rating' => 'Rating Terendah'
                    ];
                    $curSortName = $sortLabels[$sort ?? 'latest'] ?? 'Urutan: Ulasan Terbaru';
                @endphp
                <div class="relative w-full sm:w-auto min-w-[180px]" x-data="{ open: false, selectedId: '{{ $sort ?? 'latest' }}', selectedName: '{{ $curSortName }}' }">
                    <input type="hidden" name="sort" :value="selectedId">
                    <button @click="open = !open" type="button" 
                            class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs font-bold text-gray-800 dark:text-white flex items-center justify-between gap-2 shadow-2xs hover:border-amber-400 dark:hover:border-amber-500 cursor-pointer transition">
                        <span x-text="selectedName" class="truncate"></span>
                        <svg class="w-3.5 h-3.5 text-gray-400 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" @click.away="open = false" x-transition 
                         class="absolute z-50 left-0 mt-2 min-w-[190px] w-full bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700/80 py-1 max-h-60 overflow-y-auto" style="display: none;">
                        @foreach($sortLabels as $srtKey => $srtVal)
                            <button type="button" @click="selectedId = '{{ $srtKey }}'; selectedName = '{{ $srtVal }}'; open = false; applyFilter('sort', '{{ $srtKey }}')" 
                                    class="w-full px-3.5 py-2 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition flex items-center justify-between text-left cursor-pointer border-b border-gray-50 dark:border-gray-700/40 last:border-0"
                                    :class="selectedId === '{{ $srtKey }}' ? 'bg-amber-50/70 dark:bg-amber-950/30 text-amber-600 font-extrabold' : ''">
                                <span>{{ $srtVal }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Search di Kanan -->
            <div class="relative w-full md:w-80">
                <input type="text" 
                       name="search" 
                       value="{{ $search }}" @input="handleSearch($event)" 
                       placeholder="Cari ulasan, klien, fotografer..." 
                       class="w-full pl-4 pr-10 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">
                <svg class="w-4 h-4 text-gray-400 absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </div>

        <div id="dashboard-table-container" class="transition-opacity duration-200" :class="isLoading ? 'opacity-40 pointer-events-none' : 'opacity-100'">

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-[11px] font-extrabold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        <th class="py-3.5 px-4 sm:px-6 w-3/12">Pemberi & Penerima</th>
                        <th class="py-3.5 px-4 sm:px-6 w-1/12 text-center">Rating</th>
                        <th class="py-3.5 px-4 sm:px-6 w-4/12">Isi Pesan Testimoni</th>
                        <th class="py-3.5 px-4 sm:px-6 w-2/12 text-center">Status Publikasi</th>
                        <th class="py-3.5 px-4 sm:px-6 w-2/12 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60 text-xs">
                    @forelse($reviews as $rev)
                        <tr class="hover:bg-amber-50/30 dark:hover:bg-amber-950/20 transition">
                            <td class="py-4 px-4 sm:px-6 space-y-1">
                                <div class="font-extrabold text-gray-900 dark:text-white flex items-center gap-1.5">
                                    <span>Klien: {{ $rev->client->nama ?? 'Klien' }}</span>
                                </div>
                                <div class="text-[10px] text-gray-500 dark:text-gray-400 font-bold">
                                    Fotografer: {{ $rev->photographer->nama ?? 'Fotografer' }}
                                </div>
                            </td>

                            <td class="py-4 px-4 sm:px-6 align-middle text-center whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 text-xs font-black text-gray-900 dark:text-white">
                                    <svg class="w-3.5 h-3.5 fill-amber-400 text-amber-400 shrink-0" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    <span>{{ number_format($rev->rating, 1) }}</span>
                                </span>
                            </td>

                            <td class="py-4 px-4 sm:px-6 text-gray-700 dark:text-gray-300 leading-relaxed">
                                <p class="line-clamp-2">"{{ $rev->deskripsi_review }}"</p>
                                <span class="text-[10px] text-gray-400 block mt-1">{{ $rev->created_at->diffForHumans() }}</span>
                            </td>

                            <td class="py-4 px-4 sm:px-6 align-middle text-center whitespace-nowrap">
                                @if($rev->is_hidden)
                                    <span class="text-xs font-bold text-gray-400 dark:text-gray-500">
                                        Disembunyikan
                                    </span>
                                @else
                                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">
                                        Dipublikasikan
                                    </span>
                                @endif
                            </td>

                            <td class="py-4 px-4 sm:px-6 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <form action="{{ route('admin.reviews.toggle_hide', $rev->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-bold transition min-w-[70px] text-center {{ $rev->is_hidden ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-900/60' : 'bg-amber-50 hover:bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200/80 dark:border-amber-900/60' }}">
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
                                            class="px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200/80 dark:border-rose-900/60 font-bold text-xs transition min-w-[60px] text-center">
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

        @if($reviews->total() > 0)
            <div class="p-4 border-t border-gray-200 dark:border-gray-700/80 bg-gray-50/50 dark:bg-gray-900/50">
                {{ $reviews->links('partials.dashboard.pagination') }}
            </div>
        @endif
        </div>
    </div>
</div>
@endsection