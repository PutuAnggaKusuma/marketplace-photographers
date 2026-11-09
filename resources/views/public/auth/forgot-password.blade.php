<!doctype html>
<html lang="id" class="h-full">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Lupa Kata Sandi — LensMatch</title>
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
        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white tracking-tight">Lupa Kata Sandi?</h1>
        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 leading-relaxed max-w-md mx-auto">
          Masukkan alamat email yang terdaftar untuk menerima tautan pemulihan kata sandi akun LensMatch Anda.
        </p>
      </div>

      <!-- Flash Status Message -->
      @if (session('status'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800/60 text-xs font-bold text-emerald-800 dark:text-emerald-300 space-y-2">
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
            <span>{{ session('status') }}</span>
          </div>
          @if(session('simulated_reset_url'))
            <div class="pt-2 border-t border-emerald-200/60 dark:border-emerald-800/60">
              <span class="text-[11px] text-gray-500 block mb-1">Simulasi Tautan Reset (Mode Uji):</span>
              <a href="{{ session('simulated_reset_url') }}" class="text-xs font-black text-amber-600 hover:text-amber-700 underline break-all">
                Klik Di Sini untuk Langsung Buat Sandi Baru
              </a>
            </div>
          @endif
        </div>
      @endif

      <!-- Error Validation Messages -->
      @if ($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800/60 text-xs font-bold text-rose-700 dark:text-rose-300">
          {{ $errors->first() }}
        </div>
      @endif

      <!-- Forgot Password Form -->
      <form action="{{ route('password.email') }}" method="POST" x-data="forgotPasswordApp()" @submit="submitForm($event)" novalidate class="space-y-5">
        @csrf

        <div>
          <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
            Alamat Email Akun <span class="text-rose-500">*</span>
          </label>
          <input type="email" 
                 name="email" 
                 x-ref="emailInput"
                 x-model="form.email"
                 @input="touched.email = true; validateEmail()"
                 @blur="touched.email = true; validateEmail()"
                 required 
                 autocomplete="email" 
                 placeholder="nama@email.com"
                 class="w-full px-4 py-3 text-xs font-medium text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 rounded-xl border transition shadow-2xs outline-none focus:ring-2"
                 :class="touched.email && errors.email ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-400 focus:border-amber-400'" />
          <p x-show="touched.email && errors.email" x-text="errors.email" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
        </div>

        <button type="submit" 
                class="w-full py-3.5 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs sm:text-sm rounded-xl shadow-xs transition duration-150 active:scale-98 cursor-pointer text-center">
          Kirim Tautan Reset Kata Sandi
        </button>
      </form>

      <script>
      function forgotPasswordApp() {
        return {
          form: {
            email: {!! json_encode(old('email', '')) !!}
          },
          touched: {
            email: {{ $errors->has('email') ? 'true' : 'false' }}
          },
          errors: {
            email: {!! json_encode($errors->first('email') ?: '') !!}
          },
          validateEmail() {
            const val = (this.form.email || '').trim();
            if (!val) {
              this.errors.email = 'Alamat email wajib diisi.';
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
              this.errors.email = 'Format alamat email tidak valid.';
            } else {
              this.errors.email = '';
            }
          },
          submitForm(e) {
            this.touched.email = true;
            this.validateEmail();
            if (this.errors.email) {
              e.preventDefault();
              this.$refs.emailInput?.focus();
              return false;
            }
          }
        };
      }
      </script>

      <!-- Back to Signin -->
      <div class="text-center pt-2 border-t border-gray-100 dark:border-gray-700/60">
        <a href="{{ route('login') }}" class="text-xs font-bold text-gray-600 dark:text-gray-400 hover:text-amber-600 dark:hover:text-amber-400 transition inline-flex items-center gap-1.5">
          Kembali ke Halaman Masuk
        </a>
      </div>

    </div>

  </body>
</html>
