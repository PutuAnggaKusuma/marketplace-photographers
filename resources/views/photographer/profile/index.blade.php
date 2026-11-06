@extends('layouts.photographer')

@section('title', 'Profil & Bio Fotografer — Dashboard')

@section('content')
<div class="space-y-6 w-full">

  <!-- Header Section -->
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
      <h1 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">Kelola Profil</h1>
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

  <!-- Section 1: Form Biodata & Media Fotografer (Hybrid Responsive Form Standard) -->
  <div class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-3xl border border-gray-200 dark:border-gray-700/80 shadow-xs space-y-6">
    <h2 class="text-lg font-black text-gray-900 dark:text-white">Informasi Profil</h2>

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
      @csrf

      <!-- Horizontal Row 1: Nama Lengkap & Alamat Email -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Nama<span class="text-rose-500">*</span></label>
          <input type="text" name="nama" value="{{ old('nama', $user->nama) }}" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500">
        </div>

        <div>
          <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Email <span class="text-rose-500">*</span></label>
          <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500">
        </div>
      </div>

      <!-- Horizontal Row 2: Nomor Telepon / WA & Alamat / Domisili Kota -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Nomor HP</label>
          <input type="text" name="nomor_telepon" value="{{ old('nomor_telepon', $photographer?->nomor_telepon) }}" placeholder="081234567890" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500">
        </div>

        <div>
          <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Alamat</label>
          <input type="text" name="alamat" value="{{ old('alamat', $photographer?->alamat) }}" placeholder="Jl. Sunset Road No. 88, Kuta, Bali" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500">
        </div>
      </div>

      <!-- Vertical Field: Deskripsi Bio Fotografer -->
      <div>
        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Deskripsi Bio</label>
        <textarea name="deskripsi_bio" rows="6" placeholder="Tuliskan deskripsi singkat mengenai pengalaman dan spesialisasi fotografi Anda..." class="w-full min-h-[150px] px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500 resize-y leading-relaxed">{{ old('deskripsi_bio', $photographer?->deskripsi_bio) }}</textarea>
      </div>

      <!-- Vertical Component: Foto Avatar Profil -->
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
                      <span>Upload Foto Avatar</span>
                      <input type="file" name="foto_file" accept="image/*" @change="handleFile($event)" class="hidden">
                  </label>
                  <p class="text-[11px] text-gray-500 dark:text-gray-400">File gambar avatar dari perangkat (JPG, PNG, WEBP maks. 10MB)</p>
              </div>
          </div>
      </div>

      <!-- Vertical Component: Foto Sampul / Cover Banner Profil -->
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
              Foto Sampul
          </label>
          <div class="flex flex-col sm:flex-row sm:items-center gap-4">
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

      <!-- Horizontal Box: Informasi Rekening Bank (3 Kolom Responsif) -->
      <div class="p-5 sm:p-6 rounded-2xl bg-gray-50 dark:bg-gray-900/60 border border-gray-200 dark:border-gray-700/80 space-y-4">
        <div>
          <h3 class="text-xs font-bold uppercase tracking-wider font-black text-gray-900 dark:text-white">Rekening Pencairan Dana (Payout)</h3>
          <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">Digunakan oleh Admin untuk mentransfer hak pendapatan 90% hasil booking sesi pemotretan Anda.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-1">
          <!-- Nama Bank (Alpine Custom Dropdown - Rule #11) -->
          <div x-data="{
            open: false,
            selectedBank: '{{ old('nama_bank', $photographer?->nama_bank ?? 'BCA') }}',
            banks: ['BCA', 'Bank Mandiri', 'BNI', 'BRI', 'BSI', 'CIMB Niaga', 'Permata Bank', 'Bank Danamon', 'Bank Jago', 'Jenius / BTPN']
          }" class="relative">
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Nama Bank Mitra</label>
            <input type="hidden" name="nama_bank" :value="selectedBank">
            <button type="button" 
                    @click="open = !open" 
                    class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500 text-left">
              <span x-text="selectedBank || 'Pilih Nama Bank'"></span>
              <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
            </button>
            <div x-show="open" 
                 @click.away="open = false" 
                 x-cloak 
                 class="absolute z-20 mt-1 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg py-1 max-h-48 overflow-y-auto">
              <template x-for="b in banks" :key="b">
                <button type="button" 
                        @click="selectedBank = b; open = false" 
                        class="w-full text-left px-4 py-2 text-xs hover:bg-amber-50 dark:hover:bg-amber-950/40 text-gray-800 dark:text-gray-200"
                        :class="{'font-bold text-amber-600 dark:text-amber-400 bg-amber-50/50 dark:bg-amber-950/30': selectedBank === b}"
                        x-text="b">
                </button>
              </template>
            </div>
          </div>

          <!-- Nomor Rekening -->
          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Nomor Rekening Bank</label>
            <input type="text" name="nomor_rekening" value="{{ old('nomor_rekening', $photographer?->nomor_rekening) }}" placeholder="Contoh: 1234567890" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500">
          </div>

          <!-- Atas Nama Rekening -->
          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Nama Pemilik Rekening</label>
            <input type="text" name="atas_nama_rekening" value="{{ old('atas_nama_rekening', $photographer?->atas_nama_rekening) }}" placeholder="Contoh: Budi Santoso" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500">
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
  <div class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-3xl border border-gray-200 dark:border-gray-700/80 shadow-xs space-y-6">
    <h2 class="text-lg font-black text-gray-900 dark:text-white">Perbaharui Kata Sandi</h2>

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
@endsection
