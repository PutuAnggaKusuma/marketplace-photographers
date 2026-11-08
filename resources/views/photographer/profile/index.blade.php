@extends('layouts.photographer')

@section('title', 'Profil Fotografer — Dashboard')

@section('content')
<div class="space-y-6 w-full pb-10">

  <!-- Header Section -->
  <div class="flex items-center justify-between">
    <h1 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">Kelola Profil</h1>
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
      <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"></path></svg>
      <span>{{ session('warning') }}</span>
    </div>
  @endif

  @if(isset($errors) && $errors->any())
    <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 text-xs text-rose-800 dark:text-rose-300 font-bold space-y-1">
      @foreach($errors->all() as $err)
        <p>• {{ $err }}</p>
      @endforeach
    </div>
  @endif

  @if($photographer && $photographer->status_verifikasi === 'ditolak')
    <div class="p-5 rounded-2xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-900/60 space-y-2">
      <div class="flex items-center gap-2 text-rose-800 dark:text-rose-300 font-extrabold text-sm">
        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
        <span>Pendaftaran Anda Ditolak</span>
      </div>
      <p class="text-xs text-rose-700 dark:text-rose-400 leading-relaxed">
        Catatan Admin: <span class="font-bold">{{ $photographer->catatan_penolakan ?: 'Data profil belum memenuhi persyaratan.' }}</span>
      </p>
      <p class="text-[11px] text-rose-600 dark:text-rose-400/80">
        Silakan perbaiki data profil di bawah ini, lalu klik Simpan Data untuk mengajukan verifikasi kembali ke Admin.
      </p>
    </div>
  @endif

  @php
    $errors = $errors ?? new \Illuminate\Support\ViewErrorBag();
    $user = auth()->user();
    $rawFoto = $photographer?->foto ?: $user?->foto;
    $avatarUrl = $rawFoto ? (str_starts_with($rawFoto, 'http') ? $rawFoto : asset('storage/' . $rawFoto)) : '';
    
    $rawSampul = $photographer?->foto_sampul;
    $sampulUrl = $rawSampul ? (str_starts_with($rawSampul, 'http') ? $rawSampul : asset('storage/' . $rawSampul)) : '';
    
    $missingFields = $photographer?->getMissingProfileFields() ?? [];
    $missingCount = count($missingFields);
    $percentage = $photographer?->getProfileCompletionPercentage() ?? 0;
    $isVerified = (bool) ($photographer?->is_verified ?? false);
    $isComplete = $photographer?->isProfileComplete() ?? false;
    $sectionStatus = $photographer?->getProfileSectionsStatus() ?? [
        'sections' => [],
        'completed_count' => 0,
        'total_sections' => 4,
        'incomplete_names' => []
    ];
  @endphp

  <!-- Indikator Kelengkapan Profil (Gaya 3B: Dot Step Indicator - Anti AI Slop) -->
  <div class="bg-white dark:bg-gray-800 p-4 sm:p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-xs space-y-2">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
      <div class="flex items-center gap-2">
        <span class="font-bold text-gray-900 dark:text-white">Kelengkapan Profil</span>
        <span class="text-gray-300 dark:text-gray-600">•</span>
        <div class="flex items-center gap-1.5" title="{{ $sectionStatus['completed_count'] }} dari {{ $sectionStatus['total_sections'] }} bagian lengkap">
          @foreach($sectionStatus['sections'] as $sec)
            <span class="w-2 h-2 rounded-full inline-block {{ $sec['is_complete'] ? 'bg-emerald-500' : 'bg-gray-300 dark:bg-gray-600' }}" title="{{ $sec['name'] }}: {{ $sec['is_complete'] ? 'Lengkap' : 'Belum lengkap' }}"></span>
          @endforeach
        </div>
        <span class="text-gray-500 dark:text-gray-400">({{ $sectionStatus['completed_count'] }}/{{ $sectionStatus['total_sections'] }} bagian)</span>
      </div>

      <div class="flex items-center gap-2">
        <span class="font-black text-gray-900 dark:text-white">{{ $percentage }}%</span>
        @if(!$isComplete)
          <span class="text-rose-600 dark:text-rose-400 font-semibold">• Kurang {{ $missingCount }} data</span>
        @elseif(!$isVerified)
          <span class="text-amber-600 dark:text-amber-400 font-bold">• Menunggu verifikasi admin</span>
        @else
          <span class="text-emerald-600 dark:text-emerald-400 font-bold">• Terverifikasi</span>
        @endif
      </div>
    </div>

    @if(!$isComplete && count($missingFields) > 0)
      <div class="pt-2 border-t border-gray-100 dark:border-gray-700/60 text-xs text-gray-600 dark:text-gray-400">
        <span class="font-bold text-gray-700 dark:text-gray-300">Belum diisi:</span>
        <span class="text-gray-500 dark:text-gray-400 ml-1 leading-relaxed">{{ implode(', ', array_values($missingFields)) }}.</span>
      </div>
    @endif
  </div>

  <!-- Form Profil Fotografer -->
  <div x-data="photographerProfileForm()" class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-3xl border border-gray-200 dark:border-gray-700/80 shadow-xs">
    
    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" @submit="submitProfileForm($event)" class="space-y-6">
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
                   required 
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
                   required 
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
                   placeholder="081234567890" 
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
                        class="w-full text-left px-4 py-2.5 text-xs text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition flex items-center justify-between border-b border-gray-100 dark:border-gray-700/40 last:border-0 cursor-pointer"
                        :class="formData.selectedCategories.includes({{ $category->id }}) ? 'bg-amber-50/70 dark:bg-amber-950/60 font-bold text-amber-600' : ''">
                  <span>{{ $category->nama_kategori }}</span>
                  <svg x-show="formData.selectedCategories.includes({{ $category->id }})" class="w-4 h-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </button>
              @endforeach
            </div>

            <p x-show="touched.categories && errors.categories" x-text="errors.categories" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>

            <!-- Selected Category Chips Display Under Dropdown -->
            <div x-show="formData.selectedCategories.length > 0" class="flex flex-wrap gap-1.5 pt-2">
              <template x-for="cat in allCategories.filter(c => formData.selectedCategories.includes(c.id))" :key="cat.id">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200/80 dark:border-amber-900/60">
                  <span x-text="cat.name"></span>
                  <button type="button" @click="toggleCategory(cat.id)" class="text-amber-600 hover:text-rose-500 transition cursor-pointer">
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

              <!-- Layout Vertikal: Preview di Atas, Aksi di Bawah -->
              <div class="space-y-4">
                <div class="relative w-28 h-28 rounded-2xl overflow-hidden bg-gray-100 dark:bg-gray-800 border-2 shadow-2xs flex items-center justify-center transition"
                     :class="avatarPreview ? 'border-amber-400 border-solid cursor-pointer group' : 'border-dashed border-gray-300 dark:border-gray-600'"
                     @click="if (avatarPreview) openImagePreview(avatarPreview, 'Foto Profil')">
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
                
                <div class="space-y-2">
                  <label class="px-5 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs cursor-pointer shadow-xs transition inline-flex items-center justify-center">
                    <span>Upload Foto Profil</span>
                    <input type="file" name="foto_file" accept="image/*" @change="handleAvatarFile($event)" class="hidden">
                  </label>
                  <p class="text-[11px] text-gray-400">JPG, PNG, WEBP maks. 10MB</p>
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

              <!-- Layout Vertikal: Preview di Atas (Rasio 16:9 Tinggi & Proporsional), Aksi di Bawah -->
              <div class="space-y-4">
                <div class="relative w-full h-44 sm:h-48 rounded-2xl overflow-hidden bg-gray-100 dark:bg-gray-800 border-2 shadow-2xs flex items-center justify-center transition"
                     :class="sampulPreview ? 'border-amber-400 border-solid cursor-pointer group' : 'border-dashed border-gray-300 dark:border-gray-600'"
                     @click="if (sampulPreview) openImagePreview(sampulPreview, 'Foto Sampul')">
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
                
                <div class="space-y-2">
                  <label class="px-5 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs cursor-pointer shadow-xs transition inline-flex items-center justify-center">
                    <span>Upload Foto Sampul</span>
                    <input type="file" name="foto_sampul_file" accept="image/*" @change="handleSampulFile($event)" class="hidden">
                  </label>
                  <p class="text-[11px] text-gray-400">Rasio 16:9 maks. 20MB</p>
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

  <!-- SECTION 6: Kata Sandi -->
  <div x-data="photographerPasswordForm()" class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-3xl border border-gray-200 dark:border-gray-700/80 shadow-xs space-y-6">
    <h2 class="text-sm font-black text-gray-900 dark:text-white">
      6. Kata Sandi
    </h2>

    <form action="{{ route('profile.password') }}" method="POST" @submit="submitPasswordForm($event)" class="space-y-6">
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
                 @input="touched.current_password = true; validateCurrentPassword()"
                 @blur="touched.current_password = true; validateCurrentPassword()"
                 required 
                 placeholder="••••••••" 
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
                 @input="touched.new_password = true; validateNewPassword()"
                 @blur="touched.new_password = true; validateNewPassword()"
                 required 
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
                 @input="touched.new_password_confirmation = true; validateConfirmation()"
                 @blur="touched.new_password_confirmation = true; validateConfirmation()"
                 required 
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

