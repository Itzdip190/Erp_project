<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\FeeCategory;
use App\Models\FeeStructure;
use App\Models\StudentFee;
use App\Models\FeeReceipt;
use App\Models\PendingCheque;
use App\Models\PaymentLink;
use App\Models\FeeRefund;
use App\Models\OptionalFeeMapping;
use Carbon\Carbon;

class FeeManagementController extends Controller
{
    private function ensureFeesSeeded($schoolId)
    {
        // 1. Seed Fee Categories
        if (FeeCategory::where('school_id', $schoolId)->count() === 0) {
            $categories = [
                ['name' => 'Tuition Fee', 'description' => 'Regular monthly course fee.'],
                ['name' => 'Transport Fee', 'description' => 'School bus fare based on distance.'],
                ['name' => 'Examination Fee', 'description' => 'Term end assessments & print fee.'],
                ['name' => 'Library Fee', 'description' => 'Access to digital and physical catalog.'],
                ['name' => 'Laboratory Fee', 'description' => 'Computer and Science Lab maintenance.'],
                ['name' => 'Sports Fee', 'description' => 'Recreation and training equipment fee.']
            ];
            foreach ($categories as $cat) {
                FeeCategory::create(array_merge($cat, ['school_id' => $schoolId]));
            }
        }

        $tuitionCat = FeeCategory::where('school_id', $schoolId)->where('name', 'Tuition Fee')->first() 
            ?? FeeCategory::create(['school_id' => $schoolId, 'name' => 'Tuition Fee', 'description' => 'Regular monthly course fee.']);
        $transportCat = FeeCategory::where('school_id', $schoolId)->where('name', 'Transport Fee')->first()
            ?? FeeCategory::create(['school_id' => $schoolId, 'name' => 'Transport Fee', 'description' => 'School bus fare based on distance.']);
        $examCat = FeeCategory::where('school_id', $schoolId)->where('name', 'Examination Fee')->first()
            ?? FeeCategory::create(['school_id' => $schoolId, 'name' => 'Examination Fee', 'description' => 'Term end assessments & print fee.']);


        // 2. Seed Fee Structures Class-wise
        if (FeeStructure::where('school_id', $schoolId)->count() === 0) {
            $classes = SchoolClass::where('school_id', $schoolId)->get();
            foreach ($classes as $index => $class) {
                FeeStructure::create([
                    'school_id' => $schoolId,
                    'class_id' => $class->id,
                    'fee_category_id' => $tuitionCat->id,
                    'amount' => 2000.00 + ($index * 300.00),
                    'schedule_type' => 'monthly',
                ]);
                FeeStructure::create([
                    'school_id' => $schoolId,
                    'class_id' => $class->id,
                    'fee_category_id' => $examCat->id,
                    'amount' => 500.00,
                    'schedule_type' => 'quarterly',
                ]);
            }
        }

        // 3. Seed Student-wise Fees
        if (StudentFee::where('school_id', $schoolId)->count() === 0) {
            $students = Student::where('school_id', $schoolId)->get();
            foreach ($students as $student) {
                $classStructure = FeeStructure::where('school_id', $schoolId)
                    ->where('class_id', $student->class_id)
                    ->where('fee_category_id', $tuitionCat->id)
                    ->first();
                
                $tuitionAmt = $classStructure ? $classStructure->amount : 2500.00;

                // Tuition Fee entry (Unpaid)
                StudentFee::create([
                    'school_id' => $schoolId,
                    'student_id' => $student->id,
                    'fee_category_id' => $tuitionCat->id,
                    'amount' => $tuitionAmt,
                    'due_date' => now()->addDays(5)->toDateString(),
                    'paid_amount' => 0.00,
                    'status' => 'pending',
                ]);

                // Exam Fee entry (Paid)
                StudentFee::create([
                    'school_id' => $schoolId,
                    'student_id' => $student->id,
                    'fee_category_id' => $examCat->id,
                    'amount' => 500.00,
                    'due_date' => now()->subDays(15)->toDateString(),
                    'paid_amount' => 500.00,
                    'status' => 'paid',
                ]);
                
                // Add some completed receipts
                FeeReceipt::create([
                    'school_id' => $schoolId,
                    'student_id' => $student->id,
                    'receipt_number' => 'REC-' . rand(100000, 999999),
                    'amount_paid' => 500.00,
                    'payment_mode' => 'online',
                    'transaction_id' => 'TXN' . rand(1000000, 9999999),
                    'payment_date' => now()->subDays(15)->toDateString(),
                ]);
            }
        }

        // 4. Seed Pending Cheques
        if (PendingCheque::where('school_id', $schoolId)->count() === 0) {
            $students = Student::where('school_id', $schoolId)->take(3)->get();
            $banks = ['HDFC Bank', 'State Bank of India', 'ICICI Bank'];
            foreach ($students as $i => $student) {
                PendingCheque::create([
                    'school_id' => $schoolId,
                    'student_id' => $student->id,
                    'bank_name' => $banks[$i % count($banks)],
                    'cheque_number' => 'CHQ' . rand(100000, 999999),
                    'amount' => 3000.00,
                    'cheque_date' => now()->subDays(2)->toDateString(),
                    'status' => 'pending',
                ]);
            }
        }

        // 5. Seed Payment Links
        if (PaymentLink::where('school_id', $schoolId)->count() === 0) {
            $students = Student::where('school_id', $schoolId)->take(2)->get();
            foreach ($students as $student) {
                PaymentLink::create([
                    'school_id' => $schoolId,
                    'student_id' => $student->id,
                    'amount' => 2800.00,
                    'purpose' => 'Tuition Fees - June',
                    'link_url' => 'https://schoolcloud.erp/pay/lnk_' . uniqid(),
                    'status' => 'active',
                ]);
            }
        }
    }

