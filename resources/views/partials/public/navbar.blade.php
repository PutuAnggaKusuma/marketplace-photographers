<header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-gray-100 dark:bg-gray-900/95 dark:border-gray-800 shadow-xs">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between h-16 sm:h-20">
      <!-- Logo -->
      <div class="flex items-center gap-3">
        <a href="{{ url('/') }}" class="flex items-center gap-2">
          <img onerror="this.onerror=null;this.src=window.SVG_IMAGE_FALLBACK||'data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='800' height='600' viewBox='0 0 800 600' fill='none'%3E%3Crect width='800' height='600' fill='%23F3F4F6'/%3E%3Cpath d='M360 260C360 248.954 368.954 240 380 240H420C431.046 240 440 248.954 440 260V265H450C466.569 265 480 278.431 480 295V345C480 361.569 466.569 375 450 375H350C333.431 375 320 361.569 320 345V295C320 278.431 333.431 265 350 265H360V260Z' stroke='%239CA3AF' stroke-width='12' stroke-linecap='round' stroke-linejoin='round'/%3E%3Ccircle cx='400' cy='320' r='30' stroke='%239CA3AF' stroke-width='12'/%3E%3Cline x1='310' y1='240' x2='490' y2='390' stroke='%23EF4444' stroke-width='10' stroke-linecap='round'/%3E%3Ctext x='400' y='450' font-family='sans-serif' font-size='22' font-weight='700' fill='%236B7280' text-anchor='middle'%3EGambar Tidak Dapat Dimuat%3C/text%3E%3C/svg%3E';" src="{{ asset('images/logo/lensmatch_logo_transparent_yellow.png') }}" class="h-9 sm:h-10 w-auto" alt="LensMatch Logo" />
        </a>
      </div>

      <!-- Navigation Links with Pure Yellow Text Active Highlight -->
      <nav class="hidden md:flex items-center gap-8 text-sm font-semibold">
        <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'text-amber-400 dark:text-amber-400 font-extrabold' : 'text-gray-700 dark:text-gray-300 hover:text-amber-400 dark:hover:text-amber-400 transition' }}">
          Beranda
        </a>
        <a href="{{ url('/fotografer') }}" class="{{ request()->is('fotografer*') ? 'text-amber-400 dark:text-amber-400 font-extrabold' : 'text-gray-700 dark:text-gray-300 hover:text-amber-400 dark:hover:text-amber-400 transition' }}">
          Cari Fotografer
        </a>
        <a href="{{ url('/forum') }}" class="{{ request()->is('forum*') ? 'text-amber-400 dark:text-amber-400 font-extrabold' : 'text-gray-700 dark:text-gray-300 hover:text-amber-400 dark:hover:text-amber-400 transition' }}">
          Komunitas Forum
        </a>
        <a href="{{ url('/lomba') }}" class="{{ request()->is('lomba*') ? 'text-amber-400 dark:text-amber-400 font-extrabold' : 'text-gray-700 dark:text-gray-300 hover:text-amber-400 dark:hover:text-amber-400 transition' }}">
          Informasi Lomba
        </a>
        <a href="{{ url('/elearning') }}" class="{{ request()->is('elearning*') ? 'text-amber-400 dark:text-amber-400 font-extrabold' : 'text-gray-700 dark:text-gray-300 hover:text-amber-400 dark:hover:text-amber-400 transition' }}">
          E-Learning
        </a>
      </nav>

      <!-- Auth Buttons, Notifications & User Profile Dropdown -->
      <div class="flex items-center gap-3 sm:gap-4">
        @auth
          @if(in_array(auth()->user()->role, ['super_admin', 'admin']))
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2.5 text-xs sm:text-sm font-extrabold text-gray-900 bg-amber-400 hover:bg-amber-500 rounded-xl transition shadow-xs">
              Dashboard Admin
            </a>
          @elseif(auth()->user()->role === 'photographer')
            <a href="{{ route('photographer.dashboard') }}" class="px-4 py-2.5 text-xs sm:text-sm font-extrabold text-gray-900 bg-amber-400 hover:bg-amber-500 rounded-xl transition shadow-xs">
              Dashboard Studio
            </a>
          @endif

          <!-- Dynamic Real-Time Notification Bell Dropdown -->
          <div class="relative" x-data="headerNotifApp()" x-init="init()">
            <button @click="notifOpen = !notifOpen" class="relative p-2 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-amber-400 dark:hover:text-amber-400 focus:outline-none transition">
              <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
              </svg>
              <!-- Unread Red Pulse Indicator Badge -->
              <span x-show="unreadCount > 0" 
                    x-cloak 
                    class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[9px] font-black text-white ring-2 ring-white dark:ring-gray-900 animate-pulse">
                <span x-text="unreadCount > 9 ? '9+' : unreadCount"></span>
              </span>
            </button>

            <!-- Notifications Dropdown List -->
            <div x-show="notifOpen" @click.away="notifOpen = false" x-cloak x-transition class="absolute right-0 mt-3 w-80 sm:w-96 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700/80 py-2 z-50 overflow-hidden">
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

