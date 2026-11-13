@extends('layouts.app')

@section('title', 'Form Reservasi Sesi Foto — LensMatch')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
<style>
  .leaflet-container { font-family: inherit; z-index: 1; }
</style>
@endpush

@section('content')
<section class="py-10 sm:py-14 lg:py-16 bg-white dark:bg-gray-900 min-h-screen">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 sm:space-y-10" x-data="{
    allServices: {{ Illuminate\Support\Js::from($allServices->map(function($s) {
        return [
            'id' => $s->id,
            'nama' => $s->nama_layanan,
            'harga' => (int) $s->tarif_harga,
            'deskripsi' => $s->deskripsi_layanan,
            'spare_time_enabled' => (bool) $s->spare_time_enabled,
            'spare_before' => $s->getEffectiveSpareBeforeMinutes(),
            'spare_after' => $s->getEffectiveSpareAfterMinutes(),
            'spare_summary' => $s->spare_summary,
            'allow_same_location_bypass' => (bool) $s->allow_same_location_bypass,
            'details' => $s->details->map(function($d) {
                return [
                    'id' => $d->id,
                    'nama' => $d->nama_fitur,
                ];
            })->values(),
        ];
    })) }},
    selectedServiceId: {{ $service ? $service->id : 'null' }},
    paymentType: '{{ old('payment_type', 'full') === 'dp' ? 'dp' : 'full' }}',
    selectedDate: '{{ old('tanggal_sesi', $preselectedDate ?? '') }}',
    selectedStartTime: '{{ old('jam_mulai', '') }}',
    selectedEndTime: '{{ old('jam_selesai', '') }}',
    openStartDropdown: false,
    openEndDropdown: false,

    workingHours: {{ Illuminate\Support\Js::from($workingHours ?? []) }},
    blockedDates: {{ Illuminate\Support\Js::from($blockedDates ?? []) }},

    slotLoading: false,
    slotAbortCtrl: null,
    slotMatrix: {
      is_open: true,
      has_available_slots: false,
      reason: null,
      operating_window: null,
      start_options: [],
      end_options_by_start: {}
    },

    // Lokasi & Peta
    provinceCode: '{{ old('province_code', '') }}',
    cityCode: '{{ old('city_code', '') }}',
    lokasi_acara: {{ Illuminate\Support\Js::from(old('lokasi_acara', '')) }},
    latitude: '{{ old('latitude', '') }}',
    longitude: '{{ old('longitude', '') }}',
    mapInstance: null,
    mapMarker: null,
    mapQuery: '',
    mapSearching: false,
    mapResults: [],
    mapNotice: '',

    // Booking Custom Modal State
    customModalOpen: false,
    customForm: {
      tanggal_mulai: '{{ old('tanggal_mulai', '') }}',
      tanggal_selesai: '{{ old('tanggal_selesai', '') }}',
      jam_mulai: '{{ old('jam_mulai_custom', '') }}',
      jam_selesai: '{{ old('jam_selesai_custom', '') }}',
      lokasi: {{ Illuminate\Support\Js::from(old('lokasi', '')) }},
      kebutuhan: {{ Illuminate\Support\Js::from(old('kebutuhan', '')) }},
    },
    customError: '',

    // Mini Calendar State
    calendarYear: new Date('{{ old('tanggal_sesi', $preselectedDate ?: date('Y-m-d')) }}' + 'T00:00:00').getFullYear(),
    calendarMonth: new Date('{{ old('tanggal_sesi', $preselectedDate ?: date('Y-m-d')) }}' + 'T00:00:00').getMonth(),
    monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
    dayNames: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],

    init() {
      const todayStr = this.getLocalTodayStr();
      if (this.selectedDate) {
        if (this.selectedDate < todayStr || this.isDateFullDayBlocked(this.selectedDate)) {
          this.selectedDate = '';
          this.selectedStartTime = '';
          this.selectedEndTime = '';
        }
      }

      if (this.provinceCode) {
        fetchBookingCities(this.provinceCode, this.cityCode);
      }

      this.$nextTick(() => {
        this.initLeafletMap();
        if (this.selectedDate) {
          this.fetchAvailableSlots(true);
        }
      });
    },

    getLocalTodayStr() {
      const now = new Date();
      return now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0') + '-' + String(now.getDate()).padStart(2, '0');
    },

    initLeafletMap() {
      if (typeof L === 'undefined' || !this.$refs.bookingMap) return;

      const hasInitial = this.latitude !== '' && this.longitude !== '';
      const centerLat = hasInitial ? parseFloat(this.latitude) : -6.200000;
      const centerLng = hasInitial ? parseFloat(this.longitude) : 106.816666;
      const zoomLevel = hasInitial ? 15 : 11;

      this.mapInstance = L.map(this.$refs.bookingMap).setView([centerLat, centerLng], zoomLevel);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
      }).addTo(this.mapInstance);

      if (hasInitial) {
        this.placeMarker(centerLat, centerLng, false);
      }

      this.mapInstance.on('click', (e) => {
        this.placeMarker(e.latlng.lat, e.latlng.lng, true);
      });
    },

    placeMarker(lat, lng, refreshSlots = true) {
      const fixedLat = Number(lat).toFixed(6);
      const fixedLng = Number(lng).toFixed(6);
      this.latitude = fixedLat;
      this.longitude = fixedLng;
      this.mapNotice = '';

      if (this.mapInstance) {
        if (this.mapMarker) {
          this.mapMarker.setLatLng([lat, lng]);
        } else {
          this.mapMarker = L.marker([lat, lng], { draggable: true }).addTo(this.mapInstance);
          this.mapMarker.on('dragend', () => {
            const pos = this.mapMarker.getLatLng();
            this.latitude = Number(pos.lat).toFixed(6);
            this.longitude = Number(pos.lng).toFixed(6);
            if (this.selectedDate) {
              this.fetchAvailableSlots(false);
            }
          });
        }
        this.mapInstance.setView([lat, lng], Math.max(this.mapInstance.getZoom(), 15));
      }

      if (refreshSlots && this.selectedDate) {
        this.fetchAvailableSlots(false);
      }
    },

    clearMarker() {
      this.latitude = '';
      this.longitude = '';
      this.mapResults = [];
      this.mapNotice = '';
      if (this.mapMarker && this.mapInstance) {
        this.mapInstance.removeLayer(this.mapMarker);
        this.mapMarker = null;
      }
      if (this.selectedDate) {
        this.fetchAvailableSlots(false);
      }
    },

    async searchMapLocation() {
      const q = (this.mapQuery || '').trim();
      if (q.length < 3) {
        this.mapNotice = 'Masukkan minimal 3 huruf untuk mencari lokasi di peta.';
        return;
      }
      this.mapSearching = true;
      this.mapNotice = '';
      this.mapResults = [];
      try {
        const res = await fetch('https://nominatim.openstreetmap.org/search?format=json&countrycodes=id&limit=5&q=' + encodeURIComponent(q), {
          headers: { 'Accept': 'application/json' }
        });
        const data = await res.json();
        this.mapResults = Array.isArray(data) ? data : [];
        if (this.mapResults.length === 0) {
          this.mapNotice = 'Lokasi tidak ditemukan. Coba kata kunci lain atau geser peta secara manual.';
        }
      } catch (err) {
        this.mapNotice = 'Gagal mencari lokasi. Anda tetap dapat mengetik alamat secara manual.';
      } finally {
        this.mapSearching = false;
      }
    },

    pickSearchResult(item) {
      const lat = parseFloat(item.lat);
      const lng = parseFloat(item.lon);
      this.mapResults = [];
      this.placeMarker(lat, lng, true);
      if (!this.lokasi_acara || !this.lokasi_acara.trim()) {
        this.lokasi_acara = item.display_name || '';
        this.touched.lokasi = true;
        this.validateLokasi();
      }
    },

    useCurrentLocation() {
      if (!navigator.geolocation) {
        this.mapNotice = 'Perangkat tidak mendukung deteksi lokasi otomatis.';
        return;
      }
      this.mapNotice = 'Mendeteksi titik lokasi perangkat...';
      navigator.geolocation.getCurrentPosition(
        (pos) => {
          this.placeMarker(pos.coords.latitude, pos.coords.longitude, true);
        },
        () => {
          this.mapNotice = 'Izin lokasi ditolak atau tidak tersedia.';
        },
        { enableHighAccuracy: true, timeout: 8000 }
      );
    },

    prevMonth() {
      if (this.calendarMonth === 0) {
        this.calendarMonth = 11;
        this.calendarYear--;
      } else {
        this.calendarMonth--;
      }
    },

    nextMonth() {
      if (this.calendarMonth === 11) {
        this.calendarMonth = 0;
        this.calendarYear++;
      } else {
        this.calendarMonth++;
      }
    },

    isDateFullDayBlocked(dateStr) {
      if (!dateStr) return false;
      if (Array.isArray(this.blockedDates) && this.blockedDates.includes(dateStr)) {
        return true;
      }
      const d = new Date(dateStr + 'T00:00:00');
      let dow = d.getDay();
      if (dow === 0) dow = 7;
      const wh = this.workingHours[dow];
      if (wh && !wh.is_open) return true;
      return false;
    },

    get calendarDays() {
      const year = this.calendarYear;
      const month = this.calendarMonth;
      const firstDayIndex = new Date(year, month, 1).getDay();
      const totalDays = new Date(year, month + 1, 0).getDate();
      const prevTotalDays = new Date(year, month, 0).getDate();
      const todayStr = this.getLocalTodayStr();
      const days = [];

      for (let i = firstDayIndex - 1; i >= 0; i--) {
        const dNum = prevTotalDays - i;
        const m = month === 0 ? 12 : month;
        const y = month === 0 ? year - 1 : year;
        const dateStr = `${y}-${String(m).padStart(2, '0')}-${String(dNum).padStart(2, '0')}`;
        days.push({
          dayNumber: dNum,
          dateStr: dateStr,
          isCurrentMonth: false,
          isPast: true,
          isToday: false,
          isFullDayBlocked: false
        });
      }

      for (let i = 1; i <= totalDays; i++) {
        const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
        days.push({
          dayNumber: i,
          dateStr: dateStr,
          isCurrentMonth: true,
          isPast: dateStr < todayStr,
          isToday: dateStr === todayStr,
          isFullDayBlocked: this.isDateFullDayBlocked(dateStr)
        });
      }

      const remaining = (7 - (days.length % 7)) % 7;
      for (let i = 1; i <= remaining; i++) {
        const m = month === 11 ? 1 : month + 2;
        const y = month === 11 ? year + 1 : year;
        const dateStr = `${y}-${String(m).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
        days.push({
          dayNumber: i,
          dateStr: dateStr,
          isCurrentMonth: false,
          isPast: false,
          isToday: false,
          isFullDayBlocked: false
        });
      }

      return days;
    },

    selectDay(day) {
      if (!day.isCurrentMonth || day.isPast || day.isFullDayBlocked) return;
      this.selectedDate = day.dateStr;
      this.touched.date = true;
      this.validateDate();
      this.openStartDropdown = false;
      this.openEndDropdown = false;
      this.fetchAvailableSlots(false);
    },

    async fetchAvailableSlots(preserveSelection = false) {
      if (!this.selectedDate) return;

      if (this.slotAbortCtrl) {
        this.slotAbortCtrl.abort();
      }
      this.slotAbortCtrl = new AbortController();
      this.slotLoading = true;

      try {
        const params = new URLSearchParams({
          photographer_id: '{{ $photographer->id }}',
          date: this.selectedDate
        });
        if (this.selectedServiceId) params.set('service_id', this.selectedServiceId);
        if (this.latitude !== '' && this.longitude !== '') {
          params.set('latitude', this.latitude);
          params.set('longitude', this.longitude);
        }

        const res = await fetch('{{ route('booking.available-slots') }}?' + params.toString(), {
          signal: this.slotAbortCtrl.signal,
          headers: { 'Accept': 'application/json' }
        });
        if (!res.ok) return;
        const data = await res.json();
        this.slotMatrix = data;

        const stillValidStart = (data.start_options || []).find(
          opt => opt.value === this.selectedStartTime && opt.available
        );

        if (!stillValidStart) {
          this.selectedStartTime = '';
          this.selectedEndTime = '';
        } else {
          const validEnds = (data.end_options_by_start && data.end_options_by_start[this.selectedStartTime]) || [];
          const stillValidEnd = validEnds.find(e => e.value === this.selectedEndTime);
          if (!stillValidEnd) {
            this.selectedEndTime = '';
          }
        }
      } catch (err) {
        if (err.name !== 'AbortError') {
          // Abaikan gangguan sementara jaringan
        }
      } finally {
        this.slotLoading = false;
      }
    },

    get startOptions() {
      return this.slotMatrix.start_options || [];
    },

    get validEndOptions() {
      if (!this.selectedStartTime || !this.slotMatrix.end_options_by_start) return [];
      return this.slotMatrix.end_options_by_start[this.selectedStartTime] || [];
    },

    pickStartTime(opt) {
      if (!opt.available) return;
      this.selectedStartTime = opt.value;
      this.openStartDropdown = false;

      const ends = this.validEndOptions;
      if (ends.length > 0) {
        const currentStillValid = ends.find(e => e.value === this.selectedEndTime);
        if (!currentStillValid) {
          this.selectedEndTime = ends[0].value;
        }
      } else {
        this.selectedEndTime = '';
      }

      this.touched.time = true;
      this.validateTime();
    },

    pickEndTime(opt) {
      this.selectedEndTime = opt.value;
      this.openEndDropdown = false;
      this.touched.time = true;
      this.validateTime();
    },

    get selectedDurationHours() {
      if (!this.selectedStartTime || !this.selectedEndTime) return 0;
      const sh = parseInt(this.selectedStartTime.split(':')[0], 10);
      const eh = parseInt(this.selectedEndTime.split(':')[0], 10);
      return eh > sh ? (eh - sh) : 0;
    },

    formatDateIndo(dateStr) {
      if (!dateStr) return '-';
      const parts = dateStr.split('-');
      if (parts.length !== 3) return dateStr;
      const y = parts[0];
      const mIdx = parseInt(parts[1], 10) - 1;
      const d = parseInt(parts[2], 10);
      const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
      const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
      const dt = new Date(dateStr + 'T00:00:00');
      const dayName = days[dt.getDay()] || '';
      return `${dayName}, ${d} ${months[mIdx] || ''} ${y}`;
    },

    get selectedService() {
      if (!this.selectedServiceId) return null;
      return this.allServices.find(s => s.id === this.selectedServiceId) || null;
    },
    get hourlyRate() {
      return this.selectedService ? this.selectedService.harga : 0;
    },
    get totalBill() {
      if (!this.selectedService) return 0;
      const dur = this.selectedDurationHours > 0 ? this.selectedDurationHours : 1;
      return this.hourlyRate * dur;
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

    touched: {
      service: {{ $errors->has('service_id') ? 'true' : 'false' }},
      date: {{ $errors->has('tanggal_sesi') ? 'true' : 'false' }},
      time: {{ ($errors->has('jam_mulai') || $errors->has('jam_selesai')) ? 'true' : 'false' }},
      province: {{ $errors->has('province_code') ? 'true' : 'false' }},
      city: {{ $errors->has('city_code') ? 'true' : 'false' }},
      lokasi: {{ $errors->has('lokasi_acara') ? 'true' : 'false' }},
    },
    errors: {
      service: '{{ $errors->first('service_id') ?: '' }}',
      date: '{{ $errors->first('tanggal_sesi') ?: '' }}',
      time: '{{ $errors->first('jam_mulai') ?: ($errors->first('jam_selesai') ?: '') }}',
      province: '{{ $errors->first('province_code') ?: '' }}',
      city: '{{ $errors->first('city_code') ?: '' }}',
      lokasi: '{{ $errors->first('lokasi_acara') ?: '' }}',
    },

    validateService() {
      if (!this.selectedServiceId) {
        this.errors.service = 'Pilih salah satu paket layanan pemotretan.';
        return false;
      }
      this.errors.service = '';
      return true;
    },
    validateDate() {
      if (!this.selectedDate) {
        this.errors.date = 'Pilih tanggal pelaksanaan sesi foto.';
        return false;
      }
      this.errors.date = '';
      return true;
    },
    validateTime() {
      if (!this.selectedStartTime || !this.selectedEndTime || this.selectedDurationHours < 1) {
        this.errors.time = 'Pilih jam mulai dan jam selesai sesi pemotretan.';
        return false;
      }
      this.errors.time = '';
      return true;
    },
    validateProvince() {
      if (!this.provinceCode) {
        this.errors.province = 'Pilih provinsi lokasi acara.';
        return false;
      }
      this.errors.province = '';
      return true;
    },
    validateCity() {
      if (!this.cityCode) {
        this.errors.city = 'Pilih kota atau kabupaten lokasi acara.';
        return false;
      }
      this.errors.city = '';
      return true;
    },
    validateLokasi() {
      if (!this.lokasi_acara || !this.lokasi_acara.trim()) {
        this.errors.lokasi = 'Alamat lengkap lokasi acara wajib diisi.';
        return false;
      }
      if (this.lokasi_acara.trim().length < 5) {
        this.errors.lokasi = 'Alamat lengkap minimal 5 karakter.';
        return false;
      }
      this.errors.lokasi = '';
      return true;
    },

    submitBookingForm(e) {
      this.touched.service = true;
      this.touched.date = true;
      this.touched.time = true;
      this.touched.province = true;
      this.touched.city = true;
      this.touched.lokasi = true;

      const sOk = this.validateService();
      const dOk = this.validateDate();
      const pOk = this.validateProvince();
      const cOk = this.validateCity();
      const lOk = this.validateLokasi();
      const tOk = this.validateTime();

      if (!sOk || !dOk || !pOk || !cOk || !lOk || !tOk) {
        e.preventDefault();
        let target = null;
        if (!sOk) target = this.$refs.serviceSection;
        else if (!dOk) target = this.$refs.dateSection;
        else if (!pOk) target = this.$refs.provinceBtn;
        else if (!cOk) target = this.$refs.cityBtn;
        else if (!lOk) target = this.$refs.lokasiInput;
        else if (!tOk) target = this.$refs.timeSection;

        if (target) {
          target.focus?.();
          target.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        return false;
      }
      return true;
    },

    selectService(id) {
      this.selectedServiceId = (this.selectedServiceId === id ? null : id);
      this.touched.service = true;
      this.validateService();
      if (this.selectedDate) {
        this.fetchAvailableSlots(false);
      }
    },

    openCustomModal() {
      this.customError = '';
      if (!this.customForm.tanggal_mulai && this.selectedDate) {
        this.customForm.tanggal_mulai = this.selectedDate;
      }
      if (!this.customForm.lokasi && this.lokasi_acara) {
        this.customForm.lokasi = this.lokasi_acara;
      }
      this.customModalOpen = true;
    },

    submitCustomRequest(e) {
      this.customError = '';
      if (!this.customForm.tanggal_mulai || !this.customForm.tanggal_selesai) {
        e.preventDefault();
        this.customError = 'Tanggal mulai dan tanggal selesai wajib diisi.';
        return false;
      }
      if (this.customForm.tanggal_selesai < this.customForm.tanggal_mulai) {
        e.preventDefault();
        this.customError = 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.';
        return false;
      }
      if (
        this.customForm.tanggal_mulai === this.customForm.tanggal_selesai &&
        this.customForm.jam_mulai &&
        this.customForm.jam_selesai &&
        this.customForm.jam_selesai <= this.customForm.jam_mulai
      ) {
        e.preventDefault();
        this.customError = 'Jam selesai harus lebih akhir daripada jam mulai.';
        return false;
      }
      if (!this.customForm.lokasi || !this.customForm.lokasi.trim()) {
        e.preventDefault();
        this.customError = 'Lokasi acara wajib diisi.';
        return false;
      }
      if (!this.customForm.kebutuhan || !this.customForm.kebutuhan.trim()) {
        e.preventDefault();
        this.customError = 'Rincian kebutuhan acara wajib diisi.';
        return false;
      }
      return true;
    }
  }">

    <!-- Page Header -->
    <div class="text-center space-y-2 max-w-2xl mx-auto">
      <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white tracking-tight">
        Form Reservasi Sesi Foto
      </h1>
      <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 leading-relaxed">
        Pilih paket per jam, tanggal, lokasi acara, lalu tentukan rentang jam mulai dan jam selesai sesi pemotretan Anda.
      </p>
    </div>

    <!-- Booking Form Container (2-Column Grid) -->
    <form action="{{ route('booking.store') }}" method="POST" @submit="submitBookingForm($event)" novalidate class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-start">
      @csrf
      <input type="hidden" name="photographer_id" value="{{ $photographer->id }}">
      <input type="hidden" name="service_id" :value="selectedServiceId || ''">
      <input type="hidden" name="tanggal_sesi" :value="selectedDate">
      <input type="hidden" name="jam_mulai" :value="selectedStartTime">
      <input type="hidden" name="jam_selesai" :value="selectedEndTime">
      <input type="hidden" name="latitude" :value="latitude">
      <input type="hidden" name="longitude" :value="longitude">

      <!-- Kolom Kiri (7 Kolom): Urutan Paket -> Tanggal -> Lokasi -> Jam -> Catatan -->
      <div class="lg:col-span-7 space-y-6 lg:space-y-7">

        <!-- 1. Pilih Paket Layanan (/ jam) -->
        <div x-ref="serviceSection" tabindex="-1" class="bg-white dark:bg-gray-800 p-6 sm:p-7 rounded-2xl border border-gray-200/80 dark:border-gray-700/80 shadow-xs space-y-5 outline-none">
          <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-500 flex items-center justify-center shrink-0">
                <svg class="w-4.5 h-4.5 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
              </div>
              <h2 class="text-sm sm:text-base font-black text-gray-900 dark:text-white">1. Pilih Paket Layanan</h2>
            </div>
          </div>

          <div class="space-y-3">
            @forelse($allServices as $pkg)
              <div @click="selectService({{ $pkg->id }})"
                   class="p-4 sm:p-5 rounded-2xl border transition cursor-pointer select-none space-y-3"
                   :class="selectedServiceId === {{ $pkg->id }}
                            ? 'border-amber-400 bg-amber-50/40 dark:bg-amber-950/20 ring-2 ring-amber-400/40 shadow-xs'
                            : 'border-gray-200 dark:border-gray-700/80 bg-white dark:bg-gray-800/80 hover:border-gray-300 dark:hover:border-gray-600'">
                <div class="flex items-start justify-between gap-3">
                  <div class="flex items-start gap-3 flex-1 min-w-0">
                    <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center shrink-0 mt-0.5"
                         :class="selectedServiceId === {{ $pkg->id }} ? 'border-amber-500 bg-white dark:bg-gray-800' : 'border-gray-300 dark:border-gray-600'">
                      <span class="w-2 h-2 rounded-full bg-amber-500" x-show="selectedServiceId === {{ $pkg->id }}"></span>
                    </div>
                    <div class="flex-1 min-w-0">
                      <h3 class="text-xs sm:text-sm font-black text-gray-900 dark:text-white leading-snug">{{ $pkg->nama_layanan }}</h3>
                      <p class="text-[11px] text-gray-500 dark:text-gray-400 leading-relaxed mt-0.5 line-clamp-2">{{ $pkg->deskripsi_layanan }}</p>
                      @if($pkg->spare_summary)
                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1.5">
                          Jeda persiapan: {{ $pkg->spare_summary }}{{ $pkg->allow_same_location_bypass ? ' · Bebas jeda untuk lokasi sama' : '' }}
                        </p>
                      @endif
                    </div>
                  </div>
                  <div class="text-right shrink-0">
                    <span class="text-xs sm:text-sm font-black text-amber-600 dark:text-amber-400">Rp {{ number_format($pkg->tarif_harga, 0, ',', '.') }}</span>
                    <span class="block text-[10px] font-bold text-gray-400 dark:text-gray-500">/ jam</span>
                  </div>
                </div>

                @if($pkg->details->count() > 0)
                  <div class="pt-1 pl-7 grid grid-cols-1 sm:grid-cols-2 gap-1.5 text-[11px] text-gray-600 dark:text-gray-300">
                    @foreach($pkg->details->take(4) as $det)
                      <div class="flex items-center gap-1.5 min-w-0">
                        <svg class="w-3.5 h-3.5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        <span class="truncate">{{ $det->nama_fitur }}</span>
                      </div>
                    @endforeach
                    @if($pkg->details->count() > 4)
                      <span class="text-[10px] text-gray-400 font-bold self-center">+{{ $pkg->details->count() - 4 }} benefit lainnya</span>
                    @endif
                  </div>
                @endif
              </div>
            @empty
              <div class="p-6 text-center text-xs text-gray-500 rounded-2xl bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700">
                Fotografer belum menambahkan paket layanan pemotretan.
              </div>
            @endforelse
          </div>
          <p x-show="touched.service && errors.service" x-text="errors.service" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>

          <!-- Opsi Booking Custom Multi-Hari / Jam Khusus -->
          <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-900/60 border border-gray-200/80 dark:border-gray-700/70 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="space-y-0.5">
              <p class="text-xs font-bold text-gray-900 dark:text-white">Acara Lebih dari 1 Hari atau Jam Khusus</p>
              <p class="text-[11px] text-gray-500 dark:text-gray-400 leading-relaxed">
                Untuk pemotretan berurutan beberapa hari atau di luar jam reguler, ajukan lewat obrolan dengan fotografer.
              </p>
            </div>
            <button type="button"
                    @click="openCustomModal()"
                    class="px-4 py-2.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-900 dark:bg-amber-950/50 dark:text-amber-300 border border-amber-200/80 dark:border-amber-900/60 font-bold text-xs transition shrink-0 cursor-pointer">
              Booking Custom
            </button>
          </div>
        </div>

        <!-- 2. Pilih Tanggal Sesi Foto -->
        <div x-ref="dateSection" tabindex="-1" class="bg-white dark:bg-gray-800 p-6 sm:p-7 rounded-2xl border border-gray-200/80 dark:border-gray-700/80 shadow-xs space-y-5 outline-none">
          <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-500 flex items-center justify-center shrink-0">
                <svg class="w-4.5 h-4.5 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
              </div>
              <h2 class="text-sm sm:text-base font-black text-gray-900 dark:text-white">2. Pilih Tanggal Sesi</h2>
            </div>
            <span x-show="selectedDate" x-cloak class="text-xs font-bold text-gray-700 dark:text-gray-300" x-text="formatDateIndo(selectedDate)"></span>
          </div>

          <div class="p-4 sm:p-5 rounded-2xl bg-gray-50/70 dark:bg-gray-900/40 border border-gray-200/80 dark:border-gray-700/60 space-y-3.5">
            <div class="flex items-center justify-between">
              <h3 class="text-xs sm:text-sm font-black text-gray-900 dark:text-white tracking-tight"
                  x-text="monthNames[calendarMonth] + ' ' + calendarYear">
              </h3>
              <div class="flex items-center gap-1">
                <button type="button"
                        @click="prevMonth()"
                        class="w-7 h-7 rounded-lg border border-gray-200 dark:border-gray-700 flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-white dark:hover:bg-gray-800 transition cursor-pointer">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                </button>
                <button type="button"
                        @click="nextMonth()"
                        class="w-7 h-7 rounded-lg border border-gray-200 dark:border-gray-700 flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-white dark:hover:bg-gray-800 transition cursor-pointer">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                </button>
              </div>
            </div>

            <div class="grid grid-cols-7 gap-1 text-center text-[10px] font-black uppercase tracking-wider text-gray-400 dark:text-gray-500">
              <template x-for="dname in dayNames" :key="dname">
                <div class="py-0.5" x-text="dname"></div>
              </template>
            </div>

            <div class="grid grid-cols-7 gap-1 sm:gap-1.5">
              <template x-for="(day, idx) in calendarDays" :key="idx">
                <div @click="selectDay(day)"
                     class="min-h-[46px] p-1.5 rounded-xl border text-center transition flex flex-col items-center justify-center select-none"
                     :class="{
                       'opacity-20 border-transparent cursor-default': !day.isCurrentMonth,
                       'bg-gray-100/60 dark:bg-gray-900/60 border-gray-200/60 dark:border-gray-800 text-gray-400 dark:text-gray-600 cursor-not-allowed': day.isCurrentMonth && day.isPast,
                       'bg-rose-50/50 dark:bg-rose-950/20 border-rose-200/50 dark:border-rose-900/40 text-rose-500 cursor-not-allowed': day.isCurrentMonth && !day.isPast && day.isFullDayBlocked,
                       'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 hover:border-amber-400 cursor-pointer shadow-2xs': day.isCurrentMonth && !day.isPast && !day.isFullDayBlocked && selectedDate !== day.dateStr,
                       'border-amber-400 bg-amber-400 text-gray-950 font-black shadow-xs ring-2 ring-amber-400/30': day.isCurrentMonth && !day.isPast && !day.isFullDayBlocked && selectedDate === day.dateStr
                     }">
                  <span class="text-xs font-black leading-none" x-text="day.dayNumber"></span>
                  <template x-if="day.isCurrentMonth && day.isToday && selectedDate !== day.dateStr">
                    <span class="text-[9px] font-extrabold text-amber-600 dark:text-amber-400 mt-0.5">Hari Ini</span>
                  </template>
                  <template x-if="day.isCurrentMonth && !day.isPast && day.isFullDayBlocked">
                    <span class="text-[9px] font-bold text-rose-500 dark:text-rose-400 mt-0.5">Tutup</span>
                  </template>
                </div>
              </template>
            </div>
          </div>
          <p x-show="touched.date && errors.date" x-text="errors.date" x-cloak class="text-[11px] font-bold text-red-500"></p>
        </div>

        <!-- 3. Lokasi Acara (Wilayah + Peta Pin Opsional + Alamat) -->
        <div class="bg-white dark:bg-gray-800 p-6 sm:p-7 rounded-2xl border border-gray-200/80 dark:border-gray-700/80 shadow-xs space-y-5">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-500 flex items-center justify-center shrink-0">
              <svg class="w-4.5 h-4.5 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            </div>
            <h2 class="text-sm sm:text-base font-black text-gray-900 dark:text-white">3. Lokasi Acara</h2>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <!-- Province Custom Alpine Dropdown -->
            <div x-data="{ open: false, selectedCode: '{{ old('province_code', '') }}', selectedText: '{{ old('province_code') ? ($provinces->firstWhere('code', old('province_code'))?->name ?? 'Pilih Provinsi') : 'Pilih Provinsi' }}' }">
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Provinsi <span class="text-rose-500">*</span></label>
              <input type="hidden" name="province_code" :value="selectedCode">

              <div class="relative">
                <button type="button"
                        x-ref="provinceBtn"
                        @click="open = !open"
                        class="w-full px-4 py-2.5 text-xs font-medium text-left text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 rounded-xl border focus:outline-none focus:ring-2 flex items-center justify-between transition cursor-pointer"
                        :class="touched.province && errors.province ? 'border-red-400 focus:ring-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-400'">
                  <span class="truncate" x-text="selectedText"></span>
                  <svg class="w-4 h-4 text-gray-400 shrink-0 ml-2 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                </button>

                <div x-show="open" @click.away="open = false" x-transition style="display: none;" class="absolute z-50 w-full mt-1.5 max-h-60 overflow-y-auto bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl py-1">
                  @foreach($provinces as $prov)
                    <div @click="selectedCode = '{{ $prov->code }}'; selectedText = '{{ addslashes($prov->name) }}'; open = false; provinceCode = '{{ $prov->code }}'; touched.province = true; validateProvince(); fetchBookingCities('{{ $prov->code }}');" class="px-4 py-2.5 text-xs text-gray-800 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition cursor-pointer">
                      {{ $prov->name }}
                    </div>
                  @endforeach
                </div>
              </div>
              <p x-show="touched.province && errors.province" x-text="errors.province" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
            </div>

            <!-- City Custom Alpine Dropdown -->
            <div x-data="{ open: false, selectedCode: '{{ old('city_code', '') }}', selectedText: 'Pilih Kota / Kabupaten' }"
                 @booking-city-set.window="selectedCode = $event.detail.code; selectedText = $event.detail.name">
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Kota / Kabupaten <span class="text-rose-500">*</span></label>
              <input type="hidden" name="city_code" :value="selectedCode" id="bookingCityInput" @change="cityCode = $event.target.value; selectedCode = $event.target.value; touched.city = true; validateCity()">

              <div class="relative">
                <button type="button"
                        x-ref="cityBtn"
                        @click="open = !open"
                        class="w-full px-4 py-2.5 text-xs font-medium text-left text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 rounded-xl border focus:outline-none focus:ring-2 flex items-center justify-between transition cursor-pointer"
                        :class="touched.city && errors.city ? 'border-red-400 focus:ring-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-400'">
                  <span class="truncate" x-text="selectedText" id="bookingCityLabel"></span>
                  <svg class="w-4 h-4 text-gray-400 shrink-0 ml-2 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                </button>

                <div x-show="open" @click.away="open = false" x-transition style="display: none;" class="absolute z-50 w-full mt-1.5 max-h-60 overflow-y-auto bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl py-1" id="bookingCityOptionsList">
                  <div class="px-4 py-2.5 text-xs text-gray-400 font-semibold">Pilih provinsi terlebih dahulu</div>
                </div>
              </div>
              <p x-show="touched.city && errors.city" x-text="errors.city" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
            </div>
          </div>

          <!-- Peta Interaktif Titik Lokasi (Opsional, Mendukung Deteksi Satu Lokasi <= 500 m) -->
          <div class="space-y-3">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
              <label class="text-xs font-bold text-gray-700 dark:text-gray-300">
                Pin Titik Lokasi di Peta (Opsional)
              </label>
              <div class="flex items-center gap-3">
                <button type="button"
                        @click="useCurrentLocation()"
                        class="text-[11px] font-bold text-amber-600 dark:text-amber-400 hover:underline cursor-pointer">
                  Gunakan Lokasi Saat Ini
                </button>
                <button type="button"
                        x-show="latitude !== '' && longitude !== ''"
                        x-cloak
                        @click="clearMarker()"
                        class="text-[11px] font-bold text-gray-400 hover:text-rose-500 cursor-pointer">
                  Hapus Pin
                </button>
              </div>
            </div>

            <!-- Search Bar Peta (Icon Kaca Pembesar Wajib di Kanan) -->
            <div class="flex items-center gap-2">
              <div class="relative flex-1">
                <input type="text"
                       x-model="mapQuery"
                       @keydown.enter.prevent="searchMapLocation()"
                       placeholder="Cari gedung, kampus, hotel, atau area acara..."
                       class="w-full pl-4 pr-11 py-2.5 text-xs font-medium text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-amber-400 focus:outline-none transition">
                <svg class="w-4 h-4 text-gray-400 absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
              </div>
              <button type="button"
                      @click="searchMapLocation()"
                      :disabled="mapSearching"
                      class="px-4 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-xs font-bold text-gray-800 dark:text-white transition shrink-0 cursor-pointer disabled:opacity-50">
                <span x-text="mapSearching ? 'Mencari...' : 'Cari'"></span>
              </button>
            </div>

            <!-- Hasil Pencarian Peta -->
            <div x-show="mapResults.length > 0" x-cloak class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 divide-y divide-gray-100 dark:divide-gray-800 max-h-48 overflow-y-auto">
              <template x-for="(item, rIdx) in mapResults" :key="rIdx">
                <button type="button"
                        @click="pickSearchResult(item)"
                        class="w-full text-left px-4 py-2.5 text-xs text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-700 transition cursor-pointer">
                  <span class="block font-medium line-clamp-2" x-text="item.display_name"></span>
                </button>
              </template>
            </div>

            <p x-show="mapNotice" x-text="mapNotice" x-cloak class="text-[11px] text-amber-600 dark:text-amber-400 font-medium"></p>

            <!-- Container Peta -->
            <div x-ref="bookingMap" class="w-full h-56 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden bg-gray-100 dark:bg-gray-900"></div>

            <p class="text-[11px] text-gray-500 dark:text-gray-400">
              <template x-if="latitude !== '' && longitude !== ''">
                <span>Titik pin terpilih: <strong class="text-gray-900 dark:text-white font-mono" x-text="latitude + ', ' + longitude"></strong>. Jeda persiapan dapat disesuaikan jika berdekatan dalam radius 500 meter pada paket yang mengizinkan.</span>
              </template>
              <template x-if="latitude === '' || longitude === ''">
                <span>Klik peta atau cari lokasi untuk menandai titik acara. Jika tidak ditandai, jadwal tetap dapat dipesan dengan perhitungan jeda penuh.</span>
              </template>
            </p>
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Alamat Lengkap / Nama Venue <span class="text-rose-500">*</span></label>
            <textarea name="lokasi_acara"
                    x-ref="lokasiInput"
                    x-model="lokasi_acara"
                    @input="touched.lokasi = true; validateLokasi()"
                    @blur="touched.lokasi = true; validateLokasi()"
                    rows="4"
                    placeholder="Masukkan nama venue, gedung, jalan, serta patokan lokasi acara"
                    :class="touched.lokasi && errors.lokasi ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-400 focus:border-amber-400'"
                    class="w-full px-4 py-3 text-xs font-medium text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 rounded-xl border focus:ring-2 focus:outline-none transition leading-relaxed"></textarea>
            <p x-show="touched.lokasi && errors.lokasi" x-text="errors.lokasi" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
          </div>
        </div>

        <!-- 4. Pilih Jam Mulai & Jam Selesai (2 Dropdown Terpisah Kelipatan 1 Jam) -->
        <div x-ref="timeSection" tabindex="-1" class="bg-white dark:bg-gray-800 p-6 sm:p-7 rounded-2xl border border-gray-200/80 dark:border-gray-700/80 shadow-xs space-y-5 outline-none">
          <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-500 flex items-center justify-center shrink-0">
                <svg class="w-4.5 h-4.5 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              </div>
              <h2 class="text-sm sm:text-base font-black text-gray-900 dark:text-white">4. Pilih Jam Mulai dan Jam Selesai</h2>
            </div>
            <span x-show="slotLoading" x-cloak class="text-[11px] font-bold text-amber-600 dark:text-amber-400">Memeriksa slot...</span>
          </div>

          <template x-if="!selectedDate">
            <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-900/60 border border-dashed border-gray-200 dark:border-gray-700 text-center text-xs text-gray-500 dark:text-gray-400">
              Pilih tanggal sesi terlebih dahulu pada langkah 2 untuk melihat daftar jam yang tersedia.
            </div>
          </template>

          <template x-if="selectedDate && !slotLoading && (!slotMatrix.is_open || !slotMatrix.has_available_slots)">
            <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-900/60 border border-dashed border-gray-200 dark:border-gray-700 text-center text-xs text-gray-600 dark:text-gray-400" x-text="slotMatrix.reason || 'Tidak ada jam tersedia pada tanggal ini.'">
            </div>
          </template>

          <template x-if="selectedDate && (slotLoading || (slotMatrix.is_open && slotMatrix.has_available_slots))">
            <div class="space-y-4">
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Dropdown 1: Jam Mulai -->
                <div class="relative" @click.away="openStartDropdown = false">
                  <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Jam Mulai <span class="text-rose-500">*</span></label>
                  <button type="button"
                          @click="openStartDropdown = !openStartDropdown; openEndDropdown = false"
                          class="w-full px-4 py-2.5 text-xs font-bold text-left bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-400 flex items-center justify-between transition cursor-pointer"
                          :class="selectedStartTime ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-500 font-medium'">
                    <span x-text="selectedStartTime ? selectedStartTime + ' WIB' : 'Pilih Jam Mulai'"></span>
                    <svg class="w-4 h-4 text-gray-400 shrink-0 transition-transform" :class="openStartDropdown ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                  </button>

                  <div x-show="openStartDropdown" x-transition style="display: none;" class="absolute z-50 w-full mt-1.5 max-h-60 overflow-y-auto bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl py-1">
                    <template x-for="opt in startOptions" :key="opt.value">
                      <button type="button"
                              @click="pickStartTime(opt)"
                              :disabled="!opt.available"
                              :class="{
                                'bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 font-extrabold': selectedStartTime === opt.value,
                                'text-gray-800 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 cursor-pointer': opt.available && selectedStartTime !== opt.value,
                                'text-gray-400 dark:text-gray-600 cursor-not-allowed opacity-60': !opt.available
                              }"
                              class="w-full px-4 py-2.5 text-xs text-left flex items-center justify-between transition">
                        <span x-text="opt.label"></span>
                        <span x-show="!opt.available" class="text-[11px] font-medium text-gray-400">Penuh / Lewat</span>
                      </button>
                    </template>
                  </div>
                </div>

                <!-- Dropdown 2: Jam Selesai -->
                <div class="relative" @click.away="openEndDropdown = false">
                  <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Jam Selesai <span class="text-rose-500">*</span></label>
                  <button type="button"
                          :disabled="!selectedStartTime || validEndOptions.length === 0"
                          @click="if (selectedStartTime && validEndOptions.length > 0) { openEndDropdown = !openEndDropdown; openStartDropdown = false; }"
                          class="w-full px-4 py-2.5 text-xs font-bold text-left bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-amber-400 flex items-center justify-between transition"
                          :class="[
                            (!selectedStartTime || validEndOptions.length === 0) ? 'opacity-60 cursor-not-allowed' : 'hover:border-amber-400 cursor-pointer',
                            selectedEndTime ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-500 font-medium'
                          ]">
                    <span x-text="selectedEndTime ? (selectedEndTime + ' WIB (' + selectedDurationHours + ' Jam)') : 'Pilih Jam Selesai'"></span>
                    <svg class="w-4 h-4 text-gray-400 shrink-0 transition-transform" :class="openEndDropdown ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                  </button>

                  <div x-show="openEndDropdown" x-transition style="display: none;" class="absolute z-50 w-full mt-1.5 max-h-60 overflow-y-auto bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl py-1">
                    <template x-for="opt in validEndOptions" :key="opt.value">
                      <button type="button"
                              @click="pickEndTime(opt)"
                              :class="selectedEndTime === opt.value ? 'bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 font-extrabold' : 'text-gray-800 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600'"
                              class="w-full px-4 py-2.5 text-xs text-left flex items-center justify-between transition cursor-pointer">
                        <span x-text="opt.label"></span>
                      </button>
                    </template>
                  </div>
                </div>
              </div>

              <p x-show="selectedStartTime && selectedEndTime && selectedDurationHours > 0" x-cloak class="text-xs text-gray-600 dark:text-gray-300">
                Durasi pemotretan: <strong class="font-black text-gray-900 dark:text-white" x-text="selectedStartTime + ' - ' + selectedEndTime + ' WIB (' + selectedDurationHours + ' Jam)'"></strong>
              </p>
            </div>
          </template>

          <p x-show="touched.time && errors.time" x-text="errors.time" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
        </div>

        <!-- 5. Catatan Khusus -->
        <div class="bg-white dark:bg-gray-800 p-6 sm:p-7 rounded-2xl border border-gray-200/80 dark:border-gray-700/80 shadow-xs space-y-4">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-500 flex items-center justify-center shrink-0">
              <svg class="w-4.5 h-4.5 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            </div>
            <h2 class="text-sm sm:text-base font-black text-gray-900 dark:text-white">5. Catatan Khusus</h2>
          </div>

          <textarea name="catatan_khusus" rows="4" placeholder="Masukkan catatan konsep, jumlah peserta, atau instruksi khusus untuk fotografer" class="w-full px-4 py-3 text-xs font-medium text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 focus:outline-none transition leading-relaxed">{{ old('catatan_khusus', '') }}</textarea>
        </div>

      </div>

      <!-- Kolom Kanan (5 Kolom): Ringkasan Pesanan -->
      <div class="lg:col-span-5 space-y-6 sticky top-28">
        <div class="bg-white dark:bg-gray-800 p-6 sm:p-7 rounded-2xl border border-gray-200/80 dark:border-gray-700/80 shadow-sm space-y-6">
          <h2 class="text-sm font-black text-gray-900 dark:text-white">Ringkasan Pesanan</h2>

          <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-900/60 border border-gray-100 dark:border-gray-700/60">
            <x-user-avatar :user="$photographer->user" size="w-10 h-10" />
            <div class="flex-1 min-w-0">
              <h3 class="text-xs font-black text-gray-900 dark:text-white leading-snug truncate">{{ $photographer->nama }}</h3>
              <p class="text-[11px] text-gray-400 flex items-center gap-1.5 mt-0.5">
                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                <span class="truncate">{{ $photographer->city->name ?? ($photographer->alamat ?: '-') }}</span>
              </p>
            </div>
          </div>

          <div x-show="!selectedServiceId" class="p-5 rounded-2xl bg-gray-50/70 dark:bg-gray-900/40 border border-dashed border-gray-200 dark:border-gray-700 text-center space-y-2">
            <h4 class="text-xs font-black text-gray-900 dark:text-white">Pilih Paket Layanan</h4>
            <p class="text-[11px] text-gray-500 dark:text-gray-400 max-w-xs mx-auto leading-relaxed">Silakan tentukan paket layanan pada langkah 1 di sebelah kiri.</p>
          </div>

          <div x-show="selectedServiceId" x-transition class="p-4 rounded-xl bg-amber-50/70 dark:bg-amber-950/40 border border-amber-200/80 dark:border-amber-800/60 space-y-1.5">
            <div class="flex items-center justify-between gap-2">
              <span class="text-[10px] font-black uppercase tracking-wider text-amber-800 dark:text-amber-300">Paket Terpilih</span>
              <span class="text-xs font-black text-amber-600 dark:text-amber-400" x-text="'Rp ' + hourlyRate.toLocaleString('id-ID') + ' / jam'"></span>
            </div>
            <h4 class="text-xs font-black text-gray-900 dark:text-white" x-text="selectedService ? selectedService.nama : ''"></h4>
            <p class="text-[11px] text-gray-600 dark:text-gray-300 leading-relaxed line-clamp-2" x-text="selectedService ? selectedService.deskripsi : ''"></p>
          </div>

          <div x-show="selectedDate" x-cloak class="p-3.5 rounded-xl bg-gray-50 dark:bg-gray-900/60 border border-gray-100 dark:border-gray-700/60 space-y-1">
            <div class="flex items-center justify-between text-xs gap-2">
              <span class="text-gray-500 dark:text-gray-400">Jadwal Sesi:</span>
              <span class="font-bold text-amber-600 dark:text-amber-400"
                    x-text="(selectedStartTime && selectedEndTime) ? (selectedStartTime + ' - ' + selectedEndTime + ' WIB') : 'Pilih jam sesi'"></span>
            </div>
            <p class="text-[11px] text-gray-700 dark:text-gray-300 font-medium truncate" x-text="formatDateIndo(selectedDate)"></p>
          </div>

          <!-- Skema Pembayaran -->
          <div class="space-y-2.5">
            <span class="text-[11px] font-black text-gray-800 dark:text-gray-200 uppercase tracking-wider block">Skema Pembayaran</span>
            <input type="hidden" name="payment_type" :value="paymentType">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
              <label @click="paymentType = 'full'"
                     :class="paymentType === 'full' ? 'border-amber-400 bg-amber-50/70 dark:bg-amber-950/40 text-amber-950 dark:text-amber-100 shadow-2xs' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 hover:border-gray-300'"
                     class="p-3.5 rounded-xl border transition cursor-pointer select-none space-y-1">
                <input type="radio" class="sr-only peer" value="full" x-model="paymentType">
                <div class="flex items-center justify-between">
                  <span class="text-xs font-black">Bayar Penuh</span>
                  <span class="w-3.5 h-3.5 rounded-full border-2 flex items-center justify-center shrink-0" :class="paymentType === 'full' ? 'border-amber-500 bg-white dark:bg-gray-800' : 'border-gray-300 dark:border-gray-600'">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500" x-show="paymentType === 'full'"></span>
                  </span>
                </div>
                <p class="text-[11px] text-gray-500 dark:text-gray-400">Pelunasan 100% langsung</p>
                <p class="text-xs font-black text-gray-900 dark:text-white pt-1" x-text="'Rp ' + totalBill.toLocaleString('id-ID')"></p>
              </label>

              <label @click="paymentType = 'dp'"
                     :class="paymentType === 'dp' ? 'border-amber-400 bg-amber-50/70 dark:bg-amber-950/40 text-amber-950 dark:text-amber-100 shadow-2xs' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 hover:border-gray-300'"
                     class="p-3.5 rounded-xl border transition cursor-pointer select-none space-y-1">
                <input type="radio" class="sr-only peer" value="dp" x-model="paymentType">
                <div class="flex items-center justify-between">
                  <span class="text-xs font-black">Uang Muka DP 30%</span>
                  <span class="w-3.5 h-3.5 rounded-full border-2 flex items-center justify-center shrink-0" :class="paymentType === 'dp' ? 'border-amber-500 bg-white dark:bg-gray-800' : 'border-gray-300 dark:border-gray-600'">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500" x-show="paymentType === 'dp'"></span>
                  </span>
                </div>
                <p class="text-[11px] text-gray-500 dark:text-gray-400">Sisa 70% dilunasi kemudian</p>
                <p class="text-xs font-black text-amber-600 dark:text-amber-400 pt-1" x-text="'Rp ' + dpAmount.toLocaleString('id-ID')"></p>
              </label>
            </div>
          </div>

          <!-- Kalkulasi Biaya -->
          <div class="space-y-3 pt-1">
            <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 font-medium">
              <span>Tarif Per Jam</span>
              <span class="font-bold text-gray-900 dark:text-white" x-text="'Rp ' + hourlyRate.toLocaleString('id-ID')">Rp 0</span>
            </div>

            <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 font-medium">
              <span>Durasi Sesi</span>
              <span class="font-bold text-gray-900 dark:text-white" x-text="selectedDurationHours > 0 ? selectedDurationHours + ' Jam' : '-'">-</span>
            </div>

            <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 font-medium">
              <span>Total Biaya Layanan</span>
              <span class="font-bold text-gray-900 dark:text-white" x-text="'Rp ' + totalBill.toLocaleString('id-ID')">Rp 0</span>
            </div>

            <template x-if="paymentType === 'dp' && selectedServiceId">
              <div class="space-y-2.5">
                <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 font-medium">
                  <span>Tagihan DP (30%) Sekarang</span>
                  <span class="font-bold text-gray-900 dark:text-white" x-text="'Rp ' + dpAmount.toLocaleString('id-ID')"></span>
                </div>
                <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 font-medium">
                  <span>Sisa Pelunasan (70%)</span>
                  <span class="font-bold text-gray-800 dark:text-gray-200" x-text="'Rp ' + remainingAmount.toLocaleString('id-ID')"></span>
                </div>
              </div>
            </template>

            <div class="flex justify-between items-center text-gray-900 dark:text-white pt-2">
              <span class="text-xs font-bold">Total Dibayar Sekarang</span>
              <span class="text-lg font-black text-amber-600 dark:text-amber-400" x-text="'Rp ' + currentPayAmount.toLocaleString('id-ID')">Rp 0</span>
            </div>
          </div>

          <!-- Tombol Lanjut Pembayaran -->
          <div class="pt-2">
            <button type="submit"
                    :disabled="!selectedServiceId || !selectedDate || !selectedStartTime || !selectedEndTime || selectedDurationHours < 1"
                    :class="(!selectedServiceId || !selectedDate || !selectedStartTime || !selectedEndTime || selectedDurationHours < 1) ? 'opacity-50 cursor-not-allowed bg-gray-300 dark:bg-gray-700 text-gray-500' : 'bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold shadow-md cursor-pointer'"
                    class="w-full py-3.5 text-xs rounded-xl transition text-center block">
              <span x-show="!selectedServiceId">Pilih Paket Layanan Terlebih Dahulu</span>
              <span x-show="selectedServiceId && !selectedDate" style="display: none;">Pilih Tanggal Pemotretan</span>
              <span x-show="selectedServiceId && selectedDate && (!selectedStartTime || !selectedEndTime)" style="display: none;">Pilih Jam Mulai dan Jam Selesai</span>
              <span x-show="selectedServiceId && selectedDate && selectedStartTime && selectedEndTime" style="display: none;">Lanjut ke Invoice Pembayaran</span>
            </button>
          </div>
        </div>
      </div>
    </form>

    <!-- Modal Booking Custom (Rule #59: Centered Title Without Icon, Centered 2 Action Buttons, No Top-Right X) -->
    <div x-show="customModalOpen"
         x-cloak
         @keydown.escape.window="customModalOpen = false"
         class="fixed inset-0 z-[9990] flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
      <div @click.away="customModalOpen = false"
           class="bg-white dark:bg-gray-800 max-w-lg w-full p-6 sm:p-7 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-2xl space-y-5 overflow-visible">
        <div class="text-center space-y-1.5">
          <h3 class="text-base sm:text-lg font-black text-gray-900 dark:text-white tracking-tight">Ajukan Booking Custom</h3>
          <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
            Kirim rencana jadwal multi-hari berurutan atau jam khusus ke obrolan fotografer untuk disepakati bersama.
          </p>
        </div>

        <form action="{{ route('booking.custom-request') }}" method="POST" @submit="submitCustomRequest($event)" data-no-confirm="true" novalidate class="space-y-4">
          @csrf
          <input type="hidden" name="photographer_id" value="{{ $photographer->id }}">

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Tanggal Mulai <span class="text-rose-500">*</span></label>
              <input type="date" name="tanggal_mulai" min="{{ date('Y-m-d') }}" x-model="customForm.tanggal_mulai"
                     class="w-full px-3.5 py-2.5 text-xs font-bold text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-amber-400 focus:outline-none">
            </div>
            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Tanggal Selesai <span class="text-rose-500">*</span></label>
              <input type="date" name="tanggal_selesai" :min="customForm.tanggal_mulai || '{{ date('Y-m-d') }}'" x-model="customForm.tanggal_selesai"
                     class="w-full px-3.5 py-2.5 text-xs font-bold text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-amber-400 focus:outline-none">
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Perkiraan Jam Mulai</label>
              <x-time-dropdown
                model="customForm.jam_mulai"
                name="jam_mulai"
                placeholder="Opsional"
                align="left"
              />
            </div>
            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Perkiraan Jam Selesai</label>
              <x-time-dropdown
                model="customForm.jam_selesai"
                name="jam_selesai"
                placeholder="Opsional"
                align="md-right"
              />
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Lokasi Acara <span class="text-rose-500">*</span></label>
            <input type="text" name="lokasi" x-model="customForm.lokasi" placeholder="Masukkan kota atau alamat venue acara"
                   class="w-full px-4 py-2.5 text-xs font-medium text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-amber-400 focus:outline-none">
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Kebutuhan Acara <span class="text-rose-500">*</span></label>
            <textarea name="kebutuhan" rows="4" x-model="customForm.kebutuhan" placeholder="Jelaskan jenis acara, susunan hari, atau kebutuhan khusus lainnya"
                      class="w-full px-4 py-3 text-xs font-medium text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-amber-400 focus:outline-none leading-relaxed"></textarea>
          </div>

          <p x-show="customError" x-text="customError" x-cloak class="text-[11px] font-bold text-red-500 text-center"></p>

          <div class="flex items-center justify-center gap-3 pt-1">
            <button type="button"
                    @click="customModalOpen = false"
                    class="py-2.5 px-5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 hover:bg-gray-100 dark:bg-gray-700 text-xs font-bold text-gray-700 dark:text-gray-300 transition cursor-pointer">
              Batal
            </button>
            <button type="submit"
                    class="py-2.5 px-6 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs shadow-xs transition cursor-pointer">
              Kirim ke Chat
            </button>
          </div>
        </form>
      </div>
    </div>

  </div>
</section>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
  function fetchBookingCities(provinceCode, preselectCityCode = '') {
    const cityInput = document.getElementById('bookingCityInput');
    const cityList = document.getElementById('bookingCityOptionsList');

    if (!preselectCityCode) {
      cityInput.value = '';
      cityInput.dispatchEvent(new Event('change'));
      window.dispatchEvent(new CustomEvent('booking-city-set', { detail: { code: '', name: 'Memuat Kota...' } }));
    }
    cityList.innerHTML = '<div class="px-4 py-2.5 text-xs text-gray-400 font-semibold">Memuat data kota...</div>';

    fetch('/api/cities/' + provinceCode)
      .then(res => res.json())
      .then(data => {
        if (!preselectCityCode) {
          window.dispatchEvent(new CustomEvent('booking-city-set', { detail: { code: '', name: 'Pilih Kota / Kabupaten' } }));
        }
        let html = '';
        let matchedCity = null;
        data.forEach(city => {
          if (preselectCityCode && String(city.code) === String(preselectCityCode)) {
            matchedCity = city;
          }
          const escapedName = city.name.replace(/'/g, "\\'");
          html += `
            <div onclick="selectBookingCity('${city.code}', '${escapedName}')" class="px-4 py-2.5 text-xs text-gray-800 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition cursor-pointer">
              ${city.name}
            </div>
          `;
        });
        cityList.innerHTML = html;

        if (matchedCity) {
          selectBookingCity(matchedCity.code, matchedCity.name);
        }
      });
  }

  function selectBookingCity(code, name) {
    const input = document.getElementById('bookingCityInput');
    input.value = code;
    input.dispatchEvent(new Event('change'));
    window.dispatchEvent(new CustomEvent('booking-city-set', { detail: { code: code, name: name } }));
  }
</script>
@endsection
