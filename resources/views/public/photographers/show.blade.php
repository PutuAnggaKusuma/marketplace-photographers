@extends('layouts.app')

@section('title', $photographer->nama . ' — Profil & Price List Fotografer LensMatch')

@section('content')
@php
  $lowestPrice = $photographer->services->whereNotNull('tarif_harga')->where('tarif_harga', '>', 0)->min('tarif_harga');
  if (!$lowestPrice) {
      $lowestPrice = 1500000;
  }
  $validServices = $photographer->services->whereNotNull('tarif_harga')->where('tarif_harga', '>', 0);
  $avatarUrl = $photographer->foto_url ?? ($photographer->foto ? (str_starts_with($photographer->foto, 'http') ? $photographer->foto : asset('storage/' . $photographer->foto)) : 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400&q=80');
@endphp

<!-- Photographer Profile Details (Eclips Doctor Profile Design System — Dribbble Reference) -->
<section class="relative pt-8 pb-12 lg:pt-12 lg:pb-16 bg-gray-50/60 dark:bg-gray-900/60 overflow-hidden"
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
           }
         }">
  
  <!-- Subtle Elegant Fluid Background Curve Accent (Dribbble Eclips Reference) -->
  <div class="absolute inset-0 pointer-events-none overflow-hidden opacity-60 dark:opacity-20 select-none">
    <svg class="absolute -top-10 left-1/2 -translate-x-1/2 w-[1300px] h-[340px]" viewBox="0 0 1300 340" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M-80 90 C 220 260, 520 -30, 840 180 C 1040 310, 1260 110, 1380 130" stroke="url(#wave-grad-1)" stroke-width="42" stroke-linecap="round" opacity="0.14"/>
      <path d="M-30 130 C 270 300, 570 10, 890 220 C 1090 350, 1310 150, 1430 170" stroke="url(#wave-grad-2)" stroke-width="20" stroke-linecap="round" opacity="0.10"/>
      <defs>
        <linearGradient id="wave-grad-1" x1="0%" y1="0%" x2="100%" y2="0%">
          <stop offset="0%" stop-color="#f59e0b" />
          <stop offset="50%" stop-color="#3b82f6" />
          <stop offset="100%" stop-color="#10b981" />
        </linearGradient>
        <linearGradient id="wave-grad-2" x1="0%" y1="0%" x2="100%" y2="0%">
          <stop offset="0%" stop-color="#10b981" />
          <stop offset="50%" stop-color="#f59e0b" />
          <stop offset="100%" stop-color="#3b82f6" />
        </linearGradient>
      </defs>
    </svg>
  </div>

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 space-y-6">

    <!-- Top Navigation / Back Button -->
    <div class="flex items-center justify-between">
      <a href="{{ route('public.photographers.katalog') }}" 
         class="inline-flex items-center gap-2 text-xs font-bold text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
        <span>Kembali ke Katalog</span>
      </a>

      <!-- Mitra ID Tag -->
      <span class="text-[11px] font-mono font-bold text-gray-400 dark:text-gray-500">
        #FTR-{{ str_pad($photographer->id, 5, '0', STR_PAD_LEFT) }}
      </span>
    </div>

    <!-- Centered Header Section (Dribbble Eclips Style) -->
    <div class="text-center space-y-2 max-w-xl mx-auto">
      <!-- Breadcrumb Navigation -->
      <nav class="flex items-center justify-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 mb-1">
        <a href="{{ route('public.photographers.katalog') }}" class="hover:text-amber-500 transition">Katalog Fotografer</a>
        <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
        <span class="text-gray-900 dark:text-white font-bold">{{ $photographer->nama }}</span>
      </nav>
      <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white tracking-tight">
        Detail Profil Fotografer
      </h1>
      <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 leading-relaxed">
        Informasi profil resmi mitra fotografer terverifikasi LensMatch, spesialisasi keahlian, tarif layanan, dan ulasan asli klien.
      </p>
    </div>

    <!-- Main Profile Card (Eclips Structure: Portrait Photo Left + Details Right) -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 sm:p-8 shadow-xs border border-gray-100 dark:border-gray-700/80">
      
      <!-- Top Section: Photo + Metadata -->
      <div class="flex flex-col md:flex-row items-center md:items-stretch gap-6 lg:gap-8">
        
        <!-- Left: Portrait Photo with Rounded Corners (Dribbble Reference) -->
        <div class="w-full md:w-64 lg:w-72 h-80 sm:h-96 md:h-auto rounded-2xl overflow-hidden bg-gradient-to-b from-sky-50 via-amber-50/30 to-amber-100/20 dark:from-gray-800 dark:to-gray-750 flex items-center justify-center shrink-0 border border-gray-100 dark:border-gray-700/80 shadow-xs relative group">
          <img onerror="this.onerror=null;this.src=window.SVG_IMAGE_FALLBACK||'{{ asset('images/avatar-default.png') }}';" 
               src="{{ $avatarUrl }}" 
               alt="{{ $photographer->nama }}" 
               class="w-full h-full object-cover rounded-2xl group-hover:scale-105 transition-transform duration-300">
          
          @if($photographer->is_verified)
            <!-- Verified Mitra Badge Overlay -->
            <div class="absolute top-3 left-3 px-2.5 py-1 rounded-full bg-white/95 dark:bg-gray-900/95 backdrop-blur-xs text-[10px] font-extrabold text-amber-600 dark:text-amber-400 border border-amber-200/60 dark:border-amber-700/60 shadow-xs flex items-center gap-1">
              <svg class="w-3 h-3 text-amber-500 fill-amber-500" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
              <span>Verified Mitra</span>
            </div>
          @endif
        </div>

        <!-- Right: Professional Credentials & Fee (Dribbble Layout) -->
        <div class="flex-1 flex flex-col justify-between space-y-4 w-full">
          
          <div class="space-y-2">
            <!-- Studio Name & Role -->
            <div>
              <span class="text-xs font-bold uppercase tracking-wider text-amber-500 dark:text-amber-400">
                Mitra Fotografer Profesional
              </span>
              <h2 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white mt-0.5">
                {{ $photographer->nama }}
              </h2>
              @if($photographer->categories && $photographer->categories->isNotEmpty())
                <p class="text-xs sm:text-sm font-semibold text-gray-700 dark:text-gray-300 leading-snug mt-1">
                  Spesialisasi: {{ $photographer->categories->pluck('nama_kategori')->join(', ') }}
                </p>
              @endif
            </div>

            <!-- Bio Snippet -->
            @if(!empty(trim($photographer->deskripsi_bio)))
              <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed pt-1">
                {{ $photographer->deskripsi_bio }}
              </p>
            @endif
          </div>

          <!-- Dashed Divider -->
          <div class="border-b border-dashed border-gray-200 dark:border-gray-700"></div>

          <!-- Working at / Studio Base -->
          <div class="space-y-0.5">
            <span class="text-xs font-medium text-gray-400 dark:text-gray-500">Lokasi & Basis Studio</span>
            <p class="text-sm font-bold text-gray-800 dark:text-gray-200">
              {{ $photographer->city ? ucwords(strtolower($photographer->city->name)) : ($photographer->alamat ?: 'Indonesia') }}
            </p>
          </div>

          <!-- Dashed Divider -->
          <div class="border-b border-dashed border-gray-200 dark:border-gray-700"></div>

          <!-- Consultation Fee / Tarif Layanan Sesi -->
          <div class="flex flex-col sm:flex-row sm:items-baseline sm:justify-between gap-1">
            <div>
              <span class="text-xs font-medium text-gray-400 dark:text-gray-500">Tarif Layanan Sesi Foto</span>
              <div class="flex items-baseline gap-2 mt-0.5">
                <span class="text-xl sm:text-2xl font-black text-amber-500 dark:text-amber-400">
                  Mulai Rp {{ number_format($lowestPrice, 0, ',', '.') }}
                </span>
                <span class="text-xs text-gray-400 font-normal">(Termasuk proteksi escrow)</span>
              </div>
            </div>
            <span class="text-[11px] text-gray-400 sm:text-right">Per paket sesi pemotretan</span>
          </div>

        </div>

      </div>

      <!-- Middle: Stats Bar (4 columns, clean minimalist per Rule #64) -->
      <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-700/80 grid grid-cols-2 sm:grid-cols-4 gap-4 sm:gap-6 text-center sm:text-left">
        <div>
          <p class="text-[11px] font-medium text-gray-400 dark:text-gray-500">Pengalaman Studio</p>
          <p class="text-base sm:text-lg font-black text-gray-900 dark:text-white mt-0.5">5+ Tahun</p>
        </div>
        <div>
          <p class="text-[11px] font-medium text-gray-400 dark:text-gray-500">ID Registrasi Mitra</p>
          <p class="text-base sm:text-lg font-black text-gray-900 dark:text-white mt-0.5">#FTR-{{ str_pad($photographer->id, 5, '0', STR_PAD_LEFT) }}</p>
        </div>
        <div>
          <p class="text-[11px] font-medium text-gray-400 dark:text-gray-500">Bergabung Sejak</p>
          <p class="text-base sm:text-lg font-black text-gray-900 dark:text-white mt-0.5">{{ $photographer->created_at ? $photographer->created_at->locale('id')->translatedFormat('d M Y') : '15 Agu 2026' }}</p>
        </div>
        <div>
          <p class="text-[11px] font-medium text-gray-400 dark:text-gray-500">Rating & Ulasan</p>
          <p class="text-base sm:text-lg font-black text-gray-900 dark:text-white mt-0.5 flex items-center justify-center sm:justify-start gap-1">
            <span class="text-amber-400">★</span>
            <span>{{ $avgRating }}</span>
            <span class="text-xs font-normal text-gray-400">({{ $totalReviews }})</span>
          </p>
        </div>
      </div>

      <!-- Bottom: Action Buttons (Dribbble Layout: Wide Primary CTA + Chat + Bookmark) -->
      <div class="mt-6 pt-2 flex flex-col sm:flex-row items-center gap-3">
        <a href="{{ url('/booking/create?photographer_id=' . $photographer->id) }}" 
           class="w-full sm:flex-1 py-3.5 px-6 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-sm text-center shadow-xs transition cursor-pointer">
          Reservasi Sesi Foto Sekarang
        </a>

        <a href="{{ route('chat.start', $photographer->id) }}" 
           class="w-full sm:w-auto px-6 py-3.5 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-650 text-gray-800 dark:text-gray-100 font-bold text-sm text-center transition cursor-pointer flex items-center justify-center gap-2">
          <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
          <span>Tanya & Chat Fotografer</span>
        </a>

        <!-- Bookmark Favorit Button (Rule #93 Compliant with Live Toast) -->
        <div x-data="{ isFavorited: {{ ($isFavorited ?? false) ? 'true' : 'false' }}, loading: false }" class="w-full sm:w-auto">
          <button type="button" 
                  @click="
                    if (!{{ Auth::check() ? 'true' : 'false' }}) {
                      window.location.href = '{{ route('login', ['redirect' => url()->full()]) }}';
                      return;
                    }
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
                  class="w-full sm:w-auto px-5 py-3.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-750 font-bold text-sm text-center transition shadow-2xs inline-flex items-center justify-center gap-2 cursor-pointer"
                  :class="isFavorited ? 'text-amber-600 dark:text-amber-400 border-amber-300 dark:border-amber-800' : 'text-gray-700 dark:text-gray-300'">
            <svg class="w-4 h-4 transition-colors" :class="isFavorited ? 'fill-current text-amber-500' : 'fill-none stroke-current stroke-2'" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z" />
            </svg>
            <span x-text="isFavorited ? 'Tersimpan di Favorit' : 'Simpan ke Favorit'"></span>
          </button>
        </div>
      </div>

    </div>

  </div>
</section>

<!-- Detailed Tabs & Content Section (Rule #58 & Rule #78 Compliant: Flat Underline Tabs) -->
<section class="py-10 bg-white dark:bg-gray-900 min-h-screen">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

    <!-- Tab Navigation (Clean Underline Style, No Box Backgrounds) -->
    <div class="flex items-center gap-6 sm:gap-8 border-b border-gray-200 dark:border-gray-700 overflow-x-auto pb-1">
      <button type="button" 
              @click="activeTab = 'services'" 
              class="py-3 text-xs sm:text-sm transition flex items-center gap-2 cursor-pointer shrink-0"
              :class="activeTab === 'services' ? 'text-amber-600 dark:text-amber-400 font-black' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-bold'">
        <span :class="activeTab === 'services' ? 'underline underline-offset-8 decoration-2 decoration-amber-500 dark:decoration-amber-400' : ''">Paket Layanan</span>
        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold" 
              :class="activeTab === 'services' ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300'">
          {{ $validServices->count() }}
        </span>
      </button>

      <button type="button" 
              @click="activeTab = 'portfolio'" 
              class="py-3 text-xs sm:text-sm transition flex items-center gap-2 cursor-pointer shrink-0"
              :class="activeTab === 'portfolio' ? 'text-amber-600 dark:text-amber-400 font-black' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-bold'">
        <span :class="activeTab === 'portfolio' ? 'underline underline-offset-8 decoration-2 decoration-amber-500 dark:decoration-amber-400' : ''">Galeri Portofolio</span>
        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold" 
              :class="activeTab === 'portfolio' ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300'">
          {{ $photographer->portfolios->count() }}
        </span>
      </button>

      <button type="button" 
              @click="activeTab = 'availability'" 
              class="py-3 text-xs sm:text-sm transition flex items-center gap-2 cursor-pointer shrink-0"
              :class="activeTab === 'availability' ? 'text-amber-600 dark:text-amber-400 font-black' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-bold'">
        <span :class="activeTab === 'availability' ? 'underline underline-offset-8 decoration-2 decoration-amber-500 dark:decoration-amber-400' : ''">Kalender Ketersediaan</span>
      </button>

      <button type="button" 
              @click="activeTab = 'reviews'" 
              class="py-3 text-xs sm:text-sm transition flex items-center gap-2 cursor-pointer shrink-0"
              :class="activeTab === 'reviews' ? 'text-amber-600 dark:text-amber-400 font-black' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-bold'">
        <span :class="activeTab === 'reviews' ? 'underline underline-offset-8 decoration-2 decoration-amber-500 dark:decoration-amber-400' : ''">Ulasan Klien</span>
        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold" 
              :class="activeTab === 'reviews' ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300'">
          {{ $photographer->testimonials->count() }}
        </span>
      </button>

      <button type="button" 
              @click="activeTab = 'faqs'" 
              class="py-3 text-xs sm:text-sm transition flex items-center gap-2 cursor-pointer shrink-0"
              :class="activeTab === 'faqs' ? 'text-amber-600 dark:text-amber-400 font-black' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-bold'">
        <span :class="activeTab === 'faqs' ? 'underline underline-offset-8 decoration-2 decoration-amber-500 dark:decoration-amber-400' : ''">Tanya Jawab</span>
        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold" 
              :class="activeTab === 'faqs' ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300'">
          {{ $photographer->faqs->count() }}
        </span>
      </button>
    </div>

    <!-- TAB 1: PAKET LAYANAN & PRICE LIST -->
    <div x-show="activeTab === 'services'" class="space-y-6">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <div>
          <h2 class="text-xl font-black text-gray-900 dark:text-white">Pilihan Paket Layanan Sesi Foto</h2>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Pilih paket layanan pemotretan yang sesuai dengan kebutuhan acara dan anggaran Anda.</p>
        </div>
      </div>
      
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($validServices as $srv)
          <div class="bg-white dark:bg-gray-800 p-6 sm:p-7 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-xs flex flex-col justify-between space-y-6 hover:shadow-md transition">
            <div class="space-y-4">
              <div class="space-y-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400 block">Paket Sesi</span>
                <h3 class="text-lg font-extrabold text-gray-900 dark:text-white">{{ $srv->nama_layanan }}</h3>
                <p class="text-xs text-gray-500 leading-relaxed">{{ $srv->deskripsi_layanan }}</p>
              </div>

              <div class="text-2xl font-black text-gray-900 dark:text-white">
                Rp {{ number_format((float) ($srv->tarif_harga ?? 0), 0, ',', '.') }}
              </div>

              <!-- Included Features List -->
              @if($srv->details->count() > 0)
                <ul class="space-y-2 pt-2 border-t border-gray-100 dark:border-gray-700 text-xs text-gray-700 dark:text-gray-300">
                  @foreach($srv->details as $det)
                    <li class="flex items-center gap-2">
                      <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                      <span>{{ $det->nama_fitur }} @if($det->tarif_harga > 0) (+Rp {{ number_format($det->tarif_harga, 0, ',', '.') }}) @endif</span>
                    </li>
                  @endforeach
                </ul>
              @endif
            </div>

            <a href="{{ url('/booking/create?photographer_id=' . $photographer->id . '&service_id=' . $srv->id) }}" 
               class="w-full py-3 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs rounded-xl transition text-center shadow-xs block cursor-pointer">
              Pilih Paket & Booking
            </a>
          </div>
        @empty
          <div class="col-span-full bg-white dark:bg-gray-800 p-12 text-center rounded-2xl border border-gray-200 dark:border-gray-700 space-y-3">
            <h4 class="text-sm font-bold text-gray-900 dark:text-white">Belum Ada Paket Layanan</h4>
            <p class="text-xs text-gray-400">Fotografer ini belum mempublikasikan paket layanan harga.</p>
          </div>
        @endforelse
      </div>
    </div>

    <!-- TAB 2: GALERI PORTOFOLIO (RULE #75 COMPLIANT: FULL-WIDTH VERTICAL STACKED ALBUM CARDS) -->
    <div x-show="activeTab === 'portfolio'" class="space-y-6">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <div>
          <h2 class="text-xl font-black text-gray-900 dark:text-white">Karya & Portofolio Fotografi</h2>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Koleksi album tematik hasil pemotretan resmi mitra fotografer.</p>
        </div>
      </div>

      <!-- Loop Each Distinct Portfolio Album (Full-Width Vertical Stacked Cards) -->
      <div class="space-y-8 sm:space-y-10">
        @forelse($photographer->portfolios as $port)
          @php
            $photos = $port->medias;
            $pCount = $photos->count();
            $albumImagesJson = json_encode($photos->pluck('media')->toArray());
            $mainPhoto = $photos->first();
            $subPhotos = $photos->slice(1);
            $thumbList = $subPhotos->take(3);
          @endphp

          <div class="bg-white dark:bg-gray-800 rounded-[26px] p-4 sm:p-6 border border-gray-200/80 dark:border-gray-700/80 shadow-xs hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300 space-y-4">
            
            @if(!$mainPhoto)
              <div class="p-8 text-center text-xs text-gray-400">Album ini belum memiliki foto.</div>
            @else
              <!-- Foto Pertama Yang Besar (Full Width di Atas - Rule #75) -->
              <div class="relative w-full overflow-hidden bg-gray-100 dark:bg-gray-900 rounded-[20px] aspect-[16/9] sm:aspect-[2.2/1] cursor-pointer group"
                   @click="openLightbox('{{ $mainPhoto->media }}', '{{ addslashes($port->judul) }}', 0, {{ $albumImagesJson }})"
                   tabindex="0"
                   @keydown.enter="openLightbox('{{ $mainPhoto->media }}', '{{ addslashes($port->judul) }}', 0, {{ $albumImagesJson }})">
                <img src="{{ $mainPhoto->media }}" 
                     alt="{{ $port->judul }}" 
                     class="w-full h-full object-cover group-hover:scale-102 transition-transform duration-500 ease-out" 
                     onerror="this.onerror=null;this.src=window.SVG_IMAGE_FALLBACK||'{{ asset('images/placeholder.jpg') }}';" />
                
                <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                  <span class="px-4 py-2 rounded-xl bg-black/60 text-white text-xs font-bold backdrop-blur-xs flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607zM10.5 7.5v6m3-3h-6"/></svg>
                    <span>Buka Foto Penuh</span>
                  </span>
                </div>
              </div>

              <!-- 3 Thumbnail di Bawah Berjejer Horizontal -->
              @if($thumbList->count() > 0)
                <div class="grid grid-cols-3 gap-3 sm:gap-4">
                  @foreach($thumbList as $med)
                    @php
                      $actualIndex = $photos->search(fn($item) => $item->id === $med->id);
                      $lightboxTargetIdx = $actualIndex !== false ? $actualIndex : $loop->index + 1;
                    @endphp
                    <div class="relative w-full aspect-[16/10] sm:aspect-[16/9] overflow-hidden bg-gray-100 dark:bg-gray-900 rounded-[14px] cursor-pointer group/thumb"
                         @click.stop="openLightbox('{{ $med->media }}', '{{ addslashes($port->judul) }}', {{ $lightboxTargetIdx }}, {{ $albumImagesJson }})"
                         tabindex="0"
                         @keydown.enter="openLightbox('{{ $med->media }}', '{{ addslashes($port->judul) }}', {{ $lightboxTargetIdx }}, {{ $albumImagesJson }})">
                      <img src="{{ $med->media }}" 
                           alt="{{ $port->judul }}" 
                           class="w-full h-full object-cover group-hover/thumb:scale-105 transition-transform duration-500 ease-out"
                           onerror="this.onerror=null;this.src=window.SVG_IMAGE_FALLBACK||'{{ asset('images/placeholder.jpg') }}';" />

                      <!-- Overlay "+X Foto Lainnya" pada thumbnail ke-3 jika foto lebih dari 4 -->
                      @if($loop->iteration === 3 && $pCount > 4)
                        <div class="absolute inset-0 bg-gray-950/70 backdrop-blur-[2px] flex flex-col items-center justify-center text-white transition group-hover/thumb:bg-gray-950/80">
                          <span class="text-sm sm:text-base font-black tracking-tight">+{{ $pCount - 4 }}</span>
                          <span class="text-[10px] font-semibold text-gray-300">Foto Lainnya</span>
                        </div>
                      @endif
                    </div>
                  @endforeach
                </div>
              @endif

              <!-- Meta Footer Card (Judul, Deskripsi, Jumlah Foto) -->
              <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pt-2 px-1 border-t border-gray-100 dark:border-gray-700/60">
                <div class="space-y-0.5">
                  <strong class="text-base sm:text-lg font-bold text-gray-900 dark:text-white block">
                    {{ $port->judul }}
                  </strong>
                  @if(!empty(trim($port->deskripsi)))
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-2" title="{{ $port->deskripsi }}">
                      {{ $port->deskripsi }}
                    </p>
                  @endif
                </div>

                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 dark:text-gray-400 shrink-0">
                  <svg class="w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <rect x="3" y="3" width="18" height="18" rx="3"></rect>
                    <circle cx="9" cy="9" r="1.6"></circle>
                    <path d="m21 15-5-5L5 21"></path>
                  </svg>
                  <span>{{ $pCount }} Foto</span>
                </span>
              </div>
            @endif

          </div>
        @empty
          <div class="col-span-full bg-white dark:bg-gray-800 p-12 text-center rounded-[26px] border border-gray-200/80 dark:border-gray-700/80 space-y-3">
            <div class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-gray-700 text-gray-400 flex items-center justify-center mx-auto">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5z"></path></svg>
            </div>
            <h4 class="text-sm font-bold text-gray-900 dark:text-white">Belum Ada Portofolio</h4>
            <p class="text-xs text-gray-400">Fotografer ini belum menambahkan album karya portofolio.</p>
          </div>
        @endforelse
      </div>
    </div>

    <!-- TAB 3: KALENDER KETERSEDIAAN (INTERACTIVE AVAILABILITY CALENDAR GRID) -->
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
             
             // In Indonesia, Monday is day 1, Sunday is day 7
             let startDayOfWeek = firstDay.getDay() - 1;
             if (startDayOfWeek < 0) startDayOfWeek = 6;
             
             const daysInMonth = lastDay.getDate();
             const days = [];
             
             // Previous month filler days
             const prevMonthLastDay = new Date(this.year, this.month, 0).getDate();
             for (let i = startDayOfWeek - 1; i >= 0; i--) {
               days.push({
                 dayNumber: prevMonthLastDay - i,
                 isCurrentMonth: false,
                 isPast: true,
                 dateStr: ''
               });
             }
             
             // Current month days
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
             
             // Next month filler days to complete grid rows
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
      
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <div>
          <h2 class="text-xl font-black text-gray-900 dark:text-white">Jadwal & Ketersediaan Fotografer</h2>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Pantau tanggal ketersediaan sesi foto secara real-time dan pilih tanggal yang masih terbuka untuk reservasi.</p>
        </div>
        <a href="{{ url('/booking/create?photographer_id=' . $photographer->id) }}" 
           class="px-5 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs rounded-xl shadow-xs transition inline-flex items-center gap-2 self-start sm:self-auto cursor-pointer">
          <span>Booking Langsung</span>
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
        </a>
      </div>

      <!-- Main Calendar Container Card -->
      <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-200 dark:border-gray-700/80 shadow-xs p-6 sm:p-8 space-y-6">
        
        <!-- Month Navigation Header -->
        <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700/60 pb-4">
          <div class="flex items-center gap-3">
            <h3 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight" 
                x-text="monthNames[month] + ' ' + year">
            </h3>
            <button type="button" 
                    @click="year = new Date().getFullYear(); month = new Date().getMonth(); selectedDate = null; selectedDateInfo = null;"
                    class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 text-[10px] font-bold text-gray-600 dark:text-gray-300 transition cursor-pointer">
              Hari Ini
            </button>
          </div>

          <!-- Prev/Next Controls -->
          <div class="flex items-center gap-1.5">
            <button type="button" 
                    @click="prevMonth()" 
                    title="Bulan Sebelumnya" 
                    class="w-9 h-9 rounded-xl border border-gray-200 dark:border-gray-700 flex items-center justify-center text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-750 transition cursor-pointer">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
            </button>
            <button type="button" 
                    @click="nextMonth()" 
                    title="Bulan Berikutnya" 
                    class="w-9 h-9 rounded-xl border border-gray-200 dark:border-gray-700 flex items-center justify-center text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-750 transition cursor-pointer">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            </button>
          </div>
        </div>

        <!-- 7-Day Name Grid Headers -->
        <div class="grid grid-cols-7 gap-2 text-center text-xs font-black uppercase tracking-wider text-gray-400 dark:text-gray-500">
          <template x-for="dname in dayNames" :key="dname">
            <div class="py-1" x-text="dname"></div>
          </template>
        </div>

        <!-- Days Grid -->
        <div class="grid grid-cols-7 gap-2 sm:gap-3">
          <template x-for="(day, idx) in calendarDays" :key="idx">
            <div @click="selectDay(day)"
                 class="min-h-[70px] sm:min-h-[85px] p-2 rounded-2xl border transition-all duration-200 flex flex-col justify-between"
                 :class="{
                   'opacity-30 pointer-events-none border-transparent bg-gray-50/30 dark:bg-gray-850/30': !day.isCurrentMonth,
                   'opacity-40 border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-850 cursor-not-allowed': day.isCurrentMonth && day.isPast,
                   'bg-rose-50/80 dark:bg-rose-950/40 border-rose-200 dark:border-rose-900/60 text-rose-900 dark:text-rose-200 cursor-pointer': day.isCurrentMonth && !day.isPast && day.status === 'blocked',
                   'bg-amber-50/80 dark:bg-amber-950/40 border-amber-200 dark:border-amber-900/60 text-amber-900 dark:text-amber-200 cursor-pointer': day.isCurrentMonth && !day.isPast && day.status === 'booked',
                   'bg-emerald-50/50 dark:bg-emerald-950/30 border-emerald-200/80 dark:border-emerald-900/50 hover:bg-emerald-100/60 dark:hover:bg-emerald-900/40 text-emerald-950 dark:text-emerald-200 cursor-pointer hover:shadow-xs': day.isCurrentMonth && !day.isPast && day.status === 'available',
                   'ring-2 ring-amber-500 shadow-md': selectedDate === day.dateStr
                 }">
              
              <!-- Date Number & Today Dot -->
              <div class="flex items-center justify-between">
                <span class="text-xs sm:text-sm font-black" 
                      :class="day.isToday ? 'w-6 h-6 rounded-full bg-amber-400 text-gray-950 flex items-center justify-center shadow-xs' : ''"
                      x-text="day.dayNumber">
                </span>
                
                <template x-if="day.isCurrentMonth && !day.isPast && day.status === 'available'">
                  <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                </template>
                <template x-if="day.isCurrentMonth && !day.isPast && day.status === 'booked'">
                  <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                </template>
                <template x-if="day.isCurrentMonth && !day.isPast && day.status === 'blocked'">
                  <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                </template>
              </div>

              <!-- Mini Status Label in Cell -->
              <div class="text-[10px] font-bold truncate">
                <template x-if="day.isCurrentMonth && !day.isPast && day.status === 'available'">
                  <span class="text-emerald-700 dark:text-emerald-400">Tersedia</span>
                </template>
                <template x-if="day.isCurrentMonth && !day.isPast && day.status === 'booked'">
                  <span class="text-amber-700 dark:text-amber-400">Terisi Sesi</span>
                </template>
                <template x-if="day.isCurrentMonth && !day.isPast && day.status === 'blocked'">
                  <span class="text-rose-700 dark:text-rose-400">Libur</span>
                </template>
              </div>

            </div>
          </template>
        </div>

        <!-- Selected Day Detail Drawer / Panel -->
        <template x-if="selectedDateInfo">
          <div class="p-5 rounded-2xl border transition-all"
               :class="{
                 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-200 dark:border-emerald-800': selectedDateInfo.status === 'available',
                 'bg-amber-50 dark:bg-amber-950/40 border-amber-200 dark:border-amber-800': selectedDateInfo.status === 'booked',
                 'bg-rose-50 dark:bg-rose-950/40 border-rose-200 dark:border-rose-800': selectedDateInfo.status === 'blocked'
               }">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
              <div class="space-y-1">
                <div class="flex items-center gap-2">
                  <span class="text-xs font-black uppercase tracking-wider text-gray-500 dark:text-gray-400">Rincian Tanggal:</span>
                  <span class="text-sm font-black text-gray-900 dark:text-white" x-text="selectedDateInfo.dateStr"></span>
                </div>
                
                <template x-if="selectedDateInfo.status === 'available'">
                  <p class="text-xs text-emerald-800 dark:text-emerald-300 font-medium">
                    Jadwal pada tanggal ini <strong>TERSEDIA PENUH</strong> untuk sesi pemotretan. Anda dapat langsung memilih paket dan memesan jadwal ini.
                  </p>
                </template>

                <template x-if="selectedDateInfo.status === 'booked'">
                  <div class="text-xs text-amber-900 dark:text-amber-200 font-medium space-y-1">
                    <p>Tanggal ini telah memiliki jadwal sesi pemotretan aktif:</p>
                    <template x-for="bk in selectedDateInfo.bookedInfo" :key="bk.time_formatted">
                      <div class="flex items-center gap-2 font-bold">
                        <span>•</span>
                        <span x-text="bk.service_name + ' (' + bk.time_formatted + ')'"></span>
                      </div>
                    </template>
                  </div>
                </template>

                <template x-if="selectedDateInfo.status === 'blocked'">
                  <p class="text-xs text-rose-800 dark:text-rose-300 font-medium">
                    Fotografer tidak menerima sesi pemotretan pada tanggal ini: <strong x-text="selectedDateInfo.blockedInfo ? selectedDateInfo.blockedInfo.keterangan : 'Libur / Cuti Pribadi'"></strong>.
                  </p>
                </template>
              </div>

              <template x-if="selectedDateInfo.status === 'available'">
                <a :href="'{{ url('/booking/create?photographer_id=' . $photographer->id) }}&booking_date=' + selectedDateInfo.dateStr"
                   class="px-5 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs rounded-xl shadow-xs transition shrink-0 inline-flex items-center gap-2 cursor-pointer">
                  <span>Pilih Tanggal Ini & Booking</span>
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                </a>
              </template>
            </div>
          </div>
        </template>

        <!-- Legend Bar -->
        <div class="flex flex-wrap items-center justify-between gap-4 pt-4 border-t border-gray-100 dark:border-gray-700/60 text-xs">
          <div class="flex flex-wrap items-center gap-4 sm:gap-6 text-gray-600 dark:text-gray-300 font-semibold">
            <div class="flex items-center gap-2">
              <span class="w-3 h-3 rounded-full bg-emerald-500 shadow-xs"></span>
              <span>Tersedia untuk Booking</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="w-3 h-3 rounded-full bg-amber-400 shadow-xs"></span>
              <span>Terisi Sesi Klien</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="w-3 h-3 rounded-full bg-rose-500 shadow-xs"></span>
              <span>Libur / Tutup Operasional</span>
            </div>
          </div>

          <span class="text-[11px] text-gray-400">Klik tanggal pada kalender untuk melihat detail</span>
        </div>

      </div>

      <!-- Jam Kerja Operasional Mingguan Card -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 p-6 space-y-3">
        <h3 class="text-sm font-extrabold text-gray-900 dark:text-white flex items-center gap-2">
          <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <span>Standar Jam Kerja Operasional Studio</span>
        </h3>
        <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
          Sesi pemotretan reguler dilayani setiap hari kerja pukul <strong>08:00 — 18:00 WITA/WIB</strong>. Pemesanan sesi khusus malam hari atau luar pulau dapat dikoordinasikan secara langsung melalui menu chat fotografer.
        </p>
      </div>

    </div>

    <!-- TAB 4: ULASAN & RATING KLIEN -->
    <div x-show="activeTab === 'reviews'" class="space-y-6">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <div>
          <h2 class="text-xl font-black text-gray-900 dark:text-white">Ulasan & Rating Dari Klien</h2>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Ulasan resmi dari Klien yang telah mereservasi dan menyelesaikan sesi pemotretan.</p>
        </div>
      </div>

      <!-- Rating Summary Breakdown Card -->
      <div class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-3xl border border-gray-200 dark:border-gray-700 shadow-xs grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
        <!-- Average Score Column -->
        <div class="md:col-span-4 text-center md:border-r border-gray-100 dark:border-gray-700 md:pr-6 space-y-2">
          <div class="text-5xl font-black text-gray-900 dark:text-white tracking-tight">{{ $avgRating }}</div>
          <div class="flex items-center justify-center gap-1 text-amber-400">
            @for($i = 1; $i <= 5; $i++)
              <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
            @endfor
          </div>
          <p class="text-xs text-gray-500 font-medium">Berdasarkan {{ $totalReviews }} ulasan resmi</p>
        </div>

        <!-- Star Bars Column -->
        <div class="md:col-span-8 space-y-2">
          @foreach([5, 4, 3, 2, 1] as $star)
            @php
              $cnt = $starCounts[$star] ?? 0;
              $pct = $totalReviews > 0 ? ($cnt / $totalReviews) * 100 : 0;
            @endphp
            <div class="flex items-center gap-3 text-xs">
              <span class="w-12 font-bold text-gray-700 dark:text-gray-300 flex items-center gap-1 shrink-0">
                <span>{{ $star }}</span>
                <span class="text-amber-400">★</span>
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
          <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-xs space-y-3">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-300 font-black text-sm flex items-center justify-center">
                  {{ substr($t->client->nama ?? ($t->client->user->nama ?? 'Klien'), 0, 1) }}
                </div>
                <div>
                  <h4 class="text-sm font-extrabold text-gray-900 dark:text-white">
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
                  <svg class="w-4 h-4 {{ $s <= $t->rating ? 'fill-current' : 'text-gray-200 dark:text-gray-700 fill-current' }}" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                @endfor
              </div>
            </div>

            <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed">
              {{ $t->komentar }}
            </p>
          </div>
        @empty
          <div class="bg-white dark:bg-gray-800 p-12 text-center rounded-2xl border border-gray-200 dark:border-gray-700 space-y-2">
            <h4 class="text-sm font-bold text-gray-900 dark:text-white">Belum Ada Ulasan Klien</h4>
            <p class="text-xs text-gray-400">Ulasan resmi dari Klien yang telah mereservasi sesi foto akan ditampilkan di sini.</p>
          </div>
        @endforelse
      </div>
    </div>

    <!-- TAB 5: TANYA JAWAB / FAQ -->
    <div x-show="activeTab === 'faqs'" class="space-y-6" x-data="{ openFaq: null }">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <div>
          <h2 class="text-xl font-black text-gray-900 dark:text-white">Tanya Jawab Seputar Layanan Fotografer</h2>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Pertanyaan umum yang sering ditanyakan klien sebelum mereservasi sesi pemotretan.</p>
        </div>
        <a href="{{ route('chat.start', $photographer->id) }}" class="text-xs font-extrabold text-amber-600 dark:text-amber-400 hover:underline inline-flex items-center gap-1.5 self-start sm:self-auto">
          <span>Ada pertanyaan lain? Tanya via Chat</span>
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
        </a>
      </div>

      @if($photographer->faqs->count() > 0)
        <div class="space-y-3">
          @foreach($photographer->faqs as $faq)
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-xs overflow-hidden transition">
              <button type="button" 
                      @click="openFaq = (openFaq === {{ $faq->id }} ? null : {{ $faq->id }})" 
                      class="w-full p-5 text-left flex items-center justify-between gap-4 cursor-pointer hover:bg-gray-50/60 dark:hover:bg-gray-750 transition">
                <span class="text-sm font-extrabold text-gray-900 dark:text-white flex items-center gap-3">
                  <span class="w-6 h-6 rounded-full bg-amber-100 dark:bg-amber-950/80 text-amber-700 dark:text-amber-400 font-black text-xs flex items-center justify-center shrink-0">
                    Q
                  </span>
                  <span>{{ $faq->pertanyaan }}</span>
                </span>
                <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 shrink-0" 
                     :class="openFaq === {{ $faq->id }} ? 'rotate-180 text-amber-500' : ''" 
                     fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                </svg>
              </button>

              <div x-show="openFaq === {{ $faq->id }}" 
                   x-cloak 
                   x-transition:enter="transition ease-out duration-200"
                   x-transition:enter-start="opacity-0 -translate-y-1"
                   x-transition:enter-end="opacity-100 translate-y-0"
                   class="px-5 pb-5 pt-1 border-t border-gray-100 dark:border-gray-700/60">
                <div class="flex items-start gap-3 text-xs leading-relaxed text-gray-600 dark:text-gray-300">
                  <span class="w-6 h-6 rounded-full bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-400 font-black text-xs flex items-center justify-center shrink-0 mt-0.5">
                    A
                  </span>
                  <p class="pt-0.5 whitespace-pre-line">{{ $faq->jawaban }}</p>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      @else
        <div class="bg-white dark:bg-gray-800 p-12 rounded-2xl border border-gray-200 dark:border-gray-700 text-center space-y-3">
          <h3 class="text-sm font-extrabold text-gray-900 dark:text-white">Belum Ada Tanya Jawab Khusus</h3>
          <p class="text-xs text-gray-500 max-w-sm mx-auto">Fotografer belum menambahkan daftar FAQ mandiri. Anda dapat mengajukan pertanyaan langsung seputar sesi foto melalui room chat.</p>
          <a href="{{ route('chat.start', $photographer->id) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs rounded-xl shadow-xs transition">
            <span>Tanya Langsung via Chat</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
          </a>
        </div>
      @endif
    </div>

  </div>

  <!-- Fullscreen Lightbox Modal (Rule #75 Compliant) -->
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
            class="absolute top-4 right-4 sm:top-6 sm:right-6 w-11 h-11 rounded-full bg-white/10 hover:bg-white/25 text-white flex items-center justify-center transition cursor-pointer z-50">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>

    <!-- Prev Image Button -->
    <button type="button" 
            x-show="lightboxImages.length > 1" 
            @click.stop="prevImage()" 
            title="Foto Sebelumnya (Panah Kiri)" 
            class="absolute left-3 sm:left-6 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-white/10 hover:bg-white/25 text-white flex items-center justify-center transition cursor-pointer z-50">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
    </button>

    <!-- Next Image Button -->
    <button type="button" 
            x-show="lightboxImages.length > 1" 
            @click.stop="nextImage()" 
            title="Foto Berikutnya (Panah Kanan)" 
            class="absolute right-3 sm:right-6 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-white/10 hover:bg-white/25 text-white flex items-center justify-center transition cursor-pointer z-50">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
    </button>

    <!-- Main Image Display Container -->
    <div class="relative max-w-5xl max-h-[85vh] flex flex-col items-center justify-center space-y-3" @click.outside="lightboxOpen = false">
      <img :src="lightboxSrc" 
           :alt="lightboxTitle" 
           draggable="false"
           class="max-w-full max-h-[75vh] object-contain rounded-xl shadow-2xl select-none" />
      
      <div class="text-center text-white space-y-1 px-4">
        <p class="text-sm font-bold truncate max-w-lg" x-text="lightboxTitle"></p>
        <template x-if="lightboxImages.length > 1">
          <p class="text-xs text-gray-400 font-mono" x-text="(lightboxIndex + 1) + ' / ' + lightboxImages.length"></p>
        </template>
      </div>
    </div>
  </div>

</section>
@endsection
