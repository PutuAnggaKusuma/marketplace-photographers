<!doctype html>
<html lang="en">
  <head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Fotografer Dashboard — Marketplace Fotografer')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo/lensmatch_logo_transparent_yellow.png') }}" />
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        // Global Vector SVG Image Fallback Handler
        window.SVG_IMAGE_FALLBACK = "data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='800' height='600' viewBox='0 0 800 600' fill='none'%3E%3Crect width='800' height='600' fill='%23F3F4F6'/%3E%3Cpath d='M360 260C360 248.954 368.954 240 380 240H420C431.046 240 440 248.954 440 260V265H450C466.569 265 480 278.431 480 295V345C480 361.569 466.569 375 450 375H350C333.431 375 320 361.569 320 345V295C320 278.431 333.431 265 350 265H360V260Z' stroke='%239CA3AF' stroke-width='12' stroke-linecap='round' stroke-linejoin='round'/%3E%3Ccircle cx='400' cy='320' r='30' stroke='%239CA3AF' stroke-width='12'/%3E%3Cline x1='310' y1='240' x2='490' y2='390' stroke='%23EF4444' stroke-width='10' stroke-linecap='round'/%3E%3Ctext x='400' y='450' font-family='sans-serif' font-size='22' font-weight='700' fill='%236B7280' text-anchor='middle'%3EGambar Tidak Dapat Dimuat%3C/text%3E%3C/svg%3E";
        document.addEventListener('error', function (e) {
            if (e.target && e.target.tagName && e.target.tagName.toLowerCase() === 'img') {
                if (e.target.src !== window.SVG_IMAGE_FALLBACK) {
                    e.target.onerror = null;
                    e.target.src = window.SVG_IMAGE_FALLBACK;
                }
            }
        }, true);
    </script>
      <script>
      document.addEventListener('alpine:init', () => {
        Alpine.store('chatNotifier', {
          unreadCount: 0,
          _pollTimer: null,
          _abortController: null,
          _isUnloading: false,
          init() {
            this.fetchUnread();
            this._pollTimer = setInterval(() => {
              this.fetchUnread();
            }, 3000);

            const stopPolling = () => {
              this._isUnloading = true;
              if (this._pollTimer) {
                clearInterval(this._pollTimer);
                this._pollTimer = null;
              }
              if (this._abortController) {
                this._abortController.abort();
                this._abortController = null;
              }
            };
            window.addEventListener('beforeunload', stopPolling);
            window.addEventListener('pagehide', stopPolling);
          },
          async fetchUnread() {
            if (this._isUnloading) return;
            try {
              if (this._abortController) {
                this._abortController.abort();
              }
              this._abortController = new AbortController();
              const res = await fetch('{{ route('chat.unread-count') }}', {
                headers: {
                  'X-Requested-With': 'XMLHttpRequest',
                  'Accept': 'application/json'
                },
                signal: this._abortController.signal
              });
              if (res.ok) {
                const data = await res.json();
                this.unreadCount = data.unread_count || 0;
              }
            } catch (err) {
              // silent
            }
          }
        });
      });
    </script>
  </head>
  <body
    x-data="{ page: 'photographer', loaded: true, darkMode: false, stickyMenu: false, sidebarToggle: false }"
    x-init="
      darkMode = JSON.parse(localStorage.getItem('darkMode'));
      $watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)))
    "
    :class="{'dark bg-gray-900': darkMode === true}"
  >
    @include('partials.shared.preloader')

    <div class="flex h-screen overflow-hidden">
      @include('partials.photographer.sidebar')

    <!-- Mobile Backdrop Overlay with Smooth Slide & Fade Transition -->
    <div x-show="sidebarToggle" 
         @click="sidebarToggle = false" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-40 bg-black/50 backdrop-blur-xs lg:hidden" 
         style="display: none;">
    </div>

      @php
        $layoutPhotographer = null;
        if (auth()->check() && auth()->user()->role === 'photographer') {
            $layoutPhotographer = auth()->user()->rolePhotographer
                ?? \App\Models\RolePhotographer::where('id_user', auth()->id())->first();
        }
      @endphp

      <div class="relative flex flex-col flex-1 {{ request()->routeIs('photographer.chat*') ? 'h-screen overflow-hidden' : 'overflow-x-hidden overflow-y-auto' }} bg-gray-50/50 dark:bg-gray-900/50">
        @include('partials.shared.header')

        <main class="{{ request()->routeIs('photographer.chat*') ? 'flex-1 flex flex-col min-h-0 overflow-hidden' : '' }}">
          <div class="{{ request()->routeIs('photographer.chat*') ? 'w-full h-full min-h-0 flex-1 flex flex-col p-0 overflow-hidden' : 'p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6 space-y-6' }}">
            @if(!request()->routeIs('photographer.chat*')
                && $layoutPhotographer
                && !$layoutPhotographer->isPublishedToCatalog()
                && (!request()->routeIs('photographer.dashboard') || !$layoutPhotographer->isFeatureUnlocked()))
              @include('partials.photographer.stage-stepper', ['photographer' => $layoutPhotographer])
            @endif
            @yield('content')
          </div>
        </main>
      </div>
    </div>

    <!-- Floating Chat Action Button (FAB) - Only shown outside chat module -->
    @if(!request()->routeIs('photographer.chat*'))
      @php
        $unreadChatCount = 0;
        if ($layoutPhotographer) {
            $unreadChatCount = \App\Models\ChatMessage::whereHas('chatBooking', function($q) use ($layoutPhotographer) {
                $q->where('id_photographer', $layoutPhotographer->id);
            })->where('id_sender', '!=', auth()->id())->where('is_read', false)->count();
        }
      @endphp

      <div class="fixed bottom-8 right-8 sm:right-10 md:right-12 z-40">
        <a href="{{ route('photographer.chat') }}" 
           title="Buka Pesan & Chat Klien"
           class="relative w-14 h-14 rounded-full bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-white shadow-xl hover:shadow-2xl flex items-center justify-center transition-all duration-300 transform hover:scale-110 active:scale-95 group focus:outline-none focus:ring-4 focus:ring-amber-300">
           
           <!-- Chat Icon -->
           <svg class="w-7 h-7 text-white transition-transform group-hover:rotate-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
             <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a.75.75 0 01-.818-.847l.487-2.923C3.766 15.748 3 13.974 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" />
           </svg>

           <!-- Real-Time Unread Badge Indicator -->
           <span x-show="$store.chatNotifier && $store.chatNotifier.unreadCount > 0"
                 x-text="$store.chatNotifier.unreadCount > 99 ? '99+' : $store.chatNotifier.unreadCount"
                 class="absolute -top-1 -right-1 min-w-[22px] h-[22px] px-1.5 rounded-full bg-rose-500 text-white text-[11px] font-black flex items-center justify-center border-2 border-white dark:border-gray-900 shadow-md animate-pulse"
                 style="display: none;">
           </span>

           <!-- Tooltip Label on Hover -->
           <span class="absolute right-16 top-1/2 -translate-y-1/2 px-3 py-1.5 rounded-xl bg-gray-900 dark:bg-gray-800 text-white text-xs font-bold whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity duration-200 shadow-lg border border-gray-700">
             Pesan & Chat Klien
             @if($unreadChatCount > 0)
               <span class="text-amber-400 font-extrabold ml-1">({{ $unreadChatCount }})</span>
             @endif
           </span>
        </a>
      </div>
    @endif

    @include('partials.shared.file-upload-validator-js')
    @include('partials.shared.confirm-modal')
    @include('partials.shared.toast-notification')
    @include('partials.shared.dashboard-live-filter-js')
    @include('partials.shared.ajax-pagination-engine')
  </body>
</html>