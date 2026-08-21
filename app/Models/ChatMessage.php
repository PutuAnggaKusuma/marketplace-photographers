<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatMessage extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'chat_messages';

    protected $fillable = [
        'id_chat_booking',
        'id_sender',
        'message',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function chatBooking()
    {
        return $this->belongsTo(ChatBooking::class, 'id_chat_booking');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'id_sender');
    }
}