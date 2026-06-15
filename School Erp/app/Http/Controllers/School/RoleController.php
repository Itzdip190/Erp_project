<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Show roles list and user counts.
     */
    public function index()
    {
        $schoolId = auth()->user()->school_id;

        // Fetch Spatie roles with the number of users in this school
        $roles = Role::whereIn('name', ['school_admin', 'teacher', 'accountant', 'parent', 'student', 'driver'])->get();

        $rolesData = [];
        foreach ($roles as $role) {
            $userCount = User::where('school_id', $schoolId)
                ->role($role->name)
                ->count();

            $rolesData[] = [
                'name'        => $role->name,
                'display_name'=> ucfirst(str_replace('_', ' ', $role->name)),
                'user_count'  => $userCount,
                'guard'       => $role->guard_name,
                'description' => $this->getRoleDescription($role->name),
            ];
        }

        return view('school.roles.index', compact('rolesData'));
    }

    /**
     * Show staff access control list.
     */
    public function staffAccess(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $search = $request->get('search');

        $query = User::where('school_id', $schoolId)
            ->whereHas('roles', function ($q) {
                $q->whereIn('name', ['school_admin', 'teacher', 'accountant']);
            });

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->with('roles')->paginate(10);
        $roles = Role::whereIn('name', ['school_admin', 'teacher', 'accountant'])->get();

        return view('school.roles.staff_access', compact('users', 'roles', 'search'));
    }

    /**
     * Update access control / toggle status for a staff member.
     */
    public function updateStaffAccess(Request $request, User $user)
    {
        $request->validate([
            'role'      => 'required|exists:roles,name',
            'is_active' => 'required|boolean',
        ]);

        // Verify user belongs to same school
        if ($user->school_id !== auth()->user()->school_id) {
            abort(403, 'Unauthorized.');
        }

        // Toggle user status
        $user->is_active = $request->is_active;
        $user->save();

        // Sync Spatie role
        $user->syncRoles([$request->role]);

        return back()->with('success', "Access settings for {$user->name} updated successfully!");
    }

    private function getRoleDescription($roleName): string
    {
        return match($roleName) {
            'school_admin' => 'Full administrative access to manage students, staff, and configurations.',
            'teacher'      => 'Academic access to manage classrooms, mark student attendance and generate cards.',
            'accountant'   => 'Financial and read-only student access to manage fee collections and records.',
            'parent'       => 'Portal access to view student progress, attendance, and download certificates.',
            'student'      => 'Portal access to view dashboard, class documents and attendance logs.',
            'driver'       => 'Mobile access to coordinate transport routes and tracking.',
            default        => 'Standard portal access role.',
        };
    }
}
