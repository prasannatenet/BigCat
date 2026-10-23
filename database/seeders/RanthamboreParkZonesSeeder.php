<?php

namespace Database\Seeders;

use App\Models\NationalPark;
use Illuminate\Database\Seeder;

class RanthamboreParkZonesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $park = NationalPark::query()
            ->where('name', 'Ranthambore National Park')
            ->firstOrFail();

        foreach (range(1, 10) as $zoneNumber) {
            $park->zones()->firstOrCreate(
                ['name' => "Zone {$zoneNumber}"],
                ['default_available' => true],
            );
        }
    }
}
