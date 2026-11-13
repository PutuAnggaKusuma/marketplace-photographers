@extends('layouts.app')

@section('title', 'Invoice ' . ($payment->external_id ?? ('KTRK-' . str_pad($contract->id, 5, '0', STR_PAD_LEFT))) . ' — LensMatch')

@section('content')
<section class="py-12 sm:py-16 bg-gray-50/60 dark:bg-gray-900/60 min-h-screen">
  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6" x-data="{ showModal: false, lightboxOpen: false, lightboxUrl: '' }">

    <!-- Breadcrumb Navigation -->
    <nav class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
      <a href="{{ route('client.invoices') }}" class="hover:text-amber-500 transition">Faktur Pembayaran</a>
      <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
      <span class="text-gray-900 dark:text-white font-bold">Rincian Faktur {{ $payment->external_id ?? ('KTRK-' . str_pad($contract->id, 5, '0', STR_PAD_LEFT)) }}</span>
    </nav>

    <!-- Top Action Navigation -->
    <div class="flex items-center justify-between">
      <a href="{{ route('client.bookings') }}" class="text-xs font-bold text-gray-500 hover:text-gray-900 dark:hover:text-white transition flex items-center gap-1.5 cursor-pointer">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
        <span>Kembali ke Reservasi Saya</span>
      </a>

      @if($payment && $payment->payment_status === 'completed')
        <a href="{{ route('booking.invoice.print', $contract->id) }}" target="_blank" class="px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold text-xs transition shadow-2xs cursor-pointer">
          Cetak PDF
        </a>
      @endif
    </div>

    <!-- Official Invoice Document Sheet (Rule #89) -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-200 dark:border-gray-700/80 shadow-sm p-6 sm:p-10 lg:p-12 space-y-8">
      
      <!-- 1. Document Header -->
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 border-b border-gray-100 dark:border-gray-700 pb-8">
        <div class="space-y-1">
          <span class="text-2xl font-black tracking-tight text-gray-900 dark:text-white">
            Lens<span class="text-amber-500">Match</span>
          </span>
          <p class="text-xs text-gray-500 dark:text-gray-400">Platform Marketplace Fotografi Indonesia</p>
          <p class="text-[11px] text-gray-400">support@lensmatch.id</p>
        </div>

        <div class="text-left sm:text-right space-y-1">
          <div class="flex sm:justify-end items-center gap-2">
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Invoice</h1>
            @if($payment && $payment->payment_status === 'completed')
              <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                Lunas
              </span>
            @elseif($payment && $payment->bukti_transfer && $payment->payment_status === 'pending')
              <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300">
                Menunggu Verifikasi
              </span>
            @else
              <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300">
                Menunggu Pembayaran
              </span>
            @endif
          </div>
          <p class="text-xs font-mono text-gray-500 dark:text-gray-400">{{ $payment->external_id ?? ('KTRK-' . str_pad($contract->id, 5, '0', STR_PAD_LEFT)) }}</p>
          <p class="text-xs text-gray-500 dark:text-gray-400">Tanggal: {{ $contract->created_at->translatedFormat('d F Y') }}</p>
        </div>
      </div>

      <!-- 2. Flat Editorial Billing Info (Klien & Fotografer) -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 text-xs sm:text-sm">
        <div class="space-y-1">
          <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 block mb-1">Ditagihkan Kepada:</span>
          <h4 class="font-bold text-gray-900 dark:text-white text-sm sm:text-base">{{ $contract->client->nama ?? 'Klien' }}</h4>
          <p class="text-gray-500 dark:text-gray-400">{{ $contract->client->user->email ?? auth()->user()->email ?? '-' }}</p>
          @if($contract->bookingDetail?->lokasi)
            <p class="text-gray-500 dark:text-gray-400">Lokasi Acara: {{ $contract->bookingDetail->lokasi }}</p>
          @endif
        </div>

        <div class="space-y-1 sm:text-right">
          <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 block mb-1">Penyedia Jasa:</span>
          <h4 class="font-bold text-gray-900 dark:text-white text-sm sm:text-base">{{ $contract->photographer->nama ?? 'Mitra Fotografer' }}</h4>
          <p class="text-gray-500 dark:text-gray-400">{{ $contract->photographer->city->name ?? $contract->photographer->alamat ?? '-' }}</p>
          <p class="text-gray-500 dark:text-gray-400">{{ $contract->photographer->nomor_telepon ?? '-' }}</p>
        </div>
      </div>

      <!-- 3. Line-Item Table (Tabel Rincian Layanan Standar Industri) -->
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs sm:text-sm border-collapse">
          <thead>
            <tr class="border-b border-gray-200 dark:border-gray-700 text-[10px] sm:text-xs font-bold uppercase tracking-wider text-gray-400">
              <th class="py-3 pr-4">Deskripsi Layanan</th>
              <th class="py-3 px-4 text-center">Jadwal Sesi</th>
              <th class="py-3 px-4 text-center">Tipe Pembayaran</th>
              <th class="py-3 pl-4 text-right">Jumlah</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60 text-xs">
            <tr>
              <td class="py-4 pr-4">
                <span class="font-bold text-gray-900 dark:text-white block">
                  {{ $contract->bookingDetail?->service?->nama_layanan ?? 'Paket Layanan Fotografi' }}
                </span>
                <span class="text-xs text-gray-500 dark:text-gray-400 block mt-0.5">
                  Sesi foto bersama {{ $contract->photographer->nama ?? 'Mitra Fotografer' }}
                </span>
              </td>
              <td class="py-4 px-4 text-center text-xs text-gray-600 dark:text-gray-300">
                <span>{{ $contract->bookingDetail ? $contract->bookingDetail->date_range_label : '-' }}</span>
                @if($contract->bookingDetail?->jam_mulai)
                  @php
                    $invStart = substr($contract->bookingDetail->jam_mulai, 0, 5);
                    $invEnd = $contract->bookingDetail->jam_selesai ? substr($contract->bookingDetail->jam_selesai, 0, 5) : null;
                  @endphp
                  <span class="block text-gray-400 text-xs">{{ $invEnd ? "{$invStart} - {$invEnd} WIB" : "{$invStart} WIB" }} ({{ $contract->bookingDetail->effective_duration_hours }} Jam)</span>
                @endif
              </td>
              <td class="py-4 px-4 text-center text-xs text-gray-600 dark:text-gray-300 whitespace-nowrap">
                {{ $contract->payment_type === 'dp' ? 'Uang Muka (DP 30%)' : 'Pembayaran Penuh' }}
              </td>
              <td class="py-4 pl-4 text-right font-bold text-gray-900 dark:text-white whitespace-nowrap">
                Rp {{ number_format($contract->jumlah, 0, ',', '.') }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- 4. Rangkuman Kalkulasi Tagihan (Rata Kanan Bawah) -->
      <div class="flex justify-end pt-4 border-t border-gray-100 dark:border-gray-700">
        <div class="w-full sm:w-72 space-y-2 text-xs sm:text-sm">
          <div class="flex justify-between text-gray-500 dark:text-gray-400">
            <span>Subtotal</span>
            <span class="font-medium text-gray-900 dark:text-white">Rp {{ number_format($contract->jumlah, 0, ',', '.') }}</span>
          </div>
          <div class="flex justify-between text-gray-500 dark:text-gray-400">
            <span>Biaya Layanan Platform</span>
            <span class="font-medium text-gray-900 dark:text-white">Gratis</span>
          </div>
          <div class="pt-2 border-t border-gray-200 dark:border-gray-700 flex justify-between items-baseline">
            <span class="font-bold text-gray-900 dark:text-white text-sm">Total Tagihan</span>
            <span class="font-black text-gray-900 dark:text-white text-lg sm:text-xl">
              Rp {{ number_format($contract->jumlah, 0, ',', '.') }}
            </span>
          </div>
        </div>
      </div>

      <!-- 5. Status & Action Section (Tenang, Bersih, Bebas Banner Neon) -->
      @if($payment && $payment->payment_status === 'completed')
        <div class="p-5 rounded-2xl bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
          <div class="space-y-0.5">
            <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 block">Pembayaran Terverifikasi</span>
            <p class="text-xs text-gray-500 dark:text-gray-400">Pembayaran telah diterima dan jadwal sesi foto Anda telah terkonfirmasi.</p>
          </div>
          <a href="{{ route('client.bookings') }}" class="px-5 py-2.5 bg-gray-900 hover:bg-gray-800 text-white font-extrabold text-xs rounded-xl transition shadow-xs cursor-pointer">
            Lihat Jadwal Reservasi
          </a>
        </div>
      @elseif($payment && $payment->bukti_transfer && $payment->payment_status === 'pending')
        <div class="p-5 rounded-2xl bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
          <div class="space-y-0.5">
            <span class="text-xs font-bold text-blue-600 dark:text-blue-400 block">Bukti Transfer Sedang Diverifikasi</span>
            <p class="text-xs text-gray-500 dark:text-gray-400">Struk transfer Anda sedang diperiksa oleh Tim Admin. Jadwal sesi foto akan aktif otomatis setelah disetujui.</p>
          </div>
          <div class="flex items-center gap-3 shrink-0">
            <a href="{{ $payment->bukti_transfer }}" target="_blank" class="px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 text-gray-700 dark:text-gray-300 font-bold text-xs transition shadow-2xs">
              Lihat Struk
            </a>
            <button type="button" @click="showModal = true" class="px-4 py-2 bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs rounded-xl shadow-xs transition cursor-pointer">
              Unggah Ulang
            </button>
          </div>
        </div>
      @else
        <div class="p-6 rounded-2xl bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 space-y-4">
          <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
            <div>
              <span class="text-xs font-bold text-gray-900 dark:text-white block">Instruksi Pembayaran</span>
              <p class="text-xs text-gray-500 dark:text-gray-400">Silakan transfer nominal tagihan ke rekening bank berikut:</p>
            </div>
            @if($contract->expired_at)
              <span class="text-[11px] text-gray-400">Batas waktu: {{ \Carbon\Carbon::parse($contract->expired_at)->translatedFormat('d M Y, H:i') }} WIB</span>
            @endif
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-xs">
            <div>
              <span class="text-gray-400 block text-[11px]">Bank Tujuan</span>
              <strong class="font-bold text-gray-900 dark:text-white">{{ config('app.escrow_bank', 'Bank Central Asia (BCA)') }}</strong>
            </div>
            <div>
              <span class="text-gray-400 block text-[11px]">Nomor Rekening</span>
              <strong class="font-mono font-bold text-gray-900 dark:text-white text-sm">{{ config('app.escrow_account_number', '8840-1928-3746') }}</strong>
            </div>
            <div>
              <span class="text-gray-400 block text-[11px]">Atas Nama</span>
              <strong class="font-bold text-gray-900 dark:text-white">{{ config('app.escrow_account_holder', 'PT LensMatch Indonesia') }}</strong>
            </div>
          </div>

          <div class="flex justify-end pt-2">
            <button type="button" @click="showModal = true" class="px-6 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs rounded-xl shadow-xs transition cursor-pointer">
              Unggah Bukti Transfer
            </button>
          </div>
        </div>
      @endif

    </div>

    <!-- Modal Unggah Bukti Transfer (Rule #59, #89 & #97) -->
    <div x-show="showModal" 
         x-cloak 
         @keydown.escape.window="if (!lightboxOpen) showModal = false" 
         class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
      <div @click.away="if (!lightboxOpen) showModal = false" class="bg-white dark:bg-gray-800 max-w-md w-full p-6 sm:p-8 rounded-3xl border border-gray-200 dark:border-gray-700 shadow-2xl flex flex-col gap-6 text-center">
        
        <div class="flex flex-col gap-1">
          <h3 class="text-lg font-black text-gray-900 dark:text-white">Unggah Bukti Transfer</h3>
          <p class="text-xs text-gray-500">Silakan unggah foto struk transfer ke rekening {{ config('app.escrow_bank', 'Bank BCA') }} {{ config('app.escrow_account_number', '8840-1928-3746') }} a.n. {{ config('app.escrow_account_holder', 'PT LensMatch Indonesia') }}</p>
        </div>

        <div class="p-4 bg-gray-50 dark:bg-gray-900/80 border border-gray-100 dark:border-gray-700 rounded-2xl flex justify-between items-center text-xs">
          <span class="text-gray-500">Total Nominal:</span>
          <span class="font-black text-gray-900 dark:text-white text-sm">
            Rp {{ number_format(($payment ? $payment->payment_amount : $contract->jumlah), 0, ',', '.') }}
          </span>
        </div>

        <!-- Form Unggah Bukti Transfer -->
        <form action="{{ route('booking.pay.confirm', $contract->id) }}" 
              method="POST" 
              enctype="multipart/form-data" 
              novalidate
              class="flex flex-col gap-4 text-left"
              x-data="{
                fileSelected: false,
                fileName: '',
                fileSizeText: '',
                previewUrl: '',
                isDragging: false,
                touched: false,
                error: '',
                handleFiles(files) {
                  this.touched = true;
                  const incoming = Array.from(files || []);
                  if (incoming.length === 0) return false;
                  const res = window.validateUploadFiles(incoming, { maxMB: 10 });
                  if (res.hasErrors) {
                    this.error = res.errorMessage;
                    return false;
                  }
                  const file = res.validFiles[0];
                  if (!file) {
                    this.clearFile();
                    this.error = 'Foto struk / bukti transfer wajib dipilih.';
                    return false;
                  }
                  if (this.previewUrl && this.previewUrl.startsWith('blob:')) {
                    URL.revokeObjectURL(this.previewUrl);
                  }
                  if (typeof DataTransfer !== 'undefined' && this.$refs.fileInput) {
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    this.$refs.fileInput.files = dt.files;
                  }
                  this.fileSelected = true;
                  this.fileName = file.name;
                  this.fileSizeText = window.formatFileSizeMB ? window.formatFileSizeMB(file.size) : '';
                  this.previewUrl = URL.createObjectURL(file);
                  this.error = '';
                  return true;
                },
                handleDrop(e) {
                  this.isDragging = false;
                  if (e.dataTransfer && e.dataTransfer.files) {
                    this.handleFiles(e.dataTransfer.files);
                  }
                },
                clearFile() {
                  if (this.previewUrl && this.previewUrl.startsWith('blob:')) {
                    URL.revokeObjectURL(this.previewUrl);
                  }
                  this.fileSelected = false;
                  this.fileName = '';
                  this.fileSizeText = '';
                  this.previewUrl = '';
                  if (this.$refs.fileInput) this.$refs.fileInput.value = '';
                  this.touched = true;
                  this.error = 'Foto struk / bukti transfer wajib dipilih.';
                },
                submitForm(e) {
                  this.touched = true;
                  if (!this.fileSelected || this.error) {
                    e.preventDefault();
                    if (!this.error) this.error = 'Foto struk / bukti transfer wajib dipilih.';
                    this.$refs.proofDropzone?.focus();
                    this.$refs.proofDropzone?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return false;
                  }
                }
              }"
              @submit="submitForm($event)">
          @csrf
          <input type="hidden" name="payment_id" value="{{ $payment?->id }}">
          
          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Foto Struk / Bukti Transfer <span class="text-rose-500">*</span></label>
            <div class="flex flex-col gap-3">
              <div x-ref="proofDropzone"
                   tabindex="0"
                   role="button"
                   @click="$refs.fileInput.click()"
                   @keydown.enter.prevent="$refs.fileInput.click()"
                   @keydown.space.prevent="$refs.fileInput.click()"
                   @dragover.prevent="isDragging = true"
                   @dragleave.prevent="isDragging = false"
                   @drop.prevent="handleDrop($event)"
                   class="w-full min-h-[140px] px-5 py-7 rounded-2xl border-2 border-dashed flex flex-col items-center justify-center text-center gap-2 cursor-pointer transition outline-none"
                   :class="isDragging
                       ? 'border-amber-500 bg-amber-50/70 dark:bg-amber-950/25'
                       : (touched && error
                           ? 'border-red-400 bg-red-50/40 dark:bg-red-950/20 focus:ring-2 focus:ring-red-400'
                           : 'border-gray-200 dark:border-gray-700 bg-gray-50/80 dark:bg-gray-900/60 hover:border-amber-400 dark:hover:border-amber-500 hover:bg-amber-50/30 dark:hover:bg-amber-950/20 focus:ring-2 focus:ring-amber-500')">
                <input type="file"
                       name="bukti_transfer"
                       x-ref="fileInput"
                       accept="image/jpeg,image/png,image/webp"
                       @change="handleFiles($event.target.files)"
                       class="sr-only">
                <svg class="w-7 h-7 transition-colors" :class="isDragging ? 'text-amber-500' : 'text-gray-400 dark:text-gray-500'" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                </svg>
                <p class="text-xs font-bold text-gray-700 dark:text-gray-200">
                  Seret dan lepas di sini
                </p>
                <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">
                  Format JPG, PNG, WEBP maksimal 10 MB.
                </p>
              </div>

              <template x-if="previewUrl">
                <div class="rounded-xl overflow-hidden bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-2xs">
                  <div @click="lightboxUrl = previewUrl; lightboxOpen = true"
                       class="relative aspect-[4/3] w-full overflow-hidden bg-gray-100 dark:bg-gray-950 cursor-pointer group">
                    <img :src="previewUrl" alt="Pratinjau Bukti Transfer" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    <div class="absolute inset-0 bg-black/45 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                      <span class="text-xs font-bold text-white tracking-wide">Pratinjau</span>
                    </div>
                  </div>
                  <div class="px-3.5 py-2 flex items-center justify-between gap-2 border-t border-gray-100 dark:border-gray-700/80">
                    <span class="text-[11px] font-bold text-gray-700 dark:text-gray-300 truncate" x-text="fileName + (fileSizeText ? ' • ' + fileSizeText : '')"></span>
                    <button type="button" @click="clearFile()" class="text-[11px] font-bold text-rose-600 dark:text-rose-400 hover:underline shrink-0 cursor-pointer">Hapus</button>
                  </div>
                </div>
              </template>
            </div>
            <p x-show="touched && error" x-text="error" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
          </div>

          <div class="flex items-center justify-center gap-3">
            <button type="button" @click="showModal = false" class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 text-gray-700 dark:text-gray-300 font-bold text-xs transition cursor-pointer">
              Batal
            </button>
            <button type="submit" class="px-6 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs rounded-xl shadow-xs transition cursor-pointer">
              Simpan Data
            </button>
          </div>
        </form>

      </div>
    </div>

    <!-- Lightbox Pratinjau Bukti Transfer (Rule #97) -->
    <div x-show="lightboxOpen"
         x-cloak
         @keydown.escape.window="lightboxOpen = false"
         @click.self="lightboxOpen = false"
         class="fixed inset-0 z-[9999] bg-black/90 backdrop-blur-md flex flex-col items-center justify-center p-4 sm:p-8">
      <button type="button"
              @click="lightboxOpen = false"
              aria-label="Tutup pratinjau"
              class="absolute top-4 right-4 sm:top-6 sm:right-6 w-11 h-11 rounded-full bg-white/10 hover:bg-white/25 text-white flex items-center justify-center transition cursor-pointer">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
      <img :src="lightboxUrl" alt="Bukti Transfer" class="max-w-full max-h-[82vh] object-contain rounded-2xl shadow-2xl border border-white/10 select-none">
    </div>

  </div>
</section>
@endsection
