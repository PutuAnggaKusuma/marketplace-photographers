@extends('layouts.app')

@section('title', 'Informasi Lomba & Event Fotografi — LensMatch')

@section('content')
<section class="pt-10 pb-16 lg:pt-12 lg:pb-24 bg-white dark:bg-gray-900/50 min-h-screen" 
         x-data="contestSectionApp()" 
         id="contest-main-section">

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 lg:space-y-10">

    <!-- Header Section (Matching Forum Standard Typography & Spacing) -->
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
      <div class="space-y-2">
        <h1 class="text-3xl sm:text-4xl lg:text-[40px] font-black text-gray-900 dark:text-white tracking-tight leading-tight">
          Informasi Lomba & Event Fotografi
        </h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 max-w-2xl leading-relaxed">
          Temukan berbagai event lomba foto terbaru, tingkatkan skill & portofolio Anda, serta menangkan total hadiah menarik.
        </p>
      </div>

      <div class="flex items-center gap-3 shrink-0">
        @auth
          <a href="{{ route('public.contests.propose.history') }}" class="px-4 py-2.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-900 dark:bg-amber-950/50 dark:text-amber-300 border border-amber-200/80 dark:border-amber-900/60 font-bold text-xs transition inline-flex items-center gap-2 shadow-2xs">
            <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>Riwayat Pengajuan</span>
          </a>
          <a href="{{ route('public.contests.propose.create') }}" class="px-5 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs rounded-xl shadow-xs transition inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path></svg>
            <span>Ajukan Lomba Foto</span>
          </a>
        @else
          <button type="button" @click="openLoginModal()" class="px-5 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs rounded-xl shadow-xs transition inline-flex items-center gap-2 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path></svg>
            <span>Ajukan Lomba Foto</span>
          </button>
        @endauth
      </div>
    </div>

    <!-- Filter Control Bar (Exact UI Matching Komunitas Forum Page - Pure White Inputs & Proportional Spacing) -->
    <div class="bg-white dark:bg-gray-800 p-7 sm:p-8 lg:p-9 rounded-3xl border border-gray-200/80 dark:border-gray-700/80 shadow-xs">
      <div class="grid grid-cols-1 md:grid-cols-12 gap-4 lg:gap-5 items-end">
        
        <!-- Filter 1: Kategori Lomba (Alpine.js Custom Select) -->
        <div class="md:col-span-3">
          <label class="block text-[10px] font-black uppercase tracking-wider text-gray-400 dark:text-gray-400 mb-2">
            KATEGORI LOMBA
          </label>
          <div class="relative" x-data="{ open: false, selectedCode: '{{ $categoryFilter ?? 'all' }}', selectedText: '{{ $categoryFilter && $categoryFilter !== 'all' ? $categoryFilter : 'Semua Kategori' }}' }">
            <button @click="open = !open" type="button" class="w-full pl-4 pr-10 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs font-bold text-gray-800 dark:text-white text-left flex items-center justify-between shadow-xs relative focus:outline-none focus:ring-2 focus:ring-amber-500 cursor-pointer">
              <span class="truncate" x-text="selectedText"></span>
              <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5">
                <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="open ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
              </div>
            </button>
            <div x-show="open" @click.away="open = false" x-cloak x-transition class="absolute z-50 w-full top-full mt-2 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/80 rounded-xl shadow-xl py-1 text-left overflow-hidden max-h-56 overflow-y-auto divide-y divide-gray-50 dark:divide-gray-700/50">
              <div @click="selectedCode = 'all'; selectedText = 'Semua Kategori'; open = false; applyFilter('kategori', 'all');" class="px-4 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition cursor-pointer" :class="selectedCode === 'all' ? 'bg-amber-50/70 text-amber-600' : ''">
                Semua Kategori
              </div>
              @foreach($categories as $catName)
                <div @click="selectedCode = '{{ $catName }}'; selectedText = '{{ $catName }}'; open = false; applyFilter('kategori', '{{ $catName }}');" class="px-4 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition cursor-pointer" :class="selectedCode === '{{ $catName }}' ? 'bg-amber-50/70 text-amber-600' : ''">
                  {{ $catName }}
                </div>
              @endforeach
            </div>
          </div>
        </div>

        <!-- Filter 2: Status Lomba (Alpine.js Custom Select - Pure White Background) -->
        <div class="md:col-span-2">
          <label class="block text-[10px] font-black uppercase tracking-wider text-gray-400 dark:text-gray-400 mb-2">
            STATUS LOMBA
          </label>
          <div class="relative" x-data="{ open: false, selectedCode: '{{ $statusFilter }}', selectedText: '{{ $statusFilter === "buka" ? "Pendaftaran Buka" : ($statusFilter === "selesai" ? "Lomba Selesai" : "Semua Status") }}' }">
            <button @click="open = !open" type="button" class="w-full pl-4 pr-10 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs font-bold text-gray-800 dark:text-white text-left flex items-center justify-between shadow-xs relative focus:outline-none focus:ring-2 focus:ring-amber-500">
              <span class="truncate" x-text="selectedText"></span>
              <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5">
                <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="open ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
              </div>
            </button>
            <div x-show="open" @click.away="open = false" x-cloak x-transition class="absolute z-50 w-full top-full mt-2 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/80 rounded-xl shadow-xl py-1 text-left overflow-hidden">
              <div @click="selectedCode = 'all'; selectedText = 'Semua Status'; open = false; applyFilter('status', 'all');" class="px-4 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition cursor-pointer">
                Semua Status
              </div>
              <div @click="selectedCode = 'buka'; selectedText = 'Pendaftaran Buka'; open = false; applyFilter('status', 'buka');" class="px-4 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 border-t border-gray-100 dark:border-gray-700/60 transition cursor-pointer">
                Pendaftaran Buka
              </div>
              <div @click="selectedCode = 'selesai'; selectedText = 'Lomba Selesai'; open = false; applyFilter('status', 'selesai');" class="px-4 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 border-t border-gray-100 dark:border-gray-700/60 transition cursor-pointer">
                Lomba Selesai
              </div>
            </div>
          </div>
        </div>

        <!-- Filter 3: Urutkan (Alpine.js Custom Select - Pure White Background) -->
        <div class="md:col-span-2">
          <label class="block text-[10px] font-black uppercase tracking-wider text-gray-400 dark:text-gray-400 mb-2">
            URUTKAN
          </label>
          <div class="relative" x-data="{ open: false, selectedCode: '{{ $sortFilter }}', selectedText: '{{ $sortFilter === "populer" ? "Paling Populer" : ($sortFilter === "deadline" ? "Segera Berakhir" : "Terbaru") }}' }">
            <button @click="open = !open" type="button" class="w-full pl-4 pr-10 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs font-bold text-gray-800 dark:text-white text-left flex items-center justify-between shadow-xs relative focus:outline-none focus:ring-2 focus:ring-amber-500 cursor-pointer">
              <span class="truncate" x-text="selectedText"></span>
              <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5">
                <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="open ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
              </div>
            </button>
            <div x-show="open" @click.away="open = false" x-cloak x-transition class="absolute z-50 w-full top-full mt-2 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/80 rounded-xl shadow-xl py-1 text-left overflow-hidden">
              <div @click="selectedCode = 'terbaru'; selectedText = 'Terbaru'; open = false; applyFilter('sort', 'terbaru');" class="px-4 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition cursor-pointer">
                Terbaru
              </div>
              <div @click="selectedCode = 'populer'; selectedText = 'Paling Populer'; open = false; applyFilter('sort', 'populer');" class="px-4 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 border-t border-gray-100 dark:border-gray-700/60 transition cursor-pointer">
                Paling Populer
              </div>
              <div @click="selectedCode = 'deadline'; selectedText = 'Segera Berakhir'; open = false; applyFilter('sort', 'deadline');" class="px-4 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 border-t border-gray-100 dark:border-gray-700/60 transition cursor-pointer">
                Segera Berakhir
              </div>
            </div>
          </div>
        </div>

        <!-- Filter 4: Search Input (Pure White Background Matching Forum Page) -->
        <div class="md:col-span-5">
          <label class="block text-[10px] font-black uppercase tracking-wider text-gray-400 dark:text-gray-400 mb-2">
            CARI EVENT LOMBA
          </label>
          <form @submit.prevent="submitSearch()" class="relative">
            <input type="text" 
                   x-model="searchQuery" 
                   placeholder="Cari nama lomba, penyelenggara, atau kata kunci..." 
                   class="w-full pl-4 pr-10 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500 shadow-xs">
            <button type="submit" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400 hover:text-amber-500 transition">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </button>
          </form>
        </div>

      </div>
    </div>

    <!-- AJAX Container for Contest Cards Grid & Pagination -->
    <div id="contest-ajax-container" class="transition-opacity duration-150 space-y-10">
      
      <!-- 3-Column Card Grid (Inspiration from Pasted Image 1 ng.hmtiudayana.id/informasi-lomba) -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8">
        @forelse($contests as $contest)
          <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-200/80 dark:border-gray-700/80 overflow-hidden shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between group h-full">
            
            <!-- Poster Image Frame (Full Width flush to Top, Left, Right - NO PADDING) -->
            <div class="relative overflow-hidden aspect-[4/5] bg-gray-100 dark:bg-gray-900 w-full shrink-0">
              <img onerror="this.onerror=null;this.src=window.SVG_IMAGE_FALLBACK||'data:image/svg+xml;charset=UTF-8,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'800\' height=\'600\' viewBox=\'0 0 800 600\' fill=\'none\'%3E%3Crect width=\'800\' height=\'600\' fill=\'%23F3F4F6\'/%3E%3Cpath d=\'M360 260C360 248.954 368.954 240 380 240H420C431.046 240 440 248.954 440 260V265H450C466.569 265 480 278.431 480 295V345C480 361.569 466.569 375 450 375H350C333.431 375 320 361.569 320 345V295C320 278.431 333.431 265 350 265H360V260Z\' stroke=\'%239CA3AF\' stroke-width=\'12\' stroke-linecap=\'round\' stroke-linejoin=\'round\'/%3E%3Ccircle cx=\'400\' cy=\'320\' r=\'30\' stroke=\'%239CA3AF\' stroke-width=\'12\'/%3E%3Cline x1=\'310\' y1=\'240\' x2=\'490\' y2=\'390\' stroke=\'%23EF4444\' stroke-width=\'10\' stroke-linecap=\'round\'/%3E%3Ctext x=\'400\' y=\'450\' font-family=\'sans-serif\' font-size=\'22\' font-weight=\'700\' fill=\'%236B7280\' text-anchor=\'middle\'%3EGambar Tidak Dapat Dimuat%3C/text%3E%3C/svg%3E';" 
                   src="{{ $contest->banner_url }}" 
                   alt="{{ $contest->judul_lomba }}" 
                   class="w-full h-full object-cover group-hover:scale-105 transition duration-500" />
              
              <div class="absolute inset-0 bg-gradient-to-t from-gray-950/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition duration-300"></div>
            </div>

            <!-- Card Content Area with Normal Inner Padding (p-6 sm:p-7) -->
            <div class="p-6 sm:p-7 flex flex-col flex-1 justify-between space-y-4">
              
              <div class="space-y-3">
                <!-- Title & Status Badge Row -->
                <div class="flex items-start justify-between gap-2.5">
                  <h3 class="font-bold text-base sm:text-lg text-gray-900 dark:text-white group-hover:text-amber-500 transition line-clamp-2 leading-snug">
                    {{ $contest->judul_lomba }}
                  </h3>

                  <!-- Status Badge (Buka / Tutup) -->
                  @if($contest->status === 'buka')
                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-extrabold uppercase tracking-wider bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 shrink-0 mt-0.5">
                      Buka
                    </span>
                  @else
                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-extrabold uppercase tracking-wider bg-red-100 text-red-700 dark:bg-red-950/60 dark:text-red-300 border border-red-200 dark:border-red-800 shrink-0 mt-0.5">
                      Tutup
                    </span>
                  @endif
                </div>

                <!-- Registration Deadline Date -->
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium flex items-center gap-1.5">
                  <svg class="w-3.5 h-3.5 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                  <span>Batas Pendaftaran: <strong class="text-gray-700 dark:text-gray-300 font-bold">{{ \Carbon\Carbon::parse($contest->end_date)->translatedFormat('d F Y') }}</strong></span>
                </p>

                <!-- Highlight Bullet Point -->
                <div class="pt-1 text-xs text-gray-600 dark:text-gray-300 space-y-1">
                  @if($contest->hadiah)
                    <p class="font-semibold text-amber-600 dark:text-amber-400 flex items-center gap-1.5">
                      <span>Total Hadiah: {{ $contest->hadiah }}</span>
                    </p>
                  @endif
                  <p class="text-gray-500 dark:text-gray-400 line-clamp-2 text-xs leading-relaxed pt-1">
                    {{ $contest->deskrpisi_lomba }}
                  </p>
                </div>
              </div>

              <!-- Bottom Action Button (Lihat Detail ->) -->
              <div class="pt-2 mt-auto">
                <a href="{{ route('public.contests.show', $contest->id) }}" class="w-full py-3 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs sm:text-sm rounded-xl transition duration-200 flex items-center justify-center gap-2 shadow-xs">
                  <span>Lihat Lomba</span>
                  <svg class="w-3.5 h-3.5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"></path></svg>
                </a>
              </div>

            </div>

          </div>
        @empty
          <div class="col-span-full bg-white dark:bg-gray-800 p-12 sm:p-16 rounded-3xl border border-gray-200 dark:border-gray-700 text-center space-y-3 shadow-xs">
            <div class="w-14 h-14 mx-auto rounded-full bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold text-xl mb-2">
              <svg class="w-7 h-7 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.504-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.003 0H9.497m5.003 0a7.5 7.5 0 004.5-6.875V4.5a1.5 1.5 0 00-1.5-1.5h-10.5a1.5 1.5 0 00-1.5 1.5v4.25c0 2.92 1.666 5.462 4.5 6.875zM18.75 6.75h2.25a2.25 2.25 0 012.25 2.25v.75a4.5 4.5 0 01-4.5 4.5h-.45M5.25 6.75H3a2.25 2.25 0 00-2.25 2.25v.75a4.5 4.5 0 004.5 4.5h.45"></path></svg>
            </div>
            <h3 class="text-lg font-black text-gray-900 dark:text-white">Event Lomba Foto Tidak Ditemukan</h3>
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 max-w-md mx-auto">
              Belum ada event lomba foto yang sesuai dengan filter pendaftaran atau pencarian Anda saat ini.
            </p>
          </div>
        @endforelse
      </div>

      <!-- Custom Reusable AJAX Pagination Component -->
      @if($contests->total() > 0)
        <div class="pt-4">
          {{ $contests->links('partials.public.pagination') }}
        </div>
      @endif

    </div>

  </div>

  <!-- Universal Login Requirement Modal for Guests (Rule #6 & #59 Compliant) -->
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
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="closeLoginModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
      <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 text-center shadow-xl transition-all sm:my-8 w-full max-w-[420px] border border-gray-200 dark:border-gray-700 p-7 sm:p-8 space-y-4" @click.stop>
        
        <!-- Plain Close Button Absolute Top Right (SVG Vector per Rule #6 & #59) -->
        <button type="button" @click="closeLoginModal()" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 dark:hover:text-white transition cursor-pointer p-1" aria-label="Tutup">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        <!-- Header without Icon & Centered -->
        <div class="text-center">
          <h3 class="text-base sm:text-lg font-black text-gray-900 dark:text-white">Login Diperlukan</h3>
        </div>

        <!-- Body Text (Centered) -->
        <p class="text-xs sm:text-[13px] text-gray-600 dark:text-gray-300 leading-relaxed text-center">
          Untuk dapat mengajukan proposal penyelenggaraan kompetisi foto baru di platform LensMatch, Anda harus masuk ke akun terlebih dahulu.
        </p>

        <!-- Action Button -->
        <div class="pt-1">
          <a href="{{ route('login', ['redirect' => route('public.contests.propose.create')]) }}" class="w-full py-3 px-4 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs sm:text-sm rounded-xl shadow-xs transition hover:scale-[1.01] flex items-center justify-center cursor-pointer">
            Masuk ke Akun
          </a>
        </div>

      </div>
    </div>
  </div>

</section>

<!-- Smooth AJAX Page Fetching & Interactivity Script -->
<script>
function contestSectionApp() {
    return {
        loginModal: false,
        openLoginModal() {
            this.loginModal = true;
        },
        closeLoginModal() {
            this.loginModal = false;
        },
        searchQuery: '{{ $search }}',
        currentStatus: '{{ $statusFilter }}',
        currentCategory: '{{ $categoryFilter ?? "all" }}',
        currentSort: '{{ $sortFilter }}',
        
        submitSearch() {
            this.fetchContestsPage(this.buildUrl(1));
        },

        applyFilter(type, value) {
            if (type === 'status') this.currentStatus = value;
            if (type === 'kategori') this.currentCategory = value;
            if (type === 'sort') this.currentSort = value;
            this.fetchContestsPage(this.buildUrl(1));
        },

        buildUrl(page = 1) {
            const url = new URL('{{ route("public.contests.index") }}', window.location.origin);
            if (this.currentStatus && this.currentStatus !== 'all') url.searchParams.set('status', this.currentStatus);
            if (this.currentCategory && this.currentCategory !== 'all') url.searchParams.set('kategori', this.currentCategory);
            if (this.currentSort && this.currentSort !== 'terbaru') url.searchParams.set('sort', this.currentSort);
            if (this.searchQuery) url.searchParams.set('q', this.searchQuery);
            if (page > 1) url.searchParams.set('page', page);
            return url.toString();
        },

        fetchContestsPage(url) {
            const container = document.getElementById('contest-ajax-container');
            if (container) container.style.opacity = '0.4';

            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContent = doc.getElementById('contest-ajax-container');
                if (newContent && container) {
                    container.innerHTML = newContent.innerHTML;
                    container.style.opacity = '1';
                }
                history.pushState(null, '', url);
                document.getElementById('contest-main-section')?.scrollIntoView({ behavior: 'smooth' });
            })
            .catch(err => {
                console.error(err);
                if (container) container.style.opacity = '1';
                window.location.href = url;
            });
        }
    };
}

// Global AJAX Pagination Listener
document.addEventListener('DOMContentLoaded', () => {
    document.addEventListener('click', (e) => {
        const link = e.target.closest('#contest-ajax-container a[href*="page="]');
        if (link) {
            e.preventDefault();
            const app = Alpine.$data(document.getElementById('contest-main-section'));
            if (app && app.fetchContestsPage) {
                app.fetchContestsPage(link.href);
            } else {
                window.location.href = link.href;
            }
        }
    });

    window.addEventListener('popstate', () => {
        window.location.reload();
    });
});
</script>
@endsection