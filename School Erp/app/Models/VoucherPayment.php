<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherPayment extends Model
{
    use HasFactory, BelongsToSchool;

    protected $table = 'voucher_payments';

    protected $fillable = [
        'school_id',
        'expense_voucher_id',
        'payment_date',
        'invoice_no',
        'payment_mode',
        'bank_name',
        'check_issue_date',
        'branch',
        'remarks',
        'amount',
        'created_by',
    ];

    protected $casts = [
        'amount'           => 'decimal:2',
        'payment_date'     => 'date',
        'check_issue_date' => 'date',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function voucher()
    {
        return $this->belongsTo(ExpenseVoucher::class, 'expense_voucher_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
