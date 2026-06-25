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
use App\Models\ClassTimetableCell;
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
            $timetableData = ClassTimetableCell::where('school_id', $schoolId)
                ->where('teacher_id', $teacherId)
                ->with(['schoolClass', 'section', 'subject', 'period'])
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
        $designatedSubstitutes = [];

        $currentSession = AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first()
            ?? AcademicSession::where('school_id', $schoolId)->first();
        $sessionId = $currentSession ? $currentSession->id : null;

        if ($absentTeacherId) {
            // Get all periods scheduled for absent teacher on this day of week
            $periodsToSubstitute = ClassTimetableCell::where('school_id', $schoolId)
                ->where('teacher_id', $absentTeacherId)
                ->where('day_of_week', $dayOfWeek)
                ->with(['schoolClass', 'section', 'subject', 'period'])
                ->get();

            // For each period, find substitute teachers who are free
            foreach ($periodsToSubstitute as $period) {
                // Find teachers who have classes during this period's start_time and day_of_week
                $busyTeacherIds = ClassTimetableCell::where('school_id', $schoolId)
                    ->where('day_of_week', $dayOfWeek)
                    ->where('timetable_group_period_id', $period->timetable_group_period_id)
                    ->whereNotNull('teacher_id')
                    ->pluck('teacher_id')
                    ->toArray();

                $freeTeachers = Staff::where('school_id', $schoolId)
                    ->where('is_active', true)
                    ->whereNotIn('id', array_merge($busyTeacherIds, [$absentTeacherId]))
                    ->get();

                $substituteSuggestions[$period->id] = $freeTeachers;

                // Check for a pre-assigned/designated substitute teacher from Module 7
                if ($sessionId) {
                    $mapping = \App\Models\SectionSubjectStaff::where('school_id', $schoolId)
                        ->where('section_id', $period->section_id)
                        ->where('subject_id', $period->subject_id)
                        ->where('staff_id', $absentTeacherId)
                        ->where('academic_session_id', $sessionId)
                        ->with('substituteStaff')
                        ->first();
                    
                    if ($mapping && $mapping->substituteStaff) {
                        $designatedSubstitutes[$period->id] = $mapping->substituteStaff;
                    }
                }
            }
        }

        // Fetch existing substitutions for today
        $existingSubstitutions = TimetableSubstitution::where('school_id', $schoolId)
            ->whereDate('date', $date)
            ->with(['timetable.schoolClass', 'timetable.section', 'timetable.subject', 'timetable.period', 'originalTeacher', 'substituteTeacher'])
            ->get();

        return view('school.timetable.substitution', compact(
            'teachers', 'date', 'absentTeacherId', 'dayOfWeek',
            'periodsToSubstitute', 'substituteSuggestions', 'existingSubstitutions', 'designatedSubstitutes'
        ));
    }

    public function storeSubstitution(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $request->validate([
            'date' => 'required|date',
            'timetable_id' => 'required|exists:class_timetable_cells,id',
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
}
