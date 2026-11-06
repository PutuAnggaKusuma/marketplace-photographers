@extends('layouts.admin')



@section('title', 'Kelola Lomba Foto & Event — Admin')



@section('content')

<div class="space-y-6 w-full" x-data="{

    searchQuery: '{{ $search }}',

    currentSort: '{{ $sort }}',

    currentStatus: '{{ $status }}',

    isLoading: false,

    searchTimeout: null,

    

    // Proposal Review Modal State

    proposalModalOpen: false,

    proposalRejectMode: false,

    currentProposal: {},

    proposalCatatanAdmin: '',

    

    // Quick Reject Modal State

    rejectModalOpen: false,

    rejectActionUrl: '',

    rejectContestTitle: '',

    catatanAdmin: '',



    openProposalModal(contestData) {

        this.currentProposal = contestData;

        this.proposalRejectMode = false;

        this.proposalCatatanAdmin = '';

        this.proposalModalOpen = true;

    },

    openRejectModal(actionUrl, title) {

        this.rejectActionUrl = actionUrl;

        this.rejectContestTitle = title;

        this.catatanAdmin = '';

        this.rejectModalOpen = true;

    },

    applyFilter(key, val) {

        if (key === 'sort') this.currentSort = val;

        if (key === 'status') this.currentStatus = val;

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
            <h1 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">Kelola Lomba Foto</h1>
        </div>

        <a href="{{ route('admin.contests.create') }}" class="px-5 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs rounded-xl shadow-xs transition flex items-center gap-2 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path></svg>
            <span>Buat Lomba Foto Baru</span>
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

            <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Total Event Lomba</span>

            <div class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ $totalContests ?? 0 }}</div>

        </div>



        <!-- Card 2 -->

        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">

            <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Perlu Review Admin</span>

            <div class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ $pendingContests ?? 0 }} Pengajuan</div>

        </div>



        <!-- Card 3 -->

        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">

            <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Lomba Aktif Terbuka</span>

            <div class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ $openContests ?? 0 }} Event</div>

        </div>



        <!-- Card 4 -->

        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">

            <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Total Submisi Karya</span>

            <div class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ $totalSubmissions ?? 0 }} Foto</div>

        </div>

    </div>



    <!-- Status Tabs / Filter Pills -->

    <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs font-bold">

        <button type="button" @click="applyFilter('status', 'all')" 

                class="px-4 py-2 rounded-xl transition cursor-pointer shrink-0 {{ ($status ?? 'all') === 'all' ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-950 shadow-xs' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:border-gray-300' }}">

            Semua ({{ $totalContests ?? 0 }})

        </button>

        <button type="button" @click="applyFilter('status', 'pending_review')" 

                class="px-4 py-2 rounded-xl transition cursor-pointer shrink-0 {{ ($status ?? '') === 'pending_review' ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-950 shadow-xs' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:border-gray-300' }}">

            Perlu Review ({{ $pendingContests ?? 0 }})

        </button>

        <button type="button" @click="applyFilter('status', 'buka')" 

                class="px-4 py-2 rounded-xl transition cursor-pointer shrink-0 {{ ($status ?? '') === 'buka' ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-950 shadow-xs' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:border-gray-300' }}">

            Aktif Buka ({{ $openContests ?? 0 }})

        </button>

        <button type="button" @click="applyFilter('status', 'selesai')" 

                class="px-4 py-2 rounded-xl transition cursor-pointer shrink-0 {{ ($status ?? '') === 'selesai' ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-950 shadow-xs' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:border-gray-300' }}">

            Selesai ({{ $completedContests ?? 0 }})

        </button>

        <button type="button" @click="applyFilter('status', 'ditolak')" 

                class="px-4 py-2 rounded-xl transition cursor-pointer shrink-0 {{ ($status ?? '') === 'ditolak' ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-950 shadow-xs' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:border-gray-300' }}">

            Ditolak ({{ $rejectedContests ?? 0 }})

        </button>

    </div>



    <!-- Standard Data Table Card -->

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm overflow-hidden space-y-4">

        

        <!-- Search Toolbar & Sort -->

        <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex flex-col md:flex-row items-center justify-between gap-4">

            <div class="flex items-center gap-3 w-full md:w-auto">

                @php

                    $sortLabels = [

                        'latest' => 'Lomba Terbaru',

                        'oldest' => 'Lomba Terlama',

                        'highest_prize' => 'Hadiah Tertinggi',

                    ];

                    $curSortName = $sortLabels[$sort ?? 'latest'] ?? 'Lomba Terbaru';

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

                       placeholder="Cari judul atau penyelenggara..." 

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

                            <th class="py-3.5 px-4 sm:px-6 w-4/12">Nama Lomba</th>

                            <th class="py-3.5 px-4 sm:px-6 w-2/12">Status</th>

                            <th class="py-3.5 px-4 sm:px-6 w-2/12">Total Hadiah</th>

                            <th class="py-3.5 px-4 sm:px-6 w-2/12">Periode Event</th>

                            <th class="py-3.5 px-4 sm:px-6 w-2/12 text-center">Aksi</th>

                        </tr>

                    </thead>

                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60 text-xs">

                        @forelse($contests as $c)

                            @php

                                $bannerSrc = $c->banner_url ?: 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=800&q=80';

                                

                                $creatorRole = 'Client';

                                if ($c->creator) {

                                    if ($c->creator->isPhotographer()) $creatorRole = 'Fotografer';

                                    elseif ($c->creator->isAdmin()) $creatorRole = 'Admin';

                                    elseif ($c->creator->isSuperAdmin()) $creatorRole = 'Super Admin';

                                }



                                $contestPayload = [

                                    'id' => $c->id,

                                    'judul_lomba' => $c->judul_lomba,

                                    'kategori' => $c->kategori,

                                    'penyelenggara' => $c->penyelenggara,

                                    'hadiah' => $c->hadiah,

                                    'biaya_pendaftaran' => $c->biaya_pendaftaran > 0 ? 'Rp ' . number_format($c->biaya_pendaftaran, 0, ',', '.') : 'Gratis',

                                    'start_date_formatted' => $c->start_date ? $c->start_date->translatedFormat('d M Y') : '-',

                                    'end_date_formatted' => $c->end_date ? $c->end_date->translatedFormat('d M Y') : '-',

                                    'banner_url' => $bannerSrc,

                                    'deskrpisi_lomba' => $c->deskrpisi_lomba,

                                    'syarat_ketentuan' => $c->syarat_ketentuan,

                                    'status' => $c->status,

                                    'catatan_admin' => $c->catatan_admin,

                                    'creator_name' => $c->creator->nama ?? 'Admin',

                                    'creator_email' => $c->creator->email ?? '-',

                                    'creator_role' => $creatorRole,

                                    'creator_initials' => $c->creator ? $c->creator->initials : 'AD',

                                    'created_at_formatted' => $c->created_at->translatedFormat('d F Y, H:i') . ' WIB',

                                    'approve_url' => route('admin.contests.approve', $c->id),

                                    'reject_url' => route('admin.contests.reject', $c->id),

                                ];

                            @endphp

                            <tr class="hover:bg-amber-50/30 dark:hover:bg-amber-950/20 transition">

                                <!-- Col 1: Banner + Title + Creator -->

                                <td class="py-4 px-4 sm:px-6 align-top">

                                    <div class="flex items-start gap-3.5">

                                        <div class="w-16 h-12 rounded-xl bg-gray-100 dark:bg-gray-700 overflow-hidden shrink-0 border border-gray-200 dark:border-gray-600 shadow-xs">

                                            <img onerror="this.onerror=null;this.src=window.SVG_IMAGE_FALLBACK;" src="{{ $bannerSrc }}" class="w-full h-full object-cover" alt="{{ $c->judul_lomba }}">

                                        </div>

                                        <div class="space-y-1 min-w-0">

                                            <div class="flex items-center gap-1.5 text-[10px] font-bold text-gray-500 dark:text-gray-400">

                                                <span class="uppercase tracking-wider">{{ $c->kategori }}</span>

                                                <span class="text-gray-300 dark:text-gray-600">|</span>

                                                <span>Oleh: {{ $c->creator->nama ?? 'Admin' }}</span>

                                            </div>

                                            <h4 class="font-black text-gray-900 dark:text-white text-sm tracking-tight leading-snug">

                                                {{ $c->judul_lomba }}

                                            </h4>

                                            <span class="text-[11px] text-gray-400 block line-clamp-1">

                                                Penyelenggara: {{ $c->penyelenggara }}

                                            </span>

                                        </div>

                                    </div>

                                </td>



                                <!-- Col 2: Status (Pure Colored Text - Rule #14) -->

                                <td class="py-4 px-4 sm:px-6 align-top whitespace-nowrap">

                                    @if($c->status === 'pending_review')

                                        <span class="text-xs font-bold text-amber-600 dark:text-amber-400">

                                            Perlu Review

                                        </span>

                                    @elseif($c->status === 'buka')

                                        <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">

                                            Aktif Buka

                                        </span>

                                    @elseif($c->status === 'ditolak')

                                        <div class="space-y-0.5">

                                            <span class="text-xs font-bold text-rose-600 dark:text-rose-400 block">

                                                Ditolak

                                            </span>

                                            @if($c->catatan_admin)

                                                <p class="text-[10px] text-rose-500 dark:text-rose-400 max-w-[160px] truncate" title="{{ $c->catatan_admin }}">

                                                    "{{ \Illuminate\Support\Str::limit($c->catatan_admin, 28, '...') }}"

                                                </p>

                                            @endif

                                        </div>

                                    @elseif($c->status === 'selesai')

                                        <span class="text-xs font-bold text-blue-600 dark:text-blue-400">

                                            Selesai

                                        </span>

                                    @else

                                        <span class="text-xs font-bold text-gray-600 dark:text-gray-400">

                                            {{ ucfirst($c->status) }}

                                        </span>

                                    @endif

                                </td>



                                <!-- Col 3: Hadiah -->

                                <td class="py-4 px-4 sm:px-6 align-top whitespace-nowrap">

                                    <span class="text-xs font-black text-gray-900 dark:text-white">

                                        {{ $c->hadiah }}

                                    </span>

                                </td>



                                <!-- Col 4: Periode -->

                                <td class="py-4 px-4 sm:px-6 align-top whitespace-nowrap text-xs text-gray-900 dark:text-white">

                                    <span class="font-bold block">{{ $c->start_date ? $c->start_date->translatedFormat('d M Y') : '-' }}</span>

                                    <span class="block text-[11px] font-medium text-gray-700 dark:text-gray-300">s/d {{ $c->end_date ? $c->end_date->translatedFormat('d M Y') : '-' }}</span>

                                </td>



                                <!-- Col 5: Actions -->

                                <td class="py-4 px-4 sm:px-6 align-top text-center whitespace-nowrap">

                                    <div class="inline-flex items-center justify-center gap-2" x-data="{ menuOpen: false }">

                                        

                                        @if($c->status === 'pending_review')

                                            <!-- Tombol Utama: Tinjau Proposal -->

                                            <button type="button" 

                                                    @click='openProposalModal(@json($contestPayload))' 

                                                    class="px-3 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200/80 dark:border-amber-900/60 font-bold text-xs transition cursor-pointer shadow-2xs">

                                                Tinjau Proposal

                                            </button>

                                        @else

                                            <!-- Tombol Utama: Kelola Lomba (Terpadu: Pendaftar & Submisi) -->

                                            <a href="{{ route('admin.contests.manage', $c->id) }}" 

                                               class="px-3 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200/80 dark:border-amber-900/60 font-bold text-xs transition cursor-pointer shadow-2xs">

                                                Kelola Lomba

                                            </a>

                                        @endif



                                        <!-- Dropdown Menu Opsi Lainnya (•••) -->

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



                                            <!-- Floating Dropdown Menu -->

                                            <div x-show="menuOpen" 

                                                 @click.away="menuOpen = false" 

                                                 x-transition:enter="transition ease-out duration-100" 

                                                 x-transition:enter-start="transform opacity-0 scale-95" 

                                                 x-transition:enter-end="transform opacity-100 scale-100" 

                                                 x-transition:leave="transition ease-in duration-75" 

                                                 x-transition:leave-start="transform opacity-100 scale-100" 

                                                 x-transition:leave-end="transform opacity-0 scale-95" 

                                                 class="absolute right-0 top-full mt-1.5 w-44 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 py-1 z-50 text-left" 

                                                 style="display: none;">

                                                

                                                @if($c->status === 'pending_review')

                                                    <!-- Setujui Langsung -->

                                                    <button type="button"

                                                            @click="menuOpen = false; $dispatch('open-confirm-modal', {

                                                                title: 'Setujui Pengajuan Lomba?',

                                                                message: 'Event lomba {{ addslashes($c->judul_lomba) }} akan resmi dipublikasikan ke katalog publik.',

                                                                buttonText: 'Ya, Setujui Lomba',

                                                                iconType: 'check',

                                                                buttonClass: 'bg-emerald-600 hover:bg-emerald-700 text-white',

                                                                actionUrl: '{{ route('admin.contests.approve', $c->id) }}',

                                                                method: 'POST'

                                                            })"

                                                            class="w-full px-3.5 py-2 text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 transition block text-left cursor-pointer">

                                                        Setujui Lomba

                                                    </button>



                                                    <!-- Tolak -->

                                                    <button type="button"

                                                            @click="menuOpen = false; openRejectModal('{{ route('admin.contests.reject', $c->id) }}', '{{ addslashes($c->judul_lomba) }}')"

                                                            class="w-full px-3.5 py-2 text-xs font-bold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition block text-left border-b border-gray-50 dark:border-gray-700/40 cursor-pointer">

                                                        Tolak Pengajuan

                                                    </button>

                                                @endif



                                                <!-- Edit Lomba -->

                                                <a href="{{ route('admin.contests.edit', $c->id) }}" class="w-full px-3.5 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition block">

                                                    Edit

                                                </a>



                                                <!-- Lihat Halaman Publik -->

                                                <a href="{{ route('public.contests.show', $c->id) }}" target="_blank" class="w-full px-3.5 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition block">

                                                    Lihat

                                                </a>



                                                <!-- Hapus Lomba -->

                                                <button type="button" 

                                                        @click="menuOpen = false; $dispatch('open-confirm-modal', {

                                                            title: 'Hapus Event Lomba?',

                                                            message: 'Apakah Anda yakin ingin menghapus lomba {{ addslashes($c->judul_lomba) }}?',

                                                            buttonText: 'Ya, Hapus Lomba',

                                                            iconType: 'delete',

                                                            buttonClass: 'bg-red-600 hover:bg-red-700 text-white',

                                                            actionUrl: '{{ route('admin.contests.destroy', $c->id) }}',

                                                            method: 'POST'

                                                        })"

                                                        class="w-full px-3.5 py-2 text-xs font-bold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition block text-left cursor-pointer border-t border-gray-100 dark:border-gray-700/60">

                                                    Hapus

                                                </button>

                                            </div>

                                        </div>



                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="5" class="py-12 text-center text-gray-400 font-bold text-xs">

                                    Belum ada event lomba ditemukan pada filter ini.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>



            @if($contests->total() > 0)

                <div class="p-4 bg-gray-50/50 dark:bg-gray-900/50">

                    {{ $contests->links('partials.dashboard.pagination') }}

                </div>

            @endif

        </div>



    </div>



    <!-- ========================================================================= -->

    <!-- MODAL REVIEW PROPOSAL LOMBA FOTO TERPADU (LensMatch Design System)         -->

    <!-- ========================================================================= -->

    <div x-show="proposalModalOpen" 

         @click.away="proposalModalOpen = false" 

         x-cloak 

         class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 bg-black/60 backdrop-blur-xs overflow-y-auto"

         style="display: none;">

        

        <div class="bg-white dark:bg-gray-800 rounded-3xl max-w-2xl w-full p-6 sm:p-8 space-y-6 border border-gray-200 dark:border-gray-700 shadow-2xl text-left my-8" @click.stop>

            

            <!-- Modal Header -->

            <div class="flex items-start justify-between gap-4">

                <div class="space-y-1 min-w-0">

                    <span class="text-[10px] font-black uppercase tracking-wider text-amber-600 dark:text-amber-400 block">

                        Review Proposal Lomba

                    </span>

                    <h3 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight leading-snug" x-text="currentProposal.judul_lomba || 'Detail Proposal Lomba'"></h3>

                    <p class="text-xs text-gray-500 dark:text-gray-400">

                        Diajukan pada <span class="font-bold text-gray-800 dark:text-gray-200" x-text="currentProposal.created_at_formatted"></span>

                    </p>

                </div>

                <button type="button" @click="proposalModalOpen = false" class="p-2 rounded-xl text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition cursor-pointer">

                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>

                </button>

            </div>



            <!-- Normal Review View -->

            <div x-show="!proposalRejectMode" class="space-y-6">

                

                <!-- Section: Pengaju & Banner Cover -->

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <!-- Creator Info Card -->

                    <div class="p-4 rounded-2xl bg-gray-50 dark:bg-gray-900/60 border border-gray-100 dark:border-gray-700/60 space-y-2">

                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Informasi Pengaju</span>

                        <div class="flex items-center gap-3 pt-1">

                            <div class="w-10 h-10 rounded-full bg-amber-400 text-gray-950 font-black text-xs flex items-center justify-center shrink-0 border border-amber-500/30">

                                <span x-text="currentProposal.creator_initials || 'U'"></span>

                            </div>

                            <div class="min-w-0">

                                <h4 class="font-extrabold text-xs text-gray-900 dark:text-white truncate" x-text="currentProposal.creator_name"></h4>

                                <span class="text-[11px] font-bold text-gray-900 dark:text-white" x-text="currentProposal.creator_role"></span>

                                <p class="text-[10px] text-gray-400 truncate" x-text="currentProposal.creator_email"></p>

                            </div>

                        </div>

                    </div>



                    <!-- Banner Thumbnail Preview -->

                    <div class="p-4 rounded-2xl bg-gray-50 dark:bg-gray-900/60 border border-gray-100 dark:border-gray-700/60 space-y-2">

                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Banner Visual Lomba</span>

                        <div class="relative group rounded-xl overflow-hidden bg-gray-200 dark:bg-gray-700 h-20 border border-gray-200 dark:border-gray-600">

                            <img :src="currentProposal.banner_url" class="w-full h-full object-cover" alt="Banner Lomba">

                            <a :href="currentProposal.banner_url" target="_blank" class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-[11px] font-bold">

                                Buka Foto Penuh

                            </a>

                        </div>

                    </div>

                </div>



                <!-- 4 Quick Meta Metrics -->

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">

                    <div class="p-3 rounded-2xl bg-gray-50 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-700/50 space-y-0.5">

                        <span class="text-[10px] font-bold text-gray-400 uppercase">Kategori</span>

                        <p class="text-xs font-black text-gray-900 dark:text-white truncate" x-text="currentProposal.kategori"></p>

                    </div>

                    <div class="p-3 rounded-2xl bg-gray-50 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-700/50 space-y-0.5">

                        <span class="text-[10px] font-bold text-gray-400 uppercase">Total Hadiah</span>

                        <p class="text-xs font-black text-gray-900 dark:text-white truncate" x-text="currentProposal.hadiah"></p>

                    </div>

                    <div class="p-3 rounded-2xl bg-gray-50 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-700/50 space-y-0.5">

                        <span class="text-[10px] font-bold text-gray-400 uppercase">Penyelenggara</span>

                        <p class="text-xs font-black text-gray-900 dark:text-white truncate" x-text="currentProposal.penyelenggara"></p>

                    </div>

                    <div class="p-3 rounded-2xl bg-gray-50 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-700/50 space-y-0.5">

                        <span class="text-[10px] font-bold text-gray-400 uppercase">Periode</span>

                        <p class="text-xs font-black text-gray-900 dark:text-white truncate" x-text="(currentProposal.start_date_formatted || '-') + ' s/d ' + (currentProposal.end_date_formatted || '-')"></p>

                    </div>

                </div>



                <!-- Section: Deskripsi & Konsep -->

                <div class="space-y-1.5">

                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-gray-500 dark:text-gray-400 block">

                        Konsep & Deskripsi Lomba

                    </span>

                    <div class="p-4 rounded-2xl bg-gray-50/70 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-700/60 text-xs text-gray-700 dark:text-gray-300 leading-relaxed max-h-36 overflow-y-auto whitespace-pre-line" x-text="currentProposal.deskrpisi_lomba"></div>

                </div>



                <!-- Section: Syarat & Ketentuan -->

                <div class="space-y-1.5">

                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-gray-500 dark:text-gray-400 block">

                        Syarat & Ketentuan Teknis

                    </span>

                    <div class="p-4 rounded-2xl bg-gray-50/70 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-700/60 text-xs text-gray-700 dark:text-gray-300 leading-relaxed max-h-36 overflow-y-auto whitespace-pre-line" x-text="currentProposal.syarat_ketentuan"></div>

                </div>



                <!-- Action Footer Buttons (Rule #59 Compliant: Max 2 Centered Buttons) -->

                <div class="flex items-center justify-center gap-3 pt-4">

                    <button type="button" @click="proposalRejectMode = true" class="px-4 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200/80 dark:border-rose-900/60 font-bold text-xs transition cursor-pointer">

                        Tolak Pengajuan

                    </button>

                    <form :action="currentProposal.approve_url" method="POST">

                        @csrf

                        <button type="submit" class="px-6 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs rounded-xl shadow-xs transition cursor-pointer">

                            Simpan Data

                        </button>

                    </form>

                </div>

            </div>



            <!-- Reject Reason Input View -->

            <div x-show="proposalRejectMode" class="space-y-5">

                <div class="p-4 rounded-2xl bg-rose-50/60 dark:bg-rose-950/30 border border-rose-200/80 dark:border-rose-900/50 space-y-1">

                    <span class="text-xs font-bold text-rose-700 dark:text-rose-300 block">Menolak Pengajuan Event Lomba</span>

                    <p class="text-[11px] text-rose-600/90 dark:text-rose-400 leading-relaxed">

                        Tuliskan catatan alasan penolakan atau instruksi revisi yang jelas agar pengaju dapat memperbaiki konsep lomba yang diajukan.

                    </p>

                </div>



                <form :action="currentProposal.reject_url" method="POST" class="space-y-4">

                    @csrf

                    <div>

                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">

                            Alasan Penolakan / Catatan Revisi <span class="text-rose-500">*</span>

                        </label>

                        <textarea 

                            name="catatan_admin" 

                            rows="4" 

                            required 

                            x-model="proposalCatatanAdmin" 

                            placeholder="Contoh: Rincian teknis penilaian belum dicantumkan secara lengkap atau banner visual buram. Silakan unggah banner beresolusi tinggi dan ajukan kembali." 

                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-rose-500 transition resize-none"

                        ></textarea>

                    </div>



                    <div class="flex items-center justify-center gap-3 pt-4">
                        <button type="button" @click="proposalRejectMode = false" class="px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold text-xs transition cursor-pointer">
                            Kembali ke Detail
                        </button>
                        <button type="submit" 
                                :disabled="!proposalCatatanAdmin.trim() || proposalCatatanAdmin.length < 5"
                                class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 disabled:opacity-50 text-white font-extrabold text-xs shadow-xs transition cursor-pointer">
                            Konfirmasi Tolak Pengajuan
                        </button>
                    </div>

                </form>

            </div>



        </div>

    </div>



    <!-- Quick Reject Modal Dialog -->

    <div x-show="rejectModalOpen" 

         @click.away="rejectModalOpen = false" 

         x-cloak 

         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"

         style="display: none;">

        

        <div class="bg-white dark:bg-gray-800 rounded-3xl max-w-lg w-full p-6 sm:p-8 space-y-5 border border-gray-200 dark:border-gray-700 shadow-2xl text-left" @click.stop>

            <div class="text-center space-y-1">

                <h3 class="text-base font-black text-gray-900 dark:text-white text-center">Tolak Pengajuan Lomba</h3>

                <p class="text-xs text-gray-400 truncate max-w-xs mx-auto text-center" x-text="rejectContestTitle"></p>

            </div>



            <form :action="rejectActionUrl" method="POST" class="space-y-4">

                @csrf

                <div>

                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">

                        Alasan Penolakan / Catatan Revisi <span class="text-rose-500">*</span>

                    </label>

                    <textarea 

                        name="catatan_admin" 

                        rows="4" 

                        required 

                        x-model="catatanAdmin" 

                        placeholder="Contoh: Banner visual belum memenuhi rasio standar atau hadiah belum dirinci dengan jelas. Silakan perbaiki dan ajukan ulang." 

                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-rose-500 transition resize-none"

                    ></textarea>

                    <span class="text-[11px] text-gray-400 block mt-1">Catatan ini akan dikirimkan dan tampil pada riwayat pengajuan user yang bersangkutan.</span>

                </div>



                <div class="flex items-center justify-center gap-3 pt-2">

                    <button type="button" @click="rejectModalOpen = false" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold text-xs transition cursor-pointer">

                        Batal

                    </button>

                    <button type="submit" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs shadow-xs transition cursor-pointer">

                        Konfirmasi Tolak Pengajuan

                    </button>

                </div>

            </form>

        </div>

    </div>



</div>

@endsection