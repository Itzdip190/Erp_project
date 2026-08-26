<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\SectionSubjectStaff;
use App\Models\Staff;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudentSession;
use App\Models\User;
use App\Models\Scopes\SchoolScope;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StudentAttendanceSessionIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected School $school;
    protected User $admin;
    protected User $teacherUser;
    protected Staff $staff;
    protected AcademicSession $session2025;
    protected AcademicSession $session2026;
    protected SchoolClass $class4;
    protected Section $sectionA;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'superadmin']);
        Role::firstOrCreate(['name' => 'school_admin']);
        Role::firstOrCreate(['name' => 'student']);
        Role::firstOrCreate(['name' => 'parent']);
        Role::firstOrCreate(['name' => 'teacher']);
        Role::firstOrCreate(['name' => 'staff']);

        $this->school = School::create([
            'name'          => 'Test ERP School',
            'code'          => 'TES',
            'custom_domain' => 'tes.erp.test',
            'status'        => 'active',
        ]);

        $this->admin = User::factory()->create([
            'school_id' => $this->school->id,
            'role'      => 'school_admin',
            'email'     => 'admin@tes.erp.test',
        ]);
        $this->admin->assignRole('school_admin');

        $this->teacherUser = User::factory()->create([
            'school_id' => $this->school->id,
            'role'      => 'teacher',
            'email'     => 'teacher@tes.erp.test',
        ]);
        $dept = \App\Models\Department::create([
            'school_id' => $this->school->id,
            'name'      => 'Teaching',
        ]);

        $desig = \App\Models\Designation::create([
            'school_id' => $this->school->id,
            'name'      => 'Teacher',
        ]);

        $this->staff = Staff::create([
            'school_id'      => $this->school->id,
            'user_id'        => $this->teacherUser->id,
            'employee_id'    => 'EMP101',
            'first_name'     => 'Class',
            'last_name'      => 'Teacher',
            'email'          => 'teacher@tes.erp.test',
            'department_id'  => $dept->id,
            'designation_id' => $desig->id,
            'joining_date'   => '2025-01-01',
            'is_active'      => true,
        ]);

        $this->session2025 = AcademicSession::create([
            'school_id'  => $this->school->id,
            'name'       => '2025-2026',
            'start_date' => '2025-04-01',
            'end_date'   => '2026-03-31',
            'is_current' => false,
        ]);

        $this->session2026 = AcademicSession::create([
            'school_id'  => $this->school->id,
            'name'       => 'April-2026-March-2027',
            'start_date' => '2026-04-01',
            'end_date'   => '2027-03-31',
            'is_current' => true,
        ]);

        $this->class4 = SchoolClass::withoutGlobalScope(SchoolScope::class)->create([
            'school_id'    => $this->school->id,
            'name'         => '4',
            'numeric_name' => 4,
        ]);

        $this->sectionA = Section::withoutGlobalScope(SchoolScope::class)->create([
            'school_id'        => $this->school->id,
            'class_id'         => $this->class4->id,
            'name'             => 'A',
            'class_teacher_id' => $this->staff->id,
        ]);

        $subject = \App\Models\Subject::create([
            'school_id' => $this->school->id,
            'class_id'  => $this->class4->id,
            'name'      => 'Mathematics',
            'code'      => 'MATH4',
            'type'      => 'scholastic',
        ]);

        SectionSubjectStaff::create([
            'school_id'           => $this->school->id,
            'section_id'          => $this->sectionA->id,
            'staff_id'            => $this->staff->id,
            'subject_id'          => $subject->id,
            'academic_session_id' => $this->session2026->id,
        ]);
    }

    protected function createStudent(array $overrides = []): Student
    {
        static $seq = 100;
        $seq++;

        $defaults = [
            'school_id'             => $this->school->id,
            'admission_number'      => 'ADM' . $seq,
            'admission_sequence'    => $seq,
            'admission_year'        => 2026,
            'admission_date'        => '2026-04-01',
            'date_of_birth'         => '2018-05-15',
            'gender'                => 'male',
            'first_name'            => 'Student',
            'last_name'             => 'Test',
            'father_name'           => 'Father Name',
            'phone'                 => '9876543210',
            'guardian_name'         => 'Father Name',
            'guardian_phone'        => '9876543210',
            'guardian_relationship' => 'father',
            'address'               => '123 Test Street',
            'city'                  => 'Mumbai',
            'state'                 => 'Maharashtra',
            'pincode'               => '400001',
            'class_id'              => $this->class4->id,
            'section_id'            => $this->sectionA->id,
            'academic_session_id'   => $this->session2026->id,
            'roll_number'           => '01',
            'is_active'             => true,
        ];

        return Student::create(array_merge($defaults, $overrides));
    }

    protected function seedSampleStudents(): void
    {
        // 5 Active 2026-2027 students
        for ($i = 1; $i <= 5; $i++) {
            $st = $this->createStudent([
                'admission_number'    => 'STU202600' . $i,
                'first_name'          => 'ActiveStudent' . $i,
                'last_name'           => 'Singh',
                'academic_session_id' => $this->session2026->id,
                'roll_number'         => (string)$i,
                'is_active'           => true,
            ]);

            StudentSession::create([
                'school_id'           => $this->school->id,
                'student_id'          => $st->id,
                'academic_session_id' => $this->session2026->id,
                'class_id'            => $this->class4->id,
                'section_id'          => $this->sectionA->id,
                'roll_number'         => (string)$i,
                'is_promoted'         => false,
            ]);
        }

        // 10 Historical 2025-2026 students
        for ($i = 6; $i <= 15; $i++) {
            $hist = $this->createStudent([
                'admission_number'    => 'STU202500' . $i,
                'admission_year'      => 2025,
                'first_name'          => 'HistoricalStudent' . $i,
                'last_name'           => 'Kumar',
                'academic_session_id' => $this->session2025->id,
                'roll_number'         => (string)$i,
                'is_active'           => true,
            ]);

            StudentSession::create([
                'school_id'           => $this->school->id,
                'student_id'          => $hist->id,
                'academic_session_id' => $this->session2025->id,
                'class_id'            => $this->class4->id,
                'section_id'          => $this->sectionA->id,
                'roll_number'         => (string)$i,
                'is_promoted'         => true,
            ]);
        }
    }

    public function test_attendance_load_returns_only_active_students_mapped_to_current_session_section(): void
    {
        $this->seedSampleStudents();

        $response = $this->actingAs($this->admin)->postJson(route('school.attendance.students.load'), [
            'section_id'          => $this->sectionA->id,
            'academic_session_id' => $this->session2026->id,
            'date'                => '2026-08-24',
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $html = $response->json('html');

        // Must see active 2026-2027 students
        for ($i = 1; $i <= 5; $i++) {
            $this->assertStringContainsString('ActiveStudent' . $i, $html);
            $this->assertStringContainsString('STU202600' . $i, $html);
        }

        // Must NOT see historical 2025-2026 students
        for ($i = 6; $i <= 15; $i++) {
            $this->assertStringNotContainsString('HistoricalStudent' . $i, $html);
            $this->assertStringNotContainsString('STU202500' . $i, $html);
        }
    }

    public function test_attendance_preview_returns_exact_mapped_students_count(): void
    {
        $this->seedSampleStudents();

        $response = $this->actingAs($this->admin)->getJson(route('school.attendance.students.preview', [
            'class_id'            => $this->class4->id,
            'section_id'          => $this->sectionA->id,
            'academic_session_id' => $this->session2026->id,
            'date'                => '2026-08-24',
            'type'                => 'daily',
        ]));

        $response->assertOk();
        $response->assertJson(['success' => true]);
        $rows = $response->json('rows');

        // Only 5 active rows for 2026-2027
        $this->assertCount(5, $rows);
    }

    public function test_api_staff_attendance_returns_exact_mapped_students_count(): void
    {
        $this->seedSampleStudents();

        $response = $this->actingAs($this->teacherUser)->getJson("/api/v1/staff/attendance?section_id={$this->sectionA->id}&date=2026-08-24&academic_session_id={$this->session2026->id}");

        $response->assertOk();
        $response->assertJson(['success' => true]);
        $data = $response->json('data');

        $this->assertCount(5, $data);
    }
}
