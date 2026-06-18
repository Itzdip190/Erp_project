<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingCheque extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'student_id',
        'bank_name',
        'cheque_number',
        'amount',
        'cheque_date',
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
