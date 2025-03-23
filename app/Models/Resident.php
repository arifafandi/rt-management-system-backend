<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resident extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'id_card_photo',
        'resident_status', // 'permanent' or 'contract'
        'phone_number',
        'is_married',
    ];

    protected $casts = [
        'is_married' => 'boolean',
    ];

    public function houses()
    {
        return $this->belongsToMany(House::class, 'house_residents')
            ->withPivot('start_date', 'end_date', 'is_current')
            ->withTimestamps();
    }

    public function currentHouse()
    {
        return $this->belongsToMany(House::class, 'house_residents')
            ->wherePivot('is_current', true)
            ->first();
    }

    public function houseResidents()
    {
        return $this->hasMany(HouseResident::class);
    }
}
