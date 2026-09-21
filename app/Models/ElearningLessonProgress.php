<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElearningLessonProgress extends Model
{
    use HasFactory;

    protected $table = 'elearning_lesson_progress';

    protected $fillable = [
        'id_user',
        'id_elearning',
        'id_lesson',
        'is_completed',
        'completed_at',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function elearning()
    {
        return $this->belongsTo(Elearning::class, 'id_elearning');
    }

    public function lesson()
    {
        return $this->belongsTo(ElearningLesson::class, 'id_lesson');
    }
}
