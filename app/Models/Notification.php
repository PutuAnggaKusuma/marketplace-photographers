<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $fillable = [
        'id_user',
        'type',
        'title',
        'message',
        'data',
        'is_read',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /**
     * Helper static untuk memicu pengiriman notifikasi dari mana saja.
     */
    public static function send($userId, $title, $message, $type = 'info', $targetUrl = null)
    {
        return static::create([
            'id_user' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $targetUrl ? ['url' => $targetUrl] : null,
            'is_read' => false,
        ]);
    }
}