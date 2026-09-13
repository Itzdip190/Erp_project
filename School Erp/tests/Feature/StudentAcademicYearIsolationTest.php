<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentDeletionRequest;
use App\Models\StudentSession;
use App\Models\User;
use App\Models\Scopes\SchoolScope;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StudentAcademicYearIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected School $school;
    protected User $admin;
    protected AcademicSession $session2024;
    protected AcademicSession $session2025;
    protected AcademicSession $session2026;
    protected SchoolClass $class1;
    protected SchoolClass $classUKG;
    protected Section $sectionA;
    protected Section $sectionB;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'superadmin']);
        Role::firstOrCreate(['name' => 'school_admin']);
        Role::firstOrCreate(['name' => 'student']);
        Role::firstOrCreate(['name' => 'parent']);

        $this->school = School::create([
            'name'          => 'Greenwood High School',
            'code'          => 'GHS',
            'custom_domain' => 'greenwood.erp.test',
            'status'        => 'active',
        ]);

        $this->admin = User::factory()->create([
            'school_id' => $this->school->id,
            'role'      => 'school_admin',
            'email'     => 'admin@greenwood.erp.test',
        ]);
        $this->admin->assignRole('school_admin');

        $this->session2024 = AcademicSession::create([
            'school_id'  => $this->school->id,
            'name'       => '2024-2025',
            'start_date' => '2024-04-01',
            'end_date'   => '2025-03-31',
            'is_current' => false,
        ]);

        $this->session2025 = AcademicSession::create([
            'school_id'  => $this->school->id,
            'name'       => '2025-2026',
            'start_date' => '2025-04-01',
            'end_date'   => '2026-03-31',
            'is_current' => false,
        ]);

        $this->session2026 = AcademicSession::create([
            'school_id'  => $this->school->id,
            'name'       => '2026-2027',
            'start_date' => '2026-04-01',
            'end_date'   => '2027-03-31',
            'is_current' => true,
        ]);

        $this->class1 = SchoolClass::withoutGlobalScope(SchoolScope::class)->create([
            'school_id'    => $this->school->id,
            'name'         => 'Class 1',
            'numeric_name' => 1,
        ]);

        $this->classUKG = SchoolClass::withoutGlobalScope(SchoolScope::class)->create([
            'school_id'    => $this->school->id,
            'name'         => 'UKG',
            'numeric_name' => 0,
        ]);

        $this->sectionA = Section::withoutGlobalScope(SchoolScope::class)->create([
            'school_id' => $this->school->id,
            'class_id'  => $this->class1->id,
            'name'      => 'A',
        ]);

        $this->sectionB = Section::withoutGlobalScope(SchoolScope::class)->create([
            'school_id' => $this->school->id,
            'class_id'  => $this->classUKG->id,
            'name'      => 'B',
        ]);
    }

    protected function createStudent(array $overrides = []): Student
    {
        static $seq = 100;
        $seq++;

        $defaults = [
            'school_id'             => $this->school->id,
            'admission_number'      => 'ADM' . $seq,
            'admission_sequence'    => $seq,
            'admission_year'        => 2024,
            'admission_date'        => '2024-04-01',
            'date_of_birth'         => '2018-05-15',
            'gender'                => 'male',
            'first_name'            => 'Aarav',
            'last_name'             => 'Sharma',
            'father_name'           => 'Rajesh Sharma',
            'phone'                 => '9876543210',
            'guardian_name'         => 'Rajesh Sharma',
            'guardian_phone'        => '9876543210',
            'guardian_relationship' => 'father',
            'address'               => '123 Test Street',
            'city'                  => 'Mumbai',
            'state'                 => 'Maharashtra',
            'pincode'               => '400001',
            'class_id'              => $this->class1->id,
            'section_id'            => $this->sectionA->id,
            'academic_session_id'   => $this->session2026->id,
            'roll_number'           => '01',
            'is_active'             => true,
        ];

        return Student::create(array_merge($defaults, $overrides));
    }

    public function test_student_directory_isolates_records_by_academic_year(): void
    {
        // Permanent Student Identity
        $student = $this->createStudent([
            'admission_number'    => 'ADM1001',
            'first_name'          => 'Aarav',
            'last_name'           => 'Sharma',
            'father_name'         => 'Rajesh Sharma',
            'phone'               => '9876543210',
            'academic_session_id' => $this->session2026->id,
            'class_id'            => $this->class1->id,
            'section_id'          => $this->sectionA->id,
            'roll_number'         => '01',
        ]);

        // Session 2024-2025: Class 1, Roll 05, Name: Aarav Kumar, Phone: 1111111111
        StudentSession::create([
            'school_id'           => $this->school->id,
            'student_id'          => $student->id,
            'academic_session_id' => $this->session2024->id,
            'class_id'            => $this->class1->id,
            'section_id'          => $this->sectionA->id,
            'roll_number'         => '05',
            'is_promoted'         => true,
            'session_data'        => [
                'first_name'   => 'Aarav',
                'last_name'    => 'Kumar',
                'father_name'  => 'Rajesh Kumar',
                'phone'        => '1111111111',
            ],
        ]);

        // Session 2025-2026: UKG, Roll 12, Name: Aarav Dev, Phone: 2222222222
        StudentSession::create([
            'school_id'           => $this->school->id,
            'student_id'          => $student->id,
            'academic_session_id' => $this->session2025->id,
            'class_id'            => $this->classUKG->id,
            'section_id'          => $this->sectionB->id,
            'roll_number'         => '12',
            'is_promoted'         => true,
            'session_data'        => [
                'first_name'   => 'Aarav',
                'last_name'    => 'Dev',
                'father_name'  => 'Rajesh Dev',
                'phone'        => '2222222222',
            ],
        ]);

        // Session 2026-2027: Class 1, Roll 01, Name: Aarav Sharma, Phone: 9876543210
        StudentSession::create([
            'school_id'           => $this->school->id,
            'student_id'          => $student->id,
            'academic_session_id' => $this->session2026->id,
            'class_id'            => $this->class1->id,
            'section_id'          => $this->sectionA->id,
            'roll_number'         => '01',
            'is_promoted'         => false,
            'session_data'        => [
                'first_name'   => 'Aarav',
                'last_name'    => 'Sharma',
                'father_name'  => 'Rajesh Sharma',
                'phone'        => '9876543210',
            ],
        ]);

        // 1. Check Directory for Session 2024-2025 in Class 1
        $response2024 = $this->actingAs($this->admin)
            ->get(route('school.students.index', [
                'academic_session_id' => $this->session2024->id,
                'class_id'            => $this->class1->id,
            ]));

        $response2024->assertOk();
        $response2024->assertSee('Aarav Kumar');
        $response2024->assertSee('Rajesh Kumar');
        $response2024->assertSee('1111111111');
        $response2024->assertSee('05');

        // 2. Check Directory for Session 2025-2026 in UKG
        $response2025 = $this->actingAs($this->admin)
            ->get(route('school.students.index', [
                'academic_session_id' => $this->session2025->id,
                'class_id'            => $this->classUKG->id,
            ]));

        $response2025->assertOk();
        $response2025->assertSee('Aarav Dev');
        $response2025->assertSee('Rajesh Dev');
        $response2025->assertSee('2222222222');
        $response2025->assertSee('12');

        // 3. Check Directory for Session 2026-2027 in Class 1
        $response2026 = $this->actingAs($this->admin)
            ->get(route('school.students.index', [
                'academic_session_id' => $this->session2026->id,
                'class_id'            => $this->class1->id,
            ]));

        $response2026->assertOk();
        $response2026->assertSee('Aarav Sharma');
        $response2026->assertSee('Rajesh Sharma');
        $response2026->assertSee('9876543210');
        $response2026->assertSee('01');
    }

    public function test_updating_student_in_one_academic_year_does_not_modify_other_academic_years(): void
    {
        $student = $this->createStudent([
            'admission_number'    => 'ADM2002',
            'first_name'          => 'Rohan',
            'last_name'           => 'Verma',
            'father_name'         => 'Suresh Verma',
            'phone'               => '9800000000',
            'academic_session_id' => $this->session2026->id,
            'class_id'            => $this->class1->id,
            'section_id'          => $this->sectionA->id,
            'roll_number'         => '10',
        ]);

        // 2025-2026 Session
        $session2025Rec = StudentSession::create([
            'school_id'           => $this->school->id,
            'student_id'          => $student->id,
            'academic_session_id' => $this->session2025->id,
            'class_id'            => $this->classUKG->id,
            'section_id'          => $this->sectionB->id,
            'roll_number'         => '08',
            'session_data'        => [
                'first_name'   => 'Rohan',
                'last_name'    => 'Verma',
                'father_name'  => 'Suresh Verma',
                'phone'        => '9800000000',
            ],
        ]);

        // 2026-2027 Session
        $session2026Rec = StudentSession::create([
            'school_id'           => $this->school->id,
            'student_id'          => $student->id,
            'academic_session_id' => $this->session2026->id,
            'class_id'            => $this->class1->id,
            'section_id'          => $this->sectionA->id,
            'roll_number'         => '10',
            'session_data'        => [
                'first_name'   => 'Rohan',
                'last_name'    => 'Verma',
                'father_name'  => 'Suresh Verma',
                'phone'        => '9800000000',
            ],
        ]);

        // Perform edit in Academic Year 2025-2026 ONLY
        $updatePayload = [
            'academic_session_id'     => $this->session2025->id,
            'admission_number'        => 'ADM2002',
            'admission_number_prefix' => 'ADM',
            'admission_number_seq'    => '2002',
            'first_name'              => 'Rohan',
            'last_name'               => 'Gupta', // Changed last name in 2025-2026
            'father_name'             => 'Suresh Gupta',
            'father_phone'            => '9111111111',
            'phone'                   => '9111111111', // Changed phone in 2025-2026
            'date_of_birth'           => '2018-05-15',
            'gender'                  => 'male',
            'guardian_name'           => 'Suresh Gupta',
            'guardian_phone'          => '9111111111',
            'guardian_relationship'   => 'father',
            'class_id'                => $this->classUKG->id,
            'section_id'              => $this->sectionB->id,
            'roll_number'             => '99', // Changed roll number in 2025-2026
            'admission_date'          => '2024-04-01',
            'address'                 => '123 New Street',
            'city'                    => 'Mumbai',
            'state'                   => 'Maharashtra',
            'pincode'                 => '400001',
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('school.students.update', $student->id), $updatePayload);

        $response->assertRedirect(route('school.students.index'));

        // Refresh instances
        $session2025Rec->refresh();
        $session2026Rec->refresh();
        $student->refresh();

        // 1. Verify 2025-2026 has been updated
        $this->assertEquals('Gupta', $session2025Rec->last_name);
        $this->assertEquals('Suresh Gupta', $session2025Rec->father_name);
        $this->assertEquals('9111111111', $session2025Rec->phone);
        $this->assertEquals('99', $session2025Rec->roll_number);

        // 2. Verify 2026-2027 is COMPLETELY UNTOUCHED & UNCHANGED
        $this->assertEquals('Verma', $session2026Rec->last_name);
        $this->assertEquals('Suresh Verma', $session2026Rec->father_name);
        $this->assertEquals('9800000000', $session2026Rec->phone);
        $this->assertEquals('10', $session2026Rec->roll_number);

        // 3. Permanent identity (admission_number) remains unchanged
        $this->assertEquals('ADM2002', $student->admission_number);
    }

    public function test_deleting_student_in_one_session_removes_only_that_session_enrollment(): void
    {
        $student = $this->createStudent([
            'admission_number'    => 'ADM3003',
            'first_name'          => 'Priya',
            'last_name'           => 'Singh',
            'academic_session_id' => $this->session2026->id,
            'class_id'            => $this->class1->id,
            'section_id'          => $this->sectionA->id,
            'roll_number'         => '15',
        ]);

        StudentSession::create([
            'school_id'           => $this->school->id,
            'student_id'          => $student->id,
            'academic_session_id' => $this->session2025->id,
            'class_id'            => $this->classUKG->id,
            'section_id'          => $this->sectionB->id,
            'roll_number'         => '15',
            'session_data'        => ['first_name' => 'Priya', 'last_name' => 'Singh'],
        ]);

        StudentSession::create([
            'school_id'           => $this->school->id,
            'student_id'          => $student->id,
            'academic_session_id' => $this->session2026->id,
            'class_id'            => $this->class1->id,
            'section_id'          => $this->sectionA->id,
            'roll_number'         => '15',
            'session_data'        => ['first_name' => 'Priya', 'last_name' => 'Singh'],
        ]);

        // Submit deletion request for 2026-2027 ONLY
        $deleteReq = StudentDeletionRequest::create([
            'school_id'           => $this->school->id,
            'student_id'          => $student->id,
            'academic_session_id' => $this->session2026->id,
            'admission_number'    => $student->admission_number,
            'student_name'        => $student->full_name,
            'class_name'          => 'Class 1',
            'section_name'        => 'A',
            'requested_by'        => $this->admin->id,
            'requested_by_name'   => $this->admin->name ?? 'Admin',
            'reason'              => 'Left school for 2026-2027',
            'status'              => 'pending',
            'requested_at'        => now(),
        ]);

        // Approve deletion
        $response = $this->actingAs($this->admin)
            ->post(route('school.students.deletion-requests.approve', $deleteReq->id));

        $response->assertSessionHas('success');

        // 1. Session 2026-2027 enrollment MUST be deleted
        $this->assertDatabaseMissing('student_sessions', [
            'student_id'          => $student->id,
            'academic_session_id' => $this->session2026->id,
        ]);

        // 2. Session 2025-2026 enrollment MUST REMAIN intact
        $this->assertDatabaseHas('student_sessions', [
            'student_id'          => $student->id,
            'academic_session_id' => $this->session2025->id,
        ]);

        // 3. Master student record is NOT deleted because historical session exists
        $this->assertDatabaseHas('students', [
            'id'               => $student->id,
            'admission_number' => 'ADM3003',
        ]);
    }

    public function test_bulk_edit_and_bulk_update_isolates_by_academic_session(): void
    {
        $student = $this->createStudent([
            'admission_number'    => 'ADM4004',
            'first_name'          => 'Ananya',
            'last_name'           => 'Patel',
            'father_name'         => 'Kiran Patel',
            'phone'               => '9988776655',
            'academic_session_id' => $this->session2026->id,
            'class_id'            => $this->class1->id,
            'section_id'          => $this->sectionA->id,
            'roll_number'         => '20',
        ]);

        $session2025Rec = StudentSession::create([
            'school_id'           => $this->school->id,
            'student_id'          => $student->id,
            'academic_session_id' => $this->session2025->id,
            'class_id'            => $this->classUKG->id,
            'section_id'          => $this->sectionB->id,
            'roll_number'         => '20',
            'session_data'        => [
                'first_name'   => 'Ananya',
                'last_name'    => 'Patel',
                'father_name'  => 'Kiran Patel',
                'phone'        => '9988776655',
            ],
        ]);

        $session2026Rec = StudentSession::create([
            'school_id'           => $this->school->id,
            'student_id'          => $student->id,
            'academic_session_id' => $this->session2026->id,
            'class_id'            => $this->class1->id,
            'section_id'          => $this->sectionA->id,
            'roll_number'         => '20',
            'session_data'        => [
                'first_name'   => 'Ananya',
                'last_name'    => 'Patel',
                'father_name'  => 'Kiran Patel',
                'phone'        => '9988776655',
            ],
        ]);

        // Bulk update in 2025-2026 ONLY
        $bulkPayload = [
            'academic_session_id' => $this->session2025->id,
            'students'            => [
                $student->id => [
                    'admission_number' => 'ADM4004',
                    'roll_number'      => '55',
                    'first_name'       => 'Ananya',
                    'last_name'        => 'Mehta', // Changed last name in 2025
                    'class_id'         => $this->classUKG->id,
                    'section_id'       => $this->sectionB->id,
                    'phone'            => '7777777777', // Changed phone in 2025
                    'father_name'      => 'Kiran Mehta',
                ]
            ]
        ];

        $response = $this->actingAs($this->admin)
            ->postJson(route('school.students.bulk-update'), $bulkPayload);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $session2025Rec->refresh();
        $session2026Rec->refresh();

        // 2025-2026 was updated
        $this->assertEquals('Mehta', $session2025Rec->last_name);
        $this->assertEquals('7777777777', $session2025Rec->phone);
        $this->assertEquals('55', $session2025Rec->roll_number);

        // 2026-2027 remains UNTOUCHED
        $this->assertEquals('Patel', $session2026Rec->last_name);
        $this->assertEquals('9988776655', $session2026Rec->phone);
        $this->assertEquals('20', $session2026Rec->roll_number);
    }

    public function test_student_promotion_freezes_from_session_and_creates_new_session(): void
    {
        $student = $this->createStudent([
            'admission_number'    => 'ADM5005',
            'first_name'          => 'Vikram',
            'last_name'           => 'Rao',
            'father_name'         => 'Nagesh Rao',
            'phone'               => '9848012345',
            'academic_session_id' => $this->session2025->id,
            'class_id'            => $this->classUKG->id,
            'section_id'          => $this->sectionB->id,
            'roll_number'         => '03',
        ]);

        StudentSession::create([
            'school_id'           => $this->school->id,
            'student_id'          => $student->id,
            'academic_session_id' => $this->session2025->id,
            'class_id'            => $this->classUKG->id,
            'section_id'          => $this->sectionB->id,
            'roll_number'         => '03',
            'session_data'        => [
                'first_name'  => 'Vikram',
                'last_name'   => 'Rao',
                'father_name' => 'Nagesh Rao',
                'phone'       => '9848012345',
            ],
        ]);

        // Promote student from 2025-2026 (UKG) to 2026-2027 (Class 1)
        $promotePayload = [
            'from_session_id' => $this->session2025->id,
            'from_class_id'   => $this->classUKG->id,
            'to_session_id'   => $this->session2026->id,
            'to_class_id'     => $this->class1->id,
            'to_section_id'   => $this->sectionA->id,
            'student_ids'     => [$student->id],
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('school.students.promote'), $promotePayload);

        $response->assertRedirect(route('school.students.index'));

        // Verify from_session is marked promoted and has frozen session_data
        $fromSession = StudentSession::where('student_id', $student->id)
            ->where('academic_session_id', $this->session2025->id)
            ->first();

        $this->assertNotNull($fromSession);
        $this->assertTrue((bool)$fromSession->is_promoted);
        $this->assertEquals('Vikram', $fromSession->first_name);
        $this->assertEquals('Rao', $fromSession->last_name);

        // Verify to_session exists for Class 1 in 2026-2027
        $toSession = StudentSession::where('student_id', $student->id)
            ->where('academic_session_id', $this->session2026->id)
            ->first();

        $this->assertNotNull($toSession);
        $this->assertEquals($this->class1->id, $toSession->class_id);
        $this->assertEquals($this->sectionA->id, $toSession->section_id);
        $this->assertFalse((bool)$toSession->is_promoted);

        // Permanent Admission Number is identical
        $this->assertEquals('ADM5005', $student->fresh()->admission_number);
    }

    public function test_student_edit_view_renders_properly_with_and_without_session_param(): void
    {
        $student = $this->createStudent([
            'admission_number'    => 'ADM6006',
            'first_name'          => 'Kavita',
            'last_name'           => 'Sen',
            'academic_session_id' => $this->session2026->id,
            'class_id'            => $this->class1->id,
            'section_id'          => $this->sectionA->id,
            'roll_number'         => '09',
        ]);

        StudentSession::create([
            'school_id'           => $this->school->id,
            'student_id'          => $student->id,
            'academic_session_id' => $this->session2025->id,
            'class_id'            => $this->classUKG->id,
            'section_id'          => $this->sectionB->id,
            'roll_number'         => '09',
            'session_data'        => [
                'first_name'  => 'Kavita UKG',
                'last_name'   => 'Sen',
                'father_name' => 'Dipak Sen',
            ],
        ]);

        // 1. Render edit view without explicit academic_session_id param (falls back to student current session)
        $response1 = $this->actingAs($this->admin)
            ->get(route('school.students.edit', $student->id));

        $response1->assertOk();
        $response1->assertSee('name="academic_session_id"', false);

        // 2. Render edit view with explicit historical session param
        $response2 = $this->actingAs($this->admin)
            ->get(route('school.students.edit', [
                'student'             => $student->id,
                'academic_session_id' => $this->session2025->id,
            ]));

        $response2->assertOk();
        $response2->assertSee('Kavita UKG');
        $response2->assertSee('Dipak Sen');
    }

    public function test_student_photo_is_isolated_per_academic_session(): void
    {
        $student = $this->createStudent([
            'admission_number'    => 'ADM7007',
            'first_name'          => 'Rahul',
            'last_name'           => 'Verma',
            'academic_session_id' => $this->session2026->id,
            'class_id'            => $this->class1->id,
            'section_id'          => $this->sectionA->id,
            'photo'               => 'students/photos/photo_2026.jpg',
        ]);

        $session2025Record = StudentSession::create([
            'school_id'           => $this->school->id,
            'student_id'          => $student->id,
            'academic_session_id' => $this->session2025->id,
            'class_id'            => $this->classUKG->id,
            'section_id'          => $this->sectionB->id,
            'roll_number'         => '01',
            'session_data'        => [
                'first_name' => 'Rahul',
                'last_name'  => 'Verma',
                'photo'      => 'students/photos/photo_2025.jpg',
            ],
        ]);

        $session2026Record = StudentSession::create([
            'school_id'           => $this->school->id,
            'student_id'          => $student->id,
            'academic_session_id' => $this->session2026->id,
            'class_id'            => $this->class1->id,
            'section_id'          => $this->sectionA->id,
            'roll_number'         => '01',
            'session_data'        => [
                'first_name' => 'Rahul',
                'last_name'  => 'Verma',
                'photo'      => 'students/photos/photo_2026.jpg',
            ],
        ]);

        // Verify session-specific photo retrieval via StudentSession
        $this->assertEquals('students/photos/photo_2025.jpg', $session2025Record->photo);
        $this->assertEquals('students/photos/photo_2026.jpg', $session2026Record->photo);

        // Verify session-specific photo retrieval via Student model
        $this->assertEquals('students/photos/photo_2025.jpg', $student->getPhotoInSession($this->session2025->id));
        $this->assertEquals('students/photos/photo_2026.jpg', $student->getPhotoInSession($this->session2026->id));

        // Verify resolvePhotoUrl picks correct photo per session
        $this->assertStringContainsString('photo_2025.jpg', $student->resolvePhotoUrl($this->session2025->id));
        $this->assertStringContainsString('photo_2026.jpg', $student->resolvePhotoUrl($this->session2026->id));
    }
}
