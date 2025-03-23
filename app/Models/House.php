<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class House extends Model
{
    use HasFactory;

    protected $fillable = [
        'house_number',
        'occupancy_status', // 'occupied' or 'vacant'
    ];

    public function residents()
    {
        return $this->belongsToMany(Resident::class, 'house_residents')
            ->withPivot('start_date', 'end_date', 'is_current')
            ->withTimestamps();
    }

    public function currentResidents()
    {
//        return $this->belongsToMany(Resident::class, 'house_residents')
//            ->wherePivot('is_current', true)
//            ->get();

        return $this->hasMany(HouseResident::class)
            ->where('is_current', true)
            ->with('resident');
    }

    public function houseResidents()
    {
        return $this->hasMany(HouseResident::class);
    }
}
