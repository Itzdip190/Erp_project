<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamAssessment extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'exam_id',
        'class_id',
        'subject_id',
        'assessment_type',
        'name',
        'objective',
        'eval_type',
        'max_marks',
        'weightage_percentage',
        'display_order',
        'assessment_date',
        'pass_marks',
        'overall_passing_marks',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function subAssessments()
    {
        return $this->hasMany(ExamSubAssessment::class, 'exam_assessment_id')->orderBy('display_order')->orderBy('id');
    }
}
