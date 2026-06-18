<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentCertificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'student_id',
        'certificate_template_id',
        'certificate_number',
        'issue_date',
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
        return $this->belongsTo(CertificateTemplate::class, 'certificate_template_id');
    }
}
