<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'LensMatch — Platform Booking Fotografer & Komunitas')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo/lensmatch_logo_transparent_yellow.png') }}" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  </head>
  <body
    x-data="{ page: 'public', loaded: true, darkMode: false }"
    x-init="
      darkMode = JSON.parse(localStorage.getItem('darkMode'));
      $watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)))
    "
    :class="{'dark bg-gray-900 text-white': darkMode === true, 'bg-gray-50 text-gray-800': darkMode === false}"
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
  </body>
</html>
