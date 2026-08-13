<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Staff;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ForcePasswordChangeTest extends TestCase
{
    use RefreshDatabase;

    protected School $school;
    protected User $adminUser;
    protected User $studentUser;
    protected User $teacherUser;
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
            'name' => 'Grade 10',
            'numeric_name' => 10,
        ]);

        $section = Section::create([
            'school_id' => $this->school->id,
            'class_id' => $schoolClass->id,
            'name' => 'A',
        ]);

        // Admin User
        $this->adminUser = User::create([
            'school_id' => $this->school->id,
            'name' => 'Principal Admin',
            'email' => 'admin@yis.com',
            'phone' => '9998887770',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->adminUser->assignRole('admin');

        // Student User
        $this->studentUser = User::create([
            'school_id' => $this->school->id,
            'name' => 'Rahul Sharma',
            'email' => 'rahul.sharma@student.yis.com',
            'phone' => '9876543210',
            'password' => Hash::make('Student@2026!'),
            'role' => 'student',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->studentUser->assignRole('student');

        Student::create([
            'school_id' => $this->school->id,
            'user_id' => $this->studentUser->id,
            'admission_number' => 'ADM2026001',
            'admission_date' => '2026-04-01',
            'first_name' => 'Rahul',
            'last_name' => 'Sharma',
            'father_name' => 'Suresh Sharma',
            'mother_name' => 'Anita Sharma',
            'guardian_name' => 'Suresh Sharma',
            'guardian_phone' => '9876543212',
            'guardian_email' => 'suresh.sharma@parent.yis.com',
            'guardian_relationship' => 'father',
            'email' => 'rahul.sharma@student.yis.com',
            'phone' => '9876543210',
            'date_of_birth' => '2010-01-01',
            'gender' => 'male',
            'address' => 'Sample Address',
            'city' => 'Sample City',
            'state' => 'Sample State',
            'pincode' => '302001',
            'class_id' => $schoolClass->id,
            'section_id' => $section->id,
            'academic_session_id' => $session->id,
            'is_active' => true,
        ]);

        // Teacher User
        $this->teacherUser = User::create([
            'school_id' => $this->school->id,
            'name' => 'Vikram Teacher',
            'email' => 'vikram@yis.com',
            'phone' => '9876543211',
            'password' => Hash::make('TeacherPass123!'),
            'role' => 'teacher',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->teacherUser->assignRole('teacher');

        // Parent User
        $this->parentUser = User::create([
            'school_id' => $this->school->id,
            'name' => 'Suresh Sharma',
            'email' => 'suresh.sharma@parent.yis.com',
            'phone' => '9876543212',
            'password' => Hash::make('ParentPass123!'),
            'role' => 'parent',
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $this->parentUser->assignRole('parent');
    }

    #[Test]
    public function test_1_admin_reset_password_assigns_welcome123_and_flags_must_change_password()
    {
        $response = $this->actingAs($this->adminUser)
            ->withSession(['school_code' => $this->school->code])
            ->post(route('school.settings.reset-password.post'), [
                'user_id' => $this->studentUser->id,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->studentUser->refresh();
        $this->assertTrue(Hash::check('Welcome@123', $this->studentUser->password));
        $this->assertTrue($this->studentUser->must_change_password);
        $this->assertNotNull($this->studentUser->last_password_reset_at);
    }

    #[Test]
    public function test_2_user_with_must_change_password_is_redirected_to_change_password_page()
    {
        $this->studentUser->update([
            'password' => Hash::make('Welcome@123'),
            'must_change_password' => true,
        ]);

        $response = $this->actingAs($this->studentUser)
            ->get('/student/dashboard');

        $response->assertRedirect(route('password.change'));
    }

    #[Test]
    public function test_3_user_cannot_bypass_change_password_page()
    {
        $this->studentUser->update([
            'password' => Hash::make('Welcome@123'),
            'must_change_password' => true,
        ]);

        $restrictedRoutes = [
            '/student/dashboard',
            '/school/settings/reset-password',
        ];

        foreach ($restrictedRoutes as $route) {
            $response = $this->actingAs($this->studentUser)->get($route);
            $response->assertRedirect(route('password.change'));
        }
    }

    #[Test]
    public function test_4_weak_password_submission_fails_validation()
    {
        $this->studentUser->update([
            'password' => Hash::make('Welcome@123'),
            'must_change_password' => true,
        ]);

        // Weak password (missing uppercase & special character)
        $response = $this->actingAs($this->studentUser)
            ->post(route('password.change.update'), [
                'current_password' => 'Welcome@123',
                'password' => 'weakpass1',
                'password_confirmation' => 'weakpass1',
            ]);

        $response->assertSessionHasErrors('password');
        $this->studentUser->refresh();
        $this->assertTrue($this->studentUser->must_change_password);
    }

    #[Test]
    public function test_5_strong_password_updates_user_and_clears_must_change_password()
    {
        $this->studentUser->update([
            'password' => Hash::make('Welcome@123'),
            'must_change_password' => true,
        ]);

        $response = $this->actingAs($this->studentUser)
            ->post(route('password.change.update'), [
                'current_password' => 'Welcome@123',
                'password' => 'SecurePass2026!',
                'password_confirmation' => 'SecurePass2026!',
            ]);

        $response->assertRedirect('/student/dashboard');
        $response->assertSessionHas('success');

        $this->studentUser->refresh();
        $this->assertTrue(Hash::check('SecurePass2026!', $this->studentUser->password));
        $this->assertFalse($this->studentUser->must_change_password);
    }

    #[Test]
    public function test_6_user_can_access_dashboard_after_updating_password()
    {
        $this->studentUser->update([
            'password' => Hash::make('SecurePass2026!'),
            'must_change_password' => false,
        ]);

        $response = $this->actingAs($this->studentUser)
            ->get('/student/dashboard');

        $response->assertStatus(200);
    }

    #[Test]
    public function test_7_existing_users_are_not_interrupted()
    {
        $response = $this->actingAs($this->teacherUser)
            ->withSession(['school_code' => $this->school->code])
            ->get('/teacher/dashboard');

        $response->assertStatus(200);
    }
}
