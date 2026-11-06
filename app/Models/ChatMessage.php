<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatMessage extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'chat_messages';

    protected $touches = ['chatBooking'];

    protected $fillable = [
        'id_chat_booking',
        'id_sender',
        'reply_to_id',
        'id_custom_offer',
        'message',
        'attachment_url',
        'attachment_type',
        'is_read',
        'is_edited',
        'edited_at',
        'deleted_for_all',
        'deleted_by_users',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'is_edited' => 'boolean',
        'edited_at' => 'datetime',
        'deleted_for_all' => 'boolean',
        'deleted_by_users' => 'array',
    ];

    public function chatBooking()
    {
        return $this->belongsTo(ChatBooking::class, 'id_chat_booking');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'id_sender');
    }

    public function replyTo()
    {
        return $this->belongsTo(ChatMessage::class, 'reply_to_id');
    }

    public function customOffer()
    {
        return $this->belongsTo(ChatCustomOffer::class, 'id_custom_offer');
    }
}
