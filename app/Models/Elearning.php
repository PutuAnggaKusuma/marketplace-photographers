<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Elearning extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'elearnings';

    protected $fillable = [
        'judul',
        'slug',
        'kategori',
        'level',
        'durasi',
        'thumbnail_url',
        'ringkasan',
        'konten',
        'view_count',
    ];

    protected $casts = [
        'view_count' => 'integer',
    ];
}