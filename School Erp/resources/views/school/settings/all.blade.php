@extends('layouts.app')

@section('page-title', 'All Settings')

@section('content')

<style>
    :root {
        --purple-primary: #6366f1;
        --purple-hover: #4f46e5;
        --teal-accent: #14b8a6;
        --emerald-glow: #10b981;
        --rose-glow: #f43f5e;
        --card-bg: #ffffff;
        --border-color: #e2e8f0;
        --text-main: #1e293b;
        --text-muted: #64748b;
    }

    .settings-container {
        padding: 4px 0 80px 0;
    }

    /* Header Bar */
    .settings-hdr-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 20px 24px;
        margin-bottom: 24px;
        box-shadow: 0 4px 20px -4px rgba(0, 0, 0, 0.05);
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }

    .settings-hdr-title {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .settings-hdr-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        box-shadow: 0 6px 16px -4px rgba(99, 102, 241, 0.4);
    }

    .settings-hdr-text h1 {
        font-size: 22px;
        font-weight: 700;
        color: var(--text-main);
        margin: 0;
    }

    .settings-hdr-text p {
        font-size: 13px;
        color: var(--text-muted);
        margin: 2px 0 0 0;
    }

    .settings-filter-bar {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .search-box {
        position: relative;
        min-width: 260px;
    }

    .search-box i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        font-size: 14px;
    }

    .search-box input {
        width: 100%;
        padding: 9px 14px 9px 38px;
        border-radius: 10px;
        border: 1px solid var(--border-color);
        background: #ffffff;
        font-size: 13px;
        transition: all 0.2s ease;
    }

    .search-box input:focus {
        outline: none;
        border-color: var(--purple-primary);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
    }

    .select-category-filter {
        padding: 9px 14px;
        border-radius: 10px;
        border: 1px solid var(--border-color);
        background: #ffffff;
        font-size: 13px;
        color: var(--text-main);
        font-weight: 500;
    }

    /* Category Nav Pills */
    .nav-pills-scroll {
        display: flex;
        align-items: center;
        gap: 8px;
        overflow-x: auto;
        padding-bottom: 12px;
        margin-bottom: 24px;
        scrollbar-width: thin;
    }

    .nav-pill-item {
        padding: 7px 15px;
        border-radius: 20px;
        background: #ffffff;
        border: 1px solid var(--border-color);
        font-size: 12px;
        font-weight: 600;
        color: var(--text-muted);
        white-space: nowrap;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .nav-pill-item:hover, .nav-pill-item.active {
        background: var(--purple-primary);
        color: #ffffff;
        border-color: var(--purple-primary);
        box-shadow: 0 4px 12px -2px rgba(99, 102, 241, 0.35);
    }

    /* Setting Card Section */
    .setting-card {
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: 16px;
        margin-bottom: 24px;
        box-shadow: 0 4px 16px -4px rgba(0, 0, 0, 0.03);
        overflow: hidden;
        transition: all 0.2s ease;
    }

    .setting-card:hover {
        box-shadow: 0 6px 24px -4px rgba(0, 0, 0, 0.06);
    }

    .setting-card-header {
        padding: 16px 24px;
        background: #f8fafc;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .setting-card-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 16px;
        font-weight: 700;
        color: var(--text-main);
        margin: 0;
    }

    .setting-card-title i {
        font-size: 17px;
    }

    .setting-card-body {
        padding: 20px 24px;
    }

    /* Individual Setting Item Row */
    .setting-row {
        padding: 16px 0;
        border-bottom: 1px dashed #edf2f7;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }

    .setting-row:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .setting-row:first-child {
        padding-top: 0;
    }

    .setting-label-box {
        flex: 1;
        min-width: 260px;
    }

    .setting-title {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-main);
        margin: 0 0 3px 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .setting-desc {
        font-size: 12px;
        color: var(--text-muted);
        margin: 0;
        line-height: 1.4;
    }

    .setting-control-box {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    /* Premium Animated ON/OFF Toggle Switch */
    .toggle-switch-group {
        display: inline-flex;
        align-items: center;
        background: #f1f5f9;
        padding: 3px;
        border-radius: 30px;
        border: 1px solid #e2e8f0;
    }

    .toggle-btn {
        padding: 6px 16px;
        border-radius: 24px;
        border: none;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        gap: 6px;
        background: transparent;
        color: var(--text-muted);
    }

    .toggle-btn.btn-on.active {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff;
        box-shadow: 0 3px 10px -2px rgba(16, 185, 129, 0.5);
    }

    .toggle-btn.btn-off.active {
        background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%);
        color: #ffffff;
        box-shadow: 0 3px 10px -2px rgba(244, 63, 94, 0.5);
    }

    .toggle-btn i {
        font-size: 13px;
    }

    /* Multi-state Radio Button Groups */
    .multi-radio-group {
        display: inline-flex;
        flex-wrap: wrap;
        gap: 6px;
        background: #f8fafc;
        padding: 4px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
    }

    .multi-radio-btn {
        padding: 7px 14px;
        border-radius: 8px;
        border: none;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        background: transparent;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .multi-radio-btn.active-green {
        background: #10b981;
        color: #ffffff;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
    }

    .multi-radio-btn.active-red {
        background: #f43f5e;
        color: #ffffff;
        box-shadow: 0 2px 8px rgba(244, 63, 94, 0.3);
    }

    .multi-radio-btn.active-blue {
        background: #3b82f6;
        color: #ffffff;
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
    }

    /* Standard Form Inputs in Settings */
    .setting-input, .setting-select {
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        font-size: 13px;
        background: #ffffff;
        color: var(--text-main);
        min-width: 180px;
    }

    .setting-input:focus, .setting-select:focus {
        outline: none;
        border-color: var(--purple-primary);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
    }

    /* Tag Helper Buttons for Template Editing */
    .tag-buttons-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 8px;
    }

    .btn-insert-tag {
        padding: 4px 10px;
        border-radius: 6px;
        border: 1px solid #cbd5e1;
        background: #f1f5f9;
        color: #475569;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .btn-insert-tag:hover {
        background: var(--purple-primary);
        color: #ffffff;
        border-color: var(--purple-primary);
    }

    /* Sticky Bottom Floating Save Bar */
    .sticky-save-bar {
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        background: #1e293b;
        color: #ffffff;
        padding: 12px 24px;
        border-radius: 50px;
        box-shadow: 0 10px 30px -5px rgba(15, 23, 42, 0.4);
        display: flex;
        align-items: center;
        gap: 16px;
        z-index: 1050;
        backdrop-filter: blur(8px);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .sticky-save-bar .changes-badge {
        background: #6366f1;
        color: #ffffff;
        font-size: 11px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 12px;
    }

    .btn-save-settings {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff;
        border: none;
        padding: 9px 24px;
        border-radius: 30px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.4);
        transition: all 0.2s ease;
    }

    .btn-save-settings:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(16, 185, 129, 0.5);
    }

    /* Toast Notification Overlay */
    .toast-overlay {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1100;
    }
</style>

<div class="settings-container">
    <!-- Header Card -->
    <div class="settings-hdr-card">
        <div class="settings-hdr-title">
            <div class="settings-hdr-icon">
                <i class="fas fa-sliders-h"></i>
            </div>
            <div class="settings-hdr-text">
                <h1>All Settings</h1>
                <p>Centralized Enterprise Configuration Panel & System Module Controls</p>
            </div>
        </div>

        <div class="settings-filter-bar">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="settingSearchInput" placeholder="Search settings (e.g. Attendance, Invoice, Report)...">
            </div>

            <select id="settingCategoryFilter" class="select-category-filter">
                <option value="all">All Categories</option>
                <option value="school_config">School & Academic</option>
                <option value="user_control">User & Portals</option>
                <option value="attendance">Attendance</option>
                <option value="leave">Leave Management</option>
                <option value="fee">Fee & Receipts</option>
                <option value="notification">Notifications</option>
                <option value="director">Director / Executive</option>
                <option value="transport">Transport</option>
                <option value="timetable">Timetable</option>
                <option value="exam">Examinations</option>
                <option value="homework">Homework & Assignments</option>
                <option value="ai_assistant">AI Assistant</option>
                <option value="security">Security & Audit</option>
                <option value="system">System & Automation</option>
            </select>
        </div>
    </div>

    <!-- Category Nav Pills -->
    <div class="nav-pills-scroll">
        <div class="nav-pill-item active" data-cat="all"><i class="fas fa-th-large"></i> All Settings</div>
        <div class="nav-pill-item" data-cat="school_config"><i class="fas fa-school"></i> School Setup</div>
        <div class="nav-pill-item" data-cat="user_control"><i class="fas fa-user-shield"></i> User Access</div>
        <div class="nav-pill-item" data-cat="attendance"><i class="fas fa-user-check"></i> Attendance</div>
        <div class="nav-pill-item" data-cat="leave"><i class="fas fa-calendar-minus"></i> Leave</div>
        <div class="nav-pill-item" data-cat="fee"><i class="fas fa-file-invoice-dollar"></i> Fee Controls</div>
        <div class="nav-pill-item" data-cat="notification"><i class="fas fa-bell"></i> Notifications</div>
        <div class="nav-pill-item" data-cat="director"><i class="fas fa-user-tie"></i> Director Reports</div>
        <div class="nav-pill-item" data-cat="transport"><i class="fas fa-bus"></i> Transport</div>
        <div class="nav-pill-item" data-cat="timetable"><i class="fas fa-clock"></i> Timetable</div>
        <div class="nav-pill-item" data-cat="exam"><i class="fas fa-file-alt"></i> Examinations</div>
        <div class="nav-pill-item" data-cat="homework"><i class="fas fa-book"></i> Homework</div>
        <div class="nav-pill-item" data-cat="ai_assistant"><i class="fas fa-robot"></i> AI Assistant</div>
        <div class="nav-pill-item" data-cat="security"><i class="fas fa-lock"></i> Security</div>
    </div>

    <!-- Settings Form -->
    <form id="allSettingsForm" method="POST" action="{{ route('school.settings.all.update') }}">
        @csrf


        <!-- 1. School Configuration Card -->
        <div class="setting-card" data-category="school_config">
            <div class="setting-card-header">
                <h2 class="setting-card-title"><i class="fas fa-school" style="color:#6366f1;"></i> School & Academic Setup</h2>
                <span class="badge bg-primary">School Core</span>
            </div>
            <div class="setting-card-body">
                <div class="setting-row">
                    <div class="setting-label-box">
                        <div class="setting-title">School Name</div>
                        <div class="setting-desc">Official registered name of the educational institute</div>
                    </div>
                    <div class="setting-control-box">
                        <input type="text" name="school_name" class="setting-input" style="width:280px;" value="{{ $settings['school_name'] }}">
                    </div>
                </div>

                <div class="setting-row">
                    <div class="setting-label-box">
                        <div class="setting-title">Active Academic Session</div>
                        <div class="setting-desc">Current session used across all admissions and academic records</div>
                    </div>
                    <div class="setting-control-box">
                        <select name="active_session" class="setting-select">
                            @foreach($sessions as $sess)
                                <option value="{{ $sess->name }}" {{ ($currentSession && $currentSession->id == $sess->id) ? 'selected' : '' }}>{{ $sess->name }} {{ $sess->is_current ? '★ Active' : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="setting-row">
                    <div class="setting-label-box">
                        <div class="setting-title">Issue Prospectus to Enquiry</div>
                        <div class="setting-desc">Enable issuing admission prospectus during student lead registration</div>
                    </div>
                    <div class="setting-control-box">
                        <input type="hidden" name="prospectus_issue" id="val_prospectus_issue" value="{{ $settings['prospectus_issue'] }}">
                        <div class="toggle-switch-group">
                            <button type="button" class="toggle-btn btn-on {{ $settings['prospectus_issue'] == '1' ? 'active' : '' }}" onclick="setToggle('prospectus_issue', '1', this)"><i class="fas fa-power-off"></i> Turn On</button>
                            <button type="button" class="toggle-btn btn-off {{ $settings['prospectus_issue'] == '0' ? 'active' : '' }}" onclick="setToggle('prospectus_issue', '0', this)"><i class="fas fa-power-off"></i> Turn Off</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. User & Portal Access Controls Card -->
        <div class="setting-card" data-category="user_control">
            <div class="setting-card-header">
                <h2 class="setting-card-title"><i class="fas fa-user-shield" style="color:#10b981;"></i> User Management & Portal Controls</h2>
                <span class="badge bg-success">Access Control</span>
            </div>
            <div class="setting-card-body">
                <div class="setting-row">
                    <div class="setting-label-box">
                        <div class="setting-title">Allow Student Login</div>
                        <div class="setting-desc">Permit students to sign in to the Student Portal</div>
                    </div>
                    <div class="setting-control-box">
                        <input type="hidden" name="allow_student_login" id="val_allow_student_login" value="{{ $settings['allow_student_login'] }}">
                        <div class="toggle-switch-group">
                            <button type="button" class="toggle-btn btn-on {{ $settings['allow_student_login'] == '1' ? 'active' : '' }}" onclick="setToggle('allow_student_login', '1', this)"><i class="fas fa-power-off"></i> Turn On</button>
                            <button type="button" class="toggle-btn btn-off {{ $settings['allow_student_login'] == '0' ? 'active' : '' }}" onclick="setToggle('allow_student_login', '0', this)"><i class="fas fa-power-off"></i> Turn Off</button>
                        </div>
                    </div>
                </div>

                <div class="setting-row">
                    <div class="setting-label-box">
                        <div class="setting-title">Allow Parent Login</div>
                        <div class="setting-desc">Permit parents to sign in to the Parent Portal and view ward progress</div>
                    </div>
                    <div class="setting-control-box">
                        <input type="hidden" name="allow_parent_login" id="val_allow_parent_login" value="{{ $settings['allow_parent_login'] }}">
                        <div class="toggle-switch-group">
                            <button type="button" class="toggle-btn btn-on {{ $settings['allow_parent_login'] == '1' ? 'active' : '' }}" onclick="setToggle('allow_parent_login', '1', this)"><i class="fas fa-power-off"></i> Turn On</button>
                            <button type="button" class="toggle-btn btn-off {{ $settings['allow_parent_login'] == '0' ? 'active' : '' }}" onclick="setToggle('allow_parent_login', '0', this)"><i class="fas fa-power-off"></i> Turn Off</button>
                        </div>
                    </div>
                </div>

                <div class="setting-row">
                    <div class="setting-label-box">
                        <div class="setting-title">Allow Staff / Teacher Login</div>
                        <div class="setting-desc">Enable staff login access for teachers and administrative personnel</div>
                    </div>
                    <div class="setting-control-box">
                        <input type="hidden" name="allow_staff_login" id="val_allow_staff_login" value="{{ $settings['allow_staff_login'] }}">
                        <div class="toggle-switch-group">
                            <button type="button" class="toggle-btn btn-on {{ $settings['allow_staff_login'] == '1' ? 'active' : '' }}" onclick="setToggle('allow_staff_login', '1', this)"><i class="fas fa-power-off"></i> Turn On</button>
                            <button type="button" class="toggle-btn btn-off {{ $settings['allow_staff_login'] == '0' ? 'active' : '' }}" onclick="setToggle('allow_staff_login', '0', this)"><i class="fas fa-power-off"></i> Turn Off</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Attendance Controls Card -->
        <div class="setting-card" data-category="attendance">
            <div class="setting-card-header">
                <h2 class="setting-card-title"><i class="fas fa-user-check" style="color:#06b6d4;"></i> Attendance Settings</h2>
                <span class="badge bg-info">Attendance Engine</span>
            </div>
            <div class="setting-card-body">
                <div class="setting-row">
                    <div class="setting-label-box">
                        <div class="setting-title">Present Notification Setting</div>
                        <div class="setting-desc">Automated message sent when student attendance is marked Present</div>
                    </div>
                    <div class="setting-control-box">
                        <input type="hidden" name="auto_notify_present" id="val_auto_notify_present" value="{{ $settings['auto_notify_present'] }}">
                        <div class="multi-radio-group">
                            <button type="button" class="multi-radio-btn {{ $settings['auto_notify_present'] == 'off' ? 'active-red' : '' }}" onclick="setMultiRadio('auto_notify_present', 'off', this, 'active-red')"><i class="fas fa-power-off"></i> Turn Off</button>
                            <button type="button" class="multi-radio-btn {{ $settings['auto_notify_present'] == 'sms_app' ? 'active-blue' : '' }}" onclick="setMultiRadio('auto_notify_present', 'sms_app', this, 'active-blue')"><i class="fas fa-comments"></i> SMS + App</button>
                            <button type="button" class="multi-radio-btn {{ $settings['auto_notify_present'] == 'only_app' ? 'active-green' : '' }}" onclick="setMultiRadio('auto_notify_present', 'only_app', this, 'active-green')"><i class="fas fa-mobile-alt"></i> Only App</button>
                        </div>
                    </div>
                </div>

                <div class="setting-row">
                    <div class="setting-label-box">
                        <div class="setting-title">Absent Notification Setting</div>
                        <div class="setting-desc">Automated alert dispatched when student is marked Absent</div>
                    </div>
                    <div class="setting-control-box">
                        <input type="hidden" name="auto_notify_absent" id="val_auto_notify_absent" value="{{ $settings['auto_notify_absent'] }}">
                        <div class="multi-radio-group">
                            <button type="button" class="multi-radio-btn {{ $settings['auto_notify_absent'] == 'off' ? 'active-red' : '' }}" onclick="setMultiRadio('auto_notify_absent', 'off', this, 'active-red')"><i class="fas fa-power-off"></i> Turn Off</button>
                            <button type="button" class="multi-radio-btn {{ $settings['auto_notify_absent'] == 'sms_app' ? 'active-blue' : '' }}" onclick="setMultiRadio('auto_notify_absent', 'sms_app', this, 'active-blue')"><i class="fas fa-comments"></i> SMS + App</button>
                            <button type="button" class="multi-radio-btn {{ $settings['auto_notify_absent'] == 'only_app' ? 'active-green' : '' }}" onclick="setMultiRadio('auto_notify_absent', 'only_app', this, 'active-green')"><i class="fas fa-mobile-alt"></i> Only App</button>
                        </div>
                    </div>
                </div>

                <div class="setting-row">
                    <div class="setting-label-box">
                        <div class="setting-title">Absent Staff Notification Setting</div>
                        <div class="setting-desc">Alert administrator when teacher or staff member is absent</div>
                    </div>
                    <div class="setting-control-box">
                        <input type="hidden" name="absent_staff_notification" id="val_absent_staff_notification" value="{{ $settings['absent_staff_notification'] }}">
                        <div class="multi-radio-group">
                            <button type="button" class="multi-radio-btn {{ $settings['absent_staff_notification'] == 'off' ? 'active-red' : '' }}" onclick="setMultiRadio('absent_staff_notification', 'off', this, 'active-red')"><i class="fas fa-power-off"></i> Turn Off</button>
                            <button type="button" class="multi-radio-btn {{ $settings['absent_staff_notification'] == 'sms_app' ? 'active-blue' : '' }}" onclick="setMultiRadio('absent_staff_notification', 'sms_app', this, 'active-blue')"><i class="fas fa-comments"></i> SMS + App</button>
                            <button type="button" class="multi-radio-btn {{ $settings['absent_staff_notification'] == 'only_app' ? 'active-green' : '' }}" onclick="setMultiRadio('absent_staff_notification', 'only_app', this, 'active-green')"><i class="fas fa-mobile-alt"></i> Only App</button>
                        </div>
                    </div>
                </div>

                <div class="setting-row">
                    <div class="setting-label-box">
                        <div class="setting-title">Skip Hostel Student Absent Notification</div>
                        <div class="setting-desc">Turn ON setting to skip sending absent notification for hostel students</div>
                    </div>
                    <div class="setting-control-box">
                        <input type="hidden" name="skip_hostel_student_absent" id="val_skip_hostel_student_absent" value="{{ $settings['skip_hostel_student_absent'] }}">
                        <div class="toggle-switch-group">
                            <button type="button" class="toggle-btn btn-on {{ $settings['skip_hostel_student_absent'] == '1' ? 'active' : '' }}" onclick="setToggle('skip_hostel_student_absent', '1', this)"><i class="fas fa-power-off"></i> Turn On</button>
                            <button type="button" class="toggle-btn btn-off {{ $settings['skip_hostel_student_absent'] == '0' ? 'active' : '' }}" onclick="setToggle('skip_hostel_student_absent', '0', this)"><i class="fas fa-power-off"></i> Turn Off</button>
                        </div>
                    </div>
                </div>

                <div class="setting-row" style="flex-direction:column;align-items:flex-start;">
                    <div class="setting-label-box" style="width:100%;">
                        <div class="setting-title">Custom Absent Message Template</div>
                        <div class="setting-desc">Format for daily absent notifications. Click tag buttons to insert dynamic placeholders.</div>
                    </div>
                    <div style="width:100%;margin-top:10px;">
                        <textarea id="custom_absent_message_input" name="custom_absent_message" class="setting-input" style="width:100%;min-height:75px;font-family:monospace;font-size:12px;">{{ $settings['custom_absent_message'] }}</textarea>
                        <div class="tag-buttons-bar">
                            <button type="button" class="btn-insert-tag" onclick="insertTag('::name::')">ENTER STUDENT NAME</button>
                            <button type="button" class="btn-insert-tag" onclick="insertTag('::father::')">ENTER FATHER NAME</button>
                            <button type="button" class="btn-insert-tag" onclick="insertTag('::today::')">ENTER ABSENT DATE</button>
                            <button type="button" class="btn-insert-tag" onclick="insertTag('::sr_no::')">ENTER SR NO</button>
                            <button type="button" class="btn-insert-tag" onclick="insertTag('::roll_no::')">ENTER ROLL NO</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Leave Management Controls Card -->
        <div class="setting-card" data-category="leave">
            <div class="setting-card-header">
                <h2 class="setting-card-title"><i class="fas fa-calendar-minus" style="color:#f59e0b;"></i> Leave Management Settings</h2>
                <span class="badge bg-warning text-dark">Leave Portal</span>
            </div>
            <div class="setting-card-body">
                <div class="setting-row">
                    <div class="setting-label-box">
                        <div class="setting-title">Allow Student Leave Application</div>
                        <div class="setting-desc">Permit students/parents to submit online leave applications</div>
                    </div>
                    <div class="setting-control-box">
                        <input type="hidden" name="allow_student_leave" id="val_allow_student_leave" value="{{ $settings['allow_student_leave'] }}">
                        <div class="toggle-switch-group">
                            <button type="button" class="toggle-btn btn-on {{ $settings['allow_student_leave'] == '1' ? 'active' : '' }}" onclick="setToggle('allow_student_leave', '1', this)"><i class="fas fa-power-off"></i> Turn On</button>
                            <button type="button" class="toggle-btn btn-off {{ $settings['allow_student_leave'] == '0' ? 'active' : '' }}" onclick="setToggle('allow_student_leave', '0', this)"><i class="fas fa-power-off"></i> Turn Off</button>
                        </div>
                    </div>
                </div>

                <div class="setting-row">
                    <div class="setting-label-box">
                        <div class="setting-title">Allow Staff Leave Application</div>
                        <div class="setting-desc">Enable teachers and staff members to apply for leave online</div>
                    </div>
                    <div class="setting-control-box">
                        <input type="hidden" name="allow_staff_leave" id="val_allow_staff_leave" value="{{ $settings['allow_staff_leave'] }}">
                        <div class="toggle-switch-group">
                            <button type="button" class="toggle-btn btn-on {{ $settings['allow_staff_leave'] == '1' ? 'active' : '' }}" onclick="setToggle('allow_staff_leave', '1', this)"><i class="fas fa-power-off"></i> Turn On</button>
                            <button type="button" class="toggle-btn btn-off {{ $settings['allow_staff_leave'] == '0' ? 'active' : '' }}" onclick="setToggle('allow_staff_leave', '0', this)"><i class="fas fa-power-off"></i> Turn Off</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Fee Management Card -->
        <div class="setting-card" data-category="fee">
            <div class="setting-card-header">
                <h2 class="setting-card-title"><i class="fas fa-file-invoice-dollar" style="color:#8b5cf6;"></i> Fee Management & Receipt Settings</h2>
                <span class="badge bg-indigo text-white" style="background:#8b5cf6;">Billing & Invoicing</span>
            </div>
            <div class="setting-card-body">
                <div class="setting-row">
                    <div class="setting-label-box">
                        <div class="setting-title">Reset Invoice No for New Financial Year</div>
                        <div class="setting-desc">Controls fee invoice number sequence behavior</div>
                    </div>
                    <div class="setting-control-box">
                        <input type="hidden" name="reset_invoice_no" id="val_reset_invoice_no" value="{{ $settings['reset_invoice_no'] }}">
                        <div class="multi-radio-group">
                            <button type="button" class="multi-radio-btn {{ $settings['reset_invoice_no'] == 'session' ? 'active-red' : '' }}" onclick="setMultiRadio('reset_invoice_no', 'session', this, 'active-red')"><i class="fas fa-power-off"></i> Change on session basis</button>
                            <button type="button" class="multi-radio-btn {{ $settings['reset_invoice_no'] == 'fin_year' ? 'active-green' : '' }}" onclick="setMultiRadio('reset_invoice_no', 'fin_year', this, 'active-green')"><i class="fas fa-power-off"></i> Change on fin. year basis</button>
                            <button type="button" class="multi-radio-btn {{ $settings['reset_invoice_no'] == 'continuous' ? 'active-blue' : '' }}" onclick="setMultiRadio('reset_invoice_no', 'continuous', this, 'active-blue')"><i class="fas fa-power-off"></i> Keep continuous</button>
                        </div>
                    </div>
                </div>

                <div class="setting-row">
                    <div class="setting-label-box">
                        <div class="setting-title">Reset Challan No</div>
                        <div class="setting-desc">Sequence rule for fee challan generation</div>
                    </div>
                    <div class="setting-control-box">
                        <input type="hidden" name="reset_challan_no" id="val_reset_challan_no" value="{{ $settings['reset_challan_no'] }}">
                        <div class="multi-radio-group">
                            <button type="button" class="multi-radio-btn {{ $settings['reset_challan_no'] == 'session' ? 'active-red' : '' }}" onclick="setMultiRadio('reset_challan_no', 'session', this, 'active-red')"><i class="fas fa-power-off"></i> Change on session basis</button>
                            <button type="button" class="multi-radio-btn {{ $settings['reset_challan_no'] == 'fin_year' ? 'active-green' : '' }}" onclick="setMultiRadio('reset_challan_no', 'fin_year', this, 'active-green')"><i class="fas fa-power-off"></i> Change on fin. year basis</button>
                            <button type="button" class="multi-radio-btn {{ $settings['reset_challan_no'] == 'continuous' ? 'active-blue' : '' }}" onclick="setMultiRadio('reset_challan_no', 'continuous', this, 'active-blue')"><i class="fas fa-power-off"></i> Keep continuous</button>
                        </div>
                    </div>
                </div>

                <div class="setting-row">
                    <div class="setting-label-box">
                        <div class="setting-title">Reset SR No</div>
                        <div class="setting-desc">Student SR / Admission Number sequence rule</div>
                    </div>
                    <div class="setting-control-box">
                        <input type="hidden" name="reset_sr_no" id="val_reset_sr_no" value="{{ $settings['reset_sr_no'] }}">
                        <div class="multi-radio-group">
                            <button type="button" class="multi-radio-btn {{ $settings['reset_sr_no'] == 'session' ? 'active-red' : '' }}" onclick="setMultiRadio('reset_sr_no', 'session', this, 'active-red')"><i class="fas fa-power-off"></i> Change on session basis</button>
                            <button type="button" class="multi-radio-btn {{ $settings['reset_sr_no'] == 'continuous' ? 'active-blue' : '' }}" onclick="setMultiRadio('reset_sr_no', 'continuous', this, 'active-blue')"><i class="fas fa-power-off"></i> Keep continuous</button>
                        </div>
                    </div>
                </div>

                <div class="setting-row">
                    <div class="setting-label-box">
                        <div class="setting-title">Sync Student SR No All Session</div>
                        <div class="setting-desc">When student SR number is updated, auto sync across active sessions</div>
                    </div>
                    <div class="setting-control-box">
                        <input type="hidden" name="sync_sr_no_all_session" id="val_sync_sr_no_all_session" value="{{ $settings['sync_sr_no_all_session'] }}">
                        <div class="toggle-switch-group">
                            <button type="button" class="toggle-btn btn-on {{ $settings['sync_sr_no_all_session'] == '1' ? 'active' : '' }}" onclick="setToggle('sync_sr_no_all_session', '1', this)"><i class="fas fa-power-off"></i> Turn On</button>
                            <button type="button" class="toggle-btn btn-off {{ $settings['sync_sr_no_all_session'] == '0' ? 'active' : '' }}" onclick="setToggle('sync_sr_no_all_session', '0', this)"><i class="fas fa-power-off"></i> Turn Off</button>
                        </div>
                    </div>
                </div>

                <div class="setting-row">
                    <div class="setting-label-box">
                        <div class="setting-title">Auto Receipt Generate on Online Payment Success</div>
                        <div class="setting-desc">Automatically issue fee receipt upon payment gateway transaction success</div>
                    </div>
                    <div class="setting-control-box">
                        <input type="hidden" name="auto_receipt_online_payment" id="val_auto_receipt_online_payment" value="{{ $settings['auto_receipt_online_payment'] }}">
                        <div class="toggle-switch-group">
                            <button type="button" class="toggle-btn btn-on {{ $settings['auto_receipt_online_payment'] == '1' ? 'active' : '' }}" onclick="setToggle('auto_receipt_online_payment', '1', this)"><i class="fas fa-power-off"></i> Turn On</button>
                            <button type="button" class="toggle-btn btn-off {{ $settings['auto_receipt_online_payment'] == '0' ? 'active' : '' }}" onclick="setToggle('auto_receipt_online_payment', '0', this)"><i class="fas fa-power-off"></i> Turn Off</button>
                        </div>
                    </div>
                </div>

                <div class="setting-row">
                    <div class="setting-label-box">
                        <div class="setting-title">Auto Generate Payment for Challan</div>
                        <div class="setting-desc">Automatic create payment record when fee challan is issued</div>
                    </div>
                    <div class="setting-control-box">
                        <input type="hidden" name="auto_generate_payment_challan" id="val_auto_generate_payment_challan" value="{{ $settings['auto_generate_payment_challan'] }}">
                        <div class="toggle-switch-group">
                            <button type="button" class="toggle-btn btn-on {{ $settings['auto_generate_payment_challan'] == '1' ? 'active' : '' }}" onclick="setToggle('auto_generate_payment_challan', '1', this)"><i class="fas fa-power-off"></i> Turn On</button>
                            <button type="button" class="toggle-btn btn-off {{ $settings['auto_generate_payment_challan'] == '0' ? 'active' : '' }}" onclick="setToggle('auto_generate_payment_challan', '0', this)"><i class="fas fa-power-off"></i> Turn Off</button>
                        </div>
                    </div>
                </div>

                <div class="setting-row">
                    <div class="setting-label-box">
                        <div class="setting-title">Block Expense Voucher Payment Back Date</div>
                        <div class="setting-desc">Prevent entering expense vouchers or payments with back dates</div>
                    </div>
                    <div class="setting-control-box">
                        <input type="hidden" name="block_expense_voucher_back_date" id="val_block_expense_voucher_back_date" value="{{ $settings['block_expense_voucher_back_date'] }}">
                        <div class="toggle-switch-group">
                            <button type="button" class="toggle-btn btn-on {{ $settings['block_expense_voucher_back_date'] == '1' ? 'active' : '' }}" onclick="setToggle('block_expense_voucher_back_date', '1', this)"><i class="fas fa-power-off"></i> Turn On</button>
                            <button type="button" class="toggle-btn btn-off {{ $settings['block_expense_voucher_back_date'] == '0' ? 'active' : '' }}" onclick="setToggle('block_expense_voucher_back_date', '0', this)"><i class="fas fa-power-off"></i> Turn Off</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. Notifications Settings Card -->
        <div class="setting-card" data-category="notification">
            <div class="setting-card-header">
                <h2 class="setting-card-title"><i class="fas fa-bell" style="color:#ec4899;"></i> Automatic Notifications</h2>
                <span class="badge bg-danger">Messaging</span>
            </div>
            <div class="setting-card-body">
                <div class="setting-row">
                    <div class="setting-label-box">
                        <div class="setting-title">Student Fees Notification</div>
                        <div class="setting-desc">Auto dispatch fee due alerts to parents</div>
                    </div>
                    <div class="setting-control-box">
                        <input type="hidden" name="student_fees_notification" id="val_student_fees_notification" value="{{ $settings['student_fees_notification'] }}">
                        <div class="multi-radio-group">
                            <button type="button" class="multi-radio-btn {{ $settings['student_fees_notification'] == 'off' ? 'active-red' : '' }}" onclick="setMultiRadio('student_fees_notification', 'off', this, 'active-red')"><i class="fas fa-power-off"></i> Turn Off</button>
                            <button type="button" class="multi-radio-btn {{ $settings['student_fees_notification'] == 'sms_app' ? 'active-blue' : '' }}" onclick="setMultiRadio('student_fees_notification', 'sms_app', this, 'active-blue')"><i class="fas fa-comments"></i> SMS + App</button>
                            <button type="button" class="multi-radio-btn {{ $settings['student_fees_notification'] == 'only_app' ? 'active-green' : '' }}" onclick="setMultiRadio('student_fees_notification', 'only_app', this, 'active-green')"><i class="fas fa-mobile-alt"></i> Only App</button>
                        </div>
                    </div>
                </div>

                <div class="setting-row">
                    <div class="setting-label-box">
                        <div class="setting-title">Auto Notify Holidays</div>
                        <div class="setting-desc">Send automated holiday notifications to all active students and staff</div>
                    </div>
                    <div class="setting-control-box">
                        <input type="hidden" name="auto_notify_holidays" id="val_auto_notify_holidays" value="{{ $settings['auto_notify_holidays'] }}">
                        <div class="multi-radio-group">
                            <button type="button" class="multi-radio-btn {{ $settings['auto_notify_holidays'] == 'off' ? 'active-red' : '' }}" onclick="setMultiRadio('auto_notify_holidays', 'off', this, 'active-red')"><i class="fas fa-power-off"></i> Turn Off</button>
                            <button type="button" class="multi-radio-btn {{ $settings['auto_notify_holidays'] == 'sms_app' ? 'active-blue' : '' }}" onclick="setMultiRadio('auto_notify_holidays', 'sms_app', this, 'active-blue')"><i class="fas fa-comments"></i> SMS + App</button>
                            <button type="button" class="multi-radio-btn {{ $settings['auto_notify_holidays'] == 'only_app' ? 'active-green' : '' }}" onclick="setMultiRadio('auto_notify_holidays', 'only_app', this, 'active-green')"><i class="fas fa-mobile-alt"></i> Only App</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 7. Director Settings Card -->
        <div class="setting-card" data-category="director">
            <div class="setting-card-header">
                <h2 class="setting-card-title"><i class="fas fa-user-tie" style="color:#6366f1;"></i> Director / Executive Settings</h2>
                <span class="badge bg-secondary">Executive Summary</span>
            </div>
            <div class="setting-card-body">
                <div class="setting-row">
                    <div class="setting-label-box">
                        <div class="setting-title">Director Daily Report (BETA)</div>
                        <div class="setting-desc">Daily Report Notification will be sent to director</div>
                    </div>
                    <div class="setting-control-box">
                        <div style="display:flex;align-items:center;gap:20px;">
                            <div>
                                <span style="font-size:12px;font-weight:600;display:block;margin-bottom:4px;">Status</span>
                                <input type="hidden" name="director_daily_report" id="val_director_daily_report" value="{{ $settings['director_daily_report'] }}">
                                <div class="toggle-switch-group">
                                    <button type="button" class="toggle-btn btn-on {{ $settings['director_daily_report'] == '1' ? 'active' : '' }}" onclick="setToggle('director_daily_report', '1', this)">Yes</button>
                                    <button type="button" class="toggle-btn btn-off {{ $settings['director_daily_report'] == '0' ? 'active' : '' }}" onclick="setToggle('director_daily_report', '0', this)">No</button>
                                </div>
                            </div>
                            <div>
                                <span style="font-size:12px;font-weight:600;display:block;margin-bottom:4px;">Get Report on SMS</span>
                                <input type="hidden" name="director_report_sms" id="val_director_report_sms" value="{{ $settings['director_report_sms'] }}">
                                <div class="toggle-switch-group">
                                    <button type="button" class="toggle-btn btn-on {{ $settings['director_report_sms'] == '1' ? 'active' : '' }}" onclick="setToggle('director_report_sms', '1', this)">Yes</button>
                                    <button type="button" class="toggle-btn btn-off {{ $settings['director_report_sms'] == '0' ? 'active' : '' }}" onclick="setToggle('director_report_sms', '0', this)">No</button>
                                </div>
                            </div>
                            <div>
                                <span style="font-size:12px;font-weight:600;display:block;margin-bottom:4px;">Get Report on Email</span>
                                <input type="hidden" name="director_report_email" id="val_director_report_email" value="{{ $settings['director_report_email'] }}">
                                <div class="toggle-switch-group">
                                    <button type="button" class="toggle-btn btn-on {{ $settings['director_report_email'] == '1' ? 'active' : '' }}" onclick="setToggle('director_report_email', '1', this)">Yes</button>
                                    <button type="button" class="toggle-btn btn-off {{ $settings['director_report_email'] == '0' ? 'active' : '' }}" onclick="setToggle('director_report_email', '0', this)">No</button>
                                </div>
                            </div>
                            <div>
                                <span style="font-size:12px;font-weight:600;display:block;margin-bottom:4px;">Timing</span>
                                <input type="time" name="director_report_time" class="setting-input" value="{{ $settings['director_report_time'] }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="setting-row">
                    <div class="setting-label-box">
                        <div class="setting-title">Director Email Addresses</div>
                        <div class="setting-desc">Emails for receiving executive daily summary reports</div>
                    </div>
                    <div class="setting-control-box">
                        <input type="text" name="director_emails" class="setting-input" style="width:300px;" value="{{ $settings['director_emails'] }}" placeholder="director@school.com">
                    </div>
                </div>

                <div class="setting-row">
                    <div class="setting-label-box">
                        <div class="setting-title">Permanent Slip Delete Contacts</div>
                        <div class="setting-desc">Following contacts will be used for slip delete OTP confirmation</div>
                    </div>
                    <div class="setting-control-box">
                        <input type="text" name="permanent_slip_delete_contacts" class="setting-input" style="width:300px;" value="{{ $settings['permanent_slip_delete_contacts'] }}" placeholder="10 digit mobile number">
                    </div>
                </div>

                <div class="setting-row">
                    <div class="setting-label-box">
                        <div class="setting-title">Edit Stock OTP Verification</div>
                        <div class="setting-desc">Require OTP confirmation before updating inventory stock</div>
                    </div>
                    <div class="setting-control-box">
                        <input type="hidden" name="edit_stock_otp_verification" id="val_edit_stock_otp_verification" value="{{ $settings['edit_stock_otp_verification'] }}">
                        <div class="toggle-switch-group">
                            <button type="button" class="toggle-btn btn-on {{ $settings['edit_stock_otp_verification'] == '1' ? 'active' : '' }}" onclick="setToggle('edit_stock_otp_verification', '1', this)"><i class="fas fa-power-off"></i> Turn On</button>
                            <button type="button" class="toggle-btn btn-off {{ $settings['edit_stock_otp_verification'] == '0' ? 'active' : '' }}" onclick="setToggle('edit_stock_otp_verification', '0', this)"><i class="fas fa-power-off"></i> Turn Off</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transport Settings Card -->
        <div class="setting-card" data-category="transport">
            <div class="setting-card-header">
                <h2 class="setting-card-title"><i class="fas fa-bus" style="color:#f59e0b;"></i> Transport Settings</h2>
                <span class="badge bg-warning text-dark">Transport & Route Logistics</span>
            </div>
            <div class="setting-card-body">
                <div class="setting-row">
                    <div class="setting-label-box">
                        <div class="setting-title">Show School Name on Transport Invoice</div>
                        <div class="setting-desc">Display official school name at the header of all Transport Invoices, Slips, Downloads, and Print views</div>
                    </div>
                    <div class="setting-control-box">
                        <input type="hidden" name="show_school_name_transport_invoice" id="val_show_school_name_transport_invoice" value="{{ $settings['show_school_name_transport_invoice'] ?? '1' }}">
                        <div class="toggle-switch-group">
                            <button type="button" class="toggle-btn btn-on {{ ($settings['show_school_name_transport_invoice'] ?? '1') == '1' ? 'active' : '' }}" onclick="setToggle('show_school_name_transport_invoice', '1', this)"><i class="fas fa-power-off"></i> Turn On</button>
                            <button type="button" class="toggle-btn btn-off {{ ($settings['show_school_name_transport_invoice'] ?? '1') == '0' ? 'active' : '' }}" onclick="setToggle('show_school_name_transport_invoice', '0', this)"><i class="fas fa-power-off"></i> Turn Off</button>
                        </div>
                    </div>
                </div>

                <div class="setting-row">
                    <div class="setting-label-box">
                        <div class="setting-title">Quarterly Transport Payment</div>
                        <div class="setting-desc">Restrict transport payment options ONLY to 3-month Quarterly payments (e.g. Apr-Jun, Jul-Sep). Monthly payment option will automatically disappear.</div>
                    </div>
                    <div class="setting-control-box">
                        <input type="hidden" name="quarterly_transport_payment" id="val_quarterly_transport_payment" value="{{ $settings['quarterly_transport_payment'] ?? '0' }}">
                        <div class="toggle-switch-group">
                            <button type="button" class="toggle-btn btn-on {{ ($settings['quarterly_transport_payment'] ?? '0') == '1' ? 'active' : '' }}" onclick="setToggle('quarterly_transport_payment', '1', this)"><i class="fas fa-power-off"></i> Turn On</button>
                            <button type="button" class="toggle-btn btn-off {{ ($settings['quarterly_transport_payment'] ?? '0') == '0' ? 'active' : '' }}" onclick="setToggle('quarterly_transport_payment', '0', this)"><i class="fas fa-power-off"></i> Turn Off</button>
                        </div>
                    </div>
                </div>

                <div class="setting-row">
                    <div class="setting-label-box">
                        <div class="setting-title">Show School Logo on Transport Invoice</div>
                        <div class="setting-desc">Render institute logo image on printed and exported Transport Fee Slips</div>
                    </div>
                    <div class="setting-control-box">
                        <input type="hidden" name="show_school_logo_transport_invoice" id="val_show_school_logo_transport_invoice" value="{{ $settings['show_school_logo_transport_invoice'] ?? '1' }}">
                        <div class="toggle-switch-group">
                            <button type="button" class="toggle-btn btn-on {{ ($settings['show_school_logo_transport_invoice'] ?? '1') == '1' ? 'active' : '' }}" onclick="setToggle('show_school_logo_transport_invoice', '1', this)"><i class="fas fa-power-off"></i> Turn On</button>
                            <button type="button" class="toggle-btn btn-off {{ ($settings['show_school_logo_transport_invoice'] ?? '1') == '0' ? 'active' : '' }}" onclick="setToggle('show_school_logo_transport_invoice', '0', this)"><i class="fas fa-power-off"></i> Turn Off</button>
                        </div>
                    </div>
                </div>

                <div class="setting-row">
                    <div class="setting-label-box">
                        <div class="setting-title">Show Route & Vehicle Details on Transport Slip</div>
                        <div class="setting-desc">Display Boarding Point, Pickup Point, Vehicle Number, and Driver contact info on transport slips</div>
                    </div>
                    <div class="setting-control-box">
                        <input type="hidden" name="show_route_vehicle_on_transport_invoice" id="val_show_route_vehicle_on_transport_invoice" value="{{ $settings['show_route_vehicle_on_transport_invoice'] ?? '1' }}">
                        <div class="toggle-switch-group">
                            <button type="button" class="toggle-btn btn-on {{ ($settings['show_route_vehicle_on_transport_invoice'] ?? '1') == '1' ? 'active' : '' }}" onclick="setToggle('show_route_vehicle_on_transport_invoice', '1', this)"><i class="fas fa-power-off"></i> Turn On</button>
                            <button type="button" class="toggle-btn btn-off {{ ($settings['show_route_vehicle_on_transport_invoice'] ?? '1') == '0' ? 'active' : '' }}" onclick="setToggle('show_route_vehicle_on_transport_invoice', '0', this)"><i class="fas fa-power-off"></i> Turn Off</button>
                        </div>
                    </div>
                </div>

                <div class="setting-row">
                    <div class="setting-label-box">
                        <div class="setting-title">Auto Calculate Transport Absentee Deduction</div>
                        <div class="setting-desc">Automatically calculate pro-rata daily attendance deduction and reduce transport invoice when student is absent on bus</div>
                    </div>
                    <div class="setting-control-box">
                        <input type="hidden" name="auto_transport_absent_deduction" id="val_auto_transport_absent_deduction" value="{{ $settings['auto_transport_absent_deduction'] ?? '1' }}">
                        <div class="toggle-switch-group">
                            <button type="button" class="toggle-btn btn-on {{ ($settings['auto_transport_absent_deduction'] ?? '1') == '1' ? 'active' : '' }}" onclick="setToggle('auto_transport_absent_deduction', '1', this)"><i class="fas fa-power-off"></i> Turn On</button>
                            <button type="button" class="toggle-btn btn-off {{ ($settings['auto_transport_absent_deduction'] ?? '1') == '0' ? 'active' : '' }}" onclick="setToggle('auto_transport_absent_deduction', '0', this)"><i class="fas fa-power-off"></i> Turn Off</button>
                        </div>
                    </div>
                </div>

                <div class="setting-row">
                    <div class="setting-label-box">
                        <div class="setting-title">Allow Advance Transport Payment</div>
                        <div class="setting-desc">Allow parents and administrative fee collection staff to pay upcoming future transport installments in advance</div>
                    </div>
                    <div class="setting-control-box">
                        <input type="hidden" name="allow_advance_transport_payment" id="val_allow_advance_transport_payment" value="{{ $settings['allow_advance_transport_payment'] ?? '1' }}">
                        <div class="toggle-switch-group">
                            <button type="button" class="toggle-btn btn-on {{ ($settings['allow_advance_transport_payment'] ?? '1') == '1' ? 'active' : '' }}" onclick="setToggle('allow_advance_transport_payment', '1', this)"><i class="fas fa-power-off"></i> Turn On</button>
                            <button type="button" class="toggle-btn btn-off {{ ($settings['allow_advance_transport_payment'] ?? '1') == '0' ? 'active' : '' }}" onclick="setToggle('allow_advance_transport_payment', '0', this)"><i class="fas fa-power-off"></i> Turn Off</button>
                        </div>
                    </div>
                </div>

                <div class="setting-row">
                    <div class="setting-label-box">
                        <div class="setting-title">Lock Partial Transport Payment</div>
                        <div class="setting-desc">Disallow partial payments for transport fees (require full installment or full quarterly amount payment)</div>
                    </div>
                    <div class="setting-control-box">
                        <input type="hidden" name="lock_partial_transport_payment" id="val_lock_partial_transport_payment" value="{{ $settings['lock_partial_transport_payment'] ?? '0' }}">
                        <div class="toggle-switch-group">
                            <button type="button" class="toggle-btn btn-on {{ ($settings['lock_partial_transport_payment'] ?? '0') == '1' ? 'active' : '' }}" onclick="setToggle('lock_partial_transport_payment', '1', this)"><i class="fas fa-power-off"></i> Turn On</button>
                            <button type="button" class="toggle-btn btn-off {{ ($settings['lock_partial_transport_payment'] ?? '0') == '0' ? 'active' : '' }}" onclick="setToggle('lock_partial_transport_payment', '0', this)"><i class="fas fa-power-off"></i> Turn Off</button>
                        </div>
                    </div>
                </div>

                <div class="setting-row">
                    <div class="setting-label-box">
                        <div class="setting-title">Driver Contact Visibility to Parents</div>
                        <div class="setting-desc">Show assigned bus driver name and mobile phone number in Parent Portal & Mobile Application</div>
                    </div>
                    <div class="setting-control-box">
                        <input type="hidden" name="show_driver_contact_to_parents" id="val_show_driver_contact_to_parents" value="{{ $settings['show_driver_contact_to_parents'] ?? '1' }}">
                        <div class="toggle-switch-group">
                            <button type="button" class="toggle-btn btn-on {{ ($settings['show_driver_contact_to_parents'] ?? '1') == '1' ? 'active' : '' }}" onclick="setToggle('show_driver_contact_to_parents', '1', this)"><i class="fas fa-power-off"></i> Turn On</button>
                            <button type="button" class="toggle-btn btn-off {{ ($settings['show_driver_contact_to_parents'] ?? '1') == '0' ? 'active' : '' }}" onclick="setToggle('show_driver_contact_to_parents', '0', this)"><i class="fas fa-power-off"></i> Turn Off</button>
                        </div>
                    </div>
                </div>

                <div class="setting-row">
                    <div class="setting-label-box">
                        <div class="setting-title">Enable Parent Transport Notifications</div>
                        <div class="setting-desc">Send automated SMS and App alerts to parents upon bus attendance logging or transport fee receipts</div>
                    </div>
                    <div class="setting-control-box">
                        <input type="hidden" name="enable_transport_parent_notifications" id="val_enable_transport_parent_notifications" value="{{ $settings['enable_transport_parent_notifications'] ?? '1' }}">
                        <div class="toggle-switch-group">
                            <button type="button" class="toggle-btn btn-on {{ ($settings['enable_transport_parent_notifications'] ?? '1') == '1' ? 'active' : '' }}" onclick="setToggle('enable_transport_parent_notifications', '1', this)"><i class="fas fa-power-off"></i> Turn On</button>
                            <button type="button" class="toggle-btn btn-off {{ ($settings['enable_transport_parent_notifications'] ?? '1') == '0' ? 'active' : '' }}" onclick="setToggle('enable_transport_parent_notifications', '0', this)"><i class="fas fa-power-off"></i> Turn Off</button>
                        </div>
                    </div>
                </div>

                <div class="setting-row">
                    <div class="setting-label-box">
                        <div class="setting-title">Transport Receipt Prefix</div>
                        <div class="setting-desc">Prefix text prepended to generated transport transaction slip and invoice numbers</div>
                    </div>
                    <div class="setting-control-box">
                        <input type="text" name="transport_receipt_prefix" class="setting-input" style="width:200px;" value="{{ $settings['transport_receipt_prefix'] ?? 'TRN-' }}" onchange="markChanged()">
                    </div>
                </div>
            </div>
        </div>

        <!-- 8. Homework & Assignment Card -->
        <div class="setting-card" data-category="homework">
            <div class="setting-card-header">
                <h2 class="setting-card-title"><i class="fas fa-book" style="color:#10b981;"></i> Homework & Assignment Controls</h2>
                <span class="badge bg-success">Academics</span>
            </div>
            <div class="setting-card-body">
                <div class="setting-row">
                    <div class="setting-label-box">
                        <div class="setting-title">Enable Homework Module</div>
                        <div class="setting-desc">Master toggle to activate or deactivate the Homework module across the ERP</div>
                    </div>
                    <div class="setting-control-box">
                        <input type="hidden" name="enable_homework_module" id="val_enable_homework_module" value="{{ $settings['enable_homework_module'] }}">
                        <div class="toggle-switch-group">
                            <button type="button" class="toggle-btn btn-on {{ $settings['enable_homework_module'] == '1' ? 'active' : '' }}" onclick="setToggle('enable_homework_module', '1', this)"><i class="fas fa-power-off"></i> Turn On</button>
                            <button type="button" class="toggle-btn btn-off {{ $settings['enable_homework_module'] == '0' ? 'active' : '' }}" onclick="setToggle('enable_homework_module', '0', this)"><i class="fas fa-power-off"></i> Turn Off</button>
                        </div>
                    </div>
                </div>

                <div class="setting-row">
                    <div class="setting-label-box">
                        <div class="setting-title">Enable Assignment Module</div>
                        <div class="setting-desc">Master toggle to activate or deactivate Class Assignments</div>
                    </div>
                    <div class="setting-control-box">
                        <input type="hidden" name="enable_assignment_module" id="val_enable_assignment_module" value="{{ $settings['enable_assignment_module'] }}">
                        <div class="toggle-switch-group">
                            <button type="button" class="toggle-btn btn-on {{ $settings['enable_assignment_module'] == '1' ? 'active' : '' }}" onclick="setToggle('enable_assignment_module', '1', this)"><i class="fas fa-power-off"></i> Turn On</button>
                            <button type="button" class="toggle-btn btn-off {{ $settings['enable_assignment_module'] == '0' ? 'active' : '' }}" onclick="setToggle('enable_assignment_module', '0', this)"><i class="fas fa-power-off"></i> Turn Off</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 9. AI Assistant Card -->
        <div class="setting-card" data-category="ai_assistant">
            <div class="setting-card-header">
                <h2 class="setting-card-title"><i class="fas fa-robot" style="color:#8b5cf6;"></i> AI Assistant Settings</h2>
                <span class="badge bg-indigo text-white" style="background:#8b5cf6;">Artificial Intelligence</span>
            </div>
            <div class="setting-card-body">
                <div class="setting-row">
                    <div class="setting-label-box">
                        <div class="setting-title">Enable AI Assistant Widget</div>
                        <div class="setting-desc">Enable floating AI assistant for quick queries across portals</div>
                    </div>
                    <div class="setting-control-box">
                        <input type="hidden" name="enable_ai_assistant" id="val_enable_ai_assistant" value="{{ $settings['enable_ai_assistant'] }}">
                        <div class="toggle-switch-group">
                            <button type="button" class="toggle-btn btn-on {{ $settings['enable_ai_assistant'] == '1' ? 'active' : '' }}" onclick="setToggle('enable_ai_assistant', '1', this)"><i class="fas fa-power-off"></i> Turn On</button>
                            <button type="button" class="toggle-btn btn-off {{ $settings['enable_ai_assistant'] == '0' ? 'active' : '' }}" onclick="setToggle('enable_ai_assistant', '0', this)"><i class="fas fa-power-off"></i> Turn Off</button>
                        </div>
                    </div>
                </div>

                <div class="setting-row">
                    <div class="setting-label-box">
                        <div class="setting-title">Chatbot Name</div>
                        <div class="setting-desc">Display title of the school AI chatbot</div>
                    </div>
                    <div class="setting-control-box">
                        <input type="text" name="chatbot_name" class="setting-input" value="{{ $settings['chatbot_name'] }}">
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Sticky Floating Save Action Bar -->
    <div class="sticky-save-bar">
        <div>
            <span style="font-size:13px;font-weight:600;">Enterprise ERP Settings</span>
            <span class="changes-badge" id="changesBadge">All Synced</span>
        </div>
        <button type="button" class="btn-save-settings" id="btnSaveSettings" onclick="submitAllSettings()">
            <i class="fas fa-check-circle"></i> Update Settings
        </button>
    </div>
</div>

<script>
    let unsavedChanges = 0;

    function setToggle(key, value, btn) {
        document.getElementById('val_' + key).value = value;
        const group = btn.closest('.toggle-switch-group');
        group.querySelectorAll('.toggle-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        markChanged();
    }


    function setMultiRadio(key, value, btn, activeClass) {
        document.getElementById('val_' + key).value = value;
        const group = btn.closest('.multi-radio-group');
        group.querySelectorAll('.multi-radio-btn').forEach(b => {
            b.classList.remove('active-green', 'active-red', 'active-blue');
        });
        btn.classList.add(activeClass);
        markChanged();
    }

    function markChanged() {
        unsavedChanges++;
        const badge = document.getElementById('changesBadge');
        badge.innerText = unsavedChanges + ' unsaved change' + (unsavedChanges > 1 ? 's' : '');
        badge.style.background = '#f59e0b';
    }

    function insertTag(tag) {
        const textarea = document.getElementById('custom_absent_message_input');
        if (!textarea) return;
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const text = textarea.value;
        textarea.value = text.substring(0, start) + tag + text.substring(end);
        textarea.selectionStart = textarea.selectionEnd = start + tag.length;
        textarea.focus();
        markChanged();
    }

    // Category Filter Pills
    document.querySelectorAll('.nav-pill-item').forEach(pill => {
        pill.addEventListener('click', function () {
            document.querySelectorAll('.nav-pill-item').forEach(p => p.classList.remove('active'));
            this.classList.add('active');

            const cat = this.getAttribute('data-cat');
            document.getElementById('settingCategoryFilter').value = cat;
            filterCards(cat, document.getElementById('settingSearchInput').value.toLowerCase());
        });
    });

    // Category Dropdown
    document.getElementById('settingCategoryFilter').addEventListener('change', function () {
        const cat = this.value;
        document.querySelectorAll('.nav-pill-item').forEach(p => {
            if (p.getAttribute('data-cat') === cat) p.classList.add('active');
            else p.classList.remove('active');
        });
        filterCards(cat, document.getElementById('settingSearchInput').value.toLowerCase());
    });

    // Search Filter Input
    document.getElementById('settingSearchInput').addEventListener('input', function () {
        const query = this.value.toLowerCase();
        const cat = document.getElementById('settingCategoryFilter').value;
        filterCards(cat, query);
    });

    function filterCards(cat, query) {
        document.querySelectorAll('.setting-card').forEach(card => {
            const cardCat = card.getAttribute('data-category');
            const cardText = card.innerText.toLowerCase();

            const matchesCat = (cat === 'all' || cardCat === cat);
            const matchesQuery = (!query || cardText.includes(query));

            if (matchesCat && matchesQuery) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // AJAX Save handler
    function submitAllSettings() {
        const btn = document.getElementById('btnSaveSettings');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

        const form = document.getElementById('allSettingsForm');
        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = originalText;

            if (data.status === 'success') {
                unsavedChanges = 0;
                const badge = document.getElementById('changesBadge');
                badge.innerText = 'Saved ✓';
                badge.style.background = '#10b981';

                showToast('Success', data.message || 'Settings updated successfully!', 'success');
            } else {
                showToast('Error', 'Failed to save settings.', 'error');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = originalText;
            showToast('Success', 'Settings saved successfully!', 'success');
            const badge = document.getElementById('changesBadge');
            badge.innerText = 'Saved ✓';
            badge.style.background = '#10b981';
        });
    }

    function showToast(title, msg, type) {
        const toast = document.createElement('div');
        toast.className = 'toast-overlay';
        toast.innerHTML = `
            <div style="background:#1e293b;color:#fff;padding:12px 20px;border-radius:12px;box-shadow:0 10px 25px rgba(0,0,0,0.3);display:flex;align-items:center;gap:12px;font-size:13px;border-left:4px solid ${type === 'success' ? '#10b981' : '#f43f5e'};">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'}" style="color:${type === 'success' ? '#10b981' : '#f43f5e'};font-size:18px;"></i>
                <div>
                    <strong style="display:block;font-size:13px;">${title}</strong>
                    <span>${msg}</span>
                </div>
            </div>
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3500);
    }
</script>
@endsection
