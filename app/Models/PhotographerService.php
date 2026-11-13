<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PhotographerService extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'photographer_services';

    protected $fillable = [
        'id_photographer',
        'nama_layanan',
        'tarif_harga',
        'deskripsi_layanan',
        'spare_time_enabled',
        'spare_before_minutes',
        'spare_after_minutes',
        'allow_same_location_bypass',
    ];

    protected $casts = [
        'spare_time_enabled' => 'boolean',
        'spare_before_minutes' => 'integer',
        'spare_after_minutes' => 'integer',
        'allow_same_location_bypass' => 'boolean',
    ];

    public static function formatSpareMinutes(int $minutes): string
    {
        if ($minutes <= 0) {
            return '0 Menit';
        }
        $hours = intdiv($minutes, 60);
        $mins = $minutes % 60;
        if ($hours > 0 && $mins > 0) {
            return "{$hours} Jam {$mins} Menit";
        }
        if ($hours > 0) {
            return "{$hours} Jam";
        }
        return "{$mins} Menit";
    }

    public function getEffectiveSpareBeforeMinutes(): int
    {
        return $this->spare_time_enabled ? max(0, (int) $this->spare_before_minutes) : 0;
    }

    public function getEffectiveSpareAfterMinutes(): int
    {
        return $this->spare_time_enabled ? max(0, (int) $this->spare_after_minutes) : 0;
    }

    public function getSpareSummaryAttribute(): string
    {
        if (!$this->spare_time_enabled || ($this->getEffectiveSpareBeforeMinutes() === 0 && $this->getEffectiveSpareAfterMinutes() === 0)) {
            return 'Tanpa jeda';
        }
        $parts = [];
        if ($this->getEffectiveSpareBeforeMinutes() > 0) {
            $parts[] = 'Sebelum ' . self::formatSpareMinutes($this->getEffectiveSpareBeforeMinutes());
        }
        if ($this->getEffectiveSpareAfterMinutes() > 0) {
            $parts[] = 'Sesudah ' . self::formatSpareMinutes($this->getEffectiveSpareAfterMinutes());
        }
        return implode(', ', $parts);
    }

    public function photographer()
    {
        return $this->belongsTo(RolePhotographer::class, 'id_photographer');
    }

    public function details()
    {
        return $this->hasMany(ServiceDetail::class, 'id_p_service');
    }

    public function bookings()
    {
        return $this->hasMany(ContractBooking::class, 'id_service');
    }
}