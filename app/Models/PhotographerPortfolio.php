<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PhotographerPortfolio extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'photographer_portofolios';

    protected $fillable = [
        'id_photographer',
        'judul',
        'deskripsi',
    ];

    public function photographer()
    {
        return $this->belongsTo(RolePhotographer::class, 'id_photographer');
    }

    public function medias()
    {
        return $this->hasMany(PortfolioMedia::class, 'id_portofolio');
    }
}