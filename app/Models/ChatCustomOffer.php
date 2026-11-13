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
        'booking_end_date',
        'booking_time',
        'jam_selesai',
        'durasi_jam',
        'lokasi',
        'harga',
        'status',
        'id_contract',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'booking_end_date' => 'date',
        'harga' => 'decimal:2',
        'durasi_jam' => 'integer',
    ];

    public function getEffectiveEndDateAttribute()
    {
        return $this->booking_end_date ?: $this->booking_date;
    }

    public function getIsMultiDayAttribute(): bool
    {
        if (!$this->booking_date || !$this->booking_end_date) {
            return false;
        }
        return $this->booking_end_date->gt($this->booking_date);
    }

    public function getDayCountAttribute(): int
    {
        if (!$this->booking_date) {
            return 1;
        }
        $end = $this->booking_end_date ?: $this->booking_date;
        return max(1, (int) $this->booking_date->diffInDays($end) + 1);
    }

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