<script>
function headerNotifApp() {
    return {
        notifOpen: false,
        unreadCount: 0,
        notifications: [],
        init() {
            this.fetchNotifs();
            setInterval(() => this.fetchNotifs(), 30000);
        },
        fetchNotifs() {
            fetch('{{ route("notifications.unread") }}', {
                headers: { 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.unreadCount = data.unread_count || 0;
                    this.notifications = data.notifications || [];
                }
            })
            .catch(err => console.error(err));
        },
        markAllRead() {
            fetch('{{ route("notifications.read-all") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.unreadCount = 0;
                    this.notifications.forEach(n => n.is_read = true);
                }
            });
        },
        openNotif(notif) {
            fetch(`/notifications/${notif.id}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(() => {
                if (notif.url) {
                    window.location.href = notif.url;
                }
            });
        }
    };
}
</script>

          <!-- User Profile Dropdown -->
          <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="flex items-center gap-2.5 text-xs sm:text-sm font-bold text-gray-800 dark:text-white hover:text-amber-400 dark:hover:text-amber-400 focus:outline-none p-1 rounded-xl transition">
              <!-- Navbar Avatar (x-user-avatar: foto or initials) -->
              <x-user-avatar :user="auth()->user()" size="w-9 h-9 sm:w-10 sm:h-10" bg="bg-amber-400" textColor="text-gray-900" textSize="text-sm" />
              <span class="hidden sm:inline font-extrabold text-sm">{{ auth()->user()->nama }}</span>
              <svg class="w-4 h-4 text-gray-500 transition-transform duration-200" :class="open ? 'rotate-180 text-amber-400' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
            </button>

            <!-- Enlarged Proportional User Dropdown Menu -->
            <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-3 w-72 sm:w-80 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700/80 py-2 z-50 overflow-hidden">
              
              <!-- User Header Card -->
              <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/70 dark:bg-gray-900/60 flex items-center gap-3.5">
                <div class="min-w-0 flex-1">
                  <p class="text-sm font-black text-gray-900 dark:text-white truncate">{{ auth()->user()->nama }}</p>
                  <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                </div>
              </div>
              
              <!-- Client Specific Menu Links with Clean Vector SVG Icons -->
              @if(auth()->user()->role === 'client')
                <a href="{{ url('/client/bookings') }}" class="px-5 py-3.5 text-xs sm:text-sm text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 border-b border-gray-100 dark:border-gray-700/60 transition flex items-center gap-3 font-bold group">
                  <svg class="w-4 h-4 text-gray-400 group-hover:text-amber-400 dark:hover:text-amber-400 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                  <span>Reservasi Saya</span>
                </a>
                <a href="{{ url('/client/invoices') }}" class="px-5 py-3.5 text-xs sm:text-sm text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 border-b border-gray-100 dark:border-gray-700/60 transition flex items-center gap-3 font-bold group">
                  <svg class="w-4 h-4 text-gray-400 group-hover:text-amber-400 dark:hover:text-amber-400 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                  <span>Invoice & Pembayaran</span>
                </a>
                <a href="{{ url('/client/galleries') }}" class="px-5 py-3.5 text-xs sm:text-sm text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 border-b border-gray-100 dark:border-gray-700/60 transition flex items-center gap-3 font-bold group">
                  <svg class="w-4 h-4 text-gray-400 group-hover:text-amber-400 dark:hover:text-amber-400 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                  <span>Galeri Hasil Foto Saya</span>
                </a>
                <a href="{{ url('/chat') }}" class="px-5 py-3.5 text-xs sm:text-sm text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 border-b border-gray-100 dark:border-gray-700/60 transition flex items-center gap-3 font-bold group">
                  <svg class="w-4 h-4 text-gray-400 group-hover:text-amber-400 dark:hover:text-amber-400 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                  <span>Pesan & Chat Studio</span>
                </a>
                <a href="{{ url('/profile') }}" class="px-5 py-3.5 text-xs sm:text-sm text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 border-b border-gray-100 dark:border-gray-700/60 transition flex items-center gap-3 font-bold group">
                  <svg class="w-4 h-4 text-gray-400 group-hover:text-amber-400 dark:hover:text-amber-400 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                  <span>Pengaturan Profil</span>
                </a>
              @elseif(auth()->user()->role === 'photographer')
                <a href="{{ route('photographer.dashboard') }}" class="px-5 py-3.5 text-xs sm:text-sm text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 border-b border-gray-100 dark:border-gray-700/60 transition flex items-center gap-3 font-bold group">
                  <svg class="w-4 h-4 text-gray-400 group-hover:text-amber-400 dark:hover:text-amber-400 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                  <span>Dashboard Studio</span>
                </a>
                <a href="{{ route('photographer.bookings') }}" class="px-5 py-3.5 text-xs sm:text-sm text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 border-b border-gray-100 dark:border-gray-700/60 transition flex items-center gap-3 font-bold group">
                  <svg class="w-4 h-4 text-gray-400 group-hover:text-amber-400 dark:hover:text-amber-400 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                  <span>Order Booking Masuk</span>
                </a>
                <a href="{{ route('photographer.services') }}" class="px-5 py-3.5 text-xs sm:text-sm text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 border-b border-gray-100 dark:border-gray-700/60 transition flex items-center gap-3 font-bold group">
                  <svg class="w-4 h-4 text-gray-400 group-hover:text-amber-400 dark:hover:text-amber-400 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                  <span>Kelola Paket & Layanan</span>
                </a>
                <a href="{{ route('photographer.portfolio') }}" class="px-5 py-3.5 text-xs sm:text-sm text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 border-b border-gray-100 dark:border-gray-700/60 transition flex items-center gap-3 font-bold group">
                  <svg class="w-4 h-4 text-gray-400 group-hover:text-amber-400 dark:hover:text-amber-400 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                  <span>Galeri Portofolio</span>
                </a>
              @else
                <a href="{{ route('admin.dashboard') }}" class="px-5 py-3.5 text-xs sm:text-sm text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 border-b border-gray-100 dark:border-gray-700/60 transition flex items-center gap-3 font-bold group">
                  <svg class="w-4 h-4 text-gray-400 group-hover:text-amber-400 dark:hover:text-amber-400 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                  <span>Dashboard Admin Utama</span>
                </a>
                <a href="{{ route('admin.users') }}" class="px-5 py-3.5 text-xs sm:text-sm text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 border-b border-gray-100 dark:border-gray-700/60 transition flex items-center gap-3 font-bold group">
                  <svg class="w-4 h-4 text-gray-400 group-hover:text-amber-400 dark:hover:text-amber-400 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                  <span>Kelola Pengguna & Admin</span>
                </a>
              @endif
              
              <!-- Logout Action Button with Confirmation Modal Trigger -->
              <div class="pt-1">
                <button type="button" @click="open = false; $dispatch('open-confirm-modal', { title: 'Konfirmasi Keluar Akun', message: 'Apakah Anda yakin ingin keluar dari akun LensMatch?', actionUrl: '{{ route('logout') }}', method: 'POST', buttonText: 'Ya, Keluar Akun', iconType: 'logout', buttonClass: 'bg-red-600 hover:bg-red-700 text-white' })" class="w-full text-left px-5 py-3.5 text-xs sm:text-sm font-extrabold text-red-600 hover:bg-red-50 dark:hover:bg-red-950/50 transition flex items-center gap-3 group">
                  <svg class="w-4 h-4 text-red-500 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                  <span>Keluar / Logout</span>
                </button>
              </div>
            </div>
          </div>
        @else
          <a href="{{ url('/login') }}" class="text-xs font-bold text-gray-700 hover:text-amber-400 dark:hover:text-amber-400 dark:text-gray-300 px-3 py-2 transition">Masuk</a>
          <a href="{{ url('/register') }}" class="px-5 py-2 text-xs font-extrabold text-gray-900 bg-amber-400 hover:bg-amber-500 rounded-xl shadow-xs transition">Daftar</a>
        @endauth
      </div>
    </div>
  </div>
</header>