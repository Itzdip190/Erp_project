<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\LoginLog;
use App\Models\User;
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
    public function login(LoginRequest $request)
    {
        $loginInput = $request->email;
        $password = $request->password;
        $remember = $request->has('remember');

        $email = $loginInput;

        if (filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
            // Check if it's a student's personal email
            $student = \App\Models\Student::where('email', $loginInput)->first();
            if ($student && $student->user) {
                $email = $student->user->email;
            } else {
                // Check if it's a guardian's email
                $studentWithGuardian = \App\Models\Student::where('guardian_email', $loginInput)->first();
                if ($studentWithGuardian) {
                    $parentUser = User::where('email', $loginInput)->first();
                    if ($parentUser) {
                        $email = $parentUser->email;
                    }
                }
            }
        } else {
            // Check student admission number
            $student = \App\Models\Student::where('admission_number', $loginInput)->first();
            if ($student && $student->user) {
                $email = $student->user->email;
            } else {
                // Check user phone number
                $userByPhone = User::where('phone', $loginInput)->first();
                if ($userByPhone) {
                    $email = $userByPhone->email;
                }
            }
        }

        $credentials = ['email' => $email, 'password' => $password];

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();
            
            // Update last login timestamp
            $user->update(['last_login_at' => now()]);

            // Set session school_code for the resolved school
            if ($user->school_id && $user->school) {
                session(['school_code' => $user->school->code]);
            }

            // Log successful attempt
            LoginLog::create([
                'user_id' => $user->id,
                'email_attempted' => $loginInput,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => 'success',
            ]);

            return $this->redirectBasedOnRole($user);
        }

        // Log failed attempt
        $failedUser = User::where('email', $email)->first();
        LoginLog::create([
            'user_id' => $failedUser?->id,
            'email_attempted' => $loginInput,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => 'failed',
        ]);

        throw ValidationException::withMessages([
            'email' => [__('auth.failed')],
        ]);
    }

    /**
     * Redirect users to their respective dashboards based on their role.
     */
    protected function redirectBasedOnRole($user)
    {
        if ($user->hasRole('superadmin')) {
            return redirect()->intended('/superadmin/dashboard');
        } elseif ($user->hasRole('school_admin')) {
            return redirect()->intended('/school/dashboard');
        } elseif ($user->hasRole('teacher') || $user->hasRole('staff') || $user->hasRole('accountant') || $user->role === 'teacher') {
            return redirect()->intended('/teacher/dashboard');
        } elseif ($user->hasRole('parent') || $user->hasRole('student')) {
            return redirect()->intended('/parent/dashboard');
        }

        Auth::logout();
        return redirect()->route('login')->withErrors(['email' => 'Unauthorized access. Unknown role.']);
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

