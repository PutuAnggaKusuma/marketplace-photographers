@extends('layouts.app')

@section('title', 'Galeri Hasil Foto Saya — LensMatch')

@section('content')
<section class="py-12 bg-gray-50/50 dark:bg-gray-900/50 min-h-screen">
  <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

    <div class="space-y-1">
      <span class="px-3.5 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300">
        Hasil Karya Fotografi
      </span>
      <h1 class="text-3xl font-black text-gray-900 dark:text-white pt-1">Galeri Hasil Foto Saya</h1>
      <p class="text-xs text-gray-500">Kumpulan seluruh tautan galeri foto resolusi tinggi dari sesi foto yang telah selesai dikerjakan studio.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      @forelse($contracts as $c)
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm space-y-4">
          <div class="flex items-center gap-3">
            <img onerror="this.onerror=null;this.src=window.SVG_IMAGE_FALLBACK||'data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='800' height='600' viewBox='0 0 800 600' fill='none'%3E%3Crect width='800' height='600' fill='%23F3F4F6'/%3E%3Cpath d='M360 260C360 248.954 368.954 240 380 240H420C431.046 240 440 248.954 440 260V265H450C466.569 265 480 278.431 480 295V345C480 361.569 466.569 375 450 375H350C333.431 375 320 361.569 320 345V295C320 278.431 333.431 265 350 265H360V260Z' stroke='%239CA3AF' stroke-width='12' stroke-linecap='round' stroke-linejoin='round'/%3E%3Ccircle cx='400' cy='320' r='30' stroke='%239CA3AF' stroke-width='12'/%3E%3Cline x1='310' y1='240' x2='490' y2='390' stroke='%23EF4444' stroke-width='10' stroke-linecap='round'/%3E%3Ctext x='400' y='450' font-family='sans-serif' font-size='22' font-weight='700' fill='%236B7280' text-anchor='middle'%3EGambar Tidak Dapat Dimuat%3C/text%3E%3C/svg%3E';" src="{{ $c->photographer->foto }}" alt="{{ $c->photographer->nama }}" class="w-11 h-11 rounded-full object-cover border-2 border-amber-400 shrink-0" />
            <div>
              <h3 class="text-sm font-extrabold text-gray-900 dark:text-white">{{ $c->photographer->nama }}</h3>
              <p class="text-xs text-gray-500">{{ $c->bookingDetail->service->nama_layanan }}</p>
            </div>
          </div>

          <div class="p-4 bg-emerald-50 dark:bg-emerald-950/40 rounded-xl border border-emerald-200 space-y-2 text-xs">
            <span class="font-bold text-emerald-800 dark:text-emerald-300 block">Tautan Unduhan Galeri Foto:</span>
            <a href="{{ $c->bookingDetail->hasil_foto_url }}" target="_blank" class="font-mono text-emerald-700 dark:text-emerald-400 font-bold hover:underline block truncate">
              {{ $c->bookingDetail->hasil_foto_url }}
            </a>
            @if($c->bookingDetail->catatan_fotografer)
              <p class="text-gray-600 dark:text-gray-400 italic text-[11px] pt-1">"{{ $c->bookingDetail->catatan_fotografer }}"</p>
            @endif
          </div>

          <a href="{{ $c->bookingDetail->hasil_foto_url }}" target="_blank" class="w-full py-3 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs rounded-xl shadow-xs transition text-center block">
            Buka & Unduh Foto di Google Drive →
          </a>
        </div>
      @empty
        <div class="col-span-2 bg-white dark:bg-gray-800 p-12 rounded-2xl border border-gray-200 dark:border-gray-700 text-center space-y-2">
          <h3 class="text-base font-extrabold text-gray-900 dark:text-white">Belum Ada Galeri Foto Yang Selesai</h3>
          <p class="text-xs text-gray-500">Galeri foto hasil sesi Anda akan tampil otomatis setelah studio fotografer menyelesaikan proses retouching.</p>
        </div>
      @endforelse
    </div>

  </div>
</section>
@endsection