<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\Department;
use App\Models\Designation;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Staff;
use App\Models\Student;
use App\Models\User;
use App\Support\SearchHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ResetPasswordSearchTest extends TestCase
{
    use RefreshDatabase;

    protected School $school;
    protected User $adminUser;
    protected Student $studentGaurav;
    protected Staff $teacherStaff;
    protected Staff $regularStaff;
    protected User $parentUser;

    protected function setUp(): void
    {
        parent::setUp();
        app()->forgetInstance('currentSchool');

        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'parent', 'guard_name' => 'web']);

        $this->school = School::create([
            'name' => 'Yash International School',
            'code' => 'YIS',
            'status' => 'active',
        ]);

        $session = AcademicSession::create([
            'school_id' => $this->school->id,
            'name' => '2026-2027',
            'start_date' => '2026-04-01',
            'end_date' => '2027-03-31',
            'is_current' => true,
        ]);

        $schoolClass = SchoolClass::create([
            'school_id' => $this->school->id,
            'name' => 'LKG',
            'numeric_name' => 0,
        ]);

        $section = Section::create([
            'school_id' => $this->school->id,
            'class_id' => $schoolClass->id,
            'name' => 'C',
        ]);

        $department = Department::create([
            'school_id' => $this->school->id,
            'name' => 'Teaching',
        ]);

        $teacherDesignation = Designation::create([
            'school_id' => $this->school->id,
            'department_id' => $department->id,
            'name' => 'Teacher',
        ]);

        $staffDesignation = Designation::create([
            'school_id' => $this->school->id,
            'department_id' => $department->id,
            'name' => 'Accountant',
        ]);

        // 1. Admin User
        $this->adminUser = User::create([
            'school_id' => $this->school->id,
            'name' => 'Principal Admin',
            'email' => 'admin@yis.com',
            'phone' => '9998887770',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);
        $this->adminUser->assignRole('admin');

        // 2. Student Gaurav Yadav (Initially unlinked without user_id)
        $this->studentGaurav = Student::create([
            'school_id' => $this->school->id,
            'admission_number' => 'ADM2026007',
            'first_name' => 'Gaurav',
            'last_name' => 'Yadav',
            'father_name' => 'Ishaan Yadav',
            'mother_name' => 'Pooja Yadav',
            'guardian_name' => 'Ishaan Yadav',
            'guardian_phone' => '9602563421',
            'guardian_email' => 'ishaan.yadav@parent.yis.com',
            'guardian_relationship' => 'father',
            'date_of_birth' => '2020-01-01',
            'gender' => 'male',
            'address' => 'Sample Address',
            'city' => 'Sample City',
            'state' => 'Sample State',
            'pincode' => '123456',
            'class_id' => $schoolClass->id,
            'section_id' => $section->id,
            'academic_session_id' => $session->id,
            'admission_date' => '2026-04-01',
            'is_active' => true,
        ]);

        // 3. Teacher Staff
        $this->teacherStaff = Staff::create([
            'school_id' => $this->school->id,
            'employee_id' => 'EMP202601',
            'first_name' => 'Gaurav',
            'last_name' => 'Srivastava',
            'email' => 'gaurav.srivastava@yis.com',
            'phone' => '9876543210',
            'department_id' => $department->id,
            'designation_id' => $teacherDesignation->id,
            'joining_date' => '2025-01-01',
            'is_active' => true,
        ]);

        // 4. Regular Staff
        $this->regularStaff = Staff::create([
            'school_id' => $this->school->id,
            'employee_id' => 'EMP202602',
            'first_name' => 'Suresh',
            'last_name' => 'Kumar',
            'email' => 'suresh.kumar@yis.com',
            'phone' => '9876543211',
            'department_id' => $department->id,
            'designation_id' => $staffDesignation->id,
            'joining_date' => '2025-01-01',
            'is_active' => true,
        ]);

        // 5. Parent User
        $this->parentUser = User::create([
            'school_id' => $this->school->id,
            'name' => 'Ishaan Yadav',
            'email' => 'ishaan.yadav@parent.yis.com',
            'phone' => '9602563421',
            'password' => Hash::make('password123'),
            'role' => 'parent',
            'is_active' => true,
        ]);
        $this->parentUser->assignRole('parent');

        // Link student to parent
        $this->studentGaurav->update(['father_email' => $this->parentUser->email]);

        // Sync school accounts to provision any missing user links
        SearchHelper::syncSchoolUserAccounts($this->school->id);

        session(['school_code' => $this->school->code]);
        $this->withHeaders(['X-School-Code' => $this->school->code]);
    }


    #[Test]
    public function test_1_student_first_name_search_returns_gaurav_yadav()
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('school.settings.reset-password', ['search' => 'Gaurav']));

        $response->assertStatus(200);
        $response->assertSee('Gaurav Yadav');
    }

    #[Test]
    public function test_2_full_student_name_search_returns_gaurav_yadav()
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('school.settings.reset-password', ['search' => 'Gaurav Yadav']));

        $response->assertStatus(200);
        $response->assertSee('Gaurav Yadav');
        $response->assertDontSee('Ishaan Yadav');
    }

    #[Test]
    public function test_3_lowercase_search_returns_gaurav_yadav()
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('school.settings.reset-password', ['search' => 'gaurav yadav']));

        $response->assertStatus(200);
        $response->assertSee('Gaurav Yadav');
        $response->assertDontSee('Ishaan Yadav');
    }

    #[Test]
    public function test_4_uppercase_search_returns_gaurav_yadav()
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('school.settings.reset-password', ['search' => 'GAURAV YADAV']));

        $response->assertStatus(200);
        $response->assertSee('Gaurav Yadav');
        $response->assertDontSee('Ishaan Yadav');
    }

    #[Test]
    public function test_5_mixed_case_search_returns_gaurav_yadav()
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('school.settings.reset-password', ['search' => 'GaUrAv YaDaV']));

        $response->assertStatus(200);
        $response->assertSee('Gaurav Yadav');
        $response->assertDontSee('Ishaan Yadav');
    }

    #[Test]
    public function test_6_last_name_search_returns_gaurav_yadav()
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('school.settings.reset-password', ['search' => 'Yadav']));

        $response->assertStatus(200);
        $response->assertSee('Gaurav Yadav');
        $response->assertSee('Ishaan Yadav');

        // Check that users collection in view has correct roles and names without mixing
        $viewUsers = $response->viewData('users');
        $studentUser = $viewUsers->firstWhere('name', 'Gaurav Yadav');
        $parentUser = $viewUsers->firstWhere('name', 'Ishaan Yadav');

        $this->assertNotNull($studentUser);
        $this->assertNotNull($parentUser);
        $this->assertEquals('student', strtolower($studentUser->roles->first()?->name ?? $studentUser->role));
        $this->assertEquals('parent', strtolower($parentUser->roles->first()?->name ?? $parentUser->role));
    }

    #[Test]
    public function test_7_partial_name_search_returns_gaurav_yadav()
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('school.settings.reset-password', ['search' => 'Gaur']));

        $response->assertStatus(200);
        $response->assertSee('Gaurav Yadav');
    }

    #[Test]
    public function test_8_whitespace_search_returns_gaurav_yadav()
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('school.settings.reset-password', ['search' => '  Gaurav Yadav  ']));

        $response->assertStatus(200);
        $response->assertSee('Gaurav Yadav');
        $response->assertDontSee('Ishaan Yadav');
    }

    #[Test]
    public function test_9_teacher_search_returns_teacher()
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('school.settings.reset-password', ['search' => 'Gaurav Srivastava']));

        $response->assertStatus(200);
        $response->assertSee('Gaurav Srivastava');
    }

    #[Test]
    public function test_10_staff_search_returns_staff()
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('school.settings.reset-password', ['search' => 'Suresh Kumar']));

        $response->assertStatus(200);
        $response->assertSee('Suresh Kumar');
    }

    #[Test]
    public function test_11_parent_search_returns_parent()
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('school.settings.reset-password', ['search' => 'Ishaan Yadav']));

        $response->assertStatus(200);
        $response->assertSee('Ishaan Yadav');
        $response->assertDontSee('Gaurav Yadav');
    }

    #[Test]
    public function test_12_admin_search_returns_admin()
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('school.settings.reset-password', ['search' => 'Principal Admin']));

        $response->assertStatus(200);
        $response->assertSee('Principal Admin');
    }

    #[Test]
    public function test_13_email_search_returns_user()
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('school.settings.reset-password', ['search' => 'gaurav.srivastava@yis.com']));

        $response->assertStatus(200);
        $response->assertSee('Gaurav Srivastava');
    }

    #[Test]
    public function test_14_mobile_search_returns_user()
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('school.settings.reset-password', ['search' => '9602563421']));

        $response->assertStatus(200);
        $response->assertSee('Ishaan Yadav');
    }

    #[Test]
    public function test_15_admission_number_search_returns_student()
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('school.settings.reset-password', ['search' => 'ADM2026007']));

        $response->assertStatus(200);
        $response->assertSee('Gaurav Yadav');
    }

    #[Test]
    public function test_16_no_results_search()
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('school.settings.reset-password', ['search' => 'zzzzzzzzzz']));

        $response->assertStatus(200);
        $response->assertSee('No users matched your query.');
    }

    #[Test]
    public function test_17_reset_password_post_action_updates_password()
    {
        $studentUser = User::where('school_id', $this->school->id)
            ->where('name', 'Gaurav Yadav')
            ->first();

        $this->assertNotNull($studentUser);

        $response = $this->actingAs($this->adminUser)
            ->post(route('school.settings.reset-password.post'), [
                'user_id'  => $studentUser->id,
                'password' => 'NewSecurePassword123!',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $studentUser->refresh();
        $this->assertTrue(Hash::check('NewSecurePassword123!', $studentUser->password));
    }

    #[Test]
    public function test_21_reset_password_default_assigns_welcome123()
    {
        $studentUser = User::where('school_id', $this->school->id)
            ->where('name', 'Gaurav Yadav')
            ->first();

        $this->assertNotNull($studentUser);

        $response = $this->actingAs($this->adminUser)
            ->post(route('school.settings.reset-password.post'), [
                'user_id' => $studentUser->id,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $studentUser->refresh();
        $this->assertTrue(Hash::check('Welcome@123', $studentUser->password));
        $this->assertTrue($studentUser->must_change_password);
    }

    #[Test]
    public function test_18_parent_search_returns_father_display_role()
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('school.settings.reset-password', ['search' => 'Ishaan Yadav']));

        $response->assertStatus(200);
        $response->assertSee('Ishaan Yadav');
        $response->assertSee('Father');

        $viewUsers = $response->viewData('users');
        $parentUser = $viewUsers->firstWhere('name', 'Ishaan Yadav');

        $this->assertNotNull($parentUser);
        $this->assertEquals('Father', $parentUser->display_role);
    }

    #[Test]
    public function test_19_mother_search_returns_mother_display_role()
    {
        $motherUser = User::create([
            'school_id' => $this->school->id,
            'name' => 'Pooja Yadav',
            'email' => 'pooja.yadav@parent.yis.com',
            'phone' => '9602563499',
            'password' => Hash::make('password123'),
            'role' => 'parent',
            'is_active' => true,
        ]);
        $motherUser->assignRole('parent');

        $this->studentGaurav->update(['mother_email' => 'pooja.yadav@parent.yis.com']);

        $response = $this->actingAs($this->adminUser)
            ->get(route('school.settings.reset-password', ['search' => 'Pooja Yadav']));

        $response->assertStatus(200);
        $response->assertSee('Pooja Yadav');
        $response->assertSee('Mother');

        $viewUsers = $response->viewData('users');
        $foundMother = $viewUsers->firstWhere('name', 'Pooja Yadav');

        $this->assertNotNull($foundMother);
        $this->assertEquals('Mother', $foundMother->display_role);
    }

    #[Test]
    public function test_20_reloading_reset_password_page_preserves_parent_role()
    {
        for ($i = 0; $i < 3; $i++) {
            $response = $this->actingAs($this->adminUser)
                ->get(route('school.settings.reset-password', ['search' => 'Ishaan Yadav']));

            $response->assertStatus(200);
            $response->assertSee('Ishaan Yadav');
            $response->assertSee('Father');

            $viewUsers = $response->viewData('users');
            $foundParent = $viewUsers->firstWhere('name', 'Ishaan Yadav');
            $this->assertNotNull($foundParent);
            $this->assertEquals('Father', $foundParent->display_role);

            $this->parentUser->refresh();
            $this->assertNotEquals('student', $this->parentUser->role);
            $this->assertTrue($this->parentUser->hasRole('parent'));
            $this->assertFalse($this->parentUser->hasRole('student'));
        }
    }
}

