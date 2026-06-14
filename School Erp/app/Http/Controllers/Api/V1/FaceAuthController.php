<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\FaceLoginRequest;
use App\Models\FaceVector;
use App\Models\FcmDeviceToken;
use App\Models\LoginLog;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FaceAuthController extends Controller
{
    public function login(FaceLoginRequest $request)
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

        // Get all face vectors for this school
        $faceVectors = FaceVector::where('school_id', $school->id)->get();

        $minDistance = null;
        $matchedVector = null;
        $targetEncoding = $data['face_encoding'];

        // Euclidean distance comparison
        foreach ($faceVectors as $vector) {
            $storedEncoding = $vector->encoding;
            
            if (!is_array($storedEncoding) || count($storedEncoding) !== 128) {
                continue;
            }

            $sum = 0;
            for ($i = 0; $i < 128; $i++) {
                $diff = (float)$targetEncoding[$i] - (float)$storedEncoding[$i];
                $sum += $diff * $diff;
            }
            $distance = sqrt($sum);

            if ($minDistance === null || $distance < $minDistance) {
                $minDistance = $distance;
                $matchedVector = $vector;
            }
        }

        // Threshold check (0.45 is standard for deep learning face-embeddings)
        if ($minDistance === null || $minDistance >= 0.45) {
            return response()->json([
                'success' => false,
                'message' => 'Face match not found or verification failed.',
            ], 401);
        }

        $user = User::findOrFail($matchedVector->user_id);

        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'User account is deactivated.',
            ], 403);
        }

        // Revoke token for same device
        $user->tokens()->where('name', $data['device_name'])->delete();
        $token = $user->createToken($data['device_name'])->plainTextToken;

        // Register FCM Token
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

        // Write to log
        LoginLog::create([
            'user_id' => $user->id,
            'email_attempted' => $user->email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent() . " [Face ID Distance: " . round($minDistance, 4) . "]",
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

    public function enroll(Request $request)
    {
        $request->validate([
            'face_encoding' => 'required|array|size:128',
            'face_encoding.*' => 'required|numeric',
            'photo' => 'required|image|max:4096',
        ]);

        $user = auth()->user();
        $schoolId = $user->school_id;

        $path = $request->file('photo')->store('students/faces', config('filesystems.default'));

        // Save/update FaceVector
        FaceVector::updateOrCreate(
            [
                'school_id' => $schoolId,
                'user_id' => $user->id,
            ],
            [
                'encoding' => $request->face_encoding,
                'photo_path' => $path,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Face registered successfully.',
        ]);
    }
}
