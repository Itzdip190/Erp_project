<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\StudentHouse;
use App\Models\StudentCategory;
use App\Models\AcademicSession;
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
        $udise = is_array($school->udise_data) ? $school->udise_data : json_decode($school->udise_data ?? '[]', true);
        $houses = StudentHouse::where('school_id', $school->id)->get();
        $groups = StudentCategory::where('school_id', $school->id)->get();
        
        $currentSession = AcademicSession::where('school_id', $school->id)
            ->where('is_current', true)
            ->first();

        return view('school.settings.institute_info', compact('school', 'udise', 'houses', 'groups', 'currentSession'));
    }

    /**
     * Update Basic Institute Info.
     */
    public function updateInstituteInfo(Request $request)
    {
        $request->validate([
            'name'                        => 'required|string|max:150',
            'code'                        => 'required|string|max:20|unique:schools,code,' . Auth::user()->school_id,
            'affiliation_number'          => 'nullable|string|max:100',
            'udise_number'                => 'nullable|string|max:100',
            'board_name'                  => 'nullable|string|max:100',
            'logo'                        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'stamp'                       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'signature'                   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'phone'                       => 'nullable|string|max:50',
            'email'                       => 'nullable|email|max:150',
            'address'                     => 'nullable|string|max:1000',
            'academic_session_name'       => 'nullable|string|max:50',
            'academic_session_start_date' => 'nullable|date',
            'academic_session_end_date'   => 'nullable|date|after_or_equal:academic_session_start_date',
        ]);

        $school = Auth::user()->school;
        $school->name = $request->name;
        $school->code = $request->code;
        $school->phone = $request->phone;
        $school->email = $request->email;
        $school->address = $request->address;

        $udise = is_array($school->udise_data) ? $school->udise_data : json_decode($school->udise_data ?? '[]', true);
        $udise['affiliation_number'] = $request->affiliation_number;
        $udise['udise_code'] = $request->udise_number; // keep in sync with government reporting code
        $udise['udise_number'] = $request->udise_number;
        $udise['board_name'] = $request->board_name;

        if ($request->hasFile('logo')) {
            if ($school->logo && Storage::disk('public')->exists($school->logo)) {
                Storage::disk('public')->delete($school->logo);
            }
            $school->logo = $request->file('logo')->store('school-logos', 'public');
        }

        if ($request->hasFile('stamp')) {
            if (!empty($udise['stamp']) && Storage::disk('public')->exists($udise['stamp'])) {
                Storage::disk('public')->delete($udise['stamp']);
            }
            $udise['stamp'] = $request->file('stamp')->store('school-stamps', 'public');
        }

        if ($request->hasFile('signature')) {
            if (!empty($udise['signature']) && Storage::disk('public')->exists($udise['signature'])) {
                Storage::disk('public')->delete($udise['signature']);
            }
            $udise['signature'] = $request->file('signature')->store('school-signatures', 'public');
        }

        $school->udise_data = $udise;
        $school->save();

        if ($request->filled(['academic_session_name', 'academic_session_start_date', 'academic_session_end_date'])) {
            // Update or create current academic session
            $currentSession = AcademicSession::where('school_id', $school->id)
                ->where('is_current', true)
                ->first();
                
            if ($currentSession) {
                $currentSession->update([
                    'name'       => $request->academic_session_name,
                    'start_date' => $request->academic_session_start_date,
                    'end_date'   => $request->academic_session_end_date,
                ]);
            } else {
                AcademicSession::create([
                    'school_id'  => $school->id,
                    'name'       => $request->academic_session_name,
                    'start_date' => $request->academic_session_start_date,
                    'end_date'   => $request->academic_session_end_date,
                    'is_current' => true,
                ]);
            }
        }

        return back()->with('success', 'Institute information updated successfully!');
    }

    /**
     * Update Institute Days & Hours.
     */
    public function updateInstituteHours(Request $request)
    {
        $school = Auth::user()->school;
        $udise = is_array($school->udise_data) ? $school->udise_data : json_decode($school->udise_data ?? '[]', true);

        $hoursData = [];
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        foreach ($days as $day) {
            $hoursData[$day] = [
                'start_time' => $request->input("hours.{$day}.start_time") ?: '-',
                'end_time'   => $request->input("hours.{$day}.end_time")   ?: '-',
            ];
        }

        $udise['days_and_time'] = $hoursData;
        $school->udise_data = $udise;
        $school->save();

        return back()->with('success', 'Institute days and timings updated successfully!');
    }

    /**
     * Add Student House.
     */
    public function addHouse(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:100',
            'color_code' => 'nullable|string|max:7',
        ]);

        StudentHouse::create([
            'school_id'  => Auth::user()->school_id,
            'name'       => $request->name,
            'color_code' => $request->color_code ?: '#2563eb',
        ]);

        return back()->with('success', 'Student House added successfully!');
    }

    /**
     * Delete Student House.
     */
    public function deleteHouse($id)
    {
        $house = StudentHouse::where('school_id', Auth::user()->school_id)->findOrFail($id);
        $house->delete();

        return back()->with('success', 'Student House deleted successfully!');
    }

    /**
     * Add Student Group (Category).
     */
    public function addGroup(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
        ]);

        StudentCategory::create([
            'school_id'   => Auth::user()->school_id,
            'name'        => $request->name,
            'description' => $request->description,
        ]);

        return back()->with('success', 'Student Group / Category added successfully!');
    }

    /**
     * Delete Student Group (Category).
     */
    public function deleteGroup($id)
    {
        $group = StudentCategory::where('school_id', Auth::user()->school_id)->findOrFail($id);
        $group->delete();

        return back()->with('success', 'Student Group / Category deleted successfully!');
    }

    /**
     * Update Social Media URLs.
     */
    public function updateSocialMedia(Request $request)
    {
        $school = Auth::user()->school;
        $udise = is_array($school->udise_data) ? $school->udise_data : json_decode($school->udise_data ?? '[]', true);

        $social = [
            'facebook'  => $request->facebook,
            'twitter'   => $request->twitter,
            'instagram' => $request->instagram,
            'youtube'   => $request->youtube,
            'linkedin'  => $request->linkedin,
        ];

        $udise['social_media'] = $social;
        $school->udise_data = $udise;
        $school->save();

        return back()->with('success', 'Social media links updated successfully!');
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

        // Fetch school-scoped active students and classes
        $classes = \App\Models\SchoolClass::where('school_id', $school->id)->orderBy('numeric_name')->get();
        $students = \App\Models\Student::where('school_id', $school->id)->where('is_active', true)->get();
        $categories = \App\Models\StudentCategory::where('school_id', $school->id)->get();
        $categoryNames = $categories->pluck('name', 'id')->toArray();

        $enrollmentData = [];
        $grandTotalStudents = 0;
        foreach ($classes as $class) {
            $classStudents = $students->where('class_id', $class->id);
            $totalClassStudents = $classStudents->count();
            $grandTotalStudents += $totalClassStudents;

            $row = [
                'class_id' => $class->id,
                'class_name' => $class->name,
                'total_students' => $totalClassStudents,
                'general' => ['boys' => 0, 'girls' => 0],
                'sc' => ['boys' => 0, 'girls' => 0],
                'st' => ['boys' => 0, 'girls' => 0],
                'obc' => ['boys' => 0, 'girls' => 0],
            ];

            foreach ($classStudents as $student) {
                $catId = $student->category_id;
                $catName = isset($categoryNames[$catId]) ? strtolower($categoryNames[$catId]) : 'general';
                $gender = strtolower($student->gender) === 'male' ? 'boys' : 'girls';

                if (str_contains($catName, 'sc')) {
                    $row['sc'][$gender]++;
                } elseif (str_contains($catName, 'st')) {
                    $row['st'][$gender]++;
                } elseif (str_contains($catName, 'obc')) {
                    $row['obc'][$gender]++;
                } else {
                    $row['general'][$gender]++;
                }
            }
            $enrollmentData[] = $row;
        }

        // Fetch staff and count teachers
        $designations = \App\Models\Designation::where('school_id', $school->id)->get();
        $teacherDesignationIds = $designations->filter(function ($des) {
            $name = strtolower($des->name);
            return str_contains($name, 'teacher') || str_contains($name, 'faculty') || str_contains($name, 'lecturer') || str_contains($name, 'principal') || str_contains($name, 'academic');
        })->pluck('id')->toArray();

        $allStaff = \App\Models\Staff::where('school_id', $school->id)->where('is_active', true)->get();
        $teachers = $allStaff->filter(function ($staff) use ($teacherDesignationIds) {
            if (empty($teacherDesignationIds)) {
                return true; // fallback to all active staff if no teacher designations
            }
            return in_array($staff->designation_id, $teacherDesignationIds);
        });

        $totalTeachers = $teachers->count();
        $maleTeachers = $teachers->where('gender', 'male')->count();
        $femaleTeachers = $teachers->where('gender', 'female')->count();

        $regularCount = 0;
        $contractCount = 0;
        foreach ($teachers as $t) {
            $type = strtolower($t->employment_type ?? '');
            if (str_contains($type, 'contract') || str_contains($type, 'part') || str_contains($type, 'temp')) {
                $contractCount++;
            } else {
                $regularCount++;
            }
        }

        $ptr = $totalTeachers > 0 ? round($grandTotalStudents / $totalTeachers, 2) : 0;

        $teacherCounts = [
            'total' => $totalTeachers,
            'male' => $maleTeachers,
            'female' => $femaleTeachers,
            'regular' => $regularCount,
            'contract' => $contractCount,
            'ptr' => $ptr
        ];

        return view('school.settings.udise', compact(
            'school', 'udise', 'grandTotalStudents', 'enrollmentData', 'teacherCounts'
        ));
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

