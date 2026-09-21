@extends('layouts.app')

@section('title', 'Katalog Lengkap Modul E-Learning — LensMatch')

@section('content')
<section class="pt-10 pb-16 lg:pt-12 lg:pb-24 bg-white dark:bg-gray-900 min-h-screen" 
         x-data="elearningKatalogApp()" 
         id="elearning-katalog-section">

  <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">

    <!-- 1. PURE CLEAN CATALOG PAGE HEADER (No Hero, No "Kenapa Akademi Ini") -->
    <div class="space-y-3">
      <span class="text-[11px] font-extrabold uppercase tracking-widest text-amber-600 dark:text-amber-400 block pt-1">
        KATALOG LENGKAP E-LEARNING
      </span>
      <h1 class="text-3xl sm:text-4xl lg:text-[40px] font-black text-gray-900 dark:text-white tracking-tight leading-tight">
        Jelajahi Seluruh Modul Edukasi Fotografi
      </h1>
      <p class="text-sm text-gray-500 dark:text-gray-400 max-w-2xl leading-relaxed mt-2">
        Temukan panduan komprehensif teknik kamera, retouching warna, hingga manajemen studio dari fotografer profesional terkemuka.
      </p>
    </div>

    <!-- 2. FILTER & SEARCH CONTROL BAR (Matching Forum Style Image 1) -->
    <div class="bg-white dark:bg-gray-800 p-7 sm:p-8 rounded-3xl border border-gray-200/80 dark:border-gray-700/80 shadow-xs">
      <div class="grid grid-cols-1 md:grid-cols-12 gap-5 lg:gap-6 items-end">
        
        <!-- Filter 1: Kategori -->
        <div class="md:col-span-3">
          <label class="block text-[10px] font-black uppercase tracking-wider text-gray-400 dark:text-gray-400 mb-2">
            KATEGORI
          </label>
          <div class="relative" x-data="{ open: false, selectedText: '{{ empty($selectedCategory) || $selectedCategory === 'all' ? 'Semua Kategori' : $selectedCategory }}' }">
            <button @click="open = !open" type="button" class="w-full pl-4 pr-10 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs font-bold text-gray-800 dark:text-white text-left flex items-center justify-between shadow-xs relative">
              <span x-text="selectedText" class="truncate"></span>
              <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5">
                <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="open ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
              </div>
            </button>
            <div x-show="open" @click.away="open = false" x-transition class="absolute z-30 left-0 right-0 mt-2 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700/80 py-1 overflow-hidden max-h-60 overflow-y-auto" style="display: none;">
              <a @click.prevent="applyCategory('all'); open = false; selectedText = 'Semua Kategori'" href="#" class="block px-4 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition">Semua Kategori</a>
              @foreach($categories as $cat)
                <a @click.prevent="applyCategory('{{ $cat }}'); open = false; selectedText = '{{ $cat }}'" href="#" class="block px-4 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 border-t border-gray-100 dark:border-gray-700/60 transition">{{ $cat }}</a>
              @endforeach
            </div>
          </div>
        </div>

        <!-- Filter 2: Urutkan -->
        <div class="md:col-span-3">
          <label class="block text-[10px] font-black uppercase tracking-wider text-gray-400 dark:text-gray-400 mb-2">
            URUTKAN
          </label>
          @php
            $sortLabels = [
                'latest' => 'Terbaru',
                'oldest' => 'Terlama',
                'most_views' => 'Paling Populer'
            ];
            $curSortText = $sortLabels[$sort ?? 'latest'] ?? 'Terbaru';
          @endphp
          <div class="relative" x-data="{ open: false, selectedText: '{{ $curSortText }}' }">
            <button @click="open = !open" type="button" class="w-full pl-4 pr-10 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs font-bold text-gray-800 dark:text-white text-left flex items-center justify-between shadow-xs relative">
              <span x-text="selectedText"></span>
              <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5">
                <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="open ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
              </div>
            </button>
            <div x-show="open" @click.away="open = false" x-transition class="absolute z-30 left-0 right-0 mt-2 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700/80 py-1 overflow-hidden" style="display: none;">
              <a @click.prevent="applySort('latest'); open = false; selectedText = 'Terbaru'" href="#" class="block px-4 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition">Terbaru</a>
              <a @click.prevent="applySort('oldest'); open = false; selectedText = 'Terlama'" href="#" class="block px-4 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 border-t border-gray-100 dark:border-gray-700/60 transition">Terlama</a>
              <a @click.prevent="applySort('most_views'); open = false; selectedText = 'Paling Populer'" href="#" class="block px-4 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 border-t border-gray-100 dark:border-gray-700/60 transition">Paling Populer</a>
            </div>
          </div>
        </div>

        <!-- Filter 3: Cari Topik Modul (Icon Magnifier inside Right) -->
        <div class="md:col-span-6">
          <label class="block text-[10px] font-black uppercase tracking-wider text-gray-400 dark:text-gray-400 mb-2">
            CARI TOPIK MODUL
          </label>
          <form @submit.prevent="submitSearch()" class="relative">
            <input type="text" 
                   x-model="searchQuery" 
                   placeholder="Cari pertanyaan atau kata kunci..." 
                   class="w-full pl-4 pr-10 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500 font-medium">
            <button type="submit" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400 hover:text-amber-500 transition">
              <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </button>
          </form>
        </div>

      </div>
    </div>

    <!-- 3. AJAX CONTAINER FOR FULL CATALOG GRID & PAGINATION -->
    <div id="elearning-katalog-ajax-container" class="transition-opacity duration-150 space-y-10">
      
      <!-- 3-Column Course Cards Grid (FULL BLEED CARDS WITH PERFECT PADDING) -->
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

                <!-- Level or Completed Badge Top-Left -->
                @if($course->getUserProgress()?->status === 'selesai')
                  <span class="absolute top-3 left-3 px-2.5 py-1 bg-emerald-500 text-white text-[10px] font-black rounded-lg shadow-xs uppercase tracking-wider flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <span>Selesai</span>
                  </span>
                @else
                  <span class="absolute top-3 left-3 px-2.5 py-1 bg-amber-400 text-gray-900 text-[10px] font-black rounded-lg shadow-xs uppercase tracking-wider">
                    {{ $course->level ?? 'Pemula' }}
                  </span>
                @endif

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
                          class="text-left w-full block cursor-pointer">
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
                          class="font-extrabold text-amber-600 dark:text-amber-400 hover:underline flex items-center gap-1.5 text-xs cursor-pointer">
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
              Belum ada modul edukasi yang sesuai dengan kategori atau kata kunci pencarian Anda saat ini.
            </p>
          </div>
        @endforelse
      </div>

      <!-- Custom Reusable AJAX Pagination Component (Located ONLY on Full Catalog Page) -->
      @if($elearnings->total() > 0)
        <div class="pt-4 border-t border-gray-100 dark:border-gray-800">
          {{ $elearnings->links('partials.public.pagination') }}
        </div>
      @endif

    </div>

  </div>

  <!-- Universal Login Requirement Modal for Guest E-Learning Access (Square Box, Plain X, No Batal Button) -->
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
      <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 w-full max-w-[420px] border border-gray-200 dark:border-gray-700 p-7 sm:p-8 space-y-4">
        
        <!-- Header without Icon & Plain Close Button -->
        <div class="flex items-start justify-between">
          <div>
            <h3 class="text-base sm:text-lg font-black text-gray-900 dark:text-white">Login Diperlukan</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Akses Akademi E-Learning LensMatch</p>
          </div>
          <button type="button" @click="closeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-white text-base font-bold transition cursor-pointer p-0.5 leading-none">✕</button>
        </div>

        <!-- Body Text -->
        <p class="text-xs sm:text-[13px] text-gray-600 dark:text-gray-300 leading-relaxed">
          Untuk dapat membaca materi edukasi E-Learning <strong class="font-bold text-gray-900 dark:text-white" x-text="targetCourseTitle ? '“' + targetCourseTitle + '”' : ''"></strong> dan meningkatkan skill fotografi, Anda harus masuk ke akun LensMatch terlebih dahulu.
        </p>

        <!-- Action Button -->
        <div class="pt-1">
          <a :href="'{{ route('login') }}?redirect=' + encodeURIComponent(targetRedirectUrl)" class="w-full py-3 px-4 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs sm:text-sm rounded-xl shadow-xs transition hover:scale-[1.01] flex items-center justify-center cursor-pointer">
            Masuk ke Akun
          </a>
        </div>

      </div>
    </div>
  </div>

