<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamSubAssessment extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'exam_assessment_id',
        'name',
        'max_marks',
        'pass_marks',
        'weightage_percentage',
        'display_order',
    ];

    public function assessment()
    {
        return $this->belongsTo(ExamAssessment::class, 'exam_assessment_id');
    }
}
