<aside :class="sidebarToggle ? 'translate-x-0 lg:w-[90px]' : '-translate-x-full'"
  class="sidebar fixed left-0 top-0 z-50 flex h-screen w-[290px] flex-col overflow-y-hidden border-r border-gray-200 bg-white px-5 dark:border-gray-800 dark:bg-gray-900 lg:static lg:translate-x-0">
  <!-- SIDEBAR HEADER -->
  <div :class="sidebarToggle ? 'justify-center' : 'justify-between'"
    class="flex items-center gap-2 pt-6 sidebar-header pb-6 border-b border-gray-100 dark:border-gray-800">
    <a href="{{ url('/photographer/dashboard') }}" class="flex items-center gap-2">
      <img onerror="this.onerror=null;this.src=window.SVG_IMAGE_FALLBACK||'data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='800' height='600' viewBox='0 0 800 600' fill='none'%3E%3Crect width='800' height='600' fill='%23F3F4F6'/%3E%3Cpath d='M360 260C360 248.954 368.954 240 380 240H420C431.046 240 440 248.954 440 260V265H450C466.569 265 480 278.431 480 295V345C480 361.569 466.569 375 450 375H350C333.431 375 320 361.569 320 345V295C320 278.431 333.431 265 350 265H360V260Z' stroke='%239CA3AF' stroke-width='12' stroke-linecap='round' stroke-linejoin='round'/%3E%3Ccircle cx='400' cy='320' r='30' stroke='%239CA3AF' stroke-width='12'/%3E%3Cline x1='310' y1='240' x2='490' y2='390' stroke='%23EF4444' stroke-width='10' stroke-linecap='round'/%3E%3Ctext x='400' y='450' font-family='sans-serif' font-size='22' font-weight='700' fill='%236B7280' text-anchor='middle'%3EGambar Tidak Dapat Dimuat%3C/text%3E%3C/svg%3E';" src="{{ asset('images/logo/lensmatch_logo_transparent_yellow.png') }}" class="h-8 w-auto" alt="LensMatch Logo" :class="sidebarToggle ? 'hidden' : ''" />
    </a>
  </div>

  <div class="flex flex-col overflow-y-auto duration-300 ease-linear no-scrollbar pt-6 pb-6">
    <nav>
      <div>
        <h3 class="mb-4 text-[10px] font-bold uppercase tracking-wider text-gray-400">
          <span class="menu-group-title" :class="sidebarToggle ? 'lg:hidden' : ''">
            NAVIGASI UTAMA FOTOGRAFER
          </span>
        </h3>

        <ul class="flex flex-col gap-1.5 mb-6 text-sm font-semibold">
          <!-- Dashboard -->
          <li>
            <a href="{{ url('/photographer/dashboard') }}" class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 {{ request()->is('photographer/dashboard') ? 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400 font-bold' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5' }}">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" class="fill-current" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M5.5 3.25C4.25736 3.25 3.25 4.25736 3.25 5.5V8.99998C3.25 10.2426 4.25736 11.25 5.5 11.25H9C10.2426 11.25 11.25 10.2426 11.25 8.99998V5.5C11.25 4.25736 10.2426 3.25 9 3.25H5.5ZM4.75 5.5C4.75 5.08579 5.08579 4.75 5.5 4.75H9C9.41421 4.75 9.75 5.08579 9.75 5.5V8.99998C9.75 9.41419 9.41421 9.74998 9 9.74998H5.5C5.08579 9.74998 4.75 9.41419 4.75 8.99998V5.5ZM5.5 12.75C4.25736 12.75 3.25 13.7574 3.25 15V18.5C3.25 19.7426 4.25736 20.75 5.5 20.75H9C10.2426 20.75 11.25 19.7427 11.25 18.5V15C11.25 13.7574 10.2426 12.75 9 12.75H5.5ZM4.75 15C4.75 14.5858 5.08579 14.25 5.5 14.25H9C9.41421 14.25 9.75 14.5858 9.75 15V18.5C9.75 18.9142 9.41421 19.25 9 19.25H5.5C5.08579 19.25 4.75 18.9142 4.75 18.5V15ZM12.75 5.5C12.75 4.25736 13.7574 3.25 15 3.25H18.5C19.7426 3.25 20.75 4.25736 20.75 5.5V8.99998C20.75 10.2426 19.7426 11.25 18.5 11.25H15C13.7574 11.25 12.75 10.2426 12.75 8.99998V5.5ZM15 4.75C14.5858 4.75 14.25 5.08579 15 5.5V8.99998C14.25 9.41419 14.5858 9.74998 15 9.74998H18.5C18.9142 9.74998 19.25 9.41419 19.25 8.99998V5.5C19.25 5.08579 18.9142 4.75 18.5 4.75H15ZM15 12.75C13.7574 12.75 12.75 13.7574 12.75 15V18.5C12.75 19.7426 13.7574 20.75 15 20.75H18.5C19.7426 20.75 20.75 19.7427 20.75 18.5V15C20.75 13.7574 19.7426 12.75 18.5 12.75H15C14.25 15C14.25 14.5858 14.5858 14.25 15 14.25H18.5C18.9142 14.25 19.25 14.5858 19.25 15V18.5C19.25 18.9142 18.9142 19.25 18.5 19.25H15C14.5858 19.25 14.25 18.9142 14.25 18.5V15Z"/>
              </svg>
              <span :class="sidebarToggle ? 'lg:hidden' : ''">Dashboard</span>
            </a>
          </li>

          <!-- Profil & Bio -->
          <li>
            <a href="{{ url('/profile') }}" class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 {{ request()->is('photographer/profile') ? 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400 font-bold' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5' }}">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" class="fill-current" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M12 3.5C7.30558 3.5 3.5 7.30558 3.5 12C3.5 14.1526 4.3002 16.1184 5.61936 17.616C6.17279 15.3096 8.24852 13.5955 10.7246 13.5955H13.2746C15.7509 13.5955 17.8268 15.31 18.38 17.6167C19.6996 16.119 20.5 14.153 20.5 12C20.5 7.30558 16.6944 3.5 12 3.5ZM17.0246 18.8566V18.8455C17.0246 16.7744 15.3457 15.0955 13.2746 15.0955H10.7246C8.65354 15.0955 6.97461 16.7744 6.97461 18.8455V18.856C8.38223 19.8895 10.1198 20.5 12 20.5C13.8798 20.5 15.6171 19.8898 17.0246 18.8566ZM2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12ZM11.9991 7.25C10.8847 7.25 9.98126 8.15342 9.98126 9.26784C9.98126 10.3823 10.8847 11.2857 11.9991 11.2857C13.1135 11.2857 14.0169 10.3823 14.0169 9.26784C14.0169 8.15342 13.1135 7.25 11.9991 7.25Z"/>
              </svg>
              <span :class="sidebarToggle ? 'lg:hidden' : ''">Profil & Bio</span>
            </a>
          </li>

          <!-- Layanan & Tarif -->
          <li>
            <a href="{{ url('/photographer/services') }}" class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 {{ request()->is('photographer/services') ? 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400 font-bold' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5' }}">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" class="fill-current" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM12 20C7.59 20 4 16.41 4 12C4 7.59 7.59 4 12 4C16.41 4 20 7.59 20 12C20 16.41 16.41 20 12 20ZM12 6C9.79 6 8 7.79 8 10H10C10 8.9 10.9 8 12 8C13.1 8 14 8.9 14 10C14 12 11 11.75 11 15H13C13 12.75 16 12.5 16 10C16 7.79 14.21 6 12 6ZM11 16H13V18H11V16Z"/>
              </svg>
              <span :class="sidebarToggle ? 'lg:hidden' : ''">Paket Layanan & Tarif</span>
            </a>
          </li>

          <!-- Portofolio -->
          <li>
            <a href="{{ url('/photographer/portfolio') }}" class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 {{ request()->is('photographer/portfolio') ? 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400 font-bold' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5' }}">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" class="fill-current" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M21 19V5C21 3.9 20.1 3 19 3H5C3.9 3 3 3.9 3 5V19C3 20.1 3.9 21 5 21H19C20.1 21 21 20.1 21 19ZM8.5 13.5L11 16.51L14.5 12L19 18H5L8.5 13.5Z"/>
              </svg>
              <span :class="sidebarToggle ? 'lg:hidden' : ''">Portofolio & Galeri</span>
            </a>
          </li>

          <!-- Availability Kalender -->
          <li>
            <a href="{{ url('/photographer/availability') }}" class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 {{ request()->is('photographer/availability') ? 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400 font-bold' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5' }}">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" class="fill-current" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M8 2V3.75H16V2H17.75V3.75H19C19.9665 3.75 20.75 4.5335 20.75 5.5V19C20.75 19.9665 19.9665 20.75 19 20.75H5C4.0335 20.75 3.25 19.9665 3.25 19V5.5C3.25 4.5335 4.0335 3.75 5 3.75H6.25V2H8ZM5 8V19H19V8H5Z"/>
              </svg>
              <span :class="sidebarToggle ? 'lg:hidden' : ''">Jadwal & Availability</span>
            </a>
          </li>

          <!-- Booking Masuk -->
          <li>
            <a href="{{ url('/photographer/bookings') }}" class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 {{ request()->is('photographer/bookings') ? 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400 font-bold' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5' }}">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" class="fill-current" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M19 3H5C3.9 3 3 3.9 3 5V19C3 20.1 3.9 21 5 21H19C20.1 21 21 20.1 21 19V5C21 3.9 20.1 3 19 3ZM14 17H7V15H14V17ZM17 13H7V11H17V13ZM17 9H7V7H17V9Z"/>
              </svg>
              <span :class="sidebarToggle ? 'lg:hidden' : ''">Booking & Kontrak Masuk</span>
            </a>
          </li>

          <!-- Chat Client -->
          <li>
            <a href="{{ url('/photographer/chat') }}" class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 {{ request()->is('photographer/chat') ? 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400 font-bold' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5' }}">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" class="fill-current" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M20 2H4C2.9 2 2 2.9 2 4V22L6 18H20C21.1 18 22 17.1 22 16V4C22 2.9 21.1 2 20 2ZM20 16H5.17L4 17.17V4H20V16Z"/>
              </svg>
              <span :class="sidebarToggle ? 'lg:hidden' : ''">Pesan & Chat Client</span>
            </a>
          </li>

          <!-- Laporan Keuangan -->
          <li>
            <a href="{{ url('/photographer/reports') }}" class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 {{ request()->is('photographer/reports*') ? 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400 font-bold' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5' }}">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" class="fill-current" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M19 3H5C3.9 3 3 3.9 3 5V19C3 20.1 3.9 21 5 21H19C20.1 21 21 20.1 21 19V5C21 3.9 20.1 3 19 3ZM9 17H7V10H9V17ZM13 17H11V7H13V17ZM17 17H15V13H17V17Z"/>
              </svg>
              <span :class="sidebarToggle ? 'lg:hidden' : ''">Laporan Keuangan</span>
            </a>
          </li>

          <!-- Tombol Logout -->
          <li class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
            <button type="button" @click="$dispatch('open-confirm-modal', { title: 'Konfirmasi Keluar Akun', message: 'Apakah Anda yakin ingin keluar dari akun LensMatch?', actionUrl: '{{ route('logout') }}', method: 'POST', buttonText: 'Ya, Keluar Akun', iconType: 'logout', buttonClass: 'bg-red-600 hover:bg-red-700 text-white' })" class="w-full flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-red-600 hover:bg-red-50 dark:hover:bg-red-950/50 transition font-bold">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                <polyline points="16 17 21 12 16 7"></polyline>
                <line x1="21" y1="12" x2="9" y2="12"></line>
              </svg>
              <span :class="sidebarToggle ? 'lg:hidden' : ''">Keluar / Logout</span>
            </button>
          </li>
        </ul>
      </div>
    </nav>
  </div>
</aside>