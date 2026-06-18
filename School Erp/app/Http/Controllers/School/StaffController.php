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
            'employee_id'    => 'required|string|max:50|unique:staff,employee_id,NULL,id,school_id,' . $schoolId,
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'required|string|max:100',
            'email'          => 'required|email|unique:users,email',
            'phone'          => 'nullable|string|max:20',
            'password'       => 'nullable|string|min:6',
            'department_id'  => 'required|exists:departments,id',
            'designation_id' => 'required|exists:designations,id',
            'joining_date'   => 'required|date',
            'employment_type'=> 'required|in:permanent,contract,part_time',
            'basic_salary'   => 'required|numeric|min:0',
            'is_active'      => 'required|boolean',
            'photo'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
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
            'employee_id'    => 'required|string|max:50|unique:staff,employee_id,' . $staff->id . ',id,school_id,' . $schoolId,
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'required|string|max:100',
            'email'          => 'required|email|unique:users,email,' . $staff->user_id,
            'phone'          => 'nullable|string|max:20',
            'password'       => 'nullable|string|min:6',
            'department_id'  => 'required|exists:departments,id',
            'designation_id' => 'required|exists:designations,id',
            'joining_date'   => 'required|date',
            'employment_type'=> 'required|in:permanent,contract,part_time',
            'basic_salary'   => 'required|numeric|min:0',
            'is_active'      => 'required|boolean',
            'photo'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
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
     * Process bulk staff import.
     */
    public function bulkImport(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:4096',
        ]);

        $schoolId = auth()->user()->school_id;
        $file = $request->file('csv_file');
        $handle = fopen($file->getPathname(), 'r');
        $header = fgetcsv($handle);

        $imported = 0;
        $skipped = 0;

        // Default department and designation
        $defaultDept = Department::where('school_id', $schoolId)->first();
        $defaultDesg = Designation::where('school_id', $schoolId)->first();

        if (!$defaultDept || !$defaultDesg) {
            return back()->with('error', 'Please configure at least one department and designation first.');
        }

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 5) {
                $skipped++;
                continue;
            }

            $empId     = trim($row[0]);
            $firstName = trim($row[1]);
            $lastName  = trim($row[2]);
            $email     = trim($row[3]);
            $phone     = trim($row[4]);

            // Skip if duplicate email or employee ID
            if (User::where('email', $email)->exists() || Staff::where('school_id', $schoolId)->where('employee_id', $empId)->exists()) {
                $skipped++;
                continue;
            }

            $user = User::create([
                'name'      => trim($firstName . ' ' . $lastName),
                'email'     => $email,
                'phone'     => $phone,
                'password'  => Hash::make('Welcome@2026!'),
                'school_id' => $schoolId,
                'is_active' => true,
            ]);
            $user->assignRole('teacher');

            Staff::create([
                'school_id'      => $schoolId,
                'user_id'        => $user->id,
                'employee_id'    => $empId,
                'first_name'     => $firstName,
                'last_name'      => $lastName,
                'email'          => $email,
                'phone'          => $phone,
                'joining_date'   => today(),
                'department_id'  => $defaultDept->id,
                'designation_id' => $defaultDesg->id,
                'employment_type'=> 'permanent',
                'basic_salary'   => 20000.00,
                'is_active'      => true,
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
