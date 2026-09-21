@extends('layouts.app')

@section('title', 'Ajukan Event Lomba Foto — LensMatch')

@section('content')
<section class="pt-8 pb-16 lg:pt-10 lg:pb-24 bg-gray-50/50 dark:bg-gray-900/50 min-h-screen">
  <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

    <!-- Top Navigation Breadcrumb & Back -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div class="space-y-1">
        <div class="flex items-center gap-2 text-xs font-bold text-gray-400">
          <a href="{{ route('public.contests.index') }}" class="hover:text-amber-500 transition">Informasi Lomba</a>
          <span>/</span>
          <span class="text-amber-600 dark:text-amber-400">Pengajuan Lomba Baru</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white tracking-tight">
          Formulir Pengajuan Event Lomba Foto
        </h1>
        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">
          Daftarkan kompetisi fotografi Anda ke LensMatch. Seluruh pengajuan akan dikurasi oleh tim Admin sebelum dipublikasikan.
        </p>
      </div>

      <div class="flex items-center gap-3 shrink-0">
        <a href="{{ route('public.contests.propose.history') }}" class="px-4 py-2.5 rounded-xl bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-750 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 font-bold text-xs transition inline-flex items-center gap-2 shadow-2xs">
          <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          <span>Riwayat Pengajuan Saya</span>
        </a>
      </div>
    </div>

    @if($errors->any())
      <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 text-xs text-rose-800 dark:text-rose-300 font-bold space-y-1">
        <div class="flex items-center gap-2 font-black">
          <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
          <span>Terdapat beberapa data yang perlu diperbaiki:</span>
        </div>
        <ul class="list-disc list-inside space-y-0.5 text-rose-700 dark:text-rose-400 font-normal pl-2">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

      <!-- Left Column (8 cols): Main Form -->
      <div class="lg:col-span-8 bg-white dark:bg-gray-800 p-6 sm:p-10 rounded-3xl border border-gray-200 dark:border-gray-700/80 shadow-xs space-y-6">
        
        <form 
          action="{{ route('public.contests.propose.store') }}" 
          method="POST" 
          enctype="multipart/form-data" 
          class="space-y-6"
          x-data="{
            preview: null,
            handleBanner(e) {
              const file = e.target.files[0];
              if (file) {
                const reader = new FileReader();
                reader.onload = (event) => { this.preview = event.target.result; };
                reader.readAsDataURL(file);
              }
            }
          }"
        >
          @csrf

          <!-- Section 1: Informasi Dasar Lomba -->
          <div class="space-y-4">
            <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
              <span class="w-2 h-2 rounded-full bg-amber-400"></span>
              <span>1. Informasi Utama Event Lomba</span>
            </h3>

            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                Judul Lomba Foto <span class="text-rose-500">*</span>
              </label>
              <input 
                type="text" 
                name="judul_lomba" 
                value="{{ old('judul_lomba') }}" 
                required 
                placeholder="mis. Bali Cultural & Heritage Photography Contest 2026" 
                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500 transition"
              >
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                  Kategori Fotografi <span class="text-rose-500">*</span>
                </label>
                <select 
                  name="kategori" 
                  required 
                  class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs font-bold text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500 transition cursor-pointer"
                >
                  <option value="" disabled {{ old('kategori') ? '' : 'selected' }}>Pilih Kategori...</option>
                  @foreach($categories as $cat)
                    <option value="{{ $cat->nama_kategori }}" {{ old('kategori') === $cat->nama_kategori ? 'selected' : '' }}>
                      {{ $cat->nama_kategori }}
                    </option>
                  @endforeach
                  <option value="Landscape & Alam" {{ old('kategori') === 'Landscape & Alam' ? 'selected' : '' }}>Landscape & Alam</option>
                  <option value="Street Photography" {{ old('kategori') === 'Street Photography' ? 'selected' : '' }}>Street Photography</option>
                  <option value="Portrait & Human Interest" {{ old('kategori') === 'Portrait & Human Interest' ? 'selected' : '' }}>Portrait & Human Interest</option>
                  <option value="Budaya & Tradisi" {{ old('kategori') === 'Budaya & Tradisi' ? 'selected' : '' }}>Budaya & Tradisi</option>
                  <option value="Wildlife & Satwa" {{ old('kategori') === 'Wildlife & Satwa' ? 'selected' : '' }}>Wildlife & Satwa</option>
                  <option value="Umum & Konseptual" {{ old('kategori') === 'Umum & Konseptual' ? 'selected' : '' }}>Umum & Konseptual</option>
                </select>
              </div>

              <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                  Penyelenggara / Komunitas / Brand <span class="text-rose-500">*</span>
                </label>
                <input 
                  type="text" 
                  name="penyelenggara" 
                  value="{{ old('penyelenggara', auth()->user()->name) }}" 
                  required 
                  placeholder="mis. Komunitas Fotografi Dewata / Studio Anda" 
                  class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500 transition"
                >
              </div>
            </div>

            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                Total Hadiah & Benefit Apresiasi <span class="text-rose-500">*</span>
              </label>
              <input 
                type="text" 
                name="hadiah" 
                value="{{ old('hadiah') }}" 
                required 
                placeholder="mis. Rp 15.000.000 + E-Sertifikat + Publikasi Eksklusif" 
                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500 transition"
              >
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                  Tanggal Mulai Submisi <span class="text-rose-500">*</span>
                </label>
                <input 
                  type="date" 
                  name="start_date" 
                  value="{{ old('start_date', date('Y-m-d')) }}" 
                  required 
                  class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs font-bold text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500 transition"
                >
              </div>

              <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                  Tanggal Batas Akhir (Deadline) <span class="text-rose-500">*</span>
                </label>
                <input 
                  type="date" 
                  name="end_date" 
                  value="{{ old('end_date', date('Y-m-d', strtotime('+30 days'))) }}" 
                  required 
                  class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs font-bold text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500 transition"
                >
              </div>
            </div>
          </div>

          <!-- Section 2: Banner Gambar Lomba -->
          <div class="space-y-4 pt-2">
            <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
              <span class="w-2 h-2 rounded-full bg-amber-400"></span>
              <span>2. Banner Visual Event Lomba</span>
            </h3>

            <div class="p-5 rounded-2xl bg-gray-50 dark:bg-gray-900/60 border border-gray-200 dark:border-gray-700 space-y-4">
              <div class="flex flex-col sm:flex-row items-center gap-5">
                <div class="relative w-full sm:w-48 h-32 rounded-2xl overflow-hidden bg-gray-200 dark:bg-gray-800 border-2 border-amber-400 shadow-xs shrink-0 flex items-center justify-center">
                  <template x-if="preview">
                    <img :src="preview" alt="Preview Banner Lomba" class="w-full h-full object-cover">
                  </template>
                  <template x-if="!preview">
                    <div class="text-center p-3 space-y-1 text-gray-400">
                      <svg class="w-6 h-6 mx-auto" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"></path></svg>
                      <span class="text-[10px] block font-semibold">Preview Banner</span>
                    </div>
                  </template>
                </div>

                <div class="space-y-3 flex-1">
                  <label class="px-4 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs cursor-pointer shadow-xs transition inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"></path></svg>
                    <span>Pilih File Banner dari Perangkat</span>
                    <input type="file" name="banner_file" accept="image/*" @change="handleBanner($event)" class="hidden">
                  </label>
                  <p class="text-[11px] text-gray-500 dark:text-gray-400 leading-relaxed">
                    Format gambar (JPG, JPEG, PNG, WEBP). Disarankan resolusi horizontal 16:9 (minimal 1200x675 px) maksimal 5MB.
                  </p>
                </div>
              </div>

              <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                  Atau Gunakan Direct URL Gambar Banner (Opsional)
                </label>
                <input 
                  type="url" 
                  name="banner_url" 
                  value="{{ old('banner_url') }}" 
                  placeholder="https://images.unsplash.com/photo-..." 
                  class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500 transition"
                >
              </div>
            </div>
          </div>

          <!-- Section 3: Deskripsi & Syarat Ketentuan Dinamis -->
          <div class="space-y-4 pt-2">
            <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
              <span class="w-2 h-2 rounded-full bg-amber-400"></span>
              <span>3. Konten & Aturan Lomba</span>
            </h3>

            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                Deskripsi & Konsep Tema Lomba <span class="text-rose-500">*</span>
              </label>
              <textarea 
                name="deskrpisi_lomba" 
                rows="4" 
                required 
                placeholder="Tuliskan latar belakang, tujuan, tema eksplorasi foto, dan sasaran peserta kompetisi ini..." 
                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500 transition resize-y"
              >{{ old('deskrpisi_lomba') }}</textarea>
            </div>

            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                Syarat & Ketentuan Teknis Lomba <span class="text-rose-500">*</span>
              </label>
              <textarea 
                name="syarat_ketentuan" 
                rows="6" 
                required 
                placeholder="1. Orisinalitas karya foto dan kepemilikan hak cipta.&#10;2. Ketentuan teknis file (resolusi, format gambar, batas olah digital dasar).&#10;3. Kriteria penilaian dewan juri (storytelling, komposisi, pencahayaan).&#10;4. Mekanisme pengumuman pemenang & distribusi hadiah.&#10;5. Keputusan juri bersifat mutlak." 
                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500 transition resize-y leading-relaxed font-mono"
              >{{ old('syarat_ketentuan') }}</textarea>
              <span class="text-[11px] text-gray-400 block mt-1">Poin-poin syarat & ketentuan ini akan ditampilkan secara utuh pada halaman detail lomba.</span>
            </div>
          </div>

          <!-- Submit Buttons -->
          <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-4 pt-4 border-t border-gray-100 dark:border-gray-700/80">
            <a href="{{ route('public.contests.index') }}" class="px-5 py-2.5 rounded-xl bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-750 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 font-bold text-xs transition text-center shadow-2xs">
              Batal
            </a>

            <button type="submit" class="px-6 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs rounded-xl shadow-xs transition inline-flex items-center justify-center gap-2 cursor-pointer">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"></path></svg>
              <span>Kirim Pengajuan Lomba</span>
            </button>
          </div>

        </form>

      </div>

      <!-- Right Column (4 cols): Detailed Approval Criteria Sidebar Guide -->
      <div class="lg:col-span-4 space-y-6">

        <!-- Card: Standar Kelayakan Lomba Disetujui Admin -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-200 dark:border-gray-700/80 shadow-xs space-y-4">
          <div class="flex items-center gap-3">
            <span class="w-9 h-9 rounded-xl bg-amber-100 dark:bg-amber-900/50 text-amber-600 dark:text-amber-400 flex items-center justify-center font-black text-sm shrink-0">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"></path></svg>
            </span>
            <div>
              <h4 class="text-sm font-black text-gray-900 dark:text-white">Standar Kelayakan Lomba</h4>
              <p class="text-[11px] text-gray-400">Kriteria persetujuan Tim Admin</p>
            </div>
          </div>

          <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed">
            Agar pengajuan lomba Anda segera disetujui dan langsung tayang di katalog publik, pastikan memenuhi kriteria berikut:
          </p>

          <div class="space-y-3.5 text-xs text-gray-600 dark:text-gray-300">
            <div class="flex items-start gap-2.5">
              <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 flex items-center justify-center font-bold text-[10px] shrink-0 mt-0.5">✓</span>
              <div>
                <strong class="font-bold text-gray-900 dark:text-white">Identitas Penyelenggara Jelas:</strong>
                <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">Mencantumkan nama studio, komunitas, atau brand yang kredibel dan dapat dipertanggungjawabkan.</p>
              </div>
            </div>

            <div class="flex items-start gap-2.5">
              <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 flex items-center justify-center font-bold text-[10px] shrink-0 mt-0.5">✓</span>
              <div>
                <strong class="font-bold text-gray-900 dark:text-white">Kualitas Banner Profesional:</strong>
                <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">Gambar banner memiliki rasio horizontal jelas, tidak blur, dan bebas dari muatan SARA atau pornografi.</p>
              </div>
            </div>

            <div class="flex items-start gap-2.5">
              <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 flex items-center justify-center font-bold text-[10px] shrink-0 mt-0.5">✓</span>
              <div>
                <strong class="font-bold text-gray-900 dark:text-white">Transparansi Hadiah:</strong>
                <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">Menyebutkan prize pool atau bentuk penghargaan nyata bagi pemenang juara kompetisi.</p>
              </div>
            </div>

            <div class="flex items-start gap-2.5">
              <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 flex items-center justify-center font-bold text-[10px] shrink-0 mt-0.5">✓</span>
              <div>
                <strong class="font-bold text-gray-900 dark:text-white">Syarat Teknis yang Adil:</strong>
                <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">Hak cipta tetap milik fotografer peserta, dan aturan kurasi juri tertera secara transparan.</p>
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
@endsection
