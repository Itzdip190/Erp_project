@extends('layouts.app')

@section('title', 'Visitor Requests - Front Desk')
@section('page-title', 'Visitor Requests & Approvals')

@section('content')
<style>
    :root {
        --theme-blue: #1d4ed8;
        --theme-blue-gradient: linear-gradient(135deg, #1e40af 0%, #1d4ed8 50%, #2563eb 100%);
        --theme-blue-hover: #1e40af;
        --theme-blue-light: #eff6ff;
        --theme-blue-border: #bfdbfe;
    }

    /* Top 4 Stat Cards - 100% Guaranteed Horizontal Responsive Grid */
    .stat-cards-grid {
        display: grid !important;
        grid-template-columns: repeat(4, 1fr) !important;
        gap: 16px !important;
        margin-bottom: 24px !important;
        width: 100% !important;
    }

    @media (max-width: 1100px) {
        .stat-cards-grid {
            grid-template-columns: repeat(2, 1fr) !important;
        }
    }

    @media (max-width: 576px) {
        .stat-cards-grid {
            grid-template-columns: 1fr !important;
        }
    }

    .stat-badge-box {
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        transition: all 0.25s ease;
    }

    .stat-badge-box:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
        border-color: #cbd5e1;
    }

    .stat-icon-wrap {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .stat-icon-pending { background: #fef3c7; color: #d97706; }
    .stat-icon-approved { background: #dcfce7; color: #16a34a; }
    .stat-icon-rejected { background: #fee2e2; color: #dc2626; }
    .stat-icon-total { background: #eff6ff; color: #2563eb; }

    .stat-label-text {
        font-size: 11.5px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        margin-bottom: 2px;
    }

    .stat-number-val {
        font-size: 24px;
        font-weight: 900;
        color: #0f172a;
        line-height: 1;
    }

    /* Main Container Card */
    .requests-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        box-shadow: 0 10px 30px -5px rgba(29, 78, 216, 0.08), 0 4px 12px rgba(0, 0, 0, 0.03);
        overflow: hidden;
        margin-bottom: 40px;
    }

    .requests-card-header {
        background: var(--theme-blue-gradient);
        color: #ffffff;
        padding: 18px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }

    .requests-card-header .header-title {
        font-size: 17px;
        font-weight: 800;
        letter-spacing: -0.2px;
        display: flex;
        align-items: center;
        gap: 12px;
        color: #ffffff;
        margin: 0;
    }

    .header-icon-circle {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        color: #ffffff;
    }

    .btn-header-action {
        background: #ffffff;
        color: var(--theme-blue);
        border: 1.5px solid #ffffff;
        border-radius: 20px;
        padding: 6px 16px;
        font-size: 12.5px;
        font-weight: 800;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        transition: all 0.2s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        text-decoration: none;
    }

    .btn-header-action:hover {
        background: #f8fafc;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.16);
        color: #1e40af;
    }

    /* Custom Filter Tabs - Clean Horizontal Bar */
    .nav-tabs-custom {
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        list-style: none !important;
        margin: 0 !important;
        padding: 0 20px !important;
        border-bottom: 2px solid #e2e8f0 !important;
        background: #f8fafc !important;
        gap: 8px !important;
        overflow-x: auto;
    }

    .nav-tabs-custom .nav-item {
        list-style: none !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .nav-tabs-custom .nav-link {
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
        padding: 13px 18px !important;
        font-size: 13px !important;
        font-weight: 700 !important;
        color: #64748b !important;
        text-decoration: none !important;
        border: none !important;
        border-bottom: 3px solid transparent !important;
        background: transparent !important;
        cursor: pointer !important;
        transition: all 0.2s ease !important;
        white-space: nowrap;
    }

    .nav-tabs-custom .nav-link:hover {
        color: var(--theme-blue) !important;
    }

    .nav-tabs-custom .nav-link.active {
        color: var(--theme-blue) !important;
        border-bottom-color: var(--theme-blue) !important;
        background: #ffffff !important;
        border-radius: 8px 8px 0 0 !important;
        font-weight: 800 !important;
    }

    /* Search & Filter Bar */
    .search-filter-bar {
        padding: 14px 20px;
        background: #ffffff;
        border-bottom: 1px solid #e2e8f0;
    }

    .search-filter-form {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .search-input-group {
        display: flex;
        align-items: center;
        background: #f8fafc;
        border: 1.5px solid #cbd5e1;
        border-radius: 10px;
        padding: 4px 12px;
        flex: 1;
        min-width: 280px;
        transition: all 0.2s;
    }

    .search-input-group:focus-within {
        border-color: var(--theme-blue);
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.12);
    }

    .search-icon {
        color: #94a3b8;
        font-size: 14px;
        margin-right: 8px;
    }

    .search-input {
        border: none;
        background: transparent;
        font-size: 13px;
        color: #0f172a;
        width: 100%;
        outline: none;
        padding: 4px 0;
    }

    .btn-clear-search {
        color: #94a3b8;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        padding: 2px 6px;
        border-radius: 4px;
        background: #e2e8f0;
        margin-left: 6px;
        white-space: nowrap;
    }

    .btn-clear-search:hover {
        background: #cbd5e1;
        color: #475569;
    }

    .btn-filter-submit {
        background: var(--theme-blue);
        color: #ffffff;
        border: none;
        border-radius: 10px;
        padding: 8px 20px;
        font-size: 13px;
        font-weight: 800;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }

    .btn-filter-submit:hover {
        background: #1e40af;
        transform: translateY(-1px);
        box-shadow: 0 3px 8px rgba(29, 78, 216, 0.3);
    }

    /* Table Styling with Beautiful Typography */
    .requests-table {
        margin: 0;
        width: 100%;
        border-collapse: collapse;
    }

    .requests-table thead th {
        background: #f8fafc;
        color: #475569;
        font-size: 11.5px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 14px 18px;
        border-bottom: 1.5px solid #e2e8f0;
        border-top: none;
        white-space: nowrap;
    }

    .requests-table tbody td {
        padding: 16px 18px;
        font-size: 13px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        line-height: 1.4;
    }

    .requests-table tbody tr:hover {
        background-color: #fafcff;
    }

    .visitor-photo-circle {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: #e2e8f0;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1.5px solid #cbd5e1;
        flex-shrink: 0;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }

    .visitor-photo-circle img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .visitor-photo-circle i {
        color: #94a3b8;
        font-size: 20px;
    }

    .badge-type-pill {
        display: inline-block;
        background: #f1f5f9;
        color: #1e40af;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 2px 7px;
        font-size: 10.5px;
        font-weight: 800;
        text-transform: capitalize;
    }

    .pass-num-pill {
        font-size: 10.5px;
        font-weight: 800;
        color: #dc2626;
    }

    .badge-headcount {
        display: inline-block;
        background: #0f172a;
        color: #ffffff;
        padding: 3px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 800;
    }

    .badge-status {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .badge-status-pending { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
    .badge-status-approved, .badge-status-checked_in { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    .badge-status-checked_out { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
    .badge-status-rejected { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }

    /* Action Buttons */
    .btn-action-approve {
        background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
        color: #ffffff;
        border: none;
        border-radius: 8px;
        padding: 7px 14px;
        font-size: 12px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 2px 6px rgba(22, 163, 74, 0.2);
        white-space: nowrap;
    }

    .btn-action-approve:hover {
        background: #15803d;
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(22, 163, 74, 0.35);
    }

    .btn-action-reject {
        background: #ffffff;
        color: #dc2626;
        border: 1.5px solid #fca5a5;
        border-radius: 8px;
        padding: 6px 12px;
        font-size: 12px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        cursor: pointer;
        transition: all 0.2s;
        white-space: nowrap;
    }

    .btn-action-reject:hover {
        background: #fee2e2;
        color: #b91c1c;
        border-color: #f87171;
    }

    .btn-action-icon {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        border: 1.5px solid #cbd5e1;
        background: #ffffff;
        color: #475569;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        flex-shrink: 0;
    }

    .btn-action-icon:hover {
        background: #f1f5f9;
        color: var(--theme-blue);
        border-color: #94a3b8;
    }

    /* Fixed Custom Modal Overlays */
    .custom-modal-overlay {
        display: none !important;
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        background: rgba(15, 23, 42, 0.75) !important;
        backdrop-filter: blur(8px) !important;
        -webkit-backdrop-filter: blur(8px) !important;
        z-index: 999999 !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 20px !important;
        box-sizing: border-box !important;
    }

    .custom-modal-overlay.active {
        display: flex !important;
    }

    .modal-box-card {
        background: #ffffff;
        border-radius: 22px;
        width: 100%;
        max-width: 540px;
        overflow: hidden;
        box-shadow: 0 25px 60px -10px rgba(15, 23, 42, 0.45);
        animation: fadeInScale 0.25s ease both;
    }

    @keyframes fadeInScale {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }

    .modal-hdr-custom {
        padding: 16px 22px;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .modal-hdr-danger {
        background: linear-gradient(135deg, #b91c1c 0%, #dc2626 100%);
    }

    .modal-hdr-primary {
        background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #2563eb 100%);
    }

    .modal-close-x {
        background: rgba(255,255,255,0.15);
        border: none;
        color: #ffffff;
        width: 30px;
        height: 30px;
        border-radius: 6px;
        font-size: 16px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .modal-close-x:hover {
        background: rgba(239,68,68,0.85);
    }

    /* Details Profile Modal - Custom CSS Grid Layout */
    .profile-hero-bar {
        background: linear-gradient(135deg, #f8fafc 0%, #edf2f7 100%);
        border-bottom: 1.5px solid #e2e8f0;
        padding: 18px 24px;
        display: flex;
        align-items: center;
        gap: 18px;
    }

    .profile-avatar-box {
        width: 72px;
        height: 72px;
        border-radius: 16px;
        border: 2.5px solid #2563eb;
        box-shadow: 0 6px 16px rgba(37, 99, 235, 0.2);
        background: #ffffff;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .profile-avatar-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .profile-avatar-box i {
        font-size: 32px;
        color: #94a3b8;
    }

    .profile-info-grid {
        display: grid !important;
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 14px !important;
        padding: 20px 24px !important;
    }

    @media (max-width: 576px) {
        .profile-info-grid {
            grid-template-columns: 1fr !important;
        }
    }

    .profile-field-box {
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        padding: 10px 14px;
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .profile-field-box.full-span {
        grid-column: 1 / -1 !important;
    }

    .field-title-label {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .field-title-label i {
        color: #2563eb;
        font-size: 11px;
    }

    .field-value-text {
        font-size: 13px;
        font-weight: 700;
        color: #0f172a;
        word-break: break-word;
    }

    /* QR Generator Modal */
    .qr-modal-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #2563eb 100%);
        color: #ffffff;
        padding: 18px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .qr-modal-icon-badge {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(8px);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        color: #60a5fa;
    }

    .qr-modal-title {
        font-size: 16px;
        font-weight: 800;
        margin: 0;
        color: #ffffff;
    }

    .qr-modal-sub {
        font-size: 11.5px;
        color: #93c5fd;
        margin: 0;
    }

    .qr-gold-stripe {
        background: linear-gradient(90deg, #f59e0b 0%, #fbbf24 50%, #f59e0b 100%);
        height: 4px;
        width: 100%;
    }

    .qr-showcase-box {
        background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
        border: 1.5px solid #e2e8f0;
        border-radius: 18px;
        padding: 22px 20px;
        text-align: center;
    }

    .qr-smart-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #0f172a;
        color: #38bdf8;
        font-size: 10.5px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        padding: 4px 12px;
        border-radius: 20px;
        margin-bottom: 10px;
    }

    .qr-school-heading {
        font-size: 18px;
        font-weight: 900;
        color: #0f172a;
        margin-bottom: 4px;
    }

    .qr-code-pill-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 2px 8px;
        font-size: 11px;
        font-weight: 800;
        color: #1e40af;
        margin-bottom: 16px;
    }

    .qr-target-container {
        position: relative;
        display: inline-block;
        padding: 14px;
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 12px 30px -8px rgba(37, 99, 235, 0.22);
        margin-bottom: 14px;
        border: 2px solid #e0e7ff;
    }

    .scanner-corner {
        position: absolute;
        width: 20px;
        height: 20px;
        border-color: #2563eb;
        border-style: solid;
        pointer-events: none;
    }

    .corner-tl { top: 6px; left: 6px; border-width: 3px 0 0 3px; border-radius: 6px 0 0 0; }
    .corner-tr { top: 6px; right: 6px; border-width: 3px 3px 0 0; border-radius: 0 6px 0 0; }
    .corner-bl { bottom: 6px; left: 6px; border-width: 0 0 3px 3px; border-radius: 0 0 0 6px; }
    .corner-br { bottom: 6px; right: 6px; border-width: 0 3px 3px 0; border-radius: 0 0 6px 0; }

    .qr-target-img {
        width: 175px;
        height: 175px;
        display: block;
        border-radius: 8px;
    }

    .qr-scan-badge-prompt {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #1d4ed8;
        padding: 5px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 800;
        margin-bottom: 14px;
    }

    .qr-step-journey {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        margin-top: 4px;
    }

    .step-pill-item {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 5px 10px;
        font-size: 11px;
        font-weight: 700;
        color: #334155;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .step-pill-item i { color: #2563eb; }
    .step-arrow-divider { color: #94a3b8; font-size: 10px; }

    .url-copy-wrapper {
        background: #f8fafc;
        border: 1.5px solid #cbd5e1;
        border-radius: 12px;
        padding: 4px 5px 4px 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .url-copy-wrapper:focus-within {
        border-color: #2563eb;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    .url-copy-input {
        flex: 1;
        border: none;
        background: transparent;
        font-size: 12.5px;
        font-weight: 600;
        color: #1e293b;
        outline: none;
        padding: 4px 0;
    }

    .btn-copy-gradient {
        background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
        color: #ffffff;
        border: none;
        border-radius: 8px;
        padding: 7px 16px;
        font-size: 12px;
        font-weight: 800;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-modal-print {
        flex: 1.2;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: #ffffff;
        border: none;
        border-radius: 10px;
        padding: 10px 16px;
        font-size: 12.5px;
        font-weight: 800;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
    }

    .btn-modal-download {
        flex: 1;
        background: #ffffff;
        color: #334155;
        border: 1.5px solid #cbd5e1;
        border-radius: 10px;
        padding: 9px 14px;
        font-size: 12px;
        font-weight: 800;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .btn-modal-open {
        flex: 1;
        background: var(--theme-blue-light);
        color: var(--theme-blue);
        border: 1.5px solid var(--theme-blue-border);
        border-radius: 10px;
        padding: 9px 14px;
        font-size: 12px;
        font-weight: 800;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        text-decoration: none;
    }

    /* Empty State */
    .empty-box-card {
        padding: 60px 20px;
        text-align: center;
    }

    .empty-icon-circle {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: #eff6ff;
        color: #3b82f6;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 30px;
        margin-bottom: 16px;
    }
</style>

<div class="container-fluid px-0">

    <!-- Alerts -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center justify-content-between shadow-sm mb-4" role="alert" style="border-radius: 12px; border: 1.5px solid #86efac; background: #f0fdf4;">
        <div class="d-flex align-items-center text-success-emphasis fw-semibold">
            <i class="fas fa-check-circle me-2 font-size-18 text-success"></i> {{ session('success') }}
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Top Summary Stats - 100% Horizontal Grid -->
    <div class="stat-cards-grid">
        <div class="stat-badge-box">
            <div class="stat-icon-wrap stat-icon-pending">
                <i class="fas fa-clock"></i>
            </div>
            <div>
                <div class="stat-label-text">Pending Requests</div>
                <div class="stat-number-val">{{ $pendingCount }}</div>
            </div>
        </div>
        <div class="stat-badge-box">
            <div class="stat-icon-wrap stat-icon-approved">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <div class="stat-label-text">Approved Requests</div>
                <div class="stat-number-val">{{ $approvedCount }}</div>
            </div>
        </div>
        <div class="stat-badge-box">
            <div class="stat-icon-wrap stat-icon-rejected">
                <i class="fas fa-times-circle"></i>
            </div>
            <div>
                <div class="stat-label-text">Rejected Requests</div>
                <div class="stat-number-val">{{ $rejectedCount }}</div>
            </div>
        </div>
        <div class="stat-badge-box">
            <div class="stat-icon-wrap stat-icon-total">
                <i class="fas fa-qrcode"></i>
            </div>
            <div>
                <div class="stat-label-text">Total QR Self-Scans</div>
                <div class="stat-number-val">{{ $totalCount }}</div>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="requests-card">
        <!-- Card Header -->
        <div class="requests-card-header">
            <div class="header-title">
                <div class="header-icon-circle">
                    <i class="fas fa-inbox"></i>
                </div>
                <span>Visitor Requests (QR Self-Registrations)</span>
            </div>

            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn-header-action" onclick="openQrModal()">
                    <i class="fas fa-qrcode"></i> Generate Scanner / QR Poster
                </button>
                <a href="{{ route('school.front-desk.visitor-registration') }}" class="btn-header-action" style="background: rgba(255,255,255,0.15); color: #fff; border-color: rgba(255,255,255,0.4);">
                    <i class="fas fa-plus"></i> Front Desk Check-In
                </a>
            </div>
        </div>

        <!-- Filter Tabs -->
        <ul class="nav-tabs-custom">
            <li class="nav-item">
                <a class="nav-link {{ $tab === 'pending' ? 'active' : '' }}" href="{{ route('school.front-desk.visitor-requests', ['tab' => 'pending', 'search' => $search]) }}">
                    <i class="fas fa-clock text-warning"></i> Pending Requests
                    @if($pendingCount > 0)
                        <span class="badge rounded-pill bg-danger text-white ms-1" style="font-size: 10px;">{{ $pendingCount }}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tab === 'approved' ? 'active' : '' }}" href="{{ route('school.front-desk.visitor-requests', ['tab' => 'approved', 'search' => $search]) }}">
                    <i class="fas fa-check-circle text-success"></i> Approved Requests ({{ $approvedCount }})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tab === 'rejected' ? 'active' : '' }}" href="{{ route('school.front-desk.visitor-requests', ['tab' => 'rejected', 'search' => $search]) }}">
                    <i class="fas fa-times-circle text-danger"></i> Rejected ({{ $rejectedCount }})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tab === 'all' ? 'active' : '' }}" href="{{ route('school.front-desk.visitor-requests', ['tab' => 'all', 'search' => $search]) }}">
                    <i class="fas fa-list text-primary"></i> All Self-Scans ({{ $totalCount }})
                </a>
            </li>
        </ul>

        <!-- Search Bar -->
        <div class="search-filter-bar">
            <form method="GET" action="{{ route('school.front-desk.visitor-requests') }}" class="search-filter-form">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <div class="search-input-group">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" name="search" class="search-input" placeholder="Search by Visitor Name, Mobile, Email, Pass # or Host..." value="{{ $search }}">
                    @if($search)
                        <a href="{{ route('school.front-desk.visitor-requests', ['tab' => $tab]) }}" class="btn-clear-search">Clear</a>
                    @endif
                </div>
                <button type="submit" class="btn-filter-submit">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </form>
        </div>

        <!-- Table Content -->
        <div class="table-responsive">
            <table class="requests-table">
                <thead>
                    <tr>
                        <th style="min-width: 220px;">Visitor Details</th>
                        <th style="min-width: 190px;">Contact</th>
                        <th style="min-width: 180px;">Whom To Meet</th>
                        <th style="min-width: 200px;">Purpose & Remarks</th>
                        <th style="min-width: 140px;">Entourage & Gate</th>
                        <th style="min-width: 130px;">Requested At</th>
                        <th style="min-width: 110px;">Status</th>
                        <th style="text-align: right; min-width: 180px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                    <tr id="row-request-{{ $req->id }}">
                        <!-- Visitor Details -->
                        <td>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div class="visitor-photo-circle">
                                    @if($req->photo_url)
                                        <img src="{{ $req->photo_url }}" alt="{{ $req->full_name }}">
                                    @else
                                        <i class="fas fa-user"></i>
                                    @endif
                                </div>
                                <div style="display: flex; flex-direction: column; gap: 3px;">
                                    <div style="font-weight: 800; color: #0f172a; font-size: 13.5px; line-height: 1.2;">{{ $req->full_name }}</div>
                                    <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                                        <span class="badge-type-pill">{{ $req->visitor_type }}</span>
                                        <span class="pass-num-pill">#{{ $req->pass_number }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Contact -->
                        <td>
                            <div style="display: flex; flex-direction: column; gap: 3px;">
                                <div style="font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 6px; font-size: 12.5px;">
                                    <i class="fas fa-phone-alt text-primary" style="font-size: 11px;"></i> {{ $req->mobile_number }}
                                </div>
                                @if($req->email)
                                <div style="color: #64748b; font-size: 12px; display: flex; align-items: center; gap: 6px; word-break: break-all;" title="{{ $req->email }}">
                                    <i class="fas fa-envelope text-muted" style="font-size: 11px;"></i> {{ $req->email }}
                                </div>
                                @endif
                            </div>
                        </td>

                        <!-- Whom to meet -->
                        <td>
                            <div style="display: flex; flex-direction: column; gap: 2px;">
                                <div style="font-weight: 800; color: #0f172a; font-size: 13px;">{{ $req->host_name ?: 'General Desk' }}</div>
                                <div style="color: #2563eb; font-size: 11.5px; font-weight: 700;">{{ $req->whom_to_meet_type }}</div>
                            </div>
                        </td>

                        <!-- Purpose -->
                        <td>
                            <div style="display: flex; flex-direction: column; gap: 3px;">
                                <div style="font-weight: 700; color: #1e293b; font-size: 12.5px; display: flex; align-items: center; flex-wrap: wrap; gap: 4px;">
                                    <span>{{ $req->visit_purpose }}</span>
                                    @if(stripos($req->visit_purpose, 'interview') !== false || $req->is_interview || $req->cv_url)
                                        <span class="badge bg-danger text-white" style="font-size: 10px; padding: 2px 6px; border-radius: 4px;">
                                            <i class="fas fa-user-tie me-1"></i> Interview
                                        </span>
                                    @endif
                                </div>
                                @if($req->cv_url)
                                <div class="mt-1">
                                    <a href="{{ $req->cv_url }}" target="_blank" download class="btn btn-xs btn-outline-danger py-0 px-2 fw-bold" style="font-size: 11px; border-radius: 4px; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; border-width: 1.5px;">
                                        <i class="fas fa-file-pdf"></i> Download CV (PDF)
                                    </a>
                                </div>
                                @endif
                                @if($req->detailed_purpose_remarks)
                                <div style="color: #64748b; font-size: 11.5px; max-width: 220px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $req->detailed_purpose_remarks }}">
                                    {{ $req->detailed_purpose_remarks }}
                                </div>
                                @endif
                            </div>
                        </td>

                        <!-- Gate & Head Count -->
                        <td>
                            <div style="display: flex; flex-direction: column; gap: 3px;">
                                <div><span class="badge-headcount">{{ $req->entourage_count ?? 1 }} Person(s)</span></div>
                                <div style="color: #64748b; font-size: 11.5px; font-weight: 600;">{{ $req->security_gate ?: 'Main Gate 1' }}</div>
                            </div>
                        </td>

                        <!-- Requested At -->
                        <td>
                            <div style="display: flex; flex-direction: column; gap: 2px;">
                                <div style="font-weight: 700; color: #1e293b; font-size: 12px;">{{ $req->created_at->format('d M, Y') }}</div>
                                <div style="color: #94a3b8; font-size: 11px; font-weight: 600;">{{ $req->created_at->format('h:i A') }}</div>
                            </div>
                        </td>

                        <!-- Status -->
                        <td>
                            @if($req->status === 'pending')
                                <span class="badge-status badge-status-pending"><i class="fas fa-clock"></i> Pending</span>
                            @elseif(in_array($req->status, ['checked_in', 'approved']))
                                <span class="badge-status badge-status-approved"><i class="fas fa-check-circle"></i> Approved</span>
                            @elseif($req->status === 'checked_out')
                                <span class="badge-status badge-status-checked_out">Checked Out</span>
                            @elseif($req->status === 'rejected')
                                <span class="badge-status badge-status-rejected"><i class="fas fa-ban"></i> Rejected</span>
                                @if($req->rejection_reason)
                                <div style="color: #dc2626; font-size: 10px; font-weight: 700; margin-top: 2px;">{{ $req->rejection_reason }}</div>
                                @endif
                            @endif
                        </td>

                        <!-- Actions -->
                        <td style="text-align: right;">
                            <div style="display: inline-flex; align-items: center; gap: 6px; justify-content: flex-end;">
                                @if($req->status === 'pending')
                                    <button type="button" class="btn-action-approve" onclick="approveRequest({{ $req->id }}, '{{ addslashes($req->full_name) }}', '{{ addslashes($req->email) }}')" title="Approve & Send Visitor Card to Email">
                                        <i class="fas fa-check"></i> Accept & Email
                                    </button>
                                    <button type="button" class="btn-action-reject" onclick="openRejectModal({{ $req->id }}, '{{ addslashes($req->full_name) }}')" title="Reject Request">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                @endif

                                @if(in_array($req->status, ['checked_in', 'approved', 'checked_out']))
                                    <a href="{{ route('school.front-desk.visitor.print', $req->id) }}" target="_blank" class="btn-action-icon" title="Print Visitor Pass">
                                        <i class="fas fa-print"></i>
                                    </a>
                                @endif

                                <button type="button" class="btn-action-icon" onclick='openDetailsModal(@json($req))' title="View Full Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center p-0">
                            <div class="empty-box-card">
                                <div class="empty-icon-circle">
                                    <i class="fas fa-inbox"></i>
                                </div>
                                <h5 class="fw-bold text-dark mb-1">No Visitor Requests in this Tab</h5>
                                <p class="text-muted small mb-0" style="max-width: 450px; margin: 0 auto;">
                                    When visitors scan your front gate QR code and submit their details, their entry requests will automatically appear here.
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($requests->hasPages())
        <div class="p-3 border-top d-flex justify-content-end">
            {{ $requests->links() }}
        </div>
        @endif
    </div>

</div>

<!-- QR Code Generator Lightbox Modal -->
<div class="custom-modal-overlay" id="qrModalOverlay" style="display: none;">
    <div class="modal-box-card" style="max-width: 580px;">
        <div class="qr-modal-header">
            <div class="d-flex align-items-center gap-2">
                <div class="qr-modal-icon-badge">
                    <i class="fas fa-qrcode"></i>
                </div>
                <div>
                    <h6 class="qr-modal-title">Visitor Self-Registration Portal</h6>
                    <p class="qr-modal-sub">Smart Front Gate QR Scanner & Standee Poster</p>
                </div>
            </div>
            <button type="button" class="modal-close-x" onclick="closeQrModal()">&times;</button>
        </div>
        <div class="qr-gold-stripe"></div>
        <div class="p-4">
            <div class="qr-showcase-box">
                <span class="qr-smart-pill">
                    <i class="fas fa-bolt"></i> Smart Gate Self-Service QR
                </span>
                <h5 class="qr-school-heading">{{ $school->name }}</h5>
                <div class="qr-code-pill-badge">
                    <span>SCHOOL CODE:</span>
                    <span style="color: #dc2626;">{{ $schoolCode }}</span>
                </div>

                <div>
                    <div class="qr-target-container">
                        <div class="scanner-corner corner-tl"></div>
                        <div class="scanner-corner corner-tr"></div>
                        <div class="scanner-corner corner-bl"></div>
                        <div class="scanner-corner corner-br"></div>
                        <img src="{{ $qrCodeImageUrl }}" alt="QR Code" class="qr-target-img">
                    </div>
                </div>

                <div>
                    <div class="qr-scan-badge-prompt">
                        <i class="fas fa-camera"></i> Point Phone Camera to Scan & Register
                    </div>
                </div>

                <div class="qr-step-journey">
                    <div class="step-pill-item"><i class="fas fa-qrcode"></i> 1. Scan QR</div>
                    <span class="step-arrow-divider"><i class="fas fa-chevron-right"></i></span>
                    <div class="step-pill-item"><i class="fas fa-user-edit"></i> 2. Fill Form</div>
                    <span class="step-arrow-divider"><i class="fas fa-chevron-right"></i></span>
                    <div class="step-pill-item"><i class="fas fa-envelope-open-text"></i> 3. Pass to Email</div>
                </div>
            </div>

            <div class="mb-3 mt-3">
                <label class="form-label mb-1 fw-bold text-dark" style="font-size: 12px;">Direct Portal Registration URL</label>
                <div class="url-copy-wrapper">
                    <i class="fas fa-globe text-primary ms-1" style="font-size: 13px;"></i>
                    <input type="text" id="publicUrlInput" class="url-copy-input" readonly value="{{ $publicFormUrl }}">
                    <button type="button" id="btnCopyReqLink" class="btn-copy-gradient" onclick="copyPublicLink()">
                        <i class="fas fa-copy"></i> Copy Link
                    </button>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2 mt-3">
                <button type="button" class="btn-modal-print" onclick="printStandeePoster()">
                    <i class="fas fa-print"></i> Print Gate Poster
                </button>
                <a href="{{ $publicFormUrl }}" target="_blank" class="btn-modal-open">
                    <i class="fas fa-external-link-alt"></i> Open Form
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Rejection Reason Modal -->
<div class="custom-modal-overlay" id="rejectRequestModalOverlay" style="display: none;">
    <div class="modal-box-card" style="max-width: 480px;">
        <div class="modal-hdr-custom modal-hdr-danger">
            <h6 class="fw-bold mb-0 text-white"><i class="fas fa-ban me-2"></i> Reject Visitor Request</h6>
            <button type="button" class="modal-close-x" onclick="closeRejectModal()">&times;</button>
        </div>
        <form id="rejectForm" method="POST" action="">
            @csrf
            <div class="p-4">
                <p class="mb-3 text-dark">Are you sure you want to reject the visitor request for <strong id="rejectVisitorName"></strong>?</p>
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">Reason for Rejection (Optional)</label>
                    <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="3" placeholder="e.g. Official not available today, Incomplete documents..." style="border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 13px;"></textarea>
                </div>
                <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                    <button type="button" class="btn btn-secondary btn-sm fw-bold px-3" onclick="closeRejectModal()" style="border-radius: 8px;">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm fw-bold px-3" style="border-radius: 8px;">Confirm Reject</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Visitor Request Details Modal (Fix for Image 5) -->
<div class="custom-modal-overlay" id="detailsModalOverlay" style="display: none;">
    <div class="modal-box-card" style="max-width: 660px;">
        <div class="modal-hdr-custom modal-hdr-primary">
            <div class="d-flex align-items-center gap-2">
                <div class="qr-modal-icon-badge" style="width: 32px; height: 32px; font-size: 14px;">
                    <i class="fas fa-id-card"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-white" style="font-size: 15px;">Visitor Request Profile</h6>
                </div>
            </div>
            <button type="button" class="modal-close-x" onclick="closeDetailsModal()">&times;</button>
        </div>
        <div class="qr-gold-stripe"></div>

        <!-- Hero Header with Photo and Core Info -->
        <div class="profile-hero-bar">
            <div class="profile-avatar-box">
                <img id="detailVisitorPhoto" src="" alt="Photo" style="display: none;">
                <i id="detailVisitorPhotoPlaceholder" class="fas fa-user"></i>
            </div>
            <div style="flex: 1;">
                <div class="fw-black fs-5 text-dark" id="detailFullName" style="font-weight: 900; font-size: 18px; line-height: 1.2;"></div>
                <div class="d-flex align-items-center gap-2 mt-1 flex-wrap">
                    <span class="badge-type-pill" id="detailVisitorType">Visitor</span>
                    <span class="pass-num-pill fs-6" id="detailPassNumber" style="font-weight: 800;"></span>
                </div>
            </div>
        </div>

        <!-- 2-Column Responsive Information Grid -->
        <div class="profile-info-grid">
            <!-- Mobile -->
            <div class="profile-field-box">
                <span class="field-title-label"><i class="fas fa-phone-alt"></i> Mobile Number</span>
                <span class="field-value-text" id="detailMobile">N/A</span>
            </div>

            <!-- Email -->
            <div class="profile-field-box">
                <span class="field-title-label"><i class="fas fa-envelope"></i> Email Address</span>
                <span class="field-value-text" id="detailEmail">N/A</span>
            </div>

            <!-- Whom to Meet -->
            <div class="profile-field-box">
                <span class="field-title-label"><i class="fas fa-user-tie"></i> Whom to Meet</span>
                <span class="field-value-text" id="detailWhomToMeet">N/A</span>
            </div>

            <!-- Host / Staff Name -->
            <div class="profile-field-box">
                <span class="field-title-label"><i class="fas fa-user-check"></i> Host / Staff Name</span>
                <span class="field-value-text" id="detailHost">General Desk</span>
            </div>

            <!-- Purpose -->
            <div class="profile-field-box">
                <span class="field-title-label"><i class="fas fa-clipboard-list"></i> Purpose of Visit</span>
                <span class="field-value-text text-primary" id="detailPurpose">N/A</span>
            </div>

            <!-- Gate & Head Count -->
            <div class="profile-field-box">
                <span class="field-title-label"><i class="fas fa-door-open"></i> Gate & Head Count</span>
                <span class="field-value-text" id="detailGateCount">Main Gate 1 • 1 Person(s)</span>
            </div>

            <!-- ID Proof -->
            <div class="profile-field-box">
                <span class="field-title-label"><i class="fas fa-id-badge"></i> Govt ID Proof</span>
                <span class="field-value-text" id="detailIdProof">N/A</span>
            </div>

            <!-- Vehicle No -->
            <div class="profile-field-box">
                <span class="field-title-label"><i class="fas fa-car"></i> Vehicle Number</span>
                <span class="field-value-text" id="detailVehicle">N/A</span>
            </div>

            <!-- Address (Full Span) -->
            <div class="profile-field-box full-span">
                <span class="field-title-label"><i class="fas fa-map-marker-alt"></i> Complete Address</span>
                <span class="field-value-text" id="detailAddress">N/A</span>
            </div>

            <!-- Remarks (Full Span) -->
            <div class="profile-field-box full-span" id="detailRemarksContainer" style="display: none; background: #fffbeb; border-color: #fde68a;">
                <span class="field-title-label" style="color: #92400e;"><i class="fas fa-comment-dots"></i> Detailed Remarks</span>
                <span class="field-value-text" id="detailRemarks" style="color: #78350f;"></span>
            </div>

            <!-- Attached Candidate CV (PDF) -->
            <div class="profile-field-box full-span" id="detailCvContainer" style="display: none; background: #eff6ff; border: 1.5px solid #bfdbfe;">
                <span class="field-title-label" style="color: #1d4ed8;"><i class="fas fa-file-pdf text-danger"></i> Candidate CV / Resume</span>
                <div class="d-flex align-items-center justify-content-between mt-1">
                    <span class="field-value-text" id="detailCvName" style="color: #0f172a; font-weight: 700; font-size: 12.5px;">Candidate_CV.pdf</span>
                    <a href="#" id="detailCvDownloadBtn" target="_blank" download class="btn btn-sm btn-danger fw-bold" style="border-radius: 6px; font-size: 12px; display: inline-flex; align-items: center; gap: 6px; text-decoration: none;">
                        <i class="fas fa-download"></i> Download CV (PDF)
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="p-3 bg-light border-top d-flex justify-content-end">
            <button type="button" class="btn btn-secondary btn-sm fw-bold px-4" onclick="closeDetailsModal()" style="border-radius: 8px; font-size: 13px;">Close</button>
        </div>
    </div>
</div>

<script>
    // QR Modal Controls
    function openQrModal() {
        const modal = document.getElementById('qrModalOverlay');
        if (modal) {
            modal.classList.add('active');
            modal.style.setProperty('display', 'flex', 'important');
        }
    }

    function closeQrModal() {
        const modal = document.getElementById('qrModalOverlay');
        if (modal) {
            modal.classList.remove('active');
            modal.style.setProperty('display', 'none', 'important');
        }
    }

    function copyPublicLink() {
        const input = document.getElementById('publicUrlInput');
        if (!input) return;
        input.select();
        input.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(input.value).then(() => {
            const btn = document.getElementById('btnCopyReqLink');
            if (btn) {
                const orig = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check text-success"></i> Copied!';
                setTimeout(() => { btn.innerHTML = orig; }, 2000);
            }
        }).catch(() => {
            document.execCommand('copy');
            alert('Link copied to clipboard!');
        });
    }

    function printStandeePoster() {
        const schoolName = @json($school->name ?? 'School Campus');
        const schoolCode = @json($schoolCode ?? 'EDUZEN');
        const qrImgUrl = @json($qrCodeImageUrl ?? '');

        const posterHtml = `
            <!DOCTYPE html>
            <html>
            <head>
                <title>Visitor Gate Pass QR Poster - ${schoolName}</title>
                <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&display=swap" rel="stylesheet">
                <style>
                    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
                    body { background: #f8fafc; padding: 40px; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
                    .standee-poster {
                        width: 520px;
                        background: #ffffff;
                        border: 4px solid #1d4ed8;
                        border-radius: 24px;
                        padding: 36px 30px;
                        text-align: center;
                        box-shadow: 0 20px 50px rgba(0,0,0,0.12);
                    }
                    .poster-header-badge {
                        background: #1d4ed8;
                        color: #ffffff;
                        font-size: 13px;
                        font-weight: 800;
                        text-transform: uppercase;
                        letter-spacing: 1px;
                        padding: 6px 18px;
                        border-radius: 20px;
                        display: inline-block;
                        margin-bottom: 16px;
                    }
                    .school-name {
                        font-size: 24px;
                        font-weight: 900;
                        color: #0f172a;
                        margin-bottom: 6px;
                        line-height: 1.2;
                    }
                    .school-sub {
                        font-size: 13px;
                        color: #64748b;
                        margin-bottom: 24px;
                    }
                    .qr-display-box {
                        background: #f1f5f9;
                        border: 3px dashed #2563eb;
                        border-radius: 20px;
                        padding: 24px;
                        display: inline-block;
                        margin-bottom: 24px;
                    }
                    .qr-img {
                        width: 220px;
                        height: 220px;
                        display: block;
                        margin: 0 auto;
                    }
                    .scan-instructions {
                        font-size: 18px;
                        font-weight: 800;
                        color: #1d4ed8;
                        margin-bottom: 8px;
                    }
                    .scan-subtext {
                        font-size: 13px;
                        color: #475569;
                        line-height: 1.5;
                    }
                    .poster-footer {
                        margin-top: 26px;
                        padding-top: 18px;
                        border-top: 1px solid #e2e8f0;
                        font-size: 11px;
                        color: #94a3b8;
                    }
                    @media print {
                        body { background: transparent; padding: 0; }
                        .standee-poster { border: 3px solid #1d4ed8; box-shadow: none; width: 100%; max-width: 600px; margin: 0 auto; }
                    }
                </style>
            </head>
            <body>
                <div class="standee-poster">
                    <div class="poster-header-badge">Front Gate Visitor Portal</div>
                    <h1 class="school-name">${schoolName}</h1>
                    <p class="school-sub">School Code: <strong>${schoolCode}</strong></p>
                    
                    <div class="qr-display-box">
                        <img src="${qrImgUrl}" alt="Scan QR" class="qr-img">
                    </div>

                    <div class="scan-instructions">📲 Scan with Phone Camera to Register</div>
                    <p class="scan-subtext">Fill in your visit details on your mobile. Once approved, your Digital Visitor Pass will be delivered directly to your email!</p>

                    <div class="poster-footer">
                        Powered by Educorerp • Front Desk Visitor Security System
                    </div>
                </div>
                <script>
                    window.onload = function() { window.print(); }
                <\/script>
            </body>
            </html>
        `;
        const printWin = window.open('', '_blank');
        printWin.document.write(posterHtml);
        printWin.document.close();
    }

    // Approve Request Action
    function approveRequest(id, name, email) {
        if (!confirm(`Are you sure you want to approve the visitor request for "${name}"?\n\nOnce approved, an official Digital Visitor Card will be automatically emailed to ${email || 'their email'}.`)) {
            return;
        }

        const approveUrl = `{{ url('/school/front-desk/visitor-requests') }}/${id}/approve`;

        fetch(approveUrl, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert(data.message || 'Approval failed.');
            }
        })
        .catch(err => {
            console.error(err);
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = approveUrl;
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);
            document.body.appendChild(form);
            form.submit();
        });
    }

    // Reject Modal Action
    function openRejectModal(id, name) {
        document.getElementById('rejectVisitorName').textContent = name;
        document.getElementById('rejectForm').action = `{{ url('/school/front-desk/visitor-requests') }}/${id}/reject`;
        const modal = document.getElementById('rejectRequestModalOverlay');
        if (modal) {
            modal.classList.add('active');
            modal.style.setProperty('display', 'flex', 'important');
        }
    }

    function closeRejectModal() {
        const modal = document.getElementById('rejectRequestModalOverlay');
        if (modal) {
            modal.classList.remove('active');
            modal.style.setProperty('display', 'none', 'important');
        }
    }

    // Details Modal Action (Image 5 fix)
    function openDetailsModal(v) {
        document.getElementById('detailFullName').textContent = v.full_name || 'N/A';
        document.getElementById('detailVisitorType').textContent = v.visitor_type || 'Visitor';
        document.getElementById('detailPassNumber').textContent = '#' + (v.pass_number || 'N/A');
        document.getElementById('detailMobile').textContent = v.mobile_number || 'N/A';
        document.getElementById('detailEmail').textContent = v.email || 'N/A';
        document.getElementById('detailWhomToMeet').textContent = v.whom_to_meet_type || 'N/A';
        document.getElementById('detailHost').textContent = v.host_name || 'General Desk';
        document.getElementById('detailPurpose').textContent = v.visit_purpose || 'N/A';
        document.getElementById('detailGateCount').textContent = (v.security_gate || 'Main Gate 1') + ' • ' + (v.entourage_count || 1) + ' Person(s)';
        document.getElementById('detailIdProof').textContent = (v.id_proof_type || 'N/A') + (v.id_proof_number ? ' (' + v.id_proof_number + ')' : '');
        document.getElementById('detailVehicle').textContent = v.vehicle_number || 'N/A';
        
        let addr = v.street_address || '';
        if (v.city) addr += (addr ? ', ' : '') + v.city;
        if (v.state) addr += (addr ? ', ' : '') + v.state;
        if (v.pincode) addr += ' - ' + v.pincode;
        document.getElementById('detailAddress').textContent = addr || 'N/A';

        if (v.detailed_purpose_remarks) {
            document.getElementById('detailRemarks').textContent = v.detailed_purpose_remarks;
            document.getElementById('detailRemarksContainer').style.display = 'flex';
        } else {
            document.getElementById('detailRemarksContainer').style.display = 'none';
        }

        // Candidate CV Handling in Details Modal
        const cvContainer = document.getElementById('detailCvContainer');
        const cvBtn = document.getElementById('detailCvDownloadBtn');
        const cvName = document.getElementById('detailCvName');
        const cvUrl = v.cv_url || (v.meta_data && v.meta_data.cv_url) || (v.cv_path ? ('/storage/' + v.cv_path) : null);

        if (cvUrl) {
            cvBtn.href = cvUrl;
            cvName.textContent = v.cv_name || (v.full_name ? (v.full_name.replace(/\s+/g, '_') + '_CV.pdf') : 'Candidate_CV.pdf');
            cvContainer.style.display = 'block';
        } else {
            cvContainer.style.display = 'none';
        }

        const photoImg = document.getElementById('detailVisitorPhoto');
        const photoPlaceholder = document.getElementById('detailVisitorPhotoPlaceholder');
        if (v.photo_url) {
            photoImg.src = v.photo_url;
            photoImg.style.display = 'block';
            photoPlaceholder.style.display = 'none';
        } else {
            photoImg.style.display = 'none';
            photoPlaceholder.style.display = 'block';
        }

        const modal = document.getElementById('detailsModalOverlay');
        if (modal) {
            modal.classList.add('active');
            modal.style.setProperty('display', 'flex', 'important');
        }
    }

    function closeDetailsModal() {
        const modal = document.getElementById('detailsModalOverlay');
        if (modal) {
            modal.classList.remove('active');
            modal.style.setProperty('display', 'none', 'important');
        }
    }

    // Modal background click & Escape key dismiss
    document.addEventListener('DOMContentLoaded', function() {
        ['qrModalOverlay', 'rejectRequestModalOverlay', 'detailsModalOverlay'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('click', function(e) {
                    if (e.target === el) {
                        el.classList.remove('active');
                        el.style.setProperty('display', 'none', 'important');
                    }
                });
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeQrModal();
                closeRejectModal();
                closeDetailsModal();
            }
        });
    });
</script>
@endsection
