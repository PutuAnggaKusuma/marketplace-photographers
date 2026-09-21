<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContestRegistration extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'contest_registrations';

    protected $fillable = [
        'id_contest',
        'id_user',
        'nama_lengkap',
        'email',
        'no_wa',
        'asal_kota',
        'instansi',
        'bio_singkat',
        'motivasi',
        'cv_portfolio_file',
        'portfolio_link',
        'status_pendaftaran',
        'status_pembayaran',
        'bukti_transfer',
        'waktu_batas_pembayaran',
        'catatan_admin',
    ];

    protected $casts = [
        'waktu_batas_pembayaran' => 'datetime',
    ];

    public function contest()
    {
        return $this->belongsTo(PhotoContest::class, 'id_contest');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function submissions()
    {
        return $this->hasMany(ContestSubmission::class, 'id_registration');
    }

    public function isApprovedAndReady(): bool
    {
        if ($this->status_pendaftaran !== 'disetujui') {
            return false;
        }

        if ($this->status_pembayaran === 'gratis' || $this->status_pembayaran === 'lunas') {
            return true;
        }

        return false;
    }
}
