@extends('layouts.admin')

@section('title', 'Manajemen Klien - LensMatch Admin')

@section('content')
<div x-data="{
    ...dashboardLiveTable(),
    detailModalOpen: false,
    loadingDetail: false,
    clientData: null,
    suspendModalOpen: false,
    suspendActionUrl: '',
    suspendClientName: '',
    suspendReason: '',

    openDetailModal(id) {
        this.detailModalOpen = true;
        this.loadingDetail = true;
        this.clientData = null;
        fetch('{{ url('/admin/users') }}/' + id, {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            this.clientData = data;
            this.loadingDetail = false;
        })
        .catch(err => {
            console.error(err);
            this.loadingDetail = false;
        });
    },

    openSuspendModal(url, name) {
        this.suspendActionUrl = url;
        this.suspendClientName = name;
        this.suspendReason = '';
        this.suspendModalOpen = true;
    }
}" class="space-y-6">

    <!-- Page Title Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">Manajemen Klien</h1>
        </div>
    </div>

    <!-- 4 Clean Summary Metric Cards (Rule #64 Compliant) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Klien Terdaftar -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Total Klien Terdaftar</span>
            <h2 class="text-xs font-black text-gray-900 dark:text-white tracking-tight">{{ $totalClients }} Klien</h2>
        </div>

        <!-- Card 2: Email Terverifikasi -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Email Terverifikasi</span>
            <h2 class="text-xs font-black text-gray-900 dark:text-white tracking-tight">{{ $totalVerifiedClients }} Akun</h2>
        </div>

        <!-- Card 3: Menunggu Aktivasi Manual -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Menunggu Aktivasi Manual</span>
            <h2 class="text-xs font-black text-gray-900 dark:text-white tracking-tight">{{ $totalPendingActivation }} Permintaan</h2>
        </div>

        <!-- Card 4: Total Belanja Klien -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Total Belanja Klien</span>
            <h2 class="text-xs font-black text-gray-900 dark:text-white tracking-tight">Rp {{ number_format($totalSpending, 0, ',', '.') }}</h2>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm overflow-hidden">
        
        <!-- Search Toolbar & Filter (Alpine.js Custom Floating Dropdown) -->
        <div class="p-4 flex flex-col md:flex-row items-center justify-between gap-4">
            <!-- Filter di Kiri -->
            <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                <!-- 1. Filter Status Akun -->
                @php
                    $statusLabels = [
                        'all' => 'Semua Status Akun',
                        'activation_requested' => 'Menunggu Aktivasi Manual',
                        'unverified' => 'Belum Verifikasi Email',
                        'verified' => 'Email Terverifikasi',
                        'active' => 'Akun Aktif',
                        'suspended' => 'Akun Dibekukan',
                    ];
                    $curStatusName = $statusLabels[$status ?? 'all'] ?? 'Semua Status Akun';
                @endphp
                <div class="relative w-full sm:w-auto min-w-[200px]" x-data="{ open: false, selectedId: '{{ $status ?? 'all' }}', selectedName: '{{ $curStatusName }}' }">
                    <input type="hidden" name="status" :value="selectedId">
                    <button @click="open = !open" type="button" 
                            class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs font-bold text-gray-800 dark:text-white flex items-center justify-between gap-2 shadow-2xs hover:border-amber-400 dark:hover:border-amber-500 cursor-pointer transition">
                        <span x-text="selectedName" class="truncate"></span>
                        <svg class="w-3.5 h-3.5 text-gray-400 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" @click.away="open = false" x-transition 
                         class="absolute z-50 left-0 mt-2 min-w-[220px] w-full bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700/80 py-1 max-h-60 overflow-y-auto" style="display: none;">
                        @foreach($statusLabels as $stKey => $stVal)
                            <button type="button" @click="selectedId = '{{ $stKey }}'; selectedName = '{{ $stVal }}'; open = false; applyFilter('status', '{{ $stKey }}')" 
                                    class="w-full px-3.5 py-2 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition flex items-center justify-between text-left cursor-pointer border-b border-gray-50 dark:border-gray-700/40 last:border-0"
                                    :class="selectedId === '{{ $stKey }}' ? 'bg-amber-50/70 dark:bg-amber-950/30 text-amber-600 font-extrabold' : ''">
                                <span>{{ $stVal }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- 2. Filter Urutan -->
                @php
                    $sortLabels = [
                        'latest' => 'Urutan: Klien Terbaru',
                        'oldest' => 'Urutan: Klien Terlama',
                        'name_asc' => 'Nama Klien (A-Z)',
                        'activation_requested' => 'Prioritas Bantuan Aktivasi',
                    ];
                    $curSortName = $sortLabels[$sort ?? 'latest'] ?? 'Urutan: Klien Terbaru';
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

            <!-- Search di Kanan (Rule #183) -->
            <div class="relative w-full md:w-80">
                <input type="text" 
                       name="search" 
                       value="{{ $search }}" @input="handleSearch($event)" 
                       placeholder="Cari nama, email, nomor HP klien..." 
                       class="w-full pl-4 pr-10 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">
                <svg class="w-4 h-4 text-gray-400 absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </div>

        <div id="dashboard-table-container" class="transition-opacity duration-200" :class="isLoading ? 'opacity-40 pointer-events-none' : 'opacity-100'">

        <!-- Table (min-h-[240px] untuk memastikan popover dropdown tidak terpotong saat data 1-2 baris) -->
        <div class="overflow-x-auto min-h-[240px]">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-[11px] font-extrabold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        <th class="py-3.5 px-4 sm:px-6">Klien</th>
                        <th class="py-3.5 px-4 sm:px-6">Status Email</th>
                        <th class="py-3.5 px-4 sm:px-6">Status Akun</th>
                        <th class="py-3.5 px-4 sm:px-6">Tanggal Bergabung</th>
                        <th class="py-3.5 px-4 sm:px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60 text-xs">
                    @forelse($users as $usr)
                        <tr class="hover:bg-amber-50/30 dark:hover:bg-amber-950/20 transition">
                            <!-- Klien Info -->
                            <td class="py-4 px-4 sm:px-6 align-middle">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-amber-500 to-amber-300 text-white font-black text-xs flex items-center justify-center shrink-0 shadow-xs">
                                        {{ $usr->initials }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <h4 class="text-xs font-extrabold text-gray-900 dark:text-white truncate">{{ $usr->nama }}</h4>
                                        </div>
                                        <p class="text-xs text-gray-400 truncate">
                                            {{ $usr->email }} • {{ $usr->roleClient?->nomor_telepon ?: '-' }}
                                        </p>
                                        @if($usr->isActivationRequested())
                                            <span class="inline-block mt-1 text-[11px] font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 border border-amber-200/80 dark:border-amber-900/60 px-2 py-0.5 rounded-full">
                                                Menunggu Aktivasi Manual
                                            </span>
                                        @endif
                                        @if($usr->is_suspended && $usr->suspension_reason)
                                            <p class="text-[11px] text-rose-500 dark:text-rose-400 font-medium truncate mt-0.5">
                                                Alasan: {{ $usr->suspension_reason }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Status Email (Pure Colored Text Rule #14 & Rule #68) -->
                            <td class="py-4 px-4 sm:px-6 align-middle whitespace-nowrap">
                                @if($usr->isEmailVerified())
                                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">
                                        Terverifikasi
                                    </span>
                                @else
                                    <span class="text-xs font-bold text-amber-600 dark:text-amber-400">
                                        Belum Verifikasi
                                    </span>
                                @endif
                            </td>

                            <!-- Status Akun Moderasi (Pure Colored Text Rule #14 & Rule #68) -->
                            <td class="py-4 px-4 sm:px-6 align-middle whitespace-nowrap">
                                @if($usr->is_suspended)
                                    <span class="text-xs font-bold text-rose-600 dark:text-rose-400">
                                        Dibekukan
                                    </span>
                                @else
                                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">
                                        Aktif
                                    </span>
                                @endif
                            </td>

                            <!-- Tanggal Bergabung -->
                            <td class="py-4 px-4 sm:px-6 text-gray-600 dark:text-gray-400 font-semibold whitespace-nowrap">
                                {{ $usr->created_at->format('d M Y') }}
                            </td>

                            <!-- Aksi (Rule #80 & Rule #69 Compliant) -->
                            <td class="py-4 px-4 sm:px-6 text-center whitespace-nowrap">
                                @php
                                    $isDropup = ($loop->count > 2 && ($loop->last || $loop->remaining < 2)) || ($loop->count === 2 && $loop->last);
                                @endphp
                                <div class="inline-flex items-center justify-center gap-1.5" x-data="{ menuOpen: false }">
                                    <!-- 1. Tombol Utama: Lihat (Soft Blue Rule #80) -->
                                    <button type="button" 
                                            @click="openDetailModal({{ $usr->id }})"
                                            class="px-3 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200/80 dark:border-blue-900/60 font-bold text-xs transition min-w-[60px] text-center cursor-pointer shadow-2xs">
                                        Lihat
                                    </button>

                                    <!-- 2. Tombol Kondisional: Verifikasi Email (Soft Emerald) jika belum verifikasi -->
                                    @if(!$usr->isEmailVerified())
                                        <button type="button" 
                                                @click="$dispatch('open-confirm-modal', {
                                                    title: 'Verifikasi Email Klien?',
                                                    message: 'Apakah Anda yakin ingin memverifikasi email akun klien {{ addslashes($usr->nama) }} ({{ addslashes($usr->email) }}) secara manual? Akun akan langsung aktif seketika.',
                                                    confirmText: 'Ya, Verifikasi Email',
                                                    type: 'info',
                                                    actionUrl: '{{ route('admin.users.verify-email', $usr->id) }}',
                                                    method: 'POST'
                                                })"
                                                class="px-3 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-900/60 font-bold text-xs transition text-center cursor-pointer shadow-2xs">
                                            Verifikasi Email
                                        </button>
                                    @endif

                                    <!-- 3. Kebab Dropdown Menu untuk Aksi Moderasi & Hapus (Rule #69) -->
                                    <div class="relative inline-block text-left">
                                        <button type="button" 
                                                @click="menuOpen = !menuOpen"
                                                class="w-8 h-8 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white flex items-center justify-center transition shadow-2xs cursor-pointer">
                                            <svg class="w-4 h-4 pointer-events-none" fill="currentColor" viewBox="0 0 24 24">
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
                                             class="absolute right-0 {{ $isDropup ? 'bottom-full mb-1.5 origin-bottom-right' : 'top-full mt-1.5 origin-top-right' }} w-44 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 z-50 text-left overflow-hidden py-1"
                                             style="display: none;">
                                            <!-- Aksi Bekukan / Aktifkan Akun -->
                                            @if($usr->is_suspended)
                                                <button type="button" 
                                                        @click="menuOpen = false; $dispatch('open-confirm-modal', {
                                                            title: 'Aktifkan Kembali Akun Klien?',
                                                            message: 'Apakah Anda yakin ingin memulihkan status aktif untuk akun klien {{ addslashes($usr->nama) }}?',
                                                            confirmText: 'Ya, Aktifkan Akun',
                                                            type: 'info',
                                                            actionUrl: '{{ route('admin.users.toggle-suspend', $usr->id) }}',
                                                            method: 'POST'
                                                        })"
                                                        class="w-full px-4 py-2 text-xs font-bold text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 text-left cursor-pointer transition block">
                                                    Aktifkan Akun
                                                </button>
                                            @else
                                                <button type="button" 
                                                        @click="menuOpen = false; openSuspendModal('{{ route('admin.users.toggle-suspend', $usr->id) }}', '{{ addslashes($usr->nama) }}')"
                                                        class="w-full px-4 py-2 text-xs font-bold text-amber-700 hover:bg-amber-50 dark:hover:bg-amber-950/40 text-left cursor-pointer transition block">
                                                    Bekukan Akun
                                                </button>
                                            @endif

                                            <!-- Aksi Hapus (Hanya jika belum pernah transaksi) -->
                                            @php
                                                $contractCount = $usr->roleClient?->contracts()->count() ?? 0;
                                            @endphp
                                            @if($contractCount === 0)
                                                <button type="button" 
                                                        @click="menuOpen = false; $dispatch('open-confirm-modal', {
                                                            title: 'Hapus Akun Klien?',
                                                            message: 'Apakah Anda yakin ingin menghapus akun klien {{ addslashes($usr->nama) }} ({{ addslashes($usr->email) }})?',
                                                            confirmText: 'Ya, Hapus Akun',
                                                            type: 'danger',
                                                            actionUrl: '{{ route('admin.users.destroy', $usr->id) }}',
                                                            method: 'POST'
                                                        })"
                                                        class="w-full px-4 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 text-left cursor-pointer transition block border-t border-gray-100 dark:border-gray-700/60">
                                                    Hapus Akun
                                                </button>
                                            @else
                                                <div class="px-4 py-2 text-gray-400 dark:text-gray-500 text-[11px] font-semibold border-t border-gray-100 dark:border-gray-700/60 cursor-not-allowed select-none">
                                                    Hapus (Ada Transaksi)
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-gray-400 font-bold text-xs">
                                Belum ada akun klien ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Standard Pagination Footer -->
        @if($users->total() > 0)
            <div class="p-4 border-t border-gray-200 dark:border-gray-700/80 bg-gray-50/50 dark:bg-gray-900/50">
                {{ $users->links('partials.dashboard.pagination') }}
            </div>
        @endif
        </div>
    </div>

    <!-- Modal 1: Detail Profil & Riwayat Transaksi Klien (Rule #59 Compliant) -->
    <div x-show="detailModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs"
         @keydown.escape.window="detailModalOpen = false"
         style="display: none;">
        <div class="w-full max-w-2xl bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 sm:p-7 shadow-2xl space-y-6 max-h-[90vh] overflow-y-auto"
             @click.away="detailModalOpen = false">
            
            <!-- Judul Modal Rata Tengah Tanpa Ikon (Rule #59) -->
            <div class="text-center space-y-1">
                <h3 class="text-base sm:text-lg font-black text-gray-900 dark:text-white tracking-tight">
                    Detail Profil Klien
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Informasi lengkap biodata profil, kontak, serta riwayat pemesanan klien di platform.
                </p>
            </div>

            <!-- Loading State -->
            <div x-show="loadingDetail" class="py-12 text-center text-gray-400 text-xs font-bold space-y-2">
                <svg class="animate-spin h-6 w-6 text-amber-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <p>Memuat rincian data klien...</p>
            </div>

            <!-- Content State -->
            <div x-show="!loadingDetail && clientData" class="space-y-6">
                <!-- 1. Header Biodata & Kontak -->
                <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-900/60 border border-gray-200/80 dark:border-gray-700/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3.5">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-amber-500 to-amber-300 text-white font-black text-base flex items-center justify-center shrink-0 shadow-xs"
                             x-text="clientData?.initials">
                        </div>
                        <div class="space-y-0.5">
                            <h4 class="text-sm font-black text-gray-900 dark:text-white" x-text="clientData?.nama"></h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400" x-text="clientData?.email"></p>
                            <p class="text-xs font-bold text-gray-700 dark:text-gray-300 pt-0.5" x-text="clientData?.phone"></p>
                        </div>
                    </div>
                    <div class="text-left sm:text-right space-y-1.5 text-xs">
                        <div class="flex sm:justify-end items-center gap-2">
                            <span class="text-gray-400 dark:text-gray-500">Status Akun:</span>
                            <span class="font-bold text-xs" 
                                  :class="clientData?.is_suspended ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400'" 
                                  x-text="clientData?.is_suspended ? 'Dibekukan' : 'Aktif'"></span>
                        </div>
                        <div class="flex sm:justify-end items-center gap-2">
                            <span class="text-gray-400 dark:text-gray-500">Verifikasi Email:</span>
                            <span class="font-bold text-xs" 
                                  :class="clientData?.email_verified ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400'" 
                                  x-text="clientData?.email_verified ? 'Terverifikasi' : 'Belum Verifikasi'"></span>
                        </div>
                        <div class="flex sm:justify-end items-center gap-2 text-[11px] text-gray-500 dark:text-gray-400">
                            <span>Bergabung:</span>
                            <span class="font-semibold text-gray-700 dark:text-gray-300" x-text="clientData?.joined_at"></span>
                        </div>
                    </div>
                </div>

                <!-- 2. Permintaan Bantuan Aktivasi Manual Box (Jika Ada) -->
                <template x-if="clientData?.activation_requested">
                    <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200/80 dark:border-amber-900/60 text-xs text-amber-900 dark:text-amber-300 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="font-extrabold">Permintaan Bantuan Aktivasi Klien</span>
                            <span class="text-[11px] text-amber-700 dark:text-amber-400 font-bold" x-text="clientData?.activation_requested_at"></span>
                        </div>
                        <p class="text-xs text-amber-800 dark:text-amber-300 font-medium leading-relaxed" x-text="'Catatan Klien: ' + (clientData?.activation_request_note || 'Email aktivasi tidak masuk ke inbox saya.')"></p>
                    </div>
                </template>

                <!-- 3. Empat Statistik Personal Klien -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-700/60 text-center space-y-0.5">
                        <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider block">Total Booking</span>
                        <h5 class="text-xs font-black text-gray-900 dark:text-white" x-text="clientData?.stats?.total_bookings + ' Sesi'"></h5>
                    </div>
                    <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-700/60 text-center space-y-0.5">
                        <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider block">Total Belanja</span>
                        <h5 class="text-xs font-black text-gray-900 dark:text-white" x-text="clientData?.stats?.total_spent_formatted"></h5>
                    </div>
                    <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-700/60 text-center space-y-0.5">
                        <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider block">Ulasan Ditulis</span>
                        <h5 class="text-xs font-black text-gray-900 dark:text-white" x-text="clientData?.stats?.total_reviews + ' Ulasan'"></h5>
                    </div>
                    <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-700/60 text-center space-y-0.5">
                        <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider block">Partisipasi</span>
                        <h5 class="text-xs font-black text-gray-900 dark:text-white" x-text="clientData?.stats?.total_contests + ' Lomba, ' + clientData?.stats?.total_courses + ' E-Learning'"></h5>
                    </div>
                </div>

                <!-- 4. Riwayat Booking Terakhir (Maks 5 Pesanan) -->
                <div class="space-y-3">
                    <h5 class="text-xs font-extrabold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                        Riwayat Booking Terakhir
                    </h5>

                    <template x-if="clientData?.recent_bookings && clientData?.recent_bookings.length > 0">
                        <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-gray-50 dark:bg-gray-900/50 text-[10px] font-extrabold uppercase text-gray-500 border-b border-gray-200 dark:border-gray-700">
                                    <tr>
                                        <th class="p-2.5">Layanan Fotografi</th>
                                        <th class="p-2.5">Jadwal Sesi</th>
                                        <th class="p-2.5">Nominal</th>
                                        <th class="p-2.5">Status</th>
                                        <th class="p-2.5 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
                                    <template x-for="b in clientData.recent_bookings" :key="b.id">
                                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30">
                                            <td class="p-2.5">
                                                <div class="font-bold text-gray-900 dark:text-white" x-text="b.photographer_name"></div>
                                                <div class="text-[11px] text-gray-500 dark:text-gray-400" x-text="b.service_name"></div>
                                            </td>
                                            <td class="p-2.5 text-gray-600 dark:text-gray-400 font-semibold" x-text="b.date_formatted"></td>
                                            <td class="p-2.5 font-black text-gray-900 dark:text-white" x-text="b.amount_formatted"></td>
                                            <td class="p-2.5 whitespace-nowrap">
                                                <span class="font-bold text-xs" 
                                                      :class="{
                                                          'text-emerald-600 dark:text-emerald-400': b.status_contract === 'completed',
                                                          'text-blue-600 dark:text-blue-400': b.status_contract === 'active',
                                                          'text-amber-600 dark:text-amber-400': b.status_contract === 'pending_payment' || b.status_contract === 'approved',
                                                          'text-gray-400': b.status_contract === 'cancelled'
                                                      }" 
                                                      x-text="b.status_label"></span>
                                            </td>
                                            <td class="p-2.5 text-center whitespace-nowrap">
                                                <a :href="b.invoice_url" 
                                                   class="px-2.5 py-1 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200/80 dark:border-blue-900/60 font-bold text-[11px] transition inline-block">
                                                    Lihat
                                                </a>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </template>

                    <template x-if="!clientData?.recent_bookings || clientData?.recent_bookings.length === 0">
                        <div class="p-6 text-center text-gray-400 text-xs font-bold bg-gray-50/50 dark:bg-gray-900/30 rounded-xl border border-gray-100 dark:border-gray-800">
                            Klien belum memiliki riwayat pesanan booking.
                        </div>
                    </template>
                </div>
            </div>

            <!-- Tombol Bawah Rata Tengah (Rule #59) -->
            <div class="flex items-center justify-center pt-2">
                <button type="button" 
                        @click="detailModalOpen = false" 
                        class="px-6 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold text-xs transition cursor-pointer shadow-2xs">
                    Tutup
                </button>
            </div>

        </div>
    </div>

    <!-- Modal 2: Form Bekukan Akun Klien (Rule #59 Compliant) -->
    <div x-show="suspendModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs"
         @keydown.escape.window="suspendModalOpen = false"
         style="display: none;">
        <div class="w-full max-w-md bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 sm:p-7 shadow-2xl space-y-5"
             @click.away="suspendModalOpen = false">
            
            <!-- Judul Rata Tengah Tanpa Ikon (Rule #59) -->
            <div class="text-center space-y-1.5">
                <h3 class="text-base sm:text-lg font-black text-gray-900 dark:text-white tracking-tight">
                    Bekukan Akun Klien?
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed max-w-xs mx-auto">
                    Klien <span class="font-bold text-gray-900 dark:text-white" x-text="suspendClientName"></span> tidak akan dapat login atau melakukan pemesanan baru. Riwayat transaksi masa lalu tetap tersimpan secara aman.
                </p>
            </div>

            <form :action="suspendActionUrl" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                        Alasan Pembekuan (Opsional)
                    </label>
                    <textarea name="reason" 
                              x-model="suspendReason"
                              rows="3" 
                              maxlength="255"
                              placeholder="Contoh: Terindikasi spam ulasan atau pelanggaran ketentuan layanan."
                              class="w-full p-3.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition resize-none"></textarea>
                </div>

                <!-- Maksimal 2 Tombol Bawah Rata Tengah (Rule #59) -->
                <div class="flex items-center justify-center gap-3 pt-2">
                    <button type="button" 
                            @click="suspendModalOpen = false" 
                            class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold text-xs transition cursor-pointer shadow-2xs">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-6 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs rounded-xl shadow-xs transition cursor-pointer">
                        Ya, Bekukan Akun
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>
@endsection
