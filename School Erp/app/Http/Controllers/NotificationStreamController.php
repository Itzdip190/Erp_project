<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\Notification;
use App\Models\StaffLeaveApplication;
use App\Models\StaffLeaveBalance;
use App\Models\Staff;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class NotificationStreamController extends Controller
{
    /**
     * Server-Sent Events (SSE) Stream Endpoint.
     */
    public function stream(Request $request): StreamedResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->stream(function () {
                echo "event: error\ndata: " . json_encode(['error' => 'Unauthenticated']) . "\n\n";
            }, 401, ['Content-Type' => 'text/event-stream']);
        }

        // Close session write to unlock session files for other concurrent requests
        session_write_close();

        $response = new StreamedResponse(function () use ($user) {
            $lastCheckTime = Carbon::now()->subSeconds(5);

            // Send initial connection event
            echo "event: connected\ndata: " . json_encode(['status' => 'connected', 'timestamp' => time()]) . "\n\n";
            @ob_flush();
            @flush();

            $iterations = 0;
            // Run loop for maximum 25 seconds before graceful reconnect
            while ($iterations < 12) {
                if (connection_aborted()) {
                    break;
                }

                $user->refresh();
                $unreadCount = NotificationService::getUnreadCount($user);

                // Fetch new notifications since last check
                $role = $user->hasRole('school_admin') ? 'school_admin' : ($user->hasRole('teacher') ? 'teacher' : ($user->hasRole('student') ? 'student' : ($user->hasRole('parent') ? 'parent' : null)));
                $schoolId = $user->school_id ?: (app()->bound('currentSchool') ? app('currentSchool')?->id : null);

                $newNotifsQuery = Notification::where('created_at', '>=', $lastCheckTime);
                if ($schoolId) {
                    $newNotifsQuery->where('school_id', $schoolId);
                }
                $newNotifs = $newNotifsQuery->forRecipient($user, $role)->orderByDesc('id')->get();

                if ($newNotifs->count() > 0) {
                    $lastCheckTime = Carbon::now();
                    $payload = [
                        'type'         => 'new_notifications',
                        'unread_count' => $unreadCount,
                        'items'        => $newNotifs->map(function ($n) {
                            return [
                                'id'         => $n->id,
                                'title'      => $n->title,
                                'message'    => $n->message,
                                'module'     => $n->module,
                                'type'       => $n->type,
                                'icon'       => $n->icon,
                                'color'      => $n->color,
                                'time'       => $n->created_at->diffForHumans(),
                                'action_url' => $n->action_url,
                            ];
                        }),
                    ];
                    echo "event: notification\ndata: " . json_encode($payload) . "\n\n";
                    @ob_flush();
                    @flush();
                } else {
                    // Send heartbeat ping every 5 seconds to keep connection alive
                    echo "event: ping\ndata: " . json_encode(['unread_count' => $unreadCount, 'timestamp' => time()]) . "\n\n";
                    @ob_flush();
                    @flush();
                }

                sleep(2);
                $iterations++;
            }
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response->headers->set('Connection', 'keep-alive');
        $response->headers->set('X-Accel-Buffering', 'no');

        return $response;
    }

    /**
     * Fetch latest notifications via AJAX fallback.
     */
    public function fetchLatest(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $unreadCount = NotificationService::getUnreadCount($user);
        $notifications = NotificationService::getNotifications($user, 25);

        $items = $notifications->map(function ($n) {
            return [
                'id'         => $n->id,
                'title'      => $n->title,
                'message'    => $n->message,
                'module'     => $n->module,
                'type'       => $n->type,
                'icon'       => $n->icon ?: NotificationService::getDefaultIcon($n->module),
                'color'      => $n->color ?: NotificationService::getDefaultColor($n->module),
                'is_read'    => (bool) $n->is_read,
                'time'       => $n->created_at->diffForHumans(),
                'date_str'   => $n->created_at->format('d M Y, h:i A'),
                'action_url' => $n->action_url,
            ];
        });

        return response()->json([
            'unread_count'  => $unreadCount,
            'notifications' => $items,
        ]);
    }

    /**
     * Mark single notification as read.
     */
    public function markAsRead($id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $notification = Notification::where('id', $id)->first();
        if ($notification) {
            $notification->update([
                'is_read' => true,
                'read_at' => Carbon::now(),
            ]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read for current user.
     */
    public function markAllRead()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $role = $user->hasRole('school_admin') ? 'school_admin' : ($user->hasRole('teacher') ? 'teacher' : ($user->hasRole('student') ? 'student' : ($user->hasRole('parent') ? 'parent' : null)));
        $schoolId = $user->school_id ?: (app()->bound('currentSchool') ? app('currentSchool')?->id : null);

        $query = Notification::where('is_read', false);
        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }

        $query->forRecipient($user, $role)->update([
            'is_read' => true,
            'read_at' => Carbon::now(),
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Dynamic Teacher Leave Data JSON Endpoint (History + Balances).
     */
    public function getTeacherLeaveHistoryJson(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $staff = Staff::where('user_id', $user->id)->first();
        if (!$staff) {
            return response()->json(['error' => 'Staff record not found'], 404);
        }

        $schoolId = $user->school_id ?: (app()->bound('currentSchool') ? app('currentSchool')?->id : null);

        $applications = StaffLeaveApplication::where('staff_id', $staff->id)
            ->where('school_id', $schoolId)
            ->orderByDesc('id')
            ->get()
            ->map(function ($app) {
                $start = $app->start_date ? Carbon::parse($app->start_date) : null;
                $end   = $app->end_date ? Carbon::parse($app->end_date) : null;
                $duration = ($start && $end) ? ($start->diffInDays($end) + 1) : $app->total_days;

                return [
                    'id'               => $app->id,
                    'leave_type'       => $app->leave_type_name ?? $app->leave_type_code ?? 'Leave',
                    'start_date_fmt'   => $start ? $start->format('d/m/Y') : '',
                    'end_date_fmt'     => $end ? $end->format('d/m/Y') : '',
                    'duration'         => $duration,
                    'status'           => strtolower($app->status),
                    'status_label'     => ucfirst($app->status),
                    'admin_remark'     => $app->admin_remark,
                    'rejection_reason' => $app->rejection_reason,
                    'reason'           => $app->reason,
                    'created_at_fmt'   => $app->created_at ? $app->created_at->format('d M Y, h:i A') : '',
                ];
            });

        // Balances
        $balances = StaffLeaveBalance::where('staff_id', $staff->id)
            ->where('school_id', $schoolId)
            ->with('leaveType')
            ->get()
            ->map(function ($b) {
                return [
                    'leave_type' => $b->leaveType?->name ?? 'Leave',
                    'allowed'    => (float) $b->allocated_days,
                    'availed'    => (float) $b->used_days,
                    'remaining'  => (float) $b->remaining_days,
                ];
            });

        return response()->json([
            'success'      => true,
            'applications' => $applications,
            'balances'     => $balances,
        ]);
    }

    /**
     * Dynamic Admin Staff Leave Requests JSON Endpoint.
     */
    public function getAdminLeaveRequestsJson(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $schoolId = $user->school_id ?: (app()->bound('currentSchool') ? app('currentSchool')?->id : null);

        $pendingCount = StaffLeaveApplication::where('school_id', $schoolId)->where('status', 'pending')->count();
        $approvedCount = StaffLeaveApplication::where('school_id', $schoolId)->where('status', 'approved')->count();
        $rejectedCount = StaffLeaveApplication::where('school_id', $schoolId)->where('status', 'rejected')->count();

        $requests = StaffLeaveApplication::with(['staff', 'leaveType'])
            ->where('school_id', $schoolId)
            ->orderByDesc('id')
            ->limit(30)
            ->get()
            ->map(function ($app) {
                $staffName = $app->staff ? trim($app->staff->first_name . ' ' . ($app->staff->last_name ?? '')) : 'Staff';
                $start = $app->start_date ? Carbon::parse($app->start_date) : null;
                $end   = $app->end_date ? Carbon::parse($app->end_date) : null;

                return [
                    'id'             => $app->id,
                    'staff_name'     => $staffName,
                    'staff_id_code'  => $app->staff?->employee_id ?? 'EMP'.$app->staff_id,
                    'leave_type'     => $app->leave_type_name ?? $app->leave_type_code ?? 'Leave',
                    'applied_date'   => $app->created_at ? $app->created_at->format('d/m/Y') : '',
                    'start_date_fmt' => $start ? $start->format('d/m/Y') : '',
                    'end_date_fmt'   => $end ? $end->format('d/m/Y') : '',
                    'total_days'     => $app->total_days,
                    'reason'         => $app->reason,
                    'status'         => strtolower($app->status),
                    'admin_remark'   => $app->admin_remark,
                ];
            });

        return response()->json([
            'pending_count'  => $pendingCount,
            'approved_count' => $approvedCount,
            'rejected_count' => $rejectedCount,
            'requests'       => $requests,
        ]);
    }
}
