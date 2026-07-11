<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use App\Models\StudentFee;
use App\Models\FeeCategory;
use App\Models\FeeComponent;
use App\Models\FeeInvoice;
use App\Models\TransportRoute;
use App\Models\TransportFeeSchedule;
use App\Models\BusAttendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransportFeeInstallmentTest extends TestCase
{
    use RefreshDatabase;

    protected $schoolAdmin;
    protected $student;
    protected $route;
    protected $session;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);

        $this->schoolAdmin = User::where('email', 'admin@yis.com')->first();
        $this->student = Student::where('school_id', $this->schoolAdmin->school_id)->first();
        $this->session = AcademicSession::where('school_id', $this->schoolAdmin->school_id)->first();

        // Create a route for mapping
        $this->route = TransportRoute::create([
            'school_id' => $this->schoolAdmin->school_id,
            'name' => 'Route A',
            'description' => 'Test Route',
        ]);

        // Map route stops or fares on student
        $this->student->update([
            'transport_opted' => true,
            'transport_route_id' => $this->route->id,
            'transport_route' => 'Route A',
            'transport_pick_fare' => 600.00,
            'transport_drop_fare' => 400.00,
            'transport_calendar_start' => $this->session->start_date->toDateString(),
        ]);
    }

    public function test_generate_transport_installments_creates_session_rows(): void
    {
        // 1. Create a schedule with 10 months
        $months = [];
        $start = \Carbon\Carbon::parse($this->session->start_date);
        for ($i = 0; $i < 10; $i++) {
            $m = $start->copy()->addMonths($i);
            $months[] = [
                'label' => $m->format('F Y'),
                'due_date' => $m->copy()->startOfMonth()->addDays(4)->toDateString()
            ];
        }

        $schedule = TransportFeeSchedule::create([
            'school_id' => $this->schoolAdmin->school_id,
            'academic_session_id' => $this->session->id,
            'route_id' => $this->route->id,
            'name' => 'Route A Schedule',
            'months' => $months,
            'is_active' => true,
        ]);

        // Trigger generation
        StudentFee::generateTransportInstallments($this->schoolAdmin->school_id, $this->student->id);

        // Assert 10 rows created
        $transportCategory = FeeCategory::where('school_id', $this->schoolAdmin->school_id)->where('name', 'Transport')->first();
        $this->assertNotNull($transportCategory);

        $feesCount = StudentFee::withoutGlobalScope('active')
            ->where('student_id', $this->student->id)
            ->where('fee_category_id', $transportCategory->id)
            ->count();

        $this->assertEquals(10, $feesCount);

        // Assert amount is pick + drop fare
        $firstFee = StudentFee::withoutGlobalScope('active')
            ->where('student_id', $this->student->id)
            ->where('fee_category_id', $transportCategory->id)
            ->first();

        $this->assertEquals(1000.00, $firstFee->amount);
    }

    public function test_attendance_deduction_only_affects_current_month(): void
    {
        // Create schedule
        $months = [
            ['label' => 'April 2026', 'due_date' => '2026-04-05'],
            ['label' => 'May 2026', 'due_date' => '2026-05-05'],
            ['label' => 'June 2026', 'due_date' => '2026-06-05'],
        ];

        $schedule = TransportFeeSchedule::create([
            'school_id' => $this->schoolAdmin->school_id,
            'academic_session_id' => $this->session->id,
            'route_id' => $this->route->id,
            'name' => 'Three Month Schedule',
            'months' => $months,
            'is_active' => true,
        ]);

        $this->student->update([
            'transport_calendar_start' => '2026-04-01',
        ]);

        StudentFee::generateTransportInstallments($this->schoolAdmin->school_id, $this->student->id);

        // Add 5 absences in May (Installment 2)
        // Days in May 2026: 31 days. Sundays: 5 (May 3, 10, 17, 24, 31). Billable: 26.
        // pick_fare: 600, drop_fare: 400. Daily pick: 600/26 = 23.0769. Daily drop: 400/26 = 15.3846.
        // 2 pickup absences, 3 drop absences.
        BusAttendance::create([
            'school_id' => $this->schoolAdmin->school_id,
            'student_id' => $this->student->id,
            'date' => '2026-05-12',
            'trip_type' => 'pickup',
            'status' => 'absent',
        ]);
        BusAttendance::create([
            'school_id' => $this->schoolAdmin->school_id,
            'student_id' => $this->student->id,
            'date' => '2026-05-13',
            'trip_type' => 'pickup',
            'status' => 'absent',
        ]);
        BusAttendance::create([
            'school_id' => $this->schoolAdmin->school_id,
            'student_id' => $this->student->id,
            'date' => '2026-05-12',
            'trip_type' => 'drop',
            'status' => 'absent',
        ]);
        BusAttendance::create([
            'school_id' => $this->schoolAdmin->school_id,
            'student_id' => $this->student->id,
            'date' => '2026-05-13',
            'trip_type' => 'drop',
            'status' => 'absent',
        ]);
        BusAttendance::create([
            'school_id' => $this->schoolAdmin->school_id,
            'student_id' => $this->student->id,
            'date' => '2026-05-14',
            'trip_type' => 'drop',
            'status' => 'absent',
        ]);

        // Run deduction for May (month 5, year 2026)
        StudentFee::applyTransportAttendanceDeduction($this->schoolAdmin->school_id, $this->student->id, 5, 2026);

        // Get installments
        $category = FeeCategory::where('school_id', $this->schoolAdmin->school_id)->where('name', 'Transport')->first();
        $inst1 = StudentFee::withoutGlobalScope('active')->where('student_id', $this->student->id)->where('installment_no', 1)->first();
        $inst2 = StudentFee::withoutGlobalScope('active')->where('student_id', $this->student->id)->where('installment_no', 2)->first();
        $inst3 = StudentFee::withoutGlobalScope('active')->where('student_id', $this->student->id)->where('installment_no', 3)->first();

        // Installment 1 and 3 should remain exactly 1000
        $this->assertEquals(1000.00, $inst1->amount);
        $this->assertEquals(1000.00, $inst3->amount);

        // Installment 2 should be reduced
        // Expected pick: 600 - 2 * (600/26) = 600 - 46.15 = 553.85
        // Expected drop: 400 - 3 * (400/26) = 400 - 46.15 = 353.85
        // Expected total: 907.70 (approx)
        $this->assertLessThan(1000.00, $inst2->amount);
        $this->assertGreaterThan(850.00, $inst2->amount);
    }

    public function test_opt_out_preserves_paid_history(): void
    {
        $months = [
            ['label' => 'April 2026', 'due_date' => '2026-04-05'],
            ['label' => 'May 2026', 'due_date' => '2026-05-05'],
        ];

        $schedule = TransportFeeSchedule::create([
            'school_id' => $this->schoolAdmin->school_id,
            'academic_session_id' => $this->session->id,
            'route_id' => $this->route->id,
            'name' => 'Two Month Schedule',
            'months' => $months,
            'is_active' => true,
        ]);

        $this->student->update([
            'transport_calendar_start' => '2026-04-01',
        ]);

        StudentFee::generateTransportInstallments($this->schoolAdmin->school_id, $this->student->id);

        $category = FeeCategory::where('school_id', $this->schoolAdmin->school_id)->where('name', 'Transport')->first();
        $inst1 = StudentFee::withoutGlobalScope('active')
            ->where('student_id', $this->student->id)
            ->where('fee_category_id', $category->id)
            ->where('installment_no', 1)
            ->first();

        // Mark installment 1 as paid
        $inst1->update([
            'paid_amount' => 1000.00,
            'status' => 'paid',
        ]);

        // Opt out student
        $response = $this->actingAs($this->schoolAdmin)->post('/school/transport/student-route-mapping', [
            'student_id' => $this->student->id,
            'transport_route' => '', // Empty means opt out
            'transport_route_id' => '',
        ]);

        $response->assertSessionHas('success');

        // Verify paid row still exists, unpaid row deleted
        $this->assertTrue(StudentFee::withoutGlobalScope('active')
            ->where('id', $inst1->id)
            ->exists());

        $this->assertFalse(StudentFee::withoutGlobalScope('active')
            ->where('student_id', $this->student->id)
            ->where('fee_category_id', $category->id)
            ->where('installment_no', 2)
            ->exists());
    }

    public function test_payment_history_excludes_cancelled(): void
    {
        $category = FeeCategory::where('school_id', $this->schoolAdmin->school_id)->first();
        
        $fee = StudentFee::create([
            'school_id' => $this->schoolAdmin->school_id,
            'student_id' => $this->student->id,
            'fee_category_id' => $category->id,
            'installment_no' => 1,
            'amount' => 1000.00,
            'paid_amount' => 0.00,
            'status' => 'pending',
            'due_date' => now()->toDateString(),
            'academic_session_id' => $this->session->id,
        ]);

        // Record payment
        $response = $this->actingAs($this->schoolAdmin)->post('/school/fees/student-wise', [
            'action' => 'mark_paid',
            'student_id' => $this->student->id,
            'installment_no' => 1,
            'amount_paid' => 1000.00,
            'payment_mode' => 'cash',
        ]);

        $invoice = FeeInvoice::where('student_id', $this->student->id)->where('type', 'payment')->first();
        $this->assertNotNull($invoice);

        // Cancel invoice
        $response = $this->actingAs($this->schoolAdmin)->post('/school/fees/student-wise', [
            'action' => 'cancel_invoice',
            'student_id' => $this->student->id,
            'installment_no' => 1,
            'invoice_no' => $invoice->invoice_number,
            'remarks' => 'Correction',
        ]);

        // Load page, paymentHistory should not contain the cancelled invoice
        $responsePage = $this->actingAs($this->schoolAdmin)->get("/school/fees/student-wise?view_student={$this->student->id}&tab=payment_history");
        $history = $responsePage->viewData('paymentHistory');
        
        $this->assertEquals(0, $history->count());
    }

    public function test_no_schedule_fallback_creates_12_months(): void
    {
        // Delete all transport fee schedules to force fallback
        TransportFeeSchedule::query()->delete();

        StudentFee::generateTransportInstallments($this->schoolAdmin->school_id, $this->student->id);

        $category = FeeCategory::where('school_id', $this->schoolAdmin->school_id)->where('name', 'Transport')->first();
        
        $feesCount = StudentFee::withoutGlobalScope('active')
            ->where('student_id', $this->student->id)
            ->where('fee_category_id', $category->id)
            ->count();

        $this->assertEquals(12, $feesCount);
    }

    public function test_mid_session_opt_in_filters_past_months(): void
    {
        $months = [
            ['label' => 'April 2026', 'due_date' => '2026-04-05'],
            ['label' => 'May 2026', 'due_date' => '2026-05-05'],
            ['label' => 'June 2026', 'due_date' => '2026-06-05'],
            ['label' => 'July 2026', 'due_date' => '2026-07-05'],
        ];

        $schedule = TransportFeeSchedule::create([
            'school_id' => $this->schoolAdmin->school_id,
            'academic_session_id' => $this->session->id,
            'route_id' => $this->route->id,
            'name' => 'Route A Schedule',
            'months' => $months,
            'is_active' => true,
        ]);

        // Opt in starting June 2026
        $this->student->update([
            'transport_calendar_start' => '2026-06-01',
        ]);

        StudentFee::generateTransportInstallments($this->schoolAdmin->school_id, $this->student->id);

        $category = FeeCategory::where('school_id', $this->schoolAdmin->school_id)->where('name', 'Transport')->first();

        // Verify installments 1 and 2 (April, May) do NOT exist
        $this->assertFalse(StudentFee::withoutGlobalScope('active')
            ->where('student_id', $this->student->id)
            ->where('fee_category_id', $category->id)
            ->where('installment_no', 1)
            ->exists());
        $this->assertFalse(StudentFee::withoutGlobalScope('active')
            ->where('student_id', $this->student->id)
            ->where('fee_category_id', $category->id)
            ->where('installment_no', 2)
            ->exists());

        // Verify installments 3 and 4 (June, July) DO exist
        $this->assertTrue(StudentFee::withoutGlobalScope('active')
            ->where('student_id', $this->student->id)
            ->where('fee_category_id', $category->id)
            ->where('installment_no', 3)
            ->exists());
        $this->assertTrue(StudentFee::withoutGlobalScope('active')
            ->where('student_id', $this->student->id)
            ->where('fee_category_id', $category->id)
            ->where('installment_no', 4)
            ->exists());
    }

    public function test_delete_schedule_blocked_if_fees_paid(): void
    {
        $months = [
            ['label' => 'April 2026', 'due_date' => '2026-04-05'],
        ];

        $schedule = TransportFeeSchedule::create([
            'school_id' => $this->schoolAdmin->school_id,
            'academic_session_id' => $this->session->id,
            'route_id' => $this->route->id,
            'name' => 'Route A Schedule',
            'months' => $months,
            'is_active' => true,
        ]);

        $this->student->update([
            'transport_calendar_start' => '2026-04-01',
        ]);

        StudentFee::generateTransportInstallments($this->schoolAdmin->school_id, $this->student->id);

        $category = FeeCategory::where('school_id', $this->schoolAdmin->school_id)->where('name', 'Transport')->first();
        $inst = StudentFee::withoutGlobalScope('active')
            ->where('student_id', $this->student->id)
            ->where('fee_category_id', $category->id)
            ->where('installment_no', 1)
            ->first();

        // Mark as paid
        $inst->update([
            'paid_amount' => 1000.00,
            'status' => 'paid',
        ]);

        // Attempt deletion
        $response = $this->actingAs($this->schoolAdmin)->post('/school/transport/fee-schedules/delete', [
            'id' => $schedule->id,
        ]);

        $response->assertSessionHas('error');
        $this->assertTrue(TransportFeeSchedule::where('id', $schedule->id)->exists());
    }
}
