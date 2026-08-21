<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'contracts';

    protected $fillable = [
        'id_client',
        'id_photographer',
        'jumlah',
        'payment_type',
        'jumlah_dp',
        'fee_marketplace',
        'payout_amount',
        'status_payout',
        'payout_at',
        'payout_notes',
        'is_validated_photographer',
        'is_validated_client',
        'expired_at',
        'status_contract',
    ];

    protected $casts = [
        'payout_at' => 'datetime',
        'expired_at' => 'datetime',
        'fee_marketplace' => 'float',
        'payout_amount' => 'float',
        'jumlah' => 'float',
        'jumlah_dp' => 'float',
    ];

    public function client()
    {
        return $this->belongsTo(RoleClient::class, 'id_client');
    }

    public function photographer()
    {
        return $this->belongsTo(RolePhotographer::class, 'id_photographer');
    }

    public function bookingDetail()
    {
        return $this->hasOne(ContractBooking::class, 'id_contract');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'id_contract');
    }

    public function testimonial()
    {
        return $this->hasOne(Testimonial::class, 'id_contract');
    }
}