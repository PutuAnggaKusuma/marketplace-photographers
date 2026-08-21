<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PhotographerAvailability extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'photographer_availability';

    protected $fillable = [
        'id_photographer',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'status',
        'keterangan',
    ];

    public function photographer()
    {
        return $this->belongsTo(RolePhotographer::class, 'id_photographer');
    }
}