<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyTaskQuestion extends Model
{
    use HasFactory, BelongsToSchool;

    protected $table = 'daily_task_questions';

    protected $fillable = [
        'school_id',
        'daily_task_head_id',
        'class_id',
        'section_id',
        'subject_id',
        'target_role', // 'both', 'class_teacher', 'subject_teacher'
        'question',
        'evaluation_type', // 'rating', 'score', 'options', 'boolean', 'remark'
        'max_score',
        'is_mandatory',
        'sort_order',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_mandatory' => 'boolean',
        'max_score' => 'integer',
        'sort_order' => 'integer',
    ];

    public function head()
    {
        return $this->belongsTo(DailyTaskHead::class, 'daily_task_head_id');
    }

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

    public function evaluations()
    {
        return $this->hasMany(DailyTaskEvaluation::class, 'daily_task_question_id');
    }
}
