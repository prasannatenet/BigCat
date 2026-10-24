<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SafariBookingRequestSlot extends Model
{
    protected $fillable = [
        'safari_booking_request_id',
        'park_zone_id',
        'safari_date',
        'safari_shift',
        'was_available',
    ];

    protected function casts(): array
    {
        return [
            'safari_date' => 'date',
            'was_available' => 'boolean',
        ];
    }

    public function bookingRequest(): BelongsTo
    {
        return $this->belongsTo(SafariBookingRequest::class, 'safari_booking_request_id');
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(ParkZone::class, 'park_zone_id');
    }
}
