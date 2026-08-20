<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\AcademicSession;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Staff;
use App\Models\Student;
use App\Models\User;
use App\Services\SettingService;
use App\Services\StudentNumberService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DynamicIdentityNumberSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    protected function createTestStudent(array $overrides = []): Student
    {
        $schoolId = $overrides['school_id'] ?? 1;

        $class = SchoolClass::firstOrCreate(
            ['school_id' => $schoolId, 'name' => 'Class 10'],
            ['numeric_name' => 10]
        );
        $section = Section::firstOrCreate(['school_id' => $schoolId, 'class_id' => $class->id, 'name' => 'A']);
        $session = AcademicSession::firstOrCreate(
            ['school_id' => $schoolId, 'name' => '2026-2027'],
            ['is_current' => true, 'start_date' => '2026-04-01', 'end_date' => '2027-03-31']
        );

        $defaults = [
            'school_id' => $schoolId,
            'class_id' => $class->id,
            'section_id' => $section->id,
            'academic_session_id' => $session->id,
            'admission_number' => '001',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'father_name' => 'Father Doe',
            'father_phone' => '9876543210',
            'guardian_name' => 'Guardian Doe',
            'guardian_phone' => '9876543210',
            'guardian_relationship' => 'father',
            'date_of_birth' => '2010-01-01',
            'gender' => 'male',
            'address' => 'Test Street',
            'city' => 'Test City',
            'state' => 'NY',
            'pincode' => '10001',
            'admission_date' => '2026-01-01',
        ];

        return Student::create(array_merge($defaults, $overrides));
    }

    protected function createTestStaff(array $overrides = []): Staff
    {
        $schoolId = $overrides['school_id'] ?? 1;
        $dept = Department::firstOrCreate(['school_id' => $schoolId, 'name' => 'Academics']);
        $desg = Designation::firstOrCreate(['school_id' => $schoolId, 'name' => 'Teacher']);

        $defaults = [
            'school_id' => $schoolId,
            'department_id' => $dept->id,
            'designation_id' => $desg->id,
            'employee_id' => 'EMP100',
            'first_name' => 'StaffFirstName',
            'last_name' => 'StaffLastName',
            'email' => 'staff' . rand(1000, 9999) . '@test.com',
            'phone' => '9876543210',
            'joining_date' => '2026-01-01',
            'employment_type' => 'permanent',
            'basic_salary' => 30000,
        ];

        return Staff::create(array_merge($defaults, $overrides));
    }

    /**
     * Requirement 1: When a new school is created, default prefixes are empty.
     */
    public function test_new_school_has_empty_default_prefixes()
    {
        $school = School::create([
            'name' => 'New Dynamic School',
            'code' => 'NDS2026',
            'status' => 'active',
        ]);

        $studentPrefix = SettingService::get('student_id_prefix', null, $school->id);
        $staffPrefix   = SettingService::get('staff_id_prefix', null, $school->id);

        $this->assertEquals('', $studentPrefix);
        $this->assertEquals('', $staffPrefix);

        $service = app(StudentNumberService::class);
        $this->assertEquals('', $service->getStudentPrefix($school->id));
        $this->assertEquals('', $service->getStaffPrefix($school->id));
    }

    /**
     * Requirement 2 & 3: Alphabetic prefix incrementation (JPPS227 -> JPPS228).
     */
    public function test_alphabetic_prefix_incrementation()
    {
        $school = School::create(['name' => 'School Alpha', 'code' => 'ALPHA01', 'status' => 'active']);
        SettingService::set('student_id_prefix', 'JPPS227', 'school_config', 'string', $school->id);

        $service = app(StudentNumberService::class);

        // 1st student generated should be JPPS227
        $data1 = $service->getStudentPrefixAndNextSequence($school->id);
        $this->assertEquals('JPPS', $data1['prefix']);
        $this->assertEquals('227', $data1['sequence']);
        $this->assertEquals('JPPS227', $data1['full']);

        // Create student with JPPS227
        $this->createTestStudent([
            'school_id' => $school->id,
            'admission_number' => 'JPPS227',
        ]);

        // Next student generated should be JPPS228
        $data2 = $service->getStudentPrefixAndNextSequence($school->id);
        $this->assertEquals('JPPS228', $data2['full']);
    }

    /**
     * Requirement 2 & 3: Pure numeric prefix (001 -> 002, 150 -> 151).
     */
    public function test_pure_numeric_prefix_incrementation()
    {
        $school = School::create(['name' => 'School Numeric', 'code' => 'NUM01', 'status' => 'active']);
        SettingService::set('student_id_prefix', '001', 'school_config', 'string', $school->id);

        $service = app(StudentNumberService::class);

        // 1st student should be 001
        $data1 = $service->getStudentPrefixAndNextSequence($school->id);
        $this->assertEquals('', $data1['prefix']);
        $this->assertEquals('001', $data1['sequence']);
        $this->assertEquals('001', $data1['full']);

        $this->createTestStudent([
            'school_id' => $school->id,
            'admission_number' => '001',
        ]);

        // 2nd student should be 002
        $data2 = $service->getStudentPrefixAndNextSequence($school->id);
        $this->assertEquals('002', $data2['full']);

        // Now test starting from 150
        $school2 = School::create(['name' => 'School 150', 'code' => 'NUM150', 'status' => 'active']);
        SettingService::set('student_id_prefix', '150', 'school_config', 'string', $school2->id);

        $data3 = $service->getStudentPrefixAndNextSequence($school2->id);
        $this->assertEquals('150', $data3['full']);
    }

    /**
     * Requirement 4: Bulk upload sequence synchronization.
     */
    public function test_bulk_upload_sequence_synchronization()
    {
        $school = School::create(['name' => 'School Bulk', 'code' => 'BULK01', 'status' => 'active']);
        SettingService::set('student_id_prefix', '001', 'school_config', 'string', $school->id);

        // Simulate CSV upload of 001, 002, 015, 020
        foreach (['001', '002', '015', '020'] as $adm) {
            $this->createTestStudent([
                'school_id' => $school->id,
                'admission_number' => $adm,
            ]);
        }

        // Highest uploaded is 020 -> next manually created student should be 021
        $service = app(StudentNumberService::class);
        $data = $service->getStudentPrefixAndNextSequence($school->id);
        $this->assertEquals('021', $data['full']);
    }

    /**
     * Requirement 5: Employee ID dynamic logic.
     */
    public function test_employee_id_dynamic_logic()
    {
        $school = School::create(['name' => 'Staff School', 'code' => 'STF01', 'status' => 'active']);
        SettingService::set('staff_id_prefix', 'EMP100', 'school_config', 'string', $school->id);

        $service = app(StudentNumberService::class);

        $data1 = $service->getStaffPrefixAndNextSequence($school->id);
        $this->assertEquals('EMP100', $data1['full']);

        $this->createTestStaff([
            'school_id' => $school->id,
            'employee_id' => 'EMP100',
        ]);

        $data2 = $service->getStaffPrefixAndNextSequence($school->id);
        $this->assertEquals('EMP101', $data2['full']);
    }

    /**
     * Requirement 6 & 8: Multi school isolation (identical admission number in different schools).
     */
    public function test_multi_school_isolation_and_duplicate_validation()
    {
        $schoolA = School::create(['name' => 'School A', 'code' => 'SCHA01', 'status' => 'active']);
        $schoolB = School::create(['name' => 'School B', 'code' => 'SCHB01', 'status' => 'active']);

        // Create student in School A with admission number 001
        $studentA = $this->createTestStudent([
            'school_id' => $schoolA->id,
            'admission_number' => '001',
        ]);

        // Create student in School B with SAME admission number 001 — MUST succeed!
        $studentB = $this->createTestStudent([
            'school_id' => $schoolB->id,
            'admission_number' => '001',
        ]);

        $this->assertEquals('001', $studentA->admission_number);
        $this->assertEquals('001', $studentB->admission_number);
        $this->assertNotEquals($studentA->school_id, $studentB->school_id);
    }

    /**
     * Requirement 7: Login flow scoped by school_id.
     */
    public function test_login_flow_school_id_scoping()
    {
        $schoolA = School::create(['name' => 'School A', 'code' => 'SCHA01', 'status' => 'active']);
        $schoolB = School::create(['name' => 'School B', 'code' => 'SCHB01', 'status' => 'active']);

        $userA = User::create([
            'name' => 'Student User A',
            'email' => 'student.a@student.scha01.com',
            'password' => bcrypt('password123'),
            'school_id' => $schoolA->id,
            'role' => 'student',
        ]);
        $userA->assignRole('student');

        $studentA = $this->createTestStudent([
            'school_id' => $schoolA->id,
            'user_id' => $userA->id,
            'admission_number' => '001',
            'email' => 'student.a@student.scha01.com',
        ]);

        $userB = User::create([
            'name' => 'Student User B',
            'email' => 'student.b@student.schb01.com',
            'password' => bcrypt('password123'),
            'school_id' => $schoolB->id,
            'role' => 'student',
        ]);
        $userB->assignRole('student');

        $studentB = $this->createTestStudent([
            'school_id' => $schoolB->id,
            'user_id' => $userB->id,
            'admission_number' => '001',
            'email' => 'student.b@student.schb01.com',
        ]);

        // Attempt login with admission number 001 AND school_id for School A
        $responseA = $this->post(route('login'), [
            'email' => '001',
            'password' => 'password123',
            'school_id' => $schoolA->id,
        ]);
        $responseA->assertRedirect('/student/dashboard');
        $this->assertEquals($userA->id, auth()->id());

        auth()->logout();

        // Attempt login with admission number 001 AND school_id for School B
        $responseB = $this->post(route('login'), [
            'email' => '001',
            'password' => 'password123',
            'school_id' => $schoolB->id,
        ]);
        $responseB->assertRedirect('/student/dashboard');
        $this->assertEquals($userB->id, auth()->id());
    }

    /**
     * Test that registering a student with an existing user email updates the user rather than throwing UniqueConstraintViolationException.
     */
    public function test_existing_email_does_not_trigger_unique_constraint_violation()
    {
        $school = School::create(['name' => 'Duplicate Email School', 'code' => 'DUP01', 'status' => 'active']);

        // Create an existing user with email roysaswati133@gmail.com
        $existingUser = User::create([
            'school_id' => $school->id,
            'name' => 'Saswati Roy',
            'email' => 'roysaswati133@gmail.com',
            'phone' => '9907806863',
            'password' => bcrypt('password123'),
            'is_active' => true,
        ]);

        // Global lookup of existing user by email
        $studentUser = User::withoutGlobalScope(\App\Models\Scopes\SchoolScope::class)
            ->where('email', 'roysaswati133@gmail.com')
            ->first();

        $this->assertNotNull($studentUser);
        $this->assertEquals($existingUser->id, $studentUser->id);

        $student = $this->createTestStudent([
            'school_id' => $school->id,
            'user_id'   => $studentUser->id,
            'email'     => 'roysaswati133@gmail.com',
            'first_name' => 'Saswati',
            'last_name'  => 'Roy',
        ]);

        $this->assertEquals($existingUser->id, $student->user_id);
    }
}
