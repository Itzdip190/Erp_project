<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeReceipt extends Model
{
    use HasFactory, BelongsToSchool;

    protected static function booted()
    {
        static::addGlobalScope('active', function ($builder) {
            $builder->where(function($q) {
                $q->where('status', '!=', 'cancelled')
                  ->orWhereNull('status');
            });
        });
    }

    protected $fillable = [
        'school_id',
        'student_id',
        'receipt_number',
        'amount_paid',
        'discount_amount',
        'discount_type',
        'payment_mode',
        'transaction_id',
        'payment_date',
        'payment_details',
        'status',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
