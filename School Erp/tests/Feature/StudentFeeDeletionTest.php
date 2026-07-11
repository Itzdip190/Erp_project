<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\School;
use App\Models\User;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\FeeCategory;
use App\Models\FeeComponent;
use App\Models\FeeSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentFeeDeletionTest extends TestCase
{
    use RefreshDatabase;

    protected $schoolAdmin;
    protected $student;
    protected $session;
    protected $category;
    protected $component;
    protected $schedule;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        
        $this->schoolAdmin = User::where('email', 'admin@yis.com')->first();
        $this->student = Student::where('school_id', $this->schoolAdmin->school_id)->first();
        $this->session = AcademicSession::where('school_id', $this->schoolAdmin->school_id)->first();

        $this->category = FeeCategory::create([
            'school_id' => $this->schoolAdmin->school_id,
            'name' => 'Tuition',
            'description' => 'Tuition Fees',
        ]);

        $this->component = FeeComponent::create([
            'school_id' => $this->schoolAdmin->school_id,
            'academic_session_id' => $this->session->id,
            'fee_category_id' => $this->category->id,
            'component_name' => 'Monthly Tuition',
            'head_name' => 'Tuition',
            'admission_type' => 'All Students',
            'gender' => 'All Students',
        ]);

        $this->schedule = FeeSchedule::create([
            'school_id' => $this->schoolAdmin->school_id,
            'academic_session_id' => $this->session->id,
            'name' => 'Tuition Schedule',
            'classes' => 'Class 1',
            'installment_type' => 'custom',
            'start_date' => $this->session->start_date->toDateString(),
            'end_date' => $this->session->end_date->toDateString(),
            'installments' => [
                [
                    'installment_no' => 1,
                    'name' => 'Installment 1',
                    'start_date' => '2026-04-01',
                    'end_date' => '2026-04-30',
                    'due_date' => '2026-04-30',
                    'grace_days' => 5,
                ]
            ]
        ]);
    }

    public function test_delete_student_fee_blocked_if_fine_applied(): void
    {
        $this->actingAs($this->schoolAdmin);

        // Student Fee with fine applied
        $finedFee = StudentFee::create([
            'school_id' => $this->schoolAdmin->school_id,
            'student_id' => $this->student->id,
            'fee_category_id' => $this->category->id,
            'fee_schedule_id' => $this->schedule->id,
            'fee_component_id' => $this->component->id,
            'installment_no' => 1,
            'amount' => 1150.00,
            'due_date' => '2026-04-30',
            'status' => 'pending',
            'fine_applied_at' => now(),
            'fine_amount_applied' => 150.00,
        ]);

        // Attempt deletion via studentWiseFee route post
        $response = $this->post(route('school.fees.student-wise'), [
            'action' => 'delete_student_fee',
            'student_fee_id' => $finedFee->id,
        ]);

        $response->assertSessionHas('error', 'Cannot delete student fee record because a fine has already been applied. Waive the fine first.');
        
        // Assert student fee still exists in DB
        $this->assertDatabaseHas('student_fees', [
            'id' => $finedFee->id,
        ]);
    }

    public function test_delete_student_fee_succeeds_if_unpaid_and_no_fine(): void
    {
        $this->actingAs($this->schoolAdmin);

        $cleanFee = StudentFee::create([
            'school_id' => $this->schoolAdmin->school_id,
            'student_id' => $this->student->id,
            'fee_category_id' => $this->category->id,
            'fee_schedule_id' => $this->schedule->id,
            'fee_component_id' => $this->component->id,
            'installment_no' => 1,
            'amount' => 1000.00,
            'due_date' => '2026-04-30',
            'status' => 'pending',
        ]);

        $response = $this->post(route('school.fees.student-wise'), [
            'action' => 'delete_student_fee',
            'student_fee_id' => $cleanFee->id,
        ]);

        $response->assertSessionHas('success', 'Fee component deleted successfully from student profile!');
        
        // Assert student fee deleted from DB
        $this->assertDatabaseMissing('student_fees', [
            'id' => $cleanFee->id,
        ]);
    }

    public function test_component_delete_cascades_unpaid_student_fees_only(): void
    {
        $this->actingAs($this->schoolAdmin);

        // Unpaid component fee
        $unpaidFee = StudentFee::create([
            'school_id' => $this->schoolAdmin->school_id,
            'student_id' => $this->student->id,
            'fee_category_id' => $this->category->id,
            'fee_schedule_id' => $this->schedule->id,
            'fee_component_id' => $this->component->id,
            'installment_no' => 1,
            'amount' => 1000.00,
            'due_date' => '2026-04-30',
            'status' => 'pending',
        ]);

        // Paid component fee
        $paidFee = StudentFee::create([
            'school_id' => $this->schoolAdmin->school_id,
            'student_id' => $this->student->id,
            'fee_category_id' => $this->category->id,
            'fee_schedule_id' => $this->schedule->id,
            'fee_component_id' => $this->component->id,
            'installment_no' => 2,
            'amount' => 1000.00,
            'paid_amount' => 1000.00,
            'due_date' => '2026-05-30',
            'status' => 'paid',
        ]);

        // Delete component
        $response = $this->post(route('school.fees.basics'), [
            'action' => 'delete',
            'type' => 'component',
            'id' => $this->component->id,
        ]);

        $response->assertSessionHas('success');

        // Unpaid fee should be deleted
        $this->assertDatabaseMissing('student_fees', [
            'id' => $unpaidFee->id,
        ]);

        // Paid fee should remain
        $this->assertDatabaseHas('student_fees', [
            'id' => $paidFee->id,
        ]);
    }

    public function test_transport_component_delete_disables_transport_and_hard_deletes_all_fees(): void
    {
        $this->actingAs($this->schoolAdmin);

        // Opt student in
        $this->student->update([
            'transport_opted' => true,
            'transport_route_id' => 1,
        ]);

        $transportComponent = \App\Models\FeeComponent::create([
            'school_id' => $this->schoolAdmin->school_id,
            'academic_session_id' => $this->session->id,
            'component_name' => 'Transport Fee',
            'head_name' => 'Transport',
            'admission_type' => 'All Students',
            'gender' => 'All Students',
        ]);

        $unpaidTransportFee = StudentFee::create([
            'school_id' => $this->schoolAdmin->school_id,
            'student_id' => $this->student->id,
            'fee_category_id' => $this->category->id,
            'fee_schedule_id' => $this->schedule->id,
            'fee_component_id' => $transportComponent->id,
            'installment_no' => 1,
            'amount' => 600.00,
            'due_date' => '2026-04-30',
            'status' => 'pending',
        ]);

        $paidTransportFee = StudentFee::create([
            'school_id' => $this->schoolAdmin->school_id,
            'student_id' => $this->student->id,
            'fee_category_id' => $this->category->id,
            'fee_schedule_id' => $this->schedule->id,
            'fee_component_id' => $transportComponent->id,
            'installment_no' => 2,
            'amount' => 600.00,
            'paid_amount' => 600.00,
            'due_date' => '2026-05-30',
            'status' => 'paid',
        ]);

        // Delete transport component
        $response = $this->post(route('school.fees.basics'), [
            'action' => 'delete',
            'type' => 'component',
            'id' => $transportComponent->id,
        ]);

        $response->assertSessionHas('success');

        // Check student opted out
        $this->student->refresh();
        $this->assertFalse($this->student->transport_opted);
        $this->assertNull($this->student->transport_route_id);

        // Both fees (including paid) should be deleted
        $this->assertDatabaseMissing('student_fees', ['id' => $unpaidTransportFee->id]);
        $this->assertDatabaseMissing('student_fees', ['id' => $paidTransportFee->id]);
    }

    public function test_copy_class_wise_fees_to_other_classes(): void
    {
        $this->actingAs($this->schoolAdmin);

        // Create target class and student
        $targetClass = \App\Models\SchoolClass::create([
            'school_id' => $this->schoolAdmin->school_id,
            'name' => 'Class 2',
            'numeric_name' => '2',
        ]);

        $targetStudent = $this->student->replicate();
        $targetStudent->class_id = $targetClass->id;
        $targetStudent->admission_number = 'ADM-T-9999';
        $targetStudent->admission_sequence = 9999;
        $targetStudent->boarding_type = 'Day boarding';
        $targetStudent->save();

        // Get or create valid student category
        $studentCat = \App\Models\StudentCategory::firstOrCreate([
            'school_id' => $this->schoolAdmin->school_id,
            'name' => 'Day boarding',
        ]);

        // Create class-wise configuration for source class
        $sourceConfig = \App\Models\ClassWiseFee::create([
            'school_id' => $this->schoolAdmin->school_id,
            'academic_session_id' => $this->session->id,
            'class_id' => $this->student->class_id,
            'fee_schedule_id' => $this->schedule->id,
            'student_category_id' => $studentCat->id,
            'fee_component_id' => $this->component->id,
            'is_active' => true,
            'amount' => 2000.00,
            'installments' => [
                ['installment_no' => 1, 'amount' => 1000.00, 'date_range' => '01/04/2026 - 30/04/2026'],
                ['installment_no' => 2, 'amount' => 1000.00, 'date_range' => '01/05/2026 - 31/05/2026'],
            ],
        ]);

        // Post request to copy class-wise fees
        $response = $this->post(route('school.fees.class-wise.copy'), [
            'source_class_id' => $this->student->class_id,
            'target_class_ids' => [$targetClass->id],
        ]);

        $response->assertJson(['success' => true]);

        // Check target ClassWiseFee exists
        $this->assertDatabaseHas('class_wise_fees', [
            'school_id' => $this->schoolAdmin->school_id,
            'class_id' => $targetClass->id,
            'fee_component_id' => $this->component->id,
            'amount' => 2000.00,
        ]);

        // Check target student's fees are generated automatically!
        $this->assertDatabaseHas('student_fees', [
            'student_id' => $targetStudent->id,
            'fee_component_id' => $this->component->id,
            'installment_no' => 1,
            'amount' => 1000.00,
        ]);
    }
}
