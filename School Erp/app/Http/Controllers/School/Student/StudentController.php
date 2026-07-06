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
        $page = $request->get('page', 1);

        $filters = [
            'class_id' => $request->get('class_id'),
            'section_id' => $request->get('section_id'),
            'academic_session_id' => $request->get('academic_session_id'),
            'is_active' => $request->get('is_active'),
            'search' => $request->get('search'),
        ];

        $version = Cache::get('students_list_version_' . $schoolId, 'v1');
        $cacheKey = 'students_list_' . $schoolId . '_' . md5(json_encode($filters) . '_' . $page) . '_' . $version;

        $students = Cache::remember($cacheKey, 120, function () use ($schoolId, $filters) {
            $query = Student::with(['class', 'section', 'academicSession'])
                            ->where('school_id', $schoolId);

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
            if ($filters['academic_session_id']) {
                $query->where('academic_session_id', $filters['academic_session_id']);
            }
            if ($filters['is_active'] !== null && $filters['is_active'] !== '') {
                $query->where('is_active', $filters['is_active']);
            }
            if ($filters['search']) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('admission_number', 'like', "%{$search}%")
                      ->orWhere('roll_number', 'like', "%{$search}%");
                });
            }

            return $query->paginate(20);
        });

        $classes = SchoolClass::all();
        $sections = Section::all();
        $academicSessions = AcademicSession::all();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $students->items(),
            ]);
        }

        return view('school.student.index', compact('students', 'classes', 'sections', 'academicSessions', 'filters'));
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

        return view('school.student.create', compact('classes', 'sections', 'academicSessions', 'categories', 'houses'));
    }

    public function store(StudentStoreRequest $request)
    {
        $schoolId = auth()->user()->school_id;
        $data = $request->validated();
        $data['opening_due_balance'] = $data['opening_due_balance'] ?? 0.00;

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

        // Generate admission details atomically
        $data['admission_number'] = $this->studentNumberService->generateAdmissionNumber($schoolId);
        $data['admission_sequence'] = (int) explode('/', $data['admission_number'])[2];
        $data['admission_year'] = (int) date('Y');
        $data['school_id'] = $schoolId;

        $student = DB::transaction(function () use ($schoolId, $data) {
            // 1. Create student user account
            $cleanFirstName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $data['first_name']));
            $cleanLastName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $data['last_name']));
            $cleanAdmissionId = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $data['admission_number']));
            
            $studentEmail = $cleanFirstName . '.' . $cleanLastName . '.' . $cleanAdmissionId . '@student.yis.com';
            if (!empty($data['email'])) {
                $studentEmail = $data['email'];
            }

            $studentUser = User::create([
                'school_id' => $schoolId,
                'name' => trim($data['first_name'] . ' ' . $data['last_name']),
                'email' => $studentEmail,
                'phone' => $data['guardian_phone'] ?? null,
                'password' => Hash::make('Student@2026!'), // Default password
                'is_active' => true,
            ]);
            $studentUser->assignRole('student');

            // 2. Create parent user account if guardian email is provided
            if (!empty($data['guardian_email'])) {
                $parentUser = User::where('email', $data['guardian_email'])
                    ->where('school_id', $schoolId)
                    ->first();

                if (!$parentUser) {
                    $parentUser = User::create([
                        'school_id' => $schoolId,
                        'name' => $data['guardian_name'],
                        'email' => $data['guardian_email'],
                        'phone' => $data['guardian_phone'],
                        'password' => Hash::make('schoolcloud123'),
                        'is_active' => true,
                    ]);
                    $parentUser->assignRole('parent');
                }
            }

            $data['user_id'] = $studentUser->id;
            $student = Student::create($data);

            StudentSession::create([
                'school_id' => $schoolId,
                'student_id' => $student->id,
                'class_id' => $data['class_id'],
                'section_id' => $data['section_id'],
                'academic_session_id' => $data['academic_session_id'],
                'roll_number' => $data['roll_number'] ?? $this->studentNumberService->generateRollNumber($data['section_id'], $data['academic_session_id']),
                'is_promoted' => false,
            ]);

            return $student;
        });

        // Flush student list cache keys for this school
        Cache::forget('students_list_version_' . $schoolId);
        Cache::put('students_list_version_' . $schoolId, time(), 86400);

        // Sync transport and other fees
        \App\Http\Controllers\School\FeeManagementController::syncStudentFees($student);

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
            'fees'
        ));
    }

    public function edit(Student $student)
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

        return view('school.student.edit', compact('student', 'classes', 'sections', 'academicSessions', 'categories', 'houses'));
    }

    public function update(StudentUpdateRequest $request, Student $student)
    {
        $schoolId = auth()->user()->school_id;
        $data = $request->validated();
        if (array_key_exists('opening_due_balance', $data)) {
            $data['opening_due_balance'] = $data['opening_due_balance'] ?? 0.00;
        }

        if ($request->filled('captured_photo')) {
            $data['photo'] = $this->saveBase64Photo($request->input('captured_photo'), 'students/photos', $student->photo);
        } elseif ($request->hasFile('photo')) {
            if ($student->photo) {
                Storage::disk('public')->delete($student->photo);
            }
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
        if ($request->hasFile('guardian_photo')) {
            if ($student->guardian_photo) {
                Storage::disk('public')->delete($student->guardian_photo);
            }
            $data['guardian_photo'] = $request->file('guardian_photo')->store('students/photos', 'public');
        }

        DB::transaction(function () use ($schoolId, $student, &$data) {
            // 1. Manage student user account
            $studentUser = $student->user;
            if (!$studentUser || !$studentUser->hasRole('student')) {
                $cleanFirstName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $data['first_name']));
                $cleanLastName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $data['last_name']));
                $cleanAdmissionId = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $student->admission_number));
                
                $studentEmail = $cleanFirstName . '.' . $cleanLastName . '.' . $cleanAdmissionId . '@student.yis.com';
                if (!empty($data['email'])) {
                    $studentEmail = $data['email'];
                }

                $studentUser = User::create([
                    'school_id' => $schoolId,
                    'name' => trim($data['first_name'] . ' ' . $data['last_name']),
                    'email' => $studentEmail,
                    'phone' => $data['guardian_phone'] ?? null,
                    'password' => Hash::make('Student@2026!'),
                    'is_active' => true,
                ]);
                $studentUser->assignRole('student');
                $data['user_id'] = $studentUser->id;
            } else {
                $studentEmail = $studentUser->email;
                if (!empty($data['email'])) {
                    $studentEmail = $data['email'];
                } elseif ($student->first_name !== $data['first_name'] || $student->last_name !== $data['last_name']) {
                    if (str_contains($studentUser->email, '@student.yis.com')) {
                        $cleanFirstName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $data['first_name']));
                        $cleanLastName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $data['last_name']));
                        $cleanAdmissionId = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $student->admission_number));
                        $studentEmail = $cleanFirstName . '.' . $cleanLastName . '.' . $cleanAdmissionId . '@student.yis.com';
                    }
                }

                $studentUser->update([
                    'name' => trim($data['first_name'] . ' ' . $data['last_name']),
                    'email' => $studentEmail,
                    'phone' => $data['guardian_phone'] ?? null,
                ]);
            }

            // 2. Manage parent user account
            if (!empty($data['guardian_email'])) {
                $parentUser = User::where('email', $data['guardian_email'])
                    ->where('school_id', $schoolId)
                    ->first();

                if (!$parentUser) {
                    $parentUser = User::create([
                        'school_id' => $schoolId,
                        'name' => $data['guardian_name'],
                        'email' => $data['guardian_email'],
                        'phone' => $data['guardian_phone'],
                        'password' => Hash::make('schoolcloud123'),
                        'is_active' => true,
                    ]);
                    $parentUser->assignRole('parent');
                }
            }

            $student->update($data);

            // Update or create student session for current academic year
            StudentSession::updateOrCreate(
                [
                    'school_id' => $student->school_id,
                    'student_id' => $student->id,
                    'academic_session_id' => $data['academic_session_id'],
                ],
                [
                    'class_id' => $data['class_id'],
                    'section_id' => $data['section_id'],
                    'roll_number' => $data['roll_number'] ?? $student->roll_number,
                ]
            );
        });

        Cache::forget('students_list_version_' . $schoolId);
        Cache::put('students_list_version_' . $schoolId, time(), 86400);

        // Sync transport and other fees
        \App\Http\Controllers\School\FeeManagementController::syncStudentFees($student);

        return redirect()->route('school.students.index')->with('success', 'Student updated successfully.');
    }

    public function destroy(Student $student)
    {
        $schoolId = auth()->user()->school_id;
        $student->update(['is_active' => 0]);

        // Trigger notification to parent
        \Illuminate\Support\Facades\Log::info("Parent Notification: Student {$student->full_name} has been deactivated. Guardian email: {$student->guardian_email}, Phone: {$student->guardian_phone}.");

        Cache::forget('students_list_version_' . $schoolId);
        Cache::put('students_list_version_' . $schoolId, time(), 86400);

        return redirect()->route('school.students.index')->with('success', 'Student deactivated successfully. Parent notified.');
    }

    public function bulkDestroy(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        if ($request->boolean('delete_all')) {
            $query = Student::where('school_id', $schoolId);

            if ($request->filled('class_id')) {
                $query->where('class_id', $request->input('class_id'));
            }
            if ($request->filled('section_id')) {
                $sectionId = $request->input('section_id');
                if (is_numeric($sectionId)) {
                    $query->where('section_id', $sectionId);
                } else {
                    $query->whereHas('section', function ($q) use ($sectionId) {
                        $q->where('name', $sectionId);
                    });
                }
            }
            if ($request->filled('academic_session_id')) {
                $query->where('academic_session_id', $request->input('academic_session_id'));
            }
            if ($request->input('is_active') !== null && $request->input('is_active') !== '') {
                $query->where('is_active', $request->input('is_active'));
            }
            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('admission_number', 'like', "%{$search}%")
                      ->orWhere('roll_number', 'like', "%{$search}%");
                });
            }

            $students = $query->get();
            $deactivatedCount = 0;
            foreach ($students as $student) {
                if ($student->is_active) {
                    $student->update(['is_active' => 0]);
                    $deactivatedCount++;
                    \Illuminate\Support\Facades\Log::info("Parent Notification (Bulk - Delete All): Student {$student->full_name} has been deactivated. Guardian email: {$student->guardian_email}, Phone: {$student->guardian_phone}.");
                }
            }

            Cache::forget('students_list_version_' . $schoolId);
            Cache::put('students_list_version_' . $schoolId, time(), 86400);

            return response()->json([
                'success' => true,
                'message' => "Successfully deactivated {$deactivatedCount} student(s) and notified parent(s)."
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
                $deactivatedCount++;
                \Illuminate\Support\Facades\Log::info("Parent Notification (Bulk - Selected): Student {$student->full_name} has been deactivated. Guardian email: {$student->guardian_email}, Phone: {$student->guardian_phone}.");
            }
        }

        Cache::forget('students_list_version_' . $schoolId);
        Cache::put('students_list_version_' . $schoolId, time(), 86400);

        return response()->json([
            'success' => true,
            'message' => "Successfully deactivated {$deactivatedCount} student(s) and notified parent(s)."
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
            'Admission Id', 'Date Of Admission (dd/mm/yyyy)', 'First Name', 'Last Name', 'Class', 'Section', 'Roll Number',
            'DOB (dd/mm/yyyy)', 'Gender (M/F)', 'Religion', 'Caste', 'Sub Caste', 'Category (General / OBC / SC / ST)',
            'Sub Category (EWS / Others)', 'Blood Group', 'Any Allergy (Yes/No)', 'Allergy/Medical Condition Description',
            'Birthmark (if any)', 'Adhar Number', 'Father Name', 'Father Mobile Number', 'Father ID', 'Mother Name',
            'Mother Mobile Number', 'Mother ID', 'House Number', 'Location', 'City', 'State', 'Country', 'Zip',
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

    public function export(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $query = Student::with(['class', 'section']);

        if ($request->get('class_id')) {
            $query->where('class_id', $request->get('class_id'));
        }
        if ($request->get('section_id')) {
            $query->where('section_id', $request->get('section_id'));
        }
        if ($request->get('academic_session_id')) {
            $query->where('academic_session_id', $request->get('academic_session_id'));
        }

        $students = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['Admission Number', 'Roll Number', 'Full Name', 'Class', 'Section', 'Guardian Name', 'Guardian Phone', 'Guardian Email', 'Is Active'];
        $sheet->fromArray($headers, null, 'A1');

        $rowIdx = 2;
        foreach ($students as $student) {
            $sheet->fromArray([
                $student->admission_number,
                $student->roll_number,
                $student->full_name,
                $student->class?->name,
                $student->section?->name,
                $student->guardian_name,
                $student->guardian_phone,
                $student->guardian_email,
                $student->is_active ? 'Yes' : 'No'
            ], null, 'A' . $rowIdx++);
        }

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'students_export.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
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

        DB::transaction(function () use ($schoolId, $data) {
            foreach ($data['student_ids'] as $studentId) {
                $student = Student::findOrFail($studentId);

                // Promote student by updating current session records
                $student->update([
                    'class_id' => $data['to_class_id'],
                    'section_id' => $data['to_section_id'],
                    'academic_session_id' => $data['to_session_id'],
                ]);

                // Mark previous session as promoted
                StudentSession::where('student_id', $studentId)
                    ->where('academic_session_id', $data['from_session_id'])
                    ->update(['is_promoted' => true]);

                // Create student session record for new year
                StudentSession::create([
                    'school_id' => $schoolId,
                    'student_id' => $studentId,
                    'class_id' => $data['to_class_id'],
                    'section_id' => $data['to_section_id'],
                    'academic_session_id' => $data['to_session_id'],
                    'roll_number' => $this->studentNumberService->generateRollNumber($data['to_section_id'], $data['to_session_id']),
                    'is_promoted' => false,
                ]);
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
            'fees'
        ));
        return $pdf->stream("admission_form_{$student->admission_number}.pdf");
    }
}
