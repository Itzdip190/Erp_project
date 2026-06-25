<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\School;
use App\Models\AcademicSession;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Staff;
use App\Models\TimetableGroup;
use App\Models\TimetableGroupPeriod;
use App\Models\ClassSubjectTeacher;
use App\Models\ClassTimetableCell;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TimetableModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected School $school;
    protected AcademicSession $session;
    protected SchoolClass $class;
    protected Section $section;
    protected Subject $subject;
    protected Staff $teacher;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed standard DB structure
        $this->seed(DatabaseSeeder::class);

        // Fetch seeded structures
        $this->admin = User::where('email', 'admin@yis.com')->first();
        $this->school = School::find($this->admin->school_id);
        $this->session = AcademicSession::where('school_id', $this->school->id)->where('is_current', true)->first()
            ?? AcademicSession::where('school_id', $this->school->id)->first();
        $this->class = SchoolClass::where('school_id', $this->school->id)->where('name', 'Class 9')->first()
            ?? SchoolClass::where('school_id', $this->school->id)->first();
        $this->section = Section::where('class_id', $this->class->id)->first()
            ?? Section::create([
                'school_id' => $this->school->id,
                'class_id' => $this->class->id,
                'name' => 'A'
            ]);
        $this->subject = Subject::where('class_id', $this->class->id)->first()
            ?? Subject::create([
                'school_id' => $this->school->id,
                'class_id' => $this->class->id,
                'name' => 'Maths',
                'code' => 'MATH101',
                'type' => 'theory',
                'max_marks' => 100,
                'pass_marks' => 33
            ]);
        $this->teacher = Staff::where('school_id', $this->school->id)->first();
    }

    /**
     * Test groups templates index page loads successfully.
     */
    public function test_groups_templates_index_loads(): void
    {
        $response = $this->actingAs($this->admin)
            ->withHeaders(['X-School-Code' => $this->school->code])
            ->get(route('school.timetable.group'));

        $response->assertStatus(200);
        $response->assertViewHas('groups');
        $response->assertViewHas('classes');
    }

    /**
     * Test creating a new group template.
     */
    public function test_create_group_template_wizard(): void
    {
        $postData = [
            'group_name' => 'Primary Morning Shift',
            'start_date' => '2026-04-01',
            'end_date' => '2027-03-31',
            'academic_session_id' => $this->session->id,
            'class_start_time' => '08:00',
            'number_of_periods' => 3,
            'applicable_days' => ['Monday', 'Tuesday', 'Wednesday'],
            'periods' => [
                ['period_name' => 'Period 1', 'duration_minutes' => 45],
                ['period_name' => 'Interval', 'duration_minutes' => 15],
                ['period_name' => 'Period 2', 'duration_minutes' => 45]
            ],
            'class_sections' => [
                "{$this->class->id}-{$this->section->id}"
            ]
        ];

        $response = $this->actingAs($this->admin)
            ->withHeaders(['X-School-Code' => $this->school->code])
            ->postJson(route('school.timetable.group.store'), $postData);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Assert DB structures
        $this->assertDatabaseHas('timetable_groups', [
            'group_name' => 'Primary Morning Shift',
            'school_id' => $this->school->id
        ]);

        $group = TimetableGroup::where('group_name', 'Primary Morning Shift')->first();
        $this->assertCount(3, $group->periods);
        $this->assertEquals('Period 1', $group->periods[0]->period_name);
        $this->assertEquals('08:00:00', $group->periods[0]->start_time);
        $this->assertEquals('08:45:00', $group->periods[0]->end_time);

        // check intermediate class section association
        $this->assertDatabaseHas('timetable_group_class_section', [
            'timetable_group_id' => $group->id,
            'class_id' => $this->class->id,
            'section_id' => $this->section->id
        ]);
    }

    /**
     * Test toggling active/inactive status.
     */
    public function test_toggle_group_template_status(): void
    {
        $group = TimetableGroup::create([
            'school_id' => $this->school->id,
            'group_name' => 'Temporary Group',
            'start_date' => '2026-04-01',
            'end_date' => '2027-03-31',
            'academic_year' => $this->session->name,
            'class_start_time' => '08:00:00',
            'number_of_periods' => 1,
            'applicable_days' => ['Monday'],
            'is_active' => true,
            'created_by' => $this->admin->id
        ]);

        $response = $this->actingAs($this->admin)
            ->withHeaders(['X-School-Code' => $this->school->code])
            ->patchJson(route('school.timetable.group.toggle-status', $group->id));

        $response->assertStatus(200);
        $this->assertFalse($group->refresh()->is_active);
    }

    /**
     * Test Class Timetable dashboard view loads.
     */
    public function test_class_timetable_dashboard_loads(): void
    {
        $response = $this->actingAs($this->admin)
            ->withHeaders(['X-School-Code' => $this->school->code])
            ->get(route('school.timetable.class'));

        $response->assertStatus(200);
        $response->assertViewHas('classList');
        $response->assertViewHas('teachers');
    }

    /**
     * Test Teacher Check assignment.
     */
    public function test_check_teacher_assignment(): void
    {
        // 1. Unassigned check
        $response = $this->actingAs($this->admin)
            ->withHeaders(['X-School-Code' => $this->school->code])
            ->getJson(route('school.timetable.check-teacher', [
                'class_id' => $this->class->id,
                'section_id' => $this->section->id,
                'subject_id' => $this->subject->id
            ]));

        $response->assertStatus(200);
        $response->assertJson(['assigned' => false]);

        // 2. Assign and check again
        ClassSubjectTeacher::create([
            'school_id' => $this->school->id,
            'class_id' => $this->class->id,
            'section_id' => $this->section->id,
            'subject_id' => $this->subject->id,
            'teacher_id' => $this->teacher->id
        ]);

        $response2 = $this->actingAs($this->admin)
            ->withHeaders(['X-School-Code' => $this->school->code])
            ->getJson(route('school.timetable.check-teacher', [
                'class_id' => $this->class->id,
                'section_id' => $this->section->id,
                'subject_id' => $this->subject->id
            ]));

        $response2->assertStatus(200);
        $response2->assertJson([
            'assigned' => true,
            'teacher_id' => $this->teacher->id
        ]);
    }

    /**
     * Test scheduling a timetable slot.
     */
    public function test_schedule_timetable_cell_slot(): void
    {
        // Create active group template first
        $group = TimetableGroup::create([
            'school_id' => $this->school->id,
            'group_name' => 'Weekly Group',
            'start_date' => '2026-04-01',
            'end_date' => '2027-03-31',
            'academic_year' => $this->session->name,
            'class_start_time' => '08:00:00',
            'number_of_periods' => 1,
            'applicable_days' => ['Monday'],
            'is_active' => true,
            'created_by' => $this->admin->id
        ]);

        $period = TimetableGroupPeriod::create([
            'school_id' => $this->school->id,
            'timetable_group_id' => $group->id,
            'period_name' => 'Period 1',
            'duration_minutes' => 45,
            'start_time' => '08:00:00',
            'end_time' => '08:45:00',
            'sort_order' => 0
        ]);

        // Bind group to class section
        \DB::table('timetable_group_class_section')->insert([
            'school_id' => $this->school->id,
            'timetable_group_id' => $group->id,
            'class_id' => $this->class->id,
            'section_id' => $this->section->id
        ]);

        // 1. Drop check - should return teacher required true
        $postData = [
            'timetable_group_id' => $group->id,
            'class_id' => $this->class->id,
            'section_id' => $this->section->id,
            'timetable_group_period_id' => $period->id,
            'day_of_week' => 'Monday',
            'subject_id' => $this->subject->id,
            'mode' => 'online'
        ];

        $response = $this->actingAs($this->admin)
            ->withHeaders(['X-School-Code' => $this->school->code])
            ->postJson(route('school.timetable.class.cell.save'), $postData);

        $response->assertStatus(200);
        $response->assertJson(['teacher_required' => true]);

        // 2. Drop with Teacher Assignment endpoint
        $assignData = [
            'class_id' => $this->class->id,
            'section_id' => $this->section->id,
            'subject_id' => $this->subject->id,
            'teacher_id' => $this->teacher->id,
            'timetable_group_id' => $group->id,
            'timetable_group_period_id' => $period->id,
            'day_of_week' => 'Monday',
            'mode' => 'offline'
        ];

        $response2 = $this->actingAs($this->admin)
            ->withHeaders(['X-School-Code' => $this->school->code])
            ->postJson(route('school.timetable.assign-teacher'), $assignData);

        $response2->assertStatus(200);
        $response2->assertJson(['success' => true]);

        // Assert cell saved
        $this->assertDatabaseHas('class_timetable_cells', [
            'timetable_group_id' => $group->id,
            'class_id' => $this->class->id,
            'section_id' => $this->section->id,
            'timetable_group_period_id' => $period->id,
            'day_of_week' => 'Monday',
            'subject_id' => $this->subject->id,
            'teacher_id' => $this->teacher->id,
            'mode' => 'offline'
        ]);

        $cell = ClassTimetableCell::first();

        // 3. Test replication of cell
        $replicateData = [
            'targets' => [
                [
                    'timetable_group_period_id' => $period->id,
                    'day_of_week' => 'Tuesday'
                ]
            ]
        ];

        $response3 = $this->actingAs($this->admin)
            ->withHeaders(['X-School-Code' => $this->school->code])
            ->postJson(route('school.timetable.class.cell.copy', $cell->id), $replicateData);

        $response3->assertStatus(200);

        $this->assertDatabaseHas('class_timetable_cells', [
            'day_of_week' => 'Tuesday',
            'subject_id' => $this->subject->id
        ]);

        // 4. Test deleting cell
        $response4 = $this->actingAs($this->admin)
            ->withHeaders(['X-School-Code' => $this->school->code])
            ->deleteJson(route('school.timetable.class.cell.delete', $cell->id));

        $response4->assertStatus(200);
        $this->assertDatabaseMissing('class_timetable_cells', [
            'id' => $cell->id
        ]);
    }
}
