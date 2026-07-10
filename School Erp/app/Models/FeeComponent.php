<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeComponent extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'academic_session_id',
        'fee_schedule_id',
        'head_name',
        'component_name',
        'admission_type',
        'gender',
    ];

    public function session()
    {
        return $this->belongsTo(AcademicSession::class, 'academic_session_id');
    }

    public function schedule()
    {
        return $this->belongsTo(FeeSchedule::class, 'fee_schedule_id');
    }
}
