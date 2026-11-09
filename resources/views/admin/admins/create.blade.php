@extends('layouts.admin')



@section('title', 'Tambah Admin & Staff — Admin')



@section('content')

<div class="space-y-6">



    <!-- Standard Golden Header -->

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

        <div>
            <!-- Breadcrumb Navigation -->
            <nav class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 mb-1.5">
                <a href="{{ route('admin.admins') }}" class="hover:text-amber-500 transition">Kelola Admin</a>
                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                <span class="text-gray-900 dark:text-white font-bold">Tambah Admin</span>
            </nav>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">
                Tambah Akun Admin / Staff Baru
            </h1>
        </div>

        <div>

            <a href="{{ route('admin.admins') }}" class="px-4 py-2 rounded-xl bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-750 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 font-bold text-xs transition inline-flex items-center gap-1.5 shadow-2xs shrink-0">

                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"></path></svg>

                <span>Kembali</span>

            </a>

        </div>

    </div>



    <!-- Main Vertical Form Card -->

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm p-6 sm:p-8"
         x-data="adminCreateApp()">

        <form action="{{ route('admin.admins.store') }}" method="POST" @submit="submitForm($event)" novalidate class="space-y-5">

            @csrf



            <!-- Hidden Role: Otomatis Admin Operasional (Rule #27) -->

            <input type="hidden" name="role" value="admin">



            <!-- Row 1: Nama Lengkap & Email (Horizontal 2 Kolom) -->

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div>

                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">

                    Nama Lengkap Admin <span class="text-rose-500">*</span>

                </label>

                <input type="text" 
                       name="nama" 
                       x-ref="fieldNama"
                       x-model="form.nama"
                       @input="touched.nama = true; validateNama()"
                       @blur="touched.nama = true; validateNama()"
                       placeholder="Masukkan nama lengkap admin" 
                       class="w-full px-4 py-3 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition"
                       :class="touched.nama && errors.nama ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500 focus:border-amber-500'">
                <p x-show="touched.nama && errors.nama" x-text="errors.nama" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>

            </div>



            <div>

                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">

                    Alamat Email Login <span class="text-rose-500">*</span>

                </label>

                <input type="email" 
                       name="email" 
                       x-ref="fieldEmail"
                       x-model="form.email"
                       @input="touched.email = true; validateEmail()"
                       @blur="touched.email = true; validateEmail()"
                       placeholder="Masukkan alamat email admin" 
                       class="w-full px-4 py-3 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition"
                       :class="touched.email && errors.email ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500 focus:border-amber-500'">
                <p x-show="touched.email && errors.email" x-text="errors.email" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>

            </div>



            </div>



            <!-- Row 2: Kata Sandi & Konfirmasi (Horizontal 2 Kolom) -->

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div>

                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">

                    Kata Sandi Akun <span class="text-rose-500">*</span>

                </label>

                <div class="relative">
                    <input :type="showPassword ? 'text' : 'password'" 
                           name="password" 
                           x-ref="fieldPassword"
                           x-model="form.password"
                           @input="touched.password = true; validatePassword()"
                           @blur="touched.password = true; validatePassword()"
                           placeholder="Minimal 8 karakter" 
                           class="w-full px-4 py-3 pr-11 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition"
                           :class="touched.password && errors.password ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500 focus:border-amber-500'">
                    <button type="button" @click="showPassword = !showPassword" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <svg x-show="showPassword" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                    </button>
                </div>
                <p x-show="touched.password && errors.password" x-text="errors.password" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>

            </div>



            <div>

                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">

                    Konfirmasi Kata Sandi <span class="text-rose-500">*</span>

                </label>

                <div class="relative">
                    <input :type="showPasswordConfirmation ? 'text' : 'password'" 
                           name="password_confirmation" 
                           x-ref="fieldPasswordConfirmation"
                           x-model="form.password_confirmation"
                           @input="touched.password_confirmation = true; validatePasswordConfirmation()"
                           @blur="touched.password_confirmation = true; validatePasswordConfirmation()"
                           placeholder="Ulangi kata sandi di atas" 
                           class="w-full px-4 py-3 pr-11 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition"
                           :class="touched.password_confirmation && errors.password_confirmation ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500 focus:border-amber-500'">
                    <button type="button" @click="showPasswordConfirmation = !showPasswordConfirmation" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg x-show="!showPasswordConfirmation" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <svg x-show="showPasswordConfirmation" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                    </button>
                </div>
                <p x-show="touched.password_confirmation && errors.password_confirmation" x-text="errors.password_confirmation" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>

            </div>



            </div>



            <!-- Action Button Footer (Rule #74 Standard: Batal di samping kiri tombol submit, rata kanan gap-3, Rule #41: No border-t Divider) -->
            <div class="flex items-center justify-end gap-3 pt-4">
                <a href="{{ route('admin.admins') }}" class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold text-xs transition text-center shadow-2xs cursor-pointer">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs shadow-xs transition cursor-pointer">
                    Simpan Data
                </button>
            </div>

        </form>

    </div>



</div>

<script>
function adminCreateApp() {
    return {
        showPassword: false,
        showPasswordConfirmation: false,
        form: {
            nama: @js(old('nama', old('name', ''))),
            email: @js(old('email', '')),
            password: '',
            password_confirmation: ''
        },
        touched: {
            nama: {{ $errors->has('nama') || $errors->has('name') ? 'true' : 'false' }},
            email: {{ $errors->has('email') ? 'true' : 'false' }},
            password: {{ $errors->has('password') ? 'true' : 'false' }},
            password_confirmation: false
        },
        errors: {
            nama: '{{ $errors->first('nama') ?: ($errors->first('name') ?: '') }}',
            email: '{{ $errors->first('email') ?: '' }}',
            password: '{{ $errors->first('password') ?: '' }}',
            password_confirmation: ''
        },
        validateNama() {
            if (!this.form.nama || this.form.nama.trim() === '') {
                this.errors.nama = 'Nama lengkap admin wajib diisi.';
            } else if (this.form.nama.trim().length < 3) {
                this.errors.nama = 'Nama lengkap minimal 3 karakter.';
            } else {
                this.errors.nama = '';
            }
        },
        validateEmail() {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!this.form.email || this.form.email.trim() === '') {
                this.errors.email = 'Alamat email wajib diisi.';
            } else if (!re.test(this.form.email.trim())) {
                this.errors.email = 'Format alamat email tidak valid.';
            } else {
                this.errors.email = '';
            }
        },
        validatePassword() {
            if (!this.form.password) {
                this.errors.password = 'Kata sandi akun wajib diisi.';
            } else if (this.form.password.length < 8) {
                this.errors.password = 'Kata sandi minimal 8 karakter.';
            } else {
                this.errors.password = '';
            }
            if (this.touched.password_confirmation) {
                this.validatePasswordConfirmation();
            }
        },
        validatePasswordConfirmation() {
            if (!this.form.password_confirmation) {
                this.errors.password_confirmation = 'Konfirmasi kata sandi wajib diisi.';
            } else if (this.form.password_confirmation !== this.form.password) {
                this.errors.password_confirmation = 'Konfirmasi kata sandi tidak cocok.';
            } else {
                this.errors.password_confirmation = '';
            }
        },
        submitForm(e) {
            this.touched.nama = true;
            this.touched.email = true;
            this.touched.password = true;
            this.touched.password_confirmation = true;

            this.validateNama();
            this.validateEmail();
            this.validatePassword();
            this.validatePasswordConfirmation();

            let firstInvalidRef = null;
            if (this.errors.nama) firstInvalidRef = firstInvalidRef || this.$refs.fieldNama;
            if (this.errors.email) firstInvalidRef = firstInvalidRef || this.$refs.fieldEmail;
            if (this.errors.password) firstInvalidRef = firstInvalidRef || this.$refs.fieldPassword;
            if (this.errors.password_confirmation) firstInvalidRef = firstInvalidRef || this.$refs.fieldPasswordConfirmation;

            if (this.errors.nama || this.errors.email || this.errors.password || this.errors.password_confirmation) {
                e.preventDefault();
                if (firstInvalidRef) {
                    firstInvalidRef.focus();
                    firstInvalidRef.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        }
    };
}
</script>

@endsection