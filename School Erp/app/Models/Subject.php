<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'class_id',
        'name',
        'code',
        'type',
        'max_marks',
        'pass_marks',
    ];

    protected $casts = [
        'max_marks' => 'integer',
        'pass_marks' => 'integer',
    ];

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function sectionSubjectStaff()
    {
        return $this->hasMany(SectionSubjectStaff::class);
    }
}
