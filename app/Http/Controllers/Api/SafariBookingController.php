<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\BookingOptionsResource;
use App\Http\Resources\Api\SafariBookingRequestResource;
use App\Models\NationalPark;
use App\Models\ParkZone;
use App\Models\SafariBookingRequest;
use App\Models\Vehicle;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SafariBookingController extends Controller
{
    public function options(): BookingOptionsResource
    {
        $vehicles = Vehicle::query()->orderBy('type')->orderBy('name')->get();
        $safariModes = $vehicles->pluck('type')->unique()->values();
        if ($safariModes->isEmpty()) {
            $safariModes = collect(['Gypsy', 'Canter']);
        }

        return new BookingOptionsResource([
            'national_parks' => NationalPark::query()->with(['images', 'zones.images'])->orderBy('name')->get(),
            'safari_modes' => $safariModes,
            'vehicles' => $vehicles,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $availableModes = Vehicle::query()->distinct()->pluck('type');
        if ($availableModes->isEmpty()) {
            $availableModes = collect(['Gypsy', 'Canter']);
        }

        $validated = $request->validate([
            'national_park_id' => ['required', 'integer', 'exists:national_parks,id'],
            'zone_ids' => ['required', 'array', 'min:1'],
            'zone_ids.*' => ['required', 'integer', 'distinct', 'exists:park_zones,id'],
            'safari_mode' => ['required', 'string', Rule::in($availableModes->all())],
            'safari_dates' => ['required', 'array', 'min:1'],
            'safari_dates.*' => ['required', 'date_format:Y-m-d', 'after_or_equal:today', 'distinct'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'phone_country' => ['nullable', 'string', 'max:255'],
            'phone_country_code' => ['nullable', 'string', 'max:8'],
            'email' => ['required', 'email', 'max:255'],
            'coupon_name' => ['nullable', 'string', 'max:255'],
        ]);

        $zoneIds = collect($validated['zone_ids'])->map(fn (mixed $id): int => (int) $id)->unique()->values();
        $parkZoneIds = ParkZone::query()
            ->where('national_park_id', $validated['national_park_id'])
            ->whereIn('id', $zoneIds)
            ->pluck('id');
        if ($parkZoneIds->count() !== $zoneIds->count()) {
            return response()->json(['message' => 'Choose zones in the selected national park.'], 422);
        }

        $bookingRequest = SafariBookingRequest::create([
            'national_park_id' => $validated['national_park_id'],
            'park_zone_id' => $zoneIds->first(),
            'safari_mode' => $validated['safari_mode'],
            'safari_shift' => 'Any',
            'safari_date' => $validated['safari_dates'][0],
            'requested_dates' => $validated['safari_dates'],
            'requested_zone_ids' => $zoneIds->all(),
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'phone_country' => $validated['phone_country'] ?? 'India',
            'phone_country_code' => $validated['phone_country_code'] ?? '+91',
            'email' => $validated['email'],
            'coupon_name' => $validated['coupon_name'] ?? null,
            'confirmation_token' => Str::random(64),
            'was_available' => false,
        ]);

        return (new SafariBookingRequestResource($bookingRequest->load('nationalPark')))
            ->response()
            ->setStatusCode(201);
    }

    public function show(string $confirmationToken): SafariBookingRequestResource
    {
        $bookingRequest = $this->bookingRequest($confirmationToken);
        $bookingRequest->load(['nationalPark', 'slots.zone']);

        return new SafariBookingRequestResource($bookingRequest);
    }

    public function saveSlots(Request $request, string $confirmationToken): SafariBookingRequestResource|JsonResponse
    {
        $bookingRequest = $this->bookingRequest($confirmationToken);
        abort_if($bookingRequest->request_status === 'submitted', 409, 'This booking request has already been submitted.');

        $validated = $request->validate([
            'visits' => ['required', 'array', 'min:1'],
            'visits.*.date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today', 'distinct'],
            'visits.*.morning' => ['nullable', 'integer', 'exists:park_zones,id'],
            'visits.*.evening' => ['nullable', 'integer', 'exists:park_zones,id'],
        ]);

        $visitDates = collect($validated['visits'])->pluck('date')->sort()->values();
        $expectedDates = collect($bookingRequest->requested_dates ?? [])->sort()->values();
        if ($visitDates->all() !== $expectedDates->all()) {
            return response()->json(['message' => 'Provide one visit entry for each originally selected date.'], 422);
        }

        $selectedSlots = collect($validated['visits'])->flatMap(function (array $visit): array {
            $slots = [];
            foreach (['morning' => 'Morning', 'evening' => 'Evening'] as $field => $shift) {
                if (! empty($visit[$field])) {
                    $slots[] = ['date' => $visit['date'], 'zone_id' => (int) $visit[$field], 'shift' => $shift];
                }
            }

            return $slots;
        })->values();
        if ($selectedSlots->isEmpty()) {
            return response()->json(['message' => 'Select at least one visit.'], 422);
        }
        $scheduledDates = $selectedSlots->pluck('date')->unique()->sort()->values();
        if ($scheduledDates->all() !== $expectedDates->all()) {
            return response()->json(['message' => 'Select at least one zone for every selected date.'], 422);
        }

        $expectedZoneIds = collect($bookingRequest->requested_zone_ids ?? [])->map(fn (mixed $id): int => (int) $id)->sort()->values();
        $chosenZoneIds = $selectedSlots->pluck('zone_id')->unique()->sort()->values();
        if ($chosenZoneIds->all() !== $expectedZoneIds->all()) {
            return response()->json(['message' => 'Assign every selected zone to at least one date and slot.'], 422);
        }

        $zones = ParkZone::query()
            ->where('national_park_id', $bookingRequest->national_park_id)
            ->whereIn('id', $expectedZoneIds)
            ->get()
            ->keyBy('id');
        if ($zones->count() !== $expectedZoneIds->count()) {
            return response()->json(['message' => 'A selected zone is no longer available.'], 422);
        }

        $selectedShiftsByDate = $selectedSlots->groupBy('date')->map(fn ($slots): array => $slots->pluck('shift')->all());
        $requestedSlots = $selectedSlots->map(function (array $slotData) use ($zones, $selectedShiftsByDate): array {
            $zone = $zones->get($slotData['zone_id']);
            $date = CarbonImmutable::parse($slotData['date']);
            $selectedShift = $slotData['shift'];
            if (! $zone->isShiftAvailableOn($date, $selectedShift)) {
                $otherShift = $selectedShift === 'Morning' ? 'Evening' : 'Morning';
                if ($zone->isShiftAvailableOn($date, $otherShift)
                    && ! in_array($otherShift, $selectedShiftsByDate->get($slotData['date'], []), true)) {
                    $selectedShift = $otherShift;
                }
            }

            return [
                'park_zone_id' => $zone->id,
                'safari_date' => $date,
                'safari_shift' => $selectedShift,
                'was_available' => $zone->isShiftAvailableOn($date, $selectedShift),
            ];
        });

        DB::transaction(function () use ($bookingRequest, $expectedDates, $expectedZoneIds, $requestedSlots, $selectedSlots): void {
            $firstSlot = $requestedSlots->first();
            $preferredShifts = $selectedSlots->pluck('shift')->unique();
            $bookingRequest->update([
                'park_zone_id' => $firstSlot['park_zone_id'],
                'safari_date' => $firstSlot['safari_date'],
                'safari_shift' => $preferredShifts->count() === 1 ? $preferredShifts->first() : 'Any',
                'requested_dates' => $expectedDates->all(),
                'requested_zone_ids' => $expectedZoneIds->all(),
                'was_available' => $firstSlot['was_available'],
            ]);
            $bookingRequest->slots()->delete();
            $bookingRequest->slots()->createMany($requestedSlots->all());
        });

        return new SafariBookingRequestResource($bookingRequest->fresh()->load(['nationalPark', 'slots.zone']));
    }

    public function submit(string $confirmationToken): SafariBookingRequestResource
    {
        $bookingRequest = $this->bookingRequest($confirmationToken)->load('slots.zone');
        abort_if($bookingRequest->slots->isEmpty(), 409, 'Choose safari slots before submitting the booking request.');

        if ($bookingRequest->request_status !== 'submitted') {
            foreach ($bookingRequest->slots as $slot) {
                $slot->was_available = $slot->zone->isShiftAvailableOn($slot->safari_date, $slot->safari_shift);
                $slot->save();
            }
            $bookingRequest->update([
                'was_available' => $bookingRequest->slots->first()->was_available,
                'request_status' => 'submitted',
            ]);
        }

        return new SafariBookingRequestResource($bookingRequest->fresh()->load(['nationalPark', 'slots.zone']));
    }

    private function bookingRequest(string $confirmationToken): SafariBookingRequest
    {
        return SafariBookingRequest::query()
            ->where('confirmation_token', $confirmationToken)
            ->firstOrFail();
    }
}
