@extends('layouts.admin')

@section('title', 'Kelola Lomba Foto — LensMatch Admin')

@section('content')
<div class="space-y-6" x-data="{
    activeTab: '{{ $tab ?? 'pendaftar' }}',
    
    // Participant Review Modal
    participantModal: false,
    selectedParticipant: {
        id: null,
        nama_lengkap: '',
        email: '',
        no_wa: '',
        instansi: '',
        asal_kota: '',
        bio_singkat: '',
        cv_portfolio_file: '',
        portfolio_link: '',
        status_pendaftaran: 'menunggu_verifikasi',
        catatan_admin: '',
        status_pembayaran: 'menunggu_pembayaran',
        bukti_transfer: '',
        verify_payment: false,
        update_url: ''
    },
    openParticipantReview(p, updateUrl) {
        this.selectedParticipant = {
            id: p.id,
            nama_lengkap: p.nama_lengkap || 'Calon Peserta',
            email: p.email || '-',
            no_wa: p.no_wa || '-',
            instansi: p.instansi || 'Independen',
            asal_kota: p.asal_kota || '-',
            bio_singkat: p.bio_singkat || 'Tidak ada bio tambahan.',
            cv_portfolio_file: p.cv_portfolio_file || '',
            portfolio_link: p.portfolio_link || '',
            status_pendaftaran: (p.status_pendaftaran === 'pending' || !p.status_pendaftaran) ? 'menunggu_verifikasi' : p.status_pendaftaran,
            catatan_admin: p.catatan_admin || '',
            status_pembayaran: p.status_pembayaran || 'menunggu_pembayaran',
            bukti_transfer: p.bukti_transfer || '',
            verify_payment: false,
            update_url: updateUrl
        };
        this.participantModal = true;
    },

    // Submission Review Modal
    submissionModal: false,
    selectedSubmission: {
        id: null,
        judul_karya: '',
        deskripsi_karya: '',
        image_url: '',
        media_link: '',
        status_submission: 'verified',
        catatan_juri: '',
        user_name: '',
        user_email: '',
        created_at_human: '',
        update_url: ''
    },
    openSubmissionReview(sub, updateUrl) {
        this.selectedSubmission = {
            id: sub.id,
            judul_karya: sub.judul_karya || 'Tanpa Judul',
            deskripsi_karya: sub.deskripsi_karya || 'Tidak ada deskripsi tambahan.',
            image_url: sub.image_url || '',
            media_link: sub.media_link || '',
            status_submission: sub.status_submission || 'verified',
            catatan_juri: sub.catatan_juri || '',
            user_name: sub.user ? sub.user.nama : 'Peserta',
            user_email: sub.user ? sub.user.email : '-',
            created_at_human: sub.created_at_human || '',
            update_url: updateUrl
        };
        this.submissionModal = true;
    },

    // Filter & Search Handler
    applyFilter(key, val) {
        const url = new URL(window.location.href);
        url.searchParams.set('tab', this.activeTab);
        if (val === 'all' || val === '' || val === null) {
            url.searchParams.delete(key);
        } else {
            url.searchParams.set(key, val);
        }
        url.searchParams.delete(this.activeTab === 'pendaftar' ? 'p_page' : 's_page');
        window.location.href = url.toString();
    },
    handleSearch(e) {
        clearTimeout(this.searchTimer);
        this.searchTimer = setTimeout(() => {
            this.applyFilter('search', e.target.value);
        }, 500);
    },
    switchTab(tabName) {
        if (this.activeTab === tabName) return;
        this.activeTab = tabName;
        const url = new URL(window.location.href);
        url.searchParams.set('tab', tabName);
        window.history.pushState({ tab: tabName }, '', url.toString());
    },
    init() {
        window.addEventListener('popstate', () => {
            const urlParams = new URLSearchParams(window.location.search);
            this.activeTab = urlParams.get('tab') || 'pendaftar';
        });
    }
}">

    <!-- Standard Golden Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <span class="text-[11px] font-extrabold uppercase tracking-widest text-amber-600 dark:text-amber-500 block mb-1">
                Lomba Foto
            </span>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">
                Kelola Lomba: {{ $contest->judul_lomba }}
            </h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-1">
                Kelola verifikasi calon pendaftar, pembayaran, dan kurasi penilaian karya submisi.
            </p>
        </div>
        <div>
            <a href="{{ route('admin.contests') }}" class="px-4 py-2 rounded-xl bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-750 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 font-bold text-xs transition inline-flex items-center gap-1.5 shadow-2xs shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"></path></svg>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <!-- Main Navigation Tabs: Pendaftar & Submisi (Amber Brand Active with Dark Text) -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs font-bold">
        <button type="button" 
                @click="switchTab('pendaftar')" 
                class="px-4 py-2 rounded-xl transition cursor-pointer shrink-0"
                :class="activeTab === 'pendaftar' ? 'bg-amber-400 text-gray-950 font-extrabold shadow-xs' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:border-gray-300'">
            Pendaftar ({{ $totalParticipants ?? 0 }})
        </button>

        <button type="button" 
                @click="switchTab('submisi')" 
                class="px-4 py-2 rounded-xl transition cursor-pointer shrink-0"
                :class="activeTab === 'submisi' ? 'bg-amber-400 text-gray-950 font-extrabold shadow-xs' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:border-gray-300'">
            Submisi ({{ $totalSubmissions ?? 0 }})
        </button>
    </div>

    <!-- TAB 1: PENDAFTAR -->
    <div x-show="activeTab === 'pendaftar'" 
         x-transition:enter="transition-all ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 -translate-x-6"
         x-transition:enter-end="opacity-100 translate-x-0"
         x-cloak
         class="space-y-6">
        
        <!-- Summary Stat Cards Pendaftar (Rule #8 & #9) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
            <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
                <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Total Pendaftar</span>
                <div class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ $totalParticipants ?? 0 }} Orang</div>
                <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">Calon peserta terdaftar</p>
            </div>
            <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
                <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Perlu Verifikasi</span>
                <div class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ $pendingParticipants ?? 0 }} Orang</div>
                <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">Menunggu persetujuan admin</p>
            </div>
            <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
                <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Peserta Disetujui</span>
                <div class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ $approvedParticipants ?? 0 }} Orang</div>
                <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">Lolos kualifikasi & berhak submit</p>
            </div>
            <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
                <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Pendaftaran Ditolak</span>
                <div class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ $rejectedParticipants ?? 0 }} Orang</div>
                <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">Tidak memenuhi persyaratan</p>
            </div>
        </div>

        <!-- Filter Status Pills Pendaftar -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs font-bold">
            <button type="button" @click="applyFilter('status', 'all')" 
                    class="px-4 py-2 rounded-xl transition cursor-pointer shrink-0 {{ ($status ?? 'all') === 'all' ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-950 shadow-xs' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:border-gray-300' }}">
                Semua ({{ $totalParticipants ?? 0 }})
            </button>
            <button type="button" @click="applyFilter('status', 'pending')" 
                    class="px-4 py-2 rounded-xl transition cursor-pointer shrink-0 {{ ($status ?? '') === 'pending' ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-950 shadow-xs' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:border-gray-300' }}">
                Menunggu Verifikasi ({{ $pendingParticipants ?? 0 }})
            </button>
            <button type="button" @click="applyFilter('status', 'disetujui')" 
                    class="px-4 py-2 rounded-xl transition cursor-pointer shrink-0 {{ ($status ?? '') === 'disetujui' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:border-gray-300' }}">
                Disetujui ({{ $approvedParticipants ?? 0 }})
            </button>
            <button type="button" @click="applyFilter('status', 'ditolak')" 
                    class="px-4 py-2 rounded-xl transition cursor-pointer shrink-0 {{ ($status ?? '') === 'ditolak' ? 'bg-rose-600 text-white shadow-xs' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:border-gray-300' }}">
                Ditolak ({{ $rejectedParticipants ?? 0 }})
            </button>
        </div>

        <!-- Data Table Pendaftar -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm overflow-hidden space-y-4">
            <!-- Search & Sort Toolbar -->
            <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3 w-full md:w-auto">
                    @php
                        $sortLabels = [
                            'latest' => 'Pendaftar Terbaru',
                            'oldest' => 'Pendaftar Terlama',
                        ];
                        $curSortName = $sortLabels[$sort ?? 'latest'] ?? 'Pendaftar Terbaru';
                    @endphp
                    <div class="relative w-full sm:w-auto min-w-[200px]" x-data="{ open: false, selectedId: '{{ $sort ?? 'latest' }}', selectedName: '{{ $curSortName }}' }">
                        <input type="hidden" name="sort" :value="selectedId">
                        <button @click="open = !open" type="button" 
                                class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs font-bold text-gray-800 dark:text-white flex items-center justify-between gap-2 shadow-2xs hover:border-amber-400 dark:hover:border-amber-500 cursor-pointer transition">
                            <span x-text="selectedName" class="truncate"></span>
                            <svg class="w-3.5 h-3.5 text-gray-400 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition 
                             class="absolute z-50 left-0 mt-2 min-w-[210px] w-full bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700/80 py-1 max-h-60 overflow-y-auto" style="display: none;">
                            @foreach($sortLabels as $srtKey => $srtVal)
                                <button type="button" @click="selectedId = '{{ $srtKey }}'; selectedName = '{{ $srtVal }}'; open = false; applyFilter('sort', '{{ $srtKey }}')" 
                                        class="w-full px-3.5 py-2 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition flex items-center justify-between text-left cursor-pointer border-b border-gray-50 dark:border-gray-700/40 last:border-0"
                                        :class="selectedId === '{{ $srtKey }}' ? 'bg-amber-50/70 dark:bg-amber-950/30 text-amber-600 font-extrabold' : ''">
                                    <span>{{ $srtVal }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Search di Kanan (Rule #9) -->
                <div class="relative w-full md:w-80">
                    <input type="text" 
                           name="search" 
                           value="{{ $search }}" @input="handleSearch($event)" 
                           placeholder="Cari nama, email, WA, atau instansi..." 
                           class="w-full pl-4 pr-10 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">
                    <svg class="w-4 h-4 text-gray-400 absolute right-3.5 top-2.5 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>

            <!-- Table Content -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-[11px] font-extrabold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            <th class="py-3.5 px-4 sm:px-6 w-3/12">Calon Peserta</th>
                            <th class="py-3.5 px-4 sm:px-6 w-2/12">Kontak & Asal</th>
                            <th class="py-3.5 px-4 sm:px-6 w-2/12">Kualifikasi / Portofolio</th>
                            <th class="py-3.5 px-4 sm:px-6 w-2/12">Status Pendaftaran</th>
                            <th class="py-3.5 px-4 sm:px-6 w-2/12">Pembayaran</th>
                            <th class="py-3.5 px-4 sm:px-6 w-1/12 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60 text-xs">
                        @forelse($participants as $p)
                            @php
                                $pPayload = [
                                    'id' => $p->id,
                                    'nama_lengkap' => $p->nama_lengkap,
                                    'email' => $p->email,
                                    'no_wa' => $p->no_wa,
                                    'instansi' => $p->instansi,
                                    'asal_kota' => $p->asal_kota,
                                    'bio_singkat' => $p->bio_singkat,
                                    'cv_portfolio_file' => $p->cv_portfolio_file,
                                    'portfolio_link' => $p->portfolio_link,
                                    'status_pendaftaran' => $p->status_pendaftaran,
                                    'catatan_admin' => $p->catatan_admin,
                                    'status_pembayaran' => $p->status_pembayaran,
                                    'bukti_transfer' => $p->bukti_transfer,
                                ];
                            @endphp
                            <tr class="hover:bg-amber-50/30 dark:hover:bg-amber-950/20 transition">
                                <!-- Col 1: Calon Peserta -->
                                <td class="py-4 px-4 sm:px-6 align-top">
                                    <div class="font-bold text-gray-900 dark:text-white">{{ $p->nama_lengkap }}</div>
                                    <div class="text-[11px] text-gray-400 mt-0.5">{{ $p->instansi ?: 'Independen' }}</div>
                                    @if($p->bio_singkat)
                                        <p class="text-[10px] text-gray-500 mt-1 max-w-xs line-clamp-1 italic">"{{ $p->bio_singkat }}"</p>
                                    @endif
                                </td>

                                <!-- Col 2: Kontak -->
                                <td class="py-4 px-4 sm:px-6 align-top whitespace-nowrap">
                                    <div class="font-medium text-gray-800 dark:text-gray-200">{{ $p->email }}</div>
                                    <div class="text-[11px] text-gray-500 dark:text-gray-400 font-medium mt-0.5">WA: {{ $p->no_wa }}</div>
                                    <div class="text-[10px] text-gray-400">{{ $p->asal_kota ?: '-' }}</div>
                                </td>

                                <!-- Col 3: Kualifikasi -->
                                <td class="py-4 px-4 sm:px-6 align-top space-y-1">
                                    @if($p->cv_portfolio_file)
                                        <a href="{{ $p->cv_portfolio_file }}" target="_blank" class="text-xs font-semibold text-gray-900 dark:text-white underline block">
                                            Lihat CV / Portofolio (PDF)
                                        </a>
                                    @endif
                                    @if($p->portfolio_link)
                                        <a href="{{ $p->portfolio_link }}" target="_blank" class="text-xs font-semibold text-gray-900 dark:text-white underline block">
                                            Tautan Portofolio
                                        </a>
                                    @endif
                                    @if(!$p->cv_portfolio_file && !$p->portfolio_link)
                                        <span class="text-gray-400 text-xs">—</span>
                                    @endif
                                </td>

                                <!-- Col 4: Status Pendaftaran (Plain Dark Text) -->
                                <td class="py-4 px-4 sm:px-6 align-top whitespace-nowrap">
                                    @if($p->status_pendaftaran === 'disetujui')
                                        <span class="text-xs font-bold text-gray-900 dark:text-white block">
                                            Disetujui
                                        </span>
                                    @elseif($p->status_pendaftaran === 'ditolak')
                                        <div class="space-y-0.5">
                                            <span class="text-xs font-bold text-gray-900 dark:text-white block">
                                                Ditolak
                                            </span>
                                            @if($p->catatan_admin)
                                                <p class="text-[10px] text-gray-500 dark:text-gray-400 max-w-[160px] truncate" title="{{ $p->catatan_admin }}">
                                                    "{{ \Illuminate\Support\Str::limit($p->catatan_admin, 25, '...') }}"
                                                </p>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-xs font-bold text-gray-900 dark:text-white block">
                                            Menunggu Verifikasi
                                        </span>
                                    @endif
                                </td>

                                <!-- Col 5: Pembayaran (Plain Dark Text) -->
                                <td class="py-4 px-4 sm:px-6 align-top whitespace-nowrap">
                                    @if($contest->biaya_pendaftaran > 0)
                                        <div class="space-y-0.5">
                                            <div class="font-bold text-gray-900 dark:text-white">
                                                Rp {{ number_format($contest->biaya_pendaftaran, 0, ',', '.') }}
                                            </div>
                                            @if($p->status_pembayaran === 'lunas')
                                                <span class="text-[11px] font-bold text-gray-900 dark:text-white block">Lunas</span>
                                            @else
                                                <span class="text-[11px] font-bold text-gray-900 dark:text-white block">Menunggu Konfirmasi</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-xs font-bold text-gray-900 dark:text-white">Gratis (Free)</span>
                                    @endif
                                </td>

                                <!-- Col 6: Aksi (Pure Text Button - Rule #10) -->
                                <td class="py-4 px-4 sm:px-6 align-top text-center whitespace-nowrap">
                                    <button type="button" 
                                            @click="openParticipantReview({{ json_encode($pPayload) }}, '{{ route('admin.contests.participant.update_status', $p->id) }}')"
                                            class="px-3.5 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200/80 dark:border-amber-900/60 font-bold text-xs transition cursor-pointer shadow-2xs">
                                        Review Peserta
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 px-4 text-center text-xs text-gray-400 font-bold">
                                    Belum ada calon peserta yang mendaftar pada event lomba ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Pendaftar -->
            @if($participants->hasPages())
                <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                    {{ $participants->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- TAB 2: SUBMISI -->
    <div x-show="activeTab === 'submisi'" 
         x-transition:enter="transition-all ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 translate-x-6"
         x-transition:enter-end="opacity-100 translate-x-0"
         x-cloak
         class="space-y-6">
        
        <!-- Summary Stat Cards Submisi (Rule #8 & #9) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
            <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
                <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Total Karya Masuk</span>
                <div class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ $totalSubmissions ?? 0 }} Karya</div>
                <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">Akumulasi seluruh submisi</p>
            </div>
            <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
                <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Pemenang Juara</span>
                <div class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ $totalWinners ?? 0 }} Juara</div>
                <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">Peringkat 1, 2, dan 3</p>
            </div>
            <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
                <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Peserta Terverifikasi</span>
                <div class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ $totalVerified ?? 0 }} Karya</div>
                <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">Tidak mendapatkan juara</p>
            </div>
            <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
                <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Ditolak / Diskualifikasi</span>
                <div class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ $totalRejected ?? 0 }} Karya</div>
                <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">Karya gugur / tidak lolos</p>
            </div>
        </div>

        <!-- Filter Status Pills Submisi -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs font-bold">
            <button type="button" @click="applyFilter('status', 'all')" 
                    class="px-4 py-2 rounded-xl transition cursor-pointer shrink-0 {{ ($status ?? 'all') === 'all' ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-950 shadow-xs' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:border-gray-300' }}">
                Semua ({{ $totalSubmissions ?? 0 }})
            </button>
            <button type="button" @click="applyFilter('status', 'winners')" 
                    class="px-4 py-2 rounded-xl transition cursor-pointer shrink-0 {{ ($status ?? '') === 'winners' ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-950 shadow-xs' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:border-gray-300' }}">
                Juara 1-3 ({{ $totalWinners ?? 0 }})
            </button>
            <button type="button" @click="applyFilter('status', 'verified')" 
                    class="px-4 py-2 rounded-xl transition cursor-pointer shrink-0 {{ ($status ?? '') === 'verified' ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-950 shadow-xs' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:border-gray-300' }}">
                Tidak Mendapat Juara ({{ $totalVerified ?? 0 }})
            </button>
            <button type="button" @click="applyFilter('status', 'rejected_disqualified')" 
                    class="px-4 py-2 rounded-xl transition cursor-pointer shrink-0 {{ ($status ?? '') === 'rejected_disqualified' ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-950 shadow-xs' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:border-gray-300' }}">
                Ditolak / Diskualifikasi ({{ $totalRejected ?? 0 }})
            </button>
        </div>

        <!-- Data Table Submisi -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm overflow-hidden space-y-4">
            <!-- Search & Sort Toolbar -->
            <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3 w-full md:w-auto">
                    @php
                        $subSortLabels = [
                            'latest' => 'Karya Terbaru',
                            'oldest' => 'Karya Terlama',
                            'winners_first' => 'Pemenang Juara Dahulu',
                        ];
                        $curSubSortName = $subSortLabels[$sort ?? 'latest'] ?? 'Karya Terbaru';
                    @endphp
                    <div class="relative w-full sm:w-auto min-w-[200px]" x-data="{ open: false, selectedId: '{{ $sort ?? 'latest' }}', selectedName: '{{ $curSubSortName }}' }">
                        <input type="hidden" name="sort" :value="selectedId">
                        <button @click="open = !open" type="button" 
                                class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs font-bold text-gray-800 dark:text-white flex items-center justify-between gap-2 shadow-2xs hover:border-amber-400 dark:hover:border-amber-500 cursor-pointer transition">
                            <span x-text="selectedName" class="truncate"></span>
                            <svg class="w-3.5 h-3.5 text-gray-400 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition 
                             class="absolute z-50 left-0 mt-2 min-w-[210px] w-full bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700/80 py-1 max-h-60 overflow-y-auto" style="display: none;">
                            @foreach($subSortLabels as $srtKey => $srtVal)
                                <button type="button" @click="selectedId = '{{ $srtKey }}'; selectedName = '{{ $srtVal }}'; open = false; applyFilter('sort', '{{ $srtKey }}')" 
                                        class="w-full px-3.5 py-2 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition flex items-center justify-between text-left cursor-pointer border-b border-gray-50 dark:border-gray-700/40 last:border-0"
                                        :class="selectedId === '{{ $srtKey }}' ? 'bg-amber-50/70 dark:bg-amber-950/30 text-amber-600 font-extrabold' : ''">
                                    <span>{{ $srtVal }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Search di Kanan (Rule #9) -->
                <div class="relative w-full md:w-80">
                    <input type="text" 
                           name="search" 
                           value="{{ $search }}" @input="handleSearch($event)" 
                           placeholder="Cari judul karya atau peserta..." 
                           class="w-full pl-4 pr-10 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">
                    <svg class="w-4 h-4 text-gray-400 absolute right-3.5 top-2.5 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>

            <!-- Table Submisi Content -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-[11px] font-extrabold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            <th class="py-3.5 px-4 sm:px-6 w-4/12">Karya Foto & Judul</th>
                            <th class="py-3.5 px-4 sm:px-6 w-2/12">Peserta</th>
                            <th class="py-3.5 px-4 sm:px-6 w-2/12">Status Penetapan</th>
                            <th class="py-3.5 px-4 sm:px-6 w-3/12">Catatan Juri</th>
                            <th class="py-3.5 px-4 sm:px-6 w-1/12 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60 text-xs">
                        @forelse($submissions as $sub)
                            @php
                                $subPayload = [
                                    'id' => $sub->id,
                                    'judul_karya' => $sub->judul_karya,
                                    'deskripsi_karya' => $sub->deskripsi_karya,
                                    'image_url' => $sub->image_url,
                                    'media_link' => $sub->media_link,
                                    'status_submission' => $sub->status_submission,
                                    'catatan_juri' => $sub->catatan_juri,
                                    'user' => [
                                        'nama' => $sub->user->nama ?? 'Peserta',
                                        'email' => $sub->user->email ?? '-',
                                    ],
                                    'created_at_human' => $sub->created_at ? $sub->created_at->translatedFormat('d M Y, H:i') . ' WIB' : '-',
                                ];
                            @endphp
                            <tr class="hover:bg-amber-50/30 dark:hover:bg-amber-950/20 transition">
                                <!-- Col 1: Thumbnail + Judul -->
                                <td class="py-4 px-4 sm:px-6 align-top">
                                    <div class="flex items-start gap-3.5">
                                        <div class="w-16 h-12 rounded-xl bg-gray-100 dark:bg-gray-700 overflow-hidden shrink-0 border border-gray-200 dark:border-gray-600 shadow-xs cursor-pointer"
                                             @click="openSubmissionReview({{ json_encode($subPayload) }}, '{{ route('admin.contests.submission.winner', $sub->id) }}')">
                                            <img onerror="this.onerror=null;this.src=window.SVG_IMAGE_FALLBACK||'data:image/svg+xml;charset=UTF-8,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'800\' height=\'600\' viewBox=\'0 0 800 600\' fill=\'none\'%3E%3Crect width=\'800\' height=\'600\' fill=\'%23F3F4F6\'/%3E%3Cpath d=\'M360 260C360 248.954 368.954 240 380 240H420C431.046 240 440 248.954 440 260V265H450C466.569 265 480 278.431 480 295V345C480 361.569 466.569 375 450 375H350C333.431 375 320 361.569 320 345V295C320 278.431 333.431 265 350 265H360V260Z\' stroke=\'%239CA3AF\' stroke-width=\'12\' stroke-linecap=\'round\' stroke-linejoin=\'round\'/%3E%3Ccircle cx=\'400\' cy=\'320\' r=\'30\' stroke=\'%239CA3AF\' stroke-width=\'12\'/%3E%3Cline x1=\'310\' y1=\'240\' x2=\'490\' y2=\'390\' stroke=\'%23EF4444\' stroke-width=\'10\' stroke-linecap=\'round\'/%3E%3Ctext x=\'400\' y=\'450\' font-family=\'sans-serif\' font-size=\'22\' font-weight=\'700\' fill=\'%236B7280\' text-anchor=\'middle\'%3EGambar Tidak Dapat Dimuat%3C/text%3E%3C/svg%3E';" 
                                                 src="{{ $sub->image_url }}" 
                                                 class="w-full h-full object-cover hover:scale-105 transition" 
                                                 alt="{{ $sub->judul_karya }}">
                                        </div>
                                        <div class="space-y-1 min-w-0">
                                            <h4 class="font-black text-gray-900 dark:text-white text-sm tracking-tight leading-snug hover:text-amber-600 transition cursor-pointer line-clamp-1"
                                                @click="openSubmissionReview({{ json_encode($subPayload) }}, '{{ route('admin.contests.submission.winner', $sub->id) }}')">
                                                {{ $sub->judul_karya }}
                                            </h4>
                                            <p class="text-[11px] text-gray-500 dark:text-gray-400 line-clamp-2 leading-relaxed">
                                                {{ $sub->deskripsi_karya ?: 'Tidak ada deskripsi tambahan.' }}
                                            </p>
                                            @if($sub->media_link)
                                                <a href="{{ $sub->media_link }}" target="_blank" rel="noopener noreferrer" class="inline-block text-[10px] font-semibold text-gray-900 dark:text-white underline">
                                                    Tautan Media Eksternal
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Col 2: Peserta -->
                                <td class="py-4 px-4 sm:px-6 align-top whitespace-nowrap">
                                    <div class="space-y-0.5">
                                        <span class="font-bold text-gray-900 dark:text-white block">{{ $sub->user->nama ?? 'Peserta' }}</span>
                                        <span class="text-[11px] text-gray-400 block">{{ $sub->user->email ?? '-' }}</span>
                                        <span class="text-[10px] text-gray-400 font-medium block pt-0.5">{{ $sub->created_at ? $sub->created_at->translatedFormat('d M Y, H:i') . ' WIB' : '-' }}</span>
                                    </div>
                                </td>

                                <!-- Col 3: Status Penetapan (Plain Dark Text) -->
                                <td class="py-4 px-4 sm:px-6 align-top whitespace-nowrap">
                                    @if($sub->status_submission === 'winner_1')
                                        <span class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider block">Juara 1</span>
                                    @elseif($sub->status_submission === 'winner_2')
                                        <span class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider block">Juara 2</span>
                                    @elseif($sub->status_submission === 'winner_3')
                                        <span class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider block">Juara 3</span>
                                    @elseif($sub->status_submission === 'rejected')
                                        <span class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider block">Ditolak</span>
                                    @elseif($sub->status_submission === 'disqualified')
                                        <span class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider block">Diskualifikasi</span>
                                    @else
                                        <span class="text-xs font-bold text-gray-900 dark:text-white block">Tidak Mendapatkan Juara</span>
                                    @endif
                                </td>

                                <!-- Col 4: Catatan Juri -->
                                <td class="py-4 px-4 sm:px-6 align-top">
                                    @if($sub->catatan_juri)
                                        <p class="text-[11px] text-gray-600 dark:text-gray-300 leading-relaxed line-clamp-2" title="{{ $sub->catatan_juri }}">
                                            "{{ $sub->catatan_juri }}"
                                        </p>
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500 text-xs">—</span>
                                    @endif
                                </td>

                                <!-- Col 5: Aksi -->
                                <td class="py-4 px-4 sm:px-6 align-top text-center whitespace-nowrap">
                                    <button type="button" 
                                            @click="openSubmissionReview({{ json_encode($subPayload) }}, '{{ route('admin.contests.submission.winner', $sub->id) }}')" 
                                            class="px-3.5 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200/80 dark:border-amber-900/60 font-bold text-xs transition cursor-pointer shadow-2xs">
                                        Nilai
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 px-4 text-center text-xs text-gray-400 font-bold">
                                    Belum ada karya foto peserta yang sesuai dengan kriteria pencarian ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Submisi -->
            @if($submissions->hasPages())
                <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                    {{ $submissions->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- MODAL REVIEW PESERTA (Opsi B: Unified Dropdown & Single Action) -->
    <div x-show="participantModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs" 
         style="display: none;"
         @keydown.escape.window="participantModal = false">
        
        <div class="bg-white dark:bg-gray-800 w-full max-w-xl rounded-2xl border border-gray-200 dark:border-gray-700 shadow-2xl overflow-hidden flex flex-col max-h-[90vh]"
             @click.outside="participantModal = false">
            
            <!-- Modal Header (No divider line) -->
            <div class="p-6 pb-2 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-amber-600 dark:text-amber-500 block">Review Calon Peserta</span>
                    <h3 class="text-base font-black text-gray-900 dark:text-white tracking-tight" x-text="selectedParticipant.nama_lengkap"></h3>
                </div>
                <button type="button" @click="participantModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-lg font-bold p-1 cursor-pointer">
                    ✕
                </button>
            </div>

            <!-- Modal Form (Unified Single Form) -->
            <form :action="selectedParticipant.update_url" method="POST" class="flex flex-col flex-1 overflow-hidden">
                @csrf
                <div class="p-6 space-y-5 overflow-y-auto flex-1">
                    <!-- Info Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div>
                            <span class="text-gray-400 block text-[10px] uppercase font-bold">Email</span>
                            <span class="font-bold text-gray-900 dark:text-white" x-text="selectedParticipant.email"></span>
                        </div>
                        <div>
                            <span class="text-gray-400 block text-[10px] uppercase font-bold">No. WhatsApp</span>
                            <span class="font-bold text-gray-900 dark:text-white" x-text="selectedParticipant.no_wa"></span>
                        </div>
                        <div>
                            <span class="text-gray-400 block text-[10px] uppercase font-bold">Instansi / Asal</span>
                            <span class="font-bold text-gray-900 dark:text-white" x-text="selectedParticipant.instansi + ' (' + selectedParticipant.asal_kota + ')'"></span>
                        </div>
                        <div>
                            <span class="text-gray-400 block text-[10px] uppercase font-bold">Status Saat Ini</span>
                            <span class="font-bold text-gray-900 dark:text-white" 
                                  x-text="selectedParticipant.status_pendaftaran === 'disetujui' ? 'Disetujui' : (selectedParticipant.status_pendaftaran === 'ditolak' ? 'Ditolak' : 'Menunggu Verifikasi')"></span>
                        </div>
                    </div>

                    <!-- Bio Singkat (Clean Plain Text) -->
                    <div>
                        <span class="text-gray-400 block text-[10px] uppercase font-bold mb-1">Bio / Pengantar Singkat</span>
                        <p class="text-xs text-gray-800 dark:text-gray-200 leading-relaxed font-medium" x-text="selectedParticipant.bio_singkat"></p>
                    </div>

                    <!-- Dokumen Kualifikasi (Clean Underlined Links) -->
                    <div class="space-y-1">
                        <span class="text-gray-400 block text-[10px] uppercase font-bold">Dokumen Kualifikasi</span>
                        <div class="flex flex-wrap gap-4 items-center pt-0.5">
                            <template x-if="selectedParticipant.cv_portfolio_file">
                                <a :href="selectedParticipant.cv_portfolio_file" target="_blank" class="text-xs font-bold text-gray-900 dark:text-white underline hover:text-amber-600 transition inline-flex items-center gap-1.5">
                                    <span>Buka File CV / Portofolio (PDF)</span>
                                </a>
                            </template>
                            <template x-if="selectedParticipant.portfolio_link">
                                <a :href="selectedParticipant.portfolio_link" target="_blank" class="text-xs font-bold text-gray-900 dark:text-white underline hover:text-amber-600 transition inline-flex items-center gap-1.5">
                                    <span>Kunjungi Tautan Portofolio</span>
                                </a>
                            </template>
                        </div>
                    </div>

                    <!-- Bukti Transfer (Jika ada) -->
                    <template x-if="selectedParticipant.bukti_transfer">
                        <div class="space-y-2 pt-1">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-400 block text-[10px] uppercase font-bold">Bukti Transfer Pembayaran</span>
                                <span class="text-xs font-bold text-gray-900 dark:text-white" x-text="selectedParticipant.status_pembayaran === 'lunas' ? 'Status: Lunas' : 'Status: Menunggu Konfirmasi'"></span>
                            </div>
                            <a :href="selectedParticipant.bukti_transfer" 
                               target="_blank" 
                               title="Klik untuk melihat bukti transfer ukuran penuh" 
                               class="block group rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 relative h-40 flex items-center justify-center cursor-pointer">
                                <img :src="selectedParticipant.bukti_transfer" class="w-full h-full object-contain group-hover:scale-102 transition duration-200">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-1.5 text-white text-xs font-bold">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                    <span>Buka Bukti Transfer Penuh</span>
                                </div>
                            </a>
                        </div>
                    </template>

                    <!-- Dropdown Form Section (Grid 2 Kolom jika ada Bukti Transfer) -->
                    <div class="space-y-4 pt-1" :class="selectedParticipant.bukti_transfer ? 'grid grid-cols-1 sm:grid-cols-2 gap-4 space-y-0' : ''">
                        <!-- Dropdown 1: Keputusan Status Pendaftaran -->
                        <div class="space-y-1.5" x-data="{
                            openPartDropdown: false,
                            get currentPartLabel() {
                                const map = {
                                    'menunggu_verifikasi': 'Menunggu Verifikasi',
                                    'disetujui': 'Disetujui',
                                    'ditolak': 'Ditolak'
                                };
                                return map[selectedParticipant.status_pendaftaran] || 'Menunggu Verifikasi';
                            }
                        }">
                            <label class="block text-[11px] font-extrabold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                Keputusan Status Pendaftaran <span class="text-rose-500">*</span>
                            </label>
                            <input type="hidden" name="status_pendaftaran" :value="selectedParticipant.status_pendaftaran">

                            <div class="relative">
                                <button type="button" 
                                        @click="openPartDropdown = !openPartDropdown"
                                        class="w-full flex items-center justify-between px-4 py-3 text-xs font-bold rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition focus:outline-none focus:ring-2 focus:ring-amber-500/20 cursor-pointer">
                                    <span x-text="currentPartLabel"></span>
                                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 shrink-0" :class="{ 'rotate-180': openPartDropdown }" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>

                                <div x-show="openPartDropdown" 
                                     @click.outside="openPartDropdown = false"
                                     x-transition 
                                     class="absolute z-50 top-full mt-1.5 left-0 w-full bg-white dark:bg-gray-900 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 p-1.5 space-y-0.5 overflow-hidden"
                                     style="display: none;">
                                    <button type="button" 
                                            @click="selectedParticipant.status_pendaftaran = 'menunggu_verifikasi'; openPartDropdown = false" 
                                            class="w-full text-left px-3.5 py-2.5 text-xs font-bold rounded-lg text-gray-800 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-700 dark:hover:text-amber-300 transition cursor-pointer"
                                            :class="selectedParticipant.status_pendaftaran === 'menunggu_verifikasi' ? 'bg-amber-50 dark:bg-amber-950/40 text-amber-600 font-black' : ''">
                                        Menunggu Verifikasi
                                    </button>
                                    <button type="button" 
                                            @click="selectedParticipant.status_pendaftaran = 'disetujui'; openPartDropdown = false" 
                                            class="w-full text-left px-3.5 py-2.5 text-xs font-bold rounded-lg text-gray-800 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-700 dark:hover:text-amber-300 transition cursor-pointer"
                                            :class="selectedParticipant.status_pendaftaran === 'disetujui' ? 'bg-amber-50 dark:bg-amber-950/40 text-amber-600 font-black' : ''">
                                        Disetujui
                                    </button>
                                    <button type="button" 
                                            @click="selectedParticipant.status_pendaftaran = 'ditolak'; openPartDropdown = false" 
                                            class="w-full text-left px-3.5 py-2.5 text-xs font-bold rounded-lg text-gray-800 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-700 dark:hover:text-amber-300 transition cursor-pointer"
                                            :class="selectedParticipant.status_pendaftaran === 'ditolak' ? 'bg-amber-50 dark:bg-amber-950/40 text-amber-600 font-black' : ''">
                                        Ditolak
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Dropdown 2: Status Verifikasi Pembayaran (Jika ada Bukti Transfer) -->
                        <template x-if="selectedParticipant.bukti_transfer">
                            <div class="space-y-1.5" x-data="{
                                openPayDropdown: false,
                                get currentPayLabel() {
                                    const map = {
                                        'menunggu_konfirmasi': 'Menunggu Konfirmasi',
                                        'lunas': 'Lunas',
                                        'menunggu_pembayaran': 'Menunggu Pembayaran'
                                    };
                                    return map[selectedParticipant.status_pembayaran] || 'Pilih Status Pembayaran';
                                }
                            }">
                                <label class="block text-[11px] font-extrabold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    Status Verifikasi Pembayaran <span class="text-rose-500">*</span>
                                </label>
                                <input type="hidden" name="status_pembayaran" :value="selectedParticipant.status_pembayaran">

                                <div class="relative">
                                    <button type="button" 
                                            @click="openPayDropdown = !openPayDropdown"
                                            class="w-full flex items-center justify-between px-4 py-3 text-xs font-bold rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition focus:outline-none focus:ring-2 focus:ring-amber-500/20 cursor-pointer">
                                        <span x-text="currentPayLabel"></span>
                                        <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 shrink-0" :class="{ 'rotate-180': openPayDropdown }" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>

                                    <div x-show="openPayDropdown" 
                                         @click.outside="openPayDropdown = false"
                                         x-transition 
                                         class="absolute z-50 top-full mt-1.5 left-0 w-full bg-white dark:bg-gray-900 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 p-1.5 space-y-0.5 overflow-hidden"
                                         style="display: none;">
                                        <button type="button" 
                                                @click="selectedParticipant.status_pembayaran = 'menunggu_konfirmasi'; openPayDropdown = false" 
                                                class="w-full text-left px-3.5 py-2.5 text-xs font-bold rounded-lg text-gray-800 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-700 dark:hover:text-amber-300 transition cursor-pointer"
                                                :class="selectedParticipant.status_pembayaran === 'menunggu_konfirmasi' ? 'bg-amber-50 dark:bg-amber-950/40 text-amber-600 font-black' : ''">
                                            Menunggu Konfirmasi
                                        </button>
                                        <button type="button" 
                                                @click="selectedParticipant.status_pembayaran = 'lunas'; openPayDropdown = false" 
                                                class="w-full text-left px-3.5 py-2.5 text-xs font-bold rounded-lg text-gray-800 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-700 dark:hover:text-amber-300 transition cursor-pointer"
                                                :class="selectedParticipant.status_pembayaran === 'lunas' ? 'bg-amber-50 dark:bg-amber-950/40 text-amber-600 font-black' : ''">
                                            Lunas
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Catatan Admin Textarea (Required if Ditolak) -->
                    <div class="space-y-1.5 pt-1" x-show="selectedParticipant.status_pendaftaran === 'ditolak'" x-transition>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-rose-500">
                            Catatan / Alasan Penolakan <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="catatan_admin" 
                                  rows="2" 
                                  x-model="selectedParticipant.catatan_admin"
                                  :required="selectedParticipant.status_pendaftaran === 'ditolak'"
                                  placeholder="Tuliskan alasan penolakan calon peserta..." 
                                  class="w-full px-4 py-2.5 text-xs rounded-xl border border-rose-300 dark:border-rose-900 bg-rose-50/40 dark:bg-rose-950/20 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 outline-none transition resize-none leading-relaxed"></textarea>
                    </div>
                </div>

                <!-- Modal Footer (No divider line) -->
                <div class="p-6 pt-2 flex items-center justify-end gap-3 shrink-0">
                    <button type="button" @click="participantModal = false" class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-xs font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs shadow-sm transition active:scale-95 cursor-pointer">
                        Simpan Data
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- MODAL REVIEW SUBMISI KARYA -->
    <div x-show="submissionModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs" 
         style="display: none;"
         @keydown.escape.window="submissionModal = false">
        
        <div class="bg-white dark:bg-gray-800 w-full max-w-2xl rounded-2xl border border-gray-200 dark:border-gray-700 shadow-2xl overflow-hidden flex flex-col max-h-[90vh]"
             @click.outside="submissionModal = false">
            
            <div class="p-6 pb-2 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-amber-600 dark:text-amber-500 block">Inspeksi & Penilaian Karya</span>
                    <h3 class="text-base font-black text-gray-900 dark:text-white tracking-tight" x-text="selectedSubmission.judul_karya"></h3>
                </div>
                <button type="button" @click="submissionModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-lg font-bold p-1 cursor-pointer">
                    ✕
                </button>
            </div>

            <div class="p-6 space-y-5 overflow-y-auto flex-1">
                <!-- Compact Image Preview with Full Photo Click -->
                <a :href="selectedSubmission.image_url" 
                   target="_blank" 
                   title="Klik untuk melihat foto ukuran penuh" 
                   class="block group rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 relative h-48 sm:h-52 flex items-center justify-center cursor-pointer">
                    <img :src="selectedSubmission.image_url" 
                         alt="Preview Karya" 
                         class="w-full h-full object-contain group-hover:scale-102 transition duration-200">
                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-1.5 text-white text-xs font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        <span>Lihat Foto Penuh</span>
                    </div>
                </a>

                <!-- Detail Info Section -->
                <div class="space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs">
                        <div>
                            <span class="text-gray-400 block text-[10px] uppercase font-bold">Peserta</span>
                            <span class="font-bold text-gray-900 dark:text-white" x-text="selectedSubmission.user_name"></span>
                            <span class="text-gray-400 text-[11px] block" x-text="selectedSubmission.user_email"></span>
                        </div>
                        <div class="sm:text-right">
                            <span class="text-gray-400 block text-[10px] uppercase font-bold">Waktu Pengumpulan</span>
                            <span class="font-medium text-gray-600 dark:text-gray-300" x-text="selectedSubmission.created_at_human"></span>
                        </div>
                    </div>

                    <div>
                        <span class="text-gray-400 block text-[10px] uppercase font-bold mb-1">Deskripsi Karya</span>
                        <p class="text-xs text-gray-700 dark:text-gray-300 leading-relaxed" x-text="selectedSubmission.deskripsi_karya"></p>
                    </div>

                    <template x-if="selectedSubmission.media_link">
                        <div class="pt-1">
                            <span class="text-gray-400 block text-[10px] uppercase font-bold mb-1">Tautan Media Eksternal</span>
                            <a :href="selectedSubmission.media_link" target="_blank" class="text-xs font-bold text-gray-900 dark:text-white underline break-all" x-text="selectedSubmission.media_link"></a>
                        </div>
                    </template>
                </div>

                <!-- Grading / Status Assignment Form (No divider line) -->
                <form :action="selectedSubmission.update_url" method="POST" class="space-y-4 pt-2">
                    @csrf
                    
                    @php
                        $statusModalOptions = [
                            'winner_1' => 'Juara 1',
                            'winner_2' => 'Juara 2',
                            'winner_3' => 'Juara 3',
                            'verified' => 'Tidak Mendapatkan Juara',
                            'rejected' => 'Ditolak',
                            'disqualified' => 'Diskualifikasi',
                        ];
                    @endphp

                    <!-- Status Selection Dropdown -->
                    <div class="space-y-1.5" x-data="{
                        openModalDropdown: false,
                        get currentLabel() {
                            const map = {
                                'winner_1': 'Juara 1',
                                'winner_2': 'Juara 2',
                                'winner_3': 'Juara 3',
                                'verified': 'Tidak Mendapatkan Juara',
                                'rejected': 'Ditolak',
                                'disqualified': 'Diskualifikasi'
                            };
                            return map[selectedSubmission.status_submission] || 'Pilih Status Penetapan';
                        }
                    }">
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            Status Penetapan Juara / Verifikasi <span class="text-rose-500">*</span>
                        </label>
                        <input type="hidden" name="status_submission" :value="selectedSubmission.status_submission">

                        <div class="relative">
                            <button type="button" 
                                    @click="openModalDropdown = !openModalDropdown"
                                    class="w-full flex items-center justify-between px-4 py-3 text-xs font-bold rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition focus:outline-none focus:ring-2 focus:ring-amber-500/20 cursor-pointer">
                                <span x-text="currentLabel"></span>
                                <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 shrink-0" :class="{ 'rotate-180': openModalDropdown }" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div x-show="openModalDropdown" 
                                 @click.outside="openModalDropdown = false"
                                 x-transition 
                                 class="absolute z-50 top-full mt-1.5 left-0 w-full bg-white dark:bg-gray-900 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 p-1.5 space-y-0.5 overflow-hidden"
                                 style="display: none;">
                                @foreach($statusModalOptions as $optVal => $optLabel)
                                    <button type="button" 
                                            @click="selectedSubmission.status_submission = '{{ $optVal }}'; openModalDropdown = false" 
                                            class="w-full text-left px-3.5 py-2.5 text-xs font-bold rounded-lg text-gray-800 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-700 dark:hover:text-amber-300 transition cursor-pointer"
                                            :class="selectedSubmission.status_submission === '{{ $optVal }}' ? 'bg-amber-50 dark:bg-amber-950/40 text-amber-600 font-black' : ''">
                                        {{ $optLabel }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Catatan Juri Textarea -->
                    <div class="space-y-1.5">
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            Catatan / Alasan Penilaian Juri
                        </label>
                        <textarea name="catatan_juri" 
                                  rows="3" 
                                  x-model="selectedSubmission.catatan_juri"
                                  placeholder="Tuliskan catatan, feedback, atau alasan penetapan peringkat karya ini..." 
                                  class="w-full px-4 py-3 text-xs rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none transition resize-none leading-relaxed"></textarea>
                    </div>

                    <!-- Modal Actions -->
                    <div class="flex items-center justify-end gap-3 pt-3">
                        <button type="button" @click="submissionModal = false" class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-xs font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 transition cursor-pointer">
                            Tutup
                        </button>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs shadow-sm transition active:scale-95 cursor-pointer">
                            Simpan Data
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

</div>
@endsection
