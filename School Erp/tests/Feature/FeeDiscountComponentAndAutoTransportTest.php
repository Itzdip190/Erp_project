<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use App\Models\AcademicSession;
use App\Models\FeeCategory;
use App\Models\FeeComponent;
use App\Models\FeeDiscount;
use App\Models\FeeSchedule;
use App\Models\Student;
use App\Models\StudentFee;
use App\Http\Controllers\School\FeeManagementController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeeDiscountComponentAndAutoTransportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    public function test_yash_international_school_auto_creates_transport_component()
    {
        // Seeder creates admin@yis.com which belongs to Yash International School
        $admin = User::where('email', 'admin@yis.com')->first();
        $school = $admin->school;

        $this->actingAs($admin);

        // Call the basics page (which triggers ensureFeesSeeded)
        $response = $this->get(route('school.fees.basics'));
        $response->assertStatus(200);

        // Verify Transport Fee component exists
        $component = FeeComponent::where('school_id', $school->id)
            ->where('component_name', 'Transport Fee')
            ->first();
        
        $this->assertNotNull($component);
        $this->assertEquals('Transport', $component->head_name);

        // Delete the component
        $component->delete();

        // Refresh/call basics page again
        $response = $this->get(route('school.fees.basics'));
        $response->assertStatus(200);

        // Verify it was recreated automatically
        $componentRecreated = FeeComponent::where('school_id', $school->id)
            ->where('component_name', 'Transport Fee')
            ->first();
        
        $this->assertNotNull($componentRecreated);
    }

    public function test_fee_discount_components_restriction()
    {
        $admin = User::where('email', 'admin@yis.com')->first();
        $school = $admin->school;
        $student = Student::where('school_id', $school->id)->first();
        $session = AcademicSession::where('school_id', $school->id)->first();

        $this->actingAs($admin);

        // Create a schedule
        $schedule = FeeSchedule::create([
            'school_id' => $school->id,
            'academic_session_id' => $session->id,
            'classes' => 'Class 1',
            'name' => 'General Schedule',
            'start_date' => '2026-04-01',
            'end_date' => '2027-03-31',
            'installment_type' => 'yearly',
            'installments' => [
                [
                    'installment_no' => 1,
                    'name' => 'Yearly Installment',
                    'start_date' => '2026-04-01',
                    'end_date' => '2027-03-31',
                    'due_date' => '2026-04-10',
                    'grace_days' => 0
                ]
            ]
        ]);

        // Create two components
        $cat = FeeCategory::create(['school_id' => $school->id, 'name' => 'Academic']);
        
        $compTuition = FeeComponent::create([
            'school_id' => $school->id,
            'academic_session_id' => $session->id,
            'fee_schedule_id' => $schedule->id,
            'fee_category_id' => $cat->id,
            'head_name' => 'Tuition',
            'component_name' => 'Tuition Fee'
        ]);

        $compTerm = FeeComponent::create([
            'school_id' => $school->id,
            'academic_session_id' => $session->id,
            'fee_schedule_id' => $schedule->id,
            'fee_category_id' => $cat->id,
            'head_name' => 'Term',
            'component_name' => 'Term Fee'
        ]);

        // Assign student fees
        $feeTuition = StudentFee::create([
            'school_id' => $school->id,
            'student_id' => $student->id,
            'fee_category_id' => $cat->id,
            'fee_schedule_id' => $schedule->id,
            'fee_component_id' => $compTuition->id,
            'installment_no' => 1,
            'amount' => 5000,
            'due_date' => '2026-04-10',
            'status' => 'pending'
        ]);

        $feeTerm = StudentFee::create([
            'school_id' => $school->id,
            'student_id' => $student->id,
            'fee_category_id' => $cat->id,
            'fee_schedule_id' => $schedule->id,
            'fee_component_id' => $compTerm->id,
            'installment_no' => 1,
            'amount' => 2000,
            'due_date' => '2026-04-10',
            'status' => 'pending'
        ]);

        // Create a discount restricted only to Tuition Fee
        $discount = FeeDiscount::create([
            'school_id' => $school->id,
            'academic_session_id' => $session->id,
            'name' => 'Tuition Discount',
            'amount' => 1000,
            'type' => 'flat',
            'classes_installments' => json_encode([$student->class->name ?? 'Class 9']),
            'fee_component_ids' => json_encode([$compTuition->id])
        ]);

        // Sync discounts
        FeeManagementController::syncStudentDiscounts($student, $session->id);

        // Verify that discount only applied to Tuition Fee
        $feeTuition->refresh();
        $feeTerm->refresh();

        $this->assertEquals(1000, $feeTuition->instant_discount_amount);
        $this->assertEquals(0, $feeTerm->instant_discount_amount);
    }
}
