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
        'status_booking',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'id_contract');
    }

    public function service()
    {
        return $this->belongsTo(PhotographerService::class, 'id_service');
    }

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_code', 'code');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_code', 'code');
    }
}