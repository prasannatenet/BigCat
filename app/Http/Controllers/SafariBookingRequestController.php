<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\NationalPark;
use App\Models\ParkZone;
use App\Models\SafariBookingRequest;
use App\Models\Vehicle;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Throwable;

class SafariBookingRequestController extends Controller
{
    public function index(Request $request): View
    {
        $parks = NationalPark::query()->with(['images', 'zones.images'])->orderBy('name')->get();
        $vehicles = Vehicle::query()->select('type')->distinct()->orderBy('type')->pluck('type');
        if ($vehicles->isEmpty()) {
            $vehicles = collect(['Gypsy', 'Canter']);
        }
        $oldParkId = $request->old('national_park_id');
        $selectedPark = $oldParkId
            ? $parks->firstWhere('id', (int) $oldParkId)
            : ($parks->count() === 1 ? $parks->first() : null);
        $zonesByPark = $parks->mapWithKeys(fn (NationalPark $park): array => [
            $park->id => $park->zones->map(fn (ParkZone $zone): array => [
                'id' => $zone->id,
                'name' => $zone->name,
                'description' => $zone->short_description,
                'image' => $zone->images->first() ? asset('storage/'.$zone->images->first()->path) : null,
            ])->values()->all(),
        ])->all();
        $parkImagesById = $parks->mapWithKeys(fn (NationalPark $park): array => [
            $park->id => $park->images->map(fn ($image): string => asset('storage/'.$image->path))->values()->all(),
        ])->all();
        $parkNamesById = $parks->mapWithKeys(fn (NationalPark $park): array => [
            $park->id => $park->name,
        ])->all();
        $countries = $this->countryDialCodes();

        return view('welcome', compact('parks', 'vehicles', 'selectedPark', 'zonesByPark', 'parkImagesById', 'parkNamesById', 'countries'));
    }

