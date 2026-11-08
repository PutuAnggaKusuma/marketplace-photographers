<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Resmi LensMatch — {{ $activePayment->external_id ?? ('KTRK-' . str_pad($contract->id, 5, '0', STR_PAD_LEFT)) }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
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
    <div class="no-print max-w-4xl mx-auto mb-6 bg-white rounded-2xl p-4 shadow-xs border border-gray-200/90 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('booking.invoice', $contract->id) }}" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold text-xs transition inline-flex items-center gap-1.5">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg><span>Kembali ke Invoice</span>
            </a>
            <span class="text-xs text-gray-500 font-medium">Pratinjau Dokumen Resmi Invoice Pembayaran</span>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-5 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-950 font-black text-xs shadow-xs transition cursor-pointer">
                Cetak / Simpan PDF
            </button>
        </div>
    </div>

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
            $isDpPaid = isset($dpPayment) && $dpPayment && $dpPayment->payment_status === 'completed';
            $isPelunasanPaid = isset($fullPayment) && $fullPayment && $fullPayment->payment_status === 'completed';
            $isAllPaid = ($contract->payment_type === 'dp') ? ($isDpPaid && $isPelunasanPaid) : ($activePayment->payment_status === 'completed');
        @endphp

        <!-- Status Watermark Ribbon / Banner -->
        <div class="p-4 rounded-2xl flex items-center justify-between {{ $isAllPaid ? 'bg-emerald-50 border border-emerald-200' : ($contract->payment_type === 'dp' && $isDpPaid ? 'bg-blue-50 border border-blue-200' : 'bg-amber-50 border border-amber-200') }}">
            <div class="space-y-0.5">
                <span class="text-[10px] font-black uppercase tracking-wider block {{ $isAllPaid ? 'text-emerald-700' : ($contract->payment_type === 'dp' && $isDpPaid ? 'text-blue-700' : 'text-amber-700') }}">
                    Status Transaksi
                </span>
                <p class="text-xs font-bold text-gray-800">
                    @if($isAllPaid)
                        Lunas — Pembayaran Telah Terverifikasi
                    @elseif($contract->payment_type === 'dp' && $isDpPaid)
                        Uang Muka (DP 30%) Lunas — Sesi Terkonfirmasi, Menunggu Pelunasan
                    @else
                        Menunggu Pembayaran Transfer
                    @endif
                </p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider {{ $isAllPaid ? 'bg-emerald-600 text-white' : ($contract->payment_type === 'dp' && $isDpPaid ? 'bg-blue-600 text-white' : 'bg-amber-500 text-gray-950') }}">
                {{ $isAllPaid ? 'Lunas' : ($contract->payment_type === 'dp' && $isDpPaid ? 'DP Lunas' : 'Menunggu') }}
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
                    <strong class="font-bold text-gray-900">{{ $contract->bookingDetail && $contract->bookingDetail->booking_date ? date('d F Y', strtotime($contract->bookingDetail->booking_date)) : '-' }}</strong>
                </div>
                <div>
                    <span class="text-gray-400 block text-[10px]">Waktu Pemotretan:</span>
                    <strong class="font-bold text-gray-900">{{ $contract->bookingDetail && $contract->bookingDetail->jam_mulai ? ($contract->bookingDetail->jam_mulai . ' WIB') : '-' }}</strong>
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
                            <span class="text-[11px] text-gray-500">Sesi pemotretan resolusi tinggi oleh {{ $contract->photographer->nama ?? 'Mitra Fotografer' }}</span>
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

