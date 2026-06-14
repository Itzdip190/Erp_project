<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\OtpSendRequest;
use App\Http\Requests\Api\Auth\OtpVerifyRequest;
use App\Models\FcmDeviceToken;
use App\Models\LoginLog;
use App\Models\School;
use App\Models\User;
use App\Services\OtpLoginService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class OtpLoginController extends Controller
{
    public function __construct(protected OtpLoginService $otpService)
    {
    }

    public function send(OtpSendRequest $request)
    {
        $data = $request->validated();

        $school = School::where('code', $data['school_code'])->first();
        if (!$school) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid school code.',
            ], 404);
        }

        $user = User::where('phone', $data['phone'])
            ->where('school_id', $school->id)
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found with this phone number.',
            ], 404);
        }

        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'User account is inactive.',
            ], 403);
        }

        // Generate OTP
        $otpRecord = $this->otpService->generateOtp($user->id, $school->id, $data['phone']);

        // Send OTP (Mocked in local/test, real in production)
        $this->otpService->sendOtp($user->phone, $otpRecord->otp, $school->sms_config ?? []);

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully.',
        ]);
    }

    public function verify(OtpVerifyRequest $request)
    {
        $data = $request->validated();

        $school = School::where('code', $data['school_code'])->first();
        if (!$school) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid school code.',
            ], 404);
        }

        // Verify OTP using the service
        $otpRecord = $this->otpService->verifyOtp($school->id, $data['phone'], $data['otp']);

        if (!$otpRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP.',
            ], 401);
        }

        $user = User::findOrFail($otpRecord->user_id);

        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'User account is inactive.',
            ], 403);
        }

        // Issue Sanctum token
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
            'email_attempted' => $user->email,
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
                'permissions' => $user->getAllPermissions()->pluck('name'),
            ]
        ]);
    }
}
