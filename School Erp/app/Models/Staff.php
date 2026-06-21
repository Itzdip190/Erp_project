<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Staff extends Model
{
    use HasFactory, SoftDeletes, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'user_id',
        'employee_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'date_of_birth',
        'gender',
        'blood_group',
        'address',
        'city',
        'state',
        'pincode',
        'department_id',
        'designation_id',
        'employment_type',
        'qualification',
        'experience_years',
        'photo',
        'joining_date',
        'basic_salary',
        'bank_account_number',
        'bank_name',
        'ifsc_code',
        'pan_number',
        'is_active',
        'additional_fields',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'joining_date' => 'date',
        'is_active' => 'boolean',
        'basic_salary' => 'decimal:2',
        'experience_years' => 'integer',
        'additional_fields' => 'array',
    ];

    protected $appends = [
        'full_name',
        'photo_url',
    ];

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo) {
            return Storage::disk(config('filesystems.default'))->url($this->photo);
        }
        return asset('images/avatar-staff.png');
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function attendances()
    {
        return $this->hasMany(StaffAttendance::class);
    }

    public function sectionSubjectStaff()
    {
        return $this->hasMany(SectionSubjectStaff::class);
    }
}
