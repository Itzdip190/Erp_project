@extends('layouts.app')

@section('page-title', 'Daily Task Heads')

@section('content')
<style>
    /* =========================================================
       PURE BLUE & WHITE SYSTEM - ENTERPRISE GRADE
       Fully Responsive on Mobile, Tablet, Laptop & Desktop
       ========================================================= */
    :root {
        --theme-royal-blue: #0038b8;
        --theme-primary-blue: #2563eb;
        --theme-hover-blue: #1d4ed8;
        --theme-light-blue: #eff6ff;
        --theme-subtle-blue: #f0f7ff;
        --theme-blue-border: #bfdbfe;
        --theme-card-border: #e2e8f0;
        --theme-card-bg: #ffffff;
        --theme-text-dark: #0f172a;
        --theme-text-muted: #64748b;
        --theme-shadow-sm: 0 2px 8px rgba(37, 99, 235, 0.05);
        --theme-shadow-md: 0 8px 24px -4px rgba(37, 99, 235, 0.1), 0 4px 10px -2px rgba(0, 0, 0, 0.03);
        --theme-shadow-lg: 0 18px 38px -6px rgba(37, 99, 235, 0.16), 0 8px 16px -4px rgba(37, 99, 235, 0.08);
    }

    body.dark-mode {
        --theme-card-bg: #131c2e;
        --theme-subtle-blue: #19253d;
        --theme-card-border: rgba(255, 255, 255, 0.1);
        --theme-text-dark: #f8fafc;
        --theme-text-muted: #94a3b8;
        --theme-light-blue: rgba(37, 99, 235, 0.15);
        --theme-blue-border: rgba(59, 130, 246, 0.35);
    }

    /* ─── KEYFRAME ANIMATIONS ─── */
    @keyframes dtSlideUp {
        from {
            opacity: 0;
            transform: translateY(16px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes dtPulseGlow {
        0%, 100% {
            box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.35);
        }
        50% {
            box-shadow: 0 0 0 8px rgba(37, 99, 235, 0);
        }
    }

    @keyframes dtFloat {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-6px); }
    }

    .anim-slide-up {
        animation: dtSlideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    /* ─── RESPONSIVE PAGE CONTAINER ─── */
    .dt-wrapper {
        width: 100%;
        padding: 12px 16px;
        box-sizing: border-box;
    }

    @media (max-width: 576px) {
        .dt-wrapper {
            padding: 8px 10px;
        }
    }

    /* ─── PAGE HEADER BANNER ─── */
    .dt-header-banner {
        background: linear-gradient(135deg, #ffffff 0%, #f0f7ff 100%);
        border: 1.5px solid var(--theme-blue-border);
        border-radius: 18px;
        padding: 22px 28px;
        margin-bottom: 22px;
        box-shadow: var(--theme-shadow-md);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        position: relative;
        overflow: hidden;
    }

    body.dark-mode .dt-header-banner {
        background: linear-gradient(135deg, #131c2e 0%, #19253d 100%);
    }

    .dt-header-banner::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 5px;
        background: linear-gradient(180deg, #2563eb 0%, #1d4ed8 100%);
    }

    .dt-header-left {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .dt-header-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        color: var(--theme-primary-blue);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        border: 1.5px solid var(--theme-blue-border);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.12);
        animation: dtPulseGlow 3s infinite;
        flex-shrink: 0;
    }

    .dt-header-title {
        font-size: 20px;
        font-weight: 800;
        color: var(--theme-text-dark);
        margin: 0;
        letter-spacing: -0.4px;
    }

    .dt-header-subtitle {
        font-size: 13px;
        color: var(--theme-text-muted);
        margin: 2px 0 0 0;
        font-weight: 500;
    }

    @media (max-width: 768px) {
        .dt-header-banner {
            flex-direction: column;
            align-items: flex-start;
            padding: 18px 20px;
        }
        .dt-header-banner .btn-action-wrap {
            width: 100%;
        }
        .dt-header-banner .btn-action-wrap button {
            width: 100%;
            justify-content: center;
        }
    }

    /* ─── KPI STATS GRID ─── */
    .dt-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 18px;
        margin-bottom: 22px;
    }

    @media (max-width: 576px) {
        .dt-stats-grid {
            grid-template-columns: 1fr;
            gap: 12px;
        }
    }

    .dt-stat-box {
        background: var(--theme-card-bg);
        border: 1.5px solid var(--theme-blue-border);
        border-radius: 16px;
        padding: 18px 22px;
        box-shadow: var(--theme-shadow-sm);
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s, border-color 0.25s;
    }

    .dt-stat-box:hover {
        transform: translateY(-4px);
        box-shadow: var(--theme-shadow-lg);
        border-color: var(--theme-primary-blue);
    }

    .dt-stat-num {
        font-size: 28px;
        font-weight: 800;
        color: var(--theme-royal-blue);
        line-height: 1.1;
    }

    .dt-stat-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--theme-text-muted);
        margin-top: 4px;
    }

    /* ─── DATA TABLE CARD ─── */
    .dt-card-panel {
        background: var(--theme-card-bg);
        border: 1.5px solid var(--theme-blue-border);
        border-radius: 18px;
        box-shadow: var(--theme-shadow-md);
        overflow: hidden;
        margin-bottom: 30px;
        width: 100%;
    }

    /* ─── CLEAN HORIZONTAL TOOLBAR ─── */
    .dt-card-toolbar {
        padding: 16px 22px;
        background: linear-gradient(180deg, #ffffff 0%, #f8faff 100%);
        border-bottom: 1.5px solid var(--theme-blue-border);
        width: 100%;
        box-sizing: border-box;
    }

    body.dark-mode .dt-card-toolbar {
        background: var(--theme-subtle-blue);
    }

    .dt-toolbar-flex {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 14px;
        width: 100%;
        flex-wrap: wrap;
        margin: 0;
    }

    .dt-search-box {
        position: relative;
        flex: 1;
        min-width: 260px;
        max-width: 420px;
    }

    .dt-search-box input {
        width: 100%;
        padding: 10px 16px 10px 40px;
        border-radius: 12px;
        border: 1.5px solid var(--theme-blue-border);
        background: #ffffff;
        color: var(--theme-text-dark);
        font-size: 13.5px;
        font-weight: 600;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
        box-sizing: border-box;
    }

    .dt-search-box input:focus {
        border-color: var(--theme-primary-blue);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.16);
    }

    .dt-search-box i.fa-search {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--theme-primary-blue);
        font-size: 14px;
    }

    .dt-filter-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .dt-select-filter {
        padding: 10px 16px;
        border-radius: 12px;
        border: 1.5px solid var(--theme-blue-border);
        background: #ffffff;
        color: var(--theme-text-dark);
        font-size: 13.5px;
        font-weight: 600;
        outline: none;
        cursor: pointer;
        min-width: 150px;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .dt-select-filter:focus {
        border-color: var(--theme-primary-blue);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.16);
    }

    .dt-btn-reset {
        padding: 9px 16px;
        border-radius: 12px;
        border: 1.5px solid var(--theme-blue-border);
        background: var(--theme-light-blue);
        color: var(--theme-primary-blue);
        font-size: 13px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        transition: all 0.2s;
    }

    .dt-btn-reset:hover {
        background: var(--theme-primary-blue);
        color: #ffffff;
        border-color: var(--theme-primary-blue);
    }

    @media (max-width: 640px) {
        .dt-search-box {
            min-width: 100%;
            max-width: 100%;
        }
        .dt-filter-actions {
            width: 100%;
        }
        .dt-select-filter {
            flex: 1;
        }
    }

    /* ─── FULL WIDTH 100% ENTERPRISE TABLE ─── */
    .dt-table-wrap {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .dt-table {
        width: 100% !important;
        min-width: 850px;
        border-collapse: separate;
        border-spacing: 0;
        margin: 0;
        table-layout: auto;
    }

    .dt-table thead tr th {
        background: var(--theme-subtle-blue);
        color: var(--theme-text-muted);
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        padding: 14px 20px;
        border-bottom: 2px solid var(--theme-blue-border);
        border-top: none;
        white-space: nowrap;
    }

    .dt-table tbody tr td {
        padding: 16px 20px;
        vertical-align: middle;
        border-bottom: 1px solid #edf2f7;
        background: #ffffff;
        font-size: 13.5px;
        color: var(--theme-text-dark);
        transition: background-color 0.15s ease;
    }

    body.dark-mode .dt-table tbody tr td {
        background: #131c2e;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }

    .dt-table tbody tr:hover td {
        background-color: var(--theme-subtle-blue) !important;
    }

    /* Column Sizing */
    .col-order { width: 70px; text-align: center; }
    .col-name { min-width: 230px; }
    .col-code { width: 130px; }
    .col-desc { min-width: 250px; }
    .col-questions { width: 150px; text-align: center; }
    .col-status { width: 130px; text-align: center; }
    .col-actions { width: 120px; text-align: right; }

    .dt-head-icon-box {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 17px;
        flex-shrink: 0;
        box-shadow: 0 2px 6px rgba(37, 99, 235, 0.08);
    }

    /* ─── PRIMARY ACTION BUTTONS ─── */
    .dt-btn-royal {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: #ffffff !important;
        border: none;
        padding: 10px 22px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 13.5px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.28);
        transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
        text-decoration: none;
    }

    .dt-btn-royal:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 22px rgba(37, 99, 235, 0.38);
    }

    .dt-btn-royal:active {
        transform: scale(0.98);
    }

    /* ─── STATUS PILL BADGES ─── */
    .dt-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        border: 1px solid transparent;
        transition: all 0.2s;
    }

    .dt-status-active {
        background: #eff6ff;
        color: #2563eb;
        border-color: #bfdbfe;
    }

    .dt-status-inactive {
        background: #f8fafc;
        color: #64748b;
        border-color: #cbd5e1;
    }

    .dt-action-icon-btn {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--theme-blue-border);
        background: var(--theme-light-blue);
        color: var(--theme-primary-blue);
        cursor: pointer;
        transition: all 0.2s;
    }

    .dt-action-icon-btn:hover {
        background: var(--theme-primary-blue);
        color: #ffffff;
        border-color: var(--theme-primary-blue);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
    }

    .dt-action-icon-btn.btn-trash:hover {
        background: #ef4444;
        color: #ffffff;
        border-color: #ef4444;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25);
    }

    /* ─── ELEGANT EMPTY STATE ─── */
    .dt-empty-box {
        text-align: center;
        padding: 50px 20px;
    }

    .dt-empty-icon-wrap {
        width: 80px;
        height: 80px;
        border-radius: 24px;
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        color: #2563eb;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 36px;
        margin-bottom: 18px;
        border: 2px solid var(--theme-blue-border);
        box-shadow: 0 10px 25px rgba(37, 99, 235, 0.12);
        animation: dtFloat 3.5s ease-in-out infinite;
    }

    .dt-empty-title {
        font-size: 18px;
        font-weight: 800;
        color: var(--theme-text-dark);
        margin-bottom: 6px;
    }

    .dt-empty-desc {
        font-size: 13.5px;
        color: var(--theme-text-muted);
        max-width: 440px;
        margin: 0 auto 20px auto;
        line-height: 1.5;
    }

    /* =========================================================
       DYNAMIC SIDE SLIDER (OFFCANVAS DRAWER)
       ========================================================= */
    .dt-drawer-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
        z-index: 1050;
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: opacity 0.35s cubic-bezier(0.16, 1, 0.3, 1), visibility 0.35s;
    }

    .dt-drawer-backdrop.active {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
    }

    .dt-drawer-panel {
        position: fixed;
        top: 0;
        right: 0;
        bottom: 0;
        height: 100vh;
        height: 100dvh;
        width: 600px;
        max-width: 100%;
        background: #ffffff;
        z-index: 1060;
        box-shadow: -15px 0 45px rgba(0, 56, 184, 0.2), -2px 0 12px rgba(0, 0, 0, 0.08);
        transform: translateX(100%);
        transition: transform 0.38s cubic-bezier(0.16, 1, 0.3, 1);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    body.dark-mode .dt-drawer-panel {
        background: #131c2e;
    }

    .dt-drawer-panel.active {
        transform: translateX(0);
    }

    @media (max-width: 640px) {
        .dt-drawer-panel {
            width: 100vw !important;
            max-width: 100vw !important;
        }
    }

    .dt-drawer-header {
        background: linear-gradient(135deg, #002466 0%, #0038b8 100%);
        color: #ffffff;
        padding: 18px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0;
        box-shadow: 0 4px 15px rgba(0, 56, 184, 0.15);
        position: relative;
        z-index: 2;
    }

    .dt-drawer-header-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .dt-drawer-header-icon {
        width: 44px;
        height: 44px;
        background: rgba(255, 255, 255, 0.18);
        border: 1.5px solid rgba(255, 255, 255, 0.35);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: #ffffff;
        flex-shrink: 0;
    }

    .dt-drawer-title {
        font-size: 17px;
        font-weight: 800;
        margin: 0;
        color: #ffffff;
        letter-spacing: -0.3px;
    }

    .dt-drawer-subtitle {
        font-size: 12px;
        opacity: 0.9;
        margin: 2px 0 0 0;
    }

    .dt-drawer-close-btn {
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: #ffffff;
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        flex-shrink: 0;
    }

    .dt-drawer-close-btn:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: rotate(90deg);
    }

    .dt-drawer-body {
        padding: 22px;
        overflow-y: auto;
        overflow-x: hidden;
        flex: 1;
        background: #f8faff;
        scroll-behavior: smooth;
    }

    body.dark-mode .dt-drawer-body {
        background: #0b1120;
    }

    .dt-drawer-body::-webkit-scrollbar {
        width: 6px;
    }
    .dt-drawer-body::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    .dt-drawer-body::-webkit-scrollbar-thumb:hover {
        background: #2563eb;
    }

    .dt-section-card {
        background: #ffffff;
        border: 1.5px solid var(--theme-blue-border);
        border-radius: 14px;
        padding: 18px 20px;
        margin-bottom: 18px;
        box-shadow: var(--theme-shadow-sm);
    }

    body.dark-mode .dt-section-card {
        background: #19253d;
    }

    .dt-section-heading {
        font-size: 12px;
        font-weight: 800;
        color: var(--theme-primary-blue);
        text-transform: uppercase;
        letter-spacing: 0.6px;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 14px;
        padding-bottom: 8px;
        border-bottom: 1.5px solid var(--theme-light-blue);
    }

    .dt-form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-bottom: 14px;
    }

    .dt-form-group:last-child {
        margin-bottom: 0;
    }

    .dt-form-group label {
        font-size: 11.5px;
        font-weight: 800;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        margin: 0;
    }

    body.dark-mode .dt-form-group label {
        color: #cbd5e1;
    }

    .dt-input-control {
        width: 100%;
        padding: 10px 14px;
        border: 1.5px solid #cbd5e1;
        border-radius: 10px;
        font-size: 13.5px;
        font-weight: 600;
        color: #1e293b;
        outline: none;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        background: #ffffff;
        box-sizing: border-box;
    }

    body.dark-mode .dt-input-control {
        background: #131c2e;
        color: #f8fafc;
        border-color: rgba(255, 255, 255, 0.15);
    }

    .dt-input-control:focus {
        border-color: var(--theme-primary-blue);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }

    .dt-color-palette {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 4px;
    }

    .dt-color-chip {
        width: 32px;
        height: 32px;
        border-radius: 10px;
        cursor: pointer;
        border: 2px solid transparent;
        transition: transform 0.2s, border-color 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 12px;
    }

    .dt-color-chip:hover {
        transform: scale(1.15);
    }

    .dt-color-chip.selected {
        border-color: #0f172a;
        box-shadow: 0 0 0 2px #ffffff, 0 0 0 4px #2563eb;
        transform: scale(1.1);
    }

    .dt-drawer-footer {
        padding: 16px 24px;
        background: #ffffff;
        border-top: 1.5px solid var(--theme-blue-border);
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
        flex-shrink: 0;
        box-shadow: 0 -4px 14px rgba(0, 0, 0, 0.04);
    }

    body.dark-mode .dt-drawer-footer {
        background: #131c2e;
    }
