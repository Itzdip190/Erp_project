<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\ClassWiseFee;
use App\Models\FeeCategory;
use App\Models\FeeComponent;
use App\Models\FeeDiscount;
use App\Models\FeeFine;
use App\Models\FeeReceipt;
use App\Models\FeeSchedule;
use App\Models\MiscFee;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentCategory;
use App\Models\StudentFee;
use App\Models\TransportFeeSchedule;
use App\Models\User;
use App\Services\FeeStructureCarryForwardService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeeStructureCarryForwardTest extends TestCase
{
    use RefreshDatabase;

    private School $school;
    private User $admin;
    private AcademicSession $session2026;
    private AcademicSession $session2027;
    private SchoolClass $class1;
    private SchoolClass $class2;
    private Section $sectionA;
    private Section $sectionB;
    private StudentCategory $dayBoarding;
    private Student $studentJohn;

    protected function setUp(): void
    {
        parent::setUp();

        $this->school = School::firstOrCreate(
            ['code' => 'SCH_CF_TEST'],
            ['name' => 'Vedant Public School', 'status' => 'active']
        );

        $this->admin = User::factory()->create([
            'school_id' => $this->school->id,
            'role' => 'school_admin',
        ]);

        $this->session2026 = AcademicSession::create([
            'school_id' => $this->school->id,
            'name' => 'Apr 2026 - Mar 2027',
            'start_date' => '2026-04-01',
            'end_date' => '2027-03-31',
            'is_current' => true,
        ]);

        $this->class1 = SchoolClass::create([
            'school_id' => $this->school->id,
            'name' => 'Class-1',
            'numeric_name' => 1,
        ]);

        $this->class2 = SchoolClass::create([
            'school_id' => $this->school->id,
            'name' => 'Class-2',
            'numeric_name' => 2,
        ]);

        $this->sectionA = Section::create([
            'school_id' => $this->school->id,
            'class_id' => $this->class1->id,
            'name' => 'A',
        ]);

        $this->sectionB = Section::create([
            'school_id' => $this->school->id,
            'class_id' => $this->class2->id,
            'name' => 'B',
        ]);

        $this->dayBoarding = StudentCategory::firstOrCreate(
            ['school_id' => $this->school->id, 'name' => 'Day boarding']
        );

        $this->studentJohn = Student::create([
            'school_id' => $this->school->id,
            'academic_session_id' => $this->session2026->id,
            'class_id' => $this->class1->id,
            'section_id' => $this->sectionA->id,
            'admission_number' => 'ADM2026001',
            'admission_date' => '2026-04-01',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'gender' => 'Male',
            'admission_type' => 'New',
            'date_of_birth' => '2019-05-15',
            'father_name' => 'Father Doe',
            'father_phone' => '9876543210',
            'mother_name' => 'Mother Doe',
            'mother_phone' => '9876543211',
            'guardian_name' => 'Father Doe',
            'guardian_relationship' => 'Father',
            'guardian_phone' => '9876543210',
            'religion' => 'Other',
            'blood_group' => 'O+',
            'address' => '123 Test Street',
            'city' => 'Delhi',
            'state' => 'Delhi',
            'pincode' => '110001',
            'is_active' => true,
        ]);
    }

    /**
     * Helper to set up a complete Fee Structure matching the user's reference screenshots for 2026-2027.
     */
    private function setup2026FeeStructure(): array
    {
        // 1. Fee Schedule (Schedule-1 with 4 installments)
        $installments = [
            [
                'installment_no' => 1,
                'name' => 'Installment 1',
                'start_date' => '2026-04-01',
                'end_date' => '2026-06-30',
                'due_date' => '2026-06-30',
                'grace_days' => 5,
            ],
            [
                'installment_no' => 2,
                'name' => 'Installment 2',
                'start_date' => '2026-07-01',
                'end_date' => '2026-09-30',
                'due_date' => '2026-09-30',
                'grace_days' => 5,
            ],
            [
                'installment_no' => 3,
                'name' => 'Installment 3',
                'start_date' => '2026-10-01',
                'end_date' => '2026-12-31',
                'due_date' => '2026-12-31',
                'grace_days' => 5,
            ],
            [
                'installment_no' => 4,
                'name' => 'Installment 4',
                'start_date' => '2027-01-01',
                'end_date' => '2027-03-31',
                'due_date' => '2027-03-31',
                'grace_days' => 5,
            ],
        ];

        $schedule = FeeSchedule::create([
            'school_id' => $this->school->id,
            'academic_session_id' => $this->session2026->id,
            'classes' => 'Class-1, Class-2',
            'sections' => 'Class-1-A, Class-2-B',
            'no_of_installments' => 4,
            'name' => 'Schedule-1',
            'start_date' => '2026-04-01',
            'end_date' => '2027-03-31',
            'installment_type' => 'custom',
            'installments' => $installments,
        ]);

        // 2. Fee Components (5 components from screenshots)
        $compAdmission = FeeComponent::create([
            'school_id' => $this->school->id,
            'academic_session_id' => $this->session2026->id,
            'fee_schedule_id' => $schedule->id,
            'head_name' => 'School Fee',
            'component_name' => 'Admission Fee',
            'admission_type' => 'New',
            'gender' => 'All Students',
        ]);

        $compTuition = FeeComponent::create([
            'school_id' => $this->school->id,
            'academic_session_id' => $this->session2026->id,
            'fee_schedule_id' => $schedule->id,
            'head_name' => 'School Fee',
            'component_name' => 'Tution Fee',
            'admission_type' => 'All Students',
            'gender' => 'All Students',
        ]);

        $compSmartClass = FeeComponent::create([
            'school_id' => $this->school->id,
            'academic_session_id' => $this->session2026->id,
            'fee_schedule_id' => $schedule->id,
            'head_name' => 'school fee',
            'component_name' => 'smart class',
            'admission_type' => 'All Students',
            'gender' => 'All Students',
        ]);

        $compIdCard = FeeComponent::create([
            'school_id' => $this->school->id,
            'academic_session_id' => $this->session2026->id,
            'fee_schedule_id' => $schedule->id,
            'head_name' => 'School fee',
            'component_name' => 'ID card/Diary Fee',
            'admission_type' => 'All Students',
            'gender' => 'All Students',
        ]);

        $compTransport = FeeComponent::create([
            'school_id' => $this->school->id,
            'academic_session_id' => $this->session2026->id,
            'fee_schedule_id' => null,
            'head_name' => 'Transport',
            'component_name' => 'Transport Fee',
            'admission_type' => 'All Students',
            'gender' => 'All Students',
        ]);

        // 3. Fee Fine linked to Tuition Fee
        $fine = FeeFine::create([
            'school_id' => $this->school->id,
            'academic_session_id' => $this->session2026->id,
            'fee_component_id' => $compTuition->id,
            'name' => 'Late Fee',
            'fine_type' => 'Fixed Amount',
            'fine_amount' => 100.00,
            'default_grace_days' => 5,
            'status' => true,
        ]);

        $schedule->update(['fine_id' => $fine->id]);

        // 4. Fee Discounts (2 discounts)
        $discountFemale = FeeDiscount::create([
            'school_id' => $this->school->id,
            'academic_session_id' => $this->session2026->id,
            'name' => 'Female Discount',
            'amount' => 10.00,
            'type' => 'percentage',
            'classes_installments' => json_encode(['Class-1', 'Class-2']),
            'sections' => 'Class-1-A',
            'target_group' => 'gender_female',
            'fee_component_ids' => json_encode([(string) $compTuition->id]),
        ]);

        $discountAlan = FeeDiscount::create([
            'school_id' => $this->school->id,
            'academic_session_id' => $this->session2026->id,
            'name' => 'Alan Walker',
            'amount' => 10.00,
            'type' => 'percentage',
            'classes_installments' => json_encode(['Class-1']),
            'student_ids' => json_encode([(string) $this->studentJohn->id]),
            'target_group' => 'specific_students',
            'fee_component_ids' => json_encode([(string) $compTuition->id, (string) $compSmartClass->id]),
        ]);

        // 5. Misc Fee
        $miscFee = MiscFee::create([
            'school_id' => $this->school->id,
            'academic_session_id' => $this->session2026->id,
            'fee_head_name' => 'School Fee',
            'name' => 'science fair',
            'amount' => 500.00,
            'classes_installments' => json_encode(['Class-1', 'Class-2']),
        ]);

        // 6. ClassWiseFee
        $cwFeeTuition = ClassWiseFee::create([
            'school_id' => $this->school->id,
            'academic_session_id' => $this->session2026->id,
            'class_id' => $this->class1->id,
            'section_id' => $this->sectionA->id,
            'fee_schedule_id' => $schedule->id,
            'student_category_id' => $this->dayBoarding->id,
            'fee_component_id' => $compTuition->id,
            'is_active' => true,
            'amount' => 12000.00,
            'installments' => [
                ['installment_no' => 1, 'name' => 'Installment 1', 'amount' => 3000.00, 'due_date' => '2026-06-30'],
                ['installment_no' => 2, 'name' => 'Installment 2', 'amount' => 3000.00, 'due_date' => '2026-09-30'],
                ['installment_no' => 3, 'name' => 'Installment 3', 'amount' => 3000.00, 'due_date' => '2026-12-31'],
                ['installment_no' => 4, 'name' => 'Installment 4', 'amount' => 3000.00, 'due_date' => '2027-03-31'],
            ],
        ]);

        return compact(
            'schedule',
            'compAdmission',
            'compTuition',
            'compSmartClass',
            'compIdCard',
            'compTransport',
            'fine',
            'discountFemale',
            'discountAlan',
            'miscFee',
            'cwFeeTuition'
        );
    }

    /**
     * TEST 1: Create/use 2026-2027 Fee Structure and verify all components exist.
     */
    public function test_test_1_fee_structure_exists_in_source_session()
    {
        $this->setup2026FeeStructure();

        $this->assertEquals(1, FeeSchedule::where('school_id', $this->school->id)->where('academic_session_id', $this->session2026->id)->count());
        $this->assertEquals(5, FeeComponent::where('school_id', $this->school->id)->where('academic_session_id', $this->session2026->id)->count());
        $this->assertEquals(2, FeeDiscount::where('school_id', $this->school->id)->where('academic_session_id', $this->session2026->id)->count());
        $this->assertEquals(1, MiscFee::where('school_id', $this->school->id)->where('academic_session_id', $this->session2026->id)->count());
        $this->assertEquals(1, FeeFine::where('school_id', $this->school->id)->where('academic_session_id', $this->session2026->id)->count());
        $this->assertEquals(1, ClassWiseFee::where('school_id', $this->school->id)->where('academic_session_id', $this->session2026->id)->count());
    }

    /**
     * TEST 2: Create 2027-2028 via HTTP POST and verify complete 2026-2027 Fee Structure is carried forward.
     */
    public function test_test_2_create_2027_2028_carries_forward_complete_structure()
    {
        $this->setup2026FeeStructure();

        $response = $this->actingAs($this->admin)->post(route('school.fees.basics'), [
            'action' => 'add_academic_session',
            'name' => 'Apr 2027 - Mar 2028',
            'start_date' => '2027-04-01',
            'end_date' => '2028-03-31',
            'is_current' => 1,
            'source_academic_session_id' => $this->session2026->id,
        ]);

        $session2027 = AcademicSession::where('school_id', $this->school->id)->where('name', 'Apr 2027 - Mar 2028')->first();
        $this->assertNotNull($session2027);

        $response->assertRedirect(route('school.fees.basics', ['academic_session_id' => $session2027->id]));

        // Verify counts in 2027-2028 match 2026-2027
        $this->assertEquals(1, FeeSchedule::where('academic_session_id', $session2027->id)->count());
        $this->assertEquals(5, FeeComponent::where('academic_session_id', $session2027->id)->count());
        $this->assertEquals(2, FeeDiscount::where('academic_session_id', $session2027->id)->count());
        $this->assertEquals(1, MiscFee::where('academic_session_id', $session2027->id)->count());
        $this->assertEquals(1, FeeFine::where('academic_session_id', $session2027->id)->count());
        $this->assertEquals(1, ClassWiseFee::where('academic_session_id', $session2027->id)->count());
    }

    /**
     * TEST 3: Verify Fee Schedule carry-forward with shifted dates and installment count.
     */
    public function test_test_3_verify_fee_schedule_carry_forward()
    {
        $this->setup2026FeeStructure();

        $session2027 = AcademicSession::create([
            'school_id' => $this->school->id,
            'name' => 'Apr 2027 - Mar 2028',
            'start_date' => '2027-04-01',
            'end_date' => '2028-03-31',
            'is_current' => true,
        ]);

        FeeStructureCarryForwardService::carryForward($this->school->id, $this->session2026->id, $session2027->id);

        $clonedSched = FeeSchedule::where('academic_session_id', $session2027->id)->first();
        $this->assertNotNull($clonedSched);
        $this->assertEquals('Schedule-1', $clonedSched->name);
        $this->assertEquals(4, $clonedSched->no_of_installments);
        $this->assertEquals('2027-04-01', $clonedSched->start_date->toDateString());
        $this->assertEquals('2028-03-31', $clonedSched->end_date->toDateString());

        $installments = $clonedSched->installments;
        $this->assertCount(4, $installments);
        $this->assertEquals('2027-04-01', $installments[0]['start_date']);
        $this->assertEquals('2027-06-30', $installments[0]['due_date']);
        $this->assertEquals('2028-01-01', $installments[3]['start_date']);
        $this->assertEquals('2028-03-31', $installments[3]['due_date']);
    }

    /**
     * TEST 4 & 5: Verify Fee Components carry forward, Head Name & Component Name relationships and Schedule foreign key remapping.
     */
    public function test_test_4_and_5_fee_components_and_head_relationships()
    {
        $this->setup2026FeeStructure();

        $session2027 = AcademicSession::create([
            'school_id' => $this->school->id,
            'name' => 'Apr 2027 - Mar 2028',
            'start_date' => '2027-04-01',
            'end_date' => '2028-03-31',
            'is_current' => true,
        ]);

        FeeStructureCarryForwardService::carryForward($this->school->id, $this->session2026->id, $session2027->id);

        $clonedSched = FeeSchedule::where('academic_session_id', $session2027->id)->first();
        $clonedComponents = FeeComponent::where('academic_session_id', $session2027->id)->get();

        $this->assertCount(5, $clonedComponents);

        $tuition = $clonedComponents->firstWhere('component_name', 'Tution Fee');
        $this->assertNotNull($tuition);
        $this->assertEquals('School Fee', $tuition->head_name);
        $this->assertEquals('All Students', $tuition->admission_type);
        $this->assertEquals('All Students', $tuition->gender);
        $this->assertEquals($clonedSched->id, $tuition->fee_schedule_id);

        $admission = $clonedComponents->firstWhere('component_name', 'Admission Fee');
        $this->assertEquals('New', $admission->admission_type);
        $this->assertEquals($clonedSched->id, $admission->fee_schedule_id);

        $transport = $clonedComponents->firstWhere('component_name', 'Transport Fee');
        $this->assertNull($transport->fee_schedule_id);
    }

    /**
     * TEST 6: Verify Fee Discount carry-forward and component ID remapping.
     */
    public function test_test_6_fee_discount_carry_forward_and_component_remapping()
    {
        $this->setup2026FeeStructure();

        $session2027 = AcademicSession::create([
            'school_id' => $this->school->id,
            'name' => 'Apr 2027 - Mar 2028',
            'start_date' => '2027-04-01',
            'end_date' => '2028-03-31',
            'is_current' => true,
        ]);

        FeeStructureCarryForwardService::carryForward($this->school->id, $this->session2026->id, $session2027->id);

        $clonedDiscounts = FeeDiscount::where('academic_session_id', $session2027->id)->get();
        $this->assertCount(2, $clonedDiscounts);

        $clonedTuition = FeeComponent::where('academic_session_id', $session2027->id)->where('component_name', 'Tution Fee')->first();
        $femaleDisc = $clonedDiscounts->firstWhere('name', 'Female Discount');
        $this->assertNotNull($femaleDisc);
        $this->assertEquals(10.00, (float) $femaleDisc->amount);
        $this->assertEquals('percentage', $femaleDisc->type);

        $decodedComponentIds = json_decode($femaleDisc->fee_component_ids, true);
        $this->assertEquals([(string) $clonedTuition->id], $decodedComponentIds);
        $this->assertNotContains((string) FeeComponent::where('academic_session_id', $this->session2026->id)->where('component_name', 'Tution Fee')->value('id'), $decodedComponentIds);
    }

    /**
     * TEST 7: Verify Misc Fee carry-forward.
     */
    public function test_test_7_misc_fee_carry_forward()
    {
        $this->setup2026FeeStructure();

        $session2027 = AcademicSession::create([
            'school_id' => $this->school->id,
            'name' => 'Apr 2027 - Mar 2028',
            'start_date' => '2027-04-01',
            'end_date' => '2028-03-31',
            'is_current' => true,
        ]);

        FeeStructureCarryForwardService::carryForward($this->school->id, $this->session2026->id, $session2027->id);

        $miscFee = MiscFee::where('academic_session_id', $session2027->id)->first();
        $this->assertNotNull($miscFee);
        $this->assertEquals('science fair', $miscFee->name);
        $this->assertEquals('School Fee', $miscFee->fee_head_name);
        $this->assertEquals(500.00, (float) $miscFee->amount);
    }

    /**
     * TEST 8: Verify Fee Fine carry-forward and component link remapping.
     */
    public function test_test_8_fee_fine_carry_forward_and_component_remapping()
    {
        $this->setup2026FeeStructure();

        $session2027 = AcademicSession::create([
            'school_id' => $this->school->id,
            'name' => 'Apr 2027 - Mar 2028',
            'start_date' => '2027-04-01',
            'end_date' => '2028-03-31',
            'is_current' => true,
        ]);

        FeeStructureCarryForwardService::carryForward($this->school->id, $this->session2026->id, $session2027->id);

        $clonedFine = FeeFine::where('academic_session_id', $session2027->id)->first();
        $this->assertNotNull($clonedFine);
        $this->assertEquals('Late Fee', $clonedFine->name);
        $this->assertEquals('Fixed Amount', $clonedFine->fine_type);
        $this->assertEquals(100.00, (float) $clonedFine->fine_amount);
        $this->assertTrue($clonedFine->status);

        $clonedTuition = FeeComponent::where('academic_session_id', $session2027->id)->where('component_name', 'Tution Fee')->first();
        $this->assertEquals($clonedTuition->id, $clonedFine->fee_component_id);

        $clonedSched = FeeSchedule::where('academic_session_id', $session2027->id)->first();
        $this->assertEquals($clonedFine->id, $clonedSched->fine_id);
    }

    /**
     * TEST 9, 10, 11: Verify class, section, and student mappings preservation.
     */
    public function test_test_9_10_11_mappings_preservation()
    {
        $this->setup2026FeeStructure();

        $session2027 = AcademicSession::create([
            'school_id' => $this->school->id,
            'name' => 'Apr 2027 - Mar 2028',
            'start_date' => '2027-04-01',
            'end_date' => '2028-03-31',
            'is_current' => true,
        ]);

        FeeStructureCarryForwardService::carryForward($this->school->id, $this->session2026->id, $session2027->id);

        $clonedSched = FeeSchedule::where('academic_session_id', $session2027->id)->first();
        $this->assertEquals('Class-1, Class-2', $clonedSched->classes);
        $this->assertEquals('Class-1-A, Class-2-B', $clonedSched->sections);

        $clonedAlan = FeeDiscount::where('academic_session_id', $session2027->id)->where('name', 'Alan Walker')->first();
        $this->assertEquals(json_encode([(string) $this->studentJohn->id]), $clonedAlan->student_ids);

        $clonedCw = ClassWiseFee::where('academic_session_id', $session2027->id)->first();
        $this->assertEquals($this->class1->id, $clonedCw->class_id);
        $this->assertEquals($this->sectionA->id, $clonedCw->section_id);
        $this->assertEquals($this->dayBoarding->id, $clonedCw->student_category_id);
        $this->assertEquals($clonedSched->id, $clonedCw->fee_schedule_id);
    }

    /**
     * TEST 12: Modify something in 2027-2028 and confirm that 2026-2027 remains unchanged.
     */
    public function test_test_12_modify_2027_2028_leaves_2026_2027_unchanged()
    {
        $this->setup2026FeeStructure();

        $session2027 = AcademicSession::create([
            'school_id' => $this->school->id,
            'name' => 'Apr 2027 - Mar 2028',
            'start_date' => '2027-04-01',
            'end_date' => '2028-03-31',
            'is_current' => true,
        ]);

        FeeStructureCarryForwardService::carryForward($this->school->id, $this->session2026->id, $session2027->id);

        // Edit Tuition Fee in 2027-2028
        $comp2027 = FeeComponent::where('academic_session_id', $session2027->id)->where('component_name', 'Tution Fee')->first();
        $comp2027->update(['component_name' => 'Tuition Fee Senior', 'head_name' => 'Senior Fee']);

        // Check 2026-2027
        $comp2026 = FeeComponent::where('academic_session_id', $this->session2026->id)->where('component_name', 'Tution Fee')->first();
        $this->assertNotNull($comp2026);
        $this->assertEquals('School Fee', $comp2026->head_name);
        $this->assertEquals('Tution Fee', $comp2026->component_name);

        // Add a new discount only to 2027-2028
        FeeDiscount::create([
            'school_id' => $this->school->id,
            'academic_session_id' => $session2027->id,
            'name' => 'Sibling Discount 2027',
            'amount' => 500.00,
            'type' => 'flat',
        ]);

        $this->assertEquals(2, FeeDiscount::where('academic_session_id', $this->session2026->id)->count());
        $this->assertEquals(3, FeeDiscount::where('academic_session_id', $session2027->id)->count());
    }

    /**
     * TEST 13: Modify something in 2026-2027 and confirm that 2027-2028 remains unchanged.
     */
    public function test_test_13_modify_2026_2027_leaves_2027_2028_unchanged()
    {
        $this->setup2026FeeStructure();

        $session2027 = AcademicSession::create([
            'school_id' => $this->school->id,
            'name' => 'Apr 2027 - Mar 2028',
            'start_date' => '2027-04-01',
            'end_date' => '2028-03-31',
            'is_current' => true,
        ]);

        FeeStructureCarryForwardService::carryForward($this->school->id, $this->session2026->id, $session2027->id);

        // Edit Misc Fee in 2026-2027
        $misc2026 = MiscFee::where('academic_session_id', $this->session2026->id)->first();
        $misc2026->update(['amount' => 800.00]);

        // Confirm 2027-2028 remains at 500.00
        $misc2027 = MiscFee::where('academic_session_id', $session2027->id)->first();
        $this->assertEquals(500.00, (float) $misc2027->amount);
    }

    /**
     * TEST 14: Add a new Fee Structure item to 2026-2027 and verify that when the next academic year is generated, that new configuration is also carried forward.
     */
    public function test_test_14_newly_added_items_are_carried_forward_on_subsequent_generation()
    {
        $this->setup2026FeeStructure();

        // Administrator later adds Schedule-2 and a new Fee Component to 2026-2027
        $sched2 = FeeSchedule::create([
            'school_id' => $this->school->id,
            'academic_session_id' => $this->session2026->id,
            'classes' => 'Class-2',
            'no_of_installments' => 2,
            'name' => 'Schedule-2',
            'start_date' => '2026-04-01',
            'end_date' => '2027-03-31',
            'installment_type' => 'custom',
            'installments' => [
                ['installment_no' => 1, 'name' => 'Installment 1', 'start_date' => '2026-04-01', 'end_date' => '2026-09-30', 'due_date' => '2026-09-30', 'grace_days' => 5],
                ['installment_no' => 2, 'name' => 'Installment 2', 'start_date' => '2026-10-01', 'end_date' => '2027-03-31', 'due_date' => '2027-03-31', 'grace_days' => 5],
            ],
        ]);

        $compLab = FeeComponent::create([
            'school_id' => $this->school->id,
            'academic_session_id' => $this->session2026->id,
            'fee_schedule_id' => $sched2->id,
            'head_name' => 'Practical Fee',
            'component_name' => 'Robotics Lab',
            'admission_type' => 'All Students',
            'gender' => 'All Students',
        ]);

        // Now create 2027-2028
        $session2027 = AcademicSession::create([
            'school_id' => $this->school->id,
            'name' => 'Apr 2027 - Mar 2028',
            'start_date' => '2027-04-01',
            'end_date' => '2028-03-31',
            'is_current' => true,
        ]);

        FeeStructureCarryForwardService::carryForward($this->school->id, $this->session2026->id, $session2027->id);

        $this->assertEquals(2, FeeSchedule::where('academic_session_id', $session2027->id)->count());
        $this->assertEquals(6, FeeComponent::where('academic_session_id', $session2027->id)->count());

        $clonedSched2 = FeeSchedule::where('academic_session_id', $session2027->id)->where('name', 'Schedule-2')->first();
        $this->assertNotNull($clonedSched2);

        $clonedLab = FeeComponent::where('academic_session_id', $session2027->id)->where('component_name', 'Robotics Lab')->first();
        $this->assertNotNull($clonedLab);
        $this->assertEquals($clonedSched2->id, $clonedLab->fee_schedule_id);
    }

    /**
     * TEST 15: Verify that no duplicate Fee Structure records are created on re-invocations.
     */
    public function test_test_15_no_duplicate_records_on_re_invocations()
    {
        $this->setup2026FeeStructure();

        $session2027 = AcademicSession::create([
            'school_id' => $this->school->id,
            'name' => 'Apr 2027 - Mar 2028',
            'start_date' => '2027-04-01',
            'end_date' => '2028-03-31',
            'is_current' => true,
        ]);

        // First carry forward
        $result1 = FeeStructureCarryForwardService::carryForward($this->school->id, $this->session2026->id, $session2027->id);
        $this->assertTrue($result1['success']);

        $schedulesCount1 = FeeSchedule::where('academic_session_id', $session2027->id)->count();
        $componentsCount1 = FeeComponent::where('academic_session_id', $session2027->id)->count();

        // Second carry forward attempt without force
        $result2 = FeeStructureCarryForwardService::carryForward($this->school->id, $this->session2026->id, $session2027->id);
        $this->assertTrue($result2['success']);

        $schedulesCount2 = FeeSchedule::where('academic_session_id', $session2027->id)->count();
        $componentsCount2 = FeeComponent::where('academic_session_id', $session2027->id)->count();

        $this->assertEquals($schedulesCount1, $schedulesCount2);
        $this->assertEquals($componentsCount1, $componentsCount2);
    }

    /**
     * TEST 16: Verify that existing fee collection/payment data is NOT modified because of this change.
     */
    public function test_test_16_existing_fee_collection_records_unaffected()
    {
        $data = $this->setup2026FeeStructure();

        $category = FeeCategory::firstOrCreate(
            ['school_id' => $this->school->id, 'name' => 'Tuition'],
            ['description' => 'Tuition Fees']
        );

        // Create student fee and receipt in 2026-2027
        $sf = StudentFee::create([
            'school_id' => $this->school->id,
            'student_id' => $this->studentJohn->id,
            'academic_session_id' => $this->session2026->id,
            'fee_category_id' => $category->id,
            'fee_component_id' => $data['compTuition']->id,
            'fee_schedule_id' => $data['schedule']->id,
            'installment_no' => 1,
            'amount' => 3000.00,
            'paid_amount' => 3000.00,
            'status' => 'Paid',
            'due_date' => '2026-06-30',
        ]);

        $receipt = FeeReceipt::create([
            'school_id' => $this->school->id,
            'student_id' => $this->studentJohn->id,
            'receipt_number' => 'REC-TEST-001',
            'amount_paid' => 3000.00,
            'payment_mode' => 'Cash',
            'payment_date' => '2026-05-10',
            'status' => 'Paid',
        ]);

        // Carry forward to 2027-2028
        $session2027 = AcademicSession::create([
            'school_id' => $this->school->id,
            'name' => 'Apr 2027 - Mar 2028',
            'start_date' => '2027-04-01',
            'end_date' => '2028-03-31',
            'is_current' => true,
        ]);

        FeeStructureCarryForwardService::carryForward($this->school->id, $this->session2026->id, $session2027->id);

        $sfReloaded = StudentFee::find($sf->id);
        $this->assertEquals(3000.00, (float) $sfReloaded->paid_amount);
        $this->assertEquals('Paid', $sfReloaded->status);
        $this->assertEquals($data['compTuition']->id, $sfReloaded->fee_component_id);

        $receiptReloaded = FeeReceipt::find($receipt->id);
        $this->assertEquals('REC-TEST-001', $receiptReloaded->receipt_number);
        $this->assertEquals(3000.00, (float) $receiptReloaded->amount_paid);
    }

    /**
     * TEST 17: Verify that existing Previous Academic Year Due / Carry Forward Due functionality remains unaffected.
     */
    public function test_test_17_previous_academic_year_due_functionality_remains_intact()
    {
        $data = $this->setup2026FeeStructure();

        $category = FeeCategory::firstOrCreate(
            ['school_id' => $this->school->id, 'name' => 'Tuition'],
            ['description' => 'Tuition Fees']
        );

        // Student has an unpaid fee in 2026-2027
        $unpaidFee = StudentFee::create([
            'school_id' => $this->school->id,
            'student_id' => $this->studentJohn->id,
            'academic_session_id' => $this->session2026->id,
            'fee_category_id' => $category->id,
            'fee_component_id' => $data['compTuition']->id,
            'fee_schedule_id' => $data['schedule']->id,
            'installment_no' => 1,
            'amount' => 3000.00,
            'paid_amount' => 1000.00,
            'status' => 'Partial',
            'due_date' => '2026-06-30',
        ]);

        // Create 2027-2028 session & carry forward fee structure
        $session2027 = AcademicSession::create([
            'school_id' => $this->school->id,
            'name' => 'Apr 2027 - Mar 2028',
            'start_date' => '2027-04-01',
            'end_date' => '2028-03-31',
            'is_current' => true,
        ]);

        FeeStructureCarryForwardService::carryForward($this->school->id, $this->session2026->id, $session2027->id);

        // Check that 2026-2027 due balance calculation for studentJohn remains exactly 2000.00
        $due2026 = StudentFee::where('school_id', $this->school->id)
            ->where('student_id', $this->studentJohn->id)
            ->where('fee_schedule_id', $data['schedule']->id)
            ->selectRaw('SUM(amount - paid_amount) as total_due')
            ->value('total_due');

        $this->assertEquals(2000.00, (float) $due2026);
    }
}
