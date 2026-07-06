<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class MenuManagerController extends Controller
{
    /**
     * Show the school sidebar menu service configurations.
     */
    public function index(Request $request): View
    {
        $schools = School::orderBy('name', 'asc')->get();
        $selectedSchoolId = $request->query('school_id', $schools->first()?->id);
        $selectedSchool = $selectedSchoolId ? School::find($selectedSchoolId) : null;

        // Fetch dynamically from ModuleRegistry
        $registryModules = \App\Support\ModuleRegistry::all();
        $modules = [];

        foreach ($registryModules as $key => $mod) {
            $icon = $mod['icon'];
            
            // Resolve blade asset expressions like {{ asset('path') }} to absolute URLs
            $icon = preg_replace_callback('/\{\{\s*asset\(([\'"])(.*?)\1\)\s*\}\}/', function($m) {
                return asset($m[2]);
            }, $icon);

            $modules[$key] = [
                'name'          => $mod['original_title'] ?: $mod['label'],
                'icon'          => $icon,
                'features'      => implode(', ', array_values($mod['features'])),
                'features_raw'  => $mod['features'],
                'default_title' => $mod['default_title'] ?? ($mod['original_title'] ?: $mod['label']),
            ];
        }

        $disabledModules = $selectedSchool && is_array($selectedSchool->disabled_modules)
            ? $selectedSchool->disabled_modules
            : [];

        return view('superadmin.menu-manager.index', compact('schools', 'selectedSchoolId', 'selectedSchool', 'modules', 'disabledModules'));
    }

    /**
     * Update the toggled services for a specific school.
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'school_id' => 'required|exists:schools,id',
            'enabled_modules' => 'array',
        ]);

        $school = School::findOrFail($request->school_id);
        
        // Dynamically fetch all valid module keys from registry
        $allModules = array_keys(\App\Support\ModuleRegistry::all());

        $enabled = $request->input('enabled_modules', []);
        $disabled = array_values(array_diff($allModules, $enabled));

        $school->disabled_modules = $disabled;
        $school->save();

        $filteredNames = [];
        if ($request->has('menu_names')) {
            $menuNames = $request->input('menu_names');
            foreach ($menuNames as $key => $name) {
                if ($name !== null && trim($name) !== '') {
                    $filteredNames[$key] = trim($name);
                }
            }
        }
        if ($request->has('feature_names')) {
            $featureNames = $request->input('feature_names');
            foreach ($featureNames as $modKey => $feats) {
                foreach ($feats as $featKey => $name) {
                    if ($name !== null && trim($name) !== '') {
                        $filteredNames["{$modKey}:{$featKey}"] = trim($name);
                    }
                }
            }
        }
        \App\Support\ModuleRegistry::saveCustomNames($filteredNames);

        return redirect()->route('superadmin.menu-manager.index', ['school_id' => $school->id])
            ->with('success', "Service configurations and menu names updated successfully!");
    }
}
