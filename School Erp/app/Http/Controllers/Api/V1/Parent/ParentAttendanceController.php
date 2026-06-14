<?php

namespace App\Http\Controllers\Api\V1\Parent;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentAttendance;
use Illuminate\Http\Request;

class ParentAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'month' => 'nullable|integer|between:1,12',
            'year' => 'nullable|integer',
        ]);

        $studentId = $request->student_id;
        $student = Student::findOrFail($studentId);

        // Security check
        if ($student->guardian_email !== auth()->user()->email && $student->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.',
            ], 403);
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

            $calendar[] = [
                'day' => $day,
                'status' => $status,
                'remark' => $record?->remark,
            ];
        }

        $totalMarked = $present + $absent + $late + $leave;
        
        return response()->json([
            'success' => true,
            'data' => [
                'calendar' => $calendar,
                'summary' => [
                    'total' => $totalMarked,
                    'present' => $present,
                    'absent' => $absent,
                    'late' => $late,
                    'leave' => $leave,
                    'percentage' => $totalMarked > 0 ? round((($present + $late) / $totalMarked) * 100, 1) : 0,
                ]
            ]
        ]);
    }
}
