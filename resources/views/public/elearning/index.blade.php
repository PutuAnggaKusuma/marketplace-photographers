@extends('layouts.app')

@section('title', 'Akademi Edukasi Fotografi — LensMatch')

@section('content')
<div x-data="elearningOverviewApp()" id="elearning-main-section" class="bg-white dark:bg-gray-900 min-h-screen">

  <!-- 1. GRATTER-STYLE CENTERED HERO SECTION WITH WARM AMBER GRADIENT BACKGROUND -->
  <section class="w-full relative bg-gradient-to-b from-amber-50/70 via-amber-50/20 to-white dark:from-gray-800/80 dark:via-gray-800/30 dark:to-gray-900 pt-10 sm:pt-14 pb-14 sm:pb-20 px-4 sm:px-6 lg:px-8 overflow-hidden reveal-on-scroll">
    <div class="max-w-6xl mx-auto space-y-10 text-center">
      
      <!-- Centered Header Typography -->
      <div class="space-y-4 max-w-3xl mx-auto">
        <div class="flex justify-center">
          <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-[11px] font-extrabold bg-amber-100 text-amber-900 dark:bg-amber-900/50 dark:text-amber-300 border border-amber-200/80 dark:border-amber-800/60 uppercase tracking-widest shadow-2xs">
            AKADEMI EDUKASI FOTOGRAFI
          </span>
        </div>
        
        <h1 class="text-3xl sm:text-5xl lg:text-[52px] font-black text-gray-900 dark:text-white tracking-tight leading-[1.12]">
          Tingkatkan Skill Fotografi — Raih <span class="text-amber-500 dark:text-amber-400">Potensi Terbaikmu</span>
        </h1>
        <p class="text-sm sm:text-base text-gray-500 dark:text-gray-400 max-w-2xl mx-auto leading-relaxed pt-1">
          Panduan edukasi komprehensif dari fotografer profesional terkemuka untuk membantu kamu menguasai teknik kamera, retouching warna Lightroom, dan manajemen bisnis studio.
        </p>
      </div>

      <!-- Action Button Row -->
      <div class="flex items-center justify-center pt-1">
        <a href="{{ route('public.elearning.katalog') }}" class="px-8 py-4 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs sm:text-sm rounded-2xl shadow-md hover:shadow-xl transition duration-300 transform hover:-translate-y-0.5 inline-flex items-center gap-2">
          <span>Mulai Belajar Gratis</span>
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"></path></svg>
        </a>
      </div>

      <!-- Interactive Hero Showcase with YouTube / Video Player UI Overlay & Full-Bleed Floating Cards -->
      <div class="relative max-w-5xl mx-auto pt-6 pb-4">
        
        <!-- Main Center Featured Image Frame with Local Asset Image & YouTube Player Overlay -->
        <div class="w-full max-w-2xl mx-auto rounded-3xl overflow-hidden shadow-2xl bg-gray-900 aspect-[16/10] relative group cursor-pointer">
          
          <!-- Image from local asset path images/e-learning/main-foto-elearning.jpg -->
          <img src="{{ asset('images/e-learning/main-foto-elearning.jpg') }}" 
               alt="Modul Utama E-Learning Fotografi" 
               class="w-full h-full object-cover object-center group-hover:scale-105 transition duration-700" />
          
          <!-- Gradient Backdrop Overlay -->
          <div class="absolute inset-0 bg-gradient-to-t from-gray-950/80 via-gray-950/20 to-transparent"></div>

          <!-- YouTube / Video Player Bottom Control Bar Overlay -->
          <div class="absolute bottom-0 left-0 right-0 p-4 sm:p-5 bg-gradient-to-t from-gray-950/95 via-gray-950/70 to-transparent space-y-2.5">
            
            <!-- Video Progress Bar -->
            <div class="w-full bg-white/20 h-1.5 rounded-full overflow-hidden relative cursor-pointer group/bar">
              <div class="bg-amber-400 h-full w-2/5 rounded-full relative">
                <div class="absolute right-0 top-1/2 -translate-y-1/2 w-3 h-3 bg-white rounded-full shadow-md scale-0 group-hover/bar:scale-100 transition"></div>
              </div>
            </div>

            <!-- Player Controls Row -->
            <div class="flex items-center justify-between text-white text-xs font-bold">
              
              <!-- Left Controls: Active Playing Pause Icon, Volume, Timestamp -->
              <div class="flex items-center gap-3">
                <button type="button" class="text-white hover:text-amber-400 transition" title="Pause Video">
                  <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>
                </button>
                <button type="button" class="text-white hover:text-amber-400 transition">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path></svg>
                </button>
                <span class="text-[11px] text-gray-300 font-medium">14:25 / 32:00</span>
              </div>

              <!-- Right Controls: Quality Badge, Settings, Fullscreen -->
              <div class="flex items-center gap-3 text-[11px]">
                <span class="px-1.5 py-0.5 rounded border border-white/30 text-amber-400 font-extrabold text-[9px]">1080p HD</span>
                <button type="button" class="text-gray-300 hover:text-white transition">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4m0 0h4M4 4l5 5m11-2V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                </button>
              </div>

            </div>

          </div>

        </div>

        <!-- Floating Card 1 (Top-Left Mini Course Card - FULL BLEED THUMBNAIL) -->
        <div class="hidden lg:block absolute -left-4 xl:-left-8 top-6 w-52 bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700/80 overflow-hidden z-20 transform -rotate-6 animate-float-1">
          <div class="aspect-[16/10] bg-gray-100 overflow-hidden">
            <img src="https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=400&q=80" class="w-full h-full object-cover" alt="Lighting Course" />
          </div>
          <div class="p-3.5 space-y-1 text-left">
            <span class="px-2 py-0.5 bg-amber-400 text-gray-900 font-black text-[9px] rounded-md uppercase">PEMULA</span>
            <h4 class="font-bold text-xs text-gray-900 dark:text-white truncate">Teknik Lighting Portrait</h4>
          </div>
        </div>

        <!-- Floating Card 2 (Bottom-Left Key Statistics Card) -->
        <div class="hidden lg:block absolute -left-8 xl:-left-12 bottom-4 w-56 bg-white/95 dark:bg-gray-800/95 backdrop-blur-md p-4 rounded-2xl shadow-2xl border border-amber-300/40 space-y-3 z-20 text-left transform -rotate-3 animate-float-2">
          <div>
            <span class="text-[10px] font-black uppercase tracking-wider text-amber-600 dark:text-amber-400">KEY STATISTICS</span>
          </div>
          <div class="space-y-2">
            <div class="bg-amber-50 dark:bg-amber-950/60 p-2.5 rounded-xl border border-amber-200/50 flex items-center justify-between">
              <span class="text-xs font-black text-amber-900 dark:text-amber-300">{{ $totalCategories }}+ Kategori Utama</span>
              <span class="text-[10px] text-amber-600 dark:text-amber-400 font-bold">100% Gratis</span>
            </div>
            <div class="bg-emerald-50 dark:bg-emerald-950/60 p-2.5 rounded-xl border border-emerald-200/50 flex items-center justify-between">
              <span class="text-xs font-black text-emerald-900 dark:text-emerald-300">Real-time Progress</span>
              <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold">Tersimpan</span>
            </div>
          </div>
        </div>

        <!-- Floating Card 3 (Top-Right Mini Course Card - FULL BLEED THUMBNAIL) -->
        <div class="hidden lg:block absolute -right-4 xl:-right-8 top-4 w-52 bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700/80 overflow-hidden z-20 transform rotate-6 animate-float-3">
          <div class="aspect-[16/10] bg-gray-100 overflow-hidden">
            <img src="https://images.unsplash.com/photo-1452587925148-ce544e77e70d?auto=format&fit=crop&w=400&q=80" class="w-full h-full object-cover" alt="Color Grading Course" />
          </div>
          <div class="p-3.5 space-y-1 text-left">
            <span class="px-2 py-0.5 bg-amber-400 text-gray-900 font-black text-[9px] rounded-md uppercase">MENENGAH</span>
            <h4 class="font-bold text-xs text-gray-900 dark:text-white truncate">Mastering Color Grading</h4>
          </div>
        </div>

        <!-- Floating Card 4 (Bottom-Right Mini Course Card - FULL BLEED THUMBNAIL) -->
        <div class="hidden lg:block absolute -right-8 xl:-right-12 bottom-6 w-56 bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700/80 overflow-hidden z-20 transform -rotate-3 animate-float-1">
          <div class="aspect-[16/10] bg-gray-100 overflow-hidden">
            <img src="https://images.unsplash.com/photo-1556761175-5973dc0f32e7?auto=format&fit=crop&w=400&q=80" class="w-full h-full object-cover" alt="Business Studio Course" />
          </div>
          <div class="p-3.5 space-y-1 text-left">
            <span class="px-2 py-0.5 bg-amber-400 text-gray-900 font-black text-[9px] rounded-md uppercase">MAHIR</span>
            <h4 class="font-bold text-xs text-gray-900 dark:text-white truncate">Bisnis Studio & Escrow</h4>
          </div>
        </div>

      </div>

    </div>
  </section>

  <!-- BODY CONTENT WRAPPER -->
  <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

    <!-- 2. STATS BAR SECTION (Beranda-style, above Key Benefits) -->
    <section class="w-full reveal-on-scroll">
      <div class="py-14 sm:py-20 grid grid-cols-2 lg:grid-cols-3 gap-8 text-center items-center">
        
        <div class="space-y-1">
          <p class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white tracking-tight">{{ $totalCategories }} Kategori</p>
          <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Lighting, editing, bisnis studio</p>
        </div>

        <div class="space-y-1 lg:border-l lg:border-gray-200/80 lg:dark:border-gray-800 lg:pl-6">
          <p class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white tracking-tight">{{ number_format($totalViews, 0, ',', '.') }} Pembaca</p>
          <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total pembaca seluruh modul</p>
        </div>

        <div class="space-y-1 lg:border-l lg:border-gray-200/80 lg:dark:border-gray-800 lg:pl-6">
          <p class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white tracking-tight">{{ $totalCourses }} Modul</p>
          <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Level pemula sampai mahir</p>
        </div>

      </div>
    </section>

    <!-- 3. KEY BENEFITS SECTION -->
    <section class="w-full py-16 sm:py-20 reveal-on-scroll">
      <div class="space-y-10">
        
        <!-- Section Header Row (Header Left, Subtext Right) -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
          <div class="space-y-2 max-w-xl">
            <span class="text-[11px] font-extrabold uppercase tracking-widest text-amber-600 dark:text-amber-400">
              KEUNGGULAN UTAMA
            </span>
            <h2 class="text-3xl sm:text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
              Key <span class="text-amber-500 dark:text-amber-400">Benefits</span>
            </h2>
          </div>

          <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 max-w-md leading-relaxed">
            Di Akademi LensMatch, kami tidak hanya memberikan materi — kami memberikan ekosistem belajar komprehensif yang dirancang untuk melejitkan karir dan bisnis studio fotografi Anda.
          </p>
        </div>

        <!-- 4 Large Benefit Cards Grid (2x2 Layout) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8">
          
          <!-- Benefit Card 1 -->
          <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl border border-gray-200/80 dark:border-gray-700/80 shadow-xs hover:shadow-lg transition-all duration-300 space-y-4 group">
            <div class="w-12 h-12 rounded-2xl bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-200/60 dark:border-amber-800/60 flex items-center justify-center font-bold shadow-2xs group-hover:scale-105 group-hover:bg-amber-400 group-hover:text-gray-900 transition-all duration-300">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"></path></svg>
            </div>
            <div class="space-y-2">
              <h3 class="font-extrabold text-lg sm:text-xl text-gray-900 dark:text-white group-hover:text-amber-500 transition">
                Instruktur Fotografer Aktif
              </h3>
              <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 leading-relaxed">
                Materi disusun langsung oleh fotografer profesional terverifikasi yang aktif mengelola studio dan melayani klien nyata di platform marketplace LensMatch.
              </p>
            </div>
          </div>

          <!-- Benefit Card 2 -->
          <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl border border-gray-200/80 dark:border-gray-700/80 shadow-xs hover:shadow-lg transition-all duration-300 space-y-4 group">
            <div class="w-12 h-12 rounded-2xl bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-200/60 dark:border-amber-800/60 flex items-center justify-center font-bold shadow-2xs group-hover:scale-105 group-hover:bg-amber-400 group-hover:text-gray-900 transition-all duration-300">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div class="space-y-2">
              <h3 class="font-extrabold text-lg sm:text-xl text-gray-900 dark:text-white group-hover:text-amber-500 transition">
                Progres Belajar Fleksibel
              </h3>
              <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 leading-relaxed">
                Belajar kapan saja dan di mana saja dari perangkat apa pun. Progres bacaan modul Anda tersimpan secara otomatis dan real-time di akun Anda.
              </p>
            </div>
          </div>

          <!-- Benefit Card 3 -->
          <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl border border-gray-200/80 dark:border-gray-700/80 shadow-xs hover:shadow-lg transition-all duration-300 space-y-4 group">
            <div class="w-12 h-12 rounded-2xl bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-200/60 dark:border-amber-800/60 flex items-center justify-center font-bold shadow-2xs group-hover:scale-105 group-hover:bg-amber-400 group-hover:text-gray-900 transition-all duration-300">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18c-2.305 0-4.408.867-6 2.292m0-14.25v14.25"></path></svg>
            </div>
            <div class="space-y-2">
              <h3 class="font-extrabold text-lg sm:text-xl text-gray-900 dark:text-white group-hover:text-amber-500 transition">
                Format Teks, Foto &amp; Video Utuh
              </h3>
              <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 leading-relaxed">
                Setiap modul menggabungkan penjelasan artikel mendalam, foto komparasi sebelum-sesudah retouching, dan video tutorial penataan pencahayaan studio.
              </p>
            </div>
          </div>

          <!-- Benefit Card 4 -->
          <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl border border-gray-200/80 dark:border-gray-700/80 shadow-xs hover:shadow-lg transition-all duration-300 space-y-4 group">
            <div class="w-12 h-12 rounded-2xl bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-200/60 dark:border-amber-800/60 flex items-center justify-center font-bold shadow-2xs group-hover:scale-105 group-hover:bg-amber-400 group-hover:text-gray-900 transition-all duration-300">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 11.25v8.25a1.5 1.5 0 01-1.5 1.5H4.5a1.5 1.5 0 01-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 109.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1114.625 7.5H12m0 0V21m-9-9h18"></path></svg>
            </div>
            <div class="space-y-2">
              <h3 class="font-extrabold text-lg sm:text-xl text-gray-900 dark:text-white group-hover:text-amber-500 transition">
                100% Gratis Tanpa Biaya
              </h3>
              <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 leading-relaxed">
                Seluruh panduan materi E-Learning dapat diakses secara penuh tanpa biaya langganan tambahan bagi seluruh anggota komunitas fotografer LensMatch.
              </p>
            </div>
          </div>

        </div>
      </div>
    </section>

    <!-- 4. PREVIEW CATALOG SECTION (Most Read Modules Sorted by view_count DESC) -->
    <section class="w-full py-16 sm:py-20 reveal-on-scroll">
    <div class="space-y-8">
      
      <!-- Creative Section Header Row (Non-AI Slop, Elegant Vector Trending Badge) -->
      <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div class="space-y-1.5">
          <div>
            <span class="text-[11px] font-extrabold uppercase tracking-widest text-amber-600 dark:text-amber-400">
              MODUL POPULER & TERFAVORIT
            </span>
          </div>
          <h2 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
            Modul Paling Banyak Dibaca Komunitas
          </h2>
          <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 max-w-2xl leading-relaxed">
            Jelajahi panduan fotografi dan strategi studio yang paling sering dipelajari oleh para fotografer LensMatch.
          </p>
        </div>
      </div>

      <!-- Preview Grid (Top 6 Most Read Modules - FULL BLEED CARDS WITH PERFECT PADDING) -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8">
        @forelse($elearnings as $course)
          <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-200/80 dark:border-gray-700/80 shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between group h-full overflow-hidden">
            
            <!-- Top Full-Bleed Thumbnail Image Frame -->
            @auth
              <a href="{{ route('public.elearning.show', $course->slug) }}" class="block relative aspect-[16/10] bg-gray-100 dark:bg-gray-900 border-b border-gray-100 dark:border-gray-700/60 overflow-hidden shrink-0">
                <img onerror="this.onerror=null;this.src=window.SVG_IMAGE_FALLBACK||'data:image/svg+xml;charset=UTF-8,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'800\' height=\'600\' viewBox=\'0 0 800 600\' fill=\'none\'%3E%3Crect width=\'800\' height=\'600\' fill=\'%23F3F4F6\'/%3E%3Cpath d=\'M360 260C360 248.954 368.954 240 380 240H420C431.046 240 440 248.954 440 260V265H450C466.569 265 480 278.431 480 295V345C480 361.569 466.569 375 450 375H350C333.431 375 320 361.569 320 345V295C320 278.431 333.431 265 350 265H360V260Z\' stroke=\'%239CA3AF\' stroke-width=\'12\' stroke-linecap=\'round\' stroke-linejoin=\'round\'/%3E%3Ccircle cx=\'400\' cy=\'320\' r=\'30\' stroke=\'%239CA3AF\' stroke-width=\'12\'/%3E%3Cline x1=\'310\' y1=\'240\' x2=\'490\' y2=\'390\' stroke=\'%23EF4444\' stroke-width=\'10\' stroke-linecap=\'round\'/%3E%3Ctext x=\'400\' y=\'450\' font-family=\'sans-serif\' font-size=\'22\' font-weight=\'700\' fill=\'%236B7280\' text-anchor=\'middle\'%3EGambar Tidak Dapat Dimuat%3C/text%3E%3C/svg%3E';" 
                     src="{{ $course->thumbnail_url }}" 
                     alt="{{ $course->judul }}" 
                     class="w-full h-full object-cover group-hover:scale-105 transition duration-500" />
                
                <div class="absolute inset-0 bg-gradient-to-t from-gray-950/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition duration-300"></div>

                <!-- Level Badge Top-Left -->
                <span class="absolute top-3 left-3 px-2.5 py-1 bg-amber-400 text-gray-900 text-[10px] font-black rounded-lg shadow-xs uppercase tracking-wider">
                  {{ $course->level ?? 'Pemula' }}
                </span>

                <!-- Duration Badge Top-Right -->
                <span class="absolute top-3 right-3 px-2.5 py-1 bg-gray-900/90 text-white text-[10px] font-extrabold rounded-lg shadow-xs">
                  {{ $course->durasi ?? '30 Menit' }}
                </span>
              </a>
            @else
              <div @click="openGuestLoginModal('{{ route('public.elearning.show', $course->slug) }}', '{{ addslashes($course->judul) }}')" 
                   class="block relative aspect-[16/10] bg-gray-100 dark:bg-gray-900 border-b border-gray-100 dark:border-gray-700/60 overflow-hidden cursor-pointer shrink-0">
                <img onerror="this.onerror=null;this.src=window.SVG_IMAGE_FALLBACK||'data:image/svg+xml;charset=UTF-8,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'800\' height=\'600\' viewBox=\'0 0 800 600\' fill=\'none\'%3E%3Crect width=\'800\' height=\'600\' fill=\'%23F3F4F6\'/%3E%3Cpath d=\'M360 260C360 248.954 368.954 240 380 240H420C431.046 240 440 248.954 440 260V265H450C466.569 265 480 278.431 480 295V345C480 361.569 466.569 375 450 375H350C333.431 375 320 361.569 320 345V295C320 278.431 333.431 265 350 265H360V260Z\' stroke=\'%239CA3AF\' stroke-width=\'12\' stroke-linecap=\'round\' stroke-linejoin=\'round\'/%3E%3Ccircle cx=\'400\' cy=\'320\' r=\'30\' stroke=\'%239CA3AF\' stroke-width=\'12\'/%3E%3Cline x1=\'310\' y1=\'240\' x2=\'490\' y2=\'390\' stroke=\'%23EF4444\' stroke-width=\'10\' stroke-linecap=\'round\'/%3E%3Ctext x=\'400\' y=\'450\' font-family=\'sans-serif\' font-size=\'22\' font-weight=\'700\' fill=\'%236B7280\' text-anchor=\'middle\'%3EGambar Tidak Dapat Dimuat%3C/text%3E%3C/svg%3E';" 
                     src="{{ $course->thumbnail_url }}" 
                     alt="{{ $course->judul }}" 
                     class="w-full h-full object-cover group-hover:scale-105 transition duration-500" />
                
                <div class="absolute inset-0 bg-gradient-to-t from-gray-950/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition duration-300"></div>

                <!-- Level Badge Top-Left -->
                <span class="absolute top-3 left-3 px-2.5 py-1 bg-amber-400 text-gray-900 text-[10px] font-black rounded-lg shadow-xs uppercase tracking-wider">
                  {{ $course->level ?? 'Pemula' }}
                </span>

                <!-- Duration Badge Top-Right -->
                <span class="absolute top-3 right-3 px-2.5 py-1 bg-gray-900/90 text-white text-[10px] font-extrabold rounded-lg shadow-xs">
                  {{ $course->durasi ?? '30 Menit' }}
                </span>
              </div>
            @endauth

            <!-- Card Body Container (Spacious Inner Padding p-6 sm:p-7) -->
            <div class="p-6 sm:p-7 flex flex-col justify-between flex-1 space-y-4">
              <div class="space-y-2">
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-amber-600 dark:text-amber-400 block">
                  {{ $course->kategori }}
                </span>
                
                @auth
                  <a href="{{ route('public.elearning.show', $course->slug) }}" class="block">
                    <h3 class="font-bold text-base sm:text-lg text-gray-900 dark:text-white group-hover:text-amber-500 transition line-clamp-2 leading-snug">
                      {{ $course->judul }}
                    </h3>
                  </a>
                @else
                  <button type="button" 
                          @click="openGuestLoginModal('{{ route('public.elearning.show', $course->slug) }}', '{{ addslashes($course->judul) }}')" 
                          class="text-left w-full block">
                    <h3 class="font-bold text-base sm:text-lg text-gray-900 dark:text-white group-hover:text-amber-500 transition line-clamp-2 leading-snug">
                      {{ $course->judul }}
                    </h3>
                  </button>
                @endauth

                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed line-clamp-2 pt-1">
                  {{ $course->ringkasan }}
                </p>
              </div>

              <!-- Card Footer Divider Row -->
              <div class="pt-4 border-t border-gray-100 dark:border-gray-700/60 flex items-center justify-between text-xs mt-auto">
                <span class="text-[11px] text-gray-400 font-medium flex items-center gap-1.5">
                  <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                  <span>{{ number_format($course->view_count, 0, ',', '.') }} Pembaca</span>
                </span>

                @auth
                  <a href="{{ route('public.elearning.show', $course->slug) }}" class="font-extrabold text-amber-600 dark:text-amber-400 hover:underline flex items-center gap-1.5 text-xs">
                    <span>Mulai Belajar</span>
                    <svg class="w-3.5 h-3.5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"></path></svg>
                  </a>
                @else
                  <button type="button" 
                          @click="openGuestLoginModal('{{ route('public.elearning.show', $course->slug) }}', '{{ addslashes($course->judul) }}')" 
                          class="font-extrabold text-amber-600 dark:text-amber-400 hover:underline flex items-center gap-1.5 text-xs">
                    <span>Mulai Belajar</span>
                    <svg class="w-3.5 h-3.5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"></path></svg>
                  </button>
                @endauth
              </div>
            </div>

          </div>
        @empty
          <div class="col-span-full bg-white dark:bg-gray-800 p-12 sm:p-16 rounded-3xl border border-gray-200 dark:border-gray-700 text-center space-y-3 shadow-xs">
            <div class="w-14 h-14 mx-auto rounded-full bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold text-xl mb-2">
              📚
            </div>
            <h3 class="text-lg font-black text-gray-900 dark:text-white">Materi E-Learning Tidak Ditemukan</h3>
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 max-w-md mx-auto">
              Belum ada modul edukasi yang tersedia saat ini.
            </p>
          </div>
        @endforelse
      </div>

      <!-- Prominent "Lihat Lebih Banyak Modul" CTA Button Container -->
      <div class="pt-8 text-center">
        <a href="{{ route('public.elearning.katalog') }}" class="inline-flex items-center justify-center gap-2.5 px-9 py-4 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-sm rounded-2xl shadow-md hover:shadow-xl transition duration-300 transform hover:-translate-y-0.5 group">
          <span>Lihat Lebih Banyak Modul</span>
          <svg class="w-4 h-4 transition-transform group-hover:translate-x-1.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"></path></svg>
        </a>
      </div>

    </div>
    </section>

  </div>

  <!-- Universal Login Requirement Modal for Guest E-Learning Access -->
  <div x-show="loginModal" 
       x-cloak 
       x-transition:enter="transition ease-out duration-300"
       x-transition:enter-start="opacity-0 scale-95"
       x-transition:enter-end="opacity-100 scale-100"
       x-transition:leave="transition ease-in duration-200"
       x-transition:leave-start="opacity-100 scale-100"
       x-transition:leave-end="opacity-0 scale-95"
       class="fixed inset-0 z-[100] overflow-y-auto" 
       role="dialog" 
       aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="closeModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
      <div class="relative transform overflow-hidden rounded-3xl bg-white dark:bg-gray-800 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-gray-200 dark:border-gray-700 p-7 space-y-6">
        
        <div class="flex items-start justify-between">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18c-2.305 0-4.408.867-6 2.292m0-14.25v14.25"></path></svg>
            </div>
            <div>
              <h3 class="text-base font-black text-gray-900 dark:text-white">Login Diperlukan</h3>
              <p class="text-xs text-gray-500">Akses Akademi E-Learning LensMatch</p>
            </div>
          </div>
          <button type="button" @click="closeModal()" class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-white text-base font-black">✕</button>
        </div>

        <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed">
          Untuk dapat membaca materi edukasi E-Learning <strong class="text-amber-600 dark:text-amber-400" x-text="targetCourseTitle ? '“' + targetCourseTitle + '”' : ''"></strong> dan meningkatkan skill fotografi, Anda harus masuk ke akun LensMatch terlebih dahulu.
        </p>

        <div class="flex flex-col gap-2.5 pt-2">
          <a :href="'{{ route('login') }}?redirect=' + encodeURIComponent(targetRedirectUrl)" class="w-full py-3 px-4 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs rounded-xl shadow-xs transition text-center">
            Masuk ke Akun
          </a>
          
        </div>

      </div>
    </div>
  </div>

</div>

<!-- Interactivity Script -->
<script>
function elearningOverviewApp() {
    return {
        loginModal: false,
        targetRedirectUrl: '',
        targetCourseTitle: '',

        openGuestLoginModal(redirectUrl, title) {
            this.targetRedirectUrl = redirectUrl;
            this.targetCourseTitle = title;
            this.loginModal = true;
        },

        closeModal() {
            this.loginModal = false;
        }
    };
}
</script>
@endsection