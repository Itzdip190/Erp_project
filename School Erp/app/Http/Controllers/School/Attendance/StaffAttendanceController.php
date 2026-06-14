<?php

namespace App\Http\Controllers\School\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Staff;
use App\Models\StaffAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class StaffAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $departments = Department::all();
        
        $departmentId = $request->get('department_id');
        $date = $request->get('date', date('Y-m-d'));

        $staffList = collect();
        $attendances = collect();

        if ($departmentId) {
            $staffList = Staff::where('department_id', $departmentId)
                ->where('is_active', true)
                ->get();

            $attendances = StaffAttendance::where('school_id', $schoolId)
                ->where('date', $date)
                ->get()
                ->keyBy('staff_id');
        }

        return view('school.attendance.staff.index', compact('departments', 'staffList', 'attendances', 'departmentId', 'date'));
    }

    public function store(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $request->validate([
            'date' => 'required|date|before_or_equal:today',
            'department_id' => 'required|exists:departments,id',
            'attendance' => 'required|array|min:1',
            'attendance.*.staff_id' => 'required|exists:staff,id',
            'attendance.*.status' => 'required|in:present,absent,late,half_day,holiday,leave',
        ]);

        $date = $request->date;
        $markedBy = auth()->id();

        DB::transaction(function () use ($schoolId, $request, $date, $markedBy) {
            foreach ($request->attendance as $item) {
                StaffAttendance::updateOrCreate(
                    [
                        'school_id' => $schoolId,
                        'staff_id' => $item['staff_id'],
                        'date' => $date,
                    ],
                    [
                        'status' => $item['status'],
                        'clock_in_at' => $item['status'] === 'present' ? '09:00:00' : null,
                        'clock_out_at' => $item['status'] === 'present' ? '17:00:00' : null,
                        'attendance_type' => 'manual',
                        'marked_by' => $markedBy,
                    ]
                );
            }
        });

        return redirect()->route('school.attendance.staff.index', ['department_id' => $request->department_id, 'date' => $date])
            ->with('success', 'Staff attendance marked successfully.');
    }

    public function report(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $departments = Department::all();

        $departmentId = $request->get('department_id');
        $startDate = $request->get('start_date', date('Y-m-01'));
        $endDate = $request->get('end_date', date('Y-m-t'));

        $staffList = collect();
        if ($departmentId) {
            $staffList = Staff::where('department_id', $departmentId)
                ->where('is_active', true)
                ->get();

            $records = StaffAttendance::where('school_id', $schoolId)
                ->whereBetween('date', [$startDate, $endDate])
                ->get()
                ->groupBy('staff_id');

            foreach ($staffList as $staff) {
                $staffRecords = $records->get($staff->id) ?? collect();
                $present = $staffRecords->whereIn('status', ['present', 'late'])->count();
                $absent = $staffRecords->where('status', 'absent')->count();
                $total = $present + $absent;

                $staff->attendance_summary = [
                    'present' => $present,
                    'absent' => $absent,
                    'percentage' => $total > 0 ? round(($present / $total) * 100, 1) : 0,
                ];
            }
        }

        // Export if requested
        if ($request->get('export') === 'excel' && $departmentId) {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $headers = ['Employee ID', 'Full Name', 'Department', 'Designation', 'Present Days', 'Absent Days', 'Attendance Rate'];
            $sheet->fromArray($headers, null, 'A1');

            $rowIdx = 2;
            foreach ($staffList as $staff) {
                $sheet->fromArray([
                    $staff->employee_id,
                    $staff->full_name,
                    $staff->department?->name,
                    $staff->designation?->name,
                    $staff->attendance_summary['present'],
                    $staff->attendance_summary['absent'],
                    $staff->attendance_summary['percentage'] . '%'
                ], null, 'A' . $rowIdx++);
            }

            $writer = new Xlsx($spreadsheet);

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, 'staff_attendance_report.xlsx', [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        }

        return view('school.attendance.staff.report', compact('departments', 'staffList', 'departmentId', 'startDate', 'endDate'));
    }
}
