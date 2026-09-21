<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ElearningModule extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'elearning_modules';

    protected $fillable = [
        'id_elearning',
        'judul_modul',
        'deskripsi',
        'urutan',
    ];

    protected $casts = [
        'urutan' => 'integer',
    ];

    /**
     * Relasi ke Kursus E-Learning induk.
     */
    public function elearning()
    {
        return $this->belongsTo(Elearning::class, 'id_elearning');
    }

    /**
     * Relasi ke Tahapan Belajar / Lessons di dalam modul ini.
     */
    public function lessons()
    {
        return $this->hasMany(ElearningLesson::class, 'id_module')->orderBy('urutan', 'asc');
    }
}
