<?php

namespace App\Services;

use App\Models\FeeSchedule;
use App\Models\TransportFeeSchedule;
use App\Models\StudentFee;
use App\Models\Student;

class FeeHelper
{
    /**
     * Cache for loaded schedules and resolved installment names during the request lifecycle.
     */
    protected static array $scheduleCache = [];

    /**
     * Cache for session-scoped enrolled students and promoted eligibility to avoid thousands of N+1 queries.
     */
    protected static array $requestSessionCache = [];

    /**
     * Track sessions that have already been purged in the current request lifecycle.
     */
    protected static array $purgedSessions = [];

    /**
     * Resolve academic class order naturally (Pre-Nursery, Nursery, LKG, UKG, 1, 2, ..., 12).
     */
    public static function getAcademicClassOrder(?string $className, $sortOrder = null): int
    {
        if (is_numeric($sortOrder) && (int)$sortOrder > 0) {
            return (int)$sortOrder;
        }

        $name = strtolower(trim((string)$className));
        if (str_contains($name, 'play') || str_contains($name, 'pre-nursery') || str_contains($name, 'pre nursery')) return 1;
        if (str_contains($name, 'nursery') || str_contains($name, 'nur')) return 2;
        if (str_contains($name, 'lkg') || str_contains($name, 'l.k.g') || str_contains($name, 'kg1') || str_contains($name, 'kg 1') || str_contains($name, 'lower')) return 3;
        if (str_contains($name, 'ukg') || str_contains($name, 'u.k.g') || str_contains($name, 'kg2') || str_contains($name, 'kg 2') || str_contains($name, 'upper')) return 4;

        if (preg_match('/\b(12|xii)\b/i', $name)) return 160;
        if (preg_match('/\b(11|xi)\b/i', $name)) return 150;
        if (preg_match('/\b(10|x)\b/i', $name)) return 140;
        if (preg_match('/\b(9|ix)\b/i', $name)) return 130;
        if (preg_match('/\b(8|viii)\b/i', $name)) return 120;
        if (preg_match('/\b(7|vii)\b/i', $name)) return 110;
        if (preg_match('/\b(6|vi)\b/i', $name)) return 100;
        if (preg_match('/\b(5|v)\b/i', $name)) return 90;
        if (preg_match('/\b(4|iv)\b/i', $name)) return 80;
        if (preg_match('/\b(3|iii)\b/i', $name)) return 70;
        if (preg_match('/\b(2|ii)\b/i', $name)) return 60;
        if (preg_match('/\b(1|i)\b/i', $name)) return 50;

        if (preg_match('/\d+/', $name, $matches)) {
            return 50 + (int)$matches[0];
        }

        return 999;
    }

    /**
     * Resolve the dynamic installment name given a StudentFee instance, installment number, or Student model.
     */
    public static function getInstallmentName($studentFeeOrInstNo = null, ?int $instNo = null, ?Student $student = null): string
    {
        $sf = null;
        if ($studentFeeOrInstNo instanceof StudentFee) {
            $sf = $studentFeeOrInstNo;
            $instNo = $instNo ?: ($sf->installment_no ?? 1);
        } elseif (is_numeric($studentFeeOrInstNo)) {
            $instNo = (int)$studentFeeOrInstNo;
        }

        $instNo = $instNo ?: 1;

        if ($sf) {
            // Direct relationship check
            if ($sf->relationLoaded('feeSchedule') && $sf->feeSchedule) {
                return $sf->feeSchedule->getInstallmentName($instNo);
            }
            if ($sf->relationLoaded('transportFeeSchedule') && $sf->transportFeeSchedule) {
                return $sf->transportFeeSchedule->getInstallmentName($instNo);
            }
            if ($sf->fee_schedule_id) {
                $sched = static::$scheduleCache['fs_' . $sf->fee_schedule_id] ??= FeeSchedule::find($sf->fee_schedule_id);
                if ($sched) {
                    return $sched->getInstallmentName($instNo);
                }
            }
            if ($sf->transport_fee_schedule_id) {
                $sched = static::$scheduleCache['tfs_' . $sf->transport_fee_schedule_id] ??= TransportFeeSchedule::find($sf->transport_fee_schedule_id);
                if ($sched) {
                    return $sched->getInstallmentName($instNo);
                }
            }
            if ($sf->student) {
                $student = $sf->student;
            }
        }

        if ($student) {
            return static::getInstallmentNameForStudent($student, $instNo);
        }

        return 'Installment ' . $instNo;
    }

