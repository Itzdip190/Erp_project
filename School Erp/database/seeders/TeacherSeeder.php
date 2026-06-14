<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Designation;
use App\Models\School;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('code', 'YIS2024')->firstOrFail();
        $department = Department::where('school_id', $school->id)->where('name', 'Academic')->firstOrFail();
        $designation = Designation::where('school_id', $school->id)->where('name', 'Teacher')->firstOrFail();

        // 1. Create User
        $user = User::firstOrCreate(
            ['email' => 'teacher@yis.com'],
            [
                'name' => 'John Teacher',
                'password' => Hash::make('SchoolTeacherSecurePass2026!'),
                'school_id' => $school->id,
                'is_active' => true,
            ]
        );
        $user->assignRole('teacher');

        // 2. Create Staff Profile
        Staff::firstOrCreate(
            [
                'school_id' => $school->id,
                'employee_id' => 'EMP001',
            ],
            [
                'user_id' => $user->id,
                'first_name' => 'John',
                'last_name' => 'Teacher',
                'email' => 'teacher@yis.com',
                'phone' => '9988776655',
                'date_of_birth' => '1985-05-15',
                'gender' => 'male',
                'blood_group' => 'O+',
                'address' => '45 Teaching Lane',
                'city' => 'Education Town',
                'state' => 'State',
                'pincode' => '123456',
                'department_id' => $department->id,
                'designation_id' => $designation->id,
                'employment_type' => 'permanent',
                'qualification' => 'Master of Education (M.Ed.)',
                'experience_years' => 8,
                'photo' => null,
                'joining_date' => '2020-06-01',
                'basic_salary' => 45000.00,
                'is_active' => true,
            ]
        );
    }
}
