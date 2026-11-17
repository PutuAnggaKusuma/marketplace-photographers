@extends('layouts.app')

@section('title', 'Selesaikan Pembayaran — LensMatch')

@section('content')
@php
  $paymentAmount = $payment ? $payment->payment_amount : $contract->jumlah;
  $bookingDetail = $contract->bookingDetail;
  $serviceName = $bookingDetail?->service?->nama_layanan ?? 'Paket Layanan Fotografi';
  $photographerName = $contract->photographer->nama ?? 'Mitra Fotografer';
  $cityName = $contract->photographer->city->name ?? $contract->photographer->alamat ?? '-';
  $dateRangeLabel = $bookingDetail ? $bookingDetail->date_range_label : '-';
  $invStart = $bookingDetail?->jam_mulai ? substr($bookingDetail->jam_mulai, 0, 5) : null;
  $invEnd = $bookingDetail?->jam_selesai ? substr($bookingDetail->jam_selesai, 0, 5) : null;
  $effectiveDuration = $bookingDetail?->effective_duration_hours ?? 0;
  $expiredAt = $contract->expired_at ?? $contract->calculatePaymentDeadline();
  $isPendingVerification = $payment && $payment->bukti_transfer && $payment->payment_status === 'pending';
  $orderCode = $payment->external_id ?? ('KTRK-' . str_pad($contract->id, 5, '0', STR_PAD_LEFT));
  $bankAccount = config('app.escrow_account_number', '8840-1928-3746');
  $bankAccountClean = str_replace(['-', ' '], '', $bankAccount);
  $bankHolder = config('app.escrow_account_holder', 'PT LensMatch Indonesia');
  $bankName = config('app.escrow_bank', 'Bank Central Asia (BCA)');
@endphp

