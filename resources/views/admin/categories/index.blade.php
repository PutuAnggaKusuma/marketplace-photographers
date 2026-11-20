@extends('layouts.admin')

@section('title', 'Kelola Kategori Layanan — Admin')

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
            <h1 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">Kategori Layanan Fotografi</h1>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="px-5 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs rounded-xl shadow-xs transition flex items-center gap-2 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path></svg>
            <span>Tambah Kategori Baru</span>
        </a>
    </div>

        <!-- Metric Stat Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        <!-- Card 1 -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Total Kategori</span>
            <div class="text-xs font-black text-gray-900 dark:text-white tracking-tight">{{ $categories->total() }}</div>
        </div>

        <!-- Card 2 -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Kategori Terpopuler</span>
            <div class="text-xs font-black text-gray-900 dark:text-white tracking-tight">Wedding & Prewedding</div>
        </div>

        <!-- Card 3 -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Fotografer Terhubung</span>
            <div class="text-xs font-black text-gray-900 dark:text-white tracking-tight">{{ \App\Models\RolePhotographer::count() }}</div>
        </div>

        <!-- Card 4 -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Paket Layanan Terkait</span>
            <div class="text-xs font-black text-gray-900 dark:text-white tracking-tight">{{ \App\Models\PhotographerService::count() }}</div>
        </div>
    </div>

    <!-- Standard Data Table Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm overflow-hidden">
        
        <!-- Search Toolbar & Sort -->
        <div class="p-4 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3 w-full md:w-auto">
                @php
                    $sortLabels = [
                        'latest' => 'Kategori Terbaru',
                        'oldest' => 'Kategori Terlama',
                        'name_asc' => 'Nama Kategori (A-Z)',
                        'name_desc' => 'Nama Kategori (Z-A)'
                    ];
                    $curSortName = $sortLabels[$sort ?? 'latest'] ?? 'Kategori Terbaru';
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
                       placeholder="Cari nama atau deskripsi kategori..." 
                       class="w-full pl-4 pr-10 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">
                <svg class="w-4 h-4 text-gray-400 absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </div>

        <div id="dashboard-table-container" class="transition-opacity duration-200" :class="isLoading ? 'opacity-40 pointer-events-none' : 'opacity-100'">

            <!-- Table -->
            <div class="overflow-x-auto min-h-[240px]">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-[11px] font-extrabold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            <th class="py-3.5 px-4 sm:px-6 w-4/12">Nama Kategori</th>
                            <th class="py-3.5 px-4 sm:px-6 w-5/12">Deskripsi Kategori</th>
                            <th class="py-3.5 px-4 sm:px-6 w-3/12 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60 text-xs">
                        @forelse($categories as $cat)
                            <tr class="hover:bg-amber-50/30 dark:hover:bg-amber-950/20 transition">
                                <td class="py-4 px-4 sm:px-6 align-top">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700/80 shrink-0 flex items-center justify-center">
                                            @if($cat->icon_url)
                                                <img src="{{ $cat->icon_url }}" alt="{{ $cat->nama_kategori }}" class="w-full h-full object-cover">
                                            @else
                                                <svg class="w-5 h-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z"/></svg>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <span class="font-black text-gray-900 dark:text-white text-xs block truncate">{{ $cat->nama_kategori }}</span>
                                            <span class="text-[11px] text-gray-400 dark:text-gray-500 block">{{ $cat->photographers_count ?? 0 }} fotografer</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4 sm:px-6 align-top text-gray-600 dark:text-gray-400 leading-relaxed">
                                    {{ $cat->deskripsi ?: '-' }}
                                </td>
                                <td class="py-4 px-4 sm:px-6 align-middle text-center whitespace-nowrap">
                                    @php
                                        $isDropup = ($loop->count > 2 && ($loop->last || $loop->remaining < 2)) || ($loop->count === 2 && $loop->last);
                                    @endphp
                                    <div class="inline-flex items-center justify-center gap-1.5" x-data="{ menuOpen: false }">
                                        <!-- 1. Tombol Utama (Primary Action) Sesuai Rule #69 & Rule #80: Soft Blue Lihat -->
                                        <a href="{{ route('admin.categories.show', $cat->id) }}" 
                                           class="px-3 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200/80 dark:border-blue-900/60 font-bold text-xs transition min-w-[60px] text-center inline-block cursor-pointer shadow-2xs">
                                            Lihat
                                        </a>

                                        <!-- 2. Kebab Menu untuk Aksi Sekunder Sesuai Rule #69 -->
                                        <div class="relative inline-block text-left">
                                            <button type="button" 
                                                    @click="menuOpen = !menuOpen" 
                                                    class="w-8 h-8 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white flex items-center justify-center transition shadow-2xs cursor-pointer">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                    <circle cx="12" cy="5" r="1.75" />
                                                    <circle cx="12" cy="12" r="1.75" />
                                                    <circle cx="12" cy="19" r="1.75" />
                                                </svg>
                                            </button>

                                            <div x-show="menuOpen" 
                                                 @click.away="menuOpen = false" 
                                                 x-transition:enter="transition ease-out duration-100" 
                                                 x-transition:enter-start="transform opacity-0 scale-95" 
                                                 x-transition:enter-end="transform opacity-100 scale-100" 
                                                 x-transition:leave="transition ease-in duration-75" 
                                                 x-transition:leave-start="transform opacity-100 scale-100" 
                                                 x-transition:leave-end="transform opacity-0 scale-95" 
                                                 class="absolute right-0 {{ $isDropup ? 'bottom-full mb-1.5 origin-bottom-right' : 'top-full mt-1.5 origin-top-right' }} w-36 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 z-50 text-left overflow-hidden" 
                                                 style="display: none;">
                                                
                                                <a href="{{ route('admin.categories.edit', $cat->id) }}" 
                                                   class="w-full px-4 py-2.5 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition block text-left">
                                                    Edit
                                                </a>

                                                <button type="button" 
                                                        @click="menuOpen = false; $dispatch('open-confirm-modal', {
                                                            title: 'Hapus Kategori?',
                                                            message: 'Apakah Anda yakin ingin menghapus kategori {{ addslashes($cat->nama_kategori) }}?',
                                                            buttonText: 'Ya, Hapus Kategori',
                                                            iconType: 'delete',
                                                            buttonClass: 'bg-red-600 hover:bg-red-700 text-white',
                                                            actionUrl: '{{ route('admin.categories.destroy', $cat->id) }}',
                                                            method: 'POST'
                                                        })" 
                                                        class="w-full px-4 py-2.5 text-xs font-bold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition block text-left cursor-pointer border-t border-gray-100 dark:border-gray-700/60">
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-12 text-center text-gray-400 font-bold text-xs">
                                    Belum ada kategori ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($categories->total() > 0)
                <div class="p-4 bg-gray-50/50 dark:bg-gray-900/50">
                    {{ $categories->links('partials.dashboard.pagination') }}
                </div>
            @endif
        </div>

    </div>

</div>
@endsection