@extends('layouts.app')

@section('title', 'Pengaturan Akun & Profil - LensMatch')

@section('content')
<section class="py-12 bg-white dark:bg-gray-900 min-h-screen">
  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

    <!-- Flash Messages -->
    @if(session('success'))
      <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 text-xs text-emerald-800 dark:text-emerald-300 font-bold flex items-center gap-2">
        <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
        <span>{{ session('success') }}</span>
      </div>
    @endif

    @if($errors->any())
      <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 text-xs text-rose-800 dark:text-rose-300 font-bold space-y-1">
        @foreach($errors->all() as $err)
          <p>• {{ $err }}</p>
        @endforeach
      </div>
    @endif

    @php
      $avatarUrl = $photographer?->foto ?: ($client?->foto ?: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=800&q=80');
    @endphp

    <!-- Section 1: Informasi Biodata Profil (100% Vertical Flow - Rule #23) -->
    <div class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-3xl border border-gray-200 dark:border-gray-700 shadow-sm space-y-6">
      <h2 class="text-lg font-black text-gray-900 dark:text-white">Informasi Profil</h2>

      <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div>
          <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
          <input type="text" name="nama" value="{{ old('nama', $user->nama) }}" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500">
        </div>

        <div>
          <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Alamat Email <span class="text-rose-500">*</span></label>
          <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500">
        </div>

        <div>
          <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Nomor Telepon / WhatsApp</label>
          <input type="text" name="nomor_telepon" value="{{ old('nomor_telepon', $photographer?->nomor_telepon ?: $client?->nomor_telepon) }}" placeholder="081234567890" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none">
        </div>

        @if($user->role === 'photographer')
          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Alamat / Domisili Kota</label>
            <input type="text" name="alamat" value="{{ old('alamat', $photographer?->alamat) }}" placeholder="Jl. Raya Kuta No. 45, Badung, Bali" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none">
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Deskripsi Bio Fotografer</label>
            <textarea name="deskripsi_bio" rows="3" placeholder="Tuliskan deskripsi singkat mengenai pengalaman fotografi Anda..." class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none resize-none">{{ old('deskripsi_bio', $photographer?->deskripsi_bio) }}</textarea>
          </div>
        @endif

        <!-- Pure Image File Upload from Device with Live Preview (Enlarged Box) -->
        <div x-data="{ 
            preview: '{{ $avatarUrl }}', 
            handleFile(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (event) => { this.preview = event.target.result; };
                    reader.readAsDataURL(file);
                }
            }
        }" class="p-5 rounded-2xl bg-gray-50 dark:bg-gray-900/60 border border-gray-200 dark:border-gray-700/80 space-y-3">
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">
                Foto Avatar Profil
            </label>
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="relative w-24 h-24 sm:w-28 sm:h-28 rounded-2xl overflow-hidden bg-gray-100 dark:bg-gray-800 border-2 border-amber-400 shadow-sm shrink-0">
                    <img :src="preview" alt="Preview Avatar" class="w-full h-full object-cover">
                </div>
                
                <div class="flex-1 space-y-2">
                    <label class="px-4 py-2 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs cursor-pointer shadow-xs transition inline-flex items-center justify-center">
                        <span>Upload Foto Profil</span>
                        <input type="file" name="foto_file" accept="image/*" @change="handleFile($event)" class="hidden">
                    </label>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400">File gambar dari perangkat (JPG, PNG, WEBP maks. 10MB)</p>
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-3">
          <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl shadow-xs transition">
            Perbaharui Data
          </button>
        </div>
      </form>
    </div>

    <!-- Section 2: Keamanan & Ganti Password (100% Vertical Flow - Rule #23) -->
    <div class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-3xl border border-gray-200 dark:border-gray-700 shadow-sm space-y-6">
      <h2 class="text-lg font-black text-gray-900 dark:text-white">Keamanan & Kata Sandi</h2>

      <form action="{{ route('profile.password') }}" method="POST" class="space-y-6">
        @csrf

        <div>
          <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Kata Sandi Saat Ini <span class="text-rose-500">*</span></label>
          <input type="password" name="current_password" required placeholder="••••••••" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500">
        </div>

        <div>
          <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Kata Sandi Baru <span class="text-rose-500">*</span></label>
          <input type="password" name="new_password" required placeholder="Minimal 8 karakter" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500">
        </div>

        <div>
          <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Konfirmasi Kata Sandi Baru <span class="text-rose-500">*</span></label>
          <input type="password" name="new_password_confirmation" required placeholder="Ulangi kata sandi baru" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500">
        </div>

        <div class="flex justify-end pt-3">
          <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl shadow-xs transition">
            Perbaharui Data
          </button>
        </div>
      </form>
    </div>

  </div>
</section>
@endsection