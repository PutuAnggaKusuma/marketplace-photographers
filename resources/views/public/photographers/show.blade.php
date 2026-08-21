@extends('layouts.app')

@section('title', $photographer->nama . ' — Profil & Price List Fotografer LensMatch')

@section('content')
<!-- Photographer Profile Header Banner -->
<section class="bg-gradient-to-b from-gray-900 to-gray-800 text-white py-12 lg:py-16">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col md:flex-row items-center md:items-start gap-6 lg:gap-8 text-center md:text-left">
      
      <!-- Avatar Image -->
      <img onerror="this.onerror=null;this.src=window.SVG_IMAGE_FALLBACK||'data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='800' height='600' viewBox='0 0 800 600' fill='none'%3E%3Crect width='800' height='600' fill='%23F3F4F6'/%3E%3Cpath d='M360 260C360 248.954 368.954 240 380 240H420C431.046 240 440 248.954 440 260V265H450C466.569 265 480 278.431 480 295V345C480 361.569 466.569 375 450 375H350C333.431 375 320 361.569 320 345V295C320 278.431 333.431 265 350 265H360V260Z' stroke='%239CA3AF' stroke-width='12' stroke-linecap='round' stroke-linejoin='round'/%3E%3Ccircle cx='400' cy='320' r='30' stroke='%239CA3AF' stroke-width='12'/%3E%3Cline x1='310' y1='240' x2='490' y2='390' stroke='%23EF4444' stroke-width='10' stroke-linecap='round'/%3E%3Ctext x='400' y='450' font-family='sans-serif' font-size='22' font-weight='700' fill='%236B7280' text-anchor='middle'%3EGambar Tidak Dapat Dimuat%3C/text%3E%3C/svg%3E';" src="{{ $photographer->foto }}" alt="{{ $photographer->nama }}" class="w-32 h-32 lg:w-40 lg:h-40 rounded-full object-cover border-4 border-amber-400 shadow-2xl shrink-0" />

      <!-- Profile Header Metadata -->
      <div class="space-y-3 flex-1">
        <div class="flex flex-wrap items-center justify-center md:justify-start gap-2">
          <span class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-widest bg-amber-400 text-gray-900 shadow-md">
            Verified Professional
          </span>
          <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-white/10 text-gray-300 flex items-center gap-1">
            <svg class="w-3 h-3 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
            <span>{{ $photographer->city->name ?? $photographer->alamat }}</span>
          </span>
        </div>

        <h1 class="text-3xl sm:text-4xl font-black text-white tracking-tight">
          {{ $photographer->nama }}
        </h1>

        <p class="text-sm text-gray-300 max-w-3xl leading-relaxed">
          {{ $photographer->deskripsi_bio }}
        </p>

        <!-- Stats Counter & Categories Badges -->
        <div class="flex flex-wrap items-center justify-center md:justify-start gap-4 pt-2 text-xs">
          <div class="flex items-center gap-1 text-amber-400 font-bold text-sm">
             {{ $photographer->rating_average }} <span class="text-gray-400 font-normal">({{ $photographer->testimonials->count() }} Ulasan Klien)</span>
          </div>
          <span class="text-gray-600">•</span>
          <div class="flex flex-wrap gap-1.5">
            @foreach($photographer->categories as $cat)
              <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-gray-800 text-amber-300 border border-gray-700">
                {{ $cat->nama_kategori }}
              </span>
            @endforeach
          </div>
        </div>
      </div>

      <!-- Quick Action CTA -->
      <div class="shrink-0 space-y-2 text-center w-full md:w-auto">
        <a href="{{ url('/booking/create?photographer_id=' . $photographer->id) }}" class="block w-full px-8 py-3.5 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-sm rounded-xl transition shadow-lg text-center">
          Sesi Foto Sekarang
        </a>
        <a href="{{ route('chat.start', $photographer->id) }}" class="block w-full px-8 py-2.5 bg-gray-800 hover:bg-gray-700 text-white font-bold text-xs rounded-xl transition border border-gray-700 text-center">
          Tanya & Chat Studio
        </a>
      </div>

    </div>
  </div>
</section>

