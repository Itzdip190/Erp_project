<?php

namespace App\Http\Controllers\School\Student;

use App\Events\StudentAdmitted;
use App\Http\Controllers\Controller;
use App\Http\Requests\School\Student\BulkImportRequest;
use App\Http\Requests\School\Student\PromoteStudentRequest;
use App\Http\Requests\School\Student\StudentStoreRequest;
use App\Http\Requests\School\Student\StudentUpdateRequest;
use App\Jobs\ProcessStudentImport;
use App\Models\AcademicSession;
use App\Models\ImportLog;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentCategory;
use App\Models\StudentHouse;
use App\Models\StudentSession;
use App\Models\User;
use App\Services\StudentNumberService;
use App\Support\SearchHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class StudentController extends Controller
{
    public function __construct(protected StudentNumberService $studentNumberService)
    {
    }

    public function index(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $user = auth()->user();
        $isTeacher = $user && ($user->hasRole('teacher') || $user->role === 'teacher' || $user->hasRole('staff'));
        $staff = $isTeacher ? $user->staff : null;

        $assignedSectionIds = [];
        $assignedClassIds = [];
        if ($staff) {
            $secIdsFromCt = Section::where('school_id', $schoolId)
                ->where(function($q) use ($staff) {
                    $q->where('class_teacher_id', $staff->id)
                      ->orWhere('assistant_class_teacher_id', $staff->id);
                })
                ->pluck('id')->toArray();
            $secIdsFromSss = \App\Models\SectionSubjectStaff::where('school_id', $schoolId)->where('staff_id', $staff->id)->pluck('section_id')->toArray();
            $secIdsFromCells = \App\Models\ClassTimetableCell::where('school_id', $schoolId)
                ->where(function($q) use ($staff) {
                    $q->where('teacher_id', $staff->id);
                    if (\Illuminate\Support\Facades\Schema::hasColumn('class_timetable_cells', 'secondary_teacher_id')) {
                        $q->orWhere('secondary_teacher_id', $staff->id);
                    }
                })
                ->pluck('section_id')->toArray();
            $assignedSectionIds = array_unique(array_filter(array_merge($secIdsFromCt, $secIdsFromSss, $secIdsFromCells)));
            $assignedClassIds = Section::whereIn('id', $assignedSectionIds)->pluck('class_id')->unique()->filter()->toArray();
        }

        $page = $request->get('page', 1);

        $selectedSessionId = $request->get('academic_session_id');
        if (!$selectedSessionId || $selectedSessionId === '') {
            $currentSession = AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first()
                ?? AcademicSession::where('school_id', $schoolId)->first();
            $selectedSessionId = $currentSession?->id;
        }

        $filters = [
            'class_id' => $request->get('class_id'),
            'section_id' => $request->get('section_id'),
            'academic_session_id' => $selectedSessionId === 'all' ? null : $selectedSessionId,
            'is_active' => $request->get('is_active'),
            'search' => $request->get('search'),
            'status' => $request->get('status', 'active'),
            'sort' => $request->get('sort', 'asc'),
            'per_page' => $request->get('per_page'),
            'all' => $request->get('all'),
        ];

        $version = Cache::get('students_list_version_' . $schoolId, 'v1');
        $teacherCacheSuffix = $staff ? '_staff_' . $staff->id : '';
        $cacheKey = 'students_list_' . $schoolId . '_' . md5(json_encode($filters) . '_' . $page) . '_' . $version . $teacherCacheSuffix;

        $isFiltered = !empty($filters['class_id']) || !empty($filters['section_id']) || !empty($filters['search']) || $request->has('class_id') || $request->has('section_id') || $request->has('search');

        $students = Cache::remember($cacheKey, 120, function () use ($schoolId, $filters, $staff, $assignedSectionIds, $isFiltered) {
            if (!$isFiltered) {
                return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20, 1, [
                    'path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()
                ]);
            }

            if ($filters['status'] === 'deleted') {
                $query = Student::onlyTrashed()->with(['class', 'section', 'academicSession', 'pendingDeletionRequest', 'studentSessions.schoolClass', 'studentSessions.section'])
                                ->where('school_id', $schoolId);
            } else {
                $query = Student::with(['class', 'section', 'academicSession', 'pendingDeletionRequest', 'studentSessions.schoolClass', 'studentSessions.section'])
                                ->where('school_id', $schoolId);
            }

            if ($staff) {
                if (count($assignedSectionIds) > 0) {
                    $query->whereIn('section_id', $assignedSectionIds);
                } else {
                    $query->whereRaw('1 = 0');
                }
            }

            if ($filters['status'] === 'active') {
                $query->where('is_active', 1)
                      ->where('is_alumni', 0)
                      ->where(function($q) {
                          $q->where('is_transfer', 0)->orWhereNull('is_transfer');
                      })
                      ->where(function($q) {
                          $q->whereNull('tc_number')->orWhere('tc_number', '');
                      });
            } elseif ($filters['status'] === 'deactivated') {
                $query->where('is_active', 0)
                      ->where('is_alumni', 0)
                      ->where(function($q) {
                          $q->where('is_transfer', 0)->orWhereNull('is_transfer');
                      })
                      ->where(function($q) {
                          $q->whereNull('tc_number')->orWhere('tc_number', '');
                      });
            } elseif ($filters['status'] === 'transfer') {
                $query->where(function($q) {
                    $q->where('is_transfer', 1)
                      ->orWhereNotNull('tc_number')
                      ->orWhere('tc_number', '!=', '');
                });
            } elseif ($filters['status'] === 'alumni') {
                $query->where('is_alumni', 1);
            }

            if ($filters['academic_session_id']) {
                $sessionId = $filters['academic_session_id'];
                $classId = $filters['class_id'] ?? null;
                $sectionId = $filters['section_id'] ?? null;

                $query->whereHas('studentSessions', function ($sq) use ($sessionId, $classId, $sectionId) {
                    $sq->where('academic_session_id', $sessionId);
                    if ($classId) {
                        $sq->where('class_id', $classId);
                    }
                    if ($sectionId) {
                        if (is_numeric($sectionId)) {
                            $sq->where('section_id', $sectionId);
                        } else {
                            $sq->whereHas('section', function ($secQ) use ($sectionId) {
                                $secQ->where('name', $sectionId);
                            });
                        }
                    }
                });
            } else {
                if ($filters['class_id']) {
                    $query->where('class_id', $filters['class_id']);
                }
                if ($filters['section_id']) {
                    if (is_numeric($filters['section_id'])) {
                        $query->where('section_id', $filters['section_id']);
                    } else {
                        $query->whereHas('section', function ($q) use ($filters) {
                            $q->where('name', $filters['section_id']);
                        });
                    }
                }
            }
            if ($filters['is_active'] !== null && $filters['is_active'] !== '') {
                $query->where('is_active', $filters['is_active']);
            }
            if ($filters['search']) {
                SearchHelper::applyStudentSearch($query, $filters['search']);
            }

            if (($filters['sort'] ?? 'asc') === 'desc') {
                $query->orderBy('first_name', 'desc')->orderBy('last_name', 'desc');
            } else {
                $query->orderBy('first_name', 'asc')->orderBy('last_name', 'asc');
            }

            $perPage = 20;
            if (isset($filters['per_page']) && is_numeric($filters['per_page']) && $filters['per_page'] > 0) {
                $perPage = min((int) $filters['per_page'], 10000);
            } elseif (!empty($filters['all']) || (isset($filters['per_page']) && $filters['per_page'] === 'all')) {
                $perPage = 10000;
            }

            return $query->paginate($perPage);
        });

        if ($staff) {
            $classes = count($assignedClassIds) > 0 ? SchoolClass::whereIn('id', $assignedClassIds)->get() : collect();
            $sections = count($assignedSectionIds) > 0 ? Section::whereIn('id', $assignedSectionIds)->get() : collect();
        } else {
            $classes = SchoolClass::all();
            $sections = Section::all();
        }
        $academicSessions = AcademicSession::all();

        $suggestion = null;
        if ($students->total() === 0 && !empty($filters['search'])) {
            $suggestion = SearchHelper::getStudentSuggestion(
                $schoolId,
                $filters['search'],
                $filters['class_id'] ? (int) $filters['class_id'] : null,
                $filters['section_id'] && is_numeric($filters['section_id']) ? (int) $filters['section_id'] : null
            );
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $students->items(),
                'suggestion' => $suggestion,
                'isFiltered' => $isFiltered,
            ]);
        }

        return view('school.student.index', compact('students', 'classes', 'sections', 'academicSessions', 'filters', 'suggestion', 'isFiltered'));
    }

    public function create()
    {
        $classes = SchoolClass::all();
        $sections = Section::all();
        $academicSessions = AcademicSession::all();

        $schoolId = auth()->user()->school_id;
        $categoryNames = ['Gen', 'OBC', 'SC', 'ST'];
        $categories = [];
        foreach ($categoryNames as $name) {
            $categories[] = StudentCategory::firstOrCreate([
                'school_id' => $schoolId,
                'name' => $name
            ]);
        }

        $houses = StudentHouse::all();
        
        $routes = \App\Models\TransportRoute::where('school_id', $schoolId)->get();
        $vehicles = \App\Models\Vehicle::where('school_id', $schoolId)->where('status', true)->get();
        $stops = \App\Models\Stop::where('school_id', $schoolId)->get();

        $admData = $this->studentNumberService->getStudentPrefixAndNextSequence($schoolId);

        return view('school.student.create', compact('classes', 'sections', 'academicSessions', 'categories', 'houses', 'routes', 'vehicles', 'stops', 'admData'));
    }

    public function store(StudentStoreRequest $request)
    {
        $schoolId = auth()->user()->school_id;
        $data = $request->validated();

        $cleanFullName = trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''));
        $cleanFatherName = trim((string)($data['father_name'] ?? $data['guardian_name'] ?? ''));
        $dob = $data['date_of_birth'] ?? null;

        $existingStudentByIdentity = null;
        if (!empty($cleanFullName) && !empty($dob)) {
            $normFullName = strtolower(preg_replace('/\s+/', ' ', $cleanFullName));
            $normFather   = strtolower(preg_replace('/\s+/', ' ', $cleanFatherName));
            $cleanAlphaFullName = strtolower(preg_replace('/[^a-z0-9]/', '', $cleanFullName));
            $cleanAlphaFather   = strtolower(preg_replace('/[^a-z0-9]/', '', $cleanFatherName));

            $identityQuery = Student::withTrashed()
                ->where('school_id', $schoolId)
                ->whereDate('date_of_birth', $dob);

            if ($cleanAlphaFather !== '') {
                $identityQuery->where(function ($q) use ($normFather, $cleanAlphaFather) {
                    $q->whereRaw("LOWER(TRIM(father_name)) = ?", [$normFather])
                      ->orWhereRaw("LOWER(TRIM(guardian_name)) = ?", [$normFather])
                      ->orWhereRaw("LOWER(REPLACE(REPLACE(REPLACE(father_name, ' ', ''), '.', ''), '-', '')) = ?", [$cleanAlphaFather])
                      ->orWhereRaw("LOWER(REPLACE(REPLACE(REPLACE(guardian_name, ' ', ''), '.', ''), '-', '')) = ?", [$cleanAlphaFather]);
                });
            }

            $identityQuery->where(function ($q) use ($normFullName, $cleanAlphaFullName) {
                $q->whereRaw("LOWER(TRIM(CONCAT(first_name, ' ', COALESCE(last_name, '')))) = ?", [$normFullName])
                  ->orWhereRaw("LOWER(REPLACE(REPLACE(REPLACE(CONCAT(first_name, ' ', COALESCE(last_name, '')), ' ', ''), '.', ''), '-', '')) = ?", [$cleanAlphaFullName]);
            });

            $existingStudentByIdentity = $identityQuery->first();
        }

        $submittedAdmNumber = trim((string)($request->input('admission_number') ?? ''));
        $prefix = $request->input('admission_number_prefix');
        $seqInput = trim((string) $request->input('admission_number_seq', ''));
        if ($seqInput !== '') {
            $submittedAdmNumber = ($prefix ?? '') . $seqInput;
        }

        if ($existingStudentByIdentity) {
            // If existing student found by identity, preserve permanent admission number
            if ($submittedAdmNumber !== '' && strcasecmp($submittedAdmNumber, (string)$existingStudentByIdentity->admission_number) !== 0) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['admission_number' => "Admission Number mismatch for existing student {$existingStudentByIdentity->full_name}. The correct Admission Number is {$existingStudentByIdentity->admission_number}. Admission Number cannot be changed."]);
            }
            $data['admission_number'] = $existingStudentByIdentity->admission_number;
            $data['admission_sequence'] = $existingStudentByIdentity->admission_sequence;
            $data['admission_year'] = $existingStudentByIdentity->admission_year;
        } else {
            if ($submittedAdmNumber !== '') {
                $data['admission_number'] = $submittedAdmNumber;
            } else {
                $data['admission_number'] = $this->studentNumberService->generateAdmissionNumber($schoolId);
            }
            $admParsed = $this->studentNumberService->parseStudentAdmissionNumber($data['admission_number'], $schoolId);
            $seq = (int) ($admParsed['sequence'] ?? 1);
            if ($seq <= 0) {
                $seq = 1;
            }
            $admissionYear = (int) date('Y', strtotime($data['admission_date'] ?? 'now'));

            // Ensure sequence is unique for (school_id, admission_year) to respect DB unique constraint 'students_school_sequence_year_unique'
            while (Student::withTrashed()
                ->where('school_id', $schoolId)
                ->where('admission_year', $admissionYear)
                ->where('admission_sequence', $seq)
                ->exists()) {
                $seq++;
            }

            $data['admission_sequence'] = $seq;
            $data['admission_year'] = $admissionYear;
        }

        // Handle uploaded photo
        $data['opening_due_balance'] = $data['opening_due_balance'] ?? 0.00;
        $data['admission_type'] = $request->has('is_new_admission') ? 'New Admission' : 'Old Admission';

        if ($request->filled('captured_photo')) {
            $data['photo'] = $this->saveBase64Photo($request->input('captured_photo'), 'students/photos');
        } elseif ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('students/photos', 'public');
        }
        if ($request->hasFile('father_photo')) {
            $data['father_photo'] = $request->file('father_photo')->store('students/photos', 'public');
        }
        if ($request->hasFile('mother_photo')) {
            $data['mother_photo'] = $request->file('mother_photo')->store('students/photos', 'public');
        }
        if ($request->hasFile('guardian_photo')) {
            $data['guardian_photo'] = $request->file('guardian_photo')->store('students/photos', 'public');
        }

        $data['is_alumni'] = $request->has('is_alumni') ? 1 : 0;
        $data['is_transfer'] = $request->has('is_transfer') ? 1 : 0;
        $data['school_id'] = $schoolId;

        // Ensure non-null defaults for required database fields
        if (empty($data['guardian_name'])) {
            $data['guardian_name'] = !empty($data['father_name']) ? $data['father_name'] : (!empty($data['mother_name']) ? $data['mother_name'] : 'Guardian');
        }
        if (empty($data['guardian_phone'])) {
            $data['guardian_phone'] = !empty($data['father_phone']) ? $data['father_phone'] : (!empty($data['mother_phone']) ? $data['mother_phone'] : ($data['phone'] ?? '0000000000'));
        }
        if (empty($data['guardian_relationship'])) {
            $data['guardian_relationship'] = !empty($data['father_name']) ? 'father' : (!empty($data['mother_name']) ? 'mother' : 'guardian');
        }
        if (empty($data['address'])) {
            $data['address'] = !empty($data['permanent_address']) ? $data['permanent_address'] : 'N/A';
        }
        if (empty($data['city'])) {
            $data['city'] = !empty($data['permanent_city']) ? $data['permanent_city'] : 'N/A';
        }
        if (empty($data['state'])) {
            $data['state'] = !empty($data['permanent_state']) ? $data['permanent_state'] : 'N/A';
        }
        if (empty($data['pincode'])) {
            $data['pincode'] = !empty($data['permanent_pincode']) ? $data['permanent_pincode'] : '000000';
        }
        if (empty($data['admission_date'])) {
            $data['admission_date'] = date('Y-m-d');
        }
        if (!isset($data['last_name']) || $data['last_name'] === null) {
            $data['last_name'] = '';
        }

        $student = DB::transaction(function () use ($schoolId, &$data) {
            // 1. Create / find student user account
            $cleanFirstName   = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $data['first_name'] ?? ''));
            $cleanLastName    = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $data['last_name'] ?? ''));
            $cleanAdmissionId = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $data['admission_number'] ?? ''));

            $currentSchool    = \App\Models\School::find($schoolId);
            $schoolCode       = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $currentSchool?->code ?? ('sch' . $schoolId)));

            $emailPrefixParts = array_filter([$cleanFirstName, $cleanLastName, $cleanAdmissionId]);
            $defaultStudentEmail = implode('.', $emailPrefixParts) . '@student.' . $schoolCode . '.com';
            $studentEmail = (!empty($data['email'])) ? $data['email'] : $defaultStudentEmail;

            // Search by email globally across the users table
            $existingUser = User::withoutGlobalScope(\App\Models\Scopes\SchoolScope::class)
                ->where('email', $studentEmail)
                ->first();

            // Check if existing user is an Admin/Staff/Teacher account
            $isAdminOrStaff = $existingUser && (
                $existingUser->hasRole('superadmin') ||
                $existingUser->hasRole('school_admin') ||
                $existingUser->hasRole('admin') ||
                $existingUser->hasRole('staff') ||
                $existingUser->hasRole('teacher') ||
                $existingUser->hasRole('accountant') ||
                in_array($existingUser->role, ['superadmin', 'school_admin', 'admin', 'school', 'staff', 'teacher'])
            );

            if ($existingUser && !$isAdminOrStaff) {
                $studentUser = $existingUser;
                $studentUser->update([
                    'school_id' => $schoolId,
                    'name'      => trim($data['first_name'] . ' ' . $data['last_name']),
                    'phone'     => $data['guardian_phone'] ?? $studentUser->phone,
                    'is_active' => true,
                ]);
                if (!$studentUser->hasRole('student')) {
                    $studentUser->assignRole('student');
                }
            } else {
                if ($isAdminOrStaff) {
                    $studentEmail = $defaultStudentEmail;
                    $studentUser = User::withoutGlobalScope(\App\Models\Scopes\SchoolScope::class)
                        ->where('email', $studentEmail)
                        ->first();
                } else {
                    $studentUser = null;
                }

                if (!$studentUser) {
                    $studentUser = User::create([
                        'school_id' => $schoolId,
                        'name'      => trim($data['first_name'] . ' ' . $data['last_name']),
                        'email'     => $studentEmail,
                        'phone'     => $data['guardian_phone'] ?? null,
                        'password'  => Hash::make('Student@2026!'),
                        'is_active' => true,
                    ]);
                    $studentUser->assignRole('student');
                }
            }

            // 2. Create / find parent user account if guardian email is provided
            if (!empty($data['guardian_email'])) {
                $parentUser = User::withoutGlobalScope(\App\Models\Scopes\SchoolScope::class)
                    ->where('email', $data['guardian_email'])
                    ->first();

                if ($parentUser) {
                    // Existing user found — update their name/phone and ensure they have the parent role
                    $parentUser->update([
                        'school_id' => $parentUser->school_id ?? $schoolId,
                        'name'      => $data['guardian_name'] ?? $parentUser->name,
                        'phone'     => $data['guardian_phone'] ?? $parentUser->phone,
                        'is_active' => true,
                    ]);
                    if (!$parentUser->hasRole('parent')) {
                        $parentUser->assignRole('parent');
                    }
                } else {
                    $parentUser = User::create([
                        'school_id' => $schoolId,
                        'name'      => $data['guardian_name'],
                        'email'     => $data['guardian_email'],
                        'phone'     => $data['guardian_phone'],
                        'password'  => Hash::make('schoolcloud123'),
                        'is_active' => true,
                    ]);
                    $parentUser->assignRole('parent');
                }
            }

            $data['user_id'] = $studentUser->id;

            // Strip any keys not in actual DB table schema to avoid "Unknown column" errors
            // on live servers where some migrations may not have been run yet
            $dbColumns = \Illuminate\Support\Facades\Schema::getColumnListing('students');
            $safeData = array_intersect_key($data, array_flip($dbColumns));

            $existingStudent = Student::withTrashed()
                ->where('school_id', $schoolId)
                ->where('admission_number', $data['admission_number'])
                ->first();

            if ($existingStudent) {
                if ($existingStudent->trashed()) {
                    $existingStudent->restore();
                }
                $student = $existingStudent;
                // Only update active pointers if this session is the active session
                $student->update($safeData);
            } else {
                $student = Student::create($safeData);
            }

            // Build session profile snapshot
            $sessionProfileKeys = $this->getStudentProfileKeys();
            $sessionData = [];
            foreach ($sessionProfileKeys as $key) {
                if (array_key_exists($key, $data) && $data[$key] !== null) {
                    $sessionData[$key] = $data[$key];
                }
            }
            if (!empty($student->photo) && empty($sessionData['photo'])) {
                $sessionData['photo'] = $student->photo;
            }

            StudentSession::updateOrCreate(
                [
                    'school_id'           => $schoolId,
                    'student_id'          => $student->id,
                    'academic_session_id' => $data['academic_session_id'],
                ],
                [
                    'class_id'     => $data['class_id'],
                    'section_id'   => $data['section_id'],
                    'roll_number'  => $data['roll_number'] ?? $this->studentNumberService->generateRollNumber($data['section_id'], $data['academic_session_id']),
                    'is_promoted'  => false,
                    'session_data' => $sessionData,
                ]
            );

            return $student;
        });

        // Store Student Documents if provided
        if ($request->hasFile('documents')) {
            $docFiles = $request->file('documents');
            $docTypes = $request->input('document_types', []);
            foreach ($docFiles as $index => $file) {
                if ($file && $file->isValid()) {
                    $docType = !empty($docTypes[$index]) ? $docTypes[$index] : 'Other Document';
                    $path = $file->store('students/documents', 'public');
                    \App\Models\StudentDocument::create([
                        'school_id'     => $schoolId,
                        'student_id'    => $student->id,
                        'document_type' => $docType,
                        'file_path'     => $path,
                        'original_name' => $file->getClientOriginalName(),
                    ]);
                }
            }
        }

        // Flush student list cache keys for this school
        Cache::forget('students_list_version_' . $schoolId);
        Cache::put('students_list_version_' . $schoolId, time(), 86400);

        // Sync transport and other fees
        \App\Http\Controllers\School\FeeManagementController::syncStudentFees($student);

        // Send Central Notification
        $studentFullName = trim($student->first_name . ' ' . ($student->last_name ?? ''));
        \App\Services\NotificationService::send([
            'school_id'      => $schoolId,
            'recipient_role' => 'school_admin',
            'title'          => 'New Student Admission',
            'message'        => "Student {$studentFullName} (Adm No: {$student->admission_number}) admitted successfully.",
            'module'         => 'admission',
            'type'           => 'student_admission',
            'related_id'     => $student->id,
            'priority'       => 'normal',
            'action_url'     => route('school.students.show', $student->id),
            'icon'           => 'fa-user-plus',
            'color'          => '#10b981',
        ]);

        event(new StudentAdmitted($student));


        return redirect()->route('school.students.index')->with('success', 'Student admitted successfully.');
    }

    public function toggleStatus(Student $student)
    {
        $schoolId = auth()->user()->school_id;
        if ($student->school_id !== $schoolId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $student->is_active = !$student->is_active;
        $student->save();

        Cache::forget('students_list_version_' . $schoolId);
        Cache::put('students_list_version_' . $schoolId, time(), 86400);

        if (!$student->is_active) {
            \Illuminate\Support\Facades\Log::info("Parent Notification: Student {$student->full_name} has been deactivated. Guardian email: {$student->guardian_email}, Phone: {$student->guardian_phone}.");
            $msg = 'Student deactivated successfully. Parent notified.';
        } else {
            \Illuminate\Support\Facades\Log::info("Parent Notification: Student {$student->full_name} has been activated. Guardian email: {$student->guardian_email}, Phone: {$student->guardian_phone}.");
            $msg = 'Student activated successfully. Parent notified.';
        }

        return response()->json([
            'success' => true,
            'is_active' => $student->is_active,
            'message' => $msg
        ]);
    }

    public function show(Student $student)
    {
        $schoolId = auth()->user()->school_id;
        if ($student->school_id !== $schoolId) {
            abort(403, 'Unauthorized.');
        }

        $user = auth()->user();
        $isTeacher = $user && ($user->hasRole('teacher') || $user->role === 'teacher' || $user->hasRole('staff'));
        $staff = $isTeacher ? $user->staff : null;
        if ($staff) {
            $secIdsFromCt = Section::where('school_id', $schoolId)
                ->where(function($q) use ($staff) {
                    $q->where('class_teacher_id', $staff->id)
                      ->orWhere('assistant_class_teacher_id', $staff->id);
                })
                ->pluck('id')->toArray();
            $secIdsFromSss = \App\Models\SectionSubjectStaff::where('school_id', $schoolId)->where('staff_id', $staff->id)->pluck('section_id')->toArray();
            $secIdsFromCells = \App\Models\ClassTimetableCell::where('school_id', $schoolId)
                ->where(function($q) use ($staff) {
                    $q->where('teacher_id', $staff->id);
                    if (\Illuminate\Support\Facades\Schema::hasColumn('class_timetable_cells', 'secondary_teacher_id')) {
                        $q->orWhere('secondary_teacher_id', $staff->id);
                    }
                })
                ->pluck('section_id')->toArray();
            $assignedSectionIds = array_unique(array_filter(array_merge($secIdsFromCt, $secIdsFromSss, $secIdsFromCells)));
            if (!in_array($student->section_id, $assignedSectionIds)) {
                abort(403, 'Unauthorized: You are not assigned to this student\'s section.');
            }
        }

        // 1. Attendance
        $attendances = \App\Models\StudentAttendance::where('student_id', $student->id)
            ->orderBy('date', 'desc')
            ->get();
        $totalDays = $attendances->count();
        $presentDays = $attendances->where('status', 'present')->count();
        $absentDays = $attendances->where('status', 'absent')->count();
        $lateDays = $attendances->where('status', 'late')->count();
        $attendancePercentage = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 1) : 100;

        // 2. Siblings
        $siblings = Student::where('school_id', $schoolId)
            ->where('id', '!=', $student->id)
            ->where(function($q) use ($student) {
                $hasFilter = false;
                if ($student->guardian_email) {
                    $q->where('guardian_email', $student->guardian_email);
                    $hasFilter = true;
                }
                if ($student->guardian_phone) {
                    if ($hasFilter) $q->orWhere('guardian_phone', $student->guardian_phone);
                    else { $q->where('guardian_phone', $student->guardian_phone); $hasFilter = true; }
                }
                if ($student->father_phone) {
                    if ($hasFilter) $q->orWhere('father_phone', $student->father_phone);
                    else { $q->where('father_phone', $student->father_phone); $hasFilter = true; }
                }
                if ($student->mother_phone) {
                    if ($hasFilter) $q->orWhere('mother_phone', $student->mother_phone);
                    else { $q->where('mother_phone', $student->mother_phone); $hasFilter = true; }
                }
                if (!$hasFilter) {
                    $q->whereRaw('1 = 0');
                }
            })->get();

        // 3. Exams (marks)
        $marks = \App\Models\StudentMark::where('student_id', $student->id)
            ->with('subject')
            ->orderBy('exam_name', 'asc')
            ->get();

        // 4. Fees
        $fees = \App\Models\StudentFee::where('student_id', $student->id)
            ->with(['category', 'component'])
            ->orderBy('due_date', 'asc')
            ->get();

        // 5. Refunds
        $refunds = \App\Models\FeeRefund::where('student_id', $student->id)
            ->orderBy('refund_date', 'desc')
            ->get();

        // 6. Receipts / Invoices
        $receipts = \App\Models\FeeReceipt::where('student_id', $student->id)
            ->orderBy('payment_date', 'desc')
            ->get();

        // 7. Bus Attendance
        $busAttendances = \App\Models\BusAttendance::where('student_id', $student->id)
            ->orderBy('date', 'desc')
            ->get();

        // 8. Offline/Class Tests
        $offlineTests = \App\Models\OfflineTest::where('school_id', $schoolId)
            ->where('class_id', $student->class_id)
            ->where(function($q) use ($student) {
                $q->whereNull('section_id')->orWhere('section_id', $student->section_id);
            })
            ->with('subject')
            ->orderBy('start_date_time', 'desc')
            ->get();

        // 9. Leaves
        $leaves = ($student && \Illuminate\Support\Facades\Schema::hasTable('student_leave_applications')) 
            ? \App\Models\StudentLeaveApplication::where('school_id', $schoolId)
                ->where('student_id', $student->id)
                ->orderBy('from_date', 'desc')
                ->get()
            : collect();

        // 10. Documents
        $documents = \App\Models\StudentDocument::where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // 11. Gate Passes
        $gatePasses = \App\Models\StudentGatePass::where('student_id', $student->id)
            ->orderBy('pass_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        return view('school.student.show', compact(
            'student',
            'attendances',
            'totalDays',
            'presentDays',
            'absentDays',
            'lateDays',
            'attendancePercentage',
            'siblings',
            'marks',
            'fees',
            'refunds',
            'receipts',
            'busAttendances',
            'offlineTests',
            'leaves',
            'documents',
            'gatePasses'
        ));
    }

    public function edit(Request $request, Student $student)
    {
        $classes = SchoolClass::all();
        $sections = Section::all();
        $academicSessions = AcademicSession::all();

        $schoolId = auth()->user()->school_id;
        $categoryNames = ['Gen', 'OBC', 'SC', 'ST'];
        $categories = [];
        foreach ($categoryNames as $name) {
            $categories[] = StudentCategory::firstOrCreate([
                'school_id' => $schoolId,
                'name' => $name
            ]);
        }

        $houses = StudentHouse::all();

        $routes = \App\Models\TransportRoute::where('school_id', $schoolId)->get();
        $vehicles = \App\Models\Vehicle::where('school_id', $schoolId)->where('status', true)->get();
        $stops = \App\Models\Stop::where('school_id', $schoolId)->get();
        $admData = $this->studentNumberService->parseStudentAdmissionNumber($student->admission_number, $schoolId);

        $selectedSessionId = $request->get('academic_session_id');
        if (!$selectedSessionId || $selectedSessionId === 'all') {
            $selectedSessionId = $student->academic_session_id;
        }

        // Find session-specific enrollment record
        $studentSession = StudentSession::where('school_id', $schoolId)
            ->where('student_id', $student->id)
            ->where('academic_session_id', $selectedSessionId)
            ->first();

        $sessionClassId = $studentSession?->class_id ?? $student->class_id;
        $sessionSectionId = $studentSession?->section_id ?? $student->section_id;
        $sessionRollNumber = $studentSession?->roll_number ?? $student->roll_number;
        $sessionAcademicSessionId = $studentSession?->academic_session_id ?? $selectedSessionId;

        $sessionPhoto = $studentSession?->getProfileAttribute('photo') ?? $student->photo;
        $sessionPhotoUrl = $student->resolvePhotoUrl($selectedSessionId) ?? asset('images/avatar-student.png');

        return view('school.student.edit', compact(
            'student',
            'classes',
            'sections',
            'academicSessions',
            'categories',
            'houses',
            'routes',
            'vehicles',
            'stops',
            'admData',
            'studentSession',
            'selectedSessionId',
            'sessionClassId',
            'sessionSectionId',
            'sessionRollNumber',
            'sessionAcademicSessionId',
            'sessionPhoto',
            'sessionPhotoUrl'
        ));
    }

    public function update(StudentUpdateRequest $request, Student $student)
    {
        $schoolId = auth()->user()->school_id;
        $data = $request->validated();
        
        // Enforce Admission Number immutability for existing students
        if (!empty($student->admission_number)) {
            $data['admission_number']   = $student->admission_number;
            $data['admission_sequence'] = $student->admission_sequence;
            $data['admission_year']     = $student->admission_year;
        } else {
            if (!empty($data['admission_number'])) {
                $admParsed = $this->studentNumberService->parseStudentAdmissionNumber($data['admission_number'], $schoolId);
                $seq = (int) ($admParsed['sequence'] ?? 1);
                if ($seq <= 0) {
                    $seq = 1;
                }
                $admissionYear = (int) date('Y');

                while (Student::withTrashed()
                    ->where('school_id', $schoolId)
                    ->where('admission_year', $admissionYear)
                    ->where('admission_sequence', $seq)
                    ->where('id', '!=', $student->id)
                    ->exists()) {
                    $seq++;
                }

                $data['admission_sequence'] = $seq;
                $data['admission_year'] = $admissionYear;
            }
        }
        $data['admission_type'] = $request->has('is_new_admission') ? 'New Admission' : 'Old Admission';
        $data['is_alumni'] = $request->has('is_alumni') ? 1 : 0;
        $data['is_transfer'] = $request->has('is_transfer') ? 1 : 0;
        if (array_key_exists('opening_due_balance', $data)) {
            $data['opening_due_balance'] = $data['opening_due_balance'] ?? 0.00;
        }

        if ($request->filled('captured_photo')) {
            $data['photo'] = $this->saveBase64Photo($request->input('captured_photo'), 'students/photos');
        } elseif ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('students/photos', 'public');
        }
        if ($request->hasFile('father_photo')) {
            if ($student->father_photo) {
                Storage::disk('public')->delete($student->father_photo);
            }
            $data['father_photo'] = $request->file('father_photo')->store('students/photos', 'public');
        }
        if ($request->hasFile('mother_photo')) {
            if ($student->mother_photo) {
                Storage::disk('public')->delete($student->mother_photo);
            }
            $data['mother_photo'] = $request->file('mother_photo')->store('students/photos', 'public');
        }
        // Ensure non-null defaults for required database fields
        if (empty($data['guardian_name'])) {
            $data['guardian_name'] = !empty($data['father_name']) ? $data['father_name'] : (!empty($data['mother_name']) ? $data['mother_name'] : ($student->guardian_name ?? 'Guardian'));
        }
        if (empty($data['guardian_phone'])) {
            $data['guardian_phone'] = !empty($data['father_phone']) ? $data['father_phone'] : (!empty($data['mother_phone']) ? $data['mother_phone'] : ($student->guardian_phone ?? ($data['phone'] ?? '0000000000')));
        }
        if (empty($data['guardian_relationship'])) {
            $data['guardian_relationship'] = $student->guardian_relationship ?? (!empty($data['father_name']) ? 'father' : (!empty($data['mother_name']) ? 'mother' : 'guardian'));
        }
        if (empty($data['address'])) {
            $data['address'] = $student->address ?? (!empty($data['permanent_address']) ? $data['permanent_address'] : 'N/A');
        }
        if (empty($data['city'])) {
            $data['city'] = $student->city ?? (!empty($data['permanent_city']) ? $data['permanent_city'] : 'N/A');
        }
        if (empty($data['state'])) {
            $data['state'] = $student->state ?? (!empty($data['permanent_state']) ? $data['permanent_state'] : 'N/A');
        }
        if (empty($data['pincode'])) {
            $data['pincode'] = $student->pincode ?? (!empty($data['permanent_pincode']) ? $data['permanent_pincode'] : '000000');
        }
        if (!isset($data['last_name']) || $data['last_name'] === null) {
            $data['last_name'] = '';
        }

        DB::transaction(function () use ($schoolId, $student, &$data) {
            $cleanFirstName   = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $data['first_name'] ?? ''));
            $cleanLastName    = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $data['last_name'] ?? ''));
            $cleanAdmissionId = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $student->admission_number ?? ''));
            
            $currentSchool    = \App\Models\School::find($schoolId);
            $schoolCode       = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $currentSchool?->code ?? ('sch' . $schoolId)));

            $emailPrefixParts = array_filter([$cleanFirstName, $cleanLastName, $cleanAdmissionId]);
            $defaultStudentEmail = implode('.', $emailPrefixParts) . '@student.' . $schoolCode . '.com';
            $targetStudentEmail = !empty($data['email']) ? trim($data['email']) : ($student->email ? trim($student->email) : $defaultStudentEmail);

            $studentUser = $student->user_id
                ? User::withoutGlobalScope(\App\Models\Scopes\SchoolScope::class)->find($student->user_id)
                : null;

            if ($studentUser) {
                $isAdminOrStaff = (
                    $studentUser->hasRole('superadmin') ||
                    $studentUser->hasRole('school_admin') ||
                    $studentUser->hasRole('admin') ||
                    $studentUser->hasRole('staff') ||
                    $studentUser->hasRole('teacher') ||
                    in_array($studentUser->role, ['superadmin', 'school_admin', 'admin', 'school', 'staff', 'teacher'])
                );
                if ($isAdminOrStaff || $studentUser->school_id != $schoolId) {
                    $studentUser = null;
                }
            }

            if (!$studentUser) {
                $studentUser = User::withoutGlobalScope(\App\Models\Scopes\SchoolScope::class)
                    ->where('email', $targetStudentEmail)
                    ->first();
            }

            if (!$studentUser) {
                $studentUser = User::create([
                    'school_id' => $schoolId,
                    'name'      => trim($data['first_name'] . ' ' . $data['last_name']),
                    'email'     => $targetStudentEmail,
                    'phone'     => $data['guardian_phone'] ?? null,
                    'password'  => Hash::make('Student@2026!'),
                    'is_active' => true,
                ]);
                $studentUser->assignRole('student');
            } else {
                $studentUser->update([
                    'name'      => trim($data['first_name'] . ' ' . $data['last_name']),
                    'email'     => $targetStudentEmail,
                    'phone'     => $data['guardian_phone'] ?? $studentUser->phone,
                    'is_active' => true,
                ]);
                if (!$studentUser->hasRole('student')) {
                    $studentUser->assignRole('student');
                }
            }
            $data['user_id'] = $studentUser->id;

            // 2. Manage parent user account
            $parentEmail = !empty($data['guardian_email']) ? trim($data['guardian_email']) : (!empty($data['father_email']) ? trim($data['father_email']) : null);
            if (!empty($parentEmail)) {
                $parentUser = User::withoutGlobalScope(\App\Models\Scopes\SchoolScope::class)
                    ->where('email', $parentEmail)
                    ->first();

                if (!$parentUser) {
                    $parentUser = User::create([
                        'school_id' => $schoolId,
                        'name'      => $data['father_name'] ?? ($data['guardian_name'] ?? 'Parent'),
                        'email'     => $parentEmail,
                        'phone'     => $data['father_phone'] ?? ($data['guardian_phone'] ?? null),
                        'password'  => Hash::make('Student@2026!'),
                        'is_active' => true,
                    ]);
                    $parentUser->assignRole('parent');
                } else {
                    $parentUser->update([
                        'name'      => $data['father_name'] ?? ($data['guardian_name'] ?? $parentUser->name),
                        'phone'     => $data['father_phone'] ?? ($data['guardian_phone'] ?? $parentUser->phone),
                        'is_active' => true,
                    ]);
                    if (!$parentUser->hasRole('parent')) {
                        $parentUser->assignRole('parent');
                    }
                }
            }

            // 3. Freeze any other existing sessions of this student that don't have session_data yet
            $academicSessionId = (int) $data['academic_session_id'];
            $classId = $data['class_id'];
            $sectionId = $data['section_id'];
            $rollNumber = $data['roll_number'] ?? $student->roll_number;
            $sessionProfileKeys = $this->getStudentProfileKeys();

            $otherSessions = StudentSession::where('school_id', $student->school_id)
                ->where('student_id', $student->id)
                ->where('academic_session_id', '!=', $academicSessionId)
                ->get();

            foreach ($otherSessions as $otherSession) {
                if (empty($otherSession->session_data)) {
                    $freezeData = [];
                    foreach ($sessionProfileKeys as $key) {
                        $freezeData[$key] = $student->getAttribute($key);
                    }
                    $otherSession->update(['session_data' => $freezeData]);
                }
            }

            // 4. Update or create student session for the specific academic year being edited
            $targetSession = StudentSession::firstOrNew([
                'school_id'           => $student->school_id,
                'student_id'          => $student->id,
                'academic_session_id' => $academicSessionId,
            ]);

            $currentSessionData = $targetSession->session_data ?? [];
            foreach ($sessionProfileKeys as $key) {
                if (array_key_exists($key, $data)) {
                    $currentSessionData[$key] = $data[$key];
                } elseif (!array_key_exists($key, $currentSessionData)) {
                    $currentSessionData[$key] = $student->getAttribute($key);
                }
            }

            $targetSession->class_id     = $classId;
            $targetSession->section_id   = $sectionId;
            $targetSession->roll_number  = $rollNumber;
            $targetSession->session_data = $currentSessionData;
            $targetSession->save();

            // 5. Update permanent master student identity attributes
            $dbColumns = \Illuminate\Support\Facades\Schema::getColumnListing('students');
            $safeData = array_intersect_key($data, array_flip($dbColumns));

            // Check if this edited session is the latest session or current session
            $latestSessionId = StudentSession::where('school_id', $student->school_id)
                ->where('student_id', $student->id)
                ->max('academic_session_id');

            if (!$latestSessionId || $latestSessionId == $academicSessionId || $student->academic_session_id == $academicSessionId) {
                $safeData['class_id']            = $classId;
                $safeData['section_id']          = $sectionId;
                $safeData['roll_number']         = $rollNumber;
                $safeData['academic_session_id'] = $academicSessionId;
            } else {
                // When editing a historical session, keep master pointer to the student's latest active session
                unset($safeData['class_id'], $safeData['section_id'], $safeData['roll_number'], $safeData['academic_session_id']);
                // Do not overwrite active profile master fields when editing a historical session
                foreach ($sessionProfileKeys as $key) {
                    unset($safeData[$key]);
                }
            }

            if (!empty($safeData)) {
                $student->update($safeData);
            }
        });

        // Store Student Documents if provided
        if ($request->hasFile('documents')) {
            $docFiles = $request->file('documents');
            $docTypes = $request->input('document_types', []);
            foreach ($docFiles as $index => $file) {
                if ($file && $file->isValid()) {
                    $docType = !empty($docTypes[$index]) ? $docTypes[$index] : 'Other Document';
                    $path = $file->store('students/documents', 'public');
                    \App\Models\StudentDocument::create([
                        'school_id'     => $schoolId,
                        'student_id'    => $student->id,
                        'document_type' => $docType,
                        'file_path'     => $path,
                        'original_name' => $file->getClientOriginalName(),
                    ]);
                }
            }
        }

        Cache::forget('students_list_version_' . $schoolId);
        Cache::put('students_list_version_' . $schoolId, time(), 86400);

        // Sync transport and other fees
        \App\Http\Controllers\School\FeeManagementController::syncStudentFees($student);

        return redirect()->route('school.students.index')->with('success', 'Student updated successfully.');
    }

    public function destroy(Request $request, Student $student)
    {
        $schoolId = auth()->user()->school_id;
        $sessionId = $request->input('academic_session_id') ?: $request->get('academic_session_id');
        if (!$sessionId || $sessionId === 'all') {
            $currentSession = AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first()
                ?? AcademicSession::where('school_id', $schoolId)->first();
            $sessionId = $currentSession?->id ?? $student->academic_session_id;
        }

        // Delegate to StudentDeletionRequestController store to enforce approval workflow
        $controller = app(\App\Http\Controllers\School\Student\StudentDeletionRequestController::class);
        $request->merge([
            'student_id'          => $student->id,
            'academic_session_id' => $sessionId,
        ]);
        return $controller->store($request);
    }

    public function bulkDestroy(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $bulkSessionId = $request->input('academic_session_id');
        if (!$bulkSessionId || $bulkSessionId === '') {
            $currentSession = AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first()
                ?? AcademicSession::where('school_id', $schoolId)->first();
            $bulkSessionId = $currentSession?->id;
        }

        if ($request->boolean('delete_all')) {
            $query = Student::where('school_id', $schoolId);
            $classId = $request->input('class_id');
            $sectionId = $request->input('section_id');

            if ($bulkSessionId && $bulkSessionId !== 'all') {
                $query->whereHas('studentSessions', function ($sq) use ($bulkSessionId, $classId, $sectionId) {
                    $sq->where('academic_session_id', $bulkSessionId);
                    if ($classId) {
                        $sq->where('class_id', $classId);
                    }
                    if ($sectionId) {
                        if (is_numeric($sectionId)) {
                            $sq->where('section_id', $sectionId);
                        } else {
                            $sq->whereHas('section', function ($secQ) use ($sectionId) {
                                $secQ->where('name', $sectionId);
                            });
                        }
                    }
                });
            } else {
                if ($classId) {
                    $query->where('class_id', $classId);
                }
                if ($sectionId) {
                    if (is_numeric($sectionId)) {
                        $query->where('section_id', $sectionId);
                    } else {
                        $query->whereHas('section', function ($q) use ($sectionId) {
                            $query->where('name', $sectionId);
                        });
                    }
                }
            }

            $status = $request->input('status', 'active');
            if ($status === 'active') {
                $query->where('is_active', 1)
                      ->where('is_alumni', 0)
                      ->where(function($q) {
                          $q->where('is_transfer', 0)->orWhereNull('is_transfer');
                      })
                      ->where(function($q) {
                          $q->whereNull('tc_number')->orWhere('tc_number', '');
                      });
            } elseif ($request->input('is_active') !== null && $request->input('is_active') !== '') {
                $query->where('is_active', $request->input('is_active'));
            }

            if ($request->filled('search')) {
                SearchHelper::applyStudentSearch($query, $request->input('search'));
            }

            $students = $query->get();
            $deactivatedCount = 0;
            foreach ($students as $student) {
                if ($student->is_active) {
                    $student->update(['is_active' => 0]);
                    \Illuminate\Support\Facades\Log::info("Parent Notification: Student {$student->full_name} has been deactivated. Guardian email: {$student->guardian_email}, Phone: {$student->guardian_phone}.");
                    $deactivatedCount++;
                }
            }

            Cache::forget('students_list_version_' . $schoolId);
            Cache::put('students_list_version_' . $schoolId, time(), 86400);

            return response()->json([
                'success' => true,
                'message' => "Successfully deactivated {$deactivatedCount} student(s)."
            ]);
        }

        $studentIds = $request->input('student_ids', []);

        if (empty($studentIds)) {
            return response()->json([
                'success' => false,
                'message' => 'No students selected for deactivation.'
            ], 422);
        }

        $students = Student::where('school_id', $schoolId)->whereIn('id', $studentIds)->get();
        $deactivatedCount = 0;
        foreach ($students as $student) {
            if ($student->is_active) {
                $student->update(['is_active' => 0]);
                \Illuminate\Support\Facades\Log::info("Parent Notification: Student {$student->full_name} has been deactivated. Guardian email: {$student->guardian_email}, Phone: {$student->guardian_phone}.");
                $deactivatedCount++;
            }
        }

        Cache::forget('students_list_version_' . $schoolId);
        Cache::put('students_list_version_' . $schoolId, time(), 86400);

        return response()->json([
            'success' => true,
            'message' => "Successfully deactivated {$deactivatedCount} student(s)."
        ]);
    }

    public function bulkImport(BulkImportRequest $request)
    {
        $schoolId = auth()->user()->school_id;

        $path = $request->file('file')->store('students/imports', config('filesystems.default'));

        $importLog = ImportLog::create([
            'school_id' => $schoolId,
            'file_path' => $path,
            'status' => 'pending',
        ]);

        try {
            $absolutePath = Storage::disk(config('filesystems.default'))->path($path);
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($absolutePath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();
            
            // Count actual non-empty rows (excluding header)
            $totalRows = 0;
            if (count($rows) > 1) {
                // Normalize headers to identify name/admission columns
                $headers = [];
                foreach (($rows[0] ?? []) as $colIndex => $rawHeader) {
                    if ($rawHeader) {
                        $clean = preg_replace('/_+/', '_', trim(preg_replace('/[^a-z0-9]/', '_', strtolower(trim((string)$rawHeader))), '_'));
                        $headers[$colIndex] = $clean;
                    } else {
                        $headers[$colIndex] = null;
                    }
                }

                $isRowValidData = function($row) use ($headers) {
                    foreach ($headers as $colIndex => $header) {
                        if ($header && in_array($header, ['first_name', 'name'])) {
                            $val = trim((string)($row[$colIndex] ?? ''));
                            if ($val !== '') {
                                return true;
                            }
                        }
                    }
                    return false;
                };

                $dataRows = array_slice($rows, 1);
                foreach ($dataRows as $row) {
                    if ($isRowValidData($row)) {
                        $totalRows++;
                    } else {
                        // Stop counting completely when empty row is hit
                        break;
                    }
                }
            }

            $importLog->update([
                'total_rows' => $totalRows,
                'status' => 'pending'
            ]);

            if (app()->environment('testing')) {
                $studentNumberService = app(\App\Services\StudentNumberService::class);
                $job = new \App\Jobs\ProcessStudentImport($schoolId, $importLog->id, $path);
                $job->handle($studentNumberService);
            }

            return response()->json([
                'success' => true,
                'import_log_id' => $importLog->id,
                'total_rows' => $totalRows,
            ]);
        } catch (\Exception $e) {
            $importLog->update([
                'status' => 'failed',
                'errors' => [['row' => 0, 'error' => 'Failed to initialize spreadsheet: ' . $e->getMessage()]]
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to initialize spreadsheet: ' . $e->getMessage()
            ], 422);
        }
    }

    public function processImport(ImportLog $importLog)
    {
        $schoolId = auth()->user()->school_id;
        if ($importLog->school_id !== $schoolId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            // Close session write to allow concurrent progress reading
            if (session_id()) {
                session_write_close();
            }

            // Launch import command in the background
            if ($importLog->status !== 'processing' && $importLog->status !== 'completed') {
                $importLog->update(['status' => 'processing']);

                if (app()->environment('testing')) {
                    $studentNumberService = app(\App\Services\StudentNumberService::class);
                    $job = new \App\Jobs\ProcessStudentImport((int)$schoolId, (int)$importLog->id, $importLog->file_path);
                    $job->handle($studentNumberService);
                } else {
                    // Try running in background, fallback to synchronous if popen/exec fails or is disabled
                    try {
                        $artisan = base_path('artisan');
                        $command = "php \"{$artisan}\" student:import {$importLog->id} {$schoolId}";

                        if (strncasecmp(PHP_OS, 'WIN', 3) === 0) {
                            if (function_exists('popen') && function_exists('pclose')) {
                                @pclose(popen("start /B {$command}", "r"));
                            } else {
                                throw new \Exception("popen is disabled");
                            }
                        } else {
                            if (function_exists('exec')) {
                                @exec("{$command} > /dev/null 2>&1 &");
                            } else {
                                throw new \Exception("exec is disabled");
                            }
                        }
                    } catch (\Throwable $eBackground) {
                        // Fallback to synchronous execution
                        $studentNumberService = app(\App\Services\StudentNumberService::class);
                        $job = new \App\Jobs\ProcessStudentImport((int)$schoolId, (int)$importLog->id, $importLog->file_path);
                        $job->handle($studentNumberService);
                    }
                }
            }
        } catch (\Throwable $e) {
            $importLog->update([
                'status' => 'failed',
                'errors' => [['row' => 0, 'error' => 'Job execution failed: ' . $e->getMessage()]]
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Job execution failed: ' . $e->getMessage(),
                'log' => $importLog,
            ], 500);
        }

        $importLog->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Bulk import started successfully.',
            'log' => $importLog,
        ]);
    }

    public function importProgress(ImportLog $importLog)
    {
        $schoolId = auth()->user()->school_id;
        if ($importLog->school_id !== $schoolId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'success' => true,
            'status' => $importLog->status,
            'total_rows' => $importLog->total_rows,
            'success_rows' => $importLog->success_rows,
            'failed_rows' => $importLog->failed_rows,
            'errors' => $importLog->errors,
        ]);
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $headers = [
            'Admission Id', 'Date Of Admission (dd/mm/yyyy)', 'First Name', 'Last Name', 'Class', 'Section', 'Roll Number', 'Academic Year',
            'DOB (dd/mm/yyyy)', 'Gender (M/F)', 'Religion', 'Caste', 'Sub Caste', 'Category (General / OBC / SC / ST)',
            'Sub Category (EWS / Others)', 'Blood Group', 'Any Allergy (Yes/No)', 'Allergy/Medical Condition Description',
            'Birthmark (if any)', 'Adhar Number', 'Father Name', 'Father Mobile Number', 'Father ID', 'Mother Name',
            'Mother Mobile Number', 'Mother ID', 'Address', 'City', 'State', 'Country', 'Zip',
            'Emergency Name', 'Emergency Number', 'Emergency Doctor Number', 'Emergency Doctor Detail', 'Email',
            'Admission Type', 'Boarding Type', 'Defence Personal (Yes/No)', 'transport'
        ];

        $sheet->fromArray($headers, null, 'A1');

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'students_import_template.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function getExportQuery(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $user = auth()->user();
        $isTeacher = $user && ($user->hasRole('teacher') || $user->role === 'teacher' || $user->hasRole('staff'));
        $staff = $isTeacher ? $user->staff : null;

        $assignedSectionIds = [];
        if ($staff) {
            $secIdsFromCt = Section::where('school_id', $schoolId)
                ->where(function($q) use ($staff) {
                    $q->where('class_teacher_id', $staff->id)
                      ->orWhere('assistant_class_teacher_id', $staff->id);
                })
                ->pluck('id')->toArray();
            $secIdsFromSss = \App\Models\SectionSubjectStaff::where('school_id', $schoolId)->where('staff_id', $staff->id)->pluck('section_id')->toArray();
            $secIdsFromCells = \App\Models\ClassTimetableCell::where('school_id', $schoolId)
                ->where(function($q) use ($staff) {
                    $q->where('teacher_id', $staff->id);
                    if (\Illuminate\Support\Facades\Schema::hasColumn('class_timetable_cells', 'secondary_teacher_id')) {
                        $q->orWhere('secondary_teacher_id', $staff->id);
                    }
                })
                ->pluck('section_id')->toArray();
            $assignedSectionIds = array_unique(array_filter(array_merge($secIdsFromCt, $secIdsFromSss, $secIdsFromCells)));
        }

        $query = Student::where('school_id', $schoolId)->with(['class', 'section', 'academicSession', 'category']);

        if ($staff) {
            if (count($assignedSectionIds) > 0) {
                $query->whereIn('section_id', $assignedSectionIds);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        $status = $request->get('status', 'active');
        if ($status === 'deleted') {
            $query->onlyTrashed();
        } elseif ($status === 'active') {
            $query->where('is_active', 1)
                  ->where('is_alumni', 0)
                  ->where(function($q) {
                      $q->where('is_transfer', 0)->orWhereNull('is_transfer');
                  })
                  ->where(function($q) {
                      $q->whereNull('tc_number')->orWhere('tc_number', '');
                  });
        } elseif ($status === 'deactivated') {
            $query->where('is_active', 0)
                  ->where('is_alumni', 0)
                  ->where(function($q) {
                      $q->where('is_transfer', 0)->orWhereNull('is_transfer');
                  })
                  ->where(function($q) {
                      $q->whereNull('tc_number')->orWhere('tc_number', '');
                  });
        } elseif ($status === 'transfer') {
            $query->where(function($q) {
                $q->where('is_transfer', 1)
                  ->orWhereNotNull('tc_number')
                  ->orWhere('tc_number', '!=', '');
            });
        } elseif ($status === 'alumni') {
            $query->where('is_alumni', 1);
        }

        $exportSessionId = $request->get('academic_session_id');
        if (!$exportSessionId || $exportSessionId === '') {
            $currentSession = AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first()
                ?? AcademicSession::where('school_id', $schoolId)->first();
            $exportSessionId = $currentSession?->id;
        }
        $classId = $request->get('class_id');
        $sectionId = $request->get('section_id');

        if ($exportSessionId && $exportSessionId !== 'all') {
            $query->whereHas('studentSessions', function ($sq) use ($exportSessionId, $classId, $sectionId) {
                $sq->where('academic_session_id', $exportSessionId);
                if ($classId) {
                    $sq->where('class_id', $classId);
                }
                if ($sectionId) {
                    if (is_numeric($sectionId)) {
                        $sq->where('section_id', $sectionId);
                    } else {
                        $sq->whereHas('section', function ($secQ) use ($sectionId) {
                            $secQ->where('name', $sectionId);
                        });
                    }
                }
            });
        } else {
            if ($classId) {
                $query->where('class_id', $classId);
            }
            if ($sectionId) {
                if (is_numeric($sectionId)) {
                    $query->where('section_id', $sectionId);
                } else {
                    $query->whereHas('section', function ($q) use ($sectionId) {
                        $query->where('name', $sectionId);
                    });
                }
            }
        }
        if ($request->get('search')) {
            SearchHelper::applyStudentSearch($query, $request->get('search'));
        }

        return $query;
    }

    public function export(Request $request)
    {
        $students = $this->getExportQuery($request)->get();
        $schoolId = auth()->user()->school_id;
        $school = \App\Models\School::find($schoolId);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Students Data');

        // Row 1: Header Banner (Navy blue background, white text)
        $sheet->mergeCells('A1:AQ1');
        $sheet->setCellValue('A1', strtoupper($school->name ?? 'SCHOOL ERP') . ' - STUDENT MASTER DATA EXPORT');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
        $sheet->getStyle('A1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('1E3A8A');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(40);

        // Row 2: Subtitle Row (Generated date, total records count)
        $sheet->mergeCells('A2:AQ2');
        $sheet->setCellValue('A2', 'Generated On: ' . date('d M Y, h:i A') . ' | Total Records: ' . count($students));
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('475569'));
        $sheet->getStyle('A2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('F1F5F9');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(2)->setRowHeight(22);

        // Row 3: Blank gap row
        $sheet->getRowDimension(3)->setRowHeight(10);

        // Row 4: Column Headers (Blue background, white text, bold)
        $headers = [
            'Admission No', 'Date Of Admission', 'First Name', 'Last Name', 'Full Name', 'Class', 'Section', 'Roll Number', 'Academic Year',
            'DOB', 'Gender', 'Religion', 'Caste', 'Sub Caste', 'Category',
            'Sub Category', 'Blood Group', 'Any Allergy', 'Medical/Allergy Description',
            'Birthmark', 'Adhar Number', 'Father Name', 'Father Mobile Number', 'Father ID', 'Mother Name',
            'Mother Mobile Number', 'Mother ID', 'Address', 'City', 'State', 'Country', 'Zip',
            'Emergency Name', 'Emergency Number', 'Emergency Doctor Number', 'Emergency Doctor Detail', 'Email',
            'Admission Type', 'Boarding Type', 'Defence Personal', 'Transport', 'Status'
        ];

        $sheet->fromArray($headers, null, 'A4');
        $sheet->getStyle('A4:AQ4')->getFont()->setBold(true)->setSize(11)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
        $sheet->getStyle('A4:AQ4')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('1D4ED8');
        $sheet->getStyle('A4:AQ4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A4:AQ4')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)->getColor()->setARGB('1E40AF');
        $sheet->getRowDimension(4)->setRowHeight(28);

        $exportSessionId = $request->get('academic_session_id');
        if (!$exportSessionId || $exportSessionId === '') {
            $currentSession = AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first()
                ?? AcademicSession::where('school_id', $schoolId)->first();
            $exportSessionId = $currentSession?->id;
        }

        // Data Rows starting from row 5
        $rowIdx = 5;
        foreach ($students as $student) {
            $sessionRec = ($exportSessionId && $exportSessionId !== 'all')
                ? $student->studentSessions->firstWhere('academic_session_id', $exportSessionId)
                : $student->studentSessions->sortByDesc('academic_session_id')->first();

            $expClass = $sessionRec?->schoolClass?->name ?? ($student->class?->name ?? '—');
            $expSection = $sessionRec?->section?->name ?? ($student->section?->name ?? '—');
            $expRoll = $sessionRec?->roll_number ?? ($student->roll_number ?? '—');
            $expSession = $sessionRec?->academicSession?->name ?? ($student->academicSession?->name ?? ($student->admission_year ?? '—'));

            $rowData = [
                $student->admission_number ?? '—',
                $student->admission_date ? \Carbon\Carbon::parse($student->admission_date)->format('d/m/Y') : '—',
                $sessionRec?->first_name ?? ($student->first_name ?? '—'),
                $sessionRec?->last_name ?? ($student->last_name ?? '—'),
                $sessionRec?->full_name ?? ($student->full_name ?? '—'),
                $expClass,
                $expSection,
                $expRoll,
                $expSession,
                ($sessionRec?->date_of_birth ?? $student->date_of_birth) ? \Carbon\Carbon::parse($sessionRec?->date_of_birth ?? $student->date_of_birth)->format('d/m/Y') : '—',
                ucfirst($sessionRec?->gender ?? ($student->gender ?? '—')),
                $sessionRec?->religion ?? ($student->religion ?? '—'),
                $sessionRec?->caste ?? ($student->caste ?? '—'),
                $sessionRec?->sub_caste ?? ($student->sub_caste ?? '—'),
                $sessionRec?->category_name ?? ($student->category_name ?? ($student->category?->name ?? '—')),
                $sessionRec?->sub_category ?? ($student->sub_category ?? '—'),
                $sessionRec?->blood_group ?? ($student->blood_group ?? '—'),
                $sessionRec?->any_allergy ?? ($student->any_allergy ?? '—'),
                $sessionRec?->medical_allergies ?? ($student->medical_allergies ?? '—'),
                $sessionRec?->birthmark ?? ($student->birthmark ?? '—'),
                $sessionRec?->national_id ?? ($student->national_id ?? '—'),
                $sessionRec?->father_name ?? ($student->father_name ?? '—'),
                $sessionRec?->father_phone ?? ($student->father_phone ?? '—'),
                $sessionRec?->father_id ?? ($student->father_id ?? '—'),
                $sessionRec?->mother_name ?? ($student->mother_name ?? '—'),
                $sessionRec?->mother_phone ?? ($student->mother_phone ?? '—'),
                $sessionRec?->mother_id ?? ($student->mother_id ?? '—'),
                $sessionRec?->address ?? ($student->address ?? '—'),
                $sessionRec?->city ?? ($student->city ?? '—'),
                $sessionRec?->state ?? ($student->state ?? '—'),
                $sessionRec?->country ?? ($student->country ?? '—'),
                $sessionRec?->pincode ?? ($student->pincode ?? '—'),
                $sessionRec?->emergency_name ?? ($student->emergency_name ?? '—'),
                $sessionRec?->emergency_number ?? ($student->emergency_number ?? '—'),
                $sessionRec?->medical_doctor_phone ?? ($student->medical_doctor_phone ?? '—'),
                $sessionRec?->medical_doctor_name ?? ($student->medical_doctor_name ?? '—'),
                $sessionRec?->email ?? ($student->email ?? '—'),
                $sessionRec?->admission_type ?? ($student->admission_type ?? '—'),
                $sessionRec?->boarding_type ?? ($student->boarding_type ?? '—'),
                $sessionRec?->defence_personal ?? ($student->defence_personal ?? '—'),
                $sessionRec?->transport_route ?? ($student->transport_route ?? ($student->transport_opted ? 'Yes' : 'No')),
                ($sessionRec?->is_active ?? $student->is_active) ? 'Active' : 'Inactive'
            ];
            $sheet->fromArray($rowData, null, 'A' . $rowIdx);

            // Zebra striping
            $bgColor = ($rowIdx % 2 == 0) ? 'F8FAFC' : 'FFFFFF';
            $sheet->getStyle('A' . $rowIdx . ':AQ' . $rowIdx)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB($bgColor);
            $sheet->getStyle('A' . $rowIdx . ':AQ' . $rowIdx)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)->getColor()->setARGB('E2E8F0');
            $sheet->getStyle('A' . $rowIdx . ':AQ' . $rowIdx)->getFont()->setSize(10);
            $sheet->getStyle('A' . $rowIdx . ':AQ' . $rowIdx)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

            // Colored Status Pill in Column AQ
            if ($student->is_active) {
                $sheet->getStyle('AQ' . $rowIdx)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('DCFCE7');
                $sheet->getStyle('AQ' . $rowIdx)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('15803D'))->setBold(true);
            } else {
                $sheet->getStyle('AQ' . $rowIdx)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FEE2E2');
                $sheet->getStyle('AQ' . $rowIdx)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('B91C1C'))->setBold(true);
            }

            $sheet->getRowDimension($rowIdx)->setRowHeight(22);
            $rowIdx++;
        }

        // Auto-size columns up to AQ
        $highestColumn = $sheet->getHighestColumn();
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $colString = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
            $sheet->getColumnDimension($colString)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'students_export_' . date('Y_m_d_His') . '.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function exportPdf(Request $request)
    {
        $students = $this->getExportQuery($request)->get();
        $schoolId = auth()->user()->school_id;
        $school = \App\Models\School::find($schoolId);

        $exportSessionId = $request->get('academic_session_id');
        if (!$exportSessionId || $exportSessionId === '') {
            $currentSession = AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first()
                ?? AcademicSession::where('school_id', $schoolId)->first();
            $exportSessionId = $currentSession?->id;
        }

        $sessionObj = ($exportSessionId && $exportSessionId !== 'all') ? AcademicSession::find($exportSessionId) : null;
        $filters = [
            'session' => $sessionObj?->name ?? 'All Sessions',
            'class' => $request->get('class_id') ? SchoolClass::find($request->get('class_id'))?->name : 'All Classes',
            'section' => $request->get('section_id') ? (is_numeric($request->get('section_id')) ? Section::find($request->get('section_id'))?->name : $request->get('section_id')) : 'All Sections',
            'status' => ucfirst($request->get('status', 'active')),
            'search' => $request->get('search') ?? 'None',
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('school.student.export-pdf', compact('students', 'school', 'filters', 'exportSessionId'))
                ->setPaper('a3', 'landscape');

        return $pdf->download('students_export_' . date('Y_m_d_His') . '.pdf');
    }

    public function promoteForm()
    {
        $classes = SchoolClass::all();
        $sections = Section::all();
        $academicSessions = AcademicSession::all();

        return view('school.student.promote-form', compact('classes', 'sections', 'academicSessions'));
    }

    public function promote(PromoteStudentRequest $request)
    {
        $schoolId = auth()->user()->school_id;
        $data = $request->validated();
        $sessionProfileKeys = $this->getStudentProfileKeys();

        DB::transaction(function () use ($schoolId, $data, $sessionProfileKeys) {
            foreach ($data['student_ids'] as $studentId) {
                $student = Student::with('studentSessions')->findOrFail($studentId);

                // Build profile snapshot
                $existingFromSession = $student->studentSessions->firstWhere('academic_session_id', $data['from_session_id']);
                $profileSnapshot = $existingFromSession?->session_data ?? [];
                if (empty($profileSnapshot)) {
                    foreach ($sessionProfileKeys as $key) {
                        $profileSnapshot[$key] = $student->getAttribute($key);
                    }
                }

                // Ensure previous session record exists in student_sessions with snapshot
                StudentSession::updateOrCreate(
                    [
                        'school_id' => $schoolId,
                        'student_id' => $studentId,
                        'academic_session_id' => $data['from_session_id'],
                    ],
                    [
                        'class_id' => $existingFromSession?->class_id ?? $student->class_id,
                        'section_id' => $existingFromSession?->section_id ?? $student->section_id,
                        'roll_number' => $existingFromSession?->roll_number ?? $student->roll_number,
                        'is_promoted' => true,
                        'session_data' => $profileSnapshot,
                    ]
                );

                // Promote student by updating main record
                $student->update([
                    'class_id' => $data['to_class_id'],
                    'section_id' => $data['to_section_id'],
                    'academic_session_id' => $data['to_session_id'],
                ]);

                // Create student session record for new year
                StudentSession::updateOrCreate(
                    [
                        'school_id' => $schoolId,
                        'student_id' => $studentId,
                        'academic_session_id' => $data['to_session_id'],
                    ],
                    [
                        'class_id' => $data['to_class_id'],
                        'section_id' => $data['to_section_id'],
                        'roll_number' => $this->studentNumberService->generateRollNumber($data['to_section_id'], $data['to_session_id']),
                        'is_promoted' => false,
                        'session_data' => $profileSnapshot,
                    ]
                );
            }
        });

        Cache::forget('students_list_version_' . $schoolId);
        Cache::put('students_list_version_' . $schoolId, time(), 86400);

        return redirect()->route('school.students.index')->with('success', 'Students promoted successfully.');
    }

    public function issueDocument(Request $request, Student $student)
    {
        $request->validate([
            'type' => 'required|string|in:id_card,admit_card,character,dob,bonafide,transfer,appreciation,achievement',
        ]);

        $type = $request->type;
        $schoolId = auth()->user()->school_id;

        // 1. Generate PDF content depending on the type
        $pdf = null;
        if ($type === 'id_card') {
            $qrCode = base64_encode(
                \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')
                    ->size(150)
                    ->errorCorrection('H')
                    ->generate($student->admission_number)
            );
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('school.student.id-card-pdf', compact('student', 'qrCode'))
                ->setPaper('a5', 'portrait');
        } elseif ($type === 'admit_card') {
            $timetable = [
                ['date' => '2026-06-15', 'subject' => 'English', 'time' => '09:00 AM - 12:00 PM', 'room' => '101'],
                ['date' => '2026-06-17', 'subject' => 'Mathematics', 'time' => '09:00 AM - 12:00 PM', 'room' => '102'],
                ['date' => '2026-06-19', 'subject' => 'Science', 'time' => '09:00 AM - 12:00 PM', 'room' => '103'],
                ['date' => '2026-06-22', 'subject' => 'History', 'time' => '09:00 AM - 12:00 PM', 'room' => '101'],
                ['date' => '2026-06-24', 'subject' => 'Computer Science', 'time' => '09:00 AM - 12:00 PM', 'room' => 'Lab B'],
            ];
            $examName = 'First Term Examination 2026';
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('school.student.admit-card-pdf', compact('student', 'timetable', 'examName'))
                ->setPaper('a4', 'portrait');
        } else {
            // Certificates
            $title = ucwords(str_replace('_', ' ', $type)) . ' Certificate';
            $date = now()->format('d M Y');
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView("school.student.certificates.{$type}", compact('student', 'title', 'date'))
                ->setPaper('a4', 'landscape');
        }

        $content = $pdf->output();

        // 2. Save file to storage
        $filename = "{$type}_" . time() . ".pdf";
        $filePath = "students/documents/{$student->id}/{$filename}";
        Storage::disk(config('filesystems.default'))->put($filePath, $content);

        // 3. Save entry to database
        $displayName = ucwords(str_replace('_', ' ', $type)) . ' Certificate';
        if ($type === 'id_card') {
            $displayName = 'Student ID Card';
        } elseif ($type === 'admit_card') {
            $displayName = 'Exam Admit Card';
        }

        \App\Models\StudentDocument::create([
            'school_id' => $schoolId,
            'student_id' => $student->id,
            'document_type' => $type,
            'file_path' => $filePath,
            'original_name' => $displayName . '.pdf',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Document issued successfully to student dashboard!',
        ]);
    }

    public function bulkIssueDocuments(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'required|integer',
            'type' => 'required|string|in:id_card,admit_card,character,dob,bonafide,transfer,appreciation,achievement',
        ]);

        $type = $request->type;
        $studentIds = $request->student_ids;
        $schoolId = auth()->user()->school_id;

        $count = 0;
        foreach ($studentIds as $id) {
            $student = Student::where('school_id', $schoolId)->find($id);
            if (!$student) continue;

            $pdf = null;
            if ($type === 'id_card') {
                $qrCode = base64_encode(
                    \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')
                        ->size(150)
                        ->errorCorrection('H')
                        ->generate($student->admission_number)
                );
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('school.student.id-card-pdf', compact('student', 'qrCode'))
                    ->setPaper('a5', 'portrait');
            } elseif ($type === 'admit_card') {
                $timetable = [
                    ['date' => '2026-06-15', 'subject' => 'English', 'time' => '09:00 AM - 12:00 PM', 'room' => '101'],
                    ['date' => '2026-06-17', 'subject' => 'Mathematics', 'time' => '09:00 AM - 12:00 PM', 'room' => '102'],
                    ['date' => '2026-06-19', 'subject' => 'Science', 'time' => '09:00 AM - 12:00 PM', 'room' => '103'],
                    ['date' => '2026-06-22', 'subject' => 'History', 'time' => '09:00 AM - 12:00 PM', 'room' => '101'],
                    ['date' => '2026-06-24', 'subject' => 'Computer Science', 'time' => '09:00 AM - 12:00 PM', 'room' => 'Lab B'],
                ];
                $examName = 'First Term Examination 2026';
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('school.student.admit-card-pdf', compact('student', 'timetable', 'examName'))
                    ->setPaper('a4', 'portrait');
            } else {
                $title = ucwords(str_replace('_', ' ', $type)) . ' Certificate';
                $date = now()->format('d M Y');
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView("school.student.certificates.{$type}", compact('student', 'title', 'date'))
                    ->setPaper('a4', 'landscape');
            }

            $content = $pdf->output();

            $filename = "{$type}_" . time() . ".pdf";
            $filePath = "students/documents/{$student->id}/{$filename}";
            Storage::disk(config('filesystems.default'))->put($filePath, $content);

            $displayName = ucwords(str_replace('_', ' ', $type)) . ' Certificate';
            if ($type === 'id_card') {
                $displayName = 'Student ID Card';
            } elseif ($type === 'admit_card') {
                $displayName = 'Exam Admit Card';
            }

            \App\Models\StudentDocument::create([
                'school_id' => $schoolId,
                'student_id' => $student->id,
                'document_type' => $type,
                'file_path' => $filePath,
                'original_name' => $displayName . '.pdf',
            ]);
            $count++;
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully issued {$count} documents to student dashboards!",
        ]);
    }

    protected function saveBase64Photo(?string $base64Data, string $folder, ?string $oldPath = null): ?string
    {
        if (empty($base64Data)) {
            return null;
        }

        if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
            $data = substr($base64Data, strpos($base64Data, ',') + 1);
            $type = strtolower($type[1]); // e.g. png, jpeg, gif, webp

            if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png', 'webp'])) {
                return null;
            }

            $data = base64_decode($data);
            if ($data === false) {
                return null;
            }

            $fileName = \Illuminate\Support\Str::random(40) . '.' . $type;
            $path = $folder . '/' . $fileName;

            \Illuminate\Support\Facades\Storage::disk('public')->put($path, $data);

            if ($oldPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($oldPath)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
            }

            return $path;
        }

        return null;
    }

    public function downloadAdmissionForm(Student $student)
    {
        $schoolId = auth()->user()->school_id;
        if ($student->school_id !== $schoolId) {
            abort(403, 'Unauthorized.');
        }

        $formOnly = request('type') === 'form_only';

        if ($formOnly) {
            $attendances = collect();
            $totalDays = 0;
            $presentDays = 0;
            $absentDays = 0;
            $lateDays = 0;
            $attendancePercentage = 100;
            $siblings = collect();
            $marks = collect();
            $fees = collect();
        } else {
            // 1. Attendance
            $attendances = \App\Models\StudentAttendance::where('student_id', $student->id)
                ->orderBy('date', 'desc')
                ->get();
            $totalDays = $attendances->count();
            $presentDays = $attendances->where('status', 'present')->count();
            $absentDays = $attendances->where('status', 'absent')->count();
            $lateDays = $attendances->where('status', 'late')->count();
            $attendancePercentage = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 1) : 100;

            // 2. Siblings
            $siblings = Student::where('school_id', $schoolId)
                ->where('id', '!=', $student->id)
                ->where(function($q) use ($student) {
                    $hasFilter = false;
                    if ($student->guardian_email) {
                        $q->where('guardian_email', $student->guardian_email);
                        $hasFilter = true;
                    }
                    if ($student->guardian_phone) {
                        if ($hasFilter) $q->orWhere('guardian_phone', $student->guardian_phone);
                        else { $q->where('guardian_phone', $student->guardian_phone); $hasFilter = true; }
                    }
                    if ($student->father_phone) {
                        if ($hasFilter) $q->orWhere('father_phone', $student->father_phone);
                        else { $q->where('father_phone', $student->father_phone); $hasFilter = true; }
                    }
                    if ($student->mother_phone) {
                        if ($hasFilter) $q->orWhere('mother_phone', $student->mother_phone);
                        else { $q->where('mother_phone', $student->mother_phone); $hasFilter = true; }
                    }
                    if (!$hasFilter) {
                        $q->whereRaw('1 = 0');
                    }
                })->get();

            // 3. Exams (marks)
            $marks = \App\Models\StudentMark::where('student_id', $student->id)
                ->with('subject')
                ->orderBy('exam_name', 'asc')
                ->get();

            // 4. Fees
            $fees = \App\Models\StudentFee::where('student_id', $student->id)
                ->with(['category', 'component'])
                ->orderBy('due_date', 'asc')
                ->get();
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('school.student.admission-form-pdf', compact(
            'student',
            'attendances',
            'totalDays',
            'presentDays',
            'absentDays',
            'lateDays',
            'attendancePercentage',
            'siblings',
            'marks',
            'fees',
            'formOnly'
        ));
        $filename = str_replace(['/', '\\'], '_', "admission_form_{$student->admission_number}.pdf");
        return $pdf->download($filename);
    }

    public function restore(int $id)
    {
        $schoolId = auth()->user()->school_id;
        $student = Student::onlyTrashed()->where('school_id', $schoolId)->findOrFail($id);
        
        // Restore associated student user record if deactivated
        if ($student->user_id) {
            $user = \App\Models\User::withTrashed()->find($student->user_id);
            if ($user) {
                $user->restore();
                $user->update(['is_active' => true]);
            }
        }

        $student->restore();
        $student->update(['is_active' => 1]); // Set back to active

        return redirect()->route('school.students.index', ['status' => 'active'])->with('success', 'Student restored successfully.');
    }

    public function viewDocument(Request $request, $id)
    {
        $schoolId = auth()->user()->school_id;
        $doc = \App\Models\StudentDocument::where('school_id', $schoolId)->findOrFail($id);

        $filePath = $this->resolveDocumentFilePath($doc->file_path);
        if (!$filePath) {
            abort(404, 'Document file not found on disk.');
        }

        $mimeType = @mime_content_type($filePath) ?: 'application/octet-stream';
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        if ($ext === 'pdf') {
            $mimeType = 'application/pdf';
        } elseif (in_array($ext, ['jpg', 'jpeg'])) {
            $mimeType = 'image/jpeg';
        } elseif ($ext === 'png') {
            $mimeType = 'image/png';
        } elseif ($ext === 'webp') {
            $mimeType = 'image/webp';
        }

        $filename = $doc->original_name ?: basename($filePath);

        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    public function downloadDocument(Request $request, $id)
    {
        $schoolId = auth()->user()->school_id;
        $doc = \App\Models\StudentDocument::where('school_id', $schoolId)->findOrFail($id);

        $filePath = $this->resolveDocumentFilePath($doc->file_path);
        if (!$filePath) {
            abort(404, 'Document file not found on disk.');
        }

        $filename = $doc->original_name ?: basename($filePath);
        return response()->download($filePath, $filename);
    }

    private function resolveDocumentFilePath(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        $cleanPath = ltrim(str_replace(['public/', 'storage/', 'uploads/'], '', $path), '/\\');

        $candidates = [
            storage_path('app/public/' . $cleanPath),
            storage_path('app/' . $cleanPath),
            public_path('storage/' . $cleanPath),
            public_path('uploads/' . $cleanPath),
            storage_path('app/public/' . $path),
            storage_path('app/' . $path),
            public_path('storage/' . $path),
            public_path('uploads/' . $path),
            public_path($cleanPath),
            public_path($path),
        ];

        foreach ($candidates as $cand) {
            if (file_exists($cand) && is_file($cand)) {
                return $cand;
            }
        }

        $defaultDisk = config('filesystems.default', 'public');
        if (Storage::disk($defaultDisk)->exists($path)) {
            return Storage::disk($defaultDisk)->path($path);
        }
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->path($path);
        }
        if (Storage::disk('local')->exists($path)) {
            return Storage::disk('local')->path($path);
        }

        return null;
    }

    /**
     * Helper to resolve active school ID considering impersonation / multi-tenancy session
     */
    protected function getResolvedSchoolId(): ?int
    {
        if (app()->bound('currentSchool') && app('currentSchool')) {
            return (int) app('currentSchool')->id;
        }

        if (session()->has('school_id') && session('school_id')) {
            return (int) session('school_id');
        }

        if (session()->has('school_code') && session('school_code')) {
            $s = \App\Models\School::where('code', session('school_code'))->first();
            if ($s) {
                return (int) $s->id;
            }
        }

        return auth()->user()?->school_id ? (int) auth()->user()->school_id : null;
    }

    public function bulkEdit(Request $request)
    {
        $schoolId = $this->getResolvedSchoolId();
        
        $currentSession = AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first()
            ?? AcademicSession::where('school_id', $schoolId)->first();

        $reqSessionId = $request->get('academic_session_id');
        if ($reqSessionId && is_numeric($reqSessionId) && AcademicSession::where('school_id', $schoolId)->where('id', $reqSessionId)->exists()) {
            $selectedSessionId = (int) $reqSessionId;
        } else {
            $selectedSessionId = $currentSession?->id;
        }

        $classId = $request->get('class_id');
        $sectionId = $request->get('section_id');
        $search = $request->get('search');
        $status = $request->get('status', 'active');

        $query = Student::where('school_id', $schoolId)
            ->with([
                'studentSessions' => function ($sq) use ($selectedSessionId) {
                    if ($selectedSessionId) {
                        $sq->where('academic_session_id', $selectedSessionId);
                    }
                }
            ]);

        if ($status === 'active') {
            $query->where('is_active', true)->where('is_alumni', 0);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false)->where('is_alumni', 0);
        }

        if ($selectedSessionId && $selectedSessionId !== 'all') {
            $query->where(function ($q) use ($selectedSessionId, $classId, $sectionId, $schoolId) {
                $q->whereHas('studentSessions', function ($sq) use ($selectedSessionId, $classId, $sectionId, $schoolId) {
                    $sq->where('academic_session_id', $selectedSessionId)
                       ->where('school_id', $schoolId);
                    if ($classId) {
                        $sq->where('class_id', $classId);
                    }
                    if ($sectionId) {
                        if (is_numeric($sectionId)) {
                            $sq->where('section_id', $sectionId);
                        } else {
                            $sq->whereHas('section', function ($secQ) use ($sectionId) {
                                $secQ->where('name', $sectionId);
                            });
                        }
                    }
                })->orWhere(function ($sq) use ($selectedSessionId, $classId, $sectionId, $schoolId) {
                    $sq->where('school_id', $schoolId)
                       ->where('academic_session_id', $selectedSessionId)
                       ->doesntHave('studentSessions');
                    if ($classId) {
                        $sq->where('class_id', $classId);
                    }
                    if ($sectionId) {
                        if (is_numeric($sectionId)) {
                            $sq->where('section_id', $sectionId);
                        } else {
                            $sq->whereHas('section', function ($secQ) use ($sectionId) {
                                $secQ->where('name', $sectionId);
                            });
                        }
                    }
                });
            });
        } else {
            if ($classId) {
                $query->where('class_id', $classId);
            }
            if ($sectionId) {
                if (is_numeric($sectionId)) {
                    $query->where('section_id', $sectionId);
                } else {
                    $query->whereHas('section', function ($secQ) use ($sectionId) {
                        $secQ->where('name', $sectionId);
                    });
                }
            }
        }

        if ($search) {
            SearchHelper::applyStudentSearch($query, $search, '', $selectedSessionId);
        }

        // Limit to max 500 records per view for maximum DOM rendering speed
        $students = $query->orderBy('class_id', 'asc')
            ->orderBy('section_id', 'asc')
            ->orderBy('roll_number', 'asc')
            ->orderBy('first_name', 'asc')
            ->limit(500)
            ->get();

        // Sort collection class-wise and section-wise taking session records into account
        if ($selectedSessionId && $selectedSessionId !== 'all') {
            $students = $students->sort(function ($a, $b) use ($selectedSessionId) {
                $aSession = $a->studentSessions->firstWhere('academic_session_id', $selectedSessionId);
                $bSession = $b->studentSessions->firstWhere('academic_session_id', $selectedSessionId);

                $aClass = $aSession?->class_id ?? $a->class_id ?? 0;
                $bClass = $bSession?->class_id ?? $b->class_id ?? 0;
                if ($aClass != $bClass) {
                    return $aClass <=> $bClass;
                }

                $aSec = $aSession?->section_id ?? $a->section_id ?? 0;
                $bSec = $bSession?->section_id ?? $b->section_id ?? 0;
                if ($aSec != $bSec) {
                    return $aSec <=> $bSec;
                }

                $aRoll = (int) ($aSession?->roll_number ?? $a->roll_number ?? 0);
                $bRoll = (int) ($bSession?->roll_number ?? $b->roll_number ?? 0);
                if ($aRoll != $bRoll) {
                    return $aRoll <=> $bRoll;
                }

                return strcasecmp($a->first_name ?? '', $b->first_name ?? '');
            })->values();
        }

        $classes = Cache::remember("school_{$schoolId}_classes", 60, function () use ($schoolId) {
            return SchoolClass::where('school_id', $schoolId)->orderBy('id')->get();
        });

        $allSections = Cache::remember("school_{$schoolId}_sections", 60, function () use ($schoolId) {
            return Section::where('school_id', $schoolId)->orderBy('name')->get();
        });

        $sections = $classId
            ? $allSections->where('class_id', $classId)
            : $allSections;

        $categories = Cache::remember("school_{$schoolId}_categories", 60, function () use ($schoolId) {
            return StudentCategory::where('school_id', $schoolId)->get();
        });

        $academicSessions = Cache::remember("school_{$schoolId}_sessions", 60, function () use ($schoolId) {
            return AcademicSession::where('school_id', $schoolId)->orderBy('id', 'desc')->get();
        });

        return view('school.student.bulk-edit', compact(
            'students',
            'classes',
            'sections',
            'allSections',
            'categories',
            'academicSessions',
            'selectedSessionId',
            'classId',
            'sectionId',
            'search',
            'status'
        ));
    }

    public function bulkUpdate(Request $request)
    {
        $schoolId = $this->getResolvedSchoolId();
        $studentDataArray = $request->input('students', []);
        $selectedSessionId = $request->input('academic_session_id');

        if (!$selectedSessionId || $selectedSessionId === '') {
            $currentSession = AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first()
                ?? AcademicSession::where('school_id', $schoolId)->first();
            $selectedSessionId = $currentSession?->id;
        } else {
            $selectedSessionId = (int) $selectedSessionId;
        }

        if (empty($studentDataArray) || !is_array($studentDataArray)) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'No student records were modified or submitted.'], 400);
            }
            return redirect()->back()->with('error', 'No student records were modified or submitted.');
        }

        $studentIds = array_keys($studentDataArray);
        $sessionProfileKeys = $this->getStudentProfileKeys();

        DB::beginTransaction();
        try {
            // Eager load all requested students with their sessions in 1 query
            $studentsMap = Student::where('school_id', $schoolId)
                ->whereIn('id', $studentIds)
                ->with(['studentSessions'])
                ->get()
                ->keyBy('id');

            $updatedCount = 0;

            foreach ($studentDataArray as $id => $data) {
                $student = $studentsMap->get($id);
                if (!$student) {
                    continue;
                }

                // 1. Freeze other sessions of this student that don't have session_data yet
                if ($selectedSessionId) {
                    foreach ($student->studentSessions as $existingSession) {
                        if ((int)$existingSession->academic_session_id !== (int)$selectedSessionId && empty($existingSession->session_data)) {
                            $freezeData = [];
                            foreach ($sessionProfileKeys as $key) {
                                $freezeData[$key] = $student->getAttribute($key);
                            }
                            $existingSession->update(['session_data' => $freezeData]);
                        }
                    }
                }

                // Clean and normalize incoming data
                $normalized = [];
                foreach ($data as $k => $v) {
                    if ($v === '' || $v === null) {
                        $normalized[$k] = null;
                    } else {
                        $normalized[$k] = $v;
                    }
                }

                // Handle Photo Action (remove or update base64)
                $photoAction = $normalized['photo_action'] ?? null;
                $photoData = $normalized['photo'] ?? null;

                if ($photoAction === 'remove') {
                    $student->photo = null;
                    $normalized['photo'] = null;
                } elseif ($photoAction === 'update' && !empty($photoData) && str_starts_with($photoData, 'data:image')) {
                    $savedPath = $this->saveBase64Photo($photoData, 'students/photos');
                    if ($savedPath) {
                        $student->photo = $savedPath;
                        $normalized['photo'] = $savedPath;
                    }
                }

                // Non-nullable string fields in students table should default to empty string instead of NULL
                $nonNullableStringFields = [
                    'last_name', 'address', 'father_name', 'mother_name',
                    'guardian_name', 'guardian_phone', 'city', 'state', 'pincode'
                ];
                foreach ($nonNullableStringFields as $field) {
                    if (array_key_exists($field, $normalized) && $normalized[$field] === null) {
                        $normalized[$field] = '';
                    }
                }

                // Foreign key columns must be valid integers or null
                $foreignKeyFields = ['category_id', 'class_id', 'section_id', 'house_id'];
                foreach ($foreignKeyFields as $fk) {
                    if (array_key_exists($fk, $normalized)) {
                        $normalized[$fk] = (!empty($normalized[$fk])) ? (int) $normalized[$fk] : null;
                    }
                }

                // Enum normalization
                if (array_key_exists('gender', $normalized) && $normalized['gender']) {
                    $normalized['gender'] = strtolower($normalized['gender']);
                }

                // 2. Load or create target session record
                $targetSession = $selectedSessionId
                    ? $student->studentSessions->firstWhere('academic_session_id', $selectedSessionId)
                    : null;

                if (!$targetSession && $selectedSessionId) {
                    $targetSession = new StudentSession([
                        'school_id'           => $schoolId,
                        'student_id'          => $student->id,
                        'academic_session_id' => $selectedSessionId,
                        'class_id'            => $normalized['class_id'] ?? $student->class_id,
                        'section_id'          => $normalized['section_id'] ?? $student->section_id,
                        'roll_number'         => $normalized['roll_number'] ?? $student->roll_number,
                    ]);
                }

                $currentSessionData = $targetSession ? ($targetSession->session_data ?? []) : [];

                foreach ($normalized as $key => $val) {
                    if (in_array($key, $sessionProfileKeys)) {
                        $currentSessionData[$key] = $val;
                    }
                }

                if ($targetSession) {
                    if (array_key_exists('class_id', $normalized) && $normalized['class_id']) {
                        $targetSession->class_id = $normalized['class_id'];
                    }
                    if (array_key_exists('section_id', $normalized) && $normalized['section_id']) {
                        $targetSession->section_id = $normalized['section_id'];
                    }
                    if (array_key_exists('roll_number', $normalized)) {
                        $targetSession->roll_number = $normalized['roll_number'];
                    }
                    // Ensure class_id and section_id are never null on StudentSession
                    if (!$targetSession->class_id) {
                        $targetSession->class_id = $student->class_id;
                    }
                    if (!$targetSession->section_id) {
                        $targetSession->section_id = $student->section_id;
                    }

                    $targetSession->session_data = $currentSessionData;
                    $targetSession->save();
                }

                // 3. Update master student record
                $dbColumns = \Illuminate\Support\Facades\Schema::getColumnListing('students');
                $safeData = array_intersect_key($normalized, array_flip($dbColumns));

                // Avoid overwriting critical required columns with empty values if existing
                // and guarantee admission numbers cannot be changed via bulk update
                unset($safeData['admission_number']);
                unset($safeData['admission_sequence']);
                unset($safeData['admission_year']);
                if (empty($safeData['first_name'])) {
                    unset($safeData['first_name']);
                }
                if (empty($safeData['date_of_birth'])) {
                    unset($safeData['date_of_birth']);
                }
                if (empty($safeData['class_id'])) {
                    unset($safeData['class_id']);
                }
                if (empty($safeData['section_id'])) {
                    unset($safeData['section_id']);
                }
                if (array_key_exists('is_active', $safeData) && $safeData['is_active'] !== null) {
                    $safeData['is_active'] = (bool) $safeData['is_active'];
                }

                // Determine if this session is the active/latest session
                $latestSessionId = StudentSession::where('school_id', $schoolId)
                    ->where('student_id', $student->id)
                    ->max('academic_session_id');

                $isCurrentOrLatest = (!$selectedSessionId || !$latestSessionId || (int)$latestSessionId === (int)$selectedSessionId || (int)$student->academic_session_id === (int)$selectedSessionId);

                if ($isCurrentOrLatest) {
                    if (!empty($normalized['class_id'])) {
                        $safeData['class_id'] = $normalized['class_id'];
                    }
                    if (!empty($normalized['section_id'])) {
                        $safeData['section_id'] = $normalized['section_id'];
                    }
                    if (array_key_exists('roll_number', $normalized)) {
                        $safeData['roll_number'] = $normalized['roll_number'];
                    }
                    if ($selectedSessionId) {
                        $safeData['academic_session_id'] = $selectedSessionId;
                    }
                } else {
                    // For historical session, keep master enrollment pointers intact
                    unset($safeData['class_id'], $safeData['section_id'], $safeData['roll_number'], $safeData['academic_session_id']);
                }

                if (!empty($safeData)) {
                    $student->update($safeData);
                }

                $updatedCount++;
            }

            DB::commit();

            Cache::forget('students_list_version_' . $schoolId);
            Cache::put('students_list_version_' . $schoolId, time(), 86400);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "{$updatedCount} student record(s) updated successfully!"
                ]);
            }

            return redirect()->route('school.students.bulk-edit', $request->only(['class_id', 'section_id', 'search', 'status', 'academic_session_id']))
                ->with('success', "Successfully updated {$updatedCount} student record(s)!");
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Failed to update student records: ' . $e->getMessage());
        }
    }

    /**
     * Get list of profile field keys to store and isolate per academic session.
     */
    protected function getStudentProfileKeys(): array
    {
        return [
            'first_name', 'last_name', 'first_name_local', 'last_name_local', 'email', 'phone',
            'date_of_birth', 'gender', 'place_of_birth', 'birth_certificate_no', 'usn_srn_number',
            'blood_group', 'religion', 'nationality', 'caste', 'sub_caste', 'family_id', 'category_id',
            'group', 'house_id', 'house_role', 'photo', 'biometric_id', 'pen_number', 'apaar_id',
            'samagra_id', 'class_at_admission', 'enrollment_number', 'tc_number', 'transport_month',
            'transport_route', 'transport_vehicle_code', 'transport_stop', 'transport_drop_vehicle_code',
            'prev_school', 'prev_city_country', 'prev_year_attended', 'prev_board', 'prev_reg_no',
            'prev_pcm_marks', 'prev_pcm_percentage', 'prev_total_marks', 'prev_average',
            'entrance_exam_name', 'entrance_exam_rank', 'entrance_exam_remarks', 'disciplinary_action',
            'disciplinary_action_reason', 'asked_to_leave', 'asked_to_leave_reason', 'special_needs',
            'special_needs_reason', 'interests_talents', 'interests_talents_reason', 'represented_school',
            'represented_school_reason', 'other_info', 'other_info_reason',
            'father_name', 'father_phone', 'father_alternate_phone', 'father_email', 'father_occupation',
            'father_id', 'father_aadhar', 'father_income', 'father_qualification', 'father_passport',
            'father_address', 'father_photo',
            'mother_name', 'mother_phone', 'mother_alternate_phone', 'mother_email', 'mother_occupation',
            'mother_id', 'mother_aadhar', 'mother_income', 'mother_qualification', 'mother_passport',
            'mother_address', 'mother_office_address', 'mother_photo',
            'guardian_name', 'guardian_phone', 'guardian_email', 'guardian_relationship', 'guardian_occupation',
            'guardian_photo', 'guardian_passport', 'guardian_name_local', 'guardian_address',
            'whatsapp_number', 'address', 'address_line_2', 'city', 'state', 'country', 'pincode', 'region',
            'permanent_address', 'permanent_address_line_2', 'permanent_house_number', 'permanent_location',
            'permanent_city', 'permanent_state', 'permanent_country', 'permanent_pincode', 'permanent_region',
            'is_active', 'fee_visible', 'opening_due_balance', 'custom_fields', 'sub_category',
            'any_allergy', 'birthmark', 'house_number', 'location', 'emergency_name', 'emergency_number',
            'admission_type', 'boarding_type', 'defence_personal', 'is_rte', 'fee_schedule_id',
            'transport_opted', 'transport_route_id', 'transport_pick_fare', 'transport_drop_fare',
            'transport_pickup_location', 'transport_drop_location', 'transport_pickup_time', 'transport_drop_time',
            'transport_calendar_start', 'is_alumni', 'is_transfer', 'note', 'medical_height', 'medical_weight',
            'medical_vision_left', 'medical_vision_right', 'medical_dental', 'medical_illness', 'medical_history',
            'medical_allergies', 'medical_disabilities', 'medical_doctor_name', 'medical_doctor_phone', 'medical_doctor_address'
        ];
    }
}


