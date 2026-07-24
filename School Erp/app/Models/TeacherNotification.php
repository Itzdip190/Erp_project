<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherNotification extends Model
{
    use HasFactory;

    protected $table = 'teacher_notifications';

    protected $fillable = [
        'school_id',
        'staff_id',
        'user_id',
        'title',
        'message',
        'type',
        'leave_application_id',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function leaveApplication()
    {
        return $this->belongsTo(StaffLeaveApplication::class, 'leave_application_id');
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }
}
