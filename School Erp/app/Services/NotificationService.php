<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\TeacherNotification;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class NotificationService
{
    /**
     * Publish a centralized notification.
     *
     * @param array $data
     * @return Notification
     */
    public static function send(array $data): Notification
    {
        $schoolId = $data['school_id'] ?? (app()->bound('currentSchool') ? app('currentSchool')?->id : null);
        if (!$schoolId && auth()->check()) {
            $schoolId = auth()->user()->school_id;
        }

        $notification = Notification::create([
            'school_id'      => $schoolId,
            'user_id'        => $data['user_id'] ?? null,
            'recipient_role' => $data['recipient_role'] ?? null,
            'title'          => $data['title'] ?? 'Notification',
            'message'        => $data['message'] ?? '',
            'module'         => $data['module'] ?? 'general',
            'type'           => $data['type'] ?? 'info',
            'related_id'     => $data['related_id'] ?? null,
            'priority'       => $data['priority'] ?? 'normal',
            'action_url'     => $data['action_url'] ?? null,
            'icon'           => $data['icon'] ?? self::getDefaultIcon($data['module'] ?? 'general'),
            'color'          => $data['color'] ?? self::getDefaultColor($data['module'] ?? 'general'),
            'is_read'        => false,
        ]);

        // Dual-write to TeacherNotification if recipient is a teacher for legacy support
        if (($data['recipient_role'] ?? '') === 'teacher' || (!empty($data['user_id']) && Schema::hasTable('teacher_notifications'))) {
            try {
                if (Schema::hasTable('teacher_notifications')) {
                    TeacherNotification::create([
                        'school_id'            => $schoolId,
                        'user_id'              => $data['user_id'] ?? null,
                        'staff_id'             => $data['staff_id'] ?? null,
                        'title'                => $data['title'],
                        'message'              => $data['message'],
                        'type'                 => $data['type'] ?? 'general',
                        'leave_application_id' => $data['related_id'] ?? null,
                        'is_read'              => false,
                    ]);
                }
            } catch (\Throwable $e) {
                // Ignore failure on legacy table
            }
        }

        return $notification;
    }

    /**
     * Fetch unread count for current user / role.
     */
    public static function getUnreadCount($user = null): int
    {
        $user = $user ?: auth()->user();
        if (!$user) return 0;

        $role = self::getUserRole($user);
        $schoolId = $user->school_id ?: (app()->bound('currentSchool') ? app('currentSchool')?->id : null);

        $query = Notification::where('is_read', false);
        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }

        $query->forRecipient($user, $role);

        return $query->count();
    }

    /**
     * Fetch notifications for current user / role.
     */
    public static function getNotifications($user = null, $limit = 20, bool $unreadOnly = false)
    {
        $user = $user ?: auth()->user();
        if (!$user) return collect();

        $role = self::getUserRole($user);
        $schoolId = $user->school_id ?: (app()->bound('currentSchool') ? app('currentSchool')?->id : null);

        $query = Notification::query();
        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }

        if ($unreadOnly) {
            $query->where('is_read', false);
        }

        $query->forRecipient($user, $role);

        return $query->orderByDesc('created_at')->limit($limit)->get();
    }

    /**
     * Get default icon per module.
     */
    public static function getDefaultIcon(string $module): string
    {
        return match ($module) {
            'leave'         => 'fa-calendar-check',
            'admission'     => 'fa-user-plus',
            'student'       => 'fa-user-graduate',
            'teacher'       => 'fa-chalkboard-teacher',
            'homework'      => 'fa-book-open',
            'exam'          => 'fa-file-signature',
            'fee'           => 'fa-receipt',
            'attendance'    => 'fa-user-clock',
            'communication' => 'fa-bullhorn',
            'transport'     => 'fa-bus',
            'certificate'   => 'fa-certificate',
            default         => 'fa-bell',
        };
    }

    /**
     * Get default color per module.
     */
    public static function getDefaultColor(string $module): string
    {
        return match ($module) {
            'leave'         => '#8b5cf6',
            'admission'     => '#10b981',
            'student'       => '#3b82f6',
            'teacher'       => '#f59e0b',
            'homework'      => '#6366f1',
            'exam'          => '#ec4899',
            'fee'           => '#059669',
            'attendance'    => '#d97706',
            'communication' => '#8b5cf6',
            'transport'     => '#0284c7',
            'certificate'   => '#7c3aed',
            default         => '#64748b',
        };
    }

    private static function getUserRole($user): ?string
    {
        if ($user->hasRole('school_admin') || $user->hasRole('admin')) return 'school_admin';
        if ($user->hasRole('teacher') || $user->role === 'teacher') return 'teacher';
        if ($user->hasRole('student') || $user->role === 'student') return 'student';
        if ($user->hasRole('parent') || $user->role === 'parent') return 'parent';
        return $user->role ?? null;
    }
}
