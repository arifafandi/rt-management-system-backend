<?php

namespace App\Http\Controllers;

use App\Models\Resident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ResidentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $residents = Resident::all();
        return response()->json($residents);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'id_card_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'resident_status' => 'required|in:permanent,contract',
            'phone_number' => 'required|string|max:20',
            'is_married' => 'required|boolean',
        ]);

        $path = $request->file('id_card_photo')->store('id_cards', 'public');
        $validated['id_card_photo'] = $path;

        $resident = Resident::create($validated);
        return response()->json($resident, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Resident $resident)
    {
        return response()->json($resident);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Resident $resident)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'id_card_photo' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'resident_status' => 'sometimes|in:permanent,contract',
            'phone_number' => 'sometimes|string|max:20',
            'is_married' => 'sometimes|boolean',
        ]);

        if ($request->hasFile('id_card_photo')) {
            // Delete the old image
            if ($resident->id_card_photo) {
                Storage::disk('public')->delete($resident->id_card_photo);
            }
            $path = $request->file('id_card_photo')->store('id_cards', 'public');
            $validated['id_card_photo'] = $path;
        }

        $resident->update($validated);
        return response()->json($resident);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Resident $resident)
    {
        if ($resident->id_card_photo) {
            Storage::disk('public')->delete($resident->id_card_photo);
        }
        $resident->delete();
        return response()->json(null, 204);
    }
}
