<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ModulePlaceholderController extends Controller
{
    public function show(Request $request, ?string $module = null, ?string $feature = null)
    {
        $moduleName = $module ?: 'Module';
        $title = $feature ?: 'Feature Overview';

        return view('school.placeholder', compact('moduleName', 'title'));
    }
}
