<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentOptionalSubject;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubjectColorAndMappingTest extends TestCase
{
    public function test_subject_color_and_student_mapping_feature()
    {
        $school = School::create([
            'name' => 'Test ERP School',
            'code' => 'TESTSCH01',
            'status' => 'active',
        ]);

        $user = User::create([
            'name' => 'School Admin',
            'email' => 'admin@testschool.com',
            'password' => bcrypt('password'),
            'school_id' => $school->id,
            'role' => 'admin',
        ]);

        $session = AcademicSession::create([
            'school_id' => $school->id,
            'name' => '2026-2027',
            'start_date' => '2026-04-01',
            'end_date' => '2027-03-31',
            'is_current' => true,
        ]);

        $class = SchoolClass::create([
            'school_id' => $school->id,
            'name' => 'Nursery',
            'numeric_name' => 1,
        ]);

        $section = Section::create([
            'school_id' => $school->id,
            'class_id' => $class->id,
            'name' => 'A',
        ]);

        $studentData = [
            'school_id' => $school->id,
            'user_id' => $user->id,
            'academic_session_id' => $session->id,
            'class_id' => $class->id,
            'section_id' => $section->id,
            'first_name' => 'Student',
            'last_name' => 'One',
            'admission_number' => 'ADM001',
            'roll_number' => '1',
            'date_of_birth' => '2020-01-01',
            'gender' => 'male',
            'admission_date' => '2026-04-01',
            'guardian_name' => 'Father One',
            'guardian_phone' => '9876543210',
            'guardian_relationship' => 'father',
            'address' => '123 Main St',
            'city' => 'City',
            'state' => 'State',
            'pincode' => '123456',
        ];

        $student1 = Student::create($studentData);

        $studentData2 = array_merge($studentData, [
            'first_name' => 'Student',
            'last_name' => 'Two',
            'admission_number' => 'ADM002',
            'roll_number' => '2',
        ]);
        $student2 = Student::create($studentData2);

        // 1. Create Subject with Color
        $subject = Subject::create([
            'school_id' => $school->id,
            'class_id' => $class->id,
            'name' => 'Mathematics',
            'code' => 'MATH101',
            'color' => '#22C55E',
            'type' => 'Scholastic',
            'is_mandatory' => true,
        ]);

        $this->assertEquals('#22C55E', $subject->color);
        $this->assertTrue($subject->isStudentMapped($student1->id, $session->id));

        // 2. Map Student 1 only
        StudentOptionalSubject::create([
            'school_id' => $school->id,
            'student_id' => $student1->id,
            'subject_id' => $subject->id,
            'academic_session_id' => $session->id,
        ]);

        $this->assertTrue($subject->isStudentMapped($student1->id, $session->id));
        $this->assertFalse($subject->isStudentMapped($student2->id, $session->id));

        // 3. Test HTTP Controller via route
        $headers = [
            'X-School-Code' => $school->code,
            'X-Requested-With' => 'XMLHttpRequest',
        ];

        $response = $this->actingAs($user)
            ->withHeaders($headers)
            ->postJson(route('school.assignments.subjects.students'), [
                'class_ids' => [$class->id],
                'section_ids' => [$class->id => [$section->id]],
                'subject_id' => $subject->id,
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        // 4. Test Update Subject Color & Student Mapping via HTTP
        $updateResponse = $this->actingAs($user)
            ->withHeaders($headers)
            ->putJson(route('school.assignments.subjects.update', $subject->id), [
                'name' => 'Mathematics Advanced',
                'code' => 'MATH101',
                'color' => '#F97316',
                'is_mandatory' => 0,
                'type' => 'Scholastic',
                'class_ids' => [$class->id],
                'section_ids' => [$class->id => [$section->id]],
                'student_ids' => [$student2->id],
            ]);

        $updateResponse->assertStatus(200);
        $subject->refresh();
        $this->assertEquals('#F97316', $subject->color);
        $this->assertFalse($subject->isStudentMapped($student1->id, $session->id));
        $this->assertTrue($subject->isStudentMapped($student2->id, $session->id));
    }
}
