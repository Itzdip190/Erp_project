<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnquiryLead extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'student_name',
        'parent_name',
        'phone',
        'email',
        'class_interested',
        'status',
        'notes',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
