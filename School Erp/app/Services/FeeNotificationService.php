<?php

namespace App\Services;

use App\Models\Student;
use App\Models\StudentFee;
use App\Models\FeeFine;
use App\Models\FeeSchedule;
use App\Models\User;
use App\Models\Notification;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FeeNotificationService
{
    /**
     * Send Due Date Reminder notifications for installments due tomorrow.
     * Scheduled to run daily at 07:00 AM.
     */
    public static function sendDueDateReminders(?int $schoolId = null): int
    {
        $tomorrow = Carbon::tomorrow()->toDateString();
        $todayStr = Carbon::today()->toDateString();

        $query = StudentFee::withoutGlobalScopes()
            ->where(function ($q) {
                $q->whereNotIn('invoice_status', ['cancelled', 'refunded'])
                  ->orWhereNull('invoice_status');
            })
            ->where('status', '!=', 'paid')
            ->whereDate('due_date', $tomorrow);

        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }

        $dueFees = $query->with(['student', 'feeSchedule'])->get();
        $remindersSent = 0;

        // Group by student_id and installment_no to avoid sending multiple duplicate messages per student-installment
        $grouped = $dueFees->groupBy(function ($fee) {
            return $fee->student_id . '_' . $fee->installment_no;
        });

        foreach ($grouped as $groupKey => $fees) {
            $firstFee = $fees->first();
            $student = $firstFee->student;
            if (!$student || !$student->is_active) {
                continue;
            }

            $instNo = $firstFee->installment_no;
            $dueDateFormatted = Carbon::parse($firstFee->due_date)->format('d F Y');

            // Find target portal users (Student & Parent)
            $userIds = self::getPortalUsersForStudent($student);
            if (empty($userIds)) {
                continue;
            }

            // Check if reminder was already sent today for this student & installment to avoid duplicates
            $alreadyNotified = Notification::where('school_id', $student->school_id)
                ->whereIn('user_id', $userIds)
                ->where('module', 'fee')
                ->where('type', 'fee_due_reminder')
                ->where('related_id', $firstFee->id)
                ->whereDate('created_at', $todayStr)
                ->exists();

            if ($alreadyNotified) {
                continue;
            }

            // Late Fine details calculation
            $lateFineText = '';
            $finePolicy = self::getFinePolicyForFee($firstFee);
            if ($finePolicy && $finePolicy->status && floatval($finePolicy->fine_amount) > 0) {
                $fineAmountVal = floatval($finePolicy->fine_amount);
                $graceDays = 0;

                // Check schedule grace days
                if ($firstFee->feeSchedule && !empty($firstFee->feeSchedule->installments)) {
                    $instConfig = collect($firstFee->feeSchedule->installments)->firstWhere('installment_no', $instNo);
                    if ($instConfig) {
                        $graceDays = (int) ($instConfig['grace_days'] ?? $finePolicy->default_grace_days ?? 0);
                    }
                } else {
                    $graceDays = (int) ($finePolicy->default_grace_days ?? 0);
                }

                // Late fine starts 1 day after due date + grace period
                $lateFineStartDate = Carbon::parse($firstFee->due_date)->addDays($graceDays + 1)->format('d F Y');
                $lateFineAmountFmt = self::formatAmount($fineAmountVal);

                $lateFineText = " A Late Fine of ₹{$lateFineAmountFmt} will be applied from {$lateFineStartDate} if the payment is not completed before the due date.";
            }

            $title = "📢 Fee Payment Reminder";
            $message = "Your fee payment for Installment {$instNo} is due on {$dueDateFormatted}. Please complete the payment before the due date to avoid penalties.{$lateFineText}";

            foreach ($userIds as $userId) {
                $userObj = User::find($userId);
                $recipientRole = ($userObj && ($userObj->hasRole('parent') || $userObj->role === 'parent')) ? 'parent' : 'student';

                NotificationService::send([
                    'school_id'      => $student->school_id,
                    'user_id'        => $userId,
                    'recipient_role' => $recipientRole,
                    'title'          => $title,
                    'message'        => trim($message),
                    'module'         => 'fee',
                    'type'           => 'fee_due_reminder',
                    'related_id'     => $firstFee->id,
                    'action_url'     => route('parent.fees.index'),
                    'icon'           => 'fa-receipt',
                    'color'          => '#f59e0b',
                ]);
                $remindersSent++;
            }
        }

        return $remindersSent;
    }

    /**
     * Send Successful Payment Notification (Full Payment or Partial Payment).
     */
    public static function sendPaymentSuccessNotification(
        Student $student,
        int|string $installmentNo,
        float $paidAmount,
        ?float $remainingDue = null
    ): void {
        if (!$student) return;

        $userIds = self::getPortalUsersForStudent($student);
        if (empty($userIds)) return;

        if ($remainingDue === null) {
            $remainingDue = self::calculateRemainingDue($student, $installmentNo);
        }

        $paidAmountFmt = self::formatAmount($paidAmount);

        if ($remainingDue <= 0) {
            // Full Payment Case
            $title = "Payment Successful";
            $message = "₹{$paidAmountFmt} has been successfully received for Installment {$installmentNo}. Your installment has been fully paid. Thank you.";
            $type = "fee_payment_success_full";
        } else {
            // Partial Payment Case
            $remainingDueFmt = self::formatAmount($remainingDue);
            $title = "Partial Payment Received";
            $message = "₹{$paidAmountFmt} has been received for Installment {$installmentNo}. Remaining Due: ₹{$remainingDueFmt}. Please complete the remaining payment before the due date.";
            $type = "fee_payment_success_partial";
        }

        foreach ($userIds as $userId) {
            $userObj = User::find($userId);
            $recipientRole = ($userObj && ($userObj->hasRole('parent') || $userObj->role === 'parent')) ? 'parent' : 'student';

            NotificationService::send([
                'school_id'      => $student->school_id,
                'user_id'        => $userId,
                'recipient_role' => $recipientRole,
                'title'          => $title,
                'message'        => $message,
                'module'         => 'fee',
                'type'           => $type,
                'related_id'     => $student->id,
                'action_url'     => route('parent.fees.index'),
                'icon'           => 'fa-receipt',
                'color'          => '#059669',
            ]);
        }
    }

    /**
     * Send Payment Cancelled Notification.
     */
    public static function sendPaymentCancelledNotification(
        Student $student,
        int|string $installmentNo,
        ?float $currentOutstandingDue = null
    ): void {
        if (!$student) return;

        $userIds = self::getPortalUsersForStudent($student);
        if (empty($userIds)) return;

        if ($currentOutstandingDue === null) {
            $currentOutstandingDue = self::calculateCurrentOutstandingDue($student, $installmentNo);
        }

        $currentDueFmt = self::formatAmount($currentOutstandingDue);

        $title = "Payment Cancelled";
        $message = "Your previous payment has been cancelled. Installment: Installment {$installmentNo}. Current Outstanding Due: ₹{$currentDueFmt}. Please complete the payment again.";

        foreach ($userIds as $userId) {
            $userObj = User::find($userId);
            $recipientRole = ($userObj && ($userObj->hasRole('parent') || $userObj->role === 'parent')) ? 'parent' : 'student';

            NotificationService::send([
                'school_id'      => $student->school_id,
                'user_id'        => $userId,
                'recipient_role' => $recipientRole,
                'title'          => $title,
                'message'        => $message,
                'module'         => 'fee',
                'type'           => 'fee_payment_cancelled',
                'related_id'     => $student->id,
                'action_url'     => route('parent.fees.index'),
                'icon'           => 'fa-receipt',
                'color'          => '#ef4444',
            ]);
        }
    }

    /**
     * Calculate remaining due for a specific student and installment dynamically.
     */
    public static function calculateRemainingDue(Student $student, int|string $installmentNo): float
    {
        $fees = StudentFee::withoutGlobalScopes()
            ->where(function ($q) {
                $q->whereNotIn('invoice_status', ['cancelled', 'refunded'])
                  ->orWhereNull('invoice_status');
            })
            ->where('school_id', $student->school_id)
            ->where('student_id', $student->id)
            ->where('installment_no', $installmentNo)
            ->get();

        $netOwed = 0.00;
        $totalPaid = 0.00;

        foreach ($fees as $sf) {
            $amt = floatval($sf->amount);
            $fine = floatval($sf->fine_amount_applied ?? 0);
            $disc = floatval($sf->instant_discount_amount ?? 0);
            $paid = floatval($sf->paid_amount ?? 0);

            $netOwed += max(0, ($amt + $fine - $disc));
            $totalPaid += $paid;
        }

        return max(0.00, $netOwed - $totalPaid);
    }

    /**
     * Calculate current outstanding due for a student and installment dynamically.
     */
    public static function calculateCurrentOutstandingDue(Student $student, int|string $installmentNo): float
    {
        return self::calculateRemainingDue($student, $installmentNo);
    }

    /**
     * Get target portal User IDs for Student and Parent associated with a student.
     * Excludes Admin, Staff, and Teacher accounts strictly.
     */
    public static function getPortalUsersForStudent(Student $student): array
    {
        $targetUserIds = [];

        // 1. Student User ID
        if ($student->user_id) {
            $targetUserIds[] = (int) $student->user_id;
        }

        // Search for student user by email/admission if user_id is null
        if (empty($student->user_id) && !empty($student->email)) {
            $studentUsers = User::withoutGlobalScope(\App\Models\Scopes\SchoolScope::class)
                ->where('school_id', $student->school_id)
                ->where('email', $student->email)
                ->pluck('id')
                ->toArray();
            $targetUserIds = array_merge($targetUserIds, $studentUsers);
        }

        // 2. Parent User ID(s)
        $parentEmails = array_filter([
            $student->guardian_email,
            $student->father_email,
            $student->mother_email,
        ]);

        $parentPhones = array_filter([
            $student->father_phone,
            $student->mother_phone,
            $student->guardian_phone,
        ]);

        if (!empty($parentEmails) || !empty($parentPhones)) {
            $parentUserQuery = User::withoutGlobalScope(\App\Models\Scopes\SchoolScope::class)
                ->where('school_id', $student->school_id)
                ->where(function ($q) use ($parentEmails, $parentPhones) {
                    if (!empty($parentEmails)) {
                        $q->whereIn('email', $parentEmails);
                    }
                    if (!empty($parentPhones)) {
                        $q->orWhereIn('phone', $parentPhones);
                    }
                });

            $parentUserIds = $parentUserQuery->pluck('id')->toArray();
            $targetUserIds = array_merge($targetUserIds, $parentUserIds);
        }

        // Also search for auto-generated parent emails e.g. parent...yis.com
        if ($student->father_name && $student->admission_number) {
            $cleanAdmissionId = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $student->admission_number));
            $genParentEmail = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $student->father_name)) . '.' . $cleanAdmissionId . '@parent.yis.com';

            $genParentUser = User::withoutGlobalScope(\App\Models\Scopes\SchoolScope::class)
                ->where('school_id', $student->school_id)
                ->where('email', $genParentEmail)
                ->value('id');
            if ($genParentUser) {
                $targetUserIds[] = (int) $genParentUser;
            }
        }

        $targetUserIds = array_unique(array_filter($targetUserIds));

        // Filter out non-portal users (admin, staff, teacher)
        $validUsers = User::withoutGlobalScope(\App\Models\Scopes\SchoolScope::class)
            ->whereIn('id', $targetUserIds)
            ->get()
            ->reject(function ($user) {
                return $user->hasRole('school_admin') ||
                       $user->hasRole('admin') ||
                       $user->hasRole('superadmin') ||
                       $user->hasRole('teacher') ||
                       $user->hasRole('staff') ||
                       in_array($user->role, ['school_admin', 'admin', 'teacher', 'staff']);
            })
            ->pluck('id')
            ->toArray();

        return array_values(array_unique($validUsers));
    }

    /**
     * Find active Late Fine policy for a student fee row.
     */
    private static function getFinePolicyForFee(StudentFee $fee): ?FeeFine
    {
        if ($fee->feeSchedule && $fee->feeSchedule->fine_id) {
            $fine = FeeFine::find($fee->feeSchedule->fine_id);
            if ($fine && $fine->status) {
                return $fine;
            }
        }

        return FeeFine::where('school_id', $fee->school_id)
            ->where('status', true)
            ->first();
    }

    /**
     * Format number cleanly without trailing zero decimals if integer.
     */
    private static function formatAmount(float $amount): string
    {
        if (floor($amount) == $amount) {
            return number_format($amount, 0, '.', ',');
        }
        return number_format($amount, 2, '.', ',');
    }
}
