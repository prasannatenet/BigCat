<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SafariBookingRequestResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'confirmation_token' => $this->confirmation_token,
            'status' => $this->request_status,
            'customer' => [
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'phone_country' => $this->phone_country,
                'phone_country_code' => $this->phone_country_code,
            ],
            'national_park' => [
                'id' => $this->national_park_id,
                'name' => $this->whenLoaded('nationalPark', fn () => $this->nationalPark->name),
            ],
            'safari_mode' => $this->safari_mode,
            'requested_dates' => $this->requested_dates ?? [],
            'requested_zone_ids' => $this->requested_zone_ids ?? [],
            'slots' => $this->whenLoaded('slots', fn (): array => $this->slots->map(fn ($slot): array => [
                'date' => $slot->safari_date->toDateString(),
                'zone' => [
                    'id' => $slot->park_zone_id,
                    'name' => $slot->zone->name,
                ],
                'shift' => $slot->safari_shift,
                'available' => $slot->was_available,
            ])->all()),
            'availability' => $this->was_available,
            'coupon_name' => $this->coupon_name,
            'created_at' => $this->created_at,
        ];
    }
}
