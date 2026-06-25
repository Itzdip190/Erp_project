<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassTimetableCell extends Model
{
    use HasFactory, BelongsToSchool;

    protected $table = 'class_timetable_cells';

    protected $fillable = [
        'school_id',
        'timetable_group_id',
        'class_id',
        'section_id',
        'timetable_group_period_id',
        'day_of_week',
        'subject_id',
        'teacher_id',
        'mode',
    ];

    public function group()
    {
        return $this->belongsTo(TimetableGroup::class, 'timetable_group_id');
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function period()
    {
        return $this->belongsTo(TimetableGroupPeriod::class, 'timetable_group_period_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Staff::class, 'teacher_id');
    }

    public function getStartTimeAttribute()
    {
        return $this->period ? date('g:i A', strtotime($this->period->start_time)) : null;
    }

    public function getEndTimeAttribute()
    {
        return $this->period ? date('g:i A', strtotime($this->period->end_time)) : null;
    }
}