</style>

<div class="dt-wrapper">
    <!-- Header Banner -->
    <div class="dt-header-banner anim-slide-up">
        <div class="dt-header-left">
            <div class="dt-header-icon">
                <i class="fas fa-layer-group"></i>
            </div>
            <div>
                <h4 class="dt-header-title">Daily Task Heads</h4>
                <p class="dt-header-subtitle">Create and manage core categories & task rubrics for daily student evaluation</p>
            </div>
        </div>
        <div class="btn-action-wrap">
            <button type="button" class="dt-btn-royal" onclick="openCreateHeadDrawer()">
                <i class="fas fa-plus"></i> Add New Task Head
            </button>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm anim-slide-up" role="alert" style="background: #eff6ff; color: #1e40af; border-left: 4px solid #2563eb !important; border-radius: 12px;">
            <i class="fas fa-check-circle me-2 text-primary"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm anim-slide-up" role="alert" style="border-radius: 12px;">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistics Grid -->
    <div class="dt-stats-grid anim-slide-up">
        <div class="dt-stat-box">
            <div>
                <div class="dt-stat-num">{{ $stats['total'] }}</div>
                <div class="dt-stat-label">Total Task Heads</div>
            </div>
            <div class="dt-header-icon" style="width: 46px; height: 46px; font-size: 19px;">
                <i class="fas fa-tags"></i>
            </div>
        </div>
        <div class="dt-stat-box">
            <div>
                <div class="dt-stat-num" style="color: #2563eb;">{{ $stats['active'] }}</div>
                <div class="dt-stat-label">Active Heads</div>
            </div>
            <div class="dt-header-icon" style="background: #f0f7ff; color: #2563eb; width: 46px; height: 46px; font-size: 19px;">
                <i class="fas fa-circle-check"></i>
            </div>
        </div>
        <div class="dt-stat-box">
            <div>
                <div class="dt-stat-num" style="color: #1d4ed8;">{{ $stats['total_questions'] }}</div>
                <div class="dt-stat-label">Mapped Questions</div>
            </div>
            <div class="dt-header-icon" style="background: #dbeafe; color: #1e40af; width: 46px; height: 46px; font-size: 19px;">
                <i class="fas fa-clipboard-question"></i>
            </div>
        </div>
    </div>

    <!-- Table Card Panel (100% Full Width) -->
    <div class="dt-card-panel anim-slide-up">
        
        <!-- Clean Horizontal Toolbar -->
        <div class="dt-card-toolbar">
            <form method="GET" action="{{ route('school.daily-tasks.heads') }}" class="dt-toolbar-flex">
                <div class="dt-search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search by task head name, code..." onchange="this.form.submit()">
                </div>
                <div class="dt-filter-actions">
                    <select name="status" class="dt-select-filter" onchange="this.form.submit()">
                        <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All Status</option>
                        <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active Only</option>
                        <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive Only</option>
                    </select>
                    @if($search || $status !== 'all')
                        <a href="{{ route('school.daily-tasks.heads') }}" class="dt-btn-reset" title="Reset Filters">
                            <i class="fas fa-rotate-left"></i> Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- 100% Responsive Table View -->
        <div class="dt-table-wrap">
            <table class="dt-table">
                <thead>
                    <tr>
                        <th class="col-order">Order</th>
                        <th class="col-name">Task Head Name</th>
                        <th class="col-code">Code</th>
                        <th class="col-desc">Description</th>
                        <th class="col-questions">Questions</th>
                        <th class="col-status">Status</th>
                        <th class="col-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($heads as $head)
                        <tr>
                            <td class="col-order fw-bold" style="color: var(--theme-primary-blue);">
                                #{{ $head->sort_order }}
                            </td>
                            <td class="col-name">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="dt-head-icon-box" style="background: #eff6ff; color: {{ $head->color ?: '#2563eb' }}; border: 1.5px solid var(--theme-blue-border);">
                                        <i class="fas {{ $head->icon ?: 'fa-tasks' }}"></i>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold" style="font-size: 14.5px; color: var(--theme-text-dark); line-height: 1.2;">{{ $head->name }}</span>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0.5 mt-1 align-self-start" style="font-size: 10.5px; font-weight: 700;">
                                            Category
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="col-code">
                                <span class="badge bg-light text-primary border border-primary-subtle px-2.5 py-1 fw-bold">{{ $head->code ?: '—' }}</span>
                            </td>
                            <td class="col-desc text-muted" style="font-size: 13px;">
                                {{ $head->description ?: 'No description provided.' }}
                            </td>
                            <td class="col-questions">
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1.5 fw-bold" style="font-size: 12px;">
                                    <i class="fas fa-clipboard-list me-1"></i> {{ $head->questions_count }} Questions
                                </span>
                            </td>
                            <td class="col-status">
                                <form method="POST" action="{{ route('school.daily-tasks.heads.toggle', $head->id) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="border-0 bg-transparent p-0" title="Click to toggle status">
                                        @if($head->is_active)
                                            <span class="dt-status-pill dt-status-active"><i class="fas fa-circle-check"></i> Active</span>
                                        @else
                                            <span class="dt-status-pill dt-status-inactive"><i class="fas fa-circle-xmark"></i> Inactive</span>
                                        @endif
                                    </button>
                                </form>
                            </td>
                            <td class="col-actions">
                                <div class="d-flex align-items-center justify-content-end gap-2">
                                    <button type="button" class="dt-action-icon-btn" title="Edit Head (Side Slider)" onclick="editHeadDrawer({{ json_encode($head) }})">
                                        <i class="fas fa-pencil"></i>
                                    </button>
                                    <form method="POST" action="{{ route('school.daily-tasks.heads.delete', $head->id) }}" onsubmit="return confirm('Are you sure you want to delete this Task Head?');" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dt-action-icon-btn btn-trash" title="Delete Head">
                                            <i class="fas fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="dt-empty-box">
                                    <div class="dt-empty-icon-wrap">
                                        <i class="fas fa-folder-open"></i>
                                    </div>
                                    <div class="dt-empty-title">No Daily Task Heads Found</div>
                                    <div class="dt-empty-desc">Create your first task head (e.g. Homework, Behavior, Reading, Hygiene) to organize daily teacher evaluations.</div>
                                    <button type="button" class="dt-btn-royal" onclick="openCreateHeadDrawer()">
                                        <i class="fas fa-plus"></i> Create Task Head
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($heads->hasPages())
            <div class="p-3 border-top d-flex justify-content-end">
                {{ $heads->links() }}
            </div>
        @endif
    </div>
