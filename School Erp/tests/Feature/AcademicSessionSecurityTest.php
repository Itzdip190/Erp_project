<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicSessionSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_is_school_admin_helper_returns_correctly(): void
    {
        $admin = User::factory()->create(['role' => 'school_admin']);
        $superadmin = User::factory()->create(['role' => 'superadmin']);
        $teacher = User::factory()->create(['role' => 'teacher']);
        $staff = User::factory()->create(['role' => 'staff']);
        $student = User::factory()->create(['role' => 'student']);

        $this->assertTrue($admin->isSchoolAdmin());
        $this->assertTrue($superadmin->isSchoolAdmin());
        $this->assertFalse($teacher->isSchoolAdmin());
        $this->assertFalse($staff->isSchoolAdmin());
        $this->assertFalse($student->isSchoolAdmin());
    }

    public function test_school_admin_can_change_academic_session(): void
    {
        $school = School::firstOrCreate(
            ['code' => 'SCH_ADMIN_TEST'],
            ['name' => 'Admin Test School', 'status' => 'active']
        );
        $admin = User::factory()->create([
            'school_id' => $school->id,
            'role' => 'school_admin',
        ]);

        $session1 = AcademicSession::create([
            'school_id' => $school->id,
            'name' => '2025-2026',
            'start_date' => '2025-04-01',
            'end_date' => '2026-03-31',
            'is_current' => true,
        ]);

        $session2 = AcademicSession::create([
            'school_id' => $school->id,
            'name' => '2026-2027',
            'start_date' => '2026-04-01',
            'end_date' => '2027-03-31',
            'is_current' => false,
        ]);

        $response = $this->actingAs($admin)
            ->postJson(route('school.dashboard.change-session'), [
                'academic_session_id' => $session2->id,
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertFalse((bool)$session1->fresh()->is_current);
        $this->assertTrue((bool)$session2->fresh()->is_current);
    }

    public function test_teacher_or_staff_cannot_change_academic_session(): void
    {
        $school = School::firstOrCreate(
            ['code' => 'SCH_TEACHER_TEST'],
            ['name' => 'Teacher Test School', 'status' => 'active']
        );
        $teacher = User::factory()->create([
            'school_id' => $school->id,
            'role' => 'teacher',
        ]);

        $session1 = AcademicSession::create([
            'school_id' => $school->id,
            'name' => '2025-2026',
            'start_date' => '2025-04-01',
            'end_date' => '2026-03-31',
            'is_current' => true,
        ]);

        $session2 = AcademicSession::create([
            'school_id' => $school->id,
            'name' => '2026-2027',
            'start_date' => '2026-04-01',
            'end_date' => '2027-03-31',
            'is_current' => false,
        ]);

        $response = $this->actingAs($teacher)
            ->postJson(route('school.dashboard.change-session'), [
                'academic_session_id' => $session2->id,
            ]);

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
        ]);

        // Verify session did not change in database
        $this->assertTrue((bool)$session1->fresh()->is_current);
        $this->assertFalse((bool)$session2->fresh()->is_current);
    }

    public function test_superadmin_can_delete_academic_session(): void
    {
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'superadmin']);
        $superadmin = User::factory()->create(['role' => 'superadmin']);
        $superadmin->assignRole('superadmin');

        $school = School::firstOrCreate(
            ['code' => 'SCH_DEL_TEST'],
            ['name' => 'Delete Test School', 'status' => 'active']
        );

        $session1 = AcademicSession::create([
            'school_id' => $school->id,
            'name' => '2025-2026',
            'start_date' => '2025-04-01',
            'end_date' => '2026-03-31',
            'is_current' => false,
        ]);

        $session2 = AcademicSession::create([
            'school_id' => $school->id,
            'name' => '2026-2027',
            'start_date' => '2026-04-01',
            'end_date' => '2027-03-31',
            'is_current' => true,
        ]);

        $response = $this->actingAs($superadmin)
            ->deleteJson(route('superadmin.schools.academic-sessions.destroy', [
                'school' => $school->id,
                'academicSession' => $session1->id,
            ]));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseMissing('academic_sessions', ['id' => $session1->id]);
    }

    public function test_superadmin_cannot_delete_the_only_academic_session(): void
    {
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'superadmin']);
        $superadmin = User::factory()->create(['role' => 'superadmin']);
        $superadmin->assignRole('superadmin');

        $school = School::firstOrCreate(
            ['code' => 'SCH_DEL_ONE'],
            ['name' => 'Single Session School', 'status' => 'active']
        );

        $session1 = AcademicSession::create([
            'school_id' => $school->id,
            'name' => '2025-2026',
            'start_date' => '2025-04-01',
            'end_date' => '2026-03-31',
            'is_current' => true,
        ]);

        $response = $this->actingAs($superadmin)
            ->deleteJson(route('superadmin.schools.academic-sessions.destroy', [
                'school' => $school->id,
                'academicSession' => $session1->id,
            ]));

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
        $this->assertDatabaseHas('academic_sessions', ['id' => $session1->id]);
    }

    public function test_deleting_current_session_switches_current_to_fallback(): void
    {
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'superadmin']);
        $superadmin = User::factory()->create(['role' => 'superadmin']);
        $superadmin->assignRole('superadmin');

        $school = School::firstOrCreate(
            ['code' => 'SCH_DEL_CURR'],
            ['name' => 'Current Session School', 'status' => 'active']
        );

        $session1 = AcademicSession::create([
            'school_id' => $school->id,
            'name' => '2025-2026',
            'start_date' => '2025-04-01',
            'end_date' => '2026-03-31',
            'is_current' => false,
        ]);

        $session2 = AcademicSession::create([
            'school_id' => $school->id,
            'name' => '2026-2027',
            'start_date' => '2026-04-01',
            'end_date' => '2027-03-31',
            'is_current' => true,
        ]);

        $response = $this->actingAs($superadmin)
            ->deleteJson(route('superadmin.schools.academic-sessions.destroy', [
                'school' => $school->id,
                'academicSession' => $session2->id,
            ]));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseMissing('academic_sessions', ['id' => $session2->id]);
        $this->assertTrue((bool)$session1->fresh()->is_current);
    }
}
