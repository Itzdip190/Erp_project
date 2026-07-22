<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RouteStop extends Model
{
    use HasFactory, BelongsToSchool;

    protected $table = 'route_stops';

    protected $fillable = [
        'school_id',
        'route_id',
        'stop_id',
        'pick_fare',
        'drop_fare',
        'pickup_time',
        'drop_time',
    ];

    protected $casts = [
        'pick_fare' => 'decimal:2',
        'drop_fare' => 'decimal:2',
    ];

    public function route()
    {
        return $this->belongsTo(TransportRoute::class, 'route_id');
    }

    public function stop()
    {
        return $this->belongsTo(Stop::class, 'stop_id');
    }
}
