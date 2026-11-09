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

    /**
     * Standardized Dynamic Invoice Code (Rule #94 Compliant: derived from external_id or dynamic DB attributes)
     */
    public function getInvoiceCodeAttribute(): string
    {
        if (!empty($this->external_id) && !str_contains($this->external_id, 'XXXX')) {
            return $this->external_id;
        }
        $year = $this->created_at ? $this->created_at->format('Y') : date('Y');
        return 'INV-LM-' . $year . '-' . str_pad($this->id_contract ?? $this->id, 4, '0', STR_PAD_LEFT);
    }
}