<aside :class="sidebarToggle ? 'translate-x-0 lg:w-[84px]' : '-translate-x-full lg:w-[280px]'"
  class="sidebar fixed left-0 top-0 z-50 flex h-screen flex-col overflow-y-hidden border-r border-gray-200 bg-white px-4 dark:border-gray-800 dark:bg-gray-900 lg:static lg:translate-x-0 transition-all duration-300 ease-in-out shadow-lg lg:shadow-none">
  <!-- SIDEBAR HEADER -->
  <div :class="sidebarToggle ? 'justify-center px-0' : 'justify-between px-2'"
    class="flex items-center gap-2 pt-6 sidebar-header pb-6 border-b border-gray-100 dark:border-gray-800 transition-all duration-300 ease-in-out">
    <a href="{{ url('/admin/dashboard') }}" class="flex items-center gap-2">
      <img onerror="this.onerror=null;this.src=window.SVG_IMAGE_FALLBACK||'data:image/svg+xml;charset=UTF-8,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'800\' height=\'600\' viewBox=\'0 0 800 600\' fill=\'none\'%3E%3Crect width=\'800\' height=\'600\' fill=\'%23F3F4F6\'/%3E%3Cpath d=\'M360 260C360 248.954 368.954 240 380 240H420C431.046 240 440 248.954 440 260V265H450C466.569 265 480 278.431 480 295V345C480 361.569 466.569 375 450 375H350C333.431 375 320 361.569 320 345V295C320 278.431 333.431 265 350 265H360V260Z\' stroke=\'%239CA3AF\' stroke-width=\'12\' stroke-linecap=\'round\' stroke-linejoin=\'round\'/%3E%3Ccircle cx=\'400\' cy=\'320\' r=\'30\' stroke=\'%239CA3AF\' stroke-width=\'12\'/%3E%3Cline x1=\'310\' y1=\'240\' x2=\'490\' y2=\'390\' stroke=\'%23EF4444\' stroke-width=\'10\' stroke-linecap=\'round\'/%3E%3Ctext x=\'400\' y=\'450\' font-family=\'sans-serif\' font-size=\'22\' font-weight=\'700\' fill=\'%236B7280\' text-anchor=\'middle\'%3EGambar Tidak Dapat Dimuat%3C/text%3E%3C/svg%3E';" src="{{ asset('images/logo/lensmatch_logo_transparent_yellow.png') }}" class="h-8 w-auto" alt="LensMatch Logo" :class="sidebarToggle ? 'hidden' : ''" />
    </a>
  </div>

  <div class="flex flex-col overflow-y-auto duration-300 ease-linear no-scrollbar pt-6 pb-6">
    <nav>
      <div>
        <h3 class="mb-4 text-[10px] font-bold uppercase tracking-wider text-gray-400">
          <span class="menu-group-title" :class="sidebarToggle ? 'lg:hidden' : ''">
            NAVIGASI UTAMA ADMIN
          </span>
        </h3>

        <ul class="flex flex-col gap-1.5 mb-6 text-sm font-semibold">
          <!-- 1. Dashboard -->
          <li>
            <a href="{{ url('/admin/dashboard') }}" class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 active:scale-95 transition duration-150 {{ request()->is('admin/dashboard') ? 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400 font-bold' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5' }}">
              <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
              </svg>
              <span :class="sidebarToggle ? 'lg:hidden' : ''">Dashboard</span>
            </a>
          </li>

          <!-- 2. Monitoring Transaksi -->
          <li>
            <a href="{{ url('/admin/transactions') }}" class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 active:scale-95 transition duration-150 {{ request()->is('admin/transactions*') ? 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400 font-bold' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5' }}">
              <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-6-11.25h16.5A2.25 2.25 0 0121.75 7.5v10.5a2.25 2.25 0 01-2.25 2.25H3.75A2.25 2.25 0 011.5 18V7.5A2.25 2.25 0 013.75 5.25z" />
              </svg>
              <span :class="sidebarToggle ? 'lg:hidden' : ''">Monitoring Transaksi</span>
            </a>
          </li>

          <!-- 3. Manajemen User -->
          <li>
            <a href="{{ url('/admin/users') }}" class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 active:scale-95 transition duration-150 {{ request()->is('admin/users*') ? 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400 font-bold' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5' }}">
              <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
              </svg>
              <span :class="sidebarToggle ? 'lg:hidden' : ''">Manajemen User</span>
            </a>
          </li>

          <!-- 4. Manajemen Fotografer -->
          <li>
            <a href="{{ url('/admin/photographers') }}" class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 active:scale-95 transition duration-150 {{ request()->is('admin/photographers*') ? 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400 font-bold' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5' }}">
              <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
              </svg>
              <span :class="sidebarToggle ? 'lg:hidden' : ''">Manajemen Fotografer</span>
            </a>
          </li>

          <!-- 5. Manajemen Admin (Khusus Super Admin) -->
          @if(auth()->check() && auth()->user()->role === 'super_admin')
          <li>
            <a href="{{ url('/admin/admins') }}" class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 active:scale-95 transition duration-150 {{ request()->is('admin/admins*') ? 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400 font-bold' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5' }}">
              <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
              </svg>
              <span :class="sidebarToggle ? 'lg:hidden' : ''">Manajemen Admin</span>
            </a>
          </li>
          @endif

          <!-- 6. Kategori Layanan -->
          <li>
            <a href="{{ url('/admin/categories') }}" class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 active:scale-95 transition duration-150 {{ request()->is('admin/categories*') ? 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400 font-bold' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5' }}">
              <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.386l5.441-3.208c.827-.487 1.054-1.547.478-2.316L12.33 4.252A2.25 2.25 0 0010.554 3h-.986z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
              </svg>
              <span :class="sidebarToggle ? 'lg:hidden' : ''">Kategori Layanan</span>
            </a>
          </li>

          <!-- 7. Ulasan & Rating -->
          <li>
            <a href="{{ url('/admin/reviews') }}" class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 active:scale-95 transition duration-150 {{ request()->is('admin/reviews*') ? 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400 font-bold' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5' }}">
              <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
              </svg>
              <span :class="sidebarToggle ? 'lg:hidden' : ''">Ulasan & Rating</span>
            </a>
          </li>

          <!-- 8. Kelola Lomba Foto -->
          <li>
            <a href="{{ url('/admin/contests') }}" class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 active:scale-95 transition duration-150 {{ request()->is('admin/contests*') ? 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400 font-bold' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5' }}">
              <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.504-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.004-7.875v-.875c0-1.657-1.343-3-3-3s-3 1.343-3 3v.875m9.75 0v3.75a6 6 0 01-6 6h-.75a6 6 0 01-6-6v-3.75m12.75 0h2.25a2.25 2.25 0 012.25 2.25v.75a3 3 0 01-3 3h-1.5m-12.75-6H3.75A2.25 2.25 0 001.5 8.25v.75a3 3 0 003 3h1.5" />
              </svg>
              <span :class="sidebarToggle ? 'lg:hidden' : ''">Kelola Lomba Foto</span>
            </a>
          </li>

          <!-- 9. Materi E-Learning -->
          <li>
            <a href="{{ url('/admin/elearning') }}" class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 active:scale-95 transition duration-150 {{ request()->is('admin/elearning*') ? 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400 font-bold' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5' }}">
              <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
              </svg>
              <span :class="sidebarToggle ? 'lg:hidden' : ''">Materi E-Learning</span>
            </a>
          </li>

          <!-- 10. Moderasi Forum -->
          <li>
            <a href="{{ url('/admin/forum') }}" class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 active:scale-95 transition duration-150 {{ request()->is('admin/forum*') ? 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400 font-bold' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5' }}">
              <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
              </svg>
              <span :class="sidebarToggle ? 'lg:hidden' : ''">Moderasi Forum</span>
            </a>
          </li>

          <!-- Tombol Logout -->
          <li class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
            <button type="button" @click="$dispatch('open-confirm-modal', { title: 'Konfirmasi Keluar Akun', message: 'Apakah Anda yakin ingin keluar dari akun LensMatch?', actionUrl: '{{ route('logout') }}', method: 'POST', buttonText: 'Ya, Keluar Akun', iconType: 'logout', buttonClass: 'bg-red-600 hover:bg-red-700 text-white' })" class="w-full flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-red-600 hover:bg-red-50 dark:hover:bg-red-950/50 transition font-bold active:scale-95 duration-150">
              <svg class="w-5 h-5 shrink-0 text-rose-600" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
              </svg>
              <span :class="sidebarToggle ? 'lg:hidden' : ''">Keluar</span>
            </button>
          </li>
        </ul>
      </div>
    </nav>
  </div>
</aside>