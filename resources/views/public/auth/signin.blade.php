@php
  $isRegister = request()->is('register');
@endphp
<!doctype html>
<html lang="id" class="h-full">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $isRegister ? 'Daftar Akun Baru — LensMatch' : 'Masuk ke Akun — LensMatch' }}</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo/lensmatch_logo_transparent_yellow.png') }}" />
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
      [x-cloak] { display: none !important; }

      /* ========================================================
         LensMatch Auth System: 2 Distinct Animations
         1. Entrance Animation (Animasi 2): Smooth entrance on page load
         2. Slide Toggle Animation (Animasi 1): Smooth horizontal track sliding
         ======================================================== */

      /* ANIMASI 2: Smooth Entrance on Page Load */
      @keyframes authCardEntrance {
        0% {
          opacity: 0;
          transform: translateY(16px) scale(0.992);
        }
        100% {
          opacity: 1;
          transform: translateY(0) scale(1);
        }
      }

      @keyframes ambientEntrance {
        0% {
          opacity: 0;
          transform: translateY(14px);
        }
        100% {
          opacity: 0.6;
          transform: translateY(0);
        }
      }

      .animate-auth-card {
        animation: authCardEntrance 0.55s cubic-bezier(0.16, 1, 0.3, 1) both;
        will-change: opacity, transform;
      }

      .animate-ambient-enter {
        animation: ambientEntrance 0.65s cubic-bezier(0.16, 1, 0.3, 1) both;
        will-change: opacity, transform;
      }

      /* ANIMASI 1: Desktop Horizontal Sliding Track (lg: >= 1024px) */
      @media (min-width: 1024px) {
        .auth-panel-form {
          transition: transform 0.65s cubic-bezier(0.16, 1, 0.3, 1);
          transform: translate3d(0, 0, 0);
          will-change: transform;
        }

        .auth-panel-visual {
          transition: transform 0.65s cubic-bezier(0.16, 1, 0.3, 1);
          transform: translate3d(0, 0, 0);
          will-change: transform;
        }

        /* Register Mode: Form slides right (+138%), Visual card slides left (-96%) */
        .auth-mode-register .auth-panel-form {
          transform: translate3d(138%, 0, 0);
        }

        .auth-mode-register .auth-panel-visual {
          transform: translate3d(-96%, 0, 0);
        }
      }

      /* Form Cross-Fade and Soft Horizontal Shift */
      .auth-form-login,
      .auth-form-register {
        transition: opacity 0.35s ease-out, transform 0.35s ease-out;
        will-change: opacity, transform;
      }

      .auth-mode-login .auth-form-login {
        opacity: 1;
        pointer-events: auto;
        position: relative;
        z-index: 10;
        transform: translate3d(0, 0, 0);
      }

      .auth-mode-login .auth-form-register {
        opacity: 0;
        pointer-events: none;
        position: absolute;
        inset: 0;
        z-index: 0;
        transform: translate3d(24px, 0, 0);
      }

      .auth-mode-register .auth-form-login {
        opacity: 0;
        pointer-events: none;
        position: absolute;
        inset: 0;
        z-index: 0;
        transform: translate3d(-24px, 0, 0);
      }

      .auth-mode-register .auth-form-register {
        opacity: 1;
        pointer-events: auto;
        position: relative;
        z-index: 10;
        transform: translate3d(0, 0, 0);
      }

      /* Visual Card Image Cross-Fade */
      .auth-visual-login,
      .auth-visual-register {
        transition: opacity 0.55s ease-in-out;
        will-change: opacity;
      }

      .auth-mode-login .auth-visual-login {
        opacity: 1;
        pointer-events: auto;
      }

      .auth-mode-login .auth-visual-register {
        opacity: 0;
        pointer-events: none;
      }

      .auth-mode-register .auth-visual-login {
        opacity: 0;
        pointer-events: none;
      }

      .auth-mode-register .auth-visual-register {
        opacity: 1;
        pointer-events: auto;
      }

      /* Ambient Background SVG Illustrations */
      .auth-ambient-login,
      .auth-ambient-register {
        transition: opacity 0.45s ease-out, transform 0.45s ease-out;
        will-change: opacity, transform;
      }

      .auth-mode-login .auth-ambient-login {
        opacity: 0.6;
        pointer-events: auto;
        transform: translate3d(0, 0, 0);
      }

      .auth-mode-login .auth-ambient-register {
        opacity: 0;
        pointer-events: none;
        transform: translate3d(0, 12px, 0);
      }

      .auth-mode-register .auth-ambient-login {
        opacity: 0;
        pointer-events: none;
        transform: translate3d(0, 12px, 0);
      }

      .auth-mode-register .auth-ambient-register {
        opacity: 0.6;
        pointer-events: auto;
        transform: translate3d(0, 0, 0);
      }
    </style>
      <script>
      function authHandler() {
        return {
          mode: '{{ $isRegister ? 'register' : 'login' }}',
          showPassLogin: false,
          showPassRegister: false,
          selectedRole: '{{ old('role', 'client') }}',
          selectedRoleLabel: '{{ old('role') === 'photographer' ? 'Fotografer (Penyedia Jasa)' : 'Client (Pencari Fotografer)' }}',
          roleDropdownOpen: false,

          // Real-time Login Form State & Validation
          loginForm: {
            email: {!! json_encode(old('email', '')) !!},
            password: '',
          },
          loginTouched: {
            email: {{ $errors->has('email') ? 'true' : 'false' }},
            password: {{ $errors->has('password') ? 'true' : 'false' }},
          },
          loginErrors: {
            email: {!! json_encode($errors->first('email') ?: '') !!},
            password: {!! json_encode($errors->first('password') ?: '') !!},
          },
          validateLoginEmail() {
            const val = (this.loginForm.email || '').trim();
            if (!val) {
              this.loginErrors.email = 'Email wajib diisi.';
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
              this.loginErrors.email = 'Format alamat email tidak valid.';
            } else {
              this.loginErrors.email = '';
            }
          },
          validateLoginPassword() {
            const val = this.loginForm.password || '';
            if (!val) {
              this.loginErrors.password = 'Kata sandi wajib diisi.';
            } else {
              this.loginErrors.password = '';
            }
          },
          submitLogin(e) {
            this.loginTouched.email = true;
            this.loginTouched.password = true;
            this.validateLoginEmail();
            this.validateLoginPassword();
            if (this.loginErrors.email || this.loginErrors.password) {
              e.preventDefault();
              if (this.loginErrors.email) {
                this.$refs.loginEmail?.focus();
              } else if (this.loginErrors.password) {
                this.$refs.loginPassword?.focus();
              }
              return false;
            }
          },

          // Real-time Register Form State & Validation
          regForm: {
            nama: {!! json_encode(old('nama', '')) !!},
            nomor_telepon: {!! json_encode(old('nomor_telepon', '')) !!},
            email: {!! json_encode(old('email', '')) !!},
            password: '',
          },
          regTouched: {
            nama: {{ $errors->has('nama') ? 'true' : 'false' }},
            nomor_telepon: {{ $errors->has('nomor_telepon') ? 'true' : 'false' }},
            email: {{ $errors->has('email') ? 'true' : 'false' }},
            password: {{ $errors->has('password') ? 'true' : 'false' }},
          },
          regErrors: {
            nama: {!! json_encode($errors->first('nama') ?: '') !!},
            nomor_telepon: {!! json_encode($errors->first('nomor_telepon') ?: '') !!},
            email: {!! json_encode($errors->first('email') ?: '') !!},
            password: {!! json_encode($errors->first('password') ?: '') !!},
          },
          validateRegisterNama() {
            const val = (this.regForm.nama || '').trim();
            if (!val) {
              this.regErrors.nama = 'Nama lengkap wajib diisi.';
            } else if (val.length < 3) {
              this.regErrors.nama = 'Nama lengkap minimal 3 karakter.';
            } else {
              this.regErrors.nama = '';
            }
          },
          validateRegisterPhone() {
            // Strict numeric only (hanya angka murni)
            this.regForm.nomor_telepon = (this.regForm.nomor_telepon || '').replace(/\D/g, '');
            const raw = this.regForm.nomor_telepon;
            if (!raw) {
              this.regErrors.nomor_telepon = 'Nomor WhatsApp wajib diisi.';
              return;
            }
            if (raw.length < 9) {
              this.regErrors.nomor_telepon = 'Nomor WhatsApp minimal 9 digit angka.';
            } else if (raw.length > 20) {
              this.regErrors.nomor_telepon = 'Nomor WhatsApp maksimal 20 digit angka.';
            } else {
              this.regErrors.nomor_telepon = '';
            }
          },
          validateRegisterEmail() {
            const val = (this.regForm.email || '').trim();
            if (!val) {
              this.regErrors.email = 'Alamat email wajib diisi.';
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
              this.regErrors.email = 'Format alamat email tidak valid.';
            } else {
              this.regErrors.email = '';
            }
          },
          validateRegisterPassword() {
            const val = this.regForm.password || '';
            if (!val) {
              this.regErrors.password = 'Kata sandi wajib diisi.';
            } else if (val.length < 8) {
              this.regErrors.password = 'Kata sandi minimal 8 karakter.';
            } else {
              this.regErrors.password = '';
            }
          },
          submitRegister(e) {
            this.regTouched.nama = true;
            this.regTouched.nomor_telepon = true;
            this.regTouched.email = true;
            this.regTouched.password = true;
            this.validateRegisterNama();
            this.validateRegisterPhone();
            this.validateRegisterEmail();
            this.validateRegisterPassword();

            if (this.regErrors.nama || this.regErrors.nomor_telepon || this.regErrors.email || this.regErrors.password) {
              e.preventDefault();
              if (this.regErrors.nama) {
                this.$refs.regNama?.focus();
              } else if (this.regErrors.nomor_telepon) {
                this.$refs.regPhone?.focus();
              } else if (this.regErrors.email) {
                this.$refs.regEmail?.focus();
              } else if (this.regErrors.password) {
                this.$refs.regPassword?.focus();
              }
              return false;
            }
          },
          setMode(newMode) {
            this.mode = newMode;
            const searchParams = window.location.search;
            if (newMode === 'register') {
              window.history.pushState({}, '', '/register' + searchParams);
              document.title = 'Daftar Akun Baru — LensMatch';
            } else {
              window.history.pushState({}, '', '/login' + searchParams);
              document.title = 'Masuk ke Akun — LensMatch';
            }
          },
          init() {
            window.addEventListener('popstate', () => {
              this.mode = window.location.pathname.includes('register') ? 'register' : 'login';
              document.title = this.mode === 'register' ? 'Daftar Akun Baru — LensMatch' : 'Masuk ke Akun — LensMatch';
            });
          }
        };
      }
    </script>
  </head>
  <body class="min-h-screen bg-gradient-to-b from-amber-50/80 via-[#f6f4f0] to-[#edf0f4] text-gray-900 font-sans antialiased flex items-center justify-center p-3 sm:p-6 lg:p-10 relative overflow-hidden {{ $isRegister ? 'auth-mode-register' : 'auth-mode-login' }}"
        x-data="authHandler()"
        :class="{ 'auth-mode-register': mode === 'register', 'auth-mode-login': mode === 'login' }">

    <!-- Background Ambient Illustrations (Strictly 1 SVG per Mode, Bottom-Positioned) -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0 animate-ambient-enter" aria-hidden="true">
      
      <!-- 1. LOGIN MODE: ONLY lensmatch-login.svg (Bottom-Left) -->
      <div class="absolute bottom-2 left-2 sm:left-4 lg:left-8 z-0 auth-ambient-login">
        <img src="{{ asset('images/login-and-signup/lensmatch-login.svg') }}" 
             alt="Login Illustration" 
             class="w-36 sm:w-44 lg:w-56 xl:w-64 h-auto max-h-[260px] object-contain select-none drop-shadow-sm" />
      </div>

      <!-- 2. REGISTER MODE: ONLY lensmatch-register.svg (Bottom-Right) -->
      <div class="absolute bottom-2 right-2 sm:right-4 lg:right-8 z-0 auth-ambient-register">
        <img src="{{ asset('images/login-and-signup/lensmatch-register.svg') }}" 
             alt="Register Illustration" 
             class="w-36 sm:w-44 lg:w-56 xl:w-64 h-auto max-h-[260px] object-contain select-none drop-shadow-sm" />
      </div>

    </div>

    <!-- Reusable SVG Definitions for Havenix Shapes (Pre-rendered for GPU Acceleration) -->
    <svg class="absolute w-0 h-0 pointer-events-none" aria-hidden="true">
      <defs>
        <!-- Standard Right Havenix Shape (Login Mode) -->
        <clipPath id="havenix-shape-right" clipPathUnits="objectBoundingBox">
          <path d="
            M 0.22,0
            L 0.90,0
            C 0.96,0 1,0.04 1,0.08
            L 1,0.78
            C 1,0.83 0.96,0.87 0.91,0.88
            C 0.86,0.89 0.83,0.93 0.82,0.98
            C 0.815,0.995 0.805,1 0.78,1
            L 0.10,1
            C 0.04,1 0,0.96 0,0.92
            L 0,0.22
            C 0,0.17 0.04,0.13 0.09,0.12
            C 0.14,0.11 0.17,0.07 0.18,0.02
            C 0.185,0.005 0.195,0 0.22,0
            Z
          " />
        </clipPath>

        <!-- Mirrored Left Havenix Shape (Register Mode) -->
        <clipPath id="havenix-shape-left" clipPathUnits="objectBoundingBox">
          <path d="
            M 0.78,0
            L 0.10,0
            C 0.04,0 0,0.04 0,0.08
            L 0,0.78
            C 0,0.83 0.04,0.87 0.09,0.88
            C 0.14,0.89 0.17,0.93 0.18,0.98
            C 0.185,0.995 0.195,1 0.22,1
            L 0.90,1
            C 0.96,1 1,0.96 1,0.92
            L 1,0.22
            C 1,0.17 0.96,0.13 0.91,0.12
            C 0.86,0.11 0.83,0.07 0.82,0.02
            C 0.815,0.005 0.805,0 0.78,0
            Z
          " />
        </clipPath>
      </defs>
    </svg>

    <!-- Main Stationary Floating White Card Container -->
    <div class="w-full max-w-7xl bg-white rounded-[36px] sm:rounded-[44px] shadow-[0_25px_70px_rgba(0,0,0,0.07)] border border-gray-100/90 px-6 sm:px-10 lg:px-14 py-8 sm:py-12 lg:py-14 relative z-10 overflow-hidden animate-auth-card">
      
      <!-- Inner Relative Container with Balanced Proportions -->
      <div class="relative w-full min-h-[640px] flex items-center justify-between">

        <!-- PANEL 1: FORM COLUMN (Smooth Hardware-Accelerated Sliding Track - 42% width) -->
        <div class="w-full lg:w-[42%] z-20 auth-panel-form">

          <div class="w-full max-w-[440px] mx-auto relative min-h-[580px]">

            <!-- ==================== SIGN IN (LOGIN) FORM ==================== -->
            <div class="w-full space-y-6 auth-form-login">

              <!-- Brand Logo & Heading (Centered - Title in Solid Black text-gray-900) -->
              <div class="space-y-3.5 text-center">
                <div class="flex items-center justify-center">
                  <a href="{{ url('/') }}" class="inline-flex items-center gap-2 group">
                    <img src="{{ asset('images/logo/lensmatch_logo_transparent_yellow.png') }}" 
                         alt="LensMatch Logo" 
                         class="h-10 sm:h-11 w-auto group-hover:scale-105 transition duration-300 drop-shadow-xs" />
                    <span class="text-base sm:text-lg font-bold tracking-tight text-gray-800">LensMatch.</span>
                  </a>
                </div>

                <div class="space-y-1.5">
                  <h1 class="text-3xl sm:text-[34px] font-black text-gray-900 tracking-tight leading-tight">
                    Selamat Datang
                  </h1>
                  <p class="text-xs sm:text-sm text-gray-500 font-medium">
                    Silakan masuk untuk mengelola pesanan & sesi foto Anda
                  </p>
                </div>
              </div>

              <!-- Session Alerts -->
              @if (session('success'))
                <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-xs font-bold text-emerald-800 flex items-center gap-2">
                  <svg class="w-4 h-4 shrink-0 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                  <span>{{ session('success') }}</span>
                </div>
              @endif

              @if (session('error'))
                <div class="p-3.5 rounded-xl bg-red-50 border border-red-200 text-xs font-bold text-red-800 flex items-center gap-2">
                  <svg class="w-4 h-4 shrink-0 text-red-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                  <span>{{ session('error') }}</span>
                </div>
              @endif

              <!-- Sign In Form -->
              <form action="{{ route('login') }}" method="POST" @submit="submitLogin($event)" novalidate class="space-y-4 pt-1">
                @csrf
                @if(request()->filled('redirect'))
                  <input type="hidden" name="redirect" value="{{ request('redirect') }}">
                @endif
                
                <!-- Email -->
                <div class="space-y-1.5">
                  <label class="block text-xs font-bold text-gray-600">
                    Email <span class="text-rose-500">*</span>
                  </label>
                  <input type="email" 
                         name="email" 
                         x-ref="loginEmail"
                         x-model="loginForm.email"
                         @input="loginTouched.email = true; validateLoginEmail()"
                         @blur="loginTouched.email = true; validateLoginEmail()"
                         placeholder="nama@email.com" 
                         class="w-full px-4 py-3.5 text-xs sm:text-sm rounded-xl border bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 transition shadow-2xs font-medium"
                         :class="loginErrors.email && loginTouched.email ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 focus:ring-amber-400 focus:border-amber-400'" />
                  <p x-show="loginErrors.email && loginTouched.email" x-text="loginErrors.email" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
                </div>

                <!-- Password -->
                <div class="space-y-1.5">
                  <label class="block text-xs font-bold text-gray-600">
                    Kata Sandi <span class="text-rose-500">*</span>
                  </label>
                  <div class="relative">
                    <input :type="showPassLogin ? 'text' : 'password'" 
                           name="password" 
                           x-ref="loginPassword"
                           x-model="loginForm.password"
                           @input="loginTouched.password = true; validateLoginPassword()"
                           @blur="loginTouched.password = true; validateLoginPassword()"
                           placeholder="••••••••••••" 
                           class="w-full pl-4 pr-11 py-3.5 text-xs sm:text-sm rounded-xl border bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 transition shadow-2xs font-medium"
                           :class="loginErrors.password && loginTouched.password ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 focus:ring-amber-400 focus:border-amber-400'" />
                    <button type="button" 
                            @click="showPassLogin = !showPassLogin" 
                            class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400 hover:text-gray-700 transition cursor-pointer">
                      <svg x-show="!showPassLogin" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                      <svg x-show="showPassLogin" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                    </button>
                  </div>
                  <p x-show="loginErrors.password && loginTouched.password" x-text="loginErrors.password" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
                </div>

                <!-- Remember & Forgot -->
                <div class="flex items-center justify-between pt-1">
                  <label class="inline-flex items-center gap-2 cursor-pointer text-xs text-gray-600 font-medium">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded text-amber-500 focus:ring-amber-400 border-gray-300" />
                    <span>Ingat saya</span>
                  </label>
                  <a href="{{ url('/forgot-password') }}" class="text-xs font-bold text-gray-800 hover:text-amber-600 hover:underline transition">
                    Lupa password?
                  </a>
                </div>

                <!-- Button (Clean Pure Text Standard without Icons) -->
                <div class="pt-2">
                  <button type="submit" class="w-full py-3.5 px-6 bg-[#222222] hover:bg-amber-400 text-white hover:text-gray-900 font-extrabold text-sm rounded-xl transition duration-200 shadow-md text-center cursor-pointer">
                    Masuk ke Akun
                  </button>
                </div>

              </form>

              <!-- Zero-Reload Sliding Trigger Button to Register -->
              <div class="text-center text-xs sm:text-sm text-gray-500 font-medium pt-2">
                Belum punya akun? 
                <button type="button" 
                        @click="setMode('register')" 
                        class="font-extrabold text-amber-500 hover:text-amber-600 underline underline-offset-4 transition cursor-pointer">
                  Daftar Sekarang
                </button>
              </div>

            </div>


            <!-- ==================== SIGN UP (REGISTER) FORM ==================== -->
            <div class="w-full space-y-5 auth-form-register">

              <!-- Brand Logo & Heading (Centered - Title in Solid Black text-gray-900) -->
              <div class="space-y-3 text-center">
                <div class="flex items-center justify-center">
                  <a href="{{ url('/') }}" class="inline-flex items-center gap-2 group">
                    <img src="{{ asset('images/logo/lensmatch_logo_transparent_yellow.png') }}" 
                         alt="LensMatch Logo" 
                         class="h-10 sm:h-11 w-auto group-hover:scale-105 transition duration-300 drop-shadow-xs" />
                    <span class="text-base sm:text-lg font-bold tracking-tight text-gray-800">LensMatch.</span>
                  </a>
                </div>

                <div class="space-y-1">
                  <h1 class="text-3xl sm:text-[34px] font-black text-gray-900 tracking-tight leading-tight">
                    Buat Akun Baru
                  </h1>
                  <p class="text-xs sm:text-sm text-gray-500 font-medium">
                    Bergabunglah bersama komunitas fotografi LensMatch
                  </p>
                </div>
              </div>

              <!-- Session Alerts -->
              @if (session('error'))
                <div class="p-3.5 rounded-xl bg-red-50 border border-red-200 text-xs font-bold text-red-800 flex items-center gap-2">
                  <svg class="w-4 h-4 shrink-0 text-red-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                  <span>{{ session('error') }}</span>
                </div>
              @endif

              <!-- Sign Up Form -->
              <form action="{{ route('register') }}" method="POST" @submit="submitRegister($event)" novalidate class="space-y-3.5 pt-1">
                @csrf
                @if(request()->filled('redirect'))
                  <input type="hidden" name="redirect" value="{{ request('redirect') }}">
                @endif
                
                <!-- Nama Lengkap -->
                <div class="space-y-1">
                  <label class="block text-xs font-bold text-gray-600">
                    Nama Lengkap <span class="text-rose-500">*</span>
                  </label>
                  <input type="text" 
                         name="nama" 
                         x-ref="regNama"
                         x-model="regForm.nama"
                         @input="regTouched.nama = true; validateRegisterNama()"
                         @blur="regTouched.nama = true; validateRegisterNama()"
                         placeholder="Nama lengkap Anda" 
                         class="w-full px-4 py-3 text-xs sm:text-sm rounded-xl border bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 transition shadow-2xs font-medium"
                         :class="regErrors.nama && regTouched.nama ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 focus:ring-amber-400 focus:border-amber-400'" />
                  <p x-show="regErrors.nama && regTouched.nama" x-text="regErrors.nama" x-cloak class="text-[11px] font-bold text-red-500 mt-0.5"></p>
                </div>

                <!-- Role Selector (Standardized Alpine Dropdown) -->
                <div class="space-y-1">
                  <label class="block text-xs font-bold text-gray-600">
                    Daftar Sebagai <span class="text-rose-500">*</span>
                  </label>
                  <input type="hidden" name="role" :value="selectedRole" />
                  
                  <div class="relative">
                    <button @click="roleDropdownOpen = !roleDropdownOpen" 
                            type="button" 
                            class="w-full px-4 py-3 text-xs sm:text-sm rounded-xl border border-gray-200 bg-white text-gray-900 font-medium text-left flex items-center justify-between shadow-2xs focus:outline-none focus:ring-2 focus:ring-amber-400 transition cursor-pointer">
                      <span x-text="selectedRoleLabel"></span>
                      <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="roleDropdownOpen ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                      </svg>
                    </button>

                    <div x-show="roleDropdownOpen" 
                         @click.away="roleDropdownOpen = false" 
                         x-transition 
                         class="absolute z-50 left-0 right-0 mt-2 bg-white rounded-xl shadow-xl border border-gray-100 py-1 overflow-hidden"
                         style="display: none;">
                      <button type="button" 
                              @click="selectedRole = 'client'; selectedRoleLabel = 'Client (Pencari Fotografer)'; roleDropdownOpen = false" 
                              class="w-full px-4 py-2.5 text-xs sm:text-sm font-medium text-gray-700 hover:bg-amber-50 hover:text-amber-600 transition flex items-center justify-between text-left cursor-pointer"
                              :class="selectedRole === 'client' ? 'bg-amber-50/70 text-amber-600' : ''">
                        <span>Client (Pencari Fotografer)</span>
                      </button>
                      <button type="button" 
                              @click="selectedRole = 'photographer'; selectedRoleLabel = 'Fotografer (Penyedia Jasa)'; roleDropdownOpen = false" 
                              class="w-full px-4 py-2.5 text-xs sm:text-sm font-medium text-gray-700 hover:bg-amber-50 hover:text-amber-600 border-t border-gray-100 transition flex items-center justify-between text-left cursor-pointer"
                              :class="selectedRole === 'photographer' ? 'bg-amber-50/70 text-amber-600' : ''">
                        <span>Fotografer (Penyedia Jasa)</span>
                      </button>
                    </div>
                  </div>
                  @error('role')
                    <p class="text-[11px] font-bold text-red-500 mt-0.5">{{ $message }}</p>
                  @enderror
                </div>

                <!-- Nomor WhatsApp -->
                <div class="space-y-1">
                  <label class="block text-xs font-bold text-gray-600">
                    Nomor WhatsApp <span class="text-rose-500">*</span>
                  </label>
                  <input type="tel" 
                         name="nomor_telepon" 
                         x-ref="regPhone"
                         x-model="regForm.nomor_telepon"
                         inputmode="numeric"
                         pattern="[0-9]*"
                         @keypress="if (!/[0-9]/.test($event.key)) $event.preventDefault()"
                         @input="regForm.nomor_telepon = (regForm.nomor_telepon || '').replace(/\D/g, ''); regTouched.nomor_telepon = true; validateRegisterPhone()"
                         @blur="regTouched.nomor_telepon = true; validateRegisterPhone()"
                         placeholder="Contoh: 08xxxxxxxxxx" 
                         class="w-full px-4 py-3 text-xs sm:text-sm rounded-xl border bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 transition shadow-2xs font-medium"
                         :class="regErrors.nomor_telepon && regTouched.nomor_telepon ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 focus:ring-amber-400 focus:border-amber-400'" />
                  <p x-show="regErrors.nomor_telepon && regTouched.nomor_telepon" x-text="regErrors.nomor_telepon" x-cloak class="text-[11px] font-bold text-red-500 mt-0.5"></p>
                </div>

                <!-- Email -->
                <div class="space-y-1">
                  <label class="block text-xs font-bold text-gray-600">
                    Email <span class="text-rose-500">*</span>
                  </label>
                  <input type="email" 
                         name="email" 
                         x-ref="regEmail"
                         x-model="regForm.email"
                         @input="regTouched.email = true; validateRegisterEmail()"
                         @blur="regTouched.email = true; validateRegisterEmail()"
                         placeholder="nama@email.com" 
                         class="w-full px-4 py-3 text-xs sm:text-sm rounded-xl border bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 transition shadow-2xs font-medium"
                         :class="regErrors.email && regTouched.email ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 focus:ring-amber-400 focus:border-amber-400'" />
                  <p x-show="regErrors.email && regTouched.email" x-text="regErrors.email" x-cloak class="text-[11px] font-bold text-red-500 mt-0.5"></p>
                </div>

                <!-- Password -->
                <div class="space-y-1">
                  <label class="block text-xs font-bold text-gray-700">
                    Kata Sandi <span class="text-rose-500">*</span>
                  </label>
                  <div class="relative">
                    <input :type="showPassRegister ? 'text' : 'password'" 
                           name="password" 
                           x-ref="regPassword"
                           x-model="regForm.password"
                           @input="regTouched.password = true; validateRegisterPassword()"
                           @blur="regTouched.password = true; validateRegisterPassword()"
                           placeholder="Minimal 8 karakter" 
                           class="w-full pl-4 pr-11 py-3 text-xs sm:text-sm rounded-xl border bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 transition shadow-2xs font-medium"
                           :class="regErrors.password && regTouched.password ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 focus:ring-amber-400 focus:border-amber-400'" />
                    <button type="button" 
                            @click="showPassRegister = !showPassRegister" 
                            class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400 hover:text-gray-700 transition cursor-pointer">
                      <svg x-show="!showPassRegister" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                      <svg x-show="showPassRegister" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                    </button>
                  </div>
                  <p x-show="regErrors.password && regTouched.password" x-text="regErrors.password" x-cloak class="text-[11px] font-bold text-red-500 mt-0.5"></p>
                </div>

                <!-- Button (Clean Pure Text Standard without Icons) -->
                <div class="pt-2">
                  <button type="submit" class="w-full py-3.5 px-6 bg-[#222222] hover:bg-amber-400 text-white hover:text-gray-900 font-extrabold text-sm rounded-xl transition duration-200 shadow-md text-center cursor-pointer">
                    Daftar Sekarang
                  </button>
                </div>

              </form>

              <!-- Zero-Reload Sliding Trigger Button to Login -->
              <div class="text-center text-xs sm:text-sm text-gray-500 font-medium pt-2">
                Sudah punya akun? 
                <button type="button" 
                        @click="setMode('login')" 
                        class="font-extrabold text-amber-500 hover:text-amber-600 underline underline-offset-4 transition cursor-pointer">
                  Masuk ke Akun
                </button>
              </div>

            </div>

          </div>

        </div>


        <!-- PANEL 2: VISUAL CARD (Pre-rendered Hardware-Accelerated Dual Clip-Paths - 51% width) -->
        <div class="hidden lg:block lg:w-[51%] h-[630px] z-10 relative auth-panel-visual">

          <!-- Visual Layer 1: Login Shape (Right Organic Cutout) -->
          <!-- In Login Mode: Bottom-Left is SOLID (No cutout). Bottom-Right has cutout. So place text on bottom-left! -->
          <div class="absolute inset-0 w-full h-full overflow-hidden bg-cover bg-center auth-visual-login"
               style="clip-path: url(#havenix-shape-right); -webkit-clip-path: url(#havenix-shape-right); background-image: url('{{ asset('images/prewedding/prewedding-2.jpg') }}');">
            <!-- Bottom Dark Gradient for Seamless Deep Black Blend (like Image 2) -->
            <div class="absolute inset-0 bg-gradient-to-t from-black from-15% via-black/60 via-45% to-transparent"></div>
            
            <!-- Bottom-Left Tagline Text (Placed safely away from bottom-right cutout) -->
            <div class="absolute bottom-8 left-8 sm:left-10 right-28 text-left z-10">
              <p class="text-xs sm:text-sm xl:text-[14px] font-medium text-gray-300 leading-relaxed drop-shadow-md max-w-sm">
                Temukan ribuan fotografer profesional terpercaya untuk setiap momen berharga Anda.
              </p>
            </div>
          </div>

          <!-- Visual Layer 2: Register Shape (Left Mirrored Organic Cutout) -->
          <!-- In Register Mode: Bottom-Right is SOLID (No cutout). Bottom-Left has cutout. So place text on bottom-right! -->
          <div class="absolute inset-0 w-full h-full overflow-hidden bg-cover bg-center auth-visual-register"
               style="clip-path: url(#havenix-shape-left); -webkit-clip-path: url(#havenix-shape-left); background-image: url('{{ asset('images/prewedding/prewedding-holding-hands.jpg') }}');">
            <!-- Bottom Dark Gradient for Seamless Deep Black Blend (like Image 2) -->
            <div class="absolute inset-0 bg-gradient-to-t from-black from-15% via-black/60 via-45% to-transparent"></div>
            
            <!-- Bottom-Right Tagline Text (Placed safely away from bottom-left cutout) -->
            <div class="absolute bottom-8 right-8 sm:right-10 left-28 text-right z-10">
              <p class="text-xs sm:text-sm xl:text-[14px] font-medium text-gray-300 leading-relaxed drop-shadow-md max-w-sm ml-auto">
                Mulai perjalanan fotografi Anda bersama ribuan kreator & klien di seluruh penjuru negeri.
              </p>
            </div>
          </div>

        </div>

      </div>

    </div>

  </body>
</html>
