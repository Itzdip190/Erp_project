<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;

class SmsGatewayController extends Controller
{
    protected string $filePath = 'sms_gateways.json';

    /**
     * Get default configurations.
     */
    private function getDefaultSettings(): array
    {
        return [
            'twilio' => [
                'enabled' => false,
                'account_sid' => '',
                'auth_token' => '',
                'sender_number' => '',
            ],
            'msg91' => [
                'enabled' => false,
                'auth_key' => '',
                'sender_id' => '',
                'route' => '4', // Transactional route
            ],
            'fast2sms' => [
                'enabled' => false,
                'authorization_key' => '',
                'sender_id' => '',
            ],
        ];
    }

    /**
     * Display SMS config view.
     */
    public function index(): View
    {
        $settings = $this->getDefaultSettings();

        if (Storage::disk('local')->exists($this->filePath)) {
            $fileContent = json_decode(Storage::disk('local')->get($this->filePath), true);
            if (is_array($fileContent)) {
                $settings = array_replace_recursive($settings, $fileContent);
            }
        }

        return view('superadmin.sms-gateways.index', compact('settings'));
    }

    /**
     * Update configurations in file.
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'twilio.mode' => 'nullable',
            'twilio.account_sid' => 'nullable|string|max:255',
            'twilio.auth_token' => 'nullable|string|max:255',
            'twilio.sender_number' => 'nullable|string|max:255',
            'msg91.auth_key' => 'nullable|string|max:255',
            'msg91.sender_id' => 'nullable|string|max:255',
            'msg91.route' => 'nullable|string|max:50',
            'fast2sms.authorization_key' => 'nullable|string|max:255',
            'fast2sms.sender_id' => 'nullable|string|max:255',
        ]);

        $settings = [
            'twilio' => [
                'enabled' => isset($request->twilio['enabled']) && $request->twilio['enabled'] == '1',
                'account_sid' => $request->twilio['account_sid'] ?? '',
                'auth_token' => $request->twilio['auth_token'] ?? '',
                'sender_number' => $request->twilio['sender_number'] ?? '',
            ],
            'msg91' => [
                'enabled' => isset($request->msg91['enabled']) && $request->msg91['enabled'] == '1',
                'auth_key' => $request->msg91['auth_key'] ?? '',
                'sender_id' => $request->msg91['sender_id'] ?? '',
                'route' => $request->msg91['route'] ?? '4',
            ],
            'fast2sms' => [
                'enabled' => isset($request->fast2sms['enabled']) && $request->fast2sms['enabled'] == '1',
                'authorization_key' => $request->fast2sms['authorization_key'] ?? '',
                'sender_id' => $request->fast2sms['sender_id'] ?? '',
            ],
        ];

        Storage::disk('local')->put($this->filePath, json_encode($settings, JSON_PRETTY_PRINT));

        return redirect()->route('superadmin.sms-gateways.index')
            ->with('success', 'SMS gateway configurations updated successfully.');
    }
}
