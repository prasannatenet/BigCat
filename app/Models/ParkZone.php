<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\ParkZoneFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParkZone extends Model
{
    /** @use HasFactory<ParkZoneFactory> */
    use HasFactory;

    protected $fillable = [
        'national_park_id',
        'name',
        'default_available',
    ];

    protected function casts(): array
    {
        return [
            'default_available' => 'boolean',
        ];
    }

    public function nationalPark(): BelongsTo
    {
        return $this->belongsTo(NationalPark::class);
    }

    public function slots(): HasMany
    {
        return $this->hasMany(ParkZoneSlot::class);
    }

    public function isAvailableOn(CarbonInterface $date): bool
    {
        $date = $date->toDateString();
        $zoneAvailability = $this->slots()->whereDate('slot_date', $date)->value('is_available');

        if ($zoneAvailability !== null) {
            return (bool) $zoneAvailability;
        }

        $parkAvailability = $this->nationalPark
            ->slotAvailability()
            ->whereDate('slot_date', $date)
            ->value('is_available');

        return $parkAvailability !== null
            ? (bool) $parkAvailability
            : $this->default_available;
    }
}
