<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use App\Models\Scopes\SchoolScope;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StudentMultiTenantAuthTest extends TestCase
{
    use RefreshDatabase;

    protected School $schoolA;
    protected School $schoolB;
    protected SchoolClass $classA;
    protected Section $sectionA;
    protected AcademicSession $sessionA;
    protected SchoolClass $classB;
    protected Section $sectionB;
    protected AcademicSession $sessionB;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::firstOrCreate(['name' => 'student']);
        Role::firstOrCreate(['name' => 'parent']);

        // Create School A (Yash International School)
        $this->schoolA = School::create([
            'name'          => 'Yash International School',
            'code'          => 'YIS',
            'custom_domain' => 'yash.educorp.com',
            'status'        => 'active',
        ]);

        // Create School B (Dipto School)
        $this->schoolB = School::create([
            'name'          => 'Dipto School',
            'code'          => 'DIPTO',
            'custom_domain' => 'dipto.educorp.com',
            'status'        => 'active',
        ]);

        // School A dependencies
        $this->classA = SchoolClass::withoutGlobalScope(SchoolScope::class)->create([
            'school_id'    => $this->schoolA->id,
            'name'         => 'Nursery',
            'numeric_name' => 1,
        ]);
        $this->sectionA = Section::withoutGlobalScope(SchoolScope::class)->create([
            'school_id' => $this->schoolA->id,
            'class_id'  => $this->classA->id,
            'name'      => 'A',
        ]);
        $this->sessionA = AcademicSession::withoutGlobalScope(SchoolScope::class)->create([
            'school_id'  => $this->schoolA->id,
            'name'       => '2026-2027',
            'start_date' => '2026-04-01',
            'end_date'   => '2027-03-31',
            'is_current' => true,
        ]);

        // School B dependencies
        $this->classB = SchoolClass::withoutGlobalScope(SchoolScope::class)->create([
            'school_id'    => $this->schoolB->id,
            'name'         => 'Nursery',
            'numeric_name' => 1,
        ]);
        $this->sectionB = Section::withoutGlobalScope(SchoolScope::class)->create([
            'school_id' => $this->schoolB->id,
            'class_id'  => $this->classB->id,
            'name'      => 'A',
        ]);
        $this->sessionB = AcademicSession::withoutGlobalScope(SchoolScope::class)->create([
            'school_id'  => $this->schoolB->id,
            'name'       => '2026-2027',
            'start_date' => '2026-04-01',
            'end_date'   => '2027-03-31',
            'is_current' => true,
        ]);
    }

    protected function createStudent(array $attributes): Student
    {
        $schoolId = $attributes['school_id'] ?? $this->schoolA->id;
        $classId   = $schoolId == $this->schoolA->id ? $this->classA->id : $this->classB->id;
        $sectionId = $schoolId == $this->schoolA->id ? $this->sectionA->id : $this->sectionB->id;
        $sessionId = $schoolId == $this->schoolA->id ? $this->sessionA->id : $this->sessionB->id;

        return Student::withoutGlobalScope(SchoolScope::class)->create(array_merge([
            'class_id'              => $classId,
            'section_id'            => $sectionId,
            'academic_session_id'   => $sessionId,
            'gender'                => 'Male',
            'date_of_birth'         => '2022-11-27',
            'admission_date'        => '2026-05-13',
            'guardian_name'         => 'Pranav Pall',
            'guardian_relationship' => 'Father',
            'guardian_phone'        => '9876543210',
            'address'               => '123 Test Street',
            'city'                  => 'Test City',
            'state'                 => 'Test State',
            'pincode'               => '123456',
            'is_active'             => true,
        ], $attributes));
    }

    /**
     * Test 1: Login using School A admission number opens only School A student's profile.
     */
    public function test_school_a_admission_number_login_opens_school_a_profile()
    {
        $studentA = $this->createStudent([
            'school_id'            => $this->schoolA->id,
            'admission_number'     => 'YAS/2026/00504',
            'first_name'           => 'Jeremiah',
            'last_name'            => 'Pall',
            'father_name'          => 'Pranav Pall',
            'national_id_card_no'  => '589831948939',
        ]);

        $response = $this->withSession(['school_code' => 'YIS'])
            ->post('/login', [
                'email'    => 'YAS/2026/00504',
                'password' => 'Student@2026!',
            ]);

        $response->assertRedirect('/parent/dashboard');
        $this->assertAuthenticated();

        $authUser = auth()->user();
        $this->assertEquals($this->schoolA->id, $authUser->school_id);

        $loadedStudent = Student::where('school_id', $authUser->school_id)
            ->where(function ($q) use ($authUser) {
                $q->where('guardian_email', $authUser->email)
                  ->orWhere('user_id', $authUser->id);
            })
            ->first();

        $this->assertNotNull($loadedStudent);
        $this->assertEquals($studentA->id, $loadedStudent->id);
        $this->assertEquals('YAS/2026/00504', $loadedStudent->admission_number);
        $this->assertEquals($this->schoolA->id, $loadedStudent->school_id);
    }

    /**
     * Test 2: Login using School B admission number opens only School B student's profile.
     */
    public function test_school_b_admission_number_login_opens_school_b_profile()
    {
        $studentB = $this->createStudent([
            'school_id'            => $this->schoolB->id,
            'admission_number'     => 'DIP/2026/00004',
            'first_name'           => 'Jeremiah',
            'last_name'            => 'Pall',
            'father_name'          => 'Pranav Pall',
            'national_id_card_no'  => '589831948939',
        ]);

        $response = $this->withSession(['school_code' => 'DIPTO'])
            ->post('/login', [
                'email'    => 'DIP/2026/00004',
                'password' => 'Student@2026!',
            ]);

        $response->assertRedirect('/parent/dashboard');
        $this->assertAuthenticated();

        $authUser = auth()->user();
        $this->assertEquals($this->schoolB->id, $authUser->school_id);

        $loadedStudent = Student::where('school_id', $authUser->school_id)
            ->where(function ($q) use ($authUser) {
                $q->where('guardian_email', $authUser->email)
                  ->orWhere('user_id', $authUser->id);
            })
            ->first();

        $this->assertNotNull($loadedStudent);
        $this->assertEquals($studentB->id, $loadedStudent->id);
        $this->assertEquals('DIP/2026/00004', $loadedStudent->admission_number);
        $this->assertEquals($this->schoolB->id, $loadedStudent->school_id);
    }

    /**
     * Test 3: Students with identical personal details across two schools remain isolated.
     */
    public function test_students_with_identical_details_in_different_schools_remain_isolated()
    {
        $studentA = $this->createStudent([
            'school_id'            => $this->schoolA->id,
            'admission_number'     => 'YAS/2026/00504',
            'first_name'           => 'Jeremiah',
            'last_name'            => 'Pall',
            'father_name'          => 'Pranav Pall',
            'national_id_card_no'  => '589831948939',
        ]);

        $studentB = $this->createStudent([
            'school_id'            => $this->schoolB->id,
            'admission_number'     => 'DIP/2026/00004',
            'first_name'           => 'Jeremiah',
            'last_name'            => 'Pall',
            'father_name'          => 'Pranav Pall',
            'national_id_card_no'  => '589831948939',
        ]);

        // Login to School A
        $this->withSession(['school_code' => 'YIS'])
            ->post('/login', [
                'email'    => 'YAS/2026/00504',
                'password' => 'Student@2026!',
            ]);

        $authUserA = auth()->user();
        $this->assertEquals($this->schoolA->id, $authUserA->school_id);
        $this->assertNotEquals($studentB->id, $studentA->id);

        auth()->logout();

        // Login to School B
        $this->withSession(['school_code' => 'DIPTO'])
            ->post('/login', [
                'email'    => 'DIP/2026/00004',
                'password' => 'Student@2026!',
            ]);

        $authUserB = auth()->user();
        $this->assertEquals($this->schoolB->id, $authUserB->school_id);
        $this->assertNotEquals($authUserA->id, $authUserB->id);
    }

    /**
     * Test 4: Switching between schools does not cross-contaminate authentication or student access.
     */
    public function test_switching_school_context_prevents_cross_tenant_access()
    {
        $studentA = $this->createStudent([
            'school_id'          => $this->schoolA->id,
            'admission_number'   => 'YAS/2026/00504',
            'first_name'         => 'Jeremiah',
            'last_name'          => 'Pall',
        ]);

        $studentB = $this->createStudent([
            'school_id'          => $this->schoolB->id,
            'admission_number'   => 'DIP/2026/00004',
            'first_name'         => 'Jeremiah',
            'last_name'          => 'Pall',
        ]);

        // Attempt login on School A domain with School B admission number
        $response = $this->withSession(['school_code' => 'YIS'])
            ->withHeaders(['Host' => 'yash.educorp.com'])
            ->post('/login', [
                'email'    => 'DIP/2026/00004',
                'password' => 'Student@2026!',
            ]);

        // Should fail because DIP/2026/00004 does not exist in School A (yash.educorp.com)
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * Test 5: Mobile / Sanctum API authentication remains strictly tenant-isolated.
     */
    public function test_mobile_api_login_is_tenant_isolated()
    {
        $studentUserA = User::withoutGlobalScope(SchoolScope::class)->create([
            'school_id' => $this->schoolA->id,
            'name'      => 'Jeremiah Pall',
            'email'     => 'jeremiah.pall.yas202600504@student.yis.com',
            'password'  => Hash::make('Student@2026!'),
            'is_active' => true,
        ]);
        $studentUserA->assignRole('student');

        $studentUserB = User::withoutGlobalScope(SchoolScope::class)->create([
            'school_id' => $this->schoolB->id,
            'name'      => 'Jeremiah Pall',
            'email'     => 'jeremiah.pall.dip202600004@student.dipto.com',
            'password'  => Hash::make('Student@2026!'),
            'is_active' => true,
        ]);
        $studentUserB->assignRole('student');

        // Mobile API login with School A code
        $responseA = $this->postJson('/api/v1/parent/login', [
            'school_code' => 'YIS',
            'email'       => 'jeremiah.pall.yas202600504@student.yis.com',
            'password'    => 'Student@2026!',
            'device_name' => 'android_phone',
        ]);

        $responseA->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user.school_id', $this->schoolA->id);

        // Attempting to log into School B with School A's user email must fail
        $responseCross = $this->postJson('/api/v1/parent/login', [
            'school_code' => 'DIPTO',
            'email'       => 'jeremiah.pall.yas202600504@student.yis.com',
            'password'    => 'Student@2026!',
            'device_name' => 'android_phone',
        ]);

        $responseCross->assertStatus(401);
    }
}
