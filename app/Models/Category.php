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

    public function photographers()
    {
        return $this->belongsToMany(RolePhotographer::class, 'photographer_categories', 'id_category', 'id_photographer');
    }
}