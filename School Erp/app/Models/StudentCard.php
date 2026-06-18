<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'student_id',
        'card_template_id',
        'card_number',
        'expiry_date',
        'status',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function template()
    {
        return $this->belongsTo(CardTemplate::class, 'card_template_id');
    }
}
