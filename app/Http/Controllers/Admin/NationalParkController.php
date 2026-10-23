<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NationalPark;
use Illuminate\Http\Request;

class NationalParkController extends Controller
{
    /**
     * Display a listing of the national parks.
     */
    public function index()
    {
        $parks = NationalPark::latest()->paginate(10);

        return view('admin.national-parks.index', compact('parks'));
    }

    /**
     * Show the form for creating a new national park.
     */
    public function create()
    {
        return view('admin.national-parks.create');
    }

    /**
     * Store a newly created national park in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'entry_fee_indian' => ['nullable', 'numeric', 'min:0'],
            'entry_fee_foreigner' => ['nullable', 'numeric', 'min:0'],
        ]);

        // Set optional prices to null if not provided
        $validated['entry_fee_indian'] = $validated['entry_fee_indian'] ?? null;
        $validated['entry_fee_foreigner'] = $validated['entry_fee_foreigner'] ?? null;

        NationalPark::create($validated);

        return redirect()->route('admin.national-parks.index')
            ->with('status', 'National park created successfully.');
    }

    /**
     * Show the form for editing the specified national park.
     */
    public function edit(NationalPark $nationalPark)
    {
        return view('admin.national-parks.edit', compact('nationalPark'));
    }

    /**
     * Update the specified national park in storage.
     */
    public function update(Request $request, NationalPark $nationalPark)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'entry_fee_indian' => ['nullable', 'numeric', 'min:0'],
            'entry_fee_foreigner' => ['nullable', 'numeric', 'min:0'],
        ]);

        // Set optional prices to null if not provided
        $validated['entry_fee_indian'] = $validated['entry_fee_indian'] ?? null;
        $validated['entry_fee_foreigner'] = $validated['entry_fee_foreigner'] ?? null;

        $nationalPark->update($validated);

        return redirect()->route('admin.national-parks.index')
            ->with('status', 'National park updated successfully.');
    }

    /**
     * Remove the specified national park from storage.
     */
    public function destroy(NationalPark $nationalPark)
    {
        $nationalPark->delete();

        return redirect()->route('admin.national-parks.index')
            ->with('status', 'National park deleted successfully.');
    }
}
