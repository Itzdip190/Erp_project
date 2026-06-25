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
        'first_name_local',
        'last_name_local',
        'email',
        'phone',
        'date_of_birth',
        'gender',
        'place_of_birth',
        'birth_certificate_no',
        'usn_srn_number',
        'blood_group',
        'religion',
        'caste',
        'sub_caste',
        'family_id',
        'category_id',
        'group',
        'house_id',
        'house_role',
        'photo',
        'biometric_id',
        'pen_number',
        'apaar_id',
        'samagra_id',
        'class_at_admission',
        'enrollment_number',
        'tc_number',
        'transport_month',
        'transport_route',
        'transport_vehicle_code',
        'transport_stop',
        'transport_drop_vehicle_code',
        'prev_school',
        'prev_city_country',
        'prev_year_attended',
        'prev_board',
        'prev_reg_no',
        'prev_pcm_marks',
        'prev_pcm_percentage',
        'prev_total_marks',
        'prev_average',
        'entrance_exam_name',
        'entrance_exam_rank',
        'entrance_exam_remarks',
        'disciplinary_action',
        'disciplinary_action_reason',
        'asked_to_leave',
        'asked_to_leave_reason',
        'special_needs',
        'special_needs_reason',
        'interests_talents',
        'interests_talents_reason',
        'represented_school',
        'represented_school_reason',
        'other_info',
        'other_info_reason',
        'father_name',
        'father_phone',
        'father_alternate_phone',
        'father_email',
        'father_occupation',
        'father_id',
        'father_aadhar',
        'father_income',
        'father_qualification',
        'father_passport',
        'father_address',
        'father_photo',
        'mother_name',
        'mother_phone',
        'mother_alternate_phone',
        'mother_email',
        'mother_occupation',
        'mother_id',
        'mother_aadhar',
        'mother_income',
        'mother_qualification',
        'mother_passport',
        'mother_address',
        'mother_office_address',
        'mother_photo',
        'guardian_name',
        'guardian_phone',
        'guardian_email',
        'guardian_relationship',
        'guardian_occupation',
        'guardian_photo',
        'guardian_passport',
        'guardian_name_local',
        'guardian_address',
        'whatsapp_number',
        'address',
        'address_line_2',
        'city',
        'state',
        'country',
        'pincode',
        'region',
        'permanent_address',
        'permanent_address_line_2',
        'permanent_city',
        'permanent_state',
        'permanent_country',
        'permanent_pincode',
        'permanent_region',
        'section_id',
        'class_id',
        'academic_session_id',
        'admission_date',
        'is_active',
        'opening_due_balance',
        'national_id',
        'local_id',
        'bank_account_no',
        'bank_account_holder',
        'bank_name',
        'bank_branch',
        'ifsc_code',
        'bank_micr',
        'note',
        'emergency_address',
        'contact_priority',
        'medical_height',
        'medical_weight',
        'medical_vision_left',
        'medical_vision_right',
        'medical_dental',
        'medical_illness',
        'medical_history',
        'medical_allergies',
        'medical_disabilities',
        'medical_doctor_name',
        'medical_doctor_phone',
        'medical_doctor_address',
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
        'disciplinary_action' => 'boolean',
        'asked_to_leave' => 'boolean',
        'special_needs' => 'boolean',
        'interests_talents' => 'boolean',
        'represented_school' => 'boolean',
        'other_info' => 'boolean',
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
            return Storage::disk('public')->url($this->photo);
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

    public function optionalSubjects()
    {
        return $this->belongsToMany(Subject::class, 'student_optional_subjects', 'student_id', 'subject_id')
            ->withPivot('academic_session_id')
            ->withTimestamps();
    }
}

