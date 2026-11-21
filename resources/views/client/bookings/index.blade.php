@extends('layouts.app')



@section('title', 'Reservasi Sesi Foto Saya — LensMatch')



@section('content')

<section class="py-12 bg-white dark:bg-gray-900 min-h-screen" x-data="{
  cancelModalOpen: false,
  cancelContractId: null,
  cancelPhotographerName: '',
  cancelServiceName: '',
  rescheduleModalOpen: false,
  rescheduleContractId: null,
  reschedulePhotographerName: '',
  rescheduleServiceName: '',
  rescheduleDefaultDate: '{{ date('Y-m-d', strtotime('+1 day')) }}',

  isLoading: false,
  currentStatus: '{{ $filterStatus ?? 'all' }}',

  applyFilter(val) {
    this.currentStatus = val;
    let url = new URL(window.location.href);
    if (val && val !== 'all') {
      url.searchParams.set('status', val);
    } else {
      url.searchParams.delete('status');
    }
    url.searchParams.set('page', '1');
    this.fetchTable(url.toString());
  },

  handleSearch(e) {
    let q = e.target.value.toLowerCase();
    let rows = document.querySelectorAll('#booking-table-body tr');
    rows.forEach(r => {
      let text = r.innerText.toLowerCase();
      r.style.display = text.includes(q) ? '' : 'none';
    });
  },

  fetchTable(url) {
    this.isLoading = true;
    fetch(url, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(res => {
        if (!res.ok) throw new Error('Network error');
        return res.text();
      })
      .then(html => {
        let parser = new DOMParser();
        let doc = parser.parseFromString(html, 'text/html');
        let newTable = doc.querySelector('#dashboard-table-container');
        let curTable = document.querySelector('#dashboard-table-container');
        if (newTable && curTable) {
          curTable.innerHTML = newTable.innerHTML;
          if (window.Alpine) window.Alpine.initTree(curTable);
        }
        let newStats = doc.querySelector('#dashboard-stats-container');
        let curStats = document.querySelector('#dashboard-stats-container');
        if (newStats && curStats) {
          curStats.innerHTML = newStats.innerHTML;
        }
        window.history.pushState(null, '', url);
        this.isLoading = false;
      })
      .catch(err => {
        console.error(err);
        this.isLoading = false;
      });
  }
}">

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">



    <!-- Page Header -->

    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">

      <div>

        <h1 class="text-3xl font-black text-gray-900 dark:text-white">Reservasi Saya</h1>

      </div>

    </div>



    <!-- Pengingat Sesi Pintar H-1 & Hari-H (Smart Reminder & Countdown) -->

    @include('partials.shared.upcoming-session-banner', ['urgentSession' => $urgentSession, 'role' => 'client'])



    <!-- 4 Clean Summary Metric Cards (Matching Dashboard Standard) -->

    <div id="dashboard-stats-container" class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">

      <!-- Card 1: Total Semua Reservasi -->

      <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">

        <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Total Reservasi</span>

        <h2 class="text-xs font-black text-gray-900 dark:text-white tracking-tight">{{ $stats['all'] ?? 0 }} Sesi</h2>

      </div>



      <!-- Card 2: Menunggu Konfirmasi -->

      <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">

        <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Perlu Konfirmasi</span>

        <h2 class="text-xs font-black text-gray-900 dark:text-white tracking-tight">{{ $stats['pending'] ?? 0 }} Sesi</h2>

      </div>



      <!-- Card 3: Jadwal Dikonfirmasi -->

      <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">

        <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Sesi Dikonfirmasi</span>

        <h2 class="text-xs font-black text-gray-900 dark:text-white tracking-tight">{{ $stats['confirmed'] ?? 0 }} Sesi</h2>

      </div>



      <!-- Card 4: Selesai & Galeri -->

      <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">

        <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Selesai & Galeri</span>

        <h2 class="text-xs font-black text-gray-900 dark:text-white tracking-tight">{{ $stats['completed'] ?? 0 }} Sesi</h2>

      </div>

    </div>



    <!-- Booking Table Card (Following Dashboard Table Standards) -->

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm overflow-hidden">

      

      <!-- Toolbar: Status Filter di Kiri, Search Bar di Kanan -->

      <div class="p-4 flex flex-col md:flex-row items-center justify-between gap-4">

        

        <!-- Filter Status (Alpine.js Custom Floating Dropdown) -->

        <div class="flex items-center gap-3 w-full md:w-auto">

          @php
            $statusLabels = [
              'all' => 'Semua Status Reservasi',
              'pending_confirmation' => 'Menunggu Konfirmasi',
              'pending_payment' => 'Menunggu Pembayaran',
              'confirmed' => 'Jadwal Terkonfirmasi',
              'pending_client' => 'Menunggu Konfirmasi Klien',
              'completed' => 'Selesai',
              'cancelled' => 'Dibatalkan',
              'expired' => 'Kadaluarsa',
            ];
            $curStatName = $statusLabels[$filterStatus ?? 'all'] ?? 'Semua Status Reservasi';
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
                <button type="button" 
                        @click="selectedId = '{{ $stKey }}'; selectedName = '{{ $stVal }}'; open = false; applyFilter('{{ $stKey }}')"
                        class="w-full px-3.5 py-2 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition flex items-center justify-between text-left cursor-pointer border-b border-gray-50 dark:border-gray-700/40 last:border-0"
                        :class="selectedId === '{{ $stKey }}' ? 'bg-amber-50/70 dark:bg-amber-950/30 text-amber-600 font-extrabold' : ''">
                  <span>{{ $stVal }}</span>
                </button>
              @endforeach

            </div>

          </div>

        </div>



        <!-- Search Bar di Kanan -->

        <div class="relative w-full md:w-80">

          <input type="text" 

                 @input="handleSearch($event)" 

                 placeholder="Cari fotografer, layanan, lokasi..." 

                 class="w-full pl-4 pr-10 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">

          <svg class="w-4 h-4 text-gray-400 absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>

        </div>

      </div>



      <!-- Table Container -->

      <div id="dashboard-table-container" class="transition-opacity duration-200" :class="isLoading ? 'opacity-40 pointer-events-none' : 'opacity-100'">

        <div class="overflow-x-auto min-h-[300px]" style="min-height: 300px;">

        <table class="w-full text-left border-collapse">

          <thead>

            <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-[11px] font-extrabold uppercase tracking-wider text-gray-500 dark:text-gray-400">

              <th class="py-3.5 px-4 sm:px-6 w-3/12">Fotografer</th>

              <th class="py-3.5 px-4 sm:px-6 w-3/12">Layanan</th>

              <th class="py-3.5 px-4 sm:px-6 w-2/12">Harga</th>

              <th class="py-3.5 px-4 sm:px-6 w-2/12 text-center">Status</th>

              <th class="py-3.5 px-4 sm:px-6 w-2/12 text-center">Aksi</th>

            </tr>

          </thead>

          <tbody id="booking-table-body" class="divide-y divide-gray-100 dark:divide-gray-700/60 text-xs">

            @forelse($contracts as $c)

              @php

                $detail = $c->bookingDetail;

                $photographer = $c->photographer;

                $payment = $c->payments->first();

              @endphp

              <tr class="hover:bg-amber-50/30 dark:hover:bg-amber-950/20 transition">

                

                <!-- Fotografer (No icon at location) -->

                <td class="py-4 px-4 sm:px-6 align-top">

                  <div class="flex items-start gap-3">

                    <img onerror="this.onerror=null;this.src=window.SVG_IMAGE_FALLBACK||'data:image/svg+xml;charset=UTF-8,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'800\' height=\'600\' viewBox=\'0 0 800 600\' fill=\'none\'%3E%3Crect width=\'800\' height=\'600\' fill=\'%23F3F4F6\'/%3E%3Cpath d=\'M360 260C360 248.954 368.954 240 380 240H420C431.046 240 440 248.954 440 260V265H450C466.569 265 480 278.431 480 295V345C480 361.569 466.569 375 450 375H350C333.431 375 320 361.569 320 345V295C320 278.431 333.431 265 350 265H360V260Z\' stroke=\'%239CA3AF\' stroke-width=\'12\' stroke-linecap=\'round\' stroke-linejoin=\'round\'/%3E%3Ccircle cx=\'400\' cy=\'320\' r=\'30\' stroke=\'%239CA3AF\' stroke-width=\'12\'/%3E%3Cline x1=\'310\' y1=\'240\' x2=\'490\' y2=\'390\' stroke=\'%23EF4444\' stroke-width=\'10\' stroke-linecap=\'round\'/%3E%3Ctext x=\'400\' y=\'450\' font-family=\'sans-serif\' font-size=\'22\' font-weight=\'700\' fill=\'%236B7280\' text-anchor=\'middle\'%3EGambar Tidak Dapat Dimuat%3C/text%3E%3C/svg%3E';" 

                         src="{{ $photographer->foto ?? '' }}" 

                         alt="{{ $photographer->nama ?? 'Fotografer' }}" 

                         class="w-10 h-10 rounded-full object-cover border-2 border-amber-400 shrink-0 mt-0.5" />

                    <div class="space-y-0.5">

                      <span class="text-xs font-mono font-bold text-gray-400 block">KTRK-{{ str_pad($c->id, 5, '0', STR_PAD_LEFT) }}</span>

                      <h4 class="font-extrabold text-gray-900 dark:text-white text-xs tracking-tight">{{ $photographer->nama ?? 'Fotografer' }}</h4>

                      <p class="text-xs text-gray-500 dark:text-gray-400">

                        {{ $photographer->city->name ?? $photographer->alamat ?? '-' }}

                      </p>

                    </div>

                  </div>

                </td>



                <!-- Layanan -->

                <td class="py-4 px-4 sm:px-6 align-top">

                  <div class="space-y-1">

                    <span class="font-bold text-gray-800 dark:text-gray-200 block text-xs">{{ $detail->service->nama_layanan ?? 'Paket Kustom' }}</span>

                    <div class="text-xs text-gray-500 dark:text-gray-400 space-y-0.5">

                      <div class="font-medium text-gray-700 dark:text-gray-300">

                        @php
                          $cStart = $detail?->jam_mulai ? substr($detail->jam_mulai, 0, 5) : null;
                          $cEnd = $detail?->jam_selesai ? substr($detail->jam_selesai, 0, 5) : null;
                        @endphp
                        {{ $detail ? $detail->date_range_label : '-' }} • {{ $cStart ? ($cEnd ? "{$cStart} - {$cEnd} WIB" : "{$cStart} WIB") : '-' }} ({{ $detail ? $detail->effective_duration_hours : 1 }} Jam)

                      </div>

                      @if($detail && $detail->lokasi)

                        <div class="text-gray-500 dark:text-gray-400 truncate max-w-xs">Lokasi: {{ $detail->lokasi }}</div>

                      @endif

                    </div>



                    <!-- Two-Way Review: Ulasan dari Fotografer Mitra -->

                    @if($c->photographerClientReview)

                      <div class="mt-2 p-2.5 rounded-xl bg-amber-50/70 dark:bg-amber-950/30 border border-amber-200/70 dark:border-amber-900/50 text-xs space-y-1">

                        <div class="flex items-center justify-between text-amber-900 dark:text-amber-200">

                          <span class="font-bold">Ulasan dari Fotografer:</span>

                          <span class="font-black text-xs inline-flex items-center gap-1"><svg class="w-3.5 h-3.5 fill-amber-400 text-amber-500 shrink-0" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg> <span>{{ $c->photographerClientReview->rating }}.0</span></span>

                        </div>

                        <div class="flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400 font-medium">

                          <span>Waktu: {{ $c->photographerClientReview->ketepatan_waktu }}/5</span>

                          <span>Komunikasi: {{ $c->photographerClientReview->komunikasi }}/5</span>

                          <span>Kerjasama: {{ $c->photographerClientReview->kerjasama }}/5</span>

                        </div>

                        @if($c->photographerClientReview->ulasan)

                          <p class="text-xs text-gray-600 dark:text-gray-300 italic pt-0.5">

                            "{{ $c->photographerClientReview->ulasan }}"

                          </p>

                        @endif

                      </div>

                    @endif



                    <!-- Reschedule Status Banner -->

                    @if($detail && $detail->reschedule_status === 'pending')

                      @if($detail->reschedule_by === 'client')

                        <div class="mt-2 p-2 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200/80 dark:border-amber-900/60 text-amber-800 dark:text-amber-300 text-xs font-medium space-y-0.5">

                          <span class="font-bold block text-amber-900 dark:text-amber-200">Pengajuan Jadwal Ulang Dikirim</span>

                          <span class="block text-xs text-gray-600 dark:text-gray-400">Jadwal baru: {{ \Carbon\Carbon::parse($detail->reschedule_date)->translatedFormat('d F Y') }} • {{ substr($detail->reschedule_time, 0, 5) }} WIB</span>

                          <span class="block text-xs text-gray-500 italic">"{{ $detail->reschedule_reason }}"</span>

                        </div>

                      @elseif($detail->reschedule_by === 'photographer')

                        <div class="mt-2 p-2.5 rounded-xl bg-blue-50 dark:bg-blue-950/40 border border-blue-200/80 dark:border-blue-900/60 text-blue-900 dark:text-blue-300 text-xs space-y-1.5">

                          <div>

                            <span class="font-bold block text-blue-900 dark:text-blue-200">Fotografer Mengajukan Jadwal Baru:</span>

                            <span class="font-black text-gray-900 dark:text-white block mt-0.5">{{ \Carbon\Carbon::parse($detail->reschedule_date)->translatedFormat('d F Y') }} • {{ substr($detail->reschedule_time, 0, 5) }} WIB</span>

                            <span class="text-xs text-gray-600 dark:text-gray-400 block mt-0.5">Alasan: "{{ $detail->reschedule_reason }}"</span>

                          </div>

                          <div class="flex items-center gap-2 pt-1">
                            <button type="button" 
                                    @click="$dispatch('open-confirm-modal', {
                                        title: 'Setujui Jadwal Baru?',
                                        message: 'Jadwal pemotretan akan diperbarui sesuai tanggal dan jam pengajuan fotografer.',
                                        confirmText: 'Ya, Setujui Jadwal',
                                        iconType: 'check',
                                        buttonClass: 'bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold',
                                        actionUrl: '{{ route('client.bookings.reschedule-respond', $c->id) }}',
                                        method: 'POST',
                                        params: { action: 'accept' }
                                    })"
                                    class="px-3 py-1 bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs rounded-lg shadow-2xs transition cursor-pointer">
                              Setujui Jadwal Baru
                            </button>

                            <button type="button" 
                                    @click="$dispatch('open-confirm-modal', {
                                        title: 'Tolak Pengajuan Jadwal?',
                                        message: 'Pengajuan jadwal baru akan ditolak dan sesi pemotretan tetap mengacu pada jadwal sebelumnya.',
                                        confirmText: 'Ya, Tolak Jadwal',
                                        iconType: 'warning',
                                        buttonClass: 'bg-rose-600 hover:bg-rose-700 text-white font-extrabold',
                                        actionUrl: '{{ route('client.bookings.reschedule-respond', $c->id) }}',
                                        method: 'POST',
                                        params: { action: 'reject' }
                                    })"
                                    class="px-3 py-1 bg-white hover:bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 font-bold text-xs rounded-lg transition cursor-pointer">
                              Tolak
                            </button>
                          </div>

                        </div>

                      @endif

                    @endif

                  </div>

                </td>



                <!-- Harga (Clean without invoice code) -->

                <td class="py-4 px-4 sm:px-6 align-top whitespace-nowrap">

                  <span class="text-xs font-black text-gray-900 dark:text-white block">

                    Rp {{ number_format($c->jumlah, 0, ',', '.') }}

                  </span>

                </td>



                <!-- Status Booking (Standarisasi Sinkron Klien & Fotografer) -->
                <td class="py-4 px-4 sm:px-6 align-top text-center whitespace-nowrap">
                  @php
                    $hasPhotos = !empty($detail?->hasil_foto_url);
                    $isValidated = (bool) $c->is_validated_client;
                    $isRevision = $hasPhotos && !$isValidated && ($c->status_contract === 'active' || str_starts_with($detail?->catatan_fotografer ?? '', 'Klien meminta revisi:'));
                  @endphp

                  @if($c->status_contract === 'cancelled')
                    <span class="text-xs font-bold text-rose-600 dark:text-rose-400">
                      {{ ($c->cancellation && (str_contains(strtolower($c->cancellation->alasan), 'kadaluarsa') || str_contains(strtolower($c->cancellation->alasan), 'terlewati'))) ? 'Kadaluarsa' : 'Dibatalkan' }}
                    </span>
                  @elseif($c->status_contract === 'completed' && $isValidated)
                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">
                      Selesai
                    </span>
                  @elseif($isRevision)
                    <span class="text-xs font-bold text-amber-600 dark:text-amber-400">
                      Revisi Diminta
                    </span>
                  @elseif($hasPhotos && !$isValidated)
                    <span class="text-xs font-bold text-blue-600 dark:text-blue-400">
                      Menunggu Konfirmasi Klien
                    </span>
                  @elseif($c->status_contract === 'active')
                    <span class="text-xs font-bold text-blue-600 dark:text-blue-400">
                      Jadwal Terkonfirmasi
                    </span>
                  @elseif($c->status_contract === 'pending_payment' || ($c->is_validated_photographer && in_array($c->status_contract, ['approved', 'pending_payment'])))
                    <span class="text-xs font-bold text-amber-600 dark:text-amber-400">
                      Menunggu Pembayaran
                    </span>
                  @else
                    <span class="text-xs font-bold text-amber-500 dark:text-amber-400">
                      Menunggu Konfirmasi
                    </span>
                  @endif
                </td>

                <!-- Aksi (Chat Utama + Dropdown Menu '•••' untuk Opsi Lain) -->
                <td class="py-4 px-4 sm:px-6 align-top text-center whitespace-nowrap">
                  @php
                    $isDropup = ($loop->count > 2 && ($loop->last || $loop->remaining < 2)) || ($loop->count === 2 && $loop->last);
                  @endphp
                  <div class="inline-flex items-center justify-center gap-2" x-data="{ menuOpen: false }">
                    
                    @php
                      $photoName = $photographer->nama ?? 'Fotografer';
                      $serviceName = $detail?->service?->nama_layanan ?? 'sesi pemotretan';
                      $bookingDateStr = $detail?->booking_date ? \Carbon\Carbon::parse($detail->booking_date)->translatedFormat('d M Y') : '';
                      $datePhrase = $bookingDateStr ? " pada {$bookingDateStr}" : '';

                      // Template Draft Chat Kontekstual Klien
                      // Hanya ada saat tombol aksi utama yang aktif adalah 'Chat' (proses sedang menunggu respon/tindak lanjut fotografer)
                      $clientChatPrefill = null;
                      if ($detail?->status_booking === 'pending' || $c->status_contract === 'pending') {
                        // Menunggu Konfirmasi: Klien menunggu konfirmasi slot jadwal dari fotografer
                        $clientChatPrefill = "Halo Kak {$photoName}, saya ingin menanyakan ketersediaan jadwal untuk sesi {$serviceName}{$datePhrase}. Apakah slot jadwal tersebut masih bisa diambil?";
                      } elseif ($c->status_contract === 'active') {
                        // Jadwal Terkonfirmasi: Klien koordinasi persiapan menjelang hari-H pemotretan
                        $clientChatPrefill = "Halo Kak {$photoName}, untuk persiapan sesi pemotretan {$serviceName}{$datePhrase}, apakah ada hal atau outfit yang perlu saya siapkan?";
                      }
                      // Status Menunggu Pembayaran (tombol utama: Bayar), Selesai, dan Dibatalkan: Murni tanpa template (input pesan kosong)

                      $clientChatUrl = $clientChatPrefill
                        ? route('chat.start', ['photographerId' => $c->id_photographer, 'prefill' => $clientChatPrefill])
                        : route('chat.start', $c->id_photographer);
                    @endphp

                    <!-- 1. Tombol Aksi Utama (Bayar jika sudah disetujui tapi belum bayar, atau Chat) -->
                    @if($c->status_contract === 'pending_payment' || ($c->is_validated_photographer && in_array($c->status_contract, ['approved', 'pending_payment'])))
                      <a href="{{ route('booking.invoice', ['id' => $c->id, 'from' => 'bookings']) }}" class="px-3.5 py-1.5 bg-amber-400 hover:bg-amber-500 text-gray-950 font-bold text-xs rounded-xl transition inline-block">
                        Bayar
                      </a>
                    @else
                      <a href="{{ $clientChatUrl }}" class="px-3.5 py-1.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-bold text-xs rounded-xl transition inline-block">
                        Chat
                      </a>
                    @endif

                    <!-- 2. Dropdown Menu Opsi Lainnya (•••) -->
                    <div class="relative inline-block text-left">
                      <button type="button" 
                              @click="menuOpen = !menuOpen" 
                              class="w-8 h-8 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white flex items-center justify-center transition shadow-2xs cursor-pointer">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                          <circle cx="12" cy="5" r="1.75" />
                          <circle cx="12" cy="12" r="1.75" />
                          <circle cx="12" cy="19" r="1.75" />
                        </svg>
                      </button>

                      <!-- Floating Dropdown Menu (Pure Text Items) -->
                      <div x-show="menuOpen" 
                           @click.away="menuOpen = false" 
                           x-transition:enter="transition ease-out duration-100" 
                           x-transition:enter-start="transform opacity-0 scale-95" 
                           x-transition:enter-end="transform opacity-100 scale-100" 
                           x-transition:leave="transition ease-in duration-75" 
                           x-transition:leave-start="transform opacity-100 scale-100" 
                           x-transition:leave-end="transform opacity-0 scale-95" 
                           class="absolute right-0 {{ $isDropup ? 'bottom-full mb-1.5 origin-bottom-right' : 'top-full mt-1.5 origin-top-right' }} w-40 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 z-50 text-left overflow-hidden" 
                           style="display: none;">
                        
                        <!-- Lihat Invoice -->
                        <a href="{{ route('booking.invoice', ['id' => $c->id, 'view' => 'invoice', 'from' => 'bookings']) }}" class="w-full px-4 py-2.5 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition block">
                          Lihat Invoice
                        </a>

                        @if($c->status_contract === 'pending_payment' || ($c->is_validated_photographer && in_array($c->status_contract, ['approved', 'pending_payment'])))
                          <a href="{{ route('chat.start', $c->id_photographer) }}" class="w-full px-4 py-2.5 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition block">
                            Chat Fotografer
                          </a>
                        @endif



                        <!-- Akses Hasil Foto (Tunggal & Tidak Membingungkan) -->

                        @if($detail && $detail->hasil_foto_url)

                          <a href="{{ route('client.galleries') }}" class="w-full px-4 py-2.5 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition block border-t border-gray-50 dark:border-gray-700/40">

                            Hasil Foto

                          </a>

                        @endif



                        <!-- Beri Ulasan / Edit Ulasan (Hanya jika sesi sudah selesai dan divalidasi klien - Rule #90) -->
                        @if($c->status_contract === 'completed' && $c->is_validated_client)

                          @if($c->testimonial)

                            <button type="button" 

                                    @click="menuOpen = false; $dispatch('open-review-modal', {

                                        contractId: {{ $c->id }},

                                        testimonialId: {{ $c->testimonial->id }},

                                        photoName: '{{ addslashes($photographer->nama ?? '') }}',

                                        serviceName: '{{ addslashes($detail->service->nama_layanan ?? '') }}',

                                        rating: {{ (int) $c->testimonial->rating }},

                                        reviewText: '{{ addslashes($c->testimonial->deskripsi_review) }}',

                                        isEdit: true

                                    })"

                                    class="w-full px-4 py-2.5 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition block text-left border-t border-gray-50 dark:border-gray-700/40 cursor-pointer">

                              Edit Ulasan

                            </button>

                          @else

                            <button type="button" 

                                    @click="menuOpen = false; $dispatch('open-review-modal', {

                                        contractId: {{ $c->id }},

                                        photoName: '{{ addslashes($photographer->nama ?? '') }}',

                                        serviceName: '{{ addslashes($detail->service->nama_layanan ?? '') }}',

                                        isEdit: false

                                    })"

                                    class="w-full px-4 py-2.5 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition block text-left border-t border-gray-50 dark:border-gray-700/40 cursor-pointer">

                              Beri Ulasan

                            </button>

                          @endif

                        @endif



                        <!-- Ajukan Jadwal Ulang (Hanya jika belum selesai dan belum dibatalkan) -->
                        @if($detail && !in_array($detail->status_booking, ['completed', 'cancelled']) && $detail->reschedule_status !== 'pending')
                          <button type="button" 
                                  @click="menuOpen = false; rescheduleContractId = {{ $c->id }}; reschedulePhotographerName = '{{ addslashes($photographer->nama ?? 'Mitra Fotografer') }}'; rescheduleServiceName = '{{ addslashes($detail->service->nama_layanan ?? 'Sesi Fotografi') }}'; rescheduleModalOpen = true;"
                                  class="w-full px-4 py-2.5 text-xs font-semibold text-amber-700 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/40 transition block text-left border-t border-gray-50 dark:border-gray-700/40 cursor-pointer">
                            Ajukan Jadwal Ulang
                          </button>
                        @endif

                        <!-- Batalkan Reservasi (Hanya jika belum selesai dan belum dibatalkan) -->
                        @if($detail && !in_array($detail->status_booking, ['completed', 'cancelled']))
                          <button type="button" 
                                  @click="menuOpen = false; cancelContractId = {{ $c->id }}; cancelPhotographerName = '{{ addslashes($photographer->nama ?? 'Mitra Fotografer') }}'; cancelServiceName = '{{ addslashes($detail->service->nama_layanan ?? 'Sesi Fotografi') }}'; cancelModalOpen = true;"
                                  class="w-full px-4 py-2.5 text-xs font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition block text-left border-t border-gray-50 dark:border-gray-700/40 cursor-pointer">
                            Batalkan Reservasi
                          </button>
                        @endif



                      </div>

                    </div>



                  </div>

                </td>

              </tr>

            @empty

              <tr>

                <td colspan="5" class="py-12 text-center space-y-3">

                  <h3 class="text-xs font-extrabold text-gray-900 dark:text-white">Belum Ada Reservasi Sesi Foto</h3>

                  <p class="text-xs text-gray-500">Temukan fotografer impian Anda dan abadikan momen berharga bersama LensMatch.</p>

                  <a href="{{ url('/fotografer') }}" class="inline-block px-6 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs rounded-xl shadow-xs transition">

                    Cari Fotografer Sekarang

                  </a>

                </td>

              </tr>

            @endforelse

          </tbody>

        </table>

      </div>



      @if($contracts->total() > 0)

        <div class="p-4 border-t border-gray-100 dark:border-gray-700/80 bg-gray-50/50 dark:bg-gray-900/50">

          {{ $contracts->links('partials.dashboard.pagination') }}

        </div>

      @endif

      </div>

    </div>



  </div>



  <!-- Modal Pembatalan Reservasi Klien (Mematuhi Aturan #59: Judul di Tengah Tanpa Ikon, Max 2 Tombol di Bawah, Tombol X di Kanan Atas) -->

  <div x-show="cancelModalOpen" 

       x-cloak 

       class="fixed inset-0 z-50 overflow-y-auto"

       aria-labelledby="modal-title" role="dialog" aria-modal="true">

    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">

      <div x-show="cancelModalOpen" 

           x-transition:enter="ease-out duration-300"

           x-transition:enter-start="opacity-0"

           x-transition:enter-end="opacity-100"

           x-transition:leave="ease-in duration-200"

           x-transition:leave-start="opacity-100"

           x-transition:leave-end="opacity-0"

           @click="cancelModalOpen = false" 

           class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity"></div>



      <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>



      <div x-show="cancelModalOpen" 

           x-transition:enter="ease-out duration-300"

           x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"

           x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"

           x-transition:leave="ease-in duration-200"

           x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"

           x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"

           class="relative inline-block align-bottom bg-white dark:bg-gray-850 rounded-2xl p-6 sm:p-7 text-center overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-gray-100 dark:border-gray-700">

        





        <!-- Judul Rata Tengah Tanpa Ikon (Rule #59) -->

        <h3 class="text-lg font-black text-gray-900 dark:text-white text-center">

          Batalkan Reservasi Sesi Foto?

        </h3>



        <!-- Deskripsi: Fokus pada Mitra Fotografer & Layanan (Opsi A ala Airbnb/SweetEscape) -->
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 text-center max-w-sm mx-auto leading-relaxed">
          Apakah Anda yakin ingin membatalkan sesi <strong class="text-gray-900 dark:text-white" x-text="cancelServiceName"></strong> bersama <strong class="text-gray-900 dark:text-white" x-text="cancelPhotographerName"></strong>? Jika sudah melakukan pembayaran, dana akan ditinjau Admin untuk proses refund.
        </p>



        <!-- Form Alasan Pembatalan -->

        <form :action="'/client/bookings/' + cancelContractId + '/cancel'" 
              method="POST" 
              novalidate
              class="mt-5 flex flex-col gap-4"
              x-data="{
                form: { alasan: '' },
                touched: { alasan: false },
                errors: { alasan: '' },
                validateAlasan() {
                  if (!this.form.alasan || !this.form.alasan.trim()) {
                    this.errors.alasan = 'Alasan pembatalan reservasi wajib diisi.';
                    return false;
                  }
                  if (this.form.alasan.trim().length < 5) {
                    this.errors.alasan = 'Alasan pembatalan minimal 5 karakter.';
                    return false;
                  }
                  this.errors.alasan = '';
                  return true;
                },
                submitForm(e) {
                  this.touched.alasan = true;
                  if (!this.validateAlasan()) {
                    e.preventDefault();
                    this.$refs.fieldAlasan?.focus();
                    this.$refs.fieldAlasan?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return false;
                  }
                }
              }"
              @submit="submitForm($event)">

          @csrf

          <div class="text-left">

            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">

              Alasan Pembatalan <span class="text-rose-500">*</span>

            </label>

            <textarea name="alasan" 
                      x-ref="fieldAlasan"
                      x-model="form.alasan"
                      @input="touched.alasan = true; validateAlasan()"
                      @blur="touched.alasan = true; validateAlasan()"
                      rows="3" 
                      placeholder="Jelaskan alasan pembatalan reservasi Anda..."
                      class="w-full px-3.5 py-2.5 text-xs text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 border rounded-xl outline-none focus:ring-2 transition"
                      :class="touched.alasan && errors.alasan ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-400 focus:border-amber-400'"></textarea>

            <p x-show="touched.alasan && errors.alasan" x-text="errors.alasan" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>

          </div>

          <!-- Tombol Aksi di Tengah Maksimal 2 Tombol (Rule #59) -->

          <div class="flex items-center justify-center gap-3">

            <button type="button" 

                    @click="cancelModalOpen = false" 

                    class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 font-bold text-xs hover:bg-gray-100 dark:hover:bg-gray-750 transition cursor-pointer">

              Batal

            </button>

            <button type="submit" 

                    class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-xs transition cursor-pointer">

              Ya, Batalkan Pesanan

            </button>

          </div>

        </form>



      </div>

    </div>

  </div>



  <!-- Modal Ajukan Jadwal Ulang Klien (Mematuhi Rule #59, #73, #74, #76) -->

  <div x-show="rescheduleModalOpen" 

       x-cloak 

       class="fixed inset-0 z-50 overflow-y-auto"

       aria-labelledby="modal-title" role="dialog" aria-modal="true">

    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">

      <div x-show="rescheduleModalOpen" 

           x-transition:enter="ease-out duration-300"

           x-transition:enter-start="opacity-0"

           x-transition:enter-end="opacity-100"

           x-transition:leave="ease-in duration-200"

           x-transition:leave-start="opacity-100"

           x-transition:leave-end="opacity-0"

           @click="rescheduleModalOpen = false" 

           class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity"></div>



      <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>



      <div x-show="rescheduleModalOpen" 

           x-transition:enter="ease-out duration-300"

           x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"

           x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"

           x-transition:leave="ease-in duration-200"

           x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"

           x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"

           class="relative inline-block align-bottom bg-white dark:bg-gray-850 rounded-2xl p-6 sm:p-7 text-center overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-gray-100 dark:border-gray-700">

        

        <!-- Judul Rata Tengah Tanpa Ikon (Rule #59) -->

        <h3 class="text-lg font-black text-gray-900 dark:text-white text-center">

          Ajukan Jadwal Ulang Sesi

        </h3>



        <!-- Deskripsi: Fokus pada Mitra Fotografer & Layanan (Opsi A ala Airbnb/SweetEscape) -->
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 text-center max-w-sm mx-auto leading-relaxed">
          Pilih tanggal dan jam mulai pengganti untuk sesi <strong class="text-gray-900 dark:text-white" x-text="rescheduleServiceName"></strong> bersama <strong class="text-gray-900 dark:text-white" x-text="reschedulePhotographerName"></strong>.
        </p>



        <!-- Form Reschedule -->

        <form :action="'/client/bookings/' + rescheduleContractId + '/reschedule'" 
              method="POST" 
              novalidate
              class="mt-5 flex flex-col gap-4 text-left"
              x-data="{
                form: {
                  reschedule_date: rescheduleDefaultDate || '',
                  reschedule_reason: ''
                },
                touched: {
                  reschedule_date: false,
                  reschedule_reason: false
                },
                errors: {
                  reschedule_date: '',
                  reschedule_reason: ''
                },
                validateDate() {
                  if (!this.form.reschedule_date) {
                    this.errors.reschedule_date = 'Tanggal baru wajib dipilih.';
                    return false;
                  }
                  if (this.form.reschedule_date < '{{ date('Y-m-d') }}') {
                    this.errors.reschedule_date = 'Tanggal baru minimal hari ini atau mendatang.';
                    return false;
                  }
                  this.errors.reschedule_date = '';
                  return true;
                },
                validateReason() {
                  if (!this.form.reschedule_reason || !this.form.reschedule_reason.trim()) {
                    this.errors.reschedule_reason = 'Alasan perubahan jadwal wajib diisi.';
                    return false;
                  }
                  if (this.form.reschedule_reason.trim().length < 5) {
                    this.errors.reschedule_reason = 'Alasan perubahan minimal 5 karakter.';
                    return false;
                  }
                  this.errors.reschedule_reason = '';
                  return true;
                },
                submitForm(e) {
                  this.touched.reschedule_date = true;
                  this.touched.reschedule_reason = true;
                  const validDate = this.validateDate();
                  const validReason = this.validateReason();
                  if (!validDate) {
                    e.preventDefault();
                    this.$refs.fieldDate?.focus();
                    this.$refs.fieldDate?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return false;
                  }
                  if (!validReason) {
                    e.preventDefault();
                    this.$refs.fieldReason?.focus();
                    this.$refs.fieldReason?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return false;
                  }
                }
              }"
              @submit="submitForm($event)">

          @csrf

          

          <!-- Grid 2 Kolom untuk Field Pendek (Rule #73) -->

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">

            <div>

              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">

                Tanggal Baru <span class="text-rose-500">*</span>

              </label>

              <input type="date" 
                     name="reschedule_date" 
                     x-ref="fieldDate"
                     x-model="form.reschedule_date"
                     @input="touched.reschedule_date = true; validateDate()"
                     @blur="touched.reschedule_date = true; validateDate()"
                     class="w-full px-3.5 py-2.5 text-xs text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 border rounded-xl outline-none focus:ring-2 transition"
                     :class="touched.reschedule_date && errors.reschedule_date ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-400 focus:border-amber-400'">

              <p x-show="touched.reschedule_date && errors.reschedule_date" x-text="errors.reschedule_date" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>

            </div>



            <div x-data="{
              openTimeDropdown: false,
              selectedTime: '09:00',
              times: Array.from({ length: 24 }, (_, h) => {
                const hh = String(h).padStart(2, '0') + ':00';
                return { val: hh, label: hh + ' WIB' };
              })
            }">

              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">

                Jam Mulai <span class="text-rose-500">*</span>

              </label>

              <input type="hidden" name="reschedule_time" :value="selectedTime">

              <div class="relative">

                <button type="button"
                        @click="openTimeDropdown = !openTimeDropdown"
                        class="w-full flex items-center justify-between px-3.5 py-2.5 text-xs text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-amber-400 focus:outline-none transition font-semibold cursor-pointer">

                  <span x-text="selectedTime ? (selectedTime + ' WIB') : 'Pilih Jam Mulai'"></span>

                  <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 shrink-0" :class="{ 'rotate-180': openTimeDropdown }" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">

                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>

                  </svg>

                </button>

                <div x-show="openTimeDropdown"
                     @click.outside="openTimeDropdown = false"
                     x-transition
                     class="absolute z-50 top-full mt-1.5 left-0 w-full max-h-52 overflow-y-auto bg-white dark:bg-gray-900 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 p-1 space-y-0.5"
                     style="display: none;">

                  <template x-for="item in times" :key="item.val">

                    <button type="button"
                            @click="selectedTime = item.val; openTimeDropdown = false"
                            class="w-full text-left px-3 py-2 text-xs font-bold rounded-lg text-gray-800 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-700 dark:hover:text-amber-300 transition cursor-pointer"
                            :class="selectedTime === item.val ? 'bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 font-black' : ''"
                            x-text="item.label">

                    </button>

                  </template>

                </div>

              </div>

            </div>

          </div>



          <!-- Textarea Alasan (Rule #73) -->

          <div>

            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">

              Alasan Perubahan Jadwal <span class="text-rose-500">*</span>

            </label>

            <textarea name="reschedule_reason" 
                      x-ref="fieldReason"
                      x-model="form.reschedule_reason"
                      @input="touched.reschedule_reason = true; validateReason()"
                      @blur="touched.reschedule_reason = true; validateReason()"
                      rows="3" 
                      placeholder="Jelaskan alasan perubahan jadwal (misal kendala cuaca, acara diundur, dll)..."
                      class="w-full px-3.5 py-2.5 text-xs text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 border rounded-xl outline-none focus:ring-2 transition leading-relaxed"
                      :class="touched.reschedule_reason && errors.reschedule_reason ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-400 focus:border-amber-400'"></textarea>

            <p x-show="touched.reschedule_reason && errors.reschedule_reason" x-text="errors.reschedule_reason" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>

          </div>



          <!-- Tombol Aksi di Tengah Maksimal 2 Tombol (Rule #59, #74, #76) -->

          <div class="flex items-center justify-center gap-3">

            <button type="button" 

                    @click="rescheduleModalOpen = false" 

                    class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 font-bold text-xs hover:bg-gray-100 dark:hover:bg-gray-750 transition cursor-pointer">

              Batal

            </button>

            <button type="submit" 

                    class="px-5 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs shadow-xs transition cursor-pointer">

              Kirim Permintaan

            </button>

          </div>

        </form>



      </div>

    </div>

  </div>

</section>



@include('partials.client.review-modal')

@endsection