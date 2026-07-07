<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolExpense extends Model
{
    use HasFactory, BelongsToSchool;

    protected $table = 'school_expenses';

    protected $fillable = [
        'school_id',
        'expense_head_id',
        'expense_voucher_id',
        'voucher_payment_id',
        'title',
        'category',
        'amount',
        'expense_date',
        'payment_mode',
        'bank_name',
        'check_issue_date',
        'branch',
        'description',
        'reference_no',
        'receipt_no',
        'paid_to',
        'status',
        'created_by',
    ];

    protected $casts = [
        'amount'           => 'decimal:2',
        'expense_date'     => 'date',
        'check_issue_date' => 'date',
    ];

    public static function categories(): array
    {
        return [
            'salary'      => 'Salary & Wages',
            'maintenance' => 'Maintenance & Repairs',
            'utilities'   => 'Utilities',
            'transport'   => 'Transport',
            'supplies'    => 'Supplies & Stationery',
            'events'      => 'Events & Activities',
            'other'       => 'Other',
        ];
    }

    public static function paymentModes(): array
    {
        return [
            'cash'          => 'Cash',
            'bank_transfer' => 'Bank Transfer',
            'cheque'        => 'Cheque',
            'upi'           => 'UPI',
        ];
    }

    public function getCategoryLabelAttribute(): string
    {
        if ($this->expenseHead) {
            return $this->expenseHead->name;
        }
        return self::categories()[$this->category] ?? ucfirst($this->category);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function expenseHead()
    {
        return $this->belongsTo(ExpenseHead::class, 'expense_head_id');
    }

    public function voucher()
    {
        return $this->belongsTo(ExpenseVoucher::class, 'expense_voucher_id');
    }

    public function payment()
    {
        return $this->belongsTo(VoucherPayment::class, 'voucher_payment_id');
    }
}
