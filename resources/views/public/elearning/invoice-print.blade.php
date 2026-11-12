<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Struk Pembayaran E-Learning — {{ $transaction->invoice_code }}</title>
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
<a href="{{ route('client.invoices') }}" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold text-xs transition inline-flex items-center gap-1.5">
<svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg><span>Kembali ke Daftar Invoice</span>
</a>
<span class="text-xs text-gray-500 font-medium">Pratinjau Struk Pembelian Kursus E-Learning</span>
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
<div class="flex items-center gap-2">
<span class="text-xl sm:text-2xl font-black tracking-tight text-gray-900">
Lens<span class="text-amber-500">Match</span>
</span>
<span class="px-2 py-0.5 rounded-md bg-amber-100 text-amber-800 text-[10px] font-black uppercase tracking-wider">
Academy & E-Learning
</span>
</div>
<p class="text-xs text-gray-500">Pusat Sertifikasi & Kursus Fotografi Digital Profesional</p>
<p class="text-[11px] text-gray-400">Website: lensmatch.id • Dukungan: academy@lensmatch.id</p>
</div>
<div class="text-left sm:text-right space-y-1">
<h1 class="text-2xl font-black text-gray-900 tracking-tight">Invoice</h1>
<p class="text-xs font-mono text-gray-500">{{ $transaction->invoice_code }}</p>
<p class="text-xs text-gray-500">Tanggal Pembelian: {{ ($transaction->paid_at ?? $transaction->created_at)->format('d F Y, H:i') }} WIB</p>
</div>
</div>

<!-- Status Banner -->
<div class="p-4 rounded-2xl flex items-center justify-between {{ $transaction->payment_status === 'completed' ? 'bg-emerald-50 border border-emerald-200' : 'bg-amber-50 border border-amber-200' }}">
<div class="space-y-0.5">
<span class="text-[10px] font-black uppercase tracking-wider block {{ $transaction->payment_status === 'completed' ? 'text-emerald-700' : 'text-amber-700' }}">
Status Akses Pembelajaran
</span>
<p class="text-xs font-bold text-gray-800">
{{ $transaction->payment_status === 'completed' ? 'Lunas — Akses materi & modul kursus aktif seumur hidup' : 'Menunggu Konfirmasi Pembayaran' }}
</p>
</div>
<span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider {{ $transaction->payment_status === 'completed' ? 'bg-emerald-600 text-white' : 'bg-amber-500 text-gray-950' }}">
{{ $transaction->payment_status === 'completed' ? 'Lunas' : 'Menunggu' }}
</span>
</div>

<!-- 2 Column Meta: Siswa vs Penyelenggara -->
<div class="grid grid-cols-1 sm:grid-cols-2 gap-8 text-xs border-b border-gray-100 pb-8">
<div class="space-y-1.5">
<span class="text-[10px] font-black uppercase tracking-wider text-gray-400 block">DITAGIHKAN KEPADA (PESERTA)</span>
<p class="text-sm font-extrabold text-gray-900">{{ $transaction->user->nama ?? 'Pengguna' }}</p>
<p class="text-gray-600">Email: {{ $transaction->user->email ?? '-' }}</p>
<p class="text-gray-600">Metode Pembayaran: <strong class="uppercase">{{ $transaction->payment_method ?? 'QRIS' }}</strong></p>
</div>
<div class="space-y-1.5 sm:text-right">
<span class="text-[10px] font-black uppercase tracking-wider text-gray-400 block">PENYEDIA KELAS</span>
<p class="text-sm font-extrabold text-gray-900">LensMatch Academy Indonesia</p>
<p class="text-gray-600">Instruktur: {{ $transaction->elearning->author ?? '-' }}</p>
<p class="text-gray-600">Sertifikasi: Sertifikat Digital Resmi Terverifikasi</p>
</div>
</div>

<!-- Tabel Item Pembelian -->
<div class="overflow-x-auto">
<table class="w-full text-left text-xs border-collapse">
<thead>
<tr class="border-b-2 border-gray-200 text-[10px] font-black uppercase tracking-wider text-gray-400">
<th class="py-3 pr-4">Materi Kursus & Silabus</th>
<th class="py-3 px-4 text-center">Tingkat Kemahiran</th>
<th class="py-3 pl-4 text-right">Harga (IDR)</th>
</tr>
</thead>
<tbody class="divide-y divide-gray-100">
<tr>
<td class="py-4 pr-4">
<strong class="font-bold text-gray-900 text-xs block">{{ $transaction->elearning->judul ?? 'Kursus Fotografi' }}</strong>
<span class="text-xs text-gray-500">Akses video pembelajaran, kuis evaluasi kurikulum, dan sertifikat kelulusan digital</span>
</td>
<td class="py-4 px-4 text-center capitalize font-bold text-gray-700">
{{ $transaction->elearning->tingkat ?? 'Semua Tingkat' }}
</td>
<td class="py-4 pl-4 text-right font-black text-gray-900 font-mono text-xs">
Rp {{ number_format($transaction->nominal, 0, ',', '.') }}
</td>
</tr>
</tbody>
<tfoot>
<tr class="border-t-2 border-gray-900">
<td colspan="2" class="pt-4 text-right font-black text-sm uppercase">Total Pembayaran:</td>
<td class="pt-4 text-right font-black text-base text-gray-900 font-mono">
Rp {{ number_format($transaction->nominal, 0, ',', '.') }}
</td>
</tr>
</tfoot>
</table>
</div>

<!-- Footer Notice -->
<div class="pt-6 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left text-xs text-gray-500">
<div class="space-y-1 max-w-lg">
<p class="font-bold text-gray-800">Ketentuan Sertifikasi & Akses:</p>
<p class="text-[11px] leading-relaxed text-gray-500">
Struk ini merupakan bukti pembelian sah atas hak akses materi pembelajaran di LensMatch Academy. Silakan selesaikan seluruh kuis dan tugas akhir untuk mengklaim sertifikat kelulusan Anda.
</p>
</div>
<div class="shrink-0 p-3 bg-gray-50 rounded-2xl border border-gray-200 text-center font-mono text-[10px] text-gray-400">
<span class="font-bold text-gray-800 block text-xs">ACADEMY VERIFIED</span>
<span>ID: {{ substr(md5($transaction->id . 'academy_salt'), 0, 10) }}</span>
</div>
</div>
</div>
</body>
</html>
