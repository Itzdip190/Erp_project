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
     * Create a custom Eloquent Query Builder for AcademicSession to seamlessly scope
     * queries for delegated admins with a restricted session without modifying any page controller code.
     */
    public function newEloquentBuilder($query)
    {
        return new class($query) extends \Illuminate\Database\Eloquent\Builder {
            public function where($column, $operator = null, $value = null, $boolean = 'and')
            {
                // If a restricted admin is querying for 'is_current' = true
                if ($this->isCurrentColumnQuery($column, $operator, $value)) {
                    $restrictedId = $this->getRestrictedId();
                    if ($restrictedId) {
                        return parent::where($this->model->getTable() . '.id', '=', $restrictedId, $boolean);
                    }
                }

                return parent::where($column, $operator, $value, $boolean);
            }

            public function get($columns = ['*'])
            {
                $restrictedId = $this->getRestrictedId();
                if ($restrictedId) {
                    $hasIdWhere = false;
                    foreach ($this->query->wheres as $w) {
                        if (isset($w['column']) && (str_ends_with($w['column'], '.id') || $w['column'] === 'id')) {
                            $hasIdWhere = true;
                            break;
                        }
                    }
                    if (!$hasIdWhere) {
                        $this->where($this->model->getTable() . '.id', '=', $restrictedId);
                    }
                }

                $models = parent::get($columns);
                if ($restrictedId) {
                    foreach ($models as $m) {
                        $m->is_current = true;
                    }
                }
                return $models;
            }

            public function first($columns = ['*'])
            {
                $model = parent::first($columns);
                if ($model && $this->getRestrictedId()) {
                    $model->is_current = true;
                }
                return $model;
            }

            protected function isCurrentColumnQuery($column, $operator, $value): bool
            {
                if (is_string($column) && (str_ends_with($column, 'is_current') || $column === 'is_current')) {
                    if (func_num_args() === 2 && ($operator === true || $operator === 1 || $operator === '1')) {
                        return true;
                    }
                    if (func_num_args() >= 3 && in_array($operator, ['=', '=='], true) && ($value === true || $value === 1 || $value === '1')) {
                        return true;
                    }
                }
                return false;
            }

            protected function getRestrictedId(): ?int
            {
                if (!auth()->check()) {
                    return null;
                }
                $user = auth()->user();
                return method_exists($user, 'getAllowedAcademicSessionId')
                    ? $user->getAllowedAcademicSessionId()
                    : null;
            }
        };
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
        // 0. Restricted Delegated Admin: Strictly bound to their assigned academic year
        if ($user && method_exists($user, 'getAllowedAcademicSessionId')) {
            $restrictedId = $user->getAllowedAcademicSessionId();
            if ($restrictedId) {
                $restrictedSession = self::where('school_id', $schoolId)->find($restrictedId);
                if ($restrictedSession) {
                    return $restrictedSession;
                }
            }
        }

        $isSchoolAdmin = $user && method_exists($user, 'isSchoolAdmin') && $user->isSchoolAdmin();

        // 1. Non-Admin (Teacher, Student, Parent, Staff): Strictly Current Date-Anchored
        if (!$isSchoolAdmin) {
            $today = today()->toDateString();

            return \Illuminate\Support\Facades\Cache::remember("school_{$schoolId}_calendar_session_{$today}", 300, function () use ($schoolId, $today) {
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
            });
        }

        // 2. Admin / Superadmin:
        // The authoritative active academic session is the school's active session in the database (`is_current = true`).
        // Cached for 60s per school and invalidated instantly whenever session is switched.
        $activeSession = \Illuminate\Support\Facades\Cache::remember("school_{$schoolId}_current_academic_session", 60, function () use ($schoolId) {
            return self::where('school_id', $schoolId)->where('is_current', true)->first()
                ?? self::where('school_id', $schoolId)->latest('id')->first();
        });

        if ($activeSession) {
            // Keep browser session synchronized with authoritative active session
            session(['admin_selected_session_id' => $activeSession->id]);
            return $activeSession;
        }

        // Fallback if no session marked current yet
        if (session()->has('admin_selected_session_id')) {
            $sessId = (int) session('admin_selected_session_id');
            $adminSession = self::where('school_id', $schoolId)->find($sessId);
            if ($adminSession) {
                return $adminSession;
            }
        }

        return self::where('school_id', $schoolId)->latest('id')->first();
    }
}
