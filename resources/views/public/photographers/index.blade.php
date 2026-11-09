@extends('layouts.app')

@section('title', 'Eksplorasi Fotografer Profesional — LensMatch')

@section('content')
<div id="photographers-overview-page" class="bg-white dark:bg-gray-900 min-h-screen">

  <!-- 1. HERO SECTION (Pasted Image 1 Reference: Search Bar on Left + Floating Photographer Avatars on Right) -->
  <section class="w-full relative bg-gradient-to-b from-amber-50/70 via-amber-50/20 to-white dark:from-gray-800/80 dark:via-gray-800/30 dark:to-gray-900 pt-20 sm:pt-28 lg:pt-36 pb-16 sm:pb-24 lg:pb-32 overflow-hidden reveal-on-scroll">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
        
        <!-- Left Column: Headline, Description, Search Bar, & Quick Tags (7 Cols) -->
        <div class="lg:col-span-7 space-y-6 text-left">
          
          <!-- Main Bold Headline -->
          <h1 class="text-3xl sm:text-5xl lg:text-[54px] font-black text-gray-900 dark:text-white tracking-tight leading-[1.12]">
            Temukan fotografer terbaik untuk <span class="text-amber-500 dark:text-amber-400">momen berhargamu</span>
          </h1>

          <!-- Subtitle Description -->
          <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400 max-w-xl leading-relaxed">
            Bandingkan portofolio terverifikasi, transparansi paket harga, dan pesan sesi foto langsung dari {{ $totalPhotographers }}+ fotografer profesional di seluruh kota Indonesia.
          </p>

          <!-- Search Bar Container with Action Button (Sesuai Pasted Image 1) -->
          <div class="pt-2 max-w-xl">
            <form action="{{ route('public.photographers.katalog') }}" method="GET" class="relative flex items-center shadow-lg rounded-2xl bg-white dark:bg-gray-800 p-2 border border-gray-200/80 dark:border-gray-700">
              <div class="pl-3.5 pr-2 text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                </svg>
              </div>
              <input type="text" name="q" placeholder="Cari nama fotografer, kota, atau jenis sesi foto..." class="w-full bg-transparent text-xs sm:text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none py-2.5 font-medium" />
              <button type="submit" class="px-5 sm:px-6 py-3 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs sm:text-sm rounded-xl transition duration-200 shrink-0 shadow-xs flex items-center gap-2">
                <span>Cari</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
              </button>
            </form>
          </div>

          <!-- Quick Topic Pills (Sesuai Pasted Image 1) -->
          <div class="flex flex-wrap items-center gap-2 pt-1 text-xs">
            <span class="text-gray-400 font-bold text-[11px] uppercase tracking-wider">Populer:</span>
            <a href="{{ route('public.photographers.katalog', ['category' => '1']) }}" class="px-3 py-1 bg-white dark:bg-gray-800 hover:bg-amber-100 hover:text-amber-900 text-gray-600 dark:text-gray-300 rounded-full border border-gray-200/80 dark:border-gray-700 font-medium transition text-xs shadow-2xs">
              Wedding & Prewedding
            </a>
            <a href="{{ route('public.photographers.katalog', ['category' => '2']) }}" class="px-3 py-1 bg-white dark:bg-gray-800 hover:bg-amber-100 hover:text-amber-900 text-gray-600 dark:text-gray-300 rounded-full border border-gray-200/80 dark:border-gray-700 font-medium transition text-xs shadow-2xs">
              Vacation & Travel
            </a>
            <a href="{{ route('public.photographers.katalog', ['category' => '4']) }}" class="px-3 py-1 bg-white dark:bg-gray-800 hover:bg-amber-100 hover:text-amber-900 text-gray-600 dark:text-gray-300 rounded-full border border-gray-200/80 dark:border-gray-700 font-medium transition text-xs shadow-2xs">
              Studio Portrait
            </a>
          </div>

        </div>

        <!-- Right Column: Dynamic Floating Portfolio Moments Showcase (100% Dynamic Database Portfolios) -->
        <div class="lg:col-span-5 relative flex items-center justify-center min-h-[420px] sm:min-h-[480px] lg:min-h-[500px]">
          
          @php
            $p0 = $showcasePortfolios[0] ?? null;
            $img0 = $p0?->medias?->first()?->media ?? asset('images/avatar-default.png');
            $title0 = $p0?->judul ?? 'Portofolio Karya';
            $p1 = $showcasePortfolios[1] ?? null;
            $img1 = $p1?->medias?->first()?->media ?? $img0;
            $title1 = $p1?->judul ?? 'Portofolio Karya';
            $p2 = $showcasePortfolios[2] ?? null;
            $img2 = $p2?->medias?->first()?->media ?? $img0;
            $title2 = $p2?->judul ?? 'Portofolio Karya';
            $p3 = $showcasePortfolios[3] ?? null;
            $img3 = $p3?->medias?->first()?->media ?? $img1;
            $title3 = $p3?->judul ?? 'Portofolio Karya';
          @endphp

          <!-- Background Organic Soft Amber Glow Backdrop -->
          <div class="absolute w-80 sm:w-[420px] h-80 sm:h-[420px] rounded-full bg-gradient-to-tr from-amber-300/40 via-amber-200/25 to-amber-100/10 blur-3xl -z-10 pointer-events-none"></div>

          <!-- Card 1 (Top-Left): Dynamic Portfolio 1 (Tilted Left, Full Photo Edge-to-Edge) -->
          <div class="absolute top-2 left-0 sm:left-2 z-10 w-44 sm:w-52 h-32 sm:h-36 rounded-2xl overflow-hidden shadow-xl transform -rotate-6 hover:rotate-0 hover:scale-105 hover:z-40 transition-all duration-300 group cursor-pointer">
            <img src="{{ $img0 }}" alt="{{ $title0 }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" />
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>
            <span class="absolute bottom-2.5 left-2.5 px-2.5 py-0.5 rounded-md bg-black/60 backdrop-blur-md text-white text-[10px] font-extrabold tracking-wide truncate max-w-[85%]">
              {{ $title0 }}
            </span>
          </div>

          <!-- Card 2 (Top-Right): Dynamic Portfolio 2 (Tilted Right, Full Photo Edge-to-Edge) -->
          <div class="absolute top-6 right-0 sm:right-2 z-10 w-40 sm:w-48 h-28 sm:h-32 rounded-2xl overflow-hidden shadow-xl transform rotate-6 hover:rotate-0 hover:scale-105 hover:z-40 transition-all duration-300 group cursor-pointer">
            <img src="{{ $img1 }}" alt="{{ $title1 }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" />
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>
            <span class="absolute bottom-2.5 left-2.5 px-2.5 py-0.5 rounded-md bg-black/60 backdrop-blur-md text-white text-[10px] font-extrabold tracking-wide truncate max-w-[85%]">
              {{ $title1 }}
            </span>
          </div>

          <!-- Card 3 (Center Main Hero): Dynamic Portfolio 3 (Full Photo, Prominent Center) -->
          <div class="relative z-20 w-52 sm:w-60 h-52 sm:h-60 rounded-3xl overflow-hidden shadow-2xl hover:scale-105 hover:z-40 transition-all duration-300 group cursor-pointer my-auto">
            <img src="{{ $img2 }}" alt="{{ $title2 }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" />
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
            <div class="absolute bottom-3 left-3 right-3 text-left">
              <span class="px-2.5 py-0.5 rounded-md bg-black/60 backdrop-blur-md text-white text-[10px] font-extrabold tracking-wide inline-block truncate max-w-full">
                {{ $title2 }}
              </span>
            </div>
          </div>

          <!-- Card 4 (Bottom-Right): Dynamic Portfolio 4 (Slight Tilt, Full Photo Edge-to-Edge) -->
          <div class="absolute bottom-2 right-1 sm:right-4 z-20 w-44 sm:w-52 h-30 sm:h-34 rounded-2xl overflow-hidden shadow-xl transform rotate-3 hover:rotate-0 hover:scale-105 hover:z-40 transition-all duration-300 group cursor-pointer">
            <img src="{{ $img3 }}" alt="{{ $title3 }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" />
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>
            <span class="absolute bottom-2.5 left-2.5 px-2.5 py-0.5 rounded-md bg-black/60 backdrop-blur-md text-white text-[10px] font-extrabold tracking-wide truncate max-w-[85%]">
              {{ $title3 }}
            </span>
          </div>

          <!-- Floating Badge (Bottom-Left): Clean Rating & Total Pemesanan Stats (100% Dynamic Database Values) -->
          <div class="absolute bottom-6 left-0 sm:left-2 z-30 bg-white/95 dark:bg-gray-800/95 backdrop-blur-md px-4 py-2.5 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700/80 text-left transform -rotate-2 hover:rotate-0 transition duration-200">
            <div class="flex items-center gap-3">
              <div>
                <p class="text-xs font-black text-gray-900 dark:text-white leading-tight">@if($avgRating && $avgRating !== '-') {{ $avgRating }} / 5.0 @else - @endif</p>
                <p class="text-[10px] text-gray-500 dark:text-gray-400 font-semibold">Rata-rata Rating</p>
              </div>
              <div class="h-6 w-px bg-gray-200 dark:bg-gray-700"></div>
              <div>
                <p class="text-xs font-black text-gray-900 dark:text-white leading-tight">{{ $totalCompletedBookings }}+</p>
                <p class="text-[10px] text-gray-500 dark:text-gray-400 font-semibold">Total Pemesanan</p>
              </div>
            </div>
          </div>

        </div>

      </div>
    </div>
  </section>

  <!-- BODY CONTENT WRAPPER -->
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-20 lg:space-y-28 py-16 sm:py-20">

    <!-- 2. CATEGORY SECTION (2-Column Horizontal Cards SweetEscape Style) -->
    <section class="w-full space-y-10 reveal-on-scroll">
      
      <!-- Section Header Row (Header Left, Subtext Right - E-Learning Style) -->
      <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-2">
        <div class="space-y-2 max-w-xl">
          <h2 class="text-3xl sm:text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
            Pilihan <span class="text-amber-500 dark:text-amber-400">Kategori Foto</span>
          </h2>
        </div>

        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 max-w-md leading-relaxed">
          Temukan fotografer spesialis sesuai dengan kebutuhan momen berharga, perayaan penting, dan gaya visual Anda.
        </p>
      </div>

      <!-- 2-Column Horizontal Category Grid (Uniform Size & Left-Aligned Button Matching Pasted Image 2 & 3) -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
        @foreach($categories as $category)
          <!-- Category Horizontal Card with Uniform Sandstone Beige & Seamless Edge Blend -->
          <div class="bg-[#eae2d5] dark:bg-[#28231d] rounded-3xl border border-black/[0.04] dark:border-white/[0.06] overflow-hidden shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col sm:flex-row group h-full min-h-[220px] sm:h-[220px] relative">
            
            <!-- Left Info Content (Left-Aligned, Spaced Evenly) -->
            <div class="p-6 sm:p-7 flex flex-col justify-between flex-1 min-w-0 space-y-3 z-10">
              
              <div class="space-y-2 text-left">
                <h3 class="text-xl sm:text-2xl font-black text-gray-900 dark:text-white group-hover:text-amber-500 transition leading-snug">
                  {{ $category->nama_kategori }}
                </h3>
                <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300 leading-relaxed line-clamp-2">
                  {{ $category->deskripsi ?? 'Dokumentasi sesi foto profesional berkualitas tinggi untuk mengabadikan setiap momen terbaik Anda.' }}
                </p>
              </div>

              <!-- Action Button Row (LEFT-ALIGNED SEJAJAR VERTIKAL DENGAN TEKS DI ATASNYA) -->
              <div class="pt-2 flex items-center justify-start mt-auto">
                <a href="{{ route('public.photographers.katalog', ['category' => $category->id]) }}" class="px-5 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs rounded-xl shadow-xs transition duration-200 shrink-0 inline-flex items-center gap-1.5 group-hover:translate-x-0.5">
                  <span>Lihat Fotografer</span>
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                </a>
              </div>

            </div>

            <!-- Right Image Frame with Targeted Edge Blend (Spacious Clear Photo) -->
            <div class="w-full sm:w-1/2 md:w-5/12 h-48 sm:h-full shrink-0 relative overflow-hidden -ml-0 sm:-ml-8 bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
              @if($category->icon_url)
                <img 
                  src="{{ $category->icon_url }}" 
                  alt="{{ $category->nama_kategori }}" 
                  class="w-full h-full object-cover group-hover:scale-105 transition duration-700" 
                />
              @else
                <svg class="w-12 h-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z"/></svg>
              @endif
              <!-- Light Mode Wash: Focused only on the left transition edge -->
              <div class="absolute inset-y-0 left-0 w-24 sm:w-32 pointer-events-none dark:hidden" style="background: linear-gradient(to right, #eae2d5 0%, rgba(234, 226, 213, 0.85) 35%, rgba(234, 226, 213, 0) 100%);"></div>
              <!-- Dark Mode Wash: Focused only on the left transition edge -->
              <div class="absolute inset-y-0 left-0 w-24 sm:w-32 pointer-events-none hidden dark:block" style="background: linear-gradient(to right, #28231d 0%, rgba(40, 35, 29, 0.85) 35%, rgba(40, 35, 29, 0) 100%);"></div>
            </div>

          </div>
        @endforeach
      </div>

    </section>

    <!-- 3. PREVIEW FEATURED PHOTOGRAPHERS (E-Learning Style Preview Grid) -->
    <section class="w-full space-y-10 reveal-on-scroll">
      
      <!-- Section Header Row -->
      <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-2">
        <div class="space-y-2 max-w-2xl">
          <span class="text-[11px] font-extrabold uppercase tracking-widest text-amber-600 dark:text-amber-400">
            REKOMENDASI KOMUNITAS
          </span>
          <h2 class="text-3xl sm:text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
            Fotografer Pilihan Bulan Ini
          </h2>
          <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 leading-relaxed">
            Fotografer terverifikasi dengan ulasan bintang lima, portofolio memukau, dan jaminan pembayaran aman.
          </p>
        </div>


      </div>

      <!-- Featured Photographers 4-Equal Cards Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-7">
        @foreach($featuredPhotographers as $p)
          @php
            $lowestPrice = $p->services->where('tarif_harga', '>', 0)->min('tarif_harga');
            $coverPortfolio = $p->portfolios->first()?->medias?->first()?->media;
            $avatarUrl = $p->foto ? (str_starts_with($p->foto, 'http') ? $p->foto : asset('storage/' . $p->foto)) : null;
            $rating = $p->rating_average;
            $reviewCount = $p->testimonials->count();
          @endphp

          <!-- Standard Photographer Card (LensMatch UI Standard) -->
          <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-200/80 dark:border-gray-700/80 overflow-hidden shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between group h-full">
            
            <div>
              <!-- Top Cover Image Frame -->
              <div class="relative h-56 w-full overflow-hidden bg-gray-100 dark:bg-gray-700 shrink-0">
                @if($coverPortfolio)
                  <img src="{{ $coverPortfolio }}" alt="{{ $p->nama }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" />
                @else
                  <div class="w-full h-full flex flex-col items-center justify-center bg-gray-100 dark:bg-gray-800 text-gray-400">
                    <svg class="w-10 h-10 mb-1" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
                    <span class="text-[10px] font-semibold">Belum ada portofolio</span>
                  </div>
                @endif
                <!-- Rating Badge Top-Right -->
                <div class="absolute top-4 right-4 bg-white/95 dark:bg-gray-900/95 text-gray-900 dark:text-white text-xs font-black px-2.5 py-1 rounded-lg flex items-center gap-1 shadow-sm">
                  @if($rating)
                    <svg class="w-3.5 h-3.5 text-amber-400 fill-current" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                    <span>{{ $rating }}</span>
                    <span class="text-[10px] text-gray-400 font-medium">({{ $reviewCount }})</span>
                  @else
                    <span class="text-amber-500 text-[11px] font-bold">Baru</span>
                  @endif
                </div>
              </div>

              <!-- Card Body Content -->
              <div class="px-6 sm:px-7 pt-4 pb-0">
                <!-- Location (Subtle & Compact Above Name) -->
                <div class="flex items-center gap-1.5 text-[11px] text-gray-400 dark:text-gray-400 font-light mb-2.5">
                  <svg class="w-3.5 h-3.5 text-gray-400 dark:text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                  </svg>
                  <span class="truncate uppercase">{{ $p->city->name ?? ($p->alamat ?? '-') }}</span>
                </div>

                <!-- Avatar & Name Row -->
                <div class="flex items-center gap-3">
                  @if($avatarUrl)
                    <img src="{{ $avatarUrl }}" alt="{{ $p->nama }}" class="w-10 h-10 rounded-xl object-cover border-2 border-amber-400 shadow-xs shrink-0" />
                  @else
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-amber-300 text-white font-black text-xs flex items-center justify-center border-2 border-amber-400 shadow-xs shrink-0">
                      {{ strtoupper(substr($p->nama ?? 'F', 0, 2)) }}
                    </div>
                  @endif
                  <div class="flex-1 min-w-0">
                    <h3 class="font-extrabold text-base sm:text-lg text-gray-900 dark:text-white group-hover:text-amber-500 transition leading-snug truncate">
                      {{ $p->nama }}
                    </h3>
                  </div>
                </div>

                <!-- Categories as Plain Text (Under Profile, Comma Separated) -->
                @if($p->categories && $p->categories->isNotEmpty())
                  <div class="mt-2">
                    <p class="text-xs font-light text-amber-700 dark:text-amber-300 leading-snug">
                      {{ $p->categories->pluck('nama_kategori')->join(', ') }}
                    </p>
                  </div>
                @endif

                <!-- Bio Description (Under Categories with clear margin, max 500 chars) -->
                <div class="mt-4 pt-1">
                  <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                    {{ !empty(trim($p->deskripsi_bio ?? '')) ? \Illuminate\Support\Str::limit($p->deskripsi_bio, 180) : 'Fotografer profesional terverifikasi siap mengabadikan momen terbaik Anda.' }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Bottom Section: Price & Full-Width Button -->
            <div class="px-6 sm:px-7 pt-3.5 pb-6 sm:pb-7 space-y-3 mt-auto">
              <div class="space-y-0.5">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Tarif Layanan</span>
                @if($lowestPrice)
                  <p class="text-gray-900 dark:text-white font-black text-base sm:text-lg leading-tight">
                    Mulai Rp {{ number_format($lowestPrice, 0, ',', '.') }}
                  </p>
                @else
                  <p class="text-gray-400 dark:text-gray-500 font-semibold text-xs leading-tight">
                    Belum ada paket
                  </p>
                @endif
              </div>
              <div class="pt-0.5">
                <a href="{{ url('/fotografer/' . $p->id) }}" class="inline-flex items-center justify-center gap-1.5 w-full text-center py-3 bg-[#222222] dark:bg-gray-700 text-white hover:bg-amber-400 hover:text-gray-900 rounded-xl text-xs font-extrabold transition duration-200 shadow-xs">
                  <span>Lihat Profil</span>
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                </a>
              </div>
            </div>

          </div>
        @endforeach
      </div>

      <!-- Prominent "Lihat Lebih Banyak Fotografer" CTA Button Container -->
      <div class="pt-6 text-center">
        <a href="{{ route('public.photographers.katalog') }}" class="inline-flex items-center justify-center gap-2.5 px-9 py-4 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-sm rounded-2xl shadow-md hover:shadow-xl transition duration-300 transform hover:-translate-y-0.5 group">
          <span>Lihat Lebih Banyak Fotografer</span>
          <svg class="w-4 h-4 transition-transform group-hover:translate-x-1.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
        </a>
      </div>

    </section>

  </div>


</div>
@endsection
