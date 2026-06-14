<?php

namespace App\Http\Controllers\Api\V1\Driver;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\MobileLoginRequest;
use App\Models\FcmDeviceToken;
use App\Models\LoginLog;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DriverAuthController extends Controller
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

        // Validate driver role
        if (!$user->hasRole('driver')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Must be a driver account.',
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

        return response()->json([
            'success' => true,
            'data' => [
                'token' => $token,
                'user' => $user,
                'school' => $school,
                'vehicle' => null, // Hardcoded placeholder until Transport module (Phase 2+) is built
                'permissions' => $user->getAllPermissions()->pluck('name'),
            ]
        ]);
    }
}
