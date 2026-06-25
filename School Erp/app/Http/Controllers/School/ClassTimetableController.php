<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Staff;
use App\Models\ClassSubjectTeacher;
use App\Models\ClassTimetableCell;
use App\Models\TimetableGroup;
use App\Models\TimetableGroupPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassTimetableController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        // Fetch academic sessions
        $academicSessions = AcademicSession::where('school_id', $schoolId)->get();
        $currentSession = AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first()
            ?? AcademicSession::where('school_id', $schoolId)->first();

        $sessionId = $request->get('academic_session_id', $currentSession ? $currentSession->id : null);
        $selectedSession = $sessionId ? AcademicSession::find($sessionId) : null;
        $academicYear = $selectedSession ? $selectedSession->name : (date('Y') . '-' . (date('Y') + 1));

        $classList = SchoolClass::where('school_id', $schoolId)->get();
        
        $classFilterId = $request->get('class_id');
        $sectionFilterId = $request->get('section_id');

        $sectionList = collect();
        if ($classFilterId) {
            $sectionList = Section::where('class_id', $classFilterId)->get();
        }

        if ($request->has('get_sections')) {
            return response()->json([
                'success' => true,
                'sections' => $sectionList
            ]);
        }

        $teachers = Staff::where('school_id', $schoolId)->where('is_active', true)->get();

        // If Class & Section selected, retrieve the active TimetableGroup mapping
        $group = null;
        $subjects = collect();
        $gridData = [];
        $days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];

        if ($classFilterId && $sectionFilterId) {
            // Find active timetable group linked to this class and section
            $group = TimetableGroup::where('school_id', $schoolId)
                ->where('academic_year', $academicYear)
                ->where('is_active', true)
                ->whereHas('classes', function($q) use ($classFilterId) {
                    $q->where('school_classes.id', $classFilterId);
                })
                ->whereHas('sections', function($q) use ($sectionFilterId) {
                    $q->where('sections.id', $sectionFilterId);
                })
                ->first();

            // Retrieve subjects of this class
            $subjects = Subject::where('class_id', $classFilterId)->get();

            // For each subject, check if there is an assigned teacher
            foreach ($subjects as $sub) {
                $assignment = ClassSubjectTeacher::where('class_id', $classFilterId)
                    ->where('section_id', $sectionFilterId)
                    ->where('subject_id', $sub->id)
                    ->first();
                $sub->assigned_teacher = $assignment ? $assignment->teacher : null;
            }
        }

        return view('school.timetable.class.index', compact(
            'academicSessions', 'sessionId', 'selectedSession',
            'classList', 'classFilterId', 'sectionList', 'sectionFilterId',
            'teachers', 'group', 'subjects', 'days'
        ));
    }

    public function getGrid(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $classId = $request->class_id;
        $sectionId = $request->section_id;
        $academicSessionId = $request->academic_session_id;

        $session = AcademicSession::find($academicSessionId);
        $academicYear = $session ? $session->name : (date('Y') . '-' . (date('Y') + 1));

        $group = TimetableGroup::where('school_id', $schoolId)
            ->where('academic_year', $academicYear)
            ->where('is_active', true)
            ->whereHas('classes', function($q) use ($classId) {
                $q->where('school_classes.id', $classId);
            })
            ->whereHas('sections', function($q) use ($sectionId) {
                $q->where('sections.id', $sectionId);
            })
            ->first();

        // Retrieve subjects of this class
        $subjects = Subject::where('class_id', $classId)->get();

        // For each subject, check if there is an assigned teacher
        foreach ($subjects as $sub) {
            $assignment = ClassSubjectTeacher::where('class_id', $classId)
                ->where('section_id', $sectionId)
                ->where('subject_id', $sub->id)
                ->first();
            $sub->assigned_teacher = $assignment ? $assignment->teacher : null;
        }

        $paletteHtml = view('school.timetable.class._palette-partial', compact('subjects'))->render();

        if (!$group) {
            return response()->json([
                'success' => false,
                'message' => 'No active timetable template group found for the selected Class and Section.',
                'palette_html' => $paletteHtml
            ], 404);
        }

        $periods = $group->periods;
        $days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];

        // Retrieve existing cells
        $cells = ClassTimetableCell::where('school_id', $schoolId)
            ->where('class_id', $classId)
            ->where('section_id', $sectionId)
            ->where('timetable_group_id', $group->id)
            ->with(['subject', 'teacher'])
            ->get();

        $gridData = [];
        foreach ($periods as $period) {
            $gridData[$period->id] = [];
            foreach ($days as $day) {
                $cell = $cells->where('timetable_group_period_id', $period->id)
                    ->where('day_of_week', $day)
                    ->first();
                $gridData[$period->id][$day] = $cell;
            }
        }

        // Section details
        $section = Section::with('schoolClass')->find($sectionId);

        $viewHtml = view('school.timetable.class._grid-partial', compact(
            'group', 'periods', 'days', 'gridData', 'section'
        ))->render();

        return response()->json([
            'success' => true,
            'html' => $viewHtml,
            'palette_html' => $paletteHtml,
            'group_id' => $group->id
        ]);
    }

    public function checkTeacherAssigned(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:school_classes,id',
            'section_id' => 'required|exists:sections,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        $assignment = ClassSubjectTeacher::where('class_id', $request->class_id)
            ->where('section_id', $request->section_id)
            ->where('subject_id', $request->subject_id)
            ->with('teacher')
            ->first();

        if ($assignment) {
            return response()->json([
                'assigned' => true,
                'teacher_id' => $assignment->teacher_id,
                'teacher_name' => $assignment->teacher->full_name,
            ]);
        }

        return response()->json([
            'assigned' => false
        ]);
    }

    public function assignTeacher(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:school_classes,id',
            'section_id' => 'required|exists:sections,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:staff,id',
            // Optional cell coordinates to auto-save after assignment
            'timetable_group_id' => 'nullable|exists:timetable_groups,id',
            'timetable_group_period_id' => 'nullable|exists:timetable_group_periods,id',
            'day_of_week' => 'nullable|string',
            'mode' => 'nullable|string|in:online,offline',
        ]);

        $schoolId = auth()->user()->school_id;

        DB::transaction(function() use ($request, $schoolId) {
            ClassSubjectTeacher::updateOrCreate(
                [
                    'school_id' => $schoolId,
                    'class_id' => $request->class_id,
                    'section_id' => $request->section_id,
                    'subject_id' => $request->subject_id,
                ],
                [
                    'teacher_id' => $request->teacher_id,
                ]
            );

            // Auto-save cell drop if coordinates provided
            if ($request->timetable_group_id && $request->timetable_group_period_id && $request->day_of_week) {
                ClassTimetableCell::updateOrCreate(
                    [
                        'school_id' => $schoolId,
                        'class_id' => $request->class_id,
                        'section_id' => $request->section_id,
                        'timetable_group_period_id' => $request->timetable_group_period_id,
                        'day_of_week' => $request->day_of_week,
                    ],
                    [
                        'timetable_group_id' => $request->timetable_group_id,
                        'subject_id' => $request->subject_id,
                        'teacher_id' => $request->teacher_id,
                        'mode' => $request->get('mode', 'online'),
                    ]
                );
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Teacher assigned and cell scheduled successfully.'
        ]);
    }

    public function saveCell(Request $request)
    {
        $request->validate([
            'timetable_group_id' => 'required|exists:timetable_groups,id',
            'class_id' => 'required|exists:school_classes,id',
            'section_id' => 'required|exists:sections,id',
            'timetable_group_period_id' => 'required|exists:timetable_group_periods,id',
            'day_of_week' => 'required|string',
            'subject_id' => 'required|exists:subjects,id',
            'mode' => 'nullable|string|in:online,offline',
        ]);

        $schoolId = auth()->user()->school_id;

        // Check if teacher is assigned
        $assignment = ClassSubjectTeacher::where('class_id', $request->class_id)
            ->where('section_id', $request->section_id)
            ->where('subject_id', $request->subject_id)
            ->first();

        if (!$assignment) {
            $subject = Subject::find($request->subject_id);
            return response()->json([
                'teacher_required' => true,
                'subject_id' => $request->subject_id,
                'subject_name' => $subject ? $subject->name : 'N/A'
            ]);
        }

        // Check for teacher booking collision (teacher already busy at this period/day in another class)
        $collision = ClassTimetableCell::where('school_id', $schoolId)
            ->where('day_of_week', $request->day_of_week)
            ->where('timetable_group_period_id', $request->timetable_group_period_id)
            ->where('teacher_id', $assignment->teacher_id)
            ->where(function($q) use ($request) {
                $q->where('class_id', '!=', $request->class_id)
                  ->orWhere('section_id', '!=', $request->section_id);
            })
            ->first();

        if ($collision) {
            $teacher = Staff::find($assignment->teacher_id);
            $collidingClass = SchoolClass::find($collision->class_id);
            $collidingSection = Section::find($collision->section_id);
            $classLabel = ($collidingClass ? $collidingClass->name : '') . ' - ' . ($collidingSection ? $collidingSection->name : '');
            
            return response()->json([
                'success' => false,
                'collision' => true,
                'message' => "Teacher '{$teacher->full_name}' is already scheduled for class '{$classLabel}' during this period."
            ], 422);
        }

        // Save cell
        $cell = ClassTimetableCell::updateOrCreate(
            [
                'school_id' => $schoolId,
                'class_id' => $request->class_id,
                'section_id' => $request->section_id,
                'timetable_group_period_id' => $request->timetable_group_period_id,
                'day_of_week' => $request->day_of_week,
            ],
            [
                'timetable_group_id' => $request->timetable_group_id,
                'subject_id' => $request->subject_id,
                'teacher_id' => $assignment->teacher_id,
                'mode' => $request->get('mode', 'online'),
            ]
        );

        $cell->load(['subject', 'teacher']);

        return response()->json([
            'success' => true,
            'message' => 'Period scheduled successfully.',
            'cell' => $cell
        ]);
    }

    public function deleteCell(ClassTimetableCell $cell)
    {
        if ($cell->school_id !== auth()->user()->school_id) {
            abort(403);
        }

        $cell->delete();

        return response()->json([
            'success' => true,
            'message' => 'Scheduled period removed.'
        ]);
    }

    public function copyCell(Request $request, ClassTimetableCell $cell)
    {
        if ($cell->school_id !== auth()->user()->school_id) {
            abort(403);
        }

        $request->validate([
            'targets' => 'required|array',
            'targets.*.timetable_group_period_id' => 'required|exists:timetable_group_periods,id',
            'targets.*.day_of_week' => 'required|string',
        ]);

        $schoolId = auth()->user()->school_id;

        DB::transaction(function() use ($request, $cell, $schoolId) {
            foreach ($request->targets as $target) {
                // Check teacher collision for target coordinates
                $collision = ClassTimetableCell::where('school_id', $schoolId)
                    ->where('day_of_week', $target['day_of_week'])
                    ->where('timetable_group_period_id', $target['timetable_group_period_id'])
                    ->where('teacher_id', $cell->teacher_id)
                    ->where(function($q) use ($cell) {
                        $q->where('class_id', '!=', $cell->class_id)
                          ->orWhere('section_id', '!=', $cell->section_id);
                    })
                    ->first();

                if ($collision) {
                    continue; // Skip copying to this cell to prevent collisions
                }

                ClassTimetableCell::updateOrCreate(
                    [
                        'school_id' => $schoolId,
                        'class_id' => $cell->class_id,
                        'section_id' => $cell->section_id,
                        'timetable_group_period_id' => $target['timetable_group_period_id'],
                        'day_of_week' => $target['day_of_week'],
                    ],
                    [
                        'timetable_group_id' => $cell->timetable_group_id,
                        'subject_id' => $cell->subject_id,
                        'teacher_id' => $cell->teacher_id,
                        'mode' => $cell->mode,
                    ]
                );
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Cell copied successfully to target periods.'
        ]);
    }

    public function download(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $classId = $request->class_id;
        $sectionId = $request->section_id;
        $academicSessionId = $request->academic_session_id;

        $session = AcademicSession::find($academicSessionId);
        $academicYear = $session ? $session->name : (date('Y') . '-' . (date('Y') + 1));

        $group = TimetableGroup::where('school_id', $schoolId)
            ->where('academic_year', $academicYear)
            ->where('is_active', true)
            ->whereHas('classes', function($q) use ($classId) {
                $q->where('school_classes.id', $classId);
            })
            ->whereHas('sections', function($q) use ($sectionId) {
                $q->where('sections.id', $sectionId);
            })
            ->first();

        if (!$group) {
            return back()->with('error', 'Timetable grid not found.');
        }

        $periods = $group->periods;
        $days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];

        $cells = ClassTimetableCell::where('school_id', $schoolId)
            ->where('class_id', $classId)
            ->where('section_id', $sectionId)
            ->where('timetable_group_id', $group->id)
            ->with(['subject', 'teacher'])
            ->get();

        $className = SchoolClass::find($classId)->name ?? 'Class';
        $sectionName = Section::find($sectionId)->name ?? 'Section';

        // Export as CSV
        $filename = "Timetable_{$className}_{$sectionName}.csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($periods, $days, $cells) {
            $file = fopen('php://output', 'w');
            
            // CSV Header
            $csvHeader = array_merge(["Period / Time"], $days);
            fputcsv($file, $csvHeader);

            // CSV Rows
            foreach ($periods as $period) {
                $row = [$period->period_name . " (" . date('g:i A', strtotime($period->start_time)) . "-" . date('g:i A', strtotime($period->end_time)) . ")"];
                foreach ($days as $day) {
                    $cell = $cells->where('timetable_group_period_id', $period->id)
                        ->where('day_of_week', $day)
                        ->first();
                    if ($cell && $cell->subject) {
                        $row[] = $cell->subject->name . " (" . ($cell->teacher ? $cell->teacher->full_name : 'No Teacher') . ")";
                    } else {
                        $row[] = "";
                    }
                }
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
