<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentLeaveDeclaration extends Model
{
    use HasFactory, SoftDeletes, BelongsToSchool;

    protected $table = 'student_leave_declarations';

    protected $fillable = [
        'school_id',
        'title',
        'declaration_text',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];
}
