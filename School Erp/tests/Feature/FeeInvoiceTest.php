<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use App\Models\StudentFee;
use App\Models\FeeCategory;
use App\Models\FeeInvoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeeInvoiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    public function test_fee_payment_generates_invoice_and_cancel_restores_dues(): void
    {
        $schoolAdmin = User::where('email', 'admin@yis.com')->first();
        $student = Student::where('school_id', $schoolAdmin->school_id)->first();
        $category = FeeCategory::where('school_id', $schoolAdmin->school_id)->first();

        // Seed some student fees for installment 1
        $fee1 = StudentFee::create([
            'school_id' => $schoolAdmin->school_id,
            'student_id' => $student->id,
            'fee_category_id' => $category->id,
            'installment_no' => 1,
            'amount' => 1000.00,
            'paid_amount' => 0.00,
            'status' => 'pending',
            'due_date' => now()->toDateString(),
            'academic_session_id' => AcademicSession::where('school_id', $schoolAdmin->school_id)->first()->id,
        ]);

        // 1. Pay invoice
        $response = $this->actingAs($schoolAdmin)->post('/school/fees/student-wise', [
            'action' => 'mark_paid',
            'student_id' => $student->id,
            'installment_no' => 1,
            'amount_paid' => 1000.00,
            'payment_mode' => 'cash',
        ]);

        $response->assertSessionHas('success');

        // Check FeeInvoice record
        $invoice = FeeInvoice::where('student_id', $student->id)
            ->where('type', 'payment')
            ->first();

        $this->assertNotNull($invoice);
        $this->assertEquals(1000.00, $invoice->amount);
        $this->assertEquals('paid', $invoice->status);

        // Check immutability
        try {
            $invoice->update(['amount' => 2000.00]);
            $this->fail('Expected RuntimeException on update');
        } catch (\RuntimeException $e) {
            $this->assertTrue(true);
        }

        try {
            $invoice->delete();
            $this->fail('Expected RuntimeException on delete');
        } catch (\RuntimeException $e) {
            $this->assertTrue(true);
        }

        // 2. Cancel invoice
        $response = $this->actingAs($schoolAdmin)->post('/school/fees/student-wise', [
            'action' => 'cancel_invoice',
            'student_id' => $student->id,
            'installment_no' => 1,
            'invoice_no' => $invoice->invoice_number,
            'remarks' => 'Typo in entry',
        ]);

        $response->assertSessionHas('success');

        // Check original FeeInvoice is now marked cancelled
        $invoice->refresh();
        $this->assertEquals('cancelled', $invoice->status);
        
        $this->assertNull(FeeInvoice::where('type', 'cancel_payment')->first());

        // Check dues restored
        $fee1->refresh();
        $this->assertEquals(0.00, $fee1->paid_amount);
        $this->assertEquals('pending', $fee1->status);

        // 3. Double cancellation protection
        $response2 = $this->actingAs($schoolAdmin)->post('/school/fees/student-wise', [
            'action' => 'cancel_invoice',
            'student_id' => $student->id,
            'installment_no' => 1,
            'invoice_no' => $invoice->invoice_number,
            'remarks' => 'Duplicate cancel request',
        ]);

        $response2->assertSessionHas('error');
    }
}
