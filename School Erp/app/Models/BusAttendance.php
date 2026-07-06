<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusAttendance extends Model
{
    use HasFactory, BelongsToSchool;

    protected $table = 'bus_attendances';

    protected $fillable = [
        'school_id',
        'student_id',
        'date',
        'trip_type',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
