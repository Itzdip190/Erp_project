<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;

class CheckActiveStudent
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if ($user && ($user->hasRole('parent') || $user->hasRole('student') || $user->role === 'parent' || $user->role === 'student')) {
            // Find student
            $student = Student::where('school_id', $user->school_id)
                ->where(function ($q) use ($user) {
                    $q->where('guardian_email', $user->email)
                      ->orWhere('user_id', $user->id);
                })
                ->first();

            if ($student && !$student->is_active) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Student account is inactive. Access denied.'
                    ], 403);
                }

                if ($user->hasRole('student') || $user->role === 'student') {
                    Auth::logout();
                    return redirect()->route('login')->withErrors([
                        'email' => 'Your student account is inactive. You cannot access school data.'
                    ]);
                }

                // For parent, return parent.inactive view
                return response()->view('parent.inactive', compact('student'));
            }
        }

        return $next($request);
    }
}
