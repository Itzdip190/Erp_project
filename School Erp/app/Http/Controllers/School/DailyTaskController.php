<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\DailyTaskEvaluation;
use App\Models\DailyTaskHead;
use App\Models\DailyTaskQuestion;
use App\Models\DailyTaskReview;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\SectionSubjectStaff;
use App\Models\Staff;
use App\Models\Student;
use App\Models\Subject;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DailyTaskController extends Controller
{
    /**
     * Helper to get current school ID.
     */
    private function schoolId(): int
    {
        return (int) (Auth::user()->school_id ?? 0);
    }

    /**
     * Helper to resolve teacher context (is logged in user a teacher, and what are their assigned classes/subjects).
     */
    private function getTeacherContext(): array
    {
        $user = Auth::user();
        $schoolId = $this->schoolId();

        $isTeacher = $user && ($user->hasRole('teacher') || $user->role === 'teacher' || ($user->hasRole('staff') && !$user->hasRole('school_admin') && !$user->hasRole('superadmin')));
        
        $staff = null;
        if ($user) {
            $staff = $user->staff 
                ?? Staff::where('user_id', $user->id)->first()
                ?? Staff::where('school_id', $schoolId)->where('email', $user->email)->first()
                ?? Staff::where('school_id', $schoolId)->where('phone', $user->phone)->first();
        }

        $classTeacherSectionIds = [];
        $subjectTeacherSectionSubjectMap = []; // [ section_id => [ subject_id, ... ] ]
        $allAssignedSectionIds = [];
        $allAssignedClassIds = [];

        if ($staff) {
            // 1. Where staff is Class Teacher in sections table
            $classTeacherSectionIds = Section::where('school_id', $schoolId)
                ->where(function ($q) use ($staff, $user) {
                    $q->where('class_teacher_id', $staff->id)
                      ->orWhere('assistant_class_teacher_id', $staff->id)
                      ->orWhere('class_teacher_id', $user->id)
                      ->orWhere('assistant_class_teacher_id', $user->id);
                })
                ->pluck('id')
                ->toArray();

            // 2. Where staff is assigned in class_subject_teacher
            if (\Illuminate\Support\Facades\Schema::hasTable('class_subject_teacher')) {
                $cstRows = \Illuminate\Support\Facades\DB::table('class_subject_teacher')
                    ->where('school_id', $schoolId)
                    ->where(function ($q) use ($staff, $user) {
                        $q->where('teacher_id', $staff->id)
                          ->orWhere('teacher_id', $user->id);
                    })
                    ->get();

                foreach ($cstRows as $cst) {
                    if (!empty($cst->section_id)) {
                        $subjectTeacherSectionSubjectMap[$cst->section_id][] = $cst->subject_id;
                        $classTeacherSectionIds[] = $cst->section_id;
                    }
                    if (!empty($cst->class_id)) {
                        $allAssignedClassIds[] = $cst->class_id;
                    }
                }
            }

            // 3. Where staff is Subject Teacher via section_subject_staff
            if (\Illuminate\Support\Facades\Schema::hasTable('section_subject_staff')) {
                $sssRecords = SectionSubjectStaff::where('school_id', $schoolId)
                    ->where(function ($q) use ($staff, $user) {
                        $q->where('staff_id', $staff->id)
                          ->orWhere('substitute_staff_id', $staff->id)
                          ->orWhere('staff_id', $user->id);
                    })
                    ->get();

                foreach ($sssRecords as $sss) {
                    $subjectTeacherSectionSubjectMap[$sss->section_id][] = $sss->subject_id;
                }
            }

            // 4. ClassTimetableCell
            if (class_exists(\App\Models\ClassTimetableCell::class)) {
                $timetableCells = \App\Models\ClassTimetableCell::where('school_id', $schoolId)
                    ->where(function ($q) use ($staff, $user) {
                        $q->where('teacher_id', $staff->id)
                          ->orWhere('teacher_id', $user->id);
                    })
                    ->get();

                foreach ($timetableCells as $cell) {
                    if ($cell->section_id && $cell->subject_id) {
                        $subjectTeacherSectionSubjectMap[$cell->section_id][] = $cell->subject_id;
                    }
                }
            }

            $subjectTeacherSectionIds = array_keys($subjectTeacherSectionSubjectMap);
            $allAssignedSectionIds = array_unique(array_filter(array_merge($classTeacherSectionIds, $subjectTeacherSectionIds)));
            if (!empty($allAssignedSectionIds)) {
                $secClassIds = Section::where('school_id', $schoolId)
                    ->whereIn('id', $allAssignedSectionIds)
                    ->pluck('class_id')
                    ->unique()
                    ->toArray();
                $allAssignedClassIds = array_unique(array_filter(array_merge($allAssignedClassIds, $secClassIds)));
            }
        }

        return [
            'isTeacher' => (bool) $staff,
            'staff' => $staff,
            'classTeacherSectionIds' => array_unique($classTeacherSectionIds),
            'subjectTeacherSectionSubjectMap' => $subjectTeacherSectionSubjectMap,
            'allAssignedSectionIds' => $allAssignedSectionIds,
            'allAssignedClassIds' => $allAssignedClassIds,
        ];
    }

    // =========================================================================
    // PAGE 1: DAILY TASK HEADS
    // =========================================================================

    public function heads(Request $request)
    {
        $schoolId = $this->schoolId();
        $search = trim($request->get('search', ''));
        $status = $request->get('status', 'all');

        $query = DailyTaskHead::where('school_id', $schoolId)->withCount('questions');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }

        $heads = $query->orderBy('sort_order')->orderBy('name')->paginate(15)->withQueryString();

        $stats = [
            'total' => DailyTaskHead::where('school_id', $schoolId)->count(),
            'active' => DailyTaskHead::where('school_id', $schoolId)->where('is_active', true)->count(),
            'total_questions' => DailyTaskQuestion::where('school_id', $schoolId)->count(),
        ];

        return view('school.daily_tasks.heads', compact('heads', 'stats', 'search', 'status'));
    }

    public function storeHead(Request $request)
    {
        $schoolId = $this->schoolId();

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:500',
            'color' => 'nullable|string|max:30',
            'icon' => 'nullable|string|max:60',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['school_id'] = $schoolId;
        $validated['color'] = $validated['color'] ?? '#4f46e5';
        $validated['icon'] = $validated['icon'] ?? 'fa-tasks';
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['is_active'] = $request->has('is_active') ? (bool)$request->is_active : true;

        DailyTaskHead::create($validated);

        return redirect()->route('school.daily-tasks.heads')->with('success', 'Daily Task Head created successfully!');
    }

    public function updateHead(Request $request, $id)
    {
        $schoolId = $this->schoolId();
        $head = DailyTaskHead::where('school_id', $schoolId)->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:500',
            'color' => 'nullable|string|max:30',
            'icon' => 'nullable|string|max:60',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? (bool)$request->is_active : false;

        $head->update($validated);

        return redirect()->route('school.daily-tasks.heads')->with('success', 'Daily Task Head updated successfully!');
    }

    public function deleteHead(Request $request, $id)
    {
        $schoolId = $this->schoolId();
        $head = DailyTaskHead::where('school_id', $schoolId)->findOrFail($id);

        $questionsCount = $head->questions()->count();
        if ($questionsCount > 0) {
            return redirect()->route('school.daily-tasks.heads')->with('error', "Cannot delete this Task Head as it has {$questionsCount} question(s) attached. Please reassign or delete the questions first.");
        }

        $head->delete();

        return redirect()->route('school.daily-tasks.heads')->with('success', 'Daily Task Head deleted successfully!');
    }

    public function toggleHead(Request $request, $id)
    {
        $schoolId = $this->schoolId();
        $head = DailyTaskHead::where('school_id', $schoolId)->findOrFail($id);

        $head->is_active = !$head->is_active;
        $head->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'is_active' => $head->is_active,
                'message' => 'Status updated successfully.',
            ]);
        }

        return redirect()->back()->with('success', 'Task Head status updated successfully!');
    }

    // =========================================================================
    // PAGE 2: ADD QUESTION & CLASS (Class & Subject Wise Setup)
    // =========================================================================

    public function questions(Request $request)
    {
        $schoolId = $this->schoolId();
        $classId = $request->get('class_id');
        $headId = $request->get('head_id');
        $targetRole = $request->get('target_role', 'all');
        $search = trim($request->get('search', ''));

        $query = DailyTaskQuestion::where('school_id', $schoolId)
            ->with(['head', 'schoolClass', 'section', 'subject']);

        if ($classId && $classId !== 'all') {
            if ($classId === 'global') {
                $query->whereNull('class_id');
            } else {
                $query->where('class_id', $classId);
            }
        }

        if ($headId && $headId !== 'all') {
            $query->where('daily_task_head_id', $headId);
        }

        if ($targetRole && $targetRole !== 'all') {
            $query->where('target_role', $targetRole);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('question', 'like', "%{$search}%");
            });
        }

        $questions = $query->orderBy('sort_order')->orderBy('id', 'desc')->paginate(20)->withQueryString();

        $classes = SchoolClass::where('school_id', $schoolId)->with('sections')->get();
        $heads = DailyTaskHead::where('school_id', $schoolId)->where('is_active', true)->orderBy('sort_order')->get();
        $subjects = Subject::where('school_id', $schoolId)->orderBy('name')->get();

        $stats = [
            'total' => DailyTaskQuestion::where('school_id', $schoolId)->count(),
            'class_teacher' => DailyTaskQuestion::where('school_id', $schoolId)->whereIn('target_role', ['class_teacher', 'both'])->count(),
            'subject_teacher' => DailyTaskQuestion::where('school_id', $schoolId)->whereIn('target_role', ['subject_teacher', 'both'])->count(),
            'active' => DailyTaskQuestion::where('school_id', $schoolId)->where('is_active', true)->count(),
        ];

        return view('school.daily_tasks.questions', compact('questions', 'classes', 'heads', 'subjects', 'stats', 'classId', 'headId', 'targetRole', 'search'));
    }

    public function storeQuestion(Request $request)
    {
        $schoolId = $this->schoolId();

        $validated = $request->validate([
            'question' => 'required|string|max:500',
            'daily_task_head_id' => 'nullable|exists:daily_task_heads,id',
            'class_id' => 'nullable|exists:school_classes,id',
            'section_id' => 'nullable|exists:sections,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'target_role' => 'required|in:both,class_teacher,subject_teacher',
            'evaluation_type' => 'required|in:rating,score,options,boolean,remark',
            'max_score' => 'nullable|integer|min:1|max:100',
            'is_mandatory' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['school_id'] = $schoolId;
        $validated['created_by'] = Auth::id();
        $validated['max_score'] = $validated['max_score'] ?? ($validated['evaluation_type'] === 'rating' ? 5 : 10);
        $validated['is_mandatory'] = $request->has('is_mandatory') ? (bool)$request->is_mandatory : false;
        $validated['is_active'] = $request->has('is_active') ? (bool)$request->is_active : true;
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        DailyTaskQuestion::create($validated);

        return redirect()->route('school.daily-tasks.questions')->with('success', 'Daily Task Question created successfully!');
    }

    public function updateQuestion(Request $request, $id)
    {
        $schoolId = $this->schoolId();
        $question = DailyTaskQuestion::where('school_id', $schoolId)->findOrFail($id);

        $validated = $request->validate([
            'question' => 'required|string|max:500',
            'daily_task_head_id' => 'nullable|exists:daily_task_heads,id',
            'class_id' => 'nullable|exists:school_classes,id',
            'section_id' => 'nullable|exists:sections,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'target_role' => 'required|in:both,class_teacher,subject_teacher',
            'evaluation_type' => 'required|in:rating,score,options,boolean,remark',
            'max_score' => 'nullable|integer|min:1|max:100',
            'is_mandatory' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_mandatory'] = $request->has('is_mandatory') ? (bool)$request->is_mandatory : false;
        $validated['is_active'] = $request->has('is_active') ? (bool)$request->is_active : false;

        $question->update($validated);

        return redirect()->route('school.daily-tasks.questions')->with('success', 'Daily Task Question updated successfully!');
    }

    public function deleteQuestion(Request $request, $id)
    {
        $schoolId = $this->schoolId();
        $question = DailyTaskQuestion::where('school_id', $schoolId)->findOrFail($id);

        $question->delete();

        return redirect()->route('school.daily-tasks.questions')->with('success', 'Daily Task Question deleted successfully!');
    }

    public function toggleQuestion(Request $request, $id)
    {
        $schoolId = $this->schoolId();
        $question = DailyTaskQuestion::where('school_id', $schoolId)->findOrFail($id);

        $question->is_active = !$question->is_active;
        $question->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'is_active' => $question->is_active,
                'message' => 'Status updated successfully.',
            ]);
        }

        return redirect()->back()->with('success', 'Question status updated successfully!');
    }

    public function fetchQuestionsByContext(Request $request)
    {
        $schoolId = $this->schoolId();
        $classId = $request->get('class_id');
        $sectionId = $request->get('section_id');
        $subjectId = $request->get('subject_id');
        $reviewType = $request->get('review_type', 'class_teacher'); // 'class_teacher' or 'subject_teacher'

        $questions = $this->getApplicableQuestions($schoolId, $classId, $sectionId, $subjectId, $reviewType);

        return response()->json([
            'success' => true,
            'questions' => $questions,
        ]);
    }

    /**
     * Helper to get applicable questions based on context.
     */
    private function getApplicableQuestions($schoolId, $classId, $sectionId, $subjectId, $reviewType)
    {
        $query = DailyTaskQuestion::where('school_id', $schoolId)
            ->where('is_active', true)
            ->with('head');

        // Filter by class (either assigned specifically to this class OR global/null class)
        if ($classId) {
            $query->where(function ($q) use ($classId) {
                $q->where('class_id', $classId)
                  ->orWhereNull('class_id')
                  ->orWhere('class_id', 0);
            });
        }

        // Filter by section (either specific section OR null/all sections)
        if ($sectionId) {
            $query->where(function ($q) use ($sectionId) {
                $q->where('section_id', $sectionId)
                  ->orWhereNull('section_id')
                  ->orWhere('section_id', 0);
            });
        }

        // Filter by target role & subject
        if ($reviewType === 'subject_teacher') {
            // Subject teacher review -> questions marked for subject_teacher or both
            $query->whereIn('target_role', ['subject_teacher', 'both']);
            if ($subjectId) {
                $query->where(function ($q) use ($subjectId) {
                    $q->where('subject_id', $subjectId)
                      ->orWhereNull('subject_id')
                      ->orWhere('subject_id', 0);
                });
            }
        } else {
            // Class teacher review -> questions marked for class_teacher or both
            $query->whereIn('target_role', ['class_teacher', 'both']);
        }

        $questions = $query->orderBy('sort_order')->orderBy('id')->get();

        // Fallback: If no questions found for specific class/section filter, load all active questions for school
        if ($questions->isEmpty()) {
            $fallbackQuery = DailyTaskQuestion::where('school_id', $schoolId)
                ->where('is_active', true)
                ->with('head');
            if ($reviewType === 'subject_teacher') {
                $fallbackQuery->whereIn('target_role', ['subject_teacher', 'both']);
            } else {
                $fallbackQuery->whereIn('target_role', ['class_teacher', 'both']);
            }
            $questions = $fallbackQuery->orderBy('sort_order')->orderBy('id')->get();
        }

        return $questions;
    }

    // =========================================================================
    // PAGE 3: TEACHER REVIEW & STUDENT REPORT
    // =========================================================================

    public function reviewIndex(Request $request)
    {
        $schoolId = $this->schoolId();
        $teacherContext = $this->getTeacherContext();

        $isTeacher = $teacherContext['isTeacher'];
        $assignedClassIds = $teacherContext['allAssignedClassIds'];
        $assignedSectionIds = $teacherContext['allAssignedSectionIds'];
        $classTeacherSectionIds = $teacherContext['classTeacherSectionIds'];
        $subjectTeacherMap = $teacherContext['subjectTeacherSectionSubjectMap'];

        // Retrieve available classes
        $classesQuery = SchoolClass::where('school_id', $schoolId)->with('sections');
        if ($isTeacher && !empty($assignedClassIds)) {
            $classesQuery->whereIn('id', $assignedClassIds);
        }
        $classes = $classesQuery->get();

        // If teacher has no specific class restrictions resolved, show all classes
        if ($classes->isEmpty()) {
            $classes = SchoolClass::where('school_id', $schoolId)->with('sections')->get();
        }

        // Filter sections if teacher has specific assigned sections
        if ($isTeacher && !empty($assignedSectionIds)) {
            $classes->each(function ($cls) use ($assignedSectionIds) {
                $cls->setRelation('sections', $cls->sections->whereIn('id', $assignedSectionIds)->values());
            });
        }

        $selectedDate = $request->get('date', date('Y-m-d'));
        $selectedClassId = $request->get('class_id');
        $selectedSectionId = $request->get('section_id');
        $selectedReviewType = $request->get('review_type', 'class_teacher');
        $selectedSubjectId = $request->get('subject_id');

        // All subjects for dropdown
        $allSubjects = Subject::where('school_id', $schoolId)->orderBy('name')->get();

        return view('school.daily_tasks.review', compact(
            'classes',
            'allSubjects',
            'isTeacher',
            'teacherContext',
            'classTeacherSectionIds',
            'subjectTeacherMap',
            'selectedDate',
            'selectedClassId',
            'selectedSectionId',
            'selectedReviewType',
            'selectedSubjectId'
        ));
    }

    public function loadStudentsForReview(Request $request)
    {
        try {
            $schoolId = $this->schoolId();
            $date = $request->get('date', date('Y-m-d'));
            $classId = (int) $request->get('class_id');
            $sectionId = (int) $request->get('section_id');
            $reviewType = $request->get('review_type', 'class_teacher');
            $subjectId = $request->get('subject_id') ? (int) $request->get('subject_id') : null;

            if (!$classId || !$sectionId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please select both Class and Section.',
                ]);
            }

            if ($reviewType === 'subject_teacher' && !$subjectId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please select a Subject for Subject Teacher review.',
                ]);
            }

            // 1. Get Questions
            $questions = $this->getApplicableQuestions($schoolId, $classId, $sectionId, $subjectId, $reviewType);

            if ($questions->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active Daily Task questions found. Please add questions in the "Add Question & Class" section first.',
                ]);
            }

            // 2. Resolve Active Academic Session (using is_current column)
            $activeSession = \App\Models\AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first()
                ?? \App\Models\AcademicSession::where('school_id', $schoolId)->latest('id')->first();
            $sessionId = $activeSession?->id;

            // Query students assigned to this class and section in current academic session
            $studentsQuery = Student::where('school_id', $schoolId);

            if (method_exists(Student::class, 'scopeActiveStudents')) {
                $studentsQuery->activeStudents();
            } else {
                $studentsQuery->where(function($q) {
                    $q->where('is_active', 1)->orWhereNull('is_active');
                });
            }

            if ($sessionId && method_exists(Student::class, 'scopeInAcademicSession')) {
                $studentsQuery->inAcademicSession((int)$sessionId, $classId, $sectionId)
                    ->with(['studentSessions' => fn($q) => $q->where('academic_session_id', $sessionId)]);
            } else {
                if ($classId) $studentsQuery->where('class_id', $classId);
                if ($sectionId) $studentsQuery->where('section_id', $sectionId);
            }

            $students = $studentsQuery->get();

            // Fallback if inAcademicSession yielded 0 results
            if ($students->isEmpty()) {
                $fallbackQuery = Student::where('school_id', $schoolId)
                    ->where(function($q) {
                        $q->where('is_active', 1)->orWhereNull('is_active');
                    });
                if ($classId) $fallbackQuery->where('class_id', $classId);
                if ($sectionId) $fallbackQuery->where('section_id', $sectionId);
                $students = $fallbackQuery->get();
            }

            if ($students->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active students found in the selected class and section.',
                ]);
            }

            // Sort students by session roll_number or student roll_number
            $students = $students->sortBy(function($st) {
                $sess = $st->relationLoaded('studentSessions') ? $st->studentSessions->first() : null;
                $roll = $sess?->roll_number ?? $st->roll_number ?? '999999';
                return is_numeric($roll) ? (int)$roll : $roll;
            })->values();

            $mappedStudents = $students->map(function ($st) {
                $sess = $st->relationLoaded('studentSessions') ? $st->studentSessions->first() : null;
                $rawPhoto = $sess?->photo ?? $st->photo;
                $admNo = $sess?->admission_number ?? $st->admission_number;
                $photoUrl = $this->formatPhotoUrl($rawPhoto, $st->id, $admNo);

                return [
                    'id' => $st->id,
                    'first_name' => $sess?->first_name ?? $st->first_name,
                    'last_name' => $sess?->last_name ?? $st->last_name,
                    'roll_no' => $sess?->roll_number ?? $st->roll_number,
                    'admission_no' => $admNo,
                    'gender' => $sess?->gender ?? $st->gender,
                    'photo' => $photoUrl,
                ];
            });

            // 3. Check for existing review on this date
            $existingReviewQuery = DailyTaskReview::where('school_id', $schoolId)
                ->where('date', $date)
                ->where('class_id', $classId)
                ->where('section_id', $sectionId)
                ->where('review_type', $reviewType);

            if ($reviewType === 'subject_teacher' && $subjectId) {
                $existingReviewQuery->where('subject_id', $subjectId);
            }

            $existingReview = $existingReviewQuery->first();
            $evaluationsMap = []; // [ student_id => [ question_id => [ 'rating' => ..., 'score' => ..., 'status_option' => ..., 'remarks' => ... ] ] ]

            if ($existingReview) {
                $evaluations = DailyTaskEvaluation::where('daily_task_review_id', $existingReview->id)->get();
                foreach ($evaluations as $ev) {
                    $evaluationsMap[$ev->student_id][$ev->daily_task_question_id] = [
                        'rating' => $ev->rating,
                        'score' => $ev->score,
                        'status_option' => $ev->status_option,
                        'remarks' => $ev->remarks,
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'questions' => $questions,
                'students' => $mappedStudents,
                'existing_review' => $existingReview,
                'evaluations' => $evaluationsMap,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load evaluation sheet: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function saveReview(Request $request)
    {
        $schoolId = $this->schoolId();
        $user = Auth::user();

        $request->validate([
            'date' => 'required|date',
            'class_id' => 'required|exists:school_classes,id',
            'section_id' => 'required|exists:sections,id',
            'review_type' => 'required|in:class_teacher,subject_teacher',
            'subject_id' => 'nullable|exists:subjects,id',
            'overall_remarks' => 'nullable|string|max:1000',
            'evaluations' => 'required|array',
        ]);

        $date = $request->date;
        $classId = (int) $request->class_id;
        $sectionId = (int) $request->section_id;
        $reviewType = $request->review_type;
        $subjectId = $request->subject_id ? (int) $request->subject_id : null;
        $teacherContext = $this->getTeacherContext();
        $staffId = $teacherContext['staff'] ? $teacherContext['staff']->id : null;

        // Teacher permission check (only restrict if explicit assignments are configured)
        if ($teacherContext['isTeacher'] && !empty($teacherContext['classTeacherSectionIds'])) {
            if ($reviewType === 'class_teacher' && !in_array($sectionId, $teacherContext['classTeacherSectionIds'])) {
                $sec = Section::find($sectionId);
                $isCt = ($sec && ($sec->class_teacher_id == $staffId || $sec->class_teacher_id == $user->id || $sec->assistant_class_teacher_id == $staffId || $sec->assistant_class_teacher_id == $user->id));
                if ($sec && ($sec->class_teacher_id || $sec->assistant_class_teacher_id) && !$isCt) {
                    return response()->json(['success' => false, 'message' => 'Unauthorized: Another staff member is assigned as Class Teacher for this section.'], 403);
                }
            }
        }

        $academicSession = \App\Models\AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first()
            ?? \App\Models\AcademicSession::where('school_id', $schoolId)->latest('id')->first();
        $academicSessionId = $academicSession ? $academicSession->id : null;

        DB::beginTransaction();
        try {
            // Find or create DailyTaskReview header
            $review = DailyTaskReview::updateOrCreate(
                [
                    'school_id' => $schoolId,
                    'date' => $date,
                    'class_id' => $classId,
                    'section_id' => $sectionId,
                    'review_type' => $reviewType,
                    'subject_id' => $subjectId,
                ],
                [
                    'academic_session_id' => $academicSessionId,
                    'teacher_id' => $staffId,
                    'overall_remarks' => $request->overall_remarks,
                ]
            );

            // Save evaluations
            $evaluationsData = $request->evaluations; // [ student_id => [ question_id => [ 'rating' => ..., 'score' => ..., 'status_option' => ..., 'remarks' => ... ] ] ]

            foreach ($evaluationsData as $studentId => $questionEntries) {
                if (!is_array($questionEntries)) continue;

                foreach ($questionEntries as $questionId => $data) {
                    $rating = isset($data['rating']) && $data['rating'] !== '' ? (int)$data['rating'] : null;
                    $score = isset($data['score']) && $data['score'] !== '' ? (float)$data['score'] : null;
                    $statusOption = isset($data['status_option']) && $data['status_option'] !== '' ? $data['status_option'] : null;
                    $remarks = isset($data['remarks']) && $data['remarks'] !== '' ? $data['remarks'] : null;

                    // If all fields are null, remove evaluation if exists, otherwise updateOrCreate
                    if ($rating === null && $score === null && $statusOption === null && $remarks === null) {
                        DailyTaskEvaluation::where('daily_task_review_id', $review->id)
                            ->where('student_id', $studentId)
                            ->where('daily_task_question_id', $questionId)
                            ->delete();
                    } else {
                        DailyTaskEvaluation::updateOrCreate(
                            [
                                'daily_task_review_id' => $review->id,
                                'student_id' => $studentId,
                                'daily_task_question_id' => $questionId,
                            ],
                            [
                                'school_id' => $schoolId,
                                'date' => $date,
                                'rating' => $rating,
                                'score' => $score,
                                'status_option' => $statusOption,
                                'remarks' => $remarks,
                            ]
                        );
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Daily Task Reviews saved successfully!',
                'review_id' => $review->id,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to save daily reviews: ' . $e->getMessage(),
            ], 500);
        }
    }

    // =========================================================================
    // STUDENT-WISE DAILY TASK REPORTS & EXPORTS
    // =========================================================================

    public function reportIndex(Request $request)
    {
        $schoolId = $this->schoolId();
        $reportData = $this->fetchReportData($request, $schoolId);

        $classes = SchoolClass::where('school_id', $schoolId)->with('sections')->get();
        $subjects = Subject::where('school_id', $schoolId)->orderBy('name')->get();
        $heads = DailyTaskHead::where('school_id', $schoolId)->get();

        return view('school.daily_tasks.reports', array_merge($reportData, [
            'classes' => $classes,
            'subjects' => $subjects,
            'heads' => $heads,
        ]));
    }

    /**
     * Helper to resolve student photo full URL.
     */
    private function formatPhotoUrl(?string $rawPhoto, ?int $studentId = null, ?string $admNo = null): ?string
    {
        $baseUrl = rtrim(config('app.url', ''), '/');
        try {
            if (app()->bound('request') && request()) {
                $req = request();
                $baseUrl = rtrim($req->getSchemeAndHttpHost() . $req->getBaseUrl(), '/');
            }
        } catch (\Throwable $e) {}

        if (!empty($rawPhoto)) {
            $p = ltrim($rawPhoto, '/');
            if (str_starts_with($p, 'http://') || str_starts_with($p, 'https://') || str_starts_with($p, 'data:image')) {
                return $p;
            }

            $cleanPath = preg_replace('/^(public\/|uploads\/|storage\/)/', '', $p);
            $baseName = basename($cleanPath);

            $searchPaths = [
                public_path('uploads/students/photos/' . $baseName) => $baseUrl . '/uploads/students/photos/' . $baseName,
                public_path('uploads/students/' . $baseName) => $baseUrl . '/uploads/students/' . $baseName,
                public_path('uploads/' . $cleanPath) => $baseUrl . '/uploads/' . $cleanPath,
                public_path('storage/' . $cleanPath) => $baseUrl . '/storage/' . $cleanPath,
                public_path($cleanPath) => $baseUrl . '/' . $cleanPath,
            ];

            foreach ($searchPaths as $sysPath => $webUrl) {
                if (file_exists($sysPath)) {
                    return $webUrl;
                }
            }

            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($cleanPath)) {
                return \Illuminate\Support\Facades\Storage::disk('public')->url($cleanPath);
            }

            return $baseUrl . '/uploads/' . $cleanPath;
        }

        // Search by admission_number or student ID in uploads directory if photo column was not set
        $identifiers = array_filter([$admNo, (string)$studentId]);
        $extensions = ['jpg', 'jpeg', 'png', 'webp', 'JPG', 'PNG', 'JPEG', 'WEBP'];

        foreach ($identifiers as $idVal) {
            foreach ($extensions as $ext) {
                $filename = $idVal . '.' . $ext;
                if (file_exists(public_path('uploads/students/photos/' . $filename))) {
                    return $baseUrl . '/uploads/students/photos/' . $filename;
                }
                if (file_exists(public_path('uploads/students/' . $filename))) {
                    return $baseUrl . '/uploads/students/' . $filename;
                }
            }
        }

        return null;
    }

    /**
     * Helper to prepare report data & statistics.
     */
    private function fetchReportData(Request $request, int $schoolId): array
    {
        $fromDate = $request->get('from_date', date('Y-m-01'));
        $toDate = $request->get('to_date', date('Y-m-d'));
        $classId = $request->get('class_id');
        $sectionId = $request->get('section_id');
        $subjectId = $request->get('subject_id');
        $reviewType = $request->get('review_type', 'all');
        $studentId = $request->get('student_id');
        $search = trim($request->get('search', ''));

        $query = DailyTaskEvaluation::where('daily_task_evaluations.school_id', $schoolId)
            ->whereBetween('daily_task_evaluations.date', [$fromDate, $toDate])
            ->join('daily_task_reviews', 'daily_task_evaluations.daily_task_review_id', '=', 'daily_task_reviews.id')
            ->join('students', 'daily_task_evaluations.student_id', '=', 'students.id')
            ->join('daily_task_questions', 'daily_task_evaluations.daily_task_question_id', '=', 'daily_task_questions.id')
            ->leftJoin('daily_task_heads', 'daily_task_questions.daily_task_head_id', '=', 'daily_task_heads.id')
            ->leftJoin('school_classes', 'daily_task_reviews.class_id', '=', 'school_classes.id')
            ->leftJoin('sections', 'daily_task_reviews.section_id', '=', 'sections.id')
            ->leftJoin('subjects', 'daily_task_reviews.subject_id', '=', 'subjects.id')
            ->leftJoin('staff', 'daily_task_reviews.teacher_id', '=', 'staff.id')
            ->select([
                'daily_task_evaluations.*',
                'daily_task_reviews.review_type',
                'daily_task_reviews.overall_remarks as review_overall_remarks',
                'students.id as student_id',
                'students.first_name as student_first_name',
                'students.last_name as student_last_name',
                'students.roll_number as student_roll_no',
                'students.admission_number as student_admission_no',
                'students.photo as student_photo',
                'daily_task_questions.question as question_text',
                'daily_task_questions.evaluation_type',
                'daily_task_questions.max_score',
                'daily_task_heads.name as head_name',
                'daily_task_heads.color as head_color',
                'school_classes.name as class_name',
                'sections.name as section_name',
                'subjects.name as subject_name',
                'staff.first_name as teacher_first_name',
                'staff.last_name as teacher_last_name',
            ]);

        if ($classId && $classId !== 'all') {
            $query->where('daily_task_reviews.class_id', $classId);
        }

        if ($sectionId && $sectionId !== 'all') {
            $query->where('daily_task_reviews.section_id', $sectionId);
        }

        if ($subjectId && $subjectId !== 'all') {
            $query->where('daily_task_reviews.subject_id', $subjectId);
        }

        if ($reviewType && $reviewType !== 'all') {
            $query->where('daily_task_reviews.review_type', $reviewType);
        }

        if ($studentId && $studentId !== 'all') {
            $query->where('daily_task_evaluations.student_id', $studentId);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('students.first_name', 'like', "%{$search}%")
                  ->orWhere('students.last_name', 'like', "%{$search}%")
                  ->orWhere('students.roll_number', 'like', "%{$search}%")
                  ->orWhere('students.admission_number', 'like', "%{$search}%")
                  ->orWhere('daily_task_questions.question', 'like', "%{$search}%")
                  ->orWhere('daily_task_evaluations.remarks', 'like', "%{$search}%");
            });
        }

        // Summary KPI statistics
        $allEvaluations = (clone $query)->get();
        $totalEvaluations = $allEvaluations->count();
        $avgRating = $allEvaluations->whereNotNull('rating')->avg('rating') ?: 0;
        $total5Stars = $allEvaluations->where('rating', 5)->count();
        $totalReviewsLogged = DailyTaskReview::where('school_id', $schoolId)
            ->whereBetween('date', [$fromDate, $toDate])
            ->when($classId && $classId !== 'all', fn($q) => $q->where('class_id', $classId))
            ->when($sectionId && $sectionId !== 'all', fn($q) => $q->where('section_id', $sectionId))
            ->count();

        // Student-level Performance Ranking
        $studentPerformances = $allEvaluations->groupBy('student_id')->map(function ($items) {
            $first = $items->first();
            $avg = $items->whereNotNull('rating')->avg('rating') ?: ($items->whereNotNull('score')->avg('score') ?: 0);
            $photoUrl = $this->formatPhotoUrl($first->student_photo, $first->student_id, $first->student_admission_no);

            return [
                'student_id' => $first->student_id,
                'name' => trim($first->student_first_name . ' ' . ($first->student_last_name ?? '')),
                'first_name' => $first->student_first_name,
                'last_name' => $first->student_last_name,
                'roll_no' => $first->student_roll_no,
                'admission_no' => $first->student_admission_no,
                'photo_url' => $photoUrl,
                'class_section' => ($first->class_name ?? '') . ' - ' . ($first->section_name ?? ''),
                'total_tasks' => $items->count(),
                'avg_rating' => round($avg, 1),
                'items' => $items,
            ];
        })->sortByDesc('avg_rating')->values();

        $paginatedRecords = $query->orderBy('daily_task_evaluations.date', 'desc')
            ->orderBy('school_classes.sort_order')
            ->orderBy('students.roll_number')
            ->paginate(25)
            ->withQueryString();

        // Attach photo_url to paginated records collection
        $paginatedRecords->getCollection()->transform(function ($rec) {
            $rec->photo_url = $this->formatPhotoUrl($rec->student_photo, $rec->student_id, $rec->student_admission_no);
            return $rec;
        });

        return [
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'classId' => $classId,
            'sectionId' => $sectionId,
            'subjectId' => $subjectId,
            'reviewType' => $reviewType,
            'studentId' => $studentId,
            'search' => $search,
            'records' => $paginatedRecords,
            'allEvaluations' => $allEvaluations,
            'studentPerformances' => $studentPerformances,
            'stats' => [
                'total_evaluations' => $totalEvaluations,
                'total_reviews_logged' => $totalReviewsLogged,
                'avg_rating' => round($avgRating, 2),
                'total_5_stars' => $total5Stars,
                'unique_students' => $studentPerformances->count(),
            ],
        ];
    }

    public function exportReportPdf(Request $request)
    {
        $schoolId = $this->schoolId();
        $school = School::find($schoolId);
        $reportData = $this->fetchReportData($request, $schoolId);

        $records = $reportData['allEvaluations'];
        $stats = $reportData['stats'];
        $studentPerformances = $reportData['studentPerformances'];

        $pdf = Pdf::loadView('school.daily_tasks.pdf_report', [
            'school' => $school,
            'records' => $records,
            'stats' => $stats,
            'studentPerformances' => $studentPerformances,
            'fromDate' => $reportData['fromDate'],
            'toDate' => $reportData['toDate'],
            'classId' => $reportData['classId'],
            'sectionId' => $reportData['sectionId'],
            'reviewType' => $reportData['reviewType'],
        ]);

        $pdf->setPaper('a4', 'landscape');
        $fileName = 'Daily_Task_Report_' . date('Ymd_His') . '.pdf';

        return $pdf->download($fileName);
    }

    public function exportReportExcel(Request $request)
    {
        $schoolId = $this->schoolId();
        $school = School::find($schoolId);
        $reportData = $this->fetchReportData($request, $schoolId);
        $records = $reportData['allEvaluations'];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Daily Task Report');

        // Set document title block
        $sheet->mergeCells('A1:L1');
        $sheet->setCellValue('A1', ($school ? $school->name : 'School ERP') . ' - DAILY TASK EVALUATION REPORT');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('4F46E5');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(35);

        // Sub-header block
        $sheet->mergeCells('A2:L2');
        $filterInfo = "Period: {$reportData['fromDate']} to {$reportData['toDate']} | Generated: " . date('d M Y, h:i A') . " | Total Evaluations: " . count($records);
        $sheet->setCellValue('A2', $filterInfo);
        $sheet->getStyle('A2')->getFont()->setSize(10)->getColor()->setRGB('4B5563');
        $sheet->getStyle('A2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('EEF2F6');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(2)->setRowHeight(22);

        // Table Headers
        $headers = [
            'A3' => 'Date',
            'B3' => 'Admission No',
            'C3' => 'Roll No',
            'D3' => 'Student Name',
            'E3' => 'Class & Section',
            'F3' => 'Review Mode',
            'G3' => 'Subject / Category',
            'H3' => 'Task / Criteria Question',
            'I3' => 'Rating / Score',
            'J3' => 'Status / Grade',
            'K3' => 'Teacher Remarks',
            'L3' => 'Evaluated By',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E293B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
        ];
        $sheet->getStyle('A3:L3')->applyFromArray($headerStyle);
        $sheet->getRowDimension(3)->setRowHeight(26);

        // Data Rows
        $rowNum = 4;
        foreach ($records as $rec) {
            $ratingDisplay = '';
            if ($rec->rating) {
                $ratingDisplay = $rec->rating . ' / 5 Stars';
            } elseif ($rec->score !== null) {
                $ratingDisplay = $rec->score . ' / ' . ($rec->max_score ?? 10);
            }

            $modeText = $rec->review_type === 'class_teacher' ? 'Class Teacher' : 'Subject Teacher';
            $subjectOrCategory = $rec->subject_name ?: ($rec->head_name ?: 'General');
            $evaluator = trim(($rec->teacher_first_name ?? '') . ' ' . ($rec->teacher_last_name ?? '')) ?: 'Admin / Staff';

            $sheet->setCellValue('A' . $rowNum, date('d-m-Y', strtotime($rec->date)));
            $sheet->setCellValue('B' . $rowNum, $rec->student_admission_no ?: '-');
            $sheet->setCellValue('C' . $rowNum, $rec->student_roll_no ?: '-');
            $sheet->setCellValue('D' . $rowNum, trim($rec->student_first_name . ' ' . $rec->student_last_name));
            $sheet->setCellValue('E' . $rowNum, trim(($rec->class_name ?? '') . ' ' . ($rec->section_name ?? '')));
            $sheet->setCellValue('F' . $rowNum, $modeText);
            $sheet->setCellValue('G' . $rowNum, $subjectOrCategory);
            $sheet->setCellValue('H' . $rowNum, $rec->question_text);
            $sheet->setCellValue('I' . $rowNum, $ratingDisplay ?: '-');
            $sheet->setCellValue('J' . $rowNum, $rec->status_option ?: '-');
            $sheet->setCellValue('K' . $rowNum, $rec->remarks ?: ($rec->review_overall_remarks ?: '-'));
            $sheet->setCellValue('L' . $rowNum, $evaluator);

            $rowBg = ($rowNum % 2 === 0) ? 'F8FAFC' : 'FFFFFF';
            $sheet->getStyle("A{$rowNum}:L{$rowNum}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($rowBg);
            $sheet->getStyle("A{$rowNum}:L{$rowNum}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('E2E8F0');
            $sheet->getStyle("A{$rowNum}:C{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("I{$rowNum}:J{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $rowNum++;
        }

        // Auto size columns
        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'Daily_Task_Report_' . date('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