    /**
     * Resolve dynamic installment name for a student based on their class and school schedule.
     */
    public static function getInstallmentNameForStudent(Student $student, int $instNo): string
    {
        $cacheKey = "student_{$student->id}_inst_{$instNo}";
        if (isset(static::$scheduleCache[$cacheKey])) {
            return static::$scheduleCache[$cacheKey];
        }

        if ($student->fee_schedule_id) {
            $sched = static::$scheduleCache['fs_' . $student->fee_schedule_id] ??= FeeSchedule::find($student->fee_schedule_id);
            if ($sched) {
                $name = $sched->getInstallmentName($instNo);
                static::$scheduleCache[$cacheKey] = $name;
                return $name;
            }
        }

        $schoolId = $student->school_id;
        $classId = $student->class_id;

        $schedules = static::$scheduleCache["schedules_{$schoolId}"] ??= FeeSchedule::where('school_id', $schoolId)->get();

        foreach ($schedules as $sched) {
            $classes = array_map('trim', explode(',', $sched->classes ?? ''));
            $className = $student->schoolClass->name ?? ($student->class->name ?? '');
            if (in_array((string)$classId, $classes) || in_array($className, $classes)) {
                $name = $sched->getInstallmentName($instNo);
                static::$scheduleCache[$cacheKey] = $name;
                return $name;
            }
        }

        $fallback = 'Installment ' . $instNo;
        static::$scheduleCache[$cacheKey] = $fallback;
        return $fallback;
    }

