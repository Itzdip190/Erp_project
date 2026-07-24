<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentLeaveApplication extends Model
{
    use HasFactory, SoftDeletes, BelongsToSchool;

    protected static function booted()
    {
        static::saved(function ($application) {
            \App\Services\LeaveAttendanceService::syncStudentLeaveAttendance($application);
        });

        static::deleted(function ($application) {
            $application->status = 'deleted';
            \App\Services\LeaveAttendanceService::syncStudentLeaveAttendance($application);
        });
    }

    protected $table = 'student_leave_applications';

    protected $fillable = [
        'school_id',
        'academic_year',
        'student_id',
        'user_id',
        'class_id',
        'section_id',
        'leave_type',
        'title',
        'reason',
        'from_date',
        'to_date',
        'total_days',
        'attachment_path',
        'declaration_id',
        'declaration_accepted',
        'status',
        'admin_remarks',
        'action_by',
        'action_at',
    ];

    protected $casts = [
        'from_date'            => 'date',
        'to_date'              => 'date',
        'declaration_accepted' => 'boolean',
        'action_at'            => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function declaration()
    {
        return $this->belongsTo(StudentLeaveDeclaration::class, 'declaration_id');
    }

    public function actionBy()
    {
        return $this->belongsTo(User::class, 'action_by');
    }
}
