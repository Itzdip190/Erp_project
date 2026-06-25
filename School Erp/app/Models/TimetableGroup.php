<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimetableGroup extends Model
{
    use HasFactory, BelongsToSchool;

    protected $table = 'timetable_groups';

    protected $fillable = [
        'school_id',
        'group_name',
        'start_date',
        'end_date',
        'academic_year',
        'class_start_time',
        'number_of_periods',
        'applicable_days',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'applicable_days' => 'array',
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    protected $appends = ['class_section_ids'];

    public function getClassSectionIdsAttribute()
    {
        return \DB::table('timetable_group_class_section')
            ->where('timetable_group_id', $this->id)
            ->selectRaw("CONCAT(class_id, '-', section_id) as class_section_id")
            ->pluck('class_section_id')
            ->toArray();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function periods()
    {
        return $this->hasMany(TimetableGroupPeriod::class, 'timetable_group_id')->orderBy('sort_order');
    }

    public function classes()
    {
        return $this->belongsToMany(SchoolClass::class, 'timetable_group_class_section', 'timetable_group_id', 'class_id');
    }

    public function sections()
    {
        return $this->belongsToMany(Section::class, 'timetable_group_class_section', 'timetable_group_id', 'section_id');
    }
}
