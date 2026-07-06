<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\Student;
use App\Models\User;
use App\Models\Notice;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\AcademicSession;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MultiTenancyIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed the database
        $this->seed(DatabaseSeeder::class);
    }

    /**
     * Test that one school cannot access another school's data at the Eloquent query level.
     */
    public function test_eloquent_query_scoping_prevents_cross_school_access(): void
    {
        // 1. Resolve School A & Admin
        $schoolAdminA = User::where('email', 'admin@yis.com')->firstOrFail();
        $this->actingAs($schoolAdminA);

        // 2. Create another school (School B) and a notice for it
        $schoolB = School::create([
            'name' => 'School B',
            'code' => 'SCHB2026',
            'status' => 'active'
        ]);

        $noticeB = Notice::create([
            'school_id' => $schoolB->id,
            'title' => 'School B Private Notice',
            'content' => 'Top secret content for School B.',
            'target_audience' => 'all'
        ]);

        // 3. Verify that School A admin querying Notice cannot see School B's notice
        $foundNotice = Notice::find($noticeB->id);
        $this->assertNull($foundNotice, "School A admin should not be able to find School B's notice");

        $allNotices = Notice::all();
        $this->assertFalse($allNotices->contains('id', $noticeB->id), "School B's notice should not appear in School A admin's Notice listing");
    }

    /**
     * Test that parent portal routes enforce strict parent-student boundaries.
     */
    public function test_parent_cannot_view_unrelated_student_attendance(): void
    {
        // 1. Get a parent user from School A
        $parent = User::where('email', 'parent@yis.com')->firstOrFail();
        
        // 2. Create a second unrelated student in the same school (School A)
        $schoolA = School::where('code', 'YIS2024')->firstOrFail();
        $class = SchoolClass::where('school_id', $schoolA->id)->firstOrFail();
        $section = Section::where('class_id', $class->id)->firstOrFail();
        $session = AcademicSession::where('school_id', $schoolA->id)->firstOrFail();

        $unrelatedStudent = Student::create([
            'school_id' => $schoolA->id,
            'admission_number' => 'YIS/2026/09999',
            'first_name' => 'Unrelated',
            'last_name' => 'Child',
            'date_of_birth' => '2013-01-01',
            'gender' => 'male',
            'guardian_name' => 'Stranger Danger',
            'guardian_phone' => '1122334455',
            'guardian_email' => 'stranger@example.com',
            'guardian_relationship' => 'father',
            'address' => '456 Stranger Lane',
            'city' => 'Education City',
            'state' => 'State',
            'pincode' => '123456',
            'class_id' => $class->id,
            'section_id' => $section->id,
            'academic_session_id' => $session->id,
            'admission_date' => '2026-06-01',
            'is_active' => true,
        ]);

        // 3. Attempt to access the unrelated student's attendance page as the logged-in parent
        $response = $this->actingAs($parent)
            ->get("/parent/attendance?student_id={$unrelatedStudent->id}");

        // 4. Assert that access is forbidden and redirected to login with access denied error
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['email']);
    }

    /**
     * Test that a parent with no matching student gets a safe response instead of the first student fallback leak.
     */
    public function test_parent_with_no_child_does_not_leak_first_student(): void
    {
        $schoolA = School::where('code', 'YIS2024')->firstOrFail();
        
        // 1. Create a parent user with NO associated student
        $lonelyParent = User::create([
            'name' => 'Lonely Parent',
            'email' => 'lonely@parent.com',
            'password' => Hash::make('SecretPass123!'),
            'phone' => '9898989898',
            'school_id' => $schoolA->id,
            'is_active' => true
        ]);
        $lonelyParent->assignRole('parent');

        // 2. Visit dashboard
        $response = $this->actingAs($lonelyParent)->get('/parent/dashboard');
        
        // 3. Verify it does not crash (assert 200)
        $response->assertStatus(200);

        // 4. Verify that the dashboard view does NOT render the first student's name
        $firstStudent = Student::where('school_id', $schoolA->id)->first();
        if ($firstStudent) {
            $response->assertDontSee($firstStudent->full_name);
            $response->assertDontSee($firstStudent->admission_number);
        }
    }
}
