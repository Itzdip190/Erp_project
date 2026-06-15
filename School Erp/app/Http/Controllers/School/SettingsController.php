<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    /**
     * Show the settings page.
     */
    public function index()
    {
        $user = Auth::user();
        return view('school.settings.index', compact('user'));
    }

    /**
     * Update profile (name + photo).
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:100',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $user = Auth::user();
        $user->name = $request->name;

        if ($request->hasFile('photo')) {
            // Delete old photo if it exists
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }
            $path = $request->file('photo')->store('profile-photos', 'public');
            $user->photo = $path;
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully!');
    }

    /**
     * Change password.
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.'])->withInput();
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password changed successfully!');
    }
    /**
     * Show the Basic Institute Info page.
     */
    public function instituteInfo()
    {
        $school = Auth::user()->school;
        return view('school.settings.institute_info', compact('school'));
    }

    /**
     * Update Basic Institute Info.
     */
    public function updateInstituteInfo(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:150',
            'email'   => 'nullable|email|max:100',
            'phone'   => 'nullable|string|max:20',
            'code'    => 'required|string|max:20|unique:schools,code,' . Auth::user()->school_id,
            'address' => 'nullable|string',
            'logo'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $school = Auth::user()->school;
        $school->name = $request->name;
        $school->email = $request->email;
        $school->phone = $request->phone;
        $school->code = $request->code;
        $school->address = $request->address;

        if ($request->hasFile('logo')) {
            if ($school->logo && Storage::disk('public')->exists($school->logo)) {
                Storage::disk('public')->delete($school->logo);
            }
            $path = $request->file('logo')->store('school-logos', 'public');
            $school->logo = $path;
        }

        $school->save();

        return back()->with('success', 'Institute information updated successfully!');
    }

    /**
     * Show the Implementation Process wizard.
     */
    public function implementationProcess()
    {
        $schoolId = Auth::user()->school_id;

        // Dynamic checks for setup progress
        $sessionCount = \App\Models\AcademicSession::where('school_id', $schoolId)->count();
        $classCount   = \App\Models\SchoolClass::where('school_id', $schoolId)->count();
        $sectionCount = \App\Models\Section::where('school_id', $schoolId)->count();
        $staffCount   = \App\Models\Staff::where('school_id', $schoolId)->count();
        $studentCount = \App\Models\Student::where('school_id', $schoolId)->count();

        $steps = [
            ['title' => 'Academic Session Setup', 'desc' => 'Define the current academic year.', 'done' => $sessionCount > 0, 'val' => $sessionCount . ' sessions'],
            ['title' => 'Classes & Sections', 'desc' => 'Create school grades and sections.', 'done' => $classCount > 0 && $sectionCount > 0, 'val' => $classCount . ' classes, ' . $sectionCount . ' sections'],
            ['title' => 'Register Staff & Teachers', 'desc' => 'Add your academic & administration staff.', 'done' => $staffCount > 0, 'val' => $staffCount . ' staff members'],
            ['title' => 'Import Students', 'desc' => 'Admit students into classes.', 'done' => $studentCount > 0, 'val' => $studentCount . ' students admitted'],
            ['title' => 'Start Attendance marking', 'desc' => 'Begin marking daily student/staff attendance.', 'done' => \App\Models\StudentAttendance::where('school_id', $schoolId)->exists(), 'val' => 'Ready to mark'],
        ];

        return view('school.settings.implementation', compact('steps'));
    }

    /**
     * Show UDISE report page.
     */
    public function udise()
    {
        $school = Auth::user()->school;
        $udise = is_array($school->udise_data) ? $school->udise_data : json_decode($school->udise_data ?? '[]', true);

        // Fetch counts for summary
        $totalStudents = \App\Models\Student::where('school_id', $school->id)->count();
        $totalStaff    = \App\Models\Staff::where('school_id', $school->id)->count();

        return view('school.settings.udise', compact('school', 'udise', 'totalStudents', 'totalStaff'));
    }

    /**
     * Update UDISE report details.
     */
    public function updateUdise(Request $request)
    {
        $school = Auth::user()->school;

        $school->udise_data = $request->except(['_token', '_method']);
        $school->save();

        return back()->with('success', 'UDISE details saved successfully!');
    }

    /**
     * Show reset password panel.
     */
    public function resetPasswordPage(Request $request)
    {
        $schoolId = Auth::user()->school_id;
        $search = $request->get('search');
        $users = collect();

        if ($search) {
            $users = \App\Models\User::where('school_id', $schoolId)
                ->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                })
                ->with('roles')
                ->take(15)
                ->get();
        }

        return view('school.settings.reset_password', compact('users', 'search'));
    }

    /**
     * Reset selected user's password.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'user_id'  => 'required|exists:users,id',
            'password' => ['required', Password::min(8)],
        ]);

        $user = \App\Models\User::where('school_id', Auth::user()->school_id)
            ->findOrFail($request->user_id);

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', "Password for {$user->name} reset successfully!");
    }
}

