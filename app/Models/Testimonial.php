<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Testimonial extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'testimonials';

    protected $fillable = [
        'id_client',
        'id_photographer',
        'id_contract',
        'deskripsi_review',
        'rating',
        'is_hidden',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_hidden' => 'boolean',
    ];

    public function client()
    {
        return $this->belongsTo(RoleClient::class, 'id_client', 'id');
    }

    public function photographer()
    {
        return $this->belongsTo(RolePhotographer::class, 'id_photographer', 'id');
    }

    public function contract()
    {
        return $this->belongsTo(ContractBooking::class, 'id_contract', 'id');
    }
}