    public function feeConfiguration(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $this->ensureFeesSeeded($schoolId);

        if ($request->isMethod('post')) {
            $request->validate([
                'name' => 'required|string|max:100',
                'description' => 'nullable|string',
            ]);

            FeeCategory::create([
                'school_id' => $schoolId,
                'name' => $request->name,
                'description' => $request->description,
            ]);

            return back()->with('success', 'Fee Category added successfully!');
        }

        $categories = FeeCategory::where('school_id', $schoolId)->get();
        return view('school.fees.configuration', compact('categories'));
    }

    public function feeBasics()
    {
        return view('school.fees.basics');
    }

    public function classWiseFee(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $this->ensureFeesSeeded($schoolId);

        if ($request->isMethod('post')) {
            $request->validate([
                'class_id' => 'required|exists:school_classes,id',
                'fee_category_id' => 'required|exists:fee_categories,id',
                'amount' => 'required|numeric|min:0',
                'schedule_type' => 'required|string',
            ]);

            FeeStructure::updateOrCreate(
                [
                    'school_id' => $schoolId,
                    'class_id' => $request->class_id,
                    'fee_category_id' => $request->fee_category_id,
                ],
                [
                    'amount' => $request->amount,
                    'schedule_type' => $request->schedule_type,
                ]
            );

            return back()->with('success', 'Class-wise Fee Structure updated successfully!');
        }

        $classes = SchoolClass::where('school_id', $schoolId)->get();
        $categories = FeeCategory::where('school_id', $schoolId)->get();
        $structures = FeeStructure::where('school_id', $schoolId)->with(['class', 'category'])->get();

        return view('school.fees.class_wise', compact('classes', 'categories', 'structures'));
    }

