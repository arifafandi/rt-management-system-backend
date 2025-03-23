<?php

namespace Database\Seeders;

use App\Models\House;
use App\Models\HouseResident;
use App\Models\Resident;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HouseResidentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Assign permanent residents to the first 15 houses
        for ($i = 1; $i <= 15; $i++) {
            $house = House::find($i);
            $resident = Resident::where('resident_status', 'permanent')
                ->skip($i - 1)
                ->first();

            if ($house && $resident) {
                HouseResident::create([
                    'house_id' => $house->id,
                    'resident_id' => $resident->id,
                    'start_date' => Carbon::now()->subMonths(rand(1, 24))->format('Y-m-d'),
                    'end_date' => null,
                    'is_current' => true,
                ]);
            }
        }

        // Assign contract residents to houses 16-17 (currently occupied)
        for ($i = 16; $i <= 17; $i++) {
            $house = House::find($i);
            $resident = Resident::where('resident_status', 'contract')
                ->skip($i - 16)
                ->first();

            if ($house && $resident) {
                HouseResident::create([
                    'house_id' => $house->id,
                    'resident_id' => $resident->id,
                    'start_date' => Carbon::now()->subMonths(rand(1, 6))->format('Y-m-d'),
                    'end_date' => Carbon::now()->addMonths(rand(1, 6))->format('Y-m-d'),
                    'is_current' => true,
                ]);
            }
        }

        // Create historical data for houses 18-20 (previous residents)
        for ($i = 18; $i <= 20; $i++) {
            $house = House::find($i);
            $resident = Resident::where('resident_status', 'contract')
                ->skip($i - 15)
                ->first();

            if ($house && $resident) {
                $startDate = Carbon::now()->subMonths(rand(6, 12));
                $endDate = $startDate->copy()->addMonths(rand(3, 6));

                HouseResident::create([
                    'house_id' => $house->id,
                    'resident_id' => $resident->id,
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d'),
                    'is_current' => false,
                ]);
            }
        }

        // Add historical data for some houses
        $pastResidents = Resident::where('id', '>', 20)->get();
        $pastResidentIndex = 0;

        // Add 1-2 past residents to random houses for historical data
        for ($i = 1; $i <= 10; $i++) {
            $houseId = rand(1, 20);
            $resident = $pastResidents[$pastResidentIndex % $pastResidents->count()];
            $pastResidentIndex++;

            $endDate = Carbon::now()->subMonths(rand(1, 12));
            $startDate = $endDate->copy()->subMonths(rand(3, 12));

            HouseResident::create([
                'house_id' => $houseId,
                'resident_id' => $resident->id,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'is_current' => false,
            ]);
        }
    }
}
