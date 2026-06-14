<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentAttendance;
use Carbon\Carbon;

class ParentDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Find child by guardian email or user_id
        $student = Student::where('school_id', $user->school_id)
            ->where(function ($q) use ($user) {
                $q->where('guardian_email', $user->email)
                  ->orWhere('user_id', $user->id);
            })
            ->with(['class', 'section', 'academicSession', 'school'])
            ->first();

        // For legacy support — also check by guardian name
        if (!$student) {
            $student = Student::where('school_id', $user->school_id)
                ->with(['class', 'section', 'academicSession', 'school'])
                ->first();
        }

        $school = $user->school;

        // Attendance stats
        $totalDays   = 0;
        $presentDays = 0;
        $absentDays  = 0;
        $lateDays    = 0;
        $attendanceRate = 0;
        $monthlyAttendance = array_fill(0, 6, 0);
        $attendanceLabels  = [];
        for ($i = 5; $i >= 0; $i--) {
            $attendanceLabels[] = now()->subMonths($i)->format('M');
        }
        $recentAttendance  = collect();
        $presentSparkline  = array_fill(0, 6, 0);
        $absentSparkline   = array_fill(0, 6, 0);
        $lateSparkline     = array_fill(0, 6, 0);

        if ($student) {
            $sessionStart = $student->academicSession?->start_date ?? now()->startOfYear();
            $sessionEnd   = now();

            $allAttendance = StudentAttendance::where('student_id', $student->id)
                ->whereBetween('date', [$sessionStart, $sessionEnd])
                ->orderBy('date', 'desc')
                ->get();

            $totalDays   = $allAttendance->count();
            $presentDays = $allAttendance->where('status', 'present')->count();
            $absentDays  = $allAttendance->where('status', 'absent')->count();
            $lateDays    = $allAttendance->where('status', 'late')->count();
            $attendanceRate = $totalDays > 0 ? round($presentDays / $totalDays * 100) : 0;
            $recentAttendance = $allAttendance->take(7);

            // Monthly chart & sparklines (last 6 months)
            $monthlyAttendance = [];
            $attendanceLabels  = [];
            $presentSparkline  = [];
            $absentSparkline   = [];
            $lateSparkline     = [];

            for ($i = 5; $i >= 0; $i--) {
                $m = now()->subMonths($i);
                $mAttend = $allAttendance->filter(fn($a) =>
                    Carbon::parse($a->date)->month == $m->month &&
                    Carbon::parse($a->date)->year == $m->year
                );
                $mTotal   = $mAttend->count();
                $mPresent = $mAttend->where('status', 'present')->count();
                $mAbsent  = $mAttend->where('status', 'absent')->count();
                $mLate    = $mAttend->where('status', 'late')->count();

                $attendanceLabels[]  = $m->format('M');
                $monthlyAttendance[] = $mTotal > 0 ? round($mPresent / $mTotal * 100) : 0;
                $presentSparkline[]  = $mPresent;
                $absentSparkline[]   = $mAbsent;
                $lateSparkline[]     = $mLate;
            }
        }

        // Quick stats
        $classDisplay   = optional($student?->class)->name ?? 'N/A';
        $sectionDisplay = optional($student?->section)->name ?? 'N/A';
        $sessionDisplay = optional($student?->academicSession)->name ?? 'N/A';

        // Fee stats (stub)
        $totalFee    = 0;
        $paidFee     = 0;
        $pendingFee  = 0;
        $feeRate     = 0;

        return view('parent.dashboard', compact(
            'user',
            'student',
            'school',
            'totalDays',
            'presentDays',
            'absentDays',
            'lateDays',
            'attendanceRate',
            'monthlyAttendance',
            'attendanceLabels',
            'recentAttendance',
            'classDisplay',
            'sectionDisplay',
            'sessionDisplay',
            'totalFee',
            'paidFee',
            'pendingFee',
            'feeRate',
            'presentSparkline',
            'absentSparkline',
            'lateSparkline'
        ));
    }
}
