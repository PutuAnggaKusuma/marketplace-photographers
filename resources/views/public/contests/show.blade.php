@extends('layouts.app')

@section('title', $contest->judul_lomba . ' — Lomba Foto LensMatch')

@section('content')
@php
  $stage = $contest->current_stage;
@endphp
<section class="py-12 bg-white dark:bg-gray-900 min-h-screen"
         x-data="{
           loginModal: false,
           lightboxOpen: false,
           lightboxUrl: '',
           lightboxCaption: '',
           openLoginModal() { this.loginModal = true; },
           closeLoginModal() { this.loginModal = false; },
           openLightbox(url, caption = '') {
             if (!url) return;
             this.lightboxUrl = url;
             this.lightboxCaption = caption || 'Pratinjau Foto';
             this.lightboxOpen = true;
           },
           closeLightbox() {
             this.lightboxOpen = false;
             this.lightboxUrl = '';
             this.lightboxCaption = '';
           }
         }"
         @if(isset($errors) && $errors->any())
           x-init="$nextTick(() => $dispatch('show-toast', { type: 'error', message: {{ \Illuminate\Support\Js::from($errors->first()) }} }))"
         @endif>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

    <!-- Breadcrumb & Back Navigation -->
    <div class="space-y-2">
      <nav class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
        <a href="{{ route('public.contests.index') }}" class="hover:text-amber-500 transition">Informasi Lomba</a>
        <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
        <span class="text-gray-900 dark:text-white font-bold truncate max-w-md">{{ $contest->judul_lomba }}</span>
      </nav>
      <div>
        <a href="{{ route('public.contests.index') }}" class="text-xs font-bold text-gray-500 hover:text-amber-500 transition inline-flex items-center gap-1.5">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
          <span>Kembali ke Katalog Event Lomba</span>
        </a>
      </div>
    </div>

    <!-- Main Contest Card -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-xs space-y-8 p-6 sm:p-12 lg:p-16">
      
      <!-- Badges (Clean 2-Badge Header: Kategori & Status) -->
      <div class="flex flex-wrap items-center gap-2.5">
        <span class="px-3.5 py-1 bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200/80 dark:border-amber-800/60 text-xs font-extrabold rounded-xl uppercase tracking-wider">
          {{ $contest->kategori }}
        </span>

        @if($contest->status === 'buka')
          @if($stage === 'registration')
            <span class="px-3.5 py-1 bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200/80 text-xs font-bold rounded-xl">
              Pendaftaran Dibuka
            </span>
          @elseif($stage === 'submission')
            <span class="px-3.5 py-1 bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-200/80 text-xs font-bold rounded-xl">
              Pengumpulan Karya
            </span>
          @elseif($stage === 'judging')
            <span class="px-3.5 py-1 bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200/80 text-xs font-bold rounded-xl">
              Tahap Penjurian
            </span>
          @elseif($stage === 'completed')
            <span class="px-3.5 py-1 bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200/80 text-xs font-bold rounded-xl">
              Pemenang Diumumkan
            </span>
          @else
            <span class="px-3.5 py-1 bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200/80 text-xs font-bold rounded-xl">
              Dibuka
            </span>
          @endif
        @elseif($contest->status === 'ditutup')
          <span class="px-3.5 py-1 bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-200/80 text-xs font-bold rounded-xl">
            Pendaftaran Ditutup
          </span>
        @else
          <span class="px-3.5 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs font-bold rounded-xl">
            Event Selesai
          </span>
        @endif
      </div>

      <!-- Banner Image (Frame with Blurred Ambient Cover Backdrop + Full Centered Image) -->
      <div class="relative rounded-2xl overflow-hidden h-72 sm:h-80 lg:h-[420px] border border-gray-200 dark:border-gray-700 bg-gray-900 flex items-center justify-center">
        <!-- Blurred Background Cover Layer -->
        <img 
          src="{{ $contest->banner_url }}" 
          alt="" 
          aria-hidden="true"
          class="absolute inset-0 w-full h-full object-cover blur-xl scale-110 opacity-60 dark:opacity-40 pointer-events-none select-none"
        />
        <div class="absolute inset-0 bg-black/25 dark:bg-black/40"></div>

        <!-- Main Image (Displayed in Full / Uncropped in the Center of Frame) -->
        <img 
          src="{{ $contest->banner_url }}" 
          alt="{{ $contest->judul_lomba }}" 
          onerror="this.onerror=null;this.src=window.SVG_IMAGE_FALLBACK||'data:image/svg+xml;charset=UTF-8,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'800\' height=\'600\' viewBox=\'0 0 800 600\' fill=\'none\'%3E%3Crect width=\'800\' height=\'600\' fill=\'%23F3F4F6\'/%3E%3Cpath d=\'M360 260C360 248.954 368.954 240 380 240H420C431.046 240 440 248.954 440 260V265H450C466.569 265 480 278.431 480 295V345C480 361.569 466.569 375 450 375H350C333.431 375 320 361.569 320 345V295C320 278.431 333.431 265 350 265H360V260Z\' stroke=\'%239CA3AF\' stroke-width=\'12\' stroke-linecap=\'round\' stroke-linejoin=\'round\'/%3E%3Ccircle cx=\'400\' cy=\'320\' r=\'30\' stroke=\'%239CA3AF\' stroke-width=\'12\'/%3E%3Cline x1=\'310\' y1=\'240\' x2=\'490\' y2=\'390\' stroke=\'%23EF4444\' stroke-width=\'10\' stroke-linecap=\'round\'/%3E%3Ctext x=\'400\' y=\'450\' font-family=\'sans-serif\' font-size=\'22\' font-weight=\'700\' fill=\'%236B7280\' text-anchor=\'middle\'%3EGambar Tidak Dapat Dimuat%3C/text%3E%3C/svg%3E';" 
          class="relative z-10 w-full h-full object-contain mx-auto drop-shadow-md" 
        />
      </div>

      <!-- Title & Description (With Clean Organizer Info) -->
      <div class="space-y-2 pt-1">
        <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
          <span>Penyelenggara:</span>
          <span class="font-extrabold text-gray-900 dark:text-white">{{ $contest->penyelenggara }}</span>
        </div>
        <h1 class="text-xl sm:text-2xl lg:text-[26px] font-black text-gray-900 dark:text-white leading-snug">
          {{ $contest->judul_lomba }}
        </h1>
        <p class="text-sm sm:text-base text-gray-600 dark:text-gray-300 leading-relaxed whitespace-pre-line">
          {{ $contest->deskrpisi_lomba }}
        </p>
      </div>

      <!-- Grid 2 Kolom: Informasi Detail & Timeline 5 Fase -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-14 pt-2">
        
        <!-- Kolom Kiri: Informasi Lomba -->
        <div class="space-y-4">
          <h3 class="text-base font-extrabold text-gray-900 dark:text-white">Informasi Lomba</h3>
          
          <div class="space-y-3.5 text-sm">
            <div class="flex items-center gap-3">
              <span class="w-36 font-medium text-gray-500 dark:text-gray-400 shrink-0">Total Hadiah</span>
              <span class="font-medium text-gray-400 dark:text-gray-500 shrink-0">:</span>
              <span class="font-bold text-gray-900 dark:text-white">{{ $contest->hadiah }}</span>
            </div>

            <div class="flex items-center gap-3">
              <span class="w-36 font-medium text-gray-500 dark:text-gray-400 shrink-0">Biaya Registrasi</span>
              <span class="font-medium text-gray-400 dark:text-gray-500 shrink-0">:</span>
              <span class="font-bold text-gray-900 dark:text-white">
                {{ $contest->biaya_pendaftaran > 0 ? 'Rp ' . number_format($contest->biaya_pendaftaran, 0, ',', '.') : '100% Gratis' }}
              </span>
            </div>

            <div class="flex items-center gap-3">
              <span class="w-36 font-medium text-gray-500 dark:text-gray-400 shrink-0">Batas Karya / Peserta</span>
              <span class="font-medium text-gray-400 dark:text-gray-500 shrink-0">:</span>
              <span class="font-bold text-gray-900 dark:text-white">Maksimal {{ $contest->max_submissions ?? 2 }} Karya Foto</span>
            </div>

            <div class="flex items-center gap-3">
              <span class="w-36 font-medium text-gray-500 dark:text-gray-400 shrink-0">Tautan Eksternal</span>
              <span class="font-medium text-gray-400 dark:text-gray-500 shrink-0">:</span>
              <span class="font-bold text-gray-900 dark:text-white">{{ $contest->allow_link ? 'Diizinkan (Drive / YouTube)' : 'Hanya Upload File Langsung' }}</span>
            </div>

            <div class="flex items-center gap-3">
              <span class="w-36 font-medium text-gray-500 dark:text-gray-400 shrink-0">Total Karya Terkirim</span>
              <span class="font-medium text-gray-400 dark:text-gray-500 shrink-0">:</span>
              <span class="font-bold text-gray-900 dark:text-white">{{ $contest->submissions_count ?? $submissions->count() }} Karya</span>
            </div>
          </div>
        </div>

        <!-- Kolom Kanan: 5 Tahapan Kompetisi -->
        <div class="space-y-4">
          <h3 class="text-base font-extrabold text-gray-900 dark:text-white">Jadwal Lomba</h3>
          <div class="space-y-3.5 text-sm">
            <div class="flex items-start gap-3.5">
              <span class="w-6 h-6 rounded-full bg-amber-400 text-gray-950 font-black flex items-center justify-center text-xs shrink-0 mt-0.5 shadow-2xs">
                1
              </span>
              <div class="space-y-0.5 min-w-0">
                <div class="flex items-center gap-2">
                  <h4 class="text-xs font-bold text-gray-900 dark:text-white">Pendaftaran Calon Peserta</h4>
                  @if($stage === 'registration')
                    <span class="px-2 py-0.5 rounded-md bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 text-[10px] font-black">Aktif Sekarang</span>
                  @endif
                </div>
                <p class="text-[11px] text-gray-500 dark:text-gray-400">
                  {{ $contest->registration_start ? $contest->registration_start->translatedFormat('d M Y') : 'TBA' }} — {{ $contest->registration_end ? $contest->registration_end->translatedFormat('d M Y') : 'TBA' }}
                </p>
              </div>
            </div>

            <div class="flex items-start gap-3.5">
              <span class="w-6 h-6 rounded-full bg-amber-400 text-gray-950 font-black flex items-center justify-center text-xs shrink-0 mt-0.5 shadow-2xs">
                2
              </span>
              <div class="space-y-0.5 min-w-0">
                <div class="flex items-center gap-2">
                  <h4 class="text-xs font-bold text-gray-900 dark:text-white">Pengumpulan Karya</h4>
                  @if($stage === 'submission')
                    <span class="px-2 py-0.5 rounded-md bg-purple-100 dark:bg-purple-950 text-purple-700 dark:text-purple-300 text-[10px] font-black">Aktif Sekarang</span>
                  @endif
                </div>
                <p class="text-[11px] text-gray-500 dark:text-gray-400">
                  {{ $contest->submission_start ? $contest->submission_start->translatedFormat('d M Y') : 'TBA' }} — {{ $contest->submission_end ? $contest->submission_end->translatedFormat('d M Y') : 'TBA' }}
                </p>
              </div>
            </div>

            <div class="flex items-start gap-3.5">
              <span class="w-6 h-6 rounded-full bg-amber-400 text-gray-950 font-black flex items-center justify-center text-xs shrink-0 mt-0.5 shadow-2xs">
                3
              </span>
              <div class="space-y-0.5 min-w-0">
                <div class="flex items-center gap-2">
                  <h4 class="text-xs font-bold text-gray-900 dark:text-white">Penjurian Karya</h4>
                  @if($stage === 'judging')
                    <span class="px-2 py-0.5 rounded-md bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-300 text-[10px] font-black">Aktif Sekarang</span>
                  @endif
                </div>
                <p class="text-[11px] text-gray-500 dark:text-gray-400">
                  {{ $contest->judging_start ? $contest->judging_start->translatedFormat('d M Y') : 'TBA' }} — {{ $contest->judging_end ? $contest->judging_end->translatedFormat('d M Y') : 'TBA' }}
                </p>
              </div>
            </div>

            <div class="flex items-start gap-3.5">
              <span class="w-6 h-6 rounded-full bg-amber-400 text-gray-950 font-black flex items-center justify-center text-xs shrink-0 mt-0.5 shadow-2xs">
                4
              </span>
              <div class="space-y-0.5 min-w-0">
                <div class="flex items-center gap-2">
                  <h4 class="text-xs font-bold text-gray-900 dark:text-white">Pengumuman Pemenang</h4>
                </div>
                <p class="text-[11px] text-gray-500 dark:text-gray-400">
                  {{ $contest->announcement_date ? $contest->announcement_date->translatedFormat('d M Y') : 'TBA' }}
                </p>
              </div>
            </div>
          </div>
        </div>

      </div>

      <!-- Section: Syarat & Ketentuan Lomba -->
      @if(!empty($contest->syarat_ketentuan))
        <div class="space-y-4 pt-4 sm:pt-6">
          <h3 class="text-base font-extrabold text-gray-900 dark:text-white">Syarat & Ketentuan Lomba</h3>
          <div class="space-y-3.5 text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
            @php
              $rules = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $contest->syarat_ketentuan)));
            @endphp
            @if(count($rules) > 1)
              @foreach($rules as $rule)
                <p>{{ $rule }}</p>
              @endforeach
            @else
              <p class="whitespace-pre-line">{{ $contest->syarat_ketentuan }}</p>
            @endif
          </div>
        </div>
      @endif

      <!-- ========================================================================= -->
      <!-- INTERACTIVE USER LIFECYCLE SECTION (Multi-Stage Competition System)       -->
      <!-- ========================================================================= -->
      <div class="pt-2 sm:pt-4">
        
        @guest
          <!-- Case: Guest User (Belum Login) -->
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-6 sm:p-8 rounded-3xl bg-amber-50/80 dark:bg-amber-950/30 border border-amber-200/80 dark:border-amber-800/60">
            <div class="space-y-1">
              <h4 class="text-base font-extrabold text-gray-900 dark:text-white">Tertarik Mengikuti Lomba Foto Ini?</h4>
              <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed">Silakan masuk ke akun Anda terlebih dahulu untuk mendaftar sebagai peserta dan mengunggah portofolio karya terbaik Anda.</p>
            </div>
            <button type="button" @click="openLoginModal()" class="px-6 py-3 bg-amber-400 hover:bg-amber-500 text-gray-950 font-black text-xs rounded-xl shadow-xs transition shrink-0 text-center cursor-pointer">
              Daftar Lomba Foto Ini
            </button>
          </div>
        @else
          @php
            $isHoldPhotographer = auth()->check() && auth()->user()->isHoldPhotographer();
          @endphp
          <!-- Logged-in User States -->
          @if(!$userRegistration)
            <!-- CASE 1: Belum Mendaftar -->
            @php
              $regHasErrors = isset($errors) && $errors->hasAny([
                'nama_lengkap', 'email', 'no_wa', 'asal_kota', 'instansi', 'bio_singkat', 'motivasi', 'cv_portfolio_file', 'portfolio_link'
              ]);
              $regProfileDefaults = [
                'nama_lengkap' => $profileData['nama'] ?? '',
                'email' => $profileData['email'] ?? '',
                'no_wa' => $profileData['no_wa'] ?? '',
                'asal_kota' => $profileData['asal_kota'] ?? '',
                'instansi' => $profileData['instansi'] ?? '',
                'bio_singkat' => $profileData['bio'] ?? ($profileData['bio_singkat'] ?? ''),
                'portfolio_link' => $profileData['portfolio_link'] ?? '',
              ];
            @endphp
            <div x-data="{
              showForm: {{ $regHasErrors ? 'true' : 'false' }},
              profileDefaults: @js($regProfileDefaults),
              cvFileName: '',
              cvFileSize: '',
              cvDragging: false,
              cvError: @js((isset($errors) ? $errors->first('cv_portfolio_file') : '') ?: ''),
              form: {
                nama_lengkap: @js(old('nama_lengkap', $regProfileDefaults['nama_lengkap'])),
                email: @js(old('email', $regProfileDefaults['email'])),
                no_wa: @js(old('no_wa', $regProfileDefaults['no_wa'])),
                asal_kota: @js(old('asal_kota', $regProfileDefaults['asal_kota'])),
                instansi: @js(old('instansi', $regProfileDefaults['instansi'])),
                bio_singkat: @js(old('bio_singkat', $regProfileDefaults['bio_singkat'])),
                portfolio_link: @js(old('portfolio_link', $regProfileDefaults['portfolio_link'])),
                motivasi: @js(old('motivasi', ''))
              },
              touched: {
                nama_lengkap: {{ isset($errors) && $errors->has('nama_lengkap') ? 'true' : 'false' }},
                email: {{ isset($errors) && $errors->has('email') ? 'true' : 'false' }},
                no_wa: {{ isset($errors) && $errors->has('no_wa') ? 'true' : 'false' }},
                asal_kota: {{ isset($errors) && $errors->has('asal_kota') ? 'true' : 'false' }},
                motivasi: {{ isset($errors) && $errors->has('motivasi') ? 'true' : 'false' }}
              },
              errors: {
                nama_lengkap: @js((isset($errors) ? $errors->first('nama_lengkap') : '') ?: ''),
                email: @js((isset($errors) ? $errors->first('email') : '') ?: ''),
                no_wa: @js((isset($errors) ? $errors->first('no_wa') : '') ?: ''),
                asal_kota: @js((isset($errors) ? $errors->first('asal_kota') : '') ?: ''),
                motivasi: @js((isset($errors) ? $errors->first('motivasi') : '') ?: '')
              },
              useProfile() {
                this.form.nama_lengkap = this.profileDefaults.nama_lengkap || '';
                this.form.email = this.profileDefaults.email || '';
                this.form.no_wa = (this.profileDefaults.no_wa || '').replace(/[^0-9]/g, '');
                this.form.asal_kota = this.profileDefaults.asal_kota || '';
                this.form.instansi = this.profileDefaults.instansi || '';
                this.form.bio_singkat = this.profileDefaults.bio_singkat || '';
                this.form.portfolio_link = this.profileDefaults.portfolio_link || '';
                if (this.touched.nama_lengkap) this.validateNama();
                if (this.touched.email) this.validateEmail();
                if (this.touched.no_wa) this.validateNoWa();
                if (this.touched.asal_kota) this.validateAsalKota();
              },
              validateNama() {
                const v = (this.form.nama_lengkap || '').trim();
                this.errors.nama_lengkap = !v ? 'Nama lengkap wajib diisi.' : (v.length < 3 ? 'Nama lengkap minimal 3 karakter.' : '');
              },
              validateEmail() {
                const v = (this.form.email || '').trim();
                this.errors.email = !v ? 'Alamat email aktif wajib diisi.' : (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v) ? 'Format alamat email tidak valid.' : '');
              },
              validateNoWa() {
                this.form.no_wa = (this.form.no_wa || '').replace(/[^0-9]/g, '');
                const v = this.form.no_wa;
                this.errors.no_wa = !v ? 'Nomor WhatsApp aktif wajib diisi.' : (v.length < 9 ? 'Nomor WhatsApp minimal 9 digit angka.' : '');
              },
              validateAsalKota() {
                const v = (this.form.asal_kota || '').trim();
                this.errors.asal_kota = !v ? 'Asal kota / domisili wajib diisi.' : (v.length < 2 ? 'Asal kota minimal 2 karakter.' : '');
              },
              validateMotivasi() {
                const v = (this.form.motivasi || '').trim();
                this.errors.motivasi = !v ? 'Motivasi mengikuti lomba wajib diisi.' : (v.length < 10 ? 'Motivasi minimal 10 karakter.' : '');
              },
              handleCvSelect(e) {
                this.setCvFile(e.target.files?.[0]);
              },
              handleCvDrop(e) {
                this.cvDragging = false;
                this.setCvFile(e.dataTransfer?.files?.[0]);
              },
              setCvFile(file) {
                if (!file) return;
                const res = window.validateUploadFiles([file], {
                  maxMB: 10,
                  allowedMimes: [
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/zip',
                    'application/x-zip-compressed',
                    'application/x-rar-compressed',
                    'application/vnd.rar'
                  ],
                  allowedExts: ['pdf', 'doc', 'docx', 'zip', 'rar'],
                  formatLabel: 'PDF, DOC, DOCX, ZIP, atau RAR'
                });
                if (res.hasErrors) {
                  this.removeCvFile();
                  this.cvError = res.errorMessage;
                  return;
                }
                const valid = res.validFiles[0];
                if (!valid) return;
                this.cvError = '';
                this.cvFileName = valid.name;
                this.cvFileSize = window.formatFileSizeMB(valid.size);
                if (typeof DataTransfer !== 'undefined' && this.$refs.cvFileInput) {
                  const dt = new DataTransfer();
                  dt.items.add(valid);
                  this.$refs.cvFileInput.files = dt.files;
                }
              },
              removeCvFile() {
                this.cvFileName = '';
                this.cvFileSize = '';
                this.cvError = '';
                if (this.$refs.cvFileInput) this.$refs.cvFileInput.value = '';
              },
              submitRegister(e) {
                this.touched.nama_lengkap = true;
                this.touched.email = true;
                this.touched.no_wa = true;
                this.touched.asal_kota = true;
                this.touched.motivasi = true;
                this.validateNama();
                this.validateEmail();
                this.validateNoWa();
                this.validateAsalKota();
                this.validateMotivasi();

                const firstInvalid = [
                  { err: this.errors.nama_lengkap, ref: this.$refs.namaInput },
                  { err: this.errors.email, ref: this.$refs.emailInput },
                  { err: this.errors.no_wa, ref: this.$refs.waInput },
                  { err: this.errors.asal_kota, ref: this.$refs.kotaInput },
                  { err: this.errors.motivasi, ref: this.$refs.motivasiInput },
                  { err: this.cvError, ref: this.$refs.cvDropzone }
                ].find(item => !!item.err);

                if (firstInvalid) {
                  e.preventDefault();
                  if (firstInvalid.ref) {
                    firstInvalid.ref.focus();
                    firstInvalid.ref.scrollIntoView({ behavior: 'smooth', block: 'center' });
                  }
                  return false;
                }
                return true;
              }
            }">
              
              <div x-show="!showForm" x-collapse.duration.300ms class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-6 sm:p-8 rounded-3xl bg-amber-50/80 dark:bg-amber-950/30 border border-amber-200/80 dark:border-amber-800/60">
                <div class="space-y-1">
                  <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-md bg-amber-400 text-gray-950 text-[10px] font-black uppercase tracking-wider">Tahap 1</span>
                    <h4 class="text-sm sm:text-base font-extrabold text-gray-900 dark:text-white">Pendaftaran Calon Peserta Sedang Dibuka!</h4>
                  </div>
                  <p class="text-xs text-gray-600 dark:text-gray-300">
                    Lengkapi data diri dan portofolio kualifikasi Anda. Data Anda akan dikurasi oleh admin sebelum pengumpulan karya dimulai.
                  </p>
                </div>
                @if($isHoldPhotographer)
                  <button type="button" @click="openPhotographerHoldModal('mendaftar sebagai peserta lomba')" class="px-6 py-3 bg-amber-400 hover:bg-amber-500 text-gray-950 font-black text-xs rounded-xl shadow-xs transition shrink-0 cursor-pointer">
                    Daftar Calon Peserta
                  </button>
                @else
                  <button type="button" @click="showForm = true" class="px-6 py-3 bg-amber-400 hover:bg-amber-500 text-gray-950 font-black text-xs rounded-xl shadow-xs transition shrink-0 cursor-pointer">
                    Daftar Calon Peserta
                  </button>
                @endif
              </div>

              @if(!$isHoldPhotographer)
              <!-- FORMULIR REGISTRASI CALON PESERTA (TAHAP 1) -->
              <div 
                x-show="showForm" 
                x-cloak 
                x-collapse.duration.600ms
                x-transition:enter="transition-all duration-600 ease-out transform origin-top"
                x-transition:enter-start="opacity-0 -translate-y-4"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition-all duration-300 ease-in transform origin-top"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-4"
                class="space-y-6 pt-2"
              >
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2">
                  <div>
                    <h3 class="text-lg font-black text-gray-900 dark:text-white">Formulir Pendaftaran Calon Peserta</h3>
                    <p class="text-xs text-gray-500">Tahap 1: Seleksi Portofolio Peserta</p>
                  </div>
                  <div>
                    <button type="button" @click="useProfile()" class="px-3.5 py-2 rounded-xl bg-amber-100 hover:bg-amber-200 dark:bg-amber-950/60 text-amber-900 dark:text-amber-300 font-extrabold text-xs transition inline-flex items-center gap-1.5 cursor-pointer shadow-2xs">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"></path></svg>
                      <span>Gunakan Data Profil Saya</span>
                    </button>
                  </div>
                </div>

                <form action="{{ route('public.contests.register', $contest->id) }}" method="POST" enctype="multipart/form-data" @submit="submitRegister($event)" novalidate class="space-y-6 pt-3">
                  @csrf

                  <div>
                    <label class="block text-xs font-bold text-gray-800 dark:text-gray-200 mb-2">
                      Nama Lengkap <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="nama_lengkap" x-ref="namaInput" x-model="form.nama_lengkap" @input="touched.nama_lengkap = true; validateNama()" @blur="touched.nama_lengkap = true; validateNama()" placeholder="Nama lengkap sesuai KTP / Identitas" :class="touched.nama_lengkap && errors.nama_lengkap ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500 focus:border-amber-500'" class="w-full px-4 py-3 rounded-xl border bg-gray-50/80 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition shadow-2xs">
                    <p x-show="touched.nama_lengkap && errors.nama_lengkap" x-text="errors.nama_lengkap" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
                  </div>

                  <div>
                    <label class="block text-xs font-bold text-gray-800 dark:text-gray-200 mb-2">
                      Alamat Email Aktif <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" name="email" x-ref="emailInput" x-model="form.email" @input="touched.email = true; validateEmail()" @blur="touched.email = true; validateEmail()" placeholder="email@contoh.com" :class="touched.email && errors.email ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500 focus:border-amber-500'" class="w-full px-4 py-3 rounded-xl border bg-gray-50/80 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition shadow-2xs">
                    <p x-show="touched.email && errors.email" x-text="errors.email" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
                  </div>

                  <div>
                    <label class="block text-xs font-bold text-gray-800 dark:text-gray-200 mb-2">
                      No. WhatsApp Aktif <span class="text-rose-500">*</span>
                    </label>
                    <input type="tel" inputmode="numeric" pattern="[0-9]*" name="no_wa" x-ref="waInput" x-model="form.no_wa" @keypress="if(!/[0-9]/.test($event.key)) $event.preventDefault()" @input="form.no_wa = form.no_wa.replace(/[^0-9]/g, ''); touched.no_wa = true; validateNoWa()" @blur="touched.no_wa = true; validateNoWa()" placeholder="Contoh: 08123456789" :class="touched.no_wa && errors.no_wa ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500 focus:border-amber-500'" class="w-full px-4 py-3 rounded-xl border bg-gray-50/80 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition shadow-2xs">
                    <p x-show="touched.no_wa && errors.no_wa" x-text="errors.no_wa" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
                  </div>

                  <div>
                    <label class="block text-xs font-bold text-gray-800 dark:text-gray-200 mb-2">
                      Asal Kota / Domisili <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="asal_kota" x-ref="kotaInput" x-model="form.asal_kota" @input="touched.asal_kota = true; validateAsalKota()" @blur="touched.asal_kota = true; validateAsalKota()" placeholder="mis. Denpasar, Bali" :class="touched.asal_kota && errors.asal_kota ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500 focus:border-amber-500'" class="w-full px-4 py-3 rounded-xl border bg-gray-50/80 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition shadow-2xs">
                    <p x-show="touched.asal_kota && errors.asal_kota" x-text="errors.asal_kota" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
                  </div>

                  <div>
                    <label class="block text-xs font-bold text-gray-800 dark:text-gray-200 mb-2">
                      Instansi / Komunitas / Kampus
                    </label>
                    <input type="text" name="instansi" x-model="form.instansi" placeholder="mis. Universitas Udayana / Umum" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/80 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition shadow-2xs">
                  </div>

                  <div>
                    <label class="block text-xs font-bold text-gray-800 dark:text-gray-200 mb-2">
                      Bio Singkat / Latar Belakang Fotografi
                    </label>
                    <textarea name="bio_singkat" x-model="form.bio_singkat" rows="3" placeholder="Ceritakan pengalaman singkat Anda di bidang fotografi..." class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/80 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition resize-y leading-relaxed shadow-2xs"></textarea>
                  </div>

                  <div>
                    <label class="block text-xs font-bold text-gray-800 dark:text-gray-200 mb-2">
                      Motivasi Mengikuti Lomba <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="motivasi" x-ref="motivasiInput" x-model="form.motivasi" @input="touched.motivasi = true; validateMotivasi()" @blur="touched.motivasi = true; validateMotivasi()" rows="4" placeholder="Tuliskan motivasi dan harapan Anda dalam mengikuti kompetisi ini..." :class="touched.motivasi && errors.motivasi ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500 focus:border-amber-500'" class="w-full px-4 py-3 rounded-xl border bg-gray-50/80 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition resize-y leading-relaxed shadow-2xs"></textarea>
                    <p x-show="touched.motivasi && errors.motivasi" x-text="errors.motivasi" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
                  </div>

                  <div class="space-y-3">
                    <label class="block text-xs font-bold text-gray-800 dark:text-gray-200">
                      Upload Berkas CV / Portofolio <span class="text-gray-400 font-normal">(Opsional)</span>
                    </label>
                    <div x-ref="cvDropzone"
                         tabindex="0"
                         role="button"
                         @click="$refs.cvFileInput.click()"
                         @keydown.enter.prevent="$refs.cvFileInput.click()"
                         @keydown.space.prevent="$refs.cvFileInput.click()"
                         @dragover.prevent="cvDragging = true"
                         @dragleave.prevent="cvDragging = false"
                         @drop.prevent="handleCvDrop($event)"
                         class="w-full min-h-[120px] px-6 py-7 rounded-2xl border-2 border-dashed flex flex-col items-center justify-center text-center gap-2 cursor-pointer transition outline-none"
                         :class="cvDragging
                             ? 'border-amber-500 bg-amber-50/70 dark:bg-amber-950/25'
                             : (cvError
                                 ? 'border-red-400 bg-red-50/40 dark:bg-red-950/20 focus:ring-2 focus:ring-red-400'
                                 : 'border-gray-200 dark:border-gray-700 bg-gray-50/80 dark:bg-gray-900/60 hover:border-amber-400 dark:hover:border-amber-500 hover:bg-amber-50/30 dark:hover:bg-amber-950/20 focus:ring-2 focus:ring-amber-500')">
                      <input type="file"
                             name="cv_portfolio_file"
                             x-ref="cvFileInput"
                             accept=".pdf,.doc,.docx,.zip,.rar"
                             @change="handleCvSelect($event)"
                             class="sr-only">
                      <svg class="w-6 h-6 transition-colors" :class="cvDragging ? 'text-amber-500' : 'text-gray-400 dark:text-gray-500'" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                      </svg>
                      <p class="text-xs font-bold text-gray-700 dark:text-gray-200">
                        Seret dan lepas di sini
                      </p>
                      <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">
                        Format PDF, DOC, DOCX, ZIP, RAR maksimal 10 MB
                      </p>
                    </div>

                    <div x-show="cvFileName" x-cloak class="px-4 py-2.5 rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 flex items-center justify-between gap-3 shadow-2xs">
                      <span class="text-xs font-bold text-gray-700 dark:text-gray-300 truncate" x-text="cvFileName + (cvFileSize ? ' • ' + cvFileSize : '')"></span>
                      <button type="button" @click="removeCvFile()" class="text-[11px] font-bold text-rose-600 dark:text-rose-400 hover:underline shrink-0 cursor-pointer">Hapus</button>
                    </div>

                    <p x-show="cvError" x-text="cvError" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
                  </div>

                  <div>
                    <label class="block text-xs font-bold text-gray-800 dark:text-gray-200 mb-2">
                      Atau Tautan Portofolio / Instagram / Website
                    </label>
                    <input type="url" name="portfolio_link" x-model="form.portfolio_link" placeholder="https://instagram.com/akun atau link GDrive" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/80 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition shadow-2xs">
                  </div>

                  <div class="flex items-center justify-end gap-3 pt-4">
                    <button type="button" @click="showForm = false" class="px-5 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold text-xs transition cursor-pointer">
                      Batal
                    </button>
                    <button type="submit" class="px-7 py-3 bg-amber-400 hover:bg-amber-500 text-gray-950 font-black text-xs rounded-xl shadow-xs transition cursor-pointer">
                      Simpan Data
                    </button>
                  </div>
                </form>
              </div>
              @endif
            </div>

          @elseif($userRegistration->status_pendaftaran === 'pending')
            <!-- CASE 2: Menunggu Review Admin -->
            <div class="p-6 sm:p-8 rounded-3xl bg-amber-50/70 dark:bg-amber-950/30 border border-amber-200/80 dark:border-amber-800/60 space-y-4">
              <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-2xl bg-amber-400 text-gray-950 flex items-center justify-center shrink-0">
                  <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </span>
                <div>
                  <h4 class="text-base font-extrabold text-gray-900 dark:text-white">Formulir Pendaftaran Anda Sedang Dikurasi</h4>
                  <p class="text-xs text-gray-500 dark:text-gray-400">Pengajuan calon peserta Anda atas nama <span class="font-bold text-gray-900 dark:text-white">{{ $userRegistration->nama_lengkap }}</span> telah diterima dan sedang direview oleh admin.</p>
                </div>
              </div>

              <div class="p-4 rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-xs space-y-2">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-gray-600 dark:text-gray-300">
                  <p><span class="font-bold text-gray-900 dark:text-white">Email:</span> {{ $userRegistration->email }}</p>
                  <p><span class="font-bold text-gray-900 dark:text-white">WhatsApp:</span> {{ $userRegistration->no_wa }}</p>
                  <p><span class="font-bold text-gray-900 dark:text-white">Asal Kota:</span> {{ $userRegistration->asal_kota }}</p>
                  <p><span class="font-bold text-gray-900 dark:text-white">Tanggal Daftar:</span> {{ $userRegistration->created_at->translatedFormat('d M Y H:i') }}</p>
                </div>
              </div>
            </div>

          @elseif($userRegistration->status_pendaftaran === 'ditolak')
            <!-- CASE 3: Pendaftaran Ditolak -->
            <div class="p-6 sm:p-8 rounded-3xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 space-y-3">
              <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-2xl bg-rose-500 text-white flex items-center justify-center shrink-0">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                </span>
                <div>
                  <h4 class="text-base font-extrabold text-rose-900 dark:text-rose-200">Pendaftaran Belum Dapat Diterima</h4>
                  <p class="text-xs text-rose-700 dark:text-rose-300">Mohon maaf, kualifikasi portofolio Anda belum memenuhi kriteria pada kompetisi kali ini.</p>
                </div>
              </div>
              @if($userRegistration->catatan_admin)
                <div class="p-3.5 rounded-xl bg-white dark:bg-gray-800 border border-rose-200 text-xs text-gray-700 dark:text-gray-300">
                  <span class="font-bold text-rose-600 dark:text-rose-400 block mb-1">Catatan dari Admin:</span>
                  <p>{{ $userRegistration->catatan_admin }}</p>
                </div>
              @endif
            </div>

          @elseif($userRegistration->status_pendaftaran === 'menunggu_pembayaran')
            <!-- CASE 4: Lomba Berbayar - Disetujui Admin, Menunggu Transfer & Upload Bukti Pembayaran -->
            <div class="p-6 sm:p-8 rounded-3xl bg-blue-50/70 dark:bg-blue-950/30 border border-blue-200/80 dark:border-blue-800/60 space-y-6">
              <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-2xl bg-blue-500 text-white flex items-center justify-center shrink-0 font-black">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </span>
                <div>
                  <h4 class="text-base font-extrabold text-gray-900 dark:text-white">Pendaftaran Disetujui! Selesaikan Pembayaran Registrasi</h4>
                  <p class="text-xs text-gray-500 dark:text-gray-400">Admin telah menerima kualifikasi Anda. Silakan transfer nominal pendaftaran ke rekening resmi penyelenggara.</p>
                </div>
              </div>

              <!-- Rincian Rekening Bank -->
              <div class="p-5 rounded-2xl bg-white dark:bg-gray-800 border border-blue-200 dark:border-blue-800/60 space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-gray-100 dark:border-gray-700">
                  <span class="text-xs font-bold text-gray-500">Nominal Transfer:</span>
                  <span class="text-lg font-black text-amber-600 dark:text-amber-400">Rp {{ number_format($contest->biaya_pendaftaran, 0, ',', '.') }}</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                  <div>
                    <span class="text-gray-400 block text-[11px]">Bank Tujuan:</span>
                    <span class="font-black text-gray-900 dark:text-white">{{ $contest->bank_name ?? 'BCA' }}</span>
                  </div>
                  <div>
                    <span class="text-gray-400 block text-[11px]">Nomor Rekening:</span>
                    <span class="font-black font-mono text-gray-900 dark:text-white">{{ $contest->bank_account_number ?? '-' }}</span>
                  </div>
                  <div>
                    <span class="text-gray-400 block text-[11px]">Atas Nama:</span>
                    <span class="font-bold text-gray-900 dark:text-white">{{ $contest->bank_account_holder ?? '-' }}</span>
                  </div>
                </div>
              </div>

              <!-- Form Upload Bukti Transfer -->
              @if($userRegistration->status_pembayaran === 'menunggu_verifikasi')
                <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 text-xs text-emerald-800 dark:text-emerald-300 font-bold flex items-center gap-2">
                  <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                  <span>Bukti transfer telah diunggah dan sedang diverifikasi oleh admin. Terima kasih!</span>
                </div>
              @elseif($isHoldPhotographer)
                <div>
                  <button type="button" @click="openPhotographerHoldModal('mengunggah bukti pembayaran lomba')" class="px-6 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs rounded-xl shadow-xs transition cursor-pointer">
                    Unggah Bukti Transfer
                  </button>
                </div>
              @else
                <form action="{{ route('public.contests.payment', $contest->id) }}" 
                      method="POST" 
                      enctype="multipart/form-data" 
                      novalidate
                      class="space-y-4"
                      x-data="{
                        transferFileName: '',
                        transferFileSize: '',
                        transferPreview: '',
                        isDraggingTransfer: false,
                        touched: {{ isset($errors) && $errors->has('bukti_transfer') ? 'true' : 'false' }},
                        error: @js((isset($errors) ? $errors->first('bukti_transfer') : '') ?: ''),
                        handleTransferFile(e) {
                          this.setTransferFile(e.target.files?.[0]);
                        },
                        handleTransferDrop(e) {
                          this.isDraggingTransfer = false;
                          this.setTransferFile(e.dataTransfer?.files?.[0]);
                        },
                        setTransferFile(file) {
                          this.touched = true;
                          if (!file) {
                            this.removeTransferFile();
                            this.error = 'File struk / bukti transfer wajib dipilih.';
                            return false;
                          }
                          const res = window.validateUploadFiles([file], { maxMB: 5 });
                          if (res.hasErrors) {
                            this.removeTransferFile();
                            this.error = res.errorMessage;
                            return false;
                          }
                          const valid = res.validFiles[0];
                          if (!valid) {
                            this.removeTransferFile();
                            this.error = 'File struk / bukti transfer wajib dipilih.';
                            return false;
                          }
                          if (this.transferPreview) URL.revokeObjectURL(this.transferPreview);
                          this.transferFileName = valid.name;
                          this.transferFileSize = window.formatFileSizeMB(valid.size);
                          this.transferPreview = URL.createObjectURL(valid);
                          this.error = '';
                          if (typeof DataTransfer !== 'undefined' && this.$refs.transferFileInput) {
                            const dt = new DataTransfer();
                            dt.items.add(valid);
                            this.$refs.transferFileInput.files = dt.files;
                          }
                          return true;
                        },
                        removeTransferFile() {
                          if (this.transferPreview) URL.revokeObjectURL(this.transferPreview);
                          this.transferFileName = '';
                          this.transferFileSize = '';
                          this.transferPreview = '';
                          if (this.$refs.transferFileInput) this.$refs.transferFileInput.value = '';
                        },
                        submitTransfer(e) {
                          this.touched = true;
                          if (!this.transferFileName) {
                            this.error = 'File struk / bukti transfer wajib dipilih.';
                          }
                          if (!this.transferFileName || this.error) {
                            e.preventDefault();
                            if (this.$refs.transferDropzone) {
                              this.$refs.transferDropzone.focus();
                              this.$refs.transferDropzone.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            }
                            return false;
                          }
                          return true;
                        }
                      }"
                      @submit="submitTransfer($event)">
                  @csrf
                  <div class="space-y-3">
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">Unggah Struk / Bukti Transfer <span class="text-rose-500">*</span></label>
                    <div x-ref="transferDropzone"
                         tabindex="0"
                         role="button"
                         @click="$refs.transferFileInput.click()"
                         @keydown.enter.prevent="$refs.transferFileInput.click()"
                         @keydown.space.prevent="$refs.transferFileInput.click()"
                         @dragover.prevent="isDraggingTransfer = true"
                         @dragleave.prevent="isDraggingTransfer = false"
                         @drop.prevent="handleTransferDrop($event)"
                         class="w-full min-h-[140px] px-6 py-8 rounded-2xl border-2 border-dashed flex flex-col items-center justify-center text-center gap-2 cursor-pointer transition outline-none"
                         :class="isDraggingTransfer
                             ? 'border-amber-500 bg-amber-50/70 dark:bg-amber-950/25'
                             : (touched && error
                                 ? 'border-red-400 bg-red-50/40 dark:bg-red-950/20 focus:ring-2 focus:ring-red-400'
                                 : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-amber-400 dark:hover:border-amber-500 hover:bg-amber-50/30 dark:hover:bg-amber-950/20 focus:ring-2 focus:ring-amber-500')">
                      <input type="file"
                             name="bukti_transfer"
                             x-ref="transferFileInput"
                             accept="image/jpeg,image/png,image/webp"
                             @change="handleTransferFile($event)"
                             class="sr-only">
                      <svg class="w-7 h-7 transition-colors" :class="isDraggingTransfer ? 'text-amber-500' : 'text-gray-400 dark:text-gray-500'" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                      </svg>
                      <p class="text-xs font-bold text-gray-700 dark:text-gray-200">
                        Seret dan lepas di sini
                      </p>
                      <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">
                        Format gambar JPG, PNG, WEBP maksimal 5 MB.
                      </p>
                    </div>

                    <template x-if="transferPreview">
                      <div class="max-w-xs rounded-xl overflow-hidden bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-2xs flex flex-col">
                        <div @click="openLightbox(transferPreview, transferFileName || 'Bukti Transfer')"
                             class="relative aspect-[4/3] w-full overflow-hidden bg-gray-100 dark:bg-gray-900 cursor-pointer group">
                          <img :src="transferPreview" :alt="transferFileName" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                          <div class="absolute inset-0 bg-black/45 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                            <span class="text-xs font-bold text-white tracking-wide">Pratinjau</span>
                          </div>
                        </div>
                        <div class="px-3 py-2 flex items-center justify-between gap-2 bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700/80">
                          <span class="text-[11px] font-bold text-gray-700 dark:text-gray-300 truncate" x-text="transferFileName + (transferFileSize ? ' • ' + transferFileSize : '')"></span>
                          <button type="button"
                                  @click.stop="removeTransferFile(); touched = true; error = 'File struk / bukti transfer wajib dipilih.'"
                                  class="text-[11px] font-bold text-rose-600 dark:text-rose-400 hover:underline shrink-0 cursor-pointer">
                            Hapus
                          </button>
                        </div>
                      </div>
                    </template>

                    <p x-show="touched && error" x-text="error" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
                  </div>
                  <button type="submit" class="px-6 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs rounded-xl shadow-xs transition cursor-pointer">
                    Simpan Data
                  </button>
                </form>
              @endif
            </div>

          @elseif($userRegistration->isApprovedAndReady())
            <!-- CASE 5: Resmi Terdaftar (Gratis langsung disetujui, atau Berbayar lunas diverifikasi) -->
            <div class="space-y-6">
              
              <!-- Banner Status Resmi Terdaftar -->
              <div class="p-6 rounded-3xl bg-emerald-50/80 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                  <span class="w-10 h-10 rounded-2xl bg-emerald-600 text-white flex items-center justify-center shrink-0 font-black">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                  </span>
                  <div>
                    <h4 class="text-base font-extrabold text-emerald-950 dark:text-emerald-200">Anda Resmi Terdaftar sebagai Peserta!</h4>
                    <p class="text-xs text-emerald-700 dark:text-emerald-400">
                      Nama Peserta: <span class="font-bold">{{ $userRegistration->nama_lengkap }}</span> • Kuota Karya: <span class="font-bold">{{ $userSubmissions->count() }} / {{ $contest->max_submissions ?? 2 }} Terunggah</span>
                    </p>
                  </div>
                </div>
              </div>

              <!-- List Karya yang Sudah Diunggah oleh Peserta -->
              @if($userSubmissions->count() > 0)
                <div class="space-y-3">
                  <h4 class="text-sm font-extrabold text-gray-900 dark:text-white">Karya Foto Anda yang Telah Diunggah ({{ $userSubmissions->count() }}):</h4>
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($userSubmissions as $mySub)
                      <div class="p-4 rounded-2xl bg-gray-50 dark:bg-gray-900/60 border border-gray-200 dark:border-gray-700 flex gap-3.5 items-center">
                        <div class="w-20 h-20 rounded-xl overflow-hidden bg-gray-200 shrink-0 border border-gray-200 dark:border-gray-700">
                          <img src="{{ $mySub->image_url }}" alt="{{ $mySub->judul_karya }}" class="w-full h-full object-cover">
                        </div>
                        <div class="space-y-1 min-w-0 flex-1">
                          <h5 class="text-xs font-bold text-gray-900 dark:text-white truncate">{{ $mySub->judul_karya }}</h5>
                          <p class="text-[11px] text-gray-500 truncate">{{ $mySub->deskripsi_karya ?? 'Tanpa deskripsi' }}</p>
                          @if($mySub->media_link)
                            <a href="{{ $mySub->media_link }}" target="_blank" class="text-[10px] text-blue-500 font-bold hover:underline block truncate">Tautan Media Eksternal</a>
                          @endif
                          <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                            Tercatat di Sistem
                          </span>
                        </div>
                      </div>
                    @endforeach
                  </div>
                </div>
              @endif

              <!-- FORM UPLOAD KARYA FOTO (TAHAP 3) -->
              @if($userSubmissions->count() < ($contest->max_submissions ?? 2))
                <div x-data="{
                  showUploadForm: {{ isset($errors) && $errors->hasAny(['judul_karya', 'foto_file', 'media_link', 'deskripsi_karya']) ? 'true' : 'false' }},
                  preview: null,
                  fileSelected: false,
                  fileName: '',
                  fileSize: '',
                  isDraggingKarya: false,
                  judul_karya: @js(old('judul_karya', '')),
                  touched: {
                    judul: {{ isset($errors) && $errors->has('judul_karya') ? 'true' : 'false' }},
                    foto: {{ isset($errors) && $errors->has('foto_file') ? 'true' : 'false' }}
                  },
                  errors: {
                    judul: @js((isset($errors) ? $errors->first('judul_karya') : '') ?: ''),
                    foto: @js((isset($errors) ? $errors->first('foto_file') : '') ?: '')
                  },
                  validateJudul() {
                    if (!this.judul_karya || !this.judul_karya.trim()) {
                      this.errors.judul = 'Judul karya foto wajib diisi.';
                      return false;
                    }
                    if (this.judul_karya.trim().length < 3) {
                      this.errors.judul = 'Judul karya foto minimal 3 karakter.';
                      return false;
                    }
                    this.errors.judul = '';
                    return true;
                  },
                  validateFoto() {
                    if (!this.fileSelected) {
                      if (!this.errors.foto) this.errors.foto = 'File foto karya wajib dipilih.';
                      return false;
                    }
                    this.errors.foto = '';
                    return true;
                  },
                  handleFile(e) {
                    this.setKaryaFile(e.target.files?.[0]);
                  },
                  handleKaryaDrop(e) {
                    this.isDraggingKarya = false;
                    this.setKaryaFile(e.dataTransfer?.files?.[0]);
                  },
                  setKaryaFile(file) {
                    this.touched.foto = true;
                    if (!file) {
                      this.removeKaryaFile();
                      this.errors.foto = 'File foto karya wajib dipilih.';
                      return false;
                    }
                    const res = window.validateUploadFiles([file], { maxMB: 10 });
                    if (res.hasErrors) {
                      this.removeKaryaFile();
                      this.errors.foto = res.errorMessage;
                      return false;
                    }
                    const valid = res.validFiles[0];
                    if (valid) {
                      if (this.preview) URL.revokeObjectURL(this.preview);
                      this.fileSelected = true;
                      this.fileName = valid.name;
                      this.fileSize = window.formatFileSizeMB(valid.size);
                      this.errors.foto = '';
                      this.preview = URL.createObjectURL(valid);
                      if (typeof DataTransfer !== 'undefined' && this.$refs.fotoFileInput) {
                        const dt = new DataTransfer();
                        dt.items.add(valid);
                        this.$refs.fotoFileInput.files = dt.files;
                      }
                      return true;
                    }
                    this.removeKaryaFile();
                    this.errors.foto = 'File foto karya wajib dipilih.';
                    return false;
                  },
                  removeKaryaFile() {
                    if (this.preview) URL.revokeObjectURL(this.preview);
                    this.fileSelected = false;
                    this.fileName = '';
                    this.fileSize = '';
                    this.preview = null;
                    if (this.$refs.fotoFileInput) this.$refs.fotoFileInput.value = '';
                  },
                  submitKarya(e) {
                    this.touched.judul = true;
                    this.touched.foto = true;
                    const validJudul = this.validateJudul();
                    const validFoto = this.validateFoto();
                    if (!validJudul) {
                      e.preventDefault();
                      this.$refs.judulKaryaInput?.focus();
                      this.$refs.judulKaryaInput?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                      return false;
                    }
                    if (!validFoto) {
                      e.preventDefault();
                      if (this.$refs.karyaDropzone) {
                        this.$refs.karyaDropzone.focus();
                        this.$refs.karyaDropzone.scrollIntoView({ behavior: 'smooth', block: 'center' });
                      }
                      return false;
                    }
                    return true;
                  }
                }" class="space-y-4 pt-2">

                  <div x-show="!showUploadForm" class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-6 rounded-2xl bg-purple-50/70 dark:bg-purple-950/30 border border-purple-200/80 dark:border-purple-800/60">
                    <div class="space-y-1">
                      <h4 class="text-sm font-extrabold text-gray-900 dark:text-white">Unggah Karya Foto Anda</h4>
                      <p class="text-xs text-gray-600 dark:text-gray-300">
                        Anda masih memiliki sisa kuota untuk mengunggah {{ ($contest->max_submissions ?? 2) - $userSubmissions->count() }} karya foto lagi.
                      </p>
                    </div>
                    @if($isHoldPhotographer)
                      <button type="button" @click="openPhotographerHoldModal('mengunggah karya foto lomba')" class="px-6 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs rounded-xl shadow-xs transition shrink-0 cursor-pointer">
                        Unggah Karya Foto Baru
                      </button>
                    @else
                      <button type="button" @click="showUploadForm = true" class="px-6 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs rounded-xl shadow-xs transition shrink-0 cursor-pointer">
                        Unggah Karya Foto Baru
                      </button>
                    @endif
                  </div>

                  @if(!$isHoldPhotographer)
                  <div 
                    x-show="showUploadForm" 
                    x-cloak 
                    x-collapse.duration.600ms
                    x-transition:enter="transition-all duration-600 ease-out transform origin-top"
                    x-transition:enter-start="opacity-0 -translate-y-4"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition-all duration-300 ease-in transform origin-top"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-4"
                    class="p-6 rounded-3xl bg-gray-50 dark:bg-gray-900/60 border border-gray-200 dark:border-gray-700 space-y-5"
                  >
                    <div class="flex items-center justify-between pb-2">
                      <h4 class="text-sm font-black text-gray-900 dark:text-white">Unggah Karya Foto Ke-{{ $userSubmissions->count() + 1 }}</h4>
                      <button type="button" @click="showUploadForm = false" class="text-xs font-bold text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 cursor-pointer">Batal</button>
                    </div>

                    <form action="{{ route('public.contests.submit', $contest->id) }}" method="POST" enctype="multipart/form-data" @submit="submitKarya($event)" novalidate class="space-y-4">
                      @csrf

                      <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Judul Karya Foto <span class="text-rose-500">*</span></label>
                        <input type="text" name="judul_karya" x-ref="judulKaryaInput" x-model="judul_karya" @input="touched.judul = true; validateJudul()" @blur="touched.judul = true; validateJudul()" placeholder="mis. Pesona Senja di Tanah Lot" :class="touched.judul && errors.judul ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500'" class="w-full px-4 py-2.5 rounded-xl border bg-white dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2">
                        <p x-show="touched.judul && errors.judul" x-text="errors.judul" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
                      </div>

                      <div class="space-y-3">
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">
                          Foto Karya dari Perangkat <span class="text-rose-500">*</span>
                        </label>
                        <div x-ref="karyaDropzone"
                             tabindex="0"
                             role="button"
                             @click="$refs.fotoFileInput.click()"
                             @keydown.enter.prevent="$refs.fotoFileInput.click()"
                             @keydown.space.prevent="$refs.fotoFileInput.click()"
                             @dragover.prevent="isDraggingKarya = true"
                             @dragleave.prevent="isDraggingKarya = false"
                             @drop.prevent="handleKaryaDrop($event)"
                             class="w-full min-h-[160px] px-6 py-9 rounded-2xl border-2 border-dashed flex flex-col items-center justify-center text-center gap-2 cursor-pointer transition outline-none"
                             :class="isDraggingKarya
                                 ? 'border-amber-500 bg-amber-50/70 dark:bg-amber-950/25'
                                 : (touched.foto && errors.foto
                                     ? 'border-red-400 bg-red-50/40 dark:bg-red-950/20 focus:ring-2 focus:ring-red-400'
                                     : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 hover:border-amber-400 dark:hover:border-amber-500 hover:bg-amber-50/30 dark:hover:bg-amber-950/20 focus:ring-2 focus:ring-amber-500')">
                          <input type="file"
                                 name="foto_file"
                                 x-ref="fotoFileInput"
                                 accept="image/jpeg,image/png,image/webp"
                                 @change="handleFile($event)"
                                 class="sr-only">
                          <svg class="w-7 h-7 sm:w-8 sm:h-8 transition-colors" :class="isDraggingKarya ? 'text-amber-500' : 'text-gray-400 dark:text-gray-500'" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                          </svg>
                          <p class="text-xs sm:text-sm font-bold text-gray-700 dark:text-gray-200">
                            Seret dan lepas di sini
                          </p>
                          <p class="text-xs text-gray-400 dark:text-gray-500 font-medium">
                            Format JPG, PNG, WEBP maksimal 10 MB.
                          </p>
                        </div>

                        <template x-if="preview">
                          <div class="max-w-md rounded-xl overflow-hidden bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-2xs flex flex-col">
                            <div @click="openLightbox(preview, judul_karya || fileName || 'Pratinjau Karya Foto')"
                                 class="relative aspect-[16/9] w-full overflow-hidden bg-gray-100 dark:bg-gray-900 cursor-pointer group">
                              <img :src="preview" :alt="fileName || 'Preview Foto Karya'" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                              <div class="absolute inset-0 bg-black/45 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                <span class="text-xs font-bold text-white tracking-wide">Pratinjau</span>
                              </div>
                            </div>
                            <div class="px-3 py-2 flex items-center justify-between gap-2 bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700/80">
                              <span class="text-[11px] font-bold text-gray-700 dark:text-gray-300 truncate" x-text="fileName + (fileSize ? ' • ' + fileSize : '')"></span>
                              <button type="button"
                                      @click.stop="removeKaryaFile(); touched.foto = true; validateFoto()"
                                      class="text-[11px] font-bold text-rose-600 dark:text-rose-400 hover:underline shrink-0 cursor-pointer">
                                Hapus
                              </button>
                            </div>
                          </div>
                        </template>

                        <p x-show="touched.foto && errors.foto" x-text="errors.foto" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
                      </div>

                      @if($contest->allow_link)
                        <div>
                          <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Tautan Media Eksternal (Opsional)</label>
                          <input type="url" name="media_link" placeholder="https://drive.google.com/... atau link video karya" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none">
                        </div>
                      @endif

                      <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Deskripsi / Konsep Karya Foto</label>
                        <textarea name="deskripsi_karya" rows="3" placeholder="Tuliskan latar belakang konsep, lokasi pengambilan, dan cerita di balik karya ini..." class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none resize-none"></textarea>
                      </div>

                      <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" @click="showUploadForm = false" class="px-5 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold text-xs transition">
                          Batal
                        </button>
                        <button type="submit" class="px-6 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs rounded-xl shadow-xs transition cursor-pointer">
                          Simpan Data
                        </button>
                      </div>
                    </form>
                  </div>
                  @endif
                </div>
              @else
                <div class="p-4 rounded-2xl bg-gray-50 dark:bg-gray-900/60 border border-gray-200 dark:border-gray-700 text-center">
                  <p class="text-xs font-bold text-gray-600 dark:text-gray-300">
                    Anda telah mengunggah seluruh kuota maksimal ({{ $contest->max_submissions ?? 2 }} karya). Semoga berhasil pada tahap penjurian!
                  </p>
                </div>
              @endif

            </div>
          @endif
        @endguest

      </div>

    </div>

    <!-- ========================================== -->
    <!-- STATE: EVENT SELESAI (status == selesai)   -->
    <!-- ========================================== -->
    @if($contest->status === 'selesai')
      
      <!-- Section Juara: Panggung Kehormatan -->
      @if($winners->count() > 0)
        <div class="bg-white dark:bg-gray-800 p-8 sm:p-14 lg:p-16 rounded-3xl border border-amber-300 dark:border-amber-700/80 shadow-xs space-y-8">
          <div class="text-center max-w-xl mx-auto space-y-1">
            <span class="px-3 py-1 rounded-full bg-amber-400 text-gray-950 font-black text-[10px] uppercase tracking-widest">Panggung Kehormatan</span>
            <h2 class="text-2xl font-black text-gray-900 dark:text-white">Pemenang Juara Lomba Foto</h2>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($winners as $win)
              <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-2xl border border-amber-200 dark:border-amber-800/60 shadow-xs space-y-3 text-center">
                <div class="h-48 rounded-xl overflow-hidden relative border border-gray-200 dark:border-gray-700 bg-gray-100">
                  <img 
                    onerror="this.onerror=null;this.src=window.SVG_IMAGE_FALLBACK||'data:image/svg+xml;charset=UTF-8,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'800\' height=\'600\' viewBox=\'0 0 800 600\' fill=\'none\'%3E%3Crect width=\'800\' height=\'600\' fill=\'%23F3F4F6\'/%3E%3Cpath d=\'M360 260C360 248.954 368.954 240 380 240H420C431.046 240 440 248.954 440 260V265H450C466.569 265 480 278.431 480 295V345C480 361.569 466.569 375 450 375H350C333.431 375 320 361.569 320 345V295C320 278.431 333.431 265 350 265H360V260Z\' stroke=\'%239CA3AF\' stroke-width=\'12\' stroke-linecap=\'round\' stroke-linejoin=\'round\'/%3E%3Ccircle cx=\'400\' cy=\'320\' r=\'30\' stroke=\'%239CA3AF\' stroke-width=\'12\'/%3E%3Cline x1=\'310\' y1=\'240\' x2=\'490\' y2=\'390\' stroke=\'%23EF4444\' stroke-width=\'10\' stroke-linecap=\'round\'/%3E%3Ctext x=\'400\' y=\'450\' font-family=\'sans-serif\' font-size=\'22\' font-weight=\'700\' fill=\'%236B7280\' text-anchor=\'middle\'%3EGambar Tidak Dapat Dimuat%3C/text%3E%3C/svg%3E';" 
                    src="{{ $win->image_url }}" 
                    alt="{{ $win->judul_karya }}" 
                    class="w-full h-full object-cover" 
                  />
                  <span class="absolute top-2 left-2 px-2.5 py-1 bg-amber-400 text-gray-950 text-[10px] font-black rounded-md shadow-xs">
                    {{ $win->status_submission === 'winner_1' ? 'JUARA 1' : ($win->status_submission === 'winner_2' ? 'JUARA 2' : 'JUARA 3') }}
                  </span>
                </div>
                <div class="space-y-0.5">
                  <h4 class="font-extrabold text-xs text-gray-900 dark:text-white line-clamp-1">{{ $win->judul_karya }}</h4>
                  <p class="text-[11px] text-gray-500 dark:text-gray-400 font-medium">Oleh: {{ $win->user->nama ?? 'Peserta' }}</p>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      @endif

      <!-- Section Galeri Karya Peserta Lainnya -->
      @php
        $otherSubmissions = $submissions->whereNotIn('status_submission', ['winner_1', 'winner_2', 'winner_3']);
      @endphp
      @if($otherSubmissions->count() > 0)
        <div class="bg-white dark:bg-gray-800 p-8 sm:p-14 lg:p-16 rounded-3xl border border-gray-200 dark:border-gray-700 shadow-xs space-y-8">
          <div class="flex items-center justify-between">
            <h3 class="text-base sm:text-lg font-black text-gray-900 dark:text-white">Galeri Karya Peserta Lainnya</h3>
            <span class="text-xs text-gray-500 font-bold">{{ $otherSubmissions->count() }} Karya</span>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            @foreach($otherSubmissions as $sub)
              <div class="bg-gray-50 dark:bg-gray-900 p-3.5 rounded-2xl border border-gray-100 dark:border-gray-700/80 shadow-2xs space-y-2.5">
                <div class="h-44 rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-800 border border-gray-200/80 dark:border-gray-700">
                  <img 
                    onerror="this.onerror=null;this.src=window.SVG_IMAGE_FALLBACK||'data:image/svg+xml;charset=UTF-8,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'800\' height=\'600\' viewBox=\'0 0 800 600\' fill=\'none\'%3E%3Crect width=\'800\' height=\'600\' fill=\'%23F3F4F6\'/%3E%3Cpath d=\'M360 260C360 248.954 368.954 240 380 240H420C431.046 240 440 248.954 440 260V265H450C466.569 265 480 278.431 480 295V345C480 361.569 466.569 375 450 375H350C333.431 375 320 361.569 320 345V295C320 278.431 333.431 265 350 265H360V260Z\' stroke=\'%239CA3AF\' stroke-width=\'12\' stroke-linecap=\'round\' stroke-linejoin=\'round\'/%3E%3Ccircle cx=\'400\' cy=\'320\' r=\'30\' stroke=\'%239CA3AF\' stroke-width=\'12\'/%3E%3Cline x1=\'310\' y1=\'240\' x2=\'490\' y2=\'390\' stroke=\'%23EF4444\' stroke-width=\'10\' stroke-linecap=\'round\'/%3E%3Ctext x=\'400\' y=\'450\' font-family=\'sans-serif\' font-size=\'22\' font-weight=\'700\' fill=\'%236B7280\' text-anchor=\'middle\'%3EGambar Tidak Dapat Dimuat%3C/text%3E%3C/svg%3E';" 
                    src="{{ $sub->image_url }}" 
                    alt="{{ $sub->judul_karya }}" 
                    class="w-full h-full object-cover" 
                  />
                </div>
                <div class="space-y-0.5">
                  <h4 class="font-extrabold text-xs text-gray-900 dark:text-white line-clamp-1">{{ $sub->judul_karya }}</h4>
                  <p class="text-[10px] text-gray-400">Oleh: {{ $sub->user->nama ?? 'Peserta' }}</p>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      @endif

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
        <p class="text-xs sm:text-[13px] text-gray-600 dark:text-gray-300 leading-relaxed text-center">
          Untuk dapat mendaftar dan mengunggah karya pada kompetisi foto ini, Anda harus masuk ke akun LensMatch terlebih dahulu.
        </p>

        <!-- Action Button -->
        <div>
          <a href="{{ route('login', ['redirect' => request()->fullUrl()]) }}" class="w-full py-3 px-4 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs sm:text-sm rounded-xl shadow-xs transition hover:scale-[1.01] flex items-center justify-center cursor-pointer">
            Masuk ke Akun
          </a>
        </div>

      </div>
    </div>
  </div>

  <!-- Lightbox Pratinjau Foto (Rule #97) -->
  <div x-show="lightboxOpen"
       x-cloak
       @keydown.escape.window="closeLightbox()"
       @click.self="closeLightbox()"
       class="fixed inset-0 z-[9999] bg-black/90 backdrop-blur-md flex flex-col items-center justify-center p-4 sm:p-8">
    <button type="button"
            @click="closeLightbox()"
            aria-label="Tutup pratinjau"
            class="absolute top-4 right-4 sm:top-6 sm:right-6 w-11 h-11 rounded-full bg-white/10 hover:bg-white/25 text-white flex items-center justify-center transition cursor-pointer">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
    <img :src="lightboxUrl" :alt="lightboxCaption" class="max-w-full max-h-[80vh] object-contain rounded-2xl shadow-2xl border border-white/10 select-none">
    <div class="mt-4 px-4 py-2 rounded-full bg-white/10 text-xs font-semibold text-white/90" x-text="lightboxCaption"></div>
  </div>

</section>
@endsection