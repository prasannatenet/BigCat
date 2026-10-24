<?php

namespace Tests\Feature;

use App\Models\NationalPark;
use App\Models\ParkZone;
use App\Models\SafariBookingRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SafariBookingRequestControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_coupon_is_shown_and_saved_in_the_booking_draft(): void
    {
        $park = NationalPark::factory()->create();
        $zone = ParkZone::factory()->create([
            'national_park_id' => $park->id,
            'default_available' => true,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('name="coupon_name"', false);

        $date = now()->addDays(2)->toDateString();

        $this->post(route('safari-booking.details'), [
            'national_park_id' => $park->id,
            'zone_ids' => [$zone->id],
            'safari_mode' => 'Gypsy',
            'safari_dates' => [$date],
            'name' => 'Test Customer',
            'phone' => '1234567890',
            'email' => 'customer@example.com',
            'coupon_name' => 'SAFARI25',
        ])->assertRedirect(route('safari-booking.slots'))
            ->assertSessionHas('safari_booking_draft.coupon_name', 'SAFARI25');

        $this->post(route('safari-booking-requests.store'), [
            'visits' => [
                ['date' => $date, 'morning' => $zone->id],
            ],
        ])->assertRedirect();

        $bookingRequest = SafariBookingRequest::query()->firstOrFail();
        $this->assertSame('SAFARI25', $bookingRequest->coupon_name);

        $this->get(route('safari-booking.confirmation', $bookingRequest->confirmation_token))
            ->assertOk()
            ->assertSee('SAFARI25');
    }
}
