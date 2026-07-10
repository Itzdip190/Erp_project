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

        $academicSessions = AcademicSession::all();
        $sessionId = $request->get('academic_session_id');
        if ($sessionId) {
            $session = AcademicSession::find($sessionId);
        } else {
            $session = AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first()
                ?? AcademicSession::where('school_id', $schoolId)->first();
        }
        $sessionId = $session ? $session->id : null;
            
        $totalWorkingDays = 0;
        if ($session) {
            $start = \Carbon\Carbon::parse($session->start_date);
            $end = \Carbon\Carbon::parse($session->end_date);
            $days = $start->diffInDays($end) + 1;
            $sundays = 0;
            $temp = $start->copy();
            while ($temp->dayOfWeek !== \Carbon\Carbon::SUNDAY && $temp->lte($end)) {
                $temp->addDay();
            }
            if ($temp->lte($end)) {
                $sundays = 1 + floor($temp->diffInDays($end) / 7);
            }
            $totalWorkingDays = $days - $sundays;
        }

        foreach ($staffList as $st) {
            $history = $historyGroups->get($st->id) ?? collect();
            $presentDays = $history->whereIn('status', ['present', 'late', 'holiday'])->count()
                + ($history->where('status', 'half_day')->count() * 0.5);
            $st->attendance_percentage = $totalWorkingDays > 0 ? round(($presentDays / $totalWorkingDays) * 100, 2) : 0;
        }

        $mode = $request->get('mode', 'view');

        return view('school.attendance.staff.index', compact(
            'departments', 'staffList', 'dateAttendances', 'date', 
            'departmentId', 'staffType', 'status', 'search',
            'presentCount', 'absentCount', 'halfDayCount', 'leaveCount', 'customLeavesCount', 'notMarkedCount',
            'mode', 'academicSessions', 'sessionId'
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
                if ($dbStatus === 'halfday') {
                    $dbStatus = 'half_day';
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
        
        $academicSessions = AcademicSession::all();
        $sessionId = $request->get('academic_session_id');
        if ($sessionId) {
            $session = AcademicSession::find($sessionId);
        } else {
            $session = AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first()
                ?? AcademicSession::where('school_id', $schoolId)->first();
        }
        $sessionId = $session ? $session->id : null;
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

        // No day limit — allow any range
        $daysDiff = $startCarbon->diffInDays($endCarbon) + 1;

        $totalDays = $daysDiff;
        $weekdays  = 0; // Mon–Sat (working)
        $weekends  = 0; // Sun only (holiday)
        $datesInRange = [];

        $tempDate = $startCarbon->copy();
        while ($tempDate->lte($endCarbon)) {
            $datesInRange[] = $tempDate->copy();
            // Sunday (dayOfWeek === 0) is holiday; Saturday is working
            if ($tempDate->dayOfWeek === 0) {
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
            'totalDays', 'weekdays', 'weekends', 'academicYearText', 'departmentId', 'staffType', 'search',
            'academicSessions', 'sessionId'
        ));
    }

    public function saveBulkAttendance(Request $request)
    {
        $request->validate([
            'attendance' => 'required|array',
        ]);

        $schoolId = auth()->user()->school_id;
        $markedBy = auth()->id();

        $staffIds = array_keys($request->attendance);
        $staffMembers = Staff::where('school_id', $schoolId)
            ->whereIn('id', $staffIds)
            ->get()
            ->keyBy('id');

        $dates = [];
        foreach ($request->attendance as $stId => $dateArr) {
            $dates = array_unique(array_merge($dates, array_keys($dateArr)));
        }

        DB::transaction(function () use ($schoolId, $request, $markedBy, $staffIds, $dates, $staffMembers) {
            // Delete existing records in bulk to avoid updateOrCreate checks
            StaffAttendance::where('school_id', $schoolId)
                ->whereIn('staff_id', $staffIds)
                ->whereIn('date', $dates)
                ->delete();

            $insertData = [];
            foreach ($request->attendance as $staffId => $dateArr) {
                if (!isset($staffMembers[$staffId])) {
                    continue;
                }

                foreach ($dateArr as $dateStr => $data) {
                    $status = isset($data['status']) ? $data['status'] : null;

                    if (empty($status) || $status === 'not_marked') {
                        continue;
                    }

                    $dbStatus = strtolower(str_replace(' ', '_', $status));
                    if ($dbStatus === 'custom_leaves' || $dbStatus === 'custom_leave') {
                        $dbStatus = 'late';
                    }
                    if ($dbStatus === 'halfday') {
                        $dbStatus = 'half_day';
                    }

                    $clockIn = null;
                    if (!empty($data['clock_in_at'])) {
                        $clockIn = date('H:i:s', strtotime($data['clock_in_at']));
                    }

                    $clockOut = null;
                    if (!empty($data['clock_out_at'])) {
                        $clockOut = date('H:i:s', strtotime($data['clock_out_at']));
                    }

                    $insertData[] = [
                        'school_id' => $schoolId,
                        'staff_id'  => $staffId,
                        'date'      => $dateStr . ' 00:00:00',
                        'status'          => $dbStatus,
                        'clock_in_at'     => $clockIn,
                        'clock_out_at'    => $clockOut,
                        'attendance_type' => 'manual',
                        'marked_by'       => $markedBy,
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ];
                }
            }

            if (!empty($insertData)) {
                foreach (array_chunk($insertData, 500) as $chunk) {
                    StaffAttendance::insert($chunk);
                }
            }
        });

        return back()->with('success', 'Staff bulk attendance saved successfully!');
    }

    public function saveSliderBulkAttendance(Request $request)
    {
        $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date',
            'working_days' => 'required|integer|min:1',
            'present_days' => 'required|integer|min:0',
            'apply_type' => 'required|string|in:all,department,staff_type',
            'department_id' => 'required_if:apply_type,department|nullable|integer',
            'staff_type' => 'required_if:apply_type,staff_type|nullable|string',
        ]);

        $schoolId = auth()->user()->school_id;
        $markedBy = auth()->id();
        $fromDate = $request->from_date;
        $workingDays = (int)$request->working_days;
        $presentDays = (int)$request->present_days;
        $applyType = $request->apply_type;
        $departmentId = $request->department_id;
        $staffType = $request->staff_type;

        // Generate exactly W working days starting from from_date, skipping Sundays only (Saturday is a working day)
        $dates = [];
        $current = \Carbon\Carbon::parse($fromDate);
        while (count($dates) < $workingDays) {
            if ($current->dayOfWeek !== 0) {
                $dates[] = $current->toDateString();
            }
            $current->addDay();
        }

        // Get target staff
        $query = Staff::where('school_id', $schoolId)->where('is_active', true);
        
        if ($applyType === 'department') {
            $query->where('department_id', $departmentId);
        } elseif ($applyType === 'staff_type') {
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
        }
        $staffMembers = $query->get();

        if ($staffMembers->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No active staff members found matching criteria.']);
        }

        DB::transaction(function () use ($schoolId, $staffMembers, $dates, $presentDays, $markedBy) {
            $staffIds = $staffMembers->pluck('id')->toArray();
            
            // Delete existing records in bulk
            StaffAttendance::where('school_id', $schoolId)
                ->whereIn('staff_id', $staffIds)
                ->whereIn('date', $dates)
                ->delete();

            $insertData = [];
            foreach ($staffMembers as $staff) {
                foreach ($dates as $index => $dateStr) {
                    $status = ($index < $presentDays) ? 'present' : 'absent';
                    $clockIn = ($status === 'present') ? '09:00:00' : null;
                    $clockOut = ($status === 'present') ? '17:00:00' : null;

                    $insertData[] = [
                        'school_id' => $schoolId,
                        'staff_id'  => $staff->id,
                        'date'      => $dateStr . ' 00:00:00',
                        'status'          => $status,
                        'clock_in_at'     => $clockIn,
                        'clock_out_at'    => $clockOut,
                        'attendance_type' => 'manual',
                        'marked_by'       => $markedBy,
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ];
                }
            }

            if (!empty($insertData)) {
                foreach (array_chunk($insertData, 500) as $chunk) {
                    StaffAttendance::insert($chunk);
                }
            }
        });

        return response()->json(['success' => true]);
    }

    public function export(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $type = $request->get('type', 'daily'); // 'daily' or 'monthly'
        $date = $request->get('date', date('Y-m-d'));
        $departmentId = $request->get('department_id');
        $staffType = $request->get('staff_type', 'All staffs');
        $status = $request->get('status');
        $search = $request->get('search');
        
        $sessionId = $request->get('academic_session_id');
        if ($sessionId) {
            $session = AcademicSession::find($sessionId);
        } else {
            $session = AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first()
                ?? AcademicSession::where('school_id', $schoolId)->first();
        }

        if (!$session) {
            return back()->with('error', 'Academic Session not found.');
        }
        
        // Calculate academic year total working days (excluding Sundays)
        $start = \Carbon\Carbon::parse($session->start_date);
        $end = \Carbon\Carbon::parse($session->end_date);
        $days = $start->diffInDays($end) + 1;
        $sundays = 0;
        $temp = $start->copy();
        while ($temp->dayOfWeek !== \Carbon\Carbon::SUNDAY && $temp->lte($end)) {
            $temp->addDay();
        }
        if ($temp->lte($end)) {
            $sundays = 1 + floor($temp->diffInDays($end) / 7);
        }
        $totalWorkingDays = $days - $sundays;

        // Build Query
        $query = Staff::with(['department', 'designation'])->where('school_id', $schoolId)->where('is_active', true);

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
        $allStaffIds = $staffList->pluck('id')->toArray();
        
        $attendanceStats = StaffAttendance::where('school_id', $schoolId)
            ->whereIn('staff_id', $allStaffIds)
            ->get()
            ->groupBy('staff_id');

        $titleStyle = [
            'font' => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF01242E']
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ]
        ];

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF023C4D']
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FFFFFFFF']
                ]
            ]
        ];

        $dataStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FFCBD5E1']
                ]
            ],
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ]
        ];

        if ($type === 'daily') {
            $dateAttendances = StaffAttendance::where('school_id', $schoolId)
                ->whereDate('date', $date)
                ->get()
                ->keyBy('staff_id');

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Daily Register');

            $sheet->mergeCells('A1:I1');
            $sheet->setCellValue('A1', 'Staff Daily Attendance Register (Date: ' . date('d-m-Y', strtotime($date)) . ')');
            $sheet->getRowDimension(1)->setRowHeight(36);
            $sheet->getStyle('A1:I1')->applyFromArray($titleStyle);

            $headers = ['Employee ID', 'Staff Name', 'Department', 'Designation', 'Attendance Status', 'Check In', 'Check Out', 'Remarks', 'Attendance %'];
            $sheet->fromArray($headers, null, 'A2');
            $sheet->getRowDimension(2)->setRowHeight(22);
            $sheet->getStyle('A2:I2')->applyFromArray($headerStyle);

            $rowIdx = 3;
            foreach ($staffList as $st) {
                $att = $dateAttendances->get($st->id);
                $statusText = $att ? ucfirst(str_replace('_', ' ', $att->status)) : 'Not Marked';
                $checkIn = $att && $att->clock_in_at ? date('h:i A', strtotime($att->clock_in_at)) : '—';
                $checkOut = $att && $att->clock_out_at ? date('h:i A', strtotime($att->clock_out_at)) : '—';
                $remark = $att ? $att->remark : '';

                $history = $attendanceStats->get($st->id) ?? collect();
                $presentDays = $history->whereIn('status', ['present', 'late', 'holiday'])->count()
                    + ($history->where('status', 'half_day')->count() * 0.5);
                $pct = $totalWorkingDays > 0 ? round(($presentDays / $totalWorkingDays) * 100, 2) : 0;

                $sheet->fromArray([
                    $st->employee_id,
                    $st->full_name,
                    $st->department?->name ?? '—',
                    $st->designation?->name ?? '—',
                    $statusText,
                    $checkIn,
                    $checkOut,
                    $remark,
                    $pct . '%'
                ], null, 'A' . $rowIdx);

                $sheet->getRowDimension($rowIdx)->setRowHeight(20);
                $sheet->getStyle('A' . $rowIdx . ':I' . $rowIdx)->applyFromArray($dataStyle);
                $rowIdx++;
            }

            foreach (range('A', 'I') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $writer = new Xlsx($spreadsheet);
            $filename = 'Staff_Attendance_' . $date . '.xlsx';
            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
            
        } else {
            // Monthly Summary
            $month = $request->get('month', date('m'));
            $year = $request->get('year', date('Y'));
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

            $monthlyRecords = StaffAttendance::where('school_id', $schoolId)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->get()
                ->groupBy('staff_id');

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Monthly Summary');

            $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($daysInMonth + 7);

            $sheet->mergeCells('A1:' . $lastCol . '1');
            $sheet->setCellValue('A1', 'Staff Monthly Attendance Summary (' . date('F Y', mktime(0,0,0,$month,1,$year)) . ')');
            $sheet->getRowDimension(1)->setRowHeight(36);
            $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray($titleStyle);

            $headers = ['Employee ID', 'Staff Name', 'Department', 'Designation'];
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $headers[] = $d;
            }
            $headers[] = 'Present';
            $headers[] = 'Absent';
            $headers[] = 'Attendance %';

            $sheet->fromArray($headers, null, 'A2');
            $sheet->getRowDimension(2)->setRowHeight(22);
            $sheet->getStyle('A2:' . $lastCol . '2')->applyFromArray($headerStyle);

            $rowIdx = 3;
            foreach ($staffList as $st) {
                $stRecords = $monthlyRecords->get($st->id) ?? collect();
                $presentCountVal = 0;
                $absentCountVal = 0;

                $row = [
                    $st->employee_id,
                    $st->full_name,
                    $st->department?->name ?? '—',
                    $st->designation?->name ?? '—',
                ];

                for ($d = 1; $d <= $daysInMonth; $d++) {
                    $dateString = sprintf('%s-%02s-%02s', $year, $month, $d);
                    $rec = $stRecords->first(fn($r) => $r->date->format('Y-m-d') === $dateString);

                    $statusText = '—';
                    if ($rec) {
                        $status = $rec->status;
                        if ($status === 'present' || $status === 'late' || $status === 'holiday') {
                            $presentCountVal++;
                            $statusText = 'P';
                        } elseif ($status === 'absent') {
                            $absentCountVal++;
                            $statusText = 'A';
                        } elseif ($status === 'half_day') {
                            $presentCountVal += 0.5;
                            $statusText = 'HD';
                        } elseif ($status === 'leave') {
                            $statusText = 'L';
                        }
                    }
                    $row[] = $statusText;
                }

                $history = $attendanceStats->get($st->id) ?? collect();
                $presentDays = $history->whereIn('status', ['present', 'late', 'holiday'])->count()
                    + ($history->where('status', 'half_day')->count() * 0.5);
                $pct = $totalWorkingDays > 0 ? round(($presentDays / $totalWorkingDays) * 100, 2) : 0;

                $row[] = $presentCountVal;
                $row[] = $absentCountVal;
                $row[] = $pct . '%';

                $sheet->fromArray($row, null, 'A' . $rowIdx);
                $sheet->getRowDimension($rowIdx)->setRowHeight(20);
                $sheet->getStyle('A' . $rowIdx . ':' . $lastCol . $rowIdx)->applyFromArray($dataStyle);
                $rowIdx++;
            }

            for ($i = 1; $i <= ($daysInMonth + 7); $i++) {
                $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $writer = new Xlsx($spreadsheet);
            $filename = 'Staff_Monthly_Attendance_' . $year . '_' . $month . '.xlsx';
            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        }
    }

    public function preview(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $type = $request->get('type', 'daily');
        $date = $request->get('date', date('Y-m-d'));
        $departmentId = $request->get('department_id');
        $staffType = $request->get('staff_type', 'All staffs');
        $status = $request->get('status');
        $search = $request->get('search');
        
        $sessionId = $request->get('academic_session_id');
        if ($sessionId) {
            $session = AcademicSession::find($sessionId);
        } else {
            $session = AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first()
                ?? AcademicSession::where('school_id', $schoolId)->first();
        }

        if (!$session) {
            return response()->json(['success' => false, 'message' => 'Academic Session not found.']);
        }
        
        // Calculate academic year total working days (excluding Sundays)
        $start = \Carbon\Carbon::parse($session->start_date);
        $end = \Carbon\Carbon::parse($session->end_date);
        $days = $start->diffInDays($end) + 1;
        $sundays = 0;
        $temp = $start->copy();
        while ($temp->dayOfWeek !== \Carbon\Carbon::SUNDAY && $temp->lte($end)) {
            $temp->addDay();
        }
        if ($temp->lte($end)) {
            $sundays = 1 + floor($temp->diffInDays($end) / 7);
        }
        $totalWorkingDays = $days - $sundays;

        // Build Query
        $query = Staff::with(['department', 'designation'])->where('school_id', $schoolId)->where('is_active', true);

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
        $allStaffIds = $staffList->pluck('id')->toArray();
        
        $attendanceStats = StaffAttendance::where('school_id', $schoolId)
            ->whereIn('staff_id', $allStaffIds)
            ->get()
            ->groupBy('staff_id');

        if ($type === 'daily') {
            $dateAttendances = StaffAttendance::where('school_id', $schoolId)
                ->whereDate('date', $date)
                ->get()
                ->keyBy('staff_id');

            $columns = ['Employee ID', 'Staff Name', 'Department', 'Designation', 'Status', 'Check In', 'Check Out', 'Remarks', 'Attendance %'];
            $rows = [];
            foreach ($staffList as $st) {
                $att = $dateAttendances->get($st->id);
                $statusText = $att ? ucfirst(str_replace('_', ' ', $att->status)) : 'Not Marked';
                $checkIn = $att && $att->clock_in_at ? date('h:i A', strtotime($att->clock_in_at)) : '—';
                $checkOut = $att && $att->clock_out_at ? date('h:i A', strtotime($att->clock_out_at)) : '—';
                $remark = $att ? $att->remark : '';

                $history = $attendanceStats->get($st->id) ?? collect();
                $presentDays = $history->whereIn('status', ['present', 'late', 'holiday'])->count()
                    + ($history->where('status', 'half_day')->count() * 0.5);
                $pct = $totalWorkingDays > 0 ? round(($presentDays / $totalWorkingDays) * 100, 2) : 0;

                $rows[] = [
                    $st->employee_id,
                    $st->full_name,
                    $st->department?->name ?? '—',
                    $st->designation?->name ?? '—',
                    $statusText,
                    $checkIn,
                    $checkOut,
                    $remark,
                    $pct . '%'
                ];
            }
        } else {
            $month = $request->get('month', date('m'));
            $year = $request->get('year', date('Y'));
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

            $monthlyRecords = StaffAttendance::where('school_id', $schoolId)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->get()
                ->groupBy('staff_id');

            $columns = ['Employee ID', 'Staff Name', 'Department', 'Designation'];
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $columns[] = (string)$d;
            }
            $columns[] = 'Present';
            $columns[] = 'Absent';
            $columns[] = 'Attendance %';

            $rows = [];
            foreach ($staffList as $st) {
                $stRecords = $monthlyRecords->get($st->id) ?? collect();
                $presentCountVal = 0;
                $absentCountVal = 0;

                $row = [
                    $st->employee_id,
                    $st->full_name,
                    $st->department?->name ?? '—',
                    $st->designation?->name ?? '—',
                ];

                for ($d = 1; $d <= $daysInMonth; $d++) {
                    $dateString = sprintf('%s-%02s-%02s', $year, $month, $d);
                    $rec = $stRecords->first(fn($r) => $r->date->format('Y-m-d') === $dateString);

                    $statusText = '—';
                    if ($rec) {
                        $status = $rec->status;
                        if ($status === 'present' || $status === 'late' || $status === 'holiday') {
                            $presentCountVal++;
                            $statusText = 'P';
                        } elseif ($status === 'absent') {
                            $absentCountVal++;
                            $statusText = 'A';
                        } elseif ($status === 'half_day') {
                            $presentCountVal += 0.5;
                            $statusText = 'HD';
                        } elseif ($status === 'leave') {
                            $statusText = 'L';
                        }
                    }
                    $row[] = $statusText;
                }

                $history = $attendanceStats->get($st->id) ?? collect();
                $presentDays = $history->whereIn('status', ['present', 'late', 'holiday'])->count()
                    + ($history->where('status', 'half_day')->count() * 0.5);
                $pct = $totalWorkingDays > 0 ? round(($presentDays / $totalWorkingDays) * 100, 2) : 0;

                $row[] = (string)$presentCountVal;
                $row[] = (string)$absentCountVal;
                $row[] = $pct . '%';

                $rows[] = $row;
            }
        }

        $excelLetters = [];
        for ($i = 1; $i <= count($columns); $i++) {
            $excelLetters[] = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
        }

        return response()->json([
            'success' => true,
            'headers' => $excelLetters,
            'columns' => $columns,
            'rows' => $rows
        ]);
    }
}
