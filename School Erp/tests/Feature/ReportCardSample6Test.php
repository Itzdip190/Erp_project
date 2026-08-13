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
use App\Http\Controllers\School\ExaminationController;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReportCardSample6Test extends TestCase
{
    use RefreshDatabase;

    public function test_sample_6_template_selection_and_data_binding()
    {
        // 1. Create School A
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
        
        $subMath = Subject::create(['school_id' => $schoolA->id, 'class_id' => $classA->id, 'name' => 'Mathematics', 'code' => 'MATH']);
        $subEng = Subject::create(['school_id' => $schoolA->id, 'class_id' => $classA->id, 'name' => 'English', 'code' => 'ENG']);

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

        $examTerm1 = Exam::create(['school_id' => $schoolA->id, 'class_id' => $classA->id, 'name' => 'TERM 1 (HALF YEARLY)', 'term' => 'Term 1']);
        $examTerm2 = Exam::create(['school_id' => $schoolA->id, 'class_id' => $classA->id, 'name' => 'TERM 2 (ANNUAL)', 'term' => 'Term 2']);

        StudentMark::create([
            'school_id' => $schoolA->id,
            'student_id' => $studentA->id,
            'subject_id' => $subMath->id,
            'exam_name' => 'TERM 1 (HALF YEARLY)',
            'assessment_name' => 'HALF YEARLY EXAM',
            'marks_obtained' => 75,
            'max_marks' => 80,
        ]);
        StudentMark::create([
            'school_id' => $schoolA->id,
            'student_id' => $studentA->id,
            'subject_id' => $subEng->id,
            'exam_name' => 'TERM 1 (HALF YEARLY)',
            'assessment_name' => 'HALF YEARLY EXAM',
            'marks_obtained' => 65,
            'max_marks' => 80,
        ]);

        $controller = new ExaminationController();

        // Create Sample 6 template
        $tmplSample6 = ReportCardTemplate::create([
            'school_id' => $schoolA->id,
            'academic_session_id' => $sessionA->id,
            'name' => 'Sample 6 Template',
            'sample_template_key' => 'sample_6',
            'content' => $controller->getSampleTemplateContent('sample_6'),
            'is_active' => true,
        ]);

        ReportCardTemplateMapping::create([
            'school_id' => $schoolA->id,
            'report_card_template_id' => $tmplSample6->id,
            'academic_session_id' => $sessionA->id,
            'class_id' => $classA->id,
            'section_id' => $secA->id,
        ]);

        // Render report card for Student A
        $html = $controller->renderStudentReportCardHtml($schoolA->id, $sessionA->id, $studentA);

        // Assert Sample 6 layout features are present
        $this->assertStringContainsString('Yash International School', $html);
        $this->assertStringContainsString('Rehaan Subramanian', $html);
        $this->assertStringContainsString('YAS/2026/00138', $html);
        $this->assertStringContainsString('Mathematics', $html);
        $this->assertStringContainsString('English', $html);
        $this->assertStringContainsString('75', $html);
        $this->assertStringContainsString('65', $html);

        // Assert logo image tag is properly rendered and not printed as raw text
        \Illuminate\Support\Facades\Storage::fake('public');
        $fakePngContent = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==');
        $fakeLogo = \Illuminate\Http\UploadedFile::fake()->createWithContent('logo.png', $fakePngContent);
        $schoolA->update(['logo' => $fakeLogo->store('school-logos', 'public')]);

        $htmlWithLogo = $controller->renderStudentReportCardHtml($schoolA->id, $sessionA->id, $studentA);
        $this->assertStringContainsString('<img src="data:image/png;base64,', $htmlWithLogo);
        $this->assertStringNotContainsString('data:image/png;base64,iVBORw0KG', strip_tags($htmlWithLogo));

        // Test standalone tag conversion ({$SchoolLogo} inserted as plain text in template)
        $tmplSample6->update(['content' => str_replace('<img src="{$SchoolLogo}"', '{$SchoolLogo} <img src=""', $tmplSample6->content)]);
        $htmlConverted = $controller->renderStudentReportCardHtml($schoolA->id, $sessionA->id, $studentA);
        $this->assertStringContainsString('<img src="data:image/png;base64,', $htmlConverted);

        // Assert it did NOT fall back to sample_1 header styling or hardcoded dummy values
        $this->assertStringNotContainsString('456/600', $html);
    }
}
