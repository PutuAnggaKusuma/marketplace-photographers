<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContestSubmission extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'contest_submissions';

    protected $fillable = [
        'id_contest',
        'id_user',
        'id_registration',
        'judul_karya',
        'deskripsi_karya',
        'image_url',
        'media_link',
        'status_submission',
        'catatan_juri',
    ];

    public function contest()
    {
        return $this->belongsTo(PhotoContest::class, 'id_contest');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function registration()
    {
        return $this->belongsTo(ContestRegistration::class, 'id_registration');
    }
}