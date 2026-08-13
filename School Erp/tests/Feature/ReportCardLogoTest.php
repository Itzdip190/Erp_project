<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\School;
use App\Models\User;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\AcademicSession;
use App\Http\Controllers\School\ExaminationController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ReportCardLogoTest extends TestCase
{
    use RefreshDatabase;

    public function test_school_logo_rendering_and_multi_school_isolation()
    {
        Storage::fake('public');

        // Create dummy PNG content bytes for testing without requiring GD extension
        $fakePngContentA = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==');
        $fakePngContentB = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');

        $logoA = UploadedFile::fake()->createWithContent('school_a_logo.png', $fakePngContentA);
        $pathA = $logoA->store('school-logos', 'public');

        $schoolA = School::create([
            'name' => 'School A Academy',
            'code' => 'SCHA001',
            'logo' => $pathA,
            'status' => 'active',
        ]);

        $sessionA = AcademicSession::create([
            'school_id' => $schoolA->id,
            'name' => '2026-2027',
            'start_date' => '2026-04-01',
            'end_date' => '2027-03-31',
            'is_current' => true,
        ]);

        $userA = User::create([
            'name' => 'Admin School A',
            'email' => 'admin@schoola.com',
            'password' => bcrypt('password'),
            'school_id' => $schoolA->id,
        ]);

        $classA = SchoolClass::create(['school_id' => $schoolA->id, 'name' => 'Class 10', 'numeric_name' => 10]);
        $secA = Section::create(['school_id' => $schoolA->id, 'class_id' => $classA->id, 'name' => 'A']);
        $studentA = Student::create([
            'school_id' => $schoolA->id,
            'user_id' => $userA->id,
            'academic_session_id' => $sessionA->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'admission_number' => 'ADM-A-001',
            'admission_date' => '2026-04-01',
            'date_of_birth' => '2015-01-01',
            'gender' => 'male',
            'guardian_name' => 'Parent Name',
            'guardian_phone' => '9999999999',
            'guardian_relationship' => 'father',
            'address' => 'Sample Address A',
            'city' => 'City',
            'state' => 'State',
            'pincode' => '110001',
            'class_id' => $classA->id,
            'section_id' => $secA->id,
        ]);

        // Create School B with different logo
        $logoB = UploadedFile::fake()->createWithContent('school_b_logo.png', $fakePngContentB);
        $pathB = $logoB->store('school-logos', 'public');

        $schoolB = School::create([
            'name' => 'Vedant Public School B',
            'code' => 'SCHB002',
            'logo' => $pathB,
            'status' => 'active',
        ]);

        $sessionB = AcademicSession::create([
            'school_id' => $schoolB->id,
            'name' => '2026-2027',
            'start_date' => '2026-04-01',
            'end_date' => '2027-03-31',
            'is_current' => true,
        ]);

        $userB = User::create([
            'name' => 'Admin School B',
            'email' => 'admin@schoolb.com',
            'password' => bcrypt('password'),
            'school_id' => $schoolB->id,
        ]);

        $classB = SchoolClass::create(['school_id' => $schoolB->id, 'name' => 'Class 5', 'numeric_name' => 5]);
        $secB = Section::create(['school_id' => $schoolB->id, 'class_id' => $classB->id, 'name' => 'B']);
        $studentB = Student::create([
            'school_id' => $schoolB->id,
            'user_id' => $userB->id,
            'academic_session_id' => $sessionB->id,
            'first_name' => 'Abhi',
            'last_name' => 'Yanshu',
            'admission_number' => 'JPPS55',
            'admission_date' => '2026-04-01',
            'date_of_birth' => '2015-01-01',
            'gender' => 'male',
            'guardian_name' => 'Parent Name',
            'guardian_phone' => '9999999999',
            'guardian_relationship' => 'father',
            'address' => 'Sample Address B',
            'city' => 'City',
            'state' => 'State',
            'pincode' => '110001',
            'class_id' => $classB->id,
            'section_id' => $secB->id,
        ]);

        // Create School C with NO logo
        $schoolC = School::create([
            'name' => 'School C No Logo',
            'code' => 'SCHC003',
            'logo' => null,
            'status' => 'active',
        ]);

        $sessionC = AcademicSession::create([
            'school_id' => $schoolC->id,
            'name' => '2026-2027',
            'start_date' => '2026-04-01',
            'end_date' => '2027-03-31',
            'is_current' => true,
        ]);

        $userC = User::create([
            'name' => 'Admin School C',
            'email' => 'admin@schoolc.com',
            'password' => bcrypt('password'),
            'school_id' => $schoolC->id,
        ]);

        $classC = SchoolClass::create(['school_id' => $schoolC->id, 'name' => 'Class 1', 'numeric_name' => 1]);
        $secC = Section::create(['school_id' => $schoolC->id, 'class_id' => $classC->id, 'name' => 'C']);
        $studentC = Student::create([
            'school_id' => $schoolC->id,
            'user_id' => $userC->id,
            'academic_session_id' => $sessionC->id,
            'first_name' => 'Charlie',
            'last_name' => 'NoLogo',
            'admission_number' => 'ADM-C-001',
            'admission_date' => '2026-04-01',
            'date_of_birth' => '2015-01-01',
            'gender' => 'male',
            'guardian_name' => 'Parent Name',
            'guardian_phone' => '9999999999',
            'guardian_relationship' => 'father',
            'address' => 'Sample Address C',
            'city' => 'City',
            'state' => 'State',
            'pincode' => '110001',
            'class_id' => $classC->id,
            'section_id' => $secC->id,
        ]);

        $controller = new ExaminationController();

        // 1. Test School A report card HTML generation
        $this->actingAs($userA);
        $htmlA = $controller->renderStudentReportCardHtml($schoolA->id, null, $studentA);
        $this->assertStringContainsString('data:image/png;base64,', $htmlA);
        $this->assertStringContainsString('School A Academy', $htmlA);

        // 2. Test School B report card HTML generation
        $this->actingAs($userB);
        $htmlB = $controller->renderStudentReportCardHtml($schoolB->id, null, $studentB);
        $this->assertStringContainsString('data:image/png;base64,', $htmlB);
        $this->assertStringContainsString('Vedant Public School B', $htmlB);

        // Verify multi-school isolation: School A logo base64 is in School A report card, not School B
        $base64A = base64_encode($fakePngContentA);
        $base64B = base64_encode($fakePngContentB);
        
        $this->assertStringContainsString($base64A, $htmlA);
        $this->assertStringNotContainsString($base64B, $htmlA);

        $this->assertStringContainsString($base64B, $htmlB);
        $this->assertStringNotContainsString($base64A, $htmlB);

        // 3. Test School C (No logo uploaded - graceful rendering without broken img tags)
        $this->actingAs($userC);
        $htmlC = $controller->renderStudentReportCardHtml($schoolC->id, null, $studentC);
        $this->assertStringContainsString('School C No Logo', $htmlC);
        $this->assertStringNotContainsString('{$SchoolLogo}', $htmlC);

        // 4. Test Impersonation Mode: Super Admin (userA) impersonates School B
        app()->instance('currentSchool', $schoolB);
        $htmlImpersonated = $controller->renderStudentReportCardHtml($schoolB->id, null, $studentB);
        $this->assertStringContainsString($base64B, $htmlImpersonated);
        $this->assertStringNotContainsString($base64A, $htmlImpersonated);

        // 5. Verify PDF HTML structure contains valid Base64 Data URI image tags
        $this->assertMatchesRegularExpression('/<img[^>]*src=["\']data:image\/png;base64,[^"\']+["\']/i', $htmlB);
    }
}
