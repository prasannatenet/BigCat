<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    /**
     * Display a listing of the vehicles.
     */
    public function index()
    {
        $vehicles = Vehicle::latest()->paginate(10);

        return view('admin.vehicles.index', compact('vehicles'));
    }

    /**
     * Show the form for creating a new vehicle.
     */
    public function create()
    {
        return view('admin.vehicles.create');
    }

    /**
     * Store a newly created vehicle in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'total_seat' => ['required', 'integer', 'min:1'],
            'price_indian' => ['required', 'numeric', 'min:0'],
            'price_foreigner' => ['required', 'numeric', 'min:0'],
        ]);

        Vehicle::create($validated);

        return redirect()->route('admin.vehicles.index')
            ->with('status', 'Vehicle created successfully.');
    }

    /**
     * Show the form for editing the specified vehicle.
     */
    public function edit(Vehicle $vehicle)
    {
        return view('admin.vehicles.edit', compact('vehicle'));
    }

    /**
     * Update the specified vehicle in storage.
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'type' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'total_seat' => ['required', 'integer', 'min:1'],
            'price_indian' => ['required', 'numeric', 'min:0'],
            'price_foreigner' => ['required', 'numeric', 'min:0'],
        ]);

        $vehicle->update($validated);

        return redirect()->route('admin.vehicles.index')
            ->with('status', 'Vehicle updated successfully.');
    }

    /**
     * Remove the specified vehicle from storage.
     */
    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();

        return redirect()->route('admin.vehicles.index')
            ->with('status', 'Vehicle deleted successfully.');
    }
}
