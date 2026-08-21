<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PortfolioMedia extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'portofolio_medias';

    protected $fillable = [
        'id_portofolio',
        'media',
    ];

    public function portfolio()
    {
        return $this->belongsTo(PhotographerPortfolio::class, 'id_portofolio');
    }
}