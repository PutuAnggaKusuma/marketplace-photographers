@extends('layouts.app')

@section('title', 'Riwayat Pengajuan Lomba Foto — LensMatch')

@section('content')
<section class="pt-8 pb-16 lg:pt-10 lg:pb-24 bg-gray-50/50 dark:bg-gray-900/50 min-h-screen">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

    <!-- Top Header & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div class="space-y-1">

        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white tracking-tight">
          Riwayat Pengajuan Lomba Foto
        </h1>
      </div>

      <div class="flex items-center gap-3 shrink-0">
        <a href="{{ route('public.contests.propose.create') }}" class="px-5 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs rounded-xl shadow-xs transition flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path></svg>
          <span>Ajukan Lomba Baru</span>
        </a>
      </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
      <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 text-xs text-emerald-800 dark:text-emerald-300 font-bold flex items-center gap-2">
        <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
        <span>{{ session('success') }}</span>
      </div>
    @endif

    <!-- Proposals List Cards -->
    <div class="space-y-4">
      @forelse($proposals as $contest)
        <div class="bg-white dark:bg-gray-800 p-6 sm:p-7 rounded-3xl border border-gray-200 dark:border-gray-700/80 shadow-xs space-y-5 transition hover:border-amber-400/60">
          
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <!-- Banner + Title + Dates -->
            <div class="flex items-center gap-4 min-w-0">
              <div class="w-20 h-14 sm:w-28 sm:h-18 rounded-2xl overflow-hidden bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 shrink-0">
                <img 
                  onerror="this.onerror=null;this.src=window.SVG_IMAGE_FALLBACK;"
                  src="{{ $contest->banner_url }}" 
                  alt="{{ $contest->judul_lomba }}" 
                  class="w-full h-full object-cover"
                >
              </div>
              <div class="space-y-1 min-w-0">
                <span class="text-[10px] font-black uppercase tracking-wider text-amber-600 dark:text-amber-400 block">
                  {{ $contest->kategori }}
                </span>
                <h3 class="text-sm sm:text-base font-black text-gray-900 dark:text-white truncate">
                  {{ $contest->judul_lomba }}
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-2">
                  <span>Diajukan: {{ $contest->created_at->translatedFormat('d M Y, H:i') }}</span>
                  <span>•</span>
                  <span>Hadiah: <strong class="text-gray-900 dark:text-white">{{ $contest->hadiah }}</strong></span>
                </p>
              </div>
            </div>

            <!-- Status Badges -->
            <div class="shrink-0">
              @if($contest->status === 'pending_review')
                <span class="px-3.5 py-1.5 rounded-full bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 font-extrabold text-xs inline-flex items-center gap-1.5 border border-amber-300/80 dark:border-amber-800">
                  <span>Menunggu Verifikasi Admin</span>
                </span>
              @elseif($contest->status === 'buka')
                <span class="px-3.5 py-1.5 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 font-extrabold text-xs inline-flex items-center gap-1.5 border border-emerald-300/80 dark:border-emerald-800">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                  <span>Disetujui & Tayang di Publik</span>
                </span>
              @elseif($contest->status === 'ditolak')
                <span class="px-3.5 py-1.5 rounded-full bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300 font-extrabold text-xs inline-flex items-center gap-1.5 border border-rose-300/80 dark:border-rose-800">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                  <span>Ditolak / Perlu Revisi</span>
                </span>
              @else
                <span class="px-3.5 py-1.5 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-extrabold text-xs">
                  {{ ucfirst($contest->status) }}
                </span>
              @endif
            </div>
          </div>

          <!-- If Rejected: Show Admin Rejection Notes -->
          @if($contest->status === 'ditolak' && $contest->catatan_admin)
            <div class="p-4 rounded-2xl bg-rose-50/80 dark:bg-rose-950/40 border border-rose-200/90 dark:border-rose-800/60 space-y-1 text-xs text-rose-900 dark:text-rose-200">
              <strong class="font-extrabold flex items-center gap-1.5 text-rose-800 dark:text-rose-300">
                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <span>Catatan Penolakan dari Admin:</span>
              </strong>
              <p class="leading-relaxed pl-5 font-medium">{{ $contest->catatan_admin }}</p>
            </div>
          @endif

          <!-- Action Links & Detail View -->
          <div class="flex items-center justify-between pt-2 border-t border-gray-100 dark:border-gray-700/60 text-xs">
            <div class="text-gray-500 dark:text-gray-400">
              Periode: <strong class="text-gray-800 dark:text-gray-200">{{ $contest->start_date ? $contest->start_date->translatedFormat('d M Y') : '-' }}</strong> s/d <strong class="text-gray-800 dark:text-gray-200">{{ $contest->end_date ? $contest->end_date->translatedFormat('d M Y') : '-' }}</strong>
            </div>

            <div class="flex items-center gap-3">
              @if($contest->status === 'buka' || $contest->status === 'selesai')
                <a href="{{ route('public.contests.show', $contest->id) }}" class="px-4 py-2 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-950 font-black text-xs transition shadow-2xs">
                  Lihat Halaman Publik
                </a>
              @endif
            </div>
          </div>

        </div>
      @empty
        <div class="bg-white dark:bg-gray-800 p-12 text-center rounded-3xl border border-gray-200 dark:border-gray-700/80 space-y-4">
          <div class="w-14 h-14 rounded-2xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 flex items-center justify-center mx-auto">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"></path></svg>
          </div>
          <div class="space-y-1">
            <h4 class="text-sm font-black text-gray-900 dark:text-white">Belum Ada Pengajuan Lomba</h4>
            <p class="text-xs text-gray-400">Anda belum pernah mengajukan event lomba foto baru di LensMatch.</p>
          </div>

        </div>
      @endforelse

      @if($proposals->total() > 0)
        <div class="pt-4">
          {{ $proposals->links('partials.dashboard.pagination') }}
        </div>
      @endif
    </div>

  </div>
</section>
@endsection
