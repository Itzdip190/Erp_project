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

            $validator = Validator::make($rowData, [
                'first_name' => 'required|string|max:100',
                'last_name' => 'required|string|max:100',
                'first_name_local' => 'nullable|string|max:100',
                'last_name_local' => 'nullable|string|max:100',
                'email' => 'nullable|email|max:150',
                'phone' => 'nullable|string|max:20',
                'roll_number' => 'nullable|string|max:50',
                'gender' => 'required|in:male,female,other',
                'date_of_birth' => 'required|date',
                'place_of_birth' => 'nullable|string|max:150',
                'birth_certificate_no' => 'nullable|string|max:100',
                'usn_srn_number' => 'nullable|string|max:100',
                'blood_group' => 'nullable|string|max:10',
                'religion' => 'nullable|string|max:100',
                'caste' => 'nullable|string|max:100',
                'sub_caste' => 'nullable|string|max:100',
                'family_id' => 'nullable|string|max:100',
                'group' => 'nullable|string|max:100',
                'house_id' => 'nullable|integer',
                'house_role' => 'nullable|string|max:100',
                'category_id' => 'nullable|integer',
                
                'biometric_id' => 'nullable|string|max:100',
                'pen_number' => 'nullable|string|max:100',
                'apaar_id' => 'nullable|string|max:100',
                'samagra_id' => 'nullable|string|max:100',
                'class_at_admission' => 'nullable|string|max:100',
                'enrollment_number' => 'nullable|string|max:100',
                'tc_number' => 'nullable|string|max:100',

                'transport_month' => 'nullable|string|max:100',
                'transport_route' => 'nullable|string|max:150',
                'transport_vehicle_code' => 'nullable|string|max:100',
                'transport_stop' => 'nullable|string|max:150',
                'transport_drop_vehicle_code' => 'nullable|string|max:100',

                'prev_school' => 'nullable|string|max:200',
                'prev_city_country' => 'nullable|string|max:150',
                'prev_year_attended' => 'nullable|string|max:50',
                'prev_board' => 'nullable|string|max:150',
                'prev_reg_no' => 'nullable|string|max:100',
                'prev_pcm_marks' => 'nullable|string|max:50',
                'prev_pcm_percentage' => 'nullable|string|max:50',
                'prev_total_marks' => 'nullable|string|max:50',
                'prev_average' => 'nullable|string|max:50',
                'entrance_exam_name' => 'nullable|string|max:150',
                'entrance_exam_rank' => 'nullable|string|max:50',
                'entrance_exam_remarks' => 'nullable|string',
                'disciplinary_action' => 'nullable',
                'disciplinary_action_reason' => 'nullable|string',
                'asked_to_leave' => 'nullable',
                'asked_to_leave_reason' => 'nullable|string',
                'special_needs' => 'nullable',
                'special_needs_reason' => 'nullable|string',
                'interests_talents' => 'nullable',
                'interests_talents_reason' => 'nullable|string',
                'represented_school' => 'nullable',
                'represented_school_reason' => 'nullable|string',
                'other_info' => 'nullable',
                'other_info_reason' => 'nullable|string',

                'father_name' => 'nullable|string|max:150',
                'father_phone' => 'nullable|string|max:20',
                'father_alternate_phone' => 'nullable|string|max:20',
                'father_email' => 'nullable|email|max:150',
                'father_occupation' => 'nullable|string|max:150',
                'father_id' => 'nullable|string|max:100',
                'father_aadhar' => 'nullable|string|max:50',
                'father_income' => 'nullable|string|max:100',
                'father_qualification' => 'nullable|string|max:150',
                'father_passport' => 'nullable|string|max:100',
                'father_address' => 'nullable|string',

                'mother_name' => 'nullable|string|max:150',
                'mother_phone' => 'nullable|string|max:20',
                'mother_alternate_phone' => 'nullable|string|max:20',
                'mother_email' => 'nullable|email|max:150',
                'mother_occupation' => 'nullable|string|max:150',
                'mother_id' => 'nullable|string|max:100',
                'mother_aadhar' => 'nullable|string|max:50',
                'mother_income' => 'nullable|string|max:100',
                'mother_qualification' => 'nullable|string|max:150',
                'mother_passport' => 'nullable|string|max:100',
                'mother_address' => 'nullable|string',
                'mother_office_address' => 'nullable|string',

                'guardian_name' => 'required|string|max:150',
                'guardian_phone' => 'required|string',
                'guardian_email' => 'nullable|email',
                'guardian_relationship' => 'required|in:father,mother,guardian',
                'guardian_occupation' => 'nullable|string|max:150',
                'guardian_passport' => 'nullable|string|max:100',
                'guardian_name_local' => 'nullable|string|max:150',
                'guardian_address' => 'nullable|string',

                'whatsapp_number' => 'nullable|string|max:20',

                'address' => 'required|string',
                'address_line_2' => 'nullable|string|max:200',
                'city' => 'required|string|max:100',
                'state' => 'required|string|max:100',
                'country' => 'nullable|string|max:100',
                'pincode' => 'required|string|max:20',
                'region' => 'nullable|string|max:100',

                'permanent_address' => 'nullable|string',
                'permanent_address_line_2' => 'nullable|string|max:200',
                'permanent_city' => 'nullable|string|max:100',
                'permanent_state' => 'nullable|string|max:100',
                'permanent_country' => 'nullable|string|max:100',
                'permanent_pincode' => 'nullable|string|max:20',
                'permanent_region' => 'nullable|string|max:100',

                'class_id' => 'required|integer',
                'section_id' => 'required|integer',
                'academic_session_id' => 'required|integer',
                'admission_date' => 'required|date',
                'opening_due_balance' => 'nullable|numeric',

                'national_id' => 'nullable|string|max:50',
                'local_id' => 'nullable|string|max:50',
                
                'bank_account_no' => 'nullable|string|max:50',
                'bank_account_holder' => 'nullable|string|max:150',
                'bank_name' => 'nullable|string|max:150',
                'bank_branch' => 'nullable|string|max:150',
                'ifsc_code' => 'nullable|string|max:20',
                'bank_micr' => 'nullable|string|max:50',
                'note' => 'nullable|string',

                'emergency_address' => 'nullable|string',
                'contact_priority' => 'nullable|string|max:50',

                'medical_height' => 'nullable|string|max:50',
                'medical_weight' => 'nullable|string|max:50',
                'medical_vision_left' => 'nullable|string|max:50',
                'medical_vision_right' => 'nullable|string|max:50',
                'medical_dental' => 'nullable|string|max:100',
                'medical_illness' => 'nullable|string|max:150',
                'medical_history' => 'nullable|string',
                'medical_allergies' => 'nullable|string',
                'medical_disabilities' => 'nullable|string',
                'medical_doctor_name' => 'nullable|string|max:150',
                'medical_doctor_phone' => 'nullable|string|max:50',
                'medical_doctor_address' => 'nullable|string',
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
                    $parseBool = function($val) {
                        if (is_null($val)) return false;
                        $valStr = strtolower(trim((string)$val));
                        return in_array($valStr, ['yes', 'true', '1', 'y', 'on']);
                    };

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
                        'roll_number' => $rowData['roll_number'] ?? null,
                        'first_name' => $rowData['first_name'],
                        'last_name' => $rowData['last_name'],
                        'first_name_local' => $rowData['first_name_local'] ?? null,
                        'last_name_local' => $rowData['last_name_local'] ?? null,
                        'email' => $rowData['email'] ?? null,
                        'phone' => $rowData['phone'] ?? null,
                        'date_of_birth' => $rowData['date_of_birth'],
                        'gender' => $rowData['gender'],
                        'place_of_birth' => $rowData['place_of_birth'] ?? null,
                        'birth_certificate_no' => $rowData['birth_certificate_no'] ?? null,
                        'usn_srn_number' => $rowData['usn_srn_number'] ?? null,
                        'blood_group' => $rowData['blood_group'] ?? null,
                        'religion' => $rowData['religion'] ?? null,
                        'caste' => $rowData['caste'] ?? null,
                        'sub_caste' => $rowData['sub_caste'] ?? null,
                        'family_id' => $rowData['family_id'] ?? null,
                        'group' => $rowData['group'] ?? null,
                        'house_id' => $rowData['house_id'] ?? null,
                        'house_role' => $rowData['house_role'] ?? null,
                        'category_id' => $rowData['category_id'] ?? null,

                        'biometric_id' => $rowData['biometric_id'] ?? null,
                        'pen_number' => $rowData['pen_number'] ?? null,
                        'apaar_id' => $rowData['apaar_id'] ?? null,
                        'samagra_id' => $rowData['samagra_id'] ?? null,
                        'class_at_admission' => $rowData['class_at_admission'] ?? null,
                        'enrollment_number' => $rowData['enrollment_number'] ?? null,
                        'tc_number' => $rowData['tc_number'] ?? null,

                        'transport_month' => $rowData['transport_month'] ?? null,
                        'transport_route' => $rowData['transport_route'] ?? null,
                        'transport_vehicle_code' => $rowData['transport_vehicle_code'] ?? null,
                        'transport_stop' => $rowData['transport_stop'] ?? null,
                        'transport_drop_vehicle_code' => $rowData['transport_drop_vehicle_code'] ?? null,

                        'prev_school' => $rowData['prev_school'] ?? null,
                        'prev_city_country' => $rowData['prev_city_country'] ?? null,
                        'prev_year_attended' => $rowData['prev_year_attended'] ?? null,
                        'prev_board' => $rowData['prev_board'] ?? null,
                        'prev_reg_no' => $rowData['prev_reg_no'] ?? null,
                        'prev_pcm_marks' => $rowData['prev_pcm_marks'] ?? null,
                        'prev_pcm_percentage' => $rowData['prev_pcm_percentage'] ?? null,
                        'prev_total_marks' => $rowData['prev_total_marks'] ?? null,
                        'prev_average' => $rowData['prev_average'] ?? null,
                        'entrance_exam_name' => $rowData['entrance_exam_name'] ?? null,
                        'entrance_exam_rank' => $rowData['entrance_exam_rank'] ?? null,
                        'entrance_exam_remarks' => $rowData['entrance_exam_remarks'] ?? null,
                        
                        'disciplinary_action' => $parseBool($rowData['disciplinary_action'] ?? null),
                        'disciplinary_action_reason' => $rowData['disciplinary_action_reason'] ?? null,
                        'asked_to_leave' => $parseBool($rowData['asked_to_leave'] ?? null),
                        'asked_to_leave_reason' => $rowData['asked_to_leave_reason'] ?? null,
                        'special_needs' => $parseBool($rowData['special_needs'] ?? null),
                        'special_needs_reason' => $rowData['special_needs_reason'] ?? null,
                        'interests_talents' => $parseBool($rowData['interests_talents'] ?? null),
                        'interests_talents_reason' => $rowData['interests_talents_reason'] ?? null,
                        'represented_school' => $parseBool($rowData['represented_school'] ?? null),
                        'represented_school_reason' => $rowData['represented_school_reason'] ?? null,
                        'other_info' => $parseBool($rowData['other_info'] ?? null),
                        'other_info_reason' => $rowData['other_info_reason'] ?? null,

                        'father_name' => $rowData['father_name'] ?? null,
                        'father_phone' => $rowData['father_phone'] ?? null,
                        'father_alternate_phone' => $rowData['father_alternate_phone'] ?? null,
                        'father_email' => $rowData['father_email'] ?? null,
                        'father_occupation' => $rowData['father_occupation'] ?? null,
                        'father_id' => $rowData['father_id'] ?? null,
                        'father_aadhar' => $rowData['father_aadhar'] ?? null,
                        'father_income' => $rowData['father_income'] ?? null,
                        'father_qualification' => $rowData['father_qualification'] ?? null,
                        'father_passport' => $rowData['father_passport'] ?? null,
                        'father_address' => $rowData['father_address'] ?? null,

                        'mother_name' => $rowData['mother_name'] ?? null,
                        'mother_phone' => $rowData['mother_phone'] ?? null,
                        'mother_alternate_phone' => $rowData['mother_alternate_phone'] ?? null,
                        'mother_email' => $rowData['mother_email'] ?? null,
                        'mother_occupation' => $rowData['mother_occupation'] ?? null,
                        'mother_id' => $rowData['mother_id'] ?? null,
                        'mother_aadhar' => $rowData['mother_aadhar'] ?? null,
                        'mother_income' => $rowData['mother_income'] ?? null,
                        'mother_qualification' => $rowData['mother_qualification'] ?? null,
                        'mother_passport' => $rowData['mother_passport'] ?? null,
                        'mother_address' => $rowData['mother_address'] ?? null,
                        'mother_office_address' => $rowData['mother_office_address'] ?? null,

                        'guardian_name' => $rowData['guardian_name'],
                        'guardian_phone' => $rowData['guardian_phone'],
                        'guardian_email' => $rowData['guardian_email'] ?? null,
                        'guardian_relationship' => $rowData['guardian_relationship'],
                        'guardian_occupation' => $rowData['guardian_occupation'] ?? null,
                        'guardian_passport' => $rowData['guardian_passport'] ?? null,
                        'guardian_name_local' => $rowData['guardian_name_local'] ?? null,
                        'guardian_address' => $rowData['guardian_address'] ?? null,

                        'whatsapp_number' => $rowData['whatsapp_number'] ?? null,

                        'address' => $rowData['address'],
                        'address_line_2' => $rowData['address_line_2'] ?? null,
                        'city' => $rowData['city'],
                        'state' => $rowData['state'],
                        'country' => $rowData['country'] ?? null,
                        'pincode' => $rowData['pincode'],
                        'region' => $rowData['region'] ?? null,

                        'permanent_address' => $rowData['permanent_address'] ?? null,
                        'permanent_address_line_2' => $rowData['permanent_address_line_2'] ?? null,
                        'permanent_city' => $rowData['permanent_city'] ?? null,
                        'permanent_state' => $rowData['permanent_state'] ?? null,
                        'permanent_country' => $rowData['permanent_country'] ?? null,
                        'permanent_pincode' => $rowData['permanent_pincode'] ?? null,
                        'permanent_region' => $rowData['permanent_region'] ?? null,

                        'class_id' => $rowData['class_id'],
                        'section_id' => $rowData['section_id'],
                        'academic_session_id' => $rowData['academic_session_id'],
                        'admission_date' => $rowData['admission_date'],
                        'opening_due_balance' => $rowData['opening_due_balance'] ?? 0,

                        'national_id' => $rowData['national_id'] ?? null,
                        'local_id' => $rowData['local_id'] ?? null,
                        
                        'bank_account_no' => $rowData['bank_account_no'] ?? null,
                        'bank_account_holder' => $rowData['bank_account_holder'] ?? null,
                        'bank_name' => $rowData['bank_name'] ?? null,
                        'bank_branch' => $rowData['bank_branch'] ?? null,
                        'ifsc_code' => $rowData['ifsc_code'] ?? null,
                        'bank_micr' => $rowData['bank_micr'] ?? null,
                        'note' => $rowData['note'] ?? null,

                        'emergency_address' => $rowData['emergency_address'] ?? null,
                        'contact_priority' => $rowData['contact_priority'] ?? null,

                        'medical_height' => $rowData['medical_height'] ?? null,
                        'medical_weight' => $rowData['medical_weight'] ?? null,
                        'medical_vision_left' => $rowData['medical_vision_left'] ?? null,
                        'medical_vision_right' => $rowData['medical_vision_right'] ?? null,
                        'medical_dental' => $rowData['medical_dental'] ?? null,
                        'medical_illness' => $rowData['medical_illness'] ?? null,
                        'medical_history' => $rowData['medical_history'] ?? null,
                        'medical_allergies' => $rowData['medical_allergies'] ?? null,
                        'medical_disabilities' => $rowData['medical_disabilities'] ?? null,
                        'medical_doctor_name' => $rowData['medical_doctor_name'] ?? null,
                        'medical_doctor_phone' => $rowData['medical_doctor_phone'] ?? null,
                        'medical_doctor_address' => $rowData['medical_doctor_address'] ?? null,
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
