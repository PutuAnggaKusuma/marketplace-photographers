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
}