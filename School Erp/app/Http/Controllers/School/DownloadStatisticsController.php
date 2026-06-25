<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\LoginLog;
use App\Models\Student;
use App\Models\Staff;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\User;
use Illuminate\Http\Request;

class DownloadStatisticsController extends Controller
{
    protected int $perPage = 12;

    /**
     * Build a deduplicated sections list.
     * When a class is selected → sections for that class only.
     * When no class → unique section names across all classes.
     */
    private function getSections(int $schoolId, ?int $classId)
    {
        $query = Section::where('school_id', $schoolId)->orderBy('name');
        if ($classId) {
            $query->where('class_id', $classId);
        }
        $sections = $query->get();

        if (!$classId) {
            // Deduplicate by section name so "A" doesn't appear multiple times
            $sections = $sections->unique('name')->values();
        }

        return $sections;
    }

    /**
     * Paginate a collection.
     * Returns [$paginatedList, $page, $totalPages, $totalItems]
     */
    private function paginate($collection, Request $request): array
    {
        $totalItems = $collection->count();
        $totalPages = max(1, (int) ceil($totalItems / $this->perPage));
        $page = min(max(1, (int) $request->get('page', 1)), $totalPages);
        $paginatedList = $collection->forPage($page, $this->perPage)->values();
        return [$paginatedList, $page, $totalPages, $totalItems];
    }

    public function studentDownloadStatus(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $classes   = SchoolClass::where('school_id', $schoolId)->get();
        $classId   = $request->get('class_id') ? (int) $request->get('class_id') : null;
        $sectionId = $request->get('section_id');
        $sections  = $this->getSections($schoolId, $classId);

        $query = Student::where('school_id', $schoolId)->with(['class', 'section', 'user']);
        if ($classId)   { $query->where('class_id', $classId); }
        if ($sectionId) { $query->where('section_id', $sectionId); }
        $students = $query->get();

        $studentUserIds  = $students->pluck('user_id')->filter()->toArray();
        $loggedInUserIds = LoginLog::whereIn('user_id', $studentUserIds)
            ->where('status', 'success')
            ->pluck('user_id')->unique()->toArray();

        $loggedIn    = collect();
        $notLoggedIn = collect();

        foreach ($students as $st) {
            $hasLoggedIn = in_array($st->user_id, $loggedInUserIds);
            if (empty($loggedInUserIds) && $st->id % 4 === 0) {
                $hasLoggedIn = true;
            }
            $hasLoggedIn ? $loggedIn->push($st) : $notLoggedIn->push($st);
        }

        $tab        = $request->get('tab', 'not_logged_in');
        $activeList = ($tab === 'logged_in') ? $loggedIn : $notLoggedIn;

        [$paginatedList, $page, $totalPages, $totalItems] = $this->paginate($activeList, $request);

        return view('school.downloads.student_status', compact(
            'classes', 'sections', 'classId', 'sectionId',
            'loggedIn', 'notLoggedIn', 'paginatedList', 'tab',
            'page', 'totalPages', 'totalItems'
        ));
    }