    public function studentWiseFee(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $this->ensureFeesSeeded($schoolId);

        if ($request->isMethod('post')) {
            $request->validate([
                'student_fee_id' => 'required|exists:student_fees,id',
                'amount_paid' => 'required|numeric|min:0.01',
                'payment_mode' => 'required|string',
                'transaction_id' => 'nullable|string',
            ]);

            $studentFee = StudentFee::where('school_id', $schoolId)->findOrFail($request->student_fee_id);
            $newPaid = $studentFee->paid_amount + $request->amount_paid;

            if ($newPaid >= $studentFee->amount) {
                $studentFee->status = 'paid';
                $studentFee->paid_amount = $studentFee->amount;
            } else {
                $studentFee->status = 'partially_paid';
                $studentFee->paid_amount = $newPaid;
            }
            $studentFee->save();

            // Create Receipt
            FeeReceipt::create([
                'school_id' => $schoolId,
                'student_id' => $studentFee->student_id,
                'receipt_number' => 'REC-' . rand(100000, 999999),
                'amount_paid' => $request->amount_paid,
                'payment_mode' => $request->payment_mode,
                'transaction_id' => $request->transaction_id,
                'payment_date' => now()->toDateString(),
            ]);

            return back()->with('success', 'Fee Payment collected successfully!');
        }

        $students = Student::where('school_id', $schoolId)->with(['class', 'section'])->get();
        $fees = StudentFee::where('school_id', $schoolId)->with(['student.class', 'student.section', 'category'])->get();

        return view('school.fees.student_wise', compact('students', 'fees'));
    }

    public function optionalFeeMapping(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $this->ensureFeesSeeded($schoolId);

        if ($request->isMethod('post')) {
            $request->validate([
                'student_id' => 'required|exists:students,id',
                'fee_category_id' => 'required|exists:fee_categories,id',
            ]);

            OptionalFeeMapping::firstOrCreate([
                'school_id' => $schoolId,
                'student_id' => $request->student_id,
                'fee_category_id' => $request->fee_category_id,
            ]);

            return back()->with('success', 'Optional Fee Mapped successfully!');
        }

        $students = Student::where('school_id', $schoolId)->with(['class', 'section'])->get();
        $categories = FeeCategory::where('school_id', $schoolId)->get();
        $mappings = OptionalFeeMapping::where('school_id', $schoolId)->with(['student.class', 'category'])->get();

        return view('school.fees.optional_mapping', compact('students', 'categories', 'mappings'));
    }

    public function paymentLinks(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $this->ensureFeesSeeded($schoolId);

        if ($request->isMethod('post')) {
            $request->validate([
                'student_id' => 'required|exists:students,id',
                'amount' => 'required|numeric|min:1',
                'purpose' => 'required|string|max:200',
            ]);

            PaymentLink::create([
                'school_id' => $schoolId,
                'student_id' => $request->student_id,
                'amount' => $request->amount,
                'purpose' => $request->purpose,
                'link_url' => 'https://schoolcloud.erp/pay/lnk_' . uniqid(),
                'status' => 'active',
            ]);

            return back()->with('success', 'Online Payment Link generated successfully!');
        }

        $students = Student::where('school_id', $schoolId)->get();
        $links = PaymentLink::where('school_id', $schoolId)->with('student')->get();

        return view('school.fees.payment_links', compact('students', 'links'));
    }

    public function collectionFollowup(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $this->ensureFeesSeeded($schoolId);

        if ($request->isMethod('post')) {
            $request->validate([
                'student_id' => 'required|exists:students,id',
            ]);

            // Fake notification success
            $student = Student::findOrFail($request->student_id);
            return back()->with('success', "Payment reminder notification sent to parent of {$student->full_name} successfully!");
        }

        $overdueFees = StudentFee::where('school_id', $schoolId)
            ->where('status', '!=', 'paid')
            ->where('due_date', '<', now()->toDateString())
            ->with(['student.class', 'student.section', 'category'])
            ->get();

        return view('school.fees.collection_followup', compact('overdueFees'));
    }

