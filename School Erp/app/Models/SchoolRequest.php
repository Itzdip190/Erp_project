<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'phone',
        'address',
        'admin_name',
        'admin_email',
        'admin_password',
        'plan_id',
        'status',
        'rejected_reason',
        'state',
        'school_type',
        'director_name',
        'email',
        'academic_session_name',
        'academic_session_start_date',
        'academic_session_end_date',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
