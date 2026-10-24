<?php

namespace App\Models;

use Database\Factories\ParkZoneSlotFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParkZoneSlot extends Model
{
    /** @use HasFactory<ParkZoneSlotFactory> */
    use HasFactory;

    protected $fillable = [
        'park_zone_id',
        'slot_date',
        'is_available',
        'morning_available',
        'evening_available',
    ];

    protected function casts(): array
    {
        return [
            'slot_date' => 'date',
            'is_available' => 'boolean',
            'morning_available' => 'boolean',
            'evening_available' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (ParkZoneSlot $slot): void {
            $attributes = $slot->getAttributes();
            $isAvailable = (bool) ($attributes['is_available'] ?? true);

            if (! array_key_exists('morning_available', $attributes)) {
                $slot->morning_available = $isAvailable;
            }

            if (! array_key_exists('evening_available', $attributes)) {
                $slot->evening_available = $isAvailable;
            }
        });
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(ParkZone::class, 'park_zone_id');
    }
}
