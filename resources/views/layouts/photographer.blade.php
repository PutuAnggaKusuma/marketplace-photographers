<!doctype html>
<html lang="en">
  <head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Fotografer Dashboard — Marketplace Fotografer')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo/lensmatch_logo_transparent_yellow.png') }}" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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

      <div class="relative flex flex-col flex-1 overflow-x-hidden overflow-y-auto">
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
</body>
</html>