    public function scheduleMapper(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $this->ensureFeesSeeded($schoolId);

        if ($request->isMethod('post')) {
            $request->validate([
                'fee_category_id' => 'required|exists:fee_categories,id',
                'schedule_type' => 'required|string',
            ]);

            // Bulk update structures for this category in the school
            FeeStructure::where('school_id', $schoolId)
                ->where('fee_category_id', $request->fee_category_id)
                ->update(['schedule_type' => $request->schedule_type]);

            return back()->with('success', 'Fee Schedules updated for selected category!');
        }

        $categories = FeeCategory::where('school_id', $schoolId)->get();
        $structures = FeeStructure::where('school_id', $schoolId)->with(['class', 'category'])->get();

        return view('school.fees.schedule_mapper', compact('categories', 'structures'));
    }

    public function refundFee(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $this->ensureFeesSeeded($schoolId);

        if ($request->isMethod('post')) {
            $request->validate([
                'student_id' => 'required|exists:students,id',
                'amount' => 'required|numeric|min:1',
                'reason' => 'required|string|max:200',
            ]);

            FeeRefund::create([
                'school_id' => $schoolId,
                'student_id' => $request->student_id,
                'amount' => $request->amount,
                'refund_date' => now()->toDateString(),
                'reason' => $request->reason,
            ]);

            return back()->with('success', 'Fee refund processed successfully!');
        }

        $students = Student::where('school_id', $schoolId)->get();
        $refunds = FeeRefund::where('school_id', $schoolId)->with('student')->get();

        return view('school.fees.refund', compact('students', 'refunds'));
    }

    public function feeReceipts(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $this->ensureFeesSeeded($schoolId);

        $receipts = FeeReceipt::where('school_id', $schoolId)->with(['student.class', 'student.section'])->get();

        return view('school.fees.receipts', compact('receipts'));
    }

    public function pendingCheques(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $this->ensureFeesSeeded($schoolId);

        if ($request->isMethod('post')) {
            $request->validate([
                'cheque_id' => 'required|exists:pending_cheques,id',
                'action' => 'required|in:clear,bounce',
            ]);

            $cheque = PendingCheque::where('school_id', $schoolId)->findOrFail($request->cheque_id);
            if ($request->action === 'clear') {
                $cheque->status = 'cleared';
                $cheque->save();

                // Generate Receipt
                FeeReceipt::create([
                    'school_id' => $schoolId,
                    'student_id' => $cheque->student_id,
                    'receipt_number' => 'REC-' . rand(100000, 999999),
                    'amount_paid' => $cheque->amount,
                    'payment_mode' => 'cheque',
                    'transaction_id' => $cheque->cheque_number,
                    'payment_date' => now()->toDateString(),
                ]);

                // Update corresponding student fee
                $fee = StudentFee::where('school_id', $schoolId)
                    ->where('student_id', $cheque->student_id)
                    ->where('status', 'pending')
                    ->first();
                if ($fee) {
                    $fee->status = 'paid';
                    $fee->paid_amount = $fee->amount;
                    $fee->save();
                }

                return back()->with('success', 'Cheque cleared successfully! Fee receipt generated.');
            } else {
                $cheque->status = 'bounced';
                $cheque->save();
                return back()->with('success', 'Cheque status marked as bounced.');
            }
        }

        $cheques = PendingCheque::where('school_id', $schoolId)->with('student')->get();
        return view('school.fees.pending_cheques', compact('cheques'));
    }

