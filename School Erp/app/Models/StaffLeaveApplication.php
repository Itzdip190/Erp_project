<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffLeaveApplication extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::saved(function ($application) {
            static::syncStaffLeaveBalance($application);
            \App\Services\LeaveAttendanceService::syncStaffLeaveAttendance($application);
        });

        static::deleted(function ($application) {
            $application->status = 'deleted';
            static::syncStaffLeaveBalance($application);
            \App\Services\LeaveAttendanceService::syncStaffLeaveAttendance($application);
        });
    }

    public static function syncStaffLeaveBalance($application)
    {
        if (!$application->school_id || !$application->staff_id || !$application->leave_type_id) {
            return;
        }

        $totalAvailed = (float) static::where('school_id', $application->school_id)
            ->where('staff_id', $application->staff_id)
            ->where(function ($q) use ($application) {
                $q->where('leave_type_id', $application->leave_type_id);
                if (!empty($application->leave_type_code)) {
                    $q->orWhere('leave_type_code', $application->leave_type_code);
                }
            })
            ->where('status', 'approved')
            ->sum('total_days');

        $bal = StaffLeaveBalance::where('school_id', $application->school_id)
            ->where('staff_id', $application->staff_id)
            ->where('leave_type_id', $application->leave_type_id)
            ->first();

        if ($bal) {
            $bal->update(['availed' => $totalAvailed]);
        } else {
            $lt = LeaveType::find($application->leave_type_id);
            $allowed = $lt?->leave_count ?? 12;
            StaffLeaveBalance::create([
                'school_id'     => $application->school_id,
                'staff_id'      => $application->staff_id,
                'leave_type_id' => $application->leave_type_id,
                'allowed'       => $allowed,
                'availed'       => $totalAvailed,
            ]);
        }
    }

    protected $table = 'staff_leave_applications';

    protected $fillable = [
        'school_id',
        'staff_id',
        'academic_year',
        'staff_type',
        'leave_type_id',
        'leave_type_code',
        'leave_type_name',
        'start_date',
        'end_date',
        'total_days',
        'substitute_staff_id',
        'substitute_staff_name',
        'reason',
        'status',
        'rejection_reason',
        'admin_remark',
        'approved_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'total_days' => 'float',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function substituteStaff()
    {
        return $this->belongsTo(Staff::class, 'substitute_staff_id');
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }

    public function getLeaveTypeAttribute()
    {
        return $this->leave_type_name ?? $this->leave_type_code ?? $this->leaveType?->name ?? 'Leave';
    }
}
