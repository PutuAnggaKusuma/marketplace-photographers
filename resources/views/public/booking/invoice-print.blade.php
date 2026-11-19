<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Resmi LensMatch — {{ $activePayment->external_id ?? ('KTRK-' . str_pad($contract->id, 5, '0', STR_PAD_LEFT)) }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        [x-cloak] { display: none !important; }
        .font-mono {
            font-family: 'JetBrains Mono', ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        }
        @page {
            size: A4 portrait;
            margin: 12mm 15mm 15mm 15mm;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .document-sheet {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                margin: 0 !important;
                max-width: 100% !important;
                width: 100% !important;
            }
            tr {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body class="bg-gray-100/80 text-gray-900 min-h-screen py-8 px-4 sm:px-6 antialiased">

    <!-- Top Action Toolbar (Hidden during print) -->
    @php
        $currentUser = auth()->user();
        $isAdminUser = $currentUser && (in_array($currentUser->role, ['admin', 'super_admin']) || $currentUser->roleAdmin()->exists());
        $isPhotographerUser = $currentUser && ($currentUser->role === 'photographer' || $currentUser->rolePhotographer()->exists());

        if ($isAdminUser) {
            $backUrl = route('admin.transactions.show', $contract->id);
            $backLabel = 'Kembali ke Detail Transaksi';
        } elseif ($isPhotographerUser) {
            $backUrl = route('photographer.bookings');
            $backLabel = 'Kembali ke Kelola Booking';
        } else {
            $backUrl = route('booking.invoice', array_merge(['id' => $contract->id], request()->only('from', 'view')));
            $backLabel = 'Kembali ke Invoice';
        }
    @endphp
    <div class="no-print max-w-4xl mx-auto mb-6 bg-white rounded-2xl p-4 shadow-xs border border-gray-200/90 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ $backUrl }}" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold text-xs transition inline-flex items-center gap-1.5 cursor-pointer">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                <span>{{ $backLabel }}</span>
            </a>
            <span class="text-xs text-gray-500 font-medium">Pratinjau Dokumen Resmi Invoice Pembayaran</span>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-5 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-950 font-black text-xs shadow-xs transition cursor-pointer">
                Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <!-- Flash Session Alerts (No Print) -->
    @if(session('success'))
        <div class="no-print max-w-4xl mx-auto mb-4 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center gap-3 text-xs text-emerald-800 font-bold">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="no-print max-w-4xl mx-auto mb-4 p-4 rounded-2xl bg-rose-50 border border-rose-200 flex items-center gap-3 text-xs text-rose-800 font-bold">
            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if($isAdminUser)
        @php
            $feeAmount = $contract->fee_marketplace > 0 ? $contract->fee_marketplace : ($contract->jumlah * 0.10);
            $netPayout = $contract->payout_amount > 0 ? $contract->payout_amount : max(0, $contract->jumlah - $feeAmount);
            $pendingPayment = $contract->payments->where('payment_status', 'pending')->first();
            $transferProofPayment = $contract->payments->first(fn($p) => !empty($p->bukti_transfer));
            $isPayoutReady = $contract->status_payout === 'ready';
            $isPayoutPaid = $contract->status_payout === 'paid';
            $canRefund = $contract->payments->where('payment_status', 'completed')->isNotEmpty() 
                && $contract->status_contract === 'cancelled' 
                && $contract->status_payout !== 'refunded';
            $isRefunded = $contract->status_payout === 'refunded';
        @endphp

        <!-- Panel Audit Admin (No Print) -->
        <div class="no-print max-w-4xl mx-auto mb-6 bg-white rounded-3xl p-6 sm:p-8 shadow-xs border border-gray-200/90 space-y-6"
             x-data="{
                 proofLightboxOpen: false,
                 rejectModalOpen: false,
                 payoutModalOpen: false,
                 refundModalOpen: false
             }">

            <!-- Header Panel -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-gray-100 pb-4">
                <div>
                    <h2 class="text-base sm:text-lg font-black text-gray-900 tracking-tight">Panel Audit Admin</h2>
                    <p class="text-xs text-gray-500">Rincian internal platform, rekening fotografer, dan kontrol operasional pembayaran</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-[11px] font-mono font-bold text-gray-500 bg-gray-100 px-3 py-1 rounded-lg">
                        {{ 'KTRK-' . str_pad($contract->id, 5, '0', STR_PAD_LEFT) }}
                    </span>
                </div>
            </div>

            <!-- 3 Column Data Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-xs">
                
                <!-- Kolom 1: Finansial Platform -->
                <div class="space-y-3">
                    <span class="text-[10px] font-black uppercase tracking-wider text-gray-400 block">Finansial Platform</span>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center text-gray-600">
                            <span>Total Pembayaran:</span>
                            <span class="font-bold text-gray-900 font-mono">Rp {{ number_format($contract->jumlah, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-gray-600">
                            <span>Komisi Platform (10%):</span>
                            <span class="font-bold text-gray-900 font-mono">Rp {{ number_format($feeAmount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-gray-900 font-extrabold pt-2 border-t border-gray-100">
                            <span>Pendapatan Bersih:</span>
                            <span class="text-emerald-600 font-mono text-sm">Rp {{ number_format($netPayout, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Kolom 2: Rekening Bank Fotografer -->
                <div class="space-y-3">
                    <span class="text-[10px] font-black uppercase tracking-wider text-gray-400 block">Rekening Bank Fotografer</span>
                    <div class="space-y-1.5">
                        <p class="text-xs font-extrabold text-gray-900 uppercase">
                            {{ $contract->photographer->nama_bank ?? 'Bank Belum Diatur' }}
                        </p>
                        <p class="font-mono text-xs font-bold text-gray-800">
                            {{ $contract->photographer->nomor_rekening ?? '-' }}
                        </p>
                        <p class="text-gray-500 text-[11px]">
                            a.n. {{ $contract->photographer->atas_nama_rekening ?? ($contract->photographer->nama ?? '-') }}
                        </p>
                    </div>
                </div>

                <!-- Kolom 3: Status Operasional -->
                <div class="space-y-3">
                    <span class="text-[10px] font-black uppercase tracking-wider text-gray-400 block">Status Operasional</span>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500">Status Dana:</span>
                            @if($isPayoutPaid)
                                <span class="font-bold text-emerald-600">Sudah Ditransfer</span>
                            @elseif($isPayoutReady)
                                <span class="font-bold text-blue-600">Siap Ditransfer</span>
                            @elseif($isRefunded)
                                <span class="font-bold text-rose-600">Dana Dikembalikan</span>
                            @elseif($contract->status_contract === 'cancelled')
                                <span class="font-bold text-gray-500">Dibatalkan</span>
                            @elseif($contract->payments->where('payment_status', 'completed')->isNotEmpty())
                                <span class="font-bold text-amber-600">Dana Ditahan</span>
                            @else
                                <span class="font-bold text-gray-500">Menunggu Pembayaran</span>
                            @endif
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-gray-500">Status Sesi:</span>
                            @php
                                $bookingStatus = $contract->bookingDetail->status_booking ?? 'pending';
                            @endphp
                            <span class="font-bold {{ $bookingStatus === 'completed' ? 'text-emerald-600' : ($bookingStatus === 'confirmed' ? 'text-blue-600' : ($bookingStatus === 'cancelled' ? 'text-rose-600' : 'text-amber-600')) }}">
                                {{ $bookingStatus === 'completed' ? 'Selesai' : ($bookingStatus === 'confirmed' ? 'Dikonfirmasi' : ($bookingStatus === 'cancelled' ? 'Dibatalkan' : 'Menunggu Konfirmasi')) }}
                            </span>
                        </div>

                        @if($contract->cancellation)
                            <div class="pt-2 border-t border-gray-100 text-[11px] space-y-0.5">
                                <span class="text-rose-600 font-bold block">
                                    Dibatalkan: {{ $contract->cancellation->alasan }}
                                </span>
                                @if($contract->cancellation->refund_amount > 0)
                                    <span class="text-gray-500 block">
                                        Pengembalian: Rp {{ number_format($contract->cancellation->refund_amount, 0, ',', '.') }}
                                    </span>
                                @endif
                            </div>
                        @endif

                        @if($contract->payout_at)
                            <div class="pt-1 text-[11px] text-gray-500">
                                Ditransfer: {{ $contract->payout_at->format('d M Y, H:i') }} WIB
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            <!-- Bagian Bukti Transfer & Aksi Cepat -->
            <div class="border-t border-gray-100 pt-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
                
                <!-- Bukti Transfer Thumbnail (Jika Ada) -->
                <div class="flex items-center gap-3">
                    @if($transferProofPayment && $transferProofPayment->bukti_transfer)
                        <div class="relative group cursor-pointer w-24 h-16 rounded-xl overflow-hidden border border-gray-200 shrink-0"
                             @click="proofLightboxOpen = true">
                            <img src="{{ $transferProofPayment->bukti_transfer }}" alt="Bukti Transfer" class="w-full h-full object-cover group-hover:scale-105 transition">
                            <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition text-[10px] text-white font-bold">
                                Perbesar
                            </div>
                        </div>
                        <div class="space-y-0.5">
                            <span class="text-xs font-bold text-gray-900 block">Bukti Transfer Klien</span>
                            <span class="text-[11px] font-semibold {{ $transferProofPayment->payment_status === 'completed' ? 'text-emerald-600' : 'text-amber-600' }}">
                                {{ $transferProofPayment->payment_status === 'completed' ? 'Terverifikasi' : 'Menunggu Verifikasi Admin' }}
                            </span>
                        </div>
                    @else
                        <span class="text-xs text-gray-400 italic">Tidak ada berkas bukti transfer manual.</span>
                    @endif
                </div>

                <!-- Action Triggers -->
                <div class="flex flex-wrap items-center gap-2">
                    
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
                        <button type="button" @click="rejectModalOpen = true" class="px-4 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold text-xs transition cursor-pointer inline-flex items-center gap-1.5">
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
                        <span class="px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 font-bold text-xs border border-emerald-200/80">
                            Transfer Selesai
                        </span>
                    @endif

                    @if($isRefunded)
                        <span class="px-3 py-1.5 rounded-lg bg-rose-50 text-rose-700 font-bold text-xs border border-rose-200/80">
                            Refund Selesai
                        </span>
                    @endif

                </div>

            </div>

            <!-- Modal Tolak Bukti Transfer -->
            <div x-show="rejectModalOpen"
                 x-cloak
                 @keydown.escape.window="rejectModalOpen = false"
                 class="fixed inset-0 z-[999] flex items-center justify-center p-4 sm:p-6 bg-black/60 backdrop-blur-xs"
                 x-transition>
                <div @click.away="rejectModalOpen = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-200 flex flex-col gap-5 text-center">
                    <div class="space-y-1">
                        <h3 class="text-base font-black text-gray-900">Tolak Bukti Transfer</h3>
                        <p class="text-xs text-gray-500">Klien akan diminta mengunggah ulang bukti pembayaran yang sah.</p>
                    </div>
                    <form action="{{ route('admin.transactions.reject-payment', $contract->id) }}" method="POST" class="space-y-4 text-left">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">Alasan Penolakan</label>
                            <textarea name="alasan" rows="3" required placeholder="Masukkan alasan penolakan bukti transfer..." class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-amber-400 bg-gray-50 resize-none font-medium"></textarea>
                        </div>
                        <div class="flex items-center justify-center gap-2 pt-2 border-t border-gray-100">
                            <button type="button" @click="rejectModalOpen = false" class="px-4 py-2 rounded-xl border border-gray-200 bg-gray-50 hover:bg-gray-100 text-xs font-bold text-gray-700 transition cursor-pointer">
                                Batal
                            </button>
                            <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs transition shadow-2xs cursor-pointer">
                                Tolak Bukti Bayar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal Transfer Dana Fotografer -->
            <div x-show="payoutModalOpen"
                 x-cloak
                 @keydown.escape.window="payoutModalOpen = false"
                 class="fixed inset-0 z-[999] flex items-center justify-center p-4 sm:p-6 bg-black/60 backdrop-blur-xs"
                 x-transition>
                <div @click.away="payoutModalOpen = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-200 flex flex-col gap-5 text-center">
                    <div class="space-y-1">
                        <h3 class="text-base font-black text-gray-900">Transfer Dana ke Fotografer</h3>
                        <p class="text-xs text-gray-500">
                            Transfer dana sebesar <strong class="text-emerald-600">Rp {{ number_format($netPayout, 0, ',', '.') }}</strong> ke rekening mitra <strong class="text-gray-900">{{ $contract->photographer->nama ?? 'Fotografer' }}</strong>.
                        </p>
                    </div>
                    
                    <!-- Rekening Info -->
                    <div class="p-3.5 rounded-xl bg-gray-50 border border-gray-200 text-xs text-left space-y-1">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Bank:</span>
                            <span class="font-bold text-gray-900 uppercase">{{ $contract->photographer->nama_bank ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">No. Rekening:</span>
                            <span class="font-mono font-bold text-gray-900">{{ $contract->photographer->nomor_rekening ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Atas Nama:</span>
                            <span class="font-bold text-gray-900">{{ $contract->photographer->atas_nama_rekening ?? ($contract->photographer->nama ?? '-') }}</span>
                        </div>
                    </div>

                    <form action="{{ route('admin.transactions.release-payout', $contract->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-left">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">Catatan Transfer (Opsional)</label>
                            <input type="text" name="payout_notes" placeholder="Catatan atau nomor referensi bank..." class="w-full px-3.5 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-amber-400 bg-gray-50 font-medium">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">Unggah Bukti Transfer Bank (Opsional)</label>
                            <input type="file" name="bukti_transfer_payout" accept="image/jpeg,image/png,image/webp" class="w-full text-xs text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-gray-100 file:text-gray-800 hover:file:bg-gray-200 cursor-pointer">
                        </div>
                        <div class="flex items-center justify-center gap-2 pt-2 border-t border-gray-100">
                            <button type="button" @click="payoutModalOpen = false" class="px-4 py-2 rounded-xl border border-gray-200 bg-gray-50 hover:bg-gray-100 text-xs font-bold text-gray-700 transition cursor-pointer">
                                Batal
                            </button>
                            <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition shadow-2xs cursor-pointer">
                                Konfirmasi Transfer
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal Refund Dana ke Klien -->
            <div x-show="refundModalOpen"
                 x-cloak
                 @keydown.escape.window="refundModalOpen = false"
                 class="fixed inset-0 z-[999] flex items-center justify-center p-4 sm:p-6 bg-black/60 backdrop-blur-xs"
                 x-transition>
                <div @click.away="refundModalOpen = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-200 flex flex-col gap-5 text-center">
                    <div class="space-y-1">
                        <h3 class="text-base font-black text-gray-900">Proses Refund Dana</h3>
                        <p class="text-xs text-gray-500">
                            Pengembalian dana untuk pesanan yang dibatalkan kepada klien <strong class="text-gray-900">{{ $contract->client->nama ?? 'Klien' }}</strong>.
                        </p>
                    </div>
                    <form action="{{ route('admin.transactions.refund', $contract->id) }}" method="POST" class="space-y-4 text-left">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">Catatan Refund (Opsional)</label>
                            <input type="text" name="refund_notes" placeholder="Catatan pengembalian dana..." class="w-full px-3.5 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-amber-400 bg-gray-50 font-medium">
                        </div>
                        <div class="flex items-center justify-center gap-2 pt-2 border-t border-gray-100">
                            <button type="button" @click="refundModalOpen = false" class="px-4 py-2 rounded-xl border border-gray-200 bg-gray-50 hover:bg-gray-100 text-xs font-bold text-gray-700 transition cursor-pointer">
                                Batal
                            </button>
                            <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs transition shadow-2xs cursor-pointer">
                                Konfirmasi Refund
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            @if($transferProofPayment && $transferProofPayment->bukti_transfer)
                <!-- Lightbox Modal Bukti Transfer -->
                <div x-show="proofLightboxOpen"
                     x-cloak
                     @keydown.escape.window="proofLightboxOpen = false"
                     class="fixed inset-0 z-[999] flex items-center justify-center p-4 bg-black/80 backdrop-blur-xs"
                     x-transition>
                    <div @click.away="proofLightboxOpen = false" class="relative max-w-2xl w-full flex flex-col items-center">
                        <button type="button" @click="proofLightboxOpen = false" class="absolute -top-10 right-0 text-white hover:text-gray-300 text-xs font-bold transition cursor-pointer">
                            Tutup [ESC]
                        </button>
                        <img src="{{ $transferProofPayment->bukti_transfer }}" alt="Bukti Transfer" class="max-w-full max-h-[80vh] object-contain rounded-2xl shadow-2xl border border-white/20 select-none">
                    </div>
                </div>
            @endif

        </div>
    @endif

        <!-- Official Printable Invoice Sheet -->
    <div class="document-sheet max-w-4xl mx-auto bg-white rounded-3xl p-8 sm:p-12 shadow-md border border-gray-200/80 space-y-8">
        
        <!-- Document Header -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 border-b-2 border-gray-100 pb-8">
            <div class="space-y-1">
                <span class="text-xl sm:text-2xl font-black tracking-tight text-gray-900">
                    Lens<span class="text-amber-500">Match</span>
                </span>
                <p class="text-xs text-gray-500">Platform Marketplace Fotografi Indonesia</p>
                <p class="text-[11px] text-gray-400">Website: lensmatch.id • Dukungan: support@lensmatch.id</p>
            </div>

            <div class="text-left sm:text-right space-y-1">
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Invoice</h1>
                <p class="text-xs font-mono text-gray-500">{{ $activePayment->external_id ?? ('KTRK-' . str_pad($contract->id, 5, '0', STR_PAD_LEFT)) }}</p>
                <p class="text-xs text-gray-500">Tanggal Terbit: {{ $contract->created_at->format('d F Y, H:i') }} WIB</p>
            </div>
        </div>

        @php
            $isCancelled = $contract->status_contract === 'cancelled';
            $isDpPaid = isset($dpPayment) && $dpPayment && $dpPayment->payment_status === 'completed';
            $isPelunasanPaid = isset($fullPayment) && $fullPayment && $fullPayment->payment_status === 'completed';
            $isAllPaid = !$isCancelled && (($contract->payment_type === 'dp') ? ($isDpPaid && $isPelunasanPaid) : ($activePayment->payment_status === 'completed'));
        @endphp

        <!-- Status Watermark Ribbon / Banner -->
        <div class="p-4 rounded-2xl flex items-center justify-between {{ $isCancelled ? 'bg-rose-50 border border-rose-200' : ($isAllPaid ? 'bg-emerald-50 border border-emerald-200' : ($contract->payment_type === 'dp' && $isDpPaid ? 'bg-blue-50 border border-blue-200' : 'bg-amber-50 border border-amber-200')) }}">
            <div class="space-y-0.5">
                <span class="text-[10px] font-black uppercase tracking-wider block {{ $isCancelled ? 'text-rose-700' : ($isAllPaid ? 'text-emerald-700' : ($contract->payment_type === 'dp' && $isDpPaid ? 'text-blue-700' : 'text-amber-700')) }}">
                    Status Transaksi
                </span>
                <p class="text-xs font-bold text-gray-800">
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

        <!-- 2 Column Meta: Klien vs Fotografer -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 text-xs border-b border-gray-100 pb-8">
            <!-- Pihak Klien -->
            <div class="space-y-1.5">
                <span class="text-[10px] font-black uppercase tracking-wider text-gray-400 block">DITAGIHKAN KEPADA (KLIEN)</span>
                <p class="text-sm font-extrabold text-gray-900">{{ $contract->client->nama ?? 'Klien' }}</p>
                <p class="text-gray-600">Email: {{ $contract->client->user->email ?? '-' }}</p>
                <p class="text-gray-600">WhatsApp / Telp: {{ $contract->client->nomor_telepon ?? '-' }}</p>
            </div>

            <!-- Pihak Fotografer Mitra -->
            <div class="space-y-1.5 sm:text-right">
                <span class="text-[10px] font-black uppercase tracking-wider text-gray-400 block">PENYEDIA JASA (FOTOGRAFER)</span>
                <p class="text-sm font-extrabold text-gray-900">{{ $contract->photographer->nama ?? 'Mitra Fotografer' }}</p>
                <p class="text-gray-600">Lokasi: {{ $contract->photographer->city->name ?? '-' }}, {{ $contract->photographer->province->name ?? '-' }}</p>
                <p class="text-gray-600">Email: {{ $contract->photographer->user->email ?? '-' }}</p>
            </div>
        </div>

        <!-- Rincian Layanan & Sesi Booking -->
        <div class="space-y-3">
            <span class="text-[10px] font-black uppercase tracking-wider text-gray-400 block">RINCIAN SESI PEMOTRETAN</span>
            <div class="bg-gray-50/80 rounded-2xl p-4 border border-gray-100 grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                <div>
                    <span class="text-gray-400 block text-[10px]">Paket Layanan:</span>
                    <strong class="font-bold text-gray-900">{{ $contract->bookingDetail->service->nama_layanan ?? 'Paket Fotografi' }}</strong>
                </div>
                <div>
                    <span class="text-gray-400 block text-[10px]">Tanggal Sesi:</span>
                    <strong class="font-bold text-gray-900">{{ $contract->bookingDetail ? $contract->bookingDetail->date_range_label : '-' }}</strong>
                </div>
                <div>
                    <span class="text-gray-400 block text-[10px]">Waktu Pemotretan:</span>
                    @php
                        $prtStart = $contract->bookingDetail?->jam_mulai ? substr($contract->bookingDetail->jam_mulai, 0, 5) : null;
                        $prtEnd = $contract->bookingDetail?->jam_selesai ? substr($contract->bookingDetail->jam_selesai, 0, 5) : null;
                    @endphp
                    <strong class="font-bold text-gray-900">{{ $prtStart ? ($prtEnd ? "{$prtStart} - {$prtEnd} WIB" : "{$prtStart} WIB") . ' (' . ($contract->bookingDetail->effective_duration_hours ?? 1) . ' Jam)' : '-' }}</strong>
                </div>
                <div>
                    <span class="text-gray-400 block text-[10px]">Lokasi Acara:</span>
                    <strong class="font-bold text-gray-900 truncate block">{{ $contract->bookingDetail->lokasi ?? '-' }}</strong>
                </div>
            </div>
        </div>

        <!-- Tabel Perhitungan Tagihan -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b-2 border-gray-200 text-[10px] font-black uppercase tracking-wider text-gray-400">
                        <th class="py-3 pr-4">Deskripsi Item</th>
                        <th class="py-3 px-4 text-center">Tipe Pembayaran</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 pl-4 text-right">Jumlah (IDR)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <!-- Paket Utama -->
                    <tr>
                        <td class="py-3.5 pr-4">
                            <strong class="font-bold text-gray-900 block">{{ $contract->bookingDetail->service->nama_layanan ?? 'Paket Foto' }}</strong>
                            <span class="text-xs text-gray-500">Sesi pemotretan resolusi tinggi oleh {{ $contract->photographer->nama ?? 'Mitra Fotografer' }}</span>
                        </td>
                        <td class="py-3.5 px-4 text-center uppercase font-bold text-gray-600">
                            {{ $contract->payment_type === 'dp' ? 'Uang Muka 30%' : 'Bayar Penuh 100%' }}
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            @if($isAllPaid)
                                <span class="font-bold text-emerald-600">Lunas</span>
                            @elseif($contract->payment_type === 'dp' && $isDpPaid)
                                <span class="font-bold text-blue-600">DP Terbayar</span>
                            @else
                                <span class="font-bold text-amber-600">Belum Bayar</span>
                            @endif
                        </td>
                        <td class="py-3.5 pl-4 text-right font-bold text-gray-900 font-mono">
                            Rp {{ number_format($contract->jumlah, 0, ',', '.') }}
                        </td>
                    </tr>

                    @if($contract->payment_type === 'dp')
                        <!-- Rincian DP -->
                        <tr class="bg-gray-50/50">
                            <td class="py-2.5 pr-4 pl-4 text-gray-600 font-medium" colspan="2">
                                ↳ Termin 1: Uang Muka (DP 30%)
                            </td>
                            <td class="py-2.5 px-4 text-center font-bold {{ $isDpPaid ? 'text-emerald-600' : 'text-amber-600' }}">
                                {{ $isDpPaid ? 'Lunas' : 'Pending' }}
                            </td>
                            <td class="py-2.5 pl-4 text-right font-mono font-bold text-gray-800">
                                Rp {{ number_format($contract->jumlah_dp, 0, ',', '.') }}
                            </td>
                        </tr>
                        <!-- Rincian Pelunasan -->
                        <tr class="bg-gray-50/50">
                            <td class="py-2.5 pr-4 pl-4 text-gray-600 font-medium" colspan="2">
                                ↳ Termin 2: Pelunasan Sisa (70%)
                            </td>
                            <td class="py-2.5 px-4 text-center font-bold {{ $isPelunasanPaid ? 'text-emerald-600' : 'text-amber-600' }}">
                                {{ $isPelunasanPaid ? 'Lunas' : 'Menunggu' }}
                            </td>
                            <td class="py-2.5 pl-4 text-right font-mono font-bold text-gray-800">
                                Rp {{ number_format(max(0, $contract->jumlah - $contract->jumlah_dp), 0, ',', '.') }}
                            </td>
                        </tr>
                    @endif

                    <!-- Biaya Layanan Platform -->
                    <tr>
                        <td class="py-3 pr-4 text-gray-600" colspan="2">
                            Biaya Layanan Platform
                        </td>
                        <td class="py-3 px-4 text-center font-bold text-gray-700">GRATIS</td>
                        <td class="py-3 pl-4 text-right font-mono font-bold text-gray-900">Rp 0</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-gray-900">
                        <td colspan="3" class="pt-4 text-right font-black text-sm uppercase">Total Nilai Kontrak:</td>
                        <td class="pt-4 text-right font-black text-base text-gray-900 font-mono">
                            Rp {{ number_format($contract->jumlah, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Footer Notice -->
        <div class="pt-6 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left text-xs text-gray-500">
            <div class="space-y-1 max-w-lg">
                <p class="font-bold text-gray-800">Keterangan:</p>
                <p class="text-[11px] leading-relaxed text-gray-500">
                    Dokumen ini merupakan bukti tagihan sah yang diterbitkan secara elektronik oleh sistem LensMatch.
                </p>
            </div>
            <div class="text-right text-[11px] text-gray-400">
                <p class="font-medium">LensMatch Indonesia</p>
                <p>Dokumen Digital Resmi</p>
            </div>
        </div>

    </div>

</body>
</html>

