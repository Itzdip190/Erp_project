<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaffPayrollPayment extends Model
{
    use HasFactory, SoftDeletes, BelongsToSchool;

    protected $table = 'staff_payroll_payments';

    protected $fillable = [
        'school_id',
        'staff_payroll_id',
        'staff_id',
        'payment_type',
        'amount',
        'payment_date',
        'payment_method',
        'reference_no',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function staffPayroll()
    {
        return $this->belongsTo(StaffPayroll::class, 'staff_payroll_id');
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
