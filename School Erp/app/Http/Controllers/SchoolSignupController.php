<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\SchoolRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class SchoolSignupController extends Controller
{
    public function showRegistrationForm()
    {
        $plans = Plan::all();
        return view('auth.school-signup', compact('plans'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'code'           => [
                'required',
                'string',
                'max:50',
                'alpha_dash',
                'unique:schools,code',
                function ($attribute, $value, $fail) {
                    $codeUpper = strtoupper($value);
                    if (SchoolRequest::whereRaw('UPPER(code) = ?', [$codeUpper])->whereIn('status', ['pending', 'approved'])->exists()) {
                        $fail('This school code has already been requested or registered.');
                    }
                }
            ],
            'phone'          => 'nullable|string|max:20',
            'address'        => 'nullable|string|max:500',
            'admin_name'     => 'required|string|max:100',
            'admin_email'    => [
                'required',
                'email',
                'unique:users,email',
                function ($attribute, $value, $fail) {
                    if (SchoolRequest::where('admin_email', $value)->whereIn('status', ['pending', 'approved'])->exists()) {
                        $fail('This administrator email has already been used for a pending or approved request.');
                    }
                }
            ],
            'admin_password' => 'required|string|min:8|confirmed',
            'plan_id'        => 'nullable|exists:plans,id',
        ]);

        SchoolRequest::create([
            'name'           => $request->name,
            'code'           => strtoupper($request->code),
            'phone'          => $request->phone,
            'address'        => $request->address,
            'admin_name'     => $request->admin_name,
            'admin_email'    => $request->admin_email,
            'admin_password' => Crypt::encryptString($request->admin_password),
            'plan_id'        => $request->plan_id,
            'status'         => 'pending',
        ]);

        return redirect()->route('school.signup')
            ->with('success', 'Thank you! Your school registration request has been submitted. The super administrator will review and approve it shortly.');
    }
}
