@extends('layouts.photographer')

@section('title', 'Jadwal & Availability — Dashboard Fotografer')

@section('content')
<div class="space-y-6 w-full">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">Jadwal & Ketersediaan</h1>
        </div>

        @if($tab === 'overrides')
            <a href="{{ route('photographer.availability.create') }}" class="px-5 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs rounded-xl shadow-xs transition flex items-center gap-2 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path></svg>
                <span>+ Atur Tanggal Libur</span>
            </a>
        @endif
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 text-xs text-emerald-800 dark:text-emerald-300 font-bold flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 text-xs text-rose-800 dark:text-rose-300 font-bold flex items-center gap-2">
            <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Main Navigation Tabs (Rule #78 Compliant: Active Text Underline Standard & Counter Pill Badge) -->
    <div class="flex items-center gap-6 overflow-x-auto pb-1">
        <a href="{{ route('photographer.availability', ['tab' => 'weekly']) }}" 
           class="py-2 text-xs sm:text-sm transition flex items-center gap-2 cursor-pointer shrink-0 {{ $tab === 'weekly' ? 'text-amber-600 dark:text-amber-400 font-black' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-bold' }}">
            <span class="{{ $tab === 'weekly' ? 'underline underline-offset-8 decoration-2 decoration-amber-500 dark:decoration-amber-400' : '' }}">Jam Kerja Mingguan</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ $tab === 'weekly' ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300' }}">
                7 Hari
            </span>
        </a>

        <a href="{{ route('photographer.availability', ['tab' => 'overrides']) }}" 
           class="py-2 text-xs sm:text-sm transition flex items-center gap-2 cursor-pointer shrink-0 {{ $tab === 'overrides' ? 'text-amber-600 dark:text-amber-400 font-black' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-bold' }}">
            <span class="{{ $tab === 'overrides' ? 'underline underline-offset-8 decoration-2 decoration-amber-500 dark:decoration-amber-400' : '' }}">Pengecualian & Libur Khusus</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ $tab === 'overrides' ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300' }}">
                {{ $totalBlocked }}
            </span>
        </a>
    </div>

    @if($tab === 'weekly')
        <!-- TAB 1: FORM PENGATURAN JAM KERJA MINGGUAN -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm p-6 sm:p-8 space-y-6">
            <div class="space-y-1 border-b border-gray-100 dark:border-gray-700 pb-4">
                <h2 class="text-base sm:text-lg font-black text-gray-900 dark:text-white">Pola Jam Kerja Operasional Mingguan</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Atur hari dan jam operasional rutin Anda. Calon klien hanya dapat memilih slot jam sesi foto dalam batas waktu operasional yang Anda buka.
                </p>
            </div>

            <form action="{{ route('photographer.availability.update-working-hours') }}" method="POST" class="space-y-6">
                @csrf

                <div class="space-y-4">
                    @foreach($workingHours as $wh)
                        @php
                            $dow = $wh->day_of_week;
                            $bukaVal = substr($wh->jam_buka, 0, 5);
                            $tutupVal = substr($wh->jam_tutup, 0, 5);
                        @endphp
                        <div x-data="{ isOpen: {{ $wh->is_open ? 'true' : 'false' }}, is24h: {{ $wh->is_24_hours ? 'true' : 'false' }} }" 
                             class="p-4 rounded-xl border border-gray-100 dark:border-gray-700/60 bg-gray-50/50 dark:bg-gray-900/40 flex flex-col md:flex-row md:items-center justify-between gap-4 transition hover:border-amber-400/50">
                            
                            <input type="hidden" name="days[{{ $dow }}][day_name]" value="{{ $wh->day_name }}">

                            <!-- Nama Hari & Toggle Status Operasional -->
                            <div class="flex items-center gap-4 min-w-[180px]">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="days[{{ $dow }}][is_open]" value="1" x-model="isOpen" class="sr-only peer">
                                    <div class="w-10 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-amber-400"></div>
                                </label>
                                <div>
                                    <h4 class="font-black text-sm text-gray-900 dark:text-white">{{ $wh->day_name }}</h4>
                                    <span class="text-[11px] font-bold" :class="isOpen ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-400 dark:text-gray-500'" x-text="isOpen ? 'Buka Operasional' : 'Libur Rutin'"></span>
                                </div>
                            </div>

                            <!-- Opsi 24 Jam Fleksibel vs Jam Kustom -->
                            <div class="flex items-center gap-6" x-show="isOpen" x-transition>
                                <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-gray-700 dark:text-gray-300">
                                    <input type="checkbox" name="days[{{ $dow }}][is_24_hours]" value="1" x-model="is24h" class="rounded border-gray-300 text-amber-500 focus:ring-amber-400 cursor-pointer">
                                    <span>24 Jam / Fleksibel Kapan Saja</span>
                                </label>

                                <!-- Time Slot Pickers (Jam Mulai - Jam Selesai) -->
                                <div class="flex items-center gap-2" x-show="!is24h" x-transition>
                                    <input type="time" name="days[{{ $dow }}][jam_buka]" value="{{ $bukaVal }}" class="px-3 py-1.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-xs font-bold text-gray-800 dark:text-white focus:ring-2 focus:ring-amber-400 focus:outline-none">
                                    <span class="text-xs text-gray-400 font-bold">s/d</span>
                                    <input type="time" name="days[{{ $dow }}][jam_tutup]" value="{{ $tutupVal }}" class="px-3 py-1.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-xs font-bold text-gray-800 dark:text-white focus:ring-2 focus:ring-amber-400 focus:outline-none">
                                    <span class="text-[11px] text-gray-400 font-semibold">WIB</span>
                                </div>
                            </div>

                            <!-- Teks Libur jika ditutup -->
                            <div x-show="!isOpen" x-transition class="text-xs text-gray-400 italic">
                                Hari tidak aktif menerima reservasi sesi foto baru.
                            </div>

                        </div>
                    @endforeach
                </div>

                <!-- Tombol Aksi Baku (Rule #74 & Rule #76 Baku: Perbaharui Data) -->
                <div class="flex items-center justify-end gap-3 pt-4">
                    <a href="{{ route('photographer.availability', ['tab' => 'weekly']) }}" class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 text-gray-700 dark:text-gray-300 font-bold text-xs transition cursor-pointer">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs rounded-xl shadow-xs transition cursor-pointer">
                        Perbaharui Data
                    </button>
                </div>
            </form>
        </div>

    @else
        <!-- TAB 2: PENGECEUALIAN & LIBUR KHUSUS (DATE OVERRIDES) -->
        <div class="space-y-6">
            <!-- Metric Stat Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
                <!-- Card 1: Total Tanggal Diblokir -->
                <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
                    <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Total Penutupan Jadwal</span>
                    <div class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ $totalBlocked }} Periode</div>
                </div>

                <!-- Card 2: Jadwal Sesi Terdekat -->
                <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
                    <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Jadwal Sesi Terdekat</span>
                    <div class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ $nextBookingDate }}</div>
                </div>

                <!-- Card 3: Total Kontrak Aktif -->
                <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
                    <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Total Kontrak Aktif</span>
                    <div class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ $totalActiveBookings }} Sesi Booking</div>
                </div>

                <!-- Card 4: Bulan Operasional -->
                <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
                    <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Bulan Operasional</span>
                    <div class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ date('F Y') }}</div>
                </div>
            </div>

            <!-- Standard Data Table Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm overflow-hidden space-y-4">
                <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-sm font-extrabold text-gray-900 dark:text-white">Daftar Jadwal Ditutup / Libur Manual</h3>
                    <span class="text-xs text-gray-400 font-medium">{{ $availabilities->total() }} Data Terdaftar</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-[11px] font-extrabold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                <th class="py-3.5 px-4 sm:px-6 w-4/12">Tanggal & Waktu Ditutup</th>
                                <th class="py-3.5 px-4 sm:px-6 w-5/12">Alasan / Keterangan</th>
                                <th class="py-3.5 px-4 sm:px-6 w-1/12 text-center">Status</th>
                                <th class="py-3.5 px-4 sm:px-6 w-2/12 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60 text-xs">
                            @forelse($availabilities as $av)
                                <tr class="hover:bg-amber-50/30 dark:hover:bg-amber-950/20 transition">
                                    <td class="py-4 px-4 sm:px-6 align-top">
                                        <div class="font-extrabold text-gray-900 dark:text-white">
                                            {{ date('d M Y', strtotime($av->tanggal)) }}
                                        </div>
                                        <div class="text-[11px] font-medium text-gray-500 dark:text-gray-400 mt-0.5">
                                            {{ ($av->jam_mulai && $av->jam_selesai && ($av->jam_mulai != '00:00:00' || $av->jam_selesai != '23:59:59')) ? substr($av->jam_mulai, 0, 5) . ' - ' . substr($av->jam_selesai, 0, 5) . ' WIB' : 'Libur Seharian Penuh' }}
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 sm:px-6 align-top text-gray-600 dark:text-gray-400">
                                        {{ $av->keterangan ?: 'Ditutup manual oleh fotografer' }}
                                    </td>
                                    <!-- Status Teks Murni (Rule #67 Compliant) -->
                                    <td class="py-4 px-4 sm:px-6 align-top text-center whitespace-nowrap">
                                        <span class="text-xs font-bold text-rose-600 dark:text-rose-400">
                                            Ditutup
                                        </span>
                                    </td>
                                    <!-- Aksi Tombol (Rule #80 Compliant) -->
                                    <td class="py-4 px-4 sm:px-6 align-top text-center whitespace-nowrap">
                                        <button type="button" 
                                                @click="$dispatch('open-confirm-modal', {
                                                    title: 'Buka Kembali Jadwal?',
                                                    message: 'Apakah Anda yakin ingin membuka kembali jadwal tanggal {{ date('d M Y', strtotime($av->tanggal)) }}?',
                                                    buttonText: 'Ya, Buka Jadwal',
                                                    iconType: 'confirm',
                                                    buttonClass: 'bg-emerald-600 hover:bg-emerald-700 text-white',
                                                    actionUrl: '{{ route('photographer.availability.delete', $av->id) }}',
                                                    method: 'POST'
                                                })"
                                                class="px-3 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200/80 font-bold text-xs transition min-w-[60px] text-center cursor-pointer">
                                            Buka Jadwal
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-12 text-center text-gray-400 font-bold text-xs">
                                        Belum ada tanggal libur atau penutupan jam khusus yang tercatat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($availabilities->hasPages())
                    <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                        {{ $availabilities->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif

</div>
@endsection
