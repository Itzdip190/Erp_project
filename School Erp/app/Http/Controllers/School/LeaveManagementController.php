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
        // No auto-seeding
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
