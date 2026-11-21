@extends('layouts.admin')

@section('title', 'Detail Pengelola: ' . $admin->nama . ' — LensMatch')

@section('content')
<div class="space-y-6">

    <!-- Standard Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <!-- Breadcrumb Navigation (Rule #62) -->
            <nav class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 mb-1.5">
                <a href="{{ route('admin.admins') }}" class="hover:text-amber-500 transition">Manajemen Admin</a>
                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                <span class="text-gray-900 dark:text-white font-bold">Detail Pengelola</span>
            </nav>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">
                {{ $admin->nama }}
            </h1>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.admins.edit', $admin->id) }}" class="px-3.5 py-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200/80 dark:border-amber-900/60 font-bold text-xs transition inline-flex items-center gap-1.5 shadow-2xs shrink-0 cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                <span>Edit Data</span>
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
                        class="px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200/80 dark:border-rose-900/60 font-bold text-xs transition inline-flex items-center gap-1.5 shadow-2xs shrink-0 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                    <span>Hapus</span>
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
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-lg font-black text-gray-900 dark:text-white">{{ $admin->nama }}</h2>
                        @if($admin->role === 'super_admin')
                            <span class="px-2.5 py-0.5 rounded-lg bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200/80 dark:border-amber-900/60 font-bold text-xs">
                                Super Admin
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-lg bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 font-bold text-xs">
                                Admin Operasional
                            </span>
                        @endif

                        @if($admin->is_protected)
                            <span class="px-2 py-0.5 rounded-md bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300 text-[10px] font-extrabold">
                                Protected
                            </span>
                        @endif

                        @if(auth()->id() === $admin->id)
                            <span class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 text-[10px] font-bold">
                                Akun Anda
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $admin->email }}</p>
                </div>
            </div>

            <div class="flex items-center gap-2 text-xs">
                @if($admin->email_verified_at)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 font-bold">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Terverifikasi
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300 font-bold">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
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
