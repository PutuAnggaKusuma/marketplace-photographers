@extends('layouts.app')

@section('title', 'Form Reservasi Sesi Foto — LensMatch')

@section('content')
<section class="py-12 sm:py-16 lg:py-20 bg-white dark:bg-gray-900 min-h-screen">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10 lg:space-y-12" x-data="{ 
    basePrice: {{ $service->tarif_harga }},
    extraTotal: 0,
    paymentType: 'full',
    selectedDate: '{{ old('tanggal_sesi', $preselectedDate ?? date('Y-m-d', strtotime('+2 days'))) }}',
    blockedDates: {{ Illuminate\Support\Js::from($blockedDates ?? []) }},
    bookedDates: {{ Illuminate\Support\Js::from($bookedDates ?? []) }},
    get totalBill() {
        return this.basePrice + this.extraTotal;
    },
    get dpAmount() {
        return Math.round(this.totalBill * 0.3);
    },
    get remainingAmount() {
        return this.totalBill - this.dpAmount;
    },
    get currentPayAmount() {
        return this.paymentType === 'dp' ? this.dpAmount : this.totalBill;
    },
    get isDateUnavailable() {
        return this.blockedDates.includes(this.selectedDate) || this.bookedDates.includes(this.selectedDate);
    },
    get dateUnavailableReason() {
        if (this.blockedDates.includes(this.selectedDate)) {
            return 'Fotografer sedang libur / memblokir tanggal ini.';
        }
        if (this.bookedDates.includes(this.selectedDate)) {
            return 'Jadwal fotografer sudah terisi oleh pesanan sesi foto lain.';
        }
        return '';
    },
    updateExtraTotal() {
      let sum = 0;
      document.querySelectorAll('.addon-checkbox:checked').forEach(cb => {
        sum += parseInt(cb.dataset.price || 0);
      });
      this.extraTotal = sum;
    }
  }">

    <!-- Page Header (Standard Proportional Typography & Clean Spacing) -->
    <div class="text-center space-y-2 max-w-2xl mx-auto">
      <!-- Breadcrumb Navigation -->
      <nav class="flex items-center justify-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 mb-1">
        <a href="{{ route('public.photographers.katalog') }}" class="hover:text-amber-500 transition">Katalog</a>
        <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
        <a href="{{ url('/fotografer/' . $photographer->id) }}" class="hover:text-amber-500 transition truncate max-w-[150px]">{{ $photographer->nama }}</a>
        <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
        <span class="text-gray-900 dark:text-white font-bold">Reservasi Sesi Foto</span>
      </nav>
      <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white tracking-tight">
        Form Reservasi Sesi Foto
      </h1>
      <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 leading-relaxed">
        Isi rincian jadwal, lokasi acara, dan kebutuhan sesi foto Anda untuk langsung terhubung dengan fotografer pilihan.
      </p>
    </div>

    <!-- Booking Form Container (Spacious 2-Column Grid) -->
    <form action="{{ route('booking.store') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-start">
      @csrf
      <input type="hidden" name="photographer_id" value="{{ $photographer->id }}">

      <!-- Left Column: Form Inputs (7 Cols) -->
      <div class="lg:col-span-7 space-y-6 lg:space-y-7">

        <!-- Section 1: Tanggal & Waktu Sesi -->
        <div class="bg-white dark:bg-gray-800 p-6 sm:p-7 rounded-2xl border border-gray-200/80 dark:border-gray-700/80 shadow-xs space-y-5">
          <div class="flex items-center gap-3 pb-3 border-b border-gray-100 dark:border-gray-700/60">
            <div class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-500 flex items-center justify-center shrink-0">
              <svg class="w-4.5 h-4.5 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
            <div>
              <h2 class="text-sm sm:text-base font-black text-gray-900 dark:text-white">Jadwal Pelaksanaan Sesi Foto</h2>
              <p class="text-[11px] text-gray-500 dark:text-gray-400">Pilih tanggal dan estimasi waktu pemotretan Anda</p>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Tanggal Sesi Foto <span class="text-rose-500">*</span></label>
              <input type="date" 
                     name="tanggal_sesi" 
                     min="{{ date('Y-m-d') }}" 
                     x-model="selectedDate"
                     required 
                     class="w-full px-4 py-2.5 text-xs font-medium text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 focus:outline-none transition" />
              
              <!-- Real-time Warning if Date is Blocked or Booked -->
              <template x-if="isDateUnavailable">
                <div class="mt-2 p-2.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/60 text-rose-700 dark:text-rose-300 text-[11px] font-bold flex items-center gap-2">
                  <svg class="w-4 h-4 shrink-0 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                  <span x-text="dateUnavailableReason"></span>
                </div>
              </template>
            </div>

            <!-- Custom Alpine.js Dropdown for Jam Mulai Sesi -->
            <div x-data="{ open: false, selectedValue: '16:00', selectedText: '16:00 WIB (Sore Sunset)' }">
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Jam Mulai Sesi <span class="text-rose-500">*</span></label>
              <input type="hidden" name="jam_mulai" :value="selectedValue">
              
              <div class="relative">
                <button type="button" @click="open = !open" class="w-full px-4 py-2.5 text-xs font-medium text-left text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-amber-400 flex items-center justify-between transition cursor-pointer">
                  <span class="truncate" x-text="selectedText"></span>
                  <svg class="w-4 h-4 text-gray-400 shrink-0 ml-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                </button>

                <div x-show="open" @click.away="open = false" x-transition class="absolute z-50 w-full mt-1.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl py-1 overflow-hidden">
                  <div @click="selectedValue = '08:00'; selectedText = '08:00 WIB (Pagi)'; open = false;" class="px-4 py-2.5 text-xs text-gray-800 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 border-b border-gray-100 dark:border-gray-700/60 transition cursor-pointer">
                    08:00 WIB (Pagi)
                  </div>
                  <div @click="selectedValue = '10:00'; selectedText = '10:00 WIB (Pagi)'; open = false;" class="px-4 py-2.5 text-xs text-gray-800 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 border-b border-gray-100 dark:border-gray-700/60 transition cursor-pointer">
                    10:00 WIB (Pagi)
                  </div>
                  <div @click="selectedValue = '14:00'; selectedText = '14:00 WIB (Siang)'; open = false;" class="px-4 py-2.5 text-xs text-gray-800 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 border-b border-gray-100 dark:border-gray-700/60 transition cursor-pointer">
                    14:00 WIB (Siang)
                  </div>
                  <div @click="selectedValue = '16:00'; selectedText = '16:00 WIB (Sore Sunset)'; open = false;" class="px-4 py-2.5 text-xs text-gray-800 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 border-b border-gray-100 dark:border-gray-700/60 transition cursor-pointer">
                    16:00 WIB (Sore Sunset)
                  </div>
                  <div @click="selectedValue = '19:00'; selectedText = '19:00 WIB (Malam)'; open = false;" class="px-4 py-2.5 text-xs text-gray-800 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition cursor-pointer">
                    19:00 WIB (Malam)
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Section 2: Lokasi Acara / Sesi Foto -->
        <div class="bg-white dark:bg-gray-800 p-6 sm:p-7 rounded-2xl border border-gray-200/80 dark:border-gray-700/80 shadow-xs space-y-5">
          <div class="flex items-center gap-3 pb-3 border-b border-gray-100 dark:border-gray-700/60">
            <div class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-500 flex items-center justify-center shrink-0">
              <svg class="w-4.5 h-4.5 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            </div>
            <div>
              <h2 class="text-sm sm:text-base font-black text-gray-900 dark:text-white">Lokasi Pemotretan / Acara</h2>
              <p class="text-[11px] text-gray-500 dark:text-gray-400">Tentukan alamat dan wilayah tempat acara diselenggarakan</p>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            
            <!-- Province Custom Alpine Dropdown -->
            <div x-data="{ open: false, selectedCode: '', selectedText: 'Pilih Provinsi' }">
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Provinsi <span class="text-rose-500">*</span></label>
              <input type="hidden" name="province_code" :value="selectedCode">
              
              <div class="relative">
                <button type="button" @click="open = !open" class="w-full px-4 py-2.5 text-xs font-medium text-left text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-amber-400 flex items-center justify-between transition cursor-pointer">
                  <span class="truncate" x-text="selectedText"></span>
                  <svg class="w-4 h-4 text-gray-400 shrink-0 ml-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                </button>

                <div x-show="open" @click.away="open = false" x-transition class="absolute z-50 w-full mt-1.5 max-h-60 overflow-y-auto bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl py-1">
                  @foreach($provinces as $prov)
                    <div @click="selectedCode = '{{ $prov->code }}'; selectedText = '{{ addslashes($prov->name) }}'; open = false; fetchBookingCities('{{ $prov->code }}');" class="px-4 py-2.5 text-xs text-gray-800 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 border-b border-gray-100 dark:border-gray-700/60 transition cursor-pointer last:border-b-0">
                      {{ $prov->name }}
                    </div>
                  @endforeach
                </div>
              </div>
            </div>

            <!-- City Custom Alpine Dropdown -->
            <div x-data="{ open: false, selectedCode: '', selectedText: 'Pilih Kota / Kabupaten' }">
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Kota / Kabupaten <span class="text-rose-500">*</span></label>
              <input type="hidden" name="city_code" :value="selectedCode" id="bookingCityInput">
              
              <div class="relative">
                <button type="button" @click="open = !open" class="w-full px-4 py-2.5 text-xs font-medium text-left text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-amber-400 flex items-center justify-between transition cursor-pointer">
                  <span class="truncate" x-text="selectedText" id="bookingCityLabel"></span>
                  <svg class="w-4 h-4 text-gray-400 shrink-0 ml-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                </button>

                <div x-show="open" @click.away="open = false" x-transition class="absolute z-50 w-full mt-1.5 max-h-60 overflow-y-auto bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl py-1" id="bookingCityOptionsList">
                  <div class="px-4 py-2.5 text-xs text-gray-400 font-semibold">Pilih provinsi terlebih dahulu</div>
                </div>
              </div>
            </div>

          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Alamat Lengkap / Nama Venue <span class="text-rose-500">*</span></label>
            <textarea name="lokasi_acara" rows="3" placeholder="Contoh: Pantai Melasti Kuta Selatan / Gedung Pernikahan Sasana Krida Bandung..." required class="w-full px-4 py-2.5 text-xs font-medium text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 focus:outline-none transition leading-relaxed"></textarea>
          </div>
        </div>

        <!-- Section 3: Catatan Khusus -->
        <div class="bg-white dark:bg-gray-800 p-6 sm:p-7 rounded-2xl border border-gray-200/80 dark:border-gray-700/80 shadow-xs space-y-5">
          <div class="flex items-center gap-3 pb-3 border-b border-gray-100 dark:border-gray-700/60">
            <div class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-500 flex items-center justify-center shrink-0">
              <svg class="w-4.5 h-4.5 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            </div>
            <div>
              <h2 class="text-sm sm:text-base font-black text-gray-900 dark:text-white">Catatan Khusus</h2>
              <p class="text-[11px] text-gray-500 dark:text-gray-400">Sampaikan konsep foto, dresscode, atau request khusus kepada fotografer</p>
            </div>
          </div>

          <div>
            <textarea name="catatan_khusus" rows="3" placeholder="Tuliskan tema busana/gaun, jumlah anggota keluarga/tamu inti, atau referensi gaya foto yang diinginkan..." class="w-full px-4 py-2.5 text-xs font-medium text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 focus:outline-none transition leading-relaxed"></textarea>
          </div>
        </div>

      </div>

      <!-- Right Column: Order Summary & Live Price Calculator (5 Cols) -->
      <div class="lg:col-span-5 space-y-6 sticky top-28">

        <!-- Selected Package Card -->
        <div class="bg-white dark:bg-gray-800 p-6 sm:p-7 rounded-2xl border border-gray-200/80 dark:border-gray-700/80 shadow-sm space-y-6 sm:space-y-7">
          <div>
            <h3 class="text-sm font-black text-gray-900 dark:text-white">
              Ringkasan Pesanan & Tarif
            </h3>
            <p class="text-[11px] text-gray-400 mt-0.5">Rincian paket sesi dan estimasi total biaya</p>
          </div>

          <!-- Photographer Profile Info -->
          <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-900/60 border border-gray-100 dark:border-gray-700/60">
            <x-user-avatar :user="$photographer->user" size="w-10 h-10" />
            <div class="flex-1">
              <h4 class="text-xs font-black text-gray-900 dark:text-white leading-snug">{{ $photographer->nama }}</h4>
              <p class="text-[11px] text-gray-500 flex items-center gap-1 mt-0.5">
                <svg class="w-3.5 h-3.5 text-amber-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg> 
                <span class="truncate">{{ $photographer->city->name ?? $photographer->alamat }}</span>
              </p>
            </div>
          </div>

          <!-- Service Package Selection -->
          <div class="p-4 rounded-xl bg-amber-50/70 dark:bg-amber-950/40 border border-amber-200/80 dark:border-amber-800/60 space-y-2.5">
            <div class="flex items-center justify-between">
              <span class="text-[10px] font-black uppercase tracking-wider text-amber-800 dark:text-amber-300">Paket Terpilih</span>
              <span class="text-xs font-black text-amber-600 dark:text-amber-400">Rp {{ number_format($service->tarif_harga, 0, ',', '.') }}</span>
            </div>
            <div>
              <h5 class="text-xs font-black text-gray-900 dark:text-white">{{ $service->nama_layanan }}</h5>
              <input type="hidden" name="service_id" value="{{ $service->id }}">
              <p class="text-[11px] text-gray-600 dark:text-gray-300 leading-relaxed mt-1 line-clamp-3">{{ $service->deskripsi_layanan }}</p>
            </div>
            @if(isset($allServices) && $allServices->count() > 1)
              <div class="pt-2 border-t border-amber-200/70 dark:border-amber-800/60" x-data="{ openPkgDropdown: false }">
                <label class="block text-[11px] font-bold text-gray-700 dark:text-gray-300 mb-1">Ganti Pilihan Paket:</label>
                <div class="relative">
                  <button type="button"
                          @click="openPkgDropdown = !openPkgDropdown"
                          class="w-full flex items-center justify-between px-3 py-1.5 text-xs font-bold text-gray-800 dark:text-white bg-white dark:bg-gray-900 rounded-lg border border-amber-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition cursor-pointer">
                    <span>{{ $service->nama_layanan }} &mdash; Rp {{ number_format($service->tarif_harga, 0, ',', '.') }}</span>
                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 shrink-0" :class="{ 'rotate-180': openPkgDropdown }" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                  </button>
                  <div x-show="openPkgDropdown"
                       @click.outside="openPkgDropdown = false"
                       x-transition
                       class="absolute z-50 top-full mt-1 left-0 w-full bg-white dark:bg-gray-900 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 p-1 space-y-0.5 overflow-hidden"
                       style="display: none;">
                    @foreach($allServices as $s)
                      <a href="{{ url('/booking/create?photographer_id=' . $photographer->id . '&service_id=' . $s->id) }}"
                         class="block w-full text-left px-3 py-2 text-xs font-bold rounded-lg text-gray-800 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-700 dark:hover:text-amber-300 transition {{ $s->id == $service->id ? 'bg-amber-50 dark:bg-amber-950/40 text-amber-600 font-black' : '' }}">
                        {{ $s->nama_layanan }} &mdash; Rp {{ number_format($s->tarif_harga, 0, ',', '.') }}
                      </a>
                    @endforeach
                  </div>
                </div>
              </div>
            @endif
          </div>

          <!-- Extra Feature Add-ons (If Available) -->
          @if($service->details->count() > 0)
            <div class="space-y-2.5">
              <span class="text-[11px] font-black text-gray-800 dark:text-gray-200 uppercase tracking-wider block">Fitur Tambahan (Opsional):</span>
              <div class="space-y-2.5">
                @foreach($service->details as $det)
                  <label class="flex items-start gap-2.5 p-2.5 rounded-xl bg-gray-50/70 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-700/60 text-xs text-gray-700 dark:text-gray-300 hover:border-amber-300 transition cursor-pointer">
                    <input type="checkbox" name="selected_features[]" value="{{ $det->id }}" data-price="{{ $det->tarif_harga }}" @change="updateExtraTotal()" class="addon-checkbox mt-0.5 rounded text-amber-500 focus:ring-amber-400 w-3.5 h-3.5">
                    <div class="flex-1">
                      <span class="font-medium text-gray-800 dark:text-gray-200 block">{{ $det->nama_fitur }}</span>
                      @if($det->tarif_harga > 0)
                        <span class="text-[11px] font-black text-amber-600 dark:text-amber-400 mt-0.5 block">+Rp {{ number_format($det->tarif_harga, 0, ',', '.') }}</span>
                      @endif
                    </div>
                  </label>
                @endforeach
              </div>
            </div>
          @endif

          <!-- Pilihan Skema Pembayaran (Bayar Penuh / DP 30%) -->
          <div class="space-y-2.5">
            <span class="text-[11px] font-black text-gray-800 dark:text-gray-200 uppercase tracking-wider block">Skema Pembayaran:</span>
            <input type="hidden" name="payment_type" :value="paymentType">
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
              <!-- Opsi 1: Bayar Penuh 100% -->
              <div @click="paymentType = 'full'" 
                   :class="paymentType === 'full' ? 'border-amber-400 bg-amber-50/70 dark:bg-amber-950/40 text-amber-950 dark:text-amber-100 shadow-2xs' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 hover:border-gray-300'"
                   class="p-3.5 rounded-xl border transition cursor-pointer select-none space-y-1">
                <div class="flex items-center justify-between">
                  <span class="text-xs font-black">Bayar Penuh (100%)</span>
                  <span class="w-3.5 h-3.5 rounded-full border-2 flex items-center justify-center shrink-0" :class="paymentType === 'full' ? 'border-amber-500 bg-white dark:bg-gray-800' : 'border-gray-300 dark:border-gray-600'">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500" x-show="paymentType === 'full'"></span>
                  </span>
                </div>
                <p class="text-[11px] text-gray-500 dark:text-gray-400">Pembayaran aman & bergaransi LensMatch</p>
                <p class="text-xs font-black text-gray-900 dark:text-white pt-1" x-text="'Rp ' + (totalBill).toLocaleString('id-ID')"></p>
              </div>

              <!-- Opsi 2: Uang Muka DP 30% -->
              <div @click="paymentType = 'dp'" 
                   :class="paymentType === 'dp' ? 'border-amber-400 bg-amber-50/70 dark:bg-amber-950/40 text-amber-950 dark:text-amber-100 shadow-2xs' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 hover:border-gray-300'"
                   class="p-3.5 rounded-xl border transition cursor-pointer select-none space-y-1">
                <div class="flex items-center justify-between">
                  <span class="text-xs font-black">Uang Muka (DP 30%)</span>
                  <span class="w-3.5 h-3.5 rounded-full border-2 flex items-center justify-center shrink-0" :class="paymentType === 'dp' ? 'border-amber-500 bg-white dark:bg-gray-800' : 'border-gray-300 dark:border-gray-600'">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500" x-show="paymentType === 'dp'"></span>
                  </span>
                </div>
                <p class="text-[11px] text-gray-500 dark:text-gray-400">Sisa 70% dilunasi kemudian</p>
                <p class="text-xs font-black text-amber-600 dark:text-amber-400 pt-1" x-text="'Rp ' + (dpAmount).toLocaleString('id-ID')"></p>
              </div>
            </div>
          </div>

          <!-- Total Calculation (Clean Spaced Layout Without Line Dividers) -->
          <div class="space-y-3 pt-1">
            <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 font-medium">
              <span>Tarif Paket Dasar</span>
              <span class="font-bold text-gray-800 dark:text-gray-200">Rp {{ number_format($service->tarif_harga, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 font-medium">
              <span>Fitur Tambahan</span>
              <span class="font-bold text-gray-800 dark:text-gray-200" x-text="'Rp ' + (extraTotal).toLocaleString('id-ID')">Rp 0</span>
            </div>
            <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 font-medium">
              <span>Total Nilai Sesi</span>
              <span class="font-bold text-gray-900 dark:text-white" x-text="'Rp ' + (totalBill).toLocaleString('id-ID')">
                Rp {{ number_format($service->tarif_harga, 0, ',', '.') }}
              </span>
            </div>

            <template x-if="paymentType === 'dp'">
              <div class="space-y-2 pt-2 border-t border-dashed border-gray-200 dark:border-gray-700">
                <div class="flex justify-between text-xs font-bold text-amber-600 dark:text-amber-400">
                  <span>Tagihan DP (30%) Sekarang</span>
                  <span x-text="'Rp ' + (dpAmount).toLocaleString('id-ID')"></span>
                </div>
                <div class="flex justify-between text-[11px] text-gray-500 dark:text-gray-400">
                  <span>Sisa Pelunasan Nanti (70%)</span>
                  <span class="font-medium" x-text="'Rp ' + (remainingAmount).toLocaleString('id-ID')"></span>
                </div>
              </div>
            </template>

            <div class="flex justify-between items-center text-gray-900 dark:text-white pt-2">
              <span class="text-xs font-bold">Total Dibayar Sekarang</span>
              <span class="text-lg font-black text-amber-600 dark:text-amber-400" x-text="'Rp ' + (currentPayAmount).toLocaleString('id-ID')">
                Rp {{ number_format($service->tarif_harga, 0, ',', '.') }}
              </span>
            </div>
          </div>

          <!-- Submit Action Button -->
          <div class="pt-2">
            <button type="submit" 
                    :disabled="isDateUnavailable"
                    :class="isDateUnavailable ? 'opacity-50 cursor-not-allowed bg-gray-300 dark:bg-gray-700 text-gray-500' : 'bg-amber-400 hover:bg-amber-500 text-gray-900 shadow-md cursor-pointer'"
                    class="w-full py-3.5 font-black text-xs rounded-xl transition text-center block">
              
              <span>Lanjut ke Invoice Pembayaran</span>
            </button>
          </div>
        </div>

      </div>
    </form>

  </div>
</section>

<!-- Script AJAX Fetch City for Booking Form -->
<script>
  function fetchBookingCities(provinceCode) {
    const cityInput = document.getElementById('bookingCityInput');
    const cityLabel = document.getElementById('bookingCityLabel');
    const cityList = document.getElementById('bookingCityOptionsList');

    cityInput.value = '';
    cityLabel.textContent = 'Memuat Kota...';
    cityList.innerHTML = '<div class="px-4 py-2.5 text-xs text-gray-400 font-semibold">Memuat data kota...</div>';

    fetch('/api/cities/' + provinceCode)
      .then(res => res.json())
      .then(data => {
        cityLabel.textContent = 'Pilih Kota / Kabupaten';
        let html = '';
        data.forEach(city => {
          const escapedName = city.name.replace(/'/g, "\\'");
          html += `
            <div onclick="selectBookingCity('${city.code}', '${escapedName}')" class="px-4 py-2.5 text-xs text-gray-800 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 border-b border-gray-100 dark:border-gray-700/60 transition cursor-pointer last:border-b-0">
              ${city.name}
            </div>
          `;
        });
        cityList.innerHTML = html;
      });
  }

  function selectBookingCity(code, name) {
    document.getElementById('bookingCityInput').value = code;
    document.getElementById('bookingCityLabel').textContent = name;
  }
</script>
@endsection