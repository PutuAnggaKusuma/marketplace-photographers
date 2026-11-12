@extends('layouts.app')

@section('title', 'Daftar Fotografer Favorit Saya — LensMatch')

@section('content')
<section class="py-12 bg-white dark:bg-gray-900 min-h-screen">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-black text-gray-900 dark:text-white">
          Fotografer Favorit Saya
        </h1>
        <p class="text-xs sm:text-sm text-gray-500 mt-1">
          Koleksi mitra fotografer pilihan yang Anda simpan untuk perbandingan portofolio dan pemesanan mendatang.
        </p>
      </div>
    </div>

    <!-- Photographers List (Horizontal Row Cards - Opsi A) -->
    <div class="space-y-4 sm:space-y-5">
      @forelse($favorites as $photographer)
        @php
          $lowestPrice = $photographer->services->where('tarif_harga', '>', 0)->min('tarif_harga');
          $coverPortfolio = $photographer->portfolios->first()?->medias?->first()?->media;
          $avatarUrl = $photographer->foto ? (str_starts_with($photographer->foto, 'http') ? $photographer->foto : asset('storage/' . $photographer->foto)) : null;
          $rating = $photographer->rating_average;
          $reviewCount = $photographer->testimonials->count();
        @endphp

        <div x-data="{ removing: false, removed: false, isFavorited: true }" 
             x-show="!removed" 
             x-transition:leave="transition ease-in duration-300 transform"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-200/80 dark:border-gray-700/80 p-5 sm:p-6 shadow-xs hover:shadow-md transition-all duration-300 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 group">
          
          <!-- Sisi Kiri: Cover Foto Portofolio Kompak (Landscape) -->
          <div class="w-full md:w-60 h-48 md:h-44 rounded-2xl overflow-hidden bg-gray-100 dark:bg-gray-700 shrink-0 relative">
            @if($coverPortfolio)
              <img src="{{ $coverPortfolio }}" alt="{{ $photographer->nama }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" />
            @else
              <div class="w-full h-full flex flex-col items-center justify-center bg-gray-100 dark:bg-gray-800 text-gray-400">
                <svg class="w-8 h-8 mb-1" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
                <span class="text-[10px] font-semibold">Belum ada portofolio</span>
              </div>
            @endif
            
            <!-- Bookmark Un-Save Icon Button (Top Left) -->
            <button type="button" 
                    :disabled="removing"
                    @click="
                      if (removing) return;
                      removing = true;
                      fetch('{{ route('client.favorites.toggle', $photographer->id) }}', {
                        method: 'POST',
                        headers: {
                          'X-CSRF-TOKEN': '{{ csrf_token() }}',
                          'Accept': 'application/json',
                          'Content-Type': 'application/json',
                          'X-Requested-With': 'XMLHttpRequest'
                        }
                      })
                      .then(async res => {
                        const data = await res.json().catch(() => ({}));
                        if (!res.ok) throw new Error(data.message || 'Gagal memproses favorit.');
                        return data;
                      })
                      .then(data => {
                        if (data.success) {
                          isFavorited = data.is_favorited;
                          window.dispatchEvent(new CustomEvent('show-toast', { 
                            detail: { 
                              message: data.message, 
                              type: 'success' 
                            } 
                          }));
                          removed = true;
                          setTimeout(() => {
                            if (data.total_favorites === 0) {
                              window.location.reload();
                            }
                          }, 350);
                        } else {
                          removing = false;
                        }
                      })
                      .catch(err => {
                        removing = false;
                        window.dispatchEvent(new CustomEvent('show-toast', { 
                          detail: { 
                            message: err.message || 'Terjadi kesalahan sistem.', 
                            type: 'error' 
                          } 
                        }));
                      });
                    "
                    title="Hapus dari Favorit" 
                    class="absolute top-3 left-3 z-10 w-8 h-8 rounded-full bg-white/95 dark:bg-gray-900/95 flex items-center justify-center shadow-md transition-all duration-200 hover:scale-110 active:scale-95 cursor-pointer text-amber-500 disabled:opacity-50">
              <svg class="w-4 h-4 fill-current text-amber-500 transition-colors" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z" />
              </svg>
            </button>
          </div>

          <!-- Sisi Tengah: Identitas, Lokasi, Kategori & Bio (Anti-AI Slop, Zero Dividers) -->
          <div class="flex-1 min-w-0 space-y-2">
            <!-- Lokasi Studio -->
            <div class="flex items-center gap-1.5 text-[11px] text-gray-400 dark:text-gray-400 font-light uppercase truncate">
              <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
              </svg>
              <span>{{ $photographer->city->name ?? ($photographer->alamat ?? '-') }}</span>
            </div>

            <!-- Avatar & Nama Studio -->
            <div class="flex items-center gap-3">
              @if($avatarUrl)
                <img src="{{ $avatarUrl }}" alt="{{ $photographer->nama }}" class="w-10 h-10 rounded-xl object-cover border-2 border-amber-400 shadow-xs shrink-0" />
              @else
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-amber-300 text-white font-black text-xs flex items-center justify-center border-2 border-amber-400 shadow-xs shrink-0">
                  {{ strtoupper(substr($photographer->nama ?? 'F', 0, 2)) }}
                </div>
              @endif
              <div class="min-w-0 flex-1">
                <a href="{{ url('/fotografer/' . $photographer->id) }}" class="font-extrabold text-base sm:text-lg text-gray-900 dark:text-white hover:text-amber-500 transition leading-snug truncate block">
                  {{ $photographer->nama }}
                </a>
              </div>
            </div>

            <!-- Kategori Spesialisasi (Tipografi Murni, Comma Separated, Tanpa Badge Pill Sesuai Rule #67) -->
            @if($photographer->categories && $photographer->categories->isNotEmpty())
              <p class="text-xs font-light text-amber-700 dark:text-amber-300 leading-snug truncate">
                {{ $photographer->categories->pluck('nama_kategori')->join(', ') }}
              </p>
            @endif

            <!-- Format Metrik Inline Teks (Rating & Sesi Selesai) -->
            @php
              $metrics = $photographer->editorial_metrics;
            @endphp
            @if(!empty($metrics))
              <div class="flex items-center flex-wrap gap-x-2 gap-y-1 text-xs text-gray-500 dark:text-gray-400 font-medium">
                @foreach($metrics as $idx => $m)
                  @if($idx > 0)
                    <span class="text-gray-300 dark:text-gray-600 select-none">•</span>
                  @endif
                  @if($m['type'] === 'rating')
                    <span class="font-bold text-gray-900 dark:text-white flex items-center gap-1">
                      <span class="{{ $rating ? 'text-amber-500' : 'text-gray-300 dark:text-gray-600' }}">★</span>
                      <span>{{ $m['text'] }}</span>
                    </span>
                  @elseif($m['type'] === 'fast_response')
                    <span class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ $m['text'] }}</span>
                  @else
                    <span>{{ $m['text'] }}</span>
                  @endif
                @endforeach
              </div>
            @else
              <div class="flex items-center gap-1 text-xs font-bold text-gray-900 dark:text-white">
                <span class="{{ $rating ? 'text-amber-500' : 'text-gray-300 dark:text-gray-600' }}">★</span>
                <span>{{ number_format((float) ($rating ?? 0), 1) }} ({{ $reviewCount }} ulasan)</span>
              </div>
            @endif

            <!-- Cuplikan Bio Singkat -->
            <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed line-clamp-2 pt-0.5">
              {{ !empty(trim($photographer->deskripsi_bio)) ? \Illuminate\Support\Str::limit($photographer->deskripsi_bio, 180) : 'Fotografer profesional terverifikasi siap mengabadikan momen terbaik Anda.' }}
            </p>
          </div>

          <!-- Sisi Kanan: Rincian Tarif & 2 Tombol Aksi Nyata (Zero Dividers) -->
          <div class="w-full md:w-56 shrink-0 flex flex-col justify-between space-y-3.5 self-stretch">
            <div>
              <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Mulai Dari</span>
              <p class="text-gray-900 dark:text-white font-black text-base sm:text-lg leading-tight">
                Rp {{ number_format($lowestPrice, 0, ',', '.') }}
                <span class="text-xs font-normal text-gray-400">/ sesi</span>
              </p>
            </div>

            <!-- 2 Tombol Aksi Nyata (To the Point, Sesuai Rule #70 & #91) -->
            <div class="space-y-2">
              <a href="{{ url('/booking/create?photographer_id=' . $photographer->id) }}" 
                 class="w-full py-2.5 px-4 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs transition shadow-xs flex items-center justify-center gap-1.5 text-center">
                <span>Reservasi</span>
              </a>

              <a href="{{ route('chat.start', $photographer->id) }}" 
                 class="w-full py-2.5 px-4 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-650 text-gray-800 dark:text-gray-100 font-bold text-xs transition flex items-center justify-center gap-2 text-center">
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <span>Chat</span>
              </a>

              <a href="{{ url('/fotografer/' . $photographer->id) }}" 
                 class="pt-1 text-center text-[11px] font-bold text-gray-500 dark:text-gray-400 hover:text-amber-500 dark:hover:text-amber-400 transition flex items-center justify-center gap-1">
                <span>Lihat Profil Lengkap</span>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
              </a>
            </div>
          </div>

        </div>
      @empty
        <!-- Empty State Container -->
        <div class="bg-white dark:bg-gray-800 p-12 sm:p-16 rounded-3xl border border-gray-200 dark:border-gray-700 text-center space-y-4 shadow-xs">
          <div class="w-16 h-16 mx-auto rounded-full bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center mb-2">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z" /></svg>
          </div>
          <h3 class="text-xl font-black text-gray-900 dark:text-white">Belum Ada Fotografer Favorit</h3>
          <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 max-w-md mx-auto leading-relaxed">
            Anda belum menyimpan profil fotografer mana pun. Klik ikon bookmark pada fotografer yang Anda minati di katalog untuk menyimpannya di sini.
          </p>
          <div class="pt-2">
            <a href="{{ route('public.photographers.katalog') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs rounded-xl transition">
              <span>Jelajahi Katalog Fotografer</span>
            </a>
          </div>
        </div>
      @endforelse
    </div>

    <!-- Pagination -->
    @if($favorites->total() > 0)
      <div class="pt-6">
        {{ $favorites->links('partials.public.pagination') }}
      </div>
    @endif

  </div>
</section>
@endsection

