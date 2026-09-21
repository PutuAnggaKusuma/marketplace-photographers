@extends('layouts.photographer')

@section('title', 'Jadwal & Availability — Dashboard Fotografer')

@section('content')
<div class="space-y-6 w-full">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <span class="text-[11px] font-extrabold uppercase tracking-widest text-amber-600 dark:text-amber-400">Kalender & Waktu Sesi</span>
            <h1 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">Jadwal & Ketersediaan Fotografer</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Kelola hari libur, blokir tanggal sesi padat, dan pantau booking aktif pada kalender</p>
        </div>
        <a href="{{ route('photographer.availability.create') }}" class="px-5 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs rounded-xl shadow-xs transition flex items-center gap-2 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path></svg>
            <span>+ Blokir Tanggal / Libur</span>
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
        <!-- Card 1: Total Tanggal Diblokir -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
            <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Total Tanggal Libur</span>
            <div class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ $availabilities->count() }} Periode</div>
            <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">Rentang hari tidak menerima booking</p>
        </div>

        <!-- Card 2: Jadwal Sesi Terdekat -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
            <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Jadwal Sesi Terdekat</span>
            <div class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ $nextBookingDate ?? 'Belum Ada' }}</div>
            <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">Tanggal sesi pemotretan aktif</p>
        </div>

        <!-- Card 3: Total Hari Diblokir -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
            <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Status Reservasi</span>
            <div class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">Tersedia Publik</div>
            <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">Kalender aktif menerima order klien</p>
        </div>

        <!-- Card 4: Bulan Operasional -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
            <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Bulan Operasional</span>
            <div class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ date('F Y') }}</div>
            <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">Periode aktif kalender fotografer</p>
        </div>
    </div>

    <!-- Standard Data Table Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm overflow-hidden space-y-4">
        <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-sm font-extrabold text-gray-900 dark:text-white">Daftar Tanggal Libur / Diblokir Manual</h3>
            <span class="text-xs text-gray-400 font-medium">{{ $availabilities->count() }} Data Libur Terdaftar</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-[11px] font-extrabold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        <th class="py-3.5 px-4 sm:px-6 w-3/12">Rentang Tanggal Libur</th>
                        <th class="py-3.5 px-4 sm:px-6 w-5/12">Alasan / Keterangan</th>
                        <th class="py-3.5 px-4 sm:px-6 w-2/12 text-center">Status</th>
                        <th class="py-3.5 px-4 sm:px-6 w-2/12 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60 text-xs">
                    @forelse($availabilities as $av)
                        <tr class="hover:bg-amber-50/30 dark:hover:bg-amber-950/20 transition">
                            <td class="py-4 px-4 sm:px-6 align-top font-bold text-gray-900 dark:text-white">
                                {{ date('d M Y', strtotime($av->tanggal_mulai)) }} s/d {{ date('d M Y', strtotime($av->tanggal_selesai)) }}
                            </td>
                            <td class="py-4 px-4 sm:px-6 align-top text-gray-600 dark:text-gray-400">
                                {{ $av->alasan ?: 'Libur / Tidak Tersedia' }}
                            </td>
                            <td class="py-4 px-4 sm:px-6 align-top text-center whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 border border-rose-200/80 dark:border-rose-900/60">
                                    Diblokir
                                </span>
                            </td>
                            <td class="py-4 px-4 sm:px-6 align-top text-center whitespace-nowrap">
                                <button type="button" 
                                        @click="$dispatch('open-confirm-modal', {
                                            title: 'Buka Blokir Tanggal?',
                                            message: 'Apakah Anda yakin ingin membuka kembali jadwal tanggal {{ date('d M Y', strtotime($av->tanggal_mulai)) }}?',
                                            buttonText: 'Ya, Buka Jadwal',
                                            iconType: 'delete',
                                            buttonClass: 'bg-red-600 hover:bg-red-700 text-white',
                                            actionUrl: '{{ route('photographer.availability.delete', $av->id) }}',
                                            method: 'POST'
                                        })"
                                        class="px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200/80 dark:border-rose-900/60 font-bold text-xs transition min-w-[60px] text-center">
                                    Buka Jadwal
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center text-gray-400 font-bold text-xs">
                                Belum ada tanggal libur yang diblokir.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection