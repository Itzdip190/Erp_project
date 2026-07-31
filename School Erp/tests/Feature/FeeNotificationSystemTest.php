<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\AcademicSession;
use App\Models\User;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\FeeSchedule;
use App\Models\FeeCategory;
use App\Models\FeeComponent;
use App\Models\FeeFine;
use App\Models\Notification;
use App\Services\FeeNotificationService;
use Carbon\Carbon;

class FeeNotificationSystemTest extends TestCase
{
    private function getTestSchool()
    {
        return School::firstOrCreate(
            ['code' => 'YIS'],
            ['name' => 'Yash International School', 'status' => true]
        );
    }

    private function createDummyStudent($schoolId, $userId = null, $guardianEmail = null)
    {
        $class = SchoolClass::firstOrCreate(
            ['school_id' => $schoolId, 'name' => 'Nursery'],
            ['numeric_name' => 1, 'status' => true]
        );

        $section = Section::firstOrCreate(
            ['school_id' => $schoolId, 'class_id' => $class->id, 'name' => 'A'],
            ['status' => true]
        );

        $session = AcademicSession::firstOrCreate(
            ['school_id' => $schoolId, 'name' => '2026-2027'],
            ['is_current' => true, 'start_date' => '2026-04-01', 'end_date' => '2027-03-31']
        );

        $student = Student::create([
            'school_id'             => $schoolId,
            'user_id'               => $userId,
            'class_id'              => $class->id,
            'section_id'            => $section->id,
            'academic_session_id'   => $session->id,
            'admission_number'      => 'YAS/2026/' . rand(1000, 9999),
            'admission_date'        => '2025-04-01',
            'first_name'            => 'Jeremiah',
            'last_name'             => 'Pall',
            'gender'                => 'Male',
            'father_name'           => 'Pranav Pall',
            'father_phone'          => '8948214162',
            'mother_name'           => 'Riya Pall',
            'mother_phone'          => '6521854914',
            'guardian_name'         => 'Pranav Pall',
            'guardian_email'        => $guardianEmail ?: 'pranav.pall' . rand(100, 999) . '@example.com',
            'guardian_phone'        => '8948214162',
            'guardian_relationship' => 'Father',
            'date_of_birth'         => '2018-05-15',
            'religion'              => 'Other',
            'blood_group'           => 'O+',
            'address'               => '166 Oak Zila',
            'city'                  => 'Vadodara',
            'state'                 => 'Gujarat',
            'pincode'               => '390001',
            'is_active'             => true,
        ]);

        return [$student, $session, $class];
    }

    public function test_due_date_reminder_notification_is_generated_correctly_with_late_fine()
    {
        $school = $this->getTestSchool();

        // Create Student User & Student
        $studentUser = User::create([
            'school_id' => $school->id,
            'name'      => 'Jeremiah Pall',
            'email'     => 'jeremiah.pall' . rand(100, 999) . '@example.com',
            'password'  => bcrypt('password'),
            'role'      => 'student',
        ]);

        $parentUser = User::create([
            'school_id' => $school->id,
            'name'      => 'Pranav Pall',
            'email'     => 'pranav.pall' . rand(100, 999) . '@example.com',
            'password'  => bcrypt('password'),
            'role'      => 'parent',
        ]);

        [$student, $session, $class] = $this->createDummyStudent($school->id, $studentUser->id, $parentUser->email);

        $category = FeeCategory::firstOrCreate(
            ['school_id' => $school->id, 'name' => 'General Tuition'],
            ['academic_session_id' => $session->id, 'status' => true]
        );

        // Create FeeFine policy
        $fine = FeeFine::create([
            'school_id'          => $school->id,
            'academic_session_id'=> $session->id,
            'name'               => 'Standard Late Fine',
            'fine_type'          => 'Fixed',
            'fine_amount'        => 100.00,
            'default_grace_days' => 0,
            'status'             => true,
        ]);

        // Create FeeSchedule
        $schedule = FeeSchedule::create([
            'school_id'          => $school->id,
            'academic_session_id'=> $session->id,
            'name'               => 'Annual Fee Schedule',
            'classes'            => json_encode([$class->id]),
            'start_date'         => '2026-04-01',
            'end_date'           => '2027-03-31',
            'fine_id'            => $fine->id,
            'no_of_installments' => 2,
            'installments'       => [
                ['installment_no' => 1, 'due_date' => Carbon::tomorrow()->toDateString(), 'grace_days' => 0],
            ],
        ]);

        // Create StudentFee due tomorrow
        $studentFee = StudentFee::create([
            'school_id'       => $school->id,
            'student_id'      => $student->id,
            'fee_category_id' => $category->id,
            'fee_schedule_id' => $schedule->id,
            'installment_no'  => 1,
            'amount'          => 6814.00,
            'due_date'        => Carbon::tomorrow()->toDateString(),
            'paid_amount'     => 0.00,
            'status'          => 'pending',
        ]);

        // Dispatch Due Date Reminders
        $sentCount = FeeNotificationService::sendDueDateReminders($school->id);

        $this->assertGreaterThan(0, $sentCount);

        // Verify Notification record in DB for Student User
        $notif = Notification::where('school_id', $school->id)
            ->where('user_id', $studentUser->id)
            ->where('type', 'fee_due_reminder')
            ->first();

        $this->assertNotNull($notif);
        $this->assertEquals('📢 Fee Payment Reminder', $notif->title);
        $this->assertStringContainsString('Installment 1 is due on ' . Carbon::tomorrow()->format('d F Y'), $notif->message);
        $this->assertStringContainsString('A Late Fine of ₹100 will be applied from ' . Carbon::tomorrow()->addDay()->format('d F Y'), $notif->message);

        // Test duplicate prevention
        $sentCountDuplicate = FeeNotificationService::sendDueDateReminders($school->id);
        $this->assertEquals(0, $sentCountDuplicate);
    }

    public function test_full_payment_success_notification()
    {
        $school = $this->getTestSchool();

        $studentUser = User::create([
            'school_id' => $school->id,
            'name'      => 'Jeremiah Pall',
            'email'     => 'test.fullpay' . rand(100, 999) . '@example.com',
            'password'  => bcrypt('password'),
            'role'      => 'student',
        ]);

        [$student, $session] = $this->createDummyStudent($school->id, $studentUser->id);

        $category = FeeCategory::firstOrCreate(
            ['school_id' => $school->id, 'name' => 'General Tuition'],
            ['academic_session_id' => $session->id, 'status' => true]
        );

        StudentFee::create([
            'school_id'      => $school->id,
            'student_id'     => $student->id,
            'fee_category_id'=> $category->id,
            'installment_no' => 1,
            'amount'         => 6814.00,
            'paid_amount'    => 6814.00,
            'due_date'       => Carbon::now()->addDays(10)->toDateString(),
            'status'         => 'paid',
        ]);

        FeeNotificationService::sendPaymentSuccessNotification($student, 1, 6814.00, 0.00);

        $notif = Notification::where('school_id', $school->id)
            ->where('user_id', $studentUser->id)
            ->where('type', 'fee_payment_success_full')
            ->first();

        $this->assertNotNull($notif);
        $this->assertEquals('Payment Successful', $notif->title);
        $this->assertStringContainsString('₹6,814 has been successfully received for Installment 1.', $notif->message);
        $this->assertStringContainsString('Your installment has been fully paid.', $notif->message);
    }

    public function test_partial_payment_success_notification()
    {
        $school = $this->getTestSchool();

        $studentUser = User::create([
            'school_id' => $school->id,
            'name'      => 'Jeremiah Pall',
            'email'     => 'test.partpay' . rand(100, 999) . '@example.com',
            'password'  => bcrypt('password'),
            'role'      => 'student',
        ]);

        [$student, $session] = $this->createDummyStudent($school->id, $studentUser->id);

        $category = FeeCategory::firstOrCreate(
            ['school_id' => $school->id, 'name' => 'General Tuition'],
            ['academic_session_id' => $session->id, 'status' => true]
        );

        StudentFee::create([
            'school_id'      => $school->id,
            'student_id'     => $student->id,
            'fee_category_id'=> $category->id,
            'installment_no' => 1,
            'amount'         => 6814.00,
            'paid_amount'    => 558.00,
            'due_date'       => Carbon::now()->addDays(10)->toDateString(),
            'status'         => 'partially_paid',
        ]);

        FeeNotificationService::sendPaymentSuccessNotification($student, 1, 558.00, 6256.00);

        $notif = Notification::where('school_id', $school->id)
            ->where('user_id', $studentUser->id)
            ->where('type', 'fee_payment_success_partial')
            ->first();

        $this->assertNotNull($notif);
        $this->assertEquals('Partial Payment Received', $notif->title);
        $this->assertStringContainsString('₹558 has been received for Installment 1.', $notif->message);
        $this->assertStringContainsString('Remaining Due: ₹6,256.', $notif->message);
    }

