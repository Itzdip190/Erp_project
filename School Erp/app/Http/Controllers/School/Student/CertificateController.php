<?php

namespace App\Http\Controllers\School\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    public function generate(Student $student, string $type)
    {
        $allowedTypes = ['character', 'dob', 'bonafide', 'transfer', 'appreciation', 'achievement'];
        
        if (!in_array($type, $allowedTypes)) {
            abort(404, 'Certificate type not found.');
        }

        $title = ucwords(str_replace('_', ' ', $type)) . ' Certificate';
        $date = now()->format('d M Y');

        $pdf = Pdf::loadView("school.student.certificates.{$type}", compact('student', 'title', 'date'))
            ->setPaper('a4', 'landscape'); // Certificates are best printed in landscape layout

        return $pdf->stream("student_certificate_{$type}_{$student->id}.pdf");
    }
}
