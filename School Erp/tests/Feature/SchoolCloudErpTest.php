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
        $this->assertTrue(str_contains($response->streamedContent(), 'employee_id'));
        $this->assertTrue(str_contains($response->streamedContent(), 'alternate_phone'));

        // 2. Perform Import
        $csvContent = "employee_id,first_name,last_name,email,phone,alternate_phone,department,designation,epf_uan\n" .
                      "EMPTEST999,Jane,Smith,jane.smith@yis.com,9876543210,9876543211,Academics,Teacher,UAN999111";

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
        $this->assertEquals('9876543211', $staff->additional_fields['alternate_phone']);
        $this->assertEquals('UAN999111', $staff->additional_fields['epf_uan']);
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
}



