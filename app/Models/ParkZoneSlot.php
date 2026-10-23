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
    ];

    protected function casts(): array
    {
        return [
            'slot_date' => 'date',
            'is_available' => 'boolean',
        ];
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(ParkZone::class, 'park_zone_id');
    }
}
