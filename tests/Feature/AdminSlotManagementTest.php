<?php

namespace Tests\Feature;

use App\Models\NationalPark;
use App\Models\NationalParkSlotAvailability;
use App\Models\ParkZone;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminSlotManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_multiple_zones_with_default_and_date_availability(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $park = NationalPark::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.slots.store'), [
            'national_park_id' => $park->id,
            'zones' => [
                [
                    'name' => 'North Zone',
                    'default_available' => '1',
                    'dates' => [
                        ['date' => '2030-05-01', 'is_available' => '0'],
                        ['date' => '', 'is_available' => '1'],
                    ],
                ],
                [
                    'name' => 'South Zone',
                    'default_available' => '0',
                    'dates' => [],
                ],
            ],
        ]);

        $response->assertRedirect(route('admin.slots.index', ['park' => $park->id]));
        $this->assertDatabaseHas('park_zones', [
            'national_park_id' => $park->id,
            'name' => 'North Zone',
            'default_available' => true,
        ]);
        $this->assertDatabaseHas('park_zones', [
            'national_park_id' => $park->id,
            'name' => 'South Zone',
            'default_available' => false,
        ]);
        $this->assertTrue(DB::table('park_zone_slots')
            ->whereDate('slot_date', '2030-05-01')
            ->where('is_available', false)
            ->exists());
        $this->assertDatabaseCount('park_zones', 2);
        $this->assertDatabaseCount('park_zone_slots', 1);
    }

    public function test_admin_can_update_a_zone_and_replace_its_date_overrides(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $park = NationalPark::factory()->create();
        $zone = ParkZone::factory()->create([
            'national_park_id' => $park->id,
            'name' => 'Old Zone',
            'default_available' => true,
        ]);
        $zone->slots()->create([
            'slot_date' => '2030-05-01',
            'is_available' => true,
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.slots.update', $zone), [
            'name' => 'Updated Zone',
            'default_available' => '0',
            'dates' => [
                ['date' => '2030-05-02', 'is_available' => '0'],
            ],
        ]);

        $response->assertRedirect(route('admin.slots.index', ['park' => $park->id]));
        $this->assertDatabaseHas('park_zones', [
            'id' => $zone->id,
            'name' => 'Updated Zone',
            'default_available' => false,
        ]);
        $this->assertFalse(DB::table('park_zone_slots')
            ->where('park_zone_id', $zone->id)
            ->whereDate('slot_date', '2030-05-01')
            ->exists());
        $this->assertTrue(DB::table('park_zone_slots')
            ->where('park_zone_id', $zone->id)
            ->whereDate('slot_date', '2030-05-02')
            ->where('is_available', false)
            ->exists());
    }

    public function test_admin_can_set_multiple_park_wide_dates_across_months(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $park = NationalPark::factory()->create();
        $otherPark = NationalPark::factory()->create();
        $zone = ParkZone::factory()->create([
            'national_park_id' => $park->id,
            'default_available' => false,
        ]);
        $secondZone = ParkZone::factory()->create([
            'national_park_id' => $park->id,
            'default_available' => false,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.slots.park-availability.update', $park), [
            'year' => '2030',
            'availability' => [
                ['date' => '2030-01-15', 'is_available' => '1'],
                ['date' => '2030-08-22', 'is_available' => '0'],
            ],
        ]);

        $response->assertRedirect(route('admin.slots.index', ['park' => $park->id, 'year' => 2030]));
        $this->assertDatabaseCount('national_park_slot_availabilities', 2);
        $this->assertTrue($zone->fresh()->isAvailableOn(CarbonImmutable::parse('2030-01-15')));
        $this->assertFalse($zone->fresh()->isAvailableOn(CarbonImmutable::parse('2030-08-22')));
        $this->assertTrue($secondZone->fresh()->isAvailableOn(CarbonImmutable::parse('2030-01-15')));
        $this->assertFalse($secondZone->fresh()->isAvailableOn(CarbonImmutable::parse('2030-08-22')));
        $this->assertSame(0, $otherPark->slotAvailability()->count());
    }

    public function test_admin_cannot_save_the_same_park_date_more_than_once(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $park = NationalPark::factory()->create();

        $response = $this->actingAs($admin)->put(route('admin.slots.park-availability.update', $park), [
            'year' => '2030',
            'availability' => [
                ['date' => '2030-01-15', 'is_available' => '1'],
                ['date' => '2030-01-15', 'is_available' => '0'],
            ],
        ]);

        $response->assertSessionHasErrors('availability.0.date');
        $this->assertDatabaseCount('national_park_slot_availabilities', 0);
    }

    public function test_zone_specific_date_override_takes_precedence_over_park_calendar(): void
    {
        $park = NationalPark::factory()->create();
        $zone = ParkZone::factory()->create([
            'national_park_id' => $park->id,
            'default_available' => false,
        ]);
        NationalParkSlotAvailability::query()->create([
            'national_park_id' => $park->id,
            'slot_date' => '2030-05-01',
            'is_available' => true,
        ]);
        $zone->slots()->create([
            'slot_date' => '2030-05-01',
            'is_available' => false,
        ]);

        $this->assertFalse($zone->isAvailableOn(CarbonImmutable::parse('2030-05-01')));
    }

    public function test_admin_cannot_save_a_park_wide_date_from_another_year(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $park = NationalPark::factory()->create();

        $response = $this->actingAs($admin)->put(route('admin.slots.park-availability.update', $park), [
            'year' => '2030',
            'availability' => [
                ['date' => '2031-01-15', 'is_available' => '1'],
            ],
        ]);

        $response->assertSessionHasErrors('availability.0.date');
        $this->assertDatabaseCount('national_park_slot_availabilities', 0);
    }

    public function test_admin_can_view_zones_for_a_selected_park(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $park = NationalPark::factory()->create(['name' => 'Pine Ridge National Park']);
        $zone = ParkZone::factory()->create([
            'national_park_id' => $park->id,
            'name' => 'North Zone',
        ]);
        $zone->slots()->create([
            'slot_date' => '2030-05-01',
            'is_available' => false,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.slots.index', [
            'park' => $park->id,
            'year' => 2030,
        ]));

        $response->assertOk()
            ->assertSee('Slot Booking')
            ->assertSee('Pine Ridge National Park')
            ->assertSee('North Zone')
            ->assertSee('Save park calendar')
            ->assertSee('May 1, 2030')
            ->assertSee('Zone-specific date overrides');
    }

    public function test_duplicate_dates_in_one_zone_are_rejected(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $park = NationalPark::factory()->create();

        $response = $this->actingAs($admin)->from(route('admin.slots.index'))->post(route('admin.slots.store'), [
            'national_park_id' => $park->id,
            'zones' => [
                [
                    'name' => 'North Zone',
                    'default_available' => '1',
                    'dates' => [
                        ['date' => '2030-05-01', 'is_available' => '0'],
                        ['date' => '2030-05-01', 'is_available' => '1'],
                    ],
                ],
            ],
        ]);

        $response->assertRedirect(route('admin.slots.index'));
        $response->assertSessionHasErrors('zones.0.dates');
        $this->assertDatabaseCount('park_zones', 0);
    }

    public function test_non_admin_users_cannot_manage_slots(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($user)->get(route('admin.slots.index'));

        $response->assertRedirect(route('admin.login'));
    }
}
