<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    protected string $filePath = 'superadmin_settings.json';

    /**
     * Get default settings values.
     */
    private function getDefaultSettings(): array
    {
        return [
            'timezone' => 'UTC',
            'currency' => 'INR',
            'notification_email' => true,
            'notification_system' => true,
            'default_per_page' => 10,
            'mrr_target' => 500000,
        ];
    }

    /**
     * Display personal settings page.
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

        return view('superadmin.settings.index', compact('settings'));
    }

    /**
     * Save the settings to file storage.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'timezone' => 'required|string',
            'currency' => 'required|string|max:5',
            'notification_email' => 'nullable|string',
            'notification_system' => 'nullable|string',
            'default_per_page' => 'required|integer|min:5|max:100',
            'mrr_target' => 'required|numeric|min:0',
        ]);

        $settings = [
            'timezone' => $validated['timezone'],
            'currency' => $validated['currency'],
            'notification_email' => isset($request->notification_email),
            'notification_system' => isset($request->notification_system),
            'default_per_page' => (int)$validated['default_per_page'],
            'mrr_target' => (float)$validated['mrr_target'],
        ];

        Storage::disk('local')->put($this->filePath, json_encode($settings, JSON_PRETTY_PRINT));

        return redirect()->route('superadmin.settings')
            ->with('success', 'Personal account settings updated successfully.');
    }
}
