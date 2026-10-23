<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    #[Fillable(['type', 'name', 'total_seat', 'price_indian', 'price_foreigner'])]
    protected $fillable = ['type', 'name', 'total_seat', 'price_indian', 'price_foreigner'];

    protected function casts(): array
    {
        return [
            'total_seat' => 'integer',
            'price_indian' => 'decimal:2',
            'price_foreigner' => 'decimal:2',
        ];
    }
}
