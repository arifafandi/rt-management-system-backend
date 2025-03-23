<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\House;
use App\Models\HouseResident;
use App\Models\Payment;
use App\Models\Resident;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function dashboard(Request $request)
    {
        $year = $request->input('year', Carbon::now()->year);

        // Data basic
        $residentCount = Resident::count();
        $totalHouses = House::count();
        $occupiedHouses = House::where('occupancy_status', 'occupied')->count();

        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        // Ambil rumah yang terisi
        $houses = House::where('occupancy_status', 'occupied')
            ->with(['houseResidents' => function ($query) {
                $query->where('is_current', true);
            }])
            ->get();

        $pendingPayments = 0;

        foreach ($houses as $house) {
            $houseResidentIds = $house->houseResidents->pluck('id');

            // Cek ada yang bayar di rumah ini
            $hasPaid = Payment::whereIn('house_resident_id', $houseResidentIds)
                ->where('payment_period', 'monthly')
                ->where('is_paid', true)
                ->where(function ($query) use ($startOfMonth, $endOfMonth) {
                    $query->whereBetween('period_start', [$startOfMonth, $endOfMonth])
                        ->orWhereBetween('period_end', [$startOfMonth, $endOfMonth]);
                })
                ->exists();

            if (!$hasPaid) {
                $pendingPayments++; // Rumah ini belum bayar
            }
        }

        // Saldo tahun ini
        $currentYearIncome = Payment::where('is_paid', true)
            ->whereYear('payment_date', $year)
            ->sum('amount');

        $currentYearExpenses = Expense::whereYear('expense_date', $year)
            ->sum('amount');

        $balance = $currentYearIncome - $currentYearExpenses;

        return response()->json([
            'residents' => $residentCount,
            'houses' => $totalHouses,
            'occupiedHouses' => $occupiedHouses,
            'pendingPayments' => $pendingPayments, // Jumlah rumah yang belum bayar
            'yearlyBalance' => $balance
        ]);
    }
}
