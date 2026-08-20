<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Designation;
use App\Models\School;
use App\Models\Staff;
use App\Models\StaffPayroll;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class HRPayrollWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function createSchoolUserDeptAndDesig()
    {
        $school = School::create([
            'name' => 'St. Joseph Girls College',
            'code' => 'STJ001',
            'status' => 'active',
        ]);

        $user = User::create([
            'school_id' => $school->id,
            'name' => 'Admin User',
            'email' => 'admin@stjoseph.edu',
            'password' => Hash::make('password'),
            'role' => 'school_admin',
        ]);

        $dept = Department::create([
            'school_id' => $school->id,
            'name' => 'Academic Department',
        ]);

        $desig = Designation::create([
            'school_id' => $school->id,
            'department_id' => $dept->id,
            'name' => 'Primary Teacher',
        ]);

        return [$school, $user, $dept, $desig];
    }

    public function test_attendance_register_page_loads(): void
    {
        [$school, $user, $dept, $desig] = $this->createSchoolUserDeptAndDesig();

        $staff = Staff::create([
            'school_id' => $school->id,
            'user_id' => $user->id,
            'department_id' => $dept->id,
            'designation_id' => $desig->id,
            'joining_date' => '2025-01-01',
            'employee_id' => 'emp-0001',
            'first_name' => 'SR. GRETTA MARIA',
            'last_name' => 'DSOUS',
            'basic_salary' => 50000.00,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)
            ->get(route('school.payroll.attendance-register', ['month' => '2026-08']));

        $response->assertStatus(200);
        $response->assertSee('ATTENDANCE REGISTER');
        $response->assertSee('SR. GRETTA MARIA');
    }

    public function test_salary_generation_and_finalisation_workflow(): void
    {
        [$school, $user, $dept, $desig] = $this->createSchoolUserDeptAndDesig();

        $staff = Staff::create([
            'school_id' => $school->id,
            'department_id' => $dept->id,
            'designation_id' => $desig->id,
            'joining_date' => '2025-01-01',
            'employee_id' => 'emp-0002',
            'first_name' => 'SUNITA',
            'last_name' => 'SRIVASTAV',
            'basic_salary' => 60000.00,
            'is_active' => true,
        ]);

        // Process Generate Salary
        $response = $this->actingAs($user)
            ->post(route('school.payroll.process-generate'), [
                'payroll_month' => '2026-08',
                'staff_ids' => [$staff->id],
            ]);

        $response->assertRedirect(route('school.payroll.finalised', ['month' => '2026-08']));

        $this->assertDatabaseHas('staff_payrolls', [
            'school_id' => $school->id,
            'staff_id' => $staff->id,
            'status' => 'finalised',
            'net_payable' => 60000.00,
        ]);

        // Verify Finalised Salary page
        $finalisedResponse = $this->actingAs($user)
            ->get(route('school.payroll.finalised', ['month' => '2026-08']));

        $finalisedResponse->assertStatus(200);
        $finalisedResponse->assertSee('SUNITA');
    }

    public function test_generating_payroll_for_may_2026_redirects_to_may_finalised_page(): void
    {
        [$school, $user, $dept, $desig] = $this->createSchoolUserDeptAndDesig();

        $staff = Staff::create([
            'school_id' => $school->id,
            'department_id' => $dept->id,
            'designation_id' => $desig->id,
            'joining_date' => '2025-01-01',
            'employee_id' => 'EMP217',
            'first_name' => 'Aachal',
            'last_name' => 'Bora',
            'basic_salary' => 18000.00,
            'is_active' => true,
        ]);

        // Submit generate salary form for May 2026
        $response = $this->actingAs($user)
            ->post(route('school.payroll.process-generate'), [
                'salary_month' => 'May',
                'salary_year' => 2026,
            ]);

        $response->assertRedirect(route('school.payroll.finalised', ['month' => '2026-05']));

        // Follow redirect to finalised payroll page
        $finalisedResponse = $this->actingAs($user)
            ->get(route('school.payroll.finalised', ['month' => '2026-05']));

        $finalisedResponse->assertStatus(200);
        $finalisedResponse->assertSee('May-2026');
        $finalisedResponse->assertSee('Aachal');
    }

    /** @test */
    public function payroll_attendance_verification_page_and_modals_work_correctly()
    {
        [$school, $user, $dept, $desig] = $this->createSchoolUserDeptAndDesig();

        $staff = Staff::create([
            'school_id' => $school->id,
            'department_id' => $dept->id,
            'designation_id' => $desig->id,
            'joining_date' => '2025-01-01',
            'employee_id' => 'EMP-TEST-01',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'basic_salary' => 50000.00,
            'is_active' => true,
        ]);

        // 1. Check Payroll Attendance Index Page
        $response = $this->actingAs($user)
            ->get(route('school.payroll.payroll-attendance', ['salary_month' => 'July', 'salary_year' => 2026]));

        $response->assertStatus(200);
        $response->assertSee('Payroll Attendance');
        $response->assertSee('John Doe');

        // 2. Check Recalculate Attendance (AJAX)
        $recalcResponse = $this->actingAs($user)
            ->postJson(route('school.payroll.payroll-attendance.recalculate'), [
                'department_id' => 'All',
                'salary_month' => 'July',
                'salary_year' => 2026,
            ]);

        $recalcResponse->assertStatus(200);
        $recalcResponse->assertJson(['success' => true]);

        // 3. Check Attendance Modal Endpoint
        $attModalResponse = $this->actingAs($user)
            ->getJson(route('school.payroll.payroll-attendance.modal-attendance', [
                'staff_id' => $staff->id,
                'salary_month' => 'July',
                'salary_year' => 2026,
            ]));

        $attModalResponse->assertStatus(200);
        $attModalResponse->assertJson(['success' => true]);

        // 4. Check Leave Modal Endpoint
        $leaveModalResponse = $this->actingAs($user)
            ->getJson(route('school.payroll.payroll-attendance.modal-leave', [
                'staff_id' => $staff->id,
                'salary_month' => 'July',
                'salary_year' => 2026,
            ]));

        $leaveModalResponse->assertStatus(200);
        $leaveModalResponse->assertJson(['success' => true]);

        // 5. Check Salary Modal Endpoint
        $salaryModalResponse = $this->actingAs($user)
            ->getJson(route('school.payroll.payroll-attendance.modal-salary', [
                'staff_id' => $staff->id,
            ]));

        $salaryModalResponse->assertStatus(200);
        $salaryModalResponse->assertJson(['success' => true]);
    }

    public function test_add_payment_updates_salary_totals(): void
    {
        [$school, $user, $dept, $desig] = $this->createSchoolUserDeptAndDesig();

        $staff = Staff::create([
            'school_id' => $school->id,
            'department_id' => $dept->id,
            'designation_id' => $desig->id,
            'joining_date' => '2025-01-01',
            'employee_id' => 'emp-0003',
            'first_name' => 'SUJATA',
            'last_name' => 'SRIVASTAV',
            'basic_salary' => 45000.00,
            'is_active' => true,
        ]);

        // Generate salary first
        $payroll = StaffPayroll::create([
            'school_id' => $school->id,
            'staff_id' => $staff->id,
            'payroll_month' => 'Aug 2026',
            'total_days' => 31,
            'present_days' => 31,
            'payable_days' => 31,
            'basic_salary' => 45000.00,
            'gross_salary' => 45000.00,
            'net_payable' => 45000.00,
            'paid_amount' => 0,
            'remaining_balance' => 45000.00,
            'status' => 'finalised',
        ]);

        // Record a payment of 20,000
        $response = $this->actingAs($user)
            ->post(route('school.payroll.store-payment'), [
                'staff_id' => $staff->id,
                'payroll_month' => '2026-08',
                'amount' => 20000.00,
                'payment_date' => '2026-08-18',
                'payment_method' => 'bank_transfer',
                'reference_no' => 'TXN99887766',
                'notes' => 'Partial August salary payment',
            ]);

        $response->assertRedirect(route('school.payroll.finalised', ['month' => '2026-08']));

        $payroll->refresh();
        $this->assertEquals(20000.00, $payroll->paid_amount);
        $this->assertEquals(25000.00, $payroll->remaining_balance);
        $this->assertEquals('partially_paid', $payroll->payment_status);

        $this->assertDatabaseHas('staff_payroll_payments', [
            'school_id' => $school->id,
            'staff_payroll_id' => $payroll->id,
            'staff_id' => $staff->id,
            'amount' => 20000.00,
            'payment_method' => 'bank_transfer',
            'reference_no' => 'TXN99887766',
        ]);
    }

    public function test_salary_structure_create_and_edit_workflow(): void
    {
        [$school, $user, $dept, $desig] = $this->createSchoolUserDeptAndDesig();

        $staff = Staff::create([
            'school_id' => $school->id,
            'department_id' => $dept->id,
            'designation_id' => $desig->id,
            'joining_date' => '2025-01-01',
            'employee_id' => 'EDUZENEMP001',
            'first_name' => 'Saurabh',
            'last_name' => 'Kumar',
            'basic_salary' => 0.00,
            'is_active' => true,
        ]);

        // 1. Visit listing page
        $response = $this->actingAs($user)->get(route('school.payroll.salary-structure'));
        $response->assertStatus(200);
        $response->assertSee('Configure Payroll Settings');
        $response->assertSee('Salary List');

        // 2. Visit configure page
        $configureResponse = $this->actingAs($user)->get(route('school.payroll.salary-structure.configure'));
        $configureResponse->assertStatus(200);
        $configureResponse->assertSee('Saurabh Kumar');

        // 3. Store new salary structure
        $storeResponse = $this->actingAs($user)->post(route('school.payroll.salary-structure.store'), [
            'staff_id' => $staff->id,
            'basic_salary' => 20000.00,
            'salary_type' => 'Monthly',
            'hra' => 2000.00,
            'da' => 1000.00,
            'ta' => 1000.00,
            'allowance' => 500.00,
            'pf' => 500.00,
            'esi' => 200.00,
            'tds' => 1000.00,
            'prof_tax' => 100.00,
            'effective_from' => '2026-04-11',
            'is_active' => 1,
        ]);

        $storeResponse->assertRedirect(route('school.payroll.salary-structure'));

        $this->assertDatabaseHas('staff_salary_structures', [
            'school_id' => $school->id,
            'staff_id' => $staff->id,
            'basic_salary' => 20000.00,
            'salary_type' => 'Monthly',
            'hra' => 2000.00,
            'da' => 1000.00,
            'ta' => 1000.00,
            'allowance' => 500.00,
            'pf' => 500.00,
            'esi' => 200.00,
            'tds' => 1000.00,
            'prof_tax' => 100.00,
            'is_active' => 1,
        ]);

        // Check staff basic salary synced
        $staff->refresh();
        $this->assertEquals(20000.00, $staff->basic_salary);

        // 4. Edit existing salary structure (should update without duplicate)
        $updateResponse = $this->actingAs($user)->post(route('school.payroll.salary-structure.store'), [
            'staff_id' => $staff->id,
            'basic_salary' => 25000.00,
            'salary_type' => 'Monthly',
            'hra' => 2500.00,
            'da' => 1200.00,
            'ta' => 1200.00,
            'allowance' => 600.00,
            'pf' => 600.00,
            'esi' => 250.00,
            'tds' => 1100.00,
            'prof_tax' => 150.00,
            'effective_from' => '2026-04-11',
            'is_active' => 1,
        ]);

        $updateResponse->assertRedirect(route('school.payroll.salary-structure'));

        $this->assertEquals(1, \App\Models\StaffSalaryStructure::where('school_id', $school->id)->where('staff_id', $staff->id)->count());

        $this->assertDatabaseHas('staff_salary_structures', [
            'school_id' => $school->id,
            'staff_id' => $staff->id,
            'basic_salary' => 25000.00,
            'hra' => 2500.00,
        ]);
    }

    public function test_deposit_amount_search_and_transaction_workflow(): void
    {
        [$school, $user, $dept, $desig] = $this->createSchoolUserDeptAndDesig();

        $staff = Staff::create([
            'school_id' => $school->id,
            'department_id' => $dept->id,
            'designation_id' => $desig->id,
            'joining_date' => '2025-01-01',
            'employee_id' => 'EDUZENEMP003',
            'first_name' => 'Neha',
            'last_name' => 'Maurya',
            'phone' => '5646546556',
            'basic_salary' => 35000.00,
            'is_active' => true,
        ]);

        // 1. Employee search page loads (Page 1)
        $searchPageResponse = $this->actingAs($user)->get(route('school.payroll.deposit-amount'));
        $searchPageResponse->assertStatus(200);
        $searchPageResponse->assertSee('Employee Search');
        $searchPageResponse->assertSee('Find Employee');
        $searchPageResponse->assertSee('Neha Maurya');

        // 2. Select employee via search parameter (Page 2)
        $depositPageResponse = $this->actingAs($user)->get(route('school.payroll.deposit-amount', ['staff_id' => $staff->id]));
        $depositPageResponse->assertStatus(200);
        $depositPageResponse->assertSee('Deposit Amount');
        $depositPageResponse->assertSee('Neha Maurya');
        $depositPageResponse->assertSee('EDUZENEMP003');
        $depositPageResponse->assertSee('5646546556');
        $depositPageResponse->assertSee('0.00'); // Initial balance

        // 3. Perform deposit transaction
        $storeResponse = $this->actingAs($user)->post(route('school.payroll.deposit-amount.store'), [
            'staff_id' => $staff->id,
            'amount' => 5000.00,
            'payment_mode' => 'Bank Transfer',
            'transaction_type' => 'Salary Advance',
            'remark' => 'Festival advance deposit',
        ]);

        $storeResponse->assertRedirect(route('school.payroll.deposit-amount', ['staff_id' => $staff->id]));

        // Assert database record created
        $this->assertDatabaseHas('staff_payroll_deposits', [
            'school_id' => $school->id,
            'staff_id' => $staff->id,
            'amount' => 5000.00,
            'payment_mode' => 'Bank Transfer',
            'transaction_type' => 'Salary Advance',
            'balance_after_transaction' => 5000.00,
            'remark' => 'Festival advance deposit',
        ]);

        // Refresh staff and check balance
        $staff->refresh();
        $this->assertEquals(5000.00, $staff->payroll_balance);

        // 4. Perform second deposit transaction
        $storeResponse2 = $this->actingAs($user)->post(route('school.payroll.deposit-amount.store'), [
            'staff_id' => $staff->id,
            'amount' => 2000.00,
            'payment_mode' => 'UPI',
            'transaction_type' => 'Deposit',
            'remark' => 'Additional deposit',
        ]);

        $storeResponse2->assertRedirect(route('school.payroll.deposit-amount', ['staff_id' => $staff->id]));

        $this->assertDatabaseHas('staff_payroll_deposits', [
            'school_id' => $school->id,
            'staff_id' => $staff->id,
            'amount' => 2000.00,
            'balance_after_transaction' => 7000.00,
        ]);

        $staff->refresh();
        $this->assertEquals(7000.00, $staff->payroll_balance);

        // 5. Attempt deposit exceeding configured salary (35000.00)
        $excessResponse = $this->actingAs($user)->post(route('school.payroll.deposit-amount.store'), [
            'staff_id' => $staff->id,
            'amount' => 50000.00,
            'payment_mode' => 'Cash',
            'transaction_type' => 'Salary Advance',
            'remark' => 'Excess advance test',
        ]);

        $excessResponse->assertSessionHas('error');
    }
}


