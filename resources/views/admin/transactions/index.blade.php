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
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white tracking-tight">Monitoring Transaksi & Escrow</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Kelola rekening bersama, penahanan dana garansi (Escrow), dan pencairan payout studio fotografer</p>
        </div>
    </div>

    <!-- 4 Summary Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Omzet Masuk -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Omzet Masuk</span>
                <span class="p-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </span>
            </div>
            <div class="mt-3">
                <h2 class="text-xl font-black text-gray-900 dark:text-white">Rp {{ number_format($totalGrossVolume, 0, ',', '.') }}</h2>
                <p class="text-[11px] text-emerald-600 dark:text-emerald-400 font-semibold mt-1 flex items-center gap-1">
                    <span>Rekening Bersama Marketplace</span>
                </p>
            </div>
        </div>

        <!-- Card 2: Escrow Holding -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Escrow Holding</span>
                <span class="p-2 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </span>
            </div>
            <div class="mt-3">
                <h2 class="text-xl font-black text-amber-600 dark:text-amber-400">Rp {{ number_format($totalEscrowHolding, 0, ',', '.') }}</h2>
                <p class="text-[11px] text-gray-500 dark:text-gray-400 font-medium mt-1">Dana Garansi Masa Sesi Foto</p>
            </div>
        </div>

        <!-- Card 3: Siap Dicairkan (Ready) -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Siap Dicairkan</span>
                <span class="p-2 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </span>
            </div>
            <div class="mt-3">
                <h2 class="text-xl font-black text-blue-600 dark:text-blue-400">Rp {{ number_format($totalEscrowReady, 0, ',', '.') }}</h2>
                <p class="text-[11px] text-blue-600 dark:text-blue-400 font-semibold mt-1">Sesi Foto Selesai / Siap Payout</p>
            </div>
        </div>

        <!-- Card 4: Total Payout Paid & Fee -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Payout Studio Dicairkan</span>
                <span class="p-2 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </span>
            </div>
            <div class="mt-3">
                <h2 class="text-xl font-black text-gray-900 dark:text-white">Rp {{ number_format($totalPayoutPaid, 0, ',', '.') }}</h2>
                <p class="text-[11px] text-gray-500 dark:text-gray-400 font-medium mt-1">
                    Komisi Platform (10%): <strong class="text-amber-600 dark:text-amber-400">Rp {{ number_format($totalMarketplaceFee, 0, ',', '.') }}</strong>
                </p>
            </div>
        </div>
    </div>

    <!-- Filters & Search Toolbar Card -->
    <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        
        <!-- Status Filter Tabs -->
        <div class="flex items-center gap-1.5 overflow-x-auto w-full md:w-auto pb-1 md:pb-0 scrollbar-none">
            @php
                $tabs = [
                    'all' => 'Semua Transaksi',
                    'holding' => 'Escrow Holding',
                    'ready' => 'Siap Payout',
                    'paid' => 'Sudah Dicairkan',
                    'refunded' => 'Refunded'
                ];
            @endphp
            @foreach($tabs as $key => $label)
                <a href="{{ route('admin.transactions', ['status' => $key, 'search' => $search]) }}" 
                   class="px-3.5 py-2 rounded-xl text-xs font-extrabold whitespace-nowrap transition {{ $statusFilter === $key ? 'bg-amber-500 text-white shadow-md' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <!-- Search Form -->
        <form method="GET" action="{{ route('admin.transactions') }}" class="flex items-center gap-2 w-full md:w-auto">
            <input type="hidden" name="status" value="{{ $statusFilter }}">
            <div class="relative w-full md:w-64">
                <input type="text" 
                       name="search" 
                       value="{{ $search }}" 
                       placeholder="Cari Klien, Studio, Invoice..." 
                       class="w-full pl-9 pr-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <button type="submit" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-extrabold text-xs shadow-sm transition">
                Cari
            </button>
        </form>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-[11px] font-extrabold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        <th class="py-3.5 px-4 sm:px-6">Invoice / Kontrak</th>
                        <th class="py-3.5 px-4 sm:px-6">Klien (Penyewa)</th>
                        <th class="py-3.5 px-4 sm:px-6">Studio Fotografer</th>
                        <th class="py-3.5 px-4 sm:px-6">Rincian Nominal</th>
                        <th class="py-3.5 px-4 sm:px-6">Status Escrow / Payout</th>
                        <th class="py-3.5 px-4 sm:px-6 text-center">Aksi Admin</th>
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
                            <td class="py-4 px-4 sm:px-6">
                                <div class="font-extrabold text-gray-900 dark:text-white">{{ $invoiceCode }}</div>
                                <div class="text-[10px] text-gray-400 mt-0.5">{{ $c->created_at->format('d M Y, H:i') }}</div>
                                <span class="inline-block mt-1.5 px-2 py-0.5 rounded-md text-[10px] font-bold uppercase bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                    {{ $c->status_contract }}
                                </span>
                            </td>

                            <!-- Klien -->
                            <td class="py-4 px-4 sm:px-6">
                                <div class="font-bold text-gray-900 dark:text-white">{{ $c->client->nama ?? 'Klien' }}</div>
                                <div class="text-[11px] text-gray-500 dark:text-gray-400">{{ $c->client->user->email ?? '-' }}</div>
                            </td>

                            <!-- Studio Fotografer -->
                            <td class="py-4 px-4 sm:px-6">
                                <div class="font-bold text-amber-600 dark:text-amber-400">{{ $c->photographer->nama ?? 'Studio Fotografer' }}</div>
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
                            <td class="py-4 px-4 sm:px-6">
                                @if($c->status_payout === 'ready')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold bg-blue-100 text-blue-800 dark:bg-blue-950/80 dark:text-blue-300 border border-blue-200">
                                        <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                                        Siap Dicairkan (Ready)
                                    </span>
                                    <p class="text-[10px] text-gray-500 mt-1">Sesi Foto Completed</p>
                                @elseif($c->status_payout === 'paid')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300 border border-emerald-200">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                        Payout Dicairkan
                                    </span>
                                    @if($c->payout_at)
                                        <p class="text-[10px] text-gray-400 mt-1">{{ $c->payout_at->format('d M Y H:i') }}</p>
                                    @endif
                                @elseif($c->status_payout === 'refunded')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold bg-rose-100 text-rose-800 dark:bg-rose-950/80 dark:text-rose-300 border border-rose-200">
                                        🔴 Refunded (Dikembalikan)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold bg-amber-100 text-amber-800 dark:bg-amber-950/80 dark:text-amber-300 border border-amber-200">
                                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                        Holding Escrow
                                    </span>
                                    <p class="text-[10px] text-gray-500 mt-1">Garansi Masa Sesi Foto</p>
                                @endif
                            </td>

                            <!-- Aksi Admin -->
                            <td class="py-4 px-4 sm:px-6 text-center space-y-1.5">
                                <!-- Action 1: Cairkan Payout -->
                                @if(in_array($c->status_payout, ['ready', 'holding']))
                                    <button type="button" 
                                            @click="$dispatch('open-confirm-modal', {
                                                title: 'Cairkan Payout ke Studio Fotografer?',
                                                message: 'Anda akan mentransfer dana Net Payout sebesar Rp {{ number_format($netPayout, 0, ',', '.') }} ke rekening {{ $c->photographer->nama ?? 'Studio' }}. Lanjutkan pencairan?',
                                                confirmText: 'Ya, Cairkan Payout',
                                                type: 'success',
                                                actionUrl: '{{ route('admin.transactions.release-payout', $c->id) }}',
                                                method: 'POST'
                                            })"
                                            class="w-full px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-sm transition flex items-center justify-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <span>Cairkan Payout</span>
                                    </button>
                                @elseif($c->status_payout === 'paid')
                                    <span class="inline-block px-3 py-1 text-[11px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 rounded-lg">
                                        Transfer Selesai
                                    </span>
                                @endif

                                <!-- Action 2: Refund Dana -->
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
                                            class="w-full px-3 py-1 rounded-xl bg-gray-100 hover:bg-rose-100 text-gray-700 hover:text-rose-700 dark:bg-gray-700 dark:hover:bg-rose-950/60 dark:text-gray-300 dark:hover:text-rose-300 font-bold text-[11px] transition">
                                        Proses Refund
                                    </button>
                                @endif
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
        @if($contracts->hasPages())
            <div class="p-4 border-t border-gray-200 dark:border-gray-700/80 bg-gray-50/50 dark:bg-gray-900/50">
                {{ $contracts->links() }}
            </div>
        @endif
    </div>

</div>
@endsection