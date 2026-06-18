<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DigitalDiary;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Staff;

class DigitalDiaryController extends Controller
{
    private function ensureDiariesSeeded($schoolId)
    {
        if (DigitalDiary::where('school_id', $schoolId)->count() === 0) {
            $class = SchoolClass::where('school_id', $schoolId)->first();
            $section = Section::where('school_id', $schoolId)->first();
            $staff = Staff::where('school_id', $schoolId)->first();

            if ($class && $section && $staff) {
                DigitalDiary::create([
                    'school_id' => $schoolId,
                    'class_id' => $class->id,
                    'section_id' => $section->id,
                    'staff_id' => $staff->id,
                    'title' => 'Maths Homework - Exercise 5.2',
                    'content' => 'Please solve questions 1 to 10 on page 84. To be submitted tomorrow without fail.',
                    'diary_date' => now()->toDateString(),
                ]);
                DigitalDiary::create([
                    'school_id' => $schoolId,
                    'class_id' => $class->id,
                    'section_id' => $section->id,
                    'staff_id' => $staff->id,
                    'title' => 'Science Project Work',
                    'content' => 'Start researching for the Water Cycle diagram project. Due on Friday.',
                    'diary_date' => now()->subDay()->toDateString(),
                ]);
            }
        }
    }

    public function createDiary(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $this->ensureDiariesSeeded($schoolId);

        if ($request->isMethod('post')) {
            $request->validate([
                'class_id' => 'required|exists:school_classes,id',
                'section_id' => 'required|exists:sections,id',
                'title' => 'required|string|max:150',
                'content' => 'required|string',
                'diary_date' => 'required|date',
            ]);

            $staff = Staff::where('school_id', $schoolId)->first();
            $staffId = $staff ? $staff->id : 1; // Fallback

            DigitalDiary::create([
                'school_id' => $schoolId,
                'class_id' => $request->class_id,
                'section_id' => $request->section_id,
                'staff_id' => $staffId,
                'title' => $request->title,
                'content' => $request->content,
                'diary_date' => $request->diary_date,
            ]);

            return back()->with('success', 'Diary Entry logged successfully!');
        }

        $classes = SchoolClass::where('school_id', $schoolId)->with('sections')->get();
        $diaries = DigitalDiary::where('school_id', $schoolId)->with(['class', 'section', 'teacher'])->get();

        return view('school.diary.create', compact('classes', 'diaries'));
    }

    public function diaryReport(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $this->ensureDiariesSeeded($schoolId);

        $selectedClassId = $request->get('class_id');
        $selectedDate = $request->get('date', now()->toDateString());

        $query = DigitalDiary::where('school_id', $schoolId)
            ->whereDate('diary_date', $selectedDate);

        if ($selectedClassId) {
            $query->where('class_id', $selectedClassId);
        }

        $diaries = $query->with(['class', 'section', 'teacher'])->get();
        $classes = SchoolClass::where('school_id', $schoolId)->get();

        return view('school.diary.report', compact('diaries', 'classes', 'selectedClassId', 'selectedDate'));
    }
}
