<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectionSubjectStaff extends Model
{
    use HasFactory, BelongsToSchool;

    protected $table = 'section_subject_staff';

    protected $fillable = [
        'school_id',
        'section_id',
        'subject_id',
        'staff_id',
        'substitute_staff_id',
        'academic_session_id',
    ];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function substituteStaff()
    {
        return $this->belongsTo(Staff::class, 'substitute_staff_id');
    }

    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class);
    }
}
