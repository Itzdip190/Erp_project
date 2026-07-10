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
    }

    protected $fillable = [
        'school_id',
        'student_id',
        'fee_category_id',
        'fee_schedule_id',
        'fee_component_id',
        'installment_no',
        'amount',
        'due_date',
        'paid_amount',
        'instant_discount_amount',
        'instant_discount_type',
        'status',
        'invoice_no',
        'invoice_status',
    ];

    public function feeSchedule()
    {
        return $this->belongsTo(FeeSchedule::class, 'fee_schedule_id');
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

    public static function syncTransportFees($schoolId, $studentId = null)
    {
        // 1. Get student(s) who have transport opted in
        $query = \App\Models\Student::where('school_id', $schoolId)
            ->where('transport_opted', true)
            ->whereNotNull('transport_route');
            
        if ($studentId) {
            $query->where('id', $studentId);
        }
        
        $students = $query->get();

        // 2. Get or create Transport Category & Component
        $category = \App\Models\FeeCategory::firstOrCreate(
            ['school_id' => $schoolId, 'name' => 'Transport'],
            ['description' => 'Transport Fees']
        );

        $currentSession = \App\Models\AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first() 
            ?? \App\Models\AcademicSession::where('school_id', $schoolId)->first();

        $component = \App\Models\FeeComponent::firstOrCreate(
            ['school_id' => $schoolId, 'component_name' => 'Transport Fee'],
            [
                'academic_session_id' => $currentSession ? $currentSession->id : 1,
                'fee_category_id' => $category->id,
                'head_name' => 'Transport',
                'admission_type' => 'All Students',
                'gender' => 'All Students'
            ]
        );

        foreach ($students as $student) {
            $monthStr = $student->transport_month ?: date('F Y'); // e.g. "July 2026"
            
            // Calculate final transport fare based on attendance deductions
            $pickFare = (float) ($student->transport_pick_fare ?? 0);
            $dropFare = (float) ($student->transport_drop_fare ?? 0);

            // Parse month to get days and sundays, and compute integer installmentNo (1-12)
            $installmentNo = (int) date('n');
            $targetDate = now();
            if (!empty($monthStr)) {
                try {
                    $targetDate = \Carbon\Carbon::parse($monthStr);
                    $installmentNo = (int) $targetDate->month;
                } catch (\Exception $e) {
                    $targetDate = now();
                    $installmentNo = (int) $targetDate->month;
                }
            }

            $year = $targetDate->year;
            $month = $targetDate->month;
            $totalDays = $targetDate->daysInMonth;

            $sundays = 0;
            for ($d = 1; $d <= $totalDays; $d++) {
                $checkDate = \Carbon\Carbon::create($year, $month, $d);
                if ($checkDate->isSunday()) {
                    $sundays++;
                }
            }

            $billableDays = $totalDays - $sundays;
            if ($billableDays <= 0) $billableDays = 26;

            $dailyPickCost = $pickFare / $billableDays;
            $dailyDropCost = $dropFare / $billableDays;

            $startOfMonth = $targetDate->copy()->startOfMonth()->toDateString();
            $endOfMonth = $targetDate->copy()->endOfMonth()->toDateString();

            $attendances = \App\Models\BusAttendance::where('school_id', $schoolId)
                ->where('student_id', $student->id)
                ->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->get();

            $absentPickCount = $attendances->where('trip_type', 'pickup')->where('status', 'absent')->count();
            $absentDropCount = $attendances->where('trip_type', 'drop')->where('status', 'absent')->count();

            $pickDeduction = $absentPickCount * $dailyPickCost;
            $dropDeduction = $absentDropCount * $dailyDropCost;

            $finalPickFare = max(0, $pickFare - $pickDeduction);
            $finalDropFare = max(0, $dropFare - $dropDeduction);

            $finalAmount = $finalPickFare + $finalDropFare;

            // Find or create StudentFee record for this student and installment
            $studentFee = self::withoutGlobalScope('active')
                ->where('school_id', $schoolId)
                ->where('student_id', $student->id)
                ->where('fee_component_id', $component->id)
                ->where('installment_no', $installmentNo)
                ->first();

            if ($studentFee) {
                // If it is not paid, update the amount dynamically
                if ($studentFee->paid_amount <= 0 && empty($studentFee->invoice_status)) {
                    $studentFee->update([
                        'amount' => $finalAmount
                    ]);
                }
            } else {
                self::create([
                    'school_id' => $schoolId,
                    'student_id' => $student->id,
                    'fee_category_id' => $category->id,
                    'fee_component_id' => $component->id,
                    'installment_no' => $installmentNo,
                    'amount' => $finalAmount,
                    'due_date' => $targetDate->copy()->endOfMonth()->toDateString(),
                    'status' => 'unpaid'
                ]);
            }
        }

        // Clean up: If a student opted out of transport, delete their unpaid, non-invoiced transport fees
        $optedOutQuery = \App\Models\Student::where('school_id', $schoolId)
            ->where(function($q) {
                $q->where('transport_opted', false)
                  ->orWhereNull('transport_route');
            });
            
        if ($studentId) {
            $optedOutQuery->where('id', $studentId);
        }
        
        $optedOutStudents = $optedOutQuery->pluck('id');

        if ($optedOutStudents->isNotEmpty()) {
            self::withoutGlobalScope('active')
                ->where('school_id', $schoolId)
                ->whereIn('student_id', $optedOutStudents)
                ->where('fee_component_id', $component->id)
                ->where('paid_amount', '<=', 0)
                ->whereNull('invoice_no')
                ->delete();
        }
    }
}
