@extends('layouts.photographer')

@section('title', 'Kelola Order Booking Masuk — Dashboard Studio')

@section('content')
<div class="space-y-6" x-data="{ uploadModal: false, selectedContractId: null, selectedClientName: '' }">

  <!-- Header Banner -->
  <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-xs">
    <div>
      <span class="text-[11px] font-black uppercase tracking-widest text-amber-600 dark:text-amber-400">Dashboard Studio Fotografer</span>
      <h1 class="text-2xl font-black text-gray-900 dark:text-white mt-1">Kelola Order Booking & Kontrak Masuk</h1>
      <p class="text-xs text-gray-500">Konfirmasi jadwal booking dari klien, tinjau catatan khusus, dan unggah tautan hasil foto setelah sesi selesai.</p>
    </div>

    <!-- Quick Metrics -->
    <div class="flex items-center gap-3 shrink-0">
      <div class="px-4 py-2 bg-amber-50 dark:bg-amber-950/50 border border-amber-200 dark:border-amber-800 rounded-xl text-center">
        <span class="text-[10px] font-bold uppercase text-amber-700 dark:text-amber-300 block">Menunggu Konfirmasi</span>
        <span class="text-lg font-black text-amber-600 dark:text-amber-400">{{ $stats['pending'] ?? 0 }} Order</span>
      </div>
      <div class="px-4 py-2 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 rounded-xl text-center">
        <span class="text-[10px] font-bold uppercase text-emerald-700 dark:text-emerald-300 block">Sesi Dikonfirmasi</span>
        <span class="text-lg font-black text-emerald-600 dark:text-emerald-400">{{ $stats['confirmed'] ?? 0 }} Order</span>
      </div>
    </div>
  </div>

  <!-- Flash Alert Success -->
  @if(session('success'))
    <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 text-xs text-emerald-800 dark:text-emerald-300 font-bold flex items-center gap-2">
      <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
      <span>{{ session('success') }}</span>
    </div>
  @endif

  <!-- Filter Status Tabs -->
  <div class="flex items-center gap-2 border-b border-gray-200 dark:border-gray-700 pb-1 overflow-x-auto">
    <a href="{{ route('photographer.bookings', ['status' => 'all']) }}" class="px-4 py-2.5 rounded-xl text-xs font-extrabold transition {{ $filterStatus === 'all' ? 'bg-amber-400 text-gray-900 shadow-xs' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
      Semua Order ({{ $stats['all'] ?? 0 }})
    </a>
    <a href="{{ route('photographer.bookings', ['status' => 'pending']) }}" class="px-4 py-2.5 rounded-xl text-xs font-extrabold transition {{ $filterStatus === 'pending' ? 'bg-amber-400 text-gray-900 shadow-xs' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
      Menunggu Konfirmasi ({{ $stats['pending'] ?? 0 }})
    </a>
    <a href="{{ route('photographer.bookings', ['status' => 'confirmed']) }}" class="px-4 py-2.5 rounded-xl text-xs font-extrabold transition {{ $filterStatus === 'confirmed' ? 'bg-amber-400 text-gray-900 shadow-xs' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
      Jadwal Dikonfirmasi ({{ $stats['confirmed'] ?? 0 }})
    </a>
    <a href="{{ route('photographer.bookings', ['status' => 'completed']) }}" class="px-4 py-2.5 rounded-xl text-xs font-extrabold transition {{ $filterStatus === 'completed' ? 'bg-amber-400 text-gray-900 shadow-xs' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
      Selesai ({{ $stats['completed'] ?? 0 }})
    </a>
  </div>

  <!-- Bookings List Table / Cards -->
  <div class="space-y-4">
    @forelse($contracts as $c)
      <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-xs space-y-4">
        
        <!-- Order Header Bar -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-b border-gray-100 dark:border-gray-700/80 pb-4">
          <div class="flex items-center gap-3">
            <span class="w-10 h-10 rounded-full bg-amber-400 text-gray-900 flex items-center justify-center font-black text-sm shrink-0 shadow-xs">
              {{ strtoupper(substr($c->client->nama ?? 'K', 0, 1)) }}
            </span>
            <div>
              <h3 class="text-sm font-extrabold text-gray-900 dark:text-white">{{ $c->client->nama ?? 'Klien LensMatch' }}</h3>
              <p class="text-xs text-gray-500">Invoice: <strong class="text-amber-600 dark:text-amber-400">{{ $c->payments->first()->external_id ?? 'INV-XXXX' }}</strong> • Dibuat {{ $c->created_at->diffForHumans() }}</p>
            </div>
          </div>

          <!-- Status Badge -->
          <div>
            @if($c->bookingDetail->status_booking === 'pending')
              <span class="px-3 py-1 rounded-full text-xs font-black bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300">
                ⏳ Menunggu Konfirmasi Studio
              </span>
            @elseif($c->bookingDetail->status_booking === 'confirmed')
              <span class="px-3 py-1 rounded-full text-xs font-black bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-300">
                ✓ Jadwal Sesi Foto Dikonfirmasi
              </span>
            @elseif($c->bookingDetail->status_booking === 'completed')
              <span class="px-3 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300">
                ★ Selesai — Galeri Terkirim
              </span>
            @else
              <span class="px-3 py-1 rounded-full text-xs font-black bg-red-100 text-red-800 dark:bg-red-900/60 dark:text-red-300">
                Dibatalkan
              </span>
            @endif
          </div>
        </div>

        <!-- Order Content Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
          
          <div class="space-y-1">
            <span class="font-bold text-gray-400 uppercase text-[10px]">Paket Layanan:</span>
            <p class="font-extrabold text-sm text-gray-900 dark:text-white">{{ $c->bookingDetail->service->nama_layanan ?? 'Paket Foto Studio' }}</p>
            <p class="text-amber-600 dark:text-amber-400 font-extrabold text-sm">Rp {{ number_format($c->jumlah, 0, ',', '.') }}</p>
          </div>

          <div class="space-y-1">
            <span class="font-bold text-gray-400 uppercase text-[10px]">Jadwal & Lokasi Sesi Foto:</span>
            <p class="font-semibold text-gray-800 dark:text-gray-200">
              📅 {{ \Carbon\Carbon::parse($c->bookingDetail->booking_date)->format('d F Y') }} • {{ $c->bookingDetail->jam_mulai }} WIB
            </p>
            <p class="text-gray-500">
              <svg class="w-3.5 h-3.5 text-amber-500 inline-block shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg> 
              {{ $c->bookingDetail->lokasi }}
            </p>
          </div>

          <div class="space-y-1">
            <span class="font-bold text-gray-400 uppercase text-[10px]">Catatan / Request Klien:</span>
            <p class="text-gray-600 dark:text-gray-400 italic bg-gray-50 dark:bg-gray-900 p-2.5 rounded-xl border border-gray-100 dark:border-gray-700 line-clamp-2">
              "{{ $c->bookingDetail->catatan_khusus ?: 'Tidak ada request khusus.' }}"
            </p>
          </div>

        </div>

        <!-- Photo Gallery Link (If Available) -->
        @if($c->bookingDetail->hasil_foto_url)
          <div class="p-3 bg-emerald-50 dark:bg-emerald-950/40 rounded-xl border border-emerald-200 text-xs flex items-center justify-between">
            <span class="font-bold text-emerald-800 dark:text-emerald-300">Tautan Galeri Foto Terkirim:</span>
            <a href="{{ $c->bookingDetail->hasil_foto_url }}" target="_blank" class="text-xs font-black text-emerald-700 dark:text-emerald-400 hover:underline">
              Buka Google Drive / Gallery →
            </a>
          </div>
        @endif

        <!-- Action Buttons -->
        <div class="flex flex-wrap items-center justify-end gap-2 pt-2 border-t border-gray-100 dark:border-gray-700">
          
          @if($c->bookingDetail->status_booking === 'pending')
            <form action="{{ route('photographer.bookings.status', $c->id) }}" method="POST">
              @csrf
              <input type="hidden" name="status" value="confirmed">
              <button type="submit" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white font-extrabold text-xs rounded-xl shadow-xs transition">
                ✓ Setujui & Konfirmasi Pesanan
              </button>
            </form>

            <form action="{{ route('photographer.bookings.status', $c->id) }}" method="POST">
              @csrf
              <input type="hidden" name="status" value="cancelled">
              <button type="submit" class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 font-extrabold text-xs rounded-xl transition">
                Tolak Pesanan
              </button>
            </form>
          @elseif($c->bookingDetail->status_booking === 'confirmed')
            <button type="button" @click="uploadModal = true; selectedContractId = {{ $c->id }}; selectedClientName = '{{ addslashes($c->client->nama ?? 'Klien') }}'" class="px-5 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs rounded-xl shadow-xs transition flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
              <span>Unggah Galeri Hasil Foto Selesai</span>
            </button>
          @endif

          <a href="{{ url('/chat?client_id=' . $c->id_client) }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 text-gray-800 dark:text-gray-200 font-bold text-xs rounded-xl transition">
            Chat Klien
          </a>
        </div>

      </div>
    @empty
      <div class="bg-white dark:bg-gray-800 p-12 rounded-2xl border border-gray-200 dark:border-gray-700 text-center space-y-3">
        <div class="w-12 h-12 rounded-full bg-amber-100 dark:bg-amber-900/60 text-amber-600 dark:text-amber-400 mx-auto flex items-center justify-center">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
        </div>
        <h3 class="text-base font-extrabold text-gray-900 dark:text-white">Belum Ada Order Booking Masuk</h3>
        <p class="text-xs text-gray-500">Order booking sesi foto yang diajukan oleh Klien akan otomatis tampil di halaman ini.</p>
      </div>
    @endforelse

    {{ $contracts->links() }}
  </div>

  <!-- Modal Upload Galeri Foto -->
  <div x-show="uploadModal" @click.away="uploadModal = false" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 max-w-lg w-full p-6 sm:p-8 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-2xl space-y-6">
      
      <div class="space-y-1">
        <h3 class="text-lg font-black text-gray-900 dark:text-white">Unggah Link Galeri Hasil Foto</h3>
        <p class="text-xs text-gray-500">Kirimkan tautan Google Drive / Cloud Drive hasil retouching foto kepada <strong x-text="selectedClientName"></strong>.</p>
      </div>

      <form :action="'/photographer/bookings/' + selectedContractId + '/gallery'" method="POST" class="space-y-4">
        @csrf
        <div>
          <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Tautan URL Google Drive / Cloud Gallery <span class="text-red-500">*</span></label>
          <input type="url" name="hasil_foto_url" placeholder="https://drive.google.com/drive/folders/..." required class="w-full px-3.5 py-2.5 text-xs text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-amber-400 focus:outline-none" />
        </div>

        <div>
          <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Pesan / Catatan Tambahan Untuk Klien</label>
          <textarea name="catatan_fotografer" rows="3" placeholder="Tuliskan ucapan terima kasih atau petunjuk pengunduhan foto..." class="w-full px-3.5 py-2.5 text-xs text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-amber-400 focus:outline-none"></textarea>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
          <button type="button" @click="uploadModal = false" class="px-5 py-2.5 text-xs font-bold text-gray-500 hover:text-gray-800 dark:hover:text-gray-300">
            Batal
          </button>
          <button type="submit" class="px-6 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs rounded-xl shadow-md transition">
            Kirim Galeri ke Klien
          </button>
        </div>
      </form>

    </div>
  </div>

</div>
@endsection