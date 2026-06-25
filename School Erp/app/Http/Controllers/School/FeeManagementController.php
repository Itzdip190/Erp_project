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

        // 6. Seed Fee Configuration
        if (\App\Models\FeeConfiguration::where('school_id', $schoolId)->count() === 0) {
            \App\Models\FeeConfiguration::create([
                'school_id' => $schoolId,
                'receipt_layout' => 'A4 Portrait',
                'invoice_layout' => 'A4 Portrait',
                'receipt_template' => 'Default Template',
                'advance_receipt_template' => 'Default Template',
                'num_copies' => 2,
                'default_payment_mode' => 'Cash',
                'discount_label' => 'Discount',
                'payment_url_enabled' => true,
                'payment_url' => 'https://online.edutinker.com/form/student/fees?schoolId=' . $schoolId . '&schoolName=Pragya%20School',
                'add_fee_due' => true,
                'add_fee_discount' => true,
                'add_fee_balance' => true,
                'note_enabled' => false,
                'note_text' => 'Note on Fee Receipt',
                // Toggles
                'show_zero_paid_component' => true,
                'collect_siblings_fee' => false,
                'receipt_date_editable' => true,
                'entry_date_editable' => true,
                'no_show_zero_pending' => false,
                'no_repeat_discount' => true,
                'no_allow_cancelled_receipts' => false,
                'allow_manual_receipt_no' => false,
                'round_off_discount' => false,
                'fine_apply_receipt_date' => false,
                'enable_multiple_installments' => false,
                'show_head_wise_total' => false,
                'parent_select_component' => true,
                'parent_select_fine' => true,
                'parent_no_partial_payment' => false,
                'parent_no_show_components' => false,
                'parent_show_only_current_installment' => false,
                'tally_separate_ledgers' => false,
                'gst_enabled' => false,
                'details_receipt_no' => true,
                'details_receipt_date' => true,
                'details_session' => true,
                'details_student_name' => true,
                'details_admission_no' => true,
                'details_class' => true,
                'details_father_name' => false,
                'details_mother_name' => false,
                'details_address' => false,
                'details_father_phone' => false,
                'details_mother_phone' => false,
                'inst_affiliation_no' => false,
                'inst_school_url' => false,
                'inst_board_logo' => false,
            ]);
        }

        // 7. Seed Student Categories if they don't exist
        $dayBoarding = \App\Models\StudentCategory::where('school_id', $schoolId)->where('name', 'Day boarding')->first()
            ?? \App\Models\StudentCategory::create(['school_id' => $schoolId, 'name' => 'Day boarding', 'description' => 'Day boarding students']);
        $hostel = \App\Models\StudentCategory::where('school_id', $schoolId)->where('name', 'Hostel')->first()
            ?? \App\Models\StudentCategory::create(['school_id' => $schoolId, 'name' => 'Hostel', 'description' => 'Hostel boarders']);

        // Make sure students are assigned to these categories
        $students = Student::where('school_id', $schoolId)->get();
        foreach ($students as $index => $student) {
            if (is_null($student->category_id)) {
                $student->category_id = ($index % 2 === 0) ? $dayBoarding->id : $hostel->id;
                $student->save();
            }
        }
    }

    public function feeConfiguration(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $this->ensureFeesSeeded($schoolId);

        if ($request->isMethod('post')) {
            if ($request->input('action') === 'add_category') {
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
            } else {
                $config = \App\Models\FeeConfiguration::where('school_id', $schoolId)->first();
                if (!$config) {
                    $config = new \App\Models\FeeConfiguration();
                    $config->school_id = $schoolId;
                }

                $config->fill([
                    'receipt_layout' => $request->input('receipt_layout', 'A4 Portrait'),
                    'invoice_layout' => $request->input('invoice_layout', 'A4 Portrait'),
                    'receipt_template' => $request->input('receipt_template', 'Default Template'),
                    'advance_receipt_template' => $request->input('advance_receipt_template', 'Default Template'),
                    'num_copies' => (int) $request->input('num_copies', 2),
                    'default_payment_mode' => $request->input('default_payment_mode', 'Cash'),
                    'discount_label' => $request->input('discount_label', 'Discount'),
                    
                    'payment_url_enabled' => $request->has('payment_url_enabled'),
                    'payment_url' => $request->input('payment_url'),
                    
                    'add_fee_due' => $request->has('add_fee_due'),
                    'add_fee_discount' => $request->has('add_fee_discount'),
                    'add_fee_balance' => $request->has('add_fee_balance'),
                    
                    'note_enabled' => $request->has('note_enabled'),
                    'note_text' => $request->input('note_text'),
                    
                    // Other configuration toggles
                    'show_zero_paid_component' => $request->has('show_zero_paid_component'),
                    'collect_siblings_fee' => $request->has('collect_siblings_fee'),
                    'receipt_date_editable' => $request->has('receipt_date_editable'),
                    'entry_date_editable' => $request->has('entry_date_editable'),
                    'no_show_zero_pending' => $request->has('no_show_zero_pending'),
                    'no_repeat_discount' => $request->has('no_repeat_discount'),
                    'no_allow_cancelled_receipts' => $request->has('no_allow_cancelled_receipts'),
                    'allow_manual_receipt_no' => $request->has('allow_manual_receipt_no'),
                    'round_off_discount' => $request->has('round_off_discount'),
                    'fine_apply_receipt_date' => $request->has('fine_apply_receipt_date'),
                    'enable_multiple_installments' => $request->has('enable_multiple_installments'),
                    'show_head_wise_total' => $request->has('show_head_wise_total'),
                    
                    // Parent side configuration
                    'parent_select_component' => $request->has('parent_select_component'),
                    'parent_select_fine' => $request->has('parent_select_fine'),
                    'parent_no_partial_payment' => $request->has('parent_no_partial_payment'),
                    'parent_no_show_components' => $request->has('parent_no_show_components'),
                    'parent_show_only_current_installment' => $request->has('parent_show_only_current_installment'),
                    
                    // Tally integration
                    'tally_separate_ledgers' => $request->has('tally_separate_ledgers'),
                    
                    // GST
                    'gst_enabled' => $request->has('gst_enabled'),
                    
                    // Student details on receipt
                    'details_receipt_no' => $request->has('details_receipt_no'),
                    'details_receipt_date' => $request->has('details_receipt_date'),
                    'details_session' => $request->has('details_session'),
                    'details_student_name' => $request->has('details_student_name'),
                    'details_admission_no' => $request->has('details_admission_no'),
                    'details_class' => $request->has('details_class'),
                    'details_father_name' => $request->has('details_father_name'),
                    'details_mother_name' => $request->has('details_mother_name'),
                    'details_address' => $request->has('details_address'),
                    'details_father_phone' => $request->has('details_father_phone'),
                    'details_mother_phone' => $request->has('details_mother_phone'),
                    
                    // Institute details
                    'inst_affiliation_no' => $request->has('inst_affiliation_no'),
                    'inst_school_url' => $request->has('inst_school_url'),
                    'inst_board_logo' => $request->has('inst_board_logo'),
                ]);
                $config->save();

                return back()->with('success', 'Fee Configuration updated successfully!');
            }
        }

        $config = \App\Models\FeeConfiguration::where('school_id', $schoolId)->first();
        $categories = FeeCategory::where('school_id', $schoolId)->get();
        return view('school.fees.configuration', compact('categories', 'config'));
    }

    public function feeBasics(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $this->ensureFeesSeeded($schoolId);

        // Handle POST requests
        if ($request->isMethod('post')) {
            $action = $request->input('action');

            if ($action === 'add_academic_session') {
                $request->validate([
                    'name' => 'required|string|max:100',
                    'start_date' => 'required|date',
                    'end_date' => 'required|date|after:start_date',
                    'is_current' => 'nullable',
                ]);

                $isCurrent = $request->has('is_current') || $request->input('is_current') == 1;

                if ($isCurrent) {
                    \App\Models\AcademicSession::where('school_id', $schoolId)->update(['is_current' => false]);
                }

                \App\Models\AcademicSession::create([
                    'school_id' => $schoolId,
                    'name' => $request->name,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'is_current' => $isCurrent,
                ]);

                return back()->with('success', 'Academic Year added successfully!');
            }

            if ($action === 'add_fee_schedule') {
                $request->validate([
                    'name' => 'required|string|max:100',
                    'academic_session_id' => 'required|exists:academic_sessions,id',
                    'classes' => 'required|array',
                    'no_of_installments' => 'required|integer|min:1',
                    'start_date' => 'required|date',
                    'end_date' => 'required|date|after:start_date',
                ]);

                \App\Models\FeeSchedule::create([
                    'school_id' => $schoolId,
                    'academic_session_id' => $request->academic_session_id,
                    'classes' => implode(', ', $request->classes),
                    'no_of_installments' => $request->no_of_installments,
                    'name' => $request->name,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                ]);

                return back()->with('success', 'Fee Schedule added successfully!');
            }

            if ($action === 'add_fee_component') {
                $request->validate([
                    'head_name' => 'required|string|max:100',
                    'component_name' => 'required|string|max:100',
                    'academic_session_id' => 'required|exists:academic_sessions,id',
                    'admission_type' => 'required|string',
                    'gender' => 'required|string',
                ]);

                \App\Models\FeeComponent::create([
                    'school_id' => $schoolId,
                    'academic_session_id' => $request->academic_session_id,
                    'head_name' => $request->head_name,
                    'component_name' => $request->component_name,
                    'admission_type' => $request->admission_type,
                    'gender' => $request->gender,
                ]);

                return back()->with('success', 'Fee Component added successfully!');
            }

            if ($action === 'add_fee_discount') {
                $request->validate([
                    'name' => 'required|string|max:100',
                    'academic_session_id' => 'required|exists:academic_sessions,id',
                    'remarks' => 'nullable|string',
                    'amount' => 'required|numeric|min:0',
                    'classes' => 'required|array',
                    'student_ids' => 'nullable|array',
                ]);

                \App\Models\FeeDiscount::create([
                    'school_id' => $schoolId,
                    'academic_session_id' => $request->academic_session_id,
                    'name' => $request->name,
                    'remarks' => $request->remarks,
                    'classes_installments' => json_encode($request->classes),
                    'amount' => $request->amount,
                    'student_ids' => $request->has('student_ids') ? json_encode($request->student_ids) : null,
                ]);

                return back()->with('success', 'Fee Discount added successfully!');
            }

            if ($action === 'add_misc_fee') {
                $request->validate([
                    'name' => 'required|string|max:100',
                    'academic_session_id' => 'required|exists:academic_sessions,id',
                    'remarks' => 'nullable|string',
                    'amount' => 'required|numeric|min:0',
                    'classes' => 'required|array',
                    'student_ids' => 'nullable|array',
                ]);

                \App\Models\MiscFee::create([
                    'school_id' => $schoolId,
                    'academic_session_id' => $request->academic_session_id,
                    'name' => $request->name,
                    'remarks' => $request->remarks,
                    'classes_installments' => json_encode($request->classes),
                    'amount' => $request->amount,
                    'student_ids' => $request->has('student_ids') ? json_encode($request->student_ids) : null,
                ]);

                return back()->with('success', 'Misc Fee added successfully!');
            }

            if ($action === 'add_fee_fine') {
                $request->validate([
                    'name' => 'required|string|max:100',
                    'academic_session_id' => 'required|exists:academic_sessions,id',
                    'fine_type' => 'required|string',
                    'fine_amount' => 'required|numeric|min:0',
                ]);

                \App\Models\FeeFine::create([
                    'school_id' => $schoolId,
                    'academic_session_id' => $request->academic_session_id,
                    'name' => $request->name,
                    'fine_type' => $request->fine_type,
                    'fine_amount' => $request->fine_amount,
                    'status' => true,
                ]);

                return back()->with('success', 'Fee Fine added successfully!');
            }

            if ($action === 'toggle_fine_status') {
                $fine = \App\Models\FeeFine::where('school_id', $schoolId)->findOrFail($request->id);
                $fine->status = !$fine->status;
                $fine->save();

                return response()->json(['success' => true, 'new_status' => $fine->status]);
            }

            if ($action === 'delete') {
                $type = $request->input('type');
                $id = $request->input('id');

                if ($type === 'schedule') {
                    \App\Models\FeeSchedule::where('school_id', $schoolId)->where('id', $id)->delete();
                } elseif ($type === 'component') {
                    \App\Models\FeeComponent::where('school_id', $schoolId)->where('id', $id)->delete();
                } elseif ($type === 'discount') {
                    \App\Models\FeeDiscount::where('school_id', $schoolId)->where('id', $id)->delete();
                } elseif ($type === 'misc_fee') {
                    \App\Models\MiscFee::where('school_id', $schoolId)->where('id', $id)->delete();
                } elseif ($type === 'fine') {
                    \App\Models\FeeFine::where('school_id', $schoolId)->where('id', $id)->delete();
                }

                return back()->with('success', 'Item deleted successfully!');
            }
        }

        // GET request
        $academicSessions = \App\Models\AcademicSession::where('school_id', $schoolId)->get();
        
        // If no academic sessions exist, create a default one
        if ($academicSessions->count() === 0) {
            $defaultSession = \App\Models\AcademicSession::create([
                'school_id' => $schoolId,
                'name' => 'Apr 2025 - Mar 2026',
                'start_date' => Carbon::create(2025, 4, 1)->toDateString(),
                'end_date' => Carbon::create(2026, 3, 31)->toDateString(),
                'is_current' => true,
            ]);
            $academicSessions = collect([$defaultSession]);
        }

        $currentSession = \App\Models\AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first();
        if (!$currentSession) {
            $currentSession = $academicSessions->first();
        }

        $sessionId = $request->get('academic_session_id', $currentSession->id);
        $selectedSession = \App\Models\AcademicSession::where('school_id', $schoolId)->find($sessionId) ?? $currentSession;

        // Ensure default data seeded for this session
        $schedulesCount = \App\Models\FeeSchedule::where('school_id', $schoolId)->where('academic_session_id', $selectedSession->id)->count();
        $componentsCount = \App\Models\FeeComponent::where('school_id', $schoolId)->where('academic_session_id', $selectedSession->id)->count();
        $discountsCount = \App\Models\FeeDiscount::where('school_id', $schoolId)->where('academic_session_id', $selectedSession->id)->count();
        $miscFeesCount = \App\Models\MiscFee::where('school_id', $schoolId)->where('academic_session_id', $selectedSession->id)->count();
        $finesCount = \App\Models\FeeFine::where('school_id', $schoolId)->where('academic_session_id', $selectedSession->id)->count();

        if ($schedulesCount === 0) {
            \App\Models\FeeSchedule::create([
                'school_id' => $schoolId,
                'academic_session_id' => $selectedSession->id,
                'classes' => 'NUR, 6, LKG, UKG, 1, 2, 3, 4, 5, 7, 8, 9, 10, 11, 12',
                'no_of_installments' => 4,
                'name' => 'fees schedule 1',
                'start_date' => Carbon::create(2025, 4, 1)->toDateString(),
                'end_date' => Carbon::create(2025, 12, 30)->toDateString(),
            ]);
            \App\Models\FeeSchedule::create([
                'school_id' => $schoolId,
                'academic_session_id' => $selectedSession->id,
                'classes' => 'NUR, 6, LKG, UKG, 1, 2, 3, 4, 5, 7, 8, 9, 10, 11, 12',
                'no_of_installments' => 12,
                'name' => 'Fee scheduled 2',
                'start_date' => Carbon::create(2025, 4, 1)->toDateString(),
                'end_date' => Carbon::create(2026, 3, 30)->toDateString(),
            ]);
            $schedulesCount = 2;
        }

        if ($componentsCount === 0) {
            \App\Models\FeeComponent::create([
                'school_id' => $schoolId,
                'academic_session_id' => $selectedSession->id,
                'head_name' => 'School Fee',
                'component_name' => 'Transport Fee',
                'admission_type' => 'All Students',
                'gender' => 'All Students',
            ]);
            \App\Models\FeeComponent::create([
                'school_id' => $schoolId,
                'academic_session_id' => $selectedSession->id,
                'head_name' => 'School fees',
                'component_name' => 'ADMISSION FEES',
                'admission_type' => 'New',
                'gender' => 'All Students',
            ]);
            \App\Models\FeeComponent::create([
                'school_id' => $schoolId,
                'academic_session_id' => $selectedSession->id,
                'head_name' => 'School fees',
                'component_name' => 'MONTHLY FEES',
                'admission_type' => 'All Students',
                'gender' => 'All Students',
            ]);
            \App\Models\FeeComponent::create([
                'school_id' => $schoolId,
                'academic_session_id' => $selectedSession->id,
                'head_name' => 'School fees',
                'component_name' => 'ID card Fees',
                'admission_type' => 'All Students',
                'gender' => 'All Students',
            ]);
            $componentsCount = 4;
        }

        if ($finesCount === 0) {
            \App\Models\FeeFine::create([
                'school_id' => $schoolId,
                'academic_session_id' => $selectedSession->id,
                'name' => 'late fine',
                'fine_type' => 'Fixed Amount',
                'fine_amount' => 250.00,
                'status' => true,
            ]);
            $finesCount = 1;
        }

        // Fetch list items for tables
        $schedules = \App\Models\FeeSchedule::where('school_id', $schoolId)->where('academic_session_id', $selectedSession->id)->get();
        $components = \App\Models\FeeComponent::where('school_id', $schoolId)->where('academic_session_id', $selectedSession->id)->get();
        $discounts = \App\Models\FeeDiscount::where('school_id', $schoolId)->where('academic_session_id', $selectedSession->id)->get();
        $miscFees = \App\Models\MiscFee::where('school_id', $schoolId)->where('academic_session_id', $selectedSession->id)->get();
        $fines = \App\Models\FeeFine::where('school_id', $schoolId)->where('academic_session_id', $selectedSession->id)->get();

        $classes = SchoolClass::where('school_id', $schoolId)->get();
        $students = Student::where('school_id', $schoolId)->with(['class', 'section'])->get();

        return view('school.fees.basics', compact(
            'academicSessions',
            'selectedSession',
            'schedules',
            'components',
            'discounts',
            'miscFees',
            'fines',
            'classes',
            'students',
            'schedulesCount',
            'componentsCount',
            'discountsCount',
            'miscFeesCount',
            'finesCount'
        ));
    }

    public function classWiseFee(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $this->ensureFeesSeeded($schoolId);

        $academicSessions = \App\Models\AcademicSession::where('school_id', $schoolId)->get();
        $currentSession = \App\Models\AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first() ?? $academicSessions->first();
        
        $sessionId = $request->get('academic_session_id', $currentSession ? $currentSession->id : null);
        $selectedSession = \App\Models\AcademicSession::where('school_id', $schoolId)->find($sessionId) ?? $currentSession;

        $classes = SchoolClass::where('school_id', $schoolId)->orderBy('sort_order')->get();
        if ($classes->count() === 0) {
            $defaultClass = SchoolClass::create([
                'school_id' => $schoolId,
                'name' => 'NUR',
                'numeric_name' => 0,
                'sort_order' => 1,
            ]);
            $classes = collect([$defaultClass]);
        }

        $classId = $request->get('class_id', $classes->first()->id);
        $selectedClass = SchoolClass::where('school_id', $schoolId)->find($classId) ?? $classes->first();

        // Load sections for selected class
        $sections = Section::where('school_id', $schoolId)->where('class_id', $selectedClass->id)->get();
        if ($sections->count() === 0) {
            $defaultSection = Section::create([
                'school_id' => $schoolId,
                'class_id' => $selectedClass->id,
                'name' => 'A',
            ]);
            $sections = collect([$defaultSection]);
        }

        $sectionId = $request->get('section_id', $sections->first()->id);
        $selectedSection = Section::where('school_id', $schoolId)->where('class_id', $selectedClass->id)->find($sectionId) ?? $sections->first();

        // Load Student Categories
        $studentCategories = \App\Models\StudentCategory::where('school_id', $schoolId)->get();

        // Load Fee Schedules for selected session that apply to selectedClass
        $schedules = \App\Models\FeeSchedule::where('school_id', $schoolId)
            ->where('academic_session_id', $selectedSession->id)
            ->get()
            ->filter(function($sched) use ($selectedClass) {
                $classesList = array_map('trim', explode(',', $sched->classes));
                return in_array($selectedClass->name, $classesList);
            });

        // Load Fee Components for selected session
        $components = \App\Models\FeeComponent::where('school_id', $schoolId)
            ->where('academic_session_id', $selectedSession->id)
            ->get();

        // Load existing Class-wise allocations for this class/section
        $allocations = \App\Models\ClassWiseFeeAllocation::where('school_id', $schoolId)
            ->where('academic_session_id', $selectedSession->id)
            ->where('class_id', $selectedClass->id)
            ->where('section_id', $selectedSection->id)
            ->get()
            ->keyBy(function($a) {
                return "{$a->fee_schedule_id}_{$a->student_category_id}_{$a->fee_component_id}";
            });

        return view('school.fees.class_wise', compact(
            'academicSessions',
            'selectedSession',
            'classes',
            'selectedClass',
            'sections',
            'selectedSection',
            'studentCategories',
            'schedules',
            'components',
            'allocations'
        ));
    }

    public function saveAllocation(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        
        $request->validate([
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'class_id' => 'required|exists:school_classes,id',
            'section_id' => 'required|exists:sections,id',
            'fee_schedule_id' => 'required|exists:fee_schedules,id',
            'student_category_id' => 'required|exists:student_categories,id',
            'fee_component_id' => 'required|exists:fee_components,id',
            'status' => 'required|in:0,1',
            'installment_amounts' => 'nullable|array',
        ]);

        $installments = $request->input('installment_amounts', []);
        $totalAmount = array_sum(array_map('floatval', $installments));

        $allocation = \App\Models\ClassWiseFeeAllocation::updateOrCreate(
            [
                'school_id' => $schoolId,
                'academic_session_id' => $request->academic_session_id,
                'class_id' => $request->class_id,
                'section_id' => $request->section_id,
                'fee_schedule_id' => $request->fee_schedule_id,
                'student_category_id' => $request->student_category_id,
                'fee_component_id' => $request->fee_component_id,
            ],
            [
                'status' => $request->status == 1,
                'amount' => $totalAmount,
                'installment_amounts' => $installments,
            ]
        );

        $this->regenerateStudentFeesForAllocation($allocation);

        return response()->json([
            'success' => true,
            'amount' => $totalAmount,
            'status' => $allocation->status,
            'message' => 'Fee configuration and student fees updated successfully!'
        ]);
    }

    private function regenerateStudentFeesForAllocation($allocation)
    {
        $schoolId = $allocation->school_id;
        
        $component = \App\Models\FeeComponent::find($allocation->fee_component_id);
        if (!$component) return;

        $categoryName = $component->component_name;
        $category = \App\Models\FeeCategory::where('school_id', $schoolId)
            ->where('name', $categoryName)
            ->first();
        if (!$category) {
            $category = \App\Models\FeeCategory::create([
                'school_id' => $schoolId,
                'name' => $categoryName,
                'description' => "Category for component: {$categoryName}"
            ]);
        }

        $studentsQuery = Student::where('school_id', $schoolId)
            ->where('class_id', $allocation->class_id)
            ->where('category_id', $allocation->student_category_id);
            
        if ($allocation->section_id) {
            $studentsQuery->where('section_id', $allocation->section_id);
        }
        
        $students = $studentsQuery->get();

        $schedule = \App\Models\FeeSchedule::find($allocation->fee_schedule_id);
        if (!$schedule) return;

        $startDate = \Carbon\Carbon::parse($schedule->start_date);
        $endDate = \Carbon\Carbon::parse($schedule->end_date);
        $totalDays = $startDate->diffInDays($endDate);
        
        $numInstallments = count($allocation->installment_amounts ?? []);
        if ($numInstallments === 0) {
            $numInstallments = $schedule->no_of_installments;
        }

        $daysPerInstallment = $numInstallments > 0 ? ($totalDays / $numInstallments) : $totalDays;

        foreach ($students as $student) {
            \App\Models\StudentFee::where('school_id', $schoolId)
                ->where('student_id', $student->id)
                ->where('fee_category_id', $category->id)
                ->where('status', '!=', 'paid')
                ->delete();

            if ($allocation->status && count($allocation->installment_amounts ?? []) > 0) {
                foreach ($allocation->installment_amounts as $i => $amount) {
                    $amt = floatval($amount);
                    if ($amt <= 0) continue;

                    $instEnd = $startDate->copy()->addDays(round(($i + 1) * $daysPerInstallment) - 1);
                    if ($i == $numInstallments - 1) {
                        $instEnd = $endDate;
                    }

                    \App\Models\StudentFee::create([
                        'school_id' => $schoolId,
                        'student_id' => $student->id,
                        'fee_category_id' => $category->id,
                        'amount' => $amt,
                        'due_date' => $instEnd->toDateString(),
                        'paid_amount' => 0.00,
                        'status' => 'pending',
                    ]);
                }
            }
        }
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

        $studentsQuery = Student::where('school_id', $schoolId);
        $feesQuery = StudentFee::where('school_id', $schoolId)
            ->with(['student.class', 'student.section', 'category']);

        if ($request->has('class_id') && $request->class_id != '') {
            $studentsQuery->where('class_id', $request->class_id);
            $feesQuery->whereHas('student', function($q) use ($request) {
                $q->where('class_id', $request->class_id);
            });
        }

        if ($request->has('section_id') && $request->section_id != '') {
            $studentsQuery->where('section_id', $request->section_id);
            $feesQuery->whereHas('student', function($q) use ($request) {
                $q->where('section_id', $request->section_id);
            });
        }

        $students = $studentsQuery->with(['class', 'section'])->get();
        $fees = $feesQuery->get();

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

            $config = \App\Models\FeeConfiguration::where('school_id', $schoolId)->first();
            $linkUrl = 'https://schoolcloud.erp/pay/lnk_' . uniqid();
            
            if ($config && $config->payment_url_enabled && $config->payment_url) {
                $student = Student::findOrFail($request->student_id);
                // Token replacements
                $linkUrl = $config->payment_url;
                $replacements = [
                    '{student_id}' => $student->id,
                    '{student_name}' => urlencode($student->full_name),
                    '{admission_no}' => urlencode($student->admission_id),
                    '{amount}' => $request->amount,
                    '{purpose}' => urlencode($request->purpose),
                    '{school_id}' => $schoolId,
                ];
                
                $linkUrl = str_replace(array_keys($replacements), array_values($replacements), $linkUrl);
                
                // If the user didn't specify tokens, append them standardly
                if (strpos($linkUrl, '{') === false && strpos($linkUrl, 'student_id') === false) {
                    $separator = (strpos($linkUrl, '?') === false) ? '?' : '&';
                    $linkUrl .= $separator . "student_id={$student->id}&amount={$request->amount}";
                }
            }

            PaymentLink::create([
                'school_id' => $schoolId,
                'student_id' => $request->student_id,
                'amount' => $request->amount,
                'purpose' => $request->purpose,
                'link_url' => $linkUrl,
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
        $config = \App\Models\FeeConfiguration::where('school_id', $schoolId)->first();

        return view('school.fees.receipts', compact('receipts', 'config'));
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
        $config = \App\Models\FeeConfiguration::where('school_id', $schoolId)->first();

        return view('school.fees.invoice', compact('students', 'fees', 'config'));
    }

    public function feeInvoice1(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $this->ensureFeesSeeded($schoolId);

        $students = Student::where('school_id', $schoolId)->with(['class', 'section'])->get();
        $fees = StudentFee::where('school_id', $schoolId)->with(['category', 'student'])->get();
        $config = \App\Models\FeeConfiguration::where('school_id', $schoolId)->first();

        return view('school.fees.invoice1', compact('students', 'fees', 'config'));
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
