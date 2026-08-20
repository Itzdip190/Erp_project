<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffAttendanceRegister extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'payroll_month',
        'is_frozen',
        'frozen_at',
        'frozen_by',
    ];

    protected $casts = [
        'is_frozen' => 'boolean',
        'frozen_at' => 'datetime',
    ];

    public function frozenBy()
    {
        return $this->belongsTo(User::class, 'frozen_by');
    }
}
