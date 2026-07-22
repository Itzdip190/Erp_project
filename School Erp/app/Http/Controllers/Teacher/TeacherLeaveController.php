<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeaveApplication;
use App\Models\Staff;
use App\Models\AcademicSession;
use App\Models\LeaveType;
use App\Models\StaffLeaveBalance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TeacherLeaveController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $schoolId = $user->school_id;

        $staff = Staff::where('school_id', $schoolId)->where('user_id', $user->id)->first();
        if (!$staff) {
            return redirect()->route('teacher.dashboard')->with('error', 'Staff profile not found.');
        }

        // Fetch academic session
        $session = AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first()
            ?? AcademicSession::where('school_id', $schoolId)->first();
        $academicYear = $session ? $session->name : 'Apr 2025 - Mar 2026';

        $staffType = $staff->staff_type;

        // Fetch configured active leave types for this school, academic year, and staff type
        $leaveTypes = LeaveType::where('school_id', $schoolId)
            ->where('academic_year', $academicYear)
            ->where('staff_type', $staffType)
            ->where('is_active', true)
            ->get();

        // Fetch staff leave balances
        $balances = StaffLeaveBalance::where('school_id', $schoolId)
            ->where('staff_id', $staff->id)
            ->get()
            ->keyBy('leave_type_id');

        $leaveSummaries = $leaveTypes->map(function($type) use ($balances) {
            $bal = $balances->get($type->id);
            $allowed = $bal ? (float)$bal->allowed : (float)$type->leave_count;
            $availed = $bal ? (float)$bal->availed : 0;
            return [
                'id' => $type->id,
                'code' => $type->code,
                'name' => $type->name,
                'allowed' => $allowed,
                'availed' => $availed,
                'remaining' => max(0, $allowed - $availed),
            ];
        });

        // History of leave applications
        $applications = LeaveApplication::where('school_id', $schoolId)
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('teacher.leave.apply', compact('user', 'staff', 'leaveSummaries', 'applications'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $schoolId = $user->school_id;

        $staff = Staff::where('school_id', $schoolId)->where('user_id', $user->id)->first();
        if (!$staff) {
            return back()->withErrors(['error' => 'Staff profile not found.']);
        }

        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
        ]);

        $leaveType = LeaveType::where('school_id', $schoolId)->findOrFail($request->leave_type_id);

        // 1. Gender Eligibility Check
        if ($leaveType->gender_eligibility !== 'all') {
            $staffGender = strtolower($staff->gender ?? '');
            if ($staffGender !== strtolower($leaveType->gender_eligibility)) {
                return back()->withErrors(['leave_type_id' => 'You are not eligible for this leave type based on gender eligibility criteria.']);
            }
        }

        // 2. Joining Date Eligibility Check
        if ($leaveType->start_crediting_days > 0) {
            if (!$staff->joining_date) {
                return back()->withErrors(['leave_type_id' => 'No joining date is configured for your profile. Unable to verify eligibility.']);
            }
            $joiningDate = Carbon::parse($staff->joining_date);
            $daysSinceJoining = $joiningDate->diffInDays(Carbon::now());
            if ($daysSinceJoining < $leaveType->start_crediting_days) {
                return back()->withErrors(['leave_type_id' => "You are only eligible for this leave type {$leaveType->start_crediting_days} days after joining. (Current days: {$daysSinceJoining})"]);
            }
        }

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $days = $startDate->diffInDays($endDate) + 1;

        // 3. Submission in Advance Check
        if (!$leaveType->allow_before_date && $startDate->isAfter(Carbon::today())) {
            return back()->withErrors(['start_date' => 'Submission in advance is not allowed for this leave type. Please apply on or after the start date.']);
        }

        // 4. Available Balance Check
        $balance = StaffLeaveBalance::where('school_id', $schoolId)
            ->where('staff_id', $staff->id)
            ->where('leave_type_id', $leaveType->id)
            ->first();

        $allowed = $balance ? (float)$balance->allowed : (float)$leaveType->leave_count;
        $availed = $balance ? (float)$balance->availed : 0;
        $remaining = $allowed - $availed;

        if ($days > $remaining) {
            return back()->withErrors(['end_date' => "Insufficient leave balance. You requested {$days} days, but only have {$remaining} days remaining."]);
        }

        // Apply
        LeaveApplication::create([
            'school_id' => $schoolId,
            'user_id' => $user->id,
            'applicant_type' => 'staff',
            'leave_type' => $leaveType->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return redirect()->route('teacher.leave.apply')->with('success', 'Leave application submitted successfully and is pending approval.');
    }
}
