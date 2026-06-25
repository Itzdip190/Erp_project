<?php

namespace App\Http\Controllers\School\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Staff;
use App\Models\StaffAttendance;
use App\Models\AcademicSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class StaffAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $departments = Department::where('school_id', $schoolId)->get();

        // Safe Date Parsing
        $dateInput = $request->get('date');
        if ($dateInput) {
            if (strpos($dateInput, '/') !== false) {
                try {
                    $date = \Carbon\Carbon::createFromFormat('d/m/Y', $dateInput)->format('Y-m-d');
                } catch (\Exception $e) {
                    $date = date('Y-m-d');
                }
            } else {
                try {
                    $date = \Carbon\Carbon::parse($dateInput)->format('Y-m-d');
                } catch (\Exception $e) {
                    $date = date('Y-m-d');
                }
            }
        } else {
            $date = date('Y-m-d');
        }

        $departmentId = $request->get('department_id');
        $staffType = $request->get('staff_type', 'All staffs');
        $status = $request->get('status');
        $search = $request->get('search');

        // 1. Calculate dynamic counts based on the date for ALL active staff
        $allActiveStaff = Staff::where('school_id', $schoolId)->where('is_active', true)->get();
        $dateAttendances = StaffAttendance::where('school_id', $schoolId)
            ->whereDate('date', $date)
            ->get()
            ->keyBy('staff_id');

        $presentCount = 0;
        $absentCount = 0;
        $halfDayCount = 0;
        $leaveCount = 0;
        $customLeavesCount = 0;
        $notMarkedCount = 0;

        foreach ($allActiveStaff as $staff) {
            $att = $dateAttendances->get($staff->id);
            if (!$att) {
                $notMarkedCount++;
            } else {
                $stVal = strtolower($att->status);
                if ($stVal === 'present') {
                    $presentCount++;
                } elseif ($stVal === 'absent') {
                    $absentCount++;
                } elseif ($stVal === 'half_day') {
                    $halfDayCount++;
                } elseif ($stVal === 'leave') {
                    $leaveCount++;
                } elseif ($stVal === 'late' || $stVal === 'holiday') {
                    $customLeavesCount++;
                } else {
                    $notMarkedCount++;
                }
            }
        }

        // 2. Build the query to load the staff table with filters
        $query = Staff::where('school_id', $schoolId)
            ->where('is_active', true)
            ->with(['department', 'designation']);

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        // Filter by Staff Type using designatory rules
        if ($staffType && $staffType !== 'All staffs') {
            $query->where(function($q) use ($staffType) {
                if ($staffType === 'Teaching') {
                    $q->whereHas('designation', function($d) {
                        $d->where('name', 'like', '%teacher%')
                          ->orWhere('name', 'like', '%principal%');
                    })->orWhereHas('user', function($u) {
                        $u->where('role', 'teacher');
                    });
                } elseif ($staffType === 'Driver/Supporting staff' || $staffType === 'Driver') {
                    $q->whereHas('designation', function($d) {
                        $d->where('name', 'like', '%driver%')
                          ->orWhere('name', 'like', '%conductor%')
                          ->orWhere('name', 'like', '%peon%')
                          ->orWhere('name', 'like', '%supporting%')
                          ->orWhere('name', 'like', '%helper%');
                    });
                } elseif ($staffType === 'Admin') {
                    $q->whereHas('designation', function($d) {
                        $d->where('name', 'like', '%admin%')
                          ->orWhere('name', 'like', '%director%')
                          ->orWhere('name', 'like', '%manager%');
                    })->orWhereHas('user', function($u) {
                        $u->where('role', 'admin')->orWhere('role', 'school_admin');
                    });
                } elseif ($staffType === 'Non Teaching' || $staffType === 'Non-Teaching') {
                    $q->whereNot(function($qn) {
                        $qn->whereHas('designation', function($d) {
                            $d->where('name', 'like', '%teacher%')
                              ->orWhere('name', 'like', '%principal%')
                              ->orWhere('name', 'like', '%admin%')
                              ->orWhere('name', 'like', '%director%')
                              ->orWhere('name', 'like', '%manager%')
                              ->orWhere('name', 'like', '%driver%')
                              ->orWhere('name', 'like', '%conductor%')
                              ->orWhere('name', 'like', '%peon%')
                              ->orWhere('name', 'like', '%supporting%')
                              ->orWhere('name', 'like', '%helper%');
                        });
                    });
                }
            });
        }

        // Filter by current attendance status on this specific date
        if ($status && $status !== 'Select Status') {
            if ($status === 'Not Marked') {
                $query->whereDoesntHave('attendances', function($q) use ($date) {
                    $q->whereDate('date', $date);
                });
            } else {
                $dbStatus = strtolower(str_replace(' ', '_', $status));
                if ($dbStatus === 'custom_leaves') {
                    $dbStatus = 'late';
                }
                $query->whereHas('attendances', function($q) use ($date, $dbStatus) {
                    $q->whereDate('date', $date)->where('status', $dbStatus);
                });
            }
        }

        $staffList = $query->orderBy('first_name')->get();

        // 3. Calculate historical attendance percentage for each staff
        $allStaffIds = $staffList->pluck('id')->toArray();
        $historyGroups = StaffAttendance::where('school_id', $schoolId)
            ->whereIn('staff_id', $allStaffIds)
            ->get()
            ->groupBy('staff_id');

        foreach ($staffList as $st) {
            $history = $historyGroups->get($st->id) ?? collect();
            $presentDays = $history->whereIn('status', ['present', 'late', 'holiday'])->count();
            $totalDays = $history->count();
            $st->attendance_percentage = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 2) : null;
        }

        $mode = $request->get('mode', 'view');

        return view('school.attendance.staff.index', compact(
            'departments', 'staffList', 'dateAttendances', 'date', 
            'departmentId', 'staffType', 'status', 'search',
            'presentCount', 'absentCount', 'halfDayCount', 'leaveCount', 'customLeavesCount', 'notMarkedCount',
            'mode'
        ));
    }

    public function store(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $request->validate([
            'date' => 'required|date|before_or_equal:today',
            'attendance' => 'required|array|min:1',
            'attendance.*.staff_id' => 'required|exists:staff,id',
        ]);

        $date = $request->date;
        $markedBy = auth()->id();

        DB::transaction(function () use ($schoolId, $request, $date, $markedBy) {
            foreach ($request->attendance as $item) {
                $status = isset($item['status']) ? $item['status'] : 'not_marked';

                if (empty($status) || $status === 'not_marked') {
                    StaffAttendance::where('school_id', $schoolId)
                        ->where('staff_id', $item['staff_id'])
                        ->whereDate('date', $date)
                        ->delete();
                    continue;
                }

                // Map status name to DB value
                $dbStatus = strtolower(str_replace(' ', '_', $status));
                if ($dbStatus === 'custom_leaves' || $dbStatus === 'custom_leave') {
                    $dbStatus = 'late';
                }

                // Format check-in / check-out times
                $clockIn = null;
                if (!empty($item['clock_in_at'])) {
                    $clockIn = date('H:i:s', strtotime($item['clock_in_at']));
                }

                $clockOut = null;
                if (!empty($item['clock_out_at'])) {
                    $clockOut = date('H:i:s', strtotime($item['clock_out_at']));
                }

                StaffAttendance::updateOrCreate(
                    [
                        'school_id' => $schoolId,
                        'staff_id'  => $item['staff_id'],
                        'date'      => $date,
                    ],
                    [
                        'status'          => $dbStatus,
                        'clock_in_at'     => $clockIn,
                        'clock_out_at'    => $clockOut,
                        'attendance_type' => 'manual',
                        'marked_by'       => $markedBy,
                    ]
                );
            }
        });

        return redirect()->route('school.attendance.staff.index', [
            'department_id' => $request->department_id, 
            'date' => $date,
            'staff_type' => $request->staff_type,
            'status' => $request->status,
            'search' => $request->search
        ])->with('success', 'Staff attendance marked successfully.');
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

    public function bulkAttendance(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $departments = Department::where('school_id', $schoolId)->get();
        
        $session = AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first();
        $academicYearText = $session 
            ? "Academic Year: " . $session->start_date->format('d-m-Y') . " to " . $session->end_date->format('d-m-Y')
            : "Academic Year: 01-04-2025 to 31-03-2026";

        // From/To Date Parsing
        $fromDateInput = $request->get('from_date', date('Y-m-d'));
        $toDateInput = $request->get('to_date', date('Y-m-d'));

        // Parse From Date
        if (strpos($fromDateInput, '/') !== false) {
            try {
                $fromDate = \Carbon\Carbon::createFromFormat('d/m/Y', $fromDateInput)->format('Y-m-d');
            } catch (\Exception $e) {
                $fromDate = date('Y-m-d');
            }
        } else {
            try {
                $fromDate = \Carbon\Carbon::parse($fromDateInput)->format('Y-m-d');
            } catch (\Exception $e) {
                $fromDate = date('Y-m-d');
            }
        }

        // Parse To Date
        if (strpos($toDateInput, '/') !== false) {
            try {
                $toDate = \Carbon\Carbon::createFromFormat('d/m/Y', $toDateInput)->format('Y-m-d');
            } catch (\Exception $e) {
                $toDate = date('Y-m-d');
            }
        } else {
            try {
                $toDate = \Carbon\Carbon::parse($toDateInput)->format('Y-m-d');
            } catch (\Exception $e) {
                $toDate = date('Y-m-d');
            }
        }

        // Calculate Date Range Info
        $startCarbon = \Carbon\Carbon::parse($fromDate);
        $endCarbon = \Carbon\Carbon::parse($toDate);

        // Safety: Limit range to 31 days
        $daysDiff = $startCarbon->diffInDays($endCarbon) + 1;
        if ($daysDiff > 31) {
            $endCarbon = $startCarbon->copy()->addDays(30);
            $toDate = $endCarbon->format('Y-m-d');
            $daysDiff = 31;
            session()->flash('warning', 'The maximum allowed date range is 31 days. We adjusted your end date.');
        }

        $totalDays = $daysDiff;
        $weekdays = 0;
        $weekends = 0;
        $datesInRange = [];

        $tempDate = $startCarbon->copy();
        while ($tempDate->lte($endCarbon)) {
            $datesInRange[] = $tempDate->copy();
            if ($tempDate->isWeekend()) {
                $weekends++;
            } else {
                $weekdays++;
            }
            $tempDate->addDay();
        }

        $departmentId = $request->get('department_id');
        $staffType = $request->get('staff_type', 'Teaching');
        $search = $request->get('search');

        // Load staff members based on staff type and other filters
        $query = Staff::where('school_id', $schoolId)
            ->where('is_active', true)
            ->with(['department', 'designation']);

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        // Staff Type filter
        if ($staffType) {
            $query->where(function($q) use ($staffType) {
                if ($staffType === 'Teaching') {
                    $q->whereHas('designation', function($d) {
                        $d->where('name', 'like', '%teacher%')
                          ->orWhere('name', 'like', '%principal%');
                    })->orWhereHas('user', function($u) {
                        $u->where('role', 'teacher');
                    });
                } elseif ($staffType === 'Driver/Supporting staff' || $staffType === 'Driver') {
                    $q->whereHas('designation', function($d) {
                        $d->where('name', 'like', '%driver%')
                          ->orWhere('name', 'like', '%conductor%')
                          ->orWhere('name', 'like', '%peon%')
                          ->orWhere('name', 'like', '%supporting%')
                          ->orWhere('name', 'like', '%helper%');
                    });
                } elseif ($staffType === 'Admin') {
                    $q->whereHas('designation', function($d) {
                        $d->where('name', 'like', '%admin%')
                          ->orWhere('name', 'like', '%director%')
                          ->orWhere('name', 'like', '%manager%');
                    })->orWhereHas('user', function($u) {
                        $u->where('role', 'admin')->orWhere('role', 'school_admin');
                    });
                } elseif ($staffType === 'Non Teaching' || $staffType === 'Non-Teaching') {
                    $q->whereNot(function($qn) {
                        $qn->whereHas('designation', function($d) {
                            $d->where('name', 'like', '%teacher%')
                              ->orWhere('name', 'like', '%principal%')
                              ->orWhere('name', 'like', '%admin%')
                              ->orWhere('name', 'like', '%director%')
                              ->orWhere('name', 'like', '%manager%')
                              ->orWhere('name', 'like', '%driver%')
                              ->orWhere('name', 'like', '%conductor%')
                              ->orWhere('name', 'like', '%peon%')
                              ->orWhere('name', 'like', '%supporting%')
                              ->orWhere('name', 'like', '%helper%');
                        });
                    });
                }
            });
        }

        $staffMembers = $query->orderBy('first_name')->get();

        // Fetch all attendance records for these staff in the date range
        $staffIds = $staffMembers->pluck('id')->toArray();
        $attendanceRecords = StaffAttendance::where('school_id', $schoolId)
            ->whereIn('staff_id', $staffIds)
            ->whereBetween('date', [$fromDate, $toDate])
            ->get()
            ->groupBy('staff_id');

        // Build a matrix mapping [staff_id][date_string] => StaffAttendance model
        $attendanceMatrix = [];
        foreach ($staffMembers as $staff) {
            $attendanceMatrix[$staff->id] = [];
            $records = $attendanceRecords->get($staff->id) ?? collect();
            foreach ($records as $rec) {
                // Carbon date to string
                $dateStr = $rec->date instanceof \Carbon\Carbon ? $rec->date->format('Y-m-d') : substr($rec->date, 0, 10);
                $attendanceMatrix[$staff->id][$dateStr] = $rec;
            }
        }

        return view('school.staff.bulk_attendance', compact(
            'departments', 'staffMembers', 'attendanceMatrix', 'fromDate', 'toDate', 'datesInRange',
            'totalDays', 'weekdays', 'weekends', 'academicYearText', 'departmentId', 'staffType', 'search'
        ));
    }

    public function saveBulkAttendance(Request $request)
    {
        $request->validate([
            'attendance' => 'required|array',
        ]);

        $schoolId = auth()->user()->school_id;
        $markedBy = auth()->id();

        DB::transaction(function () use ($schoolId, $request, $markedBy) {
            foreach ($request->attendance as $staffId => $dates) {
                // Verify staff exists in this school
                $staff = Staff::where('school_id', $schoolId)->findOrFail($staffId);

                foreach ($dates as $dateStr => $data) {
                    $status = isset($data['status']) ? $data['status'] : null;

                    // If status is empty/null, we delete the attendance record or keep it not marked
                    if (empty($status) || $status === 'not_marked') {
                        StaffAttendance::where('school_id', $schoolId)
                            ->where('staff_id', $staffId)
                            ->whereDate('date', $dateStr)
                            ->delete();
                        continue;
                    }

                    // Map status name to DB value
                    $dbStatus = strtolower(str_replace(' ', '_', $status));
                    if ($dbStatus === 'custom_leaves' || $dbStatus === 'custom_leave') {
                        $dbStatus = 'late';
                    }

                    // Format check-in / check-out times
                    $clockIn = null;
                    if (!empty($data['clock_in_at'])) {
                        $clockIn = date('H:i:s', strtotime($data['clock_in_at']));
                    }

                    $clockOut = null;
                    if (!empty($data['clock_out_at'])) {
                        $clockOut = date('H:i:s', strtotime($data['clock_out_at']));
                    }

                    StaffAttendance::updateOrCreate(
                        [
                            'school_id' => $schoolId,
                            'staff_id'  => $staffId,
                            'date'      => $dateStr,
                        ],
                        [
                            'status'          => $dbStatus,
                            'clock_in_at'     => $clockIn,
                            'clock_out_at'    => $clockOut,
                            'attendance_type' => 'manual',
                            'marked_by'       => $markedBy,
                        ]
                    );
                }
            }
        });

        return back()->with('success', 'Staff bulk attendance saved successfully!');
    }
}
