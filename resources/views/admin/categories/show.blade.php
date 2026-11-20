@extends('layouts.admin')

@section('title', "Detail Kategori {$category->nama_kategori} — Admin")

@section('content')
<div class="space-y-6 w-full" x-data="{
    searchQuery: '{{ $search }}',
    isLoading: false,
    searchTimeout: null,
    lightboxOpen: false,
    lightboxImage: '',
    lightboxTitle: '',
    handleSearch(e) {
        this.searchQuery = e.target.value;
        clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(() => {
            let url = new URL(window.location.href);
            if (this.searchQuery) {
                url.searchParams.set('search', this.searchQuery);
            } else {
                url.searchParams.delete('search');
            }
            url.searchParams.set('page', '1');
            this.fetchTable(url.toString());
        }, 300);
    },
    fetchTable(url) {
        this.isLoading = true;
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.text())
            .then(html => {
                let parser = new DOMParser();
                let doc = parser.parseFromString(html, 'text/html');
                let newTable = doc.querySelector('#dashboard-table-container');
                let curTable = document.querySelector('#dashboard-table-container');
                if (newTable && curTable) {
                    curTable.innerHTML = newTable.innerHTML;
                }
                history.pushState(null, '', url);
                this.isLoading = false;
            })
            .catch(err => {
                console.error(err);
                this.isLoading = false;
            });
    }
}">

    <!-- Header Section (Breadcrumb, Title, & Action Buttons) -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <nav class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 mb-1.5">
                <a href="{{ route('admin.categories') }}" class="hover:text-amber-500 transition">Kategori</a>
                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                <span class="text-gray-900 dark:text-white font-bold">{{ $category->nama_kategori }}</span>
            </nav>
            <h1 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">
                Detail Kategori Layanan
            </h1>
        </div>

        <div class="flex items-center gap-2.5 shrink-0">
            <a href="{{ route('admin.categories.edit', $category->id) }}"
               class="px-4 py-2 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs transition inline-flex items-center shadow-2xs">
                <span>Edit Kategori</span>
            </a>

            <a href="{{ route('admin.categories') }}"
               class="px-4 py-2 rounded-xl bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 font-bold text-xs transition inline-flex items-center gap-1.5 shadow-2xs">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <!-- Category Profile Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-2xs p-5 sm:p-6">
        <div class="flex flex-col sm:flex-row items-start gap-5">
            <div class="w-20 h-20 rounded-2xl overflow-hidden bg-gray-100 dark:bg-gray-900 border border-gray-200 dark:border-gray-700/80 shrink-0 flex items-center justify-center relative group">
                @if($category->icon_url)
                    <img src="{{ $category->icon_url }}" 
                         alt="{{ $category->nama_kategori }}" 
                         @click="lightboxImage = '{{ $category->icon_url }}'; lightboxTitle = '{{ addslashes($category->nama_kategori) }}'; lightboxOpen = true"
                         class="w-full h-full object-cover cursor-pointer group-hover:scale-105 transition duration-200">
                @else
                    <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z"/></svg>
                @endif
            </div>

            <div class="min-w-0 flex-1">
                <h2 class="text-base sm:text-lg font-black text-gray-900 dark:text-white tracking-tight">
                    {{ $category->nama_kategori }}
                </h2>
                <p class="text-xs text-gray-600 dark:text-gray-300 mt-2 leading-relaxed">
                    {{ $category->deskripsi ?: 'Belum ada deskripsi untuk kategori layanan ini.' }}
                </p>
            </div>
        </div>
    </div>

    <!-- 3 KPI Metric Cards (Rule #64 Compliant: 2 lines only, font text-xs uniform) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-2xs space-y-1.5">
            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Fotografer Terhubung</span>
            <div class="text-xs font-black text-gray-900 dark:text-white tracking-tight">{{ number_format($category->photographers_count ?? 0) }} Mitra</div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-2xs space-y-1.5">
            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Paket Layanan Terkait</span>
            <div class="text-xs font-black text-gray-900 dark:text-white tracking-tight">{{ number_format($totalServices ?? 0) }} Paket</div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-2xs space-y-1.5">
            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Portofolio Terkait</span>
            <div class="text-xs font-black text-gray-900 dark:text-white tracking-tight">{{ number_format($category->portfolios_count ?? 0) }} Karya</div>
        </div>
    </div>

    <!-- Linked Photographers Data Table Card (Rule #106 & Rule #107: 0 border-b on toolbar) -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-2xs overflow-hidden">
        <div class="p-4 sm:p-5 flex flex-col md:flex-row items-center justify-between gap-4">
            <div>
                <h3 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-wider">
                    Daftar Fotografer Terkait
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 font-medium">
                    Mitra fotografer yang terdaftar dan memilih spesialisasi {{ $category->nama_kategori }}
                </p>
            </div>

            <!-- Search di Kanan -->
            <div class="relative w-full md:w-80">
                <input type="text" 
                       name="search" 
                       value="{{ $search }}" 
                       @input="handleSearch($event)" 
                       placeholder="Cari fotografer atau nama pemilik..." 
                       class="w-full pl-4 pr-10 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">
                <svg class="w-4 h-4 text-gray-400 absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </div>

        <div id="dashboard-table-container" class="transition-opacity duration-200" :class="isLoading ? 'opacity-40 pointer-events-none' : 'opacity-100'">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-[11px] font-extrabold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            <th class="py-3.5 px-4 sm:px-6">Fotografer</th>
                            <th class="py-3.5 px-4 sm:px-6">Domisili</th>
                            <th class="py-3.5 px-4 sm:px-6 text-center">Paket Layanan</th>
                            <th class="py-3.5 px-4 sm:px-6 text-center">Status Akun</th>
                            <th class="py-3.5 px-4 sm:px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60 text-xs">
                        @forelse($photographers as $photo)
                            <tr class="hover:bg-amber-50/30 dark:hover:bg-amber-950/20 transition">
                                <!-- Studio Profile -->
                                <td class="py-4 px-4 sm:px-6">
                                    <div class="flex items-center gap-3 min-w-[220px]">
                                        @php
                                            $avatarPhoto = $photo->foto ? (str_starts_with($photo->foto, 'http') ? $photo->foto : asset('storage/' . $photo->foto)) : null;
                                        @endphp
                                        @if($avatarPhoto)
                                            <div class="w-10 h-10 rounded-xl overflow-hidden shrink-0 border border-gray-200 dark:border-gray-700 shadow-2xs">
                                                <img src="{{ $avatarPhoto }}" class="w-full h-full object-cover" alt="{{ $photo->nama }}">
                                            </div>
                                        @else
                                            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-amber-300 text-white font-black text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                                {{ strtoupper(substr($photo->nama ?? ($photo->user->nama ?? 'F'), 0, 2)) }}
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <span class="font-black text-gray-900 dark:text-white text-xs block truncate">{{ $photo->nama }}</span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400 block truncate">
                                                Pemilik: {{ $photo->user->nama ?? '-' }} ({{ $photo->user->email ?? '-' }})
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Domisili -->
                                <td class="py-4 px-4 sm:px-6 whitespace-nowrap">
                                    @if($photo->city)
                                        <div class="font-bold text-gray-800 dark:text-gray-200 text-xs">
                                            {{ $photo->city->name }}
                                        </div>
                                        <div class="text-xs text-gray-400">
                                            {{ $photo->province ? $photo->province->name : '' }}
                                        </div>
                                    @else
                                        <span class="text-gray-400 font-medium text-xs">-</span>
                                    @endif
                                </td>

                                <!-- Paket Layanan -->
                                <td class="py-4 px-4 sm:px-6 text-center whitespace-nowrap">
                                    <span class="font-bold text-gray-800 dark:text-gray-200 text-xs">
                                        {{ $photo->services_count ?? 0 }} Layanan
                                    </span>
                                </td>

                                <!-- Status Akun (Rule #14 & #67: Pure colored text, no pill badge, no dot) -->
                                <td class="py-4 px-4 sm:px-6 text-center whitespace-nowrap">
                                    @if($photo->status_verifikasi === 'disetujui')
                                        <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">Terverifikasi</span>
                                    @elseif($photo->status_verifikasi === 'ditolak')
                                        <span class="text-xs font-bold text-rose-600 dark:text-rose-400">Ditolak</span>
                                    @else
                                        <span class="text-xs font-bold text-amber-600 dark:text-amber-400">Menunggu Review</span>
                                    @endif
                                </td>

                                <!-- Aksi -->
                                <td class="py-4 px-4 sm:px-6 text-center whitespace-nowrap">
                                    <a href="{{ route('admin.photographers.show', ['id' => $photo->id, 'from' => 'category', 'category_id' => $category->id]) }}"
                                       class="px-3 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200/80 dark:border-amber-900/60 font-bold text-xs transition inline-block">
                                        Lihat Fotografer
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-gray-400 font-bold text-xs">
                                    Belum ada fotografer yang terhubung dengan kategori ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($photographers->total() > 0)
                <div class="p-4 bg-gray-50/50 dark:bg-gray-900/50 border-t border-gray-100 dark:border-gray-700/60">
                    {{ $photographers->links('partials.dashboard.pagination') }}
                </div>
            @endif
        </div>
    </div>

    <!-- Lightbox Pratinjau Resolusi Penuh (Rule #97 Compliant) -->
    <template x-teleport="body">
        <div x-show="lightboxOpen"
             x-cloak
             @keydown.escape.window="lightboxOpen = false"
             @click.self="lightboxOpen = false"
             class="fixed inset-0 z-[9999] bg-black/90 backdrop-blur-md flex flex-col items-center justify-center p-4 sm:p-8">
            <button type="button"
                    @click="lightboxOpen = false"
                    aria-label="Tutup pratinjau"
                    class="absolute top-4 right-4 sm:top-6 sm:right-6 w-11 h-11 rounded-full bg-white/10 hover:bg-white/25 text-white flex items-center justify-center transition cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <img :src="lightboxImage" :alt="lightboxTitle" class="max-w-full max-h-[82vh] object-contain rounded-2xl shadow-2xl border border-white/10 select-none">
        </div>
    </template>
</div>
@endsection