    /**
     * Check if a given FeeSchedule is applicable to a specific Class and Section.
     */
    public static function isScheduleApplicable(FeeSchedule $schedule, ?string $className, ?string $sectionName): bool
    {
        $className = trim((string)$className);
        $sectionName = trim((string)$sectionName);

        if ($className === '') {
            return false;
        }

        // 1. Parse and check class applicability
        $classesStr = trim((string)($schedule->classes ?? ''));
        $schClasses = [];
        if (\Illuminate\Support\Str::startsWith($classesStr, '[') && \Illuminate\Support\Str::endsWith($classesStr, ']')) {
            $schClasses = json_decode($classesStr, true) ?? [];
        } else {
            $schClasses = array_map('trim', explode(',', $classesStr));
        }

        $classMatches = false;
        $cleanClassName = mb_strtolower($className);
        foreach ($schClasses as $c) {
            if (mb_strtolower(trim((string)$c)) === $cleanClassName) {
                $classMatches = true;
                break;
            }
        }

        if (!$classMatches) {
            return false;
        }

        // 2. Parse and check section applicability
        $sectionsStr = trim((string)($schedule->sections ?? ''));
        if ($sectionsStr === '') {
            // No specific section restriction; applies to all sections of this class
            return true;
        }

        if ($sectionName === '') {
            // Schedule specifies section restrictions, but student has no section
            return false;
        }

        $schSections = [];
        if (\Illuminate\Support\Str::startsWith($sectionsStr, '[') && \Illuminate\Support\Str::endsWith($sectionsStr, ']')) {
            $schSections = json_decode($sectionsStr, true) ?? [];
        } else {
            $schSections = array_map('trim', explode(',', $sectionsStr));
        }

        $cleanSectionName = mb_strtolower($sectionName);
        $candidatePatterns = [
            mb_strtolower($className . '-' . $sectionName),
            mb_strtolower($className . ' - ' . $sectionName),
            mb_strtolower($className . '_' . $sectionName),
            mb_strtolower($className . ' ' . $sectionName),
            $cleanSectionName,
            mb_strtolower('section ' . $sectionName),
            mb_strtolower('section-' . $sectionName),
        ];

        foreach ($schSections as $s) {
            $cleanScheduleSec = mb_strtolower(trim((string)$s));
            if (in_array($cleanScheduleSec, $candidatePatterns, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Find the applicable FeeSchedule for a specific School, Academic Session, Class, and Section.
     */
    public static function findApplicableSchedule(int|string $schoolId, int|string $sessionId, ?string $className, ?string $sectionName): ?FeeSchedule
    {
        if (!$schoolId || !$sessionId || !$className) {
            return null;
        }

        $schedules = FeeSchedule::where('school_id', $schoolId)
            ->where('academic_session_id', $sessionId)
            ->get();

        if ($schedules->isEmpty()) {
            return null;
        }

        $sectionSpecificMatch = null;
        $classWideMatch = null;

        foreach ($schedules as $sched) {
            if (static::isScheduleApplicable($sched, $className, $sectionName)) {
                $hasExplicitSections = trim((string)($sched->sections ?? '')) !== '';
                if ($hasExplicitSections) {
                    $sectionSpecificMatch = $sched;
                    break; // Exact section match found
                } elseif (!$classWideMatch) {
                    $classWideMatch = $sched;
                }
            }
        }

        return $sectionSpecificMatch ?? $classWideMatch;
    }

    /**
     * Resolve the applicable FeeSchedule for a Student model, respecting the specified or student's academic session.
     */
    public static function resolveStudentSchedule(Student $student, ?int $sessionId = null): ?FeeSchedule
    {
        $schoolId = $student->school_id;
        $targetSessionId = $sessionId ?: ($student->academic_session_id ?: null);

        if (!$targetSessionId) {
            $currentSession = \App\Models\AcademicSession::where('school_id', $schoolId)
                ->where('is_current', true)
                ->first();
            $targetSessionId = $currentSession?->id;
        }

        if (!$targetSessionId) {
            return null;
        }

        // Determine student class and section in the target session
        $sessionRec = $student->relationLoaded('studentSessions')
            ? $student->studentSessions->firstWhere('academic_session_id', $targetSessionId)
            : $student->studentSessions()->where('academic_session_id', $targetSessionId)->first();

        $studentClass = $sessionRec?->schoolClass ?? $student->class ?? \App\Models\SchoolClass::where('school_id', $schoolId)->find($student->class_id);
        $studentSection = $sessionRec?->section ?? $student->section ?? \App\Models\Section::where('school_id', $schoolId)->find($student->section_id);

        $className = optional($studentClass)->name;
        $sectionName = optional($studentSection)->name;

        return static::findApplicableSchedule($schoolId, $targetSessionId, $className, $sectionName);
    }

    /**
     * Get all academic session IDs in which the student has valid enrollment or active history.
     */
    public static function getStudentEnrolledSessionIds(Student $student): array
    {
        $sessionIds = collect();
        $schoolId = $student->school_id;

        // 1. Current student record's academic_session_id
        if (!empty($student->academic_session_id)) {
            $sessionIds->push((int)$student->academic_session_id);
        }

        // 2. Student sessions relation
        if ($student->relationLoaded('studentSessions')) {
            $sessIds = $student->studentSessions->pluck('academic_session_id')->filter()->map(fn($id) => (int)$id);
            $sessionIds = $sessionIds->merge($sessIds);
        } else {
            $sessIds = $student->studentSessions()->pluck('academic_session_id')->filter()->map(fn($id) => (int)$id);
            $sessionIds = $sessionIds->merge($sessIds);
        }

        // 3. Linked student records by admission_number (cross-session promotions / multiple year records)
        $linkedStudentIds = [$student->id];
        if (!empty($student->admission_number)) {
            $linkedStudents = Student::where('school_id', $schoolId)
                ->where('admission_number', $student->admission_number)
                ->get(['id', 'academic_session_id']);
            
            $linkedSessionIds = $linkedStudents->pluck('academic_session_id')->filter()->map(fn($id) => (int)$id);
            $sessionIds = $sessionIds->merge($linkedSessionIds);

            $linkedIds = $linkedStudents->pluck('id')->toArray();
            if (!empty($linkedIds)) {
                $linkedStudentIds = array_unique(array_merge($linkedStudentIds, $linkedIds));
                $linkedSessionRecs = \Illuminate\Support\Facades\DB::table('student_sessions')
                    ->whereIn('student_id', $linkedIds)
                    ->pluck('academic_session_id')
                    ->filter()
                    ->map(fn($id) => (int)$id);
                $sessionIds = $sessionIds->merge($linkedSessionRecs);
            }
        }

        // 4. Any academic session where the student (or linked student) has existing fees or fee schedules
        $feeSessionIds = FeeSchedule::where('school_id', $schoolId)
            ->whereIn('id', function($q) use ($schoolId, $linkedStudentIds) {
                $q->select('fee_schedule_id')
                  ->from('student_fees')
                  ->where('school_id', $schoolId)
                  ->whereIn('student_id', $linkedStudentIds)
                  ->whereNotNull('fee_schedule_id');
            })
            ->pluck('academic_session_id')
            ->filter()
            ->map(fn($id) => (int)$id);
        $sessionIds = $sessionIds->merge($feeSessionIds);

        $transportSessionIds = TransportFeeSchedule::where('school_id', $schoolId)
            ->whereIn('id', function($q) use ($schoolId, $linkedStudentIds) {
                $q->select('transport_fee_schedule_id')
                  ->from('student_fees')
                  ->where('school_id', $schoolId)
                  ->whereIn('student_id', $linkedStudentIds)
                  ->whereNotNull('transport_fee_schedule_id');
            })
            ->pluck('academic_session_id')
            ->filter()
            ->map(fn($id) => (int)$id);
        $sessionIds = $sessionIds->merge($transportSessionIds);

        $miscSessionIds = \App\Models\MiscFee::where('school_id', $schoolId)
            ->whereIn('id', function($q) use ($schoolId, $linkedStudentIds) {
                $q->select('misc_fee_id')
                  ->from('student_fees')
                  ->where('school_id', $schoolId)
                  ->whereIn('student_id', $linkedStudentIds)
                  ->whereNotNull('misc_fee_id');
            })
            ->pluck('academic_session_id')
            ->filter()
            ->map(fn($id) => (int)$id);
        $sessionIds = $sessionIds->merge($miscSessionIds);

        // 5. Academic sessions covered by student's admission_date
        if (!empty($student->admission_date)) {
            try {
                $admDate = \Carbon\Carbon::parse($student->admission_date)->toDateString();
                $coveredSessions = \App\Models\AcademicSession::where('school_id', $schoolId)
                    ->where(function($q) use ($admDate) {
                        $q->where('end_date', '>=', $admDate)
                          ->orWhere('start_date', '>=', $admDate);
                    })
                    ->pluck('id')
                    ->map(fn($id) => (int)$id);
                $sessionIds = $sessionIds->merge($coveredSessions);
            } catch (\Throwable $e) {}
        }

        return $sessionIds->unique()->values()->toArray();
    }

    /**
     * Check whether a student was enrolled in any of the specified previous academic sessions.
     */
    public static function isStudentEnrolledInPreviousSessions(Student $student, array $previousSessionIds, $selectedSession = null): bool
    {
        if (empty($previousSessionIds)) {
            return false;
        }

        $enrolledSessionIds = static::getStudentEnrolledSessionIds($student);
        $hasMatch = !empty(array_intersect($enrolledSessionIds, array_map('intval', $previousSessionIds)));
        if ($hasMatch) {
            return true;
        }

        if ($selectedSession && !empty($selectedSession->start_date) && !empty($student->admission_date)) {
            try {
                $admDate = \Carbon\Carbon::parse($student->admission_date)->toDateString();
                $sessStart = \Carbon\Carbon::parse($selectedSession->start_date)->toDateString();
                if ($admDate < $sessStart) {
                    return true;
                }
            } catch (\Throwable $e) {}
        }

        return false;
    }

    /**
     * Get or build cached session enrollment and promotion eligibility to eliminate N+1 queries.
     */
    public static function getSessionStudentMetadata(int $schoolId, ?\App\Models\AcademicSession $session): array
    {
        if (!$session) {
            return [
                'enrolledStudents'           => collect(),
                'enrolledStudentIds'         => [],
                'previousSessionIds'         => [],
                'eligiblePromotedStudentIds' => [],
                'newAdmissionStudentIds'     => [],
            ];
        }

        $cacheKey = "sess_{$schoolId}_{$session->id}";
        if (isset(static::$requestSessionCache[$cacheKey])) {
            return static::$requestSessionCache[$cacheKey];
        }

        // 1. All students enrolled in current session
        $enrolledStudents = Student::where('school_id', $schoolId)
            ->where(function($q) use ($session) {
                $q->where('academic_session_id', $session->id)
                  ->orWhereHas('studentSessions', fn($sq) => $sq->where('academic_session_id', $session->id));
            })
            ->with(['studentSessions' => fn($q) => $q->where('school_id', $schoolId)])
            ->get(['id', 'school_id', 'admission_number', 'admission_date', 'academic_session_id', 'class_id', 'section_id', 'fee_schedule_id']);

        // 2. Identify previous sessions
        $previousSessions = \App\Models\AcademicSession::where('school_id', $schoolId)
            ->where('id', '!=', $session->id)
            ->where(function($q) use ($session) {
                if ($session->start_date) {
                    $q->where('end_date', '<=', $session->start_date)
                      ->orWhere('id', '<', $session->id);
                } else {
                    $q->where('id', '<', $session->id);
                }
            })
            ->get(['id', 'start_date', 'end_date']);
        $previousSessionIds = $previousSessions->pluck('id')->toArray();

        // 3. Preload all admission numbers and student IDs that appeared in previous sessions via single bulk queries (0 N+1 queries!)
        $prevAdmNumbers = [];
        $prevSessionStudentIds = [];
        if (!empty($previousSessionIds)) {
            $prevAdmNumbers = Student::where('school_id', $schoolId)
                ->whereIn('academic_session_id', $previousSessionIds)
                ->whereNotNull('admission_number')
                ->pluck('admission_number')
                ->flip()
                ->toArray();

            $prevSessionStudentIds = \Illuminate\Support\Facades\DB::table('student_sessions')
                ->whereIn('academic_session_id', $previousSessionIds)
                ->pluck('student_id')
                ->flip()
                ->toArray();
        }

        $eligiblePromotedStudentIds = [];
        $newAdmissionStudentIds = [];
        $sessStart = $session->start_date ? \Carbon\Carbon::parse($session->start_date)->toDateString() : null;

        foreach ($enrolledStudents as $st) {
            $isPrevious = false;
            // Check student record's academic_session_id
            if ($st->academic_session_id && in_array((int)$st->academic_session_id, $previousSessionIds, true)) {
                $isPrevious = true;
            }
            // Check loaded studentSessions relation
            if (!$isPrevious && $st->studentSessions) {
                foreach ($st->studentSessions as $ss) {
                    if (in_array((int)$ss->academic_session_id, $previousSessionIds, true)) {
                        $isPrevious = true;
                        break;
                    }
                }
            }
            // Check preloaded student_sessions table
            if (!$isPrevious && isset($prevSessionStudentIds[$st->id])) {
                $isPrevious = true;
            }
            // Check admission_number match in previous sessions
            if (!$isPrevious && !empty($st->admission_number) && isset($prevAdmNumbers[$st->admission_number])) {
                $isPrevious = true;
            }
            // Check admission_date before session start date
            if (!$isPrevious && $sessStart && !empty($st->admission_date)) {
                try {
                    if (\Carbon\Carbon::parse($st->admission_date)->toDateString() < $sessStart) {
                        $isPrevious = true;
                    }
                } catch (\Throwable $e) {}
            }

            if ($isPrevious) {
                $eligiblePromotedStudentIds[] = $st->id;
            } else {
                $newAdmissionStudentIds[] = $st->id;
            }
        }

        $data = [
            'enrolledStudents'           => $enrolledStudents,
            'enrolledStudentIds'         => $enrolledStudents->pluck('id')->toArray(),
            'previousSessionIds'         => $previousSessionIds,
            'eligiblePromotedStudentIds' => $eligiblePromotedStudentIds,
            'newAdmissionStudentIds'     => $newAdmissionStudentIds,
        ];

        static::$requestSessionCache[$cacheKey] = $data;
        return $data;
    }

    /**
     * Purge rogue unpaid/unlocked fee records from previous academic sessions that were mistakenly created
     * for fresh students newly admitted in the specified session. Executed only once per request.
     */
    public static function purgeRoguePreviousSessionFees(int $schoolId, ?\App\Models\AcademicSession $currentSession): void
    {
        if (!$currentSession) {
            return;
        }

        $purgeKey = "purged_{$schoolId}_{$currentSession->id}";
        if (isset(static::$purgedSessions[$purgeKey])) {
            return;
        }
        static::$purgedSessions[$purgeKey] = true;

        $meta = static::getSessionStudentMetadata($schoolId, $currentSession);
        $previousSessionIds     = $meta['previousSessionIds'];
        $newAdmissionStudentIds = $meta['newAdmissionStudentIds'];

        if (empty($previousSessionIds) || empty($newAdmissionStudentIds)) {
            return;
        }

        StudentFee::withoutGlobalScope('active')
            ->where('school_id', $schoolId)
            ->whereIn('student_id', $newAdmissionStudentIds)
            ->where('paid_amount', '<=', 0)
            ->where('instant_discount_amount', '<=', 0)
            ->where('status', '!=', 'refunded')
            ->where(function($q) use ($previousSessionIds, $currentSession) {
                $q->whereHas('feeSchedule', fn($sq) => $sq->whereIn('academic_session_id', $previousSessionIds))
                  ->orWhereHas('transportFeeSchedule', fn($sq) => $sq->whereIn('academic_session_id', $previousSessionIds))
                  ->orWhereHas('miscFee', fn($sq) => $sq->whereIn('academic_session_id', $previousSessionIds));
                if ($currentSession->start_date) {
                    $q->orWhere(function($sub) use ($currentSession) {
                        $sub->whereNull('fee_schedule_id')
                            ->whereNull('transport_fee_schedule_id')
                            ->whereNull('misc_fee_id')
                            ->where('due_date', '<', $currentSession->start_date);
                    });
                }
            })
            ->delete();
    }

    /**
     * Auto-sync fees for active enrolled students in this session who have no fees assigned yet.
     */
    public static function autoSyncMissingStudentFees(int $schoolId, ?\App\Models\AcademicSession $session): void
    {
        if (!$session) return;

        $syncKey = "autosync_{$schoolId}_{$session->id}";
        if (isset(static::$purgedSessions[$syncKey])) {
            return;
        }
        static::$purgedSessions[$syncKey] = true;

        $studentsWithoutFees = Student::where('school_id', $schoolId)
            ->where('is_active', true)
            ->where(function($q) use ($session) {
                $q->where('academic_session_id', $session->id)
                  ->orWhereHas('studentSessions', fn($sq) => $sq->where('academic_session_id', $session->id));
            })
            ->whereDoesntHave('studentFees', function($fq) use ($session) {
                $fq->where(function($sq) use ($session) {
                    $sq->whereHas('feeSchedule', fn($schQ) => $schQ->where('academic_session_id', $session->id))
                       ->orWhereHas('transportFeeSchedule', fn($schQ) => $schQ->where('academic_session_id', $session->id))
                       ->orWhereHas('miscFee', fn($schQ) => $schQ->where('academic_session_id', $session->id));
                    if ($session->start_date && $session->end_date) {
                        $sq->orWhereBetween('due_date', [
                            $session->start_date->copy()->startOfDay(),
                            $session->end_date->copy()->endOfDay()
                        ]);
                    }
                });
            })
            ->with(['studentSessions' => fn($q) => $q->where('academic_session_id', $session->id)])
            ->get(['id', 'school_id', 'class_id', 'section_id', 'category_id', 'boarding_type', 'fee_schedule_id', 'academic_session_id']);

        if ($studentsWithoutFees->isEmpty()) {
            return;
        }

        foreach ($studentsWithoutFees as $st) {
            $sessRec = $st->studentSessions->firstWhere('academic_session_id', $session->id);
            $classId = $sessRec?->class_id ?? $st->class_id;
            $sectionId = $sessRec?->section_id ?? $st->section_id;

            if (!$classId) continue;

            $cwFees = \App\Models\ClassWiseFee::where('school_id', $schoolId)
                ->where('is_active', true)
                ->where(function($q) use ($session) {
                    $q->where('academic_session_id', $session->id)
                      ->orWhereNull('academic_session_id');
                })
                ->where('class_id', $classId)
                ->where(function($q) use ($sectionId) {
                    $q->whereNull('section_id')
                      ->orWhere('section_id', $sectionId);
                })
                ->get();

            foreach ($cwFees as $cwFee) {
                try {
                    \App\Http\Controllers\School\FeeManagementController::syncClassWiseFeeToStudents($schoolId, $cwFee);
                } catch (\Throwable $e) {}
            }
        }
    }

    /**
     * Build an Eloquent query for StudentFees that legitimately belong to the given session,
     * including legitimate carried-forward previous year dues for promoted students.
     */
    public static function buildSessionFeeQuery(int $schoolId, ?\App\Models\AcademicSession $session, bool $tillDateOnly = false, bool $strictSessionOnly = false)
    {
        $baseQuery = StudentFee::where('school_id', $schoolId);

        if (!$session) {
            if ($tillDateOnly) {
                $baseQuery->where('due_date', '<=', today()->toDateString());
            }
            return $baseQuery;
        }

        // 1. Purge any rogue unpaid previous session fees (runs at most once per request)
        static::purgeRoguePreviousSessionFees($schoolId, $session);

        // 2. Resolve cached student enrollment metadata (0 redundant DB queries)
        $meta = static::getSessionStudentMetadata($schoolId, $session);
        $enrolledStudentIds         = $meta['enrolledStudentIds'];
        $previousSessionIds         = $meta['previousSessionIds'];
        $eligiblePromotedStudentIds = $meta['eligiblePromotedStudentIds'];

        // 3. Build session-scoped fee query
        $baseQuery->whereIn('student_id', $enrolledStudentIds)
            ->where(function($query) use ($session, $previousSessionIds, $eligiblePromotedStudentIds, $tillDateOnly, $strictSessionOnly) {
                // A) Fees belonging directly to current session
                $query->where(function($cq) use ($session, $tillDateOnly) {
                    $cq->where(function($schQ) use ($session) {
                        $schQ->whereHas('feeSchedule', function($sq) use ($session) {
                            $sq->where(function($sSub) use ($session) {
                                $sSub->where('academic_session_id', $session->id)
                                     ->orWhereNull('academic_session_id');
                            });
                        })
                        ->orWhereHas('transportFeeSchedule', function($sq) use ($session) {
                            $sq->where(function($sSub) use ($session) {
                                $sSub->where('academic_session_id', $session->id)
                                     ->orWhereNull('academic_session_id');
                            });
                        })
                        ->orWhereHas('miscFee', function($sq) use ($session) {
                            $sq->where(function($sSub) use ($session) {
                                $sSub->where('academic_session_id', $session->id)
                                     ->orWhereNull('academic_session_id');
                            });
                        });

                        if ($session->start_date && $session->end_date) {
                            $schQ->orWhereBetween('due_date', [
                                $session->start_date->copy()->startOfDay(),
                                $session->end_date->copy()->endOfDay()
                            ]);
                        }
                    });

                    if ($tillDateOnly) {
                        $cq->where('due_date', '<=', today()->toDateString());
                    }
                });

                // B) Legitimate carried-forward previous session dues (strictly for eligible promoted students with UNPAID dues)
                if (!$strictSessionOnly && !empty($eligiblePromotedStudentIds) && !empty($previousSessionIds)) {
                    $query->orWhere(function($prevQ) use ($previousSessionIds, $session, $eligiblePromotedStudentIds) {
                        $prevQ->whereIn('student_id', $eligiblePromotedStudentIds)
                              ->where(function($unpaidQ) {
                                  $unpaidQ->where('status', '!=', 'paid')
                                          ->whereRaw('(amount + COALESCE(fine_amount_applied, 0) - COALESCE(instant_discount_amount, 0)) > COALESCE(paid_amount, 0)');
                              })
                              ->where(function($sub) use ($previousSessionIds, $session) {
                                  $sub->whereHas('feeSchedule', fn($sq) => $sq->whereIn('academic_session_id', $previousSessionIds))
                                      ->orWhereHas('transportFeeSchedule', fn($sq) => $sq->whereIn('academic_session_id', $previousSessionIds))
                                      ->orWhereHas('miscFee', fn($sq) => $sq->whereIn('academic_session_id', $previousSessionIds));
                                  if ($session->start_date) {
                                      $sub->orWhere(function($ss) use ($session) {
                                          $ss->whereNull('fee_schedule_id')
                                             ->whereNull('transport_fee_schedule_id')
                                             ->whereNull('misc_fee_id')
                                             ->where('due_date', '<', $session->start_date);
                                      });
                                  }
                              });
                    });
                }
            });

        return $baseQuery;
    }

    /**
     * Calculate comprehensive, session-scoped fee collection, dues, and pending metrics.
     */
    public static function calculateSessionFeeMetrics(int $schoolId, ?\App\Models\AcademicSession $session): array
    {
        // Auto-heal active enrolled students without fees if applicable
        if ($session) {
            static::autoSyncMissingStudentFees($schoolId, $session);
        }

        $meta = static::getSessionStudentMetadata($schoolId, $session);
        $totalStudentsInSession = count($meta['enrolledStudentIds']);

        $schoolPendingChequesTotal = (float) \App\Models\PendingCheque::where('school_id', $schoolId)
            ->where('status', 'pending')
            ->sum('amount');

        // 1. Till Date Query & Metrics
        $tillDateQuery = static::buildSessionFeeQuery($schoolId, $session, true);
        $tillDateFees = (clone $tillDateQuery)->get();

        $tillDateAssigned = 0.0;
        $tillDateDiscount = 0.0;
        $tillDateFine     = 0.0;
        $tillDatePaid     = 0.0;
        $tillDateDue      = 0.0;
        $pendingStudentIdsTillDate  = [];
        $assignedStudentIdsTillDate = [];

        foreach ($tillDateFees as $fee) {
            $amt  = (float) $fee->amount;
            $disc = (float) ($fee->instant_discount_amount ?? 0);
            $fn   = (float) ($fee->fine_amount_applied ?? 0);
            $pd   = (float) ($fee->paid_amount ?? 0);
            $netDue = max(0.00, $amt + $fn - $disc - $pd);

            $tillDateAssigned += $amt;
            $tillDateDiscount += $disc;
            $tillDateFine     += $fn;
            $tillDatePaid     += $pd;
            $tillDateDue      += $netDue;

            $assignedStudentIdsTillDate[$fee->student_id] = true;
            if ($netDue > 0.001) {
                $pendingStudentIdsTillDate[$fee->student_id] = true;
            }
        }

        $tillDateDue = max(0.00, $tillDateDue - $schoolPendingChequesTotal);
        $tillDateTotalSum = $tillDatePaid + $tillDateDue;
        $tillDateCollectedPct = $tillDateTotalSum > 0 ? min(100.0, round(($tillDatePaid / $tillDateTotalSum) * 100, 2)) : 0.0;
        $tillDateDuePct = $tillDateTotalSum > 0 ? min(100.0, round(($tillDateDue / $tillDateTotalSum) * 100, 2)) : 0.0;
        $tillDatePendingStudentsCount = count($pendingStudentIdsTillDate);
        $tillDateAssignedStudentsCount = count($assignedStudentIdsTillDate);

        // 2. Annual (Entire Session) Query & Metrics
        $annualQuery = static::buildSessionFeeQuery($schoolId, $session, false);
        $annualFees = (clone $annualQuery)->get();

        $annualAssigned = 0.0;
        $annualDiscount = 0.0;
        $annualFine     = 0.0;
        $annualPaid     = 0.0;
        $annualDue      = 0.0;
        $pendingStudentIdsAnnual  = [];
        $assignedStudentIdsAnnual = [];

        foreach ($annualFees as $fee) {
            $amt  = (float) $fee->amount;
            $disc = (float) ($fee->instant_discount_amount ?? 0);
            $fn   = (float) ($fee->fine_amount_applied ?? 0);
            $pd   = (float) ($fee->paid_amount ?? 0);
            $netDue = max(0.00, $amt + $fn - $disc - $pd);

            $annualAssigned += $amt;
            $annualDiscount += $disc;
            $annualFine     += $fn;
            $annualPaid     += $pd;
            $annualDue      += $netDue;

            $assignedStudentIdsAnnual[$fee->student_id] = true;
            if ($netDue > 0.001) {
                $pendingStudentIdsAnnual[$fee->student_id] = true;
            }
        }

        $annualDue = max(0.00, $annualDue - $schoolPendingChequesTotal);
        $annualTotalSum = $annualPaid + $annualDue;
        $annualCollectedPct = $annualTotalSum > 0 ? min(100.0, round(($annualPaid / $annualTotalSum) * 100, 2)) : 0.0;
        $annualDuePct = $annualTotalSum > 0 ? min(100.0, round(($annualDue / $annualTotalSum) * 100, 2)) : 0.0;
        $annualPendingStudentsCount = count($pendingStudentIdsAnnual);
        $annualAssignedStudentsCount = count($assignedStudentIdsAnnual);

        // 3. Today's metrics (Today fee collection & due strictly scoped to session)
        $todayFeeCollection = 0.0;
        $countedInvoiceNumbers = [];

        $sessionFeeIdMap = $annualFees->pluck('id')->flip()->all();
        $enrolledStudentIdMap = array_flip($meta['enrolledStudentIds'] ?? []);
        $todayStr = today()->toDateString();

        // Query active, non-cancelled fee payments whose payment date is TODAY
        $todayInvoices = \App\Models\FeeInvoice::where('school_id', $schoolId)
            ->where('type', 'payment')
            ->where('status', 'paid')
            ->whereDate('payment_date', $todayStr)
            ->get();

        foreach ($todayInvoices as $inv) {
            $invAmount = (float) $inv->amount;
            if ($invAmount <= 0) {
                continue;
            }

            $hasSessionMatch = false;
            $matchedAmount = 0.0;

            if (!empty($inv->payment_details)) {
                $details = is_string($inv->payment_details) ? json_decode($inv->payment_details, true) : $inv->payment_details;
                if (is_array($details)) {
                    $components = $details['components'] ?? (isset($details[0]['student_fee_id']) ? $details : []);
                    if (!empty($components) && is_array($components)) {
                        $hasComponentWithFeeId = false;
                        foreach ($components as $comp) {
                            if (!is_array($comp)) continue;
                            $sfId = $comp['student_fee_id'] ?? null;
                            if ($sfId) {
                                $hasComponentWithFeeId = true;
                                if (isset($sessionFeeIdMap[$sfId])) {
                                    $hasSessionMatch = true;
                                    $matchedAmount += (float) ($comp['amount_paid'] ?? 0);
                                }
                            }
                        }

                        // If components had student_fee_ids and we found matches, use the matched component amount
                        if ($hasComponentWithFeeId && $hasSessionMatch) {
                            $todayFeeCollection += $matchedAmount;
                            $countedInvoiceNumbers[$inv->invoice_number] = true;
                            if (!empty($inv->receipt_number)) {
                                $countedInvoiceNumbers[$inv->receipt_number] = true;
                            }
                            continue;
                        } elseif ($hasComponentWithFeeId && !$hasSessionMatch) {
                            // Components belonged explicitly to another session
                            continue;
                        }
                    }
                }
            }

            // Fallback for payments without explicit component student_fee_ids:
            // Check student enrollment in this session
            if (isset($enrolledStudentIdMap[$inv->student_id])) {
                $todayFeeCollection += $invAmount;
                $countedInvoiceNumbers[$inv->invoice_number] = true;
                if (!empty($inv->receipt_number)) {
                    $countedInvoiceNumbers[$inv->receipt_number] = true;
                }
            }
        }

        // Support any standalone FeeReceipts created today without a corresponding FeeInvoice (ensuring no double-counting or cancelled receipts)
        $todayReceipts = \App\Models\FeeReceipt::withoutGlobalScope('active')
            ->where('school_id', $schoolId)
            ->where(function($q) {
                $q->where('status', '!=', 'cancelled')
                  ->orWhereNull('status');
            })
            ->whereDate('payment_date', $todayStr)
            ->get();

        foreach ($todayReceipts as $rec) {
            // Skip if this receipt number or matching invoice was already counted
            if (isset($countedInvoiceNumbers[$rec->receipt_number])) {
                continue;
            }

            // Verify receipt has not been cancelled via FeeInvoice
            $isCancelledInInvoice = \App\Models\FeeInvoice::where('school_id', $schoolId)
                ->where('status', 'cancelled')
                ->where(function($q) use ($rec) {
                    $q->where('invoice_number', $rec->receipt_number)
                      ->orWhere('related_invoice_number', $rec->receipt_number)
                      ->orWhere('payment_details', 'like', '%' . $rec->receipt_number . '%');
                })
                ->exists();

            if ($isCancelledInInvoice) {
                continue;
            }

            $recAmount = (float) $rec->amount_paid;
            if ($recAmount <= 0) {
                continue;
            }

            $hasSessionMatch = false;
            $matchedAmount = 0.0;

            if (!empty($rec->payment_details)) {
                $details = is_string($rec->payment_details) ? json_decode($rec->payment_details, true) : $rec->payment_details;
                if (is_array($details)) {
                    $components = $details['components'] ?? (isset($details[0]['student_fee_id']) ? $details : []);
                    if (!empty($components) && is_array($components)) {
                        $hasComponentWithFeeId = false;
                        foreach ($components as $comp) {
                            if (!is_array($comp)) continue;
                            $sfId = $comp['student_fee_id'] ?? null;
                            if ($sfId) {
                                $hasComponentWithFeeId = true;
                                if (isset($sessionFeeIdMap[$sfId])) {
                                    $hasSessionMatch = true;
                                    $matchedAmount += (float) ($comp['amount_paid'] ?? 0);
                                }
                            }
                        }

                        if ($hasComponentWithFeeId && $hasSessionMatch) {
                            $todayFeeCollection += $matchedAmount;
                            $countedInvoiceNumbers[$rec->receipt_number] = true;
                            continue;
                        } elseif ($hasComponentWithFeeId && !$hasSessionMatch) {
                            continue;
                        }
                    }
                }
            }

            if (isset($enrolledStudentIdMap[$rec->student_id])) {
                $todayFeeCollection += $recAmount;
                $countedInvoiceNumbers[$rec->receipt_number] = true;
            }
        }

        $todayFeeDue = (float) (clone $tillDateQuery)
            ->whereDate('due_date', $todayStr)
            ->sum(\Illuminate\Support\Facades\DB::raw('amount + COALESCE(fine_amount_applied, 0) - paid_amount - COALESCE(instant_discount_amount, 0)'));
        $todayFeeDue = max(0.00, $todayFeeDue);

        $todayFeeCollectionPct = 0.0;
        if ($todayFeeCollection > 0) {
            if ($todayFeeDue > 0) {
                $todayFeeCollectionPct = min(100.0, round(($todayFeeCollection / $todayFeeDue) * 100));
            } else {
                $todayFeeCollectionPct = 100.0;
            }
        }

        return [
            'tillDateCollected'             => $tillDatePaid,
            'tillDateDue'                   => $tillDateDue,
            'tillDateTotal'                 => $tillDateTotalSum,
            'tillDateCollectedPct'          => $tillDateCollectedPct,
            'tillDateDuePct'                => $tillDateDuePct,
            'tillDatePendingStudentsCount'  => $tillDatePendingStudentsCount,
            'tillDateAssignedStudentsCount' => $tillDateAssignedStudentsCount,

            'annualCollected'               => $annualPaid,
            'annualDue'                     => $annualDue,
            'annualTotal'                   => $annualTotalSum,
            'annualCollectedPct'            => $annualCollectedPct,
            'annualDuePct'                  => $annualDuePct,
            'annualPendingStudentsCount'    => $annualPendingStudentsCount,
            'annualAssignedStudentsCount'   => $annualAssignedStudentsCount,
            'totalStudentsInSession'        => $totalStudentsInSession,

            'todayFeeCollection'            => $todayFeeCollection,
            'todayFeeDue'                   => $todayFeeDue,
            'todayFeeCollectionPct'         => $todayFeeCollectionPct,
            'schoolPendingChequesTotal'     => $schoolPendingChequesTotal,
        ];
    }
}