<section class="py-10 sm:py-14 bg-gray-50/70 dark:bg-gray-900/60 min-h-screen">
  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6" 
       x-data="{ 
         lightboxOpen: false, 
         lightboxUrl: '',
         reuploadOpen: false,
         copiedAccount: false,
         copiedAmount: false,
         copyText(text, type) {
           if (navigator.clipboard && navigator.clipboard.writeText) {
             navigator.clipboard.writeText(text).then(() => {
               if (type === 'account') {
                 this.copiedAccount = true;
                 setTimeout(() => this.copiedAccount = false, 2000);
               } else if (type === 'amount') {
                 this.copiedAmount = true;
                 setTimeout(() => this.copiedAmount = false, 2000);
               }
             });
           }
         }
       }">

    <!-- Top Action Navigation (Adaptive 2-Layer Back Resolution) -->
    @php
      $from = request('from');
      $prev = url()->previous();

      if (!$from && $prev && $prev !== url()->current()) {
        if (str_contains($prev, '/client/invoices')) {
          $from = 'invoices';
        } elseif (str_contains($prev, '/client/bookings')) {
          $from = 'bookings';
        } elseif (str_contains($prev, '/chat')) {
          $from = 'chat';
        } elseif (str_contains($prev, '/admin/transactions')) {
          $from = 'admin';
        }
      }

      if ($from === 'invoices') {
        $backUrl = route('client.invoices');
        $backLabel = 'Kembali ke Riwayat Pembayaran';
      } elseif ($from === 'chat') {
        $backUrl = route('client.chat');
        $backLabel = 'Kembali ke Ruang Chat';
      } elseif ($from === 'admin') {
        $backUrl = route('admin.transactions.index');
        $backLabel = 'Kembali ke Kelola Transaksi';
      } else {
        $backUrl = route('client.bookings');
        $backLabel = 'Kembali ke Reservasi Saya';
      }
    @endphp

    <div class="flex items-center justify-between">
      <a href="{{ $backUrl }}" class="text-xs font-bold text-gray-500 hover:text-gray-900 dark:hover:text-white transition flex items-center gap-1.5 cursor-pointer">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
        <span>{{ $backLabel }}</span>
      </a>

      <a href="{{ route('booking.invoice', array_merge(['id' => $contract->id, 'view' => 'invoice', 'from' => 'checkout'], $from ? ['ref' => $from] : [])) }}" class="text-xs font-bold text-gray-500 hover:text-amber-600 dark:hover:text-amber-400 transition flex items-center gap-1 cursor-pointer">
        <span>Lihat Pratinjau Invoice</span>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
      </a>
    </div>

    <!-- Alert Notifikasi Session -->
    @if(session('success'))
      <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 text-xs font-bold text-emerald-800 dark:text-emerald-300">
        {{ session('success') }}
      </div>
    @endif
    @if(session('error'))
      <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800 text-xs font-bold text-rose-800 dark:text-rose-300">
        {{ session('error') }}
      </div>
    @endif

    <!-- Page Header (Rule #51 & Rule #89) -->
    <div class="space-y-1">
      <h1 class="text-xl sm:text-2xl font-black text-gray-900 dark:text-white tracking-tight">Selesaikan Pembayaran</h1>
      <p class="text-xs text-gray-500 dark:text-gray-400">
        Nomor Pemesanan: <span class="font-mono text-gray-600 dark:text-gray-300 font-medium">{{ $orderCode }}</span>
      </p>
    </div>

    <!-- Batas Waktu Pembayaran (Countdown Timer ala Traveloka / Tiket.com) -->
    <div class="p-4 sm:p-5 rounded-2xl bg-amber-50/80 dark:bg-amber-950/30 border border-amber-200/90 dark:border-amber-800/60 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs"
         x-data="{
           targetTime: new Date('{{ $expiredAt->toISOString() }}').getTime(),
           hours: '00',
           minutes: '00',
           seconds: '00',
           isExpired: false,
           timer: null,
           updateTimer() {
             const now = new Date().getTime();
             const distance = this.targetTime - now;
             if (distance <= 0) {
               this.isExpired = true;
               this.hours = '00';
               this.minutes = '00';
               this.seconds = '00';
               if (this.timer) clearInterval(this.timer);
               return;
             }
             const h = Math.floor(distance / (1000 * 60 * 60));
             const m = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
             const s = Math.floor((distance % (1000 * 60)) / 1000);
             this.hours = String(h).padStart(2, '0');
             this.minutes = String(m).padStart(2, '0');
             this.seconds = String(s).padStart(2, '0');
           },
           init() {
             this.updateTimer();
             this.timer = setInterval(() => this.updateTimer(), 1000);
           }
         }">
      <div class="space-y-0.5">
        <span class="font-bold text-gray-900 dark:text-white block">Batas Waktu Pembayaran</span>
        <p class="text-gray-600 dark:text-gray-400">
          Selesaikan transfer sebelum <strong class="text-gray-800 dark:text-gray-200">{{ $expiredAt->translatedFormat('d F Y, H:i') }} WIB</strong>
          @if($expiredAt->isToday())
            <span class="text-amber-700 dark:text-amber-300 font-bold">(Hari Ini)</span>
          @elseif($expiredAt->isTomorrow())
            <span class="text-amber-700 dark:text-amber-300 font-bold">(Besok)</span>
          @endif
        </p>
      </div>

      <div class="flex items-center gap-2">
        <template x-if="!isExpired">
          <div class="flex items-center gap-1.5 font-mono font-bold text-xs sm:text-sm text-amber-900 dark:text-amber-200">
            <span class="px-2.5 py-1 bg-amber-100/90 dark:bg-amber-900/50 rounded-lg" x-text="hours">00</span>
            <span class="text-amber-600">:</span>
            <span class="px-2.5 py-1 bg-amber-100/90 dark:bg-amber-900/50 rounded-lg" x-text="minutes">00</span>
            <span class="text-amber-600">:</span>
            <span class="px-2.5 py-1 bg-amber-100/90 dark:bg-amber-900/50 rounded-lg" x-text="seconds">00</span>
          </div>
        </template>
        <template x-if="isExpired">
          <span class="text-xs font-bold text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/40 px-3 py-1.5 rounded-lg border border-rose-200 dark:border-rose-800">
            Waktu Pembayaran Berakhir
          </span>
        </template>
      </div>
    </div>

    <!-- Main Content 2-Column Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8 items-start">
      
      <!-- Kolom Kiri (7 Kolom): Instruksi Transfer & Form Unggah Bukti -->
      <div class="lg:col-span-7 space-y-6">

        <!-- 1. Kartu Instruksi Transfer Bank Resmi -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-200 dark:border-gray-700/80 shadow-xs p-6 sm:p-7 space-y-5">
          <div class="border-b border-gray-100 dark:border-gray-700/80 pb-4">
            <h2 class="text-sm font-bold text-gray-900 dark:text-white">Instruksi Transfer Bank</h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Transfer nominal tagihan tepat ke rekening resmi LensMatch:</p>
          </div>

          <div class="space-y-4 text-xs">
            <!-- Bank Tujuan -->
            <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700/60">
              <span class="text-gray-500 dark:text-gray-400">Bank Tujuan</span>
              <span class="font-bold text-gray-900 dark:text-white">{{ $bankName }}</span>
            </div>

            <!-- Nomor Rekening + Tombol Salin -->
            <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700/60">
              <span class="text-gray-500 dark:text-gray-400">Nomor Rekening</span>
              <div class="flex items-center gap-2">
                <span class="font-mono font-bold text-gray-900 dark:text-white text-sm tracking-wider">{{ $bankAccount }}</span>
                <button type="button" 
                        @click="copyText('{{ $bankAccountClean }}', 'account')"
                        class="px-2.5 py-1 text-[11px] font-bold rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 hover:bg-gray-100 dark:bg-gray-700/60 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 transition cursor-pointer">
                  <span x-text="copiedAccount ? 'Tersalin!' : 'Salin'">Salin</span>
                </button>
              </div>
            </div>

            <!-- Atas Nama -->
            <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700/60">
              <span class="text-gray-500 dark:text-gray-400">Atas Nama</span>
              <span class="font-semibold text-gray-900 dark:text-white">{{ $bankHolder }}</span>
            </div>

            <!-- Total Nominal Transfer + Tombol Salin Nominal -->
            <div class="flex items-center justify-between py-2">
              <span class="text-gray-500 dark:text-gray-400">Nominal Transfer</span>
              <div class="flex items-center gap-2">
                <span class="font-black text-gray-900 dark:text-white text-base">
                  Rp {{ number_format($paymentAmount, 0, ',', '.') }}
                </span>
                <button type="button" 
                        @click="copyText('{{ $paymentAmount }}', 'amount')"
                        class="px-2.5 py-1 text-[11px] font-bold rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 hover:bg-gray-100 dark:bg-gray-700/60 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 transition cursor-pointer">
                  <span x-text="copiedAmount ? 'Tersalin!' : 'Salin Nominal'">Salin Nominal</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Petunjuk Ringkas -->
          <div class="p-3.5 rounded-2xl bg-gray-50/90 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-700/60 text-[11px] text-gray-500 dark:text-gray-400 leading-relaxed">
            Pastikan transfer sesuai dengan nominal tagihan tepat hingga 3 digit terakhir untuk memudahkan verifikasi transaksi oleh Tim Admin.
          </div>
        </div>

        <!-- 2. Status / Form Unggah Bukti Pembayaran (Rule #20, #21, #91, #97) -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-200 dark:border-gray-700/80 shadow-xs p-6 sm:p-7 space-y-5">
          <div class="border-b border-gray-100 dark:border-gray-700/80 pb-4 flex items-center justify-between">
            <div>
              <h2 class="text-sm font-bold text-gray-900 dark:text-white">Bukti Pembayaran</h2>
              <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Unggah foto struk transfer atau tangkapan layar m-banking Anda.</p>
            </div>
            @if($isPendingVerification)
              <span class="text-[11px] font-bold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/40 px-2.5 py-1 rounded-lg border border-blue-200 dark:border-blue-800">
                Menunggu Verifikasi Admin
              </span>
            @endif
          </div>

          <!-- Tampilan Jika Bukti Sudah Diunggah & Menunggu Verifikasi -->
          @if($isPendingVerification)
            <div class="space-y-4" x-show="!reuploadOpen">
              <div class="p-4 rounded-2xl bg-blue-50/70 dark:bg-blue-950/20 border border-blue-200/80 dark:border-blue-800/50 text-xs text-blue-900 dark:text-blue-200 space-y-1">
                <span class="font-bold block">Bukti transfer telah berhasil dikirim!</span>
                <p class="text-blue-700 dark:text-blue-300">Tim Admin sedang memeriksa bukti transfer Anda. Jadwal sesi foto akan aktif otomatis setelah diverifikasi.</p>
              </div>

              <!-- Pratinjau Struk yang Sudah Ada (Rule #97 Lightbox) -->
              <div class="max-w-xs mx-auto rounded-2xl overflow-hidden bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-2xs">
                <div @click="lightboxUrl = '{{ $payment->bukti_transfer }}'; lightboxOpen = true"
                     class="relative aspect-[4/3] w-full overflow-hidden bg-gray-100 dark:bg-gray-950 cursor-pointer group">
                  <img src="{{ $payment->bukti_transfer }}" alt="Struk Transfer" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                  <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                    <span class="text-xs font-bold text-white tracking-wide">Klik untuk Pratinjau</span>
                  </div>
                </div>
                <div class="p-3 flex items-center justify-between text-xs border-t border-gray-100 dark:border-gray-700/80">
                  <span class="text-gray-500 truncate">Struk Pembayaran Terkirim</span>
                  <button type="button" @click="reuploadOpen = true" class="text-amber-600 dark:text-amber-400 font-bold hover:underline shrink-0 cursor-pointer">
                    Ganti Berkas
                  </button>
                </div>
              </div>
            </div>
          @endif

          <!-- Form Unggah Bukti Transfer Baru (Atau Form Unggah Ulang) -->
          <div x-show="{{ $isPendingVerification ? 'reuploadOpen' : 'true' }}" x-cloak>
            <form action="{{ route('booking.pay.confirm', $contract->id) }}" 
                  method="POST" 
                  enctype="multipart/form-data" 
                  novalidate
                  class="space-y-4"
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
                      const file = incoming[0];
                      const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
                      if (!allowedTypes.includes(file.type)) {
                        this.error = 'Format berkas harus JPG, PNG, atau WEBP.';
                        return false;
                      }
                      if (file.size > 10 * 1024 * 1024) {
                        this.error = 'Ukuran berkas maksimal 10 MB.';
                        return false;
                      }
                      if (typeof DataTransfer !== 'undefined' && this.$refs.fileInput) {
                        const dt = new DataTransfer();
                        dt.items.add(file);
                        this.$refs.fileInput.files = dt.files;
                      }
                      this.fileSelected = true;
                      this.fileName = file.name;
                      this.fileSizeText = (file.size / (1024 * 1024)).toFixed(1) + ' MB';
                      if (this.previewUrl && this.previewUrl.startsWith('blob:')) {
                        URL.revokeObjectURL(this.previewUrl);
                      }
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
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-2">
                  Foto Bukti Transfer <span class="text-rose-500">*</span>
                </label>
                
                <!-- Native Dropzone Area -->
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
                    Pilih Berkas atau Seret ke Sini
                  </p>
                  <p class="text-[11px] text-gray-400 dark:text-gray-500">
                    Format JPG, PNG, WEBP maksimal 10 MB.
                  </p>
                </div>

                <!-- Instant Thumbnail Preview with Interactive Lightbox (Rule #97) -->
                <template x-if="previewUrl">
                  <div class="mt-3 max-w-xs mx-auto rounded-2xl overflow-hidden bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-2xs">
                    <div @click="lightboxUrl = previewUrl; lightboxOpen = true"
                         class="relative aspect-[4/3] w-full overflow-hidden bg-gray-100 dark:bg-gray-950 cursor-pointer group">
                      <img :src="previewUrl" alt="Pratinjau Bukti Transfer" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                      <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                        <span class="text-xs font-bold text-white tracking-wide">Pratinjau</span>
                      </div>
                    </div>
                    <div class="px-3.5 py-2 flex items-center justify-between gap-2 border-t border-gray-100 dark:border-gray-700/80">
                      <span class="text-[11px] font-bold text-gray-700 dark:text-gray-300 truncate" x-text="fileName + (fileSizeText ? ' • ' + fileSizeText : '')"></span>
                      <button type="button" @click="clearFile()" class="text-[11px] font-bold text-rose-600 dark:text-rose-400 hover:underline shrink-0 cursor-pointer">Hapus</button>
                    </div>
                  </div>
                </template>

                <p x-show="touched && error" x-text="error" x-cloak class="text-[11px] font-bold text-red-500 mt-1.5"></p>
              </div>

              <div class="pt-2 flex items-center gap-3">
                @if($isPendingVerification)
                  <button type="button" @click="reuploadOpen = false" class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 text-gray-700 dark:text-gray-300 font-bold text-xs transition cursor-pointer">
                    Batal
                  </button>
                @endif
                <button type="submit" class="flex-1 py-3 px-6 bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs rounded-xl shadow-xs transition text-center cursor-pointer">
                  Kirim Bukti Pembayaran
                </button>
              </div>
            </form>
          </div>

        </div>

      </div>

      <!-- Kolom Kanan (5 Kolom): Ringkasan Reservasi Sesi & Total Tagihan -->
      <div class="lg:col-span-5 space-y-6 lg:sticky lg:top-24">
        
        <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-200 dark:border-gray-700/80 shadow-xs p-6 sm:p-7 space-y-5">
          <div class="border-b border-gray-100 dark:border-gray-700/80 pb-4">
            <h2 class="text-sm font-bold text-gray-900 dark:text-white">Ringkasan Reservasi</h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Detail pemotretan bersama mitra fotografer:</p>
          </div>

          <!-- Info Fotografer -->
          <div class="flex items-center gap-3.5 pb-4 border-b border-gray-100 dark:border-gray-700/60">
            @if($contract->photographer->user?->avatar_url)
              <img src="{{ $contract->photographer->user->avatar_url }}" alt="{{ $photographerName }}" class="w-10 h-10 rounded-full object-cover shrink-0 border border-gray-200 dark:border-gray-700">
            @else
              <div class="w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-200 font-black text-xs flex items-center justify-center shrink-0 border border-amber-200/60 dark:border-amber-800/40">
                {{ strtoupper(substr($photographerName, 0, 2)) }}
              </div>
            @endif
            <div class="space-y-0.5 min-w-0">
              <h3 class="text-xs font-bold text-gray-900 dark:text-white truncate">{{ $photographerName }}</h3>
              <p class="text-[11px] text-gray-500 dark:text-gray-400 truncate">{{ $cityName }}</p>
            </div>
          </div>

          <!-- Info Sesi & Jadwal -->
          <div class="space-y-2.5 text-xs">
            <div>
              <span class="text-[11px] text-gray-400 block">Layanan Pemotretan</span>
              <strong class="font-bold text-gray-900 dark:text-white block">{{ $serviceName }}</strong>
            </div>

            <div>
              <span class="text-[11px] text-gray-400 block">Jadwal Sesi</span>
              <span class="text-gray-700 dark:text-gray-300 block font-medium">{{ $dateRangeLabel }}</span>
              @if($invStart)
                <span class="text-gray-500 text-[11px] block">
                  {{ $invEnd ? "{$invStart} - {$invEnd} WIB" : "{$invStart} WIB" }} ({{ $effectiveDuration }} Jam)
                </span>
              @endif
            </div>

            @if($bookingDetail?->lokasi)
              <div>
                <span class="text-[11px] text-gray-400 block">Lokasi Acara</span>
                <span class="text-gray-700 dark:text-gray-300 block font-medium">{{ $bookingDetail->lokasi }}</span>
              </div>
            @endif
          </div>

          <!-- Rincian Biaya (Rule #106: Seragam text-xs) -->
          <div class="pt-4 border-t border-gray-100 dark:border-gray-700/80 space-y-2 text-xs">
            <div class="flex justify-between text-gray-500 dark:text-gray-400">
              <span>Subtotal Sesi</span>
              <span class="font-semibold text-gray-900 dark:text-white">Rp {{ number_format($contract->jumlah, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-gray-500 dark:text-gray-400">
              <span>Biaya Layanan Platform</span>
              <span class="font-semibold text-gray-900 dark:text-white">Gratis</span>
            </div>
            <div class="pt-2 border-t border-gray-200 dark:border-gray-700 flex justify-between items-baseline">
              <span class="font-bold text-gray-900 dark:text-white text-xs">Total Tagihan</span>
              <span class="font-black text-gray-900 dark:text-white text-xs">
                Rp {{ number_format($paymentAmount, 0, ',', '.') }}
              </span>
            </div>
            <div class="text-[11px] text-gray-400 text-right">
              Tipe: {{ $contract->payment_type === 'dp' ? 'Uang Muka (DP 30%)' : 'Pembayaran Penuh' }}
            </div>
          </div>

        </div>

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
