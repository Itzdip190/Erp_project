<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyTaskReview extends Model
{
    use HasFactory, BelongsToSchool;

    protected $table = 'daily_task_reviews';

    protected $fillable = [
        'school_id',
        'academic_session_id',
        'date',
        'class_id',
        'section_id',
        'subject_id',
        'teacher_id',
        'review_type', // 'class_teacher' or 'subject_teacher'
        'overall_remarks',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Staff::class, 'teacher_id');
    }

    public function evaluations()
    {
        return $this->hasMany(DailyTaskEvaluation::class, 'daily_task_review_id');
    }
}
