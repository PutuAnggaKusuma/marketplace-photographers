<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientFavorite extends Model
{
    use HasFactory;

    protected $table = 'client_photographer_favorites';

    protected $fillable = [
        'id_client',
        'id_photographer',
    ];

    public function client()
    {
        return $this->belongsTo(RoleClient::class, 'id_client');
    }

    public function photographer()
    {
        return $this->belongsTo(RolePhotographer::class, 'id_photographer');
    }
}

