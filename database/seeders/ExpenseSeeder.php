<?php

namespace Database\Seeders;

use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample expenses data for the past 12 months
        $months = 12;
        $currentDate = Carbon::now();

        $expenseTypes = [
            'security' => [
                'Gaji Satpam',
                'Peralatan Keamanan',
                'Perawatan CCTV',
                'Seragam Satpam'
            ],
            'cleaning' => [
                'Pembersihan Jalan',
                'Pengangkutan Sampah',
                'Pembersihan Area Umum',
                'Perlengkapan Kebersihan'
            ],
            'maintenance' => [
                'Perbaikan Jalan',
                'Perbaikan Lampu Jalan',
                'Perawatan Fasilitas Umum',
                'Pembersihan Saluran Air',
                'Perawatan Taman'
            ],
            'other' => [
                'Biaya Administrasi',
                'Acara Lingkungan',
                'Listrik Area Umum',
                'Konsumsi Rapat',
                'Perlengkapan Kantor'
            ]
        ];

        // Monthly recurring expenses
        for ($month = 0; $month < $months; $month++) {
            $expenseDate = $currentDate->copy()->subMonths($month);

            // Security guard salary (monthly) - Fixed
            Expense::create([
                'description' => 'Gaji Satpam',
                'amount' => 1500000,
                'expense_date' => $expenseDate->copy()->setDay(rand(1, 5))->format('Y-m-d'),
                'expense_type' => 'security'
            ]);

            // Public area electricity (monthly) - Varies
            Expense::create([
                'description' => 'Listrik Area Umum',
                'amount' => rand(200000, 300000),
                'expense_date' => $expenseDate->copy()->setDay(rand(10, 15))->format('Y-m-d'),
                'expense_type' => 'other'
            ]);

            // Garbage collection (monthly) - Fixed
            Expense::create([
                'description' => 'Pengangkutan Sampah',
                'amount' => 350000,
                'expense_date' => $expenseDate->copy()->setDay(rand(1, 10))->format('Y-m-d'),
                'expense_type' => 'cleaning'
            ]);
        }

        // Occasional expenses (not every month)
        for ($month = 0; $month < $months; $month++) {
            $expenseDate = $currentDate->copy()->subMonths($month);

            // Maintenance - Every 3 months
            if ($month % 3 == 0) {
                $maintenanceType = $expenseTypes['maintenance'][array_rand($expenseTypes['maintenance'])];

                Expense::create([
                    'description' => $maintenanceType,
                    'amount' => rand(500000, 1500000),
                    'expense_date' => $expenseDate->copy()->setDay(rand(1, 28))->format('Y-m-d'),
                    'expense_type' => 'maintenance'
                ]);
            }

            // Other random expenses - 40% chance each month
            if (rand(1, 10) <= 4) {
                $randomType = array_rand($expenseTypes);
                $randomDescription = $expenseTypes[$randomType][array_rand($expenseTypes[$randomType])];

                Expense::create([
                    'description' => $randomDescription,
                    'amount' => rand(100000, 500000),
                    'expense_date' => $expenseDate->copy()->setDay(rand(1, 28))->format('Y-m-d'),
                    'expense_type' => $randomType
                ]);
            }
        }

        // Add a few special/one-time expenses
        $specialExpenses = [
            [
                'description' => 'Acara Perayaan Kemerdekaan',
                'amount' => 2500000,
                'expense_date' => $currentDate->copy()->subMonths(2)->setDay(17)->format('Y-m-d'),
                'expense_type' => 'other'
            ],
            [
                'description' => 'Pengaspalan Jalan Utama',
                'amount' => 5000000,
                'expense_date' => $currentDate->copy()->subMonths(5)->setDay(15)->format('Y-m-d'),
                'expense_type' => 'maintenance'
            ],
            [
                'description' => 'Renovasi Pos Satpam',
                'amount' => 3500000,
                'expense_date' => $currentDate->copy()->subMonths(8)->setDay(10)->format('Y-m-d'),
                'expense_type' => 'security'
            ]
        ];

        foreach ($specialExpenses as $expense) {
            Expense::create($expense);
        }
    }
}
