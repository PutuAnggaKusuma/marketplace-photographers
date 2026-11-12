@extends('layouts.app')

@section('title', $photographer->nama . ' — Profil & Price List Fotografer LensMatch')

@section('content')
@php
  $lowestPrice = $photographer->services->whereNotNull('tarif_harga')->where('tarif_harga', '>', 0)->min('tarif_harga');
  $validServices = $photographer->services->whereNotNull('tarif_harga')->where('tarif_harga', '>', 0);
  $avatarUrl = $photographer->foto_url ?? ($photographer->foto ? (str_starts_with($photographer->foto, 'http') ? $photographer->foto : asset('storage/' . $photographer->foto)) : asset('images/avatar-default.png'));
  $isHoldPhotographer = auth()->check() && auth()->user()->isHoldPhotographer();
  $isClientUser = auth()->check() && auth()->user()->isClient();
@endphp

<!-- Page Wrapper with Alpine State (Rule #66, #78, #86 Compliant) -->
<div class="bg-gray-50/70 dark:bg-gray-900 min-h-screen py-8 lg:py-12"
     x-data="{
       activeTab: 'services',
       lightboxOpen: false,
       lightboxSrc: '',
       lightboxTitle: '',
       lightboxIndex: 0,
       lightboxImages: [],
       openLightbox(src, title, index = 0, images = []) {
         this.lightboxSrc = src;
         this.lightboxTitle = title;
         this.lightboxIndex = index;
         this.lightboxImages = images.length ? images : [src];
         this.lightboxOpen = true;
       },
       nextImage() {
         if (!this.lightboxImages.length) return;
         this.lightboxIndex = (this.lightboxIndex + 1) % this.lightboxImages.length;
         this.lightboxSrc = this.lightboxImages[this.lightboxIndex];
       },
       prevImage() {
         if (!this.lightboxImages.length) return;
         this.lightboxIndex = (this.lightboxIndex - 1 + this.lightboxImages.length) % this.lightboxImages.length;
         this.lightboxSrc = this.lightboxImages[this.lightboxIndex];
       },
       loginModal: false,
       loginModalMessage: '',
       loginRedirectUrl: '',
       openLoginModal(message, redirectUrl) {
         this.loginModalMessage = message;
         this.loginRedirectUrl = redirectUrl;
         this.loginModal = true;
       },
       closeLoginModal() {
         this.loginModal = false;
       }
     }"
     @open-login-modal.window="openLoginModal($event.detail.message, $event.detail.redirectUrl)">

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 sm:space-y-8">

    <!-- Top Navigation / Back Button (Rule #72: Modern Single Chevron, No Stem Arrow, No Breadcrumb) -->
    <div>
      <a href="{{ route('public.photographers.katalog') }}" 
         class="inline-flex items-center gap-2 text-xs sm:text-sm font-bold text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
        </svg>
        <span>Kembali ke Katalog</span>
      </a>
    </div>

    <!-- Main Profile Master Card (Refined Proportions & Micro-Typography Harmonized with Reservation Page) -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl sm:rounded-3xl p-6 sm:p-8 lg:p-9 border border-gray-200/80 dark:border-gray-700/80 shadow-none space-y-8 sm:space-y-10">
      
      <!-- Top Row: Photo + Editorial Creator Header -->
      <div class="flex flex-col md:flex-row md:items-start gap-6 sm:gap-8 lg:gap-9">
        
        <!-- Left: Portrait Photo (Refined 4:5 Aspect Ratio, Compact & Proportional) -->
        <div class="w-full sm:w-[200px] md:w-[220px] lg:w-[240px] aspect-[4/5] rounded-2xl overflow-hidden bg-gray-100 dark:bg-gray-700 shrink-0 border border-gray-200/80 dark:border-gray-700/80 shadow-none relative">
          <img onerror="this.onerror=null;this.src=window.SVG_IMAGE_FALLBACK;" 
               src="{{ $avatarUrl }}" 
               alt="{{ $photographer->nama }}" 
               draggable="false"
               oncontextmenu="return false;"
               class="w-full h-full object-cover select-none">
        </div>

        <!-- Right: Editorial Identity, Inline Trust Signals & Commercial CTA -->
        <div class="flex-1 min-w-0 flex flex-col justify-between space-y-5">
          
          <!-- Identity & Reputation Line -->
          <div class="space-y-2.5">
            <!-- Photographer Name -->
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
              {{ $photographer->nama }}
            </h1>

            <!-- Categories / Specialization Tagline -->
            @if($photographer->categories && $photographer->categories->isNotEmpty())
              <p class="text-xs sm:text-sm font-bold text-amber-600 dark:text-amber-400 tracking-wide">
                Spesialisasi: {{ $photographer->categories->pluck('nama_kategori')->join(', ') }}
              </p>
            @endif

            <!-- Inline Trust Signals Bar (Human-Crafted: Micro-Typography) -->
            <div class="flex flex-wrap items-center gap-y-1.5 gap-x-3.5 sm:gap-x-4 text-xs sm:text-[13px] text-gray-600 dark:text-gray-300 font-medium pt-0.5">
              <div class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                <span class="font-bold text-gray-900 dark:text-white">{{ $photographer->city ? ucwords(strtolower($photographer->city->name)) : ($photographer->alamat ?: 'Indonesia') }}</span>
              </div>
              <span class="text-gray-300 dark:text-gray-700 select-none">&bull;</span>
              <div class="flex items-center gap-1">
                <span class="{{ $totalReviews > 0 ? 'text-amber-400' : 'text-gray-300 dark:text-gray-600' }} text-sm">★</span>
                <span class="font-black text-gray-900 dark:text-white">{{ $avgRating }}</span>
                <span class="text-gray-400">({{ $totalReviews }} ulasan)</span>
              </div>
              <span class="text-gray-300 dark:text-gray-700 select-none">&bull;</span>
              <div>
                <span class="font-bold text-gray-900 dark:text-white">{{ $completedSessionsCount ?? 0 }}</span>
                <span class="text-gray-500">Sesi Selesai</span>
              </div>
              <span class="text-gray-300 dark:text-gray-700 select-none">&bull;</span>
              <div>
                <span class="font-bold text-gray-900 dark:text-white">{{ $photographer->portfolios->count() }}</span>
                <span class="text-gray-500">Portofolio Karya</span>
              </div>
            </div>

            <!-- Bio Naratif -->
            @if(!empty(trim($photographer->deskripsi_bio)))
              <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300 leading-relaxed max-w-2xl pt-1">
                {{ $photographer->deskripsi_bio }}
              </p>
            @endif
          </div>

          <!-- Commercial Anchor: Starting Price & Action Buttons Together (Zero Dividers, Pure Flow) -->
          <div class="pt-3 flex flex-wrap items-center justify-between gap-4 mt-auto">
            <!-- Starting Price -->
            <div class="space-y-0.5">
              <span class="text-[10px] sm:text-[11px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider block">Mulai Dari</span>
              <div class="flex items-baseline gap-1">
                @if($lowestPrice)
                  <span class="text-xl sm:text-2xl font-black text-gray-900 dark:text-white tracking-tight">
                    Rp {{ number_format($lowestPrice, 0, ',', '.') }}
                  </span>
                  <span class="text-xs font-normal text-gray-400">/ sesi pemotretan</span>
                @else
                  <span class="text-xs sm:text-sm font-semibold text-gray-400 dark:text-gray-500 italic">
                    Belum ada paket layanan
                  </span>
                @endif
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2.5 sm:gap-3">
              @auth
                @if($isHoldPhotographer)
                  <button type="button"
                          @click="openPhotographerHoldModal('mereservasi sesi pemotretan')"
                          class="px-6 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs sm:text-sm text-center shadow-none transition cursor-pointer">
                    Reservasi
                  </button>

                  <button type="button"
                          @click="openPhotographerHoldModal('mengirim pesan chat')"
                          class="px-4.5 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-650 text-gray-900 dark:text-white font-bold text-xs sm:text-sm text-center transition cursor-pointer inline-flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <span>Chat</span>
                  </button>
                @elseif(!$isClientUser)
                  <button type="button"
                          @click="openClientOnlyModal('reservasi sesi pemotretan')"
                          class="px-6 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs sm:text-sm text-center shadow-none transition cursor-pointer">
                    Reservasi
                  </button>

                  <button type="button"
                          @click="openClientOnlyModal('chat dengan fotografer')"
                          class="px-4.5 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-650 text-gray-900 dark:text-white font-bold text-xs sm:text-sm text-center transition cursor-pointer inline-flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <span>Chat</span>
                  </button>
                @else
                  <a href="{{ url('/booking/create?photographer_id=' . $photographer->id) }}" 
                     class="px-6 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs sm:text-sm text-center shadow-none transition cursor-pointer">
                    Reservasi
                  </a>

                  <a href="{{ route('chat.start', $photographer->id) }}" 
                     class="px-4.5 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-650 text-gray-900 dark:text-white font-bold text-xs sm:text-sm text-center transition cursor-pointer inline-flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <span>Chat</span>
                  </a>
                @endif
              @else
                <button type="button" 
                        @click="$dispatch('open-login-modal', { message: 'Untuk dapat melakukan reservasi sesi pemotretan dengan fotografer ini, Anda harus masuk ke akun LensMatch terlebih dahulu.', redirectUrl: '{{ url('/booking/create?photographer_id=' . $photographer->id) }}' })"
                        class="px-6 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs sm:text-sm text-center shadow-none transition cursor-pointer">
                  Reservasi
                </button>

                <button type="button" 
                        @click="$dispatch('open-login-modal', { message: 'Untuk dapat mengirim pesan dan mendiskusikan konsep pemotretan dengan fotografer ini, Anda harus masuk ke akun LensMatch terlebih dahulu.', redirectUrl: '{{ route('chat.start', $photographer->id) }}' })"
                        class="px-4.5 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-650 text-gray-900 dark:text-white font-bold text-xs sm:text-sm text-center transition cursor-pointer inline-flex items-center justify-center gap-1.5">
                  <svg class="w-4 h-4 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                  </svg>
                  <span>Chat</span>
                </button>
              @endauth

              <!-- Bookmark Favorit Icon Button (Rule #93 Compliant with Live Toast) -->
              <div x-data="{ isFavorited: {{ ($isFavorited ?? false) ? 'true' : 'false' }}, loading: false }" class="shrink-0">
                <button type="button" 
                        @click="
                          if (!{{ Auth::check() ? 'true' : 'false' }}) {
                            $dispatch('open-login-modal', {
                              message: 'Untuk dapat menyimpan fotografer ini ke daftar favorit Anda, Anda harus masuk ke akun LensMatch terlebih dahulu.',
                              redirectUrl: '{{ url()->full() }}'
                            });
                            return;
                          }
                          @if($isHoldPhotographer)
                            openPhotographerHoldModal('menyimpan fotografer ke favorit');
                            return;
                          @elseif(auth()->check() && !$isClientUser)
                            openClientOnlyModal('simpan fotografer ke favorit');
                            return;
                          @endif
                          if (loading) return;
                          loading = true;
                          fetch('{{ route('client.favorites.toggle', $photographer->id) }}', {
                            method: 'POST',
                            headers: {
                              'X-CSRF-TOKEN': '{{ csrf_token() }}',
                              'Accept': 'application/json',
                              'Content-Type': 'application/json',
                              'X-Requested-With': 'XMLHttpRequest'
                            }
                          })
                          .then(async res => {
                            const data = await res.json().catch(() => ({}));
                            if (!res.ok) {
                              throw new Error(data.message || 'Gagal memproses favorit.');
                            }
                            return data;
                          })
                          .then(data => {
                            loading = false;
                            if (data.success) {
                              isFavorited = data.is_favorited;
                              window.dispatchEvent(new CustomEvent('show-toast', { 
                                detail: { 
                                  message: data.message, 
                                  type: 'success' 
                                } 
                              }));
                            }
                          })
                          .catch(err => {
                            loading = false;
                            window.dispatchEvent(new CustomEvent('show-toast', { 
                              detail: { 
                                message: err.message || 'Terjadi kesalahan sistem.', 
                                type: 'warning' 
                              } 
                            }));
                          });
                        "
                        :title="isFavorited ? 'Tersimpan di Favorit (Klik untuk menghapus)' : 'Simpan ke Favorit'"
                        class="w-10 h-10 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center justify-center transition shadow-none cursor-pointer focus:outline-none focus:ring-0"
                        :class="isFavorited ? 'text-amber-600 dark:text-amber-400 border-amber-300 dark:border-amber-800' : 'text-gray-700 dark:text-gray-300'">
                  <svg class="w-4.5 h-4.5 transition-colors" :class="isFavorited ? 'fill-current text-amber-500' : 'fill-none stroke-current stroke-2'" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z" />
                  </svg>
                </button>
              </div>
            </div>
          </div>

        </div>

      </div>

      <!-- Tab Navigation & Content Section -->
      <div class="space-y-6 sm:space-y-8 pt-4 sm:pt-6">

        <!-- Tab Navigation (Clean Underline Style, No Box Backgrounds) -->
        <div class="flex items-center gap-6 sm:gap-8 overflow-x-auto pb-2">
          <button type="button" 
                  @click="activeTab = 'services'" 
                  class="py-2 text-xs sm:text-sm transition flex items-center gap-2 cursor-pointer shrink-0 focus:outline-none focus:ring-0"
                  :class="activeTab === 'services' ? 'text-amber-600 dark:text-amber-400 font-black' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-bold'">
            <span :class="activeTab === 'services' ? 'underline underline-offset-8 decoration-2 decoration-amber-500 dark:decoration-amber-400' : ''">Paket Layanan</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-black" 
                  :class="activeTab === 'services' ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'">
              {{ $validServices->count() }}
            </span>
          </button>

          <button type="button" 
                  @click="activeTab = 'portfolio'" 
                  class="py-2 text-xs sm:text-sm transition flex items-center gap-2 cursor-pointer shrink-0 focus:outline-none focus:ring-0"
                  :class="activeTab === 'portfolio' ? 'text-amber-600 dark:text-amber-400 font-black' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-bold'">
            <span :class="activeTab === 'portfolio' ? 'underline underline-offset-8 decoration-2 decoration-amber-500 dark:decoration-amber-400' : ''">Galeri Portofolio</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-black" 
                  :class="activeTab === 'portfolio' ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'">
              {{ $photographer->portfolios->count() }}
            </span>
          </button>

          <button type="button" 
                  @click="activeTab = 'availability'" 
                  class="py-2 text-xs sm:text-sm transition flex items-center gap-2 cursor-pointer shrink-0 focus:outline-none focus:ring-0"
                  :class="activeTab === 'availability' ? 'text-amber-600 dark:text-amber-400 font-black' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-bold'">
            <span :class="activeTab === 'availability' ? 'underline underline-offset-8 decoration-2 decoration-amber-500 dark:decoration-amber-400' : ''">Kalender Ketersediaan</span>
          </button>

          <button type="button" 
                  @click="activeTab = 'reviews'" 
                  class="py-2 text-xs sm:text-sm transition flex items-center gap-2 cursor-pointer shrink-0 focus:outline-none focus:ring-0"
                  :class="activeTab === 'reviews' ? 'text-amber-600 dark:text-amber-400 font-black' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-bold'">
            <span :class="activeTab === 'reviews' ? 'underline underline-offset-8 decoration-2 decoration-amber-500 dark:decoration-amber-400' : ''">Ulasan Klien</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-black" 
                  :class="activeTab === 'reviews' ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'">
              {{ $totalReviews }}
            </span>
          </button>

          <button type="button" 
                  @click="activeTab = 'faqs'" 
                  class="py-2 text-xs sm:text-sm transition flex items-center gap-2 cursor-pointer shrink-0 focus:outline-none focus:ring-0"
                  :class="activeTab === 'faqs' ? 'text-amber-600 dark:text-amber-400 font-black' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-bold'">
            <span :class="activeTab === 'faqs' ? 'underline underline-offset-8 decoration-2 decoration-amber-500 dark:decoration-amber-400' : ''">Tanya Jawab</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-black" 
                  :class="activeTab === 'faqs' ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'">
              {{ count($faqs ?? []) + count($systemFaqs ?? []) }}
            </span>
          </button>
        </div>

        <!-- TAB 1: PAKET LAYANAN (Harmonized with Reservation Page Aesthetics) -->
        <div x-show="activeTab === 'services'" class="space-y-6">
          <div>
            <h2 class="text-base sm:text-lg font-black text-gray-900 dark:text-white tracking-tight">Paket Layanan</h2>
          </div>
          
          <div class="space-y-6 sm:space-y-8">
            @forelse($validServices as $srv)
              <div class="bg-white dark:bg-gray-800 p-6 sm:p-7 lg:p-8 rounded-2xl border border-gray-200/80 dark:border-gray-700/80 shadow-none hover:border-amber-400 dark:hover:border-amber-500 transition">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">
                  
                  <!-- Kolom Kiri: Identitas Paket, Deskripsi & Tarif Sesi -->
                  <div class="lg:col-span-5 space-y-4">
                    <div class="space-y-1.5">
                      <span class="text-[10px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400 block">Paket Sesi</span>
                      <h3 class="text-base sm:text-lg font-black text-gray-900 dark:text-white leading-snug">
                        {{ $srv->nama_layanan }}
                      </h3>
                      <p class="text-xs sm:text-[13px] text-gray-500 dark:text-gray-400 leading-relaxed">
                        {{ $srv->deskripsi_layanan }}
                      </p>
                    </div>

                    <div class="pt-1">
                      <span class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider block">Tarif Paket Sesi</span>
                      <p class="text-xl sm:text-2xl font-black text-gray-900 dark:text-white mt-0.5">
                        Rp {{ number_format((float) ($srv->tarif_harga ?? 0), 0, ',', '.') }}
                        <span class="text-xs font-normal text-gray-400 ml-1">/ sesi pemotretan</span>
                      </p>
                    </div>
                  </div>

                  <!-- Kolom Kanan: Rincian Fasilitas Layanan Termasuk (Grid 2 Kolom) -->
                  <div class="lg:col-span-7 space-y-3">
                    <span class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 block">
                      Fasilitas Layanan Termasuk:
                    </span>

                    @if($srv->details && $srv->details->isNotEmpty())
                      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-3.5">
                        @foreach($srv->details as $det)
                          <div class="flex items-start gap-2.5">
                            <div class="mt-0.5 text-emerald-500 shrink-0">
                              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                            </div>
                            <div class="min-w-0">
                              <p class="text-xs text-gray-700 dark:text-gray-300 font-medium leading-tight">
                                {{ $det->nama_fitur }}
                              </p>
                              @if($det->tarif_harga > 0)
                                <p class="text-[11px] font-semibold text-amber-600 dark:text-amber-400 mt-0.5">
                                  +Rp {{ number_format($det->tarif_harga, 0, ',', '.') }}
                                </p>
                              @endif
                            </div>
                          </div>
                        @endforeach
                      </div>
                    @else
                      <p class="text-xs text-gray-400 italic">Rincian benefit fasilitas dikonfirmasi langsung saat reservasi.</p>
                    @endif
                  </div>

                </div>
              </div>
            @empty
              <div class="col-span-full bg-white dark:bg-gray-800 p-8 sm:p-10 text-center rounded-2xl border border-gray-200/80 dark:border-gray-700/80 shadow-none space-y-2">
                <h4 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white">Belum Ada Paket Layanan</h4>
                <p class="text-xs text-gray-400">Fotografer ini belum mempublikasikan paket layanan harga.</p>
              </div>
            @endforelse
          </div>
        </div>

        <!-- TAB 2: GALERI PORTOFOLIO (Anti-AI Slop: Stacked Album Cards, Refined Micro-Scale) -->
        <div x-show="activeTab === 'portfolio'" class="space-y-6">
          <div>
            <h2 class="text-base sm:text-lg font-black text-gray-900 dark:text-white tracking-tight">Galeri Portofolio</h2>
          </div>

          <!-- Loop Each Distinct Portfolio Album -->
          <div class="space-y-6 sm:space-y-8">
            @forelse($photographer->portfolios as $port)
              @php
                $photos = $port->medias;
                $pCount = $photos->count();
                $albumImagesJson = json_encode($photos->pluck('media')->toArray());
                $mainPhoto = $photos->first();
                $subPhotos = $photos->slice(1);
                $thumbList = $subPhotos->take(3);
              @endphp

              <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 sm:p-7 border border-gray-200/80 dark:border-gray-700/80 shadow-none hover:border-amber-400 dark:hover:border-amber-500 transition space-y-5">
                
                @if(!$mainPhoto)
                  <div class="p-6 text-center text-xs text-gray-400">Album ini belum memiliki foto.</div>
                @elseif($thumbList->count() === 0)
                  <!-- 1 Foto Tunggal: Full Width -->
                  <div class="relative w-full overflow-hidden bg-gray-100 dark:bg-gray-900 rounded-xl aspect-[16/9] sm:aspect-[2.2/1] cursor-pointer group shadow-none"
                       @click="openLightbox('{{ $mainPhoto->media }}', '{{ addslashes($port->judul) }}', 0, {{ $albumImagesJson }})"
                       tabindex="0"
                       @keydown.enter="openLightbox('{{ $mainPhoto->media }}', '{{ addslashes($port->judul) }}', 0, {{ $albumImagesJson }})">
                    <img src="{{ $mainPhoto->media }}" 
                         alt="{{ $port->judul }}" 
                         draggable="false"
                         oncontextmenu="return false;"
                         class="w-full h-full object-cover group-hover:scale-102 transition-transform duration-500 ease-out select-none" 
                         onerror="this.onerror=null;this.src=window.SVG_IMAGE_FALLBACK;" />
                    
                    <!-- Watermark Hak Cipta Murni Tipografi (Anti-AI Slop: Zero Badge, Zero Box) -->
                    <div class="absolute bottom-3 right-3.5 select-none pointer-events-none text-xs sm:text-sm font-semibold tracking-wider text-white/75 drop-shadow-[0_1px_2px_rgba(0,0,0,0.85)] z-10">
                      &copy; LensMatch &bull; {{ $photographer->nama }}
                    </div>

                    <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                      <span class="px-3.5 py-1.5 rounded-lg bg-black/60 text-white text-xs font-bold backdrop-blur-xs flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607zM10.5 7.5v6m3-3h-6"/></svg>
                        <span>Buka Foto Penuh</span>
                      </span>
                    </div>
                  </div>
                @else
                  <!-- Album dengan Banyak Foto -->
                  <div class="grid grid-cols-1 md:grid-cols-12 gap-3 sm:gap-3.5 items-stretch">
                    
                    <!-- Kiri: Foto Utama Paling Besar -->
                    <div class="md:col-span-8">
                      <div class="relative w-full h-[260px] sm:h-[340px] md:h-[400px] lg:h-[440px] overflow-hidden bg-gray-100 dark:bg-gray-900 rounded-xl cursor-pointer group shadow-none"
                           @click="openLightbox('{{ $mainPhoto->media }}', '{{ addslashes($port->judul) }}', 0, {{ $albumImagesJson }})"
                           tabindex="0"
                           @keydown.enter="openLightbox('{{ $mainPhoto->media }}', '{{ addslashes($port->judul) }}', 0, {{ $albumImagesJson }})">
                        <img src="{{ $mainPhoto->media }}" 
                             alt="{{ $port->judul }}" 
                             draggable="false"
                             oncontextmenu="return false;"
                             class="w-full h-full object-cover group-hover:scale-102 transition-transform duration-500 ease-out select-none" 
                             onerror="this.onerror=null;this.src=window.SVG_IMAGE_FALLBACK;" />
                        
                        <!-- Watermark Hak Cipta Murni Tipografi (Anti-AI Slop: Zero Badge, Zero Box) -->
                        <div class="absolute bottom-3 right-3.5 select-none pointer-events-none text-xs sm:text-sm font-semibold tracking-wider text-white/75 drop-shadow-[0_1px_2px_rgba(0,0,0,0.85)] z-10">
                          &copy; LensMatch &bull; {{ $photographer->nama }}
                        </div>

                        <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                          <span class="px-3.5 py-1.5 rounded-lg bg-black/60 text-white text-xs font-bold backdrop-blur-xs flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607zM10.5 7.5v6m3-3h-6"/></svg>
                            <span>Buka Foto Penuh</span>
                          </span>
                        </div>
                      </div>
                    </div>

                    <!-- Kanan: 3 Thumbnail Kecil Bertumpuk VERTIKAL -->
                    <div class="md:col-span-4 flex flex-row md:flex-col gap-2 sm:gap-2.5 h-24 sm:h-32 md:h-[400px] lg:h-[440px]">
                      @foreach($thumbList as $med)
                        @php
                          $actualIndex = $photos->search(fn($item) => $item->id === $med->id);
                          $lightboxTargetIdx = $actualIndex !== false ? $actualIndex : $loop->index + 1;
                        @endphp
                        <div class="flex-1 min-w-0 min-h-0 relative w-full h-full overflow-hidden bg-gray-100 dark:bg-gray-900 rounded-xl cursor-pointer group/thumb shadow-none"
                             @click.stop="openLightbox('{{ $med->media }}', '{{ addslashes($port->judul) }}', {{ $lightboxTargetIdx }}, {{ $albumImagesJson }})"
                             tabindex="0"
                             @keydown.enter="openLightbox('{{ $med->media }}', '{{ addslashes($port->judul) }}', {{ $lightboxTargetIdx }}, {{ $albumImagesJson }})">
                          <img src="{{ $med->media }}" 
                               alt="{{ $port->judul }}" 
                               draggable="false"
                               oncontextmenu="return false;"
                               class="w-full h-full object-cover group-hover/thumb:scale-105 transition-transform duration-500 ease-out select-none"
                               onerror="this.onerror=null;this.src=window.SVG_IMAGE_FALLBACK;" />

                          <!-- Watermark Hak Cipta Halus pada Thumbnail -->
                          @if(!($loop->iteration === 3 && $pCount > 4))
                            <div class="absolute bottom-2 right-2 select-none pointer-events-none text-[10px] font-semibold tracking-wider text-white/60 drop-shadow-[0_1px_2px_rgba(0,0,0,0.85)] z-10">
                              &copy; LensMatch
                            </div>
                          @endif

                          <!-- Overlay "+X Foto Lainnya" -->
                          @if($loop->iteration === 3 && $pCount > 4)
                            <div class="absolute inset-0 bg-gray-950/70 backdrop-blur-[2px] flex flex-col items-center justify-center text-white transition group-hover/thumb:bg-gray-950/80 z-20">
                              <span class="text-xs sm:text-sm font-black tracking-tight">+{{ $pCount - 4 }}</span>
                              <span class="text-[9px] font-semibold text-gray-300">Foto Lainnya</span>
                            </div>
                          @endif
                        </div>
                      @endforeach
                    </div>

                  </div>
                @endif

                <!-- Meta Footer Card -->
                <div class="space-y-1.5 pt-1">
                  <div class="flex flex-wrap items-baseline gap-2.5">
                    <h3 class="text-base sm:text-lg font-black text-gray-900 dark:text-white tracking-tight">
                      {{ $port->judul }}
                    </h3>
                    @if($port->category)
                      <span class="text-xs sm:text-sm font-semibold text-gray-500 dark:text-gray-400">
                        &middot; {{ $port->category->nama_kategori }}
                      </span>
                    @endif
                    <span class="text-xs sm:text-sm font-semibold text-gray-400 dark:text-gray-500">
                      &middot; {{ $pCount }} Foto
                    </span>
                  </div>
                  @if(!empty(trim($port->deskripsi)))
                    <p class="text-xs sm:text-[13px] text-gray-600 dark:text-gray-300 leading-relaxed max-w-3xl" title="{{ $port->deskripsi }}">
                      {{ $port->deskripsi }}
                    </p>
                  @endif
                </div>

              </div>
            @empty
              <div class="col-span-full bg-white dark:bg-gray-800 p-8 sm:p-10 text-center rounded-2xl border border-gray-200/80 dark:border-gray-700/80 shadow-none space-y-2">
                <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-400 flex items-center justify-center mx-auto">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5z"></path></svg>
                </div>
                <h4 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white">Belum Ada Portofolio</h4>
                <p class="text-xs text-gray-400">Fotografer ini belum menambahkan album karya portofolio.</p>
              </div>
            @endforelse
          </div>
        </div>

        <!-- TAB 3: KALENDER KETERSEDIAAN -->
        <div x-show="activeTab === 'availability'" 
             class="space-y-6"
             x-data="{
               year: new Date().getFullYear(),
               month: new Date().getMonth(),
               selectedDate: null,
               selectedDateInfo: null,
               blockedDates: {{ json_encode($blockedDates ?? []) }},
               bookedDates: {{ json_encode($bookedDates ?? []) }},
               monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
               dayNames: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
               
               get calendarDays() {
                 const firstDay = new Date(this.year, this.month, 1);
                 const lastDay = new Date(this.year, this.month + 1, 0);
                 
                 let startDayOfWeek = firstDay.getDay() - 1;
                 if (startDayOfWeek < 0) startDayOfWeek = 6;
                 
                 const daysInMonth = lastDay.getDate();
                 const days = [];
                 
                 const prevMonthLastDay = new Date(this.year, this.month, 0).getDate();
                 for (let i = startDayOfWeek - 1; i >= 0; i--) {
                   days.push({
                     dayNumber: prevMonthLastDay - i,
                     isCurrentMonth: false,
                     isPast: true,
                     dateStr: ''
                   });
                 }
                 
                 const today = new Date();
                 today.setHours(0,0,0,0);
                 
                 for (let d = 1; d <= daysInMonth; d++) {
                   const dayDate = new Date(this.year, this.month, d);
                   const y = dayDate.getFullYear();
                   const m = String(dayDate.getMonth() + 1).padStart(2, '0');
                   const dayNum = String(d).padStart(2, '0');
                   const dateStr = `${y}-${m}-${dayNum}`;
                   
                   const isPast = dayDate < today;
                   const isToday = dayDate.getTime() === today.getTime();
                   const isBlocked = !!this.blockedDates[dateStr];
                   const isBooked = !!this.bookedDates[dateStr];
                   
                   let status = 'available';
                   if (isPast) status = 'past';
                   else if (isBlocked) status = 'blocked';
                   else if (isBooked) status = 'booked';
                   
                   days.push({
                     dayNumber: d,
                     isCurrentMonth: true,
                     isPast: isPast,
                     isToday: isToday,
                     dateStr: dateStr,
                     status: status,
                     blockedInfo: this.blockedDates[dateStr] || null,
                     bookedInfo: this.bookedDates[dateStr] || null
                   });
                 }
                 
                 const remaining = 42 - days.length;
                 if (remaining > 0 && remaining < 7) {
                   for (let j = 1; j <= remaining; j++) {
                     days.push({
                       dayNumber: j,
                       isCurrentMonth: false,
                       isPast: true,
                       dateStr: ''
                     });
                   }
                 }
                 
                 return days;
               },
               
               prevMonth() {
                 if (this.month === 0) {
                   this.month = 11;
                   this.year--;
                 } else {
                   this.month--;
                 }
                 this.selectedDate = null;
                 this.selectedDateInfo = null;
               },
               
               nextMonth() {
                 if (this.month === 11) {
                   this.month = 0;
                   this.year++;
                 } else {
                   this.month++;
                 }
                 this.selectedDate = null;
                 this.selectedDateInfo = null;
               },
               
               selectDay(day) {
                 if (!day.isCurrentMonth || day.isPast) return;
                 this.selectedDate = day.dateStr;
                 this.selectedDateInfo = day;
               }
             }">
          
          <div>
            <h2 class="text-base sm:text-lg font-black text-gray-900 dark:text-white tracking-tight">Kalender Ketersediaan</h2>
          </div>

          <!-- Main Calendar Container Card -->
          <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200/80 dark:border-gray-700/80 shadow-none p-5 sm:p-7 space-y-5">
            
            <!-- Month Navigation Header -->
            <div class="flex items-center justify-between pb-1">
              <h3 class="text-sm sm:text-base font-black text-gray-900 dark:text-white tracking-tight" 
                  x-text="monthNames[month] + ' ' + year">
              </h3>

              <!-- Prev/Next Controls -->
              <div class="flex items-center gap-1.5">
                <button type="button" 
                        @click="prevMonth()" 
                        title="Bulan Sebelumnya" 
                        class="w-8 h-8 rounded-lg border border-gray-200 dark:border-gray-700 flex items-center justify-center text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition cursor-pointer">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                </button>
                <button type="button" 
                        @click="nextMonth()" 
                        title="Bulan Berikutnya" 
                        class="w-8 h-8 rounded-lg border border-gray-200 dark:border-gray-700 flex items-center justify-center text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition cursor-pointer">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                </button>
              </div>
            </div>

            <!-- 7-Day Name Grid Headers -->
            <div class="grid grid-cols-7 gap-1.5 sm:gap-2 text-center text-[10px] sm:text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">
              <template x-for="dname in dayNames" :key="dname">
                <div class="py-0.5" x-text="dname"></div>
              </template>
            </div>

            <!-- Days Grid -->
            <div class="grid grid-cols-7 gap-1.5 sm:gap-2.5">
              <template x-for="(day, idx) in calendarDays" :key="idx">
                <div @click="selectDay(day)"
                     class="min-h-[64px] sm:min-h-[76px] p-2 rounded-xl border transition-all duration-200 flex flex-col justify-between shadow-none"
                     :class="{
                       'bg-gray-50 dark:bg-gray-900/60 border-gray-100 dark:border-gray-800 cursor-default select-none': !day.isCurrentMonth,
                       'bg-gray-50 dark:bg-gray-900/60 border-gray-100 dark:border-gray-800 cursor-not-allowed select-none': day.isCurrentMonth && day.isPast,
                       'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 cursor-pointer hover:border-amber-400 dark:hover:border-amber-500': day.isCurrentMonth && !day.isPast && !day.isToday,
                       'bg-amber-50/50 dark:bg-amber-950/20 border-amber-400 dark:border-amber-500 cursor-pointer': day.isToday,
                       'ring-2 ring-amber-500': selectedDate === day.dateStr
                     }">
                  
                  <!-- Date Number & Today Text Indicator Beside Number (Rule #67 Compliant) -->
                  <div class="flex items-center gap-1 flex-wrap">
                    <span class="text-xs sm:text-sm font-black" 
                          :class="{
                            'text-gray-300 dark:text-gray-600': !day.isCurrentMonth,
                            'text-gray-400 dark:text-gray-500': day.isCurrentMonth && day.isPast,
                            'text-amber-600 dark:text-amber-400': day.isToday,
                            'text-gray-900 dark:text-white': day.isCurrentMonth && !day.isPast && !day.isToday
                          }"
                          x-text="day.dayNumber">
                    </span>
                    <template x-if="day.isToday">
                      <span class="text-[10px] font-black text-amber-600 dark:text-amber-400">
                        (Hari Ini)
                      </span>
                    </template>
                  </div>

                  <!-- Mini Status Label in Cell (Pure Colored Text - Rule #67 Compliant, Zero Dots) -->
                  <div class="text-[10px] font-bold truncate">
                    <template x-if="day.isCurrentMonth && !day.isPast && day.status === 'available'">
                      <span class="text-emerald-600 dark:text-emerald-400 font-bold">Tersedia</span>
                    </template>
                    <template x-if="day.isCurrentMonth && !day.isPast && day.status === 'booked'">
                      <span class="text-blue-600 dark:text-blue-400 font-bold">Terisi</span>
                    </template>
                    <template x-if="day.isCurrentMonth && !day.isPast && day.status === 'blocked'">
                      <span class="text-rose-600 dark:text-rose-400 font-bold">Libur</span>
                    </template>
                  </div>

                </div>
              </template>
            </div>

            <!-- Selected Day Detail Drawer / Panel -->
            <template x-if="selectedDateInfo">
              <div class="p-4 sm:p-5 rounded-xl border bg-white dark:bg-gray-800 border-gray-200/80 dark:border-gray-700/80 shadow-none transition-all space-y-1.5">
                <div class="flex items-center gap-2 flex-wrap">
                  <span class="text-[11px] font-black uppercase tracking-wider text-gray-500 dark:text-gray-400">Rincian Jadwal:</span>
                  <span class="text-xs sm:text-sm font-black text-gray-900 dark:text-white" x-text="selectedDateInfo.dateStr"></span>
                  <template x-if="selectedDateInfo.isToday">
                    <span class="text-[11px] font-black text-amber-600 dark:text-amber-400">(Hari Ini)</span>
                  </template>
                </div>
                
                <template x-if="selectedDateInfo.status === 'available'">
                  <p class="text-xs text-gray-700 dark:text-gray-300 font-medium">
                    Status: <span class="text-emerald-600 dark:text-emerald-400 font-bold">Tersedia</span> &mdash; Fotografer membuka sesi pemotretan dan siap dipesan pada tanggal ini.
                  </p>
                </template>

                <template x-if="selectedDateInfo.status === 'booked'">
                  <div class="text-xs text-gray-700 dark:text-gray-300 font-medium space-y-1">
                    <p>Status: <span class="text-blue-600 dark:text-blue-400 font-bold">Terisi Sesi</span> &mdash; Tanggal ini telah terisi oleh jadwal sesi pemotretan aktif:</p>
                    <template x-for="bk in selectedDateInfo.bookedInfo" :key="bk.time_formatted">
                      <div class="flex items-center gap-1.5 font-bold text-gray-800 dark:text-gray-200 pl-2">
                        <span>&bull;</span>
                        <span x-text="bk.service_name + ' (' + bk.time_formatted + ')'"></span>
                      </div>
                    </template>
                  </div>
                </template>

                <template x-if="selectedDateInfo.status === 'blocked'">
                  <p class="text-xs text-gray-700 dark:text-gray-300 font-medium">
                    Status: <span class="text-rose-600 dark:text-rose-400 font-bold">Libur</span> &mdash; Fotografer tidak menerima sesi pemotretan pada tanggal ini: <strong x-text="selectedDateInfo.blockedInfo ? selectedDateInfo.blockedInfo.keterangan : 'Libur / Cuti Pribadi'"></strong>.
                  </p>
                </template>
              </div>
            </template>

            <!-- Legend Bar -->
            <div class="flex flex-wrap items-center justify-between gap-3 pt-1 text-xs">
              <div class="flex flex-wrap items-center gap-4 sm:gap-6 font-semibold">
                <div class="flex items-center gap-1">
                  <span class="text-emerald-600 dark:text-emerald-400 font-bold">Tersedia</span>
                  <span class="text-gray-400 font-normal">(Terbuka)</span>
                </div>
                <div class="flex items-center gap-1">
                  <span class="text-blue-600 dark:text-blue-400 font-bold">Terisi Sesi</span>
                  <span class="text-gray-400 font-normal">(Aktif)</span>
                </div>
                <div class="flex items-center gap-1">
                  <span class="text-rose-600 dark:text-rose-400 font-bold">Libur</span>
                  <span class="text-gray-400 font-normal">(Tutup)</span>
                </div>
              </div>

              <span class="text-[11px] text-gray-400">Klik tanggal pada kalender untuk melihat rincian</span>
            </div>

          </div>

          <!-- Jam Kerja Operasional Mingguan Card -->
          <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200/80 dark:border-gray-700/80 shadow-none p-5 sm:p-6 space-y-2">
            <h3 class="text-xs sm:text-sm font-black text-gray-900 dark:text-white flex items-center gap-2">
              <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              <span>Standar Jam Kerja Operasional Studio</span>
            </h3>
            <p class="text-xs sm:text-[13px] text-gray-600 dark:text-gray-300 leading-relaxed">
              Sesi pemotretan reguler dilayani setiap hari kerja pukul <strong>08:00 &mdash; 18:00 WITA/WIB</strong>. Pemesanan sesi khusus malam hari atau luar pulau dapat dikoordinasikan secara langsung melalui menu chat fotografer.
            </p>
          </div>

        </div>

        <!-- TAB 4: ULASAN KLIEN -->
        <div x-show="activeTab === 'reviews'" class="space-y-6">
          <div>
            <h2 class="text-base sm:text-lg font-black text-gray-900 dark:text-white tracking-tight">Ulasan Klien</h2>
          </div>

          <!-- Rating Summary Breakdown Card -->
          <div class="bg-white dark:bg-gray-800 p-6 sm:p-7 rounded-2xl border border-gray-200/80 dark:border-gray-700/80 shadow-none grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
            <!-- Average Score Column -->
            <div class="md:col-span-4 text-center md:border-r border-gray-200 dark:border-gray-700 md:pr-6 space-y-2">
              <div class="text-4xl sm:text-5xl font-black text-gray-900 dark:text-white tracking-tight">
                {{ $avgRating }}
              </div>
              <div class="flex items-center justify-center gap-1 text-amber-400">
                @for($i = 1; $i <= 5; $i++)
                  <svg class="w-4 h-4 {{ $i <= round($avgRating) ? 'fill-current' : 'text-gray-200 dark:text-gray-700 fill-current' }}" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                @endfor
              </div>
              <p class="text-xs text-gray-400 font-medium">Berdasarkan {{ $totalReviews }} ulasan terverifikasi</p>
            </div>

            <!-- Star Bars Breakdown Column -->
            <div class="md:col-span-8 space-y-1.5">
              @foreach([5, 4, 3, 2, 1] as $star)
                @php
                  $cnt = $starDistribution[$star] ?? 0;
                  $pct = $totalReviews > 0 ? round(($cnt / $totalReviews) * 100) : 0;
                @endphp
                <div class="flex items-center gap-2.5 text-xs text-gray-600 dark:text-gray-400">
                  <span class="w-7 flex items-center gap-0.5 font-bold shrink-0">
                    <span>{{ $star }}</span>
                    <span class="text-amber-400 text-xs">★</span>
                  </span>
                  <div class="flex-1 h-2 rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                    <div class="h-full bg-amber-400 rounded-full" style="width: {{ $pct }}%"></div>
                  </div>
                  <span class="w-8 text-right font-medium text-gray-400 shrink-0">{{ $cnt }}</span>
                </div>
              @endforeach
            </div>
          </div>

          <!-- Testimonials List -->
          <div class="space-y-4">
            @forelse($testimonials as $t)
              <div class="bg-white dark:bg-gray-800 p-5 sm:p-6 rounded-2xl border border-gray-200/80 dark:border-gray-700/80 shadow-none space-y-2.5">
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-300 font-black text-xs flex items-center justify-center shrink-0">
                      {{ substr($t->client->nama ?? ($t->client->user->nama ?? 'Klien'), 0, 1) }}
                    </div>
                    <div>
                      <h4 class="text-xs sm:text-sm font-extrabold text-gray-900 dark:text-white">
                        {{ $t->client->nama ?? ($t->client->user->nama ?? 'Klien Terverifikasi') }}
                      </h4>
                      <span class="text-[11px] text-gray-400">
                        {{ $t->created_at ? $t->created_at->locale('id')->diffForHumans() : 'Baru saja' }}
                      </span>
                    </div>
                  </div>

                  <!-- Rating Stars -->
                  <div class="flex items-center gap-0.5 text-amber-400">
                    @for($s = 1; $s <= 5; $s++)
                      <svg class="w-3.5 h-3.5 {{ $s <= $t->rating ? 'fill-current' : 'text-gray-200 dark:text-gray-700 fill-current' }}" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                    @endfor
                  </div>
                </div>

                <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                  {{ $t->komentar }}
                </p>
              </div>
            @empty
              <div class="bg-white dark:bg-gray-800 p-8 sm:p-10 text-center rounded-2xl border border-gray-200/80 dark:border-gray-700/80 shadow-none space-y-2">
                <h4 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white">Belum Ada Ulasan Klien</h4>
                <p class="text-xs text-gray-400">Ulasan resmi dari Klien yang telah mereservasi sesi foto akan ditampilkan di sini.</p>
              </div>
            @endforelse
          </div>
        </div>

        <!-- TAB 5: TANYA JAWAB (Kombinasi Fotografer + Ketentuan Sistem Opsi A) -->
        <div x-show="activeTab === 'faqs'" class="space-y-8" x-data="{ openFaq: null }">
          <!-- Bagian 1: Tanya Jawab Fotografer -->
          <div class="space-y-4">
            <div class="space-y-1">
              <h2 class="text-base sm:text-lg font-black text-gray-900 dark:text-white tracking-tight">Tanya Jawab Fotografer</h2>
              <p class="text-xs text-gray-500 dark:text-gray-400">Informasi layanan langsung dari {{ $photographer->nama }}.</p>
            </div>

            <div class="space-y-3">
              @forelse($faqs as $faq)
                @php
                  $qId = is_array($faq) ? $faq['id'] : $faq->id;
                  $qText = is_array($faq) ? $faq['pertanyaan'] : $faq->pertanyaan;
                  $aText = is_array($faq) ? $faq['jawaban'] : $faq->jawaban;
                  $studioKey = 'photographer_' . $qId;
                @endphp
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200/80 dark:border-gray-700/80 shadow-none overflow-hidden transition hover:border-amber-400/80">
                  <button type="button" 
                          @click="openFaq = (openFaq === '{{ $studioKey }}' ? null : '{{ $studioKey }}')" 
                          class="w-full p-4 sm:p-5 text-left flex items-center justify-between gap-3 cursor-pointer hover:bg-gray-50/50 dark:hover:bg-gray-700/50 transition focus:outline-none focus:ring-0 group">
                    <span class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2.5 transition-colors"
                          :class="openFaq === '{{ $studioKey }}' ? 'text-amber-600 dark:text-amber-400' : ''">
                      <svg class="w-4 h-4 text-amber-500 dark:text-amber-400 shrink-0 transition-transform duration-200" 
                           :class="openFaq === '{{ $studioKey }}' ? 'scale-110' : ''"
                           fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                      </svg>
                      <span>{{ $qText }}</span>
                    </span>
                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 shrink-0" 
                         :class="openFaq === '{{ $studioKey }}' ? 'rotate-180 text-amber-500' : ''" 
                         fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                    </svg>
                  </button>

                  <div x-show="openFaq === '{{ $studioKey }}'" 
                       x-cloak 
                       x-transition:enter="transition ease-out duration-200"
                       x-transition:enter-start="opacity-0 -translate-y-1"
                       x-transition:enter-end="opacity-100 translate-y-0"
                       class="px-4 sm:px-5 pb-4 sm:pb-5 pt-0.5">
                    <div class="text-xs sm:text-sm leading-relaxed text-gray-600 dark:text-gray-300 pl-6 sm:pl-7">
                      <p class="whitespace-pre-line">{{ $aText }}</p>
                    </div>
                  </div>
                </div>
              @empty
                <div class="bg-white dark:bg-gray-800 p-6 sm:p-7 text-center rounded-2xl border border-gray-200/80 dark:border-gray-700/80 shadow-none space-y-1.5">
                  <h4 class="text-sm font-bold text-gray-900 dark:text-white">Belum Ada Tanya Jawab</h4>
                  <p class="text-xs text-gray-500 dark:text-gray-400">{{ $photographer->nama }} belum menambahkan pertanyaan khusus. Gunakan tombol Chat untuk bertanya langsung.</p>
                </div>
              @endforelse
            </div>
          </div>

          <!-- Bagian 2: Ketentuan Sistem LensMatch (Hardcoded Terpusat) -->
          @if(!empty($systemFaqs))
            <div class="space-y-4">
              <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-2">
                <div class="space-y-1">
                  <h3 class="text-base sm:text-lg font-black text-gray-900 dark:text-white tracking-tight">Ketentuan Sistem LensMatch</h3>
                  <p class="text-xs text-gray-500 dark:text-gray-400">Ketentuan pemesanan, pembayaran, jadwal ulang, dan penerimaan hasil foto di LensMatch.</p>
                </div>
                <a href="{{ route('public.faq') }}" target="_blank" class="text-xs font-bold text-amber-600 dark:text-amber-400 hover:underline shrink-0">
                  Lihat Pusat Bantuan
                </a>
              </div>

              <div class="space-y-3">
                @foreach($systemFaqs as $sysIdx => $sysFaq)
                  @php
                    $sysKey = 'system_' . ($sysFaq['id'] ?? $sysIdx);
                  @endphp
                  <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200/80 dark:border-gray-700/80 shadow-none overflow-hidden transition hover:border-amber-400/80">
                    <button type="button" 
                            @click="openFaq = (openFaq === '{{ $sysKey }}' ? null : '{{ $sysKey }}')" 
                            class="w-full p-4 sm:p-5 text-left flex items-center justify-between gap-3 cursor-pointer hover:bg-gray-50/50 dark:hover:bg-gray-700/50 transition focus:outline-none focus:ring-0 group">
                      <span class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2.5 transition-colors"
                            :class="openFaq === '{{ $sysKey }}' ? 'text-amber-600 dark:text-amber-400' : ''">
                        <svg class="w-4 h-4 text-amber-500 dark:text-amber-400 shrink-0 transition-transform duration-200" 
                             :class="openFaq === '{{ $sysKey }}' ? 'scale-110' : ''"
                             fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                        </svg>
                        <span>{{ $sysFaq['pertanyaan'] }}</span>
                      </span>
                      <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 shrink-0" 
                           :class="openFaq === '{{ $sysKey }}' ? 'rotate-180 text-amber-500' : ''" 
                           fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                      </svg>
                    </button>

                    <div x-show="openFaq === '{{ $sysKey }}'" 
                         x-cloak 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="px-4 sm:px-5 pb-4 sm:pb-5 pt-0.5">
                      <div class="text-xs sm:text-sm leading-relaxed text-gray-600 dark:text-gray-300 pl-6 sm:pl-7">
                        <p class="whitespace-pre-line">{{ $sysFaq['jawaban'] }}</p>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
          @endif
        </div>

      </div>

    </div>

  </div>

  <!-- Fullscreen Lightbox Modal (Rule #75 & Rule #98 Compliant) -->
  <div x-show="lightboxOpen" 
       x-cloak 
       @keydown.escape.window="lightboxOpen = false"
       @keydown.arrow-right.window="nextImage()"
       @keydown.arrow-left.window="prevImage()"
       class="fixed inset-0 z-50 flex items-center justify-center bg-black/95 backdrop-blur-md p-4 sm:p-6"
       tabindex="-1">
    
    <!-- Close Button -->
    <button type="button" 
            @click="lightboxOpen = false" 
            title="Tutup (Esc)" 
            class="absolute top-4 right-4 sm:top-6 sm:right-6 w-10 h-10 rounded-full bg-white/10 hover:bg-white/25 text-white flex items-center justify-center transition cursor-pointer z-50">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>

    <!-- Prev Image Button -->
    <button type="button" 
            x-show="lightboxImages.length > 1" 
            @click.stop="prevImage()" 
            title="Foto Sebelumnya (Panah Kiri)" 
            class="absolute left-3 sm:left-6 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/10 hover:bg-white/25 text-white flex items-center justify-center transition cursor-pointer z-50">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
    </button>

    <!-- Next Image Button -->
    <button type="button" 
            x-show="lightboxImages.length > 1" 
            @click.stop="nextImage()" 
            title="Foto Berikutnya (Panah Kanan)" 
            class="absolute right-3 sm:right-6 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/10 hover:bg-white/25 text-white flex items-center justify-center transition cursor-pointer z-50">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
    </button>

    <!-- Main Image Display Container -->
    <div class="relative max-w-5xl max-h-[85vh] flex flex-col items-center justify-center space-y-3" @click.outside="lightboxOpen = false">
      <div class="relative inline-block max-w-full max-h-[75vh]">
        <img :src="lightboxSrc" 
             :alt="lightboxTitle" 
             draggable="false"
             oncontextmenu="return false;"
             class="max-w-full max-h-[75vh] object-contain rounded-xl shadow-2xl select-none" />
        
        <!-- Watermark Hak Cipta Murni Tipografi (Anti-AI Slop: Zero Badge, Zero Box) -->
        <div class="absolute bottom-3 right-3.5 select-none pointer-events-none text-xs sm:text-sm font-semibold tracking-wider text-white/75 drop-shadow-[0_1px_3px_rgba(0,0,0,0.95)] z-10">
          &copy; LensMatch &bull; {{ $photographer->nama }}
        </div>
      </div>
      
      <div class="text-center text-white flex flex-col gap-1 px-4">
        <p class="text-xs sm:text-sm font-bold truncate max-w-lg" x-text="lightboxTitle"></p>
        <template x-if="lightboxImages.length > 1">
          <p class="text-[11px] text-gray-400 font-mono" x-text="(lightboxIndex + 1) + ' / ' + lightboxImages.length"></p>
        </template>
      </div>
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
      <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 text-center shadow-xl transition-all sm:my-8 w-full max-w-[420px] border border-gray-200 dark:border-gray-700 p-7 sm:p-8 flex flex-col gap-4" @click.stop>
        
        <!-- Plain Close Button Absolute Top Right (SVG Vector per Rule #6 & #59) -->
        <button type="button" @click="closeLoginModal()" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 dark:hover:text-white transition cursor-pointer p-1" aria-label="Tutup">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        <!-- Header without Icon & Centered -->
        <div class="text-center">
          <h3 class="text-base sm:text-lg font-black text-gray-900 dark:text-white">Login Diperlukan</h3>
        </div>

        <!-- Body Text (Centered) -->
        <p class="text-xs sm:text-[13px] text-gray-600 dark:text-gray-300 leading-relaxed text-center" x-text="loginModalMessage">
        </p>

        <!-- Action Button -->
        <div>
          <a :href="loginRedirectUrl ? '{{ route('login') }}?redirect=' + encodeURIComponent(loginRedirectUrl) : '{{ route('login') }}'" class="w-full py-3 px-4 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs sm:text-sm rounded-xl shadow-xs transition hover:scale-[1.01] flex items-center justify-center cursor-pointer">
            Masuk ke Akun
          </a>
        </div>

      </div>
    </div>
  </div>

</div>
@endsection
