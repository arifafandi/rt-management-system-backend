<?php

namespace Database\Seeders;

use App\Models\House;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 20 houses (numbered from 1 to 20)
        for ($i = 1; $i <= 20; $i++) {
            House::create([
                'house_number' => 'H-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'occupancy_status' => $i <= 15 ? 'occupied' : ($i <= 17 ? 'occupied' : 'vacant'),
            ]);
        }
    }
}
