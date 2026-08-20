<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollDeductionSetting extends Model
{
    use HasFactory, BelongsToSchool;

    protected $table = 'payroll_deduction_settings';

    protected $fillable = [
        'school_id',
        'salary_calculation_base',
        'deduction_rule',
        'deduction_multiplier',
        'effective_from',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'deduction_multiplier' => 'float',
        'is_active' => 'boolean',
        'effective_from' => 'date',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Helper to get active Payroll Deduction Setting for a school.
     */
    public static function getForSchool(int $schoolId, ?string $date = null): self
    {
        $query = static::where('school_id', $schoolId)->where('is_active', true);

        if ($date) {
            $query->where(function ($q) use ($date) {
                $q->whereNull('effective_from')
                  ->orWhere('effective_from', '<=', $date);
            });
        }

        $setting = $query->latest('effective_from')->latest('id')->first();

        if (!$setting) {
            // Return fallback default instance if not configured yet
            $setting = new static([
                'school_id' => $schoolId,
                'salary_calculation_base' => '30_days',
                'deduction_rule' => 'one_day',
                'deduction_multiplier' => 1.00,
                'effective_from' => null,
                'is_active' => true,
            ]);
        }

        return $setting;
    }
}
