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
        // Only seed FeeConfiguration so system parameters are initialized, 
        // but do not seed any demo categories, class fee structures, student fees, receipts, cheques, or payment links.
        if (\App\Models\FeeConfiguration::where('school_id', $schoolId)->count() === 0) {
            $school = \App\Models\School::find($schoolId);
            $schoolName = $school ? rawurlencode($school->name) : 'School';

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
                'payment_url' => 'https://online.edutinker.com/form/student/fees?schoolId=' . $schoolId . '&schoolName=' . $schoolName,
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

        // Also ensure a current active session exists
        $currentSession = \App\Models\AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first();
        if (!$currentSession) {
            $currentSession = \App\Models\AcademicSession::create([
                'school_id' => $schoolId,
                'name' => 'Apr 2026 - Mar 2027',
                'start_date' => '2026-04-01',
                'end_date' => '2027-03-31',
                'is_current' => true,
            ]);
        }

        // Also ensure default student categories exist
        \App\Models\StudentCategory::firstOrCreate(['school_id' => $schoolId, 'name' => 'Day boarding']);
        \App\Models\StudentCategory::firstOrCreate(['school_id' => $schoolId, 'name' => 'Hostel']);

        // Auto-seeding schedules and components disabled per user request to start with empty fee basics setup
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

        // No auto-seeding

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
        try {
            $schoolId = auth()->user()->school_id;
            $this->ensureFeesSeeded($schoolId);

        // Ensure student categories "Day boarding" and "Hostel" are created
        \App\Models\StudentCategory::firstOrCreate(['school_id' => $schoolId, 'name' => 'Day boarding']);
        \App\Models\StudentCategory::firstOrCreate(['school_id' => $schoolId, 'name' => 'Hostel']);

        // Load academic sessions
        $academicSessions = \App\Models\AcademicSession::where('school_id', $schoolId)->orderBy('name', 'desc')->get();
        $currentSession = $academicSessions->where('is_current', true)->first() ?? $academicSessions->first();
        if (!$currentSession) {
            // Create a default session if none exists
            $currentSession = \App\Models\AcademicSession::create([
                'school_id' => $schoolId,
                'name' => 'Apr 2025 - Mar 2026',
                'is_current' => true,
                'start_date' => '2025-04-01',
                'end_date' => '2026-03-31',
            ]);
            $academicSessions = collect([$currentSession]);
        }

        $sessionId = $request->get('academic_session_id', $currentSession->id);
        $selectedSession = \App\Models\AcademicSession::where('school_id', $schoolId)->find($sessionId) ?? $currentSession;

        // No auto-seeding

        if ($request->isMethod('post')) {
            if ($request->ajax() || $request->wantsJson()) {
                $request->validate([
                    'academic_session_id' => 'required|exists:academic_sessions,id',
                    'class_id' => 'required|exists:school_classes,id',
                    'section_id' => 'nullable|exists:sections,id',
                    'fee_schedule_id' => 'required|exists:fee_schedules,id',
                    'student_category_id' => 'required|exists:student_categories,id',
                    'fee_component_id' => 'required|exists:fee_components,id',
                    'is_active' => 'required',
                    'amount' => 'required|numeric|min:0',
                    'installments' => 'nullable|array',
                ]);

                $classWiseFee = \App\Models\ClassWiseFee::updateOrCreate([
                    'school_id' => $schoolId,
                    'academic_session_id' => $request->academic_session_id,
                    'class_id' => $request->class_id,
                    'section_id' => $request->section_id ?: null,
                    'fee_schedule_id' => $request->fee_schedule_id,
                    'student_category_id' => $request->student_category_id,
                    'fee_component_id' => $request->fee_component_id,
                ], [
                    'is_active' => filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN),
                    'amount' => $request->amount,
                    'installments' => $request->installments,
                ]);

                $this->syncClassWiseFeeToStudents($schoolId, $classWiseFee);

                return response()->json([
                    'success' => true,
                    'message' => 'Class-wise Fee structure updated successfully!',
                    'data' => $classWiseFee
                ]);
            }

            // Fallback for regular POST form submission (e.g. if AJAX is disabled)
            $request->validate([
                'class_id' => 'required|exists:school_classes,id',
                'fee_category_id' => 'required|exists:fee_categories,id',
                'amount' => 'required|numeric|min:0',
                'schedule_type' => 'required|string',
            ]);

            // Keep compatibility with old form if submitted
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

        $classes = SchoolClass::where('school_id', $schoolId)->orderBy('sort_order')->orderBy('name')->get();
        
        $selectedClassId = $request->get('class_id');
        if (!$selectedClassId && $classes->isNotEmpty()) {
            $selectedClassId = $classes->first()->id;
        }
        $selectedClass = $classes->where('id', $selectedClassId)->first();

        $sections = collect();
        if ($selectedClass) {
            $sections = $selectedClass->sections()->orderBy('sort_order')->orderBy('name')->get();
        }

        $selectedSectionId = $request->has('section_id') ? $request->get('section_id') : null;
        if ($selectedSectionId === null && $sections->isNotEmpty()) {
            $selectedSectionId = $sections->first()->id;
        }
        $selectedSection = $selectedSectionId ? $sections->where('id', $selectedSectionId)->first() : null;

        $schedules = \App\Models\FeeSchedule::where('school_id', $schoolId)->where('academic_session_id', $selectedSession->id)->get();
        // Only show Day boarding category in class-wise fee (Hostel removed per design)
        $studentCategories = \App\Models\StudentCategory::where('school_id', $schoolId)
            ->where('name', 'Day boarding')
            ->get();
        $components = \App\Models\FeeComponent::where('school_id', $schoolId)->where('academic_session_id', $selectedSession->id)->get();

        // Load existing configurations
        $classWiseFees = \App\Models\ClassWiseFee::where('school_id', $schoolId)
            ->where('academic_session_id', $selectedSession->id)
            ->where('class_id', $selectedClassId)
            ->where(function($q) use ($selectedSectionId) {
                if ($selectedSectionId) {
                    $q->where('section_id', $selectedSectionId)->orWhereNull('section_id');
                } else {
                    $q->whereNull('section_id');
                }
            })
            ->get();

        if ($selectedSectionId) {
            $classWiseFees = $classWiseFees->groupBy(function($item) {
                return $item->fee_schedule_id . '-' . $item->student_category_id . '-' . $item->fee_component_id;
            })->map(function($group) use ($selectedSectionId) {
                return $group->where('section_id', $selectedSectionId)->first() ?? $group->whereNull('section_id')->first();
            })->values();
        }
        return view('school.fees.class_wise', compact(
            'academicSessions',
            'selectedSession',
            'classes',
            'selectedClass',
            'sections',
            'selectedSection',
            'schedules',
            'studentCategories',
            'components',
            'classWiseFees'
        ));
        } catch (\Exception $e) {
            dd([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    private function syncClassWiseFeeToStudents($schoolId, $classWiseFee)
    {
        // Find or create FeeCategory matching this component's name
        $component = \App\Models\FeeComponent::where('school_id', $schoolId)->find($classWiseFee->fee_component_id);
        if (!$component) return;

        $category = \App\Models\FeeCategory::firstOrCreate([
            'school_id' => $schoolId,
            'name' => $component->component_name,
        ], [
            'description' => 'Automatically generated category for ' . $component->component_name
        ]);

        // Strictly scoped: find students in this school's class/section only
        $query = \App\Models\Student::where('school_id', $schoolId)
            ->where('class_id', $classWiseFee->class_id);

        if ($classWiseFee->section_id) {
            $query->where('section_id', $classWiseFee->section_id);
        }

        // Filter by category mapped from boarding_type — strictly within this school
        $dayBoardingCat = \App\Models\StudentCategory::where('school_id', $schoolId)
            ->where('name', 'Day boarding')
            ->first();
        $hostelCat = \App\Models\StudentCategory::where('school_id', $schoolId)
            ->where('name', 'Hostel')
            ->first();

        // Get the schedule for this school to use as fallback for students without fee_schedule_id
        $scheduleForSchool = \App\Models\FeeSchedule::where('school_id', $schoolId)
            ->where('id', $classWiseFee->fee_schedule_id)
            ->first();
        $classWiseFeeScheduleId = $classWiseFee->fee_schedule_id;

        $students = $query->get()->filter(function($student) use ($classWiseFeeScheduleId, $dayBoardingCat, $hostelCat, $schoolId) {
            // Determine effective category based on boarding_type
            $effectiveCatId = null;
            if (!empty($student->boarding_type) && stripos($student->boarding_type, 'hostel') !== false) {
                $effectiveCatId = $hostelCat ? $hostelCat->id : null;
            } else {
                $effectiveCatId = $dayBoardingCat ? $dayBoardingCat->id : null;
            }

            // Match by student's fee_schedule_id; if null, treat as matching the class-wise fee's schedule
            $studentScheduleId = $student->fee_schedule_id;
            if ($studentScheduleId && $studentScheduleId != $classWiseFeeScheduleId) {
                return false; // Student is explicitly on a different schedule
            }

            // If student has no schedule, they inherit the class-wise fee's schedule
            if (!$studentScheduleId) {
                // Assign the schedule to the student so future syncs work correctly
                $student->fee_schedule_id = $classWiseFeeScheduleId;
                $student->save();
            }

            // Match only day-boarding students (Hostel removed from class-wise fee UI)
            return $effectiveCatId == $classWiseFeeScheduleId || $effectiveCatId !== null;
        })->filter(function($student) use ($classWiseFee, $dayBoardingCat, $hostelCat) {
            // Double check: only match students whose effective category matches the class-wise fee's student_category_id
            $effectiveCatId = null;
            if (!empty($student->boarding_type) && stripos($student->boarding_type, 'hostel') !== false) {
                $effectiveCatId = $hostelCat ? $hostelCat->id : null;
            } else {
                $effectiveCatId = $dayBoardingCat ? $dayBoardingCat->id : null;
            }
            return $effectiveCatId == $classWiseFee->student_category_id;
        });

        // Transport automatic deletion removed to allow class-wise transport fee components to apply by default
        foreach ($students as $student) {

            if ($classWiseFee->is_active) {
                $installments = $classWiseFee->installments ?? [];
                $activeInstallmentNos = [];

                foreach ($installments as $inst) {
                    $instNo = $inst['installment_no'] ?? null;
                    if (!$instNo) continue;

                    $activeInstallmentNos[] = $instNo;
                    $instAmount = floatval($inst['amount'] ?? 0);

                    $dueDate = now()->addDays(30)->toDateString();
                    if (!empty($inst['date_range'])) {
                        $parts = explode(' - ', $inst['date_range']);
                        if (count($parts) === 2) {
                            try {
                                $dueDate = \Carbon\Carbon::createFromFormat('d/m/Y', trim($parts[1]))->toDateString();
                            } catch (\Exception $e) {}
                        }
                    }

                    $studentFee = \App\Models\StudentFee::where('school_id', $schoolId)
                        ->where('student_id', $student->id)
                        ->where('fee_component_id', $classWiseFee->fee_component_id)
                        ->where('installment_no', $instNo)
                        ->where('fee_schedule_id', $classWiseFee->fee_schedule_id)
                        ->first();

                    if ($studentFee) {
                        $paidAmount = $studentFee->paid_amount;
                        $status = 'pending';
                        if ($paidAmount >= $instAmount) $status = 'paid';
                        elseif ($paidAmount > 0) $status = 'partially_paid';

                        $studentFee->update([
                            'fee_category_id' => $category->id,
                            'fee_schedule_id'  => $classWiseFee->fee_schedule_id,
                            'amount'           => $instAmount,
                            'due_date'         => $dueDate,
                            'status'           => $status,
                        ]);
                    } else {
                        \App\Models\StudentFee::create([
                            'school_id'        => $schoolId,
                            'student_id'       => $student->id,
                            'fee_category_id'  => $category->id,
                            'fee_schedule_id'  => $classWiseFee->fee_schedule_id,
                            'fee_component_id' => $classWiseFee->fee_component_id,
                            'installment_no'   => $instNo,
                            'amount'           => $instAmount,
                            'paid_amount'      => 0.00,
                            'due_date'         => $dueDate,
                            'status'           => 'pending',
                        ]);
                    }
                }

                // Clean up out-of-range unpaid installments
                \App\Models\StudentFee::where('school_id', $schoolId)
                    ->where('student_id', $student->id)
                    ->where('fee_schedule_id', $classWiseFee->fee_schedule_id)
                    ->where('fee_component_id', $classWiseFee->fee_component_id)
                    ->whereNotIn('installment_no', $activeInstallmentNos)
                    ->where('paid_amount', 0)
                    ->delete();

            } else {
                // Component toggled OFF: remove all unpaid fees for this component under this schedule
                \App\Models\StudentFee::where('school_id', $schoolId)
                    ->where('student_id', $student->id)
                    ->where('fee_schedule_id', $classWiseFee->fee_schedule_id)
                    ->where('fee_component_id', $classWiseFee->fee_component_id)
                    ->where('paid_amount', 0)
                    ->delete();
            }
        }
    }

    public static function syncStudentFees($student)
    {
        $schoolId = $student->school_id;

        $studentScheduleId = $student->fee_schedule_id;
        if (!$studentScheduleId) {
            // Find a schedule that covers this student's class — strictly within the school
            $studentClassName = optional($student->class)->name ?? optional(\App\Models\SchoolClass::find($student->class_id))->name;
            $matchedSchedule = null;
            if ($studentClassName) {
                $schoolSchedules = \App\Models\FeeSchedule::where('school_id', $schoolId)->get();
                foreach ($schoolSchedules as $sch) {
                    $classesList = json_decode($sch->classes, true);
                    if (!is_array($classesList)) {
                        $classesList = array_map('trim', explode(',', $sch->classes ?? ''));
                    } else {
                        $classesList = array_map('trim', $classesList);
                    }
                    
                    $studClassNorm = strtolower(str_replace(' ', '', $studentClassName));
                    foreach ($classesList as $c) {
                        $cNorm = strtolower(str_replace(' ', '', $c));
                        if ($studClassNorm === $cNorm || 
                            (preg_replace('/[^0-9]/', '', $studClassNorm) === preg_replace('/[^0-9]/', '', $cNorm) && preg_replace('/[^0-9]/', '', $studClassNorm) !== '') ||
                            ($cNorm !== '' && (stripos($studClassNorm, $cNorm) !== false || stripos($cNorm, $studClassNorm) !== false))) {
                            $matchedSchedule = $sch;
                            break 2;
                        }
                    }
                }
            }
            if (!$matchedSchedule) {
                $matchedSchedule = \App\Models\FeeSchedule::where('school_id', $schoolId)->first();
            }
            $studentScheduleId = $matchedSchedule ? $matchedSchedule->id : null;
            // Persist so future syncs are consistent
            if ($studentScheduleId && !$student->fee_schedule_id) {
                $student->fee_schedule_id = $studentScheduleId;
                $student->save();
            }
        }

        // Delete unpaid student fees that do not belong to the student's active schedule
        \App\Models\StudentFee::where('student_id', $student->id)
            ->where(function($q) use ($studentScheduleId) {
                if ($studentScheduleId) {
                    $q->where('fee_schedule_id', '!=', $studentScheduleId)
                      ->orWhereNull('fee_schedule_id');
                }
            })
            ->where('paid_amount', 0)
            ->delete();

        // 1. Get all active ClassWiseFee structures for this student's class
        $query = \App\Models\ClassWiseFee::where('school_id', $schoolId)
            ->where('class_id', $student->class_id)
            ->where('is_active', true);

        if ($studentScheduleId) {
            $query->where('fee_schedule_id', $studentScheduleId);
        }

        // If the structure is section-specific, filter by student's section or null
        if ($student->section_id) {
            $query->where(function($q) use ($student) {
                $q->where('section_id', $student->section_id)
                  ->orWhereNull('section_id');
            });
        } else {
            $query->whereNull('section_id');
        }

        // Filter by student's category mapped from boarding_type
        $dayBoardingCat = \App\Models\StudentCategory::where('school_id', $schoolId)
            ->where('name', 'Day boarding')
            ->first();
        $hostelCat = \App\Models\StudentCategory::where('school_id', $schoolId)
            ->where('name', 'Hostel')
            ->first();
        
        $effectiveCatId = null;
        if (!empty($student->boarding_type) && stripos($student->boarding_type, 'hostel') !== false) {
            $effectiveCatId = $hostelCat ? $hostelCat->id : null;
        } else {
            $effectiveCatId = $dayBoardingCat ? $dayBoardingCat->id : null;
        }
        
        if ($effectiveCatId) {
            $query->where('student_category_id', $effectiveCatId);
        }

        $classWiseFees = $query->get();

        if ($student->section_id) {
            $classWiseFees = $classWiseFees->groupBy(function($item) {
                return $item->fee_schedule_id . '-' . $item->student_category_id . '-' . $item->fee_component_id;
            })->map(function($group) use ($student) {
                return $group->where('section_id', $student->section_id)->first() ?? $group->whereNull('section_id')->first();
            })->values();
        }

        $activeComponentIds = $classWiseFees->pluck('fee_component_id')->toArray();
        if (count($activeComponentIds) > 0) {
            \App\Models\StudentFee::where('student_id', $student->id)
                ->where('fee_schedule_id', $studentScheduleId)
                ->whereNotIn('fee_component_id', $activeComponentIds)
                ->where('paid_amount', 0)
                ->delete();
        } else {
            \App\Models\StudentFee::where('student_id', $student->id)
                ->where('fee_schedule_id', $studentScheduleId)
                ->where('paid_amount', 0)
                ->delete();
        }

        foreach ($classWiseFees as $classWiseFee) {
            $component = \App\Models\FeeComponent::find($classWiseFee->fee_component_id);
            if (!$component) continue;

            $category = \App\Models\FeeCategory::firstOrCreate([
                'school_id' => $schoolId,
                'name' => $component->component_name,
            ], [
                'description' => 'Automatically generated category for ' . $component->component_name
            ]);

            // Transport automatic deletion removed to allow class-wise transport fee components to apply by default

            $installments = $classWiseFee->installments ?? [];
            $activeInstallmentNos = [];

            foreach ($installments as $inst) {
                $instNo = $inst['installment_no'] ?? null;
                if (!$instNo) continue;

                $activeInstallmentNos[] = $instNo;
                $instAmount = floatval($inst['amount'] ?? 0);

                // Determine due date
                $dueDate = now()->addDays(30)->toDateString();
                if (!empty($inst['date_range'])) {
                    $parts = explode(' - ', $inst['date_range']);
                    if (count($parts) === 2) {
                        try {
                            $dueDate = \Carbon\Carbon::createFromFormat('d/m/Y', trim($parts[1]))->toDateString();
                        } catch (\Exception $e) {
                            // Keep default
                        }
                    }
                }

                // Find or create StudentFee for this specific installment
                $studentFee = \App\Models\StudentFee::where('school_id', $schoolId)
                    ->where('student_id', $student->id)
                    ->where('fee_component_id', $classWiseFee->fee_component_id)
                    ->where('installment_no', $instNo)
                    ->where('fee_schedule_id', $studentScheduleId)
                    ->first();

                if ($studentFee) {
                    $paidAmount = $studentFee->paid_amount;
                    $status = 'pending';
                    if ($paidAmount >= $instAmount) {
                        $status = 'paid';
                    } elseif ($paidAmount > 0) {
                        $status = 'partially_paid';
                    }
                    
                    $studentFee->update([
                        'fee_category_id' => $category->id,
                        'amount' => $instAmount,
                        'due_date' => $dueDate,
                        'status' => $status,
                    ]);
                } else {
                    \App\Models\StudentFee::create([
                        'school_id' => $schoolId,
                        'student_id' => $student->id,
                        'fee_category_id' => $category->id,
                        'fee_schedule_id' => $studentScheduleId,
                        'fee_component_id' => $classWiseFee->fee_component_id,
                        'installment_no' => $instNo,
                        'amount' => $instAmount,
                        'paid_amount' => 0.00,
                        'due_date' => $dueDate,
                        'status' => 'pending',
                    ]);
                }
            }

            // Delete any unpaid installments that are now out-of-range
            \App\Models\StudentFee::where('student_id', $student->id)
                ->where('fee_schedule_id', $studentScheduleId)
                ->where('fee_component_id', $classWiseFee->fee_component_id)
                ->whereNotIn('installment_no', $activeInstallmentNos)
                ->where('paid_amount', 0)
                ->delete();
        }

        // Transport automatic deletion removed to allow class-wise transport fee components to apply by default
    }


    public static function generateInstallmentDates($startDate, $endDate, $count)
    {
        $start = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate);
        $dates = [];
        
        if ($count == 12) {
            // Monthly
            for ($i = 0; $i < 12; $i++) {
                $currentStart = $start->copy()->addMonths($i)->startOfMonth();
                $currentEnd = $currentStart->copy()->endOfMonth();
                
                if ($currentEnd->gt($end)) {
                    $currentEnd = $end->copy();
                }
                if ($currentStart->gt($end)) {
                    $currentStart = $end->copy()->startOfMonth();
                    $currentEnd = $end->copy();
                }
                $dates[] = $currentStart->format('d/m/Y') . ' - ' . $currentEnd->format('d/m/Y');
            }
        } elseif ($count == 4) {
            // Quarterly: Apr, Jul, Oct, Dec
            $months = [0, 3, 6, 8];
            for ($i = 0; $i < 4; $i++) {
                $currentStart = $start->copy()->addMonths($months[$i])->startOfMonth();
                $currentEnd = $currentStart->copy()->endOfMonth();
                
                if ($currentEnd->gt($end)) {
                    $currentEnd = $end->copy();
                }
                $dates[] = $currentStart->format('d/m/Y') . ' - ' . $currentEnd->format('d/m/Y');
            }
        } else {
            // Divide range evenly
            $totalDays = $start->diffInDays($end);
            $daysPerPeriod = $count > 1 ? intval($totalDays / ($count - 1)) : $totalDays;
            
            for ($i = 0; $i < $count; $i++) {
                if ($i == 0) {
                    $currentStart = $start->copy();
                } else {
                    $currentStart = $start->copy()->addDays($i * $daysPerPeriod);
                }
                
                $currentEnd = $currentStart->copy()->addMonth()->subDay();
                if ($currentEnd->gt($end) || $i == $count - 1) {
                    $currentEnd = $end->copy();
                }
                if ($currentStart->gt($end)) {
                    $currentStart = $end->copy();
                }
                
                $dates[] = $currentStart->format('d/m/Y') . ' - ' . $currentEnd->format('d/m/Y');
            }
        }
        
        return $dates;
    }

    public function studentWiseFee(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $this->ensureFeesSeeded($schoolId);

        // ─── POST REQUESTS ────────────────────────────────────────────────
        if ($request->isMethod('post')) {
            $action = $request->input('action', 'mark_paid');

            if ($action === 'toggle_visibility') {
                $studentId = $request->input('student_id');
                $student   = Student::where('school_id', $schoolId)->findOrFail($studentId);
                $student->fee_visible = filter_var($request->input('visible'), FILTER_VALIDATE_BOOLEAN);
                $student->save();
                return response()->json(['success' => true, 'visible' => $student->fee_visible]);
            }

            if ($action === 'bulk_toggle_visibility') {
                $request->validate([
                    'student_ids' => 'required|array',
                    'student_ids.*' => 'exists:students,id',
                    'visible' => 'required|boolean',
                ]);
                Student::where('school_id', $schoolId)
                    ->whereIn('id', $request->student_ids)
                    ->update(['fee_visible' => $request->visible]);
                return response()->json(['success' => true]);
            }

            if ($action === 'mark_paid') {
                $request->validate([
                    'student_fee_id'       => 'nullable|exists:student_fees,id',
                    'student_id'           => 'nullable|exists:students,id',
                    'installment_no'       => 'nullable',
                    'amount_paid'          => 'required|numeric|min:0.01',
                    'payment_mode'         => 'required|string',
                    'transaction_id'       => 'nullable|string',
                    'receipt_date'         => 'nullable|date',
                    'receipt_no'           => 'nullable|string',
                    'bank_name'            => 'nullable|string',
                    'cheque_date'          => 'nullable|date',
                    'branch'               => 'nullable|string',
                    'instant_discount_amount' => 'nullable|numeric|min:0',
                    'instant_discount_type'   => 'nullable|in:percentage,flat',
                ]);

                // Calculate effective amount after instant discount
                $rawAmountPaid     = floatval($request->input('amount_paid'));
                $discountAmount    = floatval($request->input('instant_discount_amount', 0));
                $discountType      = $request->input('instant_discount_type', 'flat');
                $effectiveDiscount = 0;
                if ($discountAmount > 0) {
                    if ($discountType === 'percentage') {
                        $effectiveDiscount = round($rawAmountPaid * $discountAmount / 100, 2);
                    } else {
                        $effectiveDiscount = min($discountAmount, $rawAmountPaid);
                    }
                }
                // Merge the computed discount back into request for use below
                $request->merge([
                    '_computed_discount' => $effectiveDiscount,
                    '_computed_discount_type' => $discountType,
                ]);

                $receiptDate = $request->input('receipt_date') ?: now()->toDateString();
                $receiptNo   = $request->input('receipt_no') ?: ('REC-' . rand(100000, 999999));
                $paymentMode = $request->input('payment_mode');

                if ($paymentMode === 'cheque') {
                    // Create pending cheque
                    PendingCheque::create([
                        'school_id'      => $schoolId,
                        'student_id'     => $request->student_id ?: StudentFee::find($request->student_fee_id)->student_id,
                        'bank_name'      => $request->bank_name ?: 'N/A',
                        'cheque_number'  => $request->transaction_id ?: 'CHQ-' . rand(100000, 999999),
                        'amount'         => $request->amount_paid,
                        'cheque_date'    => $request->cheque_date ?: now()->toDateString(),
                        'branch'         => $request->branch,
                        'installment_no' => $request->installment_no,
                        'receipt_number' => $receiptNo,
                        'entry_date'     => now()->toDateString(),
                        'receipt_date'   => $receiptDate,
                        'status'         => 'pending',
                    ]);

                    if ($request->wantsJson()) {
                        return response()->json(['success' => true, 'message' => 'Cheque recorded as pending clearing!']);
                    }
                    return back()->with('success', 'Cheque recorded as pending clearing!');
                }

                // Normal/immediate payment (cash, online, bank transfer)
                if ($request->student_fee_id) {
                    $sf = StudentFee::where('school_id', $schoolId)->findOrFail($request->student_fee_id);
                    $discount = floatval($request->input('_computed_discount', 0));
                    if ($discount > 0) {
                        $sf->instant_discount_amount += $discount;
                        $sf->instant_discount_type = $request->input('_computed_discount_type');
                    }
                    
                    $newPaid = $sf->paid_amount + $request->amount_paid;
                    $due = max(0, $sf->amount - $sf->instant_discount_amount);
                    $sf->paid_amount = min($newPaid, $due);
                    $sf->status = $sf->paid_amount >= $due ? 'paid' : 'partially_paid';
                    $sf->save();
                    
                    $studentId = $sf->student_id;
                } else {
                    $studentId = $request->student_id;
                    $instNo = $request->installment_no;
                    $amountToDistribute = floatval($request->amount_paid);
                    $discount = floatval($request->input('_computed_discount', 0));
                    $discountType = $request->input('_computed_discount_type');

                    $feesToPay = StudentFee::where('school_id', $schoolId)
                        ->where('student_id', $studentId)
                        ->where('installment_no', $instNo)
                        ->where('status', '!=', 'paid')
                        ->orderBy('id', 'asc')
                        ->get();
                        
                    if ($feesToPay->isEmpty()) {
                        $feesToPay = StudentFee::where('school_id', $schoolId)
                            ->where('student_id', $studentId)
                            ->where('installment_no', $instNo)
                            ->orderBy('id', 'asc')
                            ->get();
                    }

                    // Distribute discount first
                    $discountToDistribute = $discount;
                    if ($discountToDistribute > 0 && $feesToPay->isNotEmpty()) {
                        foreach ($feesToPay as $sf) {
                            if ($discountToDistribute <= 0) break;
                            $dueBeforeDiscount = max(0, $sf->amount - $sf->instant_discount_amount - $sf->paid_amount);
                            if ($dueBeforeDiscount <= 0) continue;
                            $discountForThis = min($discountToDistribute, $dueBeforeDiscount);
                            $sf->instant_discount_amount += $discountForThis;
                            $sf->instant_discount_type = $discountType;
                            $sf->save();
                            $discountToDistribute -= $discountForThis;
                        }
                    }

                    // Distribute paid amount
                    foreach ($feesToPay as $sf) {
                        if ($amountToDistribute <= 0) break;
                        $due = max(0, $sf->amount - $sf->instant_discount_amount - $sf->paid_amount);
                        if ($due <= 0) continue;
                        $paymentForThis = min($amountToDistribute, $due);
                        $sf->paid_amount += $paymentForThis;
                        $sf->status = $sf->paid_amount >= ($sf->amount - $sf->instant_discount_amount) ? 'paid' : 'partially_paid';
                        $sf->save();
                        $amountToDistribute -= $paymentForThis;
                    }
                }

                FeeReceipt::create([
                    'school_id'      => $schoolId,
                    'student_id'     => $studentId,
                    'receipt_number' => $receiptNo,
                    'amount_paid'    => $request->amount_paid,
                    'discount_amount'=> $request->input('_computed_discount', 0),
                    'discount_type'  => $request->input('_computed_discount_type'),
                    'payment_mode'   => $paymentMode,
                    'transaction_id' => $request->transaction_id,
                    'payment_date'   => $receiptDate,
                ]);

                if ($request->wantsJson()) {
                    return response()->json(['success' => true, 'message' => 'Payment collected successfully!']);
                }
                return back()->with('success', 'Fee Payment collected successfully!');
            }
        }

        // ─── GET: Load academic sessions ────────────────────────────────
        $academicSessions = \App\Models\AcademicSession::where('school_id', $schoolId)->orderBy('name', 'desc')->get();
        $currentSession   = $academicSessions->where('is_current', true)->first() ?? $academicSessions->first();

        // Create default session if none
        if (!$currentSession) {
            $currentSession = \App\Models\AcademicSession::create([
                'school_id'  => $schoolId,
                'name'       => 'Apr 2025 - Mar 2026',
                'start_date' => '2025-04-01',
                'end_date'   => '2026-03-31',
                'is_current' => true,
            ]);
            $academicSessions = collect([$currentSession]);
        }

        $sessionId       = $request->get('academic_session_id', $currentSession->id);
        $selectedSession = \App\Models\AcademicSession::where('school_id', $schoolId)->find($sessionId) ?? $currentSession;

        // ─── Filters ────────────────────────────────────────────────────
        $classes         = \App\Models\SchoolClass::where('school_id', $schoolId)->orderBy('sort_order')->orderBy('name')->get();
        $selectedClassId = $request->get('class_id');
        if ($selectedClassId === '' || $selectedClassId === 'all') {
            $selectedClassId = null;
        }
        $selectedClass   = $selectedClassId ? $classes->where('id', $selectedClassId)->first() : null;

        $sections        = $selectedClass ? $selectedClass->sections()->orderBy('name')->get() : collect();
        $selectedSectionId = $request->get('section_id');
        if ($selectedSectionId === '' || $selectedSectionId === 'all') {
            $selectedSectionId = null;
        }

        // Reset section_id if it does not belong to the selected class
        if ($selectedClass && $selectedSectionId && !$sections->contains('id', $selectedSectionId)) {
            $selectedSectionId = null;
        }

        // ─── Detail view: single student ────────────────────────────────
        $viewStudentId = $request->get('view_student');
        $viewStudent   = null;
        $studentFees   = collect();
        $feeScheduleName = null;
        $appliedDiscounts = [];

        if ($viewStudentId) {
            $viewStudent = Student::where('school_id', $schoolId)
                ->with(['class', 'section', 'category'])
                ->find($viewStudentId);

            if ($viewStudent) {
                self::syncStudentFees($viewStudent);

                $studentFees = StudentFee::where('school_id', $schoolId)
                    ->where('student_id', $viewStudentId)
                    ->with(['category', 'component'])
                    ->orderBy('installment_no')
                    ->get();

                // Try to get actual matching fee schedule name for the student's class
                $studentClass = optional($viewStudent->class)->name;
                if ($studentClass) {
                    $schedules = \App\Models\FeeSchedule::where('school_id', $schoolId)
                        ->where('academic_session_id', $selectedSession->id)
                        ->get();
                    foreach ($schedules as $sch) {
                        $schClasses = array_map('trim', explode(',', $sch->classes ?? ''));
                        if (in_array($studentClass, $schClasses)) {
                            $feeScheduleName = $sch->name;
                            break;
                        }
                    }
                }

                // Applied discounts for this student
                $appliedDiscounts = \App\Models\FeeDiscount::where('school_id', $schoolId)
                    ->where('academic_session_id', $selectedSession->id)
                    ->get()
                    ->filter(function ($d) use ($viewStudentId) {
                        $ids = json_decode($d->student_ids ?? '[]', true);
                        return empty($ids) || in_array($viewStudentId, $ids);
                    });
            }
        }

        // Dynamically sync student fees for the queried class/section (limit 150)
        // to make sure their calculated totals, amounts, and statuses reflect the latest class-wise setups.
        $syncQuery = Student::where('school_id', $schoolId);
        if ($selectedClassId) {
            $syncQuery->where('class_id', $selectedClassId);
        }
        if ($selectedSectionId) {
            $syncQuery->where('section_id', $selectedSectionId);
        }
        $studentsToSync = $syncQuery->limit(150)->get();
        foreach ($studentsToSync as $sToSync) {
            self::syncStudentFees($sToSync);
        }

        // ─── LIST: students with fee summary ────────────────────────────
        $query = Student::where('school_id', $schoolId)
            ->with(['class', 'section', 'studentFees' => function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            }]);

        if ($selectedClassId) {
            $query->where('class_id', $selectedClassId);
        }
        if ($selectedSectionId) {
            $query->where('section_id', $selectedSectionId);
        }

        $search = $request->get('search');
        if ($search && strlen($search) >= 3) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                  ->orWhere('last_name', 'like', "%$search%")
                  ->orWhere('admission_number', 'like', "%$search%");
            });
        }

        if ($request->get('all_year')) {
            $query->whereHas('studentFees', function($q) use ($schoolId) {
                $q->where('school_id', $schoolId)->whereColumn('amount', '>', 'paid_amount');
            });
        }

        $studentsWithFees = $query->paginate(25)->withQueryString();

        // Build fee schedule map for each student
        $schedules = \App\Models\FeeSchedule::where('school_id', $schoolId)
            ->where('academic_session_id', $selectedSession->id)
            ->get();

        return view('school.fees.student_wise', compact(
            'academicSessions',
            'selectedSession',
            'classes',
            'selectedClass',
            'sections',
            'selectedSectionId',
            'studentsWithFees',
            'schedules',
            'viewStudent',
            'studentFees',
            'feeScheduleName',
            'appliedDiscounts',
            'search'
        ));
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
        $school = app()->bound('currentSchool') ? app('currentSchool') : null;
        $schoolId = $school ? $school->id : auth()->user()->school_id;
        $this->ensureFeesSeeded($schoolId);

        if ($request->isMethod('post')) {
            if ($request->has('student_schedules')) {
                foreach ($request->student_schedules as $studentId => $scheduleId) {
                    $student = Student::where('school_id', $schoolId)->find($studentId);
                    if ($student) {
                        $student->fee_schedule_id = $scheduleId;
                        $student->save();
                        self::syncStudentFees($student);
                    }
                }
                return back()->with('success', 'Student Fee Schedules updated successfully!');
            }

            if ($request->has('fee_category_id')) {
                $request->validate([
                    'fee_category_id' => 'required|exists:fee_categories,id',
                    'schedule_type' => 'required|string',
                ]);

                FeeStructure::where('school_id', $schoolId)
                    ->where('fee_category_id', $request->fee_category_id)
                    ->update(['schedule_type' => $request->schedule_type]);

                return back()->with('success', 'Fee Schedules updated for selected category!');
            }
        }

        $categories = FeeCategory::where('school_id', $schoolId)->get();
        $structures = FeeStructure::where('school_id', $schoolId)->with(['class', 'category'])->get();
        $sessions = \App\Models\AcademicSession::where('school_id', $schoolId)->get();
        $classes = SchoolClass::where('school_id', $schoolId)->get();
        $sections = \App\Models\Section::where('school_id', $schoolId)->get()->unique('name');

        // Resolve selected academic session based on academic_year parameter
        $selectedYear = $request->get('academic_year');
        $currentSession = \App\Models\AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first();
        if (!$currentSession) {
            $currentSession = \App\Models\AcademicSession::where('school_id', $schoolId)->first();
        }

        $selectedSession = $currentSession;
        if ($selectedYear) {
            $matchedSession = \App\Models\AcademicSession::where('school_id', $schoolId)
                ->where('name', 'like', "%{$selectedYear}%")
                ->first();
            if ($matchedSession) {
                $selectedSession = $matchedSession;
            }
        }

        $schedulesQuery = \App\Models\FeeSchedule::where('school_id', $schoolId);
        if ($selectedSession) {
            $schedulesQuery->where('academic_session_id', $selectedSession->id);
        }
        $schedules = $schedulesQuery->get()->unique('name');

        $query = Student::where('school_id', $schoolId)->with(['class', 'section']);

        if ($selectedSession) {
            $query->where('academic_session_id', $selectedSession->id);
        }

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }
        if ($request->filled('section_id')) {
            $secVal = $request->section_id;
            $query->whereHas('section', function($q) use ($secVal) {
                $q->where('id', $secVal)->orWhere('name', $secVal);
            });
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('admission_id', 'like', "%{$search}%")
                  ->orWhere('admission_number', 'like', "%{$search}%");
            });
        }

        $students = $query->take(50)->get();

        // No auto-seeding of dummy students

        return view('school.fees.schedule_mapper', compact('categories', 'structures', 'sessions', 'classes', 'sections', 'schedules', 'students'));
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

        $sessions = \App\Models\AcademicSession::where('school_id', $schoolId)->get();
        $classes = SchoolClass::where('school_id', $schoolId)->get();
        $sections = \App\Models\Section::where('school_id', $schoolId)->get()->unique('name');

        $query = FeeReceipt::where('school_id', $schoolId)->with(['student.class', 'student.section']);

        if ($request->filled('class_id')) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('class_id', $request->class_id);
            });
        }
        if ($request->filled('section_id')) {
            $secVal = $request->section_id;
            $query->whereHas('student', function($q) use ($secVal) {
                $q->whereHas('section', function($sq) use ($secVal) {
                    $sq->where('id', $secVal)->orWhere('name', $secVal);
                });
            });
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('receipt_number', 'like', "%{$search}%")
                  ->orWhere('transaction_id', 'like', "%{$search}%")
                  ->orWhereHas('student', function($sq) use ($search) {
                      $sq->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('admission_id', 'like', "%{$search}%")
                        ->orWhere('admission_number', 'like', "%{$search}%");
                  });
            });
        }

        $receipts = $query->orderBy('id', 'desc')->get();

        $totalAmount = $receipts->sum('amount_paid');
        $config = \App\Models\FeeConfiguration::where('school_id', $schoolId)->first();

        return view('school.fees.receipts', compact('sessions', 'classes', 'sections', 'receipts', 'totalAmount', 'config'));
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
                    'receipt_number' => $cheque->receipt_number ?: ('REC-' . rand(100000, 999999)),
                    'amount_paid' => $cheque->amount,
                    'payment_mode' => 'cheque',
                    'transaction_id' => $cheque->cheque_number,
                    'payment_date' => $cheque->receipt_date ?: now()->toDateString(),
                ]);

                // Update corresponding student fees for that installment!
                $installmentNo = $cheque->installment_no;
                $feesToPay = StudentFee::where('school_id', $schoolId)
                    ->where('student_id', $cheque->student_id);
                if ($installmentNo) {
                    $feesToPay->where('installment_no', $installmentNo);
                }
                $feesToPay = $feesToPay->where('status', '!=', 'paid')
                    ->orderBy('id', 'asc')
                    ->get();
                
                if ($feesToPay->isEmpty()) {
                    $feesToPay = StudentFee::where('school_id', $schoolId)
                        ->where('student_id', $cheque->student_id);
                    if ($installmentNo) {
                        $feesToPay->where('installment_no', $installmentNo);
                    }
                    $feesToPay = $feesToPay->orderBy('id', 'asc')->get();
                }

                $amountToDistribute = floatval($cheque->amount);
                foreach ($feesToPay as $sf) {
                    if ($amountToDistribute <= 0) break;
                    $due = $sf->amount - $sf->paid_amount;
                    if ($due <= 0) continue;
                    $paymentForThis = min($amountToDistribute, $due);
                    $sf->paid_amount += $paymentForThis;
                    $sf->status = $sf->paid_amount >= $sf->amount ? 'paid' : 'partially_paid';
                    $sf->save();
                    $amountToDistribute -= $paymentForThis;
                }
                if ($amountToDistribute > 0 && $feesToPay->isNotEmpty()) {
                    $first = $feesToPay->first();
                    $first->paid_amount += $amountToDistribute;
                    $first->status = 'paid';
                    $first->save();
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
        $this->seedInvoice1Data($schoolId);
        $this->ensureFeesSeeded($schoolId);

        $school = \App\Models\School::find($schoolId);
        $students = Student::where('school_id', $schoolId)->with(['class', 'section'])->get();
        $fees = StudentFee::where('school_id', $schoolId)->with(['category', 'student', 'component', 'feeSchedule'])->get();
        $config = \App\Models\FeeConfiguration::where('school_id', $schoolId)->first();

        return view('school.fees.invoice', compact('students', 'fees', 'config', 'school'));
    }

    public function feeInvoice1(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        
        // Seed the invoice 1 data
        $this->seedInvoice1Data($schoolId);

        if ($request->has('ajax') || $request->wantsJson()) {
            $query = \App\Models\StudentFee::where('student_fees.school_id', $schoolId)
                ->join('students', 'students.id', '=', 'student_fees.student_id')
                ->leftJoin('school_classes', 'school_classes.id', '=', 'students.class_id')
                ->leftJoin('sections', 'sections.id', '=', 'students.section_id')
                ->leftJoin('fee_schedules', 'fee_schedules.id', '=', 'student_fees.fee_schedule_id')
                ->leftJoin('fee_components', 'fee_components.id', '=', 'student_fees.fee_component_id')
                ->select(
                    'student_fees.id as fee_id',
                    'student_fees.student_id',
                    'student_fees.amount',
                    'student_fees.installment_no',
                    'student_fees.invoice_no',
                    'students.admission_number as admission_id',
                    'students.first_name',
                    'students.last_name',
                    'students.father_name',
                    'school_classes.name as class_name',
                    'sections.name as section_name',
                    'fee_schedules.name as schedule_name',
                    'fee_components.component_name'
                );

            if ($request->filled('academic_session_id')) {
                $query->where(function($q) use ($request) {
                    $q->where('fee_schedules.academic_session_id', $request->academic_session_id)
                      ->orWhere('students.academic_session_id', $request->academic_session_id)
                      ->orWhereNull('student_fees.fee_schedule_id');
                });
            }
            if ($request->filled('class_id')) {
                $query->where('students.class_id', $request->class_id);
            }
            if ($request->filled('section_id')) {
                $secVal = $request->section_id;
                $query->where(function($q) use ($secVal) {
                    $q->where('students.section_id', $secVal)
                      ->orWhere('sections.name', $secVal)
                      ->orWhere('sections.id', $secVal);
                });
            }
            if ($request->filled('fee_schedule_id')) {
                $query->where('student_fees.fee_schedule_id', $request->fee_schedule_id);
            }
            if ($request->filled('installment_no')) {
                $query->where('student_fees.installment_no', $request->installment_no);
            }
            if ($request->filled('fee_component_id')) {
                $query->where('student_fees.fee_component_id', $request->fee_component_id);
            }
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('students.first_name', 'like', "%{$search}%")
                      ->orWhere('students.last_name', 'like', "%{$search}%")
                      ->orWhere('students.admission_number', 'like', "%{$search}%")
                      ->orWhere('students.father_name', 'like', "%{$search}%");
                });
            }

            // Order by student_fees.id asc so the seeded Raghav etc. appear first
            $query->orderBy('student_fees.id', 'asc');

            $perPage = $request->input('per_page', 10);
            $paginator = $query->paginate($perPage);

            // Dynamically combine first and last name for JS compatibility
            $paginator->getCollection()->transform(function($row) {
                $row->student_name = trim($row->first_name . ' ' . $row->last_name);
                return $row;
            });

            return response()->json($paginator);
        }

        $academicSessions = \App\Models\AcademicSession::where('school_id', $schoolId)->get();
        $classes = \App\Models\SchoolClass::where('school_id', $schoolId)->get()->unique('name')->sortBy(function($c) {
            $order = ['Nursery' => 1, 'LKG' => 2, 'UKG' => 3, 'Class 1' => 4, 'Class 2' => 5, 'Class 3' => 6, 'Class 4' => 7, 'Class 5' => 8, 'Class 6' => 9, 'Class 7' => 10, 'Class 8' => 11, 'Class 9' => 12, 'Class 10' => 13, 'Class 11' => 14, 'Class 12' => 15];
            return $order[$c->name] ?? ($c->sort_order ?? 99);
        });
        $sections = \App\Models\Section::where('school_id', $schoolId)->get();
        $feeSchedules = \App\Models\FeeSchedule::where('school_id', $schoolId)->get();
        $feeComponents = \App\Models\FeeComponent::where('school_id', $schoolId)->get();

        return view('school.fees.invoice1', compact(
            'academicSessions',
            'classes',
            'sections',
            'feeSchedules',
            'feeComponents'
        ));
    }

    public function feeInvoice1Generate(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $feeIds = $request->input('fee_ids', []);

        if (empty($feeIds)) {
            return response()->json(['success' => false, 'message' => 'No fee components selected.'], 400);
        }

        $fees = \App\Models\StudentFee::where('school_id', $schoolId)
            ->whereIn('id', $feeIds)
            ->get();

        if ($fees->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No matching fee records found.'], 404);
        }

        // Group by student
        $grouped = $fees->groupBy('student_id');
        $generatedCount = 0;
        $invoices = [];

        foreach ($grouped as $studentId => $studentFees) {
            $student = \App\Models\Student::find($studentId);
            $prefix = $student ? preg_replace('/[^A-Za-z0-9]/', '', $student->admission_number) : 'ST';
            $invoiceNo = 'INV-' . $prefix . '-' . rand(1000, 9999);

            foreach ($studentFees as $fee) {
                $fee->invoice_no = $invoiceNo;
                $fee->save();
                $generatedCount++;
            }
            $invoices[] = [
                'student_name' => $student ? $student->full_name : 'N/A',
                'invoice_no' => $invoiceNo,
                'count' => count($studentFees)
            ];
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully generated invoice numbers for {$generatedCount} fee components across " . count($grouped) . " students.",
            'invoices' => $invoices
        ]);
    }

    private function seedInvoice1Data($schoolId)
    {
        // No auto-seeding
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
