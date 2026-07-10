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
        $this->assertEquals(0, $student->refresh()->is_active);
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
            ->whereDate('date', date('Y-m-d'))
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
     * Test school dashboard changing academic session and topbar search.
     */
    public function test_school_dashboard_change_session_and_topbar_search(): void
    {
        $schoolAdmin = User::where('email', 'admin@yis.com')->first();
        $schoolId = $schoolAdmin->school_id;

        // Ensure we have at least two academic sessions for testing
        $sessions = \App\Models\AcademicSession::where('school_id', $schoolId)->get();
        if ($sessions->count() < 2) {
            \App\Models\AcademicSession::create([
                'school_id' => $schoolId,
                'name' => 'Test Session 2',
                'start_date' => now()->startOfYear(),
                'end_date' => now()->endOfYear(),
                'is_current' => false
            ]);
            $sessions = \App\Models\AcademicSession::where('school_id', $schoolId)->get();
        }

        $currentSession = $sessions->where('is_current', true)->first() ?? $sessions->first();
        $otherSession = $sessions->where('id', '!=', $currentSession->id)->first();

        // 1. Change session
        $response = $this->actingAs($schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->postJson("/school/dashboard/change-session", [
                'academic_session_id' => $otherSession->id
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Academic session changed successfully!'
            ]);

        // Assert session updated in DB
        $this->assertTrue(
            \App\Models\AcademicSession::find($otherSession->id)->is_current
        );
        $this->assertFalse(
            \App\Models\AcademicSession::find($currentSession->id)->is_current
        );

        // Reset the current session to what it was
        \App\Models\AcademicSession::where('school_id', $schoolId)->update(['is_current' => false]);
        $currentSession->is_current = true;
        $currentSession->save();

        // 2. Topbar search
        // First query without parameter
        $response = $this->actingAs($schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->getJson("/school/topbar-search");

        $response->assertStatus(200)
            ->assertJson([
                'students' => [],
                'staff' => []
            ]);

        // Query with a name/string
        $student = \App\Models\Student::where('school_id', $schoolId)->first();
        if ($student) {
            $query = substr($student->first_name, 0, 3);
            $response = $this->actingAs($schoolAdmin)
                ->withHeaders(['X-School-Code' => 'YIS2024'])
                ->getJson("/school/topbar-search?query=" . urlencode($query));

            $response->assertStatus(200)
                ->assertJsonStructure([
                    'students',
                    'staff'
                ]);
        }
    }

    /**
     * Test school dashboard income & expense chart filter endpoint.
     */
    public function test_school_dashboard_income_expense_chart_endpoint(): void
    {
        $schoolAdmin = User::where('email', 'admin@yis.com')->first();

        // 1. Test default/This Year filter
        $response = $this->actingAs($schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->getJson("/school/dashboard/chart/income-expense?filter=This+Year");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'labels',
                'incomeData',
                'expenseData',
                'totalIncome',
                'totalExpense'
            ]);

        // 2. Test This Month filter
        $response = $this->actingAs($schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->getJson("/school/dashboard/chart/income-expense?filter=This+Month");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'labels',
                'incomeData',
                'expenseData',
                'totalIncome',
                'totalExpense'
            ]);

        // 3. Test Last 6 Months filter
        $response = $this->actingAs($schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->getJson("/school/dashboard/chart/income-expense?filter=Last+6+Months");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'labels',
                'incomeData',
                'expenseData',
                'totalIncome',
                'totalExpense'
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
        $udiseRaw = $schoolAdmin->school->refresh()->udise_data;
        $udise = is_array($udiseRaw) ? $udiseRaw : json_decode($udiseRaw ?? '[]', true);
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

    /**
     * Test bulk staff import with custom headers and additional metadata fields.
     */
    public function test_staff_bulk_import_and_metadata(): void
    {
        $schoolAdmin = User::where('email', 'admin@yis.com')->first();

        // 1. Download template check
        $response = $this->actingAs($schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->get('/school/staff/import-template');
        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->streamedContent(), 'Employee ID'));
        $this->assertTrue(str_contains($response->streamedContent(), 'Phone number'));

        // 2. Perform Import
        $csvContent = "Employee ID * (required),First Name * (required),Last Name,Email * (required),Phone number * (required),Pan Number,Adhar Number\n" .
                      "EMPTEST999,Jane,Smith,jane.smith@yis.com,9876543210,ABCDE1234F,123456789012";

        $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('staff_import.csv', $csvContent);

        $response = $this->actingAs($schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->post('/school/staff/import', [
                'csv_file' => $file
            ]);

        $response->assertRedirect();

        // 3. Verify Database
        $this->assertDatabaseHas('users', [
            'email' => 'jane.smith@yis.com'
        ]);

        $staff = \App\Models\Staff::where('employee_id', 'EMPTEST999')->first();
        $this->assertNotNull($staff);
        $this->assertEquals('Jane', $staff->first_name);
        $this->assertEquals('Smith', $staff->last_name);
        $this->assertEquals('123456789012', $staff->additional_fields['aadhar_number']);
        $this->assertEquals('ABCDE1234F', $staff->pan_number);
    }

    public function test_staff_bulk_delete_and_reimport(): void
    {
        $schoolAdmin = User::where('email', 'admin@yis.com')->first();

        // 1. Perform Import first to create staff + user
        $csvContent = "Employee ID * (required),First Name * (required),Last Name,Email * (required),Phone number * (required),Pan Number,Adhar Number\n" .
                      "EMPTEST888,Bob,Builder,bob.builder.staff@yis.com,9876543215,ABCDE1234F,123456789012";

        $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('staff_import.csv', $csvContent);

        $response = $this->actingAs($schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->post('/school/staff/import', [
                'csv_file' => $file
            ]);

        $response->assertRedirect();

        // Verify Staff & User exist
        $staff = \App\Models\Staff::where('employee_id', 'EMPTEST888')->first();
        $this->assertNotNull($staff);
        $user = $staff->user;
        $this->assertNotNull($user);

        // Delete the staff profile (simulates destroy method)
        $staff->delete(); // Soft delete
        $user->delete();  // Hard delete user

        // Verify they are deleted/soft-deleted
        $this->assertNull(\App\Models\Staff::where('employee_id', 'EMPTEST888')->first());
        $this->assertNotNull(\App\Models\Staff::withTrashed()->where('employee_id', 'EMPTEST888')->first());
        $this->assertNull(User::where('email', 'bob.builder.staff@yis.com')->first());

        // 2. Re-import the exact same data to verify it restores and updates
        $fileReimport = \Illuminate\Http\UploadedFile::fake()->createWithContent('staff_import.csv', $csvContent);

        $response = $this->actingAs($schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->post('/school/staff/import', [
                'csv_file' => $fileReimport
            ]);

        $response->assertRedirect();

        // Verify Staff is restored and user is created/re-linked
        $restoredStaff = \App\Models\Staff::where('employee_id', 'EMPTEST888')->first();
        $this->assertNotNull($restoredStaff);
        $this->assertNull($restoredStaff->deleted_at);

        $recreatedUser = $restoredStaff->user;
        $this->assertNotNull($recreatedUser);
        $this->assertEquals('bob.builder.staff@yis.com', $recreatedUser->email);
    }

    public function test_staff_bulk_attendance(): void
    {
        $schoolAdmin = User::where('email', 'admin@yis.com')->first();
        $staff = \App\Models\Staff::where('school_id', $schoolAdmin->school_id)->first();

        // 1. Load Bulk Attendance Page
        $response = $this->actingAs($schoolAdmin)
            ->withSession(['school_id' => $schoolAdmin->school_id])
            ->get('/school/staff/bulk-attendance?from_date=2026-06-21&to_date=2026-06-21&staff_type=Teaching');

        $response->assertStatus(200);

        // 2. Post Bulk Attendance
        $response = $this->actingAs($schoolAdmin)
            ->withSession(['school_id' => $schoolAdmin->school_id])
            ->post('/school/staff/bulk-attendance', [
                'attendance' => [
                    $staff->id => [
                        '2026-06-21' => [
                            'status' => 'Present',
                            'clock_in_at' => '09:00 AM',
                            'clock_out_at' => '05:00 PM',
                        ]
                    ]
                ]
            ]);

        $response->assertRedirect();
        
        // 3. Verify Database
        $this->assertDatabaseHas('staff_attendances', [
            'staff_id' => $staff->id,
            'date' => '2026-06-21 00:00:00',
            'status' => 'present',
            'clock_in_at' => '09:00:00',
            'clock_out_at' => '17:00:00',
        ]);
    }

    public function test_student_attendance_marking_report(): void
    {
        $schoolAdmin = User::where('email', 'admin@yis.com')->first();
        $class = SchoolClass::where('school_id', $schoolAdmin->school_id)->first();
        $section = Section::where('class_id', $class->id)->first();
        $session = AcademicSession::where('school_id', $schoolAdmin->school_id)->first();
        $student = Student::where('school_id', $schoolAdmin->school_id)->first();

        // 1. Mark attendance for today
        // Delete pre-existing attendance for today to avoid constraints
        \App\Models\StudentAttendance::where('student_id', $student->id)
            ->whereDate('date', date('Y-m-d'))
            ->delete();

        \App\Models\StudentAttendance::create([
            'school_id' => $schoolAdmin->school_id,
            'student_id' => $student->id,
            'date' => date('Y-m-d'),
            'section_id' => $section->id,
            'class_id' => $class->id,
            'academic_session_id' => $session->id,
            'status' => 'present',
            'attendance_type' => 'manual',
            'marked_by' => $schoolAdmin->id,
        ]);

        // 2. Request the report page
        $response = $this->actingAs($schoolAdmin)
            ->withSession(['school_id' => $schoolAdmin->school_id])
            ->get('/school/attendance/students/marking-report?from_date=' . date('Y-m-d') . '&to_date=' . date('Y-m-d'));

        $response->assertStatus(200);
        $response->assertViewHas('reportData');
        $response->assertViewHas('dates');

        $reportData = $response->viewData('reportData');
        $this->assertNotEmpty($reportData);

        // Find the section's row in reportData
        $sectionRow = null;
        foreach ($reportData as $row) {
            if ($row['section']->id === $section->id) {
                $sectionRow = $row;
                break;
            }
        }

        $this->assertNotNull($sectionRow);
        // It should have 1 working day, 1 marked day, and 100% overall percentage
        $this->assertEquals(1, $sectionRow['total_working_days']);
        $this->assertEquals(1, $sectionRow['marked_days']);
        $this->assertEquals(100, $sectionRow['overall_percentage']);
    }

    public function test_class_overview_report(): void
    {
        $schoolAdmin = User::where('email', 'admin@yis.com')->first();
        $class = SchoolClass::where('school_id', $schoolAdmin->school_id)->first();
        $section = Section::where('class_id', $class->id)->first();
        $session = AcademicSession::where('school_id', $schoolAdmin->school_id)->first();
        $teacher = \App\Models\Staff::where('school_id', $schoolAdmin->school_id)->first();

        // 1. Check Section View loads
        $response = $this->actingAs($schoolAdmin)
            ->withSession(['school_id' => $schoolAdmin->school_id])
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->get('/school/assignments/class-overview?view_mode=section&academic_session_id=' . $session->id);

        $response->assertStatus(200);
        $response->assertViewHas('reportData');
        $response->assertViewHas('totals');
        
        // 2. Check Class View loads
        $response = $this->actingAs($schoolAdmin)
            ->withSession(['school_id' => $schoolAdmin->school_id])
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->get('/school/assignments/class-overview?view_mode=class&academic_session_id=' . $session->id);

        $response->assertStatus(200);

        // 3. Check Toggle deactivation loads
        $response = $this->actingAs($schoolAdmin)
            ->withSession(['school_id' => $schoolAdmin->school_id])
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->get('/school/assignments/class-overview?view_mode=section&include_deactivated=true&academic_session_id=' . $session->id);

        $response->assertStatus(200);

        // 4. Update Class Teacher via AJAX
        $response = $this->actingAs($schoolAdmin)
            ->withSession(['school_id' => $schoolAdmin->school_id])
            ->withHeaders([
                'X-School-Code' => 'YIS2024',
                'X-Requested-With' => 'XMLHttpRequest'
            ])
            ->post("/school/assignments/sections/{$section->id}/class-teacher", [
                'class_teacher_id' => $teacher ? $teacher->id : null,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Class teacher updated successfully.'
            ]);

        $this->assertEquals($teacher ? $teacher->id : null, $section->refresh()->class_teacher_id);
    }

    public function test_class_management_flows(): void
    {
        $schoolAdmin = User::where('email', 'admin@yis.com')->first();

        // 1. Get classes form page
        $response = $this->actingAs($schoolAdmin)
            ->withSession(['school_id' => $schoolAdmin->school_id])
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->get('/school/assignments/classes');

        $response->assertStatus(200);
        $response->assertViewHas('classes');
        $response->assertViewHas('totalClasses');
        $response->assertViewHas('totalSections');

        // 2. Create Class with sections
        $response = $this->actingAs($schoolAdmin)
            ->withSession(['school_id' => $schoolAdmin->school_id])
            ->withHeaders([
                'X-School-Code' => 'YIS2024',
                'X-Requested-With' => 'XMLHttpRequest'
            ])
            ->post('/school/assignments/classes', [
                'name' => 'Grade 10 Test',
                'local_name' => '10th Local',
                'class_code' => 'G10T',
                'sections' => [
                    ['name' => 'Alpha', 'local_name' => 'A-Local'],
                    ['name' => 'Beta', 'local_name' => 'B-Local']
                ]
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $class = SchoolClass::where('name', 'Grade 10 Test')->first();
        $this->assertNotNull($class);
        $this->assertEquals('10th Local', $class->local_name);
        $this->assertEquals('G10T', $class->class_code);
        $this->assertCount(2, $class->sections);

        $sections = $class->sections;
        $this->assertEquals('Alpha', $sections[0]->name);
        $this->assertEquals('A-Local', $sections[0]->local_name);
        $this->assertEquals('Beta', $sections[1]->name);
        $this->assertEquals('B-Local', $sections[1]->local_name);

        // 3. Update Class details and sections
        // We will modify Alpha, remove Beta, and add Gamma
        $response = $this->actingAs($schoolAdmin)
            ->withSession(['school_id' => $schoolAdmin->school_id])
            ->withHeaders([
                'X-School-Code' => 'YIS2024',
                'X-Requested-With' => 'XMLHttpRequest'
            ])
            ->put("/school/assignments/classes/{$class->id}", [
                'name' => 'Grade 10 Updated',
                'local_name' => '10th Local Edit',
                'class_code' => 'G10U',
                'sections' => [
                    ['id' => $sections[0]->id, 'name' => 'Alpha Edit', 'local_name' => 'A-Local-Edit'],
                    ['name' => 'Gamma', 'local_name' => 'G-Local']
                ]
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $class->refresh();
        $this->assertEquals('Grade 10 Updated', $class->name);
        $this->assertEquals('10th Local Edit', $class->local_name);
        $this->assertEquals('G10U', $class->class_code);
        
        $updatedSections = $class->sections()->get();
        $this->assertCount(2, $updatedSections);
        $this->assertEquals('Alpha Edit', $updatedSections[0]->name);
        $this->assertEquals('Gamma', $updatedSections[1]->name);

        // 4. Reorder Classes
        $classes = SchoolClass::where('school_id', $schoolAdmin->school_id)->get();
        $orderedIds = $classes->pluck('id')->reverse()->toArray(); // reverse current sequence

        $response = $this->actingAs($schoolAdmin)
            ->withSession(['school_id' => $schoolAdmin->school_id])
            ->withHeaders([
                'X-School-Code' => 'YIS2024',
                'X-Requested-With' => 'XMLHttpRequest'
            ])
            ->post('/school/assignments/classes/reorder', [
                'ordered_ids' => $orderedIds
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $firstClass = SchoolClass::find($orderedIds[0]);
        $this->assertEquals(0, $firstClass->sort_order);

        // 5. Get Class Logs
        $response = $this->actingAs($schoolAdmin)
            ->withSession(['school_id' => $schoolAdmin->school_id])
            ->withHeaders([
                'X-School-Code' => 'YIS2024',
                'X-Requested-With' => 'XMLHttpRequest'
            ])
            ->get('/school/assignments/classes/logs');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'logs' => [
                    '*' => [
                        'id', 'row_reference', 'field_changed', 'old_value', 'new_value', 'changed_by', 'changed_at'
                    ]
                ]
            ]);
    }

    public function test_subject_management_flows(): void
    {
        $schoolAdmin = User::where('email', 'admin@yis.com')->first();
        $class = SchoolClass::where('school_id', $schoolAdmin->school_id)->first();

        // 1. Get subjects form
        $response = $this->actingAs($schoolAdmin)
            ->withSession(['school_id' => $schoolAdmin->school_id])
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->get('/school/assignments/subjects');
        $response->assertStatus(200);

        // 2. Create subject
        $response = $this->actingAs($schoolAdmin)
            ->withSession(['school_id' => $schoolAdmin->school_id])
            ->withHeaders([
                'X-School-Code' => 'YIS2024',
                'X-Requested-With' => 'XMLHttpRequest'
            ])
            ->post('/school/assignments/subjects', [
                'name' => 'Advanced Physics',
                'code' => 'PHYS-401',
                'local_name' => 'Physics Local',
                'description' => 'Advanced mechanics study',
                'is_mandatory' => 1,
                'type' => 'Scholastic',
                'class_ids' => [$class->id]
            ]);
        $response->assertStatus(200)->assertJson(['success' => true]);

        $subject = \App\Models\Subject::where('name', 'Advanced Physics')->first();
        $this->assertNotNull($subject);
        $this->assertEquals('PHYS-401', $subject->code);
        $this->assertEquals('Physics Local', $subject->local_name);
        $this->assertEquals(true, $subject->is_mandatory);

        // 3. Update subject
        $response = $this->actingAs($schoolAdmin)
            ->withSession(['school_id' => $schoolAdmin->school_id])
            ->withHeaders([
                'X-School-Code' => 'YIS2024',
                'X-Requested-With' => 'XMLHttpRequest'
            ])
            ->put("/school/assignments/subjects/{$subject->id}", [
                'name' => 'Advanced Physics Updated',
                'code' => 'PHYS-402',
                'local_name' => 'Physics Local Edit',
                'description' => 'Updated desc',
                'is_mandatory' => 0,
                'type' => 'Non Scholastic'
            ]);
        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertEquals('Advanced Physics Updated', $subject->refresh()->name);
        $this->assertEquals('Non Scholastic', $subject->type);
        $this->assertEquals(false, $subject->is_mandatory);

        // 4. Reorder subjects
        $response = $this->actingAs($schoolAdmin)
            ->withSession(['school_id' => $schoolAdmin->school_id])
            ->withHeaders([
                'X-School-Code' => 'YIS2024',
                'X-Requested-With' => 'XMLHttpRequest'
            ])
            ->post('/school/assignments/subjects/reorder', [
                'ordered_ids' => [$subject->id]
            ]);
        $response->assertStatus(200)->assertJson(['success' => true]);

        // 5. Get Subject Logs
        $response = $this->actingAs($schoolAdmin)
            ->withSession(['school_id' => $schoolAdmin->school_id])
            ->withHeaders([
                'X-School-Code' => 'YIS2024',
                'X-Requested-With' => 'XMLHttpRequest'
            ])
            ->get('/school/assignments/subjects/logs');
        $response->assertStatus(200)->assertJsonStructure(['success', 'logs']);

        // 6. Delete Subject
        $response = $this->actingAs($schoolAdmin)
            ->withSession(['school_id' => $schoolAdmin->school_id])
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->delete("/school/assignments/subjects/{$subject->id}");
        $response->assertRedirect();
        $this->assertNull(\App\Models\Subject::find($subject->id));
    }

    public function test_teacher_assignments_grid_flows(): void
    {
        $schoolAdmin = User::where('email', 'admin@yis.com')->first();
        $class = SchoolClass::where('school_id', $schoolAdmin->school_id)->first();
        $section = Section::where('class_id', $class->id)->first();
        $session = AcademicSession::where('school_id', $schoolAdmin->school_id)->first();
        $teacher = \App\Models\Staff::where('school_id', $schoolAdmin->school_id)->where('is_active', true)->first();
        $subject = \App\Models\Subject::where('school_id', $schoolAdmin->school_id)->where('class_id', $class->id)->first() ?? \App\Models\Subject::create([
            'school_id' => $schoolAdmin->school_id,
            'class_id' => $class->id,
            'name' => 'Temp Subject',
            'code' => 'TMP',
            'type' => 'Scholastic'
        ]);

        // 1. Get teachers page
        $response = $this->actingAs($schoolAdmin)
            ->withSession(['school_id' => $schoolAdmin->school_id])
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->get('/school/assignments/teachers');
        $response->assertStatus(200);

        // 2. Load teacher grid via AJAX
        $response = $this->actingAs($schoolAdmin)
            ->withSession(['school_id' => $schoolAdmin->school_id])
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->get("/school/assignments/teachers/load-grid?academic_session_id={$session->id}&class_id={$class->id}&section_id={$section->id}");
        $response->assertStatus(200)->assertJsonStructure(['success', 'grid', 'class_teacher_id']);

        // 3. Save teacher grid (assign primary teacher + substitute)
        $response = $this->actingAs($schoolAdmin)
            ->withSession(['school_id' => $schoolAdmin->school_id])
            ->withHeaders([
                'X-School-Code' => 'YIS2024',
                'X-Requested-With' => 'XMLHttpRequest'
            ])
            ->post('/school/assignments/teachers/save-grid', [
                'academic_session_id' => $session->id,
                'class_id' => $class->id,
                'section_id' => $section->id,
                'class_teacher_id' => $teacher->id,
                'assistant_class_teacher_id' => $teacher->id,
                'assignments' => [
                    [
                        'subject_id' => $subject->id,
                        'staff_id' => $teacher->id,
                        'substitute_staff_id' => $teacher->id
                    ]
                ]
            ]);
        $response->assertStatus(200)->assertJson(['success' => true]);

        $section->refresh();
        $this->assertEquals($teacher->id, $section->class_teacher_id);
        $this->assertEquals($teacher->id, $section->assistant_class_teacher_id);

        $assignment = \App\Models\SectionSubjectStaff::where('subject_id', $subject->id)->where('section_id', $section->id)->first();
        $this->assertNotNull($assignment);
        $this->assertEquals($teacher->id, $assignment->staff_id);
        $this->assertEquals($teacher->id, $assignment->substitute_staff_id);

        // 4. Get teacher assignment logs
        $response = $this->actingAs($schoolAdmin)
            ->withSession(['school_id' => $schoolAdmin->school_id])
            ->withHeaders([
                'X-School-Code' => 'YIS2024',
                'X-Requested-With' => 'XMLHttpRequest'
            ])
            ->get('/school/assignments/teachers/logs');
        $response->assertStatus(200)->assertJsonStructure(['success', 'logs']);

        // 5. Export template CSV
        $response = $this->actingAs($schoolAdmin)
            ->withSession(['school_id' => $schoolAdmin->school_id])
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->get("/school/assignments/teachers/export-template?section_ids={$section->id}");
        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->streamedContent(), 'Class Name'));
        
        // 6. Import mapping via CSV
        $csvContent = "Class Name,Section Name,Subject Name,Subject Code,Primary Teacher Employee ID,Substitute Teacher Employee ID\n" .
                      "{$class->name},{$section->name},{$subject->name},{$subject->code},{$teacher->employee_id},{$teacher->employee_id}";

        $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('teacher_mappings.csv', $csvContent);

        $response = $this->actingAs($schoolAdmin)
            ->withSession(['school_id' => $schoolAdmin->school_id])
            ->withHeaders([
                'X-School-Code' => 'YIS2024',
                'X-Requested-With' => 'XMLHttpRequest'
            ])
            ->post('/school/assignments/teachers/import', [
                'csv_file' => $file
            ]);
        $response->assertStatus(200)->assertJson(['success' => true]);
    }

    public function test_designated_substitute_visible_in_substitution_portal(): void
    {
        $schoolAdmin = User::where('email', 'admin@yis.com')->first();
        $class = SchoolClass::where('school_id', $schoolAdmin->school_id)->first();
        $section = Section::where('class_id', $class->id)->first();
        $session = AcademicSession::where('school_id', $schoolAdmin->school_id)->first();
        
        $primaryTeacher = \App\Models\Staff::where('school_id', $schoolAdmin->school_id)->where('is_active', true)->first();
        $substituteTeacher = \App\Models\Staff::where('school_id', $schoolAdmin->school_id)->where('is_active', true)->where('id', '!=', $primaryTeacher->id)->first()
            ?? \App\Models\Staff::create([
                'school_id' => $schoolAdmin->school_id,
                'first_name' => 'Substitute',
                'last_name' => 'Teacher',
                'employee_id' => 'SUB001',
                'is_active' => true,
                'department_id' => $primaryTeacher->department_id,
                'designation_id' => $primaryTeacher->designation_id,
                'email' => 'substitute_teacher@yis.com',
                'joining_date' => now()->toDateString(),
            ]);

        $subject = \App\Models\Subject::where('school_id', $schoolAdmin->school_id)->where('class_id', $class->id)->first() ?? \App\Models\Subject::create([
            'school_id' => $schoolAdmin->school_id,
            'class_id' => $class->id,
            'name' => 'Temp Subject',
            'code' => 'TMP',
            'type' => 'Scholastic'
        ]);

        // 1. Assign the designated substitute teacher in SectionSubjectStaff (Module 7)
        \App\Models\SectionSubjectStaff::create([
            'school_id' => $schoolAdmin->school_id,
            'section_id' => $section->id,
            'subject_id' => $subject->id,
            'staff_id' => $primaryTeacher->id,
            'academic_session_id' => $session->id,
            'substitute_staff_id' => $substituteTeacher->id
        ]);

        // 2. Create a ClassTimetableCell entry for the primary teacher
        $date = '2026-06-22'; // A Monday
        $dayOfWeek = 'Monday';

        $group = \App\Models\TimetableGroup::create([
            'school_id' => $schoolAdmin->school_id,
            'group_name' => 'Test Group',
            'start_date' => '2026-04-01',
            'end_date' => '2027-03-31',
            'academic_year' => $session->name,
            'class_start_time' => '09:00:00',
            'number_of_periods' => 1,
            'applicable_days' => json_encode(['Monday']),
            'is_active' => true,
        ]);

        $period = \App\Models\TimetableGroupPeriod::create([
            'school_id' => $schoolAdmin->school_id,
            'timetable_group_id' => $group->id,
            'period_name' => 'Period 1',
            'duration_minutes' => 45,
            'start_time' => '09:00:00',
            'end_time' => '09:45:00',
        ]);

        $cell = \App\Models\ClassTimetableCell::create([
            'school_id' => $schoolAdmin->school_id,
            'timetable_group_id' => $group->id,
            'class_id' => $class->id,
            'section_id' => $section->id,
            'timetable_group_period_id' => $period->id,
            'day_of_week' => $dayOfWeek,
            'subject_id' => $subject->id,
            'teacher_id' => $primaryTeacher->id,
            'mode' => 'online',
        ]);

        // 3. Load the teacher substitution page (Module 8) for the primary teacher (absent) on that date
        $response = $this->actingAs($schoolAdmin)
            ->withSession(['school_id' => $schoolAdmin->school_id])
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->get("/school/timetable/substitution?date={$date}&absent_teacher_id={$primaryTeacher->id}");

        $response->assertStatus(200);
        $response->assertViewHas('designatedSubstitutes');
        
        $viewDesignated = $response->viewData('designatedSubstitutes');
        $this->assertArrayHasKey($cell->id, $viewDesignated);
        $this->assertEquals($substituteTeacher->id, $viewDesignated[$cell->id]->id);
    }

    public function test_student_bulk_import(): void
    {
        $schoolAdmin = User::where('email', 'admin@yis.com')->first();
        $class = SchoolClass::where('school_id', $schoolAdmin->school_id)->first();
        $section = Section::where('class_id', $class->id)->first();
        $session = AcademicSession::where('school_id', $schoolAdmin->school_id)->first();

        // 1. Download template check
        $response = $this->actingAs($schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->get('/school/students/import-template');
        $response->assertStatus(200);

        // 2. Perform Import
        $csvContent = "first_name,last_name,gender,date_of_birth,guardian_name,guardian_phone,guardian_relationship,address,city,state,pincode,class_id,section_id,academic_session_id,admission_date\n" .
                      "Alice,Smith,female,2015-08-20,Richard Smith,9876543220,father,456 Main St,Metropolis,NY,10001,{$class->id},{$section->id},{$session->id},2026-06-01";

        $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('students_import.csv', $csvContent);

        $response = $this->actingAs($schoolAdmin)
            ->withHeaders([
                'X-School-Code' => 'YIS2024',
                'X-Requested-With' => 'XMLHttpRequest'
            ])
            ->post('/school/students/import', [
                'file' => $file
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // 3. Verify Database
        $student = Student::where('first_name', 'Alice')->first();
        $this->assertNotNull($student);
        $this->assertEquals('Smith', $student->last_name);
        $this->assertEquals('female', $student->gender);
        $this->assertEquals($class->id, $student->class_id);
    }

    public function test_student_bulk_delete_and_reimport(): void
    {
        $schoolAdmin = User::where('email', 'admin@yis.com')->first();
        $superAdmin = User::where('email', 'superadmin@schoolcloud.com')->first();
        $class = SchoolClass::where('school_id', $schoolAdmin->school_id)->first();
        $section = Section::where('class_id', $class->id)->first();
        $session = AcademicSession::where('school_id', $schoolAdmin->school_id)->first();

        // 1. Perform Import first to create student + student user + parent user
        $csvContent = "first_name,last_name,gender,date_of_birth,guardian_name,guardian_phone,guardian_relationship,address,city,state,pincode,class_id,section_id,academic_session_id,admission_date,email\n" .
                      "Bob,Builder,male,2015-08-20,Richard Builder,9876543225,father,456 Main St,Metropolis,NY,10001,{$class->id},{$section->id},{$session->id},2026-06-01,bob.builder@student.yis.com";

        $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('students_import.csv', $csvContent);

        $response = $this->actingAs($schoolAdmin)
            ->withHeaders([
                'X-School-Code' => 'YIS2024',
                'X-Requested-With' => 'XMLHttpRequest'
            ])
            ->post('/school/students/import', [
                'file' => $file
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verify Student & User exist
        $student = Student::where('first_name', 'Bob')->first();
        $this->assertNotNull($student);
        $this->assertNotNull($student->user_id);
        $studentUserId = $student->user_id;

        $studentUser = User::find($studentUserId);
        $this->assertNotNull($studentUser);
        $this->assertEquals('bob.builder@student.yis.com', $studentUser->email);

        // Deactivate student (so they become inactive for deletion)
        $student->update(['is_active' => false]);

        // 2. Perform Permanent Bulk Delete via SuperAdmin
        $response = $this->actingAs($superAdmin)
            ->post("/superadmin/schools/{$schoolAdmin->school_id}/inactive-students/delete", [
                'student_ids' => [$student->id]
            ]);
        $response->assertRedirect();

        // Verify Student & Student User are deleted
        $this->assertNull(Student::find($student->id));
        $this->assertNull(User::find($studentUserId));

        // 3. Re-import the exact same data to verify it works without unique constraint error
        $fileReimport = \Illuminate\Http\UploadedFile::fake()->createWithContent('students_import.csv', $csvContent);

        $response = $this->actingAs($schoolAdmin)
            ->withHeaders([
                'X-School-Code' => 'YIS2024',
                'X-Requested-With' => 'XMLHttpRequest'
            ])
            ->post('/school/students/import', [
                'file' => $fileReimport
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verify Student & Student User were created successfully again
        $reimportedStudent = Student::where('first_name', 'Bob')->first();
        $this->assertNotNull($reimportedStudent);
        $this->assertNotNull($reimportedStudent->user_id);
        $reimportedStudentUser = User::find($reimportedStudent->user_id);
        $this->assertNotNull($reimportedStudentUser);
        $this->assertEquals('bob.builder@student.yis.com', $reimportedStudentUser->email);
    }

    public function test_student_bulk_photo_upload(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $schoolAdmin = User::where('email', 'admin@yis.com')->first();
        $student = Student::where('school_id', $schoolAdmin->school_id)->first();
        $this->assertNotNull($student);

        // Convert slashes to underscores for filename matching
        $filename = str_replace('/', '_', $student->admission_number) . '.jpg';

        $file = \Illuminate\Http\UploadedFile::fake()->create($filename, 100, 'image/jpeg');

        $response = $this->actingAs($schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->post('/school/student-mgmt/bulk-photo', [
                'photos' => [$file]
            ]);

        $response->assertRedirect();
        
        $student->refresh();
        $this->assertNotNull($student->photo);
        $this->assertStringContainsString('students/photos', $student->photo);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($student->photo);
    }

    public function test_student_bulk_attendance(): void
    {
        $schoolAdmin = User::where('email', 'admin@yis.com')->first();
        $student = Student::where('school_id', $schoolAdmin->school_id)->first();
        $session = AcademicSession::where('school_id', $schoolAdmin->school_id)->where('is_current', true)->first()
            ?? AcademicSession::where('school_id', $schoolAdmin->school_id)->first();

        // 1. Load Bulk Attendance Page
        $response = $this->actingAs($schoolAdmin)
            ->withSession(['school_id' => $schoolAdmin->school_id])
            ->get("/school/student-mgmt/bulk-attendance?academic_session_id={$session->id}&class_id={$student->class_id}&section_id={$student->section_id}&from_date=2026-06-21&to_date=2026-06-21");

        $response->assertStatus(200);

        // 2. Post Bulk Attendance
        $response = $this->actingAs($schoolAdmin)
            ->withSession(['school_id' => $schoolAdmin->school_id])
            ->post('/school/student-mgmt/bulk-attendance', [
                'academic_session_id' => $session->id,
                'class_id' => $student->class_id,
                'section_id' => $student->section_id,
                'attendance' => [
                    $student->id => [
                        '2026-06-21' => [
                            'status' => 'present'
                        ]
                    ]
                ]
            ]);

        $response->assertRedirect();
        
        // 3. Verify Database
        $this->assertDatabaseHas('student_attendances', [
            'student_id' => $student->id,
            'date' => '2026-06-21 00:00:00',
            'status' => 'present',
            'class_id' => $student->class_id,
            'section_id' => $student->section_id,
            'academic_session_id' => $session->id,
        ]);
    }

    public function test_superadmin_create_school_assigns_role(): void
    {
        $superAdmin = User::where('email', 'superadmin@schoolcloud.com')->first();
        
        $response = $this->actingAs($superAdmin)
            ->post('/superadmin/schools', [
                'name' => 'Test New School',
                'code' => 'TNS2026',
                'phone' => '1234567890',
                'address' => '123 Test St',
                'custom_domain' => 'new-test-school.example.com',
                'status' => 'active',
                'admin_name' => 'New School Admin',
                'email' => 'admin@newtestschool.com',
            ]);

        $response->assertRedirect('/superadmin/schools');

        // Verify school was created
        $school = School::where('code', 'TNS2026')->first();
        $this->assertNotNull($school);

        // Verify admin user was created with Spatie role and database role column
        $adminUser = User::where('email', 'admin@newtestschool.com')->first();
        $this->assertNotNull($adminUser);
        $this->assertEquals($school->id, $adminUser->school_id);
        $this->assertEquals('school_admin', $adminUser->role);
        $this->assertTrue($adminUser->hasRole('school_admin'));

        // Log out superadmin
        $this->post('/logout');

        // Verify we can login as the newly created school admin and get redirected correctly
        $response = $this->post('/login', [
            'email' => 'admin@newtestschool.com',
            'password' => 'test@123',
        ]);

        $response->assertRedirect('/school/dashboard');
    }

    public function test_superadmin_can_view_schools_list(): void
    {
        $superAdmin = User::where('email', 'superadmin@schoolcloud.com')->first();
        
        $response = $this->actingAs($superAdmin)
            ->get('/superadmin/schools');

        $response->assertStatus(200);
        $response->assertSee('All Registered Schools');
        $response->assertSee('Yash International School');
        $response->assertSee('YIS2024');
    }

    public function test_superadmin_can_impersonate_school_admin(): void
    {
        $superAdmin = User::where('email', 'superadmin@schoolcloud.com')->first();
        $school = School::where('code', 'YIS2024')->first();
        $schoolAdmin = User::where('email', 'admin@yis.com')->first();

        // 1. Post to impersonate route
        $response = $this->actingAs($superAdmin)
            ->post("/superadmin/schools/{$school->id}/impersonate");

        // 2. Assert redirect to school dashboard
        $response->assertRedirect('/school/dashboard');

        // 3. Assert current authenticated user is the school admin
        $this->assertEquals($schoolAdmin->id, \Illuminate\Support\Facades\Auth::id());
        $this->assertEquals('YIS2024', session('school_code'));
    }

    public function test_superadmin_can_update_school_and_reset_password(): void
    {
        $superAdmin = User::where('email', 'superadmin@schoolcloud.com')->first();
        $school = School::where('code', 'YIS2024')->first();
        $schoolAdmin = User::where('email', 'admin@yis.com')->first();
        $plan = \App\Models\Plan::where('name', 'Basic')->first();

        // 1. Visit edit page
        $response = $this->actingAs($superAdmin)
            ->get("/superadmin/schools/{$school->id}/edit");
        $response->assertStatus(200);

        // 2. Submit update form with new name, plan, and admin password reset
        $response = $this->actingAs($superAdmin)
            ->put("/superadmin/schools/{$school->id}", [
                'name' => 'Yash International School Edited',
                'code' => 'YIS2024',
                'phone' => '0987654321',
                'address' => 'New Address',
                'custom_domain' => 'new.yis.com',
                'status' => 'active',
                'plan_id' => $plan->id,
                'admin_name' => 'YIS School Admin Edited',
                'admin_email' => 'admin@yis.com',
                'admin_password' => 'NewSecurePassword2026!',
                'admin_password_confirmation' => 'NewSecurePassword2026!',
            ]);

        $response->assertRedirect('/superadmin/schools');

        // 3. Verify changes in DB
        $school->refresh();
        $this->assertEquals('Yash International School Edited', $school->name);

        $schoolAdmin->refresh();
        $this->assertEquals('YIS School Admin Edited', $schoolAdmin->name);

        // 4. Logout superadmin and login as school admin with new password
        $this->post('/logout');

        $response = $this->post('/login', [
            'email' => 'admin@yis.com',
            'password' => 'NewSecurePassword2026!',
        ]);
        $response->assertRedirect('/school/dashboard');
    }

    public function test_superadmin_can_toggle_school_status(): void
    {
        $superAdmin = User::where('email', 'superadmin@schoolcloud.com')->first();
        $school = School::where('code', 'YIS2024')->first();

        $this->assertEquals('active', $school->status);

        // 1. Post to toggle-status route
        $response = $this->actingAs($superAdmin)
            ->post("/superadmin/schools/{$school->id}/toggle-status");

        $response->assertRedirect();
        
        $school->refresh();
        $this->assertEquals('suspended', $school->status);

        // 2. Toggle again
        $response = $this->actingAs($superAdmin)
            ->post("/superadmin/schools/{$school->id}/toggle-status");
        
        $school->refresh();
        $this->assertEquals('active', $school->status);
    }

    public function test_superadmin_can_delete_school(): void
    {
        $superAdmin = User::where('email', 'superadmin@schoolcloud.com')->first();
        
        // Create a temporary school and admin to delete
        $school = School::create([
            'name' => 'Delete Me School',
            'code' => 'DEL2026',
            'status' => 'active',
        ]);
        $admin = User::create([
            'name' => 'Delete Admin',
            'email' => 'delete_admin@school.com',
            'password' => bcrypt('password'),
            'school_id' => $school->id,
        ]);
        $admin->assignRole('school_admin');

        // 1. Post to destroy route
        $response = $this->actingAs($superAdmin)
            ->delete("/superadmin/schools/{$school->id}");

        $response->assertRedirect('/superadmin/schools');

        // 2. Assert records deleted
        $this->assertDatabaseMissing('schools', ['id' => $school->id]);
        $this->assertDatabaseMissing('users', ['id' => $admin->id]);
    }

    public function test_superadmin_can_manage_subscription_plans(): void
    {
        $superAdmin = User::where('email', 'superadmin@schoolcloud.com')->first();

        // 1. View plans list page
        $response = $this->actingAs($superAdmin)->get('/superadmin/plans');
        $response->assertStatus(200);

        // 2. Create a new plan
        $response = $this->actingAs($superAdmin)->post('/superadmin/plans', [
            'name' => 'Ultimate Package',
            'price' => 9999.00,
            'duration_days' => 180,
            'features' => ['AI Chatbot', 'Priority SMS', 'Unlimited Storage']
        ]);
        $response->assertRedirect('/superadmin/plans');
        
        $plan = Plan::where('name', 'Ultimate Package')->first();
        $this->assertNotNull($plan);
        $this->assertEquals(9999.00, $plan->price);
        $this->assertEquals(180, $plan->duration_days);

        // 3. Update the plan
        $response = $this->actingAs($superAdmin)->put("/superadmin/plans/{$plan->id}", [
            'name' => 'Ultimate Package V2',
            'price' => 12999.00,
            'duration_days' => 365,
            'features' => ['AI Chatbot', 'Priority SMS', 'Uncapped Disk']
        ]);
        $response->assertRedirect('/superadmin/plans');

        $plan->refresh();
        $this->assertEquals('Ultimate Package V2', $plan->name);
        $this->assertEquals(12999.00, $plan->price);

        // 4. Delete the plan
        $response = $this->actingAs($superAdmin)->delete("/superadmin/plans/{$plan->id}");
        $response->assertRedirect('/superadmin/plans');
        $this->assertDatabaseMissing('plans', ['id' => $plan->id]);
    }

    public function test_superadmin_can_extend_and_change_subscriptions(): void
    {
        $superAdmin = User::where('email', 'superadmin@schoolcloud.com')->first();
        $school = School::where('code', 'YIS2024')->first();
        $plan = Plan::where('name', 'Basic')->first();

        // 1. View subscriptions page
        $response = $this->actingAs($superAdmin)->get('/superadmin/subscriptions');
        $response->assertStatus(200);

        // 2. Change plan for a school
        $response = $this->actingAs($superAdmin)->post('/superadmin/subscriptions/change-plan', [
            'school_id' => $school->id,
            'plan_id' => $plan->id,
        ]);
        $response->assertRedirect('/superadmin/subscriptions');

        $sub = Subscription::where('school_id', $school->id)->latest()->first();
        $this->assertNotNull($sub);
        $this->assertEquals($plan->id, $sub->plan_id);
        $this->assertEquals('active', $sub->status);

        // 3. Extend subscription duration by 45 days
        $expiryBefore = \Carbon\Carbon::parse($sub->subscription_ends_at);
        $response = $this->actingAs($superAdmin)->post('/superadmin/subscriptions/extend', [
            'school_id' => $school->id,
            'days' => 45,
        ]);
        $response->assertRedirect('/superadmin/subscriptions');

        $sub->refresh();
        $expiryAfter = \Carbon\Carbon::parse($sub->subscription_ends_at);
        $this->assertEquals(45, $expiryBefore->diffInDays($expiryAfter));

        // 4. Suspend subscription
        $response = $this->actingAs($superAdmin)->post('/superadmin/subscriptions/cancel', [
            'school_id' => $school->id,
        ]);
        $response->assertRedirect('/superadmin/subscriptions');

        $sub->refresh();
        $this->assertEquals('suspended', $sub->status);
    }

    public function test_superadmin_can_filter_orders_and_approve_manually(): void
    {
        $superAdmin = User::where('email', 'superadmin@schoolcloud.com')->first();
        $school = School::where('code', 'YIS2024')->first();
        $plan = Plan::where('name', 'Basic')->first();

        // Create a pending bank transfer order
        $order = \App\Models\SubscriptionOrder::create([
            'school_id' => $school->id,
            'plan_id' => $plan->id,
            'amount' => 1500.00,
            'gateway' => 'bank_transfer',
            'status' => 'pending',
        ]);

        // 1. View orders log page
        $response = $this->actingAs($superAdmin)->get('/superadmin/orders');
        $response->assertStatus(200);

        // 2. Filter orders
        $response = $this->actingAs($superAdmin)->get('/superadmin/orders?gateway=bank_transfer&status=pending');
        $response->assertStatus(200);

        // 3. Approve the order (mark completed)
        $response = $this->actingAs($superAdmin)->put("/superadmin/orders/{$order->id}/status", [
            'status' => 'completed',
        ]);
        $response->assertRedirect('/superadmin/orders');

        $order->refresh();
        $this->assertEquals('completed', $order->status);

        // Verify active subscription is initialized for the school
        $sub = Subscription::where('school_id', $school->id)->latest()->first();
        $this->assertNotNull($sub);
        $this->assertEquals('active', $sub->status);
        $this->assertEquals($plan->id, $sub->plan_id);
    }

    public function test_superadmin_can_configure_payment_gateways(): void
    {
        $superAdmin = User::where('email', 'superadmin@schoolcloud.com')->first();

        // 1. View gateways config page
        $response = $this->actingAs($superAdmin)->get('/superadmin/gateways');
        $response->assertStatus(200);

        // 2. Save gateway settings
        $response = $this->actingAs($superAdmin)->post('/superadmin/gateways', [
            'stripe' => [
                'enabled' => '1',
                'mode' => 'sandbox',
                'publishable_key' => 'pk_test_stripe_publishable_123',
                'secret_key' => 'sk_test_stripe_secret_123',
            ],
            'razorpay' => [
                'enabled' => '0',
                'mode' => 'sandbox',
                'key_id' => '',
                'key_secret' => '',
            ],
            'bank_transfer' => [
                'enabled' => '1',
                'account_name' => 'Corporate SBI ERP Account',
                'account_number' => '112233445566',
                'bank_name' => 'SBI Bank',
                'ifsc_code' => 'SBIN0009999',
                'instructions' => 'Include school code in remarks.',
            ],
        ]);
        $response->assertRedirect('/superadmin/gateways');

        // Verify configuration was stored in local json file
        $this->assertTrue(\Illuminate\Support\Facades\Storage::disk('local')->exists('payment_gateways.json'));
        $content = json_decode(\Illuminate\Support\Facades\Storage::disk('local')->get('payment_gateways.json'), true);
        
        $this->assertTrue($content['stripe']['enabled']);
        $this->assertEquals('pk_test_stripe_publishable_123', $content['stripe']['publishable_key']);
        $this->assertFalse($content['razorpay']['enabled']);
        $this->assertEquals('Corporate SBI ERP Account', $content['bank_transfer']['account_name']);
    }

    public function test_superadmin_can_manage_sms_gateways(): void
    {
        $superAdmin = User::where('email', 'superadmin@schoolcloud.com')->first();

        $response = $this->actingAs($superAdmin)->get('/superadmin/sms-gateways');
        $response->assertStatus(200);

        $response = $this->actingAs($superAdmin)->post('/superadmin/sms-gateways', [
            'twilio' => [
                'enabled' => '1',
                'account_sid' => 'AC_test_sid_123',
                'auth_token' => 'auth_token_secret_123',
                'sender_number' => '+15555555555',
            ],
            'msg91' => [
                'enabled' => '0',
                'auth_key' => '',
                'sender_id' => '',
                'route' => '4',
            ],
            'fast2sms' => [
                'enabled' => '0',
                'authorization_key' => '',
                'sender_id' => '',
            ],
        ]);
        $response->assertRedirect('/superadmin/sms-gateways');
        $this->assertTrue(\Illuminate\Support\Facades\Storage::disk('local')->exists('sms_gateways.json'));
    }

    public function test_superadmin_can_manage_notification_types(): void
    {
        $superAdmin = User::where('email', 'superadmin@schoolcloud.com')->first();

        $response = $this->actingAs($superAdmin)->get('/superadmin/notification-types');
        $response->assertStatus(200);

        $response = $this->actingAs($superAdmin)->post('/superadmin/notification-types', [
            'attendance' => [
                'title' => 'Custom Absent Alert',
                'subject' => 'Absent: {student_name}',
                'body' => 'Child {student_name} is absent today {date}',
                'channels' => ['email'],
            ],
            'fee_reminder' => [
                'title' => 'Custom Fee Alert',
                'subject' => 'Fees: {due_amount}',
                'body' => 'Balance: {due_amount}',
                'channels' => ['sms'],
            ],
            'exam_publish' => [
                'title' => 'Custom Exam Alert',
                'subject' => 'Report: {exam_name}',
                'body' => 'Results out: {student_name}',
                'channels' => ['email', 'sms'],
            ],
        ]);
        $response->assertRedirect('/superadmin/notification-types');
        $this->assertTrue(\Illuminate\Support\Facades\Storage::disk('local')->exists('notification_types.json'));
    }

    public function test_superadmin_can_manage_blog_cms(): void
    {
        $superAdmin = User::where('email', 'superadmin@schoolcloud.com')->first();

        $response = $this->actingAs($superAdmin)->get('/superadmin/blog-cms');
        $response->assertStatus(200);

        // Store
        $response = $this->actingAs($superAdmin)->post('/superadmin/blog-cms', [
            'title' => 'Test Announcement',
            'summary' => 'This is a test summary.',
            'content' => 'Full test content here.',
            'author' => 'Test Author',
            'status' => 'published',
            'cover_url' => 'https://images.unsplash.com/photo-1546410531-bb4caa6b424d?w=200',
        ]);
        $response->assertRedirect('/superadmin/blog-cms');

        // Check in JSON
        $this->assertTrue(\Illuminate\Support\Facades\Storage::disk('local')->exists('blog_posts.json'));
        $posts = json_decode(\Illuminate\Support\Facades\Storage::disk('local')->get('blog_posts.json'), true);
        $testPost = collect($posts)->where('title', 'Test Announcement')->first();
        $this->assertNotNull($testPost);

        // Update
        $response = $this->actingAs($superAdmin)->put('/superadmin/blog-cms/' . $testPost['id'], [
            'title' => 'Updated Test Announcement',
            'summary' => 'Updated test summary.',
            'content' => 'Updated full test content here.',
            'author' => 'Test Author Edited',
            'status' => 'draft',
            'cover_url' => 'https://images.unsplash.com/photo-1546410531-bb4caa6b424d?w=200',
        ]);
        $response->assertRedirect('/superadmin/blog-cms');

        // Delete
        $response = $this->actingAs($superAdmin)->delete('/superadmin/blog-cms/' . $testPost['id']);
        $response->assertRedirect('/superadmin/blog-cms');
    }

    public function test_superadmin_can_manage_white_label_settings(): void
    {
        $superAdmin = User::where('email', 'superadmin@schoolcloud.com')->first();

        $response = $this->actingAs($superAdmin)->get('/superadmin/white-label');
        $response->assertStatus(200);

        $response = $this->actingAs($superAdmin)->post('/superadmin/white-label', [
            'app_name' => 'Custom Branding Title',
            'logo_url' => 'https://images.unsplash.com/photo-1546410531-bb4caa6b424d?w=200',
            'favicon_url' => 'https://images.unsplash.com/photo-1546410531-bb4caa6b424d?w=32',
            'copyright_text' => 'Copyright 2026 Custom Inc.',
            'support_email' => 'custom@branding.com',
            'support_phone' => '1234567890',
            'primary_color' => '#ff0000',
            'secondary_color' => '#0000ff',
        ]);
        $response->assertRedirect('/superadmin/white-label');
        $this->assertTrue(\Illuminate\Support\Facades\Storage::disk('local')->exists('white_label_settings.json'));
    }

    public function test_superadmin_can_manage_platform_settings(): void
    {
        $superAdmin = User::where('email', 'superadmin@schoolcloud.com')->first();

        $response = $this->actingAs($superAdmin)->get('/superadmin/platform-settings');
        $response->assertStatus(200);

        $response = $this->actingAs($superAdmin)->post('/superadmin/platform-settings', [
            'maintenance_mode' => '1',
            'enable_registration' => '0',
            'session_lifetime' => 60,
            'smtp_host' => 'smtp.test.io',
            'smtp_port' => 1025,
            'smtp_username' => 'testuser',
            'smtp_password' => 'testpass',
            'smtp_encryption' => 'ssl',
        ]);
        $response->assertRedirect('/superadmin/platform-settings');
        $this->assertTrue(\Illuminate\Support\Facades\Storage::disk('local')->exists('platform_settings.json'));
    }

    public function test_superadmin_profile_management(): void
    {
        $superAdmin = User::where('email', 'superadmin@schoolcloud.com')->first();

        // 1. Can view profile page
        $response = $this->actingAs($superAdmin)->get('/superadmin/profile');
        $response->assertStatus(200);

        // 2. Can update basic profile details
        $response = $this->actingAs($superAdmin)->post('/superadmin/profile/update', [
            'name' => 'Super Admin Updated Name',
            'email' => 'updated-superadmin@schoolcloud.com',
            'phone' => '+919999988888',
        ]);
        $response->assertRedirect('/superadmin/profile');
        $superAdmin->refresh();
        $this->assertEquals('Super Admin Updated Name', $superAdmin->name);
        $this->assertEquals('updated-superadmin@schoolcloud.com', $superAdmin->email);
        $this->assertEquals('+919999988888', $superAdmin->phone);

        // 3. Can update password
        $response = $this->actingAs($superAdmin)->post('/superadmin/profile/password', [
            'current_password' => 'SuperAdminSecurePass2026!',
            'password' => 'NewSuperAdminSecurePass2026!',
            'password_confirmation' => 'NewSuperAdminSecurePass2026!',
        ]);
        $response->assertRedirect('/superadmin/profile');
        $superAdmin->refresh();
        $this->assertTrue(Hash::check('NewSuperAdminSecurePass2026!', $superAdmin->password));
    }

    public function test_superadmin_settings_management(): void
    {
        $superAdmin = User::where('email', 'superadmin@schoolcloud.com')->first();

        // 1. Can view preferences page
        $response = $this->actingAs($superAdmin)->get('/superadmin/settings');
        $response->assertStatus(200);

        // 2. Can save settings
        $response = $this->actingAs($superAdmin)->post('/superadmin/settings/update', [
            'timezone' => 'Asia/Kolkata',
            'currency' => 'INR',
            'notification_email' => '1',
            'notification_system' => '1',
            'default_per_page' => 25,
            'mrr_target' => 850000,
        ]);
        $response->assertRedirect('/superadmin/settings');
        $this->assertTrue(\Illuminate\Support\Facades\Storage::disk('local')->exists('superadmin_settings.json'));
    }

    public function test_superadmin_dashboard_report_export(): void
    {
        $superAdmin = User::where('email', 'superadmin@schoolcloud.com')->first();

        $response = $this->actingAs($superAdmin)->get('/superadmin/dashboard/export-report');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->assertHeader('Content-Disposition', 'attachment; filename="Platform_Status_Report_' . now()->format('Y-m-d') . '.csv"');
    }

    public function test_school_admin_can_bulk_delete_students(): void
    {
        $schoolAdmin = User::where('email', 'admin@yis.com')->first();
        $schoolId = $schoolAdmin->school_id;

        // Create two dummy students to delete
        $class = SchoolClass::where('school_id', $schoolId)->first();
        $section = Section::where('class_id', $class->id)->first();
        $session = AcademicSession::where('school_id', $schoolId)->first();

        $student1 = Student::create([
            'school_id' => $schoolId,
            'class_id' => $class->id,
            'section_id' => $section->id,
            'academic_session_id' => $session->id,
            'admission_number' => 'DEL_TEST_01',
            'admission_sequence' => 9991,
            'admission_year' => 2026,
            'first_name' => 'Delete1',
            'last_name' => 'Student',
            'date_of_birth' => '2015-01-01',
            'gender' => 'male',
            'guardian_name' => 'Guardian',
            'guardian_phone' => '1234567890',
            'guardian_relationship' => 'guardian',
            'address' => 'Test Address',
            'city' => 'Test City',
            'state' => 'Test State',
            'pincode' => '123456',
            'admission_date' => '2026-06-01',
        ]);

        $student2 = Student::create([
            'school_id' => $schoolId,
            'class_id' => $class->id,
            'section_id' => $section->id,
            'academic_session_id' => $session->id,
            'admission_number' => 'DEL_TEST_02',
            'admission_sequence' => 9992,
            'admission_year' => 2026,
            'first_name' => 'Delete2',
            'last_name' => 'Student',
            'date_of_birth' => '2015-01-01',
            'gender' => 'male',
            'guardian_name' => 'Guardian',
            'guardian_phone' => '1234567890',
            'guardian_relationship' => 'guardian',
            'address' => 'Test Address',
            'city' => 'Test City',
            'state' => 'Test State',
            'pincode' => '123456',
            'admission_date' => '2026-06-01',
        ]);

        $response = $this->actingAs($schoolAdmin)
            ->withHeaders([
                'X-School-Code' => 'YIS2024',
                'X-Requested-With' => 'XMLHttpRequest'
            ])
            ->post('/school/students/bulk-delete', [
                'student_ids' => [$student1->id, $student2->id]
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Assert they are deactivated
        $this->assertEquals(0, $student1->refresh()->is_active);
        $this->assertEquals(0, $student2->refresh()->is_active);
    }

    public function test_school_admin_can_delete_single_student(): void
    {
        $schoolAdmin = User::where('email', 'admin@yis.com')->first();
        $schoolId = $schoolAdmin->school_id;

        $class = SchoolClass::where('school_id', $schoolId)->first();
        $section = Section::where('class_id', $class->id)->first();
        $session = AcademicSession::where('school_id', $schoolId)->first();

        $student = Student::create([
            'school_id' => $schoolId,
            'class_id' => $class->id,
            'section_id' => $section->id,
            'academic_session_id' => $session->id,
            'admission_number' => 'DEL_SINGLE_01',
            'admission_sequence' => 9993,
            'admission_year' => 2026,
            'first_name' => 'SingleDelete',
            'last_name' => 'Student',
            'date_of_birth' => '2015-01-01',
            'gender' => 'male',
            'guardian_name' => 'Guardian',
            'guardian_phone' => '1234567890',
            'guardian_relationship' => 'guardian',
            'address' => 'Test Address',
            'city' => 'Test City',
            'state' => 'Test State',
            'pincode' => '123456',
            'admission_date' => '2026-06-01',
        ]);

        $response = $this->actingAs($schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->delete("/school/students/{$student->id}");

        $response->assertRedirect('/school/students');
        $this->assertEquals(0, $student->refresh()->is_active);
    }

    public function test_fee_pages_load_with_zero_data(): void
    {
        $schoolAdmin = User::where('email', 'admin@yis.com')->first();
        $schoolId = $schoolAdmin->school_id;

        // Clear all fee structures, student fees, receipts, schedules, components, and fines for this school
        \App\Models\FeeStructure::where('school_id', $schoolId)->delete();
        \App\Models\StudentFee::where('school_id', $schoolId)->delete();
        \App\Models\FeeReceipt::where('school_id', $schoolId)->delete();
        \App\Models\FeeSchedule::where('school_id', $schoolId)->delete();
        \App\Models\FeeComponent::where('school_id', $schoolId)->delete();
        \App\Models\FeeFine::where('school_id', $schoolId)->delete();

        $response = $this->actingAs($schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->get('/school/fees/class-wise');
        $response->assertStatus(200);

        $response = $this->actingAs($schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->get('/school/fees/schedule-mapper');
        $response->assertStatus(200);

        $response = $this->actingAs($schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->get('/school/fees/receipts');
        $response->assertStatus(200);
    }

    public function test_student_fee_schedule_mapping_and_sync(): void
    {
        $schoolAdmin = User::where('email', 'admin@yis.com')->first();
        $schoolId = $schoolAdmin->school_id;

        // Clear existing students to have a clean slate
        Student::where('school_id', $schoolId)->delete();

        // 1. Get or create a session
        $session = \App\Models\AcademicSession::firstOrCreate([
            'school_id' => $schoolId,
            'name' => '2025-2026',
        ], [
            'status' => 'active',
            'start_date' => '2025-04-01',
            'end_date' => '2026-03-31',
        ]);

        // 2. Create a dummy student
        $class = SchoolClass::where('school_id', $schoolId)->first();
        $section = Section::where('class_id', $class->id)->first();

        $student = Student::create([
            'school_id' => $schoolId,
            'class_id' => $class->id,
            'section_id' => $section->id,
            'academic_session_id' => $session->id,
            'admission_number' => 'ADM-999',
            'admission_sequence' => 9993,
            'admission_year' => 2026,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'date_of_birth' => '2015-05-15',
            'gender' => 'Male',
            'guardian_name' => 'Guardian',
            'guardian_phone' => '1234567890',
            'guardian_relationship' => 'guardian',
            'address' => 'Test Address',
            'city' => 'Test City',
            'state' => 'Test State',
            'pincode' => '123456',
            'admission_date' => '2026-06-01',
            'is_active' => true,
        ]);

        // 3. Create a Fee Schedule
        $schedule = \App\Models\FeeSchedule::create([
            'school_id' => $schoolId,
            'academic_session_id' => $session->id,
            'classes' => 'Class-1',
            'no_of_installments' => 2,
            'name' => 'Test Schedule Spec',
            'start_date' => '2025-04-01',
            'end_date' => '2026-03-31',
        ]);

        // 4. Post mapping request
        $response = $this->actingAs($schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->post('/school/fees/schedule-mapper', [
                'student_schedules' => [
                    $student->id => $schedule->id,
                ],
            ]);

        $response->assertStatus(302); // Redirect back
        
        // Assert student was updated with the fee schedule id
        $student->refresh();
        $this->assertEquals($schedule->id, $student->fee_schedule_id);
    }

    public function test_school_signup_submits_successfully(): void
    {
        $response = $this->post('/school/signup', [
            'name'          => 'Greenwood Test School',
            'director_name' => 'Dr. Greenwood Admin',
            'school_type'   => 'CBSE',
            'email'         => 'contact@greenwoodtest.com',
            'phone'         => '+91 99999 88888',
        ]);

        $response->assertRedirect(route('school.signup'));
        $response->assertSessionHas('success');

        // Assert database record exists in school_requests
        $this->assertDatabaseHas('school_requests', [
            'name'          => 'Greenwood Test School',
            'director_name' => 'Dr. Greenwood Admin',
            'school_type'   => 'CBSE',
            'email'         => 'contact@greenwoodtest.com',
            'admin_name'    => 'Dr. Greenwood Admin',
            'admin_email'   => 'contact@greenwoodtest.com',
            'status'        => 'pending',
        ]);
    }

    public function test_student_profile_360_and_pdf_download(): void
    {
        $schoolAdmin = User::where('email', 'admin@yis.com')->first();
        $schoolId = $schoolAdmin->school_id;
        $student = Student::where('school_id', $schoolId)->first();

        // 1. Test student show profile 360 page loads and passes all variables
        $response = $this->actingAs($schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->get("/school/students/{$student->id}");

        $response->assertStatus(200);
        $response->assertViewHasAll([
            'student',
            'attendances',
            'totalDays',
            'presentDays',
            'absentDays',
            'lateDays',
            'attendancePercentage',
            'siblings',
            'marks',
            'fees',
            'refunds',
            'receipts',
            'busAttendances',
            'offlineTests',
            'leaves',
        ]);

        // 2. Test student download admission form PDF
        $response2 = $this->actingAs($schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->get("/school/students/{$student->id}/download-pdf");
        $response2->assertStatus(200);

        // 3. Test student download form-only PDF
        $response3 = $this->actingAs($schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->get("/school/students/{$student->id}/download-pdf?type=form_only");
        $response3->assertStatus(200);
    }

    /**
     * Test transport opted student fees applicability and student mapping updates.
     */
    public function test_transport_opted_student_fees_applicability(): void
    {
        $schoolAdmin = User::where('email', 'admin@yis.com')->first();
        $schoolId = $schoolAdmin->school_id;
        $student = Student::where('school_id', $schoolId)->first();

        // 1. Verify default state - transport not opted
        $student->update([
            'transport_opted' => false,
            'transport_route' => null,
            'transport_pick_fare' => 500,
            'transport_drop_fare' => 500
        ]);
        $this->assertFalse($student->hasTransportAssigned());
        $this->assertEquals(0, $student->transportTotalFare);

        // 2. Verify state when opted and route assigned
        $student->update([
            'transport_opted' => true,
            'transport_route' => 'Route A',
            'transport_route_id' => 1
        ]);
        $this->assertTrue($student->hasTransportAssigned());
        $this->assertEquals(1000, $student->transportTotalFare);

        // 3. Test POST student mapping update
        $route = \App\Models\TransportRoute::firstOrCreate(
            ['school_id' => $schoolId, 'name' => 'Route Z'],
            ['pick_fare' => 600, 'drop_fare' => 400]
        );

        $response = $this->actingAs($schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->post('/school/transport/student-route-mapping', [
                'student_id' => $student->id,
                'transport_route_id' => $route->id,
                'transport_route' => $route->name,
                'transport_pick_fare' => 600,
                'transport_drop_fare' => 400,
                'transport_pickup_location' => 'Pickup Spot Z',
                'transport_drop_location' => 'Drop Spot Z',
                'transport_pickup_time' => '08:00',
                'transport_drop_time' => '16:00',
                'transport_calendar_start' => '2026-07-09',
                'transport_month' => 'July 2026'
            ]);

        $response->assertRedirect();
        
        $student->refresh();
        $this->assertTrue($student->transport_opted);
        $this->assertEquals('Route Z', $student->transport_route);
        $this->assertEquals(600, $student->transport_pick_fare);
        $this->assertEquals(400, $student->transport_drop_fare);
        $this->assertEquals(1000, $student->transportTotalFare);
        $this->assertTrue($student->hasTransportAssigned());

        // 4. Clear/Opt-out student transport
        $response = $this->actingAs($schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->post('/school/transport/student-route-mapping', [
                'student_id' => $student->id,
                'transport_route_id' => '',
                'transport_route' => ''
            ]);

        $response->assertRedirect();
        
        $student->refresh();
        $this->assertFalse($student->transport_opted);
        $this->assertNull($student->transport_route);
        $this->assertEquals(0, $student->transportTotalFare);
        $this->assertFalse($student->hasTransportAssigned());
    }

    /**
     * Test vehicle expense syncing to general school expenses report.
     */
    public function test_vehicle_expense_sync_to_school_expenses_report(): void
    {
        $schoolAdmin = User::where('email', 'admin@yis.com')->first();
        $schoolId = $schoolAdmin->school_id;
        
        $vehicle = \App\Models\Vehicle::firstOrCreate(
            ['school_id' => $schoolId, 'vehicle_no' => 'MH-12-TP-9999'],
            ['capacity' => 40, 'status' => true]
        );

        // 1. Create a VehicleExpense via POST
        $response = $this->actingAs($schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->post('/school/transport/vehicle-expenses', [
                'vehicle_id' => $vehicle->id,
                'expense_type' => 'Custom Repair',
                'amount' => 4500.00,
                'date' => '2026-07-09',
                'description' => 'Replaced bus front bumper'
            ]);

        $response->assertRedirect();

        $vehicleExpense = \App\Models\VehicleExpense::where('vehicle_id', $vehicle->id)->where('expense_type', 'Custom Repair')->first();
        $this->assertNotNull($vehicleExpense);
        $this->assertNotNull($vehicleExpense->school_expense_id);

        $schoolExpense = \App\Models\SchoolExpense::find($vehicleExpense->school_expense_id);
        $this->assertNotNull($schoolExpense);
        $this->assertEquals('transport', $schoolExpense->category);
        $this->assertEquals(4500.00, $schoolExpense->amount);
        $this->assertEquals('2026-07-09', $schoolExpense->expense_date->format('Y-m-d'));
        $this->assertStringContainsString('Vehicle Expense: Custom Repair', $schoolExpense->title);

        // 2. Update the VehicleExpense via POST (with ID)
        $response2 = $this->actingAs($schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->post('/school/transport/vehicle-expenses', [
                'id' => $vehicleExpense->id,
                'vehicle_id' => $vehicle->id,
                'expense_type' => 'Custom Repair - Updated',
                'amount' => 5000.00,
                'date' => '2026-07-10',
                'description' => 'Replaced bus front bumper and side mirrors'
            ]);

        $response2->assertRedirect();
        
        $schoolExpense->refresh();
        $this->assertEquals(5000.00, $schoolExpense->amount);
        $this->assertEquals('2026-07-10', $schoolExpense->expense_date->format('Y-m-d'));
        $this->assertStringContainsString('Vehicle Expense: Custom Repair - Updated', $schoolExpense->title);

        // 3. Delete the VehicleExpense
        $response3 = $this->actingAs($schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->post('/school/transport/delete', [
                'id' => $vehicleExpense->id,
                'type' => 'expense'
            ]);

        $response3->assertRedirect();

        $this->assertNull(\App\Models\VehicleExpense::find($vehicleExpense->id));
        $this->assertNull(\App\Models\SchoolExpense::find($schoolExpense->id));
    }

    /**
     * Test that refunding and then cancelling a refund preserves the fee structure exactly.
     */
    public function test_refund_cancellation_preserves_fee_structure(): void
    {
        $schoolAdmin = \App\Models\User::where('email', 'admin@yis.com')->first();
        $student = \App\Models\Student::where('school_id', $schoolAdmin->school_id)->first();
        $academicSession = \App\Models\AcademicSession::where('school_id', $schoolAdmin->school_id)->where('is_current', true)->first();

        // 1. Create a FeeComponent
        $feeComponent = \App\Models\FeeComponent::create([
            'school_id' => $student->school_id,
            'academic_session_id' => $academicSession->id,
            'head_name' => 'Tuition Fee Head Test',
            'component_name' => 'Tuition Fee Component Test',
            'admission_type' => 'both',
            'gender' => 'both'
        ]);

        // 2. Create a FeeSchedule if none exists
        $feeSchedule = \App\Models\FeeSchedule::where('school_id', $student->school_id)->first();
        if (!$feeSchedule) {
            $feeSchedule = \App\Models\FeeSchedule::create([
                'school_id' => $student->school_id,
                'academic_session_id' => $academicSession->id,
                'classes' => json_encode(['Class 9', 'Class 10']),
                'no_of_installments' => 1,
                'name' => 'General Schedule Test',
                'start_date' => '2026-04-01',
                'end_date' => '2027-03-31'
            ]);
        }

        $category = \App\Models\StudentCategory::where('school_id', $student->school_id)->first();
        if (!$category) {
            $category = \App\Models\StudentCategory::create([
                'school_id' => $student->school_id,
                'name' => 'Day boarding'
            ]);
        }

        // 3. Create a ClassWiseFee record for this student's class
        $classWiseFee = \App\Models\ClassWiseFee::create([
            'school_id' => $student->school_id,
            'academic_session_id' => $academicSession->id,
            'class_id' => $student->class_id,
            'section_id' => null,
            'fee_schedule_id' => $feeSchedule->id,
            'student_category_id' => $category->id,
            'fee_component_id' => $feeComponent->id,
            'is_active' => true,
            'amount' => 1500.00,
            'installments' => [
                [
                    'installment_no' => 1,
                    'amount' => 1500.00,
                    'date_range' => '01/04/2026 - 30/04/2026'
                ]
            ]
        ]);

        // Clean slate for student
        \App\Models\StudentFee::where('student_id', $student->id)->delete();
        \App\Models\FeeInvoice::where('student_id', $student->id)->delete();
        \App\Models\FeeRefund::where('student_id', $student->id)->delete();

        // Update student schedule ID to match
        $student->fee_schedule_id = $feeSchedule->id;
        $student->save();

        // 1. Sync student fees
        \App\Http\Controllers\School\FeeManagementController::syncStudentFees($student);
        $fees = \App\Models\StudentFee::where('student_id', $student->id)->get();
        $totalOriginal = $fees->sum('amount');
        
        $this->assertGreaterThan(0, $totalOriginal);

        // 2. Pay 1500 on the first fee component
        $firstFee = $fees->first();
        $firstFee->paid_amount = 1500.00;
        $firstFee->status = 'paid';
        $firstFee->save();

        // Create the corresponding payment invoice
        $paymentInvoice = \App\Models\FeeInvoice::create([
            'school_id' => 1,
            'student_id' => $student->id,
            'created_by' => $schoolAdmin->id,
            'invoice_number' => 'INV-1-TEST-PAY',
            'installment_no' => $firstFee->installment_no,
            'type' => 'payment',
            'status' => 'paid',
            'amount' => 1500.00,
            'payment_mode' => 'cash',
            'payment_date' => now()->toDateString(),
        ]);

        // 3. Process a refund of 500
        $responseRefund = $this->actingAs($schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->post('/school/fees/student-wise', [
                'action' => 'process_refund',
                'student_id' => $student->id,
                'refund_date' => now()->toDateString(),
                'slip_no' => 'REF-999998',
                'payment_mode' => 'cash',
                'reason' => 'Test Refund Action',
                'fee_ids' => [$firstFee->id],
                'amount' => 500
            ]);

        $responseRefund->assertRedirect();
        
        // Assert fee record paid amount remains unchanged (1500) under the new model
        $firstFee->refresh();
        $this->assertEquals(1500.00, $firstFee->paid_amount);
        $this->assertEquals('paid', $firstFee->status);

        // Assert FeeRefund log is created
        $refundLog = \App\Models\FeeRefund::where('student_id', $student->id)->first();
        $this->assertNotNull($refundLog);
        $this->assertEquals(500.00, $refundLog->amount);

        // Assert FeeInvoice of type refund is created
        $refundInvoice = \App\Models\FeeInvoice::where('student_id', $student->id)->where('type', 'refund')->first();
        $this->assertNotNull($refundInvoice);
        $this->assertEquals('refunded', $refundInvoice->status);

        // 4. Cancel the refund
        $responseCancel = $this->actingAs($schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->post('/school/fees/student-wise', [
                'action' => 'cancel_invoice',
                'student_id' => $student->id,
                'invoice_no' => $refundInvoice->invoice_number,
                'remarks' => 'Cancel Test Refund Action',
                'installment_no' => $firstFee->installment_no
            ]);

        $responseCancel->assertRedirect();

        // 5. Assertions
        $firstFee->refresh();
        $refundInvoice->refresh();

        // Paid amount must remain 1500.00 (untouched)
        $this->assertEquals(1500.00, $firstFee->paid_amount);
        // Status of the component must still be 'paid'
        $this->assertEquals('paid', $firstFee->status);
        // Refund invoice status must be 'cancelled'
        $this->assertEquals('cancelled', $refundInvoice->status);
        // The FeeRefund log must be deleted
        $this->assertNull(\App\Models\FeeRefund::find($refundLog->id));

        // Sync again and verify total fees and installments are completely unchanged
        \App\Http\Controllers\School\FeeManagementController::syncStudentFees($student);
        $finalFees = \App\Models\StudentFee::where('student_id', $student->id)->get();
        $this->assertEquals($totalOriginal, $finalFees->sum('amount'));
    }

    /**
     * Test cancellation of legacy refunds by slip_no where no FeeInvoice record exists.
     */
    public function test_legacy_refund_cancellation_by_slip_no(): void
    {
        $schoolAdmin = \App\Models\User::where('email', 'admin@yis.com')->first();
        $student = \App\Models\Student::where('school_id', $schoolAdmin->school_id)->first();
        $academicSession = \App\Models\AcademicSession::where('school_id', $schoolAdmin->school_id)->where('is_current', true)->first();

        // 1. Create a FeeComponent
        $feeComponent = \App\Models\FeeComponent::create([
            'school_id' => $student->school_id,
            'academic_session_id' => $academicSession->id,
            'head_name' => 'Legacy Fee Head Test',
            'component_name' => 'Legacy Fee Component Test',
            'admission_type' => 'both',
            'gender' => 'both'
        ]);

        // 2. Create a FeeSchedule if none exists
        $feeSchedule = \App\Models\FeeSchedule::where('school_id', $student->school_id)->first();
        if (!$feeSchedule) {
            $feeSchedule = \App\Models\FeeSchedule::create([
                'school_id' => $student->school_id,
                'academic_session_id' => $academicSession->id,
                'classes' => json_encode(['Class 9', 'Class 10']),
                'no_of_installments' => 1,
                'name' => 'General Schedule Test',
                'start_date' => '2026-04-01',
                'end_date' => '2027-03-31'
            ]);
        }

        $category = \App\Models\StudentCategory::where('school_id', $student->school_id)->first();
        if (!$category) {
            $category = \App\Models\StudentCategory::create([
                'school_id' => $student->school_id,
                'name' => 'Day boarding'
            ]);
        }

        // 3. Create a ClassWiseFee record for this student's class
        $classWiseFee = \App\Models\ClassWiseFee::create([
            'school_id' => $student->school_id,
            'academic_session_id' => $academicSession->id,
            'class_id' => $student->class_id,
            'section_id' => null,
            'fee_schedule_id' => $feeSchedule->id,
            'student_category_id' => $category->id,
            'fee_component_id' => $feeComponent->id,
            'is_active' => true,
            'amount' => 1500.00,
            'installments' => [
                [
                    'installment_no' => 1,
                    'amount' => 1500.00,
                    'date_range' => '01/04/2026 - 30/04/2026'
                ]
            ]
        ]);

        // Clean slate for student
        \App\Models\StudentFee::where('student_id', $student->id)->delete();
        \App\Models\FeeInvoice::where('student_id', $student->id)->delete();
        \App\Models\FeeRefund::where('student_id', $student->id)->delete();

        // Update student schedule ID to match
        $student->fee_schedule_id = $feeSchedule->id;
        $student->save();

        // Sync student fees
        \App\Http\Controllers\School\FeeManagementController::syncStudentFees($student);
        $fees = \App\Models\StudentFee::where('student_id', $student->id)->get();
        $this->assertCount(1, $fees);
        $firstFee = $fees->first();

        // Pay 1500 on the first fee component
        $firstFee->paid_amount = 1500.00;
        $firstFee->status = 'paid';
        $firstFee->save();

        // Create legacy FeeRefund directly in database (no FeeInvoice created)
        $refundLog = \App\Models\FeeRefund::create([
            'school_id'      => $student->school_id,
            'student_id'     => $student->id,
            'student_fee_id' => $firstFee->id,
            'amount'         => 500.00,
            'refund_date'    => now()->toDateString(),
            'reason'         => "Legacy Refund",
            'slip_no'        => 'REF-LEGACY-123456',
            'payment_mode'   => 'cash'
        ]);

        // Cancel the legacy refund using its slip_no
        $responseCancel = $this->actingAs($schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->post('/school/fees/student-wise', [
                'action' => 'cancel_invoice',
                'student_id' => $student->id,
                'invoice_no' => 'REF-LEGACY-123456',
                'remarks' => 'Cancel Legacy Refund Remarks',
                'installment_no' => $firstFee->installment_no
            ]);

        $responseCancel->assertRedirect();

        // Assert FeeRefund log is deleted
        $this->assertNull(\App\Models\FeeRefund::find($refundLog->id));

        // Assert cancel_refund FeeInvoice entry is created to record the ledger entry
        $cancelInvoice = \App\Models\FeeInvoice::where('student_id', $student->id)
            ->where('type', 'cancel_refund')
            ->first();
        $this->assertNotNull($cancelInvoice);
        $this->assertEquals(500.00, $cancelInvoice->amount);
        $this->assertEquals('cancelled', $cancelInvoice->status);

        // Paid amount and status remain untouched
        $firstFee->refresh();
        $this->assertEquals(1500.00, $firstFee->paid_amount);
        $this->assertEquals('paid', $firstFee->status);
    }

    /**
     * Test that the student-invoices AJAX endpoint resolves successfully for both payment and refund invoices.
     */
    public function test_get_student_invoices_endpoint_resolves_without_error(): void
    {
        $schoolAdmin = \App\Models\User::where('email', 'admin@yis.com')->first();
        $student = \App\Models\Student::where('school_id', $schoolAdmin->school_id)->first();
        $academicSession = \App\Models\AcademicSession::where('school_id', $schoolAdmin->school_id)->where('is_current', true)->first();

        // 1. Create a FeeComponent
        $feeComponent = \App\Models\FeeComponent::create([
            'school_id' => $student->school_id,
            'academic_session_id' => $academicSession->id,
            'head_name' => 'Tuition Fee Head Test',
            'component_name' => 'Tuition Fee Component Test',
            'admission_type' => 'both',
            'gender' => 'both'
        ]);

        // 2. Query/Create FeeCategory and FeeSchedule
        $feeCategory = \App\Models\FeeCategory::where('school_id', $student->school_id)->first();
        if (!$feeCategory) {
            $feeCategory = \App\Models\FeeCategory::create([
                'school_id' => $student->school_id,
                'name' => 'Tuition Fee Category'
            ]);
        }

        $feeSchedule = \App\Models\FeeSchedule::where('school_id', $student->school_id)->first();
        if (!$feeSchedule) {
            $feeSchedule = \App\Models\FeeSchedule::create([
                'school_id' => $student->school_id,
                'academic_session_id' => $academicSession->id,
                'classes' => json_encode(['Class 9', 'Class 10']),
                'no_of_installments' => 1,
                'name' => 'General Schedule Test',
                'start_date' => '2026-04-01',
                'end_date' => '2027-03-31'
            ]);
        }

        // 3. Create student fee
        $studentFee = \App\Models\StudentFee::create([
            'school_id' => $student->school_id,
            'student_id' => $student->id,
            'fee_category_id' => $feeCategory->id,
            'fee_schedule_id' => $feeSchedule->id,
            'fee_component_id' => $feeComponent->id,
            'installment_no' => 1,
            'amount' => 1000.00,
            'paid_amount' => 1000.00,
            'due_date' => now()->toDateString(),
            'status' => 'paid',
        ]);

        // 3. Create a payment invoice (flat array layout)
        \App\Models\FeeInvoice::create([
            'school_id' => $student->school_id,
            'student_id' => $student->id,
            'created_by' => $schoolAdmin->id,
            'invoice_number' => 'INV-PAY-123',
            'installment_no' => 1,
            'type' => 'payment',
            'status' => 'paid',
            'amount' => 1000.00,
            'payment_mode' => 'cash',
            'payment_date' => now()->toDateString(),
            'payment_details' => json_encode([
                [
                    'student_fee_id' => $studentFee->id,
                    'component_name' => 'Tuition Fee Component Test',
                    'installment_no' => 1,
                    'amount_paid' => 1000.00
                ]
            ])
        ]);

        // 4. Create a refund invoice (nested components layout)
        \App\Models\FeeInvoice::create([
            'school_id' => $student->school_id,
            'student_id' => $student->id,
            'created_by' => $schoolAdmin->id,
            'invoice_number' => 'INV-REF-123',
            'installment_no' => 1,
            'type' => 'refund',
            'status' => 'refunded',
            'amount' => 500.00,
            'payment_mode' => 'cash',
            'payment_date' => now()->toDateString(),
            'payment_details' => json_encode([
                'slip_no' => 'REF-SLIP-123',
                'components' => [
                    [
                        'student_fee_id' => $studentFee->id,
                        'component_name' => 'Tuition Fee Component Test',
                        'installment_no' => 1,
                        'amount_paid' => 500.00
                    ]
                ]
            ])
        ]);

        // 5. Query the endpoint
        $response = $this->actingAs($schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->get("/school/fees/student-invoices/{$student->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);

        // Confirm both invoices are returned and parsed
        $invoices = $response->json('invoices');
        $this->assertCount(2, $invoices);
        
        // Confirm first invoice components are parsed
        $this->assertCount(1, $invoices[0]['components']);
        
        // Confirm second invoice (refund) components are parsed and don't throw error
        $this->assertCount(1, $invoices[1]['components']);
    }
}





