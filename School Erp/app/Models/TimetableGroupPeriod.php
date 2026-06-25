<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimetableGroupPeriod extends Model
{
    use HasFactory, BelongsToSchool;

    protected $table = 'timetable_group_periods';

    protected $fillable = [
        'school_id',
        'timetable_group_id',
        'period_name',
        'duration_minutes',
        'start_time',
        'end_time',
        'sort_order',
    ];

    public function group()
    {
        return $this->belongsTo(TimetableGroup::class, 'timetable_group_id');
    }

    public function cells()
    {
        return $this->hasMany(ClassTimetableCell::class, 'timetable_group_period_id');
    }
}
