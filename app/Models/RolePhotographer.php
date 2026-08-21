<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\Province;

class RolePhotographer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'role_photographers';

    protected $fillable = [
        'id_user',
        'nama',
        'nomor_telepon',
        'link_sosmed',
        'foto',
        'alamat',
        'province_code',
        'city_code',
        'deskripsi_bio',
        'is_verified',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_code', 'code');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_code', 'code');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'photographer_categories', 'id_photographer', 'id_category');
    }

    public function services()
    {
        return $this->hasMany(PhotographerService::class, 'id_photographer');
    }

    public function portfolios()
    {
        return $this->hasMany(PhotographerPortfolio::class, 'id_photographer');
    }

    public function availabilities()
    {
        return $this->hasMany(PhotographerAvailability::class, 'id_photographer');
    }

    public function testimonials()
    {
        return $this->hasMany(Testimonial::class, 'id_photographer');
    }

    public function getRatingAverageAttribute()
    {
        return round($this->testimonials()->avg('rating') ?? 4.9, 1);
    }

    public function getFotoUrlAttribute()
    {
        if (!$this->foto) {
            return 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400&q=80';
        }
        if (str_starts_with($this->foto, 'http://') || str_starts_with($this->foto, 'https://')) {
            return $this->foto;
        }
        return asset('storage/' . $this->foto);
    }
}
