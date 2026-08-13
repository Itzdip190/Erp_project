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
use App\Support\SearchHelper;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SearchSystemTest extends TestCase
{
    protected School $school;
    protected Student $gauravYadav;
    protected Student $gauravMurthy;
    protected Staff $staffTeacher;

    protected function setUp(): void
    {
        parent::setUp();

        // Create or fetch tenant school
        $this->school = School::firstOrCreate(
            ['code' => 'YIS'],
            ['name' => 'Yash International School', 'status' => 'active']
        );

        $session = AcademicSession::firstOrCreate(
            ['school_id' => $this->school->id, 'name' => '2026-2027'],
            ['start_date' => '2026-04-01', 'end_date' => '2027-03-31', 'is_current' => true]
        );

        $schoolClass = SchoolClass::firstOrCreate(
            ['school_id' => $this->school->id, 'name' => 'LKG'],
            ['numeric_name' => 0]
        );

        $section = Section::firstOrCreate(
            ['school_id' => $this->school->id, 'class_id' => $schoolClass->id, 'name' => 'C']
        );

        $department = Department::firstOrCreate(
            ['school_id' => $this->school->id, 'name' => 'Teaching']
        );

        $designation = Designation::firstOrCreate(
            ['school_id' => $this->school->id, 'department_id' => $department->id, 'name' => 'Teacher']
        );

        // Seed test students
        $this->gauravYadav = Student::firstOrCreate(
            ['school_id' => $this->school->id, 'admission_number' => 'ADM2026007'],
            [
                'first_name' => 'Gaurav',
                'last_name' => 'Yadav',
                'father_name' => 'Ishaan Yadav',
                'mother_name' => 'Pooja Yadav',
                'guardian_name' => 'Ishaan Yadav',
                'guardian_phone' => '9602563421',
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
            ]
        );

        $this->gauravMurthy = Student::firstOrCreate(
            ['school_id' => $this->school->id, 'admission_number' => 'YAS/2026/00334'],
            [
                'first_name' => 'Gaurav',
                'last_name' => 'Murthy',
                'father_name' => 'Abhiram Murty',
                'mother_name' => 'Ishita Murty',
                'guardian_name' => 'Abhiram Murty',
                'guardian_phone' => '7436792415',
                'guardian_relationship' => 'father',
                'date_of_birth' => '2018-05-15',
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
            ]
        );

        // Seed test staff
        $this->staffTeacher = Staff::firstOrCreate(
            ['school_id' => $this->school->id, 'employee_id' => 'EMP202601'],
            [
                'first_name' => 'Ramesh',
                'last_name' => 'Sharma',
                'email' => 'ramesh.sharma@yis.com',
                'phone' => '9876543210',
                'department_id' => $department->id,
                'designation_id' => $designation->id,
                'joining_date' => '2025-01-01',
                'is_active' => true,
            ]
        );
    }

    #[Test]
    public function test_1_single_first_name_search()
    {
        $query = Student::where('school_id', $this->school->id);
        SearchHelper::applyStudentSearch($query, 'Gaurav');
        $results = $query->get();

        $this->assertTrue($results->contains('id', $this->gauravYadav->id));
        $this->assertTrue($results->contains('id', $this->gauravMurthy->id));
    }

    #[Test]
    public function test_2_exact_full_name_search()
    {
        $query = Student::where('school_id', $this->school->id);
        SearchHelper::applyStudentSearch($query, 'Gaurav Yadav');
        $results = $query->get();

        $this->assertTrue($results->contains('id', $this->gauravYadav->id));
        $this->assertFalse($results->contains('id', $this->gauravMurthy->id));
    }

    #[Test]
    public function test_3_lowercase_full_name_search()
    {
        $query = Student::where('school_id', $this->school->id);
        SearchHelper::applyStudentSearch($query, 'gaurav yadav');
        $results = $query->get();

        $this->assertTrue($results->contains('id', $this->gauravYadav->id));
    }

    #[Test]
    public function test_4_uppercase_full_name_search()
    {
        $query = Student::where('school_id', $this->school->id);
        SearchHelper::applyStudentSearch($query, 'GAURAV YADAV');
        $results = $query->get();

        $this->assertTrue($results->contains('id', $this->gauravYadav->id));
    }

    #[Test]
    public function test_5_mixed_case_full_name_search()
    {
        $query = Student::where('school_id', $this->school->id);
        SearchHelper::applyStudentSearch($query, 'GaUrAv YaDaV');
        $results = $query->get();

        $this->assertTrue($results->contains('id', $this->gauravYadav->id));
    }

    #[Test]
    public function test_6_partial_name_prefix_search()
    {
        $query = Student::where('school_id', $this->school->id);
        SearchHelper::applyStudentSearch($query, 'Gaurav Ya');
        $results = $query->get();

        $this->assertTrue($results->contains('id', $this->gauravYadav->id));
    }

    #[Test]
    public function test_7_last_name_only_search()
    {
        $query = Student::where('school_id', $this->school->id);
        SearchHelper::applyStudentSearch($query, 'Yadav');
        $results = $query->get();

        $this->assertTrue($results->contains('id', $this->gauravYadav->id));
    }

    #[Test]
    public function test_8_leading_trailing_spaces_search()
    {
        $query = Student::where('school_id', $this->school->id);
        SearchHelper::applyStudentSearch($query, '  Gaurav Yadav  ');
        $results = $query->get();

        $this->assertTrue($results->contains('id', $this->gauravYadav->id));
    }

    #[Test]
    public function test_9_multiple_internal_spaces_search()
    {
        $query = Student::where('school_id', $this->school->id);
        SearchHelper::applyStudentSearch($query, 'Gaurav    Yadav');
        $results = $query->get();

        $this->assertTrue($results->contains('id', $this->gauravYadav->id));
    }

    #[Test]
    public function test_10_typo_in_last_name_suggestion()
    {
        $suggestion = SearchHelper::getStudentSuggestion($this->school->id, 'Gaurav Yadva');
        $this->assertEquals('Gaurav Yadav', $suggestion);
    }

    #[Test]
    public function test_11_typo_extra_char_suggestion()
    {
        $suggestion = SearchHelper::getStudentSuggestion($this->school->id, 'Gaurav Yadavv');
        $this->assertEquals('Gaurav Yadav', $suggestion);
    }

    #[Test]
    public function test_12_staff_name_and_casing_search()
    {
        $query = Staff::where('school_id', $this->school->id);
        SearchHelper::applyStaffSearch($query, 'RAMESH SHARMA');
        $results = $query->get();

        $this->assertTrue($results->contains('id', $this->staffTeacher->id));
    }

    #[Test]
    public function test_13_staff_employee_id_search()
    {
        $query = Staff::where('school_id', $this->school->id);
        SearchHelper::applyStaffSearch($query, 'EMP202601');
        $results = $query->get();

        $this->assertTrue($results->contains('id', $this->staffTeacher->id));
    }
}
