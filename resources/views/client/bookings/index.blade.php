@extends('layouts.app')

@section('title', 'Reservasi Sesi Foto Saya — LensMatch')

@section('content')
<section class="py-12 bg-gray-50/50 dark:bg-gray-900/50 min-h-screen">
  <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <div>
        <span class="px-3.5 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300">
          Riwayat Transaksi & Sesi Foto
        </span>
        <h1 class="text-3xl font-black text-gray-900 dark:text-white mt-2">Reservasi Saya</h1>
        <p class="text-xs sm:text-sm text-gray-500">Pantau status konfirmasi studio fotografer, rincian jadwal, dan akses galeri hasil foto Anda.</p>
      </div>

      <a href="{{ url('/fotografer') }}" class="px-5 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs rounded-xl shadow-xs transition shrink-0">
        + Buat Booking Baru
      </a>
    </div>

    <!-- Booking List Cards -->
    <div class="space-y-6">
      @forelse($contracts as $c)
        <div class="bg-white dark:bg-gray-800 p-6 sm:p-7 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm space-y-5">
          
          <!-- Card Header Info -->
          <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-b border-gray-100 dark:border-gray-700 pb-4">
            <div class="flex items-center gap-3.5">
              <img onerror="this.onerror=null;this.src=window.SVG_IMAGE_FALLBACK||'data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='800' height='600' viewBox='0 0 800 600' fill='none'%3E%3Crect width='800' height='600' fill='%23F3F4F6'/%3E%3Cpath d='M360 260C360 248.954 368.954 240 380 240H420C431.046 240 440 248.954 440 260V265H450C466.569 265 480 278.431 480 295V345C480 361.569 466.569 375 450 375H350C333.431 375 320 361.569 320 345V295C320 278.431 333.431 265 350 265H360V260Z' stroke='%239CA3AF' stroke-width='12' stroke-linecap='round' stroke-linejoin='round'/%3E%3Ccircle cx='400' cy='320' r='30' stroke='%239CA3AF' stroke-width='12'/%3E%3Cline x1='310' y1='240' x2='490' y2='390' stroke='%23EF4444' stroke-width='10' stroke-linecap='round'/%3E%3Ctext x='400' y='450' font-family='sans-serif' font-size='22' font-weight='700' fill='%236B7280' text-anchor='middle'%3EGambar Tidak Dapat Dimuat%3C/text%3E%3C/svg%3E';" src="{{ $c->photographer->foto }}" alt="{{ $c->photographer->nama }}" class="w-12 h-12 rounded-full object-cover border-2 border-amber-400 shrink-0" />
              <div>
                <h3 class="text-base font-extrabold text-gray-900 dark:text-white">{{ $c->photographer->nama }}</h3>
                <p class="text-xs text-gray-500">
                  <svg class="w-3.5 h-3.5 text-amber-500 inline-block shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg> 
                  {{ $c->photographer->city->name ?? $c->photographer->alamat }}
                </p>
              </div>
            </div>

            <!-- Status Indicator -->
            <div>
              @if($c->bookingDetail->status_booking === 'pending')
                <span class="px-3.5 py-1.5 rounded-full text-xs font-black bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300">
                  ⏳ Menunggu Konfirmasi Studio
                </span>
              @elseif($c->bookingDetail->status_booking === 'confirmed')
                <span class="px-3.5 py-1.5 rounded-full text-xs font-black bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-300">
                  ✓ Jadwal Sesi Dikonfirmasi
                </span>
              @elseif($c->bookingDetail->status_booking === 'completed')
                <span class="px-3.5 py-1.5 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300">
                  ★ Sesi Foto Selesai
                </span>
              @else
                <span class="px-3.5 py-1.5 rounded-full text-xs font-black bg-red-100 text-red-800 dark:bg-red-900/60 dark:text-red-300">
                  Dibatalkan
                </span>
              @endif
            </div>
          </div>

          <!-- Session Details Grid -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
            <div class="space-y-1">
              <span class="font-bold text-gray-400 uppercase text-[10px]">Paket Layanan:</span>
              <p class="font-bold text-sm text-gray-900 dark:text-white">{{ $c->bookingDetail->service->nama_layanan ?? 'Paket Fotografi' }}</p>
              <p class="text-amber-600 dark:text-amber-400 font-black text-sm">Rp {{ number_format($c->jumlah, 0, ',', '.') }}</p>
            </div>

            <div class="space-y-1">
              <span class="font-bold text-gray-400 uppercase text-[10px]">Jadwal Pelaksanaan:</span>
              <p class="font-semibold text-gray-800 dark:text-gray-200">
                📅 {{ \Carbon\Carbon::parse($c->bookingDetail->booking_date)->format('d F Y') }} • {{ $c->bookingDetail->jam_mulai }} WIB
              </p>
              <p class="text-gray-500">Lokasi: {{ $c->bookingDetail->lokasi }}</p>
            </div>

            <div class="space-y-1">
              <span class="font-bold text-gray-400 uppercase text-[10px]">Nomor Tagihan & Status Bayar:</span>
              <p class="font-mono font-bold text-amber-600 dark:text-amber-400">{{ $c->payments->first()->external_id ?? 'INV-XXXX' }}</p>
              <span class="inline-block px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                Status Tagihan: {{ strtoupper($c->payments->first()->payment_status ?? 'pending') }}
              </span>
            </div>
          </div>

          <!-- Photo Delivery Download Banner (If Available) -->
          @if($c->bookingDetail->hasil_foto_url)
            <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 text-xs space-y-2">
              <div class="flex items-center justify-between">
                <span class="font-extrabold text-emerald-800 dark:text-emerald-300 flex items-center gap-2">
                  <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                  <span>Galeri Foto Hasil Sesi Sudah Siap!</span>
                </span>
                <a href="{{ $c->bookingDetail->hasil_foto_url }}" target="_blank" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold rounded-xl shadow-xs transition">
                  Unduh Foto Google Drive →
                </a>
              </div>
              @if($c->bookingDetail->catatan_fotografer)
                <p class="text-emerald-700 dark:text-emerald-400 italic text-[11px]">
                  Pesan Studio: "{{ $c->bookingDetail->catatan_fotografer }}"
                </p>
              @endif
            </div>
          @endif

          <!-- Action Footer Links -->
          <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-gray-100 dark:border-gray-700">
            <div>
              @if($c->bookingDetail && $c->bookingDetail->status_booking === 'completed')
                @if($c->testimonial)
                  <span class="px-3.5 py-1.5 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 font-extrabold text-xs border border-amber-200/80 inline-flex items-center gap-1">
                    <span>⭐ Ulasan Diberikan ({{ $c->testimonial->rating }}.0)</span>
                  </span>
                @else
                  <button type="button" 
                          @click="$dispatch('open-review-modal', {
                              contractId: {{ $c->id }},
                              photoName: '{{ addslashes($c->photographer->nama ?? '') }}',
                              serviceName: '{{ addslashes($c->bookingDetail->service->nama_layanan ?? '') }}'
                          })"
                          class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-extrabold text-xs rounded-xl shadow-sm transition flex items-center gap-1.5">
                    <span>⭐ Beri Ulasan Studio</span>
                  </button>
                @endif
              @endif
            </div>

            <div class="flex items-center gap-3">
              <a href="{{ url('/pembayaran/' . $c->id) }}" class="text-xs font-bold text-amber-600 dark:text-amber-400 hover:underline">
                Lihat Invoice Pembayaran
              </a>
              <a href="{{ url('/chat?thread=' . $c->id) }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 text-gray-800 dark:text-gray-200 font-bold text-xs rounded-xl transition">
                Chat Studio
              </a>
            </div>
          </div>

        </div>
      @empty
        <div class="bg-white dark:bg-gray-800 p-12 rounded-2xl border border-gray-200 dark:border-gray-700 text-center space-y-3">
          <h3 class="text-base font-extrabold text-gray-900 dark:text-white">Belum Ada Reservasi Sesi Foto</h3>
          <p class="text-xs text-gray-500">Temukan fotografer impian Anda dan abadikan momen berharga bersama LensMatch.</p>
          <a href="{{ url('/fotografer') }}" class="inline-block px-6 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs rounded-xl shadow-xs transition">
            Cari Fotografer Sekarang
          </a>
        </div>
      @endforelse

      {{ $contracts->links() }}
    </div>

  </div>
</section>
@include('partials.client.review-modal')
@endsection