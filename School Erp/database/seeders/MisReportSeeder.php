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
use App\Models\Notice;
use App\Models\DigitalDiary;
use App\Models\EnquiryLead;
use App\Models\StudentAttendance;
use App\Models\StaffAttendance;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use DB;

class MisReportSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::firstOrFail();
        $schoolId = $school->id;
        $today = today();
        $todayStr = $today->toDateString();

        // Only clean up our specific seeder data to preserve test records (which use @yis.com)
        $schoolComUserIds = User::where('email', 'like', '%@school.com')->pluck('id')->toArray();

        // Also fetch any staff/user IDs associated with our employee IDs to prevent duplicate constraint issues
        $targetEmployeeIds = [];
        for ($i = 0; $i < 11; $i++) {
            $targetEmployeeIds[] = 'EMP' . str_pad($i + 100, 3, '0', STR_PAD_LEFT);
        }
        $duplicateStaffs = Staff::withTrashed()->where('school_id', $schoolId)->whereIn('employee_id', $targetEmployeeIds)->get();
        $schoolComStaffIds = array_unique(array_merge(
            Staff::withTrashed()->whereIn('user_id', $schoolComUserIds)->pluck('id')->toArray(),
            $duplicateStaffs->pluck('id')->toArray()
        ));
        $schoolComUserIds = array_unique(array_merge(
            $schoolComUserIds,
            $duplicateStaffs->pluck('user_id')->filter()->toArray()
        ));

        $schoolComStudentIds = Student::whereIn('user_id', $schoolComUserIds)->pluck('id')->toArray();

        $driver = DB::getDriverName();
        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        }
        
        // Clean up our specific fees, receipts, and tokens
        StudentFee::whereIn('student_id', $schoolComStudentIds)->delete();
        FeeReceipt::whereIn('student_id', $schoolComStudentIds)->delete();
        FcmDeviceToken::whereIn('user_id', $schoolComUserIds)->delete();
        StudentSession::whereIn('student_id', $schoolComStudentIds)->delete();
        StudentAttendance::whereIn('student_id', $schoolComStudentIds)->delete();
        StaffAttendance::whereIn('staff_id', $schoolComStaffIds)->delete();
        DigitalDiary::whereIn('staff_id', $schoolComStaffIds)->delete();
        \App\Models\LeaveApplication::whereIn('user_id', $schoolComUserIds)->delete();
        
        // Delete today's attendance for `@school.com` targets
        StudentAttendance::whereIn('student_id', $schoolComStudentIds)->whereDate('date', $todayStr)->delete();
        StaffAttendance::whereIn('staff_id', $schoolComStaffIds)->whereDate('date', $todayStr)->delete();
        
        // Delete the school.com students, staff, and users
        Student::withTrashed()->where(function($q) use ($schoolComStudentIds) {
            $q->whereIn('id', $schoolComStudentIds)
              ->orWhere('admission_sequence', '>=', 200);
        })->forceDelete();
        Staff::withTrashed()->whereIn('id', $schoolComStaffIds)->forceDelete();
        User::whereIn('id', $schoolComUserIds)->delete();

        // Safely truncate/delete tables that aren't used by other seeders
        EnquiryLead::truncate();
        Notice::where('title', 'Annual Day Celebration')->delete();
        
        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
        }

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

        // Create Departments, Designations and FeeCategory
        $deptAcademic = Department::firstOrCreate(['school_id' => $schoolId, 'name' => 'Academic']);
        $deptAdmin = Department::firstOrCreate(['school_id' => $schoolId, 'name' => 'Administration']);
        
        $desgTeacher = Designation::firstOrCreate(['school_id' => $schoolId, 'name' => 'Teacher']);
        $desgPrincipal = Designation::firstOrCreate(['school_id' => $schoolId, 'name' => 'Principal']);
        
        $feeCat = \App\Models\FeeCategory::firstOrCreate(['school_id' => $schoolId, 'name' => 'Tuition Fee']);

        // 2. Create 11 Staff Members (1 Principal + 10 Teachers)
        $staffNames = [
            ['first' => 'Alok', 'last' => 'Sen', 'role' => 'Principal', 'designation' => $desgPrincipal, 'dept' => $deptAdmin],
            ['first' => 'Deepak', 'last' => 'Sharma', 'role' => 'Teacher', 'designation' => $desgTeacher, 'dept' => $deptAcademic],
            ['first' => 'Virudh', 'last' => 'Singh', 'role' => 'Teacher', 'designation' => $desgTeacher, 'dept' => $deptAcademic],
            ['first' => 'Noor', 'last' => 'Jahan', 'role' => 'Teacher', 'designation' => $desgTeacher, 'dept' => $deptAcademic],
            ['first' => 'Amit', 'last' => 'Kumar', 'role' => 'Teacher', 'designation' => $desgTeacher, 'dept' => $deptAcademic],
            ['first' => 'Sunita', 'last' => 'Rao', 'role' => 'Teacher', 'designation' => $desgTeacher, 'dept' => $deptAcademic],
            ['first' => 'Pooja', 'last' => 'Sharma', 'role' => 'Teacher', 'designation' => $desgTeacher, 'dept' => $deptAcademic],
            ['first' => 'Ramesh', 'last' => 'Gupta', 'role' => 'Teacher', 'designation' => $desgTeacher, 'dept' => $deptAcademic],
            ['first' => 'Karan', 'last' => 'Johar', 'role' => 'Teacher', 'designation' => $desgTeacher, 'dept' => $deptAcademic],
            ['first' => 'Priya', 'last' => 'Patel', 'role' => 'Teacher', 'designation' => $desgTeacher, 'dept' => $deptAcademic],
            ['first' => 'Rahul', 'last' => 'Verma', 'role' => 'Teacher', 'designation' => $desgTeacher, 'dept' => $deptAcademic],
        ];

        $staffList = [];
        foreach ($staffNames as $i => $s) {
            $email = strtolower($s['first'] . '.' . $s['last'] . '@school.com');
            $user = User::create([
                'name' => $s['first'] . ' ' . $s['last'],
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
                'employee_id' => 'EMP' . str_pad($i + 100, 3, '0', STR_PAD_LEFT),
                'first_name' => $s['first'],
                'last_name' => $s['last'],
                'email' => $email,
                'phone' => '987654' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'date_of_birth' => ($i % 3 === 0) 
                    ? '1985-06-' . str_pad(($i * 2) + 5, 2, '0', STR_PAD_LEFT)
                    : '1988-' . str_pad(($i % 11) + 1, 2, '0', STR_PAD_LEFT) . '-' . str_pad(($i * 2) + 3, 2, '0', STR_PAD_LEFT),
                'department_id' => $s['dept']->id,
                'designation_id' => $s['designation']->id,
                'employment_type' => 'permanent',
                'qualification' => $s['role'] === 'Principal' ? 'Ph.D in Education' : 'B.Ed, M.Sc',
                'experience_years' => $s['role'] === 'Principal' ? 20 : rand(2, 15),
                'joining_date' => '2024-04-01',
                'basic_salary' => $s['role'] === 'Principal' ? 75000 : 35000,
                'is_active' => true,
            ]);
            $staffList[] = $staff;

            // Seed FCM Device Token for 9 out of 11 staff members
            if ($i < 9) {
                FcmDeviceToken::create([
                    'school_id' => $schoolId,
                    'user_id' => $user->id,
                    'token' => 'token-staff-' . $user->id,
                    'device_name' => 'Android Phone',
                    'platform' => 'android'
                ]);
            }
        }

        // Create exactly 10 parents
        $parentUsers = [];
        for ($i = 0; $i < 10; $i++) {
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

            // Seed FCM Device Token for 6 parent users
            if ($i < 6) {
                FcmDeviceToken::create([
                    'school_id' => $schoolId,
                    'user_id' => $parentUser->id,
                    'token' => 'token-parent-' . $parentUser->id,
                    'device_name' => 'Android Phone',
                    'platform' => 'android'
                ]);
            }
        }

        // 3. Create 67 Students (> 50 students)
        $studentsToCreate = [
            [
                'first_name' => 'ABHINOOR',
                'last_name' => 'SINGH',
                'class' => 'Class 1',
                'section' => 'B',
                'gender' => 'male',
                'due_amount' => 16500,
                'due_days' => 95, // 90+ days
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
                'due_days' => 120, // 90+ days
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
                'due_days' => 100, // 90+ days
                'dob' => '2018-12-10',
                'admission_date' => '2025-04-01',
            ]
        ];

        // Fill remaining defaulters to reach exactly 16 (13 in 90+ days, 3 in 61-90 days)
        $defaulterNames = [
            ['first' => 'Rohan', 'last' => 'Das', 'class' => 'Class 3', 'sec' => 'A', 'amount' => 12000, 'due_days' => 110],
            ['first' => 'Kavya', 'last' => 'Iyer', 'class' => 'Class 4', 'sec' => 'A', 'amount' => 15000, 'due_days' => 98],
            ['first' => 'Ishaan', 'last' => 'Nair', 'class' => 'Class 5', 'sec' => 'A', 'amount' => 18000, 'due_days' => 105],
            ['first' => 'Diya', 'last' => 'Sen', 'class' => 'Class 6', 'sec' => 'A', 'amount' => 20000, 'due_days' => 115],
            ['first' => 'Aarav', 'last' => 'Mehta', 'class' => 'Class 7', 'sec' => 'A', 'amount' => 22000, 'due_days' => 130],
            ['first' => 'Zara', 'last' => 'Khan', 'class' => 'Class 8', 'sec' => 'A', 'amount' => 24000, 'due_days' => 92],
            ['first' => 'Karan', 'last' => 'Patel', 'class' => 'Class 9', 'sec' => 'A', 'amount' => 25000, 'due_days' => 140],
            ['first' => 'Sanya', 'last' => 'Malhotra', 'class' => 'Class 10', 'sec' => 'A', 'amount' => 28000, 'due_days' => 150],
            ['first' => 'Kabir', 'last' => 'Bahl', 'class' => 'Class 11', 'sec' => 'A', 'amount' => 30000, 'due_days' => 125],
            ['first' => 'Riya', 'last' => 'Kapoor', 'class' => 'Class 12', 'sec' => 'A', 'amount' => 32000, 'due_days' => 101],
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

        $boyNames = ['Aarav', 'Kabir', 'Ishaan', 'Rohan', 'Vihaan', 'Aditya', 'Arjun', 'Sai', 'Aryan', 'Krishna', 'Vivaan', 'Deepak', 'Amit', 'Karan', 'Dev', 'Rahul', 'Abhishek', 'Vicky', 'Sanjay', 'Rajesh'];
        $girlNames = ['Diya', 'Sanya', 'Zara', 'Riya', 'Ananya', 'Nisha', 'Priya', 'Pooja', 'Sunita', 'Kavya', 'Pallavi', 'Neha', 'Sneha', 'Aarti', 'Divya', 'Meera', 'Aditi', 'Anjali', 'Geeta', 'Pinky'];
        $lastNames = ['Sharma', 'Gupta', 'Verma', 'Singh', 'Kumar', 'Das', 'Sen', 'Rao', 'Patel', 'Kapoor', 'Roy', 'Joshi', 'Nair', 'Iyer', 'Mehta', 'Malhotra', 'Bahl', 'Johar', 'Khan', 'Chatterjee'];

        // Add regular students to reach 67 in total
        $remainingCount = 67 - count($studentsToCreate);
        for ($i = 0; $i < $remainingCount; $i++) {
            $clsSec = array_rand($classSectionMap);
            $classObj = explode('-', $clsSec);
            $className = $classObj[0];

            $age = 6;
            if ($className === 'Nursery') {
                $age = 3;
            } elseif ($className === 'LKG') {
                $age = 4;
            } elseif ($className === 'UKG') {
                $age = 5;
            } else {
                preg_match('/\d+/', $className, $matches);
                if (!empty($matches)) {
                    $age = 5 + (int)$matches[0];
                }
            }
            $birthYear = 2026 - $age;
            $dob = Carbon::create($birthYear, rand(1, 12), rand(1, 28))->toDateString();

            $gender = rand(1, 2) === 1 ? 'male' : 'female';
            $firstName = $gender === 'male' ? $boyNames[array_rand($boyNames)] : $girlNames[array_rand($girlNames)];
            $lastName = $lastNames[array_rand($lastNames)];

            $studentsToCreate[] = [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'class' => $className,
                'section' => $classObj[1],
                'gender' => $gender,
                'due_amount' => 0,
                'due_days' => 0,
                'dob' => $dob,
                // 11 new admissions this month (June)
                'admission_date' => $i < 11 ? $todayStr : '2025-04-01'
            ];
        }

        $allCreatedStudents = [];

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

            // Seed FCM Device Token for 45 out of 67 students
            if ($idx < 45) {
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
            $parentUser = $parentUsers[$idx % 10];

            $student = Student::create([
                'school_id' => $schoolId,
                'user_id' => $user->id,
                'admission_number' => 'YIS/2026/' . str_pad($idx + 200, 5, '0', STR_PAD_LEFT),
                'admission_sequence' => $idx + 200,
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
            $allCreatedStudents[] = $student;

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
                $dueDate = $today->copy()->subDays($s['due_days'])->toDateString();
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

        // April 2026 Collections (₹45,000 total)
        $aprilDate = '2026-04-15';
        $studentA = $allCreatedStudents[1];
        $studentB = $allCreatedStudents[2];
        $studentC = $allCreatedStudents[3];

        StudentFee::create([
            'school_id' => $schoolId,
            'student_id' => $studentA->id,
            'fee_category_id' => $feeCat->id,
            'amount' => 15000.00,
            'due_date' => $aprilDate,
            'paid_amount' => 15000.00,
            'status' => 'paid',
            'updated_at' => Carbon::parse($aprilDate),
            'created_at' => Carbon::parse($aprilDate),
        ]);
        FeeReceipt::create([
            'school_id' => $schoolId,
            'student_id' => $studentA->id,
            'receipt_number' => 'REC-APR-001',
            'amount_paid' => 15000.00,
            'payment_mode' => 'online',
            'payment_date' => $aprilDate,
            'created_at' => Carbon::parse($aprilDate),
        ]);

        StudentFee::create([
            'school_id' => $schoolId,
            'student_id' => $studentB->id,
            'fee_category_id' => $feeCat->id,
            'amount' => 15000.00,
            'due_date' => $aprilDate,
            'paid_amount' => 15000.00,
            'status' => 'paid',
            'updated_at' => Carbon::parse($aprilDate),
            'created_at' => Carbon::parse($aprilDate),
        ]);
        FeeReceipt::create([
            'school_id' => $schoolId,
            'student_id' => $studentB->id,
            'receipt_number' => 'REC-APR-002',
            'amount_paid' => 15000.00,
            'payment_mode' => 'cash',
            'payment_date' => $aprilDate,
            'created_at' => Carbon::parse($aprilDate),
        ]);

        StudentFee::create([
            'school_id' => $schoolId,
            'student_id' => $studentC->id,
            'fee_category_id' => $feeCat->id,
            'amount' => 15000.00,
            'due_date' => $aprilDate,
            'paid_amount' => 15000.00,
            'status' => 'paid',
            'updated_at' => Carbon::parse($aprilDate),
            'created_at' => Carbon::parse($aprilDate),
        ]);
        FeeReceipt::create([
            'school_id' => $schoolId,
            'student_id' => $studentC->id,
            'receipt_number' => 'REC-APR-003',
            'amount_paid' => 15000.00,
            'payment_mode' => 'cheque',
            'payment_date' => $aprilDate,
            'created_at' => Carbon::parse($aprilDate),
        ]);

        // May 2026 Collections (₹40,000 total)
        $mayDate = '2026-05-15';
        $studentD = $allCreatedStudents[4];
        $studentE = $allCreatedStudents[5];

        StudentFee::create([
            'school_id' => $schoolId,
            'student_id' => $studentD->id,
            'fee_category_id' => $feeCat->id,
            'amount' => 20000.00,
            'due_date' => $mayDate,
            'paid_amount' => 20000.00,
            'status' => 'paid',
            'updated_at' => Carbon::parse($mayDate),
            'created_at' => Carbon::parse($mayDate),
        ]);
        FeeReceipt::create([
            'school_id' => $schoolId,
            'student_id' => $studentD->id,
            'receipt_number' => 'REC-MAY-001',
            'amount_paid' => 20000.00,
            'payment_mode' => 'online',
            'payment_date' => $mayDate,
            'created_at' => Carbon::parse($mayDate),
        ]);

        StudentFee::create([
            'school_id' => $schoolId,
            'student_id' => $studentE->id,
            'fee_category_id' => $feeCat->id,
            'amount' => 20000.00,
            'due_date' => $mayDate,
            'paid_amount' => 20000.00,
            'status' => 'paid',
            'updated_at' => Carbon::parse($mayDate),
            'created_at' => Carbon::parse($mayDate),
        ]);
        FeeReceipt::create([
            'school_id' => $schoolId,
            'student_id' => $studentE->id,
            'receipt_number' => 'REC-MAY-002',
            'amount_paid' => 20000.00,
            'payment_mode' => 'cash',
            'payment_date' => $mayDate,
            'created_at' => Carbon::parse($mayDate),
        ]);

        // June 2026 Collections (₹35,000 total)
        $juneDate = '2026-06-15';
        $studentF = $allCreatedStudents[6];
        $studentG = $allCreatedStudents[7];

        StudentFee::create([
            'school_id' => $schoolId,
            'student_id' => $studentF->id,
            'fee_category_id' => $feeCat->id,
            'amount' => 15000.00,
            'due_date' => $juneDate,
            'paid_amount' => 15000.00,
            'status' => 'paid',
            'updated_at' => Carbon::parse($juneDate),
            'created_at' => Carbon::parse($juneDate),
        ]);
        FeeReceipt::create([
            'school_id' => $schoolId,
            'student_id' => $studentF->id,
            'receipt_number' => 'REC-JUN-001',
            'amount_paid' => 15000.00,
            'payment_mode' => 'online',
            'payment_date' => $juneDate,
            'created_at' => Carbon::parse($juneDate),
        ]);

        StudentFee::create([
            'school_id' => $schoolId,
            'student_id' => $studentG->id,
            'fee_category_id' => $feeCat->id,
            'amount' => 20000.00,
            'due_date' => $juneDate,
            'paid_amount' => 20000.00,
            'status' => 'paid',
            'updated_at' => Carbon::parse($juneDate),
            'created_at' => Carbon::parse($juneDate),
        ]);
        FeeReceipt::create([
            'school_id' => $schoolId,
            'student_id' => $studentG->id,
            'receipt_number' => 'REC-JUN-002',
            'amount_paid' => 20000.00,
            'payment_mode' => 'cash',
            'payment_date' => $juneDate,
            'created_at' => Carbon::parse($juneDate),
        ]);

        // 4. Seed Today's Fee Collections (₹5,261 total)
        // We will add 3 receipts for today that sum to exactly ₹5,261.
        $collectionStudent = $allCreatedStudents[0];
        FeeReceipt::create([
            'school_id' => $schoolId,
            'student_id' => $collectionStudent->id,
            'receipt_number' => 'REC-001',
            'amount_paid' => 2500.00,
            'payment_mode' => 'cash',
            'payment_date' => $todayStr
        ]);
        FeeReceipt::create([
            'school_id' => $schoolId,
            'student_id' => $collectionStudent->id,
            'receipt_number' => 'REC-002',
            'amount_paid' => 2000.00,
            'payment_mode' => 'cheque',
            'payment_date' => $todayStr
        ]);
        FeeReceipt::create([
            'school_id' => $schoolId,
            'student_id' => $collectionStudent->id,
            'receipt_number' => 'REC-003',
            'amount_paid' => 761.00,
            'payment_mode' => 'online',
            'payment_date' => $todayStr
        ]);

        // Also add StudentFee records associated with paid receipts today so that SchoolDashboardController sums paid amounts correctly
        StudentFee::create([
            'school_id' => $schoolId,
            'student_id' => $collectionStudent->id,
            'fee_category_id' => $feeCat->id,
            'amount' => 6000.00,
            'due_date' => $todayStr,
            'paid_amount' => 5261.00,
            'status' => 'paid',
            'updated_at' => $today
        ]);

        // 5. Seed Today's Student Attendance (90% present, 5% absent, 5% leave)
        foreach ($allCreatedStudents as $idx => $st) {
            $status = 'present';
            if ($idx % 20 === 0) {
                $status = 'absent';
            } elseif ($idx % 20 === 1) {
                $status = 'leave';
            } elseif ($idx % 20 === 2) {
                $status = 'half_day';
            }

            StudentAttendance::create([
                'school_id' => $schoolId,
                'student_id' => $st->id,
                'date' => $todayStr,
                'class_id' => $st->class_id,
                'section_id' => $st->section_id,
                'academic_session_id' => $session->id,
                'status' => $status,
                'attendance_type' => 'manual',
                'marked_by' => $staffList[0]->user_id
            ]);
        }

        // 6. Seed Today's Staff Attendance (9 present, 1 absent, 1 half_day)
        // Since we have 11 staff, this leaves exactly 0 unmarked!
        for ($i = 0; $i < 11; $i++) {
            $status = 'present';
            if ($i === 1) {
                $status = 'absent';
            } elseif ($i === 2) {
                $status = 'half_day';
            }

            StaffAttendance::create([
                'school_id' => $schoolId,
                'staff_id' => $staffList[$i]->id,
                'date' => $todayStr,
                'status' => $status,
                'clock_in_at' => '09:00:00',
                'clock_out_at' => '17:00:00',
                'attendance_type' => 'manual',
                'marked_by' => $staffList[0]->user_id
            ]);
        }

        // 7. Seed Enquiries Today (Admissions & Academic KPIs)
        EnquiryLead::create([
            'school_id' => $schoolId,
            'student_name' => 'Aarav Sen',
            'parent_name' => 'John Sen',
            'phone' => '9876543219',
            'email' => 'john.sen@email.com',
            'class_interested' => 'Nursery',
            'status' => 'new',
            'created_at' => $today
        ]);
        EnquiryLead::create([
            'school_id' => $schoolId,
            'student_name' => 'Preeti Sharma',
            'parent_name' => 'Vijay Sharma',
            'phone' => '9876543229',
            'email' => 'vijay.sh@email.com',
            'class_interested' => 'Class 1',
            'status' => 'contacted',
            'created_at' => $today
        ]);
        EnquiryLead::create([
            'school_id' => $schoolId,
            'student_name' => 'Rohan Sen',
            'parent_name' => 'Dev Sen',
            'phone' => '9876543239',
            'email' => 'dev.sen@email.com',
            'class_interested' => 'Class 2',
            'status' => 'payment',
            'created_at' => $today
        ]);
        EnquiryLead::create([
            'school_id' => $schoolId,
            'student_name' => 'Nikhil Gupta',
            'parent_name' => 'Raj Gupta',
            'phone' => '9876543249',
            'email' => 'raj.gu@email.com',
            'class_interested' => 'Class 5',
            'status' => 'evaluation',
            'created_at' => $today
        ]);

        // 8. Seed Notice for Today
        Notice::create([
            'school_id' => $schoolId,
            'title' => 'Annual Day Celebration',
            'content' => 'The Annual Day function will be held next Friday. All students are requested to prepare accordingly.',
            'target_audience' => 'all',
            'created_at' => $today
        ]);

        // 9. Seed Digital Diary homework entry for today by Teacher 1
        DigitalDiary::create([
            'school_id' => $schoolId,
            'class_id' => $classSectionMap['Class 9-A']['class_id'],
            'section_id' => $classSectionMap['Class 9-A']['section_id'],
            'staff_id' => $staffList[1]->id, // 2nd teacher
            'title' => 'Math Homework',
            'content' => 'Please solve questions 1 to 10 on page 45.',
            'diary_date' => $todayStr,
            'created_at' => $today
        ]);
    }
}
