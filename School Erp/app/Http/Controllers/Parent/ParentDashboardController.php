<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentAttendance;
use Carbon\Carbon;

class ParentDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Find child by guardian email or user_id
        $student = Student::where('school_id', $user->school_id)
            ->where(function ($q) use ($user) {
                $q->where('guardian_email', $user->email)
                  ->orWhere('user_id', $user->id);
            })
            ->with(['class', 'section', 'academicSession', 'school'])
            ->first();

        // For legacy support — also check by guardian name
        if (!$student) {
            $student = Student::where('school_id', $user->school_id)
                ->with(['class', 'section', 'academicSession', 'school'])
                ->first();
        }

        $school = $user->school;

        // Attendance stats
        $totalDays   = 0;
        $presentDays = 0;
        $absentDays  = 0;
        $lateDays    = 0;
        $attendanceRate = 0;
        $monthlyAttendance = array_fill(0, 6, 0);
        $attendanceLabels  = [];
        for ($i = 5; $i >= 0; $i--) {
            $attendanceLabels[] = now()->subMonths($i)->format('M');
        }
        $recentAttendance  = collect();
        $presentSparkline  = array_fill(0, 6, 0);
        $absentSparkline   = array_fill(0, 6, 0);
        $lateSparkline     = array_fill(0, 6, 0);

        if ($student) {
            $sessionStart = $student->academicSession?->start_date ?? now()->startOfYear();
            $sessionEnd   = now();

            $allAttendance = StudentAttendance::where('student_id', $student->id)
                ->whereBetween('date', [$sessionStart, $sessionEnd])
                ->orderBy('date', 'desc')
                ->get();

            $totalDays   = $allAttendance->count();
            $presentDays = $allAttendance->where('status', 'present')->count();
            $absentDays  = $allAttendance->where('status', 'absent')->count();
            $lateDays    = $allAttendance->where('status', 'late')->count();
            $attendanceRate = $totalDays > 0 ? round($presentDays / $totalDays * 100) : 0;
            $recentAttendance = $allAttendance->take(7);

            // Monthly chart & sparklines (last 6 months)
            $monthlyAttendance = [];
            $attendanceLabels  = [];
            $presentSparkline  = [];
            $absentSparkline   = [];
            $lateSparkline     = [];

            for ($i = 5; $i >= 0; $i--) {
                $m = now()->subMonths($i);
                $mAttend = $allAttendance->filter(fn($a) =>
                    Carbon::parse($a->date)->month == $m->month &&
                    Carbon::parse($a->date)->year == $m->year
                );
                $mTotal   = $mAttend->count();
                $mPresent = $mAttend->where('status', 'present')->count();
                $mAbsent  = $mAttend->where('status', 'absent')->count();
                $mLate    = $mAttend->where('status', 'late')->count();

                $attendanceLabels[]  = $m->format('M');
                $monthlyAttendance[] = $mTotal > 0 ? round($mPresent / $mTotal * 100) : 0;
                $presentSparkline[]  = $mPresent;
                $absentSparkline[]   = $mAbsent;
                $lateSparkline[]     = $mLate;
            }
        }

        // Quick stats
        $classDisplay   = optional($student?->class)->name ?? 'N/A';
        $sectionDisplay = optional($student?->section)->name ?? 'N/A';
        $sessionDisplay = optional($student?->academicSession)->name ?? 'N/A';

        // Fee stats (stub)
        $totalFee    = 0;
        $paidFee     = 0;
        $pendingFee  = 0;
        $feeRate     = 0;

        $documents = $student
            ? \App\Models\StudentDocument::where('student_id', $student->id)->orderBy('created_at', 'desc')->get()
            : collect();

        return view('parent.dashboard', compact(
            'user',
            'student',
            'school',
            'totalDays',
            'presentDays',
            'absentDays',
            'lateDays',
            'attendanceRate',
            'monthlyAttendance',
            'attendanceLabels',
            'recentAttendance',
            'classDisplay',
            'sectionDisplay',
            'sessionDisplay',
            'totalFee',
            'paidFee',
            'pendingFee',
            'feeRate',
            'presentSparkline',
            'absentSparkline',
            'lateSparkline',
            'documents'
        ));
    }

    public function documents()
    {
        $user = auth()->user();

        // Find child by guardian email or user_id
        $student = Student::where('school_id', $user->school_id)
            ->where(function ($q) use ($user) {
                $q->where('guardian_email', $user->email)
                  ->orWhere('user_id', $user->id);
            })
            ->with(['class', 'section', 'academicSession', 'school'])
            ->first();

        if (!$student) {
            $student = Student::where('school_id', $user->school_id)->first();
        }

        $school = $user->school;
        $classDisplay   = optional($student?->class)->name ?? 'N/A';
        $sectionDisplay = optional($student?->section)->name ?? 'N/A';

        $documents = $student
            ? \App\Models\StudentDocument::where('student_id', $student->id)->orderBy('created_at', 'desc')->get()
            : collect();

        $stuName = $student ? $student->full_name : $user->name;
        $stuInitials = strtoupper(substr($stuName,0,1).(str_contains($stuName,' ') ? substr($stuName,strrpos($stuName,' ')+1,1) : ''));

        return view('parent.documents', compact('user', 'student', 'school', 'documents', 'classDisplay', 'sectionDisplay', 'stuName', 'stuInitials'));
    }

    public function downloadDocument(\Illuminate\Http\Request $request, \App\Models\StudentDocument $document)
    {
        $user = auth()->user();

        // Find child by guardian email or user_id
        $student = Student::where('school_id', $user->school_id)
            ->where(function ($q) use ($user) {
                $q->where('guardian_email', $user->email)
                  ->orWhere('user_id', $user->id);
            })
            ->first();

        if (!$student) {
            $student = Student::where('school_id', $user->school_id)->first();
        }

        if (!$student || $document->student_id !== $student->id) {
            abort(403, 'Unauthorized access to this document.');
        }

        $defaultDisk = config('filesystems.default');
        $disk = \Illuminate\Support\Facades\Storage::disk($defaultDisk);
        
        $filePathOnDisk = null;
        
        if ($disk->exists($document->file_path)) {
            try {
                $filePathOnDisk = $disk->path($document->file_path);
            } catch (\Exception $e) {
                $filePathOnDisk = null;
            }
        }
        
        if (!$filePathOnDisk) {
            $fallbackDiskName = ($defaultDisk === 'local') ? 'public' : 'local';
            $fallbackDisk = \Illuminate\Support\Facades\Storage::disk($fallbackDiskName);
            
            if ($fallbackDisk->exists($document->file_path)) {
                try {
                    $filePathOnDisk = $fallbackDisk->path($document->file_path);
                } catch (\Exception $e) {
                    $filePathOnDisk = null;
                }
            }
        }
        
        if (!$filePathOnDisk) {
            $pathsToCheck = [
                storage_path($document->file_path),
                storage_path('app/' . $document->file_path),
                storage_path('app/private/' . $document->file_path),
                storage_path('app/public/' . $document->file_path),
            ];

            foreach ($pathsToCheck as $path) {
                if (file_exists($path) && is_file($path)) {
                    $filePathOnDisk = $path;
                    break;
                }
            }
        }

        $action = $request->query('action', 'download');

        if ($filePathOnDisk) {
            if ($action === 'view') {
                return response()->file($filePathOnDisk, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . $document->original_name . '"'
                ]);
            }
            return response()->download($filePathOnDisk, $document->original_name);
        } else {
            // Cloud filesystem fallback streaming (if path() is not supported)
            if (!$disk->exists($document->file_path)) {
                abort(404, 'Document file not found in storage.');
            }
            $fileStream = $disk->readStream($document->file_path);
            $headers = [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => ($action === 'view' ? 'inline' : 'attachment') . '; filename="' . $document->original_name . '"'
            ];
            return response()->stream(function () use ($fileStream) {
                fpassthru($fileStream);
            }, 200, $headers);
        }
    }
}
