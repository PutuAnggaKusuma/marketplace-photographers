<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatCustomOffer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'chat_custom_offers';

    protected $fillable = [
        'id_chat_booking',
        'id_photographer',
        'id_client',
        'judul_penawaran',
        'deskripsi',
        'booking_date',
        'booking_time',
        'durasi_jam',
        'lokasi',
        'harga',
        'status',
        'id_contract',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'harga' => 'decimal:2',
        'durasi_jam' => 'integer',
    ];

    public function chatBooking()
    {
        return $this->belongsTo(ChatBooking::class, 'id_chat_booking');
    }

    public function photographer()
    {
        return $this->belongsTo(RolePhotographer::class, 'id_photographer');
    }

    public function client()
    {
        return $this->belongsTo(RoleClient::class, 'id_client');
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'id_contract');
    }

    public function message()
    {
        return $this->hasOne(ChatMessage::class, 'id_custom_offer');
    }
}

