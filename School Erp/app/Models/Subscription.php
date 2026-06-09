<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'plan_id',
        'subscription_ends_at',
        'status',
    ];

    protected $casts = [
        'subscription_ends_at' => 'datetime',
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
