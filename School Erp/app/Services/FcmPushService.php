<?php

namespace App\Services;

use App\Models\FcmDeviceToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmPushService
{
    /**
     * Send push notification to specific device tokens or school roles.
     * Supports both Firebase Legacy Server Key (AAAA...) and Firebase V1 Service Account JSON.
     */
    public static function sendToTokens(int $schoolId, array $tokens, string $title, string $message, array $extra = []): array
    {
        $tokens = array_filter(array_unique($tokens));
        if (empty($tokens)) {
            return [
                'success_count' => 0,
                'failure_count' => 0,
                'message' => 'No registered device tokens found for target audience.'
            ];
        }

        $fcmConfig = SettingService::get('fcm_server_key', config('services.fcm.key', env('FCM_SERVER_KEY')), $schoolId);

        if (empty($fcmConfig)) {
            Log::info("FCM Push prepared for School #{$schoolId} (" . count($tokens) . " tokens), but Firebase Key/JSON is not configured yet.");
            return [
                'success_count' => count($tokens),
                'failure_count' => 0,
                'message' => 'Notification recorded in app. Configure Firebase FCM Key in settings to deliver system lockscreen sounds.'
            ];
        }

        $trimmedConfig = trim($fcmConfig);

        // Check if config is a Service Account JSON (FCM V1 API)
        if (str_starts_with($trimmedConfig, '{') && str_contains($trimmedConfig, 'private_key')) {
            return self::sendViaFcmV1($schoolId, $tokens, $title, $message, $trimmedConfig, $extra);
        }

        // Otherwise, send via Standard/Legacy FCM Gateway (AAAA...)
        return self::sendViaFcmLegacy($schoolId, $tokens, $title, $message, $trimmedConfig, $extra);
    }

    /**
     * Dispatch via Legacy FCM HTTP Gateway (Key: AAAA...)
     */
    protected static function sendViaFcmLegacy(int $schoolId, array $tokens, string $title, string $message, string $serverKey, array $extra): array
    {
        $sound = $extra['sound'] ?? 'default';
        $priority = $extra['priority'] ?? 'high';
        $actionUrl = $extra['action_url'] ?? '/';
        $category = $extra['category'] ?? 'General Notice';

        $payload = [
            'registration_ids' => array_values($tokens),
            'notification' => [
                'title' => $title,
                'body' => $message,
                'sound' => $sound,
                'badge' => 1,
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                'android_channel_id' => 'school_erp_high_importance',
            ],
            'data' => [
                'title' => $title,
                'body' => $message,
                'action_url' => $actionUrl,
                'category' => $category,
                'priority' => $priority,
                'sound' => $sound,
                'timestamp' => now()->toISOString(),
            ],
            'priority' => 'high',
            'android' => [
                'priority' => 'high',
                'notification' => [
                    'sound' => 'default',
                    'default_sound' => true,
                    'default_vibrate_timings' => true,
                    'channel_id' => 'school_erp_high_importance',
                    'notification_priority' => 'PRIORITY_MAX',
                    'visibility' => 'PUBLIC',
                ],
            ],
            'apns' => [
                'headers' => [
                    'apns-priority' => '10',
                ],
                'payload' => [
                    'aps' => [
                        'alert' => [
                            'title' => $title,
                            'body' => $message,
                        ],
                        'sound' => 'default',
                        'badge' => 1,
                    ],
                ],
            ],
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $serverKey,
                'Content-Type'  => 'application/json',
            ])->timeout(10)->post('https://fcm.googleapis.com/fcm/send', $payload);

            if ($response->successful()) {
                $result = $response->json();
                $successCount = $result['success'] ?? count($tokens);
                $failureCount = $result['failure'] ?? 0;

                // Auto-cleanup dead/expired device tokens
                if (!empty($result['results'])) {
                    $tokensToPrune = [];
                    foreach ($result['results'] as $idx => $res) {
                        if (isset($res['error']) && in_array($res['error'], ['NotRegistered', 'InvalidRegistration', 'MismatchSenderId'])) {
                            if (isset($tokens[$idx])) {
                                $tokensToPrune[] = $tokens[$idx];
                            }
                        }
                    }
                    if (!empty($tokensToPrune)) {
                        FcmDeviceToken::whereIn('token', $tokensToPrune)->delete();
                    }
                }

                return [
                    'success_count' => $successCount,
                    'failure_count' => $failureCount,
                    'message' => "Successfully delivered push notification to {$successCount} mobile devices with sound."
                ];
            } else {
                Log::error("FCM Push HTTP Error: " . $response->body());
                return [
                    'success_count' => 0,
                    'failure_count' => count($tokens),
                    'message' => 'FCM Server response: ' . ($response->json('error') ?? $response->status())
                ];
            }
        } catch (\Throwable $e) {
            Log::error("FCM Push Exception: " . $e->getMessage());
            return [
                'success_count' => 0,
                'failure_count' => count($tokens),
                'message' => 'Error connecting to FCM: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Dispatch via Modern Firebase FCM HTTP v1 API using Service Account JSON
     */
    protected static function sendViaFcmV1(int $schoolId, array $tokens, string $title, string $message, string $jsonConfig, array $extra): array
    {
        $serviceAccount = json_decode($jsonConfig, true);
        if (!$serviceAccount || empty($serviceAccount['project_id']) || empty($serviceAccount['private_key']) || empty($serviceAccount['client_email'])) {
            return [
                'success_count' => 0,
                'failure_count' => count($tokens),
                'message' => 'Invalid Firebase Service Account JSON format.'
            ];
        }

        $projectId = $serviceAccount['project_id'];
        $accessToken = self::getGoogleOAuth2Token($serviceAccount);

        if (!$accessToken) {
            return [
                'success_count' => 0,
                'failure_count' => count($tokens),
                'message' => 'Failed to generate Google OAuth2 token from Service Account key.'
            ];
        }

        $successCount = 0;
        $failureCount = 0;
        $sound = $extra['sound'] ?? 'default';
        $actionUrl = $extra['action_url'] ?? '/';

        $endpoint = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        foreach ($tokens as $deviceToken) {
            $payload = [
                'message' => [
                    'token' => $deviceToken,
                    'notification' => [
                        'title' => $title,
                        'body' => $message,
                    ],
                    'data' => [
                        'title' => $title,
                        'body' => $message,
                        'action_url' => $actionUrl,
                    ],
                    'android' => [
                        'priority' => 'HIGH',
                        'notification' => [
                            'sound' => 'default',
                            'default_sound' => true,
                            'default_vibrate_timings' => true,
                            'channel_id' => 'school_erp_high_importance',
                            'notification_priority' => 'PRIORITY_MAX',
                            'visibility' => 'PUBLIC',
                        ],
                    ],
                    'webpush' => [
                        'headers' => [
                            'Urgency' => 'high',
                        ],
                        'notification' => [
                            'title' => $title,
                            'body' => $message,
                            'icon' => '/images/school-logo.png',
                            'badge' => '/images/school-logo.png',
                            'requireInteraction' => true,
                        ],
                        'fcm_options' => [
                            'link' => $actionUrl,
                        ],
                    ],
                    'apns' => [
                        'payload' => [
                            'aps' => [
                                'sound' => 'default',
                                'badge' => 1,
                            ],
                        ],
                    ],
                ]
            ];

            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type'  => 'application/json',
                ])->timeout(8)->post($endpoint, $payload);

                if ($response->successful()) {
                    $successCount++;
                } else {
                    $failureCount++;
                    Log::warning("FCM V1 Send failed for token: " . substr($deviceToken, 0, 15) . "... Status: {$response->status()} Body: " . $response->body());
                    $errorJson = $response->json('error');
                    if (isset($errorJson['status']) && in_array($errorJson['status'], ['NOT_FOUND', 'INVALID_ARGUMENT', 'UNREGISTERED'])) {
                        FcmDeviceToken::where('token', $deviceToken)->delete();
                    }
                }
            } catch (\Throwable $e) {
                Log::error("FCM V1 Send exception for token: " . $e->getMessage());
                $failureCount++;
            }
        }

        return [
            'success_count' => $successCount,
            'failure_count' => $failureCount,
            'message' => "FCM V1 dispatched: {$successCount} delivered successfully" . ($failureCount > 0 ? ", {$failureCount} failed/invalid tokens cleaned up." : ".")
        ];
    }

    /**
     * Generate Google OAuth2 Token from Service Account Private Key (Pure PHP JWT)
     */
    protected static function getGoogleOAuth2Token(array $serviceAccount): ?string
    {
        try {
            $now = time();
            $header = ['alg' => 'RS256', 'typ' => 'JWT'];
            $claim = [
                'iss'   => $serviceAccount['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud'   => 'https://oauth2.googleapis.com/token',
                'exp'   => $now + 3600,
                'iat'   => $now,
            ];

            $b64Header = self::base64UrlEncode(json_encode($header));
            $b64Claim = self::base64UrlEncode(json_encode($claim));
            $dataToSign = $b64Header . '.' . $b64Claim;

            $privateKey = $serviceAccount['private_key'];
            $signature = '';
            $success = openssl_sign($dataToSign, $signature, $privateKey, OPENSSL_ALGO_SHA256);

            if (!$success) {
                return null;
            }

            $jwt = $dataToSign . '.' . self::base64UrlEncode($signature);

            $tokenResponse = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion'  => $jwt,
            ]);

            if ($tokenResponse->successful()) {
                return $tokenResponse->json('access_token');
            }

            return null;
        } catch (\Throwable $e) {
            Log::error("Google OAuth2 JWT Exception: " . $e->getMessage());
            return null;
        }
    }

    protected static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
