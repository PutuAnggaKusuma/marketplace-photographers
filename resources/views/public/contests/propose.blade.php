@extends('layouts.app')



@section('title', 'Ajukan Event Lomba Foto — LensMatch')



@section('content')

<section class="pt-8 pb-16 lg:pt-10 lg:pb-24 bg-gray-50/50 dark:bg-gray-900/50 min-h-screen">

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">



    <!-- Top Navigation Breadcrumb & Back -->

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

      <div class="space-y-1">

        <!-- Breadcrumb Navigation -->
        <nav class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 mb-1.5">
          <a href="{{ route('public.contests.index') }}" class="hover:text-amber-500 transition">Informasi Lomba</a>
          <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
          <span class="text-gray-900 dark:text-white font-bold">Ajukan Event Lomba</span>
        </nav>

        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white tracking-tight">

          Formulir Pengajuan Event Lomba Foto

        </h1>

        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">

          Daftarkan kompetisi fotografi Anda ke LensMatch. Seluruh pengajuan akan direview oleh tim Admin sebelum dipublikasikan.

        </p>

      </div>



      <div class="flex items-center gap-3 shrink-0">

        <a href="{{ route('public.contests.propose.history') }}" class="px-5 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs rounded-xl shadow-xs transition inline-flex items-center gap-2">

          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>

          <span>Riwayat Pengajuan Saya</span>

        </a>

      </div>

    </div>



    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">



      <!-- Left Column (8 cols): Main Form -->

      <div x-data="proposeContestApp()" class="lg:col-span-8 bg-white dark:bg-gray-800 p-6 sm:p-10 rounded-3xl border border-gray-200 dark:border-gray-700/80 shadow-xs space-y-6">

        

        <form 
          action="{{ route('public.contests.propose.store') }}" 
          method="POST" 
          enctype="multipart/form-data" 
          class="space-y-6"
          @submit="submitForm($event)"
          novalidate
        >
          @csrf

          <!-- Section 1: Informasi Dasar Lomba -->
          <div class="space-y-4">
            <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-wider">
              1. Informasi Utama Event Lomba
            </h3>

            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                Judul Lomba Foto <span class="text-rose-500">*</span>
              </label>
              <input 
                type="text" 
                name="judul_lomba" 
                x-ref="fieldJudul"
                x-model="form.judul_lomba"
                @input="touched.judul_lomba = true; validateJudul()"
                @blur="touched.judul_lomba = true; validateJudul()"
                placeholder="mis. Bali Cultural & Heritage Photography Contest 2026" 
                class="w-full px-4 py-2.5 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition"
                :class="touched.judul_lomba && errors.judul_lomba ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500'"
              >
              <p x-show="touched.judul_lomba && errors.judul_lomba" x-text="errors.judul_lomba" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                  Kategori Fotografi <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                  <input type="hidden" name="kategori" :value="form.kategori">
                  <button 
                    type="button" 
                    x-ref="fieldKategori"
                    @click="categoryDropdownOpen = !categoryDropdownOpen" 
                    @click.away="categoryDropdownOpen = false" 
                    class="w-full px-4 py-2.5 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs font-bold text-gray-900 dark:text-white outline-none focus:ring-2 transition cursor-pointer flex items-center justify-between"
                    :class="touched.kategori && errors.kategori ? 'border-red-400 focus:ring-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500'"
                  >
                    <span x-text="form.kategori || 'Pilih Kategori...'" :class="form.kategori ? 'text-gray-900 dark:text-white' : 'text-gray-400 font-normal'"></span>
                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="categoryDropdownOpen ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                  </button>

                  <div 
                    x-show="categoryDropdownOpen" 
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-1"
                    class="absolute left-0 right-0 mt-1.5 max-h-56 overflow-y-auto bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 py-1 z-30 divide-y divide-gray-50 dark:divide-gray-700/50"
                    style="display: none;"
                  >
                    @php
                      $defaultCategories = ['Landscape & Alam', 'Street Photography', 'Portrait & Human Interest', 'Budaya & Tradisi', 'Wildlife & Satwa', 'Umum & Konseptual'];
                      $catList = $categories->pluck('nama_kategori')->merge($defaultCategories)->unique()->values();
                    @endphp
                    @foreach($catList as $cName)
                      <button 
                        type="button" 
                        @click="form.kategori = '{{ $cName }}'; touched.kategori = true; validateKategori(); categoryDropdownOpen = false" 
                        class="w-full px-4 py-2 text-xs font-bold text-left transition flex items-center justify-between cursor-pointer hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600"
                        :class="form.kategori === '{{ $cName }}' ? 'bg-amber-50/70 text-amber-600 font-black' : 'text-gray-700 dark:text-gray-300'"
                      >
                        <span>{{ $cName }}</span>
                        <span x-show="form.kategori === '{{ $cName }}'" class="shrink-0"><svg class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>
                      </button>
                    @endforeach
                  </div>
                </div>
                <p x-show="touched.kategori && errors.kategori" x-text="errors.kategori" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
              </div>

              <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                  Penyelenggara / Komunitas / Brand <span class="text-rose-500">*</span>
                </label>
                <input 
                  type="text" 
                  name="penyelenggara" 
                  x-ref="fieldPenyelenggara"
                  x-model="form.penyelenggara"
                  @input="touched.penyelenggara = true; validatePenyelenggara()"
                  @blur="touched.penyelenggara = true; validatePenyelenggara()"
                  placeholder="mis. Komunitas Fotografi Dewata / Studio Anda" 
                  class="w-full px-4 py-2.5 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition"
                  :class="touched.penyelenggara && errors.penyelenggara ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500'"
                >
                <p x-show="touched.penyelenggara && errors.penyelenggara" x-text="errors.penyelenggara" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
              </div>
            </div>

            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                Total Hadiah & Benefit Apresiasi <span class="text-rose-500">*</span>
              </label>
              <input 
                type="text" 
                name="hadiah" 
                x-ref="fieldHadiah"
                x-model="form.hadiah"
                @input="touched.hadiah = true; validateHadiah()"
                @blur="touched.hadiah = true; validateHadiah()"
                placeholder="mis. Rp 15.000.000 + E-Sertifikat + Publikasi Eksklusif" 
                class="w-full px-4 py-2.5 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition"
                :class="touched.hadiah && errors.hadiah ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500'"
              >
              <p x-show="touched.hadiah && errors.hadiah" x-text="errors.hadiah" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                  Tanggal Mulai Submisi <span class="text-rose-500">*</span>
                </label>
                <input 
                  type="date" 
                  name="start_date" 
                  x-ref="fieldStartDate"
                  x-model="form.start_date"
                  @input="touched.start_date = true; validateDates()"
                  @blur="touched.start_date = true; validateDates()"
                  class="w-full px-4 py-2.5 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs font-bold text-gray-900 dark:text-white outline-none focus:ring-2 transition"
                  :class="touched.start_date && errors.start_date ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500'"
                >
                <p x-show="touched.start_date && errors.start_date" x-text="errors.start_date" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
              </div>

              <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                  Tanggal Batas Akhir (Deadline) <span class="text-rose-500">*</span>
                </label>
                <input 
                  type="date" 
                  name="end_date" 
                  x-ref="fieldEndDate"
                  x-model="form.end_date"
                  @input="touched.end_date = true; validateDates()"
                  @blur="touched.end_date = true; validateDates()"
                  class="w-full px-4 py-2.5 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs font-bold text-gray-900 dark:text-white outline-none focus:ring-2 transition"
                  :class="touched.end_date && errors.end_date ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500'"
                >
                <p x-show="touched.end_date && errors.end_date" x-text="errors.end_date" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
              </div>
            </div>
          </div>

          <!-- Section 2: Banner Gambar Lomba (Rule #22 Pure-Text Dropzone & Rule #97 Lightbox) -->
          <div class="space-y-4 pt-2">
            <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-wider">
              2. Banner Visual Event Lomba
            </h3>

            <div class="space-y-4">
              <div tabindex="0"
                   role="button"
                   @click="$refs.bannerFileInput.click()"
                   @keydown.enter.prevent="$refs.bannerFileInput.click()"
                   @keydown.space.prevent="$refs.bannerFileInput.click()"
                   @dragover.prevent="isBannerDragging = true"
                   @dragleave.prevent="isBannerDragging = false"
                   @drop.prevent="handleBannerDrop($event)"
                   class="w-full min-h-[150px] px-6 py-8 rounded-2xl border-2 border-dashed flex flex-col items-center justify-center text-center gap-2 cursor-pointer transition outline-none"
                   :class="isBannerDragging
                       ? 'border-amber-500 bg-amber-50/70 dark:bg-amber-950/25'
                       : (bannerError
                           ? 'border-red-400 bg-red-50/40 dark:bg-red-950/20 focus:ring-2 focus:ring-red-400'
                           : 'border-gray-200 dark:border-gray-700 bg-gray-50/80 dark:bg-gray-900/60 hover:border-amber-400 dark:hover:border-amber-500 hover:bg-amber-50/30 dark:hover:bg-amber-950/20 focus:ring-2 focus:ring-amber-500')">
                <input type="file"
                       name="banner_file"
                       x-ref="bannerFileInput"
                       accept="image/jpeg,image/png,image/webp"
                       @change="handleBanner($event)"
                       class="sr-only">
                <p class="text-xs sm:text-sm font-bold text-gray-800 dark:text-gray-200">
                  <span class="text-amber-600 dark:text-amber-400 underline underline-offset-4 decoration-amber-400">Pilih banner lomba</span>
                  <span class="text-gray-600 dark:text-gray-300 font-medium"> atau seret dan lepas di sini</span>
                </p>
                <p class="text-xs text-gray-400 dark:text-gray-500 font-medium">
                  Disarankan rasio horizontal 16:9 (min. 1200x675 px). Format JPG, PNG, WEBP maksimal 5 MB.
                </p>
              </div>

              <template x-if="preview">
                <div class="rounded-xl overflow-hidden bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 max-w-sm shadow-2xs">
                  <div @click="bannerLightboxOpen = true"
                       class="relative aspect-[16/9] w-full overflow-hidden bg-gray-100 dark:bg-gray-900 cursor-pointer group">
                    <img :src="preview" alt="Preview Banner Lomba" onerror="this.onerror=null;this.src=window.SVG_IMAGE_FALLBACK;" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    <div class="absolute inset-0 bg-black/45 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                      <span class="text-xs font-bold text-white tracking-wide">Pratinjau</span>
                    </div>
                  </div>
                  <div class="px-3.5 py-2 flex items-center justify-between gap-2 border-t border-gray-100 dark:border-gray-700/80">
                    <span class="text-[11px] font-bold text-gray-700 dark:text-gray-300 truncate" x-text="bannerFileName || 'Banner Lomba'"></span>
                    <button type="button" @click="removeBanner()" class="text-[11px] font-bold text-rose-600 dark:text-rose-400 hover:underline shrink-0 cursor-pointer">Hapus</button>
                  </div>
                </div>
              </template>

              <p x-show="bannerError" x-text="bannerError" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
              @error('banner_file')
                <p class="text-[11px] font-bold text-red-500 mt-1">{{ $message }}</p>
              @enderror

              <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                  Atau Gunakan Direct URL Gambar Banner (Opsional)
                </label>
                <input 
                  type="url" 
                  name="banner_url" 
                  x-model="form.banner_url"
                  @input="if (!bannerFileName) preview = form.banner_url || null"
                  placeholder="https://..." 
                  class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500 transition"
                >
                @error('banner_url')
                  <p class="text-[11px] font-bold text-red-500 mt-1">{{ $message }}</p>
                @enderror
              </div>
            </div>
          </div>

          <!-- Section 3: Deskripsi & Syarat Ketentuan Dinamis -->
          <div class="space-y-4 pt-2">
            <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-wider">
              3. Konten & Aturan Lomba
            </h3>

            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                Deskripsi & Konsep Tema Lomba <span class="text-rose-500">*</span>
              </label>
              <textarea 
                name="deskrpisi_lomba" 
                rows="6" 
                x-ref="fieldDeskripsi"
                x-model="form.deskrpisi_lomba"
                @input="touched.deskrpisi_lomba = true; validateDeskripsi()"
                @blur="touched.deskrpisi_lomba = true; validateDeskripsi()"
                placeholder="Tuliskan latar belakang, tujuan, tema eksplorasi foto, dan sasaran peserta kompetisi ini..." 
                class="w-full min-h-[150px] px-4 py-3 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition resize-y leading-relaxed"
                :class="touched.deskrpisi_lomba && errors.deskrpisi_lomba ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500'"
              ></textarea>
              <p x-show="touched.deskrpisi_lomba && errors.deskrpisi_lomba" x-text="errors.deskrpisi_lomba" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
            </div>

            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                Syarat & Ketentuan Teknis Lomba <span class="text-rose-500">*</span>
              </label>
              <textarea 
                name="syarat_ketentuan" 
                rows="6" 
                x-ref="fieldSyarat"
                x-model="form.syarat_ketentuan"
                @input="touched.syarat_ketentuan = true; validateSyarat()"
                @blur="touched.syarat_ketentuan = true; validateSyarat()"
                placeholder="1. Orisinalitas karya foto dan kepemilikan hak cipta.&#10;2. Ketentuan teknis file (resolusi, format gambar, batas olah digital dasar).&#10;3. Kriteria penilaian dewan juri (storytelling, komposisi, pencahayaan).&#10;4. Mekanisme pengumuman pemenang & distribusi hadiah.&#10;5. Keputusan juri bersifat mutlak." 
                class="w-full px-4 py-2.5 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition resize-y leading-relaxed font-mono"
                :class="touched.syarat_ketentuan && errors.syarat_ketentuan ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500'"
              ></textarea>
              <span class="text-[11px] text-gray-400 block mt-1">Poin-poin syarat & ketentuan ini akan ditampilkan secara utuh pada halaman detail lomba.</span>
              <p x-show="touched.syarat_ketentuan && errors.syarat_ketentuan" x-text="errors.syarat_ketentuan" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
            </div>
          </div>

          <!-- Action Button Footer (Rule #74 Standard: Batal di samping kiri tombol submit, rata kanan gap-3, Rule #41: No border-t Divider) -->
          <div class="flex items-center justify-end gap-3 pt-4">
            <a href="{{ route('public.contests.index') }}" class="px-5 py-2.5 rounded-xl bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-750 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 font-bold text-xs transition text-center shadow-2xs cursor-pointer">
              Batal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs rounded-xl shadow-xs transition cursor-pointer">
              Simpan Data
            </button>
          </div>
        </form>

        <!-- Lightbox Pratinjau Banner Lomba (Rule #97) -->
        <div x-show="bannerLightboxOpen"
             x-cloak
             @keydown.escape.window="bannerLightboxOpen = false"
             @click.self="bannerLightboxOpen = false"
             class="fixed inset-0 z-[9999] bg-black/90 backdrop-blur-md flex flex-col items-center justify-center p-4 sm:p-8">
          <button type="button"
                  @click="bannerLightboxOpen = false"
                  aria-label="Tutup pratinjau"
                  class="absolute top-4 right-4 sm:top-6 sm:right-6 w-11 h-11 rounded-full bg-white/10 hover:bg-white/25 text-white flex items-center justify-center transition cursor-pointer">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
          <img :src="preview" alt="Banner Event Lomba" class="max-w-full max-h-[82vh] object-contain rounded-2xl shadow-2xl border border-white/10 select-none">
        </div>

      </div>



      <!-- Right Column (4 cols): Detailed Approval Criteria Sidebar Guide -->

      <div class="lg:col-span-4 space-y-6">



        <!-- Card: Standar Kelayakan Lomba Disetujui Admin -->

        <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-200 dark:border-gray-700/80 shadow-xs space-y-4">

          <h4 class="text-sm font-black text-gray-900 dark:text-white">Standar Kelayakan Lomba</h4>



          <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed">

            Agar pengajuan lomba Anda segera disetujui dan langsung tayang di katalog publik, pastikan memenuhi kriteria berikut:

          </p>



          <div class="space-y-3.5 text-xs text-gray-600 dark:text-gray-300">

            <div class="flex items-start gap-2.5">

              <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 flex items-center justify-center shrink-0 mt-0.5"><svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg></span>

              <div>

                <strong class="font-bold text-gray-900 dark:text-white">Identitas Penyelenggara Jelas:</strong>

                <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">Mencantumkan nama studio, komunitas, atau brand yang kredibel dan dapat dipertanggungjawabkan.</p>

              </div>

            </div>



            <div class="flex items-start gap-2.5">

              <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 flex items-center justify-center shrink-0 mt-0.5"><svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg></span>

              <div>

                <strong class="font-bold text-gray-900 dark:text-white">Kualitas Banner Profesional:</strong>

                <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">Gambar banner memiliki rasio horizontal jelas, tidak blur, dan bebas dari muatan SARA atau pornografi.</p>

              </div>

            </div>



            <div class="flex items-start gap-2.5">

              <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 flex items-center justify-center shrink-0 mt-0.5"><svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg></span>

              <div>

                <strong class="font-bold text-gray-900 dark:text-white">Transparansi Hadiah:</strong>

                <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">Menyebutkan prize pool atau bentuk penghargaan nyata bagi pemenang juara kompetisi.</p>

              </div>

            </div>



            <div class="flex items-start gap-2.5">

              <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 flex items-center justify-center shrink-0 mt-0.5"><svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg></span>

              <div>

                <strong class="font-bold text-gray-900 dark:text-white">Syarat Teknis yang Adil:</strong>

                <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">Hak cipta tetap milik fotografer peserta, dan aturan penilaian juri tertera secara transparan.</p>

              </div>

            </div>

          </div>

        </div>



        <!-- Card: Alur Verifikasi -->

        <div class="bg-amber-50/70 dark:bg-amber-950/30 p-6 rounded-3xl border border-amber-200/80 dark:border-amber-800/50 space-y-3">

          <h4 class="text-xs font-black uppercase tracking-wider text-amber-800 dark:text-amber-300">

            Alur Proses Verifikasi

          </h4>

          <ol class="list-decimal list-inside space-y-2 text-xs text-amber-950 dark:text-amber-200 font-medium">

            <li>Formulir dikirim dengan status <span class="font-bold">Pending Review</span>.</li>

            <li>Admin melakukan verifikasi kelengkapan dalam 1x24 jam.</li>

            <li>Jika disetujui, event otomatis <span class="font-bold">Buka</span> dan tayang di katalog lomba publik.</li>

          </ol>

        </div>

      </div>

    </div>

  </div>

</section>

<script>
function proposeContestApp() {
  return {
    preview: {!! json_encode(old('banner_url') ?: null) !!},
    bannerFileName: '',
    isBannerDragging: false,
    bannerLightboxOpen: false,
    bannerError: '',
    handleBanner(e) {
      this.applyBannerFiles(e.target.files);
    },
    handleBannerDrop(e) {
      this.isBannerDragging = false;
      if (e.dataTransfer && e.dataTransfer.files) {
        this.applyBannerFiles(e.dataTransfer.files);
      }
    },
    applyBannerFiles(fileList) {
      const incoming = Array.from(fileList || []);
      if (incoming.length === 0) return;
      const res = window.validateUploadFiles(incoming, { maxMB: 5 });
      if (res.hasErrors) {
        this.bannerError = res.errorMessage;
        return;
      }
      const file = res.validFiles[0];
      if (file) {
        if (this.preview && this.preview.startsWith('blob:')) {
          URL.revokeObjectURL(this.preview);
        }
        if (typeof DataTransfer !== 'undefined' && this.$refs.bannerFileInput) {
          const dt = new DataTransfer();
          dt.items.add(file);
          this.$refs.bannerFileInput.files = dt.files;
        }
        this.bannerError = '';
        this.bannerFileName = file.name;
        this.preview = URL.createObjectURL(file);
      }
    },
    removeBanner() {
      if (this.preview && this.preview.startsWith('blob:')) {
        URL.revokeObjectURL(this.preview);
      }
      this.bannerFileName = '';
      this.bannerError = '';
      if (this.$refs.bannerFileInput) this.$refs.bannerFileInput.value = '';
      this.preview = this.form.banner_url || null;
    },
    categoryDropdownOpen: false,
    form: {
      judul_lomba: {!! json_encode(old('judul_lomba', '')) !!},
      kategori: {!! json_encode(old('kategori', '')) !!},
      penyelenggara: {!! json_encode(old('penyelenggara', auth()->user()->nama ?? '')) !!},
      hadiah: {!! json_encode(old('hadiah', '')) !!},
      start_date: {!! json_encode(old('start_date', date('Y-m-d'))) !!},
      end_date: {!! json_encode(old('end_date', date('Y-m-d', strtotime('+30 days')))) !!},
      banner_url: {!! json_encode(old('banner_url', '')) !!},
      deskrpisi_lomba: {!! json_encode(old('deskrpisi_lomba', '')) !!},
      syarat_ketentuan: {!! json_encode(old('syarat_ketentuan', '')) !!},
    },
    touched: {
      judul_lomba: {{ $errors->has('judul_lomba') ? 'true' : 'false' }},
      kategori: {{ $errors->has('kategori') ? 'true' : 'false' }},
      penyelenggara: {{ $errors->has('penyelenggara') ? 'true' : 'false' }},
      hadiah: {{ $errors->has('hadiah') ? 'true' : 'false' }},
      start_date: {{ $errors->has('start_date') ? 'true' : 'false' }},
      end_date: {{ $errors->has('end_date') ? 'true' : 'false' }},
      deskrpisi_lomba: {{ $errors->has('deskrpisi_lomba') ? 'true' : 'false' }},
      syarat_ketentuan: {{ $errors->has('syarat_ketentuan') ? 'true' : 'false' }},
    },
    errors: {
      judul_lomba: {!! json_encode($errors->first('judul_lomba') ?: '') !!},
      kategori: {!! json_encode($errors->first('kategori') ?: '') !!},
      penyelenggara: {!! json_encode($errors->first('penyelenggara') ?: '') !!},
      hadiah: {!! json_encode($errors->first('hadiah') ?: '') !!},
      start_date: {!! json_encode($errors->first('start_date') ?: '') !!},
      end_date: {!! json_encode($errors->first('end_date') ?: '') !!},
      deskrpisi_lomba: {!! json_encode($errors->first('deskrpisi_lomba') ?: '') !!},
      syarat_ketentuan: {!! json_encode($errors->first('syarat_ketentuan') ?: '') !!},
    },
    validateJudul() {
      const val = (this.form.judul_lomba || '').trim();
      if (!val) this.errors.judul_lomba = 'Judul lomba foto wajib diisi.';
      else if (val.length < 5) this.errors.judul_lomba = 'Judul lomba minimal 5 karakter.';
      else this.errors.judul_lomba = '';
    },
    validateKategori() {
      if (!this.form.kategori) this.errors.kategori = 'Kategori lomba foto wajib dipilih.';
      else this.errors.kategori = '';
    },
    validatePenyelenggara() {
      const val = (this.form.penyelenggara || '').trim();
      if (!val) this.errors.penyelenggara = 'Penyelenggara lomba wajib diisi.';
      else if (val.length < 3) this.errors.penyelenggara = 'Nama penyelenggara minimal 3 karakter.';
      else this.errors.penyelenggara = '';
    },
    validateHadiah() {
      const val = (this.form.hadiah || '').trim();
      if (!val) this.errors.hadiah = 'Total hadiah & benefit wajib diisi.';
      else this.errors.hadiah = '';
    },
    validateDates() {
      const todayStr = '{{ date('Y-m-d') }}';
      if (!this.form.start_date) {
        this.errors.start_date = 'Tanggal mulai submisi wajib diisi.';
      } else if (this.form.start_date < todayStr) {
        this.errors.start_date = 'Tanggal mulai minimal hari ini.';
      } else {
        this.errors.start_date = '';
      }
      if (!this.form.end_date) {
        this.errors.end_date = 'Tanggal batas akhir wajib diisi.';
      } else if (this.form.start_date && this.form.end_date <= this.form.start_date) {
        this.errors.end_date = 'Tanggal batas akhir harus setelah tanggal mulai.';
      } else {
        this.errors.end_date = '';
      }
    },
    validateDeskripsi() {
      const val = (this.form.deskrpisi_lomba || '').trim();
      if (!val) this.errors.deskrpisi_lomba = 'Deskripsi lomba wajib diisi.';
      else if (val.length < 30) this.errors.deskrpisi_lomba = 'Deskripsi lomba minimal 30 karakter.';
      else this.errors.deskrpisi_lomba = '';
    },
    validateSyarat() {
      const val = (this.form.syarat_ketentuan || '').trim();
      if (!val) this.errors.syarat_ketentuan = 'Syarat & ketentuan lomba wajib diisi.';
      else if (val.length < 30) this.errors.syarat_ketentuan = 'Syarat & ketentuan minimal 30 karakter.';
      else this.errors.syarat_ketentuan = '';
    },
    submitForm(e) {
      this.touched.judul_lomba = true;
      this.touched.kategori = true;
      this.touched.penyelenggara = true;
      this.touched.hadiah = true;
      this.touched.start_date = true;
      this.touched.end_date = true;
      this.touched.deskrpisi_lomba = true;
      this.touched.syarat_ketentuan = true;

      this.validateJudul();
      this.validateKategori();
      this.validatePenyelenggara();
      this.validateHadiah();
      this.validateDates();
      this.validateDeskripsi();
      this.validateSyarat();

      const errorKeys = Object.keys(this.errors).filter(k => !!this.errors[k]);
      if (errorKeys.length > 0) {
        e.preventDefault();
        const refMap = {
          judul_lomba: this.$refs.fieldJudul,
          kategori: this.$refs.fieldKategori,
          penyelenggara: this.$refs.fieldPenyelenggara,
          hadiah: this.$refs.fieldHadiah,
          start_date: this.$refs.fieldStartDate,
          end_date: this.$refs.fieldEndDate,
          deskrpisi_lomba: this.$refs.fieldDeskripsi,
          syarat_ketentuan: this.$refs.fieldSyarat
        };
        const target = refMap[errorKeys[0]];
        if (target) {
          target.focus?.();
          target.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        return false;
      }
    }
  };
}
</script>

@endsection

