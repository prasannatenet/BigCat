<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingOptionsResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'national_parks' => collect($this->resource['national_parks'])->map(fn ($park): array => [
                'id' => $park->id,
                'name' => $park->name,
                'location' => $park->location,
                'description' => $park->description,
                'images' => $park->images->map(fn ($image): string => asset('storage/'.$image->path))->values(),
                'zones' => $park->zones->map(fn ($zone): array => [
                    'id' => $zone->id,
                    'name' => $zone->name,
                    'description' => $zone->short_description,
                    'images' => $zone->images->map(fn ($image): string => asset('storage/'.$image->path))->values(),
                ])->values(),
            ])->values(),
            'safari_modes' => $this->resource['safari_modes'],
            'vehicles' => collect($this->resource['vehicles'])->map(fn ($vehicle): array => [
                'id' => $vehicle->id,
                'type' => $vehicle->type,
                'name' => $vehicle->name,
                'total_seats' => $vehicle->total_seat,
                'price_indian' => $vehicle->price_indian,
                'price_foreigner' => $vehicle->price_foreigner,
            ])->values(),
        ];
    }
}
