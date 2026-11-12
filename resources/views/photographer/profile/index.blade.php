@extends('layouts.photographer')

@section('title', 'Profil Fotografer — Dashboard')

@section('content')
@php
  $errors = $errors ?? new \Illuminate\Support\ViewErrorBag();
  $hasPasswordErrors = $errors->hasAny(['current_password', 'new_password', 'new_password_confirmation']);
  $initialTab = (session('active_tab') === 'password' || request('tab') === 'password' || $hasPasswordErrors) ? 'password' : 'profile';
@endphp

<div x-data="{ activeTab: '{{ $initialTab }}' }" class="space-y-6 w-full pb-10">

  <!-- Header Section -->
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
    <h1 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">Kelola Profil</h1>

    @if($photographer && $photographer->isPublishedToCatalog())
      <div class="flex flex-wrap items-center gap-x-2.5 gap-y-1 text-xs">
        <span class="font-bold text-emerald-600 dark:text-emerald-400">Tayang di Katalog</span>
        <span class="text-gray-300 dark:text-gray-600">/</span>
        <a href="{{ route('public.photographers.show', $photographer->id) }}"
           target="_blank"
           class="font-extrabold text-amber-600 dark:text-amber-400 hover:text-amber-500 underline underline-offset-4 decoration-amber-400 transition">
          Lihat Profil Publik
        </a>
      </div>
    @endif
  </div>

  <!-- Tab Navigasi Horizontal (Rule #78: Active Text Underline, Tanpa Divider Panjang) -->
  <div class="flex items-center gap-6 overflow-x-auto pb-1">
    <button type="button"
            @click="activeTab = 'profile'"
            :class="activeTab === 'profile' ? 'text-amber-600 dark:text-amber-400 font-black' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-bold'"
            class="py-2 text-xs sm:text-sm transition flex items-center gap-2 cursor-pointer shrink-0">
      <span :class="activeTab === 'profile' ? 'underline underline-offset-8 decoration-2 decoration-amber-500 dark:decoration-amber-400' : ''">Data Profil</span>
    </button>

    <button type="button"
            @click="activeTab = 'password'"
            :class="activeTab === 'password' ? 'text-amber-600 dark:text-amber-400 font-black' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-bold'"
            class="py-2 text-xs sm:text-sm transition flex items-center gap-2 cursor-pointer shrink-0">
      <span :class="activeTab === 'password' ? 'underline underline-offset-8 decoration-2 decoration-amber-500 dark:decoration-amber-400' : ''">Ubah Kata Sandi</span>
    </button>
  </div>

  <!-- TAB 1: Data Profil (Biodata, Domisili, Bio, Media, Rekening Bank) -->
  <div x-show="activeTab === 'profile'" @if($initialTab !== 'profile') x-cloak @endif class="space-y-6">

  @php
    $user = auth()->user();
    $rawFoto = $photographer?->foto ?: $user?->foto;
    $avatarUrl = $rawFoto ? (str_starts_with($rawFoto, 'http') ? $rawFoto : asset('storage/' . $rawFoto)) : '';
    
    $rawSampul = $photographer?->foto_sampul;
    $sampulUrl = $rawSampul ? (str_starts_with($rawSampul, 'http') ? $rawSampul : asset('storage/' . $rawSampul)) : '';
    
    $missingFields = $photographer?->getMissingProfileFields() ?? [];
    $missingCount = count($missingFields);
    $percentage = $photographer?->getProfileCompletionPercentage() ?? 0;
    $isVerified = $photographer ? $photographer->isVerified() : false;
    $isComplete = $photographer?->isProfileComplete() ?? false;
    $sectionStatus = $photographer?->getProfileSectionsStatus() ?? [
        'sections' => [],
        'completed_count' => 0,
        'total_sections' => 4,
        'incomplete_names' => []
    ];
  @endphp

  @if(!$isComplete)
    <!-- Indikator Kelengkapan Data Profil (Pure Text - Rule #67) -->
    <div class="bg-white dark:bg-gray-800 p-4 sm:p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-xs space-y-2">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
        <div class="flex items-center gap-2">
          <span class="font-bold text-gray-900 dark:text-white">Kelengkapan Profil</span>
          <span class="text-gray-500 dark:text-gray-400">({{ $sectionStatus['completed_count'] }}/{{ $sectionStatus['total_sections'] }} bagian)</span>
        </div>

        <div class="flex items-center gap-2">
          <span class="font-black text-gray-900 dark:text-white">{{ $percentage }}%</span>
          <span class="text-rose-600 dark:text-rose-400 font-semibold">Kurang {{ $missingCount }} data</span>
        </div>
      </div>

      @if(count($missingFields) > 0)
        <div class="pt-1 text-xs text-gray-600 dark:text-gray-400">
          <span class="font-bold text-gray-700 dark:text-gray-300">Belum diisi:</span>
          <span class="text-gray-500 dark:text-gray-400 ml-1 leading-relaxed">{{ implode(', ', array_values($missingFields)) }}.</span>
        </div>
      @endif
    </div>
  @endif

  <!-- Form Profil Fotografer -->
  <div x-data="photographerProfileForm()" class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-3xl border border-gray-200 dark:border-gray-700/80 shadow-xs">
    
    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" @submit="submitProfileForm($event)" novalidate class="space-y-6">
      @csrf

      <!-- SECTION 1: Kontak -->
      <div class="space-y-4">
        <h2 class="text-sm font-black text-gray-900 dark:text-white">
          1. Kontak
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Nama Lengkap -->
          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
              Nama Lengkap <span class="text-rose-500">*</span>
            </label>
            <input type="text" 
                   name="nama" 
                   x-ref="fieldNama"
                   x-model="formData.nama"
                   @input="touched.nama = true; validateNama()"
                   @blur="touched.nama = true; validateNama()"
                   placeholder="Masukkan nama lengkap" 
                   class="w-full px-4 py-2.5 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition"
                   :class="touched.nama && errors.nama ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500'">
            <p x-show="touched.nama && errors.nama" x-text="errors.nama" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
          </div>

          <!-- Email -->
          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
              Email <span class="text-rose-500">*</span>
            </label>
            <input type="email" 
                   name="email" 
                   x-ref="fieldEmail"
                   x-model="formData.email"
                   @input="touched.email = true; validateEmail()"
                   @blur="touched.email = true; validateEmail()"
                   placeholder="nama@email.com" 
                   class="w-full px-4 py-2.5 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition"
                   :class="touched.email && errors.email ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500'">
            <p x-show="touched.email && errors.email" x-text="errors.email" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
          </div>

          <!-- Nomor WhatsApp -->
          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
              Nomor WhatsApp <span class="text-rose-500">*</span>
            </label>
            <input type="tel" 
                   name="nomor_telepon" 
                   x-ref="fieldPhone"
                   x-model="formData.nomor_telepon"
                   inputmode="numeric"
                   pattern="[0-9]*"
                   @keypress="if (!/[0-9]/.test($event.key)) $event.preventDefault()"
                   @input="formData.nomor_telepon = (formData.nomor_telepon || '').replace(/\D/g, ''); touched.nomor_telepon = true; validatePhone()"
                   @blur="touched.nomor_telepon = true; validatePhone()"
                   placeholder="Contoh: 08xxxxxxxxxx" 
                   class="w-full px-4 py-2.5 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition"
                   :class="touched.nomor_telepon && errors.nomor_telepon ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500'">
            <p x-show="touched.nomor_telepon && errors.nomor_telepon" x-text="errors.nomor_telepon" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
          </div>

          <!-- Instagram -->
          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
              Instagram <span class="text-rose-500">*</span>
            </label>
            <input type="text" 
                   name="link_sosmed" 
                   x-ref="fieldSosmed"
                   x-model="formData.link_sosmed"
                   @input="touched.link_sosmed = true; validateSosmed()"
                   @blur="touched.link_sosmed = true; validateSosmed()"
                   placeholder="https://instagram.com/username atau @username" 
                   class="w-full px-4 py-2.5 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition"
                   :class="touched.link_sosmed && errors.link_sosmed ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500'">
            <p x-show="touched.link_sosmed && errors.link_sosmed" x-text="errors.link_sosmed" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
          </div>
        </div>
      </div>

      <!-- SECTION 2: Domisili & Kategori -->
      <div class="space-y-4 pt-2">
        <h2 class="text-sm font-black text-gray-900 dark:text-white">
          2. Domisili & Kategori
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Provinsi -->
          <div class="relative" x-ref="fieldProvince">
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
              Provinsi <span class="text-rose-500">*</span>
            </label>
            <input type="hidden" name="province_code" :value="formData.province_code">
            
            <button type="button" 
                    @click="provinceDropdownOpen = !provinceDropdownOpen; cityDropdownOpen = false; categoryDropdownOpen = false" 
                    class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition text-left cursor-pointer"
                    :class="touched.province_code && errors.province_code ? 'border-red-400 focus:ring-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500'">
              <span x-text="formData.province_name || 'Pilih Provinsi'" :class="!formData.province_name ? 'text-gray-400' : ''"></span>
              <svg class="w-4 h-4 text-gray-400 shrink-0 transition-transform duration-200" :class="provinceDropdownOpen ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
            </button>

            <div x-show="provinceDropdownOpen" 
                 @click.away="provinceDropdownOpen = false" 
                 x-cloak 
                 class="absolute z-30 mt-1.5 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl py-1 max-h-56 overflow-y-auto">
              @forelse($provinces as $prov)
                <button type="button" 
                        @click="selectProvince('{{ $prov->code }}', '{{ $prov->name }}')" 
                        class="w-full text-left px-4 py-2.5 text-xs text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition border-b border-gray-100 dark:border-gray-700/40 last:border-0 cursor-pointer"
                        :class="formData.province_code === '{{ $prov->code }}' ? 'bg-amber-50 dark:bg-amber-950/60 font-bold text-amber-600' : ''">
                  {{ $prov->name }}
                </button>
              @empty
                <div class="px-4 py-3 text-xs text-gray-400 text-center">Data provinsi belum tersedia.</div>
              @endforelse
            </div>
            <p x-show="touched.province_code && errors.province_code" x-text="errors.province_code" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
          </div>

          <!-- Kota / Kabupaten -->
          <div class="relative" x-ref="fieldCity">
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
              Kota / Kabupaten <span class="text-rose-500">*</span>
            </label>
            <input type="hidden" name="city_code" :value="formData.city_code">

            <button type="button" 
                    @click="if(formData.province_code) { cityDropdownOpen = !cityDropdownOpen; provinceDropdownOpen = false; categoryDropdownOpen = false; }" 
                    :disabled="!formData.province_code || loadingCities"
                    class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition text-left"
                    :class="!formData.province_code ? 'opacity-60 cursor-not-allowed border-gray-200 dark:border-gray-700' : (touched.city_code && errors.city_code ? 'border-red-400 focus:ring-red-400 cursor-pointer' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500 cursor-pointer')">
              <span x-text="loadingCities ? 'Memuat daftar kota...' : (formData.city_name || (formData.province_code ? 'Pilih Kota / Kabupaten' : 'Pilih provinsi terlebih dahulu'))"
                    :class="!formData.city_name ? 'text-gray-400' : ''"></span>
              <svg class="w-4 h-4 text-gray-400 shrink-0 transition-transform duration-200" :class="cityDropdownOpen ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
            </button>

            <div x-show="cityDropdownOpen" 
                 @click.away="cityDropdownOpen = false" 
                 x-cloak 
                 class="absolute z-30 mt-1.5 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl py-1 max-h-56 overflow-y-auto">
              <template x-if="cities.length === 0">
                <div class="px-4 py-3 text-xs text-gray-400 text-center">Tidak ada data kota tersedia.</div>
              </template>
              <template x-for="c in cities" :key="c.code">
                <button type="button" 
                        @click="selectCity(c.code, c.name)" 
                        class="w-full text-left px-4 py-2.5 text-xs text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition border-b border-gray-100 dark:border-gray-700/40 last:border-0 cursor-pointer"
                        :class="formData.city_code === c.code ? 'bg-amber-50 dark:bg-amber-950/60 font-bold text-amber-600' : ''"
                        x-text="c.name">
                </button>
              </template>
            </div>
            <p x-show="touched.city_code && errors.city_code" x-text="errors.city_code" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
          </div>

          <!-- Alamat -->
          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
              Alamat <span class="text-rose-500">*</span>
            </label>
            <input type="text" 
                   name="alamat" 
                   x-ref="fieldAlamat"
                   x-model="formData.alamat"
                   @input="touched.alamat = true; validateAlamat()"
                   @blur="touched.alamat = true; validateAlamat()"
                   placeholder="Masukkan alamat lengkap operasional" 
                   class="w-full px-4 py-2.5 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition"
                   :class="touched.alamat && errors.alamat ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500'">
            <p x-show="touched.alamat && errors.alamat" x-text="errors.alamat" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
          </div>

          <!-- Kategori (Sleek Multi-select Dropdown) -->
          <div class="relative" x-ref="fieldCategories">
            <input type="hidden" name="categories_submitted" value="1">
            <div class="flex items-center justify-between mb-1">
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">
                Kategori <span class="text-rose-500">*</span>
              </label>
              <span class="text-[11px] text-gray-400" x-text="formData.selectedCategories.length + ' dipilih'"></span>
            </div>

            <template x-for="catId in formData.selectedCategories" :key="catId">
              <input type="hidden" name="categories[]" :value="catId">
            </template>

            <button type="button" 
                    @click="categoryDropdownOpen = !categoryDropdownOpen; provinceDropdownOpen = false; cityDropdownOpen = false" 
                    class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs outline-none focus:ring-2 transition text-left cursor-pointer"
                    :class="touched.categories && errors.categories ? 'border-red-400 focus:ring-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500'">
              <span x-text="selectedCategorySummary" 
                    :class="formData.selectedCategories.length === 0 ? 'text-gray-400' : 'font-bold text-gray-900 dark:text-white truncate'"></span>
              <svg class="w-4 h-4 text-gray-400 shrink-0 transition-transform duration-200" :class="categoryDropdownOpen ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
            </button>

            <!-- Dropdown List of Categories with Checkmarks -->
            <div x-show="categoryDropdownOpen" 
                 @click.away="categoryDropdownOpen = false" 
                 x-cloak 
                 class="absolute z-30 mt-1.5 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl py-1 max-h-56 overflow-y-auto">
              @foreach($categories as $category)
                <button type="button" 
                        @click="toggleCategory({{ $category->id }})" 
                        class="w-full text-left px-4 py-2.5 text-xs text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition flex items-center justify-between gap-2 border-b border-gray-100 dark:border-gray-700/40 last:border-0"
                        :class="isCategoryLocked({{ $category->id }})
                            ? 'bg-amber-50/70 dark:bg-amber-950/60 font-bold text-amber-700 dark:text-amber-300 cursor-not-allowed'
                            : (formData.selectedCategories.includes({{ $category->id }}) ? 'bg-amber-50/70 dark:bg-amber-950/60 font-bold text-amber-600 cursor-pointer' : 'cursor-pointer')">
                  <span>{{ $category->nama_kategori }}</span>
                  <span class="flex items-center gap-2 shrink-0">
                    <span x-show="isCategoryLocked({{ $category->id }})"
                          x-text="getCategoryUsageCount({{ $category->id }}) + ' portofolio'"
                          class="text-[11px] font-medium text-gray-400 dark:text-gray-500"></span>
                    <svg x-show="formData.selectedCategories.includes({{ $category->id }})" class="w-4 h-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                  </span>
                </button>
              @endforeach
            </div>

            <p x-show="touched.categories && errors.categories" x-text="errors.categories" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>

            <!-- Selected Category Chips Display Under Dropdown -->
            <div x-show="formData.selectedCategories.length > 0" class="flex flex-wrap gap-1.5 pt-2">
              <template x-for="cat in allCategories.filter(c => formData.selectedCategories.includes(Number(c.id)))" :key="cat.id">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200/80 dark:border-amber-900/60"
                      :title="isCategoryLocked(cat.id) ? ('Dipakai oleh ' + getCategoryUsageCount(cat.id) + ' portofolio aktif') : ''">
                  <span x-text="cat.name"></span>
                  <span x-show="isCategoryLocked(cat.id)"
                        x-text="'(' + getCategoryUsageCount(cat.id) + ' portofolio)'"
                        class="text-[10px] font-semibold text-amber-600/80 dark:text-amber-400/80"></span>
                  <button type="button"
                          x-show="!isCategoryLocked(cat.id)"
                          @click="toggleCategory(cat.id)"
                          class="text-amber-600 hover:text-rose-500 transition cursor-pointer">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                  </button>
                </span>
              </template>
            </div>
          </div>
        </div>
      </div>

      <!-- SECTION 3: Bio -->
      <div class="space-y-2 pt-2">
        <h2 class="text-sm font-black text-gray-900 dark:text-white">
          3. Bio <span class="text-rose-500">*</span>
        </h2>
        <div>
          <textarea name="deskripsi_bio" 
                    x-ref="fieldBio"
                    x-model="formData.deskripsi_bio"
                    @input="touched.deskripsi_bio = true; validateBio()"
                    @blur="touched.deskripsi_bio = true; validateBio()"
                    rows="5" 
                    placeholder="Tuliskan pengalaman, keahlian, dan deskripsi singkat mengenai layanan Anda..." 
                    class="w-full min-h-[120px] px-4 py-3 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 resize-y leading-relaxed transition"
                    :class="touched.deskripsi_bio && errors.deskripsi_bio ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500'">{{ old('deskripsi_bio', $photographer?->deskripsi_bio) }}</textarea>
          <p x-show="touched.deskripsi_bio && errors.deskripsi_bio" x-text="errors.deskripsi_bio" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
        </div>
      </div>

      <!-- SECTION 4: Foto Profil & Sampul (Layout Vertikal & Card Ditinggikan - Rule #96) -->
      <div class="space-y-4 pt-2">
        <h2 class="text-sm font-black text-gray-900 dark:text-white">
          4. Foto Profil & Sampul <span class="text-rose-500">*</span>
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
          <!-- Foto Profil -->
          <div class="p-6 sm:p-7 rounded-2xl bg-gray-50 dark:bg-gray-900/60 border border-gray-200 dark:border-gray-700/80 space-y-4">
              <div class="flex items-center justify-between">
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">
                  Foto Profil <span class="text-rose-500">*</span>
                </label>
                <span class="text-[11px] text-gray-400">Rasio 1:1 Persegi</span>
              </div>

              <!-- Layout Vertikal: Preview di Atas, Dropzone Pure-Text di Bawah (Rule #22, #94, #96, #97) -->
              <div class="space-y-4">
                <div class="relative w-28 h-28 rounded-2xl overflow-hidden bg-gray-100 dark:bg-gray-800 border-2 shadow-2xs flex items-center justify-center transition cursor-pointer"
                     :class="uploadErrors.foto_file ? 'border-red-400 border-dashed' : (avatarPreview ? 'border-amber-400 border-solid group' : 'border-dashed border-gray-300 dark:border-gray-600 hover:border-amber-400')"
                     @click="if (avatarPreview) openImagePreview(avatarPreview, 'Foto Profil'); else $refs.avatarInput.click();">
                  <img :src="avatarPreview" 
                       x-show="avatarPreview" 
                       x-cloak
                       alt="Preview Foto Profil" 
                       class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                  <div x-show="avatarPreview" 
                       x-cloak
                       class="absolute inset-0 bg-black/45 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center text-white p-1">
                    <svg class="w-5 h-5 text-white mb-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607zM10.5 7.5v6m3-3h-6"/></svg>
                    <span class="text-[10px] font-bold">Pratinjau</span>
                  </div>
                  <div x-show="!avatarPreview" 
                       class="flex items-center justify-center text-gray-400 dark:text-gray-500">
                    <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                      <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                    </svg>
                  </div>
                </div>
                
                <div class="space-y-1.5">
                  <div x-ref="avatarDropzone"
                       tabindex="0"
                       role="button"
                       @click="$refs.avatarInput.click()"
                       @keydown.enter.prevent="$refs.avatarInput.click()"
                       @keydown.space.prevent="$refs.avatarInput.click()"
                       @dragover.prevent="avatarDragging = true"
                       @dragleave.prevent="avatarDragging = false"
                       @drop.prevent="handleAvatarDrop($event)"
                       class="w-full min-h-[112px] px-4 py-5 rounded-xl border-2 border-dashed flex flex-col items-center justify-center text-center gap-1.5 cursor-pointer transition outline-none"
                       :class="avatarDragging
                           ? 'border-amber-500 bg-amber-50/70 dark:bg-amber-950/25'
                           : (uploadErrors.foto_file
                               ? 'border-red-400 bg-red-50/40 dark:bg-red-950/20 focus:ring-2 focus:ring-red-400'
                               : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900/60 hover:border-amber-400 dark:hover:border-amber-500 hover:bg-amber-50/30 dark:hover:bg-amber-950/20 focus:ring-2 focus:ring-amber-500')">
                    <input type="file" name="foto_file" x-ref="avatarInput" accept="image/jpeg, image/png, image/webp" @change="handleAvatarFile($event)" class="sr-only">
                    <svg class="w-6 h-6 transition-colors" :class="avatarDragging ? 'text-amber-500' : 'text-gray-400 dark:text-gray-500'" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                    </svg>
                    <p class="text-xs font-bold text-gray-700 dark:text-gray-200">
                      Seret dan lepas di sini
                    </p>
                    <p class="text-[11px] text-gray-400">Format JPG, PNG, WEBP maksimal 10 MB.</p>
                  </div>
                  <p x-show="uploadErrors.foto_file" x-text="uploadErrors.foto_file" x-cloak class="text-[11px] font-bold text-red-500"></p>
                </div>
              </div>
          </div>

          <!-- Foto Sampul -->
          <div class="p-6 sm:p-7 rounded-2xl bg-gray-50 dark:bg-gray-900/60 border border-gray-200 dark:border-gray-700/80 space-y-4">
              <div class="flex items-center justify-between">
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">
                  Foto Sampul <span class="text-rose-500">*</span>
                </label>
                <span class="text-[11px] text-gray-400">Rasio Lanskap 16:9</span>
              </div>

              <!-- Layout Vertikal: Preview di Atas (Rasio 16:9 Tinggi & Proporsional), Dropzone Pure-Text di Bawah -->
              <div class="space-y-4">
                <div class="relative w-full h-44 sm:h-48 rounded-2xl overflow-hidden bg-gray-100 dark:bg-gray-800 border-2 shadow-2xs flex items-center justify-center transition cursor-pointer"
                     :class="uploadErrors.foto_sampul_file ? 'border-red-400 border-dashed' : (sampulPreview ? 'border-amber-400 border-solid group' : 'border-dashed border-gray-300 dark:border-gray-600 hover:border-amber-400')"
                     @click="if (sampulPreview) openImagePreview(sampulPreview, 'Foto Sampul'); else $refs.sampulInput.click();">
                  <img :src="sampulPreview" 
                       x-show="sampulPreview" 
                       x-cloak
                       alt="Preview Foto Sampul" 
                       class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-102">
                  <div x-show="sampulPreview" 
                       x-cloak
                       class="absolute inset-0 bg-black/45 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white p-2">
                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-black/60 backdrop-blur-xs text-xs font-bold text-white shadow-lg">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607zM10.5 7.5v6m3-3h-6"/></svg>
                      <span>Klik untuk Pratinjau Foto</span>
                    </span>
                  </div>
                  <div x-show="!sampulPreview" 
                       class="flex items-center justify-center text-gray-400 dark:text-gray-500">
                    <svg class="w-10 h-10 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                      <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                    </svg>
                  </div>
                </div>
                
                <div class="space-y-1.5">
                  <div x-ref="sampulDropzone"
                       tabindex="0"
                       role="button"
                       @click="$refs.sampulInput.click()"
                       @keydown.enter.prevent="$refs.sampulInput.click()"
                       @keydown.space.prevent="$refs.sampulInput.click()"
                       @dragover.prevent="sampulDragging = true"
                       @dragleave.prevent="sampulDragging = false"
                       @drop.prevent="handleSampulDrop($event)"
                       class="w-full min-h-[112px] px-4 py-5 rounded-xl border-2 border-dashed flex flex-col items-center justify-center text-center gap-1.5 cursor-pointer transition outline-none"
                       :class="sampulDragging
                           ? 'border-amber-500 bg-amber-50/70 dark:bg-amber-950/25'
                           : (uploadErrors.foto_sampul_file
                               ? 'border-red-400 bg-red-50/40 dark:bg-red-950/20 focus:ring-2 focus:ring-red-400'
                               : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900/60 hover:border-amber-400 dark:hover:border-amber-500 hover:bg-amber-50/30 dark:hover:bg-amber-950/20 focus:ring-2 focus:ring-amber-500')">
                    <input type="file" name="foto_sampul_file" x-ref="sampulInput" accept="image/jpeg, image/png, image/webp" @change="handleSampulFile($event)" class="sr-only">
                    <svg class="w-6 h-6 transition-colors" :class="sampulDragging ? 'text-amber-500' : 'text-gray-400 dark:text-gray-500'" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                    </svg>
                    <p class="text-xs font-bold text-gray-700 dark:text-gray-200">
                      Seret dan lepas di sini
                    </p>
                    <p class="text-[11px] text-gray-400">Rasio 16:9 format JPG, PNG, WEBP maksimal 20 MB.</p>
                  </div>
                  <p x-show="uploadErrors.foto_sampul_file" x-text="uploadErrors.foto_sampul_file" x-cloak class="text-[11px] font-bold text-red-500"></p>
                </div>
              </div>
          </div>
        </div>
      </div>

      <!-- SECTION 5: Rekening Bank -->
      <div class="p-5 sm:p-6 rounded-2xl bg-gray-50 dark:bg-gray-900/60 border border-gray-200 dark:border-gray-700/80 space-y-4">
        <div>
          <h2 class="text-sm font-black text-gray-900 dark:text-white">
            5. Rekening Bank <span class="text-rose-500">*</span>
          </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-1">
          <!-- Nama Bank -->
          <div class="relative" x-ref="fieldBank">
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
              Nama Bank <span class="text-rose-500">*</span>
            </label>
            <input type="hidden" name="nama_bank" :value="formData.nama_bank">
            <button type="button" 
                    @click="bankDropdownOpen = !bankDropdownOpen" 
                    class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl border bg-white dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition text-left cursor-pointer"
                    :class="touched.nama_bank && errors.nama_bank ? 'border-red-400 focus:ring-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500'">
              <span x-text="formData.nama_bank || 'Pilih Nama Bank'" :class="!formData.nama_bank ? 'text-gray-400' : ''"></span>
              <svg class="w-4 h-4 text-gray-400 shrink-0 transition-transform duration-200" :class="bankDropdownOpen ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
            </button>
            <div x-show="bankDropdownOpen" 
                 @click.away="bankDropdownOpen = false" 
                 x-cloak 
                 class="absolute z-20 mt-1 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg py-1 max-h-48 overflow-y-auto">
              <template x-for="b in banks" :key="b">
                <button type="button" 
                        @click="formData.nama_bank = b; bankDropdownOpen = false; touched.nama_bank = true; validateBank()" 
                        class="w-full text-left px-4 py-2 text-xs hover:bg-amber-50 dark:hover:bg-amber-950/40 text-gray-800 dark:text-gray-200 cursor-pointer"
                        :class="{'font-bold text-amber-600 dark:text-amber-400 bg-amber-50/50 dark:bg-amber-950/30': formData.nama_bank === b}"
                        x-text="b">
                </button>
              </template>
            </div>
            <p x-show="touched.nama_bank && errors.nama_bank" x-text="errors.nama_bank" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
          </div>

          <!-- Nomor Rekening -->
          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
              Nomor Rekening <span class="text-rose-500">*</span>
            </label>
            <input type="text" 
                   name="nomor_rekening" 
                   x-ref="fieldRekening"
                   x-model="formData.nomor_rekening"
                   inputmode="numeric"
                   pattern="[0-9]*"
                   @keypress="if (!/[0-9]/.test($event.key)) $event.preventDefault()"
                   @input="formData.nomor_rekening = (formData.nomor_rekening || '').replace(/\D/g, ''); touched.nomor_rekening = true; validateRekening()"
                   @blur="touched.nomor_rekening = true; validateRekening()"
                   placeholder="Masukkan nomor rekening" 
                   class="w-full px-4 py-2.5 rounded-xl border bg-white dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition"
                   :class="touched.nomor_rekening && errors.nomor_rekening ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500'">
            <p x-show="touched.nomor_rekening && errors.nomor_rekening" x-text="errors.nomor_rekening" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
          </div>

          <!-- Nama Pemilik Rekening -->
          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
              Nama Pemilik Rekening <span class="text-rose-500">*</span>
            </label>
            <input type="text" 
                   name="atas_nama_rekening" 
                   x-ref="fieldAtasNama"
                   x-model="formData.atas_nama_rekening"
                   @input="touched.atas_nama_rekening = true; validateAtasNama()"
                   @blur="touched.atas_nama_rekening = true; validateAtasNama()"
                   placeholder="Masukkan nama pemilik rekening" 
                   class="w-full px-4 py-2.5 rounded-xl border bg-white dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition"
                   :class="touched.atas_nama_rekening && errors.atas_nama_rekening ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500'">
            <p x-show="touched.atas_nama_rekening && errors.atas_nama_rekening" x-text="errors.atas_nama_rekening" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
          </div>
        </div>
      </div>

      <!-- Submit Button Group -->
      <div class="flex items-center justify-end pt-2">
        <button type="submit" class="px-6 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs rounded-xl shadow-xs transition cursor-pointer">
          Perbaharui Data
        </button>
      </div>
    </form>

    <!-- Modal Pratinjau Foto Resolusi Penuh (Rule #97: Standarisasi Pratinjau Modal Lightbox Media) -->
    <div x-show="imagePreviewModalOpen" 
         x-cloak 
         @keydown.escape.window="closeImagePreview()"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 backdrop-blur-md p-4 sm:p-6"
         tabindex="-1">
      
      <!-- Tombol Tutup -->
      <button type="button" 
              @click="closeImagePreview()" 
              title="Tutup (Esc)" 
              class="absolute top-4 right-4 sm:top-6 sm:right-6 w-11 h-11 rounded-full bg-white/10 hover:bg-white/25 text-white flex items-center justify-center transition cursor-pointer z-50">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>

      <!-- Container Gambar Utama -->
      <div class="relative max-w-5xl max-h-[90vh] flex flex-col items-center justify-center space-y-3" @click.outside="closeImagePreview()">
        <img :src="imagePreviewSrc" 
             :alt="imagePreviewTitle" 
             draggable="false"
             class="max-w-full max-h-[80vh] object-contain rounded-2xl shadow-2xl select-none border border-white/10">
        
        <div x-show="imagePreviewTitle" class="px-4 py-1.5 rounded-full bg-black/60 text-white/90 text-xs font-semibold backdrop-blur-sm border border-white/10 flex items-center gap-2">
          <span x-text="imagePreviewTitle"></span>
          <span class="text-white/40">•</span>
          <span class="text-white/60 text-[11px]">Tekan Esc atau klik di luar untuk menutup</span>
        </div>
      </div>
    </div>
  </div>

  </div>

  <!-- TAB 2: Ubah Kata Sandi (Opsional) -->
  <div x-show="activeTab === 'password'" @if($initialTab !== 'password') x-cloak @endif>
    <div x-data="photographerPasswordForm()" class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-3xl border border-gray-200 dark:border-gray-700/80 shadow-xs space-y-6">
      <div class="space-y-1">
        <h2 class="text-sm font-black text-gray-900 dark:text-white">
          Ubah Kata Sandi
        </h2>
        <p class="text-xs text-gray-500 dark:text-gray-400">
          Kata sandi akun sudah aktif sejak pendaftaran. Isi formulir ini hanya jika ingin mengganti kata sandi.
        </p>
      </div>

      <form action="{{ route('profile.password') }}" method="POST" @submit="submitPasswordForm($event)" novalidate class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
              Kata Sandi Saat Ini <span class="text-rose-500">*</span>
            </label>
            <input type="password" 
                   name="current_password" 
                   x-ref="currentPassword"
                   x-model="passwordForm.current_password"
                   autocomplete="current-password"
                   @input="touched.current_password = true; validateCurrentPassword()"
                   @blur="touched.current_password = true; validateCurrentPassword()"
                   placeholder="Masukkan kata sandi saat ini" 
                   class="w-full px-4 py-2.5 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition"
                   :class="touched.current_password && errors.current_password ? 'border-red-400 focus:ring-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500'">
            <p x-show="touched.current_password && errors.current_password" x-text="errors.current_password" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
              Kata Sandi Baru <span class="text-rose-500">*</span>
            </label>
            <input type="password" 
                   name="new_password" 
                   x-ref="newPassword"
                   x-model="passwordForm.new_password"
                   autocomplete="new-password"
                   @input="touched.new_password = true; validateNewPassword()"
                   @blur="touched.new_password = true; validateNewPassword()"
                   placeholder="Minimal 8 karakter" 
                   class="w-full px-4 py-2.5 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition"
                   :class="touched.new_password && errors.new_password ? 'border-red-400 focus:ring-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500'">
            <p x-show="touched.new_password && errors.new_password" x-text="errors.new_password" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
              Konfirmasi Kata Sandi Baru <span class="text-rose-500">*</span>
            </label>
            <input type="password" 
                   name="new_password_confirmation" 
                   x-ref="confirmPassword"
                   x-model="passwordForm.new_password_confirmation"
                   autocomplete="new-password"
                   @input="touched.new_password_confirmation = true; validateConfirmation()"
                   @blur="touched.new_password_confirmation = true; validateConfirmation()"
                   placeholder="Ulangi kata sandi baru" 
                   class="w-full px-4 py-2.5 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition"
                   :class="touched.new_password_confirmation && errors.new_password_confirmation ? 'border-red-400 focus:ring-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500'">
            <p x-show="touched.new_password_confirmation && errors.new_password_confirmation" x-text="errors.new_password_confirmation" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
          </div>
        </div>

        <div class="flex justify-end pt-2">
          <button type="submit" class="px-6 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs rounded-xl shadow-xs transition cursor-pointer">
            Perbaharui Data
          </button>
        </div>
      </form>
    </div>
  </div>

</div>

<!-- Dedicated Alpine.js Component Script Functions (Robust Standard) -->
@php
  $initialCategoryIds = array_map('intval', (array) old('categories', $photographer->categories->pluck('id')->toArray()));
  foreach (array_keys($usedCategoryCounts ?? []) as $lockedCatId) {
    if (!in_array((int) $lockedCatId, $initialCategoryIds, true)) {
      $initialCategoryIds[] = (int) $lockedCatId;
    }
  }
@endphp
<script>
function photographerProfileForm() {
  return {
    formData: {
      nama: {!! json_encode(old('nama', $photographer->nama ?: $user->nama)) !!},
      email: {!! json_encode(old('email', $user->email)) !!},
      nomor_telepon: {!! json_encode(old('nomor_telepon', $photographer?->nomor_telepon ?? '')) !!},
      link_sosmed: {!! json_encode(old('link_sosmed', $photographer?->link_sosmed ?? '')) !!},
      province_code: {!! json_encode(old('province_code', $photographer->province_code ?? '')) !!},
      province_name: {!! json_encode($photographer->province->name ?? '') !!},
      city_code: {!! json_encode(old('city_code', $photographer->city_code ?? '')) !!},
      city_name: {!! json_encode($photographer->city->name ?? '') !!},
      alamat: {!! json_encode(old('alamat', $photographer?->alamat ?? '')) !!},
      selectedCategories: @js($initialCategoryIds),
      deskripsi_bio: {!! json_encode(old('deskripsi_bio', $photographer?->deskripsi_bio ?? '')) !!},
      nama_bank: {!! json_encode(old('nama_bank', $photographer?->nama_bank ?? '')) !!},
      nomor_rekening: {!! json_encode(old('nomor_rekening', $photographer?->nomor_rekening ?? '')) !!},
      atas_nama_rekening: {!! json_encode(old('atas_nama_rekening', $photographer?->atas_nama_rekening ?? '')) !!}
    },
    allCategories: @js($categories->map(fn($c) => ['id' => (int) $c->id, 'name' => $c->nama_kategori])->values()),
    usedCategoryCounts: @js((object) ($usedCategoryCounts ?? [])),
    avatarPreview: {!! json_encode($avatarUrl) !!},
    sampulPreview: {!! json_encode($sampulUrl) !!},
    avatarDragging: false,
    sampulDragging: false,
    imagePreviewModalOpen: false,
    imagePreviewSrc: '',
    imagePreviewTitle: '',
    cities: {!! json_encode($cities->map(fn($c) => ['code' => $c->code, 'name' => $c->name])) !!},
    loadingCities: false,
    provinceDropdownOpen: false,
    cityDropdownOpen: false,
    categoryDropdownOpen: false,
    bankDropdownOpen: false,
    banks: ['BCA', 'Bank Mandiri', 'BNI', 'BRI', 'BSI', 'CIMB Niaga', 'Permata Bank', 'Bank Danamon', 'Bank Jago', 'Jenius / BTPN'],

    touched: {
      nama: {{ (isset($errors) && $errors->has('nama')) ? 'true' : 'false' }},
      email: {{ (isset($errors) && $errors->has('email')) ? 'true' : 'false' }},
      nomor_telepon: {{ (isset($errors) && $errors->has('nomor_telepon')) ? 'true' : 'false' }},
      link_sosmed: {{ (isset($errors) && $errors->has('link_sosmed')) ? 'true' : 'false' }},
      province_code: {{ (isset($errors) && $errors->has('province_code')) ? 'true' : 'false' }},
      city_code: {{ (isset($errors) && $errors->has('city_code')) ? 'true' : 'false' }},
      alamat: {{ (isset($errors) && $errors->has('alamat')) ? 'true' : 'false' }},
      categories: {{ (isset($errors) && $errors->has('categories')) ? 'true' : 'false' }},
      deskripsi_bio: {{ (isset($errors) && $errors->has('deskripsi_bio')) ? 'true' : 'false' }},
      nama_bank: {{ (isset($errors) && $errors->has('nama_bank')) ? 'true' : 'false' }},
      nomor_rekening: {{ (isset($errors) && $errors->has('nomor_rekening')) ? 'true' : 'false' }},
      atas_nama_rekening: {{ (isset($errors) && $errors->has('atas_nama_rekening')) ? 'true' : 'false' }}
    },

    errors: {
      nama: {!! json_encode((isset($errors) ? $errors->first('nama') : '') ?: '') !!},
      email: {!! json_encode((isset($errors) ? $errors->first('email') : '') ?: '') !!},
      nomor_telepon: {!! json_encode((isset($errors) ? $errors->first('nomor_telepon') : '') ?: '') !!},
      link_sosmed: {!! json_encode((isset($errors) ? $errors->first('link_sosmed') : '') ?: '') !!},
      province_code: {!! json_encode((isset($errors) ? $errors->first('province_code') : '') ?: '') !!},
      city_code: {!! json_encode((isset($errors) ? $errors->first('city_code') : '') ?: '') !!},
      alamat: {!! json_encode((isset($errors) ? $errors->first('alamat') : '') ?: '') !!},
      categories: {!! json_encode((isset($errors) ? $errors->first('categories') : '') ?: '') !!},
      deskripsi_bio: {!! json_encode((isset($errors) ? $errors->first('deskripsi_bio') : '') ?: '') !!},
      nama_bank: {!! json_encode((isset($errors) ? $errors->first('nama_bank') : '') ?: '') !!},
      nomor_rekening: {!! json_encode((isset($errors) ? $errors->first('nomor_rekening') : '') ?: '') !!},
      atas_nama_rekening: {!! json_encode((isset($errors) ? $errors->first('atas_nama_rekening') : '') ?: '') !!}
    },

    uploadErrors: {
      foto_file: {!! json_encode((isset($errors) ? $errors->first('foto_file') : '') ?: '') !!},
      foto_sampul_file: {!! json_encode((isset($errors) ? $errors->first('foto_sampul_file') : '') ?: '') !!}
    },

    get selectedCategorySummary() {
      const count = this.formData.selectedCategories.length;
      if (count === 0) return 'Pilih Kategori';
      if (count === 1) {
        const found = this.allCategories.find(c => Number(c.id) === Number(this.formData.selectedCategories[0]));
        return found ? found.name : '1 Kategori Dipilih';
      }
      return count + ' Kategori Dipilih';
    },

    getCategoryUsageCount(catId) {
      return Number((this.usedCategoryCounts && this.usedCategoryCounts[Number(catId)]) || 0);
    },

    isCategoryLocked(catId) {
      return this.formData.selectedCategories.includes(Number(catId)) && this.getCategoryUsageCount(catId) > 0;
    },

    validateNama() {
      const val = (this.formData.nama || '').trim();
      if (!val) {
        this.errors.nama = 'Nama lengkap wajib diisi.';
      } else if (val.length < 3) {
        this.errors.nama = 'Nama lengkap minimal 3 karakter.';
      } else {
        this.errors.nama = '';
      }
    },

    validateEmail() {
      const val = (this.formData.email || '').trim();
      if (!val) {
        this.errors.email = 'Email wajib diisi.';
      } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
        this.errors.email = 'Format alamat email tidak valid.';
      } else {
        this.errors.email = '';
      }
    },

    validatePhone() {
      this.formData.nomor_telepon = (this.formData.nomor_telepon || '').replace(/\D/g, '');
      const raw = this.formData.nomor_telepon;
      if (!raw) {
        this.errors.nomor_telepon = 'Nomor WhatsApp wajib diisi.';
      } else if (raw.length < 9) {
        this.errors.nomor_telepon = 'Nomor WhatsApp minimal 9 digit angka.';
      } else if (raw.length > 20) {
        this.errors.nomor_telepon = 'Nomor WhatsApp maksimal 20 digit angka.';
      } else {
        this.errors.nomor_telepon = '';
      }
    },

    validateSosmed() {
      const val = (this.formData.link_sosmed || '').trim();
      if (!val) {
        this.errors.link_sosmed = 'Instagram wajib diisi.';
      } else {
        this.errors.link_sosmed = '';
      }
    },

    validateProvince() {
      if (!this.formData.province_code) {
        this.errors.province_code = 'Provinsi wajib dipilih.';
      } else {
        this.errors.province_code = '';
      }
    },

    validateCity() {
      if (!this.formData.city_code) {
        this.errors.city_code = 'Kota / Kabupaten wajib dipilih.';
      } else {
        this.errors.city_code = '';
      }
    },

    validateAlamat() {
      const val = (this.formData.alamat || '').trim();
      if (!val) {
        this.errors.alamat = 'Alamat wajib diisi.';
      } else if (val.length < 5) {
        this.errors.alamat = 'Alamat minimal 5 karakter.';
      } else {
        this.errors.alamat = '';
      }
    },

    validateCategories() {
      if (!this.formData.selectedCategories || this.formData.selectedCategories.length === 0) {
        this.errors.categories = 'Pilih minimal 1 kategori.';
      } else {
        this.errors.categories = '';
      }
    },

    validateBio() {
      const val = (this.formData.deskripsi_bio || '').trim();
      if (!val) {
        this.errors.deskripsi_bio = 'Bio wajib diisi.';
      } else if (val.length < 15) {
        this.errors.deskripsi_bio = 'Bio minimal 15 karakter.';
      } else {
        this.errors.deskripsi_bio = '';
      }
    },

    validateBank() {
      if (!this.formData.nama_bank) {
        this.errors.nama_bank = 'Nama bank wajib dipilih.';
      } else {
        this.errors.nama_bank = '';
      }
    },

    validateRekening() {
      this.formData.nomor_rekening = (this.formData.nomor_rekening || '').replace(/\D/g, '');
      const val = this.formData.nomor_rekening;
      if (!val) {
        this.errors.nomor_rekening = 'Nomor rekening wajib diisi.';
      } else if (val.length < 5) {
        this.errors.nomor_rekening = 'Nomor rekening minimal 5 digit angka.';
      } else {
        this.errors.nomor_rekening = '';
      }
    },

    validateAtasNama() {
      const val = (this.formData.atas_nama_rekening || '').trim();
      if (!val) {
        this.errors.atas_nama_rekening = 'Nama pemilik rekening wajib diisi.';
      } else if (val.length < 3) {
        this.errors.atas_nama_rekening = 'Nama pemilik rekening minimal 3 karakter.';
      } else {
        this.errors.atas_nama_rekening = '';
      }
    },

    async fetchCities(pCode) {
      if (!pCode) {
        this.cities = [];
        this.formData.city_code = '';
        this.formData.city_name = '';
        return;
      }
      this.loadingCities = true;
      try {
        const res = await fetch('/api/cities/' + pCode);
        this.cities = await res.json();
      } catch (err) {
        console.error('Gagal mengambil data kota:', err);
      } finally {
        this.loadingCities = false;
      }
    },

    selectProvince(code, name) {
      this.formData.province_code = code;
      this.formData.province_name = name;
      this.provinceDropdownOpen = false;
      this.formData.city_code = '';
      this.formData.city_name = '';
      this.touched.province_code = true;
      this.validateProvince();
      this.fetchCities(code);
    },

    selectCity(code, name) {
      this.formData.city_code = code;
      this.formData.city_name = name;
      this.cityDropdownOpen = false;
      this.touched.city_code = true;
      this.validateCity();
    },

    toggleCategory(catId) {
      const numericId = Number(catId);
      const idx = this.formData.selectedCategories.indexOf(numericId);
      if (idx > -1) {
        const usageCount = this.getCategoryUsageCount(numericId);
        if (usageCount > 0) {
          const found = this.allCategories.find(c => Number(c.id) === numericId);
          const catName = found ? found.name : 'Kategori ini';
          this.touched.categories = true;
          this.errors.categories = `${catName} tidak dapat dihapus karena masih dipakai ${usageCount} portofolio.`;
          return;
        }
        this.formData.selectedCategories.splice(idx, 1);
      } else {
        this.formData.selectedCategories.push(numericId);
      }
      this.touched.categories = true;
      this.validateCategories();
    },

    handleAvatarFile(e) {
      this.processAvatarFiles(e.target.files);
    },

    handleAvatarDrop(e) {
      this.avatarDragging = false;
      if (e.dataTransfer && e.dataTransfer.files) {
        this.processAvatarFiles(e.dataTransfer.files);
      }
    },

    processAvatarFiles(fileList) {
      const files = Array.from(fileList || []);
      if (files.length === 0) return;
      const result = window.validateUploadFiles(files, { maxMB: 10 });
      if (result.hasErrors || result.validFiles.length === 0) {
        this.uploadErrors.foto_file = result.errorMessage || 'Format atau ukuran foto profil tidak valid.';
        if (this.$refs.avatarInput) this.$refs.avatarInput.value = '';
        return;
      }
      const file = result.validFiles[0];
      if (typeof DataTransfer !== 'undefined' && this.$refs.avatarInput) {
        const dt = new DataTransfer();
        dt.items.add(file);
        this.$refs.avatarInput.files = dt.files;
      }
      this.uploadErrors.foto_file = '';
      this.avatarPreview = URL.createObjectURL(file);
    },

    handleSampulFile(e) {
      this.processSampulFiles(e.target.files);
    },

    handleSampulDrop(e) {
      this.sampulDragging = false;
      if (e.dataTransfer && e.dataTransfer.files) {
        this.processSampulFiles(e.dataTransfer.files);
      }
    },

    processSampulFiles(fileList) {
      const files = Array.from(fileList || []);
      if (files.length === 0) return;
      const result = window.validateUploadFiles(files, { maxMB: 20 });
      if (result.hasErrors || result.validFiles.length === 0) {
        this.uploadErrors.foto_sampul_file = result.errorMessage || 'Format atau ukuran foto sampul tidak valid.';
        if (this.$refs.sampulInput) this.$refs.sampulInput.value = '';
        return;
      }
      const file = result.validFiles[0];
      if (typeof DataTransfer !== 'undefined' && this.$refs.sampulInput) {
        const dt = new DataTransfer();
        dt.items.add(file);
        this.$refs.sampulInput.files = dt.files;
      }
      this.uploadErrors.foto_sampul_file = '';
      this.sampulPreview = URL.createObjectURL(file);
    },

    validateAvatar() {
      if (!this.avatarPreview) {
        this.uploadErrors.foto_file = 'Foto profil wajib diunggah.';
      } else if (this.uploadErrors.foto_file === 'Foto profil wajib diunggah.') {
        this.uploadErrors.foto_file = '';
      }
    },

    validateSampul() {
      if (!this.sampulPreview) {
        this.uploadErrors.foto_sampul_file = 'Foto sampul wajib diunggah.';
      } else if (this.uploadErrors.foto_sampul_file === 'Foto sampul wajib diunggah.') {
        this.uploadErrors.foto_sampul_file = '';
      }
    },

    openImagePreview(src, title) {
      if (!src) return;
      this.imagePreviewSrc = src;
      this.imagePreviewTitle = title || 'Pratinjau Foto';
      this.imagePreviewModalOpen = true;
    },

    closeImagePreview() {
      this.imagePreviewModalOpen = false;
      this.imagePreviewSrc = '';
      this.imagePreviewTitle = '';
    },

    submitProfileForm(e) {
      Object.keys(this.touched).forEach(k => this.touched[k] = true);
      this.validateNama();
      this.validateEmail();
      this.validatePhone();
      this.validateSosmed();
      this.validateProvince();
      this.validateCity();
      this.validateAlamat();
      this.validateCategories();
      this.validateBio();
      this.validateAvatar();
      this.validateSampul();
      this.validateBank();
      this.validateRekening();
      this.validateAtasNama();

      const orderedErrors = [
        { val: this.errors.nama, ref: this.$refs.fieldNama },
        { val: this.errors.email, ref: this.$refs.fieldEmail },
        { val: this.errors.nomor_telepon, ref: this.$refs.fieldPhone },
        { val: this.errors.link_sosmed, ref: this.$refs.fieldSosmed },
        { val: this.errors.province_code, ref: this.$refs.fieldProvince },
        { val: this.errors.city_code, ref: this.$refs.fieldCity },
        { val: this.errors.alamat, ref: this.$refs.fieldAlamat },
        { val: this.errors.categories, ref: this.$refs.fieldCategories },
        { val: this.errors.deskripsi_bio, ref: this.$refs.fieldBio },
        { val: this.uploadErrors.foto_file, ref: this.$refs.avatarDropzone },
        { val: this.uploadErrors.foto_sampul_file, ref: this.$refs.sampulDropzone },
        { val: this.errors.nama_bank, ref: this.$refs.fieldBank },
        { val: this.errors.nomor_rekening, ref: this.$refs.fieldRekening },
        { val: this.errors.atas_nama_rekening, ref: this.$refs.fieldAtasNama }
      ];
      const firstErr = orderedErrors.find(item => !!item.val);
      if (firstErr) {
        e.preventDefault();
        const targetEl = firstErr.ref;
        if (targetEl) {
          const focusEl = targetEl.matches('input, textarea, select, button, [tabindex]') ? targetEl : (targetEl.querySelector('button, input, textarea, select') || targetEl);
          focusEl.focus?.();
          targetEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        return false;
      }
    }
  };
}

function photographerPasswordForm() {
  return {
    passwordForm: {
      current_password: '',
      new_password: '',
      new_password_confirmation: ''
    },
    touched: {
      current_password: {{ (isset($errors) && $errors->has('current_password')) ? 'true' : 'false' }},
      new_password: {{ (isset($errors) && $errors->has('new_password')) ? 'true' : 'false' }},
      new_password_confirmation: {{ (isset($errors) && $errors->has('new_password_confirmation')) ? 'true' : 'false' }}
    },
    errors: {
      current_password: {!! json_encode((isset($errors) ? $errors->first('current_password') : '') ?: '') !!},
      new_password: {!! json_encode((isset($errors) ? $errors->first('new_password') : '') ?: '') !!},
      new_password_confirmation: {!! json_encode((isset($errors) ? $errors->first('new_password_confirmation') : '') ?: '') !!}
    },
    validateCurrentPassword() {
      const val = this.passwordForm.current_password || this.$refs.currentPassword?.value || '';
      if (val && !this.passwordForm.current_password) {
        this.passwordForm.current_password = val;
      }
      if (!val) {
        this.errors.current_password = 'Kata sandi saat ini wajib diisi.';
      } else {
        this.errors.current_password = '';
      }
    },
    validateNewPassword() {
      const val = this.passwordForm.new_password || this.$refs.newPassword?.value || '';
      if (val && !this.passwordForm.new_password) {
        this.passwordForm.new_password = val;
      }
      if (!val) {
        this.errors.new_password = 'Kata sandi baru wajib diisi.';
      } else if (val.length < 8) {
        this.errors.new_password = 'Kata sandi baru minimal 8 karakter.';
      } else {
        this.errors.new_password = '';
      }
      if (this.touched.new_password_confirmation) {
        this.validateConfirmation();
      }
    },
    validateConfirmation() {
      const conf = this.passwordForm.new_password_confirmation || this.$refs.confirmPassword?.value || '';
      if (conf && !this.passwordForm.new_password_confirmation) {
        this.passwordForm.new_password_confirmation = conf;
      }
      const newVal = this.passwordForm.new_password || this.$refs.newPassword?.value || '';
      if (!conf) {
        this.errors.new_password_confirmation = 'Konfirmasi kata sandi baru wajib diisi.';
      } else if (conf !== newVal) {
        this.errors.new_password_confirmation = 'Konfirmasi kata sandi baru tidak cocok.';
      } else {
        this.errors.new_password_confirmation = '';
      }
    },
    submitPasswordForm(e) {
      this.touched.current_password = true;
      this.touched.new_password = true;
      this.touched.new_password_confirmation = true;
      this.validateCurrentPassword();
      this.validateNewPassword();
      this.validateConfirmation();
      if (this.errors.current_password || this.errors.new_password || this.errors.new_password_confirmation) {
        e.preventDefault();
        let targetEl = null;
        if (this.errors.current_password) targetEl = this.$refs.currentPassword;
        else if (this.errors.new_password) targetEl = this.$refs.newPassword;
        else if (this.errors.new_password_confirmation) targetEl = this.$refs.confirmPassword;
        if (targetEl) {
          targetEl.focus();
          targetEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        return false;
      }
    }
  };
}
</script>
@endsection