</div>

<!-- =========================================================
     1. CREATE TASK HEAD DYNAMIC SIDE SLIDER (DRAWER)
     ========================================================= -->
<div class="dt-drawer-backdrop" id="createHeadDrawerBackdrop" onclick="closeCreateHeadDrawer()"></div>

<div class="dt-drawer-panel" id="createHeadDrawer">
    <!-- Drawer Header -->
    <div class="dt-drawer-header">
        <div class="dt-drawer-header-left">
            <div class="dt-drawer-header-icon">
                <i class="fas fa-layer-group"></i>
            </div>
            <div>
                <h5 class="dt-drawer-title">Add Daily Task Head</h5>
                <p class="dt-drawer-subtitle">Create new rubric or evaluation category</p>
            </div>
        </div>
        <button type="button" class="dt-drawer-close-btn" onclick="closeCreateHeadDrawer()" title="Close (Esc)">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Drawer Form Content -->
    <form method="POST" action="{{ route('school.daily-tasks.heads.store') }}" style="display: flex; flex-direction: column; flex: 1; overflow: hidden; margin: 0;">
        @csrf
        <div class="dt-drawer-body">
            
            <!-- Section 1: Basic Head Info -->
            <div class="dt-section-card">
                <div class="dt-section-heading">
                    <i class="fas fa-circle-info"></i>
                    <span>1. Basic Head Information</span>
                </div>
                <div class="dt-form-group">
                    <label>Task Head Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="dt-input-control" placeholder="e.g., Homework & Assignments, Classroom Behavior" required>
                </div>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="dt-form-group">
                            <label>Short Code / Tag</label>
                            <input type="text" name="code" class="dt-input-control" placeholder="e.g., HW, DISC, HYG">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="dt-form-group">
                            <label>Display Sort Order</label>
                            <input type="number" name="sort_order" class="dt-input-control" value="0" min="0">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Visual Styling & Icon -->
            <div class="dt-section-card">
                <div class="dt-section-heading">
                    <i class="fas fa-palette"></i>
                    <span>2. Visual Styling & Icon</span>
                </div>
                <div class="dt-form-group">
                    <label>Select Theme Color</label>
                    <div class="dt-color-palette" id="createColorPalette">
                        <div class="dt-color-chip selected" data-color="#2563eb" style="background: #2563eb;" onclick="selectColorChip(this, '#2563eb', 'createHeadColorInput')"><i class="fas fa-check"></i></div>
                        <div class="dt-color-chip" data-color="#0284c7" style="background: #0284c7;" onclick="selectColorChip(this, '#0284c7', 'createHeadColorInput')"></div>
                        <div class="dt-color-chip" data-color="#4f46e5" style="background: #4f46e5;" onclick="selectColorChip(this, '#4f46e5', 'createHeadColorInput')"></div>
                        <div class="dt-color-chip" data-color="#059669" style="background: #059669;" onclick="selectColorChip(this, '#059669', 'createHeadColorInput')"></div>
                        <div class="dt-color-chip" data-color="#d97706" style="background: #d97706;" onclick="selectColorChip(this, '#d97706', 'createHeadColorInput')"></div>
                        <div class="dt-color-chip" data-color="#7c3aed" style="background: #7c3aed;" onclick="selectColorChip(this, '#7c3aed', 'createHeadColorInput')"></div>
                        <input type="hidden" name="color" id="createHeadColorInput" value="#2563eb">
                    </div>
                </div>

                <div class="dt-form-group">
                    <label>Task Head Icon</label>
                    <select name="icon" class="dt-input-control">
                        <option value="fa-tasks">fa-tasks (General Tasks)</option>
                        <option value="fa-book-open">fa-book-open (Reading / Study)</option>
                        <option value="fa-pencil">fa-pencil (Homework / Writing)</option>
                        <option value="fa-star">fa-star (Discipline / Behavior)</option>
                        <option value="fa-heart">fa-heart (Hygiene & Care)</option>
                        <option value="fa-dumbbell">fa-dumbbell (Sports / Fitness)</option>
                        <option value="fa-music">fa-music (Arts & Creativity)</option>
                    </select>
                </div>
            </div>

            <!-- Section 3: Description & Status -->
            <div class="dt-section-card">
                <div class="dt-section-heading">
                    <i class="fas fa-sliders"></i>
                    <span>3. Description & Status</span>
                </div>
                <div class="dt-form-group">
                    <label>Description</label>
                    <textarea name="description" class="dt-input-control" rows="3" placeholder="Briefly describe what this task head covers..."></textarea>
                </div>
                <div class="form-check form-switch pt-2">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="createIsActiveSwitch" checked style="cursor: pointer;">
                    <label class="form-check-label fw-bold" for="createIsActiveSwitch" style="color: var(--theme-text-dark); font-size: 13px; cursor: pointer;">
                        Active & Available for Daily Evaluation
                    </label>
                </div>
            </div>

        </div>

        <!-- Drawer Sticky Footer -->
        <div class="dt-drawer-footer">
            <button type="button" class="btn btn-outline-secondary px-3 py-2 fw-bold" onclick="closeCreateHeadDrawer()" style="border-radius: 10px;">Cancel</button>
            <button type="submit" class="dt-btn-royal">
                <i class="fas fa-check me-1"></i> Save Task Head
            </button>
        </div>
    </form>
