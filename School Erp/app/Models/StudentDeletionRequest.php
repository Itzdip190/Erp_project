<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class StudentDeletionRequest extends Model
{
    use HasFactory, BelongsToSchool;

    protected $table = 'student_deletion_requests';

    protected $fillable = [
        'school_id',
        'student_id',
        'admission_number',
        'student_name',
        'class_name',
        'section_name',
        'reason',
        'requested_by',
        'requested_by_name',
        'requested_at',
        'approved_by',
        'approved_by_name',
        'approved_at',
        'rejected_by',
        'rejected_by_name',
        'rejected_at',
        'rejection_reason',
        'status',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'approved_at'  => 'datetime',
        'rejected_at'  => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id')->withTrashed();
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejecter()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }
}
