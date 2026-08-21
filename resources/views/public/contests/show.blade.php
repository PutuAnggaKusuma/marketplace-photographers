@extends('layouts.app')

@section('title', $contest->judul_lomba . ' — Lomba Foto LensMatch')

@section('content')
<section class="py-12 bg-white dark:bg-gray-900/50 min-h-screen">
  <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

    <!-- Back Navigation -->
    <div>
      <a href="{{ route('public.contests.index') }}" class="text-xs font-bold text-gray-500 hover:text-amber-500 transition inline-flex items-center gap-1">
        <span>← Kembali ke Katalog Event Lomba</span>
      </a>
    </div>

    <!-- Event Header Card -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm space-y-6 p-6 sm:p-10">
      <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-2">
          <span class="px-3 py-1 bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300 text-[11px] font-black rounded-lg uppercase">
            {{ $contest->kategori }}
          </span>
          <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-[11px] font-bold rounded-lg">
            Penyelenggara: {{ $contest->penyelenggara }}
          </span>
        </div>

        <span class="px-4 py-1.5 bg-amber-500 text-white font-black text-sm rounded-xl shadow-xs">
          Hadiah: {{ $contest->hadiah }}
        </span>
      </div>

      <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-gray-900 dark:text-white leading-tight">
        {{ $contest->judul_lomba }}
      </h1>

      <div class="rounded-2xl overflow-hidden h-72 sm:h-96 border border-gray-200 dark:border-gray-700">
        <img onerror="this.onerror=null;this.src=window.SVG_IMAGE_FALLBACK||'data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='800' height='600' viewBox='0 0 800 600' fill='none'%3E%3Crect width='800' height='600' fill='%23F3F4F6'/%3E%3Cpath d='M360 260C360 248.954 368.954 240 380 240H420C431.046 240 440 248.954 440 260V265H450C466.569 265 480 278.431 480 295V345C480 361.569 466.569 375 450 375H350C333.431 375 320 361.569 320 345V295C320 278.431 333.431 265 350 265H360V260Z' stroke='%239CA3AF' stroke-width='12' stroke-linecap='round' stroke-linejoin='round'/%3E%3Ccircle cx='400' cy='320' r='30' stroke='%239CA3AF' stroke-width='12'/%3E%3Cline x1='310' y1='240' x2='490' y2='390' stroke='%23EF4444' stroke-width='10' stroke-linecap='round'/%3E%3Ctext x='400' y='450' font-family='sans-serif' font-size='22' font-weight='700' fill='%236B7280' text-anchor='middle'%3EGambar Tidak Dapat Dimuat%3C/text%3E%3C/svg%3E';" src="{{ $contest->banner_url }}" alt="{{ $contest->judul_lomba }}" class="w-full h-full object-cover" />
      </div>

      <!-- Description & Rules -->
      <div class="space-y-3 pt-2">
        <h3 class="text-base font-extrabold text-gray-900 dark:text-white">Syarat & Ketentuan Lomba</h3>
        <p class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line bg-gray-50 dark:bg-gray-900 p-5 rounded-2xl border border-gray-200/60 dark:border-gray-700">
          {{ $contest->deskrpisi_lomba }}
        </p>
      </div>
    </div>

    <!-- Winners Showcase Section (If Completed) -->
    @if($winners->count() > 0)
      <div class="bg-gradient-to-r from-amber-500/10 via-amber-400/5 to-amber-500/10 p-6 sm:p-8 rounded-3xl border border-amber-500/30 space-y-6">
        <div class="text-center max-w-xl mx-auto space-y-1">
          <span class="px-3 py-1 rounded-full bg-amber-400 text-gray-900 font-black text-[10px] uppercase tracking-widest">Panggung Kehormatan</span>
          <h2 class="text-2xl font-black text-gray-900 dark:text-white">Pemenang Juara Lomba Foto</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          @foreach($winners as $win)
            <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl border border-amber-300 dark:border-amber-700/80 shadow-md space-y-3 text-center">
              <div class="h-44 rounded-xl overflow-hidden relative border border-gray-200 dark:border-gray-700">
                <img onerror="this.onerror=null;this.src=window.SVG_IMAGE_FALLBACK||'data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='800' height='600' viewBox='0 0 800 600' fill='none'%3E%3Crect width='800' height='600' fill='%23F3F4F6'/%3E%3Cpath d='M360 260C360 248.954 368.954 240 380 240H420C431.046 240 440 248.954 440 260V265H450C466.569 265 480 278.431 480 295V345C480 361.569 466.569 375 450 375H350C333.431 375 320 361.569 320 345V295C320 278.431 333.431 265 350 265H360V260Z' stroke='%239CA3AF' stroke-width='12' stroke-linecap='round' stroke-linejoin='round'/%3E%3Ccircle cx='400' cy='320' r='30' stroke='%239CA3AF' stroke-width='12'/%3E%3Cline x1='310' y1='240' x2='490' y2='390' stroke='%23EF4444' stroke-width='10' stroke-linecap='round'/%3E%3Ctext x='400' y='450' font-family='sans-serif' font-size='22' font-weight='700' fill='%236B7280' text-anchor='middle'%3EGambar Tidak Dapat Dimuat%3C/text%3E%3C/svg%3E';" src="{{ $win->image_url }}" alt="{{ $win->judul_karya }}" class="w-full h-full object-cover" />
                <span class="absolute top-2 left-2 px-2.5 py-1 bg-amber-400 text-gray-900 text-[10px] font-black rounded-md shadow-sm">
                  {{ $win->status_submission === 'winner_1' ? '🥇 JUARA 1' : ($win->status_submission === 'winner_2' ? '🥈 JUARA 2' : '🥉 JUARA 3') }}
                </span>
              </div>
              <div>
                <h4 class="font-extrabold text-xs text-gray-900 dark:text-white line-clamp-1">{{ $win->judul_karya }}</h4>
                <p class="text-[11px] font-bold text-amber-600 dark:text-amber-400 mt-0.5">{{ $win->user->nama ?? 'Peserta' }}</p>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    @endif

    <!-- Registration Submit Form Section -->
    @if($contest->status === 'buka')
      <div class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-3xl border border-amber-200/80 dark:border-gray-700 shadow-sm space-y-4">
        <h3 class="text-lg font-black text-gray-900 dark:text-white">Daftarkan Foto Karya Anda</h3>
        
        @auth
          @if($userSubmission)
            <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 text-emerald-800 dark:text-emerald-300 text-xs font-bold flex items-center justify-between">
              <div>
                <p class="font-extrabold text-sm">Status Pendaftaran Anda: Terdaftar ✅</p>
                <p class="text-[11px] font-medium mt-0.5">Judul Karya: "{{ $userSubmission->judul_karya }}"</p>
              </div>
            </div>
          @else
            <form action="{{ route('public.contests.submit', $contest->id) }}" method="POST" class="space-y-4">
              @csrf
              <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Judul Karya Foto *</label>
                <input type="text" name="judul_karya" required placeholder="mis. Ketenangan Kabut Pagi Danau Beratan" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500">
              </div>

              <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">URL Link Foto Karya *</label>
                <input type="url" name="image_url" required placeholder="https://images.unsplash.com/..." class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500">
              </div>

              <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Deskripsi / Cerita di Balik Foto</label>
                <textarea name="deskripsi_karya" rows="3" placeholder="Ceritakan latar belakang pengambilan foto ini..." class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none resize-none"></textarea>
              </div>

              <button type="submit" class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-extrabold text-xs rounded-xl shadow-xs transition">
                Kirim Pendaftaran Karya Foto
              </button>
            </form>
          @endif
        @else
          <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 text-center text-xs text-amber-800 dark:text-amber-300">
            Silakan <a href="{{ route('login') }}" class="font-extrabold underline">Login</a> terlebih dahulu untuk mendaftarkan karya foto pada kompetisi ini.
          </div>
        @endauth
      </div>
    @endif

    <!-- Submitted Entries Gallery -->
    <div class="space-y-4 pt-4">
      <h3 class="text-lg font-black text-gray-900 dark:text-white">Galeri Karya Peserta ({{ $submissions->count() }})</h3>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @forelse($submissions as $sub)
          <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-xs space-y-3">
            <div class="h-44 rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-900">
              <img onerror="this.onerror=null;this.src=window.SVG_IMAGE_FALLBACK||'data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='800' height='600' viewBox='0 0 800 600' fill='none'%3E%3Crect width='800' height='600' fill='%23F3F4F6'/%3E%3Cpath d='M360 260C360 248.954 368.954 240 380 240H420C431.046 240 440 248.954 440 260V265H450C466.569 265 480 278.431 480 295V345C480 361.569 466.569 375 450 375H350C333.431 375 320 361.569 320 345V295C320 278.431 333.431 265 350 265H360V260Z' stroke='%239CA3AF' stroke-width='12' stroke-linecap='round' stroke-linejoin='round'/%3E%3Ccircle cx='400' cy='320' r='30' stroke='%239CA3AF' stroke-width='12'/%3E%3Cline x1='310' y1='240' x2='490' y2='390' stroke='%23EF4444' stroke-width='10' stroke-linecap='round'/%3E%3Ctext x='400' y='450' font-family='sans-serif' font-size='22' font-weight='700' fill='%236B7280' text-anchor='middle'%3EGambar Tidak Dapat Dimuat%3C/text%3E%3C/svg%3E';" src="{{ $sub->image_url }}" alt="{{ $sub->judul_karya }}" class="w-full h-full object-cover" />
            </div>
            <div>
              <h4 class="font-extrabold text-xs text-gray-900 dark:text-white line-clamp-1">{{ $sub->judul_karya }}</h4>
              <p class="text-[10px] text-gray-400 mt-0.5">Oleh: {{ $sub->user->nama ?? 'Peserta' }}</p>
            </div>
          </div>
        @empty
          <div class="col-span-full bg-white dark:bg-gray-800 p-8 rounded-2xl border border-gray-200 dark:border-gray-700 text-center text-xs text-gray-400 font-bold">
            Belum ada karya foto peserta yang terdaftar pada event ini.
          </div>
        @endforelse
      </div>
    </div>

  </div>
</section>
@endsection