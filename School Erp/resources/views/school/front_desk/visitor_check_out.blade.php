@extends('layouts.app')

@section('title', 'Visitor Check-Out Terminal - Front Desk')
@section('page-title', 'Visitor Check-Out Terminal')

@section('content')
<style>
    :root {
        --theme-navy: #1e3a8a;
        --theme-blue: #1d4ed8;
        --theme-blue-light: #2563eb;
        --theme-blue-subtle: #eff6ff;
        --theme-ice: #f0f7ff;
        --theme-border: #bfdbfe;
        --theme-blue-gradient: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 50%, #2563eb 100%);
    }

    /* Keyframe Animations */
    @keyframes laserSweepBlue {
        0% { top: 6%; opacity: 0.85; }
        50% { top: 92%; opacity: 1; }
        100% { top: 6%; opacity: 0.85; }
    }
    @keyframes radarSpin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    @keyframes pulseGlowBlue {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.15); opacity: 0.65; }
    }
    @keyframes rowSlideIn {
        from { opacity: 0; transform: translateY(6px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* 100% Full Screen Container */
    .terminal-container-fullscreen {
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 4px !important;
    }

    /* 3 Horizontal Metric Cards in Full-Width Blue & White Grid */
    .metric-summary-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 20px;
        width: 100%;
    }

    .metric-card {
        background: #ffffff;
        border: 1.5px solid #dbeafe;
        border-radius: 16px;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 4px 15px rgba(29, 78, 216, 0.04);
        transition: all 0.25s ease;
    }
    .metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(29, 78, 216, 0.08);
        border-color: #93c5fd;
    }
    .metric-icon-box {
        width: 50px;
        height: 50px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
        background: #eff6ff;
        color: #1d4ed8;
        border: 1.5px solid #bfdbfe;
    }

    /* =========================================================
       BLUE & WHITE OPTICAL SCANNER CONSOLE
       ========================================================= */
    .cyber-scanner-console {
        background: #ffffff;
        border: 2px solid #bfdbfe;
        border-radius: 20px;
        box-shadow: 0 12px 35px -8px rgba(29, 78, 216, 0.12), 0 2px 8px rgba(0, 0, 0, 0.02);
        overflow: hidden;
        margin-bottom: 24px;
        position: relative;
    }

    /* Console Top Header Bar (Royal Blue Gradient) */
    .console-hud-bar {
        background: var(--theme-blue-gradient);
        color: #ffffff;
        padding: 16px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }

    .console-title-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .hud-scanner-badge {
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.4);
        color: #ffffff;
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .console-title-text {
        font-size: 17px;
        font-weight: 800;
        color: #ffffff;
        letter-spacing: -0.2px;
        margin: 0;
    }

    .hud-status-pills {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .hud-live-indicator {
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.35);
        color: #ffffff;
        font-size: 12px;
        font-weight: 700;
        padding: 5px 14px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .pulse-dot-white {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #ffffff;
        box-shadow: 0 0 8px #ffffff;
        display: inline-block;
        animation: pulseGlowBlue 1.5s infinite ease-in-out;
    }

    .pulse-dot-blue {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #1d4ed8;
        box-shadow: 0 0 8px #1d4ed8;
        display: inline-block;
        animation: pulseGlowBlue 1.5s infinite ease-in-out;
    }

    /* Console Body */
    .console-body {
        padding: 28px 32px;
        background: #ffffff;
    }

    /* Scanner Viewfinder Zone in Blue & White */
    .scanner-viewfinder-zone {
        background: linear-gradient(180deg, #f8fbff 0%, #eff6ff 100%);
        border: 2px dashed #93c5fd;
        border-radius: 18px;
        padding: 24px;
        position: relative;
        margin-bottom: 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .scanner-viewfinder-zone:hover {
        border-color: #1d4ed8;
        background: #eff6ff;
        box-shadow: 0 8px 25px rgba(29, 78, 216, 0.08);
    }

    /* Blue Target Corner Reticles */
    .reticle-corner {
        position: absolute;
        width: 22px;
        height: 22px;
        border-color: #1d4ed8;
        pointer-events: none;
    }
    .reticle-tl { top: 12px; left: 12px; border-top: 3px solid; border-left: 3px solid; border-radius: 4px 0 0 0; }
    .reticle-tr { top: 12px; right: 12px; border-top: 3px solid; border-right: 3px solid; border-radius: 0 4px 0 0; }
    .reticle-bl { bottom: 12px; left: 12px; border-bottom: 3px solid; border-left: 3px solid; border-radius: 0 0 0 4px; }
    .reticle-br { bottom: 12px; right: 12px; border-bottom: 3px solid; border-right: 3px solid; border-radius: 0 0 4px 0; }

    /* Animated Blue Radar Scanner Target */
    .scanner-target-holo {
        width: 76px;
        height: 76px;
        border-radius: 50%;
        background: #ffffff;
        border: 2px solid #3b82f6;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 30px;
        color: #1d4ed8;
        margin-bottom: 12px;
        box-shadow: 0 6px 20px rgba(29, 78, 216, 0.15);
        position: relative;
    }

    .scanner-target-holo::after {
        content: '';
        position: absolute;
        inset: -6px;
        border-radius: 50%;
        border: 1.5px dashed #1d4ed8;
        animation: radarSpin 12s linear infinite;
    }

    .scanner-lead-text {
        color: #1e3a8a;
        font-weight: 800;
        font-size: 15.5px;
        letter-spacing: 0.2px;
        margin-bottom: 4px;
        text-align: center;
    }

    .scanner-sub-text {
        color: #475569;
        font-size: 13px;
        margin-bottom: 16px;
        text-align: center;
    }

    .btn-camera-trigger-cyber {
        background: #ffffff;
        border: 1.5px solid #1d4ed8;
        color: #1d4ed8;
        font-size: 13.5px;
        font-weight: 800;
        padding: 10px 24px;
        border-radius: 30px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.25s ease;
        box-shadow: 0 2px 8px rgba(29, 78, 216, 0.1);
    }

    .btn-camera-trigger-cyber:hover {
        background: var(--theme-blue-gradient);
        color: #ffffff;
        border-color: #1d4ed8;
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(29, 78, 216, 0.25);
    }

    /* Live Phone Camera Scanner Box */
    .camera-scanner-wrapper {
        display: none;
        background: #0f172a;
        border-radius: 18px;
        overflow: hidden;
        margin-bottom: 20px;
        position: relative;
        border: 2px solid #1d4ed8;
        box-shadow: 0 10px 30px rgba(29, 78, 216, 0.25);
    }

    .camera-scanner-wrapper.active {
        display: block;
    }

    #cameraScannerContainer {
        width: 100%;
        min-height: 280px;
    }

    .camera-laser-line {
        position: absolute;
        left: 4%;
        width: 92%;
        height: 3px;
        background: #38bdf8;
        box-shadow: 0 0 12px #38bdf8, 0 0 24px #38bdf8;
        border-radius: 3px;
        animation: laserSweepBlue 2s infinite ease-in-out;
        pointer-events: none;
        z-index: 10;
    }

    /* Blue & White Search Input Group */
    .cyber-input-group {
        display: flex;
        align-items: stretch;
        background: #ffffff;
        border: 2px solid #cbd5e1;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
        transition: all 0.25s ease;
    }

    .cyber-input-group:focus-within {
        border-color: #1d4ed8;
        box-shadow: 0 0 0 4px rgba(29, 78, 216, 0.15);
    }

    .cyber-input-icon {
        padding: 0 18px;
        background: #eff6ff;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #1d4ed8;
        font-size: 18px;
        border-right: 1.5px solid #dbeafe;
    }

    .cyber-text-field {
        flex: 1;
        background: transparent;
        border: none;
        outline: none;
        padding: 16px 20px;
        font-size: 15.5px;
        font-weight: 700;
        color: #0f172a;
        letter-spacing: 0.3px;
        min-width: 0;
    }

    .cyber-text-field::placeholder {
        color: #94a3b8;
        font-weight: 500;
        font-size: 14.5px;
    }

    .btn-cyber-submit {
        background: var(--theme-blue-gradient);
        color: #ffffff;
        border: none;
        padding: 0 28px;
        font-size: 14.5px;
        font-weight: 800;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }

    .btn-cyber-submit:hover {
        background: #1e40af;
        color: #ffffff;
    }

    /* Clean Blue & White Helper Strip */
    .scanner-help-strip {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 14px;
        padding: 11px 18px;
        background: #f8fbff;
        border: 1.5px solid #dbeafe;
        border-radius: 12px;
    }

    .help-item-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 12.5px;
        font-weight: 600;
        color: #334155;
    }

    .help-icon-circle {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 10.5px;
        flex-shrink: 0;
    }

    .kbd-badge {
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        color: #1d4ed8;
        font-size: 11.5px;
        font-weight: 800;
        padding: 2px 8px;
        border-radius: 6px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        font-family: inherit;
    }

    /* Scanned Visitor Dynamic Card */
    .scanned-visitor-card {
        display: none;
        background: #ffffff;
        border: 2px solid #bfdbfe;
        border-radius: 18px;
        padding: 22px 26px;
        margin-top: 24px;
        box-shadow: 0 12px 30px rgba(29, 78, 216, 0.08);
        animation: rowSlideIn 0.35s cubic-bezier(0.16, 1, 0.3, 1) both;
    }

    .visitor-profile-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
        padding-bottom: 16px;
        border-bottom: 1.5px solid #e2e8f0;
        margin-bottom: 16px;
    }

    .visitor-photo-circle {
        width: 64px;
        height: 64px;
        border-radius: 14px;
        overflow: hidden;
        border: 2px solid #1d4ed8;
        background: #eff6ff;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 4px 10px rgba(29, 78, 216, 0.1);
    }

    .visitor-photo-circle img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .details-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 14px;
        margin-bottom: 20px;
    }

    .detail-item-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 16px;
    }

    .detail-item-box .label-text {
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        margin-bottom: 2px;
    }

    .detail-item-box .val-text {
        font-size: 13.5px;
        font-weight: 800;
        color: #0f172a;
    }

    .btn-confirm-checkout {
        background: var(--theme-blue-gradient);
        color: #ffffff;
        border: none;
        border-radius: 12px;
        padding: 12px 28px;
        font-size: 14.5px;
        font-weight: 800;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
        box-shadow: 0 4px 14px rgba(29, 78, 216, 0.35);
    }

    .btn-confirm-checkout:hover {
        background: #1e40af;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(29, 78, 216, 0.45);
        color: #ffffff;
    }

    .btn-view-pass {
        background: #ffffff;
        color: #1d4ed8;
        border: 1.5px solid #bfdbfe;
        border-radius: 12px;
        padding: 12px 20px;
        font-size: 14px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        transition: all 0.2s;
    }

    .btn-view-pass:hover {
        background: #eff6ff;
        color: #1e40af;
    }

    /* Active Visitors Table Section (Full Width Blue & White) */
    .visitors-table-card {
        background: #ffffff;
        border: 1.5px solid #dbeafe;
        border-radius: 18px;
        box-shadow: 0 10px 30px -5px rgba(29, 78, 216, 0.05);
        overflow: hidden;
        margin-top: 24px;
        width: 100%;
        animation: rowSlideIn 0.4s ease both;
    }

    .table-header-bar {
        padding: 16px 24px;
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-bottom: 1.5px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }

    .table-header-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .header-icon-badge-blue {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: #eff6ff;
        color: #1d4ed8;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        border: 1px solid #bfdbfe;
    }

    .active-count-pill {
        background: #eff6ff;
        border: 1px solid #93c5fd;
        color: #1d4ed8;
        font-size: 12px;
        font-weight: 800;
        padding: 4px 12px;
        border-radius: 30px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .live-stream-badge {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #1d4ed8;
        padding: 5px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .custom-gate-table-wrapper {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .custom-gate-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        text-align: left;
    }

    .custom-gate-table th {
        background: #f8fafc;
        color: #475569;
        font-size: 11.5px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        padding: 14px 18px;
        border-bottom: 1.5px solid #e2e8f0;
        white-space: nowrap;
    }

    .custom-gate-table td {
        padding: 14px 18px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13px;
        color: #1e293b;
        background-color: #ffffff;
        white-space: nowrap;
    }

    .custom-gate-table tbody tr {
        transition: all 0.2s ease;
        animation: rowSlideIn 0.3s ease both;
    }

    .custom-gate-table tbody tr:hover td {
        background-color: #fafcff;
    }

    .pass-badge-pill {
        background: #eff6ff;
        border: 1.5px solid #93c5fd;
        color: #1d4ed8;
        font-size: 12px;
        font-weight: 800;
        padding: 4px 10px;
        border-radius: 8px;
        letter-spacing: 0.3px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
    }

    .visitor-name-box {
        display: flex;
        align-items: center;
        gap: 10px;
        white-space: nowrap;
    }

    .visitor-avatar-sm {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: #eff6ff;
        color: #1d4ed8;
        font-weight: 800;
        font-size: 13.5px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1.5px solid #bfdbfe;
        flex-shrink: 0;
        overflow: hidden;
    }

    .visitor-avatar-sm img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .visitor-main-name {
        font-weight: 800;
        font-size: 13px;
        color: #0f172a;
        line-height: 1.25;
    }

    .visitor-category-tag {
        font-size: 11px;
        font-weight: 600;
        color: #64748b;
        margin-top: 1px;
    }

    .info-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #334155;
        font-weight: 600;
        font-size: 12px;
    }

    .gate-chip {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #1e293b;
        font-weight: 700;
        font-size: 12px;
        padding: 3px 8px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .time-chip {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #1d4ed8;
        font-weight: 700;
        font-size: 12px;
        padding: 3px 8px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .status-badge-inside-sm {
        background: #eff6ff;
        border: 1.5px solid #93c5fd;
        color: #1d4ed8;
        font-size: 11px;
        font-weight: 800;
        padding: 3px 10px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .btn-modern-checkout {
        background: #ffffff;
        color: #1d4ed8;
        border: 1.5px solid #93c5fd;
        border-radius: 8px;
        padding: 6px 14px;
        font-size: 12px;
        font-weight: 800;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 2px 6px rgba(29, 78, 216, 0.08);
        white-space: nowrap;
    }

    .btn-modern-checkout:hover {
        background: var(--theme-blue-gradient);
        color: #ffffff;
        border-color: #1d4ed8;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(29, 78, 216, 0.25);
    }

    @media (max-width: 900px) {
        .metric-summary-grid {
            grid-template-columns: 1fr;
            gap: 12px;
        }
        .console-body {
            padding: 20px 16px;
        }
    }

    @media (max-width: 768px) {
        .cyber-input-group {
            flex-direction: column;
        }
        .btn-cyber-submit {
            padding: 14px;
            justify-content: center;
            border-radius: 0 0 12px 12px;
        }
        .cyber-input-icon {
            display: none;
        }
        .details-grid {
            grid-template-columns: 1fr 1fr;
        }
        .scanner-help-strip {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<div class="terminal-container-fullscreen">

    <!-- Top 3 Metrics in Sleek Horizontal Grid -->
    <div class="metric-summary-grid">
        <div class="metric-card">
            <div class="metric-icon-box">
                <i class="fas fa-id-card"></i>
            </div>
            <div>
                <div class="text-muted fw-bold small text-uppercase" style="font-size: 11px;">Total Visitors Today</div>
                <h3 class="mb-0 fw-bold text-dark" id="counterTotalToday">{{ $totalToday ?? count($insideVisitors ?? []) + count($checkedOutVisitors ?? []) }}</h3>
            </div>
        </div>
        <div class="metric-card" style="border-color: #bfdbfe;">
            <div class="metric-icon-box">
                <i class="fas fa-walking"></i>
            </div>
            <div>
                <div class="text-primary fw-bold small text-uppercase" style="font-size: 11px;">Currently Inside Campus</div>
                <h3 class="mb-0 fw-bold text-primary" id="counterInsideCount">{{ $insideCount ?? count($insideVisitors ?? []) }}</h3>
            </div>
        </div>
        <div class="metric-card">
            <div class="metric-icon-box">
                <i class="fas fa-sign-out-alt"></i>
            </div>
            <div>
                <div class="text-muted fw-bold small text-uppercase" style="font-size: 11px;">Checked-Out Today</div>
                <h3 class="mb-0 fw-bold text-dark" id="counterOutCount">{{ $checkedOutCount ?? count($checkedOutVisitors ?? []) }}</h3>
            </div>
        </div>
    </div>

    <!-- Blue & White Scanner Console -->
    <div class="cyber-scanner-console">
        <div class="console-hud-bar">
            <div class="console-title-wrap">
                <div class="hud-scanner-badge">
                    <i class="fas fa-qrcode"></i>
                </div>
                <div>
                    <div class="console-title-text">
                        Gate Pass Scanner & Check-Out Terminal
                    </div>
                    <div class="small text-white-50" style="font-size: 11.5px;">Automated Security Exit Terminal & Optical Pass Verifier</div>
                </div>
            </div>
            <div class="hud-status-pills">
                <div class="hud-live-indicator">
                    <span class="pulse-dot-white"></span> SYSTEM READY
                </div>
                <div class="hud-live-indicator">
                    <i class="fas fa-door-open"></i> EXIT GATE 1
                </div>
            </div>
        </div>

        <div class="console-body">
            <!-- Blue & White Optical Viewfinder Zone -->
            <div class="scanner-viewfinder-zone">
                <div class="reticle-corner reticle-tl"></div>
                <div class="reticle-corner reticle-tr"></div>
                <div class="reticle-corner reticle-bl"></div>
                <div class="reticle-corner reticle-br"></div>

                <div class="scanner-target-holo">
                    <i class="fas fa-qrcode"></i>
                </div>
                <div class="scanner-lead-text">OPTICAL GATE PASS SCANNER</div>
                <div class="scanner-sub-text">Point USB/Bluetooth Barcode Scanner Gun at Pass QR or use Phone Camera</div>

                <!-- Camera Trigger Button -->
                <button type="button" class="btn-camera-trigger-cyber" id="cameraToggleBtn" onclick="toggleCameraScanner()">
                    <i class="fas fa-camera" id="cameraIcon"></i>
                    <span id="cameraBtnLabel">Tap to Launch Camera Viewfinder</span>
                </button>
            </div>

            <!-- Camera Viewfinder Box -->
            <div class="camera-scanner-wrapper" id="cameraScannerWrapper">
                <div class="camera-laser-line"></div>
                <div id="cameraScannerContainer"></div>
                <div class="text-center py-2 bg-dark text-white small" style="opacity: 0.9;">
                    <i class="fas fa-crosshairs me-1 text-info"></i> Align QR Code in center to scan pass automatically
                </div>
            </div>

            <!-- Blue & White Search Input Group -->
            <div class="cyber-input-group">
                <div class="cyber-input-icon">
                    <i class="fas fa-barcode"></i>
                </div>
                <input type="text" id="passQueryInput" class="cyber-text-field" placeholder="Scan Barcode Pass or enter Pass Number / Mobile (e.g. PASS-DLEC001-0001)..." autofocus autocomplete="off">
                <button type="button" class="btn-cyber-submit" id="btnLookupAction" onclick="executePassLookup()">
                    <i class="fas fa-search"></i> Check Pass
                </button>
            </div>

            <!-- Beautiful Blue & White Helper Strip -->
            <div class="scanner-help-strip">
                <div class="help-item-pill">
                    <span class="help-icon-circle"><i class="fas fa-bolt"></i></span>
                    <span>Scanner guns auto-trigger instant verification.</span>
                </div>
                <div class="help-item-pill">
                    <span class="help-icon-circle"><i class="fas fa-keyboard"></i></span>
                    <span>Press <kbd class="kbd-badge">Enter ↵</kbd> to submit</span>
                </div>
            </div>

            <!-- Scanned Visitor Dynamic Preview Card -->
            <div class="scanned-visitor-card" id="scannedVisitorCard">
                <div class="visitor-profile-header">
                    <div class="d-flex align-items-center gap-3">
                        <div class="visitor-photo-circle" id="resPhotoContainer">
                            <i class="fas fa-user text-primary" id="resPhotoPlaceholder"></i>
                            <img src="" id="resPhotoImg" alt="Visitor" style="display: none;">
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <h4 class="fw-bold text-dark mb-0" id="resFullName" style="font-size: 18px;">Visitor Name</h4>
                                <span class="badge bg-primary px-2 py-1" id="resVisitorType" style="border-radius: 6px; font-size: 11px;">Visitor</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="pass-badge-pill" id="resPassNumber">PASS-EDUZEN-0019</span>
                                <span class="small text-muted" id="resPhone">9876543210</span>
                            </div>
                        </div>
                    </div>
                    <div id="resStatusPillContainer">
                        <span class="status-badge-inside-sm"><span class="pulse-dot-blue"></span> Inside Campus</span>
                    </div>
                </div>

                <!-- 2-Row Details Grid -->
                <div class="details-grid">
                    <div class="detail-item-box">
                        <div class="label-text">Host to Meet</div>
                        <div class="val-text text-primary" id="resHostName">Principal / Management</div>
                    </div>
                    <div class="detail-item-box">
                        <div class="label-text">Entry Gate & Count</div>
                        <div class="val-text" id="resGateCount">Main Gate 1 (1 Head)</div>
                    </div>
                    <div class="detail-item-box">
                        <div class="label-text">Check-In Time</div>
                        <div class="val-text text-primary" id="resCheckInTime">10:24 AM</div>
                    </div>
                    <div class="detail-item-box">
                        <div class="label-text">Purpose</div>
                        <div class="val-text" id="resPurpose">Official Meeting</div>
                    </div>
                </div>

                <!-- Action Button Controls -->
                <div class="d-flex flex-wrap align-items-center justify-content-end gap-3 pt-2">
                    <a href="#" id="resPrintPassLink" target="_blank" class="btn-view-pass">
                        <i class="fas fa-print"></i> View / Print Pass
                    </a>
                    <button type="button" id="btnConfirmCheckOut" class="btn-confirm-checkout" onclick="executeCheckOutConfirm()">
                        <i class="fas fa-sign-out-alt"></i> Confirm Check-Out
                    </button>
                </div>
            </div>

        </div>
    </div>

    <!-- Active Inside Visitors List (Real-Time Dynamic Synchronized) -->
    @php
        $activeInsideCount = count($insideVisitors ?? []);
    @endphp

    <div class="visitors-table-card" id="insideVisitorsCard" style="{{ $activeInsideCount > 0 ? '' : 'display: none;' }}">
        <div class="table-header-bar">
            <div class="table-header-left">
                <div class="header-icon-badge-blue">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <span class="fw-bold text-dark" style="font-size: 15.5px; letter-spacing: -0.2px;">Visitors Currently Inside Campus</span>
                    <div class="small text-muted" style="font-size: 11.5px;">Real-time Gate Attendance Tracker</div>
                </div>
                <div class="active-count-pill ms-2" id="tableInsideBadge">
                    <span class="pulse-dot-blue"></span>
                    <span id="tableInsideText">{{ $activeInsideCount }} Inside</span>
                </div>
            </div>
            <div class="live-stream-badge">
                <i class="fas fa-satellite-dish"></i> Live Stream
            </div>
        </div>

        <div class="custom-gate-table-wrapper">
            <table class="custom-gate-table">
                <thead>
                    <tr>
                        <th style="min-width: 160px;">Pass No.</th>
                        <th style="min-width: 200px;">Visitor Name</th>
                        <th style="min-width: 130px;">Mobile</th>
                        <th style="min-width: 170px;">Whom to Meet</th>
                        <th style="min-width: 130px;">Gate</th>
                        <th style="min-width: 120px;">Entry Time</th>
                        <th style="min-width: 110px;">Status</th>
                        <th style="min-width: 120px; text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody id="insideVisitorsTableBody">
                    @forelse($insideVisitors ?? [] as $iv)
                    <tr id="visitor-row-{{ $iv->id }}">
                        <td>
                            <div class="pass-badge-pill">
                                <i class="fas fa-barcode text-primary"></i>
                                <span>{{ $iv->pass_number }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="visitor-name-box">
                                <div class="visitor-avatar-sm">
                                    @if($iv->photo_url)
                                        <img src="{{ $iv->photo_url }}" alt="Photo">
                                    @else
                                        {{ strtoupper(substr($iv->full_name, 0, 1)) }}
                                    @endif
                                </div>
                                <div>
                                    <div class="visitor-main-name">{{ $iv->full_name }}</div>
                                    <div class="visitor-category-tag">{{ $iv->visitor_type }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="info-chip">
                                <i class="fas fa-phone-alt text-primary" style="font-size: 11px;"></i>
                                <span>{{ $iv->mobile_number }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="info-chip">
                                <i class="fas fa-user-tie text-primary" style="font-size: 11px;"></i>
                                <span>{{ $iv->host_name ?: $iv->whom_to_meet_type }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="gate-chip">
                                <i class="fas fa-door-open text-muted"></i>
                                <span>{{ $iv->security_gate }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="time-chip">
                                <i class="fas fa-clock"></i>
                                <span>{{ $iv->check_in_at ? $iv->check_in_at->format('h:i A') : $iv->created_at->format('h:i A') }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="status-badge-inside-sm">
                                <span class="pulse-dot-blue"></span> Inside
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <button type="button" class="btn-modern-checkout" onclick="quickCheckOutRow('{{ $iv->id }}', '{{ $iv->pass_number }}')">
                                <i class="fas fa-sign-out-alt"></i> Check-Out
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr id="emptyRowInside">
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fas fa-user-check text-muted mb-2 font-size-24 d-block" style="opacity: 0.4;"></i>
                            No visitors currently inside campus. All visitors have checked out.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Html5Qrcode Library for Phone & Web Camera Scanning -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
    let activeScannedVisitorId = null;
    let html5QrScanner = null;
    let isCameraScanning = false;

    function playBeepSound(type = 'success') {
        try {
            const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.type = 'sine';
            if (type === 'success') {
                osc.frequency.setValueAtTime(880, audioCtx.currentTime);
                osc.frequency.exponentialRampToValueAtTime(1200, audioCtx.currentTime + 0.15);
            } else {
                osc.frequency.setValueAtTime(300, audioCtx.currentTime);
                osc.frequency.exponentialRampToValueAtTime(150, audioCtx.currentTime + 0.2);
            }
            gain.gain.setValueAtTime(0.2, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.2);
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            osc.start();
            osc.stop(audioCtx.currentTime + 0.2);
        } catch(e) {}
    }

    const passInput = document.getElementById('passQueryInput');
    if (passInput) {
        passInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                executePassLookup();
            }
        });
    }

    function executePassLookup(passedQuery = null) {
        const query = (passedQuery || passInput.value).trim();
        if (!query) {
            passInput.focus();
            return;
        }

        const btn = document.getElementById('btnLookupAction');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Checking...';

        fetch("{{ route('school.front-desk.visitor.scan-lookup') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ query: query })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-search"></i> Check Pass';

            if (data.found && data.visitor) {
                playBeepSound('success');
                displayScannedVisitorCard(data);
            } else {
                playBeepSound('error');
                alert(data.message || 'No visitor record found for pass / phone: ' + query);
                passInput.select();
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-search"></i> Check Pass';
            console.error(err);
            alert('Error communicating with server.');
        });
    }

    function displayScannedVisitorCard(data) {
        const v = data.visitor;
        activeScannedVisitorId = v.id;

        document.getElementById('resFullName').textContent = v.full_name;
        document.getElementById('resVisitorType').textContent = v.visitor_type || 'Visitor';
        document.getElementById('resPassNumber').textContent = v.pass_number;
        document.getElementById('resPhone').textContent = v.mobile_number;
        document.getElementById('resHostName').textContent = (v.host_name ? v.host_name + ' (' + v.whom_to_meet_type + ')' : v.whom_to_meet_type);
        document.getElementById('resGateCount').textContent = `${v.security_gate} (${v.entourage_count || 1} Person)`;
        document.getElementById('resCheckInTime').textContent = data.in_time;
        document.getElementById('resPurpose').textContent = v.visit_purpose;
        document.getElementById('resPrintPassLink').href = data.print_url;

        const photoImg = document.getElementById('resPhotoImg');
        const photoIcon = document.getElementById('resPhotoPlaceholder');
        if (v.photo_url || v.photo_path) {
            photoImg.src = v.photo_url || ('/storage/' + v.photo_path);
            photoImg.style.display = 'block';
            photoIcon.style.display = 'none';
        } else {
            photoImg.style.display = 'none';
            photoIcon.style.display = 'block';
        }

        const pillContainer = document.getElementById('resStatusPillContainer');
        const checkoutBtn = document.getElementById('btnConfirmCheckOut');

        if (v.status === 'checked_out') {
            pillContainer.innerHTML = `<span class="status-badge-inside-sm" style="background:#f1f5f9; border-color:#cbd5e1; color:#475569;"><i class="fas fa-check-double text-muted"></i> Checked-Out at ${data.out_time}</span>`;
            checkoutBtn.style.display = 'none';
        } else {
            pillContainer.innerHTML = `<span class="status-badge-inside-sm"><span class="pulse-dot-blue"></span> Inside Campus</span>`;
            checkoutBtn.style.display = 'inline-flex';
        }

        document.getElementById('scannedVisitorCard').style.display = 'block';
        document.getElementById('scannedVisitorCard').scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function executeCheckOutConfirm() {
        if (!activeScannedVisitorId) return;

        const btn = document.getElementById('btnConfirmCheckOut');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Processing...';

        fetch("{{ route('school.front-desk.visitor.check-out.process') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ visitor_id: activeScannedVisitorId })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-sign-out-alt"></i> Confirm Check-Out';

            if (data.success) {
                playBeepSound('success');
                alert(data.message);
                document.getElementById('resStatusPillContainer').innerHTML = `<span class="status-badge-inside-sm" style="background:#f1f5f9; border-color:#cbd5e1; color:#475569;"><i class="fas fa-check-double text-primary"></i> Checked-Out at ${data.out_time || 'Just now'}</span>`;
                btn.style.display = 'none';

                const row = document.getElementById('visitor-row-' + activeScannedVisitorId);
                if (row) row.remove();

                updateInsideCountBadge(-1);
            } else {
                alert(data.message || 'Check-out processing failed.');
            }
        });
    }

    function quickCheckOutRow(id, passNo) {
        if (!confirm(`Confirm Check-Out for Visitor ${passNo}?`)) return;

        fetch("{{ route('school.front-desk.visitor.check-out.process') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ visitor_id: id })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                playBeepSound('success');
                const row = document.getElementById('visitor-row-' + id);
                if (row) row.remove();
                updateInsideCountBadge(-1);
            } else {
                alert(data.message || 'Failed to check-out.');
            }
        });
    }

    function updateInsideCountBadge(diff) {
        const textSpan = document.getElementById('tableInsideText');
        const counter = document.getElementById('counterInsideCount');
        const counterOut = document.getElementById('counterOutCount');
        const card = document.getElementById('insideVisitorsCard');

        let current = parseInt(textSpan.textContent.replace(/[^0-9]/g, '') || '0');
        let next = Math.max(0, current + diff);
        textSpan.textContent = `${next} Inside`;
        if (counter) counter.textContent = next;

        if (counterOut && diff < 0) {
            let outVal = parseInt(counterOut.textContent || '0');
            counterOut.textContent = outVal + 1;
        }

        if (next === 0) {
            if (card) card.style.display = 'none';
        } else {
            if (card) card.style.display = 'block';
        }
    }

    function fetchLiveTerminalData() {
        fetch("{{ route('school.front-desk.visitor.live-data') }}")
            .then(res => res.json())
            .then(data => {
                document.getElementById('counterTotalToday').textContent = data.total_today;
                document.getElementById('counterInsideCount').textContent = data.inside_count;
                document.getElementById('counterOutCount').textContent = data.checked_out_count;

                const textSpan = document.getElementById('tableInsideText');
                const card = document.getElementById('insideVisitorsCard');

                if (textSpan) textSpan.textContent = `${data.inside_count} Inside`;
                if (card) card.style.display = data.inside_count > 0 ? 'block' : 'none';

                renderInsideTable(data.inside_visitors);
            })
            .catch(() => {});
    }

    function renderInsideTable(visitors) {
        const tbody = document.getElementById('insideVisitorsTableBody');
        if (!tbody || !visitors) return;

        if (visitors.length === 0) {
            tbody.innerHTML = `
                <tr id="emptyRowInside">
                    <td colspan="8" class="text-center py-5 text-muted">
                        <i class="fas fa-user-check text-muted mb-2 font-size-24 d-block" style="opacity: 0.4;"></i>
                        No visitors currently inside campus. All visitors have checked out.
                    </td>
                </tr>
            `;
            return;
        }

        let html = '';
        visitors.forEach(v => {
            const avatarContent = v.photo_url 
                ? `<img src="${v.photo_url}" alt="Photo">` 
                : (v.full_name ? v.full_name.charAt(0).toUpperCase() : 'V');

            html += `
                <tr id="visitor-row-${v.id}">
                    <td>
                        <div class="pass-badge-pill">
                            <i class="fas fa-barcode text-primary"></i>
                            <span>${v.pass_number}</span>
                        </div>
                    </td>
                    <td>
                        <div class="visitor-name-box">
                            <div class="visitor-avatar-sm">
                                ${avatarContent}
                            </div>
                            <div>
                                <div class="visitor-main-name">${v.full_name}</div>
                                <div class="visitor-category-tag">${v.visitor_type}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="info-chip">
                            <i class="fas fa-phone-alt text-primary" style="font-size: 11px;"></i>
                            <span>${v.mobile_number}</span>
                        </div>
                    </td>
                    <td>
                        <div class="info-chip">
                            <i class="fas fa-user-tie text-primary" style="font-size: 11px;"></i>
                            <span>${v.host_name}</span>
                        </div>
                    </td>
                    <td>
                        <div class="gate-chip">
                            <i class="fas fa-door-open text-muted"></i>
                            <span>${v.security_gate}</span>
                        </div>
                    </td>
                    <td>
                        <div class="time-chip">
                            <i class="fas fa-clock"></i>
                            <span>${v.in_time}</span>
                        </div>
                    </td>
                    <td>
                        <span class="status-badge-inside-sm">
                            <span class="pulse-dot-blue"></span> Inside
                        </span>
                    </td>
                    <td style="text-align: right;">
                        <button type="button" class="btn-modern-checkout" onclick="quickCheckOutRow('${v.id}', '${v.pass_number}')">
                            <i class="fas fa-sign-out-alt"></i> Check-Out
                        </button>
                    </td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
    }

    setInterval(fetchLiveTerminalData, 8000);

    function toggleCameraScanner() {
        const wrapper = document.getElementById('cameraScannerWrapper');
        const triggerBtn = document.getElementById('cameraToggleBtn');
        const btnLabel = document.getElementById('cameraBtnLabel');
        const icon = document.getElementById('cameraIcon');

        if (isCameraScanning) {
            if (html5QrScanner) {
                html5QrScanner.stop().then(() => {
                    wrapper.classList.remove('active');
                    btnLabel.textContent = 'Tap to Launch Camera Viewfinder';
                    icon.className = 'fas fa-camera';
                    isCameraScanning = false;
                });
            }
        } else {
            wrapper.classList.add('active');
            btnLabel.textContent = 'Close Camera Scanner';
            icon.className = 'fas fa-times';

            html5QrScanner = new Html5Qrcode("cameraScannerContainer");
            html5QrScanner.start(
                { facingMode: "environment" },
                { fps: 15, qrbox: { width: 260, height: 260 } },
                (decodedText) => {
                    passInput.value = decodedText;
                    executePassLookup(decodedText);
                    toggleCameraScanner();
                },
                () => {}
            ).catch(err => {
                alert('Camera permission denied or camera not found: ' + err);
                wrapper.classList.remove('active');
                btnLabel.textContent = 'Tap to Launch Camera Viewfinder';
                icon.className = 'fas fa-camera';
                isCameraScanning = false;
            });
            isCameraScanning = true;
        }
    }
</script>
@endsection
