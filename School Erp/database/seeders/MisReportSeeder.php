<?php

namespace Database\Seeders;

use App\Models\AcademicSession;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentSession;
use App\Models\User;
use App\Models\Staff;
use App\Models\Department;
use App\Models\Designation;
use App\Models\StudentFee;
use App\Models\FeeReceipt;
use App\Models\FcmDeviceToken;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use DB;

class MisReportSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Clean up existing data for mis report seeding to avoid duplicate constraints
        DB::statement('PRAGMA foreign_keys = OFF;');
        StudentFee::truncate();
        FeeReceipt::truncate();
        FcmDeviceToken::truncate();
        StudentSession::truncate();
        Student::truncate();
        Staff::truncate();
        User::where('role', 'student')->orWhere('role', 'teacher')->orWhere('role', 'parent')->delete();
        DB::statement('PRAGMA foreign_keys = ON;');

        $school = School::firstOrFail();
        $schoolId = $school->id;

        // Current session
        $session = AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first();
        if (!$session) {
            $session = AcademicSession::create([
                'school_id'  => $schoolId,
                'name'       => '2025-2026',
                'start_date' => '2025-04-01',
                'end_date'   => '2026-03-31',
                'is_current' => true,
            ]);
        }

        // Get/Create Classes & Sections
        $classNames = [
            'Nursery' => ['A'],
            'LKG' => ['A'],
            'UKG' => ['A'],
            'Class 1' => ['A', 'B'],
            'Class 2' => ['A', 'B'],
            'Class 3' => ['A'],
            'Class 4' => ['A'],
            'Class 5' => ['A'],
            'Class 6' => ['A'],
            'Class 7' => ['A'],
            'Class 8' => ['A'],
            'Class 9' => ['A'],
            'Class 10' => ['A'],
            'Class 11' => ['A'],
            'Class 12' => ['A'],
        ];

        $classSectionMap = [];
        foreach ($classNames as $cName => $secs) {
            $class = SchoolClass::firstOrCreate(
                ['school_id' => $schoolId, 'name' => $cName],
                ['numeric_name' => rand(1, 12)]
            );
            foreach ($secs as $sName) {
                $sec = Section::firstOrCreate([
                    'school_id' => $schoolId,
                    'class_id' => $class->id,
                    'name' => $sName
                ]);
                $classSectionMap["$cName-$sName"] = [
                    'class_id' => $class->id,
                    'section_id' => $sec->id
                ];
            }
        }

        // Create Department and Designation and FeeCategory
        $dept = Department::firstOrCreate(['school_id' => $schoolId, 'name' => 'Academic']);
        $desg = Designation::firstOrCreate(['school_id' => $schoolId, 'name' => 'Teacher']);
        $feeCat = \App\Models\FeeCategory::firstOrCreate(['school_id' => $schoolId, 'name' => 'Tuition Fee']);

        // 2. Create 8 Staff Members
        $staffNames = [
            ['first' => 'Deepak', 'last' => 'Sharma'],
            ['first' => 'Virudh', 'last' => 'Singh'],
            ['first' => 'Noor', 'last' => 'Jahan'],
            ['first' => 'Amit', 'last' => 'Kumar'],
            ['first' => 'Sunita', 'last' => 'Rao'],
            ['first' => 'Pooja', 'last' => 'Sharma'],
            ['first' => 'Ramesh', 'last' => 'Gupta'],
            ['first' => 'Karan', 'last' => 'Johar'],
        ];

        $staffList = [];
        foreach ($staffNames as $i => $sName) {
            $email = strtolower($sName['first'] . '.' . $sName['last'] . '@school.com');
            $user = User::create([
                'name' => $sName['first'] . ' ' . $sName['last'],
                'email' => $email,
                'password' => Hash::make('Teacher@2026!'),
                'school_id' => $schoolId,
                'role' => 'teacher',
                'is_active' => true,
            ]);
            $user->assignRole('teacher');

            $staff = Staff::create([
                'school_id' => $schoolId,
                'user_id' => $user->id,
                'employee_id' => 'EMP' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'first_name' => $sName['first'],
                'last_name' => $sName['last'],
                'email' => $email,
                'phone' => '987654' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'department_id' => $dept->id,
                'designation_id' => $desg->id,
                'employment_type' => 'permanent',
                'experience_years' => rand(2, 15),
                'joining_date' => '2024-04-01',
                'basic_salary' => 30000,
                'is_active' => true,
            ]);
            $staffList[] = $staff;

            // Seed FCM Device Token for 7 out of 8 staff members
            if ($i < 7) {
                FcmDeviceToken::create([
                    'school_id' => $schoolId,
                    'user_id' => $user->id,
                    'token' => 'token-staff-' . $user->id,
                    'device_name' => 'Android Phone',
                    'platform' => 'android'
                ]);
            }
        }

        // Create exactly 4 parents
        $parentUsers = [];
        for ($i = 0; $i < 4; $i++) {
            $parentEmail = "parent.{$i}@school.com";
            $parentUser = User::create([
                'name' => "Parent " . ($i + 1),
                'email' => $parentEmail,
                'password' => Hash::make('Parent@2026!'),
                'school_id' => $schoolId,
                'role' => 'parent',
                'is_active' => true,
            ]);
            $parentUser->assignRole('parent');
            $parentUsers[] = $parentUser;

            // Seed FCM Device Token for 2 parent users
            if ($i < 2) {
                FcmDeviceToken::create([
                    'school_id' => $schoolId,
                    'user_id' => $parentUser->id,
                    'token' => 'token-parent-' . $parentUser->id,
                    'device_name' => 'Android Phone',
                    'platform' => 'android'
                ]);
            }
        }

        // 3. Create 67 Students
        $studentsToCreate = [
            [
                'first_name' => 'ABHINOOR',
                'last_name' => 'SINGH',
                'class' => 'Class 1',
                'section' => 'B',
                'gender' => 'male',
                'due_amount' => 16500,
                'due_days' => 417,
                'dob' => '2019-05-15',
                'admission_date' => '2025-04-01',
            ],
            [
                'first_name' => 'Aadvik',
                'last_name' => 'Gupta',
                'class' => 'Class 2',
                'section' => 'A',
                'gender' => 'male',
                'due_amount' => 41400,
                'due_days' => 417,
                'dob' => '2018-08-20',
                'admission_date' => '2025-04-01',
            ],
            [
                'first_name' => 'Pallavi',
                'last_name' => 'Kumari',
                'class' => 'Class 2',
                'section' => 'B',
                'gender' => 'female',
                'due_amount' => 35000,
                'due_days' => 417,
                'dob' => '2018-12-10',
                'admission_date' => '2025-04-01',
            ]
        ];

        // Fill remaining defaulters to reach exactly 16 (13 in 90+ days, 3 in 61-90 days)
        $defaulterNames = [
            ['first' => 'Rohan', 'last' => 'Das', 'class' => 'Class 3', 'sec' => 'A', 'amount' => 12000, 'due_days' => 417],
            ['first' => 'Kavya', 'last' => 'Iyer', 'class' => 'Class 4', 'sec' => 'A', 'amount' => 15000, 'due_days' => 417],
            ['first' => 'Ishaan', 'last' => 'Nair', 'class' => 'Class 5', 'sec' => 'A', 'amount' => 18000, 'due_days' => 417],
            ['first' => 'Diya', 'last' => 'Sen', 'class' => 'Class 6', 'sec' => 'A', 'amount' => 20000, 'due_days' => 417],
            ['first' => 'Aarav', 'last' => 'Mehta', 'class' => 'Class 7', 'sec' => 'A', 'amount' => 22000, 'due_days' => 417],
            ['first' => 'Zara', 'last' => 'Khan', 'class' => 'Class 8', 'sec' => 'A', 'amount' => 24000, 'due_days' => 417],
            ['first' => 'Karan', 'last' => 'Patel', 'class' => 'Class 9', 'sec' => 'A', 'amount' => 25000, 'due_days' => 417],
            ['first' => 'Sanya', 'last' => 'Malhotra', 'class' => 'Class 10', 'sec' => 'A', 'amount' => 28000, 'due_days' => 417],
            ['first' => 'Kabir', 'last' => 'Bahl', 'class' => 'Class 11', 'sec' => 'A', 'amount' => 30000, 'due_days' => 417],
            ['first' => 'Riya', 'last' => 'Kapoor', 'class' => 'Class 12', 'sec' => 'A', 'amount' => 32000, 'due_days' => 417],
            // 3 defaulters in 61-90 days (due 75 days ago)
            ['first' => 'Ananya', 'last' => 'Roy', 'class' => 'Class 1', 'sec' => 'A', 'amount' => 5000, 'due_days' => 75],
            ['first' => 'Dev', 'last' => 'Joshi', 'class' => 'Class 2', 'sec' => 'A', 'amount' => 6000, 'due_days' => 75],
            ['first' => 'Nisha', 'last' => 'Sharma', 'class' => 'Class 3', 'sec' => 'A', 'amount' => 7000, 'due_days' => 75],
        ];

        foreach ($defaulterNames as $d) {
            $studentsToCreate[] = [
                'first_name' => $d['first'],
                'last_name' => $d['last'],
                'class' => $d['class'],
                'section' => $d['sec'],
                'gender' => rand(1, 2) === 1 ? 'male' : 'female',
                'due_amount' => $d['amount'],
                'due_days' => $d['due_days'],
                'dob' => '2016-01-01',
                'admission_date' => '2025-04-01'
            ];
        }

        // Add regular students to reach 67 in total
        $remainingCount = 67 - count($studentsToCreate);
        for ($i = 0; $i < $remainingCount; $i++) {
            // Assign randomly to classes
            $clsSec = array_rand($classSectionMap);
            $classObj = explode('-', $clsSec);
            $studentsToCreate[] = [
                'first_name' => 'Student',
                'last_name' => str_pad($i + 1, 2, '0', STR_PAD_LEFT),
                'class' => $classObj[0],
                'section' => $classObj[1],
                'gender' => rand(1, 2) === 1 ? 'male' : 'female',
                'due_amount' => 0,
                'due_days' => 0,
                'dob' => '2017-06-21', // Seed birthdays!
                // 11 new admissions this month (June 2026)
                'admission_date' => $i < 11 ? '2026-06-05' : '2025-04-01'
            ];
        }

        // Seed students and fees
        foreach ($studentsToCreate as $idx => $s) {
            $email = "student.{$idx}@school.com";
            $user = User::create([
                'name' => $s['first_name'] . ' ' . $s['last_name'],
                'email' => $email,
                'password' => Hash::make('Student@2026!'),
                'school_id' => $schoolId,
                'role' => 'student',
                'is_active' => true,
            ]);
            $user->assignRole('student');

            // Seed FCM Device Token for 10 out of 67 students
            if ($idx < 10) {
                FcmDeviceToken::create([
                    'school_id' => $schoolId,
                    'user_id' => $user->id,
                    'token' => 'token-student-' . $user->id,
                    'device_name' => 'Android Phone',
                    'platform' => 'android'
                ]);
            }

            // Mappings
            $clsSecKey = "{$s['class']}-{$s['section']}";
            $clsSec = $classSectionMap[$clsSecKey];

            // Parent mapping
            $parentUser = $parentUsers[$idx % 4];

            $student = Student::create([
                'school_id' => $schoolId,
                'user_id' => $user->id,
                'admission_number' => 'YIS/2026/' . str_pad($idx + 1, 5, '0', STR_PAD_LEFT),
                'admission_sequence' => $idx + 1,
                'admission_year' => 2026,
                'roll_number' => str_pad($idx + 1, 2, '0', STR_PAD_LEFT),
                'first_name' => $s['first_name'],
                'last_name' => $s['last_name'],
                'date_of_birth' => $s['dob'],
                'gender' => $s['gender'],
                'guardian_name' => $parentUser->name,
                'guardian_phone' => '998877' . str_pad($idx + 1, 4, '0', STR_PAD_LEFT),
                'guardian_email' => $parentUser->email,
                'guardian_relationship' => 'father',
                'address' => 'Sample Residential Address',
                'city' => 'Delhi',
                'state' => 'Delhi',
                'pincode' => '110001',
                'class_id' => $clsSec['class_id'],
                'section_id' => $clsSec['section_id'],
                'academic_session_id' => $session->id,
                'admission_date' => $s['admission_date'],
                'is_active' => true,
                'opening_due_balance' => 0.00,
            ]);

            // Promote in session
            StudentSession::create([
                'school_id' => $schoolId,
                'student_id' => $student->id,
                'academic_session_id' => $session->id,
                'class_id' => $clsSec['class_id'],
                'section_id' => $clsSec['section_id'],
                'roll_number' => $student->roll_number,
                'is_promoted' => false
            ]);

            // Seed Defaulter Student Fees
            if ($s['due_amount'] > 0) {
                $dueDate = Carbon::create(2026, 6, 21)->subDays($s['due_days'])->toDateString();
                StudentFee::create([
                    'school_id' => $schoolId,
                    'student_id' => $student->id,
                    'fee_category_id' => $feeCat->id,
                    'amount' => $s['due_amount'],
                    'due_date' => $dueDate,
                    'paid_amount' => 0.00,
                    'status' => 'unpaid'
                ]);
            }
        }

        // 4. Seed Overall Monthly Collection (₹2,561 in June 2026)
        // We will add 3 receipts in June 2026 that sum to exactly 2561.
        $collectionStudent = Student::first();
        FeeReceipt::create([
            'school_id' => $schoolId,
            'student_id' => $collectionStudent->id,
            'receipt_number' => 'REC-001',
            'amount_paid' => 1000.00,
            'payment_mode' => 'online',
            'payment_date' => '2026-06-15'
        ]);
        FeeReceipt::create([
            'school_id' => $schoolId,
            'student_id' => $collectionStudent->id,
            'receipt_number' => 'REC-002',
            'amount_paid' => 1500.00,
            'payment_mode' => 'cash',
            'payment_date' => '2026-06-18'
        ]);
        FeeReceipt::create([
            'school_id' => $schoolId,
            'student_id' => $collectionStudent->id,
            'receipt_number' => 'REC-003',
            'amount_paid' => 61.00,
            'payment_mode' => 'upi',
            'payment_date' => '2026-06-20'
        ]);

        // 5. Seed 5 staff attendances today (June 21, 2026) with status 'absent'
        // Since there are 8 staff in total, this leaves exactly 3 unmarked!
        $date = Carbon::create(2026, 6, 21);
        for ($i = 0; $i < 5; $i++) {
            \App\Models\StaffAttendance::create([
                'school_id' => $schoolId,
                'staff_id' => $staffList[$i]->id,
                'date' => $date->toDateString(),
                'status' => 'absent',
                'clock_in_at' => null,
                'clock_out_at' => null,
                'attendance_type' => 'manual',
            ]);
        }

        // 6. Seed 1 digital diary entry 3 days ago for Teacher 8 (EMP008)
        // This leaves exactly 7 teachers who haven't shared any content in 7 days!
        \App\Models\DigitalDiary::create([
            'school_id' => $schoolId,
            'class_id' => $classSectionMap['Class 9-A']['class_id'],
            'section_id' => $classSectionMap['Class 9-A']['section_id'],
            'staff_id' => $staffList[7]->id, // 8th teacher (EMP008)
            'title' => 'Sample Homework',
            'content' => 'Please complete exercise 4.2',
            'diary_date' => $date->copy()->subDays(3)->toDateString(),
        ]);
    }
}
