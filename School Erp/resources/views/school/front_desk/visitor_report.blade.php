@extends('layouts.app')

@section('title', 'Visitor Report - Front Desk')
@section('page-title', 'Visitor Report')

@section('content')
<style>
    :root {
        --theme-royal-blue: #0038b8;
        --theme-royal-blue-hover: #002d99;
        --theme-blue-dark: #002366;
        --theme-blue-header: #002c77;
        --theme-blue-subtle: #eff6ff;
        --theme-blue-ice: #f4f8ff;
        --theme-blue-border: #bfdbfe;
        --theme-border-dash: #cbd5e1;
        --theme-text-main: #1e293b;
        --theme-text-muted: #64748b;
    }

    .report-page-container {
        width: 100%;
        padding: 0 4px 30px 4px;
        font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    /* Top Breadcrumb & Page Title */
    .report-top-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 20px;
    }

    .report-breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: var(--theme-text-muted);
        font-weight: 500;
        margin-bottom: 4px;
    }

    .report-breadcrumb a {
        color: var(--theme-text-muted);
        text-decoration: none;
        transition: color 0.15s ease;
    }

    .report-breadcrumb a:hover {
        color: var(--theme-royal-blue);
    }

    .report-breadcrumb .sep {
        font-size: 10px;
        opacity: 0.5;
    }

    .report-page-title {
        font-size: 24px;
        font-weight: 800;
        color: var(--theme-text-main);
        letter-spacing: -0.4px;
        margin: 0;
    }

    /* Top Quick Stats */
    .report-stats-strip {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .stat-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 14px;
        border-radius: 30px;
        font-size: 12.5px;
        font-weight: 700;
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }

    .stat-pill.primary {
        border-color: #bfdbfe;
        background: #eff6ff;
        color: var(--theme-royal-blue);
    }

    .stat-pill .count {
        font-size: 14px;
        font-weight: 800;
    }

    /* =========================================================
       FILTER SECTION (EXACT MATCH TO SCREENSHOT)
       ========================================================= */
    .filter-card-container {
        background: #ffffff;
        border: 1.5px dashed var(--theme-border-dash);
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 24px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
    }

    .filter-form-grid {
        display: grid;
        grid-template-columns: 1.2fr 1.2fr 2.5fr auto;
        gap: 16px;
        align-items: flex-end;
    }

    @media (max-width: 1100px) {
        .filter-form-grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 640px) {
        .filter-form-grid {
            grid-template-columns: 1fr;
        }
    }

    .filter-field-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .filter-label {
        font-size: 11px;
        font-weight: 800;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        margin: 0;
    }

    .filter-input-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }

    .filter-input-wrap input {
        width: 100%;
        height: 42px;
        padding: 8px 14px;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        font-size: 13.5px;
        font-weight: 600;
        color: var(--theme-text-main);
        background-color: #ffffff;
        transition: all 0.2s ease;
        outline: none;
    }

    .filter-input-wrap input::placeholder {
        color: #94a3b8;
        font-weight: 500;
        text-transform: uppercase;
        font-size: 12px;
    }

    .filter-input-wrap input:focus {
        border-color: var(--theme-royal-blue);
        box-shadow: 0 0 0 3px rgba(0, 56, 184, 0.12);
    }

    .filter-input-wrap input[type="date"] {
        font-family: inherit;
        text-transform: uppercase;
    }

    /* Button Search (Royal Blue) */
    .btn-search-main {
        height: 42px;
        padding: 0 32px;
        background: var(--theme-royal-blue);
        color: #ffffff;
        border: none;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 800;
        letter-spacing: 0.8px;
        text-transform: uppercase;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 4px 12px rgba(0, 56, 184, 0.22);
        white-space: nowrap;
    }

    .btn-search-main:hover {
        background: var(--theme-royal-blue-hover);
        box-shadow: 0 6px 16px rgba(0, 56, 184, 0.35);
        transform: translateY(-1px);
        color: #ffffff;
    }

    .btn-reset-filters {
        height: 42px;
        padding: 0 16px;
        background: #ffffff;
        color: #64748b;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        font-size: 12.5px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-reset-filters:hover {
        background: #f8fafc;
        border-color: #94a3b8;
        color: #1e293b;
    }

    .filter-actions-wrap {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* =========================================================
       REPORT TABLE CARD (MATCHING SCREENSHOT)
       ========================================================= */
    .report-table-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 56, 184, 0.05), 0 1px 3px rgba(0,0,0,0.03);
        border: 1.5px solid #e2e8f0;
        overflow: hidden;
    }

    .table-toolbar-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 20px;
        background: #ffffff;
        border-bottom: 1px solid #edf2f7;
        flex-wrap: wrap;
        gap: 12px;
    }

    .table-results-counter {
        font-size: 13px;
        font-weight: 700;
        color: #475569;
    }

    .table-results-counter strong {
        color: var(--theme-royal-blue);
    }

    .table-quick-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .btn-table-tool {
        padding: 6px 14px;
        font-size: 12px;
        font-weight: 700;
        color: #475569;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.15s ease;
    }

    .btn-table-tool:hover {
        background: #f8fafc;
        border-color: #94a3b8;
        color: var(--theme-royal-blue);
    }

    .report-table-responsive {
        width: 100%;
        overflow-x: auto;
    }

    .visitor-profile-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        text-align: left;
    }

    /* Royal Blue Table Header */
    .visitor-profile-table thead tr {
        background: var(--theme-blue-header);
        background: linear-gradient(180deg, #002d72 0%, #002366 100%);
    }

    .visitor-profile-table thead th {
        color: #ffffff;
        font-size: 11.5px;
        font-weight: 800;
        letter-spacing: 0.8px;
        text-transform: uppercase;
        padding: 13px 14px;
        border: none;
        white-space: nowrap;
    }

    .visitor-profile-table thead th.text-center {
        text-align: center;
    }

    .visitor-profile-table tbody tr {
        background: #ffffff;
        transition: background 0.15s ease;
        border-bottom: 1px solid #f1f5f9;
    }

    .visitor-profile-table tbody tr:nth-child(even) {
        background: #fafcff;
    }

    .visitor-profile-table tbody tr:hover {
        background: var(--theme-blue-ice);
    }

    .visitor-profile-table tbody td {
        padding: 13px 14px;
        vertical-align: middle;
        font-size: 12.5px;
        color: var(--theme-text-main);
        border-bottom: 1px solid #edf2f7;
    }

    /* Column Styles */
    .col-sno {
        width: 50px;
        font-weight: 700;
        color: #64748b;
        text-align: center;
    }

    .col-name {
        font-weight: 800;
        color: #0f172a;
        font-size: 13px;
        min-width: 160px;
    }

    .col-gender {
        width: 80px;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
    }

    .col-dob {
        width: 110px;
        font-weight: 600;
        color: #334155;
        white-space: nowrap;
    }

    .col-mobile {
        width: 120px;
        font-weight: 700;
        color: #0f172a;
        white-space: nowrap;
    }

    .col-email {
        min-width: 160px;
        color: #475569;
        word-break: break-all;
    }

    .col-state {
        min-width: 110px;
        color: #334155;
        font-weight: 600;
    }

    .col-city {
        min-width: 100px;
        color: #334155;
        font-weight: 600;
    }

    .col-address {
        min-width: 160px;
        color: #475569;
    }

    .col-pincode {
        width: 90px;
        color: #334155;
        font-weight: 700;
    }

    /* Actions Column Buttons */
    .col-action {
        min-width: 150px;
        text-align: center;
        white-space: nowrap;
    }

    .action-btn-group {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    /* Edit Button (Pencil Icon) */
    .btn-action-edit {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        background: #ffffff;
        color: #1e293b;
        border: 1px solid #cbd5e1;
        border-radius: 5px;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.15s ease;
    }

    .btn-action-edit:hover {
        background: #eff6ff;
        border-color: var(--theme-royal-blue);
        color: var(--theme-royal-blue);
        transform: translateY(-1px);
    }

    /* Delete Button (Trash Icon) */
    .btn-action-delete {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        background: #ffffff;
        color: #dc2626;
        border: 1px solid #fecaca;
        border-radius: 5px;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.15s ease;
    }

    .btn-action-delete:hover {
        background: #fef2f2;
        border-color: #dc2626;
        color: #b91c1c;
        transform: translateY(-1px);
    }

    /* Print Pass Shortcut */
    .btn-action-pass {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 8px;
        background: #eff6ff;
        color: var(--theme-royal-blue);
        border: 1px solid #bfdbfe;
        border-radius: 5px;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.15s ease;
    }

    .btn-action-pass:hover {
        background: var(--theme-royal-blue);
        border-color: var(--theme-royal-blue);
        color: #ffffff;
        transform: translateY(-1px);
    }

    /* =========================================================
       DYNAMIC SIDE SLIDER (OFFCANVAS DRAWER)
       Responsive on all devices with rich bezier animation
       ========================================================= */
    .drawer-backdrop-overlay {
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

    .drawer-backdrop-overlay.active {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
    }

    .drawer-slider-panel {
        position: fixed;
        top: 0;
        right: 0;
        bottom: 0;
        height: 100vh;
        height: 100dvh;
        width: 680px;
        max-width: 100%;
        background: #ffffff;
        z-index: 1060;
        box-shadow: -15px 0 45px rgba(0, 56, 184, 0.18), -2px 0 12px rgba(0, 0, 0, 0.08);
        transform: translateX(100%);
        transition: transform 0.38s cubic-bezier(0.16, 1, 0.3, 1);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .drawer-slider-panel.active {
        transform: translateX(0);
    }

    /* Screen Responsiveness for Slider */
    @media (max-width: 768px) {
        .drawer-slider-panel {
            width: 580px;
            max-width: 90vw;
        }
    }

    @media (max-width: 576px) {
        .drawer-slider-panel {
            width: 100vw !important;
            max-width: 100vw !important;
        }
    }

    /* Drawer Header (Royal Blue Gradient) */
    .drawer-header-bar {
        background: linear-gradient(135deg, #002466 0%, #0038b8 100%);
        color: #ffffff;
        padding: 16px 22px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0;
        box-shadow: 0 4px 15px rgba(0, 56, 184, 0.15);
        position: relative;
        z-index: 2;
    }

    .drawer-title-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .drawer-icon-badge {
        width: 42px;
        height: 42px;
        background: rgba(255, 255, 255, 0.18);
        border: 1.5px solid rgba(255, 255, 255, 0.35);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 19px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        flex-shrink: 0;
    }

    .drawer-title-text {
        font-size: 17px;
        font-weight: 800;
        margin: 0;
        letter-spacing: -0.3px;
        color: #ffffff;
    }

    .drawer-pass-pill {
        background: #ffffff;
        color: var(--theme-royal-blue);
        font-size: 11px;
        font-weight: 800;
        padding: 2px 9px;
        border-radius: 6px;
        display: inline-block;
        letter-spacing: 0.3px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .btn-drawer-close {
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

    .btn-drawer-close:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: rotate(90deg) scale(1.05);
    }

    /* Drawer Scrollable Content Body */
    .drawer-body-content {
        padding: 24px;
        overflow-y: auto;
        overflow-x: hidden;
        flex: 1;
        background: #ffffff;
        scroll-behavior: smooth;
        position: relative;
    }

    /* Custom Scrollbar for Drawer */
    .drawer-body-content::-webkit-scrollbar {
        width: 6px;
    }
    .drawer-body-content::-webkit-scrollbar-track {
        background: #f1f5f9;
    }
    .drawer-body-content::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    .drawer-body-content::-webkit-scrollbar-thumb:hover {
        background: var(--theme-royal-blue);
    }

    /* Drawer Form Sections with Micro-Animations */
    .slider-section-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px 18px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .slider-section-card:hover {
        border-color: var(--theme-blue-border);
        box-shadow: 0 4px 16px rgba(0, 56, 184, 0.06);
    }

    .slider-section-heading {
        font-size: 12.5px;
        font-weight: 800;
        color: var(--theme-royal-blue);
        text-transform: uppercase;
        letter-spacing: 0.6px;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 14px;
        padding-bottom: 8px;
        border-bottom: 1.5px solid var(--theme-blue-subtle);
    }

    .slider-grid-2 {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 14px;
    }

    .slider-grid-3 {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
    }

    @media (max-width: 640px) {
        .slider-grid-2, .slider-grid-3 {
            grid-template-columns: 1fr;
        }
    }

    .form-group-item {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .form-group-item label {
        font-size: 11px;
        font-weight: 800;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        margin: 0;
    }

    .form-group-item input,
    .form-group-item select,
    .form-group-item textarea {
        width: 100%;
        padding: 10px 14px;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        color: #1e293b;
        outline: none;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        background: #ffffff;
    }

    .form-group-item select {
        cursor: pointer;
    }

    .form-group-item input:focus,
    .form-group-item select:focus,
    .form-group-item textarea:focus {
        border-color: var(--theme-royal-blue);
        box-shadow: 0 0 0 3px rgba(0, 56, 184, 0.14);
        background: #ffffff;
    }

    .photo-preview-wrap {
        display: flex;
        align-items: center;
        gap: 16px;
        background: var(--theme-blue-subtle);
        padding: 14px 18px;
        border-radius: 12px;
        border: 1.5px dashed var(--theme-blue-border);
    }

    .photo-thumb-circle {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        border: 3px solid #ffffff;
        box-shadow: 0 4px 12px rgba(0, 56, 184, 0.22);
        object-fit: cover;
        background: #ffffff;
        transition: transform 0.2s ease;
    }

    .photo-thumb-circle:hover {
        transform: scale(1.06);
    }

    /* Drawer Sticky Footer */
    .drawer-footer-bar {
        padding: 16px 24px;
        background: #f8fafc;
        border-top: 1.5px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        flex-shrink: 0;
        z-index: 2;
    }

    .btn-drawer-cancel {
        padding: 10px 22px;
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        color: #475569;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-drawer-cancel:hover {
        background: #f1f5f9;
        border-color: #94a3b8;
        color: #1e293b;
    }

    .btn-drawer-save {
        padding: 10px 28px;
        background: var(--theme-royal-blue);
        border: none;
        color: #ffffff;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 800;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 14px rgba(0, 56, 184, 0.28);
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .btn-drawer-save:hover {
        background: var(--theme-royal-blue-hover);
        box-shadow: 0 6px 18px rgba(0, 56, 184, 0.4);
        transform: translateY(-1px);
    }

    .btn-drawer-save:active {
        transform: translateY(0);
    }

    .btn-print-card-direct {
        padding: 10px 18px;
        background: #ffffff;
        border: 1.5px solid var(--theme-royal-blue);
        color: var(--theme-royal-blue);
        border-radius: 8px;
        font-size: 13px;
        font-weight: 800;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
    }

    .btn-print-card-direct:hover {
        background: var(--theme-royal-blue);
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 56, 184, 0.2);
    }

    /* Custom Toast */
    .toast-alert-wrap {
        position: fixed;
        bottom: 24px;
        right: 24px;
        z-index: 1100;
        display: none;
        background: #0f172a;
        color: #ffffff;
        padding: 14px 20px;
        border-radius: 10px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.25);
        font-size: 13.5px;
        font-weight: 600;
        align-items: center;
        gap: 12px;
        animation: toastSlideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }

    @keyframes toastSlideUp {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .toast-alert-wrap.success {
        border-left: 4px solid #22c55e;
    }

    .toast-alert-wrap.error {
        border-left: 4px solid #ef4444;
    }

    /* Loading Shimmer Effect for Slider Fields */
    .slider-loading-overlay {
        position: absolute;
        inset: 0;
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(2px);
        z-index: 10;
        display: none;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: 12px;
    }

    .slider-loading-spinner {
        width: 44px;
        height: 44px;
        border: 4px solid #dbeafe;
        border-top-color: var(--theme-royal-blue);
        border-radius: 50%;
        animation: spinLoader 0.7s linear infinite;
    }

    @keyframes spinLoader {
        to { transform: rotate(360deg); }
    }

    /* Pagination */
    .report-pagination-wrap {
        padding: 16px 20px;
        background: #ffffff;
        border-top: 1px solid #edf2f7;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }
</style>

<div class="container-fluid report-page-container px-0">

    <!-- Top Header & Breadcrumbs -->
    <div class="report-top-header">
        <div>
            <h4 class="report-page-title">Visitor Master Records Report</h4>
        </div>

        <!-- Quick Summary Strip -->
        <div class="report-stats-strip">
            <div class="stat-pill primary" title="Total visitor master records">
                <i class="fas fa-users"></i>
                <span>Total Visitors:</span>
                <span class="count">{{ number_format($totalCount ?? 0) }}</span>
            </div>
            <div class="stat-pill" title="Male Visitors">
                <i class="fas fa-mars text-primary"></i>
                <span>Male:</span>
                <span class="count">{{ number_format($maleCount ?? 0) }}</span>
            </div>
            <div class="stat-pill" title="Female Visitors">
                <i class="fas fa-venus text-danger"></i>
                <span>Female:</span>
                <span class="count">{{ number_format($femaleCount ?? 0) }}</span>
            </div>
        </div>
    </div>

    <!-- =========================================================
         TOP FILTER SECTION (EXACT MATCH TO SCREENSHOT)
         ========================================================= -->
    <div class="filter-card-container">
        <form method="GET" action="{{ route('school.front-desk.visitor-report') }}" id="filterVisitorForm">
            <div class="filter-form-grid">
                
                <!-- 1. FROM DATE -->
                <div class="filter-field-group">
                    <label class="filter-label" for="from_date">FROM DATE</label>
                    <div class="filter-input-wrap">
                        <input type="date" 
                               id="from_date" 
                               name="from_date" 
                               value="{{ request('from_date') }}" 
                               placeholder="dd-mm-yyyy">
                    </div>
                </div>

                <!-- 2. TO DATE -->
                <div class="filter-field-group">
                    <label class="filter-label" for="to_date">TO DATE</label>
                    <div class="filter-input-wrap">
                        <input type="date" 
                               id="to_date" 
                               name="to_date" 
                               value="{{ request('to_date') }}" 
                               placeholder="dd-mm-yyyy">
                    </div>
                </div>

                <!-- 3. SEARCH (NAME/MOBILE) -->
                <div class="filter-field-group">
                    <label class="filter-label" for="search">SEARCH (NAME/MOBILE)</label>
                    <div class="filter-input-wrap">
                        <input type="text" 
                               id="search" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="KEYWORD ENTRY...">
                    </div>
                </div>

                <!-- 4. SEARCH BUTTON -->
                <div class="filter-actions-wrap">
                    <button type="submit" class="btn-search-main" id="btnSearch">
                        <i class="fas fa-search"></i>
                        <span>SEARCH</span>
                    </button>

                    @if(request()->hasAny(['from_date', 'to_date', 'search']))
                        <a href="{{ route('school.front-desk.visitor-report') }}" class="btn-reset-filters" title="Reset Filters">
                            <i class="fas fa-rotate-left"></i>
                        </a>
                    @endif
                </div>

            </div>
        </form>
    </div>

    <!-- =========================================================
         DATA TABLE CARD (EXACT COLUMNS FROM SCREENSHOT)
         ========================================================= -->
    <div class="report-table-card">
        
        <!-- Table Toolbar -->
        <div class="table-toolbar-bar">
            <div class="table-results-counter">
                Showing <strong>{{ $visitors->firstItem() ?? 0 }}</strong> - <strong>{{ $visitors->lastItem() ?? 0 }}</strong> of <strong>{{ $visitors->total() ?? 0 }}</strong> Visitor Profiles
            </div>

            <div class="table-quick-actions">
                <button type="button" class="btn-table-tool" onclick="window.print();" title="Print Current View">
                    <i class="fas fa-print"></i>
                    <span>Print</span>
                </button>
                <button type="button" class="btn-table-tool" onclick="exportVisitorTableToCSV('visitor_profiles_report.csv');" title="Export as CSV">
                    <i class="fas fa-file-csv text-success"></i>
                    <span>Export CSV</span>
                </button>
            </div>
        </div>

        <!-- Table View -->
        <div class="report-table-responsive">
            <table class="visitor-profile-table" id="visitorMasterTable">
                <thead>
                    <tr>
                        <th class="col-sno text-center">S.NO</th>
                        <th class="col-name">VISITOR NAME</th>
                        <th class="col-gender">GENDER</th>
                        <th class="col-dob">DOB</th>
                        <th class="col-mobile">MOBILE</th>
                        <th class="col-email">EMAIL</th>
                        <th class="col-state">STATE</th>
                        <th class="col-city">CITY</th>
                        <th class="col-address">ADDRESS</th>
                        <th class="col-pincode">PINCODE</th>
                        <th class="col-action text-center">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($visitors as $index => $visitor)
                        @php
                            $sNo = ($visitors->currentPage() - 1) * $visitors->perPage() + ($index + 1);
                            
                            // Prepare complete JSON payload for instant drawer hydration
                            $visitorJson = [
                                'id'                       => $visitor->id,
                                'pass_number'              => $visitor->pass_number,
                                'full_name'                => $visitor->full_name,
                                'gender'                   => $visitor->gender,
                                'dob'                      => $visitor->dob ? $visitor->dob->format('Y-m-d') : null,
                                'mobile_number'            => $visitor->mobile_number,
                                'alternate_mobile'         => $visitor->alternate_mobile,
                                'email'                    => $visitor->email,
                                'street_address'           => $visitor->street_address,
                                'state'                    => $visitor->state,
                                'city'                     => $visitor->city,
                                'pincode'                  => $visitor->pincode,
                                'visitor_type'             => $visitor->visitor_type,
                                'whom_to_meet_type'        => $visitor->whom_to_meet_type,
                                'host_name'                => $visitor->host_name,
                                'security_gate'            => $visitor->security_gate,
                                'entourage_count'          => $visitor->entourage_count ?? 1,
                                'visit_purpose'            => $visitor->visit_purpose,
                                'detailed_purpose_remarks' => $visitor->detailed_purpose_remarks,
                                'id_proof_type'            => $visitor->id_proof_type,
                                'id_proof_number'          => $visitor->id_proof_number,
                                'vehicle_number'           => $visitor->vehicle_number,
                                'photo_url'                => $visitor->photo_url,
                                'security_notes'           => $visitor->security_notes,
                                'status'                   => $visitor->status,
                                'print_url'                => route('school.front-desk.visitor.print', $visitor->id),
                            ];
                        @endphp
                        <tr id="visitor-row-{{ $visitor->id }}">
                            <!-- S.NO -->
                            <td class="col-sno text-center">{{ $sNo }}</td>

                            <!-- VISITOR NAME -->
                            <td class="col-name" id="v-name-{{ $visitor->id }}">
                                {{ $visitor->full_name }}
                            </td>

                            <!-- GENDER -->
                            <td class="col-gender" id="v-gender-{{ $visitor->id }}">
                                {{ strtoupper($visitor->gender ?: '---') }}
                            </td>

                            <!-- DOB -->
                            <td class="col-dob" id="v-dob-{{ $visitor->id }}">
                                {{ $visitor->dob ? $visitor->dob->format('d-M-Y') : '---' }}
                            </td>

                            <!-- MOBILE -->
                            <td class="col-mobile" id="v-mobile-{{ $visitor->id }}">
                                {{ $visitor->mobile_number }}
                            </td>

                            <!-- EMAIL -->
                            <td class="col-email" id="v-email-{{ $visitor->id }}">
                                {{ $visitor->email ?: '---' }}
                            </td>

                            <!-- STATE -->
                            <td class="col-state" id="v-state-{{ $visitor->id }}">
                                {{ $visitor->state ?: '---' }}
                            </td>

                            <!-- CITY -->
                            <td class="col-city" id="v-city-{{ $visitor->id }}">
                                {{ $visitor->city ?: '---' }}
                            </td>

                            <!-- ADDRESS -->
                            <td class="col-address" id="v-address-{{ $visitor->id }}">
                                {{ $visitor->street_address ?: '---' }}
                            </td>

                            <!-- PINCODE -->
                            <td class="col-pincode" id="v-pincode-{{ $visitor->id }}">
                                {{ $visitor->pincode ?: '---' }}
                            </td>

                            <!-- ACTION -->
                            <td class="col-action text-center">
                                <div class="action-btn-group">
                                    <!-- Edit Button (Opens Side Slider Drawer) -->
                                    <button type="button" 
                                            class="btn-action-edit" 
                                            data-visitor='@json($visitorJson)'
                                            onclick="openEditVisitorDrawer({{ $visitor->id }}, this)"
                                            title="Edit Visitor Profile & Pass in Side Drawer">
                                        <i class="fas fa-edit"></i>
                                        <span>Edit</span>
                                    </button>

                                    <!-- Delete Button -->
                                    <button type="button" 
                                            class="btn-action-delete" 
                                            onclick="deleteVisitorRecord({{ $visitor->id }}, '{{ addslashes($visitor->full_name) }}', '{{ $visitor->pass_number }}')"
                                            title="Delete Visitor Record">
                                        <i class="fas fa-trash-can"></i>
                                        <span>Delete</span>
                                    </button>

                                    <!-- Quick Print Pass Button -->
                                    <a href="{{ route('school.front-desk.visitor.print', $visitor->id) }}" 
                                       target="_blank" 
                                       class="btn-action-pass" 
                                       title="View & Print Pass Card">
                                        <i class="fas fa-id-card"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11">
                                <div style="text-align: center; padding: 50px 20px;">
                                    <div style="width: 60px; height: 60px; border-radius: 50%; background: #eff6ff; color: #0038b8; display: inline-flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 12px; border: 1.5px solid #bfdbfe;">
                                        <i class="fas fa-users-slash"></i>
                                    </div>
                                    <h5 style="font-weight: 800; color: #1e293b; margin-bottom: 6px;">No Visitor Records Found</h5>
                                    <p style="color: #64748b; font-size: 13.5px; margin-bottom: 16px;">
                                        No visitor details matched your search keyword or date criteria.
                                    </p>
                                    @if(request()->hasAny(['from_date', 'to_date', 'search']))
                                        <a href="{{ route('school.front-desk.visitor-report') }}" class="btn-search-main" style="text-decoration: none;">
                                            <i class="fas fa-rotate-left"></i>
                                            <span>Clear Filters</span>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Table Pagination Footer -->
        @if($visitors->hasPages())
            <div class="report-pagination-wrap">
                <div class="text-muted small" style="font-weight: 600;">
                    Showing {{ $visitors->firstItem() }} to {{ $visitors->lastItem() }} of {{ $visitors->total() }} results
                </div>
                <div>
                    {{ $visitors->links() }}
                </div>
            </div>
        @endif

    </div>

</div>

<!-- =========================================================
     DYNAMIC SLIDER / OFFCANVAS DRAWER (EDIT VISITOR PROFILE)
     Responsive on Desktop, Tablet & Mobile with Smooth Animations
     ========================================================= -->
<div class="drawer-backdrop-overlay" id="editVisitorDrawerBackdrop" onclick="closeEditVisitorDrawer()"></div>

<div class="drawer-slider-panel" id="editVisitorDrawer">
    
    <!-- Drawer Loading Shimmer Overlay -->
    <div class="slider-loading-overlay" id="sliderLoadingOverlay">
        <div class="slider-loading-spinner"></div>
        <div style="font-size: 13px; font-weight: 700; color: #0038b8;">Loading Visitor Details...</div>
    </div>

    <!-- Drawer Top Header Bar -->
    <div class="drawer-header-bar">
        <div class="drawer-title-wrap">
            <div class="drawer-icon-badge">
                <i class="fas fa-user-pen"></i>
            </div>
            <div>
                <h5 class="drawer-title-text">Edit Visitor Profile & Pass</h5>
                <div style="font-size: 12px; opacity: 0.95; display: flex; align-items: center; gap: 8px; margin-top: 3px;">
                    <span>Pass:</span>
                    <strong class="drawer-pass-pill" id="sliderDisplayPassNumber">PASS - EDUZEN - 0000</strong>
                </div>
            </div>
        </div>
        <button type="button" class="btn-drawer-close" onclick="closeEditVisitorDrawer()" title="Close Slider (Esc)">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Drawer Form Content -->
    <form id="editVisitorSliderForm" enctype="multipart/form-data" style="display: flex; flex-direction: column; flex: 1; overflow: hidden; margin: 0;">
        @csrf
        <input type="hidden" id="edit_slider_visitor_id" name="visitor_id">

        <div class="drawer-body-content">
            
            <!-- Section 1: Personal & Contact Information -->
            <div class="slider-section-card">
                <div class="slider-section-heading">
                    <i class="fas fa-id-card"></i>
                    <span>1. Personal & Contact Profile</span>
                </div>
                
                <div class="slider-grid-2" style="margin-bottom: 14px;">
                    <div class="form-group-item">
                        <label for="edit_slider_full_name">Full Name <span class="text-danger">*</span></label>
                        <input type="text" id="edit_slider_full_name" name="full_name" required placeholder="e.g. Souhardyadip Mondal">
                    </div>
                    <div class="form-group-item">
                        <label for="edit_slider_gender">Gender</label>
                        <select id="edit_slider_gender" name="gender">
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>

                <div class="slider-grid-2" style="margin-bottom: 14px;">
                    <div class="form-group-item">
                        <label for="edit_slider_dob">Date of Birth</label>
                        <input type="date" id="edit_slider_dob" name="dob">
                    </div>
                    <div class="form-group-item">
                        <label for="edit_slider_mobile_number">Mobile Number <span class="text-danger">*</span></label>
                        <input type="text" id="edit_slider_mobile_number" name="mobile_number" required placeholder="e.g. 9876543210">
                    </div>
                </div>

                <div class="slider-grid-2">
                    <div class="form-group-item">
                        <label for="edit_slider_alternate_mobile">Alternate Mobile</label>
                        <input type="text" id="edit_slider_alternate_mobile" name="alternate_mobile" placeholder="Optional phone">
                    </div>
                    <div class="form-group-item">
                        <label for="edit_slider_email">Email Address</label>
                        <input type="email" id="edit_slider_email" name="email" placeholder="name@domain.com">
                    </div>
                </div>
            </div>

            <!-- Section 2: Address & Location -->
            <div class="slider-section-card">
                <div class="slider-section-heading">
                    <i class="fas fa-map-location-dot"></i>
                    <span>2. Address & Location Details</span>
                </div>
                <div class="slider-grid-2" style="margin-bottom: 14px;">
                    <div class="form-group-item">
                        <label for="edit_slider_street_address">Street Address</label>
                        <input type="text" id="edit_slider_street_address" name="street_address" placeholder="e.g. Dilshad Garden, Block B">
                    </div>
                    <div class="form-group-item">
                        <label for="edit_slider_city">City</label>
                        <input type="text" id="edit_slider_city" name="city" placeholder="e.g. Delhi / Agra">
                    </div>
                </div>
                <div class="slider-grid-2">
                    <div class="form-group-item">
                        <label for="edit_slider_state">State / UT</label>
                        <input type="text" id="edit_slider_state" name="state" placeholder="e.g. Delhi NCR / Uttar Pradesh">
                    </div>
                    <div class="form-group-item">
                        <label for="edit_slider_pincode">Pincode</label>
                        <input type="text" id="edit_slider_pincode" name="pincode" placeholder="e.g. 110095">
                    </div>
                </div>
            </div>

            <!-- Section 3: Visit, Host & Security Gate Assignment -->
            <div class="slider-section-card">
                <div class="slider-section-heading">
                    <i class="fas fa-building-user"></i>
                    <span>3. Visit Assignment & Security Gate</span>
                </div>
                
                <div class="slider-grid-2" style="margin-bottom: 14px;">
                    <div class="form-group-item">
                        <label for="edit_slider_visitor_type">Visitor Category</label>
                        <select id="edit_slider_visitor_type" name="visitor_type">
                            @foreach($visitorTypes as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group-item">
                        <label for="edit_slider_whom_to_meet_type">Whom to Meet Category</label>
                        <select id="edit_slider_whom_to_meet_type" name="whom_to_meet_type">
                            @foreach($whomToMeetTypes as $wtm)
                                <option value="{{ $wtm }}">{{ $wtm }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="slider-grid-2" style="margin-bottom: 14px;">
                    <div class="form-group-item">
                        <label for="edit_slider_host_name">Host / Person Name</label>
                        <input type="text" id="edit_slider_host_name" name="host_name" placeholder="e.g. Principal / Sachin">
                    </div>
                    <div class="form-group-item">
                        <label for="edit_slider_security_gate">Security Gate</label>
                        <select id="edit_slider_security_gate" name="security_gate">
                            @foreach($securityGates as $gate)
                                <option value="{{ $gate }}">{{ $gate }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="slider-grid-2" style="margin-bottom: 14px;">
                    <div class="form-group-item">
                        <label for="edit_slider_entourage_count">Persons (Entourage)</label>
                        <input type="number" id="edit_slider_entourage_count" name="entourage_count" min="1" max="50" value="1">
                    </div>
                    <div class="form-group-item">
                        <label for="edit_slider_status">Visitor Status</label>
                        <select id="edit_slider_status" name="status">
                            <option value="checked_in">Checked In (Inside Campus)</option>
                            <option value="checked_out">Checked Out (Departed)</option>
                            <option value="expected">Expected Visit</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>

                <div class="form-group-item" style="margin-bottom: 14px;">
                    <label for="edit_slider_visit_purpose">Visit Purpose</label>
                    <select id="edit_slider_visit_purpose" name="visit_purpose">
                        @foreach($visitPurposes as $purpose)
                            <option value="{{ $purpose }}">{{ $purpose }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group-item">
                    <label for="edit_slider_detailed_purpose_remarks">Detailed Purpose Remarks</label>
                    <textarea id="edit_slider_detailed_purpose_remarks" name="detailed_purpose_remarks" rows="2" placeholder="Specific appointment details or visiting notes..."></textarea>
                </div>
            </div>

            <!-- Section 4: Verification, Vehicle & Visitor Photo -->
            <div class="slider-section-card">
                <div class="slider-section-heading">
                    <i class="fas fa-shield-halved"></i>
                    <span>4. Verification, Vehicle & Photo</span>
                </div>
                
                <div class="slider-grid-3" style="margin-bottom: 14px;">
                    <div class="form-group-item">
                        <label for="edit_slider_id_proof_type">ID Proof Type</label>
                        <select id="edit_slider_id_proof_type" name="id_proof_type">
                            <option value="">None / Not Provided</option>
                            @foreach($idProofTypes as $idType)
                                <option value="{{ $idType }}">{{ $idType }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group-item">
                        <label for="edit_slider_id_proof_number">ID Proof Number</label>
                        <input type="text" id="edit_slider_id_proof_number" name="id_proof_number" placeholder="e.g. XXXX-XXXX-1234">
                    </div>
                    <div class="form-group-item">
                        <label for="edit_slider_vehicle_number">Vehicle Number</label>
                        <input type="text" id="edit_slider_vehicle_number" name="vehicle_number" placeholder="e.g. DL 01 AB 1234">
                    </div>
                </div>

                <div class="form-group-item" style="margin-bottom: 14px;">
                    <label for="edit_slider_security_notes">Security Desk Notes</label>
                    <input type="text" id="edit_slider_security_notes" name="security_notes" placeholder="e.g. Bag checked, Visitor tag issued">
                </div>

                <div class="form-group-item">
                    <label>Update Visitor Photo</label>
                    <div class="photo-preview-wrap">
                        <img src="" id="edit_slider_photo_preview" class="photo-thumb-circle" alt="Visitor Avatar" onerror="this.src='https://ui-avatars.com/api/?name=Visitor&background=0038b8&color=fff'">
                        <div style="flex: 1;">
                            <input type="file" id="edit_slider_photo" name="photo" accept="image/*" style="padding: 6px 8px; font-size: 12px;" onchange="previewSliderVisitorPhoto(this)">
                            <div style="font-size: 11px; color: #64748b; margin-top: 4px;">
                                Supported formats: JPG, PNG, WebP (Max 5MB). Leaving blank preserves current photo.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Drawer Sticky Footer Bar -->
        <div class="drawer-footer-bar">
            <button type="button" class="btn-drawer-cancel" onclick="closeEditVisitorDrawer()">
                Cancel
            </button>

            <div style="display: flex; align-items: center; gap: 10px;">
                <!-- Direct Print Updated Card button -->
                <a href="#" target="_blank" id="btnSliderPrintCard" class="btn-print-card-direct" style="display: none;">
                    <i class="fas fa-id-card"></i>
                    <span>Print Pass Card</span>
                </a>

                <!-- Save Changes Button -->
                <button type="submit" class="btn-drawer-save" id="btnSaveSliderVisitor">
                    <i class="fas fa-save"></i>
                    <span>Save & Update Pass</span>
                </button>
            </div>
        </div>
    </form>

</div>

<!-- Custom Notification Toast -->
<div class="toast-alert-wrap success" id="notificationToast">
    <i class="fas fa-circle-check text-success" id="toastIcon" style="font-size: 18px;"></i>
    <span id="toastMessage">Changes saved successfully!</span>
</div>

<!-- =========================================================
     JAVASCRIPT: SLIDER DRAWER CONTROLLER, AJAX & CSV
     ========================================================= -->
<script>
// Dynamic Base URL correctly formatted for /school/front-desk/visitor
const visitorApiBaseUrl = "{{ url('school/front-desk/visitor') }}";
let currentEditingVisitorId = null;

// Helper: Smart Dropdown value setter that handles case and adds missing options
function setSelectValue(selectId, value) {
    const sel = document.getElementById(selectId);
    if (!sel || value === null || value === undefined) return;
    
    const valTrim = String(value).trim().toLowerCase();
    let matched = false;
    
    for (let i = 0; i < sel.options.length; i++) {
        const optVal = sel.options[i].value.trim().toLowerCase();
        const optTxt = sel.options[i].text.trim().toLowerCase();
        if (optVal === valTrim || optTxt === valTrim) {
            sel.selectedIndex = i;
            matched = true;
            break;
        }
    }
    
    // If not in existing option list, dynamically append and select
    if (!matched && String(value).trim() !== '') {
        const opt = document.createElement('option');
        opt.value = value;
        opt.text = value;
        opt.selected = true;
        sel.appendChild(opt);
    }
}

// Populate all slider drawer form fields from visitor data object
function populateDrawerFields(v) {
    if (!v) return;

    document.getElementById('edit_slider_visitor_id').value = v.id || '';
    document.getElementById('sliderDisplayPassNumber').innerText = v.pass_number || 'PASS - EDUZEN';
    document.getElementById('edit_slider_full_name').value = v.full_name || '';
    setSelectValue('edit_slider_gender', v.gender || '');
    document.getElementById('edit_slider_dob').value = v.dob || '';
    document.getElementById('edit_slider_mobile_number').value = v.mobile_number || '';
    document.getElementById('edit_slider_alternate_mobile').value = v.alternate_mobile || '';
    document.getElementById('edit_slider_email').value = v.email || '';
    document.getElementById('edit_slider_street_address').value = v.street_address || '';
    document.getElementById('edit_slider_city').value = v.city || '';
    document.getElementById('edit_slider_state').value = v.state || '';
    document.getElementById('edit_slider_pincode').value = v.pincode || '';
    
    setSelectValue('edit_slider_visitor_type', v.visitor_type || '');
    setSelectValue('edit_slider_whom_to_meet_type', v.whom_to_meet_type || '');
    document.getElementById('edit_slider_host_name').value = v.host_name || '';
    setSelectValue('edit_slider_security_gate', v.security_gate || '');
    document.getElementById('edit_slider_entourage_count').value = v.entourage_count || 1;
    setSelectValue('edit_slider_status', v.status || 'checked_in');
    setSelectValue('edit_slider_visit_purpose', v.visit_purpose || '');
    document.getElementById('edit_slider_detailed_purpose_remarks').value = v.detailed_purpose_remarks || '';
    
    setSelectValue('edit_slider_id_proof_type', v.id_proof_type || '');
    document.getElementById('edit_slider_id_proof_number').value = v.id_proof_number || '';
    document.getElementById('edit_slider_vehicle_number').value = v.vehicle_number || '';
    document.getElementById('edit_slider_security_notes').value = v.security_notes || '';

    // Update photo preview
    const previewImg = document.getElementById('edit_slider_photo_preview');
    if (v.photo_url) {
        previewImg.src = v.photo_url;
    } else {
        const fallbackName = encodeURIComponent(v.full_name || 'Visitor');
        previewImg.src = `https://ui-avatars.com/api/?name=${fallbackName}&background=0038b8&color=fff&size=128&bold=true`;
    }

    // Set up pass print direct button
    const printBtn = document.getElementById('btnSliderPrintCard');
    if (v.print_url) {
        printBtn.href = v.print_url;
        printBtn.style.display = 'inline-flex';
    } else {
        printBtn.href = `${visitorApiBaseUrl}/${v.id}/print`;
        printBtn.style.display = 'inline-flex';
    }
}

// Open Side Slider Drawer
function openEditVisitorDrawer(visitorId, triggerEl = null) {
    currentEditingVisitorId = visitorId;
    const backdrop = document.getElementById('editVisitorDrawerBackdrop');
    const panel = document.getElementById('editVisitorDrawer');
    const loadingOverlay = document.getElementById('sliderLoadingOverlay');
    
    // Reset file input
    const fileInput = document.getElementById('edit_slider_photo');
    if (fileInput) fileInput.value = '';

    // Open Slider with Smooth Animation
    backdrop.classList.add('active');
    panel.classList.add('active');
    document.body.style.overflow = 'hidden';

    // 1. Instant hydration if element has data-visitor
    if (triggerEl && triggerEl.dataset && triggerEl.dataset.visitor) {
        try {
            const cachedVisitor = JSON.parse(triggerEl.dataset.visitor);
            populateDrawerFields(cachedVisitor);
        } catch (e) {
            console.warn('Could not parse cached visitor data:', e);
        }
    }

    // 2. Fetch fresh details via AJAX using proper /school/front-desk/visitor/{id} route
    loadingOverlay.style.display = 'flex';

    fetch(`${visitorApiBaseUrl}/${visitorId}`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) throw new Error('HTTP error ' + response.status);
        return response.json();
    })
    .then(data => {
        loadingOverlay.style.display = 'none';
        if (data.success && data.visitor) {
            populateDrawerFields(data.visitor);
        }
    })
    .catch(err => {
        loadingOverlay.style.display = 'none';
        console.error('Error fetching fresh visitor details:', err);
    });
}

// Close Side Slider Drawer
function closeEditVisitorDrawer() {
    const backdrop = document.getElementById('editVisitorDrawerBackdrop');
    const panel = document.getElementById('editVisitorDrawer');
    
    backdrop.classList.remove('active');
    panel.classList.remove('active');
    document.body.style.overflow = '';
}

// Preview uploaded photo thumbnail in slider
function previewSliderVisitorPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('edit_slider_photo_preview').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Submit Edit Form via AJAX
document.getElementById('editVisitorSliderForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const visitorId = document.getElementById('edit_slider_visitor_id').value;
    const saveBtn = document.getElementById('btnSaveSliderVisitor');
    const originalBtnText = saveBtn.innerHTML;

    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Saving...</span>';

    const formData = new FormData(this);

    fetch(`${visitorApiBaseUrl}/${visitorId}/update`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) throw new Error('HTTP error ' + response.status);
        return response.json();
    })
    .then(data => {
        saveBtn.disabled = false;
        saveBtn.innerHTML = originalBtnText;

        if (data.success) {
            showToast(data.message || 'Visitor details & pass updated successfully!', 'success');
            
            // Update table DOM row values instantly
            const v = data.visitor;
            if (v) {
                if (document.getElementById(`v-name-${v.id}`)) {
                    document.getElementById(`v-name-${v.id}`).innerText = v.full_name;
                }
                if (document.getElementById(`v-gender-${v.id}`)) {
                    document.getElementById(`v-gender-${v.id}`).innerText = (v.gender || '---').toUpperCase();
                }
                if (document.getElementById(`v-dob-${v.id}`)) {
                    document.getElementById(`v-dob-${v.id}`).innerText = v.dob ? new Date(v.dob).toLocaleDateString('en-GB', {day: '2-digit', month: 'short', year: 'numeric'}).replace(/ /g, '-') : '---';
                }
                if (document.getElementById(`v-mobile-${v.id}`)) {
                    document.getElementById(`v-mobile-${v.id}`).innerText = v.mobile_number;
                }
                if (document.getElementById(`v-email-${v.id}`)) {
                    document.getElementById(`v-email-${v.id}`).innerText = v.email || '---';
                }
                if (document.getElementById(`v-state-${v.id}`)) {
                    document.getElementById(`v-state-${v.id}`).innerText = v.state || '---';
                }
                if (document.getElementById(`v-city-${v.id}`)) {
                    document.getElementById(`v-city-${v.id}`).innerText = v.city || '---';
                }
                if (document.getElementById(`v-address-${v.id}`)) {
                    document.getElementById(`v-address-${v.id}`).innerText = v.street_address || '---';
                }
                if (document.getElementById(`v-pincode-${v.id}`)) {
                    document.getElementById(`v-pincode-${v.id}`).innerText = v.pincode || '---';
                }

                // Update cached data-visitor attribute on row's edit button
                const rowEditBtn = document.querySelector(`#visitor-row-${v.id} .btn-action-edit`);
                if (rowEditBtn) {
                    rowEditBtn.dataset.visitor = JSON.stringify(v);
                }
            }

            // Update print button in slider
            const printBtn = document.getElementById('btnSliderPrintCard');
            if (data.print_url) {
                printBtn.href = data.print_url;
                printBtn.style.display = 'inline-flex';
            }

            // Auto close drawer after short feedback pause
            setTimeout(() => {
                closeEditVisitorDrawer();
            }, 600);
        } else {
            showToast(data.message || 'Error updating visitor profile.', 'error');
        }
    })
    .catch(err => {
        saveBtn.disabled = false;
        saveBtn.innerHTML = originalBtnText;
        console.error('Error updating visitor:', err);
        showToast('Network error while saving details.', 'error');
    });
});

// Delete Visitor Record
function deleteVisitorRecord(visitorId, visitorName, passNumber) {
    if (!confirm(`Are you sure you want to delete visitor record for "${visitorName}" (${passNumber})?`)) {
        return;
    }

    fetch(`${visitorApiBaseUrl}/${visitorId}/delete`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) throw new Error('HTTP error ' + response.status);
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showToast(data.message || 'Visitor record deleted.', 'success');
            const row = document.getElementById(`visitor-row-${visitorId}`);
            if (row) {
                row.style.transition = 'all 0.3s ease';
                row.style.opacity = '0';
                row.style.transform = 'scale(0.95)';
                setTimeout(() => row.remove(), 300);
            }
        } else {
            showToast(data.message || 'Error deleting visitor record.', 'error');
        }
    })
    .catch(err => {
        console.error('Error deleting visitor:', err);
        showToast('Could not delete record.', 'error');
    });
}

// Show Toast Notification
function showToast(message, type = 'success') {
    const toast = document.getElementById('notificationToast');
    const toastMsg = document.getElementById('toastMessage');
    const toastIcon = document.getElementById('toastIcon');

    toastMsg.innerText = message;
    toast.className = `toast-alert-wrap ${type}`;

    if (type === 'success') {
        toastIcon.className = 'fas fa-circle-check text-success';
    } else {
        toastIcon.className = 'fas fa-circle-exclamation text-danger';
    }

    toast.style.display = 'flex';
    setTimeout(() => {
        toast.style.display = 'none';
    }, 4000);
}

// Export CSV Functionality
function exportVisitorTableToCSV(filename) {
    var csv = [];
    var rows = document.querySelectorAll("#visitorMasterTable tr");
    
    for (var i = 0; i < rows.length; i++) {
        var row = [], cols = rows[i].querySelectorAll("td, th");
        if (cols.length === 1 && cols[0].getAttribute("colspan")) continue;

        var colLimit = cols.length === 11 ? 10 : cols.length;
        for (var j = 0; j < colLimit; j++) {
            var data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, " ").trim();
            data = data.replace(/"/g, '""');
            row.push('"' + data + '"');
        }
        csv.push(row.join(","));
    }

    var csvFile = new Blob([csv.join("\n")], {type: "text/csv"});
    var downloadLink = document.createElement("a");
    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = "none";
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}

// Close drawer on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeEditVisitorDrawer();
    }
});
</script>
@endsection
