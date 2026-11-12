<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhotographerWorkingHour extends Model
{
    use HasFactory;

    protected $table = 'photographer_working_hours';

    protected $fillable = [
        'id_photographer',
        'day_of_week',
        'day_name',
        'is_open',
        'is_24_hours',
        'jam_buka',
        'jam_tutup',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
        'is_open' => 'boolean',
        'is_24_hours' => 'boolean',
    ];

    public function photographer()
    {
        return $this->belongsTo(RolePhotographer::class, 'id_photographer');
    }

    /**
     * Inisialisasi jadwal default 7 hari (default libur/OFF sampai fotografer mengaktifkannya).
     */
    public static function initializeDefaults(int $photographerId, bool $defaultOpen = false): array
    {
        $days = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        ];

        $records = [];
        foreach ($days as $dayNum => $dayName) {
            $records[] = self::firstOrCreate(
                [
                    'id_photographer' => $photographerId,
                    'day_of_week' => $dayNum,
                ],
                [
                    'day_name' => $dayName,
                    'is_open' => $defaultOpen,
                    'is_24_hours' => false,
                    'jam_buka' => '08:00:00',
                    'jam_tutup' => '17:00:00',
                ]
            );
        }

        return $records;
    }
}

