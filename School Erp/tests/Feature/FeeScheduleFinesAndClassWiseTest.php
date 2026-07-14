<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use App\Models\AcademicSession;
use App\Models\FeeCategory;
use App\Models\FeeComponent;
use App\Models\FeeSchedule;
use App\Models\FeeFine;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\ClassWiseFee;
use App\Http\Controllers\School\FeeManagementController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeeScheduleFinesAndClassWiseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    public function test_fee_schedule_class_wise_filtering_and_fine_application()
    {
        $admin = User::where('email', 'admin@yis.com')->first();
        $school = $admin->school;
        $student = Student::where('school_id', $school->id)->first();
        $session = AcademicSession::where('school_id', $school->id)->first();

        // 1. Create class-wise schedule and non-applicable schedule
        $scheduleApplicable = FeeSchedule::create([
            'school_id' => $school->id,
            'academic_session_id' => $session->id,
            'classes' => $student->class->name,
            'name' => 'Applicable Schedule',
            'start_date' => '2025-04-01',
            'end_date' => '2026-03-31',
            'installment_type' => 'yearly',
            'installments' => [
                [
                    'installment_no' => 1,
                    'name' => 'Installment 1',
                    'start_date' => '2025-04-01',
                    'end_date' => '2026-03-31',
                    'due_date' => '2025-04-10',
                    'grace_days' => 5
                ]
            ]
        ]);

        $scheduleNonApplicable = FeeSchedule::create([
            'school_id' => $school->id,
            'academic_session_id' => $session->id,
            'classes' => 'Some Other Class Name',
            'name' => 'Non-Applicable Schedule',
            'start_date' => '2025-04-01',
            'end_date' => '2026-03-31',
            'installment_type' => 'yearly',
            'installments' => [
                [
                    'installment_no' => 1,
                    'name' => 'Installment 1',
                    'start_date' => '2025-04-01',
                    'end_date' => '2026-03-31',
                    'due_date' => '2025-04-10',
                    'grace_days' => 5
                ]
            ]
        ]);

        $this->actingAs($admin);

        // Call the class-wise page for student's class
        $response = $this->get(route('school.fees.class-wise', [
            'class_id' => $student->class_id,
            'academic_session_id' => $session->id
        ]));
        $response->assertStatus(200);

        // Verify that only the applicable schedule is passed in view schedules collection
        $viewSchedules = $response->viewData('schedules');
        $this->assertTrue($viewSchedules->contains('id', $scheduleApplicable->id));
        $this->assertFalse($viewSchedules->contains('id', $scheduleNonApplicable->id));

        // 2. Test propagation of installment changes on update
        $fine = FeeFine::create([
            'school_id' => $school->id,
            'academic_session_id' => $session->id,
            'name' => 'Late Fine Policy',
            'fine_type' => 'Fixed Amount',
            'fine_amount' => 100.00
        ]);

        // Configure class-wise fee
        $comp = FeeComponent::create([
            'school_id' => $school->id,
            'academic_session_id' => $session->id,
            'fee_schedule_id' => $scheduleApplicable->id,
            'fee_category_id' => 1,
            'head_name' => 'Tuition',
            'component_name' => 'Tuition Fee'
        ]);

        $cwFee = ClassWiseFee::create([
            'school_id' => $school->id,
            'academic_session_id' => $session->id,
            'class_id' => $student->class_id,
            'fee_schedule_id' => $scheduleApplicable->id,
            'student_category_id' => 1,
            'fee_component_id' => $comp->id,
            'is_active' => true,
            'amount' => 1000.00,
            'installments' => [
                [
                    'installment_no' => 1,
                    'name' => 'Installment 1',
                    'amount' => 1000.00,
                    'due_date' => '2025-04-10',
                    'start_date' => '2025-04-01',
                    'end_date' => '2026-03-31',
                ]
            ]
        ]);

        // First sync to create student fees
        FeeManagementController::syncClassWiseFeeToStudents($school->id, $cwFee);

        $studentFee = StudentFee::where('student_id', $student->id)->where('fee_component_id', $comp->id)->first();
        $this->assertNotNull($studentFee);
        $this->assertEquals(1000.00, $studentFee->amount);
        $this->assertEquals(0.00, $studentFee->fine_amount_applied);

        // Edit schedule via post request
        $updatedInstallments = [
            [
                'installment_no' => 1,
                'name' => 'Updated Name',
                'start_date' => '2025-04-01',
                'end_date' => '2026-03-31',
                'due_date' => '2025-04-02', // past date to trigger overdue fine
                'grace_days' => 1
            ]
        ];

        $postData = [
            'action' => 'edit_fee_schedule',
            'id' => $scheduleApplicable->id,
            'academic_session_id' => $session->id,
            'name' => 'Applicable Schedule Updated',
            'classes' => [$student->class->name],
            'sections' => [],
            'installment_type' => 'yearly',
            'installments' => $updatedInstallments,
            'fine_id' => $fine->id
        ];

        $editResponse = $this->post(route('school.fees.basics'), $postData);
        $editResponse->assertSessionHasNoErrors();
        $editResponse->assertRedirect();

        // Check if ClassWiseFee was updated
        $cwFee->refresh();
        $this->assertEquals('2025-04-02', $cwFee->installments[0]['due_date']);
        $this->assertEquals('Updated Name', $cwFee->installments[0]['name']);

        // Check if student fee due date was updated and fine applied automatically!
        $studentFee->refresh();
        $this->assertEquals('2025-04-02', $studentFee->due_date);
        $this->assertEquals(100.00, $studentFee->fine_amount_applied);
        $this->assertEquals(1000.00, $studentFee->amount); // Base remains 1000
        $this->assertEquals(1100.00, floatval($studentFee->amount) + floatval($studentFee->fine_amount_applied)); // 1000 + 100 fine
    }

    public function test_miscellaneous_fee_creation_and_synchronization()
    {
        $admin = User::where('email', 'admin@yis.com')->first();
        $school = $admin->school;
        $student = Student::where('school_id', $school->id)->first();
        $session = AcademicSession::where('school_id', $school->id)->first();

        // Put student in a known class and section
        $student->class->update(['name' => 'Nursery']);
        $student->section->update(['name' => 'A']);
        $student->refresh();

        $this->actingAs($admin);

        $postData = [
            'action' => 'add_misc_fee',
            'academic_session_id' => $session->id,
            'fee_head_name' => 'ADMISSION FEES',
            'name' => 'Demo Book Fee',
            'remarks' => 'Dynamic description',
            'amount' => 500.00,
            'classes' => [
                'Nursery' => [
                    'active' => '1',
                    'sections' => ['A'],
                    'installments' => [
                        '1' => [
                            'active' => '1',
                            'start_date' => '2026-07-13',
                            'end_date' => '2026-07-13'
                        ]
                    ]
                ]
            ],
            'student_ids' => []
        ];

        $response = $this->post(route('school.fees.basics'), $postData);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        // Assert MiscFee was created
        $miscFee = \App\Models\MiscFee::where('school_id', $school->id)->where('name', 'Demo Book Fee')->first();
        $this->assertNotNull($miscFee);
        $this->assertEquals('ADMISSION FEES', $miscFee->fee_head_name);

        // Assert StudentFee was created and mapped correctly
        $studentFee = StudentFee::where('student_id', $student->id)->where('misc_fee_id', $miscFee->id)->first();
        $this->assertNotNull($studentFee);
        $this->assertEquals(500.00, $studentFee->amount);
        $this->assertEquals('2026-07-13', $studentFee->due_date);
        $this->assertEquals('ADMISSION FEES', $studentFee->category->name);
    }
}
