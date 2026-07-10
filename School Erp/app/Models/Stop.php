<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stop extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'name',
        'landmark',
        'fare',
        'pick_fare',
        'drop_fare',
    ];

    protected $casts = [
        'fare'      => 'decimal:2',
        'pick_fare' => 'decimal:2',
        'drop_fare' => 'decimal:2',
    ];

    /**
     * Total transport fare = pick + drop
     * Falls back to legacy 'fare' if split fares not set.
     */
    public function getTotalFareAttribute(): float
    {
        $pickFare = (float) ($this->pick_fare ?? 0);
        $dropFare = (float) ($this->drop_fare ?? 0);

        if ($pickFare + $dropFare > 0) {
            return $pickFare + $dropFare;
        }

        // Legacy fallback
        return (float) ($this->fare ?? 0);
    }
}
