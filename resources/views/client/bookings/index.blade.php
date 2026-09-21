@extends('layouts.app')

@section('title', 'Reservasi Sesi Foto Saya — LensMatch')

@section('content')
<section class="py-12 bg-white dark:bg-gray-900 min-h-screen" x-data="{
  handleSearch(e) {
    let q = e.target.value.toLowerCase();
    let rows = document.querySelectorAll('#booking-table-body tr');
    rows.forEach(r => {
      let text = r.innerText.toLowerCase();
      r.style.display = text.includes(q) ? '' : 'none';
    });
  }
}">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-black text-gray-900 dark:text-white">Reservasi Saya</h1>
        <p class="text-xs sm:text-sm text-gray-500 mt-1">Pantau status konfirmasi fotografer, rincian jadwal, dan akses galeri hasil foto Anda.</p>
      </div>

      <a href="{{ url('/fotografer') }}" class="px-5 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs rounded-xl shadow-xs transition flex items-center gap-2 shrink-0">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path></svg>
        <span>Buat Booking Baru</span>
      </a>
    </div>

    <!-- 4 Clean Summary Metric Cards (Matching Dashboard Standard) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
      <!-- Card 1: Total Semua Reservasi -->
      <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
        <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Total Reservasi</span>
        <h2 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ $stats['all'] ?? 0 }} Sesi</h2>
        <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">Akumulasi seluruh pesanan</p>
      </div>

      <!-- Card 2: Menunggu Konfirmasi -->
      <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
        <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Perlu Konfirmasi</span>
        <h2 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ $stats['pending'] ?? 0 }} Sesi</h2>
        <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">Menunggu respon fotografer</p>
      </div>

      <!-- Card 3: Jadwal Dikonfirmasi -->
      <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
        <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Sesi Dikonfirmasi</span>
        <h2 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ $stats['confirmed'] ?? 0 }} Sesi</h2>
        <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">Jadwal pemotretan aktif</p>
      </div>

      <!-- Card 4: Selesai & Galeri -->
      <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
        <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Selesai & Galeri</span>
        <h2 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ $stats['completed'] ?? 0 }} Sesi</h2>
        <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">Hasil foto telah diserahkan</p>
      </div>
    </div>

    <!-- Booking Table Card (Following Dashboard Table Standards) -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm overflow-hidden space-y-4">
      
      <!-- Toolbar: Status Filter di Kiri, Search Bar di Kanan -->
      <div class="p-4 border-b border-gray-100 dark:border-gray-700/80 flex flex-col md:flex-row items-center justify-between gap-4">
        
        <!-- Filter Status (Alpine.js Custom Floating Dropdown) -->
        <div class="flex items-center gap-3 w-full md:w-auto">
          @php
            $statusLabels = [
              'all' => 'Semua Status Reservasi',
              'pending' => 'Menunggu Konfirmasi',
              'confirmed' => 'Jadwal Dikonfirmasi',
              'completed' => 'Sesi Selesai',
              'cancelled' => 'Dibatalkan'
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
                 placeholder="Cari fotografer, layanan, lokasi..." 
                 class="w-full pl-9 pr-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">
          <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>
      </div>

      <!-- Table Container -->
      <div class="overflow-x-auto">
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
                      <span class="text-[10px] font-mono font-bold text-gray-400 block">#KTRK-{{ str_pad($c->id, 5, '0', STR_PAD_LEFT) }}</span>
                      <h4 class="font-extrabold text-gray-900 dark:text-white text-sm tracking-tight">{{ $photographer->nama ?? 'Fotografer' }}</h4>
                      <p class="text-[11px] text-gray-500 dark:text-gray-400">
                        {{ $photographer->city->name ?? $photographer->alamat ?? '-' }}
                      </p>
                    </div>
                  </div>
                </td>

                <!-- Layanan -->
                <td class="py-4 px-4 sm:px-6 align-top">
                  <div class="space-y-1">
                    <span class="font-bold text-gray-800 dark:text-gray-200 block text-sm">{{ $detail->service->nama_layanan ?? 'Paket Fotografi' }}</span>
                    <div class="text-[11px] text-gray-500 dark:text-gray-400 space-y-0.5">
                      <div class="font-medium text-gray-700 dark:text-gray-300">
                        {{ $detail && $detail->booking_date ? \Carbon\Carbon::parse($detail->booking_date)->translatedFormat('d F Y') : '-' }} • {{ $detail->jam_mulai ?? '-' }} WIB
                      </div>
                      @if($detail && $detail->lokasi)
                        <div class="text-gray-500 dark:text-gray-400 truncate max-w-xs">Lokasi: {{ $detail->lokasi }}</div>
                      @endif
                    </div>
                  </div>
                </td>

                <!-- Harga (Clean without invoice code) -->
                <td class="py-4 px-4 sm:px-6 align-top whitespace-nowrap">
                  <span class="text-sm font-black text-gray-900 dark:text-white block">
                    Rp {{ number_format($c->jumlah, 0, ',', '.') }}
                  </span>
                </td>

                <!-- Status Booking (Rule #14: Pure Colored Text) -->
                <td class="py-4 px-4 sm:px-6 align-top text-center whitespace-nowrap">
                  @if($detail && $detail->status_booking === 'pending')
                    <span class="text-xs font-bold text-amber-600 dark:text-amber-400">
                      Menunggu Konfirmasi
                    </span>
                  @elseif($detail && $detail->status_booking === 'confirmed')
                    <span class="text-xs font-bold text-blue-600 dark:text-blue-400">
                      Jadwal Dikonfirmasi
                    </span>
                  @elseif($detail && $detail->status_booking === 'completed')
                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">
                      Sesi Selesai
                    </span>
                  @else
                    <span class="text-xs font-bold text-rose-600 dark:text-rose-400">
                      Dibatalkan
                    </span>
                  @endif
                </td>

                <!-- Aksi (Chat Utama + Dropdown Menu '•••' untuk Opsi Lain) -->
                <td class="py-4 px-4 sm:px-6 align-top text-center whitespace-nowrap">
                  <div class="inline-flex items-center justify-center gap-2" x-data="{ menuOpen: false }">
                    
                    <!-- 1. Tombol Chat Utama (Pure Text) -->
                    <a href="{{ route('chat.start', $c->id_photographer) }}" class="px-3.5 py-1.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-bold text-xs rounded-xl transition inline-block">
                      Chat
                    </a>

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
                           class="absolute right-0 top-full mt-1.5 w-40 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 py-1 z-50 text-left" 
                           style="display: none;">
                        
                        <!-- Lihat Invoice -->
                        <a href="{{ url('/pembayaran/' . $c->id) }}" class="w-full px-3.5 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition block">
                          Lihat Invoice
                        </a>

                        <!-- Unduh Foto Drive (jika ada) -->
                        @if($detail && $detail->hasil_foto_url)
                          <a href="{{ $detail->hasil_foto_url }}" target="_blank" class="w-full px-3.5 py-2 text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 transition block border-t border-gray-50 dark:border-gray-700/40">
                            Unduh Foto
                          </a>
                        @endif

                        <!-- Beri Ulasan / Status Ulasan -->
                        @if($detail && $detail->status_booking === 'completed')
                          @if($c->testimonial)
                            <div class="px-3.5 py-2 text-xs font-bold text-amber-600 dark:text-amber-400 border-t border-gray-50 dark:border-gray-700/40">
                              Ulasan ({{ $c->testimonial->rating }}.0)
                            </div>
                          @else
                            <button type="button" 
                                    @click="menuOpen = false; $dispatch('open-review-modal', {
                                        contractId: {{ $c->id }},
                                        photoName: '{{ addslashes($photographer->nama ?? '') }}',
                                        serviceName: '{{ addslashes($detail->service->nama_layanan ?? '') }}'
                                    })"
                                    class="w-full px-3.5 py-2 text-xs font-semibold text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/40 transition block text-left border-t border-gray-50 dark:border-gray-700/40 cursor-pointer">
                              Beri Ulasan
                            </button>
                          @endif
                        @endif

                      </div>
                    </div>

                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="py-12 text-center space-y-3">
                  <h3 class="text-base font-extrabold text-gray-900 dark:text-white">Belum Ada Reservasi Sesi Foto</h3>
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
</section>

@include('partials.client.review-modal')
@endsection