<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NationalPark;
use App\Models\ParkZone;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SlotController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'park' => ['nullable', 'integer', 'exists:national_parks,id'],
            'year' => ['nullable', 'integer', 'min:2000', 'max:2100'],
            'months' => ['nullable', 'integer', 'min:1', 'max:24'],
        ]);

        $selectedParkId = $validated['park'] ?? null;
        $calendarYear = (int) ($validated['year'] ?? now()->year);
        $months = (int) ($validated['months'] ?? $request->session()->get('admin.slots.months', 12));
        $request->session()->put('admin.slots.months', $months);
        $parks = NationalPark::query()->orderBy('name')->get();
        $selectedPark = $selectedParkId
            ? $parks->firstWhere('id', (int) $selectedParkId)
            : null;
        $zones = ParkZone::query()
            ->with([
                'nationalPark',
                'slots' => fn (HasMany $query) => $query->orderBy('slot_date'),
            ])
            ->when($selectedParkId, fn (Builder $query) => $query->where('national_park_id', $selectedParkId))
            ->orderBy('national_park_id')
            ->orderBy('name')
            ->get();

        // Calculate date range based on months from current date (forward)
        // Start from current date (not start of month) so we show exactly $months months from today
        $calendarStartDate = CarbonImmutable::today();
        $calendarEndDate = $calendarStartDate->addMonths((int) $months);

        $parkAvailability = $selectedPark?->slotAvailability()
            ->whereBetween('slot_date', [
                $calendarStartDate->toDateString(),
                $calendarEndDate->toDateString(),
            ])
            ->orderBy('slot_date')
            ->pluck('is_available', 'slot_date')
            ->map(fn (mixed $isAvailable): bool => (bool) $isAvailable)
            ->all() ?? [];

        return view('admin.slots.index', compact(
            'calendarYear',
            'parkAvailability',
            'parks',
            'selectedPark',
            'selectedParkId',
            'zones',
            'months',
            'calendarStartDate',
            'calendarEndDate',
        ));
    }

    public function updateParkAvailability(Request $request, NationalPark $nationalPark): RedirectResponse
    {
        $validated = $request->validate([
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'months' => ['nullable', 'integer', 'min:1', 'max:24'],
            'availability' => ['sometimes', 'array'],
            'availability.*' => ['required', 'array'],
            'availability.*.date' => ['required', 'date_format:Y-m-d', 'distinct'],
            'availability.*.is_available' => ['required', 'in:0,1'],
        ]);
        $calendarYear = (int) $validated['year'];
        $months = $validated['months'] ?? null;
        $rangeStart = CarbonImmutable::today();
        $rangeEnd = $months !== null
            ? $rangeStart->addMonths((int) $months)
            : CarbonImmutable::create($calendarYear, 12, 31);

        foreach ($validated['availability'] ?? [] as $index => $dateAvailability) {
            $date = CarbonImmutable::parse($dateAvailability['date']);
            if ($date->lt($rangeStart)) {
                throw ValidationException::withMessages([
                    "availability.{$index}.date" => 'Each availability date must not be before today.',
                ]);
            }
            if ($date->gt($rangeEnd)) {
                throw ValidationException::withMessages([
                    "availability.{$index}.date" => 'Each availability date must not exceed the selected calendar range.',
                ]);
            }
        }

        $rangeStartDate = $rangeStart->toDateString();
        $rangeEndDate = $rangeEnd->toDateString();

        DB::transaction(function () use ($nationalPark, $validated, $rangeStartDate, $rangeEndDate): void {
            $nationalPark->slotAvailability()
                ->whereBetween('slot_date', [$rangeStartDate, $rangeEndDate])
                ->delete();

            $availability = collect($validated['availability'] ?? [])
                ->map(fn (array $dateAvailability): array => [
                    'slot_date' => $dateAvailability['date'],
                    'is_available' => $dateAvailability['is_available'],
                ])
                ->all();

            $nationalPark->slotAvailability()->createMany($availability);
        });

        return redirect()->route('admin.slots.index', array_filter([
            'park' => $nationalPark->id,
            'year' => $calendarYear,
            'months' => $months,
        ], fn (mixed $value): bool => $value !== null))->with('status', 'Park-wide date availability saved for all zones.');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'national_park_id' => ['required', 'integer', 'exists:national_parks,id'],
            'zones' => ['required', 'array', 'min:1'],
            'zones.*' => ['required', 'array'],
            'zones.*.name' => [
                'required',
                'string',
                'max:255',
                'distinct',
                Rule::unique('park_zones', 'name')
                    ->where('national_park_id', $request->input('national_park_id')),
            ],
            'zones.*.default_available' => ['required', 'in:0,1'],
            'zones.*.dates' => ['sometimes', 'array'],
            'zones.*.dates.*' => ['array'],
            'zones.*.dates.*.date' => ['nullable', 'date_format:Y-m-d'],
            'zones.*.dates.*.is_available' => ['required_with:zones.*.dates.*.date', 'in:0,1'],
        ]);

        $this->validateUniqueDates($validated['zones']);

        DB::transaction(function () use ($validated): void {
            $park = NationalPark::findOrFail($validated['national_park_id']);

            foreach ($validated['zones'] as $zoneData) {
                $zone = $park->zones()->create([
                    'name' => $zoneData['name'],
                    'default_available' => $zoneData['default_available'],
                ]);

                $this->saveDates($zone, $zoneData['dates'] ?? []);
            }
        });

        return redirect()->route('admin.slots.index', ['park' => $validated['national_park_id']])
            ->with('status', 'Zones and slot availability created successfully.');
    }

    public function update(Request $request, ParkZone $parkZone): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('park_zones', 'name')
                    ->where('national_park_id', $parkZone->national_park_id)
                    ->ignore($parkZone->id),
            ],
            'default_available' => ['required', 'in:0,1'],
            'dates' => ['sometimes', 'array'],
            'dates.*' => ['array'],
            'dates.*.date' => ['nullable', 'date_format:Y-m-d'],
            'dates.*.is_available' => ['required_with:dates.*.date', 'in:0,1'],
        ]);

        $this->validateUniqueDates([$validated['dates'] ?? []], 'dates');

        DB::transaction(function () use ($parkZone, $validated): void {
            $parkZone->update([
                'name' => $validated['name'],
                'default_available' => $validated['default_available'],
            ]);

            $parkZone->slots()->delete();
            $this->saveDates($parkZone, $validated['dates'] ?? []);
        });

        return redirect()->route('admin.slots.index', ['park' => $parkZone->national_park_id])
            ->with('status', 'Zone availability updated successfully.');
    }

    public function destroy(ParkZone $parkZone): RedirectResponse
    {
        $parkId = $parkZone->national_park_id;
        $parkZone->delete();

        return redirect()->route('admin.slots.index', ['park' => $parkId])
            ->with('status', 'Zone deleted successfully.');
    }

    /**
     * @param  array<int|string, array<string, mixed>>  $groups
     */
    private function validateUniqueDates(array $groups, string $field = 'zones'): void
    {
        foreach ($groups as $groupIndex => $group) {
            $dates = collect($group['dates'] ?? $group)
                ->pluck('date')
                ->filter()
                ->values();

            if ($dates->count() !== $dates->unique()->count()) {
                $errorField = $field === 'zones' ? "{$field}.{$groupIndex}.dates" : $field;

                throw ValidationException::withMessages([
                    $errorField => 'Each slot date must be unique within its zone.',
                ]);
            }
        }
    }

    /**
     * @param  array<int|string, array{date?: ?string, is_available?: string}>  $dates
     */
    private function saveDates(ParkZone $zone, array $dates): void
    {
        foreach ($dates as $date) {
            if (empty($date['date'])) {
                continue;
            }

            $zone->slots()->create([
                'slot_date' => $date['date'],
                'is_available' => $date['is_available'],
            ]);
        }
    }
}
