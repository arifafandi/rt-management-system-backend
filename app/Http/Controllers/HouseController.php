<?php

namespace App\Http\Controllers;

use App\Models\House;
use App\Models\HouseResident;
use App\Models\Payment;
use Illuminate\Http\Request;

class HouseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $houses = House::with('currentResidents.resident')->get();

        $result = $houses->map(function ($house) {
            // Default status
            $paymentStatus = 'unpaid';

            $houseResidentIds = $house->currentResidents()->get()->pluck('id');

            $hasPaid = Payment::whereIn('house_resident_id', $houseResidentIds)
                ->where('payment_period', 'monthly') // Sesuaikan kalau perlu
                ->where('is_paid', true)
                ->whereDate('period_start', '<=', now())
                ->whereDate('period_end', '>=', now())
                ->exists();

            if ($hasPaid) {
                $paymentStatus = 'paid';
            }

            return [
                'id' => $house->id,
                'house_number' => $house->house_number,
                'occupancy_status' => $house->occupancy_status,
                'payment_status' => $paymentStatus,
            ];
        });

        return response()->json($result);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'house_number' => 'required|string|max:10|unique:houses',
            'occupancy_status' => 'sometimes|in:occupied,vacant',
        ]);

        $house = House::create($validated);
        return response()->json($house, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(House $house)
    {
        return response()->json([
            'house' => $house,
            'current_residents' => $house->currentResidents()->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, House $house)
    {
        $validated = $request->validate([
            'house_number' => 'sometimes|string|max:10|unique:houses,house_number,' . $house->id,
            'occupancy_status' => 'sometimes|in:occupied,vacant',
        ]);

        $house->update($validated);
        return response()->json($house);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(House $house)
    {
        $house->delete();
        return response()->json(null, 204);
    }

    public function history(House $house)
    {
        $history = HouseResident::where('house_id', $house->id)
            ->with('resident')
            ->orderBy('start_date', 'desc')
            ->get();

        return response()->json($history);
    }

    public function paymentHistory(House $house)
    {
        $paymentHistory = HouseResident::where('house_id', $house->id)
            ->with(['resident', 'payments'])
            ->get();

        return response()->json($paymentHistory);
    }

    public function addResident(Request $request, House $house)
    {
        $validated = $request->validate([
            'resident_id' => 'required|exists:residents,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        // Create new residency
        $houseResident = HouseResident::create([
            'house_id' => $house->id,
            'resident_id' => $validated['resident_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'] ?? null,
            'is_current' => true,
        ]);

        // Update house status
        $house->update(['occupancy_status' => 'occupied']);

        return response()->json($houseResident, 201);
    }

    public function removeResident(Request $request, House $house)
    {
        $validated = $request->validate([
            'resident_id' => 'required|exists:residents,id',
            'end_date' => 'required|date',
        ]);

        $houseResident = HouseResident::where('house_id', $house->id)
            ->where('resident_id', $validated['resident_id'])
            ->where('is_current', true)
            ->first();

        if (!$houseResident) {
            return response()->json(['message' => 'Resident not found in this house'], 404);
        }

        $houseResident->update([
            'is_current' => false,
            'end_date' => $validated['end_date'],
        ]);

        // Update house status if no more current residents
        if (HouseResident::where('house_id', $house->id)->where('is_current', true)->count() === 0) {
            $house->update(['occupancy_status' => 'vacant']);
        }

        return response()->json($houseResident);
    }
}
