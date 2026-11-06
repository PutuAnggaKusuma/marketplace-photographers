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
        'jam_mulai',
        'jam_selesai',
        'lokasi',
        'province_code',
        'city_code',
        'catatan_khusus',
        'hasil_foto_url',
        'catatan_fotografer',
        'durasi',
        'reschedule_by',
        'reschedule_date',
        'reschedule_time',
        'reschedule_reason',
        'reschedule_status',
        'status_booking',
    ];

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
            ->whereBetween('booking_date', [$today, $tomorrow])
            ->whereHas('contract', function ($q) {
                $q->whereNotIn('status_contract', ['cancelled', 'completed']);
            })
            ->orderBy('booking_date', 'asc')
            ->orderBy('jam_mulai', 'asc');
    }

    public function getIsTodayAttribute(): bool
    {
        return $this->booking_date === now()->toDateString();
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
        $start = $this->session_date_time;
        $durationHours = (int) ($this->durasi ?: ($this->service->durasi_jam ?? 3));
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