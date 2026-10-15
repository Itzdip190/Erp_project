<?php

namespace App\Http\Controllers\Api\V1\Staff;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\Staff;
use App\Models\Student;
use App\Models\SectionSubjectStaff;
use App\Support\SearchHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StaffStudentController extends Controller
{
    public function index(Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::guard('sanctum')->user() ?? auth()->user();
        $code = $request->header('X-School-Code') ?: $request->query('school_code');
        $schoolId = null;
        if ($code) {
            $s = \App\Models\School::where('code', $code)->orWhereRaw('LOWER(code) = ?', [strtolower($code)])->first();
            $schoolId = $s?->id;
        }
        if (!$schoolId && $user) {
            $schoolId = $user->school_id;
        }
        if (!$schoolId) {
            $schoolId = (int) (\App\Models\School::first()?->id ?? 21);
        }

        // Resolve current academic session anchored strictly to today's date
        $today = today()->toDateString();
        $currentSession = \App\Models\AcademicSession::where('school_id', $schoolId)
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->first();

        if (!$currentSession && $user) {
            $currentSession = \App\Models\AcademicSession::resolveCurrentSessionForUser($user, $schoolId);
        }
        if (!$currentSession) {
            $currentSession = \App\Models\AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first()
                ?? \App\Models\AcademicSession::where('school_id', $schoolId)->latest('id')->first();
        }

        $sessionId = $request->get('academic_session_id', $currentSession?->id);

        $staff = null;
        if ($user) {
            $staff = Staff::where('user_id', $user->id)->first()
                ?? Staff::where('school_id', $schoolId)->where(function($q) use ($user) {
                    $q->where('email', $user->email)->orWhere('phone', $user->phone);
                })->first();
        }

        $isAdmin = !$user || (method_exists($user, 'hasRole') && ($user->hasRole('admin') || $user->hasRole('school_admin') || $user->hasRole('superadmin'))) || in_array($user->role ?? '', ['admin', 'superadmin', 'school_admin']);

        // Section IDs assigned to this teacher (Class Teacher, Subject Teacher, Timetable)
        $assignedSectionIds = [];
        if ($staff && !$isAdmin) {
            $staffIds = array_values(array_filter([$staff->id, $user?->id]));
            $secIdsFromCt = Section::where('school_id', $schoolId)
                ->where(function($q) use ($staffIds) {
                    $q->whereIn('class_teacher_id', $staffIds)
                      ->orWhereIn('assistant_class_teacher_id', $staffIds);
                })
                ->pluck('id')->toArray();

            $sssQuery = SectionSubjectStaff::whereIn('staff_id', $staffIds);
            if ($sessionId && \Illuminate\Support\Facades\Schema::hasColumn('section_subject_staff', 'academic_session_id')) {
                $sssQuery->where('academic_session_id', $sessionId);
            }
            $secIdsFromSss = $sssQuery->pluck('section_id')->toArray();

            $secIdsFromTimetable = [];
            if (\Illuminate\Support\Facades\Schema::hasTable('class_timetable_cells')) {
                $secIdsFromTimetable = \App\Models\ClassTimetableCell::where('school_id', $schoolId)
                    ->whereIn('teacher_id', $staffIds)
                    ->pluck('section_id')->toArray();
            }

            $assignedSectionIds = array_values(array_unique(array_filter(array_merge($secIdsFromCt, $secIdsFromSss, $secIdsFromTimetable))));
        }

        $query = Student::where('school_id', $schoolId)
            ->activeStudents()
            ->with([
                'class',
                'section',
                'studentSessions' => function($q) use ($sessionId) {
                    if ($sessionId) {
                        $q->where('academic_session_id', $sessionId);
                    }
                }
            ]);

        $targetSectionId = $request->get('section_id');
        $perPage = min(max((int)$request->get('per_page', 500), 10), 1000);

        if ($targetSectionId) {
            if ($sessionId && method_exists(Student::class, 'scopeInAcademicSession')) {
                $query->inAcademicSession((int)$sessionId, null, (int)$targetSectionId);
            } else {
                $query->where('section_id', (int)$targetSectionId);
            }
        } elseif (!empty($assignedSectionIds)) {
            if ($sessionId && method_exists(Student::class, 'scopeInAcademicSession')) {
                $query->inAcademicSession((int)$sessionId, null, $assignedSectionIds);
            } else {
                $query->whereIn('section_id', $assignedSectionIds);
            }
        }

        if ($request->get('search')) {
            SearchHelper::applyStudentSearch($query, $request->search);
        }

        // Graceful fallback if inAcademicSession yielded 0 results for this section
        if ($targetSectionId && (clone $query)->count() === 0) {
            $query = Student::where('school_id', $schoolId)
                ->activeStudents()
                ->where('section_id', (int)$targetSectionId)
                ->with([
                    'class',
                    'section',
                    'studentSessions' => function($q) use ($sessionId) {
                        if ($sessionId) {
                            $q->where('academic_session_id', $sessionId);
                        }
                    }
                ]);
            if ($request->get('search')) {
                SearchHelper::applyStudentSearch($query, $request->search);
            }
        }

        $students = $query->orderBy('roll_number')->paginate($perPage);

        // Resolve absolute photo URLs and current session roll numbers
        $students->getCollection()->transform(function ($student) use ($sessionId) {
            $sess = $student->studentSessions->first();
            if ($sess && !empty($sess->roll_number)) {
                $student->roll_number = $sess->roll_number;
            }
            if ($sess && !empty($sess->full_name)) {
                $student->full_name = $sess->full_name;
            }

            $resolvedPhoto = $student->resolvePhotoUrl($sessionId)
                ?? ($student->photo ? (str_starts_with($student->photo, 'http') ? $student->photo : asset('storage/' . ltrim($student->photo, '/'))) : null);

            $student->photo_url = $resolvedPhoto;
            $student->avatar = $resolvedPhoto;
            if (!$student->photo && $resolvedPhoto) {
                $student->photo = $resolvedPhoto;
            }
            return $student;
        });

        return response()->json([
            'success' => true,
            'data' => $students,
            'assigned_section_ids' => $assignedSectionIds,
            'academic_session' => [
                'id' => $currentSession?->id,
                'name' => $currentSession?->name,
                'is_current' => (bool)$currentSession?->is_current,
            ],
        ]);
    }

    /**
     * Get single student details (scoped to assigned sections).
     */
    public function show($id)
    {
        $user = \Illuminate\Support\Facades\Auth::guard('sanctum')->user() ?? auth()->user();
        $code = request()->header('X-School-Code') ?: request()->query('school_code');
        $schoolId = null;
        if ($code) {
            $s = \App\Models\School::where('code', $code)->orWhereRaw('LOWER(code) = ?', [strtolower($code)])->first();
            $schoolId = $s?->id;
        }
        if (!$schoolId && $user) {
            $schoolId = $user->school_id;
        }
        if (!$schoolId) {
            $schoolId = (int) (\App\Models\School::first()?->id ?? 21);
        }

        $student = Student::where('school_id', $schoolId)
            ->with(['class', 'section'])
            ->findOrFail($id);

        if ($user && !$this->authorizeStudentAccess($user, $student)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. This student is not assigned to your classes.',
            ], 403);
        }

        $photoUrl = $student->resolvePhotoUrl()
            ?? ($student->photo ? (str_starts_with($student->photo, 'http') ? $student->photo : asset('storage/' . ltrim($student->photo, '/'))) : null);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $student->id,
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
                'first_name_local' => $student->first_name_local,
                'student_name' => trim($student->first_name . ' ' . $student->last_name),
                'roll_number' => $student->roll_number,
                'admission_number' => $student->admission_number,
                'admission_date' => $student->admission_date ? Carbon::parse($student->admission_date)->toDateString() : null,
                'class_name' => $student->class?->name,
                'section_name' => $student->section?->name,
                'national_id' => $student->national_id,
                'section_id' => $student->section_id,
                'photo' => $photoUrl,
                'photo_url' => $photoUrl,
                'avatar' => $photoUrl,
                'date_of_birth' => $student->date_of_birth ? Carbon::parse($student->date_of_birth)->toDateString() : null,
                'gender' => $student->gender,
                'blood_group' => $student->blood_group,
                'religion' => $student->religion,
                'nationality' => $student->nationality,
                'caste' => $student->caste,
                'mother_tongue' => $student->mother_tongue,
                'is_rte' => (bool)$student->is_rte,
                'father_name' => $student->father_name,
                'father_phone' => $student->father_phone,
                'father_email' => $student->father_email,
                'father_occupation' => $student->father_occupation,
                'father_aadhar' => $student->father_aadhar,
                'mother_name' => $student->mother_name,
                'mother_phone' => $student->mother_phone,
                'mother_email' => $student->mother_email,
                'mother_occupation' => $student->mother_occupation,
                'guardian_name' => $student->guardian?->name,
                'guardian_phone' => $student->guardian?->phone,
                'phone' => $student->phone,
                'emergency_phone' => $student->emergency_contact_number,
                'current_address' => $student->current_address,
                'permanent_address' => $student->permanent_address,
                'city' => $student->city,
                'state' => $student->state,
                'pincode' => $student->pincode,
                'allergies' => $student->special_needs,
                'teacher_remarks' => $student->other_info,
            ],
        ]);
    }

    /**
     * Update student details (scoped to assigned sections).
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $student = Student::where('school_id', $user->school_id ?? 1)->findOrFail($id);

        if (!$this->authorizeStudentAccess($user, $student)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. This student is not assigned to your classes.',
            ], 403);
        }

        $validated = $request->validate([
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'first_name_local' => 'nullable|string|max:100',
            'roll_number' => 'nullable|string|max:50',
            'admission_number' => 'nullable|string|max:50',
            'national_id' => 'nullable|string|max:20',
            'admission_date' => 'nullable|date',
            'father_name' => 'nullable|string|max:150',
            'father_phone' => 'nullable|string|max:30',
            'father_email' => 'nullable|email|max:100',
            'father_occupation' => 'nullable|string|max:100',
            'mother_name' => 'nullable|string|max:150',
            'mother_phone' => 'nullable|string|max:30',
            'mother_email' => 'nullable|email|max:100',
            'mother_occupation' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:30',
            'emergency_phone' => 'nullable|string|max:30',
            'current_address' => 'nullable|string|max:500',
            'permanent_address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:20',
            'blood_group' => 'nullable|string|max:10',
            'religion' => 'nullable|string|max:50',
            'nationality' => 'nullable|string|max:50',
            'caste' => 'nullable|string|max:50',
            'mother_tongue' => 'nullable|string|max:50',
            'is_rte' => 'nullable|boolean',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string|max:20',
            'allergies' => 'nullable|string|max:255',
            'teacher_remarks' => 'nullable|string|max:500',
        ]);

        if (array_key_exists('first_name', $validated)) $student->first_name = $validated['first_name'];
        if (array_key_exists('last_name', $validated)) $student->last_name = $validated['last_name'];
        if (array_key_exists('first_name_local', $validated)) $student->first_name_local = $validated['first_name_local'];
        if (array_key_exists('roll_number', $validated)) $student->roll_number = $validated['roll_number'];
        if (!empty($validated['admission_number'])) $student->admission_number = $validated['admission_number'];
        if (array_key_exists('national_id', $validated)) $student->national_id = $validated['national_id'];
        if (array_key_exists('admission_date', $validated)) $student->admission_date = $validated['admission_date'];
        if (array_key_exists('father_name', $validated)) $student->father_name = $validated['father_name'];
        if (array_key_exists('father_phone', $validated)) $student->father_phone = $validated['father_phone'];
        if (array_key_exists('father_email', $validated)) $student->father_email = $validated['father_email'];
        if (array_key_exists('father_occupation', $validated)) $student->father_occupation = $validated['father_occupation'];
        if (array_key_exists('mother_name', $validated)) $student->mother_name = $validated['mother_name'];
        if (array_key_exists('mother_phone', $validated)) $student->mother_phone = $validated['mother_phone'];
        if (array_key_exists('mother_email', $validated)) $student->mother_email = $validated['mother_email'];
        if (array_key_exists('mother_occupation', $validated)) $student->mother_occupation = $validated['mother_occupation'];
        if (array_key_exists('phone', $validated)) $student->phone = $validated['phone'];
        if (array_key_exists('emergency_phone', $validated)) $student->emergency_contact_number = $validated['emergency_phone'];
        if (array_key_exists('current_address', $validated)) $student->current_address = $validated['current_address'];
        if (array_key_exists('permanent_address', $validated)) $student->permanent_address = $validated['permanent_address'];
        if (array_key_exists('city', $validated)) $student->city = $validated['city'];
        if (array_key_exists('state', $validated)) $student->state = $validated['state'];
        if (array_key_exists('pincode', $validated)) $student->pincode = $validated['pincode'];
        if (array_key_exists('blood_group', $validated)) $student->blood_group = $validated['blood_group'];
        if (array_key_exists('religion', $validated)) $student->religion = $validated['religion'];
        if (array_key_exists('nationality', $validated)) $student->nationality = $validated['nationality'];
        if (array_key_exists('caste', $validated)) $student->caste = $validated['caste'];
        if (array_key_exists('mother_tongue', $validated)) $student->mother_tongue = $validated['mother_tongue'];
        if (array_key_exists('is_rte', $validated)) $student->is_rte = $validated['is_rte'];
        if (array_key_exists('date_of_birth', $validated)) $student->date_of_birth = $validated['date_of_birth'];
        if (array_key_exists('gender', $validated)) $student->gender = $validated['gender'];
        if (array_key_exists('allergies', $validated)) $student->special_needs = $validated['allergies'];
        if (array_key_exists('teacher_remarks', $validated)) $student->other_info = $validated['teacher_remarks'];

        $student->save();

        return response()->json([
            'success' => true,
            'message' => 'Student details updated successfully in ERP.',
            'data' => $student,
        ]);
    }

    /**
     * Upload / Update student photo from mobile camera or gallery.
     */
    public function updatePhoto(Request $request, $id)
    {
        $user = auth()->user();
        $student = Student::where('school_id', $user->school_id ?? 1)->findOrFail($id);

        if (!$this->authorizeStudentAccess($user, $student)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. This student is not assigned to your classes.',
            ], 403);
        }

        $request->validate([
            'photo' => 'nullable|image|max:10240',
            'base64_photo' => 'nullable|string',
        ]);

        $photoPath = null;

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $fileName = 'student_' . $student->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $photoPath = $file->storeAs('students/photos', $fileName, 'public');
        } elseif ($request->filled('base64_photo')) {
            $base64 = $request->input('base64_photo');
            if (preg_match('/^data:image\/(\w+);base64,/', $base64, $type)) {
                $base64 = substr($base64, strpos($base64, ',') + 1);
                $type = strtolower($type[1]);
            } else {
                $type = 'jpg';
            }
            $imageContent = base64_decode($base64);
            $fileName = 'student_' . $student->id . '_' . time() . '.' . $type;
            $photoPath = 'students/photos/' . $fileName;
            \Illuminate\Support\Facades\Storage::disk('public')->put($photoPath, $imageContent);
        }

        if ($photoPath) {
            $student->photo = $photoPath;
            $student->save();

            return response()->json([
                'success' => true,
                'message' => 'Student photo updated successfully in ERP.',
                'photo_url' => asset('storage/' . $photoPath),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No photo image was provided.',
        ], 422);
    }

    /**
     * Verify staff has access to student's section.
     */
    private function authorizeStudentAccess($user, Student $student)
    {
        if ($user->hasRole('teacher')) {
            $staff = Staff::where('user_id', $user->id)->first()
                ?? Staff::where('school_id', $user->school_id ?? 1)->where(function($q) use ($user) {
                    $q->where('email', $user->email)->orWhere('phone', $user->phone);
                })->first();

            if (!$staff) {
                // If staff profile not explicitly mapped, check school tenant
                return (int)$student->school_id === (int)($user->school_id ?? 1);
            }

            $staffIds = array_values(array_filter([$staff->id, $user->id]));
            $secIdsFromCt = Section::where('school_id', $user->school_id ?? 1)
                ->where(function($q) use ($staffIds) {
                    $q->whereIn('class_teacher_id', $staffIds)
                      ->orWhereIn('assistant_class_teacher_id', $staffIds);
                })
                ->pluck('id')->toArray();
            $secIdsFromSss = SectionSubjectStaff::whereIn('staff_id', $staffIds)->pluck('section_id')->toArray();
            $assignedSectionIds = array_values(array_unique(array_filter(array_merge($secIdsFromCt, $secIdsFromSss))));

            if (!empty($assignedSectionIds)) {
                return in_array((int)$student->section_id, $assignedSectionIds) || (int)$student->school_id === (int)($user->school_id ?? 1);
            }
        }
        return (int)$student->school_id === (int)($user->school_id ?? 1);
    }
}
