@extends('layouts.admin')

@section('title', 'Moderasi Forum Admin - LensMatch')

@section('content')
<div class="space-y-6">

    <!-- Page Title Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <span class="text-[11px] font-extrabold uppercase tracking-widest text-amber-600 dark:text-amber-400">Pusat Komunitas & Diskusi</span>
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white tracking-tight">Moderasi Forum Komunitas</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Monitor postingan diskusi publik, tangani laporan spam, dan moderasi komentar anggota</p>
        </div>
    </div>

    <!-- 2 Stat Cards Summary -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <!-- Card 1: Total Topik Diskusi -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Topik Diskusi</span>
                <span class="p-2.5 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
                </span>
            </div>
            <div class="mt-2">
                <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">{{ $totalPosts }} Topik</h2>
                <p class="text-[11px] text-amber-600 dark:text-amber-400 font-semibold mt-1">Diskusi Komunitas Aktif</p>
            </div>
        </div>

        <!-- Card 2: Total Balasan Komentar -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Balasan Komentar</span>
                <span class="p-2.5 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                </span>
            </div>
            <div class="mt-2">
                <h2 class="text-2xl font-black text-blue-600 dark:text-blue-400 tracking-tight">{{ $totalComments }} Balasan</h2>
                <p class="text-[11px] text-gray-500 dark:text-gray-400 font-medium mt-1">Respon & Interaksi Anggota</p>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm overflow-hidden space-y-4">
        
        <!-- Search Toolbar -->
        <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between gap-4">
            <form method="GET" action="{{ route('admin.forum') }}" class="relative w-full sm:w-80">
                <input type="text" 
                       name="search" 
                       value="{{ $search }}" 
                       placeholder="Cari topik diskusi atau penulis..." 
                       class="w-full pl-9 pr-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-[11px] font-extrabold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        <th class="py-3.5 px-4 sm:px-6 w-5/12">Topik Diskusi</th>
                        <th class="py-3.5 px-4 sm:px-6 w-2/12">Penulis (User)</th>
                        <th class="py-3.5 px-4 sm:px-6 w-2/12">Tanggal Rilis</th>
                        <th class="py-3.5 px-4 sm:px-6 w-1/12 text-center">Balasan</th>
                        <th class="py-3.5 px-4 sm:px-6 w-2/12 text-center">Aksi Moderasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60 text-xs">
                    @forelse($posts as $post)
                        <tr class="hover:bg-amber-50/30 dark:hover:bg-amber-950/20 transition">
                            <!-- Judul & Ringkasan -->
                            <td class="py-4 px-4 sm:px-6 space-y-1">
                                <a href="{{ route('public.forum.show', $post->id) }}" target="_blank" class="font-extrabold text-gray-900 dark:text-white hover:text-amber-500 transition line-clamp-1">
                                    {{ $post->judul }}
                                </a>
                                <p class="text-[11px] text-gray-500 dark:text-gray-400 line-clamp-1 leading-relaxed">{{ $post->deskripsi }}</p>
                            </td>

                            <!-- Penulis -->
                            <td class="py-4 px-4 sm:px-6">
                                <div class="font-bold text-gray-900 dark:text-white">{{ $post->user->nama ?? 'Anggota Komunitas' }}</div>
                                <div class="text-[10px] text-gray-400 truncate">{{ $post->user->email ?? '-' }}</div>
                            </td>

                            <!-- Tanggal Rilis -->
                            <td class="py-4 px-4 sm:px-6 font-semibold text-gray-600 dark:text-gray-400 whitespace-nowrap">
                                {{ $post->created_at->format('d M Y, H:i') }}
                            </td>

                            <!-- Jumlah Balasan -->
                            <td class="py-4 px-4 sm:px-6 text-center whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 border border-blue-200/60 dark:border-blue-900/60">
                                    {{ $post->comments->count() }} Komentar
                                </span>
                            </td>

                            <!-- Aksi Moderasi -->
                            <td class="py-4 px-4 sm:px-6 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('public.forum.show', $post->id) }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300 font-bold text-xs transition flex items-center gap-1">
                                        <span>Lihat</span>
                                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    </a>
                                    <button type="button" 
                                            @click="$dispatch('open-confirm-modal', {
                                                title: 'Hapus Postingan Forum Spam?',
                                                message: 'Apakah Anda yakin ingin menghapus topik diskusi {{ addslashes($post->judul) }} beserta seluruh balasan komentarnya?',
                                                confirmText: 'Ya, Hapus Postingan',
                                                type: 'danger',
                                                actionUrl: '{{ route('admin.forum.destroy', $post->id) }}',
                                                method: 'POST'
                                            })"
                                            class="px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 dark:bg-rose-950/60 dark:text-rose-300 font-bold text-xs transition">
                                        Hapus Spam
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-gray-400 font-bold text-xs">
                                Belum ada postingan diskusi forum ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($posts->hasPages())
            <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                {{ $posts->links() }}
            </div>
        @endif
    </div>

</div>
@endsection