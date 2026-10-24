<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParkZoneImage extends Model
{
    protected $fillable = [
        'park_zone_id',
        'path',
        'position',
    ];

    protected function casts(): array
    {
        return ['position' => 'integer'];
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(ParkZone::class, 'park_zone_id');
    }
}
