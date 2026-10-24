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
        'short_description',
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

    public function images(): HasMany
    {
        return $this->hasMany(ParkZoneImage::class)->orderBy('position');
    }

    public function isAvailableOn(CarbonInterface $date): bool
    {
        $date = $date->toDateString();
        $zoneSlot = $this->slots()->whereDate('slot_date', $date)->first();

        if ($zoneSlot !== null) {
            return $zoneSlot->morning_available || $zoneSlot->evening_available;
        }

        $parkAvailability = $this->nationalPark
            ->slotAvailability()
            ->whereDate('slot_date', $date)
            ->value('is_available');

        return $parkAvailability !== null
            ? (bool) $parkAvailability
            : $this->default_available;
    }

    public function isShiftAvailableOn(CarbonInterface $date, string $shift): bool
    {
        $date = $date->toDateString();
        $zoneSlot = $this->slots()->whereDate('slot_date', $date)->first();

        if ($zoneSlot !== null) {
            return match ($shift) {
                'Morning' => $zoneSlot->morning_available,
                'Evening' => $zoneSlot->evening_available,
                'Any' => $zoneSlot->morning_available || $zoneSlot->evening_available,
                default => false,
            };
        }

        $parkAvailability = $this->nationalPark
            ->slotAvailability()
            ->whereDate('slot_date', $date)
            ->value('is_available');

        return $parkAvailability !== null
            ? (bool) $parkAvailability
            : $this->default_available;
    }

    public function isExplicitShiftAvailableOn(CarbonInterface $date, string $shift): bool
    {
        $zoneSlot = $this->slots()->whereDate('slot_date', $date->toDateString())->first();
        if ($zoneSlot === null) {
            return false;
        }

        return match ($shift) {
            'Morning' => $zoneSlot->morning_available,
            'Evening' => $zoneSlot->evening_available,
            'Any' => $zoneSlot->morning_available || $zoneSlot->evening_available,
            default => false,
        };
    }
}
