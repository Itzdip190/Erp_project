<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportCardTemplateMapping extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'report_card_template_id',
        'academic_session_id',
        'class_id',
        'section_id',
    ];

    public function template()
    {
        return $this->belongsTo(ReportCardTemplate::class, 'report_card_template_id');
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class);
    }
}
