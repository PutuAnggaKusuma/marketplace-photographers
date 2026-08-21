<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatBooking extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'chat_bookings';

    protected $fillable = [
        'id_photographer',
        'id_client',
    ];

    public function photographer()
    {
        return $this->belongsTo(RolePhotographer::class, 'id_photographer');
    }

    public function client()
    {
        return $this->belongsTo(RoleClient::class, 'id_client');
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'id_chat_booking');
    }

    public function lastMessage()
    {
        return $this->hasOne(ChatMessage::class, 'id_chat_booking')->latestOfMany();
    }
}