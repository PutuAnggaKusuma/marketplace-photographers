<!doctype html>
<html lang="id" class="h-full">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Verifikasi Email — LensMatch</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo/lensmatch_logo_transparent_yellow.png') }}" />
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
      [x-cloak] { display: none !important; }
      @keyframes authCardEntrance {
        0% { opacity: 0; transform: translateY(16px) scale(0.992); }
        100% { opacity: 1; transform: translateY(0) scale(1); }
      }
      .animate-auth-card {
        animation: authCardEntrance 0.55s cubic-bezier(0.16, 1, 0.3, 1) both;
        will-change: opacity, transform;
      }
    </style>
  </head>
  <body class="min-h-screen bg-gradient-to-b from-amber-50/80 via-[#f6f4f0] to-[#edf0f4] dark:from-gray-950 dark:via-gray-900 dark:to-gray-900 text-gray-900 dark:text-gray-100 font-sans antialiased flex items-center justify-center p-4 sm:p-6 lg:p-8 relative"
        x-data="{ helpModalOpen: false }">

    <div class="max-w-xl w-full space-y-6 bg-white dark:bg-gray-800 p-8 sm:p-10 rounded-3xl border border-gray-200/80 dark:border-gray-700/80 shadow-2xl relative z-10 animate-auth-card">
      
      <!-- Brand Logo -->
      <div class="text-center space-y-3">
        <a href="{{ url('/') }}" class="inline-block transition hover:scale-105 duration-200">
          <img src="{{ asset('images/logo/lensmatch_logo_transparent_yellow.png') }}" alt="LensMatch Logo" class="h-12 sm:h-14 w-auto mx-auto drop-shadow-sm" />
        </a>
        <h1 class="text-xl sm:text-2xl font-black text-gray-900 dark:text-white tracking-tight">Verifikasi Email Anda</h1>
        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 leading-relaxed max-w-md mx-auto">
          Tautan verifikasi akun telah dikirim ke alamat email <strong class="text-gray-900 dark:text-white font-bold">{{ auth()->user()->email }}</strong>. Silakan periksa kotak masuk atau folder spam Anda.
        </p>
      </div>

      <!-- Flash Status Message (Kirim Ulang) -->
      @if (session('status'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800/60 text-xs font-bold text-emerald-800 dark:text-emerald-300 flex items-center gap-2">
          <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
          <span>{{ session('status') }}</span>
        </div>
      @endif

      <!-- Flash Success Message (Permintaan Bantuan) -->
      @if (session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800/60 text-xs font-bold text-emerald-800 dark:text-emerald-300 flex items-center gap-2">
          <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
          <span>{{ session('success') }}</span>
        </div>
      @endif

      <!-- Status Info Jika Sudah Mengajukan Bantuan Aktivasi -->
      @if(auth()->user()->isActivationRequested())
        <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200/80 dark:border-amber-900/60 text-left space-y-1.5">
          <div class="flex items-center justify-between">
            <span class="text-xs font-extrabold text-amber-900 dark:text-amber-300">Permintaan Bantuan Sedang Ditinjau</span>
            <span class="text-[11px] font-bold text-amber-700 dark:text-amber-400">
              {{ auth()->user()->activation_requested_at->format('d M Y H:i') }}
            </span>
          </div>
          <p class="text-xs text-amber-800 dark:text-amber-300 font-medium leading-relaxed">
            Permintaan aktivasi akun Anda telah diterima oleh admin. Setelah admin memverifikasi akun Anda, cukup segarkan (refresh) halaman ini untuk langsung masuk.
          </p>
          @if(auth()->user()->activation_request_note)
            <div class="pt-2 border-t border-amber-200/60 dark:border-amber-900/40 text-[11px] text-amber-900/80 dark:text-amber-400">
              <span class="font-bold">Pesan Anda:</span> {{ auth()->user()->activation_request_note }}
            </div>
          @endif
        </div>
      @endif

      <!-- Action Buttons Row -->
      <div class="space-y-3 pt-2">
        <!-- Form Kirim Ulang Email -->
        <form action="{{ route('verification.send') }}" method="POST">
          @csrf
          <button type="submit" 
                  class="w-full py-3 px-4 bg-amber-50 hover:bg-amber-100 text-amber-900 dark:bg-amber-950/50 dark:text-amber-300 border border-amber-200/80 dark:border-amber-900/60 font-bold text-xs rounded-xl shadow-2xs transition cursor-pointer">
            Kirim Ulang Email Verifikasi
          </button>
        </form>

        <!-- Tombol Butuh Bantuan Aktivasi -->
        <button type="button" 
                @click="helpModalOpen = true"
                class="w-full py-3 px-4 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 font-bold text-xs rounded-xl shadow-2xs transition cursor-pointer">
          {{ auth()->user()->isActivationRequested() ? 'Perbarui Pesan Bantuan Aktivasi' : 'Butuh Bantuan Aktivasi?' }}
        </button>
      </div>

      <!-- Bottom Utilities: Refresh & Logout -->
      <div class="pt-4 border-t border-gray-100 dark:border-gray-700/60 flex items-center justify-between text-xs">
        <button type="button" 
                onclick="window.location.reload()" 
                class="font-bold text-amber-600 dark:text-amber-400 hover:underline cursor-pointer">
          Segarkan Halaman
        </button>

        <form action="{{ route('logout') }}" method="POST" class="inline">
          @csrf
          <button type="submit" class="font-bold text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 cursor-pointer">
            Keluar dari Akun
          </button>
        </form>
      </div>

    </div>

    <!-- Modal Permintaan Bantuan Aktivasi Manual (Rule #59 Compliant) -->
    <div x-show="helpModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs"
         @keydown.escape.window="helpModalOpen = false"
         style="display: none;">
      <div class="w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 sm:p-7 shadow-2xl space-y-5"
           @click.away="helpModalOpen = false">
        
        <!-- Judul Rata Tengah Tanpa Ikon (Rule #59) -->
        <div class="text-center space-y-1.5">
          <h3 class="text-base sm:text-lg font-black text-gray-900 dark:text-white tracking-tight">
            Permintaan Bantuan Aktivasi
          </h3>
          <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed max-w-md mx-auto">
            Jika email aktivasi tidak kunjung masuk ke inbox atau folder spam Anda, kirimkan permohonan ke admin untuk verifikasi akun secara manual.
          </p>
        </div>

        <form action="{{ route('verification.request_activation') }}" method="POST" class="space-y-4">
          @csrf
          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
              Pesan Kendala (Opsional)
            </label>
            <textarea name="pesan" 
                      rows="3" 
                      maxlength="500"
                      placeholder="Contoh: Email aktivasi tidak masuk ke inbox saya."
                      class="w-full p-3.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition resize-none">{{ auth()->user()->activation_request_note ?: '' }}</textarea>
          </div>

          <!-- Maksimal 2 Tombol Aksi Bawah Rata Tengah (Rule #59) -->
          <div class="flex items-center justify-center gap-3 pt-2">
            <button type="button" 
                    @click="helpModalOpen = false" 
                    class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold text-xs transition cursor-pointer shadow-2xs">
              Batal
            </button>
            <button type="submit" 
                    class="px-6 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs rounded-xl shadow-xs transition cursor-pointer">
              Kirim Permintaan Bantuan
            </button>
          </div>
        </form>

      </div>
    </div>

  </body>
</html>
