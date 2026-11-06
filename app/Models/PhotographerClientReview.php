<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhotographerClientReview extends Model
{
    use HasFactory;

    protected $table = 'photographer_client_reviews';

    protected $fillable = [
        'id_contract',
        'id_photographer',
        'id_client',
        'rating',
        'ulasan',
        'ketepatan_waktu',
        'komunikasi',
        'kerjasama',
    ];

    protected $casts = [
        'rating' => 'integer',
        'ketepatan_waktu' => 'integer',
        'komunikasi' => 'integer',
        'kerjasama' => 'integer',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'id_contract');
    }

    public function photographer()
    {
        return $this->belongsTo(RolePhotographer::class, 'id_photographer');
    }

    public function client()
    {
        return $this->belongsTo(RoleClient::class, 'id_client');
    }
}

