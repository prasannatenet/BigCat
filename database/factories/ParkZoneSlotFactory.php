<?php

namespace Database\Factories;

use App\Models\ParkZone;
use App\Models\ParkZoneSlot;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ParkZoneSlot>
 */
class ParkZoneSlotFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $isAvailable = fake()->boolean();

        return [
            'park_zone_id' => ParkZone::factory(),
            'slot_date' => fake()->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
            'is_available' => $isAvailable,
            'morning_available' => $isAvailable,
            'evening_available' => $isAvailable,
        ];
    }
}
