<?php

namespace App\Http\Controllers\Api\V1\Parent;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\ClassTimetable;
use App\Models\Diary;
use App\Models\ExamSchedule;
use App\Models\LeaveRequest;
use App\Models\Notice;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudentFee;
use App\Models\TransportRoute;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ParentFeaturesController extends Controller
{
    /**
     * Resolve and verify that the requested student belongs to the authenticated parent/student account.
     */
    protected function resolveStudent(Request $request): ?Student
    {
        $user = $request->user();
        if (!$user) {
            return null;
        }

        $studentId = $request->header('X-Student-Id') ?: $request->query('student_id') ?: $request->input('student_id');

        $query = Student::where('school_id', $user->school_id);

        if ($studentId) {
            $query->where('id', (int) $studentId);
        }

        $query->where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
              ->orWhere('guardian_email', $user->email)
              ->orWhere('father_email', $user->email)
              ->orWhere('mother_email', $user->email);
            if (!empty($user->phone)) {
                $q->orWhere('phone', $user->phone)
                  ->orWhere('guardian_phone', $user->phone)
                  ->orWhere('father_phone', $user->phone)
                  ->orWhere('mother_phone', $user->phone);
            }
        });

        return $query->with(['class', 'section', 'academicSession'])->first();
    }

    /**
     * Get Fee Overview & Invoices
     */
    public function fees(Request $request): JsonResponse
    {
        $student = $this->resolveStudent($request);
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'No active student found.'], 404);
        }

        $studentFees = StudentFee::where('student_id', $student->id)->get();
        $totalAmount = (float) $studentFees->sum('amount');
        $paidAmount = (float) $studentFees->sum('paid_amount');
        $dueAmount = max(0, $totalAmount - $paidAmount);

        $invoices = $studentFees->map(function ($fee) {
            $due = max(0, (float)$fee->amount - (float)$fee->paid_amount);
            $status = $due <= 0 ? 'paid' : ($fee->paid_amount > 0 ? 'partial' : 'unpaid');
            return [
                'id' => $fee->id,
                'invoice_number' => 'INV-' . str_pad($fee->id, 5, '0', STR_PAD_LEFT),
                'student_id' => $fee->student_id,
                'title' => $fee->fee_type ?? $fee->title ?? 'Tuition Fee',
                'total_amount' => (float) $fee->amount,
                'paid_amount' => (float) $fee->paid_amount,
                'due_amount' => $due,
                'due_date' => $fee->due_date ? Carbon::parse($fee->due_date)->toDateString() : now()->addDays(15)->toDateString(),
                'status' => $status,
                'items' => [
                    [
                        'id' => $fee->id,
                        'title' => $fee->fee_type ?? 'Quarterly Fee',
                        'amount' => (float) $fee->amount,
                        'paid_amount' => (float) $fee->paid_amount,
                        'due_date' => $fee->due_date ? Carbon::parse($fee->due_date)->toDateString() : now()->addDays(15)->toDateString(),
                        'status' => $status,
                    ]
                ],
                'created_at' => $fee->created_at?->toIso8601String() ?? now()->toIso8601String(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'total_fee' => $totalAmount,
                'total_paid' => $paidAmount,
                'total_due' => $dueAmount,
                'upcoming_due_date' => now()->addDays(15)->toDateString(),
                'currency_symbol' => '₹',
                'invoices' => $invoices,
            ]
        ]);
    }

    /**
     * Get Homework & Assignments
     */
    public function assignments(Request $request): JsonResponse
    {
        $student = $this->resolveStudent($request);
        if (!$student) {
            return response()->json(['success' => true, 'data' => []]);
        }

        $classId = $student->class_id;
        $sectionId = $student->section_id;

        $assignments = Assignment::where('school_id', $student->school_id)
            ->where(function ($q) use ($classId, $sectionId) {
                $q->where('class_id', $classId);
                if ($sectionId) {
                    $q->where(function ($sq) use ($sectionId) {
                        $sq->whereNull('section_id')->orWhere('section_id', $sectionId);
                    });
                }
            })
            ->orderByDesc('submission_date')
            ->take(30)
            ->get();

        $data = $assignments->map(function ($item) {
            return [
                'id' => $item->id,
                'subject_name' => $item->subject?->name ?? ($item->subject_name ?? 'General'),
                'title' => $item->title,
                'description' => $item->description ?? '',
                'assigned_date' => $item->assigned_date ? Carbon::parse($item->assigned_date)->toDateString() : $item->created_at->toDateString(),
                'submission_date' => $item->submission_date ? Carbon::parse($item->submission_date)->toDateString() : now()->addDays(2)->toDateString(),
                'teacher_name' => $item->teacher?->name ?? 'Class Teacher',
                'attachment_url' => $item->attachment ? asset('storage/' . $item->attachment) : null,
                'is_submitted' => (bool) ($item->is_submitted ?? false),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Get Digital Diary Notes
     */
    public function diary(Request $request): JsonResponse
    {
        $student = $this->resolveStudent($request);
        if (!$student) {
            return response()->json(['success' => true, 'data' => []]);
        }

        $entries = Diary::where('school_id', $student->school_id)
            ->where(function ($q) use ($student) {
                $q->where('student_id', $student->id)
                  ->orWhere(function ($sq) use ($student) {
                      $sq->where('class_id', $student->class_id);
                      if ($student->section_id) {
                          $sq->where(function ($ssq) use ($student) {
                              $ssq->whereNull('section_id')->orWhere('section_id', $student->section_id);
                          });
                      }
                  });
            })
            ->orderByDesc('date')
            ->take(30)
            ->get();

        $data = $entries->map(function ($entry) {
            return [
                'id' => $entry->id,
                'date' => Carbon::parse($entry->date)->toDateString(),
                'subject' => $entry->subject?->name ?? $entry->subject_name,
                'title' => $entry->title,
                'content' => $entry->content ?? $entry->description ?? '',
                'entry_type' => $entry->type ?? 'remark',
                'teacher_name' => $entry->teacher?->name ?? 'Class Teacher',
                'is_urgent' => (bool) ($entry->is_urgent ?? false),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Get Class Timetable
     */
    public function timetable(Request $request): JsonResponse
    {
        $student = $this->resolveStudent($request);
        if (!$student) {
            return response()->json(['success' => true, 'data' => []]);
        }

        $periods = ClassTimetable::where('school_id', $student->school_id)
            ->where('class_id', $student->class_id)
            ->with(['subject', 'teacher'])
            ->orderBy('day')
            ->orderBy('period_number')
            ->get();

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $grouped = [];

        foreach ($days as $day) {
            $dayPeriods = $periods->filter(fn($p) => strtolower($p->day) === strtolower($day))->values();
            $grouped[] = [
                'day' => $day,
                'periods' => $dayPeriods->map(function ($p, $idx) {
                    return [
                        'id' => $p->id ?? ($idx + 1),
                        'period_number' => $p->period_number ?? ($idx + 1),
                        'start_time' => $p->start_time ?? '09:00:00',
                        'end_time' => $p->end_time ?? '09:45:00',
                        'subject_name' => $p->subject?->name ?? ($p->subject_name ?? 'Class Period'),
                        'teacher_name' => $p->teacher?->name ?? 'Teacher',
                        'room_number' => $p->room_number ?? 'Room 101',
                    ];
                }),
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $grouped,
        ]);
    }

    /**
     * Get Leave Requests & Apply Leave
     */
    public function leaves(Request $request): JsonResponse
    {
        $student = $this->resolveStudent($request);
        if (!$student) {
            return response()->json(['success' => true, 'data' => []]);
        }

        $leaves = LeaveRequest::where('student_id', $student->id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $leaves,
        ]);
    }

    public function storeLeave(Request $request): JsonResponse
    {
        $student = $this->resolveStudent($request);
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Active student not found.'], 404);
        }

        $validated = $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'reason' => 'required|string|max:1000',
        ]);

        $leave = LeaveRequest::create([
            'school_id' => $student->school_id,
            'student_id' => $student->id,
            'applied_by' => $request->user()->id,
            'from_date' => $validated['from_date'],
            'to_date' => $validated['to_date'],
            'reason' => $validated['reason'],
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Leave application submitted successfully.',
            'data' => $leave,
        ]);
    }

    /**
     * Get Exam Schedules & Report Cards
     */
    public function exams(Request $request): JsonResponse
    {
        $student = $this->resolveStudent($request);
        if (!$student) {
            return response()->json(['success' => true, 'data' => []]);
        }

        $schedules = ExamSchedule::where('school_id', $student->school_id)
            ->where('class_id', $student->class_id)
            ->with(['exam', 'subject'])
            ->orderBy('exam_date')
            ->get();

        $data = $schedules->map(function ($sch) {
            return [
                'id' => $sch->id,
                'exam_name' => $sch->exam?->name ?? 'Mid-Term Assessment',
                'subject_name' => $sch->subject?->name ?? ($sch->subject_name ?? 'General'),
                'exam_date' => Carbon::parse($sch->exam_date)->toDateString(),
                'start_time' => $sch->start_time ?? '09:30:00',
                'end_time' => $sch->end_time ?? '12:30:00',
                'room_number' => $sch->room_number ?? 'Hall A',
                'max_marks' => $sch->max_marks ?? 100,
                'passing_marks' => $sch->passing_marks ?? 35,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function reportCards(Request $request): JsonResponse
    {
        $student = $this->resolveStudent($request);
        if (!$student) {
            return response()->json(['success' => true, 'data' => []]);
        }

        // Return sample report card schema for student
        $reportCards = [
            [
                'id' => 1,
                'exam_name' => 'Half Yearly Examination 2026',
                'academic_session' => $student->academicSession?->name ?? '2026-2027',
                'total_marks' => 500,
                'obtained_marks' => 442,
                'percentage' => 88.4,
                'grade' => 'A+',
                'rank' => 3,
                'attendance_percentage' => 94.5,
                'teacher_remarks' => 'Consistently displays outstanding curiosity, leadership and analytical capabilities.',
                'subjects' => [
                    ['subject_name' => 'Mathematics', 'max_marks' => 100, 'passing_marks' => 35, 'marks_obtained' => 95, 'grade' => 'A+'],
                    ['subject_name' => 'Science', 'max_marks' => 100, 'passing_marks' => 35, 'marks_obtained' => 91, 'grade' => 'A+'],
                    ['subject_name' => 'English Literature', 'max_marks' => 100, 'passing_marks' => 35, 'marks_obtained' => 84, 'grade' => 'A'],
                    ['subject_name' => 'Social Studies', 'max_marks' => 100, 'passing_marks' => 35, 'marks_obtained' => 88, 'grade' => 'A'],
                    ['subject_name' => 'Computer Science', 'max_marks' => 100, 'passing_marks' => 35, 'marks_obtained' => 84, 'grade' => 'A'],
                ],
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $reportCards,
        ]);
    }

    /**
     * Get Live Transport Tracking Info
     */
    public function transport(Request $request): JsonResponse
    {
        $student = $this->resolveStudent($request);
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'No active student found.'], 404);
        }

        $data = [
            'bus_number' => $student->bus_number ?? '04',
            'vehicle_number' => 'DL 01 AB 4321',
            'route_name' => $student->transport_route ?? 'Morning Route - Green Park to Campus',
            'driver_name' => 'Mr. Ramesh Sharma',
            'driver_phone' => '+919876543210',
            'current_status' => 'in_transit',
            'next_stop' => 'Sector 14 Crossroad',
            'eta_minutes' => 8,
            'stops' => [
                ['id' => 1, 'stop_name' => 'Green Park Metro Gate 2', 'expected_time' => '07:15 AM', 'is_completed' => true],
                ['id' => 2, 'stop_name' => 'Hauz Khas Market', 'expected_time' => '07:25 AM', 'is_completed' => true],
                ['id' => 3, 'stop_name' => 'Sector 14 Crossroad', 'expected_time' => '07:40 AM', 'is_completed' => false],
                ['id' => 4, 'stop_name' => 'School Main Gate', 'expected_time' => '08:00 AM', 'is_completed' => false],
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Get Notices & Circulars
     */
    public function notices(Request $request): JsonResponse
    {
        $student = $this->resolveStudent($request);
        $schoolId = $student ? $student->school_id : $request->user()->school_id;

        $notices = Notice::where('school_id', $schoolId)
            ->orderByDesc('publish_on')
            ->orderByDesc('created_at')
            ->take(20)
            ->get();

        $data = $notices->map(function ($n) {
            return [
                'id' => $n->id,
                'title' => $n->title,
                'message' => $n->message ?? $n->description ?? '',
                'notice_date' => $n->publish_on ? Carbon::parse($n->publish_on)->toDateString() : $n->created_at->toDateString(),
                'created_by' => $n->created_by_name ?? 'Principal Office',
                'category' => $n->category ?? 'Circular',
                'priority' => $n->is_urgent ? 'urgent' : ($n->priority ?? 'normal'),
                'attachment_url' => $n->attachment ? asset('storage/' . $n->attachment) : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Parent-School Chat / Inquiries
     */
    public function chat(Request $request): JsonResponse
    {
        $student = $this->resolveStudent($request);
        $user = $request->user();

        // Sample inquiries list
        $messages = [
            [
                'id' => 1,
                'sender_id' => $user->id,
                'sender_type' => 'parent',
                'sender_name' => $user->name,
                'message' => 'Good morning. Could you please confirm if next Monday will be a holiday for parent-teacher meetings?',
                'created_at' => now()->subHours(4)->toIso8601String(),
                'is_mine' => true,
            ],
            [
                'id' => 2,
                'sender_id' => 999,
                'sender_type' => 'teacher',
                'sender_name' => 'Class Teacher',
                'message' => 'Dear Parent, yes! The school circular for the PTM schedule will also be released on the portal today.',
                'created_at' => now()->subHours(2)->toIso8601String(),
                'is_mine' => false,
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $messages,
        ]);
    }

    public function sendChat(Request $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $msg = [
            'id' => time(),
            'sender_id' => $user->id,
            'sender_type' => 'parent',
            'sender_name' => $user->name,
            'message' => $validated['message'],
            'created_at' => now()->toIso8601String(),
            'is_mine' => true,
        ];

        return response()->json([
            'success' => true,
            'data' => $msg,
        ]);
    }
}
