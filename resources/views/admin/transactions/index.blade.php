@extends('layouts.admin')



@section('title', 'Monitoring Transaksi - LensMatch')



@section('content')

<div class="space-y-6" x-data="{

    activeTab: 'booking',

    detailModalOpen: false,

    payoutModalOpen: false,

    refundModalOpen: false,

    activeContract: null,

    payoutNotes: '',

    refundNotes: '',

    openDetail(c) {

        this.activeContract = c;

        this.detailModalOpen = true;

    },

    openPayout(c) {

        this.activeContract = c;

        this.payoutNotes = 'Pencairan dana 90% disetujui Admin.';

        this.payoutModalOpen = true;

    },

    openRefund(c) {

        this.activeContract = c;

        this.refundNotes = 'Pengembalian dana (refund) transaksi disetujui Admin.';

        this.refundModalOpen = true;

    },

    isLoading: false,

    applyFilter(param, val) {

        const url = new URL(window.location.href);

        url.searchParams.set(param, val);

        url.searchParams.delete('page');

        window.location.href = url.toString();

    },

    handleSearch(e) {

        const q = e.target.value.toLowerCase();

        const rows = document.querySelectorAll('tbody tr');

        rows.forEach(r => {

            const text = r.innerText.toLowerCase();

            r.style.display = text.includes(q) ? '' : 'none';

        });

    }

}">



    <!-- Page Title Header -->

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

        <div>

            <h1 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">Monitoring Transaksi</h1>

        </div>

    </div>



    <!-- 4 Summary Stat Cards -->

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        <!-- Card 1: Total Omzet Masuk -->

        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">

            <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Total Omzet Masuk</span>

            <h2 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">Rp {{ number_format($totalGrossVolume, 0, ',', '.') }}</h2>

        </div>



        <!-- Card 2: Dana Ditahan -->

        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">

            <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Dana Ditahan</span>

            <h2 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">Rp {{ number_format($totalEscrowHolding, 0, ',', '.') }}</h2>

        </div>



        <!-- Card 3: Siap Dicairkan (Ready) -->

        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">

            <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Siap Dicairkan</span>

            <h2 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">Rp {{ number_format($totalEscrowReady, 0, ',', '.') }}</h2>

        </div>



        <!-- Card 4: Total Dana Ditransfer -->

        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">

            <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Total Dana Ditransfer</span>

            <h2 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">Rp {{ number_format($totalPayoutPaid, 0, ',', '.') }}</h2>

        </div>

    </div>



    <!-- Tabs: Escrow Sesi Booking vs Penjualan E-Learning vs Pendaftaran Lomba (Clean Underline Style, No Long Divider Line) -->

    <div class="flex items-center gap-6 overflow-x-auto pb-1">

        <button type="button" 

                @click="activeTab = 'booking'"

                :class="activeTab === 'booking' ? 'text-amber-600 dark:text-amber-400 font-black' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-bold'"

                class="py-2 text-xs sm:text-sm transition flex items-center gap-2 cursor-pointer">

            <span :class="activeTab === 'booking' ? 'underline underline-offset-8 decoration-2 decoration-amber-500 dark:decoration-amber-400' : ''">Transaksi Booking</span>

            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold" 

                  :class="activeTab === 'booking' ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300'">

                {{ $contracts->total() }}

            </span>

        </button>



        <button type="button" 

                @click="activeTab = 'elearning'"

                :class="activeTab === 'elearning' ? 'text-amber-600 dark:text-amber-400 font-black' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-bold'"

                class="py-2 text-xs sm:text-sm transition flex items-center gap-2 cursor-pointer">

            <span :class="activeTab === 'elearning' ? 'underline underline-offset-8 decoration-2 decoration-amber-500 dark:decoration-amber-400' : ''">Penjualan E-Learning</span>

            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold"

                  :class="activeTab === 'elearning' ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300'">

                {{ count($elearningTransactions) }}

            </span>

        </button>



        <button type="button" 

                @click="activeTab = 'contest'"

                :class="activeTab === 'contest' ? 'text-amber-600 dark:text-amber-400 font-black' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-bold'"

                class="py-2 text-xs sm:text-sm transition flex items-center gap-2 cursor-pointer">

            <span :class="activeTab === 'contest' ? 'underline underline-offset-8 decoration-2 decoration-amber-500 dark:decoration-amber-400' : ''">Pendaftaran Lomba</span>

            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold"

                  :class="activeTab === 'contest' ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300'">

                {{ count($contestRegistrations ?? []) }}

            </span>

        </button>

    </div>



    <!-- Data Table Card -->

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm overflow-hidden space-y-4">

        

        <!-- Filters & Search Toolbar (Alpine.js Custom Floating Dropdown) -->

        <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex flex-col md:flex-row items-center justify-between gap-4">

            <!-- Filter Dropdowns di Kiri -->

            <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">

                <!-- 1. Dropdown Filter Status Escrow -->

                @php

                    $statusLabels = [

                        'all' => 'Semua Status Transaksi',

                        'holding' => 'Dana Ditahan',

                        'ready' => 'Siap Dicairkan',

                        'paid' => 'Sudah Ditransfer',

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

                       class="w-full pl-4 pr-10 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">

                <svg class="w-4 h-4 text-gray-400 absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>

            </div>

        </div>



        <div id="dashboard-table-container" x-show="activeTab === 'booking'" class="transition-opacity duration-200" :class="isLoading ? 'opacity-40 pointer-events-none' : 'opacity-100'">

        <div class="overflow-x-auto">

            <table class="w-full text-left border-collapse">

                <thead>

                    <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-[11px] font-extrabold uppercase tracking-wider text-gray-500 dark:text-gray-400">

                        <th class="py-3.5 px-4 sm:px-6">Invoice</th>

                        <th class="py-3.5 px-4 sm:px-6">Klien</th>

                        <th class="py-3.5 px-4 sm:px-6">Fotografer</th>

                        <th class="py-3.5 px-4 sm:px-6">Rincian Nominal</th>

                        <th class="py-3.5 px-4 sm:px-6">Status Pembayaran</th>

                        <th class="py-3.5 px-4 sm:px-6 text-center">Aksi</th>

                    </tr>

                </thead>

                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60 text-xs">

                    @forelse($contracts as $c)

                        @php

                            $payment = $c->payments->first();

                            $invoiceCode = $c->invoice_code;

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



                            <!-- Fotografer -->

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

                                    Komisi Platform (10%): <span class="font-bold text-gray-700 dark:text-gray-300">Rp {{ number_format($feeAmount, 0, ',', '.') }}</span>

                                </div>

                                <div class="text-[11px] font-bold text-gray-900 dark:text-white">

                                    Pendapatan Bersih: Rp {{ number_format($netPayout, 0, ',', '.') }}

                                </div>

                            </td>



                            <!-- Status Pembayaran -->

                            <td class="py-4 px-4 sm:px-6 align-middle whitespace-nowrap">

                                @if($c->status_payout === 'ready')

                                    <span class="text-xs font-bold text-blue-600 dark:text-blue-400">

                                        Siap Dicairkan

                                    </span>

                                @elseif($c->status_payout === 'paid')

                                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">

                                        Sudah Ditransfer

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

                                        Dana Ditahan

                                    </span>

                                @endif

                            </td>



                            <!-- Aksi -->

                            <td class="py-4 px-4 sm:px-6 align-middle text-center whitespace-nowrap">

                                @php

                                    $contractData = [

                                        'id' => $c->id,

                                        'code' => 'KTRK-' . str_pad($c->id, 5, '0', STR_PAD_LEFT),

                                        'created_at' => $c->created_at->format('d M Y, H:i'),

                                        'status_contract' => $c->status_contract,

                                        'status_payout' => $c->status_payout ?? 'holding',

                                        'payout_at' => $c->payout_at ? $c->payout_at->format('d M Y, H:i') : null,

                                        'payout_notes' => $c->payout_notes,

                                        'jumlah_formatted' => 'Rp ' . number_format($c->jumlah, 0, ',', '.'),

                                        'fee_formatted' => 'Rp ' . number_format($feeAmount, 0, ',', '.'),

                                        'net_payout_formatted' => 'Rp ' . number_format($netPayout, 0, ',', '.'),

                                        'client_name' => $c->client->nama ?? 'Klien',

                                        'client_email' => $c->client->user->email ?? '-',

                                        'client_phone' => $c->client->nomor_telepon ?? '-',

                                        'photographer_name' => $c->photographer->nama ?? 'Fotografer',

                                        'photographer_email' => $c->photographer->user->email ?? '-',

                                        'photographer_phone' => $c->photographer->nomor_telepon ?? '-',

                                        'photographer_bank_name' => $c->photographer->nama_bank ?? null,

                                        'photographer_bank_account' => $c->photographer->nomor_rekening ?? null,

                                        'photographer_bank_holder' => $c->photographer->atas_nama_rekening ?? null,

                                        'service_name' => $c->bookingDetail->service->nama_layanan ?? 'Paket Foto',

                                        'booking_date' => $c->bookingDetail && $c->bookingDetail->booking_date ? date('d M Y', strtotime($c->bookingDetail->booking_date)) : '-',

                                        'booking_time' => $c->bookingDetail && $c->bookingDetail->jam_mulai ? ($c->bookingDetail->jam_mulai . ' WIB') : '-',

                                        'location' => $c->bookingDetail->lokasi ?? '-',

                                        'status_booking' => $c->bookingDetail->status_booking ?? 'pending',

                                        'hasil_foto_url' => $c->bookingDetail->hasil_foto_url ?? null,

                                        'catatan_fotografer' => $c->bookingDetail->catatan_fotografer ?? null,

                                        'external_id' => $c->invoice_code,

                                        'payment_status' => $c->payments->first()->payment_status ?? 'pending',

                                        'payment_type' => $c->payments->first()->payment_type ?? 'transfer',

                                        'bukti_transfer' => $c->payments->first()->bukti_transfer ?? null,

                                        'cancellation_reason' => $c->cancellation->alasan ?? null,

                                        'cancelled_by' => $c->cancellation->dibatalkan_oleh ?? null,

                                        'refund_amount' => $c->cancellation ? ('Rp ' . number_format($c->cancellation->refund_amount, 0, ',', '.')) : null,

                                    ];

                                @endphp

                                <div class="flex items-center justify-center gap-1.5" x-data="{ menuOpen: false }">

                                    <!-- Tombol Utama: Lihat Detail Transaksi -->

                                    <button type="button" 

                                            @click="openDetail({{ json_encode($contractData) }})" 

                                            class="px-3 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200/80 dark:border-blue-900/60 font-bold text-xs transition min-w-[60px] text-center cursor-pointer shadow-2xs">

                                        Lihat

                                    </button>



                                    <!-- Dropdown Menu Opsi Lainnya (⋮) -->

                                    <div class="relative inline-block text-left">

                                        <button type="button" 

                                                @click="menuOpen = !menuOpen" 

                                                class="w-8 h-8 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white flex items-center justify-center transition shadow-2xs cursor-pointer">

                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">

                                                <circle cx="12" cy="5" r="1.75" />

                                                <circle cx="12" cy="12" r="1.75" />

                                                <circle cx="12" cy="19" r="1.75" />

                                            </svg>

                                        </button>



                                        <!-- Floating Dropdown Menu -->

                                        <div x-show="menuOpen" 

                                             @click.away="menuOpen = false" 

                                             x-transition:enter="transition ease-out duration-100" 

                                             x-transition:enter-start="transform opacity-0 scale-95" 

                                             x-transition:enter-end="transform opacity-100 scale-100" 

                                             x-transition:leave="transition ease-in duration-75" 

                                             x-transition:leave-start="transform opacity-100 scale-100" 

                                             x-transition:leave-end="transform opacity-0 scale-95" 

                                             class="absolute right-0 top-full mt-1.5 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 py-1.5 z-50 text-left" 

                                             style="display: none;">

                                            

                                            @if($c->payments->first() && $c->payments->first()->bukti_transfer && $c->payments->first()->payment_status === 'pending')

                                                <!-- Verifikasi Pembayaran Transfer (Universal Confirm Modal per Rule #18) -->

                                                <button type="button" 

                                                        @click="menuOpen = false; $dispatch('open-confirm-modal', {

                                                            title: 'Verifikasi Pembayaran Transfer?',

                                                            message: 'Apakah Anda yakin pembayaran transfer untuk pesanan ini sudah valid dan dana telah masuk ke rekening bersama escrow?',

                                                            confirmText: 'Ya, Verifikasi Bayar',

                                                            type: 'success',

                                                            actionUrl: '{{ route('admin.transactions.verify-payment', $c->id) }}',

                                                            method: 'POST'

                                                        })"

                                                        class="w-full px-3.5 py-2 text-xs font-semibold text-emerald-700 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 transition block text-left cursor-pointer">

                                                    <span>Verifikasi Bayar</span>

                                                </button>



                                                <!-- Tolak Bukti Transfer (Universal Confirm Modal per Rule #18) -->

                                                <button type="button" 

                                                        @click="menuOpen = false; $dispatch('open-confirm-modal', {

                                                            title: 'Tolak Bukti Transfer?',

                                                            message: 'Apakah Anda yakin ingin menolak bukti transfer ini dan meminta klien untuk mengunggah ulang bukti pembayaran?',

                                                            confirmText: 'Ya, Tolak Bukti Bayar',

                                                            type: 'danger',

                                                            actionUrl: '{{ route('admin.transactions.reject-payment', $c->id) }}',

                                                            method: 'POST'

                                                        })"

                                                        class="w-full px-3.5 py-2 text-xs font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition block text-left cursor-pointer">

                                                    <span>Tolak Bukti Bayar</span>

                                                </button>

                                                <div class="border-t border-gray-100 dark:border-gray-700/60 my-1"></div>

                                            @endif



                                            @if(in_array($c->status_payout, ['ready', 'holding']))

                                                <!-- Transfer Dana -->

                                                <button type="button" 

                                                        @click="menuOpen = false; openPayout({{ json_encode($contractData) }})"

                                                        class="w-full px-3.5 py-2 text-xs font-semibold text-emerald-700 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 transition block text-left cursor-pointer">

                                                    <span>Transfer Dana</span>

                                                </button>

                                            @endif



                                            @if($c->status_payout !== 'refunded' && $c->status_payout !== 'paid')

                                                <!-- Proses Refund -->

                                                <button type="button" 

                                                        @click="menuOpen = false; openRefund({{ json_encode($contractData) }})"

                                                        class="w-full px-3.5 py-2 text-xs font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition block text-left cursor-pointer">

                                                    <span>Proses Refund</span>

                                                </button>

                                            @endif



                                            @if($c->status_payout === 'paid')

                                                <div class="px-3.5 py-1.5 text-[11px] font-bold text-gray-400 dark:text-gray-500 block"><span>Transfer Selesai</span></div>

                                            @elseif($c->status_payout === 'refunded')

                                                <div class="px-3.5 py-1.5 text-[11px] font-bold text-gray-400 dark:text-gray-500 block">

                                                    <span>Refund Selesai</span>

                                                </div>

                                            @endif



                                            <div class="border-t border-gray-100 dark:border-gray-700/60 my-1"></div>



                                            <!-- Cetak / Invoice -->

                                            <a href="{{ route('booking.invoice.print', $c->id) }}" target="_blank" class="w-full px-3.5 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition flex items-center gap-2 text-left">

                                                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>

                                                <span>Cetak Invoice</span>

                                            </a>

                                        </div>

                                    </div>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="6" class="py-12 text-center text-gray-400 dark:text-gray-500 space-y-2">

                                <svg class="w-10 h-10 mx-auto text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>

                                <p class="text-xs font-bold">Tidak ada data transaksi ditemukan.</p>

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



        <!-- 2. Table Container: Penjualan Kursus E-Learning -->

        <div x-show="activeTab === 'elearning'" style="display: none;" class="overflow-x-auto">

            <table class="w-full text-left border-collapse">

                <thead>

                    <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-[11px] font-extrabold uppercase tracking-wider text-gray-500 dark:text-gray-400">

                        <th class="py-3.5 px-4 sm:px-6">Invoice & Tanggal</th>

                        <th class="py-3.5 px-4 sm:px-6">Pembeli (User)</th>

                        <th class="py-3.5 px-4 sm:px-6">Materi Kursus E-Learning</th>

                        <th class="py-3.5 px-4 sm:px-6">Metode</th>

                        <th class="py-3.5 px-4 sm:px-6">Nominal Transaksi</th>

                        <th class="py-3.5 px-4 sm:px-6">Status Pembayaran</th>

                        <th class="py-3.5 px-4 sm:px-6 text-center">Aksi</th>

                    </tr>

                </thead>

                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60 text-xs">

                    @forelse($elearningTransactions as $el)

                        <tr class="hover:bg-amber-50/30 dark:hover:bg-amber-950/20 transition">

                            <!-- Invoice & Tanggal -->

                            <td class="py-4 px-4 sm:px-6 align-middle font-mono font-bold text-gray-900 dark:text-white">

                                {{ $el->invoice_code }}

                                <span class="block text-[10px] font-normal text-gray-400 mt-0.5">

                                    {{ $el->paid_at ? $el->paid_at->format('d M Y, H:i') : $el->created_at->format('d M Y, H:i') }}

                                </span>

                            </td>



                            <!-- Pembeli -->

                            <td class="py-4 px-4 sm:px-6 align-middle">

                                <span class="font-bold text-gray-900 dark:text-white block">{{ $el->user->nama ?? 'Pengguna' }}</span>

                                <span class="text-[11px] text-gray-500">{{ $el->user->email ?? '-' }}</span>

                            </td>



                            <!-- Kursus E-Learning -->

                            <td class="py-4 px-4 sm:px-6 align-middle">

                                <span class="font-extrabold text-gray-900 dark:text-white block">

                                    {{ $el->elearning->judul ?? 'Kursus Fotografi' }}

                                </span>

                                <span class="text-[11px] text-gray-500 capitalize">

                                    Tingkat: {{ $el->elearning->tingkat ?? 'Semua Tingkat' }}

                                </span>

                            </td>



                            <!-- Metode Bayar -->

                            <td class="py-4 px-4 sm:px-6 align-middle uppercase font-bold text-gray-600 dark:text-gray-300">

                                {{ $el->payment_method ?? 'QRIS' }}

                            </td>



                            <!-- Nominal -->

                            <td class="py-4 px-4 sm:px-6 align-middle font-black text-gray-900 dark:text-white whitespace-nowrap">

                                Rp {{ number_format($el->nominal, 0, ',', '.') }}

                                <span class="block text-[10px] text-gray-400 font-medium">100% Platform Revenue</span>

                            </td>



                            <!-- Status Pembayaran -->

                            <td class="py-4 px-4 sm:px-6 align-middle whitespace-nowrap">

                                @if($el->payment_status === 'completed')

                                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">

                                        Lunas

                                    </span>

                                @elseif($el->payment_status === 'failed')

                                    <span class="text-xs font-bold text-rose-600 dark:text-rose-400">

                                        Gagal

                                    </span>

                                @else

                                    <span class="text-xs font-bold text-amber-500 dark:text-amber-400">

                                        Menunggu Pembayaran

                                    </span>

                                @endif

                            </td>



                            <!-- Aksi -->

                            <td class="py-4 px-4 sm:px-6 align-middle text-center whitespace-nowrap">

                                @if($el->elearning)

                                    <a href="{{ route('public.elearning.show', $el->elearning->slug) }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-bold text-[11px] shadow-2xs transition inline-block">

                                        Lihat Kursus

                                    </a>

                                @else

                                    <span class="text-xs text-gray-400">-</span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="7" class="py-12 text-center text-gray-400 dark:text-gray-500 space-y-2">

                                <p class="text-xs font-bold">Belum ada data transaksi e-learning.</p>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>



        <!-- 3. Table Container: Pendaftaran Lomba Foto Berbayar -->

        <div x-show="activeTab === 'contest'" style="display: none;" class="overflow-x-auto">

            <table class="w-full text-left border-collapse">

                <thead>

                    <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-[11px] font-extrabold uppercase tracking-wider text-gray-500 dark:text-gray-400">

                        <th class="py-3.5 px-4 sm:px-6">Kode Reg & Tanggal</th>

                        <th class="py-3.5 px-4 sm:px-6">Peserta (User)</th>

                        <th class="py-3.5 px-4 sm:px-6">Event Lomba Foto</th>

                        <th class="py-3.5 px-4 sm:px-6">Biaya Pendaftaran</th>

                        <th class="py-3.5 px-4 sm:px-6">Bukti Bayar</th>

                        <th class="py-3.5 px-4 sm:px-6">Status Pembayaran</th>

                        <th class="py-3.5 px-4 sm:px-6 text-center">Aksi Kelola</th>

                    </tr>

                </thead>

                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60 text-xs">

                    @forelse($contestRegistrations as $cr)

                        <tr class="hover:bg-amber-50/30 dark:hover:bg-amber-950/20 transition">

                            <!-- Kode & Tanggal -->

                            <td class="py-4 px-4 sm:px-6 align-middle font-mono font-bold text-gray-900 dark:text-white">

                                REG-LMB-{{ str_pad($cr->id, 4, '0', STR_PAD_LEFT) }}

                                <span class="block text-[10px] font-normal text-gray-400 mt-0.5">

                                    {{ $cr->created_at->format('d M Y, H:i') }}

                                </span>

                            </td>



                            <!-- Peserta -->

                            <td class="py-4 px-4 sm:px-6 align-middle">

                                <span class="font-bold text-gray-900 dark:text-white block">{{ $cr->nama_lengkap }}</span>

                                <span class="text-[11px] text-gray-500">{{ $cr->email }} • {{ $cr->no_wa }}</span>

                            </td>



                            <!-- Event Lomba -->

                            <td class="py-4 px-4 sm:px-6 align-middle">

                                <span class="font-extrabold text-gray-900 dark:text-white block">

                                    {{ $cr->contest->judul_lomba ?? 'Lomba Foto' }}

                                </span>

                                <span class="text-[11px] text-gray-500">

                                    Penyelenggara: {{ $cr->contest->penyelenggara ?? '-' }}

                                </span>

                            </td>



                            <!-- Biaya Pendaftaran -->

                            <td class="py-4 px-4 sm:px-6 align-middle font-black text-gray-900 dark:text-white whitespace-nowrap">

                                Rp {{ number_format($cr->contest->biaya_pendaftaran ?? 0, 0, ',', '.') }}

                                <span class="block text-[10px] text-gray-400 font-medium">100% Platform Revenue</span>

                            </td>



                            <!-- Bukti Bayar -->

                            <td class="py-4 px-4 sm:px-6 align-middle whitespace-nowrap">

                                @if($cr->bukti_transfer)

                                    <a href="{{ $cr->bukti_transfer }}" target="_blank" class="text-xs font-semibold text-gray-900 dark:text-white underline">

                                        Lihat Bukti

                                    </a>

                                @else

                                    <span class="text-gray-400 text-xs italic">Belum upload</span>

                                @endif

                            </td>



                            <!-- Status Pembayaran -->

                            <td class="py-4 px-4 sm:px-6 align-middle whitespace-nowrap">

                                @if($cr->status_pembayaran === 'lunas')

                                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">

                                        Lunas

                                    </span>

                                @elseif($cr->status_pembayaran === 'menunggu_konfirmasi')

                                    <span class="text-xs font-bold text-blue-600 dark:text-blue-400">

                                        Menunggu Verifikasi

                                    </span>

                                @else

                                    <span class="text-xs font-bold text-amber-500 dark:text-amber-400">

                                        Menunggu Pembayaran

                                    </span>

                                @endif

                            </td>



                            <!-- Aksi Kelola -->

                            <td class="py-4 px-4 sm:px-6 align-middle text-center whitespace-nowrap">

                                @if($cr->contest)

                                    <a href="{{ route('admin.contests.manage', $cr->contest->id) }}" class="px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-bold text-[11px] shadow-2xs transition inline-block">

                                        Kelola Peserta

                                    </a>

                                @else

                                    <span class="text-xs text-gray-400">-</span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="7" class="py-12 text-center text-gray-400 dark:text-gray-500 space-y-2">

                                <p class="text-xs font-bold">Belum ada data pendaftaran lomba berbayar.</p>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>



    <!-- 1. Modal Detail Transaksi & Escrow (Universal Clean & Structured) -->

    <div x-show="detailModalOpen" 

         x-cloak 

         @keydown.escape.window="detailModalOpen = false" 

         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"

         x-transition>

        <div @click.away="detailModalOpen = false" 

             class="bg-white dark:bg-gray-800 max-w-2xl w-full p-6 sm:p-8 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-2xl space-y-6 max-h-[90vh] overflow-y-auto">

            

            <!-- Modal Header (No Icon, No X button because Tutup button exists) -->

            <div class="border-b border-gray-100 dark:border-gray-700/80 pb-4 text-center space-y-1">

                <h3 class="text-lg font-black text-gray-900 dark:text-white" x-text="activeContract?.code || 'Detail Transaksi'"></h3>

                <p class="text-xs text-gray-500 mt-0.5">Dibuat pada <span x-text="activeContract?.created_at"></span></p>

            </div>



            <template x-if="activeContract">

                <div class="space-y-6">

                    <!-- Ringkasan Nominal 3 Kolom -->

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">

                        <div class="bg-gray-50 dark:bg-gray-900/60 p-4 rounded-xl border border-gray-100 dark:border-gray-700/60">

                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Total Bruto</span>

                            <div class="text-base font-black text-gray-900 dark:text-white mt-1" x-text="activeContract.jumlah_formatted"></div>

                            <span class="text-[10px] text-gray-500">Nilai invoice sesi</span>

                        </div>

                        <div class="bg-amber-50/60 dark:bg-amber-950/30 p-4 rounded-xl border border-amber-200/60 dark:border-amber-800/40">

                            <span class="text-[10px] font-bold text-amber-700 dark:text-amber-400 uppercase tracking-wider block">Fee Platform (10%)</span>

                            <div class="text-base font-black text-amber-600 dark:text-amber-400 mt-1" x-text="activeContract.fee_formatted"></div>

                            <span class="text-[10px] text-amber-700/70 dark:text-amber-400/70">Pendapatan LensMatch</span>

                        </div>

                        <div class="bg-emerald-50/60 dark:bg-emerald-950/30 p-4 rounded-xl border border-emerald-200/60 dark:border-emerald-800/40">

                            <span class="text-[10px] font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider block">Pendapatan Bersih (90%)</span>

                            <div class="text-base font-black text-emerald-600 dark:text-emerald-400 mt-1" x-text="activeContract.net_payout_formatted"></div>

                            <span class="text-[10px] text-emerald-700/70 dark:text-emerald-400/70">Hak mitra fotografer</span>

                        </div>

                    </div>



                    <!-- Informasi 2 Pihak (Klien vs Fotografer) -->

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">

                        <!-- Pihak Klien -->

                        <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-700 space-y-1.5">

                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 block mb-1">Pihak Pemesan (Klien)</span>

                            <div class="font-bold text-sm text-gray-900 dark:text-white" x-text="activeContract.client_name"></div>

                            <p class="text-gray-500" x-text="'Email: ' + activeContract.client_email"></p>

                            <p class="text-gray-500" x-text="'Telepon: ' + activeContract.client_phone"></p>

                        </div>



                        <!-- Pihak Fotografer -->

                        <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-700 space-y-1.5">

                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-purple-600 dark:text-purple-400 block mb-1">Mitra Fotografer</span>

                            <div class="font-bold text-sm text-gray-900 dark:text-white" x-text="activeContract.photographer_name"></div>

                            <p class="text-gray-500" x-text="'Email: ' + activeContract.photographer_email"></p>

                            <p class="text-gray-500" x-text="'Telepon: ' + activeContract.photographer_phone"></p>

                            <template x-if="activeContract.photographer_bank_account">

                                <div class="pt-2 mt-2 border-t border-gray-100 dark:border-gray-800 text-[11px] space-y-0.5">

                                    <span class="text-gray-400 block font-bold uppercase text-[9px]">Rekening Tujuan:</span>

                                    <p class="font-bold text-gray-900 dark:text-white" x-text="activeContract.photographer_bank_name + ' — ' + activeContract.photographer_bank_account"></p>

                                    <p class="text-gray-500" x-text="'a.n. ' + (activeContract.photographer_bank_holder || activeContract.photographer_name)"></p>

                                </div>

                            </template>

                        </div>

                    </div>



                    <!-- Rincian Layanan & Sesi Booking -->

                    <div class="p-4 rounded-xl bg-gray-50/70 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-700 space-y-2 text-xs">

                        <span class="text-[10px] font-black uppercase tracking-wider text-gray-400 block">Rincian Paket & Jadwal Acara</span>

                        <div class="flex justify-between py-1 border-b border-gray-100 dark:border-gray-800">

                            <span class="text-gray-500">Nama Layanan:</span>

                            <span class="font-bold text-gray-900 dark:text-white" x-text="activeContract.service_name"></span>

                        </div>

                        <div class="flex justify-between py-1 border-b border-gray-100 dark:border-gray-800">

                            <span class="text-gray-500">Jadwal Sesi:</span>

                            <span class="font-bold text-gray-900 dark:text-white" x-text="activeContract.booking_date + ' (' + activeContract.booking_time + ')'"></span>

                        </div>

                        <div class="flex justify-between py-1 border-b border-gray-100 dark:border-gray-800">

                            <span class="text-gray-500">Lokasi Acara:</span>

                            <span class="font-bold text-gray-900 dark:text-white text-right max-w-xs" x-text="activeContract.location"></span>

                        </div>

                        <div class="flex justify-between py-1 border-b border-gray-100 dark:border-gray-800">

                            <span class="text-gray-500">Nomor Invoice:</span>

                            <span class="font-bold text-amber-600 dark:text-amber-400 font-mono" x-text="activeContract.external_id"></span>

                        </div>

                        <div class="flex justify-between py-1">

                            <span class="text-gray-500">Status Sesi Booking:</span>

                            <span class="font-bold uppercase text-[11px]" 

                                  :class="{

                                      'text-emerald-600': activeContract.status_booking === 'completed',

                                      'text-blue-600': activeContract.status_booking === 'confirmed',

                                      'text-amber-600': activeContract.status_booking === 'pending',

                                      'text-rose-600': activeContract.status_booking === 'cancelled'

                                  }" 

                                  x-text="activeContract.status_booking"></span>

                        </div>

                    </div>



                    <!-- Galeri Hasil Foto (jika sudah ada) -->

                    <template x-if="activeContract.hasil_foto_url">

                        <div class="p-4 rounded-xl bg-emerald-50/60 dark:bg-emerald-950/40 border border-emerald-200/70 dark:border-emerald-800/50 space-y-2 text-xs">

                            <span class="text-[10px] font-black uppercase tracking-wider text-emerald-700 dark:text-emerald-400 block">Tautan Galeri Hasil Foto</span>

                            <a :href="activeContract.hasil_foto_url" target="_blank" class="inline-flex items-center gap-1.5 font-bold text-emerald-700 dark:text-emerald-300 hover:underline break-all">

                                <span x-text="activeContract.hasil_foto_url"></span>

                            </a>

                            <template x-if="activeContract.catatan_fotografer">

                                <p class="text-gray-600 dark:text-gray-400 italic mt-1" x-text="'Catatan: ' + activeContract.catatan_fotografer"></p>

                            </template>

                        </div>

                    </template>



                    <!-- Bukti Transfer Pembayaran Klien (jika ada) -->

                    <template x-if="activeContract.bukti_transfer">

                        <div class="p-4 rounded-xl bg-blue-50/60 dark:bg-blue-950/40 border border-blue-200/70 dark:border-blue-800/50 space-y-2 text-xs">

                            <div class="flex items-center justify-between">

                                <span class="text-[10px] font-black uppercase tracking-wider text-blue-700 dark:text-blue-400 block">Bukti Transfer Pembayaran Klien</span>

                                <span class="text-[10px] font-bold" :class="activeContract.payment_status === 'completed' ? 'text-emerald-600' : 'text-amber-600'" x-text="activeContract.payment_status === 'completed' ? 'Terverifikasi' : 'Menunggu Verifikasi'"></span>

                            </div>

                            <a :href="activeContract.bukti_transfer" target="_blank" class="block rounded-lg overflow-hidden border border-blue-200 dark:border-blue-700 max-h-48 group relative">

                                <img :src="activeContract.bukti_transfer" alt="Bukti Transfer" class="w-full h-40 object-cover group-hover:scale-102 transition" />

                                <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition text-white font-bold text-xs">

                                    Buka Struk Ukuran Penuh

                                </div>

                            </a>

                        </div>

                    </template>



                    <!-- Info Riwayat Pembatalan Pesanan (jika ada) -->

                    <template x-if="activeContract.cancellation_reason">

                        <div class="p-4 rounded-xl bg-rose-50/80 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/60 space-y-1.5 text-xs text-left">

                            <div class="flex items-center justify-between">

                                <span class="text-[10px] font-black uppercase tracking-wider text-rose-700 dark:text-rose-400">Riwayat Pembatalan Pesanan</span>

                                <span class="text-[10px] font-bold text-gray-500 uppercase" x-text="'Dibatalkan oleh: ' + (activeContract.cancelled_by === 'client' ? 'Klien' : 'Fotografer')"></span>

                            </div>

                            <p class="text-rose-800 dark:text-rose-300 font-medium" x-text="'Alasan: ' + activeContract.cancellation_reason"></p>

                            <template x-if="activeContract.refund_amount">

                                <p class="text-gray-600 dark:text-gray-400 text-[11px]" x-text="'Nominal Refund: ' + activeContract.refund_amount"></p>

                            </template>

                        </div>

                    </template>



                    <!-- Status Escrow & Catatan Admin -->

                    <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-700 space-y-2 text-xs">

                        <span class="text-[10px] font-black uppercase tracking-wider text-gray-400 block">Status Dana</span>

                        <div class="flex items-center justify-between">

                            <span class="text-gray-500">Status Pencairan:</span>

                            <span class="font-bold text-xs" 

                                  :class="{

                                      'text-emerald-600': activeContract.status_payout === 'paid',

                                      'text-blue-600': activeContract.status_payout === 'ready',

                                      'text-amber-600': activeContract.status_payout === 'holding',

                                      'text-rose-600': activeContract.status_payout === 'refunded'

                                  }"

                                  x-text="activeContract.status_payout === 'paid' ? 'Sudah Ditransfer' : (activeContract.status_payout === 'ready' ? 'Siap Dicairkan' : (activeContract.status_payout === 'refunded' ? 'Refund Transaksi' : 'Dana Ditahan'))">

                            </span>

                        </div>

                        <template x-if="activeContract.payout_at">

                            <div class="flex items-center justify-between">

                                <span class="text-gray-500">Waktu Pencairan:</span>

                                <span class="font-bold text-gray-800 dark:text-gray-200" x-text="activeContract.payout_at"></span>

                            </div>

                        </template>

                        <template x-if="activeContract.payout_notes">

                            <div class="pt-2 border-t border-gray-100 dark:border-gray-700/60">

                                <span class="text-gray-500 block mb-1">Catatan Admin:</span>

                                <p class="text-gray-800 dark:text-gray-200 italic" x-text="activeContract.payout_notes"></p>

                            </div>

                        </template>

                    </div>

                </div>

            </template>



            <!-- Modal Footer (Centered) -->

            <div class="flex justify-center pt-3 border-t border-gray-100 dark:border-gray-700/80">

                <button type="button" @click="detailModalOpen = false" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-bold text-xs rounded-xl transition cursor-pointer">

                    Tutup

                </button>

            </div>

        </div>

    </div>



    <!-- 2. Modal Konfirmasi Transfer Dana -->

    <div x-show="payoutModalOpen" 

         x-cloak 

         @keydown.escape.window="payoutModalOpen = false" 

         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"

         x-transition>

        <div @click.away="payoutModalOpen = false" 

             class="bg-white dark:bg-gray-800 max-w-md w-full p-6 sm:p-7 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-2xl space-y-6">

            

            <!-- Modal Title (No Icon, Centered) -->

            <div class="space-y-2 text-center">

                <h3 class="text-base font-black text-gray-900 dark:text-white">Transfer Dana ke Fotografer?</h3>

                <p class="text-xs text-gray-500">

                    Anda akan mentransfer dana sebesar <strong class="text-emerald-600 dark:text-emerald-400" x-text="activeContract?.net_payout_formatted"></strong> ke rekening mitra <strong class="text-gray-900 dark:text-white" x-text="activeContract?.photographer_name"></strong>.

                </p>

            </div>



            <!-- Rincian Rekening Bank Fotografer -->

            <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-900/70 border border-gray-200 dark:border-gray-700/80 space-y-2 text-xs text-left">

                <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Rekening Tujuan Transfer</span>

                <template x-if="activeContract?.photographer_bank_account">

                    <div class="space-y-1">

                        <div class="flex justify-between">

                            <span class="text-gray-500">Nama Bank:</span>

                            <span class="font-bold text-gray-900 dark:text-white uppercase" x-text="activeContract?.photographer_bank_name || '-'"></span>

                        </div>

                        <div class="flex justify-between">

                            <span class="text-gray-500">Nomor Rekening:</span>

                            <span class="font-mono font-black text-gray-900 dark:text-white" x-text="activeContract?.photographer_bank_account"></span>

                        </div>

                        <div class="flex justify-between">

                            <span class="text-gray-500">Atas Nama:</span>

                            <span class="font-bold text-gray-900 dark:text-white" x-text="activeContract?.photographer_bank_holder || activeContract?.photographer_name"></span>

                        </div>

                    </div>

                </template>

                <template x-if="!activeContract?.photographer_bank_account">

                    <p class="text-[11px] font-bold text-amber-600 dark:text-amber-400">

                        Mitra fotografer belum melengkapi informasi nomor rekening bank di profilnya.

                    </p>

                </template>

            </div>



            <form :action="'{{ url('/admin/transactions') }}/' + activeContract?.id + '/release-payout'" method="POST" enctype="multipart/form-data" class="space-y-4">

                @csrf

                <div>

                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Unggah Bukti Struk Transfer Bank (Opsional)</label>

                    <input type="file" name="bukti_transfer_payout" accept="image/*" class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer">

                    <p class="text-[10px] text-gray-400 mt-1">Mendukung format gambar bukti transfer (JPG, PNG, WEBP)</p>

                </div>



                <div>

                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Catatan / Nomor Referensi Transfer</label>

                    <textarea name="payout_notes" x-model="payoutNotes" rows="2" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-400" placeholder="Contoh: Transfer bank BCA via nomor ref TRX123456..."></textarea>

                </div>



                <div class="flex items-center justify-center gap-3 pt-2">

                    <button type="button" @click="payoutModalOpen = false" class="px-5 py-2.5 border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-xs font-bold text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-100 transition cursor-pointer">

                        Batal

                    </button>

                    <button type="submit" class="px-6 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs rounded-xl shadow-xs transition cursor-pointer">

                        Ya, Transfer Dana

                    </button>

                </div>

            </form>

        </div>

    </div>



    <!-- 3. Modal Konfirmasi Refund Dana (Process Refund) -->

    <div x-show="refundModalOpen" 

         x-cloak 

         @keydown.escape.window="refundModalOpen = false" 

         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"

         x-transition>

        <div @click.away="refundModalOpen = false" 

             class="bg-white dark:bg-gray-800 max-w-md w-full p-6 sm:p-7 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-2xl space-y-6">

            

            <!-- Modal Title (No Icon, Centered) -->

            <div class="space-y-2 text-center">

                <h3 class="text-base font-black text-gray-900 dark:text-white">Refund Transaksi ke Klien?</h3>

                <p class="text-xs text-gray-500">

                    Anda akan mengembalikan dana sebesar <strong class="text-rose-600 dark:text-rose-400" x-text="activeContract?.jumlah_formatted"></strong> ke akun Klien <strong class="text-gray-900 dark:text-white" x-text="activeContract?.client_name"></strong>. Sesi kontrak akan dibatalkan.

                </p>

            </div>



            <form :action="'{{ url('/admin/transactions') }}/' + activeContract?.id + '/refund'" method="POST" class="space-y-4">

                @csrf

                <div>

                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Alasan Refund Transaksi <span class="text-rose-500">*</span></label>

                    <textarea name="refund_notes" x-model="refundNotes" required rows="2" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-rose-400" placeholder="Alasan pembatalan dan persetujuan refund dana..."></textarea>

                </div>



                <div class="flex items-center justify-center gap-3 pt-2">

                    <button type="button" @click="refundModalOpen = false" class="px-5 py-2.5 border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-xs font-bold text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-100 transition cursor-pointer">

                        Batal

                    </button>

                    <button type="submit" class="px-6 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs rounded-xl shadow-xs transition cursor-pointer">

                        Ya, Refund Dana

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection