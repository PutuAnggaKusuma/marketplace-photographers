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
    ];

    public function photographer()
    {
        return $this->belongsTo(RolePhotographer::class, 'id_photographer');
    }

    public function details()
    {
        return $this->hasMany(ServiceDetail::class, 'id_p_service');
    }
}