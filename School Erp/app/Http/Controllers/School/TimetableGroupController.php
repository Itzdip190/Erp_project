<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\TimetableGroup;
use App\Models\TimetableGroupPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TimetableGroupController extends Controller
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

        // Fetch groups
        $groups = TimetableGroup::where('school_id', $schoolId)
            ->where('academic_year', $academicYear)
            ->with(['classes', 'sections', 'periods'])
            ->get();

        // Fetch classes and sections for the creator step 2
        $classes = SchoolClass::where('school_id', $schoolId)->with('sections')->get();

        return view('school.timetable.groups.index', compact(
            'academicSessions', 'sessionId', 'selectedSession', 'groups', 'classes'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'group_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'class_start_time' => 'required|string',
            'number_of_periods' => 'required|integer|min:1',
            'applicable_days' => 'required|array|min:1',
            'periods' => 'required|array|min:1',
            'periods.*.period_name' => 'required|string',
            'periods.*.duration_minutes' => 'required|integer|min:1',
            'class_sections' => 'required|array|min:1',
            'class_sections.*' => 'required|string', // format: "classId-sectionId"
        ]);

        $schoolId = auth()->user()->school_id;
        $session = AcademicSession::find($request->academic_session_id);
        $academicYear = $session ? $session->name : (date('Y') . '-' . (date('Y') + 1));

        // Format class_start_time to H:i:s
        $classStartTime = date('H:i:s', strtotime($request->class_start_time));

        // Validate no overlaps or active group conflicts
        // (For simplicity, if an active group is assigned to a class+section, warning or overwrite)
        
        DB::transaction(function() use ($request, $schoolId, $academicYear, $classStartTime) {
            $group = TimetableGroup::create([
                'school_id' => $schoolId,
                'group_name' => $request->group_name,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'academic_year' => $academicYear,
                'class_start_time' => $classStartTime,
                'number_of_periods' => $request->number_of_periods,
                'applicable_days' => $request->applicable_days,
                'is_active' => filter_var($request->get('is_active', true), FILTER_VALIDATE_BOOLEAN),
                'created_by' => auth()->id(),
            ]);

            // Save periods and compute times
            $currentTime = strtotime($classStartTime);
            foreach ($request->periods as $index => $periodData) {
                $duration = intval($periodData['duration_minutes']);
                
                // Calculate start and end times if not explicitly given
                $periodStart = isset($periodData['start_time']) && !empty($periodData['start_time'])
                    ? date('H:i:s', strtotime($periodData['start_time']))
                    : date('H:i:s', $currentTime);

                $periodEnd = isset($periodData['end_time']) && !empty($periodData['end_time'])
                    ? date('H:i:s', strtotime($periodData['end_time']))
                    : date('H:i:s', strtotime("+$duration minutes", strtotime($periodStart)));

                TimetableGroupPeriod::create([
                    'school_id' => $schoolId,
                    'timetable_group_id' => $group->id,
                    'period_name' => $periodData['period_name'],
                    'duration_minutes' => $duration,
                    'start_time' => $periodStart,
                    'end_time' => $periodEnd,
                    'sort_order' => $index,
                ]);

                $currentTime = strtotime($periodEnd);
            }

            // Save class-sections pivots
            foreach ($request->class_sections as $cs) {
                list($classId, $sectionId) = explode('-', $cs);
                
                // Disable existing active group class-section mappings to prevent double scheduling
                DB::table('timetable_group_class_section')
                    ->join('timetable_groups', 'timetable_groups.id', '=', 'timetable_group_class_section.timetable_group_id')
                    ->where('timetable_groups.school_id', $schoolId)
                    ->where('timetable_groups.academic_year', $academicYear)
                    ->where('timetable_group_class_section.class_id', $classId)
                    ->where('timetable_group_class_section.section_id', $sectionId)
                    ->delete();

                DB::table('timetable_group_class_section')->insert([
                    'school_id' => $schoolId,
                    'timetable_group_id' => $group->id,
                    'class_id' => $classId,
                    'section_id' => $sectionId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Timetable group template created successfully.'
        ]);
    }

    public function update(Request $request, TimetableGroup $group)
    {
        if ($group->school_id !== auth()->user()->school_id) {
            abort(403);
        }

        $request->validate([
            'group_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'class_start_time' => 'required|string',
            'number_of_periods' => 'required|integer|min:1',
            'applicable_days' => 'required|array|min:1',
            'periods' => 'required|array|min:1',
            'periods.*.period_name' => 'required|string',
            'periods.*.duration_minutes' => 'required|integer|min:1',
            'class_sections' => 'required|array|min:1',
            'class_sections.*' => 'required|string', // format: "classId-sectionId"
        ]);

        $schoolId = auth()->user()->school_id;
        $academicYear = $group->academic_year;

        // Format class_start_time to H:i:s
        $classStartTime = date('H:i:s', strtotime($request->class_start_time));

        DB::transaction(function() use ($request, $group, $schoolId, $academicYear, $classStartTime) {
            $group->update([
                'group_name' => $request->group_name,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'class_start_time' => $classStartTime,
                'number_of_periods' => $request->number_of_periods,
                'applicable_days' => $request->applicable_days,
                'is_active' => filter_var($request->get('is_active', true), FILTER_VALIDATE_BOOLEAN),
            ]);

            // Save periods and compute times
            $group->periods()->delete();

            $currentTime = strtotime($classStartTime);
            foreach ($request->periods as $index => $periodData) {
                $duration = intval($periodData['duration_minutes']);
                
                $periodStart = isset($periodData['start_time']) && !empty($periodData['start_time'])
                    ? date('H:i:s', strtotime($periodData['start_time']))
                    : date('H:i:s', $currentTime);

                $periodEnd = isset($periodData['end_time']) && !empty($periodData['end_time'])
                    ? date('H:i:s', strtotime($periodData['end_time']))
                    : date('H:i:s', strtotime("+$duration minutes", strtotime($periodStart)));

                TimetableGroupPeriod::create([
                    'school_id' => $schoolId,
                    'timetable_group_id' => $group->id,
                    'period_name' => $periodData['period_name'],
                    'duration_minutes' => $duration,
                    'start_time' => $periodStart,
                    'end_time' => $periodEnd,
                    'sort_order' => $index,
                ]);

                $currentTime = strtotime($periodEnd);
            }

            // Save class-sections pivots
            DB::table('timetable_group_class_section')
                ->where('timetable_group_id', $group->id)
                ->delete();

            foreach ($request->class_sections as $cs) {
                list($classId, $sectionId) = explode('-', $cs);
                
                // Disable existing active group class-section mappings to prevent double scheduling
                DB::table('timetable_group_class_section')
                    ->join('timetable_groups', 'timetable_groups.id', '=', 'timetable_group_class_section.timetable_group_id')
                    ->where('timetable_groups.school_id', $schoolId)
                    ->where('timetable_groups.academic_year', $academicYear)
                    ->where('timetable_group_class_section.class_id', $classId)
                    ->where('timetable_group_class_section.section_id', $sectionId)
                    ->delete();

                DB::table('timetable_group_class_section')->insert([
                    'school_id' => $schoolId,
                    'timetable_group_id' => $group->id,
                    'class_id' => $classId,
                    'section_id' => $sectionId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Timetable group template updated successfully.'
        ]);
    }

    public function toggleStatus(TimetableGroup $group)
    {
        if ($group->school_id !== auth()->user()->school_id) {
            abort(403);
        }

        $group->update(['is_active' => !$group->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully.',
            'is_active' => $group->is_active
        ]);
    }
}
