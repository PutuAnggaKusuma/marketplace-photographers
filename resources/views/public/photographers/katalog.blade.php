@extends('layouts.app')

@section('title', 'Katalog Lengkap Fotografer — LensMatch')

@section('content')
<!-- Header Banner Section -->
<section class="bg-gradient-to-b from-amber-50/80 via-amber-50/30 to-transparent dark:from-gray-800/80 dark:via-gray-800/30 dark:to-transparent pt-12 pb-10 border-b border-gray-100 dark:border-gray-800">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-3">
    <!-- Centered Main Title -->
    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-gray-900 dark:text-white tracking-tight leading-tight max-w-3xl mx-auto">
      Katalog Seluruh Fotografer
    </h1>

    <!-- Centered Subtext -->
    <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 max-w-2xl mx-auto leading-relaxed">
      Gunakan filter lokasi, kategori sesi foto, dan rentang harga untuk menemukan fotografer profesional terverifikasi yang paling sesuai dengan kebutuhan Anda.
    </p>
  </div>
</section>

<!-- Filter & Catalog Section -->
<section class="py-12 bg-white dark:bg-gray-900/50 min-h-screen"
         x-data="{
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
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
    
    <!-- Filter Panel Container (Spacious Card Padding p-8 sm:p-9 lg:p-10) -->
    <div class="bg-white dark:bg-gray-800 p-8 sm:p-9 lg:p-10 rounded-3xl shadow-xs border border-gray-200/80 dark:border-gray-700/80 space-y-6">
      <form id="filterForm" action="{{ route('public.photographers.katalog') }}" method="GET" class="space-y-6">
        <input type="hidden" name="sort" id="sortInput" value="{{ $selectedSort }}">
        @if($locationKeyword)
          <input type="hidden" name="location" value="{{ $locationKeyword }}">
        @endif
        
        <!-- Search Keyword Row (Search Icon on the RIGHT side) -->
        <div class="relative">
          <input type="text" name="q" value="{{ $searchKeyword }}" placeholder="Cari nama fotografer, keahlian, atau kata kunci bio..." class="w-full pl-5 pr-12 py-3.5 sm:py-4 text-xs sm:text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-amber-400 focus:outline-none placeholder-gray-400 font-medium" />
          <button type="submit" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-amber-500 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
              <circle cx="11" cy="11" r="8"></circle>
              <path stroke-linecap="round" d="M21 21l-4.35-4.35"></path>
            </svg>
          </button>
        </div>

        <!-- 4-Column Grid Filter with Uppercase Labels -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
          
          <!-- PROVINSI Dropdown -->
          <div>
            <label class="block text-[10px] font-black uppercase tracking-wider text-gray-400 mb-1.5">PROVINSI</label>
            @php
              $currentProvName = 'Semua Provinsi';
              if ($selectedProvince) {
                $foundP = $provinces->firstWhere('code', $selectedProvince);
                if ($foundP) $currentProvName = $foundP->name;
              }
            @endphp
            <div class="relative" x-data="{ open: false, selectedCode: '{{ $selectedProvince }}', selectedText: '{{ $currentProvName }}' }">
              <input type="hidden" name="province" :value="selectedCode" id="provinceInput">
              <button type="button" @click="open = !open" class="w-full pl-4 pr-10 py-3 text-xs sm:text-sm font-bold text-left text-gray-800 dark:text-white bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-amber-400 flex items-center justify-between">
                <span class="truncate" x-text="selectedText"></span>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                </div>
              </button>

              <!-- Custom Options List Container -->
              <div x-show="open" @click.away="open = false" x-transition class="absolute z-50 w-full mt-1.5 max-h-60 overflow-y-auto bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl py-1">
                <div @click="selectedCode = ''; selectedText = 'Semua Provinsi'; open = false; fetchCities('');" class="px-4 py-2.5 text-xs text-gray-800 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 border-b border-gray-100 dark:border-gray-700/60 transition cursor-pointer font-bold">
                  Semua Provinsi
                </div>
                @foreach($provinces as $prov)
                  <div @click="selectedCode = '{{ $prov->code }}'; selectedText = '{{ addslashes($prov->name) }}'; open = false; fetchCities('{{ $prov->code }}');" class="px-4 py-2.5 text-xs text-gray-800 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 border-b border-gray-100 dark:border-gray-700/60 transition cursor-pointer last:border-b-0 font-medium">
                    {{ $prov->name }}
                  </div>
                @endforeach
              </div>
            </div>
          </div>

          <!-- KOTA / KABUPATEN Dropdown -->
          <div>
            <label class="block text-[10px] font-black uppercase tracking-wider text-gray-400 mb-1.5">KOTA / KABUPATEN</label>
            @php
              $currentCityName = 'Semua Kota';
              if ($selectedCity) {
                $foundC = $cities->firstWhere('code', $selectedCity);
                if ($foundC) $currentCityName = $foundC->name;
              }
            @endphp
            <div class="relative" x-data="{ open: false, selectedCode: '{{ $selectedCity }}', selectedText: '{{ $currentCityName }}' }" id="cityDropdownContainer">
              <input type="hidden" name="city" :value="selectedCode" id="cityInput">
              <button type="button" @click="open = !open" class="w-full pl-4 pr-10 py-3 text-xs sm:text-sm font-bold text-left text-gray-800 dark:text-white bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-amber-400 flex items-center justify-between">
                <span class="truncate" x-text="selectedText" id="cityLabel"></span>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                </div>
              </button>

              <!-- Custom Options List Container -->
              <div x-show="open" @click.away="open = false" x-transition class="absolute z-50 w-full mt-1.5 max-h-60 overflow-y-auto bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl py-1" id="cityOptionsList">
                <div @click="selectedCode = ''; selectedText = 'Semua Kota'; open = false;" class="px-4 py-2.5 text-xs text-gray-800 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 border-b border-gray-100 dark:border-gray-700/60 transition cursor-pointer font-bold">
                  Semua Kota
                </div>
                @foreach($cities as $c)
                  <div @click="selectedCode = '{{ $c->code }}'; selectedText = '{{ addslashes($c->name) }}'; open = false;" class="px-4 py-2.5 text-xs text-gray-800 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 border-b border-gray-100 dark:border-gray-700/60 transition cursor-pointer last:border-b-0 font-medium">
                    {{ $c->name }}
                  </div>
                @endforeach
              </div>
            </div>
          </div>

          <!-- KATEGORI SESI Dropdown -->
          <div>
            <label class="block text-[10px] font-black uppercase tracking-wider text-gray-400 mb-1.5">KATEGORI SESI</label>
            @php
              $currentCatName = 'Semua Kategori';
              if ($selectedCategory) {
                if (is_numeric($selectedCategory)) {
                  $foundCat = $categories->firstWhere('id', $selectedCategory);
                  if ($foundCat) $currentCatName = $foundCat->nama_kategori;
                } else {
                  $foundCat = $categories->firstWhere('slug', $selectedCategory);
                  if ($foundCat) $currentCatName = $foundCat->nama_kategori;
                }
              }
            @endphp
            <div class="relative" x-data="{ open: false, selectedCode: '{{ $selectedCategory }}', selectedText: '{{ $currentCatName }}' }">
              <input type="hidden" name="category" :value="selectedCode" id="categoryInput">
              <button type="button" @click="open = !open" class="w-full pl-4 pr-10 py-3 text-xs sm:text-sm font-bold text-left text-gray-800 dark:text-white bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-amber-400 flex items-center justify-between">
                <span class="truncate" x-text="selectedText"></span>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                </div>
              </button>

              <!-- Custom Options List Container -->
              <div x-show="open" @click.away="open = false" x-transition class="absolute z-50 w-full mt-1.5 max-h-60 overflow-y-auto bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl py-1">
                <div @click="selectedCode = ''; selectedText = 'Semua Kategori'; open = false;" class="px-4 py-2.5 text-xs text-gray-800 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 border-b border-gray-100 dark:border-gray-700/60 transition cursor-pointer font-bold">
                  Semua Kategori
                </div>
                @foreach($categories as $cat)
                  <div @click="selectedCode = '{{ $cat->id }}'; selectedText = '{{ addslashes($cat->nama_kategori) }}'; open = false;" class="px-4 py-2.5 text-xs text-gray-800 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 border-b border-gray-100 dark:border-gray-700/60 transition cursor-pointer last:border-b-0 font-medium">
                    {{ $cat->nama_kategori }}
                  </div>
                @endforeach
              </div>
            </div>
          </div>

          <!-- RATING MINIMAL Dropdown -->
          <div>
            <label class="block text-[10px] font-black uppercase tracking-wider text-gray-400 mb-1.5">RATING MINIMAL</label>
            @php
              $currentRatingLabel = 'Semua Rating';
              if ($ratingMin == '4.5') $currentRatingLabel = 'Rating 4.5 Ke Atas';
              elseif ($ratingMin == '4.0') $currentRatingLabel = 'Rating 4.0 Ke Atas';
              elseif ($ratingMin == '3.5') $currentRatingLabel = 'Rating 3.5 Ke Atas';
            @endphp
            <div class="relative" x-data="{ open: false, selectedCode: '{{ $ratingMin }}', selectedText: '{{ $currentRatingLabel }}' }">
              <input type="hidden" name="rating_min" :value="selectedCode">
              <button type="button" @click="open = !open" class="w-full pl-4 pr-10 py-3 text-xs sm:text-sm font-bold text-left text-gray-800 dark:text-white bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-amber-400 flex items-center justify-between">
                <span class="truncate" x-text="selectedText"></span>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                </div>
              </button>

              <div x-show="open" @click.away="open = false" x-transition class="absolute z-50 w-full mt-1.5 max-h-60 overflow-y-auto bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl py-1">
                <div @click="selectedCode = ''; selectedText = 'Semua Rating'; open = false;" class="px-4 py-2.5 text-xs text-gray-800 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 border-b border-gray-100 dark:border-gray-700/60 transition cursor-pointer font-bold">
                  Semua Rating
                </div>
                <div @click="selectedCode = '4.5'; selectedText = 'Rating 4.5 Ke Atas'; open = false;" class="px-4 py-2.5 text-xs text-gray-800 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 border-b border-gray-100 dark:border-gray-700/60 transition cursor-pointer font-medium">
                  Rating 4.5 Ke Atas
                </div>
                <div @click="selectedCode = '4.0'; selectedText = 'Rating 4.0 Ke Atas'; open = false;" class="px-4 py-2.5 text-xs text-gray-800 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 border-b border-gray-100 dark:border-gray-700/60 transition cursor-pointer font-medium">
                  Rating 4.0 Ke Atas
                </div>
                <div @click="selectedCode = '3.5'; selectedText = 'Rating 3.5 Ke Atas'; open = false;" class="px-4 py-2.5 text-xs text-gray-800 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition cursor-pointer font-medium">
                  Rating 3.5 Ke Atas
                </div>
              </div>
            </div>
          </div>

        </div>

        <!-- Row 2: Price Range & Action Buttons -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 pt-1 border-t border-gray-100 dark:border-gray-700/60">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-w-md w-full">
            <div>
              <label class="block text-[10px] font-black uppercase tracking-wider text-gray-400 mb-1.5">HARGA MIN (RP)</label>
              <input type="number" name="price_min" value="{{ $priceMin }}" placeholder="Contoh: 500000" class="w-full px-4 py-3 text-xs sm:text-sm font-bold text-gray-800 dark:text-white bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-amber-400" />
            </div>
            <div>
              <label class="block text-[10px] font-black uppercase tracking-wider text-gray-400 mb-1.5">HARGA MAKS (RP)</label>
              <input type="number" name="price_max" value="{{ $priceMax }}" placeholder="Contoh: 5000000" class="w-full px-4 py-3 text-xs sm:text-sm font-bold text-gray-800 dark:text-white bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-amber-400" />
            </div>
          </div>

          <!-- Submit & Reset Button Row -->
          <div class="flex items-center justify-end gap-3 shrink-0">
            @if($searchKeyword || $selectedProvince || $selectedCity || $selectedCategory || $priceMin || $priceMax || $ratingMin || $locationKeyword || ($selectedSort && $selectedSort !== 'rekomendasi'))
              <a href="{{ route('public.photographers.katalog') }}" class="text-xs font-bold text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white px-4 py-2.5 transition">
                Reset filter
              </a>
            @endif
            <button type="submit" class="px-7 py-3 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs sm:text-sm rounded-xl shadow-xs transition duration-200">
              Terapkan Filter
            </button>
          </div>
        </div>

      </form>
    </div>

    <!-- Results Count & Interactive Sorting Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-2">
      <div>
        <h2 class="text-xl sm:text-2xl font-black text-gray-900 dark:text-white tracking-tight">
          Menampilkan {{ $photographers->total() }} fotografer
        </h2>
        @if($searchKeyword || $selectedCategory || $selectedProvince || $selectedCity || $priceMin || $priceMax || $ratingMin || $locationKeyword)
          <p class="text-xs text-gray-500 dark:text-gray-400 pt-1">
            Hasil pencarian @if($locationKeyword)lokasi <span class="font-bold text-gray-800 dark:text-gray-200">"{{ $locationKeyword }}"</span> @endif dengan filter yang Anda tentukan.
          </p>
        @endif
      </div>

      <!-- Interactive Sorting Dropdown (Standard Marketplace Pattern) -->
      <div class="flex items-center gap-2.5 self-start sm:self-auto">
        <span class="text-xs font-bold text-gray-500 dark:text-gray-400 shrink-0">Urutkan:</span>
        <div class="relative" x-data="{ 
          open: false, 
          selectedSort: '{{ $selectedSort }}', 
          selectedLabel: '{{ $sortLabels[$selectedSort] ?? 'Rekomendasi' }}' 
        }">
          <button type="button" @click="open = !open" class="px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-xs font-bold text-gray-800 dark:text-white flex items-center gap-2 shadow-2xs hover:border-amber-400 transition">
            <span x-text="selectedLabel"></span>
            <svg class="w-3.5 h-3.5 text-gray-400 transition-transform duration-200" :class="open ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
          </button>

          <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 z-50 w-52 mt-1.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl py-1 text-xs">
            @foreach($sortLabels as $sKey => $sLabel)
              <div @click="applySort('{{ $sKey }}')" class="px-4 py-2.5 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 cursor-pointer font-medium flex items-center justify-between {{ $selectedSort === $sKey ? 'text-amber-600 font-bold bg-amber-50/50 dark:bg-amber-950/30' : 'text-gray-700 dark:text-gray-300' }}">
                <span>{{ $sLabel }}</span>
                @if($selectedSort === $sKey)
                  <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                @endif
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>

    <!-- Dynamic AJAX Container for Photographers & Pagination -->
    <div id="photographers-katalog-ajax-container" class="space-y-10 transition-opacity duration-200">
      <!-- Photographers Grid (3 Columns Desktop, 2 Columns Tablet, 1 Column Mobile) -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
      @forelse($photographers as $photographer)
        @php
          $lowestPrice = $photographer->services->where('tarif_harga', '>', 0)->min('tarif_harga');
          $coverPortfolio = $photographer->portfolios->first()?->medias?->first()?->media;
          $avatarUrl = $photographer->foto ? (str_starts_with($photographer->foto, 'http') ? $photographer->foto : asset('storage/' . $photographer->foto)) : null;
          $rating = $photographer->rating_average;
          $reviewCount = $photographer->testimonials->count();
        @endphp

        <!-- Photographer Card (Tight, Proportional Distance between Categories and Price) -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-200/80 dark:border-gray-700/80 overflow-hidden shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between group h-full">
          
          <div>
            <!-- Top Cover Image Frame -->
            <div class="relative h-56 w-full overflow-hidden bg-gray-100 dark:bg-gray-700 shrink-0">
              @if($coverPortfolio)
                <img src="{{ $coverPortfolio }}" alt="{{ $photographer->nama }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" />
              @else
                <div class="w-full h-full flex flex-col items-center justify-center bg-gray-100 dark:bg-gray-800 text-gray-400">
                  <svg class="w-10 h-10 mb-1" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
                  <span class="text-[10px] font-semibold">Belum ada portofolio</span>
                </div>
              @endif

              <!-- Bookmark Favorit Button Top-Left -->
              @php
                $isFav = in_array($photographer->id, $favoritedIds ?? []);
              @endphp
              <div class="absolute top-4 left-4 z-10" x-data="{ isFavorited: {{ $isFav ? 'true' : 'false' }}, loading: false }">
                <button type="button" 
                        @click.stop="
                          if (!{{ Auth::check() ? 'true' : 'false' }}) {
                            window.dispatchEvent(new CustomEvent('open-login-modal', {
                              detail: {
                                message: 'Untuk dapat menyimpan fotografer ini ke daftar favorit Anda, Anda harus masuk ke akun LensMatch terlebih dahulu.',
                                redirectUrl: '{{ url()->full() }}'
                              }
                            }));
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
                        :title="isFavorited ? 'Hapus dari Favorit' : 'Simpan ke Favorit'"
                        class="w-8 h-8 rounded-full bg-white/95 dark:bg-gray-900/95 flex items-center justify-center shadow-md transition-all duration-200 hover:scale-110 active:scale-95 cursor-pointer"
                        :class="isFavorited ? 'text-amber-500' : 'text-gray-400 hover:text-amber-500'">
                  <svg class="w-4 h-4 transition-colors" :class="isFavorited ? 'fill-current text-amber-500' : 'fill-none stroke-current stroke-2'" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z" />
                  </svg>
                </button>
              </div>
              
              <!-- Rating Badge Top-Right -->
              <div class="absolute top-4 right-4 bg-white/95 dark:bg-gray-900/95 text-gray-900 dark:text-white text-xs font-black px-2.5 py-1 rounded-lg flex items-center gap-1 shadow-sm">
                @if($rating)
                  <svg class="w-3.5 h-3.5 text-amber-400 fill-current" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                  <span>{{ $rating }}</span>
                  <span class="text-[10px] text-gray-400 font-medium">({{ $reviewCount }})</span>
                @else
                  <span class="text-amber-500 text-[11px] font-bold">Baru</span>
                @endif
              </div>
            </div>

            <!-- Card Body Content (No excessive bottom padding) -->
            <div class="px-6 sm:px-7 pt-4 pb-0">
              
              <!-- Location (Subtle & Compact Above Name) -->
              <div class="flex items-center gap-1.5 text-[11px] text-gray-400 dark:text-gray-400 font-light mb-2.5">
                <svg class="w-3.5 h-3.5 text-gray-400 dark:text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                </svg>
                <span class="truncate uppercase">{{ $photographer->city->name ?? ($photographer->alamat ?? '-') }}</span>
              </div>

              <!-- Avatar & Name Row -->
              <div class="flex items-center gap-3">
                @if($avatarUrl)
                  <img src="{{ $avatarUrl }}" alt="{{ $photographer->nama }}" class="w-10 h-10 rounded-xl object-cover border-2 border-amber-400 shadow-xs shrink-0" />
                @else
                  <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-amber-300 text-white font-black text-xs flex items-center justify-center border-2 border-amber-400 shadow-xs shrink-0">
                    {{ strtoupper(substr($photographer->nama ?? 'F', 0, 2)) }}
                  </div>
                @endif
                <div class="flex-1 min-w-0">
                  <h3 class="font-extrabold text-base sm:text-lg text-gray-900 dark:text-white group-hover:text-amber-500 transition leading-snug truncate">
                    {{ $photographer->nama }}
                  </h3>
                </div>
              </div>

              <!-- Categories as Plain Text (Under Profile, Comma Separated) -->
              @if($photographer->categories && $photographer->categories->isNotEmpty())
                <div class="mt-2">
                  <p class="text-xs font-light text-amber-700 dark:text-amber-300 leading-snug">
                    {{ $photographer->categories->pluck('nama_kategori')->join(', ') }}
                  </p>
                </div>
              @endif

              <!-- Format Metrik Inline Teks (Standar SweetEscape / Upwork - Clean Typography) -->
              @php
                $metrics = $photographer->editorial_metrics;
              @endphp
              @if(!empty($metrics))
                <div class="flex items-center flex-wrap gap-x-2 gap-y-1 mt-2.5 text-xs text-gray-500 dark:text-gray-400 font-medium">
                  @foreach($metrics as $idx => $m)
                    @if($idx > 0)
                      <span class="text-gray-300 dark:text-gray-600 select-none">•</span>
                    @endif
                    @if($m['type'] === 'rating')
                      <span class="font-bold text-gray-900 dark:text-white flex items-center gap-1">
                        @if($rating)
                          <span class="text-amber-500">★</span>
                        @endif
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

              <!-- Bio Description (Under Categories with clear margin, max 500 chars) -->
              <div class="mt-4 pt-1">
                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                  {{ !empty(trim($photographer->deskripsi_bio ?? '')) ? \Illuminate\Support\Str::limit($photographer->deskripsi_bio, 180) : 'Fotografer profesional terverifikasi siap mengabadikan momen terbaik Anda.' }}
                </p>
              </div>

            </div>
          </div>

          <!-- Bottom Section: Price & Full-Width Stacked Button (Tight Proportional Distance from Categories) -->
          <div class="px-6 sm:px-7 pt-3.5 pb-6 sm:pb-7 space-y-3 mt-auto">
            
            <!-- Price -->
            <div class="space-y-0.5">
              <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Tarif Layanan</span>
              @if($lowestPrice)
                <p class="text-gray-900 dark:text-white font-black text-base sm:text-lg leading-tight">
                  Mulai Rp {{ number_format($lowestPrice, 0, ',', '.') }}
                </p>
              @else
                <p class="text-gray-400 dark:text-gray-500 font-semibold text-xs leading-tight">
                  Belum ada paket
                </p>
              @endif
            </div>

            <!-- Full-Width Action Button -->
            <div class="pt-0.5">
              <a href="{{ url('/fotografer/' . $photographer->id) }}" class="inline-flex items-center justify-center gap-1.5 w-full text-center py-3 bg-[#222222] dark:bg-gray-700 text-white hover:bg-amber-400 hover:text-gray-900 rounded-xl text-xs font-extrabold transition duration-200 shadow-xs">
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
            <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z"/></svg>
          </div>
          <h3 class="text-xl font-black text-gray-900 dark:text-white">Tidak Ada Fotografer Ditemukan</h3>
          <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 max-w-md mx-auto leading-relaxed">
            Tidak ada fotografer yang cocok dengan kriteria pencarian Anda. Coba ubah kata kunci atau reset filter pencarian.
          </p>
          <div class="pt-2">
            <a href="{{ route('public.photographers.katalog') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs rounded-xl transition">
              <span>Reset Semua Filter</span>
            </a>
          </div>
        </div>
      @endforelse
    </div>

    <!-- Pagination Container -->
      @if($photographers->total() > 0)
        <div class="pt-10">
          {{ $photographers->links('partials.public.pagination') }}
        </div>
      @endif
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
          <p class="text-xs sm:text-[13px] text-gray-600 dark:text-gray-300 leading-relaxed text-center" x-text="loginModalMessage">
          </p>

          <!-- Action Button -->
          <div class="pt-1">
            <a :href="loginRedirectUrl ? '{{ route('login') }}?redirect=' + encodeURIComponent(loginRedirectUrl) : '{{ route('login') }}'" class="w-full py-3 px-4 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs sm:text-sm rounded-xl shadow-xs transition hover:scale-[1.01] flex items-center justify-center cursor-pointer">
              Masuk ke Akun
            </a>
          </div>

        </div>
      </div>
    </div>

  </div>
</section>

<!-- Dynamic City Fetching Script -->
<script>
function fetchCities(provinceCode) {
    const cityDropdown = document.getElementById('cityOptionsList');
    const cityLabel = document.getElementById('cityLabel');
    const cityInput = document.getElementById('cityInput');

    if (!cityDropdown) return;

    if (!provinceCode) {
        cityDropdown.innerHTML = '<div onclick="selectCityOption(\'\', \'Semua Kota\')" class="px-4 py-2.5 text-xs text-gray-800 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 border-b border-gray-100 dark:border-gray-700/60 transition cursor-pointer font-bold">Semua Kota</div>';
        if (cityLabel) cityLabel.textContent = 'Semua Kota';
        if (cityInput) cityInput.value = '';
        return;
    }

    fetch(`/api/cities/${provinceCode}`)
        .then(response => response.json())
        .then(cities => {
            let html = '<div onclick="selectCityOption(\'\', \'Semua Kota\')" class="px-4 py-2.5 text-xs text-gray-800 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 border-b border-gray-100 dark:border-gray-700/60 transition cursor-pointer font-bold">Semua Kota</div>';
            cities.forEach(city => {
                const escapedName = city.name.replace(/'/g, "\\'");
                html += `<div onclick="selectCityOption('${city.code}', '${escapedName}')" class="px-4 py-2.5 text-xs text-gray-800 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 border-b border-gray-100 dark:border-gray-700/60 transition cursor-pointer last:border-b-0 font-medium">${city.name}</div>`;
            });
            cityDropdown.innerHTML = html;
        })
        .catch(error => {
            console.error('Error fetching cities:', error);
        });
}

function selectCityOption(code, text) {
    const cityLabel = document.getElementById('cityLabel');
    const cityInput = document.getElementById('cityInput');
    if (cityLabel) cityLabel.textContent = text;
    if (cityInput) cityInput.value = code;

    // Trigger Alpine data update if needed
    const container = document.getElementById('cityDropdownContainer');
    if (container && container._x_dataStack) {
        container._x_dataStack[0].open = false;
        container._x_dataStack[0].selectedCode = code;
        container._x_dataStack[0].selectedText = text;
    }
}

function applySort(sortKey) {
    const sortInput = document.getElementById('sortInput');
    const form = document.getElementById('filterForm');
    if (sortInput && form) {
        sortInput.value = sortKey;
        form.submit();
    }
}
</script>
@endsection
