<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.login');
    }

    /**
     * Handle authentication attempt.
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
            'login_type' => ['required', 'in:email_mobile,admission_id'],
        ]);

        $username = $request->input('username');
        $password = $request->input('password');
        $loginType = $request->input('login_type');
        $remember = $request->has('remember');

        $credentials = [];

        if ($loginType === 'admission_id') {
            $credentials = ['admission_id' => $username, 'password' => $password];
        } else {
            // Check if input is email or mobile number
            if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
                $credentials = ['email' => $username, 'password' => $password];
            } else {
                $credentials = ['mobile' => $username, 'password' => $password];
            }
        }

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();
            return $this->redirectBasedOnRole($user);
        }

        throw ValidationException::withMessages([
            'username' => [__('auth.failed')],
        ]);
    }

    /**
     * Redirect users to their respective dashboards based on their role.
     */
    protected function redirectBasedOnRole($user)
    {
        if ($user->hasRole('superadmin') || $user->role === 'superadmin') {
            return redirect()->intended(route('superadmin.dashboard'));
        } elseif ($user->hasRole('school_admin') || $user->role === 'school_admin' || $user->hasRole('teacher') || $user->role === 'teacher') {
            return redirect()->intended(route('school.dashboard'));
        } elseif ($user->hasRole('parent') || $user->role === 'parent') {
            return redirect()->intended(route('parent.dashboard'));
        } elseif ($user->hasRole('student') || $user->role === 'student') {
            return redirect()->intended(route('student.dashboard'));
        }

        Auth::logout();
        return redirect()->route('login')->withErrors(['username' => 'Unauthorized access. Unknown role.']);
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
