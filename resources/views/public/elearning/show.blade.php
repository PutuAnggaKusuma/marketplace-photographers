@extends('layouts.app')

@section('title', ($currentLesson ? $currentLesson->judul : $course->judul) . ' — LensMatch Learning')

@section('content')
@php
  $currentModule = $currentLesson ? $currentLesson->module : null;
  $moduleLessons = $currentModule ? $currentModule->lessons : collect();
  $moduleLessonIds = $moduleLessons->pluck('id')->toArray();
@endphp

<div 
  x-data="{
    activeLessonId: {{ $currentLesson ? $currentLesson->id : 'null' }},
    isLessonCompleted: {{ ($currentLesson && in_array($currentLesson->id, $completedLessonIds)) ? 'true' : 'false' }},
    showCompleteConfirmModal: false,
    showCelebrationModal: false,
    completedIds: {{ json_encode($completedLessonIds) }},
    moduleLessonIds: {{ json_encode($moduleLessonIds) }},
    moduleTotal: {{ count($moduleLessonIds) }},
    coursePercentage: {{ $userProgressPercentage }},
    loadingToggle: false,
    activeTab: 'deskripsi',
    userNotes: localStorage.getItem('lesson_notes_' + ({{ $currentLesson ? $currentLesson->id : 0 }})) || '',
    videoProgress: {{ ($currentLesson && in_array($currentLesson->id, $completedLessonIds)) ? 100 : 0 }},
    ytPlayer: null,
    ytInterval: null,
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
    },

    init() {
      if (!this.isLessonCompleted && this.activeLessonId) {
        const saved = localStorage.getItem('lesson_progress_' + this.activeLessonId);
        if (saved) {
          this.videoProgress = parseInt(saved, 10) || 0;
        }
      }

      @if($currentLesson && $currentLesson->tipe === 'video' && $currentLesson->youtube_embed_url)
        this.initYoutubePlayer();
      @endif
    },

    currentProgress() {
      return this.isLessonCompleted ? 100 : Math.max(0, Math.min(100, this.videoProgress));
    },

    onMediaTimeUpdate(e) {
      if (this.isLessonCompleted) return;
      const cur = e.target.currentTime;
      const dur = e.target.duration;
      if (dur > 0) {
        const pct = Math.round((cur / dur) * 100);
        if (pct > this.videoProgress) {
          this.videoProgress = Math.min(100, pct);
          localStorage.setItem('lesson_progress_' + this.activeLessonId, this.videoProgress);
        }
        if (pct >= 98 || (dur > 0 && cur >= dur - 0.5)) {
          this.autoCompleteMediaLesson();
        }
      }
    },

    onMediaEnded() {
      this.videoProgress = 100;
      this.autoCompleteMediaLesson();
    },

    initYoutubePlayer() {
      const setup = () => {
        const iframe = document.getElementById('lesson-youtube-player');
        if (!iframe || typeof YT === 'undefined' || !YT.Player) return;
        try {
          this.ytPlayer = new YT.Player('lesson-youtube-player', {
            events: {
              'onStateChange': (event) => {
                if (event.data === 1) { // 1 = YT.PlayerState.PLAYING
                  this.startYtPolling();
                } else if (event.data === 0) { // 0 = YT.PlayerState.ENDED
                  this.stopYtPolling();
                  this.onMediaEnded();
                } else {
                  this.stopYtPolling();
                }
              }
            }
          });
        } catch (e) {
          console.error(e);
        }
      };

      if (window.YT && window.YT.Player) {
        setup();
      } else {
        const oldCallback = window.onYouTubeIframeAPIReady;
        window.onYouTubeIframeAPIReady = () => {
          if (oldCallback) oldCallback();
          setup();
        };
        if (!document.getElementById('yt-iframe-api-script')) {
          const tag = document.createElement('script');
          tag.id = 'yt-iframe-api-script';
          tag.src = 'https://www.youtube.com/iframe_api';
          document.head.appendChild(tag);
        }
      }
    },

    startYtPolling() {
      if (this.ytInterval) clearInterval(this.ytInterval);
      this.ytInterval = setInterval(() => {
        if (this.ytPlayer && typeof this.ytPlayer.getCurrentTime === 'function' && typeof this.ytPlayer.getDuration === 'function') {
          const cur = this.ytPlayer.getCurrentTime();
          const dur = this.ytPlayer.getDuration();
          if (dur > 0) {
            const pct = Math.round((cur / dur) * 100);
            if (pct > this.videoProgress) {
              this.videoProgress = Math.min(100, pct);
              localStorage.setItem('lesson_progress_' + this.activeLessonId, this.videoProgress);
            }
            if (pct >= 98 || (dur > 0 && cur >= dur - 1)) {
              this.autoCompleteMediaLesson();
            }
          }
        }
      }, 500);
    },

    stopYtPolling() {
      if (this.ytInterval) {
        clearInterval(this.ytInterval);
        this.ytInterval = null;
      }
    },

    moduleCompletedCount() {
      return this.completedIds.filter(id => this.moduleLessonIds.includes(id)).length;
    },

    modulePercentage() {
      return this.moduleTotal > 0 ? Math.round((this.moduleCompletedCount() / this.moduleTotal) * 100) : 0;
    },

    saveNotes() {
      if (this.activeLessonId) {
        localStorage.setItem('lesson_notes_' + this.activeLessonId, this.userNotes);
        window.dispatchEvent(new CustomEvent('show-toast', { detail: { message: 'Catatan tersimpan!', type: 'success' } }));
      }
    },

    async completeCurrentLesson() {
      @guest
        this.openLoginModal(
          'Untuk dapat menyimpan progres belajar dan mencatat penyelesaian modul ini, Anda harus masuk ke akun LensMatch terlebih dahulu.',
          '{{ request()->fullUrl() }}'
        );
        return;
      @endguest

      if (this.loadingToggle || !this.activeLessonId || this.isLessonCompleted) return;
      this.loadingToggle = true;

      try {
        const res = await fetch('/elearning/lesson/' + this.activeLessonId + '/toggle', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
          }
        });

        const data = await res.json();
        if (data.success) {
          this.isLessonCompleted = true;
          this.coursePercentage = data.percentage;
          this.videoProgress = 100;
          if (!this.completedIds.includes(this.activeLessonId)) {
            this.completedIds.push(this.activeLessonId);
          }

          if (data.percentage >= 100) {
            this.showCelebrationModal = true;
          }

          window.dispatchEvent(new CustomEvent('show-toast', {
            detail: { message: data.message || 'Materi berhasil diselesaikan!', type: 'success' }
          }));
        } else {
          window.dispatchEvent(new CustomEvent('show-toast', {
            detail: { message: data.message || 'Gagal memperbarui status.', type: 'error' }
          }));
        }
      } catch (err) {
        console.error(err);
      } finally {
        this.loadingToggle = false;
        this.showCompleteConfirmModal = false;
      }
    },

    async autoCompleteMediaLesson() {
      if (this.isLessonCompleted || this.loadingToggle || !this.activeLessonId) return;
      this.videoProgress = 100;
      localStorage.setItem('lesson_progress_' + this.activeLessonId, 100);

      @auth
        await this.completeCurrentLesson();
      @else
        this.isLessonCompleted = true;
        if (!this.completedIds.includes(this.activeLessonId)) {
          this.completedIds.push(this.activeLessonId);
        }
      @endauth
    }
  }" 
  @auto-complete-media.window="autoCompleteMediaLesson()" 
  class="w-full h-full flex flex-col bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100"
