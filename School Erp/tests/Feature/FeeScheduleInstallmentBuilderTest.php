<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\School;
use App\Models\User;
use App\Models\FeeFine;
use App\Models\FeeSchedule;
use App\Models\TransportFeeSchedule;
use App\Services\FeeInstallmentDistributor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;
use Tests\TestCase;

class FeeScheduleInstallmentBuilderTest extends TestCase
{
    use RefreshDatabase;

    protected $schoolAdmin;
    protected $session;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $this->schoolAdmin = User::where('email', 'admin@yis.com')->first();
        $this->session = AcademicSession::where('school_id', $this->schoolAdmin->school_id)->first();
    }

    public function test_distributor_generate_monthly(): void
    {
        $start = Carbon::parse('2026-04-01');
        $end = Carbon::parse('2027-03-31');

        $installments = FeeInstallmentDistributor::generate($start, $end, 'monthly');
        $this->assertCount(12, $installments);
        $this->assertEquals('April 2026', $installments[0]['name']);
        $this->assertEquals('2026-04-01', $installments[0]['start_date']);
        $this->assertEquals('2026-04-30', $installments[0]['end_date']);
        $this->assertEquals('March 2027', $installments[11]['name']);
        $this->assertEquals('2027-03-01', $installments[11]['start_date']);
        $this->assertEquals('2027-03-31', $installments[11]['end_date']);
    }

    public function test_distributor_generate_quarterly(): void
    {
        $start = Carbon::parse('2026-04-01');
        $end = Carbon::parse('2027-03-31');

        $installments = FeeInstallmentDistributor::generate($start, $end, 'quarterly');
        $this->assertCount(4, $installments);
        $this->assertEquals('Q1 (Apr-Jun 2026)', $installments[0]['name']);
        $this->assertEquals('2026-04-01', $installments[0]['start_date']);
        $this->assertEquals('2026-06-30', $installments[0]['end_date']);
    }

    public function test_distributor_generate_yearly(): void
    {
        $start = Carbon::parse('2026-04-01');
        $end = Carbon::parse('2027-03-31');

        $installments = FeeInstallmentDistributor::generate($start, $end, 'yearly');
        $this->assertCount(1, $installments);
        $this->assertEquals('2026-04-01', $installments[0]['start_date']);
        $this->assertEquals('2027-03-31', $installments[0]['end_date']);
    }

    public function test_distributor_generate_custom(): void
    {
        $start = Carbon::parse('2026-04-01');
        $end = Carbon::parse('2027-03-31');

        $installments = FeeInstallmentDistributor::generate($start, $end, 'custom', 5);
        $this->assertCount(5, $installments);
        $this->assertEquals('2026-04-01', $installments[0]['start_date']);
        $this->assertEquals('2027-03-31', $installments[4]['end_date']);
    }

    public function test_validate_installments_detects_overlap(): void
    {
        $session = $this->session;
        $session->start_date = '2026-04-01';
        $session->end_date = '2027-03-31';
        $session->save();

        $installments = [
            [
                'installment_no' => 1,
                'name' => 'Inst 1',
                'start_date' => '2026-04-01',
                'end_date' => '2026-06-30',
                'due_date' => '2026-06-30',
                'grace_days' => 5,
            ],
            [
                'installment_no' => 2,
                'name' => 'Inst 2',
                'start_date' => '2026-06-15', // Overlaps with Inst 1
                'end_date' => '2026-09-30',
                'due_date' => '2026-09-30',
                'grace_days' => 5,
            ]
        ];

        $error = FeeInstallmentDistributor::validateInstallments($installments, $session);
        $this->assertNotNull($error);
        $this->assertStringContainsString('overlaps', $error);
    }

    public function test_validate_installments_detects_out_of_bounds(): void
    {
        $session = $this->session;
        $session->start_date = '2026-04-01';
        $session->end_date = '2027-03-31';
        $session->save();

        $installments = [
            [
                'installment_no' => 1,
                'name' => 'Inst 1',
                'start_date' => '2026-03-01', // Before session start
                'end_date' => '2026-06-30',
                'due_date' => '2026-06-30',
                'grace_days' => 5,
            ]
        ];

        $error = FeeInstallmentDistributor::validateInstallments($installments, $session);
        $this->assertNotNull($error);
        $this->assertStringContainsString('bounds', $error);
    }

    public function test_controller_add_fee_schedule_validation(): void
    {
        $this->actingAs($this->schoolAdmin);

        $fine = FeeFine::create([
            'school_id' => $this->schoolAdmin->school_id,
            'academic_session_id' => $this->session->id,
            'name' => 'Late Fine',
            'fine_type' => 'Fixed Amount',
            'fine_amount' => 100,
            'status' => true,
        ]);

        $postData = [
            'action' => 'add_fee_schedule',
            'name' => 'Schedule X',
            'academic_session_id' => $this->session->id,
            'classes' => ['Class 1', 'Class 2'],
            'installment_type' => 'custom',
            'custom_count' => 2,
            'fine_id' => $fine->id,
            'installments' => [
                [
                    'installment_no' => 1,
                    'name' => 'Inst 1',
                    'start_date' => $this->session->start_date->toDateString(),
                    'end_date' => $this->session->start_date->copy()->addMonths(2)->toDateString(),
                    'due_date' => $this->session->start_date->copy()->addMonths(2)->toDateString(),
                    'grace_days' => 5,
                ],
                [
                    'installment_no' => 2,
                    'name' => 'Inst 2',
                    'start_date' => $this->session->start_date->copy()->addMonths(2)->addDay()->toDateString(),
                    'end_date' => $this->session->end_date->toDateString(),
                    'due_date' => $this->session->end_date->toDateString(),
                    'grace_days' => 5,
                ]
            ]
        ];

        $response = $this->post(route('school.fees.basics'), $postData);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $schedule = FeeSchedule::where('name', 'Schedule X')->first();
        $this->assertNotNull($schedule);
        $this->assertEquals(2, $schedule->no_of_installments);
        $this->assertEquals('custom', $schedule->installment_type);
        $this->assertEquals($fine->id, $schedule->fine_id);
    }
}
