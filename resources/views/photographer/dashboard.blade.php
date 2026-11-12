@extends('layouts.photographer')

@section('title', 'Dashboard Fotografer — Marketplace Fotografer')

@section('content')
<div class="space-y-6">

  <!-- 1. Header Row -->
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
      <h1 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">Dashboard</h1>
    </div>
    
    <div class="flex flex-wrap items-center gap-3">
      @if($photographer->isPublishedToCatalog())
        <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">Tayang di Katalog</span>
      @endif

      <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700/80 shadow-2xs text-xs font-bold text-gray-700 dark:text-gray-300">
        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
        </svg>
        <span>Tahun Operasional: {{ date('Y') }}</span>
      </div>
    </div>
  </div>

  @php
    $storefrontChecklist = $storefrontChecklist ?? $photographer->getStorefrontChecklist();
  @endphp

  @if($photographer->isFeatureUnlocked() && !$photographer->isPublishedToCatalog())
    <div class="bg-white dark:bg-gray-800 p-6 sm:p-7 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-xs space-y-5">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <div>
          <h2 class="text-base font-extrabold text-gray-900 dark:text-white tracking-tight">Persiapan Tayang Etalase</h2>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
            @if($photographer->isStorefrontPending())
              Pengajuan tayang Anda sedang ditinjau oleh Admin sebelum muncul di katalog publik.
            @else
              Lengkapi paket layanan, portofolio, dan jadwal agar etalase dapat diajukan tayang di katalog publik.
            @endif
          </p>
        </div>

        <div class="shrink-0 text-xs font-bold">
          @if($photographer->isStorefrontPending())
            <span class="text-amber-600 dark:text-amber-400">Menunggu Review Admin</span>
          @elseif($photographer->isStorefrontRejected())
            <span class="text-rose-600 dark:text-rose-400">Etalase Ditolak</span>
          @elseif($storefrontChecklist['is_ready'])
            <span class="text-emerald-600 dark:text-emerald-400">Siap Diajukan</span>
          @else
            <span class="text-gray-500 dark:text-gray-400">{{ $storefrontChecklist['completed_count'] }}/{{ $storefrontChecklist['total_count'] }} Lengkap</span>
          @endif
        </div>
      </div>

      @if($photographer->isStorefrontRejected())
        <p class="text-xs leading-relaxed text-gray-600 dark:text-gray-300">
          <span class="font-bold text-rose-600 dark:text-rose-400">Catatan Admin:</span> {{ trim((string) ($photographer->catatan_penolakan_etalase ?? '')) ?: 'Etalase belum memenuhi syarat penayangan.' }}
        </p>
      @endif

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        @php
          $stepRoutes = [
              'services' => [
                  'route' => route('photographer.services'),
                  'desc' => 'Minimal 1 paket layanan aktif dengan tarif dan deskripsi.',
                  'done_label' => 'Kelola',
                  'todo_label' => 'Buat Paket',
              ],
              'portfolios' => [
                  'route' => route('photographer.portfolio'),
                  'desc' => 'Minimal 1 album portofolio berisi karya foto.',
                  'done_label' => 'Kelola',
                  'todo_label' => 'Unggah Karya',
              ],
              'schedule' => [
                  'route' => route('photographer.availability', ['tab' => 'weekly']),
                  'desc' => 'Tentukan hari buka dan simpan jam operasional pada menu Jadwal.',
                  'done_label' => 'Ubah',
                  'todo_label' => 'Atur Jadwal',
              ],
          ];
        @endphp

        @foreach($storefrontChecklist['items'] as $stepKey => $stepItem)
          @php $meta = $stepRoutes[$stepKey]; @endphp
          <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-700/70 bg-gray-50/70 dark:bg-gray-900/40 flex flex-col justify-between gap-3">
            <div class="space-y-1.5">
              <div class="flex items-center justify-between gap-2">
                <span class="text-xs font-extrabold text-gray-900 dark:text-white">{{ $stepItem['label'] }}</span>
                @if($stepItem['done'])
                  <span class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400">Lengkap</span>
                @else
                  <span class="text-[11px] font-bold text-gray-400 dark:text-gray-500">Belum</span>
                @endif
              </div>
              <p class="text-[11px] text-gray-500 dark:text-gray-400 leading-relaxed">{{ $meta['desc'] }}</p>
            </div>

            <div>
              <a href="{{ $meta['route'] }}" class="text-xs font-extrabold text-amber-600 dark:text-amber-400 hover:text-amber-500 underline underline-offset-4 decoration-amber-400 transition">
                {{ $stepItem['done'] ? $meta['done_label'] : $meta['todo_label'] }}
              </a>
            </div>
          </div>
        @endforeach
      </div>

      @if(!$photographer->isStorefrontPending())
        <div class="flex flex-wrap items-center justify-end gap-3 pt-1">
          @if(!$storefrontChecklist['is_ready'])
            <span class="text-xs text-gray-400 dark:text-gray-500">
              Lengkapi {{ implode(', ', $storefrontChecklist['missing_labels']) }} sebelum mengajukan tayang.
            </span>
          @endif

          <form action="{{ route('photographer.storefront.submit') }}"
                method="POST"
                @if($storefrontChecklist['is_ready'])
                  data-confirm="true"
                  data-confirm-title="Ajukan Tayang Etalase?"
                  data-confirm-message="Admin akan meninjau paket layanan, portofolio, dan jadwal Anda sebelum profil tampil di katalog publik."
                  data-confirm-btn="Ya, Ajukan Tayang"
                @endif>
            @csrf
            <button type="submit"
                    @if(!$storefrontChecklist['is_ready']) disabled @endif
                    class="{{ $storefrontChecklist['is_ready'] ? 'px-5 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs rounded-xl shadow-xs transition cursor-pointer' : 'px-5 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500 font-bold text-xs rounded-xl cursor-not-allowed' }}">
              {{ $photographer->isStorefrontRejected() ? 'Ajukan Ulang Tayang' : 'Ajukan Tayang' }}
            </button>
          </form>
        </div>
      @endif
    </div>
  @endif

  <!-- Pengingat Sesi Pintar H-1 & Hari-H (Smart Reminder & Countdown) -->
  @include('partials.shared.upcoming-session-banner', ['urgentSession' => $urgentSession, 'role' => 'photographer'])



  <!-- 2. Actionable Operational Queue (3 Alert Cards Sesuai Standar Proyek) -->
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    
    <!-- Action 1: Booking Masuk Perlu Konfirmasi -->
    <a href="{{ route('photographer.bookings', ['status' => 'pending']) }}" class="group p-4 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-xs hover:border-amber-400 dark:hover:border-amber-500 transition flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
        </div>
        <div>
          <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">{{ $pendingBookingsCount }} Order Perlu Konfirmasi</p>
          <span class="text-[11px] text-gray-500 dark:text-gray-400 font-medium">Permintaan booking baru dari klien</span>
        </div>
      </div>
      <svg class="w-4 h-4 text-gray-400 group-hover:text-amber-500 group-hover:translate-x-0.5 transition shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
    </a>

    <!-- Action 2: Galeri Foto Harus Diunggah -->
    <a href="{{ route('photographer.bookings', ['status' => 'confirmed']) }}" class="group p-4 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-xs hover:border-amber-400 dark:hover:border-amber-500 transition flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
        </div>
        <div>
          <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">{{ $needGalleryUploadCount }} Sesi Siap Kirim Foto</p>
          <span class="text-[11px] text-gray-500 dark:text-gray-400 font-medium">Unggah link Google Drive hasil edit</span>
        </div>
      </div>
      <svg class="w-4 h-4 text-gray-400 group-hover:text-amber-500 group-hover:translate-x-0.5 transition shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
    </a>

    <!-- Action 3: Dana Siap Ditarik -->
    <a href="{{ route('photographer.reports') }}" class="group p-4 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-xs hover:border-amber-400 dark:hover:border-amber-500 transition flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
          <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">{{ $readyPayoutCount }} Payout Dalam Proses</p>
          <span class="text-[11px] text-gray-500 dark:text-gray-400 font-medium">Saldo siap dicairkan</span>
        </div>
      </div>
      <svg class="w-4 h-4 text-gray-400 group-hover:text-amber-500 group-hover:translate-x-0.5 transition shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
    </a>

  </div>

  <!-- 3. Top 4 Core Financial & Performance Metric Cards (Strictly Following Rules #7, #8, #9, #18) -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    
    <!-- Card 1: Total Omzet Bersih -->
    <div class="p-6 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-xs space-y-2">
      <span class="text-xs text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider block">Total Pendapatan Bersih</span>
      <p class="text-xs font-black text-gray-900 dark:text-white tracking-tight">Rp {{ number_format($totalNetRevenue, 0, ',', '.') }}</p>
    </div>

    <!-- Card 2: Total Kontrak Booking -->
    <div class="p-6 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-xs space-y-2">
      <span class="text-xs text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider block">Total Kontrak Booking</span>
      <p class="text-xs font-black text-gray-900 dark:text-white tracking-tight">{{ $totalBookingsCount }} Order</p>
    </div>

    <!-- Card 3: Kepuasan Klien Klien -->
    <div class="p-6 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-xs space-y-2">
      <span class="text-xs text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider block">Kepuasan Klien</span>
      <div class="flex items-center gap-1.5">
        <p class="text-xs font-black text-gray-900 dark:text-white tracking-tight">{{ $avgRating }}</p>
        <div class="flex items-center text-amber-400">
          <svg class="w-3.5 h-3.5 fill-amber-400 text-amber-400" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
        </div>
      </div>
    </div>

    <!-- Card 4: Katalog & Portofolio Aktif -->
    <div class="p-6 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-xs space-y-2">
      <span class="text-xs text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider block">Galeri Portofolio Aktif</span>
      <p class="text-xs font-black text-gray-900 dark:text-white tracking-tight">{{ $totalServices }} Paket</p>
    </div>

  </div>

  <!-- Reputasi & Metrik Keunggulan Fotografer (README Rule #19 & Rule #84 Compliant) -->
  <div class="bg-white dark:bg-gray-800 p-6 sm:p-7 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-xs space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
      <div>
        <h2 class="text-base font-extrabold text-gray-900 dark:text-white tracking-tight">Reputasi & Metrik Keunggulan Fotografer</h2>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Pantau pencapaian indikator performa akun Anda untuk meningkatkan reputasi di katalog publik.</p>
      </div>
      @if($photographer->isPublishedToCatalog())
        <a href="{{ route('public.photographers.show', $photographer->id) }}" target="_blank" class="text-xs font-bold text-gray-600 dark:text-gray-300 hover:text-amber-500 transition inline-flex items-center gap-1.5 shrink-0">
          <span>Lihat Profil Publik</span>
          <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
        </a>
      @endif
    </div>

    @php
      $isTopRated = ($avgRating >= 4.8 && $totalReviews >= 5);
      $isHighFlight = ($completedBookingsCount >= 10);
      $responseStats = $responseStats ?? $photographer->getClientResponseStats();
      $isFastResponse = $responseStats['is_fast_response'];
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <!-- Metrik 1: Rating Fotografer -->
      <div class="p-4 sm:p-5 rounded-xl border border-gray-100 dark:border-gray-700/70 bg-gray-50/70 dark:bg-gray-900/40 space-y-3">
        <div class="flex items-center justify-between">
          <span class="text-xs font-bold text-gray-900 dark:text-white">Rating Fotografer</span>
          @if($isTopRated)
            <span class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400">Tercapai</span>
          @else
            <span class="text-[11px] font-medium text-gray-400 dark:text-gray-500">Belum Tercapai</span>
          @endif
        </div>
        <p class="text-[11px] text-gray-500 dark:text-gray-400 leading-relaxed font-medium">
          Syarat: Rating &ge; 4.8 dari minimal 5 ulasan klien.
        </p>
        <div class="space-y-1.5 pt-1">
          <div class="flex items-center justify-between text-[11px] text-gray-500 dark:text-gray-400 font-medium">
            <span>Progres saat ini:</span>
            <span><strong class="font-bold text-gray-900 dark:text-white">{{ $avgRating }}</strong> / 5.0 (<strong class="font-bold text-gray-900 dark:text-white">{{ $totalReviews }}</strong> ulasan)</span>
          </div>
          <div class="w-full bg-gray-200 dark:bg-gray-700 h-1.5 rounded-full overflow-hidden">
            <div class="bg-amber-400 h-full rounded-full transition-all duration-300" style="width: {{ min(100, round(($totalReviews / 5) * 100)) }}%"></div>
          </div>
        </div>
      </div>

      <!-- Metrik 2: Jam Terbang Sesi -->
      <div class="p-4 sm:p-5 rounded-xl border border-gray-100 dark:border-gray-700/70 bg-gray-50/70 dark:bg-gray-900/40 space-y-3">
        <div class="flex items-center justify-between">
          <span class="text-xs font-bold text-gray-900 dark:text-white">Jam Terbang Sesi</span>
          @if($isHighFlight)
            <span class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400">Tercapai</span>
          @else
            <span class="text-[11px] font-medium text-gray-400 dark:text-gray-500">Belum Tercapai</span>
          @endif
        </div>
        <p class="text-[11px] text-gray-500 dark:text-gray-400 leading-relaxed font-medium">
          Syarat: Menyelesaikan minimal 10 sesi pemotretan sukses.
        </p>
        <div class="space-y-1.5 pt-1">
          <div class="flex items-center justify-between text-[11px] text-gray-500 dark:text-gray-400 font-medium">
            <span>Sesi selesai:</span>
            <span><strong class="font-bold text-gray-900 dark:text-white">{{ $completedBookingsCount }}</strong> / 10 sesi</span>
          </div>
          <div class="w-full bg-gray-200 dark:bg-gray-700 h-1.5 rounded-full overflow-hidden">
            <div class="bg-amber-400 h-full rounded-full transition-all duration-300" style="width: {{ min(100, round(($completedBookingsCount / 10) * 100)) }}%"></div>
          </div>
        </div>
      </div>

      <!-- Metrik 3: Kecepatan Respon -->
      <div class="p-4 sm:p-5 rounded-xl border border-gray-100 dark:border-gray-700/70 bg-gray-50/70 dark:bg-gray-900/40 space-y-3">
        <div class="flex items-center justify-between">
          <span class="text-xs font-bold text-gray-900 dark:text-white">Kecepatan Respon</span>
          @if($isFastResponse)
            <span class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400">Tercapai</span>
          @else
            <span class="text-[11px] font-medium text-gray-400 dark:text-gray-500">Belum Tercapai</span>
          @endif
        </div>
        <p class="text-[11px] text-gray-500 dark:text-gray-400 leading-relaxed font-medium">
          Syarat: Rata-rata balas chat klien &le; 24 jam tanpa tunggakan order pending &gt; 24 jam.
        </p>
        <div class="space-y-1.5 pt-1">
          <div class="flex items-center justify-between gap-2 text-[11px] text-gray-500 dark:text-gray-400 font-medium">
            <span>Status respon:</span>
            @if($responseStats['overdue_booking_count'] > 0)
              <span class="font-bold text-rose-600 dark:text-rose-400 text-right">{{ $responseStats['overdue_booking_count'] }} order pending &gt; 24 jam</span>
            @elseif($responseStats['overdue_chat_count'] > 0)
              <span class="font-bold text-rose-600 dark:text-rose-400 text-right">{{ $responseStats['overdue_chat_count'] }} chat belum dibalas &gt; 24 jam</span>
            @elseif($responseStats['responded_chat_count'] === 0)
              <span class="font-bold text-gray-500 dark:text-gray-400 text-right">Belum ada chat klien</span>
            @elseif($isFastResponse)
              <span class="font-bold text-gray-900 dark:text-white text-right">Rata-rata {{ $responseStats['avg_reply_label'] }}</span>
            @else
              <span class="font-bold text-gray-500 dark:text-gray-400 text-right">Rata-rata {{ $responseStats['avg_reply_label'] }} (&gt; 24 jam)</span>
            @endif
          </div>
          <div class="w-full bg-gray-200 dark:bg-gray-700 h-1.5 rounded-full overflow-hidden">
            <div class="{{ $isFastResponse ? 'bg-amber-400' : 'bg-gray-300 dark:bg-gray-600' }} h-full rounded-full transition-all duration-300" style="width: {{ $isFastResponse ? '100%' : '0%' }}"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Live Preview Tampilan Publik (Format Metrik Inline Teks) -->
    <div class="p-4 rounded-xl bg-gray-50/70 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-700/60 flex items-center justify-between flex-wrap gap-3">
      <div class="space-y-1">
        <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 block">Pratinjau di Katalog Publik:</span>
        <div class="flex items-center flex-wrap gap-x-2.5 gap-y-1 text-xs text-gray-600 dark:text-gray-300 font-medium">
          @php $metrics = $photographer->editorial_metrics; @endphp
          @foreach($metrics as $idx => $m)
            @if($idx > 0)
              <span class="text-gray-300 dark:text-gray-600 select-none">•</span>
            @endif
            @if($m['type'] === 'rating')
              <span class="font-bold text-gray-900 dark:text-white flex items-center gap-1">
                <span class="{{ $m['highlight'] ? 'text-amber-500' : 'text-gray-300 dark:text-gray-600' }}">★</span>
                <span>{{ $m['text'] }}</span>
              </span>
            @elseif($m['type'] === 'fast_response')
              <span class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ $m['text'] }}</span>
            @else
              <span>{{ $m['text'] }}</span>
            @endif
          @endforeach
        </div>
      </div>
      <span class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">Tampil otomatis di profil dan kartu katalog publik</span>
    </div>
  </div>

  <!-- 4. Middle Section: Monthly Revenue Chart + Service Packages Demand Breakdown -->
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    
    <!-- Left: 8 Cols - Monthly Net Revenue Chart -->
    <div class="lg:col-span-8 bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-xs space-y-5">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <div>
          <h2 class="text-base font-extrabold text-gray-900 dark:text-white tracking-tight">Tren Pendapatan Bersih Bulanan</h2>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Perkembangan pemasukan bersih fotografer per bulan (dalam Juta Rupiah)</p>
        </div>
        <div class="flex items-center gap-4 text-xs font-bold">
          <div class="flex items-center gap-1.5 text-gray-900 dark:text-white">
            <span class="w-3.5 h-2 rounded-xs bg-amber-400"></span>
            <span>Pendapatan Bersih (90%)</span>
          </div>
        </div>
      </div>

      <!-- Vector SVG Bar Visual Chart -->
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
              $maxRef = max(array_values($monthlyRevenue)) ?: 20;
              $heightPercent = min(100, max(12, ($val / $maxRef) * 100));
            @endphp
            <div class="flex-1 flex flex-col items-center h-full justify-end group relative z-10">
              <!-- Tooltip on Hover -->
              <div class="opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none absolute -top-10 bg-gray-900 text-white text-[10px] font-extrabold px-2.5 py-1.5 rounded-lg shadow-xl whitespace-nowrap z-30">
                <span>Pendapatan: Rp {{ number_format($val, 1, ',', '.') }} Jt</span>
              </div>
              
              <!-- Column Bars (1 Solid Color: Amber) -->
              <div class="w-full max-w-[28px] bg-amber-400 group-hover:bg-amber-500 transition-all duration-300 rounded-t-lg" style="height: {{ $heightPercent }}%;"></div>
              
              <!-- Month Label -->
              <span class="text-[10px] font-bold text-gray-500 dark:text-gray-400 mt-2 absolute -bottom-5">{{ $m }}</span>
            </div>
          @endforeach
        </div>
      </div>
      
      <!-- Summary Bar Bottom Stats (Clean Whitespace without Top Border) -->
      <div class="grid grid-cols-3 gap-2 pt-4 text-center">
        <div>
          <span class="text-xs font-bold text-gray-400 uppercase">Rata-Rata Bulanan</span>
          <p class="text-xs font-black text-gray-900 dark:text-white mt-0.5">Rp {{ $avgMonthlyRevenue }} Juta</p>
        </div>
        <div>
          <span class="text-xs font-bold text-gray-400 uppercase">Bulan Tertinggi</span>
          <p class="text-xs font-black text-gray-900 dark:text-white mt-0.5">Rp {{ $maxMonthlyRevenue }} Juta</p>
        </div>
        <div>
          <span class="text-xs font-bold text-gray-400 uppercase">Total Akumulasi</span>
          <p class="text-xs font-black text-gray-900 dark:text-white mt-0.5">Rp {{ $totalAccumulatedRevenue }} Juta</p>
        </div>
      </div>
    </div>

    <!-- Right: 4 Cols - Service Packages Breakdown -->
    <div class="lg:col-span-4 bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-xs flex flex-col justify-between">
      <div class="space-y-4">
        <div>
          <h2 class="text-base font-extrabold text-gray-900 dark:text-white tracking-tight">Katalog Paket Layanan</h2>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Daftar tarif & porsi penawaran aktif</p>
        </div>

        <!-- Service Progress Bars -->
        <div class="space-y-3.5 pt-1">
          @foreach($serviceStats as $srv)
            <div class="space-y-1.5">
              <div class="flex items-center justify-between text-xs">
                <span class="font-bold text-gray-800 dark:text-gray-200 truncate max-w-[170px]" title="{{ $srv['name'] }}">{{ $srv['name'] }}</span>
                <span class="font-black text-gray-900 dark:text-white shrink-0">Rp {{ number_format($srv['price'] / 1000, 0, ',', '.') }}rb</span>
              </div>
              <div class="w-full bg-gray-100 dark:bg-gray-700 h-2.5 rounded-full overflow-hidden">
                <div class="{{ $srv['color'] }} h-full rounded-full transition-all duration-500" style="width: {{ $srv['share'] }}%"></div>
              </div>
            </div>
          @endforeach
        </div>
      </div>

      <div class="mt-5 flex justify-end">
        <a href="{{ route('photographer.services') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs rounded-xl shadow-xs transition">
          <span>Kelola Paket Layanan</span>
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
        </a>
      </div>
    </div>

  </div>

  <!-- 5. Bottom Section: Recent Booking Orders (8 cols) + Upcoming Sessions Schedule (4 cols) -->
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
    
    <!-- Left: 8 Cols - Recent Bookings Table -->
    <div class="lg:col-span-8 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-xs overflow-hidden p-6 space-y-4">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-base font-extrabold text-gray-900 dark:text-white tracking-tight">Order Booking Masuk Terkini</h2>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Pemantauan reservasi & status transaksi klien</p>
        </div>
        @if(($totalBookingsCount ?? 0) > 5)
          <a href="{{ route('photographer.bookings') }}" class="text-xs font-extrabold text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 hover:underline inline-flex items-center gap-1 transition">
            <span>Lihat Semua</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
          </a>
        @endif
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full text-left">
          <thead>
            <tr class="border-gray-100 border-y dark:border-gray-800 text-[11px] font-extrabold uppercase tracking-wider text-gray-500 dark:text-gray-400 bg-gray-50/50 dark:bg-gray-900/40">
              <th class="py-3 px-3 sm:px-4">Klien & ID Kontrak</th>
              <th class="py-3 px-3 sm:px-4">Paket Layanan</th>
              <th class="py-3 px-3 sm:px-4">Pendapatan Bersih</th>
              <th class="py-3 px-3 sm:px-4 text-center">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-xs">
            @forelse($recentBookings as $b)
              @php
                $detail = $b->bookingDetail;
                $client = $b->client;
              @endphp
              <tr class="hover:bg-amber-50/30 dark:hover:bg-amber-950/20 transition">
                <td class="py-3.5 px-3 sm:px-4">
                  <div class="space-y-0.5">
                    <p class="font-extrabold text-gray-900 dark:text-white">{{ $client->nama ?? ($client->user->nama ?? 'Klien') }}</p>
                    <span class="text-xs font-mono text-gray-400">KTRK-{{ str_pad($b->id, 5, '0', STR_PAD_LEFT) }}</span>
                  </div>
                </td>
                <td class="py-3.5 px-3 sm:px-4 font-bold text-gray-800 dark:text-gray-200">
                  {{ $detail->service->nama_layanan ?? 'Paket Kustom' }}
                </td>
                <td class="py-3.5 px-3 sm:px-4 font-black text-gray-900 dark:text-white">
                  Rp {{ number_format($b->payout_amount ?: ($b->jumlah * 0.9), 0, ',', '.') }}
                </td>
                <td class="py-3.5 px-3 sm:px-4 text-center whitespace-nowrap">
                  @if($b->status_contract === 'completed')
                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">Selesai</span>
                  @elseif($b->status_contract === 'active')
                    <span class="text-xs font-bold text-blue-600 dark:text-blue-400">Dikonfirmasi</span>
                  @else
                    <span class="text-xs font-bold text-amber-600 dark:text-amber-400">Menunggu</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="py-8 text-center text-gray-400 font-bold text-xs">
                  Belum ada order booking terbaru.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <!-- Right: 4 Cols - Upcoming Sessions & Availability -->
    <div class="lg:col-span-4 bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-xs space-y-4">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-base font-extrabold text-gray-900 dark:text-white tracking-tight">Jadwal Sesi Terdekat</h2>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Agenda pemotretan yang akan datang</p>
        </div>
      </div>

      <div class="space-y-3">
        @forelse($upcomingSchedules as $sched)
          <div class="p-3.5 rounded-xl bg-gray-50 dark:bg-gray-900/60 border border-gray-100 dark:border-gray-700/80 space-y-1">
            <div class="flex items-center justify-between">
              <span class="text-[11px] font-black text-amber-600 dark:text-amber-400">
                {{ \Carbon\Carbon::parse($sched->booking_date)->translatedFormat('d M Y') }}
              </span>
              <span class="text-[10px] font-bold text-gray-500">
                {{ $sched->jam_mulai }} - {{ $sched->jam_selesai }} WITA
              </span>
            </div>
            <p class="text-xs font-black text-gray-900 dark:text-white">{{ $sched->contract->client->nama ?? 'Klien' }}</p>
            <p class="text-[11px] text-gray-500 dark:text-gray-400 truncate">{{ $sched->lokasi ?: 'Lokasi Fleksibel' }}</p>
          </div>
        @empty
          <div class="py-6 text-center text-gray-400 font-medium text-xs">
            Belum ada jadwal pemotretan terdekat.
          </div>
        @endforelse
      </div>

      <div class="pt-2 flex justify-end">
        <a href="{{ route('photographer.availability') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs rounded-xl shadow-xs transition">
          <span>+ Blokir / Atur Jadwal</span>
        </a>
      </div>
    </div>

  </div>

</div>
@endsection