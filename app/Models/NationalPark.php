<?php

namespace App\Models;

use Database\Factories\NationalParkFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NationalPark extends Model
{
    /** @use HasFactory<NationalParkFactory> */
    use HasFactory;

    #[Fillable(['name', 'location', 'description', 'entry_fee_indian', 'entry_fee_foreigner'])]
    protected $fillable = ['name', 'location', 'description', 'entry_fee_indian', 'entry_fee_foreigner'];

    protected function casts(): array
    {
        return [
            'entry_fee_indian' => 'decimal:2',
            'entry_fee_foreigner' => 'decimal:2',
        ];
    }

    public function zones(): HasMany
    {
        return $this->hasMany(ParkZone::class);
    }

    public function slotAvailability(): HasMany
    {
        return $this->hasMany(NationalParkSlotAvailability::class);
    }
}
