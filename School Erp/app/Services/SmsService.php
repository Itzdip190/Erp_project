<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SmsService
{
    protected static string $configFile = 'sms_gateways.json';

    /**
     * Retrieve all saved SMS gateway configurations.
     */
    public static function getSettings(): array
    {
        if (Storage::disk('local')->exists(self::$configFile)) {
            $data = json_decode(Storage::disk('local')->get(self::$configFile), true);
            if (is_array($data)) {
                return $data;
            }
        }
        return [];
    }

    /**
     * Determine the currently active gateway key and its configuration.
     */
    public static function getActiveGateway(): ?array
    {
        $settings = self::getSettings();

        // Check configured gateways in order of preference
        $gateways = ['custom', 'twilio', 'msg91', 'fast2sms'];
        foreach ($gateways as $gw) {
            if (!empty($settings[$gw]['enabled'])) {
                return [
                    'driver' => $gw,
                    'config' => $settings[$gw],
                ];
            }
        }

        return null;
    }

    /**
     * Send an SMS to a single recipient or comma-separated recipients.
     *
     * @param string $phone Mobile number(s)
     * @param string $message Text content
     * @param array $options Additional options (e.g. ['bypass_local' => true, 'template_id' => '...'])
     * @return array ['success' => bool, 'message' => string, 'gateway' => string, 'raw_response' => mixed]
     */
    public static function send(string $phone, string $message, array $options = []): array
    {
        $active = self::getActiveGateway();

        // If forced gateway driver passed in options
        if (!empty($options['driver']) && !empty($options['config'])) {
            $active = [
                'driver' => $options['driver'],
                'config' => $options['config'],
            ];
        }

        if (!$active) {
            Log::warning("SmsService: No active SMS gateway configured.");
            return [
                'success' => false,
                'message' => 'No SMS gateway is currently enabled in settings.',
                'gateway' => 'none',
                'raw_response' => null,
            ];
        }

        // Clean recipient number (remove whitespace, dashes)
        $cleanPhone = preg_replace('/[^\d+]/', '', trim($phone));

        // In local/testing mode, mock unless bypass_local is specified (e.g. from the Live Test SMS button)
        $bypassLocal = $options['bypass_local'] ?? false;
        if (app()->environment('local', 'testing') && !$bypassLocal) {
            Log::info("SmsService [MOCK - {$active['driver']}]: Message to {$cleanPhone}: \"{$message}\"");
            return [
                'success' => true,
                'message' => "Mock SMS logged locally to {$cleanPhone} (Driver: {$active['driver']}).",
                'gateway' => $active['driver'],
                'raw_response' => ['mock' => true, 'phone' => $cleanPhone, 'text' => $message],
            ];
        }

        return match ($active['driver']) {
            'custom'   => self::sendViaCustom($cleanPhone, $message, $active['config'], $options),
            'twilio'   => self::sendViaTwilio($cleanPhone, $message, $active['config']),
            'msg91'    => self::sendViaMsg91($cleanPhone, $message, $active['config'], $options),
            'fast2sms' => self::sendViaFast2Sms($cleanPhone, $message, $active['config']),
            default    => [
                'success' => false,
                'message' => "Unsupported gateway driver: {$active['driver']}",
                'gateway' => $active['driver'],
                'raw_response' => null,
            ],
        };
    }

    /**
     * Dispatch via Custom / Generic HTTP SMS Gateway.
     */
    protected static function sendViaCustom(string $phone, string $message, array $cfg, array $options): array
    {
        $apiUrl = trim($cfg['api_url'] ?? '');
        if (empty($apiUrl)) {
            return [
                'success' => false,
                'message' => 'Custom SMS Gateway API Endpoint URL is empty.',
                'gateway' => 'custom',
                'raw_response' => null,
            ];
        }

        $method = strtoupper($cfg['method'] ?? 'POST_JSON');
        $authType = $cfg['auth_type'] ?? 'bearer';
        $authHeaderName = trim($cfg['auth_header_name'] ?? 'Authorization');
        $authToken = trim($cfg['auth_token'] ?? '');

        $phoneParam = trim($cfg['phone_param'] ?? 'to') ?: 'to';
        $messageParam = trim($cfg['message_param'] ?? 'message') ?: 'message';
        $senderIdParam = trim($cfg['sender_id_param'] ?? 'sender') ?: 'sender';
        $senderIdValue = trim($cfg['sender_id_value'] ?? '');

        // Prepare request parameters
        $payload = [
            $phoneParam => $phone,
            $messageParam => $message,
        ];

        if (!empty($senderIdValue)) {
            $payload[$senderIdParam] = $senderIdValue;
        }

        // Merge extra static parameters if configured
        if (!empty($cfg['extra_params'])) {
            $extra = is_array($cfg['extra_params']) 
                ? $cfg['extra_params'] 
                : json_decode($cfg['extra_params'], true);
            if (is_array($extra)) {
                $payload = array_merge($payload, $extra);
            }
        }

        // Also merge runtime options
        if (!empty($options['extra_params']) && is_array($options['extra_params'])) {
            $payload = array_merge($payload, $options['extra_params']);
        }

        // Configure Client Headers
        $headers = [
            'Accept' => 'application/json',
        ];

        $queryParams = [];

        // Apply Authentication
        switch ($authType) {
            case 'bearer':
                if (!empty($authToken)) {
                    $headers['Authorization'] = str_starts_with($authToken, 'Bearer ') ? $authToken : 'Bearer ' . $authToken;
                }
                break;
            case 'header':
                if (!empty($authHeaderName) && !empty($authToken)) {
                    $headers[$authHeaderName] = $authToken;
                }
                break;
            case 'query':
                if (!empty($authHeaderName) && !empty($authToken)) {
                    $queryParams[$authHeaderName] = $authToken;
                }
                break;
            case 'basic':
                // Token can be username:password
                if (!empty($authToken)) {
                    $headers['Authorization'] = 'Basic ' . base64_encode($authToken);
                }
                break;
            case 'none':
            default:
                break;
        }

        try {
            $client = Http::withHeaders($headers)->timeout(15);

            if ($method === 'GET') {
                $allQuery = array_merge($queryParams, $payload);
                $response = $client->get($apiUrl, $allQuery);
            } elseif ($method === 'POST_FORM') {
                if (!empty($queryParams)) {
                    $apiUrl .= (str_contains($apiUrl, '?') ? '&' : '?') . http_build_query($queryParams);
                }
                $response = $client->asForm()->post($apiUrl, $payload);
            } else { // POST_JSON default
                if (!empty($queryParams)) {
                    $apiUrl .= (str_contains($apiUrl, '?') ? '&' : '?') . http_build_query($queryParams);
                }
                $response = $client->asJson()->post($apiUrl, $payload);
            }

            $isSuccess = $response->successful();
            $statusCode = $response->status();
            $rawBody = $response->json() ?? $response->body();

            if ($isSuccess) {
                Log::info("SmsService [Custom]: Delivered to {$phone}. Status: {$statusCode}");
                return [
                    'success' => true,
                    'message' => "SMS successfully dispatched via Custom Gateway (HTTP {$statusCode}).",
                    'gateway' => 'custom',
                    'status_code' => $statusCode,
                    'raw_response' => $rawBody,
                ];
            }

            Log::error("SmsService [Custom]: Failed for {$phone}. HTTP {$statusCode} - " . json_encode($rawBody));
            return [
                'success' => false,
                'message' => "Gateway returned HTTP {$statusCode}: " . (is_string($rawBody) ? substr($rawBody, 0, 200) : json_encode($rawBody)),
                'gateway' => 'custom',
                'status_code' => $statusCode,
                'raw_response' => $rawBody,
            ];

        } catch (\Throwable $e) {
            Log::error("SmsService [Custom]: Exception dispatching to {$phone}: " . $e->getMessage());
            return [
                'success' => false,
                'message' => "Connection exception: " . $e->getMessage(),
                'gateway' => 'custom',
                'status_code' => 500,
                'raw_response' => null,
            ];
        }
    }

    /**
     * Dispatch via Twilio REST API.
     */
    protected static function sendViaTwilio(string $phone, string $message, array $cfg): array
    {
        $sid = trim($cfg['account_sid'] ?? '');
        $token = trim($cfg['auth_token'] ?? '');
        $from = trim($cfg['sender_number'] ?? '');

        if (!$sid || !$token || !$from) {
            return [
                'success' => false,
                'message' => 'Twilio configuration is incomplete (Account SID, Auth Token, or Sender Number missing).',
                'gateway' => 'twilio',
                'raw_response' => null,
            ];
        }

        try {
            $url = "https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json";
            $response = Http::withBasicAuth($sid, $token)
                ->asForm()
                ->timeout(15)
                ->post($url, [
                    'To'   => $phone,
                    'From' => $from,
                    'Body' => $message,
                ]);

            $isSuccess = $response->successful();
            $rawBody = $response->json() ?? $response->body();

            if ($isSuccess) {
                Log::info("SmsService [Twilio]: Delivered to {$phone}. SID: " . ($rawBody['sid'] ?? 'N/A'));
                return [
                    'success' => true,
                    'message' => 'SMS delivered successfully via Twilio.',
                    'gateway' => 'twilio',
                    'raw_response' => $rawBody,
                ];
            }

            Log::error("SmsService [Twilio]: Failed for {$phone}: " . json_encode($rawBody));
            return [
                'success' => false,
                'message' => $rawBody['message'] ?? 'Twilio API delivery failed.',
                'gateway' => 'twilio',
                'raw_response' => $rawBody,
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Twilio Exception: ' . $e->getMessage(),
                'gateway' => 'twilio',
                'raw_response' => null,
            ];
        }
    }

    /**
     * Dispatch via Msg91.
     */
    protected static function sendViaMsg91(string $phone, string $message, array $cfg, array $options): array
    {
        $authKey = trim($cfg['auth_key'] ?? '');
        $senderId = trim($cfg['sender_id'] ?? '');
        $route = trim($cfg['route'] ?? '4');

        if (!$authKey) {
            return [
                'success' => false,
                'message' => 'Msg91 Auth Key is missing.',
                'gateway' => 'msg91',
                'raw_response' => null,
            ];
        }

        try {
            $response = Http::timeout(15)->get('https://api.msg91.com/api/sendhttp.php', [
                'authkey'  => $authKey,
                'mobiles'  => $phone,
                'message'  => urlencode($message),
                'sender'   => $senderId,
                'route'    => $route,
                'country'  => $options['country'] ?? '91',
            ]);

            $body = $response->body();
            $isSuccess = $response->successful() && !str_contains(strtolower($body), 'error');

            return [
                'success' => $isSuccess,
                'message' => $isSuccess ? 'SMS sent via Msg91.' : "Msg91 Error: {$body}",
                'gateway' => 'msg91',
                'raw_response' => $body,
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Msg91 Exception: ' . $e->getMessage(),
                'gateway' => 'msg91',
                'raw_response' => null,
            ];
        }
    }

    /**
     * Dispatch via Fast2SMS.
     */
    protected static function sendViaFast2Sms(string $phone, string $message, array $cfg): array
    {
        $apiKey = trim($cfg['authorization_key'] ?? '');
        $senderId = trim($cfg['sender_id'] ?? '');

        if (!$apiKey) {
            return [
                'success' => false,
                'message' => 'Fast2SMS Authorization Key is missing.',
                'gateway' => 'fast2sms',
                'raw_response' => null,
            ];
        }

        try {
            $payload = [
                'route'    => 'q',
                'message'  => $message,
                'language' => 'english',
                'flash'    => 0,
                'numbers'  => $phone,
            ];
            if (!empty($senderId)) {
                $payload['sender_id'] = $senderId;
            }

            $response = Http::withHeaders([
                'authorization' => $apiKey,
                'Accept'        => 'application/json',
            ])->timeout(15)->post('https://www.fast2sms.com/dev/bulkV2', $payload);

            $rawBody = $response->json();
            $isSuccess = $response->successful() && !empty($rawBody['return']);

            return [
                'success' => $isSuccess,
                'message' => $isSuccess ? 'SMS dispatched via Fast2SMS.' : ($rawBody['message'][0] ?? 'Fast2SMS delivery failed.'),
                'gateway' => 'fast2sms',
                'raw_response' => $rawBody,
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Fast2SMS Exception: ' . $e->getMessage(),
                'gateway' => 'fast2sms',
                'raw_response' => null,
            ];
        }
    }
}
