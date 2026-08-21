<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'payments';

    protected $fillable = [
        'id_contract',
        'payment_amount',
        'payment_status',
        'contract_payment_type',
        'payment_link',
        'bukti_transfer',
        'payment_type',
        'external_id',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'id_contract');
    }
}