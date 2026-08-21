@extends('layouts.photographer')

@section('title', 'Laporan Keuangan Studio - LensMatch')

@section('content')
<div class="space-y-6">

    <!-- Page Title & Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <span class="text-[11px] font-extrabold uppercase tracking-widest text-amber-600 dark:text-amber-400">Pusat Informasi Keuangan</span>
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white tracking-tight">Laporan Keuangan & Statistik Studio</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Pantau akumulasi omzet bersih, proyeksi pencairan escrow, dan unduh laporan resmi studio Anda</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('photographer.reports.export', ['year' => $selectedYear]) }}" 
               target="_blank"
               class="px-4 py-2.5 rounded-xl bg-gray-900 hover:bg-black dark:bg-amber-400 dark:hover:bg-amber-500 text-white dark:text-gray-900 font-extrabold text-xs shadow-sm transition flex items-center gap-2 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Cetak Laporan PDF / Print</span>
            </a>
        </div>
    </div>

    <!-- 4 Summary Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Net Income -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Omzet Bersih Studio</span>
                <span class="p-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </span>
            </div>
            <div class="mt-3">
                <h2 class="text-xl font-black text-gray-900 dark:text-white">Rp {{ number_format($totalNetIncome, 0, ',', '.') }}</h2>
                <p class="text-[11px] text-emerald-600 dark:text-emerald-400 font-semibold mt-1">90% Payout Diterima Bersih</p>
            </div>
        </div>

        <!-- Card 2: Escrow Pending -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Escrow Pending</span>
                <span class="p-2 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </span>
            </div>
            <div class="mt-3">
                <h2 class="text-xl font-black text-amber-600 dark:text-amber-400">Rp {{ number_format($totalEscrowPending, 0, ',', '.') }}</h2>
                <p class="text-[11px] text-gray-500 dark:text-gray-400 font-medium mt-1">Proyeksi Pencairan Sesi Foto</p>
            </div>
        </div>

        <!-- Card 3: Completed Count -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sesi Foto Selesai</span>
                <span class="p-2 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                </span>
            </div>
            <div class="mt-3">
                <h2 class="text-xl font-black text-blue-600 dark:text-blue-400">{{ $completedCount }} Sesi</h2>
                <p class="text-[11px] text-gray-500 dark:text-gray-400 font-medium mt-1">Terverifikasi Selesai</p>
            </div>
        </div>

        <!-- Card 4: Avg Revenue per Session -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rata-Rata / Sesi</span>
                <span class="p-2 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </span>
            </div>
            <div class="mt-3">
                <h2 class="text-xl font-black text-gray-900 dark:text-white">Rp {{ number_format($avgRevenuePerSession, 0, ',', '.') }}</h2>
                <p class="text-[11px] text-gray-500 dark:text-gray-400 font-medium mt-1">
                    Komisi Platform (10%): <strong class="text-amber-600 dark:text-amber-400">Rp {{ number_format($totalMarketplaceFee, 0, ',', '.') }}</strong>
                </p>
            </div>
        </div>
    </div>

    <!-- Monthly Revenue Visual Bar Chart Card -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-gray-100 dark:border-gray-700 pb-4">
            <div>
                <h3 class="text-base font-extrabold text-gray-900 dark:text-white">Grafik Tren Pendapatan Bulanan (Tahun {{ $selectedYear }})</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">Visualisasi akumulasi omzet bersih studio per bulan</p>
            </div>
            <form method="GET" action="{{ route('photographer.reports') }}" class="flex items-center gap-2">
                <input type="hidden" name="status" value="{{ $statusFilter }}">
                <select name="year" onchange="this.form.submit()" class="px-3 py-1.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white font-bold outline-none">
                    @foreach([2026, 2025, 2024] as $yr)
                        <option value="{{ $yr }}" {{ $selectedYear == $yr ? 'selected' : '' }}>Tahun {{ $yr }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <!-- SVG Bar Chart -->
        <div class="pt-4 pb-2">
            <div class="grid grid-cols-12 gap-2 sm:gap-4 items-end h-48 sm:h-56 px-2 border-b border-gray-200 dark:border-gray-700 pb-2">
                @foreach(range(1, 12) as $mNum)
                    @php
                        $rev = $monthlyRevenue[$mNum];
                        $bkCount = $monthlyBookings[$mNum];
                        $barHeight = $maxMonthlyRevenue > 0 ? round(($rev / $maxMonthlyRevenue) * 100) : 0;
                        $minHeight = $rev > 0 ? max($barHeight, 8) : 4;
                    @endphp
                    <div class="flex flex-col items-center gap-2 group relative">
                        <!-- Tooltip Hover -->
                        <div class="opacity-0 group-hover:opacity-100 transition-opacity absolute -top-12 z-30 px-2.5 py-1 bg-gray-900 text-white rounded-lg text-[10px] font-bold whitespace-nowrap shadow-md pointer-events-none">
                            Rp {{ number_format($rev, 0, ',', '.') }} ({{ $bkCount }} Order)
                        </div>

                        <!-- Bar -->
                        <div class="w-full max-w-[32px] rounded-t-xl transition-all duration-500 {{ $rev > 0 ? 'bg-gradient-to-t from-amber-500 to-amber-400 group-hover:from-amber-600 group-hover:to-amber-500 shadow-xs' : 'bg-gray-100 dark:bg-gray-700/60' }}"
                             style="height: {{ $minHeight }}%;"></div>

                        <!-- Month Label -->
                        <span class="text-[10px] sm:text-xs font-bold text-gray-500 dark:text-gray-400 mt-1">
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
                        <th class="py-3.5 px-4 sm:px-6">Net Payout Studio (90%)</th>
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
                            <td class="py-4 px-4 sm:px-6">
                                @if($c->status_payout === 'paid')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300 border border-emerald-200">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                        Payout Dicairkan
                                    </span>
                                    @if($c->payout_at)
                                        <p class="text-[10px] text-gray-400 mt-1">{{ $c->payout_at->format('d M Y H:i') }}</p>
                                    @endif
                                @elseif($c->status_payout === 'ready')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold bg-blue-100 text-blue-800 dark:bg-blue-950/80 dark:text-blue-300 border border-blue-200">
                                        Siap Dicairkan Admin
                                    </span>
                                @elseif($c->status_payout === 'refunded')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold bg-rose-100 text-rose-800 dark:bg-rose-950/80 dark:text-rose-300 border border-rose-200">
                                        🔴 Refunded
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold bg-amber-100 text-amber-800 dark:bg-amber-950/80 dark:text-amber-300 border border-amber-200">
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
                {{ $contracts->links() }}
            </div>
        @endif
    </div>

</div>
@endsection