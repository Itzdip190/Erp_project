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
        return $query->where(function ($q) use ($user, $role) {
            if ($user) {
                $q->where('user_id', $user->id);
            }
            if ($role) {
                $q->orWhere('recipient_role', $role);
            }
            if ($user && $user->hasRole('school_admin')) {
                $q->orWhere('recipient_role', 'school_admin')
                  ->orWhere('recipient_role', 'admin');
            }
            if ($user && $user->hasRole('teacher')) {
                $q->orWhere('recipient_role', 'teacher');
            }
            if ($user && $user->hasRole('student')) {
                $q->orWhere('recipient_role', 'student');
            }
            if ($user && $user->hasRole('parent')) {
                $q->orWhere('recipient_role', 'parent');
            }
        });
    }
}
