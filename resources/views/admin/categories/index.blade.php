@extends('layouts.admin')

@section('title', 'Manajemen Kategori Layanan Admin - LensMatch')

@section('content')
<div class="space-y-6" x-data="categoryModalApp()">

    <!-- Page Title Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <span class="text-[11px] font-extrabold uppercase tracking-widest text-amber-600 dark:text-amber-400">Master Data System</span>
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white tracking-tight">Kategori Layanan Fotografi</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Kelola master spesialisasi kategori fotografi yang tampil pada katalog pencarian publik</p>
        </div>

        <button type="button" 
                @click="openModal('create')" 
                class="px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-extrabold text-xs shadow-xs transition flex items-center justify-center gap-2 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
            <span>Tambah Kategori Baru</span>
        </button>
    </div>

    <!-- Stat Card Summary -->
    <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm flex items-center justify-between">
        <div>
            <span class="text-xs font-extrabold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Kategori Terdaftar</span>
            <h2 class="text-2xl font-black text-gray-900 dark:text-white mt-1">{{ $totalCategories }} Kategori</h2>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/60 text-amber-500 flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm overflow-hidden space-y-4">
        
        <!-- Search Toolbar -->
        <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between gap-4">
            <form method="GET" action="{{ route('admin.categories') }}" class="relative w-full sm:w-80">
                <input type="text" 
                       name="search" 
                       value="{{ $search }}" 
                       placeholder="Cari nama atau deskripsi kategori..." 
                       class="w-full pl-9 pr-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-[11px] font-extrabold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        <th class="py-3.5 px-4 sm:px-6 w-4/12">Kategori & Gambar</th>
                        <th class="py-3.5 px-4 sm:px-6 w-2/12">Slug System</th>
                        <th class="py-3.5 px-4 sm:px-6 w-4/12">Deskripsi Ringkas</th>
                        <th class="py-3.5 px-4 sm:px-6 w-2/12 text-center">Aksi Moderasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60 text-xs">
                    @forelse($categories as $cat)
                        <tr class="hover:bg-amber-50/30 dark:hover:bg-amber-950/20 transition">
                            <td class="py-4 px-4 sm:px-6 flex items-center gap-3">
                                <img src="{{ $cat->icon_url ?: 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=800&q=80' }}" alt="{{ $cat->nama_kategori }}" class="w-12 h-10 rounded-lg object-cover border border-gray-200 dark:border-gray-700 shrink-0 shadow-xs" />
                                <div>
                                    <h4 class="font-extrabold text-gray-900 dark:text-white">{{ $cat->nama_kategori }}</h4>
                                </div>
                            </td>

                            <td class="py-4 px-4 sm:px-6 font-mono text-[11px] text-amber-600 dark:text-amber-400 font-bold whitespace-nowrap">
                                {{ $cat->slug }}
                            </td>

                            <td class="py-4 px-4 sm:px-6 text-gray-600 dark:text-gray-400 line-clamp-2 leading-relaxed">
                                {{ $cat->deskripsi ?: 'Tidak ada deskripsi.' }}
                            </td>

                            <td class="py-4 px-4 sm:px-6 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button type="button" 
                                            @click="openModal('edit', {{ json_encode($cat) }})" 
                                            class="px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-amber-100 text-gray-700 hover:text-amber-700 dark:bg-gray-700 dark:hover:bg-amber-950/60 dark:text-gray-300 dark:hover:text-amber-300 font-bold text-xs transition">
                                        Edit
                                    </button>
                                    <button type="button" 
                                            @click="$dispatch('open-confirm-modal', {
                                                title: 'Hapus Master Kategori?',
                                                message: 'Apakah Anda yakin ingin menghapus kategori {{ addslashes($cat->nama_kategori) }}?',
                                                confirmText: 'Ya, Hapus Kategori',
                                                type: 'danger',
                                                actionUrl: '{{ route('admin.categories.destroy', $cat->id) }}',
                                                method: 'POST'
                                            })"
                                            class="px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 dark:bg-rose-950/60 dark:text-rose-300 font-bold text-xs transition">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center text-gray-400 font-bold text-xs">
                                Belum ada master kategori ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($categories->hasPages())
            <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                {{ $categories->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Form Create / Edit Category -->
    <div x-show="modalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto" 
         role="dialog" aria-modal="true">
        
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="closeModal()"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-200 dark:border-gray-700 p-6 space-y-4">
                
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-3">
                    <h3 class="text-base font-extrabold text-gray-900 dark:text-white" x-text="modalMode === 'create' ? 'Tambah Kategori Baru' : 'Edit Kategori'"></h3>
                    <button type="button" @click="closeModal()" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>

                <form :action="formAction" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Nama Kategori *</label>
                        <input type="text" name="nama_kategori" x-model="formData.nama_kategori" required placeholder="mis. Prewedding & Engagement" class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">URL Gambar Icon / Cover</label>
                        <input type="url" name="icon_url" x-model="formData.icon_url" placeholder="https://images.unsplash.com/..." class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Deskripsi Kategori</label>
                        <textarea name="deskripsi" x-model="formData.deskripsi" rows="3" placeholder="Penjelasan mengenai spesialisasi kategori ini..." class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none resize-none"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100 dark:border-gray-700">
                        <button type="button" @click="closeModal()" class="px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 text-xs font-bold">Batal</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-extrabold text-xs shadow-sm">Simpan Kategori</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

</div>

<script>
function categoryModalApp() {
    return {
        modalOpen: false,
        modalMode: 'create',
        formAction: '{{ route('admin.categories.store') }}',
        formData: {
            nama_kategori: '',
            icon_url: '',
            deskripsi: ''
        },
        openModal(mode, data = null) {
            this.modalMode = mode;
            if (mode === 'create') {
                this.formAction = '{{ route('admin.categories.store') }}';
                this.formData = { nama_kategori: '', icon_url: '', deskripsi: '' };
            } else if (data) {
                this.formAction = `/admin/categories/${data.id}/update`;
                this.formData = {
                    nama_kategori: data.nama_kategori || '',
                    icon_url: data.icon_url || '',
                    deskripsi: data.deskripsi || ''
                };
            }
            this.modalOpen = true;
        },
        closeModal() {
            this.modalOpen = false;
        }
    };
}
</script>
@endsection