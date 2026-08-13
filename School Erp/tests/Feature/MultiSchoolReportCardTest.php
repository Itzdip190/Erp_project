<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\School;
use App\Models\User;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Exam;
use App\Models\StudentMark;
use App\Models\AcademicSession;
use App\Models\ReportCardTemplate;
use App\Models\ReportCardTemplateMapping;
use App\Models\GradeScale;
use App\Http\Controllers\School\ExaminationController;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MultiSchoolReportCardTest extends TestCase
{
    use RefreshDatabase;

    public function test_multi_school_template_selection_isolation_and_data_binding()
    {
        $controller = new ExaminationController();

        // -------------------------------------------------------------
        // SCHOOL A: Yash International School
        // -------------------------------------------------------------
        $schoolA = School::create(['name' => 'Yash International School', 'code' => 'YAS001', 'status' => 'active']);
        $sessionA = AcademicSession::create([
            'school_id' => $schoolA->id,
            'name' => '2026-2027',
            'start_date' => '2026-04-01',
            'end_date' => '2027-03-31',
            'is_current' => true,
        ]);
        $userA = User::create(['name' => 'Admin Yash', 'email' => 'admin@yash.com', 'password' => bcrypt('password'), 'school_id' => $schoolA->id]);
        $classA = SchoolClass::create(['school_id' => $schoolA->id, 'name' => 'Class 5', 'numeric_name' => 5]);
        $secA = Section::create(['school_id' => $schoolA->id, 'class_id' => $classA->id, 'name' => 'A']);
        $subMathA = Subject::create(['school_id' => $schoolA->id, 'class_id' => $classA->id, 'name' => 'Mathematics', 'code' => 'MATH']);
        $subEngA = Subject::create(['school_id' => $schoolA->id, 'class_id' => $classA->id, 'name' => 'English', 'code' => 'ENG']);

        $studentA = Student::create([
            'school_id' => $schoolA->id,
            'user_id' => $userA->id,
            'academic_session_id' => $sessionA->id,
            'first_name' => 'Rehaan',
            'last_name' => 'Subramanian',
            'admission_number' => 'YAS/2026/00138',
            'admission_date' => '2026-04-01',
            'roll_number' => '10',
            'date_of_birth' => '2018-05-15',
            'gender' => 'male',
            'father_name' => 'Chatresh Subramanian',
            'mother_name' => 'Kashish Subramanian',
            'guardian_name' => 'Chatresh Subramanian',
            'guardian_phone' => '9999999999',
            'guardian_relationship' => 'father',
            'address' => 'Sample Address',
            'city' => 'City',
            'state' => 'State',
            'pincode' => '110001',
            'class_id' => $classA->id,
            'section_id' => $secA->id,
        ]);

        StudentMark::create([
            'school_id' => $schoolA->id,
            'student_id' => $studentA->id,
            'subject_id' => $subMathA->id,
            'exam_name' => 'TERM 1',
            'assessment_name' => 'HALF YEARLY',
            'marks_obtained' => 78,
            'max_marks' => 80,
        ]);
        StudentMark::create([
            'school_id' => $schoolA->id,
            'student_id' => $studentA->id,
            'subject_id' => $subEngA->id,
            'exam_name' => 'TERM 1',
            'assessment_name' => 'HALF YEARLY',
            'marks_obtained' => 70,
            'max_marks' => 80,
        ]);

        // Assign Sample 1 to School A
        $tmplA_Sample1 = ReportCardTemplate::create([
            'school_id' => $schoolA->id,
            'academic_session_id' => $sessionA->id,
            'name' => 'School A Sample 1',
            'sample_template_key' => 'sample_1',
            'content' => $controller->getSampleTemplateContent('sample_1'),
            'is_active' => true,
        ]);
        ReportCardTemplateMapping::create([
            'school_id' => $schoolA->id,
            'report_card_template_id' => $tmplA_Sample1->id,
            'academic_session_id' => $sessionA->id,
            'class_id' => $classA->id,
            'section_id' => $secA->id,
        ]);

        $htmlA1 = $controller->renderStudentReportCardHtml($schoolA->id, $sessionA->id, $studentA);
        $this->assertStringContainsString('Yash International School', $htmlA1);
        $this->assertStringContainsString('Rehaan Subramanian', $htmlA1);
        $this->assertStringContainsString('Mathematics', $htmlA1);
        $this->assertStringContainsString('English', $htmlA1);

        // Reassign School A to Sample 6
        $tmplA_Sample6 = ReportCardTemplate::create([
            'school_id' => $schoolA->id,
            'academic_session_id' => $sessionA->id,
            'name' => 'School A Sample 6',
            'sample_template_key' => 'sample_6',
            'content' => $controller->getSampleTemplateContent('sample_6'),
            'is_active' => true,
        ]);
        ReportCardTemplateMapping::where('school_id', $schoolA->id)->where('class_id', $classA->id)->delete();
        ReportCardTemplateMapping::create([
            'school_id' => $schoolA->id,
            'report_card_template_id' => $tmplA_Sample6->id,
            'academic_session_id' => $sessionA->id,
            'class_id' => $classA->id,
            'section_id' => $secA->id,
        ]);

        $htmlA6 = $controller->renderStudentReportCardHtml($schoolA->id, $sessionA->id, $studentA);
        $this->assertStringContainsString('Yash International School', $htmlA6);
        $this->assertStringContainsString('Rehaan Subramanian', $htmlA6);
        $this->assertStringContainsString('Mathematics', $htmlA6);
        $this->assertStringContainsString('78', $htmlA6);

        // -------------------------------------------------------------
        // SCHOOL B: Vedant Public School (Matching Screenshot 1 & 2)
        // -------------------------------------------------------------
        $schoolB = School::create(['name' => 'Vedant Public School', 'code' => 'VED002', 'status' => 'active']);
        $sessionB = AcademicSession::create([
            'school_id' => $schoolB->id,
            'name' => '2026-2027',
            'start_date' => '2026-04-01',
            'end_date' => '2027-03-31',
            'is_current' => true,
        ]);
        $userB = User::create(['name' => 'Admin Vedant', 'email' => 'admin@vedant.com', 'password' => bcrypt('password'), 'school_id' => $schoolB->id]);
        $classB = SchoolClass::create(['school_id' => $schoolB->id, 'name' => 'Class 1', 'numeric_name' => 1]);
        $secB = Section::create(['school_id' => $schoolB->id, 'class_id' => $classB->id, 'name' => 'A']);
        $subCompB = Subject::create(['school_id' => $schoolB->id, 'class_id' => $classB->id, 'name' => 'Computer', 'code' => 'COMP']);

        $studentB = Student::create([
            'school_id' => $schoolB->id,
            'user_id' => $userB->id,
            'academic_session_id' => $sessionB->id,
            'first_name' => 'Abhi',
            'last_name' => 'Yanshu',
            'admission_number' => 'JPPS55',
            'admission_date' => '2026-04-01',
            'roll_number' => '1',
            'date_of_birth' => '2014-05-16',
            'gender' => 'male',
            'father_name' => 'Anil Kumar Gupta',
            'mother_name' => 'Parent Name',
            'guardian_name' => 'Anil Kumar Gupta',
            'guardian_phone' => '8888888888',
            'guardian_relationship' => 'father',
            'address' => 'Gurgaon, Haryana',
            'city' => 'Gurgaon',
            'state' => 'Haryana',
            'pincode' => '122001',
            'class_id' => $classB->id,
            'section_id' => $secB->id,
        ]);

        StudentMark::create([
            'school_id' => $schoolB->id,
            'student_id' => $studentB->id,
            'subject_id' => $subCompB->id,
            'exam_name' => 'PA-1',
            'assessment_name' => 'Unit-1',
            'marks_obtained' => 0,
            'max_marks' => 25,
        ]);

        // Sample 1 for Vedant Public School
        $tmplB_Sample1 = ReportCardTemplate::create([
            'school_id' => $schoolB->id,
            'academic_session_id' => $sessionB->id,
            'name' => 'Vedant Sample 1',
            'sample_template_key' => 'sample_1',
            'content' => $controller->getSampleTemplateContent('sample_1'),
            'is_active' => true,
        ]);
        ReportCardTemplateMapping::create([
            'school_id' => $schoolB->id,
            'report_card_template_id' => $tmplB_Sample1->id,
            'academic_session_id' => $sessionB->id,
            'class_id' => $classB->id,
            'section_id' => $secB->id,
        ]);

        $htmlB1 = $controller->renderStudentReportCardHtml($schoolB->id, $sessionB->id, $studentB);
        $this->assertStringContainsString('Vedant Public School', $htmlB1);
        $this->assertStringContainsString('Abhi Yanshu', $htmlB1);
        $this->assertStringContainsString('Computer', $htmlB1);
        $this->assertStringNotContainsString('Yash International School', $htmlB1);

        // Sample 6 for Vedant Public School
        $tmplB_Sample6 = ReportCardTemplate::create([
            'school_id' => $schoolB->id,
            'academic_session_id' => $sessionB->id,
            'name' => 'Vedant Sample 6',
            'sample_template_key' => 'sample_6',
            'content' => $controller->getSampleTemplateContent('sample_6'),
            'is_active' => true,
        ]);
        ReportCardTemplateMapping::where('school_id', $schoolB->id)->where('class_id', $classB->id)->delete();
        ReportCardTemplateMapping::create([
            'school_id' => $schoolB->id,
            'report_card_template_id' => $tmplB_Sample6->id,
            'academic_session_id' => $sessionB->id,
            'class_id' => $classB->id,
            'section_id' => $secB->id,
        ]);

        $htmlB6 = $controller->renderStudentReportCardHtml($schoolB->id, $sessionB->id, $studentB);
        $this->assertStringContainsString('Vedant Public School', $htmlB6);
        $this->assertStringContainsString('Abhi Yanshu', $htmlB6);
        $this->assertStringContainsString('Computer', $htmlB6);
        // Ensure static hardcoded subjects (HINDI 70 B2, etc.) from static template do NOT leak
        $this->assertStringNotContainsString('HINDI', $htmlB6);
        $this->assertStringNotContainsString('456/600', $htmlB6);

        // -------------------------------------------------------------
        // SCHOOL C: Greenwood High (Custom Exam & Assessment Setup)
        // -------------------------------------------------------------
        $schoolC = School::create(['name' => 'Greenwood High', 'code' => 'GWH003', 'status' => 'active']);
        $sessionC = AcademicSession::create([
            'school_id' => $schoolC->id,
            'name' => '2026-2027',
            'start_date' => '2026-04-01',
            'end_date' => '2027-03-31',
            'is_current' => true,
        ]);
        $userC = User::create(['name' => 'Admin Greenwood', 'email' => 'admin@greenwood.com', 'password' => bcrypt('password'), 'school_id' => $schoolC->id]);
        $classC = SchoolClass::create(['school_id' => $schoolC->id, 'name' => 'Grade 10', 'numeric_name' => 10]);
        $secC = Section::create(['school_id' => $schoolC->id, 'class_id' => $classC->id, 'name' => 'B']);

        $subPhyC = Subject::create(['school_id' => $schoolC->id, 'class_id' => $classC->id, 'name' => 'Physics', 'code' => 'PHY']);
        $subChemC = Subject::create(['school_id' => $schoolC->id, 'class_id' => $classC->id, 'name' => 'Chemistry', 'code' => 'CHEM']);

        $studentC = Student::create([
            'school_id' => $schoolC->id,
            'user_id' => $userC->id,
            'academic_session_id' => $sessionC->id,
            'first_name' => 'Aarav',
            'last_name' => 'Patel',
            'admission_number' => 'GWH/2026/99',
            'admission_date' => '2026-04-01',
            'roll_number' => '15',
            'date_of_birth' => '2012-08-20',
            'gender' => 'male',
            'father_name' => 'Vikram Patel',
            'mother_name' => 'Neha Patel',
            'guardian_name' => 'Vikram Patel',
            'guardian_phone' => '7777777777',
            'guardian_relationship' => 'father',
            'address' => 'Ahmedabad, Gujarat',
            'city' => 'Ahmedabad',
            'state' => 'Gujarat',
            'pincode' => '380001',
            'class_id' => $classC->id,
            'section_id' => $secC->id,
        ]);

        StudentMark::create([
            'school_id' => $schoolC->id,
            'student_id' => $studentC->id,
            'subject_id' => $subPhyC->id,
            'exam_name' => 'Mid Term',
            'assessment_name' => 'Theory',
            'marks_obtained' => 88,
            'max_marks' => 100,
        ]);
        StudentMark::create([
            'school_id' => $schoolC->id,
            'student_id' => $studentC->id,
            'subject_id' => $subChemC->id,
            'exam_name' => 'Mid Term',
            'assessment_name' => 'Theory',
            'marks_obtained' => 94,
            'max_marks' => 100,
        ]);

        // Sample 6 for Greenwood High
        $tmplC_Sample6 = ReportCardTemplate::create([
            'school_id' => $schoolC->id,
            'academic_session_id' => $sessionC->id,
            'name' => 'Greenwood Sample 6',
            'sample_template_key' => 'sample_6',
            'content' => $controller->getSampleTemplateContent('sample_6'),
            'is_active' => true,
        ]);
        ReportCardTemplateMapping::create([
            'school_id' => $schoolC->id,
            'report_card_template_id' => $tmplC_Sample6->id,
            'academic_session_id' => $sessionC->id,
            'class_id' => $classC->id,
            'section_id' => $secC->id,
        ]);

        $htmlC6 = $controller->renderStudentReportCardHtml($schoolC->id, $sessionC->id, $studentC);
        $this->assertStringContainsString('Greenwood High', $htmlC6);
        $this->assertStringContainsString('Aarav Patel', $htmlC6);
        $this->assertStringContainsString('Physics', $htmlC6);
        $this->assertStringContainsString('Chemistry', $htmlC6);
        $this->assertStringContainsString('88', $htmlC6);
        $this->assertStringContainsString('94', $htmlC6);
        $this->assertStringNotContainsString('Yash International School', $htmlC6);
        $this->assertStringNotContainsString('Vedant Public School', $htmlC6);
    }

    public function test_no_duplicate_exam_columns_when_assessment_names_match_marks()
    {
        $controller = new ExaminationController();

        $school = School::create(['name' => 'Vedant Public School Test', 'code' => 'VPS002', 'status' => 'active']);
        $session = AcademicSession::create([
            'school_id' => $school->id,
            'name' => '2026-2027',
            'start_date' => '2026-04-01',
            'end_date' => '2027-03-31',
            'is_current' => true,
        ]);
        $user = User::create(['name' => 'Admin VPS', 'email' => 'admin@vps.com', 'password' => bcrypt('password'), 'school_id' => $school->id]);
        $class = SchoolClass::create(['school_id' => $school->id, 'name' => 'Class-1', 'numeric_name' => 1]);
        $sec = Section::create(['school_id' => $school->id, 'class_id' => $class->id, 'name' => 'A']);

        $examPA1 = Exam::create([
            'school_id' => $school->id,
            'class_id' => $class->id,
            'academic_session_id' => $session->id,
            'name' => 'PA-1',
            'start_date' => '2026-07-13',
            'end_date' => '2026-07-20',
        ]);
        $subComp = Subject::create(['school_id' => $school->id, 'class_id' => $class->id, 'name' => 'Computer', 'code' => 'COMP']);
        $subConv = Subject::create(['school_id' => $school->id, 'class_id' => $class->id, 'name' => 'Conversation', 'code' => 'CONV']);

        if (\Schema::hasTable('exam_assessments')) {
            \DB::table('exam_assessments')->insert([
                'school_id' => $school->id,
                'class_id' => $class->id,
                'exam_id' => $examPA1->id,
                'subject_id' => $subComp->id,
                'name' => 'Unit-1',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $student = Student::create([
            'school_id' => $school->id,
            'user_id' => $user->id,
            'academic_session_id' => $session->id,
            'first_name' => 'Abhi',
            'last_name' => 'Yanshu',
            'admission_number' => 'JPPS55',
            'admission_date' => '2026-04-01',
            'roll_number' => '1',
            'date_of_birth' => '2014-05-16',
            'gender' => 'male',
            'father_name' => 'Anil Kumar Gupta',
            'mother_name' => 'Parent Name',
            'guardian_name' => 'Anil Kumar Gupta',
            'guardian_phone' => '9451805575',
            'guardian_relationship' => 'father',
            'address' => 'Gurgaon',
            'city' => 'Gurgaon',
            'state' => 'Haryana',
            'pincode' => '122001',
            'class_id' => $class->id,
            'section_id' => $sec->id,
        ]);

        StudentMark::create([
            'school_id' => $school->id,
            'student_id' => $student->id,
            'subject_id' => $subComp->id,
            'exam_name' => 'PA-1',
            'assessment_name' => 'Unit-1',
            'marks_obtained' => 24,
            'max_marks' => 25,
        ]);
        StudentMark::create([
            'school_id' => $school->id,
            'student_id' => $student->id,
            'subject_id' => $subConv->id,
            'exam_name' => 'PA-1',
            'assessment_name' => 'Unit-1',
            'marks_obtained' => 22,
            'max_marks' => 25,
        ]);

        $tmpl = ReportCardTemplate::create([
            'school_id' => $school->id,
            'academic_session_id' => $session->id,
            'name' => 'VPS Sample 6',
            'sample_template_key' => 'sample_6',
            'content' => $controller->getSampleTemplateContent('sample_6'),
            'is_active' => true,
        ]);
        ReportCardTemplateMapping::create([
            'school_id' => $school->id,
            'report_card_template_id' => $tmpl->id,
            'academic_session_id' => $session->id,
            'class_id' => $class->id,
            'section_id' => $sec->id,
        ]);

        $html = $controller->renderStudentReportCardHtml($school->id, $session->id, $student);

        $this->assertStringContainsString('PA-1', $html);
        $this->assertStringContainsString('Unit-1', $html);
        $this->assertStringContainsString('Computer', $html);
        $this->assertStringContainsString('Conversation', $html);
        $this->assertStringContainsString('24', $html);
        $this->assertStringContainsString('22', $html);
        $this->assertStringContainsString('46 / 50', $html);
    }
}