    public function test_payment_cancelled_notification()
    {
        $school = $this->getTestSchool();

        $studentUser = User::create([
            'school_id' => $school->id,
            'name'      => 'Jeremiah Pall',
            'email'     => 'test.cancelpay' . rand(100, 999) . '@example.com',
            'password'  => bcrypt('password'),
            'role'      => 'student',
        ]);

        [$student, $session] = $this->createDummyStudent($school->id, $studentUser->id);

        $category = FeeCategory::firstOrCreate(
            ['school_id' => $school->id, 'name' => 'General Tuition'],
            ['academic_session_id' => $session->id, 'status' => true]
        );

        StudentFee::create([
            'school_id'      => $school->id,
            'student_id'     => $student->id,
            'fee_category_id'=> $category->id,
            'installment_no' => 1,
            'amount'         => 6814.00,
            'paid_amount'    => 558.00,
            'due_date'       => Carbon::now()->addDays(10)->toDateString(),
            'status'         => 'partially_paid',
        ]);

        FeeNotificationService::sendPaymentCancelledNotification($student, 1, 6256.00);

        $notif = Notification::where('school_id', $school->id)
            ->where('user_id', $studentUser->id)
            ->where('type', 'fee_payment_cancelled')
            ->first();

        $this->assertNotNull($notif);
        $this->assertEquals('Payment Cancelled', $notif->title);
        $this->assertStringContainsString('Your previous payment has been cancelled.', $notif->message);
        $this->assertStringContainsString('Installment: Installment 1.', $notif->message);
        $this->assertStringContainsString('Current Outstanding Due: ₹6,256.', $notif->message);
    }

    public function test_fee_payment_does_not_generate_duplicate_notifications_across_school_users()
    {
        $school = $this->getTestSchool();

        // Create multiple dummy users in the school
        for ($i = 0; $i < 5; $i++) {
            User::create([
                'school_id' => $school->id,
                'name'      => "Other Student {$i}",
                'email'     => "otherstudent{$i}_" . rand(1000, 9999) . "@example.com",
                'password'  => bcrypt('password'),
                'role'      => 'student',
            ]);
        }

        $studentUser = User::create([
            'school_id' => $school->id,
            'name'      => 'Target Student',
            'email'     => 'target.student' . rand(100, 999) . '@example.com',
            'password'  => bcrypt('password'),
            'role'      => 'student',
        ]);

        [$student, $session] = $this->createDummyStudent($school->id, $studentUser->id);

        $initialNotifCount = Notification::where('school_id', $school->id)->count();

        // Send payment success notification
        FeeNotificationService::sendPaymentSuccessNotification($student, 1, 5000.00, 0.00);

        $newNotifCount = Notification::where('school_id', $school->id)->count() - $initialNotifCount;

        // Must create exactly 1 notification for the student (and 0 for uninvolved school users)
        $this->assertEquals(1, $newNotifCount);

        $targetNotif = Notification::where('school_id', $school->id)
            ->where('user_id', $studentUser->id)
            ->where('type', 'fee_payment_success_full')
            ->count();

        $this->assertEquals(1, $targetNotif);
    }

    public function test_student_scope_recipient_isolation()
    {
        $school = $this->getTestSchool();

        $studentUser1 = User::create([
            'school_id' => $school->id,
            'name'      => 'Student One',
            'email'     => 'student.one' . rand(100, 999) . '@example.com',
            'password'  => bcrypt('password'),
            'role'      => 'student',
        ]);

        $studentUser2 = User::create([
            'school_id' => $school->id,
            'name'      => 'Student Two',
            'email'     => 'student.two' . rand(100, 999) . '@example.com',
            'password'  => bcrypt('password'),
            'role'      => 'student',
        ]);

        [$student1, $session1] = $this->createDummyStudent($school->id, $studentUser1->id);

        // Send fee notification for Student 1
        FeeNotificationService::sendPaymentSuccessNotification($student1, 1, 3000.00, 0.00);

        // Unread count for Student 1 must be 1
        $this->assertEquals(1, \App\Services\NotificationService::getUnreadCount($studentUser1));

        // Unread count for Student 2 MUST BE 0 (isolated, no leakage)
        $this->assertEquals(0, \App\Services\NotificationService::getUnreadCount($studentUser2));
    }

    public function test_mark_all_read_endpoint_clears_unread_notifications()
    {
        $school = $this->getTestSchool();

        $studentUser = User::create([
            'school_id' => $school->id,
            'name'      => 'Student MarkRead',
            'email'     => 'student.mr' . rand(100, 999) . '@example.com',
            'password'  => bcrypt('password'),
            'role'      => 'student',
        ]);

        [$student, $session] = $this->createDummyStudent($school->id, $studentUser->id);

        // Send 3 fee notifications
        FeeNotificationService::sendPaymentSuccessNotification($student, 1, 1000.00, 2000.00);
        FeeNotificationService::sendPaymentSuccessNotification($student, 1, 1000.00, 1000.00);
        FeeNotificationService::sendPaymentSuccessNotification($student, 1, 1000.00, 0.00);

        $this->assertEquals(3, \App\Services\NotificationService::getUnreadCount($studentUser));

        // Act as student & mark all as read
        $response = $this->actingAs($studentUser)->postJson(route('notifications.read-all'));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Unread count must now be 0
        $this->assertEquals(0, \App\Services\NotificationService::getUnreadCount($studentUser));

        // Ensure records are NOT deleted from DB (history preserved)
        $totalInDb = Notification::where('school_id', $school->id)
            ->where('user_id', $studentUser->id)
            ->count();

        $this->assertEquals(3, $totalInDb);
    }
}
