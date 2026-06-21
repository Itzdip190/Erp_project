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
use App\Models\StudentHouse;
use App\Models\StudentCategory;
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

        // Delete pre-seeded attendance for today to prevent unique constraint violation on weekdays
        \App\Models\StudentAttendance::where('student_id', $student->id)
            ->where('date', date('Y-m-d'))
            ->delete();

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

    /**
     * Test parent/student web portal pages.
     */
    public function test_parent_web_portal_pages(): void
    {
        $parent = User::where('email', 'parent@yis.com')->first();

        // 1. Dashboard loads
        $response = $this->actingAs($parent)->get('/parent/dashboard');
        $response->assertStatus(200);

        // 2. Notices page loads
        $response = $this->actingAs($parent)->get('/parent/notices');
        $response->assertStatus(200);

        // 3. Surveys page loads
        $response = $this->actingAs($parent)->get('/parent/surveys');
        $response->assertStatus(200);

        // 4. Fees page loads
        $response = $this->actingAs($parent)->get('/parent/fees');
        $response->assertStatus(200);

        // 5. Timetable page loads
        $response = $this->actingAs($parent)->get('/parent/timetable');
        $response->assertStatus(200);
    }

    /**
     * Test school dashboard details AJAX endpoints.
     */
    public function test_school_dashboard_details_ajax_endpoints(): void
    {
        $schoolAdmin = User::where('email', 'admin@yis.com')->first();

        $types = [
            'students',
            'staffs',
            'income',
            'expense',
            'today_collection',
            'student_attendance',
            'staff_attendance',
            'fee_pending',
            'admissions',
            'calendar_events'
        ];

        foreach ($types as $type) {
            $response = $this->actingAs($schoolAdmin)
                ->withHeaders(['X-School-Code' => 'YIS2024'])
                ->getJson("/school/dashboard/details?type={$type}");

            $response->assertStatus(200)
                ->assertJsonStructure([
                    'title',
                    'data',
                    'type'
                ]);
        }

        // Test sending reminder
        $response = $this->actingAs($schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->postJson("/school/dashboard/send-reminder");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Dues notification reminders have been sent successfully!'
            ]);
    }

    /**
     * Test school dashboard MIS report.
     */
    public function test_school_dashboard_mis_report(): void
    {
        $schoolAdmin = User::where('email', 'admin@yis.com')->first();

        $response = $this->actingAs($schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->get('/school/dashboard/mis-report?date=2026-06-21');

        $response->assertStatus(200)
            ->assertViewIs('school.dashboard.mis_report')
            ->assertViewHas([
                'dailyRevenue',
                'studentAttendancePct',
                'studentAttendanceRatio',
                'staffAttendancePct',
                'staffAttendanceRatio',
                'newAdmissionsCount',
                'newAdmissionsThisMonth',
                'attendanceNotMarkedTeachersCount',
                'feeDefaultersCriticalCount',
                'appNotDownloadedCount',
                'todayFeeCollection',
                'studentAppDownloadedCount',
                'studentAppDownloadedTotal',
                'staffAppDownloadedCount',
                'staffAppDownloadedTotal',
                'parentAppDownloadedCount',
                'parentAppDownloadedTotal',
                'pendingDownloadsCount',
                'teachersNoSharing7DaysCount',
                'classesMissingDiaryTodayCount',
                'studentPresentCount',
                'studentAbsentCount',
                'studentHalfDayCount',
                'studentLeaveCount',
                'studentNotMarkedCount',
                'staffPresentCount',
                'staffAbsentCount',
                'staffHalfDayCount',
                'staffLeaveCount',
                'staffNotMarkedCount',
                'criticalAttendanceIssues',
                'feeCashCollection',
                'feeChequeCollection',
                'feeOnlineCollection',
                'feeTotalCollection',
                'defaulters0_30Count',
                'defaulters31_60Count',
                'defaulters61_90Count',
                'defaulters90PlusCount',
                'overallMonthlyCollection',
                'pendingDiscountApprovalsCount',
                'feeDefaulters90PlusList',
                'feeDefaulters90PlusMoreCount',
                'classesAttendanceNotMarkedList',
                'classesAttendanceNotMarkedMoreCount',
                'teachersNotMarkedAttendance7DaysList',
                'teachersNotMarkedAttendance7DaysMoreCount',
                'teachersNoSharing7DaysList',
                'teachersNoSharing7DaysMoreCount',
                'classesMissingDiaryTodayList',
                'classesMissingDiaryTodayMoreCount'
            ]);
    }

    /**
     * Test school institute info setting flows.
     */
    public function test_school_institute_info_flow(): void
    {
        $schoolAdmin = User::where('email', 'admin@yis.com')->first();

        // 1. Check page loads
        $response = $this->actingAs($schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->get('/school/settings/institute-info');

        $response->assertStatus(200)
            ->assertViewIs('school.settings.institute_info')
            ->assertViewHasAll(['school', 'udise', 'houses', 'groups']);

        // 2. Update Details
        $response = $this->actingAs($schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->put('/school/settings/institute-info', [
                'name' => 'Yash International School Update',
                'code' => 'YIS2024',
                'affiliation_number' => 'AFF-889922',
                'udise_number' => '11223344556',
                'board_name' => 'CBSE'
            ]);
        $response->assertRedirect();
        $this->assertEquals('Yash International School Update', $schoolAdmin->school->refresh()->name);

        // 3. Update Timings
        $response = $this->actingAs($schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->put('/school/settings/institute-hours', [
                'hours' => [
                    'Monday' => ['start_time' => '07:30 AM', 'end_time' => '01:30 PM']
                ]
            ]);
        $response->assertRedirect();
        $udise = json_decode($schoolAdmin->school->refresh()->udise_data, true);
        $this->assertEquals('07:30 AM', $udise['days_and_time']['Monday']['start_time']);

        // 4. Add Student House
        $response = $this->actingAs($schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->post('/school/settings/houses', [
                'name' => 'Blue House',
                'color_code' => '#2563eb'
            ]);
        $response->assertRedirect();
        $house = StudentHouse::where('school_id', $schoolAdmin->school_id)->where('name', 'Blue House')->first();
        $this->assertNotNull($house);

        // 5. Add Student Category Group
        $response = $this->actingAs($schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->post('/school/settings/groups', [
                'name' => 'General Category',
                'description' => 'General students'
            ]);
        $response->assertRedirect();
        $group = StudentCategory::where('school_id', $schoolAdmin->school_id)->where('name', 'General Category')->first();
        $this->assertNotNull($group);

        // 6. Delete House
        $response = $this->actingAs($schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->delete("/school/settings/houses/{$house->id}");
        $response->assertRedirect();
        $this->assertNull(StudentHouse::find($house->id));

        // 7. Delete Group
        $response = $this->actingAs($schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->delete("/school/settings/groups/{$group->id}");
        $response->assertRedirect();
        $this->assertNull(StudentCategory::find($group->id));
    }

    /**
     * Test school UDISE data report settings page and updates.
     */
    public function test_school_udise_flow(): void
    {
        $schoolAdmin = User::where('email', 'admin@yis.com')->first();

        // 1. Get UDISE page
        $response = $this->actingAs($schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->get('/school/settings/udise');

        $response->assertStatus(200)
            ->assertViewIs('school.settings.udise')
            ->assertViewHasAll(['school', 'udise', 'grandTotalStudents', 'enrollmentData', 'teacherCounts']);

        // 2. Update UDISE page data
        $postData = [
            'academic_year' => '2025-2026',
            'udise_code' => '11223344556',
            'school_category' => 'higher_secondary',
            'management_type' => 'private',
            'affiliation_board' => 'CBSE',
            'affiliation_number' => '12345678',
            'classrooms_count' => 15,
            'good_classrooms_count' => 12,
            'boys_toilets' => 5,
            'girls_toilets' => 6,
            'library_available' => '1',
            'playground_available' => '1',
            'declared_by' => 'Dr. Jane Doe',
            'declared_designation' => 'Principal',
            'declared_confirm' => '1'
        ];

        $response = $this->actingAs($schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->put('/school/settings/udise', $postData);

        $response->assertRedirect();

        // 3. Verify DB update
        $school = $schoolAdmin->school->refresh();
        $udiseData = is_array($school->udise_data) ? $school->udise_data : json_decode($school->udise_data, true);

        $this->assertEquals('11223344556', $udiseData['udise_code']);
        $this->assertEquals('higher_secondary', $udiseData['school_category']);
        $this->assertEquals(15, $udiseData['classrooms_count']);
        $this->assertEquals('Dr. Jane Doe', $udiseData['declared_by']);
    }
}

