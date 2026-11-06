<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ElearningTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'elearning_transactions';

    protected $fillable = [
        'id_user',
        'id_e_learning',
        'invoice_code',
        'nominal',
        'payment_method',
        'payment_status',
        'paid_at',
    ];

    protected $casts = [
        'nominal' => 'float',
        'paid_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function elearning()
    {
        return $this->belongsTo(Elearning::class, 'id_e_learning');
    }
}

