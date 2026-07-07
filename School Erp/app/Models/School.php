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
        'email',
        'state',
        'school_type',
        'director_name',
        'dashboard_theme',
        'status',
        'sms_config',
        'late_grace_minutes',
        'staff_punch_in_start',
        'staff_punch_in_end',
        'disabled_modules',
        'sidebar_order',
    ];

    protected $casts = [
        'sms_config' => 'array',
        'late_grace_minutes' => 'integer',
        'udise_data' => 'array',
        'disabled_modules' => 'array',
        'sidebar_order' => 'array',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function staff()
    {
        return $this->hasMany(Staff::class);
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

    public static function getStatesList()
    {
        return [
            'AN' => 'Andaman and Nicobar Islands',
            'AP' => 'Andhra Pradesh',
            'AR' => 'Arunachal Pradesh',
            'AS' => 'Assam',
            'BR' => 'Bihar',
            'CH' => 'Chandigarh',
            'CG' => 'Chhattisgarh',
            'DN' => 'Dadra and Nagar Haveli and Daman and Diu',
            'DL' => 'Delhi',
            'GA' => 'Goa',
            'GJ' => 'Gujarat',
            'HR' => 'Haryana',
            'HP' => 'Himachal Pradesh',
            'JK' => 'Jammu and Kashmir',
            'JH' => 'Jharkhand',
            'KA' => 'Karnataka',
            'KL' => 'Kerala',
            'LA' => 'Ladakh',
            'LD' => 'Lakshadweep',
            'MP' => 'Madhya Pradesh',
            'MH' => 'Maharashtra',
            'MN' => 'Manipur',
            'ML' => 'Meghalaya',
            'MZ' => 'Mizoram',
            'NL' => 'Nagaland',
            'OD' => 'Odisha',
            'PY' => 'Puducherry',
            'PB' => 'Punjab',
            'RJ' => 'Rajasthan',
            'SK' => 'Sikkim',
            'TN' => 'Tamil Nadu',
            'TG' => 'Telangana',
            'TR' => 'Tripura',
            'UP' => 'Uttar Pradesh',
            'UK' => 'Uttarakhand',
            'WB' => 'West Bengal',
        ];
    }

    public static function generateNextCode(string $stateCode): string
    {
        $stateCode = strtoupper($stateCode);
        $prefix = $stateCode . 'EC';
        
        $schoolCodes = self::where('code', 'LIKE', $prefix . '%')
            ->pluck('code')
            ->toArray();
            
        $requestCodes = SchoolRequest::where('code', 'LIKE', $prefix . '%')
            ->pluck('code')
            ->toArray();
            
        $allCodes = array_unique(array_merge($schoolCodes, $requestCodes));
        
        $maxNum = 0;
        foreach ($allCodes as $code) {
            $numPart = substr($code, strlen($prefix));
            if (is_numeric($numPart)) {
                $num = (int)$numPart;
                if ($num > $maxNum) {
                    $maxNum = $num;
                }
            }
        }
        
        $nextNum = $maxNum + 1;
        return $prefix . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
    }
}

