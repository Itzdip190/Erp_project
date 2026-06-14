<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class Student extends Model
{
    use HasFactory, SoftDeletes, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'user_id',
        'admission_number',
        'admission_sequence',
        'admission_year',
        'roll_number',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'blood_group',
        'religion',
        'caste',
        'category_id',
        'house_id',
        'photo',
        'guardian_name',
        'guardian_phone',
        'guardian_email',
        'guardian_relationship',
        'address',
        'city',
        'state',
        'pincode',
        'section_id',
        'class_id',
        'academic_session_id',
        'admission_date',
        'is_active',
        'opening_due_balance',
        'custom_fields',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'admission_date' => 'date',
        'is_active' => 'boolean',
        'opening_due_balance' => 'decimal:2',
        'custom_fields' => 'array',
        'admission_sequence' => 'integer',
        'admission_year' => 'integer',
    ];

    protected $appends = [
        'full_name',
        'photo_url',
        'age',
    ];

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo) {
            // Returns S3 or local url depending on default filesystem disk configured
            return Storage::disk(config('filesystems.default'))->url($this->photo);
        }
        return asset('images/avatar-student.png');
    }

    public function getAgeAttribute(): int
    {
        return Carbon::parse($this->date_of_birth)->age;
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class, 'academic_session_id');
    }

    public function category()
    {
        return $this->belongsTo(StudentCategory::class, 'category_id');
    }

    public function house()
    {
        return $this->belongsTo(StudentHouse::class, 'house_id');
    }

    public function documents()
    {
        return $this->hasMany(StudentDocument::class);
    }

    public function studentSessions()
    {
        return $this->hasMany(StudentSession::class);
    }

    public function attendances()
    {
        return $this->hasMany(StudentAttendance::class);
    }
}

