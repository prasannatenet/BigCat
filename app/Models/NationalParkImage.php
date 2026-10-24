<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NationalParkImage extends Model
{
    protected $fillable = [
        'national_park_id',
        'path',
        'position',
    ];

    protected function casts(): array
    {
        return ['position' => 'integer'];
    }

    public function nationalPark(): BelongsTo
    {
        return $this->belongsTo(NationalPark::class);
    }
}
