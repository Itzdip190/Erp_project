<?php

namespace App\Http\Controllers\School\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;

class AdmitCardController extends Controller
{
    public function generate(Student $student, $examId = null)
    {
        // Mock exam timetable data (since Exam Management is in later phases)
        $timetable = [
            ['date' => '2026-06-15', 'subject' => 'English', 'time' => '09:00 AM - 12:00 PM', 'room' => '101'],
            ['date' => '2026-06-17', 'subject' => 'Mathematics', 'time' => '09:00 AM - 12:00 PM', 'room' => '102'],
            ['date' => '2026-06-19', 'subject' => 'Science', 'time' => '09:00 AM - 12:00 PM', 'room' => '103'],
            ['date' => '2026-06-22', 'subject' => 'History', 'time' => '09:00 AM - 12:00 PM', 'room' => '101'],
            ['date' => '2026-06-24', 'subject' => 'Computer Science', 'time' => '09:00 AM - 12:00 PM', 'room' => 'Lab B'],
        ];

        $examName = 'First Term Examination 2026';

        $pdf = Pdf::loadView('school.student.admit-card-pdf', compact('student', 'timetable', 'examName'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream("student_admit_card_{$student->id}.pdf");
    }
}
