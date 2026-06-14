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
        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();
            
            // Update last login timestamp
            $user->update(['last_login_at' => now()]);

            // Log successful attempt
            LoginLog::create([
                'user_id' => $user->id,
                'email_attempted' => $request->email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => 'success',
            ]);

            return $this->redirectBasedOnRole($user);
        }

        // Log failed attempt
        $failedUser = User::where('email', $request->email)->first();
        LoginLog::create([
            'user_id' => $failedUser?->id,
            'email_attempted' => $request->email,
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

