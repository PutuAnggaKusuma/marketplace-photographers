@extends('layouts.app')

@section('title', 'Pengaturan Akun & Profil — LensMatch')

@section('content')
<section class="py-12 bg-white dark:bg-gray-900 min-h-screen">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

    @php
      $rawAvatar = $photographer?->foto ?: ($client?->foto ?: $user?->foto);
      $avatarUrl = $rawAvatar ? (str_starts_with($rawAvatar, 'http') ? $rawAvatar : asset('storage/' . $rawAvatar)) : '';
    @endphp

    <!-- Section 1: Informasi Biodata Profil (Hybrid Responsive Form Standard) -->
    <div x-data="clientProfileApp()" class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-3xl border border-gray-200 dark:border-gray-700 shadow-sm space-y-6">
      <h2 class="text-lg font-black text-gray-900 dark:text-white">Informasi Profil</h2>

      <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" @submit="submitProfile($event)" novalidate class="space-y-6">
        @csrf

        <!-- Horizontal Row 1: Nama Lengkap & Alamat Email -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
            <input type="text" 
                   name="nama" 
                   x-ref="fieldNama"
                   x-model="form.nama"
                   @input="touched.nama = true; validateNama()"
                   @blur="touched.nama = true; validateNama()"
                   required 
                   placeholder="Masukkan nama lengkap"
                   class="w-full px-4 py-2.5 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition"
                   :class="touched.nama && errors.nama ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500'">
            <p x-show="touched.nama && errors.nama" x-text="errors.nama" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Alamat Email <span class="text-rose-500">*</span></label>
            <input type="email" 
                   name="email" 
                   x-ref="fieldEmail"
                   x-model="form.email"
                   @input="touched.email = true; validateEmail()"
                   @blur="touched.email = true; validateEmail()"
                   required 
                   placeholder="nama@email.com"
                   class="w-full px-4 py-2.5 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition"
                   :class="touched.email && errors.email ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500'">
            <p x-show="touched.email && errors.email" x-text="errors.email" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
          </div>
        </div>

        <!-- Horizontal Row 2: Nomor Telepon / WA & Domisili Kota / Peran -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Nomor Telepon / WhatsApp</label>
            <input type="tel" 
                   name="nomor_telepon" 
                   x-ref="fieldPhone"
                   x-model="form.nomor_telepon"
                   inputmode="numeric"
                   pattern="[0-9]*"
                   @keypress="if (!/[0-9]/.test($event.key)) $event.preventDefault()"
                   @input="form.nomor_telepon = (form.nomor_telepon || '').replace(/\D/g, ''); touched.nomor_telepon = true; validatePhone()"
                   @blur="touched.nomor_telepon = true; validatePhone()"
                   placeholder="Contoh: 08xxxxxxxxxx" 
                   class="w-full px-4 py-2.5 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition"
                   :class="touched.nomor_telepon && errors.nomor_telepon ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500'">
            <p x-show="touched.nomor_telepon && errors.nomor_telepon" x-text="errors.nomor_telepon" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
          </div>

          @if($user->role === 'photographer')
            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Alamat / Domisili Kota</label>
              <input type="text" name="alamat" x-model="form.alamat" placeholder="Masukkan alamat domisili" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500">
              @error('alamat')
                <p class="text-[11px] font-bold text-red-500 mt-1">{{ $message }}</p>
              @enderror
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
            <textarea name="deskripsi_bio" x-model="form.deskripsi_bio" rows="6" placeholder="Tuliskan deskripsi lengkap mengenai pengalaman fotografi, spesialisasi, dan pendekatan artistik Anda..." class="w-full min-h-[150px] px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none resize-y focus:ring-2 focus:ring-amber-500 leading-relaxed"></textarea>
            @error('deskripsi_bio')
              <p class="text-[11px] font-bold text-red-500 mt-1">{{ $message }}</p>
            @enderror
          </div>
        @endif

        <!-- Vertical Component: Foto Avatar Profil (Rule #97: Standarisasi Pratinjau Modal Lightbox) -->
        <div class="p-5 rounded-2xl bg-gray-50 dark:bg-gray-900/60 border border-gray-200 dark:border-gray-700/80 space-y-3">
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">
                Foto Avatar Profil
            </label>
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="relative w-24 h-24 sm:w-28 sm:h-28 rounded-2xl overflow-hidden bg-gray-100 dark:bg-gray-800 border-2 shadow-xs shrink-0 flex items-center justify-center transition"
                     :class="uploadError ? 'border-red-400 border-solid' : (preview ? 'border-amber-400 border-solid cursor-pointer group' : 'border-dashed border-gray-300 dark:border-gray-600')"
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
                        <input type="file" name="foto_file" accept="image/jpeg,image/png,image/webp" @change="handleFile($event)" class="hidden">
                    </label>
                    <p x-show="uploadError" x-text="uploadError" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
                    @error('foto_file')
                        <p class="text-[11px] font-bold text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-[11px] text-gray-500 dark:text-gray-400">File gambar dari perangkat (JPG, PNG, WEBP maks. 10 MB)</p>
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
    <div x-data="clientPasswordApp()" class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-3xl border border-gray-200 dark:border-gray-700 shadow-sm space-y-6">
      <h2 class="text-lg font-black text-gray-900 dark:text-white">Keamanan & Kata Sandi</h2>

      <form action="{{ route('profile.password') }}" method="POST" @submit="submitPassword($event)" novalidate class="space-y-6">
        @csrf

        <!-- Horizontal Row: 3 Password Inputs Side-by-Side -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Kata Sandi Saat Ini <span class="text-rose-500">*</span></label>
            <input type="password" 
                   name="current_password" 
                   x-ref="fieldCurrentPassword"
                   x-model="form.current_password"
                   @input="touched.current_password = true; validateCurrent()"
                   @blur="touched.current_password = true; validateCurrent()"
                   required 
                   placeholder="••••••••" 
                   class="w-full px-4 py-2.5 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition"
                   :class="touched.current_password && errors.current_password ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500'">
            <p x-show="touched.current_password && errors.current_password" x-text="errors.current_password" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Kata Sandi Baru <span class="text-rose-500">*</span></label>
            <input type="password" 
                   name="new_password" 
                   x-ref="fieldNewPassword"
                   x-model="form.new_password"
                   @input="touched.new_password = true; validateNew()"
                   @blur="touched.new_password = true; validateNew()"
                   required 
                   placeholder="Minimal 8 karakter" 
                   class="w-full px-4 py-2.5 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition"
                   :class="touched.new_password && errors.new_password ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500'">
            <p x-show="touched.new_password && errors.new_password" x-text="errors.new_password" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Konfirmasi Kata Sandi Baru <span class="text-rose-500">*</span></label>
            <input type="password" 
                   name="new_password_confirmation" 
                   x-ref="fieldNewPasswordConfirmation"
                   x-model="form.new_password_confirmation"
                   @input="touched.new_password_confirmation = true; validateConfirmation()"
                   @blur="touched.new_password_confirmation = true; validateConfirmation()"
                   required 
                   placeholder="Ulangi kata sandi baru" 
                   class="w-full px-4 py-2.5 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition"
                   :class="touched.new_password_confirmation && errors.new_password_confirmation ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500'">
            <p x-show="touched.new_password_confirmation && errors.new_password_confirmation" x-text="errors.new_password_confirmation" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
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

<script>
function clientProfileApp() {
  return {
    preview: '{{ $avatarUrl }}',
    imagePreviewModalOpen: false,
    uploadError: '',
    handleFile(e) {
      const res = window.validateUploadFiles(e.target, { maxMB: 10 });
      if (res.hasErrors) {
        this.uploadError = res.errorMessage;
        return;
      }
      const file = res.validFiles[0];
      if (file) {
        this.uploadError = '';
        this.preview = URL.createObjectURL(file);
      }
    },
    form: {
      nama: {!! json_encode(old('nama', $user->nama ?? '')) !!},
      email: {!! json_encode(old('email', $user->email ?? '')) !!},
      nomor_telepon: {!! json_encode(old('nomor_telepon', $photographer?->nomor_telepon ?: $client?->nomor_telepon ?: '')) !!},
      alamat: {!! json_encode(old('alamat', $photographer?->alamat ?? '')) !!},
      deskripsi_bio: {!! json_encode(old('deskripsi_bio', $photographer?->deskripsi_bio ?? '')) !!},
    },
    touched: {
      nama: {{ $errors->has('nama') ? 'true' : 'false' }},
      email: {{ $errors->has('email') ? 'true' : 'false' }},
      nomor_telepon: {{ $errors->has('nomor_telepon') ? 'true' : 'false' }},
    },
    errors: {
      nama: {!! json_encode($errors->first('nama') ?: '') !!},
      email: {!! json_encode($errors->first('email') ?: '') !!},
      nomor_telepon: {!! json_encode($errors->first('nomor_telepon') ?: '') !!},
    },
    validateNama() {
      const val = (this.form.nama || '').trim();
      if (!val) {
        this.errors.nama = 'Nama lengkap wajib diisi.';
      } else if (val.length < 3) {
        this.errors.nama = 'Nama lengkap minimal 3 karakter.';
      } else {
        this.errors.nama = '';
      }
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
    validatePhone() {
      this.form.nomor_telepon = (this.form.nomor_telepon || '').replace(/\D/g, '');
      const raw = this.form.nomor_telepon;
      if (raw && raw.length < 9) {
        this.errors.nomor_telepon = 'Nomor WhatsApp minimal 9 digit angka.';
      } else if (raw && raw.length > 20) {
        this.errors.nomor_telepon = 'Nomor WhatsApp maksimal 20 digit angka.';
      } else {
        this.errors.nomor_telepon = '';
      }
    },
    submitProfile(e) {
      this.touched.nama = true;
      this.touched.email = true;
      this.touched.nomor_telepon = true;
      this.validateNama();
      this.validateEmail();
      this.validatePhone();
      if (this.errors.nama || this.errors.email || this.errors.nomor_telepon) {
        e.preventDefault();
        if (this.errors.nama) this.$refs.fieldNama?.focus();
        else if (this.errors.email) this.$refs.fieldEmail?.focus();
        else if (this.errors.nomor_telepon) this.$refs.fieldPhone?.focus();
        return false;
      }
    }
  };
}

function clientPasswordApp() {
  return {
    form: {
      current_password: '',
      new_password: '',
      new_password_confirmation: ''
    },
    touched: {
      current_password: {{ $errors->has('current_password') ? 'true' : 'false' }},
      new_password: {{ $errors->has('new_password') ? 'true' : 'false' }},
      new_password_confirmation: {{ $errors->has('new_password_confirmation') ? 'true' : 'false' }}
    },
    errors: {
      current_password: {!! json_encode($errors->first('current_password') ?: '') !!},
      new_password: {!! json_encode($errors->first('new_password') ?: '') !!},
      new_password_confirmation: {!! json_encode($errors->first('new_password_confirmation') ?: '') !!}
    },
    validateCurrent() {
      if (!this.form.current_password) {
        this.errors.current_password = 'Kata sandi saat ini wajib diisi.';
      } else {
        this.errors.current_password = '';
      }
    },
    validateNew() {
      const val = this.form.new_password || '';
      if (!val) {
        this.errors.new_password = 'Kata sandi baru wajib diisi.';
      } else if (val.length < 8) {
        this.errors.new_password = 'Kata sandi baru minimal 8 karakter.';
      } else {
        this.errors.new_password = '';
      }
      if (this.touched.new_password_confirmation) {
        this.validateConfirmation();
      }
    },
    validateConfirmation() {
      const conf = this.form.new_password_confirmation || '';
      if (!conf) {
        this.errors.new_password_confirmation = 'Konfirmasi kata sandi baru wajib diisi.';
      } else if (conf !== this.form.new_password) {
        this.errors.new_password_confirmation = 'Konfirmasi kata sandi baru tidak cocok.';
      } else {
        this.errors.new_password_confirmation = '';
      }
    },
    submitPassword(e) {
      this.touched.current_password = true;
      this.touched.new_password = true;
      this.touched.new_password_confirmation = true;
      this.validateCurrent();
      this.validateNew();
      this.validateConfirmation();
      if (this.errors.current_password || this.errors.new_password || this.errors.new_password_confirmation) {
        e.preventDefault();
        if (this.errors.current_password) this.$refs.fieldCurrentPassword?.focus();
        else if (this.errors.new_password) this.$refs.fieldNewPassword?.focus();
        else if (this.errors.new_password_confirmation) this.$refs.fieldNewPasswordConfirmation?.focus();
        return false;
      }
    }
  };
}
</script>
@endsection
