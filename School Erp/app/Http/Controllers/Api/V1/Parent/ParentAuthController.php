<?php

namespace App\Http\Controllers\Api\V1\Parent;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\MobileLoginRequest;
use App\Models\FcmDeviceToken;
use App\Models\LoginLog;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ParentAuthController extends Controller
{
    public function login(MobileLoginRequest $request)
    {
        $data = $request->validated();

        $school = School::where('code', $data['school_code'])->first();
        if (!$school) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid school code.',
            ], 404);
        }

        if ($school->status !== 'active' && $school->activeSubscription() === null) {
            return response()->json([
                'success' => false,
                'message' => 'School subscription is inactive or suspended.',
            ], 403);
        }

        $user = User::where('email', $data['email'])
            ->where('school_id', $school->id)
            ->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            LoginLog::create([
                'user_id' => $user?->id,
                'email_attempted' => $data['email'],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => 'failed',
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password.',
            ], 401);
        }

        // Enforce parent/student role check
        if (!$user->hasRole('parent') && !$user->hasRole('student')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Must be a parent or student account.',
            ], 403);
        }

        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Account is deactivated.',
            ], 403);
        }

        $user->tokens()->where('name', $data['device_name'])->delete();
        $token = $user->createToken($data['device_name'])->plainTextToken;

        if (!empty($data['fcm_token'])) {
            FcmDeviceToken::updateOrCreate(
                [
                    'school_id' => $school->id,
                    'user_id' => $user->id,
                    'device_name' => $data['device_name'],
                ],
                [
                    'token' => $data['fcm_token'],
                    'platform' => $request->header('X-Platform', 'android'),
                ]
            );
        }

        LoginLog::create([
            'user_id' => $user->id,
            'email_attempted' => $data['email'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => 'success',
        ]);

        // Fetch children
        $children = collect();
        if ($user->hasRole('parent')) {
            $children = Student::where('guardian_email', $user->email)
                ->with(['class', 'section'])
                ->get();
        } elseif ($user->hasRole('student')) {
            $children = Student::where('user_id', $user->id)
                ->with(['class', 'section'])
                ->get();
        }

        return response()->json([
            'success' => true,
            'data' => [
                'token' => $token,
                'user' => $user,
                'school' => $school,
                'children' => $children,
                'permissions' => $user->getAllPermissions()->pluck('name'),
            ]
        ]);
    }
}
