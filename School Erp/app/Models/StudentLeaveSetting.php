<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentLeaveSetting extends Model
{
    use HasFactory, BelongsToSchool;

    protected $table = 'student_leave_settings';

    protected $fillable = [
        'school_id',
        'use_acknowledgement',
    ];

    protected $casts = [
        'use_acknowledgement' => 'boolean',
    ];
}
