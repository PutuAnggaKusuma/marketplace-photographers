@extends('layouts.app')

@section('title', $post->judul . ' — Forum Komunitas LensMatch')

@section('content')
<section class="py-10 bg-white dark:bg-gray-900/50 min-h-screen">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

    <!-- Breadcrumb Navigation (Matching AnalisAnggaran UI) -->
    <nav class="flex items-center gap-2 text-[11px] font-extrabold uppercase tracking-wider text-gray-400">
      <a href="{{ url('/') }}" class="hover:text-amber-500 transition">Home</a>
      <span>/</span>
      <a href="{{ route('public.forum.index') }}" class="hover:text-amber-500 transition">Forum</a>
      <span>/</span>
      <span class="text-gray-700 dark:text-gray-300 truncate max-w-md">{{ $post->judul }}</span>
    </nav>

    <!-- 2-Column Main Layout Grid (8 Cols Left + 4 Cols Sidebar Right) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

      <!-- Left Main Thread Column (8 Cols) -->
      <div class="lg:col-span-8 space-y-6">
        
        <!-- Main Thread Content Card -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-200 dark:border-gray-700/80 shadow-sm p-6 sm:p-8 space-y-6">
          
          <!-- Meta Header: "Diskusi Forum" Badge on Top, Date & Author Row Below It -->
          <div class="space-y-2.5">
            <div>
              <span class="px-4 py-1.5 rounded-full font-black uppercase tracking-widest text-[10px] bg-gray-900 text-white dark:bg-amber-400 dark:text-gray-900 shadow-xs inline-block">
                Diskusi Forum
              </span>
            </div>

            <div class="flex flex-wrap items-center gap-2.5 text-[11px] text-gray-500 dark:text-gray-400 font-bold">
              <span>{{ strtoupper($post->created_at->format('d F Y H:i:s')) }}</span>
              <span>|</span>
              <div class="flex items-center gap-1.5 font-extrabold text-gray-700 dark:text-gray-300">
                <span>DITULIS OLEH {{ strtoupper($post->user->nama ?? 'ADMIN') }}</span>
              </div>
            </div>
          </div>

          <!-- Title H1 (Uninterrupted Focus for Reader) -->
          <div>
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white leading-tight tracking-tight">
              {{ $post->judul }}
            </h1>
          </div>

          <!-- Main Post Paragraph Content (Sub-Text Deskripsi - Added Proportional Bottom Spacing pb-6 sm:pb-7) -->
          <div class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 leading-relaxed space-y-4 whitespace-pre-line pb-6 sm:pb-7">
            {{ $post->deskripsi }}
          </div>

          <!-- Action & View Counter Row (Positioned After Sub-Text Content) -->
          <div class="pt-6 border-t border-gray-100 dark:border-gray-700/60 flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-3">
              <!-- Sukai Diskusi Button (Standard Social Media Thumbs-Up Like Icon) -->
              <button type="button" class="px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 text-xs font-bold text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition flex items-center gap-2 shadow-xs group">
                <svg class="w-4 h-4 text-amber-500 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M1 21h4V9H1v12zm22-11c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14.17 1 7.58 7.59C7.22 7.95 7 8.45 7 9v10c0 1.1.9 2 2 2h9c.83 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-2z"/>
                </svg>
                <span>Sukai Diskusi</span>
              </button>

              <!-- Bagikan Tautan Button (Positioned After Sub-Text Content) -->
              <button type="button" 
                      @click="navigator.clipboard.writeText(window.location.href); $dispatch('show-toast', { message: 'Tautan forum berhasil disalin!', type: 'success' })" 
                      class="px-4 py-2 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs transition flex items-center gap-2 shadow-xs" 
                      title="Salin Tautan Forum">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                </svg>
                <span>Bagikan Tautan</span>
              </button>
            </div>

            <!-- View Counter -->
            <div class="text-xs font-bold text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
              <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
              <span>Dilihat {{ number_format($post->views ?? 0) }} kali</span>
            </div>
          </div>

        </div>

        <!-- Ethics Warning Banner -->
        <div class="bg-amber-500/10 border border-amber-500/30 p-5 rounded-2xl text-center space-y-1">
          <p class="text-xs font-black text-amber-800 dark:text-amber-300">
            Dimohon untuk tetap mengikuti Aturan Berkomunikasi dalam Forum
          </p>
          <p class="text-[11px] text-amber-700 dark:text-amber-400">
            Gunakan bahasa yang santun, saling menghargai pendapat anggota komunitas, dan hindari konten spam.
          </p>
        </div>

        <!-- Comments Section Main Card Container (Clean Background Process for 2s Delay & 5s Silent Cooldown) -->
        <div x-data="{
          loginModal: false,
          isWaiting: false,
          cooldown: 0,
          timer: null,
          cooldownInterval: null,
          handleGuestClick() {
            if (this.cooldown > 0 || this.isWaiting || this.loginModal) return;
            this.isWaiting = true;
            this.timer = setTimeout(() => {
              this.isWaiting = false;
              this.loginModal = true;
            }, 2000);
          },
          closeModal() {
            this.loginModal = false;
            this.startCooldown();
          },
          startCooldown() {
            this.cooldown = 5;
            if (this.cooldownInterval) clearInterval(this.cooldownInterval);
            this.cooldownInterval = setInterval(() => {
              if (this.cooldown > 1) {
                this.cooldown--;
              } else {
                this.cooldown = 0;
                clearInterval(this.cooldownInterval);
              }
            }, 1000);
          }
        }" class="bg-white dark:bg-gray-800 p-7 sm:p-9 rounded-3xl border border-gray-200/80 dark:border-gray-700/80 shadow-xs space-y-7 relative">
          
          <!-- Header Row: Comments (Count) Title Left + Popular/Newest Filter Pills Right -->
          <div class="flex items-center justify-between flex-wrap gap-4 border-b border-gray-100 dark:border-gray-700/60 pb-5">
            <h3 class="text-xl font-black text-gray-900 dark:text-white flex items-center gap-2">
              <span>Comments</span>
              <span class="text-gray-400 font-extrabold text-base">({{ number_format($post->comments->count()) }})</span>
            </h3>

            <!-- Filter Toggle Pills: Popular vs Newest -->
            <div class="bg-gray-100 dark:bg-gray-700/60 p-1 rounded-2xl flex items-center gap-1 text-xs font-extrabold shadow-inner" x-data="{ tab: 'newest' }">
              <button type="button" 
                      @click="tab = 'popular'" 
                      :class="tab === 'popular' ? 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-xs' : 'text-gray-500 hover:text-gray-800 dark:hover:text-white'" 
                      class="px-3.5 py-1.5 rounded-xl transition flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                <span>Popular</span>
              </button>

              <button type="button" 
                      @click="tab = 'newest'" 
                      :class="tab === 'newest' ? 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-xs' : 'text-gray-500 hover:text-gray-800 dark:hover:text-white'" 
                      class="px-3.5 py-1.5 rounded-xl transition flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <span>Newest</span>
              </button>
            </div>
          </div>

          <!-- Write Comment Box Container -->
          @auth
            <form action="{{ route('public.forum.comment', $post->id) }}" method="POST" class="flex items-start gap-3.5">
              @csrf
              <!-- User Avatar Circle (x-user-avatar: foto or initials) -->
              <x-user-avatar :user="auth()->user()" size="w-10 h-10" bg="bg-amber-400" textColor="text-gray-900" class="mt-1" />

              <!-- Spacious Input Textarea Container Box -->
              <div class="flex-1 relative bg-gray-50/80 dark:bg-gray-900/60 border border-gray-200 dark:border-gray-700/80 rounded-3xl p-4 sm:p-5 transition focus-within:ring-2 focus-within:ring-amber-500 focus-within:bg-white dark:focus-within:bg-gray-900 min-h-[105px]">
                <textarea name="comment" 
                          rows="2" 
                          required 
                          placeholder="Write your comments here..." 
                          class="w-full bg-transparent border-0 outline-none resize-none text-xs sm:text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-0 pr-12 leading-relaxed"></textarea>
                
                <!-- Proportional Yellow Send Circle Button -->
                <button type="submit" 
                        class="absolute right-3.5 bottom-3.5 w-8.5 h-8.5 rounded-full bg-amber-400 hover:bg-amber-500 text-gray-900 flex items-center justify-center shadow-xs hover:scale-105 transition shrink-0" 
                        title="Kirim Balasan">
                  <svg class="w-4 h-4 flex items-center justify-center" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                  </svg>
                </button>
              </div>
            </form>
          @else
            <!-- Guest Comment Form View (100% Original Clean UI, Silent 2s Delay & 5s Cooldown in Background) -->
            <div @click="handleGuestClick()" 
                 class="flex items-start gap-3.5 cursor-pointer group">
              <div class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-400 font-black text-xs flex items-center justify-center shrink-0 shadow-xs mt-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
              </div>

              <div class="flex-1 relative bg-gray-50/80 dark:bg-gray-900/60 border border-gray-200 dark:border-gray-700/80 rounded-3xl p-4 sm:p-5 transition min-h-[105px] flex flex-col justify-between group-hover:border-amber-400">
                <textarea placeholder="Write your comments here..." 
                          @focus="handleGuestClick()"
                          @click="handleGuestClick()"
                          class="w-full bg-transparent border-0 outline-none resize-none text-xs sm:text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-0 pr-12 leading-relaxed cursor-pointer"></textarea>

                <!-- Proportional Yellow Send Circle Button -->
                <button type="button" 
                        @click.stop="handleGuestClick()" 
                        class="absolute right-3.5 bottom-3.5 w-8.5 h-8.5 rounded-full bg-amber-400 hover:bg-amber-500 text-gray-900 flex items-center justify-center shadow-xs group-hover:scale-105 transition shrink-0" 
                        title="Kirim Balasan">
                  <svg class="w-4 h-4 flex items-center justify-center" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                  </svg>
                </button>
              </div>
            </div>
          @endauth

          <!-- Comments Timeline List (Real Dynamic MySQL Data for Likes & Dislikes) -->
          <div class="divide-y divide-gray-100 dark:divide-gray-700/60 pt-2">
            @forelse($post->comments as $index => $comment)
              <div class="py-6 first:pt-0 last:pb-0 space-y-3" 
                   x-data="{ likesCount: {{ $comment->likes ?? 0 }}, dislikesCount: {{ $comment->dislikes ?? 0 }}, liked: false, disliked: false }">
                
                <div class="flex items-start gap-3.5">
                  <!-- Avatar Circle (x-user-avatar: foto or initials) -->
                  <x-user-avatar :user="$comment->user" size="w-10 h-10" class="mt-0.5" />

                  <!-- Comment Main Content Block -->
                  <div class="flex-1 space-y-1.5 min-w-0">
                    <!-- Author Name, Date & Three-Dots Menu -->
                    <div class="flex items-center justify-between">
                      <div class="flex items-center gap-2 flex-wrap">
                        <h4 class="font-extrabold text-xs sm:text-sm text-gray-900 dark:text-white line-clamp-1">{{ $comment->user->nama ?? 'Anggota Komunitas' }}</h4>
                        <span class="text-[11px] font-bold text-gray-400 dark:text-gray-500">{{ $comment->created_at->format('M d, Y') }}</span>
                      </div>
                      <button type="button" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg>
                      </button>
                    </div>

                    <!-- Comment Body Paragraph -->
                    <p class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">
                      {{ $comment->comment }}
                    </p>

                    <!-- Optional Translate Link -->
                    <div>
                      <button type="button" class="text-[11px] font-bold text-gray-400 hover:text-amber-500 transition">
                        Translate
                      </button>
                    </div>

                    <!-- Bottom Action Row: Dynamic Likes (👍 count), Dislikes (👎 count), Reply (💬 Reply) -->
                    <div class="pt-2 flex items-center gap-5 text-xs font-bold text-gray-600 dark:text-gray-300">
                      <!-- Real Dynamic Like Button -->
                      @auth
                        <button type="button" 
                                @click="if(!liked) { 
                                  likesCount++; 
                                  liked = true; 
                                  fetch('/forum/comment/{{ $comment->id }}/like', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }); 
                                }" 
                                :class="liked ? 'text-amber-500 font-black' : 'text-gray-600 dark:text-gray-300 hover:text-amber-500'" 
                                class="flex items-center gap-1.5 transition">
                          <svg class="w-4 h-4" :class="liked ? 'text-amber-500' : 'text-gray-400 hover:text-amber-500'" fill="currentColor" viewBox="0 0 24 24"><path d="M1 21h4V9H1v12zm22-11c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14.17 1 7.58 7.59C7.22 7.95 7 8.45 7 9v10c0 1.1.9 2 2 2h9c.83 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-2z"/></svg>
                          <span x-text="likesCount"></span>
                        </button>

                        <button type="button" 
                                @click="if(!disliked) { 
                                  dislikesCount++; 
                                  disliked = true; 
                                  fetch('/forum/comment/{{ $comment->id }}/dislike', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }); 
                                }" 
                                :class="disliked ? 'text-red-500 font-black' : 'text-gray-600 dark:text-gray-300 hover:text-red-500'" 
                                class="flex items-center gap-1.5 transition">
                          <svg class="w-4 h-4 transform rotate-180" :class="disliked ? 'text-red-500' : 'text-gray-400 hover:text-red-500'" fill="currentColor" viewBox="0 0 24 24"><path d="M1 21h4V9H1v12zm22-11c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14.17 1 7.58 7.59C7.22 7.95 7 8.45 7 9v10c0 1.1.9 2 2 2h9c.83 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-2z"/></svg>
                          <span x-text="dislikesCount"></span>
                        </button>

                        <button type="button" class="flex items-center gap-1.5 hover:text-amber-500 transition">
                          <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                          <span>Reply</span>
                        </button>
                      @else
                        <button type="button" @click="handleGuestClick()" class="flex items-center gap-1.5 hover:text-amber-500 transition">
                          <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M1 21h4V9H1v12zm22-11c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14.17 1 7.58 7.59C7.22 7.95 7 8.45 7 9v10c0 1.1.9 2 2 2h9c.83 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-2z"/></svg>
                          <span x-text="likesCount"></span>
                        </button>

                        <button type="button" @click="handleGuestClick()" class="flex items-center gap-1.5 hover:text-red-500 transition">
                          <svg class="w-4 h-4 text-gray-400 transform rotate-180" fill="currentColor" viewBox="0 0 24 24"><path d="M1 21h4V9H1v12zm22-11c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14.17 1 7.58 7.59C7.22 7.95 7 8.45 7 9v10c0 1.1.9 2 2 2h9c.83 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-2z"/></svg>
                          <span x-text="dislikesCount"></span>
                        </button>

                        <button type="button" @click="handleGuestClick()" class="flex items-center gap-1.5 hover:text-amber-500 transition">
                          <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                          <span>Reply</span>
                        </button>
                      @endauth
                    </div>

                    <!-- See Replies Toggle (Clean Button Styling Without Browser Focus Ring) -->
                    <div class="pt-1">
                      <button type="button" class="text-xs font-black text-amber-600 dark:text-amber-400 hover:underline flex items-center gap-1 focus:outline-none border-0 outline-none">
                        <span>▾ See Replies</span>
                      </button>
                    </div>

                  </div>
                </div>

              </div>
            @empty
              <div class="py-12 text-center text-xs text-gray-400 font-bold">
                Belum ada balasan komentar pada topik ini. Jadilah yang pertama memberikan tanggapan!
              </div>
            @endforelse
          </div>

          <!-- Clean Login Requirement Modal (Clean Action Buttons Only) -->
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
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="closeModal()"></div>
            <div class="flex min-h-full items-center justify-center p-4">
              <div class="relative transform overflow-hidden rounded-3xl bg-white dark:bg-gray-800 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-gray-200 dark:border-gray-700 p-7 space-y-6">
                
                <div class="flex items-start justify-between">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                    </div>
                    <div>
                      <h3 class="text-base font-black text-gray-900 dark:text-white">Login Diperlukan</h3>
                      <p class="text-xs text-gray-500">Akses Diskusi Komunitas LensMatch</p>
                    </div>
                  </div>
                  <button type="button" @click="closeModal()" class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-white text-base font-black">✕</button>
                </div>

                <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed">
                  Untuk dapat menuliskan balasan komentar atau berinteraksi di forum ini, Anda harus masuk ke akun LensMatch terlebih dahulu.
                </p>

                <div class="flex flex-col gap-2.5 pt-2">
                  <a href="{{ route('login', ['redirect' => request()->fullUrl()]) }}" class="w-full py-3 px-4 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs rounded-xl shadow-xs transition text-center">
                    Masuk ke Akun
                  </a>
                  
                </div>

              </div>
            </div>
          </div>

        </div>

      </div>

      <!-- Right Sidebar Column (4 Cols) -->
      <div class="lg:col-span-4 space-y-6">
        
        <!-- Sidebar Widget 1: Kategori Populer -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-4">
          <h3 class="text-xs font-black uppercase tracking-wider text-gray-900 dark:text-white border-l-4 border-amber-500 pl-2">
            Kategori Populer
          </h3>

          <div class="divide-y divide-gray-100 dark:divide-gray-700/60 text-xs">
            @foreach($categoriesList as $catItem)
              <a href="{{ route('public.forum.index', ['cat' => $catItem['slug']]) }}" class="py-3 flex items-center justify-between text-gray-700 dark:text-gray-300 hover:text-amber-500 transition font-bold">
                <span>{{ $catItem['name'] }}</span>
                <span class="text-xs font-black text-gray-400 dark:text-gray-500">
                  {{ $catItem['count'] }}
                </span>
              </a>
            @endforeach
          </div>
        </div>

        <!-- Sidebar Widget 2: Forum Lainnya -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-4">
          <h3 class="text-xs font-black uppercase tracking-wider text-gray-900 dark:text-white border-l-4 border-amber-500 pl-2">
            Forum Lainnya
          </h3>

          <div class="divide-y divide-gray-100 dark:divide-gray-700/60 text-xs space-y-1">
            @foreach($relatedPosts as $relPost)
              <a href="{{ route('public.forum.show', $relPost->id) }}" class="py-3 block space-y-1 group">
                <h4 class="font-extrabold text-xs text-gray-900 dark:text-white group-hover:text-amber-500 transition line-clamp-2 leading-snug">
                  {{ $relPost->judul }}
                </h4>
                <p class="text-[10px] text-gray-400 flex items-center gap-1">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                  <span>{{ $relPost->created_at->format('d F Y H:i') }}</span>
                </p>
              </a>
            @endforeach
          </div>
        </div>

      </div>

    </div>

  </div>
</section>
@endsection