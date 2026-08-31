@extends('layouts.photographer')

@section('title', 'Kelola Paket Layanan & Tarif — Dashboard Fotografer')

@section('content')
<div class="space-y-6 w-full" x-data="{
    searchQuery: '{{ $search }}',
    currentSort: '{{ $sort }}',
    isLoading: false,
    searchTimeout: null,
    applyFilter(key, val) {
        if (key === 'sort') this.currentSort = val;
        let url = new URL(window.location.href);
        url.searchParams.set(key, val);
        url.searchParams.set('page', '1');
        this.fetchTable(url.toString());
    },
    handleSearch(e) {
        this.searchQuery = e.target.value;
        clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(() => {
            let url = new URL(window.location.href);
            url.searchParams.set('search', this.searchQuery);
            url.searchParams.set('page', '1');
            this.fetchTable(url.toString());
        }, 300);
    },
    fetchTable(url) {
        this.isLoading = true;
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.text())
            .then(html => {
                let parser = new DOMParser();
                let doc = parser.parseFromString(html, 'text/html');
                let newTable = doc.querySelector('#dashboard-table-container');
                let curTable = document.querySelector('#dashboard-table-container');
                if (newTable && curTable) {
                    curTable.innerHTML = newTable.innerHTML;
                }
                history.pushState(null, '', url);
                this.isLoading = false;
            })
            .catch(err => {
                console.error(err);
                this.isLoading = false;
            });
    }
}">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <span class="text-[11px] font-extrabold uppercase tracking-widest text-amber-600 dark:text-amber-400">Katalog & Penawaran Jasa</span>
            <h1 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">Kelola Paket Layanan & Tarif</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Daftar paket pemotretan resmi, rincian fasilitas yang didapat klien, dan tarif harga penawaran Anda</p>
        </div>
        <a href="{{ route('photographer.services.create') }}" class="px-5 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs rounded-xl shadow-xs transition flex items-center gap-2 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path></svg>
            <span>Buat Paket Layanan Baru</span>
        </a>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 text-xs text-emerald-800 dark:text-emerald-300 font-bold flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 text-xs text-rose-800 dark:text-rose-300 font-bold space-y-1">
            @foreach($errors->all() as $err)
                <p>• {{ $err }}</p>
            @endforeach
        </div>
    @endif

    <!-- Metric Stat Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        
        <!-- Card 1: Total Paket -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
            <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Total Paket Aktif</span>
            <div class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ $totalServices }}</div>
            <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">Tersedia di etalase publik</p>
        </div>

        <!-- Card 2: Rata-rata Tarif -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
            <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Rata-rata Tarif</span>
            <div class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">Rp {{ number_format($avgPrice, 0, ',', '.') }}</div>
            <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">Estimasi per sesi foto</p>
        </div>

        <!-- Card 3: Range Harga -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
            <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Rentang Harga</span>
            <div class="text-base sm:text-lg font-black text-gray-900 dark:text-white tracking-tight truncate">
                {{ $minPrice > 0 ? 'Rp ' . number_format($minPrice, 0, ',', '.') : '-' }}
            </div>
            <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">
                s/d {{ $maxPrice > 0 ? 'Rp ' . number_format($maxPrice, 0, ',', '.') : '-' }}
            </p>
        </div>

        <!-- Card 4: Total Fasilitas -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
            <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Total Benefit Layanan</span>
            <div class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ $totalFeatures }} Benefit</div>
            <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">Item benefit fasilitas terdaftar</p>
        </div>

    </div>

    <!-- Standard Data Table Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm overflow-hidden space-y-4">
        
        <!-- Search Toolbar & Sort -->
        <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex flex-col md:flex-row items-center justify-between gap-4">
            
            <!-- Filter Urutan di Kiri -->
            <div class="flex items-center gap-3 w-full md:w-auto">
                @php
                    $sortLabels = [
                        'latest' => 'Paket Terbaru',
                        'oldest' => 'Paket Terlama',
                        'price_desc' => 'Tarif Tertinggi',
                        'price_asc' => 'Tarif Terendah',
                        'name_asc' => 'Nama Paket (A-Z)',
                        'name_desc' => 'Nama Paket (Z-A)'
                    ];
                    $curSortName = $sortLabels[$sort ?? 'latest'] ?? 'Paket Terbaru';
                @endphp
                <div class="relative w-full sm:w-auto min-w-[200px]" x-data="{ open: false, selectedId: '{{ $sort ?? 'latest' }}', selectedName: '{{ $curSortName }}' }">
                    <input type="hidden" name="sort" :value="selectedId">
                    <button @click="open = !open" type="button" 
                            class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs font-bold text-gray-800 dark:text-white flex items-center justify-between gap-2 shadow-2xs hover:border-amber-400 dark:hover:border-amber-500 cursor-pointer transition">
                        <span x-text="selectedName" class="truncate"></span>
                        <svg class="w-3.5 h-3.5 text-gray-400 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" @click.away="open = false" x-transition 
                          class="absolute z-50 left-0 mt-2 min-w-[210px] w-full bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700/80 py-1 max-h-60 overflow-y-auto" style="display: none;">
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
                       placeholder="Cari paket layanan atau fasilitas..." 
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
                            <th class="py-3.5 px-4 sm:px-6 w-3/12">Nama Paket & Fasilitas</th>
                            <th class="py-3.5 px-4 sm:px-6 w-4/12">Deskripsi Layanan</th>
                            <th class="py-3.5 px-4 sm:px-6 w-2/12">Tarif Harga (Rp)</th>
                            <th class="py-3.5 px-4 sm:px-6 w-1/12 text-center">Status</th>
                            <th class="py-3.5 px-4 sm:px-6 w-2/12 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60 text-xs">
                        @forelse($services as $srv)
                            <tr class="hover:bg-amber-50/30 dark:hover:bg-amber-950/20 transition">
                                
                                <!-- Nama Paket & Fasilitas -->
                                <td class="py-4 px-4 sm:px-6 align-top">
                                    <div class="space-y-1.5">
                                        <h4 class="font-black text-gray-900 dark:text-white text-sm tracking-tight">{{ $srv->nama_layanan }}</h4>
                                        @if($srv->details->count() > 0)
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($srv->details as $d)
                                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                                        {{ $d->nama_fitur }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-[10px] italic text-gray-400">Belum ada rincian fasilitas</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Deskripsi -->
                                <td class="py-4 px-4 sm:px-6 align-top text-gray-600 dark:text-gray-400 leading-relaxed">
                                    {{ $srv->deskripsi_layanan ?: '-' }}
                                </td>

                                <!-- Tarif Harga (Rp) -->
                                <td class="py-4 px-4 sm:px-6 align-top whitespace-nowrap">
                                    <span class="text-sm font-black text-gray-900 dark:text-white">
                                        Rp {{ number_format($srv->tarif_harga, 0, ',', '.') }}
                                    </span>
                                    <span class="text-[11px] text-gray-400 block mt-0.5">/ sesi foto</span>
                                </td>

                                <!-- Status -->
                                <td class="py-4 px-4 sm:px-6 align-top text-center whitespace-nowrap">
                                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">Aktif</span>
                                </td>

                                <!-- Aksi -->
                                <td class="py-4 px-4 sm:px-6 align-top text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="{{ route('photographer.services.edit', $srv->id) }}" 
                                           class="px-3 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200/80 dark:border-amber-900/60 font-bold text-xs transition min-w-[60px] text-center inline-block">
                                            Edit
                                        </a>
                                        <button type="button" 
                                                @click="$dispatch('open-confirm-modal', {
                                                    title: 'Hapus Paket Layanan?',
                                                    message: 'Apakah Anda yakin ingin menghapus paket {{ addslashes($srv->nama_layanan) }}?',
                                                    buttonText: 'Ya, Hapus Paket',
                                                    iconType: 'delete',
                                                    buttonClass: 'bg-red-600 hover:bg-red-700 text-white',
                                                    actionUrl: '{{ route('photographer.services.delete', $srv->id) }}',
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
                                    Belum ada paket layanan ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($services->total() > 0)
                <div class="p-4 bg-gray-50/50 dark:bg-gray-900/50">
                    {{ $services->links('partials.dashboard.pagination') }}
                </div>
            @endif
        </div>

    </div>

</div>
@endsection