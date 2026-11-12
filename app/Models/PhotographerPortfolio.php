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
        'id_kategori',
        'judul',
        'deskripsi',
    ];

    public function photographer()
    {
        return $this->belongsTo(RolePhotographer::class, 'id_photographer');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'id_kategori')->withTrashed();
    }

    public function medias()
    {
        return $this->hasMany(PortfolioMedia::class, 'id_portofolio');
    }
}