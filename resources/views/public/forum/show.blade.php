@extends('layouts.app')



@section('title', $post->judul . ' — Forum Komunitas LensMatch')



@section('content')

<section class="py-10 bg-white dark:bg-gray-900 min-h-screen">

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">



    <!-- Breadcrumb Navigation -->
    <nav class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
      <a href="{{ url('/') }}" class="hover:text-amber-500 transition">Beranda</a>
      <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
      <a href="{{ route('public.forum.index') }}" class="hover:text-amber-500 transition">Forum Diskusi</a>
      <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
      <span class="text-gray-900 dark:text-white font-bold truncate max-w-md">{{ $post->judul }}</span>
    </nav>



    <!-- 2-Column Main Layout Grid (8 Cols Left + 4 Cols Sidebar Right) -->

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">



      <!-- Left Main Thread Column (8 Cols) -->

      <div class="lg:col-span-8 space-y-6">

        

        <!-- Main Thread Content Card -->

        <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-200 dark:border-gray-700/80 shadow-sm p-6 sm:p-8 space-y-6">

          

          <!-- Meta Header: "Diskusi Forum" Badge on Top, Date & Author Row Below It -->

          <div class="space-y-2.5">

            <div>

              <span class="px-4 py-1.5 rounded-full font-black uppercase tracking-widest text-[10px] bg-gray-900 text-white dark:bg-amber-400 dark:text-gray-900 shadow-xs inline-block">

                Diskusi Forum

              </span>

            </div>



            <div class="flex flex-wrap items-center gap-2.5 text-[11px] text-gray-500 dark:text-gray-400 font-bold">

              <span>{{ strtoupper($post->created_at->format('d F Y H:i:s')) }}</span>

              <span>|</span>

              <div class="flex items-center gap-1.5 font-extrabold text-gray-700 dark:text-gray-300">

                <span>DITULIS OLEH {{ strtoupper($post->user->nama ?? 'ADMIN') }}</span>

              </div>

            </div>

          </div>



          <!-- Title H1 (Uninterrupted Focus for Reader) -->

          <div>

            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white leading-tight tracking-tight">

              {{ $post->judul }}

            </h1>

          </div>



          <!-- Main Post Paragraph Content (Sub-Text Deskripsi - Added Proportional Bottom Spacing pb-6 sm:pb-7) -->

          <div class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 leading-relaxed space-y-4 whitespace-pre-line pb-6 sm:pb-7">

            {{ $post->deskripsi }}

          </div>



          @if($post->medias->isNotEmpty())

            <!-- Media Gallery (1 to 5 Photos) with Responsive Layout & Interactive Lightbox -->

            <div x-data="{

              lightboxOpen: false,

              activeIdx: 0,

              photos: [

                @foreach($post->medias as $m)

                  '{{ $m->foto_url }}',

                @endforeach

              ],

              openLightbox(idx) {

                this.activeIdx = idx;

                this.lightboxOpen = true;

              },

              nextPhoto() {

                this.activeIdx = (this.activeIdx + 1) % this.photos.length;

              },

              prevPhoto() {

                this.activeIdx = (this.activeIdx - 1 + this.photos.length) % this.photos.length;

              }

            }" class="space-y-3 pb-6">

              

              <div class="flex items-center justify-between">

                <span class="text-xs font-black uppercase tracking-wider text-gray-600 dark:text-gray-300 flex items-center gap-2">

                  <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>

                  <span>Lampiran Foto ({{ $post->medias->count() }})</span>

                </span>

                <span class="text-[11px] text-gray-400 font-medium">Klik foto untuk melihat ukuran penuh</span>

              </div>



              @php $totalMedia = $post->medias->count(); @endphp



              @if($totalMedia === 1)

                <!-- 1 Single Large Photo -->

                <div @click="openLightbox(0)" class="relative rounded-3xl overflow-hidden border border-gray-200/80 dark:border-gray-700/80 bg-gray-900 group cursor-pointer max-h-[520px] flex items-center justify-center">

                  <img src="{{ $post->medias[0]->foto_url }}" alt="{{ $post->judul }}" class="w-full h-full max-h-[520px] object-cover sm:object-contain group-hover:scale-102 transition duration-300">

                  <div class="absolute bottom-3 right-3 px-3 py-1.5 rounded-xl bg-black/70 backdrop-blur-sm text-white text-xs font-bold flex items-center gap-1.5 opacity-90 group-hover:opacity-100 transition shadow-sm">

                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>

                    <span>Perbesar</span>

                  </div>

                </div>

              @elseif($totalMedia === 2)

                <!-- 2 Photos Equal Grid -->

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">

                  @foreach($post->medias as $idx => $m)

                    <div @click="openLightbox({{ $idx }})" class="relative h-64 sm:h-72 rounded-2xl overflow-hidden border border-gray-200/80 dark:border-gray-700/80 bg-gray-100 dark:bg-gray-900 group cursor-pointer">

                      <img src="{{ $m->foto_url }}" alt="Foto {{ $idx + 1 }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">

                      <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">

                        <span class="p-2.5 rounded-full bg-black/60 text-white shadow"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg></span>

                      </div>

                    </div>

                  @endforeach

                </div>

              @elseif($totalMedia === 3)

                <!-- 3 Photos: 1 Hero Left + 2 Right -->

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 h-auto sm:h-80">

                  <div @click="openLightbox(0)" class="sm:col-span-2 relative h-64 sm:h-full rounded-2xl overflow-hidden border border-gray-200/80 dark:border-gray-700/80 bg-gray-100 dark:bg-gray-900 group cursor-pointer">

                    <img src="{{ $post->medias[0]->foto_url }}" alt="Foto 1" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">

                    <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">

                      <span class="p-2.5 rounded-full bg-black/60 text-white shadow"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg></span>

                    </div>

                  </div>

                  <div class="grid grid-cols-2 sm:grid-cols-1 gap-3.5 h-full">

                    @foreach($post->medias->slice(1, 2) as $idx => $m)

                      <div @click="openLightbox({{ $idx + 1 }})" class="relative h-32 sm:h-full rounded-2xl overflow-hidden border border-gray-200/80 dark:border-gray-700/80 bg-gray-100 dark:bg-gray-900 group cursor-pointer">

                        <img src="{{ $m->foto_url }}" alt="Foto {{ $idx + 2 }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">

                        <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">

                          <span class="p-2 rounded-full bg-black/60 text-white shadow"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg></span>

                        </div>

                      </div>

                    @endforeach

                  </div>

                </div>

              @elseif($totalMedia === 4)

                <!-- 4 Photos: 2x2 Grid -->

                <div class="grid grid-cols-2 gap-3.5">

                  @foreach($post->medias as $idx => $m)

                    <div @click="openLightbox({{ $idx }})" class="relative h-48 sm:h-60 rounded-2xl overflow-hidden border border-gray-200/80 dark:border-gray-700/80 bg-gray-100 dark:bg-gray-900 group cursor-pointer">

                      <img src="{{ $m->foto_url }}" alt="Foto {{ $idx + 1 }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">

                      <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">

                        <span class="p-2.5 rounded-full bg-black/60 text-white shadow"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg></span>

                      </div>

                    </div>

                  @endforeach

                </div>

              @else

                <!-- 5 Photos: 2 Top + 3 Bottom -->

                <div class="space-y-3.5">

                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">

                    @foreach($post->medias->take(2) as $idx => $m)

                      <div @click="openLightbox({{ $idx }})" class="relative h-56 sm:h-64 rounded-2xl overflow-hidden border border-gray-200/80 dark:border-gray-700/80 bg-gray-100 dark:bg-gray-900 group cursor-pointer">

                        <img src="{{ $m->foto_url }}" alt="Foto {{ $idx + 1 }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">

                        <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">

                          <span class="p-2.5 rounded-full bg-black/60 text-white shadow"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg></span>

                        </div>

                      </div>

                    @endforeach

                  </div>

                  <div class="grid grid-cols-3 gap-3.5">

                    @foreach($post->medias->slice(2, 3) as $idx => $m)

                      <div @click="openLightbox({{ $idx + 2 }})" class="relative h-36 sm:h-44 rounded-2xl overflow-hidden border border-gray-200/80 dark:border-gray-700/80 bg-gray-100 dark:bg-gray-900 group cursor-pointer">

                        <img src="{{ $m->foto_url }}" alt="Foto {{ $idx + 3 }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">

                        <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">

                          <span class="p-2 rounded-full bg-black/60 text-white shadow"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg></span>

                        </div>

                      </div>

                    @endforeach

                  </div>

                </div>

              @endif



              <!-- Fullscreen Lightbox Modal -->

              <div x-show="lightboxOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 backdrop-blur-md p-4" @keydown.escape.window="lightboxOpen = false" @keydown.right.window="nextPhoto()" @keydown.left.window="prevPhoto()">

                <!-- Close Button -->

                <button type="button" @click="lightboxOpen = false" class="absolute top-5 right-5 text-white/80 hover:text-white p-2.5 rounded-full bg-white/10 hover:bg-white/20 transition z-50 cursor-pointer">

                  <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>

                </button>



                <!-- Navigation Previous -->

                <button x-show="photos.length > 1" type="button" @click="prevPhoto()" class="absolute left-4 top-1/2 -translate-y-1/2 text-white/80 hover:text-white p-3 rounded-full bg-white/10 hover:bg-white/20 transition z-50 cursor-pointer">

                  <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>

                </button>



                <!-- Current Image & Counter -->

                <div class="max-w-5xl max-h-[85vh] flex flex-col items-center gap-3">

                  <img :src="photos[activeIdx]" class="max-h-[80vh] max-w-full rounded-2xl object-contain shadow-2xl">

                  <div class="px-4 py-1.5 rounded-full bg-white/15 text-white text-xs font-bold">

                    <span x-text="(activeIdx + 1) + ' / ' + photos.length"></span>

                  </div>

                </div>



                <!-- Navigation Next -->

                <button x-show="photos.length > 1" type="button" @click="nextPhoto()" class="absolute right-4 top-1/2 -translate-y-1/2 text-white/80 hover:text-white p-3 rounded-full bg-white/10 hover:bg-white/20 transition z-50 cursor-pointer">

                  <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>

                </button>

              </div>



            </div>

          @endif



          <!-- Action & View Counter Row (Positioned After Sub-Text Content) -->

          <div class="pt-6 border-t border-gray-100 dark:border-gray-700/60 flex items-center justify-between flex-wrap gap-4">

            <div class="flex items-center gap-3">

              <!-- Sukai Diskusi Button (Standard Social Media Thumbs-Up Like Icon) -->

              <button type="button" class="px-4 py-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-900 dark:bg-amber-950/50 dark:text-amber-300 border border-amber-200/80 dark:border-amber-900/60 font-bold text-xs transition flex items-center gap-2 shadow-xs group">

                <svg class="w-4 h-4 text-amber-500 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24">

                  <path d="M1 21h4V9H1v12zm22-11c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14.17 1 7.58 7.59C7.22 7.95 7 8.45 7 9v10c0 1.1.9 2 2 2h9c.83 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-2z"/>

                </svg>

                <span>Sukai Diskusi</span>

              </button>



              <!-- Bagikan Tautan Button (Positioned After Sub-Text Content) -->

              <button type="button" 

                      @click="navigator.clipboard.writeText(window.location.href); $dispatch('show-toast', { message: 'Tautan forum berhasil disalin!', type: 'success' })" 

                      class="px-4 py-2 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs transition flex items-center gap-2 shadow-xs" 

                      title="Salin Tautan Forum">

                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">

                  <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>

                </svg>

                <span>Bagikan Tautan</span>

              </button>

            </div>



            <!-- View Counter -->

            <div class="text-xs font-bold text-gray-500 dark:text-gray-400 flex items-center gap-1.5">

              <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>

              <span>Dilihat {{ number_format($post->views ?? 0) }} kali</span>

            </div>

          </div>



        </div>



        <!-- Ethics Warning Banner -->

        <div class="bg-amber-500/10 border border-amber-500/30 p-5 rounded-2xl text-center space-y-1">

          <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">

            Dimohon untuk tetap mengikuti Aturan Berkomunikasi dalam Forum

          </p>

          <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">

            Gunakan bahasa yang santun, saling menghargai pendapat anggota komunitas, dan hindari konten spam.

          </p>

        </div>



        <!-- Comments Section Main Card Container -->

        <div x-data="{

          loginModal: false,

          reportModalOpen: false,

          reportCommentId: null,

          reportAuthor: '',

          reportSnippet: '',

          reportActionUrl: '',

          reportKategori: '',

          reportTouched: false,

          reportError: '',

          validateReportKategori() {
            if (!this.reportKategori) {
              this.reportError = 'Silakan pilih salah satu kategori pelanggaran.';
              return false;
            }
            this.reportError = '';
            return true;
          },

          submitReport(e) {
            this.reportTouched = true;
            if (!this.validateReportKategori()) {
              e.preventDefault();
              return false;
            }
            return true;
          },

          copyComment(text) {

            if (navigator.clipboard && navigator.clipboard.writeText) {

              navigator.clipboard.writeText(text);

            } else {

              const el = document.createElement('textarea');

              el.value = text;

              document.body.appendChild(el);

              el.select();

              document.execCommand('copy');

              document.body.removeChild(el);

            }

            window.dispatchEvent(new CustomEvent('show-toast', {

              detail: { type: 'success', message: 'Teks komentar berhasil disalin ke clipboard.' }

            }));

          },

          openReportModal(id, author, snippet) {

            this.reportCommentId = id;

            this.reportAuthor = author;

            this.reportSnippet = snippet;

            this.reportActionUrl = '/forum/comment/' + id + '/report';

            this.reportKategori = '';

            this.reportTouched = false;

            this.reportError = '';

            this.reportModalOpen = true;

          },

          handleGuestClick() {

            this.loginModal = true;

          },

          closeModal() {

            this.loginModal = false;

          },

          commentSort: '{{ $commentSort ?? 'newest' }}',

          sortComments(mode) {

            this.commentSort = mode;

            const list = this.$refs.commentsList;

            if (!list) return;

            const items = Array.from(list.children);

            items.sort((a, b) => {

              if (mode === 'popular') {

                const likesA = parseInt(a.dataset.likes || '0', 10);

                const likesB = parseInt(b.dataset.likes || '0', 10);

                return likesB - likesA;

              } else {

                const timeA = parseInt(a.dataset.time || '0', 10);

                const timeB = parseInt(b.dataset.time || '0', 10);

                return timeB - timeA;

              }

            });

            items.forEach(item => list.appendChild(item));

            const url = new URL(window.location.href);

            url.searchParams.set('comment_sort', mode);

            window.history.replaceState({}, '', url.toString());

          }

        }" class="bg-white dark:bg-gray-800 p-7 sm:p-9 rounded-3xl border border-gray-200/80 dark:border-gray-700/80 shadow-xs space-y-7 relative">

          

          <!-- Header Row: Comments (Count) Title Left + Popular/Newest Filter Pills Right -->

          <div class="flex items-center justify-between flex-wrap gap-4 border-b border-gray-100 dark:border-gray-700/60 pb-5">

            <h3 class="text-xl font-black text-gray-900 dark:text-white flex items-center gap-2">

              <span>Comments</span>

              <span class="text-gray-400 font-extrabold text-base">({{ number_format($post->comments_count ?? $post->comments->count()) }})</span>

            </h3>



            <!-- Filter Toggle Pills: Popular vs Newest (Real Dynamic Sorting) -->

            <div class="bg-gray-100 dark:bg-gray-700/60 p-1 rounded-2xl flex items-center gap-1 text-xs font-extrabold shadow-inner">

              <button type="button" 

                      @click="sortComments('popular')" 

                      :class="commentSort === 'popular' ? 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-xs' : 'text-gray-500 hover:text-gray-800 dark:hover:text-white'" 

                      class="px-3.5 py-1.5 rounded-xl transition flex items-center gap-1.5 cursor-pointer">

                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>

                <span>Popular</span>

              </button>



              <button type="button" 

                      @click="sortComments('newest')" 

                      :class="commentSort === 'newest' ? 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-xs' : 'text-gray-500 hover:text-gray-800 dark:hover:text-white'" 

                      class="px-3.5 py-1.5 rounded-xl transition flex items-center gap-1.5 cursor-pointer">

                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>

                <span>Newest</span>

              </button>

            </div>

          </div>



          <!-- Write Comment Box Container -->

          @php
            $isHoldPhotographer = auth()->check() && auth()->user()->isHoldPhotographer();
          @endphp

          @auth

            @if($isHoldPhotographer)

              <button type="button"
                      @click="openPhotographerHoldModal('menulis komentar forum')"
                      class="w-full text-left flex items-center justify-between p-4 sm:p-5 bg-gray-50/90 dark:bg-gray-800/60 border border-dashed border-gray-300 dark:border-gray-700 hover:border-amber-400 dark:hover:border-amber-500 hover:bg-amber-50/40 dark:hover:bg-amber-950/20 rounded-2xl sm:rounded-3xl transition duration-200 group cursor-pointer">

                <div class="flex items-center gap-3 sm:gap-3.5">
                  <div class="w-10 h-10 rounded-xl bg-gray-200/80 dark:bg-gray-700/80 text-gray-500 dark:text-gray-400 group-hover:text-amber-600 dark:group-hover:text-amber-400 group-hover:bg-amber-100 dark:group-hover:bg-amber-900/40 flex items-center justify-center shrink-0 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                    </svg>
                  </div>

                  <div>
                    <h4 class="text-xs sm:text-sm font-bold text-gray-800 dark:text-gray-200 group-hover:text-amber-600 dark:group-hover:text-amber-400 transition">
                      Verifikasi Akun untuk Menulis Komentar
                    </h4>
                    <p class="text-[11px] sm:text-xs text-gray-500 dark:text-gray-400">
                      Klik di sini untuk melihat status akun fotografer Anda
                    </p>
                  </div>
                </div>

                <div class="shrink-0 pl-2">
                  <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-amber-400 group-hover:bg-amber-500 text-gray-900 font-extrabold text-xs rounded-xl shadow-xs transition">
                    <span>Lihat Status</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                  </span>
                </div>

              </button>

            @else

              <form action="{{ route('public.forum.comment', $post->id) }}" method="POST"
                  x-data="{
                    comment: '',
                    touched: {{ $errors->has('comment') ? 'true' : 'false' }},
                    error: '{{ $errors->first('comment') ?: '' }}',
                    validateComment() {
                      if (!this.comment || !this.comment.trim()) {
                        this.error = 'Komentar tidak boleh kosong.';
                        return false;
                      }
                      if (this.comment.trim().length < 3) {
                        this.error = 'Komentar minimal 3 karakter.';
                        return false;
                      }
                      this.error = '';
                      return true;
                    },
                    submitComment(e) {
                      this.touched = true;
                      if (!this.validateComment()) {
                        e.preventDefault();
                        this.$refs.commentInput?.focus();
                        return false;
                      }
                      return true;
                    }
                  }"
                  @submit="submitComment($event)"
                  novalidate
                  class="flex items-start gap-3.5">

              @csrf

              <!-- User Avatar Circle (x-user-avatar: foto or initials) -->

              <x-user-avatar :user="auth()->user()" size="w-10 h-10" bg="bg-amber-400" textColor="text-gray-900" class="mt-1" />



              <!-- Spacious Input Textarea Container Box -->

              <div class="flex-1 space-y-1">
                <div class="relative bg-gray-50/80 dark:bg-gray-900/60 border rounded-3xl p-4 sm:p-5 transition focus-within:ring-2 focus-within:bg-white dark:focus-within:bg-gray-900 min-h-[105px]"
                     :class="touched && error ? 'border-red-400 focus-within:ring-red-400 focus-within:border-red-400' : 'border-gray-200 dark:border-gray-700/80 focus-within:ring-amber-500'">

                  <textarea name="comment" 
                            x-ref="commentInput"
                            x-model="comment"
                            @input="touched = true; validateComment()"
                            @blur="touched = true; validateComment()"
                            rows="2" 
                            placeholder="Tulis komentar atau tanggapan Anda di sini..." 
                            class="w-full bg-transparent border-0 outline-none resize-none text-xs sm:text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-0 pr-12 leading-relaxed"></textarea>

                  

                  <!-- Proportional Yellow Send Circle Button -->

                  <button type="submit" 

                          class="absolute right-3.5 bottom-3.5 w-8.5 h-8.5 rounded-full bg-amber-400 hover:bg-amber-500 text-gray-900 flex items-center justify-center shadow-xs hover:scale-105 transition shrink-0 cursor-pointer" 

                          title="Kirim Balasan">

                    <svg class="w-4 h-4 flex items-center justify-center" fill="currentColor" viewBox="0 0 24 24">

                      <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>

                    </svg>

                  </button>

                </div>
                <p x-show="touched && error" x-text="error" x-cloak class="text-[11px] font-bold text-red-500 pl-3"></p>
              </div>

            </form>

            @endif

          @else

            <!-- Locked Guest Comment Container (Clean & Simple Anti-AI-Slop State with Universal Modal Trigger) -->
            <button type="button" 
                    @click="handleGuestClick()" 
                    class="w-full text-left flex items-center justify-between p-4 sm:p-5 bg-gray-50/90 dark:bg-gray-800/60 border border-dashed border-gray-300 dark:border-gray-700 hover:border-amber-400 dark:hover:border-amber-500 hover:bg-amber-50/40 dark:hover:bg-amber-950/20 rounded-2xl sm:rounded-3xl transition duration-200 group cursor-pointer">
              
              <div class="flex items-center gap-3 sm:gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-gray-200/80 dark:bg-gray-700/80 text-gray-500 dark:text-gray-400 group-hover:text-amber-600 dark:group-hover:text-amber-400 group-hover:bg-amber-100 dark:group-hover:bg-amber-900/40 flex items-center justify-center shrink-0 transition">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                  </svg>
                </div>
                
                <div>
                  <h4 class="text-xs sm:text-sm font-bold text-gray-800 dark:text-gray-200 group-hover:text-amber-600 dark:group-hover:text-amber-400 transition">
                    Masuk untuk Menulis Komentar
                  </h4>
                  <p class="text-[11px] sm:text-xs text-gray-500 dark:text-gray-400">
                    Klik di sini untuk login dan berinteraksi di topik ini
                  </p>
                </div>
              </div>

              <div class="shrink-0 pl-2">
                <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-amber-400 group-hover:bg-amber-500 text-gray-900 font-extrabold text-xs rounded-xl shadow-xs transition">
                  <span>Masuk</span>
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                </span>
              </div>

            </button>

          @endauth



          <!-- Comments Timeline List (Real Dynamic MySQL Data for Likes & Dislikes) -->

          <div x-ref="commentsList" class="divide-y divide-gray-100 dark:divide-gray-700/60 pt-2">

            @forelse($post->comments as $index => $comment)

              <div class="py-4 first:pt-0 last:pb-0" 

                   data-likes="{{ $comment->likes ?? 0 }}"

                   data-time="{{ $comment->created_at->timestamp }}"

                   x-data="{ likesCount: {{ $comment->likes ?? 0 }}, dislikesCount: {{ $comment->dislikes ?? 0 }}, liked: false, disliked: false, menuOpen: false, replyOpen: false, showReplies: false }">

                

                <div class="flex items-start gap-3">

                  <!-- Avatar Circle (x-user-avatar: foto or initials) -->

                  <x-user-avatar :user="$comment->user" size="w-9 h-9 sm:w-10 sm:h-10" class="mt-0.5" />



                  <!-- Comment Main Content Block -->

                  <div class="flex-1 min-w-0">

                    <!-- Author Name, Date & Three-Dots Menu -->

                    <div class="flex items-center justify-between">

                      <div class="flex items-center gap-2 flex-wrap">

                        <h4 class="font-extrabold text-xs sm:text-sm text-gray-900 dark:text-white line-clamp-1">{{ $comment->user->nama ?? 'Anggota Komunitas' }}</h4>

                        <span class="text-[11px] font-bold text-gray-400 dark:text-gray-500">{{ $comment->created_at->format('M d, Y') }}</span>

                      </div>

                      <!-- Three-Dots Kebab Action Menu -->

                      <div class="relative" @click.away="menuOpen = false">

                        <button type="button" 

                                @click="menuOpen = !menuOpen" 

                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700/50 transition cursor-pointer"

                                title="Opsi Komentar">

                          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg>

                        </button>



                        <div x-show="menuOpen" 

                             x-cloak 

                             x-transition:enter="transition ease-out duration-100"

                             x-transition:enter-start="opacity-0 scale-95"

                             x-transition:enter-end="opacity-100 scale-100"

                             x-transition:leave="transition ease-in duration-75"

                             x-transition:leave-start="opacity-100 scale-100"

                             x-transition:leave-end="opacity-0 scale-95"

                             class="absolute right-0 mt-1 w-44 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700/80 py-1 z-30 divide-y divide-gray-100 dark:divide-gray-700/50">

                          

                          <!-- Option 1: Salin Teks -->

                          <div class="py-0.5">

                            <button type="button" 

                                    @click="copyComment({{ json_encode($comment->comment) }}); menuOpen = false;"

                                    class="w-full text-left px-3.5 py-2 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition cursor-pointer">

                              Salin Teks Komentar

                            </button>

                          </div>



                          <!-- Option 2: Laporkan Komentar -->

                          @auth

                            @if(auth()->id() !== $comment->id_user)

                              <div class="py-0.5">

                                @if($isHoldPhotographer)
                                  <button type="button"
                                          @click="menuOpen = false; openPhotographerHoldModal('melaporkan komentar forum');"
                                          class="w-full text-left px-3.5 py-2 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition cursor-pointer">

                                    Laporkan Komentar

                                  </button>
                                @else
                                  <button type="button" 

                                          @click="openReportModal({{ $comment->id }}, {{ json_encode($comment->user->nama ?? 'Anggota Komunitas') }}, {{ json_encode(Str::limit($comment->comment, 80)) }}); menuOpen = false;"

                                          class="w-full text-left px-3.5 py-2 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition cursor-pointer">

                                    Laporkan Komentar

                                  </button>
                                @endif

                              </div>

                            @endif

                          @else

                            <div class="py-0.5">

                              <button type="button" 

                                      @click="menuOpen = false; handleGuestClick();"

                                      class="w-full text-left px-3.5 py-2 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition cursor-pointer">

                                Laporkan Komentar

                              </button>

                            </div>

                          @endauth



                          <!-- Option 3: Hapus Komentar (Author or Admin) -->

                          @auth

                            @if(auth()->id() === $comment->id_user || auth()->user()->isAdmin())

                              <div class="py-0.5">

                                <button type="button" 
                                        @click="$dispatch('open-confirm-modal', {
                                            title: 'Hapus Komentar Forum?',
                                            message: 'Apakah Anda yakin ingin menghapus komentar ini dari topik diskusi?',
                                            confirmText: 'Ya, Hapus Komentar',
                                            type: 'danger',
                                            actionUrl: '{{ route('public.forum.comment.delete', $comment->id) }}',
                                            method: 'POST'
                                        })"
                                        class="w-full text-left px-3.5 py-2 text-xs font-bold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition cursor-pointer">Hapus</button>

                              </div>

                            @endif

                          @endauth



                        </div>

                      </div>

                    </div>



                    <!-- Comment Body Paragraph (Crisp YouTube Layout, No Artificial Blank Space) -->

                    @if($comment->is_hidden)

                      <div class="mt-1 p-3 rounded-xl bg-gray-50 dark:bg-gray-900/60 border border-gray-200 dark:border-gray-700 text-xs text-gray-500 dark:text-gray-400 italic leading-relaxed">

                        Komentar ini disembunyikan sementara karena menerima banyak laporan dari komunitas dan sedang ditinjau oleh admin.

                      </div>

                    @else

                      <p class="mt-0.5 text-xs sm:text-sm text-gray-800 dark:text-gray-200 leading-relaxed whitespace-pre-line">{{ trim($comment->comment) }}</p>

                    @endif



                    <!-- Bottom Action Row: Dynamic Likes, Dislikes, Reply (Crisp YouTube Spacing) -->

                    <div class="mt-1.5 flex items-center gap-4 text-xs font-semibold text-gray-500 dark:text-gray-400">

                      <!-- Real Dynamic Like Button -->

                      @auth

                        @if($isHoldPhotographer)

                          <button type="button"
                                  @click="openPhotographerHoldModal('menyukai komentar forum')"
                                  class="flex items-center gap-1.5 text-gray-600 dark:text-gray-300 hover:text-amber-500 transition cursor-pointer">

                            <svg class="w-4 h-4 text-gray-400 hover:text-amber-500" fill="currentColor" viewBox="0 0 24 24"><path d="M1 21h4V9H1v12zm22-11c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14.17 1 7.58 7.59C7.22 7.95 7 8.45 7 9v10c0 1.1.9 2 2 2h9c.83 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-2z"/></svg>

                            <span x-text="likesCount"></span>

                          </button>

                          <button type="button"
                                  @click="openPhotographerHoldModal('memberi tanda tidak suka pada komentar forum')"
                                  class="flex items-center gap-1.5 text-gray-600 dark:text-gray-300 hover:text-red-500 transition cursor-pointer">

                            <svg class="w-4 h-4 transform rotate-180 text-gray-400 hover:text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M1 21h4V9H1v12zm22-11c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14.17 1 7.58 7.59C7.22 7.95 7 8.45 7 9v10c0 1.1.9 2 2 2h9c.83 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-2z"/></svg>

                            <span x-text="dislikesCount"></span>

                          </button>

                          <button type="button"
                                  @click="openPhotographerHoldModal('membalas komentar forum')"
                                  class="flex items-center gap-1.5 hover:text-amber-500 transition cursor-pointer">

                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>

                            <span>Reply</span>

                          </button>

                        @else

                          <button type="button" 

                                  @click="if(!liked) { 

                                    likesCount++; 

                                    liked = true; 

                                    fetch('/forum/comment/{{ $comment->id }}/like', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }); 

                                  }" 

                                  :class="liked ? 'text-amber-500 font-black' : 'text-gray-600 dark:text-gray-300 hover:text-amber-500'" 

                                  class="flex items-center gap-1.5 transition">

                            <svg class="w-4 h-4" :class="liked ? 'text-amber-500' : 'text-gray-400 hover:text-amber-500'" fill="currentColor" viewBox="0 0 24 24"><path d="M1 21h4V9H1v12zm22-11c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14.17 1 7.58 7.59C7.22 7.95 7 8.45 7 9v10c0 1.1.9 2 2 2h9c.83 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-2z"/></svg>

                            <span x-text="likesCount"></span>

                          </button>



                          <button type="button" 

                                  @click="if(!disliked) { 

                                    dislikesCount++; 

                                    disliked = true; 

                                    fetch('/forum/comment/{{ $comment->id }}/dislike', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }); 

                                  }" 

                                  :class="disliked ? 'text-red-500 font-black' : 'text-gray-600 dark:text-gray-300 hover:text-red-500'" 

                                  class="flex items-center gap-1.5 transition">

                            <svg class="w-4 h-4 transform rotate-180" :class="disliked ? 'text-red-500' : 'text-gray-400 hover:text-red-500'" fill="currentColor" viewBox="0 0 24 24"><path d="M1 21h4V9H1v12zm22-11c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14.17 1 7.58 7.59C7.22 7.95 7 8.45 7 9v10c0 1.1.9 2 2 2h9c.83 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-2z"/></svg>

                            <span x-text="dislikesCount"></span>

                          </button>



                          <button type="button" @click="replyOpen = !replyOpen" class="flex items-center gap-1.5 hover:text-amber-500 transition cursor-pointer">

                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>

                            <span>Reply</span>

                          </button>

                        @endif

                      @else

                        <button type="button" @click="handleGuestClick()" class="flex items-center gap-1.5 hover:text-amber-500 transition cursor-pointer">

                          <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M1 21h4V9H1v12zm22-11c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14.17 1 7.58 7.59C7.22 7.95 7 8.45 7 9v10c0 1.1.9 2 2 2h9c.83 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-2z"/></svg>

                          <span x-text="likesCount"></span>

                        </button>



                        <button type="button" @click="handleGuestClick()" class="flex items-center gap-1.5 hover:text-red-500 transition cursor-pointer">

                          <svg class="w-4 h-4 text-gray-400 transform rotate-180" fill="currentColor" viewBox="0 0 24 24"><path d="M1 21h4V9H1v12zm22-11c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14.17 1 7.58 7.59C7.22 7.95 7 8.45 7 9v10c0 1.1.9 2 2 2h9c.83 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-2z"/></svg>

                          <span x-text="dislikesCount"></span>

                        </button>



                        <button type="button" @click="handleGuestClick()" class="flex items-center gap-1.5 hover:text-amber-500 transition cursor-pointer">

                          <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>

                          <span>Reply</span>

                        </button>

                      @endauth

                    </div>



                    <!-- Inline Reply Form (Shown when user clicks Reply) -->
                    @if(auth()->check() && !$isHoldPhotographer)
                      <div x-show="replyOpen" x-cloak class="mt-2.5">
                        <form action="{{ route('public.forum.comment', $post->id) }}" method="POST"
                              x-data="{
                                replyComment: '',
                                touched: false,
                                error: '',
                                validateReply() {
                                  if (!this.replyComment || !this.replyComment.trim()) {
                                    this.error = 'Balasan komentar tidak boleh kosong.';
                                    return false;
                                  }
                                  if (this.replyComment.trim().length < 2) {
                                    this.error = 'Balasan minimal 2 karakter.';
                                    return false;
                                  }
                                  this.error = '';
                                  return true;
                                },
                                submitReply(e) {
                                  this.touched = true;
                                  if (!this.validateReply()) {
                                    e.preventDefault();
                                    this.$refs.replyInput?.focus();
                                    return false;
                                  }
                                  return true;
                                }
                              }"
                              @submit="submitReply($event)"
                              novalidate
                              class="flex items-start gap-2.5">
                          @csrf
                          <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                          <x-user-avatar :user="auth()->user()" size="w-7 h-7 sm:w-8 sm:h-8" bg="bg-amber-400" textColor="text-gray-900" class="mt-0.5" />
                          <div class="flex-1 min-w-0">
                            <textarea name="comment" 
                                      x-ref="replyInput"
                                      x-model="replyComment"
                                      @input="touched = true; validateReply()"
                                      @blur="touched = true; validateReply()"
                                      rows="2" 
                                      placeholder="Balas komentar {{ $comment->user->nama ?? 'pengguna' }}..." 
                                      :class="touched && error ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-400 focus:border-amber-400'"
                                      class="w-full text-xs sm:text-sm p-2.5 rounded-xl border bg-gray-50 dark:bg-gray-900/50 text-gray-900 dark:text-white focus:outline-none resize-none"></textarea>
                            <p x-show="touched && error" x-text="error" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
                            <div class="mt-1 flex items-center justify-end gap-2">
                              <button type="button" @click="replyOpen = false" class="px-2.5 py-1 text-xs font-bold text-gray-500 hover:text-gray-800 dark:hover:text-gray-200 transition cursor-pointer">Batal</button>
                              <button type="submit" class="px-3 py-1 text-xs font-bold bg-amber-400 hover:bg-amber-500 text-gray-900 rounded-lg shadow-xs transition cursor-pointer">Balas</button>
                            </div>
                          </div>
                        </form>
                      </div>
                    @endif

                    @if($comment->replies && $comment->replies->count() > 0)
                      {{-- See Replies Toggle (ONLY Rendered When Comment ACTUALLY Has Child Replies) --}}
                      <div class="mt-1.5">
                        <button type="button" 
                                @click="showReplies = !showReplies" 
                                class="text-xs font-black text-amber-600 dark:text-amber-400 hover:underline flex items-center gap-1 focus:outline-none border-0 outline-none cursor-pointer">
                          <span x-text="showReplies ? '▴ Hide Replies' : '▾ See Replies (' + {{ $comment->replies->count() }} + ')'">▾ See Replies ({{ $comment->replies->count() }})</span>
                        </button>
                      </div>

                      <!-- Nested Child Replies List -->
                      <div x-show="showReplies" x-cloak class="mt-2.5 ml-2 sm:ml-4 pl-3.5 border-l-2 border-gray-100 dark:border-gray-700 space-y-3">
                        @foreach($comment->replies as $reply)
                          <div class="pt-2 first:pt-0" 
                               x-data="{ replyLikes: {{ $reply->likes ?? 0 }}, replyDislikes: {{ $reply->dislikes ?? 0 }}, rLiked: false, rDisliked: false, rMenuOpen: false }">
                            <div class="flex items-start gap-2.5">
                              <x-user-avatar :user="$reply->user" size="w-7 h-7 sm:w-8 sm:h-8" class="mt-0.5" />
                              <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                  <div class="flex items-center gap-2 flex-wrap">
                                    <h5 class="font-extrabold text-xs text-gray-900 dark:text-white line-clamp-1">{{ $reply->user->nama ?? 'Anggota Komunitas' }}</h5>
                                    <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500">{{ $reply->created_at->format('M d, Y') }}</span>
                                  </div>
                                  
                                  <!-- Three-dots kebab menu for reply -->
                                  <div class="relative" @click.away="rMenuOpen = false">
                                    <button type="button" @click="rMenuOpen = !rMenuOpen" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700/50 transition cursor-pointer">
                                      <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg>
                                    </button>
                                    <div x-show="rMenuOpen" x-cloak class="absolute right-0 mt-1 w-40 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700/80 py-1 z-30 divide-y divide-gray-100 dark:divide-gray-700/50">
                                      <div class="py-0.5">
                                        <button type="button" @click="copyComment({{ json_encode($reply->comment) }}); rMenuOpen = false;" class="w-full text-left px-3 py-1.5 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition cursor-pointer">Salin Teks</button>
                                      </div>
                                      @auth
                                        @if(auth()->id() !== $reply->id_user)
                                          <div class="py-0.5">
                                            @if($isHoldPhotographer)
                                              <button type="button" @click="rMenuOpen = false; openPhotographerHoldModal('melaporkan komentar forum');" class="w-full text-left px-3 py-1.5 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition cursor-pointer">Laporkan</button>
                                            @else
                                              <button type="button" @click="openReportModal({{ $reply->id }}, {{ json_encode($reply->user->nama ?? 'Anggota Komunitas') }}, {{ json_encode(Str::limit($reply->comment, 80)) }}); rMenuOpen = false;" class="w-full text-left px-3 py-1.5 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition cursor-pointer">Laporkan</button>
                                            @endif
                                          </div>
                                        @else
                                          <div class="py-0.5">
                                            <button type="button" @click="$dispatch('open-confirm-modal', { title: 'Hapus Balasan?', message: 'Apakah Anda yakin ingin menghapus balasan komentar ini?', confirmText: 'Ya, Hapus Balasan', type: 'danger', actionUrl: '{{ route('public.forum.comment.delete', $reply->id) }}', method: 'POST' })" class="w-full text-left px-3 py-1.5 text-xs font-bold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition cursor-pointer">Hapus</button>
                                          </div>
                                        @endif
                                      @else
                                        <div class="py-0.5">
                                          <button type="button" @click="rMenuOpen = false; handleGuestClick();" class="w-full text-left px-3 py-1.5 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition cursor-pointer">Laporkan</button>
                                        </div>
                                      @endauth
                                    </div>
                                  </div>
                                </div>

                                <!-- Reply Comment Text -->
                                @if($reply->is_hidden)
                                  <div class="mt-0.5 p-2 rounded-lg bg-gray-50 dark:bg-gray-900/60 border border-gray-200 dark:border-gray-700 text-[11px] text-gray-500 italic">
                                    Komentar ini disembunyikan sementara.
                                  </div>
                                @else
                                  <p class="mt-0.5 text-xs text-gray-800 dark:text-gray-200 leading-relaxed whitespace-pre-line">{{ trim($reply->comment) }}</p>
                                @endif

                                <!-- Reply Action Row (Likes, Dislikes, Reply back) -->
                                <div class="mt-1 flex items-center gap-3.5 text-[11px] font-semibold text-gray-500 dark:text-gray-400">
                                  @auth
                                    @if($isHoldPhotographer)
                                      <button type="button"
                                              @click="openPhotographerHoldModal('menyukai komentar forum')"
                                              class="flex items-center gap-1 text-gray-600 dark:text-gray-300 hover:text-amber-500 transition cursor-pointer">
                                        <svg class="w-3.5 h-3.5 text-gray-400 hover:text-amber-500" fill="currentColor" viewBox="0 0 24 24"><path d="M1 21h4V9H1v12zm22-11c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14.17 1 7.58 7.59C7.22 7.95 7 8.45 7 9v10c0 1.1.9 2 2 2h9c.83 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-2z"/></svg>
                                        <span x-text="replyLikes"></span>
                                      </button>
                                      <button type="button"
                                              @click="openPhotographerHoldModal('memberi tanda tidak suka pada komentar forum')"
                                              class="flex items-center gap-1 text-gray-600 dark:text-gray-300 hover:text-red-500 transition cursor-pointer">
                                        <svg class="w-3.5 h-3.5 transform rotate-180 text-gray-400 hover:text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M1 21h4V9H1v12zm22-11c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14.17 1 7.58 7.59C7.22 7.95 7 8.45 7 9v10c0 1.1.9 2 2 2h9c.83 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-2z"/></svg>
                                        <span x-text="replyDislikes"></span>
                                      </button>
                                      <button type="button" @click="openPhotographerHoldModal('membalas komentar forum')" class="flex items-center gap-1 hover:text-amber-500 transition cursor-pointer">
                                        <span>Reply</span>
                                      </button>
                                    @else
                                      <button type="button" 
                                              @click="if(!rLiked) { replyLikes++; rLiked = true; fetch('/forum/comment/{{ $reply->id }}/like', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }); }" 
                                              :class="rLiked ? 'text-amber-500 font-black' : 'text-gray-600 dark:text-gray-300 hover:text-amber-500'" 
                                              class="flex items-center gap-1 transition cursor-pointer">
                                        <svg class="w-3.5 h-3.5" :class="rLiked ? 'text-amber-500' : 'text-gray-400 hover:text-amber-500'" fill="currentColor" viewBox="0 0 24 24"><path d="M1 21h4V9H1v12zm22-11c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14.17 1 7.58 7.59C7.22 7.95 7 8.45 7 9v10c0 1.1.9 2 2 2h9c.83 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-2z"/></svg>
                                        <span x-text="replyLikes"></span>
                                      </button>
                                      <button type="button" 
                                              @click="if(!rDisliked) { replyDislikes++; rDisliked = true; fetch('/forum/comment/{{ $reply->id }}/dislike', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }); }" 
                                              :class="rDisliked ? 'text-red-500 font-black' : 'text-gray-600 dark:text-gray-300 hover:text-red-500'" 
                                              class="flex items-center gap-1 transition cursor-pointer">
                                        <svg class="w-3.5 h-3.5 transform rotate-180" :class="rDisliked ? 'text-red-500' : 'text-gray-400 hover:text-red-500'" fill="currentColor" viewBox="0 0 24 24"><path d="M1 21h4V9H1v12zm22-11c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14.17 1 7.58 7.59C7.22 7.95 7 8.45 7 9v10c0 1.1.9 2 2 2h9c.83 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-2z"/></svg>
                                        <span x-text="replyDislikes"></span>
                                      </button>
                                      <button type="button" @click="replyOpen = true" class="flex items-center gap-1 hover:text-amber-500 transition cursor-pointer">
                                        <span>Reply</span>
                                      </button>
                                    @endif
                                  @else
                                    <button type="button" @click="handleGuestClick()" class="flex items-center gap-1 hover:text-amber-500 transition cursor-pointer">
                                      <svg class="w-3.5 h-3.5 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M1 21h4V9H1v12zm22-11c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14.17 1 7.58 7.59C7.22 7.95 7 8.45 7 9v10c0 1.1.9 2 2 2h9c.83 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-2z"/></svg>
                                      <span x-text="replyLikes"></span>
                                    </button>
                                    <button type="button" @click="handleGuestClick()" class="flex items-center gap-1 hover:text-red-500 transition cursor-pointer">
                                      <svg class="w-3.5 h-3.5 text-gray-400 transform rotate-180" fill="currentColor" viewBox="0 0 24 24"><path d="M1 21h4V9H1v12zm22-11c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14.17 1 7.58 7.59C7.22 7.95 7 8.45 7 9v10c0 1.1.9 2 2 2h9c.83 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-2z"/></svg>
                                      <span x-text="replyDislikes"></span>
                                    </button>
                                    <button type="button" @click="handleGuestClick()" class="flex items-center gap-1 hover:text-amber-500 transition cursor-pointer">
                                      <span>Reply</span>
                                    </button>
                                  @endauth
                                </div>
                              </div>
                            </div>
                          </div>
                        @endforeach
                      </div>
                    @endif



                  </div>

                </div>



              </div>

            @empty

              <div class="py-12 text-center text-xs text-gray-400 font-bold">

                Belum ada balasan komentar pada topik ini. Jadilah yang pertama memberikan tanggapan!

              </div>

            @endforelse

          </div>



          <!-- Report Comment Modal (Rule #59 Compliant: Centered Title without Icon, Centered 2 Action Buttons, No 'X' Close Button) -->

          <div x-show="reportModalOpen" 

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

            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="reportModalOpen = false"></div>

            <div class="flex min-h-full items-center justify-center p-4">

              <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 text-center shadow-xl transition-all sm:my-8 w-full max-w-[460px] border border-gray-200 dark:border-gray-700 p-6 sm:p-8 flex flex-col gap-5">

                

                <!-- Centered Header without Icon (Rule #59) -->

                <div class="text-center flex flex-col gap-1">

                  <h3 class="text-base sm:text-lg font-black text-gray-900 dark:text-white">Laporkan Komentar</h3>

                  <p class="text-xs text-gray-500 dark:text-gray-400">

                    Bantu kami menjaga ruang diskusi komunitas tetap aman dan nyaman.

                  </p>

                </div>



                <!-- Comment Snippet Preview -->

                <div class="p-3.5 rounded-xl bg-gray-50 dark:bg-gray-900/60 border border-gray-100 dark:border-gray-700/60 text-left space-y-1">

                  <span class="text-[11px] font-bold text-gray-400 dark:text-gray-500 block" x-text="'Penulis: ' + reportAuthor"></span>

                  <p class="text-xs text-gray-700 dark:text-gray-300 italic line-clamp-2" x-text="'“' + reportSnippet + '”'"></p>

                </div>



                <!-- Report Form -->

                <form :action="reportActionUrl" method="POST" @submit="submitReport($event)" novalidate class="flex flex-col gap-4">

                  @csrf



                  <!-- Violation Category Radios -->

                  <div class="flex flex-col gap-2 text-left">

                    <label class="text-xs font-bold text-gray-700 dark:text-gray-300 block">

                      Kategori Pelanggaran <span class="text-rose-500">*</span>

                    </label>

                    <div class="space-y-1.5 text-xs text-gray-700 dark:text-gray-300">

                      <label class="flex items-center gap-2.5 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/40 cursor-pointer">

                        <input type="radio" name="kategori" value="spam" x-model="reportKategori" @change="reportKategori = 'spam'; reportTouched = true; validateReportKategori()" class="text-amber-500 accent-amber-500 focus:ring-amber-400">

                        <span>Spam atau Promosi Tidak Relevan</span>

                      </label>

                      <label class="flex items-center gap-2.5 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/40 cursor-pointer">

                        <input type="radio" name="kategori" value="harassment" x-model="reportKategori" @change="reportKategori = 'harassment'; reportTouched = true; validateReportKategori()" class="text-amber-500 accent-amber-500 focus:ring-amber-400">

                        <span>Pelecehan atau Ujaran Kebencian</span>

                      </label>

                      <label class="flex items-center gap-2.5 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/40 cursor-pointer">

                        <input type="radio" name="kategori" value="inappropriate" x-model="reportKategori" @change="reportKategori = 'inappropriate'; reportTouched = true; validateReportKategori()" class="text-amber-500 accent-amber-500 focus:ring-amber-400">

                        <span>Konten Tidak Pantas atau Asusila</span>

                      </label>

                      <label class="flex items-center gap-2.5 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/40 cursor-pointer">

                        <input type="radio" name="kategori" value="misinformation" x-model="reportKategori" @change="reportKategori = 'misinformation'; reportTouched = true; validateReportKategori()" class="text-amber-500 accent-amber-500 focus:ring-amber-400">

                        <span>Informasi Menyesatkan atau Penipuan</span>

                      </label>

                      <label class="flex items-center gap-2.5 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/40 cursor-pointer">

                        <input type="radio" name="kategori" value="other" x-model="reportKategori" @change="reportKategori = 'other'; reportTouched = true; validateReportKategori()" class="text-amber-500 accent-amber-500 focus:ring-amber-400">

                        <span>Lainnya</span>

                      </label>

                    </div>
                    <p x-show="reportTouched && reportError" x-text="reportError" x-cloak class="text-[11px] font-bold text-red-500"></p>

                  </div>



                  <!-- Optional Notes Textarea -->

                  <div class="flex flex-col gap-1.5 text-left">

                    <label class="text-xs font-bold text-gray-700 dark:text-gray-300 block">Catatan Tambahan (Opsional)</label>

                    <textarea name="alasan" rows="2" placeholder="Berikan rincian singkat alasan laporan jika diperlukan..." class="w-full text-xs p-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-400 resize-none"></textarea>

                  </div>



                  <!-- Modal Action Buttons (Centered, Max 2 Buttons, Gap-3, No 'X' Button - Rule #59) -->

                  <div class="flex items-center justify-center gap-3">

                    <button type="button" @click="reportModalOpen = false" class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold text-xs hover:bg-gray-100 dark:hover:bg-gray-600 transition cursor-pointer">

                      Batal

                    </button>

                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs shadow-xs transition cursor-pointer">

                      Kirim Laporan

                    </button>

                  </div>

                </form>



              </div>

            </div>

          </div>



          <!-- Clean Login Requirement Modal (Clean Action Buttons Only) -->

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

              <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 text-center shadow-xl transition-all sm:my-8 w-full max-w-[420px] border border-gray-200 dark:border-gray-700 p-7 sm:p-8 flex flex-col gap-4">

                

                <!-- Plain Close Button Absolute Top Right (SVG Vector per Rule #6 & #59) -->

                <button type="button" @click="closeModal()" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 dark:hover:text-white transition cursor-pointer p-1">

                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>

                </button>



                <!-- Header without Icon & Centered -->

                <div class="text-center">

                  <h3 class="text-base sm:text-lg font-black text-gray-900 dark:text-white">Login Diperlukan</h3>

                </div>



                <!-- Body Text (Centered) -->

                <p class="text-xs sm:text-[13px] text-gray-600 dark:text-gray-300 leading-relaxed text-center">

                  Untuk dapat menuliskan balasan komentar atau berinteraksi di forum ini, Anda harus masuk ke akun LensMatch terlebih dahulu.

                </p>



                <!-- Action Button -->

                <div>

                  <a href="{{ route('login', ['redirect' => request()->fullUrl()]) }}" class="w-full py-3 px-4 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs sm:text-sm rounded-xl shadow-xs transition hover:scale-[1.01] flex items-center justify-center cursor-pointer">

                    Masuk ke Akun

                  </a>

                </div>



              </div>

            </div>

          </div>



        </div>



      </div>



      <!-- Right Sidebar Column (4 Cols) -->

      <div class="lg:col-span-4 space-y-6">

        

        <!-- Sidebar Widget 1: Kategori Populer -->

        <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-4">

          <h3 class="text-xs font-black uppercase tracking-wider text-gray-900 dark:text-white border-l-4 border-amber-500 pl-2">

            Kategori Populer

          </h3>



          <div class="divide-y divide-gray-100 dark:divide-gray-700/60 text-xs">

            @foreach($categoriesList as $catItem)

              <a href="{{ route('public.forum.index', ['cat' => $catItem['slug']]) }}" class="py-3 flex items-center justify-between text-gray-700 dark:text-gray-300 hover:text-amber-500 transition font-bold">

                <span>{{ $catItem['name'] }}</span>

                <span class="text-xs font-black text-gray-400 dark:text-gray-500">

                  {{ $catItem['count'] }}

                </span>

              </a>

            @endforeach

          </div>

        </div>



        <!-- Sidebar Widget 2: Forum Lainnya -->

        <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-4">

          <h3 class="text-xs font-black uppercase tracking-wider text-gray-900 dark:text-white border-l-4 border-amber-500 pl-2">

            Forum Lainnya

          </h3>



          <div class="divide-y divide-gray-100 dark:divide-gray-700/60 text-xs space-y-1">

            @foreach($relatedPosts as $relPost)

              <a href="{{ route('public.forum.show', $relPost->id) }}" class="py-3 block space-y-1 group">

                <h4 class="font-extrabold text-xs text-gray-900 dark:text-white group-hover:text-amber-500 transition line-clamp-2 leading-snug">

                  {{ $relPost->judul }}

                </h4>

                <p class="text-[10px] text-gray-400 flex items-center gap-1">

                  <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>

                  <span>{{ $relPost->created_at->format('d F Y H:i') }}</span>

                </p>

              </a>

            @endforeach

          </div>

        </div>



      </div>



    </div>



  </div>

</section>

@endsection