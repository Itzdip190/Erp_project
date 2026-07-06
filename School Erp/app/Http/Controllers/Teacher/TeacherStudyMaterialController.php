<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassTimetableCell;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\SectionSubjectStaff;
use App\Models\Staff;
use App\Models\StudyMaterial;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class TeacherStudyMaterialController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $staff = Staff::where('user_id', $user->id)->first();

        $materials = StudyMaterial::where('school_id', $user->school_id)
            ->when($staff, fn($q) => $q->where('staff_id', $staff->id))
            ->with(['schoolClass', 'section', 'subject'])
            ->latest()
            ->paginate(15);

        return view('teacher.study_materials.index', compact('materials', 'staff'));
    }

    public function create()
    {
        $user = Auth::user();
        $staff = Staff::where('user_id', $user->id)->first();

        $allocatedClassIds = [];
        $allocatedSectionIds = [];
        $allocatedSubjectIds = [];
        $allocationMap = [];

        if ($staff) {
            if (Schema::hasTable('section_subject_staff')) {
                $sss = SectionSubjectStaff::where('school_id', $user->school_id)
                    ->where('staff_id', $staff->id)
                    ->with(['section.class', 'subject'])
                    ->get();
                foreach ($sss as $row) {
                    if ($row->section) {
                        $cId = $row->section->class_id;
                        $sId = $row->section_id;
                        $subId = $row->subject_id;

                        $allocatedClassIds[] = $cId;
                        $allocatedSectionIds[] = $sId;
                        if ($subId) $allocatedSubjectIds[] = $subId;

                        $allocationMap[$cId]['sections'][$sId] = $row->section->name;
                        if ($subId && $row->subject) {
                            $allocationMap[$cId]['subjects'][$subId] = $row->subject->name;
                        }
                    }
                }
            }

            if (Schema::hasTable('class_timetable_cells')) {
                $cells = ClassTimetableCell::where('school_id', $user->school_id)
                    ->where('teacher_id', $staff->id)
                    ->with(['schoolClass', 'section', 'subject'])
                    ->get();
                foreach ($cells as $c) {
                    if ($c->class_id && $c->section_id) {
                        $allocatedClassIds[] = $c->class_id;
                        $allocatedSectionIds[] = $c->section_id;
                        if ($c->subject_id) $allocatedSubjectIds[] = $c->subject_id;

                        $allocationMap[$c->class_id]['sections'][$c->section_id] = $c->section?->name ?? 'Section';
                        if ($c->subject_id && $c->subject) {
                            $allocationMap[$c->class_id]['subjects'][$c->subject_id] = $c->subject->name;
                        }
                    }
                }
            }
        }

        $allocatedClassIds = array_unique(array_filter($allocatedClassIds));
        $allocatedSectionIds = array_unique(array_filter($allocatedSectionIds));
        $allocatedSubjectIds = array_unique(array_filter($allocatedSubjectIds));

        $classes = count($allocatedClassIds) > 0 
            ? SchoolClass::where('school_id', $user->school_id)->whereIn('id', $allocatedClassIds)->get()
            : SchoolClass::where('school_id', $user->school_id)->get();

        $sections = count($allocatedSectionIds) > 0
            ? Section::where('school_id', $user->school_id)->whereIn('id', $allocatedSectionIds)->get()
            : Section::where('school_id', $user->school_id)->get();

        $subjects = count($allocatedSubjectIds) > 0
            ? Subject::where('school_id', $user->school_id)->whereIn('id', $allocatedSubjectIds)->get()
            : Subject::where('school_id', $user->school_id)->get();

        return view('teacher.study_materials.create', compact('classes', 'sections', 'subjects', 'allocationMap'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $staff = Staff::where('user_id', $user->id)->first();

        if (!$staff) {
            return back()->with('error', 'Staff profile not found for logged-in user.');
        }

        $request->validate([
            'class_id'   => 'required|exists:school_classes,id',
            'section_id' => 'required|exists:sections,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'title'      => 'required|string|max:255',
            'description'=> 'nullable|string',
            'attachment' => 'required|file|max:20480',
        ]);

        $filePath = $request->file('attachment')->store('study_materials', 'public');

        StudyMaterial::create([
            'school_id'  => $user->school_id,
            'class_id'   => $request->class_id,
            'section_id' => $request->section_id,
            'subject_id' => $request->subject_id,
            'staff_id'   => $staff->id,
            'title'      => $request->title,
            'description'=> $request->description,
            'file_path'  => $filePath,
        ]);

        return redirect()->route('teacher.study-materials.index')->with('success', 'Study material uploaded successfully and available for class students!');
    }

    public function destroy(StudyMaterial $studyMaterial)
    {
        $user = Auth::user();
        if ($studyMaterial->school_id !== $user->school_id) {
            abort(403);
        }

        if ($studyMaterial->file_path && Storage::disk('public')->exists($studyMaterial->file_path)) {
            Storage::disk('public')->delete($studyMaterial->file_path);
        }

        $studyMaterial->delete();

        return back()->with('success', 'Study material deleted successfully.');
    }
}
