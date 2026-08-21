@extends('layouts.app')

@section('title', 'Eksplorasi Fotografer Profesional — LensMatch')

@section('content')
<div id="photographers-overview-page" class="bg-white dark:bg-gray-900 min-h-screen">

  <!-- 1. HERO SECTION (Pasted Image 1 Reference: Search Bar on Left + Floating Photographer Avatars on Right) -->
  <section class="w-full relative bg-gradient-to-b from-amber-50/70 via-amber-50/20 to-white dark:from-gray-800/80 dark:via-gray-800/30 dark:to-gray-900 pt-12 sm:pt-16 pb-16 sm:pb-24 px-4 sm:px-6 lg:px-8 overflow-hidden reveal-on-scroll">
    <div class="max-w-7xl mx-auto">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
        
        <!-- Left Column: Headline, Description, Search Bar, & Quick Tags (7 Cols) -->
        <div class="lg:col-span-7 space-y-6 text-left">
          
          <!-- Kicker Tag -->
          <div>
            <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-[11px] font-extrabold bg-amber-100 text-amber-900 dark:bg-amber-900/50 dark:text-amber-300 border border-amber-200/80 dark:border-amber-800/60 uppercase tracking-widest shadow-2xs">
              MARKETPLACE FOTOGRAFER PROFESIONAL
            </span>
          </div>

          <!-- Main Bold Headline -->
          <h1 class="text-3xl sm:text-5xl lg:text-[54px] font-black text-gray-900 dark:text-white tracking-tight leading-[1.12]">
            Temukan fotografer terbaik untuk <span class="text-amber-500 dark:text-amber-400">momen berhargamu</span>
          </h1>

          <!-- Subtitle Description -->
          <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400 max-w-xl leading-relaxed">
            Bandingkan portofolio terverifikasi, transparansi paket harga, dan pesan sesi foto langsung dari 150+ fotografer profesional di seluruh kota Indonesia.
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
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
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

        <!-- Right Column: Dynamic Floating Portfolio Moments Showcase (Clean Full Photo Edge-to-Edge) -->
        <div class="lg:col-span-5 relative flex items-center justify-center min-h-[420px] sm:min-h-[480px] lg:min-h-[500px]">
          
          <!-- Background Organic Soft Amber Glow Backdrop -->
          <div class="absolute w-80 sm:w-[420px] h-80 sm:h-[420px] rounded-full bg-gradient-to-tr from-amber-300/40 via-amber-200/25 to-amber-100/10 blur-3xl -z-10 pointer-events-none"></div>

          <!-- Card 1 (Top-Left): Prewedding • Bali (Tilted Left, Full Photo Edge-to-Edge) -->
          <div class="absolute top-2 left-0 sm:left-2 z-10 w-44 sm:w-52 h-32 sm:h-36 rounded-2xl overflow-hidden shadow-xl transform -rotate-6 hover:rotate-0 hover:scale-105 hover:z-40 transition-all duration-300 group cursor-pointer">
            <img src="https://images.unsplash.com/photo-1519741497674-611481863552?w=600&q=80" alt="Prewedding Session" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" />
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>
            <span class="absolute bottom-2.5 left-2.5 px-2.5 py-0.5 rounded-md bg-black/60 backdrop-blur-md text-white text-[10px] font-extrabold tracking-wide">
              Prewedding • Bali
            </span>
          </div>

          <!-- Card 2 (Top-Right): Vacation & Travel • Sumba (Tilted Right, Full Photo Edge-to-Edge) -->
          <div class="absolute top-6 right-0 sm:right-2 z-10 w-40 sm:w-48 h-28 sm:h-32 rounded-2xl overflow-hidden shadow-xl transform rotate-6 hover:rotate-0 hover:scale-105 hover:z-40 transition-all duration-300 group cursor-pointer">
            <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=600&q=80" alt="Vacation Session" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" />
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>
            <span class="absolute bottom-2.5 left-2.5 px-2.5 py-0.5 rounded-md bg-black/60 backdrop-blur-md text-white text-[10px] font-extrabold tracking-wide">
              Vacation • Sumba
            </span>
          </div>

          <!-- Card 3 (Center Main Hero): Studio & Editorial Portrait (Full Photo, No Yellow Border, Prominent Center) -->
          <div class="relative z-20 w-52 sm:w-60 h-52 sm:h-60 rounded-3xl overflow-hidden shadow-2xl hover:scale-105 hover:z-40 transition-all duration-300 group cursor-pointer my-auto">
            <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=600&q=80" alt="Studio Portrait Session" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" />
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
            <div class="absolute top-3 right-3">
              <span class="px-2.5 py-0.5 rounded-md bg-amber-400 text-gray-900 text-[10px] font-black uppercase tracking-wider shadow-xs">
                Populer
              </span>
            </div>
            <div class="absolute bottom-3 left-3 right-3 space-y-0.5 text-left">
              <span class="px-2.5 py-0.5 rounded-md bg-black/60 backdrop-blur-md text-white text-[10px] font-extrabold tracking-wide inline-block">
                Studio & Portrait
              </span>
              <p class="text-[11px] text-gray-200 font-bold">Dokumentasi Kualitas Studio</p>
            </div>
          </div>

          <!-- Card 4 (Bottom-Right): Graduation / Wisuda • Jogja (Slight Tilt, Full Photo Edge-to-Edge) -->
          <div class="absolute bottom-2 right-1 sm:right-4 z-20 w-44 sm:w-52 h-30 sm:h-34 rounded-2xl overflow-hidden shadow-xl transform rotate-3 hover:rotate-0 hover:scale-105 hover:z-40 transition-all duration-300 group cursor-pointer">
            <img src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?w=600&q=80" alt="Wisuda Session" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" />
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>
            <span class="absolute bottom-2.5 left-2.5 px-2.5 py-0.5 rounded-md bg-black/60 backdrop-blur-md text-white text-[10px] font-extrabold tracking-wide">
              Wisuda • Yogyakarta
            </span>
          </div>

          <!-- Floating Badge (Bottom-Left): Clean Rating & Total Pemesanan Stats -->
          <div class="absolute bottom-6 left-0 sm:left-2 z-30 bg-white/95 dark:bg-gray-800/95 backdrop-blur-md px-4 py-2.5 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700/80 text-left transform -rotate-2 hover:rotate-0 transition duration-200">
            <div class="flex items-center gap-3">
              <div>
                <p class="text-xs font-black text-gray-900 dark:text-white leading-tight">4.9 / 5.0</p>
                <p class="text-[10px] text-gray-500 dark:text-gray-400 font-semibold">Rata-rata Rating</p>
              </div>
              <div class="h-6 w-px bg-gray-200 dark:bg-gray-700"></div>
              <div>
                <p class="text-xs font-black text-amber-500 leading-tight">2.400+</p>
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

    <!-- 2. CATEGORY SECTION (Pasted Image 3 Reference: 2-Column Horizontal Cards SweetEscape Style) -->
    <section class="w-full space-y-10 reveal-on-scroll">
      
      <!-- Section Header Row (Header Left, Subtext Right - E-Learning Style) -->
      <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-2">
        <div class="space-y-2 max-w-xl">
          <span class="text-[11px] font-extrabold uppercase tracking-widest text-amber-600 dark:text-amber-400">
            KATEGORI FOTOGRAFI
          </span>
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
          <!-- Category Horizontal Card with Uniform Sandstone Beige & Seamless Blur/Fade Mask (SweetEscape Style) -->
          <div class="bg-[#eae2d5] dark:bg-[#28231d] rounded-3xl border border-black/[0.04] dark:border-white/[0.06] overflow-hidden shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col sm:flex-row group h-full min-h-[220px] sm:h-[220px] relative">
            
            <!-- Left Info Content (Left-Aligned, Spaced Evenly) -->
            <div class="p-6 sm:p-7 flex flex-col justify-between flex-1 space-y-3 z-10">
              
              <div class="space-y-2 text-left">
                <h3 class="text-xl sm:text-2xl font-black text-gray-900 dark:text-white group-hover:text-amber-500 transition leading-snug line-clamp-1">
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
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                </a>
              </div>

            </div>

            <!-- Right Image Frame with Smooth Alpha Mask Fade to Card Background (No Hard Cutoff Line) -->
            <div class="w-full sm:w-1/2 md:w-5/12 h-48 sm:h-full shrink-0 relative overflow-hidden">
              <img 
                src="{{ $category->icon_url ?: 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=800&q=80' }}" 
                alt="{{ $category->nama_kategori }}" 
                class="w-full h-full object-cover group-hover:scale-105 transition duration-700 [mask-image:linear-gradient(to_bottom,transparent_0%,black_35%)] sm:[mask-image:linear-gradient(to_right,transparent_0%,black_35%)] [-webkit-mask-image:linear-gradient(to_bottom,transparent_0%,black_35%)] sm:[-webkit-mask-image:linear-gradient(to_right,transparent_0%,black_35%)]" 
              />
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
            Fotografer terverifikasi dengan ulasan bintang lima, portofolio memukau, dan jaminan escrow aman.
          </p>
        </div>


      </div>

      <!-- Featured Photographers 4-Equal Cards Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-7">
        @foreach($featuredPhotographers as $p)
          @php
            $lowestPrice = $p->services->min('tarif_harga') ?? 1500000;
            $coverPortfolio = $p->portfolios->first()?->medias?->first()?->media ?? 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=600&q=80';
            $avatarUrl = $p->foto_url ?? ($p->foto ? (str_starts_with($p->foto, 'http') ? $p->foto : asset('storage/' . $p->foto)) : 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400&q=80');
            $rating = $p->rating_average ?? 4.9;
            $reviewCount = $p->testimonials->count();
          @endphp

          <!-- Standard Photographer Card (LensMatch UI Standard) -->
          <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-200/80 dark:border-gray-700/80 overflow-hidden shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between group h-full">
            
            <div>
              <!-- Top Cover Image Frame -->
              <div class="relative h-56 w-full overflow-hidden bg-gray-100 dark:bg-gray-700 shrink-0">
                <img src="{{ $coverPortfolio }}" alt="{{ $p->nama }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" />
                <!-- Rating Badge Top-Right -->
                <div class="absolute top-4 right-4 bg-white/95 dark:bg-gray-900/95 text-gray-900 dark:text-white text-xs font-black px-2.5 py-1 rounded-lg flex items-center gap-1 shadow-sm">
                  <svg class="w-3.5 h-3.5 text-amber-400 fill-current" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                  <span>{{ $rating }}</span>
                  <span class="text-[10px] text-gray-400 font-medium">({{ $reviewCount }})</span>
                </div>
              </div>

              <!-- Card Body Content -->
              <div class="px-6 sm:px-7 pt-4 pb-0">
                <!-- Location (Subtle & Compact Above Name) -->
                <div class="flex items-center gap-1.5 text-[11px] text-gray-400 dark:text-gray-400 font-medium mb-2.5">
                  <svg class="w-3.5 h-3.5 text-gray-400 dark:text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                  </svg>
                  <span class="truncate">{{ $p->city->name ?? 'Indonesia' }}</span>
                </div>

                <!-- Avatar & Name Row -->
                <div class="flex items-center gap-3">
                  <img src="{{ $avatarUrl }}" alt="{{ $p->nama }}" class="w-10 h-10 rounded-xl object-cover border-2 border-amber-400 shadow-xs shrink-0" />
                  <h3 class="font-extrabold text-base sm:text-lg text-gray-900 dark:text-white truncate group-hover:text-amber-500 transition leading-snug">
                    {{ $p->nama }}
                  </h3>
                </div>

                <!-- Bio Description -->
                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed line-clamp-2 mt-2.5">
                  {{ $p->deskripsi_bio ?? 'Fotografer profesional terverifikasi siap mengabadikan momen terbaik Anda.' }}
                </p>

                <!-- Category Tags Row (Max 5) -->
                <div class="flex flex-wrap gap-1.5 pt-2.5 items-center">
                  @foreach($p->categories->take(5) as $cat)
                    <span class="px-2.5 py-1 bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 font-extrabold text-[10px] rounded-lg border border-amber-200/60 dark:border-amber-800/60 uppercase tracking-wider">
                      {{ $cat->nama_kategori }}
                    </span>
                  @endforeach
                </div>
              </div>
            </div>

            <!-- Bottom Section: Price & Full-Width Button -->
            <div class="px-6 sm:px-7 pt-3.5 pb-6 sm:pb-7 space-y-3 mt-auto">
              <div class="space-y-0.5">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Mulai Dari</span>
                <p class="text-gray-900 dark:text-white font-black text-base sm:text-lg leading-tight">
                  Rp {{ number_format($lowestPrice, 0, ',', '.') }}
                </p>
              </div>
              <div class="pt-0.5">
                <a href="{{ url('/fotografer/' . $p->id) }}" class="inline-flex items-center justify-center gap-1.5 w-full text-center py-3 bg-[#222222] dark:bg-gray-700 text-white hover:bg-amber-400 hover:text-gray-900 rounded-xl text-xs font-extrabold transition duration-200 shadow-xs">
                  <span>Lihat Profil</span>
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                </a>
              </div>
            </div>

          </div>
        @endforeach
      </div>

      <!-- Prominent "Lihat Lebih Banyak Fotografer" CTA Button Container (E-Learning Style) -->
      <div class="pt-6 text-center">
        <a href="{{ route('public.photographers.katalog') }}" class="inline-flex items-center justify-center gap-2.5 px-9 py-4 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-sm rounded-2xl shadow-md hover:shadow-xl transition duration-300 transform hover:-translate-y-0.5 group">
          <span>Lihat Lebih Banyak Fotografer</span>
          <svg class="w-4 h-4 transition-transform group-hover:translate-x-1.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
        </a>
      </div>

    </section>

  </div>

</div>
@endsection