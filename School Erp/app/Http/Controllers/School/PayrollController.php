<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Event;
use App\Models\LeaveType;
use App\Models\PayrollDeductionSetting;
use App\Models\Staff;
use App\Models\StaffAttendance;
use App\Models\StaffAttendanceRegister;
use App\Models\StaffLeaveApplication;
use App\Models\StaffLeaveBalance;
use App\Models\StaffPayroll;
use App\Models\StaffPayrollDeposit;
use App\Models\StaffPayrollPayment;
use App\Models\StaffSalaryStructure;
use App\Support\SearchHelper;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PayrollController extends Controller
{
    /**
     * Helper to resolve active school ID considering impersonation / multi-tenancy session
     */
    protected function getSchoolId(): ?int
    {
        if (app()->bound('currentSchool') && app('currentSchool')) {
            return (int)app('currentSchool')->id;
        }

        if (session()->has('school_id') && session('school_id')) {
            return (int)session('school_id');
        }

        if (session()->has('school_code') && session('school_code')) {
            $s = \App\Models\School::where('code', session('school_code'))->first();
            if ($s) {
                return (int)$s->id;
            }
        }

        if (auth()->check() && auth()->user()->school_id) {
            return (int)auth()->user()->school_id;
        }

        return null;
    }

    /**
     * Helper to format payroll month input into a clean string e.g. "Aug 2026"
     */
    protected function parseMonthInput(?string $input): array
    {
        if ($input) {
            try {
                if (preg_match('/^(\d{4})-(\d{2})$/', $input, $m)) {
                    $date = Carbon::createFromDate((int)$m[1], (int)$m[2], 1);
                } else {
                    $date = Carbon::parse($input);
                }
            } catch (\Exception $e) {
                $date = Carbon::now();
            }
        } else {
            $date = Carbon::now();
        }

        return [
            'formatted' => $date->format('M Y'),   // "Aug 2026"
            'picker_val' => $date->format('Y-m'),  // "2026-08"
            'display_full' => $date->format('F Y'),// "August 2026"
            'month_name' => $date->format('F'),  // "August"
            'year' => $date->year,
            'month' => $date->month,
            'days_in_month' => $date->daysInMonth,
        ];
    }

    /**
     * 1. SALARY STRUCTURE MANAGEMENT (Listing Page)
     */
    public function salaryStructure(Request $request)
    {
        $schoolId = $this->getSchoolId();
        $search = $request->get('search');
        $deptId = $request->get('department_id');

        $departments = Department::where('school_id', $schoolId)->orderBy('name')->get();

        $query = StaffSalaryStructure::where('school_id', $schoolId)
            ->with(['staff.department', 'staff.designation']);

        if ($search) {
            $query->whereHas('staff', function ($q) use ($search) {
                SearchHelper::applyStaffSearch($q, $search);
            });
        }
        if ($deptId) {
            $query->whereHas('staff', function ($q) use ($deptId) {
                $q->where('department_id', $deptId);
            });
        }

        $salaryStructures = $query->latest('updated_at')->paginate(15)->appends($request->all());

        $totalMonthlyBudget = StaffSalaryStructure::where('school_id', $schoolId)->where('is_active', true)->sum('basic_salary');
        $configuredCount = StaffSalaryStructure::where('school_id', $schoolId)->where('is_active', true)->count();
        $avgSalary = $configuredCount > 0 ? round($totalMonthlyBudget / $configuredCount, 2) : 0;

        return view('school.payroll.salary-structure', [
            'salaryStructures' => $salaryStructures,
            'departments' => $departments,
            'totalMonthlyBudget' => $totalMonthlyBudget,
            'avgSalary' => $avgSalary,
            'configuredCount' => $configuredCount,
            'search' => $search,
            'deptId' => $deptId,
        ]);
    }

    /**
     * CONFIGURE PAYROLL FORM (Create/Edit Page)
     */
    public function configureSalaryStructure(Request $request)
    {
        $schoolId = $this->getSchoolId();
        $id = $request->get('id');
        $staffId = $request->get('staff_id');

        $activeStaff = Staff::where('school_id', $schoolId)
            ->where('is_active', true)
            ->with(['salaryStructure', 'department', 'designation'])
            ->orderBy('first_name')
            ->get();

        $structure = null;
        if ($id) {
            $structure = StaffSalaryStructure::where('school_id', $schoolId)->find($id);
        } elseif ($staffId) {
            $structure = StaffSalaryStructure::where('school_id', $schoolId)->where('staff_id', $staffId)->first();
        }

        return view('school.payroll.configure-salary-structure', [
            'activeStaff' => $activeStaff,
            'structure' => $structure,
            'selectedStaffId' => $staffId ?? $structure?->staff_id,
        ]);
    }

    /**
     * STORE / UPDATE SALARY STRUCTURE
     */
    public function storeSalaryStructure(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'basic_salary' => 'required|numeric|min:0',
            'salary_type' => 'required|string|in:Monthly,Daily,Hourly,Contract',
            'effective_from' => 'required|date',
            'hra' => 'nullable|numeric|min:0',
            'da' => 'nullable|numeric|min:0',
            'ta' => 'nullable|numeric|min:0',
            'allowance' => 'nullable|numeric|min:0',
            'pf' => 'nullable|numeric|min:0',
            'esi' => 'nullable|numeric|min:0',
            'tds' => 'nullable|numeric|min:0',
            'prof_tax' => 'nullable|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $schoolId = $this->getSchoolId();
        $staff = Staff::where('school_id', $schoolId)->findOrFail($request->staff_id);

        $isActive = $request->has('is_active') ? (bool)$request->is_active : true;

        $structure = StaffSalaryStructure::updateOrCreate(
            [
                'school_id' => $schoolId,
                'staff_id' => $staff->id,
            ],
            [
                'basic_salary' => (float)$request->basic_salary,
                'salary_type' => $request->salary_type,
                'hra' => (float)($request->hra ?: 0),
                'da' => (float)($request->da ?: 0),
                'ta' => (float)($request->ta ?: 0),
                'allowance' => (float)($request->allowance ?: 0),
                'pf' => (float)($request->pf ?: 0),
                'esi' => (float)($request->esi ?: 0),
                'tds' => (float)($request->tds ?: 0),
                'prof_tax' => (float)($request->prof_tax ?: 0),
                'effective_from' => $request->effective_from,
                'is_active' => $isActive,
                'updated_by' => auth()->id(),
                'created_by' => auth()->id(),
            ]
        );

        // Sync basic_salary and additional_fields on Staff model for backward compatibility
        $staff->basic_salary = (float)$request->basic_salary;
        $additional = $staff->additional_fields ?: [];
        $additional['salary_structure'] = [
            'salary_type' => $request->salary_type,
            'hra' => (float)($request->hra ?: 0),
            'da' => (float)($request->da ?: 0),
            'ta' => (float)($request->ta ?: 0),
            'allowance' => (float)($request->allowance ?: 0),
            'pf' => (float)($request->pf ?: 0),
            'esi' => (float)($request->esi ?: 0),
            'tds' => (float)($request->tds ?: 0),
            'prof_tax' => (float)($request->prof_tax ?: 0),
            'effective_from' => $request->effective_from,
        ];
        $staff->additional_fields = $additional;
        $staff->save();

        return redirect()->route('school.payroll.salary-structure')
            ->with('success', 'Salary structure configured successfully for ' . $staff->full_name);
    }


    /**
     * 2. DEPOSIT AMOUNT (EMPLOYEE SEARCH & PAYROLL ACCOUNT DEPOSIT)
     */
    public function depositAmount(Request $request)
    {
        $schoolId = $this->getSchoolId();
        $staffId = $request->get('staff_id');
        $employeeIdInput = trim($request->get('employee_id', ''));
        $search = trim($request->get('search', ''));

        $staffList = Staff::where('school_id', $schoolId)
            ->where('is_active', true)
            ->with(['department', 'designation'])
            ->orderBy('first_name')
            ->get();

        $selectedStaff = null;

        if ($staffId) {
            $selectedStaff = Staff::where('school_id', $schoolId)
                ->with(['department', 'designation', 'salaryStructure'])
                ->find($staffId);
        } elseif ($employeeIdInput !== '') {
            $selectedStaff = Staff::where('school_id', $schoolId)
                ->where(function($q) use ($employeeIdInput) {
                    $q->where('employee_id', $employeeIdInput)
                      ->orWhere('id', $employeeIdInput);
                })
                ->with(['department', 'designation', 'salaryStructure'])
                ->first();
        } elseif ($search !== '') {
            $selectedStaff = Staff::where('school_id', $schoolId)
                ->where(function($q) use ($search) {
                    SearchHelper::applyStaffSearch($q, $search);
                })
                ->with(['department', 'designation', 'salaryStructure'])
                ->first();
        }

        $isSearchAttempted = ($request->has('staff_id') && $staffId) || $employeeIdInput !== '' || $search !== '';

        if ($isSearchAttempted && !$selectedStaff) {
            return redirect()->route('school.payroll.deposit-amount')
                ->with('error', 'Employee not found with the provided details. Please select or enter a valid Employee ID or Name.');
        }

        $currentBalance = 0.00;
        $depositHistory = collect();
        $configuredSalary = 0.00;
        $isPayrollGenerated = false;

        if ($selectedStaff) {
            $currentBalance = $selectedStaff->payroll_balance;

            // Fetch net payable salary: Priority 1: latest generated payroll net_payable, Priority 2: salary structure net_salary, Priority 3: staff basic_salary
            $latestPayroll = StaffPayroll::where('school_id', $schoolId)
                ->where('staff_id', $selectedStaff->id)
                ->latest('id')
                ->first();

            if ($latestPayroll && (float)$latestPayroll->net_payable > 0) {
                $configuredSalary = (float)$latestPayroll->net_payable;
            } elseif ($selectedStaff->salaryStructure) {
                $configuredSalary = (float)$selectedStaff->salaryStructure->net_salary;
            } else {
                $configuredSalary = (float)($selectedStaff->basic_salary ?: 0);
            }

            $depositHistory = StaffPayrollDeposit::where('school_id', $schoolId)
                ->where('staff_id', $selectedStaff->id)
                ->with('creator')
                ->latest('id')
                ->get();

            // MANDATORY BUSINESS RULE: Verify Payroll Generated = YES
            $isPayrollGenerated = StaffPayroll::where('school_id', $schoolId)
                ->where('staff_id', $selectedStaff->id)
                ->exists();
        }

        return view('school.payroll.deposit-amount', [
            'staffList' => $staffList,
            'selectedStaff' => $selectedStaff,
            'currentBalance' => $currentBalance,
            'configuredSalary' => $configuredSalary,
            'depositHistory' => $depositHistory,
            'employeeIdInput' => $employeeIdInput,
            'search' => $search,
            'isPayrollGenerated' => $isPayrollGenerated,
        ]);
    }

    /**
     * STORE DEPOSIT AMOUNT TRANSACTION
     */
    public function storeDepositAmount(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'amount' => 'required|numeric|gt:0',
            'payment_mode' => 'required|string|in:Cash,Bank Transfer,UPI,Cheque,Other',
            'transaction_type' => 'required|string|in:Salary Advance,Deposit,Adjustment,Other',
            'remark' => 'nullable|string|max:1000',
        ]);

        $schoolId = $this->getSchoolId();
        $staffId = $request->staff_id;

        $staff = Staff::where('school_id', $schoolId)->with('salaryStructure')->findOrFail($staffId);

        $hasPayrollGenerated = StaffPayroll::where('school_id', $schoolId)
            ->where('staff_id', $staffId)
            ->exists();

        if (!$hasPayrollGenerated && !$staff->salaryStructure && (float)($staff->basic_salary ?: 0) <= 0) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Please generate payroll first. Salary deposit is not allowed without generated payroll.');
        }
        
        $latestPayroll = StaffPayroll::where('school_id', $schoolId)
            ->where('staff_id', $staffId)
            ->latest('id')
            ->first();

        if ($latestPayroll && (float)$latestPayroll->net_payable > 0) {
            $configuredSalary = (float)$latestPayroll->net_payable;
        } elseif ($staff->salaryStructure) {
            $configuredSalary = (float)$staff->salaryStructure->net_salary;
        } else {
            $configuredSalary = (float)($staff->basic_salary ?: 0);
        }

        if ($configuredSalary <= 0) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Salary structure is not configured for ' . $staff->full_name . '. Please configure salary structure first.');
        }

        if ((float)$request->amount > $configuredSalary) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Deposit amount (₹' . number_format($request->amount, 2) . ') cannot exceed the net payable salary of ₹' . number_format($configuredSalary, 2) . '.');
        }

        DB::beginTransaction();
        try {
            $lastDeposit = StaffPayrollDeposit::where('school_id', $schoolId)
                ->where('staff_id', $staffId)
                ->latest('id')
                ->first();

            $prevBalance = $lastDeposit ? (float)$lastDeposit->balance_after_transaction : 0.00;
            $newBalance = $prevBalance + (float)$request->amount;

            $deposit = StaffPayrollDeposit::create([
                'school_id' => $schoolId,
                'staff_id' => $staffId,
                'amount' => (float)$request->amount,
                'payment_mode' => $request->payment_mode,
                'transaction_type' => $request->transaction_type,
                'remark' => $request->remark,
                'balance_after_transaction' => $newBalance,
                'status' => 'completed',
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            // Sync with latest StaffPayroll record and update Payment Status to Paid
            $payroll = StaffPayroll::where('school_id', $schoolId)
                ->where('staff_id', $staffId)
                ->latest('id')
                ->first();

            if ($payroll) {
                StaffPayrollPayment::create([
                    'school_id' => $schoolId,
                    'staff_payroll_id' => $payroll->id,
                    'staff_id' => $staffId,
                    'payment_type' => strtolower(str_replace(' ', '_', $request->transaction_type)) === 'salary_advance' ? 'advance_payment' : 'salary_payment',
                    'amount' => $request->amount,
                    'payment_date' => now()->format('Y-m-d'),
                    'payment_method' => strtolower(str_replace(' ', '_', $request->payment_mode)),
                    'reference_no' => 'DEP-' . str_pad($deposit->id, 6, '0', STR_PAD_LEFT),
                    'notes' => "[{$request->transaction_type}] " . ($request->remark ?: ''),
                    'created_by' => auth()->id(),
                ]);

                // Automatically change Payment Status from Unpaid to Paid inside Payroll List
                $payroll->paid_amount = (float)$payroll->paid_amount + (float)$request->amount;
                $payroll->remaining_balance = max(0, (float)$payroll->net_payable - (float)$payroll->paid_amount);
                $payroll->payment_status = 'paid';
                $payroll->save();
            }

            DB::commit();

            return redirect()->route('school.payroll.deposit-amount', ['staff_id' => $staffId])
                ->with('success', 'Amount of ₹' . number_format($request->amount, 2) . ' deposited successfully for ' . $staff->full_name . '. Payment status updated to Paid.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to store deposit transaction: ' . $e->getMessage());
        }
    }

    /**
     * 3. GENERATE PAYROLL PAGE & BATCH GENERATION LOGIC
     */
    public function generatePayrollIndex(Request $request)
    {
        $schoolId = $this->getSchoolId();

        $months = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];

        $selectedMonth = $request->get('salary_month', date('F'));
        $selectedYear = (int)$request->get('salary_year', date('Y'));

        $currentYear = (int)date('Y');
        $years = range($currentYear - 2, $currentYear + 5);

        $monthNumbers = [
            'january' => 1, 'february' => 2, 'march' => 3, 'april' => 4,
            'may' => 5, 'june' => 6, 'july' => 7, 'august' => 8,
            'september' => 9, 'october' => 10, 'november' => 11, 'december' => 12,
            'jan' => 1, 'feb' => 2, 'mar' => 3, 'apr' => 4,
            'jun' => 6, 'jul' => 7, 'aug' => 8,
            'sep' => 9, 'sept' => 9, 'oct' => 10, 'nov' => 11, 'dec' => 12,
        ];

        // Payroll History aggregated by month & year, sorted chronologically descending
        $rawHistory = StaffPayroll::where('school_id', $schoolId)
            ->select(
                'payroll_month',
                'salary_month',
                'salary_year',
                DB::raw('COUNT(DISTINCT staff_id) as total_employees'),
                DB::raw('MIN(created_at) as generated_on')
            )
            ->groupBy('payroll_month', 'salary_month', 'salary_year')
            ->get();

        $history = $rawHistory->sortByDesc(function ($item) use ($monthNumbers) {
            $year = (int)($item->salary_year ?: date('Y', strtotime($item->payroll_month)));
            $mStr = strtolower(trim($item->salary_month ?: explode(' ', $item->payroll_month)[0]));
            $mNum = $monthNumbers[$mStr] ?? (int)date('n', strtotime($item->payroll_month));
            return ($year * 100) + $mNum;
        })->values();

        return view('school.payroll.generate-payroll', [
            'months' => $months,
            'years' => $years,
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
            'history' => $history,
        ]);
    }

    public function processGenerateSalary(Request $request)
    {
        if ($request->has('payroll_month') && (!$request->has('salary_month') || !$request->has('salary_year'))) {
            $mInfo = $this->parseMonthInput($request->payroll_month);
            $request->merge([
                'salary_month' => $mInfo['month_name'],
                'salary_year' => (int)$mInfo['year'],
            ]);
        }

        $request->validate([
            'salary_month' => 'required|string',
            'salary_year' => 'required|integer|min:2020|max:2050',
        ]);

        $schoolId = $this->getSchoolId();
        $salaryMonth = trim($request->salary_month);
        $salaryYear = (int)$request->salary_year;
        $mInfo = $this->parseMonthInput("{$salaryMonth} {$salaryYear}");
        $payrollMonth = $mInfo['display_full'];
        $shortMonth = substr($salaryMonth, 0, 3);

        // 1. Direct DB deletion of any existing payroll for this school & month (100% clean wipe before regeneration)
        try {
            DB::table('staff_payroll_payments')
                ->whereIn('staff_payroll_id', function($q) use ($schoolId, $payrollMonth, $salaryMonth, $shortMonth, $salaryYear) {
                    $q->select('id')
                      ->from('staff_payrolls')
                      ->where('school_id', $schoolId)
                      ->where(function($sub) use ($payrollMonth, $salaryMonth, $shortMonth, $salaryYear) {
                          $sub->where('payroll_month', $payrollMonth)
                              ->orWhere('payroll_month', 'like', $shortMonth . ' ' . $salaryYear . '%')
                              ->orWhere('payroll_month', 'like', $salaryMonth . ' ' . $salaryYear . '%')
                              ->orWhere(function($inner) use ($salaryMonth, $salaryYear) {
                                  $inner->where('salary_month', $salaryMonth)
                                        ->where('salary_year', $salaryYear);
                              });
                      });
                })
                ->delete();

            DB::table('staff_payrolls')
                ->where('school_id', $schoolId)
                ->where(function($q) use ($payrollMonth, $salaryMonth, $shortMonth, $salaryYear) {
                    $q->where('payroll_month', $payrollMonth)
                      ->orWhere('payroll_month', 'like', $shortMonth . ' ' . $salaryYear . '%')
                      ->orWhere('payroll_month', 'like', $salaryMonth . ' ' . $salaryYear . '%')
                      ->orWhere(function($inner) use ($salaryMonth, $salaryYear) {
                          $inner->where('salary_month', $salaryMonth)
                                ->where('salary_year', $salaryYear);
                      });
                })
                ->delete();
        } catch (\Exception $e) {
            // Ignore if tables are empty
        }

        // 2. Fetch active staff
        $activeStaff = Staff::where('school_id', $schoolId)
            ->where('is_active', true)
            ->with('salaryStructure')
            ->get()
            ->unique('id');

        if ($activeStaff->isEmpty()) {
            return redirect()->back()->with('error', 'No active employees found to generate payroll for this school.');
        }

        DB::beginTransaction();
        try {
            $generatedCount = 0;
            $insertedStaffIds = [];

            // Fetch verified attendance details for the salary month to calculate exact attendance deductions
            $attDetails = $this->computePayrollAttendanceDetails($schoolId, $salaryMonth, $salaryYear);
            $staffAttMap = [];
            foreach ($attDetails['department_cards'] as $deptCard) {
                foreach ($deptCard['staff_rows'] as $r) {
                    $staffAttMap[$r['staff_id']] = $r;
                }
            }

            $deductionSetting = PayrollDeductionSetting::getForSchool($schoolId);
            $multiplier = $deductionSetting->is_active ? (float)$deductionSetting->deduction_multiplier : 1.00;

            foreach ($activeStaff as $staff) {
                // Prevent duplicate staff ID in same loop
                if (in_array($staff->id, $insertedStaffIds)) {
                    continue;
                }

                $struct = $staff->salaryStructure;

                $basicSalary = $struct ? (float)$struct->basic_salary : (float)($staff->basic_salary ?: 0);
                if ($basicSalary <= 0) {
                    continue;
                }

                $hra = $struct ? (float)($struct->hra ?: 0) : 0;
                $da = $struct ? (float)($struct->da ?: 0) : 0;
                $ta = $struct ? (float)($struct->ta ?: 0) : 0;
                $allowance = $struct ? (float)($struct->allowance ?: 0) : 0;
                $pf = $struct ? (float)($struct->pf ?: 0) : 0;
                $esi = $struct ? (float)($struct->esi ?: 0) : 0;
                $tds = $struct ? (float)($struct->tds ?: 0) : 0;
                $profTax = $struct ? (float)($struct->prof_tax ?: 0) : 0;

                $attRow = $staffAttMap[$staff->id] ?? null;
                $extraLeaveDays = $attRow ? (float)$attRow['extra_leave_days'] : 0.00;
                $presentDays = $attRow ? (float)$attRow['present_days'] : 30.00;
                $absentDays = $attRow ? (float)$attRow['absent_days'] : 0.00;
                $leaveDays = $attRow ? (float)($attRow['paid_leaves'] + $attRow['unpaid_leaves']) : 0.00;
                $halfDays = $attRow ? (float)$attRow['half_days'] : 0.00;
                $payableDays = $attRow ? (float)$attRow['effective_present'] : 30.00;
                $totalDaysInMonth = $attDetails['days_in_month'] ?? 30;

                // Fixed 30 Days Cycle Base Calculation: Daily Salary = Basic Salary / 30
                $dailySalary = round($basicSalary / 30, 4);
                $attendanceDeduction = round($extraLeaveDays * $dailySalary * $multiplier, 2);

                $totalAllowances = $hra + $da + $ta + $allowance;
                $otherDeductions = $pf + $esi + $tds + $profTax;
                $totalDeductions = $otherDeductions + $attendanceDeduction;
                $grossSalary = $basicSalary + $totalAllowances;
                $netPayable = max(0, $grossSalary - $totalDeductions);

                StaffPayroll::create([
                    'school_id' => $schoolId,
                    'staff_id' => $staff->id,
                    'payroll_month' => $payrollMonth,
                    'salary_month' => $salaryMonth,
                    'salary_year' => $salaryYear,
                    'total_days' => $totalDaysInMonth,
                    'present_days' => $presentDays,
                    'absent_days' => $absentDays,
                    'leave_days' => $leaveDays,
                    'half_days' => $halfDays,
                    'payable_days' => $payableDays,
                    'basic_salary' => $basicSalary,
                    'allowances' => $totalAllowances,
                    'deductions' => $totalDeductions,
                    'attendance_deduction' => $attendanceDeduction,
                    'attendance_deduction_days' => $extraLeaveDays,
                    'attendance_deduction_multiplier' => $multiplier,
                    'gross_salary' => $grossSalary,
                    'net_payable' => $netPayable,
                    'paid_amount' => 0.00,
                    'remaining_balance' => $netPayable,
                    'status' => 'finalised',
                    'payment_status' => 'unpaid',
                    'generated_by' => auth()->id(),
                    'finalised_at' => now(),
                ]);

                $insertedStaffIds[] = $staff->id;
                $generatedCount++;
            }

            if ($generatedCount === 0) {
                DB::rollBack();
                return redirect()->back()
                    ->with('error', 'No active employees have a configured Salary Structure. Please configure salary structures first.');
            }

            DB::commit();

            $pickerVal = $mInfo['picker_val'];
            return redirect()->route('school.payroll.finalised', ['month' => $pickerVal])
                ->with('success', "Payroll for {$payrollMonth} generated successfully for {$generatedCount} active employee(s).");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error generating payroll: ' . $e->getMessage());
        }
    }

    /**
     * DELETE GENERATED PAYROLL BATCH (Remove only generated payroll records of that month, do NOT delete salary structures)
     */
    public function deleteGeneratedPayroll(Request $request)
    {
        $request->validate([
            'payroll_month' => 'required|string',
        ]);

        $schoolId = $this->getSchoolId();
        $payrollMonth = trim($request->payroll_month);
        $parts = explode(' ', $payrollMonth);
        $monthName = $parts[0] ?? '';
        $year = (int)($parts[1] ?? date('Y'));
        $shortMonth = substr($monthName, 0, 3);

        DB::beginTransaction();
        try {
            // Delete payments linked to these payrolls
            DB::table('staff_payroll_payments')
                ->whereIn('staff_payroll_id', function($q) use ($schoolId, $payrollMonth, $monthName, $shortMonth, $year) {
                    $q->select('id')
                      ->from('staff_payrolls')
                      ->where('school_id', $schoolId)
                      ->where(function($sub) use ($payrollMonth, $monthName, $shortMonth, $year) {
                          $sub->where('payroll_month', $payrollMonth)
                              ->orWhere('payroll_month', 'like', $shortMonth . ' ' . $year . '%')
                              ->orWhere('payroll_month', 'like', $monthName . ' ' . $year . '%')
                              ->orWhere(function($inner) use ($monthName, $year) {
                                  $inner->where('salary_month', $monthName)
                                        ->where('salary_year', $year);
                              });
                      });
                })
                ->delete();

            // Delete payrolls directly from database
            $deletedCount = DB::table('staff_payrolls')
                ->where('school_id', $schoolId)
                ->where(function($sub) use ($payrollMonth, $monthName, $shortMonth, $year) {
                    $sub->where('payroll_month', $payrollMonth)
                        ->orWhere('payroll_month', 'like', $shortMonth . ' ' . $year . '%')
                        ->orWhere('payroll_month', 'like', $monthName . ' ' . $year . '%')
                        ->orWhere(function($inner) use ($monthName, $year) {
                            $inner->where('salary_month', $monthName)
                                  ->where('salary_year', $year);
                        });
                })
                ->delete();

            DB::commit();

            return redirect()->route('school.payroll.generate-payroll')
                ->with('success', "Generated payroll records for {$payrollMonth} deleted successfully ({$deletedCount} records removed).");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to delete generated payroll: ' . $e->getMessage());
        }
    }

    /**
     * 4. PAYROLL LIST PAGE
     */
    public function payrollList(Request $request)
    {
        $schoolId = $this->getSchoolId();

        $monthNumbers = [
            'january' => 1, 'february' => 2, 'march' => 3, 'april' => 4,
            'may' => 5, 'june' => 6, 'july' => 7, 'august' => 8,
            'september' => 9, 'october' => 10, 'november' => 11, 'december' => 12,
            'jan' => 1, 'feb' => 2, 'mar' => 3, 'apr' => 4,
            'jun' => 6, 'jul' => 7, 'aug' => 8,
            'sep' => 9, 'sept' => 9, 'oct' => 10, 'nov' => 11, 'dec' => 12,
        ];

        $rawAvailableMonths = StaffPayroll::where('school_id', $schoolId)
            ->select('payroll_month', 'salary_month', 'salary_year')
            ->distinct()
            ->get();

        $availableMonths = $rawAvailableMonths->sortBy(function ($item) use ($monthNumbers) {
            $year = (int)($item->salary_year ?: date('Y', strtotime($item->payroll_month)));
            $mStr = strtolower(trim($item->salary_month ?: explode(' ', $item->payroll_month)[0]));
            $mNum = $monthNumbers[$mStr] ?? (int)date('n', strtotime($item->payroll_month));
            return ($year * 100) + $mNum;
        })->values();

        $monthYearInput = $request->get('month_year');
        $selectedMonth = $request->get('salary_month');
        $selectedYear = $request->get('salary_year');
        $search = trim($request->get('search', ''));

        // If month parameter like "2026-08" or "Aug 2026" is passed
        if ($request->has('month') && !$selectedMonth) {
            $mInfo = $this->parseMonthInput($request->get('month'));
            $selectedMonth = $mInfo['month_name'];
            $selectedYear = (string)$mInfo['year'];
        }

        // If month_year like "August - 2026" or "August 2026" is passed
        if ($monthYearInput && !$selectedMonth) {
            $parts = preg_split('/[\s\-]+/', trim($monthYearInput));
            if (count($parts) >= 2) {
                $selectedMonth = $parts[0];
                $selectedYear = $parts[1];
            } else {
                $selectedMonth = $monthYearInput;
            }
        }

        $hasSelectedMonth = $request->has('salary_month') || $request->has('month_year') || $request->has('view_list') || $request->has('search') || $request->has('month');

        $payrolls = collect();
        $totalGross = 0;
        $totalDeductions = 0;
        $totalNet = 0;

        if ($hasSelectedMonth) {
            $query = StaffPayroll::where('school_id', $schoolId)
                ->with(['staff.department', 'staff.designation', 'staff.salaryStructure']);

            if ($selectedMonth && $selectedMonth !== 'All') {
                $shortM = substr($selectedMonth, 0, 3);
                $query->where(function($q) use ($selectedMonth, $shortM) {
                    $q->where('salary_month', $selectedMonth)
                      ->orWhere('payroll_month', 'like', $selectedMonth . '%')
                      ->orWhere('payroll_month', 'like', $shortM . '%');
                });
            }

            if ($selectedYear && $selectedYear !== 'All') {
                $query->where(function($q) use ($selectedYear) {
                    $q->where('salary_year', $selectedYear)
                      ->orWhere('payroll_month', 'like', '%' . $selectedYear);
                });
            }

            if ($search !== '') {
                $query->whereHas('staff', function ($q) use ($search) {
                    SearchHelper::applyStaffSearch($q, $search);
                });
            }

            // Export handles
            if ($request->get('export') === 'excel') {
                return $this->exportPayrollListExcel($query->latest('id')->get(), $selectedMonth ?: 'All', $selectedYear ?: 'All');
            }
            if ($request->get('export') === 'pdf') {
                return $this->exportPayrollListPdf($query->latest('id')->get(), $selectedMonth ?: 'All', $selectedYear ?: 'All');
            }

            $payrolls = $query->latest('id')->paginate(20)->appends($request->all());
            $totalGross = $payrolls->sum('gross_salary');
            $totalDeductions = $payrolls->sum('deductions');
            $totalNet = $payrolls->sum('net_payable');
        }

        $months = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];
        $currentYear = (int)date('Y');
        $years = range($currentYear - 2, $currentYear + 5);

        return view('school.payroll.payroll-list', [
            'hasSelectedMonth' => $hasSelectedMonth,
            'availableMonths' => $availableMonths,
            'payrolls' => $payrolls,
            'months' => $months,
            'years' => $years,
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
            'monthYearInput' => $monthYearInput ?: ($selectedMonth ? "{$selectedMonth} - {$selectedYear}" : date('F - Y')),
            'search' => $search,
            'totalGross' => $totalGross,
            'totalDeductions' => $totalDeductions,
            'totalNet' => $totalNet,
        ]);
    }

    protected function exportPayrollListExcel($records, $month, $year)
    {
        $filename = 'payroll_list_' . strtolower($month) . '_' . $year . '_' . date('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($records) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, [
                'S.No', 'Employee ID', 'Employee Name', 'Department',
                'Designation', 'Salary Month', 'Gross Salary', 'Deduction',
                'Net Salary', 'Payment Status'
            ]);

            foreach ($records as $index => $row) {
                $st = $row->staff;
                fputcsv($file, [
                    $index + 1,
                    $st?->employee_id ?: 'EMP-' . $row->staff_id,
                    $st?->full_name ?: 'N/A',
                    $st?->department?->name ?: 'N/A',
                    $st?->designation?->name ?: 'N/A',
                    $row->payroll_month,
                    number_format($row->gross_salary, 2, '.', ''),
                    number_format($row->deductions, 2, '.', ''),
                    number_format($row->net_payable, 2, '.', ''),
                    strtolower($row->payment_status) === 'paid' ? 'Paid' : 'Unpaid',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    protected function exportPayrollListPdf($records, $month, $year)
    {
        $school = auth()->user()->school;
        return view('school.payroll.pdf-payroll-list', [
            'records' => $records,
            'month' => $month,
            'year' => $year,
            'school' => $school,
        ]);
    }

    public function finalisedSalaryIndex(Request $request)
    {
        return $this->payrollList($request);
    }

    /**
     * 5. SALARY PAYMENT DISBURSAL MODULE (2-Stage Workflow: Stage 1 = Select Month, Stage 2 = Payment List)
     */
    public function salaryPayment(Request $request)
    {
        $schoolId = $this->getSchoolId();

        $monthNumbers = [
            'january' => 1, 'february' => 2, 'march' => 3, 'april' => 4,
            'may' => 5, 'june' => 6, 'july' => 7, 'august' => 8,
            'september' => 9, 'october' => 10, 'november' => 11, 'december' => 12,
            'jan' => 1, 'feb' => 2, 'mar' => 3, 'apr' => 4,
            'jun' => 6, 'jul' => 7, 'aug' => 8,
            'sep' => 9, 'sept' => 9, 'oct' => 10, 'nov' => 11, 'dec' => 12,
        ];

        // PAGE 1 DATA LOADING LOGIC:
        // Only display months where payroll has already been generated AND at least one employee is still Unpaid.
        $rawUnpaidMonths = StaffPayroll::where('school_id', $schoolId)
            ->where(function($q) {
                $q->where('payment_status', '!=', 'paid')
                  ->orWhere('remaining_balance', '>', 0);
            })
            ->select('payroll_month', 'salary_month', 'salary_year')
            ->distinct()
            ->get();

        $unpaidMonths = $rawUnpaidMonths->sortBy(function ($item) use ($monthNumbers) {
            $year = (int)($item->salary_year ?: date('Y', strtotime($item->payroll_month)));
            $mStr = strtolower(trim($item->salary_month ?: explode(' ', $item->payroll_month)[0]));
            $mNum = $monthNumbers[$mStr] ?? (int)date('n', strtotime($item->payroll_month));
            return ($year * 100) + $mNum;
        })->values()->map(function($item) {
            $year = $item->salary_year ?: date('Y', strtotime($item->payroll_month));
            $month = $item->salary_month ?: explode(' ', $item->payroll_month)[0];
            return (object)[
                'salary_month' => $month,
                'salary_year' => $year,
                'payroll_month' => $item->payroll_month,
                'display_label' => "{$month} - {$year}",
                'value_key' => "{$month}-{$year}",
            ];
        });

        $monthYearInput = $request->get('month_year');
        $selectedMonth = $request->get('salary_month');
        $selectedYear = $request->get('salary_year');
        $search = trim($request->get('search', ''));

        // If month parameter like "2026-08" or "Aug 2026" is passed
        if ($request->has('month') && !$selectedMonth) {
            $mInfo = $this->parseMonthInput($request->get('month'));
            $selectedMonth = $mInfo['display_full'];
            $selectedYear = (string)$mInfo['year'];
        }

        // If month_year like "August - 2026" or "August-2026" is passed
        if ($monthYearInput && (!$selectedMonth || !$selectedYear)) {
            $parts = preg_split('/[\s\-]+/', trim($monthYearInput));
            if (count($parts) >= 2) {
                $selectedMonth = $parts[0];
                $selectedYear = $parts[1];
            } else {
                $selectedMonth = $monthYearInput;
            }
        }

        $hasSelectedMonth = !empty($selectedMonth) && !empty($selectedYear);

        $payrolls = collect();

        if ($hasSelectedMonth) {
            $query = StaffPayroll::where('school_id', $schoolId)
                ->with(['staff.department', 'staff.designation', 'staff.salaryStructure', 'payments.creator']);

            if ($selectedMonth && $selectedMonth !== 'All') {
                $query->where(function($q) use ($selectedMonth) {
                    $q->where('salary_month', $selectedMonth)
                      ->orWhere('payroll_month', 'like', $selectedMonth . '%');
                });
            }

            if ($selectedYear && $selectedYear !== 'All') {
                $query->where(function($q) use ($selectedYear) {
                    $q->where('salary_year', $selectedYear)
                      ->orWhere('payroll_month', 'like', '%' . $selectedYear);
                });
            }

            if ($search !== '') {
                $query->whereHas('staff', function ($q) use ($search) {
                    SearchHelper::applyStaffSearch($q, $search);
                    $q->orWhere('bank_name', 'like', "%{$search}%")
                      ->orWhere('bank_account_number', 'like', "%{$search}%")
                      ->orWhere('ifsc_code', 'like', "%{$search}%")
                      ->orWhere('pan_number', 'like', "%{$search}%");
                });
            }

            // Export handles
            if ($request->get('export') === 'excel') {
                return $this->exportSalaryPaymentExcel($query->latest('id')->get(), $selectedMonth ?: 'All', $selectedYear ?: 'All');
            }
            if ($request->get('export') === 'pdf') {
                return $this->exportSalaryPaymentPdf($query->latest('id')->get(), $selectedMonth ?: 'All', $selectedYear ?: 'All');
            }

            $payrolls = $query->latest('id')->paginate(20)->appends($request->all());
        }

        return view('school.payroll.salary-payment', [
            'hasSelectedMonth' => $hasSelectedMonth,
            'unpaidMonths' => $unpaidMonths,
            'payrolls' => $payrolls,
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
            'monthYearInput' => $monthYearInput ?: ($selectedMonth ? "{$selectedMonth} - {$selectedYear}" : ''),
            'search' => $search,
        ]);
    }

    public function addPaymentIndex(Request $request)
    {
        return $this->salaryPayment($request);
    }

    /**
     * PAY NOW / DISBURSE SALARY PAYMENT WITH STRICT VALIDATIONS
     */
    public function storePayment(Request $request)
    {
        if ($request->has('payroll_month') && (!$request->has('salary_month') || !$request->has('salary_year'))) {
            $mInfo = $this->parseMonthInput($request->payroll_month);
            $request->merge([
                'salary_month' => $mInfo['display_full'],
                'salary_year' => (string)$mInfo['year'],
            ]);
        }

        $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'staff_payroll_id' => 'nullable|exists:staff_payrolls,id',
            'salary_month' => 'nullable|string',
            'salary_year' => 'nullable|string',
            'payroll_month' => 'nullable|string',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
            'reference_no' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        $schoolId = $this->getSchoolId();
        $staffId = $request->staff_id;
        $staff = Staff::where('school_id', $schoolId)->with(['salaryStructure', 'department', 'designation'])->findOrFail($staffId);

        // VALIDATION 1: Check Payroll Generated
        $payroll = null;
        if ($request->filled('staff_payroll_id')) {
            $payroll = StaffPayroll::where('school_id', $schoolId)->where('id', $request->staff_payroll_id)->first();
        } else {
            $payrollMonthStr = $request->payroll_month ?: ($request->salary_month . ' ' . $request->salary_year);
            $formattedMonth = isset($mInfo) ? $mInfo['formatted'] : '';
            $monthName = isset($mInfo) ? $mInfo['month_name'] : $request->salary_month;
            $shortMonth = $monthName ? substr($monthName, 0, 3) : '';

            $payroll = StaffPayroll::where('school_id', $schoolId)
                ->where('staff_id', $staffId)
                ->where(function($q) use ($request, $payrollMonthStr, $formattedMonth, $monthName, $shortMonth) {
                    $q->where('payroll_month', $payrollMonthStr)
                      ->orWhere('payroll_month', $formattedMonth)
                      ->orWhere('payroll_month', 'like', $shortMonth . '%')
                      ->orWhere('payroll_month', 'like', $monthName . '%')
                      ->orWhere(function($sub) use ($request) {
                          if ($request->filled('salary_month')) {
                              $sub->where('salary_month', $request->salary_month);
                          }
                          if ($request->filled('salary_year')) {
                              $sub->where('salary_year', $request->salary_year);
                          }
                      });
                })
                ->latest('id')
                ->first();
        }

        if (!$payroll) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Payroll has not been generated.');
        }

        // VALIDATION 2: Check Already Paid
        if (strtolower($payroll->payment_status) === 'paid' || (float)$payroll->remaining_balance <= 0) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Salary already paid.');
        }

        // VALIDATION 3: Check Employee Salary Structure Exists
        if (!$staff->salaryStructure && (float)$staff->basic_salary <= 0) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Salary structure does not exist for ' . $staff->full_name . '. Please configure salary structure first.');
        }

        // OPTIONAL / INLINE BANK DETAILS UPDATE: If user updated bank details directly in modal
        $updatedBankData = [];
        if ($request->filled('bank_name')) {
            $updatedBankData['bank_name'] = trim($request->bank_name);
        }
        if ($request->filled('bank_account_number')) {
            $updatedBankData['bank_account_number'] = trim($request->bank_account_number);
        }
        if ($request->filled('ifsc_code')) {
            $updatedBankData['ifsc_code'] = trim($request->ifsc_code);
        }
        if ($request->has('pan_number')) {
            $updatedBankData['pan_number'] = trim($request->pan_number ?: '');
        }

        if (!empty($updatedBankData)) {
            $staff->update($updatedBankData);
            $staff->refresh();
        }

        // VALIDATION 4: Check Mandatory Bank Details Available
        $missingBankDetails = [];
        if (empty(trim($staff->bank_name ?? ''))) {
            $missingBankDetails[] = 'Bank Name';
        }
        if (empty(trim($staff->bank_account_number ?? ''))) {
            $missingBankDetails[] = 'Account Number';
        }
        if (empty(trim($staff->ifsc_code ?? ''))) {
            $missingBankDetails[] = 'IFSC Code';
        }

        if (!empty($missingBankDetails) && $request->has('check_bank_details')) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Employee bank information (' . implode(', ', $missingBankDetails) . ') is missing for ' . $staff->full_name . '. Please update employee bank details before disbursing payment.');
        }

        DB::beginTransaction();
        try {
            $amountToPay = (float)($request->amount ?: $payroll->remaining_balance);
            if ($amountToPay <= 0) {
                $amountToPay = (float)$payroll->net_payable;
            }

            $refNo = $request->reference_no ?: ('TXN-' . date('Ymd') . '-' . str_pad($payroll->id, 5, '0', STR_PAD_LEFT));

            $payment = StaffPayrollPayment::create([
                'school_id' => $schoolId,
                'staff_payroll_id' => $payroll->id,
                'staff_id' => $staffId,
                'payment_type' => 'salary_payment',
                'amount' => $amountToPay,
                'payment_date' => $request->payment_date ?: date('Y-m-d'),
                'payment_method' => $request->payment_method ?: 'bank_transfer',
                'reference_no' => $refNo,
                'notes' => $request->notes,
                'created_by' => auth()->id(),
            ]);

            // Update Payroll Record
            $payroll->paid_amount = (float)$payroll->paid_amount + $amountToPay;
            $payroll->remaining_balance = max(0, (float)$payroll->net_payable - (float)$payroll->paid_amount);
            $payroll->payment_status = $payroll->remaining_balance <= 0 ? 'paid' : 'partially_paid';
            $payroll->save();

            DB::commit();

            $pickerVal = isset($mInfo) ? $mInfo['picker_val'] : date('Y-m');
            return redirect()->route('school.payroll.finalised', ['month' => $pickerVal])
                ->with('success', 'Salary payment of ₹' . number_format($amountToPay, 2) . ' disbursed successfully for ' . $staff->full_name . '. Payment status updated to Paid.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to process salary payment: ' . $e->getMessage());
        }
    }

    protected function exportSalaryPaymentExcel($records, $month, $year)
    {
        $filename = 'salary_payment_list_' . strtolower($month) . '_' . $year . '_' . date('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($records) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, [
                'S.No', 'Salary Month', 'Employee ID', 'Employee Name',
                'Department', 'Designation', 'Bank Name', 'Account Number',
                'IFSC Code', 'PAN Number', 'Net Salary', 'Payment Status', 'Payment Date'
            ]);

            foreach ($records as $index => $row) {
                $st = $row->staff;
                $lastPayment = $row->payments?->last();
                $payDate = $lastPayment ? \Carbon\Carbon::parse($lastPayment->payment_date)->format('d M Y') : 'N/A';

                fputcsv($file, [
                    $index + 1,
                    $row->payroll_month,
                    $st?->employee_id ?: 'EMP-' . $row->staff_id,
                    $st?->full_name ?: 'N/A',
                    $st?->department?->name ?: 'N/A',
                    $st?->designation?->name ?: 'N/A',
                    $st?->bank_name ?: 'N/A',
                    $st?->bank_account_number ?: 'N/A',
                    $st?->ifsc_code ?: 'N/A',
                    $st?->pan_number ?: 'N/A',
                    number_format($row->net_payable, 2, '.', ''),
                    strtolower($row->payment_status) === 'paid' ? 'Paid' : 'Unpaid',
                    $payDate,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    protected function exportSalaryPaymentPdf($records, $month, $year)
    {
        $school = auth()->user()->school;
        return view('school.payroll.pdf-salary-payment', [
            'records' => $records,
            'month' => $month,
            'year' => $year,
            'school' => $school,
        ]);
    }


    /**
     * 6. PAYSLIP GENERATION, LIST & EXPORT MODULE
     */
    public function payslipIndex(Request $request)
    {
        $schoolId = $this->getSchoolId();
        $selectedStaffId = $request->get('staff_id');
        $employeeIdInput = trim($request->get('employee_id', ''));
        $search = trim($request->get('search', ''));
        $monthInfo = $this->parseMonthInput($request->get('month'));

        // Single PDF download request from index page if payroll_id is supplied
        if ($request->get('export') === 'pdf' && $request->has('payroll_id')) {
            return $this->generateSinglePayslipPdf($request->get('payroll_id'));
        }

        // Active staff list for dropdown selector
        $staffList = Staff::where('school_id', $schoolId)
            ->where('is_active', true)
            ->with(['department', 'designation'])
            ->orderBy('first_name')
            ->get();

        $selectedStaff = null;
        if ($selectedStaffId) {
            $selectedStaff = Staff::where('school_id', $schoolId)
                ->with(['department', 'designation', 'salaryStructure'])
                ->find($selectedStaffId);
        } elseif ($employeeIdInput !== '') {
            $selectedStaff = Staff::where('school_id', $schoolId)
                ->where(function($q) use ($employeeIdInput) {
                    $q->where('employee_id', $employeeIdInput)
                      ->orWhere('id', $employeeIdInput);
                })
                ->with(['department', 'designation', 'salaryStructure'])
                ->first();
        } elseif ($search !== '') {
            $selectedStaff = Staff::where('school_id', $schoolId)
                ->where(function($q) use ($search) {
                    SearchHelper::applyStaffSearch($q, $search);
                })
                ->with(['department', 'designation', 'salaryStructure'])
                ->first();
        }

        $isSearchAttempted = ($request->has('staff_id') && $selectedStaffId) || $employeeIdInput !== '' || $search !== '';

        $paidPayrolls = collect();

        if ($selectedStaff) {
            // STRICT BUSINESS RULE: Only display months for which salary has ALREADY been paid.
            $paidPayrolls = StaffPayroll::where('school_id', $schoolId)
                ->where('staff_id', $selectedStaff->id)
                ->where(function($q) {
                    $q->where('payment_status', 'paid')
                      ->orWhere('paid_amount', '>', 0);
                })
                ->with(['staff.department', 'staff.designation', 'payments'])
                ->latest('id')
                ->get();
        }

        // Handle Excel Export
        if ($request->get('export') === 'excel') {
            return $this->exportPayslipHistoryExcel($paidPayrolls, $selectedStaff);
        }

        // Handle PDF Export for full history list
        if ($request->get('export') === 'pdf') {
            return $this->exportPayslipHistoryPdf($paidPayrolls, $selectedStaff);
        }

        // Legacy compatibility: $selectedPayroll for single slip view if needed
        $selectedPayroll = $paidPayrolls->first();

        return view('school.payroll.payslip', [
            'monthInfo' => $monthInfo,
            'staffList' => $staffList,
            'selectedStaff' => $selectedStaff,
            'selectedStaffId' => $selectedStaff?->id ?: $selectedStaffId,
            'employeeIdInput' => $employeeIdInput,
            'search' => $search,
            'isSearchAttempted' => $isSearchAttempted,
            'paidPayrolls' => $paidPayrolls,
            'selectedPayroll' => $selectedPayroll,
        ]);
    }

    public function payslipView(Request $request, $id)
    {
        $schoolId = $this->getSchoolId();
        $payroll = StaffPayroll::where('school_id', $schoolId)
            ->with(['staff.department', 'staff.designation', 'payments', 'school'])
            ->findOrFail($id);

        if ($request->get('export') === 'pdf' || $request->has('download')) {
            return $this->generateSinglePayslipPdf($payroll->id);
        }

        $monthInfo = $this->parseMonthInput($payroll->payroll_month);
        $staffList = Staff::where('school_id', $schoolId)->where('is_active', true)->get();

        return view('school.payroll.payslip', [
            'monthInfo' => $monthInfo,
            'staffList' => $staffList,
            'selectedStaff' => $payroll->staff,
            'selectedStaffId' => $payroll->staff_id,
            'selectedPayroll' => $payroll,
            'paidPayrolls' => collect([$payroll]),
            'isSearchAttempted' => true,
        ]);
    }

    /**
     * Generate & Download Professional PDF Payslip
     */
    public function generateSinglePayslipPdf($payrollId)
    {
        $schoolId = $this->getSchoolId();
        $payroll = StaffPayroll::where('school_id', $schoolId)
            ->with(['staff.department', 'staff.designation', 'payments', 'school'])
            ->findOrFail($payrollId);

        $staff = $payroll->staff;
        $school = auth()->user()->school ?: $payroll->school;
        $struct = StaffSalaryStructure::where('school_id', $schoolId)->where('staff_id', $staff->id)->first();

        // Salary components breakdown
        $basicSalary = (float)$payroll->basic_salary;
        $hra = $struct ? (float)$struct->hra : (float)($staff?->additional_fields['salary_structure']['hra'] ?? 0);
        $da = $struct ? (float)$struct->da : (float)($staff?->additional_fields['salary_structure']['da'] ?? 0);
        $ta = $struct ? (float)$struct->ta : (float)($staff?->additional_fields['salary_structure']['ta'] ?? 0);
        $allowance = $struct ? (float)$struct->allowance : (float)($staff?->additional_fields['salary_structure']['allowance'] ?? 0);
        
        $pf = $struct ? (float)$struct->pf : (float)($staff?->additional_fields['salary_structure']['pf'] ?? 0);
        $esi = $struct ? (float)$struct->esi : (float)($staff?->additional_fields['salary_structure']['esi'] ?? 0);
        $profTax = $struct ? (float)$struct->prof_tax : (float)($staff?->additional_fields['salary_structure']['prof_tax'] ?? 0);
        $tds = $struct ? (float)$struct->tds : (float)($staff?->additional_fields['salary_structure']['tds'] ?? 0);
        
        $attendanceDeduction = (float)$payroll->attendance_deduction;
        $totalComponentAllowances = $hra + $da + $ta + $allowance;
        $totalComponentDeductions = $pf + $esi + $profTax + $tds;

        $otherDeductions = max(0, (float)$payroll->deductions - ($totalComponentDeductions + $attendanceDeduction));
        $grossSalary = (float)$payroll->gross_salary > 0 ? (float)$payroll->gross_salary : ($basicSalary + $totalComponentAllowances);
        $totalDeductions = (float)$payroll->deductions > 0 ? (float)$payroll->deductions : ($totalComponentDeductions + $attendanceDeduction + $otherDeductions);
        $netSalary = (float)$payroll->net_payable;

        $lastPayment = $payroll->payments?->last();
        $paymentDate = $lastPayment ? \Carbon\Carbon::parse($lastPayment->payment_date)->format('d M Y') : ($payroll->finalised_at ? $payroll->finalised_at->format('d M Y') : date('d M Y'));

        $netInWords = $this->convertNumberToWords($netSalary);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('school.payroll.pdf-payslip', [
            'payroll' => $payroll,
            'staff' => $staff,
            'school' => $school,
            'struct' => $struct,
            'basicSalary' => $basicSalary,
            'hra' => $hra,
            'da' => $da,
            'ta' => $ta,
            'allowance' => $allowance,
            'pf' => $pf,
            'esi' => $esi,
            'profTax' => $profTax,
            'tds' => $tds,
            'attendanceDeduction' => $attendanceDeduction,
            'otherDeductions' => $otherDeductions,
            'grossSalary' => $grossSalary,
            'totalDeductions' => $totalDeductions,
            'netSalary' => $netSalary,
            'netInWords' => $netInWords,
            'paymentDate' => $paymentDate,
            'generatedDate' => date('d M Y h:i A'),
        ])->setPaper('a4', 'portrait');

        $empCode = $staff?->employee_id ?: 'EMP-' . $payroll->staff_id;
        $filename = 'payslip_' . strtolower(str_replace(' ', '_', $payroll->payroll_month)) . '_' . $empCode . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Export Paid Payslips Excel Report
     */
    protected function exportPayslipHistoryExcel($records, $staff)
    {
        $school = auth()->user()->school;
        $empCode = $staff?->employee_id ?: ($staff ? 'EMP-' . $staff->id : 'ALL');
        $empName = $staff?->full_name ?: 'All Employees';
        $filename = 'payslip_history_' . str_replace(' ', '_', strtolower($empName)) . '_' . date('Ymd_His') . '.xls';

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($records, $staff, $school, $empName, $empCode) {
            echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
            echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
            echo '<style>';
            echo 'table { border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; font-size: 12px; }';
            echo 'th { background-color: #1e3a8a; color: #ffffff; font-weight: bold; border: 1px solid #0f172a; padding: 10px; text-align: center; }';
            echo 'td { border: 1px solid #cbd5e1; padding: 8px; text-align: left; vertical-align: middle; }';
            echo '.hdr-title { font-size: 18px; font-weight: bold; color: #1e3a8a; text-align: center; padding: 12px; }';
            echo '.hdr-sub { font-size: 13px; color: #475569; text-align: center; padding-bottom: 12px; }';
            echo '.num { text-align: right; }';
            echo '.bold { font-weight: bold; }';
            echo '.status-paid { color: #15803d; font-weight: bold; text-align: center; }';
            echo '</style>';
            echo '</head><body>';

            echo '<table>';
            echo '<tr><td colspan="10" class="hdr-title">' . htmlspecialchars($school?->name ?: 'SCHOOL ERP PAYROLL REPORT') . '</td></tr>';
            echo '<tr><td colspan="10" class="hdr-sub">PAID SALARY PAYSLIP HISTORY REPORT &mdash; ' . htmlspecialchars($empName) . ' (' . htmlspecialchars($empCode) . ')</td></tr>';
            echo '<tr><td colspan="10" style="height: 10px;"></td></tr>';

            echo '<tr>';
            echo '<th>S.No</th>';
            echo '<th>Salary Month</th>';
            echo '<th>Employee ID</th>';
            echo '<th>Employee Name</th>';
            echo '<th>Gross Salary</th>';
            echo '<th>Total Deduction</th>';
            echo '<th>Net Salary</th>';
            echo '<th>Payment Date</th>';
            echo '<th>Payment Status</th>';
            echo '<th>Payslip Status</th>';
            echo '</tr>';

            $totalGross = 0;
            $totalDed = 0;
            $totalNet = 0;

            foreach ($records as $index => $row) {
                $st = $row->staff;
                $lastPayment = $row->payments?->last();
                $payDate = $lastPayment ? \Carbon\Carbon::parse($lastPayment->payment_date)->format('d M Y') : ($row->finalised_at ? $row->finalised_at->format('d M Y') : 'N/A');

                $totalGross += (float)$row->gross_salary;
                $totalDed += (float)$row->deductions;
                $totalNet += (float)$row->net_payable;

                echo '<tr>';
                echo '<td style="text-align:center;">' . ($index + 1) . '</td>';
                echo '<td>' . htmlspecialchars($row->payroll_month) . '</td>';
                echo '<td>' . htmlspecialchars($st?->employee_id ?: 'EMP-' . $row->staff_id) . '</td>';
                echo '<td>' . htmlspecialchars($st?->full_name ?: 'N/A') . '</td>';
                echo '<td class="num">&#8377;' . number_format($row->gross_salary, 2) . '</td>';
                echo '<td class="num">&#8377;' . number_format($row->deductions, 2) . '</td>';
                echo '<td class="num bold">&#8377;' . number_format($row->net_payable, 2) . '</td>';
                echo '<td style="text-align:center;">' . $payDate . '</td>';
                echo '<td class="status-paid">PAID</td>';
                echo '<td style="text-align:center; color:#1e40af;">Generated</td>';
                echo '</tr>';
            }

            if ($records->isNotEmpty()) {
                echo '<tr style="background-color: #f1f5f9; font-weight: bold;">';
                echo '<td colspan="4" style="text-align: right; font-weight: bold;">TOTAL:</td>';
                echo '<td class="num bold">&#8377;' . number_format($totalGross, 2) . '</td>';
                echo '<td class="num bold">&#8377;' . number_format($totalDed, 2) . '</td>';
                echo '<td class="num bold" style="color: #1e3a8a;">&#8377;' . number_format($totalNet, 2) . '</td>';
                echo '<td colspan="3"></td>';
                echo '</tr>';
            }

            echo '</table>';
            echo '</body></html>';
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export Paid Payslips PDF Summary Report
     */
    protected function exportPayslipHistoryPdf($records, $staff)
    {
        $school = auth()->user()->school;
        $empCode = $staff?->employee_id ?: ($staff ? 'EMP-' . $staff->id : 'ALL');
        $empName = $staff?->full_name ?: 'All Employees';
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('school.payroll.pdf-payroll-list', [
            'records' => $records,
            'month' => 'Paid History',
            'year' => date('Y'),
            'school' => $school,
            'staff' => $staff,
        ])->setPaper('a4', 'landscape');

        $filename = 'payslips_summary_' . str_replace(' ', '_', strtolower($empName)) . '_' . date('Ymd') . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Helper to convert numeric salary amount into words (Indian Number System / Standard English)
     */
    protected function convertNumberToWords($number): string
    {
        $no = floor($number);
        $point = round(($number - $no) * 100);
        $hundred = null;
        $digits_1 = strlen($no);
        $i = 0;
        $str = array();
        $words = array(
            '0' => '', '1' => 'One', '2' => 'Two',
            '3' => 'Three', '4' => 'Four', '5' => 'Five', '6' => 'Six',
            '7' => 'Seven', '8' => 'Eight', '9' => 'Nine',
            '10' => 'Ten', '11' => 'Eleven', '12' => 'Twelve',
            '13' => 'Thirteen', '14' => 'Fourteen',
            '15' => 'Fifteen', '16' => 'Sixteen', '17' => 'Seventeen',
            '18' => 'Eighteen', '19' => 'Nineteen', '20' => 'Twenty',
            '30' => 'Thirty', '40' => 'Forty', '50' => 'Fifty',
            '60' => 'Sixty', '70' => 'Seventy', '80' => 'Eighty',
            '90' => 'Ninety'
        );
        $digits = array('', 'Hundred', 'Thousand', 'Lakh', 'Crore');
        while ($i < $digits_1) {
            $divider = ($i == 2) ? 10 : 100;
            $number = floor($no % $divider);
            $no = floor($no / $divider);
            $i += ($divider == 10) ? 1 : 2;
            if ($number) {
                $plural = (($counter = count($str)) && $number > 9) ? 's' : '';
                $hundred = ($counter == 1 && $str[0]) ? ' and ' : '';
                $str [] = ($number < 21) ? $words[$number] .
                    " " . $digits[$counter] . $plural . " " . $hundred
                    :
                    $words[floor($number / 10) * 10]
                    . " " . $words[$number % 10] . " "
                    . $digits[$counter] . $plural . " " . $hundred;
            } else $str[] = '';
        }
        $str = array_reverse($str);
        $result = implode('', $str);
        $points = ($point) ?
            " and " . ($words[$point / 10] . " " . $words[$point % 10]) . " Paise" : '';
        return trim($result) ? trim($result) . " Rupees Only" : "Zero Rupees";
    }

    /**
     * 7. ACCOUNT STATEMENT
     */
    public function accountStatement(Request $request)
    {
        $schoolId = $this->getSchoolId();

        $staffId = $request->get('staff_id');
        $employeeIdInput = trim($request->get('employee_id', ''));
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');
        $month = $request->get('month');
        $year = $request->get('year');
        $preset = $request->get('preset', '');
        $paymentMethodFilter = $request->get('payment_method', '');
        $search = trim($request->get('search', ''));

        // Fetch active academic session for school
        $activeSession = AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first();
        if (!$activeSession) {
            $activeSession = AcademicSession::where('school_id', $schoolId)->latest('id')->first();
        }
        $sessionName = $activeSession?->name ?: (date('m') >= 4 ? date('Y') . '-' . (date('Y') + 1) : (date('Y') - 1) . '-' . date('Y'));

        // Handle date preset shortcuts
        if ($preset === 'this_month') {
            $fromDate = Carbon::now()->startOfMonth()->toDateString();
            $toDate = Carbon::now()->endOfMonth()->toDateString();
        } elseif ($preset === 'last_month') {
            $fromDate = Carbon::now()->subMonth()->startOfMonth()->toDateString();
            $toDate = Carbon::now()->subMonth()->endOfMonth()->toDateString();
        } elseif ($preset === 'last_90_days') {
            $fromDate = Carbon::now()->subDays(90)->toDateString();
            $toDate = Carbon::now()->toDateString();
        } elseif ($preset === 'current_fy' || $preset === 'academic_year') {
            if ($activeSession && $activeSession->start_date && $activeSession->end_date) {
                $fromDate = Carbon::parse($activeSession->start_date)->toDateString();
                $toDate = Carbon::parse($activeSession->end_date)->toDateString();
            } else {
                $currentMonth = (int)date('m');
                $currentYear = (int)date('Y');
                $fyStart = $currentMonth >= 4 ? $currentYear : ($currentYear - 1);
                $fromDate = "{$fyStart}-04-01";
                $toDate = ($fyStart + 1) . "-03-31";
            }
        }

        // Active staff list for "Select Employee Name" dropdown
        $staffList = Staff::where('school_id', $schoolId)
            ->where('is_active', true)
            ->with(['department', 'designation'])
            ->orderBy('first_name')
            ->get();

        $selectedStaff = null;
        if ($staffId) {
            $selectedStaff = Staff::where('school_id', $schoolId)->with(['department', 'designation'])->find($staffId);
        } elseif ($employeeIdInput !== '') {
            $selectedStaff = Staff::where('school_id', $schoolId)
                ->where(function($q) use ($employeeIdInput) {
                    $q->where('employee_id', $employeeIdInput)
                      ->orWhere('id', $employeeIdInput);
                })
                ->with(['department', 'designation'])
                ->first();
        }

        $query = StaffPayrollPayment::where('school_id', $schoolId)
            ->with(['staff.department', 'staff.designation', 'creator']);

        if ($selectedStaff) {
            $query->where('staff_id', $selectedStaff->id);
        } elseif ($search !== '') {
            $query->whereHas('staff', function ($q) use ($search) {
                SearchHelper::applyStaffSearch($q, $search);
            });
        }

        if ($fromDate) {
            $query->whereDate('payment_date', '>=', $fromDate);
        }

        if ($toDate) {
            $query->whereDate('payment_date', '<=', $toDate);
        }

        if ($month && $month !== '' && $month !== 'All') {
            if (is_numeric($month)) {
                $query->whereMonth('payment_date', (int)$month);
            } else {
                try {
                    $mNum = Carbon::parse("1 {$month}")->month;
                    $query->whereMonth('payment_date', $mNum);
                } catch (\Exception $e) {
                    // ignore parse exception
                }
            }
        }

        if ($year && $year !== '' && $year !== 'All') {
            $query->whereYear('payment_date', (int)$year);
        }

        if ($paymentMethodFilter && $paymentMethodFilter !== 'All') {
            if ($paymentMethodFilter === 'bank') {
                $query->whereIn('payment_method', ['bank_transfer', 'bank']);
            } elseif ($paymentMethodFilter === 'cash') {
                $query->where('payment_method', 'cash');
            } elseif ($paymentMethodFilter === 'other') {
                $query->whereNotIn('payment_method', ['bank_transfer', 'bank', 'cash']);
            }
        }

        $statementList = $query->latest('payment_date')->latest('id')->get();

        // Export handlers
        if ($request->get('export') === 'excel') {
            return $this->exportAccountStatementExcel($statementList, $selectedStaff, $fromDate, $toDate, $month, $year);
        }

        if ($request->get('export') === 'pdf') {
            return $this->exportAccountStatementPdf($statementList, $selectedStaff, $fromDate, $toDate, $month, $year);
        }

        $totalDisbursed = $statementList->sum('amount');
        $bankDisbursed = $statementList->whereIn('payment_method', ['bank_transfer', 'bank'])->sum('amount');
        $cashDisbursed = $statementList->where('payment_method', 'cash')->sum('amount');
        $chequeUpiDisbursed = $totalDisbursed - ($bankDisbursed + $cashDisbursed);

        $months = [
            '1' => 'January', '2' => 'February', '3' => 'March', '4' => 'April',
            '5' => 'May', '6' => 'June', '7' => 'July', '8' => 'August',
            '9' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'
        ];

        $isSearchSubmitted = ($staffId && $staffId !== '') 
            || ($employeeIdInput && $employeeIdInput !== '') 
            || ($fromDate && $fromDate !== '') 
            || ($toDate && $toDate !== '') 
            || ($month && $month !== '') 
            || ($preset && $preset !== '') 
            || ($paymentMethodFilter && $paymentMethodFilter !== '') 
            || ($search && $search !== '') 
            || $request->has('view_all')
            || $request->has('submit_search');

        return view('school.payroll.account-statement', [
            'isSearchSubmitted' => $isSearchSubmitted,
            'staffList' => $staffList,
            'selectedStaff' => $selectedStaff,
            'selectedStaffId' => $selectedStaff?->id ?: $staffId,
            'employeeIdInput' => $employeeIdInput,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'month' => $month,
            'year' => $year,
            'preset' => $preset,
            'paymentMethodFilter' => $paymentMethodFilter,
            'sessionName' => $sessionName,
            'months' => $months,
            'statementList' => $statementList,
            'totalDisbursed' => $totalDisbursed,
            'bankDisbursed' => $bankDisbursed,
            'cashDisbursed' => $cashDisbursed,
            'chequeUpiDisbursed' => $chequeUpiDisbursed,
            'search' => $search,
        ]);
    }

    /**
     * Export Account Statement Excel (.xls)
     */
    protected function exportAccountStatementExcel($statementList, $selectedStaff, $fromDate, $toDate, $month, $year)
    {
        $school = auth()->user()->school;
        $empName = $selectedStaff ? $selectedStaff->full_name . ' (' . ($selectedStaff->employee_id ?: 'EMP-' . $selectedStaff->id) . ')' : 'All Employees';
        $filename = 'account_statement_' . str_replace(' ', '_', strtolower($empName)) . '_' . date('Ymd_His') . '.xls';

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($statementList, $selectedStaff, $school, $empName, $fromDate, $toDate, $month, $year) {
            echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
            echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
            echo '<style>';
            echo 'table { border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; font-size: 12px; }';
            echo 'th { background-color: #1e3a8a; color: #ffffff; font-weight: bold; border: 1px solid #0f172a; padding: 10px; text-align: center; }';
            echo 'td { border: 1px solid #cbd5e1; padding: 8px; text-align: left; vertical-align: middle; }';
            echo '.hdr-title { font-size: 18px; font-weight: bold; color: #1e3a8a; text-align: center; padding: 12px; }';
            echo '.hdr-sub { font-size: 13px; color: #475569; text-align: center; padding-bottom: 12px; }';
            echo '.num { text-align: right; }';
            echo '.bold { font-weight: bold; }';
            echo '</style>';
            echo '</head><body>';

            echo '<table>';
            echo '<tr><td colspan="7" class="hdr-title">' . htmlspecialchars($school?->name ?: 'SCHOOL ERP ACCOUNT STATEMENT') . '</td></tr>';
            echo '<tr><td colspan="7" class="hdr-sub">PAYROLL ACCOUNT STATEMENT &mdash; ' . htmlspecialchars($empName) . '</td></tr>';
            if ($fromDate || $toDate || $month || $year) {
                $filterStr = [];
                if ($fromDate) $filterStr[] = "From: {$fromDate}";
                if ($toDate) $filterStr[] = "To: {$toDate}";
                if ($month) $filterStr[] = "Month: {$month}";
                if ($year) $filterStr[] = "Year: {$year}";
                echo '<tr><td colspan="7" style="text-align:center; color:#64748b; font-size:11px;">Filter Criteria: ' . implode(' | ', $filterStr) . '</td></tr>';
            }
            echo '<tr><td colspan="7" style="height: 10px;"></td></tr>';

            echo '<tr>';
            echo '<th>Transaction Date</th>';
            echo '<th>Voucher / Ref No</th>';
            echo '<th>Staff Beneficiary</th>';
            echo '<th>Department</th>';
            echo '<th>Disbursal Type</th>';
            echo '<th>Payment Channel</th>';
            echo '<th>Debit Amount (&#8377;)</th>';
            echo '</tr>';

            $total = 0;
            foreach ($statementList as $index => $item) {
                $total += (float)$item->amount;
                $payDate = \Carbon\Carbon::parse($item->payment_date)->format('d M Y');
                $refNo = $item->reference_no ?: '#PAY-' . str_pad($item->id, 5, '0', STR_PAD_LEFT);
                $staffName = $item->staff?->full_name ?: 'N/A';
                $staffCode = $item->staff?->employee_id ?: 'EMP-' . $item->staff_id;
                $deptName = $item->staff?->department?->name ?: 'General';
                $type = ucfirst(str_replace('_', ' ', $item->payment_type));
                $method = strtoupper(str_replace('_', ' ', $item->payment_method));

                echo '<tr>';
                echo '<td style="text-align:center;">' . $payDate . '</td>';
                echo '<td>' . htmlspecialchars($refNo) . '</td>';
                echo '<td>' . htmlspecialchars($staffName) . ' (' . htmlspecialchars($staffCode) . ')</td>';
                echo '<td>' . htmlspecialchars($deptName) . '</td>';
                echo '<td>' . htmlspecialchars($type) . '</td>';
                echo '<td>' . htmlspecialchars($method) . '</td>';
                echo '<td class="num bold" style="color:#dc2626;">&#8377;' . number_format($item->amount, 2) . '</td>';
                echo '</tr>';
            }

            echo '<tr style="background-color: #f1f5f9; font-weight: bold;">';
            echo '<td colspan="6" style="text-align: right; font-weight: bold;">TOTAL OUTFLOW STATEMENT:</td>';
            echo '<td class="num bold" style="color: #dc2626;">&#8377;' . number_format($total, 2) . '</td>';
            echo '</tr>';

            echo '</table>';
            echo '</body></html>';
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export Account Statement PDF
     */
    protected function exportAccountStatementPdf($statementList, $selectedStaff, $fromDate, $toDate, $month, $year)
    {
        $school = auth()->user()->school;
        $totalDisbursed = $statementList->sum('amount');
        $bankDisbursed = $statementList->whereIn('payment_method', ['bank_transfer', 'bank'])->sum('amount');
        $cashDisbursed = $statementList->where('payment_method', 'cash')->sum('amount');
        $chequeUpiDisbursed = $totalDisbursed - ($bankDisbursed + $cashDisbursed);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('school.payroll.pdf-account-statement', [
            'statementList' => $statementList,
            'selectedStaff' => $selectedStaff,
            'school' => $school,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'month' => $month,
            'year' => $year,
            'totalDisbursed' => $totalDisbursed,
            'bankDisbursed' => $bankDisbursed,
            'cashDisbursed' => $cashDisbursed,
            'chequeUpiDisbursed' => $chequeUpiDisbursed,
            'generatedDate' => date('d M Y h:i A'),
        ])->setPaper('a4', 'landscape');

        $empName = $selectedStaff ? str_replace(' ', '_', strtolower($selectedStaff->full_name)) : 'all_staff';
        $filename = 'account_statement_' . $empName . '_' . date('Ymd') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * ATTENDANCE REGISTER SUPPORT METHOD
     */
    public function attendanceRegister(Request $request)
    {
        $schoolId = $this->getSchoolId();
        $monthInfo = $this->parseMonthInput($request->get('month'));
        $search = $request->get('search');

        $registerStatus = StaffAttendanceRegister::where('school_id', $schoolId)
            ->where('payroll_month', $monthInfo['formatted'])
            ->first();
        $isFrozen = $registerStatus ? $registerStatus->is_frozen : false;

        $query = Staff::where('school_id', $schoolId)
            ->where('is_active', true)
            ->with(['department', 'designation']);

        if ($search) {
            SearchHelper::applyStaffSearch($query, $search);
        }

        $staffList = $query->orderBy('first_name')->get();

        $attendances = StaffAttendance::where('school_id', $schoolId)
            ->whereYear('date', $monthInfo['year'])
            ->whereMonth('date', $monthInfo['month'])
            ->get()
            ->groupBy('staff_id');

        $registerData = [];
        foreach ($staffList as $staff) {
            $staffAtts = $attendances->get($staff->id, collect());
            
            $presentCount = 0;
            $absentCount = 0;
            $leaveCount = 0;
            $halfDayCount = 0;

            foreach ($staffAtts as $att) {
                $st = strtolower($att->status);
                if ($st === 'present' || $st === 'late') {
                    $presentCount++;
                } elseif ($st === 'absent') {
                    $absentCount++;
                } elseif ($st === 'leave' || $st === 'holiday') {
                    $leaveCount++;
                } elseif ($st === 'half_day') {
                    $halfDayCount++;
                }
            }

            $workOn = $presentCount + ($halfDayCount * 0.5);
            $payableDays = $presentCount + $leaveCount + ($halfDayCount * 0.5);

            $registerData[] = [
                'staff' => $staff,
                'emp_id' => $staff->employee_id ?: 'emp-' . str_pad($staff->id, 4, '0', STR_PAD_LEFT),
                'name_code' => $staff->full_name . ($staff->employee_id ? " ({$staff->employee_id})" : ''),
                'contact' => $staff->phone ?: 'N/A',
                'designation' => $staff->designation ? $staff->designation->name : 'Staff',
                'work_on' => $workOn,
                'absent' => $absentCount,
                'leave' => $leaveCount,
                'payable_days' => $payableDays,
                'total_days' => $monthInfo['days_in_month'],
            ];
        }

        return view('school.payroll.attendance-register', [
            'monthInfo' => $monthInfo,
            'registerData' => $registerData,
            'isFrozen' => $isFrozen,
            'search' => $search,
        ]);
    }

    public function freezeAttendanceRegister(Request $request)
    {
        $schoolId = $this->getSchoolId();
        $monthInfo = $this->parseMonthInput($request->get('month'));

        $register = StaffAttendanceRegister::firstOrCreate(
            ['school_id' => $schoolId, 'payroll_month' => $monthInfo['formatted']],
            ['is_frozen' => false]
        );

        $register->is_frozen = !$register->is_frozen;
        $register->frozen_at = $register->is_frozen ? now() : null;
        $register->frozen_by = $register->is_frozen ? auth()->id() : null;
        $register->save();

        $statusMsg = $register->is_frozen ? 'Attendance register frozen successfully.' : 'Attendance register unfrozen.';
        return redirect()->back()->with('success', $statusMsg);
    }

    /**
     * Get School Payroll Deduction Settings (AJAX / JSON API)
     */
    public function getDeductionSettings(Request $request)
    {
        $schoolId = $this->getSchoolId();
        $setting = PayrollDeductionSetting::getForSchool($schoolId);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $setting->id,
                'salary_calculation_base' => '30 Days',
                'deduction_rule' => $setting->deduction_rule ?: 'one_day',
                'deduction_multiplier' => (float)($setting->deduction_multiplier ?: 1.00),
                'effective_from' => $setting->effective_from ? $setting->effective_from->format('Y-m-d') : date('Y-m-01'),
                'is_active' => (bool)$setting->is_active,
            ]
        ]);
    }

    /**
     * Store or Update School Payroll Deduction Settings
     */
    public function storeDeductionSettings(Request $request)
    {
        $request->validate([
            'deduction_rule' => 'required|string|in:half_day,one_day,one_and_half_day,two_days,custom',
            'deduction_multiplier' => 'required|numeric|min:0',
            'effective_from' => 'required|date',
            'is_active' => 'nullable',
        ]);

        $schoolId = $this->getSchoolId();

        $ruleMultipliers = [
            'half_day' => 0.50,
            'one_day' => 1.00,
            'one_and_half_day' => 1.50,
            'two_days' => 2.00,
        ];

        $rule = $request->deduction_rule;
        $multiplier = ($rule === 'custom')
            ? (float)$request->deduction_multiplier
            : ($ruleMultipliers[$rule] ?? (float)$request->deduction_multiplier);

        if ($multiplier < 0) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Deduction multiplier cannot be negative.',
                ], 422);
            }
            return redirect()->back()->with('error', 'Deduction multiplier cannot be negative.');
        }

        $isActive = $request->boolean('is_active');

        $setting = PayrollDeductionSetting::updateOrCreate(
            [
                'school_id' => $schoolId,
            ],
            [
                'salary_calculation_base' => '30_days',
                'deduction_rule' => $rule,
                'deduction_multiplier' => $multiplier,
                'effective_from' => $request->effective_from,
                'is_active' => $isActive,
                'updated_by' => auth()->id(),
                'created_by' => auth()->id(),
            ]
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Payroll Deduction Settings updated successfully for your school.',
                'data' => [
                    'id' => $setting->id,
                    'salary_calculation_base' => '30 Days',
                    'deduction_rule' => $setting->deduction_rule,
                    'deduction_multiplier' => (float)$setting->deduction_multiplier,
                    'effective_from' => $setting->effective_from ? $setting->effective_from->format('Y-m-d') : date('Y-m-01'),
                    'is_active' => (bool)$setting->is_active,
                ]
            ]);
        }

        return redirect()->back()->with('success', 'Payroll Deduction Settings updated successfully.');
    }

    /**
     * Centralized calculation helper for Payroll Attendance verification
     */
    protected function computePayrollAttendanceDetails($schoolId, $monthName, $year, $departmentId = null, $search = null): array
    {
        $monthNumbers = [
            'january' => 1, 'february' => 2, 'march' => 3, 'april' => 4,
            'may' => 5, 'june' => 6, 'july' => 7, 'august' => 8,
            'september' => 9, 'october' => 10, 'november' => 11, 'december' => 12,
            'jan' => 1, 'feb' => 2, 'mar' => 3, 'apr' => 4,
            'jun' => 6, 'jul' => 7, 'aug' => 8,
            'sep' => 9, 'sept' => 9, 'oct' => 10, 'nov' => 11, 'dec' => 12,
        ];

        $mLower = strtolower(trim((string)$monthName));
        $monthNum = $monthNumbers[$mLower] ?? (is_numeric($monthName) ? (int)$monthName : (int)date('n'));
        if ($monthNum < 1 || $monthNum > 12) {
            $monthNum = (int)date('n');
        }

        $startDate = Carbon::createFromDate($year, $monthNum, 1)->startOfDay();
        $endDate = Carbon::createFromDate($year, $monthNum, 1)->endOfMonth()->startOfDay();
        $daysInMonth = (int)$startDate->daysInMonth;
        $monthFormatted = $startDate->format('F Y');
        $shortMonthFormatted = $startDate->format('M Y');
        $dateRangeDisplay = $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y');

        // Fetch School Payroll Deduction Setting
        $deductionSetting = PayrollDeductionSetting::getForSchool($schoolId, $startDate->format('Y-m-d'));
        $deductionMultiplier = $deductionSetting->is_active ? (float)$deductionSetting->deduction_multiplier : 1.00;

        // 1. Get official holidays from Event model
        $holidayEvents = Event::where('school_id', $schoolId)
            ->where('is_holiday', true)
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                  ->orWhereBetween('end_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                  ->orWhere(function ($sub) use ($startDate, $endDate) {
                      $sub->where('start_date', '<=', $startDate->format('Y-m-d'))
                          ->where('end_date', '>=', $endDate->format('Y-m-d'));
                  });
            })
            ->get();

        $holidayDates = [];
        foreach ($holidayEvents as $ev) {
            $sEv = Carbon::parse($ev->start_date)->startOfDay();
            $eEv = Carbon::parse($ev->end_date)->startOfDay();
            $cur = $sEv->copy();
            while ($cur->lte($eEv)) {
                if ($cur->gte($startDate) && $cur->lte($endDate)) {
                    $holidayDates[$cur->format('Y-m-d')] = $ev->title ?: 'Holiday';
                }
                $cur->addDay();
            }
        }

        // 2. Fetch Active Staff
        $staffQuery = Staff::where('school_id', $schoolId)
            ->where('is_active', true)
            ->with(['department', 'designation', 'salaryStructure']);

        if ($departmentId && $departmentId !== 'All' && is_numeric($departmentId)) {
            $staffQuery->where('department_id', (int)$departmentId);
        }

        if ($search) {
            SearchHelper::applyStaffSearch($staffQuery, $search);
        }

        $allStaff = $staffQuery->orderBy('first_name')->get();

        // 3. Fetch Attendances in Month
        $attendances = StaffAttendance::where('school_id', $schoolId)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->groupBy('staff_id');

        // 4. Fetch Approved Leaves in Month
        $leaveApps = StaffLeaveApplication::where('school_id', $schoolId)
            ->where('status', 'approved')
            ->where(function ($q) use ($startDate, $endDate) {
                $q->where('start_date', '<=', $endDate->format('Y-m-d'))
                  ->where('end_date', '>=', $startDate->format('Y-m-d'));
            })
            ->with('leaveType')
            ->get()
            ->groupBy('staff_id');

        // Fetch Staff Leave Balances for CL / Paid leave adjustment
        $staffBalancesMap = StaffLeaveBalance::where('school_id', $schoolId)
            ->with('leaveType')
            ->get()
            ->groupBy('staff_id');
        $defaultLeaveTypes = LeaveType::where('school_id', $schoolId)->get();

        // 5. Pre-build calendar template for the month
        $calendarTemplate = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $dateObj = Carbon::createFromDate($year, $monthNum, $d)->startOfDay();
            $dateStr = $dateObj->format('Y-m-d');
            $isSunday = ($dateObj->dayOfWeek === 0);
            $isOfficialHoliday = isset($holidayDates[$dateStr]);

            $calendarTemplate[] = [
                'day_num' => $d,
                'date' => $dateStr,
                'formatted_date' => $dateObj->format('d-m-Y'),
                'day_name' => $dateObj->format('l'),
                'day_short' => $dateObj->format('D'),
                'is_sunday' => $isSunday,
                'is_holiday' => $isOfficialHoliday,
                'holiday_name' => $isOfficialHoliday ? $holidayDates[$dateStr] : null,
            ];
        }

        // Count total Sundays and Holidays in month
        $monthSundays = 0;
        $monthHolidays = 0;
        foreach ($calendarTemplate as $cDay) {
            if ($cDay['is_sunday']) {
                $monthSundays++;
            } elseif ($cDay['is_holiday']) {
                $monthHolidays++;
            }
        }
        $monthStandardWorkingDays = max(0, $daysInMonth - ($monthSundays + $monthHolidays));

        // 6. Compute Staff rows & group by Department
        $departmentsMap = [];
        $departmentsList = Department::where('school_id', $schoolId)->orderBy('name')->get();
        foreach ($departmentsList as $d) {
            $departmentsMap[$d->id] = [
                'department' => $d,
                'id' => $d->id,
                'name' => $d->name,
                'staff_rows' => [],
                'total_staff' => 0,
                'total_present_days' => 0,
                'total_absent_days' => 0,
                'total_paid_leaves' => 0,
                'total_unpaid_leaves' => 0,
                'total_half_days' => 0,
                'total_holidays' => 0,
                'avg_attendance_pct' => 0,
            ];
        }
        // Also placeholder for unassigned
        $unassignedKey = 'unassigned';
        $departmentsMap[$unassignedKey] = [
            'department' => (object)['id' => 0, 'name' => 'General / Other Staff'],
            'id' => 0,
            'name' => 'General / Other Staff',
            'staff_rows' => [],
            'total_staff' => 0,
            'total_present_days' => 0,
            'total_absent_days' => 0,
            'total_paid_leaves' => 0,
            'total_unpaid_leaves' => 0,
            'total_half_days' => 0,
            'total_holidays' => 0,
            'avg_attendance_pct' => 0,
        ];

        $globalPresentStaffCount = 0;
        $globalLeavesStaffCount = 0;
        $globalAbsentStaffCount = 0;
        $totalPctSum = 0;

        foreach ($allStaff as $staff) {
            $staffAtts = $attendances->get($staff->id, collect())->keyBy(function ($item) {
                return Carbon::parse($item->date)->format('Y-m-d');
            });
            $staffLeaves = $leaveApps->get($staff->id, collect());

            $presentDays = 0;
            $absentDays = 0;
            $halfDays = 0;
            $holidays = 0;
            $weekOffs = 0;
            $paidLeaves = 0;
            $unpaidLeaves = 0;
            $dailyDetails = [];

            foreach ($calendarTemplate as $cDay) {
                $dStr = $cDay['date'];
                $attRecord = $staffAtts->get($dStr);

                // Check leave
                $matchingLeave = null;
                foreach ($staffLeaves as $lApp) {
                    $lStart = Carbon::parse($lApp->start_date)->format('Y-m-d');
                    $lEnd = Carbon::parse($lApp->end_date)->format('Y-m-d');
                    if ($dStr >= $lStart && $dStr <= $lEnd) {
                        $matchingLeave = $lApp;
                        break;
                    }
                }

                $isLwp = false;
                if ($matchingLeave) {
                    $ltCode = strtoupper(trim($matchingLeave->leave_type_code ?? ''));
                    $ltName = strtolower(trim($matchingLeave->leave_type_name ?? ''));
                    if ($ltCode === 'LWP' || $ltCode === 'UNPAID' || str_contains($ltName, 'without pay') || str_contains($ltName, 'unpaid')) {
                        $isLwp = true;
                    }
                }

                $status = 'not_marked';
                $statusLabel = 'Not Marked';
                $badgeClass = 'badge-not-marked';
                $remarks = '';

                if ($attRecord) {
                    $rawSt = strtolower(trim($attRecord->status));
                    if ($rawSt === 'present' || $rawSt === 'late') {
                        $status = 'present';
                        $statusLabel = ($rawSt === 'late') ? 'Present (Late)' : 'Present';
                        $badgeClass = 'badge-present';
                        $presentDays++;
                    } elseif ($rawSt === 'half_day') {
                        $status = 'half_day';
                        $statusLabel = 'Half Day';
                        $badgeClass = 'badge-half-day';
                        $halfDays++;
                    } elseif ($rawSt === 'absent') {
                        $status = 'absent';
                        $statusLabel = 'Absent';
                        $badgeClass = 'badge-absent';
                        $absentDays++;
                    } elseif ($rawSt === 'holiday') {
                        $status = 'holiday';
                        $statusLabel = 'Holiday';
                        $badgeClass = 'badge-holiday';
                        $holidays++;
                    } elseif ($rawSt === 'leave') {
                        if ($isLwp) {
                            $status = 'unpaid_leave';
                            $statusLabel = 'Leave (Unpaid / LWP)';
                            $badgeClass = 'badge-unpaid-leave';
                            $unpaidLeaves++;
                        } else {
                            $status = 'paid_leave';
                            $statusLabel = 'Leave (Paid: ' . ($matchingLeave?->leave_type_name ?: 'Approved') . ')';
                            $badgeClass = 'badge-paid-leave';
                            $paidLeaves++;
                        }
                    } else {
                        $status = $rawSt;
                        $statusLabel = ucfirst(str_replace('_', ' ', $rawSt));
                    }
                    if ($attRecord->clock_in_at) {
                        $remarks = 'In: ' . Carbon::parse($attRecord->clock_in_at)->format('h:i A');
                        if ($attRecord->clock_out_at) {
                            $remarks .= ' | Out: ' . Carbon::parse($attRecord->clock_out_at)->format('h:i A');
                        }
                    }
                } elseif ($matchingLeave) {
                    if ($isLwp) {
                        $status = 'unpaid_leave';
                        $statusLabel = 'Leave (Unpaid / LWP)';
                        $badgeClass = 'badge-unpaid-leave';
                        $unpaidLeaves++;
                    } else {
                        $status = 'paid_leave';
                        $statusLabel = 'Leave (Paid: ' . ($matchingLeave->leave_type_name ?: 'Approved') . ')';
                        $badgeClass = 'badge-paid-leave';
                        $paidLeaves++;
                    }
                    $remarks = 'Approved Leave';
                } elseif ($cDay['is_holiday']) {
                    $status = 'holiday';
                    $statusLabel = 'Holiday' . ($cDay['holiday_name'] ? ' (' . $cDay['holiday_name'] . ')' : '');
                    $badgeClass = 'badge-holiday';
                    $holidays++;
                } elseif ($cDay['is_sunday']) {
                    $status = 'week_off';
                    $statusLabel = 'Weekly Off (Sunday)';
                    $badgeClass = 'badge-week-off';
                    $weekOffs++;
                } else {
                    $status = 'not_marked';
                    $statusLabel = 'Not Marked';
                    $badgeClass = 'badge-not-marked';
                }

                $dailyDetails[] = [
                    'day_num' => $cDay['day_num'],
                    'date' => $cDay['formatted_date'],
                    'day_name' => $cDay['day_name'],
                    'status' => $status,
                    'status_label' => $statusLabel,
                    'badge_class' => $badgeClass,
                    'remarks' => $remarks,
                ];
            }

            $effectivePresent = $presentDays + ($halfDays * 0.5) + $paidLeaves + $holidays + $weekOffs;
            $attPct = $daysInMonth > 0 ? min(100, round(($effectivePresent / $daysInMonth) * 100, 1)) : 0;
            $totalPctSum += $attPct;

            if ($presentDays > 0 || $attPct >= 70) {
                $globalPresentStaffCount++;
            }
            if ($paidLeaves > 0 || $unpaidLeaves > 0) {
                $globalLeavesStaffCount++;
            }
            if ($absentDays > 0) {
                $globalAbsentStaffCount++;
            }

            $basicSalary = $staff->salaryStructure ? (float)$staff->salaryStructure->basic_salary : (float)($staff->basic_salary ?: 0);
            $dailySalary = round($basicSalary / 30, 2);

            // Fetch available Casual Leave (CL) / eligible paid leave balance
            $availCl = 0.0;
            $staffBals = $staffBalancesMap->get($staff->id);
            if ($staffBals && $staffBals->isNotEmpty()) {
                foreach ($staffBals as $b) {
                    $ltCode = strtoupper(trim($b->leaveType?->code ?? ''));
                    $ltName = strtolower(trim($b->leaveType?->name ?? ''));
                    if ($ltCode !== 'LWP' && $ltCode !== 'UNPAID' && !str_contains($ltName, 'without pay') && !str_contains($ltName, 'unpaid')) {
                        $availCl += max(0, (float)$b->allowed - (float)$b->availed);
                    }
                }
            } else {
                foreach ($defaultLeaveTypes as $lt) {
                    $ltCode = strtoupper(trim($lt->code ?? ''));
                    $ltName = strtolower(trim($lt->name ?? ''));
                    if ($ltCode !== 'LWP' && $ltCode !== 'UNPAID' && !str_contains($ltName, 'without pay') && !str_contains($ltName, 'unpaid')) {
                        $availCl += (float)($lt->leave_count ?? 0);
                    }
                }
            }

            $rawAbsenceDays = (float)($unpaidLeaves + $absentDays + ($halfDays * 0.5));
            $clCoveredDays = min($rawAbsenceDays, $availCl);
            $extraLeaveDays = max(0, $rawAbsenceDays - $clCoveredDays);
            $attDeduction = round($extraLeaveDays * ($basicSalary / 30) * $deductionMultiplier, 2);

            $staffRowData = [
                'staff' => $staff,
                'staff_id' => $staff->id,
                'employee_id' => $staff->employee_id ?: 'EMP-' . str_pad($staff->id, 4, '0', STR_PAD_LEFT),
                'name' => $staff->full_name,
                'designation' => $staff->designation?->name ?: ($staff->staff_type ?: 'Staff'),
                'department_name' => $staff->department?->name ?: 'General',
                'department_id' => $staff->department_id ?: 0,
                'present_days' => $presentDays,
                'absent_days' => $absentDays,
                'holidays' => $holidays,
                'half_days' => $halfDays,
                'half_day_equivalent' => $halfDays * 0.5,
                'paid_leaves' => $paidLeaves,
                'unpaid_leaves' => $unpaidLeaves,
                'extra_leave_days' => $extraLeaveDays,
                'basic_salary' => $basicSalary,
                'daily_salary' => $dailySalary,
                'attendance_deduction' => $attDeduction,
                'deduction_multiplier' => $deductionMultiplier,
                'week_offs' => $weekOffs,
                'total_days' => $daysInMonth,
                'working_days' => max(0, $daysInMonth - $extraLeaveDays),
                'effective_present' => $effectivePresent,
                'attendance_pct' => $attPct,
                'daily_details' => $dailyDetails,
            ];

            $deptKey = $staff->department_id && isset($departmentsMap[$staff->department_id]) ? $staff->department_id : $unassignedKey;
            $departmentsMap[$deptKey]['staff_rows'][] = $staffRowData;
            $departmentsMap[$deptKey]['total_staff']++;
            $departmentsMap[$deptKey]['total_present_days'] += $presentDays;
            $departmentsMap[$deptKey]['total_absent_days'] += $absentDays;
            $departmentsMap[$deptKey]['total_paid_leaves'] += $paidLeaves;
            $departmentsMap[$deptKey]['total_unpaid_leaves'] += $unpaidLeaves;
            $departmentsMap[$deptKey]['total_half_days'] += $halfDays;
            $departmentsMap[$deptKey]['total_holidays'] += $holidays;
        }

        // Finalize department averages
        $finalDepartmentCards = [];
        foreach ($departmentsMap as $k => $dData) {
            if ($dData['total_staff'] > 0 || ($departmentId && (int)$departmentId === (int)$k)) {
                $stCount = count($dData['staff_rows']);
                $sumPct = 0;
                foreach ($dData['staff_rows'] as $r) {
                    $sumPct += $r['attendance_pct'];
                }
                $dData['avg_attendance_pct'] = $stCount > 0 ? round($sumPct / $stCount, 1) : 0;
                $finalDepartmentCards[] = $dData;
            }
        }

        $totalEmployees = $allStaff->count();
        $averageAttendancePct = $totalEmployees > 0 ? round($totalPctSum / $totalEmployees, 1) : 0;

        return [
            'salary_month' => $startDate->format('F'),
            'salary_year' => (int)$year,
            'month_number' => $monthNum,
            'payroll_month' => $monthFormatted,
            'short_payroll_month' => $shortMonthFormatted,
            'date_range_display' => $dateRangeDisplay,
            'days_in_month' => $daysInMonth,
            'month_sundays' => $monthSundays,
            'month_holidays' => $monthHolidays,
            'month_working_days' => $monthStandardWorkingDays,
            'calendar_template' => $calendarTemplate,
            'department_cards' => $finalDepartmentCards,
            'all_staff_count' => $totalEmployees,
            'deduction_setting' => [
                'rule' => $deductionSetting->deduction_rule,
                'multiplier' => $deductionMultiplier,
                'is_active' => (bool)$deductionSetting->is_active,
            ],
            'global_kpi' => [
                'total_employees' => $totalEmployees,
                'present_employees' => $globalPresentStaffCount,
                'employees_on_leave' => $globalLeavesStaffCount,
                'employees_absent' => $globalAbsentStaffCount,
                'average_attendance_pct' => $averageAttendancePct,
                'total_departments' => count($finalDepartmentCards),
                'verified_departments_pct' => 100,
            ]
        ];
    }

    /**
     * 1. PAYROLL ATTENDANCE VERIFICATION PAGE
     */
    public function payrollAttendance(Request $request)
    {
        $schoolId = $this->getSchoolId();

        $months = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];

        $currentYear = (int)date('Y');
        $years = range($currentYear - 3, $currentYear + 4);

        $selectedMonth = $request->get('salary_month', date('F'));
        $selectedYear = (int)$request->get('salary_year', date('Y'));
        $selectedDeptId = $request->get('department_id', 'All');
        $search = trim($request->get('search', ''));

        // Handle salary cycle input like "01 Jul - 31 Jul"
        if ($request->has('salary_cycle') && $request->salary_cycle) {
            $cycle = $request->salary_cycle;
            if (preg_match('/([a-zA-Z]+)/', $cycle, $cm)) {
                $selectedMonth = Carbon::parse("1 " . $cm[1])->format('F');
            }
        }

        $departments = Department::where('school_id', $schoolId)->orderBy('name')->get();

        $data = $this->computePayrollAttendanceDetails($schoolId, $selectedMonth, $selectedYear, $selectedDeptId, $search);

        return view('school.payroll.payroll-attendance', [
            'months' => $months,
            'years' => $years,
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
            'selectedDeptId' => $selectedDeptId,
            'search' => $search,
            'departments' => $departments,
            'attendanceData' => $data,
        ]);
    }

    /**
     * RECALCULATE ATTENDANCE ACTION (AJAX & Form POST)
     */
    public function recalculatePayrollAttendance(Request $request)
    {
        $schoolId = $this->getSchoolId();
        $selectedMonth = $request->get('salary_month', date('F'));
        $selectedYear = (int)$request->get('salary_year', date('Y'));
        $deptId = $request->get('department_id', 'All');
        $search = trim($request->get('search', ''));

        $data = $this->computePayrollAttendanceDetails($schoolId, $selectedMonth, $selectedYear, $deptId, $search);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Attendance data recalculated successfully from Staff Attendance & Leave Management for {$data['payroll_month']}.",
                'data' => $data,
            ]);
        }

        return redirect()->route('school.payroll.payroll-attendance', [
            'salary_month' => $selectedMonth,
            'salary_year' => $selectedYear,
            'department_id' => $deptId,
            'search' => $search,
        ])->with('success', "Attendance data recalculated successfully from Staff Attendance & Leave Management for {$data['payroll_month']}.");
    }

    /**
     * EXPORT PAYROLL ATTENDANCE EXCEL (.xls / CSV)
     */
    public function exportPayrollAttendanceExcel(Request $request)
    {
        $schoolId = $this->getSchoolId();
        $selectedMonth = $request->get('salary_month', date('F'));
        $selectedYear = (int)$request->get('salary_year', date('Y'));
        $deptId = $request->get('department_id', 'All');
        $search = trim($request->get('search', ''));

        $school = auth()->user()->school;
        $data = $this->computePayrollAttendanceDetails($schoolId, $selectedMonth, $selectedYear, $deptId, $search);

        $filename = 'payroll_attendance_' . strtolower($selectedMonth) . '_' . $selectedYear . '_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($data, $school, $selectedMonth, $selectedYear) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

            fputcsv($file, [$school?->name ?: 'SCHOOL ERP PAYROLL ATTENDANCE REPORT']);
            fputcsv($file, ["Payroll Verification Period: {$data['date_range_display']} ({$data['payroll_month']})"]);
            fputcsv($file, ["Total Staff: {$data['global_kpi']['total_employees']}", "Average Attendance: {$data['global_kpi']['average_attendance_pct']}%", "Generated on: " . date('d M Y, h:i A')]);
            fputcsv($file, []);

            foreach ($data['department_cards'] as $deptCard) {
                fputcsv($file, ["--- DEPARTMENT: " . strtoupper($deptCard['name']) . " (Total Staff: {$deptCard['total_staff']} | Avg Attendance: {$deptCard['avg_attendance_pct']}%) ---"]);
                fputcsv($file, [
                    'S.No', 'Employee Name', 'Employee ID', 'Designation', 'Department',
                    'Present Days', 'Absent Days', 'Holidays', 'Half Days', 'Paid Leave', 'Unpaid Leave',
                    'Total Days', 'Working Days', 'Attendance %'
                ]);

                foreach ($deptCard['staff_rows'] as $idx => $row) {
                    fputcsv($file, [
                        $idx + 1,
                        $row['name'],
                        $row['employee_id'],
                        $row['designation'],
                        $row['department_name'],
                        $row['present_days'],
                        $row['absent_days'],
                        $row['holidays'],
                        $row['half_days'],
                        $row['paid_leaves'],
                        $row['unpaid_leaves'],
                        $row['total_days'],
                        $row['working_days'],
                        $row['attendance_pct'] . '%',
                    ]);
                }
                fputcsv($file, []);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * EXPORT PAYROLL ATTENDANCE PDF
     */
    public function exportPayrollAttendancePdf(Request $request)
    {
        $schoolId = $this->getSchoolId();
        $selectedMonth = $request->get('salary_month', date('F'));
        $selectedYear = (int)$request->get('salary_year', date('Y'));
        $deptId = $request->get('department_id', 'All');
        $search = trim($request->get('search', ''));

        $school = auth()->user()->school;
        $data = $this->computePayrollAttendanceDetails($schoolId, $selectedMonth, $selectedYear, $deptId, $search);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('school.payroll.pdf-payroll-attendance', [
            'data' => $data,
            'school' => $school,
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
            'generatedDate' => date('d M Y h:i A'),
        ])->setPaper('a4', 'landscape');

        $filename = 'payroll_attendance_' . strtolower($selectedMonth) . '_' . $selectedYear . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * POPUP MODAL 1: VIEW STAFF ATTENDANCE DETAILS
     */
    public function staffAttendanceModal(Request $request)
    {
        $schoolId = $this->getSchoolId();
        $staffId = (int)$request->get('staff_id');
        $selectedMonth = $request->get('salary_month', date('F'));
        $selectedYear = (int)$request->get('salary_year', date('Y'));

        $staff = Staff::where('school_id', $schoolId)
            ->with(['department', 'designation'])
            ->findOrFail($staffId);

        $data = $this->computePayrollAttendanceDetails($schoolId, $selectedMonth, $selectedYear, null, null);

        $targetRow = null;
        foreach ($data['department_cards'] as $deptCard) {
            foreach ($deptCard['staff_rows'] as $r) {
                if ((int)$r['staff_id'] === $staffId) {
                    $targetRow = $r;
                    break 2;
                }
            }
        }

        if (!$targetRow) {
            return response()->json([
                'success' => false,
                'message' => 'Staff attendance record not found for the selected cycle.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'staff' => [
                'id' => $staff->id,
                'name' => $staff->full_name,
                'employee_id' => $staff->employee_id ?: 'EMP-' . str_pad($staff->id, 4, '0', STR_PAD_LEFT),
                'designation' => $staff->designation?->name ?: ($staff->staff_type ?: 'Staff'),
                'department' => $staff->department?->name ?: 'General',
                'phone' => $staff->phone ?: 'N/A',
                'email' => $staff->email ?: 'N/A',
            ],
            'month_info' => [
                'month' => $data['salary_month'],
                'year' => $data['salary_year'],
                'formatted' => $data['payroll_month'],
                'date_range' => $data['date_range_display'],
            ],
            'summary' => [
                'total_days' => $targetRow['total_days'],
                'present_days' => $targetRow['present_days'],
                'absent_days' => $targetRow['absent_days'],
                'half_days' => $targetRow['half_days'],
                'holidays' => $targetRow['holidays'],
                'week_offs' => $targetRow['week_offs'],
                'paid_leaves' => $targetRow['paid_leaves'],
                'unpaid_leaves' => $targetRow['unpaid_leaves'],
                'working_days' => $targetRow['working_days'],
                'attendance_pct' => $targetRow['attendance_pct'],
            ],
            'daily_records' => $targetRow['daily_details'],
        ]);
    }

    /**
     * POPUP MODAL 2: VIEW STAFF LEAVE DETAILS
     */
    public function staffLeaveModal(Request $request)
    {
        $schoolId = $this->getSchoolId();
        $staffId = (int)$request->get('staff_id');
        $selectedMonth = $request->get('salary_month', date('F'));
        $selectedYear = (int)$request->get('salary_year', date('Y'));

        $staff = Staff::where('school_id', $schoolId)
            ->with(['department', 'designation'])
            ->findOrFail($staffId);

        // Fetch Leave Balances
        $balances = StaffLeaveBalance::where('school_id', $schoolId)
            ->where('staff_id', $staffId)
            ->with('leaveType')
            ->get();

        if ($balances->isEmpty()) {
            // Provide default types
            $defaultTypes = LeaveType::where('school_id', $schoolId)->get();
            $balanceList = [];
            foreach ($defaultTypes as $lt) {
                $balanceList[] = [
                    'type_name' => $lt->name,
                    'type_code' => $lt->code,
                    'allowed' => (float)$lt->leave_count,
                    'availed' => 0,
                    'balance' => (float)$lt->leave_count,
                ];
            }
        } else {
            $balanceList = $balances->map(function ($b) {
                return [
                    'type_name' => $b->leaveType?->name ?: 'Leave',
                    'type_code' => $b->leaveType?->code ?: 'L',
                    'allowed' => (float)$b->allowed,
                    'availed' => (float)$b->availed,
                    'balance' => max(0, (float)$b->allowed - (float)$b->availed),
                ];
            })->values()->toArray();
        }

        // Fetch Leave Applications History
        $leaveHistory = StaffLeaveApplication::where('school_id', $schoolId)
            ->where('staff_id', $staffId)
            ->with('leaveType')
            ->latest('id')
            ->get()
            ->map(function ($app) {
                $code = strtoupper(trim($app->leave_type_code ?: ''));
                $name = strtolower(trim($app->leave_type_name ?: ''));
                $isUnpaid = ($code === 'LWP' || $code === 'UNPAID' || str_contains($name, 'without pay') || str_contains($name, 'unpaid'));

                return [
                    'id' => $app->id,
                    'leave_type' => $app->leave_type_name ?: ($app->leaveType?->name ?: 'Casual Leave'),
                    'leave_code' => $app->leave_type_code ?: ($app->leaveType?->code ?: 'CL'),
                    'start_date' => Carbon::parse($app->start_date)->format('d M Y'),
                    'end_date' => Carbon::parse($app->end_date)->format('d M Y'),
                    'total_days' => (float)$app->total_days,
                    'is_paid' => !$isUnpaid,
                    'paid_status' => $isUnpaid ? 'Unpaid (LWP)' : 'Paid Leave',
                    'status' => ucfirst($app->status ?: 'pending'),
                    'reason' => $app->reason ?: 'Personal Reason',
                    'admin_remark' => $app->admin_remark ?: ($app->rejection_reason ?: 'None'),
                    'applied_date' => $app->created_at ? $app->created_at->format('d M Y') : 'N/A',
                ];
            });

        // Fetch Attendance Deduction Breakdown for the selected cycle
        $data = $this->computePayrollAttendanceDetails($schoolId, $selectedMonth, $selectedYear, null, null);
        $targetRow = null;
        foreach ($data['department_cards'] as $deptCard) {
            foreach ($deptCard['staff_rows'] as $r) {
                if ((int)$r['staff_id'] === $staffId) {
                    $targetRow = $r;
                    break 2;
                }
            }
        }

        $deductionBreakdown = null;
        if ($targetRow) {
            $deductionBreakdown = [
                'gross_salary' => (float)$targetRow['basic_salary'],
                'daily_salary' => (float)$targetRow['daily_salary'],
                'absent_days' => (float)$targetRow['absent_days'],
                'half_days' => (float)$targetRow['half_days'],
                'half_day_equivalent' => (float)($targetRow['half_days'] * 0.5),
                'unpaid_leaves' => (float)$targetRow['unpaid_leaves'],
                'total_deduction_days' => (float)$targetRow['extra_leave_days'],
                'attendance_deduction' => (float)$targetRow['attendance_deduction'],
            ];
        }

        return response()->json([
            'success' => true,
            'staff' => [
                'id' => $staff->id,
                'name' => $staff->full_name,
                'employee_id' => $staff->employee_id ?: 'EMP-' . str_pad($staff->id, 4, '0', STR_PAD_LEFT),
                'designation' => $staff->designation?->name ?: ($staff->staff_type ?: 'Staff'),
                'department' => $staff->department?->name ?: 'General',
            ],
            'balances' => $balanceList,
            'history' => $leaveHistory,
            'deduction_breakdown' => $deductionBreakdown,
        ]);
    }

    /**
     * POPUP MODAL 3: VIEW PREVIOUS SALARY HISTORY
     */
    public function staffSalaryModal(Request $request)
    {
        $schoolId = $this->getSchoolId();
        $staffId = (int)$request->get('staff_id');

        $staff = Staff::where('school_id', $schoolId)
            ->with(['department', 'designation', 'salaryStructure'])
            ->findOrFail($staffId);

        $struct = $staff->salaryStructure;

        $salaryHistory = StaffPayroll::where('school_id', $schoolId)
            ->where('staff_id', $staffId)
            ->with('payments')
            ->latest('id')
            ->get()
            ->map(function ($p) {
                $lastPayment = $p->payments?->last();
                $payDate = $lastPayment ? Carbon::parse($lastPayment->payment_date)->format('d M Y') : ($p->finalised_at ? $p->finalised_at->format('d M Y') : 'N/A');

                return [
                    'id' => $p->id,
                    'payroll_month' => $p->payroll_month,
                    'gross_salary' => (float)$p->gross_salary,
                    'deductions' => (float)$p->deductions,
                    'net_payable' => (float)$p->net_payable,
                    'paid_amount' => (float)$p->paid_amount,
                    'payment_status' => strtolower($p->payment_status) === 'paid' ? 'Paid' : (strtolower($p->payment_status) === 'partially_paid' ? 'Partially Paid' : 'Unpaid'),
                    'payment_date' => $payDate,
                    'payslip_url' => route('school.payroll.payslip.view', ['id' => $p->id, 'download' => 1]),
                ];
            });

        return response()->json([
            'success' => true,
            'staff' => [
                'id' => $staff->id,
                'name' => $staff->full_name,
                'employee_id' => $staff->employee_id ?: 'EMP-' . str_pad($staff->id, 4, '0', STR_PAD_LEFT),
                'designation' => $staff->designation?->name ?: ($staff->staff_type ?: 'Staff'),
                'department' => $staff->department?->name ?: 'General',
                'bank_name' => $staff->bank_name ?: 'N/A',
                'account_number' => $staff->bank_account_number ?: 'N/A',
                'ifsc_code' => $staff->ifsc_code ?: 'N/A',
            ],
            'structure' => [
                'basic_salary' => $struct ? (float)$struct->basic_salary : (float)($staff->basic_salary ?: 0),
                'hra' => $struct ? (float)$struct->hra : 0,
                'da' => $struct ? (float)$struct->da : 0,
                'ta' => $struct ? (float)$struct->ta : 0,
                'allowance' => $struct ? (float)$struct->allowance : 0,
                'pf' => $struct ? (float)$struct->pf : 0,
                'esi' => $struct ? (float)$struct->esi : 0,
                'tds' => $struct ? (float)$struct->tds : 0,
                'prof_tax' => $struct ? (float)$struct->prof_tax : 0,
                'net_salary' => $struct ? (float)$struct->net_salary : (float)($staff->basic_salary ?: 0),
            ],
            'history' => $salaryHistory,
        ]);
    }
}
