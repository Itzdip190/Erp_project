<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Staff;
use App\Models\StaffAttendance;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardSessionAttendanceTest extends TestCase
{
    use RefreshDatabase;

    protected School $school;
    protected User $schoolAdmin;
    protected AcademicSession $session2025;
    protected AcademicSession $session2026;
    protected SchoolClass $schoolClass;
    protected Section $section;
    protected Student $student2026;
    protected Staff $staff;

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
            'name'          => 'Test Jyoti Balika Inter College',
            'code'          => 'UPEC005',
            'custom_domain' => 'jyoti.erp.test',
            'status'        => 'active',
        ]);

        $this->schoolAdmin = User::factory()->create([
            'school_id' => $this->school->id,
            'role'      => 'school_admin',
            'email'     => 'admin@jyoti.erp.test',
        ]);
        $this->schoolAdmin->assignRole('school_admin');

        $this->session2025 = AcademicSession::create([
            'school_id'  => $this->school->id,
            'name'       => 'April-2025-March-2026',
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

        $this->schoolClass = SchoolClass::create([
            'school_id' => $this->school->id,
            'name'      => 'Class 10',
        ]);

        $this->section = Section::create([
            'school_id' => $this->school->id,
            'class_id'  => $this->schoolClass->id,
            'name'      => 'A',
        ]);

        $this->student2026 = Student::create([
            'school_id'           => $this->school->id,
            'first_name'          => 'Ansh',
            'last_name'           => 'Yadav',
            'admission_number'    => 'ADM-2026-001',
            'roll_number'         => '101',
            'class_id'            => $this->schoolClass->id,
            'section_id'          => $this->section->id,
            'academic_session_id' => $this->session2026->id,
            'is_active'           => true,
        ]);

        $this->staff = Staff::create([
            'school_id'   => $this->school->id,
            'first_name'  => 'Sunita',
            'last_name'   => 'Sharma',
            'employee_id' => 'STF-001',
            'gender'      => 'female',
            'is_active'   => true,
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_attendance_shown_when_current_session_matches_today_date(): void
    {
        // Mock current date to September 11, 2026 (inside session 2026-2027)
        Carbon::setTestNow('2026-09-11 10:00:00');

        // Mark student attendance for today
        StudentAttendance::create([
            'school_id'           => $this->school->id,
            'student_id'          => $this->student2026->id,
            'class_id'            => $this->schoolClass->id,
            'section_id'          => $this->section->id,
            'academic_session_id' => $this->session2026->id,
            'date'                => '2026-09-11',
            'status'              => 'present',
            'marked_by'           => $this->schoolAdmin->id,
            'attendance_type'     => 'manual',
        ]);

        // Mark staff attendance for today
        StaffAttendance::create([
            'school_id'   => $this->school->id,
            'staff_id'    => $this->staff->id,
            'date'        => '2026-09-11',
            'status'      => 'present',
            'marked_by'   => $this->schoolAdmin->id,
        ]);

        $response = $this->actingAs($this->schoolAdmin)->get(route('school.dashboard'));
        $response->assertStatus(200);
        $response->assertViewHas('studentPresentToday', 1);
        $response->assertViewHas('staffPresentToday', 1);

        // Check AJAX refresh box
        $ajaxResponse = $this->actingAs($this->schoolAdmin)
            ->getJson(route('school.dashboard.refresh-box', ['box' => 'attendance']));
        $ajaxResponse->assertStatus(200);
        $ajaxResponse->assertJson([
            'success' => true,
            'data' => [
                'studentPresentToday' => 1,
                'staffPresentToday'   => 1,
            ],
        ]);
    }

    public function test_attendance_is_zero_when_switching_to_previous_academic_year(): void
    {
        // Mock current date to September 11, 2026 (outside session 2025-2026)
        Carbon::setTestNow('2026-09-11 10:00:00');

        // Mark attendance on 2026-09-11 (belongs to session 2026-2027)
        StudentAttendance::create([
            'school_id'           => $this->school->id,
            'student_id'          => $this->student2026->id,
            'class_id'            => $this->schoolClass->id,
            'section_id'          => $this->section->id,
            'academic_session_id' => $this->session2026->id,
            'date'                => '2026-09-11',
            'status'              => 'present',
            'marked_by'           => $this->schoolAdmin->id,
            'attendance_type'     => 'manual',
        ]);

        StaffAttendance::create([
            'school_id'   => $this->school->id,
            'staff_id'    => $this->staff->id,
            'date'        => '2026-09-11',
            'status'      => 'present',
            'marked_by'   => $this->schoolAdmin->id,
        ]);

        // Switch to session 2025-2026
        $this->session2026->update(['is_current' => false]);
        $this->session2025->update(['is_current' => true]);

        $response = $this->actingAs($this->schoolAdmin)->get(route('school.dashboard'));
        $response->assertStatus(200);
        // In previous session 2025-2026, today's attendance must NOT bleed in
        $response->assertViewHas('studentPresentToday', 0);
        $response->assertViewHas('studentAttendancePct', 0);
        $response->assertViewHas('staffPresentToday', 0);
        $response->assertViewHas('staffAttendancePct', 0);

        // Check AJAX refresh box
        $ajaxResponse = $this->actingAs($this->schoolAdmin)
            ->getJson(route('school.dashboard.refresh-box', ['box' => 'attendance']));
        $ajaxResponse->assertStatus(200);
        $ajaxResponse->assertJson([
            'success' => true,
            'data' => [
                'studentPresentToday'  => 0,
                'studentAttendancePct' => 0,
                'staffPresentToday'    => 0,
                'staffAttendancePct'   => 0,
            ],
        ]);

        // Check snapshot endpoint
        $snapshotResponse = $this->actingAs($this->schoolAdmin)
            ->getJson(route('school.dashboard.snapshot'));
        $snapshotResponse->assertStatus(200);
        $snapshotResponse->assertJson([
            'students_present' => 0,
            'staff_present'    => 0,
        ]);
    }
}
