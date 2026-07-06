<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Student;

class ParentChildrenController extends Controller
{
    public function show(Student $student)
    {
        if ($student->guardian_email !== auth()->user()->email && $student->user_id !== auth()->user()->id) {
            abort(403, 'Access denied');
        }
        
        return view('parent.children.show', compact('student'));
    }
}
