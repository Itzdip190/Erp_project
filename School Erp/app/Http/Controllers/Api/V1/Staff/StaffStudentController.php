<?php

namespace App\Http\Controllers\Api\V1\Staff;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\Student;
use App\Models\SectionSubjectStaff;
use Illuminate\Http\Request;

class StaffStudentController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Student::with(['class', 'section']);

        if ($user->hasRole('teacher')) {
            $staff = Staff::where('user_id', $user->id)->first();
            if (!$staff) {
                return response()->json([
                    'success' => false,
                    'message' => 'Staff profile not found.',
                ], 403);
            }

            // Get section IDs assigned to this teacher in SectionSubjectStaff mapping
            $assignedSectionIds = SectionSubjectStaff::where('staff_id', $staff->id)
                ->pluck('section_id')
                ->unique()
                ->toArray();

            $query->whereIn('section_id', $assignedSectionIds);

            // If a specific section is requested, ensure teacher is assigned to it
            if ($request->get('section_id')) {
                if (!in_array((int)$request->section_id, $assignedSectionIds)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized access to this section.',
                    ], 403);
                }
                $query->where('section_id', $request->section_id);
            }
        } else {
            // For admins or other roles, allow filtering directly
            if ($request->get('section_id')) {
                $query->where('section_id', $request->section_id);
            }
        }

        if ($request->get('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('admission_number', 'like', "%{$search}%")
                  ->orWhere('roll_number', 'like', "%{$search}%");
            });
        }

        $students = $query->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $students,
        ]);
    }
}
