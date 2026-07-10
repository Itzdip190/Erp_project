<?php

namespace App\Http\Controllers;

use App\Mail\SchoolRequestReceived;
use App\Models\Plan;
use App\Models\SchoolRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SchoolSignupController extends Controller
{
    public function showRegistrationForm()
    {
        $plans  = Plan::all();
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
            'name'          => 'required|string|max:255',
            'director_name' => 'required|string|max:255',
            'school_type'   => 'required|string|in:CBSE,CBSE PATTERN,ICSE,STATE BOARD',
            'email'       => [
                'required',
                'email',
                'max:255',
                function ($attribute, $value, $fail) {
                    if (SchoolRequest::where('email', $value)
                        ->whereIn('status', ['pending', 'approved'])
                        ->exists()) {
                        $fail('A request with this email is already pending or approved.');
                    }
                }
            ],
            'phone'       => 'nullable|string|max:20',
        ]);

        // Auto-generate a placeholder code (state-independent for now; finalized on approval)
        $generatedCode = 'REQ-' . strtoupper(substr(preg_replace('/\s+/', '', $request->name), 0, 4))
                         . '-' . strtoupper(\Illuminate\Support\Str::random(4));

        $schoolRequest = SchoolRequest::create([
            'name'          => $request->name,
            'code'          => $generatedCode,
            'phone'         => $request->phone,
            'school_type'   => $request->school_type,
            'director_name' => $request->director_name,
            'email'         => $request->email,
            'admin_name'    => $request->director_name,
            'admin_email'   => $request->email,
            'admin_password'=> \Illuminate\Support\Facades\Crypt::encryptString(\Illuminate\Support\Str::random(16)),
            'status'        => 'pending',
        ]);

        // Send thank-you confirmation email (silently fail if mail not configured)
        try {
            Mail::to($request->email)->send(new SchoolRequestReceived($schoolRequest));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('SchoolRequest confirmation email failed: ' . $e->getMessage());
        }

        return redirect()->route('school.signup')
            ->with('success', 'Thank you! Your school registration request has been submitted. Our agent will reach out to you shortly.');
    }
}
