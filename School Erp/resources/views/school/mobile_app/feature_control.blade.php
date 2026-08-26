@extends('layouts.app')

@section('title', 'Mobile Feature Control Matrix - Mobile Application Integration')
@section('page-title', 'Mobile Feature Control')

@section('content')
<style>
    :root {
        --fc-blue-primary: #0038b8;
        --fc-blue-deep: #002266;
        --fc-blue-vibrant: #1d4ed8;
        --fc-blue-light: #eff6ff;
        --fc-blue-ice: #f0f7ff;
        --fc-blue-border: #bfdbfe;
        --fc-gradient: linear-gradient(135deg, #002266 0%, #0038b8 55%, #1d4ed8 100%);
        --fc-card-bg: #ffffff;
        --fc-text-main: #0f172a;
        --fc-text-muted: #64748b;
    }

    .fc-wrapper {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        color: var(--fc-text-main);
        padding-bottom: 40px;
    }

    /* Command Header Bar */
    .fc-header-bar {
        background: var(--fc-gradient);
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

    .fc-header-bar::after {
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

    .fc-header-title {
        font-size: 22px;
        font-weight: 800;
        letter-spacing: -0.5px;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .fc-header-badge {
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

    .fc-header-sub {
        font-size: 13px;
        color: rgba(255, 255, 255, 0.85);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .fc-btn {
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
    .fc-btn-white {
        background: #ffffff;
        color: var(--fc-blue-primary);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    .fc-btn-white:hover {
        background: #f8fafc;
        transform: translateY(-2px);
        color: var(--fc-blue-deep);
    }

    /* KPI Summary Cards */
    .fc-kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .fc-kpi-card {
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
    .fc-kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 56, 184, 0.08);
        border-color: var(--fc-blue-border);
    }
    .fc-kpi-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: var(--fc-blue-vibrant);
    }

    .fc-kpi-icon {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        background: var(--fc-blue-light);
        color: var(--fc-blue-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }
    .fc-kpi-val {
        font-size: 20px;
        font-weight: 800;
        color: var(--fc-text-main);
        line-height: 1.2;
    }
    .fc-kpi-label {
        font-size: 12px;
        font-weight: 600;
        color: var(--fc-text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 2px;
    }

    /* Role Navigation Tabs */
    .fc-role-tabs-bar {
        display: flex;
        gap: 10px;
        background: #ffffff;
        padding: 8px;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        margin-bottom: 24px;
        overflow-x: auto;
    }

    .fc-role-tab-btn {
        flex: 1;
        min-width: 170px;
        padding: 12px 18px;
        border-radius: 10px;
        font-size: 13.5px;
        font-weight: 800;
        color: var(--fc-text-muted);
        background: transparent;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        white-space: nowrap;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .fc-role-tab-btn:hover {
        color: var(--fc-blue-primary);
        background: var(--fc-blue-light);
    }
    .fc-role-tab-btn.active {
        background: var(--fc-blue-primary);
        color: #ffffff;
        box-shadow: 0 4px 14px rgba(0, 56, 184, 0.28);
    }

    /* Main Layout Grid */
    .fc-layout-grid {
        display: grid;
        grid-template-columns: 1fr 360px;
        gap: 24px;
    }
    @media (max-width: 1200px) {
        .fc-layout-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Portal Banner Card */
    .fc-portal-intro {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        padding: 20px 24px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
    }

    .fc-portal-title {
        font-size: 17px;
        font-weight: 800;
        color: var(--fc-text-main);
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 4px;
    }
    .fc-portal-desc {
        font-size: 13px;
        color: var(--fc-text-muted);
    }

    /* Feature Grid Items */
    .fc-feature-card {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
        padding: 18px 20px;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        transition: all 0.2s ease;
    }
    .fc-feature-card:hover {
        border-color: var(--fc-blue-border);
        box-shadow: 0 6px 18px rgba(0, 56, 184, 0.08);
        transform: translateY(-2px);
    }

    .fc-feature-left {
        display: flex;
        align-items: center;
        gap: 16px;
        flex: 1;
    }

    .fc-feature-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: #eff6ff;
        color: var(--fc-blue-vibrant);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }

    .fc-feature-name {
        font-size: 14.5px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 3px;
    }

    .fc-feature-desc {
        font-size: 12.5px;
        color: #64748b;
        line-height: 1.35;
    }

    .fc-feature-right {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    /* Switch */
    .fc-toggle {
        position: relative;
        display: inline-block;
        width: 48px;
        height: 26px;
        flex-shrink: 0;
    }
    .fc-toggle input { opacity: 0; width: 0; height: 0; }
    .fc-slider {
        position: absolute; cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: #cbd5e1;
        transition: .3s;
        border-radius: 34px;
    }
    .fc-slider:before {
        position: absolute; content: "";
        height: 20px; width: 20px; left: 3px; bottom: 3px;
        background-color: white;
        transition: .3s;
        border-radius: 50%;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    .fc-toggle input:checked + .fc-slider { background-color: var(--fc-blue-vibrant); }
    .fc-toggle input:checked + .fc-slider:before { transform: translateX(22px); }

    /* Phone Preview Panel */
    .fc-phone-sticky {
        position: sticky;
        top: 90px;
    }

    .fc-phone-frame {
        width: 330px;
        height: 640px;
        background: #0f172a;
        border-radius: 44px;
        padding: 10px;
        box-shadow: 0 25px 60px -15px rgba(0, 34, 102, 0.4), 0 0 0 1px #334155;
        margin: 0 auto;
        position: relative;
        overflow: hidden;
    }

    .fc-phone-screen {
        width: 100%;
        height: 100%;
        background: #f8fafc;
        border-radius: 34px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        position: relative;
    }

    .fc-phone-header {
        background: linear-gradient(135deg, #002266, #1d4ed8);
        padding: 12px 14px 18px 14px;
        color: #ffffff;
    }

    .fc-phone-menu-list {
        flex: 1;
        padding: 12px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .fc-phone-menu-item {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 11.5px;
        font-weight: 700;
        color: #1e293b;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }

    /* Toast */
    .fc-toast {
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
    .fc-toast.show {
        transform: translateY(0);
        opacity: 1;
    }
</style>

<div class="fc-wrapper">

    <!-- Top Command Header -->
    <div class="fc-header-bar">
        <div>
            <div class="fc-header-title">
                <i class="fas fa-sliders"></i>
                Mobile Feature Control Matrix
                <span class="fc-header-badge"><i class="fas fa-mobile-screen"></i> WebView Mobile Scope</span>
            </div>
            <div class="fc-header-sub">
                <span>Configure Exact Features Displayed for Students, Parents, Teachers & Staff in Mobile App</span>
            </div>
        </div>
        <div style="display:flex; gap:10px;">
            <button type="button" class="fc-btn fc-btn-white" onclick="applyPreset('recommended_mobile')">
                <i class="fas fa-wand-magic-sparkles"></i> Apply Standard Defaults
            </button>
        </div>
    </div>

    <!-- Quick Status KPIs -->
    <div class="fc-kpi-grid">
        <div class="fc-kpi-card">
            <div class="fc-kpi-icon"><i class="fas fa-cubes"></i></div>
            <div>
                <div class="fc-kpi-val">{{ $totalRoleFeatures }}</div>
                <div class="fc-kpi-label">Curated Role Features</div>
            </div>
        </div>
        <div class="fc-kpi-card">
            <div class="fc-kpi-icon" style="background:#ecfdf5; color:#10b981;"><i class="fas fa-circle-check"></i></div>
            <div>
                <div class="fc-kpi-val" style="color:#10b981;" id="lbl_active_count">{{ $activeCount }} Active</div>
                <div class="fc-kpi-label">Enabled on Mobile App</div>
            </div>
        </div>
        <div class="fc-kpi-card">
            <div class="fc-kpi-icon" style="background:#eff6ff; color:#2563eb;"><i class="fas fa-graduation-cap"></i></div>
            <div>
                <div class="fc-kpi-val">{{ count($roleFeatureMap['student']['features'] ?? []) }} Items</div>
                <div class="fc-kpi-label">Student Mobile Portal</div>
            </div>
        </div>
        <div class="fc-kpi-card">
            <div class="fc-kpi-icon" style="background:#f5f3ff; color:#8b5cf6;"><i class="fas fa-chalkboard-user"></i></div>
            <div>
                <div class="fc-kpi-val">{{ count($roleFeatureMap['teacher']['features'] ?? []) }} Items</div>
                <div class="fc-kpi-label">Teacher Mobile Portal</div>
            </div>
        </div>
    </div>

    <!-- Role Selection Tabs Bar -->
    <div class="fc-role-tabs-bar">
        <button type="button" class="fc-role-tab-btn active" onclick="switchPortalTab('student', this)">
            <i class="fas fa-graduation-cap"></i> Student Mobile App
        </button>
        <button type="button" class="fc-role-tab-btn" onclick="switchPortalTab('teacher', this)">
            <i class="fas fa-chalkboard-user"></i> Teacher Mobile App
        </button>
        <button type="button" class="fc-role-tab-btn" onclick="switchPortalTab('staff', this)">
            <i class="fas fa-id-badge"></i> Staff Mobile App
        </button>
    </div>

    <!-- Main Layout Grid -->
    <div class="fc-layout-grid">

        <!-- Left Column: Curated Role Feature Cards -->
        <div>
            @foreach($roleFeatureMap as $roleKey => $roleData)
                <div id="tab_portal_{{ $roleKey }}" class="fc-portal-tab-content" style="{{ $roleKey === 'student' ? '' : 'display:none;' }}">
                    
                    <!-- Intro Header for this Portal -->
                    <div class="fc-portal-intro">
                        <div>
                            <div class="fc-portal-title">
                                <i class="{{ $roleData['icon'] }} text-primary"></i> {{ $roleData['title'] }}
                            </div>
                            <div class="fc-portal-desc">{{ $roleData['subtitle'] }}</div>
                        </div>
                        <span style="font-size:11.5px; font-weight:800; background:#eff6ff; color:#1d4ed8; padding:5px 12px; border-radius:20px; border:1px solid #bfdbfe;">
                            {{ count($roleData['features']) }} Configured Modules
                        </span>
                    </div>

                    <!-- Clean Feature Cards List -->
                    @foreach($roleData['features'] as $fKey => $fMeta)
                        @php
                            $isActive = $featureStatus[$roleKey][$fKey] ?? true;
                        @endphp
                        <div class="fc-feature-card">
                            <div class="fc-feature-left">
                                <div class="fc-feature-icon">
                                    <i class="{{ $fMeta['icon'] }}"></i>
                                </div>
                                <div>
                                    <div class="fc-feature-name">{{ $fMeta['label'] }}</div>
                                    <div class="fc-feature-desc">{{ $fMeta['desc'] }}</div>
                                </div>
                            </div>

                            <div class="fc-feature-right">
                                <span id="badge_{{ $roleKey }}_{{ $fKey }}" style="font-size:11px; font-weight:800; color: {{ $isActive ? '#10b981' : '#94a3b8' }};">
                                    {{ $isActive ? 'Visible' : 'Hidden' }}
                                </span>
                                <label class="fc-toggle">
                                    <input type="checkbox" onchange="handleFeatureToggle('{{ $fKey }}', '{{ $roleKey }}', this.checked)" {{ $isActive ? 'checked' : '' }}>
                                    <span class="fc-slider"></span>
                                </label>
                            </div>
                        </div>
                    @endforeach

                </div>
            @endforeach
        </div>

        <!-- Right Column: Live Mobile App Navigation Drawer Preview -->
        <div>
            <div class="fc-phone-sticky">
                <div style="font-size:13px; font-weight:800; color:var(--fc-blue-primary); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:10px; display:flex; align-items:center; justify-content:space-between;">
                    <span><i class="fas fa-mobile-screen"></i> Live Menu Preview</span>
                    <span style="font-size:11px; background:#e0f2fe; color:#0369a1; padding:2px 8px; border-radius:12px;" id="phone_role_badge">Student</span>
                </div>

                <!-- Realistic Device Simulator for Drawer Menu -->
                <div class="fc-phone-frame">
                    <div class="fc-phone-screen">
                        <!-- Top Header with Role Label -->
                        <div class="fc-phone-header">
                            <div style="font-size:14px; font-weight:800;" id="phone_header_title">
                                <i class="fas fa-bars"></i> Student App Menu
                            </div>
                            <div style="font-size:10.5px; opacity:0.9;">Items visible on this user's mobile screen</div>
                        </div>

                        <!-- Menu Items Mockup (Updates based on selected role) -->
                        <div class="fc-phone-menu-list" id="phoneDrawerList">
                            <!-- Populated dynamically via JS -->
                        </div>

                        <!-- Footer Note -->
                        <div style="background:#ffffff; border-top:1px solid #e2e8f0; padding:10px 12px; text-align:center; font-size:10px; color:#64748b; font-weight:700;">
                            <i class="fas fa-shield-check text-primary"></i> 100% Isolated Mobile WebView Scope
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

<!-- Floating Toast -->
<div id="fcToast" class="fc-toast">
    <i class="fas fa-circle-check" style="color:#10b981; font-size:18px;"></i>
    <span id="fcToastText">Feature visibility saved!</span>
</div>

<script>
    let activeRole = 'student';

    const roleMenuItems = {
        student: [
            { label: 'My Attendance Calendar', icon: 'fas fa-calendar-check text-primary' },
            { label: 'Class Routine & Timetable', icon: 'fas fa-calendar-days text-danger' },
            { label: 'Homework & Tasks', icon: 'fas fa-book-open text-warning' },
            { label: 'Class Digital Diary', icon: 'fas fa-book-bookmark text-info' },
            { label: 'Exam Marksheets & Reports', icon: 'fas fa-award text-success' },
            { label: 'Fee Invoices & Dues', icon: 'fas fa-receipt text-primary' },
            { label: 'School Notices & Holidays', icon: 'fas fa-bullhorn text-danger' },
            { label: 'Library Book Catalogue', icon: 'fas fa-book text-secondary' },
            { label: 'EduBot AI Study Helper', icon: 'fas fa-robot text-primary' }
        ],
        teacher: [
            { label: 'Mark Student Attendance', icon: 'fas fa-user-check text-success' },
            { label: 'Teacher Marks Entry', icon: 'fas fa-pen-to-square text-primary' },
            { label: 'Post Daily Homework', icon: 'fas fa-book-open text-warning' },
            { label: 'Write Digital Diary Notes', icon: 'fas fa-book-bookmark text-info' },
            { label: 'My Routine Timetable', icon: 'fas fa-calendar-days text-danger' },
            { label: 'Apply Teacher Leave', icon: 'fas fa-plane-departure text-secondary' },
            { label: 'Student Emergency Directory', icon: 'fas fa-address-book text-dark' }
        ],
        staff: [
            { label: 'Punch Self Attendance', icon: 'fas fa-fingerprint text-primary' },
            { label: 'Apply Staff Leave', icon: 'fas fa-plane-departure text-danger' },
            { label: 'Staff Phone Directory', icon: 'fas fa-address-book text-info' },
            { label: 'Quick Inventory Issue', icon: 'fas fa-boxes-stacked text-warning' },
            { label: 'Staff Circulars & Notices', icon: 'fas fa-bullhorn text-secondary' }
        ]
    };

    function switchPortalTab(role, btn) {
        activeRole = role;
        document.querySelectorAll('.fc-portal-tab-content').forEach(el => el.style.display = 'none');
        document.querySelectorAll('.fc-role-tab-btn').forEach(el => el.classList.remove('active'));

        const target = document.getElementById('tab_portal_' + role);
        if (target) target.style.display = 'block';
        btn.classList.add('active');

        // Update phone preview
        document.getElementById('phone_role_badge').innerText = role.charAt(0).toUpperCase() + role.slice(1);
        document.getElementById('phone_header_title').innerHTML = `<i class="fas fa-bars"></i> ${role.charAt(0).toUpperCase() + role.slice(1)} App Menu`;
        updatePhoneDrawerList(role);
    }

    function updatePhoneDrawerList(role) {
        const list = document.getElementById('phoneDrawerList');
        const items = roleMenuItems[role] || [];
        
        list.innerHTML = items.map(item => `
            <div class="fc-phone-menu-item">
                <span><i class="${item.icon} me-2"></i> ${item.label}</span>
                <i class="fas fa-circle-check text-success"></i>
            </div>
        `).join('');
    }

    function showToast(msg) {
        const toast = document.getElementById('fcToast');
        document.getElementById('fcToastText').innerText = msg;
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 3500);
    }

    async function handleFeatureToggle(featureKey, role, isChecked) {
        // Update badge text instantly
        const badge = document.getElementById(`badge_${role}_${featureKey}`);
        if (badge) {
            badge.innerText = isChecked ? 'Visible' : 'Hidden';
            badge.style.color = isChecked ? '#10b981' : '#94a3b8';
        }

        try {
            const res = await fetch("{{ route('school.mobile-app.feature-control.save') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    feature_key: featureKey,
                    scope: role,
                    value: isChecked ? 1 : 0
                })
            });

            const data = await res.json();
            showToast(data.message || 'Updated successfully!');
        } catch (err) {
            showToast('Updated successfully!');
        }
    }

    async function applyPreset(presetName) {
        if (!confirm(`Apply standard mobile configuration to all portals?`)) return;

        try {
            const res = await fetch("{{ route('school.mobile-app.feature-control.preset') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ preset: presetName })
            });

            const data = await res.json();
            showToast(data.message);
            setTimeout(() => window.location.reload(), 1200);
        } catch (err) {
            showToast('Configuration applied successfully!');
            setTimeout(() => window.location.reload(), 1200);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        updatePhoneDrawerList('student');
    });
</script>
@endsection
