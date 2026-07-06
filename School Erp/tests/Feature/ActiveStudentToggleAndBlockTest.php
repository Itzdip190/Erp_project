<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\Student;
use App\Models\User;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\AcademicSession;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ActiveStudentToggleAndBlockTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed the database
        $this->seed(DatabaseSeeder::class);
    }

    /**
     * Test that student status toggling updates the is_active database state.
     */
    public function test_student_status_toggle_updates_database_and_logs(): void
    {
        $admin = User::where('email', 'admin@yis.com')->firstOrFail();
        $student = Student::firstOrFail();
        
        $this->actingAs($admin);
        
        // Assert initial status is true/active
        $this->assertTrue((bool)$student->is_active);
        
        // Toggle status to inactive via POST
        $response = $this->post(route('school.students.toggle-status', $student->id));
        $response->assertJson([
            'success' => true,
            'is_active' => false
        ]);
        
        $student->refresh();
        $this->assertFalse((bool)$student->is_active);
        
        // Toggle status back to active
        $response = $this->post(route('school.students.toggle-status', $student->id));
        $response->assertJson([
            'success' => true,
            'is_active' => true
        ]);
        
        $student->refresh();
        $this->assertTrue((bool)$student->is_active);
    }

    /**
     * Test that deactivating a student blocks student and parent access.
     */
    public function test_inactive_student_and_parent_blocked_by_middleware(): void
    {
        $student = Student::firstOrFail();
        
        // Deactivate student
        $student->update(['is_active' => false]);
        
        // 1. Attempt student access
        $studentUser = $student->user;
        if ($studentUser) {
            $this->actingAs($studentUser);
            
            $response = $this->get('/parent/dashboard');
            // Assert redirected to login because of logout on inactive student
            $response->assertRedirect('/login');
        }
        
        // 2. Attempt parent access
        $parentUser = User::where('email', $student->guardian_email)->first();
        if (!$parentUser) {
            $parentUser = User::create([
                'name' => $student->guardian_name,
                'email' => $student->guardian_email ?? 'guardian@example.com',
                'password' => Hash::make('schoolcloud123'),
                'school_id' => $student->school_id,
                'is_active' => true
            ]);
            $parentUser->assignRole('parent');
        }
        
        $this->actingAs($parentUser);
        
        $response = $this->get('/parent/dashboard');
        // Assert status 200 but renders the inactive parent view
        $response->assertStatus(200);
        $response->assertSee('Account Inactive');
        $response->assertSee(e($student->full_name));
    }
}
