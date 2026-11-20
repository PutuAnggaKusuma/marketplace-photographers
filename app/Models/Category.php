<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'categories';

    protected $fillable = [
        'nama_kategori',
        'slug',
        'deskripsi',
        'icon_url',
    ];

    protected static function booted()
    {
        static::deleting(function ($category) {
            if (!$category->isForceDeleting()) {
                $category->nama_kategori = $category->nama_kategori . ' [deleted-' . $category->id . ']';
                $category->slug = $category->slug . '-deleted-' . $category->id;
                $category->saveQuietly();
            }
        });
    }

    public function photographers()
    {
        return $this->belongsToMany(RolePhotographer::class, 'photographer_categories', 'id_category', 'id_photographer');
    }

    public function portfolios()
    {
        return $this->hasMany(PhotographerPortfolio::class, 'id_kategori');
    }
}