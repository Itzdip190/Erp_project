<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolIncome extends Model
{
    use HasFactory, BelongsToSchool;

    protected $table = 'school_incomes';

    protected $fillable = [
        'school_id',
        'income_head_id',
        'income_voucher_id',
        'voucher_receipt_id',
        'title',
        'category',
        'amount',
        'income_date',
        'payment_mode',
        'bank_name',
        'check_issue_date',
        'branch',
        'description',
        'reference_no',
        'receipt_no',
        'received_from',
        'status',
        'created_by',
    ];

    protected $casts = [
        'amount'           => 'decimal:2',
        'income_date'      => 'date',
        'check_issue_date' => 'date',
    ];

    public static function categories(): array
    {
        return [
            'fees'       => 'Academic Fees',
            'admissions' => 'Admission & Registration',
            'transport'  => 'Transport Fees',
            'sales'      => 'Store & Uniform Sales',
            'donations'  => 'Donations & Sponsorships',
            'events'     => 'Events & Functions',
            'other'      => 'Other',
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
        if ($this->incomeHead) {
            return $this->incomeHead->name;
        }
        return self::categories()[$this->category] ?? ucfirst($this->category);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function incomeHead()
    {
        return $this->belongsTo(IncomeHead::class, 'income_head_id');
    }

    public function voucher()
    {
        return $this->belongsTo(IncomeVoucher::class, 'income_voucher_id');
    }

    public function receipt()
    {
        return $this->belongsTo(VoucherReceipt::class, 'voucher_receipt_id');
    }
}
