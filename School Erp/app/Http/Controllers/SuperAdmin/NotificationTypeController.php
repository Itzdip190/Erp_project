<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;

class NotificationTypeController extends Controller
{
    protected string $filePath = 'notification_types.json';

    /**
     * Get default notification templates.
     */
    private function getDefaultSettings(): array
    {
        return [
            'attendance' => [
                'title' => 'Student Absent Alert',
                'subject' => 'Attendance Alert: {student_name} is Absent',
                'body' => "Dear Parent,\nThis is to inform you that your child {student_name} was marked absent on {date}. Please contact the school office if you wish to verify this absence.",
                'channels' => ['email', 'sms'],
            ],
            'fee_reminder' => [
                'title' => 'Fee Payment Reminder',
                'subject' => 'Fee Outstanding: ₹{due_amount} Due',
                'body' => "Dear Parent,\nThis is a friendly reminder that an amount of ₹{due_amount} is outstanding for {student_name}'s academic fees. Kindly settle the dues by {due_date} to avoid late fee penalties.",
                'channels' => ['email', 'sms'],
            ],
            'exam_publish' => [
                'title' => 'Exam Results Published',
                'subject' => 'Results Announced: {exam_name}',
                'body' => "Dear Parent,\nThe report card for {student_name} in {exam_name} has been published. Please log in to your Parent Web Portal or Mobile App to view the detailed marks sheet.",
                'channels' => ['email'],
            ],
        ];
    }

    /**
     * Display configuration dashboard.
     */
    public function index(): View
    {
        $settings = $this->getDefaultSettings();

        if (Storage::disk('local')->exists($this->filePath)) {
            $fileContent = json_decode(Storage::disk('local')->get($this->filePath), true);
            if (is_array($fileContent)) {
                $settings = array_replace_recursive($settings, $fileContent);
            }
        }

        return view('superadmin.notification-types.index', compact('settings'));
    }

    /**
     * Update notification templates in storage.
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'attendance.title' => 'required|string|max:255',
            'attendance.subject' => 'required|string|max:255',
            'attendance.body' => 'required|string|max:2000',
            'attendance.channels' => 'nullable|array',
            
            'fee_reminder.title' => 'required|string|max:255',
            'fee_reminder.subject' => 'required|string|max:255',
            'fee_reminder.body' => 'required|string|max:2000',
            'fee_reminder.channels' => 'nullable|array',
            
            'exam_publish.title' => 'required|string|max:255',
            'exam_publish.subject' => 'required|string|max:255',
            'exam_publish.body' => 'required|string|max:2000',
            'exam_publish.channels' => 'nullable|array',
        ]);

        $settings = [
            'attendance' => [
                'title' => $request->attendance['title'],
                'subject' => $request->attendance['subject'],
                'body' => $request->attendance['body'],
                'channels' => $request->attendance['channels'] ?? [],
            ],
            'fee_reminder' => [
                'title' => $request->fee_reminder['title'],
                'subject' => $request->fee_reminder['subject'],
                'body' => $request->fee_reminder['body'],
                'channels' => $request->fee_reminder['channels'] ?? [],
            ],
            'exam_publish' => [
                'title' => $request->exam_publish['title'],
                'subject' => $request->exam_publish['subject'],
                'body' => $request->exam_publish['body'],
                'channels' => $request->exam_publish['channels'] ?? [],
            ],
        ];

        Storage::disk('local')->put($this->filePath, json_encode($settings, JSON_PRETTY_PRINT));

        return redirect()->route('superadmin.notification-types.index')
            ->with('success', 'Notification templates updated successfully.');
    }
}
