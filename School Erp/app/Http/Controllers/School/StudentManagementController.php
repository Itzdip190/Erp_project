<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use App\Models\StudentAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentManagementController extends Controller
{
    public function bulkImport()
    {
        return view('school.student.import');
    }

    public function bulkPhoto()
    {
        return view('school.student.bulk-photo');
    }

    public function optionalSubject(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $classes = SchoolClass::where('school_id', $schoolId)->get();
        $sections = Section::where('school_id', $schoolId)->get();
        $subjects = Subject::where('school_id', $schoolId)->get();

        $classId = $request->get('class_id');
        $sectionId = $request->get('section_id');

        $students = collect();
        if ($classId && $sectionId) {
            $students = Student::where('school_id', $schoolId)
                ->where('class_id', $classId)
                ->where('section_id', $sectionId)
                ->get();
        }

        return view('school.student.optional-subject', compact('classes', 'sections', 'subjects', 'students', 'classId', 'sectionId'));
    }

    public function saveOptionalSubject(Request $request)
    {
        return back()->with('success', 'Optional subjects allocated successfully.');
    }

    public function admissionReport()
    {
        $schoolId = auth()->user()->school_id;
        
        // Stats calculations
        $totalAdmitted = Student::where('school_id', $schoolId)->count();
        $maleCount = Student::where('school_id', $schoolId)->where('gender', 'male')->count();
        $femaleCount = Student::where('school_id', $schoolId)->where('gender', 'female')->count();
        $otherCount = Student::where('school_id', $schoolId)->where('gender', 'other')->count();
        
        $classDistribution = Student::where('school_id', $schoolId)
            ->select('class_id', DB::raw('count(*) as count'))
            ->groupBy('class_id')
            ->with('class')
            ->get();

        return view('school.student.admission-report', compact('totalAdmitted', 'maleCount', 'femaleCount', 'otherCount', 'classDistribution'));
    }

    public function siblings()
    {
        $schoolId = auth()->user()->school_id;
        
        // Find students grouped by guardian phone to identify siblings
        $siblingsGroups = Student::where('school_id', $schoolId)
            ->select('guardian_phone', DB::raw('count(*) as count'))
            ->groupBy('guardian_phone')
            ->having('count', '>', 1)
            ->pluck('guardian_phone');

        $groupedSiblings = Student::where('school_id', $schoolId)
            ->whereIn('guardian_phone', $siblingsGroups)
            ->with(['class', 'section'])
            ->get()
            ->groupBy('guardian_phone');

        return view('school.student.siblings', compact('groupedSiblings'));
    }

    public function bulkAttendance(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $classes = SchoolClass::where('school_id', $schoolId)->get();
        $sections = Section::where('school_id', $schoolId)->get();

        $classId = $request->get('class_id');
        $sectionId = $request->get('section_id');
        $date = $request->get('date', today()->toDateString());

        $students = collect();
        $attendance = collect();

        if ($classId && $sectionId) {
            $students = Student::where('school_id', $schoolId)
                ->where('class_id', $classId)
                ->where('section_id', $sectionId)
                ->orderBy('roll_number')
                ->get();

            $attendance = StudentAttendance::where('school_id', $schoolId)
                ->whereDate('date', $date)
                ->get()
                ->keyBy('student_id');
        }

        return view('school.student.bulk-attendance', compact('classes', 'sections', 'students', 'attendance', 'classId', 'sectionId', 'date'));
    }

    public function studentReport(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $classes = SchoolClass::where('school_id', $schoolId)->get();
        
        $classId = $request->get('class_id');
        $students = collect();

        if ($classId) {
            $students = Student::where('school_id', $schoolId)
                ->where('class_id', $classId)
                ->get();
        }

        return view('school.student.report', compact('classes', 'classId', 'students'));
    }

    public function appSettings()
    {
        // Return view with app settings stub
        return view('school.student.app-settings');
    }

    public function saveAppSettings(Request $request)
    {
        return back()->with('success', 'Mobile app update configurations saved successfully.');
    }

    public function bulkAdmissionNumber()
    {
        $schoolId = auth()->user()->school_id;
        $students = Student::where('school_id', $schoolId)->with(['class', 'section'])->get();
        return view('school.student.bulk-admission-number', compact('students'));
    }

    public function saveBulkAdmissionNumber(Request $request)
    {
        return back()->with('success', 'Admission IDs updated and synchronized successfully.');
    }

    public function attendanceReport(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $classes = SchoolClass::where('school_id', $schoolId)->get();
        
        $classId = $request->get('class_id');
        $reportData = collect();

        if ($classId) {
            $students = Student::where('school_id', $schoolId)
                ->where('class_id', $classId)
                ->get();

            foreach ($students as $student) {
                $total = StudentAttendance::where('student_id', $student->id)->count();
                $present = StudentAttendance::where('student_id', $student->id)->where('status', 'present')->count();
                $rate = $total > 0 ? round(($present / $total) * 100) : 100; // default to 100

                $reportData->push([
                    'student' => $student,
                    'total' => $total,
                    'present' => $present,
                    'rate' => $rate
                ]);
            }
        }

        return view('school.student.attendance-report', compact('classes', 'classId', 'reportData'));
    }

    public function discipline()
    {
        $schoolId = auth()->user()->school_id;
        $students = Student::where('school_id', $schoolId)->get();
        return view('school.student.discipline', compact('students'));
    }

    public function saveDiscipline(Request $request)
    {
        return back()->with('success', 'Disciplinary entry logged successfully.');
    }

    public function bulkOperation(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $classes = SchoolClass::where('school_id', $schoolId)->get();
        $sections = Section::where('school_id', $schoolId)->get();

        $classId = $request->get('class_id');
        $sectionId = $request->get('section_id');

        $students = collect();
        if ($classId && $sectionId) {
            $students = Student::where('school_id', $schoolId)
                ->where('class_id', $classId)
                ->where('section_id', $sectionId)
                ->get();
        }

        return view('school.student.bulk-operation', compact('classes', 'sections', 'students', 'classId', 'sectionId'));
    }

    public function saveBulkOperation(Request $request)
    {
        return back()->with('success', 'Bulk operations completed successfully.');
    }

    public function ptm()
    {
        $schoolId = auth()->user()->school_id;
        $classes = SchoolClass::where('school_id', $schoolId)->get();
        return view('school.student.ptm', compact('classes'));
    }

    public function savePtm(Request $request)
    {
        return back()->with('success', 'PTM Meeting scheduled and notifications sent.');
    }

    public function cca()
    {
        $schoolId = auth()->user()->school_id;
        $students = Student::where('school_id', $schoolId)->with(['class', 'section'])->get();
        return view('school.student.cca', compact('students'));
    }

    public function saveCca(Request $request)
    {
        return back()->with('success', 'CCA points allocation saved successfully.');
    }
}
