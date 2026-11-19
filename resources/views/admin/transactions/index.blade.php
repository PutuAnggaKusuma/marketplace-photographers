@extends('layouts.admin')



@section('title', 'Monitoring Transaksi - LensMatch')



@section('content')

<div class="space-y-6" x-data="{

    activeTab: 'booking',

    payoutModalOpen: false,

    refundModalOpen: false,

    activeContract: null,

    payoutNotes: '',

    payoutProofError: '',

    payoutProofPreviewUrl: '',

    payoutProofFileName: '',

    payoutProofDragging: false,

    payoutProofLightboxOpen: false,

    refundNotes: '',

    openPayout(c) {

        this.activeContract = c;

        this.payoutNotes = 'Pencairan dana 90% disetujui Admin.';

        this.payoutProofError = '';

        this.payoutProofPreviewUrl = '';

        this.payoutProofFileName = '';

        if (this.$refs.payoutProofInput) this.$refs.payoutProofInput.value = '';

        this.payoutModalOpen = true;

    },

    handlePayoutProofFile(event) {

        this.processPayoutProofFiles(event.target.files);

    },

    handlePayoutProofDrop(event) {

        this.payoutProofDragging = false;

        if (event.dataTransfer && event.dataTransfer.files) {

            const file = event.dataTransfer.files[0];

            if (file && typeof DataTransfer !== 'undefined' && this.$refs.payoutProofInput) {

                const dt = new DataTransfer();

                dt.items.add(file);

                this.$refs.payoutProofInput.files = dt.files;

            }

            this.processPayoutProofFiles(event.dataTransfer.files);

        }

    },

    processPayoutProofFiles(fileList) {

        const res = window.validateUploadFiles(fileList, { maxMB: 10 });

        if (res.hasErrors) {

            this.payoutProofError = res.errorMessage;

            this.payoutProofPreviewUrl = '';

            this.payoutProofFileName = '';

            return;

        }

        const file = res.validFiles[0];

        if (file) {

            this.payoutProofError = '';

            this.payoutProofFileName = `${file.name} (${window.formatFileSizeMB(file.size)})`;

            this.payoutProofPreviewUrl = URL.createObjectURL(file);

        }

    },

    removePayoutProofFile() {

        if (this.$refs.payoutProofInput) this.$refs.payoutProofInput.value = '';

        this.payoutProofError = '';

        this.payoutProofPreviewUrl = '';

        this.payoutProofFileName = '';

    },

    submitPayout(e) {

        if (this.payoutProofError) {

            e.preventDefault();

            this.$refs.payoutProofDropzone?.focus();

            this.$refs.payoutProofDropzone?.scrollIntoView({ behavior: 'smooth', block: 'center' });

            return false;

        }

    },

    openRefund(c) {

        this.activeContract = c;

        this.refundNotes = 'Pengembalian dana (refund) transaksi disetujui Admin.';

        this.refundNotesTouched = false;

        this.refundNotesError = '';

        this.refundModalOpen = true;

    },

    refundNotesTouched: false,

    refundNotesError: '',

    validateRefundNotes() {

        if (!this.refundNotes || !this.refundNotes.trim()) {

            this.refundNotesError = 'Alasan refund transaksi wajib diisi.';

            return false;

        }

        if (this.refundNotes.trim().length < 5) {

            this.refundNotesError = 'Alasan refund minimal 5 karakter.';

            return false;

        }

        this.refundNotesError = '';

        return true;

    },

    submitRefund(e) {

        this.refundNotesTouched = true;

        if (!this.validateRefundNotes()) {

            e.preventDefault();

            this.$refs.refundNotesInput?.focus();

            return false;

        }

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

            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Total Omzet Masuk</span>

            <h2 class="text-xs font-black text-gray-900 dark:text-white tracking-tight">Rp {{ number_format($totalGrossVolume, 0, ',', '.') }}</h2>

        </div>



        <!-- Card 2: Dana Ditahan -->

        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">

            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Dana Ditahan</span>

            <h2 class="text-xs font-black text-gray-900 dark:text-white tracking-tight">Rp {{ number_format($totalEscrowHolding, 0, ',', '.') }}</h2>

        </div>



        <!-- Card 3: Siap Dicairkan (Ready) -->

        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">

            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Siap Dicairkan</span>

            <h2 class="text-xs font-black text-gray-900 dark:text-white tracking-tight">Rp {{ number_format($totalEscrowReady, 0, ',', '.') }}</h2>

        </div>



        <!-- Card 4: Total Dana Ditransfer -->

        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">

            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Total Dana Ditransfer</span>

            <h2 class="text-xs font-black text-gray-900 dark:text-white tracking-tight">Rp {{ number_format($totalPayoutPaid, 0, ',', '.') }}</h2>

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

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm overflow-hidden">

        

        <!-- Filters & Search Toolbar (Alpine.js Custom Floating Dropdown) -->

        <div class="p-4 flex flex-col md:flex-row items-center justify-between gap-4">

            <!-- Filter Dropdowns di Kiri -->

            <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">

                <!-- 1. Dropdown Filter Status Escrow -->

                @php

                    $statusLabels = [

                        'all' => 'Semua Status Transaksi',

                        'holding' => 'Dana Ditahan',

                        'ready' => 'Siap Dicairkan',

                        'paid' => 'Sudah Ditransfer',

                        'pending_payment' => 'Menunggu Pembayaran',

                        'cancelled' => 'Dibatalkan',

                        'expired' => 'Kadaluarsa',

                        'refunded' => 'Refund'

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

                                <div class="text-xs text-gray-400 mt-0.5">{{ $c->created_at->format('d M Y, H:i') }}</div>

                            </td>



                            <!-- Klien -->

                            <td class="py-4 px-4 sm:px-6">

                                <div class="font-bold text-gray-900 dark:text-white">{{ $c->client->nama ?? 'Klien' }}</div>

                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $c->client->user->email ?? '-' }}</div>

                            </td>



                            <!-- Fotografer -->

                            <td class="py-4 px-4 sm:px-6">

                                <div class="font-bold text-gray-900 dark:text-white">{{ $c->photographer->nama ?? 'Fotografer' }}</div>

                                <div class="text-xs text-gray-500 dark:text-gray-400">

                                    {{ $c->bookingDetail->service->nama_layanan ?? 'Paket Foto Custom' }}

                                </div>

                            </td>



                            <!-- Rincian Nominal -->

                            <td class="py-4 px-4 sm:px-6 space-y-1">

                                <div class="font-extrabold text-gray-900 dark:text-white">Total: Rp {{ number_format($c->jumlah, 0, ',', '.') }}</div>

                                <div class="text-xs text-gray-500">

                                    Komisi Platform (10%): <span class="font-bold text-gray-700 dark:text-gray-300">Rp {{ number_format($feeAmount, 0, ',', '.') }}</span>

                                </div>

                                <div class="text-xs font-bold text-gray-900 dark:text-white">

                                    Pendapatan Bersih: Rp {{ number_format($netPayout, 0, ',', '.') }}

                                </div>

                            </td>



                            <!-- Status Pembayaran -->

                            <td class="py-4 px-4 sm:px-6 align-middle whitespace-nowrap">

                                @if($c->status_payout === 'paid')

                                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">

                                        Sudah Ditransfer

                                    </span>

                                    @if($c->payout_at)

                                        <p class="text-xs text-gray-400 mt-0.5">{{ $c->payout_at->format('d M Y, H:i') }}</p>

                                    @endif

                                @elseif($c->status_payout === 'ready')

                                    <span class="text-xs font-bold text-blue-600 dark:text-blue-400">

                                        Siap Dicairkan

                                    </span>

                                @elseif($c->status_payout === 'refunded')

                                    <span class="text-xs font-bold text-rose-600 dark:text-rose-400">

                                        Refund

                                    </span>

                                @elseif($c->status_contract === 'draft' || ($c->status_contract === 'pending' && !$c->is_validated_photographer))

                                    <span class="text-xs font-bold text-amber-600 dark:text-amber-400">

                                        Menunggu Konfirmasi

                                    </span>

                                @elseif($c->status_contract === 'pending_payment')

                                    <span class="text-xs font-bold text-amber-600 dark:text-amber-400">

                                        Menunggu Pembayaran

                                    </span>

                                @elseif($c->status_contract === 'cancelled')
                                    @php
                                        $isExpired = $c->cancellation && (str_contains(strtolower($c->cancellation->alasan), 'kadaluarsa') || str_contains(strtolower($c->cancellation->alasan), 'terlewati'));
                                    @endphp
                                    <span class="text-xs font-bold text-gray-400 dark:text-gray-500">
                                        {{ $isExpired ? 'Kadaluarsa' : 'Dibatalkan' }}
                                    </span>

                                @elseif($c->payments->where('payment_status', 'completed')->isNotEmpty() && ($c->status_payout === 'holding' || $c->status_contract === 'active'))

                                    <span class="text-xs font-bold text-amber-600 dark:text-amber-400">

                                        Dana Ditahan

                                    </span>

                                @else

                                    <span class="text-xs font-bold text-gray-400 dark:text-gray-500">

                                        -

                                    </span>

                                @endif

                            </td>



                            <!-- Aksi -->
                            <td class="py-4 px-4 sm:px-6 align-middle text-center whitespace-nowrap">
                                <div class="flex items-center justify-center">
                                    <a href="{{ route('admin.transactions.show', $c->id) }}" 
                                       class="px-3 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200/80 dark:border-blue-900/60 font-bold text-xs transition min-w-[60px] text-center inline-block cursor-pointer shadow-2xs">
                                        Lihat
                                    </a>
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

                        <th class="py-3.5 px-4 sm:px-6">Invoice dan Tanggal</th>

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

                            <!-- Invoice dan Tanggal -->

                            <td class="py-4 px-4 sm:px-6 align-middle font-mono font-bold text-gray-900 dark:text-white">

                                {{ $el->invoice_code }}

                                <span class="block text-xs font-normal text-gray-400 mt-0.5">

                                    {{ $el->paid_at ? $el->paid_at->format('d M Y, H:i') : $el->created_at->format('d M Y, H:i') }}

                                </span>

                            </td>



                            <!-- Pembeli -->

                            <td class="py-4 px-4 sm:px-6 align-middle">

                                <span class="font-bold text-gray-900 dark:text-white block">{{ $el->user->nama ?? 'Pengguna' }}</span>

                                <span class="text-xs text-gray-500">{{ $el->user->email ?? '-' }}</span>

                            </td>



                            <!-- Kursus E-Learning -->

                            <td class="py-4 px-4 sm:px-6 align-middle">

                                <span class="font-extrabold text-gray-900 dark:text-white block">

                                    {{ $el->elearning->judul ?? 'Kursus Fotografi' }}

                                </span>

                                <span class="text-xs text-gray-500 capitalize">

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

                                <span class="block text-xs text-gray-400 font-medium">Pendapatan Platform</span>

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

                                    <a href="{{ route('public.elearning.show', $el->elearning->slug) }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-bold text-xs shadow-2xs transition inline-block">

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

                        <th class="py-3.5 px-4 sm:px-6">Registrasi Peserta</th>

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

                            <!-- Kode dan Tanggal -->

                            <td class="py-4 px-4 sm:px-6 align-middle font-mono font-bold text-gray-900 dark:text-white">

                                REG-LMB-{{ str_pad($cr->id, 4, '0', STR_PAD_LEFT) }}

                                <span class="block text-xs font-normal text-gray-400 mt-0.5">

                                    {{ $cr->created_at->format('d M Y, H:i') }}

                                </span>

                            </td>



                            <!-- Peserta -->

                            <td class="py-4 px-4 sm:px-6 align-middle">

                                <span class="font-bold text-gray-900 dark:text-white block">{{ $cr->nama_lengkap }}</span>

                                <span class="text-xs text-gray-500">{{ $cr->email }} • {{ $cr->no_wa }}</span>

                            </td>



                            <!-- Event Lomba -->

                            <td class="py-4 px-4 sm:px-6 align-middle">

                                <span class="font-extrabold text-gray-900 dark:text-white block">

                                    {{ $cr->contest->judul_lomba ?? 'Lomba Foto' }}

                                </span>

                                <span class="text-xs text-gray-500">

                                    Penyelenggara: {{ $cr->contest->penyelenggara ?? '-' }}

                                </span>

                            </td>



                            <!-- Biaya Pendaftaran -->

                            <td class="py-4 px-4 sm:px-6 align-middle font-black text-gray-900 dark:text-white whitespace-nowrap">

                                Rp {{ number_format($cr->contest->biaya_pendaftaran ?? 0, 0, ',', '.') }}

                                <span class="block text-xs text-gray-400 font-medium">Pendapatan Platform</span>

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

                                    <a href="{{ route('admin.contests.manage', $cr->contest->id) }}" class="px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-bold text-xs shadow-2xs transition inline-block">

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

    <!-- 2. Modal Konfirmasi Transfer Dana -->
    <template x-teleport="body">
        <div x-show="payoutModalOpen" 
             x-cloak 
             @keydown.escape.window="payoutModalOpen = false" 
             class="fixed inset-0 z-[999] flex items-center justify-center p-4 sm:p-6 bg-black/60 backdrop-blur-xs"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">

            <div @click.away="payoutModalOpen = false" 
                 class="bg-white dark:bg-gray-800 max-w-md w-full p-6 sm:p-7 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-2xl flex flex-col gap-6 max-h-[85vh] overflow-y-auto">
                
                <!-- Modal Title (No Icon, Centered) -->
                <div class="flex flex-col gap-2 text-center">
                    <h3 class="text-base font-black text-gray-900 dark:text-white">Transfer Dana ke Fotografer?</h3>
                    <p class="text-xs text-gray-500">
                        Anda akan mentransfer dana sebesar <strong class="text-emerald-600 dark:text-emerald-400" x-text="activeContract?.net_payout_formatted"></strong> ke rekening mitra <strong class="text-gray-900 dark:text-white" x-text="activeContract?.photographer_name"></strong>.
                    </p>
                </div>

                <!-- Rincian Rekening Bank Fotografer -->
                <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-900/70 border border-gray-200 dark:border-gray-700/80 flex flex-col gap-2 text-xs text-left">
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



            <form :action="'{{ url('/admin/transactions') }}/' + activeContract?.id + '/release-payout'" method="POST" enctype="multipart/form-data" @submit="submitPayout($event)" novalidate class="flex flex-col gap-4">

                @csrf

                <div class="flex flex-col gap-3">

                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">Unggah Bukti Struk Transfer Bank (Opsional)</label>

                    <div x-ref="payoutProofDropzone"
                         tabindex="0"
                         role="button"
                         @click="$refs.payoutProofInput.click()"
                         @keydown.enter.prevent="$refs.payoutProofInput.click()"
                         @keydown.space.prevent="$refs.payoutProofInput.click()"
                         @dragover.prevent="payoutProofDragging = true"
                         @dragleave.prevent="payoutProofDragging = false"
                         @drop.prevent="handlePayoutProofDrop($event)"
                         class="w-full min-h-[120px] px-5 py-6 rounded-2xl border-2 border-dashed flex flex-col items-center justify-center text-center gap-1.5 cursor-pointer transition outline-none"
                         :class="payoutProofDragging
                             ? 'border-amber-500 bg-amber-50/70 dark:bg-amber-950/25'
                             : (payoutProofError
                                 ? 'border-red-400 bg-red-50/40 dark:bg-red-950/20 focus:ring-2 focus:ring-red-400'
                                 : 'border-gray-200 dark:border-gray-700 bg-gray-50/80 dark:bg-gray-900/60 hover:border-amber-400 dark:hover:border-amber-500 focus:ring-2 focus:ring-amber-500')">
                        <input type="file" name="bukti_transfer_payout" x-ref="payoutProofInput" accept="image/jpeg,image/png,image/webp" @change="handlePayoutProofFile($event)" class="sr-only">
                        <svg class="w-6 h-6 transition-colors" :class="payoutProofDragging ? 'text-amber-500' : 'text-gray-400 dark:text-gray-500'" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                        </svg>
                        <p class="text-xs font-bold text-gray-700 dark:text-gray-200">
                            Seret dan lepas di sini
                        </p>
                        <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">Format JPG, PNG, WEBP maksimal 10 MB.</p>
                    </div>

                    <template x-if="payoutProofPreviewUrl">
                        <div class="rounded-xl overflow-hidden bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 max-w-xs mx-auto shadow-2xs">
                            <div @click="payoutProofLightboxOpen = true" class="relative aspect-[4/3] w-full overflow-hidden bg-gray-100 dark:bg-gray-900 cursor-pointer group">
                                <img :src="payoutProofPreviewUrl" :alt="payoutProofFileName" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                <div class="absolute inset-0 bg-black/45 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                    <span class="text-xs font-bold text-white tracking-wide">Pratinjau</span>
                                </div>
                            </div>
                            <div class="px-3 py-2 flex items-center justify-between gap-2 border-t border-gray-100 dark:border-gray-700/80">
                                <span class="text-[11px] font-bold text-gray-700 dark:text-gray-300 truncate" x-text="payoutProofFileName"></span>
                                <button type="button" @click="removePayoutProofFile()" class="text-[11px] font-bold text-rose-600 dark:text-rose-400 hover:underline shrink-0 cursor-pointer">Hapus</button>
                            </div>
                        </div>
                    </template>

                    <p x-show="payoutProofError" x-text="payoutProofError" x-cloak class="text-[11px] font-bold text-red-500"></p>

                    @error('bukti_transfer_payout')

                        <p class="text-[11px] font-bold text-red-500">{{ $message }}</p>

                    @enderror

                </div>



                <div>

                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Catatan / Nomor Referensi Transfer</label>

                    <textarea name="payout_notes" x-model="payoutNotes" rows="2" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-400" placeholder="Masukkan catatan atau nomor referensi transfer bank..."></textarea>

                </div>



                <div class="flex items-center justify-center gap-3">

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
    </template>



    <!-- 3. Modal Konfirmasi Refund Dana (Process Refund) -->

    <template x-teleport="body">
    <div x-show="refundModalOpen" 
         x-cloak 
         @keydown.escape.window="refundModalOpen = false" 
         class="fixed inset-0 z-[999] flex items-center justify-center p-4 sm:p-6 bg-black/60 backdrop-blur-xs"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <div @click.away="refundModalOpen = false" 
             class="bg-white dark:bg-gray-800 max-w-md w-full p-6 sm:p-7 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-2xl flex flex-col gap-6 max-h-[85vh] overflow-y-auto">

            

            <!-- Modal Title (No Icon, Centered) -->

            <div class="flex flex-col gap-2 text-center">

                <h3 class="text-base font-black text-gray-900 dark:text-white">Refund Transaksi ke Klien?</h3>

                <p class="text-xs text-gray-500">

                    Anda akan mengembalikan dana sebesar <strong class="text-rose-600 dark:text-rose-400" x-text="activeContract?.jumlah_formatted"></strong> ke akun Klien <strong class="text-gray-900 dark:text-white" x-text="activeContract?.client_name"></strong>. Sesi kontrak akan dibatalkan.

                </p>

            </div>



            <form :action="'{{ url('/admin/transactions') }}/' + activeContract?.id + '/refund'" method="POST" @submit="submitRefund($event)" novalidate class="flex flex-col gap-4">

                @csrf

                <div>

                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Alasan Refund Transaksi <span class="text-rose-500">*</span></label>

                    <textarea name="refund_notes" 
                              x-ref="refundNotesInput"
                              x-model="refundNotes" 
                              @input="refundNotesTouched = true; validateRefundNotes()"
                              @blur="refundNotesTouched = true; validateRefundNotes()"
                              rows="2" 
                              class="w-full px-3.5 py-2.5 text-xs rounded-xl border bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-white outline-none focus:ring-2 transition" 
                              :class="refundNotesTouched && refundNotesError ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-rose-400 focus:border-rose-400'"
                              placeholder="Alasan pembatalan dan persetujuan refund dana..."></textarea>
                    <p x-show="refundNotesTouched && refundNotesError" x-text="refundNotesError" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>

                </div>



                <div class="flex items-center justify-center gap-3">

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
    </template>

    <!-- Lightbox Pratinjau Bukti Transfer Payout (Rule #97) -->
    <template x-teleport="body">
    <div x-show="payoutProofLightboxOpen"
         x-cloak
         @keydown.escape.window="payoutProofLightboxOpen = false"
         @click.self="payoutProofLightboxOpen = false"
         class="fixed inset-0 z-[9999] bg-black/90 backdrop-blur-md flex flex-col items-center justify-center p-4 sm:p-8">
        <button type="button"
                @click="payoutProofLightboxOpen = false"
                aria-label="Tutup pratinjau"
                class="absolute top-4 right-4 sm:top-6 sm:right-6 w-11 h-11 rounded-full bg-white/10 hover:bg-white/25 text-white flex items-center justify-center transition cursor-pointer">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <img :src="payoutProofPreviewUrl" :alt="payoutProofFileName" class="max-w-full max-h-[82vh] object-contain rounded-2xl shadow-2xl border border-white/10 select-none">
    </div>
    </template>

</div>

@endsection