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

        $cacheKey = 'students_list_' . $schoolId . '_' . md5(json_encode($filters) . '_' . $page);

        $students = Cache::tags(['students_' . $schoolId])->remember($cacheKey, 120, function () use ($filters) {
            $query = Student::with(['class', 'section', 'academicSession']);

            if ($filters['class_id']) {
                $query->where('class_id', $filters['class_id']);
            }
            if ($filters['section_id']) {
                $query->where('section_id', $filters['section_id']);
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

        return view('school.student.index', compact('students', 'classes', 'sections', 'academicSessions', 'filters'));
    }

    public function create()
    {
        $classes = SchoolClass::all();
        $sections = Section::all();
        $academicSessions = AcademicSession::all();
        $categories = StudentCategory::all();
        $houses = StudentHouse::all();

        return view('school.student.create', compact('classes', 'sections', 'academicSessions', 'categories', 'houses'));
    }

    public function store(StudentStoreRequest $request)
    {
        $schoolId = auth()->user()->school_id;
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('students/photos', config('filesystems.default'));
        }

        // Generate admission details atomically
        $data['admission_number'] = $this->studentNumberService->generateAdmissionNumber($schoolId);
        $data['admission_sequence'] = (int) explode('/', $data['admission_number'])[2];
        $data['admission_year'] = (int) date('Y');
        $data['school_id'] = $schoolId;

        $student = DB::transaction(function () use ($schoolId, $data) {
            $userId = null;
            if (!empty($data['guardian_email'])) {
                $user = User::where('email', $data['guardian_email'])
                    ->where('school_id', $schoolId)
                    ->first();

                if (!$user) {
                    $user = User::create([
                        'school_id' => $schoolId,
                        'name' => $data['guardian_name'],
                        'email' => $data['guardian_email'],
                        'phone' => $data['guardian_phone'],
                        'password' => Hash::make('schoolcloud123'),
                        'is_active' => true,
                    ]);
                    $user->assignRole('parent');
                }
                $userId = $user->id;
            }

            $data['user_id'] = $userId;
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

        Cache::tags(['students_' . $schoolId])->flush();

        event(new StudentAdmitted($student));

        return redirect()->route('school.students.index')->with('success', 'Student admitted successfully.');
    }

    public function show(Student $student)
    {
        return view('school.student.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $classes = SchoolClass::all();
        $sections = Section::all();
        $academicSessions = AcademicSession::all();
        $categories = StudentCategory::all();
        $houses = StudentHouse::all();

        return view('school.student.edit', compact('student', 'classes', 'sections', 'academicSessions', 'categories', 'houses'));
    }

    public function update(StudentUpdateRequest $request, Student $student)
    {
        $schoolId = auth()->user()->school_id;
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            if ($student->photo) {
                Storage::disk(config('filesystems.default'))->delete($student->photo);
            }
            $data['photo'] = $request->file('photo')->store('students/photos', config('filesystems.default'));
        }

        DB::transaction(function () use ($student, $data) {
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

        Cache::tags(['students_' . $schoolId])->flush();

        return redirect()->route('school.students.index')->with('success', 'Student updated successfully.');
    }

    public function destroy(Student $student)
    {
        $schoolId = auth()->user()->school_id;
        $student->delete();

        Cache::tags(['students_' . $schoolId])->flush();

        return redirect()->route('school.students.index')->with('success', 'Student deleted successfully.');
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

        // Dispatch background job to Redis imports queue
        ProcessStudentImport::dispatch($schoolId, $importLog->id, $path)->onQueue('imports');

        return response()->json([
            'success' => true,
            'message' => 'Bulk import is processing in the background.',
            'import_log_id' => $importLog->id,
        ]);
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $headers = [
            'first_name', 'last_name', 'roll_number', 'gender', 'date_of_birth',
            'guardian_name', 'guardian_phone', 'guardian_email', 'guardian_relationship',
            'address', 'city', 'state', 'pincode', 'class_id', 'section_id',
            'academic_session_id', 'admission_date', 'opening_due_balance'
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

        Cache::tags(['students_' . $schoolId])->flush();

        return redirect()->route('school.students.index')->with('success', 'Students promoted successfully.');
    }
}
