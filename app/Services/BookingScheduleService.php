<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\ContractBooking;
use App\Models\PhotographerAvailability;
use App\Models\PhotographerWorkingHour;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class BookingScheduleService
{
    public const SAME_LOCATION_RADIUS_METERS = 500.0;

    public static function parseTimeToMinutes(?string $time): ?int
    {
        if (!$time) {
            return null;
        }
        $clean = substr(trim($time), 0, 5);
        if (!preg_match('/^(\d{2}):(\d{2})$/', $clean, $m)) {
            return null;
        }
        return ((int) $m[1] * 60) + (int) $m[2];
    }

    public static function parseEndTimeToMinutes(?string $time): ?int
    {
        if (!$time) {
            return null;
        }
        $clean = substr(trim($time), 0, 8);
        if ($clean === '23:59:59' || $clean === '23:59' || $clean === '24:00' || $clean === '24:00:00') {
            return 1440;
        }
        return self::parseTimeToMinutes($time);
    }

    public static function formatMinutesToTime(int $minutes): string
    {
        $clamped = max(0, min(1440, $minutes));
        if ($clamped === 1440) {
            return '24:00';
        }
        $h = intdiv($clamped, 60);
        $m = $clamped % 60;
        return sprintf('%02d:%02d', $h, $m);
    }

    public static function formatMinutesToDbTime(int $minutes): string
    {
        $clamped = max(0, min(1439, $minutes === 1440 ? 1439 : $minutes));
        $h = intdiv($clamped, 60);
        $m = $clamped % 60;
        return sprintf('%02d:%02d:00', $h, $m);
    }

    public static function distanceInMeters(?float $lat1, ?float $lng1, ?float $lat2, ?float $lng2): ?float
    {
        if ($lat1 === null || $lng1 === null || $lat2 === null || $lng2 === null) {
            return null;
        }
        if (($lat1 == 0.0 && $lng1 == 0.0) || ($lat2 == 0.0 && $lng2 == 0.0)) {
            return null;
        }

        $earthRadius = 6371000.0;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * (sin($dLng / 2) ** 2);

        $c = 2 * atan2(sqrt($a), sqrt(max(0.0, 1 - $a)));
        return $earthRadius * $c;
    }

    public static function isSameLocation(?float $lat1, ?float $lng1, ?float $lat2, ?float $lng2): bool
    {
        $distance = self::distanceInMeters($lat1, $lng1, $lat2, $lng2);
        return $distance !== null && $distance <= self::SAME_LOCATION_RADIUS_METERS;
    }

    /**
     * Ambil daftar booking aktif yang mencakup tanggal tertentu (termasuk multi-hari).
     */
    public static function getActiveBookingsForDate(int $photographerId, string $date, ?int $excludeContractId = null)
    {
        return ContractBooking::whereHas('contract', function ($q) use ($photographerId, $excludeContractId) {
            $q->where('id_photographer', $photographerId)
              ->whereIn('status_contract', ['active', 'approved', 'pending_payment']);
            if ($excludeContractId !== null) {
                $q->where('id', '!=', $excludeContractId);
            }
        })
            ->whereDate('booking_date', '<=', $date)
            ->whereRaw('COALESCE(booking_end_date, booking_date) >= ?', [$date])
            ->get();
    }

    /**
     * Ambil seluruh tanggal terblokir penuh (manual full-day block atau booking multi-hari).
     */
    public static function getFullDayUnavailableDates(int $photographerId, string $fromDate): array
    {
        $dates = [];

        $manualFullBlocks = PhotographerAvailability::where('id_photographer', $photographerId)
            ->where('tanggal', '>=', $fromDate)
            ->get();

        foreach ($manualFullBlocks as $blk) {
            $isFullDay = !$blk->jam_mulai
                || !$blk->jam_selesai
                || ($blk->jam_mulai === '00:00:00' && $blk->jam_selesai === '23:59:59');
            if ($isFullDay && $blk->tanggal) {
                $dates[] = $blk->tanggal;
            }
        }

        $multiDayBookings = ContractBooking::whereHas('contract', function ($q) use ($photographerId) {
            $q->where('id_photographer', $photographerId)
              ->whereIn('status_contract', ['active', 'approved', 'pending_payment']);
        })
            ->whereNotNull('booking_end_date')
            ->whereColumn('booking_end_date', '>', 'booking_date')
            ->where('booking_end_date', '>=', $fromDate)
            ->get();

        foreach ($multiDayBookings as $mb) {
            $period = CarbonPeriod::create(
                max($fromDate, $mb->booking_date),
                $mb->booking_end_date
            );
            foreach ($period as $dt) {
                $dates[] = $dt->format('Y-m-d');
            }
        }

        return array_values(array_unique($dates));
    }

    /**
     * Validasi satu slot reguler pada tanggal tertentu (rentang jam bulat + spare time + bypass 500m).
     * Mengembalikan null jika valid, atau pesan error jika bentrok.
     */
    public static function validateRegularBookingSlot(
        int $photographerId,
        string $date,
        int $startMin,
        int $endMin,
        array $targetOptions = [],
        ?int $excludeContractId = null
    ): ?string {
        if ($startMin < 0 || $endMin > 1440 || $startMin >= $endMin) {
            return 'Jam selesai harus lebih akhir daripada jam mulai.';
        }
        if ($startMin % 60 !== 0 || ($endMin % 60 !== 0 && $endMin !== 1440)) {
            return 'Pilihan jam sesi reguler harus dalam kelipatan 1 jam penuh.';
        }

        $now = Carbon::now();
        if ($date < $now->toDateString()) {
            return 'Tanggal pemesanan tidak boleh di masa lalu.';
        }
        if ($date === $now->toDateString()) {
            $nowMinutes = ($now->hour * 60) + $now->minute;
            if ($startMin <= $nowMinutes) {
                return 'Jam mulai ' . self::formatMinutesToTime($startMin) . ' WIB sudah terlewat untuk hari ini.';
            }
        }

        $allowOutsideWorkingHours = (bool) ($targetOptions['allow_outside_working_hours'] ?? false);

        if (!$allowOutsideWorkingHours) {
            $dow = (int) Carbon::parse($date)->dayOfWeekIso;
            $workingHour = PhotographerWorkingHour::where('id_photographer', $photographerId)
                ->where('day_of_week', $dow)
                ->first();

            if ($workingHour) {
                if (!$workingHour->is_open) {
                    return "Fotografer tidak beroperasi pada hari {$workingHour->day_name}.";
                }
                if (!$workingHour->is_24_hours) {
                    $openMin = self::parseTimeToMinutes($workingHour->jam_buka) ?? 480;
                    $closeMin = self::parseEndTimeToMinutes($workingHour->jam_tutup) ?? 1020;

                    if ($startMin < $openMin || $endMin > $closeMin) {
                        $bukaStr = self::formatMinutesToTime($openMin);
                        $tutupStr = self::formatMinutesToTime($closeMin);
                        return "Jam sesi harus berada dalam jam operasional {$workingHour->day_name} ({$bukaStr} s/d {$tutupStr} WIB).";
                    }
                }
            }
        }

        // Blokir manual (Date Overrides)
        $blockedAvailabilities = PhotographerAvailability::where('id_photographer', $photographerId)
            ->where('tanggal', $date)
            ->get();

        foreach ($blockedAvailabilities as $blk) {
            $isFullDay = !$blk->jam_mulai
                || !$blk->jam_selesai
                || ($blk->jam_mulai === '00:00:00' && $blk->jam_selesai === '23:59:59');
            if ($isFullDay) {
                return 'Fotografer libur pada tanggal tersebut (' . ($blk->keterangan ?: 'Libur Penuh') . ').';
            }
            $blkStart = self::parseTimeToMinutes($blk->jam_mulai) ?? 0;
            $blkEnd = self::parseEndTimeToMinutes($blk->jam_selesai) ?? 1440;
            if ($startMin < $blkEnd && $endMin > $blkStart) {
                return 'Rentang jam berbenturan dengan penutupan jadwal fotografer (' . self::formatMinutesToTime($blkStart) . ' - ' . self::formatMinutesToTime($blkEnd) . ' WIB).';
            }
        }

        // Cek pesanan aktif lain beserta spare time & bypass 500m
        $targetBefore = max(0, (int) ($targetOptions['spare_before_minutes'] ?? 0));
        $targetAfter = max(0, (int) ($targetOptions['spare_after_minutes'] ?? 0));
        $targetBypass = (bool) ($targetOptions['allow_same_location_bypass'] ?? false);
        $targetLat = isset($targetOptions['latitude']) && $targetOptions['latitude'] !== null ? (float) $targetOptions['latitude'] : null;
        $targetLng = isset($targetOptions['longitude']) && $targetOptions['longitude'] !== null ? (float) $targetOptions['longitude'] : null;

        $existingBookings = self::getActiveBookingsForDate($photographerId, $date, $excludeContractId);

        foreach ($existingBookings as $eb) {
            if ($eb->is_multi_day) {
                return 'Tanggal tersebut sudah terisi oleh pemesanan khusus multi-hari (' . $eb->date_range_label . ').';
            }

            $ebStart = self::parseTimeToMinutes($eb->jam_mulai ?: '08:00') ?? 480;
            $ebEnd = $eb->jam_selesai
                ? (self::parseEndTimeToMinutes($eb->jam_selesai) ?? ($ebStart + 120))
                : ($ebStart + max(1, $eb->effective_duration_hours) * 60);

            // 1. Bentrok langsung jam sesi utama
            if ($startMin < $ebEnd && $endMin > $ebStart) {
                return 'Jadwal ' . self::formatMinutesToTime($startMin) . ' - ' . self::formatMinutesToTime($endMin)
                    . ' WIB berbenturan dengan sesi aktif lain (' . self::formatMinutesToTime($ebStart) . ' - ' . self::formatMinutesToTime($ebEnd) . ' WIB).';
            }

            // 2. Cek apakah kedua booking berhak bypass spare time karena 1 lokasi <= 500m
            $ebBefore = max(0, (int) ($eb->spare_before_minutes ?? 0));
            $ebAfter = max(0, (int) ($eb->spare_after_minutes ?? 0));
            $ebBypass = (bool) ($eb->allow_same_location_bypass ?? false);

            $sameLoc = self::isSameLocation(
                $targetLat,
                $targetLng,
                $eb->latitude !== null ? (float) $eb->latitude : null,
                $eb->longitude !== null ? (float) $eb->longitude : null
            );

            $canBypass = $sameLoc && $targetBypass && ($ebBypass || ($ebBefore === 0 && $ebAfter === 0));
            if ($canBypass) {
                continue;
            }

            // 3. Cek bentrok dua arah dengan jeda spare time
            $hitsExistingBuffer = ($startMin < ($ebEnd + $ebAfter)) && ($endMin > ($ebStart - $ebBefore));
            $hitsTargetBuffer = (($startMin - $targetBefore) < $ebEnd) && (($endMin + $targetAfter) > $ebStart);

            if ($hitsExistingBuffer || $hitsTargetBuffer) {
                return 'Jadwal ' . self::formatMinutesToTime($startMin) . ' - ' . self::formatMinutesToTime($endMin)
                    . ' WIB terlalu mepet dan masuk ke waktu jeda persiapan sesi lain.';
            }
        }

        return null;
    }

    /**
     * Validasi rentang tanggal untuk Penawaran Khusus Chat (1 hari atau multi-hari berurutan).
     * Jam bebas melewati jam operasional reguler, namun tidak boleh menabrak libur atau pesanan aktif.
     */
    public static function validateCustomOfferSchedule(
        int $photographerId,
        string $startDate,
        string $endDate,
        ?int $startMin = null,
        ?int $endMin = null,
        ?int $excludeContractId = null
    ): ?string {
        if ($endDate < $startDate) {
            return 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.';
        }

        $isSingleDay = ($startDate === $endDate);
        if ($isSingleDay && $startMin !== null && $endMin !== null && $startMin >= $endMin) {
            return 'Jam selesai harus lebih akhir daripada jam mulai.';
        }

        $period = CarbonPeriod::create($startDate, $endDate);
        foreach ($period as $dt) {
            $curDate = $dt->format('Y-m-d');

            // Cek blokir manual (tanggal libur)
            $blocks = PhotographerAvailability::where('id_photographer', $photographerId)
                ->where('tanggal', $curDate)
                ->get();

            foreach ($blocks as $blk) {
                $isFullDay = !$blk->jam_mulai
                    || !$blk->jam_selesai
                    || ($blk->jam_mulai === '00:00:00' && $blk->jam_selesai === '23:59:59');

                if ($isFullDay || !$isSingleDay || $startMin === null || $endMin === null) {
                    return 'Tanggal ' . $dt->format('d M Y') . ' ditandai libur oleh fotografer (' . ($blk->keterangan ?: 'Jadwal Ditutup') . ').';
                }

                $blkStart = self::parseTimeToMinutes($blk->jam_mulai) ?? 0;
                $blkEnd = self::parseEndTimeToMinutes($blk->jam_selesai) ?? 1440;
                if ($startMin < $blkEnd && $endMin > $blkStart) {
                    return 'Jadwal pada ' . $dt->format('d M Y') . ' berbenturan dengan penutupan jam fotografer (' . self::formatMinutesToTime($blkStart) . ' - ' . self::formatMinutesToTime($blkEnd) . ' WIB).';
                }
            }

            // Cek pesanan aktif lain
            $existingBookings = self::getActiveBookingsForDate($photographerId, $curDate, $excludeContractId);
            foreach ($existingBookings as $eb) {
                if (!$isSingleDay || $eb->is_multi_day || $startMin === null || $endMin === null) {
                    return 'Tanggal ' . $dt->format('d M Y') . ' sudah terisi oleh pesanan aktif klien lain.';
                }

                $ebStart = self::parseTimeToMinutes($eb->jam_mulai ?: '08:00') ?? 480;
                $ebEnd = $eb->jam_selesai
                    ? (self::parseEndTimeToMinutes($eb->jam_selesai) ?? ($ebStart + 120))
                    : ($ebStart + max(1, $eb->effective_duration_hours) * 60);
                $ebBefore = max(0, (int) ($eb->spare_before_minutes ?? 0));
                $ebAfter = max(0, (int) ($eb->spare_after_minutes ?? 0));

                if ($startMin < ($ebEnd + $ebAfter) && $endMin > ($ebStart - $ebBefore)) {
                    return 'Jam ' . self::formatMinutesToTime($startMin) . ' - ' . self::formatMinutesToTime($endMin)
                        . ' WIB pada ' . $dt->format('d M Y') . ' berbenturan dengan jadwal atau jeda sesi klien lain.';
                }
            }
        }

        return null;
    }

    /**
     * Hitung daftar jam mulai bulat yang tersedia beserta pilihan jam selesai validnya
     * untuk dropdown dinamis halaman reservasi / reschedule.
     */
    public static function getAvailableTimeMatrix(
        int $photographerId,
        string $date,
        array $targetOptions = [],
        ?int $excludeContractId = null
    ): array {
        $dow = (int) Carbon::parse($date)->dayOfWeekIso;
        $workingHour = PhotographerWorkingHour::where('id_photographer', $photographerId)
            ->where('day_of_week', $dow)
            ->first();

        if ($workingHour && !$workingHour->is_open) {
            return [
                'is_open' => false,
                'reason' => "Fotografer tidak beroperasi pada hari {$workingHour->day_name}.",
                'operating_window' => null,
                'start_options' => [],
                'end_options_by_start' => [],
            ];
        }

        $openMin = 480; // default 08:00
        $closeMin = 1020; // default 17:00
        if ($workingHour) {
            if ($workingHour->is_24_hours) {
                $openMin = 0;
                $closeMin = 1380; // 23:00 agar sesi bulat valid maksimal 22:00-23:00 (aman untuk kolom TIME)
            } else {
                $openMin = self::parseTimeToMinutes($workingHour->jam_buka) ?? 480;
                $rawClose = self::parseEndTimeToMinutes($workingHour->jam_tutup) ?? 1020;
                $closeMin = $rawClose === 1440 ? 1380 : $rawClose;
            }
        }

        $firstHourStart = (int) ceil($openMin / 60) * 60;
        $lastHourEnd = (int) floor($closeMin / 60) * 60;

        $startOptions = [];
        $endOptionsByStart = [];
        $hasAnySlot = false;

        for ($s = $firstHourStart; $s <= $lastHourEnd - 60; $s += 60) {
            $validEnds = [];
            for ($e = $s + 60; $e <= $lastHourEnd; $e += 60) {
                $err = self::validateRegularBookingSlot($photographerId, $date, $s, $e, $targetOptions, $excludeContractId);
                if ($err === null) {
                    $validEnds[] = [
                        'value' => self::formatMinutesToTime($e),
                        'label' => self::formatMinutesToTime($e) . ' WIB (' . (($e - $s) / 60) . ' Jam)',
                        'hours' => (int) (($e - $s) / 60),
                    ];
                } else {
                    // Begitu suatu rentang terpotong oleh blokir/booking di tengah, perpanjangan lebih lanjut pasti tetap menabrak
                    if (!empty($validEnds)) {
                        break;
                    }
                }
            }

            $timeVal = self::formatMinutesToTime($s);
            $isAvailable = count($validEnds) > 0;
            if ($isAvailable) {
                $hasAnySlot = true;
            }

            $startOptions[] = [
                'value' => $timeVal,
                'label' => $timeVal . ' WIB',
                'available' => $isAvailable,
            ];

            if ($isAvailable) {
                $endOptionsByStart[$timeVal] = $validEnds;
            }
        }

        $reason = null;
        if (!$hasAnySlot) {
            $blocks = PhotographerAvailability::where('id_photographer', $photographerId)
                ->where('tanggal', $date)
                ->get();
            foreach ($blocks as $blk) {
                if (!$blk->jam_mulai || !$blk->jam_selesai || ($blk->jam_mulai === '00:00:00' && $blk->jam_selesai === '23:59:59')) {
                    $reason = 'Fotografer libur pada tanggal ini (' . ($blk->keterangan ?: 'Libur Penuh') . ').';
                    break;
                }
            }
            if (!$reason) {
                $reason = 'Seluruh jam pada tanggal ini sudah terisi atau melewati waktu saat ini.';
            }
        }

        return [
            'is_open' => true,
            'has_available_slots' => $hasAnySlot,
            'reason' => $reason,
            'operating_window' => [
                'start' => self::formatMinutesToTime($firstHourStart),
                'end' => self::formatMinutesToTime($lastHourEnd),
            ],
            'start_options' => $startOptions,
            'end_options_by_start' => $endOptionsByStart,
        ];
    }

    /**
     * Validasi pengajuan & persetujuan reschedule untuk kontrak reguler maupun multi-hari/custom.
     * Mempertahankan durasi asli, spare time, koordinat bypass 500m, atau rentang hari berurutan.
     */
    public static function validateReschedule(Contract $contract, string $newStartDate, string $newStartTime): array
    {
        $booking = $contract->bookingDetail;
        $photographerId = (int) $contract->id_photographer;
        $cleanStart = substr(trim($newStartTime), 0, 5);
        $startMin = self::parseTimeToMinutes($cleanStart);

        if ($startMin === null) {
            return [
                'error' => 'Format jam mulai jadwal baru tidak valid.',
                'new_end_date' => $newStartDate,
                'new_start_time' => $cleanStart . ':00',
                'new_end_time' => $cleanStart . ':00',
            ];
        }

        if ($booking && $booking->is_multi_day) {
            $origDays = max(0, (int) Carbon::parse($booking->booking_date)->diffInDays(Carbon::parse($booking->effective_end_date)));
            $newEndDate = Carbon::parse($newStartDate)->addDays($origDays)->format('Y-m-d');
            $origStartMin = self::parseTimeToMinutes($booking->jam_mulai ?: '08:00') ?? 480;
            $origEndMin = self::parseEndTimeToMinutes($booking->jam_selesai ?: '17:00') ?? 1020;
            $dailyMinutes = max(60, $origEndMin - $origStartMin);
            $endMin = min(1439, $startMin + $dailyMinutes);

            $error = self::validateCustomOfferSchedule(
                $photographerId,
                $newStartDate,
                $newEndDate,
                $startMin,
                $endMin,
                $contract->id
            );

            return [
                'error' => $error,
                'new_end_date' => $newEndDate,
                'new_start_time' => self::formatMinutesToDbTime($startMin),
                'new_end_time' => self::formatMinutesToDbTime($endMin),
            ];
        }

        $isCustomSingle = !$booking || !$booking->id_service;
        if ($isCustomSingle) {
            $origStartMin = self::parseTimeToMinutes($booking?->jam_mulai ?: '08:00') ?? 480;
            $origEndMin = $booking?->jam_selesai
                ? (self::parseEndTimeToMinutes($booking->jam_selesai) ?? ($origStartMin + 120))
                : ($origStartMin + max(1, (int) ($booking?->effective_duration_hours ?? 2)) * 60);
            $durationMin = max(60, $origEndMin - $origStartMin);
            $endMin = $startMin + $durationMin;

            if ($endMin > 1440) {
                return [
                    'error' => 'Rentang jam baru melewati batas tengah malam.',
                    'new_end_date' => $newStartDate,
                    'new_start_time' => self::formatMinutesToDbTime($startMin),
                    'new_end_time' => self::formatMinutesToDbTime(1439),
                ];
            }

            $error = self::validateCustomOfferSchedule(
                $photographerId,
                $newStartDate,
                $newStartDate,
                $startMin,
                $endMin,
                $contract->id
            );

            return [
                'error' => $error,
                'new_end_date' => $newStartDate,
                'new_start_time' => self::formatMinutesToDbTime($startMin),
                'new_end_time' => self::formatMinutesToDbTime($endMin),
            ];
        }

        if ($startMin % 60 !== 0) {
            return [
                'error' => 'Jadwal sesi reguler harus dimulai pada jam bulat (contoh 09:00, 10:00).',
                'new_end_date' => $newStartDate,
                'new_start_time' => self::formatMinutesToDbTime($startMin),
                'new_end_time' => self::formatMinutesToDbTime($startMin + 60),
            ];
        }

        $durationHours = max(1, (int) $booking->effective_duration_hours);
        $endMin = $startMin + ($durationHours * 60);
        if ($endMin > 1440) {
            return [
                'error' => "Durasi sesi {$durationHours} jam melewati batas akhir hari.",
                'new_end_date' => $newStartDate,
                'new_start_time' => self::formatMinutesToDbTime($startMin),
                'new_end_time' => self::formatMinutesToDbTime(1439),
            ];
        }

        $targetOptions = [
            'spare_before_minutes' => (int) ($booking->spare_before_minutes ?? 0),
            'spare_after_minutes' => (int) ($booking->spare_after_minutes ?? 0),
            'allow_same_location_bypass' => (bool) ($booking->allow_same_location_bypass ?? false),
            'latitude' => $booking->latitude !== null ? (float) $booking->latitude : null,
            'longitude' => $booking->longitude !== null ? (float) $booking->longitude : null,
        ];

        $error = self::validateRegularBookingSlot(
            $photographerId,
            $newStartDate,
            $startMin,
            $endMin,
            $targetOptions,
            $contract->id
        );

        return [
            'error' => $error,
            'new_end_date' => $newStartDate,
            'new_start_time' => self::formatMinutesToDbTime($startMin),
            'new_end_time' => self::formatMinutesToDbTime($endMin),
        ];
    }
}
