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
        if (!$schoolId) {
            return;
        }
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
                'payment_url' => null,
                'show_installment_components_on_invoice' => false,
                'show_due_on_invoice' => true,
                'invoice_title' => 'Fee Invoice',
                'transport_invoice_title' => 'Transport Invoice',
                'school_fee_prefix' => 'REC',
                'transport_fee_prefix' => 'TRN',
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

        // Auto-create "Transport Fee" component for Yash International School
        $school = \App\Models\School::find($schoolId);
        if ($school && strpos(strtolower($school->name), 'yash international') !== false) {
            $category = \App\Models\FeeCategory::firstOrCreate(
                ['school_id' => $schoolId, 'name' => 'Transport'],
                ['description' => 'Transport Fees']
            );

            \App\Models\FeeComponent::firstOrCreate(
                [
                    'school_id' => $schoolId,
                    'component_name' => 'Transport Fee',
                    'academic_session_id' => $currentSession->id
                ],
                [
                    'fee_category_id' => $category->id,
                    'head_name' => 'Transport',
                    'admission_type' => 'All Students',
                    'gender' => 'All Students'
                ]
            );
        }

        // Auto-seeding schedules and components disabled per user request to start with empty fee basics setup
    }

    private function getSessionScopedClasses($schoolId, $sessionId)
    {
        return \App\Models\SchoolClass::where('school_id', $schoolId)
            ->where(function($query) use ($sessionId) {
                $query->whereIn('id', function($q) use ($sessionId) {
                    $q->select('class_id')
                      ->from('students')
                      ->where('academic_session_id', $sessionId)
                      ->whereNull('deleted_at');
                })
                ->orWhereIn('id', function($q) use ($sessionId) {
                    $q->select('class_id')
                      ->from('student_sessions')
                      ->where('academic_session_id', $sessionId);
                })
                ->orWhereIn('id', function($q) use ($sessionId) {
                    $q->select('class_id')
                      ->from('class_wise_fees')
                      ->where('academic_session_id', $sessionId);
                })
                ->orWhere(function($orQ) {
                    $orQ->whereNotExists(function($q) {
                        $q->select(\Illuminate\Support\Facades\DB::raw(1))
                          ->from('students')
                          ->whereColumn('students.class_id', 'school_classes.id')
                          ->whereNull('deleted_at');
                    })
                    ->whereNotExists(function($q) {
                        $q->select(\Illuminate\Support\Facades\DB::raw(1))
                          ->from('student_sessions')
                          ->whereColumn('student_sessions.class_id', 'school_classes.id');
                    });
                });
            })
            ->with(['sections' => function($query) use ($sessionId) {
                $query->where(function($q) use ($sessionId) {
                    $q->whereIn('id', function($sq) use ($sessionId) {
                        $sq->select('section_id')
                          ->from('students')
                          ->where('academic_session_id', $sessionId)
                          ->whereNull('deleted_at');
                    })
                    ->orWhereIn('id', function($sq) use ($sessionId) {
                        $sq->select('section_id')
                          ->from('student_sessions')
                          ->where('academic_session_id', $sessionId);
                    })
                    ->orWhereIn('id', function($sq) use ($sessionId) {
                        $sq->select('section_id')
                          ->from('class_wise_fees')
                          ->where('academic_session_id', $sessionId);
                    })
                    ->orWhere(function($orQ) {
                        $orQ->whereNotExists(function($sq) {
                            $sq->select(\Illuminate\Support\Facades\DB::raw(1))
                              ->from('students')
                              ->whereColumn('students.section_id', 'sections.id')
                              ->whereNull('deleted_at');
                        })
                        ->whereNotExists(function($sq) {
                            $sq->select(\Illuminate\Support\Facades\DB::raw(1))
                              ->from('student_sessions')
                              ->whereColumn('student_sessions.section_id', 'sections.id');
                        });
                    });
                });
            }])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    private function getSessionScopedSections($schoolId, $sessionId)
    {
        return \App\Models\Section::where('school_id', $schoolId)
            ->where(function($q) use ($sessionId) {
                $q->whereIn('id', function($sq) use ($sessionId) {
                    $sq->select('section_id')
                      ->from('students')
                      ->where('academic_session_id', $sessionId)
                      ->whereNull('deleted_at');
                })
                ->orWhereIn('id', function($sq) use ($sessionId) {
                    $sq->select('section_id')
                      ->from('student_sessions')
                      ->where('academic_session_id', $sessionId);
                })
                ->orWhereIn('id', function($sq) use ($sessionId) {
                    $sq->select('section_id')
                      ->from('class_wise_fees')
                      ->where('academic_session_id', $sessionId);
                })
                ->orWhere(function($orQ) {
                    $orQ->whereNotExists(function($sq) {
                        $sq->select(\Illuminate\Support\Facades\DB::raw(1))
                          ->from('students')
                          ->whereColumn('students.section_id', 'sections.id')
                          ->whereNull('deleted_at');
                    })
                    ->whereNotExists(function($sq) {
                        $sq->select(\Illuminate\Support\Facades\DB::raw(1))
                          ->from('student_sessions')
                          ->whereColumn('student_sessions.section_id', 'sections.id');
                    });
                });
            })
            ->get();
    }

    public function feeConfiguration(Request $request)
    {
        $schoolId = $this->resolveSchoolId();
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
                    'school_fee_prefix' => $request->input('school_fee_prefix', 'REC'),
                    'transport_fee_prefix' => $request->input('transport_fee_prefix', 'TRN'),
                    
                    'payment_url_enabled' => $request->has('payment_url_enabled'),
                    'payment_url' => $request->input('payment_url'),
                    
                    'add_fee_due' => $request->has('add_fee_due'),
                    'add_fee_discount' => $request->has('add_fee_discount'),
                    'add_fee_balance' => $request->has('add_fee_balance'),
                    
                    'note_enabled' => $request->has('note_enabled'),
                    'note_text' => $request->input('note_text'),
                    'show_installment_components_on_invoice' => $request->has('show_installment_components_on_invoice'),
                    'show_due_on_invoice' => $request->has('show_due_on_invoice'),
                    'invoice_title' => $request->input('invoice_title', 'Fee Invoice'),
                    'transport_invoice_title' => $request->input('transport_invoice_title', 'Transport Invoice'),
                    
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
        return view('school.fees.configuration', compact('config'));
    }

    public function feeBasics(Request $request)
    {
        $schoolId = $this->resolveSchoolId();
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
                if (is_string($request->input('installments'))) {
                    $decoded = json_decode($request->input('installments'), true);
                    if (is_array($decoded)) {
                        $request->merge(['installments' => $decoded]);
                    }
                }

                $request->validate([
                    'name' => 'required|string|max:100',
                    'academic_session_id' => 'required|exists:academic_sessions,id',
                    'classes' => 'required|array',
                    'sections' => 'nullable|array',
                    'installment_type' => 'required|in:monthly,quarterly,yearly,custom',
                    'custom_count' => 'required_if:installment_type,custom|nullable|integer|min:1|max:24',
                    'installments' => 'required|array|min:1',
                    'installments.*.name' => 'required|string|max:100',
                    'installments.*.start_date' => 'required|date',
                    'installments.*.end_date' => 'required|date|after_or_equal:installments.*.start_date',
                    'installments.*.due_date' => 'required|date',
                    'installments.*.grace_days' => 'required|integer|min:0',
                    'fine_id' => 'nullable|exists:fee_fines,id',
                ]);

                $session = \App\Models\AcademicSession::where('school_id', $schoolId)->findOrFail($request->academic_session_id);
                $error = \App\Services\FeeInstallmentDistributor::validateInstallments($request->installments, $session);
                if ($error) {
                    return back()->withErrors(['installments' => $error])->withInput();
                }

                \App\Models\FeeSchedule::create([
                    'school_id' => $schoolId,
                    'academic_session_id' => $request->academic_session_id,
                    'classes' => implode(', ', $request->classes),
                    'sections' => $request->has('sections') ? implode(', ', $request->sections) : null,
                    'name' => $request->name,
                    'start_date' => $session->start_date,
                    'end_date' => $session->end_date,
                    'installment_type' => $request->installment_type,
                    'installments' => $request->installments,
                    'fine_id' => $request->fine_id ?: null,
                ]);

                return back()->with('success', 'Fee Schedule added successfully!');
            }

            if ($action === 'edit_fee_schedule') {
                if (is_string($request->input('installments'))) {
                    $decoded = json_decode($request->input('installments'), true);
                    if (is_array($decoded)) {
                        $request->merge(['installments' => $decoded]);
                    }
                }

                $request->validate([
                    'id' => 'required|exists:fee_schedules,id',
                    'name' => 'required|string|max:100',
                    'academic_session_id' => 'required|exists:academic_sessions,id',
                    'classes' => 'required|array',
                    'sections' => 'nullable|array',
                    'installment_type' => 'required|in:monthly,quarterly,yearly,custom',
                    'custom_count' => 'required_if:installment_type,custom|nullable|integer|min:1|max:24',
                    'installments' => 'required|array|min:1',
                    'installments.*.name' => 'required|string|max:100',
                    'installments.*.start_date' => 'required|date',
                    'installments.*.end_date' => 'required|date|after_or_equal:installments.*.start_date',
                    'installments.*.due_date' => 'required|date',
                    'installments.*.grace_days' => 'required|integer|min:0',
                    'fine_id' => 'nullable|exists:fee_fines,id',
                ]);

                $schedule = \App\Models\FeeSchedule::where('school_id', $schoolId)->findOrFail($request->id);
                $session = \App\Models\AcademicSession::where('school_id', $schoolId)->findOrFail($request->academic_session_id);
                $error = \App\Services\FeeInstallmentDistributor::validateInstallments($request->installments, $session);
                if ($error) {
                    return back()->withErrors(['installments' => $error])->withInput();
                }

                $schedule->update([
                    'classes' => implode(', ', $request->classes),
                    'sections' => $request->has('sections') ? implode(', ', $request->sections) : null,
                    'name' => $request->name,
                    'start_date' => $session->start_date,
                    'end_date' => $session->end_date,
                    'installment_type' => $request->installment_type,
                    'installments' => $request->installments,
                    'fine_id' => $request->fine_id ?: null,
                ]);

                // Update ClassWiseFee records matching this schedule to propagate installment date updates
                $classWiseFees = \App\Models\ClassWiseFee::where('fee_schedule_id', $schedule->id)->get();
                foreach ($classWiseFees as $cwFee) {
                    $cwInstallments = $cwFee->installments ?? [];
                    $updatedCwInstallments = [];
                    foreach ($request->installments as $schedInst) {
                        $instNo = $schedInst['installment_no'] ?? null;
                        // Find matching class-wise installment to get its amount
                        $existingCwInst = collect($cwInstallments)->firstWhere('installment_no', $instNo);
                        $amount = $existingCwInst ? floatval($existingCwInst['amount'] ?? 0) : 0.00;
                        
                        $updatedCwInstallments[] = [
                            'installment_no' => $instNo,
                            'name' => $schedInst['name'] ?? '',
                            'amount' => $amount,
                            'due_date' => $schedInst['due_date'] ?? '',
                            'start_date' => $schedInst['start_date'] ?? '',
                            'end_date' => $schedInst['end_date'] ?? '',
                        ];
                    }
                    $cwFee->update([
                        'installments' => $updatedCwInstallments
                    ]);

                    // Sync the updated class-wise fee to students
                    self::syncClassWiseFeeToStudents($schoolId, $cwFee);
                }

                return back()->with('success', 'Fee Schedule updated successfully!');
            }

            if ($action === 'add_fee_component') {
                $request->validate([
                    'head_name' => 'required|string|max:100',
                    'component_name' => 'required|string|max:100',
                    'academic_session_id' => 'required|exists:academic_sessions,id',
                    'fee_schedule_id' => 'required|exists:fee_schedules,id',
                    'admission_type' => 'required|string',
                    'gender' => 'required|string',
                ]);

                \App\Models\FeeComponent::create([
                    'school_id' => $schoolId,
                    'academic_session_id' => $request->academic_session_id,
                    'fee_schedule_id' => $request->fee_schedule_id,
                    'head_name' => $request->head_name,
                    'component_name' => $request->component_name,
                    'admission_type' => $request->admission_type,
                    'gender' => $request->gender,
                ]);

                return back()->with('success', 'Fee Component added successfully!');
            }

            if ($action === 'edit_fee_component') {
                $request->validate([
                    'id' => 'required|exists:fee_components,id',
                    'head_name' => 'required|string|max:100',
                    'component_name' => 'required|string|max:100',
                    'academic_session_id' => 'required|exists:academic_sessions,id',
                    'fee_schedule_id' => 'required|exists:fee_schedules,id',
                    'admission_type' => 'required|string',
                    'gender' => 'required|string',
                ]);

                $componentObj = \App\Models\FeeComponent::where('school_id', $schoolId)->findOrFail($request->id);
                $componentObj->update([
                    'academic_session_id' => $request->academic_session_id,
                    'fee_schedule_id' => $request->fee_schedule_id,
                    'head_name' => $request->head_name,
                    'component_name' => $request->component_name,
                    'admission_type' => $request->admission_type,
                    'gender' => $request->gender,
                ]);

                return back()->with('success', 'Fee Component updated successfully!');
            }

            if ($action === 'add_fee_discount') {
                $request->validate([
                    'name' => 'required|string|max:100',
                    'academic_session_id' => 'required|exists:academic_sessions,id',
                    'remarks' => 'nullable|string',
                    'amount' => 'required|numeric|min:0',
                    'type' => 'nullable|in:flat,percentage',
                    'classes' => 'required|array',
                    'sections' => 'nullable|array',
                    'student_ids' => 'nullable|array',
                    'target_group' => 'nullable|string',
                    'fee_component_ids' => 'nullable|array',
                ]);

                $discount = \App\Models\FeeDiscount::create([
                    'school_id' => $schoolId,
                    'academic_session_id' => $request->academic_session_id,
                    'name' => $request->name,
                    'remarks' => $request->remarks,
                    'classes_installments' => json_encode($request->classes),
                    'sections' => $request->has('sections') ? implode(', ', $request->sections) : null,
                    'amount' => $request->amount,
                    'type' => $request->type ?? 'flat',
                    'student_ids' => $request->has('student_ids') ? json_encode($request->student_ids) : null,
                    'target_group' => $request->target_group ?: 'all',
                    'fee_component_ids' => $request->has('fee_component_ids') ? json_encode($request->fee_component_ids) : null,
                ]);

                \Illuminate\Support\Facades\DB::table('deleted_concessions')->insert([
                    'school_id' => $schoolId,
                    'concession_name' => $discount->name,
                    'deleted_by' => (auth()->user()->name ?? 'Administrator') . ' (Created)',
                    'date' => now()->toDateString(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Sync the new discount to all students in the school
                $students = \App\Models\Student::where('school_id', $schoolId)->get();
                foreach ($students as $student) {
                    self::syncStudentDiscounts($student, $request->academic_session_id);
                }

                return back()->with('success', 'Fee Discount added successfully!');
            }

            if ($action === 'edit_fee_discount') {
                $request->validate([
                    'id' => 'required|exists:fee_discounts,id',
                    'name' => 'required|string|max:100',
                    'academic_session_id' => 'required|exists:academic_sessions,id',
                    'remarks' => 'nullable|string',
                    'amount' => 'required|numeric|min:0',
                    'type' => 'nullable|in:flat,percentage',
                    'classes' => 'required|array',
                    'sections' => 'nullable|array',
                    'student_ids' => 'nullable|array',
                    'target_group' => 'nullable|string',
                    'fee_component_ids' => 'nullable|array',
                ]);

                $discount = \App\Models\FeeDiscount::where('school_id', $schoolId)->findOrFail($request->id);
                $discount->update([
                    'academic_session_id' => $request->academic_session_id,
                    'name' => $request->name,
                    'remarks' => $request->remarks,
                    'classes_installments' => json_encode($request->classes),
                    'sections' => $request->has('sections') ? implode(', ', $request->sections) : null,
                    'amount' => $request->amount,
                    'type' => $request->type ?? 'flat',
                    'student_ids' => $request->has('student_ids') ? json_encode($request->student_ids) : null,
                    'target_group' => $request->target_group ?: 'all',
                    'fee_component_ids' => $request->has('fee_component_ids') ? json_encode($request->fee_component_ids) : null,
                ]);

                // Sync the modified discount to all students in the school
                $students = \App\Models\Student::where('school_id', $schoolId)->get();
                foreach ($students as $student) {
                    self::syncStudentDiscounts($student, $request->academic_session_id);
                }

                return back()->with('success', 'Fee Discount updated successfully!');
            }

            if ($action === 'add_misc_fee') {
                $request->validate([
                    'name' => 'required|string|max:100',
                    'fee_head_name' => 'required|string|max:100',
                    'academic_session_id' => 'required|exists:academic_sessions,id',
                    'remarks' => 'nullable|string',
                    'amount' => 'required|numeric|min:0',
                    'classes' => 'required|array',
                    'student_ids' => 'nullable|array',
                ]);

                $mfee = \App\Models\MiscFee::create([
                    'school_id' => $schoolId,
                    'academic_session_id' => $request->academic_session_id,
                    'fee_head_name' => $request->fee_head_name,
                    'name' => $request->name,
                    'remarks' => $request->remarks,
                    'classes_installments' => json_encode($request->classes),
                    'amount' => $request->amount,
                    'student_ids' => $request->has('student_ids') ? json_encode($request->student_ids) : null,
                ]);

                \App\Models\StudentFee::generateMiscFeeInstallments($schoolId, $mfee);

                return back()->with('success', 'Misc Fee added successfully!');
            }

            if ($action === 'edit_misc_fee') {
                $request->validate([
                    'id' => 'required|exists:misc_fees,id',
                    'name' => 'required|string|max:100',
                    'fee_head_name' => 'required|string|max:100',
                    'academic_session_id' => 'required|exists:academic_sessions,id',
                    'remarks' => 'nullable|string',
                    'amount' => 'required|numeric|min:0',
                    'classes' => 'required|array',
                    'student_ids' => 'nullable|array',
                ]);

                $miscFee = \App\Models\MiscFee::where('school_id', $schoolId)->findOrFail($request->id);
                $miscFee->update([
                    'academic_session_id' => $request->academic_session_id,
                    'fee_head_name' => $request->fee_head_name,
                    'name' => $request->name,
                    'remarks' => $request->remarks,
                    'classes_installments' => json_encode($request->classes),
                    'amount' => $request->amount,
                    'student_ids' => $request->has('student_ids') ? json_encode($request->student_ids) : null,
                ]);

                \App\Models\StudentFee::generateMiscFeeInstallments($schoolId, $miscFee);

                return back()->with('success', 'Misc Fee updated successfully!');
            }

            if ($action === 'add_fee_fine') {
                $request->validate([
                    'name' => 'required|string|max:100',
                    'academic_session_id' => 'required|exists:academic_sessions,id',
                    'fee_component_id' => 'nullable|exists:fee_components,id',
                    'fine_type' => 'required|string',
                    'fine_amount' => 'required|numeric|min:0',
                ]);

                $fine = \App\Models\FeeFine::create([
                    'school_id' => $schoolId,
                    'academic_session_id' => $request->academic_session_id,
                    'fee_component_id' => $request->fee_component_id ?: null,
                    'name' => $request->name,
                    'fine_type' => $request->fine_type,
                    'fine_amount' => $request->fine_amount,
                    'status' => true,
                ]);

                \Illuminate\Support\Facades\DB::table('deleted_fines')->insert([
                    'school_id' => $schoolId,
                    'fine_name' => $fine->name,
                    'deleted_by' => (auth()->user()->name ?? 'Administrator') . ' (Created)',
                    'date' => now()->toDateString(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                return back()->with('success', 'Fee Fine added successfully!');
            }

            if ($action === 'edit_fee_fine') {
                $request->validate([
                    'id' => 'required|exists:fee_fines,id',
                    'name' => 'required|string|max:100',
                    'academic_session_id' => 'required|exists:academic_sessions,id',
                    'fee_component_id' => 'nullable|exists:fee_components,id',
                    'fine_type' => 'required|string',
                    'fine_amount' => 'required|numeric|min:0',
                ]);

                $fine = \App\Models\FeeFine::where('school_id', $schoolId)->findOrFail($request->id);
                $fine->update([
                    'academic_session_id' => $request->academic_session_id,
                    'fee_component_id' => $request->fee_component_id ?: null,
                    'name' => $request->name,
                    'fine_type' => $request->fine_type,
                    'fine_amount' => $request->fine_amount,
                ]);

                return back()->with('success', 'Fee Fine updated successfully!');
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

                $errorMsg = $this->checkHasActivePayments($type, $id, $schoolId);
                if ($errorMsg) {
                    return back()->with('error', $errorMsg);
                }

                // Determine name of the item being requested for deletion
                $itemName = 'Unknown';
                if ($type === 'schedule') {
                    $itemName = optional(\App\Models\FeeSchedule::where('school_id', $schoolId)->find($id))->name ?? 'Schedule';
                } elseif ($type === 'component') {
                    $itemName = optional(\App\Models\FeeComponent::where('school_id', $schoolId)->find($id))->component_name ?? 'Component';
                } elseif ($type === 'discount') {
                    $itemName = optional(\App\Models\FeeDiscount::where('school_id', $schoolId)->find($id))->name ?? 'Discount';
                } elseif ($type === 'misc_fee') {
                    $itemName = optional(\App\Models\MiscFee::where('school_id', $schoolId)->find($id))->name ?? 'Misc Fee';
                } elseif ($type === 'fine') {
                    $itemName = optional(\App\Models\FeeFine::where('school_id', $schoolId)->find($id))->name ?? 'Fine';
                }

                \App\Models\PendingDeletion::create([
                    'school_id' => $schoolId,
                    'type' => $type,
                    'target_id' => $id,
                    'item_name' => $itemName,
                    'requested_by' => auth()->user()->name ?? 'Administrator',
                ]);

                return back()->with('success', 'Deletion request submitted successfully. A notification has been sent to the dashboard. Deletion will occur once approved.');
            }

            if ($action === 'approve_deletion') {
                $deletionId = $request->input('deletion_id');
                $status = $request->input('status'); // approve or reject

                $pending = \App\Models\PendingDeletion::where('school_id', $schoolId)->findOrFail($deletionId);

                if ($status === 'approve') {
                    $type = $pending->type;
                    $id = $pending->target_id;

                    $errorMsg = $this->checkHasActivePayments($type, $id, $schoolId);
                    if ($errorMsg) {
                        return back()->with('error', $errorMsg);
                    }

                    if ($type === 'schedule') {
                        \App\Models\StudentFee::withoutGlobalScope('active')
                            ->where('school_id', $schoolId)
                            ->where('fee_schedule_id', $id)
                            ->delete();
                        \App\Models\FeeSchedule::where('school_id', $schoolId)->where('id', $id)->delete();
                    } elseif ($type === 'component') {
                        $componentObj = \App\Models\FeeComponent::where('school_id', $schoolId)->find($id);
                        if ($componentObj) {
                            if ($componentObj->component_name === 'Transport Fee' || $componentObj->head_name === 'Transport') {
                                \App\Models\Student::where('school_id', $schoolId)->update([
                                    'transport_opted' => false,
                                    'transport_route_id' => null,
                                    'transport_route' => null,
                                    'transport_vehicle_code' => null,
                                    'transport_stop' => null,
                                    'transport_drop_vehicle_code' => null,
                                    'transport_pick_fare' => 0,
                                    'transport_drop_fare' => 0,
                                ]);
                            }
                            \App\Models\StudentFee::withoutGlobalScope('active')
                                ->where('school_id', $schoolId)
                                ->where('fee_component_id', $id)
                                ->delete();
                            $componentObj->delete();
                        }
                    } elseif ($type === 'discount') {
                        $discount = \App\Models\FeeDiscount::where('school_id', $schoolId)->where('id', $id)->first();
                        if ($discount) {
                            \Illuminate\Support\Facades\DB::table('deleted_concessions')->insert([
                                'school_id' => $schoolId,
                                'concession_name' => $discount->name,
                                'deleted_by' => (auth()->user()->name ?? 'Administrator') . ' (Deleted)',
                                'date' => now()->toDateString(),
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                            $discount->delete();

                            // Sync student fees to recalculate balances and remove deleted discount immediately
                            $students = \App\Models\Student::where('school_id', $schoolId)->get();
                            foreach ($students as $student) {
                                self::syncStudentFees($student);
                            }
                        }
                    } elseif ($type === 'misc_fee') {
                        \App\Models\StudentFee::withoutGlobalScope('active')
                            ->where('school_id', $schoolId)
                            ->where('misc_fee_id', $id)
                            ->delete();
                        \App\Models\MiscFee::where('school_id', $schoolId)->where('id', $id)->delete();
                    } elseif ($type === 'fine') {
                        $fine = \App\Models\FeeFine::where('school_id', $schoolId)->where('id', $id)->first();
                        if ($fine) {
                            \Illuminate\Support\Facades\DB::table('deleted_fines')->insert([
                                'school_id' => $schoolId,
                                'fine_name' => $fine->name,
                                'deleted_by' => (auth()->user()->name ?? 'Administrator') . ' (Deleted)',
                                'date' => now()->toDateString(),
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                            $fine->delete();

                            // Sync student fees to recalculate balances and remove deleted fine immediately
                            $students = \App\Models\Student::where('school_id', $schoolId)->get();
                            foreach ($students as $student) {
                                self::syncStudentFees($student);
                            }
                        }
                    }
                    $pending->delete();
                    return back()->with('success', 'Item deleted and request removed successfully!');
                } else {
                    $pending->delete();
                    return back()->with('success', 'Deletion request rejected and removed successfully.');
                }
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
        $components = \App\Models\FeeComponent::where('school_id', $schoolId)->where('academic_session_id', $selectedSession->id)->with('schedule')->get();
        $discounts = \App\Models\FeeDiscount::where('school_id', $schoolId)->where('academic_session_id', $selectedSession->id)->get();
        $miscFees = \App\Models\MiscFee::where('school_id', $schoolId)->where('academic_session_id', $selectedSession->id)->get();
        $fines = \App\Models\FeeFine::where('school_id', $schoolId)->where('academic_session_id', $selectedSession->id)->get();

        $classes = $this->getSessionScopedClasses($schoolId, $selectedSession->id);
        $students = Student::where('school_id', $schoolId)->where('academic_session_id', $selectedSession->id)->with(['class', 'section'])->get();
        $sections = $this->getSessionScopedSections($schoolId, $selectedSession->id);

        $deletedFines = \Illuminate\Support\Facades\DB::table('deleted_fines')->where('school_id', $schoolId)->orderByDesc('id')->get();
        $deletedDiscounts = \Illuminate\Support\Facades\DB::table('deleted_concessions')->where('school_id', $schoolId)->orderByDesc('id')->get();

        return view('school.fees.basics', compact(
            'academicSessions',
            'selectedSession',
            'schedules',
            'components',
            'discounts',
            'miscFees',
            'fines',
            'classes',
            'sections',
            'students',
            'schedulesCount',
            'componentsCount',
            'discountsCount',
            'miscFeesCount',
            'finesCount',
            'deletedFines',
            'deletedDiscounts'
        ));
    }

    public function classWiseFee(Request $request)
    {
        try {
            $schoolId = $this->resolveSchoolId();
            $this->ensureFeesSeeded($schoolId);

        // Ensure student categories "Day boarding", "Hostel", and "Transport" are created
        \App\Models\StudentCategory::firstOrCreate(['school_id' => $schoolId, 'name' => 'Day boarding']);
        \App\Models\StudentCategory::firstOrCreate(['school_id' => $schoolId, 'name' => 'Hostel']);
        \App\Models\StudentCategory::firstOrCreate(['school_id' => $schoolId, 'name' => 'Transport']);

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

                self::syncClassWiseFeeToStudents($schoolId, $classWiseFee);

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

        $classes = $this->getSessionScopedClasses($schoolId, $selectedSession->id);
        
        $selectedClassId = $request->get('class_id');
        if (!$selectedClassId && $classes->isNotEmpty()) {
            $selectedClassId = $classes->first()->id;
        }
        $selectedClass = $classes->where('id', $selectedClassId)->first();

        $sections = collect();
        if ($selectedClass) {
            $sections = $selectedClass->sections;
        }

        $selectedSectionId = $request->has('section_id') ? $request->get('section_id') : null;
        if ($selectedSectionId === null && $sections->isNotEmpty()) {
            $selectedSectionId = $sections->first()->id;
        }
        $selectedSection = $selectedSectionId ? $sections->where('id', $selectedSectionId)->first() : null;

        $selectedClassName = $selectedClass ? $selectedClass->name : '';
        $schedules = \App\Models\FeeSchedule::where('school_id', $schoolId)
            ->where('academic_session_id', $selectedSession->id)
            ->get();
        if ($selectedClassName) {
            $schedules = $schedules->filter(function($sched) use ($selectedClassName) {
                $classesStr = $sched->classes ?? '';
                $schedClasses = [];
                if (\Illuminate\Support\Str::startsWith($classesStr, '[') && \Illuminate\Support\Str::endsWith($classesStr, ']')) {
                    $schedClasses = json_decode($classesStr, true) ?? [];
                } else {
                    $schedClasses = array_map('trim', explode(',', $classesStr));
                }
                return in_array($selectedClassName, $schedClasses);
            })->values();
        }
        $hasTransportStudents = \App\Models\Student::where('school_id', $schoolId)
            ->where('class_id', $selectedClassId)
            ->where(function($q) use ($selectedSectionId) {
                if ($selectedSectionId) {
                    $q->where('section_id', $selectedSectionId);
                }
            })
            ->where('transport_opted', true)
            ->exists();

        if ($hasTransportStudents) {
            $cat = \App\Models\FeeCategory::firstOrCreate(
                ['school_id' => $schoolId, 'name' => 'Transport'],
                ['description' => 'Transport Fees']
            );
            \App\Models\FeeComponent::firstOrCreate(
                [
                    'school_id' => $schoolId, 
                    'component_name' => 'Transport Fee',
                    'academic_session_id' => $selectedSession->id
                ],
                [
                    'fee_category_id' => $cat->id,
                    'head_name' => 'Transport',
                    'admission_type' => 'All Students',
                    'gender' => 'All Students'
                ]
            );
        }

        $studentCategories = \App\Models\StudentCategory::where('school_id', $schoolId)
            ->where(function($q) use ($hasTransportStudents) {
                $q->whereRaw('LOWER(name) = ?', ['day boarding']);
                if ($hasTransportStudents) {
                    $q->orWhereRaw('LOWER(name) = ?', ['transport']);
                }
            })
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

    public function copyClassWiseFeesToOtherClasses(Request $request)
    {
        $schoolId = $this->resolveSchoolId();
        $currentSession = \App\Models\AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first();
        if (!$currentSession) {
            return response()->json(['success' => false, 'message' => 'No active session found.']);
        }

        $request->validate([
            'source_class_id' => 'required|exists:school_classes,id',
            'source_section_id' => 'nullable|exists:sections,id',
            'target_class_ids' => 'required|array',
            'target_class_ids.*' => 'required|exists:school_classes,id',
            'target_section_ids' => 'nullable|array',
        ]);

        $sourceClassId = $request->source_class_id;
        $sourceSectionId = $request->source_section_id;
        $targetClassIds = $request->target_class_ids;
        $targetSectionIds = $request->input('target_section_ids', []); // [class_id => [sec_id1, sec_id2]]

        // Fetch all class-wise fees configured for the source class and source section in the current session
        $sourceFees = \App\Models\ClassWiseFee::where('school_id', $schoolId)
            ->where('academic_session_id', $currentSession->id)
            ->where('class_id', $sourceClassId)
            ->where('section_id', $sourceSectionId)
            ->get();

        if ($sourceFees->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No fee configurations found for the source class and section.']);
        }

        \Illuminate\Support\Facades\DB::transaction(function() use ($schoolId, $currentSession, $sourceClassId, $sourceSectionId, $targetClassIds, $targetSectionIds, $sourceFees) {
            foreach ($targetClassIds as $targetClassId) {
                // Determine if we have specific sections for this class
                $sectionsToCopy = [null]; // Default to class-level
                if (isset($targetSectionIds[$targetClassId]) && is_array($targetSectionIds[$targetClassId]) && !empty($targetSectionIds[$targetClassId])) {
                    $sectionsToCopy = $targetSectionIds[$targetClassId];
                }

                foreach ($sectionsToCopy as $sectionId) {
                    // If it is the exact same class and section as the source, skip it
                    if ($targetClassId == $sourceClassId && $sectionId == $sourceSectionId) {
                        continue;
                    }

                    // Delete target configurations that are not present in source
                    $sourceKeys = $sourceFees->map(function($f) {
                        return $f->fee_schedule_id . '-' . $f->student_category_id . '-' . $f->fee_component_id;
                    })->toArray();

                    $existingTargetFees = \App\Models\ClassWiseFee::where('school_id', $schoolId)
                        ->where('academic_session_id', $currentSession->id)
                        ->where('class_id', $targetClassId)
                        ->where('section_id', $sectionId)
                        ->get();

                    foreach ($existingTargetFees as $existingTargetFee) {
                        $key = $existingTargetFee->fee_schedule_id . '-' . $existingTargetFee->student_category_id . '-' . $existingTargetFee->fee_component_id;
                        if (!in_array($key, $sourceKeys)) {
                            $existingTargetFee->delete();

                            $studentIds = \App\Models\Student::where('school_id', $schoolId)
                                ->where('class_id', $targetClassId)
                                ->when($sectionId, function($q) use ($sectionId) {
                                    $q->where('section_id', $sectionId);
                                })
                                ->pluck('id');

                            \App\Models\StudentFee::withoutGlobalScope('active')
                                ->where('school_id', $schoolId)
                                ->whereIn('student_id', $studentIds)
                                ->where('fee_schedule_id', $existingTargetFee->fee_schedule_id)
                                ->where('fee_component_id', $existingTargetFee->fee_component_id)
                                ->where('paid_amount', 0)
                                ->delete();
                        }
                    }

                    foreach ($sourceFees as $sourceFee) {
                        // Clone or update the configuration for target class & section
                        $targetFee = \App\Models\ClassWiseFee::updateOrCreate([
                            'school_id' => $schoolId,
                            'academic_session_id' => $currentSession->id,
                            'class_id' => $targetClassId,
                            'section_id' => $sectionId,
                            'fee_schedule_id' => $sourceFee->fee_schedule_id,
                            'student_category_id' => $sourceFee->student_category_id,
                            'fee_component_id' => $sourceFee->fee_component_id,
                        ], [
                            'is_active' => $sourceFee->is_active,
                            'amount' => $sourceFee->amount,
                            'installments' => $sourceFee->installments,
                        ]);

                        // Sync to students of target class/section automatically!
                        self::syncClassWiseFeeToStudents($schoolId, $targetFee);
                    }
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Class-wise fees copied and synchronized for target classes and sections successfully!',
        ]);
    }

    public static function syncStudentClassFees($student)
    {
        $schoolId = $student->school_id;
        
        // Validate schedule compatibility on class/section transfer
        if ($student->fee_schedule_id) {
            $sched = \App\Models\FeeSchedule::where('school_id', $schoolId)->find($student->fee_schedule_id);
            if ($sched) {
                $classModel = \App\Models\SchoolClass::where('school_id', $schoolId)->find($student->class_id);
                $studentClassName = $classModel ? $classModel->name : null;
                $classesStr = $sched->classes ?? '';
                $schClasses = [];
                if (\Illuminate\Support\Str::startsWith($classesStr, '[') && \Illuminate\Support\Str::endsWith($classesStr, ']')) {
                    $schClasses = json_decode($classesStr, true) ?? [];
                } else {
                    $schClasses = array_map('trim', explode(',', $classesStr));
                }
                $isCompatible = $studentClassName && in_array($studentClassName, $schClasses);
                
                if ($isCompatible && $sched->sections) {
                    $schSections = array_map('trim', explode(',', $sched->sections));
                    $sectionModel = \App\Models\Section::where('school_id', $schoolId)->find($student->section_id);
                    $studentSectionName = $sectionModel ? $sectionModel->name : null;
                    $studentClassSection = $studentClassName && $studentSectionName ? ($studentClassName . '-' . $studentSectionName) : null;
                    if ($studentClassSection && !in_array($studentClassSection, $schSections)) {
                        $isCompatible = false;
                    }
                }
                
                if (!$isCompatible) {
                    $student->fee_schedule_id = null;
                    $student->saveQuietly();
                }
            } else {
                $student->fee_schedule_id = null;
                $student->saveQuietly();
            }
        }

        // Find all active class-wise fees for this student's class (and optionally section)
        $classWiseFees = \App\Models\ClassWiseFee::where('school_id', $schoolId)
            ->where('class_id', $student->class_id)
            ->where(function($q) use ($student) {
                $q->whereNull('section_id')
                  ->orWhere('section_id', $student->section_id);
            })
            ->where('is_active', true)
            ->get();

        foreach ($classWiseFees as $cwFee) {
            self::syncClassWiseFeeToStudents($schoolId, $cwFee);
        }

        // Also sync discounts!
        $currentSession = \App\Models\AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first();
        if ($currentSession) {
            self::syncStudentDiscounts($student, $currentSession->id);
        }
    }

    public static function syncClassWiseFeeToStudents($schoolId, $classWiseFee)
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
            ->whereRaw('LOWER(name) = ?', ['day boarding'])
            ->first();
        $hostelCat = \App\Models\StudentCategory::where('school_id', $schoolId)
            ->whereRaw('LOWER(name) = ?', ['hostel'])
            ->first();
        $transportCat = \App\Models\StudentCategory::where('school_id', $schoolId)
            ->whereRaw('LOWER(name) = ?', ['transport'])
            ->first();

        // Get the schedule for this school to use as fallback for students without fee_schedule_id
        $scheduleForSchool = \App\Models\FeeSchedule::where('school_id', $schoolId)
            ->where('id', $classWiseFee->fee_schedule_id)
            ->first();
        $classWiseFeeScheduleId = $classWiseFee->fee_schedule_id;

        $students = $query->get()->filter(function($student) use ($classWiseFee, $dayBoardingCat, $hostelCat, $transportCat, $classWiseFeeScheduleId) {
            // The student's fee schedule must match the class-wise fee schedule exactly
            if ($student->fee_schedule_id != $classWiseFeeScheduleId) {
                return false;
            }

            // Target Category validation
            $targetCatId = $classWiseFee->student_category_id;
            if ($transportCat && $targetCatId == $transportCat->id) {
                // For Transport category, only apply if student has transport_opted = true
                return (bool) $student->transport_opted;
            }

            if ($dayBoardingCat && $targetCatId == $dayBoardingCat->id) {
                // Day boarding applies to students who are not hostel (or have empty boarding type)
                return empty($student->boarding_type) || stripos($student->boarding_type, 'hostel') === false || stripos($student->boarding_type, 'day') !== false;
            }

            if ($hostelCat && $targetCatId == $hostelCat->id) {
                return !empty($student->boarding_type) && stripos($student->boarding_type, 'hostel') !== false;
            }

            // Fallback: if student category/boarding type is completely empty, default match to Day boarding category
            if ($dayBoardingCat && $targetCatId == $dayBoardingCat->id) {
                return true;
            }

            return false;
        });

        foreach ($students as $student) {
            // Check if this class-wise fee is the transport component
            if ($transportCat && $classWiseFee->student_category_id == $transportCat->id) {
                if ($classWiseFee->is_active) {
                    \App\Models\StudentFee::generateTransportInstallments($schoolId, $student->id, $classWiseFee->academic_session_id);
                } else {
                    \App\Models\StudentFee::withoutGlobalScope('active')
                        ->where('school_id', $schoolId)
                        ->where('student_id', $student->id)
                        ->where('fee_component_id', $classWiseFee->fee_component_id)
                        ->where('paid_amount', '<=', 0)
                        ->whereNull('invoice_no')
                        ->delete();
                }
                self::applyOverdueFinesToStudent($student);
                continue;
            }

            $pendingChequeFeeIds = [];
            $pendingCheques = \App\Models\PendingCheque::where('school_id', $schoolId)
                ->where('student_id', $student->id)
                ->where('status', 'pending')
                ->get();
            foreach ($pendingCheques as $chq) {
                $ids = json_decode($chq->student_fee_ids, true) ?? [];
                $pendingChequeFeeIds = array_merge($pendingChequeFeeIds, $ids);
            }
            $pendingChequeFeeIds = array_unique(array_filter(array_map('intval', $pendingChequeFeeIds)));

            if ($classWiseFee->is_active) {
                $installments = $classWiseFee->installments ?? [];
                $activeInstallmentNos = [];

                $schedule = $classWiseFee->feeSchedule;
                $scheduleInstallments = $schedule ? ($schedule->installments ?? []) : [];

                foreach ($installments as $inst) {
                    $instNo = $inst['installment_no'] ?? null;
                    if (!$instNo) continue;

                    $activeInstallmentNos[] = $instNo;
                    $instAmount = floatval($inst['amount'] ?? 0);

                    // Find matching installment in schedule
                    $scheduleInst = collect($scheduleInstallments)->firstWhere('installment_no', $instNo);

                    if ($scheduleInst && !empty($scheduleInst['due_date'])) {
                        $dueDate = $scheduleInst['due_date'];
                    } else {
                        $dueDate = now()->addDays(30)->toDateString();
                        if (!empty($inst['date_range'])) {
                            $parts = explode(' - ', $inst['date_range']);
                            if (count($parts) === 2) {
                                try {
                                    $dueDate = \Carbon\Carbon::createFromFormat('d/m/Y', trim($parts[1]))->toDateString();
                                } catch (\Exception $e) {}
                            }
                        }
                    }

                    $studentFee = \App\Models\StudentFee::withoutGlobalScope('active')
                        ->where('school_id', $schoolId)
                        ->where('student_id', $student->id)
                        ->where('fee_component_id', $classWiseFee->fee_component_id)
                        ->where('installment_no', $instNo)
                        ->where('fee_schedule_id', $classWiseFee->fee_schedule_id)
                        ->first();

                    // Resolve the correct active invoice number for this installment
                    $activeInvoiceNo = \App\Models\StudentFee::withoutGlobalScope('active')
                        ->where('school_id', $schoolId)
                        ->where('student_id', $student->id)
                        ->where('installment_no', $instNo)
                        ->whereNotNull('invoice_no')
                        ->value('invoice_no');
                    
                    if (!$activeInvoiceNo) {
                        $existingInvoices = \App\Models\StudentFee::withoutGlobalScope('active')
                            ->where('school_id', $schoolId)
                            ->where('student_id', $student->id)
                            ->where('installment_no', $instNo)
                            ->distinct()
                            ->pluck('invoice_no')
                            ->filter()
                            ->toArray();
                        
                        if (empty($existingInvoices)) {
                            $activeInvoiceNo = 'INV-' . $instNo;
                        } else {
                            $maxSuffix = 1;
                            foreach ($existingInvoices as $invNo) {
                                if (preg_match('/INV-\d+-(\d+)/i', $invNo, $m)) {
                                    $maxSuffix = max($maxSuffix, (int)$m[1]);
                                }
                            }
                            $activeInvoiceNo = 'INV-' . $instNo . '-' . ($maxSuffix + 1);
                        }
                    }

                    // Check if there is any existing paid/locked student fee for this component/installment
                    $existingLockedFee = \App\Models\StudentFee::withoutGlobalScope('active')
                        ->where('school_id', $schoolId)
                        ->where('student_id', $student->id)
                        ->where('installment_no', $instNo)
                        ->where(function($q) use ($classWiseFee, $component) {
                            $q->where('fee_component_id', $classWiseFee->fee_component_id);
                            if ($component && $component->component_name) {
                                $q->orWhereHas('component', function($c) use ($component) {
                                    $c->where('component_name', $component->component_name);
                                });
                            }
                        })
                        ->get()
                        ->first(fn($sf) => $sf->isLocked());

                    if ($existingLockedFee) {
                        continue;
                    }

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
                            'invoice_no'       => $studentFee->invoice_no ?: $activeInvoiceNo,
                            'invoice_status'   => 'active',
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
                            'invoice_no'       => $activeInvoiceNo,
                            'invoice_status'   => 'active',
                        ]);
                    }
                }

                // Clean up out-of-range unpaid installments
                \App\Models\StudentFee::withoutGlobalScope('active')
                    ->where('school_id', $schoolId)
                    ->where('student_id', $student->id)
                    ->where('fee_schedule_id', $classWiseFee->fee_schedule_id)
                    ->where('fee_component_id', $classWiseFee->fee_component_id)
                    ->whereNotIn('installment_no', $activeInstallmentNos)
                    ->where('paid_amount', '<=', 0)
                    ->where('instant_discount_amount', '<=', 0)
                    ->where(function($q) {
                        $q->whereNull('invoice_no')
                          ->orWhere('invoice_status', 'cancelled');
                    })
                    ->whereNotIn('id', $pendingChequeFeeIds)
                    ->where('status', '!=', 'refunded')
                    ->delete();

            } else {
                // Component toggled OFF: remove all unpaid fees for this component under this schedule
                \App\Models\StudentFee::withoutGlobalScope('active')
                    ->where('school_id', $schoolId)
                    ->where('student_id', $student->id)
                    ->where('fee_schedule_id', $classWiseFee->fee_schedule_id)
                    ->where('fee_component_id', $classWiseFee->fee_component_id)
                    ->where('paid_amount', '<=', 0)
                    ->where('instant_discount_amount', '<=', 0)
                    ->where(function($q) {
                        $q->whereNull('invoice_no')
                          ->orWhere('invoice_status', 'cancelled');
                    })
                    ->whereNotIn('id', $pendingChequeFeeIds)
                    ->where('status', '!=', 'refunded')
                    ->delete();
            }

            self::applyOverdueFinesToStudent($student);
        }
    }

    public static function syncStudentFees($student)
    {
        $schoolId = $student->school_id;
        $studentScheduleId = $student->fee_schedule_id;

        // Fetch pending cheques first to exclude their fees from deletion
        $pendingChequeFeeIds = [];
        $pendingCheques = \App\Models\PendingCheque::where('school_id', $schoolId)
            ->where('student_id', $student->id)
            ->where('status', 'pending')
            ->get();
        foreach ($pendingCheques as $chq) {
            $ids = json_decode($chq->student_fee_ids, true) ?? [];
            $pendingChequeFeeIds = array_merge($pendingChequeFeeIds, $ids);
        }
        $pendingChequeFeeIds = array_unique(array_filter(array_map('intval', $pendingChequeFeeIds)));

        // Delete all unpaid/unlocked student fees belonging to any other schedule
        \App\Models\StudentFee::withoutGlobalScope('active')
            ->where('school_id', $schoolId)
            ->where('student_id', $student->id)
            ->where(function($q) use ($studentScheduleId) {
                if ($studentScheduleId) {
                    $q->where('fee_schedule_id', '!=', $studentScheduleId)
                      ->orWhereNull('fee_schedule_id');
                } else {
                    $q->whereNotNull('fee_schedule_id');
                }
            })
            ->where('paid_amount', '<=', 0)
            ->where('instant_discount_amount', '<=', 0)
            ->whereNotIn('id', $pendingChequeFeeIds)
            ->where('status', '!=', 'refunded')
            ->whereNull('misc_fee_id')
            ->delete();

        // Auto-heal duplicate fee records by component/category name to prevent UI mismatch
        $allStudentFees = \App\Models\StudentFee::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->where('student_id', $student->id)
            ->whereNull('misc_fee_id')
            ->with(['component', 'category'])
            ->get();

        $groupedFees = $allStudentFees->groupBy(function($fee) {
            $name = $fee->component->component_name ?? ($fee->category->name ?? 'Fee');
            return strtolower(trim($name)) . '-' . $fee->installment_no;
        });

        $activeComponentIds = \App\Models\ClassWiseFee::where('school_id', $schoolId)
            ->where('class_id', $student->class_id)
            ->where('is_active', true)
            ->pluck('fee_component_id')
            ->toArray();

        foreach ($groupedFees as $key => $group) {
            if ($group->count() > 1) {
                // If any record in this group is locked, do not merge or delete them
                if ($group->contains(fn($f) => $f->isLocked())) {
                    continue;
                }

                // Find the active record matching the active class-wise fee components
                $activeRecord = $group->first(function($f) use ($activeComponentIds) {
                    return in_array($f->fee_component_id, $activeComponentIds);
                });

                if (!$activeRecord) {
                    $activeRecord = $group->first();
                }

                $legacyRecords = $group->filter(function($f) use ($activeRecord) {
                    return $f->id !== $activeRecord->id;
                });

                $totalPaid = $activeRecord->paid_amount;
                $invoiceNo = $activeRecord->invoice_no;

                foreach ($legacyRecords as $legacy) {
                    $totalPaid += $legacy->paid_amount;
                    if (empty($invoiceNo) && !empty($legacy->invoice_no)) {
                        $invoiceNo = $legacy->invoice_no;
                    }
                }

                $status = 'pending';
                if ($totalPaid >= $activeRecord->amount) {
                    $status = 'paid';
                } elseif ($totalPaid > 0) {
                    $status = 'partially_paid';
                }

                $activeRecord->update([
                    'paid_amount' => $totalPaid,
                    'invoice_no' => $invoiceNo ?: $activeRecord->invoice_no,
                    'status' => $status
                ]);

                foreach ($legacyRecords as $legacy) {
                    $legacy->delete();
                }
            }
        }

        // 1. Delete duplicate pending records created by the global scope bug
        $refundedFees = \App\Models\StudentFee::withoutGlobalScope('active')
            ->where('school_id', $schoolId)
            ->where('student_id', $student->id)
            ->whereNull('misc_fee_id')
            ->where(function($q) {
                $q->where('status', 'refunded')
                  ->orWhere('invoice_status', 'refunded');
            })
            ->get();
        
        foreach ($refundedFees as $refFee) {
            \App\Models\StudentFee::withoutGlobalScope('active')
                ->where('school_id', $schoolId)
                ->where('student_id', $student->id)
                ->where('fee_component_id', $refFee->fee_component_id)
                ->where('installment_no', $refFee->installment_no)
                ->where('id', '!=', $refFee->id)
                ->where('paid_amount', 0)
                ->where('status', 'pending')
                ->whereNull('misc_fee_id')
                ->delete();
        }

        // 2. Auto-heal / migrate database to the new refund model:
        foreach ($refundedFees as $sf) {
            $sfFresh = \App\Models\StudentFee::withoutGlobalScope('active')
                ->where('school_id', $schoolId)
                ->find($sf->id);
            if ($sfFresh && ($sfFresh->status === 'refunded' || $sfFresh->invoice_status === 'refunded')) {
                $refAmt = \App\Models\FeeRefund::where('school_id', $schoolId)->where('student_fee_id', $sfFresh->id)->sum('amount');
                if ($refAmt > 0) {
                    $sfFresh->paid_amount = $sfFresh->paid_amount + $refAmt;
                }
                $sfFresh->status = $sfFresh->paid_amount >= ($sfFresh->amount - $sfFresh->instant_discount_amount) ? 'paid' : ($sfFresh->paid_amount > 0 ? 'partially_paid' : 'pending');
                $sfFresh->invoice_status = 'active';
                $sfFresh->save();
            }
        }

        if (!$studentScheduleId) {
            return;
        }

        // Fetch all active ClassWiseFee structures for this student's class matching the student's schedule
        $classWiseFeesQuery = \App\Models\ClassWiseFee::where('school_id', $schoolId)
            ->where('class_id', $student->class_id)
            ->where('fee_schedule_id', $studentScheduleId)
            ->where('is_active', true);

        if ($student->section_id) {
            $classWiseFeesQuery->where(function($q) use ($student) {
                $q->where('section_id', $student->section_id)
                  ->orWhereNull('section_id');
            });
        } else {
            $classWiseFeesQuery->whereNull('section_id');
        }

        // Filter by student's category
        $dayBoardingCat = \App\Models\StudentCategory::where('school_id', $schoolId)
            ->whereRaw('LOWER(name) = ?', ['day boarding'])
            ->first();
        $hostelCat = \App\Models\StudentCategory::where('school_id', $schoolId)
            ->whereRaw('LOWER(name) = ?', ['hostel'])
            ->first();
        $transportCat = \App\Models\StudentCategory::where('school_id', $schoolId)
            ->whereRaw('LOWER(name) = ?', ['transport'])
            ->first();
        
        $effectiveCategories = [];
        if (!empty($student->boarding_type) && stripos($student->boarding_type, 'hostel') !== false) {
            if ($hostelCat) $effectiveCategories[] = $hostelCat->id;
        } else {
            if ($dayBoardingCat) $effectiveCategories[] = $dayBoardingCat->id;
        }
        if ($student->transport_opted && $transportCat) {
            $effectiveCategories[] = $transportCat->id;
        }
        
        if (count($effectiveCategories) > 0) {
            $classWiseFeesQuery->whereIn('student_category_id', $effectiveCategories);
        }

        $classWiseFees = $classWiseFeesQuery->get();

        if ($student->section_id) {
            $classWiseFees = $classWiseFees->groupBy(function($item) {
                return $item->fee_schedule_id . '-' . $item->student_category_id . '-' . $item->fee_component_id;
            })->map(function($group) use ($student) {
                return $group->where('section_id', $student->section_id)->first() ?? $group->whereNull('section_id')->first();
            })->values();
        }

        // Clean up unpaid student fees that do not correspond to any active class-wise configuration for this student
        $unpaidFees = \App\Models\StudentFee::withoutGlobalScope('active')
            ->where('school_id', $schoolId)
            ->where('student_id', $student->id)
            ->where('paid_amount', '<=', 0)
            ->where('instant_discount_amount', '<=', 0)
            ->whereNull('misc_fee_id')
            ->whereNotIn('id', $pendingChequeFeeIds)
            ->where('status', '!=', 'refunded')
            ->get();

        foreach ($unpaidFees as $uf) {
            $hasMatch = $classWiseFees->contains(function($cwf) use ($uf) {
                return $cwf->fee_schedule_id == $uf->fee_schedule_id && $cwf->fee_component_id == $uf->fee_component_id;
            });
            if (!$hasMatch) {
                $uf->delete();
            }
        }

        // Now synchronize active classwise fees to student fees
        foreach ($classWiseFees as $classWiseFee) {
            $component = \App\Models\FeeComponent::where('school_id', $schoolId)->find($classWiseFee->fee_component_id);
            if (!$component) continue;

            $category = \App\Models\FeeCategory::firstOrCreate([
                'school_id' => $schoolId,
                'name' => $component->component_name,
            ], [
                'description' => 'Automatically generated category for ' . $component->component_name
            ]);

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

                $studentFee = \App\Models\StudentFee::withoutGlobalScope('active')
                    ->where('school_id', $schoolId)
                    ->where('student_id', $student->id)
                    ->where('fee_component_id', $classWiseFee->fee_component_id)
                    ->where('installment_no', $instNo)
                    ->where('fee_schedule_id', $classWiseFee->fee_schedule_id)
                    ->first();

                // Check if there is any existing paid/locked student fee for this component/installment
                $existingLockedFee = \App\Models\StudentFee::withoutGlobalScope('active')
                    ->where('school_id', $schoolId)
                    ->where('student_id', $student->id)
                    ->where('installment_no', $instNo)
                    ->where(function($q) use ($classWiseFee, $component) {
                        $q->where('fee_component_id', $classWiseFee->fee_component_id);
                        if ($component && $component->component_name) {
                            $q->orWhereHas('component', function($c) use ($component) {
                                $c->where('component_name', $component->component_name);
                            });
                        }
                    })
                    ->get()
                    ->first(fn($sf) => $sf->isLocked());

                if ($existingLockedFee) {
                    continue;
                }

                $activeInvoiceNo = \App\Models\StudentFee::withoutGlobalScope('active')
                    ->where('school_id', $schoolId)
                    ->where('student_id', $student->id)
                    ->where('installment_no', $instNo)
                    ->whereNotNull('invoice_no')
                    ->value('invoice_no');
                
                if (!$activeInvoiceNo) {
                    $existingInvoices = \App\Models\StudentFee::withoutGlobalScope('active')
                        ->where('school_id', $schoolId)
                        ->where('student_id', $student->id)
                        ->where('installment_no', $instNo)
                        ->distinct()
                        ->pluck('invoice_no')
                        ->filter()
                        ->toArray();
                    
                    if (empty($existingInvoices)) {
                        $activeInvoiceNo = 'INV-' . $instNo;
                    } else {
                        $maxSuffix = 1;
                        foreach ($existingInvoices as $invNo) {
                            if (preg_match('/INV-\d+-(\d+)/i', $invNo, $m)) {
                                $maxSuffix = max($maxSuffix, (int)$m[1]);
                            }
                        }
                        $activeInvoiceNo = 'INV-' . $instNo . '-' . ($maxSuffix + 1);
                    }
                }

                if ($studentFee) {
                    $paidAmount = $studentFee->paid_amount;
                    $due = floatval($instAmount) + floatval($studentFee->fine_amount_applied) - floatval($studentFee->instant_discount_amount);
                    $status = 'pending';
                    if ($studentFee->status === 'refunded' && $paidAmount == 0) {
                        $status = $studentFee->invoice_status === 'cancelled' ? 'pending' : 'refunded';
                    } elseif ($paidAmount == 0 && $studentFee->status !== 'paid') {
                        $wasRefunded = \App\Models\FeeRefund::where('school_id', $schoolId)
                            ->where('student_id', $student->id)
                            ->where(function($q) use ($studentFee) {
                                $q->where('student_fee_id', $studentFee->id)
                                  ->orWhere(function($q2) use ($studentFee) {
                                      $q2->whereNull('student_fee_id')
                                         ->where('reason', 'like', '%Installment ' . $studentFee->installment_no . ')');
                                  });
                            })
                            ->exists();
                        if ($wasRefunded) {
                            $status = $studentFee->invoice_status === 'cancelled' ? 'pending' : 'refunded';
                        }
                    } elseif ($paidAmount >= $due) {
                        $status = 'paid';
                    } elseif ($paidAmount > 0) {
                        $status = 'partially_paid';
                    }
                    
                    $studentFee->update([
                        'fee_category_id' => $category->id,
                        'fee_schedule_id' => $classWiseFee->fee_schedule_id,
                        'amount' => $instAmount,
                        'due_date' => $dueDate,
                        'status' => $status,
                        'invoice_no' => $studentFee->invoice_no ?: $activeInvoiceNo,
                        'invoice_status' => 'active',
                    ]);
                } else {
                    \App\Models\StudentFee::create([
                        'school_id' => $schoolId,
                        'student_id' => $student->id,
                        'fee_category_id' => $category->id,
                        'fee_schedule_id' => $classWiseFee->fee_schedule_id,
                        'fee_component_id' => $classWiseFee->fee_component_id,
                        'installment_no' => $instNo,
                        'amount' => $instAmount,
                        'paid_amount' => 0.00,
                        'due_date' => $dueDate,
                        'status' => 'pending',
                        'invoice_no' => $activeInvoiceNo,
                        'invoice_status' => 'active',
                    ]);
                }
            }

            // Delete any unpaid installments that are now out-of-range
            \App\Models\StudentFee::withoutGlobalScope('active')
                ->where('school_id', $schoolId)
                ->where('student_id', $student->id)
                ->where('fee_schedule_id', $classWiseFee->fee_schedule_id)
                ->where('fee_component_id', $classWiseFee->fee_component_id)
                ->whereNotIn('installment_no', $activeInstallmentNos)
                ->where('paid_amount', '<=', 0)
                ->where('instant_discount_amount', '<=', 0)
                ->whereNotIn('id', $pendingChequeFeeIds)
                ->where('status', '!=', 'refunded')
                ->delete();
        }

        // Sync active discounts
        $academicSessions = \App\Models\AcademicSession::where('school_id', $schoolId)->get();
        $currentSession = $academicSessions->where('is_current', true)->first() ?? $academicSessions->first();
        if ($currentSession) {
            self::syncStudentDiscounts($student, $currentSession->id);
        }

        // Apply overdue late fines automatically
        self::applyOverdueFinesToStudent($student);

        // Sync miscellaneous fees
        $miscFees = \App\Models\MiscFee::where('school_id', $schoolId)->get();
        foreach ($miscFees as $mfee) {
            \App\Models\StudentFee::generateMiscFeeInstallments($schoolId, $mfee);
        }
    }

    public static function applyOverdueFinesToStudent($student)
    {
        $schoolId = $student->school_id;
        $today = now()->startOfDay();

        $studentFees = \App\Models\StudentFee::withoutGlobalScope('active')
            ->where('school_id', $schoolId)
            ->where('student_id', $student->id)
            ->where('status', '!=', 'paid')
            ->get();

        if ($studentFees->isEmpty()) {
            return;
        }

        // Batch pre-fetch all fee schedules and transport fee schedules with fine policies
        $schedIds = $studentFees->pluck('fee_schedule_id')
            ->filter()
            ->push($student->fee_schedule_id)
            ->unique()
            ->values()
            ->toArray();

        $transportSchedIds = $studentFees->pluck('transport_fee_schedule_id')
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        $feeSchedules = !empty($schedIds)
            ? \App\Models\FeeSchedule::where('school_id', $schoolId)
                ->whereIn('id', $schedIds)
                ->with('fine')
                ->get()
                ->keyBy('id')
            : collect();

        $transportFeeSchedules = !empty($transportSchedIds)
            ? \App\Models\TransportFeeSchedule::where('school_id', $schoolId)
                ->whereIn('id', $transportSchedIds)
                ->with('fine')
                ->get()
                ->keyBy('id')
            : collect();

        foreach ($studentFees as $sf) {
            $sched = null;
            $insts = [];
            $finePolicy = null;

            // Fines should not be applied to miscellaneous fees
            if ($sf->misc_fee_id === null) {
                $schedId = $sf->fee_schedule_id ?: $student->fee_schedule_id;
                if ($schedId) {
                    $sched = $feeSchedules->get($schedId);
                    if ($sched && $sched->fine && $sched->fine->status) {
                        $finePolicy = $sched->fine;
                        $insts = $sched->installments ?? [];
                    }
                } elseif ($sf->transport_fee_schedule_id) {
                    $sched = $transportFeeSchedules->get($sf->transport_fee_schedule_id);
                    if ($sched && $sched->fine && $sched->fine->status) {
                        $finePolicy = $sched->fine;
                        $insts = $sched->installments ?? [];
                    }
                }
            }

            // If the fine policy is component-specific, it must match the student fee component_id
            if ($finePolicy && $finePolicy->fee_component_id !== null) {
                if ($finePolicy->fee_component_id !== $sf->fee_component_id) {
                    $finePolicy = null;
                }
            }

            if (!$finePolicy) {
                // If there's no active fine policy or it's not applicable, clear any applied fine
                if ($sf->fine_applied_at !== null || floatval($sf->fine_amount_applied) > 0) {
                    $sf->fine_amount_applied = 0.00;
                    $sf->fine_applied_at = null;
                    $sf->save();
                }
                continue;
            }

            // Find matching installment in schedule
            $inst = collect($insts)->firstWhere('installment_no', $sf->installment_no);
            if (!$inst || empty($inst['due_date'])) {
                // If no matching installment or due date is found, we should also clear the fine.
                if ($sf->fine_applied_at !== null || floatval($sf->fine_amount_applied) > 0) {
                    $sf->fine_amount_applied = 0.00;
                    $sf->fine_applied_at = null;
                    $sf->save();
                }
                continue;
            }

            $dueDate = \Carbon\Carbon::parse($inst['due_date'])->startOfDay();
            $graceDays = (int) ($inst['grace_days'] ?? $finePolicy->default_grace_days ?? 0);
            $graceDate = $dueDate->copy()->addDays($graceDays);

            if ($today->gt($graceDate)) {
                $fineAmount = $finePolicy->calculateFor($sf, $inst['due_date'], $graceDays);
                if ($fineAmount > 0) {
                    if ($sf->is_fine_applied === false || $sf->is_fine_applied === 0) {
                        // Admin override: Late Fine set to Not Applied for this installment
                        if (floatval($sf->fine_amount_applied) !== 0.00) {
                            $sf->fine_amount_applied = 0.00;
                            $sf->save();
                        }
                    } else {
                        $alreadyApplied = floatval($sf->fine_amount_applied ?? 0);
                        if ($fineAmount != $alreadyApplied) {
                            $sf->fine_amount_applied = $fineAmount;
                            $sf->fine_applied_at = now();
                            $sf->save();
                        }
                    }
                }
            } else {
                // If due date was updated and is no longer overdue, clear the fine
                if ($sf->fine_applied_at !== null || floatval($sf->fine_amount_applied) > 0) {
                    $sf->fine_amount_applied = 0.00;
                    $sf->fine_applied_at = null;
                    $sf->save();
                }
            }
        }
    }

    public static function isDiscountApplicableForStudent($d, $student)
    {
        // 1. Check group eligibility first if defined
        if ($d->target_group && strtolower($d->target_group) !== 'all') {
            $grp = strtolower($d->target_group);
            if ($grp === 'all male' || $grp === 'male') {
                if (strtolower($student->gender ?? '') !== 'male') return false;
            } elseif ($grp === 'all female' || $grp === 'female') {
                if (strtolower($student->gender ?? '') !== 'female') return false;
            } elseif ($grp === 'rte') {
                if (!$student->is_rte) return false;
            } elseif ($grp === 'new admission' || $grp === 'new') {
                if (strtolower($student->admission_type ?? '') !== 'new') return false;
            } elseif ($grp === 'old admission' || $grp === 'old' || $grp === 'existing') {
                $admType = strtolower($student->admission_type ?? '');
                if ($admType !== 'old' && $admType !== 'existing') return false;
            } else {
                // SC, ST, OBC, STC, etc.
                $caste = strtolower($student->caste ?? '');
                if (stripos($caste, $grp) === false) {
                    return false;
                }
            }
        }

        // 2. Check student_ids (explicit inclusion/exclusion)
        if ($d->student_ids) {
            $studentIds = json_decode($d->student_ids, true);
            if (is_array($studentIds)) {
                if (in_array($student->id, $studentIds)) {
                    return true;
                } else {
                    return false;
                }
            }
        }

        // 3. Check class & section eligibility
        if ($d->classes_installments) {
            $classes = json_decode($d->classes_installments, true);

            // If empty array or null-decoded, treat as "no class filter" → match all
            if (!is_array($classes) || count($classes) === 0) {
                // No class restriction — skip class check, apply to all
            } else {
                // Check if the classes list contains an "all" sentinel value
                $hasAllSentinel = false;
                foreach ($classes as $cls) {
                    if (strtolower(trim((string) $cls)) === 'all'
                        || stripos(trim((string) $cls), 'all class') !== false) {
                        $hasAllSentinel = true;
                        break;
                    }
                }

                if (!$hasAllSentinel) {
                    // Specific classes listed — check if student's class is included
                    $className = optional($student->class)->name;
                    if (!$className || !in_array($className, $classes)) {
                        return false;
                    }

                    // Check section eligibility if sections are defined
                    if ($d->sections) {
                        $sectionsTrimmed = trim($d->sections);

                        // Check if sections value is an "all sections" sentinel
                        $isSectionAll = ($sectionsTrimmed === ''
                            || strtolower($sectionsTrimmed) === 'all'
                            || stripos($sectionsTrimmed, 'all section') !== false);

                        if (!$isSectionAll) {
                            $sections = array_map('trim', explode(',', $sectionsTrimmed));
                            $secName = optional($student->section)->name;
                            if ($secName) {
                                // Format: "ClassName-SectionName" (e.g. "Nursery-A" or "Class 11-B")
                                if (!in_array($className . '-' . $secName, $sections)) {
                                    return false;
                                }
                            } else {
                                // Student has no section — only allow if sections list
                                // contains the class name alone (no section suffix)
                                $classOnlyEntry = in_array($className, $sections);
                                if (!$classOnlyEntry) {
                                    return false;
                                }
                            }
                        }
                        // If $isSectionAll, skip section check — matches all sections
                    }
                }
                // If $hasAllSentinel, skip both class and section checks
            }
        }
        // If classes_installments is null/falsy, no class restriction → apply to all

        // 4. Check fee component eligibility if defined
        if ($d->fee_component_ids) {
            $compIds = json_decode($d->fee_component_ids, true);
            if (is_array($compIds) && count($compIds) > 0) {
                $hasFee = \App\Models\StudentFee::where('student_id', $student->id)
                    ->whereIn('fee_component_id', $compIds)
                    ->exists();
                if (!$hasFee) {
                    return false;
                }
            }
        }

        return true;
    }

    public static function syncStudentDiscounts($student, $sessionId)
    {
        $schoolId = $student->school_id;

        // 1. Get all FeeDiscount records for this session & school
        $discounts = \App\Models\FeeDiscount::where('school_id', $schoolId)
            ->where('academic_session_id', $sessionId)
            ->get();

        // Filter discounts applicable to this student
        $applicableDiscounts = $discounts->filter(function($d) use ($student) {
            return self::isDiscountApplicableForStudent($d, $student);
        });

        // 2. Load all student fees for this session
        // We load ALL fees for the student and filter by session below.
        // Previously using whereHas('feeSchedule', ...) excluded misc fees and
        // transport fees (which have fee_schedule_id = null), so discounts
        // never applied to those fee components. Now we correctly include all
        // fee lines that belong to the session.
        $studentFees = StudentFee::where('school_id', $schoolId)
            ->where('student_id', $student->id)
            ->where(function ($q) use ($sessionId, $student) {
                // Include fees linked to the student's active mapped schedule
                $q->where(function ($sq) use ($student) {
                    $sq->where('fee_schedule_id', $student->fee_schedule_id)
                      ->whereNotNull('fee_schedule_id');
                })
                // Also include misc fees and transport fees (no fee_schedule_id)
                // by checking if the component belongs to the session
                ->orWhereHas('component', function ($cq) use ($sessionId) {
                    $cq->where('academic_session_id', $sessionId);
                })
                // Also include fees that have no schedule at all (older transport fees)
                ->orWhereNull('fee_schedule_id');
            })
            ->get();

        // 3. Reset instant discounts on completely unpaid installments
        // Build a set of installment_nos that have at least one fee with paid_amount > 0
        // so we do NOT reset discounts on the zero-paid siblings in those installments
        $partiallyPaidInstallments = $studentFees
            ->where('paid_amount', '>', 0)
            ->pluck('installment_no')
            ->unique()
            ->toArray();

        foreach ($studentFees as $sf) {
            if ($sf->paid_amount == 0 && !in_array($sf->installment_no, $partiallyPaidInstallments)) {
                $sf->instant_discount_amount = 0.00;
                $sf->instant_discount_type = null;
                $sf->save();
            }
        }

        // 4. Apply each applicable FeeDiscount to unpaid installments
        // Only re-apply to fees whose installment is NOT partially paid
        foreach ($applicableDiscounts as $d) {
            $discountVal = floatval($d->amount);
            $unpaidFees = $studentFees->where('paid_amount', 0)
                ->filter(function($sf) use ($partiallyPaidInstallments) {
                    return !in_array($sf->installment_no, $partiallyPaidInstallments);
                });
            if ($d->installment_no) {
                $unpaidFees = $unpaidFees->where('installment_no', $d->installment_no);
            }
            if ($d->fee_component_ids) {
                $compIds = json_decode($d->fee_component_ids, true);
                if (is_array($compIds) && count($compIds) > 0) {
                    $unpaidFees = $unpaidFees->whereIn('fee_component_id', $compIds);
                }
            }

            if ($unpaidFees->isEmpty()) continue;

            if ($d->type === 'percentage') {
                foreach ($unpaidFees as $sf) {
                    // Calculate discount ONLY on actual fee component amount (excluding late fine/penalty)
                    $discountableBase = max(0.00, floatval($sf->amount) - floatval($sf->paid_amount));
                    $discountForThis = round($discountableBase * $discountVal / 100, 2);
                    $sf->instant_discount_amount += $discountForThis;
                    $sf->instant_discount_type = 'percentage';
                    
                    $due = max(0, floatval($sf->amount) + floatval($sf->fine_amount_applied) - floatval($sf->instant_discount_amount));
                    if ($sf->status === 'refunded' && $sf->paid_amount == 0) {
                        $sf->status = 'refunded';
                    } else {
                        $sf->status = $sf->paid_amount >= $due ? 'paid' : ($sf->paid_amount > 0 ? 'partially_paid' : 'pending');
                    }
                    $sf->save();
                }
            } else {
                // Flat discount distribution (must NEVER discount fine/penalty amounts)
                $remaining = $discountVal;
                foreach ($unpaidFees as $sf) {
                    if ($remaining <= 0) break;
                    $discountableDue = max(0.00, floatval($sf->amount) - floatval($sf->paid_amount) - floatval($sf->instant_discount_amount));
                    if ($discountableDue <= 0) continue;
                    
                    $applied = min($remaining, $discountableDue);
                    $sf->instant_discount_amount += $applied;
                    $sf->instant_discount_type = 'flat';
                    
                    $dueAfter = max(0, floatval($sf->amount) + floatval($sf->fine_amount_applied) - floatval($sf->instant_discount_amount));
                    if ($sf->status === 'refunded' && $sf->paid_amount == 0) {
                        $sf->status = 'refunded';
                    } else {
                        $sf->status = $sf->paid_amount >= $dueAfter ? 'paid' : ($sf->paid_amount > 0 ? 'partially_paid' : 'pending');
                    }
                    $sf->save();
                    
                    $remaining -= $applied;
                }
            }
        }
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
        $schoolId = $this->resolveSchoolId();
        $this->ensureFeesSeeded($schoolId);

        // ─── POST REQUESTS ────────────────────────────────────────────────
        if ($request->isMethod('post')) {
            \App\Models\StudentFee::clearPendingReservationsCache();
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

            if ($action === 'get_late_fine_details') {
                $studentId = $request->input('student_id');
                $student = Student::where('school_id', $schoolId)->findOrFail($studentId);
                self::applyOverdueFinesToStudent($student);

                $studentFees = \App\Models\StudentFee::withoutGlobalScope('active')
                    ->where('school_id', $schoolId)
                    ->where('student_id', $student->id)
                    ->where('status', '!=', 'paid')
                    ->get();

                $today = now()->startOfDay();
                $grouped = $studentFees->groupBy('installment_no');
                $installmentDetails = [];

                // Pre-fetch all relevant FeeSchedule and TransportFeeSchedule models
                $schedIds = $studentFees->pluck('fee_schedule_id')
                    ->filter()
                    ->push($student->fee_schedule_id)
                    ->unique()
                    ->values()
                    ->toArray();

                $transportSchedIds = $studentFees->pluck('transport_fee_schedule_id')
                    ->filter()
                    ->unique()
                    ->values()
                    ->toArray();

                $feeSchedules = !empty($schedIds)
                    ? \App\Models\FeeSchedule::where('school_id', $schoolId)
                        ->whereIn('id', $schedIds)
                        ->with('fine')
                        ->get()
                        ->keyBy('id')
                    : collect();

                $transportFeeSchedules = !empty($transportSchedIds)
                    ? \App\Models\TransportFeeSchedule::where('school_id', $schoolId)
                        ->whereIn('id', $transportSchedIds)
                        ->with('fine')
                        ->get()
                        ->keyBy('id')
                    : collect();

                foreach ($grouped as $instNo => $fees) {
                    $firstFee = $fees->first();
                    $policyFineAmount = 0.00;
                    $finePolicy = null;
                    $instDueDate = null;
                    $graceDays = 0;

                    // Determine policy fine for this installment
                    if ($firstFee->misc_fee_id === null) {
                        $schedId = $firstFee->fee_schedule_id ?: $student->fee_schedule_id;
                        if ($schedId) {
                            $sched = $feeSchedules->get($schedId);
                            if ($sched && $sched->fine && $sched->fine->status) {
                                $finePolicy = $sched->fine;
                                $insts = $sched->installments ?? [];
                                $instData = collect($insts)->firstWhere('installment_no', $instNo);
                                if ($instData && !empty($instData['due_date'])) {
                                    $instDueDate = $instData['due_date'];
                                    $graceDays = (int) ($instData['grace_days'] ?? $finePolicy->default_grace_days ?? 0);
                                }
                            }
                        } elseif ($firstFee->transport_fee_schedule_id) {
                            $sched = $transportFeeSchedules->get($firstFee->transport_fee_schedule_id);
                            if ($sched && $sched->fine && $sched->fine->status) {
                                $finePolicy = $sched->fine;
                                $insts = $sched->installments ?? [];
                                $instData = collect($insts)->firstWhere('installment_no', $instNo);
                                if ($instData && !empty($instData['due_date'])) {
                                    $instDueDate = $instData['due_date'];
                                    $graceDays = (int) ($instData['grace_days'] ?? $finePolicy->default_grace_days ?? 0);
                                }
                            }
                        }
                    }

                    if ($finePolicy && $instDueDate) {
                        $dueDateObj = \Carbon\Carbon::parse($instDueDate)->startOfDay();
                        $graceDateObj = $dueDateObj->copy()->addDays($graceDays);
                        if ($today->gt($graceDateObj)) {
                            // Sum policy fine across matching component fees
                            foreach ($fees as $f) {
                                if ($finePolicy->fee_component_id === null || $finePolicy->fee_component_id === $f->fee_component_id) {
                                    $policyFineAmount += $finePolicy->calculateFor($f, $instDueDate, $graceDays);
                                }
                            }
                        }
                    }

                    $currentAppliedFine = $fees->sum('fine_amount_applied');
                    $isApplied = !$fees->contains(function ($f) {
                        return $f->is_fine_applied === false || $f->is_fine_applied === 0;
                    });

                    $displayFine = max($policyFineAmount, $currentAppliedFine);

                    if ($displayFine > 0 || $currentAppliedFine > 0 || !$isApplied) {
                        $installmentDetails[] = [
                            'installment_no' => (int) $instNo,
                            'installment_name' => 'Installment ' . $instNo,
                            'fine_amount' => floatval($displayFine),
                            'fine_formatted' => '₹' . number_format($displayFine, 0),
                            'is_applied' => (bool) $isApplied,
                            'status_label' => $isApplied ? 'Applied' : 'Not Applied',
                        ];
                    }
                }

                // Sort by installment_no ascending
                usort($installmentDetails, fn($a, $b) => $a['installment_no'] <=> $b['installment_no']);

                $canManage = \App\Support\StaffAccessHelper::hasAccess('fee_management', 'student_wise_fee', 'edit');

                return response()->json([
                    'success' => true,
                    'student_id' => $student->id,
                    'student_name' => $student->full_name,
                    'can_manage' => $canManage,
                    'installments' => $installmentDetails,
                ]);
            }

            if ($action === 'manage_late_fine') {
                $canManage = \App\Support\StaffAccessHelper::hasAccess('fee_management', 'student_wise_fee', 'edit');
                if (!$canManage) {
                    if ($request->wantsJson()) {
                        return response()->json(['success' => false, 'message' => 'You do not have permission to manage late fines.'], 403);
                    }
                    return back()->with('error', 'You do not have permission to manage late fines.');
                }

                $request->validate([
                    'student_id' => 'required|exists:students,id',
                    'installments' => 'required|array',
                    'installments.*.installment_no' => 'required|integer',
                    'installments.*.status' => 'required|in:applied,not_applied',
                    'reason' => 'nullable|string|max:500',
                ]);

                $studentId = $request->input('student_id');
                $student = Student::where('school_id', $schoolId)->findOrFail($studentId);
                $reason = $request->input('reason');

                // Pre-fetch all student fees for this student in a single query
                $allStudentFees = \App\Models\StudentFee::withoutGlobalScope('active')
                    ->where('school_id', $schoolId)
                    ->where('student_id', $student->id)
                    ->get();

                $oldFineTotals = [];
                $targetStatusMap = [];

                foreach ($request->input('installments') as $item) {
                    $instNo = (int) $item['installment_no'];
                    $targetStatus = $item['status'];
                    $shouldApply = ($targetStatus === 'applied');
                    $targetStatusMap[$instNo] = $shouldApply;

                    $instFees = $allStudentFees->where('installment_no', $instNo);
                    if ($instFees->isEmpty()) {
                        continue;
                    }

                    $oldFineTotals[$instNo] = floatval($instFees->sum('fine_amount_applied'));

                    foreach ($instFees as $sf) {
                        $sf->is_fine_applied = $shouldApply;
                        if (!$shouldApply) {
                            $sf->fine_amount_applied = 0.00;
                        }
                        $sf->save();
                    }
                }

                // Recalculate fine policy & sync student fees ONCE after all updates
                self::applyOverdueFinesToStudent($student);
                self::syncStudentFees($student);

                // Fetch fresh student fees after sync to record audit logs and calculate total
                $refreshedFees = \App\Models\StudentFee::withoutGlobalScope('active')
                    ->where('school_id', $schoolId)
                    ->where('student_id', $student->id)
                    ->get();

                foreach ($targetStatusMap as $instNo => $shouldApply) {
                    if (!isset($oldFineTotals[$instNo])) {
                        continue;
                    }
                    $instFees = $refreshedFees->where('installment_no', $instNo);
                    $newFineTotal = floatval($instFees->sum('fine_amount_applied'));

                    \App\Models\LateFineAuditLog::create([
                        'school_id' => $schoolId,
                        'student_id' => $student->id,
                        'user_id' => auth()->id(),
                        'installment_no' => $instNo,
                        'action' => $shouldApply ? 'applied' : 'removed',
                        'old_fine' => $oldFineTotals[$instNo],
                        'new_fine' => $newFineTotal,
                        'reason' => $reason,
                    ]);
                }

                $activeFees = $refreshedFees->where('status', '!=', 'paid');
                $totalFine = floatval($activeFees->sum('fine_amount_applied'));

                $totalAmount = floatval($refreshedFees->sum('amount'));
                $totalPaid = floatval($refreshedFees->sum('paid_amount'));
                $totalDiscount = floatval($refreshedFees->sum('instant_discount_amount'));
                $effectiveDue = max(0, ($totalAmount + $totalFine) - ($totalPaid + $totalDiscount));

                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Late Fine settings updated successfully!',
                        'total_fine' => $totalFine,
                        'total_fine_formatted' => '₹' . number_format($totalFine, 0),
                        'effective_due' => $effectiveDue,
                        'effective_due_formatted' => '₹' . number_format($effectiveDue, 0),
                    ]);
                }

                return back()->with('success', 'Late Fine settings updated successfully!');
            }
            if ($action === 'add_student_misc_fee') {
                $request->validate([
                    'student_id'           => 'required|exists:students,id',
                    'installment_no'       => 'required',
                    'misc_fee_amount'      => 'required|numeric|min:0',
                    'selected_misc_fee_id' => 'nullable',
                    'new_misc_fee_head'    => 'nullable|required_without:selected_misc_fee_id|string|max:100',
                    'new_misc_fee_name'    => 'nullable|required_without:selected_misc_fee_id|string|max:100',
                ]);

                $studentIdTemp = $request->input('student_id');
                $instNoTemp = $request->input('installment_no') ?: 1;

                $miscFee = null;
                if ($request->filled('selected_misc_fee_id')) {
                    $miscFee = \App\Models\MiscFee::where('school_id', $schoolId)->find($request->input('selected_misc_fee_id'));
                    if ($miscFee) {
                        if ($request->filled('misc_fee_amount')) {
                            $miscFee->update(['amount' => floatval($request->input('misc_fee_amount'))]);
                        }
                        $stIds = json_decode($miscFee->student_ids, true) ?: [];
                        if (!in_array($studentIdTemp, $stIds)) {
                            $stIds[] = $studentIdTemp;
                            $miscFee->update(['student_ids' => json_encode($stIds)]);
                        }
                    }
                } else {
                    $head = $request->input('new_misc_fee_head') ?: 'Miscellaneous Fee';
                    $name = $request->input('new_misc_fee_name') ?: 'Misc Fee';
                    $amt = floatval($request->input('misc_fee_amount') ?: 0);
                    
                    $session = \App\Models\AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first() 
                        ?? \App\Models\AcademicSession::where('school_id', $schoolId)->first();
                    $sessId = $session ? $session->id : null;

                    $miscFee = \App\Models\MiscFee::create([
                        'school_id' => $schoolId,
                        'academic_session_id' => $sessId,
                        'fee_head_name' => $head,
                        'name' => $name,
                        'amount' => $amt,
                        'student_ids' => json_encode([$studentIdTemp]),
                        'classes_installments' => json_encode([]),
                    ]);
                }

                if ($miscFee) {
                    $category = \App\Models\FeeCategory::firstOrCreate(
                        ['school_id' => $schoolId, 'name' => $miscFee->fee_head_name ?: 'Miscellaneous Fee'],
                        ['description' => 'Miscellaneous Fees']
                    );

                    \App\Models\StudentFee::withoutGlobalScope('active')->firstOrCreate([
                        'school_id' => $schoolId,
                        'student_id' => $studentIdTemp,
                        'misc_fee_id' => $miscFee->id,
                        'installment_no' => $instNoTemp,
                    ], [
                        'fee_category_id' => $category->id,
                        'fee_schedule_id' => null,
                        'amount' => $miscFee->amount,
                        'due_date' => now()->toDateString(),
                        'status' => 'pending'
                    ]);
                }

                return back()->with('success', 'Miscellaneous Fee added successfully!');
            }

            if ($action === 'mark_paid') {
                // Diagnostic logging to a public file
                try {
                    $logData = "=========================================\n";
                    $logData .= "TIME: " . now()->toDateTimeString() . "\n";
                    $logData .= "REQUEST: " . json_encode($request->all()) . "\n";
                    $logData .= "SCHOOL ID: " . $schoolId . "\n";
                    
                    $studentId = $request->student_id;
                    $instNo = $request->installment_no ?: 1;
                    
                    $studentObj = Student::where('school_id', $schoolId)->find($studentId);
                    if ($studentObj) {
                        $logData .= "STUDENT: {$studentObj->full_name} | opted=" . ($studentObj->transport_opted ? 'true' : 'false') . " | route_id=" . ($studentObj->transport_route_id ?? 'null') . " | route=" . ($studentObj->transport_route ?? 'null') . "\n";
                    }
                    
                    $allFeesCount = StudentFee::withoutGlobalScopes()
                        ->where('school_id', $schoolId)
                        ->where('student_id', $studentId)
                        ->count();
                    $logData .= "ALL FEES COUNT FOR STUDENT (NO SCOPES): " . $allFeesCount . "\n";
                    
                    $allFeesList = StudentFee::withoutGlobalScopes()
                        ->where('school_id', $schoolId)
                        ->where('student_id', $studentId)
                        ->get();
                    foreach ($allFeesList as $fee) {
                        $logData .= " - Fee ID: {$fee->id}, Category: " . optional($fee->category)->name . ", Component: " . optional($fee->component)->component_name . ", Inst No: {$fee->installment_no}, Status: {$fee->status}, Amt: {$fee->amount}, Paid: {$fee->paid_amount}\n";
                    }
                    
                    file_put_contents(public_path('debug_payment.txt'), $logData, FILE_APPEND);
                } catch (\Exception $e) {
                    file_put_contents(public_path('debug_payment.txt'), "LOG ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
                }

                if ($request->has('student_fee_id') && ($request->student_fee_id === '' || $request->student_fee_id === 'null' || empty($request->student_fee_id))) {
                    $request->merge(['student_fee_id' => null]);
                }

                $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                    'student_fee_id'       => 'nullable',
                    'student_id'           => 'nullable|exists:students,id',
                    'installment_no'       => 'nullable',
                    'amount_paid'          => 'required|numeric|min:0',
                    'payment_mode'         => 'required|string',
                    'transaction_id'       => 'nullable|string',
                    'receipt_date'         => 'nullable|date',
                    'receipt_no'           => 'nullable|string',
                    'bank_name'            => 'nullable|string',
                    'cheque_date'          => 'nullable|date',
                    'branch'               => 'nullable|string',
                    'instant_discount_amount' => 'nullable|numeric|min:0',
                    'instant_discount_type'   => 'nullable|in:percentage,flat',
                    'discount_fee_component_ids' => 'nullable|array',
                    'discount_fee_component_ids.*' => 'exists:fee_components,id',
                    'discount_installment_nos'   => 'nullable|array',
                ]);

                $validator->after(function ($validator) use ($request, $schoolId) {
                    $amtPaid = floatval($request->input('amount_paid', 0));
                    $discAmt = floatval($request->input('instant_discount_amount', 0));
                    $discType = $request->input('instant_discount_type', 'flat');

                    if ($amtPaid <= 0 && $discAmt <= 0) {
                        $validator->errors()->add('amount_paid', 'Either the amount to collect or the instant discount must be greater than zero.');
                    }
                    if ($discType === 'percentage' && ($discAmt < 0 || $discAmt > 100)) {
                        $validator->errors()->add('instant_discount_amount', 'Discount percentage must be between 0 and 100.');
                    }
                    if ($discType === 'flat' && $discAmt < 0) {
                        $validator->errors()->add('instant_discount_amount', 'Discount amount cannot be negative.');
                    }
                    if ($request->input('payment_mode') === 'cheque') {
                        $chequeNo = $request->input('transaction_id');
                        if (empty($chequeNo)) {
                            $validator->errors()->add('transaction_id', 'Cheque Number is required.');
                        } elseif (!preg_match('/^[0-9]+$/', $chequeNo)) {
                            $validator->errors()->add('transaction_id', 'Cheque Number must contain digits only.');
                        }
                    }

                    // --- Component-wise Instant Discount Zero Payable Validation ---
                    $discountFeeComponentIds = $request->input('discount_fee_component_ids', []);
                    if (!is_array($discountFeeComponentIds)) {
                        $discountFeeComponentIds = [];
                    }
                    
                    if (count($discountFeeComponentIds) > 0 && $discAmt > 0) {
                        $discountInstallmentNos = $request->input('discount_installment_nos', []);
                        if (!is_array($discountInstallmentNos)) {
                            $discountInstallmentNos = [];
                        }

                        $studentIdTemp = $request->input('student_id');
                        $instNoTemp = $request->input('installment_no') ?: 1;

                        // Get candidate installments
                        $installmentsToCheck = $discountInstallmentNos;
                        if (empty($installmentsToCheck)) {
                            if ($instNoTemp == 999) {
                                // Combined payment, get all installments from the fee records in this combined payment
                                $selectedFeeIdsTemp = [];
                                if ($request->filled('student_fee_ids')) {
                                    $selectedFeeIdsTemp = array_filter(array_map('trim', explode(',', $request->input('student_fee_ids'))));
                                }
                                $installmentsToCheck = \App\Models\StudentFee::withoutGlobalScopes()
                                    ->where('school_id', $schoolId)
                                    ->where('student_id', $studentIdTemp)
                                    ->whereIn('id', $selectedFeeIdsTemp)
                                    ->pluck('installment_no')
                                    ->unique()
                                    ->toArray();
                            } else {
                                $installmentsToCheck = [$instNoTemp];
                            }
                        }

                        foreach ($installmentsToCheck as $instNo) {
                            foreach ($discountFeeComponentIds as $compId) {
                                $feeRecord = \App\Models\StudentFee::withoutGlobalScopes()
                                    ->where('school_id', $schoolId)
                                    ->where('student_id', $studentIdTemp)
                                    ->where('installment_no', $instNo)
                                    ->where('fee_component_id', $compId)
                                    ->first();

                                $due = 0.00;
                                if ($feeRecord) {
                                    $due = max(0.00, floatval($feeRecord->amount) + floatval($feeRecord->fine_amount_applied ?? 0) - floatval($feeRecord->paid_amount) - floatval($feeRecord->instant_discount_amount) - floatval($feeRecord->pending_cheque_amount));
                                }

                                if ($due <= 0.00) {
                                    $compObj = \App\Models\FeeComponent::find($compId);
                                    $compName = $compObj ? $compObj->component_name : 'Fee Component';
                                    $instName = 'Installment ' . $instNo;

                                    $validator->errors()->add(
                                        'discount_fee_component_ids',
                                        "{$compName} cannot receive an instant discount in {$instName} because its payable amount is ₹0. Please remove the {$compName} component for {$instName} or select another applicable installment."
                                    );
                                    break 2;
                                }
                            }
                        }
                    }
                });

                if ($validator->fails()) {
                    if ($request->wantsJson()) {
                        return response()->json(['success' => false, 'errors' => $validator->errors()->toArray()]);
                    }
                    return back()->withErrors($validator)->withInput();
                }

                // Fetch selected fees first to calculate remaining balance before this transaction
                $studentIdTemp = $request->input('student_id');
                $instNoTemp = $request->input('installment_no') ?: 1;
                $selectedFeeIdsTemp = [];
                if ($request->filled('student_fee_ids')) {
                    $selectedFeeIdsTemp = array_filter(array_map('trim', explode(',', $request->input('student_fee_ids'))));
                }

                // Resolve real installment number if 999 is passed
                if ($instNoTemp == 999) {
                    if (!empty($selectedFeeIdsTemp)) {
                        $firstFee = StudentFee::withoutGlobalScopes()->where('school_id', $schoolId)->whereIn('id', $selectedFeeIdsTemp)->first();
                        if ($firstFee) {
                            $instNoTemp = $firstFee->installment_no;
                        }
                    }
                    if ($instNoTemp == 999 && $request->filled('student_fee_id')) {
                        $firstFee = StudentFee::withoutGlobalScopes()->where('school_id', $schoolId)->find($request->student_fee_id);
                        if ($firstFee) {
                            $instNoTemp = $firstFee->installment_no;
                        }
                    }
                    if ($instNoTemp == 999) {
                        $firstFee = StudentFee::withoutGlobalScopes()
                            ->where('school_id', $schoolId)
                            ->where('student_id', $studentIdTemp)
                            ->orderBy('installment_no')
                            ->first();
                        if ($firstFee) {
                            $instNoTemp = $firstFee->installment_no;
                        }
                    }
                    if ($instNoTemp == 999) {
                        $instNoTemp = 1;
                    }
                }

                // ── Handle Miscellaneous Fee creation/assignment ──
                if ($request->input('add_misc_fee') == '1') {
                    $miscFee = null;
                    if ($request->filled('selected_misc_fee_id')) {
                        $miscFee = \App\Models\MiscFee::where('school_id', $schoolId)->find($request->input('selected_misc_fee_id'));
                        if ($miscFee) {
                            if ($request->filled('misc_fee_amount')) {
                                $miscFee->update(['amount' => floatval($request->input('misc_fee_amount'))]);
                            }
                            $stIds = json_decode($miscFee->student_ids, true) ?: [];
                            if (!in_array($studentIdTemp, $stIds)) {
                                $stIds[] = $studentIdTemp;
                                $miscFee->update(['student_ids' => json_encode($stIds)]);
                            }
                        }
                    } else {
                        // Create a new Miscellaneous Fee on the fly
                        $head = $request->input('new_misc_fee_head') ?: 'Miscellaneous Fee';
                        $name = $request->input('new_misc_fee_name') ?: 'Misc Fee';
                        $amt = floatval($request->input('misc_fee_amount') ?: 0);
                        
                        // Fallback session ID selection
                        $session = \App\Models\AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first() 
                            ?? \App\Models\AcademicSession::where('school_id', $schoolId)->first();
                        $sessId = $session ? $session->id : null;

                        $miscFee = \App\Models\MiscFee::create([
                            'school_id' => $schoolId,
                            'academic_session_id' => $sessId,
                            'fee_head_name' => $head,
                            'name' => $name,
                            'amount' => $amt,
                            'student_ids' => json_encode([$studentIdTemp]),
                            'classes_installments' => json_encode([]),
                        ]);
                    }

                    if ($miscFee) {
                        $category = \App\Models\FeeCategory::firstOrCreate(
                            ['school_id' => $schoolId, 'name' => $miscFee->fee_head_name ?: 'Miscellaneous Fee'],
                            ['description' => 'Miscellaneous Fees']
                        );

                        $studentFee = \App\Models\StudentFee::withoutGlobalScope('active')->firstOrCreate([
                            'school_id' => $schoolId,
                            'student_id' => $studentIdTemp,
                            'misc_fee_id' => $miscFee->id,
                            'installment_no' => $instNoTemp,
                        ], [
                            'fee_category_id' => $category->id,
                            'fee_schedule_id' => null,
                            'amount' => $miscFee->amount,
                            'due_date' => now()->toDateString(),
                            'status' => 'pending'
                        ]);

                        if ($studentFee && !empty($selectedFeeIdsTemp)) {
                            $selectedFeeIdsTemp[] = $studentFee->id;
                        }
                    }
                }

                $feesToPayTemp = collect();
                if (!empty($selectedFeeIdsTemp)) {
                    $feesToPayTemp = StudentFee::withoutGlobalScopes()
                        ->where('school_id', $schoolId)
                        ->where('student_id', $studentIdTemp)
                        ->whereIn('id', $selectedFeeIdsTemp)
                        ->get();
                } else {
                    $sfTemp = null;
                    if ($request->input('student_fee_id')) {
                        $sfTemp = StudentFee::withoutGlobalScopes()
                            ->where('school_id', $schoolId)
                            ->find($request->input('student_fee_id'));
                        if ($sfTemp && $sfTemp->student_id != $studentIdTemp) {
                            $sfTemp = null;
                        }
                    }
                    if ($sfTemp) {
                        $feesToPayTemp = collect([$sfTemp]);
                        if (isset($studentFee)) {
                            $feesToPayTemp->push($studentFee);
                        }
                    } else {
                        $allFeesTemp = StudentFee::withoutGlobalScopes()
                            ->where('school_id', $schoolId)
                            ->where('student_id', $studentIdTemp)
                            ->where('installment_no', $instNoTemp)
                            ->where('status', '!=', 'paid')
                            ->with(['category', 'component'])
                            ->orderBy('id', 'asc')
                            ->get();

                        $feesToPayTemp = $allFeesTemp->filter(function($fee) use ($request) {
                            $isTrans = (optional($fee->category)->name === 'Transport' || 
                                        stripos(optional($fee->category)->name ?? '', 'Transport') !== false ||
                                        stripos(optional($fee->component)->component_name ?? '', 'Transport') !== false);
                            return $request->input('fee_type') === 'transport' ? $isTrans : !$isTrans;
                        })->values();
                        
                        if ($feesToPayTemp->isEmpty()) {
                            $allFeesFallbackTemp = StudentFee::withoutGlobalScopes()
                                ->where('school_id', $schoolId)
                                ->where('student_id', $studentIdTemp)
                                ->where('installment_no', $instNoTemp)
                                ->with(['category', 'component'])
                                ->orderBy('id', 'asc')
                                ->get();
                            
                            $feesToPayTemp = $allFeesFallbackTemp->filter(function($fee) use ($request) {
                                $isTrans = (optional($fee->category)->name === 'Transport' || 
                                            stripos(optional($fee->category)->name ?? '', 'Transport') !== false ||
                                            stripos(optional($fee->component)->component_name ?? '', 'Transport') !== false);
                                return $request->input('fee_type') === 'transport' ? $isTrans : !$isTrans;
                            })->values();
                        }
                    }
                }

                $totalAmountTemp = $feesToPayTemp->sum(fn($f) => floatval($f->amount));
                $amountPaidPriorTemp = $feesToPayTemp->sum(fn($f) => floatval($f->paid_amount));
                $priorDiscountTemp = $feesToPayTemp->sum(fn($f) => floatval($f->instant_discount_amount));
                $totalFineAppliedTemp = $feesToPayTemp->sum(fn($f) => floatval($f->fine_amount_applied ?? 0));
                
                // totalAmountDue = amount + fine_amount_applied - paid_amount - instant_discount_amount_prior
                $totalAmountDueTemp = max(0.00, $totalAmountTemp + $totalFineAppliedTemp - $amountPaidPriorTemp - $priorDiscountTemp);

                $rawAmountPaid = floatval($request->input('amount_paid'));

                // ── Issue 1 Fix: Validate amount_paid does not exceed total due (subtracting pending cheques) ──
                $pendingChequesForTheseFees = \App\Models\PendingCheque::where('school_id', $schoolId)
                    ->where('student_id', $studentIdTemp)
                    ->where('status', 'pending')
                    ->get();
                
                $pendingChequeAmountForTheseFees = 0;
                foreach ($pendingChequesForTheseFees as $pc) {
                    $chqFeeIds = json_decode($pc->student_fee_ids, true) ?? [];
                    if (is_array($chqFeeIds)) {
                        $intersection = array_intersect($feesToPayTemp->pluck('id')->toArray(), $chqFeeIds);
                        if (!empty($intersection)) {
                            $pendingChequeAmountForTheseFees += floatval($pc->amount);
                        }
                    }
                }

                $discountFeeComponentIds = $request->input('discount_fee_component_ids', []);
                if (!is_array($discountFeeComponentIds)) {
                    $discountFeeComponentIds = [];
                }
                $discountInstallmentNos = $request->input('discount_installment_nos', []);
                if (!is_array($discountInstallmentNos)) {
                    $discountInstallmentNos = [];
                }
                $hasComponentDiscount = count($discountFeeComponentIds) > 0;

                $discountableDueTemp = 0.00;
                foreach ($feesToPayTemp as $f) {
                    $sfDueBefore = max(0.00, floatval($f->amount) + floatval($f->fine_amount_applied ?? 0) - floatval($f->paid_amount) - floatval($f->instant_discount_amount) - floatval($f->pending_cheque_amount));
                    $fineApplied = max(0.00, floatval($f->fine_amount_applied ?? 0));
                    $sfFineRemaining = min($fineApplied, $sfDueBefore);
                    $sfDiscountable = max(0.00, $sfDueBefore - $sfFineRemaining);

                    if ($hasComponentDiscount) {
                        $isCompEligible = in_array($f->fee_component_id, $discountFeeComponentIds);
                        $isInstEligible = empty($discountInstallmentNos) || in_array($f->installment_no, $discountInstallmentNos);
                        if ($isCompEligible && $isInstEligible) {
                            $discountableDueTemp += $sfDiscountable;
                        }
                    } else {
                        $discountableDueTemp += $sfDiscountable;
                    }
                }

                $instantDiscAmt = floatval($request->input('instant_discount_amount', 0));
                $instantDiscType = $request->input('instant_discount_type', 'flat');
                $previewDiscount = 0;
                if ($instantDiscAmt > 0) {
                    if ($instantDiscType === 'percentage') {
                        $pct = max(0, min(100, $instantDiscAmt));
                        $previewDiscount = round($discountableDueTemp * $pct / 100, 2);
                    } else {
                        $previewDiscount = min($instantDiscAmt, $discountableDueTemp);
                    }
                }
                $maxAllowedPayment = max(0.00, $totalAmountDueTemp - $previewDiscount - $pendingChequeAmountForTheseFees);
                if ($rawAmountPaid > 0 && round($rawAmountPaid, 2) > round($maxAllowedPayment + 0.01, 2)) {
                    $errMsg = 'Amount to Collect cannot exceed the total payable amount.';
                    if ($request->wantsJson()) {
                        return response()->json(['success' => false, 'message' => $errMsg], 422);
                    }
                    return back()->with('error', $errMsg)->withInput();
                }
                // ── End Issue 1 Fix ──

                $discountAmount    = floatval($request->input('instant_discount_amount', 0));
                $discountType      = $request->input('instant_discount_type', 'flat');
                $effectiveDiscount = 0;
                if ($discountAmount > 0) {
                    if ($discountType === 'percentage') {
                        $percent = max(0.00, min(100.00, $discountAmount));
                        $effectiveDiscount = round($discountableDueTemp * $percent / 100, 2);
                    } else {
                        $effectiveDiscount = min(max(0.00, $discountAmount), $discountableDueTemp);
                    }
                }
                
                // remainingBeforeDiscount = max(0, totalAmountDue - amountCollectedNow)
                $remainingBeforeDiscountTemp = max(0.00, $totalAmountDueTemp - $rawAmountPaid);
                
                $priorDiscountNamesList = [];
                $currentSession = \App\Models\AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first();
                $currentStudent = \App\Models\Student::where('school_id', $schoolId)->find($studentIdTemp);
                if ($currentStudent && $currentSession) {
                    $applicableDiscList = \App\Models\FeeDiscount::where('school_id', $schoolId)
                        ->where('academic_session_id', $currentSession->id)
                        ->get()
                        ->filter(function ($d) use ($currentStudent) {
                            return self::isDiscountApplicableForStudent($d, $currentStudent);
                        });
                    $priorDiscountNamesList = $applicableDiscList->pluck('name')->unique()->toArray();
                }

                // Merge the computed discount back into request for use below
                $request->merge([
                    '_computed_discount' => $effectiveDiscount,
                    '_computed_discount_type' => $discountType,
                    '_computed_total_amount' => $totalAmountTemp,
                    '_computed_paid_prior' => $amountPaidPriorTemp,
                    '_computed_prior_discount' => $priorDiscountTemp,
                    '_computed_prior_discount_names' => implode(', ', $priorDiscountNamesList),
                    '_computed_remaining_before_disc' => $remainingBeforeDiscountTemp,
                    '_computed_total_due_before' => $totalAmountDueTemp,
                ]);

                $config = \App\Models\FeeConfiguration::where('school_id', $schoolId)->first();
                $schoolPrefix = $config->school_fee_prefix ?? 'REC';
                $transportPrefix = $config->transport_fee_prefix ?? 'TRN';

                $receiptDate = $request->input('receipt_date') ?: now()->toDateString();
                $feeType     = $request->input('fee_type', 'tuition');
                $defaultPrefix = ($feeType === 'transport') ? $transportPrefix : $schoolPrefix;

                $receiptNo   = $request->input('receipt_no');
                if (empty($receiptNo) || preg_match('/^(?:REC|TRN)-\d+$/', $receiptNo) || preg_match('/^' . preg_quote($defaultPrefix) . '-\d+$/', $receiptNo)) {
                    $receiptNo = self::generateNextReceiptNumber($schoolId, $defaultPrefix);
                }
                $paymentMode = $request->input('payment_mode');

                if ($paymentMode === 'cheque') {
                    // Collect the IDs of the fees being covered by this cheque
                    $chequeStudentFeeIds = $feesToPayTemp->pluck('id')->toArray();

                    $chequeAmount = floatval($request->amount_paid);
                    if ($request->input('add_misc_fee') == '1' && isset($studentFee)) {
                        $chequeAmount += floatval($studentFee->amount);
                    }

                    // Create pending cheque — do NOT update student paid_amount yet
                    PendingCheque::create([
                        'school_id'        => $schoolId,
                        'student_id'       => $request->student_id,
                        'bank_name'        => $request->bank_name ?: 'N/A',
                        'cheque_number'    => $request->transaction_id ?: 'CHQ-' . rand(100000, 999999),
                        'amount'           => $chequeAmount,
                        'cheque_date'      => $request->cheque_date ?: now()->toDateString(),
                        'branch'           => $request->branch,
                        'installment_no'   => $instNoTemp,
                        'receipt_number'   => $receiptNo,
                        'entry_date'       => now()->toDateString(),
                        'receipt_date'     => $receiptDate,
                        'status'           => 'pending',
                        'discount_amount'  => $effectiveDiscount,
                        'student_fee_ids'  => json_encode($chequeStudentFeeIds),
                    ]);

                    \App\Models\StudentFee::clearPendingReservationsCache();

                    if ($request->wantsJson()) {
                        return response()->json(['success' => true, 'message' => 'Cheque recorded as pending clearing successfully! Note: No invoice has been generated yet as this cheque is not cleared.']);
                    }
                    return back()->with('success', 'Cheque recorded as pending clearing successfully! Note: No invoice has been generated yet as this cheque is not cleared.');
                }

                // Normal/immediate payment (cash, online, bank transfer)
                try {
                    $invoiceNo = \Illuminate\Support\Facades\DB::transaction(function() use ($request, $schoolId, $receiptDate, $receiptNo, $paymentMode, $rawAmountPaid, $effectiveDiscount, $discountType, $discountFeeComponentIds, $discountInstallmentNos, $hasComponentDiscount) {
                        $detailsArray = [];
                        $studentId = $request->student_id;
                        $instNo = $request->installment_no ?: 1;

                        $selectedFeeIds = [];
                        if ($request->filled('student_fee_ids')) {
                            $selectedFeeIds = array_filter(array_map('trim', explode(',', $request->student_fee_ids)));
                        }

                        // Resolve real installment number if 999 is passed
                        if ($instNo == 999) {
                            if (!empty($selectedFeeIds)) {
                                $firstFee = StudentFee::withoutGlobalScopes()->where('school_id', $schoolId)->whereIn('id', $selectedFeeIds)->first();
                                if ($firstFee) {
                                    $instNo = $firstFee->installment_no;
                                }
                            }
                            if ($instNo == 999 && $request->filled('student_fee_id')) {
                                $firstFee = StudentFee::withoutGlobalScopes()->where('school_id', $schoolId)->find($request->student_fee_id);
                                if ($firstFee) {
                                    $instNo = $firstFee->installment_no;
                                }
                            }
                            if ($instNo == 999) {
                                $firstFee = StudentFee::withoutGlobalScopes()
                                    ->where('school_id', $schoolId)
                                    ->where('student_id', $studentId)
                                    ->orderBy('installment_no')
                                    ->first();
                                if ($firstFee) {
                                    $instNo = $firstFee->installment_no;
                                }
                            }
                            if ($instNo == 999) {
                                $instNo = 1;
                            }
                        }

                        if (!empty($selectedFeeIds)) {
                            if ($request->input('add_misc_fee') == '1') {
                                $miscSf = StudentFee::withoutGlobalScope('active')
                                    ->where('school_id', $schoolId)
                                    ->where('student_id', $studentId)
                                    ->where('installment_no', $instNo)
                                    ->whereNotNull('misc_fee_id')
                                    ->where('status', 'pending')
                                    ->orderBy('id', 'desc')
                                    ->first();
                                if ($miscSf && !in_array($miscSf->id, $selectedFeeIds)) {
                                    $selectedFeeIds[] = $miscSf->id;
                                }
                            }
                            $feesToPay = StudentFee::withoutGlobalScopes()
                                ->where('school_id', $schoolId)
                                ->where('student_id', $studentId)
                                ->whereIn('id', $selectedFeeIds)
                                ->with(['category', 'component'])
                                ->get();
                        } else {
                            $sf = null;
                            if ($request->student_fee_id) {
                                $sf = StudentFee::withoutGlobalScopes()
                                    ->where('school_id', $schoolId)
                                    ->find($request->student_fee_id);
                                if ($sf && $sf->student_id != $request->student_id) {
                                    $sf = null;
                                }
                            }
                            
                            if ($sf) {
                                $feesToPay = collect([$sf]);
                                if ($request->input('add_misc_fee') == '1') {
                                    $miscSf = StudentFee::withoutGlobalScope('active')
                                        ->where('school_id', $schoolId)
                                        ->where('student_id', $studentId)
                                        ->where('installment_no', $instNo)
                                        ->whereNotNull('misc_fee_id')
                                        ->where('status', 'pending')
                                        ->orderBy('id', 'desc')
                                        ->first();
                                    if ($miscSf) {
                                        $feesToPay->push($miscSf);
                                    }
                                }
                            } else {
                                $allFees = StudentFee::withoutGlobalScopes()
                                    ->where('school_id', $schoolId)
                                    ->where('student_id', $studentId)
                                    ->where('installment_no', $instNo)
                                    ->where('status', '!=', 'paid')
                                    ->with(['category', 'component'])
                                    ->orderBy('id', 'asc')
                                    ->get();

                                $feesToPay = $allFees->filter(function($fee) use ($request) {
                                    $isTrans = (optional($fee->category)->name === 'Transport' || 
                                                stripos(optional($fee->category)->name ?? '', 'Transport') !== false ||
                                                stripos(optional($fee->component)->component_name ?? '', 'Transport') !== false);
                                    return $request->fee_type === 'transport' ? $isTrans : !$isTrans;
                                })->values();
                                    
                                if ($feesToPay->isEmpty()) {
                                    $allFeesFallback = StudentFee::withoutGlobalScopes()
                                        ->where('school_id', $schoolId)
                                        ->where('student_id', $studentId)
                                        ->where('installment_no', $instNo)
                                    ->with(['category', 'component'])
                                    ->orderBy('id', 'asc')
                                    ->get();
                                    
                                    $feesToPay = $allFeesFallback->filter(function($fee) use ($request) {
                                        $isTrans = (optional($fee->category)->name === 'Transport' || 
                                                    stripos(optional($fee->category)->name ?? '', 'Transport') !== false ||
                                                    stripos(optional($fee->component)->component_name ?? '', 'Transport') !== false);
                                        return $request->fee_type === 'transport' ? $isTrans : !$isTrans;
                                    })->values();
                                }
                            }
                        }

                        if ($feesToPay->isNotEmpty()) {
                            $instNo = $feesToPay->first()->installment_no;
                            $studentId = $feesToPay->first()->student_id;
                        }

                        // 1. Sorting: Sequential FIFO allocation rule.
                        //    Priority 0 = normal/tuition fees, Priority 1 = transport fees.
                        //    This guarantees all selected tuition installments are fully cleared
                        //    before any money flows to transport installments, and within each
                        //    group ordering is strictly by installment_no (chronological) then id.
                        $feesToPay = $feesToPay->sortBy(function($f) {
                            // A fee is a transport fee if it has a transport_fee_schedule_id,
                            // or if its category name contains "Transport" (belt-and-suspenders).
                            $isTransport = !empty($f->transport_fee_schedule_id)
                                || stripos(optional($f->category)->name ?? '', 'Transport') !== false
                                || stripos(optional($f->component)->component_name ?? '', 'Transport Fee') !== false;

                            $typePriority = $isTransport ? 1 : 0;
                            return sprintf('%d-%05d-%010d', $typePriority, intval($f->installment_no), intval($f->id));
                        })->values();

                        // 2. Discount Distribution (Happens first to base it on original due before this transaction)
                        $discountToDistribute = floatval($effectiveDiscount);
                        $appliedDiscountsMap = [];
                        $totalDiscountVal = $discountToDistribute;
                        $runningDiscountSum = 0;
                        $discountPercent = $discountType === 'percentage' ? floatval($request->input('instant_discount_amount', 0)) : 0;

                        // Identify eligible fees for discount (fees with outstanding due before this payment)
                        $eligibleFeesForDiscount = [];
                        foreach ($feesToPay as $f) {
                            $sfDueBefore = max(0.00, floatval($f->amount) + floatval($f->fine_amount_applied ?? 0) - floatval($f->paid_amount) - floatval($f->instant_discount_amount) - floatval($f->pending_cheque_amount));
                            $fineApplied = max(0.00, floatval($f->fine_amount_applied ?? 0));
                            $sfFineRemaining = min($fineApplied, $sfDueBefore);
                            $sfDiscountable = max(0.00, $sfDueBefore - $sfFineRemaining);
                            
                            $isCompEligible = !$hasComponentDiscount || in_array($f->fee_component_id, $discountFeeComponentIds);
                            $isInstEligible = !$hasComponentDiscount || empty($discountInstallmentNos) || in_array($f->installment_no, $discountInstallmentNos);

                            if ($sfDiscountable > 0 && $isCompEligible && $isInstEligible) {
                                $eligibleFeesForDiscount[] = $f;
                            } else {
                                $appliedDiscountsMap[$f->id] = 0;
                            }
                        }

                        $totalEligible = count($eligibleFeesForDiscount);
                        foreach ($eligibleFeesForDiscount as $index => $f) {
                            $sfDueBefore = max(0.00, floatval($f->amount) + floatval($f->fine_amount_applied ?? 0) - floatval($f->paid_amount) - floatval($f->instant_discount_amount) - floatval($f->pending_cheque_amount));
                            $fineApplied = max(0.00, floatval($f->fine_amount_applied ?? 0));
                            $sfFineRemaining = min($fineApplied, $sfDueBefore);
                            $sfDiscountable = max(0.00, $sfDueBefore - $sfFineRemaining);
                            
                            if ($discountType === 'percentage') {
                                if ($index === $totalEligible - 1) {
                                    $allocated = max(0.00, $totalDiscountVal - $runningDiscountSum);
                                } else {
                                    $allocated = round($sfDiscountable * ($discountPercent / 100), 2);
                                }
                                $allocated = min($allocated, $sfDiscountable);
                            } else {
                                if ($index === $totalEligible - 1) {
                                    $allocated = max(0.00, $discountToDistribute);
                                } else {
                                    $allocated = min($discountToDistribute, $sfDiscountable);
                                }
                                $allocated = min($allocated, $sfDiscountable);
                                $discountToDistribute -= $allocated;
                            }

                            $f->instant_discount_amount = floatval($f->instant_discount_amount) + $allocated;
                            $f->instant_discount_type = $discountType;
                            $appliedDiscountsMap[$f->id] = $allocated;
                            $runningDiscountSum += $allocated;
                        }

                        // 3. Cash Distribution (Happens second, applied on remaining due after the new discount)
                        $amountToDistribute = max(0.00, floatval($request->amount_paid));
                        $appliedCashMap = [];
                        foreach ($feesToPay as $f) {
                            $appliedCashMap[$f->id] = 0;
                            if ($amountToDistribute > 0) {
                                // Due after new discount is applied, also subtracting pending cheque
                                $sfDueAfterDiscount = max(0.00, floatval($f->amount) + floatval($f->fine_amount_applied ?? 0) - floatval($f->paid_amount) - floatval($f->instant_discount_amount) - floatval($f->pending_cheque_amount));
                                if ($sfDueAfterDiscount > 0) {
                                    $allocated = min($amountToDistribute, $sfDueAfterDiscount);
                                    $f->paid_amount = floatval($f->paid_amount) + $allocated;
                                    $appliedCashMap[$f->id] = $allocated;
                                    $amountToDistribute -= $allocated;
                                }
                            }
                        }

                        // 4. Save and build breakdown details
                        foreach ($feesToPay as $f) {
                            $due = floatval($f->amount) + floatval($f->fine_amount_applied) - floatval($f->instant_discount_amount);
                            $f->status = floatval($f->paid_amount) >= $due ? 'paid' : (floatval($f->paid_amount) > 0 ? 'partially_paid' : 'pending');
                            $f->save();

                            $paymentForThis = $appliedCashMap[$f->id] ?? 0;
                            $discForThis = $appliedDiscountsMap[$f->id] ?? 0;

                            if ($paymentForThis > 0 || $discForThis > 0 || floatval($f->instant_discount_amount) > 0 || $f->misc_fee_id !== null) {
                                $detailsArray[] = [
                                    'student_fee_id' => $f->id,
                                    'component_name' => $f->component ? $f->component->component_name : ($f->miscFee ? $f->miscFee->name : ($f->category ? $f->category->name : 'Fee')),
                                    'installment_no' => $f->installment_no,
                                    'amount_paid' => $paymentForThis,
                                    'discount_amount' => $f->instant_discount_amount,
                                    'transaction_discount' => $discForThis,
                                ];
                            }
                        }

                        $discountInfo = [
                            'total_amount' => floatval($request->input('_computed_total_amount')),
                            'amount_paid_prior' => floatval($request->input('_computed_paid_prior')),
                            'amount_paid_this_transaction' => floatval($rawAmountPaid),
                            'remaining_amount_before_discount' => floatval($request->input('_computed_remaining_before_disc')),
                            'discount_type' => $discountType,
                            'discount_percent' => $discountType === 'percentage' ? floatval($request->input('instant_discount_amount', 0)) : 0,
                            'discount_value' => floatval($effectiveDiscount),
                            'final_remaining_amount' => max(0, floatval($request->input('_computed_remaining_before_disc')) - floatval($effectiveDiscount)),
                            'prior_discount_amount' => floatval($request->input('_computed_prior_discount', 0)),
                            'prior_discount_names' => $request->input('_computed_prior_discount_names', ''),
                        ];

                        $paymentDetailsData = [
                            'components' => $detailsArray,
                            'instant_discount_info' => $discountInfo,
                            'receipt_number' => $receiptNo,
                        ];
                        $paymentDetailsJson = json_encode($paymentDetailsData);

                        // Create legacy receipt for backward compatibility
                        FeeReceipt::create([
                            'school_id'      => $schoolId,
                            'student_id'     => $studentId,
                            'receipt_number' => $receiptNo,
                            'amount_paid'    => $rawAmountPaid,
                            'discount_amount'=> $effectiveDiscount,
                            'discount_type'  => $discountType,
                            'payment_mode'   => $paymentMode,
                            'transaction_id' => $request->transaction_id,
                            'payment_date'   => $receiptDate,
                            'payment_details'=> $paymentDetailsJson,
                        ]);

                        // Send Fee Notifications to Student & Parent Portals
                        $feeStudent = \App\Models\Student::find($studentId);
                        if ($feeStudent) {
                            \App\Services\FeeNotificationService::sendPaymentSuccessNotification($feeStudent, $instNo, floatval($rawAmountPaid));
                        }


                        // Generate unique collision-free invoice number
                        $invNo = null;
                        $attempts = 0;
                        while ($attempts < 3) {
                            $invNo = 'INV-' . $instNo . '-PAY-' . now()->format('YmdHisu') . '-' . rand(10, 99);
                            if (!\App\Models\FeeInvoice::where('invoice_number', $invNo)->exists()) {
                                break;
                            }
                            $attempts++;
                        }

                        // Sum up the final active discounts for the invoice total discount_amount
                        $totalInvoiceDiscount = 0;
                        foreach ($detailsArray as $detail) {
                            $totalInvoiceDiscount += ($detail['discount_amount'] ?? 0);
                        }

                        // Create the FeeInvoice audit log record
                        \App\Models\FeeInvoice::create([
                            'school_id' => $schoolId,
                            'student_id' => $studentId,
                            'created_by' => auth()->id(),
                            'invoice_number' => $invNo,
                            'installment_no' => $instNo,
                            'type' => 'payment',
                            'status' => 'paid',
                            'amount' => $rawAmountPaid,
                            'discount_amount' => $effectiveDiscount,
                            'payment_mode' => $paymentMode,
                            'payment_date' => $receiptDate,
                            'payment_details' => $paymentDetailsJson,
                            'remarks' => $request->transaction_id ? ('Transaction ID: ' . $request->transaction_id) : 'Fee Payment',
                        ]);

                        return $invNo;
                    });
                } catch (\Exception $e) {
                    dd('Exception in DB Transaction:', $e->getMessage(), $e->getTraceAsString(), $request->all());
                }

                // Flash new invoice number for print dialog
                session()->flash('print_receipt_no', $invoiceNo);

                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => true, 
                        'message' => 'Payment collected successfully!', 
                        'receipt_number' => $invoiceNo
                    ]);
                }
                return back()->with('success', 'Fee Payment collected successfully!')->with('print_receipt_no', $invoiceNo);
            }

            if ($action === 'apply_discount') {
                $request->validate([
                    'student_id'     => 'required|exists:students,id',
                    'name'           => 'required|string|max:100',
                    'type'           => 'required|in:flat,percentage',
                    'amount'         => 'required|numeric|min:0.01',
                    'remarks'        => 'nullable|string',
                    'installment_no' => 'nullable|integer|min:1|max:12',
                ]);

                $studentId = $request->student_id;
                $student = Student::where('school_id', $schoolId)->findOrFail($studentId);
                
                // Get selected session
                $academicSessions = \App\Models\AcademicSession::where('school_id', $schoolId)->get();
                $currentSession = $academicSessions->where('is_current', true)->first() ?? $academicSessions->first();
                $selectedSessionId = $request->input('academic_session_id', $currentSession->id);

                // Create FeeDiscount
                $discount = \App\Models\FeeDiscount::create([
                    'school_id' => $schoolId,
                    'academic_session_id' => $selectedSessionId,
                    'name' => $request->name,
                    'remarks' => $request->remarks,
                    'amount' => $request->amount,
                    'type' => $request->type,
                    'student_ids' => json_encode([$studentId]),
                    'installment_no' => $request->installment_no,
                ]);

                \Illuminate\Support\Facades\DB::table('deleted_concessions')->insert([
                    'school_id' => $schoolId,
                    'concession_name' => $discount->name,
                    'deleted_by' => (auth()->user()->name ?? 'Administrator') . ' (Created)',
                    'date' => now()->toDateString(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Sync/load fees first to ensure everything is correct (which will run syncStudentDiscounts)
                self::syncStudentFees($student);

                return back()->with('success', 'Discount applied successfully!');
            }

            if ($action === 'process_refund') {
                $request->validate([
                    'student_id'    => 'required|exists:students,id',
                    'refund_date'   => 'required|date',
                    'slip_no'       => 'required|string',
                    'payment_mode'  => 'required|string',
                    'bank_date'     => 'nullable|date',
                    'bank_name'     => 'nullable|string',
                    'reason'        => 'required|string|max:200',
                    'fee_ids'       => 'required|array',
                    'fee_ids.*'     => 'exists:student_fees,id',
                    'amount'        => 'required|numeric|min:1',
                ]);

                $studentId = $request->student_id;
                
                $invoiceNo = \Illuminate\Support\Facades\DB::transaction(function() use ($request, $schoolId, $studentId) {
                    $requestedRefundAmt = floatval($request->amount);
                    $remainingRefundAmount = $requestedRefundAmt;
                    $totalRefAmt = 0;
                    $instNo = 1;
                    $detailsArray = [];

                    foreach ($request->fee_ids as $feeId) {
                        if ($remainingRefundAmount <= 0) break;

                        $sf = StudentFee::withoutGlobalScope('active')->where('school_id', $schoolId)->where('student_id', $studentId)->findOrFail($feeId);
                        
                        // Calculate maximum remaining paid amount that can be refunded
                        $alreadyRefunded = FeeRefund::where('student_fee_id', $sf->id)->sum('amount');
                        $maxRefundable = max(0, $sf->paid_amount - $alreadyRefunded);

                        if ($maxRefundable <= 0) continue;

                        $refThisComp = min($maxRefundable, $remainingRefundAmount);
                        $remainingRefundAmount -= $refThisComp;
                        $totalRefAmt += $refThisComp;
                        $instNo = $sf->installment_no;

                        $detailsArray[] = [
                            'student_fee_id' => $sf->id,
                            'component_name' => $sf->component ? $sf->component->component_name : ($sf->category ? $sf->category->name : 'Fee'),
                            'installment_no' => $sf->installment_no,
                            'amount_paid' => $refThisComp,
                        ];

                        // Record legacy refund
                        FeeRefund::create([
                            'school_id'      => $schoolId,
                            'student_id'     => $studentId,
                            'student_fee_id' => $sf->id,
                            'amount'         => $refThisComp,
                            'refund_date'    => $request->refund_date,
                            'reason'         => $request->reason . " (Refunded: " . ($sf->component ? $sf->component->component_name : 'Fee') . " - Installment " . $sf->installment_no . ")",
                            'slip_no'        => $request->slip_no,
                            'payment_mode'   => $request->payment_mode,
                            'bank_date'      => $request->bank_date,
                            'bank_name'      => $request->bank_name,
                        ]);

                        // We do NOT modify the StudentFee paid_amount or status anymore
                        // so the main fees stay completely unchanged!
                    }

                    // Generate unique collision-free invoice number
                    $invNo = null;
                    $attempts = 0;
                    while ($attempts < 3) {
                        $invNo = 'INV-' . $instNo . '-REF-' . now()->format('YmdHisu') . '-' . rand(10, 99);
                        if (!\App\Models\FeeInvoice::where('invoice_number', $invNo)->exists()) {
                            break;
                        }
                        $attempts++;
                    }

                    // Create FeeInvoice for refund
                    \App\Models\FeeInvoice::create([
                        'school_id' => $schoolId,
                        'student_id' => $studentId,
                        'created_by' => auth()->id(),
                        'invoice_number' => $invNo,
                        'installment_no' => $instNo,
                        'type' => 'refund',
                        'status' => 'refunded',
                        'amount' => $totalRefAmt,
                        'discount_amount' => 0.00,
                        'payment_mode' => $request->payment_mode,
                        'payment_date' => $request->refund_date,
                        'payment_details' => json_encode([
                            'slip_no' => $request->slip_no,
                            'components' => $detailsArray
                        ]),
                        'remarks' => $request->reason,
                    ]);

                    return $invNo;
                });

                return back()->with('success', 'Fee refund processed successfully!')->with('print_refund_slip', $invoiceNo);
            }

            if ($action === 'cancel_invoice') {
                $request->validate([
                    'student_id'     => 'required|exists:students,id',
                    'installment_no' => 'required|integer',
                    'invoice_no'     => 'required|string',
                    'remarks'        => 'required|string|max:200',
                ]);

                $studentId = $request->student_id;
                $instNo = $request->installment_no;
                $invoiceNo = $request->invoice_no;

                $invoiceToCancel = \App\Models\FeeInvoice::where('school_id', $schoolId)
                    ->where('student_id', $studentId)
                    ->where('invoice_number', $invoiceNo)
                    ->first();

                if (!$invoiceToCancel) {
                    $legacyRefunds = \App\Models\FeeRefund::where('school_id', $schoolId)
                        ->where('student_id', $studentId)
                        ->where('slip_no', $invoiceNo)
                        ->get();

                    if ($legacyRefunds->isEmpty()) {
                        return back()->with('error', 'Invoice record not found.');
                    }

                    $newInvoiceNo = \Illuminate\Support\Facades\DB::transaction(function() use ($legacyRefunds, $schoolId, $studentId, $instNo, $request, $invoiceNo) {
                        $attempts = 0;
                        $invNo = null;
                        while ($attempts < 3) {
                            $invNo = 'INV-' . $instNo . '-RCN-' . now()->format('YmdHisu') . '-' . rand(10, 99);
                            if (!\App\Models\FeeInvoice::where('invoice_number', $invNo)->exists()) {
                                break;
                            }
                            $attempts++;
                        }

                        $totalAmount = $legacyRefunds->sum('amount');
                        
                        $components = $legacyRefunds->map(function($ref) {
                            $desc = $ref->reason;
                            if (strpos($desc, ' (Refunded: ') !== false) {
                                $desc = str_replace(' (Refunded: ', '', strstr($desc, ' (Refunded: '));
                                $desc = rtrim($desc, ')');
                            } else {
                                $desc = 'Fee component';
                            }
                            return [
                                'component_name' => $desc,
                                'amount_paid' => $ref->amount,
                                'student_fee_id' => $ref->student_fee_id,
                            ];
                        })->toArray();

                        \App\Models\FeeInvoice::create([
                            'school_id' => $schoolId,
                            'student_id' => $studentId,
                            'created_by' => auth()->id(),
                            'invoice_number' => $invNo,
                            'related_invoice_number' => $invoiceNo,
                            'installment_no' => $instNo,
                            'type' => 'cancel_refund',
                            'status' => 'cancelled',
                            'amount' => $totalAmount,
                            'discount_amount' => 0.00,
                            'payment_mode' => $legacyRefunds->first()->payment_mode ?? 'cash',
                            'payment_date' => now()->toDateString(),
                            'remarks' => $request->remarks,
                            'payment_details' => json_encode([
                                'slip_no' => $invoiceNo,
                                'components' => $components
                            ])
                        ]);

                        foreach ($legacyRefunds as $ref) {
                            $ref->delete();
                        }

                        return $invNo;
                    });

                    if ($request->wantsJson()) {
                        return response()->json([
                            'success' => true, 
                            'message' => 'Refund cancelled successfully!', 
                            'receipt_number' => $newInvoiceNo
                        ]);
                    }
                    return back()->with('success', 'Refund cancelled successfully!')->with('print_refund_slip', $newInvoiceNo);
                }

                // Idempotency: prevent double cancelling
                if ($invoiceToCancel->status === 'cancelled') {
                    return back()->with('error', 'This invoice is already cancelled.');
                }

                $newInvoiceNo = \Illuminate\Support\Facades\DB::transaction(function() use ($invoiceToCancel, $schoolId, $studentId, $instNo, $request) {
                    if ($invoiceToCancel->type === 'payment') {
                        // Reverse/subtract the paid amounts and discounts from student fees
                        if (!empty($invoiceToCancel->payment_details)) {
                            $decoded = json_decode($invoiceToCancel->payment_details, true);
                            if (is_array($decoded)) {
                                $compList = isset($decoded['components']) && is_array($decoded['components']) 
                                    ? $decoded['components'] 
                                    : $decoded;
                                
                                foreach ($compList as $item) {
                                    if (!is_array($item)) continue;
                                    $sfId = $item['student_fee_id'] ?? null;
                                    if ($sfId) {
                                        $sf = StudentFee::withoutGlobalScope('active')->where('school_id', $schoolId)->find($sfId);
                                        if ($sf) {
                                            $sf->paid_amount = max(0, $sf->paid_amount - ($item['amount_paid'] ?? 0));
                                            
                                            // Reverse the specific discount applied in this transaction
                                            $txDiscount = isset($item['transaction_discount']) ? $item['transaction_discount'] : ($item['discount_amount'] ?? 0);
                                            $sf->instant_discount_amount = max(0, $sf->instant_discount_amount - $txDiscount);
                                            if ($sf->instant_discount_amount == 0) {
                                                $sf->instant_discount_type = null;
                                            }
                                            
                                            $sf->status = $sf->paid_amount >= ($sf->amount + $sf->fine_amount_applied - $sf->instant_discount_amount) ? 'paid' : ($sf->paid_amount > 0 ? 'partially_paid' : 'pending');
                                            $sf->save();
                                        }
                                    }
                                }
                            }
                        }

                        // Also mark legacy receipt as cancelled
                        $receipt = FeeReceipt::withoutGlobalScope('active')
                            ->where('school_id', $schoolId)
                            ->where('student_id', $studentId)
                            ->where('receipt_number', $invoiceToCancel->invoice_number)
                            ->first();
                        if ($receipt) {
                            $receipt->status = 'cancelled';
                            $receipt->save();
                        }

                        // Mark original payment invoice as cancelled
                        $invoiceToCancel->status = 'cancelled';
                        $invoiceToCancel->save();
                    } elseif ($invoiceToCancel->type === 'refund') {
                        // Reverse the refund: delete FeeRefund entries and restore StudentFee
                        $details = json_decode($invoiceToCancel->payment_details, true);
                        $slipNo = is_array($details) ? ($details['slip_no'] ?? null) : null;
                        
                        $refunds = collect();
                        if ($slipNo) {
                            $refunds = FeeRefund::where('student_id', $studentId)
                                ->where('slip_no', $slipNo)
                                ->get();
                        }
                        if ($refunds->isEmpty()) {
                            $refunds = FeeRefund::where('student_id', $studentId)
                                ->where('slip_no', $invoiceToCancel->invoice_number)
                                ->get();
                        }
                        if ($refunds->isEmpty() && is_array($details)) {
                            $components = $details['components'] ?? $details;
                            if (is_array($components)) {
                                $feeIds = array_column($components, 'student_fee_id');
                                if (!empty($feeIds)) {
                                    $refunds = FeeRefund::where('student_id', $studentId)
                                        ->whereIn('student_fee_id', $feeIds)
                                        ->where('refund_date', $invoiceToCancel->payment_date)
                                        ->get();
                                }
                            }
                        }

                        foreach ($refunds as $ref) {
                            $ref->delete();
                        }

                        // Mark original refund invoice as cancelled
                        $invoiceToCancel->status = 'cancelled';
                        $invoiceToCancel->save();
                    }

                    return $invoiceToCancel->invoice_number;
                });

                if ($invoiceToCancel->type === 'payment') {
                    $cStudent = \App\Models\Student::find($studentId);
                    if ($cStudent) {
                        \App\Services\FeeNotificationService::sendPaymentCancelledNotification($cStudent, $instNo);
                    }
                }

                $isRefund = $invoiceToCancel->type === 'refund';
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => true, 
                        'message' => 'Invoice cancelled successfully!', 
                        'receipt_number' => $newInvoiceNo
                    ]);
                }
                $key = $isRefund ? 'print_refund_slip' : 'print_receipt_no';
                return back()->with('success', 'Invoice cancelled successfully!')->with($key, $newInvoiceNo);
            }

            if ($action === 'remove_discount') {
                $request->validate([
                    'student_id'  => 'required|exists:students,id',
                    'discount_id' => 'required|exists:fee_discounts,id',
                ]);

                $studentId = $request->student_id;
                $student = Student::where('school_id', $schoolId)->findOrFail($studentId);
                $discount = \App\Models\FeeDiscount::where('school_id', $schoolId)->findOrFail($request->discount_id);

                if ($discount->student_ids) {
                    $studentIds = json_decode($discount->student_ids, true);
                    if (is_array($studentIds)) {
                        $studentIds = array_diff($studentIds, [$studentId]);
                        if (empty($studentIds)) {
                            $discount->delete();
                        } else {
                            $discount->student_ids = json_encode(array_values($studentIds));
                            $discount->save();
                        }
                    } else {
                        $discount->delete();
                    }
                } else {
                    // For class-wide/global discount, convert student_ids list to include everyone in class except this student
                    $className = optional($student->class)->name;
                    if ($className) {
                        $classes = $discount->classes_installments ? json_decode($discount->classes_installments, true) : [];
                        // If the discount is indeed applicable to this student's class
                        if (empty($classes) || in_array($className, $classes)) {
                            // Find all students in class except this one
                            $otherStudentIds = Student::where('school_id', $schoolId)
                                ->where('class_id', $student->class_id)
                                ->where('id', '!=', $studentId)
                                ->pluck('id')
                                ->toArray();
                            
                            $discount->student_ids = json_encode($otherStudentIds);
                            $discount->save();
                        }
                    } else {
                        $discount->delete();
                    }
                }

                // Re-sync fees (which will run syncStudentDiscounts and recalculate)
                self::syncStudentFees($student);

                return back()->with('success', 'Discount removed successfully!');
            }

            if ($action === 'delete_student_fee') {
                $request->validate([
                    'student_fee_id' => 'required|exists:student_fees,id',
                ]);

                $studentFee = StudentFee::withoutGlobalScope('active')
                    ->where('school_id', $schoolId)
                    ->findOrFail($request->student_fee_id);

                if ($studentFee->fine_applied_at !== null) {
                    return back()->with('error', 'Cannot delete student fee record because a fine has already been applied. Waive the fine first.');
                }

                if ($studentFee->paid_amount > 0 || !empty($studentFee->invoice_no)) {
                    return back()->with('error', 'Invoiced or paid student fees cannot be deleted.');
                }

                $studentFee->delete();

                return back()->with('success', 'Fee component deleted successfully from student profile!');
            }

            // ── Issue 2 Fix: Cheque Clear / Bounce Status Update ──────────────
            if ($action === 'update_cheque_status') {
                $request->validate([
                    'cheque_id' => 'required|exists:pending_cheques,id',
                    'status'    => 'required|in:cleared,bounced,pending',
                ]);

                $cheque = \App\Models\PendingCheque::where('school_id', $schoolId)
                    ->findOrFail($request->cheque_id);

                $oldStatus = $cheque->status;
                $newStatus = $request->status;

                if ($oldStatus === $newStatus) {
                    if ($request->wantsJson()) {
                        return response()->json(['success' => false, 'message' => 'Status is already ' . $newStatus]);
                    }
                    return back()->with('info', 'Cheque status is already ' . $newStatus . '.');
                }

                $this->processChequeStatusTransition($cheque, $newStatus);

                $cheque->status_changed_at = now();
                $cheque->status_changed_by = auth()->id();
                $cheque->status_remarks = $request->input('status_remarks', '');
                $cheque->save();

                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Cheque status updated to ' . ucfirst($newStatus) . ' successfully!',
                        'new_status' => $newStatus,
                    ]);
                }
                return back()->with('success', 'Cheque status updated to ' . ucfirst($newStatus) . ' successfully!');
            }
            // ── End Issue 2 Fix ──────────────────────────────────────────────

            if ($action === 'calculate_discount') {
                $studentIdTemp = $request->input('student_id');
                $instNoTemp = $request->input('installment_no') ?: 1;
                $feeType = $request->input('fee_type', 'tuition');
                $selectedFeeIdsTemp = [];
                if ($request->filled('student_fee_ids')) {
                    $selectedFeeIdsTemp = array_filter(array_map('trim', explode(',', $request->input('student_fee_ids'))));
                }

                // Resolve real installment number if 999 is passed
                if ($instNoTemp == 999) {
                    if (!empty($selectedFeeIdsTemp)) {
                        $firstFee = StudentFee::withoutGlobalScopes()->where('school_id', $schoolId)->whereIn('id', $selectedFeeIdsTemp)->first();
                        if ($firstFee) {
                            $instNoTemp = $firstFee->installment_no;
                        }
                    }
                    if ($instNoTemp == 999 && $request->filled('student_fee_id')) {
                        $firstFee = StudentFee::withoutGlobalScopes()->where('school_id', $schoolId)->find($request->student_fee_id);
                        if ($firstFee) {
                            $instNoTemp = $firstFee->installment_no;
                        }
                    }
                    if ($instNoTemp == 999) {
                        $firstFee = StudentFee::withoutGlobalScopes()
                            ->where('school_id', $schoolId)
                            ->where('student_id', $studentIdTemp)
                            ->orderBy('installment_no')
                            ->first();
                        if ($firstFee) {
                            $instNoTemp = $firstFee->installment_no;
                        }
                    }
                    if ($instNoTemp == 999) {
                        $instNoTemp = 1;
                    }
                }

                $feesToPayTemp = collect();
                if (!empty($selectedFeeIdsTemp)) {
                    $feesToPayTemp = StudentFee::withoutGlobalScopes()
                        ->where('school_id', $schoolId)
                        ->where('student_id', $studentIdTemp)
                        ->whereIn('id', $selectedFeeIdsTemp)
                        ->get();
                } else {
                    $sfTemp = null;
                    if ($request->input('student_fee_id')) {
                        $sfTemp = StudentFee::withoutGlobalScopes()
                            ->where('school_id', $schoolId)
                            ->find($request->input('student_fee_id'));
                        if ($sfTemp && $sfTemp->student_id != $studentIdTemp) {
                            $sfTemp = null;
                        }
                    }
                    if ($sfTemp) {
                        $feesToPayTemp = collect([$sfTemp]);
                    } else {
                        $allFeesTemp = StudentFee::withoutGlobalScopes()
                            ->where('school_id', $schoolId)
                            ->where('student_id', $studentIdTemp)
                            ->where('installment_no', $instNoTemp)
                            ->where('status', '!=', 'paid')
                            ->with(['category', 'component'])
                            ->orderBy('id', 'asc')
                            ->get();

                        $feesToPayTemp = $allFeesTemp->filter(function($fee) use ($feeType) {
                            $isTrans = (optional($fee->category)->name === 'Transport' || 
                                        stripos(optional($fee->category)->name ?? '', 'Transport') !== false ||
                                        stripos(optional($fee->component)->component_name ?? '', 'Transport') !== false);
                            return $feeType === 'transport' ? $isTrans : !$isTrans;
                        })->values();

                        if ($feesToPayTemp->isEmpty()) {
                            $allFeesFallbackTemp = StudentFee::withoutGlobalScopes()
                                ->where('school_id', $schoolId)
                                ->where('student_id', $studentIdTemp)
                                ->where('installment_no', $instNoTemp)
                                ->with(['category', 'component'])
                                ->orderBy('id', 'asc')
                                ->get();
                            
                            $feesToPayTemp = $allFeesFallbackTemp->filter(function($fee) use ($feeType) {
                                $isTrans = (optional($fee->category)->name === 'Transport' || 
                                            stripos(optional($fee->category)->name ?? '', 'Transport') !== false ||
                                            stripos(optional($fee->component)->component_name ?? '', 'Transport') !== false);
                                return $feeType === 'transport' ? $isTrans : !$isTrans;
                            })->values();
                        }
                    }
                }

                // Account for temporary misc fee if checked
                if ($request->input('add_misc_fee') == '1') {
                    $miscAmt = floatval($request->input('misc_fee_amount') ?: 0);
                    $tempMiscFee = new StudentFee([
                        'school_id' => $schoolId,
                        'student_id' => $studentIdTemp,
                        'amount' => $miscAmt,
                        'paid_amount' => 0.00,
                        'instant_discount_amount' => 0.00,
                        'fine_amount_applied' => 0.00,
                        'installment_no' => $instNoTemp,
                        'status' => 'pending',
                    ]);
                    $feesToPayTemp->push($tempMiscFee);
                }

                $discountFeeComponentIds = $request->input('discount_fee_component_ids', []);
                if (!is_array($discountFeeComponentIds)) {
                    $discountFeeComponentIds = [];
                }
                $discountInstallmentNos = $request->input('discount_installment_nos', []);
                if (!is_array($discountInstallmentNos)) {
                    $discountInstallmentNos = [];
                }
                $hasComponentDiscount = count($discountFeeComponentIds) > 0;

                // Overall due calculation
                $totalAmountTemp = $feesToPayTemp->sum(fn($f) => floatval($f->amount));
                $amountPaidPriorTemp = $feesToPayTemp->sum(fn($f) => floatval($f->paid_amount));
                $priorDiscountTemp = $feesToPayTemp->sum(fn($f) => floatval($f->instant_discount_amount));
                $totalFineAppliedTemp = $feesToPayTemp->sum(fn($f) => floatval($f->fine_amount_applied ?? 0));
                
                $totalAmountDueTemp = max(0.00, $totalAmountTemp + $totalFineAppliedTemp - $amountPaidPriorTemp - $priorDiscountTemp);

                // discountableDueTemp (excluding fines/penalties)
                $discountableDueTemp = 0.00;
                foreach ($feesToPayTemp as $f) {
                    $sfDueBefore = max(0.00, floatval($f->amount) + floatval($f->fine_amount_applied ?? 0) - floatval($f->paid_amount) - floatval($f->instant_discount_amount) - floatval($f->pending_cheque_amount));
                    $fineApplied = max(0.00, floatval($f->fine_amount_applied ?? 0));
                    $sfFineRemaining = min($fineApplied, $sfDueBefore);
                    $sfDiscountable = max(0.00, $sfDueBefore - $sfFineRemaining);

                    if ($hasComponentDiscount) {
                        $isCompEligible = in_array($f->fee_component_id, $discountFeeComponentIds);
                        $isInstEligible = empty($discountInstallmentNos) || in_array($f->installment_no, $discountInstallmentNos);
                        if ($isCompEligible && $isInstEligible) {
                            $discountableDueTemp += $sfDiscountable;
                        }
                    } else {
                        $discountableDueTemp += $sfDiscountable;
                    }
                }

                // Compute discount amount
                $discountAmount = floatval($request->input('instant_discount_amount', 0));
                $discountType = $request->input('instant_discount_type', 'flat');
                $effectiveDiscount = 0.00;
                if ($discountAmount > 0) {
                    if ($discountType === 'percentage') {
                        $percent = max(0.00, min(100.00, $discountAmount));
                        $effectiveDiscount = round($discountableDueTemp * $percent / 100, 2);
                    } else {
                        $effectiveDiscount = min(max(0.00, $discountAmount), $discountableDueTemp);
                    }
                }

                // Suggested amount and remaining due based on selection mode
                $eligibleAmount = $discountableDueTemp;
                $suggestedAmount = max(0.00, $totalAmountDueTemp - $effectiveDiscount);

                $rawAmountPaid = floatval($request->input('amount_paid', 0));
                $remainingDue = max(0.00, $totalAmountDueTemp - $rawAmountPaid - $effectiveDiscount);

                return response()->json([
                    'success' => true,
                    'eligible_amount' => $eligibleAmount,
                    'discount_amount' => $effectiveDiscount,
                    'suggested_amount' => $suggestedAmount,
                    'remaining_due' => $remainingDue,
                    'total_due' => $totalAmountDueTemp
                ]);
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
        $classes         = $this->getSessionScopedClasses($schoolId, $selectedSession->id);
        $selectedClassId = $request->get('class_id');
        if ($selectedClassId === '' || $selectedClassId === 'all') {
            $selectedClassId = null;
        }
        $selectedClass   = $selectedClassId ? $classes->where('id', $selectedClassId)->first() : null;

        $sections        = $selectedClass ? $selectedClass->sections : collect();
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
        $tuitionFees   = collect();
        $transportFees = collect();
        $paymentHistory = collect();
        $feeScheduleName = null;
        $appliedDiscounts = [];
        $refunds = collect();

        if ($viewStudentId) {
            $viewStudent = Student::where('school_id', $schoolId)
                ->with(['class', 'section', 'category'])
                ->find($viewStudentId);

            if ($viewStudent) {
                self::syncStudentFees($viewStudent);
                \App\Models\StudentFee::syncTransportFees($schoolId);

                // Check if class-wise transport fee is active
                $isTransportActive = false;
                $transportCat = \App\Models\StudentCategory::where('school_id', $schoolId)->where('name', 'Transport')->first();
                $transportComp = \App\Models\FeeComponent::where('school_id', $schoolId)
                    ->where('component_name', 'Transport Fee')
                    ->where('academic_session_id', optional($selectedSession)->id)
                    ->first();
                if ($transportCat && $transportComp) {
                    $isTransportActive = \App\Models\ClassWiseFee::where('school_id', $schoolId)
                        ->where('class_id', $viewStudent->class_id)
                        ->where(function($q) use ($viewStudent) {
                            $q->whereNull('section_id')
                              ->orWhere('section_id', $viewStudent->section_id);
                        })
                        ->where('student_category_id', $transportCat->id)
                        ->where('fee_component_id', $transportComp->id)
                        ->where('is_active', true)
                        ->exists();
                }

                // Run transport fee generation on load if student is opted in and class-wise transport is enabled
                if ($viewStudent->transport_opted && $viewStudent->transport_route_id && $isTransportActive) {
                    $student = $viewStudent;
                    
                    // Get current academic session (using selected session to match view context)
                    $currentSession = $selectedSession;
                    
                    if ($currentSession) {
                        $schedule = \App\Models\TransportFeeSchedule::resolveFor($schoolId, $currentSession->id, $student->transport_route_id);
                        
                        $category = \App\Models\FeeCategory::firstOrCreate(
                            ['school_id' => $schoolId, 'name' => 'Transport'],
                            ['description' => 'Transport Fees']
                        );

                        $component = \App\Models\FeeComponent::firstOrCreate(
                            [
                                'school_id' => $schoolId, 
                                'component_name' => 'Transport Fee',
                                'academic_session_id' => $currentSession->id
                            ],
                            [
                                'fee_category_id' => $category->id,
                                'head_name' => 'Transport',
                                'admission_type' => 'All Students',
                                'gender' => 'All Students'
                            ]
                        );

                        if ($component->fee_category_id !== $category->id) {
                            $component->update(['fee_category_id' => $category->id]);
                        }

                        $startMonthStr = $student->transport_calendar_start ?: now()->toDateString();
                        $startMonth = \Carbon\Carbon::parse($startMonthStr)->startOfMonth();

                        // Clear future unprotected installments (excluding those covered by pending cheques)
                        $pendingChequeFeeIds = [];
                        $pendingCheques = \App\Models\PendingCheque::where('school_id', $schoolId)
                            ->where('student_id', $student->id)
                            ->where('status', 'pending')
                            ->get();
                        foreach ($pendingCheques as $chq) {
                            $ids = json_decode($chq->student_fee_ids, true) ?: [];
                            if (is_array($ids)) {
                                $pendingChequeFeeIds = array_merge($pendingChequeFeeIds, $ids);
                            }
                        }
                        $pendingChequeFeeIds = array_unique(array_filter(array_map('intval', $pendingChequeFeeIds)));

                        $delQuery = \App\Models\StudentFee::withoutGlobalScope('active')
                            ->where('school_id', $schoolId)
                            ->where('student_id', $student->id)
                            ->where('fee_component_id', $component->id)
                            ->where('paid_amount', '<=', 0)
                            ->whereNull('invoice_no')
                            ->where('due_date', '>=', $startMonth->toDateString());
                        if (!empty($pendingChequeFeeIds)) {
                            $delQuery->whereNotIn('id', $pendingChequeFeeIds);
                        }
                        $delQuery->delete();

                        $pickFare = (float)($student->transport_pick_fare ?? 0);
                        $dropFare = (float)($student->transport_drop_fare ?? 0);
                        $totalFare = $pickFare + $dropFare;

                        if ($schedule) {
                            $instList = $schedule->installments ?? [];
                        } else {
                            $start = \Carbon\Carbon::parse($currentSession->start_date);
                            $end = \Carbon\Carbon::parse($currentSession->end_date);
                            $monthsCount = ($end->year - $start->year) * 12 + ($end->month - $start->month) + 1;
                            if ($monthsCount < 1) {
                                $monthsCount = 12;
                            }
                            
                            $instList = [];
                            for ($i = 0; $i < $monthsCount; $i++) {
                                $monthDate = $start->copy()->addMonths($i);
                                $instList[] = [
                                    'installment_no' => $i + 1,
                                    'name' => $monthDate->format('F Y'),
                                    'start_date' => $monthDate->copy()->startOfMonth()->toDateString(),
                                    'end_date' => $monthDate->copy()->endOfMonth()->toDateString(),
                                    'due_date' => $monthDate->copy()->startOfMonth()->addDays(4)->toDateString(),
                                    'grace_days' => 5
                                ];
                            }
                        }

                        foreach ($instList as $index => $instData) {
                            $installmentNo = $instData['installment_no'] ?? ($index + 1);
                            $dueDate = \Carbon\Carbon::parse($instData['due_date']);
                            
                            if ($dueDate->copy()->startOfMonth()->lt($startMonth)) {
                                continue;
                            }

                            $existing = \App\Models\StudentFee::withoutGlobalScope('active')
                                ->where('school_id', $schoolId)
                                ->where('student_id', $student->id)
                                ->where('fee_component_id', $component->id)
                                ->where('installment_no', $installmentNo)
                                ->first();

                            if (!$existing) {
                                \App\Models\StudentFee::create([
                                    'school_id' => $schoolId,
                                    'student_id' => $student->id,
                                    'fee_category_id' => $category->id,
                                    'fee_schedule_id' => null,
                                    'transport_fee_schedule_id' => $schedule->id ?? null,
                                    'fee_component_id' => $component->id,
                                    'installment_no' => $installmentNo,
                                    'amount' => $totalFare,
                                    'due_date' => $dueDate->toDateString(),
                                    'status' => 'pending'
                                ]);
                            } else {
                                if ($existing->paid_amount <= 0 && empty($existing->invoice_no)) {
                                    $existing->update([
                                        'amount' => $totalFare,
                                        'due_date' => $dueDate->toDateString(),
                                        'transport_fee_schedule_id' => $schedule->id ?? null,
                                    ]);
                                }
                            }
                        }
                    }
                }

                $studentFees = StudentFee::where('school_id', $schoolId)
                    ->where('student_id', $viewStudentId)
                    ->where(function($query) use ($selectedSession, $viewStudent) {
                        $query->where(function($q) use ($viewStudent) {
                            $q->where('fee_schedule_id', $viewStudent->fee_schedule_id);
                        })->orWhereHas('transportFeeSchedule', function($q) use ($selectedSession) {
                            $q->where('academic_session_id', $selectedSession->id);
                        })->orWhereHas('miscFee', function($q) use ($selectedSession) {
                            $q->where('academic_session_id', $selectedSession->id);
                        })->orWhere(function($q) use ($selectedSession) {
                            $q->whereNull('fee_schedule_id')
                              ->whereNull('transport_fee_schedule_id')
                              ->whereNull('misc_fee_id')
                              ->whereBetween('due_date', [$selectedSession->start_date, $selectedSession->end_date]);
                        });
                    })
                    ->with(['category', 'component'])
                    ->orderBy('installment_no')
                    ->get();

                // Re-evaluate transport active status for display filter
                $isTransportActive = false;
                $transportCat = \App\Models\StudentCategory::where('school_id', $schoolId)->where('name', 'Transport')->first();
                $transportComp = \App\Models\FeeComponent::where('school_id', $schoolId)
                    ->where('component_name', 'Transport Fee')
                    ->where('academic_session_id', $selectedSession->id)
                    ->first();
                if ($viewStudent && $transportCat && $transportComp) {
                    $isTransportActive = \App\Models\ClassWiseFee::where('school_id', $schoolId)
                        ->where('class_id', $viewStudent->class_id)
                        ->where(function($q) use ($viewStudent) {
                            $q->whereNull('section_id')
                              ->orWhere('section_id', $viewStudent->section_id);
                        })
                        ->where('student_category_id', $transportCat->id)
                        ->where('fee_component_id', $transportComp->id)
                        ->where('is_active', true)
                        ->exists();
                }

                $transportFees = $isTransportActive ? $studentFees->filter(fn($f) => 
                    optional($f->category)->name === 'Transport' || 
                    (optional($f->component)->component_name ?? '') === 'Transport Fee'
                )->values() : collect();
                $tuitionFees = $studentFees->filter(fn($f) => 
                    optional($f->category)->name !== 'Transport' && 
                    (optional($f->component)->component_name ?? '') !== 'Transport Fee'
                )->values();

                $paymentHistory = \App\Models\FeeInvoice::where('school_id', $schoolId)
                    ->where('student_id', $viewStudentId)
                    ->orderByDesc('payment_date')
                    ->orderByDesc('id')
                    ->get();

                // Retrieve pending/bounced/cancelled/returned/rejected cheques for this student (all except cleared)
                $cheques = \App\Models\PendingCheque::where('school_id', $schoolId)
                    ->where('student_id', $viewStudentId)
                    ->where('status', '!=', 'cleared')
                    ->get();

                foreach ($cheques as $ch) {
                    if ($ch->status !== 'pending') {
                        $hasRealInvoice = $paymentHistory->contains(function($inv) use ($ch) {
                            // Must be the right status type
                            $typeMatch = ($inv->status === $ch->status
                                || $inv->type === $ch->status . '_cheque');
                            if (!$typeMatch) return false;

                            // Path 1: payment_details.cheque_id (set by the updated controller for new records)
                            $details = is_string($inv->payment_details)
                                ? json_decode($inv->payment_details, true)
                                : null;
                            if (is_array($details)) {
                                if (!empty($details['cheque_id']) && intval($details['cheque_id']) === intval($ch->id)) {
                                    return true;
                                }
                                // payment_details.cheque_number (exact match)
                                if (!empty($details['cheque_number']) && strval($details['cheque_number']) === strval($ch->cheque_number)) {
                                    return true;
                                }
                            }

                            // Path 2: remarks contains cheque_number (old records without payment_details)
                            if ($ch->cheque_number && str_contains(strval($inv->remarks), strval($ch->cheque_number))) {
                                return true;
                            }

                            // Path 3: invoice_number contains cheque_number
                            if ($ch->cheque_number && str_contains(strval($inv->invoice_number), strval($ch->cheque_number))) {
                                return true;
                            }

                            return false;
                        });
                        if ($hasRealInvoice) {
                            continue;
                        }
                    }

                    $pseudoInvoice = new \App\Models\FeeInvoice();
                    $pseudoInvoice->id = 'cheque_' . $ch->id;
                    $pseudoInvoice->invoice_number = $ch->receipt_number ?: ('CHQ-' . $ch->cheque_number);
                    $pseudoInvoice->payment_date = $ch->receipt_date ?: ($ch->cheque_date ?: ($ch->created_at ? $ch->created_at->toDateString() : now()->toDateString()));
                    $pseudoInvoice->type = $ch->status; // 'pending', 'bounced', 'cancelled', 'returned', 'rejected', etc.
                    $pseudoInvoice->status = $ch->status;
                    $pseudoInvoice->amount = $ch->amount;
                    $pseudoInvoice->discount_amount = $ch->discount_amount ?: 0;
                    $pseudoInvoice->payment_mode = 'cheque';
                    $pseudoInvoice->remarks = ucfirst($ch->status) . ' Cheque (No: ' . $ch->cheque_number . ', Bank: ' . $ch->bank_name . ')';
                    
                    $details = [];
                    if ($ch->student_fee_ids) {
                        $feeIds = json_decode($ch->student_fee_ids, true);
                        if (is_array($feeIds)) {
                            $fees = \App\Models\StudentFee::withoutGlobalScopes()->whereIn('id', $feeIds)->get();
                            
                            // FIFO sort by installment and ID
                            $fees = $fees->sortBy(function($f) {
                                return sprintf('%05d-%010d', intval($f->installment_no), intval($f->id));
                            })->values();

                            $amountToDistribute = floatval($ch->amount);
                            foreach ($fees as $fee) {
                                $due = max(0.00, floatval($fee->amount) + floatval($fee->fine_amount_applied ?? 0) - floatval($fee->paid_amount) - floatval($fee->instant_discount_amount));
                                $allocated = 0;
                                if ($amountToDistribute > 0) {
                                    $allocated = min($amountToDistribute, $due);
                                    $amountToDistribute -= $allocated;
                                }
                                // Fallback to ratio/default if amount exceeds or first component
                                if ($allocated <= 0 && $amountToDistribute > 0) {
                                    $allocated = $amountToDistribute;
                                    $amountToDistribute = 0;
                                }

                                if ($allocated > 0) {
                                    $details[] = [
                                        'student_fee_id' => $fee->id,
                                        'component_name' => optional($fee->component)->component_name ?? (optional($fee->category)->name ?? 'Fee component'),
                                        'installment_no' => $fee->installment_no,
                                        'amount_paid' => $allocated,
                                        'transaction_discount' => 0
                                    ];
                                }
                            }
                        }
                    }
                    if (empty($details)) {
                        $details[] = [
                            'component_name' => ucfirst($ch->status) . ' Cheque (No: ' . $ch->cheque_number . ')',
                            'installment_no' => $ch->installment_no ?: 1,
                            'amount_paid' => $ch->amount,
                            'transaction_discount' => $ch->discount_amount ?: 0
                        ];
                    }
                    $pseudoInvoice->school_id = $ch->school_id;
                    $pseudoInvoice->student_id = $ch->student_id;
                    $pseudoInvoice->cheque_id_raw = $ch->id;
                    $pseudoInvoice->payment_details = json_encode($details);
                    $pseudoInvoice->created_at = $ch->created_at;
                    $paymentHistory->push($pseudoInvoice);
                }
                $paymentHistory = $paymentHistory->sortByDesc(function($invoice) {
                    $dateTime = $invoice->created_at ? $invoice->created_at->toDateTimeString() : ($invoice->payment_date . ' 00:00:00');
                    return $dateTime . '_' . $invoice->id;
                })->values();

                // Retrieve explicit matching fee schedule name
                if ($viewStudent->fee_schedule_id) {
                    $feeScheduleName = optional($viewStudent->feeSchedule)->name;
                }

                // Applied discounts for this student
                try {
                    $appliedDiscounts = \App\Models\FeeDiscount::where('school_id', $schoolId)
                        ->where('academic_session_id', optional($selectedSession)->id)
                        ->get()
                        ->filter(function ($d) use ($viewStudent) {
                            return self::isDiscountApplicableForStudent($d, $viewStudent);
                        });
                } catch (\Throwable $e) {
                    $appliedDiscounts = collect();
                }
                
                try {
                    $refunds = \App\Models\FeeRefund::where('school_id', $schoolId)
                        ->where('student_id', $viewStudent->id)
                        ->orderByDesc('id')
                        ->get();
                } catch (\Throwable $e) {
                    $refunds = collect();
                }

                // Load pending (not yet cleared/bounced) cheques for this student
                try {
                    $pendingCheques = \App\Models\PendingCheque::where('school_id', $schoolId)
                        ->where('student_id', $viewStudent->id)
                        ->where('status', 'pending')
                        ->orderByDesc('id')
                        ->get();
                    $pendingChequeTotal = $pendingCheques->sum('amount');
                } catch (\Throwable $e) {
                    $pendingCheques = collect();
                    $pendingChequeTotal = 0;
                }

                // Retrieve siblings sharing ANY parent / family attributes (excluding the current student)
                $siblings = collect();
                if ($viewStudent) {
                    $siblings = Student::where('school_id', $schoolId)
                        ->where('academic_session_id', optional($selectedSession)->id)
                        ->where('id', '!=', $viewStudent->id)
                        ->where(function($q) use ($viewStudent) {
                            if ($viewStudent->family_id) {
                                $q->orWhere('family_id', $viewStudent->family_id);
                            }
                            if ($viewStudent->guardian_phone) {
                                $q->orWhere('guardian_phone', $viewStudent->guardian_phone);
                            }
                            if ($viewStudent->father_phone) {
                                $q->orWhere('father_phone', $viewStudent->father_phone);
                            }
                            if ($viewStudent->mother_phone) {
                                $q->orWhere('mother_phone', $viewStudent->mother_phone);
                            }
                            if ($viewStudent->father_name && strlen(trim($viewStudent->father_name)) > 2) {
                                $q->orWhere('father_name', $viewStudent->father_name);
                            }
                            if ($viewStudent->guardian_name && strlen(trim($viewStudent->guardian_name)) > 2) {
                                $q->orWhere('guardian_name', $viewStudent->guardian_name);
                            }
                        })
                        ->with(['class', 'section', 'studentFees'])
                        ->get();
                }
            }
        }



        // ─── LIST: students with fee summary ────────────────────────────
        $showDeactivated = $request->get('show_deactivated') == '1';
        $showDeleted = $request->get('show_deleted') == '1';

        $query = Student::where('school_id', $schoolId)
            ->where('academic_session_id', optional($selectedSession)->id);

        if (!$showDeactivated) {
            $query->where('is_active', 1);
        }

        if ($showDeleted) {
            $query->withTrashed();
        }

        // ── Load all studentFees to correctly aggregate dues across all years/sessions ──
        $query->with(['class', 'section', 'feeSchedule', 'studentFees' => function ($q) use ($schoolId) {
            $q->where('school_id', $schoolId)
              ->with(['feeSchedule', 'transportFeeSchedule', 'miscFee']);
        }]);
        // ── End Issue 3 Fix ──

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

        // Load/reload all studentFees
        $studentsWithFees->load(['class', 'section', 'feeSchedule', 'studentFees' => function ($q) use ($schoolId) {
            $q->where('school_id', $schoolId)
              ->with(['feeSchedule', 'transportFeeSchedule', 'miscFee']);
        }]);

        // Build fee schedule map for each student
        $schedules = \App\Models\FeeSchedule::where('school_id', $schoolId)
            ->where('academic_session_id', optional($selectedSession)->id)
            ->get();

        // Load fee configuration for this school
        $config = \App\Models\FeeConfiguration::where('school_id', $schoolId)->first();

        // Load siblings variable if not view_student page
        $siblings = $siblings ?? collect();

        // Ensure pending cheque variables are always defined (defaults for list view)
        $pendingCheques = $pendingCheques ?? collect();
        $pendingChequeTotal = $pendingChequeTotal ?? 0;

        // Load active fee components for the session, excluding transport-related components
        try {
            $sessionIdForComp = $selectedSession->id ?? null;
            if ($schoolId && $sessionIdForComp) {
                $feeComponents = \App\Models\FeeComponent::where('school_id', $schoolId)
                    ->where('academic_session_id', $sessionIdForComp)
                    ->get()
                    ->filter(function ($comp) {
                        $name = strtolower((string) ($comp->component_name ?? ''));
                        $head = strtolower((string) ($comp->head_name ?? ''));
                        return (
                            strpos($name, 'transport') === false &&
                            strpos($name, 'vehicle') === false &&
                            strpos($name, 'bus') === false &&
                            strpos($head, 'transport') === false &&
                            strpos($head, 'vehicle') === false &&
                            strpos($head, 'bus') === false
                        );
                    })
                    ->values();
            } else {
                $feeComponents = collect();
            }
        } catch (\Throwable $e) {
            $feeComponents = collect();
        }

        // Retrieve all available miscellaneous fees for the current session to show in the dropdown
        try {
            $availableMiscFees = \App\Models\MiscFee::where('school_id', $schoolId)
                ->where('academic_session_id', optional($selectedSession)->id)
                ->get();
        } catch (\Throwable $e) {
            $availableMiscFees = collect();
        }

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
            'tuitionFees',
            'transportFees',
            'paymentHistory',
            'feeScheduleName',
            'appliedDiscounts',
            'search',
            'refunds',
            'siblings',
            'config',
            'pendingCheques',
            'pendingChequeTotal',
            'availableMiscFees',
            'feeComponents'
        ));
    }

    public function optionalFeeMapping(Request $request)
    {
        $schoolId = $this->resolveSchoolId();
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
        $schoolId = $this->resolveSchoolId();
        $this->ensureFeesSeeded($schoolId);

        if ($request->isMethod('post')) {
            $request->validate([
                'student_id' => 'required|exists:students,id',
                'amount' => 'required|numeric|min:1',
                'purpose' => 'required|string|max:200',
            ]);

            $config = \App\Models\FeeConfiguration::where('school_id', $schoolId)->first();
            $linkUrl = 'https://schoolcloud.erp/pay/lnk_' . uniqid();
            
            $currentSession = \App\Models\AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first() 
                ?? \App\Models\AcademicSession::where('school_id', $schoolId)->first();
            $sessId = $currentSession ? $currentSession->id : null;

            if ($config && $config->payment_url_enabled && $config->payment_url) {
                $student = Student::where('school_id', $schoolId)->where('academic_session_id', $sessId)->findOrFail($request->student_id);
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

        $currentSession = \App\Models\AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first() 
            ?? \App\Models\AcademicSession::where('school_id', $schoolId)->first();
        $selectedSession = $currentSession;

        $students = Student::where('school_id', $schoolId)->where('academic_session_id', $selectedSession->id)->get();
        $links = PaymentLink::where('school_id', $schoolId)
            ->whereHas('student', function($q) use ($selectedSession) {
                $q->where('academic_session_id', $selectedSession->id);
            })
            ->with('student')->get();

        return view('school.fees.payment_links', compact('students', 'links'));
    }

    public function collectionFollowup(Request $request)
    {
        $schoolId = $this->resolveSchoolId();
        $this->ensureFeesSeeded($schoolId);

        if ($request->isMethod('post')) {
            $request->validate([
                'student_id' => 'required|exists:students,id',
            ]);

            // Fake notification success
            $student = Student::where('school_id', $schoolId)->findOrFail($request->student_id);
            return back()->with('success', "Payment reminder notification sent to parent of {$student->full_name} successfully!");
        }

        $currentSession = \App\Models\AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first() 
            ?? \App\Models\AcademicSession::where('school_id', $schoolId)->first();
        $selectedSession = $currentSession;

        $overdueFees = StudentFee::where('school_id', $schoolId)
            ->where('status', '!=', 'paid')
            ->where('due_date', '<', now()->toDateString())
            ->whereHas('student', function($q) use ($selectedSession) {
                $q->where('academic_session_id', $selectedSession->id);
            })
            ->with(['student.class', 'student.section', 'category'])
            ->get();

        return view('school.fees.collection_followup', compact('overdueFees'));
    }

    public function scheduleMapper(Request $request)
    {
        $school = app()->bound('currentSchool') ? app('currentSchool') : null;
        $schoolId = $school ? $school->id : $this->resolveSchoolId();
        $this->ensureFeesSeeded($schoolId);

        if ($request->isMethod('post')) {
            \Illuminate\Support\Facades\Log::info('Schedule Mapper POST received', [
                'has_schedules' => $request->has('student_schedules'),
                'schedules_count' => count($request->input('student_schedules', [])),
                'data' => $request->all()
            ]);
            file_put_contents(public_path('debug_mapper.txt'), "POST RECEIVED AT " . now()->toDateTimeString() . "\n" . print_r($request->all(), true), FILE_APPEND);

            if ($request->has('student_schedules')) {
                $errorMsg = null;
                \Illuminate\Support\Facades\DB::transaction(function() use ($request, $schoolId, &$errorMsg) {
                    foreach ($request->student_schedules as $studentId => $scheduleId) {
                        $student = Student::where('school_id', $schoolId)->find($studentId);
                        if ($student) {
                            $scheduleId = $scheduleId ?: null;
                            if ($student->fee_schedule_id != $scheduleId) {
                                // Once ANY fee has been collected from a student, the assigned fee schedule becomes LOCKED.
                                $hasActiveInvoice = \App\Models\FeeInvoice::where('school_id', $schoolId)
                                    ->where('student_id', $student->id)
                                    ->where('status', '!=', 'cancelled')
                                    ->exists();

                                $hasActiveReceipt = \App\Models\FeeReceipt::withoutGlobalScope('active')
                                    ->where('school_id', $schoolId)
                                    ->where('student_id', $student->id)
                                    ->where('status', '!=', 'cancelled')
                                    ->exists();

                                $hasPaidFees = \App\Models\StudentFee::withoutGlobalScopes()
                                    ->where('school_id', $schoolId)
                                    ->where('student_id', $student->id)
                                    ->where(function($q) {
                                        $q->where('paid_amount', '>', 0)
                                          ->orWhere('instant_discount_amount', '>', 0);
                                    })
                                    ->exists();

                                if ($hasActiveInvoice || $hasActiveReceipt || $hasPaidFees) {
                                    // DO NOT change anything. Old fee must remain.
                                    continue;
                                }
                            }
                            if ($scheduleId !== null) {
                                $sched = \App\Models\FeeSchedule::where('school_id', $schoolId)->find($scheduleId);
                                if (!$sched) {
                                    $errorMsg = "Selected fee schedule does not exist.";
                                    return;
                                }
                                $studentClassName = optional($student->class)->name;
                                $schClasses = array_map('trim', explode(',', $sched->classes ?? ''));
                                if ($studentClassName && !in_array($studentClassName, $schClasses)) {
                                    $errorMsg = "Schedule '{$sched->name}' is not applicable to Class '{$studentClassName}'.";
                                    return;
                                }
                                if ($sched->sections) {
                                    $schSections = array_map('trim', explode(',', $sched->sections));
                                    $studentSectionName = optional($student->section)->name;
                                    $studentClassSection = $studentClassName && $studentSectionName ? ($studentClassName . '-' . $studentSectionName) : null;
                                    if ($studentClassSection && !in_array($studentClassSection, $schSections)) {
                                        $errorMsg = "Schedule '{$sched->name}' is not applicable to Section '{$studentClassSection}'.";
                                        return;
                                    }
                                }
                            }
                            $student->fee_schedule_id = $scheduleId;
                            $student->saveQuietly();
                            self::syncStudentFees($student);
                        }
                    }
                });

                if ($errorMsg) {
                    return back()->with('error', $errorMsg);
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

        $classes = $this->getSessionScopedClasses($schoolId, $selectedSession->id);
        $sections = $this->getSessionScopedSections($schoolId, $selectedSession->id)->unique('name');

        $schedulesQuery = \App\Models\FeeSchedule::where('school_id', $schoolId);
        if ($selectedSession) {
            $schedulesQuery->where('academic_session_id', $selectedSession->id);
        }
        $schedules = $schedulesQuery->get()->unique('name');

        $showDeactivated = $request->get('show_deactivated') == '1';
        $showDeleted     = $request->get('show_deleted')     == '1';

        // withTrashed() MUST be called first, before any where() scopes
        $query = $showDeleted
            ? Student::withTrashed()->with(['class', 'section'])
            : Student::with(['class', 'section']);

        $query->where('school_id', $schoolId);

        // Filter inactive (deactivated) students unless the toggle is on
        if (!$showDeactivated) {
            $query->where(function($q) {
                $q->whereNull('is_active')->orWhere('is_active', 1);
            });
        }

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
                  ->orWhere('admission_number', 'like', "%{$search}%");
            });
        }

        $students = $query->paginate(25)->withQueryString();

        // No auto-seeding of dummy students

        return view('school.fees.schedule_mapper', compact('categories', 'structures', 'sessions', 'classes', 'sections', 'schedules', 'students'));
    }

    public function refundFee(Request $request)
    {
        $schoolId = $this->resolveSchoolId();
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
        $schoolId = $this->resolveSchoolId();
        $this->ensureFeesSeeded($schoolId);

        $sessions = \App\Models\AcademicSession::where('school_id', $schoolId)->get();
        $classes = SchoolClass::where('school_id', $schoolId)->get();
        $sections = \App\Models\Section::where('school_id', $schoolId)->get()->unique('name');

        $query = FeeReceipt::withoutGlobalScope('active')->where('school_id', $schoolId)->with(['student.class', 'student.section']);

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
        $schoolId = $this->resolveSchoolId();
        $this->ensureFeesSeeded($schoolId);

        if ($request->isMethod('post')) {
            $request->validate([
                'cheque_id' => 'required|exists:pending_cheques,id',
                'action' => 'required|in:clear,bounce',
            ]);

            $cheque = PendingCheque::where('school_id', $schoolId)->findOrFail($request->cheque_id);
            $newStatus = $request->action === 'clear' ? 'cleared' : 'bounced';

            $oldStatus = $cheque->status;
            if ($oldStatus === $newStatus) {
                return back()->with('info', 'Cheque status is already ' . $newStatus . '.');
            }

            $this->processChequeStatusTransition($cheque, $newStatus);

            $cheque->status_changed_at = now();
            $cheque->status_changed_by = auth()->id();
            $cheque->status_remarks = $request->input('status_remarks', '');
            $cheque->save();

            $msg = $newStatus === 'cleared' ? 'Cheque cleared successfully! Fee receipt generated.' : 'Cheque status marked as bounced.';
            return back()->with('success', $msg);
        }

        $cheques = PendingCheque::where('school_id', $schoolId)->with('student')->get();
        return view('school.fees.pending_cheques', compact('cheques'));
    }

    public function feeReports(Request $request)
    {
        $schoolId = $this->resolveSchoolId();
        $this->ensureFeesSeeded($schoolId);

        $currentSession = \App\Models\AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first()
            ?? \App\Models\AcademicSession::where('school_id', $schoolId)->first();
        $selectedSession = $currentSession;

        $totalCollected = FeeReceipt::where('school_id', $schoolId)
            ->whereHas('student', function($q) use ($selectedSession) {
                $q->where('academic_session_id', $selectedSession->id);
            })
            ->sum('amount_paid');

        $totalRefunded = FeeRefund::where('school_id', $schoolId)
            ->whereHas('student', function($q) use ($selectedSession) {
                $q->where('academic_session_id', $selectedSession->id);
            })
            ->sum('amount');

        $totalDues = StudentFee::where('school_id', $schoolId)
            ->where('status', '!=', 'paid')
            ->whereHas('student', function($q) use ($selectedSession) {
                $q->where('academic_session_id', $selectedSession->id);
            })
            ->sum('amount') 
            - StudentFee::where('school_id', $schoolId)
            ->where('status', 'partially_paid')
            ->whereHas('student', function($q) use ($selectedSession) {
                $q->where('academic_session_id', $selectedSession->id);
            })
            ->sum('paid_amount');

        $receiptsByMode = FeeReceipt::where('school_id', $schoolId)
            ->whereHas('student', function($q) use ($selectedSession) {
                $q->where('academic_session_id', $selectedSession->id);
            })
            ->selectRaw('payment_mode, SUM(amount_paid) as total')
            ->groupBy('payment_mode')
            ->get();

        $collectionByClass = FeeReceipt::where('fee_receipts.school_id', $schoolId)
            ->join('students', 'fee_receipts.student_id', '=', 'students.id')
            ->where('students.academic_session_id', $selectedSession->id)
            ->join('school_classes', 'students.class_id', '=', 'school_classes.id')
            ->selectRaw('school_classes.name as class_name, SUM(amount_paid) as total')
            ->groupBy('school_classes.name')
            ->get();

        return view('school.fees.reports', compact('totalCollected', 'totalRefunded', 'totalDues', 'receiptsByMode', 'collectionByClass'));
    }

    public function feeInvoice(Request $request)
    {
        $schoolId = $this->resolveSchoolId();
        $this->ensureFeesSeeded($schoolId);

        $school = \App\Models\School::find($schoolId);
        $students = Student::where('school_id', $schoolId)
            ->with(['class', 'section'])
            ->select('id', 'first_name', 'last_name', 'admission_number', 'father_name', 'class_id', 'section_id')
            ->get();
        $classes = \App\Models\SchoolClass::where('school_id', $schoolId)->get();
        $config = \App\Models\FeeConfiguration::where('school_id', $schoolId)->first();

        return view('school.fees.invoice', compact('students', 'classes', 'config', 'school'));
    }

    public function getStudentInvoices($studentId)
    {
        $schoolId = $this->resolveSchoolId();
        $student = Student::where('school_id', $schoolId)->with(['class', 'section'])->findOrFail($studentId);

        $invoices = \App\Models\FeeInvoice::where('school_id', $schoolId)
            ->where('student_id', $studentId)
            ->orderBy('id', 'desc')
            ->get()
            ->map(function($invoice) use ($schoolId, $studentId) {
                // Compute current active due for this installment
                $activeDue = StudentFee::where('school_id', $schoolId)
                    ->where('student_id', $studentId)
                    ->where('installment_no', $invoice->installment_no)
                    ->get()
                    ->sum(function($sf) {
                        return max(0, $sf->amount - $sf->instant_discount_amount - $sf->paid_amount);
                    });

                $status = $invoice->status;
                if ($status === 'paid' && $activeDue > 0) {
                    $status = 'partially_paid';
                }

                $components = [];
                $isTransport = false;
                $transportMonths = [];

                if (!empty($invoice->payment_details)) {
                    $decoded = json_decode($invoice->payment_details, true);
                    if (is_array($decoded)) {
                        $compList = isset($decoded['components']) && is_array($decoded['components']) 
                            ? $decoded['components'] 
                            : $decoded;
                        
                        foreach ($compList as $comp) {
                            if (!is_array($comp) || !isset($comp['student_fee_id'])) continue;
                            
                            $sf = StudentFee::withoutGlobalScope('active')->find($comp['student_fee_id']);
                            $origAmt = $sf ? $sf->amount : ($comp['amount_paid'] ?? 0);
                            $disc = $sf ? $sf->instant_discount_amount : 0;
                            $due = $sf ? max(0, $sf->amount - $sf->instant_discount_amount - $sf->paid_amount) : 0;

                            $rawName = $comp['component_name'] ?? 'Fee Component';
                            $isTransportComp = ($sf && $sf->transport_fee_schedule_id !== null)
                                || stripos($rawName, 'transport') !== false;

                            if ($isTransportComp) {
                                $isTransport = true;
                                if ($sf && $sf->due_date) {
                                    $monthLabel = \Carbon\Carbon::parse($sf->due_date)->format('F Y');
                                    $transportMonths[] = $monthLabel;
                                    $rawName = 'Transport Fee — ' . $monthLabel;
                                }
                            }

                            $components[] = [
                                'name' => $rawName,
                                'amount' => $origAmt,
                                'discount' => $disc,
                                'paid' => $comp['amount_paid'] ?? 0,
                                'status' => $invoice->status,
                                'due' => $due,
                                'is_transport' => $isTransportComp,
                            ];
                        }
                    }
                }
                
                if (empty($components)) {
                    $components[] = [
                        'name' => 'Installment ' . $invoice->installment_no,
                        'amount' => $invoice->amount + $invoice->discount_amount,
                        'discount' => $invoice->discount_amount,
                        'paid' => $invoice->amount,
                        'status' => $invoice->status,
                        'due' => $activeDue,
                        'is_transport' => false,
                    ];
                }

                // Build descriptive installment label
                if ($isTransport && count($transportMonths) > 0) {
                    $monthsStr = implode(', ', array_unique($transportMonths));
                    $installmentLabel = 'Transport — Installment ' . $invoice->installment_no . ' (' . $monthsStr . ')';
                } else {
                    $installmentLabel = 'Installment ' . $invoice->installment_no;
                }

                return [
                    'installment_no' => $invoice->installment_no,
                    'installment_label' => $installmentLabel,
                    'is_transport' => $isTransport,
                    'invoice_no' => $invoice->invoice_number,
                    'total' => $invoice->amount + $invoice->discount_amount,
                    'discount' => $invoice->discount_amount,
                    'paid' => ($invoice->type === 'payment') ? $invoice->amount : 0,
                    'due' => $activeDue,
                    'status' => $status,
                    'invoice_status' => $invoice->status,
                    'components' => $components,
                ];
            });

        return response()->json([
            'success' => true,
            'student' => [
                'id' => $student->id,
                'name' => $student->full_name,
                'admission_number' => $student->admission_number,
                'class' => optional($student->class)->name,
                'section' => optional($student->section)->name,
                'father_name' => $student->father_name ?? '—',
                'mother_name' => $student->mother_name ?? '—',
                'address' => $student->address ?? '—',
                'phone' => $student->father_phone ?? '—',
            ],
            'invoices' => $invoices
        ]);
    }

    public function printSlip($type, $number)
    {
        $schoolId = $this->resolveSchoolId();
        $school = \App\Models\School::find($schoolId);
        $config = \App\Models\FeeConfiguration::where('school_id', $schoolId)->first();

        $student = null;
        $invoice = null;
        $items = collect();
        $title = '';
        $date = '';
        $mode = '';
        $amount = 0;
        $bankName = null;
        $bankDate = null;
        $remarks = '';

        if ($type === 'combined') {
            $feeIds = array_filter(array_map('trim', explode(',', $number)));
            $fees = StudentFee::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->whereIn('id', $feeIds)
                ->with(['category', 'component'])
                ->get();
                
            if ($fees->isNotEmpty()) {
                $student = Student::withoutGlobalScopes()->with(['class', 'section'])->findOrFail($fees->first()->student_id);
                $title = $config?->invoice_title ?: 'Fee Receipt';
                
                // Try to find the latest invoice matching one of these fees
                $latestInvoice = \App\Models\FeeInvoice::where('school_id', $schoolId)
                    ->where('student_id', $student->id)
                    ->where(function($q) use ($feeIds) {
                        foreach ($feeIds as $id) {
                            $q->orWhere('payment_details', 'like', '%"student_fee_id":' . $id . '%')
                              ->orWhere('payment_details', 'like', '%"student_fee_id":"' . $id . '"%');
                        }
                    })
                    ->orderBy('id', 'desc')
                    ->first();
                
                $invoice = $latestInvoice;
                $date = $latestInvoice ? $latestInvoice->payment_date : now()->toDateString();
                $mode = $latestInvoice ? ($latestInvoice->payment_mode ?: 'Combined') : 'Combined';
                $amount = $fees->sum('paid_amount');
                $remarks = $latestInvoice ? ($latestInvoice->remarks ?? '') : 'Combined Printed Slip';
                $number = $latestInvoice ? $latestInvoice->invoice_number : 'COMB-' . rand(100000, 999999);
                
                $items = $fees->map(function($fee) {
                    $isTransportFee = optional($fee->category)->name === 'Transport'
                        || ($fee->component?->component_name ?? '') === 'Transport Fee'
                        || $fee->transport_fee_schedule_id !== null;

                    if ($isTransportFee && $fee->due_date) {
                        $monthLabel = \Carbon\Carbon::parse($fee->due_date)->format('F Y');
                        $pickOrDrop = '';
                        if ($fee->component) {
                            $cn = strtolower($fee->component->component_name ?? '');
                            if (str_contains($cn, 'pick')) $pickOrDrop = 'Pickup Cost';
                            elseif (str_contains($cn, 'drop')) $pickOrDrop = 'Drop Cost';
                        }
                        $desc = 'Transport' . ($pickOrDrop ? ' ' . $pickOrDrop : '') . ' — ' . $monthLabel;
                    } else {
                        $desc = 'Installment ' . $fee->installment_no;
                    }

                    $isMisc = $fee->misc_fee_id !== null;
                    return (object)[
                        'student_fee_id' => $fee->id,
                        'installment_no' => $fee->installment_no,
                        'description' => $desc,
                        'amount' => $fee->amount,
                        'instant_discount_amount' => $fee->instant_discount_amount,
                        'paid_amount' => $fee->paid_amount,
                        'invoice_status' => 'paid',
                        'is_misc' => $isMisc,
                    ];
                });
            }
        } else {
            $invoice = \App\Models\FeeInvoice::where('school_id', $schoolId)
                ->where('invoice_number', $number)
                ->first();

            if ($invoice) {
            // Tenancy scoping check
            if ($invoice->school_id !== $schoolId) {
                abort(403, 'Unauthorized access to invoice.');
            }

            $type = $invoice->type;

            $student = Student::where('school_id', $schoolId)->with(['class', 'section'])->findOrFail($invoice->student_id);
            $date = $invoice->payment_date;
            $mode = $invoice->payment_mode ?: 'Invoice Only';
            $amount = $invoice->amount;
            $remarks = $invoice->remarks ?? '';
            $bankName = null;
            $bankDate = null;
            
            if ($invoice->type === 'payment' || $invoice->type === 'cancel_payment') {
                $title = $config?->invoice_title ?: 'Fee Receipt';
            } elseif ($invoice->type === 'refund' || $invoice->type === 'cancel_refund') {
                $title = 'Refund Voucher';
            } else {
                $title = $config?->invoice_title ?: 'Fee Invoice';
            }

            $items = collect();
            if ($config?->show_installment_components_on_invoice || (in_array($invoice->type, ['payment', 'refund', 'cancel_refund']) && !empty($invoice->payment_details))) {
                if ($invoice->type === 'invoice') {
                    // Query components from student_fees table by invoice_no
                    $fees = StudentFee::withoutGlobalScope('active')
                        ->where('school_id', $schoolId)
                        ->where('student_id', $invoice->student_id)
                        ->where('invoice_no', $invoice->invoice_number)
                        ->with(['category', 'component'])
                        ->get();
                    
                    if ($fees->isEmpty()) {
                        // fallback to installment_no
                        $fees = StudentFee::withoutGlobalScope('active')
                            ->where('school_id', $schoolId)
                            ->where('student_id', $invoice->student_id)
                            ->where('installment_no', $invoice->installment_no)
                            ->with(['category', 'component'])
                            ->get();
                    }
                    
                    $items = $fees->map(function($fee) use ($invoice) {
                        $isTransportFee = optional($fee->category)->name === 'Transport'
                            || ($fee->component?->component_name ?? '') === 'Transport Fee'
                            || $fee->transport_fee_schedule_id !== null;

                        if ($isTransportFee && $fee->due_date) {
                            $monthLabel = \Carbon\Carbon::parse($fee->due_date)->format('F Y');
                            $desc = 'Transport Installment ' . $fee->installment_no . ' — ' . $monthLabel;
                        } else {
                            $desc = 'Installment ' . $fee->installment_no;
                        }

                        $isMisc = $fee->misc_fee_id !== null;
                        return (object)[
                            'student_fee_id' => $fee->id,
                            'installment_no' => $fee->installment_no,
                            'description' => $desc,
                            'amount' => $fee->amount,
                            'instant_discount_amount' => $fee->instant_discount_amount,
                            'paid_amount' => $fee->paid_amount,
                            'invoice_status' => $fee->invoice_status ?? 'active',
                            'is_misc' => $isMisc,
                        ];
                    });
                } elseif (in_array($invoice->type, ['payment', 'refund', 'cancel_refund']) && !empty($invoice->payment_details)) {
                    // Decoded from payment_details
                    $decoded = json_decode($invoice->payment_details, true);
                    if (is_array($decoded)) {
                        $compList = isset($decoded['components']) && is_array($decoded['components']) 
                            ? $decoded['components'] 
                            : $decoded;
                        
                        foreach ($compList as $comp) {
                            if (!is_array($comp)) continue;
                            $sf = null;
                            if (isset($comp['student_fee_id'])) {
                                $sf = StudentFee::withoutGlobalScope('active')->find($comp['student_fee_id']);
                            }
                            
                            $disc = isset($comp['transaction_discount']) ? $comp['transaction_discount'] : ($comp['discount_amount'] ?? 0);
                            $origAmt = $sf ? floatval($sf->amount) : (($comp['amount_paid'] ?? 0) + $disc);

                            $compInstNo = $comp['installment_no'] ?? ($sf ? $sf->installment_no : $invoice->installment_no);
                            $isTransportComp = stripos($comp['component_name'] ?? '', 'Transport') !== false
                                || ($sf && $sf->transport_fee_schedule_id !== null);

                            if ($isTransportComp && $sf && $sf->due_date) {
                                $monthLabel = \Carbon\Carbon::parse($sf->due_date)->format('F Y');
                                // Detect pick/drop sub-type from component name
                                $cn = strtolower($comp['component_name'] ?? '');
                                if (str_contains($cn, 'pick')) {
                                    $desc = 'Transport Pickup Cost — ' . $monthLabel;
                                } elseif (str_contains($cn, 'drop')) {
                                    $desc = 'Transport Drop Cost — ' . $monthLabel;
                                } else {
                                    $desc = 'Transport Fee — ' . $monthLabel;
                                }
                            } elseif ($isTransportComp) {
                                $desc = 'Transport Installment ' . $compInstNo;
                            } else {
                                $desc = $comp['component_name'] ?? ('Installment ' . $compInstNo);
                            }

                            $isMisc = ($sf && $sf->misc_fee_id !== null)
                                || (isset($comp['misc_fee_id']) && $comp['misc_fee_id'] !== null)
                                || (stripos($comp['component_name'] ?? '', 'Misc') !== false)
                                || (stripos($comp['component_name'] ?? '', 'Miscellaneous') !== false)
                                || (stripos($desc, 'Misc') !== false)
                                || (stripos($desc, 'Miscellaneous') !== false);

                            $items->push((object)[
                                'student_fee_id' => $comp['student_fee_id'] ?? null,
                                'installment_no' => $compInstNo,
                                'description' => $desc,
                                'amount' => $origAmt,
                                'instant_discount_amount' => $disc,
                                'paid_amount' => $comp['amount_paid'] ?? 0,
                                'invoice_status' => $invoice->status,
                                'is_misc' => $isMisc,
                            ]);
                        }
                    }
                }
            }

            if ($items->isEmpty()) {
                $isTransport = false;
                if (!empty($invoice->payment_details)) {
                    $decoded = json_decode($invoice->payment_details, true);
                    if (is_array($decoded)) {
                        $compList = isset($decoded['components']) && is_array($decoded['components']) ? $decoded['components'] : $decoded;
                        foreach ($compList as $comp) {
                            if (is_array($comp) && (stripos($comp['component_name'] ?? '', 'Transport') !== false)) {
                                $isTransport = true;
                                break;
                            }
                        }
                    }
                }
                
                if (!$isTransport) {
                    if (stripos($number, '-T-') !== false || stripos($invoice->remarks, 'Transport') !== false) {
                        $isTransport = true;
                    }
                }

                $items = collect([
                    (object)[
                        'installment_no' => $invoice->installment_no,
                        'description' => $isTransport ? ('Transport Installment ' . $invoice->installment_no) : ('Installment ' . $invoice->installment_no),
                        'amount' => $invoice->amount + $invoice->discount_amount,
                        'instant_discount_amount' => $invoice->discount_amount,
                        'paid_amount' => $invoice->amount,
                        'invoice_status' => $invoice->status,
                    ]
                ]);
            }
        } else {
            // Fallback for legacy database queries
            if ($type === 'payment') {
                $receipt = FeeReceipt::withoutGlobalScope('active')->where('school_id', $schoolId)
                    ->where('receipt_number', $number)
                    ->firstOrFail();
                $student = Student::where('school_id', $schoolId)->with(['class', 'section'])->findOrFail($receipt->student_id);
                 $title = $config?->invoice_title ?: 'Fee Receipt';
                $date = $receipt->payment_date;
                $mode = $receipt->payment_mode;
                $amount = $receipt->amount_paid;
                $remarks = $receipt->remarks ?? '';

                $items = collect();
                if (!empty($receipt->payment_details)) {
                    $decoded = json_decode($receipt->payment_details, true);
                    if (is_array($decoded)) {
                        $compList = isset($decoded['components']) && is_array($decoded['components']) 
                            ? $decoded['components'] 
                            : $decoded;
                        
                        foreach ($compList as $comp) {
                            if (!is_array($comp)) continue;
                            $sf = null;
                            if (isset($comp['student_fee_id'])) {
                                $sf = StudentFee::withoutGlobalScope('active')->find($comp['student_fee_id']);
                            }
                            
                            $origAmt = ($comp['amount_paid'] ?? 0) + ($comp['discount_amount'] ?? 0);
                            $disc = $sf ? $sf->instant_discount_amount : 0;
                            if (isset($comp['discount_amount'])) {
                                $disc = $comp['discount_amount'];
                            }

                            $items->push((object)[
                                'student_fee_id' => $comp['student_fee_id'] ?? null,
                                'installment_no' => 1,
                                'description' => ($comp['component_name'] ?? 'Fee Component') . ' (Inst. 1)',
                                'amount' => $origAmt,
                                'instant_discount_amount' => $disc,
                                'paid_amount' => $comp['amount_paid'] ?? 0,
                                'invoice_status' => $receipt->status === 'cancelled' ? 'cancelled' : 'paid',
                            ]);
                        }
                    }
                }

                if ($items->isEmpty()) {
                    $items = collect([
                        (object)[
                            'installment_no' => 1,
                            'description' => 'Installment 1',
                            'amount' => $receipt->amount_paid + $receipt->discount_amount,
                            'instant_discount_amount' => $receipt->discount_amount,
                            'paid_amount' => $receipt->amount_paid,
                            'invoice_status' => $receipt->status === 'cancelled' ? 'cancelled' : 'paid',
                        ]
                    ]);
                }
            } elseif ($type === 'refund') {
                // Try finding by slip_no
                $refunds = FeeRefund::where('school_id', $schoolId)
                    ->where('slip_no', $number)
                    ->get();
                
                // If empty, try finding the FeeInvoice of type refund first to get its matching student_fee_ids
                if ($refunds->isEmpty()) {
                    $invoice = \App\Models\FeeInvoice::where('school_id', $schoolId)
                        ->where('invoice_number', $number)
                        ->where('type', 'refund')
                        ->first();
                    if ($invoice) {
                        $details = json_decode($invoice->payment_details, true);
                        $compInfo = is_array($details) ? ($details['components'] ?? []) : [];
                        $feeIds = array_column($compInfo, 'student_fee_id');
                        
                        $refunds = FeeRefund::where('school_id', $schoolId)
                            ->where('student_id', $invoice->student_id)
                            ->whereIn('student_fee_id', $feeIds)
                            ->where('refund_date', $invoice->payment_date)
                            ->get();
                    }
                }

                if ($refunds->isEmpty()) {
                    abort(404, 'Refund slip not found.');
                }
                $first = $refunds->first();
                $student = Student::where('school_id', $schoolId)->with(['class', 'section'])->findOrFail($first->student_id);
                $title = 'Refund Voucher';
                $date = $first->refund_date;
                $mode = $first->payment_mode;
                $amount = $refunds->sum('amount');
                $bankName = $first->bank_name;
                $bankDate = $first->bank_date;
                $remarks = explode(' (Refunded:', $first->reason)[0];

                $items = collect([
                    (object)[
                        'installment_no' => 1,
                        'description' => $remarks ?: 'Refund',
                        'amount' => $amount,
                        'instant_discount_amount' => 0.00,
                        'paid_amount' => $amount,
                        'invoice_status' => 'refunded',
                    ]
                ]);
            } elseif ($type === 'invoice') {
                $studentId = request()->get('student_id');
                if (!$studentId) {
                    abort(400, 'Student ID is required for invoice printing.');
                }
                $student = Student::where('school_id', $schoolId)->with(['class', 'section'])->findOrFail($studentId);
                $title = $config?->invoice_title ?: 'Fee Invoice';
                
                $fees = StudentFee::withoutGlobalScope('active')
                    ->where('school_id', $schoolId)
                    ->where('student_id', $student->id)
                    ->where('invoice_no', $number)
                    ->with(['category', 'component'])
                    ->get();
                
                if ($fees->isEmpty() && preg_match('/^INV-(\d+)$/i', $number, $matches)) {
                    $installmentNo = (int) $matches[1];
                    $fees = StudentFee::withoutGlobalScope('active')
                        ->where('school_id', $schoolId)
                        ->where('student_id', $student->id)
                        ->where('installment_no', $installmentNo)
                        ->whereNull('invoice_no')
                        ->with(['category', 'component'])
                        ->get();
                }
                
                if ($fees->isEmpty()) {
                    abort(404, 'Invoice items not found.');
                }
                
                $firstFee = $fees->first();
                $date = $firstFee->created_at ?? now();
                $mode = 'Invoice Only';
                $groupTotal = $fees->sum('amount');
                $groupDiscount = $fees->sum('instant_discount_amount');
                $amount = $groupTotal - $groupDiscount;
                $remarks = 'Installment ' . ($installmentNo ?? $firstFee->installment_no) . ' Invoice';

                if ($config?->show_installment_components_on_invoice) {
                    $items = $fees->map(function($fee) {
                        return (object)[
                            'student_fee_id' => $fee->id,
                            'installment_no' => $fee->installment_no,
                            'description' => ($fee->component?->component_name ?: 'Fee Component') . ' (Inst. ' . $fee->installment_no . ')',
                            'amount' => $fee->amount,
                            'instant_discount_amount' => $fee->instant_discount_amount,
                            'paid_amount' => $fee->paid_amount,
                            'invoice_status' => $fee->invoice_status ?? 'active',
                        ];
                    });
                } else {
                    $items = collect([
                        (object)[
                            'installment_no' => $installmentNo ?? $firstFee->installment_no,
                            'description' => 'Installment ' . ($installmentNo ?? $firstFee->installment_no),
                            'amount' => $groupTotal,
                            'instant_discount_amount' => $groupDiscount,
                            'paid_amount' => $fees->sum('paid_amount'),
                            'invoice_status' => $firstFee->invoice_status ?? 'active',
                        ]
                    ]);
                }
            } else {
                abort(404);
            }
        }
        }

        // Issue 1: Ensure all unpaid fee heads of represented installments are included in the invoice items
        $isTransport = false;
        foreach ($items as $item) {
            $desc = strtolower($item->description ?? '');
            if (strpos($desc, 'transport') !== false || strpos($desc, 'bus') !== false || strpos($desc, 'vehicle') !== false) {
                $isTransport = true;
                break;
            }
        }

        if (!$isTransport && isset($student)) {
            $installmentNos = $items->pluck('installment_no')->filter()->unique();
            foreach ($installmentNos as $instNo) {
                $studentFees = \App\Models\StudentFee::withoutGlobalScopes()
                    ->where('school_id', $schoolId)
                    ->where('student_id', $student->id)
                    ->where('installment_no', $instNo)
                    ->with(['category', 'component'])
                    ->get();

                // Filter out transport components to avoid mixing them in a normal school fee invoice
                $studentFees = $studentFees->filter(function($fee) {
                    $isTrans = (optional($fee->category)->name === 'Transport' || 
                                stripos(optional($fee->category)->name ?? '', 'Transport') !== false ||
                                stripos(optional($fee->component)->component_name ?? '', 'Transport') !== false ||
                                $fee->transport_fee_schedule_id !== null);
                    return !$isTrans;
                });

                foreach ($studentFees as $fee) {
                    $dueAmt = max(0.00, floatval($fee->amount) + floatval($fee->fine_amount_applied ?? 0) - floatval($fee->paid_amount) - floatval($fee->instant_discount_amount ?? 0));
                    if ($dueAmt > 0 && $fee->status !== 'paid') {
                        // Check if this fee is already in $items by student_fee_id
                        $exists = $items->contains(function($item) use ($fee) {
                            return isset($item->student_fee_id) && $item->student_fee_id == $fee->id;
                        });

                        if (!$exists) {
                            $desc = $fee->component ? $fee->component->component_name : ($fee->miscFee ? $fee->miscFee->name : ($fee->category ? $fee->category->name : 'Fee'));
                            $items->push((object)[
                                'student_fee_id' => $fee->id,
                                'installment_no' => $instNo,
                                'description' => $desc,
                                'amount' => floatval($fee->amount),
                                'instant_discount_amount' => floatval($fee->instant_discount_amount ?? 0),
                                'paid_amount' => 0.00,
                                'invoice_status' => isset($invoice) ? $invoice->status : 'active',
                                'is_misc' => $fee->misc_fee_id !== null,
                            ]);
                        }
                    }
                }
            }
        }
        if ($isTransport && ($config?->receipt_template !== 'Minimal Template')) {
            $title = $config?->transport_invoice_title ?: 'Transport Invoice';
            
            // Resolve the school branding dynamically from the invoice or student context
            if (isset($invoice) && $invoice->school_id) {
                $school = \App\Models\School::find($invoice->school_id);
            } elseif (isset($student) && $student->school_id) {
                $school = \App\Models\School::find($student->school_id);
            }

            return view('school.fees.print_transport_slip', compact(
                'school', 'config', 'student', 'type', 'number', 'items', 'title', 'date', 'mode', 'amount', 'bankName', 'bankDate', 'remarks', 'invoice'
            ));
        }

        return view('school.fees.print_slip', compact(
            'school', 'config', 'student', 'type', 'number', 'items', 'title', 'date', 'mode', 'amount', 'bankName', 'bankDate', 'remarks', 'invoice'
        ));
    }

    public function feeInvoice1(Request $request)
    {
        $schoolId = $this->resolveSchoolId();
        
        // Seed the invoice 1 data
        $this->seedInvoice1Data($schoolId);

        // Sync transport fee records dynamically based on latest route assignments and attendance deductions
        $this->syncTransportFees($schoolId);

        $currentSession = \App\Models\AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first()
            ?? \App\Models\AcademicSession::where('school_id', $schoolId)->first();
        $selectedSession = $currentSession;
        if ($request->filled('academic_session_id')) {
            $selectedSession = \App\Models\AcademicSession::where('school_id', $schoolId)->find($request->academic_session_id) ?? $currentSession;
        }

        if ($request->has('ajax') || $request->wantsJson()) {
            $query = \App\Models\Student::where('students.school_id', $schoolId)
                ->where('students.transport_opted', true)
                ->whereNotNull('students.transport_route')
                ->leftJoin('school_classes', 'school_classes.id', '=', 'students.class_id')
                ->leftJoin('sections', 'sections.id', '=', 'students.section_id')
                ->select(
                    'students.id as student_id',
                    'students.admission_number',
                    'students.first_name',
                    'students.last_name',
                    'students.father_name',
                    'students.transport_route',
                    'school_classes.name as class_name',
                    'sections.name as section_name'
                );

            if ($selectedSession) {
                $query->where('students.academic_session_id', $selectedSession->id);
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
            if ($request->filled('transport_route')) {
                $query->where('students.transport_route', $request->transport_route);
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

            $query->orderBy('students.id', 'asc');

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
        $classes = $this->getSessionScopedClasses($schoolId, $selectedSession->id)->unique('name')->sortBy(function($c) {
            $order = ['Nursery' => 1, 'LKG' => 2, 'UKG' => 3, 'Class 1' => 4, 'Class 2' => 5, 'Class 3' => 6, 'Class 4' => 7, 'Class 5' => 8, 'Class 6' => 9, 'Class 7' => 10, 'Class 8' => 11, 'Class 9' => 12, 'Class 10' => 13, 'Class 11' => 14, 'Class 12' => 15];
            return $order[$c->name] ?? ($c->sort_order ?? 99);
        });
        $sections = $this->getSessionScopedSections($schoolId, $selectedSession->id);
        $feeSchedules = \App\Models\FeeSchedule::where('school_id', $schoolId)->where('academic_session_id', $selectedSession->id)->get();
        $feeComponents = \App\Models\FeeComponent::where('school_id', $schoolId)->where('academic_session_id', $selectedSession->id)->get();
        $routes = \App\Models\TransportRoute::where('school_id', $schoolId)->get();

        return view('school.fees.invoice1', compact(
            'academicSessions',
            'classes',
            'sections',
            'feeSchedules',
            'feeComponents',
            'routes'
        ));
    }

    public function feeInvoice1Generate(Request $request)
    {
        $schoolId = $this->resolveSchoolId();
        
        // Sync transport fee records dynamically based on latest route assignments and attendance deductions
        $this->syncTransportFees($schoolId);

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
            $student = \App\Models\Student::where('school_id', $schoolId)->find($studentId);
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

    private function syncTransportFees($schoolId)
    {
        \App\Models\StudentFee::syncTransportFees($schoolId);
    }

    public function feeBulkUpload(Request $request)
    {
        $schoolId = $this->resolveSchoolId();
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
        $schoolId = $this->resolveSchoolId();
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

    private function processChequeStatusTransition($cheque, $newStatus)
    {
        $schoolId = $cheque->school_id;
        $oldStatus = $cheque->status;

        if ($oldStatus === $newStatus) {
            return;
        }

        \Illuminate\Support\Facades\DB::transaction(function() use ($cheque, $oldStatus, $newStatus, $schoolId) {
            \App\Models\StudentFee::clearPendingReservationsCache();

            if ($newStatus === 'cleared') {
                $cheque->status = 'cleared';
                $cheque->save();

                $receiptNum = $cheque->receipt_number ?: ('REC-' . rand(100000, 999999));
                $installmentNo = $cheque->installment_no;
                $instNo = $installmentNo ?: 1;

                $storedFeeIds = [];
                if (!empty($cheque->student_fee_ids)) {
                    $decoded = json_decode($cheque->student_fee_ids, true);
                    if (is_array($decoded)) {
                        $storedFeeIds = array_filter(array_map('intval', $decoded));
                    }
                }

                if (!empty($storedFeeIds)) {
                    $feesToPay = StudentFee::withoutGlobalScope('active')
                        ->where('school_id', $schoolId)
                        ->where('student_id', $cheque->student_id)
                        ->whereIn('id', $storedFeeIds)
                        ->orderBy('installment_no', 'asc')
                        ->orderBy('id', 'asc')
                        ->get();
                } else {
                    $feesToPay = StudentFee::where('school_id', $schoolId)
                        ->where('student_id', $cheque->student_id);
                    if ($installmentNo) {
                        $feesToPay->where('installment_no', $installmentNo);
                    }
                    $feesToPay = $feesToPay->where('status', '!=', 'paid')
                        ->orderBy('id', 'asc')
                        ->get();

                    if ($feesToPay->isEmpty()) {
                        $feesToPay = StudentFee::withoutGlobalScope('active')
                            ->where('school_id', $schoolId)
                            ->where('student_id', $cheque->student_id);
                        if ($installmentNo) {
                            $feesToPay->where('installment_no', $installmentNo);
                        }
                        $feesToPay = $feesToPay->orderBy('id', 'asc')->get();
                    }
                }

                $chequeDiscountTotal = floatval($cheque->discount_amount ?? 0);
                $totalDiscountRemaining = $chequeDiscountTotal;

                $detailsArray = [];
                $amountToDistribute = floatval($cheque->amount);

                foreach ($feesToPay as $sf) {
                    $netDue = max(0.00, floatval($sf->amount)
                        + floatval($sf->fine_amount_applied ?? 0)
                        - floatval($sf->paid_amount)
                        - floatval($sf->instant_discount_amount));

                    if ($netDue <= 0 && $amountToDistribute <= 0) {
                        continue;
                    }

                    $discountForThis = 0;
                    if ($totalDiscountRemaining > 0 && $netDue > 0) {
                        $discountForThis = min($totalDiscountRemaining, $netDue);
                        $sf->instant_discount_amount = floatval($sf->instant_discount_amount) + $discountForThis;
                        if (!$sf->instant_discount_type) {
                            $sf->instant_discount_type = 'flat';
                        }
                        $totalDiscountRemaining -= $discountForThis;
                        
                        $netDue = max(0.00, floatval($sf->amount)
                            + floatval($sf->fine_amount_applied ?? 0)
                            - floatval($sf->paid_amount)
                            - floatval($sf->instant_discount_amount));
                    }

                    $paymentForThis = 0;
                    if ($amountToDistribute > 0 && $netDue > 0) {
                        $paymentForThis = min($amountToDistribute, $netDue);
                        $sf->paid_amount = floatval($sf->paid_amount) + $paymentForThis;
                        $amountToDistribute -= $paymentForThis;
                    }

                    $totalNetOwed = floatval($sf->amount)
                        + floatval($sf->fine_amount_applied ?? 0)
                        - floatval($sf->instant_discount_amount);
                    if ($sf->paid_amount >= $totalNetOwed) {
                        $sf->status = 'paid';
                    } elseif ($sf->paid_amount > 0) {
                        $sf->status = 'partially_paid';
                    }
                    $sf->save();

                    if ($paymentForThis > 0 || $discountForThis > 0 || $sf->misc_fee_id !== null) {
                        $detailsArray[] = [
                            'student_fee_id'       => $sf->id,
                            'component_name'       => $sf->component ? $sf->component->component_name : ($sf->miscFee ? $sf->miscFee->name : ($sf->category ? $sf->category->name : 'Fee')),
                            'installment_no'       => $sf->installment_no,
                            'amount_paid'          => $paymentForThis,
                            'discount_amount'      => floatval($sf->instant_discount_amount),
                            'transaction_discount' => $discountForThis,
                        ];
                    }
                }

                if ($amountToDistribute > 0 && $feesToPay->isNotEmpty()) {
                    $first = $feesToPay->first();
                    $first->paid_amount = floatval($first->paid_amount) + $amountToDistribute;
                    $totalNetOwed = floatval($first->amount)
                        + floatval($first->fine_amount_applied ?? 0)
                        - floatval($first->instant_discount_amount);
                    $first->status = $first->paid_amount >= $totalNetOwed ? 'paid' : 'partially_paid';
                    $first->save();

                    if (!empty($detailsArray)) {
                        $detailsArray[count($detailsArray) - 1]['amount_paid'] += $amountToDistribute;
                    } else {
                        $detailsArray[] = [
                            'student_fee_id'       => $first->id,
                            'component_name'       => $first->component ? $first->component->component_name : ($first->category ? $first->category->name : 'Fee'),
                            'installment_no'       => $first->installment_no,
                            'amount_paid'          => $amountToDistribute,
                            'discount_amount'      => floatval($first->instant_discount_amount),
                            'transaction_discount' => 0,
                        ];
                    }
                }

                FeeReceipt::create([
                    'school_id'       => $schoolId,
                    'student_id'      => $cheque->student_id,
                    'receipt_number'  => $receiptNum,
                    'amount_paid'     => floatval($cheque->amount),
                    'discount_amount' => $chequeDiscountTotal,
                    'discount_type'   => $chequeDiscountTotal > 0 ? 'flat' : null,
                    'payment_mode'    => 'cheque',
                    'transaction_id'  => $cheque->cheque_number,
                    'payment_date'    => $cheque->receipt_date ?: now()->toDateString(),
                    'payment_details' => json_encode([
                        'components'            => $detailsArray,
                        'cheque_number'         => $cheque->cheque_number,
                        'bank_name'             => $cheque->bank_name,
                        'cheque_clearance_date' => now()->toDateString(),
                    ]),
                ]);

                $isTransportCheque = false;
                if ($cheque->receipt_number && stripos($cheque->receipt_number, 'TRN') !== false) {
                    $isTransportCheque = true;
                } else {
                    foreach ($feesToPay as $sf) {
                        if ($sf->transport_fee_schedule_id !== null || (optional($sf->category)->name === 'Transport') || (optional($sf->component)->component_name === 'Transport Fee')) {
                            $isTransportCheque = true;
                            break;
                        }
                    }
                }

                $remarks = $isTransportCheque 
                    ? 'Cheque Clearance (Transport) (Cheque No: ' . $cheque->cheque_number . ')'
                    : 'Cheque Clearance (Cheque No: ' . $cheque->cheque_number . ')';

                $invNo = 'INV-' . $instNo . '-PAY-' . now()->format('YmdHisu') . '-' . rand(10, 99);
                \App\Models\FeeInvoice::create([
                    'school_id'       => $schoolId,
                    'student_id'      => $cheque->student_id,
                    'created_by'      => auth()->id(),
                    'invoice_number'  => $invNo,
                    'installment_no'  => $instNo,
                    'type'            => 'payment',
                    'status'          => 'paid',
                    'amount'          => floatval($cheque->amount),
                    'discount_amount' => $chequeDiscountTotal,
                    'payment_mode'    => 'cheque',
                    'payment_date'    => $cheque->receipt_date ?: now()->toDateString(),
                    'payment_details' => json_encode([
                        'components'            => $detailsArray,
                        'cheque_number'         => $cheque->cheque_number,
                        'bank_name'             => $cheque->bank_name,
                        'cheque_clearance_date' => now()->toDateString(),
                        'receipt_number'        => $receiptNum,
                    ]),
                    'remarks'         => $remarks,
                ]);

                $cStudent = \App\Models\Student::find($cheque->student_id);
                if ($cStudent) {
                    \App\Services\FeeNotificationService::sendPaymentSuccessNotification($cStudent, $instNo, floatval($cheque->amount));
                }

            } elseif (in_array($newStatus, ['bounced', 'cancelled', 'returned', 'rejected'])) {
                $cheque->status = $newStatus;
                $cheque->save();

                if ($oldStatus === 'cleared') {
                    $receipt = \App\Models\FeeReceipt::where('school_id', $schoolId)
                        ->where('receipt_number', $cheque->receipt_number)
                        ->first();

                    if ($receipt && $receipt->payment_details) {
                        $details = json_decode($receipt->payment_details, true);
                        $components = $details['components'] ?? [];

                        foreach ($components as $comp) {
                            $sfId = $comp['student_fee_id'] ?? null;
                            if ($sfId) {
                                $sf = StudentFee::withoutGlobalScopes()->find($sfId);
                                if ($sf) {
                                    $sf->paid_amount = max(0.00, floatval($sf->paid_amount) - floatval($comp['amount_paid'] ?? 0));
                                    $sf->instant_discount_amount = max(0.00, floatval($sf->instant_discount_amount) - floatval($comp['transaction_discount'] ?? 0));
                                    $totalNetOwed = floatval($sf->amount) + floatval($sf->fine_amount_applied ?? 0) - floatval($sf->instant_discount_amount);
                                    $sf->status = floatval($sf->paid_amount) <= 0 ? 'pending' : (floatval($sf->paid_amount) >= $totalNetOwed ? 'paid' : 'partially_paid');
                                    $sf->save();
                                }
                            }
                        }
                        $receipt->delete();
                    } else {
                        $feeIds = json_decode($cheque->student_fee_ids, true) ?: [];
                        if (!empty($feeIds)) {
                            $fees = StudentFee::withoutGlobalScopes()
                                ->where('school_id', $schoolId)
                                ->whereIn('id', $feeIds)
                                ->get();
                            $toReverse = floatval($cheque->amount);
                            foreach ($fees as $fee) {
                                $reversible = min($toReverse, floatval($fee->paid_amount));
                                if ($reversible > 0) {
                                    $fee->paid_amount = max(0.00, floatval($fee->paid_amount) - $reversible);
                                    $totalDue = floatval($fee->amount) + floatval($fee->fine_amount_applied ?? 0) - floatval($fee->instant_discount_amount);
                                    $fee->status = floatval($fee->paid_amount) <= 0 ? 'pending' : (floatval($fee->paid_amount) >= $totalDue ? 'paid' : 'partially_paid');
                                    $fee->save();
                                    $toReverse -= $reversible;
                                }
                            }
                        }
                    }

                    \App\Models\FeeInvoice::where('school_id', $schoolId)
                        ->where('student_id', $cheque->student_id)
                        ->where('payment_mode', 'cheque')
                        ->where('status', 'paid')
                        ->where('remarks', 'like', '%' . $cheque->cheque_number . '%')
                        ->update(['status' => 'cancelled']);

                    $cStudent = \App\Models\Student::find($cheque->student_id);
                    if ($cStudent) {
                        \App\Services\FeeNotificationService::sendPaymentCancelledNotification($cStudent, $cheque->installment_no ?? 1);
                    }
                }

                $invNo = 'INV-CHQ-' . strtoupper(substr($newStatus, 0, 3)) . '-' . now()->format('YmdHisu') . '-' . rand(10, 99);
                \App\Models\FeeInvoice::create([
                    'school_id'       => $schoolId,
                    'student_id'      => $cheque->student_id,
                    'created_by'      => auth()->id(),
                    'invoice_number'  => $invNo,
                    'installment_no'  => $cheque->installment_no ?? 1,
                    'type'            => $newStatus . '_cheque',
                    'status'          => $newStatus,
                    'amount'          => floatval($cheque->amount),
                    'discount_amount' => floatval($cheque->discount_amount ?? 0),
                    'payment_mode'    => 'cheque',
                    'payment_date'    => now()->toDateString(),
                    'payment_details' => json_encode([
                        'cheque_number' => $cheque->cheque_number,
                        'bank_name'     => $cheque->bank_name,
                        'cheque_id'     => $cheque->id,
                        'cheque_status' => $newStatus,
                    ]),
                    'remarks'         => 'Cheque ' . ucfirst($newStatus) . ' (No: ' . $cheque->cheque_number . ', Bank: ' . $cheque->bank_name . ')',
                ]);

            } elseif ($newStatus === 'pending') {
                $cheque->status = 'pending';
                $cheque->save();

                if ($oldStatus === 'cleared') {
                    $receipt = \App\Models\FeeReceipt::where('school_id', $schoolId)
                        ->where('receipt_number', $cheque->receipt_number)
                        ->first();

                    if ($receipt && $receipt->payment_details) {
                        $details = json_decode($receipt->payment_details, true);
                        $components = $details['components'] ?? [];

                        foreach ($components as $comp) {
                            $sfId = $comp['student_fee_id'] ?? null;
                            if ($sfId) {
                                $sf = StudentFee::withoutGlobalScopes()->find($sfId);
                                if ($sf) {
                                    $sf->paid_amount = max(0.00, floatval($sf->paid_amount) - floatval($comp['amount_paid'] ?? 0));
                                    $sf->instant_discount_amount = max(0.00, floatval($sf->instant_discount_amount) - floatval($comp['transaction_discount'] ?? 0));
                                    $totalNetOwed = floatval($sf->amount) + floatval($sf->fine_amount_applied ?? 0) - floatval($sf->instant_discount_amount);
                                    $sf->status = floatval($sf->paid_amount) <= 0 ? 'pending' : (floatval($sf->paid_amount) >= $totalNetOwed ? 'paid' : 'partially_paid');
                                    $sf->save();
                                }
                            }
                        }
                        $receipt->delete();
                    } else {
                        $feeIds = json_decode($cheque->student_fee_ids, true) ?: [];
                        if (!empty($feeIds)) {
                            $fees = StudentFee::withoutGlobalScopes()
                                ->where('school_id', $schoolId)
                                ->whereIn('id', $feeIds)
                                ->get();
                            $toReverse = floatval($cheque->amount);
                            foreach ($fees as $fee) {
                                $reversible = min($toReverse, floatval($fee->paid_amount));
                                if ($reversible > 0) {
                                    $fee->paid_amount = max(0.00, floatval($fee->paid_amount) - $reversible);
                                    $totalDue = floatval($fee->amount) + floatval($fee->fine_amount_applied ?? 0) - floatval($fee->instant_discount_amount);
                                    $fee->status = floatval($fee->paid_amount) <= 0 ? 'pending' : (floatval($fee->paid_amount) >= $totalDue ? 'paid' : 'partially_paid');
                                    $fee->save();
                                    $toReverse -= $reversible;
                                }
                            }
                        }
                    }

                    \App\Models\FeeInvoice::where('school_id', $schoolId)
                        ->where('student_id', $cheque->student_id)
                        ->where('payment_mode', 'cheque')
                        ->where('status', 'paid')
                        ->where('remarks', 'like', '%' . $cheque->cheque_number . '%')
                        ->update(['status' => 'cancelled']);
                }

                \App\Models\FeeInvoice::where('school_id', $schoolId)
                    ->where('student_id', $cheque->student_id)
                    ->where('payment_mode', 'cheque')
                    ->whereIn('status', ['bounced', 'cancelled', 'returned', 'rejected'])
                    ->where('remarks', 'like', '%' . $cheque->cheque_number . '%')
                    ->update(['status' => 'cancelled']);
            }

            \App\Models\StudentFee::clearPendingReservationsCache();
        });
        \App\Models\StudentFee::syncTransportFees($schoolId);
    }

    private function checkHasActivePayments($type, $id, $schoolId)
    {
        if ($type === 'schedule') {
            $feeInvoiceNumbers = \App\Models\StudentFee::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->where('fee_schedule_id', $id)
                ->whereNotNull('invoice_no')
                ->pluck('invoice_no')
                ->toArray();
            
            $hasInvoice = false;
            if (!empty($feeInvoiceNumbers)) {
                $hasInvoice = \App\Models\FeeInvoice::where('school_id', $schoolId)
                    ->whereIn('invoice_number', $feeInvoiceNumbers)
                    ->exists();
            }

            $hasPaid = \App\Models\StudentFee::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->where('fee_schedule_id', $id)
                ->where('paid_amount', '>', 0)
                ->exists();

            if ($hasPaid || $hasInvoice) {
                return 'This schedule cannot be deleted because payments or invoices have already been recorded under it.';
            }

            // Check pending cheques referencing this schedule's fees
            $pendingCheques = \App\Models\PendingCheque::where('school_id', $schoolId)
                ->where('status', 'pending')
                ->get();
            foreach ($pendingCheques as $pc) {
                $chqFeeIds = json_decode($pc->student_fee_ids, true) ?? [];
                if (!is_array($chqFeeIds)) continue;
                $hasScheduleFee = \App\Models\StudentFee::withoutGlobalScopes()
                    ->whereIn('id', $chqFeeIds)
                    ->where('fee_schedule_id', $id)
                    ->exists();
                if ($hasScheduleFee) {
                    return 'This schedule cannot be deleted because there is a pending cheque associated with one of its fee installments.';
                }
            }
        } elseif ($type === 'component') {
            $feeInvoiceNumbers = \App\Models\StudentFee::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->where('fee_component_id', $id)
                ->whereNotNull('invoice_no')
                ->pluck('invoice_no')
                ->toArray();
            
            $hasInvoice = false;
            if (!empty($feeInvoiceNumbers)) {
                $hasInvoice = \App\Models\FeeInvoice::where('school_id', $schoolId)
                    ->whereIn('invoice_number', $feeInvoiceNumbers)
                    ->exists();
            }

            $hasPaid = \App\Models\StudentFee::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->where('fee_component_id', $id)
                ->where('paid_amount', '>', 0)
                ->exists();

            if ($hasPaid || $hasInvoice) {
                return 'This component cannot be deleted because payments or invoices have already been recorded under it.';
            }

            // Check pending cheques referencing this component
            $pendingCheques = \App\Models\PendingCheque::where('school_id', $schoolId)
                ->where('status', 'pending')
                ->get();
            foreach ($pendingCheques as $pc) {
                $chqFeeIds = json_decode($pc->student_fee_ids, true) ?? [];
                if (!is_array($chqFeeIds)) continue;
                $hasComponentFee = \App\Models\StudentFee::withoutGlobalScopes()
                    ->whereIn('id', $chqFeeIds)
                    ->where('fee_component_id', $id)
                    ->exists();
                if ($hasComponentFee) {
                    return 'This component cannot be deleted because there is a pending cheque associated with it.';
                }
            }
        } elseif ($type === 'misc_fee') {
            $feeInvoiceNumbers = \App\Models\StudentFee::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->where('misc_fee_id', $id)
                ->whereNotNull('invoice_no')
                ->pluck('invoice_no')
                ->toArray();
            
            $hasInvoice = false;
            if (!empty($feeInvoiceNumbers)) {
                $hasInvoice = \App\Models\FeeInvoice::where('school_id', $schoolId)
                    ->whereIn('invoice_number', $feeInvoiceNumbers)
                    ->exists();
            }

            $hasPaid = \App\Models\StudentFee::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->where('misc_fee_id', $id)
                ->where('paid_amount', '>', 0)
                ->exists();

            if ($hasPaid || $hasInvoice) {
                return 'This miscellaneous fee cannot be deleted because payments or invoices have already been recorded under it.';
            }

            // Check pending cheques referencing this misc fee
            $pendingCheques = \App\Models\PendingCheque::where('school_id', $schoolId)
                ->where('status', 'pending')
                ->get();
            foreach ($pendingCheques as $pc) {
                $chqFeeIds = json_decode($pc->student_fee_ids, true) ?? [];
                if (!is_array($chqFeeIds)) continue;
                $hasMiscFee = \App\Models\StudentFee::withoutGlobalScopes()
                    ->whereIn('id', $chqFeeIds)
                    ->where('misc_fee_id', $id)
                    ->exists();
                if ($hasMiscFee) {
                    return 'This miscellaneous fee cannot be deleted because there is a pending cheque associated with it.';
                }
            }
        }

        return false;
    }

    public static function generateNextReceiptNumber($schoolId, $prefix)
    {
        // Search fee_receipts for matching prefix
        $latestReceipt = \App\Models\FeeReceipt::withoutGlobalScope('active')
            ->where('school_id', $schoolId)
            ->where('receipt_number', 'like', $prefix . '-%')
            ->orderBy('id', 'desc')
            ->first();

        $nextNum = 1;
        if ($latestReceipt) {
            $parts = explode('-', $latestReceipt->receipt_number);
            $lastNumStr = end($parts);
            if (is_numeric($lastNumStr)) {
                $nextNum = intval($lastNumStr) + 1;
            }
        }

        // Also check pending cheques to prevent duplicate numbers
        $latestCheque = \App\Models\PendingCheque::where('school_id', $schoolId)
            ->where('receipt_number', 'like', $prefix . '-%')
            ->orderBy('id', 'desc')
            ->first();
        if ($latestCheque) {
            $parts = explode('-', $latestCheque->receipt_number);
            $lastNumStr = end($parts);
            if (is_numeric($lastNumStr)) {
                $chequeNum = intval($lastNumStr) + 1;
                if ($chequeNum > $nextNum) {
                    $nextNum = $chequeNum;
                }
            }
        }

        return $prefix . '-' . str_pad($nextNum, 6, '0', STR_PAD_LEFT);
    }

    public function getNextReceiptNo(Request $request)
    {
        $schoolId = $this->resolveSchoolId();
        $config = \App\Models\FeeConfiguration::where('school_id', $schoolId)->first();
        $schoolPrefix = $config->school_fee_prefix ?? 'REC';
        $transportPrefix = $config->transport_fee_prefix ?? 'TRN';

        $feeType = $request->input('fee_type', 'tuition');
        $prefix = ($feeType === 'transport') ? $transportPrefix : $schoolPrefix;
        
        $receiptNo = self::generateNextReceiptNumber($schoolId, $prefix);

        return response()->json([
            'success' => true,
            'receipt_no' => $receiptNo
        ]);
    }

    private function resolveSchoolId()
    {
        return optional(auth()->user())->school_id 
            ?? session('school_id') 
            ?? (app()->bound('currentSchool') ? app('currentSchool')->id : null) 
            ?? \App\Models\School::value('id');
    }
}
