@extends('layouts.app')

@section('title', 'Aktivitas Komunitas Saya — LensMatch')

@section('content')
<section class="py-12 bg-white dark:bg-gray-900 min-h-screen" 
         x-data="{ 
           activeTab: '{{ $activeTab }}',
           lightboxOpen: false,
           lightboxImg: '',
           lightboxTitle: '',
           openLightbox(img, title) {
             this.lightboxImg = img;
             this.lightboxTitle = title;
             this.lightboxOpen = true;
           }
         }">

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

    <!-- Breadcrumb Navigation (Rule #62 & Rule #72) -->
    <nav class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
      <a href="{{ url('/') }}" class="hover:text-amber-500 transition">Beranda</a>
      <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
      <span class="text-gray-900 dark:text-white font-bold">Aktivitas Komunitas</span>
    </nav>

    <!-- Page Header Title -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">
          Aktivitas Komunitas Saya
        </h1>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
          Pantau progres kursus e-learning, partisipasi kompetisi lomba foto, dan riwayat diskusi forum Anda.
        </p>
      </div>
    </div>

    <!-- 4 Clean Summary Metric Cards (Rule #64 Compliant: 2 Baris Murni text-xs) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
      <!-- Card 1: E-Learning Berjalan -->
      <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-2xs space-y-2">
        <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">E-Learning Aktif</span>
        <h2 class="text-xs font-black text-gray-900 dark:text-white tracking-tight">{{ $stats['elearning_active'] }} Kursus</h2>
      </div>

      <!-- Card 2: E-Learning Selesai -->
      <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-2xs space-y-2">
        <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">E-Learning Tuntas</span>
        <h2 class="text-xs font-black text-gray-900 dark:text-white tracking-tight">{{ $stats['elearning_completed'] }} Kursus</h2>
      </div>

      <!-- Card 3: Lomba Diikuti -->
      <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-2xs space-y-2">
        <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Lomba Diikuti</span>
        <h2 class="text-xs font-black text-gray-900 dark:text-white tracking-tight">{{ $stats['contests_total'] }} Kompetisi</h2>
      </div>

      <!-- Card 4: Topik Forum -->
      <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-2xs space-y-2">
        <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Diskusi Forum</span>
        <h2 class="text-xs font-black text-gray-900 dark:text-white tracking-tight">{{ $stats['forum_posts_total'] }} Topik</h2>
      </div>
    </div>

    <!-- Tabs: E-Learning vs Lomba Foto vs Diskusi Forum (Rule #58 Compliant: Clean Underline Text, Zero Full Divider) -->
    <div class="flex items-center gap-6 sm:gap-8 overflow-x-auto pb-1">
      <button type="button" 
              @click="activeTab = 'elearning'" 
              :class="activeTab === 'elearning' ? 'text-amber-600 dark:text-amber-400 font-black' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-bold'"
              class="group py-2 text-xs sm:text-sm transition cursor-pointer flex items-center gap-2 whitespace-nowrap">
        <svg class="w-4 h-4 transition duration-200" 
             :class="activeTab === 'elearning' ? 'text-amber-500 dark:text-amber-400' : 'text-gray-400 group-hover:text-amber-500 dark:group-hover:text-amber-400'" 
             fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
        <span :class="activeTab === 'elearning' ? 'underline underline-offset-8 decoration-2 decoration-amber-500 dark:decoration-amber-400' : ''">E-Learning Saya</span>
        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold"
              :class="activeTab === 'elearning' ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300'">
          {{ $elearningProgresses->count() }}
        </span>
      </button>

      <button type="button" 
              @click="activeTab = 'contests'" 
              :class="activeTab === 'contests' ? 'text-amber-600 dark:text-amber-400 font-black' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-bold'"
              class="group py-2 text-xs sm:text-sm transition cursor-pointer flex items-center gap-2 whitespace-nowrap">
        <svg class="w-4 h-4 transition duration-200" 
             :class="activeTab === 'contests' ? 'text-amber-500 dark:text-amber-400' : 'text-gray-400 group-hover:text-amber-500 dark:group-hover:text-amber-400'" 
             fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.504-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.004 0A2.25 2.25 0 0015 12V6.75a2.25 2.25 0 00-2.25-2.25h-1.5A2.25 2.25 0 009 6.75V12a2.25 2.25 0 001.504 2.125m3.996 0H9.5"/></svg>
        <span :class="activeTab === 'contests' ? 'underline underline-offset-8 decoration-2 decoration-amber-500 dark:decoration-amber-400' : ''">Lomba Foto</span>
        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold"
              :class="activeTab === 'contests' ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300'">
          {{ $contestRegistrations->count() }}
        </span>
      </button>

      <button type="button" 
              @click="activeTab = 'forum'" 
              :class="activeTab === 'forum' ? 'text-amber-600 dark:text-amber-400 font-black' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-bold'"
              class="group py-2 text-xs sm:text-sm transition cursor-pointer flex items-center gap-2 whitespace-nowrap">
        <svg class="w-4 h-4 transition duration-200" 
             :class="activeTab === 'forum' ? 'text-amber-500 dark:text-amber-400' : 'text-gray-400 group-hover:text-amber-500 dark:group-hover:text-amber-400'" 
             fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3.57-3.091c-.48-.008-.962-.02-1.44-.037a48.79 48.79 0 01-4.74-.448m6.75-8.072V6.75A2.25 2.25 0 0013.5 4.5h-9a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25h.75v3l3-3h3"/></svg>
        <span :class="activeTab === 'forum' ? 'underline underline-offset-8 decoration-2 decoration-amber-500 dark:decoration-amber-400' : ''">Diskusi Forum</span>
        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold"
              :class="activeTab === 'forum' ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300'">
          {{ $forumPosts->count() }}
        </span>
      </button>
    </div>

    <!-- ========================================== -->
    <!-- TAB 1: E-LEARNING SAYA                     -->
    <!-- ========================================== -->
    <div x-show="activeTab === 'elearning'" x-cloak class="space-y-6">
      @if($elearningProgresses->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 items-start">
          @foreach($elearningProgresses as $item)
            @php
              $course = $item->elearning;
              $pct = $course ? $course->calculateUserPercentage(auth()->id()) : $item->progress_percentage;
              $isCompleted = ($pct >= 100 || $item->status === 'selesai');
            @endphp
            @if($course)
              <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-2xs overflow-hidden flex flex-col justify-between transition hover:shadow-md">
                
                <!-- Course Banner 16:9 -->
                <div class="aspect-video w-full bg-gray-100 dark:bg-gray-900 relative overflow-hidden">
                  @if($course->thumbnail_url)
                    <img src="{{ $course->thumbnail_url }}" alt="{{ $course->judul }}" class="w-full h-full object-cover">
                  @else
                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                      <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                    </div>
                  @endif
                  
                  <span class="absolute top-3 left-3 px-2.5 py-1 rounded-lg bg-black/60 backdrop-blur-md text-white text-[10px] font-bold">
                    {{ $course->kategori }}
                  </span>

                  @if($isCompleted)
                    <span class="absolute top-3 right-3 px-2.5 py-1 rounded-lg bg-emerald-500 text-white text-[10px] font-black shadow-xs">
                      Selesai
                    </span>
                  @endif
                </div>

                <!-- Course Body -->
                <div class="p-5 sm:p-6 space-y-4 flex-1 flex flex-col justify-between">
                  <div class="space-y-2">
                    <div class="flex items-center gap-2 text-[11px] text-gray-500 dark:text-gray-400 font-medium">
                      <span>{{ ucfirst($course->level ?? 'Pemula') }}</span>
                      <span>•</span>
                      <span>{{ $course->durasi ?? '1 Jam' }}</span>
                      <span>•</span>
                      <span>{{ $course->lessons_count ?? $course->lessons->count() }} Materi</span>
                    </div>

                    <h3 class="text-sm font-black text-gray-900 dark:text-white line-clamp-2 leading-snug">
                      {{ $course->judul }}
                    </h3>
                  </div>

                  <!-- Progress Bar -->
                  <div class="space-y-1.5 pt-2">
                    <div class="flex items-center justify-between text-[11px] font-bold">
                      <span class="text-gray-500 dark:text-gray-400">Progres Belajar</span>
                      <span class="{{ $isCompleted ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-500' }} font-black">{{ $pct }}%</span>
                    </div>
                    <div class="w-full h-2 rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                      <div class="h-full rounded-full transition-all duration-300 {{ $isCompleted ? 'bg-emerald-500' : 'bg-amber-400' }}" style="width: {{ $pct }}%"></div>
                    </div>
                  </div>

                  <!-- Card Action Footer -->
                  <div class="pt-3">
                    @if($isCompleted)
                      <div class="grid grid-cols-2 gap-2">
                        <a href="{{ route('public.elearning.certificate', $course->slug) }}" 
                           class="py-2.5 px-3 bg-emerald-50 dark:bg-emerald-950/40 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 font-bold text-xs rounded-xl transition text-center flex items-center justify-center gap-1.5">
                          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                          <span>Sertifikat</span>
                        </a>
                        <a href="{{ route('public.elearning.show', $course->slug) }}" 
                           class="py-2.5 px-3 bg-[#222222] hover:bg-black dark:bg-gray-700 dark:hover:bg-gray-600 text-white font-bold text-xs rounded-xl transition text-center flex items-center justify-center gap-1">
                          <span>Ulas Materi</span>
                        </a>
                      </div>
                    @else
                      <a href="{{ route('public.elearning.show', $course->slug) }}" 
                         class="w-full py-2.5 px-4 bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs rounded-xl shadow-xs transition text-center flex items-center justify-center gap-2">
                        <span>Lanjutkan Belajar</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                      </a>
                    @endif
                  </div>

                </div>

              </div>
            @endif
          @endforeach
        </div>
      @else
        <!-- Empty State (Rule #94: Zero Dummy Fallback) -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 p-12 text-center space-y-4">
          <div class="w-14 h-14 rounded-2xl bg-amber-50 dark:bg-amber-950/40 text-amber-500 flex items-center justify-center mx-auto">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
          </div>
          <div class="max-w-md mx-auto space-y-1">
            <h3 class="text-sm font-black text-gray-900 dark:text-white">
              Belum Ada E-Learning yang Diikuti
            </h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
              Tingkatkan wawasan fotografi Anda dengan mempelajari materi terstruktur dari fotografer profesional.
            </p>
          </div>
          <div class="pt-2">
            <a href="{{ route('public.elearning.index') }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs rounded-xl shadow-xs transition">
              <span>Jelajahi E-Learning</span>
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            </a>
          </div>
        </div>
      @endif
    </div>

    <!-- ========================================== -->
    <!-- TAB 2: LOMBA FOTO                          -->
    <!-- ========================================== -->
    <div x-show="activeTab === 'contests'" x-cloak class="space-y-6">
      @if($contestRegistrations->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
          @foreach($contestRegistrations as $reg)
            @php
              $contest = $reg->contest;
              $submission = $reg->submissions->first();
            @endphp
            @if($contest)
              <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-2xs p-5 sm:p-6 space-y-5 transition hover:shadow-md">
                
                <!-- Contest Header -->
                <div class="space-y-2">
                  <div class="flex items-center justify-between gap-2">
                    <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 px-2.5 py-1 rounded-lg">
                      {{ $contest->kategori ?? 'Kompetisi Terbuka' }}
                    </span>
                    <span class="text-xs font-black text-gray-900 dark:text-white">
                      Hadiah Rp {{ number_format($contest->hadiah ?? 0, 0, ',', '.') }}
                    </span>
                  </div>

                  <h3 class="text-base font-black text-gray-900 dark:text-white line-clamp-1">
                    {{ $contest->judul }}
                  </h3>
                </div>

                <!-- Status Cards (Pure Information - Zero AI Slop) -->
                <div class="grid grid-cols-2 gap-3 text-xs">
                  <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700/60 space-y-1">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Pendaftaran</span>
                    @if($reg->status_pendaftaran === 'disetujui')
                      <span class="font-black text-emerald-600 dark:text-emerald-400">Diterima</span>
                    @elseif($reg->status_pendaftaran === 'ditolak')
                      <span class="font-black text-rose-600 dark:text-rose-400">Ditolak</span>
                    @else
                      <span class="font-black text-amber-500">Menunggu Verifikasi</span>
                    @endif
                  </div>

                  <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700/60 space-y-1">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Biaya Masuk</span>
                    @if(in_array($reg->status_pembayaran, ['lunas', 'gratis']))
                      <span class="font-black text-emerald-600 dark:text-emerald-400">Lunas / Bebas Biaya</span>
                    @elseif($reg->status_pembayaran === 'menunggu_verifikasi')
                      <span class="font-black text-amber-500">Menunggu Verifikasi</span>
                    @else
                      <span class="font-black text-gray-500">Belum Bayar</span>
                    @endif
                  </div>
                </div>

                <!-- Submission Status Box -->
                <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700/60">
                  <div class="flex items-center justify-between gap-3">
                    <div class="space-y-0.5">
                      <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Karya Submisi</span>
                      @if($submission)
                        <p class="text-xs font-black text-gray-900 dark:text-white line-clamp-1">
                          {{ $submission->judul_karya }}
                        </p>
                        <span class="text-[11px] text-emerald-600 dark:text-emerald-400 font-bold">Karya telah dikirimkan</span>
                      @else
                        <p class="text-xs font-bold text-gray-500">
                          Belum mengirim karya foto
                        </p>
                      @endif
                    </div>

                    @if($submission && $submission->image_url)
                      <button type="button" 
                              @click="openLightbox('{{ $submission->image_url }}', '{{ $submission->judul_karya }}')"
                              class="w-12 h-12 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 shrink-0 cursor-pointer hover:opacity-80 transition"
                              title="Klik untuk pratinjau">
                        <img src="{{ $submission->image_url }}" alt="{{ $submission->judul_karya }}" class="w-full h-full object-cover">
                      </button>
                    @endif
                  </div>
                </div>

                <!-- Action Button -->
                <div class="pt-1">
                  <a href="{{ route('public.contests.show', $contest->slug ?: $contest->id) }}" 
                     class="w-full py-2.5 px-4 bg-[#222222] hover:bg-black dark:bg-gray-700 dark:hover:bg-gray-600 text-white font-extrabold text-xs rounded-xl shadow-xs transition text-center flex items-center justify-center gap-2">
                    <span>Lihat Halaman Lomba</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                  </a>
                </div>

              </div>
            @endif
          @endforeach
        </div>
      @else
        <!-- Empty State -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 p-12 text-center space-y-4">
          <div class="w-14 h-14 rounded-2xl bg-amber-50 dark:bg-amber-950/40 text-amber-500 flex items-center justify-center mx-auto">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.504-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.004 0A2.25 2.25 0 0015 12V6.75a2.25 2.25 0 00-2.25-2.25h-1.5A2.25 2.25 0 009 6.75V12a2.25 2.25 0 001.504 2.125m3.996 0H9.5"/></svg>
          </div>
          <div class="max-w-md mx-auto space-y-1">
            <h3 class="text-sm font-black text-gray-900 dark:text-white">
              Belum Mengikuti Lomba Foto
            </h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
              Tunjukkan karya visual terbaik Anda, bersaing secara sehat, dan menangkan hadiah menarik di kompetisi resmi LensMatch.
            </p>
          </div>
          <div class="pt-2">
            <a href="{{ route('public.contests.index') }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs rounded-xl shadow-xs transition">
              <span>Jelajahi Lomba Foto</span>
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            </a>
          </div>
        </div>
      @endif
    </div>

    <!-- ========================================== -->
    <!-- TAB 3: DISKUSI FORUM SAYA                  -->
    <!-- ========================================== -->
    <div x-show="activeTab === 'forum'" x-cloak class="space-y-6">
      @if($forumPosts->isNotEmpty())
        <div class="space-y-4">
          @foreach($forumPosts as $post)
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-2xs p-5 sm:p-6 space-y-3 transition hover:shadow-md">
              <div class="flex items-center justify-between gap-3">
                <span class="text-[10px] font-bold text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 px-2.5 py-1 rounded-lg">
                  {{ $post->kategori }}
                </span>
                <span class="text-[11px] text-gray-400">
                  {{ $post->created_at ? $post->created_at->locale('id')->diffForHumans() : '' }}
                </span>
              </div>

              <div>
                <a href="{{ route('public.forum.show', $post->id) }}" class="text-base font-black text-gray-900 dark:text-white hover:text-amber-500 transition">
                  {{ $post->judul }}
                </a>
                <p class="text-xs text-gray-600 dark:text-gray-300 line-clamp-2 leading-relaxed mt-1">
                  {{ $post->deskripsi }}
                </p>
              </div>

              <div class="flex items-center justify-between pt-2">
                <div class="flex items-center gap-1.5 text-xs font-bold text-gray-500 dark:text-gray-400">
                  <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a.75.75 0 01-.84-.84c.14-.84.288-1.745.397-2.618A7.95 7.95 0 013 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z"/></svg>
                  <span>{{ $post->comments_count }} Tanggapan</span>
                </div>

                <a href="{{ route('public.forum.show', $post->id) }}" 
                   class="inline-flex items-center gap-1.5 text-xs font-black text-amber-500 hover:text-amber-600 dark:hover:text-amber-400 transition">
                  <span>Buka Diskusi</span>
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                </a>
              </div>
            </div>
          @endforeach
        </div>
      @else
        <!-- Empty State -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 p-12 text-center space-y-4">
          <div class="w-14 h-14 rounded-2xl bg-amber-50 dark:bg-amber-950/40 text-amber-500 flex items-center justify-center mx-auto">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3.57-3.091c-.48-.008-.962-.02-1.44-.037a48.79 48.79 0 01-4.74-.448m6.75-8.072V6.75A2.25 2.25 0 0013.5 4.5h-9a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25h.75v3l3-3h3"/></svg>
          </div>
          <div class="max-w-md mx-auto space-y-1">
            <h3 class="text-sm font-black text-gray-900 dark:text-white">
              Belum Ada Topik Diskusi
            </h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
              Mulai berbagi cerita, ajukan pertanyaan seputar teknis kamera, atau diskusikan tren fotografi bersama anggota komunitas.
            </p>
          </div>
          <div class="pt-2">
            <a href="{{ route('public.forum.index') }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs rounded-xl shadow-xs transition">
              <span>Buka Forum Diskusi</span>
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            </a>
          </div>
        </div>
      @endif
    </div>

  </div>

  <!-- Lightbox Modal (Rule #97) -->
  <div x-show="lightboxOpen" 
       x-cloak 
       @keydown.escape.window="lightboxOpen = false"
       class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 backdrop-blur-md p-4 sm:p-6"
       tabindex="-1">
    <button type="button" 
            @click="lightboxOpen = false" 
            title="Tutup (Esc)" 
            class="absolute top-4 right-4 sm:top-6 sm:right-6 w-11 h-11 rounded-full bg-white/10 hover:bg-white/25 text-white flex items-center justify-center transition cursor-pointer z-50">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
    <div class="relative max-w-5xl max-h-[90vh] flex flex-col items-center justify-center space-y-3" @click.outside="lightboxOpen = false">
      <img :src="lightboxImg" 
           :alt="lightboxTitle" 
           draggable="false"
           class="max-w-full max-h-[80vh] object-contain rounded-2xl shadow-2xl select-none border border-white/10">
      <div class="px-4 py-1.5 rounded-full bg-black/60 text-white/90 text-xs font-semibold backdrop-blur-sm border border-white/10 flex items-center gap-2">
        <span x-text="lightboxTitle"></span>
        <span class="text-white/40">•</span>
        <span class="text-white/60 text-[11px]">Tekan Esc untuk menutup</span>
      </div>
    </div>
  </div>

</section>
@endsection
