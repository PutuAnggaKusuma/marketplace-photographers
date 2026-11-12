@extends('layouts.photographer')

@section('title', 'Jadwal — Dashboard Fotografer')

@section('content')
<div class="space-y-6 w-full" @if($tab === 'overrides') x-data="dashboardLiveTable()" @endif>

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">Jadwal</h1>
        </div>

        @if($tab === 'overrides')
            <a href="{{ route('photographer.availability.create') }}" class="px-5 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs rounded-xl shadow-xs transition flex items-center gap-2 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path></svg>
                <span>Atur Tanggal Libur</span>
            </a>
        @endif
    </div>

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
            <span class="{{ $tab === 'overrides' ? 'underline underline-offset-8 decoration-2 decoration-amber-500 dark:decoration-amber-400' : '' }}">Tanggal Libur</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ $tab === 'overrides' ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300' }}">
                {{ $totalAllBlocked }}
            </span>
        </a>
    </div>

    @if($tab === 'weekly')
        <!-- TAB 1: FORM PENGATURAN JAM KERJA MINGGUAN (Rule #105 Compliant) -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm p-6 sm:p-8 space-y-6">
            <div class="space-y-1">
                <h2 class="text-base sm:text-lg font-black text-gray-900 dark:text-white">Jam Operasional</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Tentukan hari dan jam buka untuk menerima pemesanan sesi.
                </p>
            </div>

            <form action="{{ route('photographer.availability.update-working-hours') }}" method="POST" novalidate
                  x-data="{
                      validateRow(row) {
                          if (!row.isOpen || row.is24h) return '';
                          if (!row.buka) return 'Jam buka wajib diisi.';
                          if (!row.tutup) return 'Jam tutup wajib diisi.';
                          if (row.buka >= row.tutup) return 'Jam tutup harus lebih akhir daripada jam buka.';
                          return '';
                      },
                      submitWeeklyHours(e) {
                          let firstInvalidEl = null;
                          const rows = this.$el.querySelectorAll('[data-working-day-row]');
                          rows.forEach((el) => {
                              const toggle = el.querySelector('[data-is-open]');
                              const is24El = el.querySelector('[data-is-24h]');
                              const bukaEl = el.querySelector('[data-jam-buka]');
                              const tutupEl = el.querySelector('[data-jam-tutup]');
                              const bukaTrigger = el.querySelector('[data-jam-buka-trigger]');
                              const tutupTrigger = el.querySelector('[data-jam-tutup-trigger]');
                              const isOpen = toggle ? toggle.checked : false;
                              const is24h = is24El ? is24El.checked : false;
                              if (isOpen && !is24h) {
                                  const b = bukaEl ? bukaEl.value : '';
                                  const t = tutupEl ? tutupEl.value : '';
                                  if (!b) firstInvalidEl = firstInvalidEl || bukaTrigger || bukaEl;
                                  else if (!t || b >= t) firstInvalidEl = firstInvalidEl || tutupTrigger || tutupEl;
                              }
                          });
                          window.dispatchEvent(new CustomEvent('validate-working-hours'));
                          if (firstInvalidEl) {
                              e.preventDefault();
                              firstInvalidEl.focus();
                              firstInvalidEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                              return false;
                          }
                      }
                  }"
                  @submit="submitWeeklyHours($event)"
                  class="space-y-6">
                @csrf

                <div class="divide-y divide-gray-100 dark:divide-gray-700/60">
                    @foreach($workingHours as $wh)
                        @php
                            $dow = $wh->day_of_week;
                            $bukaVal = old("days.{$dow}.jam_buka", substr($wh->jam_buka, 0, 5));
                            $tutupVal = old("days.{$dow}.jam_tutup", substr($wh->jam_tutup, 0, 5));
                            $hasOldDays = is_array(old('days')) && count(old('days')) > 0;
                            $isOpenVal = $hasOldDays ? !empty(old("days.{$dow}.is_open")) : (bool) $wh->is_open;
                            $is24hVal = $hasOldDays ? !empty(old("days.{$dow}.is_24_hours")) : (bool) $wh->is_24_hours;
                        @endphp
                        <div data-working-day-row
                             x-data="{
                                 isOpen: {{ $isOpenVal ? 'true' : 'false' }},
                                 is24h: {{ $is24hVal ? 'true' : 'false' }},
                                 buka: @js($bukaVal),
                                 tutup: @js($tutupVal),
                                 touched: false,
                                 get timeError() {
                                     if (!this.isOpen || this.is24h) return '';
                                     if (!this.buka) return 'Jam buka wajib diisi.';
                                     if (!this.tutup) return 'Jam tutup wajib diisi.';
                                     if (this.buka >= this.tutup) return 'Jam tutup harus lebih akhir daripada jam buka.';
                                     return '';
                                 }
                             }"
                             @validate-working-hours.window="touched = true"
                             class="py-4 first:pt-1 last:pb-1 grid grid-cols-1 md:grid-cols-12 md:items-center gap-3 md:gap-4">

                            <input type="hidden" name="days[{{ $dow }}][day_name]" value="{{ $wh->day_name }}">

                            <!-- Nama Hari & Status -->
                            <div class="md:col-span-4 flex items-center gap-4">
                                <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                    <input type="checkbox" data-is-open name="days[{{ $dow }}][is_open]" value="1" x-model="isOpen" class="sr-only peer">
                                    <div class="w-10 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-amber-400"></div>
                                </label>
                                <h4 class="font-bold text-sm text-gray-900 dark:text-white">
                                    <span>{{ $wh->day_name }}</span>
                                    <span :class="isOpen ? 'text-emerald-600 dark:text-emerald-400 font-bold' : 'text-gray-400 dark:text-gray-500 font-medium'" x-text="isOpen ? '(Buka)' : '(Libur)'">{{ $isOpenVal ? '(Buka)' : '(Libur)' }}</span>
                                </h4>
                            </div>

                            <!-- Opsi 24 Jam -->
                            <div class="md:col-span-3" :class="!isOpen ? 'hidden md:block' : ''">
                                <label x-show="isOpen" @if(!$isOpenVal) style="display: none;" @endif class="inline-flex items-center gap-2 cursor-pointer text-xs font-semibold text-gray-700 dark:text-gray-300 select-none">
                                    <input type="checkbox" data-is-24h name="days[{{ $dow }}][is_24_hours]" value="1" x-model="is24h" class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-amber-500 accent-amber-400 focus:ring-amber-400 cursor-pointer">
                                    <span>24 Jam</span>
                                </label>
                            </div>

                            <!-- Rentang Jam Operasional -->
                            <div class="md:col-span-5 md:flex md:flex-col md:items-end">
                                <div x-show="isOpen && !is24h" @if(!($isOpenVal && !$is24hVal)) style="display: none;" @endif class="flex flex-wrap items-center gap-2">
                                    <x-time-dropdown
                                        model="buka"
                                        name="days[{{ $dow }}][jam_buka]"
                                        data-attr="data-jam-buka"
                                        trigger-data-attr="data-jam-buka-trigger"
                                        error="touched && timeError"
                                        on-select="touched = true"
                                        trigger-class="w-[104px] px-3.5 py-2 rounded-xl border bg-white dark:bg-gray-900 text-xs flex items-center justify-between gap-2 shadow-2xs outline-none transition cursor-pointer"
                                    />
                                    <span class="text-xs text-gray-400 font-bold">-</span>
                                    <x-time-dropdown
                                        model="tutup"
                                        name="days[{{ $dow }}][jam_tutup]"
                                        data-attr="data-jam-tutup"
                                        trigger-data-attr="data-jam-tutup-trigger"
                                        error="touched && timeError"
                                        on-select="touched = true"
                                        align="right"
                                        trigger-class="w-[104px] px-3.5 py-2 rounded-xl border bg-white dark:bg-gray-900 text-xs flex items-center justify-between gap-2 shadow-2xs outline-none transition cursor-pointer"
                                    />
                                    <span class="text-[11px] text-gray-400 font-semibold">WIB</span>
                                </div>

                                <p x-show="isOpen && !is24h && touched && timeError" x-text="timeError" style="display: none;" class="text-[11px] font-bold text-red-500 mt-1.5"></p>

                                <span x-show="isOpen && is24h" @if(!($isOpenVal && $is24hVal)) style="display: none;" @endif class="text-xs font-semibold text-gray-500 dark:text-gray-400 tabular-nums">
                                    00:00 - 24:00 WIB
                                </span>

                                <span x-show="!isOpen" @if($isOpenVal) style="display: none;" @endif class="text-xs font-medium text-gray-400 dark:text-gray-500">
                                    -
                                </span>
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
            <div id="dashboard-stats-container">
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
                    <!-- Card 1: Total Tanggal Diblokir -->
                    <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
                        <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Libur Aktif</span>
                        <div class="text-xs font-black text-gray-900 dark:text-white tracking-tight">{{ $totalBlocked }} Periode</div>
                    </div>

                    <!-- Card 2: Jadwal Sesi Terdekat -->
                    <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
                        <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Jadwal Sesi Terdekat</span>
                        <div class="text-xs font-black text-gray-900 dark:text-white tracking-tight">{{ $nextBookingDate }}</div>
                    </div>

                    <!-- Card 3: Total Kontrak Aktif -->
                    <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
                        <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Total Kontrak Aktif</span>
                        <div class="text-xs font-black text-gray-900 dark:text-white tracking-tight">{{ $totalActiveBookings }} Sesi Booking</div>
                    </div>

                    <!-- Card 4: Bulan Operasional -->
                    <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
                        <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Bulan Operasional</span>
                        <div class="text-xs font-black text-gray-900 dark:text-white tracking-tight">{{ date('F Y') }}</div>
                    </div>
                </div>
            </div>

            <!-- Standard Data Table Card (Rule #15, #16, #17, #18, #45, #107 Compliant) -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm overflow-hidden">

                <!-- Search Toolbar & Filters (Alpine.js Custom Floating Dropdown) -->
                <div class="p-4 flex flex-col md:flex-row items-center justify-between gap-4">
                    <!-- Filter Dropdowns di Kiri -->
                    <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                        <!-- 1. Filter Periode Tanggal -->
                        @php
                            $periodeLabels = [
                                'all' => 'Semua Periode',
                                'upcoming' => 'Aktif (Mendatang)',
                                'past' => 'Riwayat (Sudah Lewat)',
                            ];
                            $curPeriodeName = $periodeLabels[$periode ?? 'all'] ?? 'Semua Periode';
                        @endphp
                        <div class="relative w-full sm:w-auto min-w-[175px]" x-data="{ open: false, selectedId: '{{ $periode ?? 'all' }}', selectedName: '{{ $curPeriodeName }}' }">
                            <input type="hidden" name="periode" :value="selectedId">
                            <button @click="open = !open" type="button"
                                    class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs font-bold text-gray-800 dark:text-white flex items-center justify-between gap-2 shadow-2xs hover:border-amber-400 dark:hover:border-amber-500 cursor-pointer transition">
                                <span x-text="selectedName" class="truncate"></span>
                                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition
                                 class="absolute z-50 left-0 mt-2 min-w-[190px] w-full bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700/80 py-1 max-h-60 overflow-y-auto" style="display: none;">
                                @foreach($periodeLabels as $pKey => $pVal)
                                    <button type="button" @click="selectedId = '{{ $pKey }}'; selectedName = '{{ $pVal }}'; open = false; applyFilter('periode', '{{ $pKey }}')"
                                            class="w-full px-3.5 py-2 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition flex items-center justify-between text-left cursor-pointer"
                                            :class="selectedId === '{{ $pKey }}' ? 'bg-amber-50/70 dark:bg-amber-950/30 text-amber-600 font-extrabold' : ''">
                                        <span>{{ $pVal }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- 2. Filter Tipe Penutupan -->
                        @php
                            $tipeLabels = [
                                'all' => 'Semua Tipe Libur',
                                'all_day' => 'Libur Seharian',
                                'time_slot' => 'Tutup Jam Tertentu',
                            ];
                            $curTipeName = $tipeLabels[$tipe ?? 'all'] ?? 'Semua Tipe Libur';
                        @endphp
                        <div class="relative w-full sm:w-auto min-w-[175px]" x-data="{ open: false, selectedId: '{{ $tipe ?? 'all' }}', selectedName: '{{ $curTipeName }}' }">
                            <input type="hidden" name="tipe" :value="selectedId">
                            <button @click="open = !open" type="button"
                                    class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs font-bold text-gray-800 dark:text-white flex items-center justify-between gap-2 shadow-2xs hover:border-amber-400 dark:hover:border-amber-500 cursor-pointer transition">
                                <span x-text="selectedName" class="truncate"></span>
                                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition
                                 class="absolute z-50 left-0 mt-2 min-w-[190px] w-full bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700/80 py-1 max-h-60 overflow-y-auto" style="display: none;">
                                @foreach($tipeLabels as $tKey => $tVal)
                                    <button type="button" @click="selectedId = '{{ $tKey }}'; selectedName = '{{ $tVal }}'; open = false; applyFilter('tipe', '{{ $tKey }}')"
                                            class="w-full px-3.5 py-2 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition flex items-center justify-between text-left cursor-pointer"
                                            :class="selectedId === '{{ $tKey }}' ? 'bg-amber-50/70 dark:bg-amber-950/30 text-amber-600 font-extrabold' : ''">
                                        <span>{{ $tVal }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- 3. Filter Urutan -->
                        @php
                            $sortLabels = [
                                'date_asc' => 'Tanggal Terdekat',
                                'date_desc' => 'Tanggal Terjauh',
                                'latest' => 'Terbaru Ditambahkan',
                                'oldest' => 'Terlama Ditambahkan',
                            ];
                            $curSortName = $sortLabels[$sort ?? 'date_asc'] ?? 'Tanggal Terdekat';
                        @endphp
                        <div class="relative w-full sm:w-auto min-w-[180px]" x-data="{ open: false, selectedId: '{{ $sort ?? 'date_asc' }}', selectedName: '{{ $curSortName }}' }">
                            <input type="hidden" name="sort" :value="selectedId">
                            <button @click="open = !open" type="button"
                                    class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs font-bold text-gray-800 dark:text-white flex items-center justify-between gap-2 shadow-2xs hover:border-amber-400 dark:hover:border-amber-500 cursor-pointer transition">
                                <span x-text="selectedName" class="truncate"></span>
                                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition
                                 class="absolute z-50 left-0 mt-2 min-w-[190px] w-full bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700/80 py-1 max-h-60 overflow-y-auto" style="display: none;">
                                @foreach($sortLabels as $srtKey => $srtVal)
                                    <button type="button" @click="selectedId = '{{ $srtKey }}'; selectedName = '{{ $srtVal }}'; open = false; applyFilter('sort', '{{ $srtKey }}')"
                                            class="w-full px-3.5 py-2 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition flex items-center justify-between text-left cursor-pointer"
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
                               placeholder="Cari tanggal atau keterangan libur..."
                               class="w-full pl-4 pr-10 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">
                        <svg class="w-4 h-4 text-gray-400 absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </div>

                <div id="dashboard-table-container" class="transition-opacity duration-200" :class="isLoading ? 'opacity-40 pointer-events-none' : 'opacity-100'">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-[11px] font-extrabold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    <th class="py-3.5 px-4 sm:px-6 whitespace-nowrap">Tanggal Libur</th>
                                    <th class="py-3.5 px-4 sm:px-6 whitespace-nowrap">Keterangan</th>
                                    <th class="py-3.5 px-4 sm:px-6 text-center whitespace-nowrap">Status</th>
                                    <th class="py-3.5 px-4 sm:px-6 text-center whitespace-nowrap">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60 text-xs">
                                @php $todayDate = date('Y-m-d'); @endphp
                                @forelse($availabilities as $av)
                                    @php $isPast = $av->tanggal && $av->tanggal < $todayDate; @endphp
                                    <tr class="hover:bg-amber-50/30 dark:hover:bg-amber-950/20 transition">
                                        <td class="py-4 px-4 sm:px-6 align-top">
                                            <div class="font-extrabold text-gray-900 dark:text-white">
                                                {{ date('d M Y', strtotime($av->tanggal)) }}
                                            </div>
                                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 mt-0.5">
                                                {{ ($av->jam_mulai && $av->jam_selesai && ($av->jam_mulai != '00:00:00' || $av->jam_selesai != '23:59:59')) ? substr($av->jam_mulai, 0, 5) . ' - ' . substr($av->jam_selesai, 0, 5) . ' WIB' : 'Libur Seharian Penuh' }}
                                            </div>
                                        </td>
                                        <td class="py-4 px-4 sm:px-6 align-top text-gray-600 dark:text-gray-400">
                                            {{ $av->keterangan ?: 'Ditutup manual oleh fotografer' }}
                                        </td>
                                        <!-- Status Teks Murni (Rule #67 Compliant) -->
                                        <td class="py-4 px-4 sm:px-6 align-top text-center whitespace-nowrap">
                                            @if($isPast)
                                                <span class="text-xs font-bold text-gray-400 dark:text-gray-500">
                                                    Sudah Lewat
                                                </span>
                                            @else
                                                <span class="text-xs font-bold text-rose-600 dark:text-rose-400">
                                                    Ditutup
                                                </span>
                                            @endif
                                        </td>
                                        <!-- Aksi Tombol (Rule #80 Compliant) -->
                                        <td class="py-4 px-4 sm:px-6 align-top text-center whitespace-nowrap">
                                            @if($isPast)
                                                <button type="button"
                                                        @click="$dispatch('open-confirm-modal', {
                                                            title: 'Hapus Riwayat Libur?',
                                                            message: 'Apakah Anda yakin ingin menghapus riwayat tanggal libur {{ date('d M Y', strtotime($av->tanggal)) }}?',
                                                            buttonText: 'Ya, Hapus Data',
                                                            iconType: 'delete',
                                                            buttonClass: 'bg-red-600 hover:bg-red-700 text-white',
                                                            actionUrl: '{{ route('photographer.availability.delete', $av->id) }}',
                                                            method: 'POST'
                                                        })"
                                                        class="px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200/80 dark:border-rose-900/60 font-bold text-xs transition min-w-[60px] text-center cursor-pointer">
                                                    Hapus
                                                </button>
                                            @else
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
                                            @endif
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

                    @if($availabilities->total() > 0)
                        <div class="p-4 bg-gray-50/50 dark:bg-gray-900/50">
                            {{ $availabilities->links('partials.dashboard.pagination') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

</div>
@endsection
