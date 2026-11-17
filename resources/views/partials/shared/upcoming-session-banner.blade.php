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

    $jamMulai = substr($urgentSession->jam_mulai, 0, 5) . ' WIB';
    $statusText = $isToday ? "HARI INI • {$jamMulai}" : ($isTomorrow ? "BESOK • {$jamMulai}" : \Carbon\Carbon::parse($urgentSession->booking_date)->translatedFormat('d M Y') . " • {$jamMulai}");

    $timeLabel = $isToday ? 'hari ini' : ($isTomorrow ? 'besok' : 'pada ' . \Carbon\Carbon::parse($urgentSession->booking_date)->translatedFormat('d M Y'));
    $locationPart = $urgentSession->lokasi ? " di {$urgentSession->lokasi}" : '';
    $servicePart = $service->nama_layanan ?? 'sesi pemotretan';

    if ($role === 'photographer') {
        $counterpartName = $contract->client->nama ?? ($contract->client->user->nama ?? 'Klien');
        $prefillMessage = "Halo Kak {$counterpartName}, mengingatkan kembali jadwal sesi {$servicePart} {$timeLabel} pukul {$jamMulai}{$locationPart}. Apakah ada hal yang perlu dipersiapkan bersama?";
        $chatUrl = route('photographer.chat.client', ['clientId' => $contract->id_client, 'prefill' => $prefillMessage]);
    } else {
        $photographer = $contract->photographer;
        $counterpartName = $photographer->nama ?? 'Mitra Fotografer';
        $prefillMessage = $isToday
            ? "Halo {$counterpartName}, memastikan kembali untuk sesi {$servicePart} hari ini pukul {$jamMulai}{$locationPart}. Sampai bertemu di lokasi ya!"
            : "Halo {$counterpartName}, memastikan kembali untuk sesi {$servicePart} {$timeLabel} pukul {$jamMulai}{$locationPart}. Apakah semua persiapan sudah siap?";
        $chatUrl = route('chat.start', ['photographerId' => $contract->id_photographer, 'prefill' => $prefillMessage]);
    }
  @endphp

  <!-- Compact Minimalist Bar (Inspirasi Airbnb / Uber) -->
  <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700/80 rounded-2xl p-4 sm:p-5 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
    <!-- Kiri: Label status waktu simpel, judul mengalir alami, dan sub-info lokasi -->
    <div class="space-y-1 min-w-0">
      <span class="text-xs font-black uppercase tracking-wider block text-amber-600 dark:text-amber-400">
        {{ $statusText }}
      </span>

      <h3 class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white truncate">
        {{ $service->nama_layanan ?? 'Sesi Pemotretan' }} bersama {{ $counterpartName }}
      </h3>

      @if($urgentSession->lokasi)
        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
          Lokasi: {{ $urgentSession->lokasi }}
        </p>
      @endif
    </div>

    <!-- Kanan: Dua tombol kompak -->
    <div class="flex items-center gap-2.5 shrink-0 self-start md:self-center">
      @if($urgentSession->lokasi)
        <a href="{{ $urgentSession->google_maps_url }}" 
           target="_blank" 
           class="px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 font-bold text-xs transition shadow-2xs">
          Petunjuk Arah
        </a>
      @endif

      <a href="{{ $chatUrl }}" 
         class="px-4 py-2 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs shadow-xs transition">
        Chat
      </a>
    </div>
  </div>
@endif
