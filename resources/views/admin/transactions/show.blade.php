@extends('layouts.admin')

@section('title', 'Detail Transaksi — Admin LensMatch')

@section('content')
<div class="space-y-6"
     x-data="{
         proofLightboxOpen: false,
         rejectModalOpen: false,
         payoutModalOpen: false,
         refundModalOpen: false,
         payoutProofDragging: false,
         payoutProofPreview: null,
         handlePayoutProofFile(e) {
             const file = e.target.files[0];
             if (file) {
                 if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
                     alert('Format file tidak didukung. Harap pilih gambar JPG, PNG, atau WEBP.');
                     e.target.value = '';
                     return;
                 }
                 if (file.size > 10 * 1024 * 1024) {
                     alert('Ukuran file terlalu besar. Maksimal 10 MB.');
                     e.target.value = '';
                     return;
                 }
                 const reader = new FileReader();
                 reader.onload = (ev) => {
                     this.payoutProofPreview = ev.target.result;
                 };
                 reader.readAsDataURL(file);
             }
         },
         handlePayoutProofDrop(e) {
             this.payoutProofDragging = false;
             if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                 const file = e.dataTransfer.files[0];
                 const input = this.$refs.payoutProofInput;
                 const dt = new DataTransfer();
                 dt.items.add(file);
                 input.files = dt.files;
                 this.handlePayoutProofFile({ target: input });
             }
         }
     }">

    @php
        $pendingPayment = $contract->payments->where('payment_status', 'pending')->first();
        $transferProofPayment = $contract->payments->first(fn($p) => !empty($p->bukti_transfer));
        $isPayoutReady = $contract->status_payout === 'ready';
        $isPayoutPaid = $contract->status_payout === 'paid';
        $canRefund = $contract->payments->where('payment_status', 'completed')->isNotEmpty() 
            && $contract->status_contract === 'cancelled' 
            && $contract->status_payout !== 'refunded';
        $isRefunded = $contract->status_payout === 'refunded';

        $isCancelled = $contract->status_contract === 'cancelled';
        $isDpPaid = isset($dpPayment) && $dpPayment && $dpPayment->payment_status === 'completed';
        $isPelunasanPaid = isset($fullPayment) && $fullPayment && $fullPayment->payment_status === 'completed';
        $isAllPaid = !$isCancelled && (($contract->payment_type === 'dp') ? ($isDpPaid && $isPelunasanPaid) : ($activePayment && $activePayment->payment_status === 'completed'));
    @endphp

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <nav class="flex items-center gap-1.5 text-xs text-gray-400 dark:text-gray-500 mb-1.5">
                <a href="{{ route('admin.transactions') }}" class="hover:text-amber-500 transition">Monitoring Transaksi</a>
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                <span>Detail Transaksi</span>
            </nav>
            <h1 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">
                Detail Transaksi
            </h1>
        </div>

        <div class="flex items-center gap-2.5 shrink-0">
            <a href="{{ route('booking.invoice.print', $contract->id) }}" 
               target="_blank" 
               class="px-4 py-2 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-950 font-bold text-xs shadow-xs transition inline-flex items-center gap-1.5 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24-1.284-.36-2.607-.36-3.954 0-4.008 2.224-7.587 5.64-9.375m2.72 9.375c.24-1.284.36-2.607.36-3.954 0-4.008-2.224-7.587-5.64-9.375M3 18.75h18M3 18.75a2.25 2.25 0 002.25 2.25h13.5A2.25 2.25 0 0021 18.75m-18 0v-4.5m18 4.5v-4.5m-18 0h18"/>
                </svg>
                <span>Cetak / Simpan PDF</span>
            </a>

            <a href="{{ route('admin.transactions') }}" 
               class="px-4 py-2 rounded-xl bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 font-bold text-xs transition inline-flex items-center gap-1.5 shadow-2xs">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <!-- Flash Session Alerts -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 flex items-center gap-3 text-xs text-emerald-800 dark:text-emerald-300 font-bold">
            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/60 flex items-center gap-3 text-xs text-rose-800 dark:text-rose-300 font-bold">
            <svg class="w-5 h-5 text-rose-600 dark:text-rose-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Panel Kontrol Transaksi -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 sm:p-7 border border-gray-200/80 dark:border-gray-700/80 shadow-xs space-y-6">
        
        <!-- Header Panel Kontrol -->
        <div class="border-b border-gray-100 dark:border-gray-700/80 pb-4">
            <h2 class="text-base sm:text-lg font-black text-gray-900 dark:text-white tracking-tight">Panel Pengawasan Transaksi</h2>
            <p class="text-xs text-gray-500 dark:text-gray-400">Rincian komisi platform, rekening mitra fotografer, verifikasi bayar, dan kontrol pencairan dana.</p>
        </div>

        <!-- 3 Column Metric Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-xs">
            
            <!-- Kolom 1: Finansial Platform -->
            <div class="space-y-3 p-4 rounded-2xl bg-gray-50/70 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-700/60">
                <span class="text-[10px] font-black uppercase tracking-wider text-gray-400 dark:text-gray-500 block">Finansial Platform</span>
                <div class="space-y-2">
                    <div class="flex justify-between items-center text-gray-600 dark:text-gray-400">
                        <span>Total Pembayaran:</span>
                        <span class="font-bold text-gray-900 dark:text-white font-mono">Rp {{ number_format($contract->jumlah, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-gray-600 dark:text-gray-400">
                        <span>Komisi Platform (10%):</span>
                        <span class="font-bold text-gray-900 dark:text-white font-mono">Rp {{ number_format($feeAmount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-gray-900 dark:text-white font-extrabold pt-2 border-t border-gray-200 dark:border-gray-700">
                        <span>Pendapatan Bersih Mitra:</span>
                        <span class="text-emerald-600 dark:text-emerald-400 font-mono text-sm">Rp {{ number_format($netPayout, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Kolom 2: Rekening Bank Fotografer -->
            <div class="space-y-3 p-4 rounded-2xl bg-gray-50/70 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-700/60">
                <span class="text-[10px] font-black uppercase tracking-wider text-gray-400 dark:text-gray-500 block">Rekening Bank Fotografer</span>
                <div class="space-y-1.5">
                    <p class="text-xs font-extrabold text-gray-900 dark:text-white uppercase">
                        {{ $contract->photographer->nama_bank ?? 'Bank Belum Diatur' }}
                    </p>
                    <p class="font-mono text-xs font-bold text-gray-800 dark:text-gray-200">
                        {{ $contract->photographer->nomor_rekening ?? '-' }}
                    </p>
                    <p class="text-gray-500 dark:text-gray-400 text-[11px]">
                        a.n. {{ $contract->photographer->atas_nama_rekening ?? ($contract->photographer->nama ?? '-') }}
                    </p>
                </div>
            </div>

            <!-- Kolom 3: Status Operasional -->
            <div class="space-y-3 p-4 rounded-2xl bg-gray-50/70 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-700/60">
                <span class="text-[10px] font-black uppercase tracking-wider text-gray-400 dark:text-gray-500 block">Status Operasional</span>
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 dark:text-gray-400">Status Dana:</span>
                        @if($isPayoutPaid)
                            <span class="font-bold text-emerald-600 dark:text-emerald-400">Sudah Ditransfer</span>
                        @elseif($isPayoutReady)
                            <span class="font-bold text-blue-600 dark:text-blue-400">Siap Ditransfer</span>
                        @elseif($isRefunded)
                            <span class="font-bold text-rose-600 dark:text-rose-400">Dana Dikembalikan</span>
                        @elseif($contract->status_contract === 'cancelled')
                            <span class="font-bold text-gray-500 dark:text-gray-400">Dibatalkan</span>
                        @elseif($contract->payments->where('payment_status', 'completed')->isNotEmpty())
                            <span class="font-bold text-amber-600 dark:text-amber-400">Dana Ditahan</span>
                        @else
                            <span class="font-bold text-gray-500 dark:text-gray-400">Menunggu Pembayaran</span>
                        @endif
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 dark:text-gray-400">Status Sesi:</span>
                        @php
                            $bookingStatus = $contract->bookingDetail->status_booking ?? 'pending';
                        @endphp
                        <span class="font-bold {{ $bookingStatus === 'completed' ? 'text-emerald-600 dark:text-emerald-400' : ($bookingStatus === 'confirmed' ? 'text-blue-600 dark:text-blue-400' : ($bookingStatus === 'cancelled' ? 'text-rose-600 dark:text-rose-400' : 'text-amber-600 dark:text-amber-400')) }}">
                            {{ $bookingStatus === 'completed' ? 'Selesai' : ($bookingStatus === 'confirmed' ? 'Dikonfirmasi' : ($bookingStatus === 'cancelled' ? 'Dibatalkan' : 'Menunggu Konfirmasi')) }}
                        </span>
                    </div>

                    @if($contract->cancellation)
                        <div class="pt-2 border-t border-gray-200 dark:border-gray-700 text-[11px] space-y-0.5">
                            <span class="text-rose-600 dark:text-rose-400 font-bold block">
                                Dibatalkan: {{ $contract->cancellation->alasan }}
                            </span>
                            @if($contract->cancellation->refund_amount > 0)
                                <span class="text-gray-500 dark:text-gray-400 block">
                                    Pengembalian: Rp {{ number_format($contract->cancellation->refund_amount, 0, ',', '.') }}
                                </span>
                            @endif
                        </div>
                    @endif

                    @if($contract->payout_at)
                        <div class="pt-1 text-[11px] text-gray-500 dark:text-gray-400">
                            Waktu Transfer: {{ $contract->payout_at->format('d M Y, H:i') }} WIB
                        </div>
                    @endif
                </div>
            </div>

        </div>

        <!-- Bagian Bukti Transfer dan Kontrol Aksi Cepat -->
        <div class="border-t border-gray-100 dark:border-gray-700/80 pt-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
            
            <!-- Bukti Transfer Thumbnail -->
            <div class="flex items-center gap-3.5">
                @if($transferProofPayment && $transferProofPayment->bukti_transfer)
                    <div class="relative group cursor-pointer w-24 h-16 rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 shrink-0 shadow-2xs"
                         @click="proofLightboxOpen = true">
                        <img src="{{ $transferProofPayment->bukti_transfer }}" alt="Bukti Transfer" class="w-full h-full object-cover group-hover:scale-105 transition">
                        <div class="absolute inset-0 bg-black/45 flex items-center justify-center opacity-0 group-hover:opacity-100 transition text-[10px] text-white font-bold">
                            Perbesar
                        </div>
                    </div>
                    <div class="space-y-0.5">
                        <span class="text-xs font-bold text-gray-900 dark:text-white block">Bukti Transfer Klien</span>
                        <span class="text-[11px] font-semibold {{ $transferProofPayment->payment_status === 'completed' ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">
                            {{ $transferProofPayment->payment_status === 'completed' ? 'Terverifikasi' : 'Menunggu Verifikasi Admin' }}
                        </span>
                        <p class="text-[10px] text-gray-400">Klik gambar untuk melihat resolusi penuh</p>
                    </div>
                @else
                    <div class="flex items-center gap-2 text-xs text-gray-400 dark:text-gray-500 italic">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                        </svg>
                        <span>Tidak ada berkas bukti transfer manual.</span>
                    </div>
                @endif
            </div>

            <!-- Tombol Aksi Admin -->
            <div class="flex flex-wrap items-center gap-2.5">
                
                @if($pendingPayment && $pendingPayment->bukti_transfer && $contract->status_contract !== 'cancelled')
                    <!-- Verifikasi Bayar Form -->
                    <form action="{{ route('admin.transactions.verify-payment', $contract->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin verifikasi pembayaran ini sudah valid dan dana telah masuk ke rekening resmi LensMatch?')">
                        @csrf
                        <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition shadow-2xs cursor-pointer inline-flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                            <span>Verifikasi Bayar</span>
                        </button>
                    </form>

                    <!-- Tolak Bukti Bayar Button -->
                    <button type="button" @click="rejectModalOpen = true" class="px-4 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300 border border-rose-200 dark:border-rose-900/60 font-bold text-xs transition cursor-pointer inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span>Tolak Bukti Bayar</span>
                    </button>
                @endif

                @if($isPayoutReady)
                    <button type="button" @click="payoutModalOpen = true" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition shadow-2xs cursor-pointer inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>
                        <span>Transfer Dana Fotografer</span>
                    </button>
                @endif

                @if($canRefund)
                    <button type="button" @click="refundModalOpen = true" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs transition shadow-2xs cursor-pointer inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/></svg>
                        <span>Proses Refund</span>
                    </button>
                @endif

                @if($isPayoutPaid)
                    <span class="px-3.5 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 font-bold text-xs border border-emerald-200/80 dark:border-emerald-800/60 inline-flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        Transfer Selesai
                    </span>
                @endif

                @if($isRefunded)
                    <span class="px-3.5 py-1.5 rounded-xl bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300 font-bold text-xs border border-rose-200/80 dark:border-rose-800/60 inline-flex items-center gap-1.5">
                        Refund Selesai
                    </span>
                @endif

            </div>

        </div>

    </div>

    <!-- Lembar Dokumen Resmi Invoice Pembayaran (Document Sheet Card) -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 sm:p-10 border border-gray-200/80 dark:border-gray-700/80 shadow-xs space-y-8">
        
        <!-- Header Dokumen Invoice -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 border-b-2 border-gray-100 dark:border-gray-700 pb-8">
            <div class="space-y-1">
                <span class="text-xl sm:text-2xl font-black tracking-tight text-gray-900 dark:text-white">
                    Lens<span class="text-amber-500">Match</span>
                </span>
                <p class="text-xs text-gray-500 dark:text-gray-400">Platform Marketplace Fotografi Indonesia</p>
                <p class="text-[11px] text-gray-400 dark:text-gray-500">Website: lensmatch.id • Dukungan: support@lensmatch.id</p>
            </div>

            <div class="text-left sm:text-right space-y-1">
                <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Invoice</h1>
                <p class="text-xs font-mono font-bold text-gray-500 dark:text-gray-400">{{ $activePayment->external_id ?? ('KTRK-' . str_pad($contract->id, 5, '0', STR_PAD_LEFT)) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Tanggal Terbit: {{ $contract->created_at->format('d F Y, H:i') }} WIB</p>
            </div>
        </div>

        <!-- Status Transaksi Ribbon -->
        <div class="p-4 rounded-2xl flex items-center justify-between {{ $isCancelled ? 'bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900/60' : ($isAllPaid ? 'bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-900/60' : ($contract->payment_type === 'dp' && $isDpPaid ? 'bg-blue-50 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-900/60' : 'bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/60')) }}">
            <div class="space-y-0.5">
                <span class="text-[10px] font-black uppercase tracking-wider block {{ $isCancelled ? 'text-rose-700 dark:text-rose-400' : ($isAllPaid ? 'text-emerald-700 dark:text-emerald-400' : ($contract->payment_type === 'dp' && $isDpPaid ? 'text-blue-700 dark:text-blue-400' : 'text-amber-700 dark:text-amber-400')) }}">
                    Status Transaksi
                </span>
                <p class="text-xs font-bold text-gray-800 dark:text-gray-200">
                    @if($isCancelled)
                        Kadaluarsa — Batas Waktu Pembayaran Telah Berakhir
                    @elseif($isAllPaid)
                        Lunas — Pembayaran Telah Terverifikasi
                    @elseif($contract->payment_type === 'dp' && $isDpPaid)
                        Uang Muka (DP 30%) Lunas — Sesi Terkonfirmasi, Menunggu Pelunasan
                    @else
                        Menunggu Pembayaran Transfer
                    @endif
                </p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider {{ $isCancelled ? 'bg-rose-600 text-white' : ($isAllPaid ? 'bg-emerald-600 text-white' : ($contract->payment_type === 'dp' && $isDpPaid ? 'bg-blue-600 text-white' : 'bg-amber-500 text-gray-950')) }}">
                {{ $isCancelled ? 'Kadaluarsa' : ($isAllPaid ? 'Lunas' : ($contract->payment_type === 'dp' && $isDpPaid ? 'DP Lunas' : 'Menunggu')) }}
            </span>
        </div>

        <!-- 2 Kolom Meta: Klien vs Fotografer -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 text-xs border-b border-gray-100 dark:border-gray-700 pb-8">
            <!-- Pihak Klien -->
            <div class="space-y-1.5">
                <span class="text-[10px] font-black uppercase tracking-wider text-gray-400 dark:text-gray-500 block">DITAGIHKAN KEPADA (KLIEN)</span>
                <p class="text-sm font-extrabold text-gray-900 dark:text-white">{{ $contract->client->nama ?? 'Klien' }}</p>
                <p class="text-gray-600 dark:text-gray-400">Email: {{ $contract->client->user->email ?? '-' }}</p>
                <p class="text-gray-600 dark:text-gray-400">WhatsApp / Telp: {{ $contract->client->nomor_telepon ?? '-' }}</p>
            </div>

            <!-- Pihak Fotografer Mitra -->
            <div class="space-y-1.5 sm:text-right">
                <span class="text-[10px] font-black uppercase tracking-wider text-gray-400 dark:text-gray-500 block">PENYEDIA JASA (FOTOGRAFER)</span>
                <p class="text-sm font-extrabold text-gray-900 dark:text-white">{{ $contract->photographer->nama ?? 'Mitra Fotografer' }}</p>
                <p class="text-gray-600 dark:text-gray-400">Lokasi: {{ $contract->photographer->city->name ?? '-' }}, {{ $contract->photographer->province->name ?? '-' }}</p>
                <p class="text-gray-600 dark:text-gray-400">Email: {{ $contract->photographer->user->email ?? '-' }}</p>
            </div>
        </div>

        <!-- Rincian Layanan dan Sesi Booking -->
        <div class="space-y-3">
            <span class="text-[10px] font-black uppercase tracking-wider text-gray-400 dark:text-gray-500 block">RINCIAN SESI PEMOTRETAN</span>
            <div class="bg-gray-50/80 dark:bg-gray-900/60 rounded-2xl p-4 border border-gray-100 dark:border-gray-700 grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                <div>
                    <span class="text-gray-400 dark:text-gray-500 block text-[10px]">Paket Layanan:</span>
                    <strong class="font-bold text-gray-900 dark:text-white">{{ $contract->bookingDetail->service->nama_layanan ?? 'Paket Fotografi' }}</strong>
                </div>
                <div>
                    <span class="text-gray-400 dark:text-gray-500 block text-[10px]">Tanggal Sesi:</span>
                    <strong class="font-bold text-gray-900 dark:text-white">{{ $contract->bookingDetail ? $contract->bookingDetail->date_range_label : '-' }}</strong>
                </div>
                <div>
                    <span class="text-gray-400 dark:text-gray-500 block text-[10px]">Waktu Pemotretan:</span>
                    @php
                        $prtStart = $contract->bookingDetail?->jam_mulai ? substr($contract->bookingDetail->jam_mulai, 0, 5) : null;
                        $prtEnd = $contract->bookingDetail?->jam_selesai ? substr($contract->bookingDetail->jam_selesai, 0, 5) : null;
                    @endphp
                    <strong class="font-bold text-gray-900 dark:text-white">{{ $prtStart ? ($prtEnd ? "{$prtStart} - {$prtEnd} WIB" : "{$prtStart} WIB") . ' (' . ($contract->bookingDetail->effective_duration_hours ?? 1) . ' Jam)' : '-' }}</strong>
                </div>
                <div>
                    <span class="text-gray-400 dark:text-gray-500 block text-[10px]">Lokasi Acara:</span>
                    <strong class="font-bold text-gray-900 dark:text-white truncate block">{{ $contract->bookingDetail->lokasi ?? '-' }}</strong>
                </div>
            </div>
        </div>

        <!-- Tabel Perhitungan Tagihan -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b-2 border-gray-200 dark:border-gray-700 text-[10px] font-black uppercase tracking-wider text-gray-400 dark:text-gray-500">
                        <th class="py-3 pr-4">Deskripsi Item</th>
                        <th class="py-3 px-4 text-center">Tipe Pembayaran</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 pl-4 text-right">Jumlah (IDR)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
                    <!-- Paket Utama -->
                    <tr>
                        <td class="py-3.5 pr-4">
                            <strong class="font-bold text-gray-900 dark:text-white block">{{ $contract->bookingDetail->service->nama_layanan ?? 'Paket Foto' }}</strong>
                            <span class="text-xs text-gray-500 dark:text-gray-400">Sesi pemotretan resolusi tinggi oleh {{ $contract->photographer->nama ?? 'Mitra Fotografer' }}</span>
                        </td>
                        <td class="py-3.5 px-4 text-center uppercase font-bold text-gray-600 dark:text-gray-300">
                            {{ $contract->payment_type === 'dp' ? 'Uang Muka 30%' : 'Bayar Penuh 100%' }}
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            @if($isAllPaid)
                                <span class="font-bold text-emerald-600 dark:text-emerald-400">Lunas</span>
                            @elseif($contract->payment_type === 'dp' && $isDpPaid)
                                <span class="font-bold text-blue-600 dark:text-blue-400">DP Terbayar</span>
                            @else
                                <span class="font-bold text-amber-600 dark:text-amber-400">Belum Bayar</span>
                            @endif
                        </td>
                        <td class="py-3.5 pl-4 text-right font-bold text-gray-900 dark:text-white font-mono">
                            Rp {{ number_format($contract->jumlah, 0, ',', '.') }}
                        </td>
                    </tr>

                    @if($contract->payment_type === 'dp')
                        <!-- Rincian DP -->
                        <tr class="bg-gray-50/50 dark:bg-gray-900/30">
                            <td class="py-2.5 pr-4 pl-4 text-gray-600 dark:text-gray-400 font-medium" colspan="2">
                                ↳ Termin 1: Uang Muka (DP 30%)
                            </td>
                            <td class="py-2.5 px-4 text-center font-bold {{ $isDpPaid ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">
                                {{ $isDpPaid ? 'Lunas' : 'Pending' }}
                            </td>
                            <td class="py-2.5 pl-4 text-right font-mono font-bold text-gray-800 dark:text-gray-200">
                                Rp {{ number_format($contract->jumlah_dp, 0, ',', '.') }}
                            </td>
                        </tr>
                        <!-- Rincian Pelunasan -->
                        <tr class="bg-gray-50/50 dark:bg-gray-900/30">
                            <td class="py-2.5 pr-4 pl-4 text-gray-600 dark:text-gray-400 font-medium" colspan="2">
                                ↳ Termin 2: Pelunasan Sisa (70%)
                            </td>
                            <td class="py-2.5 px-4 text-center font-bold {{ $isPelunasanPaid ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">
                                {{ $isPelunasanPaid ? 'Lunas' : 'Menunggu' }}
                            </td>
                            <td class="py-2.5 pl-4 text-right font-mono font-bold text-gray-800 dark:text-gray-200">
                                Rp {{ number_format(max(0, $contract->jumlah - $contract->jumlah_dp), 0, ',', '.') }}
                            </td>
                        </tr>
                    @endif

                    <!-- Biaya Layanan Platform -->
                    <tr>
                        <td class="py-3 pr-4 text-gray-600 dark:text-gray-400" colspan="2">
                            Biaya Layanan Platform
                        </td>
                        <td class="py-3 px-4 text-center font-bold text-gray-700 dark:text-gray-300">GRATIS</td>
                        <td class="py-3 pl-4 text-right font-mono font-bold text-gray-900 dark:text-white">Rp 0</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-gray-900 dark:border-gray-100">
                        <td colspan="3" class="pt-4 text-right font-black text-sm uppercase text-gray-900 dark:text-white">Total Nilai Kontrak:</td>
                        <td class="pt-4 text-right font-black text-base text-gray-900 dark:text-white font-mono">
                            Rp {{ number_format($contract->jumlah, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Catatan Dokumen -->
        <div class="pt-6 border-t border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left text-xs text-gray-500 dark:text-gray-400">
            <div class="space-y-1 max-w-lg">
                <p class="font-bold text-gray-800 dark:text-gray-200">Keterangan:</p>
                <p class="text-[11px] leading-relaxed text-gray-500 dark:text-gray-400">
                    Dokumen ini merupakan bukti tagihan sah yang diterbitkan secara elektronik oleh sistem LensMatch untuk keperluan monitoring transaksi dan audit internal.
                </p>
            </div>
            <div class="text-right text-[11px] text-gray-400 dark:text-gray-500">
                <p class="font-medium text-gray-600 dark:text-gray-300">LensMatch Indonesia</p>
                <p>Dokumen Digital Resmi</p>
            </div>
        </div>

    </div>

    <!-- Modal Tolak Bukti Transfer (Rule #59 Compliant) -->
    <template x-teleport="body">
        <div x-show="rejectModalOpen"
             x-cloak
             @keydown.escape.window="rejectModalOpen = false"
             class="fixed inset-0 z-[999] flex items-center justify-center p-4 sm:p-6 bg-black/60 backdrop-blur-xs"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <div @click.away="rejectModalOpen = false" 
                 class="bg-white dark:bg-gray-800 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-200 dark:border-gray-700 flex flex-col gap-5 text-center">
                <div class="space-y-1">
                    <h3 class="text-base font-black text-gray-900 dark:text-white">Tolak Bukti Transfer</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Klien akan menerima notifikasi untuk mengunggah ulang bukti pembayaran yang sah.</p>
                </div>
                <form action="{{ route('admin.transactions.reject-payment', $contract->id) }}" method="POST" class="space-y-4 text-left">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Alasan Penolakan</label>
                        <textarea name="alasan" rows="3" required placeholder="Tuliskan alasan penolakan bukti transfer secara jelas..." class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-400 resize-none font-medium"></textarea>
                    </div>
                    <div class="flex items-center justify-center gap-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                        <button type="button" @click="rejectModalOpen = false" class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 hover:bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 text-xs font-bold text-gray-700 dark:text-gray-300 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs transition shadow-2xs cursor-pointer">
                            Tolak Bukti Bayar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    <!-- Modal Transfer Dana Fotografer (Rule #59 Compliant) -->
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
                 class="bg-white dark:bg-gray-800 rounded-2xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-gray-200 dark:border-gray-700 flex flex-col gap-5 text-center max-h-[85vh] overflow-y-auto">
                <div class="space-y-1">
                    <h3 class="text-base font-black text-gray-900 dark:text-white">Transfer Dana ke Fotografer</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Transfer dana sebesar <strong class="text-emerald-600 dark:text-emerald-400 font-mono">Rp {{ number_format($netPayout, 0, ',', '.') }}</strong> ke rekening mitra <strong class="text-gray-900 dark:text-white">{{ $contract->photographer->nama ?? 'Fotografer' }}</strong>.
                    </p>
                </div>
                
                <!-- Rekening Info -->
                <div class="p-3.5 rounded-xl bg-gray-50 dark:bg-gray-900/70 border border-gray-200 dark:border-gray-700 text-xs text-left space-y-1.5">
                    <span class="text-[10px] font-black uppercase tracking-wider text-gray-400 block">Rekening Tujuan Transfer</span>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Nama Bank:</span>
                        <span class="font-bold text-gray-900 dark:text-white uppercase">{{ $contract->photographer->nama_bank ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Nomor Rekening:</span>
                        <span class="font-mono font-bold text-gray-900 dark:text-white">{{ $contract->photographer->nomor_rekening ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Atas Nama:</span>
                        <span class="font-bold text-gray-900 dark:text-white">{{ $contract->photographer->atas_nama_rekening ?? ($contract->photographer->nama ?? '-') }}</span>
                    </div>
                </div>

                <form action="{{ route('admin.transactions.release-payout', $contract->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-left">
                    @csrf
                    
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Unggah Bukti Struk Transfer Bank (Opsional)</label>
                        <div tabindex="0"
                             role="button"
                             @click="$refs.payoutProofInput.click()"
                             @keydown.enter.prevent="$refs.payoutProofInput.click()"
                             @keydown.space.prevent="$refs.payoutProofInput.click()"
                             @dragover.prevent="payoutProofDragging = true"
                             @dragleave.prevent="payoutProofDragging = false"
                             @drop.prevent="handlePayoutProofDrop($event)"
                             class="w-full min-h-[110px] px-4 py-5 rounded-2xl border-2 border-dashed flex flex-col items-center justify-center text-center gap-1.5 cursor-pointer transition outline-none"
                             :class="payoutProofDragging
                                 ? 'border-amber-500 bg-amber-50/70 dark:bg-amber-950/25'
                                 : 'border-gray-200 dark:border-gray-700 bg-gray-50/80 dark:bg-gray-900/60 hover:border-amber-400'">
                            <input type="file" name="bukti_transfer_payout" x-ref="payoutProofInput" accept="image/jpeg,image/png,image/webp" @change="handlePayoutProofFile($event)" class="sr-only">
                            
                            <template x-if="payoutProofPreview">
                                <div class="space-y-1">
                                    <img :src="payoutProofPreview" class="h-16 w-auto mx-auto rounded-lg object-contain shadow-xs">
                                    <p class="text-[11px] text-emerald-600 dark:text-emerald-400 font-bold">Struk terpilih (klik untuk mengganti)</p>
                                </div>
                            </template>
                            
                            <template x-if="!payoutProofPreview">
                                <div class="space-y-1">
                                    <svg class="w-6 h-6 mx-auto text-gray-400" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                                    </svg>
                                    <p class="text-xs font-bold text-gray-700 dark:text-gray-300">Pilih gambar atau tarik ke sini</p>
                                    <p class="text-[10px] text-gray-400">JPG, PNG, atau WEBP hingga 10 MB</p>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Catatan Tambahan (Opsional)</label>
                        <input type="text" name="payout_notes" placeholder="Contoh: Transfer via m-Banking BCA..." class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-400 font-medium">
                    </div>

                    <div class="flex items-center justify-center gap-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                        <button type="button" @click="payoutModalOpen = false" class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 hover:bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 text-xs font-bold text-gray-700 dark:text-gray-300 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition shadow-2xs cursor-pointer">
                            Konfirmasi Transfer Dana
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    <!-- Modal Refund Dana (Rule #59 Compliant) -->
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
                 class="bg-white dark:bg-gray-800 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-200 dark:border-gray-700 flex flex-col gap-5 text-center">
                <div class="space-y-1">
                    <h3 class="text-base font-black text-gray-900 dark:text-white">Proses Refund Dana ke Klien</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Dana pembayaran akan dikembalikan kepada klien <strong class="text-gray-900 dark:text-white">{{ $contract->client->nama ?? 'Klien' }}</strong> karena pesanan telah dibatalkan.
                    </p>
                </div>
                <form action="{{ route('admin.transactions.refund', $contract->id) }}" method="POST" class="space-y-4 text-left">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Catatan Refund</label>
                        <textarea name="refund_notes" rows="3" placeholder="Tuliskan catatan refund atau referensi transfer pengembalian dana..." class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-400 resize-none font-medium"></textarea>
                    </div>
                    <div class="flex items-center justify-center gap-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                        <button type="button" @click="refundModalOpen = false" class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 hover:bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 text-xs font-bold text-gray-700 dark:text-gray-300 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs transition shadow-2xs cursor-pointer">
                            Setujui Refund
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    <!-- Modal Bukti Transfer Lightbox -->
    @if($transferProofPayment && $transferProofPayment->bukti_transfer)
        <template x-teleport="body">
            <div x-show="proofLightboxOpen"
                 x-cloak
                 @keydown.escape.window="proofLightboxOpen = false"
                 class="fixed inset-0 z-[999] flex items-center justify-center p-4 bg-black/80 backdrop-blur-xs"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                <div @click.away="proofLightboxOpen = false" class="relative max-w-2xl w-full flex flex-col items-center">
                    <button type="button" @click="proofLightboxOpen = false" class="absolute -top-10 right-0 text-white hover:text-gray-300 text-xs font-bold transition cursor-pointer">
                        Tutup [ESC]
                    </button>
                    <img src="{{ $transferProofPayment->bukti_transfer }}" alt="Bukti Transfer" class="max-w-full max-h-[80vh] object-contain rounded-2xl shadow-2xl border border-white/20 select-none">
                </div>
            </div>
        </template>
    @endif

</div>
@endsection
