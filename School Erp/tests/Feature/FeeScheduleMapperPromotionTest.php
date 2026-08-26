<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\ClassWiseFee;
use App\Models\FeeCategory;
use App\Models\FeeComponent;
use App\Models\FeeReceipt;
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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FeeScheduleMapperPromotionTest extends TestCase
{
    use RefreshDatabase;

    protected School $school;
    protected User $admin;
    protected AcademicSession $session2025;
    protected AcademicSession $session2026;
    protected SchoolClass $classNursery;
    protected SchoolClass $classUKG;
    protected Section $sectionNurseryA;
    protected Section $sectionUKGA;
    protected FeeCategory $category;
    protected FeeSchedule $scheduleNursery2025;
    protected FeeSchedule $scheduleUKG2026;
    protected FeeComponent $componentNursery;
    protected FeeComponent $componentUKG;
    protected ClassWiseFee $classWiseNursery;
    protected ClassWiseFee $classWiseUKG;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'superadmin']);
        Role::firstOrCreate(['name' => 'school_admin']);

        $this->school = School::create([
            'name'          => 'EduCore Test School',
            'code'          => 'ETS',
            'custom_domain' => 'educore.erp.test',
            'status'        => 'active',
        ]);

        $this->admin = User::factory()->create([
            'school_id' => $this->school->id,
            'role'      => 'school_admin',
            'email'     => 'admin@educore.erp.test',
        ]);
        $this->admin->assignRole('school_admin');

        $this->session2025 = AcademicSession::create([
            'school_id'  => $this->school->id,
            'name'       => '2025-2026',
            'start_date' => '2025-04-01',
            'end_date'   => '2026-03-31',
            'is_current' => false,
        ]);

        $this->session2026 = AcademicSession::create([
            'school_id'  => $this->school->id,
            'name'       => '2026-2027',
            'start_date' => '2026-04-01',
            'end_date'   => '2027-03-31',
            'is_current' => true,
        ]);

        $this->classNursery = SchoolClass::withoutGlobalScope(SchoolScope::class)->create([
            'school_id'    => $this->school->id,
            'name'         => 'Nursery',
            'numeric_name' => 0,
        ]);

        $this->classUKG = SchoolClass::withoutGlobalScope(SchoolScope::class)->create([
            'school_id'    => $this->school->id,
            'name'         => 'UKG',
            'numeric_name' => 1,
        ]);

        $this->sectionNurseryA = Section::withoutGlobalScope(SchoolScope::class)->create([
            'school_id' => $this->school->id,
            'class_id'  => $this->classNursery->id,
            'name'      => 'A',
        ]);

        $this->sectionUKGA = Section::withoutGlobalScope(SchoolScope::class)->create([
            'school_id' => $this->school->id,
            'class_id'  => $this->classUKG->id,
            'name'      => 'A',
        ]);

        $studentCategory = StudentCategory::firstOrCreate(['school_id' => $this->school->id, 'name' => 'Day boarding']);

        $this->category = FeeCategory::create([
            'school_id' => $this->school->id,
            'name'      => 'Tuition Fee',
        ]);

        // Fee Schedules
        $this->scheduleNursery2025 = FeeSchedule::create([
            'school_id'           => $this->school->id,
            'academic_session_id' => $this->session2025->id,
            'name'                => 'Nursery 2025 Fee',
            'classes'             => 'Nursery',
            'sections'            => 'Nursery-A',
            'installment_type'    => 'custom',
            'start_date'          => '2025-04-01',
            'end_date'            => '2026-03-31',
            'installments'        => [
                ['installment_no' => 1, 'name' => 'Installment 1', 'start_date' => '2025-04-10', 'due_date' => '2025-04-30', 'amount' => 5000],
            ],
        ]);

        $this->scheduleUKG2026 = FeeSchedule::create([
            'school_id'           => $this->school->id,
            'academic_session_id' => $this->session2026->id,
            'name'                => 'UKG 2026 Fee',
            'classes'             => 'UKG',
            'sections'            => 'UKG-A',
            'installment_type'    => 'custom',
            'start_date'          => '2026-04-01',
            'end_date'            => '2027-03-31',
            'installments'        => [
                ['installment_no' => 1, 'name' => 'Installment 1', 'start_date' => '2026-04-10', 'due_date' => '2026-04-30', 'amount' => 6000],
            ],
        ]);

        $this->componentNursery = FeeComponent::create([
            'school_id'           => $this->school->id,
            'academic_session_id' => $this->session2025->id,
            'fee_schedule_id'     => $this->scheduleNursery2025->id,
            'component_name'      => 'Tuition Fee',
            'head_name'           => 'Tuition Fee',
        ]);

        $this->componentUKG = FeeComponent::create([
            'school_id'           => $this->school->id,
            'academic_session_id' => $this->session2026->id,
            'fee_schedule_id'     => $this->scheduleUKG2026->id,
            'component_name'      => 'Tuition Fee',
            'head_name'           => 'Tuition Fee',
        ]);

        $this->classWiseNursery = ClassWiseFee::create([
            'school_id'           => $this->school->id,
            'academic_session_id' => $this->session2025->id,
            'class_id'            => $this->classNursery->id,
            'section_id'          => $this->sectionNurseryA->id,
            'fee_schedule_id'     => $this->scheduleNursery2025->id,
            'fee_component_id'    => $this->componentNursery->id,
            'student_category_id' => $studentCategory->id,
            'is_active'           => true,
            'installments'        => [
                ['installment_no' => 1, 'amount' => 5000, 'date_range' => '10/04/2025 - 30/04/2025'],
            ],
        ]);

        $this->classWiseUKG = ClassWiseFee::create([
            'school_id'           => $this->school->id,
            'academic_session_id' => $this->session2026->id,
            'class_id'            => $this->classUKG->id,
            'section_id'          => $this->sectionUKGA->id,
            'fee_schedule_id'     => $this->scheduleUKG2026->id,
            'fee_component_id'    => $this->componentUKG->id,
            'student_category_id' => $studentCategory->id,
            'is_active'           => true,
            'installments'        => [
                ['installment_no' => 1, 'amount' => 6000, 'date_range' => '10/04/2026 - 30/04/2026'],
            ],
        ]);
    }

    protected function createTestStudent(array $overrides = []): Student
    {
        static $seq = 500;
        $seq++;

        $defaults = [
            'school_id'             => $this->school->id,
            'admission_number'      => 'ADM' . $seq,
            'admission_sequence'    => $seq,
            'admission_year'        => 2025,
            'admission_date'        => '2025-04-01',
            'date_of_birth'         => '2020-05-15',
            'gender'                => 'female',
            'first_name'            => 'Test',
            'last_name'             => 'Student' . $seq,
            'father_name'           => 'Father ' . $seq,
            'phone'                 => '9876543210',
            'guardian_name'         => 'Father ' . $seq,
            'guardian_phone'        => '9876543210',
            'guardian_relationship' => 'father',
            'address'               => '123 Test Street',
            'city'                  => 'Mumbai',
            'state'                 => 'Maharashtra',
            'pincode'               => '400001',
            'class_id'              => $this->classNursery->id,
            'section_id'            => $this->sectionNurseryA->id,
            'academic_session_id'   => $this->session2025->id,
            'roll_number'           => '01',
            'is_active'             => true,
            'is_alumni'             => false,
        ];

        return Student::withoutGlobalScope(SchoolScope::class)->create(array_merge($defaults, $overrides));
    }

    /**
     * SCENARIO 1: Promoted students appear correctly in Fee Schedule Mapper for both past and promoted sessions.
     */
    public function test_promoted_students_appear_in_fee_schedule_mapper_for_selected_academic_year(): void
    {
        // Create student in 2025-2026 Nursery
        $student = $this->createTestStudent([
            'first_name'          => 'Barkha',
            'last_name'           => 'Anne',
            'admission_number'    => 'ADM10840',
            'academic_session_id' => $this->session2025->id,
            'class_id'            => $this->classNursery->id,
            'section_id'          => $this->sectionNurseryA->id,
            'fee_schedule_id'     => $this->scheduleNursery2025->id,
        ]);

        StudentSession::create([
            'school_id'           => $this->school->id,
            'student_id'          => $student->id,
            'academic_session_id' => $this->session2025->id,
            'class_id'            => $this->classNursery->id,
            'section_id'          => $this->sectionNurseryA->id,
            'is_promoted'         => false,
        ]);

        // Promote to 2026-2027 UKG
        $promoteResp = $this->actingAs($this->admin)->post(route('school.students.promote'), [
            'from_session_id' => $this->session2025->id,
            'from_class_id'   => $this->classNursery->id,
            'to_session_id'   => $this->session2026->id,
            'to_class_id'     => $this->classUKG->id,
            'to_section_id'   => $this->sectionUKGA->id,
            'student_ids'     => [$student->id],
        ]);
        $promoteResp->assertSessionHas('success');

        // Check 1: Fee Schedule Mapper for 2026-2027 UKG Section A displays the promoted student
        $response2026 = $this->actingAs($this->admin)->get(route('school.fees.schedule-mapper', [
            'academic_year' => '2026-2027',
            'class_id'      => $this->classUKG->id,
            'section_id'    => 'A',
        ]));
        $response2026->assertStatus(200);
        $response2026->assertSee('Barkha Anne');
        $response2026->assertSee('ADM10840');

        // Check 2: Fee Schedule Mapper for 2025-2026 Nursery Section A also displays the student's historical enrollment
        $response2025 = $this->actingAs($this->admin)->get(route('school.fees.schedule-mapper', [
            'academic_year' => '2025-2026',
            'class_id'      => $this->classNursery->id,
            'section_id'    => 'A',
        ]));
        $response2025->assertStatus(200);
        $response2025->assertSee('Barkha Anne');
    }

    /**
     * SCENARIO 2 & 3: Promoted student who paid fees in previous year can be assigned a new Fee Schedule in the new year.
     */
    public function test_promoted_student_with_previous_year_paid_fees_can_receive_new_fee_schedule(): void
    {
        // Create student in 2025-2026 Nursery with paid fee and receipt
        $student = $this->createTestStudent([
            'first_name'          => 'Charles',
            'last_name'           => 'Lala',
            'admission_number'    => 'ADM10414',
            'academic_session_id' => $this->session2025->id,
            'class_id'            => $this->classNursery->id,
            'section_id'          => $this->sectionNurseryA->id,
            'fee_schedule_id'     => $this->scheduleNursery2025->id,
        ]);

        StudentSession::create([
            'school_id'           => $this->school->id,
            'student_id'          => $student->id,
            'academic_session_id' => $this->session2025->id,
            'class_id'            => $this->classNursery->id,
            'section_id'          => $this->sectionNurseryA->id,
            'is_promoted'         => true,
        ]);

        // Add 2025-2026 paid fee record and receipt
        $fee2025 = StudentFee::withoutGlobalScopes()->create([
            'school_id'        => $this->school->id,
            'student_id'       => $student->id,
            'fee_category_id'  => $this->category->id,
            'fee_schedule_id'  => $this->scheduleNursery2025->id,
            'fee_component_id' => $this->componentNursery->id,
            'installment_no'   => 1,
            'amount'           => 5000,
            'paid_amount'      => 5000,
            'due_date'         => '2025-04-30',
            'status'           => 'paid',
            'invoice_no'       => 'INV-1',
            'invoice_status'   => 'active',
        ]);

        FeeReceipt::create([
            'school_id'      => $this->school->id,
            'student_id'     => $student->id,
            'receipt_number' => 'REC-1001',
            'amount_paid'    => 5000,
            'payment_mode'   => 'Cash',
            'payment_date'   => '2025-04-15',
            'status'         => 'active',
        ]);

        // Student promoted to 2026-2027 UKG
        $student->update([
            'academic_session_id' => $this->session2026->id,
            'class_id'            => $this->classUKG->id,
            'section_id'          => $this->sectionUKGA->id,
        ]);

        StudentSession::create([
            'school_id'           => $this->school->id,
            'student_id'          => $student->id,
            'academic_session_id' => $this->session2026->id,
            'class_id'            => $this->classUKG->id,
            'section_id'          => $this->sectionUKGA->id,
            'is_promoted'         => false,
        ]);

        // Assign UKG 2026 Fee Schedule using Fee Schedule Mapper POST
        $postResponse = $this->actingAs($this->admin)->post(route('school.fees.schedule-mapper'), [
            'student_schedules' => [
                $student->id => $this->scheduleUKG2026->id,
            ],
        ]);

        $postResponse->assertSessionHas('success');

        $student->refresh();
        $this->assertEquals($this->scheduleUKG2026->id, $student->fee_schedule_id);

        // Verify that 2026-2027 student fee is created
        $fee2026 = StudentFee::withoutGlobalScopes()
            ->where('school_id', $this->school->id)
            ->where('student_id', $student->id)
            ->where('fee_schedule_id', $this->scheduleUKG2026->id)
            ->first();

        $this->assertNotNull($fee2026);
        $this->assertEquals(6000, $fee2026->amount);
        $this->assertEquals(0, $fee2026->paid_amount);

        // Verify that 2025-2026 paid fee and receipt remain completely intact
        $this->assertDatabaseHas('student_fees', [
            'id'          => $fee2025->id,
            'amount'      => 5000,
            'paid_amount' => 5000,
            'status'      => 'paid',
        ]);

        $this->assertDatabaseHas('fee_receipts', [
            'receipt_number' => 'REC-1001',
            'student_id'     => $student->id,
        ]);
    }

    /**
     * SCENARIO 4 & 5: Already mapped students are not duplicated, existing fees remain intact.
     */
    public function test_mapping_does_not_duplicate_fees_or_create_unwanted_records(): void
    {
        $student = $this->createTestStudent([
            'first_name'          => 'Jeet',
            'last_name'           => 'Handa',
            'admission_number'    => 'ADM10018',
            'academic_session_id' => $this->session2026->id,
            'class_id'            => $this->classUKG->id,
            'section_id'          => $this->sectionUKGA->id,
            'fee_schedule_id'     => $this->scheduleUKG2026->id,
        ]);

        StudentSession::create([
            'school_id'           => $this->school->id,
            'student_id'          => $student->id,
            'academic_session_id' => $this->session2026->id,
            'class_id'            => $this->classUKG->id,
            'section_id'          => $this->sectionUKGA->id,
            'is_promoted'         => false,
        ]);

        // Map schedule first time
        $this->actingAs($this->admin)->post(route('school.fees.schedule-mapper'), [
            'student_schedules' => [
                $student->id => $this->scheduleUKG2026->id,
            ],
        ]);

        $feeCountFirst = StudentFee::withoutGlobalScopes()
            ->where('student_id', $student->id)
            ->where('fee_schedule_id', $this->scheduleUKG2026->id)
            ->count();

        $this->assertEquals(1, $feeCountFirst);

        // Map schedule second time (saving again)
        $this->actingAs($this->admin)->post(route('school.fees.schedule-mapper'), [
            'student_schedules' => [
                $student->id => $this->scheduleUKG2026->id,
            ],
        ]);

        $feeCountSecond = StudentFee::withoutGlobalScopes()
            ->where('student_id', $student->id)
            ->where('fee_schedule_id', $this->scheduleUKG2026->id)
            ->count();

        $this->assertEquals(1, $feeCountSecond);
    }

    /**
     * SCENARIO 6: No unnecessary Academic Year records are created when using Fee Schedule Mapper.
     */
    public function test_fee_schedule_mapper_never_creates_unexpected_academic_year_entries(): void
    {
        $sessionCountBefore = AcademicSession::where('school_id', $this->school->id)->count();
        $this->assertEquals(2, $sessionCountBefore); // 2025-2026 and 2026-2027

        // Access Fee Schedule Mapper GET
        $this->actingAs($this->admin)->get(route('school.fees.schedule-mapper', [
            'academic_year' => '2026-2027',
            'class_id'      => $this->classUKG->id,
            'section_id'    => 'A',
        ]));

        $sessionCountAfterGet = AcademicSession::where('school_id', $this->school->id)->count();
        $this->assertEquals(2, $sessionCountAfterGet);

        // Access Fee Schedule Mapper POST
        $student = $this->createTestStudent([
            'first_name'          => 'Ekaraj',
            'last_name'           => 'Dara',
            'admission_number'    => 'ADM10272',
            'academic_session_id' => $this->session2026->id,
            'class_id'            => $this->classUKG->id,
            'section_id'          => $this->sectionUKGA->id,
        ]);

        StudentSession::create([
            'school_id'           => $this->school->id,
            'student_id'          => $student->id,
            'academic_session_id' => $this->session2026->id,
            'class_id'            => $this->classUKG->id,
            'section_id'          => $this->sectionUKGA->id,
            'is_promoted'         => false,
        ]);

        $this->actingAs($this->admin)->post(route('school.fees.schedule-mapper'), [
            'student_schedules' => [
                $student->id => $this->scheduleUKG2026->id,
            ],
        ]);

        $sessionCountAfterPost = AcademicSession::where('school_id', $this->school->id)->count();
        $this->assertEquals(2, $sessionCountAfterPost);
    }

    /**
     * SCENARIO 7: Newly admitted students and promoted students behave identically in Fee Schedule Mapper.
     */
    public function test_newly_admitted_and_promoted_students_behave_identically(): void
    {
        // 1. Newly admitted student directly in 2026-2027 UKG
        $newStudent = $this->createTestStudent([
            'first_name'          => 'Harita',
            'last_name'           => 'Dutta',
            'admission_number'    => 'ADM10227',
            'academic_session_id' => $this->session2026->id,
            'class_id'            => $this->classUKG->id,
            'section_id'          => $this->sectionUKGA->id,
        ]);
        StudentSession::create([
            'school_id'           => $this->school->id,
            'student_id'          => $newStudent->id,
            'academic_session_id' => $this->session2026->id,
            'class_id'            => $this->classUKG->id,
            'section_id'          => $this->sectionUKGA->id,
            'is_promoted'         => false,
        ]);

        // 2. Promoted student from 2025-2026 Nursery to 2026-2027 UKG
        $promotedStudent = $this->createTestStudent([
            'first_name'          => 'Ridhi',
            'last_name'           => 'Randhawa',
            'admission_number'    => 'ADM10283',
            'academic_session_id' => $this->session2026->id,
            'class_id'            => $this->classUKG->id,
            'section_id'          => $this->sectionUKGA->id,
        ]);
        StudentSession::create([
            'school_id'           => $this->school->id,
            'student_id'          => $promotedStudent->id,
            'academic_session_id' => $this->session2025->id,
            'class_id'            => $this->classNursery->id,
            'section_id'          => $this->sectionNurseryA->id,
            'is_promoted'         => true,
        ]);
        StudentSession::create([
            'school_id'           => $this->school->id,
            'student_id'          => $promotedStudent->id,
            'academic_session_id' => $this->session2026->id,
            'class_id'            => $this->classUKG->id,
            'section_id'          => $this->sectionUKGA->id,
            'is_promoted'         => false,
        ]);

        // View in Fee Schedule Mapper
        $response = $this->actingAs($this->admin)->get(route('school.fees.schedule-mapper', [
            'academic_year' => '2026-2027',
            'class_id'      => $this->classUKG->id,
            'section_id'    => 'A',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Harita Dutta');
        $response->assertSee('Ridhi Randhawa');

        // Assign schedules to both simultaneously
        $saveResponse = $this->actingAs($this->admin)->post(route('school.fees.schedule-mapper'), [
            'student_schedules' => [
                $newStudent->id      => $this->scheduleUKG2026->id,
                $promotedStudent->id => $this->scheduleUKG2026->id,
            ],
        ]);

        $saveResponse->assertSessionHas('success');

        $newStudent->refresh();
        $promotedStudent->refresh();

        $this->assertEquals($this->scheduleUKG2026->id, $newStudent->fee_schedule_id);
        $this->assertEquals($this->scheduleUKG2026->id, $promotedStudent->fee_schedule_id);

        $newFee = StudentFee::withoutGlobalScopes()->where('student_id', $newStudent->id)->first();
        $promotedFee = StudentFee::withoutGlobalScopes()->where('student_id', $promotedStudent->id)->first();

        $this->assertNotNull($newFee);
        $this->assertNotNull($promotedFee);
        $this->assertEquals($newFee->amount, $promotedFee->amount);
        $this->assertEquals($newFee->fee_schedule_id, $promotedFee->fee_schedule_id);
    }
}
