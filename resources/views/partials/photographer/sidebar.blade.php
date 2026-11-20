<aside :class="sidebarToggle ? 'translate-x-0 lg:w-[84px] lg:px-3.5' : '-translate-x-full lg:w-[280px] lg:px-4'"
  class="sidebar fixed left-0 top-0 z-50 flex h-screen flex-col overflow-y-hidden border-r border-gray-200 bg-white px-4 dark:border-gray-800 dark:bg-gray-900 lg:static lg:translate-x-0 transition-all duration-300 ease-in-out shadow-lg lg:shadow-none">
  @php
    $sidebarPhotographer = auth()->user()?->rolePhotographer;
    $sidebarIsUnlocked = $sidebarPhotographer && $sidebarPhotographer->isFeatureUnlocked();
  @endphp
  <!-- SIDEBAR HEADER -->
  <div :class="sidebarToggle ? 'lg:justify-center lg:px-0' : 'justify-between px-2'"
    class="flex items-center gap-2 pt-6 sidebar-header pb-6 border-b border-gray-100 dark:border-gray-800 transition-all duration-300 ease-in-out">
    <div class="relative flex items-center justify-center h-9 w-full">
      <!-- Expanded Full Logo -->
      <a href="{{ url('/photographer/dashboard') }}" 
         class="flex items-center transition-all duration-300 ease-in-out"
         :class="sidebarToggle ? 'lg:opacity-0 lg:scale-90 lg:pointer-events-none lg:w-0 overflow-hidden' : 'opacity-100 scale-100'">
        <img onerror="this.onerror=null;this.src=window.SVG_IMAGE_FALLBACK||'data:image/svg+xml;charset=UTF-8,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'800\' height=\'600\' viewBox=\'0 0 800 600\' fill=\'none\'%3E%3Crect width=\'800\' height=\'600\' fill=\'%23F3F4F6\'/%3E%3Cpath d=\'M360 260C360 248.954 368.954 240 380 240H420C431.046 240 440 248.954 440 260V265H450C466.569 265 480 278.431 480 295V345C480 361.569 466.569 375 450 375H350C333.431 375 320 361.569 320 345V295C320 278.431 333.431 265 350 265H360V260Z\' stroke=\'%239CA3AF\' stroke-width=\'12\' stroke-linecap=\'round\' stroke-linejoin=\'round\'/%3E%3Ccircle cx=\'400\' cy=\'320\' r=\'30\' stroke=\'%239CA3AF\' stroke-width=\'12\'/%3E%3Cline x1=\'310\' y1=\'240\' x2=\'490\' y2=\'390\' stroke=\'%23EF4444\' stroke-width=\'10\' stroke-linecap=\'round\'/%3E%3Ctext x=\'400\' y=\'450\' font-family=\'sans-serif\' font-size=\'22\' font-weight=\'700\' fill=\'%236B7280\' text-anchor=\'middle\'%3EGambar Tidak Dapat Dimuat%3C/text%3E%3C/svg%3E';" 
             src="{{ asset('images/logo/lensmatch_logo_transparent_yellow.png') }}" 
             class="h-8 w-auto max-w-[160px] object-contain" 
             alt="LensMatch Logo" />
      </a>

      <!-- Collapsed Compact Monogram Mark -->
      <a href="{{ url('/photographer/dashboard') }}"
         title="LensMatch"
         class="hidden lg:flex items-center justify-center transition-all duration-300 ease-in-out"
         :class="sidebarToggle ? 'opacity-100 scale-100' : 'opacity-0 scale-90 pointer-events-none w-0 overflow-hidden'">
        <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60 text-amber-500 font-black text-lg tracking-wider shadow-2xs">
          LM
        </span>
      </a>
    </div>
  </div>

  <div class="flex flex-col overflow-y-auto duration-300 ease-linear no-scrollbar pt-6 pb-6">
    <nav>
      <div>
        <div class="mb-3 transition-all duration-300 ease-in-out"
             :class="sidebarToggle ? 'lg:mb-2 lg:flex lg:justify-center' : ''">
          <span class="overflow-hidden whitespace-nowrap block text-[10px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 transition-all duration-300 ease-in-out px-1"
                :class="sidebarToggle ? 'lg:opacity-0 lg:max-h-0 lg:max-w-0 lg:pointer-events-none' : 'opacity-100 max-h-6 max-w-[220px]'">
            NAVIGASI UTAMA FOTOGRAFER
          </span>
          <span class="hidden transition-all duration-300 ease-in-out w-6 h-0.5 bg-gray-200 dark:bg-gray-800 rounded-full"
                :class="sidebarToggle ? 'lg:block' : ''"></span>
        </div>

        <ul class="flex flex-col gap-1.5 mb-6 text-sm font-semibold">
          <!-- 1. Dashboard -->
          <li>
            <a href="{{ url('/photographer/dashboard') }}" 
               title="Dashboard"
               :class="sidebarToggle ? 'lg:justify-center lg:px-0 lg:gap-0' : 'px-3.5 gap-3'"
               class="flex items-center rounded-xl py-2.5 active:scale-95 transition-all duration-300 ease-in-out {{ request()->is('photographer/dashboard') ? 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400 font-bold' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5' }}">
              <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
              </svg>
              <span class="overflow-hidden whitespace-nowrap transition-all duration-300 ease-in-out font-medium"
                    :class="sidebarToggle ? 'lg:opacity-0 lg:max-w-0 lg:-translate-x-2 lg:pointer-events-none' : 'opacity-100 max-w-[200px] translate-x-0'">Dashboard</span>
            </a>
          </li>

          <!-- 2. Profil -->
          <li>
            <a href="{{ route('photographer.profile') }}" 
               title="Profil"
               :class="sidebarToggle ? 'lg:justify-center lg:px-0 lg:gap-0' : 'px-3.5 gap-3'"
               class="flex items-center rounded-xl py-2.5 active:scale-95 transition-all duration-300 ease-in-out {{ request()->is('photographer/profile') ? 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400 font-bold' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5' }}">
              <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.964 0a9 9 0 10-11.963 0m11.964 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              <span class="overflow-hidden whitespace-nowrap transition-all duration-300 ease-in-out font-medium"
                    :class="sidebarToggle ? 'lg:opacity-0 lg:max-w-0 lg:-translate-x-2 lg:pointer-events-none' : 'opacity-100 max-w-[200px] translate-x-0'">Profil</span>
            </a>
          </li>

          <!-- 3. Paket Layanan -->
          <li>
            @if($sidebarIsUnlocked)
              <a href="{{ url('/photographer/services') }}" 
                 title="Paket Layanan"
                 :class="sidebarToggle ? 'lg:justify-center lg:px-0 lg:gap-0' : 'px-3.5 gap-3'"
                 class="flex items-center rounded-xl py-2.5 active:scale-95 transition-all duration-300 ease-in-out {{ request()->is('photographer/services*') ? 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400 font-bold' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.386l5.441-3.208c.827-.487 1.054-1.547.478-2.316L12.33 4.252A2.25 2.25 0 0010.554 3h-.986z" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
                </svg>
                <span class="overflow-hidden whitespace-nowrap transition-all duration-300 ease-in-out font-medium"
                      :class="sidebarToggle ? 'lg:opacity-0 lg:max-w-0 lg:-translate-x-2 lg:pointer-events-none' : 'opacity-100 max-w-[200px] translate-x-0'">Paket Layanan</span>
              </a>
            @else
              <a href="javascript:void(0)" 
                 @click.prevent="$dispatch('show-toast', { type: 'warning', message: 'Lengkapi profil 100% dan tunggu verifikasi Admin untuk membuka fitur Paket Layanan.' })"
                 title="Lengkapi profil 100% dan tunggu verifikasi Admin untuk membuka fitur Paket Layanan"
                 :class="sidebarToggle ? 'lg:justify-center lg:px-0 lg:gap-0' : 'px-3.5 gap-3 justify-between'"
                 class="flex items-center rounded-xl py-2.5 text-gray-400 dark:text-gray-600 opacity-40 cursor-not-allowed select-none transition-all duration-300 ease-in-out">
                <div class="flex items-center gap-3 min-w-0" :class="sidebarToggle ? 'lg:gap-0' : ''">
                  <svg class="w-5 h-5 shrink-0 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.386l5.441-3.208c.827-.487 1.054-1.547.478-2.316L12.33 4.252A2.25 2.25 0 0010.554 3h-.986z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
                  </svg>
                  <span class="overflow-hidden whitespace-nowrap transition-all duration-300 ease-in-out font-medium truncate"
                        :class="sidebarToggle ? 'lg:opacity-0 lg:max-w-0 lg:-translate-x-2 lg:pointer-events-none' : 'opacity-100 max-w-[160px] translate-x-0'">Paket Layanan</span>
                </div>
                <svg :class="sidebarToggle ? 'lg:opacity-0 lg:max-w-0 lg:pointer-events-none' : 'opacity-100'" 
                     class="w-3.5 h-3.5 text-gray-400 dark:text-gray-600 shrink-0 transition-all duration-300 ease-in-out" 
                     fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                </svg>
              </a>
            @endif
          </li>

          <!-- 4. Portofolio -->
          <li>
            @if($sidebarIsUnlocked)
              <a href="{{ url('/photographer/portfolio') }}" 
                 title="Portofolio"
                 :class="sidebarToggle ? 'lg:justify-center lg:px-0 lg:gap-0' : 'px-3.5 gap-3'"
                 class="flex items-center rounded-xl py-2.5 active:scale-95 transition-all duration-300 ease-in-out {{ request()->is('photographer/portfolio*') ? 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400 font-bold' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                </svg>
                <span class="overflow-hidden whitespace-nowrap transition-all duration-300 ease-in-out font-medium"
                      :class="sidebarToggle ? 'lg:opacity-0 lg:max-w-0 lg:-translate-x-2 lg:pointer-events-none' : 'opacity-100 max-w-[200px] translate-x-0'">Portofolio</span>
              </a>
            @else
              <a href="javascript:void(0)" 
                 @click.prevent="$dispatch('show-toast', { type: 'warning', message: 'Lengkapi profil 100% dan tunggu verifikasi Admin untuk membuka fitur Portofolio.' })"
                 title="Lengkapi profil 100% dan tunggu verifikasi Admin untuk membuka fitur Portofolio"
                 :class="sidebarToggle ? 'lg:justify-center lg:px-0 lg:gap-0' : 'px-3.5 gap-3 justify-between'"
                 class="flex items-center rounded-xl py-2.5 text-gray-400 dark:text-gray-600 opacity-40 cursor-not-allowed select-none transition-all duration-300 ease-in-out">
                <div class="flex items-center gap-3 min-w-0" :class="sidebarToggle ? 'lg:gap-0' : ''">
                  <svg class="w-5 h-5 shrink-0 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                  </svg>
                  <span class="overflow-hidden whitespace-nowrap transition-all duration-300 ease-in-out font-medium truncate"
                        :class="sidebarToggle ? 'lg:opacity-0 lg:max-w-0 lg:-translate-x-2 lg:pointer-events-none' : 'opacity-100 max-w-[160px] translate-x-0'">Portofolio</span>
                </div>
                <svg :class="sidebarToggle ? 'lg:opacity-0 lg:max-w-0 lg:pointer-events-none' : 'opacity-100'" 
                     class="w-3.5 h-3.5 text-gray-400 dark:text-gray-600 shrink-0 transition-all duration-300 ease-in-out" 
                     fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                </svg>
              </a>
            @endif
          </li>

          <!-- 5. Tanya Jawab (FAQ) -->
          <li>
            @if($sidebarIsUnlocked)
              <a href="{{ route('photographer.faqs') }}" 
                 title="Tanya Jawab (FAQ)"
                 :class="sidebarToggle ? 'lg:justify-center lg:px-0 lg:gap-0' : 'px-3.5 gap-3'"
                 class="flex items-center rounded-xl py-2.5 active:scale-95 transition-all duration-300 ease-in-out {{ request()->is('photographer/faqs*') ? 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400 font-bold' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                </svg>
                <span class="overflow-hidden whitespace-nowrap transition-all duration-300 ease-in-out font-medium"
                      :class="sidebarToggle ? 'lg:opacity-0 lg:max-w-0 lg:-translate-x-2 lg:pointer-events-none' : 'opacity-100 max-w-[200px] translate-x-0'">Tanya Jawab (FAQ)</span>
              </a>
            @else
              <a href="javascript:void(0)" 
                 @click.prevent="$dispatch('show-toast', { type: 'warning', message: 'Lengkapi profil 100% dan tunggu verifikasi Admin untuk membuka fitur Tanya Jawab (FAQ).' })"
                 title="Lengkapi profil 100% dan tunggu verifikasi Admin untuk membuka fitur Tanya Jawab (FAQ)"
                 :class="sidebarToggle ? 'lg:justify-center lg:px-0 lg:gap-0' : 'px-3.5 gap-3 justify-between'"
                 class="flex items-center rounded-xl py-2.5 text-gray-400 dark:text-gray-600 opacity-40 cursor-not-allowed select-none transition-all duration-300 ease-in-out">
                <div class="flex items-center gap-3 min-w-0" :class="sidebarToggle ? 'lg:gap-0' : ''">
                  <svg class="w-5 h-5 shrink-0 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                  </svg>
                  <span class="overflow-hidden whitespace-nowrap transition-all duration-300 ease-in-out font-medium truncate"
                        :class="sidebarToggle ? 'lg:opacity-0 lg:max-w-0 lg:-translate-x-2 lg:pointer-events-none' : 'opacity-100 max-w-[160px] translate-x-0'">Tanya Jawab (FAQ)</span>
                </div>
                <svg :class="sidebarToggle ? 'lg:opacity-0 lg:max-w-0 lg:pointer-events-none' : 'opacity-100'" 
                     class="w-3.5 h-3.5 text-gray-400 dark:text-gray-600 shrink-0 transition-all duration-300 ease-in-out" 
                     fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                </svg>
              </a>
            @endif
          </li>

          <!-- 6. Availability Kalender -->
          <li>
            @if($sidebarIsUnlocked)
              <a href="{{ url('/photographer/availability') }}" 
                 title="Jadwal"
                 :class="sidebarToggle ? 'lg:justify-center lg:px-0 lg:gap-0' : 'px-3.5 gap-3'"
                 class="flex items-center rounded-xl py-2.5 active:scale-95 transition-all duration-300 ease-in-out {{ request()->is('photographer/availability*') ? 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400 font-bold' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.253M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                </svg>
                <span class="overflow-hidden whitespace-nowrap transition-all duration-300 ease-in-out font-medium"
                      :class="sidebarToggle ? 'lg:opacity-0 lg:max-w-0 lg:-translate-x-2 lg:pointer-events-none' : 'opacity-100 max-w-[200px] translate-x-0'">Jadwal</span>
              </a>
            @else
              <a href="javascript:void(0)" 
                 @click.prevent="$dispatch('show-toast', { type: 'warning', message: 'Lengkapi profil 100% dan tunggu verifikasi Admin untuk membuka fitur Jadwal.' })"
                 title="Lengkapi profil 100% dan tunggu verifikasi Admin untuk membuka fitur Jadwal"
                 :class="sidebarToggle ? 'lg:justify-center lg:px-0 lg:gap-0' : 'px-3.5 gap-3 justify-between'"
                 class="flex items-center rounded-xl py-2.5 text-gray-400 dark:text-gray-600 opacity-40 cursor-not-allowed select-none transition-all duration-300 ease-in-out">
                <div class="flex items-center gap-3 min-w-0" :class="sidebarToggle ? 'lg:gap-0' : ''">
                  <svg class="w-5 h-5 shrink-0 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.253M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                  </svg>
                  <span class="overflow-hidden whitespace-nowrap transition-all duration-300 ease-in-out font-medium truncate"
                        :class="sidebarToggle ? 'lg:opacity-0 lg:max-w-0 lg:-translate-x-2 lg:pointer-events-none' : 'opacity-100 max-w-[160px] translate-x-0'">Jadwal</span>
                </div>
                <svg :class="sidebarToggle ? 'lg:opacity-0 lg:max-w-0 lg:pointer-events-none' : 'opacity-100'" 
                     class="w-3.5 h-3.5 text-gray-400 dark:text-gray-600 shrink-0 transition-all duration-300 ease-in-out" 
                     fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                </svg>
              </a>
            @endif
          </li>

          <!-- 7. Booking Masuk -->
          <li>
            @if($sidebarIsUnlocked)
              <a href="{{ url('/photographer/bookings') }}" 
                 title="Booking"
                 :class="sidebarToggle ? 'lg:justify-center lg:px-0 lg:gap-0' : 'px-3.5 gap-3 justify-between'"
                 class="relative flex items-center rounded-xl py-2.5 active:scale-95 transition-all duration-300 ease-in-out {{ request()->is('photographer/bookings*') ? 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400 font-bold' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5' }}">
                <div class="flex items-center gap-3 min-w-0" :class="sidebarToggle ? 'lg:gap-0' : ''">
                  <div class="relative shrink-0">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                    </svg>
                    <!-- Mini Dot Indicator for Collapsed State -->
                    <span x-show="$store.bookingNotifier && $store.bookingNotifier.pendingCount > 0"
                          :class="sidebarToggle ? 'lg:block' : ''"
                          class="hidden absolute -top-1 -right-1 w-2.5 h-2.5 rounded-full bg-rose-500 ring-2 ring-white dark:ring-gray-900"
                          style="display: none;"></span>
                  </div>
                  <span class="overflow-hidden whitespace-nowrap transition-all duration-300 ease-in-out font-medium truncate"
                        :class="sidebarToggle ? 'lg:opacity-0 lg:max-w-0 lg:-translate-x-2 lg:pointer-events-none' : 'opacity-100 max-w-[140px] translate-x-0'">Booking</span>
                </div>

                <!-- Real-Time Pending Booking Count Badge in Sidebar -->
                <span x-show="$store.bookingNotifier && $store.bookingNotifier.pendingCount > 0" 
                      :class="sidebarToggle ? 'lg:opacity-0 lg:max-w-0 lg:pointer-events-none' : 'opacity-100'"
                      x-text="$store.bookingNotifier.pendingCount > 99 ? '99+' : $store.bookingNotifier.pendingCount"
                      class="px-2 py-0.5 text-[10px] font-black rounded-full bg-rose-500 text-white shadow-xs animate-pulse shrink-0 transition-all duration-300 ease-in-out"
                      style="display: none;">
                </span>
              </a>
            @else
              <a href="javascript:void(0)" 
                 @click.prevent="$dispatch('show-toast', { type: 'warning', message: 'Lengkapi profil 100% dan tunggu verifikasi Admin untuk membuka fitur Booking.' })"
                 title="Lengkapi profil 100% dan tunggu verifikasi Admin untuk membuka fitur Booking"
                 :class="sidebarToggle ? 'lg:justify-center lg:px-0 lg:gap-0' : 'px-3.5 gap-3 justify-between'"
                 class="flex items-center rounded-xl py-2.5 text-gray-400 dark:text-gray-600 opacity-40 cursor-not-allowed select-none transition-all duration-300 ease-in-out">
                <div class="flex items-center gap-3 min-w-0" :class="sidebarToggle ? 'lg:gap-0' : ''">
                  <svg class="w-5 h-5 shrink-0 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                  </svg>
                  <span class="overflow-hidden whitespace-nowrap transition-all duration-300 ease-in-out font-medium truncate"
                        :class="sidebarToggle ? 'lg:opacity-0 lg:max-w-0 lg:-translate-x-2 lg:pointer-events-none' : 'opacity-100 max-w-[140px] translate-x-0'">Booking</span>
                </div>
                <svg :class="sidebarToggle ? 'lg:opacity-0 lg:max-w-0 lg:pointer-events-none' : 'opacity-100'" 
                     class="w-3.5 h-3.5 text-gray-400 dark:text-gray-600 shrink-0 transition-all duration-300 ease-in-out" 
                     fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                </svg>
              </a>
            @endif
          </li>

          <!-- 8. Chat Client -->
          <li>
            @if($sidebarIsUnlocked)
              <a href="{{ url('/photographer/chat') }}" 
                 title="Pesan"
                 :class="sidebarToggle ? 'lg:justify-center lg:px-0 lg:gap-0' : 'px-3.5 gap-3 justify-between'"
                 class="relative flex items-center rounded-xl py-2.5 active:scale-95 transition-all duration-300 ease-in-out {{ request()->is('photographer/chat*') ? 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400 font-bold' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5' }}">
                <div class="flex items-center gap-3 min-w-0" :class="sidebarToggle ? 'lg:gap-0' : ''">
                  <div class="relative shrink-0">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                    </svg>
                    <!-- Mini Dot Indicator for Collapsed State -->
                    <span x-show="$store.chatNotifier && $store.chatNotifier.unreadCount > 0"
                          :class="sidebarToggle ? 'lg:block' : ''"
                          class="hidden absolute -top-1 -right-1 w-2.5 h-2.5 rounded-full bg-rose-500 ring-2 ring-white dark:ring-gray-900"
                          style="display: none;"></span>
                  </div>
                  <span class="overflow-hidden whitespace-nowrap transition-all duration-300 ease-in-out font-medium truncate"
                        :class="sidebarToggle ? 'lg:opacity-0 lg:max-w-0 lg:-translate-x-2 lg:pointer-events-none' : 'opacity-100 max-w-[140px] translate-x-0'">Pesan</span>
                </div>
                
                <!-- Real-Time Unread Count Badge in Sidebar -->
                <span x-show="$store.chatNotifier && $store.chatNotifier.unreadCount > 0" 
                      :class="sidebarToggle ? 'lg:opacity-0 lg:max-w-0 lg:pointer-events-none' : 'opacity-100'"
                      x-text="$store.chatNotifier.unreadCount > 99 ? '99+' : $store.chatNotifier.unreadCount"
                      class="px-2 py-0.5 text-[10px] font-black rounded-full bg-rose-500 text-white shadow-xs animate-pulse shrink-0 transition-all duration-300 ease-in-out"
                      style="display: none;">
                </span>
              </a>
            @else
              <a href="javascript:void(0)" 
                 @click.prevent="$dispatch('show-toast', { type: 'warning', message: 'Lengkapi profil 100% dan tunggu verifikasi Admin untuk membuka fitur Pesan Chat.' })"
                 title="Lengkapi profil 100% dan tunggu verifikasi Admin untuk membuka fitur Pesan Chat"
                 :class="sidebarToggle ? 'lg:justify-center lg:px-0 lg:gap-0' : 'px-3.5 gap-3 justify-between'"
                 class="flex items-center rounded-xl py-2.5 text-gray-400 dark:text-gray-600 opacity-40 cursor-not-allowed select-none transition-all duration-300 ease-in-out">
                <div class="flex items-center gap-3 min-w-0" :class="sidebarToggle ? 'lg:gap-0' : ''">
                  <svg class="w-5 h-5 shrink-0 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                  </svg>
                  <span class="overflow-hidden whitespace-nowrap transition-all duration-300 ease-in-out font-medium truncate"
                        :class="sidebarToggle ? 'lg:opacity-0 lg:max-w-0 lg:-translate-x-2 lg:pointer-events-none' : 'opacity-100 max-w-[140px] translate-x-0'">Pesan</span>
                </div>
                <svg :class="sidebarToggle ? 'lg:opacity-0 lg:max-w-0 lg:pointer-events-none' : 'opacity-100'" 
                     class="w-3.5 h-3.5 text-gray-400 dark:text-gray-600 shrink-0 transition-all duration-300 ease-in-out" 
                     fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                </svg>
              </a>
            @endif
          </li>

          <!-- 9. Laporan Keuangan -->
          <li>
            @if($sidebarIsUnlocked)
              <a href="{{ url('/photographer/reports') }}" 
                 title="Laporan Keuangan"
                 :class="sidebarToggle ? 'lg:justify-center lg:px-0 lg:gap-0' : 'px-3.5 gap-3'"
                 class="flex items-center rounded-xl py-2.5 active:scale-95 transition-all duration-300 ease-in-out {{ request()->is('photographer/reports*') ? 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400 font-bold' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                </svg>
                <span class="overflow-hidden whitespace-nowrap transition-all duration-300 ease-in-out font-medium"
                      :class="sidebarToggle ? 'lg:opacity-0 lg:max-w-0 lg:-translate-x-2 lg:pointer-events-none' : 'opacity-100 max-w-[200px] translate-x-0'">Laporan Keuangan</span>
              </a>
            @else
              <a href="javascript:void(0)" 
                 @click.prevent="$dispatch('show-toast', { type: 'warning', message: 'Lengkapi profil 100% dan tunggu verifikasi Admin untuk membuka fitur Laporan Keuangan.' })"
                 title="Lengkapi profil 100% dan tunggu verifikasi Admin untuk membuka fitur Laporan Keuangan"
                 :class="sidebarToggle ? 'lg:justify-center lg:px-0 lg:gap-0' : 'px-3.5 gap-3 justify-between'"
                 class="flex items-center rounded-xl py-2.5 text-gray-400 dark:text-gray-600 opacity-40 cursor-not-allowed select-none transition-all duration-300 ease-in-out">
                <div class="flex items-center gap-3 min-w-0" :class="sidebarToggle ? 'lg:gap-0' : ''">
                  <svg class="w-5 h-5 shrink-0 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                  </svg>
                  <span class="overflow-hidden whitespace-nowrap transition-all duration-300 ease-in-out font-medium truncate"
                        :class="sidebarToggle ? 'lg:opacity-0 lg:max-w-0 lg:-translate-x-2 lg:pointer-events-none' : 'opacity-100 max-w-[160px] translate-x-0'">Laporan Keuangan</span>
                </div>
                <svg :class="sidebarToggle ? 'lg:opacity-0 lg:max-w-0 lg:pointer-events-none' : 'opacity-100'" 
                     class="w-3.5 h-3.5 text-gray-400 dark:text-gray-600 shrink-0 transition-all duration-300 ease-in-out" 
                     fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                </svg>
              </a>
            @endif
          </li>

          <!-- 10. Event & Lomba Foto -->
          <li>
            @if($sidebarIsUnlocked)
              <a href="{{ route('public.contests.propose.history') }}" 
                 title="Pengajuan Lomba"
                 :class="sidebarToggle ? 'lg:justify-center lg:px-0 lg:gap-0' : 'px-3.5 gap-3'"
                 class="flex items-center rounded-xl py-2.5 active:scale-95 transition-all duration-300 ease-in-out {{ request()->is('lomba*') ? 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400 font-bold' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.982-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.483 2.244 9.774 2 12 2c2.226 0 4.517.244 6.75.721v1.515m0 0c.982.143 1.954.317 2.916.52a6.003 6.003 0 01-5.396 4.972m2.48-5.492V4.5c0 2.108-.966 3.99-2.48 5.228m0 0a6.01 6.01 0 01-4.75 0" />
                </svg>
                <span class="overflow-hidden whitespace-nowrap transition-all duration-300 ease-in-out font-medium"
                      :class="sidebarToggle ? 'lg:opacity-0 lg:max-w-0 lg:-translate-x-2 lg:pointer-events-none' : 'opacity-100 max-w-[200px] translate-x-0'">Pengajuan Lomba</span>
              </a>
            @else
              <a href="javascript:void(0)" 
                 @click.prevent="$dispatch('show-toast', { type: 'warning', message: 'Lengkapi profil 100% dan tunggu verifikasi Admin untuk membuka fitur Pengajuan Lomba.' })"
                 title="Lengkapi profil 100% dan tunggu verifikasi Admin untuk membuka fitur Pengajuan Lomba"
                 :class="sidebarToggle ? 'lg:justify-center lg:px-0 lg:gap-0' : 'px-3.5 gap-3 justify-between'"
                 class="flex items-center rounded-xl py-2.5 text-gray-400 dark:text-gray-600 opacity-40 cursor-not-allowed select-none transition-all duration-300 ease-in-out">
                <div class="flex items-center gap-3 min-w-0" :class="sidebarToggle ? 'lg:gap-0' : ''">
                  <svg class="w-5 h-5 shrink-0 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.982-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.483 2.244 9.774 2 12 2c2.226 0 4.517.244 6.75.721v1.515m0 0c.982.143 1.954.317 2.916.52a6.003 6.003 0 01-5.396 4.972m2.48-5.492V4.5c0 2.108-.966 3.99-2.48 5.228m0 0a6.01 6.01 0 01-4.75 0" />
                  </svg>
                  <span class="overflow-hidden whitespace-nowrap transition-all duration-300 ease-in-out font-medium truncate"
                        :class="sidebarToggle ? 'lg:opacity-0 lg:max-w-0 lg:-translate-x-2 lg:pointer-events-none' : 'opacity-100 max-w-[160px] translate-x-0'">Pengajuan Lomba</span>
                </div>
                <svg :class="sidebarToggle ? 'lg:opacity-0 lg:max-w-0 lg:pointer-events-none' : 'opacity-100'" 
                     class="w-3.5 h-3.5 text-gray-400 dark:text-gray-600 shrink-0 transition-all duration-300 ease-in-out" 
                     fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                </svg>
              </a>
            @endif
          </li>

          <!-- Tombol Logout -->
          <li class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
            <button type="button" 
                    title="Keluar Akun"
                    @click="$dispatch('open-confirm-modal', { title: 'Konfirmasi Keluar Akun', message: 'Apakah Anda yakin ingin keluar dari akun LensMatch?', actionUrl: '{{ route('logout') }}', method: 'POST', buttonText: 'Ya, Keluar Akun', iconType: 'logout', buttonClass: 'bg-red-600 hover:bg-red-700 text-white' })" 
                    :class="sidebarToggle ? 'lg:justify-center lg:px-0 lg:gap-0' : 'px-3.5 gap-3'"
                    class="w-full flex items-center rounded-xl py-2.5 text-red-600 hover:bg-red-50 dark:hover:bg-red-950/50 transition-all duration-300 ease-in-out font-bold active:scale-95 cursor-pointer">
              <svg class="w-5 h-5 shrink-0 text-rose-600" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
              </svg>
              <span class="overflow-hidden whitespace-nowrap transition-all duration-300 ease-in-out"
                    :class="sidebarToggle ? 'lg:opacity-0 lg:max-w-0 lg:-translate-x-2 lg:pointer-events-none' : 'opacity-100 max-w-[200px] translate-x-0'">Keluar</span>
            </button>
          </li>
        </ul>
      </div>
    </nav>
  </div>
</aside>
