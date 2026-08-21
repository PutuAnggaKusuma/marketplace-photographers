@extends('layouts.app')

@section('title', 'Form Reservasi Sesi Foto — LensMatch')

@section('content')
<section class="py-12 bg-white dark:bg-gray-900/50 min-h-screen">
  <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8" x-data="{ 
    basePrice: {{ $service->tarif_harga }},
    extraTotal: 0,
    updateExtraTotal() {
      let sum = 0;
      document.querySelectorAll('.addon-checkbox:checked').forEach(cb => {
        sum += parseInt(cb.dataset.price || 0);
      });
      this.extraTotal = sum;
    }
  }">

    <!-- Page Header -->
    <div class="text-center space-y-2">
      <span class="px-3.5 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300">
        Langkah 1 dari 2 — Formulir Pemesanan Sesi Foto
      </span>
      <h1 class="text-3xl sm:text-4xl font-black text-gray-900 dark:text-white">Form Reservasi Sesi Foto</h1>
      <p class="text-xs sm:text-sm text-gray-500 max-w-xl mx-auto leading-relaxed">Isi rincian jadwal, lokasi acara, dan kebutuhan sesi foto Anda untuk langsung terhubung dengan studio fotografer.</p>
    </div>

    <!-- Booking Form Container -->
    <form action="{{ route('booking.store') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      @csrf
      <input type="hidden" name="photographer_id" value="{{ $photographer->id }}">

      <!-- Left Column: Form Inputs -->
      <div class="lg:col-span-2 space-y-6">

        <!-- Section 1: Tanggal & Waktu Sesi -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-xs space-y-4">
          <h2 class="text-base font-extrabold text-gray-900 dark:text-white flex items-center gap-2.5">
            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            <span>Tanggal & Waktu Pelaksanaan Sesi Foto</span>
          </h2>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Tanggal Sesi Foto <span class="text-red-500">*</span></label>
              <input type="date" name="tanggal_sesi" min="{{ date('Y-m-d') }}" value="{{ old('tanggal_sesi', date('Y-m-d', strtotime('+2 days'))) }}" required class="w-full px-3.5 py-2.5 text-xs text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-amber-400 focus:outline-none" />
            </div>

            <!-- Custom Alpine.js Dropdown for Jam Mulai Sesi -->
            <div x-data="{ open: false, selectedValue: '16:00', selectedText: '16:00 WIB (Sore Sunset)' }">
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Jam Mulai Sesi <span class="text-red-500">*</span></label>
              <input type="hidden" name="jam_mulai" :value="selectedValue">
              
              <div class="relative">
                <button type="button" @click="open = !open" class="w-full pl-3.5 pr-9 py-2.5 text-xs font-semibold text-left text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-amber-400 flex items-center justify-between">
                  <span class="truncate" x-text="selectedText"></span>
                  <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                  </div>
                </button>

                <div x-show="open" @click.away="open = false" x-transition class="absolute z-50 w-full mt-1.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl py-1">
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
                  <div @click="selectedValue = '19:00'; selectedText = '19:00 WIB (Malam)'; open = false;" class="px-4 py-2.5 text-xs text-gray-800 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 border-b border-gray-100 dark:border-gray-700/60 transition cursor-pointer last:border-b-0">
                    19:00 WIB (Malam)
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Section 2: Lokasi Acara / Sesi Foto -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-xs space-y-4">
          <h2 class="text-base font-extrabold text-gray-900 dark:text-white flex items-center gap-2.5">
            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            <span>Lokasi Sesi Foto / Acara</span>
          </h2>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            
            <!-- Province Custom Alpine Dropdown -->
            <div x-data="{ open: false, selectedCode: '', selectedText: 'Pilih Provinsi' }">
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Provinsi <span class="text-red-500">*</span></label>
              <input type="hidden" name="province_code" :value="selectedCode">
              
              <div class="relative">
                <button type="button" @click="open = !open" class="w-full pl-3.5 pr-9 py-2.5 text-xs font-semibold text-left text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-amber-400 flex items-center justify-between">
                  <span class="truncate" x-text="selectedText"></span>
                  <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                  </div>
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
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Kota / Kabupaten <span class="text-red-500">*</span></label>
              <input type="hidden" name="city_code" :value="selectedCode" id="bookingCityInput">
              
              <div class="relative">
                <button type="button" @click="open = !open" class="w-full pl-3.5 pr-9 py-2.5 text-xs font-semibold text-left text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-amber-400 flex items-center justify-between">
                  <span class="truncate" x-text="selectedText" id="bookingCityLabel"></span>
                  <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                  </div>
                </button>

                <div x-show="open" @click.away="open = false" x-transition class="absolute z-50 w-full mt-1.5 max-h-60 overflow-y-auto bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl py-1" id="bookingCityOptionsList">
                  <div class="px-4 py-2.5 text-xs text-gray-400 font-semibold">Pilih provinsi terlebih dahulu</div>
                </div>
              </div>
            </div>

          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Alamat / Nama Tempat Sesi Foto <span class="text-red-500">*</span></label>
            <textarea name="lokasi_acara" rows="2" placeholder="Contoh: Pantai Melasti Unhas / Studio Foto Utama Riau Bandung..." required class="w-full px-3.5 py-2.5 text-xs text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-amber-400 focus:outline-none"></textarea>
          </div>
        </div>

        <!-- Section 3: Catatan Khusus -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-xs space-y-4">
          <h2 class="text-base font-extrabold text-gray-900 dark:text-white flex items-center gap-2.5">
            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            <span>Catatan / Request Khusus Klien</span>
          </h2>

          <div>
            <textarea name="catatan_khusus" rows="3" placeholder="Tuliskan tema gaun/baju, jumlah anggota keluarga, atau request gaya foto tertentu..." class="w-full px-3.5 py-2.5 text-xs text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-amber-400 focus:outline-none"></textarea>
          </div>
        </div>

      </div>

      <!-- Right Column: Order Summary & Live Price Calculator -->
      <div class="space-y-6">

        <!-- Selected Package Card -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm space-y-5 sticky top-24">
          <h3 class="text-sm font-extrabold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-3">
            Ringkasan Pesanan & Tarif
          </h3>

          <!-- Studio Photographer Info -->
          <div class="flex items-center gap-3">
            <img onerror="this.onerror=null;this.src=window.SVG_IMAGE_FALLBACK||'data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='800' height='600' viewBox='0 0 800 600' fill='none'%3E%3Crect width='800' height='600' fill='%23F3F4F6'/%3E%3Cpath d='M360 260C360 248.954 368.954 240 380 240H420C431.046 240 440 248.954 440 260V265H450C466.569 265 480 278.431 480 295V345C480 361.569 466.569 375 450 375H350C333.431 375 320 361.569 320 345V295C320 278.431 333.431 265 350 265H360V260Z' stroke='%239CA3AF' stroke-width='12' stroke-linecap='round' stroke-linejoin='round'/%3E%3Ccircle cx='400' cy='320' r='30' stroke='%239CA3AF' stroke-width='12'/%3E%3Cline x1='310' y1='240' x2='490' y2='390' stroke='%23EF4444' stroke-width='10' stroke-linecap='round'/%3E%3Ctext x='400' y='450' font-family='sans-serif' font-size='22' font-weight='700' fill='%236B7280' text-anchor='middle'%3EGambar Tidak Dapat Dimuat%3C/text%3E%3C/svg%3E';" src="{{ $photographer->foto }}" alt="{{ $photographer->nama }}" class="w-11 h-11 rounded-full object-cover border-2 border-amber-400 shrink-0" />
            <div class="min-w-0">
              <h4 class="text-xs font-extrabold text-gray-900 dark:text-white truncate">{{ $photographer->nama }}</h4>
              <p class="text-[11px] text-gray-500"><svg class="w-3.5 h-3.5 text-amber-500 inline-block shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg> {{ $photographer->city->name ?? $photographer->alamat }}</p>
            </div>
          </div>

          <!-- Service Package Selection -->
          <div class="p-4 rounded-xl bg-amber-50/70 dark:bg-amber-950/40 border border-amber-200/80 dark:border-amber-800/60 space-y-2">
            <span class="text-[10px] font-black uppercase text-amber-800 dark:text-amber-300">Paket Terpilih</span>
            <div class="flex items-center justify-between">
              <h5 class="text-xs font-bold text-gray-900 dark:text-white">{{ $service->nama_layanan }}</h5>
              <input type="hidden" name="service_id" value="{{ $service->id }}">
            </div>
            <p class="text-[11px] text-gray-500 line-clamp-2">{{ $service->deskripsi_layanan }}</p>
            <div class="text-sm font-black text-amber-600 dark:text-amber-400 pt-1">
              Rp {{ number_format($service->tarif_harga, 0, ',', '.') }}
            </div>
          </div>

          <!-- Extra Feature Add-ons (If Available) -->
          @if($service->details->count() > 0)
            <div class="space-y-2.5 pt-2">
              <span class="text-xs font-bold text-gray-700 dark:text-gray-300">Fitur Tambahan (Opsional):</span>
              @foreach($service->details as $det)
                <label class="flex items-start gap-2.5 text-xs text-gray-700 dark:text-gray-300 cursor-pointer">
                  <input type="checkbox" name="selected_features[]" value="{{ $det->id }}" data-price="{{ $det->tarif_harga }}" @change="updateExtraTotal()" class="addon-checkbox mt-0.5 rounded text-amber-500 focus:ring-amber-400">
                  <span>{{ $det->nama_fitur }} @if($det->tarif_harga > 0) <strong class="text-amber-600 dark:text-amber-400">(+Rp {{ number_format($det->tarif_harga, 0, ',', '.') }})</strong> @endif</span>
                </label>
              @endforeach
            </div>
          @endif

          <!-- Total Calculation -->
          <div class="pt-4 border-t border-gray-100 dark:border-gray-700 space-y-2">
            <div class="flex justify-between text-xs text-gray-500">
              <span>Tarif Paket Dasar</span>
              <span>Rp {{ number_format($service->tarif_harga, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-xs text-gray-500">
              <span>Fitur Tambahan</span>
              <span x-text="'Rp ' + (extraTotal).toLocaleString('id-ID')">Rp 0</span>
            </div>
            <div class="flex justify-between text-base font-black text-gray-900 dark:text-white pt-2 border-t border-dashed border-gray-200 dark:border-gray-700">
              <span>Total Tagihan</span>
              <span class="text-amber-600 dark:text-amber-400" x-text="'Rp ' + (basePrice + extraTotal).toLocaleString('id-ID')">
                Rp {{ number_format($service->tarif_harga, 0, ',', '.') }}
              </span>
            </div>
          </div>

          <!-- Submit Action Button -->
          <button type="submit" class="w-full py-3.5 bg-amber-400 hover:bg-amber-500 text-gray-900 font-black text-xs rounded-xl shadow-lg transition flex items-center justify-center gap-2">
            <svg class="w-4 h-4 text-gray-900" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            <span>Lanjut ke Invoice Pembayaran</span>
          </button>
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