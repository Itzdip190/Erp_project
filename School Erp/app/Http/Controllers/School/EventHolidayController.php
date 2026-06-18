<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;

class EventHolidayController extends Controller
{
    private function ensureEventsSeeded($schoolId)
    {
        if (Event::where('school_id', $schoolId)->count() === 0) {
            Event::create([
                'school_id' => $schoolId,
                'title' => 'Independence Day Celebration',
                'description' => 'Flag hoisting ceremony and patriotic cultural events at school ground.',
                'start_date' => now()->addDays(5)->toDateString(),
                'end_date' => now()->addDays(5)->toDateString(),
                'is_holiday' => true,
            ]);
            Event::create([
                'school_id' => $schoolId,
                'title' => 'Annual Science Exhibition',
                'description' => 'Students will display science projects and experiments in classrooms.',
                'start_date' => now()->addDays(12)->toDateString(),
                'end_date' => now()->addDays(13)->toDateString(),
                'is_holiday' => false,
            ]);
            Event::create([
                'school_id' => $schoolId,
                'title' => 'Summer Vacation',
                'description' => 'School closed for summer break.',
                'start_date' => now()->subDays(10)->toDateString(),
                'end_date' => now()->subDays(2)->toDateString(),
                'is_holiday' => true,
            ]);
        }
    }

    public function eventManagement(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $this->ensureEventsSeeded($schoolId);

        if ($request->isMethod('post')) {
            $request->validate([
                'title' => 'required|string|max:150',
                'description' => 'nullable|string',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'is_holiday' => 'nullable|boolean',
            ]);

            Event::create([
                'school_id' => $schoolId,
                'title' => $request->title,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'is_holiday' => $request->has('is_holiday') ? true : false,
            ]);

            return back()->with('success', 'Event / Holiday created successfully!');
        }

        $events = Event::where('school_id', $schoolId)->orderBy('start_date', 'asc')->get();
        return view('school.events.index', compact('events'));
    }
}
