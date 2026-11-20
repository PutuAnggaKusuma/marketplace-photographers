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
        return $this->belongsTo(RoleAdmin::class, 'id_admin');
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
        if (in_array($this->status, ['selesai', 'ditutup', 'ditolak'])) {
            return 'selesai';
        }

        $now = now();

        if ($this->announcement_date && $now->gte($this->announcement_date)) {
            return 'selesai';
        }

        // Jika batas akhir lomba (end_date) sudah terlewat, tahapan resmi berakhir/selesai
        if ($this->end_date && $now->gt(\Carbon\Carbon::parse($this->end_date)->endOfDay())) {
            return 'selesai';
        }

        $regStart = $this->registration_start ? \Carbon\Carbon::parse($this->registration_start)->startOfDay() : null;
        $regEnd = $this->registration_end ? \Carbon\Carbon::parse($this->registration_end)->endOfDay() : null;

        $subStart = $this->submission_start ? \Carbon\Carbon::parse($this->submission_start)->startOfDay() : null;
        $subEnd = $this->submission_end ? \Carbon\Carbon::parse($this->submission_end)->endOfDay() : null;

        $judgeStart = $this->judging_start ? \Carbon\Carbon::parse($this->judging_start)->startOfDay() : null;
        $judgeEnd = $this->judging_end ? \Carbon\Carbon::parse($this->judging_end)->endOfDay() : null;

        if ($judgeStart && $judgeEnd && $now->between($judgeStart, $judgeEnd)) {
            return 'penjurian';
        }

        if ($subStart && $subEnd && $now->between($subStart, $subEnd)) {
            return 'pengumpulan_karya';
        }

        if ($regStart && $regEnd) {
            if ($now->between($regStart, $regEnd)) {
                return 'pendaftaran';
            } elseif ($now->gt($regEnd) && (!$subStart || $now->lt($subStart))) {
                return 'verifikasi';
            }
        }

        if ($subEnd && $now->gt($subEnd) && (!$judgeStart || $now->lt($judgeStart))) {
            return 'verifikasi';
        }

        // Fallback berdasarkan status umum lomba
        if ($this->status === 'buka') {
            return 'pendaftaran';
        }

        return 'selesai';
    }
}