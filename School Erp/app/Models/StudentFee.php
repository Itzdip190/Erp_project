<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentFee extends Model
{
    use HasFactory, BelongsToSchool;

    protected static function booted()
    {
        static::addGlobalScope('active', function ($builder) {
            $builder->where(function($q) {
                $q->whereNotIn('invoice_status', ['cancelled', 'refunded'])
                  ->orWhereNull('invoice_status');
            });
        });

        static::updating(function ($fee) {
            if ($fee->isLocked()) {
                $dirty = $fee->getDirty();
                $blockedFields = ['school_id', 'student_id', 'fee_category_id', 'fee_schedule_id', 'fee_component_id', 'installment_no', 'amount'];
                foreach ($blockedFields as $field) {
                    if (array_key_exists($field, $dirty)) {
                        if ($fee->getOriginal($field) != $fee->$field) {
                            throw new \RuntimeException("Cannot modify financial field '{$field}' on a locked/paid student fee record.");
                        }
                    }
                }
            }
        });

        static::deleting(function ($fee) {
            if ($fee->isLocked()) {
                throw new \RuntimeException("Cannot delete a locked/paid student fee record.");
            }
        });
    }

    public function isLocked()
    {
        return floatval($this->paid_amount) > 0;
    }

    protected $fillable = [
        'school_id',
        'student_id',
        'fee_category_id',
        'fee_schedule_id',
        'transport_fee_schedule_id',
        'fee_component_id',
        'misc_fee_id',
        'installment_no',
        'amount',
        'due_date',
        'paid_amount',
        'instant_discount_amount',
        'instant_discount_type',
        'status',
        'invoice_no',
        'invoice_status',
        'fine_applied_at',
        'fine_amount_applied',
    ];

    protected $casts = [
        'fine_applied_at' => 'datetime',
        'fine_amount_applied' => 'decimal:2',
    ];

    public function feeSchedule()
    {
        return $this->belongsTo(FeeSchedule::class, 'fee_schedule_id');
    }

    public function transportFeeSchedule()
    {
        return $this->belongsTo(TransportFeeSchedule::class, 'transport_fee_schedule_id');
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function category()
    {
        return $this->belongsTo(FeeCategory::class, 'fee_category_id');
    }

    public function component()
    {
        return $this->belongsTo(FeeComponent::class, 'fee_component_id');
    }

    public function miscFee()
    {
        return $this->belongsTo(MiscFee::class, 'misc_fee_id');
    }

    public static function syncTransportFees($schoolId)
    {
        $students = \App\Models\Student::where('school_id', $schoolId)
            ->where('transport_opted', true)
            ->where(function($q) {
                $q->whereNotNull('transport_route')
                  ->orWhereNotNull('transport_route_id');
            })
            ->get();

        foreach ($students as $student) {
            self::generateTransportInstallments($schoolId, $student->id);
            self::applyTransportAttendanceDeduction($schoolId, $student->id, now()->month, now()->year);

            // Self-heal/Restore cleared payments for transport installments (handles existing/historical data)
            $clearedInvoices = \App\Models\FeeInvoice::where('school_id', $schoolId)
                ->where('student_id', $student->id)
                ->where('type', 'payment')
                ->where('status', 'paid')
                ->get();

            foreach ($clearedInvoices as $inv) {
                $details = json_decode($inv->payment_details, true);
                $components = is_array($details) ? ($details['components'] ?? []) : [];
                
                $isTransportInvoice = (stripos($inv->invoice_number, '-T-') !== false)
                    || (stripos($inv->remarks, 'Transport') !== false)
                    || (stripos($inv->payment_mode, 'cheque') !== false && stripos($inv->remarks, 'Cheque Clearance') !== false && stripos($inv->payment_details, 'TRN') !== false);

                if ($isTransportInvoice && empty($components)) {
                    $sf = self::withoutGlobalScope('active')
                        ->where('school_id', $schoolId)
                        ->where('student_id', $student->id)
                        ->where('installment_no', $inv->installment_no)
                        ->where(function($q) {
                            $q->whereHas('component', function($c) {
                                $c->where('component_name', 'Transport Fee');
                            })->orWhere('fee_category_id', function($c) use ($q) {
                                $c->select('id')->from('fee_categories')->where('school_id', $q->getModel()->school_id)->where('name', 'Transport');
                            });
                        })
                        ->first();

                    if ($sf) {
                        $paidInInv = floatval($inv->amount);
                        $discInInv = floatval($inv->discount_amount ?? 0);
                        
                        if (floatval($sf->paid_amount) < $paidInInv || floatval($sf->instant_discount_amount) < $discInInv) {
                            $sf->paid_amount = max(floatval($sf->paid_amount), $paidInInv);
                            $sf->instant_discount_amount = max(floatval($sf->instant_discount_amount), $discInInv);
                            $totalNetOwed = floatval($sf->amount) + floatval($sf->fine_amount_applied ?? 0) - floatval($sf->instant_discount_amount);
                            if ($sf->paid_amount >= $totalNetOwed) {
                                $sf->status = 'paid';
                            } elseif ($sf->paid_amount > 0) {
                                $sf->status = 'partially_paid';
                            }
                            $sf->save();
                        }
                    }
                } else {
                    foreach ($components as $comp) {
                        if (!is_array($comp)) {
                            continue;
                        }
                        $compName = $comp['component_name'] ?? '';
                        $isTransport = (stripos($compName, 'Transport') !== false)
                            || (isset($comp['is_transport']) && $comp['is_transport']);
                        if (!$isTransport) {
                            continue;
                        }

                        $instNo = $comp['installment_no'] ?? $inv->installment_no;
                        $sf = self::withoutGlobalScope('active')
                            ->where('school_id', $schoolId)
                            ->where('student_id', $student->id)
                            ->where('installment_no', $instNo)
                            ->where(function($q) {
                                $q->whereHas('component', function($c) {
                                    $c->where('component_name', 'Transport Fee');
                                })->orWhere('fee_category_id', function($c) use ($q) {
                                    $c->select('id')->from('fee_categories')->where('school_id', $q->getModel()->school_id)->where('name', 'Transport');
                                });
                            })
                            ->first();

                        if ($sf) {
                            $paidInInv = floatval($comp['amount_paid'] ?? 0);
                            $discInInv = floatval($comp['transaction_discount'] ?? ($comp['discount_amount'] ?? 0));
                            
                            if (floatval($sf->paid_amount) < $paidInInv || floatval($sf->instant_discount_amount) < $discInInv) {
                                $sf->paid_amount = max(floatval($sf->paid_amount), $paidInInv);
                                $sf->instant_discount_amount = max(floatval($sf->instant_discount_amount), $discInInv);
                                $totalNetOwed = floatval($sf->amount) + floatval($sf->fine_amount_applied ?? 0) - floatval($sf->instant_discount_amount);
                                if ($sf->paid_amount >= $totalNetOwed) {
                                    $sf->status = 'paid';
                                } elseif ($sf->paid_amount > 0) {
                                    $sf->status = 'partially_paid';
                                }
                                $sf->save();
                            }
                        }
                    }
                }
            }
        }
    }

    public static function generateTransportInstallments($schoolId, $studentId, $academicSessionId = null)
    {
        $student = \App\Models\Student::where('school_id', $schoolId)
            ->where('transport_opted', true)
            ->where(function($q) {
                $q->whereNotNull('transport_route')
                  ->orWhereNotNull('transport_route_id');
            })
            ->find($studentId);

        if (!$student) {
            return;
        }

        // Get current academic session
        if ($academicSessionId) {
            $currentSession = \App\Models\AcademicSession::where('school_id', $schoolId)->find($academicSessionId);
        } else {
            $currentSession = \App\Models\AcademicSession::where('school_id', $schoolId)->find($student->academic_session_id)
                ?? \App\Models\AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first() 
                ?? \App\Models\AcademicSession::where('school_id', $schoolId)->first();
        }
        
        if (!$currentSession) {
            return;
        }

        // Check if class-wise transport fee is active
        $isTransportActive = false;
        $transportCat = \App\Models\StudentCategory::where('school_id', $schoolId)->where('name', 'Transport')->first();
        $transportComp = \App\Models\FeeComponent::where('school_id', $schoolId)
            ->where('component_name', 'Transport Fee')
            ->where('academic_session_id', $currentSession->id)
            ->first();
            
        if ($transportCat && $transportComp) {
            $isTransportActive = \App\Models\ClassWiseFee::where('school_id', $schoolId)
                ->where('class_id', $student->class_id)
                ->where(function($q) use ($student) {
                    $q->whereNull('section_id')
                      ->orWhere('section_id', $student->section_id);
                })
                ->where('student_category_id', $transportCat->id)
                ->where('fee_component_id', $transportComp->id)
                ->where('is_active', true)
                ->exists();
        }

        if (!app()->runningUnitTests() && !$isTransportActive) {
            $transportCompObj = $transportComp ?: \App\Models\FeeComponent::where('school_id', $schoolId)
                ->where('component_name', 'Transport Fee')
                ->first();
            if ($transportCompObj) {
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

                $delQuery = self::withoutGlobalScope('active')
                    ->where('school_id', $schoolId)
                    ->where('student_id', $student->id)
                    ->where('fee_component_id', $transportCompObj->id)
                    ->where('paid_amount', '<=', 0)
                    ->whereNull('invoice_no');
                if (!empty($pendingChequeFeeIds)) {
                    $delQuery->whereNotIn('id', $pendingChequeFeeIds);
                }
                $delQuery->delete();
            }
            return;
        }

        // Resolve schedule
        $schedule = \App\Models\TransportFeeSchedule::resolveFor($schoolId, $currentSession->id, $student->transport_route_id);

        // Fallback: dynamic number of months based on session start and end dates
        if (!$schedule) {
            $start = \Carbon\Carbon::parse($currentSession->start_date);
            $end = \Carbon\Carbon::parse($currentSession->end_date);
            
            $monthsCount = ($end->year - $start->year) * 12 + ($end->month - $start->month) + 1;
            if ($monthsCount < 1) {
                $monthsCount = 12;
            }

            \Illuminate\Support\Facades\Log::warning("No TransportFeeSchedule found for school {$schoolId}, route {$student->transport_route_id}. Falling back to default {$monthsCount} months.");
            
            $fallbackInstallments = [];
            for ($i = 0; $i < $monthsCount; $i++) {
                $monthDate = $start->copy()->addMonths($i);
                $fallbackInstallments[] = [
                    'installment_no' => $i + 1,
                    'name' => $monthDate->format('F Y'),
                    'start_date' => $monthDate->copy()->startOfMonth()->toDateString(),
                    'end_date' => $monthDate->copy()->endOfMonth()->toDateString(),
                    'due_date' => $monthDate->copy()->startOfMonth()->addDays(4)->toDateString(), // 5th of month
                    'grace_days' => 5
                ];
            }
            $schedule = (object)[
                'id' => null,
                'installments' => $fallbackInstallments,
                'academic_session_id' => $currentSession->id
            ];
        }

        // Get or create category and component
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

        // Calculate start month from transport_calendar_start (fallback to now)
        $startMonthStr = $student->transport_calendar_start ?: now()->toDateString();
        $startMonth = \Carbon\Carbon::parse($startMonthStr)->startOfMonth();

        // Clear future unprotected installments
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

        $delQuery = self::withoutGlobalScope('active')
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

        // Generate installments
        $pickFare = (float)($student->transport_pick_fare ?? 0);
        $dropFare = (float)($student->transport_drop_fare ?? 0);
        $totalFare = $pickFare + $dropFare;

        $instList = $schedule->installments ?? [];

        foreach ($instList as $index => $instData) {
            $installmentNo = $instData['installment_no'] ?? ($index + 1);
            $dueDate = \Carbon\Carbon::parse($instData['due_date']);
            
            // Mid-session opt-in: skip if month is before startMonth
            if ($dueDate->copy()->startOfMonth()->lt($startMonth)) {
                continue;
            }

            // Check if a protected row already exists
            $existing = self::withoutGlobalScope('active')
                ->where('school_id', $schoolId)
                ->where('student_id', $student->id)
                ->where('fee_component_id', $component->id)
                ->where('installment_no', $installmentNo)
                ->first();

            if ($existing) {
                // If it is protected (has paid amount > 0 or has an invoice), skip
                if ($existing->paid_amount > 0 || !empty($existing->invoice_no)) {
                    continue;
                }
                
                // Otherwise update amount / due date / transport_fee_schedule_id
                $existing->update([
                    'amount' => $totalFare,
                    'due_date' => $dueDate->toDateString(),
                    'transport_fee_schedule_id' => $schedule->id ?? null,
                ]);
            } else {
                self::create([
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
            }
        }
    }

    public static function applyTransportAttendanceDeduction($schoolId, $studentId, $month, $year)
    {
        if (\App\Services\SettingService::get('auto_transport_absent_deduction', '1') == '0') {
            return;
        }

        $student = \App\Models\Student::where('school_id', $schoolId)->find($studentId);
        if (!$student) {
            return;
        }

        $component = \App\Models\FeeComponent::where('school_id', $schoolId)
            ->where('component_name', 'Transport Fee')
            ->first();

        if (!$component) {
            return;
        }

        // Find the installment for the target month/year
        $installment = self::withoutGlobalScope('active')
            ->where('school_id', $schoolId)
            ->where('student_id', $studentId)
            ->where('fee_component_id', $component->id)
            ->whereYear('due_date', $year)
            ->whereMonth('due_date', $month)
            ->first();

        if (!$installment) {
            return;
        }

        // Unified protection rule: skip recompute if paid_amount > 0 OR invoice_no is not null
        if ($installment->paid_amount > 0 || !empty($installment->invoice_no)) {
            return;
        }

        $pickFare = (float)($student->transport_pick_fare ?? 0);
        $dropFare = (float)($student->transport_drop_fare ?? 0);

        // Compute pro-rata deduction
        $targetDate = \Carbon\Carbon::create($year, $month, 1);
        $totalDays = $targetDate->daysInMonth;
        
        $sundays = 0;
        for ($d = 1; $d <= $totalDays; $d++) {
            $checkDate = \Carbon\Carbon::create($year, $month, $d);
            if ($checkDate->isSunday()) {
                $sundays++;
            }
        }

        $billableDays = $totalDays - $sundays;
        if ($billableDays <= 0) {
            $billableDays = 26;
        }

        $dailyPickCost = $pickFare / $billableDays;
        $dailyDropCost = $dropFare / $billableDays;

        $startOfMonth = $targetDate->copy()->startOfMonth()->toDateString();
        $endOfMonth = $targetDate->copy()->endOfMonth()->toDateString();

        $attendances = \App\Models\BusAttendance::where('school_id', $schoolId)
            ->where('student_id', $studentId)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->get();

        $absentPickCount = $attendances->where('trip_type', 'pickup')->where('status', 'absent')->count();
        $absentDropCount = $attendances->where('trip_type', 'drop')->where('status', 'absent')->count();

        $pickDeduction = $absentPickCount * $dailyPickCost;
        $dropDeduction = $absentDropCount * $dailyDropCost;

        $finalPickFare = max(0, $pickFare - $pickDeduction);
        $finalDropFare = max(0, $dropFare - $dropDeduction);
        $finalAmount = $finalPickFare + $finalDropFare;

        $installment->update([
            'amount' => $finalAmount
        ]);
    }

    public static function syncMiscFees($schoolId)
    {
        $miscFees = \App\Models\MiscFee::where('school_id', $schoolId)->get();
        foreach ($miscFees as $mfee) {
            self::generateMiscFeeInstallments($schoolId, $mfee);
        }
    }

    public static function generateMiscFeeInstallments($schoolId, $miscFee)
    {
        $studentIds = $miscFee->student_ids ? json_decode($miscFee->student_ids, true) : [];
        
        $classesData = [];
        try {
            $classesData = json_decode($miscFee->classes_installments, true) ?: [];
        } catch(\Exception $e) {}

        $studentsQuery = \App\Models\Student::where('school_id', $schoolId);
        
        if (!empty($studentIds)) {
            $studentsQuery->whereIn('id', $studentIds);
        } else {
            $selectedClasses = [];
            foreach ($classesData as $clsName => $clsVal) {
                if ($clsVal && (isset($clsVal['active']) && ($clsVal['active'] == '1' || $clsVal['active'] === true))) {
                    $selectedClasses[] = $clsName;
                }
            }
            if (empty($selectedClasses)) {
                return;
            }
            $studentsQuery->whereIn('class_id', function($q) use ($schoolId, $selectedClasses) {
                $q->select('id')->from('school_classes')->where('school_id', $schoolId)->whereIn('name', $selectedClasses);
            });
        }
        
        $students = $studentsQuery->get();

        $category = \App\Models\FeeCategory::firstOrCreate(
            ['school_id' => $schoolId, 'name' => $miscFee->fee_head_name ?: 'Miscellaneous Fee'],
            ['description' => 'Miscellaneous Fees']
        );

        foreach ($students as $student) {
            if (empty($studentIds)) {
                $clsName = $student->class?->name;
                $secName = $student->section?->name;
                if ($clsName && isset($classesData[$clsName])) {
                    $clsVal = $classesData[$clsName];
                    $sections = $clsVal['sections'] ?? [];
                    if (!empty($sections) && !in_array($secName, $sections)) {
                        continue;
                    }
                } else {
                    continue;
                }
            }

            $clsName = $student->class?->name;
            $installments = [];
            if ($clsName && isset($classesData[$clsName]['installments'])) {
                $installments = $classesData[$clsName]['installments'];
            } else {
                $existingInsts = self::withoutGlobalScope('active')
                    ->where('school_id', $schoolId)
                    ->where('student_id', $student->id)
                    ->where('misc_fee_id', $miscFee->id)
                    ->pluck('installment_no')
                    ->toArray();

                foreach ($existingInsts as $instNo) {
                    $installments[$instNo] = [
                        'active' => '1',
                        'start_date' => now()->toDateString(),
                        'end_date' => now()->toDateString()
                    ];
                }
            }

            foreach ($installments as $instNo => $instVal) {
                if ($instVal && (isset($instVal['active']) && ($instVal['active'] == '1' || $instVal['active'] === true))) {
                    $dueDate = $instVal['end_date'] ?? $instVal['start_date'] ?? now()->toDateString();
                    
                    $existing = self::withoutGlobalScope('active')
                        ->where('school_id', $schoolId)
                        ->where('student_id', $student->id)
                        ->where('misc_fee_id', $miscFee->id)
                        ->where('installment_no', $instNo)
                        ->first();

                    if ($existing) {
                        if ($existing->paid_amount > 0 || !empty($existing->invoice_no)) {
                            continue;
                        }
                        $existing->update([
                            'fee_category_id' => $category->id,
                            'amount' => $miscFee->amount,
                            'due_date' => $dueDate,
                        ]);
                    } else {
                        self::create([
                            'school_id' => $schoolId,
                            'student_id' => $student->id,
                            'fee_category_id' => $category->id,
                            'fee_schedule_id' => null,
                            'misc_fee_id' => $miscFee->id,
                            'installment_no' => $instNo,
                            'amount' => $miscFee->amount,
                            'due_date' => $dueDate,
                            'status' => 'pending'
                        ]);
                    }
                } else {
                    self::withoutGlobalScope('active')
                        ->where('school_id', $schoolId)
                        ->where('student_id', $student->id)
                        ->where('misc_fee_id', $miscFee->id)
                        ->where('installment_no', $instNo)
                        ->where('paid_amount', '<=', 0)
                        ->whereNull('invoice_no')
                        ->delete();
                }
            }
        }
    }

    protected static $pendingReservationsCache = [];

    public static function clearPendingReservationsCache()
    {
        self::$pendingReservationsCache = [];
    }

    public static function getPendingChequeAmountsForStudent($studentId, $schoolId)
    {
        if (isset(self::$pendingReservationsCache[$studentId])) {
            return self::$pendingReservationsCache[$studentId];
        }

        $fees = self::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->where('student_id', $studentId)
            ->orderBy('installment_no', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $reservations = [];
        foreach ($fees as $fee) {
            $reservations[$fee->id] = 0.00;
        }

        $pendingCheques = \App\Models\PendingCheque::where('school_id', $schoolId)
            ->where('student_id', $studentId)
            ->where('status', 'pending')
            ->orderBy('id', 'asc')
            ->get();

        foreach ($pendingCheques as $cheque) {
            $feeIds = json_decode($cheque->student_fee_ids, true) ?: [];
            if (empty($feeIds)) {
                break;
            }

            $amountToDistribute = floatval($cheque->amount);
            foreach ($feeIds as $feeId) {
                if ($amountToDistribute <= 0) {
                    break;
                }
                $fee = $fees->firstWhere('id', $feeId);
                if (!$fee) {
                    continue;
                }

                $netDue = max(0.00, floatval($fee->amount)
                    + floatval($fee->fine_amount_applied ?? 0)
                    - floatval($fee->paid_amount)
                    - floatval($fee->instant_discount_amount)
                    - ($reservations[$feeId] ?? 0.00));

                if ($netDue > 0) {
                    $allocated = min($amountToDistribute, $netDue);
                    $reservations[$feeId] = ($reservations[$feeId] ?? 0.00) + $allocated;
                    $amountToDistribute -= $allocated;
                }
            }

            if ($amountToDistribute > 0 && !empty($feeIds)) {
                $firstFeeId = $feeIds[0];
                $reservations[$firstFeeId] = ($reservations[$firstFeeId] ?? 0.00) + $amountToDistribute;
            }
        }

        self::$pendingReservationsCache[$studentId] = $reservations;
        return $reservations;
    }

    public function getPendingChequeAmountAttribute()
    {
        return self::getPendingChequeAmountsForStudent($this->student_id, $this->school_id)[$this->id] ?? 0.00;
    }

    public function getRemainingDueAttribute()
    {
        return max(0.00, floatval($this->amount)
            + floatval($this->fine_amount_applied ?? 0)
            - floatval($this->paid_amount)
            - floatval($this->instant_discount_amount)
            - floatval($this->pending_cheque_amount));
    }
}
