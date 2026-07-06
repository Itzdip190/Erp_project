@extends('superadmin.layouts.master')

@section('styles')
<style>
/* ─── AUDIT LOGS PAGE ────────────────────────────────────────── */
.sa-audit-wrap { padding: 0; }

.sa-audit-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 14px;
}
.sa-audit-title-area {
    display: flex;
    align-items: center;
    gap: 16px;
}
.sa-audit-icon {
    width: 52px; height: 52px;
    border-radius: 16px;
    background: linear-gradient(135deg, #6366f1, #4f46e5);
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 8px 24px rgba(99,102,241,.25);
    color: #fff;
    font-size: 20px;
}
.sa-audit-title-area h1 {
    font-size: 20px; font-weight: 800;
    color: #1e1b4b; margin: 0;
}
.sa-audit-title-area p { font-size: 12px; color: #64748b; margin: 3px 0 0; }

/* Custom Tab Styling */
.sa-tabs-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(0,0,0,.03);
    overflow: hidden;
    margin-bottom: 30px;
}
.sa-tabs-header {
    display: flex;
    align-items: center;
    border-bottom: 1.5px solid #f1f5f9;
    background: #f8fafc;
    padding: 0 24px;
}
.sa-tab-btn {
    padding: 16px 20px;
    background: none;
    border: none;
    font-size: 13.5px;
    font-weight: 700;
    color: #64748b;
    cursor: pointer;
    position: relative;
    transition: color .2s;
    outline: none !important;
}
.sa-tab-btn:hover { color: #4f46e5; }
.sa-tab-btn.active {
    color: #4f46e5;
}
.sa-tab-btn.active:after {
    content: '';
    position: absolute;
    bottom: -1.5px; left: 0; right: 0;
    height: 3px;
    background: #4f46e5;
    border-radius: 3px 3px 0 0;
}
.sa-tab-content { display: none; padding: 24px; }
.sa-tab-content.active { display: block; }

/* Table customization */
.sa-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
.sa-table th {
    padding: 12px 16px;
    font-size: 10.5px;
    font-weight: 800;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    background: #f8fafc;
    text-align: left;
    border-bottom: 1px solid #f1f5f9;
}
.sa-table td {
    padding: 14px 16px;
    font-size: 13px;
    color: #374151;
    border-bottom: 1px solid #f8fafc;
    vertical-align: middle;
}
.sa-table tr:hover td { background: #fafbfc; }
.sa-table tr:last-child td { border-bottom: none; }

/* Badge styling */
.sa-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 11px; font-weight: 700;
}
.sa-badge.success { background: rgba(16,185,129,.1); color: #10b981; }
.sa-badge.failed  { background: rgba(239,68,68,.08); color: #ef4444; }
.sa-badge-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }

.sa-school-badge {
    background: rgba(99,102,241,.1);
    color: #4f46e5;
    font-weight: 700;
    font-size: 11.5px;
    padding: 3px 8px;
    border-radius: 8px;
}
.sa-ip-badge {
    background: #f1f5f9;
    color: #475569;
    font-weight: 600;
    font-family: monospace;
    font-size: 11.5px;
    padding: 2px 6px;
    border-radius: 6px;
}

.text-old { color: #94a3b8; text-decoration: line-through; font-size: 12px; }
.text-new { color: #10b981; font-weight: 600; }

.pagination-wrapper {
    display: flex;
    justify-content: flex-end;
    margin-top: 15px;
}
.pagination-wrapper .pagination { margin: 0; }

/* Dark Mode overrides */
body.dark-mode .sa-audit-title-area h1 {
    color: #f8fafc !important;
}
body.dark-mode .sa-tabs-card {
    background-color: #111827 !important;
    border-color: #1e293b !important;
    box-shadow: 0 4px 20px rgba(0,0,0,.3) !important;
}
body.dark-mode .sa-tabs-header {
    background-color: #0f172a !important;
    border-bottom-color: #1e293b !important;
}
body.dark-mode .sa-tab-btn {
    color: #94a3b8 !important;
}
body.dark-mode .sa-tab-btn:hover {
    color: #818cf8 !important;
}
body.dark-mode .sa-tab-btn.active {
    color: #818cf8 !important;
}
body.dark-mode .sa-tab-btn.active:after {
    background: #818cf8 !important;
}
body.dark-mode .sa-table th {
    background-color: #0b0f19 !important;
    color: #475569 !important;
    border-bottom-color: #1e293b !important;
}
body.dark-mode .sa-table td {
    color: #cbd5e1 !important;
    border-bottom-color: #1e293b !important;
}
body.dark-mode .sa-table tr:hover td {
    background-color: #1a2235 !important;
}
body.dark-mode .sa-ip-badge {
    background-color: #1f2937 !important;
    color: #cbd5e1 !important;
}
body.dark-mode .sa-school-badge {
    background-color: rgba(99, 102, 241, 0.15) !important;
    color: #818cf8 !important;
}
body.dark-mode .text-old {
    color: #64748b !important;
}
body.dark-mode .text-new {
    color: #34d399 !important;
}
body.dark-mode .badge-light {
    background-color: #1f2937 !important;
    color: #cbd5e1 !important;
    border-color: #374151 !important;
}
body.dark-mode td span[style*="color:#334155"] {
    color: #cbd5e1 !important;
}
.sa-school-badge.sa-badge-global {
    background: #f1f5f9;
    color: #64748b;
}
body.dark-mode .sa-school-badge.sa-badge-global {
    background: #1f2937 !important;
    color: #cbd5e1 !important;
}
.sa-changed-by {
    font-weight: 600;
    color: #334155;
}
body.dark-mode .sa-changed-by {
    color: #cbd5e1 !important;
}
.sa-log-timestamp {
    color: #64748b;
    font-size: 12px;
}
body.dark-mode .sa-log-timestamp {
    color: #cbd5e1 !important;
}
</style>
@endsection

@section('content')
<div class="sa-audit-wrap">
    
    <!-- Top Header -->
    <div class="sa-audit-header">
        <div class="sa-audit-title-area">
            <div class="sa-audit-icon">
                <i class="fas fa-history"></i>
            </div>
            <div>
                <h1>System Security & Audit Logs</h1>
                <p>Monitor platform activity, tenant configuration updates, and login status across all school nodes.</p>
            </div>
        </div>
    </div>

    <!-- Tabbed Panel -->
    <div class="sa-tabs-card">
        <div class="sa-tabs-header">
            <button class="sa-tab-btn active" onclick="switchTab(event, 'activity-tab')">
                <i class="fas fa-desktop mr-1.5"></i> System Activity Logs
            </button>
            <button class="sa-tab-btn" onclick="switchTab(event, 'login-tab')">
                <i class="fas fa-shield-alt mr-1.5"></i> Login Audits
            </button>
        </div>

        <!-- Tab 1: System Activity Logs -->
        <div id="activity-tab" class="sa-tab-content active">
            <div class="table-responsive">
                <table class="sa-table">
                    <thead>
                        <tr>
                            <th>School</th>
                            <th>Module / Tab</th>
                            <th>Reference Item</th>
                            <th>Field</th>
                            <th>Old Value</th>
                            <th>New Value</th>
                            <th>Changed By</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activityLogs as $log)
                            <tr>
                                <td>
                                    @if($log->school)
                                        <span class="sa-school-badge">{{ $log->school->name }} ({{ $log->school->code }})</span>
                                    @else
                                        <span class="sa-school-badge sa-badge-global">Global / System</span>
                                    @endif
                                </td>
                                <td><strong>{{ $log->tab_name }}</strong></td>
                                <td><code style="color:#e11d48; font-weight: 600;">{{ $log->row_reference }}</code></td>
                                <td><span class="badge badge-light" style="font-size:11px; border:1px solid #cbd5e1;">{{ $log->field_changed }}</span></td>
                                <td><span class="text-old">{{ $log->old_value ?? 'NULL' }}</span></td>
                                <td><span class="text-new">{{ $log->new_value ?? 'NULL' }}</span></td>
                                <td><span class="sa-changed-by"><i class="fas fa-user-circle mr-1" style="color:#94a3b8;"></i> {{ $log->changed_by }}</span></td>
                                <td><span class="sa-log-timestamp">{{ $log->created_at ? $log->created_at->format('M d, Y h:i A') : ($log->changed_at ? $log->changed_at->format('M d, Y h:i A') : 'N/A') }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align:center; padding: 40px 10px; color:#94a3b8;">
                                    <i class="fas fa-info-circle mr-1" style="font-size:18px;"></i> No system activity logs recorded yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="pagination-wrapper">
                {{ $activityLogs->appends(request()->except('activity_page'))->links('pagination::bootstrap-4') }}
            </div>
        </div>

        <!-- Tab 2: Login Audits -->
        <div id="login-tab" class="sa-tab-content">
            <div class="table-responsive">
                <table class="sa-table">
                    <thead>
                        <tr>
                            <th>Email Attempted</th>
                            <th>Resolved User</th>
                            <th>IP Address</th>
                            <th>Status</th>
                            <th>User Agent</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($loginLogs as $log)
                            <tr>
                                <td><strong>{{ $log->email_attempted }}</strong></td>
                                <td>
                                    @if($log->user)
                                        <span style="font-weight:600;"><i class="fas fa-user mr-1" style="font-size:11px;color:#94a3b8;"></i> {{ $log->user->name }}</span>
                                    @else
                                        <span class="text-muted" style="font-size:12px; font-style:italic;">Unresolved</span>
                                    @endif
                                </td>
                                <td><span class="sa-ip-badge">{{ $log->ip_address }}</span></td>
                                <td>
                                    @if($log->status === 'success')
                                        <span class="sa-badge success">
                                            <span class="sa-badge-dot"></span> Success
                                        </span>
                                    @else
                                        <span class="sa-badge failed">
                                            <span class="sa-badge-dot"></span> Failed
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span style="color:#64748b; font-size:11px;" title="{{ $log->user_agent }}">
                                        {{ Str::limit($log->user_agent, 65) }}
                                    </span>
                                </td>
                                <td><span style="color:#64748b; font-size:12px;">{{ $log->created_at ? $log->created_at->format('M d, Y h:i A') : 'N/A' }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align:center; padding: 40px 10px; color:#94a3b8;">
                                    <i class="fas fa-info-circle mr-1" style="font-size:18px;"></i> No login attempt logs recorded yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="pagination-wrapper">
                {{ $loginLogs->appends(request()->except('login_page'))->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
// Simple Tab Switching Logic
function switchTab(evt, tabId) {
    // Hide all tab contents
    const tabContents = document.getElementsByClassName("sa-tab-content");
    for (let i = 0; i < tabContents.length; i++) {
        tabContents[i].classList.remove("active");
    }

    // Deactivate all tab buttons
    const tabBtns = document.getElementsByClassName("sa-tab-btn");
    for (let i = 0; i < tabBtns.length; i++) {
        tabBtns[i].classList.remove("active");
    }

    // Show selected tab content and active current button
    document.getElementById(tabId).classList.add("active");
    evt.currentTarget.classList.add("active");

    // Persist active tab in session storage so reload stays on the same tab
    sessionStorage.setItem('active_audit_tab', tabId);
}

// Restore active tab after page reload (useful when navigating pagination pages)
document.addEventListener("DOMContentLoaded", function() {
    const activeTab = sessionStorage.getItem('active_audit_tab');
    if (activeTab && document.getElementById(activeTab)) {
        // Trigger click on corresponding tab button
        const buttons = document.getElementsByClassName("sa-tab-btn");
        for (let i = 0; i < buttons.length; i++) {
            if (buttons[i].getAttribute('onclick').includes(activeTab)) {
                buttons[i].click();
                break;
            }
        }
    }
    
    // Also, if page URL has 'login_page' query parameter, switch to login tab automatically
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('login_page')) {
        const buttons = document.getElementsByClassName("sa-tab-btn");
        for (let i = 0; i < buttons.length; i++) {
            if (buttons[i].getAttribute('onclick').includes('login-tab')) {
                buttons[i].click();
                break;
            }
        }
    }
});
</script>
@endsection
