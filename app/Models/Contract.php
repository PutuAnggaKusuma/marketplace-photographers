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
        'payout_requested_at',
        'payout_notes',
        'is_validated_photographer',
        'is_validated_client',
        'expired_at',
        'status_contract',
    ];

    protected $casts = [
        'payout_at' => 'datetime',
        'payout_requested_at' => 'datetime',
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

    public function detail()
    {
        return $this->bookingDetail();
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'id_contract');
    }

    public function testimonial()
    {
        return $this->hasOne(Testimonial::class, 'id_contract');
    }

    public function cancellation()
    {
        return $this->hasOne(ContractCancellation::class, 'id_contract');
    }

    public function photographerClientReview()
    {
        return $this->hasOne(PhotographerClientReview::class, 'id_contract');
    }

    /**
     * Standardized Contract Code (Rule #89 Compliant: clean prefix KTRK-, zero hash symbols)
     */
    public function getContractCodeAttribute(): string
    {
        return 'KTRK-' . str_pad($this->id, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Standardized Dynamic Invoice Code (Rule #94 Compliant: derived from payment external_id or dynamic DB attributes)
     */
    public function getInvoiceCodeAttribute(): string
    {
        $payment = $this->relationLoaded('payments') ? $this->payments->first() : $this->payments()->first();
        if ($payment && !empty($payment->external_id) && !str_contains($payment->external_id, 'XXXX')) {
            return $payment->external_id;
        }
        $year = $this->created_at ? $this->created_at->format('Y') : date('Y');
        return 'INV-LM-' . $year . '-' . str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }
}