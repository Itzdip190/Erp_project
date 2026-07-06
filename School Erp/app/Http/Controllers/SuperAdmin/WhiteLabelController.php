<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;

class WhiteLabelController extends Controller
{
    protected string $filePath = 'white_label_settings.json';

    /**
     * Get default settings.
     */
    private function getDefaultSettings(): array
    {
        return [
            'app_name' => 'SchoolCloud ERP',
            'logo_url' => 'https://images.unsplash.com/photo-1546410531-bb4caa6b424d?w=200',
            'favicon_url' => 'https://images.unsplash.com/photo-1546410531-bb4caa6b424d?w=32',
            'copyright_text' => '© 2026 EduCore Solutions. All rights reserved.',
            'support_email' => 'support@schoolcloud.com',
            'support_phone' => '+91 99999 88888',
            'primary_color' => '#3b82f6',
            'secondary_color' => '#0f172a',
        ];
    }

    /**
     * Show branding config form.
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

        return view('superadmin.white-label.index', compact('settings'));
    }

    /**
     * Update branding config in storage.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'app_name' => 'required|string|max:255',
            'logo_url' => 'nullable|url',
            'favicon_url' => 'nullable|url',
            'copyright_text' => 'required|string|max:255',
            'support_email' => 'required|email|max:255',
            'support_phone' => 'required|string|max:50',
            'primary_color' => 'required|string|regex:/^#[a-fA-F0-9]{6}$/',
            'secondary_color' => 'required|string|regex:/^#[a-fA-F0-9]{6}$/',
        ]);

        Storage::disk('local')->put($this->filePath, json_encode($validated, JSON_PRETTY_PRINT));

        return redirect()->route('superadmin.white-label.index')
            ->with('success', 'White-label configurations saved successfully.');
    }
}
