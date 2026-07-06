<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleTrip extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'vehicle_id',
        'route_id',
        'trip_name',
        'type',
        'start_time',
        'end_time',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function route()
    {
        return $this->belongsTo(TransportRoute::class, 'route_id');
    }
}
