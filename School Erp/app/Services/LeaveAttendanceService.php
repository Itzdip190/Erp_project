<?php

namespace App\Services;

use App\Models\AcademicSession;
use App\Models\StaffAttendance;
use App\Models\StaffLeaveApplication;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudentLeaveApplication;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class LeaveAttendanceService
{
    /**
     * Synchronize Student Leave Application with Student Attendance records.
     */
    public static function syncStudentLeaveAttendance(StudentLeaveApplication $app): void
    {
        $schoolId = $app->school_id;
        $studentId = $app->student_id;

        if (!$schoolId || !$studentId || !$app->from_date || !$app->to_date) {
            return;
        }

        $startDate = Carbon::parse($app->from_date)->startOfDay();
        $endDate = Carbon::parse($app->to_date)->startOfDay();

        if ($startDate->gt($endDate)) {
            return;
        }

        $period = CarbonPeriod::create($startDate, $endDate);

        if ($app->status === 'approved') {
            $student = $app->student ?? Student::find($studentId);
            $classId = $app->class_id ?? $student?->class_id;
            $sectionId = $app->section_id ?? $student?->section_id;

            $sessionId = $student?->academic_session_id;
            if (!$sessionId && $schoolId) {
                $sessionId = AcademicSession::where('school_id', $schoolId)
                    ->where('name', $app->academic_year)
                    ->value('id')
                    ?? AcademicSession::where('school_id', $schoolId)
                        ->where('is_current', true)
                        ->value('id')
                    ?? AcademicSession::where('school_id', $schoolId)
                        ->value('id');
            }

            $markedBy = $app->action_by ?? $app->user_id ?? auth()->id() ?? 1;
            $remarkText = 'Approved Leave' . ($app->leave_type ? ': ' . $app->leave_type : '');

            foreach ($period as $date) {
                $dateStr = $date->format('Y-m-d');

                $att = StudentAttendance::where('school_id', $schoolId)
                    ->where('student_id', $studentId)
                    ->whereDate('date', $dateStr)
                    ->first();

                if ($att) {
                    $att->update([
                        'class_id'            => $classId ?? $att->class_id,
                        'section_id'          => $sectionId ?? $att->section_id,
                        'academic_session_id' => $sessionId ?? $att->academic_session_id,
                        'status'              => 'leave',
                        'marked_by'           => $markedBy,
                        'remark'              => $remarkText,
                        'attendance_type'     => 'manual',
                    ]);
                } else {
                    StudentAttendance::create([
                        'school_id'           => $schoolId,
                        'student_id'          => $studentId,
                        'class_id'            => $classId,
                        'section_id'          => $sectionId,
                        'academic_session_id' => $sessionId,
                        'date'                => $dateStr,
                        'status'              => 'leave',
                        'marked_by'           => $markedBy,
                        'remark'              => $remarkText,
                        'attendance_type'     => 'manual',
                    ]);
                }
            }
        } else {
            // Revert attendance if leave is pending, rejected, cancelled, or acknowledged without approval
            foreach ($period as $date) {
                $dateStr = $date->format('Y-m-d');

                StudentAttendance::where('school_id', $schoolId)
                    ->where('student_id', $studentId)
                    ->whereDate('date', $dateStr)
                    ->where('status', 'leave')
                    ->delete();
            }
        }
    }

    /**
     * Synchronize Staff Leave Application with Staff Attendance records.
     */
    public static function syncStaffLeaveAttendance(StaffLeaveApplication $app): void
    {
        $schoolId = $app->school_id;
        $staffId = $app->staff_id;

        if (!$schoolId || !$staffId || !$app->start_date || !$app->end_date) {
            return;
        }

        $startDate = Carbon::parse($app->start_date)->startOfDay();
        $endDate = Carbon::parse($app->end_date)->startOfDay();

        if ($startDate->gt($endDate)) {
            return;
        }

        $period = CarbonPeriod::create($startDate, $endDate);

        if ($app->status === 'approved') {
            $markedBy = $app->approved_by ?? auth()->id() ?? 1;

            foreach ($period as $date) {
                $dateStr = $date->format('Y-m-d');

                $att = StaffAttendance::where('school_id', $schoolId)
                    ->where('staff_id', $staffId)
                    ->whereDate('date', $dateStr)
                    ->first();

                if ($att) {
                    $att->update([
                        'status'          => 'leave',
                        'clock_in_at'     => null,
                        'clock_out_at'    => null,
                        'marked_by'       => $markedBy,
                        'attendance_type' => 'manual',
                    ]);
                } else {
                    StaffAttendance::create([
                        'school_id'       => $schoolId,
                        'staff_id'        => $staffId,
                        'date'            => $dateStr,
                        'status'          => 'leave',
                        'clock_in_at'     => null,
                        'clock_out_at'    => null,
                        'marked_by'       => $markedBy,
                        'attendance_type' => 'manual',
                    ]);
                }
            }
        } else {
            // Revert staff attendance if leave is pending, rejected, cancelled, etc.
            foreach ($period as $date) {
                $dateStr = $date->format('Y-m-d');

                StaffAttendance::where('school_id', $schoolId)
                    ->where('staff_id', $staffId)
                    ->whereDate('date', $dateStr)
                    ->where('status', 'leave')
                    ->delete();
            }
        }
    }
}
