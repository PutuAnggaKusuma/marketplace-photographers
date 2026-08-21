<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan {{ $photographer->nama }} - {{ $selectedYear }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; }
            .print-border { border: 1px solid #e5e7eb !important; }
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 font-sans p-6 sm:p-10 max-w-5xl mx-auto space-y-8">

    <!-- Action Toolbar (Hidden when printing) -->
    <div class="no-print flex items-center justify-between bg-white p-4 rounded-2xl shadow-sm border border-gray-200">
        <div>
            <h2 class="font-extrabold text-sm text-gray-900">Format Cetak Laporan Keuangan PDF</h2>
            <p class="text-xs text-gray-500">Klik tombol di kanan untuk mengunduh PDF atau mencetak laporan.</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-black text-xs rounded-xl shadow-sm transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Cetak / Simpan PDF</span>
            </button>
            <button onclick="window.close()" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-xl transition">
                Tutup
            </button>
        </div>
    </div>

    <!-- Official Report Document Paper -->
    <div class="bg-white p-8 sm:p-12 rounded-2xl shadow-md border border-gray-200 print-border space-y-8">
        
        <!-- Header -->
        <div class="flex items-center justify-between border-b-2 border-gray-900 pb-6">
            <div class="space-y-1">
                <span class="text-[10px] font-black uppercase tracking-widest text-amber-600">Dokumen Keuangan Resmi</span>
                <h1 class="text-2xl font-black tracking-tight text-gray-900 uppercase">Laporan Keuangan & Rekapitutasi Omzet</h1>
                <p class="text-xs font-bold text-gray-500">LensMatch Marketplace Photography Platform</p>
            </div>
            <div class="text-right">
                <h2 class="text-lg font-black text-amber-600">{{ $photographer->nama }}</h2>
                <p class="text-xs text-gray-500">Periode Tahun {{ $selectedYear }}</p>
                <p class="text-[10px] text-gray-400 mt-1">Dicetak pada: {{ date('d F Y, H:i') }} WIB</p>
            </div>
        </div>

        <!-- Studio Profile Info Box -->
        <div class="grid grid-cols-2 gap-4 text-xs bg-gray-50 p-4 rounded-xl border border-gray-200">
            <div>
                <span class="text-gray-400 font-bold uppercase text-[10px] block">Nama Studio Fotografer:</span>
                <p class="font-extrabold text-sm text-gray-900">{{ $photographer->nama }}</p>
                <p class="text-gray-600">{{ $photographer->alamat }}</p>
            </div>
            <div>
                <span class="text-gray-400 font-bold uppercase text-[10px] block">Kontak & Lokasi:</span>
                <p class="font-bold text-gray-800">{{ $photographer->user->email ?? '-' }} • {{ $photographer->nomor_telepon ?? '-' }}</p>
                <p class="text-gray-600">{{ $photographer->city->name ?? '' }}, {{ $photographer->province->name ?? '' }}</p>
            </div>
        </div>

        <!-- Financial Summary Grid -->
        <div class="grid grid-cols-4 gap-4">
            <div class="p-4 rounded-xl bg-gray-50 border border-gray-200">
                <span class="text-[10px] font-bold text-gray-400 uppercase">Total Volume Bruto</span>
                <p class="text-sm font-black text-gray-900 mt-1">Rp {{ number_format($totalGrossVolume, 0, ',', '.') }}</p>
            </div>
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200">
                <span class="text-[10px] font-bold text-emerald-600 uppercase">Net Income Studio (90%)</span>
                <p class="text-sm font-black text-emerald-700 mt-1">Rp {{ number_format($totalNetIncome, 0, ',', '.') }}</p>
            </div>
            <div class="p-4 rounded-xl bg-amber-50 border border-amber-200">
                <span class="text-[10px] font-bold text-amber-600 uppercase">Fee Platform (10%)</span>
                <p class="text-sm font-black text-amber-700 mt-1">Rp {{ number_format($totalMarketplaceFee, 0, ',', '.') }}</p>
            </div>
            <div class="p-4 rounded-xl bg-blue-50 border border-blue-200">
                <span class="text-[10px] font-bold text-blue-600 uppercase">Escrow Pending</span>
                <p class="text-sm font-black text-blue-700 mt-1">Rp {{ number_format($totalEscrowPending, 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Detailed Transactions Table -->
        <div class="space-y-3">
            <h3 class="font-extrabold text-sm text-gray-900 uppercase tracking-wider">Rincian Transaksi Sesi Foto</h3>
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b-2 border-gray-900 bg-gray-100 text-[10px] font-black uppercase text-gray-700">
                        <th class="py-2.5 px-3">No</th>
                        <th class="py-2.5 px-3">Invoice</th>
                        <th class="py-2.5 px-3">Tanggal Sesi</th>
                        <th class="py-2.5 px-3">Klien</th>
                        <th class="py-2.5 px-3">Paket Layanan</th>
                        <th class="py-2.5 px-3">Total Bruto</th>
                        <th class="py-2.5 px-3">Fee 10%</th>
                        <th class="py-2.5 px-3">Net Payout</th>
                        <th class="py-2.5 px-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($contracts as $index => $c)
                        @php
                            $payment = $c->payments->first();
                            $invCode = $payment ? $payment->external_id : 'INV-LM-' . str_pad($c->id, 4, '0', STR_PAD_LEFT);
                            $feeAmount = $c->fee_marketplace > 0 ? $c->fee_marketplace : ($c->jumlah * 0.10);
                            $netPayout = $c->payout_amount > 0 ? $c->payout_amount : ($c->jumlah - $feeAmount);
                        @endphp
                        <tr>
                            <td class="py-3 px-3 font-bold">{{ $index + 1 }}</td>
                            <td class="py-3 px-3 font-mono font-bold">{{ $invCode }}</td>
                            <td class="py-3 px-3">{{ \Carbon\Carbon::parse($c->bookingDetail->booking_date ?? $c->created_at)->format('d/m/Y') }}</td>
                            <td class="py-3 px-3 font-semibold">{{ $c->client->nama ?? 'Klien' }}</td>
                            <td class="py-3 px-3">{{ $c->bookingDetail->service->nama_layanan ?? 'Paket Fotografi' }}</td>
                            <td class="py-3 px-3 font-bold">Rp {{ number_format($c->jumlah, 0, ',', '.') }}</td>
                            <td class="py-3 px-3 text-amber-700">Rp {{ number_format($feeAmount, 0, ',', '.') }}</td>
                            <td class="py-3 px-3 font-extrabold text-emerald-700">Rp {{ number_format($netPayout, 0, ',', '.') }}</td>
                            <td class="py-3 px-3 font-bold uppercase text-[10px]">
                                {{ $c->status_payout }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-6 text-center text-gray-400 italic">Tidak ada transaksi tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer Verification Signatures -->
        <div class="pt-8 border-t border-gray-200 grid grid-cols-2 gap-8 text-center text-xs">
            <div class="space-y-12">
                <p class="font-bold text-gray-500">Diverifikasi Oleh Platform:</p>
                <div class="space-y-1">
                    <p class="font-black text-gray-900 uppercase">LensMatch Finance System</p>
                    <p class="text-[10px] text-emerald-600 font-bold">✓ Verified Digital Signature</p>
                </div>
            </div>
            <div class="space-y-12">
                <p class="font-bold text-gray-500">Pemilik Studio Fotografer:</p>
                <div class="space-y-1">
                    <p class="font-black text-gray-900 uppercase">{{ $photographer->nama }}</p>
                    <p class="text-[10px] text-gray-400">Tanda Tangan & Stempel Studio</p>
                </div>
            </div>
        </div>

    </div>

</body>
</html>