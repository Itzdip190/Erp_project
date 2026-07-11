<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\School;
use App\Models\User;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\FeeCategory;
use App\Models\FeeComponent;
use App\Models\FeeFine;
use App\Models\FeeSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;
use Tests\TestCase;

class OverdueInstallmentFineTest extends TestCase
{
    use RefreshDatabase;

    protected $schoolAdmin;
    protected $student;
    protected $session;
    protected $category;
    protected $component;
    protected $fine;
    protected $schedule;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        
        $this->schoolAdmin = User::where('email', 'admin@yis.com')->first();
        $this->student = Student::where('school_id', $this->schoolAdmin->school_id)->first();
        $this->session = AcademicSession::where('school_id', $this->schoolAdmin->school_id)->first();

        // Create standard categories & components
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

        // Create Fine Policy
        $this->fine = FeeFine::create([
            'school_id' => $this->schoolAdmin->school_id,
            'academic_session_id' => $this->session->id,
            'name' => 'Overdue Flat Fine',
            'fine_type' => 'Fixed Amount',
            'fine_amount' => 150.00,
            'default_grace_days' => 5,
            'status' => true,
        ]);

        // Create Fee Schedule with fine linked
        $this->schedule = FeeSchedule::create([
            'school_id' => $this->schoolAdmin->school_id,
            'academic_session_id' => $this->session->id,
            'name' => 'Tuition Schedule',
            'classes' => 'Class 1',
            'installment_type' => 'custom',
            'fine_id' => $this->fine->id,
            'start_date' => $this->session->start_date->toDateString(),
            'end_date' => $this->session->end_date->toDateString(),
            'installments' => [
                [
                    'installment_no' => 1,
                    'name' => 'Installment 1',
                    'start_date' => '2026-04-01',
                    'end_date' => '2026-04-30',
                    'due_date' => '2026-04-30',
                    'grace_days' => 5, // due 2026-04-30 + 5 days grace = 2026-05-05 grace date
                ]
            ]
        ]);
    }

    public function test_fine_applied_once_after_grace_period(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-05-06')); // Grace period ended on 2026-05-05. Today is 2026-05-06.

        // Create unpaid Student Fee
        $studentFee = StudentFee::create([
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

        // Run overdue fine job first time
        (new \App\Console\Commands\ApplyOverdueInstallmentFines())->handle();

        $studentFee->refresh();
        $this->assertEquals(1150.00, $studentFee->amount); // 1000 + 150 fine
        $this->assertEquals(150.00, $studentFee->fine_amount_applied);
        $this->assertNotNull($studentFee->fine_applied_at);

        // Run it a second time
        (new \App\Console\Commands\ApplyOverdueInstallmentFines())->handle();

        $studentFee->refresh();
        $this->assertEquals(1150.00, $studentFee->amount); // Should NOT add fine again
        $this->assertEquals(150.00, $studentFee->fine_amount_applied);
        
        Carbon::setTestNow();
    }

    public function test_fine_not_applied_within_grace_period(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-05-04')); // Grace date is 2026-05-05. Today is 2026-05-04 (within grace).

        // Create unpaid Student Fee
        $studentFee = StudentFee::create([
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

        (new \App\Console\Commands\ApplyOverdueInstallmentFines())->handle();

        $studentFee->refresh();
        $this->assertEquals(1000.00, $studentFee->amount); // No fine
        $this->assertNull($studentFee->fine_applied_at);

        Carbon::setTestNow();
    }

    public function test_fine_not_applied_to_paid_or_cancelled_installments(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-05-06'));

        // Paid fee
        $paidFee = StudentFee::create([
            'school_id' => $this->schoolAdmin->school_id,
            'student_id' => $this->student->id,
            'fee_category_id' => $this->category->id,
            'fee_schedule_id' => $this->schedule->id,
            'fee_component_id' => $this->component->id,
            'installment_no' => 1,
            'amount' => 1000.00,
            'paid_amount' => 1000.00,
            'due_date' => '2026-04-30',
            'status' => 'paid',
        ]);

        // Cancelled fee
        $cancelledFee = StudentFee::create([
            'school_id' => $this->schoolAdmin->school_id,
            'student_id' => $this->student->id,
            'fee_category_id' => $this->category->id,
            'fee_schedule_id' => $this->schedule->id,
            'fee_component_id' => $this->component->id,
            'installment_no' => 1,
            'amount' => 1000.00,
            'due_date' => '2026-04-30',
            'status' => 'pending',
            'invoice_status' => 'cancelled',
        ]);

        (new \App\Console\Commands\ApplyOverdueInstallmentFines())->handle();

        $paidFee->refresh();
        $this->assertEquals(1000.00, $paidFee->amount);
        $this->assertNull($paidFee->fine_applied_at);

        $cancelledFee->refresh();
        $this->assertEquals(1000.00, $cancelledFee->amount);
        $this->assertNull($cancelledFee->fine_applied_at);

        Carbon::setTestNow();
    }

    public function test_daily_fine_type(): void
    {
        // Daily fine configuration
        $dailyFine = FeeFine::create([
            'school_id' => $this->schoolAdmin->school_id,
            'academic_session_id' => $this->session->id,
            'name' => 'Overdue Daily Fine',
            'fine_type' => 'Daily',
            'fine_amount' => 10.00, // 10 per day overdue
            'default_grace_days' => 5,
            'status' => true,
        ]);

        $dailySchedule = FeeSchedule::create([
            'school_id' => $this->schoolAdmin->school_id,
            'academic_session_id' => $this->session->id,
            'name' => 'Daily Tuition Schedule',
            'classes' => 'Class 1',
            'installment_type' => 'custom',
            'fine_id' => $dailyFine->id,
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

        Carbon::setTestNow(Carbon::parse('2026-05-10')); // 10 days overdue since due date is 2026-04-30.

        // Create unpaid Student Fee
        $studentFee = StudentFee::create([
            'school_id' => $this->schoolAdmin->school_id,
            'student_id' => $this->student->id,
            'fee_category_id' => $this->category->id,
            'fee_schedule_id' => $dailySchedule->id,
            'fee_component_id' => $this->component->id,
            'installment_no' => 1,
            'amount' => 1000.00,
            'due_date' => '2026-04-30',
            'status' => 'pending',
        ]);

        (new \App\Console\Commands\ApplyOverdueInstallmentFines())->handle();

        $studentFee->refresh();
        // 10 days overdue * 10 fine amount = 100 fine applied.
        $this->assertEquals(1100.00, $studentFee->amount);
        $this->assertEquals(100.00, $studentFee->fine_amount_applied);

        Carbon::setTestNow();
    }
}
