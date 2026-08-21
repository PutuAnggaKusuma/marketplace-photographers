<header x-data="{menuToggle: false}"
  class="sticky top-0 z-50 flex w-full border-gray-200 bg-white lg:border-b dark:border-gray-800 dark:bg-gray-900">
  <div class="flex grow flex-col items-center justify-between lg:flex-row lg:px-6">
    <div
      class="flex w-full items-center justify-between gap-2 border-b border-gray-200 px-3 py-3 sm:gap-4 lg:justify-normal lg:border-b-0 lg:px-0 lg:py-4 dark:border-gray-800">
      <!-- Hamburger Toggle BTN -->
      <button :class="sidebarToggle ? 'lg:bg-transparent dark:lg:bg-transparent bg-gray-100 dark:bg-gray-800' : ''"
        class="z-50 flex h-10 w-10 items-center justify-center rounded-lg border-gray-200 text-gray-500 lg:h-11 lg:w-11 lg:border dark:border-gray-800 dark:text-gray-400"
        @click.stop="sidebarToggle = !sidebarToggle">
        <svg class="hidden fill-current lg:block" width="16" height="12" viewBox="0 0 16 12" fill="none"
          xmlns="http://www.w3.org/2000/svg">
          <path fill-rule="evenodd" clip-rule="evenodd"
            d="M0.583252 1C0.583252 0.585788 0.919038 0.25 1.33325 0.25H14.6666C15.0808 0.25 15.4166 0.585786 15.4166 1C15.4166 1.41421 15.0808 1.75 14.6666 1.75L1.33325 1.75C0.919038 1.75 0.583252 1.41422 0.583252 1ZM0.583252 11C0.583252 10.5858 0.919038 10.25 1.33325 10.25L14.6666 10.25C15.0808 10.25 15.4166 11.75 15.4166 11C15.4166 11.4142 15.0808 11.75 14.6666 11.75L1.33325 11.75C0.919038 11.75 0.583252 11.4142 0.583252 11ZM1.33325 5.25C0.919038 5.25 0.583252 5.58579 0.583252 6C0.583252 6.41421 0.919038 6.75 1.33325 6.75L7.99992 6.75C8.41413 6.75 8.74992 6.41421 8.74992 6C8.74992 5.58579 8.41413 5.25 7.99992 5.25L1.33325 5.25Z"
            fill="" />
        </svg>

        <svg :class="sidebarToggle ? 'hidden' : 'block lg:hidden'" class="fill-current lg:hidden" width="24" height="24"
          viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path fill-rule="evenodd" clip-rule="evenodd"
            d="M3.25 6C3.25 5.58579 3.58579 5.25 4 5.25L20 5.25C20.4142 5.25 20.75 5.58579 20.75 6C20.75 6.41421 20.4142 6.75 20 6.75L4 6.75C3.58579 6.75 3.25 6.41422 3.25 6ZM3.25 18C3.25 17.5858 3.58579 17.25 4 17.25L20 17.25C20.4142 17.25 20.75 17.5858 20.75 18C20.75 18.4142 20.4142 18.75 20 18.75L4 18.75C3.58579 18.75 3.25 18.4142 3.25 18ZM4 11.25C3.58579 11.25 3.25 11.5858 3.25 12C3.25 12.4142 3.58579 12.75 4 12.75L12 12.75C12.4142 12.75 12.75 12.4142 12.75 12C12.75 11.5858 12.4142 11.25 12 11.25L4 11.25Z"
            fill="" />
        </svg>

        <svg :class="sidebarToggle ? 'block lg:hidden' : 'hidden'" class="fill-current" width="24" height="24"
          viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path fill-rule="evenodd" clip-rule="evenodd"
            d="M6.21967 7.28131C5.92678 6.98841 5.92678 6.51354 6.21967 6.22065C6.51256 5.92775 6.98744 5.92775 7.28033 6.22065L11.999 10.9393L16.7176 6.22078C17.0105 5.92789 17.4854 5.92788 17.7782 6.22078C18.0711 6.51367 18.0711 6.98855 17.7782 7.28144L13.0597 12L17.7782 16.7186C18.0711 17.0115 18.0711 17.4863 17.7782 17.7792C17.4854 18.0721 17.0105 18.0721 16.7176 17.7792L11.999 13.0607L7.28033 17.7794C6.98744 18.0722 6.51256 18.0722 6.21967 17.7794C5.92678 17.4865 5.92678 17.0116 6.21967 16.7187L10.9384 12L6.21967 7.28131Z"
            fill="" />
        </svg>
      </button>

      <a href="/" class="lg:hidden">
        <span class="text-xl font-bold text-amber-500">LensMatch</span>
      </a>

      <div class="hidden lg:block">
        <form>
          <div class="relative">
            <span class="absolute top-1/2 left-4 -translate-y-1/2">
              <svg class="fill-gray-500 dark:fill-gray-400" width="20" height="20" viewBox="0 0 20 20" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd"
                  d="M3.04175 9.37363C3.04175 5.87693 5.87711 3.04199 9.37508 3.04199C12.8731 3.04199 15.7084 5.87693 15.7084 9.37363C15.7084 12.8703 12.8731 15.7053 9.37508 15.7053C5.87711 15.7053 3.04175 12.8703 3.04175 9.37363ZM9.37508 1.54199C5.04902 1.54199 1.54175 5.04817 1.54175 9.37363C1.54175 13.6991 5.04902 17.2053 9.37508 17.2053C11.2674 17.2053 13.003 16.5344 14.357 15.4176L17.177 18.238C17.4699 18.5309 17.9448 18.5309 18.2377 18.238C18.5306 17.9451 18.5306 17.4703 18.2377 17.1774L15.418 14.3573C16.5365 13.0033 17.2084 11.2669 17.2084 9.37363C17.2084 5.04817 13.7011 1.54199 9.37508 1.54199Z"
                  fill="" />
              </svg>
            </span>
            <input type="text" placeholder="Cari layanan, fotografer, booking..." id="search-input"
              class="dark:bg-dark-900 shadow-theme-xs focus:border-amber-300 focus:ring-amber-500/10 dark:focus:border-amber-800 h-11 w-full rounded-lg border border-gray-200 bg-transparent py-2.5 pr-14 pl-12 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden xl:w-[430px] dark:border-gray-800 dark:bg-gray-900 dark:bg-white/[0.03] dark:text-white/90 dark:placeholder:text-white/30" />
          </div>
        </form>
      </div>
    </div>

    <div :class="menuToggle ? 'flex' : 'hidden'"
      class="shadow-theme-md w-full items-center justify-between gap-4 px-5 py-4 lg:flex lg:justify-end lg:px-0 lg:shadow-none">
      <div class="2xsm:gap-3 flex items-center gap-2">
        <!-- Dynamic Notification Bell Dropdown -->
        @auth
          <div class="relative" x-data="headerNotifApp()" x-init="init()">
            <button @click="notifOpen = !notifOpen" 
                    title="Notifikasi Saya"
                    class="relative flex h-10 w-10 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-600 transition-colors hover:bg-amber-100 hover:text-amber-800 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-amber-400 focus:outline-none">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
              </svg>
              <!-- Unread Red Pulse Badge -->
              <span x-show="unreadCount > 0" 
                    x-cloak 
                    class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[9px] font-black text-white ring-2 ring-white dark:ring-gray-900 animate-pulse">
                <span x-text="unreadCount > 9 ? '9+' : unreadCount"></span>
              </span>
            </button>

            <!-- Notifications Dropdown List -->
            <div x-show="notifOpen" @click.away="notifOpen = false" x-cloak x-transition class="absolute right-0 mt-3 w-80 sm:w-96 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700/80 py-2 z-50 overflow-hidden text-left">
              <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between bg-gray-50/70 dark:bg-gray-900/60">
                <div class="flex items-center gap-2">
                  <h3 class="text-sm font-black text-gray-900 dark:text-white">Notifikasi Saya</h3>
                  <span x-show="unreadCount > 0" class="px-2 py-0.5 rounded-full bg-amber-100 dark:bg-amber-900/60 text-amber-700 dark:text-amber-300 font-extrabold text-[10px]" x-text="unreadCount + ' Baru'"></span>
                </div>
                <button type="button" @click="markAllRead()" class="text-[11px] font-bold text-amber-600 dark:text-amber-400 hover:underline">Tandai dibaca</button>
              </div>

              <div class="max-h-80 overflow-y-auto divide-y divide-gray-100 dark:divide-gray-700/60">
                <template x-for="notif in notifications" :key="notif.id">
                  <div @click="openNotif(notif)" 
                       class="px-5 py-3.5 transition cursor-pointer flex items-start gap-3"
                       :class="!notif.is_read ? 'bg-amber-50/40 dark:bg-amber-950/20 hover:bg-amber-50/80 dark:hover:bg-amber-950/40' : 'hover:bg-gray-50 dark:hover:bg-gray-700/40'">
                    
                    <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 mt-0.5"
                         :class="{
                            'bg-amber-100 text-amber-600 dark:bg-amber-900/60 dark:text-amber-300': notif.type === 'booking',
                            'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/60 dark:text-emerald-300': notif.type === 'payout' || notif.type === 'payment',
                            'bg-blue-100 text-blue-600 dark:bg-blue-900/60 dark:text-blue-300': notif.type === 'gallery',
                            'bg-purple-100 text-purple-600 dark:bg-purple-900/60 dark:text-purple-300': notif.type === 'chat'
                         }">
                      <template x-if="notif.type === 'booking'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                      </template>
                      <template x-if="notif.type === 'payout' || notif.type === 'payment'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                      </template>
                      <template x-if="notif.type === 'gallery'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                      </template>
                      <template x-if="notif.type !== 'booking' && notif.type !== 'payout' && notif.type !== 'payment' && notif.type !== 'gallery'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                      </template>
                    </div>

                    <div class="min-w-0 flex-1 space-y-0.5">
                      <div class="flex items-center justify-between">
                        <p class="text-xs font-bold text-gray-900 dark:text-white truncate" x-text="notif.title"></p>
                        <span x-show="!notif.is_read" class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span>
                      </div>
                      <p class="text-xs text-gray-600 dark:text-gray-300 line-clamp-2" x-text="notif.message"></p>
                      <p class="text-[10px] text-gray-400 font-medium" x-text="notif.created_at"></p>
                    </div>
                  </div>
                </template>

                <template x-if="notifications.length === 0">
                  <div class="px-5 py-8 text-center text-gray-400 space-y-1">
                    <p class="text-xs font-bold">Belum ada notifikasi baru.</p>
                  </div>
                </template>
              </div>
            </div>
          </div>
        @endauth

        <!-- Dark Mode Toggler -->
        <button
          title="Toggle Light/Dark Theme"
          class="relative flex h-10 w-10 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-600 transition-colors hover:bg-amber-100 hover:text-amber-800 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-amber-400 focus:outline-none"
          @click.prevent="darkMode = !darkMode">
          <!-- Sun Icon (Visible in Dark Mode) -->
          <svg class="hidden dark:block w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="5"></circle>
            <line x1="12" y1="1" x2="12" y2="3"></line>
            <line x1="12" y1="21" x2="12" y2="23"></line>
            <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
            <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
            <line x1="1" y1="12" x2="3" y2="12"></line>
            <line x1="21" y1="12" x2="23" y2="12"></line>
            <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
            <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
          </svg>
          <!-- Moon Icon (Visible in Light Mode) -->
          <svg class="block dark:hidden w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
          </svg>
        </button>
      </div>

      <!-- User Profile Static Badge (Non-clickable, strict roles: Klien, Fotografer, or Admin) -->
      @auth
        <div class="flex items-center gap-3 px-3 py-1.5 rounded-xl bg-gray-50/80 dark:bg-gray-800/80 border border-gray-200 dark:border-gray-700/80">
          <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-amber-500 to-amber-300 text-white font-black text-xs flex items-center justify-center shadow-sm overflow-hidden shrink-0">
            {{ strtoupper(substr(auth()->user()->nama ?? 'U', 0, 2)) }}
          </div>
          <div class="text-left hidden sm:block pr-1">
            <span class="block text-xs font-extrabold text-gray-900 dark:text-white leading-tight">
              {{ auth()->user()->nama }}
            </span>
            <span class="block text-[10px] font-bold text-amber-600 dark:text-amber-400 capitalize">
              {{ auth()->user()->role === 'photographer' ? 'Fotografer' : (in_array(auth()->user()->role, ['super_admin', 'admin']) ? 'Admin' : 'Klien') }}
            </span>
          </div>
        </div>
      @endauth
    </div>
  </div>
</header>