<!-- Dedicated Alpine.js Component Script Functions (Robust Standard) -->
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
      selectedCategories: {!! json_encode(old('categories', $photographer->categories->pluck('id')->toArray())) !!},
      deskripsi_bio: {!! json_encode(old('deskripsi_bio', $photographer?->deskripsi_bio ?? '')) !!},
      nama_bank: {!! json_encode(old('nama_bank', $photographer?->nama_bank ?? '')) !!},
      nomor_rekening: {!! json_encode(old('nomor_rekening', $photographer?->nomor_rekening ?? '')) !!},
      atas_nama_rekening: {!! json_encode(old('atas_nama_rekening', $photographer?->atas_nama_rekening ?? '')) !!}
    },
    allCategories: {!! json_encode($categories->map(fn($c) => ['id' => $c->id, 'name' => $c->nama_kategori])) !!},
    avatarPreview: {!! json_encode($avatarUrl) !!},
    sampulPreview: {!! json_encode($sampulUrl) !!},
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

    get selectedCategorySummary() {
      const count = this.formData.selectedCategories.length;
      if (count === 0) return 'Pilih Kategori';
      if (count === 1) {
        const found = this.allCategories.find(c => c.id == this.formData.selectedCategories[0]);
        return found ? found.name : '1 Kategori Dipilih';
      }
      return count + ' Kategori Dipilih';
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
      const idx = this.formData.selectedCategories.indexOf(catId);
      if (idx > -1) {
        this.formData.selectedCategories.splice(idx, 1);
      } else {
        this.formData.selectedCategories.push(catId);
      }
      this.touched.categories = true;
      this.validateCategories();
    },

    handleAvatarFile(e) {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = (event) => { this.avatarPreview = event.target.result; };
        reader.readAsDataURL(file);
      }
    },

    handleSampulFile(e) {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = (event) => { this.sampulPreview = event.target.result; };
        reader.readAsDataURL(file);
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
      this.validateBank();
      this.validateRekening();
      this.validateAtasNama();

      const errorKeys = Object.keys(this.errors).filter(k => !!this.errors[k]);
      if (errorKeys.length > 0) {
        e.preventDefault();
        const firstErr = errorKeys[0];
        const refMap = {
          nama: this.$refs.fieldNama,
          email: this.$refs.fieldEmail,
          nomor_telepon: this.$refs.fieldPhone,
          link_sosmed: this.$refs.fieldSosmed,
          province_code: this.$refs.fieldProvince,
          city_code: this.$refs.fieldCity,
          alamat: this.$refs.fieldAlamat,
          categories: this.$refs.fieldCategories,
          deskripsi_bio: this.$refs.fieldBio,
          nama_bank: this.$refs.fieldBank,
          nomor_rekening: this.$refs.fieldRekening,
          atas_nama_rekening: this.$refs.fieldAtasNama
        };
        const targetEl = refMap[firstErr];
        if (targetEl) {
          targetEl.focus?.();
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
      current_password: false,
      new_password: false,
      new_password_confirmation: false
    },
    errors: {
      current_password: '',
      new_password: '',
      new_password_confirmation: ''
    },
    validateCurrentPassword() {
      if (!this.passwordForm.current_password) {
        this.errors.current_password = 'Kata sandi saat ini wajib diisi.';
      } else {
        this.errors.current_password = '';
      }
    },
    validateNewPassword() {
      const val = this.passwordForm.new_password || '';
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
      const conf = this.passwordForm.new_password_confirmation || '';
      if (!conf) {
        this.errors.new_password_confirmation = 'Konfirmasi kata sandi baru wajib diisi.';
      } else if (conf !== this.passwordForm.new_password) {
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
        if (this.errors.current_password) this.$refs.currentPassword?.focus();
        else if (this.errors.new_password) this.$refs.newPassword?.focus();
        else if (this.errors.new_password_confirmation) this.$refs.confirmPassword?.focus();
        return false;
      }
    }
  };
}
</script>
@endsection
