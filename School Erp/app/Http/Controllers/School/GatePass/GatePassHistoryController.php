<?php

namespace App\Http\Controllers\School\GatePass;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Department;
use App\Models\Designation;
use App\Models\StaffGatePass;
use App\Models\StudentGatePass;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GatePassHistoryController extends Controller
{
    /**
     * Display School-wide Student Gate Pass History.
     */
    public function students(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $query = StudentGatePass::with(['student.class', 'student.section', 'issuedByUser'])
            ->where('school_id', $schoolId);

        // Filter: Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter: Class
        if ($request->filled('class_id')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('class_id', $request->class_id);
            });
        }

        // Filter: Section
        if ($request->filled('section_id')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('section_id', $request->section_id);
            });
        }

        // Filter: Date Range
        if ($request->filled('date_from')) {
            $query->whereDate('pass_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('pass_date', '<=', $request->date_to);
        }

        // Filter: Search (Pass No, Student Name, Adm No, Reason, Guardian)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('gate_pass_number', 'LIKE', "%{$search}%")
                  ->orWhere('reason', 'LIKE', "%{$search}%")
                  ->orWhere('guardian_name', 'LIKE', "%{$search}%")
                  ->orWhereHas('student', function ($sq) use ($search) {
                      $sq->where('first_name', 'LIKE', "%{$search}%")
                         ->orWhere('last_name', 'LIKE', "%{$search}%")
                         ->orWhere('admission_number', 'LIKE', "%{$search}%");
                  });
            });
        }

        $gatePasses = $query->orderBy('pass_date', 'desc')->paginate(20)->withQueryString();

        // Metrics for summary cards
        $totalCount = StudentGatePass::where('school_id', $schoolId)->count();
        $issuedCount = StudentGatePass::where('school_id', $schoolId)->where('status', 'issued')->count();
        $returnedCount = StudentGatePass::where('school_id', $schoolId)->where('status', 'returned')->count();
        $cancelledCount = StudentGatePass::where('school_id', $schoolId)->where('status', 'cancelled')->count();

        $classes = SchoolClass::where('school_id', $schoolId)->orderBy('numeric_name')->get();

        return view('school.gate-pass-history.students', compact(
            'gatePasses',
            'totalCount',
            'issuedCount',
            'returnedCount',
            'cancelledCount',
            'classes'
        ));
    }

    /**
     * Display School-wide Staff Gate Pass History.
     */
    public function staff(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $query = StaffGatePass::with(['staff.designation', 'staff.department', 'issuedByUser'])
            ->where('school_id', $schoolId);

        // Filter: Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter: Department
        if ($request->filled('department_id')) {
            $query->whereHas('staff', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        // Filter: Designation
        if ($request->filled('designation_id')) {
            $query->whereHas('staff', function ($q) use ($request) {
                $q->where('designation_id', $request->designation_id);
            });
        }

        // Filter: Date Range
        if ($request->filled('date_from')) {
            $query->whereDate('pass_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('pass_date', '<=', $request->date_to);
        }

        // Filter: Search (Pass No, Staff Name, Emp ID, Reason)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('gate_pass_number', 'LIKE', "%{$search}%")
                  ->orWhere('reason', 'LIKE', "%{$search}%")
                  ->orWhereHas('staff', function ($sq) use ($search) {
                      $sq->where('first_name', 'LIKE', "%{$search}%")
                         ->orWhere('last_name', 'LIKE', "%{$search}%")
                         ->orWhere('employee_id', 'LIKE', "%{$search}%");
                  });
            });
        }

        $gatePasses = $query->orderBy('pass_date', 'desc')->paginate(20)->withQueryString();

        // Metrics for summary cards
        $totalCount = StaffGatePass::where('school_id', $schoolId)->count();
        $issuedCount = StaffGatePass::where('school_id', $schoolId)->where('status', 'issued')->count();
        $returnedCount = StaffGatePass::where('school_id', $schoolId)->where('status', 'returned')->count();
        $cancelledCount = StaffGatePass::where('school_id', $schoolId)->where('status', 'cancelled')->count();

        $departments = Department::where('school_id', $schoolId)->get();
        $designations = Designation::where('school_id', $schoolId)->get();

        return view('school.gate-pass-history.staff', compact(
            'gatePasses',
            'totalCount',
            'issuedCount',
            'returnedCount',
            'cancelledCount',
            'departments',
            'designations'
        ));
    }

    /**
     * Cancel Student Gate Pass.
     */
    public function cancelStudentPass(Request $request, StudentGatePass $gatePass)
    {
        $schoolId = Auth::user()->school_id;
        if ($gatePass->school_id !== $schoolId) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        $gatePass->update([
            'status' => 'cancelled',
            'remarks' => ($gatePass->remarks ? $gatePass->remarks . ' | ' : '') . 'Cancelled: ' . $validated['cancellation_reason'],
        ]);

        // Send cancellation notification
        try {
            if (class_exists(NotificationService::class)) {
                $student = $gatePass->student;
                NotificationService::send(
                    targetUserOrRole: 'admin',
                    title: 'Student Gate Pass Cancelled',
                    message: "Gate Pass #{$gatePass->gate_pass_number} for student {$student?->full_name} has been CANCELLED. Reason: {$validated['cancellation_reason']}",
                    type: 'gate_pass_cancelled',
                    data: ['gate_pass_id' => $gatePass->id, 'student_id' => $student?->id],
                    schoolId: $schoolId
                );
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to dispatch student gate pass cancellation notification: ' . $e->getMessage());
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Student Gate Pass #{$gatePass->gate_pass_number} has been cancelled.",
            ]);
        }

        return back()->with('success', "Student Gate Pass #{$gatePass->gate_pass_number} has been cancelled successfully.");
    }

    /**
     * Cancel Staff Gate Pass.
     */
    public function cancelStaffPass(Request $request, StaffGatePass $gatePass)
    {
        $schoolId = Auth::user()->school_id;
        if ($gatePass->school_id !== $schoolId) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        $gatePass->update([
            'status' => 'cancelled',
            'cancellation_reason' => $validated['cancellation_reason'],
            'remarks' => ($gatePass->remarks ? $gatePass->remarks . ' | ' : '') . 'Cancelled: ' . $validated['cancellation_reason'],
        ]);

        // Send cancellation notification
        try {
            if (class_exists(NotificationService::class)) {
                $staff = $gatePass->staff;
                NotificationService::send(
                    targetUserOrRole: 'admin',
                    title: 'Staff Gate Pass Cancelled',
                    message: "Staff Gate Pass #{$gatePass->gate_pass_number} for {$staff?->full_name} has been CANCELLED. Reason: {$validated['cancellation_reason']}",
                    type: 'staff_gate_pass_cancelled',
                    data: ['gate_pass_id' => $gatePass->id, 'staff_id' => $staff?->id],
                    schoolId: $schoolId
                );

                if ($staff?->user_id) {
                    NotificationService::send(
                        targetUserOrRole: $staff->user_id,
                        title: 'Your Gate Pass Has Been Cancelled',
                        message: "Gate Pass #{$gatePass->gate_pass_number} was cancelled. Reason: {$validated['cancellation_reason']}",
                        type: 'staff_gate_pass_cancelled',
                        data: ['gate_pass_id' => $gatePass->id],
                        schoolId: $schoolId
                    );
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to dispatch staff gate pass cancellation notification: ' . $e->getMessage());
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Staff Gate Pass #{$gatePass->gate_pass_number} has been cancelled.",
            ]);
        }

        return back()->with('success', "Staff Gate Pass #{$gatePass->gate_pass_number} has been cancelled successfully.");
    }
}
