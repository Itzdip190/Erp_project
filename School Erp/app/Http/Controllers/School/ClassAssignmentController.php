<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
use App\Models\SectionSubjectStaff;
use App\Models\Staff;
use App\Models\AcademicSession;
use Illuminate\Http\Request;

class ClassAssignmentController extends Controller
{
    public function classOverview(Request $request)
    {
        $classes = SchoolClass::with(['sections.classTeacher', 'sections.students', 'subjects'])->get();
        return view('school.assignments.overview', compact('classes'));
    }

    public function classesForm(Request $request)
    {
        $classes = SchoolClass::with('sections.classTeacher')->get();
        $teachers = Staff::all();
        return view('school.assignments.classes', compact('classes', 'teachers'));
    }

    public function storeClass(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'numeric_name' => 'nullable|integer',
        ]);

        SchoolClass::create([
            'name' => $request->name,
            'numeric_name' => $request->numeric_name,
        ]);

        return redirect()->back()->with('success', 'Class created successfully.');
    }

    public function destroyClass(SchoolClass $class)
    {
        // Deleting class will also delete sections/subjects through database cascade or manually
        $class->sections()->delete();
        $class->subjects()->delete();
        $class->delete();

        return redirect()->back()->with('success', 'Class and its sections/subjects deleted successfully.');
    }

    public function storeSection(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'class_id' => 'required|exists:school_classes,id',
            'class_teacher_id' => 'nullable|exists:staff,id',
        ]);

        Section::create([
            'name' => $request->name,
            'class_id' => $request->class_id,
            'class_teacher_id' => $request->class_teacher_id,
        ]);

        return redirect()->back()->with('success', 'Section created successfully.');
    }

    public function destroySection(Section $section)
    {
        $section->delete();
        return redirect()->back()->with('success', 'Section deleted successfully.');
    }

    public function subjectsForm(Request $request)
    {
        $classes = SchoolClass::all();
        $subjects = Subject::with('schoolClass')->get();
        return view('school.assignments.subjects', compact('classes', 'subjects'));
    }

    public function storeSubject(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'type' => 'required|in:Theory,Practical',
            'class_id' => 'required|exists:school_classes,id',
            'max_marks' => 'required|integer|min:0',
            'pass_marks' => 'required|integer|min:0',
        ]);

        Subject::create([
            'name' => $request->name,
            'code' => $request->code,
            'type' => $request->type,
            'class_id' => $request->class_id,
            'max_marks' => $request->max_marks,
            'pass_marks' => $request->pass_marks,
        ]);

        return redirect()->back()->with('success', 'Subject created successfully.');
    }

    public function destroySubject(Subject $subject)
    {
        $subject->delete();
        return redirect()->back()->with('success', 'Subject deleted successfully.');
    }

    public function teachersForm(Request $request)
    {
        $sections = Section::with('schoolClass')->get();
        $subjects = Subject::with('schoolClass')->get();
        $teachers = Staff::all();

        // Get current academic session
        $currentSchool = app()->bound('currentSchool') ? app('currentSchool') : null;
        $currentSession = $currentSchool
            ? AcademicSession::where('school_id', $currentSchool->id)->where('is_current', true)->first()
            : null;

        $assignments = SectionSubjectStaff::with(['section.schoolClass', 'subject', 'staff'])
            ->when($currentSession, function($q) use ($currentSession) {
                return $q->where('academic_session_id', $currentSession->id);
            })
            ->get();

        return view('school.assignments.teachers', compact('sections', 'subjects', 'teachers', 'assignments'));
    }

    public function storeAssignment(Request $request)
    {
        $request->validate([
            'section_id' => 'required|exists:sections,id',
            'subject_id' => 'required|exists:subjects,id',
            'staff_id' => 'required|exists:staff,id',
        ]);

        $currentSchool = app()->bound('currentSchool') ? app('currentSchool') : null;
        $currentSession = $currentSchool
            ? AcademicSession::where('school_id', $currentSchool->id)->where('is_current', true)->first()
            : null;

        SectionSubjectStaff::firstOrCreate([
            'section_id' => $request->section_id,
            'subject_id' => $request->subject_id,
            'staff_id' => $request->staff_id,
            'academic_session_id' => $currentSession?->id,
        ]);

        return redirect()->back()->with('success', 'Teacher assigned successfully.');
    }

    public function updateClassTeacher(Request $request, Section $section)
    {
        $request->validate([
            'class_teacher_id' => 'nullable|exists:staff,id',
        ]);

        $section->update([
            'class_teacher_id' => $request->class_teacher_id,
        ]);

        return redirect()->back()->with('success', 'Class teacher updated successfully.');
    }

    public function destroyAssignment($id)
    {
        $assignment = SectionSubjectStaff::findOrFail($id);
        $assignment->delete();

        return redirect()->back()->with('success', 'Teacher assignment removed successfully.');
    }
}
