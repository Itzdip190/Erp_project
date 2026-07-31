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
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IdentityNumberSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_identity_number_settings_can_be_updated_in_all_settings()
    {
        $schoolAdmin = User::where('email', 'admin@yis.com')->first();
        $this->assertNotNull($schoolAdmin);

        $response = $this->actingAs($schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->post(route('school.settings.all.update'), [
                'student_id_prefix' => 'DAV',
                'staff_id_prefix'   => 'STAFF',
            ]);

        $response->assertRedirect();

        $this->assertEquals('DAV', SettingService::get('student_id_prefix', 'YAS', $schoolAdmin->school_id));
        $this->assertEquals('STAFF', SettingService::get('staff_id_prefix', 'EMP', $schoolAdmin->school_id));
    }

    public function test_student_admission_number_uses_configured_prefix_and_allows_editing_sequence()
    {
        $schoolAdmin = User::where('email', 'admin@yis.com')->first();
        $schoolId = $schoolAdmin->school_id;

        SettingService::set('student_id_prefix', 'KVS', 'school_config', 'string', $schoolId);

        $class = SchoolClass::where('school_id', $schoolId)->first() ?? SchoolClass::create(['school_id' => $schoolId, 'name' => 'Class 10']);
        $section = Section::where('school_id', $schoolId)->first() ?? Section::create(['school_id' => $schoolId, 'class_id' => $class->id, 'name' => 'A']);
        $session = AcademicSession::where('school_id', $schoolId)->first() ?? AcademicSession::create(['school_id' => $schoolId, 'name' => '2026-2027', 'is_current' => true]);

        $response = $this->actingAs($schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->post(route('school.students.store'), [
                'first_name' => 'KvsStudentUnique',
                'last_name' => 'Sharma',
                'father_name' => 'Suresh Sharma',
                'father_phone' => '9876543210',
                'guardian_name' => 'Suresh Sharma',
                'guardian_phone' => '9876543210',
                'guardian_relationship' => 'father',
                'date_of_birth' => '2010-05-15',
                'gender' => 'male',
                'address' => '123 Main St',
                'city' => 'Metropolis',
                'state' => 'NY',
                'pincode' => '10001',
                'admission_date' => '2026-06-01',
                'class_id' => $class->id,
                'section_id' => $section->id,
                'academic_session_id' => $session->id,
                'admission_number_prefix' => 'KVS/2026/',
                'admission_number_seq' => '00050',
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $student = Student::where('first_name', 'KvsStudentUnique')->first();
        $this->assertNotNull($student);
        $this->assertEquals('KVS/2026/00050', $student->admission_number);
    }

    public function test_staff_employee_id_uses_configured_prefix_and_allows_editing_sequence()
    {
        $schoolAdmin = User::where('email', 'admin@yis.com')->first();
        $schoolId = $schoolAdmin->school_id;

        SettingService::set('staff_id_prefix', 'TCH', 'school_config', 'string', $schoolId);

        $dept = Department::where('school_id', $schoolId)->first() ?? Department::create(['school_id' => $schoolId, 'name' => 'Academic']);
        $desg = Designation::where('school_id', $schoolId)->first() ?? Designation::create(['school_id' => $schoolId, 'name' => 'Senior Teacher']);

        $response = $this->actingAs($schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->post(route('school.staff.store'), [
                'employee_id_prefix' => 'TCH/2026/',
                'employee_id_seq'    => '00012',
                'first_name'         => 'AnitaStaffUnique',
                'last_name'          => 'Verma',
                'email'              => 'anita.unique.staff@test.com',
                'phone'              => '9876543210',
                'department_id'      => $dept->id,
                'designation_id'     => $desg->id,
                'joining_date'       => '2026-01-01',
                'employment_type'    => 'permanent',
                'basic_salary'       => 50000,
                'is_active'          => 1,
            ]);

        $response->assertRedirect();

        $staff = Staff::where('first_name', 'AnitaStaffUnique')->first();
        $this->assertNotNull($staff);
        $this->assertEquals('TCH/2026/00012', $staff->employee_id);
    }
}
