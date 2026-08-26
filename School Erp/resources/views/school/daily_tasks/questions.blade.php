@extends('layouts.app')

@section('page-title', 'Daily Task Questions & Class Setup')

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

    /* Page Header Banner */
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

    /* KPI Stats Grid */
    .dt-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
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
        font-size: 26px;
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

    /* Table Card Panel */
    .dt-card-panel {
        background: var(--theme-card-bg);
        border: 1.5px solid var(--theme-blue-border);
        border-radius: 18px;
        box-shadow: var(--theme-shadow-md);
        overflow: hidden;
        margin-bottom: 30px;
        width: 100%;
    }

    /* Clean Horizontal Toolbar */
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
        gap: 12px;
        width: 100%;
        flex-wrap: wrap;
        margin: 0;
    }

    .dt-search-box {
        position: relative;
        flex: 1;
        min-width: 240px;
        max-width: 380px;
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
        flex-wrap: wrap;
    }

    .dt-select-filter {
        padding: 10px 14px;
        border-radius: 12px;
        border: 1.5px solid var(--theme-blue-border);
        background: #ffffff;
        color: var(--theme-text-dark);
        font-size: 13px;
        font-weight: 600;
        outline: none;
        cursor: pointer;
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

    @media (max-width: 992px) {
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

    /* Full Width 100% Table */
    .dt-table-wrap {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .dt-table {
        width: 100% !important;
        min-width: 900px;
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
        padding: 14px 18px;
        border-bottom: 2px solid var(--theme-blue-border);
        border-top: none;
        white-space: nowrap;
    }

    .dt-table tbody tr td {
        padding: 16px 18px;
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

    .col-q-num { width: 60px; text-align: center; }
    .col-q-text { min-width: 250px; }
    .col-q-head { width: 140px; }
    .col-q-class { width: 150px; }
    .col-q-target { width: 170px; }
    .col-q-mode { width: 150px; }
    .col-q-status { width: 110px; text-align: center; }
    .col-q-actions { width: 110px; text-align: right; }

    .role-tag-both {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        color: #1e40af;
        border: 1px solid #bfdbfe;
        padding: 4px 10px;
        border-radius: 10px;
        font-size: 11.5px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .role-tag-ct {
        background: #f0f7ff;
        color: #2563eb;
        border: 1px solid #dbeafe;
        padding: 4px 10px;
        border-radius: 10px;
        font-size: 11.5px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .role-tag-st {
        background: #e0f2fe;
        color: #0369a1;
        border: 1px solid #bae6fd;
        padding: 4px 10px;
        border-radius: 10px;
        font-size: 11.5px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .eval-mode-pill {
        background: #f8faff;
        color: #1e40af;
        border: 1px solid #dbeafe;
        padding: 4px 9px;
        border-radius: 8px;
        font-size: 11.5px;
        font-weight: 700;
    }

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

    .dt-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
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
        max-width: 460px;
        margin: 0 auto 20px auto;
        line-height: 1.5;
    }

    /* Drawer Styles */
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
        width: 640px;
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
                <i class="fas fa-clipboard-question"></i>
            </div>
            <div>
                <h4 class="dt-header-title">Daily Task Questions & Class Setup</h4>
                <p class="dt-header-subtitle">Configure class-wise evaluation criteria for <strong>Class Teachers</strong> and <strong>Subject Teachers</strong></p>
            </div>
        </div>
        <div class="btn-action-wrap">
            <button type="button" class="dt-btn-royal" onclick="openCreateQuestionDrawer()">
                <i class="fas fa-plus"></i> Add New Question
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

    <!-- KPI Statistics Grid -->
    <div class="dt-stats-grid anim-slide-up">
        <div class="dt-stat-box">
            <div>
                <div class="dt-stat-num">{{ $stats['total'] }}</div>
                <div class="dt-stat-label">Total Questions</div>
            </div>
            <div class="dt-header-icon" style="width: 46px; height: 46px; font-size: 19px;">
                <i class="fas fa-list-check"></i>
            </div>
        </div>
        <div class="dt-stat-box">
            <div>
                <div class="dt-stat-num" style="color: #2563eb;">{{ $stats['class_teacher'] }}</div>
                <div class="dt-stat-label">Class Teacher Tasks</div>
            </div>
            <div class="dt-header-icon" style="background: #f0f7ff; color: #2563eb; width: 46px; height: 46px; font-size: 19px;">
                <i class="fas fa-chalkboard-user"></i>
            </div>
        </div>
        <div class="dt-stat-box">
            <div>
                <div class="dt-stat-num" style="color: #1d4ed8;">{{ $stats['subject_teacher'] }}</div>
                <div class="dt-stat-label">Subject Teacher Tasks</div>
            </div>
            <div class="dt-header-icon" style="background: #dbeafe; color: #1e40af; width: 46px; height: 46px; font-size: 19px;">
                <i class="fas fa-book-bookmark"></i>
            </div>
        </div>
        <div class="dt-stat-box">
            <div>
                <div class="dt-stat-num" style="color: #0284c7;">{{ $stats['active'] }}</div>
                <div class="dt-stat-label">Active Questions</div>
            </div>
            <div class="dt-header-icon" style="background: #e0f2fe; color: #0284c7; width: 46px; height: 46px; font-size: 19px;">
                <i class="fas fa-circle-check"></i>
            </div>
        </div>
    </div>

    <!-- Table Card Panel (100% Full Width) -->
    <div class="dt-card-panel anim-slide-up">
        
        <!-- Clean Horizontal Toolbar -->
        <div class="dt-card-toolbar">
            <form method="GET" action="{{ route('school.daily-tasks.questions') }}" class="dt-toolbar-flex">
                <div class="dt-search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search questions..." onchange="this.form.submit()">
                </div>
                
                <div class="dt-filter-actions">
                    <select name="class_id" class="dt-select-filter" onchange="this.form.submit()">
                        <option value="all" {{ $classId === 'all' || !$classId ? 'selected' : '' }}>All Classes</option>
                        <option value="global" {{ $classId === 'global' ? 'selected' : '' }}>Global (All Classes)</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ (string)$classId === (string)$c->id ? 'selected' : '' }}>Class: {{ $c->name }}</option>
                        @endforeach
                    </select>

                    <select name="head_id" class="dt-select-filter" onchange="this.form.submit()">
                        <option value="all" {{ $headId === 'all' || !$headId ? 'selected' : '' }}>All Task Heads</option>
                        @foreach($heads as $h)
                            <option value="{{ $h->id }}" {{ (string)$headId === (string)$h->id ? 'selected' : '' }}>{{ $h->name }}</option>
                        @endforeach
                    </select>

                    <select name="target_role" class="dt-select-filter" onchange="this.form.submit()">
                        <option value="all" {{ $targetRole === 'all' ? 'selected' : '' }}>All Target Roles</option>
                        <option value="both" {{ $targetRole === 'both' ? 'selected' : '' }}>Both Roles</option>
                        <option value="class_teacher" {{ $targetRole === 'class_teacher' ? 'selected' : '' }}>Class Teacher</option>
                        <option value="subject_teacher" {{ $targetRole === 'subject_teacher' ? 'selected' : '' }}>Subject Teacher</option>
                    </select>

                    @if($search || ($classId && $classId !== 'all') || ($headId && $headId !== 'all') || ($targetRole && $targetRole !== 'all'))
                        <a href="{{ route('school.daily-tasks.questions') }}" class="dt-btn-reset" title="Reset Filters">
                            <i class="fas fa-rotate-left"></i> Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- 100% Full Width Table View -->
        <div class="dt-table-wrap">
            <table class="dt-table">
                <thead>
                    <tr>
                        <th class="col-q-num">#</th>
                        <th class="col-q-text">Question / Task Statement</th>
                        <th class="col-q-head">Task Head</th>
                        <th class="col-q-class">Class & Section</th>
                        <th class="col-q-target">Target Teacher</th>
                        <th class="col-q-mode">Evaluation Mode</th>
                        <th class="col-q-status">Status</th>
                        <th class="col-q-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($questions as $q)
                        <tr>
                            <td class="col-q-num fw-bold" style="color: var(--theme-primary-blue);">
                                {{ $loop->iteration }}
                            </td>
                            <td class="col-q-text">
                                <div class="fw-bold" style="font-size: 14px; color: var(--theme-text-dark);">
                                    {{ $q->question }}
                                </div>
                                @if($q->subject)
                                    <div class="mt-1">
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-0.5" style="font-size: 11px;">
                                            <i class="fas fa-book me-1"></i> Subject: {{ $q->subject->name }}
                                        </span>
                                    </div>
                                @endif
                            </td>
                            <td class="col-q-head">
                                @if($q->head)
                                    <span class="badge bg-light text-primary border border-primary-subtle px-2.5 py-1 fw-bold">
                                        <i class="fas {{ $q->head->icon ?: 'fa-tasks' }} me-1"></i> {{ $q->head->name }}
                                    </span>
                                @else
                                    <span class="text-muted small">General</span>
                                @endif
                            </td>
                            <td class="col-q-class">
                                @if($q->schoolClass)
                                    <span class="fw-bold text-primary">{{ $q->schoolClass->name }}</span>
                                    @if($q->section)
                                        <span class="text-muted small">({{ $q->section->name }})</span>
                                    @else
                                        <span class="text-muted small">(All Sec)</span>
                                    @endif
                                @else
                                    <span class="badge bg-light text-secondary border">Global (All Classes)</span>
                                @endif
                            </td>
                            <td class="col-q-target">
                                @if($q->target_role === 'both')
                                    <span class="role-tag-both"><i class="fas fa-users"></i> Both Roles</span>
                                @elseif($q->target_role === 'class_teacher')
                                    <span class="role-tag-ct"><i class="fas fa-chalkboard-user"></i> Class Teacher</span>
                                @else
                                    <span class="role-tag-st"><i class="fas fa-book-open-reader"></i> Subject Teacher</span>
                                @endif
                            </td>
                            <td class="col-q-mode">
                                @if($q->evaluation_type === 'rating')
                                    <span class="eval-mode-pill"><i class="fas fa-star text-warning me-1"></i> 1-5 Stars</span>
                                @elseif($q->evaluation_type === 'score')
                                    <span class="eval-mode-pill"><i class="fas fa-calculator text-primary me-1"></i> Marks (Max: {{ $q->max_score }})</span>
                                @elseif($q->evaluation_type === 'options')
                                    <span class="eval-mode-pill"><i class="fas fa-list-ul text-primary me-1"></i> Options</span>
                                @elseif($q->evaluation_type === 'boolean')
                                    <span class="eval-mode-pill"><i class="fas fa-toggle-on text-primary me-1"></i> Yes / No</span>
                                @else
                                    <span class="eval-mode-pill"><i class="fas fa-comment-dots text-secondary me-1"></i> Remarks</span>
                                @endif
                            </td>
                            <td class="col-q-status">
                                <form method="POST" action="{{ route('school.daily-tasks.questions.toggle', $q->id) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="border-0 bg-transparent p-0" title="Click to toggle status">
                                        @if($q->is_active)
                                            <span class="dt-status-pill dt-status-active"><i class="fas fa-circle-check"></i> Active</span>
                                        @else
                                            <span class="dt-status-pill dt-status-inactive"><i class="fas fa-circle-xmark"></i> Inactive</span>
                                        @endif
                                    </button>
                                </form>
                            </td>
                            <td class="col-q-actions">
                                <div class="d-flex align-items-center justify-content-end gap-2">
                                    <button type="button" class="dt-action-icon-btn" title="Edit Question (Side Slider)" onclick="editQuestionDrawer({{ json_encode($q) }})">
                                        <i class="fas fa-pencil"></i>
                                    </button>
                                    <form method="POST" action="{{ route('school.daily-tasks.questions.delete', $q->id) }}" onsubmit="return confirm('Are you sure you want to delete this question?');" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dt-action-icon-btn btn-trash" title="Delete Question">
                                            <i class="fas fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="dt-empty-box">
                                    <div class="dt-empty-icon-wrap">
                                        <i class="fas fa-clipboard-question"></i>
                                    </div>
                                    <div class="dt-empty-title">No Daily Task Questions Configured</div>
                                    <div class="dt-empty-desc">Create evaluation questions tailored for Class Teachers and Subject Teachers across all or specific classes.</div>
                                    <button type="button" class="dt-btn-royal" onclick="openCreateQuestionDrawer()">
                                        <i class="fas fa-plus"></i> Add First Question
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($questions->hasPages())
            <div class="p-3 border-top d-flex justify-content-end">
                {{ $questions->links() }}
            </div>
        @endif
    </div>
</div>

<!-- =========================================================
     1. CREATE QUESTION DYNAMIC SIDE SLIDER (DRAWER)
     ========================================================= -->
<div class="dt-drawer-backdrop" id="createQuestionDrawerBackdrop" onclick="closeCreateQuestionDrawer()"></div>

<div class="dt-drawer-panel" id="createQuestionDrawer">
    <!-- Header -->
    <div class="dt-drawer-header">
        <div class="dt-drawer-header-left">
            <div class="dt-drawer-header-icon">
                <i class="fas fa-clipboard-question"></i>
            </div>
            <div>
                <h5 class="dt-drawer-title">Add Daily Task Question</h5>
                <p class="dt-drawer-subtitle">Class & Teacher evaluation criteria builder</p>
            </div>
        </div>
        <button type="button" class="dt-drawer-close-btn" onclick="closeCreateQuestionDrawer()" title="Close (Esc)">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Form Content -->
    <form method="POST" action="{{ route('school.daily-tasks.questions.store') }}" style="display: flex; flex-direction: column; flex: 1; overflow: hidden; margin: 0;">
        @csrf
        <div class="dt-drawer-body">
            
            <!-- Section 1: Question Text & Category -->
            <div class="dt-section-card">
                <div class="dt-section-heading">
                    <i class="fas fa-circle-info"></i>
                    <span>1. Evaluation Question & Rubric Head</span>
                </div>
                <div class="dt-form-group">
                    <label>Question Statement <span class="text-danger">*</span></label>
                    <textarea name="question" class="dt-input-control" rows="2" placeholder="e.g., Completed assigned homework on time?, Attentive & disciplined in class?, Math concept practice completed?" required></textarea>
                </div>
                <div class="dt-form-group">
                    <label>Task Head (Category)</label>
                    <select name="daily_task_head_id" class="dt-input-control">
                        <option value="">-- Select Task Head (Optional) --</option>
                        @foreach($heads as $h)
                            <option value="{{ $h->id }}">{{ $h->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Section 2: Target Role & Class Assignment -->
            <div class="dt-section-card">
                <div class="dt-section-heading">
                    <i class="fas fa-users-gear"></i>
                    <span>2. Target Role & Class Assignment</span>
                </div>
                <div class="dt-form-group">
                    <label>Target Teacher Role <span class="text-danger">*</span></label>
                    <select name="target_role" id="createTargetRole" class="dt-input-control" onchange="toggleSubjectField(this.value, 'createSubjectDiv')" required>
                        <option value="both">Both (Class Teacher & Subject Teacher)</option>
                        <option value="class_teacher">Class Teacher Only</option>
                        <option value="subject_teacher">Subject Teacher Only</option>
                    </select>
                    <small class="text-muted" style="font-size: 11px;">Controls whether this question appears for Class Teacher, Subject Teacher, or both.</small>
                </div>

                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="dt-form-group">
                            <label>Applicable Class</label>
                            <select name="class_id" id="createClassSelect" class="dt-input-control" onchange="loadSectionsForClass(this.value, 'createSectionSelect')">
                                <option value="">All Classes (Global)</option>
                                @foreach($classes as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="dt-form-group">
                            <label>Applicable Section</label>
                            <select name="section_id" id="createSectionSelect" class="dt-input-control">
                                <option value="">All Sections</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="dt-form-group" id="createSubjectDiv" style="display: none; margin-top: 10px;">
                    <label>Specific Subject (Optional for Subject Teacher)</label>
                    <select name="subject_id" class="dt-input-control">
                        <option value="">-- Any / All Subjects --</option>
                        @foreach($subjects as $s)
                            <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->schoolClass->name ?? 'All' }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Section 3: Evaluation Mode & Scoring -->
            <div class="dt-section-card">
                <div class="dt-section-heading">
                    <i class="fas fa-sliders"></i>
                    <span>3. Evaluation Mode & Scoring</span>
                </div>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="dt-form-group">
                            <label>Evaluation Mode <span class="text-danger">*</span></label>
                            <select name="evaluation_type" id="createEvalType" class="dt-input-control" onchange="toggleMaxScoreField(this.value, 'createMaxScoreDiv')" required>
                                <option value="rating">Star Rating (1 to 5 Stars)</option>
                                <option value="score">Numeric Score / Marks</option>
                                <option value="options">Status Options (Excellent, Good, Average...)</option>
                                <option value="boolean">Yes / No</option>
                                <option value="remark">Teacher Remark Only</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6" id="createMaxScoreDiv" style="display: none;">
                        <div class="dt-form-group">
                            <label>Maximum Score / Marks</label>
                            <input type="number" name="max_score" class="dt-input-control" value="10" min="1" max="100">
                        </div>
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-sm-6">
                        <div class="dt-form-group">
                            <label>Display Sort Order</label>
                            <input type="number" name="sort_order" class="dt-input-control" value="0" min="0">
                        </div>
                    </div>
                    <div class="col-sm-6 d-flex align-items-center pt-3 gap-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="createQActive" checked style="cursor: pointer;">
                            <label class="form-check-label fw-bold" for="createQActive" style="color: var(--theme-text-dark); font-size: 13px; cursor: pointer;">Active</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_mandatory" value="1" id="createQMandatory" style="cursor: pointer;">
                            <label class="form-check-label fw-bold" for="createQMandatory" style="color: var(--theme-text-dark); font-size: 13px; cursor: pointer;">Mandatory</label>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Footer -->
        <div class="dt-drawer-footer">
            <button type="button" class="btn btn-outline-secondary px-3 py-2 fw-bold" onclick="closeCreateQuestionDrawer()" style="border-radius: 10px;">Cancel</button>
            <button type="submit" class="dt-btn-royal">
                <i class="fas fa-check me-1"></i> Save Question
            </button>
        </div>
    </form>
</div>

<!-- =========================================================
     2. EDIT QUESTION DYNAMIC SIDE SLIDER (DRAWER)
     ========================================================= -->
<div class="dt-drawer-backdrop" id="editQuestionDrawerBackdrop" onclick="closeEditQuestionDrawer()"></div>

<div class="dt-drawer-panel" id="editQuestionDrawer">
    <!-- Header -->
    <div class="dt-drawer-header">
        <div class="dt-drawer-header-left">
            <div class="dt-drawer-header-icon">
                <i class="fas fa-pencil"></i>
            </div>
            <div>
                <h5 class="dt-drawer-title">Edit Daily Task Question</h5>
                <p class="dt-drawer-subtitle">Update criteria, target role, or scoring settings</p>
            </div>
        </div>
        <button type="button" class="dt-drawer-close-btn" onclick="closeEditQuestionDrawer()" title="Close (Esc)">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Form Content -->
    <form id="editQuestionSliderForm" method="POST" style="display: flex; flex-direction: column; flex: 1; overflow: hidden; margin: 0;">
        @csrf
        <div class="dt-drawer-body">
            
            <!-- Section 1: Question Text & Category -->
            <div class="dt-section-card">
                <div class="dt-section-heading">
                    <i class="fas fa-circle-info"></i>
                    <span>1. Evaluation Question & Rubric Head</span>
                </div>
                <div class="dt-form-group">
                    <label>Question Statement <span class="text-danger">*</span></label>
                    <textarea name="question" id="editQText" class="dt-input-control" rows="2" required></textarea>
                </div>
                <div class="dt-form-group">
                    <label>Task Head (Category)</label>
                    <select name="daily_task_head_id" id="editQHead" class="dt-input-control">
                        <option value="">-- Select Task Head (Optional) --</option>
                        @foreach($heads as $h)
                            <option value="{{ $h->id }}">{{ $h->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Section 2: Target Role & Class Assignment -->
            <div class="dt-section-card">
                <div class="dt-section-heading">
                    <i class="fas fa-users-gear"></i>
                    <span>2. Target Role & Class Assignment</span>
                </div>
                <div class="dt-form-group">
                    <label>Target Teacher Role <span class="text-danger">*</span></label>
                    <select name="target_role" id="editQTargetRole" class="dt-input-control" onchange="toggleSubjectField(this.value, 'editSubjectDiv')" required>
                        <option value="both">Both (Class Teacher & Subject Teacher)</option>
                        <option value="class_teacher">Class Teacher Only</option>
                        <option value="subject_teacher">Subject Teacher Only</option>
                    </select>
                </div>

                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="dt-form-group">
                            <label>Applicable Class</label>
                            <select name="class_id" id="editQClass" class="dt-input-control" onchange="loadSectionsForClass(this.value, 'editQSection')">
                                <option value="">All Classes (Global)</option>
                                @foreach($classes as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="dt-form-group">
                            <label>Applicable Section</label>
                            <select name="section_id" id="editQSection" class="dt-input-control">
                                <option value="">All Sections</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="dt-form-group" id="editSubjectDiv" style="display: none; margin-top: 10px;">
                    <label>Specific Subject (Optional for Subject Teacher)</label>
                    <select name="subject_id" id="editQSubject" class="dt-input-control">
                        <option value="">-- Any / All Subjects --</option>
                        @foreach($subjects as $s)
                            <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->schoolClass->name ?? 'All' }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Section 3: Evaluation Mode & Scoring -->
            <div class="dt-section-card">
                <div class="dt-section-heading">
                    <i class="fas fa-sliders"></i>
                    <span>3. Evaluation Mode & Scoring</span>
                </div>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="dt-form-group">
                            <label>Evaluation Mode <span class="text-danger">*</span></label>
                            <select name="evaluation_type" id="editQEvalType" class="dt-input-control" onchange="toggleMaxScoreField(this.value, 'editMaxScoreDiv')" required>
                                <option value="rating">Star Rating (1 to 5 Stars)</option>
                                <option value="score">Numeric Score / Marks</option>
                                <option value="options">Status Options (Excellent, Good, Average...)</option>
                                <option value="boolean">Yes / No</option>
                                <option value="remark">Teacher Remark Only</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6" id="editMaxScoreDiv" style="display: none;">
                        <div class="dt-form-group">
                            <label>Maximum Score / Marks</label>
                            <input type="number" name="max_score" id="editQMaxScore" class="dt-input-control" value="10" min="1" max="100">
                        </div>
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-sm-6">
                        <div class="dt-form-group">
                            <label>Display Sort Order</label>
                            <input type="number" name="sort_order" id="editQSortOrder" class="dt-input-control" value="0" min="0">
                        </div>
                    </div>
                    <div class="col-sm-6 d-flex align-items-center pt-3 gap-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="editQActive" style="cursor: pointer;">
                            <label class="form-check-label fw-bold" for="editQActive" style="color: var(--theme-text-dark); font-size: 13px; cursor: pointer;">Active</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_mandatory" value="1" id="editQMandatory" style="cursor: pointer;">
                            <label class="form-check-label fw-bold" for="editQMandatory" style="color: var(--theme-text-dark); font-size: 13px; cursor: pointer;">Mandatory</label>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Footer -->
        <div class="dt-drawer-footer">
            <button type="button" class="btn btn-outline-secondary px-3 py-2 fw-bold" onclick="closeEditQuestionDrawer()" style="border-radius: 10px;">Cancel</button>
            <button type="submit" class="dt-btn-royal">
                <i class="fas fa-check me-1"></i> Update Question
            </button>
        </div>
    </form>
</div>

<script>
    const classesData = @json($classes);

    function openCreateQuestionDrawer() {
        document.getElementById('createQuestionDrawerBackdrop').classList.add('active');
        document.getElementById('createQuestionDrawer').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeCreateQuestionDrawer() {
        document.getElementById('createQuestionDrawerBackdrop').classList.remove('active');
        document.getElementById('createQuestionDrawer').classList.remove('active');
        document.body.style.overflow = '';
    }

    function toggleSubjectField(role, divId) {
        const div = document.getElementById(divId);
        if (role === 'subject_teacher' || role === 'both') {
            div.style.display = 'block';
        } else {
            div.style.display = 'none';
        }
    }

    function toggleMaxScoreField(type, divId) {
        const div = document.getElementById(divId);
        if (type === 'score') {
            div.style.display = 'block';
        } else {
            div.style.display = 'none';
        }
    }

    function loadSectionsForClass(classId, selectId, selectedSectionId = null) {
        const select = document.getElementById(selectId);
        select.innerHTML = '<option value="">All Sections</option>';

        if (!classId) return;

        const cls = classesData.find(c => String(c.id) === String(classId));
        if (cls && cls.sections) {
            cls.sections.forEach(sec => {
                const opt = document.createElement('option');
                opt.value = sec.id;
                opt.textContent = sec.name;
                if (selectedSectionId && String(sec.id) === String(selectedSectionId)) {
                    opt.selected = true;
                }
                select.appendChild(opt);
            });
        }
    }

    function editQuestionDrawer(q) {
        document.getElementById('editQuestionSliderForm').action = "{{ url('school/daily-tasks/questions/update') }}/" + q.id;
        document.getElementById('editQText').value = q.question || '';
        document.getElementById('editQHead').value = q.daily_task_head_id || '';
        document.getElementById('editQTargetRole').value = q.target_role || 'both';
        document.getElementById('editQClass').value = q.class_id || '';
        document.getElementById('editQEvalType').value = q.evaluation_type || 'rating';
        document.getElementById('editQMaxScore').value = q.max_score || 10;
        document.getElementById('editQSortOrder').value = q.sort_order || 0;
        document.getElementById('editQActive').checked = Boolean(q.is_active);
        document.getElementById('editQMandatory').checked = Boolean(q.is_mandatory);

        toggleSubjectField(q.target_role, 'editSubjectDiv');
        toggleMaxScoreField(q.evaluation_type, 'editMaxScoreDiv');

        loadSectionsForClass(q.class_id, 'editQSection', q.section_id);
        document.getElementById('editQSubject').value = q.subject_id || '';

        document.getElementById('editQuestionDrawerBackdrop').classList.add('active');
        document.getElementById('editQuestionDrawer').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeEditQuestionDrawer() {
        document.getElementById('editQuestionDrawerBackdrop').classList.remove('active');
        document.getElementById('editQuestionDrawer').classList.remove('active');
        document.body.style.overflow = '';
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCreateQuestionDrawer();
            closeEditQuestionDrawer();
        }
    });
</script>
@endsection
