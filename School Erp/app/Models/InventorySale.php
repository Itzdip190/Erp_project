<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventorySale extends Model
{
    use HasFactory, BelongsToSchool;

    protected $table = 'inventory_sales';

    protected $fillable = [
        'school_id',
        'invoice_number',
        'receipt_number',
        'student_id',
        'admission_no',
        'customer_name',
        'customer_address',
        'customer_mobile',
        'payment_mode',
        'reference_no',
        'total_mrp',
        'sub_total',
        'total_tax',
        'total_discount',
        'grand_total',
        'paid_amount',
        'due_amount',
        'status',
        'sale_date',
        'created_by',
        'remarks',
    ];

    protected $casts = [
        'total_mrp' => 'decimal:2',
        'sub_total' => 'decimal:2',
        'total_tax' => 'decimal:2',
        'total_discount' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
        'sale_date' => 'datetime',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function items()
    {
        return $this->hasMany(InventorySaleItem::class, 'sale_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get payment mode formatted label
     */
    public function getPaymentModeLabelAttribute(): string
    {
        return match (strtolower($this->payment_mode ?? 'cash')) {
            'cash' => 'Cash',
            'upi', 'online' => 'UPI / Online',
            'card' => 'Debit/Credit Card',
            'cheque' => 'Cheque',
            'dd', 'demand_draft' => 'Demand Draft',
            'bank_transfer' => 'Bank Transfer',
            default => ucfirst($this->payment_mode ?? 'Cash'),
        };
    }
}
