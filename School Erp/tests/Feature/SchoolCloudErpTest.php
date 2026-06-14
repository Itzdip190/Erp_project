<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use App\Models\Subscription;
use App\Models\Plan;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SchoolCloudErpTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed the database
        $this->seed(DatabaseSeeder::class);
    }

    /**
     * Test role-based web redirects on successful login.
     */
    public function test_web_login_redirects_by_role(): void
    {
        // 1. Test SuperAdmin redirect
        $superAdmin = User::where('email', 'superadmin@schoolcloud.com')->first();
        $response = $this->post('/login', [
            'email' => 'superadmin@schoolcloud.com',
            'password' => 'SuperAdminSecurePass2026!',
        ]);
        $response->assertRedirect('/superadmin/dashboard');

        $this->post('/logout');

        // 2. Test School Admin redirect
        $schoolAdmin = User::where('email', 'admin@yis.com')->first();
        $response = $this->post('/login', [
            'email' => 'admin@yis.com',
            'password' => 'SchoolAdminSecurePass2026!',
        ]);
        $response->assertRedirect('/school/dashboard');

        $this->post('/logout');

        // 3. Test Parent redirect
        $parent = User::where('email', 'parent@yis.com')->first();
        $response = $this->post('/login', [
            'email' => 'parent@yis.com',
            'password' => 'ParentSecurePass2026!',
        ]);
        $response->assertRedirect('/parent/dashboard');
    }

    /**
     * Test school-scoped tenancy works via host or header.
     */
    public function test_school_middleware_resolves_tenant(): void
    {
        $schoolAdmin = User::where('email', 'admin@yis.com')->first();

        // Test with custom domain in production context (via host)
        $school = School::where('code', 'YIS2024')->first();
        $school->update(['custom_domain' => 'yis.schoolcloud.com']);

        // Set X-School-Code to an invalid one so it bypasses testing override and checks custom_domain
        $response = $this->actingAs($schoolAdmin)->withHeaders([
            'X-School-Code' => 'INVALID_CODE_123'
        ])->get('http://yis.schoolcloud.com/school/dashboard');

        $response->assertStatus(200);

        // Test with invalid domain
        $response = $this->actingAs($schoolAdmin)->withHeaders([
            'X-School-Code' => 'INVALID_CODE_123'
        ])->get('http://unknown.schoolcloud.com/school/dashboard');
        
        $response->assertStatus(404);
    }

    /**
     * Test tenanted Student CRUD.
     */
    public function test_student_management_crud(): void
    {
        $schoolAdmin = User::where('email', 'admin@yis.com')->first();
        $class = SchoolClass::where('school_id', $schoolAdmin->school_id)->first();
        $section = Section::where('class_id', $class->id)->first();
        $session = AcademicSession::where('school_id', $schoolAdmin->school_id)->first();

        // 1. Store Student
        $response = $this->actingAs($schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->post('/school/students', [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'date_of_birth' => '2012-05-15',
                'gender' => 'male',
                'guardian_name' => 'Richard Doe',
                'guardian_phone' => '9876543210',
                'guardian_email' => 'richard@doe.com',
                'guardian_relationship' => 'father',
                'address' => '123 Main St',
                'city' => 'Metropolis',
                'state' => 'NY',
                'pincode' => '10001',
                'class_id' => $class->id,
                'section_id' => $section->id,
                'academic_session_id' => $session->id,
                'admission_date' => '2026-06-01',
                'opening_due_balance' => 100.00,
            ]);

        $response->assertRedirect('/school/students');
        
        $student = Student::where('first_name', 'John')->first();
        $this->assertNotNull($student);
        $this->assertStringStartsWith('YAS/', $student->admission_number);

        // 2. Edit/Update Student
        $response = $this->actingAs($schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->put("/school/students/{$student->id}", [
                'first_name' => 'John Edited',
                'last_name' => 'Doe',
                'date_of_birth' => '2012-05-15',
                'gender' => 'male',
                'guardian_name' => 'Richard Doe',
                'guardian_phone' => '9876543210',
                'guardian_email' => 'richard@doe.com',
                'guardian_relationship' => 'father',
                'address' => '123 Main St Updated',
                'city' => 'Metropolis',
                'state' => 'NY',
                'pincode' => '10001',
                'class_id' => $class->id,
                'section_id' => $section->id,
                'academic_session_id' => $session->id,
                'admission_date' => '2026-06-01',
                'opening_due_balance' => 100.00,
            ]);

        $response->assertRedirect('/school/students');
        $this->assertEquals('John Edited', $student->refresh()->first_name);

        // 3. Delete (Soft delete) Student
        $response = $this->actingAs($schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->delete("/school/students/{$student->id}");

        $response->assertRedirect('/school/students');
        $this->assertTrue($student->refresh()->trashed());
    }

    /**
     * Test school-scoped validation.
     */
    public function test_school_scoped_validation_fails_for_cross_tenant_ids(): void
    {
        $schoolAdmin = User::where('email', 'admin@yis.com')->first();
        
        // Create another school and a class for it
        $otherSchool = School::create([
            'name' => 'Other School',
            'code' => 'OTH2026',
            'status' => 'active'
        ]);
        $otherClass = SchoolClass::create([
            'school_id' => $otherSchool->id,
            'name' => 'Other Class',
            'numeric_name' => 1
        ]);
        $otherSection = Section::create([
            'school_id' => $otherSchool->id,
            'class_id' => $otherClass->id,
            'name' => 'A'
        ]);
        $otherSession = AcademicSession::create([
            'school_id' => $otherSchool->id,
            'name' => '2025-26',
            'start_date' => '2025-04-01',
            'end_date' => '2026-03-31',
            'is_current' => true
        ]);

        // Trying to save student in YIS2024 but with class_id/section_id/session_id of Other School
        $response = $this->actingAs($schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->post('/school/students', [
                'first_name' => 'Invalid',
                'last_name' => 'Student',
                'date_of_birth' => '2012-05-15',
                'gender' => 'male',
                'guardian_name' => 'Richard Doe',
                'guardian_phone' => '9876543210',
                'guardian_email' => 'richard@doe.com',
                'guardian_relationship' => 'father',
                'address' => '123 Main St',
                'city' => 'Metropolis',
                'state' => 'NY',
                'pincode' => '10001',
                'class_id' => $otherClass->id,
                'section_id' => $otherSection->id,
                'academic_session_id' => $otherSession->id,
                'admission_date' => '2026-06-01',
            ]);

        $response->assertSessionHasErrors(['class_id', 'section_id', 'academic_session_id']);
    }

    /**
     * Test Attendance marking.
     */
    public function test_attendance_marking(): void
    {
        $schoolAdmin = User::where('email', 'admin@yis.com')->first();
        $class = SchoolClass::where('school_id', $schoolAdmin->school_id)->first();
        $section = Section::where('class_id', $class->id)->first();
        $session = AcademicSession::where('school_id', $schoolAdmin->school_id)->first();
        $student = Student::where('school_id', $schoolAdmin->school_id)->first();

        $response = $this->actingAs($schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->post('/school/attendance/students', [
                'section_id' => $section->id,
                'academic_session_id' => $session->id,
                'date' => date('Y-m-d'),
                'attendance' => [
                    [
                        'student_id' => $student->id,
                        'status' => 'present',
                        'remark' => 'On time',
                    ]
                ]
            ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('student_attendances', [
            'student_id' => $student->id,
            'status' => 'present',
            'remark' => 'On time',
        ]);
    }

    /**
     * Test parent API actions.
     */
    public function test_parent_api_endpoints(): void
    {
        $parent = User::where('email', 'parent@yis.com')->first();

        // 1. API Login
        $response = $this->postJson('/api/v1/parent/login', [
            'school_code' => 'YIS2024',
            'email' => 'parent@yis.com',
            'password' => 'ParentSecurePass2026!',
            'device_name' => 'iPhone 15',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'token',
                    'user',
                    'school',
                    'children',
                ]
            ]);

        $token = $response->json('data.token');

        // 2. Fetch Children
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/parent/children');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data'); // 1 child seeded for parent
    }

    /**
     * Test staff self-attendance GPS punches.
     */
    public function test_staff_self_attendance_punch(): void
    {
        $teacher = User::where('email', 'teacher@yis.com')->first();

        // 1. Login
        $response = $this->postJson('/api/v1/login', [
            'school_code' => 'YIS2024',
            'email' => 'teacher@yis.com',
            'password' => 'SchoolTeacherSecurePass2026!',
            'device_name' => 'Android Phone',
        ]);

        $response->assertStatus(200);
        $token = $response->json('data.token');

        // Update school punch window to allow punch-in now
        $school = School::where('code', 'YIS2024')->first();
        $school->update([
            'staff_punch_in_start' => '00:00:00',
            'staff_punch_in_end' => '23:59:59',
        ]);

        // 2. Punch In (GPS coordinates)
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/v1/staff/self-attendance/punch', [
            'type' => 'in',
            'latitude' => 12.9716,
            'longitude' => 77.5946,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Punched in successfully.',
            ]);

        $this->assertDatabaseHas('staff_attendances', [
            'staff_id' => $teacher->staff->id,
            'latitude' => 12.97160000,
            'longitude' => 77.59460000,
        ]);
    }
}
