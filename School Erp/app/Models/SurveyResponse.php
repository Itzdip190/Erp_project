<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id',
        'survey_option_id',
        'user_id',
    ];

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function option()
    {
        return $this->belongsTo(SurveyOption::class, 'survey_option_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
