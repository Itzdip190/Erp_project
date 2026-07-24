<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\LeaveType;
use App\Models\School;
use App\Models\Staff;
use App\Models\StaffLeaveApplication;
use App\Models\StaffLeaveBalance;
use App\Models\TeacherNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class TeacherLeaveController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $schoolId = $user->school_id;
        if (!$schoolId && app()->bound('currentSchool')) {
            $schoolId = app('currentSchool')?->id;
        }
        if (!$schoolId && Schema::hasTable('schools')) {
            $schoolId = School::first()?->id ?? 1;
        }

        $currentSession = null;
        if (Schema::hasTable('academic_sessions')) {
            $currentSession = AcademicSession::where('school_id', $schoolId)
                ->where('is_current', true)
                ->first()
                ?? AcademicSession::where('school_id', $schoolId)->first();
        }
        $academicYear = $currentSession?->name ?? '2026-2027';

        $staff = null;
        if (Schema::hasTable('staff')) {
            $staff = Staff::where('user_id', $user->id)->first();
        }

        $staffType = $staff?->staff_type ?? 'Teaching';

        // Fetch leave types for school, filtered by academic year & staff type
        $leaveTypes = collect();
        if (Schema::hasTable('leave_types')) {
            $leaveTypes = LeaveType::where('school_id', $schoolId)
                ->where('academic_year', $academicYear)
                ->where('staff_type', $staffType)
                ->where('is_active', true)
                ->orderBy('id', 'asc')
                ->get();

            if ($leaveTypes->isEmpty()) {
                $leaveTypes = LeaveType::where('school_id', $schoolId)
                    ->where('staff_type', $staffType)
                    ->where('is_active', true)
                    ->orderBy('id', 'asc')
                    ->get();
            }

            if ($leaveTypes->isEmpty()) {
                $leaveTypes = LeaveType::where('school_id', $schoolId)
                    ->where('is_active', true)
                    ->orderBy('id', 'asc')
                    ->get();
            }

            if ($leaveTypes->isEmpty()) {
                $leaveTypes = LeaveType::where('school_id', $schoolId)->orderBy('id', 'asc')->get();
            }

            // Deduplicate by code so every leave type appears ONLY ONCE
            $leaveTypes = $leaveTypes->unique(function ($item) {
                return strtoupper(trim($item->code ?? $item->name));
            })->values();
        }

        $leaveSummaries = collect();
        if ($staff && $leaveTypes->isNotEmpty()) {
            foreach ($leaveTypes as $lt) {
                $bal = null;
                if (Schema::hasTable('staff_leave_balances')) {
                    $bal = StaffLeaveBalance::where('school_id', $schoolId)
                        ->where('staff_id', $staff->id)
                        ->where('leave_type_id', $lt->id)
                        ->first();
                }

                $allowed = ($bal && (float)$bal->allowed > 0) 
                    ? (float)$bal->allowed 
                    : (float)($lt->leave_count ?? 12);

                // Calculate total availed days from approved applications for this leave type
                $availed = 0.0;
                if (Schema::hasTable('staff_leave_applications')) {
                    $availed = (float) StaffLeaveApplication::where('school_id', $schoolId)
                        ->where('staff_id', $staff->id)
                        ->where(function ($q) use ($lt) {
                            $q->where('leave_type_id', $lt->id)
                              ->orWhere('leave_type_code', $lt->code);
                        })
                        ->where('status', 'approved')
                        ->sum('total_days');
                }

                // Keep database balance table availed column in sync
                if ($bal && (float)$bal->availed !== (float)$availed) {
                    $bal->update(['availed' => $availed]);
                }

                $remaining = max(0, $allowed - $availed);

                $leaveSummaries->push([
                    'id'        => $lt->id,
                    'name'      => $lt->name,
                    'code'      => $lt->code,
                    'allowed'   => $allowed,
                    'availed'   => $availed,
                    'remaining' => $remaining,
                ]);
            }
        }

        $applications = collect();
        if ($staff && Schema::hasTable('staff_leave_applications')) {
            $applications = StaffLeaveApplication::where('school_id', $schoolId)
                ->where('staff_id', $staff->id)
                ->latest()
                ->get();
        }

        return view('teacher.leave.apply', compact('user', 'staff', 'leaveSummaries', 'applications'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
            'reason'        => 'required|string|max:1000',
        ]);

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $schoolId = $user->school_id;
        if (!$schoolId && app()->bound('currentSchool')) {
            $schoolId = app('currentSchool')?->id;
        }

        $staff = Staff::where('user_id', $user->id)->first();
        if (!$staff) {
            return redirect()->back()->withErrors(['msg' => 'Staff record not found for your user account.']);
        }

        $currentSession = AcademicSession::where('school_id', $schoolId)
            ->where('is_current', true)
            ->first();
        $academicYear = $currentSession?->name ?? '2026-2027';

        $leaveType = LeaveType::where('school_id', $schoolId)->findOrFail($request->leave_type_id);

        $startDate = Carbon::parse($request->start_date);
        $endDate   = Carbon::parse($request->end_date);
        $totalDays = $startDate->diffInDays($endDate) + 1;

        $application = StaffLeaveApplication::create([
            'school_id'       => $schoolId,
            'staff_id'        => $staff->id,
            'academic_year'   => $academicYear,
            'staff_type'      => $staff->staff_type ?? 'Teaching',
            'leave_type_id'   => $leaveType->id,
            'leave_type_code' => $leaveType->code,
            'leave_type_name' => $leaveType->name,
            'start_date'      => $startDate->toDateString(),
            'end_date'        => $endDate->toDateString(),
            'total_days'      => $totalDays,
            'reason'          => $request->reason,
            'status'          => 'pending',
        ]);

        // 1. Notify Teacher
        \App\Services\NotificationService::send([
            'school_id'      => $schoolId,
            'user_id'        => $user->id,
            'staff_id'       => $staff->id,
            'recipient_role' => 'teacher',
            'title'          => 'Leave Request Submitted',
            'message'        => "Your leave request for {$leaveType->name} ({$startDate->format('d/m/Y')} to {$endDate->format('d/m/Y')}) has been submitted successfully and is awaiting approval.",
            'module'         => 'leave',
            'type'           => 'leave_submitted',
            'related_id'     => $application->id,
            'action_url'     => route('teacher.leave.apply'),
            'icon'           => 'fa-paper-plane',
            'color'          => '#8b5cf6',
        ]);

        // 2. Notify School Admin
        $staffName = trim($staff->first_name . ' ' . ($staff->last_name ?? ''));
        \App\Services\NotificationService::send([
            'school_id'      => $schoolId,
            'recipient_role' => 'school_admin',
            'title'          => 'Staff Applied for Leave',
            'message'        => "{$staffName} applied for {$leaveType->name} ({$startDate->format('d/m/Y')} to {$endDate->format('d/m/Y')}).",
            'module'         => 'leave',
            'type'           => 'staff_leave_submitted',
            'related_id'     => $application->id,
            'priority'       => 'high',
            'action_url'     => route('school.leave.staff'),
            'icon'           => 'fa-calendar-plus',
            'color'          => '#3b82f6',
        ]);

        return redirect()->route('teacher.leave.apply')->with('success', 'Leave application submitted successfully.');
    }


    /**
     * API: Get notifications for logged-in teacher
     */
    public function getNotifications(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['notifications' => [], 'unread_count' => 0]);
        }

        if (!Schema::hasTable('teacher_notifications')) {
            return response()->json(['notifications' => [], 'unread_count' => 0]);
        }

        $staff = Staff::where('user_id', $user->id)->first();

        $query = TeacherNotification::where(function ($q) use ($user, $staff) {
            $q->where('user_id', $user->id);
            if ($staff) {
                $q->orWhere('staff_id', $staff->id);
            }
        });

        $unreadCount = (clone $query)->where('is_read', false)->count();

        $notifications = $query->orderByDesc('id')
            ->take(30)
            ->get()
            ->map(function ($item) {
                return [
                    'id'         => $item->id,
                    'title'      => $item->title,
                    'message'    => $item->message,
                    'type'       => $item->type,
                    'is_read'    => (bool) $item->is_read,
                    'time_ago'   => $item->created_at ? $item->created_at->diffForHumans() : 'Recently',
                    'created_at' => $item->created_at ? $item->created_at->format('d M Y, h:i A') : '',
                ];
            });

        return response()->json([
            'status'        => 'success',
            'unread_count'  => $unreadCount,
            'notifications' => $notifications,
        ]);
    }

    /**
     * API: Mark individual notification as read
     */
    public function markAsRead(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user || !Schema::hasTable('teacher_notifications')) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $staff = Staff::where('user_id', $user->id)->first();

        $notification = TeacherNotification::where(function ($q) use ($user, $staff) {
            $q->where('user_id', $user->id);
            if ($staff) {
                $q->orWhere('staff_id', $staff->id);
            }
        })->find($id);

        if ($notification) {
            $notification->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }

        return response()->json(['status' => 'success', 'message' => 'Notification marked as read']);
    }

    /**
     * API: Mark all notifications as read
     */
    public function markAllRead(Request $request)
    {
        $user = Auth::user();
        if (!$user || !Schema::hasTable('teacher_notifications')) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $staff = Staff::where('user_id', $user->id)->first();

        TeacherNotification::where(function ($q) use ($user, $staff) {
            $q->where('user_id', $user->id);
            if ($staff) {
                $q->orWhere('staff_id', $staff->id);
            }
        })
        ->where('is_read', false)
        ->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json(['status' => 'success', 'message' => 'All notifications marked as read']);
    }
}
