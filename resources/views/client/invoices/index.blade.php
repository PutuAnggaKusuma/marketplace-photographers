@extends('layouts.app')

@section('title', 'Daftar Invoice & Tagihan Saya — LensMatch')

@section('content')
<section class="py-12 bg-white dark:bg-gray-900 min-h-screen" x-data="{
  activeTab: 'booking',
  handleSearch(e) {
    let q = e.target.value.toLowerCase();
    let rows = document.querySelectorAll('.invoice-row');
    rows.forEach(r => {
      let text = r.innerText.toLowerCase();
      r.style.display = text.includes(q) ? '' : 'none';
    });
  }
}">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-black text-gray-900 dark:text-white">Invoice & Pembayaran Saya</h1>
        <p class="text-xs sm:text-sm text-gray-500 mt-1">Daftar seluruh tagihan pembayaran resmi LensMatch.</p>
      </div>
    </div>

    <!-- 4 Clean Summary Metric Cards (Matching Dashboard Standard) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
      <!-- Card 1: Total Semua Invoice -->
      <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
        <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Total Tagihan</span>
        <h2 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ $stats['all'] ?? 0 }} Tagihan</h2>
      </div>

      <!-- Card 2: Menunggu Pembayaran -->
      <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
        <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Menunggu Bayar</span>
        <h2 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ $stats['pending'] ?? 0 }} Tagihan</h2>
      </div>

      <!-- Card 3: Pembayaran Lunas -->
      <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
        <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Lunas</span>
        <h2 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ $stats['completed'] ?? 0 }} Tagihan</h2>
      </div>

      <!-- Card 4: Total Nominal Terbayar -->
      <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
        <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Total Terbayar</span>
        <h2 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">Rp {{ number_format($stats['paid_amount'] ?? 0, 0, ',', '.') }}</h2>
      </div>
    </div>

    <!-- Tabs: Sesi Booking vs Kursus E-Learning vs Pendaftaran Lomba (Clean Underline Style, No Long Divider Line) -->
    <div class="flex items-center gap-6 overflow-x-auto pb-1">
      <button type="button" 
              @click="activeTab = 'booking'"
              :class="activeTab === 'booking' ? 'text-amber-600 dark:text-amber-400 font-black' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-bold'"
              class="py-2 text-xs sm:text-sm transition flex items-center gap-2 cursor-pointer">
        <span :class="activeTab === 'booking' ? 'underline underline-offset-8 decoration-2 decoration-amber-500 dark:decoration-amber-400' : ''">Sesi Booking Fotografer</span>
        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold" 
              :class="activeTab === 'booking' ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300'">
          {{ $stats['contracts_count'] ?? $contracts->total() }}
        </span>
      </button>

      <button type="button" 
              @click="activeTab = 'elearning'"
              :class="activeTab === 'elearning' ? 'text-amber-600 dark:text-amber-400 font-black' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-bold'"
              class="py-2 text-xs sm:text-sm transition flex items-center gap-2 cursor-pointer">
        <span :class="activeTab === 'elearning' ? 'underline underline-offset-8 decoration-2 decoration-amber-500 dark:decoration-amber-400' : ''">Kursus E-Learning</span>
        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold"
              :class="activeTab === 'elearning' ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300'">
          {{ $stats['elearning_count'] ?? count($elearningTransactions) }}
        </span>
      </button>

      <button type="button" 
              @click="activeTab = 'contest'"
              :class="activeTab === 'contest' ? 'text-amber-600 dark:text-amber-400 font-black' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-bold'"
              class="py-2 text-xs sm:text-sm transition flex items-center gap-2 cursor-pointer">
        <span :class="activeTab === 'contest' ? 'underline underline-offset-8 decoration-2 decoration-amber-500 dark:decoration-amber-400' : ''">Pendaftaran Lomba Foto</span>
        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold"
              :class="activeTab === 'contest' ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300'">
          {{ $stats['contest_count'] ?? count($contestRegistrations ?? []) }}
        </span>
      </button>
    </div>

    <!-- Data Table Card (Following Dashboard Table Standard) -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm overflow-hidden space-y-0">
      
      <!-- Toolbar: Filter Status di Kiri, Search Bar di Kanan -->
      <div class="p-4 border-b border-gray-100 dark:border-gray-700/80 flex flex-col md:flex-row items-center justify-between gap-4">
        
        <!-- Filter Status (Alpine.js Custom Floating Dropdown) -->
        <div class="flex items-center gap-3 w-full md:w-auto">
          @php
            $statusLabels = [
              'all' => 'Semua Status Tagihan',
              'pending' => 'Menunggu Pembayaran',
              'completed' => 'Lunas',
              'failed' => 'Gagal'
            ];
            $curStatName = $statusLabels[$filterStatus ?? 'all'] ?? 'Semua Status Tagihan';
          @endphp
          <div class="relative w-full sm:w-auto min-w-[220px]" x-data="{ open: false, selectedId: '{{ $filterStatus ?? 'all' }}', selectedName: '{{ $curStatName }}' }">
            <button @click="open = !open" type="button" 
                    class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs font-bold text-gray-800 dark:text-white flex items-center justify-between gap-2 shadow-2xs hover:border-amber-400 dark:hover:border-amber-500 cursor-pointer transition">
              <span x-text="selectedName" class="truncate"></span>
              <svg class="w-3.5 h-3.5 text-gray-400 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <div x-show="open" @click.away="open = false" x-transition 
                 class="absolute z-50 left-0 mt-2 min-w-[220px] w-full bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700/80 py-1 max-h-60 overflow-y-auto" style="display: none;">
              @foreach($statusLabels as $stKey => $stVal)
                <a href="{{ request()->fullUrlWithQuery(['status' => $stKey, 'page' => 1]) }}" 
                   class="w-full px-3.5 py-2 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition flex items-center justify-between text-left cursor-pointer border-b border-gray-50 dark:border-gray-700/40 last:border-0 {{ ($filterStatus ?? 'all') === $stKey ? 'bg-amber-50/70 dark:bg-amber-950/30 text-amber-600 font-extrabold' : '' }}">
                  <span>{{ $stVal }}</span>
                </a>
              @endforeach
            </div>
          </div>
        </div>

        <!-- Search Bar di Kanan -->
        <div class="relative w-full md:w-80">
          <input type="text" 
                 @input="handleSearch($event)" 
                 placeholder="Cari nomor invoice, layanan, kursus..." 
                 class="w-full pl-4 pr-10 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">
          <svg class="w-4 h-4 text-gray-400 absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>
      </div>

      <!-- 1. Table Container: Sesi Booking Fotografer -->
      <div x-show="activeTab === 'booking'" class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-[11px] font-extrabold uppercase tracking-wider text-gray-500 dark:text-gray-400">
              <th class="py-3.5 px-4 sm:px-6">Nomor Invoice</th>
              <th class="py-3.5 px-4 sm:px-6">Layanan</th>
              <th class="py-3.5 px-4 sm:px-6">Fotografer</th>
              <th class="py-3.5 px-4 sm:px-6">Total Tagihan</th>
              <th class="py-3.5 px-4 sm:px-6 text-center">Status</th>
              <th class="py-3.5 px-4 sm:px-6 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody id="invoice-table-body" class="divide-y divide-gray-100 dark:divide-gray-700/60 text-xs">
            @forelse($contracts as $c)
              @php
                $dpPayment = $c->payments->where('contract_payment_type', 'dp')->first();
                $fullPayment = $c->payments->where('contract_payment_type', 'full')->first();
                $isDp = $c->payment_type === 'dp';
                $isDpPaid = $dpPayment && $dpPayment->payment_status === 'completed';
                $isFullPaid = $fullPayment && $fullPayment->payment_status === 'completed';
                $isFullySettled = $isDp ? ($isDpPaid && $isFullPaid) : ($c->payments->first() && $c->payments->first()->payment_status === 'completed');

                $payment = $c->payments->first();
                $pStatus = $payment->payment_status ?? 'pending';
                $rawExternal = $payment->external_id ?? null;
                if ($rawExternal && !str_contains($rawExternal, 'XXXX')) {
                    $invoiceCode = $rawExternal;
                } else {
                    $invoiceCode = 'INV-LM-' . $c->created_at->format('Y') . '-' . str_pad($c->id, 3, '0', STR_PAD_LEFT);
                }
              @endphp
              <tr class="invoice-row hover:bg-amber-50/30 dark:hover:bg-amber-950/20 transition">
                
                <!-- Nomor Invoice -->
                <td class="py-4 px-4 sm:px-6 align-middle whitespace-nowrap">
                  <span class="font-mono text-xs font-black text-gray-900 dark:text-white block">{{ $invoiceCode }}</span>
                  <span class="text-[10px] text-gray-400 dark:text-gray-500 block mt-0.5">{{ $c->created_at->translatedFormat('d M Y, H:i') }}</span>
                </td>

                <!-- Layanan -->
                <td class="py-4 px-4 sm:px-6 align-middle">
                  <div class="space-y-0.5">
                    <span class="font-bold text-gray-900 dark:text-white block text-xs sm:text-sm">
                      {{ $c->bookingDetail->service->nama_layanan ?? 'Sesi Fotografi' }}
                    </span>
                    <span class="text-[11px] text-gray-500 dark:text-gray-400 block">
                      Jadwal: {{ $c->bookingDetail && $c->bookingDetail->booking_date ? \Carbon\Carbon::parse($c->bookingDetail->booking_date)->translatedFormat('d M Y') : '-' }}
                    </span>
                  </div>
                </td>

                <!-- Fotografer -->
                <td class="py-4 px-4 sm:px-6 align-middle">
                  <div class="flex items-center gap-3">
                    <img onerror="this.onerror=null;this.src=window.SVG_IMAGE_FALLBACK||'data:image/svg+xml;charset=UTF-8,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'800\' height=\'600\' viewBox=\'0 0 800 600\' fill=\'none\'%3E%3Crect width=\'800\' height=\'600\' fill=\'%23F3F4F6\'/%3E%3Cpath d=\'M360 260C360 248.954 368.954 240 380 240H420C431.046 240 440 248.954 440 260V265H450C466.569 265 480 278.431 480 295V345C480 361.569 466.569 375 450 375H350C333.431 375 320 361.569 320 345V295C320 278.431 333.431 265 350 265H360V260Z\' stroke=\'%239CA3AF\' stroke-width=\'12\' stroke-linecap=\'round\' stroke-linejoin=\'round\'/%3E%3Ccircle cx=\'400\' cy=\'320\' r=\'30\' stroke=\'%239CA3AF\' stroke-width=\'12\'/%3E%3Cline x1=\'310\' y1=\'240\' x2=\'490\' y2=\'390\' stroke=\'%23EF4444\' stroke-width=\'10\' stroke-linecap=\'round\'/%3E%3Ctext x=\'400\' y=\'450\' font-family=\'sans-serif\' font-size=\'22\' font-weight=\'700\' fill=\'%236B7280\' text-anchor=\'middle\'%3EGambar Tidak Dapat Dimuat%3C/text%3E%3C/svg%3E';" 
                         src="{{ $c->photographer->foto ?? '' }}" 
                         alt="{{ $c->photographer->nama ?? 'Fotografer' }}" 
                         class="w-8 h-8 rounded-full object-cover border border-amber-400 shrink-0" />
                    <div>
                      <span class="font-bold text-gray-900 dark:text-white block">{{ $c->photographer->nama ?? 'Fotografer' }}</span>
                      <span class="text-[11px] text-gray-500 dark:text-gray-400 block">{{ $c->photographer->city->name ?? $c->photographer->alamat ?? '-' }}</span>
                    </div>
                  </div>
                </td>

                <!-- Total Tagihan -->
                <td class="py-4 px-4 sm:px-6 align-middle whitespace-nowrap">
                  <span class="text-sm font-black text-gray-900 dark:text-white block">Rp {{ number_format($c->jumlah, 0, ',', '.') }}</span>
                  <span class="text-[10px] text-gray-400 dark:text-gray-500 font-medium block">
                    {{ $isDp ? 'Uang Muka (DP 30%)' : 'Pembayaran Penuh (100%)' }}
                  </span>
                </td>

                <!-- Status Pembayaran (Rule #14: Pure Colored Text) -->
                <td class="py-4 px-4 sm:px-6 align-middle text-center whitespace-nowrap">
                  @if($isFullySettled)
                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">
                      Lunas
                    </span>
                  @elseif($isDp && $isDpPaid && !$isFullPaid)
                    <span class="text-xs font-bold text-blue-600 dark:text-blue-400">
                      DP Terbayar
                    </span>
                  @elseif($pStatus === 'failed')
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
                  <a href="{{ url('/pembayaran/' . $c->id) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-600 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300 transition group">
                    <span>Rincian</span>
                    <svg class="w-4 h-4 transition-transform duration-150 group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"></path>
                    </svg>
                  </a>
                </td>

              </tr>
            @empty
              <tr>
                <td colspan="6" class="py-12 text-center space-y-3">
                  <h3 class="text-base font-extrabold text-gray-900 dark:text-white">Belum Ada Tagihan Pembayaran</h3>
                  <p class="text-xs text-gray-500">Semua riwayat tagihan invoice sesi booking Anda akan ditampilkan di sini.</p>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>

        <!-- Pagination Footer for Contracts -->
        @if($contracts->total() > 0)
          <div class="p-4 border-t border-gray-100 dark:border-gray-700/80 bg-gray-50/50 dark:bg-gray-900/50">
            {{ $contracts->links('partials.dashboard.pagination') }}
          </div>
        @endif
      </div>

      <!-- 2. Table Container: Kursus E-Learning -->
      <div x-show="activeTab === 'elearning'" class="overflow-x-auto" style="display: none;">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-[11px] font-extrabold uppercase tracking-wider text-gray-500 dark:text-gray-400">
              <th class="py-3.5 px-4 sm:px-6">Nomor Invoice</th>
              <th class="py-3.5 px-4 sm:px-6">Materi Kursus E-Learning</th>
              <th class="py-3.5 px-4 sm:px-6">Metode Bayar</th>
              <th class="py-3.5 px-4 sm:px-6">Total Tagihan</th>
              <th class="py-3.5 px-4 sm:px-6 text-center">Status</th>
              <th class="py-3.5 px-4 sm:px-6 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60 text-xs">
            @forelse($elearningTransactions as $el)
              <tr class="invoice-row hover:bg-amber-50/30 dark:hover:bg-amber-950/20 transition">
                <!-- Nomor Invoice -->
                <td class="py-4 px-4 sm:px-6 align-middle font-mono font-bold text-gray-900 dark:text-white">
                  {{ $el->invoice_code }}
                  <span class="block text-[10px] font-normal text-gray-400 mt-0.5">
                    {{ $el->paid_at ? $el->paid_at->format('d M Y, H:i') : $el->created_at->format('d M Y, H:i') }}
                  </span>
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

                <!-- Total Tagihan -->
                <td class="py-4 px-4 sm:px-6 align-middle font-extrabold text-gray-900 dark:text-white whitespace-nowrap">
                  Rp {{ number_format($el->nominal, 0, ',', '.') }}
                </td>

                <!-- Status -->
                <td class="py-4 px-4 sm:px-6 align-middle text-center whitespace-nowrap">
                  @if($el->payment_status === 'completed')
                    <span class="text-xs font-extrabold text-emerald-600 dark:text-emerald-400">
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
                  <div class="inline-flex items-center gap-3">
                    <a href="{{ route('elearning.invoice.print', $el->id) }}" target="_blank" class="text-xs font-bold text-gray-500 hover:text-gray-800 dark:hover:text-gray-200 transition">
                      Cetak Struk
                    </a>
                    @if($el->elearning)
                      <a href="{{ route('public.elearning.show', $el->elearning->slug) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-600 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300 transition group">
                        <span>Buka Kelas</span>
                        <svg class="w-4 h-4 transition-transform duration-150 group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"></path>
                        </svg>
                      </a>
                    @endif
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="py-12 text-center space-y-3">
                  <h3 class="text-base font-extrabold text-gray-900 dark:text-white">Belum Ada Transaksi E-Learning</h3>
                  <p class="text-xs text-gray-500">Materi e-learning berbayar yang telah Anda beli akan tercatat rapi di sini.</p>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <!-- 3. Table Container: Pendaftaran Lomba Foto -->
      <div x-show="activeTab === 'contest'" class="overflow-x-auto" style="display: none;">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-[11px] font-extrabold uppercase tracking-wider text-gray-500 dark:text-gray-400">
              <th class="py-3.5 px-4 sm:px-6">Kode Registrasi</th>
              <th class="py-3.5 px-4 sm:px-6">Event Lomba Foto</th>
              <th class="py-3.5 px-4 sm:px-6">Penyelenggara</th>
              <th class="py-3.5 px-4 sm:px-6">Biaya Pendaftaran</th>
              <th class="py-3.5 px-4 sm:px-6 text-center">Status Pembayaran</th>
              <th class="py-3.5 px-4 sm:px-6 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60 text-xs">
            @forelse($contestRegistrations as $cr)
              <tr class="invoice-row hover:bg-amber-50/30 dark:hover:bg-amber-950/20 transition">
                <!-- Kode Registrasi -->
                <td class="py-4 px-4 sm:px-6 align-middle font-mono font-bold text-gray-900 dark:text-white">
                  REG-LMB-{{ str_pad($cr->id, 4, '0', STR_PAD_LEFT) }}
                  <span class="block text-[10px] font-normal text-gray-400 mt-0.5">
                    {{ $cr->created_at->format('d M Y, H:i') }}
                  </span>
                </td>

                <!-- Event Lomba Foto -->
                <td class="py-4 px-4 sm:px-6 align-middle">
                  <span class="font-extrabold text-gray-900 dark:text-white block">
                    {{ $cr->contest->judul_lomba ?? 'Event Lomba Foto' }}
                  </span>
                  <span class="text-[11px] text-gray-500 capitalize">
                    Kategori: {{ $cr->contest->kategori ?? '-' }}
                  </span>
                </td>

                <!-- Penyelenggara -->
                <td class="py-4 px-4 sm:px-6 align-middle font-medium text-gray-700 dark:text-gray-300">
                  {{ $cr->contest->penyelenggara ?? 'LensMatch Official' }}
                </td>

                <!-- Biaya Pendaftaran -->
                <td class="py-4 px-4 sm:px-6 align-middle font-extrabold text-gray-900 dark:text-white whitespace-nowrap">
                  Rp {{ number_format($cr->contest->biaya_pendaftaran ?? 0, 0, ',', '.') }}
                </td>

                <!-- Status Pembayaran -->
                <td class="py-4 px-4 sm:px-6 align-middle text-center whitespace-nowrap">
                  @if($cr->status_pembayaran === 'lunas')
                    <span class="text-xs font-extrabold text-emerald-600 dark:text-emerald-400">
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

                <!-- Aksi -->
                <td class="py-4 px-4 sm:px-6 align-middle text-center whitespace-nowrap">
                  @if($cr->contest)
                    <a href="{{ route('public.contests.show', $cr->contest->id) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-600 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300 transition group">
                      <span>Detail Event</span>
                      <svg class="w-4 h-4 transition-transform duration-150 group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"></path>
                      </svg>
                    </a>
                  @else
                    <span class="text-xs text-gray-400">-</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="py-12 text-center space-y-3">
                  <h3 class="text-base font-extrabold text-gray-900 dark:text-white">Belum Ada Pendaftaran Lomba Berbayar</h3>
                  <p class="text-xs text-gray-500">Seluruh struk pembayaran pendaftaran event lomba foto Anda akan muncul di tab ini.</p>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

    </div>

  </div>
</section>
@endsection