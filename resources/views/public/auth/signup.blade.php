<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Daftar Akun Baru — LensMatch</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>
  <body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-white">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-md w-full space-y-8 bg-white dark:bg-gray-800 p-8 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-xl">
        <div class="text-center space-y-2">
          <a href="{{ url('/') }}" class="inline-block">
            <img src="{{ asset('images/logo/lensmatch_logo_transparent_yellow.png') }}" class="h-10 w-auto mx-auto" alt="LensMatch Logo" />
          </a>
          <h2 class="text-xl font-bold">Buat Akun Baru</h2>
          <p class="text-xs text-gray-500">Daftar sebagai Client atau Fotografer</p>
        </div>

        @if (session('error'))
          <div class="p-3.5 rounded-xl bg-red-50 dark:bg-red-950/60 border border-red-200 dark:border-red-800 text-xs font-semibold text-red-800 dark:text-red-300">
            {{ session('error') }}
          </div>
        @endif

        <form action="{{ route('register') }}" method="POST" class="space-y-4">
          @csrf
          <div>
            <label class="block text-xs font-semibold mb-1 text-gray-700 dark:text-gray-300">Nama Lengkap</label>
            <input type="text" name="nama" value="{{ old('nama') }}" required placeholder="Masukkan nama lengkap Anda" class="w-full px-4 py-2.5 text-xs rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 focus:ring-2 focus:ring-amber-400 focus:outline-none" />
            @error('nama')
              <p class="text-[11px] font-semibold text-red-500 mt-1">{{ $message }}</p>
            @enderror
          </div>

          <div>
            <label class="block text-xs font-semibold mb-1 text-gray-700 dark:text-gray-300">Daftar Sebagai</label>
            <div class="relative">
              <select name="role" required class="w-full pl-4 pr-9 py-2.5 text-xs rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 focus:ring-2 focus:ring-amber-400 focus:outline-none appearance-none cursor-pointer">
                <option value="client" {{ old('role') === 'client' ? 'selected' : '' }}>Client / Pencari Fotografer</option>
                <option value="photographer" {{ old('role') === 'photographer' ? 'selected' : '' }}>Fotografer / Penyedia Jasa</option>
              </select>
              <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
              </div>
            </div>
            @error('role')
              <p class="text-[11px] font-semibold text-red-500 mt-1">{{ $message }}</p>
            @enderror
          </div>

          <div>
            <label class="block text-xs font-semibold mb-1 text-gray-700 dark:text-gray-300">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required placeholder="nama@email.com" class="w-full px-4 py-2.5 text-xs rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 focus:ring-2 focus:ring-amber-400 focus:outline-none" />
            @error('email')
              <p class="text-[11px] font-semibold text-red-500 mt-1">{{ $message }}</p>
            @enderror
          </div>

          <div>
            <label class="block text-xs font-semibold mb-1 text-gray-700 dark:text-gray-300">Password</label>
            <input type="password" name="password" required placeholder="Minimal 8 karakter" class="w-full px-4 py-2.5 text-xs rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-900 focus:ring-2 focus:ring-amber-400 focus:outline-none" />
            @error('password')
              <p class="text-[11px] font-semibold text-red-500 mt-1">{{ $message }}</p>
            @enderror
          </div>

          <button type="submit" class="w-full py-3 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-sm rounded-xl transition shadow-md">
            Daftar Sekarang
          </button>
        </form>

        <p class="text-center text-xs text-gray-500">
          Sudah punya akun? <a href="{{ url('/login') }}" class="text-brand-500 font-semibold hover:underline">Masuk</a>
        </p>
      </div>
    </div>
  </body>
</html>
