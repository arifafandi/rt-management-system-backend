<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'house_resident_id',
        'payment_type', // 'security' or 'cleaning'
        'amount',
        'payment_date',
        'payment_period', // 'monthly' or 'yearly'
        'period_start',
        'period_end',
        'is_paid',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
        'period_start' => 'date',
        'period_end' => 'date',
        'is_paid' => 'boolean',
    ];

    public function houseResident()
    {
        return $this->belongsTo(HouseResident::class);
    }
}
