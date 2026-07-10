<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeInvoice extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'student_id',
        'created_by',
        'invoice_number',
        'related_invoice_id',
        'related_invoice_number',
        'installment_no',
        'type',
        'status',
        'amount',
        'discount_amount',
        'payment_mode',
        'payment_date',
        'payment_details',
        'remarks',
    ];

    protected static function booted()
    {
        // Enforce ledger immutability (except for status column)
        static::updating(function ($invoice) {
            if ($invoice->isDirty('status') && count($invoice->getDirty()) === 1) {
                return;
            }
            throw new \RuntimeException('Fee Invoices are immutable and cannot be updated.');
        });

        static::deleting(function ($invoice) {
            throw new \RuntimeException('Fee Invoices are immutable and cannot be deleted.');
        });
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function relatedInvoice()
    {
        return $this->belongsTo(FeeInvoice::class, 'related_invoice_id');
    }
}
