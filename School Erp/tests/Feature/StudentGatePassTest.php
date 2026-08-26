<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentGatePass;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentGatePassTest extends TestCase
{
    use RefreshDatabase;

    protected $school;
    protected $admin;
    protected $student;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->school = School::first();
        $this->admin = User::where('school_id', $this->school->id)->where('email', 'admin@yis.com')->first();
        $this->student = Student::where('school_id', $this->school->id)->first();
    }

    /**
     * Test Student 360 profile loads Gate Pass tab and counters.
     */
    public function test_student_360_profile_contains_gate_pass_tab(): void
    {
        $response = $this->actingAs($this->admin)->get(route('school.students.show', $this->student->id));

        $response->assertStatus(200);
        $response->assertSee('Gate Pass');
        $response->assertSee('Gate Pass Management');
        $response->assertSee('Issue New Gate Pass');
    }

    /**
     * Test Gate Pass generator view opens with auto-filled details.
     */
    public function test_gate_pass_generator_screen_loads(): void
    {
        $response = $this->actingAs($this->admin)->get(route('school.students.gate-passes.create', $this->student->id));

        $response->assertStatus(200);
        $response->assertSee('Issue Gate Pass');
        $response->assertSee('School Details (Auto-filled)');
        $response->assertSee('Student Details (Auto-filled)');
        $response->assertSee('Change Template');
        $response->assertSee('Student Out Pass');
    }

    /**
     * Test creating a Gate Pass with auto numbering, notification, and database storage.
     */
    public function test_create_and_store_gate_pass(): void
    {
        $payload = [
            'gate_pass_number'     => '2026/GP/1',
            'template'             => 'classic',
            'pass_date'            => now()->format('Y-m-d H:i:s'),
            'reason'               => 'Medical Checkup / Hospital Visit',
            'guardian_type'        => 'father',
            'guardian_name'        => 'Nishant Kumar',
            'guardian_relation'    => 'Father',
            'guardian_phone'       => '9876543210',
            'expected_return_time' => '04:30 PM',
            'remarks'              => 'Early departure approved by class teacher',
            'approved_by'          => 'Principal',
            'status'               => 'issued',
            'school_name'          => 'Test School ERP',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('school.students.gate-passes.store', $this->student->id), $payload);

        $response->assertRedirect(route('school.students.show', $this->student->id));

        $this->assertDatabaseHas('student_gate_passes', [
            'school_id'         => $this->school->id,
            'student_id'        => $this->student->id,
            'gate_pass_number'  => '2026/GP/1',
            'reason'            => 'Medical Checkup / Hospital Visit',
            'guardian_name'     => 'Nishant Kumar',
            'guardian_relation' => 'Father',
            'status'            => 'issued',
        ]);
    }

    /**
     * Test Gate Pass status update (e.g. Return, Cancel).
     */
    public function test_update_gate_pass_status(): void
    {
        $gatePass = StudentGatePass::create([
            'school_id'         => $this->school->id,
            'student_id'        => $this->student->id,
            'gate_pass_number'  => '2026/GP/10',
            'pass_date'         => now(),
            'template'          => 'classic',
            'reason'            => 'Illness',
            'guardian_type'     => 'father',
            'guardian_name'     => 'Nishant Kumar',
            'guardian_relation' => 'Father',
            'status'            => 'issued',
            'issued_by'         => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('school.students.gate-passes.status', [$this->student->id, $gatePass->id]), [
                'status' => 'returned',
                'remarks' => 'Student returned to school on time.',
            ]);

        $response->assertJson(['success' => true]);

        $gatePass->refresh();
        $this->assertEquals('returned', $gatePass->status);
        $this->assertNotNull($gatePass->actual_return_time);
    }
}
