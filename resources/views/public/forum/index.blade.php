@extends('layouts.app')

@section('title', 'Forum Diskusi Fotografer — LensMatch')

@section('content')
<section class="pt-10 pb-4 lg:pt-12 lg:pb-6 bg-white dark:bg-gray-900/50 min-h-screen" 
         x-data="forumSectionApp()" 
         id="forum-main-section">

  <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 lg:space-y-10">

    <!-- Header Section (Generous Breathing Room) -->
    <div class="space-y-3">
      <span class="text-[11px] font-extrabold uppercase tracking-widest text-amber-500 dark:text-amber-500">
        KOMUNITAS
      </span>
      <h1 class="text-3xl sm:text-4xl lg:text-[40px] font-black text-gray-900 dark:text-white tracking-tight leading-tight">
        Forum Diskusi Fotografer
      </h1>
      <p class="text-sm text-gray-500 dark:text-gray-400 max-w-2xl leading-relaxed mt-2">
        Tanya, berbagi teknik, dan diskusi bareng sesama fotografer dari seluruh Indonesia.
      </p>
    </div>

    <!-- Filter Control Bar (Standard #9 Dropdown Chevron Spacing Compliance) -->
    <div class="bg-white dark:bg-gray-800 p-7 sm:p-8 lg:p-9 rounded-3xl border border-gray-200/80 dark:border-gray-700/80 shadow-xs">
      <div class="grid grid-cols-1 md:grid-cols-12 gap-5 lg:gap-6 items-end">
        
        <!-- Filter 1: Kategori -->
        <div class="md:col-span-3">
          <label class="block text-[10px] font-black uppercase tracking-wider text-gray-400 dark:text-gray-400 mb-2">
            KATEGORI
          </label>
          <div class="relative" x-data="{ open: false, selectedText: '{{ $category === 'semua' ? 'Semua Kategori' : ucfirst($category) }}' }">
            <button @click="open = !open" type="button" class="w-full pl-4 pr-10 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs font-bold text-gray-800 dark:text-white text-left flex items-center justify-between shadow-xs relative">
              <span x-text="selectedText"></span>
              <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5">
                <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="open ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
              </div>
            </button>
            <div x-show="open" @click.away="open = false" x-transition class="absolute z-30 left-0 right-0 mt-2 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700/80 py-1 overflow-hidden">
              <a @click.prevent="loadAjaxPage('{{ route('public.forum.index', array_merge(request()->query(), ['cat' => 'semua', 'page' => 1])) }}'); open = false" href="#" class="block px-4 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition">Semua Kategori</a>
              <a @click.prevent="loadAjaxPage('{{ route('public.forum.index', array_merge(request()->query(), ['cat' => 'teknis', 'page' => 1])) }}'); open = false" href="#" class="block px-4 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 border-t border-gray-100 dark:border-gray-700/60 transition">Teknis & Pengaturan Lensa</a>
              <a @click.prevent="loadAjaxPage('{{ route('public.forum.index', array_merge(request()->query(), ['cat' => 'spot_foto', 'page' => 1])) }}'); open = false" href="#" class="block px-4 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 border-t border-gray-100 dark:border-gray-700/60 transition">Spot Foto Outdoor</a>
              <a @click.prevent="loadAjaxPage('{{ route('public.forum.index', array_merge(request()->query(), ['cat' => 'peralatan', 'page' => 1])) }}'); open = false" href="#" class="block px-4 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 border-t border-gray-100 dark:border-gray-700/60 transition">Peralatan Studio</a>
            </div>
          </div>
        </div>

        <!-- Filter 2: Urutkan -->
        <div class="md:col-span-3">
          <label class="block text-[10px] font-black uppercase tracking-wider text-gray-400 dark:text-gray-400 mb-2">
            URUTKAN
          </label>
          <div class="relative" x-data="{ open: false, selectedText: '{{ $sort === 'terpopuler' ? 'Terpopuler' : 'Terbaru' }}' }">
            <button @click="open = !open" type="button" class="w-full pl-4 pr-10 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs font-bold text-gray-800 dark:text-white text-left flex items-center justify-between shadow-xs relative">
              <span x-text="selectedText"></span>
              <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5">
                <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="open ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
              </div>
            </button>
            <div x-show="open" @click.away="open = false" x-transition class="absolute z-30 left-0 right-0 mt-2 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700/80 py-1 overflow-hidden">
              <a @click.prevent="loadAjaxPage('{{ route('public.forum.index', array_merge(request()->query(), ['sort' => 'terbaru', 'page' => 1])) }}'); open = false" href="#" class="block px-4 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition">Terbaru</a>
              <a @click.prevent="loadAjaxPage('{{ route('public.forum.index', array_merge(request()->query(), ['sort' => 'terpopuler', 'page' => 1])) }}'); open = false" href="#" class="block px-4 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 border-t border-gray-100 dark:border-gray-700/60 transition">Terpopuler</a>
            </div>
          </div>
        </div>

        <!-- Filter 3: Cari Topik Forum -->
        <div class="md:col-span-4">
          <label class="block text-[10px] font-black uppercase tracking-wider text-gray-400 dark:text-gray-400 mb-2">
            CARI TOPIK FORUM
          </label>
          <form @submit.prevent="submitSearch($event)" class="relative">
            <input type="text" name="q" value="{{ $search }}" placeholder="Cari pertanyaan atau kata kunci..." class="w-full pl-4 pr-10 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500">
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5">
              <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
          </form>
        </div>

        <!-- Action Button: Buat Forum -->
        <div class="md:col-span-2">
          <button type="button" @click="newPostModal = true" class="w-full py-3 px-4 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs rounded-xl shadow-xs transition flex items-center justify-center gap-1.5 shrink-0 whitespace-nowrap">
            <span>Buat Forum</span>
          </button>
        </div>

      </div>
    </div>

    <!-- DYNAMIC AJAX CONTAINER FOR POSTS & PAGINATION -->
    <div id="forum-ajax-container" :class="loading ? 'opacity-40 pointer-events-none transition-opacity duration-150' : 'opacity-100 transition-opacity duration-150'" class="space-y-7 lg:space-y-8">
      
      <!-- 2-Column Discussion Card Grid (Generous Gap gap-7 lg:gap-8) -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-7 lg:gap-8">
        @forelse($posts as $post)
          @php
            $cats = ['SPOT FOTO', 'TEKNIS', 'PERALATAN', 'LAINNYA'];
            $catBadge = $cats[$post->id % count($cats)];
            $words = explode(' ', $post->user->nama ?? 'Admin LensMatch');
            $inits = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
          @endphp
          
          <!-- Card Container (Generous Internal Padding p-7 lg:p-9) -->
          <div class="bg-white dark:bg-gray-800 p-7 sm:p-8 lg:p-9 rounded-3xl border border-gray-200/80 dark:border-gray-700/80 shadow-xs hover:shadow-md transition flex flex-col justify-between space-y-5 group">
            
            <div class="space-y-4">
              <!-- Top Category Badge (LensMatch Amber Standard — matching katalog photographer card style) -->
              <div>
                <span class="inline-flex items-center px-3 py-1 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 font-extrabold text-[10px] uppercase tracking-wider border border-amber-200/60 dark:border-amber-800/60">
                  {{ $catBadge }}
                </span>
              </div>

              <!-- Title & Excerpt Body Text -->
              <a href="{{ route('public.forum.show', $post->id) }}" class="block space-y-2.5">
                <h3 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white group-hover:text-amber-600 transition leading-snug line-clamp-2">
                  {{ $post->judul }}
                </h3>
                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 leading-relaxed line-clamp-3">
                  {{ $post->deskripsi }}
                </p>
              </a>
            </div>

            <!-- Bottom Meta Row Footer (Refined Tight Proportional Padding pt-3.5 mt-3) -->
            <div class="pt-3.5 sm:pt-4 border-t border-gray-100 dark:border-gray-700/60 flex items-center justify-between">
              <!-- Author & Date -->
              <div class="flex items-center gap-3.5">
                <x-user-avatar :user="$post->user" size="w-9 h-9" />
                <div>
                  <h4 class="font-extrabold text-xs sm:text-sm text-gray-900 dark:text-white line-clamp-1">{{ $post->user->nama ?? 'Admin LensMatch' }}</h4>
                  <p class="text-[10px] text-gray-400 font-medium mt-0.5">{{ $post->created_at->format('Y-m-d') }} • {{ $post->created_at->format('H:i') }}</p>
                </div>
              </div>

              <!-- Real Dynamic View & Comment Metrics (Enlarged Proportional SVG Icons w-4 h-4 sm:w-4.5 sm:h-4.5) -->
              <div class="flex items-center gap-4 text-xs sm:text-sm text-gray-500 dark:text-gray-400 font-bold shrink-0">
                <span class="flex items-center gap-1.5">
                  <svg class="w-4 h-4 sm:w-4.5 sm:h-4.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                  <span>{{ number_format($post->views ?? 0) }}</span>
                </span>

                <span class="flex items-center gap-1.5">
                  <svg class="w-4 h-4 sm:w-4.5 sm:h-4.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                  <span>{{ $post->comments->count() }}</span>
                </span>
              </div>
            </div>

          </div>
        @empty
          <div class="col-span-full bg-white dark:bg-gray-800 p-12 rounded-3xl border border-gray-200 dark:border-gray-700 text-center space-y-3">
            <h3 class="text-base font-extrabold text-gray-900 dark:text-white">Belum Ada Topik Diskusi Ditemukan</h3>
            <p class="text-xs text-gray-500">Jadilah yang pertama untuk membuat topik forum baru!</p>
          </div>
        @endforelse
      </div>

      <!-- Pagination Component Container -->
      @if($posts->total() > 0)
        <div class="pt-4 sm:pt-5">
          {{ $posts->links('partials.public.pagination') }}
        </div>
      @endif

    </div>

    <!-- Modal Form Create New Discussion Post -->
    <div x-show="newPostModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
      <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="newPostModal = false"></div>
      <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative transform overflow-hidden rounded-3xl bg-white dark:bg-gray-800 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-200 dark:border-gray-700 p-7 sm:p-9 space-y-6">
          <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-4">
            <div>
              <span class="text-[10px] font-black uppercase tracking-wider text-amber-600 dark:text-amber-400">Komunitas Forum</span>
              <h3 class="text-lg font-black text-gray-900 dark:text-white">Buat Topik Forum Baru</h3>
            </div>
            <button type="button" @click="newPostModal = false" class="p-1 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-white">✕</button>
          </div>

          <form action="{{ route('public.forum.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Judul Topik *</label>
              <input type="text" name="judul" required placeholder="mis. Rekomendasi Lensa Portrait Terbaik..." class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500">
            </div>
            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Penjelasan & Isi Diskusi *</label>
              <textarea name="deskripsi" rows="5" required placeholder="Tuliskan pertanyaan atau informasi diskusi Anda secara detail..." class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none resize-none focus:ring-2 focus:ring-amber-500"></textarea>
            </div>
            <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100 dark:border-gray-700">
              <button type="button" @click="newPostModal = false" class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 text-xs font-bold text-gray-700 dark:text-gray-300">Batal</button>
              <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs shadow-xs">Terbitkan Topik</button>
            </div>
          </form>
        </div>
      </div>
    </div>

  </div>
</section>

<script>
function forumSectionApp() {
  return {
    newPostModal: false,
    loading: false,
    init() {
      window.addEventListener('popstate', () => {
        this.loadAjaxPage(window.location.href, false);
      });
    },
    async loadAjaxPage(url, pushState = true) {
      if (!url || url === '#' || url === 'javascript:void(0)') return;
      this.loading = true;
      try {
        const response = await fetch(url, {
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          }
        });
        const html = await response.text();
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        
        const newContainer = doc.querySelector('#forum-ajax-container');
        const currentContainer = document.querySelector('#forum-ajax-container');
        
        if (newContainer && currentContainer) {
          currentContainer.innerHTML = newContainer.innerHTML;
        }

        if (pushState) {
          history.pushState(null, '', url);
        }

        const sectionTop = document.querySelector('#forum-main-section').offsetTop - 40;
        window.scrollTo({ top: sectionTop, behavior: 'smooth' });

      } catch (err) {
        console.error('AJAX Load Error:', err);
      } finally {
        this.loading = false;
      }
    },
    submitSearch(e) {
      const formData = new FormData(e.target);
      const searchVal = formData.get('q') || '';
      const currentUrl = new URL(window.location.href);
      currentUrl.searchParams.set('q', searchVal);
      currentUrl.searchParams.set('page', 1);
      this.loadAjaxPage(currentUrl.toString());
    }
  };
}
</script>
@endsection