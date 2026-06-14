<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Student;

class ParentDashboardController extends Controller
{
    public function index()
    {
        $children = Student::where('guardian_email', auth()->user()->email)
            ->with(['section', 'class'])
            ->get();
            
        return view('parent.dashboard', compact('children'));
    }
}
