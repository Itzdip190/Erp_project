<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /**
     * Show or redirect to the forgot password form / modal.
     */
    public function showLinkRequestForm(Request $request)
    {
        return redirect()->route('login', ['forgot' => 1]);
    }

    /**
     * Handle forgot password submission (Email / Mobile / Admission ID).
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'login_input' => 'required|string|max:191',
        ], [
            'login_input.required' => 'Please enter your Email, Mobile Number, or Admission ID.',
        ]);

        $input = trim($request->input('login_input'));
        $cleanPhone = preg_replace('/[^0-9]/', '', $input);

        // 1. Try finding user directly by email or phone
        $user = User::withoutGlobalScopes()
            ->where(function ($q) use ($input, $cleanPhone) {
                $q->where('email', $input);
                if (!empty($cleanPhone) && strlen($cleanPhone) >= 7) {
                    $q->orWhere('phone', $input)
                      ->orWhere('phone', $cleanPhone);
                }
            })->first();

        // 2. If not found in User, search Student table
        if (!$user) {
            $student = Student::withoutGlobalScopes()
                ->where(function ($q) use ($input, $cleanPhone) {
                    $q->where('admission_number', $input)
                      ->orWhereRaw('LOWER(admission_number) = ?', [strtolower($input)])
                      ->orWhere('email', $input)
                      ->orWhere('guardian_email', $input);
                    if (!empty($cleanPhone) && strlen($cleanPhone) >= 7) {
                        $q->orWhere('phone', $input)
                          ->orWhere('phone', $cleanPhone)
                          ->orWhere('father_phone', $input)
                          ->orWhere('father_phone', $cleanPhone)
                          ->orWhere('guardian_phone', $input)
                          ->orWhere('guardian_phone', $cleanPhone);
                    }
                })->first();

            if ($student && $student->user_id) {
                $user = User::withoutGlobalScopes()->find($student->user_id);
            }
        }

        if (!$user) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No account found matching this Email, Phone, or Admission ID. Please verify your details or contact your school administrator.',
                ], 422);
            }
            return back()->withErrors(['login_input' => 'No account found matching this identifier.'])->withInput();
        }

        // Generate token
        $token = Str::random(64);
        $emailIdentifier = !empty($user->email) ? $user->email : ($user->phone ?? 'user_' . $user->id . '@educorerp.com');

        // Store or update in password_reset_tokens
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $emailIdentifier],
            [
                'email' => $emailIdentifier,
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        $resetUrl = route('password.reset', ['token' => $token, 'email' => $emailIdentifier]);

        // Attempt to send email if valid email address is present
        $emailSent = false;
        if (!empty($user->email) && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
            try {
                $userName = $user->name;
                Mail::raw(
                    "Hello {$userName},\n\nYou requested a password reset for your EduCore ERP account.\nClick the link below to reset your password:\n\n" . $resetUrl . "\n\nThis link will expire in 60 minutes.\nIf you did not request a password reset, please ignore this email.",
                    function ($message) use ($user) {
                        $message->to($user->email)->subject('EduCore ERP - Password Reset Link');
                    }
                );
                $emailSent = true;
            } catch (\Throwable $e) {
                Log::warning("Password reset email sending failed: " . $e->getMessage());
            }
        }

        $obfuscatedEmail = !empty($user->email) && filter_var($user->email, FILTER_VALIDATE_EMAIL)
            ? preg_replace('/(?<=^.).(?=.*@)/', '*', $user->email)
            : null;

        $msg = !empty($obfuscatedEmail)
            ? "A password reset link has been sent to your registered email ({$obfuscatedEmail})."
            : "A password reset link has been sent to your registered email address.";

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'email_sent' => $emailSent,
            ]);
        }

        return back()->with('status', $msg);
    }

    /**
     * Show the password reset form with token.
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.passwords.reset')->with([
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /**
     * Reset the user's password.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.min' => 'Password must be at least 8 characters long.',
            'password.confirmed' => 'Password confirmation does not match.',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record) {
            return back()->withErrors(['email' => 'Invalid or expired password reset request.'])->withInput();
        }

        // Check if token matches
        if (!Hash::check($request->token, $record->token) && $request->token !== $record->token) {
            return back()->withErrors(['email' => 'Invalid reset token. Please request a new link.'])->withInput();
        }

        // Check 60 minute expiration
        if (now()->diffInMinutes($record->created_at) > 60) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['email' => 'This password reset link has expired. Please request a new one.'])->withInput();
        }

        // Find user and update password
        $user = User::withoutGlobalScopes()
            ->where('email', $request->email)
            ->orWhere('phone', $request->email)
            ->first();

        if (!$user && str_starts_with($request->email, 'user_')) {
            $parts = explode('_', str_replace('@educorerp.com', '', $request->email));
            if (isset($parts[1]) && is_numeric($parts[1])) {
                $user = User::withoutGlobalScopes()->find($parts[1]);
            }
        }

        if (!$user) {
            return back()->withErrors(['email' => 'User account not found.'])->withInput();
        }

        $user->password = Hash::make($request->password);
        $user->must_change_password = false;
        $user->save();

        // Delete used token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('status', 'Your password has been reset successfully! You can now log in with your new password.');
    }
}
