<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ChangePasswordController extends Controller
{
    /**
     * Display the mandatory change password form.
     */
    public function showChangePasswordForm()
    {
        return view('auth.change_password');
    }

    /**
     * Handle updating user's password from temporary password.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[^\w\s]/',
                'confirmed',
                'different:current_password',
            ],
        ], [
            'password.min' => 'The new password must be at least 8 characters long.',
            'password.regex' => 'The new password must contain at least one uppercase letter (A-Z), one lowercase letter (a-z), one digit (0-9), and one special character (e.g. @$!%*#?&).',
            'password.confirmed' => 'The new password confirmation does not match.',
            'password.different' => 'The new password must be different from your current temporary password.',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The provided current password is incorrect.'],
            ]);
        }

        $user->password = Hash::make($request->password);
        $user->must_change_password = false;
        $user->last_password_reset_at = now();
        $user->save();

        $redirectUrl = $this->getDashboardRouteForUser($user);

        return redirect($redirectUrl)->with('success', 'Your password has been updated successfully! You can now access your account.');
    }

    /**
     * Helper to resolve dashboard route based on user role.
     */
    protected function getDashboardRouteForUser($user): string
    {
        if ($user->hasRole('superadmin')) {
            return '/superadmin/dashboard';
        }
        if ($user->hasRole('school_admin') || $user->hasRole('admin') || in_array($user->role, ['admin', 'school_admin'], true)) {
            return '/school/dashboard';
        }
        if ($user->hasRole('teacher') || $user->hasRole('staff') || $user->hasRole('accountant') || in_array($user->role, ['teacher', 'staff'], true)) {
            return '/teacher/dashboard';
        }
        if ($user->hasRole('student') || $user->role === 'student') {
            return '/student/dashboard';
        }
        if ($user->hasRole('parent') || $user->role === 'parent') {
            return '/parent/dashboard';
        }

        return '/school/dashboard';
    }
}
