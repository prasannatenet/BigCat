<?php

namespace Database\Factories;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Coupon>
 */
class CouponFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'type' => 'percentage',
            'discount_value' => fake()->randomFloat(2, 1, 100),
            'custom_text' => null,
            'starts_at' => '2030-01-01',
            'ends_at' => '2030-12-31',
        ];
    }
}
