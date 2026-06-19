<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeaveApplication;
use App\Models\Staff;
use App\Models\Student;

class LeaveManagementController extends Controller
{
    private function ensureLeavesSeeded($schoolId)
    {
        if (LeaveApplication::where('school_id', $schoolId)->count() === 0) {
            // Seed a few staff leave requests
            $staff = Staff::where('school_id', $schoolId)->first();
            if ($staff && $staff->user) {
                LeaveApplication::create([
                    'school_id' => $schoolId,
                    'user_id' => $staff->user->id,
                    'applicant_type' => 'staff',
                    'leave_type' => 'Sick Leave',
                    'start_date' => now()->subDays(2)->toDateString(),
                    'end_date' => now()->addDays(1)->toDateString(),
                    'reason' => 'Feeling unwell with a fever.',
                    'status' => 'pending',
                ]);
            }

            // Seed a few student leave requests
            $student = Student::where('school_id', $schoolId)->first();
            if ($student && $student->user) {
                LeaveApplication::create([
                    'school_id' => $schoolId,
                    'user_id' => $student->user->id,
                    'applicant_type' => 'student',
                    'leave_type' => 'Casual Leave',
                    'start_date' => now()->addDays(3)->toDateString(),
                    'end_date' => now()->addDays(5)->toDateString(),
                    'reason' => 'Attending sister\'s wedding ceremony.',
                    'status' => 'pending',
                ]);
            }
        }
    }

    public function basics(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $this->ensureLeavesSeeded($schoolId);

        if ($request->isMethod('post')) {
            return back()->with('success', 'Leave policy and configuration saved successfully!');
        }

        return view('school.leave.basics');
    }

    public function staff(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $this->ensureLeavesSeeded($schoolId);

        if ($request->isMethod('post')) {
            $request->validate([
                'id' => 'required|exists:leave_applications,id',
                'action' => 'required|in:approve,reject',
            ]);

            $leave = LeaveApplication::where('school_id', $schoolId)->findOrFail($request->id);
            $leave->status = $request->action === 'approve' ? 'approved' : 'rejected';
            $leave->approved_by = auth()->id();
            $leave->save();

            return back()->with('success', 'Staff leave application ' . $leave->status . ' successfully.');
        }

        $applications = LeaveApplication::where('school_id', $schoolId)
            ->where('applicant_type', 'staff')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('school.leave.staff', compact('applications'));
    }

    public function student(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $this->ensureLeavesSeeded($schoolId);

        if ($request->isMethod('post')) {
            $request->validate([
                'id' => 'required|exists:leave_applications,id',
                'action' => 'required|in:approve,reject',
            ]);

            $leave = LeaveApplication::where('school_id', $schoolId)->findOrFail($request->id);
            $leave->status = $request->action === 'approve' ? 'approved' : 'rejected';
            $leave->approved_by = auth()->id();
            $leave->save();

            return back()->with('success', 'Student leave application ' . $leave->status . ' successfully.');
        }

        $applications = LeaveApplication::where('school_id', $schoolId)
            ->where('applicant_type', 'student')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('school.leave.student', compact('applications'));
    }
}
