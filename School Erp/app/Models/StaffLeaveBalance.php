<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffLeaveBalance extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'staff_id',
        'leave_type_id',
        'allowed',
        'availed',
    ];

    protected $casts = [
        'allowed' => 'decimal:2',
        'availed' => 'decimal:2',
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
