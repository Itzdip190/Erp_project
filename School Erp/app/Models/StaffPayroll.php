<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaffPayroll extends Model
{
    use HasFactory, SoftDeletes, BelongsToSchool;

    protected $table = 'staff_payrolls';

    protected $fillable = [
        'school_id',
        'staff_id',
        'payroll_month',
        'salary_month',
        'salary_year',
        'total_days',
        'present_days',
        'absent_days',
        'leave_days',
        'half_days',
        'payable_days',
        'basic_salary',
        'gross_salary',
        'deductions',
        'attendance_deduction',
        'attendance_deduction_days',
        'attendance_deduction_multiplier',
        'allowances',
        'net_payable',
        'paid_amount',
        'remaining_balance',
        'status',
        'payment_status',
        'is_frozen',
        'generated_by',
        'finalised_at',
    ];

    protected $casts = [
        'total_days' => 'integer',
        'present_days' => 'decimal:2',
        'absent_days' => 'decimal:2',
        'leave_days' => 'decimal:2',
        'half_days' => 'decimal:2',
        'payable_days' => 'decimal:2',
        'basic_salary' => 'decimal:2',
        'gross_salary' => 'decimal:2',
        'deductions' => 'decimal:2',
        'attendance_deduction' => 'decimal:2',
        'attendance_deduction_days' => 'decimal:2',
        'attendance_deduction_multiplier' => 'decimal:2',
        'allowances' => 'decimal:2',
        'net_payable' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_balance' => 'decimal:2',
        'is_frozen' => 'boolean',
        'finalised_at' => 'datetime',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function payments()
    {
        return $this->hasMany(StaffPayrollPayment::class, 'staff_payroll_id');
    }

    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    /**
     * Recalculate paid amount and remaining balance based on payments.
     */
    public function updatePaymentStatus(): void
    {
        $totalPaid = $this->payments()->sum('amount');
        $this->paid_amount = $totalPaid;
        $this->remaining_balance = max(0, $this->net_payable - $totalPaid);

        if ($this->paid_amount <= 0) {
            $this->payment_status = 'unpaid';
        } elseif ($this->remaining_balance <= 0) {
            $this->payment_status = 'paid';
        } else {
            $this->payment_status = 'partially_paid';
        }

        $this->save();
    }
}