</div>

<!-- =========================================================
     2. EDIT TASK HEAD DYNAMIC SIDE SLIDER (DRAWER)
     ========================================================= -->
<div class="dt-drawer-backdrop" id="editHeadDrawerBackdrop" onclick="closeEditHeadDrawer()"></div>

<div class="dt-drawer-panel" id="editHeadDrawer">
    <!-- Drawer Header -->
    <div class="dt-drawer-header">
        <div class="dt-drawer-header-left">
            <div class="dt-drawer-header-icon">
                <i class="fas fa-pencil"></i>
            </div>
            <div>
                <h5 class="dt-drawer-title">Edit Daily Task Head</h5>
                <p class="dt-drawer-subtitle">Update task rubric and visual settings</p>
            </div>
        </div>
        <button type="button" class="dt-drawer-close-btn" onclick="closeEditHeadDrawer()" title="Close (Esc)">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Drawer Form Content -->
    <form id="editHeadSliderForm" method="POST" style="display: flex; flex-direction: column; flex: 1; overflow: hidden; margin: 0;">
        @csrf
        <div class="dt-drawer-body">
            
            <!-- Section 1: Basic Head Info -->
            <div class="dt-section-card">
                <div class="dt-section-heading">
                    <i class="fas fa-circle-info"></i>
                    <span>1. Basic Head Information</span>
                </div>
                <div class="dt-form-group">
                    <label>Task Head Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="editHeadNameInput" class="dt-input-control" required>
                </div>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="dt-form-group">
                            <label>Short Code / Tag</label>
                            <input type="text" name="code" id="editHeadCodeInput" class="dt-input-control">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="dt-form-group">
                            <label>Display Sort Order</label>
                            <input type="number" name="sort_order" id="editHeadSortOrderInput" class="dt-input-control" min="0">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Visual Styling & Icon -->
            <div class="dt-section-card">
                <div class="dt-section-heading">
                    <i class="fas fa-palette"></i>
                    <span>2. Visual Styling & Icon</span>
                </div>
                <div class="dt-form-group">
                    <label>Select Theme Color</label>
                    <div class="dt-color-palette" id="editColorPalette">
                        <div class="dt-color-chip" data-color="#2563eb" style="background: #2563eb;" onclick="selectColorChip(this, '#2563eb', 'editHeadColorInput')"></div>
                        <div class="dt-color-chip" data-color="#0284c7" style="background: #0284c7;" onclick="selectColorChip(this, '#0284c7', 'editHeadColorInput')"></div>
                        <div class="dt-color-chip" data-color="#4f46e5" style="background: #4f46e5;" onclick="selectColorChip(this, '#4f46e5', 'editHeadColorInput')"></div>
                        <div class="dt-color-chip" data-color="#059669" style="background: #059669;" onclick="selectColorChip(this, '#059669', 'editHeadColorInput')"></div>
                        <div class="dt-color-chip" data-color="#d97706" style="background: #d97706;" onclick="selectColorChip(this, '#d97706', 'editHeadColorInput')"></div>
                        <div class="dt-color-chip" data-color="#7c3aed" style="background: #7c3aed;" onclick="selectColorChip(this, '#7c3aed', 'editHeadColorInput')"></div>
                        <input type="hidden" name="color" id="editHeadColorInput" value="#2563eb">
                    </div>
                </div>

                <div class="dt-form-group">
                    <label>Task Head Icon</label>
                    <select name="icon" id="editHeadIconSelect" class="dt-input-control">
                        <option value="fa-tasks">fa-tasks (General Tasks)</option>
                        <option value="fa-book-open">fa-book-open (Reading / Study)</option>
                        <option value="fa-pencil">fa-pencil (Homework / Writing)</option>
                        <option value="fa-star">fa-star (Discipline / Behavior)</option>
                        <option value="fa-heart">fa-heart (Hygiene & Care)</option>
                        <option value="fa-dumbbell">fa-dumbbell (Sports / Fitness)</option>
                        <option value="fa-music">fa-music (Arts & Creativity)</option>
                    </select>
                </div>
            </div>

            <!-- Section 3: Description & Status -->
            <div class="dt-section-card">
                <div class="dt-section-heading">
                    <i class="fas fa-sliders"></i>
                    <span>3. Description & Status</span>
                </div>
                <div class="dt-form-group">
                    <label>Description</label>
                    <textarea name="description" id="editHeadDescInput" class="dt-input-control" rows="3"></textarea>
                </div>
                <div class="form-check form-switch pt-2">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="editIsActiveSwitch" style="cursor: pointer;">
                    <label class="form-check-label fw-bold" for="editIsActiveSwitch" style="color: var(--theme-text-dark); font-size: 13px; cursor: pointer;">
                        Active & Available for Daily Evaluation
                    </label>
                </div>
            </div>

        </div>

        <!-- Drawer Sticky Footer -->
        <div class="dt-drawer-footer">
            <button type="button" class="btn btn-outline-secondary px-3 py-2 fw-bold" onclick="closeEditHeadDrawer()" style="border-radius: 10px;">Cancel</button>
            <button type="submit" class="dt-btn-royal">
                <i class="fas fa-check me-1"></i> Update Task Head
            </button>
        </div>
    </form>