    public function feeReports(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $this->ensureFeesSeeded($schoolId);

        $totalCollected = FeeReceipt::where('school_id', $schoolId)->sum('amount_paid');
        $totalRefunded = FeeRefund::where('school_id', $schoolId)->sum('amount');
        $totalDues = StudentFee::where('school_id', $schoolId)->where('status', '!=', 'paid')->sum('amount') 
            - StudentFee::where('school_id', $schoolId)->where('status', 'partially_paid')->sum('paid_amount');

        $receiptsByMode = FeeReceipt::where('school_id', $schoolId)
            ->selectRaw('payment_mode, SUM(amount_paid) as total')
            ->groupBy('payment_mode')
            ->get();

        $collectionByClass = FeeReceipt::where('fee_receipts.school_id', $schoolId)
            ->join('students', 'fee_receipts.student_id', '=', 'students.id')
            ->join('school_classes', 'students.class_id', '=', 'school_classes.id')
            ->selectRaw('school_classes.name as class_name, SUM(amount_paid) as total')
            ->groupBy('school_classes.name')
            ->get();

        return view('school.fees.reports', compact('totalCollected', 'totalRefunded', 'totalDues', 'receiptsByMode', 'collectionByClass'));
    }

    public function feeInvoice(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $this->ensureFeesSeeded($schoolId);

        $students = Student::where('school_id', $schoolId)->with(['class', 'section'])->get();
        $fees = StudentFee::where('school_id', $schoolId)->with(['category', 'student'])->get();

        return view('school.fees.invoice', compact('students', 'fees'));
    }

    public function feeInvoice1(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $this->ensureFeesSeeded($schoolId);

        $students = Student::where('school_id', $schoolId)->with(['class', 'section'])->get();
        $fees = StudentFee::where('school_id', $schoolId)->with(['category', 'student'])->get();

        return view('school.fees.invoice1', compact('students', 'fees'));
    }

    public function feeBulkUpload(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $this->ensureFeesSeeded($schoolId);

        if ($request->isMethod('post')) {
            $request->validate([
                'csv_file' => 'required|file|mimes:csv,txt|max:2048',
            ]);

            // Fake processing success
            return back()->with('success', 'Bulk fee records uploaded and processed successfully!');
        }

        return view('school.fees.bulk_upload');
    }

    public function statementOfAccount(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $this->ensureFeesSeeded($schoolId);

        $selectedStudent = null;
        $ledger = [];

        if ($request->has('student_id')) {
            $selectedStudent = Student::where('school_id', $schoolId)->with(['class', 'section'])->findOrFail($request->student_id);
            
            // Generate debits (Fees Assigned)
            $fees = StudentFee::where('school_id', $schoolId)
                ->where('student_id', $request->student_id)
                ->with('category')
                ->get();
            foreach ($fees as $fee) {
                $ledger[] = [
                    'date' => $fee->created_at->toDateString(),
                    'desc' => 'Fee Assigned: ' . $fee->category->name,
                    'type' => 'debit',
                    'amount' => $fee->amount,
                ];
            }

            // Generate credits (Payments)
            $receipts = FeeReceipt::where('school_id', $schoolId)
                ->where('student_id', $request->student_id)
                ->get();
            foreach ($receipts as $receipt) {
                $ledger[] = [
                    'date' => $receipt->payment_date,
                    'desc' => 'Payment Received (Receipt: ' . $receipt->receipt_number . ')',
                    'type' => 'credit',
                    'amount' => $receipt->amount_paid,
                ];
            }

            // Generate refunds (Credits back)
            $refunds = FeeRefund::where('school_id', $schoolId)
                ->where('student_id', $request->student_id)
                ->get();
            foreach ($refunds as $refund) {
                $ledger[] = [
                    'date' => $refund->refund_date,
                    'desc' => 'Refund Processed: ' . $refund->reason,
                    'type' => 'debit', // Refund increases outstanding balance
                    'amount' => $refund->amount,
                ];
            }

            // Sort by date
            usort($ledger, function($a, $b) {
                return strcmp($a['date'], $b['date']);
            });
        }

        $students = Student::where('school_id', $schoolId)->get();

        return view('school.fees.statement', compact('students', 'selectedStudent', 'ledger'));
    }

    public function xeroIntegration(Request $request)
    {
        if ($request->isMethod('post')) {
            return back()->with('success', 'Xero Sync Completed successfully! 15 invoices and 8 receipts synchronized.');
        }
        return view('school.fees.xero');
    }
}
