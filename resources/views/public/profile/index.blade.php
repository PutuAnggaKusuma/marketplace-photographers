@extends('layouts.app')

@section('title', 'Pengaturan Akun & Profil - LensMatch')

@section('content')
<section class="py-12 bg-white dark:bg-gray-900/50 min-h-screen">
  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

    <!-- Page Header -->
    <div class="bg-gradient-to-r from-gray-900 via-amber-950 to-gray-900 text-white rounded-3xl p-8 sm:p-10 shadow-xl border border-amber-500/20 flex flex-col sm:flex-row items-center justify-between gap-6">
      <div class="flex items-center gap-5">
        @php
          $avatarUrl = $photographer?->foto ?: ($client?->foto ?: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=800&q=80');
        @endphp
        <div class="w-20 h-20 rounded-2xl overflow-hidden bg-gray-800 border-2 border-amber-400 shrink-0 shadow-lg relative">
          <img onerror="this.onerror=null;this.src=window.SVG_IMAGE_FALLBACK||'data:image/svg+xml;charset=UTF-8,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'800\' height=\'600\' viewBox=\'0 0 800 600\' fill=\'none\'%3E%3Crect width=\'800\' height=\'600\' fill=\'%23F3F4F6\'/%3E%3Cpath d=\'M360 260C360 248.954 368.954 240 380 240H420C431.046 240 440 248.954 440 260V265H450C466.569 265 480 278.431 480 295V345C480 361.569 466.569 375 450 375H350C333.431 375 320 361.569 320 345V295C320 278.431 333.431 265 350 265H360V260Z\' stroke=\'%239CA3AF\' stroke-width=\'12\' stroke-linecap=\'round\' stroke-linejoin=\'round\'/%3E%3Ccircle cx=\'400\' cy=\'320\' r=\'30\' stroke=\'%239CA3AF\' stroke-width=\'12\'/%3E%3Cline x1=\'310\' y1=\'240\' x2=\'490\' y2=\'390\' stroke=\'%23EF4444\' stroke-width=\'10\' stroke-linecap=\'round\'/%3E%3Ctext x=\'400\' y=\'450\' font-family=\'sans-serif\' font-size=\'22\' font-weight=\'700\' fill=\'%236B7280\' text-anchor=\'middle\'%3EGambar Tidak Dapat Dimuat%3C/text%3E%3C/svg%3E';" src="{{ $avatarUrl }}" alt="{{ $user->nama }}" class="w-full h-full object-cover" />
        </div>
        <div>
          <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-400 text-gray-900">
            {{ $user->role === 'client' ? 'Akun Klien' : ($user->role === 'photographer' ? 'Studio Fotografer' : 'Administrator') }}
          </span>
          <h1 class="text-2xl sm:text-3xl font-black text-white mt-1">{{ $user->nama }}</h1>
          <p class="text-xs text-gray-300 mt-0.5">{{ $user->email }} • Bergabung sejak {{ $user->created_at->format('M Y') }}</p>
        </div>
      </div>
    </div>

    <!-- Section 1: Informasi Biodata Profil -->
    <div class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-3xl border border-gray-200 dark:border-gray-700 shadow-sm space-y-6">
      <div class="border-b border-gray-100 dark:border-gray-700 pb-4">
        <h2 class="text-lg font-black text-gray-900 dark:text-white">Informasi Diri & Biodata</h2>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Perbarui nama, alamat email, dan foto avatar profil Anda</p>
      </div>

      <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Nama Lengkap *</label>
            <input type="text" name="nama" value="{{ old('nama', $user->nama) }}" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500">
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Alamat Email *</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500">
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Nomor Telepon / WhatsApp</label>
            <input type="text" name="nomor_telepon" value="{{ old('nomor_telepon', $photographer?->nomor_telepon ?: $client?->nomor_telepon) }}" placeholder="081234567890" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none">
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">URL Foto Avatar Profil</label>
            <input type="url" name="foto" value="{{ old('foto', $avatarUrl) }}" placeholder="https://images.unsplash.com/..." class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none">
          </div>
        </div>

        @if($user->role === 'photographer')
          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Alamat Studio / Kota</label>
            <input type="text" name="alamat" value="{{ old('alamat', $photographer?->alamat) }}" placeholder="Jl. Raya Kuta No. 45, Badung, Bali" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none">
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Deskripsi Bio Studio</label>
            <textarea name="deskripsi_bio" rows="3" placeholder="Tuliskan deskripsi singkat mengenai pengalaman studio fotografi Anda..." class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none resize-none">{{ old('deskripsi_bio', $photographer?->deskripsi_bio) }}</textarea>
          </div>
        @endif

        <div class="flex justify-end pt-2">
          <button type="submit" class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-extrabold text-xs rounded-xl shadow-xs transition">
            Simpan Perubahan Profil
          </button>
        </div>
      </form>
    </div>

    <!-- Section 2: Keamanan & Ganti Password -->
    <div class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-3xl border border-gray-200 dark:border-gray-700 shadow-sm space-y-6">
      <div class="border-b border-gray-100 dark:border-gray-700 pb-4">
        <h2 class="text-lg font-black text-gray-900 dark:text-white">Keamanan & Kata Sandi</h2>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Perbarui kata sandi akun Anda secara berkala demi keamanan</p>
      </div>

      <form action="{{ route('profile.password') }}" method="POST" class="space-y-4">
        @csrf

        <div>
          <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Kata Sandi Saat Ini *</label>
          <input type="password" name="current_password" required placeholder="••••••••" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Kata Sandi Baru *</label>
            <input type="password" name="new_password" required placeholder="Minimal 8 karakter" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500">
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Konfirmasi Kata Sandi Baru *</label>
            <input type="password" name="new_password_confirmation" required placeholder="Ulangi kata sandi baru" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500">
          </div>
        </div>

        <div class="flex justify-end pt-2">
          <button type="submit" class="px-6 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs rounded-xl shadow-xs transition">
            Perbarui Kata Sandi
          </button>
        </div>
      </form>
    </div>

  </div>
</section>
@endsection