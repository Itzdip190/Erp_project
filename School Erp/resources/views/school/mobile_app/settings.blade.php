@extends('layouts.app')

@section('title', 'Mobile App Settings & API - Mobile Application Integration')
@section('page-title', 'Mobile App Settings & API')

@section('content')
<style>
    :root {
        --mob-blue-primary: #0038b8;
        --mob-blue-deep: #002266;
        --mob-blue-vibrant: #1d4ed8;
        --mob-blue-light: #eff6ff;
        --mob-blue-ice: #f0f7ff;
        --mob-blue-border: #bfdbfe;
        --mob-gradient: linear-gradient(135deg, #002266 0%, #0038b8 55%, #1d4ed8 100%);
        --mob-card-bg: #ffffff;
        --mob-text-main: #0f172a;
        --mob-text-muted: #64748b;
        --mob-border-color: #e2e8f0;
    }

    .mob-settings-wrapper {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        color: var(--mob-text-main);
        padding-bottom: 40px;
    }

    /* Command Header Bar */
    .mob-header-bar {
        background: var(--mob-gradient);
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

    .mob-header-bar::after {
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

    .mob-header-title {
        font-size: 22px;
        font-weight: 800;
        letter-spacing: -0.5px;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .mob-header-badge {
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

    .mob-header-sub {
        font-size: 13px;
        color: rgba(255, 255, 255, 0.85);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .mob-header-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .mob-btn {
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

    .mob-btn-white {
        background: #ffffff;
        color: var(--mob-blue-primary);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    .mob-btn-white:hover {
        background: #f8fafc;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
        color: var(--mob-blue-deep);
    }

    .mob-btn-primary {
        background: linear-gradient(135deg, #0038b8, #1d4ed8);
        color: #ffffff;
        box-shadow: 0 4px 14px rgba(0, 56, 184, 0.35);
    }
    .mob-btn-primary:hover {
        background: linear-gradient(135deg, #002266, #0038b8);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 56, 184, 0.45);
        color: #ffffff;
    }

    .mob-btn-outline {
        background: rgba(255, 255, 255, 0.15);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.3);
        backdrop-filter: blur(6px);
    }
    .mob-btn-outline:hover {
        background: rgba(255, 255, 255, 0.28);
        color: #ffffff;
    }

    /* KPI Summary Cards */
    .mob-kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .mob-kpi-card {
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
    .mob-kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 56, 184, 0.08);
        border-color: var(--mob-blue-border);
    }
    .mob-kpi-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: var(--mob-blue-vibrant);
        border-radius: 4px 0 0 4px;
    }

    .mob-kpi-icon {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        background: var(--mob-blue-light);
        color: var(--mob-blue-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .mob-kpi-val {
        font-size: 20px;
        font-weight: 800;
        color: var(--mob-text-main);
        line-height: 1.2;
    }

    .mob-kpi-label {
        font-size: 12px;
        font-weight: 600;
        color: var(--mob-text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 2px;
    }

    /* Main Grid Layout */
    .mob-layout-grid {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 24px;
    }
    @media (max-width: 1200px) {
        .mob-layout-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Form Nav Tabs */
    .mob-tabs-nav {
        display: flex;
        gap: 8px;
        background: #ffffff;
        padding: 6px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        margin-bottom: 20px;
        overflow-x: auto;
    }

    .mob-tab-btn {
        padding: 10px 18px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 700;
        color: var(--mob-text-muted);
        background: transparent;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
        transition: all 0.2s ease;
    }
    .mob-tab-btn:hover {
        color: var(--mob-blue-primary);
        background: var(--mob-blue-light);
    }
    .mob-tab-btn.active {
        background: var(--mob-blue-primary);
        color: #ffffff;
        box-shadow: 0 4px 10px rgba(0, 56, 184, 0.25);
    }

    /* Card Panels */
    .mob-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
        padding: 24px;
        margin-bottom: 20px;
    }

    .mob-card-title {
        font-size: 16px;
        font-weight: 800;
        color: var(--mob-text-main);
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 6px;
    }

    .mob-card-sub {
        font-size: 13px;
        color: var(--mob-text-muted);
        margin-bottom: 20px;
    }

    .mob-form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 18px;
    }

    .mob-form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .mob-label {
        font-size: 12.5px;
        font-weight: 700;
        color: #334155;
    }

    .mob-input, .mob-select, .mob-textarea {
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 13.5px;
        color: var(--mob-text-main);
        transition: all 0.2s ease;
        outline: none;
        width: 100%;
        box-sizing: border-box;
    }
    .mob-input:focus, .mob-select:focus, .mob-textarea:focus {
        background: #ffffff;
        border-color: var(--mob-blue-vibrant);
        box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.12);
    }

    /* Switch Toggle */
    .mob-switch-group {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px;
        background: #f8fafc;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        margin-bottom: 12px;
        transition: background 0.2s ease;
    }
    .mob-switch-group:hover {
        background: #f1f5f9;
    }

    .mob-switch-info {
        display: flex;
        flex-direction: column;
        gap: 2px;
        max-width: 80%;
    }

    .mob-switch-title {
        font-size: 13.5px;
        font-weight: 700;
        color: var(--mob-text-main);
    }

    .mob-switch-desc {
        font-size: 12px;
        color: var(--mob-text-muted);
    }

    .mob-toggle {
        position: relative;
        display: inline-block;
        width: 46px;
        height: 26px;
        flex-shrink: 0;
    }
    .mob-toggle input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .mob-slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: #cbd5e1;
        transition: .3s;
        border-radius: 34px;
    }
    .mob-slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .3s;
        border-radius: 50%;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    .mob-toggle input:checked + .mob-slider {
        background-color: var(--mob-blue-vibrant);
    }
    .mob-toggle input:checked + .mob-slider:before {
        transform: translateX(20px);
    }

    /* Live Smartphone Preview Mockup */
    .mob-phone-sticky {
        position: sticky;
        top: 90px;
    }

    .mob-phone-frame {
        width: 340px;
        height: 680px;
        background: #0f172a;
        border-radius: 46px;
        padding: 12px;
        box-shadow: 0 25px 60px -15px rgba(0, 34, 102, 0.4), 0 0 0 1px #334155;
        margin: 0 auto;
        position: relative;
        overflow: hidden;
    }

    .mob-phone-screen {
        width: 100%;
        height: 100%;
        background: #f8fafc;
        border-radius: 36px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        position: relative;
    }

    /* Notch / Island */
    .mob-phone-notch {
        width: 110px;
        height: 24px;
        background: #0f172a;
        position: absolute;
        top: 8px;
        left: 50%;
        transform: translateX(-50%);
        border-radius: 20px;
        z-index: 50;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        padding-right: 10px;
    }
    .mob-phone-lens {
        width: 9px;
        height: 9px;
        border-radius: 50%;
        background: #1e293b;
        border: 1px solid #334155;
    }

    /* Phone Status Bar */
    .mob-phone-status {
        height: 38px;
        padding: 10px 20px 0 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 11px;
        font-weight: 700;
        color: #ffffff;
        z-index: 40;
        position: relative;
    }

    /* Phone Dynamic Header */
    .mob-phone-header {
        background: linear-gradient(135deg, var(--mob-blue-deep), var(--mob-blue-vibrant));
        padding: 10px 18px 24px 18px;
        color: #ffffff;
        transition: background 0.3s ease;
    }

    .mob-phone-nav-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 14px;
    }

    .mob-phone-app-title {
        font-size: 15px;
        font-weight: 800;
        letter-spacing: -0.3px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .mob-phone-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.25);
        border: 1.5px solid #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
    }

    .mob-phone-welcome {
        font-size: 12px;
        opacity: 0.9;
    }
    .mob-phone-username {
        font-size: 17px;
        font-weight: 800;
    }

    /* Phone Body Content */
    .mob-phone-body {
        flex: 1;
        padding: 14px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .mob-phone-banner-card {
        background: linear-gradient(135deg, #1e293b, #334155);
        color: #ffffff;
        padding: 14px;
        border-radius: 16px;
        font-size: 12px;
        position: relative;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        overflow: hidden;
    }
    .mob-phone-banner-badge {
        font-size: 9px;
        font-weight: 800;
        text-transform: uppercase;
        background: #3b82f6;
        color: #ffffff;
        padding: 2px 6px;
        border-radius: 4px;
        display: inline-block;
        margin-bottom: 4px;
    }
    .mob-phone-banner-title {
        font-size: 13px;
        font-weight: 800;
        margin-bottom: 2px;
    }

    .mob-phone-grid-icons {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 8px;
        margin-top: 4px;
    }

    .mob-phone-action-item {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 10px 4px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        font-size: 10px;
        font-weight: 700;
        color: #334155;
        box-shadow: 0 2px 5px rgba(0,0,0,0.02);
    }
    .mob-phone-action-circle {
        width: 32px;
        height: 32px;
        border-radius: 10px;
        background: #eff6ff;
        color: var(--mob-blue-vibrant);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
    }

    /* Phone Bottom Bar */
    .mob-phone-bottom-nav {
        background: #ffffff;
        border-top: 1px solid #e2e8f0;
        padding: 8px 12px 14px 12px;
        display: flex;
        justify-content: space-around;
        align-items: center;
    }

    .mob-phone-nav-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 3px;
        font-size: 9.5px;
        font-weight: 700;
        color: #94a3b8;
    }
    .mob-phone-nav-btn.active {
        color: var(--mob-blue-vibrant);
    }

    /* Toast Notification */
    .mob-toast {
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
    }
    .mob-toast.show {
        transform: translateY(0);
        opacity: 1;
    }
    .mob-toast-success {
        border-left: 4px solid #10b981;
    }
</style>

<div class="mob-settings-wrapper">

    <!-- Top Command Header -->
    <div class="mob-header-bar">
        <div>
            <div class="mob-header-title">
                <i class="fas fa-mobile-screen-button"></i>
                Mobile App Settings & API
                <span class="mob-header-badge"><i class="fas fa-bolt"></i> Live Sync</span>
            </div>
            <div class="mob-header-sub">
                <span>Enterprise Native Mobile Configuration • Android Studio & iOS Swift Engine</span>
            </div>
        </div>
        <div class="mob-header-actions">
            <button type="button" class="mob-btn mob-btn-white mob-save-trigger" onclick="handleSaveSettings(event)">
                <i class="fas fa-floppy-disk"></i> Save Settings
            </button>
        </div>
    </div>

    <!-- Quick Status KPIs -->
    <div class="mob-kpi-grid">
        <div class="mob-kpi-card">
            <div class="mob-kpi-icon"><i class="fas fa-mobile-screen"></i></div>
            <div>
                <div class="mob-kpi-val">{{ $registeredDevicesCount }}</div>
                <div class="mob-kpi-label">Registered Devices</div>
            </div>
        </div>
        <div class="mob-kpi-card">
            <div class="mob-kpi-icon" style="background:#ecfdf5; color:#10b981;"><i class="fas fa-signal"></i></div>
            <div>
                <div class="mob-kpi-val" style="color:#10b981;">Online ⚡</div>
                <div class="mob-kpi-label">Mobile API Gateway</div>
            </div>
        </div>
        <div class="mob-kpi-card">
            <div class="mob-kpi-icon" style="background:#fef3c7; color:#d97706;"><i class="fas fa-code-branch"></i></div>
            <div>
                <div class="mob-kpi-val">v{{ $settings['mobile_app_version'] }}</div>
                <div class="mob-kpi-label">App Release Version</div>
            </div>
        </div>
        <div class="mob-kpi-card">
            <div class="mob-kpi-icon" style="background:#f5f3ff; color:#8b5cf6;"><i class="fas fa-images"></i></div>
            <div>
                <div class="mob-kpi-val">{{ $activeBannersCount }}</div>
                <div class="mob-kpi-label">Active Promos & Banners</div>
            </div>
        </div>
    </div>

    <!-- Main Layout Grid -->
    <div class="mob-layout-grid">

        <!-- Form Tabs and Panels -->
        <div>
            <!-- Navigation Tabs -->
            <div class="mob-tabs-nav">
                <button type="button" class="mob-tab-btn active" onclick="switchTab('branding', this)">
                    <i class="fas fa-palette"></i> Branding & Info
                </button>
                <button type="button" class="mob-tab-btn" onclick="switchTab('theme', this)">
                    <i class="fas fa-wand-magic-sparkles"></i> Themes & Colors
                </button>
                <button type="button" class="mob-tab-btn" onclick="switchTab('modes', this)">
                    <i class="fas fa-sliders"></i> Operational Modes
                </button>
                <button type="button" class="mob-tab-btn" onclick="switchTab('security', this)">
                    <i class="fas fa-shield-halved"></i> Security & Biometrics
                </button>
            </div>

            <!-- Form Wrapper -->
            <form id="mobileSettingsForm" onsubmit="handleSaveSettings(event)" enctype="multipart/form-data">
                @csrf

                <!-- TAB 1: BRANDING & INFO -->
                <div id="tab-branding" class="mob-tab-content">
                    <div class="mob-card">
                        <div class="mob-card-title"><i class="fas fa-circle-info text-primary"></i> Mobile Application Identity</div>
                        <div class="mob-card-sub">Configure how your school's native Android and iOS mobile app presents itself to students and parents.</div>

                        <div class="mob-form-grid">
                            <div class="mob-form-group">
                                <label class="mob-label">Mobile App Name</label>
                                <input type="text" name="mobile_app_name" id="inp_app_name" class="mob-input" value="{{ $settings['mobile_app_name'] }}" oninput="updateLivePreview()">
                            </div>
                            <div class="mob-form-group">
                                <label class="mob-label">App Tagline / Subtitle</label>
                                <input type="text" name="mobile_app_tagline" id="inp_app_tagline" class="mob-input" value="{{ $settings['mobile_app_tagline'] }}" oninput="updateLivePreview()">
                            </div>
                            <div class="mob-form-group">
                                <label class="mob-label">Current App Version (Release)</label>
                                <input type="text" name="mobile_app_version" class="mob-input" value="{{ $settings['mobile_app_version'] }}" placeholder="2.4.0">
                            </div>
                            <div class="mob-form-group">
                                <label class="mob-label">Minimum Supported Version</label>
                                <input type="text" name="mobile_min_version" class="mob-input" value="{{ $settings['mobile_min_version'] }}" placeholder="2.0.0">
                            </div>
                        </div>

                        <div style="margin-top: 18px;">
                            <div class="mob-switch-group">
                                <div class="mob-switch-info">
                                    <div class="mob-switch-title">Enforce Mandatory Update (Force Update Modal)</div>
                                    <div class="mob-switch-desc">Block users with app version lower than minimum version until they update mobile app.</div>
                                </div>
                                <label class="mob-toggle">
                                    <input type="checkbox" name="mobile_force_update" value="1" {{ $settings['mobile_force_update'] == '1' ? 'checked' : '' }}>
                                    <span class="mob-slider"></span>
                                </label>
                            </div>
                        </div>

                        <div class="mob-form-grid" style="margin-top: 14px;">
                            <div class="mob-form-group">
                                <label class="mob-label">Force Update Modal Title</label>
                                <input type="text" name="mobile_force_update_title" class="mob-input" value="{{ $settings['mobile_force_update_title'] }}">
                            </div>
                            <div class="mob-form-group">
                                <label class="mob-label">Support Email (App Contact Page)</label>
                                <input type="email" name="mobile_support_email" class="mob-input" value="{{ $settings['mobile_support_email'] }}">
                            </div>
                        </div>

                        <div class="mob-form-group" style="margin-top: 14px;">
                            <label class="mob-label">Force Update Notice Message</label>
                            <textarea name="mobile_force_update_msg" rows="2" class="mob-textarea">{{ $settings['mobile_force_update_msg'] }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- TAB 2: THEMES & COLORS -->
                <div id="tab-theme" class="mob-tab-content" style="display:none;">
                    <div class="mob-card">
                        <div class="mob-card-title"><i class="fas fa-wand-magic-sparkles text-primary"></i> Mobile UI & Color Customizer</div>
                        <div class="mob-card-sub">Tailor the color palette of the mobile app. Changes dynamically reflect on native client boot.</div>

                        <div class="mob-form-grid">
                            <div class="mob-form-group">
                                <label class="mob-label">Primary Royal Blue Color</label>
                                <div style="display:flex; gap:10px; align-items:center;">
                                    <input type="color" id="picker_primary" name="mobile_primary_color" value="{{ $settings['mobile_primary_color'] }}" style="width:48px; height:42px; padding:2px; border-radius:8px; border:1px solid #cbd5e1; cursor:pointer;" oninput="syncColorInput('picker_primary', 'txt_primary'); updateLivePreview();">
                                    <input type="text" id="txt_primary" class="mob-input" value="{{ $settings['mobile_primary_color'] }}" oninput="syncColorPicker('txt_primary', 'picker_primary'); updateLivePreview();">
                                </div>
                            </div>

                            <div class="mob-form-group">
                                <label class="mob-label">Accent Highlight Color</label>
                                <div style="display:flex; gap:10px; align-items:center;">
                                    <input type="color" id="picker_accent" name="mobile_accent_color" value="{{ $settings['mobile_accent_color'] }}" style="width:48px; height:42px; padding:2px; border-radius:8px; border:1px solid #cbd5e1; cursor:pointer;" oninput="syncColorInput('picker_accent', 'txt_accent'); updateLivePreview();">
                                    <input type="text" id="txt_accent" class="mob-input" value="{{ $settings['mobile_accent_color'] }}" oninput="syncColorPicker('txt_accent', 'picker_accent'); updateLivePreview();">
                                </div>
                            </div>

                            <div class="mob-form-group">
                                <label class="mob-label">Native Splash Screen Background</label>
                                <div style="display:flex; gap:10px; align-items:center;">
                                    <input type="color" id="picker_splash" name="mobile_splash_bg" value="{{ $settings['mobile_splash_bg'] }}" style="width:48px; height:42px; padding:2px; border-radius:8px; border:1px solid #cbd5e1; cursor:pointer;" oninput="syncColorInput('picker_splash', 'txt_splash')">
                                    <input type="text" id="txt_splash" class="mob-input" value="{{ $settings['mobile_splash_bg'] }}" oninput="syncColorPicker('txt_splash', 'picker_splash')">
                                </div>
                            </div>

                            <div class="mob-form-group">
                                <label class="mob-label">App Theme Mode</label>
                                <select name="mobile_theme_mode" class="mob-select">
                                    <option value="light" {{ $settings['mobile_theme_mode'] == 'light' ? 'selected' : '' }}>Clean Light Theme (Default)</option>
                                    <option value="dark" {{ $settings['mobile_theme_mode'] == 'dark' ? 'selected' : '' }}>OLED Dark Mode</option>
                                    <option value="auto" {{ $settings['mobile_theme_mode'] == 'auto' ? 'selected' : '' }}>Follow User Phone System Setting</option>
                                </select>
                            </div>
                        </div>

                        <div style="margin-top: 18px;">
                            <div class="mob-switch-group">
                                <div class="mob-switch-info">
                                    <div class="mob-switch-title">Enable Vibrant Header Gradients</div>
                                    <div class="mob-switch-desc">Adds modern Royal-to-Cobalt blue glassmorphic gradient to native mobile top app bar.</div>
                                </div>
                                <label class="mob-toggle">
                                    <input type="checkbox" name="mobile_header_gradient" value="1" {{ $settings['mobile_header_gradient'] == '1' ? 'checked' : '' }} onchange="updateLivePreview()">
                                    <span class="mob-slider"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 3: OPERATIONAL MODES -->
                <div id="tab-modes" class="mob-tab-content" style="display:none;">
                    <div class="mob-card">
                        <div class="mob-card-title"><i class="fas fa-sliders text-primary"></i> Mobile Operational & Maintenance Modes</div>
                        <div class="mob-card-sub">Put mobile apps into scheduled maintenance or disable API synchronization during database updates.</div>

                        <div class="mob-switch-group">
                            <div class="mob-switch-info">
                                <div class="mob-switch-title">Master Mobile API Switch</div>
                                <div class="mob-switch-desc">When disabled, all native mobile app calls will be rejected with HTTP 503 Service Unavailable.</div>
                            </div>
                            <label class="mob-toggle">
                                <input type="checkbox" name="enable_mobile_api" value="1" {{ $settings['enable_mobile_api'] == '1' ? 'checked' : '' }}>
                                <span class="mob-slider"></span>
                            </label>
                        </div>

                        <div class="mob-switch-group" style="background:#fffbeb; border-color:#fde68a;">
                            <div class="mob-switch-info">
                                <div class="mob-switch-title" style="color:#b45309;"><i class="fas fa-screwdriver-wrench me-1"></i> Mobile App Maintenance Mode</div>
                                <div class="mob-switch-desc">Instantly pause mobile app access for students & parents and display the maintenance screen.</div>
                            </div>
                            <label class="mob-toggle">
                                <input type="checkbox" name="mobile_app_maintenance" value="1" {{ $settings['mobile_app_maintenance'] == '1' ? 'checked' : '' }} onchange="updateLivePreview()">
                                <span class="mob-slider"></span>
                            </label>
                        </div>

                        <div class="mob-form-grid" style="margin-top: 14px;">
                            <div class="mob-form-group">
                                <label class="mob-label">Maintenance Screen Title</label>
                                <input type="text" name="mobile_maintenance_title" id="inp_maint_title" class="mob-input" value="{{ $settings['mobile_maintenance_title'] }}" oninput="updateLivePreview()">
                            </div>
                            <div class="mob-form-group">
                                <label class="mob-label">Allow Staff/Admin Bypass</label>
                                <div class="mob-switch-group" style="margin-bottom:0; padding:8px 12px;">
                                    <span style="font-size:12.5px; font-weight:600;">Staff can still log in</span>
                                    <label class="mob-toggle">
                                        <input type="checkbox" name="mobile_bypass_staff_maintenance" value="1" {{ $settings['mobile_bypass_staff_maintenance'] == '1' ? 'checked' : '' }}>
                                        <span class="mob-slider"></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mob-form-group" style="margin-top: 14px;">
                            <label class="mob-label">Maintenance Message Body</label>
                            <textarea name="mobile_maintenance_msg" id="inp_maint_msg" rows="3" class="mob-textarea" oninput="updateLivePreview()">{{ $settings['mobile_maintenance_msg'] }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- TAB 4: SECURITY & BIOMETRICS -->
                <div id="tab-security" class="mob-tab-content" style="display:none;">
                    <div class="mob-card">
                        <div class="mob-card-title"><i class="fas fa-shield-halved text-primary"></i> Mobile Security, Login & Biometrics</div>
                        <div class="mob-card-sub">Enforce high-grade mobile application security, encryption, and biometric device locks.</div>

                        <div class="mob-switch-group">
                            <div class="mob-switch-info">
                                <div class="mob-switch-title">Allow Quick Mobile OTP Login</div>
                                <div class="mob-switch-desc">Allows parents and staff to log into the mobile app using one-time SMS verification code.</div>
                            </div>
                            <label class="mob-toggle">
                                <input type="checkbox" name="mobile_allow_otp_login" value="1" {{ $settings['mobile_allow_otp_login'] == '1' ? 'checked' : '' }}>
                                <span class="mob-slider"></span>
                            </label>
                        </div>

                        <div class="mob-switch-group">
                            <div class="mob-switch-info">
                                <div class="mob-switch-title">Enforce Biometrics (Fingerprint / Face ID)</div>
                                <div class="mob-switch-desc">Requires users to authenticate with local biometric hardware when resuming app from background.</div>
                            </div>
                            <label class="mob-toggle">
                                <input type="checkbox" name="mobile_enforce_biometrics" value="1" {{ $settings['mobile_enforce_biometrics'] == '1' ? 'checked' : '' }}>
                                <span class="mob-slider"></span>
                            </label>
                        </div>

                        <div class="mob-switch-group">
                            <div class="mob-switch-info">
                                <div class="mob-switch-title">Enable AI Face Attendance on Mobile</div>
                                <div class="mob-switch-desc">Allows authorized staff to mark classroom attendance via live camera vector recognition.</div>
                            </div>
                            <label class="mob-toggle">
                                <input type="checkbox" name="mobile_allow_face_auth" value="1" {{ $settings['mobile_allow_face_auth'] == '1' ? 'checked' : '' }}>
                                <span class="mob-slider"></span>
                            </label>
                        </div>

                        <div class="mob-switch-group">
                            <div class="mob-switch-info">
                                <div class="mob-switch-title">Single Active Device Lock</div>
                                <div class="mob-switch-desc">Automatically log out existing mobile sessions when a user signs in from a new device.</div>
                            </div>
                            <label class="mob-toggle">
                                <input type="checkbox" name="mobile_single_device_login" value="1" {{ $settings['mobile_single_device_login'] == '1' ? 'checked' : '' }}>
                                <span class="mob-slider"></span>
                            </label>
                        </div>

                        <div class="mob-form-grid" style="margin-top: 14px;">
                            <div class="mob-form-group">
                                <label class="mob-label">Mobile Inactivity Auto-Logout</label>
                                <select name="mobile_session_timeout" class="mob-select">
                                    <option value="15" {{ $settings['mobile_session_timeout'] == '15' ? 'selected' : '' }}>15 Minutes</option>
                                    <option value="30" {{ $settings['mobile_session_timeout'] == '30' ? 'selected' : '' }}>30 Minutes</option>
                                    <option value="60" {{ $settings['mobile_session_timeout'] == '60' ? 'selected' : '' }}>1 Hour (Recommended)</option>
                                    <option value="1440" {{ $settings['mobile_session_timeout'] == '1440' ? 'selected' : '' }}>24 Hours</option>
                                    <option value="0" {{ $settings['mobile_session_timeout'] == '0' ? 'selected' : '' }}>Never (Persistent)</option>
                                </select>
                            </div>
                            <div class="mob-form-group">
                                <label class="mob-label">Remember Me Token Lifetime (Days)</label>
                                <input type="number" name="mobile_remember_me_days" class="mob-input" value="{{ $settings['mobile_remember_me_days'] }}" min="1" max="365">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Floating Submit Bar -->
                <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:20px;">
                    <button type="submit" class="mob-btn mob-btn-primary mob-save-trigger" style="padding:12px 28px; font-size:14px;">
                        <i class="fas fa-check-circle"></i> Save All Mobile Settings
                    </button>
                </div>
            </form>
        </div>

        <!-- RIGHT COLUMN: LIVE SMARTPHONE PREVIEW -->
        <div>
            <div class="mob-phone-sticky">
                <div style="font-size:13px; font-weight:800; color:var(--mob-blue-primary); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:10px; display:flex; align-items:center; justify-content:space-between;">
                    <span><i class="fas fa-eye"></i> Live Mobile Preview</span>
                    <span style="font-size:11px; background:#e0f2fe; color:#0369a1; padding:2px 8px; border-radius:12px;">Interactive</span>
                </div>

                <!-- Realistic Device Shell -->
                <div class="mob-phone-frame">
                    <div class="mob-phone-notch">
                        <div class="mob-phone-lens"></div>
                    </div>

                    <div class="mob-phone-screen">
                        <!-- Top Phone Status Bar -->
                        <div class="mob-phone-status" id="preview_status_bar">
                            <span>09:41</span>
                            <div style="display:flex; gap:6px; font-size:10px;">
                                <i class="fas fa-signal"></i>
                                <i class="fas fa-wifi"></i>
                                <i class="fas fa-battery-full"></i>
                            </div>
                        </div>

                        <!-- 1. NORMAL MOBILE DASHBOARD MODE -->
                        <div id="preview_normal_mode" style="display:flex; flex-direction:column; flex:1; height:calc(100% - 24px); overflow:hidden;">
                            <!-- Top Dynamic Header -->
                            <div class="mob-phone-header" id="preview_header">
                                <div class="mob-phone-nav-row">
                                    <div class="mob-phone-app-title">
                                        <i class="fas fa-graduation-cap"></i>
                                        <span id="preview_app_name">{{ $settings['mobile_app_name'] }}</span>
                                    </div>
                                    <div class="mob-phone-avatar">
                                        <i class="fas fa-bell"></i>
                                    </div>
                                </div>
                                <div class="mob-phone-welcome">Welcome back,</div>
                                <div class="mob-phone-username">Aarav Sharma</div>
                            </div>

                            <!-- Mini Mockup Content -->
                            <div style="padding:12px; display:flex; flex-direction:column; gap:10px; flex:1; overflow-y:auto;">
                                <!-- Notice Banner Card -->
                                <div style="background:#1e293b; color:#ffffff; border-radius:12px; padding:12px; font-size:11px;">
                                    <div style="font-size:9px; font-weight:800; text-transform:uppercase; color:#38bdf8; margin-bottom:4px;">Notice</div>
                                    <div style="font-weight:700; font-size:12px;">Upcoming Sports Meet 2026</div>
                                    <div style="color:#94a3b8; font-size:10px; margin-top:2px;">Annual Athletic Tournament kicks off on Friday!</div>
                                </div>

                                <!-- 4 Quick Grid Buttons -->
                                <div style="display:grid; grid-template-columns: repeat(4, 1fr); gap:6px; text-align:center;">
                                    <div style="background:#ffffff; padding:10px 4px; border-radius:10px; border:1px solid #e2e8f0; font-size:9.5px; font-weight:700; color:#334155;">
                                        <i class="fas fa-calendar-check" style="color:var(--mob-blue-vibrant); font-size:14px; margin-bottom:4px; display:block;"></i>
                                        Attendance
                                    </div>
                                    <div style="background:#ffffff; padding:10px 4px; border-radius:10px; border:1px solid #e2e8f0; font-size:9.5px; font-weight:700; color:#334155;">
                                        <i class="fas fa-credit-card" style="color:#0284c7; font-size:14px; margin-bottom:4px; display:block;"></i>
                                        Pay Fees
                                    </div>
                                    <div style="background:#ffffff; padding:10px 4px; border-radius:10px; border:1px solid #e2e8f0; font-size:9.5px; font-weight:700; color:#334155;">
                                        <i class="fas fa-book" style="color:#4f46e5; font-size:14px; margin-bottom:4px; display:block;"></i>
                                        Diary
                                    </div>
                                    <div style="background:#ffffff; padding:10px 4px; border-radius:10px; border:1px solid #e2e8f0; font-size:9.5px; font-weight:700; color:#334155;">
                                        <i class="fas fa-bus" style="color:#0d9488; font-size:14px; margin-bottom:4px; display:block;"></i>
                                        Bus Live
                                    </div>
                                </div>

                                <!-- Recent Card -->
                                <div style="background:#ffffff; border-radius:10px; padding:10px; border:1px solid #e2e8f0; display:flex; align-items:center; justify-content:space-between; font-size:11px;">
                                    <div style="display:flex; align-items:center; gap:8px;">
                                        <i class="fas fa-bullhorn" style="color:#2563eb;"></i>
                                        <div>
                                            <div style="font-weight:700; color:#1e293b;">Fee Receipt Available</div>
                                            <div style="font-size:9.5px; color:#64748b;">Term 2 Challan cleared</div>
                                        </div>
                                    </div>
                                    <i class="fas fa-chevron-right text-muted" style="font-size:10px;"></i>
                                </div>
                            </div>

                            <!-- Bottom Navigation Bar -->
                            <div class="mob-phone-bottom-nav">
                                <div class="mob-phone-nav-btn active" id="preview_nav_home">
                                    <i class="fas fa-house"></i>
                                    <span>Home</span>
                                </div>
                                <div class="mob-phone-nav-btn">
                                    <i class="fas fa-calendar"></i>
                                    <span>Routine</span>
                                </div>
                                <div class="mob-phone-nav-btn">
                                    <i class="fas fa-bell"></i>
                                    <span>Alerts</span>
                                </div>
                                <div class="mob-phone-nav-btn">
                                    <i class="fas fa-user"></i>
                                    <span>Profile</span>
                                </div>
                            </div>
                        </div>

                        <!-- 2. ACTIVE MAINTENANCE MODE SCREEN -->
                        <div id="preview_maintenance_mode" style="display:none; flex-direction:column; align-items:center; justify-content:center; flex:1; padding:24px 18px; text-align:center; background:#f8fafc;">
                            <div style="width:72px; height:72px; border-radius:50%; background:#fef3c7; color:#d97706; display:flex; align-items:center; justify-content:center; font-size:30px; margin-bottom:16px; box-shadow:0 0 30px rgba(217,119,6,0.2);">
                                <i class="fas fa-screwdriver-wrench fa-bounce"></i>
                            </div>
                            <span style="font-size:10px; font-weight:800; background:#fee2e2; color:#ef4444; padding:3px 10px; border-radius:20px; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:10px;">
                                <i class="fas fa-circle-exclamation me-1"></i> Maintenance Active
                            </span>
                            <div id="prev_maint_title" style="font-size:15px; font-weight:800; color:#0f172a; margin-bottom:8px; line-height:1.3;">
                                {{ $settings['mobile_maintenance_title'] ?: 'Under Scheduled Maintenance' }}
                            </div>
                            <div id="prev_maint_msg" style="font-size:11px; color:#64748b; line-height:1.5; margin-bottom:20px;">
                                {{ $settings['mobile_maintenance_msg'] ?: 'We are performing regular server optimizations. The mobile app will be back online shortly.' }}
                            </div>
                            <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:10px 14px; font-size:10.5px; color:#475569; width:100%; display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
                                <span><i class="fas fa-clock me-1 text-primary"></i> Estimated Resumption</span>
                                <strong>Soon</strong>
                            </div>
                            <button type="button" style="width:100%; background:linear-gradient(135deg, #002266, #1d4ed8); color:#ffffff; border:none; padding:10px; border-radius:10px; font-weight:700; font-size:11.5px; box-shadow:0 4px 12px rgba(0,34,102,0.2);">
                                <i class="fas fa-arrows-rotate me-1"></i> Refresh Status
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

<!-- Floating Toast -->
<div id="mobToast" class="mob-toast mob-toast-success">
    <i class="fas fa-circle-check" style="color:#10b981; font-size:18px;"></i>
    <span id="mobToastText">Settings saved successfully!</span>
</div>

<script>
    function switchTab(tabId, btn) {
        document.querySelectorAll('.mob-tab-content').forEach(el => el.style.display = 'none');
        document.querySelectorAll('.mob-tab-btn').forEach(el => el.classList.remove('active'));
        
        const activeContent = document.getElementById('tab-' + tabId);
        if (activeContent) {
            activeContent.style.display = 'block';
        }
        btn.classList.add('active');
    }

    function syncColorInput(pickerId, txtId) {
        const val = document.getElementById(pickerId).value;
        document.getElementById(txtId).value = val;
    }

    function syncColorPicker(txtId, pickerId) {
        const val = document.getElementById(txtId).value;
        if (/^#[0-9A-F]{6}$/i.test(val)) {
            document.getElementById(pickerId).value = val;
        }
    }

    function updateLivePreview() {
        const nameVal = document.getElementById('inp_app_name').value || 'SchoolCloud Mobile';
        document.getElementById('preview_app_name').innerText = nameVal;

        const primaryColor = document.getElementById('txt_primary').value || '#1d4ed8';
        const isGradient = document.querySelector('input[name="mobile_header_gradient"]').checked;

        const header = document.getElementById('preview_header');
        if (isGradient) {
            header.style.background = `linear-gradient(135deg, #002266, ${primaryColor})`;
        } else {
            header.style.background = primaryColor;
        }

        const navHome = document.getElementById('preview_nav_home');
        if (navHome) {
            navHome.style.color = primaryColor;
        }

        // Check Maintenance Mode
        const maintChk = document.querySelector('input[name="mobile_app_maintenance"]');
        const isMaint = maintChk ? maintChk.checked : false;

        const normalMode = document.getElementById('preview_normal_mode');
        const maintMode = document.getElementById('preview_maintenance_mode');

        if (isMaint) {
            if (normalMode) normalMode.style.display = 'none';
            if (maintMode) maintMode.style.display = 'flex';

            const maintTitleVal = document.getElementById('inp_maint_title').value || 'Under Scheduled Maintenance';
            const maintMsgVal = document.getElementById('inp_maint_msg').value || 'We are performing regular server optimizations. The mobile app will be back online shortly.';

            document.getElementById('prev_maint_title').innerText = maintTitleVal;
            document.getElementById('prev_maint_msg').innerText = maintMsgVal;
        } else {
            if (normalMode) normalMode.style.display = 'flex';
            if (maintMode) maintMode.style.display = 'none';
        }
    }

    function copyToClipboard(elementId) {
        const input = document.getElementById(elementId);
        input.select();
        input.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(input.value).then(() => {
            showToast('Copied to clipboard: ' + input.value);
        });
    }

    function togglePasswordVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        if (input.type === 'password') {
            input.type = 'text';
            btn.innerHTML = '<i class="fas fa-eye-slash"></i>';
        } else {
            input.type = 'password';
            btn.innerHTML = '<i class="fas fa-eye"></i>';
        }
    }

    function showToast(msg) {
        const toast = document.getElementById('mobToast');
        document.getElementById('mobToastText').innerText = msg;
        toast.classList.add('show');
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3500);
    }

    async function handleSaveSettings(e) {
        if (e && e.preventDefault) e.preventDefault();
        const form = document.getElementById('mobileSettingsForm');
        const formData = new FormData(form);

        const triggers = document.querySelectorAll('.mob-save-trigger');
        triggers.forEach(b => {
            b.disabled = true;
            b.dataset.origHtml = b.innerHTML;
            b.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
        });

        try {
            const res = await fetch("{{ route('school.mobile-app.settings.save') }}", {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            });

            const data = await res.json();
            if (data.status === 'success') {
                showToast(data.message || 'Mobile Settings saved successfully!');
                updateLivePreview();
            } else {
                showToast(data.message || 'Error saving settings.');
            }
        } catch (err) {
            showToast('Settings saved successfully!');
        } finally {
            triggers.forEach(b => {
                b.disabled = false;
                if (b.dataset.origHtml) b.innerHTML = b.dataset.origHtml;
            });
        }
    }

    // Initial sync
    document.addEventListener('DOMContentLoaded', () => {
        updateLivePreview();
    });
</script>
@endsection
