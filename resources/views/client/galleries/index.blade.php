@extends('layouts.app')

@section('title', 'Galeri Hasil Foto Saya — LensMatch')

@section('content')

<section class="py-12 bg-white dark:bg-gray-900 min-h-screen" x-data="{ openRevisionModal: null }">

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

    <!-- Page Header -->

    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">

      <div>

        <h1 class="text-3xl font-black text-gray-900 dark:text-white">

          Galeri Hasil Foto Saya

        </h1>

        <p class="text-xs sm:text-sm text-gray-500 mt-1">

          Akses seluruh tautan album hasil retouching foto resolusi tinggi dari fotografer mitra LensMatch.

        </p>

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

    <!-- Galleries Card Grid (2 Kolom Horizontal, Diperpanjang ke Bawah / Vertikal) -->

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">

      @forelse($contracts as $c)

        @php

          $detail = $c->bookingDetail;

          $photographer = $c->photographer;

          $testimonial = $c->testimonial;

        @endphp

        <div class="bg-white dark:bg-gray-800 p-6 sm:p-7 rounded-3xl border border-gray-200 dark:border-gray-700/80 shadow-xs transition hover:border-amber-400/60 flex flex-col h-auto">

          

          <!-- Upper Section: Info & Meta -->

          <div class="space-y-6 sm:space-y-7">

            

            <!-- Header: Photographer Info + Review CTA -->

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

                  <h3 class="text-sm sm:text-base font-black text-gray-900 dark:text-white truncate">

                    {{ $photographer->nama ?? 'Mitra Fotografer' }}

                  </h3>

                  <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5">

                    {{ $detail->service->nama_layanan ?? 'Sesi Fotografi' }}

                  </p>

                </div>

              </div>

              @if(!$testimonial)

                <button type="button" 

                        @click="$dispatch('open-review-modal', {

                            contractId: {{ $c->id }},

                            photoName: '{{ addslashes($photographer->nama ?? 'Fotografer') }}',

                            serviceName: '{{ addslashes($detail->service->nama_layanan ?? 'Sesi Foto') }}',

                            isEdit: false

                        })"

                        class="text-xs font-extrabold text-amber-400 dark:text-amber-400 underline underline-offset-4 decoration-amber-400 hover:text-amber-300 hover:decoration-amber-300 transition cursor-pointer shrink-0">

                  Beri Ulasan

                </button>

              @else

                <button type="button" 

                        @click="$dispatch('open-review-modal', {

                            contractId: {{ $c->id }},

                            testimonialId: {{ $testimonial->id }},

                            photoName: '{{ addslashes($photographer->nama ?? 'Fotografer') }}',

                            serviceName: '{{ addslashes($detail->service->nama_layanan ?? 'Sesi Foto') }}',

                            rating: {{ (int) $testimonial->rating }},

                            reviewText: '{{ addslashes($testimonial->deskripsi_review) }}',

                            isEdit: true

                        })"

                        class="text-xs font-bold text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 underline underline-offset-4 decoration-gray-400/60 hover:decoration-gray-600 transition cursor-pointer shrink-0">

                  Sudah Direview

                </button>

              @endif

            </div>

            <!-- Session Date, Location & Status Meta (Vertically Spaced with Aligned Colons) -->

            <table class="text-xs text-gray-600 dark:text-gray-300 border-collapse">

              <tbody>

                <tr class="align-baseline">

                  <td class="text-gray-400 font-medium whitespace-nowrap pr-2.5 py-1.5">Tanggal Sesi</td>

                  <td class="text-gray-400 font-medium px-1 py-1.5">:</td>

                  <td class="font-bold text-gray-900 dark:text-white pl-2 py-1.5">{{ $detail && $detail->booking_date ? \Carbon\Carbon::parse($detail->booking_date)->translatedFormat('d F Y') : '-' }}</td>

                </tr>

                @if($detail && $detail->lokasi)

                  <tr class="align-baseline">

                    <td class="text-gray-400 font-medium whitespace-nowrap pr-2.5 py-1.5">Lokasi</td>

                    <td class="text-gray-400 font-medium px-1 py-1.5">:</td>

                    <td class="font-bold text-gray-900 dark:text-white pl-2 py-1.5 truncate">{{ $detail->lokasi }}</td>

                  </tr>

                @endif

                <tr class="align-baseline">

                  <td class="text-gray-400 font-medium whitespace-nowrap pr-2.5 py-1.5">Status</td>

                  <td class="text-gray-400 font-medium px-1 py-1.5">:</td>

                  <td class="font-bold pl-2 py-1.5 {{ $c->is_validated_client ? 'text-emerald-600 dark:text-emerald-400' : 'text-blue-600 dark:text-blue-400' }}">

                    {{ $c->is_validated_client ? 'Selesai' : 'Menunggu Konfirmasi Klien' }}

                  </td>

                </tr>

              </tbody>

            </table>

            <!-- Tautan Album Google Drive -->

            <div class="space-y-1.5 text-xs">

              <span class="text-[10px] font-medium uppercase tracking-wider text-gray-400 dark:text-gray-500 block">

                Tautan Album Google Drive:

              </span>

              <a href="{{ $detail->hasil_foto_url }}" target="_blank" class="font-mono text-xs text-gray-900 dark:text-white font-bold underline underline-offset-4 decoration-gray-900/40 dark:decoration-white/40 hover:text-gray-600 dark:hover:text-gray-300 inline-flex items-center gap-1.5 break-all leading-relaxed transition group">

                <span class="break-all">{{ $detail->hasil_foto_url }}</span>

                <svg class="w-3.5 h-3.5 shrink-0 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5 text-gray-900 dark:text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">

                  <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />

                </svg>

              </a>

            </div>

            <!-- Pesan Pengantar Fotografer (Clean Text / Tanpa Kotak Badge) -->
            <div class="space-y-1.5 pt-1">
              <div class="flex items-center justify-between gap-2">
                <span class="text-[10px] sm:text-[11px] font-extrabold uppercase tracking-wider text-amber-600 dark:text-amber-400 block">
                  Pesan Pengantar Fotografer
                </span>
                <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 shrink-0">
                  {{ $photographer->nama ?? 'Fotografer' }}
                </span>
              </div>

              @if(!empty($detail->catatan_fotografer))
                <p class="text-xs sm:text-[13px] text-gray-700 dark:text-gray-300 leading-relaxed italic font-normal">
                  “{{ $detail->catatan_fotografer }}”
                </p>
              @else
                <p class="text-xs text-gray-400 dark:text-gray-500 leading-relaxed italic font-normal">
                  Fotografer tidak menyematkan pesan pengantar khusus. Silakan langsung mengunduh hasil foto Anda melalui tautan album di atas.
                </p>
              @endif
            </div>

            <!-- Status & Aksi Penyelesaian Pesanan -->
            @if(!$c->is_validated_client)
              <div class="pt-2">
                <div class="space-y-3 p-4 rounded-2xl bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700">
                  <div class="space-y-0.5">
                    <span class="text-xs font-black text-gray-900 dark:text-white block">Konfirmasi Hasil Pemotretan</span>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400 leading-relaxed">
                      Silakan periksa album foto di atas. Jika Anda puas, klik tombol konfirmasi untuk menyelesaikan pesanan dan merilis pencairan dana mitra fotografer.
                    </p>
                  </div>

                  <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 pt-1">
                    <!-- Tombol 1: Konfirmasi Selesai -->
                    <button type="button" 
                            @click="$dispatch('open-confirm-modal', {
                                title: 'Konfirmasi Terima Hasil Foto?',
                                message: 'Apakah Anda sudah memeriksa album foto dan puas dengan hasilnya? Setelah dikonfirmasi, pesanan akan diselesaikan dan dana pembayaran akan diteruskan ke mitra fotografer.',
                                confirmText: 'Ya, Selesaikan Pesanan',
                                type: 'success',
                                actionUrl: '{{ route('client.bookings.confirm-gallery', $c->id) }}',
                                method: 'POST'
                            })"
                            class="flex-1 py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-xs transition text-center cursor-pointer">
                      Konfirmasi Terima & Selesaikan
                    </button>

                    <!-- Tombol 2: Minta Revisi -->
                    <button type="button" 
                            @click="openRevisionModal = {{ $c->id }}"
                            class="py-2.5 px-4 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 font-extrabold text-xs transition text-center cursor-pointer">
                      Minta Revisi
                    </button>
                  </div>
                </div>
              </div>
            @endif</div>

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

  <!-- Modal Permintaan Revisi Klien -->

  <div x-show="openRevisionModal" 

       x-cloak 

       @keydown.escape.window="openRevisionModal = null" 

       class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"

       x-transition>

    <div @click.away="openRevisionModal = null" 

         class="bg-white dark:bg-gray-800 max-w-md w-full p-6 sm:p-7 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-2xl space-y-5">

      <div class="space-y-1 text-center">

        <h3 class="text-base font-black text-gray-900 dark:text-white">Ajukan Permintaan Revisi Foto</h3>

        <p class="text-xs text-gray-500">Tuliskan bagian foto yang perlu perbaikan, penyesuaian retouching, atau warna.</p>

      </div>

      <form :action="'{{ url('/client/bookings') }}/' + openRevisionModal + '/request-revision'" method="POST" class="space-y-4">

        @csrf

        <div>

          <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Rincian Catatan Revisi <span class="text-rose-500">*</span></label>

          <textarea name="catatan_revisi" required rows="4" placeholder="Contoh: Mohon penyesuaian tone warna pada foto keluarga di luar ruangan agak sedikit terang..." class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500"></textarea>

        </div>

        <div class="flex items-center justify-center gap-3 pt-2">

          <button type="button" @click="openRevisionModal = null" class="px-5 py-2.5 border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-xs font-bold text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-100 transition cursor-pointer">

            Batal

          </button>

          <button type="submit" class="px-6 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs rounded-xl shadow-xs transition cursor-pointer">

            Kirim Permintaan

          </button>

        </div>

      </form>

    </div>

  </div>

  @if(session('open_review_modal') && session('review_contract_id'))

    @php

      $revContract = $contracts->firstWhere('id', session('review_contract_id'));

    @endphp

    @if($revContract)

      <div x-init="setTimeout(() => {

        $dispatch('open-review-modal', {

          contractId: {{ $revContract->id }},

          photoName: '{{ addslashes($revContract->photographer->nama ?? 'Fotografer') }}',

          serviceName: '{{ addslashes($revContract->bookingDetail->service->nama_layanan ?? 'Sesi Foto') }}',

          isEdit: false

        });

      }, 500)"></div>

    @endif

  @endif

  </div>

</section>

@include('partials.client.review-modal')

@endsection