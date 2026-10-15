<?php

namespace App\Http\Controllers\Api\V1\Staff;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\LeaveType;
use App\Models\Staff;
use App\Models\StaffLeaveApplication;
use App\Models\StaffLeaveBalance;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class StaffLeaveApiController extends Controller
{
    /**
     * Get Leave balances and application history for authenticated staff.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $schoolId = $user->school_id ?? 1;

        $staff = Staff::where('user_id', $user->id)->first();
        if (!$staff) {
            return response()->json([
                'success' => false,
                'message' => 'Staff profile not found for this account.',
            ], 403);
        }

        // 1. Fetch Balances
        $balances = [];
        if (Schema::hasTable('staff_leave_balances')) {
            $balances = StaffLeaveBalance::where('staff_id', $staff->id)
                ->where('school_id', $schoolId)
                ->with('leaveType')
                ->get()
                ->map(function ($b) {
                    return [
                        'leave_type_id' => $b->leave_type_id,
                        'leave_type'    => $b->leaveType?->name ?? 'Leave',
                        'allowed'       => (float) $b->allocated_days,
                        'availed'       => (float) $b->used_days,
                        'remaining'     => (float) $b->remaining_days,
                    ];
                });
        }

        // 2. Fallback / Active Leave Types if balances table empty
        if (empty($balances) || count($balances) === 0) {
            $leaveTypes = LeaveType::where('school_id', $schoolId)->where('is_active', true)->get();
            $balances = $leaveTypes->map(function ($lt) {
                return [
                    'leave_type_id' => $lt->id,
                    'leave_type'    => $lt->name,
                    'allowed'       => (float) ($lt->leave_count ?? 12),
                    'availed'       => 0.0,
                    'remaining'     => (float) ($lt->leave_count ?? 12),
                ];
            });
        }

        // 3. Applications History
        $applications = StaffLeaveApplication::where('staff_id', $staff->id)
            ->where('school_id', $schoolId)
            ->orderByDesc('id')
            ->get()
            ->map(function ($app) {
                $start = $app->start_date ? Carbon::parse($app->start_date) : null;
                $end   = $app->end_date ? Carbon::parse($app->end_date) : null;
                $days  = ($start && $end) ? ($start->diffInDays($end) + 1) : $app->total_days;

                return [
                    'id'               => $app->id,
                    'leave_type'       => $app->leave_type_name ?? $app->leave_type_code ?? 'Leave',
                    'start_date'       => $app->start_date,
                    'end_date'         => $app->end_date,
                    'start_date_fmt'   => $start ? $start->format('d M Y') : '',
                    'end_date_fmt'     => $end ? $end->format('d M Y') : '',
                    'total_days'       => $days,
                    'status'           => strtolower($app->status),
                    'status_label'     => ucfirst($app->status),
                    'reason'           => $app->reason,
                    'admin_remark'     => $app->admin_remark,
                    'created_at_fmt'   => $app->created_at ? $app->created_at->format('d M Y, h:i A') : '',
                ];
            });

        return response()->json([
            'success'      => true,
            'balances'     => $balances,
            'applications' => $applications,
        ]);
    }

    /**
     * Submit a new staff leave application.
     */
    public function store(Request $request)
    {
        $request->validate([
            'leave_type_id' => 'nullable|exists:leave_types,id',
            'leave_type'    => 'nullable|string',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
            'reason'        => 'required|string|max:1000',
            'duration_mode' => 'nullable|in:full,first_half,second_half',
        ]);

        $user = Auth::user();
        $schoolId = $user->school_id ?? 1;

        $staff = Staff::where('user_id', $user->id)->first();
        if (!$staff) {
            return response()->json([
                'success' => false,
                'message' => 'Staff profile not found.',
            ], 403);
        }

        $currentSession = AcademicSession::resolveCurrentSessionForUser($user, $schoolId);
        $academicYear = $currentSession?->name ?? '2026-2027';

        $leaveTypeId = $request->leave_type_id;
        $leaveTypeName = $request->leave_type ?? 'Casual Leave';
        $leaveTypeCode = 'CL';

        if ($leaveTypeId) {
            $lt = LeaveType::find($leaveTypeId);
            if ($lt) {
                $leaveTypeName = $lt->name;
                $leaveTypeCode = $lt->code ?? 'LV';
            }
        } else {
            $lt = LeaveType::where('school_id', $schoolId)->where('name', 'LIKE', "%{$leaveTypeName}%")->first();
            if ($lt) {
                $leaveTypeId = $lt->id;
                $leaveTypeCode = $lt->code ?? 'LV';
            }
        }

        $startDate = Carbon::parse($request->start_date);
        $endDate   = Carbon::parse($request->end_date);
        $totalDays = $request->duration_mode === 'full' || empty($request->duration_mode)
            ? ($startDate->diffInDays($endDate) + 1)
            : 0.5;

        $application = StaffLeaveApplication::create([
            'school_id'       => $schoolId,
            'staff_id'        => $staff->id,
            'academic_year'   => $academicYear,
            'staff_type'      => $staff->staff_type ?? 'Teaching',
            'leave_type_id'   => $leaveTypeId,
            'leave_type_code' => $leaveTypeCode,
            'leave_type_name' => $leaveTypeName,
            'start_date'      => $startDate->toDateString(),
            'end_date'        => $endDate->toDateString(),
            'total_days'      => $totalDays,
            'reason'          => $request->reason,
            'status'          => 'pending',
        ]);

        // Send notifications
        try {
            NotificationService::send([
                'school_id'      => $schoolId,
                'user_id'        => $user->id,
                'staff_id'       => $staff->id,
                'recipient_role' => 'teacher',
                'title'          => 'Leave Request Submitted',
                'message'        => "Your leave request for {$leaveTypeName} ({$startDate->format('d/m/Y')} to {$endDate->format('d/m/Y')}) has been logged in ERP.",
                'module'         => 'leave',
                'type'           => 'leave_submitted',
                'related_id'     => $application->id,
                'icon'           => 'fa-paper-plane',
                'color'          => '#52B766',
            ]);

            $staffName = trim($staff->first_name . ' ' . ($staff->last_name ?? ''));
            NotificationService::send([
                'school_id'      => $schoolId,
                'recipient_role' => 'school_admin',
                'title'          => 'Staff Applied for Leave',
                'message'        => "{$staffName} applied for {$totalDays} day(s) {$leaveTypeName} ({$startDate->format('d/m/Y')}).",
                'module'         => 'leave',
                'type'           => 'staff_leave_applied',
                'related_id'     => $application->id,
                'icon'           => 'fa-calendar-alt',
                'color'          => '#f59e0b',
            ]);
        } catch (\Throwable $e) {}

        return response()->json([
            'success' => true,
            'message' => 'Staff leave application submitted successfully.',
            'data'    => $application,
        ]);
    }
}
