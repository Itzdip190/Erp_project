<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\SchoolAiSetting;
use App\Models\School;
use App\Models\Student;
use App\Models\Staff;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Department;
use App\Models\Designation;
use App\Models\StudentFee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Contracts\View\View;

class AiController extends Controller
{
    private function getSchoolId(): int
    {
        return Auth::user()->school_id ?? 0;
    }

    private function getSettings(): SchoolAiSetting
    {
        return SchoolAiSetting::firstOrCreate(
            ['school_id' => $this->getSchoolId()],
            [
                'enabled' => false,
                'chatbot_name' => 'AI Assistant',
                'ai_model' => 'gemini-2.0-flash',
                'ai_provider' => 'gemini',
                'max_tokens' => 1024,
            ]
        );
    }

    // ─── AI Settings Page ─────────────────────────────────────
    public function settings(): View
    {
        $aiSettings = $this->getSettings();
        return view('school.ai.settings', compact('aiSettings'));
    }

    // ─── Save AI Settings ─────────────────────────────────────
    public function saveSettings(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'chatbot_name' => 'nullable|string|max:100',
            'ai_model'     => 'nullable|string|max:100',
            'ai_provider'  => 'nullable|string|max:50',
            'max_tokens'   => 'nullable|integer|min:256|max:8192',
        ]);

        $settings = $this->getSettings();
        $settings->enabled      = $request->boolean('enabled');
        $settings->chatbot_name = $request->input('chatbot_name', 'AI Assistant');
        $settings->ai_model     = $request->input('ai_model', 'gemini-2.0-flash');
        $settings->ai_provider  = $request->input('ai_provider', 'gemini');
        $settings->max_tokens   = (int) $request->input('max_tokens', 1024);

        // Only update API key if a new one was provided (not masked)
        $newKey = $request->input('api_key', '');
        if ($newKey && !str_contains($newKey, '****')) {
            $settings->api_key = trim($newKey);
        }

        $settings->save();

        return redirect()->route('school.ai.settings')
            ->with('success', 'AI settings saved successfully!');
    }

    // ─── AI Chat Page ─────────────────────────────────────────
    public function chat(): View
    {
        $aiSettings = $this->getSettings();
        return view('school.ai.chat', compact('aiSettings'));
    }

    // ─── Send Message (AJAX) ──────────────────────────────────
    public function sendMessage(Request $request): JsonResponse
    {
        $request->validate([
            'message'  => 'required|string|max:2000',
            'history'  => 'nullable|array',
        ]);

        $settings = $this->getSettings();

        if (!$settings->enabled || !$settings->api_key) {
            return response()->json([
                'reply' => '⚠️ AI is not configured yet. Please go to **AI Settings** and add your API key to get started.'
            ]);
        }

        $message = trim($request->input('message'));
        $history = $request->input('history', []);

        $schoolContext = $this->getSchoolContext($this->getSchoolId(), $message);

        try {
            if ($settings->ai_provider === 'gemini') {
                return $this->callGemini($message, $history, $settings, $schoolContext);
            } elseif ($settings->ai_provider === 'openai') {
                return $this->callOpenAI($message, $history, $settings, $schoolContext);
            } else {
                return response()->json(['reply' => 'Unknown AI provider configured.']);
            }
        } catch (\Exception $e) {
            return response()->json([
                'reply' => '❌ Error communicating with AI: ' . $e->getMessage()
            ], 500);
        }
    }

    // ─── Gemini API Call ──────────────────────────────────────
    private function callGemini(string $message, array $history, SchoolAiSetting $settings, string $schoolContext): JsonResponse
    {
        $systemPrompt = "You are " . $settings->chatbot_name . ", an intelligent AI assistant for the school ERP system.\n"
            . "Your goal is to answer the user's questions about the school's students, staff, classes, fees, and general statistics using ONLY the provided school data context.\n\n"
            . "CRITICAL INSTRUCTIONS:\n"
            . "1. Answer ONLY based on the facts, numbers, names, and details provided in the Context section below.\n"
            . "2. If the user asks for information that is NOT in the context (or if the context is empty/insufficient), politely respond that you do not have access to that information in the school's ERP database and cannot lookup external general information. Do not make up facts or search the general internet.\n"
            . "3. Never mention the word 'context' or 'database' to the user; present the information naturally as if you know it.\n"
            . "4. Keep your replies professional, helpful, and concise.\n\n"
            . "--- CONTEXT START ---\n"
            . $schoolContext
            . "\n--- CONTEXT END ---";

        $contents = [];

        // Add chat history
        foreach ($history as $h) {
            $role = ($h['role'] ?? 'user') === 'user' ? 'user' : 'model';
            $contents[] = ['role' => $role, 'parts' => [['text' => $h['content'] ?? '']]];
        }

        // Add current message
        $contents[] = ['role' => 'user', 'parts' => [['text' => $message]]];

        $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/'
            . $settings->ai_model . ':generateContent?key=' . $settings->api_key;

        $response = Http::timeout(30)->post($apiUrl, [
            'system_instruction' => ['parts' => [['text' => $systemPrompt]]],
            'contents'           => $contents,
            'generationConfig'   => [
                'maxOutputTokens' => $settings->max_tokens,
                'temperature'     => 0.3,
            ],
        ]);

        if ($response->failed()) {
            $error = $response->json('error.message', 'Unknown error from Gemini API.');
            return response()->json(['reply' => '❌ Gemini API error: ' . $error], 500);
        }

        $reply = $response->json('candidates.0.content.parts.0.text', 'No response from AI.');
        return response()->json(['reply' => $reply]);
    }

    // ─── OpenAI API Call ──────────────────────────────────────
    private function callOpenAI(string $message, array $history, SchoolAiSetting $settings, string $schoolContext): JsonResponse
    {
        $systemPrompt = "You are " . $settings->chatbot_name . ", an intelligent AI assistant for the school ERP system.\n"
            . "Your goal is to answer the user's questions about the school's students, staff, classes, fees, and general statistics using ONLY the provided school data context.\n\n"
            . "CRITICAL INSTRUCTIONS:\n"
            . "1. Answer ONLY based on the facts, numbers, names, and details provided in the Context section below.\n"
            . "2. If the user asks for information that is NOT in the context (or if the context is empty/insufficient), politely respond that you do not have access to that information in the school's ERP database and cannot lookup external general information. Do not make up facts or search the general internet.\n"
            . "3. Never mention the word 'context' or 'database' to the user; present the information naturally as if you know it.\n"
            . "4. Keep your replies professional, helpful, and concise.\n\n"
            . "--- CONTEXT START ---\n"
            . $schoolContext
            . "\n--- CONTEXT END ---";

        $messages = [['role' => 'system', 'content' => $systemPrompt]];

        foreach ($history as $h) {
            $messages[] = ['role' => $h['role'] ?? 'user', 'content' => $h['content'] ?? ''];
        }
        $messages[] = ['role' => 'user', 'content' => $message];

        $response = Http::withToken($settings->api_key)->timeout(30)->post(
            'https://api.openai.com/v1/chat/completions',
            [
                'model'      => $settings->ai_model,
                'messages'   => $messages,
                'max_tokens' => $settings->max_tokens,
                'temperature' => 0.3,
            ]
        );

        if ($response->failed()) {
            $error = $response->json('error.message', 'Unknown error from OpenAI.');
            return response()->json(['reply' => '❌ OpenAI API error: ' . $error], 500);
        }

        $reply = $response->json('choices.0.message.content', 'No response from AI.');
        return response()->json(['reply' => $reply]);
    }

    // ─── Context Builder ──────────────────────────────────────
    private function getSchoolContext(int $schoolId, string $query): string
    {
        $school = School::where('id', $schoolId)->first();
        if (!$school) {
            return "No school information found.";
        }

        $context = "=== SCHOOL INFORMATION ===\n";
        $context .= "School Name: " . $school->name . "\n";
        $context .= "School Code: " . $school->code . "\n";
        if ($school->address) $context .= "Address: " . $school->address . "\n";
        if ($school->phone) $context .= "Phone: " . $school->phone . "\n";
        $context .= "\n";

        // Statistics - explicitly query by school_id
        $totalStudents = Student::where('school_id', $schoolId)->count();
        $activeStudents = Student::where('school_id', $schoolId)->where('is_active', true)->count();
        $inactiveStudents = $totalStudents - $activeStudents;

        $totalClasses = SchoolClass::where('school_id', $schoolId)->count();
        $totalSections = Section::where('school_id', $schoolId)->count();

        $totalStaff = Staff::where('school_id', $schoolId)->count();
        $activeStaff = Staff::where('school_id', $schoolId)->where('is_active', true)->count();
        
        $teachingStaff = 0;
        $activeStaffList = Staff::where('school_id', $schoolId)
            ->where('is_active', true)
            ->with(['user', 'designation', 'department'])
            ->get();
        foreach ($activeStaffList as $s) {
            if ($s->staff_type === 'Teaching') {
                $teachingStaff++;
            }
        }
        $nonTeachingStaff = $activeStaff - $teachingStaff;

        $context .= "=== ERP GENERAL STATISTICS ===\n";
        $context .= "Total Enrolled Students: $totalStudents ($activeStudents active, $inactiveStudents inactive)\n";
        $context .= "Total Staff Members: $totalStaff ($activeStaff active: $teachingStaff teaching staff, $nonTeachingStaff non-teaching/support staff)\n";
        $context .= "Total Classes: $totalClasses\n";
        $context .= "Total Sections: $totalSections\n";

        // Fees summary - scoped to school_id
        $totalFeesInvoiced = StudentFee::where('school_id', $schoolId)->sum('amount');
        $totalFeesPaid = StudentFee::where('school_id', $schoolId)->sum('paid_amount');
        $totalFeesDue = $totalFeesInvoiced - $totalFeesPaid;
        $context .= "Total Fees Invoiced (All Time): " . number_format($totalFeesInvoiced, 2) . "\n";
        $context .= "Total Fees Paid (All Time): " . number_format($totalFeesPaid, 2) . "\n";
        $context .= "Total Fees Outstanding/Due: " . number_format($totalFeesDue, 2) . "\n";
        $context .= "\n";

        // Classes & Sections list
        $context .= "=== CLASSES, SECTIONS & ENROLLMENT ===\n";
        $classes = SchoolClass::where('school_id', $schoolId)
            ->with(['sections' => function($q) use ($schoolId) {
                $q->where('school_id', $schoolId)->orderBy('sort_order')->orderBy('name');
            }, 'sections.classTeacher' => function($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            }])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        foreach ($classes as $class) {
            $sectionsInfo = [];
            foreach ($class->sections as $sec) {
                $teacherName = $sec->classTeacher ? $sec->classTeacher->full_name : 'None';
                $studentCount = Student::where('school_id', $schoolId)->where('section_id', $sec->id)->count();
                $sectionsInfo[] = "Section {$sec->name} (Class Teacher: {$teacherName}, Enrolled Students: {$studentCount})";
            }
            $context .= "- Class {$class->name}: " . (empty($sectionsInfo) ? "No sections" : implode('; ', $sectionsInfo)) . "\n";
        }
        $context .= "\n";

        // Search for relevant students/staff matching search query terms
        $terms = preg_split('/[\s,\.\?\!\:\(\)\-\_]+/', strtolower($query));
        $searchTerms = [];
        $stopWords = [
            'who', 'what', 'where', 'how', 'when', 'why', 'class', 'student', 'teacher', 
            'fees', 'attendance', 'school', 'the', 'and', 'show', 'list', 'name', 'total', 
            'active', 'number', 'find', 'search', 'info', 'details', 'about', 'get', 'give',
            'please', 'tell', 'help', 'with', 'from', 'this', 'that', 'there', 'their'
        ];
        
        foreach ($terms as $term) {
            $term = trim($term);
            if (strlen($term) >= 3 && !in_array($term, $stopWords)) {
                $searchTerms[] = $term;
            }
        }

        if (!empty($searchTerms)) {
            $foundStudents = collect();
            $foundStaff = collect();

            foreach ($searchTerms as $word) {
                // Search Students scoped by school_id
                $students = Student::where('school_id', $schoolId)
                    ->where(function($q) use ($word) {
                        $q->where('first_name', 'like', "%{$word}%")
                          ->orWhere('last_name', 'like', "%{$word}%")
                          ->orWhere('admission_number', 'like', "%{$word}%")
                          ->orWhere('roll_number', 'like', "%{$word}%");
                    })
                    ->get();
                $foundStudents = $foundStudents->merge($students);

                // Search Staff scoped by school_id
                $staff = Staff::where('school_id', $schoolId)
                    ->where(function($q) use ($word) {
                        $q->where('first_name', 'like', "%{$word}%")
                          ->orWhere('last_name', 'like', "%{$word}%")
                          ->orWhere('employee_id', 'like', "%{$word}%");
                    })
                    ->get();
                $foundStaff = $foundStaff->merge($staff);
            }

            $foundStudents = $foundStudents->unique('id')->take(8);
            $foundStaff = $foundStaff->unique('id')->take(8);

            if ($foundStudents->isNotEmpty()) {
                $context .= "=== MATCHING STUDENT SEARCH RESULTS ===\n";
                foreach ($foundStudents as $student) {
                    $className = $student->class_id ? (SchoolClass::where('school_id', $schoolId)->where('id', $student->class_id)->first()->name ?? 'Unknown') : 'N/A';
                    $sectionName = $student->section_id ? (Section::where('school_id', $schoolId)->where('id', $student->section_id)->first()->name ?? 'Unknown') : 'N/A';
                    
                    // Fee status for student
                    $stdFeeTotal = StudentFee::where('school_id', $schoolId)->where('student_id', $student->id)->sum('amount');
                    $stdFeePaid = StudentFee::where('school_id', $schoolId)->where('student_id', $student->id)->sum('paid_amount');
                    $stdFeeDue = $stdFeeTotal - $stdFeePaid;

                    $context .= "- Student: {$student->first_name} {$student->last_name}\n";
                    $context .= "  * Admission No: {$student->admission_number}\n";
                    $context .= "  * Roll No: {$student->roll_number}\n";
                    $context .= "  * Class & Section: {$className} - {$sectionName}\n";
                    $context .= "  * Enrollment Status: " . ($student->is_active ? 'Active' : 'Inactive') . "\n";
                    $context .= "  * Gender: {$student->gender}\n";
                    $context .= "  * Date of Birth: " . ($student->date_of_birth ? Carbon::parse($student->date_of_birth)->format('Y-m-d') : 'N/A') . "\n";
                    if ($student->phone) $context .= "  * Phone: {$student->phone}\n";
                    if ($student->email) $context .= "  * Email: {$student->email}\n";
                    if ($student->father_name) $context .= "  * Father's Name: {$student->father_name} (Phone: {$student->father_phone})\n";
                    if ($student->mother_name) $context .= "  * Mother's Name: {$student->mother_name} (Phone: {$student->mother_phone})\n";
                    $context .= "  * Fees: Total: " . number_format($stdFeeTotal, 2) . ", Paid: " . number_format($stdFeePaid, 2) . ", Due: " . number_format($stdFeeDue, 2) . "\n";
                }
                $context .= "\n";
            }

            if ($foundStaff->isNotEmpty()) {
                $context .= "=== MATCHING STAFF/TEACHER SEARCH RESULTS ===\n";
                foreach ($foundStaff as $member) {
                    $deptName = $member->department_id ? (Department::where('school_id', $schoolId)->where('id', $member->department_id)->first()->name ?? 'N/A') : 'N/A';
                    $desgName = $member->designation_id ? (Designation::where('school_id', $schoolId)->where('id', $member->designation_id)->first()->name ?? 'N/A') : 'N/A';
                    $context .= "- Staff Member: {$member->first_name} {$member->last_name}\n";
                    $context .= "  * Employee ID: {$member->employee_id}\n";
                    $context .= "  * Role Type: {$member->staff_type}\n";
                    $context .= "  * Designation: {$desgName}\n";
                    $context .= "  * Department: {$deptName}\n";
                    $context .= "  * Employment Status: " . ($member->is_active ? 'Active' : 'Inactive') . "\n";
                    if ($member->phone) $context .= "  * Phone: {$member->phone}\n";
                    if ($member->email) $context .= "  * Email: {$member->email}\n";
                    if ($member->joining_date) $context .= "  * Joining Date: " . Carbon::parse($member->joining_date)->format('Y-m-d') . "\n";
                }
                $context .= "\n";
            }
        }

        return $context;
    }
}
