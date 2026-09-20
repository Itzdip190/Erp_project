<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransportRouteSessionFare extends Model
{
    use HasFactory, BelongsToSchool;

    protected $table = 'transport_route_session_fares';

    protected $fillable = [
        'school_id',
        'academic_session_id',
        'route_id',
        'stop_id',
        'pick_fare',
        'drop_fare',
    ];

    protected $casts = [
        'pick_fare' => 'decimal:2',
        'drop_fare' => 'decimal:2',
    ];

    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class, 'academic_session_id');
    }

    public function route()
    {
        return $this->belongsTo(TransportRoute::class, 'route_id');
    }

    public function stop()
    {
        return $this->belongsTo(Stop::class, 'stop_id');
    }

    /**
     * Total fare = pick + drop
     */
    public function getTotalFareAttribute(): float
    {
        return (float) ($this->pick_fare ?? 0) + (float) ($this->drop_fare ?? 0);
    }
}
