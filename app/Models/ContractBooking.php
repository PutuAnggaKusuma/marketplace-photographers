<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\Province;

class ContractBooking extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'contract_bookings';

    protected $fillable = [
        'id_contract',
        'id_service',
        'booking_date',
        'booking_end_date',
        'jam_mulai',
        'jam_selesai',
        'lokasi',
        'latitude',
        'longitude',
        'province_code',
        'city_code',
        'catatan_khusus',
        'hasil_foto_url',
        'catatan_fotografer',
        'durasi',
        'spare_before_minutes',
        'spare_after_minutes',
        'allow_same_location_bypass',
        'reschedule_by',
        'reschedule_date',
        'reschedule_time',
        'reschedule_reason',
        'reschedule_status',
        'status_booking',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'spare_before_minutes' => 'integer',
        'spare_after_minutes' => 'integer',
        'allow_same_location_bypass' => 'boolean',
    ];

    public function getEffectiveEndDateAttribute(): ?string
    {
        return $this->booking_end_date ?: $this->booking_date;
    }

    public function getIsMultiDayAttribute(): bool
    {
        return !empty($this->booking_date)
            && !empty($this->booking_end_date)
            && $this->booking_end_date > $this->booking_date;
    }

    public function getDayCountAttribute(): int
    {
        if (!$this->booking_date) {
            return 1;
        }
        $start = \Carbon\Carbon::parse($this->booking_date);
        $end = \Carbon\Carbon::parse($this->effective_end_date);
        return max(1, (int) $start->diffInDays($end) + 1);
    }

    public function getDateRangeLabelAttribute(): string
    {
        if (!$this->booking_date) {
            return '-';
        }
        $start = \Carbon\Carbon::parse($this->booking_date)->locale('id')->translatedFormat('d M Y');
        if ($this->is_multi_day) {
            $end = \Carbon\Carbon::parse($this->booking_end_date)->locale('id')->translatedFormat('d M Y');
            return "{$start} s/d {$end} ({$this->day_count} Hari)";
        }
        return $start;
    }

    public function getEffectiveDurationHoursAttribute(): int
    {
        if ($this->jam_mulai && $this->jam_selesai) {
            $startParts = explode(':', substr($this->jam_mulai, 0, 5));
            $endParts = explode(':', substr($this->jam_selesai, 0, 5));
            if (count($startParts) === 2 && count($endParts) === 2) {
                $diff = ((int) $endParts[0] * 60 + (int) $endParts[1]) - ((int) $startParts[0] * 60 + (int) $startParts[1]);
                if ($diff > 0) {
                    return max(1, (int) round($diff / 60));
                }
            }
        }
        $parsed = (int) preg_replace('/[^0-9]/', '', (string) $this->durasi);
        return $parsed > 0 ? $parsed : 1;
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'id_contract');
    }

    public function getBookingTimeAttribute()
    {
        return $this->jam_mulai;
    }

    public function service()
    {
        return $this->belongsTo(PhotographerService::class, 'id_service')->withTrashed();
    }

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_code', 'code');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_code', 'code');
    }

    /**
     * Scope untuk mengambil sesi foto terdekat dalam batas waktu mendesak (H-1 & Hari-H).
     */
    public function scopeUpcomingUrgent($query)
    {
        $today = now()->toDateString();
        $tomorrow = now()->copy()->addDay()->toDateString();

        return $query->whereIn('status_booking', ['confirmed', 'approved'])
            ->where(function ($q) use ($today, $tomorrow) {
                $q->whereBetween('booking_date', [$today, $tomorrow])
                  ->orWhere(function ($mq) use ($today) {
                      $mq->whereNotNull('booking_end_date')
                         ->where('booking_date', '<=', $today)
                         ->where('booking_end_date', '>=', $today);
                  });
            })
            ->whereHas('contract', function ($q) {
                $q->whereNotIn('status_contract', ['cancelled', 'completed']);
            })
            ->orderBy('booking_date', 'asc')
            ->orderBy('jam_mulai', 'asc');
    }

    public function getIsTodayAttribute(): bool
    {
        $today = now()->toDateString();
        return $this->booking_date <= $today && $this->effective_end_date >= $today;
    }

    public function getIsTomorrowAttribute(): bool
    {
        return $this->booking_date === now()->copy()->addDay()->toDateString();
    }

    public function getSessionDateTimeAttribute(): \Carbon\Carbon
    {
        $time = $this->jam_mulai ?: '08:00:00';
        if (strlen($time) === 5) {
            $time .= ':00';
        }
        return \Carbon\Carbon::parse($this->booking_date . ' ' . $time);
    }

    public function getIsOngoingAttribute(): bool
    {
        if (!$this->is_today) return false;
        if ($this->is_multi_day) return true;
        $start = $this->session_date_time;
        $durationHours = $this->effective_duration_hours;
        $end = $start->copy()->addHours($durationHours);

        return now()->between($start, $end);
    }

    public function getCountdownTextAttribute(): string
    {
        if ($this->is_ongoing) {
            return 'Sedang Berlangsung Hari Ini';
        }

        $now = now();
        $target = $this->session_date_time;

        if ($now->greaterThan($target)) {
            return 'Sesi Dijadwalkan Hari Ini';
        }

        $diffHours = (int) $now->diffInHours($target);
        $diffMinutes = (int) ($now->diffInMinutes($target) % 60);

        if ($diffHours > 0) {
            return "{$diffHours} Jam {$diffMinutes} Menit lagi";
        }

        return "{$diffMinutes} Menit lagi";
    }

    public function getGoogleMapsUrlAttribute(): string
    {
        $query = $this->lokasi ?: 'Indonesia';
        return 'https://www.google.com/maps/search/?api=1&query=' . urlencode($query);
    }
}