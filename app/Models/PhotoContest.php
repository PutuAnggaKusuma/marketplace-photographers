<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PhotoContest extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'photo_contests';

    protected $fillable = [
        'id_admin',
        'judul_lomba',
        'kategori',
        'deskrpisi_lomba',
        'start_date',
        'end_date',
        'hadiah',
        'status',
        'penyelenggara',
        'banner_url',
        'view_count',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'view_count' => 'integer',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'id_admin');
    }

    public function submissions()
    {
        return $this->hasMany(ContestSubmission::class, 'id_contest');
    }
}