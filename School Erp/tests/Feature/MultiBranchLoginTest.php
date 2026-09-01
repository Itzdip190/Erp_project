<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\BranchGroup;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use App\Models\Scopes\SchoolScope;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MultiBranchLoginTest extends TestCase
{
    use RefreshDatabase;

    protected School $school1;
    protected School $school2;
    protected School $school3;
    protected School $schoolUnassigned;
    protected User $superAdmin;
    protected User $admin1;
    protected User $admin2;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::firstOrCreate(['name' => 'superadmin']);
        Role::firstOrCreate(['name' => 'school_admin']);
        Role::firstOrCreate(['name' => 'teacher']);
        Role::firstOrCreate(['name' => 'student']);

        // Create School 1
        $this->school1 = School::create([
            'name'   => "St. Xavier's High School, Aurangabad",
            'code'   => 'SLBR2603164',
            'status' => 'active',
        ]);

        // Create School 2
        $this->school2 = School::create([
            'name'   => 'St. Xavier high school (Gopalganj)',
            'code'   => 'SXHG2026',
            'status' => 'active',
        ]);

        // Create School 3
        $this->school3 = School::create([
            'name'   => 'St. Xavier School, Patna',
            'code'   => 'SXSP2026',
            'status' => 'active',
        ]);

        // Create Unassigned School (different organization)
        $this->schoolUnassigned = School::create([
            'name'   => 'Delhi Public School',
            'code'   => 'DPS2026',
            'status' => 'active',
        ]);

        // Create Academic Sessions for schools
        AcademicSession::create([
            'school_id'  => $this->school1->id,
            'name'       => '2026-2027',
            'start_date' => '2026-04-01',
            'end_date'   => '2027-03-31',
            'is_current' => true,
        ]);
        AcademicSession::create([
            'school_id'  => $this->school2->id,
            'name'       => '2026-2027',
            'start_date' => '2026-04-01',
            'end_date'   => '2027-03-31',
            'is_current' => true,
        ]);
        AcademicSession::create([
            'school_id'  => $this->school3->id,
            'name'       => '2026-2027',
            'start_date' => '2026-04-01',
            'end_date'   => '2027-03-31',
            'is_current' => true,
        ]);

        // Create Super Admin user
        $this->superAdmin = User::create([
            'name'      => 'Super Admin',
            'email'     => 'superadmin@erp.com',
            'password'  => Hash::make('Password@123'),
            'role'      => 'superadmin',
            'is_active' => true,
        ]);
        $this->superAdmin->assignRole('superadmin');

        // Create Admin for School 1
        $this->admin1 = User::create([
            'name'      => 'Admin School 1',
            'email'     => 'admin1@stxavier.com',
            'password'  => Hash::make('Password@123'),
            'school_id' => $this->school1->id,
            'role'      => 'school_admin',
            'is_active' => true,
        ]);
        $this->admin1->assignRole('school_admin');

        // Create Admin for School 2
        $this->admin2 = User::create([
            'name'      => 'Admin School 2',
            'email'     => 'admin2@stxavier.com',
            'password'  => Hash::make('Password@123'),
            'school_id' => $this->school2->id,
            'role'      => 'school_admin',
            'is_active' => true,
        ]);
        $this->admin2->assignRole('school_admin');
    }

    /**
     * Scenario 1: Super Admin can configure branch group and link multiple schools.
     */
    public function test_superadmin_can_assign_branch_schools_and_create_branch_group(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->put(route('superadmin.schools.update', $this->school1->id), [
                'name'                        => $this->school1->name,
                'code'                        => $this->school1->code,
                'status'                      => 'active',
                'admin_name'                  => $this->admin1->name,
                'admin_email'                 => $this->admin1->email,
                'academic_session_name'       => '2026-2027',
                'academic_session_start_date' => '2026-04-01',
                'academic_session_end_date'   => '2027-03-31',
                'branch_access_enabled'       => '1',
                'branch_school_ids'           => [$this->school2->id, $this->school3->id],
                'branch_group_name'           => 'St. Xavier Group of Schools',
            ]);

        $response->assertRedirect(route('superadmin.schools.index'));

        // Refresh schools
        $this->school1->refresh();
        $this->school2->refresh();
        $this->school3->refresh();
        $this->schoolUnassigned->refresh();

        // Verify all 3 schools share the same branch group
        $this->assertTrue($this->school1->branch_access_enabled);
        $this->assertTrue($this->school2->branch_access_enabled);
        $this->assertTrue($this->school3->branch_access_enabled);
        $this->assertNotNull($this->school1->branch_group_id);
        $this->assertEquals($this->school1->branch_group_id, $this->school2->branch_group_id);
        $this->assertEquals($this->school1->branch_group_id, $this->school3->branch_group_id);

        // Verify unassigned school was NOT linked
        $this->assertFalse($this->schoolUnassigned->branch_access_enabled);
        $this->assertNull($this->schoolUnassigned->branch_group_id);

        // Verify branch group name
        $group = BranchGroup::find($this->school1->branch_group_id);
        $this->assertEquals('St. Xavier Group of Schools', $group->name);
    }

    /**
     * Scenario 2 & 3: Accessible branches return ONLY assigned branch schools, never unassigned.
     */
    public function test_accessible_branches_returns_only_assigned_active_branch_schools(): void
    {
        $group = BranchGroup::create(['name' => 'St. Xavier Group']);
        $this->school1->update(['branch_group_id' => $group->id, 'branch_access_enabled' => true]);
        $this->school2->update(['branch_group_id' => $group->id, 'branch_access_enabled' => true]);
        $this->school3->update(['branch_group_id' => $group->id, 'branch_access_enabled' => true]);

        $branches1 = $this->school1->getAccessibleBranches();
        $branches2 = $this->school2->getAccessibleBranches();

        $branchIds1 = $branches1->pluck('id')->all();
        $branchIds2 = $branches2->pluck('id')->all();

        // Assigned schools appear
        $this->assertContains($this->school1->id, $branchIds1);
        $this->assertContains($this->school2->id, $branchIds1);
        $this->assertContains($this->school3->id, $branchIds1);

        // Both schools see each other
        $this->assertContains($this->school1->id, $branchIds2);
        $this->assertContains($this->school2->id, $branchIds2);
        $this->assertContains($this->school3->id, $branchIds2);

        // Unassigned school NEVER appears
        $this->assertNotContains($this->schoolUnassigned->id, $branchIds1);
        $this->assertNotContains($this->schoolUnassigned->id, $branchIds2);
    }

    /**
     * Scenario 4 & 7: Switching between assigned schools works without logging out and updates session.
     */
    public function test_authenticated_school_user_can_switch_to_assigned_branch_seamlessly(): void
    {
        $group = BranchGroup::create(['name' => 'St. Xavier Group']);
        $this->school1->update(['branch_group_id' => $group->id, 'branch_access_enabled' => true]);
        $this->school2->update(['branch_group_id' => $group->id, 'branch_access_enabled' => true]);

        // Login as School 1 Admin
        $this->actingAs($this->admin1);

        $this->assertEquals($this->school1->id, auth()->user()->school_id);

        // Switch to School 2
        $response = $this->post(route('school.switch-branch'), [
            'school_id' => $this->school2->id,
        ]);

        $response->assertRedirect(route('school.dashboard'));
        $response->assertSessionHas('success');
        $response->assertSessionHas('school_id', $this->school2->id);
        $response->assertSessionHas('school_code', $this->school2->code);

        // User is still authenticated
        $this->assertTrue(Auth::check());

        // User active school is now School 2
        $this->admin1->refresh();
        $this->assertEquals($this->school2->id, $this->admin1->school_id);
    }

    /**
     * Scenario 5 & 6: Tenant isolation is strictly enforced with zero cross-school data leakage.
     */
    public function test_tenant_isolation_is_strictly_enforced_after_branch_switch(): void
    {
        $group = BranchGroup::create(['name' => 'St. Xavier Group']);
        $this->school1->update(['branch_group_id' => $group->id, 'branch_access_enabled' => true]);
        $this->school2->update(['branch_group_id' => $group->id, 'branch_access_enabled' => true]);
        $class1 = SchoolClass::withoutGlobalScope(SchoolScope::class)->create([
            'school_id'    => $this->school1->id,
            'name'         => 'Class 1',
            'numeric_name' => 1,
        ]);
        $section1 = Section::withoutGlobalScope(SchoolScope::class)->create([
            'school_id' => $this->school1->id,
            'class_id'  => $class1->id,
            'name'      => 'A',
        ]);

        $class2 = SchoolClass::withoutGlobalScope(SchoolScope::class)->create([
            'school_id'    => $this->school2->id,
            'name'         => 'Class 1',
            'numeric_name' => 1,
        ]);
        $section2 = Section::withoutGlobalScope(SchoolScope::class)->create([
            'school_id' => $this->school2->id,
            'class_id'  => $class2->id,
            'name'      => 'A',
        ]);

        $session1 = AcademicSession::where('school_id', $this->school1->id)->first();
        $session2 = AcademicSession::where('school_id', $this->school2->id)->first();

        // Create student in School 1
        $student1 = Student::withoutGlobalScope(SchoolScope::class)->create([
            'school_id'             => $this->school1->id,
            'academic_session_id'   => $session1->id,
            'class_id'              => $class1->id,
            'section_id'            => $section1->id,
            'admission_number'      => 'STU-SCH1-001',
            'first_name'            => 'Aarav',
            'last_name'             => 'Sharma',
            'gender'                => 'Male',
            'date_of_birth'         => '2015-01-01',
            'admission_date'        => '2026-04-01',
            'guardian_name'         => 'Rajesh Sharma',
            'guardian_relationship' => 'Father',
            'guardian_phone'        => '9876543210',
            'address'               => '123 Test Street',
            'city'                  => 'Aurangabad',
            'state'                 => 'Maharashtra',
            'pincode'               => '431001',
            'is_active'             => true,
        ]);

        // Create student in School 2
        $student2 = Student::withoutGlobalScope(SchoolScope::class)->create([
            'school_id'             => $this->school2->id,
            'academic_session_id'   => $session2->id,
            'class_id'              => $class2->id,
            'section_id'            => $section2->id,
            'admission_number'      => 'STU-SCH2-001',
            'first_name'            => 'Rohan',
            'last_name'             => 'Verma',
            'gender'                => 'Male',
            'date_of_birth'         => '2015-02-02',
            'admission_date'        => '2026-04-01',
            'guardian_name'         => 'Suresh Verma',
            'guardian_relationship' => 'Father',
            'guardian_phone'        => '9876543211',
            'address'               => '456 Test Street',
            'city'                  => 'Gopalganj',
            'state'                 => 'Bihar',
            'pincode'               => '841428',
            'is_active'             => true,
        ]);

        // Login as Admin 1 in School 1
        $this->actingAs($this->admin1);

        // When in School 1, only School 1 students are visible
        $visibleStudentsSchool1 = Student::all();
        $this->assertCount(1, $visibleStudentsSchool1);
        $this->assertEquals('STU-SCH1-001', $visibleStudentsSchool1->first()->admission_number);

        // Switch to School 2
        $this->post(route('school.switch-branch'), [
            'school_id' => $this->school2->id,
        ]);

        // Refresh user
        $this->admin1->refresh();
        $this->assertEquals($this->school2->id, $this->admin1->school_id);

        // In School 2, ONLY School 2 students are visible; School 1 student is 100% hidden
        $visibleStudentsSchool2 = Student::all();
        $this->assertCount(1, $visibleStudentsSchool2);
        $this->assertEquals('STU-SCH2-001', $visibleStudentsSchool2->first()->admission_number);

        // Switch back to School 1
        $this->post(route('school.switch-branch'), [
            'school_id' => $this->school1->id,
        ]);

        $this->admin1->refresh();
        $this->assertEquals($this->school1->id, $this->admin1->school_id);

        $visibleStudentsReturned = Student::all();
        $this->assertCount(1, $visibleStudentsReturned);
        $this->assertEquals('STU-SCH1-001', $visibleStudentsReturned->first()->admission_number);
    }

    /**
     * Security check: Direct URL / form manipulation attempting to switch to an unassigned school returns 403 Forbidden.
     */
    public function test_unauthorized_branch_switch_is_blocked_with_403(): void
    {
        $group = BranchGroup::create(['name' => 'St. Xavier Group']);
        $this->school1->update(['branch_group_id' => $group->id, 'branch_access_enabled' => true]);
        $this->school2->update(['branch_group_id' => $group->id, 'branch_access_enabled' => true]);

        // Attempting to switch to unassigned School (DPS)
        $this->actingAs($this->admin1);

        $response = $this->post(route('school.switch-branch'), [
            'school_id' => $this->schoolUnassigned->id,
        ]);

        // Should be blocked (either 403 response or redirected to login per exception handler)
        $this->assertTrue(
            $response->status() === 403 || $response->isRedirect(route('login'))
        );

        // User should still belong to School 1
        $this->admin1->refresh();
        $this->assertEquals($this->school1->id, $this->admin1->school_id);
    }

    /**
     * Disabling branch access removes branch switching capability.
     */
    public function test_disabling_branch_access_revokes_switching(): void
    {
        $group = BranchGroup::create(['name' => 'St. Xavier Group']);
        $this->school1->update(['branch_group_id' => $group->id, 'branch_access_enabled' => true]);
        $this->school2->update(['branch_group_id' => $group->id, 'branch_access_enabled' => true]);

        // Super Admin disables branch access for School 1
        $this->actingAs($this->superAdmin)
            ->put(route('superadmin.schools.update', $this->school1->id), [
                'name'                        => $this->school1->name,
                'code'                        => $this->school1->code,
                'status'                      => 'active',
                'admin_name'                  => $this->admin1->name,
                'admin_email'                 => $this->admin1->email,
                'academic_session_name'       => '2026-2027',
                'academic_session_start_date' => '2026-04-01',
                'academic_session_end_date'   => '2027-03-31',
                'branch_access_enabled'       => '0', // disabled
            ]);

        $this->school1->refresh();
        $this->assertFalse($this->school1->branch_access_enabled);
        $this->assertNull($this->school1->branch_group_id);

        // Accessible branches contains only self
        $branches = $this->school1->getAccessibleBranches();
        $this->assertCount(1, $branches);
        $this->assertEquals($this->school1->id, $branches->first()->id);

        // Attempting to switch is blocked
        $this->actingAs($this->admin1);
        $response = $this->post(route('school.switch-branch'), [
            'school_id' => $this->school2->id,
        ]);

        $this->assertTrue(
            $response->status() === 403 || $response->isRedirect(route('login'))
        );
    }
}
