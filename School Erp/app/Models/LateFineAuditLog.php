<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LateFineAuditLog extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'student_id',
        'user_id',
        'installment_no',
        'action',
        'old_fine',
        'new_fine',
        'reason',
    ];

    protected $casts = [
        'old_fine' => 'decimal:2',
        'new_fine' => 'decimal:2',
        'installment_no' => 'integer',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
