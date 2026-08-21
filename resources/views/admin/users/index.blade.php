@extends('layouts.admin')

@section('title', 'Manajemen Pengguna & Verifikasi Studio - LensMatch')

@section('content')
<div class="space-y-6">

    <!-- Page Title Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <span class="text-[11px] font-extrabold uppercase tracking-widest text-amber-600 dark:text-amber-400">Pusat Akun & Verifikasi</span>
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white tracking-tight">Manajemen Pengguna Platform</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Kelola data seluruh akun anggota, filter berdasarkan role, dan berikan Verifikasi Studio Centang Biru</p>
        </div>
    </div>

    <!-- 4 Stat Cards Summary -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Pengguna -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total User</span>
                <span class="p-2.5 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </span>
            </div>
            <div class="mt-2">
                <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">{{ $totalUsers }} User</h2>
                <p class="text-[11px] text-gray-500 dark:text-gray-400 font-medium mt-1">Anggota Komunitas LensMatch</p>
            </div>
        </div>

        <!-- Card 2: Total Klien -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Akun Klien</span>
                <span class="p-2.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </span>
            </div>
            <div class="mt-2">
                <h2 class="text-2xl font-black text-emerald-600 dark:text-emerald-400 tracking-tight">{{ $totalClients }} Klien</h2>
                <p class="text-[11px] text-gray-500 dark:text-gray-400 font-medium mt-1">Penyewa Jasa Fotografi</p>
            </div>
        </div>

        <!-- Card 3: Total Studio Fotografer -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Studio Fotografer</span>
                <span class="p-2.5 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                </span>
            </div>
            <div class="mt-2">
                <h2 class="text-2xl font-black text-amber-600 dark:text-amber-400 tracking-tight">{{ $totalPhotographers }} Studio</h2>
                <p class="text-[11px] text-gray-500 dark:text-gray-400 font-medium mt-1">Penyedia Jasa Fotografi</p>
            </div>
        </div>

        <!-- Card 4: Studio Terverifikasi (Centang Biru) -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Studio Verified</span>
                <span class="p-2.5 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </span>
            </div>
            <div class="mt-2">
                <h2 class="text-2xl font-black text-blue-600 dark:text-blue-400 tracking-tight">{{ $totalVerifiedPhotographers }} Verified</h2>
                <p class="text-[11px] text-blue-600 dark:text-blue-400 font-semibold mt-1">Badge Centang Biru Aktif</p>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm overflow-hidden space-y-4">
        
        <!-- Search Toolbar & Filter -->
        <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row items-center justify-between gap-4">
            <form method="GET" action="{{ route('admin.users') }}" class="relative w-full sm:w-80">
                <input type="text" 
                       name="search" 
                       value="{{ $search }}" 
                       placeholder="Cari nama atau email pengguna..." 
                       class="w-full pl-9 pr-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </form>

            <div class="flex items-center gap-2 w-full sm:w-auto">
                <a href="{{ route('admin.users', ['role' => 'all']) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $roleFilter === 'all' ? 'bg-amber-500 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">Semua Role</a>
                <a href="{{ route('admin.users', ['role' => 'client']) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $roleFilter === 'client' ? 'bg-emerald-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">Klien</a>
                <a href="{{ route('admin.users', ['role' => 'photographer']) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $roleFilter === 'photographer' ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">Fotografer</a>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-[11px] font-extrabold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        <th class="py-3.5 px-4 sm:px-6 w-4/12">Profil Pengguna</th>
                        <th class="py-3.5 px-4 sm:px-6 w-2/12">Role Resmi</th>
                        <th class="py-3.5 px-4 sm:px-6 w-3/12">Status Verifikasi Studio</th>
                        <th class="py-3.5 px-4 sm:px-6 w-1/12">Bergabung</th>
                        <th class="py-3.5 px-4 sm:px-6 w-2/12 text-center">Aksi Moderasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60 text-xs">
                    @forelse($users as $usr)
                        <tr class="hover:bg-amber-50/30 dark:hover:bg-amber-950/20 transition">
                            <!-- User Profile -->
                            <td class="py-4 px-4 sm:px-6 flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-amber-500 to-amber-300 text-white font-black text-xs flex items-center justify-center shrink-0 shadow-xs">
                                    {{ strtoupper(substr($usr->nama ?? 'U', 0, 2)) }}
                                </div>
                                <div class="min-w-0">
                                    <h4 class="font-extrabold text-gray-900 dark:text-white truncate">{{ $usr->nama }}</h4>
                                    <p class="text-[10px] text-gray-400 truncate">{{ $usr->email }}</p>
                                </div>
                            </td>

                            <!-- Role Badge -->
                            <td class="py-4 px-4 sm:px-6 whitespace-nowrap">
                                @if($usr->role === 'client')
                                    <span class="px-2.5 py-1 rounded-md text-[10px] font-extrabold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200/60">
                                        Klien
                                    </span>
                                @elseif($usr->role === 'photographer')
                                    <span class="px-2.5 py-1 rounded-md text-[10px] font-extrabold bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-200/60">
                                        Fotografer
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-md text-[10px] font-extrabold bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 border border-purple-200/60">
                                        Admin
                                    </span>
                                @endif
                            </td>

                            <!-- Status Verifikasi Studio -->
                            <td class="py-4 px-4 sm:px-6 whitespace-nowrap">
                                @if($usr->role === 'photographer')
                                    @if($usr->rolePhotographer && $usr->rolePhotographer->is_verified)
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-blue-50 text-blue-600 dark:bg-blue-950/60 dark:text-blue-400 border border-blue-200/60 flex items-center gap-1 w-max">
                                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                            <span>Verified Studio ✓</span>
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400 border border-gray-200/60">
                                            Unverified
                                        </span>
                                    @endif
                                @else
                                    <span class="text-[11px] text-gray-400 font-medium">-</span>
                                @endif
                            </td>

                            <!-- Tanggal Bergabung -->
                            <td class="py-4 px-4 sm:px-6 text-gray-600 dark:text-gray-400 font-semibold whitespace-nowrap">
                                {{ $usr->created_at->format('d M Y') }}
                            </td>

                            <!-- Aksi Moderasi -->
                            <td class="py-4 px-4 sm:px-6 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    @if($usr->role === 'photographer')
                                        <form action="{{ route('admin.users.verify', $usr->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ ($usr->rolePhotographer && $usr->rolePhotographer->is_verified) ? 'bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100' : 'bg-blue-600 hover:bg-blue-700 text-white shadow-xs' }}">
                                                {{ ($usr->rolePhotographer && $usr->rolePhotographer->is_verified) ? 'Batal Verified' : 'Verifikasi Studio ✓' }}
                                            </button>
                                        </form>
                                    @endif

                                    @if(!$usr->is_protected && !in_array($usr->role, ['super_admin', 'admin']))
                                        <button type="button" 
                                                @click="$dispatch('open-confirm-modal', {
                                                    title: 'Hapus Akun Pengguna?',
                                                    message: 'Apakah Anda yakin ingin menghapus akun {{ addslashes($usr->nama) }} ({{ addslashes($usr->email) }})?',
                                                    confirmText: 'Ya, Hapus Akun',
                                                    type: 'danger',
                                                    actionUrl: '{{ route('admin.users.destroy', $usr->id) }}',
                                                    method: 'POST'
                                                })"
                                                class="px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 dark:bg-rose-950/60 dark:text-rose-300 font-bold text-xs transition">
                                            Hapus
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-gray-400 font-bold text-xs">
                                Belum ada akun pengguna ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>
@endsection