<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoleAdmin extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'role_admins';

    protected $fillable = [
        'id_user',
        'nama',
        'no_telepon',
        'alamat',
        'foto',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
