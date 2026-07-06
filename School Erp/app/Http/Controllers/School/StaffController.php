<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Staff;
use App\Models\StaffAttendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class StaffController extends Controller
{
    /**
     * Display a listing of staff.
     */
    public function index(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $search = $request->get('search');
        $deptId = $request->get('department_id');
        $desgId = $request->get('designation_id');
        $status = $request->get('status');

        $query = Staff::where('school_id', $schoolId)
            ->with(['department', 'designation', 'user']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($deptId) {
            $query->where('department_id', $deptId);
        }

        if ($desgId) {
            $query->where('designation_id', $desgId);
        }

        if ($status !== null && $status !== '') {
            $query->where('is_active', $status === '1');
        }

        $staffList = $query->paginate(10);
        $departments = Department::where('school_id', $schoolId)->get();
        $designations = Designation::where('school_id', $schoolId)->get();

        return view('school.staff.index', compact('staffList', 'departments', 'designations', 'search', 'deptId', 'desgId', 'status'));
    }

    /**
     * Show form to create new staff.
     */
    public function create()
    {
        $schoolId = auth()->user()->school_id;
        $departments = Department::where('school_id', $schoolId)->get();
        $designations = Designation::where('school_id', $schoolId)->get();

        return view('school.staff.create', compact('departments', 'designations'));
    }

    /**
     * Store new staff.
     */
    public function store(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $request->validate([
            'employee_id'         => 'required|string|max:50|unique:staff,employee_id,NULL,id,school_id,' . $schoolId,
            'first_name'          => 'required|string|max:100',
            'last_name'           => 'required|string|max:100',
            'email'               => 'required|email|unique:users,email',
            'phone'               => 'required|digits:10',
            'password'            => 'nullable|string|min:6',
            'department_id'       => 'required|exists:departments,id',
            'designation_id'      => 'required|exists:designations,id',
            'joining_date'        => 'required|date',
            'employment_type'     => 'required|in:permanent,contract,part_time',
            'basic_salary'        => 'required|numeric|min:0',
            'is_active'           => 'required|boolean',
            'photo'               => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'captured_photo'      => 'nullable|string',
            'gender'              => 'nullable|string',
            'blood_group'         => 'nullable|string',
            'address'             => 'nullable|string',
            'city'                => 'nullable|string',
            'state'               => 'nullable|string',
            'pincode'             => 'nullable|string',
            'bank_name'           => 'nullable|string',
            'bank_account_number' => 'nullable|string',
            'ifsc_code'           => 'nullable|string',
            'pan_number'          => 'nullable|string',
            'qualification'       => 'nullable|string',
            'experience_years'    => 'nullable|integer',
            'date_of_birth'       => 'nullable|date',
            'additional_fields'   => 'nullable|array',
            'additional_fields.aadhar_number' => 'nullable|string',
            'additional_fields.alternate_phone' => 'nullable|digits:10',
            'additional_fields.father_phone' => 'nullable|digits:10',
        ]);

        // 1. Create linked User
        $user = User::create([
            'name'      => trim($request->first_name . ' ' . $request->last_name),
            'email'     => $request->email,
            'phone'     => $request->phone,
            'password'  => Hash::make($request->password ?: 'Welcome@2026!'),
            'school_id' => $schoolId,
            'is_active' => $request->is_active,
        ]);

        // Assign Spatie Role based on Designation
        $designation = Designation::findOrFail($request->designation_id);
        $roleName = str_contains(strtolower($designation->name), 'admin') || str_contains(strtolower($designation->name), 'principal')
            ? 'school_admin'
            : (str_contains(strtolower($designation->name), 'accountant') ? 'accountant' : 'teacher');

        $user->assignRole($roleName);

        // 2. Upload Photo
        $photoPath = null;
        if ($request->filled('captured_photo')) {
            $photoPath = $this->saveBase64Photo($request->input('captured_photo'), 'staff-photos');
        } elseif ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('staff-photos', 'public');
        }

        // 3. Create Staff profile
        $staff = Staff::create(array_merge(
            $request->except(['photo', 'captured_photo']),
            [
                'school_id' => $schoolId,
                'user_id'   => $user->id,
                'photo'     => $photoPath
            ]
        ));
        $staff->designations()->sync([$request->designation_id]);

        return redirect()->route('school.staff.index')->with('success', 'Staff member registered successfully!');
    }

    /**
     * Show form to edit staff.
     */
    public function edit(Staff $staff)
    {
        $schoolId = auth()->user()->school_id;
        if ($staff->school_id !== $schoolId) {
            abort(403, 'Unauthorized.');
        }

        $departments = Department::where('school_id', $schoolId)->get();
        $designations = Designation::where('school_id', $schoolId)->get();

        return view('school.staff.edit', compact('staff', 'departments', 'designations'));
    }

    /**
     * Update staff details.
     */
    public function update(Request $request, Staff $staff)
    {
        $schoolId = auth()->user()->school_id;
        if ($staff->school_id !== $schoolId) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'employee_id'         => 'required|string|max:50|unique:staff,employee_id,' . $staff->id . ',id,school_id,' . $schoolId,
            'first_name'          => 'required|string|max:100',
            'last_name'           => 'required|string|max:100',
            'email'               => 'required|email|unique:users,email,' . $staff->user_id,
            'phone'               => 'required|digits:10',
            'password'            => 'nullable|string|min:6',
            'department_id'       => 'required|exists:departments,id',
            'designation_id'      => 'required|exists:designations,id',
            'joining_date'        => 'required|date',
            'employment_type'     => 'required|in:permanent,contract,part_time',
            'basic_salary'        => 'required|numeric|min:0',
            'is_active'           => 'required|boolean',
            'photo'               => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'captured_photo'      => 'nullable|string',
            'gender'              => 'nullable|string',
            'blood_group'         => 'nullable|string',
            'address'             => 'nullable|string',
            'city'                => 'nullable|string',
            'state'               => 'nullable|string',
            'pincode'             => 'nullable|string',
            'bank_name'           => 'nullable|string',
            'bank_account_number' => 'nullable|string',
            'ifsc_code'           => 'nullable|string',
            'pan_number'          => 'nullable|string',
            'qualification'       => 'nullable|string',
            'experience_years'    => 'nullable|integer',
            'date_of_birth'       => 'nullable|date',
            'additional_fields'   => 'nullable|array',
            'additional_fields.aadhar_number' => 'nullable|string',
            'additional_fields.alternate_phone' => 'nullable|digits:10',
            'additional_fields.father_phone' => 'nullable|digits:10',
        ]);

        $user = $staff->user;
        if ($user) {
            $user->name = trim($request->first_name . ' ' . $request->last_name);
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->is_active = $request->is_active;
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            $user->save();

            // Re-sync Spatie Role based on Designation
            $designation = Designation::findOrFail($request->designation_id);
            $roleName = str_contains(strtolower($designation->name), 'admin') || str_contains(strtolower($designation->name), 'principal')
                ? 'school_admin'
                : (str_contains(strtolower($designation->name), 'accountant') ? 'accountant' : 'teacher');
            $user->syncRoles([$roleName]);
        }

        $photoPath = $staff->photo;
        if ($request->filled('captured_photo')) {
            $photoPath = $this->saveBase64Photo($request->input('captured_photo'), 'staff-photos', $staff->photo);
        } elseif ($request->hasFile('photo')) {
            if ($staff->photo && Storage::disk('public')->exists($staff->photo)) {
                Storage::disk('public')->delete($staff->photo);
            }
            $photoPath = $request->file('photo')->store('staff-photos', 'public');
        }

        $staff->update(array_merge(
            $request->except(['photo', 'captured_photo']),
            ['photo' => $photoPath]
        ));
        $staff->designations()->sync([$request->designation_id]);

        return redirect()->route('school.staff.index')->with('success', 'Staff details updated successfully!');
    }

    /**
     * Delete staff profile.
     */
    public function destroy(Staff $staff)
    {
        if ($staff->school_id !== auth()->user()->school_id) {
            abort(403, 'Unauthorized.');
        }

        $user = $staff->user;
        $staff->delete();
        if ($user) {
            $user->delete();
        }

        return redirect()->route('school.staff.index')->with('success', 'Staff profile deleted successfully!');
    }

    /**
     * Show bulk import form.
     */
    public function importForm()
    {
        return view('school.staff.import');
    }

    /**
     * Download bulk staff import template (CSV).
     */
    public function downloadTemplate()
    {
        $headers = [
            'First Name * (required)',
            'Last Name',
            'Email * (required)',
            'Joining Date (dd/mm/yyyy)',
            'Employee ID * (required)',
            'Gender',
            'DOB (dd/mm/yyyy)',
            'Phone number * (required)',
            'Staff Type',
            'Adhar Number',
            'Pan Number',
            'House Number',
            'Location',
            'Country',
            'State',
            'Zip',
            'Bank Name',
            'Account Name',
            'Account Number',
            'Ifsc Code',
            'City',
            'Highest Qualification'
        ];

        $exampleRow = [
            'John',
            'Doe',
            'john.doe@yis.com',
            '01/06/2026',
            'EMP101',
            'male',
            '15/05/1990',
            '9876543210',
            'Teaching',
            '123456789012',
            'ABCDE1234F',
            '123',
            'Main Street',
            'India',
            'Delhi',
            '110001',
            'State Bank of India',
            'John Doe',
            '12345678901',
            'SBIN0001234',
            'New Delhi',
            'B.Ed, M.Sc'
        ];

        return response()->streamDownload(function () use ($headers, $exampleRow) {
            $output = fopen('php://output', 'w');
            fputcsv($output, $headers);
            fputcsv($output, $exampleRow);
            fclose($output);
        }, 'staff_import_template.csv', [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="staff_import_template.csv"',
        ]);
    }

    /**
     * Process bulk staff import.
     */
    public function bulkImport(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|max:10240',
        ]);

        $schoolId = auth()->user()->school_id;
        $file = $request->file('csv_file');

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to read spreadsheet file: ' . $e->getMessage());
        }

        if (empty($rows)) {
            return back()->with('error', 'Spreadsheet file is empty.');
        }

        $headerRow = $rows[0] ?? [];
        if (empty($headerRow)) {
            return back()->with('error', 'Spreadsheet file is empty.');
        }

        // Normalization helper for headers
        $normalizeHeader = function($name) {
            $name = strtolower(trim((string)$name));
            // Remove parenthesized content (e.g. "(dd/mm/yyyy)", "*(required)")
            $name = preg_replace('/\s*\(.*?\)\s*/', '', $name);
            // Remove asterisks
            $name = str_replace('*', '', $name);
            // Strip spaces, dashes, and underscores for key lookup
            $name = preg_replace('/[\s_-]+/', '', $name);
            
            $aliases = [
                'employeeid' => 'employee_id',
                'firstname' => 'first_name',
                'lastname' => 'last_name',
                'email' => 'email',
                'phone' => 'phone',
                'phonenumber' => 'phone',
                'joiningdate' => 'joining_date',
                'dateofjoining' => 'joining_date',
                'gender' => 'gender',
                'dob' => 'date_of_birth',
                'dateofbirth' => 'date_of_birth',
                'stafftype' => 'staff_type',
                'adharnumber' => 'aadhar_number',
                'aadhar' => 'aadhar_number',
                'adhar' => 'aadhar_number',
                'aadharnumber' => 'aadhar_number',
                'pannumber' => 'pan_number',
                'pan' => 'pan_number',
                'housenumber' => 'house_number',
                'location' => 'location',
                'country' => 'country',
                'state' => 'state',
                'zip' => 'pincode',
                'zipcode' => 'pincode',
                'pincode' => 'pincode',
                'bankname' => 'bank_name',
                'accountname' => 'account_name',
                'accountnumber' => 'bank_account_number',
                'bankaccountnumber' => 'bank_account_number',
                'ifsccode' => 'ifsc_code',
                'ifsc' => 'ifsc_code',
                'city' => 'city',
                'highestqualification' => 'qualification',
                'qualification' => 'qualification',
            ];
            
            return $aliases[$name] ?? $name;
        };

        // Map column header names to their column index
        $headerMap = [];
        foreach ($headerRow as $index => $rawHeader) {
            if ($rawHeader !== null && $rawHeader !== '') {
                $normalized = $normalizeHeader($rawHeader);
                $headerMap[$normalized] = $index;
            }
        }

        // Helper function to safely fetch cell value by header name
        $val = function($row, $headerName) use ($headerMap) {
            if (isset($headerMap[$headerName]) && isset($row[$headerMap[$headerName]])) {
                return trim((string)$row[$headerMap[$headerName]]);
            }
            return '';
        };

        // Date parser helper
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

        $imported = 0;
        $skipped = 0;

        $dataRows = array_slice($rows, 1);
        foreach ($dataRows as $row) {
            // Skip completely empty rows
            if (empty(array_filter($row))) {
                continue;
            }
            // Basic validation
            $empId     = $val($row, 'employee_id');
            $firstName = $val($row, 'first_name');
            $lastName  = $val($row, 'last_name') ?: '';
            $email     = $val($row, 'email');
            $phone     = $val($row, 'phone');

            if (empty($empId) || empty($firstName) || empty($email) || empty($phone)) {
                $skipped++;
                continue;
            }

            // Skip if duplicate email or employee ID
            if (User::where('email', $email)->exists() || Staff::where('school_id', $schoolId)->where('employee_id', $empId)->exists()) {
                $skipped++;
                continue;
            }

            // 1. Resolve Department
            $deptName = $val($row, 'department') ?: 'Academics';
            $dept = Department::firstOrCreate([
                'school_id' => $schoolId,
                'name'      => $deptName
            ]);

            // 2. Resolve Designation
            $desgName = $val($row, 'designation') ?: 'Teacher';
            $desg = Designation::firstOrCreate([
                'school_id' => $schoolId,
                'name'      => $desgName
            ]);

            // 3. Create linked User
            $user = User::create([
                'name'      => trim($firstName . ' ' . $lastName),
                'email'     => $email,
                'phone'     => $phone ?: null,
                'password'  => Hash::make('Welcome@2026!'),
                'school_id' => $schoolId,
                'is_active' => true,
            ]);

            // Assign Spatie Role based on Designation
            $roleName = 'teacher';
            $lowerDesg = strtolower($desgName);
            if (str_contains($lowerDesg, 'admin') || str_contains($lowerDesg, 'principal')) {
                $roleName = 'school_admin';
            } elseif (str_contains($lowerDesg, 'accountant')) {
                $roleName = 'accountant';
            } elseif (str_contains($lowerDesg, 'driver')) {
                $roleName = 'driver';
            }
            $user->assignRole($roleName);

            // 4. Group JSON additional fields
            $additionalFields = [
                'alternate_phone'          => $val($row, 'alternate_phone'),
                'marital_status'           => $val($row, 'marital_status'),
                'category'                 => $val($row, 'category'),
                'religion'                 => $val($row, 'religion'),
                'mother_tongue'            => $val($row, 'mother_tongue'),
                'aadhar_number'            => $val($row, 'aadhar_number'),
                'epf_account'              => $val($row, 'epf_account'),
                'esi_account'              => $val($row, 'esi_account'),
                'epf_uan'                  => $val($row, 'epf_uan'),
                'epf_joining_date'         => $parseDate($val($row, 'epf_joining_date')),
                'epf_exit_date'            => $parseDate($val($row, 'epf_exit_date')),
                'esi_joining_date'         => $parseDate($val($row, 'esi_joining_date')),
                'esi_exit_date'            => $parseDate($val($row, 'esi_exit_date')),
                'remarks'                  => $val($row, 'remarks'),
                'branch_name'              => $val($row, 'branch_name'),
                'father_name'              => $val($row, 'father_name'),
                'father_phone'             => $val($row, 'father_phone'),
                'mother_name'              => $val($row, 'mother_name'),
                'mother_phone'             => $val($row, 'mother_phone'),
                'spouse_name'              => $val($row, 'spouse_name'),
                'spouse_phone'             => $val($row, 'spouse_phone'),
                'passport_number'          => $val($row, 'passport_number'),
                'visa_details'             => $val($row, 'visa_details'),
                'correspondence_address'   => $val($row, 'correspondence_address'),
                'correspondence_city'      => $val($row, 'correspondence_city'),
                'correspondence_state'     => $val($row, 'correspondence_state'),
                'correspondence_pincode'   => $val($row, 'correspondence_pincode'),
                'emergency_contact_name'   => $val($row, 'emergency_contact_name'),
                'emergency_relationship'   => $val($row, 'emergency_relationship'),
                'emergency_contact_phone'  => $val($row, 'emergency_contact_phone'),
                'emergency_alt_phone'      => $val($row, 'emergency_alt_phone'),
                'dl_number'                => $val($row, 'dl_number'),
                'dl_expiry'                => $parseDate($val($row, 'dl_expiry')),
                'gross_salary'             => $val($row, 'gross_salary'),
                'net_salary'               => $val($row, 'net_salary'),
                'deductions'               => $val($row, 'deductions'),
                'linkedin_url'             => $val($row, 'linkedin_url'),
                'facebook_url'             => $val($row, 'facebook_url'),
                'twitter_url'              => $val($row, 'twitter_url'),
                
                // Add the user's custom fields
                'staff_type'               => $val($row, 'staff_type'),
                'house_number'             => $val($row, 'house_number'),
                'location'                 => $val($row, 'location'),
                'country'                  => $val($row, 'country'),
                'account_name'             => $val($row, 'account_name'),
            ];

            // Build permanent address from House Number and Location if permanent_address is empty
            $address = $val($row, 'permanent_address') ?: trim($val($row, 'house_number') . ' ' . $val($row, 'location'));
            if (empty($address)) {
                $address = null;
            }

            // 5. Create Staff Profile
            $staff = Staff::create([
                'school_id'           => $schoolId,
                'user_id'             => $user->id,
                'employee_id'         => $empId,
                'first_name'          => $firstName,
                'last_name'           => $lastName,
                'email'               => $email,
                'phone'               => $phone ?: null,
                'joining_date'        => $parseDate($val($row, 'joining_date')) ?: today()->toDateString(),
                'date_of_birth'       => $parseDate($val($row, 'date_of_birth')),
                'gender'              => in_array(strtolower($val($row, 'gender')), ['male', 'female', 'other']) ? strtolower($val($row, 'gender')) : null,
                'blood_group'         => $val($row, 'blood_group') ?: null,
                'address'             => $address,
                'city'                => $val($row, 'city') ?: null,
                'state'               => $val($row, 'state') ?: null,
                'pincode'             => $val($row, 'pincode') ?: null,
                'department_id'       => $dept->id,
                'designation_id'      => $desg->id,
                'employment_type'     => in_array(strtolower($val($row, 'employment_type')), ['permanent', 'contract', 'part_time']) ? strtolower($val($row, 'employment_type')) : 'permanent',
                'qualification'       => $val($row, 'qualification') ?: null,
                'experience_years'    => intval($val($row, 'experience_years')) ?: 0,
                'basic_salary'        => floatval($val($row, 'basic_salary')) ?: 0.00,
                'bank_account_number' => $val($row, 'bank_account_number') ?: null,
                'bank_name'           => $val($row, 'bank_name') ?: null,
                'ifsc_code'           => $val($row, 'ifsc_code') ?: null,
                'pan_number'          => $val($row, 'pan_number') ?: null,
                'is_active'           => true,
                'additional_fields'   => $additionalFields,
            ]);
            $staff->designations()->sync([$desg->id]);

            $imported++;
        }

        return redirect()->route('school.staff.index')->with('success', "Bulk import complete! Imported: {$imported}, Skipped: {$skipped}");
    }

    /**
     * Show bulk photo upload form.
     */
    public function bulkPhotoForm()
    {
        return view('school.staff.bulk_photo');
    }

    /**
     * Process bulk photo upload.
     */
    public function bulkPhotoUpload(Request $request)
    {
        $request->validate([
            'photos'   => 'required|array',
            'photos.*' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $schoolId = auth()->user()->school_id;
        $updated = 0;

        // Fetch all staff members for normalization matching
        $staffMembers = Staff::where('school_id', $schoolId)->get();

        foreach ($request->file('photos') as $file) {
            $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $normalizedFilename = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $filename));

            // Find matching staff member
            $staff = $staffMembers->first(function($st) use ($normalizedFilename) {
                $normEmpId = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $st->employee_id));
                return $normEmpId === $normalizedFilename;
            });

            if ($staff) {
                if ($staff->photo && Storage::disk('public')->exists($staff->photo)) {
                    Storage::disk('public')->delete($staff->photo);
                }
                $path = $file->store('staff-photos', 'public');
                $staff->photo = $path;
                $staff->save();
                $updated++;
            }
        }

        return back()->with('success', "Bulk photo upload complete! Updated {$updated} staff profiles.");
    }

    /**
     * Show staff mark bulk attendance page.
     */
    public function bulkAttendance(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $date = $request->get('date', today()->toDateString());
        $deptId = $request->get('department_id');

        $departments = Department::where('school_id', $schoolId)->get();
        $staffMembers = collect();

        if ($deptId) {
            $staffMembers = Staff::where('school_id', $schoolId)
                ->where('department_id', $deptId)
                ->where('is_active', true)
                ->orderBy('first_name')
                ->get();
        }

        // Fetch existing attendance records
        $attendance = StaffAttendance::where('school_id', $schoolId)
            ->whereDate('date', $date)
            ->get()
            ->keyBy('staff_id');

        return view('school.staff.bulk_attendance', compact('departments', 'staffMembers', 'attendance', 'date', 'deptId'));
    }

    /**
     * Save bulk attendance.
     */
    public function saveBulkAttendance(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*.status' => 'required|in:present,absent,late',
        ]);

        $schoolId = auth()->user()->school_id;
        $date = $request->date;

        foreach ($request->attendance as $staffId => $data) {
            $staff = Staff::where('school_id', $schoolId)->findOrFail($staffId);

            StaffAttendance::updateOrCreate(
                [
                    'school_id' => $schoolId,
                    'staff_id'  => $staffId,
                    'date'      => $date,
                ],
                [
                    'status'    => $data['status'],
                    'marked_by' => auth()->id(),
                ]
            );
        }

        return back()->with('success', 'Staff attendance updated successfully!');
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

    public function downloadProfile(Staff $staff)
    {
        $schoolId = auth()->user()->school_id;
        if ($staff->school_id !== $schoolId) {
            abort(403, 'Unauthorized.');
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('school.staff.profile-pdf', compact('staff'));
        return $pdf->stream("staff_profile_{$staff->employee_id}.pdf");
    }
}
