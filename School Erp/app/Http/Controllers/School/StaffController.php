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
            'phone'               => 'required|string|max:20',
            'password'            => 'nullable|string|min:6',
            'department_id'       => 'required|exists:departments,id',
            'designation_id'      => 'required|exists:designations,id',
            'joining_date'        => 'required|date',
            'employment_type'     => 'required|in:permanent,contract,part_time',
            'basic_salary'        => 'required|numeric|min:0',
            'is_active'           => 'required|boolean',
            'photo'               => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
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
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('staff-photos', 'public');
        }

        // 3. Create Staff profile
        Staff::create(array_merge(
            $request->except('photo'),
            [
                'school_id' => $schoolId,
                'user_id'   => $user->id,
                'photo'     => $photoPath
            ]
        ));

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
            'phone'               => 'required|string|max:20',
            'password'            => 'nullable|string|min:6',
            'department_id'       => 'required|exists:departments,id',
            'designation_id'      => 'required|exists:designations,id',
            'joining_date'        => 'required|date',
            'employment_type'     => 'required|in:permanent,contract,part_time',
            'basic_salary'        => 'required|numeric|min:0',
            'is_active'           => 'required|boolean',
            'photo'               => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
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
        if ($request->hasFile('photo')) {
            if ($staff->photo && Storage::disk('public')->exists($staff->photo)) {
                Storage::disk('public')->delete($staff->photo);
            }
            $photoPath = $request->file('photo')->store('staff-photos', 'public');
        }

        $staff->update(array_merge(
            $request->except('photo'),
            ['photo' => $photoPath]
        ));

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
            'employee_id', 'first_name', 'last_name', 'email', 'phone', 'alternate_phone',
            'date_of_birth', 'gender', 'marital_status', 'category', 'blood_group', 'religion',
            'mother_tongue', 'pan_number', 'aadhar_number', 'department', 'designation',
            'employment_type', 'joining_date', 'basic_salary', 'qualification', 'experience_years',
            'epf_account', 'esi_account', 'epf_uan', 'epf_joining_date', 'epf_exit_date',
            'esi_joining_date', 'esi_exit_date', 'remarks', 'bank_name', 'bank_account_number',
            'ifsc_code', 'branch_name', 'father_name', 'father_phone', 'mother_name', 'mother_phone',
            'spouse_name', 'spouse_phone', 'passport_number', 'visa_details', 'permanent_address',
            'permanent_city', 'permanent_state', 'permanent_pincode', 'correspondence_address',
            'correspondence_city', 'correspondence_state', 'correspondence_pincode',
            'emergency_contact_name', 'emergency_relationship', 'emergency_contact_phone',
            'emergency_alt_phone', 'dl_number', 'dl_expiry', 'gross_salary', 'net_salary',
            'deductions', 'linkedin_url', 'facebook_url', 'twitter_url'
        ];

        $exampleRow = [
            'EMP101', 'John', 'Doe', 'john.doe@yis.com', '9876543210', '9876543211',
            '1990-05-15', 'male', 'married', 'General', 'O+', 'Christianity',
            'English', 'ABCDE1234F', '123456789012', 'Academics', 'Teacher',
            'permanent', '2026-06-01', '25000.00', 'B.Ed, M.Sc', '5',
            'EPF12345', 'ESI12345', 'UAN123456789', '2026-06-01', '',
            '2026-06-01', '', 'Hardworking staff', 'State Bank of India', '12345678901',
            'SBIN0001234', 'Main Branch', 'Robert Doe', '9876543212', 'Mary Doe', '9876543213',
            'Sarah Doe', '9876543214', 'PP123456', 'Valid till 2030', '123 Main St',
            'New York', 'NY', '10001', '456 Side St',
            'New York', 'NY', '10001',
            'Jane Doe', 'Sister', '9876543215',
            '9876543216', 'DL12345', '2035-12-31', '30000.00', '25000.00',
            '5000.00', 'https://linkedin.com/in/johndoe', 'https://facebook.com/johndoe', 'https://twitter.com/johndoe'
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
            'csv_file' => 'required|file|mimes:csv,txt|max:8192',
        ]);

        $schoolId = auth()->user()->school_id;
        $file = $request->file('csv_file');
        $handle = fopen($file->getPathname(), 'r');
        $headerRow = fgetcsv($handle);

        if (!$headerRow) {
            return back()->with('error', 'CSV file is empty.');
        }

        // Map column header names to their column index
        $headerMap = array_flip(array_map('trim', $headerRow));

        // Helper function to safely fetch cell value by header name
        $val = function($row, $headerName) use ($headerMap) {
            if (isset($headerMap[$headerName]) && isset($row[$headerMap[$headerName]])) {
                return trim($row[$headerMap[$headerName]]);
            }
            return '';
        };

        $imported = 0;
        $skipped = 0;

        while (($row = fgetcsv($handle)) !== false) {
            // Basic validation
            $empId     = $val($row, 'employee_id');
            $firstName = $val($row, 'first_name');
            $lastName  = $val($row, 'last_name');
            $email     = $val($row, 'email');

            if (empty($empId) || empty($firstName) || empty($lastName) || empty($email)) {
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
                'phone'     => $val($row, 'phone') ?: null,
                'password'  => Hash::make('Welcome@2026!'),
                'school_id' => $schoolId,
                'is_active' => true,
            ]);

            // Assign Spatie Role based on Designation
            $roleName = str_contains(strtolower($desgName), 'admin') || str_contains(strtolower($desgName), 'principal')
                ? 'school_admin'
                : (str_contains(strtolower($desgName), 'accountant') ? 'accountant' : 'teacher');
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
                'epf_joining_date'         => $val($row, 'epf_joining_date'),
                'epf_exit_date'            => $val($row, 'epf_exit_date'),
                'esi_joining_date'         => $val($row, 'esi_joining_date'),
                'esi_exit_date'            => $val($row, 'esi_exit_date'),
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
                'dl_expiry'                => $val($row, 'dl_expiry'),
                'gross_salary'             => $val($row, 'gross_salary'),
                'net_salary'               => $val($row, 'net_salary'),
                'deductions'               => $val($row, 'deductions'),
                'linkedin_url'             => $val($row, 'linkedin_url'),
                'facebook_url'             => $val($row, 'facebook_url'),
                'twitter_url'              => $val($row, 'twitter_url'),
            ];

            // 5. Create Staff Profile
            Staff::create([
                'school_id'           => $schoolId,
                'user_id'             => $user->id,
                'employee_id'         => $empId,
                'first_name'          => $firstName,
                'last_name'           => $lastName,
                'email'               => $email,
                'phone'               => $val($row, 'phone') ?: null,
                'joining_date'        => $val($row, 'joining_date') ?: today()->toDateString(),
                'date_of_birth'       => $val($row, 'date_of_birth') ?: null,
                'gender'              => in_array(strtolower($val($row, 'gender')), ['male', 'female', 'other']) ? strtolower($val($row, 'gender')) : 'other',
                'blood_group'         => $val($row, 'blood_group') ?: null,
                'address'             => $val($row, 'permanent_address') ?: null,
                'city'                => $val($row, 'permanent_city') ?: null,
                'state'               => $val($row, 'permanent_state') ?: null,
                'pincode'             => $val($row, 'permanent_pincode') ?: null,
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

            $imported++;
        }
        fclose($handle);

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

        foreach ($request->file('photos') as $file) {
            $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $staff = Staff::where('school_id', $schoolId)
                ->where('employee_id', $filename)
                ->first();

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
}
