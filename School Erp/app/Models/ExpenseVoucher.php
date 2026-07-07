<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseVoucher extends Model
{
    use HasFactory, BelongsToSchool, SoftDeletes;

    protected $table = 'expense_vouchers';

    protected $fillable = [
        'school_id',
        'voucher_no',
        'expense_head_id',
        'amount',
        'expense_date',
        'reason',
        'remarks',
        'document_path',
        'approval_status',
        'payment_status',
        'created_by',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'expense_date' => 'date',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function expenseHead()
    {
        return $this->belongsTo(ExpenseHead::class, 'expense_head_id');
    }

    public function payments()
    {
        return $this->hasMany(VoucherPayment::class, 'expense_voucher_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Accessors for paid and due amounts
    public function getTotalPaidAttribute(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    public function getTotalDueAttribute(): float
    {
        return max(0.00, (float) $this->amount - $this->total_paid);
    }
}
