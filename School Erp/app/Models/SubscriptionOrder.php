<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'plan_id',
        'amount',
        'gateway',
        'status',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
