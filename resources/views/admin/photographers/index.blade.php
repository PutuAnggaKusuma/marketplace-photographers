@extends('layouts.admin')

@section('title', 'Manajemen Fotografer - LensMatch Admin')

@section('content')
<div x-data="{
    ...dashboardLiveTable(),
    deleteModalOpen: false,
    deleteActionUrl: '',
    deleteStudioName: '',
    openDeleteModal(url, name) {
        this.deleteActionUrl = url;
        this.deleteStudioName = name;
        this.deleteModalOpen = true;
    }
}" class="space-y-6">

    <!-- Page Title Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            
            <h1 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">Manajemen Fotografer</h1>
        </div>
    </div>

    <!-- 4 Clean Summary Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Mitra -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Total Mitra Fotografer</span>
            <h2 class="text-xs font-black text-gray-900 dark:text-white tracking-tight">{{ $totalStudios }} Fotografer</h2>
        </div>

        <!-- Card 2: Tayang di Katalog -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Tayang di Katalog</span>
            <h2 class="text-xs font-black text-gray-900 dark:text-white tracking-tight">{{ $totalPublished }} Fotografer</h2>
        </div>

        <!-- Card 3: Menunggu Review -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Menunggu Review</span>
            <h2 class="text-xs font-black text-gray-900 dark:text-white tracking-tight">{{ $totalPending }} Fotografer</h2>
        </div>

        <!-- Card 4: Ditolak -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Ditolak</span>
            <h2 class="text-xs font-black text-gray-900 dark:text-white tracking-tight">{{ $totalRejected }} Fotografer</h2>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm overflow-hidden">
        
        <!-- Search Toolbar & Status Filters (Alpine.js Custom Floating Dropdown) -->
        <div class="p-4 flex flex-col md:flex-row items-center justify-between gap-4">
            <!-- Filter Dropdowns di Kiri -->
            <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                <!-- 1. Dropdown Filter Status Verifikasi -->
                @php
                    $statusLabels = [
                        'all' => 'Semua Status (' . $totalStudios . ')',
                        'pending' => 'Menunggu Profil (' . $totalPendingProfile . ')',
                        'pending_storefront' => 'Review Etalase (' . $totalPendingStorefront . ')',
                        'setup_storefront' => 'Lengkapi Etalase (' . $totalSetupStorefront . ')',
                        'published' => 'Tayang (' . $totalPublished . ')',
                        'rejected' => 'Ditolak (' . $totalRejected . ')'
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
                        <th class="py-3.5 px-4 sm:px-6">Fotografer</th>
                        <th class="py-3.5 px-4 sm:px-6">Domisili</th>
                        <th class="py-3.5 px-4 sm:px-6">Kategori</th>
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
                                    @php
                                        $avatarPhoto = $photo->foto ? (str_starts_with($photo->foto, 'http') ? $photo->foto : asset('storage/' . $photo->foto)) : null;
                                    @endphp
                                    @if($avatarPhoto)
                                        <div class="w-11 h-11 rounded-xl overflow-hidden shrink-0 border border-gray-200 dark:border-gray-700 shadow-2xs">
                                            <img src="{{ $avatarPhoto }}" 
                                                 class="w-full h-full object-cover" 
                                                 alt="{{ $photo->nama }}" />
                                        </div>
                                    @else
                                        <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-amber-500 to-amber-300 text-white font-black text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                            {{ strtoupper(substr($photo->nama ?? ($photo->user->nama ?? 'F'), 0, 2)) }}
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <h4 class="text-xs font-extrabold text-gray-900 dark:text-white truncate">{{ $photo->nama }}</h4>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                            Pemilik: {{ $photo->user->nama ?? '-' }} ({{ $photo->user->email ?? '-' }})
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <!-- Domisili -->
                            <td class="py-4 px-4 sm:px-6 whitespace-nowrap">
                                @if($photo->city)
                                    <div class="font-bold text-gray-800 dark:text-gray-200">
                                        {{ $photo->city->name }}
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        {{ $photo->province ? $photo->province->name : '' }}
                                    </div>
                                @else
                                    <span class="text-gray-400 font-medium">-</span>
                                @endif
                            </td>

                            <!-- Kategori Spesialisasi -->
                            <td class="py-4 px-4 sm:px-6 whitespace-nowrap">
                                @if($photo->categories && $photo->categories->isNotEmpty())
                                    <p class="text-xs font-medium text-gray-700 dark:text-gray-300">
                                        {{ $photo->categories->pluck('nama_kategori')->join(', ') }}
                                    </p>
                                @else
                                    <span class="text-xs text-gray-400 dark:text-gray-500 font-normal">
                                        Belum diatur
                                    </span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="py-4 px-4 sm:px-6 align-middle text-center whitespace-nowrap">
                                @if($photo->status_verifikasi === 'ditolak')
                                    <span class="text-xs font-bold text-rose-600 dark:text-rose-400">
                                        Profil Ditolak
                                    </span>
                                @elseif($photo->status_verifikasi !== 'terverifikasi')
                                    <span class="text-xs font-bold text-amber-600 dark:text-amber-400">
                                        Menunggu Profil
                                    </span>
                                @elseif($photo->isPublishedToCatalog())
                                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">
                                        Tayang
                                    </span>
                                @elseif($photo->status_etalase === 'menunggu')
                                    <span class="text-xs font-bold text-blue-600 dark:text-blue-400">
                                        Review Etalase
                                    </span>
                                @elseif($photo->status_etalase === 'ditolak')
                                    <span class="text-xs font-bold text-rose-600 dark:text-rose-400">
                                        Etalase Ditolak
                                    </span>
                                @else
                                    <span class="text-xs font-bold text-gray-500 dark:text-gray-400">
                                        Lengkapi Etalase
                                    </span>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="py-4 px-4 sm:px-6 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('admin.photographers.show', $photo->id) }}" 
                                       class="px-3 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200/80 dark:border-blue-900/60 font-bold text-xs transition min-w-[60px] text-center inline-block">
                                        Lihat
                                    </a>

                                    <button type="button" 
                                            @click="openDeleteModal('{{ route('admin.photographers.destroy', $photo->id) }}', '{{ addslashes($photo->nama) }}')"
                                            class="px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200/80 dark:border-rose-900/60 font-bold text-xs transition min-w-[60px] text-center cursor-pointer">
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

    <!-- Modal Hapus Fotografer dengan Kategori & Alasan Pelanggaran (Rule #59) -->
    <div x-show="deleteModalOpen" 
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
         @keydown.escape.window="deleteModalOpen = false">
        <div @click.away="deleteModalOpen = false" 
             class="w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/80 shadow-2xl overflow-hidden p-6 sm:p-7 flex flex-col gap-5">
            
            <div class="text-center flex flex-col gap-1">
                <h3 class="text-base sm:text-lg font-black text-gray-900 dark:text-white">
                    Hapus Fotografer
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Studio: <span class="font-bold text-gray-800 dark:text-gray-200" x-text="deleteStudioName"></span>
                </p>
            </div>

            <form :action="deleteActionUrl" method="POST" novalidate class="flex flex-col gap-4" x-data="{
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

                <div class="flex items-center justify-end gap-3">
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
</div>
@endsection