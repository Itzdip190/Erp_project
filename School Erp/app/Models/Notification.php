<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class Notification extends Model
{
    use HasFactory, BelongsToSchool;

    protected $table = 'notifications';

    protected $fillable = [
        'school_id',
        'user_id',
        'recipient_role',
        'title',
        'message',
        'module',
        'type',
        'related_id',
        'priority',
        'action_url',
        'icon',
        'color',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeForRecipient($query, $user, $role = null)
    {
        if (!$user) {
            return $query;
        }

        $userId = $user->id;
        $userRoles = [];
        if ($role) {
            $userRoles[] = $role;
        }
        if ($user->hasRole('school_admin') || ($user->role ?? null) === 'school_admin' || $user->hasRole('admin') || ($user->role ?? null) === 'admin') {
            $userRoles[] = 'school_admin';
            $userRoles[] = 'admin';
        }
        if ($user->hasRole('teacher') || ($user->role ?? null) === 'teacher') {
            $userRoles[] = 'teacher';
        }
        if ($user->hasRole('student') || ($user->role ?? null) === 'student') {
            $userRoles[] = 'student';
        }
        if ($user->hasRole('parent') || ($user->role ?? null) === 'parent') {
            $userRoles[] = 'parent';
        }
        $userRoles = array_values(array_unique(array_filter($userRoles)));

        return $query->where(function ($q) use ($userId, $userRoles) {
            // 1. Personal notification targeted directly to this user ID
            $q->where('user_id', $userId);

            // 2. Role-based broadcast notifications (where user_id is NULL) matching the user's role(s)
            if (!empty($userRoles)) {
                $q->orWhere(function ($subQ) use ($userRoles) {
                    $subQ->whereNull('user_id')
                         ->whereIn('recipient_role', $userRoles);
                });
            }
        });
    }
}
