<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DigitalDiary extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'class_id',
        'section_id',
        'staff_id',
        'title',
        'content',
        'diary_date',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }
}
