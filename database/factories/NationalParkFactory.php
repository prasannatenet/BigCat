<?php

namespace Database\Factories;

use App\Models\NationalPark;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<NationalPark>
 */
class NationalParkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->city().' National Park',
            'location' => fake()->city().', India',
            'description' => fake()->sentence(),
            'entry_fee_indian' => fake()->randomFloat(2, 0, 1000),
            'entry_fee_foreigner' => fake()->randomFloat(2, 100, 5000),
        ];
    }
}
