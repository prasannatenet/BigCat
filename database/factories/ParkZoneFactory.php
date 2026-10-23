<?php

namespace Database\Factories;

use App\Models\NationalPark;
use App\Models\ParkZone;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ParkZone>
 */
class ParkZoneFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'national_park_id' => NationalPark::factory(),
            'name' => fake()->unique()->words(2, true),
            'default_available' => fake()->boolean(),
        ];
    }
}
