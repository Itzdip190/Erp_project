<?php

namespace Database\Seeders;

use App\Models\AcademicSession;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentSession;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ParentStudentSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('code', 'YIS2024')->firstOrFail();
        $class9 = SchoolClass::where('school_id', $school->id)->where('name', 'Class 9')->firstOrFail();
        $section9A = Section::where('school_id', $school->id)->where('class_id', $class9->id)->where('name', 'A')->firstOrFail();
        $session = AcademicSession::where('school_id', $school->id)->where('is_current', true)->firstOrFail();

        // 1. Create Parent User
        $parentUser = User::firstOrCreate(
            ['email' => 'parent@yis.com'],
            [
                'name' => 'John Doe',
                'password' => Hash::make('ParentSecurePass2026!'),
                'phone' => '9876543210',
                'school_id' => $school->id,
                'is_active' => true,
            ]
        );
        $parentUser->assignRole('parent');

        // 2. Create Student Jane Doe
        $admissionYear = (int) date('Y');
        $admissionNumber = "YIS/{$admissionYear}/00001";

        $student = Student::firstOrCreate(
            [
                'school_id' => $school->id,
                'admission_number' => $admissionNumber,
            ],
            [
                'user_id' => $parentUser->id,
                'admission_sequence' => 1,
                'admission_year' => $admissionYear,
                'roll_number' => '1',
                'first_name' => 'Jane',
                'last_name' => 'Doe',
                'date_of_birth' => '2012-08-20',
                'gender' => 'female',
                'guardian_name' => 'John Doe',
                'guardian_phone' => '9876543210',
                'guardian_email' => 'parent@yis.com',
                'guardian_relationship' => 'father',
                'address' => '123 Parent St',
                'city' => 'Education City',
                'state' => 'State',
                'pincode' => '123456',
                'section_id' => $section9A->id,
                'class_id' => $class9->id,
                'academic_session_id' => $session->id,
                'admission_date' => '2025-04-01',
                'is_active' => true,
                'opening_due_balance' => 0.00,
            ]
        );

        // 3. Create Student Session
        StudentSession::firstOrCreate(
            [
                'school_id' => $school->id,
                'student_id' => $student->id,
                'academic_session_id' => $session->id,
            ],
            [
                'class_id' => $class9->id,
                'section_id' => $section9A->id,
                'roll_number' => '1',
                'is_promoted' => false,
            ]
        );
    }
}
