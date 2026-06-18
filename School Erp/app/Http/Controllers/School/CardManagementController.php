<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CardTemplate;
use App\Models\StudentCard;
use App\Models\Student;
use App\Models\SchoolClass;

class CardManagementController extends Controller
{
    private function ensureTemplatesSeeded($schoolId)
    {
        if (CardTemplate::where('school_id', $schoolId)->count() === 0) {
            CardTemplate::create([
                'school_id' => $schoolId,
                'name' => 'Default Student ID Card',
                'type' => 'id_card',
                'background_color' => '#1a1f3c',
                'text_color' => '#ffffff',
                'layout_style' => 'classic',
            ]);
            CardTemplate::create([
                'school_id' => $schoolId,
                'name' => 'City Transport Bus Pass',
                'type' => 'bus_pass',
                'background_color' => '#065f46',
                'text_color' => '#ffffff',
                'layout_style' => 'minimal',
            ]);
            CardTemplate::create([
                'school_id' => $schoolId,
                'name' => 'Final Exam Admit Card',
                'type' => 'admit_card',
                'background_color' => '#78350f',
                'text_color' => '#ffffff',
                'layout_style' => 'detailed',
            ]);
        }

        if (StudentCard::where('school_id', $schoolId)->count() === 0) {
            $students = Student::where('school_id', $schoolId)->take(3)->get();
            $tpl = CardTemplate::where('school_id', $schoolId)->first();
            if ($tpl) {
                foreach ($students as $st) {
                    StudentCard::create([
                        'school_id' => $schoolId,
                        'student_id' => $st->id,
                        'card_template_id' => $tpl->id,
                        'card_number' => 'CRD-' . rand(100000, 999999),
                        'expiry_date' => now()->addYear()->toDateString(),
                        'status' => 'active',
                    ]);
                }
            }
        }
    }

    public function templateCreator(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $this->ensureTemplatesSeeded($schoolId);

        if ($request->isMethod('post')) {
            $request->validate([
                'name' => 'required|string|max:100',
                'type' => 'required|string|in:id_card,bus_pass,admit_card',
                'background_color' => 'required|string',
                'text_color' => 'required|string',
                'layout_style' => 'required|string',
            ]);

            CardTemplate::create([
                'school_id' => $schoolId,
                'name' => $request->name,
                'type' => $request->type,
                'background_color' => $request->background_color,
                'text_color' => $request->text_color,
                'layout_style' => $request->layout_style,
            ]);

            return back()->with('success', 'Card Template created successfully!');
        }

        $templates = CardTemplate::where('school_id', $schoolId)->get();
        return view('school.cards.template_creator', compact('templates'));
    }

    public function generateCard(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $this->ensureTemplatesSeeded($schoolId);

        if ($request->isMethod('post')) {
            $request->validate([
                'student_id' => 'required|exists:students,id',
                'card_template_id' => 'required|exists:card_templates,id',
                'expiry_date' => 'required|date',
            ]);

            StudentCard::create([
                'school_id' => $schoolId,
                'student_id' => $request->student_id,
                'card_template_id' => $request->card_template_id,
                'card_number' => 'CRD-' . rand(100000, 999999),
                'expiry_date' => $request->expiry_date,
                'status' => 'active',
            ]);

            return back()->with('success', 'Student Card generated and registered successfully!');
        }

        $students = Student::where('school_id', $schoolId)->with(['class', 'section'])->get();
        $templates = CardTemplate::where('school_id', $schoolId)->get();
        $cards = StudentCard::where('school_id', $schoolId)->with(['student.class', 'template'])->get();

        return view('school.cards.generate_card', compact('students', 'templates', 'cards'));
    }
}
