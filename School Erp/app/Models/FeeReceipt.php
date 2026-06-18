<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeReceipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'student_id',
        'receipt_number',
        'amount_paid',
        'payment_mode',
        'transaction_id',
        'payment_date',
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
