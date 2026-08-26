<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Designation;
use App\Models\School;
use App\Models\Staff;
use App\Models\StaffGatePass;
use App\Models\Student;
use App\Models\StudentGatePass;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffGatePassAndHistoryTest extends TestCase
{
    use RefreshDatabase;

    protected $school;
    protected $admin;
    protected $staff;
    protected $student;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->school = School::first();
        $this->admin = User::where('school_id', $this->school->id)->where('email', 'admin@yis.com')->first();
        $this->student = Student::where('school_id', $this->school->id)->first();

        $this->staff = Staff::where('school_id', $this->school->id)->first();
        if (!$this->staff) {
            $dept = Department::firstOrCreate(['school_id' => $this->school->id], ['name' => 'Academics']);
            $desig = Designation::firstOrCreate(['school_id' => $this->school->id], ['name' => 'Teacher']);

            $this->staff = Staff::create([
                'school_id' => $this->school->id,
                'employee_id' => 'EMP-0099',
                'first_name' => 'Amitabh',
                'last_name' => 'Verma',
                'department_id' => $dept->id,
                'designation_id' => $desig->id,
                'email' => 'amitabh@yis.com',
                'phone' => '9123456780',
                'gender' => 'male',
                'joining_date' => now()->subYears(2),
                'is_active' => true,
            ]);
        }
    }

    /**
     * Test 1: Staff 360 profile contains Gate Pass tab.
     */
    public function test_staff_360_profile_contains_gate_pass_tab(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('school.staff.show', $this->staff->id));

        $response->assertStatus(200);
        $response->assertSee('Gate Pass');
        $response->assertSee('Staff Gate Pass Records');
        $response->assertSee('Generate Gate Pass');
    }

    /**
     * Test 2: Staff Gate Pass generator screen loads properly.
     */
    public function test_staff_gate_pass_generator_screen_loads(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('school.staff.gate-passes.create', $this->staff->id));

        $response->assertStatus(200);
        $response->assertSee('Generate Staff Gate Pass');
        $response->assertSee($this->staff->full_name);
        $response->assertSee($this->staff->employee_id);
        $response->assertSee('Official School Duty');
    }

    /**
     * Test 3: Create and issue Staff Gate Pass.
     */
    public function test_create_and_store_staff_gate_pass(): void
    {
        $postData = [
            'reason' => 'Official School Duty',
            'expected_return_time' => '04:00 PM',
            'template' => 'classic',
            'remarks' => 'Visiting Education Board Office',
            'approved_by' => 'Dr. R. K. Singh',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('school.staff.gate-passes.store', $this->staff->id), $postData);

        $response->assertRedirect(route('school.staff.show', ['staff' => $this->staff->id, 'tab' => 'gatepass']));

        $this->assertDatabaseHas('staff_gate_passes', [
            'school_id' => $this->school->id,
            'staff_id' => $this->staff->id,
            'reason' => 'Official School Duty',
            'expected_return_time' => '04:00 PM',
            'status' => 'issued',
        ]);
    }

    /**
     * Test 4: School-wide Student Gate Pass History list and Cancellation.
     */
    public function test_school_wide_student_gate_pass_history_and_cancellation(): void
    {
        $studentPass = StudentGatePass::create([
            'school_id' => $this->school->id,
            'student_id' => $this->student->id,
            'gate_pass_number' => '2026/GP/1',
            'pass_date' => now(),
            'template' => 'classic',
            'reason' => 'Medical Checkup',
            'guardian_name' => 'Vikram Sharma',
            'guardian_relation' => 'Father',
            'status' => 'issued',
        ]);

        // Access History List
        $response = $this->actingAs($this->admin)
            ->get(route('school.gate-passes.students.index'));

        $response->assertStatus(200);
        $response->assertSee('Student Outpass History');
        $response->assertSee('2026/GP/1');
        $response->assertSee($this->student->full_name);

        // Cancel Student Gate Pass
        $cancelResponse = $this->actingAs($this->admin)
            ->post(route('school.gate-passes.students.cancel', $studentPass->id), [
                'cancellation_reason' => 'Parent cancelled the doctor visit',
            ]);

        $cancelResponse->assertSessionHas('success');

        $this->assertDatabaseHas('student_gate_passes', [
            'id' => $studentPass->id,
            'status' => 'cancelled',
        ]);
    }

    /**
     * Test 5: School-wide Staff Gate Pass History list and Cancellation.
     */
    public function test_school_wide_staff_gate_pass_history_and_cancellation(): void
    {
        $staffPass = StaffGatePass::create([
            'school_id' => $this->school->id,
            'staff_id' => $this->staff->id,
            'gate_pass_number' => '2026/SGP/1',
            'pass_date' => now(),
            'template' => 'classic',
            'reason' => 'Bank Duty',
            'status' => 'issued',
        ]);

        // Access History List
        $response = $this->actingAs($this->admin)
            ->get(route('school.gate-passes.staff.index'));

        $response->assertStatus(200);
        $response->assertSee('Staff Outpass History');
        $response->assertSee('2026/SGP/1');
        $response->assertSee($this->staff->full_name);

        // Cancel Staff Gate Pass
        $cancelResponse = $this->actingAs($this->admin)
            ->post(route('school.gate-passes.staff.cancel', $staffPass->id), [
                'cancellation_reason' => 'Meeting rescheduled',
            ]);

        $cancelResponse->assertSessionHas('success');

        $this->assertDatabaseHas('staff_gate_passes', [
            'id' => $staffPass->id,
            'status' => 'cancelled',
            'cancellation_reason' => 'Meeting rescheduled',
        ]);
    }
}