</section>

<!-- Smooth AJAX Page Fetching & Interactivity Script -->
<script>
function elearningKatalogApp() {
    return {
        searchQuery: '{{ $search }}',
        currentCategory: '{{ $selectedCategory ?? "all" }}',
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
        },
        
        submitSearch() {
            this.fetchElearningsPage(this.buildUrl(1));
        },

        applyCategory(value) {
            this.currentCategory = value;
            this.fetchElearningsPage(this.buildUrl(1));
        },

        buildUrl(page = 1) {
            const url = new URL('{{ route("public.elearning.katalog") }}', window.location.origin);
            if (this.currentCategory && this.currentCategory !== 'all') url.searchParams.set('category', this.currentCategory);
            if (this.searchQuery) url.searchParams.set('q', this.searchQuery);
            if (page > 1) url.searchParams.set('page', page);
            return url.toString();
        },

        fetchElearningsPage(url) {
            const container = document.getElementById('elearning-katalog-ajax-container');
            if (container) container.style.opacity = '0.4';

            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContent = doc.getElementById('elearning-katalog-ajax-container');
                if (newContent && container) {
                    container.innerHTML = newContent.innerHTML;
                    if (window.Alpine) {
                        window.Alpine.initTree(container);
                    }
                    container.style.opacity = '1';
                }
                history.pushState(null, '', url);
                document.getElementById('elearning-katalog-section')?.scrollIntoView({ behavior: 'smooth' });
            })
            .catch(err => {
                console.error(err);
                if (container) container.style.opacity = '1';
                window.location.href = url;
            });
        }
    };
}

// Global AJAX Pagination Listener for Katalog Page
document.addEventListener('DOMContentLoaded', () => {
    document.addEventListener('click', (e) => {
        const link = e.target.closest('#elearning-katalog-section a[href*="page="]');
        if (link) {
            e.preventDefault();
            const app = Alpine.$data(document.getElementById('elearning-katalog-section'));
            if (app && app.fetchElearningsPage) {
                app.fetchElearningsPage(link.href);
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