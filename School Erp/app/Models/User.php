<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasApiTokens, BelongsToSchool;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'photo',
        'password',
        'school_id',
        'is_active',
        'must_change_password',
        'last_login_at',
        'last_password_reset_at',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'must_change_password' => 'boolean',
            'last_login_at' => 'datetime',
            'last_password_reset_at' => 'datetime',
        ];
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function staff()
    {
        return $this->hasOne(Staff::class);
    }

    public function student()
    {
        return $this->hasOne(Student::class, 'user_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'user_id');
    }

    /**
     * Get associated student record for display (either direct student account or child student for parent account).
     */
    public function getLinkedStudentAttribute(): ?Student
    {
        if ($this->relationLoaded('student') && $this->student) {
            return $this->student;
        }
        if ($this->relationLoaded('students') && $this->students->isNotEmpty()) {
            return $this->students->sortByDesc('academic_session_id')->sortByDesc('id')->first();
        }
        if ($this->relationLoaded('student') && $this->relationLoaded('students')) {
            if (in_array($this->role, ['teacher', 'staff', 'admin', 'school_admin', 'superadmin'], true)) {
                return null;
            }
        }
        if ($this->role === 'student' || $this->hasRole('student')) {
            $st = Student::where('school_id', $this->school_id)
                ->where('user_id', $this->id)
                ->orderByDesc('academic_session_id')
                ->orderByDesc('id')
                ->first();
            if ($st) {
                return $st;
            }
        }

        $schoolId  = $this->school_id;
        $userEmail = strtolower(trim($this->email ?? ''));
        $userPhone = trim($this->phone ?? '');
        $userName  = strtolower(trim($this->name ?? ''));

        if (!$schoolId || ($userEmail === '' && $userPhone === '' && $userName === '')) {
            return null;
        }

        $query = Student::where('school_id', $schoolId)
            ->orderByDesc('academic_session_id')
            ->orderByDesc('id');

        return $query->where(function ($sq) use ($userEmail, $userPhone, $userName) {
            $hasCondition = false;
            if ($userEmail !== '') {
                $sq->whereRaw("LOWER(father_email) = ?", [$userEmail])
                   ->orWhereRaw("LOWER(mother_email) = ?", [$userEmail])
                   ->orWhereRaw("LOWER(guardian_email) = ?", [$userEmail]);
                $hasCondition = true;
            }
            if ($userPhone !== '') {
                if ($hasCondition) {
                    $sq->orWhere('father_phone', $userPhone)
                       ->orWhere('mother_phone', $userPhone)
                       ->orWhere('guardian_phone', $userPhone);
                } else {
                    $sq->where('father_phone', $userPhone)
                       ->orWhere('mother_phone', $userPhone)
                       ->orWhere('guardian_phone', $userPhone);
                    $hasCondition = true;
                }
            }
            if ($userName !== '') {
                if ($hasCondition) {
                    $sq->orWhereRaw("LOWER(father_name) = ?", [$userName])
                       ->orWhereRaw("LOWER(mother_name) = ?", [$userName])
                       ->orWhereRaw("LOWER(guardian_name) = ?", [$userName]);
                } else {
                    $sq->whereRaw("LOWER(father_name) = ?", [$userName])
                       ->orWhereRaw("LOWER(mother_name) = ?", [$userName])
                       ->orWhereRaw("LOWER(guardian_name) = ?", [$userName]);
                }
            }
        })->with(['schoolClass', 'section'])->first();
    }

    /**
     * Get profile photo URL for the user (checking direct user photo and associated staff photo).
     */
    public function getPhotoUrlAttribute(): ?string
    {
        $photo = $this->photo ?: $this->staff?->photo;
        if (empty($photo)) {
            return null;
        }
        if (str_starts_with($photo, 'http://') || str_starts_with($photo, 'https://') || str_starts_with($photo, 'data:image')) {
            return $photo;
        }
        $cleanPhoto = ltrim($photo, '/');
        if (str_starts_with($cleanPhoto, 'uploads/') || str_starts_with($cleanPhoto, 'storage/')) {
            return asset($cleanPhoto);
        }
        if (file_exists(public_path('uploads/' . $cleanPhoto))) {
            return asset('uploads/' . $cleanPhoto);
        }
        if (file_exists(public_path($cleanPhoto))) {
            return asset($cleanPhoto);
        }
        return \Illuminate\Support\Facades\Storage::disk('public')->url($cleanPhoto);
    }

    /**
     * Determine dynamic relation label (Father / Mother / Guardian) for a parent user.
     */
    public function getParentRelationLabel(): string
    {
        $userEmail = strtolower(trim($this->email ?? ''));
        $userPhone = trim($this->phone ?? '');
        $userName  = strtolower(trim($this->name ?? ''));

        // 1. Check in-memory eager loaded students first without querying database
        $students = null;
        if ($this->relationLoaded('students') && $this->students->isNotEmpty()) {
            $students = $this->students;
        } elseif ($this->relationLoaded('student') && $this->student) {
            $students = collect([$this->student]);
        }

        if ($students) {
            foreach ($students as $student) {
                $fatherEmail = strtolower(trim($student->father_email ?? ''));
                $fatherPhone = trim($student->father_phone ?? '');
                $fatherName  = strtolower(trim($student->father_name ?? ''));

                if (($userEmail !== '' && $userEmail === $fatherEmail) ||
                    ($userPhone !== '' && $userPhone === $fatherPhone) ||
                    ($userName !== '' && $fatherName !== '' && $userName === $fatherName)) {
                    return 'Father';
                }

                $motherEmail = strtolower(trim($student->mother_email ?? ''));
                $motherPhone = trim($student->mother_phone ?? '');
                $motherName  = strtolower(trim($student->mother_name ?? ''));

                if (($userEmail !== '' && $userEmail === $motherEmail) ||
                    ($userPhone !== '' && $userPhone === $motherPhone) ||
                    ($userName !== '' && $motherName !== '' && $userName === $motherName)) {
                    return 'Mother';
                }

                $guardianEmail = strtolower(trim($student->guardian_email ?? ''));
                $guardianPhone = trim($student->guardian_phone ?? '');
                $guardianName  = strtolower(trim($student->guardian_name ?? ''));

                if (($userEmail !== '' && $userEmail === $guardianEmail) ||
                    ($userPhone !== '' && $userPhone === $guardianPhone) ||
                    ($userName !== '' && $guardianName !== '' && $userName === $guardianName)) {
                    $rel = strtolower(trim($student->guardian_relationship ?? ''));
                    if ($rel === 'father') {
                        return 'Father';
                    }
                    if ($rel === 'mother') {
                        return 'Mother';
                    }
                    return 'Guardian';
                }
            }
            return 'Parent';
        }

        $schoolId  = $this->school_id;
        $query = Student::withoutGlobalScope(\App\Models\Scopes\SchoolScope::class);
        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }

        $students = $query->where(function ($sq) use ($userEmail, $userPhone, $userName) {
            $hasCondition = false;
            if ($userEmail !== '') {
                $sq->whereRaw("LOWER(father_email) = ?", [$userEmail])
                   ->orWhereRaw("LOWER(mother_email) = ?", [$userEmail])
                   ->orWhereRaw("LOWER(guardian_email) = ?", [$userEmail]);
                $hasCondition = true;
            }
            if ($userPhone !== '') {
                if ($hasCondition) {
                    $sq->orWhere('father_phone', $userPhone)
                       ->orWhere('mother_phone', $userPhone)
                       ->orWhere('guardian_phone', $userPhone);
                } else {
                    $sq->where('father_phone', $userPhone)
                       ->orWhere('mother_phone', $userPhone)
                       ->orWhere('guardian_phone', $userPhone);
                    $hasCondition = true;
                }
            }
            if ($userName !== '') {
                if ($hasCondition) {
                    $sq->orWhereRaw("LOWER(father_name) = ?", [$userName])
                       ->orWhereRaw("LOWER(mother_name) = ?", [$userName])
                       ->orWhereRaw("LOWER(guardian_name) = ?", [$userName]);
                } else {
                    $sq->whereRaw("LOWER(father_name) = ?", [$userName])
                       ->orWhereRaw("LOWER(mother_name) = ?", [$userName])
                       ->orWhereRaw("LOWER(guardian_name) = ?", [$userName]);
                }
            }
        })->get();

        foreach ($students as $student) {
            $fatherEmail = strtolower(trim($student->father_email ?? ''));
            $fatherPhone = trim($student->father_phone ?? '');
            $fatherName  = strtolower(trim($student->father_name ?? ''));

            if (($userEmail !== '' && $userEmail === $fatherEmail) ||
                ($userPhone !== '' && $userPhone === $fatherPhone) ||
                ($userName !== '' && $fatherName !== '' && $userName === $fatherName)) {
                return 'Father';
            }

            $motherEmail = strtolower(trim($student->mother_email ?? ''));
            $motherPhone = trim($student->mother_phone ?? '');
            $motherName  = strtolower(trim($student->mother_name ?? ''));

            if (($userEmail !== '' && $userEmail === $motherEmail) ||
                ($userPhone !== '' && $userPhone === $motherPhone) ||
                ($userName !== '' && $motherName !== '' && $userName === $motherName)) {
                return 'Mother';
            }

            $guardianEmail = strtolower(trim($student->guardian_email ?? ''));
            $guardianPhone = trim($student->guardian_phone ?? '');
            $guardianName  = strtolower(trim($student->guardian_name ?? ''));

            if (($userEmail !== '' && $userEmail === $guardianEmail) ||
                ($userPhone !== '' && $userPhone === $guardianPhone) ||
                ($userName !== '' && $guardianName !== '' && $userName === $guardianName)) {
                $rel = strtolower(trim($student->guardian_relationship ?? ''));
                if ($rel === 'father') {
                    return 'Father';
                }
                if ($rel === 'mother') {
                    return 'Mother';
                }
                return 'Guardian';
            }
        }

        return 'Parent';
    }

    /**
     * Get human-readable display role for the user (Student, Father, Mother, Guardian, Teacher, Staff, Admin, etc.)
     */
    public function getDisplayRoleAttribute(): string
    {
        // 1. Admin roles
        if ($this->hasRole('superadmin') || $this->role === 'superadmin') {
            return 'Super Admin';
        }
        if ($this->hasRole('school_admin') || $this->hasRole('admin') || in_array($this->role, ['admin', 'school_admin'])) {
            return 'Admin';
        }

        // 2. Staff / Teacher roles
        if ($this->hasRole('teacher') || $this->role === 'teacher') {
            return 'Teacher';
        }
        if ($this->hasRole('staff') || $this->role === 'staff') {
            return 'Staff';
        }

        // 3. Parent role check
        $isParent = $this->role === 'parent' || $this->hasRole('parent') || str_contains(strtolower($this->email ?? ''), '@parent.');

        if (!$isParent && ($this->role !== 'student' && !$this->hasRole('student'))) {
            // Check in-memory relationships first if already loaded
            if ($this->relationLoaded('students') && $this->students->isNotEmpty()) {
                $isParent = true;
            } elseif (!$this->relationLoaded('student') && !$this->relationLoaded('students')) {
                // Check if matched as parent on any student record
                $schoolId  = $this->school_id;
                $userEmail = strtolower(trim($this->email ?? ''));
                $userPhone = trim($this->phone ?? '');
                $userName  = strtolower(trim($this->name ?? ''));

                if ($userEmail !== '' || $userPhone !== '' || $userName !== '') {
                    $query = Student::withoutGlobalScope(\App\Models\Scopes\SchoolScope::class);
                    if ($schoolId) {
                        $query->where('school_id', $schoolId);
                    }
                    $parentMatchExists = $query->where(function ($sq) use ($userEmail, $userPhone, $userName) {
                        if ($userEmail !== '') {
                            $sq->whereRaw("LOWER(father_email) = ?", [$userEmail])
                               ->orWhereRaw("LOWER(mother_email) = ?", [$userEmail])
                               ->orWhereRaw("LOWER(guardian_email) = ?", [$userEmail]);
                        }
                        if ($userPhone !== '') {
                            $sq->orWhere('father_phone', $userPhone)
                               ->orWhere('mother_phone', $userPhone)
                               ->orWhere('guardian_phone', $userPhone);
                        }
                        if ($userName !== '') {
                            $sq->orWhereRaw("LOWER(father_name) = ?", [$userName])
                               ->orWhereRaw("LOWER(mother_name) = ?", [$userName])
                               ->orWhereRaw("LOWER(guardian_name) = ?", [$userName]);
                        }
                    })->exists();

                    if ($parentMatchExists) {
                        $isParent = true;
                    }
                }
            }
        }

        if ($isParent) {
            return $this->getParentRelationLabel();
        }

        // 4. Student role check
        $isStudent = $this->role === 'student' 
            || ($this->relationLoaded('roles') ? $this->hasRole('student') : $this->hasRole('student'))
            || ($this->relationLoaded('student') ? (bool)$this->student : $this->student()->exists());

        if ($isStudent) {
            return 'Student';
        }

        $spatieRole = $this->roles->first()?->name;
        if ($spatieRole) {
            return ucfirst(str_replace('_', ' ', $spatieRole));
        }

        return ucfirst(str_replace('_', ' ', $this->role ?? 'User'));
    }

    /**
     * Check if the user is a School Admin or SuperAdmin with academic session management rights.
     */
    public function isSchoolAdmin(): bool
    {
        if ($this->hasRole('school_admin') || $this->hasRole('superadmin') || $this->hasRole('admin')) {
            return true;
        }
        $role = strtolower(trim((string)($this->role ?? '')));
        return in_array($role, ['school_admin', 'superadmin', 'admin'], true);
    }

    /**
     * Get the restricted academic session ID if assigned via a School Admin designation.
     * Returns null if user is SuperAdmin or Main School Owner (unrestricted across all years).
     */
    public function getAllowedAcademicSessionId(): ?int
    {
        if ($this->hasRole('superadmin') || $this->role === 'superadmin') {
            return null;
        }

        $staff = $this->relationLoaded('staff') ? $this->staff : $this->staff()->first();
        if (!$staff) {
            return null;
        }

        $designationIds = [];
        if ($staff->designation_id) {
            $designationIds[] = $staff->designation_id;
        }
        try {
            $pivotIds = $staff->designations()->pluck('designations.id')->toArray();
            $designationIds = array_unique(array_merge($designationIds, $pivotIds));
        } catch (\Throwable $e) {
            // designation_staff pivot fallback
        }

        if (empty($designationIds)) {
            return null;
        }

        try {
            $adminDesignations = Designation::whereIn('id', $designationIds)
                ->where('system_role', 'school_admin')
                ->whereNotNull('academic_session_id')
                ->get();

            foreach ($adminDesignations as $desg) {
                if (!empty($desg->academic_session_id)) {
                    return (int) $desg->academic_session_id;
                }
            }
        } catch (\Throwable $e) {
            // Column may not exist yet if migration pending
            return null;
        }

        return null;
    }

    /**
     * Check if this user is a delegated admin with a locked academic session.
     */
    public function hasRestrictedAcademicSession(): bool
    {
        return $this->getAllowedAcademicSessionId() !== null;
    }
}



