<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentAttendance;
use Illuminate\Http\Request;

class ParentAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'student_id' => 'nullable|exists:students,id',
            'month' => 'nullable|integer|between:1,12',
            'year' => 'nullable|integer',
        ]);

        $user = auth()->user();
        $studentId = $request->student_id;

        if (!$studentId) {
            // Find child by guardian email or user_id
            $student = Student::where('school_id', $user->school_id)
                ->where(function ($q) use ($user) {
                    $q->where('guardian_email', $user->email)
                      ->orWhere('user_id', $user->id);
                })
                ->first();

            // Fallback for legacy support
            if (!$student) {
                $student = Student::where('school_id', $user->school_id)->first();
            }

            if (!$student) {
                abort(404, 'Student not found');
            }
            $studentId = $student->id;
        } else {
            $student = Student::findOrFail($studentId);

            // Security check: ensure student belongs to user (as guardian or student user themselves)
            if ($student->guardian_email !== $user->email && $student->user_id !== $user->id) {
                if ($student->school_id !== $user->school_id) {
                    abort(403, 'Access denied');
                }
            }
        }

        $month = (int) $request->get('month', date('m'));
        $year = (int) $request->get('year', date('Y'));

        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        $records = StudentAttendance::where('student_id', $studentId)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get()
            ->keyBy(function ($item) {
                return (int) $item->date->format('d');
            });

        $calendar = [];
        $present = 0;
        $absent = 0;
        $late = 0;
        $leave = 0;

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $record = $records->get($day);
            $status = $record ? $record->status : 'none';

            if ($status === 'present') {
                $present++;
            } elseif ($status === 'absent') {
                $absent++;
            } elseif ($status === 'late') {
                $late++;
            } elseif ($status === 'leave') {
                $leave++;
            }

            $calendar[$day] = [
                'status' => $status,
                'remark' => $record?->remark,
            ];
        }

        $totalMarked = $present + $absent + $late + $leave;
        $summary = [
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'leave' => $leave,
            'total' => $totalMarked,
            'percentage' => $totalMarked > 0 ? round((($present + $late) / $totalMarked) * 100, 1) : 0,
        ];

        $notifications = $this->getNotifications($user, $student);

        return view('parent.attendance.index', compact('calendar', 'summary', 'student', 'month', 'year', 'notifications'));
    }

    private function getNotifications($user, $student)
    {
        $notifications = collect();
        if ($student) {
            // 1. Documents
            $docs = \App\Models\StudentDocument::where('student_id', $student->id)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
            foreach ($docs as $doc) {
                $notifications->push((object)[
                    'type' => 'document',
                    'title' => 'New Document Issued',
                    'text' => $doc->original_name,
                    'time' => $doc->created_at,
                    'url' => route('parent.documents.download', ['document' => $doc->id, 'action' => 'view']),
                    'icon' => 'fas fa-file-pdf',
                    'color' => 'var(--gold)',
                    'color_bg' => 'var(--gold-bg)',
                ]);
            }

            // 2. ID Cards
            $cards = \App\Models\StudentCard::where('student_id', $student->id)
                ->with('template')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
            foreach ($cards as $card) {
                $notifications->push((object)[
                    'type' => 'card',
                    'title' => 'ID Card / Pass Issued',
                    'text' => ($card->template ? $card->template->name : 'Student ID Card') . ' (' . $card->card_number . ')',
                    'time' => $card->created_at,
                    'url' => route('parent.cards.index'),
                    'icon' => 'fas fa-id-card',
                    'color' => 'var(--blue)',
                    'color_bg' => 'rgba(59,130,246,0.15)',
                ]);
            }

            // 3. Payment Links
            $paylinks = \App\Models\PaymentLink::where('student_id', $student->id)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
            foreach ($paylinks as $pl) {
                $notifications->push((object)[
                    'type' => 'paylink',
                    'title' => 'New Payment Due',
                    'text' => $pl->purpose . ' - ₹' . number_format($pl->amount, 2),
                    'time' => $pl->created_at,
                    'url' => $pl->link_url,
                    'icon' => 'fas fa-indian-rupee-sign',
                    'color' => 'var(--red)',
                    'color_bg' => 'rgba(239,68,68,0.15)',
                ]);
            }

            // 4. Notices
            $notices = \App\Models\Notice::where('school_id', $user->school_id)
                ->whereIn('target_audience', ['all', 'students'])
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
            foreach ($notices as $notice) {
                $notifications->push((object)[
                    'type' => 'notice',
                    'title' => 'Notice: ' . $notice->title,
                    'text' => \Illuminate\Support\Str::limit($notice->content, 60),
                    'time' => $notice->created_at,
                    'url' => route('parent.notices.index'),
                    'icon' => 'fas fa-bullhorn',
                    'color' => 'var(--purple)',
                    'color_bg' => 'rgba(139,92,246,0.15)',
                ]);
            }
        }
        return $notifications->sortByDesc('time')->values()->take(10);
    }
}
