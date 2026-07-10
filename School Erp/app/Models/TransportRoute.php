<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransportRoute extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'name',
        'description',
        'pick_fare',
        'drop_fare',
    ];

    protected $casts = [
        'pick_fare' => 'decimal:2',
        'drop_fare' => 'decimal:2',
    ];

    public function trips()
    {
        return $this->hasMany(VehicleTrip::class, 'route_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'transport_route_id');
    }

    /**
     * Total fare = pick + drop
     */
    public function getTotalFareAttribute(): float
    {
        return (float) ($this->pick_fare ?? 0) + (float) ($this->drop_fare ?? 0);
    }
}
