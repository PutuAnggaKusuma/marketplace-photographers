<!doctype html>
<html lang="id" class="h-full">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Atur Ulang Kata Sandi — LensMatch</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo/lensmatch_logo_transparent_yellow.png') }}" />
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>
  <body class="min-h-screen bg-gradient-to-b from-amber-50/80 via-[#f6f4f0] to-[#edf0f4] text-gray-900 font-sans antialiased flex items-center justify-center p-4 sm:p-6 lg:p-8 relative">

    <div class="max-w-2xl w-full space-y-8 bg-white dark:bg-gray-800 p-8 sm:p-12 lg:p-14 rounded-3xl border border-gray-200/80 dark:border-gray-700/80 shadow-2xl relative z-10 animate-auth-card">
      
      <!-- Logo & Header -->
      <div class="text-center space-y-3.5">
        <a href="{{ url('/') }}" class="inline-block transition hover:scale-105 duration-200">
          <img src="{{ asset('images/logo/lensmatch_logo_transparent_yellow.png') }}" alt="LensMatch Logo" class="h-14 sm:h-16 w-auto mx-auto drop-shadow-sm" />
        </a>
        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white tracking-tight">Atur Kata Sandi Baru</h1>
        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 leading-relaxed max-w-md mx-auto">
          Buat kata sandi baru yang kuat minimal 8 karakter untuk mengamankan akun LensMatch Anda.
        </p>
      </div>

      <!-- Error Validation Messages -->
      @if ($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800/60 text-xs font-bold text-rose-700 dark:text-rose-300">
          {{ $errors->first() }}
        </div>
      @endif

      <!-- Reset Password Form -->
      <form action="{{ route('password.update') }}" method="POST" class="space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div>
          <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
            Alamat Email <span class="text-rose-500">*</span>
          </label>
          <input type="email" 
                 name="email" 
                 value="{{ old('email', $email) }}" 
                 required 
                 readonly
                 class="w-full px-4 py-3 text-xs font-medium text-gray-800 dark:text-gray-300 bg-gray-100 dark:bg-gray-900/80 rounded-xl border border-gray-200 dark:border-gray-700 focus:outline-none transition cursor-not-allowed" />
        </div>

        <div>
          <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
            Kata Sandi Baru <span class="text-rose-500">*</span>
          </label>
          <input type="password" 
                 name="password" 
                 required 
                 autocomplete="new-password" 
                 placeholder="Minimal 8 karakter"
                 class="w-full px-4 py-3 text-xs font-medium text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition" />
        </div>

        <div>
          <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
            Konfirmasi Kata Sandi Baru <span class="text-rose-500">*</span>
          </label>
          <input type="password" 
                 name="password_confirmation" 
                 required 
                 autocomplete="new-password" 
                 placeholder="Ulangi kata sandi baru"
                 class="w-full px-4 py-3 text-xs font-medium text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition" />
        </div>

        <button type="submit" 
                class="w-full py-3.5 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs sm:text-sm rounded-xl shadow-xs transition duration-150 active:scale-98 cursor-pointer text-center mt-2">
          Perbaharui Data
        </button>
      </form>

      <!-- Back to Signin -->
      <div class="text-center pt-2 border-t border-gray-100 dark:border-gray-700/60">
        <a href="{{ route('login') }}" class="text-xs font-bold text-gray-600 dark:text-gray-400 hover:text-amber-600 dark:hover:text-amber-400 transition inline-flex items-center gap-1.5">
          Batal & Kembali ke Halaman Masuk
        </a>
      </div>

    </div>

  </body>
</html>
