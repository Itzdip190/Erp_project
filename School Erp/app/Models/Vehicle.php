<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'vehicle_no',
        'vehicle_model',
        'driver_name',
        'driver_phone',
        'capacity',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'capacity' => 'integer',
    ];

    public function trips()
    {
        return $this->hasMany(VehicleTrip::class, 'vehicle_id');
    }

    public function expenses()
    {
        return $this->hasMany(VehicleExpense::class, 'vehicle_id');
    }
}
