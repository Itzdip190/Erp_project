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

    public int    $timeout = 300;        // 5 minutes for large files
    public int    $tries   = 1;          // no retry — avoids duplicate rows

    public function __construct(
        public int    $schoolId,
        public int    $importLogId,
        public string $filePath,         // disk-agnostic path
    ) {
        $this->queue = 'imports';
    }

    public function handle(StudentNumberService $studentNumberService): void
    {
        $log = ImportLog::find($this->importLogId);
        if (!$log) {
            return;
        }

        $log->update(['status' => 'processing']);

        $classes = \App\Models\SchoolClass::where('school_id', $this->schoolId)->get();
        $sections = \App\Models\Section::where('school_id', $this->schoolId)->get();

        $normalizeString = function($str) {
            return preg_replace('/[^a-z0-9]/', '', strtolower(trim((string)$str)));
        };

        $tempFile = null;
        $isTemp = false;

        try {
            // Try to resolve absolute path for local files to avoid copying/writing permissions issues
            $absolutePath = Storage::disk(config('filesystems.default'))->path($this->filePath);
            if (file_exists($absolutePath)) {
                $tempFile = $absolutePath;
                $isTemp = false;
            }
        } catch (\Exception $e) {
            // path() not supported (e.g. S3) - will fall back to downloading
        }

        if (!$tempFile) {
            // Fallback: download to a temp file inside the Laravel project storage (avoiding /tmp restrictions)
            $tempDir = storage_path('app/imports');
            if (!file_exists($tempDir)) {
                @mkdir($tempDir, 0755, true);
            }
            $tempFile = tempnam($tempDir, 'student_import_');
            try {
                $contents = Storage::disk(config('filesystems.default'))->get($this->filePath);
                file_put_contents($tempFile, $contents);
                $isTemp = true;
            } catch (\Exception $ex) {
                if ($tempFile && file_exists($tempFile)) {
                    @unlink($tempFile);
                }
                $log->update([
                    'status' => 'failed',
                    'errors' => [['row' => 0, 'error' => 'Failed to retrieve storage file: ' . $ex->getMessage()]]
                ]);
                return;
            }
        }

        try {
            $spreadsheet = IOFactory::load($tempFile);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();
            if ($isTemp && file_exists($tempFile)) {
                @unlink($tempFile);
            }
        } catch (\Exception $e) {
            if ($isTemp && file_exists($tempFile)) {
                @unlink($tempFile);
            }
            $log->update([
                'status' => 'failed',
                'errors' => [['row' => 0, 'error' => 'Failed to read spreadsheet file: ' . $e->getMessage()]]
            ]);
            return;
        }

        // Normalize headers
        $headers = [];
        foreach (($rows[0] ?? []) as $colIndex => $rawHeader) {
            if ($rawHeader) {
                $clean = preg_replace('/_+/', '_', trim(preg_replace('/[^a-z0-9]/', '_', strtolower(trim((string)$rawHeader))), '_'));
                $headers[$colIndex] = $clean;
            } else {
                $headers[$colIndex] = null;
            }
        }

        $dataRows = array_slice($rows, 1);
        $errors = [];
        $totalValidRows = 0;

        // Helper to check if row has student data (Name/First Name must not be empty)
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

        // Filter and count valid data rows, and stop when we find trailing empty rows
        $validDataRows = [];
        foreach ($dataRows as $index => $row) {
            if ($isRowValidData($row)) {
                $validDataRows[] = [
                    'original_index' => $index,
                    'row_data' => $row
                ];
            } else {
                // Stop importer completely when empty row is hit
                break;
            }
        }

        $log->update(['total_rows' => count($validDataRows)]);

        foreach ($validDataRows as $item) {
            $rowNum = $item['original_index'] + 2;
            $row = $item['row_data'];

            // Map row to keys
            $rowData = [];
            foreach ($headers as $colIndex => $header) {
                if ($header) {
                    $rowData[$header] = $row[$colIndex] ?? null;
                }
            }

            // Map keys to standard fields
            $mappedData = [];
            $mappedData['admission_number'] = $rowData['admission_id'] ?? $rowData['admission_number'] ?? null;
            $mappedData['admission_date'] = $rowData['date_of_admission_dd_mm_yyyy'] ?? $rowData['admission_date'] ?? null;
            $firstName = trim((string)($rowData['first_name'] ?? $rowData['name'] ?? ''));
            $lastName = trim((string)($rowData['last_name'] ?? ''));
            if ($firstName !== '' && $lastName === '') {
                $nameParts = explode(' ', $firstName, 2);
                $firstName = $nameParts[0];
                $lastName = $nameParts[1] ?? '';
            }
            $mappedData['first_name'] = $firstName;
            $mappedData['last_name'] = $lastName;
            $mappedData['class_name'] = $rowData['class'] ?? $rowData['class_name'] ?? null;
            $mappedData['section_name'] = $rowData['section'] ?? $rowData['section_name'] ?? null;
            $mappedData['class_id'] = $rowData['class_id'] ?? null;
            $mappedData['section_id'] = $rowData['section_id'] ?? null;
            $mappedData['academic_session_id'] = $rowData['academic_session_id'] ?? null;
            $mappedData['roll_number'] = $rowData['roll_number'] ?? null;
            $mappedData['date_of_birth'] = $rowData['dob_dd_mm_yyyy'] ?? $rowData['date_of_birth'] ?? $rowData['dob'] ?? null;
            $mappedData['gender'] = $rowData['gender_m_f'] ?? $rowData['gender'] ?? null;
            $mappedData['religion'] = $rowData['religion'] ?? null;
            $mappedData['caste'] = $rowData['caste'] ?? null;
            $mappedData['sub_caste'] = $rowData['sub_caste'] ?? null;
            $mappedData['category_name'] = $rowData['category_general_obc_sc_st'] ?? $rowData['category_name'] ?? $rowData['category'] ?? null;
            $mappedData['sub_category'] = $rowData['sub_category_ews_others'] ?? $rowData['sub_category'] ?? null;
            $mappedData['blood_group'] = $rowData['blood_group'] ?? null;
            $mappedData['any_allergy'] = $rowData['any_allergy_yes_no'] ?? $rowData['any_allergy'] ?? null;
            $mappedData['medical_allergies'] = $rowData['allergy_medical_condition_description'] ?? $rowData['medical_allergies'] ?? null;
            $mappedData['birthmark'] = $rowData['birthmark_if_any'] ?? $rowData['birthmark'] ?? null;
            $mappedData['national_id'] = $rowData['adhar_number'] ?? $rowData['national_id'] ?? $rowData['aadhar_number'] ?? null;
            
            $mappedData['father_name'] = $rowData['father_name'] ?? null;
            $mappedData['father_phone'] = $rowData['father_mobile_number'] ?? $rowData['father_phone'] ?? null;
            $mappedData['father_id'] = $rowData['father_id'] ?? null;
            $mappedData['mother_name'] = $rowData['mother_name'] ?? null;
            $mappedData['mother_phone'] = $rowData['mother_mobile_number'] ?? $rowData['mother_phone'] ?? null;
            $mappedData['mother_id'] = $rowData['mother_id'] ?? null;
            $mappedData['guardian_email'] = $rowData['guardian_email'] ?? $rowData['parent_email'] ?? $rowData['father_email'] ?? $rowData['mother_email'] ?? null;
            
            $mappedData['house_number'] = $rowData['house_number'] ?? null;
            $mappedData['location'] = $rowData['location'] ?? null;
            $mappedData['city'] = $rowData['city'] ?? null;
            $mappedData['state'] = $rowData['state'] ?? null;
            $mappedData['country'] = $rowData['country'] ?? null;
            $mappedData['pincode'] = $rowData['zip'] ?? null;
            
            $mappedData['emergency_name'] = $rowData['emergency_name'] ?? null;
            $mappedData['emergency_number'] = $rowData['emergency_number'] ?? null;
            $mappedData['medical_doctor_phone'] = $rowData['emergency_doctor_number'] ?? null;
            $mappedData['medical_doctor_name'] = $rowData['emergency_doctor_detail'] ?? null;
            
            $mappedData['email'] = $rowData['email'] ?? null;
            $mappedData['admission_type'] = $rowData['admission_type'] ?? null;
            $mappedData['boarding_type'] = $rowData['boarding_type'] ?? null;
            $mappedData['defence_personal'] = $rowData['defence_personal_yes_no'] ?? null;
            $mappedData['transport_route'] = $rowData['transport'] ?? null;

            // Date parsing (dd/mm/yyyy support)
            $parseDate = function($val) {
                if (empty($val)) return null;
                $val = trim((string)$val);
                if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $val)) {
                    $parts = explode('/', $val);
                    return sprintf('%04d-%02d-%02d', $parts[2], $parts[1], $parts[0]);
                }
                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $val)) {
                    return $val;
                }
                try {
                    return \Carbon\Carbon::parse($val)->format('Y-m-d');
                } catch (\Exception $e) {
                    return null;
                }
            };
            $mappedData['date_of_birth'] = $parseDate($mappedData['date_of_birth']) ?? '2015-01-01';
            $mappedData['admission_date'] = $parseDate($mappedData['admission_date']) ?? date('Y-m-d');

            // Gender parsing
            $parseGender = function($val) {
                if (empty($val)) return 'other';
                $val = strtolower(trim((string)$val));
                if ($val === 'm' || $val === 'male') return 'male';
                if ($val === 'f' || $val === 'female') return 'female';
                return 'other';
            };
            $mappedData['gender'] = $parseGender($mappedData['gender']);

            // Parse booleans
            $parseBool = function($val) {
                if (is_null($val)) return false;
                $valStr = strtolower(trim((string)$val));
                return in_array($valStr, ['yes', 'true', '1', 'y', 'on']);
            };
            $mappedData['any_allergy'] = $parseBool($mappedData['any_allergy']);
            $mappedData['defence_personal'] = $parseBool($mappedData['defence_personal']);

            // Look up existing student for update mode (safe duplicates)
            $admissionNumber = trim((string)($mappedData['admission_number'] ?? ''));
            
            // Normalize admission number first if in standard format (e.g. YIS/2026/11 -> YIS/2026/00011)
            if ($admissionNumber !== '') {
                $parts = explode('/', $admissionNumber);
                if (count($parts) === 3) {
                    $prefix = strtoupper(trim($parts[0]));
                    $year = trim($parts[1]);
                    $seq = (int)trim($parts[2]);
                    $admissionNumber = sprintf('%s/%s/%05d', $prefix, $year, $seq);
                }
            }

            $existingStudent = null;
            if ($admissionNumber !== '') {
                $existingStudent = Student::withTrashed()
                    ->where('school_id', $this->schoolId)
                    ->where('admission_number', $admissionNumber)
                    ->first();
            } else {
                $admissionNumber = $studentNumberService->generateAdmissionNumber($this->schoolId);
            }
            $mappedData['admission_number'] = $admissionNumber;

            // Class lookup
            $classId = $mappedData['class_id'] ?? null;
            $class = null;
            if ($classId) {
                $class = $classes->firstWhere('id', (int)$classId);
            }
            if (!$class) {
                $className = trim((string)($mappedData['class_name'] ?? ''));
                if ($className === '') {
                    $className = 'General Class';
                }
                
                $normClassName = $normalizeString($className);
                $class = $classes->first(function($c) use ($normClassName, $normalizeString) {
                    return $normalizeString($c->name) === $normClassName;
                });

                if (!$class) {
                    // Determine numeric name
                    $numericName = 0;
                    preg_match('/\d+/', $className, $matches);
                    if (!empty($matches[0])) {
                        $numericName = (int)$matches[0];
                    }

                    // Create the class dynamically
                    $class = \App\Models\SchoolClass::create([
                        'school_id' => $this->schoolId,
                        'name' => $className,
                        'numeric_name' => $numericName,
                    ]);
                    $classes->push($class);
                }
            }
            $mappedData['class_id'] = $class->id;

            // Section lookup
            $sectionId = $mappedData['section_id'] ?? null;
            $section = null;
            if ($sectionId) {
                $section = $sections->firstWhere('id', (int)$sectionId);
            }
            if (!$section) {
                $sectionName = trim((string)($mappedData['section_name'] ?? ''));
                if ($sectionName === '') {
                    $sectionName = 'A';
                }

                $normSecName = $normalizeString($sectionName);
                $section = $sections->first(function($s) use ($normSecName, $class, $normalizeString) {
                    return $s->class_id === $class->id && $normalizeString($s->name) === $normSecName;
                });

                if (!$section) {
                    $section = \App\Models\Section::create([
                        'school_id' => $this->schoolId,
                        'class_id' => $class->id,
                        'name' => $sectionName,
                    ]);
                    $sections->push($section);
                }
            }
            $mappedData['section_id'] = $section->id;

            // Academic Session
            $academicSessionId = $mappedData['academic_session_id'] ?? null;
            $activeSession = null;
            if ($academicSessionId) {
                $activeSession = \App\Models\AcademicSession::where('school_id', $this->schoolId)->find($academicSessionId);
            }
            if (!$activeSession) {
                $activeSession = \App\Models\AcademicSession::where('school_id', $this->schoolId)
                    ->where('is_current', true)
                    ->first();
                if (!$activeSession) {
                    $activeSession = \App\Models\AcademicSession::where('school_id', $this->schoolId)->first();
                }
            }
            if (!$activeSession) {
                $errors[] = [
                    'row' => $rowNum,
                    'error' => "No active academic session found for the school."
                ];
                $log->increment('failed_rows');
                continue;
            }
            $mappedData['academic_session_id'] = $activeSession->id;

            // Category lookup/create
            $categoryName = trim((string)($mappedData['category_name'] ?? ''));
            if ($categoryName !== '') {
                $category = \App\Models\StudentCategory::where('school_id', $this->schoolId)
                    ->where('name', 'like', $categoryName)
                    ->first();
                if (!$category) {
                    $category = \App\Models\StudentCategory::create([
                        'school_id' => $this->schoolId,
                        'name' => $categoryName,
                        'description' => 'Imported via bulk upload'
                    ]);
                }
                $mappedData['category_id'] = $category->id;
            } else {
                $mappedData['category_id'] = null;
            }

            // Defaults & validations for parent/guardian and address fields
            $mappedData['guardian_name'] = $mappedData['father_name'] ?? 'Father';
            $mappedData['guardian_phone'] = $mappedData['father_phone'] ?? '0000000000';
            $mappedData['guardian_relationship'] = 'father';
            
            $mappedData['address'] = trim(($mappedData['house_number'] ?? '') . ' ' . ($mappedData['location'] ?? ''));
            if ($mappedData['address'] === '') {
                $mappedData['address'] = 'N/A';
            }
            if (empty($mappedData['city'])) {
                $mappedData['city'] = 'N/A';
            }
            if (empty($mappedData['state'])) {
                $mappedData['state'] = 'N/A';
            }
            if (empty($mappedData['pincode'])) {
                $mappedData['pincode'] = 'N/A';
            }

            $validator = Validator::make($mappedData, [
                'admission_number' => 'required|string|max:100',
                'first_name' => 'required|string|max:100',
                'class_id' => 'required|integer',
                'section_id' => 'required|integer',
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
                DB::transaction(function () use ($mappedData, $studentNumberService, $existingStudent) {
                    $cleanFirstName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $mappedData['first_name']));
                    $cleanLastName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $mappedData['last_name']));
                    $cleanAdmissionId = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $mappedData['admission_number']));
                    
                    $studentEmail = $cleanFirstName . '.' . $cleanLastName . '.' . $cleanAdmissionId . '@student.yis.com';
                    if (!empty($mappedData['email'])) {
                        $studentEmail = $mappedData['email'];
                    }

                    $studentUser = null;
                    if ($existingStudent && $existingStudent->user_id) {
                        $studentUser = User::find($existingStudent->user_id);
                    }

                    if ($studentUser) {
                        $studentUser->update([
                            'name' => trim($mappedData['first_name'] . ' ' . $mappedData['last_name']),
                            'email' => $studentEmail,
                            'phone' => $mappedData['guardian_phone'] ?? $studentUser->phone,
                        ]);
                    } else {
                        // Make sure studentEmail is unique globally if it's generated
                        if (empty($mappedData['email'])) {
                            $emailExists = User::where('email', $studentEmail)->exists();
                            if ($emailExists) {
                                $suffix = 1;
                                do {
                                    $suffix++;
                                    $testEmail = $cleanFirstName . '.' . $cleanLastName . '.' . $cleanAdmissionId . '_' . $suffix . '@student.yis.com';
                                } while (User::where('email', $testEmail)->exists());
                                $studentEmail = $testEmail;
                            }
                        }

                        $studentUser = User::create([
                            'school_id' => $this->schoolId,
                            'name' => trim($mappedData['first_name'] . ' ' . $mappedData['last_name']),
                            'email' => $studentEmail,
                            'phone' => $mappedData['guardian_phone'] ?? null,
                            'password' => Hash::make('Student@2026!'),
                            'is_active' => true,
                        ]);
                        $studentUser->assignRole('student');
                    }

                    // Create/Update parent user account
                    $parentUser = null;
                    $parentPhone = $mappedData['father_phone'] ?? $mappedData['guardian_phone'] ?? null;
                    if (!empty($parentPhone) || !empty($mappedData['guardian_email'])) {
                        $parentEmail = $mappedData['guardian_email'];
                        if (empty($parentEmail)) {
                            $parentEmail = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $mappedData['father_name'] ?? 'father')) . '.' . $cleanAdmissionId . '@parent.yis.com';
                        }
                        
                        $parentUser = User::where('school_id', $this->schoolId)
                            ->where(function($q) use ($parentEmail, $parentPhone) {
                                $q->where('email', $parentEmail);
                                if (!empty($parentPhone)) {
                                    $q->orWhere('phone', $parentPhone);
                                }
                            })->first();

                        if (!$parentUser) {
                            if (empty($mappedData['guardian_email']) && User::where('email', $parentEmail)->exists()) {
                                $suffix = 1;
                                do {
                                    $suffix++;
                                    $testEmail = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $mappedData['father_name'] ?? 'father')) . '.' . $cleanAdmissionId . '_' . $suffix . '@parent.yis.com';
                                } while (User::where('email', $testEmail)->exists());
                                $parentEmail = $testEmail;
                            }

                            $parentUser = User::create([
                                'school_id' => $this->schoolId,
                                'name' => $mappedData['father_name'] ?: ($mappedData['guardian_name'] ?: 'Parent'),
                                'email' => $parentEmail,
                                'phone' => $parentPhone,
                                'password' => Hash::make('schoolcloud123'),
                                'is_active' => true,
                            ]);
                            $parentUser->assignRole('parent');
                        } else {
                            $parentUser->update([
                                'name' => $mappedData['father_name'] ?: ($mappedData['guardian_name'] ?: $parentUser->name),
                                'email' => $parentEmail,
                                'phone' => $parentPhone ?: $parentUser->phone,
                            ]);
                        }
                    }

                    // Parse Year & Sequence
                    $admissionYear = (int) date('Y');
                    if ($existingStudent) {
                        $admissionYear = $existingStudent->admission_year;
                        $seq = $existingStudent->admission_sequence;
                    } else {
                        $parts = explode('/', $mappedData['admission_number']);
                        
                        // Parse year from admission number if format is YIS/YYYY/XXXXX
                        if (count($parts) >= 2 && is_numeric($parts[1]) && strlen($parts[1]) === 4) {
                            $admissionYear = (int)$parts[1];
                        } else {
                            $admissionYear = (int) date('Y', strtotime($mappedData['admission_date']));
                        }

                        if (count($parts) >= 3) {
                            $seq = (int)$parts[2];
                        } else {
                            $seq = Student::where('school_id', $this->schoolId)
                                ->where('admission_year', $admissionYear)
                                ->max('admission_sequence') + 1;
                        }

                        // Ensure sequence is unique for (school_id, admission_year) to prevent integrity constraint violations
                        $originalSeq = $seq;
                        while (Student::where('school_id', $this->schoolId)
                            ->where('admission_year', $admissionYear)
                            ->where('admission_sequence', $seq)
                            ->exists()) {
                            $seq++;
                        }

                        // If the sequence was adjusted and the admission number matches the standard format, update it to remain consistent
                        if ($seq !== $originalSeq) {
                            if (count($parts) >= 3) {
                                $prefix = strtoupper(trim($parts[0]));
                                $mappedData['admission_number'] = sprintf('%s/%d/%05d', $prefix, $admissionYear, $seq);
                            }
                        }
                    }

                    $studentFields = [
                        'school_id' => $this->schoolId,
                        'user_id' => $studentUser->id,
                        'admission_number' => $mappedData['admission_number'],
                        'admission_sequence' => $seq,
                        'admission_year' => $admissionYear,
                        'roll_number' => $mappedData['roll_number'] ?? null,
                        'first_name' => $mappedData['first_name'],
                        'last_name' => $mappedData['last_name'],
                        'date_of_birth' => $mappedData['date_of_birth'],
                        'gender' => $mappedData['gender'],
                        'religion' => $mappedData['religion'] ?? null,
                        'caste' => $mappedData['caste'] ?? null,
                        'sub_caste' => $mappedData['sub_caste'] ?? null,
                        'category_id' => $mappedData['category_id'],
                        'sub_category' => $mappedData['sub_category'],
                        'blood_group' => $mappedData['blood_group'] ?? null,
                        'any_allergy' => $mappedData['any_allergy'],
                        'medical_allergies' => $mappedData['medical_allergies'] ?? null,
                        'birthmark' => $mappedData['birthmark'] ?? null,
                        'national_id' => $mappedData['national_id'] ?? null,

                        'father_name' => $mappedData['father_name'] ?? null,
                        'father_phone' => $mappedData['father_phone'] ?? null,
                        'father_id' => $mappedData['father_id'] ?? null,
                        'mother_name' => $mappedData['mother_name'] ?? null,
                        'mother_phone' => $mappedData['mother_phone'] ?? null,
                        'mother_id' => $mappedData['mother_id'] ?? null,

                        'house_number' => $mappedData['house_number'] ?? null,
                        'location' => $mappedData['location'] ?? null,
                        'city' => $mappedData['city'],
                        'state' => $mappedData['state'],
                        'country' => $mappedData['country'] ?? 'India',
                        'pincode' => $mappedData['pincode'],
                        
                        'emergency_name' => $mappedData['emergency_name'] ?? null,
                        'emergency_number' => $mappedData['emergency_number'] ?? null,
                        'medical_doctor_phone' => $mappedData['medical_doctor_phone'] ?? null,
                        'medical_doctor_name' => $mappedData['medical_doctor_name'] ?? null,

                        'email' => $mappedData['email'] ?? null,
                        'guardian_email' => $mappedData['guardian_email'] ?? null,
                        'admission_type' => $mappedData['admission_type'] ?? null,
                        'boarding_type' => $mappedData['boarding_type'] ?? null,
                        'defence_personal' => $mappedData['defence_personal'],
                        'transport_route' => $mappedData['transport_route'] ?? null,

                        'guardian_name' => $mappedData['guardian_name'],
                        'guardian_phone' => $mappedData['guardian_phone'],
                        'guardian_relationship' => $mappedData['guardian_relationship'],
                        'address' => $mappedData['address'],
                        'class_id' => $mappedData['class_id'],
                        'section_id' => $mappedData['section_id'],
                        'academic_session_id' => $mappedData['academic_session_id'],
                        'admission_date' => $mappedData['admission_date'],
                        'is_active' => true,
                    ];

                    if ($existingStudent) {
                        if ($existingStudent->trashed()) {
                            $existingStudent->restore();
                        }
                        $existingStudent->update($studentFields);
                        $student = $existingStudent;
                    } else {
                        $student = Student::create($studentFields);
                    }

                    $sessionFields = [
                        'school_id' => $this->schoolId,
                        'student_id' => $student->id,
                        'class_id' => $mappedData['class_id'],
                        'section_id' => $mappedData['section_id'],
                        'academic_session_id' => $mappedData['academic_session_id'],
                        'roll_number' => $mappedData['roll_number'] ?? $studentNumberService->generateRollNumber($mappedData['section_id'], $mappedData['academic_session_id']),
                        'is_promoted' => false,
                    ];

                    $existingSession = StudentSession::where('school_id', $this->schoolId)
                        ->where('student_id', $student->id)
                        ->where('academic_session_id', $mappedData['academic_session_id'])
                        ->first();

                    if ($existingSession) {
                        $existingSession->update($sessionFields);
                    } else {
                        StudentSession::create($sessionFields);
                    }
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
