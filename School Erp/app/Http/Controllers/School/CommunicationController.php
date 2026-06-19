<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notice;
use App\Models\Survey;
use App\Models\SurveyOption;
use App\Models\ChatMessage;
use App\Models\User;

class CommunicationController extends Controller
{
    private function ensureCommunicationSeeded($schoolId)
    {
        if (Notice::where('school_id', $schoolId)->count() === 0) {
            Notice::create([
                'school_id' => $schoolId,
                'title' => 'Annual Sports Meet 2026',
                'content' => 'The Annual Sports Meet is scheduled to take place next month from the 10th to the 12th. All students are encouraged to participate.',
                'target_audience' => 'all',
            ]);
            Notice::create([
                'school_id' => $schoolId,
                'title' => 'Mid-term Exams Notice',
                'content' => 'Please note that the mid-term examinations syllabus and date sheets have been published. Check the examination section for details.',
                'target_audience' => 'students',
            ]);
        }

        if (Survey::where('school_id', $schoolId)->count() === 0) {
            $survey = Survey::create([
                'school_id' => $schoolId,
                'question' => 'Which extra-curricular activity do you prefer for summer camp?',
                'is_active' => true,
            ]);

            SurveyOption::create(['survey_id' => $survey->id, 'option_text' => 'Robot Building & Coding']);
            SurveyOption::create(['survey_id' => $survey->id, 'option_text' => 'Lawn Tennis & Swimming']);
            SurveyOption::create(['survey_id' => $survey->id, 'option_text' => 'Drama & Creative Arts']);
        }
    }

    public function settings(Request $request)
    {
        if ($request->isMethod('post')) {
            return back()->with('success', 'Notification configurations updated successfully.');
        }
        return view('school.communication.settings');
    }

    public function notice(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $this->ensureCommunicationSeeded($schoolId);

        if ($request->isMethod('post')) {
            $request->validate([
                'title' => 'required|string|max:150',
                'content' => 'required|string',
                'target_audience' => 'required|string',
            ]);

            Notice::create([
                'school_id' => $schoolId,
                'title' => $request->title,
                'content' => $request->content,
                'target_audience' => $request->target_audience,
            ]);

            return back()->with('success', 'Notice bulletin published successfully!');
        }

        $notices = Notice::where('school_id', $schoolId)->orderBy('created_at', 'desc')->get();
        return view('school.communication.notice', compact('notices'));
    }

    public function survey(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $this->ensureCommunicationSeeded($schoolId);

        if ($request->isMethod('post')) {
            $request->validate([
                'question' => 'required|string|max:200',
                'options' => 'required|array|min:2',
                'options.*' => 'required|string|max:100',
            ]);

            $survey = Survey::create([
                'school_id' => $schoolId,
                'question' => $request->question,
                'is_active' => true,
            ]);

            foreach ($request->options as $optText) {
                SurveyOption::create([
                    'survey_id' => $survey->id,
                    'option_text' => $optText,
                ]);
            }

            return back()->with('success', 'New opinion poll survey created successfully!');
        }

        $surveys = Survey::where('school_id', $schoolId)->with(['options.responses', 'responses'])->orderBy('created_at', 'desc')->get();
        return view('school.communication.survey', compact('surveys'));
    }

    public function sms(Request $request)
    {
        if ($request->isMethod('post')) {
            return back()->with('success', 'SMS broadcast job initiated successfully.');
        }
        return view('school.communication.sms');
    }

    public function smsTemplate(Request $request)
    {
        if ($request->isMethod('post')) {
            return back()->with('success', 'SMS Template created successfully.');
        }
        return view('school.communication.sms_template');
    }

    public function whatsapp(Request $request)
    {
        if ($request->isMethod('post')) {
            return back()->with('success', 'WhatsApp campaign scheduled successfully.');
        }
        return view('school.communication.whatsapp');
    }

    public function email(Request $request)
    {
        if ($request->isMethod('post')) {
            return back()->with('success', 'Email broadcast queue dispatched successfully.');
        }
        return view('school.communication.email');
    }

    public function chat(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $users = User::where('school_id', $schoolId)->where('id', '!=', auth()->id())->get();

        $selectedUserId = $request->get('user_id');
        $messages = collect();

        if ($selectedUserId) {
            $messages = ChatMessage::where('school_id', $schoolId)
                ->where(function($q) use ($selectedUserId) {
                    $q->where('sender_id', auth()->id())->where('receiver_id', $selectedUserId);
                })
                ->orWhere(function($q) use ($selectedUserId) {
                    $q->where('sender_id', $selectedUserId)->where('receiver_id', auth()->id());
                })
                ->orderBy('created_at', 'asc')
                ->get();
        }

        if ($request->isMethod('post')) {
            $request->validate([
                'receiver_id' => 'required|exists:users,id',
                'message' => 'required|string',
            ]);

            ChatMessage::create([
                'school_id' => $schoolId,
                'sender_id' => auth()->id(),
                'receiver_id' => $request->receiver_id,
                'message' => $request->message,
            ]);

            return redirect()->route('school.communication.chat', ['user_id' => $request->receiver_id])->with('success', 'Message sent.');
        }

        return view('school.communication.chat', compact('users', 'messages', 'selectedUserId'));
    }
}
