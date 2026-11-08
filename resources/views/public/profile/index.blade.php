@extends('layouts.app')

@section('title', 'Pengaturan Akun & Profil — LensMatch')

@section('content')
<section class="py-12 bg-white dark:bg-gray-900 min-h-screen">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

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
      $rawAvatar = $photographer?->foto ?: ($client?->foto ?: $user?->foto);
      $avatarUrl = $rawAvatar ? (str_starts_with($rawAvatar, 'http') ? $rawAvatar : asset('storage/' . $rawAvatar)) : '';
    @endphp

    <!-- Section 1: Informasi Biodata Profil (Hybrid Responsive Form Standard) -->
    <div class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-3xl border border-gray-200 dark:border-gray-700 shadow-sm space-y-6">
      <h2 class="text-lg font-black text-gray-900 dark:text-white">Informasi Profil</h2>

      <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Horizontal Row 1: Nama Lengkap & Alamat Email -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
            <input type="text" name="nama" value="{{ old('nama', $user->nama) }}" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500">
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Alamat Email <span class="text-rose-500">*</span></label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500">
          </div>
        </div>

        <!-- Horizontal Row 2: Nomor Telepon / WA & Domisili Kota / Peran -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Nomor Telepon / WhatsApp</label>
            <input type="text" name="nomor_telepon" value="{{ old('nomor_telepon', $photographer?->nomor_telepon ?: $client?->nomor_telepon) }}" placeholder="Masukkan nomor telepon / WhatsApp" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500">
          </div>

          @if($user->role === 'photographer')
            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Alamat / Domisili Kota</label>
              <input type="text" name="alamat" value="{{ old('alamat', $photographer?->alamat) }}" placeholder="Masukkan alamat domisili" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500">
            </div>
          @else
            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Tipe Akun Pengguna</label>
              <input type="text" value="{{ ucfirst($user->role ?? 'Klien') }}" disabled class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 text-xs text-gray-500 dark:text-gray-400 cursor-not-allowed">
            </div>
          @endif
        </div>

        @if($user->role === 'photographer')
          <!-- Vertical Long Field: Deskripsi Bio Fotografer -->
          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Deskripsi Bio Fotografer</label>
            <textarea name="deskripsi_bio" rows="6" placeholder="Tuliskan deskripsi lengkap mengenai pengalaman fotografi, spesialisasi, dan pendekatan artistik Anda..." class="w-full min-h-[150px] px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none resize-y focus:ring-2 focus:ring-amber-500 leading-relaxed">{{ old('deskripsi_bio', $photographer?->deskripsi_bio) }}</textarea>
          </div>
        @endif

        <!-- Vertical Component: Foto Avatar Profil (Rule #97: Standarisasi Pratinjau Modal Lightbox) -->
        <div x-data="{ 
            preview: '{{ $avatarUrl }}', 
            imagePreviewModalOpen: false,
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
                <div class="relative w-24 h-24 sm:w-28 sm:h-28 rounded-2xl overflow-hidden bg-gray-100 dark:bg-gray-800 border-2 shadow-xs shrink-0 flex items-center justify-center transition"
                     :class="preview ? 'border-amber-400 border-solid cursor-pointer group' : 'border-dashed border-gray-300 dark:border-gray-600'"
                     @click="if (preview) imagePreviewModalOpen = true">
                  <img :src="preview" 
                       x-show="preview" 
                       alt="Preview Avatar" 
                       class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" 
                       style="display: none;" 
                       :style="preview ? 'display: block;' : 'display: none;'">
                  <div x-show="preview" 
                       class="absolute inset-0 bg-black/45 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center text-white text-[10px] font-bold">
                    <svg class="w-5 h-5 text-white mb-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607zM10.5 7.5v6m3-3h-6"/></svg>
                    <span>Pratinjau</span>
                  </div>
                  <div x-show="!preview" 
                       class="flex items-center justify-center text-gray-400 dark:text-gray-500"
                       :style="!preview ? 'display: flex;' : 'display: none;'">
                    <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                      <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                    </svg>
                  </div>
                </div>
                
                <div class="flex-1 space-y-2">
                    <label class="px-4 py-2 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs cursor-pointer shadow-xs transition inline-flex items-center justify-center">
                        <span>Upload Foto Profil</span>
                        <input type="file" name="foto_file" accept="image/*" @change="handleFile($event)" class="hidden">
                    </label>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400">File gambar dari perangkat (JPG, PNG, WEBP maks. 10MB)</p>
                </div>
            </div>

            <!-- Modal Pratinjau Foto Resolusi Penuh -->
            <div x-show="imagePreviewModalOpen" 
                 x-cloak 
                 @keydown.escape.window="imagePreviewModalOpen = false"
                 class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 backdrop-blur-md p-4 sm:p-6"
                 tabindex="-1">
                <button type="button" 
                        @click="imagePreviewModalOpen = false" 
                        title="Tutup (Esc)" 
                        class="absolute top-4 right-4 sm:top-6 sm:right-6 w-11 h-11 rounded-full bg-white/10 hover:bg-white/25 text-white flex items-center justify-center transition cursor-pointer z-50">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
                <div class="relative max-w-5xl max-h-[90vh] flex flex-col items-center justify-center space-y-3" @click.outside="imagePreviewModalOpen = false">
                    <img :src="preview" 
                         alt="Foto Avatar Profil" 
                         draggable="false"
                         class="max-w-full max-h-[80vh] object-contain rounded-2xl shadow-2xl select-none border border-white/10">
                    <div class="px-4 py-1.5 rounded-full bg-black/60 text-white/90 text-xs font-semibold backdrop-blur-sm border border-white/10 flex items-center gap-2">
                        <span>Foto Avatar Profil</span>
                        <span class="text-white/40">•</span>
                        <span class="text-white/60 text-[11px]">Tekan Esc atau klik di luar untuk menutup</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-3">
          <button type="submit" class="px-6 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs rounded-xl shadow-xs transition cursor-pointer">
            Perbaharui Data
          </button>
        </div>
      </form>
    </div>

    <!-- Section 2: Keamanan & Ganti Password (Hybrid Responsive Form Standard) -->
    <div class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-3xl border border-gray-200 dark:border-gray-700 shadow-sm space-y-6">
      <h2 class="text-lg font-black text-gray-900 dark:text-white">Keamanan & Kata Sandi</h2>

      <form action="{{ route('profile.password') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Horizontal Row: 3 Password Inputs Side-by-Side -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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
        </div>

        <div class="flex justify-end pt-3">
          <button type="submit" class="px-6 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs rounded-xl shadow-xs transition cursor-pointer">
            Perbaharui Data
          </button>
        </div>
      </form>
    </div>

  </div>
</section>
@endsection
