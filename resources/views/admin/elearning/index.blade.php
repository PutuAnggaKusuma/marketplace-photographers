@extends('layouts.admin')

@section('title', 'Manajemen E-Learning Admin - LensMatch')

@section('content')
<div class="space-y-6" x-data="elearningModalApp()">

    <!-- Page Title Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <span class="text-[11px] font-extrabold uppercase tracking-widest text-amber-600 dark:text-amber-400">Akademi Edukasi Fotografi</span>
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white tracking-tight">Manajemen E-Learning</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Kelola materi pembelajaran, modul tutorial, dan artikel edukasi fotografi untuk fotografer & Klien</p>
        </div>

        <button type="button" 
                @click="openModal('create')" 
                class="px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-extrabold text-xs shadow-xs transition flex items-center justify-center gap-2 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
            <span>Tambah Materi Baru</span>
        </button>
    </div>

    <!-- 2 Stat Cards Summary -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <!-- Card 1: Total Modul Materi -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Modul Materi</span>
                <span class="p-2.5 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </span>
            </div>
            <div class="mt-2">
                <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">{{ $totalCourses }} Modul</h2>
                <p class="text-[11px] text-amber-600 dark:text-amber-400 font-semibold mt-1">Kurikulum Edukasi Aktif</p>
            </div>
        </div>

        <!-- Card 2: Total Pembaca (Views) -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Pembaca (Views)</span>
                <span class="p-2.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                </span>
            </div>
            <div class="mt-2">
                <h2 class="text-2xl font-black text-emerald-600 dark:text-emerald-400 tracking-tight">{{ number_format($totalViews, 0, ',', '.') }} Pembaca</h2>
                <p class="text-[11px] text-gray-500 dark:text-gray-400 font-medium mt-1">Interaksi Pembaca Materi</p>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm overflow-hidden space-y-4">
        
        <!-- Search Toolbar -->
        <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between gap-4">
            <form method="GET" action="{{ route('admin.elearning') }}" class="relative w-full sm:w-80">
                <input type="text" 
                       name="search" 
                       value="{{ $search }}" 
                       placeholder="Cari modul materi..." 
                       class="w-full pl-9 pr-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-[11px] font-extrabold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        <th class="py-3.5 px-4 sm:px-6 w-4/12">Materi & Thumbnail</th>
                        <th class="py-3.5 px-4 sm:px-6 w-2/12">Kategori</th>
                        <th class="py-3.5 px-4 sm:px-6 w-2/12">Level</th>
                        <th class="py-3.5 px-4 sm:px-6 w-1/12">Durasi</th>
                        <th class="py-3.5 px-4 sm:px-6 w-1/12">Pembaca</th>
                        <th class="py-3.5 px-4 sm:px-6 w-2/12 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60 text-xs">
                    @forelse($elearnings as $course)
                        <tr class="hover:bg-amber-50/30 dark:hover:bg-amber-950/20 transition">
                            <td class="py-4 px-4 sm:px-6 flex items-center gap-3">
                                <img onerror="this.onerror=null;this.src=window.SVG_IMAGE_FALLBACK||'data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='800' height='600' viewBox='0 0 800 600' fill='none'%3E%3Crect width='800' height='600' fill='%23F3F4F6'/%3E%3Cpath d='M360 260C360 248.954 368.954 240 380 240H420C431.046 240 440 248.954 440 260V265H450C466.569 265 480 278.431 480 295V345C480 361.569 466.569 375 450 375H350C333.431 375 320 361.569 320 345V295C320 278.431 333.431 265 350 265H360V260Z' stroke='%239CA3AF' stroke-width='12' stroke-linecap='round' stroke-linejoin='round'/%3E%3Ccircle cx='400' cy='320' r='30' stroke='%239CA3AF' stroke-width='12'/%3E%3Cline x1='310' y1='240' x2='490' y2='390' stroke='%23EF4444' stroke-width='10' stroke-linecap='round'/%3E%3Ctext x='400' y='450' font-family='sans-serif' font-size='22' font-weight='700' fill='%236B7280' text-anchor='middle'%3EGambar Tidak Dapat Dimuat%3C/text%3E%3C/svg%3E';" src="{{ $course->thumbnail_url }}" alt="{{ $course->judul }}" class="w-14 h-10 rounded-lg object-cover border border-gray-200 dark:border-gray-700 shrink-0 shadow-xs" />
                                <div>
                                    <h4 class="font-extrabold text-gray-900 dark:text-white line-clamp-1">{{ $course->judul }}</h4>
                                    <p class="text-[10px] text-gray-400 mt-0.5 line-clamp-1 leading-relaxed">{{ $course->ringkasan }}</p>
                                </div>
                            </td>

                            <td class="py-4 px-4 sm:px-6 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-extrabold bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-200/60 dark:border-amber-900/60">
                                    {{ $course->kategori }}
                                </span>
                            </td>

                            <td class="py-4 px-4 sm:px-6 font-bold text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                {{ $course->level }}
                            </td>

                            <td class="py-4 px-4 sm:px-6 font-semibold text-gray-600 dark:text-gray-400 whitespace-nowrap">
                                {{ $course->durasi }}
                            </td>

                            <td class="py-4 px-4 sm:px-6 font-bold text-gray-900 dark:text-white whitespace-nowrap">
                                {{ number_format($course->view_count, 0, ',', '.') }} views
                            </td>

                            <td class="py-4 px-4 sm:px-6 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button type="button" 
                                            @click="openModal('edit', {{ json_encode($course) }})" 
                                            class="px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-amber-100 text-gray-700 hover:text-amber-700 dark:bg-gray-700 dark:hover:bg-amber-950/60 dark:text-gray-300 dark:hover:text-amber-300 font-bold text-xs transition">
                                        Edit
                                    </button>
                                    <button type="button" 
                                            @click="$dispatch('open-confirm-modal', {
                                                title: 'Hapus Materi E-Learning?',
                                                message: 'Apakah Anda yakin ingin menghapus materi {{ addslashes($course->judul) }}?',
                                                confirmText: 'Ya, Hapus Materi',
                                                type: 'danger',
                                                actionUrl: '{{ route('admin.elearning.destroy', $course->id) }}',
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
                            <td colspan="6" class="py-12 text-center text-gray-400 font-bold text-xs">
                                Belum ada materi e-learning ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($elearnings->hasPages())
            <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                {{ $elearnings->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Form Create / Edit E-Learning Course -->
    <div x-show="modalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto" 
         role="dialog" aria-modal="true">
        
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="closeModal()"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-gray-200 dark:border-gray-700 p-6 space-y-4">
                
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-3">
                    <h3 class="text-base font-extrabold text-gray-900 dark:text-white" x-text="modalMode === 'create' ? 'Tambah Materi E-Learning Baru' : 'Edit Materi E-Learning'"></h3>
                    <button type="button" @click="closeModal()" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>

                <form :action="formAction" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Judul Materi *</label>
                        <input type="text" name="judul" x-model="formData.judul" required placeholder="mis. Teknik Lighting Portrait Outdoor" class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500">
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Kategori *</label>
                            <input type="text" name="kategori" x-model="formData.kategori" required placeholder="Lighting & Teknik" class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Level *</label>
                            <select name="level" x-model="formData.level" required class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none">
                                <option value="Pemula">Pemula</option>
                                <option value="Menengah">Menengah</option>
                                <option value="Mahir">Mahir</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Durasi *</label>
                            <input type="text" name="durasi" x-model="formData.durasi" required placeholder="45 Menit" class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">URL Thumbnail Gambar</label>
                        <input type="url" name="thumbnail_url" x-model="formData.thumbnail_url" placeholder="https://images.unsplash.com/..." class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Ringkasan Singkat *</label>
                        <textarea name="ringkasan" x-model="formData.ringkasan" rows="2" required placeholder="Penjelasan singkat materi..." class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none resize-none"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Konten Lengkap Materi *</label>
                        <textarea name="konten" x-model="formData.konten" rows="5" required placeholder="Tuliskan materi pembelajaran lengkap..." class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none resize-none"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100 dark:border-gray-700">
                        <button type="button" @click="closeModal()" class="px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 text-xs font-bold">Batal</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-extrabold text-xs shadow-sm">Simpan Materi</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

</div>

<script>
function elearningModalApp() {
    return {
        modalOpen: false,
        modalMode: 'create',
        formAction: '{{ route('admin.elearning.store') }}',
        formData: {
            judul: '',
            kategori: 'Lighting & Teknik',
            level: 'Pemula',
            durasi: '45 Menit',
            thumbnail_url: '',
            ringkasan: '',
            konten: ''
        },
        openModal(mode, data = null) {
            this.modalMode = mode;
            if (mode === 'create') {
                this.formAction = '{{ route('admin.elearning.store') }}';
                this.formData = { judul: '', kategori: 'Lighting & Teknik', level: 'Pemula', durasi: '45 Menit', thumbnail_url: '', ringkasan: '', konten: '' };
            } else if (data) {
                this.formAction = `/admin/elearning/${data.id}/update`;
                this.formData = {
                    judul: data.judul || '',
                    kategori: data.kategori || 'Lighting & Teknik',
                    level: data.level || 'Pemula',
                    durasi: data.durasi || '45 Menit',
                    thumbnail_url: data.thumbnail_url || '',
                    ringkasan: data.ringkasan || '',
                    konten: data.konten || ''
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