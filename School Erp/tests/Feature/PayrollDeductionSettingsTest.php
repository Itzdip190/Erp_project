<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Designation;
use App\Models\LeaveType;
use App\Models\PayrollDeductionSetting;
use App\Models\School;
use App\Models\Staff;
use App\Models\StaffAttendance;
use App\Models\StaffLeaveBalance;
use App\Models\StaffPayroll;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PayrollDeductionSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function createSchoolEnvironment()
    {
        $school = School::create([
            'name' => 'St. Xavier High School',
            'code' => 'STX001',
            'status' => 'active',
        ]);

        $user = User::create([
            'school_id' => $school->id,
            'name' => 'School Admin',
            'email' => 'admin@stxavier.edu',
            'password' => Hash::make('password'),
            'role' => 'school_admin',
        ]);

        $dept = Department::create([
            'school_id' => $school->id,
            'name' => 'Science Department',
        ]);

        $desig = Designation::create([
            'school_id' => $school->id,
            'department_id' => $dept->id,
            'name' => 'Senior Teacher',
        ]);

        return [$school, $user, $dept, $desig];
    }

    public function test_can_fetch_and_store_payroll_deduction_settings_school_wise(): void
    {
        [$school, $user, $dept, $desig] = $this->createSchoolEnvironment();

        // GET settings (returns default if not configured yet)
        $getRes = $this->actingAs($user)
            ->getJson(route('school.payroll.deduction-settings'));

        $getRes->assertStatus(200);
        $getRes->assertJson([
            'success' => true,
            'data' => [
                'salary_calculation_base' => '30 Days',
                'deduction_rule' => 'one_day',
                'deduction_multiplier' => 1.0,
            ]
        ]);

        // STORE setting for School 1 (e.g., Half Day Deduction = 0.5x)
        $storeRes = $this->actingAs($user)
            ->postJson(route('school.payroll.deduction-settings.store'), [
                'deduction_rule' => 'half_day',
                'deduction_multiplier' => 0.5,
                'effective_from' => '2026-08-01',
                'is_active' => 1,
            ]);

        $storeRes->assertStatus(200);
        $storeRes->assertJson([
            'success' => true,
            'data' => [
                'deduction_rule' => 'half_day',
                'deduction_multiplier' => 0.5,
            ]
        ]);

        $this->assertDatabaseHas('payroll_deduction_settings', [
            'school_id' => $school->id,
            'deduction_rule' => 'half_day',
            'deduction_multiplier' => 0.5,
        ]);
    }

    public function test_multiplier_cannot_be_negative(): void
    {
        [$school, $user, $dept, $desig] = $this->createSchoolEnvironment();

        $response = $this->actingAs($user)
            ->postJson(route('school.payroll.deduction-settings.store'), [
                'deduction_rule' => 'custom',
                'deduction_multiplier' => -1.5,
                'effective_from' => '2026-08-01',
                'is_active' => 1,
            ]);

        $response->assertStatus(422);
    }

    public function test_salary_generation_calculates_attendance_deduction_fixed_30_days(): void
    {
        [$school, $user, $dept, $desig] = $this->createSchoolEnvironment();

        $staff = Staff::create([
            'school_id' => $school->id,
            'department_id' => $dept->id,
            'designation_id' => $desig->id,
            'joining_date' => '2025-01-01',
            'employee_id' => 'EMP15000',
            'first_name' => 'Rahul',
            'last_name' => 'Sharma',
            'basic_salary' => 15000.00, // Monthly salary 15,000 -> 15,000 / 30 = 500 / day
            'is_active' => true,
        ]);

        // Configure deduction setting: 1 Day Salary Deduction (multiplier = 1.0)
        PayrollDeductionSetting::create([
            'school_id' => $school->id,
            'salary_calculation_base' => '30_days',
            'deduction_rule' => 'one_day',
            'deduction_multiplier' => 1.00,
            'effective_from' => '2026-08-01',
            'is_active' => true,
        ]);

        // Generate salary for August 2026
        $genRes = $this->actingAs($user)
            ->post(route('school.payroll.process-generate'), [
                'salary_month' => 'August',
                'salary_year' => 2026,
            ]);

        $genRes->assertRedirect();

        $payroll = StaffPayroll::where('school_id', $school->id)
            ->where('staff_id', $staff->id)
            ->first();

        $this->assertNotNull($payroll);
        $this->assertEquals(15000.00, (float)$payroll->basic_salary);
        $this->assertEquals(15000.00, (float)$payroll->gross_salary);
        // Total daily salary is 15,000 / 30 = 500 per day. If 0 extra leaves -> 0 deduction
        $this->assertEquals(0.00, (float)$payroll->attendance_deduction);
        $this->assertEquals(15000.00, (float)$payroll->net_payable);
    }

    public function test_cl_balance_adjusts_effective_absence_before_salary_deduction(): void
    {
        [$school, $user, $dept, $desig] = $this->createSchoolEnvironment();

        $staff = Staff::create([
            'school_id' => $school->id,
            'department_id' => $dept->id,
            'designation_id' => $desig->id,
            'joining_date' => '2025-01-01',
            'employee_id' => 'EMP217',
            'first_name' => 'Aachal',
            'last_name' => 'Bora',
            'basic_salary' => 18000.00, // Monthly salary 18,000 / 30 = 600 / day
            'is_active' => true,
        ]);

        $leaveType = LeaveType::create([
            'school_id' => $school->id,
            'academic_year' => '2026-2027',
            'staff_type' => 'Teaching',
            'code' => 'CL',
            'name' => 'Casual Leaves',
            'leave_count' => 21,
            'is_active' => true,
        ]);

        StaffLeaveBalance::create([
            'school_id' => $school->id,
            'staff_id' => $staff->id,
            'leave_type_id' => $leaveType->id,
            'allowed' => 21,
            'availed' => 19, // 21 - 19 = 2 Days Left
        ]);

        // Mark 7 absent days and 2 half days in July 2026
        // 7 absent + 2 half days (1 full day) = 8 effective absent days
        for ($d = 1; $d <= 7; $d++) {
            StaffAttendance::create([
                'school_id' => $school->id,
                'staff_id' => $staff->id,
                'date' => sprintf('2026-07-%02d', $d),
                'status' => 'absent',
                'attendance_type' => 'manual',
            ]);
        }
        for ($d = 8; $d <= 9; $d++) {
            StaffAttendance::create([
                'school_id' => $school->id,
                'staff_id' => $staff->id,
                'date' => sprintf('2026-07-%02d', $d),
                'status' => 'half_day',
                'attendance_type' => 'manual',
            ]);
        }

        // Configure deduction multiplier 1.00
        PayrollDeductionSetting::create([
            'school_id' => $school->id,
            'salary_calculation_base' => '30_days',
            'deduction_rule' => 'one_day',
            'deduction_multiplier' => 1.00,
            'effective_from' => '2026-07-01',
            'is_active' => true,
        ]);

        // Generate salary for July 2026
        $genRes = $this->actingAs($user)
            ->post(route('school.payroll.process-generate'), [
                'salary_month' => 'July',
                'salary_year' => 2026,
            ]);

        $genRes->assertRedirect();

        $payroll = StaffPayroll::where('school_id', $school->id)
            ->where('staff_id', $staff->id)
            ->first();

        $this->assertNotNull($payroll);
        $this->assertEquals(18000.00, (float)$payroll->basic_salary);
        // Effective absence: 8 days. CL available: 2 days. Unpaid deduction days: 6 days.
        // Deduction: 6 days * ₹600 = ₹3,600
        $this->assertEquals(6.00, (float)$payroll->attendance_deduction_days);
        $this->assertEquals(3600.00, (float)$payroll->attendance_deduction);
        $this->assertEquals(14400.00, (float)$payroll->net_payable);
    }
}
