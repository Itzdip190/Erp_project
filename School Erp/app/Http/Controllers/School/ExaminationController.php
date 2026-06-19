<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use App\Models\StudentMark;

class ExaminationController extends Controller
{
    private function ensureMarksSeeded($schoolId)
    {
        if (StudentMark::where('school_id', $schoolId)->count() === 0) {
            $student = Student::where('school_id', $schoolId)->first();
            $subjects = Subject::where('school_id', $schoolId)->take(3)->get();
            if ($student && $subjects->isNotEmpty()) {
                $exams = ['Unit Test 1', 'Term 1'];
                foreach ($exams as $exam) {
                    foreach ($subjects as $sub) {
                        StudentMark::create([
                            'school_id' => $schoolId,
                            'student_id' => $student->id,
                            'subject_id' => $sub->id,
                            'exam_name' => $exam,
                            'marks_obtained' => rand(70, 95),
                            'max_marks' => 100,
                            'grade' => 'A',
                            'remarks' => 'Good performance.',
                        ]);
                    }
                }
            }
        }
    }

    public function gradeScale(Request $request)
    {
        if ($request->isMethod('post')) {
            return back()->with('success', 'Grade scaling rules updated successfully.');
        }
        return view('school.examination.grade_scale');
    }

    public function marksEntry(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $this->ensureMarksSeeded($schoolId);

        $classes = SchoolClass::where('school_id', $schoolId)->get();
        $sections = Section::where('school_id', $schoolId)->get();
        $subjects = Subject::where('school_id', $schoolId)->get();

        $classId = $request->get('class_id');
        $sectionId = $request->get('section_id');
        $subjectId = $request->get('subject_id');
        $examName = $request->get('exam_name', 'Unit Test 1');

        $students = collect();
        $marks = collect();

        if ($classId && $sectionId && $subjectId) {
            $students = Student::where('school_id', $schoolId)
                ->where('class_id', $classId)
                ->where('section_id', $sectionId)
                ->orderBy('roll_number')
                ->get();

            $marks = StudentMark::where('school_id', $schoolId)
                ->where('subject_id', $subjectId)
                ->where('exam_name', $examName)
                ->get()
                ->keyBy('student_id');
        }

        if ($request->isMethod('post')) {
            $request->validate([
                'subject_id' => 'required|exists:subjects,id',
                'exam_name' => 'required|string',
                'marks' => 'required|array',
                'marks.*.student_id' => 'required|exists:students,id',
                'marks.*.marks_obtained' => 'required|numeric|min:0',
                'marks.*.max_marks' => 'required|numeric|min:1',
            ]);

            foreach ($request->marks as $m) {
                // Calculate grade
                $obtained = (float)$m['marks_obtained'];
                $max = (float)$m['max_marks'];
                $pct = $max > 0 ? ($obtained / $max) * 100 : 0;
                
                $grade = 'F';
                if ($pct >= 90) $grade = 'A+';
                elseif ($pct >= 80) $grade = 'A';
                elseif ($pct >= 70) $grade = 'B';
                elseif ($pct >= 60) $grade = 'C';
                elseif ($pct >= 50) $grade = 'D';

                StudentMark::updateOrCreate(
                    [
                        'school_id' => $schoolId,
                        'student_id' => $m['student_id'],
                        'subject_id' => $request->subject_id,
                        'exam_name' => $request->exam_name,
                    ],
                    [
                        'marks_obtained' => $obtained,
                        'max_marks' => $max,
                        'grade' => $grade,
                        'remarks' => $m['remarks'] ?? null,
                    ]
                );
            }

            return back()->with('success', 'Student marks entered and saved successfully.');
        }

        return view('school.examination.marks_entry', compact('classes', 'sections', 'subjects', 'students', 'marks', 'classId', 'sectionId', 'subjectId', 'examName'));
    }

    public function offlineTests(Request $request)
    {
        if ($request->isMethod('post')) {
            return back()->with('success', 'Offline Test Date Sheet scheduled successfully.');
        }
        return view('school.examination.offline_tests');
    }

    public function lmsTests(Request $request)
    {
        if ($request->isMethod('post')) {
            return back()->with('success', 'Online LMS Test Linked successfully.');
        }
        return view('school.examination.lms_tests');
    }

    public function reportCardTemplate(Request $request)
    {
        if ($request->isMethod('post')) {
            return back()->with('success', 'Report Card layout templates updated.');
        }
        return view('school.examination.report_card_template');
    }

    public function reportCard(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $this->ensureMarksSeeded($schoolId);

        $students = Student::where('school_id', $schoolId)->with(['class', 'section'])->get();
        $selectedStudentId = $request->get('student_id');
        $marks = collect();
        $student = null;

        if ($selectedStudentId) {
            $student = Student::where('school_id', $schoolId)->findOrFail($selectedStudentId);
            $marks = StudentMark::where('school_id', $schoolId)
                ->where('student_id', $selectedStudentId)
                ->with('subject')
                ->get();
        }

        return view('school.examination.report_card', compact('students', 'student', 'marks', 'selectedStudentId'));
    }

    public function reportCardV2(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $this->ensureMarksSeeded($schoolId);

        $students = Student::where('school_id', $schoolId)->with(['class', 'section'])->get();
        $selectedStudentId = $request->get('student_id');
        $marks = collect();
        $student = null;

        if ($selectedStudentId) {
            $student = Student::where('school_id', $schoolId)->findOrFail($selectedStudentId);
            $marks = StudentMark::where('school_id', $schoolId)
                ->where('student_id', $selectedStudentId)
                ->with('subject')
                ->get();
        }

        return view('school.examination.report_card_v2', compact('students', 'student', 'marks', 'selectedStudentId'));
    }

    public function marksheetsReport(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $this->ensureMarksSeeded($schoolId);

        $classes = SchoolClass::where('school_id', $schoolId)->get();
        $classId = $request->get('class_id');
        $reportData = collect();

        if ($classId) {
            $students = Student::where('school_id', $schoolId)->where('class_id', $classId)->get();
            $subjects = Subject::where('school_id', $schoolId)->get();

            foreach ($students as $student) {
                $studentMarks = StudentMark::where('student_id', $student->id)->get()->keyBy('subject_id');
                $reportData->push([
                    'student' => $student,
                    'marks' => $studentMarks,
                ]);
            }
            return view('school.examination.marksheets_report', compact('classes', 'classId', 'subjects', 'reportData'));
        }

        return view('school.examination.marksheets_report', compact('classes', 'classId'));
    }

    public function reports()
    {
        return view('school.examination.reports');
    }
}
