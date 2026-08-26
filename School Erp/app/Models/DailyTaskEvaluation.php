<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyTaskEvaluation extends Model
{
    use HasFactory, BelongsToSchool;

    protected $table = 'daily_task_evaluations';

    protected $fillable = [
        'school_id',
        'daily_task_review_id',
        'daily_task_question_id',
        'student_id',
        'date',
        'rating',
        'score',
        'status_option',
        'remarks',
    ];

    protected $casts = [
        'date' => 'date',
        'rating' => 'integer',
        'score' => 'decimal:2',
    ];

    public function review()
    {
        return $this->belongsTo(DailyTaskReview::class, 'daily_task_review_id');
    }

    public function question()
    {
        return $this->belongsTo(DailyTaskQuestion::class, 'daily_task_question_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
