<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\LoginLog;
use App\Models\StudentDocument;
use Illuminate\Http\Request;

class DownloadStatisticsController extends Controller
{
    public function downloadStatus()
    {
        $schoolId = auth()->user()->school_id;

        // Fetch documents download history
        $documents = StudentDocument::where('school_id', $schoolId)
            ->with(['student.class', 'student.section'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Stub data for statistics
        $studentDownloadsCount = $documents->count();
        $staffDownloadsCount = round($documents->count() * 0.4);
        $parentDownloadsCount = round($documents->count() * 0.8);

        return view('school.downloads.status', compact(
            'documents',
            'studentDownloadsCount',
            'staffDownloadsCount',
            'parentDownloadsCount'
        ));
    }

    public function userActivity()
    {
        $schoolId = auth()->user()->school_id;

        // Query login logs to show active user sessions
        $logs = LoginLog::whereHas('user', function($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            })
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('school.downloads.activity', compact('logs'));
    }
}
