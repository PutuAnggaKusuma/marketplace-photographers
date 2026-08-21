@extends('layouts.app')

@section('title', 'Katalog Lengkap Fotografer — LensMatch')

@section('content')
<!-- Header Banner Section -->
<section class="bg-gradient-to-b from-amber-50/80 via-amber-50/30 to-transparent dark:from-gray-800/80 dark:via-gray-800/30 dark:to-transparent pt-12 pb-10 border-b border-gray-100 dark:border-gray-800">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-3">
    <!-- Breadcrumb / Kicker -->
    <div class="flex items-center justify-center gap-2 text-xs font-bold text-gray-500 dark:text-gray-400">
      <a href="{{ url('/fotografer') }}" class="hover:text-amber-500 transition">Eksplorasi</a>
      <span>/</span>
      <span class="text-amber-600 dark:text-amber-400">Katalog Lengkap</span>
    </div>

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
<section class="py-12 bg-white dark:bg-gray-900/50 min-h-screen">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
    
    <!-- Filter Panel Container (Spacious Card Padding p-8 sm:p-9 lg:p-10) -->
    <div class="bg-white dark:bg-gray-800 p-8 sm:p-9 lg:p-10 rounded-3xl shadow-xs border border-gray-200/80 dark:border-gray-700/80 space-y-6">
      <form id="filterForm" action="{{ route('public.photographers.katalog') }}" method="GET" class="space-y-6">
        
        <!-- Search Keyword Row (Search Icon on the RIGHT side) -->
        <div class="relative">
          <input type="text" name="q" value="{{ $searchKeyword }}" placeholder="Cari nama fotografer, nama studio, atau kata kunci bio..." class="w-full pl-5 pr-12 py-3.5 sm:py-4 text-xs sm:text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-amber-400 focus:outline-none placeholder-gray-400 font-medium" />
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

          <!-- HARGA MAKS Input -->
          <div>
            <label class="block text-[10px] font-black uppercase tracking-wider text-gray-400 mb-1.5">HARGA MAKS</label>
            <input type="number" name="price_max" value="{{ $priceMax }}" placeholder="Mis. 3.500.000" class="w-full px-4 py-3 text-xs sm:text-sm font-bold text-gray-800 dark:text-white bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-amber-400" />
          </div>

        </div>

        <!-- Submit & Reset Button Row -->
        <div class="flex items-center justify-end gap-3 pt-2">
          @if($searchKeyword || $selectedProvince || $selectedCity || $selectedCategory || $priceMax || $locationKeyword)
            <a href="{{ route('public.photographers.katalog') }}" class="text-xs font-bold text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white px-4 py-2.5 transition">
              Reset filter
            </a>
          @endif
          <button type="submit" class="px-7 py-3 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs sm:text-sm rounded-xl shadow-xs transition duration-200">
            Terapkan Filter
          </button>
        </div>

      </form>
    </div>

    <!-- Results Count & Sorting Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-2">
      <div>
        <h2 class="text-xl sm:text-2xl font-black text-gray-900 dark:text-white tracking-tight">
          Menampilkan {{ $photographers->total() }} fotografer
        </h2>
        @if($searchKeyword || $selectedCategory || $selectedProvince)
          <p class="text-xs text-gray-500 dark:text-gray-400 pt-1">
            Hasil pencarian dengan filter yang Anda tentukan.
          </p>
        @endif
      </div>
      <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">
        DIURUTKAN BERDASARKAN RATING
      </div>
    </div>

    <!-- Photographers Grid (3 Columns Desktop, 2 Columns Tablet, 1 Column Mobile) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
      @forelse($photographers as $photographer)
        @php
          $lowestPrice = $photographer->services->min('tarif_harga') ?? 1500000;
          $coverPortfolio = $photographer->portfolios->first()?->medias?->first()?->media ?? 'https://images.unsplash.com/photo-1537633552985-df8429e8048b?w=600&q=80';
          $avatarUrl = $photographer->foto_url ?? ($photographer->foto ? (str_starts_with($photographer->foto, 'http') ? $photographer->foto : asset('storage/' . $photographer->foto)) : 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400&q=80');
          $rating = $photographer->rating_average ?? 4.9;
          $reviewCount = $photographer->testimonials->count();
        @endphp

        <!-- Photographer Card (Tight, Proportional Distance between Categories and Price) -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-200/80 dark:border-gray-700/80 overflow-hidden shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between group h-full">
          
          <div>
            <!-- Top Cover Image Frame -->
            <div class="relative h-56 w-full overflow-hidden bg-gray-100 dark:bg-gray-700 shrink-0">
              <img src="{{ $coverPortfolio }}" alt="{{ $photographer->nama }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" />
              
              <!-- Rating Badge Top-Right -->
              <div class="absolute top-4 right-4 bg-white/95 dark:bg-gray-900/95 text-gray-900 dark:text-white text-xs font-black px-2.5 py-1 rounded-lg flex items-center gap-1 shadow-sm">
                <svg class="w-3.5 h-3.5 text-amber-400 fill-current" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                <span>{{ $rating }}</span>
                <span class="text-[10px] text-gray-400 font-medium">({{ $reviewCount }})</span>
              </div>
            </div>

            <!-- Card Body Content (No excessive bottom padding) -->
            <div class="px-6 sm:px-7 pt-4 pb-0">
              
              <!-- Location (Subtle & Compact Above Name) -->
              <div class="flex items-center gap-1.5 text-[11px] text-gray-400 dark:text-gray-400 font-medium mb-2.5">
                <svg class="w-3.5 h-3.5 text-gray-400 dark:text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                </svg>
                <span class="truncate">{{ $photographer->city->name ?? 'Indonesia' }}</span>
              </div>

              <!-- Avatar & Name Row -->
              <div class="flex items-center gap-3">
                <img src="{{ $avatarUrl }}" alt="{{ $photographer->nama }}" class="w-10 h-10 rounded-xl object-cover border-2 border-amber-400 shadow-xs shrink-0" />
                <h3 class="font-extrabold text-base sm:text-lg text-gray-900 dark:text-white truncate group-hover:text-amber-500 transition leading-snug">
                  {{ $photographer->nama }}
                </h3>
              </div>

              <!-- Bio Description -->
              <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed line-clamp-2 mt-2.5">
                {{ $photographer->deskripsi_bio ?? 'Fotografer profesional berpengalaman dengan peralatan kamera premium siap mengabadikan momen terbaik Anda.' }}
              </p>

              <!-- Category Tags Row -->
              <div class="flex flex-wrap gap-1.5 pt-2.5 items-center">
                @foreach($photographer->categories->take(5) as $cat)
                  <span class="px-2.5 py-1 bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 font-extrabold text-[10px] rounded-lg border border-amber-200/60 dark:border-amber-800/60 uppercase tracking-wider">
                    {{ $cat->nama_kategori }}
                  </span>
                @endforeach
              </div>

            </div>
          </div>

          <!-- Bottom Section: Price & Full-Width Stacked Button (Tight Proportional Distance from Categories) -->
          <div class="px-6 sm:px-7 pt-3.5 pb-6 sm:pb-7 space-y-3 mt-auto">
            
            <!-- Price -->
            <div class="space-y-0.5">
              <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Mulai Dari</span>
              <p class="text-gray-900 dark:text-white font-black text-base sm:text-lg leading-tight">
                Rp {{ number_format($lowestPrice, 0, ',', '.') }}
              </p>
            </div>

            <!-- Full-Width Action Button -->
            <div class="pt-0.5">
              <a href="{{ url('/fotografer/' . $photographer->id) }}" class="inline-flex items-center justify-center gap-1.5 w-full text-center py-3 bg-[#222222] dark:bg-gray-700 text-white hover:bg-amber-400 hover:text-gray-900 rounded-xl text-xs font-extrabold transition duration-200 shadow-xs">
                <span>Lihat Profil</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
              </a>
            </div>

          </div>

        </div>
      @empty
        <!-- Empty State Container -->
        <div class="col-span-full bg-white dark:bg-gray-800 p-12 sm:p-16 rounded-3xl border border-gray-200 dark:border-gray-700 text-center space-y-4 shadow-xs">
          <div class="w-16 h-16 mx-auto rounded-full bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold text-2xl mb-2">
            📸
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
    @if($photographers->hasPages())
      <div class="pt-6 flex justify-center">
        {{ $photographers->links('partials.public.pagination') }}
      </div>
    @endif

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
</script>
@endsection