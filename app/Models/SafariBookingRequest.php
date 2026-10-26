<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SafariBookingRequest extends Model
{
    protected $fillable = [
        'national_park_id',
        'park_zone_id',
        'safari_mode',
        'safari_shift',
        'safari_date',
        'requested_dates',
        'requested_zone_ids',
        'name',
        'phone',
        'phone_country',
        'phone_country_code',
        'email',
        'coupon_name',
        'coupon_id',
        'confirmation_token',
        'was_available',
        'request_status',
    ];

    protected function casts(): array
    {
        return [
            'safari_date' => 'date',
            'requested_dates' => 'array',
            'requested_zone_ids' => 'array',
            'was_available' => 'boolean',
        ];
    }

    public function nationalPark(): BelongsTo
    {
        return $this->belongsTo(NationalPark::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(ParkZone::class, 'park_zone_id');
    }

    public function slots(): HasMany
    {
        return $this->hasMany(SafariBookingRequestSlot::class);
    }
}
