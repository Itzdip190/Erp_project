<?php

namespace App\Http\Controllers\Api\V1\Staff;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\Notification;
use App\Models\Staff;
use App\Models\User;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StaffChatApiController extends Controller
{
    /**
     * Get active conversations and contact roster for the authenticated staff.
     */
    public function conversations(Request $request): JsonResponse
    {
        $user = auth()->user();
        $schoolId = $user->school_id ?? 1;
        $authId = $user->id;

        // Fetch recent messages involving this user
        $recentMessages = ChatMessage::where('school_id', $schoolId)
            ->where(function ($q) use ($authId) {
                $q->where('sender_id', $authId)->orWhere('receiver_id', $authId);
            })
            ->latest('id')
            ->limit(300)
            ->get();

        $userMessages = $recentMessages->groupBy(function ($msg) use ($authId) {
            return (int) $msg->sender_id === (int) $authId ? (int) $msg->receiver_id : (int) $msg->sender_id;
        });

        // Unread notifications for chat
        $unreadNotifs = Notification::where('user_id', $authId)
            ->where('module', 'communication')
            ->where('is_read', false)
            ->get(['action_url']);

        // Fetch relevant school users (Admins, Teachers, Staff)
        $users = User::where('school_id', $schoolId)
            ->where('id', '!=', $authId)
            ->select('id', 'name', 'email', 'phone', 'role', 'photo', 'school_id')
            ->with(['roles:id,name'])
            ->get();

        $conversations = $users->map(function ($u) use ($userMessages, $unreadNotifs) {
            $msgs = $userMessages->get($u->id);
            $lastMsg = $msgs ? $msgs->first() : null;

            $roleName = strtolower($u->roles->first()?->name ?? $u->role ?? 'user');
            if (str_contains($roleName, 'teacher')) {
                $roleCategory = 'teacher';
            } elseif (str_contains($roleName, 'parent') || str_contains($roleName, 'student')) {
                $roleCategory = 'parent';
            } elseif (str_contains($roleName, 'driver')) {
                $roleCategory = 'driver';
            } else {
                $roleCategory = 'admin';
            }

            $displayRole = ucfirst($u->roles->first()?->name ?? $u->role ?? 'Staff');

            $unreadCount = $unreadNotifs->filter(function ($n) use ($u) {
                return str_contains($n->action_url ?? '', 'user_id=' . $u->id);
            })->count();

            $avatarUrl = null;
            if ($u->photo) {
                $avatarUrl = str_starts_with($u->photo, 'http') ? $u->photo : asset('storage/' . ltrim($u->photo, '/'));
            }

            return [
                'id' => $u->id,
                'user_id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'phone' => $u->phone,
                'role' => $roleCategory,
                'display_role' => $displayRole,
                'avatar' => $avatarUrl,
                'last_message' => $lastMsg ? $lastMsg->message : null,
                'last_message_at' => $lastMsg && $lastMsg->created_at ? $lastMsg->created_at->toIso8601String() : null,
                'last_message_time' => $lastMsg && $lastMsg->created_at ? $lastMsg->created_at->format('g:i A') : null,
                'unread_count' => $unreadCount,
            ];
        })
        ->sort(function ($a, $b) {
            if ($a['last_message_at'] && $b['last_message_at']) {
                return $b['last_message_at'] <=> $a['last_message_at'];
            }
            if ($a['last_message_at']) return -1;
            if ($b['last_message_at']) return 1;
            return strnatcasecmp($a['name'], $b['name']);
        })
        ->values();

        return response()->json([
            'success' => true,
            'data' => $conversations,
        ]);
    }

    /**
     * Get messages between authenticated staff and a target contact.
     */
    public function messages(Request $request): JsonResponse
    {
        $user = auth()->user();
        $schoolId = $user->school_id ?? 1;
        $authId = $user->id;

        $targetUserId = (int) $request->input('contact_id', $request->input('user_id'));
        $afterId = (int) $request->input('after_id', 0);

        if (!$targetUserId) {
            return response()->json([
                'success' => false,
                'message' => 'Contact ID is required.',
            ], 422);
        }

        $targetUser = User::where('school_id', $schoolId)
            ->where('id', $targetUserId)
            ->with(['roles:id,name'])
            ->first(['id', 'name', 'role', 'photo']);

        if (!$targetUser) {
            return response()->json([
                'success' => false,
                'message' => 'Contact user not found.',
            ], 404);
        }

        $query = ChatMessage::where('school_id', $schoolId)
            ->where(function ($q) use ($targetUserId, $authId) {
                $q->where(function ($sub) use ($targetUserId, $authId) {
                    $sub->where('sender_id', $authId)->where('receiver_id', $targetUserId);
                })->orWhere(function ($sub) use ($targetUserId, $authId) {
                    $sub->where('sender_id', $targetUserId)->where('receiver_id', $authId);
                });
            });

        if ($afterId > 0) {
            $query->where('id', '>', $afterId);
        }

        $chatLogs = $query->orderBy('id', 'asc')->get();

        // Mark unread notifications from this contact as read
        Notification::where('user_id', $authId)
            ->where('module', 'communication')
            ->where('is_read', false)
            ->where(function ($q) use ($targetUserId) {
                $q->where('action_url', 'like', '%user_id=' . $targetUserId)
                  ->orWhere('action_url', 'like', '%user_id=' . $targetUserId . '&%');
            })
            ->update(['is_read' => true, 'read_at' => now()]);

        $formatted = $chatLogs->map(function ($msg) use ($authId) {
            return [
                'id' => $msg->id,
                'sender_id' => $msg->sender_id,
                'receiver_id' => $msg->receiver_id,
                'message' => $msg->message,
                'is_me' => (int) $msg->sender_id === (int) $authId,
                'time' => $msg->created_at ? $msg->created_at->format('g:i A') : 'Just now',
                'created_at' => $msg->created_at ? $msg->created_at->toIso8601String() : null,
            ];
        });

        $displayRole = ucfirst($targetUser->roles->first()?->name ?? $targetUser->role ?? 'User');
        $avatarUrl = null;
        if ($targetUser->photo) {
            $avatarUrl = str_starts_with($targetUser->photo, 'http') ? $targetUser->photo : asset('storage/' . ltrim($targetUser->photo, '/'));
        }

        return response()->json([
            'success' => true,
            'contact' => [
                'id' => $targetUser->id,
                'name' => $targetUser->name,
                'display_role' => $displayRole,
                'avatar' => $avatarUrl,
            ],
            'messages' => $formatted,
        ]);
    }

    /**
     * Send a chat message (synced directly with ERP Communication Chat).
     */
    public function send(Request $request): JsonResponse
    {
        $user = auth()->user();
        $schoolId = $user->school_id ?? 1;
        $authId = $user->id;

        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:5000',
        ]);

        $receiverId = (int) $request->receiver_id;
        $chatMsg = ChatMessage::create([
            'school_id' => $schoolId,
            'sender_id' => $authId,
            'receiver_id' => $receiverId,
            'message' => trim($request->message),
        ]);

        // Dispatch central notification to receiver
        if (class_exists(NotificationService::class)) {
            try {
                NotificationService::send([
                    'school_id'    => $schoolId,
                    'user_id'      => $receiverId,
                    'title'        => 'New Message from ' . $user->name,
                    'message'      => Str::limit($request->message, 80),
                    'module'       => 'communication',
                    'type'         => 'chat',
                    'related_id'   => $chatMsg->id,
                    'icon'         => 'fa-comments',
                    'color'        => '#2563eb',
                    'action_url'   => route('school.communication.chat', ['user_id' => $authId]),
                ]);
            } catch (\Throwable $e) {}
        }

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $chatMsg->id,
                'sender_id' => $chatMsg->sender_id,
                'receiver_id' => $chatMsg->receiver_id,
                'message' => $chatMsg->message,
                'is_me' => true,
                'time' => $chatMsg->created_at ? $chatMsg->created_at->format('g:i A') : 'Just now',
                'created_at' => $chatMsg->created_at ? $chatMsg->created_at->toIso8601String() : now()->toIso8601String(),
            ],
        ]);
    }
}
