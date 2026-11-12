@extends('layouts.admin')



@section('title', 'Kelola Karya Submisi Lomba Foto — Admin')



@section('content')

<div class="space-y-6" x-data="{

    // Modal State

    modalOpen: false,

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

    openReview(sub, updateUrl) {

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

        this.submissionStatusTouched = false;

        this.submissionStatusError = '';

        this.modalOpen = true;

    },

    submissionStatusTouched: false,

    submissionStatusError: '',

    validateSubmissionStatus() {

        if (!this.selectedSubmission.status_submission) {

            this.submissionStatusError = 'Status penetapan karya wajib dipilih.';

            return false;

        }

        this.submissionStatusError = '';

        return true;

    },

    submitSubmissionReview(e) {

        this.submissionStatusTouched = true;

        if (!this.validateSubmissionStatus()) {

            e.preventDefault();

            this.$refs.submissionStatusBtn?.focus();

            this.$refs.submissionStatusBtn?.scrollIntoView({ behavior: 'smooth', block: 'center' });

            return false;

        }

    },

    // Filter & Search

    isLoading: false,

    applyFilter(key, val) {

        const url = new URL(window.location.href);

        if (val === 'all' || val === '' || val === null) {

            url.searchParams.delete(key);

        } else {

            url.searchParams.set(key, val);

        }

        url.searchParams.delete('page');

        window.location.href = url.toString();

    },

    handleSearch(e) {

        clearTimeout(this.searchTimer);

        this.searchTimer = setTimeout(() => {

            this.applyFilter('search', e.target.value);

        }, 500);

    }

}">



    <!-- Standard Golden Header -->

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">
                Karya Submisi: {{ $contest->judul_lomba }}
            </h1>
        </div>

        <div>

            <a href="{{ route('admin.contests') }}" class="px-4 py-2 rounded-xl bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-750 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 font-bold text-xs transition inline-flex items-center gap-1.5 shadow-2xs shrink-0">

                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"></path></svg>

                <span>Kembali</span>

            </a>

        </div>

    </div>



    <!-- Summary Metric Stat Cards (Neutral Subtitles - Rule #8 & #9) -->

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">

        <!-- Card 1: Total Submisi -->

        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">

            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Total Karya Masuk</span>

            <div class="text-xs font-black text-gray-900 dark:text-white tracking-tight">{{ $totalSubmissions ?? 0 }} Karya</div>

        </div>



        <!-- Card 2: Juara Ditetapkan -->

        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">

            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Pemenang Juara</span>

            <div class="text-xs font-black text-gray-900 dark:text-white tracking-tight">{{ $totalWinners ?? 0 }} Juara</div>

        </div>



        <!-- Card 3: Tidak Mendapatkan Juara -->

        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">

            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Peserta Terverifikasi</span>

            <div class="text-xs font-black text-gray-900 dark:text-white tracking-tight">{{ $totalVerified ?? 0 }} Karya</div>

        </div>



        <!-- Card 4: Ditolak / Diskualifikasi -->

        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">

            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Ditolak / Diskualifikasi</span>

            <div class="text-xs font-black text-gray-900 dark:text-white tracking-tight">{{ $totalRejected ?? 0 }} Karya</div>

        </div>

    </div>



    <!-- Status Tabs / Filter Pills -->

    <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs font-bold">

        <button type="button" @click="applyFilter('status', 'all')" 

                class="px-4 py-2 rounded-xl transition cursor-pointer shrink-0 {{ ($status ?? 'all') === 'all' ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-950 shadow-xs' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:border-gray-300' }}">

            Semua ({{ $totalSubmissions ?? 0 }})

        </button>

        <button type="button" @click="applyFilter('status', 'winners')" 

                class="px-4 py-2 rounded-xl transition cursor-pointer shrink-0 {{ ($status ?? '') === 'winners' ? 'bg-amber-400 text-gray-950 font-black shadow-xs' : 'bg-white dark:bg-gray-800 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800/80 hover:bg-amber-50' }}">

            Juara 1-3 ({{ $totalWinners ?? 0 }})

        </button>

        <button type="button" @click="applyFilter('status', 'verified')" 

                class="px-4 py-2 rounded-xl transition cursor-pointer shrink-0 {{ ($status ?? '') === 'verified' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:border-gray-300' }}">

            Tidak Mendapat Juara ({{ $totalVerified ?? 0 }})

        </button>

        <button type="button" @click="applyFilter('status', 'rejected_disqualified')" 

                class="px-4 py-2 rounded-xl transition cursor-pointer shrink-0 {{ ($status ?? '') === 'rejected_disqualified' ? 'bg-rose-600 text-white shadow-xs' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:border-gray-300' }}">

            Ditolak / Diskualifikasi ({{ $totalRejected ?? 0 }})

        </button>

    </div>



    <!-- Standard Data Table Card -->

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm overflow-hidden">

        

        <!-- Search Toolbar & Sort -->

        <div class="p-4 flex flex-col md:flex-row items-center justify-between gap-4">

            <div class="flex items-center gap-3 w-full md:w-auto">

                @php

                    $sortLabels = [

                        'latest' => 'Karya Terbaru',

                        'oldest' => 'Karya Terlama',

                        'winners_first' => 'Pemenang Juara Dahulu',

                    ];

                    $curSortName = $sortLabels[$sort ?? 'latest'] ?? 'Karya Terbaru';

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



            <!-- Search di Kanan (Rule #9 - Icon di Kanan) -->

            <div class="relative w-full md:w-80">

                <input type="text" 

                       name="search" 

                       value="{{ $search }}" @input="handleSearch($event)" 

                       placeholder="Cari judul karya atau peserta..." 

                       class="w-full pl-4 pr-10 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">

                <svg class="w-4 h-4 text-gray-400 absolute right-3.5 top-2.5 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>

            </div>

        </div>



        <!-- Table Container -->

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

                                'created_at_human' => $sub->created_at->diffForHumans(),

                            ];

                        @endphp

                        <tr class="hover:bg-amber-50/30 dark:hover:bg-amber-950/20 transition">

                            <!-- Col 1: Thumbnail + Judul + Deskripsi -->

                            <td class="py-4 px-4 sm:px-6 align-top">

                                <div class="flex items-start gap-3.5">

                                    <div class="w-16 h-12 rounded-xl bg-gray-100 dark:bg-gray-700 overflow-hidden shrink-0 border border-gray-200 dark:border-gray-600 shadow-xs cursor-pointer"

                                         @click="openReview({{ json_encode($subPayload) }}, '{{ route('admin.contests.submission.winner', $sub->id) }}')">

                                        <img onerror="this.onerror=null;this.src=window.SVG_IMAGE_FALLBACK||'data:image/svg+xml;charset=UTF-8,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'800\' height=\'600\' viewBox=\'0 0 800 600\' fill=\'none\'%3E%3Crect width=\'800\' height=\'600\' fill=\'%23F3F4F6\'/%3E%3Cpath d=\'M360 260C360 248.954 368.954 240 380 240H420C431.046 240 440 248.954 440 260V265H450C466.569 265 480 278.431 480 295V345C480 361.569 466.569 375 450 375H350C333.431 375 320 361.569 320 345V295C320 278.431 333.431 265 350 265H360V260Z\' stroke=\'%239CA3AF\' stroke-width=\'12\' stroke-linecap=\'round\' stroke-linejoin=\'round\'/%3E%3Ccircle cx=\'400\' cy=\'320\' r=\'30\' stroke=\'%239CA3AF\' stroke-width=\'12\'/%3E%3Cline x1=\'310\' y1=\'240\' x2=\'490\' y2=\'390\' stroke=\'%23EF4444\' stroke-width=\'10\' stroke-linecap=\'round\'/%3E%3Ctext x=\'400\' y=\'450\' font-family=\'sans-serif\' font-size=\'22\' font-weight=\'700\' fill=\'%236B7280\' text-anchor=\'middle\'%3EGambar Tidak Dapat Dimuat%3C/text%3E%3C/svg%3E';" 

                                             src="{{ $sub->image_url }}" 

                                             class="w-full h-full object-cover hover:scale-105 transition" 

                                             alt="{{ $sub->judul_karya }}">

                                    </div>

                                    <div class="space-y-1 min-w-0">

                                        <h4 class="font-black text-gray-900 dark:text-white text-xs tracking-tight leading-snug hover:text-amber-600 transition cursor-pointer line-clamp-1"

                                            @click="openReview({{ json_encode($subPayload) }}, '{{ route('admin.contests.submission.winner', $sub->id) }}')">

                                            {{ $sub->judul_karya }}

                                        </h4>

                                        <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2 leading-relaxed">

                                            {{ $sub->deskripsi_karya ?: 'Tidak ada deskripsi tambahan.' }}

                                        </p>

                                        @if($sub->media_link)

                                            <a href="{{ $sub->media_link }}" target="_blank" rel="noopener noreferrer" class="inline-block text-xs font-semibold text-gray-900 dark:text-white underline">

                                                Tautan Media Eksternal

                                            </a>

                                        @endif

                                    </div>

                                </div>

                            </td>



                            <!-- Col 2: Peserta -->

                            <td class="py-4 px-4 sm:px-6 align-top whitespace-nowrap">

                                <div class="space-y-0.5">

                                    <span class="font-bold text-gray-900 dark:text-white block">

                                        {{ $sub->user->nama ?? 'Peserta' }}

                                    </span>

                                    <span class="text-xs text-gray-400 block">

                                        {{ $sub->user->email ?? '-' }}

                                    </span>

                                    <span class="text-xs text-gray-400 font-medium block pt-0.5">

                                        {{ $sub->created_at->diffForHumans() }}

                                    </span>

                                </div>

                            </td>



                            <!-- Col 3: Status Penetapan (Pure Colored Text - Rule #14) -->

                            <td class="py-4 px-4 sm:px-6 align-top whitespace-nowrap">

                                @if($sub->status_submission === 'winner_1')

                                    <span class="text-xs font-black text-amber-600 dark:text-amber-400 uppercase tracking-wider block">

                                        Juara 1 (Utama)

                                    </span>

                                @elseif($sub->status_submission === 'winner_2')

                                    <span class="text-xs font-black text-slate-600 dark:text-slate-400 uppercase tracking-wider block">

                                        Juara 2

                                    </span>

                                @elseif($sub->status_submission === 'winner_3')

                                    <span class="text-xs font-black text-amber-700 dark:text-amber-500 uppercase tracking-wider block">

                                        Juara 3

                                    </span>

                                @elseif($sub->status_submission === 'rejected')

                                    <span class="text-xs font-bold text-rose-600 dark:text-rose-400 uppercase tracking-wider block">

                                        Ditolak

                                    </span>

                                @elseif($sub->status_submission === 'disqualified')

                                    <span class="text-xs font-bold text-red-700 dark:text-red-400 uppercase tracking-wider block">

                                        Diskualifikasi

                                    </span>

                                @else

                                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 block">

                                        Tidak Mendapatkan Juara

                                    </span>

                                @endif

                            </td>



                            <!-- Col 4: Catatan Juri -->

                            <td class="py-4 px-4 sm:px-6 align-top">

                                @if($sub->catatan_juri)

                                    <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed line-clamp-2" title="{{ $sub->catatan_juri }}">

                                        "{{ $sub->catatan_juri }}"

                                    </p>

                                @else

                                    <span class="text-gray-400 dark:text-gray-500 text-xs">—</span>

                                @endif

                            </td>



                            <!-- Col 5: Aksi (Pure Text Buttons - Rule #10) -->

                            <td class="py-4 px-4 sm:px-6 align-top text-center whitespace-nowrap">

                                <button type="button" 

                                        @click="openReview({{ json_encode($subPayload) }}, '{{ route('admin.contests.submission.winner', $sub->id) }}')" 

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



        <!-- Pagination Footer -->

        @if($submissions->hasPages())

            <div class="p-4 border-t border-gray-100 dark:border-gray-700">

                {{ $submissions->links() }}

            </div>

        @endif

    </div>



    <!-- Review & Grading Modal Dialog -->

    <div x-show="modalOpen" 

         x-transition:enter="transition ease-out duration-200"

         x-transition:enter-start="opacity-0"

         x-transition:enter-end="opacity-100"

         x-transition:leave="transition ease-in duration-150"

         x-transition:leave-start="opacity-100"

         x-transition:leave-end="opacity-0"

         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs" 

         style="display: none;"

         @keydown.escape.window="modalOpen = false">

        

        <div class="bg-white dark:bg-gray-800 w-full max-w-2xl rounded-2xl border border-gray-200 dark:border-gray-700 shadow-2xl overflow-hidden flex flex-col max-h-[90vh]"

             @click.outside="modalOpen = false">

            

            <!-- Modal Header (Rule #59 Compliant: Centered without Icon, NO X button) -->

            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 text-center">

                
                <h3 class="text-base font-black text-gray-900 dark:text-white tracking-tight text-center" x-text="selectedSubmission.judul_karya"></h3>

            </div>



            <!-- Modal Body Scrollable -->

            <div class="p-6 flex flex-col gap-6 overflow-y-auto flex-1">

                <!-- Large Image Preview -->

                <div class="rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 relative">

                    <img :src="selectedSubmission.image_url" 

                         alt="Preview Karya" 

                         class="w-full max-h-80 object-contain mx-auto">

                </div>



                <!-- Detail Info Section -->

                <div class="flex flex-col gap-3">

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

                        <div>

                            <span class="text-gray-400 block text-[10px] uppercase font-bold mb-1">Tautan Media Eksternal</span>

                            <a :href="selectedSubmission.media_link" target="_blank" class="text-xs font-bold text-blue-600 hover:underline break-all" x-text="selectedSubmission.media_link"></a>

                        </div>

                    </template>

                </div>



                <!-- Grading / Status Assignment Form -->

                <form :action="selectedSubmission.update_url" method="POST" @submit="submitSubmissionReview($event)" novalidate class="flex flex-col gap-4 pt-6 border-t border-gray-100 dark:border-gray-700">

                    @csrf

                    

                    @php

                        $statusModalOptions = [

                            'winner_1' => 'Juara 1 (Utama)',

                            'winner_2' => 'Juara 2',

                            'winner_3' => 'Juara 3',

                            'verified' => 'Tidak Mendapatkan Juara',

                            'rejected' => 'Ditolak',

                            'disqualified' => 'Diskualifikasi',

                        ];

                    @endphp



                    <!-- Status Selection Dropdown -->

                    <div class="flex flex-col gap-1.5" x-data="{

                        openModalDropdown: false,

                        get currentLabel() {

                            const map = {

                                'winner_1': 'Juara 1 (Utama)',

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
                                    x-ref="submissionStatusBtn"
                                    @click="openModalDropdown = !openModalDropdown"
                                    class="w-full flex items-center justify-between px-4 py-3 text-xs font-bold rounded-xl border bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition focus:outline-none focus:ring-2 cursor-pointer"
                                    :class="submissionStatusTouched && submissionStatusError ? 'border-red-400 focus:ring-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500/20'">

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
                                            @click="selectedSubmission.status_submission = '{{ $optVal }}'; openModalDropdown = false; submissionStatusTouched = true; validateSubmissionStatus()" 
                                            class="w-full text-left px-3.5 py-2.5 text-xs font-bold rounded-lg text-gray-800 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-700 dark:hover:text-amber-300 transition cursor-pointer"
                                            :class="selectedSubmission.status_submission === '{{ $optVal }}' ? 'bg-amber-50 dark:bg-amber-950/40 text-amber-600 font-black' : ''">

                                        {{ $optLabel }}

                                    </button>

                                @endforeach

                            </div>

                        </div>

                        <p x-show="submissionStatusTouched && submissionStatusError" x-text="submissionStatusError" x-cloak class="text-[11px] font-bold text-red-500"></p>

                    </div>



                    <!-- Catatan Juri Textarea -->

                    <div class="flex flex-col gap-1.5">

                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-gray-500 dark:text-gray-400">

                            Catatan / Alasan Penilaian Juri

                        </label>

                        <textarea name="catatan_juri" 

                                  rows="3" 

                                  x-model="selectedSubmission.catatan_juri"

                                  placeholder="Tuliskan catatan, feedback, atau alasan penetapan peringkat karya ini..." 

                                  class="w-full px-4 py-3 text-xs rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none transition resize-none leading-relaxed"></textarea>

                    </div>



                    <!-- Modal Actions (Rule #59 Compliant: Centered Buttons) -->

                    <div class="flex items-center justify-center gap-3">

                        <button type="button" @click="modalOpen = false" class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-xs font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 transition cursor-pointer">

                            Batal

                        </button>

                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs shadow-xs transition cursor-pointer">

                            Simpan Data

                        </button>

                    </div>

                </form>

            </div>



        </div>

    </div>



</div>

@endsection