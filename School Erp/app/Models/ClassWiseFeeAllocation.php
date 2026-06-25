<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassWiseFeeAllocation extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'academic_session_id',
        'class_id',
        'section_id',
        'fee_schedule_id',
        'student_category_id',
        'fee_component_id',
        'status',
        'amount',
        'installment_amounts',
    ];

    protected $casts = [
        'status' => 'boolean',
        'amount' => 'decimal:2',
        'installment_amounts' => 'array',
    ];

    public function session()
    {
        return $this->belongsTo(AcademicSession::class, 'academic_session_id');
    }

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function schedule()
    {
        return $this->belongsTo(FeeSchedule::class, 'fee_schedule_id');
    }

    public function category()
    {
        return $this->belongsTo(StudentCategory::class, 'student_category_id');
    }

    public function component()
    {
        return $this->belongsTo(FeeComponent::class, 'fee_component_id');
    }
}