</div>

<script>
    function selectColorChip(chip, color, inputId) {
        const palette = chip.parentElement;
        palette.querySelectorAll('.dt-color-chip').forEach(c => {
            c.classList.remove('selected');
            c.innerHTML = '';
        });
        chip.classList.add('selected');
        chip.innerHTML = '<i class="fas fa-check"></i>';
        document.getElementById(inputId).value = color;
    }

    function openCreateHeadDrawer() {
        document.getElementById('createHeadDrawerBackdrop').classList.add('active');
        document.getElementById('createHeadDrawer').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeCreateHeadDrawer() {
        document.getElementById('createHeadDrawerBackdrop').classList.remove('active');
        document.getElementById('createHeadDrawer').classList.remove('active');
        document.body.style.overflow = '';
    }

    function editHeadDrawer(head) {
        document.getElementById('editHeadSliderForm').action = "{{ url('school/daily-tasks/heads/update') }}/" + head.id;
        document.getElementById('editHeadNameInput').value = head.name || '';
        document.getElementById('editHeadCodeInput').value = head.code || '';
        document.getElementById('editHeadSortOrderInput').value = head.sort_order || 0;
        document.getElementById('editHeadDescInput').value = head.description || '';
        document.getElementById('editIsActiveSwitch').checked = Boolean(head.is_active);
        document.getElementById('editHeadIconSelect').value = head.icon || 'fa-tasks';

        // Select color chip
        const color = head.color || '#2563eb';
        document.getElementById('editHeadColorInput').value = color;
        const palette = document.getElementById('editColorPalette');
        let matched = false;
        palette.querySelectorAll('.dt-color-chip').forEach(c => {
            c.classList.remove('selected');
            c.innerHTML = '';
            if (c.getAttribute('data-color') === color) {
                c.classList.add('selected');
                c.innerHTML = '<i class="fas fa-check"></i>';
                matched = true;
            }
        });
        if (!matched && palette.firstElementChild) {
            palette.firstElementChild.classList.add('selected');
            palette.firstElementChild.innerHTML = '<i class="fas fa-check"></i>';
        }

        document.getElementById('editHeadDrawerBackdrop').classList.add('active');
        document.getElementById('editHeadDrawer').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeEditHeadDrawer() {
        document.getElementById('editHeadDrawerBackdrop').classList.remove('active');
        document.getElementById('editHeadDrawer').classList.remove('active');
        document.body.style.overflow = '';
    }

    // Keyboard ESC key listener
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCreateHeadDrawer();
            closeEditHeadDrawer();
        }
    });
</script>
@endsection
