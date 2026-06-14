<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\MobileLoginRequest;
use App\Models\FcmDeviceToken;
use App\Models\LoginLog;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(MobileLoginRequest $request)
    {
        $data = $request->validated();

        // 1. Resolve school by code
        $school = School::where('code', $data['school_code'])->first();
        if (!$school) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid school code.',
            ], 404);
        }

        // 2. Check school status
        if ($school->status !== 'active' && $school->activeSubscription() === null) {
            return response()->json([
                'success' => false,
                'message' => 'School subscription is inactive or suspended.',
            ], 403);
        }

        // 3. Resolve user in school tenant scope
        $user = User::where('email', $data['email'])
            ->where('school_id', $school->id)
            ->first();

        // 4. Validate credentials
        if (!$user || !Hash::check($data['password'], $user->password)) {
            // Log failed attempt
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

        // 5. Check user status
        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Account is deactivated.',
            ], 403);
        }

        // 6. Revoke old tokens for this device name
        $user->tokens()->where('name', $data['device_name'])->delete();

        // 7. Create new personal access token
        $token = $user->createToken($data['device_name'])->plainTextToken;

        // 8. Register FCM device token if supplied
        if (!empty($data['fcm_token'])) {
            FcmDeviceToken::updateOrCreate(
                [
                    'school_id' => $school->id,
                    'user_id' => $user->id,
                    'device_name' => $data['device_name'],
                ],
                [
                    'token' => $data['fcm_token'],
                    'platform' => $request->header('X-Platform', 'android'), // default android
                ]
            );
        }

        // 9. Write successful log entry
        LoginLog::create([
            'user_id' => $user->id,
            'email_attempted' => $data['email'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => 'success',
        ]);

        // 10. Return authentication response
        return response()->json([
            'success' => true,
            'data' => [
                'token' => $token,
                'user' => $user,
                'school' => $school,
                'permissions' => $user->getAllPermissions()->pluck('name'),
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user) {
            // Delete current token
            $user->currentAccessToken()->delete();

            // Clear device token
            $deviceName = $request->header('X-Device-Name');
            if ($deviceName) {
                FcmDeviceToken::where('user_id', $user->id)
                    ->where('device_name', $deviceName)
                    ->delete();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.'
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user();
        $user->load('school');

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'school' => $user->school,
                'role' => $user->roles->first()?->name,
                'permissions' => $user->getAllPermissions()->pluck('name'),
            ]
        ]);
    }
}
