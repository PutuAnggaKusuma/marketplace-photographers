@extends('layouts.admin')

@section('title', 'Log Aktivitas & Audit Trail Admin - LensMatch')

@section('content')
<div class="space-y-6" x-data="{
    ...dashboardLiveTable(),
    detailModalOpen: false,
    activeLog: null,
    openDetail(log) {
        this.activeLog = log;
        this.detailModalOpen = true;
    }
}">

    <!-- Page Title Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">Log Aktivitas</h1>
        </div>

        @if(auth()->user()->role === 'super_admin')
        <div>
            <button type="button" 
                    @click="$dispatch('open-confirm-modal', {
                        title: 'Bersihkan Log Aktivitas Lama',
                        message: 'Seluruh riwayat log aktivitas yang telah berumur lebih dari 30 hari akan dihapus secara permanen. Lanjutkan tindakan ini?',
                        actionUrl: '{{ route('admin.activity_logs.clear') }}',
                        method: 'POST',
                        buttonText: 'Ya, Bersihkan Log',
                        buttonClass: 'bg-red-600 hover:bg-red-700 text-white'
                    })"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-700 dark:bg-rose-950/40 dark:hover:bg-rose-900/60 dark:text-rose-400 rounded-xl font-bold text-xs transition duration-150 active:scale-95 border border-rose-200 dark:border-rose-900/50">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                </svg>
                <span>Bersihkan Log</span>
            </button>
        </div>
        @endif
    </div>

    <!-- 4 Summary Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Log Aktivitas -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
            <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Total Audit Log</span>
            <h2 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ number_format($totalLogs, 0, ',', '.') }}</h2>
        </div>

        <!-- Card 2: Log Transaksi & Rekber -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
            <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Transaksi & Rekber</span>
            <h2 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ number_format($totalTransactions, 0, ',', '.') }}</h2>
        </div>

        <!-- Card 3: Log Booking & Reservasi -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
            <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Booking & Sesi Foto</span>
            <h2 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ number_format($totalBookings, 0, ',', '.') }}</h2>
        </div>

        <!-- Card 4: Log Sistem & Pengguna -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
            <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Sistem & Keamanan</span>
            <h2 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ number_format($totalSystem, 0, ',', '.') }}</h2>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm overflow-hidden space-y-4">
        
        <!-- Search Toolbar & Filter (Alpine.js Custom Floating Dropdown) -->
        <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex flex-col md:flex-row items-center justify-between gap-4">
            <!-- Filter di Kiri -->
            <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                <!-- 1. Dropdown Filter Tipe Aktivitas -->
                @php
                    $curLogTypeName = $logFilter === 'all' ? 'Semua Tipe Aktivitas' : ucwords(str_replace('_', ' ', $logFilter));
                @endphp
                <div class="relative w-full sm:w-auto min-w-[190px]" x-data="{ open: false, selectedId: '{{ $logFilter }}', selectedName: '{{ $curLogTypeName }}' }">
                    <button @click="open = !open" type="button" 
                            class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs font-bold text-gray-800 dark:text-white flex items-center justify-between gap-2 shadow-2xs hover:border-amber-400 dark:hover:border-amber-500 cursor-pointer transition">
                        <span x-text="selectedName" class="truncate"></span>
                        <svg class="w-3.5 h-3.5 text-gray-400 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" @click.away="open = false" x-transition 
                         class="absolute z-50 left-0 mt-2 min-w-[210px] w-full bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700/80 py-1 max-h-60 overflow-y-auto" style="display: none;">
                        <button type="button" @click="selectedId = 'all'; selectedName = 'Semua Tipe Aktivitas'; open = false; applyFilter('log_name', 'all')" 
                                class="w-full px-3.5 py-2 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition flex items-center justify-between text-left cursor-pointer border-b border-gray-50 dark:border-gray-700/40"
                                :class="selectedId === 'all' ? 'bg-amber-50/70 dark:bg-amber-950/30 text-amber-600 font-extrabold' : ''">
                            <span>Semua Tipe Aktivitas</span>
                        </button>
                        @foreach($logTypes as $lt)
                            <button type="button" @click="selectedId = '{{ $lt }}'; selectedName = '{{ ucwords(str_replace('_', ' ', $lt)) }}'; open = false; applyFilter('log_name', '{{ $lt }}')" 
                                    class="w-full px-3.5 py-2 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition flex items-center justify-between text-left cursor-pointer border-b border-gray-50 dark:border-gray-700/40 last:border-0"
                                    :class="selectedId === '{{ $lt }}' ? 'bg-amber-50/70 dark:bg-amber-950/30 text-amber-600 font-extrabold' : ''">
                                <span>{{ ucwords(str_replace('_', ' ', $lt)) }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- 2. Dropdown Filter Urutan -->
                @php
                    $sortLabels = [
                        'latest' => 'Urutan: Terbaru',
                        'oldest' => 'Urutan: Terlama'
                    ];
                    $curSortName = $sortLabels[$sort ?? 'latest'] ?? 'Urutan: Terbaru';
                @endphp
                <div class="relative w-full sm:w-auto min-w-[170px]" x-data="{ open: false, selectedId: '{{ $sort ?? 'latest' }}', selectedName: '{{ $curSortName }}' }">
                    <button @click="open = !open" type="button" 
                            class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs font-bold text-gray-800 dark:text-white flex items-center justify-between gap-2 shadow-2xs hover:border-amber-400 dark:hover:border-amber-500 cursor-pointer transition">
                        <span x-text="selectedName" class="truncate"></span>
                        <svg class="w-3.5 h-3.5 text-gray-400 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" @click.away="open = false" x-transition 
                         class="absolute z-50 left-0 mt-2 min-w-[180px] w-full bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700/80 py-1 max-h-60 overflow-y-auto" style="display: none;">
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
                       placeholder="Cari deskripsi, pelaku, email..." 
                       class="w-full pl-9 pr-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </div>

        <div id="dashboard-table-container" class="transition-opacity duration-200" :class="isLoading ? 'opacity-40 pointer-events-none' : 'opacity-100'">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-700/60 bg-gray-50/75 dark:bg-gray-900/30 text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        <th class="py-3.5 px-4 sm:px-6">Waktu</th>
                        <th class="py-3.5 px-4">Pengguna / Pelaku</th>
                        <th class="py-3.5 px-4">Tipe Aktivitas</th>
                        <th class="py-3.5 px-4 sm:px-6">Deskripsi Peristiwa</th>
                        <th class="py-3.5 px-4 text-center">Metadata</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50 text-sm">
                    @forelse($logs as $log)
                    <tr class="hover:bg-amber-50/30 dark:hover:bg-gray-750 transition duration-150">
                        <!-- Waktu -->
                        <td class="py-4 px-4 sm:px-6 whitespace-nowrap">
                            <div class="font-bold text-gray-900 dark:text-white text-xs">
                                {{ $log->created_at->format('d M Y') }}
                            </div>
                            <div class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">
                                {{ $log->created_at->format('H:i') }} WIB ({{ $log->created_at->diffForHumans() }})
                            </div>
                        </td>

                        <!-- Pengguna / Pelaku -->
                        <td class="py-4 px-4 whitespace-nowrap">
                            @if($log->user)
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400 font-extrabold flex items-center justify-center text-xs shrink-0">
                                    {{ $log->user->initials ?? 'U' }}
                                </div>
                                <div class="truncate max-w-[140px]">
                                    <div class="font-bold text-gray-900 dark:text-white text-xs truncate">
                                        {{ $log->user->nama }}
                                    </div>
                                    <div class="text-[11px] text-gray-400 dark:text-gray-500 font-medium mt-0.5 truncate">
                                        @if($log->user->role === 'super_admin')
                                            Super Admin
                                        @elseif($log->user->role === 'admin')
                                            Staff Admin
                                        @elseif($log->user->role === 'photographer')
                                            Fotografer
                                        @else
                                            Klien
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @else
                            <span class="text-xs text-gray-400 italic">Sistem Otomatis</span>
                            @endif
                        </td>

                        <!-- Tipe Aktivitas -->
                        <td class="py-4 px-4 whitespace-nowrap">
                            <span class="text-xs font-bold text-gray-800 dark:text-gray-200">
                                {{ ucwords(str_replace('_', ' ', $log->log_name ?? 'Aktivitas')) }}
                            </span>
                        </td>

                        <!-- Deskripsi Peristiwa -->
                        <td class="py-4 px-4 sm:px-6">
                            <p class="text-xs text-gray-800 dark:text-gray-200 leading-relaxed font-medium">
                                {{ $log->description }}
                            </p>
                        </td>

                        <!-- Metadata -->
                        <td class="py-4 px-4 text-center whitespace-nowrap">
                            @if(!empty($log->properties))
                            <button type="button" 
                                    @click="openDetail({{ json_encode($log) }})"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-200 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                                </svg>
                                <span>Detail</span>
                            </button>
                            @else
                            <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-gray-400">
                            <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                            <span class="text-sm font-semibold">Belum ada riwayat aktivitas yang tercatat.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Custom Pagination -->
        @if($logs->hasPages() || $logs->total() > 0)
        <div class="px-4 py-3.5 border-t border-gray-100 dark:border-gray-700/60 bg-gray-50/50 dark:bg-gray-900/30">
            {{ $logs->links('partials.public.pagination') }}
        </div>
        @endif
        </div>
    </div>

    <!-- Detail Modal (Rule #59 Compliant) -->
    <div x-show="detailModalOpen" 
         x-transition:enter="transition ease-out duration-200" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="transition ease-in duration-150" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs" 
         style="display: none;">
        <div @click.away="detailModalOpen = false" class="bg-white dark:bg-gray-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl relative border border-gray-200 dark:border-gray-700">


            <!-- Centered Title without icons (Rule #59) -->
            <h3 class="text-lg font-bold text-center text-gray-900 dark:text-white mb-4">Detail Metadata Aktivitas</h3>

            <div class="space-y-4">
                <div>
                    <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Peristiwa</label>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white mt-0.5" x-text="activeLog?.description"></p>
                </div>

                <div>
                    <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Payload Data (JSON)</label>
                    <pre class="mt-1 p-3 bg-gray-50 dark:bg-gray-900 rounded-xl text-xs font-mono text-gray-800 dark:text-gray-200 overflow-x-auto max-h-48 border border-gray-200 dark:border-gray-700" x-text="JSON.stringify(activeLog?.properties, null, 2)"></pre>
                </div>

                <div class="grid grid-cols-2 gap-3 text-xs">
                    <div>
                        <span class="text-gray-500">Subject Type:</span>
                        <p class="font-semibold text-gray-800 dark:text-gray-200 truncate mt-0.5" x-text="activeLog?.subject_type || '-'"></p>
                    </div>
                    <div>
                        <span class="text-gray-500">Subject ID:</span>
                        <p class="font-semibold text-gray-800 dark:text-gray-200 mt-0.5" x-text="activeLog?.subject_id || '-'"></p>
                    </div>
                </div>
            </div>

            <!-- Single Centered Close Button (Rule #59) -->
            <div class="mt-6 flex justify-center">
                <button type="button" @click="detailModalOpen = false" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-bold text-xs rounded-xl transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
