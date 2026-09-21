<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Elearning extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'elearnings';

    protected $fillable = [
        'judul',
        'slug',
        'kategori',
        'level',
        'durasi',
        'harga',
        'is_premium',
        'thumbnail_url',
        'ringkasan',
        'konten',
        'view_count',
    ];

    protected $casts = [
        'view_count' => 'integer',
        'harga' => 'integer',
        'is_premium' => 'boolean',
    ];

    /**
     * Relasi ke topik/modul kurikulum kursus.
     */
    public function modules()
    {
        return $this->hasMany(ElearningModule::class, 'id_elearning')->orderBy('urutan', 'asc');
    }

    /**
     * Relasi ke seluruh tahapan belajar / lessons.
     */
    public function lessons()
    {
        return $this->hasMany(ElearningLesson::class, 'id_elearning')->orderBy('urutan', 'asc');
    }

    /**
     * Relasi ke tracking progress per tahapan.
     */
    public function lessonProgresses()
    {
        return $this->hasMany(ElearningLessonProgress::class, 'id_elearning');
    }

    /**
     * Relasi ke data status progress kursus keseluruhan.
     */
    public function progresses()
    {
        return $this->hasMany(ELearningProgress::class, 'id_e_learning');
    }

    /**
     * Helper progress untuk user tertentu / auth user.
     */
    public function getUserProgress($userId = null)
    {
        $userId = $userId ?? auth()->id();
        if (!$userId) return null;

        return $this->progresses()->where('id_user', $userId)->first();
    }

    /**
     * Hitung persentase progress belajar user untuk kursus ini.
     */
    public function calculateUserPercentage(?int $userId): int
    {
        if (!$userId) return 0;
        $totalLessons = $this->lessons()->count();
        if ($totalLessons === 0) {
            $courseProgress = $this->getUserProgress($userId);
            return ($courseProgress && $courseProgress->status === 'selesai') ? 100 : 0;
        }

        $completedLessons = $this->lessonProgresses()
            ->where('id_user', $userId)
            ->where('is_completed', true)
            ->count();

        return min(100, (int) round(($completedLessons / $totalLessons) * 100));
    }
}