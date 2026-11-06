<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $table = 'activity_logs';

    protected $fillable = [
        'id_user',
        'log_name',
        'description',
        'subject_type',
        'subject_id',
        'properties',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    /**
     * Relationship to the user who triggered the activity.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /**
     * Polymorphic relation to subject (Contract, Category, User, PhotoContest, etc.)
     */
    public function subject()
    {
        return $this->morphTo(__FUNCTION__, 'subject_type', 'subject_id');
    }

    /**
     * Helper to easily record activity across the platform.
     */
    public static function record(string $logName, string $description, $subject = null, array $properties = [], ?int $userId = null): self
    {
        return self::create([
            'id_user' => $userId ?? (auth()->check() ? auth()->id() : null),
            'log_name' => $logName,
            'description' => $description,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject ? $subject->getKey() : null,
            'properties' => !empty($properties) ? $properties : null,
        ]);
    }
}