<!-- Detailed Tabs & Content Section -->
<section class="py-12 bg-white dark:bg-gray-900/50 min-h-screen" x-data="{ activeTab: 'services' }">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

    <!-- Tab Navigation Buttons -->
    <div class="flex items-center gap-2 border-b border-gray-200 dark:border-gray-700 overflow-x-auto pb-1">
      <button @click="activeTab = 'services'" :class="activeTab === 'services' ? 'border-amber-400 text-amber-600 dark:text-amber-400 font-extrabold' : 'border-transparent text-gray-500 hover:text-gray-800 dark:text-gray-400 font-semibold'" class="px-5 py-3 border-b-2 text-sm transition whitespace-nowrap flex items-center gap-2">
        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
        <span>Paket Layanan & Price List ({{ $photographer->services->count() }})</span>
      </button>
      <button @click="activeTab = 'portfolio'" :class="activeTab === 'portfolio' ? 'border-amber-400 text-amber-600 dark:text-amber-400 font-extrabold' : 'border-transparent text-gray-500 hover:text-gray-800 dark:text-gray-400 font-semibold'" class="px-5 py-3 border-b-2 text-sm transition whitespace-nowrap flex items-center gap-2">
        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
        <span>Galeri Portofolio ({{ $photographer->portfolios->count() }})</span>
      </button>
      <button @click="activeTab = 'availability'" :class="activeTab === 'availability' ? 'border-amber-400 text-amber-600 dark:text-amber-400 font-extrabold' : 'border-transparent text-gray-500 hover:text-gray-800 dark:text-gray-400 font-semibold'" class="px-5 py-3 border-b-2 text-sm transition whitespace-nowrap flex items-center gap-2">
        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
        <span>Kalender Ketersediaan</span>
      </button>
      <button @click="activeTab = 'reviews'" :class="activeTab === 'reviews' ? 'border-amber-400 text-amber-600 dark:text-amber-400 font-extrabold' : 'border-transparent text-gray-500 hover:text-gray-800 dark:text-gray-400 font-semibold'" class="px-5 py-3 border-b-2 text-sm transition whitespace-nowrap flex items-center gap-2">
        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
        <span>Ulasan Klien ({{ $photographer->testimonials->count() }})</span>
      </button>
    </div>

    <!-- TAB 1: PAKET LAYANAN & PRICE LIST -->
    <div x-show="activeTab === 'services'" class="space-y-6">
      <h2 class="text-xl font-black text-gray-900 dark:text-white">Daftar Paket Layanan & Sesi Foto</h2>
      
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($photographer->services as $srv)
          <div class="bg-white dark:bg-gray-800 p-6 sm:p-7 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm flex flex-col justify-between space-y-6">
            <div class="space-y-4">
              <div class="space-y-1">
                <h3 class="text-lg font-extrabold text-gray-900 dark:text-white">{{ $srv->nama_layanan }}</h3>
                <p class="text-xs text-gray-500 leading-relaxed">{{ $srv->deskripsi_layanan }}</p>
              </div>

              <div class="text-2xl font-black text-amber-600 dark:text-amber-400">
                Rp {{ number_format($srv->tarif_harga, 0, ',', '.') }}
              </div>

              <!-- Included Features List -->
              @if($srv->details->count() > 0)
                <ul class="space-y-2 pt-2 border-t border-gray-100 dark:border-gray-700 text-xs text-gray-700 dark:text-gray-300">
                  @foreach($srv->details as $det)
                    <li class="flex items-center gap-2">
                      <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                      <span>{{ $det->nama_fitur }} @if($det->tarif_harga > 0) (+Rp {{ number_format($det->tarif_harga, 0, ',', '.') }}) @endif</span>
                    </li>
                  @endforeach
                </ul>
              @endif
            </div>

            <a href="{{ url('/booking/create?photographer_id=' . $photographer->id . '&service_id=' . $srv->id) }}" class="w-full py-3 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs rounded-xl transition text-center shadow-sm block">
              Pilih Paket & Booking
            </a>
          </div>
        @endforeach
      </div>
    </div>

    <!-- TAB 2: GALERI PORTOFOLIO -->
    <div x-show="activeTab === 'portfolio'" class="space-y-6">
      <h2 class="text-xl font-black text-gray-900 dark:text-white">Karya & Portofolio Fotografi</h2>

      @foreach($photographer->portfolios as $port)
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-200 dark:border-gray-700 space-y-4">
          <div class="space-y-1">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $port->judul }}</h3>
            <p class="text-xs text-gray-500">{{ $port->deskripsi }}</p>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            @foreach($port->medias as $med)
              <div class="h-56 rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-900 border border-gray-200/60 dark:border-gray-700">
                <img onerror="this.onerror=null;this.src=window.SVG_IMAGE_FALLBACK||'data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='800' height='600' viewBox='0 0 800 600' fill='none'%3E%3Crect width='800' height='600' fill='%23F3F4F6'/%3E%3Cpath d='M360 260C360 248.954 368.954 240 380 240H420C431.046 240 440 248.954 440 260V265H450C466.569 265 480 278.431 480 295V345C480 361.569 466.569 375 450 375H350C333.431 375 320 361.569 320 345V295C320 278.431 333.431 265 350 265H360V260Z' stroke='%239CA3AF' stroke-width='12' stroke-linecap='round' stroke-linejoin='round'/%3E%3Ccircle cx='400' cy='320' r='30' stroke='%239CA3AF' stroke-width='12'/%3E%3Cline x1='310' y1='240' x2='490' y2='390' stroke='%23EF4444' stroke-width='10' stroke-linecap='round'/%3E%3Ctext x='400' y='450' font-family='sans-serif' font-size='22' font-weight='700' fill='%236B7280' text-anchor='middle'%3EGambar Tidak Dapat Dimuat%3C/text%3E%3C/svg%3E';" src="{{ $med->media }}" alt="{{ $port->judul }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300 cursor-pointer" />
              </div>
            @endforeach
          </div>
        </div>
      @endforeach
    </div>

    <!-- TAB 3: KALENDER KETERSEDIAAN -->
    <div x-show="activeTab === 'availability'" class="space-y-6">
      <h2 class="text-xl font-black text-gray-900 dark:text-white">Jadwal & Ketersediaan Fotografer</h2>

      <div class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-2xl border border-gray-200 dark:border-gray-700 space-y-4">
        <p class="text-xs text-gray-500 leading-relaxed">
          Fotografer ini saat ini membuka jadwal booking untuk 30 hari ke depan. Silakan pilih tanggal yang belum terisi saat membuat pengajuan booking.
        </p>

        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 text-xs text-emerald-800 dark:text-emerald-300 font-semibold flex items-center gap-2">
          <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
          <span>Fotografer Siap Menerima Booking Baru Minggu Ini!</span>
        </div>
      </div>
    </div>

    <!-- TAB 4: ULASAN & RATING KLIEN -->
    <div x-show="activeTab === 'reviews'" class="space-y-6">
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-black text-gray-900 dark:text-white">Ulasan & Rating Dari Klien</h2>
        <span class="text-xs text-gray-500">Ulasan Asli Terverifikasi Sesi Foto</span>
      </div>

      <!-- Rating Summary Breakdown Card -->
      <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
        <!-- Average Score Column -->
        <div class="md:col-span-4 text-center md:border-r border-gray-100 dark:border-gray-700 md:pr-6 space-y-2">
          <div class="text-5xl font-black text-gray-900 dark:text-white tracking-tight">{{ $avgRating }}</div>
          <div class="flex items-center justify-center gap-1 text-amber-400">
            @for($i = 1; $i <= 5; $i++)
              <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
            @endfor
          </div>
          <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Berdasarkan {{ $totalReviews }} Ulasan Klien Terverifikasi</p>
        </div>

        <!-- Progress Bars Column -->
        <div class="md:col-span-8 space-y-2">
          @foreach([5, 4, 3, 2, 1] as $sVal)
            @php
              $cnt = $starCounts[$sVal] ?? 0;
              $pct = $totalReviews > 0 ? round(($cnt / $totalReviews) * 100) : 0;
            @endphp
            <div class="flex items-center gap-3 text-xs">
              <span class="w-16 font-extrabold text-gray-700 dark:text-gray-300 flex items-center gap-1">
                <span>{{ $sVal }}</span>
                <svg class="w-3.5 h-3.5 text-amber-400 fill-amber-400" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
              </span>
              <div class="flex-1 h-2.5 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                <div class="h-full bg-amber-400 rounded-full" style="width: {{ $pct }}%"></div>
              </div>
              <span class="w-12 text-right text-gray-400 text-[11px] font-bold">{{ $cnt }} ({{ $pct }}%)</span>
            </div>
          @endforeach
        </div>
      </div>

      <!-- Testimonial Cards List -->
      @if($testimonials->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
          @foreach($testimonials as $t)
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm space-y-3 flex flex-col justify-between">
              <div class="space-y-2">
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-amber-500 to-amber-300 flex items-center justify-center text-white font-black text-xs shadow-xs">
                      {{ strtoupper(substr($t->client->nama ?? 'K', 0, 2)) }}
                    </div>
                    <div>
                      <h4 class="font-extrabold text-xs text-gray-900 dark:text-white">{{ $t->client->nama ?? 'Klien LensMatch' }}</h4>
                      <p class="text-[10px] text-gray-400">{{ $t->created_at->format('d M Y') }}</p>
                    </div>
                  </div>

                  <!-- Star Rating Icons -->
                  <div class="flex items-center gap-0.5 text-amber-400">
                    @for($st = 1; $st <= 5; $st++)
                      <svg class="w-4 h-4 {{ $st <= $t->rating ? 'fill-amber-400 text-amber-400' : 'text-gray-200 dark:text-gray-700 fill-transparent' }}" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                      </svg>
                    @endfor
                  </div>
                </div>

                <p class="text-xs text-gray-700 dark:text-gray-300 leading-relaxed italic">
                  "{{ $t->deskripsi_review }}"
                </p>
              </div>

              <div class="pt-2 border-t border-gray-100 dark:border-gray-700/60 flex items-center justify-between text-[10px] text-gray-400">
                <span class="font-semibold">Sesi Foto Terverifikasi</span>
                <span class="text-emerald-600 dark:text-emerald-400 font-extrabold flex items-center gap-1">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                  Verified Order
                </span>
              </div>
            </div>
          @endforeach
        </div>
      @else
        <div class="bg-white dark:bg-gray-800 p-12 rounded-2xl border border-gray-200 dark:border-gray-700 text-center space-y-2">
          <div class="w-12 h-12 rounded-full bg-amber-50 dark:bg-amber-950/60 text-amber-500 mx-auto flex items-center justify-center text-xl font-bold">
            ⭐
          </div>
          <h3 class="text-sm font-extrabold text-gray-900 dark:text-white">Belum Ada Ulasan Klien</h3>
          <p class="text-xs text-gray-500 max-w-sm mx-auto">Ulasan resmi dari Klien yang telah mereservasi sesi foto akan ditampilkan di sini.</p>
        </div>
      @endif
    </div>

  </div>
</section>
@endsection