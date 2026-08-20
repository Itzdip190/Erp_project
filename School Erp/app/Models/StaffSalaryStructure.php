<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaffSalaryStructure extends Model
{
    use HasFactory, SoftDeletes, BelongsToSchool;

    protected $table = 'staff_salary_structures';

    protected $fillable = [
        'school_id',
        'staff_id',
        'basic_salary',
        'salary_type',
        'hra',
        'da',
        'ta',
        'allowance',
        'pf',
        'esi',
        'tds',
        'prof_tax',
        'effective_from',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'hra' => 'decimal:2',
        'da' => 'decimal:2',
        'ta' => 'decimal:2',
        'allowance' => 'decimal:2',
        'pf' => 'decimal:2',
        'esi' => 'decimal:2',
        'tds' => 'decimal:2',
        'prof_tax' => 'decimal:2',
        'effective_from' => 'date',
        'is_active' => 'boolean',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get total allowances sum
     */
    public function getTotalAllowancesAttribute(): float
    {
        return (float) ($this->hra + $this->da + $this->ta + $this->allowance);
    }

    /**
     * Get total deductions sum
     */
    public function getTotalDeductionsAttribute(): float
    {
        return (float) ($this->pf + $this->esi + $this->tds + $this->prof_tax);
    }

    /**
     * Get net estimated monthly salary
     */
    public function getNetSalaryAttribute(): float
    {
        return max(0, (float) ($this->basic_salary + $this->total_allowances - $this->total_deductions));
    }
}
