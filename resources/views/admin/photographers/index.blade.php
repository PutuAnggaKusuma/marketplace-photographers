@extends('layouts.admin')

@section('title', 'Manajemen Fotografer - LensMatch Admin')

@section('content')
<div x-data="dashboardLiveTable()" class="space-y-6">

    <!-- Page Title Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <span class="text-[11px] font-extrabold uppercase tracking-widest text-amber-600 dark:text-amber-400">Kurasi Kualitas Platform</span>
            <h1 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">Manajemen Fotografer</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Kelola data seluruh mitra fotografer, monitoring legalitas portofolio, status verifikasi centang biru, dan manajemen katalog layanan</p>
        </div>
    </div>

    <!-- 4 Clean Summary Metric Cards (Gray Subtitle) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Studio -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
            <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Total Mitra Fotografer</span>
            <h2 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ $totalStudios }} Fotografer</h2>
            <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">Terdaftar di ekosistem</p>
        </div>

        <!-- Card 2: Terverifikasi -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
            <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Fotografer Terverifikasi</span>
            <h2 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ $totalVerified }} Fotografer</h2>
            <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">Lencana Verified Aktif</p>
        </div>

        <!-- Card 3: Menunggu Verifikasi -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
            <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Menunggu Verifikasi</span>
            <h2 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ $totalUnverified }} Fotografer</h2>
            <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">Butuh Peninjauan Portofolio</p>
        </div>

        <!-- Card 4: Total Paket Layanan -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
            <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Total Layanan Terlisting</span>
            <h2 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ $totalServices }} Paket</h2>
            <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">Aktif di Katalog Publik</p>
        </div>
    </div>

    </div>

    <!-- Data Table Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm overflow-hidden space-y-4">
        
        <!-- Search Toolbar & Status Filters (Alpine.js Custom Floating Dropdown) -->
        <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex flex-col md:flex-row items-center justify-between gap-4">
            <!-- Filter Dropdowns di Kiri -->
            <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                <!-- 1. Dropdown Filter Status Verifikasi -->
                @php
                    $statusLabels = [
                        'all' => 'Semua Status (' . $totalStudios . ')',
                        'verified' => 'Terverifikasi (' . $totalVerified . ')',
                        'unverified' => 'Pending Verifikasi (' . $totalUnverified . ')'
                    ];
                    $curStatusName = $statusLabels[$statusFilter] ?? 'Semua Status';
                @endphp
                <div class="relative w-full sm:w-auto min-w-[180px]" x-data="{ open: false, selectedId: '{{ $statusFilter }}', selectedName: '{{ $curStatusName }}' }">
                    <input type="hidden" name="status" :value="selectedId">
                    <button @click="open = !open" type="button" 
                            class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs font-bold text-gray-800 dark:text-white flex items-center justify-between gap-2 shadow-2xs hover:border-amber-400 dark:hover:border-amber-500 cursor-pointer transition">
                        <span x-text="selectedName" class="truncate"></span>
                        <svg class="w-3.5 h-3.5 text-gray-400 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" @click.away="open = false" x-transition 
                         class="absolute z-50 left-0 mt-2 min-w-[190px] w-full bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700/80 py-1 max-h-60 overflow-y-auto" style="display: none;">
                        @foreach($statusLabels as $sKey => $sVal)
                            <button type="button" @click="selectedId = '{{ $sKey }}'; selectedName = '{{ $sVal }}'; open = false; applyFilter('status', '{{ $sKey }}')" 
                                    class="w-full px-3.5 py-2 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition flex items-center justify-between text-left cursor-pointer border-b border-gray-50 dark:border-gray-700/40 last:border-0"
                                    :class="selectedId === '{{ $sKey }}' ? 'bg-amber-50/70 dark:bg-amber-950/30 text-amber-600 font-extrabold' : ''">
                                <span>{{ $sVal }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- 2. Dropdown Filter Kategori -->
                @php
                    $curCat = $categoryFilter !== 'all' && isset($categories) ? $categories->firstWhere('id', $categoryFilter) : null;
                    $curCatName = $curCat ? $curCat->nama_kategori : 'Semua Kategori Sesi';
                @endphp
                <div class="relative w-full sm:w-auto min-w-[180px]" x-data="{ open: false, selectedId: '{{ $categoryFilter }}', selectedName: '{{ $curCatName }}' }">
                    <input type="hidden" name="category" :value="selectedId">
                    <button @click="open = !open" type="button" 
                            class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs font-bold text-gray-800 dark:text-white flex items-center justify-between gap-2 shadow-2xs hover:border-amber-400 dark:hover:border-amber-500 cursor-pointer transition">
                        <span x-text="selectedName" class="truncate"></span>
                        <svg class="w-3.5 h-3.5 text-gray-400 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" @click.away="open = false" x-transition 
                         class="absolute z-50 left-0 mt-2 min-w-[200px] w-full bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700/80 py-1 max-h-60 overflow-y-auto" style="display: none;">
                        <button type="button" @click="selectedId = 'all'; selectedName = 'Semua Kategori Sesi'; open = false; applyFilter('category', 'all')" 
                                class="w-full px-3.5 py-2 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition flex items-center justify-between text-left cursor-pointer border-b border-gray-50 dark:border-gray-700/40"
                                :class="selectedId === 'all' ? 'bg-amber-50/70 dark:bg-amber-950/30 text-amber-600 font-extrabold' : ''">
                            <span>Semua Kategori Sesi</span>
                        </button>
                        @foreach($categories as $cat)
                            <button type="button" @click="selectedId = '{{ $cat->id }}'; selectedName = '{{ $cat->nama_kategori }}'; open = false; applyFilter('category', '{{ $cat->id }}')" 
                                    class="w-full px-3.5 py-2 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition flex items-center justify-between text-left cursor-pointer border-b border-gray-50 dark:border-gray-700/40 last:border-0"
                                    :class="selectedId == '{{ $cat->id }}' ? 'bg-amber-50/70 dark:bg-amber-950/30 text-amber-600 font-extrabold' : ''">
                                <span>{{ $cat->nama_kategori }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- 3. Dropdown Urutan / Sort -->
                @php
                    $sortLabels = [
                        'latest' => 'Urutan: Terbaru',
                        'oldest' => 'Urutan: Terlama',
                        'name_asc' => 'Nama Fotografer (A-Z)'
                    ];
                    $curSortName = $sortLabels[$sort ?? 'latest'] ?? 'Urutan: Terbaru';
                @endphp
                <div class="relative w-full sm:w-auto min-w-[160px]" x-data="{ open: false, selectedId: '{{ $sort ?? 'latest' }}', selectedName: '{{ $curSortName }}' }">
                    <input type="hidden" name="sort" :value="selectedId">
                    <button @click="open = !open" type="button" 
                            class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs font-bold text-gray-800 dark:text-white flex items-center justify-between gap-2 shadow-2xs hover:border-amber-400 dark:hover:border-amber-500 cursor-pointer transition">
                        <span x-text="selectedName" class="truncate"></span>
                        <svg class="w-3.5 h-3.5 text-gray-400 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" @click.away="open = false" x-transition 
                         class="absolute z-50 left-0 mt-2 min-w-[170px] w-full bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700/80 py-1 max-h-60 overflow-y-auto" style="display: none;">
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
                       placeholder="Cari fotografer, nama, kota..." 
                       class="w-full pl-9 pr-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </div>

        <div id="dashboard-table-container" class="transition-opacity duration-200" :class="isLoading ? 'opacity-40 pointer-events-none' : 'opacity-100'">

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-[11px] font-extrabold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        <th class="py-3.5 px-4 sm:px-6">Fotografer & Profil</th>
                        <th class="py-3.5 px-4 sm:px-6">Wilayah Operasional</th>
                        <th class="py-3.5 px-4 sm:px-6">Spesialisasi Kategori</th>
                        <th class="py-3.5 px-4 sm:px-6 text-center">Status</th>
                        <th class="py-3.5 px-4 sm:px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60 text-xs">
                    @forelse($photographers as $photo)
                        <tr class="hover:bg-amber-50/30 dark:hover:bg-amber-950/20 transition">
                            <!-- Studio Profile -->
                            <td class="py-4 px-4 sm:px-6">
                                <div class="flex items-center gap-3 min-w-[220px]">
                                    <div class="w-11 h-11 rounded-xl overflow-hidden shrink-0 border border-gray-200 dark:border-gray-700 shadow-2xs">
                                        <img src="{{ $photo->foto ?: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150&q=80' }}" 
                                             class="w-full h-full object-cover" 
                                             alt="{{ $photo->nama }}" />
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="font-extrabold text-gray-900 dark:text-white truncate">{{ $photo->nama }}</h4>
                                        <p class="text-[11px] text-gray-500 dark:text-gray-400 truncate">
                                            Pemilik: {{ $photo->user->nama ?? '-' }} ({{ $photo->user->email ?? '-' }})
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <!-- Wilayah Operasional -->
                            <td class="py-4 px-4 sm:px-6 whitespace-nowrap">
                                <div class="font-bold text-gray-800 dark:text-gray-200">
                                    {{ $photo->city ? $photo->city->name : 'Indonesia' }}
                                </div>
                                <div class="text-[10px] text-gray-400">
                                    {{ $photo->province ? $photo->province->name : '-' }}
                                </div>
                            </td>

                            <!-- Kategori Spesialisasi -->
                            <td class="py-4 px-4 sm:px-6 whitespace-nowrap">
                                <p class="font-medium text-gray-700 text-theme-sm dark:text-gray-300">
                                    {{ $photo->categories && $photo->categories->isNotEmpty() ? $photo->categories->pluck('nama_kategori')->join(', ') : 'Semua Kategori' }}
                                </p>
                            </td>

                            <!-- Status -->
                            <td class="py-4 px-4 sm:px-6 align-middle text-center whitespace-nowrap">
                                @if($photo->is_verified)
                                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">
                                        Terverifikasi
                                    </span>
                                @else
                                    <span class="text-xs font-bold text-amber-600 dark:text-amber-400">
                                        Menunggu
                                    </span>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="py-4 px-4 sm:px-6 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <form action="{{ route('admin.photographers.verify', $photo->id) }}" method="POST" class="inline">
                                        @csrf
                                        @if($photo->is_verified)
                                            <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-bold transition bg-amber-50 text-amber-700 hover:bg-amber-100 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200/80 dark:border-amber-900/60 min-w-[74px] text-center">
                                                Batalkan
                                            </button>
                                        @else
                                            <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-bold transition bg-emerald-50 text-emerald-700 hover:bg-emerald-100 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-900/60 min-w-[74px] text-center">
                                                Verifikasi
                                            </button>
                                        @endif
                                    </form>

                                    <button type="button" 
                                            @click="$dispatch('open-confirm-modal', {
                                                title: 'Hapus Mitra Fotografer?',
                                                message: 'Apakah Anda yakin ingin menghapus fotografer {{ addslashes($photo->nama) }} beserta data layanannya?',
                                                confirmText: 'Ya, Hapus Fotografer',
                                                type: 'danger',
                                                actionUrl: '{{ route('admin.photographers.destroy', $photo->id) }}',
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
                                Belum ada data mitra fotografer ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Standard Pagination Footer -->
        @if($photographers->total() > 0)
            <div class="p-4 border-t border-gray-200 dark:border-gray-700/80 bg-gray-50/50 dark:bg-gray-900/50">
                {{ $photographers->links('partials.dashboard.pagination') }}
            </div>
        @endif
        </div>
    </div>
</div>
@endsection