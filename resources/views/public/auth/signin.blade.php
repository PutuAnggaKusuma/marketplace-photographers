<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Masuk Akun — LensMatch</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>
  <body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-white">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-md w-full space-y-8 bg-white dark:bg-gray-800 p-8 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-xl">
        <div class="text-center space-y-2">
          <a href="{{ url('/') }}" class="inline-block">
            <img src="{{ asset('images/logo/lensmatch_logo_transparent_yellow.png') }}" class="h-10 w-auto mx-auto" alt="LensMatch Logo" />
          </a>
          <h2 class="text-xl font-bold">Masuk ke Akun Anda</h2>
          <p class="text-xs text-gray-500">Silakan masukkan email dan kata sandi Anda untuk masuk</p>
        </div>

        @if (session('success'))
          <div class="p-3.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-xs font-semibold text-emerald-800 dark:text-emerald-300">
            {{ session('success') }}
          </div>
        @endif

        @if (session('error'))
          <div class="p-3.5 rounded-xl bg-red-50 dark:bg-red-950/60 border border-red-200 dark:border-red-800 text-xs font-semibold text-red-800 dark:text-red-300">
            {{ session('error') }}
          </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-4">
          @csrf
          <div>
            <label class="block text-xs font-semibold mb-1 text-gray-700 dark:text-gray-300">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required placeholder="nama@email.com" class="w-full px-4 py-2.5 text-xs rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 focus:ring-2 focus:ring-amber-400 focus:outline-none" />
            @error('email')
              <p class="text-[11px] font-semibold text-red-500 mt-1">{{ $message }}</p>
            @enderror
          </div>

          <div>
            <label class="block text-xs font-semibold mb-1 text-gray-700 dark:text-gray-300">Password</label>
            <input type="password" name="password" required placeholder="••••••••" class="w-full px-4 py-2.5 text-xs rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 focus:ring-2 focus:ring-amber-400 focus:outline-none" />
            @error('password')
              <p class="text-[11px] font-semibold text-red-500 mt-1">{{ $message }}</p>
            @enderror
          </div>

          <button type="submit" class="w-full py-3 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-sm rounded-xl transition shadow-md">
            Masuk Akun
          </button>
        </form>

        <p class="text-center text-xs text-gray-500">
          Belum punya akun? <a href="{{ url('/register') }}" class="text-brand-500 font-semibold hover:underline">Daftar sekarang</a>
        </p>
      </div>
    </div>
  </body>
</html>
