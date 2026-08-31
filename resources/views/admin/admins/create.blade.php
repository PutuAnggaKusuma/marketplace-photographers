@extends('layouts.admin')

@section('title', 'Tambah Admin & Staff — Admin')

@section('content')
<div class="space-y-6">

    <!-- Standard Golden Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <span class="text-[11px] font-extrabold uppercase tracking-widest text-amber-600 dark:text-amber-500 block mb-1">
                Otoritas & Hak Akses
            </span>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">
                Tambah Akun Admin / Staff Baru
            </h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-1">
                Daftarkan akun pengelola sistem internal platform dengan hak akses operasional.
            </p>
        </div>
        <div>
            <a href="{{ route('admin.admins') }}" class="px-4 py-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200/80 dark:border-amber-900/60 font-extrabold text-xs transition inline-flex items-center justify-center shrink-0">Kembali ke Daftar Admin</a>
        </div>
    </div>

    <!-- Main Vertical Form Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm p-6 sm:p-8">
        <form action="{{ route('admin.admins.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Hidden Role: Otomatis Admin Operasional (Rule #27) -->
            <input type="hidden" name="role" value="admin">

            <!-- 1. Nama Lengkap -->
            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                    Nama Lengkap Admin <span class="text-rose-500">*</span>
                </label>
                <input type="text" 
                       name="name" 
                       value="{{ old('name') }}"
                       required 
                       placeholder="Contoh: Budi Pratama Santoso" 
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition @error('name') border-rose-500 @enderror">
                @error('name')
                    <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 2. Alamat Email -->
            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                    Alamat Email Login <span class="text-rose-500">*</span>
                </label>
                <input type="email" 
                       name="email" 
                       value="{{ old('email') }}"
                       required 
                       placeholder="admin.budi@lensmatch.com" 
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition @error('email') border-rose-500 @enderror">
                @error('email')
                    <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 3. Kata Sandi -->
            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                    Kata Sandi Akun <span class="text-rose-500">*</span>
                </label>
                <input type="password" 
                       name="password" 
                       required 
                       placeholder="Minimal 8 karakter" 
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition @error('password') border-rose-500 @enderror">
                @error('password')
                    <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 4. Konfirmasi Kata Sandi -->
            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                    Konfirmasi Kata Sandi <span class="text-rose-500">*</span>
                </label>
                <input type="password" 
                       name="password_confirmation" 
                       required 
                       placeholder="Ulangi kata sandi di atas" 
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">
            </div>

            <!-- Single Action Button (Simpan Hijau Emerald - Rule #23) -->
            <div class="pt-4 flex justify-end">
                <button type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-extrabold text-xs shadow-sm transition inline-flex items-center justify-center gap-2 cursor-pointer">
                    <span>Simpan Akun Admin</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection