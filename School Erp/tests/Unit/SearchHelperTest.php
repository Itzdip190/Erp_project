<?php

namespace Tests\Unit;

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
use Tests\TestCase;

class SearchHelperTest extends TestCase
{
    use RefreshDatabase;

    protected $school;
    protected $academicSession;
    protected $classA;
    protected $classB;
    protected $sectionA1;
    protected $sectionA2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->school = School::create([
            'name' => 'Yash International School',
            'code' => 'YIS001',
            'email' => 'info@yashschool.com',
            'phone' => '9876543210',
            'is_active' => true,
        ]);

        $this->academicSession = AcademicSession::create([
            'school_id' => $this->school->id,
            'name' => '2026-2027',
            'is_current' => true,
            'start_date' => '2026-04-01',
            'end_date' => '2027-03-31',
        ]);

        $this->classA = SchoolClass::create([
            'school_id' => $this->school->id,
            'name' => 'LKG',
            'numeric_name' => 1,
        ]);

        $this->classB = SchoolClass::create([
            'school_id' => $this->school->id,
            'name' => '5',
            'numeric_name' => 5,
        ]);

        $this->sectionA1 = Section::create([
            'school_id' => $this->school->id,
            'class_id' => $this->classA->id,
            'name' => 'C',
        ]);

        $this->sectionA2 = Section::create([
            'school_id' => $this->school->id,
            'class_id' => $this->classB->id,
            'name' => 'D',
        ]);

        // Student 1: Gaurav Yadav (First Name: Gaurav)
        Student::create([
            'school_id' => $this->school->id,
            'academic_session_id' => $this->academicSession->id,
            'class_id' => $this->classA->id,
            'section_id' => $this->sectionA1->id,
            'admission_number' => 'ADM2026007',
            'roll_number' => '1',
            'first_name' => 'Gaurav',
            'last_name' => 'Yadav',
            'father_name' => 'Ishaan Yadav',
            'mother_name' => 'Pooja Yadav',
            'guardian_name' => 'Ishaan Yadav',
            'guardian_relationship' => 'father',
            'guardian_phone' => '9602563421',
            'date_of_birth' => '2020-01-01',
            'gender' => 'male',
            'admission_date' => '2026-04-01',
            'address' => 'Test Address 1',
            'city' => 'Test City',
            'state' => 'Test State',
            'pincode' => '123456',
            'is_active' => true,
        ]);

        // Student 2: Karan Reddy (First Name: Karan, Father: Gaurav Reddy)
        Student::create([
            'school_id' => $this->school->id,
            'academic_session_id' => $this->academicSession->id,
            'class_id' => $this->classB->id,
            'section_id' => $this->sectionA2->id,
            'admission_number' => 'ADM2026025',
            'roll_number' => '1',
            'first_name' => 'Karan',
            'last_name' => 'Reddy',
            'father_name' => 'Gaurav Reddy',
            'mother_name' => 'Myra Reddy',
            'guardian_name' => 'Gaurav Reddy',
            'guardian_relationship' => 'father',
            'guardian_phone' => '9455022961',
            'date_of_birth' => '2016-01-01',
            'gender' => 'male',
            'admission_date' => '2026-04-01',
            'address' => 'Test Address 2',
            'city' => 'Test City',
            'state' => 'Test State',
            'pincode' => '123456',
            'is_active' => true,
        ]);

        // Student 3: Riya Roy (First Name: Riya, Father: Gaurav Roy)
        Student::create([
            'school_id' => $this->school->id,
            'academic_session_id' => $this->academicSession->id,
            'class_id' => $this->classA->id,
            'section_id' => $this->sectionA1->id,
            'admission_number' => 'ADM2026040',
            'roll_number' => '2',
            'first_name' => 'Riya',
            'last_name' => 'Roy',
            'father_name' => 'Gaurav Roy',
            'mother_name' => 'Kritika Roy',
            'guardian_name' => 'Gaurav Roy',
            'guardian_relationship' => 'father',
            'guardian_phone' => '9157473384',
            'date_of_birth' => '2020-02-01',
            'gender' => 'female',
            'admission_date' => '2026-04-01',
            'address' => 'Test Address 3',
            'city' => 'Test City',
            'state' => 'Test State',
            'pincode' => '123456',
            'is_active' => true,
        ]);

        // Student 4: Gaurav Sharma (First Name: Gaurav)
        Student::create([
            'school_id' => $this->school->id,
            'academic_session_id' => $this->academicSession->id,
            'class_id' => $this->classA->id,
            'section_id' => $this->sectionA1->id,
            'admission_number' => 'ADM2026099',
            'roll_number' => '3',
            'first_name' => 'Gaurav',
            'last_name' => 'Sharma',
            'father_name' => 'Vikram Sharma',
            'mother_name' => 'Anita Sharma',
            'guardian_name' => 'Vikram Sharma',
            'guardian_relationship' => 'father',
            'guardian_phone' => '9888877777',
            'date_of_birth' => '2020-03-01',
            'gender' => 'male',
            'admission_date' => '2026-04-01',
            'address' => 'Test Address 4',
            'city' => 'Test City',
            'state' => 'Test State',
            'pincode' => '123456',
            'is_active' => true,
        ]);
    }

    /** Test 1: Student search for 'gaurav' returns ONLY matching students and NO unrelated students */
    public function test_student_search_for_gaurav_returns_only_matching_students()
    {
        $query = Student::where('school_id', $this->school->id);
        SearchHelper::applyStudentSearch($query, 'gaurav');
        $results = $query->get();

        $names = $results->pluck('first_name')->toArray();

        $this->assertCount(2, $results);
        $this->assertContains('Gaurav', $names);
        $this->assertNotContains('Karan', $names);
        $this->assertNotContains('Riya', $names);
    }

    /** Test 2: Full name search 'Gaurav Yadav' */
    public function test_student_full_name_search()
    {
        $query = Student::where('school_id', $this->school->id);
        SearchHelper::applyStudentSearch($query, 'Gaurav Yadav');
        $results = $query->get();

        $this->assertCount(1, $results);
        $this->assertEquals('Gaurav', $results->first()->first_name);
        $this->assertEquals('Yadav', $results->first()->last_name);
    }

    /** Test 3: Case-insensitive search 'GAURAV' */
    public function test_student_case_insensitive_search()
    {
        $query = Student::where('school_id', $this->school->id);
        SearchHelper::applyStudentSearch($query, 'GAURAV');
        $results = $query->get();

        $this->assertCount(2, $results);
    }

    /** Test 4: Last name search 'Yadav' */
    public function test_student_last_name_search()
    {
        $query = Student::where('school_id', $this->school->id);
        SearchHelper::applyStudentSearch($query, 'Yadav');
        $results = $query->get();

        $this->assertCount(1, $results);
        $this->assertEquals('Yadav', $results->first()->last_name);
    }

    /** Test 5: Invalid search 'xyz_non_existing' returns 0 results */
    public function test_student_invalid_search_returns_empty()
    {
        $query = Student::where('school_id', $this->school->id);
        SearchHelper::applyStudentSearch($query, 'xyz_non_existing');
        $results = $query->get();

        $this->assertCount(0, $results);
    }

    /** Test 6: Class filter + Search intersection */
    public function test_student_class_filter_and_search_intersection()
    {
        $query = Student::where('school_id', $this->school->id)
            ->where('class_id', $this->classA->id);
        SearchHelper::applyStudentSearch($query, 'gaurav');
        $results = $query->get();

        $this->assertCount(2, $results);

        // Searching in Class B for 'gaurav' should return 0 since Karan Reddy is in Class B but his name is Karan
        $queryB = Student::where('school_id', $this->school->id)
            ->where('class_id', $this->classB->id);
        SearchHelper::applyStudentSearch($queryB, 'gaurav');
        $resultsB = $queryB->get();

        $this->assertCount(0, $resultsB);
    }

    /** Test 7: Section filter + Search intersection */
    public function test_student_section_filter_and_search_intersection()
    {
        $query = Student::where('school_id', $this->school->id)
            ->where('section_id', $this->sectionA1->id);
        SearchHelper::applyStudentSearch($query, 'gaurav');
        $results = $query->get();

        $this->assertCount(2, $results);
    }

    /** Test 8: Staff search returns only matching staff */
    public function test_staff_search()
    {
        $dept = Department::create(['school_id' => $this->school->id, 'name' => 'Academic']);
        $desg = Designation::create(['school_id' => $this->school->id, 'name' => 'Teacher']);

        Staff::create([
            'school_id' => $this->school->id,
            'department_id' => $dept->id,
            'designation_id' => $desg->id,
            'employee_id' => 'EMP001',
            'first_name' => 'Rahul',
            'last_name' => 'Verma',
            'email' => 'rahul@school.com',
            'phone' => '9998887776',
            'joining_date' => '2026-01-01',
            'is_active' => true,
        ]);

        Staff::create([
            'school_id' => $this->school->id,
            'department_id' => $dept->id,
            'designation_id' => $desg->id,
            'employee_id' => 'EMP002',
            'first_name' => 'Amit',
            'last_name' => 'Kumar',
            'email' => 'amit@school.com',
            'phone' => '9998887775',
            'joining_date' => '2026-01-01',
            'is_active' => true,
        ]);

        $query = Staff::where('school_id', $this->school->id);
        SearchHelper::applyStaffSearch($query, 'rahul');
        $results = $query->get();

        $this->assertCount(1, $results);
        $this->assertEquals('Rahul', $results->first()->first_name);
    }

    /** Test 9: Parent search returns parent details */
    public function test_parent_search()
    {
        $query = Student::where('school_id', $this->school->id);
        SearchHelper::applyParentSearch($query, 'Gaurav Reddy');
        $results = $query->get();

        $this->assertCount(1, $results);
        $this->assertEquals('Karan', $results->first()->first_name);
    }

    /** Test 10: Reset Password user search strictly filters matching users */
    public function test_reset_password_user_search()
    {
        // User 1: Student Gaurav Yadav
        $u1 = User::create([
            'school_id' => $this->school->id,
            'name' => 'Gaurav Yadav',
            'email' => 'gaurav.yadav@student.com',
            'password' => bcrypt('password'),
            'role' => 'student',
            'is_active' => true,
        ]);

        // User 2: Student Karan Reddy (whose father is Gaurav Reddy)
        $u2 = User::create([
            'school_id' => $this->school->id,
            'name' => 'Karan Reddy',
            'email' => 'karan.reddy@student.com',
            'password' => bcrypt('password'),
            'role' => 'student',
            'is_active' => true,
        ]);

        // User 3: Parent Gaurav Reddy
        $u3 = User::create([
            'school_id' => $this->school->id,
            'name' => 'Gaurav Reddy',
            'email' => 'gaurav.reddy@parent.com',
            'password' => bcrypt('password'),
            'role' => 'parent',
            'is_active' => true,
        ]);

        $query = User::where('school_id', $this->school->id);
        SearchHelper::applyUserSearch($query, 'gaurav');
        $results = $query->get();

        $userNames = $results->pluck('name')->toArray();

        // Gaurav Yadav and Gaurav Reddy match; Karan Reddy MUST NOT match
        $this->assertContains('Gaurav Yadav', $userNames);
        $this->assertContains('Gaurav Reddy', $userNames);
        $this->assertNotContains('Karan Reddy', $userNames);
    }
}
