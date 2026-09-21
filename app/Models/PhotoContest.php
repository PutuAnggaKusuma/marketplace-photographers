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
        'user_id',
        'judul_lomba',
        'kategori',
        'deskrpisi_lomba',
        'syarat_ketentuan',
        'catatan_admin',
        'start_date',
        'end_date',
        'hadiah',
        'biaya_pendaftaran',
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
        'max_submissions',
        'allow_link',
        'registration_start',
        'registration_end',
        'submission_start',
        'submission_end',
        'judging_start',
        'judging_end',
        'announcement_date',
        'status',
        'penyelenggara',
        'banner_url',
        'view_count',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'registration_start' => 'datetime',
        'registration_end' => 'datetime',
        'submission_start' => 'datetime',
        'submission_end' => 'datetime',
        'judging_start' => 'datetime',
        'judging_end' => 'datetime',
        'announcement_date' => 'datetime',
        'biaya_pendaftaran' => 'float',
        'max_submissions' => 'integer',
        'allow_link' => 'boolean',
        'view_count' => 'integer',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'id_admin');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function registrations()
    {
        return $this->hasMany(ContestRegistration::class, 'id_contest');
    }

    public function submissions()
    {
        return $this->hasMany(ContestSubmission::class, 'id_contest');
    }

    /**
     * Helper untuk menentukan tahapan lomba yang sedang aktif saat ini.
     * Return: 'pendaftaran' | 'verifikasi' | 'pengumpulan_karya' | 'penjurian' | 'selesai'
     */
    public function getCurrentStageAttribute(): string
    {
        if ($this->status === 'selesai') {
            return 'selesai';
        }

        $now = now();

        if ($this->announcement_date && $now->gte($this->announcement_date)) {
            return 'selesai';
        }

        if ($this->judging_start && $this->judging_end && $now->between($this->judging_start, $this->judging_end)) {
            return 'penjurian';
        }

        if ($this->submission_start && $this->submission_end && $now->between($this->submission_start, $this->submission_end)) {
            return 'pengumpulan_karya';
        }

        if ($this->registration_start && $this->registration_end) {
            if ($now->between($this->registration_start, $this->registration_end)) {
                return 'pendaftaran';
            } elseif ($now->gt($this->registration_end) && (!$this->submission_start || $now->lt($this->submission_start))) {
                return 'verifikasi';
            }
        }

        // Fallback berdasarkan status umum lomba
        if ($this->status === 'buka') {
            return 'pendaftaran';
        }

        return 'selesai';
    }
}