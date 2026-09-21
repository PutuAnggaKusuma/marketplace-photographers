@extends('layouts.photographer')

@section('title', 'Profil & Bio Fotografer — Dashboard')

@section('content')
<div class="space-y-6 w-full max-w-4xl mx-auto">

  <!-- Header Section -->
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
      <span class="text-[11px] font-extrabold uppercase tracking-widest text-amber-600 dark:text-amber-400">Pengaturan Identitas</span>
      <h1 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">Kelola Profil & Bio Fotografer</h1>
      <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Perbarui biodata publik, kontak WhatsApp, deskripsi bio, foto profil & foto sampul dari perangkat, dan kata sandi akun Anda</p>
    </div>
  </div>

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
    $user = auth()->user();
    $avatarUrl = $photographer?->foto ?: ($user->foto ?: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=800&q=80');
    $sampulUrl = $photographer?->foto_sampul ?: ($photographer?->portfolios?->first()?->medias?->first()?->media ?: 'https://images.unsplash.com/photo-1537633552985-df8429e8048b?w=1200&q=80');
  @endphp

  <!-- Section 1: Form Biodata & Media Fotografer (100% Vertical Flow - Rule #23) -->
  <div class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-3xl border border-gray-200 dark:border-gray-700/80 shadow-xs space-y-6">
    <h2 class="text-lg font-black text-gray-900 dark:text-white">Informasi Profil</h2>

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
      @csrf

      <!-- Field 1: Nama Lengkap Fotografer -->
      <div>
        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Nama Lengkap Fotografer <span class="text-rose-500">*</span></label>
        <input type="text" name="nama" value="{{ old('nama', $user->nama) }}" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500">
      </div>

      <!-- Field 2: Alamat Email -->
      <div>
        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Alamat Email <span class="text-rose-500">*</span></label>
        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500">
      </div>

      <!-- Field 3: Nomor Telepon / WhatsApp -->
      <div>
        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Nomor Telepon / WhatsApp</label>
        <input type="text" name="nomor_telepon" value="{{ old('nomor_telepon', $photographer?->nomor_telepon) }}" placeholder="081234567890" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500">
      </div>

      <!-- Field 4: Alamat / Domisili Kota -->
      <div>
        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Alamat / Domisili Kota</label>
        <input type="text" name="alamat" value="{{ old('alamat', $photographer?->alamat) }}" placeholder="Jl. Sunset Road No. 88, Kuta, Bali" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500">
      </div>

      <!-- Field 5: Deskripsi Bio Fotografer -->
      <div>
        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Deskripsi Bio Fotografer</label>
        <textarea name="deskripsi_bio" rows="4" placeholder="Tuliskan deskripsi singkat mengenai pengalaman dan spesialisasi fotografi Anda..." class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500 resize-none">{{ old('deskripsi_bio', $photographer?->deskripsi_bio) }}</textarea>
      </div>

      <!-- Field 6: Foto Avatar Profil (Preview Kotak Diperbesar) -->
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
              <!-- Enlarged Avatar Preview Box -->
              <div class="relative w-24 h-24 sm:w-28 sm:h-28 rounded-2xl overflow-hidden bg-gray-100 dark:bg-gray-800 border-2 border-amber-400 shadow-sm shrink-0">
                  <img :src="preview" alt="Preview Avatar" class="w-full h-full object-cover">
              </div>
              
              <div class="flex-1 space-y-2">
                  <label class="px-4 py-2 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs cursor-pointer shadow-xs transition inline-flex items-center justify-center">
                      <span>Upload Foto Avatar</span>
                      <input type="file" name="foto_file" accept="image/*" @change="handleFile($event)" class="hidden">
                  </label>
                  <p class="text-[11px] text-gray-500 dark:text-gray-400">File gambar avatar dari perangkat (JPG, PNG, WEBP maks. 10MB)</p>
              </div>
          </div>
      </div>

      <!-- Field 7: Foto Sampul / Cover Banner Profil (Preview Kotak Diperbesar) -->
      <div x-data="{ 
          previewSampul: '{{ $sampulUrl }}', 
          handleFileSampul(e) {
              const file = e.target.files[0];
              if (file) {
                  const reader = new FileReader();
                  reader.onload = (event) => { this.previewSampul = event.target.result; };
                  reader.readAsDataURL(file);
              }
          }
      }" class="p-5 rounded-2xl bg-gray-50 dark:bg-gray-900/60 border border-gray-200 dark:border-gray-700/80 space-y-3">
          <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">
              Foto Sampul / Cover Banner
          </label>
          <div class="flex flex-col sm:flex-row sm:items-center gap-4">
              <!-- Enlarged Cover Banner Preview Box -->
              <div class="relative w-48 h-28 sm:w-56 sm:h-32 rounded-2xl overflow-hidden bg-gray-100 dark:bg-gray-800 border-2 border-amber-400 shadow-sm shrink-0">
                  <img :src="previewSampul" alt="Preview Sampul" class="w-full h-full object-cover">
              </div>
              
              <div class="flex-1 space-y-2">
                  <label class="px-4 py-2 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs cursor-pointer shadow-xs transition inline-flex items-center justify-center">
                      <span>Upload Foto Sampul</span>
                      <input type="file" name="foto_sampul_file" accept="image/*" @change="handleFileSampul($event)" class="hidden">
                  </label>
                  <p class="text-[11px] text-gray-500 dark:text-gray-400">Rasio 16:9 dari perangkat (JPG, PNG, WEBP maks. 20MB)</p>
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
  <div class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-3xl border border-gray-200 dark:border-gray-700/80 shadow-xs space-y-6">
    <h2 class="text-lg font-black text-gray-900 dark:text-white">Keamanan & Kata Sandi</h2>

    <form action="{{ route('profile.password') }}" method="POST" class="space-y-6">
      @csrf

      <!-- Field 1: Kata Sandi Saat Ini -->
      <div>
        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Kata Sandi Saat Ini <span class="text-rose-500">*</span></label>
        <input type="password" name="current_password" required placeholder="••••••••" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500">
      </div>

      <!-- Field 2: Kata Sandi Baru -->
      <div>
        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Kata Sandi Baru <span class="text-rose-500">*</span></label>
        <input type="password" name="new_password" required placeholder="Minimal 8 karakter" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500">
      </div>

      <!-- Field 3: Konfirmasi Kata Sandi Baru -->
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
@endsection