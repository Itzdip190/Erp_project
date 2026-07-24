<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class ReportCardTemplate extends Model
{
    use HasFactory, BelongsToSchool, SoftDeletes;

    protected $fillable = [
        'school_id',
        'academic_session_id',
        'name',
        'content',
        'sample_template_key',
        'background_image',
        'design_settings',
        'is_active',
    ];

    protected $casts = [
        'design_settings' => 'array',
        'is_active' => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function mappings()
    {
        return $this->hasMany(ReportCardTemplateMapping::class, 'report_card_template_id');
    }
}
