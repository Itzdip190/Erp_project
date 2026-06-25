<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimetableSubstitution extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'date',
        'timetable_id',
        'original_staff_id',
        'substitute_staff_id',
        'status',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function timetable()
    {
        return $this->belongsTo(ClassTimetableCell::class, 'timetable_id');
    }

    public function originalTeacher()
    {
        return $this->belongsTo(Staff::class, 'original_staff_id');
    }

    public function substituteTeacher()
    {
        return $this->belongsTo(Staff::class, 'substitute_staff_id');
    }
}