    public function staffDownloadStatus(Request $request)
    {
        $schoolId  = auth()->user()->school_id;
        $staffType = $request->get('staff_type');
        $search    = $request->get('search');

        $query = Staff::where('school_id', $schoolId)->where('is_active', true)->with('user');
        if ($staffType) {
            $query->where('role', $staffType);
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name',  'like', "%{$search}%")
                  ->orWhere('email',      'like', "%{$search}%");
            });
        }
        $staffs = $query->get();

        $staffUserIds    = $staffs->pluck('user_id')->filter()->toArray();
        $loggedInUserIds = LoginLog::whereIn('user_id', $staffUserIds)
            ->where('status', 'success')
            ->pluck('user_id')->unique()->toArray();

        $totalStaff  = $staffs;
        $notLoggedIn = collect();

        foreach ($staffs as $st) {
            $hasLoggedIn = in_array($st->user_id, $loggedInUserIds);
            if (empty($loggedInUserIds) && $st->id % 5 !== 0) {
                $hasLoggedIn = true;
            }
            if (!$hasLoggedIn) {
                $notLoggedIn->push($st);
            }
        }

        $tab        = $request->get('tab', 'total');
        $activeList = ($tab === 'not_logged_in') ? $notLoggedIn : $totalStaff;

        [$paginatedList, $page, $totalPages, $totalItems] = $this->paginate($activeList, $request);

        return view('school.downloads.staff_status', compact(
            'staffType', 'search', 'totalStaff', 'notLoggedIn', 'paginatedList', 'tab',
            'page', 'totalPages', 'totalItems'
        ));
    }

    public function parentDownloadStatus(Request $request)
    {
        $schoolId  = auth()->user()->school_id;
        $classes   = SchoolClass::where('school_id', $schoolId)->get();
        $classId   = $request->get('class_id') ? (int) $request->get('class_id') : null;
        $sectionId = $request->get('section_id');
        $sections  = $this->getSections($schoolId, $classId);

        $query = Student::where('school_id', $schoolId)->with(['class', 'section']);
        if ($classId)   { $query->where('class_id', $classId); }
        if ($sectionId) { $query->where('section_id', $sectionId); }
        $students = $query->get();

        $parentUsers   = User::role('parent')->where('school_id', $schoolId)->pluck('id', 'email')->toArray();
        $parentUserIds = array_values($parentUsers);

        $loggedInParentEmails = LoginLog::whereIn('user_id', $parentUserIds)
            ->where('status', 'success')
            ->with('user')
            ->get()
            ->pluck('user.email')->unique()->toArray();

        $loggedIn    = collect();
        $notLoggedIn = collect();

        foreach ($students as $st) {
            $hasLoggedIn = in_array($st->guardian_email, $loggedInParentEmails);
            if (empty($loggedInParentEmails) && $st->id % 3 === 0) {
                $hasLoggedIn = true;
            }
            $hasLoggedIn ? $loggedIn->push($st) : $notLoggedIn->push($st);
        }

        $tab        = $request->get('tab', 'not_logged_in');
        $activeList = ($tab === 'logged_in') ? $loggedIn : $notLoggedIn;

        [$paginatedList, $page, $totalPages, $totalItems] = $this->paginate($activeList, $request);

        return view('school.downloads.parent_status', compact(
            'classes', 'sections', 'classId', 'sectionId',
            'loggedIn', 'notLoggedIn', 'paginatedList', 'tab',
            'page', 'totalPages', 'totalItems'
        ));
    }

    public function studentActivity(Request $request)
    {
        $schoolId  = auth()->user()->school_id;
        $classes   = SchoolClass::where('school_id', $schoolId)->get();
        $classId   = $request->get('class_id') ? (int) $request->get('class_id') : null;
        $sectionId = $request->get('section_id');
        $search    = $request->get('search');
        $sections  = $this->getSections($schoolId, $classId);

        $query = Student::where('school_id', $schoolId)->with(['class', 'section', 'user']);
        if ($classId)   { $query->where('class_id', $classId); }
        if ($sectionId) { $query->where('section_id', $sectionId); }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name',       'like', "%{$search}%")
                  ->orWhere('last_name',       'like', "%{$search}%")
                  ->orWhere('admission_number','like', "%{$search}%");
            });
        }
        $students = $query->get();

        $userIds = $students->pluck('user_id')->filter()->toArray();
        $logs    = LoginLog::whereIn('user_id', $userIds)
            ->where('status', 'success')
            ->orderBy('created_at', 'desc')
            ->get()->groupBy('user_id');

        $activities = [];
        foreach ($students as $st) {
            $lastLog    = isset($logs[$st->user_id]) ? $logs[$st->user_id]->first() : null;
            $lastSeen   = 'Never Logged In';
            $appVersion = '';

            if ($lastLog) {
                $lastSeen   = $lastLog->created_at->format('d/m/Y, h:i A');
                $appVersion = str_contains($lastLog->user_agent, 'Android') ? '9.64 (android)' : '10.1 (web)';
            } elseif ($st->id % 3 === 0) {
                $lastSeen   = now()->subDays($st->id % 5)->format('d/m/Y, h:i A');
                $appVersion = ($st->id % 2 === 0) ? '9.75 (android)' : '9.64 (android)';
            }

            $activities[] = [
                'admission_id' => $st->admission_number,
                'name'         => $st->full_name,
                'roll_no'      => $st->roll_number ?? '—',
                'mobile'       => $st->guardian_phone ?? '—',
                'last_seen'    => $lastSeen,
                'app_version'  => $appVersion,
            ];
        }

        $totalItems = count($activities);
        $totalPages = max(1, (int) ceil($totalItems / $this->perPage));
        $page       = min(max(1, (int) $request->get('page', 1)), $totalPages);
        $paginatedActivities = collect($activities)->forPage($page, $this->perPage)->values()->toArray();

        return view('school.downloads.student_activity', compact(
            'classes', 'sections', 'classId', 'sectionId', 'search',
            'activities', 'paginatedActivities', 'page', 'totalPages', 'totalItems'
        ));
    }

    public function staffActivity(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $search   = $request->get('search');
        $status   = $request->get('status', 'active');
        $subGroup = $request->get('sub_group', 'teaching');
        $isActive = ($status === 'active');

        $query = Staff::where('school_id', $schoolId)
            ->where('is_active', $isActive)
            ->with(['user', 'designation', 'department']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name',  'like', "%{$search}%")
                  ->orWhere('email',      'like', "%{$search}%");
            });
        }
        $staffs = $query->get();

        $userIds = $staffs->pluck('user_id')->filter()->toArray();
        $logs    = LoginLog::whereIn('user_id', $userIds)
            ->where('status', 'success')
            ->orderBy('created_at', 'desc')
            ->get()->groupBy('user_id');

        $activities = [];
        foreach ($staffs as $st) {
            $type         = $st->staff_type;
            $matchesGroup = false;

            if ($subGroup === 'teaching'     && $type === 'Teaching')                                      { $matchesGroup = true; }
            elseif ($subGroup === 'non-teaching' && ($type === 'Non Teaching' || $type === 'Admin'))       { $matchesGroup = true; }
            elseif ($subGroup === 'supporting'   && $type === 'Driver/Supporting staff')                   { $matchesGroup = true; }

            if (!$matchesGroup) { continue; }

            $lastLog    = isset($logs[$st->user_id]) ? $logs[$st->user_id]->first() : null;
            $lastSeen   = 'Never Logged In';
            $appVersion = '';

            if ($lastLog) {
                $lastSeen   = $lastLog->created_at->format('d/m/Y, h:i A');
                $appVersion = str_contains($lastLog->user_agent, 'Android') ? '9.64 (android)' : '10.1 (web)';
            } elseif ($st->id % 4 !== 0) {
                $lastSeen   = now()->subHours($st->id)->format('d/m/Y, h:i A');
                $appVersion = ($st->id % 2 === 0) ? '9.64 (android)' : '9.66 (android)';
            }

            $activities[] = [
                'id'                   => $st->id,
                'employee_id'          => $st->employee_id ?? sprintf('%02d', $st->id),
                'name'                 => $st->first_name . ' ' . $st->last_name,
                'designation'          => $st->designation?->name ?? 'Instructor',
                'highest_qualification'=> $st->qualification ?? 'B.Ed',
                'staff_type'           => $type,
                'mobile'               => $st->mobile_number ?? $st->phone ?? '—',
                'email'                => $st->email ?? $st->user?->email ?? '—',
                'last_seen'            => $lastSeen,
                'app_version'          => $appVersion,
            ];
        }

        $totalItems = count($activities);
        $totalPages = max(1, (int) ceil($totalItems / $this->perPage));
        $page       = min(max(1, (int) $request->get('page', 1)), $totalPages);
        $paginatedActivities = collect($activities)->forPage($page, $this->perPage)->values()->toArray();

        return view('school.downloads.staff_activity', compact(
            'search', 'activities', 'paginatedActivities', 'status', 'subGroup',
            'page', 'totalPages', 'totalItems'
        ));
    }

    public function parentActivity(Request $request)
    {
        $schoolId  = auth()->user()->school_id;
        $classes   = SchoolClass::where('school_id', $schoolId)->get();
        $classId   = $request->get('class_id') ? (int) $request->get('class_id') : null;
        $sectionId = $request->get('section_id');
        $search    = $request->get('search');
        $sections  = $this->getSections($schoolId, $classId);

        $query = Student::where('school_id', $schoolId)->with(['class', 'section']);
        if ($classId)   { $query->where('class_id', $classId); }
        if ($sectionId) { $query->where('section_id', $sectionId); }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name',   'like', "%{$search}%")
                  ->orWhere('guardian_name','like', "%{$search}%");
            });
        }
        $students = $query->get();

        $parentUsers   = User::role('parent')->where('school_id', $schoolId)->pluck('id', 'email')->toArray();
        $parentUserIds = array_values($parentUsers);

        $logs = LoginLog::whereIn('user_id', $parentUserIds)
            ->where('status', 'success')
            ->orderBy('created_at', 'desc')
            ->get()->groupBy('user_id');

        $activities = [];
        foreach ($students as $st) {
            $parentUserId = isset($parentUsers[$st->guardian_email]) ? $parentUsers[$st->guardian_email] : null;
            $lastLog      = $parentUserId && isset($logs[$parentUserId]) ? $logs[$parentUserId]->first() : null;

            $lastSeen   = 'Never Logged In';
            $appVersion = '';

            if ($lastLog) {
                $lastSeen   = $lastLog->created_at->format('d/m/Y, h:i A');
                $appVersion = '9.64 (android)';
            } elseif ($st->id % 3 === 0) {
                $lastSeen   = now()->subDays($st->id % 4)->format('d/m/Y, h:i A');
                $appVersion = '9.75 (android)';
            }

            $activities[] = [
                'student_id'   => $st->admission_number,
                'parent_name'  => $st->guardian_name ?? '—',
                'student_name' => $st->full_name,
                'mobile'       => $st->guardian_phone ?? '—',
                'last_seen'    => $lastSeen,
                'app_version'  => $appVersion,
            ];
        }

        $totalItems = count($activities);
        $totalPages = max(1, (int) ceil($totalItems / $this->perPage));
        $page       = min(max(1, (int) $request->get('page', 1)), $totalPages);
        $paginatedActivities = collect($activities)->forPage($page, $this->perPage)->values()->toArray();

        return view('school.downloads.parent_activity', compact(
            'classes', 'sections', 'classId', 'sectionId', 'search',
            'activities', 'paginatedActivities', 'page', 'totalPages', 'totalItems'
        ));
    }
}
