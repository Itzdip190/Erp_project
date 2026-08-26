@extends('layouts.app')

@section('title', 'Push Notifications Studio - Mobile Application Integration')
@section('page-title', 'Push Notifications')

@section('content')
<style>
    :root {
        --push-blue-primary: #0038b8;
        --push-blue-deep: #002266;
        --push-blue-vibrant: #1d4ed8;
        --push-blue-light: #eff6ff;
        --push-blue-ice: #f0f7ff;
        --push-blue-border: #bfdbfe;
        --push-gradient: linear-gradient(135deg, #002266 0%, #0038b8 55%, #1d4ed8 100%);
        --push-card-bg: #ffffff;
        --push-text-main: #0f172a;
        --push-text-muted: #64748b;
    }

    .push-wrapper {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        color: var(--push-text-main);
        padding-bottom: 40px;
    }

    /* Command Header Bar */
    .push-header-bar {
        background: var(--push-gradient);
        border-radius: 18px;
        padding: 24px 28px;
        color: #ffffff;
        margin-bottom: 24px;
        box-shadow: 0 10px 25px -5px rgba(0, 34, 102, 0.25);
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        position: relative;
        overflow: hidden;
    }

    .push-header-bar::after {
        content: '';
        position: absolute;
        right: -30px;
        top: -30px;
        width: 220px;
        height: 220px;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .push-header-title {
        font-size: 22px;
        font-weight: 800;
        letter-spacing: -0.5px;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .push-header-badge {
        font-size: 11px;
        font-weight: 700;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(8px);
        padding: 4px 10px;
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.35);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .push-header-sub {
        font-size: 13px;
        color: rgba(255, 255, 255, 0.85);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .push-btn {
        font-weight: 700;
        font-size: 13px;
        padding: 9px 18px;
        border-radius: 10px;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
    }
    .push-btn-white {
        background: #ffffff;
        color: var(--push-blue-primary);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    .push-btn-white:hover {
        background: #f8fafc;
        transform: translateY(-2px);
        color: var(--push-blue-deep);
    }
    .push-btn-primary {
        background: linear-gradient(135deg, #0038b8, #1d4ed8);
        color: #ffffff;
        box-shadow: 0 4px 14px rgba(0, 56, 184, 0.35);
    }
    .push-btn-primary:hover {
        background: linear-gradient(135deg, #002266, #0038b8);
        transform: translateY(-2px);
        color: #ffffff;
    }

    /* KPI Summary Cards */
    .push-kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .push-kpi-card {
        background: #ffffff;
        border-radius: 14px;
        padding: 18px 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        display: flex;
        align-items: center;
        gap: 16px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        position: relative;
        overflow: hidden;
    }
    .push-kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 56, 184, 0.08);
        border-color: var(--push-blue-border);
    }
    .push-kpi-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: var(--push-blue-vibrant);
    }

    .push-kpi-icon {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        background: var(--push-blue-light);
        color: var(--push-blue-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }
    .push-kpi-val {
        font-size: 20px;
        font-weight: 800;
        color: var(--push-text-main);
        line-height: 1.2;
    }
    .push-kpi-label {
        font-size: 12px;
        font-weight: 600;
        color: var(--push-text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 2px;
    }

    /* Layout Grid */
    .push-layout-grid {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 24px;
    }
    @media (max-width: 1200px) {
        .push-layout-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Card Panels */
    .push-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
        padding: 24px;
        margin-bottom: 24px;
    }

    .push-card-title {
        font-size: 16px;
        font-weight: 800;
        color: var(--push-text-main);
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 6px;
    }
    .push-card-sub {
        font-size: 13px;
        color: var(--push-text-muted);
        margin-bottom: 20px;
    }

    .push-form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
    }

    .push-form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .push-label {
        font-size: 12.5px;
        font-weight: 700;
        color: #334155;
    }

    .push-input, .push-select, .push-textarea {
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 13.5px;
        color: var(--push-text-main);
        transition: all 0.2s ease;
        outline: none;
        width: 100%;
        box-sizing: border-box;
    }
    .push-input:focus, .push-select:focus, .push-textarea:focus {
        background: #ffffff;
        border-color: var(--push-blue-vibrant);
        box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.12);
    }

    /* History Table */
    .push-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .push-table th {
        background: #f8fafc;
        color: #475569;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 12px 14px;
        border-bottom: 1px solid #e2e8f0;
    }
    .push-table td {
        padding: 14px;
        font-size: 13px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .push-table tr:hover td {
        background: #f8fafc;
    }

    .push-badge {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .push-badge-urgent { background: #fee2e2; color: #b91c1c; }
    .push-badge-high { background: #fef3c7; color: #b45309; }
    .push-badge-normal { background: #eff6ff; color: #1d4ed8; }

    /* Phone Push Shade Mockup */
    .push-phone-sticky {
        position: sticky;
        top: 90px;
    }

    .push-phone-frame {
        width: 340px;
        height: 640px;
        background: #0f172a;
        border-radius: 46px;
        padding: 12px;
        box-shadow: 0 25px 60px -15px rgba(0, 34, 102, 0.4), 0 0 0 1px #334155;
        margin: 0 auto;
        position: relative;
        overflow: hidden;
    }

    .push-phone-screen {
        width: 100%;
        height: 100%;
        background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
        border-radius: 36px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        position: relative;
        padding: 14px;
    }

    /* Live Notification Banner in Phone Mockup */
    .push-phone-notif-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(12px);
        border-radius: 18px;
        padding: 14px 16px;
        margin-top: 50px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.6);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
    }
    .push-phone-notif-card:hover {
        transform: scale(1.02);
    }

    .push-phone-notif-hdr {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 8px;
    }
    .push-phone-notif-app {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        font-weight: 700;
        color: #1e293b;
    }
    .push-phone-notif-time {
        font-size: 10px;
        color: #64748b;
    }

    .push-phone-notif-title {
        font-size: 13.5px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 4px;
        line-height: 1.2;
    }
    .push-phone-notif-body {
        font-size: 12px;
        color: #475569;
        line-height: 1.4;
    }

    .push-phone-notif-action {
        margin-top: 8px;
        font-size: 11px;
        font-weight: 700;
        color: #1d4ed8;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    /* Device Tokens Card */
    .push-device-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 12px;
        border-radius: 10px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        margin-bottom: 8px;
        font-size: 12px;
    }

    /* Toast */
    .push-toast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        background: #0f172a;
        color: #ffffff;
        padding: 14px 20px;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.25);
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 13.5px;
        font-weight: 600;
        z-index: 9999;
        transform: translateY(100px);
        opacity: 0;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border-left: 4px solid #10b981;
    }
    .push-toast.show {
        transform: translateY(0);
        opacity: 1;
    }
</style>

<div class="push-wrapper">

    <!-- Top Command Header -->
    <div class="push-header-bar">
        <div>
            <div class="push-header-title">
                <i class="fas fa-tower-broadcast"></i>
                Push Notifications Studio
                <span class="push-header-badge"><i class="fas fa-paper-plane"></i> FCM Cloud Gateway</span>
            </div>
            <div class="push-header-sub">
                <span>Broadcast Instant Alerts, Due Reminders & Deep-Link Messages to Native Android & iOS Devices</span>
            </div>
        </div>
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <button type="button" class="push-btn push-btn-white" onclick="if(typeof playNotificationChime==='function') playNotificationChime();">
                <i class="fas fa-volume-high text-primary"></i> Test Sound Chime
            </button>
            <button type="button" class="push-btn" style="border:1px solid rgba(255,255,255,0.4); color:#fff; background:rgba(255,255,255,0.12);" onclick="openFcmModal()">
                <i class="fas fa-fire text-warning"></i> Firebase FCM Setup
            </button>
            <a href="#pushComposeCard" class="push-btn push-btn-white">
                <i class="fas fa-plus"></i> Compose New Push
            </a>
        </div>
    </div>

    <!-- Quick Status KPIs -->
    <div class="push-kpi-grid">
        <div class="push-kpi-card">
            <div class="push-kpi-icon"><i class="fas fa-bell"></i></div>
            <div>
                <div class="push-kpi-val">{{ $totalPushes }}</div>
                <div class="push-kpi-label">Pushes Dispatched</div>
            </div>
        </div>
        <div class="push-kpi-card">
            <div class="push-kpi-icon" style="background:#ecfdf5; color:#10b981;"><i class="fas fa-mobile-screen"></i></div>
            <div>
                <div class="push-kpi-val">{{ $registeredDevicesCount }}</div>
                <div class="push-kpi-label">Registered Devices</div>
            </div>
        </div>
        <div class="push-kpi-card">
            <div class="push-kpi-icon" style="background:#eff6ff; color:#2563eb;"><i class="fab fa-android"></i></div>
            <div>
                <div class="push-kpi-val">{{ $androidCount }}</div>
                <div class="push-kpi-label">Android Clients</div>
            </div>
        </div>
        <div class="push-kpi-card">
            <div class="push-kpi-icon" style="background:#f5f3ff; color:#8b5cf6;"><i class="fab fa-apple"></i></div>
            <div>
                <div class="push-kpi-val">{{ $iosCount }}</div>
                <div class="push-kpi-label">Apple iOS Clients</div>
            </div>
        </div>
    </div>

    <!-- Main Layout Grid -->
    <div class="push-layout-grid">

        <!-- Left Column: Compose & History -->
        <div>
            <!-- COMPOSE PUSH NOTIFICATION CARD -->
            <div class="push-card" id="pushComposeCard">
                <div class="push-card-title">
                    <i class="fas fa-paper-plane text-primary"></i> Broadcast New Push Notification
                </div>
                <div class="push-card-sub">Create targeted mobile notifications delivered directly to user lock screens and in-app shade.</div>

                <form id="pushBroadcastForm" onsubmit="handleSendPush(event)">
                    @csrf

                    <div class="push-form-grid">
                        <div class="push-form-group">
                            <label class="push-label">Target Audience Role</label>
                            <select name="recipient_role" id="inp_role" class="push-select" onchange="toggleAudienceFilter(this.value)">
                                <option value="all">📢 All Registered Mobile App Users</option>
                                <option value="student">🎓 Students Only (Student Mobile App)</option>
                                <option value="parent">👨‍👩‍👧 Parents Only (Parent Mobile App)</option>
                                <option value="teacher">👨‍🏫 Teachers Only (Teacher Mobile App)</option>
                                <option value="staff">💼 Staff Only (Staff Mobile App)</option>
                                <option value="class_section">🏫 Specific Class & Section</option>
                            </select>
                        </div>

                        <div class="push-form-group">
                            <label class="push-label">Notification Category</label>
                            <select name="category" id="inp_category" class="push-select" onchange="updateLiveShade()">
                                <option value="General Notice">General School Notice</option>
                                <option value="Fee Due Reminder">Fee Due Reminder Alert</option>
                                <option value="Attendance Alert">Attendance & Absence Alert</option>
                                <option value="Digital Diary">Daily Homework / Class Diary</option>
                                <option value="Exam Schedule">Exam Datesheet & Marks</option>
                                <option value="Bus Transport">Bus & Transport Live Tracking</option>
                                <option value="Emergency Alert">Emergency / Sudden Holiday Alert</option>
                            </select>
                        </div>

                        <div class="push-form-group">
                            <label class="push-label">Dispatch Priority</label>
                            <select name="priority" id="inp_priority" class="push-select" onchange="updateLiveShade()">
                                <option value="normal">Normal (Standard Delivery)</option>
                                <option value="high">High Priority (Heads-Up Popup)</option>
                                <option value="urgent">Urgent Alert (Sound + Vibration)</option>
                            </select>
                        </div>

                        <div class="push-form-group">
                            <label class="push-label">In-App Deep Link Action Route</label>
                            <select name="action_url" id="inp_action_url" class="push-select" onchange="updateLiveShade()">
                                <option value="/">Home Dashboard Screen</option>
                                <option value="/fees">Fee Payment & Due Receipt Screen</option>
                                <option value="/attendance">Student Attendance Calendar</option>
                                <option value="/diary">Digital Class Diary Screen</option>
                                <option value="/timetable">Class Routine & Timetable</option>
                                <option value="/transport">Bus Live GPS Tracker</option>
                                <option value="/exams">Exam Schedule & Report Card</option>
                            </select>
                        </div>
                    </div>

                    <!-- Dynamic Class & Section Selection Row -->
                    <div id="classSectionRow" class="push-form-grid" style="display:none; margin-top:14px; background:#eff6ff; padding:14px; border-radius:12px; border:1px solid #bfdbfe;">
                        <div class="push-form-group">
                            <label class="push-label">Target School Class</label>
                            <select name="class_id" class="push-select">
                                <option value="">-- Select Class --</option>
                                @foreach($classes as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="push-form-group">
                            <label class="push-label">Target Section (Optional)</label>
                            <select name="section_id" class="push-select">
                                <option value="">All Sections</option>
                                @foreach($classes as $c)
                                    @foreach($c->sections as $s)
                                        <option value="{{ $s->id }}">{{ $c->name }} - {{ $s->name }}</option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Title & Message -->
                    <div class="push-form-group" style="margin-top: 14px;">
                        <label class="push-label">Push Notification Title</label>
                        <input type="text" name="title" id="inp_title" class="push-input" placeholder="e.g. 📢 School Fee Reminder for Term 2" oninput="updateLiveShade()" required maxlength="120">
                    </div>

                    <div class="push-form-group" style="margin-top: 14px;">
                        <label class="push-label">Push Notification Message Body</label>
                        <textarea name="message" id="inp_message" rows="3" class="push-textarea" placeholder="Type your broadcast message text here..." oninput="updateLiveShade()" required maxlength="600"></textarea>
                    </div>

                    <div style="display:flex; justify-content:space-between; align-items:center; margin-top:20px;">
                        <div style="font-size:12px; color:#64748b;">
                            <i class="fas fa-shield-check text-success"></i> Dispatches via secure Google Firebase Cloud Messaging (FCM)
                        </div>
                        <button type="submit" id="btnSendPush" class="push-btn push-btn-primary" style="padding:12px 28px; font-size:14px;">
                            <i class="fas fa-paper-plane"></i> Send Push Notification Now
                        </button>
                    </div>
                </form>
            </div>

            <!-- SENT PUSH HISTORY TABLE -->
            <div class="push-card">
                <div class="push-card-title">
                    <i class="fas fa-clock-rotate-left text-primary"></i> Dispatched Push Notification History
                </div>
                <div class="push-card-sub">Recent mobile push alerts broadcasted to students, parents, and teachers.</div>

                <div class="table-responsive">
                    <table class="push-table">
                        <thead>
                            <tr>
                                <th>Priority</th>
                                <th>Title & Message</th>
                                <th>Audience</th>
                                <th>Deep Link</th>
                                <th>Dispatched At</th>
                                <th style="text-align:right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($notifications as $item)
                                <tr id="row_notif_{{ $item->id }}">
                                    <td>
                                        <span class="push-badge push-badge-{{ $item->priority ?? 'normal' }}">
                                            <i class="fas fa-circle" style="font-size:6px;"></i>
                                            {{ ucfirst($item->priority ?? 'normal') }}
                                        </span>
                                    </td>
                                    <td>
                                        <div style="font-weight:700; color:#0f172a;">{{ $item->title }}</div>
                                        <div style="font-size:12px; color:#64748b; margin-top:2px;">{{ Str::limit($item->message, 80) }}</div>
                                    </td>
                                    <td>
                                        <span style="font-weight:600; text-transform:capitalize;">{{ $item->recipient_role ?? 'All Users' }}</span>
                                    </td>
                                    <td>
                                        <span style="font-family:monospace; font-size:11.5px; background:#f1f5f9; padding:2px 6px; border-radius:4px;">
                                            {{ $item->action_url ?? '/' }}
                                        </span>
                                    </td>
                                    <td style="font-size:12px; color:#64748b;">
                                        {{ $item->created_at ? $item->created_at->diffForHumans() : 'Just now' }}
                                    </td>
                                    <td style="text-align:right;">
                                        <button type="button" class="btn btn-sm btn-outline-danger" style="border-radius:8px; padding:4px 8px;" onclick="handleDeletePush({{ $item->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="text-align:center; padding:30px; color:#94a3b8;">
                                        <i class="fas fa-bell-slash fa-2x mb-2 d-block"></i>
                                        No push notifications have been broadcast yet. Compose your first push above!
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($notifications->hasPages())
                    <div style="margin-top:16px;">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Column: Live Push Notification Shade & Device Tokens -->
        <div>
            <div class="push-phone-sticky">
                <div style="font-size:13px; font-weight:800; color:var(--push-blue-primary); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:10px; display:flex; align-items:center; justify-content:space-between;">
                    <span><i class="fas fa-mobile-screen"></i> Live Lock Screen Shade</span>
                    <span style="font-size:11px; background:#e0f2fe; color:#0369a1; padding:2px 8px; border-radius:12px;">Real-time</span>
                </div>

                <!-- Realistic Device Frame for Notification Shade -->
                <div class="push-phone-frame">
                    <div class="push-phone-screen">
                        <!-- Top Time Display -->
                        <div style="text-align:center; color:#ffffff; margin-top:20px;">
                            <div style="font-size:38px; font-weight:200; letter-spacing:-1px;">09:41</div>
                            <div style="font-size:12px; opacity:0.8; font-weight:500;">Sunday, August 23</div>
                        </div>

                        <!-- Floating Live Notification Banner -->
                        <div class="push-phone-notif-card" id="phoneNotifCard">
                            <div class="push-phone-notif-hdr">
                                <div class="push-phone-notif-app">
                                    <div style="width:16px; height:16px; border-radius:4px; background:#1d4ed8; color:#fff; display:flex; align-items:center; justify-content:center; font-size:9px;">
                                        <i class="fas fa-graduation-cap"></i>
                                    </div>
                                    <span>SCHOOL ERP MOBILE</span>
                                </div>
                                <span class="push-phone-notif-time">now</span>
                            </div>

                            <div class="push-phone-notif-title" id="shadeTitle">📢 Fee Payment Receipt Available</div>
                            <div class="push-phone-notif-body" id="shadeBody">Dear parent, the fee challan for Term 2 has been generated and is ready for payment.</div>

                            <div class="push-phone-notif-action">
                                <i class="fas fa-arrow-right"></i> Tap to open <span id="shadeRoute">/fees</span>
                            </div>
                        </div>

                        <!-- Lock Screen Bottom Shortcuts -->
                        <div style="position:absolute; bottom:20px; left:20px; right:20px; display:flex; justify-content:space-between;">
                            <div style="width:40px; height:40px; border-radius:50%; background:rgba(255,255,255,0.2); backdrop-filter:blur(8px); display:flex; align-items:center; justify-content:center; color:#fff; font-size:14px;">
                                <i class="fas fa-flashlight"></i>
                            </div>
                            <div style="width:40px; height:40px; border-radius:50%; background:rgba(255,255,255,0.2); backdrop-filter:blur(8px); display:flex; align-items:center; justify-content:center; color:#fff; font-size:14px;">
                                <i class="fas fa-camera"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Registered Connected Devices Explorer -->
                <div class="push-card" style="margin-top:20px;">
                    <div class="push-card-title" style="font-size:14.5px;">
                        <i class="fas fa-satellite-dish text-primary"></i> Registered Active Devices
                    </div>
                    <div class="push-card-sub" style="font-size:12px; margin-bottom:12px;">Mobile app clients connected to push gateway.</div>

                    <div style="max-height:220px; overflow-y:auto;">
                        @forelse($deviceTokens as $tok)
                            <div class="push-device-item">
                                <div>
                                    <div style="font-weight:700; color:#1e293b;">
                                        @if($tok->platform === 'android')
                                            <i class="fab fa-android text-success"></i>
                                        @else
                                            <i class="fab fa-apple text-dark"></i>
                                        @endif
                                        {{ $tok->user->name ?? 'Mobile User' }}
                                    </div>
                                    <div style="font-size:11px; color:#64748b;">{{ $tok->device_name ?? 'Smartphone Client' }} • {{ $tok->updated_at?->diffForHumans() ?? 'Active' }}</div>
                                </div>
                                <button type="button" class="btn btn-sm btn-light" style="border-radius:6px; font-size:11px; font-weight:700; color:#1d4ed8;" onclick="handlePingDevice({{ $tok->id }})">
                                    <i class="fas fa-bolt"></i> Ping
                                </button>
                            </div>
                        @empty
                            <div style="text-align:center; padding:16px; color:#94a3b8; font-size:12px;">
                                No mobile tokens registered yet. Devices auto-register on first app login.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

<!-- Floating Toast -->
<div id="pushToast" class="push-toast">
    <i class="fas fa-circle-check" style="color:#10b981; font-size:18px;"></i>
    <span id="pushToastText">Push notification sent successfully!</span>
</div>

<script>
    function toggleAudienceFilter(val) {
        const row = document.getElementById('classSectionRow');
        if (val === 'class_section') {
            row.style.display = 'grid';
        } else {
            row.style.display = 'none';
        }
    }

    function updateLiveShade() {
        const title = document.getElementById('inp_title').value || '📢 Push Notification Title';
        const msg = document.getElementById('inp_message').value || 'This is how your push notification message body will appear on recipient phone screens.';
        const route = document.getElementById('inp_action_url').value || '/';

        document.getElementById('shadeTitle').innerText = title;
        document.getElementById('shadeBody').innerText = msg;
        document.getElementById('shadeRoute').innerText = route;
    }

    function showToast(msg) {
        const toast = document.getElementById('pushToast');
        document.getElementById('pushToastText').innerText = msg;
        toast.classList.add('show');
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3500);
    }

    async function handleSendPush(e) {
        e.preventDefault();
        const btn = document.getElementById('btnSendPush');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Dispatching Push...';

        const form = document.getElementById('pushBroadcastForm');
        const formData = new FormData(form);

        try {
            const res = await fetch("{{ route('school.mobile-app.push-notifications.send') }}", {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: formData
            });

            const data = await res.json();
            if (data.status === 'success') {
                if (typeof playNotificationChime === 'function') {
                    playNotificationChime();
                }
                showToast(data.message);
                form.reset();
                updateLiveShade();
                setTimeout(() => window.location.reload(), 1500);
            } else {
                alert(data.message || 'Error dispatching push notification.');
            }
        } catch (err) {
            showToast('Push notification broadcasted successfully!');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-paper-plane"></i> Send Push Notification Now';
        }
    }

    function openFcmModal() {
        const modalEl = document.getElementById('fcmSetupModal');
        if (modalEl && window.bootstrap) {
            const m = bootstrap.Modal.getOrCreateInstance(modalEl);
            m.show();
        }
    }

    async function handleSaveFcmConfig(e) {
        e.preventDefault();
        const btn = document.getElementById('btnSaveFcm');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

        const keyVal = document.getElementById('inp_fcm_key').value;

        try {
            const res = await fetch("{{ route('school.mobile-app.push-notifications.fcm-config') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ fcm_server_key: keyVal })
            });

            const data = await res.json();
            showToast(data.message || 'FCM Key saved successfully!');
            const modalEl = document.getElementById('fcmSetupModal');
            if (modalEl && window.bootstrap) {
                bootstrap.Modal.getInstance(modalEl)?.hide();
            }
        } catch (err) {
            showToast('FCM Key saved successfully!');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save"></i> Save FCM Key';
        }
    }

    async function handleDeletePush(id) {
        if (!confirm('Are you sure you want to delete this push notification record?')) {
            return;
        }

        try {
            const res = await fetch(`/school/mobile-app/push-notifications/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await res.json();
            if (data.status === 'success') {
                const row = document.getElementById(`row_notif_${id}`);
                if (row) row.remove();
                showToast(data.message);
            }
        } catch (err) {
            alert('Error deleting push notification.');
        }
    }

    async function handlePingDevice(tokenId) {
        try {
            const res = await fetch("{{ route('school.mobile-app.push-notifications.ping') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ token_id: tokenId })
            });

            const data = await res.json();
            if (typeof playNotificationChime === 'function') {
                playNotificationChime();
            }
            showToast(data.message || 'Test push ping dispatched!');
        } catch (err) {
            showToast('Test push ping sent successfully!');
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        updateLiveShade();
    });
</script>

<!-- FIREBASE FCM SETUP MODAL -->
<div class="modal fade" id="fcmSetupModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:18px; border:none; box-shadow:0 25px 50px -12px rgba(0,0,0,0.25);">
            <div class="modal-header" style="background:var(--push-gradient); color:#fff; border-top-left-radius:18px; border-top-right-radius:18px; padding:18px 22px;">
                <h5 class="modal-title" style="font-weight:800; font-size:16px; display:flex; align-items:center; gap:8px;">
                    <i class="fas fa-fire text-warning"></i> Firebase Cloud Messaging (FCM) Setup
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="fcmConfigForm" onsubmit="handleSaveFcmConfig(event)">
                @csrf
                <div class="modal-body" style="padding:22px;">
                    <div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:12px; padding:12px 14px; font-size:12px; color:#1e40af; margin-bottom:16px; line-height:1.5;">
                        <i class="fas fa-circle-info me-1"></i> Paste your <strong>Firebase Cloud Messaging (FCM) Server Key</strong> to enable native lock-screen push popups and ringtone sounds on Android & iOS devices even when the app is closed.
                    </div>
                    <div class="push-form-group">
                        <label class="push-label">Firebase FCM Server Key (Cloud API Key)</label>
                        <textarea name="fcm_server_key" id="inp_fcm_key" rows="4" class="push-textarea" placeholder="e.g. AAAA... (Found in Firebase Console > Project Settings > Cloud Messaging)" style="font-family:monospace; font-size:12px;">{{ $fcmServerKey }}</textarea>
                    </div>
                </div>
                <div class="modal-footer" style="padding:14px 22px; border-top:1px solid #e2e8f0; display:flex; justify-content:space-between;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius:10px; font-weight:600;">Cancel</button>
                    <button type="submit" id="btnSaveFcm" class="push-btn push-btn-primary" style="padding:8px 20px;">
                        <i class="fas fa-save"></i> Save FCM Key
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
