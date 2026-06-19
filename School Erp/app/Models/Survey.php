<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'question',
        'is_active',
    ];

    public function options()
    {
        return $this->hasMany(SurveyOption::class);
    }

    public function responses()
    {
        return $this->hasMany(SurveyResponse::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
