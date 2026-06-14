<?php

namespace App\Http\Controllers\School\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class StudentIdCardController extends Controller
{
    public function generate(Student $student)
    {
        // Generate QR Code containing the student's admission number
        $qrCode = base64_encode(
            QrCode::format('png')
                ->size(150)
                ->errorCorrection('H')
                ->generate($student->admission_number)
        );

        $pdf = Pdf::loadView('school.student.id-card-pdf', compact('student', 'qrCode'))
            ->setPaper('a5', 'portrait');

        return $pdf->stream("student_id_card_{$student->id}.pdf");
    }
}
