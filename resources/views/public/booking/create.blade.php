@extends('layouts.app')

@section('title', 'Form Reservasi Sesi Foto — LensMatch')

@section('content')
<section class="py-10 sm:py-14 lg:py-16 bg-white dark:bg-gray-900 min-h-screen">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 sm:space-y-10" x-data="{ 
    allServices: {{ Illuminate\Support\Js::from($allServices->map(function($s) {
        return [
            'id' => $s->id,
            'nama' => $s->nama_layanan,
            'harga' => (int) $s->tarif_harga,
            'deskripsi' => $s->deskripsi_layanan,
            'details' => $s->details->map(function($d) {
                return [
                    'id' => $d->id,
                    'nama' => $d->nama_fitur,
                    'harga' => (int) $d->tarif_harga,
                ];
            }),
        ];
    })) }},
    selectedServiceId: {{ $service ? $service->id : 'null' }},
    paymentType: 'full',
    sessionDuration: 2,
    selectedDate: '{{ old('tanggal_sesi', $preselectedDate ?? date('Y-m-d', strtotime('+2 days'))) }}',
    selectedTime: '{{ old('jam_mulai', '14:00') }}',
    workingHours: {{ Illuminate\Support\Js::from($workingHours ?? []) }},
    availabilities: {{ Illuminate\Support\Js::from($availabilities ?? []) }},
    bookedSlots: {{ Illuminate\Support\Js::from($bookedSlots ?? []) }},
    candidateTimes: ['08:00', '10:00', '12:00', '14:00', '16:00', '18:00', '20:00'],
    selectedFeatures: [],

    // Mini Calendar Navigation State
    calendarYear: new Date('{{ old('tanggal_sesi', $preselectedDate ?? date('Y-m-d', strtotime('+2 days'))) }}' + 'T00:00:00').getFullYear(),
    calendarMonth: new Date('{{ old('tanggal_sesi', $preselectedDate ?? date('Y-m-d', strtotime('+2 days'))) }}' + 'T00:00:00').getMonth(),
    monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
    dayNames: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],

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

    get calendarDays() {
      const year = this.calendarYear;
      const month = this.calendarMonth;
      const firstDayIndex = new Date(year, month, 1).getDay();
      const totalDays = new Date(year, month + 1, 0).getDate();
      const prevTotalDays = new Date(year, month, 0).getDate();
      const todayStr = new Date().toISOString().split('T')[0];
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
        const isPast = dateStr < todayStr;
        const isToday = dateStr === todayStr;

        let isFullDayBlocked = false;
        const avail = this.availabilities.find(a => a.tanggal === dateStr);
        if (avail && avail.is_full_day) {
          isFullDayBlocked = true;
        }

        const d = new Date(dateStr + 'T00:00:00');
        let dow = d.getDay();
        if (dow === 0) dow = 7;
        const wh = this.workingHours[dow];
        if (wh && !wh.is_open) {
          isFullDayBlocked = true;
        }

        days.push({
          dayNumber: i,
          dateStr: dateStr,
          isCurrentMonth: true,
          isPast: isPast,
          isToday: isToday,
          isFullDayBlocked: isFullDayBlocked
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
      
      const currentStatus = this.getSlotStatus(this.selectedTime);
      if (!currentStatus.available) {
        const firstAvail = this.candidateTimes.find(t => this.getSlotStatus(t).available);
        this.selectedTime = firstAvail || '';
      }
    },

    get isDateFullDayBlocked() {
      const avail = this.availabilities.find(a => a.tanggal === this.selectedDate);
      if (avail && avail.is_full_day) return true;

      const d = new Date(this.selectedDate + 'T00:00:00');
      let dow = d.getDay();
      if (dow === 0) dow = 7;
      const wh = this.workingHours[dow];
      if (wh && !wh.is_open) return true;

      return false;
    },

    getSlotStatus(timeStr) {
      if (!this.selectedDate) return { available: false, label: 'Pilih Tanggal' };
      if (this.isDateFullDayBlocked) return { available: false, label: 'Libur' };

      const slotStart = timeStr;
      const [h, m] = slotStart.split(':').map(Number);
      const endH = h + this.sessionDuration;
      const slotEnd = String(endH).padStart(2, '0') + ':' + String(m).padStart(2, '0');

      const d = new Date(this.selectedDate + 'T00:00:00');
      let dow = d.getDay();
      if (dow === 0) dow = 7;
      const wh = this.workingHours[dow];
      if (wh) {
        if (!wh.is_open) return { available: false, label: 'Tutup' };
        if (!wh.is_24_hours) {
          const openTime = wh.jam_buka.substring(0, 5);
          const closeTime = wh.jam_tutup.substring(0, 5);
          if (slotStart < openTime || slotEnd > closeTime) {
            return { available: false, label: 'Tutup' };
          }
        }
      }

      for (let a of this.availabilities) {
        if (a.tanggal === this.selectedDate) {
          if (a.is_full_day) return { available: false, label: 'Libur' };
          if (a.jam_mulai && a.jam_selesai) {
            const bStart = a.jam_mulai.substring(0, 5);
            const bEnd = a.jam_selesai.substring(0, 5);
            if (slotStart < bEnd && slotEnd > bStart) {
              const ket = (a.keterangan || '').toLowerCase();
              return { available: false, label: (ket.includes('istirahat') || ket.includes('break')) ? 'Istirahat' : 'Tutup' };
            }
          }
        }
      }

      for (let bk of this.bookedSlots) {
        if (bk.date === this.selectedDate) {
          const bkStart = (bk.jam_mulai || '08:00').substring(0, 5);
          let bkEnd = bk.jam_selesai ? bk.jam_selesai.substring(0, 5) : '';
          if (!bkEnd) {
            const [bkh, bkm] = bkStart.split(':').map(Number);
            const dur = Number(bk.durasi) || 2;
            bkEnd = String(bkh + dur).padStart(2, '0') + ':' + String(bkm).padStart(2, '0');
          }
          if (slotStart < bkEnd && slotEnd > bkStart) {
            return { available: false, label: 'Terisi' };
          }
        }
      }

      return { available: true, label: 'Tersedia' };
    },

    selectSlot(timeStr) {
      const status = this.getSlotStatus(timeStr);
      if (status.available) {
        this.selectedTime = timeStr;
      }
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

    calculateEndTime(startTime) {
      if (!startTime) return '';
      const [h, m] = startTime.split(':').map(Number);
      const endH = h + this.sessionDuration;
      return String(endH).padStart(2, '0') + ':' + String(m).padStart(2, '0');
    },

    get selectedService() {
        if (!this.selectedServiceId) return null;
        return this.allServices.find(s => s.id === this.selectedServiceId) || null;
    },
    get basePrice() {
        return this.selectedService ? this.selectedService.harga : 0;
    },
    get extraTotal() {
        if (!this.selectedService) return 0;
        let sum = 0;
        this.selectedFeatures.forEach(featId => {
            let feat = this.selectedService.details.find(d => d.id === featId);
            if (feat && feat.harga > 0) {
                sum += feat.harga;
            }
        });
        return sum;
    },
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
    selectService(id) {
        this.selectedServiceId = id;
        this.selectedFeatures = [];
    },
    toggleFeature(id) {
        const index = this.selectedFeatures.indexOf(id);
        if (index > -1) {
            this.selectedFeatures.splice(index, 1);
        } else {
            this.selectedFeatures.push(id);
        }
    }
  }">

    <!-- Page Header (Anti-AI Slop: Clean Typography Without Breadcrumb) -->
    <div class="text-center space-y-2 max-w-2xl mx-auto">
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
      <input type="hidden" name="service_id" :value="selectedServiceId">
      <input type="hidden" name="tanggal_sesi" :value="selectedDate" required>
      <input type="hidden" name="jam_mulai" :value="selectedTime" required>
      <input type="hidden" name="durasi" :value="sessionDuration">

      <!-- Left Column: Form Inputs (7 Cols) -->
      <div class="lg:col-span-7 space-y-6 lg:space-y-7">

        <!-- Seksi 1 (Paling Atas): Pilih Paket Layanan Sesi Foto -->
        <div class="bg-white dark:bg-gray-800 p-6 sm:p-7 rounded-2xl border border-gray-200/80 dark:border-gray-700/80 shadow-xs space-y-5">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-500 flex items-center justify-center shrink-0">
              <svg class="w-4.5 h-4.5 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
            </div>
            <div>
              <h2 class="text-sm sm:text-base font-black text-gray-900 dark:text-white">Pilih Paket Layanan</h2>
              <p class="text-[11px] text-gray-500 dark:text-gray-400">Pilih salah satu paket pemotretan yang disediakan oleh fotografer</p>
            </div>
          </div>

          <!-- Package Cards Selection List -->
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
                    </div>
                  </div>
                  <div class="text-right shrink-0">
                    <span class="text-xs sm:text-sm font-black text-amber-600 dark:text-amber-400">Rp {{ number_format($pkg->tarif_harga, 0, ',', '.') }}</span>
                  </div>
                </div>

                @if($pkg->details->count() > 0)
                  <div class="pt-2 grid grid-cols-1 sm:grid-cols-2 gap-1.5 text-[11px] text-gray-600 dark:text-gray-300">
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
        </div>

        <!-- Seksi 2: Jadwal Pelaksanaan Sesi Foto (Mini Kalender & Time Slot Chips) -->
        <div class="bg-white dark:bg-gray-800 p-6 sm:p-7 rounded-2xl border border-gray-200/80 dark:border-gray-700/80 shadow-xs space-y-6">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-500 flex items-center justify-center shrink-0">
              <svg class="w-4.5 h-4.5 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
            <div>
              <h2 class="text-sm sm:text-base font-black text-gray-900 dark:text-white">Jadwal Pelaksanaan Sesi Foto</h2>
              <p class="text-[11px] text-gray-500 dark:text-gray-400">Pilih tanggal dan slot jam sesi pemotretan Anda</p>
            </div>
          </div>

          <!-- Bagian A: Mini Kalender Interaktif -->
          <div class="p-4 sm:p-5 rounded-2xl bg-gray-50/70 dark:bg-gray-900/40 border border-gray-200/80 dark:border-gray-700/60 space-y-3.5">
            <div class="flex items-center justify-between">
              <h3 class="text-xs sm:text-sm font-black text-gray-900 dark:text-white tracking-tight" 
                  x-text="monthNames[calendarMonth] + ' ' + calendarYear">
              </h3>
              
              <!-- Month Navigation Controls -->
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

            <!-- Day Headers -->
            <div class="grid grid-cols-7 gap-1 text-center text-[10px] font-black uppercase tracking-wider text-gray-400 dark:text-gray-500">
              <template x-for="dname in dayNames" :key="dname">
                <div class="py-0.5" x-text="dname"></div>
              </template>
            </div>

            <!-- Calendar Days Matrix -->
            <div class="grid grid-cols-7 gap-1 sm:gap-1.5">
              <template x-for="(day, idx) in calendarDays" :key="idx">
                <div @click="selectDay(day)"
                     class="min-h-[46px] p-1.5 rounded-xl border text-center transition flex flex-col items-center justify-center select-none"
                     :class="{
                       'opacity-20 border-transparent cursor-default': !day.isCurrentMonth,
                       'bg-gray-100/60 dark:bg-gray-900/60 border-gray-200/60 dark:border-gray-800 text-gray-400 dark:text-gray-600 cursor-not-allowed': day.isCurrentMonth && day.isPast,
                       'bg-rose-50/50 dark:bg-rose-950/20 border-rose-200/50 dark:border-rose-900/40 text-rose-500 cursor-not-allowed': day.isCurrentMonth && !day.isPast && day.isFullDayBlocked,
                       'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 hover:border-amber-400 cursor-pointer shadow-2xs': day.isCurrentMonth && !day.isPast && !day.isFullDayBlocked && selectedDate !== day.dateStr,
                       'border-amber-400 bg-amber-400 text-gray-950 font-black shadow-xs ring-2 ring-amber-400/30': day.isCurrentMonth && selectedDate === day.dateStr
                     }">
                  <span class="text-xs font-black leading-none" x-text="day.dayNumber"></span>
                  
                  <template x-if="day.isCurrentMonth && day.isToday && selectedDate !== day.dateStr">
                    <span class="text-[9px] font-extrabold text-amber-600 dark:text-amber-400 mt-0.5">Hari Ini</span>
                  </template>
                  <template x-if="day.isCurrentMonth && !day.isPast && day.isFullDayBlocked">
                    <span class="text-[9px] font-bold text-rose-500 dark:text-rose-400 mt-0.5">Libur</span>
                  </template>
                </div>
              </template>
            </div>
          </div>

          <!-- Bagian B: Time-Slot Chips Matrix -->
          <div class="space-y-3 pt-1">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">
                Pilih Jam Mulai Sesi <span class="text-rose-500">*</span>
              </label>
              <span class="text-[11px] text-gray-500 dark:text-gray-400">
                Jadwal: <strong class="text-amber-600 dark:text-amber-400" x-text="formatDateIndo(selectedDate)"></strong>
              </span>
            </div>

            <!-- Jika fotografer libur pada tanggal terpilih -->
            <template x-if="isDateFullDayBlocked">
              <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-900/60 border border-dashed border-gray-200 dark:border-gray-700 text-center text-xs text-gray-500 dark:text-gray-400">
                Fotografer tidak menerima sesi pemotretan pada tanggal ini (Libur Operasional). Silakan klik tanggal lain pada kalender di atas.
              </div>
            </template>

            <!-- Jika tanggal buka: Grid Slot Jam -->
            <template x-if="!isDateFullDayBlocked">
              <div class="space-y-3">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                  <template x-for="slot in candidateTimes" :key="slot">
                    <div @click="selectSlot(slot)"
                         class="p-3 rounded-xl border text-center transition select-none flex flex-col justify-center items-center gap-0.5"
                         :class="{
                           'border-amber-400 bg-amber-400 text-gray-950 font-extrabold shadow-xs ring-2 ring-amber-400/30 cursor-pointer': selectedTime === slot,
                           'border-gray-200 dark:border-gray-700/80 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 hover:border-amber-400 cursor-pointer shadow-2xs': selectedTime !== slot && getSlotStatus(slot).available,
                           'border-dashed border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-900/50 text-gray-400 dark:text-gray-500 cursor-not-allowed opacity-50': !getSlotStatus(slot).available
                         }">
                      <span class="text-xs font-black" x-text="slot + ' WIB'"></span>
                      
                      <!-- Status label per slot -->
                      <template x-if="selectedTime === slot">
                        <span class="text-[10px] font-black text-gray-950">Terpilih</span>
                      </template>
                      <template x-if="selectedTime !== slot && getSlotStatus(slot).available">
                        <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400">Tersedia</span>
                      </template>
                      <template x-if="!getSlotStatus(slot).available">
                        <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500" x-text="getSlotStatus(slot).label"></span>
                      </template>
                    </div>
                  </template>
                </div>

                <!-- Active Slot Info -->
                <template x-if="selectedTime && getSlotStatus(selectedTime).available">
                  <p class="text-[11px] text-gray-500 dark:text-gray-400">
                    Estimasi durasi sesi: <strong class="text-gray-900 dark:text-white" x-text="selectedTime + ' WIB s/d ' + calculateEndTime(selectedTime) + ' WIB (' + sessionDuration + ' Jam)'"></strong>
                  </p>
                </template>
              </div>
            </template>
          </div>
        </div>

        <!-- Seksi 3: Lokasi Pemotretan / Acara -->
        <div class="bg-white dark:bg-gray-800 p-6 sm:p-7 rounded-2xl border border-gray-200/80 dark:border-gray-700/80 shadow-xs space-y-5">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-500 flex items-center justify-center shrink-0">
              <svg class="w-4.5 h-4.5 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            </div>
            <div>
              <h2 class="text-sm sm:text-base font-black text-gray-900 dark:text-white">Lokasi Pemotretan</h2>
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
                    <div @click="selectedCode = '{{ $prov->code }}'; selectedText = '{{ addslashes($prov->name) }}'; open = false; fetchBookingCities('{{ $prov->code }}');" class="px-4 py-2.5 text-xs text-gray-800 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition cursor-pointer">
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

        <!-- Seksi 4: Catatan Khusus -->
        <div class="bg-white dark:bg-gray-800 p-6 sm:p-7 rounded-2xl border border-gray-200/80 dark:border-gray-700/80 shadow-xs space-y-5">
          <div class="flex items-center gap-3">
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
        <div class="bg-white dark:bg-gray-800 p-6 sm:p-7 rounded-2xl border border-gray-200/80 dark:border-gray-700/80 shadow-sm space-y-6">
          <div>
            <h2 class="text-sm font-black text-gray-900 dark:text-white">
              Ringkasan Pesanan
            </h2>
            <p class="text-[11px] text-gray-400 mt-0.5">Rincian paket sesi dan estimasi total biaya</p>
          </div>

          <!-- Photographer Profile Info -->
          <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-900/60 border border-gray-100 dark:border-gray-700/60">
            <x-user-avatar :user="$photographer->user" size="w-10 h-10" />
            <div class="flex-1 min-w-0">
              <h3 class="text-xs font-black text-gray-900 dark:text-white leading-snug truncate">{{ $photographer->nama }}</h3>
              <p class="text-[11px] text-gray-500 flex items-center gap-1 mt-0.5">
                <svg class="w-3.5 h-3.5 text-amber-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg> 
                <span class="truncate">{{ $photographer->city->name ?? $photographer->alamat }}</span>
              </p>
            </div>
          </div>

          <!-- State A: Package Not Yet Selected -->
          <div x-show="!selectedServiceId" class="p-5 rounded-2xl bg-gray-50/70 dark:bg-gray-900/40 border border-dashed border-gray-200 dark:border-gray-700 text-center space-y-2">
            <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-500 mx-auto flex items-center justify-center">
              <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
            </div>
            <h4 class="text-xs font-black text-gray-900 dark:text-white">Pilih Paket Layanan</h4>
            <p class="text-[11px] text-gray-500 dark:text-gray-400 max-w-xs mx-auto leading-relaxed">Silakan pilih salah satu paket layanan pemotretan pada formulir di sebelah kiri terlebih dahulu.</p>
          </div>

          <!-- State B: Package Selected Details -->
          <div x-show="selectedServiceId" x-transition class="p-4 rounded-xl bg-amber-50/70 dark:bg-amber-950/40 border border-amber-200/80 dark:border-amber-800/60 space-y-2">
            <div class="flex items-center justify-between">
              <span class="text-[10px] font-black uppercase tracking-wider text-amber-800 dark:text-amber-300">Paket Terpilih</span>
              <span class="text-xs font-black text-amber-600 dark:text-amber-400" x-text="'Rp ' + (selectedService ? selectedService.harga.toLocaleString('id-ID') : 0)"></span>
            </div>
            <div>
              <h4 class="text-xs font-black text-gray-900 dark:text-white" x-text="selectedService ? selectedService.nama : ''"></h4>
              <p class="text-[11px] text-gray-600 dark:text-gray-300 leading-relaxed mt-1 line-clamp-3" x-text="selectedService ? selectedService.deskripsi : ''"></p>
            </div>
          </div>

          <!-- Jadwal Terpilih Info -->
          <div x-show="selectedDate && selectedTime" class="p-3.5 rounded-xl bg-gray-50 dark:bg-gray-900/60 border border-gray-100 dark:border-gray-700/60 space-y-1">
            <div class="flex items-center justify-between text-xs">
              <span class="text-gray-500 dark:text-gray-400">Jadwal Sesi:</span>
              <span class="font-bold text-amber-600 dark:text-amber-400" x-text="selectedTime + ' - ' + calculateEndTime(selectedTime) + ' WIB'"></span>
            </div>
            <p class="text-[11px] text-gray-700 dark:text-gray-300 font-medium truncate" x-text="formatDateIndo(selectedDate)"></p>
          </div>

          <!-- Dynamic Feature Add-ons (If Available for Selected Package) -->
          <template x-if="selectedService && selectedService.details && selectedService.details.length > 0">
            <div class="space-y-2.5">
              <span class="text-[11px] font-black text-gray-800 dark:text-gray-200 uppercase tracking-wider block">Fitur Tambahan (Opsional):</span>
              <div class="space-y-2">
                <template x-for="det in selectedService.details" :key="det.id">
                  <label class="flex items-start gap-2.5 p-2.5 rounded-xl border text-xs transition cursor-pointer select-none"
                         :class="selectedFeatures.includes(det.id) ? 'border-amber-400 bg-amber-50/40 dark:bg-amber-950/20 text-gray-900 dark:text-white' : 'border-gray-100 dark:border-gray-700/60 bg-gray-50/70 dark:bg-gray-900/40 text-gray-700 dark:text-gray-300 hover:border-gray-200'">
                    <input type="checkbox" 
                           :checked="selectedFeatures.includes(det.id)"
                           @click="toggleFeature(det.id)"
                           class="mt-0.5 rounded text-amber-500 focus:ring-amber-400 w-3.5 h-3.5">
                    <div class="flex-1">
                      <span class="font-medium block" x-text="det.nama"></span>
                      <template x-if="det.harga > 0">
                        <span class="text-[11px] font-black text-amber-600 dark:text-amber-400 mt-0.5 block" x-text="'+Rp ' + det.harga.toLocaleString('id-ID')"></span>
                      </template>
                    </div>
                  </label>
                </template>
              </div>
            </div>
          </template>

          <!-- Hidden Inputs for Form Submission of Selected Features -->
          <template x-for="featId in selectedFeatures" :key="featId">
            <input type="hidden" name="selected_features[]" :value="featId">
          </template>

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
                <p class="text-[11px] text-gray-500 dark:text-gray-400">Pembayaran aman dengan garansi LensMatch</p>
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
              <span class="font-bold text-gray-800 dark:text-gray-200" x-text="'Rp ' + basePrice.toLocaleString('id-ID')">Rp 0</span>
            </div>
            <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 font-medium">
              <span>Fitur Tambahan</span>
              <span class="font-bold text-gray-800 dark:text-gray-200" x-text="'Rp ' + extraTotal.toLocaleString('id-ID')">Rp 0</span>
            </div>
            <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 font-medium">
              <span>Total Nilai Sesi</span>
              <span class="font-bold text-gray-900 dark:text-white" x-text="'Rp ' + totalBill.toLocaleString('id-ID')">Rp 0</span>
            </div>

            <template x-if="paymentType === 'dp' && selectedServiceId">
              <div class="space-y-2 pt-2">
                <div class="flex justify-between text-xs font-bold text-amber-600 dark:text-amber-400">
                  <span>Tagihan DP (30%) Sekarang</span>
                  <span x-text="'Rp ' + dpAmount.toLocaleString('id-ID')"></span>
                </div>
                <div class="flex justify-between text-[11px] text-gray-500 dark:text-gray-400">
                  <span>Sisa Pelunasan Nanti (70%)</span>
                  <span class="font-medium" x-text="'Rp ' + remainingAmount.toLocaleString('id-ID')"></span>
                </div>
              </div>
            </template>

            <div class="flex justify-between items-center text-gray-900 dark:text-white pt-2">
              <span class="text-xs font-bold">Total Dibayar Sekarang</span>
              <span class="text-lg font-black text-amber-600 dark:text-amber-400" x-text="'Rp ' + currentPayAmount.toLocaleString('id-ID')">Rp 0</span>
            </div>
          </div>

          <!-- Submit Action Button (Brand Amber Button Rule #91) -->
          <div class="pt-2">
            <button type="submit" 
                    :disabled="!selectedServiceId || !selectedDate || !selectedTime || !getSlotStatus(selectedTime).available"
                    :class="(!selectedServiceId || !selectedDate || !selectedTime || !getSlotStatus(selectedTime).available) ? 'opacity-50 cursor-not-allowed bg-gray-300 dark:bg-gray-700 text-gray-500' : 'bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold shadow-md cursor-pointer'"
                    class="w-full py-3.5 text-xs rounded-xl transition text-center block">
              <span x-show="!selectedServiceId">Pilih Paket Layanan Terlebih Dahulu</span>
              <span x-show="selectedServiceId && (!selectedTime || !getSlotStatus(selectedTime).available)" style="display: none;">Pilih Slot Jam Pemotretan</span>
              <span x-show="selectedServiceId && selectedTime && getSlotStatus(selectedTime).available" style="display: none;">Lanjut ke Invoice Pembayaran</span>
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
            <div onclick="selectBookingCity('${city.code}', '${escapedName}')" class="px-4 py-2.5 text-xs text-gray-800 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition cursor-pointer">
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
