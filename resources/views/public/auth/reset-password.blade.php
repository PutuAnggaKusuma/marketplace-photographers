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
      <form action="{{ route('password.update') }}" method="POST" x-data="resetPasswordApp()" @submit="submitForm($event)" novalidate class="space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div>
          <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
            Alamat Email <span class="text-rose-500">*</span>
          </label>
          <input type="email" 
                 name="email" 
                 value="{{ old('email', $email) }}" 
                 readonly
                 class="w-full px-4 py-3 text-xs font-medium text-gray-800 dark:text-gray-300 bg-gray-100 dark:bg-gray-900/80 rounded-xl border border-gray-200 dark:border-gray-700 focus:outline-none transition cursor-not-allowed" />
        </div>

        <div>
          <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
            Kata Sandi Baru <span class="text-rose-500">*</span>
          </label>
          <div class="relative">
            <input :type="showPass ? 'text' : 'password'" 
                   name="password" 
                   x-ref="passwordInput"
                   x-model="form.password"
                   @input="touched.password = true; validatePassword()"
                   @blur="touched.password = true; validatePassword()"
                   autocomplete="new-password" 
                   placeholder="Minimal 8 karakter"
                   class="w-full pl-4 pr-11 py-3 text-xs font-medium text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 rounded-xl border transition shadow-2xs outline-none focus:ring-2"
                   :class="touched.password && errors.password ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-400 focus:border-amber-400'" />
            <button type="button" 
                    @click="showPass = !showPass" 
                    class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition cursor-pointer">
              <svg x-show="!showPass" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
              <svg x-show="showPass" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
            </button>
          </div>
          <p x-show="touched.password && errors.password" x-text="errors.password" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
        </div>

        <div>
          <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
            Konfirmasi Kata Sandi Baru <span class="text-rose-500">*</span>
          </label>
          <div class="relative">
            <input :type="showPassConf ? 'text' : 'password'" 
                   name="password_confirmation" 
                   x-ref="confirmPasswordInput"
                   x-model="form.password_confirmation"
                   @input="touched.password_confirmation = true; validateConfirmation()"
                   @blur="touched.password_confirmation = true; validateConfirmation()"
                   autocomplete="new-password" 
                   placeholder="Ulangi kata sandi baru"
                   class="w-full pl-4 pr-11 py-3 text-xs font-medium text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 rounded-xl border transition shadow-2xs outline-none focus:ring-2"
                   :class="touched.password_confirmation && errors.password_confirmation ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-400 focus:border-amber-400'" />
            <button type="button" 
                    @click="showPassConf = !showPassConf" 
                    class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition cursor-pointer">
              <svg x-show="!showPassConf" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
              <svg x-show="showPassConf" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
            </button>
          </div>
          <p x-show="touched.password_confirmation && errors.password_confirmation" x-text="errors.password_confirmation" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
        </div>

        <button type="submit" 
                class="w-full py-3.5 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs sm:text-sm rounded-xl shadow-xs transition duration-150 active:scale-98 cursor-pointer text-center mt-2">
          Perbaharui Data
        </button>
      </form>

      <script>
      function resetPasswordApp() {
        return {
          showPass: false,
          showPassConf: false,
          form: {
            password: '',
            password_confirmation: ''
          },
          touched: {
            password: {{ $errors->has('password') ? 'true' : 'false' }},
            password_confirmation: {{ $errors->has('password_confirmation') ? 'true' : 'false' }}
          },
          errors: {
            password: {!! json_encode($errors->first('password') ?: '') !!},
            password_confirmation: {!! json_encode($errors->first('password_confirmation') ?: '') !!}
          },
          validatePassword() {
            const val = this.form.password || '';
            if (!val) {
              this.errors.password = 'Kata sandi baru wajib diisi.';
            } else if (val.length < 8) {
              this.errors.password = 'Kata sandi baru minimal 8 karakter.';
            } else {
              this.errors.password = '';
            }
            if (this.touched.password_confirmation) {
              this.validateConfirmation();
            }
          },
          validateConfirmation() {
            const conf = this.form.password_confirmation || '';
            if (!conf) {
              this.errors.password_confirmation = 'Konfirmasi kata sandi baru wajib diisi.';
            } else if (conf !== this.form.password) {
              this.errors.password_confirmation = 'Konfirmasi kata sandi baru tidak cocok.';
            } else {
              this.errors.password_confirmation = '';
            }
          },
          submitForm(e) {
            this.touched.password = true;
            this.touched.password_confirmation = true;
            this.validatePassword();
            this.validateConfirmation();
            if (this.errors.password || this.errors.password_confirmation) {
              e.preventDefault();
              if (this.errors.password) this.$refs.passwordInput?.focus();
              else if (this.errors.password_confirmation) this.$refs.confirmPasswordInput?.focus();
              return false;
            }
          }
        };
      }
      </script>

      <!-- Back to Signin -->
      <div class="text-center pt-2 border-t border-gray-100 dark:border-gray-700/60">
        <a href="{{ route('login') }}" class="text-xs font-bold text-gray-600 dark:text-gray-400 hover:text-amber-600 dark:hover:text-amber-400 transition inline-flex items-center gap-1.5">
          Batal & Kembali ke Halaman Masuk
        </a>
      </div>

    </div>

  </body>
</html>
