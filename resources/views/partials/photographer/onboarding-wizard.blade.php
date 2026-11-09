@php
  $provincesData = $onboardingProvinces ?? \Laravolt\Indonesia\Models\Province::orderBy('name', 'asc')->get();
  $categoriesData = $onboardingCategories ?? \App\Models\Category::all();
@endphp

<!-- Onboarding Modal & Dashboard Integration Component -->
<div x-data="onboardingWizardApp()">

  <!-- 1. Top Onboarding Warning Banner (Tampil Jika Profil Belum Lengkap) -->
  @if(!$photographer->is_onboarded)
    <div class="p-5 sm:p-6 rounded-2xl bg-gradient-to-r from-amber-500/15 via-amber-500/5 to-white dark:from-amber-950/40 dark:via-gray-800 dark:to-gray-800 border-2 border-amber-300 dark:border-amber-700/60 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div class="space-y-1.5">
        <div class="flex items-center gap-2">
          <span class="text-xs font-black uppercase tracking-wider text-rose-600 dark:text-rose-400">
            Profil Belum Lengkap
          </span>
          <span class="text-xs text-gray-400 font-bold">•</span>
          <span class="text-xs font-bold text-gray-500 dark:text-gray-400">
            Belum Tayang di Katalog Publik
          </span>
        </div>
        <h2 class="text-base sm:text-lg font-black text-gray-900 dark:text-white leading-snug">
          Lengkapi Profil Fotografer Anda untuk Mulai Menerima Pesanan
        </h2>
        <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 leading-relaxed max-w-2xl">
          Selesaikan 3 langkah mudah onboarding (Domisili & Kategori Spesialisasi, Portofolio & Paket Layanan, serta Rekening Payout) agar profil Anda resmi aktif dan dapat ditemukan oleh calon klien di seluruh Indonesia.
        </p>
      </div>
      <button type="button" 
              @click="open = true; step = 1" 
              class="px-5 py-3 bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs sm:text-sm rounded-xl transition shadow-xs shrink-0 cursor-pointer text-center">
        Lengkapi Profil Fotografer Anda
      </button>
    </div>
  @endif

  <!-- 2. Onboarding 3-Step Wizard Modal -->
  <div x-show="open" 
       x-cloak 
       class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-3 sm:p-4 md:p-6"
       style="display: none;">
    
    <!-- Modal Backdrop -->
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" 
         @click="open = false"></div>

    <!-- Modal Dialog Window -->
    <div class="relative bg-white dark:bg-gray-850 rounded-3xl border border-gray-100 dark:border-gray-700/80 shadow-2xl w-full max-w-3xl overflow-hidden z-10 transition-all my-8"
         @click.stop>
      
      <!-- Wizard Header: Title & Step Indicator -->
      <div class="p-6 sm:p-8 border-b border-gray-100 dark:border-gray-700/80 text-center space-y-3 bg-gradient-to-b from-amber-50/50 to-transparent dark:from-amber-950/20">
        <h2 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white tracking-tight">
          Lengkapi Profil Fotografer Anda
        </h2>
        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 max-w-lg mx-auto">
          Atur data operasional Anda dalam 3 langkah singkat sebelum profil resmi tayang di etalase publik LensMatch.
        </p>

        <!-- Progress Steps Indicator -->
        <div class="pt-3 max-w-md mx-auto">
          <div class="grid grid-cols-3 gap-2">
            <!-- Step 1 Indicator -->
            <div class="text-left space-y-1.5 cursor-pointer" @click="step = 1">
              <div class="h-1.5 rounded-full transition-all duration-300"
                   :class="step >= 1 ? 'bg-amber-400' : 'bg-gray-200 dark:bg-gray-700'"></div>
              <p class="text-[10px] font-extrabold uppercase tracking-wider"
                 :class="step === 1 ? 'text-amber-600 dark:text-amber-400' : 'text-gray-400'">
                1. Domisili & Kategori
              </p>
            </div>

            <!-- Step 2 Indicator -->
            <div class="text-left space-y-1.5 cursor-pointer" @click="nextStep()">
              <div class="h-1.5 rounded-full transition-all duration-300"
                   :class="step >= 2 ? 'bg-amber-400' : 'bg-gray-200 dark:bg-gray-700'"></div>
              <p class="text-[10px] font-extrabold uppercase tracking-wider"
                 :class="step === 2 ? 'text-amber-600 dark:text-amber-400' : 'text-gray-400'">
                2. Portofolio & Layanan
              </p>
            </div>

            <!-- Step 3 Indicator -->
            <div class="text-left space-y-1.5 cursor-pointer" @click="nextStep()">
              <div class="h-1.5 rounded-full transition-all duration-300"
                   :class="step >= 3 ? 'bg-amber-400' : 'bg-gray-200 dark:bg-gray-700'"></div>
              <p class="text-[10px] font-extrabold uppercase tracking-wider"
                 :class="step === 3 ? 'text-amber-600 dark:text-amber-400' : 'text-gray-400'">
                3. Rekening Payout
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Wizard Form Content -->
      <form action="{{ route('photographer.onboarding.complete') }}" method="POST" @submit="submitOnboardingForm($event)" novalidate class="p-6 sm:p-8 space-y-6">
        @csrf

        <!-- ==================== STEP 1: DOMISILI & KATEGORI SPESIALISASI ==================== -->
        <div x-show="step === 1" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
          <div class="border-b border-gray-100 dark:border-gray-700/60 pb-3">
            <h3 class="text-base sm:text-lg font-black text-gray-900 dark:text-white">
              Langkah 1: Domisili Operasional & Kategori Spesialisasi
            </h3>
            <p class="text-xs text-gray-500 dark:text-gray-400">
              Tentukan wilayah kota pemotretan Anda dan jenis kategori sesi foto yang Anda kuasai.
            </p>
          </div>

          <!-- Hybrid Grid: Provinsi & Kota (Rule #73 & Rule #46) -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <!-- Dropdown Provinsi -->
            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">
                Provinsi Domisili <span class="text-rose-500">*</span>
              </label>
              <input type="hidden" name="province_code" :value="provinceCode">

              <div class="relative">
                <button type="button" 
                        @click="provinceDropdownOpen = !provinceDropdownOpen; cityDropdownOpen = false"
                        :class="touched.province && errors.province ? 'border-red-400 focus:ring-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-400'"
                        class="w-full px-4 py-3 text-xs sm:text-sm rounded-xl border bg-white dark:bg-gray-800 text-gray-900 dark:text-white font-bold text-left flex items-center justify-between shadow-2xs focus:outline-none focus:ring-2 transition cursor-pointer">
                  <span x-text="provinceName ? provinceName : 'Pilih Provinsi Domisili'"></span>
                  <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="provinceDropdownOpen ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                  </svg>
                </button>

                <div x-show="provinceDropdownOpen" 
                     @click.away="provinceDropdownOpen = false" 
                     class="absolute z-50 left-0 right-0 mt-2 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 py-1 max-h-56 overflow-y-auto"
                     style="display: none;">
                  @foreach($provincesData as $prov)
                    <button type="button" 
                            @click="provinceCode = '{{ $prov->code }}'; provinceName = '{{ addslashes($prov->name) }}'; cityCode = ''; cityName = ''; provinceDropdownOpen = false; touched.province = true; validateProvince(); touched.city = true; validateCity(); fetchCities('{{ $prov->code }}');"
                            class="w-full px-4 py-2 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition text-left cursor-pointer border-b border-gray-50 dark:border-gray-700/40 last:border-0"
                            :class="provinceCode === '{{ $prov->code }}' ? 'bg-amber-50 dark:bg-amber-950/50 text-amber-600' : ''">
                      {{ $prov->name }}
                    </button>
                  @endforeach
                </div>
              </div>
              <p x-show="touched.province && errors.province" x-text="errors.province" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
            </div>

            <!-- Dropdown Kota / Kabupaten -->
            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">
                Kota / Kabupaten <span class="text-rose-500">*</span>
              </label>
              <input type="hidden" name="city_code" :value="cityCode">

              <div class="relative">
                <button type="button" 
                        @click="if(provinceCode) { cityDropdownOpen = !cityDropdownOpen; provinceDropdownOpen = false; }"
                        :disabled="!provinceCode || loadingCities"
                        :class="touched.city && errors.city ? 'border-red-400 focus:ring-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-400'"
                        class="w-full px-4 py-3 text-xs sm:text-sm rounded-xl border bg-white dark:bg-gray-800 text-gray-900 dark:text-white font-bold text-left flex items-center justify-between shadow-2xs focus:outline-none focus:ring-2 transition cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                  <span x-text="loadingCities ? 'Memuat daftar kota...' : (cityName ? cityName : (provinceCode ? 'Pilih Kota / Kabupaten' : 'Pilih provinsi terlebih dahulu'))"></span>
                  <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="cityDropdownOpen ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                  </svg>
                </button>

                <div x-show="cityDropdownOpen" 
                     @click.away="cityDropdownOpen = false" 
                     class="absolute z-50 left-0 right-0 mt-2 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 py-1 max-h-56 overflow-y-auto"
                     style="display: none;">
                  <template x-for="c in cities" :key="c.code">
                    <button type="button" 
                            @click="cityCode = c.code; cityName = c.name; cityDropdownOpen = false; touched.city = true; validateCity();"
                            class="w-full px-4 py-2 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition text-left cursor-pointer border-b border-gray-50 dark:border-gray-700/40 last:border-0"
                            :class="cityCode === c.code ? 'bg-amber-50 dark:bg-amber-950/50 text-amber-600' : ''"
                            x-text="c.name">
                    </button>
                  </template>
                </div>
              </div>
              <p x-show="touched.city && errors.city" x-text="errors.city" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
            </div>
          </div>

          <!-- Alamat / Domisili Operasional (Full-Width Textarea) -->
          <div class="space-y-1.5">
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">
              Alamat Lengkap / Domisili Operasional <span class="text-rose-500">*</span>
            </label>
            <textarea name="alamat" 
                      x-ref="alamatInput" 
                      x-model="form.alamat"
                      @input="touched.alamat = true; validateAlamat()"
                      @blur="touched.alamat = true; validateAlamat()"
                      rows="3" 
                      placeholder="Contoh: Jl. Sunset Road No. 88, Kuta, Bali (Mencakup area pemotretan Bali Selatan, Canggu, dan Ubud)"
                      :class="touched.alamat && errors.alamat ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-400'"
                      class="w-full px-4 py-3 text-xs sm:text-sm rounded-xl border bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 transition shadow-2xs font-medium leading-relaxed"></textarea>
            <p x-show="touched.alamat && errors.alamat" x-text="errors.alamat" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
          </div>

          <!-- Kategori Spesialisasi Foto (Pill Selection with Minimum 1 Category) -->
          <div class="space-y-2">
            <div class="flex items-center justify-between">
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">
                Kategori Spesialisasi Foto <span class="text-rose-500">*</span>
              </label>
              <span class="text-[11px] font-bold text-gray-400" x-text="selectedCategories.length + ' kategori dipilih'"></span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
              @foreach($categoriesData as $cat)
                <button type="button" 
                        @click="toggleCategory({{ $cat->id }})"
                        class="p-3 rounded-xl border text-left transition flex items-center justify-between cursor-pointer shadow-2xs"
                        :class="selectedCategories.includes({{ $cat->id }}) 
                                ? 'bg-amber-50 dark:bg-amber-950/60 border-amber-400 text-amber-900 dark:text-amber-200 font-extrabold ring-1 ring-amber-400' 
                                : 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 font-medium hover:border-gray-300'">
                  <span class="text-xs truncate">{{ $cat->nama_kategori }}</span>
                  <div class="w-4 h-4 rounded-full border flex items-center justify-center shrink-0"
                       :class="selectedCategories.includes({{ $cat->id }}) ? 'border-amber-500 bg-amber-500 text-white' : 'border-gray-300'">
                    <svg x-show="selectedCategories.includes({{ $cat->id }})" class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                  </div>
                </button>
              @endforeach
            </div>

            <!-- Hidden Inputs for Categories -->
            <template x-for="catId in selectedCategories" :key="catId">
              <input type="hidden" name="categories[]" :value="catId">
            </template>
            <p x-show="touched.categories && errors.categories" x-text="errors.categories" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
          </div>
        </div>

        <!-- ==================== STEP 2: PORTOFOLIO / SOSMED & PAKET PERDANA ==================== -->
        <div x-show="step === 2" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6" style="display: none;">
          <div class="border-b border-gray-100 dark:border-gray-700/60 pb-3">
            <h3 class="text-base sm:text-lg font-black text-gray-900 dark:text-white">
              Langkah 2: Tautan Portofolio & Paket Layanan Perdana
            </h3>
            <p class="text-xs text-gray-500 dark:text-gray-400">
              Tautkan akun media sosial / portofolio karya Anda dan buat satu paket layanan pertama agar klien dapat langsung memesan sesi.
            </p>
          </div>

          <!-- Tautan Instagram / Portofolio Web -->
          <div class="space-y-1.5">
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">
              Tautan Instagram / Website Portofolio <span class="text-rose-500">*</span>
            </label>
            <input type="text" 
                   name="link_sosmed" 
                   x-ref="sosmedInput"
                   x-model="form.link_sosmed"
                   @input="touched.link_sosmed = true; validateSosmed()"
                   @blur="touched.link_sosmed = true; validateSosmed()"
                   placeholder="https://instagram.com/studioanda atau https://portofolioanda.com" 
                   :class="touched.link_sosmed && errors.link_sosmed ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-400'"
                   class="w-full px-4 py-3 text-xs sm:text-sm rounded-xl border bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 transition shadow-2xs font-medium" />
            <p x-show="touched.link_sosmed && errors.link_sosmed" x-text="errors.link_sosmed" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
          </div>

          <!-- Bio Singkat Perkenalan -->
          <div class="space-y-1.5">
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">
              Deskripsi Bio Singkat Profil <span class="text-rose-500">*</span>
            </label>
            <textarea name="deskripsi_bio" 
                      x-ref="bioInput"
                      x-model="form.deskripsi_bio"
                      @input="touched.deskripsi_bio = true; validateBio()"
                      @blur="touched.deskripsi_bio = true; validateBio()"
                      rows="3" 
                      placeholder="Ceritakan gaya fotografi Anda, pengalaman, atau konsep pemotretan yang menjadi keunggulan Anda..."
                      :class="touched.deskripsi_bio && errors.deskripsi_bio ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-400'"
                      class="w-full px-4 py-3 text-xs sm:text-sm rounded-xl border bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 transition shadow-2xs font-medium leading-relaxed"></textarea>
            <p x-show="touched.deskripsi_bio && errors.deskripsi_bio" x-text="errors.deskripsi_bio" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
          </div>

          <!-- Paket Layanan Perdana (Card Box) -->
          <div class="p-5 rounded-2xl bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 space-y-4">
            <div class="flex items-center justify-between">
              <span class="text-[11px] font-black uppercase tracking-wider text-amber-700 dark:text-amber-400">
                PAKET LAYANAN PERDANA
              </span>
              <span class="text-[10px] text-gray-400 font-bold">Paket Pertama di Katalog</span>
            </div>

            <!-- Grid: Nama Paket & Tarif Harga (Rule #73) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="space-y-1.5">
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">
                  Nama Paket Layanan <span class="text-rose-500">*</span>
                </label>
                <input type="text" 
                       name="nama_layanan" 
                       x-ref="layananInput"
                       x-model="form.nama_layanan"
                       @input="touched.nama_layanan = true; validateLayanan()"
                       @blur="touched.nama_layanan = true; validateLayanan()"
                       placeholder="Contoh: Paket Sesi Personal Portrait / Wisuda" 
                       :class="touched.nama_layanan && errors.nama_layanan ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-400'"
                       class="w-full px-4 py-3 text-xs sm:text-sm rounded-xl border bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 transition shadow-2xs font-medium" />
                <p x-show="touched.nama_layanan && errors.nama_layanan" x-text="errors.nama_layanan" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
              </div>

              <div class="space-y-1.5">
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">
                  Tarif Harga Sesi (Rp) <span class="text-rose-500">*</span>
                </label>
                <input type="number" 
                       name="tarif_harga" 
                       x-ref="tarifInput"
                       x-model="form.tarif_harga"
                       @input="touched.tarif_harga = true; validateTarif()"
                       @blur="touched.tarif_harga = true; validateTarif()"
                       min="10000" 
                       step="10000"
                       placeholder="Masukkan nominal tarif paket (min. 10000)" 
                       :class="touched.tarif_harga && errors.tarif_harga ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-400'"
                       class="w-full px-4 py-3 text-xs sm:text-sm rounded-xl border bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 transition shadow-2xs font-medium" />
                <p x-show="touched.tarif_harga && errors.tarif_harga" x-text="errors.tarif_harga" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
              </div>
            </div>

            <!-- Uraian Deskripsi Fasilitas Paket -->
            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">
                Deskripsi & Fasilitas Paket <span class="text-rose-500">*</span>
              </label>
              <textarea name="deskripsi_layanan" 
                        x-ref="descLayananInput"
                        x-model="form.deskripsi_layanan"
                        @input="touched.deskripsi_layanan = true; validateDescLayanan()"
                        @blur="touched.deskripsi_layanan = true; validateDescLayanan()"
                        rows="3" 
                        placeholder="Contoh: Sesi pemotretan 2 jam, 30 foto high-res edited, seluruh file raw dikirimkan via Google Drive dalam 3 hari kerja."
                        :class="touched.deskripsi_layanan && errors.deskripsi_layanan ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-400'"
                        class="w-full px-4 py-3 text-xs sm:text-sm rounded-xl border bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 transition shadow-2xs font-medium leading-relaxed"></textarea>
              <p x-show="touched.deskripsi_layanan && errors.deskripsi_layanan" x-text="errors.deskripsi_layanan" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
            </div>
          </div>
        </div>

        <!-- ==================== STEP 3: REKENING BANK PENCAIRAN (PAYOUT) ==================== -->
        <div x-show="step === 3" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6" style="display: none;">
          <div class="border-b border-gray-100 dark:border-gray-700/60 pb-3">
            <h3 class="text-base sm:text-lg font-black text-gray-900 dark:text-white">
              Langkah 3: Rekening Bank Pencairan Dana (Payout)
            </h3>
            <p class="text-xs text-gray-500 dark:text-gray-400">
              Rekening ini digunakan oleh Admin untuk mentransfer omzet bersih pemotretan Anda ke rekening pribadi Anda.
            </p>
          </div>

          <!-- Hybrid Grid Rekening: Bank, No Rekening, Atas Nama (Rule #73) -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <!-- Nama Bank (Alpine Floating Dropdown) -->
            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">
                Nama Bank <span class="text-rose-500">*</span>
              </label>
              <input type="hidden" name="nama_bank" :value="bankName">

              <div class="relative">
                <button type="button" 
                        @click="bankDropdownOpen = !bankDropdownOpen"
                        :class="touched.bank && errors.bank ? 'border-red-400 focus:ring-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-400'"
                        class="w-full px-4 py-3 text-xs sm:text-sm rounded-xl border bg-white dark:bg-gray-800 text-gray-900 dark:text-white font-bold text-left flex items-center justify-between shadow-2xs focus:outline-none focus:ring-2 transition cursor-pointer">
                  <span x-text="bankName ? bankName : 'Pilih Bank'"></span>
                  <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="bankDropdownOpen ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                  </svg>
                </button>

                <div x-show="bankDropdownOpen" 
                     @click.away="bankDropdownOpen = false" 
                     class="absolute z-50 left-0 right-0 mt-2 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 py-1 max-h-56 overflow-y-auto"
                     style="display: none;">
                  @foreach(['BCA', 'Mandiri', 'BRI', 'BNI', 'CIMB Niaga', 'Bank Syariah Indonesia (BSI)', 'Permata Bank', 'Bank Jago'] as $b)
                    <button type="button" 
                            @click="bankName = '{{ $b }}'; bankDropdownOpen = false; touched.bank = true; validateBank();"
                            class="w-full px-4 py-2 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition text-left cursor-pointer border-b border-gray-50 dark:border-gray-700/40 last:border-0"
                            :class="bankName === '{{ $b }}' ? 'bg-amber-50 dark:bg-amber-950/50 text-amber-600' : ''">
                      {{ $b }}
                    </button>
                  @endforeach
                </div>
              </div>
              <p x-show="touched.bank && errors.bank" x-text="errors.bank" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
            </div>

            <!-- Nomor Rekening -->
            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">
                Nomor Rekening <span class="text-rose-500">*</span>
              </label>
              <input type="tel" 
                     inputmode="numeric"
                     pattern="[0-9]*"
                     name="nomor_rekening" 
                     x-ref="rekeningInput"
                     x-model="form.nomor_rekening"
                     @keypress="if(!/[0-9]/.test($event.key)) $event.preventDefault()"
                     @input="form.nomor_rekening = form.nomor_rekening.replace(/[^0-9]/g, ''); touched.nomor_rekening = true; validateRekening()"
                     @blur="touched.nomor_rekening = true; validateRekening()"
                     placeholder="Contoh: 1234567890" 
                     :class="touched.nomor_rekening && errors.nomor_rekening ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-400'"
                     class="w-full px-4 py-3 text-xs sm:text-sm rounded-xl border bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 transition shadow-2xs font-medium" />
              <p x-show="touched.nomor_rekening && errors.nomor_rekening" x-text="errors.nomor_rekening" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
            </div>

            <!-- Atas Nama Rekening -->
            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">
                Atas Nama Pemilik <span class="text-rose-500">*</span>
              </label>
              <input type="text" 
                     name="atas_nama_rekening" 
                     x-ref="atasNamaInput"
                     x-model="form.atas_nama_rekening"
                     @input="touched.atas_nama_rekening = true; validateAtasNama()"
                     @blur="touched.atas_nama_rekening = true; validateAtasNama()"
                     placeholder="Sesuai buku tabungan" 
                     :class="touched.atas_nama_rekening && errors.atas_nama_rekening ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-400'"
                     class="w-full px-4 py-3 text-xs sm:text-sm rounded-xl border bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 transition shadow-2xs font-medium" />
              <p x-show="touched.atas_nama_rekening && errors.atas_nama_rekening" x-text="errors.atas_nama_rekening" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
            </div>
          </div>

          <!-- Readiness Summary Box -->
          <div class="p-5 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200/80 dark:border-emerald-800/60 space-y-2">
            <div class="flex items-center gap-2 text-emerald-800 dark:text-emerald-300 font-black text-xs uppercase tracking-wider">
              <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
              <span>Semua Data Siap Diterbitkan</span>
            </div>
            <p class="text-xs text-emerald-700 dark:text-emerald-400 leading-relaxed">
              Setelah menekan tombol <strong>Selesaikan Onboarding</strong>, profil Anda akan otomatis diverifikasi aktif dan langsung tayang di katalog pencarian LensMatch. Anda dapat menambahkan portofolio foto dan paket tambahan lainnya kapan saja melalui dashboard ini.
            </p>
          </div>
        </div>

        <!-- Wizard Navigation Footer Buttons (Kepatuhan Rule #59: Center, Max 2 Buttons) -->
        <div class="pt-4 border-t border-gray-100 dark:border-gray-700/80 flex items-center justify-center gap-3">
          
          <!-- STEP 1 FOOTER BUTTONS -->
          <template x-if="step === 1">
            <div class="flex items-center justify-center gap-3 w-full sm:w-auto">
              <button type="button" 
                      @click="open = false" 
                      class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 text-gray-700 dark:text-gray-300 font-bold text-xs transition cursor-pointer">
                Nanti Saja
              </button>
              <button type="button" 
                      @click="nextStep()" 
                      class="px-6 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs rounded-xl shadow-xs transition inline-flex items-center gap-2 cursor-pointer">
                <span>Lanjut ke Langkah 2</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
              </button>
            </div>
          </template>

          <!-- STEP 2 FOOTER BUTTONS -->
          <template x-if="step === 2">
            <div class="flex items-center justify-center gap-3 w-full sm:w-auto">
              <button type="button" 
                      @click="prevStep()" 
                      class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 text-gray-700 dark:text-gray-300 font-bold text-xs transition inline-flex items-center gap-1.5 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                <span>Kembali</span>
              </button>
              <button type="button" 
                      @click="nextStep()" 
                      class="px-6 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs rounded-xl shadow-xs transition inline-flex items-center gap-2 cursor-pointer">
                <span>Lanjut ke Langkah 3</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
              </button>
            </div>
          </template>

          <!-- STEP 3 FOOTER BUTTONS -->
          <template x-if="step === 3">
            <div class="flex items-center justify-center gap-3 w-full sm:w-auto">
              <button type="button" 
                      @click="prevStep()" 
                      class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 text-gray-700 dark:text-gray-300 font-bold text-xs transition inline-flex items-center gap-1.5 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                <span>Kembali</span>
              </button>
              <button type="submit" 
                      class="px-7 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-950 font-black text-xs rounded-xl shadow-md hover:shadow-lg transition inline-flex items-center gap-2 cursor-pointer">
                <span>Selesaikan Onboarding</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
              </button>
            </div>
          </template>

        </div>

      </form>

    </div>

  </div>

</div>


<script>
function onboardingWizardApp() {
  return {
    open: {{ $photographer->is_onboarded ? 'false' : 'true' }},
    step: 1,
    provinceCode: '{{ old('province_code', $photographer->province_code) }}',
    provinceName: '{{ addslashes($photographer->province->name ?? '') }}',
    cityCode: '{{ old('city_code', $photographer->city_code) }}',
    cityName: '{{ addslashes($photographer->city->name ?? '') }}',
    cities: [],
    loadingCities: false,
    selectedCategories: {{ json_encode(old('categories', $photographer->categories->pluck('id')->toArray())) }},
    bankName: '{{ old('nama_bank', $photographer->nama_bank ?? '') }}',
    provinceDropdownOpen: false,
    cityDropdownOpen: false,
    bankDropdownOpen: false,

    form: {
      alamat: '{{ addslashes(old('alamat', $photographer->alamat ?? '')) }}',
      link_sosmed: '{{ addslashes(old('link_sosmed', $photographer->link_sosmed ?? '')) }}',
      deskripsi_bio: '{{ addslashes(old('deskripsi_bio', $photographer->deskripsi_bio ?? '')) }}',
      nama_layanan: '{{ addslashes(old('nama_layanan', '')) }}',
      tarif_harga: '{{ old('tarif_harga', '') }}',
      deskripsi_layanan: '{{ addslashes(old('deskripsi_layanan', '')) }}',
      nomor_rekening: '{{ addslashes(old('nomor_rekening', $photographer->nomor_rekening ?? '')) }}',
      atas_nama_rekening: '{{ addslashes(old('atas_nama_rekening', $photographer->atas_nama_rekening ?? '')) }}',
    },

    touched: {
      province: {{ $errors->has('province_code') ? 'true' : 'false' }},
      city: {{ $errors->has('city_code') ? 'true' : 'false' }},
      alamat: {{ $errors->has('alamat') ? 'true' : 'false' }},
      categories: {{ $errors->has('categories') ? 'true' : 'false' }},
      link_sosmed: {{ $errors->has('link_sosmed') ? 'true' : 'false' }},
      deskripsi_bio: {{ $errors->has('deskripsi_bio') ? 'true' : 'false' }},
      nama_layanan: {{ $errors->has('nama_layanan') ? 'true' : 'false' }},
      tarif_harga: {{ $errors->has('tarif_harga') ? 'true' : 'false' }},
      deskripsi_layanan: {{ $errors->has('deskripsi_layanan') ? 'true' : 'false' }},
      bank: {{ $errors->has('nama_bank') ? 'true' : 'false' }},
      nomor_rekening: {{ $errors->has('nomor_rekening') ? 'true' : 'false' }},
      atas_nama_rekening: {{ $errors->has('atas_nama_rekening') ? 'true' : 'false' }},
    },

    errors: {
      province: '{{ $errors->first('province_code') ?: '' }}',
      city: '{{ $errors->first('city_code') ?: '' }}',
      alamat: '{{ $errors->first('alamat') ?: '' }}',
      categories: '{{ $errors->first('categories') ?: '' }}',
      link_sosmed: '{{ $errors->first('link_sosmed') ?: '' }}',
      deskripsi_bio: '{{ $errors->first('deskripsi_bio') ?: '' }}',
      nama_layanan: '{{ $errors->first('nama_layanan') ?: '' }}',
      tarif_harga: '{{ $errors->first('tarif_harga') ?: '' }}',
      deskripsi_layanan: '{{ $errors->first('deskripsi_layanan') ?: '' }}',
      bank: '{{ $errors->first('nama_bank') ?: '' }}',
      nomor_rekening: '{{ $errors->first('nomor_rekening') ?: '' }}',
      atas_nama_rekening: '{{ $errors->first('atas_nama_rekening') ?: '' }}',
    },

    async fetchCities(pCode) {
      if (!pCode) {
        this.cities = [];
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

    init() {
      if (this.provinceCode) {
        this.fetchCities(this.provinceCode);
      }
      window.addEventListener('open-onboarding-modal', () => {
        this.open = true;
        this.step = 1;
      });
    },

    toggleCategory(catId) {
      const idx = this.selectedCategories.indexOf(catId);
      if (idx > -1) {
        this.selectedCategories.splice(idx, 1);
      } else {
        this.selectedCategories.push(catId);
      }
      this.touched.categories = true;
      this.validateCategories();
    },

    validateProvince() {
      if (!this.provinceCode) {
        this.errors.province = 'Silakan pilih provinsi domisili operasional.';
        return false;
      }
      this.errors.province = '';
      return true;
    },

    validateCity() {
      if (!this.cityCode) {
        this.errors.city = 'Silakan pilih kota / kabupaten domisili operasional.';
        return false;
      }
      this.errors.city = '';
      return true;
    },

    validateAlamat() {
      if (!this.form.alamat || !this.form.alamat.trim()) {
        this.errors.alamat = 'Alamat domisili operasional wajib diisi.';
        return false;
      }
      if (this.form.alamat.trim().length < 5) {
        this.errors.alamat = 'Alamat minimal 5 karakter.';
        return false;
      }
      this.errors.alamat = '';
      return true;
    },

    validateCategories() {
      if (!this.selectedCategories || this.selectedCategories.length === 0) {
        this.errors.categories = 'Pilih minimal 1 kategori spesialisasi foto.';
        return false;
      }
      this.errors.categories = '';
      return true;
    },

    validateSosmed() {
      if (!this.form.link_sosmed || !this.form.link_sosmed.trim()) {
        this.errors.link_sosmed = 'Tautan Instagram atau portofolio wajib diisi.';
        return false;
      }
      this.errors.link_sosmed = '';
      return true;
    },

    validateBio() {
      if (!this.form.deskripsi_bio || !this.form.deskripsi_bio.trim()) {
        this.errors.deskripsi_bio = 'Deskripsi bio profil wajib diisi.';
        return false;
      }
      if (this.form.deskripsi_bio.trim().length < 15) {
        this.errors.deskripsi_bio = 'Deskripsi bio minimal 15 karakter.';
        return false;
      }
      this.errors.deskripsi_bio = '';
      return true;
    },

    validateLayanan() {
      if (!this.form.nama_layanan || !this.form.nama_layanan.trim()) {
        this.errors.nama_layanan = 'Nama paket layanan perdana wajib diisi.';
        return false;
      }
      this.errors.nama_layanan = '';
      return true;
    },

    validateTarif() {
      const val = Number(this.form.tarif_harga);
      if (!this.form.tarif_harga || isNaN(val) || val < 10000) {
        this.errors.tarif_harga = 'Tarif paket perdana minimal Rp 10.000.';
        return false;
      }
      this.errors.tarif_harga = '';
      return true;
    },

    validateDescLayanan() {
      if (!this.form.deskripsi_layanan || !this.form.deskripsi_layanan.trim()) {
        this.errors.deskripsi_layanan = 'Deskripsi fasilitas paket layanan wajib diisi.';
        return false;
      }
      if (this.form.deskripsi_layanan.trim().length < 10) {
        this.errors.deskripsi_layanan = 'Deskripsi fasilitas paket minimal 10 karakter.';
        return false;
      }
      this.errors.deskripsi_layanan = '';
      return true;
    },

    validateBank() {
      if (!this.bankName) {
        this.errors.bank = 'Silakan pilih nama bank pencairan.';
        return false;
      }
      this.errors.bank = '';
      return true;
    },

    validateRekening() {
      const clean = (this.form.nomor_rekening || '').replace(/[^0-9]/g, '');
      if (!clean) {
        this.errors.nomor_rekening = 'Nomor rekening bank wajib diisi.';
        return false;
      }
      if (clean.length < 8 || clean.length > 20) {
        this.errors.nomor_rekening = 'Nomor rekening harus terdiri dari 8-20 digit angka.';
        return false;
      }
      this.errors.nomor_rekening = '';
      return true;
    },

    validateAtasNama() {
      if (!this.form.atas_nama_rekening || !this.form.atas_nama_rekening.trim()) {
        this.errors.atas_nama_rekening = 'Nama pemilik rekening wajib diisi.';
        return false;
      }
      if (this.form.atas_nama_rekening.trim().length < 3) {
        this.errors.atas_nama_rekening = 'Nama pemilik rekening minimal 3 karakter.';
        return false;
      }
      this.errors.atas_nama_rekening = '';
      return true;
    },

    nextStep() {
      if (this.step === 1) {
        this.touched.province = true;
        this.touched.city = true;
        this.touched.alamat = true;
        this.touched.categories = true;

        const pValid = this.validateProvince();
        const cValid = this.validateCity();
        const aValid = this.validateAlamat();
        const catValid = this.validateCategories();

        if (!pValid || !cValid || !aValid || !catValid) {
          if (!aValid && this.$refs.alamatInput) {
            this.$refs.alamatInput.focus();
          }
          return;
        }
        this.step = 2;
      } else if (this.step === 2) {
        this.touched.link_sosmed = true;
        this.touched.deskripsi_bio = true;
        this.touched.nama_layanan = true;
        this.touched.tarif_harga = true;
        this.touched.deskripsi_layanan = true;

        const sValid = this.validateSosmed();
        const bValid = this.validateBio();
        const lValid = this.validateLayanan();
        const tValid = this.validateTarif();
        const dValid = this.validateDescLayanan();

        if (!sValid || !bValid || !lValid || !tValid || !dValid) {
          if (!sValid && this.$refs.sosmedInput) {
            this.$refs.sosmedInput.focus();
          } else if (!bValid && this.$refs.bioInput) {
            this.$refs.bioInput.focus();
          } else if (!lValid && this.$refs.layananInput) {
            this.$refs.layananInput.focus();
          } else if (!tValid && this.$refs.tarifInput) {
            this.$refs.tarifInput.focus();
          } else if (!dValid && this.$refs.descLayananInput) {
            this.$refs.descLayananInput.focus();
          }
          return;
        }
        this.step = 3;
      }
    },

    prevStep() {
      if (this.step > 1) {
        this.step--;
      }
    },

    submitOnboardingForm(e) {
      this.touched.bank = true;
      this.touched.nomor_rekening = true;
      this.touched.atas_nama_rekening = true;

      const bankValid = this.validateBank();
      const rekValid = this.validateRekening();
      const anValid = this.validateAtasNama();

      if (!bankValid || !rekValid || !anValid) {
        e.preventDefault();
        if (!rekValid && this.$refs.rekeningInput) {
          this.$refs.rekeningInput.focus();
        } else if (!anValid && this.$refs.atasNamaInput) {
          this.$refs.atasNamaInput.focus();
        }
        return false;
      }
      return true;
    }
  };
}
</script>
