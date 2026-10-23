<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NationalParkSlotAvailability extends Model
{
    protected $fillable = [
        'national_park_id',
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

    public function nationalPark(): BelongsTo
    {
        return $this->belongsTo(NationalPark::class);
    }
}
