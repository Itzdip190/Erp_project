<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\ClassWiseFee;
use App\Models\FeeCategory;
use App\Models\FeeComponent;
use App\Models\FeeSchedule;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentCategory;
use App\Models\StudentFee;
use App\Models\StudentSession;
use App\Models\User;
use App\Models\Scopes\SchoolScope;
use App\Services\FeeHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FeeScheduleAutoAssignmentTest extends TestCase
{
    use RefreshDatabase;

    protected School $school;
    protected User $admin;
    protected AcademicSession $session2026;
    protected AcademicSession $session2027;
    protected SchoolClass $class5;
    protected SchoolClass $class6;
    protected SchoolClass $class7;
    protected Section $section5A;
    protected Section $section5B;
    protected Section $section5C;
    protected Section $section6A;
    protected Section $section6B;
    protected Section $section7A;
    protected FeeSchedule $schedule1_2026;
    protected FeeSchedule $schedule2_2026;
    protected FeeSchedule $schedule2027_Class5B;
    protected FeeComponent $componentTuition;
    protected StudentCategory $studentCategory;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'superadmin']);
        Role::firstOrCreate(['name' => 'school_admin']);

        $this->school = School::create([
            'name'          => 'Vedant Public School',
            'code'          => 'VPS',
            'custom_domain' => 'vps.educore.test',
            'status'        => 'active',
        ]);

        $this->admin = User::factory()->create([
            'school_id' => $this->school->id,
            'role'      => 'school_admin',
            'email'     => 'admin@vps.educore.test',
        ]);
        $this->admin->assignRole('school_admin');

        $this->session2026 = AcademicSession::create([
            'school_id'  => $this->school->id,
            'name'       => '2026-2027',
            'start_date' => '2026-04-01',
            'end_date'   => '2027-03-31',
            'is_current' => true,
        ]);

        $this->session2027 = AcademicSession::create([
            'school_id'  => $this->school->id,
            'name'       => '2027-2028',
            'start_date' => '2027-04-01',
            'end_date'   => '2028-03-31',
            'is_current' => false,
        ]);

        $this->class5 = SchoolClass::withoutGlobalScope(SchoolScope::class)->create([
            'school_id'    => $this->school->id,
            'name'         => 'Class-5',
            'numeric_name' => 5,
        ]);

        $this->class6 = SchoolClass::withoutGlobalScope(SchoolScope::class)->create([
            'school_id'    => $this->school->id,
            'name'         => 'Class-6',
            'numeric_name' => 6,
        ]);

        $this->class7 = SchoolClass::withoutGlobalScope(SchoolScope::class)->create([
            'school_id'    => $this->school->id,
            'name'         => 'Class-7',
            'numeric_name' => 7,
        ]);

        $this->section5A = Section::withoutGlobalScope(SchoolScope::class)->create([
            'school_id' => $this->school->id,
            'class_id'  => $this->class5->id,
            'name'      => 'A',
        ]);

        $this->section5B = Section::withoutGlobalScope(SchoolScope::class)->create([
            'school_id' => $this->school->id,
            'class_id'  => $this->class5->id,
            'name'      => 'B',
        ]);

        $this->section5C = Section::withoutGlobalScope(SchoolScope::class)->create([
            'school_id' => $this->school->id,
            'class_id'  => $this->class5->id,
            'name'      => 'C',
        ]);

        $this->section6A = Section::withoutGlobalScope(SchoolScope::class)->create([
            'school_id' => $this->school->id,
            'class_id'  => $this->class6->id,
            'name'      => 'A',
        ]);

        $this->section6B = Section::withoutGlobalScope(SchoolScope::class)->create([
            'school_id' => $this->school->id,
            'class_id'  => $this->class6->id,
            'name'      => 'B',
        ]);

        $this->section7A = Section::withoutGlobalScope(SchoolScope::class)->create([
            'school_id' => $this->school->id,
            'class_id'  => $this->class7->id,
            'name'      => 'A',
        ]);

        $this->studentCategory = StudentCategory::firstOrCreate(['school_id' => $this->school->id, 'name' => 'Day boarding']);

        // Schedule-1 in 2026-2027: Class-5 B and Class-6 A
        $this->schedule1_2026 = FeeSchedule::create([
            'school_id'           => $this->school->id,
            'academic_session_id' => $this->session2026->id,
            'name'                => 'Schedule-1',
            'classes'             => 'Class-5, Class-6',
            'sections'            => 'Class-5-B, Class-6-A',
            'installment_type'    => 'custom',
            'start_date'          => '2026-04-01',
            'end_date'            => '2027-03-31',
            'installments'        => [
                ['installment_no' => 1, 'name' => 'Term 1', 'start_date' => '2026-04-10', 'due_date' => '2026-04-30', 'amount' => 5000],
            ],
        ]);

        // Schedule-2 in 2026-2027: Class-6 B
        $this->schedule2_2026 = FeeSchedule::create([
            'school_id'           => $this->school->id,
            'academic_session_id' => $this->session2026->id,
            'name'                => 'Schedule-2',
            'classes'             => 'Class-6',
            'sections'            => 'Class-6-B',
            'installment_type'    => 'custom',
            'start_date'          => '2026-04-01',
            'end_date'            => '2027-03-31',
            'installments'        => [
                ['installment_no' => 1, 'name' => 'Term 1', 'start_date' => '2026-04-10', 'due_date' => '2026-04-30', 'amount' => 7000],
            ],
        ]);

        // Schedule-Next in 2027-2028: Class-5 B has a DIFFERENT schedule in 2027-2028
        $this->schedule2027_Class5B = FeeSchedule::create([
            'school_id'           => $this->school->id,
            'academic_session_id' => $this->session2027->id,
            'name'                => 'Schedule-2027-Special',
            'classes'             => 'Class-5',
            'sections'            => 'Class-5-B',
            'installment_type'    => 'custom',
            'start_date'          => '2027-04-01',
            'end_date'            => '2028-03-31',
            'installments'        => [
                ['installment_no' => 1, 'name' => 'Term 1', 'start_date' => '2027-04-10', 'due_date' => '2027-04-30', 'amount' => 8000],
            ],
        ]);

        // Fee component and ClassWiseFee for Class-5 B
        $this->componentTuition = FeeComponent::create([
            'school_id'           => $this->school->id,
            'academic_session_id' => $this->session2026->id,
            'fee_schedule_id'     => $this->schedule1_2026->id,
            'component_name'      => 'Tuition Fee',
            'head_name'           => 'School Fee',
        ]);

        ClassWiseFee::create([
            'school_id'           => $this->school->id,
            'academic_session_id' => $this->session2026->id,
            'class_id'            => $this->class5->id,
            'section_id'          => $this->section5B->id,
            'fee_schedule_id'     => $this->schedule1_2026->id,
            'fee_component_id'    => $this->componentTuition->id,
            'student_category_id' => $this->studentCategory->id,
            'is_active'           => true,
            'installments'        => [
                ['installment_no' => 1, 'amount' => 5000, 'date_range' => '10/04/2026 - 30/04/2026'],
            ],
        ]);
    }

    protected function makeStudent(array $overrides = []): Student
    {
        static $seq = 100;
        $seq++;

        $defaults = [
            'school_id'             => $this->school->id,
            'admission_number'      => 'VPS' . $seq,
            'admission_sequence'    => $seq,
            'admission_year'        => 2026,
            'admission_date'        => '2026-04-01',
            'date_of_birth'         => '2015-05-15',
            'gender'                => 'male',
            'first_name'            => 'Student',
            'last_name'             => (string)$seq,
            'father_name'           => 'Father ' . $seq,
            'phone'                 => '9876543210',
            'guardian_name'         => 'Father ' . $seq,
            'guardian_phone'        => '9876543210',
            'guardian_relationship' => 'father',
            'address'               => '123 Test Street',
            'city'                  => 'Mumbai',
            'state'                 => 'Maharashtra',
            'pincode'               => '400001',
            'class_id'              => $this->class5->id,
            'section_id'            => $this->section5B->id,
            'academic_session_id'   => $this->session2026->id,
            'roll_number'           => (string)$seq,
            'is_active'             => true,
            'is_alumni'             => false,
        ];

        $student = Student::withoutGlobalScope(SchoolScope::class)->create(array_merge($defaults, $overrides));

        StudentSession::create([
            'school_id'           => $this->school->id,
            'student_id'          => $student->id,
            'academic_session_id' => $student->academic_session_id,
            'class_id'            => $student->class_id,
            'section_id'          => $student->section_id,
            'roll_number'         => $student->roll_number,
            'is_promoted'         => false,
        ]);

        return $student;
    }

    /**
     * TEST 1: Select an academic year. Verify that existing students automatically show the Fee Schedule configured for their Class + Section.
     */
    public function test_1_existing_students_auto_show_configured_fee_schedule_in_mapper(): void
    {
        $student = $this->makeStudent([
            'first_name' => 'Pranav',
            'last_name'  => 'Tripathi',
            'class_id'   => $this->class5->id,
            'section_id' => $this->section5B->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('school.fees.schedule-mapper', [
            'academic_year' => '2026-2027',
            'class_id'      => $this->class5->id,
            'section_id'    => 'B',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Pranav Tripathi');
        // Dropdown option for Schedule-1 must be selected
        $response->assertSee('name="student_schedules[' . $student->id . ']"', false);
        $response->assertSee('<option value="' . $this->schedule1_2026->id . '" selected>' . $this->schedule1_2026->name . '</option>', false);
    }

    /**
     * TEST 2: Select another Class + Section that has a different Fee Schedule. Verify the correct schedule appears.
     */
    public function test_2_different_class_section_auto_shows_respective_schedule(): void
    {
        $student6B = $this->makeStudent([
            'first_name' => 'Aditya',
            'last_name'  => 'Yadav',
            'class_id'   => $this->class6->id,
            'section_id' => $this->section6B->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('school.fees.schedule-mapper', [
            'academic_year' => '2026-2027',
            'class_id'      => $this->class6->id,
            'section_id'    => 'B',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Aditya Yadav');
        // Dropdown option for Schedule-2 must be selected for Class-6 B
        $response->assertSee('name="student_schedules[' . $student6B->id . ']"', false);
        $response->assertSee('<option value="' . $this->schedule2_2026->id . '" selected>' . $this->schedule2_2026->name . '</option>', false);
    }

    /**
     * TEST 3: Select a Class + Section that has NO Fee Schedule. Verify that no schedule is automatically assigned.
     */
    public function test_3_class_section_without_schedule_shows_unselected(): void
    {
        $student5C = $this->makeStudent([
            'first_name' => 'Ananya',
            'last_name'  => 'Yadav',
            'class_id'   => $this->class5->id,
            'section_id' => $this->section5C->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('school.fees.schedule-mapper', [
            'academic_year' => '2026-2027',
            'class_id'      => $this->class5->id,
            'section_id'    => 'C',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Ananya Yadav');
        $response->assertSee('name="student_schedules[' . $student5C->id . ']"', false);
        // No schedule option should be selected for this student
        $response->assertDontSee('<option value="' . $this->schedule1_2026->id . '" selected>' . $this->schedule1_2026->name . '</option>', false);
        $response->assertDontSee('<option value="' . $this->schedule2_2026->id . '" selected>' . $this->schedule2_2026->name . '</option>', false);
    }

    /**
     * TEST 4: Create a new student in a Class + Section that already has a Fee Schedule. Verify auto-assignment.
     */
    public function test_4_new_student_in_configured_class_section_automatically_receives_schedule(): void
    {
        $student = $this->makeStudent([
            'first_name' => 'Shreya',
            'last_name'  => 'Yadav',
            'class_id'   => $this->class5->id,
            'section_id' => $this->section5B->id,
        ]);

        // Student model event / FeeManagementController::syncStudentClassFees must have set fee_schedule_id
        $this->assertEquals($this->schedule1_2026->id, $student->fresh()->fee_schedule_id);

        // Student fee records should be generated
        $this->assertDatabaseHas('student_fees', [
            'school_id'        => $this->school->id,
            'student_id'       => $student->id,
            'fee_schedule_id'  => $this->schedule1_2026->id,
            'fee_component_id' => $this->componentTuition->id,
            'amount'           => 5000,
        ]);
    }

    /**
     * TEST 5: Create a new student in a Class + Section that has no Fee Schedule. Verify no schedule is assigned.
     */
    public function test_5_new_student_in_unconfigured_class_section_has_no_schedule(): void
    {
        $student = $this->makeStudent([
            'first_name' => 'Rahul',
            'last_name'  => 'Sharma',
            'class_id'   => $this->class5->id,
            'section_id' => $this->section5C->id,
        ]);

        $this->assertNull($student->fresh()->fee_schedule_id);
        $this->assertEquals(0, StudentFee::withoutGlobalScopes()->where('student_id', $student->id)->count());
    }

    /**
     * TEST 6: Change a student's Class/Section. Verify that the applicable schedule follows the new Class + Section.
     */
    public function test_6_student_class_section_change_automatically_follows_new_configuration(): void
    {
        // 1. Student initially in Class-5 B -> receives Schedule-1
        $student = $this->makeStudent([
            'first_name' => 'Kunal',
            'last_name'  => 'Verma',
            'class_id'   => $this->class5->id,
            'section_id' => $this->section5B->id,
        ]);
        $this->assertEquals($this->schedule1_2026->id, $student->fresh()->fee_schedule_id);

        // 2. Transfer student to Class-6 B (which is configured for Schedule-2)
        $student->update([
            'class_id'   => $this->class6->id,
            'section_id' => $this->section6B->id,
        ]);

        $this->assertEquals($this->schedule2_2026->id, $student->fresh()->fee_schedule_id);

        // 3. Transfer student to Class-7 A (which has NO schedule configured)
        $student->update([
            'class_id'   => $this->class7->id,
            'section_id' => $this->section7A->id,
        ]);

        $this->assertNull($student->fresh()->fee_schedule_id);
    }

    /**
     * TEST 7: Switch between academic years. Verify schedules are determined only from the selected academic year's Fee Structure.
     */
    public function test_7_academic_year_isolation_determines_schedules_strictly_per_session(): void
    {
        // Student in 2026-2027 Class-5 B -> Schedule-1
        $student2026 = $this->makeStudent([
            'first_name'          => 'Aman',
            'academic_session_id' => $this->session2026->id,
            'class_id'            => $this->class5->id,
            'section_id'          => $this->section5B->id,
        ]);

        // Student in 2027-2028 Class-5 B -> Schedule-2027-Special
        $student2027 = $this->makeStudent([
            'first_name'          => 'Rohit',
            'academic_session_id' => $this->session2027->id,
            'class_id'            => $this->class5->id,
            'section_id'          => $this->section5B->id,
        ]);

        $this->assertEquals($this->schedule1_2026->id, $student2026->fresh()->fee_schedule_id);
        $this->assertEquals($this->schedule2027_Class5B->id, $student2027->fresh()->fee_schedule_id);

        // Verify Mapper for 2026-2027
        $resp2026 = $this->actingAs($this->admin)->get(route('school.fees.schedule-mapper', [
            'academic_year' => '2026-2027',
            'class_id'      => $this->class5->id,
            'section_id'    => 'B',
        ]));
        $resp2026->assertSee('<option value="' . $this->schedule1_2026->id . '" selected>' . $this->schedule1_2026->name . '</option>', false);

        // Verify Mapper for 2027-2028
        $resp2027 = $this->actingAs($this->admin)->get(route('school.fees.schedule-mapper', [
            'academic_year' => '2027-2028',
            'class_id'      => $this->class5->id,
            'section_id'    => 'B',
        ]));
        $resp2027->assertSee('<option value="' . $this->schedule2027_Class5B->id . '" selected>' . $this->schedule2027_Class5B->name . '</option>', false);
    }

    /**
     * TEST 8: Verify that existing manually saved mappings are not broken.
     */
    public function test_8_manually_saved_mappings_remain_respected(): void
    {
        // Student in Class-6 A (which matches Schedule-1)
        $student = $this->makeStudent([
            'first_name' => 'Manish',
            'class_id'   => $this->class6->id,
            'section_id' => $this->section6A->id,
        ]);

        // Manually post to save Schedule-1
        $postResponse = $this->actingAs($this->admin)->post(route('school.fees.schedule-mapper'), [
            'student_schedules' => [
                $student->id => $this->schedule1_2026->id,
            ],
        ]);
        $postResponse->assertSessionHas('success');

        $this->assertEquals($this->schedule1_2026->id, $student->fresh()->fee_schedule_id);
    }

    /**
     * TEST 9: Verify that no duplicate Fee Schedule or student mapping records are created.
     */
    public function test_9_no_duplicate_fee_schedules_or_fees_created(): void
    {
        $schedCountBefore = FeeSchedule::where('school_id', $this->school->id)->count();

        $student = $this->makeStudent([
            'first_name' => 'Deepak',
            'class_id'   => $this->class5->id,
            'section_id' => $this->section5B->id,
        ]);

        // Trigger updates multiple times
        $student->touch();
        \App\Http\Controllers\School\FeeManagementController::syncStudentClassFees($student);

        $schedCountAfter = FeeSchedule::where('school_id', $this->school->id)->count();
        $this->assertEquals($schedCountBefore, $schedCountAfter);

        // Only 1 student fee record should exist for Tuition Fee
        $feesCount = StudentFee::withoutGlobalScopes()
            ->where('student_id', $student->id)
            ->where('fee_schedule_id', $this->schedule1_2026->id)
            ->count();
        $this->assertEquals(1, $feesCount);
    }

    /**
     * TEST 10: Refresh the page and verify that the correct schedule remains selected.
     */
    public function test_10_refresh_page_preserves_correct_schedule_selection(): void
    {
        $student = $this->makeStudent([
            'first_name' => 'Pooja',
            'class_id'   => $this->class5->id,
            'section_id' => $this->section5B->id,
        ]);

        // 1st request
        $resp1 = $this->actingAs($this->admin)->get(route('school.fees.schedule-mapper', [
            'academic_year' => '2026-2027',
            'class_id'      => $this->class5->id,
            'section_id'    => 'B',
        ]));
        $resp1->assertStatus(200);
        $resp1->assertSee('<option value="' . $this->schedule1_2026->id . '" selected>' . $this->schedule1_2026->name . '</option>', false);

        // 2nd request (page refresh)
        $resp2 = $this->actingAs($this->admin)->get(route('school.fees.schedule-mapper', [
            'academic_year' => '2026-2027',
            'class_id'      => $this->class5->id,
            'section_id'    => 'B',
        ]));
        $resp2->assertStatus(200);
        $resp2->assertSee('<option value="' . $this->schedule1_2026->id . '" selected>' . $this->schedule1_2026->name . '</option>', false);
    }
}
