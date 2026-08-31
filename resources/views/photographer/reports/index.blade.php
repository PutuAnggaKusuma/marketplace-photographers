@extends('layouts.photographer')

@section('title', 'Laporan Keuangan Fotografer - LensMatch')

@section('content')
<div x-data="dashboardLiveTable()" class="space-y-6">

    <!-- Page Title & Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <span class="text-[11px] font-extrabold uppercase tracking-widest text-amber-600 dark:text-amber-400">Pusat Informasi Keuangan</span>
            <h1 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">Laporan Keuangan & Statistik Fotografer</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Pantau akumulasi omzet bersih, proyeksi pencairan escrow, dan unduh laporan resmi keuangan Anda</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('photographer.reports.export', ['year' => $selectedYear]) }}" 
               target="_blank"
               class="px-5 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs rounded-xl shadow-xs transition inline-flex items-center justify-center shrink-0">
                Cetak Laporan PDF
            </a>
        </div>
    </div>

    <!-- 4 Summary Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Net Income -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm relative overflow-hidden">
            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Omzet Bersih Fotografer</span>
            <div class="mt-3">
                <h2 class="text-xl font-black text-gray-900 dark:text-white">Rp {{ number_format($totalNetIncome, 0, ',', '.') }}</h2>
                <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium mt-1"><span class="font-bold text-gray-700 dark:text-gray-300">90%</span> Payout Diterima Bersih</p>
            </div>
        </div>

        <!-- Card 2: Escrow Pending -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm relative overflow-hidden">
            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Escrow Pending</span>
            <div class="mt-3">
                <h2 class="text-xl font-black text-gray-900 dark:text-white">Rp {{ number_format($totalEscrowPending, 0, ',', '.') }}</h2>
                <p class="text-[11px] text-gray-500 dark:text-gray-400 font-medium mt-1">Proyeksi Pencairan Sesi Foto</p>
            </div>
        </div>

        <!-- Card 3: Completed Count -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm relative overflow-hidden">
            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sesi Foto Selesai</span>
            <div class="mt-3">
                <h2 class="text-xl font-black text-gray-900 dark:text-white">{{ $completedCount }} Sesi</h2>
                <p class="text-[11px] text-gray-500 dark:text-gray-400 font-medium mt-1">Terverifikasi Selesai</p>
            </div>
        </div>

        <!-- Card 4: Avg Revenue per Session -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm relative overflow-hidden">
            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rata-Rata / Sesi</span>
            <div class="mt-3">
                <h2 class="text-xl font-black text-gray-900 dark:text-white">Rp {{ number_format($avgRevenuePerSession, 0, ',', '.') }}</h2>
                <p class="text-[11px] text-gray-500 dark:text-gray-400 font-medium mt-1">
                    Komisi Platform (<span class="font-bold text-gray-700 dark:text-gray-300">10%</span>): <span class="font-bold text-gray-700 dark:text-gray-300">Rp {{ number_format($totalMarketplaceFee, 0, ',', '.') }}</span>
                </p>
            </div>
        </div>
    </div>

    </div>

    <!-- Monthly Revenue Visual Bar Chart Card -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-gray-100 dark:border-gray-700 pb-4">
            <div>
                <h3 class="text-base font-extrabold text-gray-900 dark:text-white">Grafik Tren Pendapatan Bulanan (Tahun {{ $selectedYear }})</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">Visualisasi akumulasi omzet bersih per bulan</p>
            </div>
            <form method="GET" action="{{ route('photographer.reports') }}" class="relative" x-data="{ open: false, selectedYear: '{{ $selectedYear }}' }">
                <input type="hidden" name="status" value="{{ $statusFilter }}">
                <input type="hidden" name="year" :value="selectedYear">
                <button @click="open = !open" type="button" class="px-3.5 py-1.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs font-bold text-gray-900 dark:text-white flex items-center gap-2 shadow-2xs cursor-pointer">
                    <span x-text="'Tahun ' + selectedYear"></span>
                    <svg class="w-3.5 h-3.5 text-gray-400 transition-transform duration-200" :class="open ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-32 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700/80 py-1 z-30 overflow-hidden" style="display: none;">
                    @foreach([2026, 2025, 2024] as $yr)
                        <button type="button" @click="selectedYear = '{{ $yr }}'; open = false; $nextTick(() => $el.closest('form').submit())" class="w-full px-3 py-2 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition flex items-center justify-between text-left cursor-pointer" :class="selectedYear == '{{ $yr }}' ? 'bg-amber-50/70 text-amber-600' : ''">
                            <span>Tahun {{ $yr }}</span>
                            
                        </button>
                    @endforeach
                </div>
            </form>
        </div>

        <!-- Bar Chart Container -->
        @php
            $months = $months ?? ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        @endphp
        <div class="h-64 sm:h-72 w-full flex flex-col justify-between pt-2">
            <div class="h-full w-full relative flex items-end justify-between gap-2 sm:gap-4 px-1 pb-6">
                
                <!-- Background Horizontal Scale Lines -->
                <div class="absolute inset-0 flex flex-col justify-between pointer-events-none pb-6 opacity-30 dark:opacity-20">
                    <div class="border-b border-dashed border-gray-300 dark:border-gray-600 w-full"></div>
                    <div class="border-b border-dashed border-gray-300 dark:border-gray-600 w-full"></div>
                    <div class="border-b border-dashed border-gray-300 dark:border-gray-600 w-full"></div>
                    <div class="border-b border-dashed border-gray-300 dark:border-gray-600 w-full"></div>
                </div>

                <!-- 12 Monthly Bar Columns -->
                @foreach(range(1, 12) as $mNum)
                    @php
                        $rev = $monthlyRevenue[$mNum] ?? 0;
                        $bkCount = $monthlyBookings[$mNum] ?? 0;
                        $barPercent = $maxMonthlyRevenue > 0 && $rev > 0 ? round(($rev / $maxMonthlyRevenue) * 85) + 15 : 6;
                    @endphp
                    <div class="flex-1 flex flex-col items-center h-full justify-end group relative z-10">
                        <!-- Tooltip Hover -->
                        <div class="opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none absolute -top-12 bg-gray-900 text-white text-[10px] font-extrabold px-2.5 py-1.5 rounded-lg shadow-xl whitespace-nowrap z-30">
                            <span>Rp {{ number_format($rev, 0, ',', '.') }}</span>
                            <span class="block text-amber-300 font-semibold text-[9px]">{{ $bkCount }} Sesi Pemotretan</span>
                        </div>

                        <!-- Column Bar -->
                        <div class="w-full max-w-[28px] bg-gray-100 dark:bg-gray-700/60 rounded-t-lg relative flex flex-col justify-end overflow-hidden" style="height: {{ $barPercent }}%;">
                            <div class="w-full {{ $rev > 0 ? 'bg-amber-400 group-hover:bg-amber-500 transition-colors' : 'bg-gray-200/70 dark:bg-gray-700/50' }} rounded-t-lg" style="height: 100%;"></div>
                        </div>

                        <!-- Month Label -->
                        <span class="text-[10px] sm:text-xs font-bold text-gray-500 dark:text-gray-400 mt-2 absolute -bottom-5">
                            {{ $months[$mNum - 1] }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Transaction List Table Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm overflow-hidden space-y-4">
        
        <!-- Table Header & Status Filter Tabs -->
        <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex flex-col md:flex-row items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-extrabold text-gray-900 dark:text-white">Rincian Transaksi & Pencairan Payout</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">Daftar lengkap pendapatan per pesanan sesi foto</p>
            </div>

            <div class="flex items-center gap-2">
                @php
                    $tTabs = [
                        'all' => 'Semua Transaksi',
                        'paid' => 'Pencairan Selesai',
                        'holding' => 'Escrow Pending'
                    ];
                @endphp
                @foreach($tTabs as $k => $l)
                    <a href="{{ route('photographer.reports', ['status' => $k, 'year' => $selectedYear]) }}" 
                       class="px-3 py-1.5 rounded-xl text-xs font-extrabold transition {{ $statusFilter === $k ? 'bg-amber-500 text-white shadow-xs' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                        {{ $l }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-[11px] font-extrabold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        <th class="py-3.5 px-4 sm:px-6">Kode Invoice</th>
                        <th class="py-3.5 px-4 sm:px-6">Klien & Layanan</th>
                        <th class="py-3.5 px-4 sm:px-6">Tanggal Sesi</th>
                        <th class="py-3.5 px-4 sm:px-6">Total Tarif</th>
                        <th class="py-3.5 px-4 sm:px-6">Fee Platform (10%)</th>
                        <th class="py-3.5 px-4 sm:px-6">Net Payout Fotografer (90%)</th>
                        <th class="py-3.5 px-4 sm:px-6">Status Pencairan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60 text-xs">
                    @forelse($contracts as $c)
                        @php
                            $payment = $c->payments->first();
                            $invCode = $payment ? $payment->external_id : 'INV-LM-' . str_pad($c->id, 4, '0', STR_PAD_LEFT);
                            $feeAmount = $c->fee_marketplace > 0 ? $c->fee_marketplace : ($c->jumlah * 0.10);
                            $netPayout = $c->payout_amount > 0 ? $c->payout_amount : ($c->jumlah - $feeAmount);
                        @endphp
                        <tr class="hover:bg-amber-50/30 dark:hover:bg-amber-950/20 transition">
                            <!-- Invoice Code -->
                            <td class="py-4 px-4 sm:px-6">
                                <span class="font-extrabold text-gray-900 dark:text-white font-mono">{{ $invCode }}</span>
                                <div class="text-[10px] text-gray-400 mt-0.5">{{ $c->created_at->format('d M Y, H:i') }}</div>
                            </td>

                            <!-- Klien & Layanan -->
                            <td class="py-4 px-4 sm:px-6">
                                <div class="font-bold text-gray-900 dark:text-white">{{ $c->client->nama ?? 'Klien' }}</div>
                                <div class="text-[11px] text-amber-600 dark:text-amber-400 font-medium">
                                    {{ $c->bookingDetail->service->nama_layanan ?? 'Paket Foto Custom' }}
                                </div>
                            </td>

                            <!-- Tanggal Sesi -->
                            <td class="py-4 px-4 sm:px-6 font-semibold text-gray-700 dark:text-gray-300">
                                {{ \Carbon\Carbon::parse($c->bookingDetail->booking_date ?? $c->created_at)->format('d M Y') }}
                            </td>

                            <!-- Total Tarif -->
                            <td class="py-4 px-4 sm:px-6 font-bold text-gray-900 dark:text-white">
                                Rp {{ number_format($c->jumlah, 0, ',', '.') }}
                            </td>

                            <!-- Fee Platform -->
                            <td class="py-4 px-4 sm:px-6 text-amber-600 dark:text-amber-400 font-bold">
                                Rp {{ number_format($feeAmount, 0, ',', '.') }}
                            </td>

                            <!-- Net Payout -->
                            <td class="py-4 px-4 sm:px-6 font-black text-emerald-600 dark:text-emerald-400 text-sm">
                                Rp {{ number_format($netPayout, 0, ',', '.') }}
                            </td>

                            <!-- Status Pencairan -->
                            <td class="py-4 px-4 sm:px-6 align-middle whitespace-nowrap">
                                @if($c->status_payout === 'paid')
                                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">
                                        Payout Dicairkan
                                    </span>
                                    @if($c->payout_at)
                                        <p class="text-[10px] text-gray-400 mt-0.5">{{ $c->payout_at->format('d M Y, H:i') }}</p>
                                    @endif
                                @elseif($c->status_payout === 'ready')
                                    <span class="text-xs font-bold text-blue-600 dark:text-blue-400">
                                        Siap Dicairkan
                                    </span>
                                @elseif($c->status_payout === 'refunded')
                                    <span class="text-xs font-bold text-rose-600 dark:text-rose-400">
                                        Refund
                                    </span>
                                @else
                                    <span class="text-xs font-bold text-amber-600 dark:text-amber-400">
                                        Holding Escrow
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-gray-400 space-y-2">
                                <svg class="w-10 h-10 mx-auto text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <p class="text-xs font-bold">Belum ada rincian data transaksi untuk periode ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($contracts->hasPages())
            <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50">
                {{ $contracts->links('partials.dashboard.pagination') }}
            </div>
        @endif
        </div>
    </div>
</div>
@endsection