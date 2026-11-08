@props([
    'urgentSession' => null,
    'role' => 'client', // 'client' or 'photographer'
])

@if($urgentSession)
  @php
    $contract = $urgentSession->contract;
    $service = $urgentSession->service;
    $isToday = $urgentSession->is_today;
    $isTomorrow = $urgentSession->is_tomorrow;
    $sessionIso = $urgentSession->session_date_time->toISOString();

    if ($role === 'photographer') {
        $counterpartName = $contract->client->nama ?? 'Klien LensMatch';
        $counterpartRole = 'Klien Pemesan';
        $counterpartPhone = $contract->client->nomor_telepon ?? ($contract->client->user->nomor_telepon ?? null);
        $counterpartAvatar = $contract->client->user->avatar ?? null;
        $counterpartInitial = substr($counterpartName, 0, 2);
        $chatUrl = route('photographer.chat.client', $contract->id_client);
    } else {
        $photographer = $contract->photographer;
        $counterpartName = $photographer->nama ?? 'Mitra Fotografer';
        $counterpartRole = 'Mitra Fotografer';
        $counterpartPhone = $photographer->user->nomor_telepon ?? null;
        $counterpartAvatar = $photographer->foto;
        $counterpartInitial = substr($counterpartName, 0, 2);
        $chatUrl = route('chat.start', $contract->id_photographer);
    }
  @endphp

  <div x-data="{
        targetTime: new Date('{{ $sessionIso }}').getTime(),
        hours: '--',
        minutes: '--',
        seconds: '--',
        isOngoing: {{ $urgentSession->is_ongoing ? 'true' : 'false' }},
        isPassed: false,
        updateCountdown() {
            const now = new Date().getTime();
            const diff = this.targetTime - now;
            if (diff <= 0) {
                this.isPassed = true;
                return;
            }
            const h = Math.floor(diff / (1000 * 60 * 60));
            const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const s = Math.floor((diff % (1000 * 60)) / 1000);
            this.hours = String(h).padStart(2, '0');
            this.minutes = String(m).padStart(2, '0');
            this.seconds = String(s).padStart(2, '0');
        },
        init() {
            this.updateCountdown();
            setInterval(() => this.updateCountdown(), 1000);
        }
      }"
      class="p-5 sm:p-6 rounded-2xl sm:rounded-3xl border border-amber-300/80 dark:border-amber-800/70 bg-linear-to-r from-amber-500/10 via-amber-500/5 to-white dark:from-amber-950/40 dark:via-gray-800/80 dark:to-gray-800 shadow-xs space-y-4">
    
    <!-- Top Bar: Badge, Countdown Clock & Code -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-amber-200/60 dark:border-gray-700/60 pb-3.5">
      <div class="flex items-center gap-2.5 flex-wrap">
        @if($isToday)
          <span class="px-2.5 py-1 rounded-full text-[11px] font-black uppercase tracking-wider bg-amber-400 text-gray-950 shadow-2xs">
            Sesi Hari Ini
          </span>
        @elseif($isTomorrow)
          <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold uppercase tracking-wider bg-blue-100 dark:bg-blue-950/70 text-blue-700 dark:text-blue-300 border border-blue-200/70 dark:border-blue-900/60">
            Pengingat Sesi H-1 (Besok)
          </span>
        @else
          <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold uppercase tracking-wider bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
            Jadwal Terdekat
          </span>
        @endif

        <span class="text-xs font-bold text-gray-600 dark:text-gray-300">
          ID Kontrak: <span class="font-mono text-gray-900 dark:text-white font-extrabold">KTRK-{{ str_pad($contract->id, 5, '0', STR_PAD_LEFT) }}</span>
        </span>
      </div>

      <!-- Live Dynamic Countdown Timer -->
      <div class="flex items-center gap-2 text-xs">
        <span class="text-gray-500 dark:text-gray-400 font-medium">Hitung Mundur Sesi:</span>
        <template x-if="!isPassed && !isOngoing">
          <div class="inline-flex items-center gap-1 font-mono font-black text-amber-700 dark:text-amber-400 bg-amber-100/70 dark:bg-amber-950/70 px-2.5 py-0.5 rounded-lg border border-amber-200 dark:border-amber-900/50">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span x-text="hours + 'j : ' + minutes + 'm : ' + seconds + 'd'"></span>
          </div>
        </template>
        <template x-if="isPassed || isOngoing">
          <span class="inline-flex items-center gap-1 font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/50 px-2.5 py-0.5 rounded-lg border border-emerald-200 dark:border-emerald-900/50">
            Sedang Berlangsung Hari Ini
          </span>
        </template>
      </div>
    </div>

    <!-- Main Content: Counterpart & Session Specs & Quick Actions -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5">
      
      <!-- Counterpart & Session Details -->
      <div class="flex items-start sm:items-center gap-4 min-w-0">
        <!-- Avatar -->
        <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl overflow-hidden bg-gray-100 dark:bg-gray-700 border-2 border-amber-400 shrink-0 shadow-2xs">
          @if($counterpartAvatar)
            <img src="{{ $counterpartAvatar }}" alt="{{ $counterpartName }}" class="w-full h-full object-cover">
          @else
            <div class="w-full h-full flex items-center justify-center bg-amber-400 text-gray-950 font-black text-sm uppercase">
              {{ $counterpartInitial }}
            </div>
          @endif
        </div>

        <!-- Meta Text -->
        <div class="space-y-1 min-w-0">
          <div class="flex items-center gap-2 flex-wrap">
            <h3 class="text-sm sm:text-base font-black text-gray-900 dark:text-white truncate">
              {{ $service->nama_layanan ?? 'Sesi Pemotretan Khusus' }}
            </h3>
            <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-md bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-300">
              {{ $counterpartRole }}: {{ $counterpartName }}
            </span>
          </div>

          <div class="flex items-center gap-4 text-xs text-gray-600 dark:text-gray-300 flex-wrap">
            <!-- Waktu -->
            <div class="flex items-center gap-1.5 font-bold text-gray-900 dark:text-white">
              <svg class="w-3.5 h-3.5 text-amber-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
              <span>{{ \Carbon\Carbon::parse($urgentSession->booking_date)->translatedFormat('l, d F Y') }}</span>
              <span class="text-gray-400">•</span>
              <span>{{ substr($urgentSession->jam_mulai, 0, 5) }} - {{ substr($urgentSession->jam_selesai, 0, 5) }} WITA</span>
            </div>

            <!-- Kontak Telepon / WA jika ada -->
            @if($counterpartPhone)
              <div class="flex items-center gap-1 text-gray-500">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
                <span>{{ $counterpartPhone }}</span>
              </div>
            @endif
          </div>

          <!-- Lokasi Pemotretan -->
          <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
            <svg class="w-3.5 h-3.5 text-rose-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
            <span class="truncate font-medium">{{ $urgentSession->lokasi ?: 'Lokasi Acara Fleksibel' }}</span>
          </div>
        </div>
      </div>

      <!-- 2 Action Buttons (One-Click Google Maps & Direct Chat) -->
      <div class="flex items-center gap-2.5 sm:self-center shrink-0">
        <!-- Button 1: Google Maps Direct Link -->
        <a href="{{ $urgentSession->google_maps_url }}" 
           target="_blank" 
           title="Buka titik koordinat lokasi pemotretan di Google Maps"
           class="px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-750 text-gray-800 dark:text-gray-200 font-extrabold text-xs transition inline-flex items-center gap-1.5 shadow-2xs cursor-pointer">
          <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
          <span>Petunjuk Arah (Maps)</span>
        </a>

        <!-- Button 2: Direct Chat Thread -->
        <a href="{{ $chatUrl }}" 
           title="Buka obrolan langsung untuk konfirmasi persiapan sesi"
           class="px-4 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs shadow-xs transition inline-flex items-center gap-1.5 cursor-pointer">
          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a.75.75 0 01-.818-.847l.487-2.923C3.766 15.748 3 13.974 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z"/></svg>
          <span>Buka Chat Sesi</span>
        </a>
      </div>

    </div>

  </div>
@endif

