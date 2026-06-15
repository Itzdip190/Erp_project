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

        // If it's not a valid email, check if it's an Admission Number or a Phone Number
        if (!filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
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
        } elseif ($user->hasRole('school_admin') || $user->hasRole('teacher') || $user->hasRole('accountant')) {
            return redirect()->intended('/school/dashboard');
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

