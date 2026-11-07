<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoleClient extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'role_clients';

    protected $fillable = [
        'id_user',
        'nama',
        'nomor_telepon',
        'foto',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class, 'id_client');
    }

    public function favorites()
    {
        return $this->hasMany(ClientFavorite::class, 'id_client');
    }

    public function favoritePhotographers()
    {
        return $this->belongsToMany(RolePhotographer::class, 'client_photographer_favorites', 'id_client', 'id_photographer')->withTimestamps();
    }

    public function receivedPhotographerReviews()
    {
        return $this->hasMany(PhotographerClientReview::class, 'id_client')->latest();
    }

    public function getAveragePhotographerRatingAttribute(): ?float
    {
        $reviews = $this->relationLoaded('receivedPhotographerReviews') 
            ? $this->receivedPhotographerReviews 
            : $this->receivedPhotographerReviews();
            
        $avg = $reviews->avg('rating');
        return $avg !== null ? round((float) $avg, 1) : null;
    }
}
