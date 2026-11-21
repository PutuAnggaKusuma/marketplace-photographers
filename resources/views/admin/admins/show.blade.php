@extends('layouts.admin')

@section('title', 'Detail Pengelola: ' . $admin->nama . ' — LensMatch')

@section('content')
<div class="space-y-6">

    <!-- Standard Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <!-- Breadcrumb Navigation (Rule #62: Abu-abu netral seragam tanpa highlight tebal) -->
            <nav class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 mb-1.5">
                <a href="{{ route('admin.admins') }}" class="hover:text-amber-500 transition">Manajemen Admin</a>
                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                <span class="text-gray-400 dark:text-gray-500 font-bold">Detail Pengelola</span>
            </nav>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">
                {{ $admin->nama }}
            </h1>
        </div>

        <div class="flex items-center gap-2">
            <!-- Tombol Edit Baku (Rule #80: Teks Tunggal 'Edit', Rule #22: Pure Text Tanpa Icon) -->
            <a href="{{ route('admin.admins.edit', $admin->id) }}" class="px-4 py-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200/80 dark:border-amber-900/60 font-bold text-xs transition inline-flex items-center justify-center shadow-2xs shrink-0 cursor-pointer">
                Edit
            </a>

            @if(auth()->id() !== $admin->id && !$admin->is_protected && $admin->role !== 'super_admin')
                <button type="button" 
                        @click="$dispatch('open-confirm-modal', {
                            title: 'Hapus Akun Pengelola?',
                            message: 'Apakah Anda yakin ingin menghapus akun pengelola {{ addslashes($admin->nama) }}? Tindakan ini tidak dapat dibatalkan.',
                            buttonText: 'Ya, Hapus Admin',
                            iconType: 'delete',
                            buttonClass: 'bg-red-600 hover:bg-red-700 text-white',
                            actionUrl: '{{ route('admin.admins.destroy', $admin->id) }}',
                            method: 'POST'
                        })"
                        class="px-4 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200/80 dark:border-rose-900/60 font-bold text-xs transition inline-flex items-center justify-center shadow-2xs shrink-0 cursor-pointer">
                    Hapus
                </button>
            @endif

            <a href="{{ route('admin.admins') }}" class="px-4 py-2 rounded-xl bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-750 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 font-bold text-xs transition inline-flex items-center gap-1.5 shadow-2xs shrink-0 cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"></path></svg>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <!-- Main Profile Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-xs p-6 sm:p-8 space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-gray-100 dark:border-gray-700/60">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-amber-400 text-gray-950 font-black text-lg flex items-center justify-center shadow-xs shrink-0 select-none">
                    {{ strtoupper(substr($admin->nama, 0, 2)) }}
                </div>
                <div>
                    <div class="flex items-center gap-3 flex-wrap">
                        <h2 class="text-lg font-black text-gray-900 dark:text-white">{{ $admin->nama }}</h2>
                        
                        <!-- Role Akun Teks Murni (Rule #11 Standard) -->
                        @if($admin->role === 'super_admin')
                            <span class="text-xs font-extrabold text-amber-600 dark:text-amber-400">
                                Super Admin
                            </span>
                        @else
                            <span class="text-xs font-bold text-blue-600 dark:text-blue-400">
                                Admin Operasional
                            </span>
                        @endif

                        @if($admin->is_protected)
                            <span class="text-[11px] font-bold text-gray-400 dark:text-gray-500">
                                (Protected)
                            </span>
                        @endif

                        @if(auth()->id() === $admin->id)
                            <span class="text-[11px] font-bold text-blue-600 dark:text-blue-400">
                                • Akun Anda
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $admin->email }}</p>
                </div>
            </div>

            <!-- Status Email Teks Murni Tanpa Dot / Bulatan Status (Rule #14 & Rule #67 Item 4 Standard) -->
            <div class="flex items-center text-xs">
                @if($admin->email_verified_at)
                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">
                        Terverifikasi
                    </span>
                @else
                    <span class="text-xs font-bold text-amber-600 dark:text-amber-400">
                        Belum Verifikasi
                    </span>
                @endif
            </div>
        </div>

        <!-- Detail Data Grid (Rule #73 Standard) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div>
                <span class="block text-[11px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">Nama Lengkap</span>
                <p class="text-xs font-bold text-gray-900 dark:text-white">{{ $admin->nama }}</p>
            </div>

            <div>
                <span class="block text-[11px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">Alamat Email</span>
                <p class="text-xs font-bold text-gray-900 dark:text-white">{{ $admin->email }}</p>
            </div>

            <div>
                <span class="block text-[11px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">Nomor Telepon</span>
                <p class="text-xs font-bold text-gray-900 dark:text-white">{{ $admin->roleAdmin?->no_telepon ?: '-' }}</p>
            </div>

            <div>
                <span class="block text-[11px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">Role Akun</span>
                <p class="text-xs font-bold text-gray-900 dark:text-white">
                    {{ $admin->role === 'super_admin' ? 'Super Admin' : 'Admin Operasional' }}
                </p>
            </div>

            <div>
                <span class="block text-[11px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">Tanggal Terdaftar</span>
                <p class="text-xs font-bold text-gray-900 dark:text-white">
                    {{ $admin->created_at ? $admin->created_at->format('d M Y, H:i') : '-' }}
                </p>
            </div>

            <div>
                <span class="block text-[11px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">Terakhir Diperbaharui</span>
                <p class="text-xs font-bold text-gray-900 dark:text-white">
                    {{ $admin->updated_at ? $admin->updated_at->format('d M Y, H:i') : '-' }}
                </p>
            </div>
        </div>

    </div>

    <!-- Activity Log Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-xs p-6 sm:p-8 space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white">Riwayat Aktivitas Terakhir</h3>
            <span class="text-xs text-gray-400 font-medium">{{ $activities->count() }} aktivitas</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-[11px] font-extrabold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        <th class="py-3 px-4">Waktu</th>
                        <th class="py-3 px-4">Tipe Aktivitas</th>
                        <th class="py-3 px-4">Deskripsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60 text-xs">
                    @forelse($activities as $act)
                        <tr class="hover:bg-amber-50/30 dark:hover:bg-amber-950/20 transition">
                            <td class="py-3 px-4 text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                {{ $act->created_at ? $act->created_at->format('d M Y, H:i') : '-' }}
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="font-bold text-gray-800 dark:text-gray-200">{{ $act->log_name }}</span>
                            </td>
                            <td class="py-3 px-4 text-gray-700 dark:text-gray-300">
                                {{ $act->description }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-8 text-center text-gray-400 font-medium text-xs">
                                Belum ada catatan aktivitas untuk pengelola ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
