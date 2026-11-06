<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Keuangan - {{ $photographer->nama }} ({{ $selectedYear }})</title>
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
            <div class="w-9 h-9 rounded-xl bg-amber-400 text-gray-950 font-black text-sm flex items-center justify-center shrink-0 shadow-2xs">
                LM
            </div>
            <div>
                <h2 class="text-xs sm:text-sm font-black text-gray-900 tracking-tight">Pratinjau Dokumen Rekapitulasi Keuangan Resmi</h2>
                <p class="text-[11px] text-gray-500">Format A4 standar settlement akun. Klik cetak untuk mengunduh versi PDF resmi.</p>
            </div>
        </div>

        <div class="flex items-center gap-2.5 shrink-0">
            <button type="button" onclick="window.close()" class="px-4 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs transition cursor-pointer">
                Tutup Jendela
            </button>
            <button type="button" onclick="window.print()" class="px-5 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs rounded-xl shadow-xs transition inline-flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Cetak / Simpan PDF</span>
            </button>
        </div>
    </div>

    <!-- Official Printable A4 Document Sheet -->
    <main class="document-sheet max-w-4xl mx-auto bg-white p-8 sm:p-12 rounded-2xl shadow-sm border border-gray-200 text-gray-900 space-y-7">

        <!-- 1. Formal Institutional Letterhead / Header -->
        <header class="border-b-2 border-gray-900 pb-5 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2.5">
                    <span class="w-7 h-7 rounded-lg bg-gray-900 text-amber-400 font-black text-xs flex items-center justify-center">LM</span>
                    <span class="text-base font-black tracking-wider uppercase text-gray-900">LENSMATCH INDONESIA</span>
                </div>
                <p class="text-[11px] text-gray-600 font-medium">Platform Marketplace & Layanan Rekening Bersama Fotografi</p>
                <p class="text-[10px] text-gray-400 font-mono">PT LensMatch Digital Solusindo • finance@lensmatch.com • www.lensmatch.com</p>
            </div>

            <div class="sm:text-right space-y-0.5">
                <span class="inline-block text-[9px] font-black uppercase tracking-widest text-gray-500 border border-gray-300 px-2 py-0.5 rounded">
                    Official Settlement Statement
                </span>
                <h1 class="text-base sm:text-lg font-black text-gray-900 tracking-tight uppercase">
                    Rekapitulasi Keuangan & Omzet
                </h1>
                <p class="text-xs font-mono font-bold text-gray-800">
                    No: STMT/{{ $selectedYear }}/{{ str_pad($photographer->id, 4, '0', STR_PAD_LEFT) }}
                </p>
                <p class="text-[10px] text-gray-500">
                    Periode: <strong class="font-bold text-gray-800">Tahun Buku {{ $selectedYear }}</strong> • Diterbitkan: {{ date('d/m/Y H:i') }} WIB
                </p>
            </div>
        </header>

        <!-- 2. Account & Partner Meta Grid (Two Column Clean Ledger) -->
        <section class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs border border-gray-200 rounded-xl p-4 bg-gray-50/50">
            <div class="space-y-1 sm:border-r sm:border-gray-200 sm:pr-4">
                <span class="text-[10px] font-black uppercase tracking-wider text-gray-400 block">Identitas Mitra Fotografer</span>
                <p class="font-extrabold text-sm text-gray-900 tracking-tight">{{ $photographer->nama }}</p>
                <p class="text-gray-600 leading-relaxed">{{ $photographer->alamat ?? 'Alamat operasional studio tidak dicantumkan' }}</p>
                <p class="text-gray-500 font-mono text-[11px]">
                    {{ $photographer->city->name ?? 'Wilayah' }}, {{ $photographer->province->name ?? 'Indonesia' }} • {{ $photographer->nomor_telepon ?? '-' }}
                </p>
            </div>

            <div class="space-y-1.5 sm:pl-2">
                <span class="text-[10px] font-black uppercase tracking-wider text-gray-400 block">Parameter Rekening & Settlement</span>
                <div class="grid grid-cols-2 gap-x-2 gap-y-1 text-[11px]">
                    <span class="text-gray-500">ID Mitra:</span>
                    <span class="font-mono font-bold text-gray-900">LM-PH-{{ str_pad($photographer->id, 5, '0', STR_PAD_LEFT) }}</span>

                    <span class="text-gray-500">Email Akun:</span>
                    <span class="font-mono text-gray-800 truncate">{{ $photographer->user->email ?? '-' }}</span>

                    <span class="text-gray-500">Skema Komisi:</span>
                    <span class="font-bold text-gray-900">90% Mitra / 10% Platform</span>

                    <span class="text-gray-500">Metode Transaksi:</span>
                    <span class="font-bold text-gray-900">Rekening Bersama</span>
                </div>
            </div>
        </section>

        <!-- 3. Financial Summary Strip (Monochrome Professional Ledger) -->
        <section class="border border-gray-900/80 rounded-xl divide-y sm:divide-y-0 sm:divide-x divide-gray-200 grid grid-cols-2 sm:grid-cols-4 bg-white overflow-hidden">
            <div class="p-3.5 sm:p-4">
                <span class="text-[10px] font-bold uppercase tracking-wider text-gray-500 block">Total Bruto (Gross)</span>
                <p class="text-base sm:text-lg font-black text-gray-900 tracking-tight mt-1">
                    Rp {{ number_format($totalGrossVolume, 0, ',', '.') }}
                </p>
                <span class="text-[10px] text-gray-400 font-medium">Akumulasi nilai pesanan</span>
            </div>

            <div class="p-3.5 sm:p-4">
                <span class="text-[10px] font-bold uppercase tracking-wider text-gray-500 block">Fee Platform (10%)</span>
                <p class="text-base sm:text-lg font-black text-gray-900 tracking-tight mt-1">
                    Rp {{ number_format($totalMarketplaceFee, 0, ',', '.') }}
                </p>
                <span class="text-[10px] text-gray-400 font-medium">Biaya jasa penjaminan</span>
            </div>

            <div class="p-3.5 sm:p-4 bg-gray-50/60">
                <span class="text-[10px] font-bold uppercase tracking-wider text-gray-900 block">Pendapatan Bersih Diterima</span>
                <p class="text-base sm:text-lg font-black text-gray-900 tracking-tight mt-1">
                    Rp {{ number_format($totalNetIncome, 0, ',', '.') }}
                </p>
                <span class="text-[10px] text-emerald-600 font-bold">Status: Telah ditransfer</span>
            </div>

            <div class="p-3.5 sm:p-4">
                <span class="text-[10px] font-bold uppercase tracking-wider text-gray-500 block">Dana Tertahan</span>
                <p class="text-base sm:text-lg font-black text-gray-900 tracking-tight mt-1">
                    Rp {{ number_format($totalEscrowPending, 0, ',', '.') }}
                </p>
                <span class="text-[10px] text-amber-600 font-bold">Menunggu sesi tuntas</span>
            </div>
        </section>

        <!-- 4. Detailed Accounting Ledger Table -->
        <section class="space-y-2">
            <div class="flex items-center justify-between">
                <h3 class="text-xs font-black uppercase tracking-wider text-gray-900">
                    Rincian Transaksi Pemotretan & Pencairan Settlement
                </h3>
                <span class="text-[10px] text-gray-400 font-mono">Total Transaksi: {{ $contracts->count() }} Sesi</span>
            </div>

            <div class="border border-gray-200 rounded-xl overflow-hidden">
                <table class="w-full text-left border-collapse text-[11px]">
                    <thead>
                        <tr class="bg-gray-100/90 border-b border-gray-200 text-[10px] font-black uppercase tracking-wider text-gray-700">
                            <th class="py-2.5 px-3 w-10 text-center">No</th>
                            <th class="py-2.5 px-3 whitespace-nowrap">No. Invoice</th>
                            <th class="py-2.5 px-3 text-center whitespace-nowrap">Tgl Sesi</th>
                            <th class="py-2.5 px-3">Klien & Paket Layanan</th>
                            <th class="py-2.5 px-3 text-right whitespace-nowrap">Bruto (IDR)</th>
                            <th class="py-2.5 px-3 text-right whitespace-nowrap">Fee 10%</th>
                            <th class="py-2.5 px-3 text-right whitespace-nowrap">Pendapatan Bersih</th>
                            <th class="py-2.5 px-3 text-center whitespace-nowrap">Status Pembayaran</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 font-medium">
                        @forelse($contracts as $index => $c)
                            @php
                                $payment = $c->payments->first();
                                $invCode = $payment ? $payment->external_id : 'INV-LM-' . str_pad($c->id, 4, '0', STR_PAD_LEFT);
                                $feeAmount = $c->fee_marketplace > 0 ? $c->fee_marketplace : ($c->jumlah * 0.10);
                                $netPayout = $c->payout_amount > 0 ? $c->payout_amount : ($c->jumlah - $feeAmount);
                                $isPaid = strtolower($c->status_payout) === 'paid';
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="py-2.5 px-3 text-center text-gray-400 font-mono text-[10px]">{{ $index + 1 }}</td>
                                <td class="py-2.5 px-3 font-mono font-bold text-gray-900 whitespace-nowrap">{{ $invCode }}</td>
                                <td class="py-2.5 px-3 text-center font-mono text-gray-600 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($c->bookingDetail->booking_date ?? $c->created_at)->format('d/m/Y') }}
                                </td>
                                <td class="py-2.5 px-3">
                                    <span class="font-bold text-gray-900 block">{{ $c->client->nama ?? 'Klien LensMatch' }}</span>
                                    <span class="text-[10px] text-gray-500 block truncate max-w-xs">{{ $c->bookingDetail->service->nama_layanan ?? 'Paket Layanan Fotografi' }}</span>
                                </td>
                                <td class="py-2.5 px-3 text-right font-mono text-gray-700 whitespace-nowrap">
                                    {{ number_format($c->jumlah, 0, ',', '.') }}
                                </td>
                                <td class="py-2.5 px-3 text-right font-mono text-gray-500 whitespace-nowrap">
                                    {{ number_format($feeAmount, 0, ',', '.') }}
                                </td>
                                <td class="py-2.5 px-3 text-right font-mono font-bold text-gray-900 whitespace-nowrap">
                                    {{ number_format($netPayout, 0, ',', '.') }}
                                </td>
                                <td class="py-2.5 px-3 text-center whitespace-nowrap">
                                    @if($isPaid)
                                        <span class="inline-block text-[9px] font-mono font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                            CAIR / SELESAI
                                        </span>
                                    @elseif(in_array(strtolower($c->status_payout), ['holding', 'ready']))
                                        <span class="inline-block text-[9px] font-mono font-bold text-amber-800 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                                            DANA DITAHAN
                                        </span>
                                    @else
                                        <span class="inline-block text-[9px] font-mono font-bold text-gray-700 bg-gray-100 px-2 py-0.5 rounded border border-gray-200">
                                            {{ strtoupper($c->status_payout) }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-gray-400 italic">
                                    Tidak ada riwayat transaksi pemotretan yang tercatat untuk periode tahun ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-gray-100/90 font-black text-gray-900 border-t-2 border-gray-900 border-b-4 border-double border-gray-900">
                        <tr>
                            <td colspan="4" class="py-3 px-3 text-right uppercase tracking-wider text-[10px]">
                                TOTAL AKUMULASI LAPORAN (IDR):
                            </td>
                            <td class="py-3 px-3 text-right font-mono text-xs whitespace-nowrap">
                                {{ number_format($totalGrossVolume, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-3 text-right font-mono text-xs whitespace-nowrap text-gray-700">
                                {{ number_format($totalMarketplaceFee, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-3 text-right font-mono text-xs whitespace-nowrap text-gray-900">
                                {{ number_format($contracts->sum(function($c) { return $c->payout_amount > 0 ? $c->payout_amount : ($c->jumlah * 0.9); }), 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-3 text-center text-[10px] text-gray-500 font-mono">
                                {{ $contracts->where('status_payout', 'paid')->count() }}/{{ $contracts->count() }} Payout
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </section>

        <!-- 5. Formal Legal Disclaimer & Dual Signatures -->
        <footer class="pt-4 border-t border-gray-200 space-y-6">
            <div class="text-[10px] text-gray-500 leading-relaxed space-y-1 bg-gray-50 p-3.5 rounded-xl border border-gray-200">
                <p class="font-bold text-gray-700">Ketentuan & Keabsahan Dokumen:</p>
                <p>
                    1. Dokumen ini merupakan rekapitulasi resmi sistem settlement terpadu LensMatch atas dana kontrak kerja pemotretan klien. Seluruh transaksi menggunakan rekening penjaminan resmi LensMatch.
                </p>
                <p>
                    2. Pendapatan bersih (90%) diteruskan ke rekening fotografer mitra setelah sesi selesai dan berkas galeri foto dinyatakan tuntas diverifikasi. Dokumen ini sah dan diterbitkan secara digital.
                </p>
            </div>

            <div class="grid grid-cols-2 gap-8 text-xs pt-2">
                <div class="space-y-12">
                    <div class="space-y-0.5">
                        <span class="text-[10px] font-black uppercase tracking-wider text-gray-400 block">Diterbitkan & Diverifikasi Oleh:</span>
                        <p class="font-bold text-gray-900">Divisi Keuangan & Settlement LensMatch</p>
                    </div>
                    <div class="space-y-0.5 border-t border-gray-300 pt-2">
                        <p class="font-black text-gray-900 uppercase">PT LensMatch Digital Solusindo</p>
                        <p class="text-[10px] text-gray-400 font-mono">Digital Signature • Verified Timestamp ID</p>
                    </div>
                </div>

                <div class="space-y-12 text-right">
                    <div class="space-y-0.5">
                        <span class="text-[10px] font-black uppercase tracking-wider text-gray-400 block">Diterima & Disetujui Oleh Mitra:</span>
                        <p class="font-bold text-gray-900">{{ $photographer->nama }}</p>
                    </div>
                    <div class="space-y-0.5 border-t border-gray-300 pt-2">
                        <p class="font-black text-gray-900 uppercase">{{ $photographer->nama }}</p>
                        <p class="text-[10px] text-gray-400 font-mono">Mitra Fotografer Terverifikasi</p>
                    </div>
                </div>
            </div>
        </footer>

    </main>

</body>
</html>