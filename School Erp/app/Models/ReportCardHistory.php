<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportCardHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'academic_session_id',
        'class_id',
        'section_id',
        'exam_name',
        'title',
        'layout_style',
        'template_id',
        'template_name',
        'student_count',
        'is_sent_to_students',
        'sent_at',
        'passed_result_text',
        'failed_result_text',
        'passed_promoted_text',
        'failed_promoted_text',
        'positions_show_till',
        'failed_position_text',
        'absent_position_text',
        'medical_position_text',
    ];

    protected $casts = [
        'is_sent_to_students' => 'boolean',
        'sent_at' => 'datetime',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class);
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

    public function students()
    {
        return $this->hasMany(ReportCardHistoryStudent::class, 'report_card_history_id');
    }
}
