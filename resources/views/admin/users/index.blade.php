@extends('layouts.admin')

@section('title', 'Manajemen User - LensMatch Admin')

@section('content')
<div x-data="dashboardLiveTable()" class="space-y-6">

    <!-- Page Title Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <span class="text-[11px] font-extrabold uppercase tracking-widest text-amber-600 dark:text-amber-400">Pengelolaan Akun Klien</span>
            <h1 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">Manajemen User</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Kelola data seluruh akun klien pengguna platform, monitoring status registrasi, dan informasi kontak penyewa jasa</p>
        </div>
    </div>

    <!-- 4 Clean Summary Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Klien Terdaftar -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
            <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Total Klien Terdaftar</span>
            <h2 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ $totalClients }} Klien</h2>
            <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">Akun klien di database</p>
        </div>

        <!-- Card 2: Email Terverifikasi -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
            <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Email Terverifikasi</span>
            <h2 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ $totalVerifiedClients }} Akun</h2>
            <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">Status akun aktif</p>
        </div>

        <!-- Card 3: Total Transaksi Sesi Foto -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
            <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Total Booking Klien</span>
            <h2 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ $totalBookings }} Sesi</h2>
            <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">Reservasi pemotretan klien</p>
        </div>

        <!-- Card 4: Total Belanja Klien -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
            <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Total Belanja Klien</span>
            <h2 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">Rp {{ number_format($totalSpending, 0, ',', '.') }}</h2>
            <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">Akumulasi nilai pesanan sesi foto</p>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm overflow-hidden space-y-4">
        
        <!-- Search Toolbar & Filter (Alpine.js Custom Floating Dropdown) -->
        <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex flex-col md:flex-row items-center justify-between gap-4">
            <!-- Filter Urutan di Kiri -->
            <div class="flex items-center gap-3 w-full md:w-auto">
                @php
                    $sortLabels = [
                        'latest' => 'Urutan: Klien Terbaru',
                        'oldest' => 'Urutan: Klien Terlama',
                        'name_asc' => 'Nama Klien (A-Z)'
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

            <!-- Search di Kanan -->
            <div class="relative w-full md:w-80">
                <input type="text" 
                       name="search" 
                       value="{{ $search }}" @input="handleSearch($event)" 
                       placeholder="Cari nama, email, nomor HP klien..." 
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
                        <th class="py-3.5 px-4 sm:px-6">Klien & Kontak</th>
                        <th class="py-3.5 px-4 sm:px-6">Tipe Akun</th>
                        <th class="py-3.5 px-4 sm:px-6">Tanggal Bergabung</th>
                        <th class="py-3.5 px-4 sm:px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60 text-xs">
                    @forelse($users as $usr)
                        <tr class="hover:bg-amber-50/30 dark:hover:bg-amber-950/20 transition">
                            <!-- User Profile -->
                            <td class="py-4 px-4 sm:px-6 align-middle"><div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-amber-500 to-amber-300 text-white font-black text-xs flex items-center justify-center shrink-0 shadow-xs">
                                    {{ strtoupper(substr($usr->nama ?? 'K', 0, 2)) }}
                                </div>
                                <div class="min-w-0">
                                    <h4 class="font-extrabold text-gray-900 dark:text-white truncate">{{ $usr->nama }}</h4>
                                    <p class="text-[10px] text-gray-400 truncate">{{ $usr->email }} • {{ $usr->no_hp ?: '-' }}</p>
                                </div>
                                </div>
                            </td>

                            <!-- Role Badge (Murni Klien) -->
                            <td class="py-4 px-4 sm:px-6 align-middle whitespace-nowrap">
                                <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">
                                    Klien
                                </span>
                            </td>

                            <!-- Tanggal Bergabung -->
                            <td class="py-4 px-4 sm:px-6 text-gray-600 dark:text-gray-400 font-semibold whitespace-nowrap">
                                {{ $usr->created_at->format('d M Y') }}
                            </td>

                            <!-- Aksi -->
                            <td class="py-4 px-4 sm:px-6 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button type="button" 
                                            @click="$dispatch('open-confirm-modal', {
                                                title: 'Hapus Akun Klien?',
                                                message: 'Apakah Anda yakin ingin menghapus akun klien {{ addslashes($usr->nama) }} ({{ addslashes($usr->email) }})?',
                                                confirmText: 'Ya, Hapus Akun',
                                                type: 'danger',
                                                actionUrl: '{{ route('admin.users.destroy', $usr->id) }}',
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
                            <td colspan="4" class="py-12 text-center text-gray-400 font-bold text-xs">
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
</div>
@endsection