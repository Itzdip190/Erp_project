<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\FeeCategory;
use App\Models\FeeComponent;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeeDiscountValidationTest extends TestCase
{
    use RefreshDatabase;

    protected User $schoolAdmin;
    protected Student $student;
    protected AcademicSession $session;
    protected FeeCategory $category;
    protected FeeComponent $component;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed the database
        $this->seed(DatabaseSeeder::class);

        $this->schoolAdmin = User::where('email', 'admin@yis.com')->first();
        $this->student = Student::where('school_id', $this->schoolAdmin->school_id)->first();
        $this->session = AcademicSession::where('school_id', $this->schoolAdmin->school_id)->where('is_current', true)->first()
            ?? AcademicSession::where('school_id', $this->schoolAdmin->school_id)->first();

        $this->category = FeeCategory::firstOrCreate(
            ['school_id' => $this->schoolAdmin->school_id, 'name' => 'Tuition'],
            ['description' => 'Tuition fees']
        );

        $this->component = FeeComponent::create([
            'school_id' => $this->schoolAdmin->school_id,
            'academic_session_id' => $this->session->id,
            'head_name' => 'Tuition Fee',
            'component_name' => 'Tuition Fee',
        ]);
    }

    /**
     * Test validation blocks payment and discount when component has ₹0 payable in any selected installment.
     */
    public function test_validation_blocks_discount_when_component_has_zero_payable_amount_in_selected_installment(): void
    {
        // Create fee record 1 with payable amount > 0 (e.g. ₹1000)
        $fee1 = StudentFee::create([
            'school_id' => $this->schoolAdmin->school_id,
            'student_id' => $this->student->id,
            'fee_category_id' => $this->category->id,
            'fee_component_id' => $this->component->id,
            'installment_no' => 1,
            'amount' => 1000.00,
            'paid_amount' => 0.00,
            'due_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        // Create fee record 2 with payable amount = 0 (₹0 amount)
        $fee2 = StudentFee::create([
            'school_id' => $this->schoolAdmin->school_id,
            'student_id' => $this->student->id,
            'fee_category_id' => $this->category->id,
            'fee_component_id' => $this->component->id,
            'installment_no' => 2,
            'amount' => 0.00,
            'paid_amount' => 0.00,
            'due_date' => now()->addMonth()->toDateString(),
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->post('/school/fees/student-wise', [
                'action' => 'mark_paid',
                'student_id' => $this->student->id,
                'installment_no' => 999, // Combined payment
                'student_fee_ids' => "{$fee1->id},{$fee2->id}",
                'amount_paid' => 1000.00,
                'payment_mode' => 'cash',
                'instant_discount_amount' => 10.00,
                'instant_discount_type' => 'percentage',
                'discount_fee_component_ids' => [$this->component->id],
                'discount_installment_nos' => [1, 2],
            ]);

        $response->assertSessionHasErrors(['discount_fee_component_ids']);
        
        $errors = session('errors')->get('discount_fee_component_ids');
        $this->assertCount(1, $errors);
        $this->assertEquals(
            "Tuition Fee cannot receive an instant discount in Installment 2 because its payable amount is ₹0. Please remove the Tuition Fee component for Installment 2 or select another applicable installment.",
            $errors[0]
        );
    }

    /**
     * Test validation passes when selected components have payable amounts.
     */
    public function test_validation_passes_when_selected_components_have_payable_amounts(): void
    {
        $fee1 = StudentFee::create([
            'school_id' => $this->schoolAdmin->school_id,
            'student_id' => $this->student->id,
            'fee_category_id' => $this->category->id,
            'fee_component_id' => $this->component->id,
            'installment_no' => 1,
            'amount' => 1000.00,
            'paid_amount' => 0.00,
            'due_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        $fee2 = StudentFee::create([
            'school_id' => $this->schoolAdmin->school_id,
            'student_id' => $this->student->id,
            'fee_category_id' => $this->category->id,
            'fee_component_id' => $this->component->id,
            'installment_no' => 2,
            'amount' => 2500.00,
            'paid_amount' => 0.00,
            'due_date' => now()->addMonth()->toDateString(),
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->post('/school/fees/student-wise', [
                'action' => 'mark_paid',
                'student_id' => $this->student->id,
                'installment_no' => 999, // Combined payment
                'student_fee_ids' => "{$fee1->id},{$fee2->id}",
                'amount_paid' => 3000.00, // Partial or full payment
                'payment_mode' => 'cash',
                'instant_discount_amount' => 10.00,
                'instant_discount_type' => 'percentage',
                'discount_fee_component_ids' => [$this->component->id],
                'discount_installment_nos' => [1, 2],
            ]);

        $response->assertSessionHasNoErrors();
    }

    /**
     * Test payment proceeds normally when no fee components are selected for discount.
     */
    public function test_payment_proceeds_when_no_components_are_selected_for_discount(): void
    {
        $fee1 = StudentFee::create([
            'school_id' => $this->schoolAdmin->school_id,
            'student_id' => $this->student->id,
            'fee_category_id' => $this->category->id,
            'fee_component_id' => $this->component->id,
            'installment_no' => 1,
            'amount' => 1000.00,
            'paid_amount' => 0.00,
            'due_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->post('/school/fees/student-wise', [
                'action' => 'mark_paid',
                'student_id' => $this->student->id,
                'installment_no' => 1,
                'amount_paid' => 1000.00,
                'payment_mode' => 'cash',
            ]);

        $response->assertSessionHasNoErrors();
    }

    /**
     * Test suggested amount and remaining due calculations match the screenshot.
     */
    public function test_suggested_amount_and_remaining_due_calculation_with_component_discount_using_screenshot_numbers(): void
    {
        // Setup Fee Components
        $admissionComp = FeeComponent::create([
            'school_id' => $this->schoolAdmin->school_id,
            'academic_session_id' => $this->session->id,
            'head_name' => 'Admission Fee',
            'component_name' => 'Admission Fee',
        ]);
        
        $smartClassComp = FeeComponent::create([
            'school_id' => $this->schoolAdmin->school_id,
            'academic_session_id' => $this->session->id,
            'head_name' => 'smart class',
            'component_name' => 'smart class',
        ]);

        $idCardComp = FeeComponent::create([
            'school_id' => $this->schoolAdmin->school_id,
            'academic_session_id' => $this->session->id,
            'head_name' => 'ID card/Diary Fee',
            'component_name' => 'ID card/Diary Fee',
        ]);

        // Create Installment 1 Fees
        $fees = [];
        
        // Admission Fee (10,000)
        $fees[] = StudentFee::create([
            'school_id' => $this->schoolAdmin->school_id,
            'student_id' => $this->student->id,
            'fee_category_id' => $this->category->id,
            'fee_component_id' => $admissionComp->id,
            'installment_no' => 1,
            'amount' => 10000.00,
            'paid_amount' => 0.00,
            'due_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        // Tuition Fee (2500 + 100 fine = 2600 due)
        $fees[] = StudentFee::create([
            'school_id' => $this->schoolAdmin->school_id,
            'student_id' => $this->student->id,
            'fee_category_id' => $this->category->id,
            'fee_component_id' => $this->component->id,
            'installment_no' => 1,
            'amount' => 2500.00,
            'fine_amount_applied' => 100.00,
            'paid_amount' => 0.00,
            'due_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        // smart class (200)
        $fees[] = StudentFee::create([
            'school_id' => $this->schoolAdmin->school_id,
            'student_id' => $this->student->id,
            'fee_category_id' => $this->category->id,
            'fee_component_id' => $smartClassComp->id,
            'installment_no' => 1,
            'amount' => 200.00,
            'paid_amount' => 0.00,
            'due_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        // ID card/Diary Fee (250)
        $fees[] = StudentFee::create([
            'school_id' => $this->schoolAdmin->school_id,
            'student_id' => $this->student->id,
            'fee_category_id' => $this->category->id,
            'fee_component_id' => $idCardComp->id,
            'installment_no' => 1,
            'amount' => 250.00,
            'paid_amount' => 0.00,
            'due_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        // Create Installment 2 Fees
        // Admission Fee (0)
        $fees[] = StudentFee::create([
            'school_id' => $this->schoolAdmin->school_id,
            'student_id' => $this->student->id,
            'fee_category_id' => $this->category->id,
            'fee_component_id' => $admissionComp->id,
            'installment_no' => 2,
            'amount' => 0.00,
            'paid_amount' => 0.00,
            'due_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        // Tuition Fee (2500 + 100 fine = 2600 due)
        $fees[] = StudentFee::create([
            'school_id' => $this->schoolAdmin->school_id,
            'student_id' => $this->student->id,
            'fee_category_id' => $this->category->id,
            'fee_component_id' => $this->component->id,
            'installment_no' => 2,
            'amount' => 2500.00,
            'fine_amount_applied' => 100.00,
            'paid_amount' => 0.00,
            'due_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        // smart class (200)
        $fees[] = StudentFee::create([
            'school_id' => $this->schoolAdmin->school_id,
            'student_id' => $this->student->id,
            'fee_category_id' => $this->category->id,
            'fee_component_id' => $smartClassComp->id,
            'installment_no' => 2,
            'amount' => 200.00,
            'paid_amount' => 0.00,
            'due_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        // ID card/Diary Fee (0)
        $fees[] = StudentFee::create([
            'school_id' => $this->schoolAdmin->school_id,
            'student_id' => $this->student->id,
            'fee_category_id' => $this->category->id,
            'fee_component_id' => $idCardComp->id,
            'installment_no' => 2,
            'amount' => 0.00,
            'paid_amount' => 0.00,
            'due_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        $feeIdsStr = implode(',', array_map(fn($f) => $f->id, $fees));

        $response = $this->actingAs($this->schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->postJson('/school/fees/student-wise', [
                'action' => 'calculate_discount',
                'student_id' => $this->student->id,
                'installment_no' => 999, // Combined payment
                'student_fee_ids' => $feeIdsStr,
                'amount_paid' => 13450.00,
                'instant_discount_amount' => 11.00,
                'instant_discount_type' => 'percentage',
                'discount_fee_component_ids' => [$this->component->id],
                'discount_installment_nos' => [1, 2],
            ]);

        $response->assertStatus(200);
        $data = $response->json();

        $this->assertTrue($data['success']);
        
        // Eligible Component Amount = Tuition Fee Inst 1 (2500 tuition) + Tuition Fee Inst 2 (2500 tuition) = 5000 (fines are excluded)
        $this->assertEquals(5000.00, $data['eligible_amount']);
        
        // Discount Amount = 11% of 5000 = 550
        $this->assertEquals(550.00, $data['discount_amount']);
        
        // Total Due = 15850
        $this->assertEquals(15850.00, $data['total_due']);
        
        // Suggested Amount = Total Due - Discount = 15850 - 550 = 15300
        $this->assertEquals(15300.00, $data['suggested_amount']);
        
        // Remaining Due = Total Due - Discount - Amount Paid = 15850 - 550 - 13450 = 1850
        $this->assertEquals(1850.00, $data['remaining_due']);
    }

    /**
     * Test regression scenarios for instant discount calculations excluding fine.
     */
    public function test_instant_discount_regression_scenarios_excluding_fine(): void
    {
        // Setup Fee Components
        $admissionComp = FeeComponent::create([
            'school_id' => $this->schoolAdmin->school_id,
            'academic_session_id' => $this->session->id,
            'head_name' => 'Admission Fee',
            'component_name' => 'Admission Fee',
        ]);
        
        $smartClassComp = FeeComponent::create([
            'school_id' => $this->schoolAdmin->school_id,
            'academic_session_id' => $this->session->id,
            'head_name' => 'smart class',
            'component_name' => 'smart class',
        ]);

        $idCardComp = FeeComponent::create([
            'school_id' => $this->schoolAdmin->school_id,
            'academic_session_id' => $this->session->id,
            'head_name' => 'ID card/Diary Fee',
            'component_name' => 'ID card/Diary Fee',
        ]);

        // Create Installment 1 Fees
        $fees = [];
        
        // Admission Fee (10,000)
        $fees[] = StudentFee::create([
            'school_id' => $this->schoolAdmin->school_id,
            'student_id' => $this->student->id,
            'fee_category_id' => $this->category->id,
            'fee_component_id' => $admissionComp->id,
            'installment_no' => 1,
            'amount' => 10000.00,
            'paid_amount' => 0.00,
            'due_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        // Tuition Fee (2500 + 100 fine = 2600 due)
        $fees[] = StudentFee::create([
            'school_id' => $this->schoolAdmin->school_id,
            'student_id' => $this->student->id,
            'fee_category_id' => $this->category->id,
            'fee_component_id' => $this->component->id,
            'installment_no' => 1,
            'amount' => 2500.00,
            'fine_amount_applied' => 100.00,
            'paid_amount' => 0.00,
            'due_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        // smart class (200)
        $fees[] = StudentFee::create([
            'school_id' => $this->schoolAdmin->school_id,
            'student_id' => $this->student->id,
            'fee_category_id' => $this->category->id,
            'fee_component_id' => $smartClassComp->id,
            'installment_no' => 1,
            'amount' => 200.00,
            'paid_amount' => 0.00,
            'due_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        // ID card/Diary Fee (250)
        $fees[] = StudentFee::create([
            'school_id' => $this->schoolAdmin->school_id,
            'student_id' => $this->student->id,
            'fee_category_id' => $this->category->id,
            'fee_component_id' => $idCardComp->id,
            'installment_no' => 1,
            'amount' => 250.00,
            'paid_amount' => 0.00,
            'due_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        // Create Installment 2 Fees
        // Tuition Fee (2500 + 100 fine = 2600 due)
        $fees[] = StudentFee::create([
            'school_id' => $this->schoolAdmin->school_id,
            'student_id' => $this->student->id,
            'fee_category_id' => $this->category->id,
            'fee_component_id' => $this->component->id,
            'installment_no' => 2,
            'amount' => 2500.00,
            'fine_amount_applied' => 100.00,
            'paid_amount' => 0.00,
            'due_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        // smart class (200)
        $fees[] = StudentFee::create([
            'school_id' => $this->schoolAdmin->school_id,
            'student_id' => $this->student->id,
            'fee_category_id' => $this->category->id,
            'fee_component_id' => $smartClassComp->id,
            'installment_no' => 2,
            'amount' => 200.00,
            'paid_amount' => 0.00,
            'due_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        $feeIdsStr = implode(',', array_map(fn($f) => $f->id, $fees));

        // Scenario 1: No component selected. 10% Discount should ignore the ₹200 fine.
        // Discountable amount: (10000 + 2500 + 200 + 250 + 2500 + 200) = 15650.
        // 10% of 15650 = 1565.
        // Suggested amount to collect: 15850 - 1565 = 14285.
        $response1 = $this->actingAs($this->schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->postJson('/school/fees/student-wise', [
                'action' => 'calculate_discount',
                'student_id' => $this->student->id,
                'installment_no' => 999,
                'student_fee_ids' => $feeIdsStr,
                'amount_paid' => 0.00,
                'instant_discount_amount' => 10.00,
                'instant_discount_type' => 'percentage',
                'discount_fee_component_ids' => [],
                'discount_installment_nos' => [],
            ]);

        $response1->assertStatus(200);
        $data1 = $response1->json();
        $this->assertEquals(15650.00, $data1['eligible_amount']);
        $this->assertEquals(1565.00, $data1['discount_amount']);
        $this->assertEquals(14285.00, $data1['suggested_amount']);

        // Scenario 2: Component selected, Tuition Fee contains ₹100 fine in Installment 1 and ₹100 fine in Installment 2.
        // If we select Tuition Fee and Installment 1:
        // Eligible amount should be Tuition Fee Inst 1 base amount: ₹2500.
        // 10% discount should be ₹250.
        // Suggested amount: 15850 - 250 = 15600.
        $response2 = $this->actingAs($this->schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->postJson('/school/fees/student-wise', [
                'action' => 'calculate_discount',
                'student_id' => $this->student->id,
                'installment_no' => 999,
                'student_fee_ids' => $feeIdsStr,
                'amount_paid' => 0.00,
                'instant_discount_amount' => 10.00,
                'instant_discount_type' => 'percentage',
                'discount_fee_component_ids' => [$this->component->id],
                'discount_installment_nos' => [1],
            ]);

        $response2->assertStatus(200);
        $data2 = $response2->json();
        $this->assertEquals(2500.00, $data2['eligible_amount']);
        $this->assertEquals(250.00, $data2['discount_amount']);
        $this->assertEquals(15600.00, $data2['suggested_amount']);

        // Scenario 3: Flat Discount. Flat discount of ₹3,000 on selected components.
        // Selected components: Tuition Fee and Admission Fee, Installment 1.
        // Total component due = 10000 (Admission) + 2600 (Tuition Inst 1) = 12600.
        // Total component fine = 100.
        // Discountable component amount = 12500.
        // Flat discount is ₹3,000. It is fully covered by 12500.
        // Discount amount: 3000.
        // Suggested amount: 15850 - 3000 = 12850.
        $response3 = $this->actingAs($this->schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->postJson('/school/fees/student-wise', [
                'action' => 'calculate_discount',
                'student_id' => $this->student->id,
                'installment_no' => 999,
                'student_fee_ids' => $feeIdsStr,
                'amount_paid' => 0.00,
                'instant_discount_amount' => 3000.00,
                'instant_discount_type' => 'flat',
                'discount_fee_component_ids' => [$this->component->id, $admissionComp->id],
                'discount_installment_nos' => [1],
            ]);

        $response3->assertStatus(200);
        $data3 = $response3->json();
        $this->assertEquals(12500.00, $data3['eligible_amount']);
        $this->assertEquals(3000.00, $data3['discount_amount']);
        $this->assertEquals(12850.00, $data3['suggested_amount']);

        // Scenario 4: Flat Discount exceeding base due must be capped at base due (fine remains fully payable).
        // Let's select only Tuition Fee Installment 1 (due 2600, fine 100, base due 2500).
        // Apply flat discount of 3000.
        // Capped discount should be 2500.
        // Suggested amount: 15850 - 2500 = 13350.
        $response4 = $this->actingAs($this->schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->postJson('/school/fees/student-wise', [
                'action' => 'calculate_discount',
                'student_id' => $this->student->id,
                'installment_no' => 999,
                'student_fee_ids' => $feeIdsStr,
                'amount_paid' => 0.00,
                'instant_discount_amount' => 3000.00,
                'instant_discount_type' => 'flat',
                'discount_fee_component_ids' => [$this->component->id],
                'discount_installment_nos' => [1],
            ]);

        $response4->assertStatus(200);
        $data4 = $response4->json();
        $this->assertEquals(2500.00, $data4['eligible_amount']);
        $this->assertEquals(2500.00, $data4['discount_amount']);
        $this->assertEquals(13350.00, $data4['suggested_amount']);

        // Scenario 5: Mark Paid with flat discount to verify allocation.
        // Let's submit mark_paid with flat discount of ₹2500 on Tuition Fee Inst 1.
        // Suggested amount is 13350. We pay Suggested Amount.
        // All fees should be fully cleared since we paid the suggested amount.
        $response5 = $this->actingAs($this->schoolAdmin)
            ->withHeaders(['X-School-Code' => 'YIS2024'])
            ->post('/school/fees/student-wise', [
                'action' => 'mark_paid',
                'student_id' => $this->student->id,
                'installment_no' => 999,
                'student_fee_ids' => $feeIdsStr,
                'amount_paid' => 13350.00,
                'payment_mode' => 'cash',
                'instant_discount_amount' => 2500.00,
                'instant_discount_type' => 'flat',
                'discount_fee_component_ids' => [$this->component->id],
                'discount_installment_nos' => [1],
            ]);

        $response5->assertSessionHasNoErrors();
        
        // Retrieve tuition record and verify instant_discount_amount is 2500 and status is paid
        $tuitionFeeRecord = StudentFee::find($fees[1]->id); // Tuition Inst 1
        $this->assertEquals(2500.00, floatval($tuitionFeeRecord->instant_discount_amount));
        $this->assertEquals(100.00, floatval($tuitionFeeRecord->paid_amount)); // the fine was paid using Cash
        $this->assertEquals('paid', $tuitionFeeRecord->status);
    }

    /**
     * Test existing fee discounts (syncStudentDiscounts) logic to ensure fine is NEVER discounted.
     */
    public function test_sync_student_discounts_excludes_late_fine_from_existing_fee_discounts(): void
    {
        // 1. Create a FeeDiscount for Female students (10% on Tuition Fee)
        $femaleStudent = $this->student->replicate();
        $femaleStudent->admission_number = 'FEM-1001';
        $femaleStudent->admission_sequence = 9001;
        $femaleStudent->gender = 'Female';
        $femaleStudent->save();

        $discount = \App\Models\FeeDiscount::create([
            'school_id' => $this->schoolAdmin->school_id,
            'academic_session_id' => $this->session->id,
            'name' => 'Female Discount',
            'amount' => 10.00,
            'type' => 'percentage',
            'target_group' => 'Female',
            'fee_component_ids' => json_encode([$this->component->id]),
        ]);

        // Scenario 1: Tuition Fee = ₹2,500, Fine = ₹100, Existing Discount = 10%
        // Expected Discount: ₹250 (10% of 2500, NOT 10% of 2600)
        // Expected Due: ₹2,350
        $fee1 = StudentFee::create([
            'school_id' => $this->schoolAdmin->school_id,
            'student_id' => $femaleStudent->id,
            'fee_category_id' => $this->category->id,
            'fee_component_id' => $this->component->id,
            'installment_no' => 1,
            'amount' => 2500.00,
            'fine_amount_applied' => 100.00,
            'paid_amount' => 0.00,
            'due_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        \App\Http\Controllers\School\FeeManagementController::syncStudentDiscounts($femaleStudent, $this->session->id);

        $fee1->refresh();
        $this->assertEquals(250.00, floatval($fee1->instant_discount_amount));
        $this->assertEquals(2350.00, floatval($fee1->remaining_due));

        // Scenario 2: Tuition Fee = ₹2,500, Fine = ₹0, Existing Discount = 10%
        // Expected Discount: ₹250, Expected Due: ₹2,250
        $femaleStudent2 = $this->student->replicate();
        $femaleStudent2->admission_number = 'FEM-1002';
        $femaleStudent2->admission_sequence = 9002;
        $femaleStudent2->gender = 'Female';
        $femaleStudent2->save();

        $fee2 = StudentFee::create([
            'school_id' => $this->schoolAdmin->school_id,
            'student_id' => $femaleStudent2->id,
            'fee_category_id' => $this->category->id,
            'fee_component_id' => $this->component->id,
            'installment_no' => 1,
            'amount' => 2500.00,
            'fine_amount_applied' => 0.00,
            'paid_amount' => 0.00,
            'due_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        \App\Http\Controllers\School\FeeManagementController::syncStudentDiscounts($femaleStudent2, $this->session->id);

        $fee2->refresh();
        $this->assertEquals(250.00, floatval($fee2->instant_discount_amount));
        $this->assertEquals(2250.00, floatval($fee2->remaining_due));

        // Scenario 3: Fine exists on multiple installments. Discount must ignore every Fine amount.
        $feeInst2 = StudentFee::create([
            'school_id' => $this->schoolAdmin->school_id,
            'student_id' => $femaleStudent->id,
            'fee_category_id' => $this->category->id,
            'fee_component_id' => $this->component->id,
            'installment_no' => 2,
            'amount' => 2500.00,
            'fine_amount_applied' => 150.00,
            'paid_amount' => 0.00,
            'due_date' => now()->addMonth()->toDateString(),
            'status' => 'pending',
        ]);

        \App\Http\Controllers\School\FeeManagementController::syncStudentDiscounts($femaleStudent, $this->session->id);

        $feeInst2->refresh();
        $this->assertEquals(250.00, floatval($feeInst2->instant_discount_amount));
        $this->assertEquals(2400.00, floatval($feeInst2->remaining_due)); // 2500 + 150 - 250 = 2400
    }
}
