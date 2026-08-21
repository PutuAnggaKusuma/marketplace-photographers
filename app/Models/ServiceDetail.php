<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'service_details';

    protected $fillable = [
        'id_p_service',
        'nama_fitur',
        'tarif_harga',
    ];

    public function service()
    {
        return $this->belongsTo(PhotographerService::class, 'id_p_service');
    }
}