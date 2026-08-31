@extends('layouts.admin')

@section('title', 'Kelola Modul E-Learning — Admin')

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
            <span class="text-[11px] font-extrabold uppercase tracking-widest text-amber-600 dark:text-amber-400">Edukasi & Pembelajaran Fotografi</span>
            <h1 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">Kelola Materi E-Learning</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Kelola artikel tutorial, panduan teknik kamera, dan materi edukasi untuk pengguna platform</p>
        </div>
        <a href="{{ route('admin.elearning.create') }}" class="px-5 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs rounded-xl shadow-xs transition flex items-center gap-2 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path></svg>
            <span>+ Tambah Materi Baru</span>
        </a>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 text-xs text-emerald-800 dark:text-emerald-300 font-bold flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

        <!-- Metric Stat Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        <!-- Card 1 -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
            <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Total Materi</span>
            <div class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ $materials->total() }}</div>
            <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">Modul artikel & panduan aktif</p>
        </div>

        <!-- Card 2 -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
            <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Materi Bervideo</span>
            <div class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ $materials->whereNotNull('video_url')->count() }}</div>
            <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">Dilengkapi video embed YouTube</p>
        </div>

        <!-- Card 3 -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
            <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Kategori Edukasi</span>
            <div class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ $categories->count() }} Kategori</div>
            <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">Topik fotografi beragam</p>
        </div>

        <!-- Card 4 -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
            <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Total Pembaca</span>
            <div class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ \App\Models\Elearning::sum('view_count') }} View</div>
            <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">Akumulasi pembaca materi</p>
        </div>
    </div>

    <!-- Standard Data Table Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm overflow-hidden space-y-4">
        
        <!-- Search Toolbar & Sort -->
        <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3 w-full md:w-auto">
                @php
                    $sortLabels = [
                        'latest' => 'Materi Terbaru',
                        'oldest' => 'Materi Terlama',
                        'title_asc' => 'Judul Materi (A-Z)',
                        'title_desc' => 'Judul Materi (Z-A)'
                    ];
                    $curSortName = $sortLabels[$sort ?? 'latest'] ?? 'Materi Terbaru';
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
                       placeholder="Cari judul materi atau kategori..." 
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
                            <th class="py-3.5 px-4 sm:px-6 w-4/12">Materi & Thumbnail</th>
                            <th class="py-3.5 px-4 sm:px-6 w-2/12">Kategori</th>
                            <th class="py-3.5 px-4 sm:px-6 w-3/12">Video Pembelajaran</th>
                            <th class="py-3.5 px-4 sm:px-6 w-1/12 text-center">Status</th>
                            <th class="py-3.5 px-4 sm:px-6 w-2/12 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60 text-xs">
                        @forelse($materials as $m)
                            <tr class="hover:bg-amber-50/30 dark:hover:bg-amber-950/20 transition">
                                <td class="py-4 px-4 sm:px-6 align-top">
                                    <div class="flex items-center gap-3.5">
                                        <div class="w-14 h-10 rounded-xl bg-gray-100 dark:bg-gray-700 overflow-hidden shrink-0 border border-gray-200 dark:border-gray-600 shadow-xs">
                                            @php
                                                $thumbSrc = $m->thumbnail_url ?: ($m->thumbnail ? asset('storage/' . $m->thumbnail) : 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=800&q=80');
                                            @endphp
                                            <img onerror="this.onerror=null;this.src=window.SVG_IMAGE_FALLBACK;" src="{{ $thumbSrc }}" class="w-full h-full object-cover" alt="{{ $m->judul }}">
                                        </div>
                                        <div>
                                            <h4 class="font-black text-gray-900 dark:text-white text-sm tracking-tight">{{ $m->judul }}</h4>
                                            <span class="text-[11px] text-gray-400 block mt-0.5">{{ $m->created_at ? $m->created_at->format('d M Y') : '-' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4 sm:px-6 align-top whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200/80 dark:border-amber-900/60">
                                        {{ $m->kategori ?? ($m->category ? $m->category->nama_kategori : 'Umum') }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 sm:px-6 align-top text-gray-600 dark:text-gray-400">
                                    @if($m->video_url)
                                        <a href="{{ $m->video_url }}" target="_blank" class="text-amber-600 hover:underline font-bold flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>Buka Video</span>
                                        </a>
                                    @else
                                        <span class="text-gray-400 italic">Artikel Teks</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 sm:px-6 align-top text-center whitespace-nowrap">
                                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">Aktif</span>
                                </td>
                                <td class="py-4 px-4 sm:px-6 align-top text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="{{ route('admin.elearning.edit', $m->id) }}" 
                                           class="px-3 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200/80 dark:border-amber-900/60 font-bold text-xs transition min-w-[60px] text-center inline-block">
                                            Edit
                                        </a>
                                        <button type="button" 
                                                @click="$dispatch('open-confirm-modal', {
                                                    title: 'Hapus Materi E-Learning?',
                                                    message: 'Apakah Anda yakin ingin menghapus materi {{ addslashes($m->judul) }}?',
                                                    buttonText: 'Ya, Hapus Materi',
                                                    iconType: 'delete',
                                                    buttonClass: 'bg-red-600 hover:bg-red-700 text-white',
                                                    actionUrl: '{{ route('admin.elearning.destroy', $m->id) }}',
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
                                    Belum ada materi e-learning ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($materials->total() > 0)
                <div class="p-4 bg-gray-50/50 dark:bg-gray-900/50">
                    {{ $materials->links('partials.dashboard.pagination') }}
                </div>
            @endif
        </div>

    </div>

</div>
@endsection