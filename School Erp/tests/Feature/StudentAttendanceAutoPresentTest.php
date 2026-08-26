<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudentSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StudentAttendanceAutoPresentTest extends TestCase
{
    use RefreshDatabase;

    protected School $school;
    protected User $schoolAdmin;
    protected AcademicSession $session;
    protected SchoolClass $class;
    protected Section $section;
    /** @var Student[] */
    protected array $students = [];

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
            'name'          => 'Vedant Public School',
            'code'          => 'VPS',
            'custom_domain' => 'vps.erp.test',
            'status'        => 'active',
        ]);

        $this->schoolAdmin = User::factory()->create([
            'school_id' => $this->school->id,
            'role'      => 'school_admin',
            'email'     => 'admin@vps.erp.test',
        ]);
        $this->schoolAdmin->assignRole('school_admin');

        $this->session = AcademicSession::create([
            'school_id'  => $this->school->id,
            'name'       => 'Apr 2026 - Mar 2027',
            'start_date' => '2026-04-01',
            'end_date'   => '2027-03-31',
            'is_current' => true,
        ]);

        $this->class = SchoolClass::create([
            'school_id'    => $this->school->id,
            'name'         => 'Class 10',
            'numeric_name' => 10,
        ]);

        $this->section = Section::create([
            'school_id' => $this->school->id,
            'class_id'  => $this->class->id,
            'name'      => 'A',
        ]);

        // Create 10 students
        $names = [
            'LAKSHAYA TIWARI',
            'AYUSH YADAV',
            'MANYANK YADAV',
            'SHREYA YADAV',
            'SHRISHI YADAV',
            'DAKASH KUSHWAHA',
            'ANKITA PATEL',
            'SHRUTI SHUKLA',
            'MARUTI SHUKLA',
            'PIYUSH YADAV',
        ];

        for ($i = 0; $i < 10; $i++) {
            $parts = explode(' ', $names[$i], 2);
            $firstName = $parts[0];
            $lastName = $parts[1] ?? 'Student';

            $student = Student::create([
                'school_id'             => $this->school->id,
                'first_name'            => $firstName,
                'last_name'             => $lastName,
                'admission_number'      => 'JPPS' . (16 + $i),
                'admission_sequence'    => 16 + $i,
                'admission_year'        => 2026,
                'admission_date'        => '2026-04-01',
                'date_of_birth'         => '2015-01-01',
                'gender'                => 'male',
                'father_name'           => 'Father ' . $lastName,
                'phone'                 => '987654321' . $i,
                'guardian_name'         => 'Father ' . $lastName,
                'guardian_phone'        => '987654321' . $i,
                'guardian_relationship' => 'father',
                'address'               => 'Test Address',
                'city'                  => 'Test City',
                'state'                 => 'Test State',
                'pincode'               => '110001',
                'class_id'              => $this->class->id,
                'section_id'            => $this->section->id,
                'academic_session_id'   => $this->session->id,
                'roll_number'           => (string)($i + 1),
                'status'                => 'active',
                'is_active'             => true,
            ]);

            StudentSession::create([
                'school_id'           => $this->school->id,
                'student_id'          => $student->id,
                'academic_session_id' => $this->session->id,
                'class_id'            => $this->class->id,
                'section_id'          => $this->section->id,
                'roll_number'         => (string)($i + 1),
                'status'              => 'active',
            ]);

            $this->students[$i + 1] = $student;
        }
    }

    /**
     * TEST 1:
     * - Select HD for Student 2
     * - Select A for Student 4
     * - Select L for Student 5
     * - Select A for Student 8
     * - Leave all remaining students (1, 3, 6, 7, 9, 10) unmarked.
     * - Click SAVE.
     * - Verify that explicitly selected students retain HD, A, L respectively.
     * - Verify that every unmarked student is saved as P (Present).
     */
    public function test_test1_hd_a_l_marking_and_all_unmarked_students_saved_as_present(): void
    {
        $date = '2026-08-25';

        // Simulated form submission (unmarked students have student_id but no status key)
        $attendancePayload = [
            0 => ['student_id' => $this->students[1]->id], // Unmarked -> should be P
            1 => ['student_id' => $this->students[2]->id, 'status' => 'half_day'], // HD
            2 => ['student_id' => $this->students[3]->id], // Unmarked -> should be P
            3 => ['student_id' => $this->students[4]->id, 'status' => 'absent'], // A
            4 => ['student_id' => $this->students[5]->id, 'status' => 'leave'], // L
            5 => ['student_id' => $this->students[6]->id], // Unmarked -> should be P
            6 => ['student_id' => $this->students[7]->id], // Unmarked -> should be P
            7 => ['student_id' => $this->students[8]->id, 'status' => 'absent'], // A
            8 => ['student_id' => $this->students[9]->id], // Unmarked -> should be P
            9 => ['student_id' => $this->students[10]->id], // Unmarked -> should be P
        ];

        $response = $this->actingAs($this->schoolAdmin)->post(route('school.attendance.students.store'), [
            'section_id'          => $this->section->id,
            'academic_session_id' => $this->session->id,
            'date'                => $date,
            'attendance'          => $attendancePayload,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Attendance marked successfully.');

        // Verify explicit markings
        $this->assertEquals('half_day', StudentAttendance::where('student_id', $this->students[2]->id)->whereDate('date', $date)->value('status'));
        $this->assertEquals('absent', StudentAttendance::where('student_id', $this->students[4]->id)->whereDate('date', $date)->value('status'));
        $this->assertEquals('leave', StudentAttendance::where('student_id', $this->students[5]->id)->whereDate('date', $date)->value('status'));
        $this->assertEquals('absent', StudentAttendance::where('student_id', $this->students[8]->id)->whereDate('date', $date)->value('status'));

        // Verify that ALL unmarked students are saved as 'present' (P)
        $unmarkedStudentNumbers = [1, 3, 6, 7, 9, 10];
        foreach ($unmarkedStudentNumbers as $num) {
            $this->assertEquals('present', StudentAttendance::where('student_id', $this->students[$num]->id)->whereDate('date', $date)->value('status'));
        }

        // Total saved attendances in DB must be exactly 10 (no student is left missing or not marked)
        $this->assertEquals(10, StudentAttendance::where('school_id', $this->school->id)->whereDate('date', $date)->count());
    }

    /**
     * TEST 2:
     * - Mark only one student as A.
     * - Leave everyone else unmarked.
     * - Save.
     * - Verify that only that student is A and all others are P.
     */
    public function test_test2_mark_only_one_student_as_absent(): void
    {
        $date = '2026-08-25';

        $attendancePayload = [];
        for ($i = 1; $i <= 10; $i++) {
            if ($i === 4) {
                $attendancePayload[] = ['student_id' => $this->students[$i]->id, 'status' => 'absent'];
            } else {
                $attendancePayload[] = ['student_id' => $this->students[$i]->id];
            }
        }

        $response = $this->actingAs($this->schoolAdmin)->post(route('school.attendance.students.store'), [
            'section_id'          => $this->section->id,
            'academic_session_id' => $this->session->id,
            'date'                => $date,
            'attendance'          => $attendancePayload,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('student_attendances', [
            'student_id' => $this->students[4]->id,
            'status'     => 'absent',
        ]);

        for ($i = 1; $i <= 10; $i++) {
            if ($i !== 4) {
                $this->assertDatabaseHas('student_attendances', [
                    'student_id' => $this->students[$i]->id,
                    'status'     => 'present',
                ]);
            }
        }

        $this->assertEquals(1, StudentAttendance::where('status', 'absent')->count());
        $this->assertEquals(9, StudentAttendance::where('status', 'present')->count());
    }

    /**
     * TEST 3:
     * - Mark only one student as HD.
     * - Leave everyone else unmarked.
     * - Save.
     * - Verify that only that student is HD and all others are P.
     */
    public function test_test3_mark_only_one_student_as_half_day(): void
    {
        $date = '2026-08-25';

        $attendancePayload = [];
        for ($i = 1; $i <= 10; $i++) {
            if ($i === 2) {
                $attendancePayload[] = ['student_id' => $this->students[$i]->id, 'status' => 'half_day'];
            } else {
                $attendancePayload[] = ['student_id' => $this->students[$i]->id];
            }
        }

        $response = $this->actingAs($this->schoolAdmin)->post(route('school.attendance.students.store'), [
            'section_id'          => $this->section->id,
            'academic_session_id' => $this->session->id,
            'date'                => $date,
            'attendance'          => $attendancePayload,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('student_attendances', [
            'student_id' => $this->students[2]->id,
            'status'     => 'half_day',
        ]);

        for ($i = 1; $i <= 10; $i++) {
            if ($i !== 2) {
                $this->assertDatabaseHas('student_attendances', [
                    'student_id' => $this->students[$i]->id,
                    'status'     => 'present',
                ]);
            }
        }

        $this->assertEquals(1, StudentAttendance::where('status', 'half_day')->count());
        $this->assertEquals(9, StudentAttendance::where('status', 'present')->count());
    }

    /**
     * TEST 4:
     * - Mark only one student as L.
     * - Leave everyone else unmarked.
     * - Save.
     * - Verify that only that student is L and all others are P.
     */
    public function test_test4_mark_only_one_student_as_leave(): void
    {
        $date = '2026-08-25';

        $attendancePayload = [];
        for ($i = 1; $i <= 10; $i++) {
            if ($i === 5) {
                $attendancePayload[] = ['student_id' => $this->students[$i]->id, 'status' => 'leave'];
            } else {
                $attendancePayload[] = ['student_id' => $this->students[$i]->id];
            }
        }

        $response = $this->actingAs($this->schoolAdmin)->post(route('school.attendance.students.store'), [
            'section_id'          => $this->section->id,
            'academic_session_id' => $this->session->id,
            'date'                => $date,
            'attendance'          => $attendancePayload,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('student_attendances', [
            'student_id' => $this->students[5]->id,
            'status'     => 'leave',
        ]);

        for ($i = 1; $i <= 10; $i++) {
            if ($i !== 5) {
                $this->assertDatabaseHas('student_attendances', [
                    'student_id' => $this->students[$i]->id,
                    'status'     => 'present',
                ]);
            }
        }

        $this->assertEquals(1, StudentAttendance::where('status', 'leave')->count());
        $this->assertEquals(9, StudentAttendance::where('status', 'present')->count());
    }

    /**
     * TEST 5:
     * - Open the attendance again after saving.
     * - Verify that previously unmarked students now display P.
     * - Verify that HD/A/L statuses are preserved correctly.
     */
    public function test_test5_reload_attendance_table_after_saving_displays_correct_statuses(): void
    {
        $date = '2026-08-25';

        // 1. Mark and Save (Student 2=HD, Student 4=A, Student 5=L, others unmarked)
        $attendancePayload = [
            0 => ['student_id' => $this->students[1]->id],
            1 => ['student_id' => $this->students[2]->id, 'status' => 'half_day'],
            2 => ['student_id' => $this->students[3]->id],
            3 => ['student_id' => $this->students[4]->id, 'status' => 'absent'],
            4 => ['student_id' => $this->students[5]->id, 'status' => 'leave'],
            5 => ['student_id' => $this->students[6]->id],
            6 => ['student_id' => $this->students[7]->id],
            7 => ['student_id' => $this->students[8]->id, 'status' => 'absent'],
            8 => ['student_id' => $this->students[9]->id],
            9 => ['student_id' => $this->students[10]->id],
        ];

        $this->actingAs($this->schoolAdmin)->post(route('school.attendance.students.store'), [
            'section_id'          => $this->section->id,
            'academic_session_id' => $this->session->id,
            'date'                => $date,
            'attendance'          => $attendancePayload,
        ]);

        // 2. Load attendance table via AJAX endpoint (POST /school/attendance/students/load)
        $loadResponse = $this->actingAs($this->schoolAdmin)->postJson(route('school.attendance.students.load'), [
            'section_id'          => $this->section->id,
            'academic_session_id' => $this->session->id,
            'date'                => $date,
        ]);

        $loadResponse->assertOk();
        $loadResponse->assertJson(['success' => true]);

        $html = $loadResponse->json('html');

        // Check that student rows have correct data-status attributes in returned HTML
        $this->assertStringContainsString('data-student-id="' . $this->students[1]->id . '" data-status="present"', $html);
        $this->assertStringContainsString('data-student-id="' . $this->students[2]->id . '" data-status="half_day"', $html);
        $this->assertStringContainsString('data-student-id="' . $this->students[3]->id . '" data-status="present"', $html);
        $this->assertStringContainsString('data-student-id="' . $this->students[4]->id . '" data-status="absent"', $html);
        $this->assertStringContainsString('data-student-id="' . $this->students[5]->id . '" data-status="leave"', $html);
        $this->assertStringContainsString('data-student-id="' . $this->students[6]->id . '" data-status="present"', $html);
        $this->assertStringContainsString('data-student-id="' . $this->students[7]->id . '" data-status="present"', $html);
        $this->assertStringContainsString('data-student-id="' . $this->students[8]->id . '" data-status="absent"', $html);
        $this->assertStringContainsString('data-student-id="' . $this->students[9]->id . '" data-status="present"', $html);
        $this->assertStringContainsString('data-student-id="' . $this->students[10]->id . '" data-status="present"', $html);

        // Check that badge texts are present
        $this->assertStringContainsString('badge-status present', $html);
        $this->assertStringContainsString('badge-status half_day', $html);
        $this->assertStringContainsString('badge-status absent', $html);
        $this->assertStringContainsString('badge-status leave', $html);
        $this->assertStringNotContainsString('badge-status not_marked', $html);
    }
}
