<?php

namespace App\Http\Controllers;

use App\Models\HouseResident;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Payment::with(['houseResident.resident', 'houseResident.house'])->latest();

        if ($request->has('month') && $request->has('year')) {
            $startDate = Carbon::createFromDate($request->year, $request->month, 1)->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();

            $query->whereBetween('payment_date', [$startDate, $endDate]);
        }

        $payments = $query->get();
        return response()->json($payments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'house_resident_id' => 'required|exists:house_residents,id',
            'payment_type' => 'required|in:security,cleaning',
            'amount' => 'required|numeric',
            'payment_date' => 'required|date',
            'payment_period' => 'required|in:monthly,yearly',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after:period_start',
            'is_paid' => 'required|boolean',
        ]);

        // Get the house_id from house_resident_id
        $houseResident = DB::table('house_residents')
            ->where('id', $validated['house_resident_id'])
            ->first();

        if (!$houseResident) {
            return response()->json([
                'message' => 'Data penghuni rumah tidak ditemukan',
            ], 404);
        }

        $houseId = $houseResident->house_id;

        // Check if a payment for this house, period and type already exists
        $existingPayment = DB::table('payments')
            ->join('house_residents', 'payments.house_resident_id', '=', 'house_residents.id')
            ->where('house_residents.house_id', $houseId)
            ->where('payments.payment_type', $validated['payment_type'])
            ->where('payments.is_paid', true)
            ->where('payments.period_end', '>=', now())
            ->orderBy('payments.period_end', 'desc')
            ->first();

        if ($existingPayment) {
            return response()->json([
                'message' => 'Pembayaran masih aktif untuk rumah ini',
                'errors' => [
                    'period_start' => ['Pembayaran ' . ($validated['payment_type'] == 'security' ? 'keamanan' : 'kebersihan') . ' masih aktif hingga ' . $existingPayment->period_end],
                ]
            ], 422);
        }

        if ($existingPayment && $validated['is_paid'] == true) {
            return response()->json([
                'message' => 'Pembayaran untuk rumah ini pada periode tersebut sudah dilakukan',
                'errors' => [
                    'period_start' => ['Pembayaran ' . ($validated['payment_type'] == 'security' ? 'keamanan' : 'kebersihan') . ' untuk rumah ini pada periode ini sudah dilakukan'],
                ]
            ], 422);
        }

        $payment = Payment::create($validated);
        return response()->json($payment, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        return response()->json($payment->load(['houseResident.resident', 'houseResident.house']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'house_resident_id' => 'sometimes|exists:house_residents,id',
            'payment_type' => 'sometimes|in:security,cleaning',
            'amount' => 'sometimes|numeric',
            'payment_date' => 'sometimes|date',
            'payment_period' => 'sometimes|in:monthly,yearly',
            'period_start' => 'sometimes|date',
            'period_end' => 'sometimes|date|after:period_start',
            'is_paid' => 'sometimes|boolean',
        ]);

        $payment->update($validated);
        return response()->json($payment);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        $payment->delete();
        return response()->json(null, 204);
    }

    public function summary(Request $request)
    {
        $year = $request->input('year', Carbon::now()->year);

        $monthly_data = [];

        for ($month = 1; $month <= 12; $month++) {
            $start_date = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $end_date = $start_date->copy()->endOfMonth();

            $security_income = Payment::where('payment_type', 'security')
                ->where('is_paid', true)
                ->whereDate('payment_date', '>=', $start_date)
                ->whereDate('payment_date', '<=', $end_date)
                ->sum('amount');

            $cleaning_income = Payment::where('payment_type', 'cleaning')
                ->where('is_paid', true)
                ->whereDate('payment_date', '>=', $start_date)
                ->whereDate('payment_date', '<=', $end_date)
                ->sum('amount');

            $expenses = DB::table('expenses')
                ->whereDate('expense_date', '>=', $start_date)
                ->whereDate('expense_date', '<=', $end_date)
                ->sum('amount');

            $monthly_data[] = [
                'month' => $start_date->locale('id')->translatedFormat('F'),
                'security_income' => $security_income,
                'cleaning_income' => $cleaning_income,
                'total_income' => $security_income + $cleaning_income,
                'total_expenses' => $expenses,
                'balance' => ($security_income + $cleaning_income) - $expenses,
            ];
        }

        return response()->json([
            'year' => $year,
            'monthly_data' => $monthly_data,
        ]);
    }

    public function monthlyDetail(Request $request)
    {
        $year = $request->input('year', Carbon::now()->year);
        $month = $request->input('month', Carbon::now()->month);

        $start_date = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $end_date = $start_date->copy()->endOfMonth();

        $payments = Payment::with(['houseResident.resident', 'houseResident.house'])
            ->whereDate('payment_date', '>=', $start_date)
            ->whereDate('payment_date', '<=', $end_date)
            ->get();

        $expenses = DB::table('expenses')
            ->whereDate('expense_date', '>=', $start_date)
            ->whereDate('expense_date', '<=', $end_date)
            ->get();

        return response()->json([
            'year' => $year,
            'month' => $month,
            'payments' => $payments,
            'expenses' => $expenses,
            'total_income' => $payments->where('is_paid', true)->sum('amount'),
            'total_expenses' => $expenses->sum('amount'),
        ]);
    }
}
