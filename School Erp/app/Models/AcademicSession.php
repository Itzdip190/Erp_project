<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicSession extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'name',
        'start_date',
        'end_date',
        'is_current',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
    ];

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function studentSessions()
    {
        return $this->hasMany(StudentSession::class);
    }

    /**
     * Resolve the active academic session.
     * - Teachers, Students, Parents, and Staff are strictly anchored to the current calendar date.
     *   If a session covers today's date and has enrolled students, it is returned.
     *   Otherwise safely falls back to the school's active session.
     * - School Admins and Superadmins use their selected view session or the school's current active session.
     */
    public static function resolveCurrentSessionForUser(?User $user, int $schoolId): ?self
    {
        $isSchoolAdmin = $user && method_exists($user, 'isSchoolAdmin') && $user->isSchoolAdmin();

        // 1. Non-Admin (Teacher, Student, Parent, Staff): Strictly Current Date-Anchored
        if (!$isSchoolAdmin) {
            $today = today()->toDateString();

            // Find session covering today's calendar date
            $calendarSession = self::where('school_id', $schoolId)
                ->whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->first();

            if ($calendarSession) {
                // Safety check: verify students exist in this session to prevent blank screens during transition
                $hasStudents = StudentSession::where('school_id', $schoolId)
                    ->where('academic_session_id', $calendarSession->id)
                    ->exists()
                    || Student::where('school_id', $schoolId)
                        ->where('academic_session_id', $calendarSession->id)
                        ->exists();

                if ($hasStudents) {
                    return $calendarSession;
                }
            }

            // Fallback: active session
            return self::where('school_id', $schoolId)->where('is_current', true)->first()
                ?? self::where('school_id', $schoolId)->latest('id')->first();
        }

        // 2. Admin / Superadmin:
        if (session()->has('admin_selected_session_id')) {
            $sessId = (int) session('admin_selected_session_id');
            $adminSession = self::where('school_id', $schoolId)->find($sessId);
            if ($adminSession) {
                return $adminSession;
            }
        }

        return self::where('school_id', $schoolId)->where('is_current', true)->first()
            ?? self::where('school_id', $schoolId)->latest('id')->first();
    }
}
