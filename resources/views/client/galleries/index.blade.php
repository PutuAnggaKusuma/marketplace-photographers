@extends('layouts.app')

@section('title', 'Galeri Hasil Foto Saya — LensMatch')

@section('content')
<section class="py-10 lg:py-16 bg-gray-50/50 dark:bg-gray-900/50 min-h-screen">
  <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

    <!-- Top Header & Breadcrumb -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div class="space-y-1">
        <div class="flex items-center gap-2 text-xs font-bold text-gray-400">
          <a href="{{ route('client.bookings') }}" class="hover:text-amber-500 transition">Reservasi Saya</a>
          <span>/</span>
          <span class="text-amber-600 dark:text-amber-400">Galeri Hasil Foto</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white tracking-tight">
          Galeri Hasil Foto Saya
        </h1>
        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">
          Akses seluruh tautan album hasil retouching foto resolusi tinggi dari fotografer mitra LensMatch.
        </p>
      </div>

      <div class="flex items-center gap-3 shrink-0">
        <a href="{{ route('client.bookings') }}" class="px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-xs font-bold text-gray-700 dark:text-gray-200 hover:border-amber-400 transition shadow-2xs">
          Lihat Semua Reservasi
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
    @if(session('error'))
      <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 text-xs text-rose-800 dark:text-rose-300 font-bold flex items-center gap-2">
        <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
        <span>{{ session('error') }}</span>
      </div>
    @endif

    <!-- Galleries Card Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      @forelse($contracts as $c)
        @php
          $detail = $c->bookingDetail;
          $photographer = $c->photographer;
          $testimonial = $c->testimonial;
        @endphp
        <div class="bg-white dark:bg-gray-800 p-6 sm:p-7 rounded-3xl border border-gray-200 dark:border-gray-700/80 shadow-xs space-y-5 transition hover:border-amber-400/60 flex flex-col justify-between">
          
          <!-- Upper Section -->
          <div class="space-y-4">
            
            <!-- Header: Photographer Info + Contract Code -->
            <div class="flex items-start justify-between gap-4">
              <div class="flex items-center gap-3.5 min-w-0">
                <div class="w-12 h-12 rounded-full overflow-hidden bg-gray-100 dark:bg-gray-700 border-2 border-amber-400 shrink-0">
                  @if($photographer && $photographer->foto)
                    <img onerror="this.onerror=null;this.src=window.SVG_IMAGE_FALLBACK;" src="{{ $photographer->foto }}" alt="{{ $photographer->nama }}" class="w-full h-full object-cover">
                  @else
                    <div class="w-full h-full flex items-center justify-center bg-amber-400 text-gray-900 font-black text-sm">
                      {{ $photographer ? substr($photographer->nama, 0, 2) : 'FT' }}
                    </div>
                  @endif
                </div>
                <div class="min-w-0">
                  <span class="text-[10px] font-mono font-bold text-gray-400 block">
                    #KTRK-{{ str_pad($c->id, 5, '0', STR_PAD_LEFT) }}
                  </span>
                  <h3 class="text-sm sm:text-base font-black text-gray-900 dark:text-white truncate">
                    {{ $photographer->nama ?? 'Mitra Fotografer' }}
                  </h3>
                  <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                    {{ $detail->service->nama_layanan ?? 'Sesi Fotografi' }}
                  </p>
                </div>
              </div>

              <span class="px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-bold text-[11px] border border-emerald-200/80 dark:border-emerald-800/60 shrink-0">
                Selesai
              </span>
            </div>

            <!-- Session Date & Location Meta -->
            <div class="p-3.5 rounded-2xl bg-gray-50 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-700/60 text-xs text-gray-600 dark:text-gray-300 space-y-1">
              <div class="flex items-center justify-between">
                <span class="text-gray-400 font-medium">Tanggal Sesi:</span>
                <span class="font-bold text-gray-900 dark:text-white">{{ $detail && $detail->booking_date ? \Carbon\Carbon::parse($detail->booking_date)->translatedFormat('d F Y') : '-' }}</span>
              </div>
              @if($detail && $detail->lokasi)
                <div class="flex items-center justify-between">
                  <span class="text-gray-400 font-medium">Lokasi:</span>
                  <span class="font-bold text-gray-900 dark:text-white truncate max-w-[200px]">{{ $detail->lokasi }}</span>
                </div>
              @endif
            </div>

            <!-- Download Google Drive Box -->
            <div class="p-4 rounded-2xl bg-amber-50/70 dark:bg-amber-950/30 border border-amber-200/80 dark:border-amber-800/60 space-y-2 text-xs">
              <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-700 dark:text-amber-400 block">
                Tautan Album Google Drive:
              </span>
              <a href="{{ $detail->hasil_foto_url }}" target="_blank" class="font-mono text-xs text-amber-800 dark:text-amber-300 font-bold hover:underline block truncate">
                {{ $detail->hasil_foto_url }}
              </a>
              @if($detail->catatan_fotografer)
                <p class="text-gray-600 dark:text-gray-300 italic text-[11px] pt-1">
                  "{{ $detail->catatan_fotografer }}"
                </p>
              @endif
            </div>

            <!-- Direct Download Button -->
            <a href="{{ $detail->hasil_foto_url }}" target="_blank" class="w-full py-3 bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs rounded-xl shadow-xs transition text-center block cursor-pointer">
              Buka & Unduh Galeri di Google Drive
            </a>

          </div>

          <!-- Lower Section: Testimonial Status / CTA -->
          <div class="pt-4 border-t border-gray-100 dark:border-gray-700/60">
            @if($testimonial)
              <!-- Already Reviewed Card -->
              <div class="p-3.5 rounded-2xl bg-gray-50 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-700/60 space-y-1.5">
                <div class="flex items-center justify-between">
                  <span class="text-[11px] font-bold text-gray-400">Ulasan Anda</span>
                  <div class="flex items-center gap-1 text-amber-500 font-black text-xs">
                    <svg class="w-3.5 h-3.5 fill-amber-400 text-amber-400" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                    <span>{{ number_format($testimonial->rating, 1) }} / 5.0</span>
                  </div>
                </div>
                <p class="text-xs text-gray-700 dark:text-gray-300 italic line-clamp-2">
                  "{{ $testimonial->deskripsi_review }}"
                </p>
              </div>
            @else
              <!-- Not Yet Reviewed: Button to Open Modal -->
              <button type="button" 
                      @click="$dispatch('open-review-modal', {
                          contractId: {{ $c->id }},
                          photoName: '{{ addslashes($photographer->nama ?? 'Fotografer') }}',
                          serviceName: '{{ addslashes($detail->service->nama_layanan ?? 'Sesi Foto') }}'
                      })"
                      class="w-full py-2.5 px-4 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-bold text-xs transition text-center cursor-pointer">
                Beri Ulasan & Rating Fotografer
              </button>
            @endif
          </div>

        </div>
      @empty
        <div class="col-span-1 md:col-span-2 bg-white dark:bg-gray-800 p-12 text-center rounded-3xl border border-gray-200 dark:border-gray-700/80 space-y-4">
          <div class="w-14 h-14 rounded-2xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 flex items-center justify-center mx-auto">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>
          </div>
          <div class="space-y-1">
            <h4 class="text-sm font-black text-gray-900 dark:text-white">Belum Ada Galeri Foto Yang Selesai</h4>
            <p class="text-xs text-gray-400 max-w-md mx-auto">Tautan galeri foto hasil sesi pemotretan Anda akan tampil otomatis di halaman ini setelah fotografer menyelesaikan proses retouching foto.</p>
          </div>
          <a href="{{ route('client.bookings') }}" class="inline-block px-5 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs rounded-xl shadow-xs transition">
            Lihat Status Reservasi Saya
          </a>
        </div>
      @endforelse
    </div>

    @if($contracts->total() > 0)
      <div class="pt-4">
        {{ $contracts->links('partials.dashboard.pagination') }}
      </div>
    @endif

  </div>
</section>

@include('partials.client.review-modal')
@endsection