@extends('layouts.admin')

@section('title', 'Monitoring Transaksi & Escrow Admin - LensMatch')

@section('content')
<div class="space-y-6" x-data="{
    detailModalOpen: false,
    selectedContract: null,
    openDetailModal(contract) {
        this.selectedContract = contract;
        this.detailModalOpen = true;
    }
}">

    <!-- Page Title Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <span class="text-[11px] font-extrabold uppercase tracking-widest text-amber-600 dark:text-amber-400">Pusat Keuangan Marketplace</span>
            <h1 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">Monitoring Transaksi & Escrow</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Kelola rekening bersama, penahanan dana garansi (Escrow), dan pencairan payout mitra fotografer</p>
        </div>
    </div>

    <!-- 4 Summary Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Omzet Masuk -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm relative overflow-hidden">
            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Omzet Masuk</span>
            <div class="mt-3">
                <h2 class="text-xl font-black text-gray-900 dark:text-white">Rp {{ number_format($totalGrossVolume, 0, ',', '.') }}</h2>
                <p class="text-[11px] text-gray-500 dark:text-gray-400 font-medium mt-1">Rekening Bersama Marketplace</p>
            </div>
        </div>

        <!-- Card 2: Escrow Holding -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm relative overflow-hidden">
            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Escrow Holding</span>
            <div class="mt-3">
                <h2 class="text-xl font-black text-gray-900 dark:text-white">Rp {{ number_format($totalEscrowHolding, 0, ',', '.') }}</h2>
                <p class="text-[11px] text-gray-500 dark:text-gray-400 font-medium mt-1">Dana Garansi Masa Sesi Foto</p>
            </div>
        </div>

        <!-- Card 3: Siap Dicairkan (Ready) -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm relative overflow-hidden">
            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Siap Dicairkan</span>
            <div class="mt-3">
                <h2 class="text-xl font-black text-gray-900 dark:text-white">Rp {{ number_format($totalEscrowReady, 0, ',', '.') }}</h2>
                <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">Sesi Foto Selesai / Siap Payout</p>
            </div>
        </div>

        <!-- Card 4: Total Payout Paid & Fee -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm relative overflow-hidden">
            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Payout Fotografer Dicairkan</span>
            <div class="mt-3">
                <h2 class="text-xl font-black text-gray-900 dark:text-white">Rp {{ number_format($totalPayoutPaid, 0, ',', '.') }}</h2>
                <p class="text-[11px] text-gray-500 dark:text-gray-400 font-medium mt-1">
                    Komisi Platform (10%): <strong class="text-gray-900 dark:text-white font-bold">Rp {{ number_format($totalMarketplaceFee, 0, ',', '.') }}</strong>
                </p>
            </div>
        </div>
    </div>

    </div>

    <!-- Filters & Search Toolbar Card (Alpine.js Custom Floating Dropdown) -->
    <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <!-- Filter Dropdowns di Kiri -->
        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <!-- 1. Dropdown Filter Status Escrow -->
            @php
                $statusLabels = [
                    'all' => 'Semua Status Transaksi',
                    'holding' => 'Holding Escrow',
                    'ready' => 'Siap Dicairkan',
                    'paid' => 'Payout Selesai',
                    'refunded' => 'Refund Transaksi'
                ];
                $curStatusName = $statusLabels[$statusFilter] ?? 'Semua Status Transaksi';
            @endphp
            <div class="relative w-full sm:w-auto min-w-[190px]" x-data="{ open: false, selectedId: '{{ $statusFilter }}', selectedName: '{{ $curStatusName }}' }">
                <button @click="open = !open" type="button" 
                        class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs font-bold text-gray-800 dark:text-white flex items-center justify-between gap-2 shadow-2xs hover:border-amber-400 dark:hover:border-amber-500 cursor-pointer transition">
                    <span x-text="selectedName" class="truncate"></span>
                    <svg class="w-3.5 h-3.5 text-gray-400 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open" @click.away="open = false" x-transition 
                     class="absolute z-50 left-0 mt-2 min-w-[200px] w-full bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700/80 py-1 max-h-60 overflow-y-auto" style="display: none;">
                    @foreach($statusLabels as $sKey => $sVal)
                        <button type="button" @click="selectedId = '{{ $sKey }}'; selectedName = '{{ $sVal }}'; open = false; applyFilter('status', '{{ $sKey }}')" 
                                class="w-full px-3.5 py-2 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition flex items-center justify-between text-left cursor-pointer border-b border-gray-50 dark:border-gray-700/40 last:border-0"
                                :class="selectedId === '{{ $sKey }}' ? 'bg-amber-50/70 dark:bg-amber-950/30 text-amber-600 font-extrabold' : ''">
                            <span>{{ $sVal }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- 2. Dropdown Urutan / Sort -->
            @php
                $sortLabels = [
                    'latest' => 'Urutan: Terbaru (Default)',
                    'oldest' => 'Urutan: Terlama',
                    'highest' => 'Nominal Tertinggi',
                    'lowest' => 'Nominal Terendah'
                ];
                $curSortName = $sortLabels[$sort ?? 'latest'] ?? 'Urutan: Terbaru';
            @endphp
            <div class="relative w-full sm:w-auto min-w-[180px]" x-data="{ open: false, selectedId: '{{ $sort ?? 'latest' }}', selectedName: '{{ $curSortName }}' }">
                <button @click="open = !open" type="button" 
                        class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs font-bold text-gray-800 dark:text-white flex items-center justify-between gap-2 shadow-2xs hover:border-amber-400 dark:hover:border-amber-500 cursor-pointer transition">
                    <span x-text="selectedName" class="truncate"></span>
                    <svg class="w-3.5 h-3.5 text-gray-400 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open" @click.away="open = false" x-transition 
                     class="absolute z-50 left-0 mt-2 min-w-[190px] w-full bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700/80 py-1 max-h-60 overflow-y-auto" style="display: none;">
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

        <!-- Search Bar di Kanan (NO BUTTON CARI - Real-Time Live Search) -->
        <div class="relative w-full md:w-80">
            <input type="text" 
                   value="{{ $search }}" 
                   @input="handleSearch($event)"
                   placeholder="Cari Klien, Fotografer, Invoice..." 
                   class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">
            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm overflow-hidden">
        <div id="dashboard-table-container" class="transition-opacity duration-200" :class="isLoading ? 'opacity-40 pointer-events-none' : 'opacity-100'">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-[11px] font-extrabold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        <th class="py-3.5 px-4 sm:px-6">Invoice / Kontrak</th>
                        <th class="py-3.5 px-4 sm:px-6">Klien (Penyewa)</th>
                        <th class="py-3.5 px-4 sm:px-6">Fotografer</th>
                        <th class="py-3.5 px-4 sm:px-6">Rincian Nominal</th>
                        <th class="py-3.5 px-4 sm:px-6">Status Escrow / Payout</th>
                        <th class="py-3.5 px-4 sm:px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60 text-xs">
                    @forelse($contracts as $c)
                        @php
                            $payment = $c->payments->first();
                            $invoiceCode = $payment ? $payment->external_id : 'INV-LM-' . str_pad($c->id, 4, '0', STR_PAD_LEFT);
                            $feeAmount = $c->fee_marketplace > 0 ? $c->fee_marketplace : ($c->jumlah * 0.10);
                            $netPayout = $c->payout_amount > 0 ? $c->payout_amount : ($c->jumlah - $feeAmount);
                        @endphp
                        <tr class="hover:bg-amber-50/30 dark:hover:bg-amber-950/20 transition">
                            <!-- Invoice / Kontrak -->
                            <td class="py-4 px-4 sm:px-6 align-middle">
                                <div class="font-extrabold text-gray-900 dark:text-white">{{ $invoiceCode }}</div>
                                <div class="text-[10px] text-gray-400 mt-0.5">{{ $c->created_at->format('d M Y, H:i') }}</div>
                            </td>

                            <!-- Klien -->
                            <td class="py-4 px-4 sm:px-6">
                                <div class="font-bold text-gray-900 dark:text-white">{{ $c->client->nama ?? 'Klien' }}</div>
                                <div class="text-[11px] text-gray-500 dark:text-gray-400">{{ $c->client->user->email ?? '-' }}</div>
                            </td>

                            <!-- Studio Fotografer -->
                            <td class="py-4 px-4 sm:px-6">
                                <div class="font-bold text-gray-900 dark:text-white">{{ $c->photographer->nama ?? 'Fotografer' }}</div>
                                <div class="text-[11px] text-gray-500 dark:text-gray-400">
                                    {{ $c->bookingDetail->service->nama_layanan ?? 'Paket Foto Custom' }}
                                </div>
                            </td>

                            <!-- Rincian Nominal -->
                            <td class="py-4 px-4 sm:px-6 space-y-1">
                                <div class="font-extrabold text-gray-900 dark:text-white">Total: Rp {{ number_format($c->jumlah, 0, ',', '.') }}</div>
                                <div class="text-[10px] text-gray-500">
                                    Komisi Platform (10%): <span class="text-amber-600 dark:text-amber-400 font-bold">Rp {{ number_format($feeAmount, 0, ',', '.') }}</span>
                                </div>
                                <div class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400">
                                    Net Payout: Rp {{ number_format($netPayout, 0, ',', '.') }}
                                </div>
                            </td>

                            <!-- Status Escrow / Payout -->
                            <td class="py-4 px-4 sm:px-6 align-middle whitespace-nowrap">
                                @if($c->status_payout === 'ready')
                                    <span class="text-xs font-bold text-blue-600 dark:text-blue-400">
                                        Siap Dicairkan
                                    </span>
                                @elseif($c->status_payout === 'paid')
                                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">
                                        Payout Dicairkan
                                    </span>
                                    @if($c->payout_at)
                                        <p class="text-[10px] text-gray-400 mt-0.5">{{ $c->payout_at->format('d M Y, H:i') }}</p>
                                    @endif
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

                            <!-- Aksi -->
                            <td class="py-4 px-4 sm:px-6 align-middle text-center whitespace-nowrap">
                                <div class="inline-flex flex-col items-center gap-1.5 w-28">
                                    <!-- Action 1: Cairkan Payout -->
                                    @if(in_array($c->status_payout, ['ready', 'holding']))
                                        <button type="button" 
                                                @click="$dispatch('open-confirm-modal', {
                                                    title: 'Cairkan Payout ke Fotografer?',
                                                    message: 'Anda akan mentransfer dana Net Payout sebesar Rp {{ number_format($netPayout, 0, ',', '.') }} ke rekening {{ $c->photographer->nama ?? 'Fotografer' }}. Lanjutkan pencairan?',
                                                    confirmText: 'Ya, Cairkan Payout',
                                                    type: 'success',
                                                    actionUrl: '{{ route('admin.transactions.release-payout', $c->id) }}',
                                                    method: 'POST'
                                                })"
                                                class="w-full py-1.5 px-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] shadow-2xs transition">
                                            Cairkan Payout
                                        </button>
                                    @elseif($c->status_payout === 'paid')
                                        <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 block py-1">Transfer Selesai</span>
                                    @endif

                                    <!-- Action 2: Refund Dana (Warna Merah Kontras) -->
                                    @if($c->status_payout !== 'refunded' && $c->status_payout !== 'paid')
                                        <button type="button" 
                                                @click="$dispatch('open-confirm-modal', {
                                                    title: 'Refund Transaksi ke Klien?',
                                                    message: 'Anda akan mengembalikan dana sebesar Rp {{ number_format($c->jumlah, 0, ',', '.') }} ke akun Klien {{ $c->client->nama ?? '' }}. Lanjutkan refund?',
                                                    confirmText: 'Ya, Refund Dana',
                                                    type: 'danger',
                                                    actionUrl: '{{ route('admin.transactions.refund', $c->id) }}',
                                                    method: 'POST'
                                                })"
                                                class="w-full py-1.5 px-2.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200/80 dark:border-rose-900/60 font-bold text-[11px] transition">
                                            Proses Refund
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-gray-400 dark:text-gray-500 space-y-2">
                                <svg class="w-10 h-10 mx-auto text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <p class="text-xs font-bold">Tidak ada data transaksi atau escrow ditemukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        @if($contracts->total() > 0)
            <div class="p-4 border-t border-gray-200 dark:border-gray-700/80 bg-gray-50/50 dark:bg-gray-900/50">
                {{ $contracts->links('partials.dashboard.pagination') }}
            </div>
        @endif
        </div>
    </div>
</div>
@endsection