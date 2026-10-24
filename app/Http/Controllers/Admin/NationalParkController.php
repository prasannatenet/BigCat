<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NationalPark;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class NationalParkController extends Controller
{
    /**
     * Display a listing of the national parks.
     */
    public function index(): View
    {
        $parks = NationalPark::latest()->paginate(10);

        return view('admin.national-parks.index', compact('parks'));
    }

    /**
     * Show the form for creating a new national park.
     */
    public function create(): View
    {
        return view('admin.national-parks.create');
    }

    /**
     * Store a newly created national park in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'entry_fee_indian' => ['nullable', 'numeric', 'min:0'],
            'entry_fee_foreigner' => ['nullable', 'numeric', 'min:0'],
            'images' => ['sometimes', 'array', 'max:5'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        // Set optional prices to null if not provided
        $validated['entry_fee_indian'] = $validated['entry_fee_indian'] ?? null;
        $validated['entry_fee_foreigner'] = $validated['entry_fee_foreigner'] ?? null;

        $images = $validated['images'] ?? [];
        unset($validated['images']);
        $park = NationalPark::create($validated);
        $this->storeImages($park, $images);

        return redirect()->route('admin.national-parks.index')
            ->with('status', 'National park created successfully.');
    }

    /**
     * Show the form for editing the specified national park.
     */
    public function edit(NationalPark $nationalPark): View
    {
        return view('admin.national-parks.edit', compact('nationalPark'));
    }

    /**
     * Update the specified national park in storage.
     */
    public function update(Request $request, NationalPark $nationalPark): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'entry_fee_indian' => ['nullable', 'numeric', 'min:0'],
            'entry_fee_foreigner' => ['nullable', 'numeric', 'min:0'],
            'images' => ['sometimes', 'array', 'max:5'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remove_images' => ['sometimes', 'array'],
            'remove_images.*' => ['integer', 'distinct'],
            'image_positions' => ['sometimes', 'array'],
            'image_positions.*' => ['required', 'integer', 'min:1', 'max:5', 'distinct'],
        ]);

        $removeIds = $validated['remove_images'] ?? [];
        $uploads = $validated['images'] ?? [];
        $positions = $validated['image_positions'] ?? [];
        unset($validated['images'], $validated['remove_images'], $validated['image_positions']);

        $existingImages = $nationalPark->images()->get();
        if (array_diff($removeIds, $existingImages->pluck('id')->all()) !== []) {
            throw ValidationException::withMessages(['remove_images' => 'Choose images belonging to this park.']);
        }
        if (array_diff(array_keys($positions), $existingImages->pluck('id')->map(fn ($id) => (string) $id)->all()) !== []) {
            throw ValidationException::withMessages(['image_positions' => 'Choose images belonging to this park.']);
        }
        if ($existingImages->count() - count($removeIds) + count($uploads) > 5) {
            throw ValidationException::withMessages(['images' => 'A national park can have up to 5 images. Remove an image before adding another.']);
        }

        // Set optional prices to null if not provided
        $validated['entry_fee_indian'] = $validated['entry_fee_indian'] ?? null;
        $validated['entry_fee_foreigner'] = $validated['entry_fee_foreigner'] ?? null;

        $nationalPark->update($validated);

        foreach ($removeIds as $imageId) {
            $image = $existingImages->firstWhere('id', $imageId);
            Storage::disk('public')->delete($image->path);
            $image->delete();
        }
        foreach ($positions as $imageId => $position) {
            $nationalPark->images()->whereKey($imageId)->update(['position' => $position]);
        }
        $this->storeImages($nationalPark, $uploads);

        return redirect()->route('admin.national-parks.index')
            ->with('status', 'National park updated successfully.');
    }

    /**
     * Remove the specified national park from storage.
     */
    public function destroy(NationalPark $nationalPark): RedirectResponse
    {
        foreach ($nationalPark->images as $image) {
            Storage::disk('public')->delete($image->path);
        }

        $nationalPark->delete();

        return redirect()->route('admin.national-parks.index')
            ->with('status', 'National park deleted successfully.');
    }

    /** @param array<int, UploadedFile> $uploads */
    private function storeImages(NationalPark $park, array $uploads): void
    {
        $usedPositions = $park->images()->pluck('position')->map(fn ($position): int => (int) $position)->all();
        foreach ($uploads as $upload) {
            $position = collect(range(1, 5))->first(fn (int $position): bool => ! in_array($position, $usedPositions, true));
            $park->images()->create([
                'path' => $upload->store('national-parks/'.$park->id, 'public'),
                'position' => $position,
            ]);
            $usedPositions[] = $position;
        }
    }
}
