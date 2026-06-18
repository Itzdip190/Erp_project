<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Staff;
use App\Models\Timetable;
use App\Models\TimetableSubstitution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TimetableController extends Controller
{
    public function classTimetable(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $classes = SchoolClass::where('school_id', $schoolId)->get();
        $sections = Section::where('school_id', $schoolId)->get();
        $subjects = Subject::where('school_id', $schoolId)->get();
        $teachers = Staff::where('school_id', $schoolId)->where('is_active', true)->get();

        $classId = $request->get('class_id');
        $sectionId = $request->get('section_id');
        $dayOfWeek = $request->get('day_of_week', 'Monday');

        $periods = collect();
        if ($classId && $sectionId) {
            $periods = Timetable::where('school_id', $schoolId)
                ->where('class_id', $classId)
                ->where('section_id', $sectionId)
                ->where('day_of_week', $dayOfWeek)
                ->with(['subject', 'teacher'])
                ->orderBy('start_time')
                ->get();
        }

        return view('school.timetable.class', compact(
            'classes', 'sections', 'subjects', 'teachers',
            'classId', 'sectionId', 'dayOfWeek', 'periods'
        ));
    }

    public function storeClassTimetable(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $request->validate([
            'class_id' => 'required|exists:school_classes,id',
            'section_id' => 'required|exists:sections,id',
            'day_of_week' => 'required|string',
            'start_time' => 'required|string',
            'end_time' => 'required|string',
            'subject_id' => 'required|exists:subjects,id',
            'staff_id' => 'required|exists:staff,id',
            'room_number' => 'nullable|string',
        ]);

        // Check if teacher is already booked at that time on that day
        $conflict = Timetable::where('school_id', $schoolId)
            ->where('day_of_week', $request->day_of_week)
            ->where('staff_id', $request->staff_id)
            ->where('start_time', $request->start_time)
            ->exists();

        if ($conflict) {
            return back()->with('error', 'Teacher is already scheduled for another class at this time!');
        }

        Timetable::create(array_merge($request->all(), ['school_id' => $schoolId]));

        return back()->with('success', 'Timetable period added successfully.');
    }

    public function destroyClassTimetable(Timetable $timetable)
    {
        if ($timetable->school_id !== auth()->user()->school_id) {
            abort(403);
        }
        $timetable->delete();
        return back()->with('success', 'Period deleted successfully.');
    }

    public function groupTimetable()
    {
        $schoolId = auth()->user()->school_id;
        $classes = SchoolClass::where('school_id', $schoolId)->with('sections')->get();
        
        $timetables = Timetable::where('school_id', $schoolId)
            ->with(['class', 'section', 'subject', 'teacher'])
            ->get()
            ->groupBy(function($item) {
                return $item->class_id . '-' . $item->section_id;
            });

        return view('school.timetable.group', compact('classes', 'timetables'));
    }

    public function teacherTimetable(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $teachers = Staff::where('school_id', $schoolId)->where('is_active', true)->get();
        
        $teacherId = $request->get('teacher_id');
        $timetableData = collect();

        if ($teacherId) {
            $timetableData = Timetable::where('school_id', $schoolId)
                ->where('staff_id', $teacherId)
                ->with(['class', 'section', 'subject'])
                ->get()
                ->groupBy('day_of_week');
        }

        return view('school.timetable.teacher', compact('teachers', 'teacherId', 'timetableData'));
    }

    public function teacherSubstitution(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $teachers = Staff::where('school_id', $schoolId)->where('is_active', true)->get();
        
        $date = $request->get('date', today()->toDateString());
        $absentTeacherId = $request->get('absent_teacher_id');
        $dayOfWeek = date('l', strtotime($date));

        $periodsToSubstitute = collect();
        $substituteSuggestions = [];

        if ($absentTeacherId) {
            // Get all periods scheduled for absent teacher on this day of week
            $periodsToSubstitute = Timetable::where('school_id', $schoolId)
                ->where('staff_id', $absentTeacherId)
                ->where('day_of_week', $dayOfWeek)
                ->with(['class', 'section', 'subject'])
                ->get();

            // For each period, find substitute teachers who are free
            foreach ($periodsToSubstitute as $period) {
                // Find teachers who have NO classes during this period's start_time and day_of_week
                $busyTeacherIds = Timetable::where('school_id', $schoolId)
                    ->where('day_of_week', $dayOfWeek)
                    ->where('start_time', $period->start_time)
                    ->pluck('staff_id')
                    ->toArray();

                $freeTeachers = Staff::where('school_id', $schoolId)
                    ->where('is_active', true)
                    ->whereNotIn('id', array_merge($busyTeacherIds, [$absentTeacherId]))
                    ->get();

                $substituteSuggestions[$period->id] = $freeTeachers;
            }
        }

        // Fetch existing substitutions for today
        $existingSubstitutions = TimetableSubstitution::where('school_id', $schoolId)
            ->whereDate('date', $date)
            ->with(['timetable.class', 'timetable.section', 'timetable.subject', 'originalTeacher', 'substituteTeacher'])
            ->get();

        return view('school.timetable.substitution', compact(
            'teachers', 'date', 'absentTeacherId', 'dayOfWeek',
            'periodsToSubstitute', 'substituteSuggestions', 'existingSubstitutions'
        ));
    }

    public function storeSubstitution(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $request->validate([
            'date' => 'required|date',
            'timetable_id' => 'required|exists:timetables,id',
            'original_staff_id' => 'required|exists:staff,id',
            'substitute_staff_id' => 'required|exists:staff,id',
        ]);

        TimetableSubstitution::updateOrCreate(
            [
                'school_id' => $schoolId,
                'date' => $request->date,
                'timetable_id' => $request->timetable_id,
            ],
            [
                'original_staff_id' => $request->original_staff_id,
                'substitute_staff_id' => $request->substitute_staff_id,
                'status' => 'active',
            ]
        );

        return back()->with('success', 'Teacher substitution assigned successfully!');
    }

    public function destroySubstitution(TimetableSubstitution $substitution)
    {
        if ($substitution->school_id !== auth()->user()->school_id) {
            abort(403);
        }
        $substitution->delete();
        return back()->with('success', 'Substitution cancelled successfully.');
    }

    public function teacherWorkload()
    {
        $schoolId = auth()->user()->school_id;

        $workloads = Staff::where('school_id', $schoolId)
            ->where('is_active', true)
            ->with(['department', 'designation'])
            ->get()
            ->map(function($teacher) use ($schoolId) {
                // Count weekly periods
                $periodCount = Timetable::where('school_id', $schoolId)
                    ->where('staff_id', $teacher->id)
                    ->count();

                // Compute workload label
                if ($periodCount < 10) {
                    $status = 'Underloaded';
                    $color = 'var(--blue)';
                } elseif ($periodCount <= 20) {
                    $status = 'Optimal';
                    $color = 'var(--green)';
                } else {
                    $status = 'Overloaded';
                    $color = 'var(--red)';
                }

                return [
                    'teacher' => $teacher,
                    'periods' => $periodCount,
                    'status' => $status,
                    'color' => $color
                ];
            });

        return view('school.timetable.workload', compact('workloads'));
    }
}
