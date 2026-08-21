<!doctype html>
<html lang="en">
  <head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'LensMatch — Platform Booking Fotografer & Komunitas')</title>
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
    x-data="{ page: 'public', loaded: true, darkMode: false }"
    x-init="
      darkMode = JSON.parse(localStorage.getItem('darkMode'));
      $watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)))
    "
    :class="{'dark bg-gray-900 text-white': darkMode === true, 'bg-white text-gray-800': darkMode === false}"
  >
    @include('partials.shared.preloader')
    @include('partials.public.navbar')

    <main class="min-h-screen">
      @yield('content')
    </main>

    @include('partials.shared.chat-widget')
    @include('partials.public.footer')

    <!-- Modern Scroll Reveal IntersectionObserver Script -->
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        const observerOptions = {
          root: null,
          rootMargin: '0px 0px -60px 0px',
          threshold: 0.08
        };

        const observer = new IntersectionObserver((entries) => {
          entries.forEach(entry => {
            if (entry.isIntersecting) {
              entry.target.classList.add('revealed');
            } else {
              entry.target.classList.remove('revealed');
            }
          });
        }, observerOptions);

        document.querySelectorAll('.reveal-on-scroll').forEach(el => observer.observe(el));
      });
    </script>
    @include('partials.shared.confirm-modal')
  @include('partials.shared.toast-notification')
</body>
</html>
