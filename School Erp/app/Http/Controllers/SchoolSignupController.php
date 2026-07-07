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
        $states = \App\Models\School::getStatesList();
        return view('auth.school-signup', compact('plans', 'states'));
    }

    public function getNextCode(Request $request)
    {
        $state = $request->query('state');
        if (empty($state) || strlen($state) !== 2) {
            return response()->json(['code' => '']);
        }
        
        $code = \App\Models\School::generateNextCode($state);
        return response()->json(['code' => $code]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'state'          => 'required|string|size:2',
            'school_type'    => 'required|string|in:CBSE,CBSE PATTERN,ICSE,STATE BOARD',
            'director_name'  => 'required|string|max:255',
            'email'          => 'required|email|max:255',
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
            'academic_session_name'       => 'required|string|max:100',
            'academic_session_start_date' => 'required|date',
            'academic_session_end_date'   => 'required|date|after_or_equal:academic_session_start_date',
        ]);

        $generatedCode = \App\Models\School::generateNextCode($request->state);

        SchoolRequest::create([
            'name'           => $request->name,
            'code'           => $generatedCode,
            'phone'          => $request->phone,
            'address'        => $request->address,
            'state'          => strtoupper($request->state),
            'school_type'    => $request->school_type,
            'director_name'  => $request->director_name,
            'email'          => $request->email,
            'admin_name'     => $request->admin_name,
            'admin_email'    => $request->admin_email,
            'admin_password' => Crypt::encryptString($request->admin_password),
            'plan_id'        => $request->plan_id,
            'status'         => 'pending',
            'academic_session_name'       => $request->academic_session_name,
            'academic_session_start_date' => $request->academic_session_start_date,
            'academic_session_end_date'   => $request->academic_session_end_date,
        ]);

        return redirect()->route('school.signup')
            ->with('success', 'Thank you! Your school registration request has been submitted. The super administrator will review and approve it shortly.');
    }
}
