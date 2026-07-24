<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'code',
        'name',
        'academic_year',
        'staff_type',
        'is_active',
        'unit',
        'leave_count',
        'pro_rata',
        'credit_on_joining',
        'non_carry_forward',
        'accrue_after_month',
        'allow_before_date',
        'gender_eligibility',
        'start_crediting_days',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'pro_rata' => 'boolean',
        'credit_on_joining' => 'boolean',
        'non_carry_forward' => 'boolean',
        'accrue_after_month' => 'boolean',
        'allow_before_date' => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
