<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractCancellation extends Model
{
    use HasFactory;

    protected $table = 'contract_cancellations';

    protected $fillable = [
        'id_contract',
        'alasan',
        'dibatalkan_oleh',
        'refund_amount',
    ];

    protected $casts = [
        'refund_amount' => 'float',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'id_contract');
    }
}
