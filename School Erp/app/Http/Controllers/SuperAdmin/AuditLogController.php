<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ImplementationTracker\ImplActivityLog;
use App\Models\LoginLog;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class AuditLogController extends Controller
{
    /**
     * Show paginated system activity logs and user login logs.
     */
    public function index(Request $request): View
    {
        // 1. Fetch system activity logs with pagination, without filtering by single school scope since it is for superadmin.
        // We eager load the 'school' relationship.
        $activityLogs = ImplActivityLog::with('school')
            ->orderBy('created_at', 'desc')
            ->paginate(25, ['*'], 'activity_page');

        // 2. Fetch login logs with pagination.
        $loginLogs = LoginLog::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(25, ['*'], 'login_page');

        return view('superadmin.audit-logs.index', compact('activityLogs', 'loginLogs'));
    }
}
