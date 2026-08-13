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
}
