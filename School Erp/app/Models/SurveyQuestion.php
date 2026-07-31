<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyQuestion extends Model
{
    use HasFactory;

    protected $table = 'survey_questions';

    protected $fillable = [
        'survey_id',
        'question_text',
        'sort_order',
    ];

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function options()
    {
        return $this->hasMany(SurveyOption::class, 'survey_question_id');
    }

    public function responses()
    {
        return $this->hasMany(SurveyResponse::class, 'survey_question_id');
    }
}
