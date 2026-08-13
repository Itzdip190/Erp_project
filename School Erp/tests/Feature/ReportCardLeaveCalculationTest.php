<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\School;
use App\Models\User;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
use App\Models\StudentMark;
use App\Models\AcademicSession;
use App\Http\Controllers\School\ExaminationController;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReportCardLeaveCalculationTest extends TestCase
{
    use RefreshDatabase;

    private $controller;
    private $school;
    private $session;
    private $student;
    private $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new ExaminationController();
        $this->school = School::create(['name' => 'Test School', 'code' => 'TST001', 'status' => 'active']);
        $this->session = AcademicSession::create([
            'school_id' => $this->school->id,
            'name' => '2026-2027',
            'start_date' => '2026-04-01',
            'end_date' => '2027-03-31',
            'is_current' => true,
        ]);
        $user = User::create(['name' => 'Admin Test', 'email' => 'admin@test.com', 'password' => bcrypt('password'), 'school_id' => $this->school->id]);
        $class = SchoolClass::create(['school_id' => $this->school->id, 'name' => 'Class 1', 'numeric_name' => 1]);
        $section = Section::create(['school_id' => $this->school->id, 'class_id' => $class->id, 'name' => 'A']);
        $this->subject = Subject::create(['school_id' => $this->school->id, 'class_id' => $class->id, 'name' => 'Computer', 'code' => 'COMP']);

        $this->student = Student::create([
            'school_id' => $this->school->id,
            'user_id' => $user->id,
            'academic_session_id' => $this->session->id,
            'class_id' => $class->id,
            'section_id' => $section->id,
            'first_name' => 'Aarav',
            'last_name' => 'Sharma',
            'admission_number' => 'ADM001',
            'roll_number' => '01',
            'date_of_birth' => '2018-01-01',
            'gender' => 'male',
            'admission_date' => '2026-04-01',
            'father_name' => 'Father Name',
            'mother_name' => 'Mother Name',
            'guardian_name' => 'Father Name',
            'guardian_relationship' => 'Father',
            'guardian_phone' => '9999999999',
            'address' => '123 Test Street',
            'city' => 'Delhi',
            'state' => 'Delhi',
            'pincode' => '110001',
        ]);
    }

    public function test_scenario_1_present_plus_present()
    {
        StudentMark::create([
            'school_id' => $this->school->id,
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'class_id' => $this->student->class_id,
            'section_id' => $this->student->section_id,
            'exam_name' => 'Term 1',
            'assessment_name' => 'Unit 1',
            'marks_obtained' => 40,
            'max_marks' => 50,
            'attendance_status' => 'present',
        ]);

        StudentMark::create([
            'school_id' => $this->school->id,
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'class_id' => $this->student->class_id,
            'section_id' => $this->student->section_id,
            'exam_name' => 'Term 1',
            'assessment_name' => 'Unit 2',
            'marks_obtained' => 40,
            'max_marks' => 50,
            'attendance_status' => 'present',
        ]);

        $html = $this->controller->renderStudentReportCardHtml($this->school->id, $this->session->id, $this->student, 'All Exams', null, [
            'consider_absent' => true,
            'consider_medical_leave' => true,
        ]);

        $this->assertStringContainsString('80 / 100', $html);
    }

    public function test_scenario_2_absent_plus_present_checkbox_enabled()
    {
        StudentMark::create([
            'school_id' => $this->school->id,
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'class_id' => $this->student->class_id,
            'section_id' => $this->student->section_id,
            'exam_name' => 'Term 1',
            'assessment_name' => 'Unit 1',
            'marks_obtained' => 0,
            'max_marks' => 50,
            'attendance_status' => 'absent',
        ]);

        StudentMark::create([
            'school_id' => $this->school->id,
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'class_id' => $this->student->class_id,
            'section_id' => $this->student->section_id,
            'exam_name' => 'Term 1',
            'assessment_name' => 'Unit 2',
            'marks_obtained' => 40,
            'max_marks' => 50,
            'attendance_status' => 'present',
        ]);

        $html = $this->controller->renderStudentReportCardHtml($this->school->id, $this->session->id, $this->student, 'All Exams', null, [
            'consider_absent' => true,
            'consider_medical_leave' => true,
        ]);

        $this->assertStringContainsString('Absent', $html);
        $this->assertStringContainsString('40 / 100', $html);
    }

    public function test_scenario_3_absent_plus_present_checkbox_disabled()
    {
        StudentMark::create([
            'school_id' => $this->school->id,
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'class_id' => $this->student->class_id,
            'section_id' => $this->student->section_id,
            'exam_name' => 'Term 1',
            'assessment_name' => 'Unit 1',
            'marks_obtained' => 0,
            'max_marks' => 50,
            'attendance_status' => 'absent',
        ]);

        StudentMark::create([
            'school_id' => $this->school->id,
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'class_id' => $this->student->class_id,
            'section_id' => $this->student->section_id,
            'exam_name' => 'Term 1',
            'assessment_name' => 'Unit 2',
            'marks_obtained' => 40,
            'max_marks' => 50,
            'attendance_status' => 'present',
        ]);

        $html = $this->controller->renderStudentReportCardHtml($this->school->id, $this->session->id, $this->student, 'All Exams', null, [
            'consider_absent' => false,
            'consider_medical_leave' => true,
        ]);

        $this->assertStringContainsString('Absent', $html);
        $this->assertStringContainsString('40 / 50', $html);
    }

    public function test_scenario_4_medical_leave_plus_present_checkbox_enabled()
    {
        StudentMark::create([
            'school_id' => $this->school->id,
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'class_id' => $this->student->class_id,
            'section_id' => $this->student->section_id,
            'exam_name' => 'Term 1',
            'assessment_name' => 'Unit 1',
            'marks_obtained' => 0,
            'max_marks' => 50,
            'attendance_status' => 'medical_leave',
        ]);

        StudentMark::create([
            'school_id' => $this->school->id,
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'class_id' => $this->student->class_id,
            'section_id' => $this->student->section_id,
            'exam_name' => 'Term 1',
            'assessment_name' => 'Unit 2',
            'marks_obtained' => 40,
            'max_marks' => 50,
            'attendance_status' => 'present',
        ]);

        $html = $this->controller->renderStudentReportCardHtml($this->school->id, $this->session->id, $this->student, 'All Exams', null, [
            'consider_absent' => true,
            'consider_medical_leave' => true,
        ]);

        $this->assertStringContainsString('Medical Leave', $html);
        $this->assertStringContainsString('40 / 100', $html);
    }

    public function test_scenario_5_medical_leave_plus_present_checkbox_disabled()
    {
        StudentMark::create([
            'school_id' => $this->school->id,
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'class_id' => $this->student->class_id,
            'section_id' => $this->student->section_id,
            'exam_name' => 'Term 1',
            'assessment_name' => 'Unit 1',
            'marks_obtained' => 0,
            'max_marks' => 50,
            'attendance_status' => 'medical_leave',
        ]);

        StudentMark::create([
            'school_id' => $this->school->id,
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'class_id' => $this->student->class_id,
            'section_id' => $this->student->section_id,
            'exam_name' => 'Term 1',
            'assessment_name' => 'Unit 2',
            'marks_obtained' => 40,
            'max_marks' => 50,
            'attendance_status' => 'present',
        ]);

        $html = $this->controller->renderStudentReportCardHtml($this->school->id, $this->session->id, $this->student, 'All Exams', null, [
            'consider_absent' => true,
            'consider_medical_leave' => false,
        ]);

        $this->assertStringContainsString('Medical Leave', $html);
        $this->assertStringContainsString('40 / 50', $html);
    }

    public function test_scenario_6_multiple_absent_assessments()
    {
        StudentMark::create([
            'school_id' => $this->school->id,
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'class_id' => $this->student->class_id,
            'section_id' => $this->student->section_id,
            'exam_name' => 'Term 1',
            'assessment_name' => 'Unit 1',
            'marks_obtained' => 0,
            'max_marks' => 50,
            'attendance_status' => 'absent',
        ]);

        StudentMark::create([
            'school_id' => $this->school->id,
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'class_id' => $this->student->class_id,
            'section_id' => $this->student->section_id,
            'exam_name' => 'Term 1',
            'assessment_name' => 'Unit 2',
            'marks_obtained' => 0,
            'max_marks' => 50,
            'attendance_status' => 'absent',
        ]);

        StudentMark::create([
            'school_id' => $this->school->id,
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'class_id' => $this->student->class_id,
            'section_id' => $this->student->section_id,
            'exam_name' => 'Term 1',
            'assessment_name' => 'Unit 3',
            'marks_obtained' => 30,
            'max_marks' => 50,
            'attendance_status' => 'present',
        ]);

        // When absent excluded: max is 50, obt is 30 -> 30 / 50
        $htmlExcluded = $this->controller->renderStudentReportCardHtml($this->school->id, $this->session->id, $this->student, 'All Exams', null, [
            'consider_absent' => false,
            'consider_medical_leave' => false,
        ]);

        $this->assertStringContainsString('30 / 50', $htmlExcluded);

        // When absent included: max is 150, obt is 30 -> 30 / 150
        $htmlIncluded = $this->controller->renderStudentReportCardHtml($this->school->id, $this->session->id, $this->student, 'All Exams', null, [
            'consider_absent' => true,
            'consider_medical_leave' => true,
        ]);

        $this->assertStringContainsString('30 / 150', $htmlIncluded);
    }

    public function test_scenario_8_combination_of_present_absent_medical_leave()
    {
        StudentMark::create([
            'school_id' => $this->school->id,
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'class_id' => $this->student->class_id,
            'section_id' => $this->student->section_id,
            'exam_name' => 'Term 1',
            'assessment_name' => 'Unit 1',
            'marks_obtained' => 40,
            'max_marks' => 50,
            'attendance_status' => 'present',
        ]);

        StudentMark::create([
            'school_id' => $this->school->id,
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'class_id' => $this->student->class_id,
            'section_id' => $this->student->section_id,
            'exam_name' => 'Term 1',
            'assessment_name' => 'Unit 2',
            'marks_obtained' => 0,
            'max_marks' => 50,
            'attendance_status' => 'absent',
        ]);

        StudentMark::create([
            'school_id' => $this->school->id,
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'class_id' => $this->student->class_id,
            'section_id' => $this->student->section_id,
            'exam_name' => 'Term 1',
            'assessment_name' => 'Unit 3',
            'marks_obtained' => 0,
            'max_marks' => 50,
            'attendance_status' => 'medical_leave',
        ]);

        // Exclude Absent (false), Include Medical Leave (true)
        // Present (40/50) + Absent (Excluded) + ML (0/50 included)
        // Total Obt = 40, Total Max = 100
        $html = $this->controller->renderStudentReportCardHtml($this->school->id, $this->session->id, $this->student, 'All Exams', null, [
            'consider_absent' => false,
            'consider_medical_leave' => true,
        ]);

        $this->assertStringContainsString('Absent', $html);
        $this->assertStringContainsString('Medical Leave', $html);
        $this->assertStringContainsString('40 / 100', $html);
    }

    public function test_user_exact_example_scenarios_1_to_4()
    {
        // Assessment 1: 100 max, Absent, 0 marks
        StudentMark::create([
            'school_id' => $this->school->id,
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'class_id' => $this->student->class_id,
            'section_id' => $this->student->section_id,
            'exam_name' => 'Term 2',
            'assessment_name' => 'Assessment 1',
            'marks_obtained' => 0,
            'max_marks' => 100,
            'attendance_status' => 'absent',
        ]);

        // Assessment 2: 50 max, Medical Leave, 0 marks
        StudentMark::create([
            'school_id' => $this->school->id,
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'class_id' => $this->student->class_id,
            'section_id' => $this->student->section_id,
            'exam_name' => 'Term 2',
            'assessment_name' => 'Assessment 2',
            'marks_obtained' => 0,
            'max_marks' => 50,
            'attendance_status' => 'medical_leave',
        ]);

        // Assessment 3: 100 max, Present, 45 marks
        StudentMark::create([
            'school_id' => $this->school->id,
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'class_id' => $this->student->class_id,
            'section_id' => $this->student->section_id,
            'exam_name' => 'Term 2',
            'assessment_name' => 'Assessment 3',
            'marks_obtained' => 45,
            'max_marks' => 100,
            'attendance_status' => 'present',
        ]);

        // Scenario 1: Both enabled -> 45 / 250 -> 18%
        $html1 = $this->controller->renderStudentReportCardHtml($this->school->id, $this->session->id, $this->student, 'Term 2', null, [
            'consider_absent' => true,
            'consider_medical_leave' => true,
        ]);
        $this->assertStringContainsString('45 / 250', $html1);

        // Scenario 2: Both disabled -> 45 / 100 -> 45%
        $html2 = $this->controller->renderStudentReportCardHtml($this->school->id, $this->session->id, $this->student, 'Term 2', null, [
            'consider_absent' => false,
            'consider_medical_leave' => false,
        ]);
        $this->assertStringContainsString('45 / 100', $html2);

        // Scenario 3: Only Absent enabled -> 45 / 200 -> 22.5%
        $html3 = $this->controller->renderStudentReportCardHtml($this->school->id, $this->session->id, $this->student, 'Term 2', null, [
            'consider_absent' => true,
            'consider_medical_leave' => false,
        ]);
        $this->assertStringContainsString('45 / 200', $html3);

        // Scenario 4: Only Medical Leave enabled -> 45 / 150 -> 30%
        $html4 = $this->controller->renderStudentReportCardHtml($this->school->id, $this->session->id, $this->student, 'Term 2', null, [
            'consider_absent' => false,
            'consider_medical_leave' => true,
        ]);
        $this->assertStringContainsString('45 / 150', $html4);
    }
}
