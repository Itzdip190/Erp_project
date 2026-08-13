<?php

namespace App\Http\Controllers\School\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentDeletionRequest;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class StudentDeletionRequestController extends Controller
{
    /**
     * Display a listing of deletion requests (Audit Trail & Approvals).
     */
    public function index(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $status = $request->get('status', 'all');

        $query = StudentDeletionRequest::where('school_id', $schoolId)
            ->with(['student', 'requester', 'approver', 'rejecter']);

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        $requests = $query->orderByDesc('created_at')->paginate(20);

        return view('school.student.deletion_requests.index', compact('requests', 'status'));
    }

    /**
     * Store a new student deletion request.
     */
    public function store(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $user = auth()->user();

        $request->validate([
            'student_id' => 'required|exists:students,id',
            'reason'     => 'nullable|string|max:500',
        ]);

        $student = Student::where('school_id', $schoolId)->findOrFail($request->student_id);

        // Check if there is already a pending request for this student
        $existing = StudentDeletionRequest::where('school_id', $schoolId)
            ->where('student_id', $student->id)
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'A deletion request for this student is already pending approval by the School Admin.'
                ], 422);
            }
            return redirect()->back()->with('error', 'A deletion request for this student is already pending approval by the School Admin.');
        }

        // Create the deletion request (Student remains 100% active)
        $deletionRequest = StudentDeletionRequest::create([
            'school_id'         => $schoolId,
            'student_id'        => $student->id,
            'admission_number'  => $student->admission_number ?? 'N/A',
            'student_name'      => $student->full_name,
            'class_name'        => optional($student->class)->name ?? 'N/A',
            'section_name'      => optional($student->section)->name ?? 'N/A',
            'reason'            => $request->input('reason'),
            'requested_by'      => $user->id,
            'requested_by_name' => $user->name ?? ($user->username ?? 'Staff'),
            'requested_at'      => now(),
            'status'            => 'pending',
        ]);

        // Trigger School Admin Notification inside the panel
        NotificationService::send([
            'school_id'      => $schoolId,
            'recipient_role' => 'school_admin',
            'title'          => 'Student Deletion Request',
            'message'        => "A deletion request has been submitted. Student: {$student->full_name}, Admission ID: " . ($student->admission_number ?? 'N/A') . ", Requested By: " . ($user->name ?? 'Staff') . ", Date: " . now()->format('d M Y, h:i A'),
            'module'         => 'student',
            'type'           => 'warning',
            'related_id'     => $deletionRequest->id,
            'action_url'     => route('school.students.deletion-requests.index'),
            'icon'           => 'fa-user-times',
            'color'          => '#ef4444',
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Deletion request sent to School Admin for approval.'
            ]);
        }

        return redirect()->route('school.students.index')->with('success', 'Deletion request sent to School Admin for approval.');
    }

    /**
     * Approve a deletion request and execute permanent deletion.
     */
    public function approve(Request $request, $id)
    {
        $user = auth()->user();

        // SECURITY CHECK: Only School Admin / Super Admin can approve
        if (!($user->hasRole('school_admin') || $user->hasRole('superadmin') || ($user->role ?? null) === 'school_admin' || ($user->role ?? null) === 'superadmin')) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized. Only School Admin can approve student deletion requests.'], 403);
            }
            abort(403, 'Unauthorized. Only School Admin can approve student deletion requests.');
        }

        $schoolId = $user->school_id;
        $deletionRequest = StudentDeletionRequest::where('school_id', $schoolId)
            ->where('status', 'pending')
            ->findOrFail($id);

        // Update Audit Log Status to Approved
        $deletionRequest->update([
            'status'           => 'approved',
            'approved_by'      => $user->id,
            'approved_by_name' => $user->name ?? 'School Admin',
            'approved_at'      => now(),
        ]);

        // THEN AND ONLY THEN: Execute existing ERP deletion logic
        $student = Student::withTrashed()->find($deletionRequest->student_id);
        if ($student) {
            $student->update(['is_active' => 0]);
            $student->delete();

            // Trigger notification to parent
            Log::info("Parent Notification: Student {$student->full_name} has been deactivated. Guardian email: {$student->guardian_email}, Phone: {$student->guardian_phone}.");

            Cache::forget('students_list_version_' . $schoolId);
            Cache::put('students_list_version_' . $schoolId, time(), 86400);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Student deletion request approved. Student has been deleted.'
            ]);
        }

        return redirect()->back()->with('success', 'Student deletion request approved. Student has been deleted.');
    }

    /**
     * Reject a deletion request.
     */
    public function reject(Request $request, $id)
    {
        $user = auth()->user();

        // SECURITY CHECK: Only School Admin / Super Admin can reject
        if (!($user->hasRole('school_admin') || $user->hasRole('superadmin') || ($user->role ?? null) === 'school_admin' || ($user->role ?? null) === 'superadmin')) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized. Only School Admin can reject student deletion requests.'], 403);
            }
            abort(403, 'Unauthorized. Only School Admin can reject student deletion requests.');
        }

        $schoolId = $user->school_id;
        $deletionRequest = StudentDeletionRequest::where('school_id', $schoolId)
            ->where('status', 'pending')
            ->findOrFail($id);

        // Update Audit Log Status to Rejected
        $deletionRequest->update([
            'status'           => 'rejected',
            'rejected_by'      => $user->id,
            'rejected_by_name' => $user->name ?? 'School Admin',
            'rejected_at'      => now(),
            'rejection_reason' => $request->input('rejection_reason'),
        ]);

        // Student remains completely active and unchanged in DB

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Student deletion request rejected. Student remains active.'
            ]);
        }

        return redirect()->back()->with('success', 'Student deletion request rejected. Student remains active.');
    }
}
