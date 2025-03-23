<?php

namespace Database\Seeders;

use App\Models\HouseResident;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get current house residents
        $currentResidents = HouseResident::where('is_current', true)->get();

        // Initialize month counters for each payment type
        $months = 12; // Generate one year of data

        // Current date for reference
        $currentDate = Carbon::now();

        // Generate payment records for each month
        for ($month = 0; $month < $months; $month++) {
            // Calculate payment date for this month
            $paymentDate = $currentDate->copy()->subMonths($month);
            $monthStart = $paymentDate->copy()->startOfMonth();
            $monthEnd = $monthStart->copy()->endOfMonth();

            // For each resident, create security and cleaning payments
            foreach ($currentResidents as $resident) {
                // Skip if resident moved in after this payment date
                $residentStartDate = Carbon::parse($resident->start_date);
                if ($residentStartDate->gt($monthEnd)) {
                    continue;
                }

                // Security payment (100k monthly)
                Payment::create([
                    'house_resident_id' => $resident->id,
                    'payment_type' => 'security',
                    'amount' => 100000,
                    'payment_date' => $paymentDate->format('Y-m-d'),
                    'payment_period' => 'monthly',
                    'period_start' => $monthStart->format('Y-m-d'),
                    'period_end' => $monthEnd->format('Y-m-d'),
                    'is_paid' => $month < 3 || rand(0, 10) > 2, // Most recent 3 months always paid, others 80% chance
                ]);

                // Cleaning payment (15k monthly)
                Payment::create([
                    'house_resident_id' => $resident->id,
                    'payment_type' => 'cleaning',
                    'amount' => 15000,
                    'payment_date' => $paymentDate->format('Y-m-d'),
                    'payment_period' => 'monthly',
                    'period_start' => $monthStart->format('Y-m-d'),
                    'period_end' => $monthEnd->format('Y-m-d'),
                    'is_paid' => $month < 3 || rand(0, 10) > 2, // Most recent 3 months always paid, others 80% chance
                ]);
            }

            // For some residents, create yearly payments instead of monthly
            if ($month % 12 == 0) { // Yearly payments at start of year
                // Select a few residents for yearly payments
                $yearlyPaymentResidents = $currentResidents->random(min(5, $currentResidents->count()));

                foreach ($yearlyPaymentResidents as $resident) {
                    // Skip if resident moved in after this payment date
                    $residentStartDate = Carbon::parse($resident->start_date);
                    if ($residentStartDate->gt($monthEnd)) {
                        continue;
                    }

                    $yearStart = $monthStart->copy();
                    $yearEnd = $yearStart->copy()->addMonths(11)->endOfMonth();

                    // Yearly security payment (100k * 12 with 5% discount)
                    $yearlyAmount = 100000 * 12 * 0.95;

                    Payment::create([
                        'house_resident_id' => $resident->id,
                        'payment_type' => 'security',
                        'amount' => $yearlyAmount,
                        'payment_date' => $yearStart->format('Y-m-d'),
                        'payment_period' => 'yearly',
                        'period_start' => $yearStart->format('Y-m-d'),
                        'period_end' => $yearEnd->format('Y-m-d'),
                        'is_paid' => true, // All yearly payments are paid
                    ]);
                }
            }
        }
    }
}
