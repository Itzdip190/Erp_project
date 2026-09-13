<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\FeeCategory;
use App\Models\FeeComponent;
use App\Models\FeeSchedule;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PreviousYearFeeCarryForwardTest extends TestCase
{
    use RefreshDatabase;

    private $school;
    private $admin;
    private $session2025;
    private $session2026;
    private $classUkg;
    private $classOne;
    private $sectionA;
    private $feeCategory;
    private $feeComponent;
    private $schedule2025;
    private $schedule2026;

    protected function setUp(): void
    {
        parent::setUp();

        $this->school = School::firstOrCreate(
            ['code' => 'SCH_FEE_TEST'],
            ['name' => 'Finance Test School', 'status' => 'active']
        );

        $this->admin = User::factory()->create([
            'school_id' => $this->school->id,
            'role' => 'school_admin',
        ]);

        $this->session2025 = AcademicSession::create([
            'school_id' => $this->school->id,
            'name' => '2025-2026',
            'start_date' => '2025-04-01',
            'end_date' => '2026-03-31',
            'is_current' => false,
        ]);

        $this->session2026 = AcademicSession::create([
            'school_id' => $this->school->id,
            'name' => '2026-2027',
            'start_date' => '2026-04-01',
            'end_date' => '2027-03-31',
            'is_current' => true,
        ]);

        $this->classUkg = SchoolClass::create([
            'school_id' => $this->school->id,
            'name' => 'UKG',
            'numeric_name' => 0,
        ]);

        $this->classOne = SchoolClass::create([
            'school_id' => $this->school->id,
            'name' => 'Class 1',
            'numeric_name' => 1,
        ]);

        $this->sectionA = Section::create([
            'school_id' => $this->school->id,
            'class_id' => $this->classUkg->id,
            'name' => 'A',
        ]);

        $this->feeCategory = FeeCategory::create([
            'school_id' => $this->school->id,
            'name' => 'Tuition Fee',
        ]);

        $this->feeComponent = FeeComponent::create([
            'school_id' => $this->school->id,
            'academic_session_id' => $this->session2025->id,
            'component_name' => 'Tuition Fee',
            'head_name' => 'Tuition',
        ]);

        $this->schedule2025 = FeeSchedule::create([
            'school_id' => $this->school->id,
            'academic_session_id' => $this->session2025->id,
            'name' => 'UKG Schedule 2025',
            'classes' => 'UKG',
            'start_date' => '2025-04-01',
            'end_date' => '2026-03-31',
            'no_of_installments' => 1,
            'installments' => [
                ['installment_no' => 1, 'name' => 'Installment 1', 'due_date' => '2025-05-10', 'amount' => 6000.00]
            ],
        ]);

        $this->schedule2026 = FeeSchedule::create([
            'school_id' => $this->school->id,
            'academic_session_id' => $this->session2026->id,
            'name' => 'Class 1 Schedule 2026',
            'classes' => 'Class 1',
            'start_date' => '2026-04-01',
            'end_date' => '2027-03-31',
            'no_of_installments' => 1,
            'installments' => [
                ['installment_no' => 1, 'name' => 'Installment 1', 'due_date' => '2026-05-10', 'amount' => 8000.00]
            ],
        ]);
    }

    private function createTestStudent(array $attributes = []): Student
    {
        $defaults = [
            'school_id' => $this->school->id,
            'academic_session_id' => $this->session2026->id,
            'class_id' => $this->classOne->id,
            'section_id' => $this->sectionA->id,
            'fee_schedule_id' => $this->schedule2026->id,
            'admission_number' => 'JPPS' . rand(1000, 9999),
            'admission_date' => '2025-04-01',
            'first_name' => 'AISHA',
            'last_name' => 'BANO',
            'gender' => 'Female',
            'father_name' => 'Father Test',
            'father_phone' => '9876543210',
            'mother_name' => 'Mother Test',
            'mother_phone' => '9876543211',
            'guardian_name' => 'Father Test',
            'guardian_relationship' => 'Father',
            'guardian_phone' => '9876543210',
            'date_of_birth' => '2019-05-15',
            'religion' => 'Other',
            'blood_group' => 'O+',
            'address' => '123 Test Street',
            'city' => 'Delhi',
            'state' => 'Delhi',
            'pincode' => '110001',
            'is_active' => true,
        ];

        return Student::create(array_merge($defaults, $attributes));
    }

    /**
     * TEST 1: Previous year has due.
     * 2025-2026: Total = ₹6,000, Paid = ₹3,000, Due = ₹3,000
     * 2026-2027: Expected Previous Year Due = ₹3,000.
     */
    public function test_previous_year_due_carries_forward_to_next_year(): void
    {
        $student = $this->createTestStudent([
            'admission_number' => 'JPPS56',
            'first_name' => 'AISHA',
            'last_name' => 'BANO',
        ]);

        // Fee from 2025-2026: Total 6000, Paid 3000, Remaining Due 3000
        StudentFee::create([
            'school_id' => $this->school->id,
            'student_id' => $student->id,
            'fee_category_id' => $this->feeCategory->id,
            'fee_schedule_id' => $this->schedule2025->id,
            'fee_component_id' => $this->feeComponent->id,
            'installment_no' => 1,
            'amount' => 6000.00,
            'paid_amount' => 3000.00,
            'due_date' => '2025-05-10',
            'status' => 'partially_paid',
        ]);

        // When viewing student-wise fee in 2026-2027
        $response = $this->actingAs($this->admin)->get(route('school.fees.student-wise', [
            'view_student' => $student->id,
            'academic_session_id' => $this->session2026->id,
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('previousYearDue', 3000.00);
        $response->assertSee('Previous Academic Year Due / Carry Forward');
        $response->assertSee('₹3,000');
    }

    /**
     * TEST 2: Previous year is fully paid.
     * Total = ₹6,000, Paid = ₹6,000, Due = ₹0
     * Expected next year carry forward = ₹0.
     */
    public function test_fully_paid_previous_year_has_zero_carry_forward(): void
    {
        $student = $this->createTestStudent([
            'admission_number' => 'JPPS57',
            'first_name' => 'RAHUL',
            'last_name' => 'SHARMA',
        ]);

        // Fee from 2025-2026: Total 6000, Paid 6000, Remaining Due 0
        StudentFee::create([
            'school_id' => $this->school->id,
            'student_id' => $student->id,
            'fee_category_id' => $this->feeCategory->id,
            'fee_schedule_id' => $this->schedule2025->id,
            'fee_component_id' => $this->feeComponent->id,
            'installment_no' => 1,
            'amount' => 6000.00,
            'paid_amount' => 6000.00,
            'due_date' => '2025-05-10',
            'status' => 'paid',
        ]);

        $response = $this->actingAs($this->admin)->get(route('school.fees.student-wise', [
            'view_student' => $student->id,
            'academic_session_id' => $this->session2026->id,
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('previousYearDue', 0.00);
    }

    /**
     * TEST 3: Previous year partially paid.
     * Total = ₹10,000, Paid = ₹7,500, Due = ₹2,500
     * Expected next year: Previous Year Due = ₹2,500.
     */
    public function test_partially_paid_previous_year_carries_only_unpaid_balance(): void
    {
        $student = $this->createTestStudent([
            'admission_number' => 'JPPS58',
            'first_name' => 'KAVITA',
            'last_name' => 'SINGH',
        ]);

        StudentFee::create([
            'school_id' => $this->school->id,
            'student_id' => $student->id,
            'fee_category_id' => $this->feeCategory->id,
            'fee_schedule_id' => $this->schedule2025->id,
            'fee_component_id' => $this->feeComponent->id,
            'installment_no' => 1,
            'amount' => 10000.00,
            'paid_amount' => 7500.00,
            'due_date' => '2025-05-10',
            'status' => 'partially_paid',
        ]);

        $response = $this->actingAs($this->admin)->get(route('school.fees.student-wise', [
            'view_student' => $student->id,
            'academic_session_id' => $this->session2026->id,
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('previousYearDue', 2500.00);
    }

    /**
     * TEST 4: Pay part of carried-forward amount.
     * Previous Due = ₹3,000, Payment = ₹1,000
     * Expected remaining due = ₹2,000.
     */
    public function test_partial_payment_of_carried_forward_due(): void
    {
        $student = $this->createTestStudent([
            'admission_number' => 'JPPS59',
            'first_name' => 'AMIT',
            'last_name' => 'KUMAR',
        ]);

        $prevFee = StudentFee::create([
            'school_id' => $this->school->id,
            'student_id' => $student->id,
            'fee_category_id' => $this->feeCategory->id,
            'fee_schedule_id' => $this->schedule2025->id,
            'fee_component_id' => $this->feeComponent->id,
            'installment_no' => 1,
            'amount' => 6000.00,
            'paid_amount' => 3000.00,
            'due_date' => '2025-05-10',
            'status' => 'partially_paid',
        ]);

        // Pay 1,000 towards the previous year fee
        $response = $this->actingAs($this->admin)->post(route('school.fees.student-wise'), [
            'action' => 'mark_paid',
            'student_id' => $student->id,
            'student_fee_id' => $prevFee->id,
            'installment_no' => 1,
            'amount_paid' => 1000.00,
            'payment_mode' => 'cash',
            'receipt_date' => '2026-05-01',
            'receipt_no' => 'REC-10001',
        ]);

        $this->assertEquals(4000.00, floatval($prevFee->fresh()->paid_amount));
        $this->assertEquals('partially_paid', $prevFee->fresh()->status);

        // Verify next view shows remaining due = 2,000
        $viewResponse = $this->actingAs($this->admin)->get(route('school.fees.student-wise', [
            'view_student' => $student->id,
            'academic_session_id' => $this->session2026->id,
        ]));

        $viewResponse->assertStatus(200);
        $viewResponse->assertViewHas('previousYearDue', 2000.00);
    }

    /**
     * TEST 5: Pay complete carried-forward amount.
     * Previous Due = ₹3,000, Payment = ₹3,000
     * Expected remaining due = ₹0.
     */
    public function test_complete_settlement_of_carried_forward_due(): void
    {
        $student = $this->createTestStudent([
            'admission_number' => 'JPPS60',
            'first_name' => 'PRIYA',
            'last_name' => 'VERMA',
        ]);

        $prevFee = StudentFee::create([
            'school_id' => $this->school->id,
            'student_id' => $student->id,
            'fee_category_id' => $this->feeCategory->id,
            'fee_schedule_id' => $this->schedule2025->id,
            'fee_component_id' => $this->feeComponent->id,
            'installment_no' => 1,
            'amount' => 3000.00,
            'paid_amount' => 0.00,
            'due_date' => '2025-05-10',
            'status' => 'pending',
        ]);

        // Full settlement: Pay 3,000
        $this->actingAs($this->admin)->post(route('school.fees.student-wise'), [
            'action' => 'mark_paid',
            'student_id' => $student->id,
            'student_fee_id' => $prevFee->id,
            'installment_no' => 1,
            'amount_paid' => 3000.00,
            'payment_mode' => 'cash',
            'receipt_date' => '2026-05-01',
            'receipt_no' => 'REC-10002',
        ]);

        $this->assertEquals(3000.00, floatval($prevFee->fresh()->paid_amount));
        $this->assertEquals('paid', $prevFee->fresh()->status);

        $viewResponse = $this->actingAs($this->admin)->get(route('school.fees.student-wise', [
            'view_student' => $student->id,
            'academic_session_id' => $this->session2026->id,
        ]));

        $viewResponse->assertStatus(200);
        $viewResponse->assertViewHas('previousYearDue', 0.00);
    }

    /**
     * TEST 6: Student promotion across classes.
     * 2025-2026: UKG -> 2026-2027: Class 1
     * Outstanding is linked via Admission Number.
     */
    public function test_student_promotion_links_previous_due_via_admission_number(): void
    {
        // Student record in 2025-2026 UKG
        $student = $this->createTestStudent([
            'academic_session_id' => $this->session2025->id,
            'admission_number' => 'JPPS99',
            'first_name' => 'ZARA',
            'last_name' => 'KHAN',
            'class_id' => $this->classUkg->id,
            'fee_schedule_id' => $this->schedule2025->id,
        ]);

        // Fee attached to student record in 2025-2026
        StudentFee::create([
            'school_id' => $this->school->id,
            'student_id' => $student->id,
            'fee_category_id' => $this->feeCategory->id,
            'fee_schedule_id' => $this->schedule2025->id,
            'fee_component_id' => $this->feeComponent->id,
            'installment_no' => 1,
            'amount' => 5000.00,
            'paid_amount' => 2000.00,
            'due_date' => '2025-05-10',
            'status' => 'partially_paid',
        ]);

        // Student promoted to 2026-2027 Class 1
        $student->update([
            'academic_session_id' => $this->session2026->id,
            'class_id' => $this->classOne->id,
            'fee_schedule_id' => $this->schedule2026->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('school.fees.student-wise', [
            'view_student' => $student->id,
            'academic_session_id' => $this->session2026->id,
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('previousYearDue', 3000.00);
    }

    /**
     * TEST 7: Multiple students with different previous balances.
     * Ensure student balances never cross-contaminate.
     */
    public function test_multiple_students_remain_strictly_isolated(): void
    {
        $studentA = $this->createTestStudent([
            'admission_number' => 'ADM001',
            'first_name' => 'STUDENT',
            'last_name' => 'A',
        ]);

        $studentB = $this->createTestStudent([
            'admission_number' => 'ADM002',
            'first_name' => 'STUDENT',
            'last_name' => 'B',
        ]);

        // Student A has 4,000 due
        StudentFee::create([
            'school_id' => $this->school->id,
            'student_id' => $studentA->id,
            'fee_category_id' => $this->feeCategory->id,
            'fee_schedule_id' => $this->schedule2025->id,
            'fee_component_id' => $this->feeComponent->id,
            'installment_no' => 1,
            'amount' => 4000.00,
            'paid_amount' => 0.00,
            'due_date' => '2025-05-10',
            'status' => 'pending',
        ]);

        // Student B has 1,500 due
        StudentFee::create([
            'school_id' => $this->school->id,
            'student_id' => $studentB->id,
            'fee_category_id' => $this->feeCategory->id,
            'fee_schedule_id' => $this->schedule2025->id,
            'fee_component_id' => $this->feeComponent->id,
            'installment_no' => 1,
            'amount' => 1500.00,
            'paid_amount' => 0.00,
            'due_date' => '2025-05-10',
            'status' => 'pending',
        ]);

        $respA = $this->actingAs($this->admin)->get(route('school.fees.student-wise', [
            'view_student' => $studentA->id,
            'academic_session_id' => $this->session2026->id,
        ]));
        $respA->assertViewHas('previousYearDue', 4000.00);

        $respB = $this->actingAs($this->admin)->get(route('school.fees.student-wise', [
            'view_student' => $studentB->id,
            'academic_session_id' => $this->session2026->id,
        ]));
        $respB->assertViewHas('previousYearDue', 1500.00);
    }

    /**
     * TEST 8: Duplicate student names with distinct Admission Numbers.
     * Ensure student records with identical names remain separated.
     */
    public function test_duplicate_student_names_separated_by_admission_number(): void
    {
        $student1 = $this->createTestStudent([
            'admission_number' => 'DUP001',
            'first_name' => 'ROHAN',
            'last_name' => 'SHARMA',
        ]);

        $student2 = $this->createTestStudent([
            'admission_number' => 'DUP002',
            'first_name' => 'ROHAN',
            'last_name' => 'SHARMA',
        ]);

        // Student 1 has 5,000 due
        StudentFee::create([
            'school_id' => $this->school->id,
            'student_id' => $student1->id,
            'fee_category_id' => $this->feeCategory->id,
            'fee_schedule_id' => $this->schedule2025->id,
            'fee_component_id' => $this->feeComponent->id,
            'installment_no' => 1,
            'amount' => 5000.00,
            'paid_amount' => 0.00,
            'due_date' => '2025-05-10',
            'status' => 'pending',
        ]);

        // Student 2 has 0 due (fully paid)
        StudentFee::create([
            'school_id' => $this->school->id,
            'student_id' => $student2->id,
            'fee_category_id' => $this->feeCategory->id,
            'fee_schedule_id' => $this->schedule2025->id,
            'fee_component_id' => $this->feeComponent->id,
            'installment_no' => 1,
            'amount' => 5000.00,
            'paid_amount' => 5000.00,
            'due_date' => '2025-05-10',
            'status' => 'paid',
        ]);

        $resp1 = $this->actingAs($this->admin)->get(route('school.fees.student-wise', [
            'view_student' => $student1->id,
            'academic_session_id' => $this->session2026->id,
        ]));
        $resp1->assertViewHas('previousYearDue', 5000.00);

        $resp2 = $this->actingAs($this->admin)->get(route('school.fees.student-wise', [
            'view_student' => $student2->id,
            'academic_session_id' => $this->session2026->id,
        ]));
        $resp2->assertViewHas('previousYearDue', 0.00);
    }

    /**
     * TEST 9: Academic-year isolation.
     * Switching back to 2025-2026 preserves historical fee records.
     */
    public function test_switching_academic_years_preserves_historical_records(): void
    {
        $student = $this->createTestStudent([
            'admission_number' => 'ISO100',
            'first_name' => 'SANJAY',
            'last_name' => 'GUPTA',
        ]);

        // 2025-2026 fee: 6,000 total, 3,000 paid
        StudentFee::create([
            'school_id' => $this->school->id,
            'student_id' => $student->id,
            'fee_category_id' => $this->feeCategory->id,
            'fee_schedule_id' => $this->schedule2025->id,
            'fee_component_id' => $this->feeComponent->id,
            'installment_no' => 1,
            'amount' => 6000.00,
            'paid_amount' => 3000.00,
            'due_date' => '2025-05-10',
            'status' => 'partially_paid',
        ]);

        // 2026-2027 fee: 8,000 total, 0 paid
        StudentFee::create([
            'school_id' => $this->school->id,
            'student_id' => $student->id,
            'fee_category_id' => $this->feeCategory->id,
            'fee_schedule_id' => $this->schedule2026->id,
            'fee_component_id' => $this->feeComponent->id,
            'installment_no' => 1,
            'amount' => 8000.00,
            'paid_amount' => 0.00,
            'due_date' => '2026-05-10',
            'status' => 'pending',
        ]);

        // View in 2026-2027: Current Due 8,000, Previous Due 3,000
        $resp2026 = $this->actingAs($this->admin)->get(route('school.fees.student-wise', [
            'view_student' => $student->id,
            'academic_session_id' => $this->session2026->id,
        ]));
        $resp2026->assertStatus(200);
        $resp2026->assertViewHas('previousYearDue', 3000.00);

        // View in 2025-2026: 2025 fee is active session fee (3,000 due, 0 previous due)
        $resp2025 = $this->actingAs($this->admin)->get(route('school.fees.student-wise', [
            'view_student' => $student->id,
            'academic_session_id' => $this->session2025->id,
        ]));
        $resp2025->assertStatus(200);
        $resp2025->assertViewHas('previousYearDue', 0.00);
    }

    /**
     * TEST 10: Previous year has multiple installments (mix of paid and unpaid).
     * Installments 1 & 2: Paid (Due 0)
     * Installments 3, 4, 5: Unpaid (Due 4000 each = 12,000 total)
     * Expected: Total Previous Year Due = 12,000; only Installments 3, 4, 5 rendered.
     */
    public function test_previous_year_shows_only_unpaid_installments_in_dropdown_and_filters_paid_installments(): void
    {
        $student = $this->createTestStudent([
            'admission_number' => 'JPPS200',
            'first_name' => 'SALAM',
            'last_name' => 'ROCKY BHAI',
        ]);

        // Installment 1: Fully Paid
        StudentFee::create([
            'school_id' => $this->school->id,
            'student_id' => $student->id,
            'fee_category_id' => $this->feeCategory->id,
            'fee_schedule_id' => $this->schedule2025->id,
            'fee_component_id' => $this->feeComponent->id,
            'installment_no' => 1,
            'amount' => 4000.00,
            'paid_amount' => 4000.00,
            'due_date' => '2025-05-10',
            'status' => 'paid',
        ]);

        // Installment 2: Fully Paid
        StudentFee::create([
            'school_id' => $this->school->id,
            'student_id' => $student->id,
            'fee_category_id' => $this->feeCategory->id,
            'fee_schedule_id' => $this->schedule2025->id,
            'fee_component_id' => $this->feeComponent->id,
            'installment_no' => 2,
            'amount' => 4000.00,
            'paid_amount' => 4000.00,
            'due_date' => '2025-06-10',
            'status' => 'paid',
        ]);

        // Installment 3: Unpaid
        StudentFee::create([
            'school_id' => $this->school->id,
            'student_id' => $student->id,
            'fee_category_id' => $this->feeCategory->id,
            'fee_schedule_id' => $this->schedule2025->id,
            'fee_component_id' => $this->feeComponent->id,
            'installment_no' => 3,
            'amount' => 4000.00,
            'paid_amount' => 0.00,
            'due_date' => '2025-07-10',
            'status' => 'pending',
        ]);

        // Installment 4: Unpaid
        StudentFee::create([
            'school_id' => $this->school->id,
            'student_id' => $student->id,
            'fee_category_id' => $this->feeCategory->id,
            'fee_schedule_id' => $this->schedule2025->id,
            'fee_component_id' => $this->feeComponent->id,
            'installment_no' => 4,
            'amount' => 4000.00,
            'paid_amount' => 0.00,
            'due_date' => '2025-08-10',
            'status' => 'pending',
        ]);

        // Installment 5: Unpaid
        StudentFee::create([
            'school_id' => $this->school->id,
            'student_id' => $student->id,
            'fee_category_id' => $this->feeCategory->id,
            'fee_schedule_id' => $this->schedule2025->id,
            'fee_component_id' => $this->feeComponent->id,
            'installment_no' => 5,
            'amount' => 4000.00,
            'paid_amount' => 0.00,
            'due_date' => '2025-09-10',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->get(route('school.fees.student-wise', [
            'view_student' => $student->id,
            'academic_session_id' => $this->session2026->id,
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('previousYearDue', 12000.00);
        $response->assertSee('Outstanding Due: ₹12,000');
        $response->assertSee('togglePrevDue');
        $response->assertSee('Installment 3');
        $response->assertSee('Installment 4');
        $response->assertSee('Installment 5');
    }

    /**
     * TEST 11: Pay Multiple Fees for selected previous-year installments.
     * Select Installment 3 (4000) + Installment 4 (4000) = 8,000 total.
     * After multi-payment: Installments 3 & 4 become paid, remaining previous due = 4,000.
     */
    public function test_multi_pay_can_collect_payment_for_multiple_selected_previous_year_installments(): void
    {
        $student = $this->createTestStudent([
            'admission_number' => 'JPPS201',
            'first_name' => 'VIKRAM',
            'last_name' => 'RATHORE',
        ]);

        $fee3 = StudentFee::create([
            'school_id' => $this->school->id,
            'student_id' => $student->id,
            'fee_category_id' => $this->feeCategory->id,
            'fee_schedule_id' => $this->schedule2025->id,
            'fee_component_id' => $this->feeComponent->id,
            'installment_no' => 3,
            'amount' => 4000.00,
            'paid_amount' => 0.00,
            'due_date' => '2025-07-10',
            'status' => 'pending',
        ]);

        $fee4 = StudentFee::create([
            'school_id' => $this->school->id,
            'student_id' => $student->id,
            'fee_category_id' => $this->feeCategory->id,
            'fee_schedule_id' => $this->schedule2025->id,
            'fee_component_id' => $this->feeComponent->id,
            'installment_no' => 4,
            'amount' => 4000.00,
            'paid_amount' => 0.00,
            'due_date' => '2025-08-10',
            'status' => 'pending',
        ]);

        $fee5 = StudentFee::create([
            'school_id' => $this->school->id,
            'student_id' => $student->id,
            'fee_category_id' => $this->feeCategory->id,
            'fee_schedule_id' => $this->schedule2025->id,
            'fee_component_id' => $this->feeComponent->id,
            'installment_no' => 5,
            'amount' => 4000.00,
            'paid_amount' => 0.00,
            'due_date' => '2025-09-10',
            'status' => 'pending',
        ]);

        // Pay Installments 3 & 4 combined (8,000)
        $response = $this->actingAs($this->admin)->post(route('school.fees.student-wise'), [
            'action' => 'mark_paid',
            'student_id' => $student->id,
            'installment_no' => 999,
            'student_fee_ids' => "{$fee3->id},{$fee4->id}",
            'amount_paid' => 8000.00,
            'payment_mode' => 'cash',
            'receipt_date' => '2026-05-01',
            'receipt_no' => 'REC-MULTI-01',
        ]);

        $this->assertEquals(4000.00, floatval($fee3->fresh()->paid_amount));
        $this->assertEquals('paid', $fee3->fresh()->status);
        $this->assertEquals(4000.00, floatval($fee4->fresh()->paid_amount));
        $this->assertEquals('paid', $fee4->fresh()->status);
        $this->assertEquals(0.00, floatval($fee5->fresh()->paid_amount));
        $this->assertEquals('pending', $fee5->fresh()->status);

        // Next view should show remaining previous due = 4,000
        $viewResponse = $this->actingAs($this->admin)->get(route('school.fees.student-wise', [
            'view_student' => $student->id,
            'academic_session_id' => $this->session2026->id,
        ]));

        $viewResponse->assertStatus(200);
        $viewResponse->assertViewHas('previousYearDue', 4000.00);
        $viewResponse->assertSee('Outstanding Due: ₹4,000');
        $viewResponse->assertSee('Installment 5');
    }

    /**
     * TEST 12: Partially paid previous-year installment.
     * Installment Amount = 4000, Paid = 2500, Due = 1500.
     * Expected: Carry-forward section shows Due = 1500 (not 4000), selectable due = 1500.
     */
    public function test_partially_paid_installment_shows_only_outstanding_balance_in_dropdown(): void
    {
        $student = $this->createTestStudent([
            'admission_number' => 'JPPS202',
            'first_name' => 'DEEPAK',
            'last_name' => 'SHARMA',
        ]);

        $fee = StudentFee::create([
            'school_id' => $this->school->id,
            'student_id' => $student->id,
            'fee_category_id' => $this->feeCategory->id,
            'fee_schedule_id' => $this->schedule2025->id,
            'fee_component_id' => $this->feeComponent->id,
            'installment_no' => 1,
            'amount' => 4000.00,
            'paid_amount' => 2500.00,
            'due_date' => '2025-05-10',
            'status' => 'partially_paid',
        ]);

        $response = $this->actingAs($this->admin)->get(route('school.fees.student-wise', [
            'view_student' => $student->id,
            'academic_session_id' => $this->session2026->id,
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('previousYearDue', 1500.00);
        $response->assertSee('Outstanding Due: ₹1,500');
        $response->assertSee('data-due="1500"', false);

        // Complete the remaining 1500
        $this->actingAs($this->admin)->post(route('school.fees.student-wise'), [
            'action' => 'mark_paid',
            'student_id' => $student->id,
            'student_fee_id' => $fee->id,
            'installment_no' => 1,
            'amount_paid' => 1500.00,
            'payment_mode' => 'cash',
            'receipt_date' => '2026-05-01',
            'receipt_no' => 'REC-PART-01',
        ]);

        $this->assertEquals(4000.00, floatval($fee->fresh()->paid_amount));
        $this->assertEquals('paid', $fee->fresh()->status);
    }

    /**
     * TEST 13: Promoted student with previous session records paid via multi-pay.
     */
    public function test_promoted_student_previous_records_multi_pay(): void
    {
        // Student in 2025-2026
        $student2025 = $this->createTestStudent([
            'academic_session_id' => $this->session2025->id,
            'admission_number' => 'JPPS203',
            'first_name' => 'SAMEER',
            'last_name' => 'KHAN',
            'class_id' => $this->classUkg->id,
        ]);

        $prevFee1 = StudentFee::create([
            'school_id' => $this->school->id,
            'student_id' => $student2025->id,
            'fee_category_id' => $this->feeCategory->id,
            'fee_schedule_id' => $this->schedule2025->id,
            'fee_component_id' => $this->feeComponent->id,
            'installment_no' => 1,
            'amount' => 4000.00,
            'paid_amount' => 0.00,
            'due_date' => '2025-05-10',
            'status' => 'pending',
        ]);

        $prevFee2 = StudentFee::create([
            'school_id' => $this->school->id,
            'student_id' => $student2025->id,
            'fee_category_id' => $this->feeCategory->id,
            'fee_schedule_id' => $this->schedule2025->id,
            'fee_component_id' => $this->feeComponent->id,
            'installment_no' => 2,
            'amount' => 4000.00,
            'paid_amount' => 0.00,
            'due_date' => '2025-06-10',
            'status' => 'pending',
        ]);

        // Student promoted to 2026-2027 Class 1
        $student2025->update([
            'academic_session_id' => $this->session2026->id,
            'class_id' => $this->classOne->id,
            'fee_schedule_id' => $this->schedule2026->id,
        ]);

        // Multi-pay both previous fees via student's ID
        $response = $this->actingAs($this->admin)->post(route('school.fees.student-wise'), [
            'action' => 'mark_paid',
            'student_id' => $student2025->id,
            'installment_no' => 999,
            'student_fee_ids' => "{$prevFee1->id},{$prevFee2->id}",
            'amount_paid' => 8000.00,
            'payment_mode' => 'cash',
            'receipt_date' => '2026-05-01',
            'receipt_no' => 'REC-PROM-01',
        ]);

        $this->assertEquals(4000.00, floatval($prevFee1->fresh()->paid_amount));
        $this->assertEquals('paid', $prevFee1->fresh()->status);
        $this->assertEquals(4000.00, floatval($prevFee2->fresh()->paid_amount));
        $this->assertEquals('paid', $prevFee2->fresh()->status);

        // View in 2026-2027
        $viewResponse = $this->actingAs($this->admin)->get(route('school.fees.student-wise', [
            'view_student' => $student2025->id,
            'academic_session_id' => $this->session2026->id,
        ]));

        $viewResponse->assertStatus(200);
        $viewResponse->assertViewHas('previousYearDue', 0.00);
    }

    /**
     * TEST 14: Promoted student visibility in previous academic session listing and old dues payment.
     * Scenario:
     * - Student "SuperStart Yash" in 2025-2026 Class UKG Section A has ₹10,000 fee due.
     * - Student is promoted to 2026-2027 Class 1 Section A.
     * - When viewing Student Wise Fees in 2025-2026 filtered by Class UKG and Section A:
     *   Student MUST be visible, show UKG A, and show ₹10,000 due.
     * - When viewing Student Wise Fees in 2026-2027 filtered by Class 1 and Section A:
     *   Student MUST be visible, show Class 1 A, and show ₹10,000 carried-forward due.
     * - Pay ₹10,000 for 2025-2026 fee.
     * - Next session (2026-2027) carry-forward due automatically reduces to ₹0.
     */
    public function test_promoted_student_visible_in_previous_academic_session_list_and_can_pay_old_dues(): void
    {
        $student = $this->createTestStudent([
            'admission_number' => 'JPPS_YASH',
            'first_name' => 'SuperStart',
            'last_name' => 'Yash',
            'academic_session_id' => $this->session2025->id,
            'class_id' => $this->classUkg->id,
            'section_id' => $this->sectionA->id,
            'fee_schedule_id' => $this->schedule2025->id,
        ]);

        // StudentSession record for 2025-2026
        \App\Models\StudentSession::create([
            'school_id' => $this->school->id,
            'student_id' => $student->id,
            'academic_session_id' => $this->session2025->id,
            'class_id' => $this->classUkg->id,
            'section_id' => $this->sectionA->id,
            'is_promoted' => true,
        ]);

        // 2025-2026 Fee: ₹10,000
        $fee2025 = StudentFee::create([
            'school_id' => $this->school->id,
            'student_id' => $student->id,
            'fee_category_id' => $this->feeCategory->id,
            'fee_schedule_id' => $this->schedule2025->id,
            'fee_component_id' => $this->feeComponent->id,
            'installment_no' => 1,
            'amount' => 10000.00,
            'paid_amount' => 0.00,
            'due_date' => '2025-05-10',
            'status' => 'pending',
        ]);

        // Promote student to 2026-2027 Class 1 Section A
        $student->update([
            'academic_session_id' => $this->session2026->id,
            'class_id' => $this->classOne->id,
            'section_id' => $this->sectionA->id,
            'fee_schedule_id' => $this->schedule2026->id,
        ]);

        \App\Models\StudentSession::create([
            'school_id' => $this->school->id,
            'student_id' => $student->id,
            'academic_session_id' => $this->session2026->id,
            'class_id' => $this->classOne->id,
            'section_id' => $this->sectionA->id,
            'is_promoted' => false,
        ]);

        // 1. Verify Student Wise Fees LIST VIEW in 2025-2026 (UKG A)
        $listResponse2025 = $this->actingAs($this->admin)->get(route('school.fees.student-wise', [
            'academic_session_id' => $this->session2025->id,
            'class_id' => $this->classUkg->id,
            'section_id' => $this->sectionA->id,
        ]));

        $listResponse2025->assertStatus(200);
        $listResponse2025->assertSee('SuperStart Yash');
        $listResponse2025->assertSee('JPPS_YASH');
        $listResponse2025->assertSee('UKG');
        $listResponse2025->assertSee('UKG Schedule 2025');

        // 2. Verify Student Wise Fees LIST VIEW in 2026-2027 (Class 1 A)
        $listResponse2026 = $this->actingAs($this->admin)->get(route('school.fees.student-wise', [
            'academic_session_id' => $this->session2026->id,
            'class_id' => $this->classOne->id,
            'section_id' => $this->sectionA->id,
        ]));

        $listResponse2026->assertStatus(200);
        $listResponse2026->assertSee('SuperStart Yash');
        $listResponse2026->assertSee('Class 1');

        // 3. Verify Detail View in 2025-2026
        $detailResponse2025 = $this->actingAs($this->admin)->get(route('school.fees.student-wise', [
            'view_student' => $student->id,
            'academic_session_id' => $this->session2025->id,
        ]));

        $detailResponse2025->assertStatus(200);
        $detailResponse2025->assertSee('SuperStart Yash');
        $detailResponse2025->assertSee('UKG Schedule 2025');

        // 4. Pay ₹10,000 for 2025-2026 fee in previous session
        $payResponse = $this->actingAs($this->admin)->post(route('school.fees.student-wise'), [
            'action' => 'mark_paid',
            'student_id' => $student->id,
            'installment_no' => 1,
            'student_fee_id' => $fee2025->id,
            'amount_paid' => 10000.00,
            'payment_mode' => 'cash',
            'receipt_date' => '2026-05-15',
            'receipt_no' => 'REC-YASH-01',
        ]);

        $this->assertEquals(10000.00, floatval($fee2025->fresh()->paid_amount));
        $this->assertEquals('paid', $fee2025->fresh()->status);

        // 5. Verify that in 2026-2027, the carry-forward due is now ₹0
        $detailResponse2026 = $this->actingAs($this->admin)->get(route('school.fees.student-wise', [
            'view_student' => $student->id,
            'academic_session_id' => $this->session2026->id,
        ]));

        $detailResponse2026->assertStatus(200);
        $detailResponse2026->assertViewHas('previousYearDue', 0.00);
    }

    /**
     * TEST 15: New admission in current academic session with NO fee structure created.
     * Scenario:
     * - In 2026-2027, a new student is admitted (fresh admission, no enrollment in 2025-2026).
     * - Even if rogue 2025-2026 fee records exist from an unscoped sync or stale fee_schedule_id pointing to 2025-2026:
     * - In 2026-2027 Student-wise Fee page:
     *   - Fee Schedule Name MUST be '-' (not 2025-2026 schedule).
     *   - Previous Year Due MUST be 0.
     *   - Total Due (All Yrs) MUST be 0.
     *   - Rogue unpaid fees from non-enrolled sessions are automatically purged.
     */
    public function test_new_admission_in_current_session_has_zero_dues_when_no_current_fee_structure_exists(): void
    {
        // New student admitted in 2026-2027 (admission date inside 2026-2027)
        $newStudent = $this->createTestStudent([
            'admission_number' => 'JPPS_FRESH_NURSERY',
            'first_name' => 'Sanskar',
            'last_name' => 'Pal',
            'academic_session_id' => $this->session2026->id,
            'admission_date' => '2026-04-10',
            'class_id' => $this->classUkg->id,
            'section_id' => $this->sectionA->id,
            'fee_schedule_id' => $this->schedule2025->id, // Simulate stale cross-session assignment bug
        ]);

        // Create student_session for 2026-2027 ONLY (never enrolled in 2025-2026)
        \App\Models\StudentSession::create([
            'school_id' => $this->school->id,
            'student_id' => $newStudent->id,
            'academic_session_id' => $this->session2026->id,
            'class_id' => $this->classUkg->id,
            'section_id' => $this->sectionA->id,
            'is_promoted' => false,
        ]);

        // Simulate rogue 2025-2026 fee generated by unscoped sync bug
        $rogueFee = StudentFee::create([
            'school_id' => $this->school->id,
            'student_id' => $newStudent->id,
            'fee_category_id' => $this->feeCategory->id,
            'fee_schedule_id' => $this->schedule2025->id,
            'fee_component_id' => $this->feeComponent->id,
            'installment_no' => 1,
            'amount' => 7650.00,
            'paid_amount' => 0.00,
            'due_date' => '2025-05-10',
            'status' => 'pending',
        ]);

        // 1. Visit Student Wise Fees LIST VIEW in 2026-2027
        $listResponse = $this->actingAs($this->admin)->get(route('school.fees.student-wise', [
            'academic_session_id' => $this->session2026->id,
            'class_id' => $this->classUkg->id,
            'section_id' => $this->sectionA->id,
        ]));

        $listResponse->assertStatus(200);
        $listResponse->assertSee('Sanskar Pal');
        $listResponse->assertDontSee('UKG Schedule 2025'); // Must NOT show 2025-2026 schedule name
        $listResponse->assertDontSee('₹7,650'); // Must NOT show rogue 7650 due

        // 2. Visit Detail View in 2026-2027
        $detailResponse = $this->actingAs($this->admin)->get(route('school.fees.student-wise', [
            'view_student' => $newStudent->id,
            'academic_session_id' => $this->session2026->id,
        ]));

        $detailResponse->assertStatus(200);
        $detailResponse->assertViewHas('previousYearDue', 0.00);
        $detailResponse->assertViewHas('feeScheduleName', '-');

        // 3. Verify rogue fee was purged
        $this->assertNull(StudentFee::withoutGlobalScopes()->find($rogueFee->id));
        // Verify stale fee_schedule_id on student was cleared
        $this->assertNull($newStudent->fresh()->fee_schedule_id);
    }
}

