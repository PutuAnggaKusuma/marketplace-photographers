<header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-gray-100 dark:bg-gray-900/95 dark:border-gray-800 shadow-xs">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between h-16">
      <!-- Logo -->
      <div class="flex items-center gap-3">
        <a href="{{ url('/') }}" class="flex items-center gap-2">
          <img src="{{ asset('images/logo/lensmatch_logo_transparent_yellow.png') }}" class="h-9 w-auto" alt="LensMatch Logo" />
        </a>
      </div>

      <!-- Navigation Links -->
      <nav class="hidden md:flex items-center gap-8 font-semibold text-sm text-gray-700 dark:text-gray-300">
        <a href="{{ url('/') }}" class="hover:text-amber-500 transition">Beranda</a>
        <a href="{{ url('/fotografer') }}" class="hover:text-amber-500 transition">Cari Fotografer</a>
        <a href="{{ url('/forum') }}" class="hover:text-amber-500 transition">Komunitas Forum</a>
        <a href="{{ url('/lomba') }}" class="hover:text-amber-500 transition">Lomba Foto</a>
        <a href="{{ url('/e-learning') }}" class="hover:text-amber-500 transition">E-Learning</a>
      </nav>

      <!-- Auth Buttons -->
      <div class="flex items-center gap-3">
        <a href="{{ url('/login') }}" class="text-sm font-semibold text-gray-700 hover:text-amber-500 dark:text-gray-300 px-3 py-2">Masuk</a>
        <a href="{{ url('/register') }}" class="px-5 py-2.5 text-sm font-bold text-gray-900 bg-amber-400 hover:bg-amber-500 rounded-xl shadow-xs transition">Daftar</a>
      </div>
    </div>
  </div>
</header>
