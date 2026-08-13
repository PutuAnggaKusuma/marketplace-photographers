@extends('layouts.app')

@section('title', 'LensMatch — Platform Booking Fotografer Profesional Terpercaya')

@section('content')
<main class="min-h-screen space-y-16 sm:space-y-24 bg-white dark:bg-gray-900 text-gray-900 dark:text-white pb-0">
  
  <!-- 1. HERO SECTION (Truvista Centered Layout) -->
  <section class="w-full relative bg-gradient-to-b from-amber-50/70 via-amber-50/20 to-white dark:from-gray-800/80 dark:via-gray-800/30 dark:to-gray-900 pt-10 sm:pt-14 pb-4 sm:pb-6 px-4 sm:px-6 lg:px-8 overflow-hidden reveal-on-scroll">
    <div class="max-w-6xl mx-auto space-y-7 sm:space-y-9">
      
      <!-- Centered Hero Titles & Kicker Tag -->
      <div class="max-w-3xl mx-auto text-center space-y-4 pt-2">
        <div class="flex justify-center">
          <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-[11px] font-extrabold bg-amber-100 text-amber-900 dark:bg-amber-900/50 dark:text-amber-300 border border-amber-200/80 dark:border-amber-800/60 uppercase tracking-widest shadow-2xs">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" class="fill-current" xmlns="http://www.w3.org/2000/svg">
              <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/>
            </svg>
            Marketplace Fotografer Lokal Terverifikasi
          </span>
        </div>

        <h1 class="text-3xl sm:text-4xl lg:text-[48px] font-black text-gray-900 dark:text-white tracking-tight leading-[1.12]">
          Abadikan Momen Indah <br class="hidden sm:inline" />
          <span class="text-amber-600 dark:text-amber-400">Dimanapun Anda Berada.</span>
        </h1>

        <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300 font-normal leading-relaxed max-w-xl mx-auto text-center">
          Hubungkan momen berharga liburan, pernikahan, atau acara spesial Anda dengan fotografer profesional lokal. Transparan, aman, dan terlindungi kontrak kerja digital.
        </p>
      </div>

      <!-- Compact Centered Floating Search Box -->
      <div class="max-w-3xl mx-auto bg-white dark:bg-gray-800 p-3.5 sm:p-4 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 flex flex-col md:flex-row gap-3 items-center">
        <div class="relative flex-1 w-full">
          <div class="relative">
            <input type="text" placeholder="Cari kota (mis: Bali, Jakarta, Bandung)..." class="w-full pl-9 pr-4 py-2.5 text-xs text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-amber-400" />
            <svg class="w-3.5 h-3.5 absolute left-3 top-3 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
          </div>
        </div>

        <div class="w-full md:w-56 relative">
          <select class="w-full pl-3.5 pr-9 py-2.5 text-xs text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-amber-400 appearance-none cursor-pointer">
            <option value="">Semua Kategori Sesi</option>
            <option value="wedding">Wedding & Prewedding</option>
            <option value="travel">Vacation & Travel</option>
            <option value="event">Event & Konser</option>
            <option value="portrait">Studio & Portrait</option>
          </select>
          <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
          </div>
        </div>

        <div class="w-full md:w-auto">
          <a href="{{ url('/fotografer') }}" class="w-full md:w-auto px-6 py-2.5 bg-amber-400 hover:bg-amber-500 font-bold text-xs text-gray-900 rounded-xl transition shadow-md flex items-center justify-center gap-2">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="11" cy="11" r="8"></circle>
              <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            Temukan Fotografer
          </a>
        </div>
      </div>

      <!-- 5-Photo Staggered Floating Wave Gallery (Pasted Image 1 Floating Animation) -->
      <div class="grid grid-cols-5 gap-3.5 sm:gap-6 items-end max-w-6xl mx-auto pt-4 sm:pt-6">
        
        <!-- Image 1 (Far Left - Floating Animation 1) -->
        <div class="h-40 sm:h-56 lg:h-64 rounded-2xl overflow-hidden group border-2 border-white dark:border-gray-800 transition animate-float-1 shadow-md hover:shadow-xl">
          <img src="https://images.unsplash.com/photo-1542038784456-1ea8e935640e?w=500&q=80" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="Camera Gear" />
        </div>

        <!-- Image 2 (Mid Left - Floating Animation 2) -->
        <div class="h-56 sm:h-76 lg:h-[330px] rounded-2xl overflow-hidden group border-2 border-white dark:border-gray-800 transition animate-float-2 shadow-md hover:shadow-xl">
          <img src="https://images.unsplash.com/photo-1492691527719-9d1e07e534b4?w=500&q=80" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="Landscape Photo" />
        </div>

        <!-- Image 3 (Center - TALLEST HIGHLIGHT - Floating Animation 3) -->
        <div class="h-72 sm:h-96 lg:h-[400px] rounded-2xl overflow-hidden group border-2 border-white dark:border-gray-800 transition animate-float-3 shadow-lg hover:shadow-2xl">
          <img src="https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=600&q=80" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="Photographer Lens" />
        </div>

        <!-- Image 4 (Mid Right - Floating Animation 4) -->
        <div class="h-60 sm:h-80 lg:h-[350px] rounded-2xl overflow-hidden group border-2 border-white dark:border-gray-800 transition animate-float-4 shadow-md hover:shadow-xl">
          <img src="https://images.unsplash.com/photo-1520854221256-17451cc331bf?w=500&q=80" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="Editorial Photoshoot" />
        </div>

        <!-- Image 5 (Far Right - Floating Animation 5) -->
        <div class="h-40 sm:h-56 lg:h-64 rounded-2xl overflow-hidden group border-2 border-white dark:border-gray-800 transition animate-float-5 shadow-md hover:shadow-xl">
          <img src="https://images.unsplash.com/photo-1469371670807-013ccf25f16a?w=500&q=80" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="Romantic Moment" />
        </div>

      </div>

    </div>
  </section>

  <!-- 2. TRUST STATS STRIP (Clean Hairline Counter Bar with Spacious Padding) -->
  <section class="w-full max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 reveal-on-scroll">
    <div class="py-12 sm:py-16 border-y border-gray-200/80 dark:border-gray-800 grid grid-cols-2 lg:grid-cols-4 gap-8 text-center items-center">
      
      <div class="space-y-1">
        <p class="text-3xl sm:text-4xl font-black text-gray-900 dark:text-white tracking-tight">150+</p>
        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Fotografer Terverifikasi</p>
      </div>

      <div class="space-y-1 lg:border-l lg:border-gray-200/80 lg:dark:border-gray-800 lg:pl-6">
        <p class="text-3xl sm:text-4xl font-black text-gray-900 dark:text-white tracking-tight">35+</p>
        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kota Layanan Indonesia</p>
      </div>

      <div class="space-y-1 lg:border-l lg:border-gray-200/80 lg:dark:border-gray-800 lg:pl-6">
        <p class="text-3xl sm:text-4xl font-black text-gray-900 dark:text-white tracking-tight">2.400+</p>
        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sesi Foto Sukses</p>
      </div>

      <div class="space-y-1 lg:border-l lg:border-gray-200/80 lg:dark:border-gray-800 lg:pl-6">
        <div class="flex items-center justify-center gap-1.5 text-emerald-600 dark:text-emerald-400">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/></svg>
          <span class="text-3xl sm:text-4xl font-black tracking-tight">4.9 / 5.0</span>
        </div>
        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rating Kepuasan Klien</p>
      </div>

    </div>
  </section>

  <!-- 3. KATEGORI SESI FOTO (Clean White Canvas) -->
  <section class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10 reveal-on-scroll">
    
    <!-- Consistent Left-Aligned Header with Natural Spacing -->
    <div class="space-y-2">
      <span class="text-[11px] font-extrabold text-amber-600 dark:text-amber-400 uppercase tracking-widest">Kategori Fotografi</span>
      <h2 class="text-3xl sm:text-4xl lg:text-[40px] font-black text-gray-900 dark:text-white leading-[1.15] tracking-tight">Dokumentasi Momen Spesial Anda</h2>
      <p class="text-sm sm:text-base text-gray-500 dark:text-gray-400 leading-relaxed max-w-2xl">Temukan fotografer profesional sesuai dengan momen & milestone penting dalam hidup Anda.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-7">
      
      <!-- Card 1: Wedding & Prewedding (POPULER #1 Featured Card - WITH YELLOW BADGE) -->
      <a href="{{ url('/fotografer?cat=wedding-prewedding') }}" class="group relative rounded-2xl overflow-hidden min-h-[300px] flex flex-col justify-end p-7 text-white shadow-md hover:shadow-xl transition border border-gray-100 dark:border-gray-800">
        <div class="absolute inset-0 bg-gradient-to-t from-gray-950/95 via-gray-900/60 to-gray-900/20 z-10"></div>
        <img src="https://images.unsplash.com/photo-1519741497674-611481863552?w=600&q=80" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="Wedding" />
        
        <div class="relative z-20 space-y-2.5">
          <span class="px-3 py-1 bg-amber-400 text-gray-900 text-[10px] font-black rounded-md inline-block uppercase tracking-wider">POPULER #1</span>
          <h3 class="text-xl font-extrabold text-white leading-snug">Wedding & Prewedding Sesi</h3>
          <p class="text-xs text-gray-200 leading-relaxed">Abadikan momen romantis Anda bersama fotografer wedding berpengalaman outdoor & indoor.</p>
          <span class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-300 group-hover:translate-x-1 transition pt-1">
            Lihat 120+ Fotografer Available
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
          </span>
        </div>
      </a>

      <!-- Card 2: Holiday & Travel -->
      <a href="{{ url('/fotografer?cat=vacation-travel') }}" class="group relative rounded-2xl overflow-hidden min-h-[300px] flex flex-col justify-end p-7 text-white shadow-md hover:shadow-xl transition border border-gray-100 dark:border-gray-800">
        <div class="absolute inset-0 bg-gradient-to-t from-gray-950/95 via-gray-900/60 to-gray-900/20 z-10"></div>
        <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=600&q=80" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="Holiday" />
        
        <div class="relative z-20 space-y-2.5">
          <h3 class="text-xl font-extrabold text-white leading-snug">Holiday & Travel Shoot</h3>
          <p class="text-xs text-gray-200 leading-relaxed">Abadikan momen berharga saat liburan di lokasi wisata impian Anda.</p>
          <span class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-300 group-hover:translate-x-1 transition pt-1">
            Lihat 95+ Fotografer Available
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
          </span>
        </div>
      </a>

      <!-- Card 3: Family -->
      <a href="{{ url('/fotografer?cat=family-gathering') }}" class="group relative rounded-2xl overflow-hidden min-h-[300px] flex flex-col justify-end p-7 text-white shadow-md hover:shadow-xl transition border border-gray-100 dark:border-gray-800">
        <div class="absolute inset-0 bg-gradient-to-t from-gray-950/95 via-gray-900/60 to-gray-900/20 z-10"></div>
        <img src="https://images.unsplash.com/photo-1511895426328-dc8714191300?w=600&q=80" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="Family" />
        
        <div class="relative z-20 space-y-2.5">
          <h3 class="text-xl font-extrabold text-white leading-snug">Family & Gathering</h3>
          <p class="text-xs text-gray-200 leading-relaxed">Sesi foto hangat keluarga kecil hingga acara reuni keluarga besar.</p>
          <span class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-300 group-hover:translate-x-1 transition pt-1">
            Lihat 80+ Fotografer Available
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
          </span>
        </div>
      </a>

      <!-- Card 4: Birthday -->
      <a href="{{ url('/fotografer?cat=birthday-celebration') }}" class="group relative rounded-2xl overflow-hidden min-h-[300px] flex flex-col justify-end p-7 text-white shadow-md hover:shadow-xl transition border border-gray-100 dark:border-gray-800">
        <div class="absolute inset-0 bg-gradient-to-t from-gray-950/95 via-gray-900/60 to-gray-900/20 z-10"></div>
        <img src="https://images.unsplash.com/photo-1530103862676-de8c9debad1d?w=600&q=80" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="Birthday" />
        
        <div class="relative z-20 space-y-2.5">
          <h3 class="text-xl font-extrabold text-white leading-snug">Birthday & Party</h3>
          <p class="text-xs text-gray-200 leading-relaxed">Setiap momen pesta ulang tahun patut dirayakan dan diabadikan.</p>
          <span class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-300 group-hover:translate-x-1 transition pt-1">
            Lihat 65+ Fotografer Available
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
          </span>
        </div>
      </a>

      <!-- Card 5: Graduation -->
      <a href="{{ url('/fotografer?cat=graduation-wisuda') }}" class="group relative rounded-2xl overflow-hidden min-h-[300px] flex flex-col justify-end p-7 text-white shadow-md hover:shadow-xl transition border border-gray-100 dark:border-gray-800">
        <div class="absolute inset-0 bg-gradient-to-t from-gray-950/95 via-gray-900/60 to-gray-900/20 z-10"></div>
        <img src="https://images.unsplash.com/photo-1541339907198-e08756dedf3f?w=600&q=80" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="Graduation" />
        
        <div class="relative z-20 space-y-2.5">
          <h3 class="text-xl font-extrabold text-white leading-snug">Graduation & Wisuda</h3>
          <p class="text-xs text-gray-200 leading-relaxed">Rayakan kelulusan wisuda dan pencapaian akademik terbaik Anda.</p>
          <span class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-300 group-hover:translate-x-1 transition pt-1">
            Lihat 75+ Fotografer Available
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
          </span>
        </div>
      </a>

      <!-- Card 6: Baby & Maternity -->
      <a href="{{ url('/fotografer?cat=maternity-newborn') }}" class="group relative rounded-2xl overflow-hidden min-h-[300px] flex flex-col justify-end p-7 text-white shadow-md hover:shadow-xl transition border border-gray-100 dark:border-gray-800">
        <div class="absolute inset-0 bg-gradient-to-t from-gray-950/95 via-gray-900/60 to-gray-900/20 z-10"></div>
        <img src="https://images.unsplash.com/photo-1555252333-9f8e92e65df9?w=600&q=80" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="Baby" />
        
        <div class="relative z-20 space-y-2.5">
          <h3 class="text-xl font-extrabold text-white leading-snug">Baby & Maternity</h3>
          <p class="text-xs text-gray-200 leading-relaxed">Dokumentasi manis kehamilan hingga foto bayi baru lahir.</p>
          <span class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-300 group-hover:translate-x-1 transition pt-1">
            Lihat 50+ Fotografer Available
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
          </span>
        </div>
      </a>

      <!-- Card 7: Event & Concert -->
      <a href="{{ url('/fotografer?cat=event-concert') }}" class="group relative rounded-2xl overflow-hidden min-h-[300px] flex flex-col justify-end p-7 text-white shadow-md hover:shadow-xl transition border border-gray-100 dark:border-gray-800">
        <div class="absolute inset-0 bg-gradient-to-t from-gray-950/95 via-gray-900/60 to-gray-900/20 z-10"></div>
        <img src="https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?w=600&q=80" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="Event" />
        
        <div class="relative z-20 space-y-2.5">
          <h3 class="text-xl font-extrabold text-white leading-snug">Event & Concert</h3>
          <p class="text-xs text-gray-200 leading-relaxed">Dokumentasi konser musik, pameran, dan pesta perusahaan.</p>
          <span class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-300 group-hover:translate-x-1 transition pt-1">
            Lihat 85+ Fotografer Available
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
          </span>
        </div>
      </a>

      <!-- Card 8: Browse All Categories (CTA Card) -->
      <a href="{{ url('/fotografer') }}" class="group bg-[#222222] text-white p-7 rounded-2xl border border-gray-800 hover:bg-amber-400 hover:text-gray-900 transition flex flex-col items-center justify-center text-center gap-5 min-h-[300px] shadow-md">
        <h3 class="text-xl font-black transition">Lihat Semua Kategori</h3>
        <div class="w-14 h-14 rounded-full bg-amber-400 text-gray-900 group-hover:bg-gray-900 group-hover:text-amber-400 flex items-center justify-center transition duration-300 shadow-md group-hover:scale-110">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="5" y1="12" x2="19" y2="12"></line>
            <polyline points="12 5 19 12 12 19"></polyline>
          </svg>
        </div>
      </a>

    </div>
  </section>

  <!-- 4. CARA KERJA BOOKING (Full-Width Soft Slate Gray Section Background - Swapped Alignment with Title on Right) -->
  <section class="w-full bg-gray-50/90 dark:bg-gray-800/60 py-28 sm:py-36 border-y border-gray-100 dark:border-gray-800 reveal-on-scroll">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 sm:gap-16 items-center">
        
        <!-- Left Column: 01, 02, 03 Steps -->
        <div class="lg:col-span-7 space-y-5">
          
          <div class="bg-white dark:bg-gray-900 p-7 sm:p-8 rounded-2xl border border-gray-200/80 dark:border-gray-700 shadow-2xs flex gap-6 items-start">
            <span class="text-2xl font-black text-amber-500 shrink-0">01</span>
            <div class="space-y-1.5">
              <h3 class="font-bold text-base text-gray-900 dark:text-white">Pilih Fotografer & Paket Layanan</h3>
              <p class="text-xs sm:text-sm text-gray-500 leading-relaxed">Filter berdasarkan kota lokasi foto, lihat portofolio galeri karya, dan pilih estimasi tarif paket jam fotonya.</p>
            </div>
          </div>

          <div class="bg-white dark:bg-gray-900 p-7 sm:p-8 rounded-2xl border border-gray-200/80 dark:border-gray-700 shadow-2xs flex gap-6 items-start">
            <span class="text-2xl font-black text-amber-500 shrink-0">02</span>
            <div class="space-y-1.5">
              <h3 class="font-bold text-base text-gray-900 dark:text-white">Validasi Kontrak & DP Escrow</h3>
              <p class="text-xs sm:text-sm text-gray-500 leading-relaxed">Fotografer menyetujui jadwal. Sepakati ketentuan kontrak digital dan lakukan bayar DP aman yang ditahan sistem.</p>
            </div>
          </div>

          <div class="bg-white dark:bg-gray-900 p-7 sm:p-8 rounded-2xl border border-gray-200/80 dark:border-gray-700 shadow-2xs flex gap-6 items-start">
            <span class="text-2xl font-black text-amber-500 shrink-0">03</span>
            <div class="space-y-1.5">
              <h3 class="font-bold text-base text-gray-900 dark:text-white">Pelaksanaan Sesi & Unduh Galeri Digital</h3>
              <p class="text-xs sm:text-sm text-gray-500 leading-relaxed">Lakukan pemotretan santai di lokasi. Unduh hasil foto ter-edit kualitas studio langsung dari portal akun Anda.</p>
            </div>
          </div>

        </div>

        <!-- Right Column: Title, Description & CTA Button -->
        <div class="lg:col-span-5 space-y-5">
          <span class="text-[11px] font-extrabold text-amber-600 dark:text-amber-400 uppercase tracking-widest">Alur Transaksi Transparan</span>
          <h2 class="text-3xl sm:text-4xl font-black text-gray-900 dark:text-white leading-[1.15] tracking-tight">
            Cara Kerja Praktis & Aman di LensMatch
          </h2>
          <p class="text-sm sm:text-base text-gray-600 dark:text-gray-300 leading-relaxed max-w-xl">
            Kami memastikan setiap pesanan terlindungi dengan **Kontrak Digital resmi** dan sistem **Pembayaran DP Escrow**. Uang Anda aman sampai hasil foto dikirimkan.
          </p>
          <div class="pt-2">
            <a href="{{ url('/fotografer') }}" class="inline-flex items-center gap-2.5 px-7 py-3.5 bg-[#222222] text-white hover:bg-amber-400 hover:text-gray-900 font-bold text-xs rounded-xl transition shadow-xs">
              Mulai Cari Fotografer
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
            </a>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- 5. FOTOGRAFER PILIHAN BULAN INI (Clean 4-Equal-Cards Grid Layout with Crisp HD Unsplash Photos & Spacious Padding) -->
  <section class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10 reveal-on-scroll">
    
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
      <div class="space-y-2">
        <span class="text-[11px] font-extrabold text-amber-600 dark:text-amber-400 uppercase tracking-widest">Rekomendasi Komunitas</span>
        <h2 class="text-3xl sm:text-4xl font-black text-gray-900 dark:text-white leading-[1.15] tracking-tight">Fotografer Pilihan Bulan Ini</h2>
      </div>
      <a href="{{ url('/fotografer') }}" class="inline-flex items-center gap-1.5 text-amber-600 dark:text-amber-400 font-bold text-xs hover:underline">
        Lihat Katalog Lengkap (150+)
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
      </a>
    </div>

    <!-- 4 Equal Photographer Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-7">
      
      <!-- Photographer Card 1 (Alex Visual Studio - HD Unsplash Portrait) -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden shadow-xs hover:shadow-md transition flex flex-col justify-between group">
        <div>
          <div class="h-56 bg-gray-100 relative overflow-hidden">
            <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=600&q=80" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="Alex Visual Studio" />
            <span class="absolute top-3.5 right-3.5 px-2.5 py-1 bg-white/95 dark:bg-gray-900/95 text-gray-900 dark:text-white text-[11px] font-bold rounded-lg shadow-2xs flex items-center gap-1">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" class="text-amber-500"><path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/></svg>
              4.9 (42)
            </span>
          </div>
          <div class="p-7 space-y-3">
            <h3 class="font-bold text-lg text-gray-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 transition">Alex Visual Studio</h3>
            <p class="text-xs text-gray-500 leading-relaxed">Ahli fotografi momen outdoor wedding, prewedding romantis, serta foto pasangan liburan di Jakarta & Bali.</p>
            <div class="pt-2">
              <span class="text-[10px] font-bold text-gray-400 uppercase block">Mulai Dari</span>
              <p class="text-amber-600 dark:text-amber-400 font-extrabold text-base">Rp 3.500.000</p>
            </div>
          </div>
        </div>
        <div class="p-7 pt-0">
          <a href="{{ url('/fotografer/1') }}" class="inline-flex items-center justify-center gap-1.5 w-full text-center py-2.5 bg-gray-900 text-white hover:bg-amber-400 hover:text-gray-900 rounded-xl text-xs font-bold transition">
            Lihat Profil
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
          </a>
        </div>
      </div>

      <!-- Photographer Card 2 (Budi Event Captures - HD Unsplash Portrait) -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden shadow-xs hover:shadow-md transition flex flex-col justify-between group">
        <div>
          <div class="h-56 bg-gray-100 relative overflow-hidden">
            <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=600&q=80" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="Budi Event Captures" />
            <span class="absolute top-3.5 right-3.5 px-2.5 py-1 bg-white/95 dark:bg-gray-900/95 text-gray-900 dark:text-white text-[11px] font-bold rounded-lg shadow-2xs flex items-center gap-1">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" class="text-amber-500"><path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/></svg>
              4.8 (38)
            </span>
          </div>
          <div class="p-7 space-y-3">
            <h3 class="font-bold text-lg text-gray-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 transition">Budi Event Captures</h3>
            <p class="text-xs text-gray-500 leading-relaxed">Dokumentasi konser musik panggung, festival budaya, dan dokumentasi acara eksklusif perusahaan di Surabaya.</p>
            <div class="pt-2">
              <span class="text-[10px] font-bold text-gray-400 uppercase block">Mulai Dari</span>
              <p class="text-amber-600 dark:text-amber-400 font-extrabold text-base">Rp 2.800.000</p>
            </div>
          </div>
        </div>
        <div class="p-7 pt-0">
          <a href="{{ url('/fotografer/1') }}" class="inline-flex items-center justify-center gap-1.5 w-full text-center py-2.5 bg-gray-900 text-white hover:bg-amber-400 hover:text-gray-900 rounded-xl text-xs font-bold transition">
            Lihat Profil
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
          </a>
        </div>
      </div>

      <!-- Photographer Card 3 (Clara Commercial Lens - HD Unsplash Portrait) -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden shadow-xs hover:shadow-md transition flex flex-col justify-between group">
        <div>
          <div class="h-56 bg-gray-100 relative overflow-hidden">
            <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=600&q=80" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="Clara Commercial Lens" />
            <span class="absolute top-3.5 right-3.5 px-2.5 py-1 bg-white/95 dark:bg-gray-900/95 text-gray-900 dark:text-white text-[11px] font-bold rounded-lg shadow-2xs flex items-center gap-1">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" class="text-amber-500"><path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/></svg>
              5.0 (29)
            </span>
          </div>
          <div class="p-7 space-y-3">
            <h3 class="font-bold text-lg text-gray-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 transition">Clara Commercial Lens</h3>
            <p class="text-xs text-gray-500 leading-relaxed">Dokumentasi katalog produk UMKM lokal, komersial brand fashion, dan foto komersial studio di Bandung.</p>
            <div class="pt-2">
              <span class="text-[10px] font-bold text-gray-400 uppercase block">Mulai Dari</span>
              <p class="text-amber-600 dark:text-amber-400 font-extrabold text-base">Rp 1.500.000</p>
            </div>
          </div>
        </div>
        <div class="p-7 pt-0">
          <a href="{{ url('/fotografer/1') }}" class="inline-flex items-center justify-center gap-1.5 w-full text-center py-2.5 bg-gray-900 text-white hover:bg-amber-400 hover:text-gray-900 rounded-xl text-xs font-bold transition">
            Lihat Profil
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
          </a>
        </div>
      </div>

      <!-- Photographer Card 4 (Dewa Portrait Studio - HD Unsplash Portrait) -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden shadow-xs hover:shadow-md transition flex flex-col justify-between group">
        <div>
          <div class="h-56 bg-gray-100 relative overflow-hidden">
            <img src="https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=600&q=80" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="Dewa Portrait Studio" />
            <span class="absolute top-3.5 right-3.5 px-2.5 py-1 bg-white/95 dark:bg-gray-900/95 text-gray-900 dark:text-white text-[11px] font-bold rounded-lg shadow-2xs flex items-center gap-1">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" class="text-amber-500"><path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/></svg>
              4.9 (51)
            </span>
          </div>
          <div class="p-7 space-y-3">
            <h3 class="font-bold text-lg text-gray-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 transition">Dewa Portrait Studio</h3>
            <p class="text-xs text-gray-500 leading-relaxed">Spesialis portrait wisuda, foto liburan keluarga, serta dokumentasi lanskap estetik di Yogyakarta & Bali.</p>
            <div class="pt-2">
              <span class="text-[10px] font-bold text-gray-400 uppercase block">Mulai Dari</span>
              <p class="text-amber-600 dark:text-amber-400 font-extrabold text-base">Rp 2.200.000</p>
            </div>
          </div>
        </div>
        <div class="p-7 pt-0">
          <a href="{{ url('/fotografer/1') }}" class="inline-flex items-center justify-center gap-1.5 w-full text-center py-2.5 bg-gray-900 text-white hover:bg-amber-400 hover:text-gray-900 rounded-xl text-xs font-bold transition">
            Lihat Profil
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
          </a>
        </div>
      </div>

    </div>
  </section>

  <!-- 6. TESTIMONI PELANGGAN (Strictly Non-Looping 5-Item Bounded Slider with Center Highlighted Card & Disabled Arrow State) -->
  <section class="w-full bg-amber-50/70 dark:bg-gray-800/80 py-24 sm:py-32 border-y border-amber-100/80 dark:border-gray-800 overflow-hidden reveal-on-scroll" 
    x-data="{ 
      activeIndex: 1, 
      items: [
        { initial: 'DF', name: 'Dian & Febri', category: 'Sesi Prewedding — DKI Jakarta', quote: 'Cari fotografer prewedding di Jakarta jadi cepat banget. Portofolio langsung kelihatan jelas beserta pilihan paket jam fotonya.' },
        { initial: 'SR', name: 'Siti Rahmawati', category: 'Sesi Foto Liburan — Bali', quote: 'Sistem kontrak digitalnya bikin tenang banget. Hak cipta foto dan tanggal sesi foto tercatat resmi. Pembayaran DP aman via sistem escrow.' },
        { initial: 'RP', name: 'Reza Pratama', category: 'Sesi Wisuda — DI Yogyakarta', quote: 'Hasil foto wisuda saya sangat memuaskan! Fotografer ramah, datang tepat waktu, dan proses serah terima galeri foto sangat cepat.' },
        { initial: 'MK', name: 'Maya & Kevin', category: 'Ulang Tahun & Family — Bandung', quote: 'Sangat puas dengan dokumentasi foto ulang tahun anak kami di Bandung. Fotografernya pandai mengambil momen candid yang natural!' },
        { initial: 'RH', name: 'Rudi Hermawan', category: 'Foto Produk UMKM — Surabaya', quote: 'Fotografer komersial untuk produk UMKM kuliner saya sangat profesional. Hasil foto lighting studio bikin penjualan produk naik drastis.' }
      ] 
    }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
      
      <!-- Centered Header -->
      <div class="text-center space-y-3 max-w-2xl mx-auto">
        <span class="text-[11px] font-extrabold text-amber-600 dark:text-amber-400 uppercase tracking-widest">Testimonials</span>
        <h2 class="text-3xl sm:text-4xl lg:text-[40px] font-black text-gray-900 dark:text-white leading-tight">Cerita Pengalaman Bersama LensMatch</h2>
      </div>

      <!-- Slider Container with Bounded Left/Right Arrow Buttons & 3 Visible Cards -->
      <div class="relative flex items-center justify-center">
        
        <!-- Left Arrow Button (Disabled when at First Item) -->
        <button @click="if (activeIndex > 0) activeIndex--" 
                :disabled="activeIndex === 0" 
                :class="activeIndex === 0 ? 'opacity-30 cursor-not-allowed bg-gray-100 dark:bg-gray-800 text-gray-400 border-gray-200 dark:border-gray-700' : 'bg-white dark:bg-gray-900 text-gray-800 dark:text-white shadow-lg border border-gray-200 dark:border-gray-700 hover:bg-amber-400 hover:text-gray-900 dark:hover:bg-amber-400 dark:hover:text-gray-900 cursor-pointer'" 
                class="absolute left-0 sm:left-2 lg:left-6 z-30 w-11 h-11 rounded-full flex items-center justify-center transition duration-300 focus:outline-none">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="15 18 9 12 15 6"></polyline>
          </svg>
        </button>

        <!-- 3 Visible Cards Row (Left Card, Center Active Card, Right Card) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8 items-center max-w-6xl mx-auto px-10 sm:px-12 w-full py-4">
          
          <!-- Left Visible Card -->
          <div :class="activeIndex > 0 ? 'opacity-70 scale-95 shadow-sm border border-gray-100 dark:border-gray-800 bg-white/90 dark:bg-gray-900/90' : 'opacity-0 pointer-events-none scale-90 bg-transparent border-0'" class="z-10 p-8 rounded-2xl transition-all duration-500 flex flex-col justify-between space-y-6 min-h-[270px]">
            <template x-if="activeIndex > 0">
              <div class="flex flex-col justify-between h-full space-y-6">
                <div class="space-y-4">
                  <div class="text-amber-500 font-serif text-4xl leading-none">“</div>
                  <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300 leading-relaxed italic" x-text="items[activeIndex - 1].quote"></p>
                </div>
                <div class="pt-4 border-t border-gray-100 dark:border-gray-800 flex items-center gap-3">
                  <div class="w-10 h-10 rounded-full bg-amber-200 text-gray-900 font-bold text-xs flex items-center justify-center shrink-0" x-text="items[activeIndex - 1].initial"></div>
                  <div>
                    <h4 class="font-bold text-xs text-gray-900 dark:text-white" x-text="items[activeIndex - 1].name"></h4>
                    <p class="text-[10px] text-gray-400" x-text="items[activeIndex - 1].category"></p>
                  </div>
                </div>
              </div>
            </template>
          </div>

          <!-- Center Active Elevated Card (Menonjol with Emas Border & Shadow) -->
          <div class="scale-105 shadow-2xl border-2 border-amber-400 dark:border-amber-400 z-20 opacity-100 bg-white dark:bg-gray-900 p-8 sm:p-9 rounded-2xl transition-all duration-500 flex flex-col justify-between space-y-6 min-h-[290px]">
            <div class="space-y-4">
              <div class="text-amber-500 font-serif text-4xl leading-none">“</div>
              <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300 leading-relaxed italic" x-text="items[activeIndex].quote"></p>
            </div>
            <div class="pt-4 border-t border-gray-100 dark:border-gray-800 flex items-center gap-3">
              <div class="w-10 h-10 rounded-full bg-amber-200 text-gray-900 font-bold text-xs flex items-center justify-center shrink-0" x-text="items[activeIndex].initial"></div>
              <div>
                <h4 class="font-bold text-xs text-gray-900 dark:text-white" x-text="items[activeIndex].name"></h4>
                <p class="text-[10px] text-gray-400" x-text="items[activeIndex].category"></p>
              </div>
            </div>
          </div>

          <!-- Right Visible Card -->
          <div :class="activeIndex < items.length - 1 ? 'opacity-70 scale-95 shadow-sm border border-gray-100 dark:border-gray-800 bg-white/90 dark:bg-gray-900/90' : 'opacity-0 pointer-events-none scale-90 bg-transparent border-0'" class="z-10 p-8 rounded-2xl transition-all duration-500 flex flex-col justify-between space-y-6 min-h-[270px]">
            <template x-if="activeIndex < items.length - 1">
              <div class="flex flex-col justify-between h-full space-y-6">
                <div class="space-y-4">
                  <div class="text-amber-500 font-serif text-4xl leading-none">“</div>
                  <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300 leading-relaxed italic" x-text="items[activeIndex + 1].quote"></p>
                </div>
                <div class="pt-4 border-t border-gray-100 dark:border-gray-800 flex items-center gap-3">
                  <div class="w-10 h-10 rounded-full bg-amber-200 text-gray-900 font-bold text-xs flex items-center justify-center shrink-0" x-text="items[activeIndex + 1].initial"></div>
                  <div>
                    <h4 class="font-bold text-xs text-gray-900 dark:text-white" x-text="items[activeIndex + 1].name"></h4>
                    <p class="text-[10px] text-gray-400" x-text="items[activeIndex + 1].category"></p>
                  </div>
                </div>
              </div>
            </template>
          </div>

        </div>

        <!-- Right Arrow Button (Disabled when at Last Item) -->
        <button @click="if (activeIndex < items.length - 1) activeIndex++" 
                :disabled="activeIndex === items.length - 1" 
                :class="activeIndex === items.length - 1 ? 'opacity-30 cursor-not-allowed bg-gray-100 dark:bg-gray-800 text-gray-400 border-gray-200 dark:border-gray-700' : 'bg-white dark:bg-gray-900 text-gray-800 dark:text-white shadow-lg border border-gray-200 dark:border-gray-700 hover:bg-amber-400 hover:text-gray-900 dark:hover:bg-amber-400 dark:hover:text-gray-900 cursor-pointer'" 
                class="absolute right-0 sm:right-2 lg:right-6 z-30 w-11 h-11 rounded-full flex items-center justify-center transition duration-300 focus:outline-none">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="9 18 15 12 9 6"></polyline>
          </svg>
        </button>

      </div>

      <!-- 5 Pagination Indicator Dots -->
      <div class="flex items-center justify-center gap-2 pt-2">
        <template x-for="(item, index) in items" :key="index">
          <button @click="activeIndex = index" :class="activeIndex === index ? 'w-8 bg-amber-500' : 'w-2.5 bg-gray-300 dark:bg-gray-700 hover:bg-gray-400'" class="h-2.5 rounded-full transition-all duration-300 focus:outline-none"></button>
        </template>
      </div>

    </div>
  </section>

  <!-- 7. CTA GABUNG MITRA FOTOGRAFER (Dark Charcoal Banner on Pure White Canvas) -->
  <section class="w-full bg-white dark:bg-gray-900 py-16 sm:py-20 reveal-on-scroll">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="bg-[#222222] text-white rounded-3xl p-10 sm:p-14 flex flex-col md:flex-row items-center justify-between gap-8 shadow-xl">
        <div class="space-y-3 text-center md:text-left">
          <span class="text-[11px] font-black text-amber-400 uppercase tracking-widest">Kemitraan Fotografer</span>
          <h2 class="text-3xl sm:text-4xl font-black tracking-tight leading-tight">Apakah Anda Fotografer Profesional?</h2>
          <p class="text-xs sm:text-sm text-gray-300 leading-relaxed max-w-xl">
            Bergabunglah bersama 150+ fotografer di Indonesia. Kelola jadwal ketersediaan, buat paket layanan, dan terima pesanan booking dengan jaminan sistem escrow.
          </p>
        </div>
        <a href="{{ url('/register') }}" class="inline-flex items-center gap-2.5 px-8 py-4 bg-amber-400 hover:bg-amber-500 text-gray-900 font-bold text-sm rounded-xl transition shadow-lg shrink-0">
          Daftar Sebagai Fotografer
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="5" y1="12" x2="19" y2="12"></line>
            <polyline points="12 5 19 12 12 19"></polyline>
          </svg>
        </a>
      </div>
    </div>
  </section>

</main>
@endsection
