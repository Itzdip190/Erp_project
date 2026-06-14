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

class TestStudentSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('code', 'YIS2024')->firstOrFail();

        // Get Class 9 Section A
        $class9 = SchoolClass::where('school_id', $school->id)
            ->where('name', 'Class 9')->first();

        // If class doesn't exist yet, create it
        if (!$class9) {
            $class9 = SchoolClass::create([
                'school_id'    => $school->id,
                'name'         => 'Class 9',
                'numeric_name' => 9,
            ]);
        }

        $section9A = Section::where('school_id', $school->id)
            ->where('class_id', $class9->id)
            ->where('name', 'A')->first();

        if (!$section9A) {
            $section9A = Section::create([
                'school_id' => $school->id,
                'class_id'  => $class9->id,
                'name'      => 'A',
            ]);
        }

        // Get or create current academic session
        $session = AcademicSession::where('school_id', $school->id)
            ->where('is_current', true)->first();

        if (!$session) {
            $session = AcademicSession::create([
                'school_id'  => $school->id,
                'name'       => '2025-2026',
                'start_date' => '2025-04-01',
                'end_date'   => '2026-03-31',
                'is_current' => true,
            ]);
        }

        $admissionYear = (int) date('Y');

        // Test students list
        $students = [
            [
                'first_name'      => 'Aarav',
                'last_name'       => 'Sharma',
                'gender'          => 'male',
                'dob'             => '2010-03-15',
                'admission_no'    => "YIS/{$admissionYear}/00001",
                'seq'             => 1,
                'roll'            => '01',
                'guardian_name'   => 'Rajesh Sharma',
                'guardian_phone'  => '9876543201',
                'guardian_email'  => 'rajesh.sharma@email.com',
                'guardian_rel'    => 'father',
                'address'         => '12 MG Road, Block A',
                'city'            => 'Mumbai',
                'state'           => 'Maharashtra',
                'pincode'         => '400001',
                'email'           => 'aarav.student@yis.com',
            ],
            [
                'first_name'      => 'Priya',
                'last_name'       => 'Patel',
                'gender'          => 'female',
                'dob'             => '2010-07-22',
                'admission_no'    => "YIS/{$admissionYear}/00002",
                'seq'             => 2,
                'roll'            => '02',
                'guardian_name'   => 'Suresh Patel',
                'guardian_phone'  => '9876543202',
                'guardian_email'  => 'suresh.patel@email.com',
                'guardian_rel'    => 'father',
                'address'         => '45 Park Street',
                'city'            => 'Mumbai',
                'state'           => 'Maharashtra',
                'pincode'         => '400002',
                'email'           => 'priya.student@yis.com',
            ],
            [
                'first_name'      => 'Rahul',
                'last_name'       => 'Verma',
                'gender'          => 'male',
                'dob'             => '2010-11-05',
                'admission_no'    => "YIS/{$admissionYear}/00003",
                'seq'             => 3,
                'roll'            => '03',
                'guardian_name'   => 'Anil Verma',
                'guardian_phone'  => '9876543203',
                'guardian_email'  => 'anil.verma@email.com',
                'guardian_rel'    => 'father',
                'address'         => '78 Gandhi Nagar',
                'city'            => 'Pune',
                'state'           => 'Maharashtra',
                'pincode'         => '411001',
                'email'           => 'rahul.student@yis.com',
            ],
            [
                'first_name'      => 'Sneha',
                'last_name'       => 'Gupta',
                'gender'          => 'female',
                'dob'             => '2010-02-18',
                'admission_no'    => "YIS/{$admissionYear}/00004",
                'seq'             => 4,
                'roll'            => '04',
                'guardian_name'   => 'Ramesh Gupta',
                'guardian_phone'  => '9876543204',
                'guardian_email'  => 'ramesh.gupta@email.com',
                'guardian_rel'    => 'father',
                'address'         => '34 Laxmi Colony',
                'city'            => 'Nagpur',
                'state'           => 'Maharashtra',
                'pincode'         => '440001',
                'email'           => 'sneha.student@yis.com',
            ],
            [
                'first_name'      => 'Arjun',
                'last_name'       => 'Singh',
                'gender'          => 'male',
                'dob'             => '2010-09-30',
                'admission_no'    => "YIS/{$admissionYear}/00005",
                'seq'             => 5,
                'roll'            => '05',
                'guardian_name'   => 'Vikram Singh',
                'guardian_phone'  => '9876543205',
                'guardian_email'  => 'vikram.singh@email.com',
                'guardian_rel'    => 'father',
                'address'         => '21 Nehru Street',
                'city'            => 'Mumbai',
                'state'           => 'Maharashtra',
                'pincode'         => '400003',
                'email'           => 'arjun.student@yis.com',
            ],
        ];

        foreach ($students as $data) {
            // Create student user account
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'      => $data['first_name'] . ' ' . $data['last_name'],
                    'password'  => Hash::make('Student@2026!'),
                    'school_id' => $school->id,
                    'is_active' => true,
                ]
            );
            // Assign student role if not already assigned
            if (!$user->hasRole('student')) {
                $user->assignRole('student');
            }

            // Create student record
            $student = Student::firstOrCreate(
                [
                    'school_id'        => $school->id,
                    'admission_number' => $data['admission_no'],
                ],
                [
                    'user_id'              => $user->id,
                    'admission_sequence'   => $data['seq'],
                    'admission_year'       => $admissionYear,
                    'roll_number'          => $data['roll'],
                    'first_name'           => $data['first_name'],
                    'last_name'            => $data['last_name'],
                    'date_of_birth'        => $data['dob'],
                    'gender'               => $data['gender'],
                    'guardian_name'        => $data['guardian_name'],
                    'guardian_phone'       => $data['guardian_phone'],
                    'guardian_email'       => $data['guardian_email'],
                    'guardian_relationship'=> $data['guardian_rel'],
                    'address'              => $data['address'],
                    'city'                 => $data['city'],
                    'state'                => $data['state'],
                    'pincode'              => $data['pincode'],
                    'section_id'           => $section9A->id,
                    'class_id'             => $class9->id,
                    'academic_session_id'  => $session->id,
                    'admission_date'       => '2025-04-01',
                    'is_active'            => true,
                    'opening_due_balance'  => 0.00,
                ]
            );

            // Create student session record
            StudentSession::firstOrCreate(
                [
                    'school_id'           => $school->id,
                    'student_id'          => $student->id,
                    'academic_session_id' => $session->id,
                ],
                [
                    'class_id'    => $class9->id,
                    'section_id'  => $section9A->id,
                    'roll_number' => $data['roll'],
                    'is_promoted' => false,
                ]
            );

            // Seed attendance records for this student for the last 45 school days
            $adminUser = User::role('school_admin')->where('school_id', $school->id)->first() ?? $user;
            $startDate = now()->subDays(45);
            $endDate = now();
            for ($d = $startDate->copy(); $d->lte($endDate); $d->addDay()) {
                if ($d->isWeekday()) {
                    // 90% present, 5% absent, 5% late
                    $rand = rand(1, 100);
                    $status = 'present';
                    if ($rand <= 5) {
                        $status = 'absent';
                    } elseif ($rand <= 10) {
                        $status = 'late';
                    }

                    \App\Models\StudentAttendance::firstOrCreate([
                        'school_id'  => $school->id,
                        'student_id' => $student->id,
                        'date'       => $d->toDateString(),
                    ], [
                        'section_id'          => $section9A->id,
                        'class_id'            => $class9->id,
                        'academic_session_id' => $session->id,
                        'status'              => $status,
                        'marked_by'           => $adminUser->id,
                        'attendance_type'     => 'manual',
                    ]);
                }
            }
        }

        $this->command->info('✅ 5 test students created successfully!');
        $this->command->table(
            ['Name', 'Admission No', 'Login Email', 'Password'],
            collect($students)->map(fn($s) => [
                $s['first_name'] . ' ' . $s['last_name'],
                $s['admission_no'],
                $s['email'],
                'Student@2026!',
            ])->toArray()
        );
    }
}
