@extends('layouts.admin')

@section('title', 'Admin Dashboard — Marketplace Fotografer')

@section('content')
<div class="space-y-6">

  <!-- 1. Header Row -->
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
      <h1 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">Dashboard</h1>
    </div>
    
    <div class="flex items-center gap-2">
      <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700/80 shadow-2xs text-xs font-bold text-gray-700 dark:text-gray-300">
        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
        </svg>
        <span>Tahun Operasional: {{ date('Y') }}</span>
      </div>
    </div>
  </div>

  <!-- 2. Actionable Operational Queue (Alert Cards) -->
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <!-- Action 1: Pending Studio Verification -->
    <a href="{{ route('admin.users') }}" class="group p-4 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-xs hover:border-amber-400 dark:hover:border-amber-500 transition flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
        </div>
        <div>
          <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">{{ $pendingVerifications }} Verifikasi Fotografer</p>
          <span class="text-[11px] text-gray-500 dark:text-gray-400 font-medium">Menunggu persetujuan KTP/Porto</span>
        </div>
      </div>
      <svg class="w-4 h-4 text-gray-400 group-hover:text-amber-500 group-hover:translate-x-0.5 transition shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
    </a>

    <!-- Action 2: Ready Payouts -->
    <a href="{{ route('admin.transactions') }}" class="group p-4 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-xs hover:border-amber-400 dark:hover:border-amber-500 transition flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
          <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">{{ $pendingPayouts }} Dana Siap Tarik</p>
          <span class="text-[11px] text-gray-500 dark:text-gray-400 font-medium">Sesi selesai & verifikasi Klien</span>
        </div>
      </div>
      <svg class="w-4 h-4 text-gray-400 group-hover:text-amber-500 group-hover:translate-x-0.5 transition shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
    </a>

    <!-- Action 3: Contest Submissions to Review -->
    <a href="{{ route('admin.contests') }}" class="group p-4 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-xs hover:border-amber-400 dark:hover:border-amber-500 transition flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.504-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007-7.5V4.875a1.875 1.875 0 00-1.875-1.875h-1.5a1.875 1.875 0 00-1.875 1.875V6.75m6.75 0a3 3 0 00-3-3h-1.5a3 3 0 00-3 3m6.75 0v3.375c0 .621-.504 1.125-1.125 1.125h-.871M7.5 6.75v3.375c0 .621.504 1.125 1.125 1.125h.872"/></svg>
        </div>
        <div>
          <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">{{ $pendingContestReviews }} Submisi Lomba</p>
          <span class="text-[11px] text-gray-500 dark:text-gray-400 font-medium">Penjurian karya peserta foto</span>
        </div>
      </div>
      <svg class="w-4 h-4 text-gray-400 group-hover:text-amber-500 group-hover:translate-x-0.5 transition shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
    </a>
  </div>

  <!-- 3. Top 4 Core Financial & System Metric Cards (Strictly Following Rules #7, #8, #9, #18) -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <!-- Card 1: Total User Registrasi -->
    <div class="p-6 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-xs space-y-2">
      <span class="text-[11px] text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider block">Total User Registrasi</span>
      <p class="text-lg sm:text-xl font-black text-gray-900 dark:text-white leading-tight">{{ number_format($totalUsers, 0, ',', '.') }}</p>
    </div>

    <!-- Card 2: Fotografer Terverifikasi -->
    <div class="p-6 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-xs space-y-2">
      <span class="text-[11px] text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider block">Fotografer Terverifikasi</span>
      <p class="text-lg sm:text-xl font-black text-gray-900 dark:text-white leading-tight">{{ $verifiedPhotographers }}</p>
    </div>

    <!-- Card 3: Total Kontrak Booking -->
    <div class="p-6 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-xs space-y-2">
      <span class="text-[11px] text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider block">Total Kontrak Booking</span>
      <p class="text-lg sm:text-xl font-black text-gray-900 dark:text-white leading-tight">{{ number_format($totalContracts, 0, ',', '.') }}</p>
    </div>

    <!-- Card 4: Total Omzet Transaksi -->
    <div class="p-6 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-xs space-y-2">
      <span class="text-[11px] text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider block">Total Omzet Transaksi</span>
      <p class="text-lg sm:text-xl font-black text-gray-900 dark:text-white leading-tight">Rp {{ number_format($grossRevenue, 0, ',', '.') }}</p>
    </div>
  </div>

  <!-- 4. Section: Tren Omzet & Transaksi Bulanan -->
  <div class="w-full bg-white dark:bg-gray-800 p-6 sm:p-7 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-xs space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-gray-100 dark:border-gray-700/60">
      <div>
        <h2 class="text-base font-extrabold text-gray-900 dark:text-white tracking-tight">Tren Omzet & Transaksi Bulanan</h2>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Perkembangan Gross Merchandise Value (GMV) per bulan (dalam Juta Rupiah)</p>
      </div>
      <div class="flex items-center gap-4 text-xs font-bold shrink-0">
        <div class="flex items-center gap-1.5 text-gray-900 dark:text-white">
          <span class="w-3.5 h-2 rounded-xs bg-amber-400"></span>
          <span>Total Omzet (GMV)</span>
        </div>
        <div class="flex items-center gap-1.5 text-gray-500 dark:text-gray-400">
          <span class="w-3.5 h-2 rounded-xs bg-gray-800 dark:bg-gray-300"></span>
          <span>Komisi Platform (10%)</span>
        </div>
      </div>
    </div>

    <!-- Vector SVG Area/Bar Visual Chart -->
    <div class="h-64 sm:h-72 w-full flex flex-col justify-between pt-2">
      <div class="h-full w-full relative flex items-end justify-between gap-2 sm:gap-4 px-1 pb-6">
        
        <!-- Background Horizontal Scale Lines -->
        <div class="absolute inset-0 flex flex-col justify-between pointer-events-none pb-6 opacity-30 dark:opacity-20">
          <div class="border-b border-dashed border-gray-300 dark:border-gray-600 w-full"></div>
          <div class="border-b border-dashed border-gray-300 dark:border-gray-600 w-full"></div>
          <div class="border-b border-dashed border-gray-300 dark:border-gray-600 w-full"></div>
          <div class="border-b border-dashed border-gray-300 dark:border-gray-600 w-full"></div>
        </div>

        <!-- 12 Monthly Bar Columns -->
        @foreach($months as $idx => $m)
          @php
            $val = $monthlyRevenue[$idx] ?? 0;
            $fee = $monthlyCommission[$idx] ?? 0;
            $heightPercent = $val > 0 ? min(100, max(10, ($val / 75) * 100)) : 3;
          @endphp
          <div class="flex-1 flex flex-col items-center h-full justify-end group relative z-10">
            <!-- Tooltip on Hover -->
            <div class="opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none absolute -top-12 bg-gray-900 text-white text-[10px] font-extrabold px-2.5 py-1.5 rounded-lg shadow-xl whitespace-nowrap z-30">
              <span>Omzet: Rp {{ $val }} Jt</span>
              <span class="block text-amber-300 font-semibold text-[9px]">Fee: Rp {{ $fee }} Jt</span>
            </div>
            
            <!-- Column Bars (1 Solid Color: Amber) -->
            <div class="w-full max-w-[28px] bg-amber-400 group-hover:bg-amber-500 transition-all duration-300 rounded-t-lg" style="height: {{ $heightPercent }}%;"></div>
            
            <!-- Month Label -->
            <span class="text-[10px] font-bold text-gray-500 dark:text-gray-400 mt-2 absolute -bottom-5">{{ $m }}</span>
          </div>
        @endforeach
      </div>
    </div>
    
    <!-- Summary Bar Bottom Stats -->
    <div class="grid grid-cols-3 gap-2 pt-4 text-center border-t border-gray-100 dark:border-gray-700/60">
      <div>
        <span class="text-[10px] font-bold text-gray-400 uppercase">Rata-Rata Bulanan</span>
        <p class="text-sm font-black text-gray-900 dark:text-white mt-0.5">Rp {{ $avgMonthlyRevenue }} Juta</p>
      </div>
      <div>
        <span class="text-[10px] font-bold text-gray-400 uppercase">Bulan Tertinggi</span>
        <p class="text-sm font-black text-gray-900 dark:text-white mt-0.5">Rp {{ $maxMonthlyRevenue }} Juta</p>
      </div>
      <div>
        <span class="text-[10px] font-bold text-gray-400 uppercase">Akumulasi Komisi</span>
        <p class="text-sm font-black text-gray-900 dark:text-white mt-0.5">Rp {{ $totalCommission }} Juta</p>
      </div>
    </div>
  </div>

  <!-- 5. Section: Kategori Sesi Terlaris (Dedicated Section Below Chart) -->
  <div class="w-full bg-white dark:bg-gray-800 p-6 sm:p-7 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-xs space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b border-gray-100 dark:border-gray-700/60">
      <div>
        <h2 class="text-base font-extrabold text-gray-900 dark:text-white tracking-tight">Kategori Sesi Terlaris</h2>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Pangsa pasar & minat pesanan sesi foto klien</p>
      </div>

      <!-- Pangsa Pasar Tertinggi Badge -->
      <div class="px-3.5 py-1.5 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60 flex items-center gap-2 self-start sm:self-auto">
        <span class="text-[10px] font-black uppercase tracking-wider text-amber-700 dark:text-amber-400">Pangsa Pasar Tertinggi:</span>
        <span class="text-xs text-gray-800 dark:text-gray-200 font-bold">Wedding & Prewedding (54%)</span>
      </div>
    </div>

    <!-- Categories Progress Bars Responsive Grid (3 Columns) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
      @foreach($categoryStats as $cat)
        <div class="p-3.5 rounded-xl bg-gray-50/80 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-700/60 space-y-2">
          <div class="flex items-center justify-between text-xs">
            <span class="font-bold text-gray-800 dark:text-gray-200 truncate pr-2">{{ $cat['name'] }}</span>
            <span class="font-black text-gray-900 dark:text-white shrink-0">{{ $cat['share'] }}% <span class="text-gray-400 font-semibold text-[11px]">({{ $cat['count'] }})</span></span>
          </div>
          <div class="w-full bg-gray-200/80 dark:bg-gray-700 h-2 rounded-full overflow-hidden">
            <div class="{{ $cat['color'] }} h-full rounded-full transition-all duration-500" style="width: {{ $cat['share'] }}%"></div>
          </div>
        </div>
      @endforeach
    </div>
  </div>

  <!-- 6. Bottom Section: Recent Transactions (8 cols) + Top Performing Photographers (4 cols) -->
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
    
    <!-- Left: 8 Cols - Recent Transactions Table -->
    <div class="lg:col-span-8">
      @include('partials.table.table-01')
    </div>

    <!-- Right: 4 Cols - Top Performing Studios -->
    <div class="lg:col-span-4 bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-xs space-y-4">
      <div>
        <h2 class="text-base font-extrabold text-gray-900 dark:text-white tracking-tight">Fotografer Unggulan</h2>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Rating & performa tertinggi</p>
      </div>

      <!-- Top Studios List -->
      <div class="divide-y divide-gray-100 dark:divide-gray-700/60">
        @foreach($topPhotographers as $photo)
          @php
            $avatarPhoto = $photo->foto ? (str_starts_with($photo->foto, 'http') ? $photo->foto : asset('storage/' . $photo->foto)) : null;
            $photoRating = $photo->rating_average;
            $sessionsCount = $photo->contracts()->where('status_contract', 'completed')->count();
          @endphp
          <div class="py-3 flex items-center justify-between gap-3 first:pt-0 last:pb-0">
            <div class="flex items-center gap-3 min-w-0">
              @if($avatarPhoto)
                <div class="w-10 h-10 rounded-xl overflow-hidden shrink-0 border border-gray-100 dark:border-gray-700">
                  <img src="{{ $avatarPhoto }}" 
                       alt="{{ $photo->nama }}" 
                       class="w-full h-full object-cover" />
                </div>
              @else
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-amber-300 text-white font-black text-xs flex items-center justify-center shrink-0 shadow-2xs">
                  {{ strtoupper(substr($photo->nama ?? 'F', 0, 2)) }}
                </div>
              @endif
              <div class="min-w-0">
                <p class="text-xs font-bold text-gray-900 dark:text-white truncate">{{ $photo->nama }}</p>
                <p class="text-[11px] text-gray-500 dark:text-gray-400 truncate">{{ $photo->city ? $photo->city->name : '-' }}</p>
              </div>
            </div>
            <div class="text-right shrink-0">
              <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 text-[10px] font-black">
                @if($photoRating)
                  <svg class="w-3 h-3 fill-amber-400 text-amber-400" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                  <span>{{ $photoRating }}</span>
                @else
                  <span>Baru</span>
                @endif
              </div>
              <span class="block text-[10px] text-gray-400 font-semibold mt-0.5">{{ $sessionsCount }} Sesi</span>
            </div>
          </div>
        @endforeach
      </div>

      <div class="pt-2 flex justify-end">
        <a href="{{ route('admin.photographers') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs rounded-xl shadow-xs transition">
          <span>Kelola Fotografer</span>
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
        </a>
      </div>
    </div>

  </div>

</div>
@endsection