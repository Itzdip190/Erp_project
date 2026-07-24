<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportCardHistoryStudent extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_card_history_id',
        'school_id',
        'student_id',
        'class_id',
        'section_id',
        'exam_name',
        'template_id',
        'total_marks',
        'percentage',
        'grade',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'percentage' => 'float',
    ];

    public function history()
    {
        return $this->belongsTo(ReportCardHistory::class, 'report_card_history_id');
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function template()
    {
        return $this->belongsTo(ReportCardTemplate::class, 'template_id');
    }
}
