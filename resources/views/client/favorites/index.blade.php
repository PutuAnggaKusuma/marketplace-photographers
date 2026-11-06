@extends('layouts.app')

@section('title', 'Daftar Fotografer Favorit Saya — LensMatch')

@section('content')
<section class="py-12 bg-white dark:bg-gray-900 min-h-screen">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-black text-gray-900 dark:text-white flex items-center gap-3">
          <span>Fotografer Favorit Saya</span>
          @if($totalFavorites > 0)
            <span class="text-xs font-black px-2.5 py-1 rounded-full bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400">
              {{ $totalFavorites }} Disimpan
            </span>
          @endif
        </h1>
        <p class="text-xs sm:text-sm text-gray-500 mt-1">
          Koleksi mitra fotografer pilihan yang Anda simpan untuk perbandingan portofolio dan pemesanan mendatang.
        </p>
      </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
      <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 text-xs text-emerald-800 dark:text-emerald-300 font-bold flex items-center gap-2">
        <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
        <span>{{ session('success') }}</span>
      </div>
    @endif
    @if(session('warning'))
      <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/60 border border-amber-200 text-xs text-amber-800 dark:text-amber-300 font-bold flex items-center gap-2">
        <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <span>{{ session('warning') }}</span>
      </div>
    @endif

    <!-- Photographers Grid (3 Columns Desktop, 2 Columns Tablet, 1 Column Mobile) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8 items-stretch">
      @forelse($favorites as $photographer)
        @php
          $lowestPrice = $photographer->services->where('tarif_harga', '>', 0)->min('tarif_harga') ?? 1500000;
          $coverPortfolio = $photographer->portfolios->first()?->medias?->first()?->media ?? 'https://images.unsplash.com/photo-1537633552985-df8429e8048b?w=600&q=80';
          $avatarUrl = $photographer->foto_url;
          $rating = $photographer->rating_average;
          $reviewCount = $photographer->testimonials->count();
          $badges = $photographer->badges;
        @endphp

        <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-200/80 dark:border-gray-700/80 overflow-hidden shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between group h-full">
          <div>
            <!-- Top Cover Image Frame -->
            <div class="relative h-52 w-full overflow-hidden bg-gray-100 dark:bg-gray-700 shrink-0">
              <img src="{{ $coverPortfolio }}" alt="{{ $photographer->nama }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" />
              
              <!-- Remove from Favorite Button (Top Left) -->
              <form action="{{ route('client.favorites.toggle', $photographer->id) }}" method="POST" class="absolute top-4 left-4 z-10">
                @csrf
                <button type="submit" title="Hapus dari Favorit" class="w-9 h-9 rounded-full bg-white/90 dark:bg-gray-900/90 text-rose-500 hover:bg-rose-50 hover:text-rose-600 flex items-center justify-center shadow-md transition cursor-pointer">
                  <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                    <path d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z" />
                  </svg>
                </button>
              </form>

              <!-- Rating Badge (Top Right) -->
              <div class="absolute top-4 right-4 bg-white/95 dark:bg-gray-900/95 text-gray-900 dark:text-white text-xs font-black px-2.5 py-1 rounded-lg flex items-center gap-1 shadow-sm">
                <svg class="w-3.5 h-3.5 text-amber-400 fill-current" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                <span>{{ $rating }}</span>
                <span class="text-[10px] text-gray-400 font-medium">({{ $reviewCount }})</span>
              </div>
            </div>

            <!-- Card Body Content -->
            <div class="px-6 sm:px-7 pt-4 pb-0 space-y-3">
              <!-- Location -->
              <div class="flex items-center gap-1.5 text-[11px] text-gray-400 dark:text-gray-400 font-light">
                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                </svg>
                <span class="truncate uppercase">{{ $photographer->city->name ?? ($photographer->alamat ?? 'Indonesia') }}</span>
              </div>

              <!-- Avatar & Name Row -->
              <div class="flex items-center gap-3">
                <img src="{{ $avatarUrl }}" alt="{{ $photographer->nama }}" class="w-10 h-10 rounded-xl object-cover border-2 border-amber-400 shadow-xs shrink-0" />
                <div class="flex-1 min-w-0">
                  <h3 class="font-extrabold text-base text-gray-900 dark:text-white truncate">
                    {{ $photographer->nama }}
                  </h3>
                  @if($photographer->categories && $photographer->categories->isNotEmpty())
                    <p class="text-xs font-medium text-amber-700 dark:text-amber-400 truncate mt-0.5">
                      {{ $photographer->categories->pluck('nama_kategori')->join(', ') }}
                    </p>
                  @endif
                </div>
              </div>

              <!-- Format Metrik Inline Teks (Standar SweetEscape / Upwork) -->
              @php
                $metrics = $photographer->editorial_metrics;
              @endphp
              @if(!empty($metrics))
                <div class="flex items-center flex-wrap gap-x-2 gap-y-1 text-xs text-gray-500 dark:text-gray-400 font-medium">
                  @foreach($metrics as $idx => $m)
                    @if($idx > 0)
                      <span class="text-gray-300 dark:text-gray-600 select-none">•</span>
                    @endif
                    @if($m['type'] === 'rating')
                      <span class="font-bold text-gray-900 dark:text-white flex items-center gap-1">
                        <span class="text-amber-500">★</span>
                        <span>{{ $m['text'] }}</span>
                      </span>
                    @elseif($m['type'] === 'fast_response')
                      <span class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ $m['text'] }}</span>
                    @else
                      <span>{{ $m['text'] }}</span>
                    @endif
                  @endforeach
                </div>
              @endif

              <!-- Bio Snippet -->
              <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed line-clamp-2">
                {{ !empty(trim($photographer->deskripsi_bio)) ? $photographer->deskripsi_bio : 'Fotografer profesional terverifikasi siap mengabadikan momen terbaik Anda.' }}
              </p>
            </div>
          </div>

          <!-- Bottom Section: Price & Action Buttons -->
          <div class="px-6 sm:px-7 pt-4 pb-6 space-y-3 mt-auto border-t border-gray-100 dark:border-gray-700/60">
            <div class="flex items-center justify-between">
              <div>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Mulai Dari</span>
                <p class="text-gray-900 dark:text-white font-black text-sm sm:text-base">
                  Rp {{ number_format($lowestPrice, 0, ',', '.') }}
                </p>
              </div>
              <a href="{{ url('/fotografer/' . $photographer->id) }}" class="px-4 py-2 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs transition shadow-xs inline-flex items-center gap-1.5">
                <span>Lihat Profil</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
              </a>
            </div>
          </div>
        </div>
      @empty
        <!-- Empty State Container -->
        <div class="col-span-full bg-white dark:bg-gray-800 p-12 sm:p-16 rounded-3xl border border-gray-200 dark:border-gray-700 text-center space-y-4 shadow-xs">
          <div class="w-16 h-16 mx-auto rounded-full bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center mb-2">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z" /></svg>
          </div>
          <h3 class="text-xl font-black text-gray-900 dark:text-white">Belum Ada Fotografer Favorit</h3>
          <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 max-w-md mx-auto leading-relaxed">
            Anda belum menyimpan profil fotografer mana pun. Klik ikon bookmark pada fotografer yang Anda minati di katalog untuk menyimpannya di sini.
          </p>
          <div class="pt-2">
            <a href="{{ route('public.photographers.katalog') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs rounded-xl transition">
              <span>Jelajahi Katalog Fotografer</span>
            </a>
          </div>
        </div>
      @endforelse
    </div>

    <!-- Pagination -->
    @if($favorites->total() > 0)
      <div class="pt-6">
        {{ $favorites->links('partials.public.pagination') }}
      </div>
    @endif

  </div>
</section>
@endsection

