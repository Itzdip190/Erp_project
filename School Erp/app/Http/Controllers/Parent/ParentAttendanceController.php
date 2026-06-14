<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentAttendance;
use Illuminate\Http\Request;

class ParentAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'student_id' => 'nullable|exists:students,id',
            'month' => 'nullable|integer|between:1,12',
            'year' => 'nullable|integer',
        ]);

        $user = auth()->user();
        $studentId = $request->student_id;

        if (!$studentId) {
            // Find child by guardian email or user_id
            $student = Student::where('school_id', $user->school_id)
                ->where(function ($q) use ($user) {
                    $q->where('guardian_email', $user->email)
                      ->orWhere('user_id', $user->id);
                })
                ->first();

            // Fallback for legacy support
            if (!$student) {
                $student = Student::where('school_id', $user->school_id)->first();
            }

            if (!$student) {
                abort(404, 'Student not found');
            }
            $studentId = $student->id;
        } else {
            $student = Student::findOrFail($studentId);

            // Security check: ensure student belongs to user (as guardian or student user themselves)
            if ($student->guardian_email !== $user->email && $student->user_id !== $user->id) {
                if ($student->school_id !== $user->school_id) {
                    abort(403, 'Access denied');
                }
            }
        }

        $month = (int) $request->get('month', date('m'));
        $year = (int) $request->get('year', date('Y'));

        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        $records = StudentAttendance::where('student_id', $studentId)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get()
            ->keyBy(function ($item) {
                return (int) $item->date->format('d');
            });

        $calendar = [];
        $present = 0;
        $absent = 0;
        $late = 0;
        $leave = 0;

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $record = $records->get($day);
            $status = $record ? $record->status : 'none';

            if ($status === 'present') {
                $present++;
            } elseif ($status === 'absent') {
                $absent++;
            } elseif ($status === 'late') {
                $late++;
            } elseif ($status === 'leave') {
                $leave++;
            }

            $calendar[$day] = [
                'status' => $status,
                'remark' => $record?->remark,
            ];
        }

        $totalMarked = $present + $absent + $late + $leave;
        $summary = [
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'leave' => $leave,
            'total' => $totalMarked,
            'percentage' => $totalMarked > 0 ? round((($present + $late) / $totalMarked) * 100, 1) : 0,
        ];

        return view('parent.attendance.index', compact('calendar', 'summary', 'student', 'month', 'year'));
    }
}
