@extends('layouts.app')



@section('title', 'Forum Diskusi Fotografer — LensMatch')



@section('content')

<section class="pt-10 pb-4 lg:pt-12 lg:pb-6 bg-white dark:bg-gray-900 min-h-screen" 

         x-data="forumSectionApp()" 

         id="forum-main-section">



  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 lg:space-y-10">



    <!-- Header Section (Matching Lomba/Contest Standard Header & Action Button Placement) -->

    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">

      <div class="space-y-2">

        <h1 class="text-3xl sm:text-4xl lg:text-[40px] font-black text-gray-900 dark:text-white tracking-tight leading-tight">

          Forum Diskusi Fotografer

        </h1>

        <p class="text-sm text-gray-500 dark:text-gray-400 max-w-2xl leading-relaxed">

          Tanya, berbagi teknik, dan diskusi bareng sesama fotografer dari seluruh Indonesia.

        </p>

      </div>



      @php
        $isHoldPhotographer = auth()->check() && auth()->user()->isHoldPhotographer();
      @endphp

      <div class="flex items-center gap-3 shrink-0">

        @auth

          @if($isHoldPhotographer)

            <button type="button" @click="openPhotographerHoldModal('membuat topik diskusi forum')" class="px-5 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs rounded-xl shadow-xs transition inline-flex items-center gap-2 cursor-pointer">

              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path></svg>

              <span>Buat Topik Forum</span>

            </button>

          @else

            <button type="button" @click="newPostModal = true" class="px-5 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs rounded-xl shadow-xs transition inline-flex items-center gap-2 cursor-pointer">

              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path></svg>

              <span>Buat Topik Forum</span>

            </button>

          @endif

        @else

          <button type="button" @click="loginRequiredModal = true" class="px-5 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs rounded-xl shadow-xs transition inline-flex items-center gap-2 cursor-pointer">

            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path></svg>

            <span>Buat Topik Forum</span>

          </button>

        @endauth

      </div>

    </div>



    <!-- Filter Control Bar (Standard 12-Column Balanced Spacing) -->

    <div class="bg-white dark:bg-gray-800 p-7 sm:p-8 lg:p-9 rounded-3xl border border-gray-200/80 dark:border-gray-700/80 shadow-xs">

      <div class="grid grid-cols-1 md:grid-cols-12 gap-5 lg:gap-6 items-end">

        

        <!-- Filter 1: Kategori -->

        <div class="md:col-span-3">

          <label class="block text-[10px] font-black uppercase tracking-wider text-gray-400 dark:text-gray-400 mb-2">

            KATEGORI

          </label>

          @php
            $catNames = [
              'semua' => 'Semua Kategori',
              'teknis' => 'Teknis & Pengaturan Lensa',
              'spot_foto' => 'Spot Foto Outdoor',
              'peralatan' => 'Peralatan Studio',
              'lainnya' => 'Lainnya & Diskusi Umum',
            ];
            $curCatName = $catNames[$category] ?? 'Semua Kategori';
          @endphp
          <div class="relative" x-data="{ open: false, selectedText: '{{ $curCatName }}' }">

            <button @click="open = !open" type="button" class="w-full pl-4 pr-10 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs font-bold text-gray-800 dark:text-white text-left flex items-center justify-between shadow-xs relative">

              <span x-text="selectedText"></span>

              <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5">

                <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="open ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>

              </div>

            </button>

            <div x-show="open" @click.away="open = false" x-transition class="absolute z-30 left-0 right-0 mt-2 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700/80 py-1 overflow-hidden" style="display: none;">

              <a @click.prevent="loadAjaxPage('{{ route('public.forum.index', array_merge(request()->query(), ['cat' => 'semua', 'page' => 1])) }}'); open = false; selectedText = 'Semua Kategori'" href="#" class="block px-4 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition" :class="'{{ $category }}' === 'semua' ? 'bg-amber-50/70 text-amber-600' : ''">Semua Kategori</a>

              <a @click.prevent="loadAjaxPage('{{ route('public.forum.index', array_merge(request()->query(), ['cat' => 'teknis', 'page' => 1])) }}'); open = false; selectedText = 'Teknis & Pengaturan Lensa'" href="#" class="block px-4 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 border-t border-gray-100 dark:border-gray-700/60 transition" :class="'{{ $category }}' === 'teknis' ? 'bg-amber-50/70 text-amber-600' : ''">Teknis & Pengaturan Lensa</a>

              <a @click.prevent="loadAjaxPage('{{ route('public.forum.index', array_merge(request()->query(), ['cat' => 'spot_foto', 'page' => 1])) }}'); open = false; selectedText = 'Spot Foto Outdoor'" href="#" class="block px-4 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 border-t border-gray-100 dark:border-gray-700/60 transition" :class="'{{ $category }}' === 'spot_foto' ? 'bg-amber-50/70 text-amber-600' : ''">Spot Foto Outdoor</a>

              <a @click.prevent="loadAjaxPage('{{ route('public.forum.index', array_merge(request()->query(), ['cat' => 'peralatan', 'page' => 1])) }}'); open = false; selectedText = 'Peralatan Studio'" href="#" class="block px-4 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 border-t border-gray-100 dark:border-gray-700/60 transition" :class="'{{ $category }}' === 'peralatan' ? 'bg-amber-50/70 text-amber-600' : ''">Peralatan Studio</a>

              <a @click.prevent="loadAjaxPage('{{ route('public.forum.index', array_merge(request()->query(), ['cat' => 'lainnya', 'page' => 1])) }}'); open = false; selectedText = 'Lainnya & Diskusi Umum'" href="#" class="block px-4 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 border-t border-gray-100 dark:border-gray-700/60 transition" :class="'{{ $category }}' === 'lainnya' ? 'bg-amber-50/70 text-amber-600' : ''">Lainnya & Diskusi Umum</a>

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



        <!-- Filter 3: Cari Topik Forum (Expanded to md:col-span-6 for full breathing room) -->

        <div class="md:col-span-6">

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



      </div>

    </div>



    <!-- DYNAMIC AJAX CONTAINER FOR POSTS & PAGINATION -->

    <div id="forum-ajax-container" :class="loading ? 'opacity-40 pointer-events-none transition-opacity duration-150' : 'opacity-100 transition-opacity duration-150'" class="space-y-7 lg:space-y-8">

      

      <!-- 2-Column Discussion Card Grid (Generous Gap gap-7 lg:gap-8, items-start for Natural Content Height) -->

      <div class="grid grid-cols-1 md:grid-cols-2 gap-7 lg:gap-8 items-start">

        @forelse($posts as $post)

          @php

            $badgeLabels = [
              'teknis' => 'TEKNIS',
              'spot_foto' => 'SPOT FOTO',
              'peralatan' => 'PERALATAN',
              'lainnya' => 'LAINNYA',
            ];
            $catBadge = $badgeLabels[$post->kategori ?? 'lainnya'] ?? 'LAINNYA';

            $authorName = $post->user->nama ?? ($post->user->email ?? 'Pengguna');

            $words = explode(' ', $authorName);

            $inits = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));

          @endphp

          

          <!-- Card Container (Generous Internal Padding p-7 lg:p-9) -->

          <div class="bg-white dark:bg-gray-800 p-7 sm:p-8 lg:p-9 rounded-3xl border border-gray-200/80 dark:border-gray-700/80 shadow-xs hover:shadow-md transition flex flex-col justify-between space-y-5 group">

            

            <div class="space-y-4">

                            <!-- Top Category Badge -->

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



              <!-- Photo Gallery Preview on Card (1 to 5 Photos) - Standardized Fixed Height across all photo counts -->

              @if($post->medias->isNotEmpty())

                <a href="{{ route('public.forum.show', $post->id) }}" class="block pt-1">

                  @php $mCount = $post->medias->count(); @endphp

                  @if($mCount == 1)

                    <div class="h-36 sm:h-40 rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-700/80 bg-gray-100 dark:bg-gray-900">

                      <img src="{{ $post->medias[0]->foto_url }}" alt="{{ $post->judul }}" class="w-full h-full object-cover group-hover:scale-102 transition duration-300" loading="lazy">

                    </div>

                  @elseif($mCount == 2)

                    <div class="grid grid-cols-2 gap-2 h-36 sm:h-40 rounded-2xl overflow-hidden">

                      @foreach($post->medias->take(2) as $media)

                        <div class="h-full rounded-xl overflow-hidden border border-gray-100 dark:border-gray-700/80 bg-gray-100 dark:bg-gray-900">

                          <img src="{{ $media->foto_url }}" alt="{{ $post->judul }}" class="w-full h-full object-cover group-hover:scale-102 transition duration-300" loading="lazy">

                        </div>

                      @endforeach

                    </div>

                  @else

                    <div class="grid grid-cols-3 gap-2 h-36 sm:h-40 rounded-2xl overflow-hidden">

                      @foreach($post->medias->take(3) as $idx => $media)

                        <div class="relative h-full rounded-xl overflow-hidden border border-gray-100 dark:border-gray-700/80 bg-gray-100 dark:bg-gray-900">

                          <img src="{{ $media->foto_url }}" alt="{{ $post->judul }}" class="w-full h-full object-cover group-hover:scale-102 transition duration-300" loading="lazy">

                          @if($idx === 2 && $mCount > 3)

                            <div class="absolute inset-0 bg-black/65 backdrop-blur-xs flex items-center justify-center text-white font-black text-sm">

                              +{{ $mCount - 3 }} Foto

                            </div>

                          @endif

                        </div>

                      @endforeach

                    </div>

                  @endif

                </a>

              @endif

            </div>



            <!-- Bottom Meta Row Footer (Refined Tight Proportional Padding pt-3.5 mt-3) -->

            <div class="pt-3.5 sm:pt-4 border-t border-gray-100 dark:border-gray-700/60 flex items-center justify-between">

              <!-- Author & Date -->

              <div class="flex items-center gap-3.5">

                <x-user-avatar :user="$post->user" size="w-9 h-9" />

                <div>

                  <h4 class="font-extrabold text-xs sm:text-sm text-gray-900 dark:text-white line-clamp-1">{{ $post->user->nama ?? ($post->user->email ?? 'Pengguna') }}</h4>

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



    <!-- Modal Form Create New Discussion Post (Standard Hybrid Responsive Form Standard) -->
    @if(auth()->check() && !$isHoldPhotographer)
    <div x-show="newPostModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">

      <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="newPostModal = false"></div>

      <div class="flex min-h-full items-center justify-center p-4 sm:p-6 lg:p-8">

        <div class="relative transform overflow-hidden rounded-3xl bg-white dark:bg-gray-800 text-left shadow-2xl transition-all sm:my-8 w-full sm:max-w-2xl lg:max-w-3xl border border-gray-200/80 dark:border-gray-700/80 p-6 sm:p-8 lg:p-10 space-y-6"

             x-data="forumPostUpload()"

             @click.stop>

          

          <!-- Modal Header (Rule #59 Compliant: Centered without Icon, NO X button) -->

          <div class="border-b border-gray-100 dark:border-gray-700/70 pb-5 text-center space-y-1">

            <h3 class="text-xl sm:text-2xl font-black text-gray-900 dark:text-white tracking-tight text-center">

              Buat Topik Forum Baru

            </h3>

            <p class="text-xs text-gray-500 dark:text-gray-400 text-center">

              Mulai diskusi baru, bagikan hasil foto, atau tanyakan saran teknis fotografi kepada sesama anggota komunitas.

            </p>

          </div>



          <!-- Form Element -->
          <form action="{{ route('public.forum.store') }}" method="POST" enctype="multipart/form-data" @submit="submitPost($event)" novalidate class="space-y-5">
            @csrf

            <!-- Field 1: Judul Topik Diskusi -->
            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">
                Judul Topik Diskusi <span class="text-rose-500">*</span>
              </label>
              <input type="text" 
                     name="judul" 
                     x-ref="fieldJudul"
                     x-model="formData.judul"
                     @input="touched.judul = true; validateJudul()"
                     @blur="touched.judul = true; validateJudul()"
                     required 
                     maxlength="255"
                     placeholder="mis. Rekomendasi Lensa 85mm f/1.4 vs 50mm f/1.2 untuk Sesi Outdoor..." 
                     class="w-full px-4 py-3 rounded-xl border bg-white dark:bg-gray-900 text-xs sm:text-sm text-gray-900 dark:text-white placeholder-gray-400 outline-none focus:ring-2 transition shadow-xs"
                     :class="touched.judul && errors.judul ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500'">
              <p x-show="touched.judul && errors.judul" x-text="errors.judul" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
              <p class="text-[11px] text-gray-400">Tuliskan judul yang spesifik agar memudahkan fotografer lain menemukan topik Anda.</p>
            </div>

            <!-- Field 2: Kategori Topik Diskusi (Rule #11 Compliant Custom Alpine.js Dropdown) -->
            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">
                Kategori Diskusi <span class="text-rose-500">*</span>
              </label>
              <input type="hidden" name="kategori" :value="formData.kategori" required>
              <div class="relative">
                <button type="button"
                        @click="categoryDropdownOpen = !categoryDropdownOpen"
                        class="w-full px-4 py-3 rounded-xl border bg-white dark:bg-gray-900 text-xs sm:text-sm font-bold text-gray-900 dark:text-white flex items-center justify-between gap-2 shadow-2xs hover:border-amber-400 dark:hover:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-400 transition cursor-pointer text-left"
                        :class="touched.kategori && errors.kategori ? 'border-red-400 focus:ring-red-400' : 'border-gray-200 dark:border-gray-700'">
                  <span x-text="selectedCategoryLabel" class="truncate"></span>
                  <svg class="w-4 h-4 text-gray-400 shrink-0 transition-transform duration-200"
                       :class="categoryDropdownOpen ? 'rotate-180 text-amber-500' : ''"
                       fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                  </svg>
                </button>
                <div x-show="categoryDropdownOpen"
                     @click.away="categoryDropdownOpen = false"
                     x-transition
                     class="absolute z-50 left-0 right-0 mt-2 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700/80 py-1 overflow-hidden"
                     style="display: none;">
                  <template x-for="(label, key) in categories" :key="key">
                    <button type="button"
                            @click="formData.kategori = key; selectedCategoryLabel = label; touched.kategori = true; validateKategori(); categoryDropdownOpen = false"
                            class="w-full px-4 py-2.5 text-xs sm:text-sm font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition flex items-center justify-between text-left cursor-pointer border-b border-gray-50 dark:border-gray-700/40 last:border-0"
                            :class="formData.kategori === key ? 'bg-amber-50/70 dark:bg-amber-950/40 text-amber-600' : ''">
                      <span x-text="label"></span>
                    </button>
                  </template>
                </div>
              </div>
              <p x-show="touched.kategori && errors.kategori" x-text="errors.kategori" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
            </div>

            <!-- Field 3: Penjelasan & Detail Isi Diskusi -->
            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">
                Penjelasan & Isi Diskusi <span class="text-rose-500">*</span>
              </label>
              <textarea name="deskripsi" 
                        rows="5" 
                        x-ref="fieldDeskripsi"
                        x-model="formData.deskripsi"
                        @input="touched.deskripsi = true; validateDeskripsi()"
                        @blur="touched.deskripsi = true; validateDeskripsi()"
                        required 
                        placeholder="Tuliskan pertanyaan, detail permasalahan, lokasi, setting ISO/shutter speed, atau konteks pembahasan Anda secara lengkap..." 
                        class="w-full px-4 py-3 rounded-xl border bg-white dark:bg-gray-900 text-xs sm:text-sm text-gray-900 dark:text-white placeholder-gray-400 outline-none resize-none focus:ring-2 transition shadow-xs leading-relaxed"
                        :class="touched.deskripsi && errors.deskripsi ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500'"></textarea>
              <p x-show="touched.deskripsi && errors.deskripsi" x-text="errors.deskripsi" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
              <p class="text-[11px] text-gray-400">Uraikan detail secara jelas untuk mendapatkan respon dan solusi yang akurat.</p>
            </div>



            <!-- Field 3: Multi-Photo Upload (Max 5 Photos) -->

            <div class="space-y-2 pt-1">

              <div class="flex items-center justify-between flex-wrap gap-2">

                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">

                  Foto Pendukung / Karya <span class="text-gray-400 font-normal">(Opsional, Maksimal 5 Foto)</span>

                </label>

                <span class="text-xs font-normal text-gray-400 dark:text-gray-500" 

                      x-text="photos.length + ' / 5 Foto Terpilih'">

                  0 / 5 Foto Terpilih

                </span>

              </div>



              <!-- Real Hidden File Input -->

              <input type="file" 

                     name="foto_files[]" 

                     multiple 

                     accept="image/jpeg,image/png,image/webp,image/jpg" 

                     x-ref="fileInput" 

                     @change="handleFiles($event)" 

                     class="hidden">



              <!-- Interactive Upload Box / Dropzone -->

              <div @click="if (photos.length < 5) $refs.fileInput.click()"

                   @dragover.prevent="isDragging = true"

                   @dragleave.prevent="isDragging = false"

                   @drop.prevent="handleDrop($event)"

                   :class="{

                     'border-red-400 bg-red-50/40 dark:bg-red-950/20': errorMsg,

                     'border-gray-400 bg-gray-100 dark:bg-gray-800': isDragging && !errorMsg,

                     'opacity-60 cursor-not-allowed pointer-events-none': photos.length >= 5,

                     'cursor-pointer hover:border-gray-400 dark:hover:border-gray-600': photos.length < 5 && !errorMsg

                   }"

                   class="relative rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-700 bg-gray-50/70 dark:bg-gray-900/50 p-6 text-center transition-all group">

                

                <div class="flex flex-col items-center justify-center space-y-2">

                  <div class="w-12 h-12 rounded-2xl bg-white dark:bg-gray-800 shadow-xs border border-gray-200/80 dark:border-gray-700 flex items-center justify-center text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-gray-300 group-hover:scale-105 transition">

                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"></path></svg>

                  </div>

                  <div>

                    <p class="text-xs font-bold text-gray-700 dark:text-gray-300">

                      Klik untuk memilih foto atau seret foto ke area ini

                    </p>

                    <p class="text-[11px] text-gray-400 mt-0.5">

                      Mendukung format JPG, PNG, WEBP. Maksimal 5 foto, masing-masing hingga 10MB.

                    </p>

                  </div>

                </div>

              </div>



              <!-- Validation Error Message Alert -->

              <div x-show="errorMsg" x-cloak class="p-3 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-xs text-rose-600 dark:text-rose-400 font-bold flex items-center gap-2">

                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>

                <span x-text="errorMsg"></span>

              </div>



              <!-- Live Image Previews Grid (Interactive remove, order badges, size labels) -->

              <div x-show="photos.length > 0" x-cloak class="pt-2">

                <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">

                  <template x-for="(p, index) in photos" :key="index">

                    <div class="relative aspect-square rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 group shadow-xs">

                      <img :src="p.url" class="w-full h-full object-cover">

                      

                      <!-- Order Badge -->

                      <span class="absolute top-2 left-2 px-2 py-0.5 rounded-md bg-black/70 backdrop-blur-xs text-[10px] font-black text-white" x-text="'#' + (index + 1)"></span>

                      

                      <!-- Delete Button -->

                      <button type="button" 

                              @click.stop="removePhoto(index)" 

                              class="absolute top-2 right-2 p-1.5 rounded-full bg-black/60 hover:bg-rose-600 text-white backdrop-blur-xs transition shadow-md cursor-pointer" 

                              title="Hapus foto ini">

                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>

                      </button>



                      <!-- Size and filename pill at bottom -->

                      <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent p-2 text-center">

                        <p class="text-[9px] text-white font-extrabold truncate" x-text="p.size"></p>

                      </div>

                    </div>

                  </template>

                </div>

              </div>



            </div>



            <!-- Modal Action Buttons Footer (Rule #74 Standard: Batal di samping kiri tombol kuning, rata kanan gap-3) -->

            <div class="flex items-center justify-center gap-3 pt-4 border-t border-gray-100 dark:border-gray-700/80">

              <button type="button" 

                      @click="newPostModal = false" 

                      class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 text-xs font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition cursor-pointer">

                Batal

              </button>

              <button type="submit"
                      class="px-6 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs shadow-xs transition cursor-pointer">
                Simpan Data
              </button>

            </div>



          </form>



        </div>

      </div>

    </div>
    @endif

    <!-- Login Required Modal for Guest (Standard Clean Modal per Pasted Image 4) -->
    <div x-show="loginRequiredModal" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto" 
         role="dialog" 
         aria-modal="true">
      <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="loginRequiredModal = false"></div>
      <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 text-center shadow-xl transition-all sm:my-8 w-full max-w-[420px] border border-gray-200 dark:border-gray-700 p-7 sm:p-8 space-y-4" @click.stop>
          
          <!-- Plain Close Button Absolute Top Right (SVG Vector per Rule #6 & #59) -->
          <button type="button" @click="loginRequiredModal = false" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 dark:hover:text-white transition cursor-pointer p-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>

          <!-- Header without Icon & Centered -->
          <div class="text-center">
            <h3 class="text-base sm:text-lg font-black text-gray-900 dark:text-white">Login Diperlukan</h3>
          </div>

          <!-- Body Text (Centered) -->
          <p class="text-xs sm:text-[13px] text-gray-600 dark:text-gray-300 leading-relaxed text-center">
            Untuk dapat membuat topik diskusi atau berinteraksi di forum ini, Anda harus masuk ke akun LensMatch terlebih dahulu.
          </p>

          <!-- Action Button -->
          <div class="pt-1">
            <a href="{{ route('login', ['redirect' => route('public.forum.index')]) }}" class="w-full py-3 px-4 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs sm:text-sm rounded-xl shadow-xs transition hover:scale-[1.01] flex items-center justify-center cursor-pointer">
              Masuk ke Akun
            </a>
          </div>

        </div>
      </div>
    </div>



  </div>

</section>



<script>

function forumPostUpload() {
  return {
    photos: [],
    errorMsg: '',
    isDragging: false,
    categoryDropdownOpen: false,
    selectedCategoryLabel: 'Lainnya & Diskusi Umum',
    categories: {
      'teknis': 'Teknis & Pengaturan Lensa',
      'spot_foto': 'Rekomendasi Spot Foto Outdoor',
      'peralatan': 'Peralatan & Aksesoris Studio',
      'lainnya': 'Lainnya & Diskusi Umum'
    },
    formData: {
      judul: '',
      kategori: 'lainnya',
      deskripsi: ''
    },
    touched: {
      judul: false,
      kategori: false,
      deskripsi: false
    },
    errors: {
      judul: '',
      kategori: '',
      deskripsi: ''
    },
    validateJudul() {
      const val = (this.formData.judul || '').trim();
      if (!val) this.errors.judul = 'Judul topik diskusi wajib diisi.';
      else if (val.length < 5) this.errors.judul = 'Judul minimal 5 karakter.';
      else this.errors.judul = '';
    },
    validateKategori() {
      if (!this.formData.kategori) this.errors.kategori = 'Kategori diskusi wajib dipilih.';
      else this.errors.kategori = '';
    },
    validateDeskripsi() {
      const val = (this.formData.deskripsi || '').trim();
      if (!val) this.errors.deskripsi = 'Penjelasan & isi diskusi wajib diisi.';
      else if (val.length < 15) this.errors.deskripsi = 'Isi diskusi minimal 15 karakter.';
      else this.errors.deskripsi = '';
    },
    submitPost(e) {
      this.touched.judul = true;
      this.touched.kategori = true;
      this.touched.deskripsi = true;
      this.validateJudul();
      this.validateKategori();
      this.validateDeskripsi();
      if (this.errors.judul || this.errors.kategori || this.errors.deskripsi) {
        e.preventDefault();
        if (this.errors.judul) this.$refs.fieldJudul?.focus();
        else if (this.errors.deskripsi) this.$refs.fieldDeskripsi?.focus();
        return false;
      }
    },

    handleFiles(event) {

      this.errorMsg = '';

      const incoming = Array.from(event.target.files || []);

      this.processFiles(incoming);

      this.syncInput();

    },

    handleDrop(event) {

      this.isDragging = false;

      this.errorMsg = '';

      const incoming = Array.from(event.dataTransfer.files || []);

      this.processFiles(incoming);

      this.syncInput();

    },

    processFiles(incoming) {

      const availableSlots = Math.max(0, 5 - this.photos.length);

      if (availableSlots === 0) {

        this.errorMsg = 'Maksimal 5 foto yang dapat diunggah per topik diskusi.';

        return;

      }

      const res = window.validateUploadFiles(incoming, {

        maxMB: 10,

        multiple: true,

        maxFiles: availableSlots

      });

      this.errorMsg = res.errorMessage;

      for (const file of res.validFiles) {

        this.photos.push({

          file: file,

          url: URL.createObjectURL(file),

          name: file.name,

          size: window.formatFileSizeMB(file.size)

        });

      }

    },

    removePhoto(index) {

      if (this.photos[index]) {

        URL.revokeObjectURL(this.photos[index].url);

        this.photos.splice(index, 1);

      }

      this.errorMsg = '';

      this.syncInput();

    },

    syncInput() {

      if (!this.$refs.fileInput) return;

      const dt = new DataTransfer();

      this.photos.forEach(p => dt.items.add(p.file));

      this.$refs.fileInput.files = dt.files;

    }

  };

}



function forumSectionApp() {

  return {

    newPostModal: false,

    loginRequiredModal: false,

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