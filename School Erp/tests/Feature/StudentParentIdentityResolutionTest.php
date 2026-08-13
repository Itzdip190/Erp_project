<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use App\Models\Scopes\SchoolScope;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StudentParentIdentityResolutionTest extends TestCase
{
    use RefreshDatabase;

    protected School $school;
    protected SchoolClass $class;
    protected Section $section;
    protected AcademicSession $session;
    protected Student $student;
    protected User $parentUser;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'student']);
        Role::firstOrCreate(['name' => 'parent']);

        $this->school = School::create([
            'name'          => 'Test Apex Academy',
            'code'          => 'APEX',
            'custom_domain' => 'apex.educorp.com',
            'status'        => 'active',
        ]);

        $this->class = SchoolClass::withoutGlobalScope(SchoolScope::class)->create([
            'school_id'    => $this->school->id,
            'name'         => 'Grade 10',
            'numeric_name' => 10,
        ]);

        $this->section = Section::withoutGlobalScope(SchoolScope::class)->create([
            'school_id' => $this->school->id,
            'class_id'  => $this->class->id,
            'name'      => 'A',
        ]);

        $this->session = AcademicSession::withoutGlobalScope(SchoolScope::class)->create([
            'school_id'  => $this->school->id,
            'name'       => '2026-2027',
            'start_date' => '2026-04-01',
            'end_date'   => '2027-03-31',
            'is_current' => true,
        ]);

        // Create Parent user explicitly
        $this->parentUser = User::withoutGlobalScope(SchoolScope::class)->create([
            'school_id' => $this->school->id,
            'name'      => 'Ayushman Bobal',
            'email'     => 'ayushman.parent@apex.com',
            'phone'     => '9832365333',
            'password'  => Hash::make('Parent@2026!'),
            'is_active' => true,
        ]);
        $this->parentUser->assignRole('parent');

        // Create Student record
        $this->student = Student::withoutGlobalScope(SchoolScope::class)->create([
            'school_id'           => $this->school->id,
            'admission_number'    => 'ADM10015',
            'first_name'          => 'Aarush',
            'last_name'           => 'Bobal',
            'email'               => 'aarush.student@apex.com',
            'phone'               => '9832365999',
            'class_id'            => $this->class->id,
            'section_id'          => $this->section->id,
            'academic_session_id' => $this->session->id,
            'gender'              => 'Male',
            'date_of_birth'       => '2012-05-15',
            'admission_date'      => '2026-04-01',
            'father_name'         => 'Ayushman Bobal',
            'father_phone'        => '9832365333',
            'father_email'        => 'ayushman.parent@apex.com',
            'guardian_name'         => 'Ayushman Bobal',
            'guardian_relationship' => 'Father',
            'guardian_phone'        => '9832365333',
            'guardian_email'      => 'ayushman.parent@apex.com',
            'address'               => '123 Main Street',
            'city'                  => 'Test City',
            'state'                 => 'Test State',
            'pincode'               => '123456',
            'is_active'             => true,
        ]);
    }

    /**
     * Test 1: Login via Admission ID -> Redirects to Student Dashboard.
     */
    public function test_login_via_admission_id_redirects_to_student_dashboard()
    {
        $response = $this->withSession(['school_code' => 'APEX'])
            ->post('/login', [
                'email'    => 'ADM10015',
                'password' => 'Student@2026!',
            ]);

        $response->assertRedirect('/student/dashboard');
        $this->assertAuthenticated();
        $this->assertTrue(auth()->user()->hasRole('student'));
    }

    /**
     * Test 2: Login via Student Email -> Redirects to Student Dashboard.
     */
    public function test_login_via_student_email_redirects_to_student_dashboard()
    {
        $response = $this->withSession(['school_code' => 'APEX'])
            ->post('/login', [
                'email'    => 'aarush.student@apex.com',
                'password' => 'Student@2026!',
            ]);

        $response->assertRedirect('/student/dashboard');
        $this->assertAuthenticated();
        $this->assertTrue(auth()->user()->hasRole('student'));
    }

    /**
     * Test 3: Login via Student Mobile -> Redirects to Student Dashboard.
     */
    public function test_login_via_student_mobile_redirects_to_student_dashboard()
    {
        $response = $this->withSession(['school_code' => 'APEX'])
            ->post('/login', [
                'email'    => '9832365999',
                'password' => 'Student@2026!',
            ]);

        $response->assertRedirect('/student/dashboard');
        $this->assertAuthenticated();
        $this->assertTrue(auth()->user()->hasRole('student'));
    }

    /**
     * Test 4: Login via Parent Email -> Redirects to Parent Dashboard.
     */
    public function test_login_via_parent_email_redirects_to_parent_dashboard()
    {
        $response = $this->withSession(['school_code' => 'APEX'])
            ->post('/login', [
                'email'    => 'ayushman.parent@apex.com',
                'password' => 'Parent@2026!',
            ]);

        $response->assertRedirect('/parent/dashboard');
        $this->assertAuthenticated();
        $this->assertTrue(auth()->user()->hasRole('parent'));
    }

    /**
     * Test 5: Login via Parent Mobile -> Redirects to Parent Dashboard.
     */
    public function test_login_via_parent_mobile_redirects_to_parent_dashboard()
    {
        $response = $this->withSession(['school_code' => 'APEX'])
            ->post('/login', [
                'email'    => '9832365333',
                'password' => 'Parent@2026!',
            ]);

        $response->assertRedirect('/parent/dashboard');
        $this->assertAuthenticated();
        $this->assertTrue(auth()->user()->hasRole('parent'));
    }

    /**
     * Edge Case Test: Shared contact number used as guardian_phone for student.
     * When student has no personal phone and uses guardian_phone, logging in with student credentials/password
     * prioritizes Student account.
     */
    public function test_shared_contact_prioritizes_student_when_logging_in_as_student()
    {
        // Set student phone to same as father phone
        $this->student->update(['phone' => '9832365333']);

        $response = $this->withSession(['school_code' => 'APEX'])
            ->post('/login', [
                'email'    => '9832365333',
                'password' => 'Student@2026!',
            ]);

        $response->assertRedirect('/student/dashboard');
        $this->assertAuthenticated();
        $this->assertTrue(auth()->user()->hasRole('student'));
    }
}
