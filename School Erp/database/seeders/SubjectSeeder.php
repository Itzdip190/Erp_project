<?php

namespace Database\Seeders;

use App\Models\AcademicSession;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\SectionSubjectStaff;
use App\Models\Staff;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('code', 'YIS2024')->firstOrFail();
        $class9 = SchoolClass::where('school_id', $school->id)->where('name', 'Class 9')->firstOrFail();
        $section9A = Section::where('school_id', $school->id)->where('class_id', $class9->id)->where('name', 'A')->firstOrFail();
        $session = AcademicSession::where('school_id', $school->id)->where('is_current', true)->firstOrFail();
        $teacher = Staff::where('school_id', $school->id)->where('employee_id', 'EMP001')->firstOrFail();

        $subjects = [
            ['name' => 'English', 'code' => 'ENG9', 'type' => 'theory'],
            ['name' => 'Mathematics', 'code' => 'MATH9', 'type' => 'theory'],
            ['name' => 'Science', 'code' => 'SCI9', 'type' => 'both'],
            ['name' => 'History', 'code' => 'HIST9', 'type' => 'theory'],
            ['name' => 'Computer Science', 'code' => 'CS9', 'type' => 'practical'],
        ];

        foreach ($subjects as $subjectData) {
            $subject = Subject::firstOrCreate(
                [
                    'school_id' => $school->id,
                    'class_id' => $class9->id,
                    'code' => $subjectData['code'],
                ],
                [
                    'name' => $subjectData['name'],
                    'type' => $subjectData['type'],
                    'max_marks' => 100,
                    'pass_marks' => 33,
                ]
            );

            // Assign subject and teacher to Class 9 - Section A for this session
            SectionSubjectStaff::firstOrCreate(
                [
                    'school_id' => $school->id,
                    'section_id' => $section9A->id,
                    'subject_id' => $subject->id,
                    'academic_session_id' => $session->id,
                ],
                [
                    'staff_id' => $teacher->id,
                ]
            );
        }
    }
}
