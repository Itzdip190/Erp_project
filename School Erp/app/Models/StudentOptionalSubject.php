<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentOptionalSubject extends Model
{
    use HasFactory, BelongsToSchool;

    protected $table = 'student_optional_subjects';

    protected $fillable = [
        'school_id',
        'student_id',
        'subject_id',
        'academic_session_id',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class);
    }
}
