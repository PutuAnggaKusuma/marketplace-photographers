@extends('layouts.admin')

@section('title', 'Moderasi Forum Admin - LensMatch')

@section('content')
<div x-data="dashboardLiveTable()" class="space-y-6">

    <!-- Page Title Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">Moderasi Forum Komunitas</h1>
        </div>
    </div>

    <!-- 4 Stat Cards Summary (Rule #64 Minimalist Standard) -->
    <div id="dashboard-stats-container">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Topik Diskusi -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Total Topik Diskusi</span>
            <h2 class="text-xs font-black text-gray-900 dark:text-white tracking-tight">{{ $totalPosts }} Topik</h2>
        </div>

        <!-- Card 2: Total Balasan Komentar -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Total Balasan Komentar</span>
            <h2 class="text-xs font-black text-gray-900 dark:text-white tracking-tight">{{ $totalComments }} Balasan</h2>
        </div>

        <!-- Card 3: Topik Bulan Ini -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Topik Bulan Ini</span>
            <h2 class="text-xs font-black text-gray-900 dark:text-white tracking-tight">{{ $thisMonthPosts }} Topik</h2>
        </div>

        <!-- Card 4: Topik Paling Ramai -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Topik Paling Ramai</span>
            <h2 class="text-xs font-black text-gray-900 dark:text-white tracking-tight truncate" title="{{ $topPost ? $topPost->judul : '-' }}">{{ $topPost ? $topPost->judul : '-' }}</h2>
        </div>
    </div>

    </div>

    <!-- Data Table Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm overflow-hidden">
        
        <!-- Search Toolbar & Sort (Alpine.js Custom Floating Dropdown) -->
        <div class="p-4 flex flex-col md:flex-row items-center justify-between gap-4">
            <!-- Filter Urutan di Kiri -->
            <div class="flex items-center gap-3 w-full md:w-auto">
                @php
                    $sortLabels = [
                        'latest' => 'Urutan: Diskusi Terbaru',
                        'oldest' => 'Urutan: Diskusi Terlama',
                        'most_comments' => 'Paling Banyak Komentar'
                    ];
                    $curSortName = $sortLabels[$sort ?? 'latest'] ?? 'Urutan: Diskusi Terbaru';
                @endphp
                <div class="relative w-full sm:w-auto min-w-[190px]" x-data="{ open: false, selectedId: '{{ $sort ?? 'latest' }}', selectedName: '{{ $curSortName }}' }">
                    <input type="hidden" name="sort" :value="selectedId">
                    <button @click="open = !open" type="button" 
                            class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs font-bold text-gray-800 dark:text-white flex items-center justify-between gap-2 shadow-2xs hover:border-amber-400 dark:hover:border-amber-500 cursor-pointer transition">
                        <span x-text="selectedName" class="truncate"></span>
                        <svg class="w-3.5 h-3.5 text-gray-400 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" @click.away="open = false" x-transition 
                         class="absolute z-50 left-0 mt-2 min-w-[200px] w-full bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700/80 py-1 max-h-60 overflow-y-auto" style="display: none;">
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
                       placeholder="Cari topik diskusi atau penulis..." 
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
                        <th class="py-3.5 px-4 sm:px-6 w-5/12">Topik Diskusi</th>
                        <th class="py-3.5 px-4 sm:px-6 w-2/12">Penulis</th>
                        <th class="py-3.5 px-4 sm:px-6 w-2/12">Tanggal Rilis</th>
                        <th class="py-3.5 px-4 sm:px-6 w-1/12 text-center">Balasan</th>
                        <th class="py-3.5 px-4 sm:px-6 w-2/12 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60 text-xs">
                    @forelse($posts as $post)
                        <tr class="hover:bg-amber-50/30 dark:hover:bg-amber-950/20 transition">
                            <!-- Judul & Ringkasan -->
                            <td class="py-4 px-4 sm:px-6 space-y-1">
                                <a href="{{ route('public.forum.show', $post->id) }}" target="_blank" class="text-xs font-extrabold text-gray-900 dark:text-white hover:text-amber-500 transition line-clamp-1">
                                    {{ $post->judul }}
                                </a>
                                <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-1 leading-relaxed">{{ $post->deskripsi }}</p>
                            </td>

                            <!-- Penulis -->
                            <td class="py-4 px-4 sm:px-6">
                                <div class="text-xs font-bold text-gray-900 dark:text-white">{{ $post->user->nama ?? 'Anggota Komunitas' }}</div>
                                <div class="text-xs text-gray-400 truncate">{{ $post->user->email ?? '-' }}</div>
                            </td>

                            <!-- Tanggal Rilis -->
                            <td class="py-4 px-4 sm:px-6 font-semibold text-gray-600 dark:text-gray-400 whitespace-nowrap">
                                {{ $post->created_at->format('d M Y, H:i') }}
                            </td>

                            <!-- Jumlah Balasan -->
                            <td class="py-4 px-4 sm:px-6 align-middle text-center whitespace-nowrap">
                                <span class="text-xs font-bold text-gray-700 dark:text-gray-300">
                                    {{ $post->comments->count() }} Komentar
                                </span>
                            </td>

                            <!-- Aksi -->
                            <td class="py-4 px-4 sm:px-6 align-middle text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('public.forum.show', $post->id) }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200/80 dark:border-blue-900/60 font-bold text-xs transition min-w-[60px] text-center">Lihat</a>
                                    <button type="button" 
                                            @click="$dispatch('open-confirm-modal', {
                                                title: 'Hapus Postingan Forum Spam?',
                                                message: 'Apakah Anda yakin ingin menghapus topik diskusi {{ addslashes($post->judul) }} beserta seluruh balasan komentarnya?',
                                                confirmText: 'Ya, Hapus Postingan',
                                                type: 'danger',
                                                actionUrl: '{{ route('admin.forum.destroy', $post->id) }}',
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
                                Belum ada postingan diskusi forum ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($posts->total() > 0)
            <div class="p-4 border-t border-gray-200 dark:border-gray-700/80 bg-gray-50/50 dark:bg-gray-900/50">
                {{ $posts->links('partials.dashboard.pagination') }}
            </div>
        @endif
        </div>
    </div>
</div>
@endsection