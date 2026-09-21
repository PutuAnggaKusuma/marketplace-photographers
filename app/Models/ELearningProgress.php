<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ELearningProgress extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'e_learning_progress';

    protected $fillable = [
        'id_user',
        'id_e_learning',
        'status',
        'progress_percentage',
        'completed_at',
    ];

    protected $casts = [
        'progress_percentage' => 'integer',
        'completed_at' => 'datetime',
    ];

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /**
     * Relasi ke Elearning Course
     */
    public function elearning()
    {
        return $this->belongsTo(Elearning::class, 'id_e_learning');
    }
}
