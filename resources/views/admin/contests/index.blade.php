@extends('layouts.admin')

@section('title', 'Manajemen Lomba Foto Admin - LensMatch')

@section('content')
<div class="space-y-6" x-data="contestModalApp()">

    <!-- Page Title Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <span class="text-[11px] font-extrabold uppercase tracking-widest text-amber-600 dark:text-amber-400">Pusat Event Kompetisi</span>
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white tracking-tight">Manajemen Lomba Foto</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Kelola event kompetisi fotografi nasional, atur total hadiah, dan seleksi penetapan pemenang</p>
        </div>

        <button type="button" 
                @click="openModal('create')" 
                class="px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-extrabold text-xs shadow-xs transition flex items-center justify-center gap-2 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
            <span>Terbitkan Lomba Foto Baru</span>
        </button>
    </div>

    <!-- 2 Stat Cards Summary -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <!-- Card 1: Total Event Lomba -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Event Lomba</span>
                <span class="p-2.5 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                </span>
            </div>
            <div class="mt-2">
                <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">{{ $totalContests }} Event</h2>
                <p class="text-[11px] text-amber-600 dark:text-amber-400 font-semibold mt-1">Kompetisi Fotografi Terdaftar</p>
            </div>
        </div>

        <!-- Card 2: Total Karya Peserta -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Karya Peserta</span>
                <span class="p-2.5 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </span>
            </div>
            <div class="mt-2">
                <h2 class="text-2xl font-black text-purple-600 dark:text-purple-400 tracking-tight">{{ $totalSubmissions }} Foto Submisi</h2>
                <p class="text-[11px] text-gray-500 dark:text-gray-400 font-medium mt-1">Total Pendaftaran Karya Peserta</p>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm overflow-hidden space-y-4">
        
        <!-- Search Toolbar -->
        <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row items-center justify-between gap-4">
            <form method="GET" action="{{ route('admin.contests') }}" class="relative w-full sm:w-80">
                <input type="text" 
                       name="search" 
                       value="{{ $search }}" 
                       placeholder="Cari event lomba foto..." 
                       class="w-full pl-9 pr-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </form>

            <div class="flex items-center gap-2 w-full sm:w-auto">
                <a href="{{ route('admin.contests', ['status' => 'all']) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $status === 'all' ? 'bg-amber-500 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">Semua Status</a>
                <a href="{{ route('admin.contests', ['status' => 'buka']) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $status === 'buka' ? 'bg-emerald-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">Buka</a>
                <a href="{{ route('admin.contests', ['status' => 'selesai']) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $status === 'selesai' ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">Selesai</a>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-[11px] font-extrabold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        <th class="py-3.5 px-4 sm:px-6 w-4/12">Event Lomba & Penyelenggara</th>
                        <th class="py-3.5 px-4 sm:px-6 w-2/12">Kategori</th>
                        <th class="py-3.5 px-4 sm:px-6 w-2/12">Total Hadiah</th>
                        <th class="py-3.5 px-4 sm:px-6 w-1/12 text-center">Status</th>
                        <th class="py-3.5 px-4 sm:px-6 w-1/12 text-center">Peserta</th>
                        <th class="py-3.5 px-4 sm:px-6 w-2/12 text-center">Aksi Moderasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60 text-xs">
                    @forelse($contests as $contest)
                        <tr class="hover:bg-amber-50/30 dark:hover:bg-amber-950/20 transition">
                            <td class="py-4 px-4 sm:px-6 flex items-center gap-3">
                                <img onerror="this.onerror=null;this.src=window.SVG_IMAGE_FALLBACK||'data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='800' height='600' viewBox='0 0 800 600' fill='none'%3E%3Crect width='800' height='600' fill='%23F3F4F6'/%3E%3Cpath d='M360 260C360 248.954 368.954 240 380 240H420C431.046 240 440 248.954 440 260V265H450C466.569 265 480 278.431 480 295V345C480 361.569 466.569 375 450 375H350C333.431 375 320 361.569 320 345V295C320 278.431 333.431 265 350 265H360V260Z' stroke='%239CA3AF' stroke-width='12' stroke-linecap='round' stroke-linejoin='round'/%3E%3Ccircle cx='400' cy='320' r='30' stroke='%239CA3AF' stroke-width='12'/%3E%3Cline x1='310' y1='240' x2='490' y2='390' stroke='%23EF4444' stroke-width='10' stroke-linecap='round'/%3E%3Ctext x='400' y='450' font-family='sans-serif' font-size='22' font-weight='700' fill='%236B7280' text-anchor='middle'%3EGambar Tidak Dapat Dimuat%3C/text%3E%3C/svg%3E';" src="{{ $contest->banner_url }}" alt="{{ $contest->judul_lomba }}" class="w-16 h-10 rounded-lg object-cover border border-gray-200 dark:border-gray-700 shrink-0 shadow-xs" />
                                <div>
                                    <h4 class="font-extrabold text-gray-900 dark:text-white line-clamp-1">{{ $contest->judul_lomba }}</h4>
                                    <p class="text-[10px] text-gray-400 mt-0.5">{{ $contest->penyelenggara }}</p>
                                </div>
                            </td>

                            <td class="py-4 px-4 sm:px-6 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-extrabold bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-200/60 dark:border-amber-900/60">
                                    {{ $contest->kategori }}
                                </span>
                            </td>

                            <td class="py-4 px-4 sm:px-6 font-black text-amber-600 dark:text-amber-400 whitespace-nowrap">
                                {{ $contest->hadiah }}
                            </td>

                            <td class="py-4 px-4 sm:px-6 text-center whitespace-nowrap">
                                @if($contest->status === 'buka')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-400 border border-emerald-200/60">Pendaftaran Buka</span>
                                @elseif($contest->status === 'ditutup')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-rose-50 text-rose-600 dark:bg-rose-950/60 dark:text-rose-400 border border-rose-200/60">Ditutup</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-blue-50 text-blue-600 dark:bg-blue-950/60 dark:text-blue-400 border border-blue-200/60">Selesai / Juara Diumumkan</span>
                                @endif
                            </td>

                            <td class="py-4 px-4 sm:px-6 text-center whitespace-nowrap font-bold text-gray-900 dark:text-white">
                                {{ $contest->submissions_count }} Karya
                            </td>

                            <td class="py-4 px-4 sm:px-6 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('admin.contests.submissions', $contest->id) }}" class="px-3 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 dark:bg-blue-950/60 dark:text-blue-300 font-bold text-xs transition">
                                        Review Karya
                                    </a>
                                    <button type="button" 
                                            @click="openModal('edit', {{ json_encode($contest) }})" 
                                            class="px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-amber-100 text-gray-700 hover:text-amber-700 dark:bg-gray-700 dark:hover:bg-amber-950/60 dark:text-gray-300 dark:hover:text-amber-300 font-bold text-xs transition">
                                        Edit
                                    </button>
                                    <button type="button" 
                                            @click="$dispatch('open-confirm-modal', {
                                                title: 'Hapus Event Lomba Foto?',
                                                message: 'Apakah Anda yakin ingin menghapus event {{ addslashes($contest->judul_lomba) }}?',
                                                confirmText: 'Ya, Hapus Event',
                                                type: 'danger',
                                                actionUrl: '{{ route('admin.contests.destroy', $contest->id) }}',
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
                                Belum ada event lomba foto ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($contests->hasPages())
            <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                {{ $contests->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Form Create / Edit Photo Contest -->
    <div x-show="modalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto" 
         role="dialog" aria-modal="true">
        
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="closeModal()"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-gray-200 dark:border-gray-700 p-6 space-y-4">
                
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-3">
                    <h3 class="text-base font-extrabold text-gray-900 dark:text-white" x-text="modalMode === 'create' ? 'Terbitkan Lomba Foto Baru' : 'Edit Event Lomba Foto'"></h3>
                    <button type="button" @click="closeModal()" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>

                <form :action="formAction" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Judul Event Lomba *</label>
                        <input type="text" name="judul_lomba" x-model="formData.judul_lomba" required placeholder="mis. Kompetisi Foto Landscape Pesona Nusantara" class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500">
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Kategori *</label>
                            <input type="text" name="kategori" x-model="formData.kategori" required placeholder="Landscape & Alam" class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Total Hadiah *</label>
                            <input type="text" name="hadiah" x-model="formData.hadiah" required placeholder="Rp 25.000.000" class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Status Event *</label>
                            <select name="status" x-model="formData.status" required class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none">
                                <option value="buka">Pendaftaran Buka</option>
                                <option value="ditutup">Ditutup</option>
                                <option value="selesai">Selesai / Diumumkan</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Tanggal Mulai *</label>
                            <input type="date" name="start_date" x-model="formData.start_date" required class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Batas Deadline *</label>
                            <input type="date" name="end_date" x-model="formData.end_date" required class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Penyelenggara / Sponsor *</label>
                        <input type="text" name="penyelenggara" x-model="formData.penyelenggara" required placeholder="LensMatch x Sponsor" class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">URL Banner Image</label>
                        <input type="url" name="banner_url" x-model="formData.banner_url" placeholder="https://images.unsplash.com/..." class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Syarat & Deskripsi Lomba *</label>
                        <textarea name="deskrpisi_lomba" x-model="formData.deskrpisi_lomba" rows="4" required placeholder="Tuliskan syarat & ketentuan event lomba..." class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none resize-none"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100 dark:border-gray-700">
                        <button type="button" @click="closeModal()" class="px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 text-xs font-bold">Batal</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-extrabold text-xs shadow-sm">Simpan Event</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

</div>

<script>
function contestModalApp() {
    return {
        modalOpen: false,
        modalMode: 'create',
        formAction: '{{ route('admin.contests.store') }}',
        formData: {
            judul_lomba: '',
            kategori: 'Landscape & Alam',
            penyelenggara: 'LensMatch Official',
            hadiah: 'Rp 20.000.000',
            start_date: '{{ date('Y-m-d') }}',
            end_date: '{{ date('Y-m-d', strtotime('+30 days')) }}',
            status: 'buka',
            banner_url: '',
            deskrpisi_lomba: ''
        },
        openModal(mode, data = null) {
            this.modalMode = mode;
            if (mode === 'create') {
                this.formAction = '{{ route('admin.contests.store') }}';
                this.formData = {
                    judul_lomba: '',
                    kategori: 'Landscape & Alam',
                    penyelenggara: 'LensMatch Official',
                    hadiah: 'Rp 20.000.000',
                    start_date: '{{ date('Y-m-d') }}',
                    end_date: '{{ date('Y-m-d', strtotime('+30 days')) }}',
                    status: 'buka',
                    banner_url: '',
                    deskrpisi_lomba: ''
                };
            } else if (data) {
                this.formAction = `/admin/contests/${data.id}/update`;
                this.formData = {
                    judul_lomba: data.judul_lomba || '',
                    kategori: data.kategori || 'Landscape & Alam',
                    penyelenggara: data.penyelenggara || 'LensMatch Official',
                    hadiah: data.hadiah || 'Rp 20.000.000',
                    start_date: data.start_date ? data.start_date.substring(0, 10) : '{{ date('Y-m-d') }}',
                    end_date: data.end_date ? data.end_date.substring(0, 10) : '{{ date('Y-m-d', strtotime('+30 days')) }}',
                    status: data.status || 'buka',
                    banner_url: data.banner_url || '',
                    deskrpisi_lomba: data.deskrpisi_lomba || ''
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