    public function startBooking(Request $request): RedirectResponse
    {
        $countries = collect($this->countryDialCodes());
        $countryOptions = $countries->pluck('iso2')->all();
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
            'phone_country_iso' => ['nullable', 'string', Rule::in($countryOptions)],
            'email' => ['required', 'email', 'max:255'],
            'coupon_name' => ['nullable', 'string', 'max:255'],
        ]);

        $selectedPhoneCountry = $countries->firstWhere('iso2', $validated['phone_country_iso'] ?? 'IN')
            ?? $countries->firstWhere('iso2', 'IN')
            ?? $countries->first();
        $validated['phone_country'] = $selectedPhoneCountry['name'];
        $validated['phone_country_code'] = $selectedPhoneCountry['calling_code'];
        unset($validated['phone_country_iso']);

        $zoneIds = collect($validated['zone_ids'])->map(fn (mixed $id): int => (int) $id)->unique()->values();
        $parkZoneIds = ParkZone::query()
            ->where('national_park_id', $validated['national_park_id'])
            ->whereIn('id', $zoneIds)
            ->pluck('id')
            ->map(fn (mixed $id): int => (int) $id);
        if ($parkZoneIds->count() !== $zoneIds->count()) {
            return back()->withErrors(['zone_ids' => 'Choose zones in the selected national park.'])->withInput();
        }

        $draft = collect($validated)->except(['safari_dates', 'zone_ids'])->all();
        $draft['dates'] = $validated['safari_dates'];
        $draft['zone_ids'] = $zoneIds->all();
        $bookingRequest = SafariBookingRequest::query()->find($request->session()->get('safari_booking_draft.booking_request_id'));
        $bookingAttributes = [
            'national_park_id' => $draft['national_park_id'],
            'park_zone_id' => $zoneIds->first(),
            'safari_mode' => $draft['safari_mode'],
            'safari_shift' => 'Any',
            'safari_date' => $draft['dates'][0],
            'requested_dates' => $draft['dates'],
            'requested_zone_ids' => $zoneIds->all(),
            'name' => $draft['name'],
            'phone' => $draft['phone'],
            'phone_country' => $draft['phone_country'],
            'phone_country_code' => $draft['phone_country_code'],
            'email' => $draft['email'],
            'coupon_name' => $draft['coupon_name'] ?? null,
            'was_available' => false,
        ];
        if ($bookingRequest === null) {
            $bookingAttributes['confirmation_token'] = Str::random(64);
            $bookingRequest = SafariBookingRequest::create($bookingAttributes);
        } else {
            $bookingRequest->update($bookingAttributes);
        }
        $draft['booking_request_id'] = $bookingRequest->id;
        $draft['confirmation_token'] = $bookingRequest->confirmation_token;
        $request->session()->put('safari_booking_draft', $draft);

        return redirect()->route('safari-booking.slots');
    }

    public function slots(Request $request): View|RedirectResponse
    {
        $draft = $request->session()->get('safari_booking_draft');
        if (! is_array($draft)) {
            return redirect()->route('home')->withErrors(['booking' => 'Choose your dates and zones before selecting safari slots.']);
        }

        $parks = NationalPark::query()->with(['images', 'zones.images'])->orderBy('name')->get();
        $selectedPark = $parks->firstWhere('id', (int) $draft['national_park_id']);
        if (! $selectedPark) {
            $request->session()->forget('safari_booking_draft');

            return redirect()->route('home')->withErrors(['national_park_id' => 'Choose an available national park.']);
        }

        $zonesById = $selectedPark->zones->whereIn('id', $draft['zone_ids'] ?? [])->keyBy('id');
        if ($zonesById->count() !== collect($draft['zone_ids'] ?? [])->unique()->count()) {
            $request->session()->forget('safari_booking_draft');

            return redirect()->route('home')->withErrors(['zone_ids' => 'A selected zone is no longer available. Please choose your dates and zones again.']);
        }

        $days = collect($draft['dates'] ?? [])->map(function (string $date) use ($zonesById): array {
            $visitDate = CarbonImmutable::parse($date);
            $zoneChoices = $zonesById->map(fn (ParkZone $zone): array => [
                'zone' => $zone,
                'morning_available' => $zone->isShiftAvailableOn($visitDate, 'Morning'),
                'evening_available' => $zone->isShiftAvailableOn($visitDate, 'Evening'),
            ])->values();

            return [
                'date' => $date,
                'zone_choices' => $zoneChoices,
            ];
        });

        return view('booking-slots', compact('draft', 'selectedPark', 'days'));
    }

    public function availability(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'zone' => ['required', 'integer', 'exists:park_zones,id'],
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'month' => ['required', 'integer', 'between:1,12'],
        ]);
        $zone = ParkZone::with('nationalPark')->findOrFail($validated['zone']);
        $start = CarbonImmutable::create($validated['year'], $validated['month'], 1)->startOfMonth();
        $end = $start->endOfMonth();
        $zoneOverrides = $zone->slots()->whereBetween('slot_date', [$start->toDateString(), $end->toDateString()])
            ->pluck('is_available', 'slot_date');
        $parkOverrides = $zone->nationalPark->slotAvailability()
            ->whereBetween('slot_date', [$start->toDateString(), $end->toDateString()])
            ->pluck('is_available', 'slot_date');
        $availability = [];

        for ($date = $start; $date <= $end; $date = $date->addDay()) {
            $dateString = $date->toDateString();
            $isAvailable = $zoneOverrides->has($dateString)
                ? (bool) $zoneOverrides->get($dateString)
                : ($parkOverrides->has($dateString)
                    ? (bool) $parkOverrides->get($dateString)
                    : $zone->default_available);
            $availability[$dateString] = $date->gte(CarbonImmutable::today()) && $isAvailable;
        }

        return response()->json($availability);
    }

    public function checking(string $confirmationToken): View
    {
        $bookingRequest = SafariBookingRequest::query()
            ->where('confirmation_token', $confirmationToken)
            ->firstOrFail();

        return view('safari-booking-checking', compact('bookingRequest'));
    }

    public function confirmation(string $confirmationToken): View
    {
        $bookingRequest = SafariBookingRequest::query()
            ->with(['nationalPark.zones', 'slots.zone.nationalPark'])
            ->where('confirmation_token', $confirmationToken)
            ->firstOrFail();

        foreach ($bookingRequest->slots as $slot) {
            $slot->was_available = $slot->zone->isShiftAvailableOn($slot->safari_date, $slot->safari_shift);
        }
        $slotsByDate = $bookingRequest->slots->groupBy(fn ($slot): string => $slot->safari_date->toDateString());
        $availableCount = $bookingRequest->slots->where('was_available', true)->count();
        $requestedCount = $bookingRequest->slots->count();
        $coupon = $bookingRequest->coupon;
        $enteredCoupon = $bookingRequest->coupon_name
            ? Coupon::query()
                ->whereRaw('LOWER(name) = ?', [mb_strtolower(trim($bookingRequest->coupon_name))])
                ->first()
            : null;
        $couponExpired = $enteredCoupon !== null && $enteredCoupon->ends_at->lt(CarbonImmutable::today());

        return view('safari-booking-confirmation', compact('availableCount', 'bookingRequest', 'coupon', 'couponExpired', 'enteredCoupon', 'requestedCount', 'slotsByDate'));
    }

    public function submitBookingRequest(string $confirmationToken): RedirectResponse
    {
        $bookingRequest = SafariBookingRequest::query()
            ->with('slots.zone.nationalPark')
            ->where('confirmation_token', $confirmationToken)
            ->firstOrFail();
        if ($bookingRequest->request_status !== 'submitted') {
            foreach ($bookingRequest->slots as $slot) {
                $slot->was_available = $slot->zone->isShiftAvailableOn($slot->safari_date, $slot->safari_shift);
                $slot->save();
            }
            $bookingRequest->update([
                'was_available' => $bookingRequest->slots->first()?->was_available ?? false,
                'request_status' => 'submitted',
            ]);
        }

        return redirect()->route('safari-booking.confirmation', ['confirmationToken' => $confirmationToken])
            ->with('status', 'We got your request. Our team will contact you within 1 hour.');
    }

    public function store(Request $request): RedirectResponse
    {
        $draft = $request->session()->get('safari_booking_draft');
        if (! is_array($draft)) {
            return redirect()->route('home')->withErrors(['booking' => 'Enter your details before submitting a booking request.']);
        }

        $validated = $request->validate([
            'visits' => ['required', 'array', 'min:1'],
            'visits.*.date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today', 'distinct'],
            'visits.*.morning' => ['nullable', 'integer', 'exists:park_zones,id'],
            'visits.*.evening' => ['nullable', 'integer', 'exists:park_zones,id'],
        ]);

        $visitDates = collect($validated['visits'])->pluck('date')->sort()->values();
        $expectedDates = collect($draft['dates'])->sort()->values();
        if ($visitDates->all() !== $expectedDates->all()) {
            return redirect()->route('safari-booking.slots')
                ->withErrors(['visits' => 'Review each selected date and assign one or two zone visits.']);
        }

        $selectedSlots = collect($validated['visits'])->flatMap(function (array $visit): array {
            $slots = [];
            foreach (['morning' => 'Morning', 'evening' => 'Evening'] as $field => $shift) {
                if (! empty($visit[$field])) {
                    $slots[] = [
                        'date' => $visit['date'],
                        'zone_id' => (int) $visit[$field],
                        'shift' => $shift,
                    ];
                }
            }

            return $slots;
        })->values();
        if ($selectedSlots->isEmpty()) {
            return redirect()->route('safari-booking.slots')->withErrors(['visits' => 'Select at least one visit.']);
        }
        $scheduledDates = $selectedSlots->pluck('date')->unique()->sort()->values();
        if ($scheduledDates->all() !== $expectedDates->all()) {
            return redirect()->route('safari-booking.slots')->withErrors(['visits' => 'Select at least one zone for every date.']);
        }

        $chosenZoneIds = $selectedSlots->pluck('zone_id')->unique()->sort()->values();
        $expectedZoneIds = collect($draft['zone_ids'])->map(fn (mixed $id): int => (int) $id)->sort()->values();
        if ($chosenZoneIds->all() !== $expectedZoneIds->all()) {
            return redirect()->route('safari-booking.slots')
                ->withErrors(['visits' => 'Assign every selected zone to at least one date and slot.']);
        }
        $zones = ParkZone::query()
            ->where('national_park_id', $draft['national_park_id'])
            ->whereIn('id', $expectedZoneIds)
            ->get()
            ->keyBy('id');

        if ($zones->count() !== collect($draft['zone_ids'])->unique()->count()) {
            $request->session()->forget('safari_booking_draft');

            return redirect()->route('home')->withErrors(['zone_ids' => 'A selected zone is no longer available. Please choose again.']);
        }

        $requestedSlots = [];
        $automaticFallbacks = [];
        $selectedShiftsByDate = $selectedSlots->groupBy('date')
            ->map(fn ($slots): array => $slots->pluck('shift')->all());
        foreach ($selectedSlots as $slotData) {
            $zone = $zones->get((int) $slotData['zone_id']);
            if ($zone === null) {
                return redirect()->route('safari-booking.slots')
                    ->withErrors(['slots' => 'Choose a zone from the selected national park.']);
            }
            $date = CarbonImmutable::parse($slotData['date']);
            $requestedShift = $slotData['shift'];
            $selectedShift = $requestedShift;
            if (! $zone->isShiftAvailableOn($date, $requestedShift)) {
                $otherShift = $requestedShift === 'Morning' ? 'Evening' : 'Morning';
                if ($zone->isShiftAvailableOn($date, $otherShift)
                    && ! in_array($otherShift, $selectedShiftsByDate->get($slotData['date'], []), true)) {
                    $selectedShift = $otherShift;
                    $automaticFallbacks[] = "{$zone->name} on {$date->format('M j, Y')}: {$requestedShift} is unavailable, so {$otherShift} was selected.";
                }
            }
            $requestedSlots[] = [
                'park_zone_id' => $zone->id,
                'safari_date' => $date,
                'safari_shift' => $selectedShift,
                'was_available' => $zone->isShiftAvailableOn($date, $selectedShift),
            ];
        }

        $firstRequestedSlot = $requestedSlots[0];
        $confirmationToken = $draft['confirmation_token'] ?? Str::random(64);
        $bookingRequestId = $draft['booking_request_id'] ?? null;
        $preferredShifts = $selectedSlots->pluck('shift')->unique();
        $bookingShift = $preferredShifts->count() === 1 ? $preferredShifts->first() : 'Any';

        DB::transaction(function () use ($draft, $bookingRequestId, $bookingShift, $firstRequestedSlot, $requestedSlots, $confirmationToken): void {
            $couponId = null;
            if (! empty($draft['coupon_name'])) {
                $coupon = Coupon::query()
                    ->whereRaw('LOWER(name) = ?', [mb_strtolower(trim($draft['coupon_name']))])
                    ->lockForUpdate()
                    ->first();

                if ($coupon !== null
                    && $coupon->starts_at->lte(CarbonImmutable::today())
                    && $coupon->ends_at->gte(CarbonImmutable::today())) {
                    $normalizedEmail = mb_strtolower(trim($draft['email']));
                    $alreadyUsedByCustomer = SafariBookingRequest::query()
                        ->where('coupon_id', $coupon->id)
                        ->whereRaw('LOWER(email) = ?', [$normalizedEmail])
                        ->exists();
                    $usedCustomerCount = SafariBookingRequest::query()
                        ->where('coupon_id', $coupon->id)
                        ->selectRaw('COUNT(DISTINCT LOWER(email)) as aggregate')
                        ->value('aggregate');

                    if ($coupon->usage_limit === null
                        || $alreadyUsedByCustomer
                        || (int) $usedCustomerCount < $coupon->usage_limit) {
                        $couponId = $coupon->id;
                    }
                }
            }

            $bookingAttributes = [
                'national_park_id' => $draft['national_park_id'],
                'park_zone_id' => $firstRequestedSlot['park_zone_id'],
                'safari_mode' => $draft['safari_mode'],
                'safari_shift' => $bookingShift,
                'safari_date' => $firstRequestedSlot['safari_date'],
                'name' => $draft['name'],
                'phone' => $draft['phone'],
                'phone_country' => $draft['phone_country'],
                'phone_country_code' => $draft['phone_country_code'],
                'email' => $draft['email'],
                'coupon_name' => $draft['coupon_name'] ?? null,
                'coupon_id' => $couponId,
                'confirmation_token' => $confirmationToken,
                'was_available' => $firstRequestedSlot['was_available'],
            ];
            $bookingRequest = SafariBookingRequest::query()->find($bookingRequestId);
            if ($bookingRequest === null) {
                $bookingRequest = SafariBookingRequest::create($bookingAttributes);
            } else {
                $bookingRequest->update($bookingAttributes);
            }

            $bookingRequest->slots()->createMany($requestedSlots);
        });

        $request->session()->forget('safari_booking_draft');

        $redirect = redirect()->route('safari-booking.checking', ['confirmationToken' => $confirmationToken]);
        if ($automaticFallbacks !== []) {
            $redirect->with('status', implode(' ', $automaticFallbacks));
        }

        return $redirect;
    }

    /** @return array<int, array{iso2: string, name: string, flag: string, calling_code: string}> */
    private function countryDialCodes(): array
    {
        $countriesCacheKey = 'country_dial_codes.v2';
        $fallbackCacheKey = 'country_dial_codes_fallback.v1';
        $cachedCountries = Cache::get($countriesCacheKey);
        if (is_array($cachedCountries)) {
            return $cachedCountries;
        }

        $cachedFallback = Cache::get($fallbackCacheKey);
        if (is_array($cachedFallback)) {
            return $cachedFallback;
        }

        $apiKey = (string) config('services.rest_countries.key');
        if ($apiKey === '') {
            $fallbackCountries = $this->fallbackCountryDialCodes();
            Cache::put($fallbackCacheKey, $fallbackCountries, now()->addMinutes(10));

            return $fallbackCountries;
        }

        try {
            $fetchPage = function (int $offset) use ($apiKey): array {
                return Http::withToken($apiKey)
                    ->acceptJson()
                    ->connectTimeout(3)
                    ->timeout(8)
                    ->get('https://api.restcountries.com/countries/v5', [
                        'limit' => 100,
                        'offset' => $offset,
                        'response_fields' => 'names.common,codes.alpha_2,flag.emoji,calling_codes',
                    ])
                    ->throw()
                    ->json();
            };

            $firstPage = $fetchPage(0);
            $countryRecords = collect(data_get($firstPage, 'data.objects', []));
            $totalCountries = (int) data_get($firstPage, 'data.meta.total', $countryRecords->count());

            for ($offset = 100; $offset < $totalCountries; $offset += 100) {
                $countryRecords = $countryRecords->merge(
                    data_get($fetchPage($offset), 'data.objects', [])
                );
            }

            $countries = $countryRecords
                ->map(function (array $country): ?array {
                    $iso2 = data_get($country, 'codes.alpha_2');
                    $name = data_get($country, 'names.common');
                    $callingCode = collect(data_get($country, 'calling_codes', []))
                        ->map(fn (mixed $code): string => preg_replace('/\D+/', '', (string) $code) ?? '')
                        ->first(fn (string $code): bool => $code !== '');

                    if (! is_string($iso2) || $iso2 === '' || ! is_string($name) || $callingCode === null) {
                        return null;
                    }

                    return [
                        'iso2' => $iso2,
                        'name' => $name,
                        'flag' => (string) data_get($country, 'flag.emoji', ''),
                        'calling_code' => '+'.$callingCode,
                    ];
                })
                ->filter()
                ->unique('iso2')
                ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
                ->values();

            if ($countries->isNotEmpty()) {
                $countryOptions = $countries->all();
                Cache::put($countriesCacheKey, $countryOptions, now()->addDay());
                Cache::forget($fallbackCacheKey);

                return $countryOptions;
            }
        } catch (Throwable) {
            $fallbackCountries = $this->fallbackCountryDialCodes();
            Cache::put($fallbackCacheKey, $fallbackCountries, now()->addMinutes(10));

            return $fallbackCountries;
        }

        $fallbackCountries = $this->fallbackCountryDialCodes();
        Cache::put($fallbackCacheKey, $fallbackCountries, now()->addMinutes(10));

        return $fallbackCountries;
    }

    /** @return array<int, array{iso2: string, name: string, flag: string, calling_code: string}> */
    private function fallbackCountryDialCodes(): array
    {
        return [
            ['iso2' => 'AU', 'name' => 'Australia', 'flag' => '', 'calling_code' => '+61'],
            ['iso2' => 'CA', 'name' => 'Canada', 'flag' => '', 'calling_code' => '+1'],
            ['iso2' => 'IN', 'name' => 'India', 'flag' => '', 'calling_code' => '+91'],
            ['iso2' => 'GB', 'name' => 'United Kingdom', 'flag' => '', 'calling_code' => '+44'],
            ['iso2' => 'US', 'name' => 'United States', 'flag' => '', 'calling_code' => '+1'],
        ];
    }
}
