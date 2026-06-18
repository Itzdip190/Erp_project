<?php

namespace App\Jobs;

use App\Models\ImportLog;
use App\Models\Student;
use App\Models\StudentSession;
use App\Models\User;
use App\Services\StudentNumberService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProcessStudentImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $queue   = 'imports';  // named queue
    public int    $timeout = 300;        // 5 minutes for large files
    public int    $tries   = 1;          // no retry — avoids duplicate rows

    public function __construct(
        public int    $schoolId,
        public int    $importLogId,
        public string $filePath,         // disk-agnostic path
    ) {}

    public function handle(StudentNumberService $studentNumberService): void
    {
        $log = ImportLog::find($this->importLogId);
        if (!$log) {
            return;
        }

        $log->update(['status' => 'processing']);

        // Download to a local temp file to avoid issues reading stream over S3 or cloud disks
        $tempFile = tempnam(sys_get_temp_dir(), 'student_import_');
        $contents = Storage::disk(config('filesystems.default'))->get($this->filePath);
        file_put_contents($tempFile, $contents);

        try {
            $spreadsheet = IOFactory::load($tempFile);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();
            unlink($tempFile);
        } catch (\Exception $e) {
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
            $log->update([
                'status' => 'failed',
                'errors' => [['row' => 0, 'error' => 'Failed to read spreadsheet file: ' . $e->getMessage()]]
            ]);
            return;
        }

        // Row 1 is header: first_name, last_name, roll_number, gender, date_of_birth, guardian_name, guardian_phone, guardian_email, guardian_relationship, address, city, state, pincode, class_id, section_id, academic_session_id, admission_date, opening_due_balance
        $headers = array_map('strtolower', array_map('trim', $rows[0] ?? []));
        $dataRows = array_slice($rows, 1);
        
        $log->update(['total_rows' => count($dataRows)]);
        $errors = [];

        foreach ($dataRows as $index => $row) {
            $rowNum = $index + 2;
            
            // Skip completely empty rows
            if (empty(array_filter($row))) {
                continue;
            }

            // Map row to keys
            $rowData = [];
            foreach ($headers as $colIndex => $header) {
                if ($header) {
                    $rowData[$header] = $row[$colIndex] ?? null;
                }
            }

            // Validate
            $validator = Validator::make($rowData, [
                'first_name' => 'required|string|max:100',
                'last_name' => 'required|string|max:100',
                'roll_number' => 'nullable|string|max:50',
                'gender' => 'required|in:male,female,other',
                'date_of_birth' => 'required|date',
                'guardian_name' => 'required|string|max:150',
                'guardian_phone' => 'required|string|digits:10',
                'guardian_email' => 'nullable|email',
                'guardian_relationship' => 'required|in:father,mother,guardian',
                'address' => 'required|string',
                'city' => 'required|string|max:100',
                'state' => 'required|string|max:100',
                'pincode' => 'required|string|max:20',
                'class_id' => 'required|integer',
                'section_id' => 'required|integer',
                'academic_session_id' => 'required|integer',
                'admission_date' => 'required|date',
                'opening_due_balance' => 'nullable|numeric',
            ]);

            if ($validator->fails()) {
                $errors[] = [
                    'row' => $rowNum,
                    'error' => implode(', ', $validator->errors()->all())
                ];
                $log->increment('failed_rows');
                continue;
            }

            try {
                DB::transaction(function () use ($rowData, $studentNumberService) {
                    // 1. Generate admission number atomically
                    $admissionNumber = $studentNumberService->generateAdmissionNumber($this->schoolId);
                    
                    // 2. Create student user account
                    $cleanFirstName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $rowData['first_name']));
                    $cleanLastName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $rowData['last_name']));
                    $cleanAdmissionId = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $admissionNumber));
                    $studentEmail = $cleanFirstName . '.' . $cleanLastName . '.' . $cleanAdmissionId . '@student.yis.com';

                    $studentUser = User::create([
                        'school_id' => $this->schoolId,
                        'name' => trim($rowData['first_name'] . ' ' . $rowData['last_name']),
                        'email' => $studentEmail,
                        'phone' => $rowData['guardian_phone'] ?? null,
                        'password' => Hash::make('Student@2026!'), // Default student password
                        'is_active' => true,
                    ]);
                    $studentUser->assignRole('student');

                    // 3. Create parent user account if guardian email is provided
                    if (!empty($rowData['guardian_email'])) {
                        $parentUser = User::where('email', $rowData['guardian_email'])
                            ->where('school_id', $this->schoolId)
                            ->first();
                            
                        if (!$parentUser) {
                            $parentUser = User::create([
                                'school_id' => $this->schoolId,
                                'name' => $rowData['guardian_name'],
                                'email' => $rowData['guardian_email'],
                                'phone' => $rowData['guardian_phone'],
                                'password' => Hash::make('schoolcloud123'), // default password
                                'is_active' => true,
                            ]);
                            $parentUser->assignRole('parent');
                        }
                    }

                    // 4. Create Student
                    $student = Student::create([
                        'school_id' => $this->schoolId,
                        'user_id' => $studentUser->id,
                        'admission_number' => $admissionNumber,
                        'admission_sequence' => (int) explode('/', $admissionNumber)[2],
                        'admission_year' => (int) date('Y'),
                        'roll_number' => $rowData['roll_number'],
                        'first_name' => $rowData['first_name'],
                        'last_name' => $rowData['last_name'],
                        'date_of_birth' => $rowData['date_of_birth'],
                        'gender' => $rowData['gender'],
                        'guardian_name' => $rowData['guardian_name'],
                        'guardian_phone' => $rowData['guardian_phone'],
                        'guardian_email' => $rowData['guardian_email'],
                        'guardian_relationship' => $rowData['guardian_relationship'],
                        'address' => $rowData['address'],
                        'city' => $rowData['city'],
                        'state' => $rowData['state'],
                        'pincode' => $rowData['pincode'],
                        'class_id' => $rowData['class_id'],
                        'section_id' => $rowData['section_id'],
                        'academic_session_id' => $rowData['academic_session_id'],
                        'admission_date' => $rowData['admission_date'],
                        'opening_due_balance' => $rowData['opening_due_balance'] ?? 0,
                        'is_active' => true,
                    ]);

                    // 4. Create Student Session
                    StudentSession::create([
                        'school_id' => $this->schoolId,
                        'student_id' => $student->id,
                        'class_id' => $rowData['class_id'],
                        'section_id' => $rowData['section_id'],
                        'academic_session_id' => $rowData['academic_session_id'],
                        'roll_number' => $rowData['roll_number'] ?? $studentNumberService->generateRollNumber($rowData['section_id'], $rowData['academic_session_id']),
                        'is_promoted' => false,
                    ]);
                });

                $log->increment('success_rows');
            } catch (\Exception $e) {
                $errors[] = [
                    'row' => $rowNum,
                    'error' => 'Database error: ' . $e->getMessage()
                ];
                $log->increment('failed_rows');
            }
        }

        $log->update([
            'status' => 'completed',
            'errors' => count($errors) > 0 ? $errors : null,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        ImportLog::find($this->importLogId)?->update([
            'status' => 'failed',
            'errors' => [['row' => 0, 'error' => 'Job execution failed: ' . $exception->getMessage()]]
        ]);
    }
}
