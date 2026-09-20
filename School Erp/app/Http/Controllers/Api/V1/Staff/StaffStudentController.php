<?php

namespace App\Http\Controllers\Api\V1\Staff;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\Staff;
use App\Models\Student;
use App\Models\SectionSubjectStaff;
use App\Support\SearchHelper;
use Illuminate\Http\Request;

class StaffStudentController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $schoolId = $user->school_id ?? 1;
        $sessionId = $request->get('academic_session_id');
        if (!$sessionId) {
            $currentSession = \App\Models\AcademicSession::resolveCurrentSessionForUser($user, $schoolId);
            $sessionId = $currentSession?->id;
        }

        $query = Student::where('school_id', $schoolId)
            ->activeStudents()
            ->with(['class', 'section', 'studentSessions' => fn($q) => $q->where('academic_session_id', $sessionId)]);

        if ($user->hasRole('teacher')) {
            $staff = Staff::where('user_id', $user->id)->first();
            if (!$staff) {
                return response()->json([
                    'success' => false,
                    'message' => 'Staff profile not found.',
                ], 403);
            }

            // Get section IDs assigned to this teacher (both Class Teacher and Subject Teacher)
            $secIdsFromCt = Section::where('school_id', $schoolId)
                ->where(function($q) use ($staff) {
                    $q->where('class_teacher_id', $staff->id)
                      ->orWhere('assistant_class_teacher_id', $staff->id);
                })
                ->pluck('id')->toArray();
            $secIdsFromSss = SectionSubjectStaff::where('staff_id', $staff->id)->pluck('section_id')->toArray();
            $assignedSectionIds = array_values(array_unique(array_filter(array_merge($secIdsFromCt, $secIdsFromSss))));

            $targetSectionId = $request->get('section_id');
            if ($targetSectionId) {
                if (!in_array((int)$targetSectionId, $assignedSectionIds)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized access to this section.',
                    ], 403);
                }
                $query->inAcademicSession($sessionId, null, (int)$targetSectionId);
            } else {
                $query->inAcademicSession($sessionId, null, $assignedSectionIds);
            }
        } else {
            // For admins or other roles, allow filtering directly
            $targetSectionId = $request->get('section_id');
            if ($targetSectionId) {
                $query->inAcademicSession($sessionId, null, (int)$targetSectionId);
            } else {
                $query->inAcademicSession($sessionId);
            }
        }

        if ($request->get('search')) {
            SearchHelper::applyStudentSearch($query, $request->search);
        }

        $students = $query->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $students,
        ]);
    }
}
