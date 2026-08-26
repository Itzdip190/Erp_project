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
}

