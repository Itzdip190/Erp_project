<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IncomeVoucher extends Model
{
    use HasFactory, BelongsToSchool, SoftDeletes;

    protected $table = 'income_vouchers';

    protected $fillable = [
        'school_id',
        'voucher_no',
        'income_head_id',
        'amount',
        'income_date',
        'reason',
        'remarks',
        'document_path',
        'approval_status',
        'payment_status',
        'created_by',
    ];

    protected $casts = [
        'amount'      => 'decimal:2',
        'income_date' => 'date',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function incomeHead()
    {
        return $this->belongsTo(IncomeHead::class, 'income_head_id');
    }

    public function receipts()
    {
        return $this->hasMany(VoucherReceipt::class, 'income_voucher_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Accessors for paid and due amounts
    public function getTotalPaidAttribute(): float
    {
        return (float) $this->receipts()->sum('amount');
    }

    public function getTotalDueAttribute(): float
    {
        return max(0.00, (float) $this->amount - $this->total_paid);
    }
}
