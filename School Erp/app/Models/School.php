<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'custom_domain',
        'logo',
        'address',
        'phone',
        'dashboard_theme',
        'status',
        'sms_config',
        'late_grace_minutes',
        'staff_punch_in_start',
        'staff_punch_in_end',
    ];

    protected $casts = [
        'sms_config' => 'array',
        'late_grace_minutes' => 'integer',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription(): ?Subscription
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->where('subscription_ends_at', '>', now())
            ->latest()
            ->first();
    }

    public function subscriptionOrders()
    {
        return $this->hasMany(SubscriptionOrder::class);
    }
}