>

  <!-- 3-COLUMN WORKSPACE (Left Syllabus + Center Stage + Right Notes) -->
  <div class="flex-1 w-full flex items-stretch overflow-hidden h-full">

    <!-- 1. LEFT SIDEBAR: Silabus Materi Bertahap (Coursera Style Layout) -->
    <aside 
      class="shrink-0 border-r border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 flex flex-col h-full overflow-hidden transition-all duration-500 ease-in-out"
      :class="classroomSidebarOpen ? 'w-72 lg:w-80 opacity-100' : 'w-0 border-r-0 opacity-0 pointer-events-none'"
    >
      <div class="w-72 lg:w-80 flex flex-col h-full overflow-hidden shrink-0">
        <!-- Sidebar Top Header: Course Title & Close/Collapse Button (Coursera style) -->
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 space-y-3 bg-white dark:bg-gray-900 shrink-0">
          <div class="flex items-start justify-between gap-3">
            <div class="space-y-1 min-w-0">
              <p class="text-[10px] font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider">Silabus Kursus</p>
              <h2 class="text-sm font-bold text-gray-900 dark:text-white leading-tight line-clamp-2" title="{{ $course->judul }}">
                {{ $course->judul }}
              </h2>
            </div>
            <button 
              type="button" 
              @click="classroomSidebarOpen = false; $dispatch('sidebar-toggle', { open: false });" 
              class="p-1.5 -mr-1.5 rounded-lg text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition shrink-0 cursor-pointer" 
              title="Tutup Silabus"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Live Course Completion Progress Indicator -->
          <div class="space-y-1.5 pt-1">
            <div class="flex items-center justify-between text-[11px]">
              <span class="text-gray-500 dark:text-gray-400 font-semibold">Progres Belajar</span>
              <span class="font-extrabold text-gray-900 dark:text-white" x-text="coursePercentage + '%'">{{ $userProgressPercentage }}%</span>
            </div>
            <div class="w-full h-1.5 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
              <div class="h-full bg-emerald-500 rounded-full transition-all duration-500" :style="'width: ' + coursePercentage + '%'"></div>
            </div>
          </div>

          <!-- Download Certificate Button (Visible when 100% completed) -->
          <div x-show="coursePercentage >= 100" x-cloak class="pt-1">
            <a 
              href="{{ route('public.elearning.certificate', $course->slug) }}" 
              target="_blank"
              class="w-full px-3 py-2 rounded-xl bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-500 hover:to-amber-600 text-gray-950 font-black text-xs shadow-xs transition flex items-center justify-center gap-2 group"
            >
              <svg class="w-4 h-4 text-gray-950 group-hover:scale-110 transition shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
              </svg>
              <span>Unduh Sertifikat (PDF)</span>
            </a>
          </div>
        </div>

        <!-- Modules Accordion (Coursera style) -->
        <div class="flex-1 overflow-y-auto divide-y divide-gray-100 dark:divide-gray-800/80">
          @forelse($course->modules as $modIndex => $mod)
            <div 
              x-data="{ openModul: true }" 
              class="pb-1"
            >
              <!-- Module Header with Modul Label, Title, & Rotating Caret Chevron -->
              <button 
                type="button" 
                @click="openModul = !openModul" 
                class="w-full text-left px-4 py-3.5 flex items-start justify-between gap-2 select-none hover:bg-gray-50/80 dark:hover:bg-gray-850/60 transition cursor-pointer group"
                title="Buka / Tutup Modul"
              >
                <div class="space-y-0.5 min-w-0 flex-1">
                  <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider block">
                    Modul {{ $modIndex + 1 }}
                  </span>
                  <h3 class="text-xs font-bold text-gray-700 dark:text-gray-300 leading-snug group-hover:text-amber-500 dark:group-hover:text-amber-400 transition">
                    {{ $mod->judul_modul }}
                  </h3>
                </div>
                
                <!-- Rotating Chevron Caret -->
                <div class="p-1 rounded text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300 transition shrink-0 mt-0.5">
                  <svg 
                    class="w-4 h-4 transition-transform duration-200" 
                    :class="openModul ? 'rotate-180' : ''" 
                    fill="none" 
                    stroke="currentColor" 
                    stroke-width="2" 
                    viewBox="0 0 24 24"
                  >
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                  </svg>
                </div>
              </button>

              <!-- List of Lessons under this Module -->
              <div x-show="openModul" x-transition.opacity.duration.150ms class="px-2 space-y-1 pb-2">
                @forelse($mod->lessons as $les)
                  @php
                    $isActive = $currentLesson && $currentLesson->id === $les->id;
                  @endphp
                  <a 
                    href="{{ route('public.elearning.show', [$course->slug, 'lesson' => $les->id]) }}" 
                    class="group px-3 py-2.5 rounded-lg flex items-start gap-3 transition cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-850/70"
                  >
                    <!-- Completed Icon (Solid Green Circle with Checkmark) vs Empty Circle -->
                    <template x-if="completedIds.includes({{ $les->id }})">
                      <div class="w-4 h-4 rounded-full bg-emerald-600 text-white flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">
                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                      </div>
                    </template>
                    <template x-if="!completedIds.includes({{ $les->id }})">
                      <div class="w-4 h-4 rounded-full border {{ $isActive ? 'border-amber-500' : 'border-gray-300 dark:border-gray-600 group-hover:border-amber-500' }} shrink-0 mt-0.5 transition"></div>
                    </template>

                    <!-- Lesson Title & Details (Active Title is Colored Amber) -->
                    <div class="flex-1 min-w-0">
                      <p class="text-xs leading-snug transition {{ $isActive ? 'text-amber-600 dark:text-amber-400 font-bold' : 'text-gray-700 dark:text-gray-300 font-medium group-hover:text-amber-500 dark:group-hover:text-amber-400' }}">
                        {{ $les->judul }}
                      </p>
                      <div class="flex items-center gap-2 mt-1 text-[11px] text-gray-400 dark:text-gray-500">
                        @if($les->tipe === 'video')
                          <span>Video {{ $les->durasi_menit }} mnt</span>
                        @elseif($les->tipe === 'audio')
                          <span>Audio {{ $les->durasi_menit }} mnt</span>
                        @else
                          <span>Materi Teks</span>
                        @endif

                        @if(!$les->is_free && !$isPurchased)
                          <span class="inline-flex items-center gap-0.5 text-amber-600 dark:text-amber-400 font-semibold text-[10px]">
                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                            <span>Berbayar</span>
                          </span>
                        @endif
                      </div>
                    </div>
                  </a>
                @empty
                  <div class="px-4 py-2 text-xs text-gray-400 italic">Belum ada tahapan di modul ini.</div>
                @endforelse
              </div>
            </div>
          @empty
            <!-- Fallback jika belum terbagi modul -->
            <div x-data="{ openModul: true }" class="p-2">
              <div class="space-y-1">
                @foreach($allLessons as $les)
                  @php $isActive = $currentLesson && $currentLesson->id === $les->id; @endphp
                  <a href="{{ route('public.elearning.show', [$course->slug, 'lesson' => $les->id]) }}" class="group px-3 py-2.5 rounded-lg flex items-start gap-3 transition cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-850">
                    <template x-if="completedIds.includes({{ $les->id }})">
                      <div class="w-4 h-4 rounded-full bg-emerald-600 text-white flex items-center justify-center shrink-0 mt-0.5 shadow-2xs" title="Selesai">
                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                      </div>
                    </template>
                    <template x-if="!completedIds.includes({{ $les->id }})">
                      <div class="w-4 h-4 rounded-full border {{ $isActive ? 'border-amber-500' : 'border-gray-300 dark:border-gray-600 group-hover:border-amber-500' }} shrink-0 mt-0.5 transition"></div>
                    </template>
                    <p class="text-xs truncate {{ $isActive ? 'text-amber-600 dark:text-amber-400 font-bold' : 'text-gray-700 dark:text-gray-300 font-medium' }}">{{ $les->judul }}</p>
                  </a>
                @endforeach
              </div>
            </div>
          @endforelse
        </div>
      </div>
    </aside>

    <!-- 2. CENTER STAGE: Area Konten Pembelajaran Utama -->
    <main class="flex-1 min-w-0 overflow-y-auto py-6 relative h-full">

      <!-- Floating Button to Reopen Sidebar if Closed -->
      <button 
        x-show="!classroomSidebarOpen" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-x-4"
        x-transition:enter-end="opacity-100 translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-x-0"
        x-transition:leave-end="opacity-0 -translate-x-4"
        @click="classroomSidebarOpen = true; $dispatch('sidebar-toggle', { open: true });"
        x-cloak
        type="button" 
        class="fixed left-4 bottom-6 z-40 px-3.5 py-2 rounded-xl bg-white dark:bg-gray-850 border border-gray-200 dark:border-gray-700 shadow-lg text-xs font-bold text-gray-800 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-800 flex items-center gap-2 transition cursor-pointer"
        title="Buka Silabus Materi"
      >
        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
        </svg>
        <span>Buka Silabus</span>
      </button>

      <div class="w-full max-w-[1380px] mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

      @if($currentLesson)

        <!-- TOP BAR: BADGE STATUS MATERI & PROGRESS MATERI AKTIF -->
        <div class="w-full flex flex-col sm:flex-row sm:items-center justify-between gap-3">
          <!-- Left: Badge Status Materi & Tipe Konten (Text Only, Rounded-lg, Tanpa Titik Pemisah) -->
          <div class="flex items-center gap-2 flex-wrap">
            @if($currentLesson->tipe === 'video')
              <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200/80 dark:border-amber-800/60">
                Video {{ $currentLesson->durasi_menit }} Menit
              </span>
            @elseif($currentLesson->tipe === 'audio')
              <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200/80 dark:border-amber-800/60">
                Audio {{ $currentLesson->durasi_menit }} Menit
              </span>
            @else
              <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200/80 dark:border-amber-800/60">
                Materi Teks
              </span>
            @endif

            <!-- Badge Gratis / Premium (Warna Netral, Text Only, Rounded-lg) -->
            @if($currentLesson->is_free)
              <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200/80 dark:border-gray-700/80">
                Gratis Preview
              </span>
            @else
              <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200/80 dark:border-gray-700/80">
                Akses Premium
              </span>
            @endif
          </div>

          <!-- Right: Progress Materi Aktif (Bar di Kiri, Teks di Kanan, Real-time Monotonik) -->
          <div class="flex items-center gap-2.5 sm:justify-end shrink-0">
            <div class="w-24 sm:w-28 h-2 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden shrink-0 border border-gray-200/60 dark:border-gray-700/60">
              <div 
                class="h-full bg-amber-500 rounded-full transition-all duration-300" 
                :style="'width: ' + currentProgress() + '%'"
              ></div>
            </div>
            <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 whitespace-nowrap">
              Progress: <strong class="text-amber-600 dark:text-amber-400" x-text="currentProgress() + '% Selesai'"></strong>
            </span>
          </div>
        </div>

        <!-- STEP 1: PEMUTAR MEDIA HANYA UNTUK VIDEO & AUDIO -->
        @if(in_array($currentLesson->tipe, ['video', 'audio']))
          <div class="w-full">
            @if($isCurrentLessonLocked)
              <!-- TAMPILAN TERKUNCI KHUSUS MEDIA -->
              <div class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-xs flex items-center justify-center p-6 sm:p-10" style="height: 380px; max-height: 45vh;">
                <div class="max-w-md mx-auto text-center space-y-3.5">
                  <div class="w-12 h-12 sm:w-14 sm:h-14 mx-auto rounded-2xl bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-400 dark:text-gray-500 flex items-center justify-center shadow-xs">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                  </div>
                  
                  <div class="space-y-1.5">
                    <h3 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white tracking-tight">
                      Tahapan Ini Terkunci
                    </h3>
                    <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed max-w-sm mx-auto">
                      Tahapan <strong>"{{ $currentLesson->judul }}"</strong> memerlukan akses penuh kursus untuk membuka media lengkap.
                    </p>
                  </div>

                  <div class="pt-1 flex items-center justify-center">
                    @auth
                      <a 
                        href="{{ route('public.elearning.checkout', $course->slug) }}" 
                        class="px-6 py-2.5 text-xs font-extrabold text-gray-900 bg-amber-400 hover:bg-amber-500 rounded-xl shadow-xs transition cursor-pointer"
                      >
                        Beli Akses Kursus
                      </a>
                    @else
                      <button 
                        type="button"
                        @click="openLoginModal('Untuk dapat membeli akses materi kursus ini dan mempelajari seluruh modul, Anda harus masuk ke akun LensMatch terlebih dahulu.', '{{ route('public.elearning.checkout', $course->slug) }}')" 
                        class="px-6 py-2.5 text-xs font-extrabold text-gray-900 bg-amber-400 hover:bg-amber-500 rounded-xl shadow-xs transition cursor-pointer"
                      >
                        Beli Akses Kursus
                      </button>
                    @endauth
                  </div>
                </div>
              </div>

            @else
              {{-- 1. FRAME KHUSUS VIDEO (HTML5 MP4 atau YouTube Embed) --}}
              @if($currentLesson->tipe === 'video')
                <div class="w-full bg-black overflow-hidden shadow-xs relative group flex items-center justify-center border border-gray-200 dark:border-gray-800" style="height: 420px; max-height: 48vh;">
                  @if($currentLesson->youtube_embed_url)
                    <iframe 
                      id="lesson-youtube-player"
                      src="{{ $currentLesson->youtube_embed_url }}?autoplay=0&rel=0&modestbranding=1&enablejsapi=1" 
                      title="{{ $currentLesson->judul }}" 
                      class="w-full h-full border-0" 
                      allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                      allowfullscreen
                    ></iframe>
                  @else
                    <video 
                      controls 
                      preload="metadata" 
                      @timeupdate="onMediaTimeUpdate($event)"
                      @ended="onMediaEnded()"
                      class="w-full h-full object-contain bg-black"
                    >
                      <source src="{{ $currentLesson->resolved_media_url ?: 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4' }}" type="video/mp4">
                      Browser Anda tidak mendukung video HTML5.
                    </video>
                  @endif
                </div>

              {{-- 2. TEMPLATE KHUSUS AUDIO (Sesuai Referensi UI Audio Bersih) --}}
              @elseif($currentLesson->tipe === 'audio')
                <div 
                  x-data="{
                    audioEl: null,
                    isPlaying: false,
                    currentTime: 0,
                    duration: 0,
                    isMuted: false,
                    initAudio() {
                      this.$nextTick(() => {
                        this.audioEl = this.$refs.audioPlayerEl;
                        if (this.audioEl) {
                          this.audioEl.addEventListener('loadedmetadata', () => {
                            this.duration = this.audioEl.duration || 0;
                          });
                          this.audioEl.addEventListener('timeupdate', () => {
                            this.currentTime = this.audioEl.currentTime || 0;
                            if (this.duration > 0) {
                              const pct = Math.round((this.currentTime / this.duration) * 100);
                              if (pct > videoProgress) {
                                videoProgress = pct;
                                localStorage.setItem('lesson_progress_' + activeLessonId, videoProgress);
                              }
                              if (pct >= 98 || this.currentTime >= this.duration - 0.5) {
                                $dispatch('auto-complete-media');
                              }
                            }
                          });
                          this.audioEl.addEventListener('ended', () => {
                            this.isPlaying = false;
                            videoProgress = 100;
                            $dispatch('auto-complete-media');
                          });
                        }
                      });
                    },
                    toggleAudioPlay() {
                      if (!this.audioEl) return;
                      if (this.isPlaying) {
                        this.audioEl.pause();
                        this.isPlaying = false;
                      } else {
                        this.audioEl.play();
                        this.isPlaying = true;
                      }
                    },
                    seekAudio(secs) {
                      if (!this.audioEl) return;
                      this.audioEl.currentTime = Math.max(0, Math.min(this.duration || 0, this.audioEl.currentTime + secs));
                    },
                    onScrub(e) {
                      if (!this.audioEl || !this.duration) return;
                      this.audioEl.currentTime = (e.target.value / 100) * this.duration;
                    },
                    toggleAudioMute() {
                      if (!this.audioEl) return;
                      this.isMuted = !this.isMuted;
                      this.audioEl.muted = this.isMuted;
                    },
                    fmtTime(s) {
                      if (!s || isNaN(s)) return '0:00';
                      const min = Math.floor(s / 60);
                      const sec = Math.floor(s % 60);
                      return min + ':' + (sec < 10 ? '0' : '') + sec;
                    }
                  }"
                  x-init="initAudio()"
                  class="w-full bg-white dark:bg-gray-900 py-8 sm:py-12 my-2 flex items-center justify-start"
                >
                  <audio x-ref="audioPlayerEl" preload="metadata" class="hidden">
                    <source src="{{ $currentLesson->resolved_media_url ?: 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3' }}" type="audio/mpeg">
                  </audio>

                  <!-- Card Audio Player (Left Aligned & Pure White) -->
                  <div class="w-full max-w-xl bg-white dark:bg-gray-900 space-y-4 text-left">
                    <div class="space-y-1">
                      <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white tracking-tight">
                        {{ $currentLesson->judul }}
                      </h3>
                      <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 leading-relaxed">
                        {{ $currentLesson->konten ? \Illuminate\Support\Str::limit($currentLesson->konten, 110) : 'Materi rekaman audio panduan pembelajaran dan tips lapangan.' }}
                      </p>
                    </div>

                    <!-- Audio Controller Bar -->
                    <div class="px-4 py-3 rounded-2xl bg-gray-50 dark:bg-gray-800/80 border border-gray-200/80 dark:border-gray-700 flex items-center justify-between gap-3 sm:gap-4">
                      <!-- Play / Pause Button -->
                      <button 
                        type="button" 
                        @click="toggleAudioPlay()" 
                        class="w-10 h-10 rounded-full bg-amber-500 hover:bg-amber-600 text-white flex items-center justify-center shrink-0 shadow-xs transition cursor-pointer" 
                        :title="isPlaying ? 'Jeda' : 'Putar'"
                      >
                        <svg x-show="!isPlaying" class="w-5 h-5 ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                          <path d="M8 5v14l11-7z" />
                        </svg>
                        <svg x-show="isPlaying" x-cloak class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                          <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z" />
                        </svg>
                      </button>

                      <!-- Progress Slider Bar & Duration -->
                      <div class="flex-1 flex items-center gap-2.5 min-w-0">
                        <input 
                          type="range" 
                          min="0" 
                          max="100" 
                          :value="duration > 0 ? (currentTime / duration) * 100 : 0" 
                          @input="onScrub($event)" 
                          class="w-full h-1.5 bg-gray-200 dark:bg-gray-700 rounded-lg appearance-none cursor-pointer accent-amber-500"
                        />
                        <span class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 whitespace-nowrap tabular-nums">
                          <span x-text="fmtTime(currentTime)">0:00</span> / <span x-text="fmtTime(duration)">0:00</span>
                        </span>
                      </div>

                      <!-- Mute / Unmute Button -->
                      <button 
                        type="button" 
                        @click="toggleAudioMute()" 
                        class="p-1.5 text-gray-400 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition cursor-pointer shrink-0" 
                        :title="isMuted ? 'Bunyikan' : 'Bisukan'"
                      >
                        <svg x-show="!isMuted" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 010 12.728M16.463 8.288a5.25 5.25 0 010 7.424M6.75 8.25l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.396C2.806 8.757 3.63 8.25 4.51 8.25H6.75z" />
                        </svg>
                        <svg x-show="isMuted" x-cloak class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 9.75L19.5 12m0 0l2.25 2.25M19.5 12l2.25-2.25M19.5 12l-2.25 2.25m-10.5-3.75l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.396C2.806 8.757 3.63 8.25 4.51 8.25H6.75z" />
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>

              @endif
            @endif
          </div>
        @endif

        <!-- STEP 2: LESSON TITLE -->
        <div class="pt-1 space-y-1">
          <!-- Breadcrumb Navigation -->
          <nav class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 mb-1">
            <a href="{{ route('public.elearning.index') }}" class="hover:text-amber-500 transition">E-Learning</a>
            <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            <span class="hover:text-amber-500 transition truncate max-w-[150px]">{{ $course->judul }}</span>
            @if($currentLesson)
              <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
              <span class="text-gray-900 dark:text-white font-bold truncate max-w-[150px]">{{ $currentLesson->judul }}</span>
            @endif
          </nav>
          <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white tracking-tight leading-snug">
            {{ $currentLesson ? $currentLesson->judul : $course->judul }}
          </h1>
        </div>

        <!-- STEP 3: TAB NAVIGASI MATERI (Pill Style, Bebas Garis Bawah/Kanan) -->
        <div class="pt-2 flex items-center gap-6 text-xs sm:text-sm overflow-x-auto pb-1">
          <button 
            type="button" 
            @click="activeTab = 'deskripsi'"
            class="py-2 transition cursor-pointer shrink-0 flex items-center gap-2"
            :class="activeTab === 'deskripsi' ? 'text-amber-600 dark:text-amber-400 font-black' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-bold'"
          >
            <span :class="activeTab === 'deskripsi' ? 'underline underline-offset-8 decoration-2 decoration-amber-500 dark:decoration-amber-400' : ''">Deskripsi</span>
          </button>
          
          <button 
            type="button" 
            @click="activeTab = 'lampiran'"
            class="py-2 transition cursor-pointer shrink-0 flex items-center gap-2"
            :class="activeTab === 'lampiran' ? 'text-amber-600 dark:text-amber-400 font-black' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-bold'"
          >
            <span :class="activeTab === 'lampiran' ? 'underline underline-offset-8 decoration-2 decoration-amber-500 dark:decoration-amber-400' : ''">Lampiran</span>
          </button>

          <button 
            type="button" 
            @click="activeTab = 'catatan'"
            class="py-2 transition cursor-pointer shrink-0 flex items-center gap-2"
            :class="activeTab === 'catatan' ? 'text-amber-600 dark:text-amber-400 font-black' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-bold'"
          >
            <span :class="activeTab === 'catatan' ? 'underline underline-offset-8 decoration-2 decoration-amber-500 dark:decoration-amber-400' : ''">Catatan</span>
          </button>
        </div>

        <!-- STEP 4: ISI KONTEN TAB -->
        <div class="pt-3">

          <!-- TAB 1: DESKRIPSI -->
          <div x-show="activeTab === 'deskripsi'" class="space-y-4">
            @if($isCurrentLessonLocked)
              <div class="text-sm sm:text-base text-gray-700 dark:text-gray-300 leading-relaxed font-normal">
                <p class="leading-relaxed">
                  {{ \Illuminate\Support\Str::limit($currentLesson->konten ?: $course->konten, 180) }}
                </p>
              </div>

              <!-- Locked Teaser Banner -->
              <div class="p-8 sm:p-10 text-center space-y-2 border border-dashed border-gray-200 dark:border-gray-800 rounded-xl bg-gray-50/50 dark:bg-gray-850/40">
                <svg class="w-7 h-7 mx-auto text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                </svg>
                <h4 class="text-sm font-bold text-gray-900 dark:text-white">Deskripsi Terkunci</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400 max-w-sm mx-auto">
                  Beli akses kursus untuk membaca deskripsi lengkap materi ini.
                </p>
              </div>
            @else
              <div class="text-sm sm:text-base text-gray-800 dark:text-gray-200 leading-relaxed font-normal space-y-4">
                @if(strip_tags($currentLesson->konten) != $currentLesson->konten)
                  {!! $currentLesson->konten !!}
                @else
                  <div class="whitespace-pre-line leading-relaxed">
                    {{ $currentLesson->konten ?: $course->konten }}
                  </div>
                @endif
              </div>
            @endif
          </div>

          <!-- TAB 2: LAMPIRAN -->
          <div x-show="activeTab === 'lampiran'" x-cloak class="space-y-4">
            @if($isCurrentLessonLocked)
              <div class="p-8 sm:p-10 text-center space-y-2 border border-dashed border-gray-200 dark:border-gray-800 rounded-xl bg-gray-50/50 dark:bg-gray-850/40">
                <svg class="w-7 h-7 mx-auto text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                </svg>
                <h4 class="text-sm font-bold text-gray-900 dark:text-white">Lampiran Terkunci</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400 max-w-sm mx-auto">
                  Beli akses kursus untuk mengunduh seluruh file lampiran materi ini.
                </p>
              </div>
            @else
              @php
                $lampiranList = $currentLesson ? $currentLesson->lampiran_list : [];
              @endphp

              @if(count($lampiranList) > 0)
                <p class="text-sm text-gray-600 dark:text-gray-400">Unduh lampiran:</p>
                
                <div class="space-y-4 pt-1">
                  @foreach($lampiranList as $file)
                    <div class="flex items-start gap-3.5">
                      <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                      <div class="min-w-0">
                        <div class="flex items-center flex-wrap gap-y-1">
                          <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $file['name'] }}</p>
                          <button 
                            type="button" 
                            @click="window.dispatchEvent(new CustomEvent('show-toast', { detail: { message: '{{ $file['action_message'] }}', type: 'success' } }))" 
                            class="ml-5 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:text-amber-600 dark:hover:text-amber-400 underline transition shrink-0 cursor-pointer inline-flex items-center gap-1.5"
                            style="margin-left: 24px;"
                          >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                            <span>Unduh</span>
                          </button>
                        </div>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $file['type'] }} • {{ $file['size'] }}</p>
                      </div>
                    </div>
                  @endforeach
                </div>
              @else
                <div class="py-10 text-center space-y-2 border border-dashed border-gray-200 dark:border-gray-800 rounded-xl bg-gray-50/30 dark:bg-gray-850/20">
                  <svg class="w-7 h-7 mx-auto text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                  </svg>
                  <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Tidak Ada Lampiran</h4>
                  <p class="text-xs text-gray-500 dark:text-gray-400 max-w-sm mx-auto">
                    Belum ada file lampiran atau dokumen pendukung yang disertakan pada sesi materi ini.
                  </p>
                </div>
              @endif
            @endif
          </div>

          <!-- TAB 3: CATATAN -->
          <div x-show="activeTab === 'catatan'" x-cloak class="space-y-3">
            @if($isCurrentLessonLocked)
              <div class="p-8 sm:p-10 text-center space-y-2 border border-dashed border-gray-200 dark:border-gray-800 rounded-xl bg-gray-50/50 dark:bg-gray-850/40">
                <svg class="w-7 h-7 mx-auto text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                </svg>
                <h4 class="text-sm font-bold text-gray-900 dark:text-white">Catatan Terkunci</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400 max-w-sm mx-auto">
                  Beli akses kursus untuk menggunakan fitur catatan materi ini.
                </p>
              </div>
            @else
              <p class="text-sm text-gray-600 dark:text-gray-400">
                Tulis catatan pribadi atau rangkuman poin penting yang Anda pelajari pada sesi materi ini:
              </p>

              <textarea 
                x-model="userNotes" 
                @input.debounce.500ms="saveNotes()"
                rows="12" 
                class="w-full min-h-[260px] text-sm sm:text-base p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-amber-500 focus:outline-none resize-y leading-relaxed"
                placeholder="Ketik catatan pribadi atau poin penting yang Anda pelajari di sini..."
              ></textarea>
            @endif
          </div>

        </div>

        <!-- COURSERA HIERARCHY STEP 4: BOTTOM NAVIGATION BAR (Sebelumnya, Tandai Selesai, Selanjutnya) -->
        <div class="pt-6 pb-8 flex items-center justify-between gap-4">
          @if($prevLesson)
            <a href="{{ route('public.elearning.show', [$course->slug, 'lesson' => $prevLesson->id]) }}" class="py-2 pr-4 text-xs font-bold hover:text-amber-500 dark:hover:text-amber-400 transition inline-flex items-center gap-1.5 text-gray-700 dark:text-gray-300 cursor-pointer">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
              <span>Sebelumnya</span>
            </a>
          @else
            <div></div>
          @endif

          <div class="flex items-center gap-3">
            @php
              $isMediaLesson = in_array($currentLesson->tipe, ['video', 'audio']);
            @endphp

            @if(!$isCurrentLessonLocked && !$isMediaLesson)
              <!-- Tombol Tandai Selesai (Hanya untuk materi teks dan jika belum selesai) -->
              <button 
                x-show="!isLessonCompleted" 
                x-cloak
                type="button" 
                @click="showCompleteConfirmModal = true" 
                class="px-5 py-2.5 rounded-lg font-bold text-xs bg-emerald-600 hover:bg-emerald-700 text-white transition inline-flex items-center gap-2 cursor-pointer shadow-xs"
              >
                <span>Tandai Selesai</span>
              </button>
            @endif

            @if($nextLesson)
              <a 
                x-show="isLessonCompleted" 
                x-cloak
                x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="opacity-0 translate-x-2"
                x-transition:enter-end="opacity-100 translate-x-0"
                href="{{ route('public.elearning.show', [$course->slug, 'lesson' => $nextLesson->id]) }}" 
                class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-xs font-bold transition inline-flex items-center gap-1.5 shadow-xs"
              >
                <span>Selanjutnya</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
              </a>
            @endif
          </div>
        </div>

      @endif

      </div>
    </main>
  </div>

  @if($currentLesson && !in_array($currentLesson->tipe, ['video', 'audio']))
    <!-- MODAL VALIDASI KONFIRMASI TANDAI SELESAI -->
    <div 
      x-show="showCompleteConfirmModal" 
      x-cloak
      class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
      style="display: none;"
    >
      <!-- Backdrop Blur -->
      <div 
        x-show="showCompleteConfirmModal"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="showCompleteConfirmModal = false"
        class="fixed inset-0 bg-black/60 backdrop-blur-xs transition-opacity"
      ></div>

      <!-- Dialog Card Persegi Simetris -->
      <div 
        x-show="showCompleteConfirmModal"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        @click.stop
        class="relative w-full max-w-[400px] bg-white dark:bg-gray-850 rounded-2xl p-6 sm:p-7 shadow-2xl border border-gray-100 dark:border-gray-800 z-10 space-y-4 text-left"
      >
        <!-- Header (Rule #59 Compliant: Centered without Icon, NO X button) -->
        <div class="text-center space-y-1">
          <h3 class="text-base font-black text-gray-900 dark:text-white tracking-tight text-center">
            Selesaikan Materi Ini?
          </h3>
        </div>

        <!-- Content Message -->
        <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
          Apakah Anda yakin telah selesai mempelajari materi <strong>"{{ $currentLesson->judul }}"</strong>? Setelah ditandai selesai, status ini tidak dapat dibatalkan.
        </p>

        <!-- Action Buttons (Rule #59 Compliant: Centered Buttons) -->
        <div class="flex items-center justify-center gap-3 pt-2">
          <button 
            type="button" 
            @click="showCompleteConfirmModal = false"
            class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-xs font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 transition cursor-pointer"
          >
            Batal
          </button>
          <button 
            type="button" 
            @click="completeCurrentLesson()"
            :disabled="loadingToggle"
            class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white rounded-xl text-xs font-extrabold transition inline-flex items-center gap-1.5 shadow-xs cursor-pointer"
          >
            <svg x-show="loadingToggle" class="animate-spin -ml-1 mr-1 h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <span>Ya, Tandai Selesai</span>
          </button>
        </div>
      </div>
    </div>
  @endif

  <!-- Celebration Graduation Modal (Popup when reaching 100% Completion) -->
  <div 
    x-show="showCelebrationModal" 
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    role="dialog" 
    aria-modal="true"
  >
    <!-- Dark Backdrop -->
    <div 
      x-show="showCelebrationModal"
      x-transition:enter="transition ease-out duration-200"
      x-transition:enter-start="opacity-0"
      x-transition:enter-end="opacity-100"
      x-transition:leave="transition ease-in duration-150"
      x-transition:leave-start="opacity-100"
      x-transition:leave-end="opacity-0"
      @click="showCelebrationModal = false"
      class="fixed inset-0 bg-black/70 backdrop-blur-sm transition-opacity"
    ></div>

    <!-- Celebration Card -->
    <div 
      x-show="showCelebrationModal"
      x-transition:enter="transition ease-out duration-300"
      x-transition:enter-start="opacity-0 scale-90 translate-y-4"
      x-transition:enter-end="opacity-100 scale-100 translate-y-0"
      x-transition:leave="transition ease-in duration-150"
      x-transition:leave-start="opacity-100 scale-100 translate-y-0"
      x-transition:leave-end="opacity-0 scale-90 translate-y-4"
      @click.stop
      class="relative w-full max-w-md bg-white dark:bg-gray-850 rounded-3xl p-6 sm:p-8 shadow-2xl border border-amber-300 dark:border-amber-700/60 z-10 text-center space-y-5 overflow-hidden"
    >
      <!-- Background Ambient Glow -->
      <div class="absolute -top-12 -left-12 w-32 h-32 bg-amber-400/20 rounded-full blur-2xl pointer-events-none"></div>
      <div class="absolute -bottom-12 -right-12 w-32 h-32 bg-emerald-400/20 rounded-full blur-2xl pointer-events-none"></div>

      <!-- Icon & Medal Animation -->
      <div class="relative w-20 h-20 mx-auto rounded-full bg-gradient-to-tr from-amber-500 via-amber-400 to-yellow-200 flex items-center justify-center shadow-lg shadow-amber-500/30">
        <svg class="w-10 h-10 text-amber-950" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
        </svg>
      </div>

      <!-- Title & Description -->
      <div class="space-y-1.5">
        <span class="text-[10px] font-black uppercase tracking-widest text-amber-600 dark:text-amber-400">Selamat & Sukses!</span>
        <h3 class="text-xl font-black text-gray-900 dark:text-white tracking-tight">
          Anda Telah Menyelesaikan Kelas
        </h3>
        <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed max-w-sm mx-auto">
          Seluruh kurikulum pada <strong>"{{ $course->judul }}"</strong> telah tuntas 100%. Sertifikat kelulusan resmi Anda telah diterbitkan!
        </p>
      </div>

      <!-- Action Buttons -->
      <div class="space-y-2.5 pt-2">
        <a 
          href="{{ route('public.elearning.certificate', $course->slug) }}" 
          target="_blank"
          class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-500 hover:to-amber-600 text-gray-950 font-black text-xs shadow-md transition flex items-center justify-center gap-2 group"
        >
          <svg class="w-4 h-4 text-gray-950 group-hover:scale-110 transition" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd"></path>
          </svg>
          <span>Unduh Sertifikat Kelulusan (PDF)</span>
        </a>

        <button 
          type="button" 
          @click="showCelebrationModal = false"
          class="w-full py-2.5 px-4 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 font-bold text-xs transition"
        >
          Nanti Saja
        </button>
      </div>
    </div>
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
@endsection