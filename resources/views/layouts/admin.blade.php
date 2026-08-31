<!doctype html>
<html lang="en">
  <head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Admin Dashboard — Marketplace Fotografer')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo/lensmatch_logo_transparent_yellow.png') }}" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
</head>
  <body
    x-data="{ page: 'admin', loaded: true, darkMode: false, stickyMenu: false, sidebarToggle: false }"
    x-init="
      darkMode = JSON.parse(localStorage.getItem('darkMode'));
      $watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)))
    "
    :class="{'dark bg-gray-900': darkMode === true}"
  >
    @include('partials.shared.preloader')

    <div class="flex h-screen overflow-hidden">
      @include('partials.admin.sidebar')

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

      <div class="relative flex flex-col flex-1 overflow-x-hidden overflow-y-auto bg-gray-50/50 dark:bg-gray-900/50">
        @include('partials.shared.header')

        <main>
          <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6 space-y-6">
            @yield('content')
          </div>
        </main>
      </div>
    </div>
    @include('partials.shared.confirm-modal')
  @include('partials.shared.toast-notification')
  @include('partials.shared.dashboard-live-filter-js')
  @include('partials.shared.ajax-pagination-engine')
</body>
</html>
