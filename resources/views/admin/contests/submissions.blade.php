@extends('layouts.admin')

@section('title', 'Penetapan Pemenang Lomba Foto Admin - LensMatch')

@section('content')
<div class="space-y-6">

    <!-- Back & Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <a href="{{ route('admin.contests') }}" class="text-xs font-bold text-amber-600 hover:underline flex items-center gap-1 mb-1">
                <span>← Kembali ke Daftar Event Lomba</span>
            </a>
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white tracking-tight">Karya Submisi: {{ $contest->judul_lomba }}</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Review foto karya peserta dan tetapkan Pemenang Juara 1, 2, dan 3</p>
        </div>

        <span class="px-4 py-2 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 font-extrabold text-xs border border-amber-200/60 shrink-0">
            Total Hadiah: {{ $contest->hadiah }}
        </span>
    </div>

    <!-- Submissions Gallery Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($submissions as $sub)
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 overflow-hidden shadow-sm flex flex-col justify-between p-4 space-y-4">
                
                <div class="space-y-3">
                    <div class="h-48 rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-900 relative border border-gray-100 dark:border-gray-700">
                        <img onerror="this.onerror=null;this.src=window.SVG_IMAGE_FALLBACK||'data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='800' height='600' viewBox='0 0 800 600' fill='none'%3E%3Crect width='800' height='600' fill='%23F3F4F6'/%3E%3Cpath d='M360 260C360 248.954 368.954 240 380 240H420C431.046 240 440 248.954 440 260V265H450C466.569 265 480 278.431 480 295V345C480 361.569 466.569 375 450 375H350C333.431 375 320 361.569 320 345V295C320 278.431 333.431 265 350 265H360V260Z' stroke='%239CA3AF' stroke-width='12' stroke-linecap='round' stroke-linejoin='round'/%3E%3Ccircle cx='400' cy='320' r='30' stroke='%239CA3AF' stroke-width='12'/%3E%3Cline x1='310' y1='240' x2='490' y2='390' stroke='%23EF4444' stroke-width='10' stroke-linecap='round'/%3E%3Ctext x='400' y='450' font-family='sans-serif' font-size='22' font-weight='700' fill='%236B7280' text-anchor='middle'%3EGambar Tidak Dapat Dimuat%3C/text%3E%3C/svg%3E';" src="{{ $sub->image_url }}" alt="{{ $sub->judul_karya }}" class="w-full h-full object-cover" />
                        
                        <!-- Winner Badge Badge -->
                        @if($sub->status_submission === 'winner_1')
                            <span class="absolute top-3 left-3 px-3 py-1 bg-amber-400 text-gray-900 text-[11px] font-black rounded-lg shadow-md border border-amber-300">
                                🥇 JUARA 1 (UTAMA)
                            </span>
                        @elseif($sub->status_submission === 'winner_2')
                            <span class="absolute top-3 left-3 px-3 py-1 bg-gray-200 text-gray-900 text-[11px] font-black rounded-lg shadow-md border border-gray-300">
                                🥈 JUARA 2
                            </span>
                        @elseif($sub->status_submission === 'winner_3')
                            <span class="absolute top-3 left-3 px-3 py-1 bg-amber-700 text-white text-[11px] font-black rounded-lg shadow-md border border-amber-600">
                                🥉 JUARA 3
                            </span>
                        @endif
                    </div>

                    <div>
                        <h4 class="font-extrabold text-sm text-gray-900 dark:text-white line-clamp-1">{{ $sub->judul_karya }}</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">{{ $sub->deskripsi_karya ?: 'Tidak ada deskripsi tambahan.' }}</p>
                    </div>

                    <div class="pt-2 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between text-xs">
                        <div class="min-w-0">
                            <p class="font-bold text-gray-900 dark:text-white truncate">{{ $sub->user->nama ?? 'Peserta' }}</p>
                            <p class="text-[10px] text-gray-400 truncate">{{ $sub->user->email ?? '-' }}</p>
                        </div>
                        <span class="text-[10px] text-gray-400 font-medium whitespace-nowrap">{{ $sub->created_at->diffForHumans() }}</span>
                    </div>
                </div>

                <!-- Winner Assignment Action Form -->
                <form action="{{ route('admin.contests.submission.winner', $sub->id) }}" method="POST" class="pt-3 border-t border-gray-100 dark:border-gray-700 flex items-center gap-2">
                    @csrf
                    <select name="status_submission" class="w-full px-3 py-1.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs font-bold text-gray-900 dark:text-white outline-none">
                        <option value="verified" {{ $sub->status_submission === 'verified' ? 'selected' : '' }}>Peserta Terverifikasi</option>
                        <option value="winner_1" {{ $sub->status_submission === 'winner_1' ? 'selected' : '' }}>🥇 Tetapkan Juara 1</option>
                        <option value="winner_2" {{ $sub->status_submission === 'winner_2' ? 'selected' : '' }}>🥈 Tetapkan Juara 2</option>
                        <option value="winner_3" {{ $sub->status_submission === 'winner_3' ? 'selected' : '' }}>🥉 Tetapkan Juara 3</option>
                        <option value="rejected" {{ $sub->status_submission === 'rejected' ? 'selected' : '' }}>Diskualifikasi / Tolak</option>
                    </select>
                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-white font-extrabold text-xs shadow-xs transition shrink-0">
                        Simpan
                    </button>
                </form>

            </div>
        @empty
            <div class="col-span-full bg-white dark:bg-gray-800 p-12 rounded-2xl border border-gray-200 dark:border-gray-700 text-center text-xs text-gray-400 font-bold">
                Belum ada foto karya peserta yang didaftarkan pada event lomba ini.
            </div>
        @endforelse
    </div>

</div>
@endsection