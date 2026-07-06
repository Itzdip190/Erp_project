<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;

class PlatformSettingsController extends Controller
{
    protected string $filePath = 'platform_settings.json';

    /**
     * Get default settings.
     */
    private function getDefaultSettings(): array
    {
        return [
            'maintenance_mode' => false,
            'enable_registration' => true,
            'session_lifetime' => 120,
            'smtp_host' => 'smtp.mailtrap.io',
            'smtp_port' => 2525,
            'smtp_username' => '',
            'smtp_password' => '',
            'smtp_encryption' => 'tls',
        ];
    }

    /**
     * Show general settings form.
     */
    public function index(): View
    {
        $settings = $this->getDefaultSettings();

        if (Storage::disk('local')->exists($this->filePath)) {
            $fileContent = json_decode(Storage::disk('local')->get($this->filePath), true);
            if (is_array($fileContent)) {
                $settings = array_replace($settings, $fileContent);
            }
        }

        return view('superadmin.platform-settings.index', compact('settings'));
    }

    /**
     * Update configurations in storage.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'maintenance_mode' => 'nullable|string',
            'enable_registration' => 'nullable|string',
            'session_lifetime' => 'required|integer|min:15|max:1440',
            'smtp_host' => 'nullable|string|max:255',
            'smtp_port' => 'nullable|integer|min:1|max:65535',
            'smtp_username' => 'nullable|string|max:255',
            'smtp_password' => 'nullable|string|max:255',
            'smtp_encryption' => 'required|in:none,tls,ssl',
        ]);

        $settings = [
            'maintenance_mode' => isset($request->maintenance_mode) && $request->maintenance_mode == '1',
            'enable_registration' => isset($request->enable_registration) && $request->enable_registration == '1',
            'session_lifetime' => (int)$validated['session_lifetime'],
            'smtp_host' => $request->smtp_host ?? '',
            'smtp_port' => $request->smtp_port ? (int)$request->smtp_port : null,
            'smtp_username' => $request->smtp_username ?? '',
            'smtp_password' => $request->smtp_password ?? '',
            'smtp_encryption' => $validated['smtp_encryption'],
        ];

        Storage::disk('local')->put($this->filePath, json_encode($settings, JSON_PRETTY_PRINT));

        return redirect()->route('superadmin.platform-settings.index')
            ->with('success', 'Platform configurations saved successfully.');
    }
}
