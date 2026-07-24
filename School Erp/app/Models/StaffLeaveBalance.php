<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffLeaveBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'staff_id',
        'leave_type_id',
        'allowed',
        'availed',
    ];

    protected $casts = [
        'allowed' => 'float',
        'availed' => 'float',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }
}
