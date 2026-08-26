@extends('layouts.app')

@section('title', 'Library Transactions - Library Management')
@section('page-title', 'Library Transactions')

@section('content')
<style>
    /* =========================================================================
       LIBRARY TRANSACTIONS - ULTRA-PREMIUM ROYAL COBALT BLUE ERP THEME
       ========================================================================= */
    :root {
        --cat-blue-primary: #0038b8;
        --cat-blue-deep: #002266;
        --cat-blue-light: #f0f5ff;
        --cat-blue-hover: #002b8f;
        --cat-blue-border: #bfdbfe;
        --cat-blue-gradient: linear-gradient(135deg, #0038b8 0%, #1d4ed8 100%);
        --cat-header-bg: #002266;
        --cat-gold: #f59e0b;
        --cat-success: #16a34a;
        --cat-danger: #dc2626;
        --cat-slate-50: #f8fafc;
        --cat-slate-100: #f1f5f9;
        --cat-slate-200: #e2e8f0;
        --cat-slate-300: #cbd5e1;
        --cat-slate-700: #334155;
        --cat-slate-800: #1e293b;
        --cat-slate-900: #0f172a;
    }

    .transactions-wrapper {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        color: var(--cat-slate-800);
    }

    /* =========================================================================
       1. SLEEK HORIZONTAL FILTER BAR
       ========================================================================= */
    .filter-box-horizontal {
        background: #ffffff;
        border: 1px solid var(--cat-slate-200);
        border-radius: 12px;
        padding: 14px 18px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0, 34, 102, 0.04);
    }

    .filter-grid-layout {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)) auto;
        gap: 12px;
        align-items: flex-end;
        width: 100%;
    }

    @media (min-width: 992px) {
        .filter-grid-layout.students-grid {
            grid-template-columns: 1.1fr 1fr 1fr 1fr 1fr 1.1fr auto;
        }
        .filter-grid-layout.staffs-grid {
            grid-template-columns: 1.2fr 1fr 1fr 1.2fr auto;
        }
    }

    .param-group {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .param-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: #475569;
        margin-bottom: 5px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .param-control {
        border-radius: 8px;
        border: 1.5px solid var(--cat-slate-300);
        padding: 7px 11px;
        font-size: 12.5px;
        font-weight: 500;
        color: var(--cat-slate-900);
        background-color: #ffffff;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        height: 38px;
        width: 100%;
    }

    .param-control:focus {
        border-color: var(--cat-blue-primary);
        box-shadow: 0 0 0 3px rgba(0, 56, 184, 0.12);
        outline: none;
    }

    .filter-actions-group {
        display: flex;
        align-items: center;
        gap: 8px;
        height: 38px;
    }

    .btn-filter-apply {
        height: 38px;
        padding: 0 16px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        background: var(--cat-blue-gradient);
        color: #ffffff;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 2px 6px rgba(0, 56, 184, 0.25);
        white-space: nowrap;
    }

    .btn-filter-apply:hover {
        background: linear-gradient(135deg, #002b8f 0%, #1e40af 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(0, 56, 184, 0.32);
        color: #ffffff;
    }

    .btn-filter-clear {
        height: 38px;
        padding: 0 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        background: #ffffff;
        color: #475569;
        border: 1.5px solid var(--cat-slate-300);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .btn-filter-clear:hover {
        border-color: var(--cat-blue-primary);
        color: var(--cat-blue-primary);
        background: var(--cat-slate-50);
    }

    /* =========================================================================
       2. MAIN TRANSACTION DECK & TOOLBAR
       ========================================================================= */
    .cat-card-deck {
        background: #ffffff;
        border: 1px solid var(--cat-slate-200);
        border-radius: 14px;
        box-shadow: 0 4px 20px rgba(0, 34, 102, 0.05);
        margin-bottom: 24px;
        overflow: hidden;
    }

    .deck-action-bar {
        padding: 14px 18px;
        border-bottom: 1px solid var(--cat-slate-200);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
        background: #ffffff;
    }

    /* Tabs (Students vs Staffs) */
    .cat-tabs-wrap {
        display: inline-flex;
        background: var(--cat-slate-100);
        padding: 3.5px;
        border-radius: 9px;
        gap: 4px;
    }

    .cat-tab-btn {
        padding: 7px 18px;
        font-size: 12px;
        font-weight: 700;
        border-radius: 7px;
        text-decoration: none;
        color: #475569;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        border: none;
    }

    .cat-tab-btn:hover {
        color: var(--cat-blue-primary);
    }

    .cat-tab-btn.active {
        background: var(--cat-blue-gradient);
        color: #ffffff;
        box-shadow: 0 2px 8px rgba(0, 56, 184, 0.28);
    }

    .cat-tab-btn .tab-badge {
        font-size: 10.5px;
        padding: 2px 6.5px;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.25);
        color: #ffffff;
        font-weight: 800;
    }

    .cat-tab-btn:not(.active) .tab-badge {
        background: #cbd5e1;
        color: #334155;
    }

    /* Search Box */
    .deck-search-box {
        flex: 1 1 280px;
        max-width: 450px;
        position: relative;
    }

    .deck-search-input {
        width: 100%;
        height: 38px;
        border-radius: 8px;
        border: 1.5px solid var(--cat-slate-300);
        padding: 6px 14px 6px 36px;
        font-size: 12.5px;
        color: var(--cat-slate-900);
        background: #ffffff;
        transition: all 0.2s ease;
    }

    .deck-search-input:focus {
        border-color: var(--cat-blue-primary);
        box-shadow: 0 0 0 3px rgba(0, 56, 184, 0.12);
        outline: none;
    }

    .deck-search-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #64748b;
        font-size: 13px;
        pointer-events: none;
    }

    /* Action Buttons */
    .cat-btn-action {
        height: 38px;
        padding: 0 16px;
        border-radius: 8px;
        font-size: 12.5px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        text-decoration: none;
        white-space: nowrap;
        cursor: pointer;
        border: none;
    }

    .cat-btn-primary {
        background: var(--cat-blue-gradient);
        color: #ffffff;
        box-shadow: 0 2px 8px rgba(0, 56, 184, 0.25);
    }

    .cat-btn-primary:hover {
        background: linear-gradient(135deg, #002b8f 0%, #1e40af 100%);
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 56, 184, 0.32);
    }

    .cat-btn-return {
        background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
        color: #ffffff;
        box-shadow: 0 2px 8px rgba(2, 132, 199, 0.22);
    }

    .cat-btn-return:hover {
        background: linear-gradient(135deg, #0369a1 0%, #075985 100%);
        color: #ffffff;
        transform: translateY(-1px);
    }

    .cat-btn-outline {
        background: #ffffff;
        color: var(--cat-blue-deep);
        border: 1.5px solid var(--cat-slate-300);
    }

    .cat-btn-outline:hover {
        border-color: var(--cat-blue-primary);
        color: var(--cat-blue-primary);
        background: var(--cat-slate-50);
    }

    /* Custom Table Toolbar */
    .cat-table-toolbar {
        background: var(--cat-header-bg);
        color: #ffffff;
        padding: 9px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }

    .toolbar-left-tools {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .toolbar-tool-item {
        color: rgba(255, 255, 255, 0.9);
        font-size: 11.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.7px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        user-select: none;
        transition: color 0.15s ease;
        position: relative;
    }

    .toolbar-tool-item:hover {
        color: #ffffff;
    }

    .toolbar-popover {
        position: absolute;
        top: calc(100% + 8px);
        left: 0;
        background: #ffffff;
        border: 1px solid var(--cat-slate-300);
        border-radius: 10px;
        box-shadow: 0 10px 25px rgba(0, 34, 102, 0.18);
        padding: 12px;
        min-width: 200px;
        z-index: 1050;
        display: none;
    }

    .toolbar-popover.show {
        display: block;
        animation: catPopFade 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }

    @keyframes catPopFade {
        from { opacity: 0; transform: translateY(-5px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Table Design */
    .cat-table {
        margin-bottom: 0;
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .cat-table thead th {
        background: var(--cat-header-bg);
        color: #ffffff;
        font-size: 11.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 12px 14px;
        border: none;
        white-space: nowrap;
        vertical-align: middle;
    }

    .cat-table tbody td {
        padding: 13px 14px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 12.5px;
        color: #334155;
        background: #ffffff;
        transition: background 0.15s ease;
    }

    .cat-table tbody tr:hover td {
        background: #f8fafc;
    }

    /* Density Switcher */
    .cat-table.density-compact tbody td, .cat-table.density-compact thead th {
        padding: 7px 10px !important;
        font-size: 11.5px !important;
    }

    .cat-table.density-spacious tbody td, .cat-table.density-spacious thead th {
        padding: 17px 16px !important;
        font-size: 13px !important;
    }

    /* Status Badges */
    .status-badge {
        font-size: 10.5px;
        font-weight: 700;
        padding: 4px 8px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .status-issued {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }

    .status-returned {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }

    .status-overdue {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
        animation: pulseOverdue 2s infinite;
    }

    @keyframes pulseOverdue {
        0% { box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.3); }
        70% { box-shadow: 0 0 0 5px rgba(220, 38, 38, 0); }
        100% { box-shadow: 0 0 0 0 rgba(220, 38, 38, 0); }
    }

    .fine-pill {
        font-size: 11.5px;
        font-weight: 800;
        padding: 3px 7.5px;
        border-radius: 5px;
        display: inline-block;
    }

    .fine-pill-zero {
        background: #f1f5f9;
        color: #64748b;
    }

    .fine-pill-pending {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }

    .fine-pill-paid {
        background: #ecfdf5;
        color: #16a34a;
        border: 1px solid #a7f3d0;
    }

    .cat-action-btn {
        width: 30px;
        height: 30px;
        border-radius: 7px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        border: 1px solid var(--cat-slate-200);
        background: #ffffff;
        color: #475569;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .cat-action-btn:hover {
        background: var(--cat-blue-light);
        border-color: var(--cat-blue-border);
        color: var(--cat-blue-primary);
        transform: translateY(-1px);
    }

    /* Empty State Box */
    .empty-state-box {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 46px 20px;
        background: #ffffff;
        text-align: center;
    }

    .empty-state-icon {
        width: 68px;
        height: 68px;
        border-radius: 50%;
        background: var(--cat-blue-light);
        color: var(--cat-blue-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        margin-bottom: 14px;
        box-shadow: 0 4px 14px rgba(0, 56, 184, 0.12);
    }

    /* =========================================================================
       3. SLIDE-OVER DRAWERS & SEARCH AUTO-LOOKUP (Multi-Book Support)
       ========================================================================= */
    .cat-drawer-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 17, 51, 0.45);
        backdrop-filter: blur(4px);
        z-index: 99990;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .cat-drawer-overlay.open {
        opacity: 1;
        visibility: visible;
    }

    .cat-drawer {
        position: fixed;
        top: 0;
        right: 0;
        bottom: 0;
        width: 100%;
        max-width: 650px;
        height: 100vh;
        max-height: 100vh;
        background: #ffffff;
        z-index: 99999;
        box-shadow: -14px 0 50px rgba(0, 34, 102, 0.32);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        transform: translateX(100%);
        transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .cat-drawer.open {
        transform: translateX(0);
    }

    .cat-drawer-header {
        height: 60px;
        min-height: 60px;
        max-height: 60px;
        background: var(--cat-blue-gradient);
        color: #ffffff;
        padding: 0 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 4px 16px rgba(0, 56, 184, 0.25);
        flex-shrink: 0;
        z-index: 20;
    }

    .cat-drawer-header h5 {
        margin: 0;
        font-size: 16px;
        font-weight: 800;
        letter-spacing: -0.2px;
        display: flex;
        align-items: center;
        gap: 10px;
        color: #ffffff;
    }

    .cat-drawer-close {
        background: rgba(255, 255, 255, 0.15);
        border: none;
        color: #ffffff;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .cat-drawer-close:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: rotate(90deg);
    }

    .cat-drawer-form {
        display: flex;
        flex-direction: column;
        flex: 1 1 auto;
        min-height: 0;
        height: calc(100vh - 60px);
        max-height: calc(100vh - 60px);
        margin: 0;
        overflow: hidden;
    }

    .cat-drawer-body {
        padding: 20px;
        overflow-y: auto !important;
        overflow-x: hidden;
        flex: 1 1 auto;
        min-height: 0;
        background: #fbfcfe;
        -webkit-overflow-scrolling: touch;
    }

    .cat-drawer-footer {
        height: 68px;
        min-height: 68px;
        max-height: 68px;
        padding: 0 20px;
        background: #ffffff;
        border-top: 1.5px solid var(--cat-slate-200);
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        flex-shrink: 0;
        position: relative;
        z-index: 20;
        box-shadow: 0 -4px 16px rgba(0, 34, 102, 0.06);
    }

    .drawer-card-box {
        background: #ffffff;
        border: 1px solid var(--cat-slate-200);
        border-radius: 12px;
        padding: 16px 18px;
        margin-bottom: 16px;
        box-shadow: 0 2px 8px rgba(0, 56, 184, 0.03);
        position: relative;
    }

    .drawer-card-box-title {
        font-size: 12.5px;
        font-weight: 800;
        color: var(--cat-blue-deep);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .cat-field-label {
        font-size: 11.5px;
        font-weight: 700;
        color: #334155;
        margin-bottom: 6px;
        display: block;
    }

    /* Embedded Input Groups */
    .cat-input-group {
        display: flex;
        align-items: stretch;
        width: 100%;
        position: relative;
    }

    .cat-input-addon {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 12px;
        background: #f8fafc;
        border: 1.5px solid var(--cat-slate-300);
        border-right: none;
        border-radius: 8px 0 0 8px;
        color: var(--cat-blue-primary);
        font-size: 13.5px;
        flex-shrink: 0;
    }

    .cat-input-field {
        flex: 1 1 auto;
        border: 1.5px solid var(--cat-slate-300);
        border-radius: 0 8px 8px 0 !important;
        padding: 8px 12px;
        font-size: 13px;
        font-weight: 500;
        color: var(--cat-slate-900);
        background: #ffffff;
        transition: all 0.2s ease;
        width: 100%;
        outline: none;
    }

    .cat-input-field:focus {
        border-color: var(--cat-blue-primary);
        box-shadow: 0 0 0 3px rgba(0, 56, 184, 0.12);
    }

    .cat-form-control, .cat-form-select {
        border-radius: 8px;
        border: 1.5px solid var(--cat-slate-300);
        padding: 8px 12px;
        font-size: 13px;
        color: var(--cat-slate-900);
        background-color: #ffffff;
        transition: all 0.2s ease;
        width: 100%;
    }

    .cat-form-control:focus, .cat-form-select:focus {
        border-color: var(--cat-blue-primary);
        box-shadow: 0 0 0 3px rgba(0, 56, 184, 0.12);
        outline: none;
    }

    /* Borrower Pill Toggle */
    .borrower-type-toggle {
        display: flex;
        gap: 10px;
        margin-bottom: 14px;
    }

    .type-pill-btn {
        flex: 1;
        padding: 9px 14px;
        border-radius: 8px;
        border: 1.5px solid var(--cat-slate-300);
        background: #ffffff;
        font-size: 12.5px;
        font-weight: 700;
        color: #475569;
        cursor: pointer;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        transition: all 0.2s ease;
        user-select: none;
    }

    .type-pill-btn.active {
        background: var(--cat-blue-light);
        border-color: var(--cat-blue-primary);
        color: var(--cat-blue-primary);
        box-shadow: 0 2px 8px rgba(0, 56, 184, 0.14);
    }

    /* Custom Auto-complete Search Dropdown */
    .live-search-dropdown {
        position: absolute;
        top: calc(100% + 4px);
        left: 0;
        right: 0;
        background: #ffffff;
        border: 1.5px solid #bfdbfe;
        border-radius: 9px;
        box-shadow: 0 10px 30px rgba(0, 34, 102, 0.22);
        z-index: 999999;
        max-height: 230px;
        overflow-y: auto;
        display: none;
    }

    .search-result-item {
        padding: 10px 14px;
        border-bottom: 1px solid #f1f5f9;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: background 0.15s ease;
    }

    .search-result-item:last-child {
        border-bottom: none;
    }

    .search-result-item:hover, .search-result-item.highlighted {
        background: #f0f5ff;
    }

    /* Selected Entity Details Card */
    .selected-entity-card {
        background: #f8fafc;
        border: 1.5px solid #93c5fd;
        border-radius: 10px;
        padding: 12px 16px;
        margin-top: 12px;
        animation: catCardPop 0.2s ease;
    }

    @keyframes catCardPop {
        from { opacity: 0; transform: translateY(-4px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .entity-avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: var(--cat-blue-primary);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        flex-shrink: 0;
        font-weight: 700;
    }

    .entity-avatar.book-avatar {
        background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
    }

    .entity-title {
        font-size: 13.5px;
        font-weight: 800;
        color: var(--cat-slate-900);
        line-height: 1.3;
    }

    .entity-subtitle {
        font-size: 11.5px;
        color: #64748b;
        font-weight: 500;
    }

    /* Multi-Book Selected List */
    .multi-books-container {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-top: 12px;
    }

    .multi-book-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 9px 12px;
        background: #f0f7ff;
        border: 1.5px solid #bfdbfe;
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .multi-book-remove {
        background: #fee2e2;
        border: 1px solid #fca5a5;
        color: #b91c1c;
        width: 26px;
        height: 26px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .multi-book-remove:hover {
        background: #dc2626;
        color: #ffffff;
    }

    /* Search Radios */
    .search-mode-radios {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 12px;
    }

    .mode-radio-label {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
        font-weight: 600;
        color: #334155;
        cursor: pointer;
        user-select: none;
    }

    .mode-radio-label input[type="radio"] {
        accent-color: var(--cat-blue-primary);
        width: 15px;
        height: 15px;
    }

    /* Ultra-Visible Alert Toast */
    .cat-toast {
        position: fixed;
        top: 24px;
        right: 24px;
        bottom: auto;
        left: auto;
        background: #0f172a;
        color: #ffffff;
        padding: 16px 20px;
        border-radius: 12px;
        box-shadow: 0 16px 50px rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: flex-start;
        gap: 14px;
        z-index: 10000000 !important;
        font-weight: 600;
        font-size: 13px;
        max-width: 600px;
        width: calc(100vw - 48px);
        word-break: break-word;
        overflow-wrap: anywhere;
        transform: translateY(-60px);
        opacity: 0;
        pointer-events: none;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        border: 1.5px solid rgba(255, 255, 255, 0.18);
    }

    .cat-toast.show {
        transform: translateY(0);
        opacity: 1;
        pointer-events: auto;
    }

</style>

<div class="transactions-wrapper">
    <!-- =========================================================================
         1. HORIZONTAL PARAMETERS FILTER BAR (Single Line Grid)
         ========================================================================= -->
    <div class="filter-box-horizontal">
        <form method="GET" action="{{ route('school.library.transactions') }}" id="transFilterForm">
            <input type="hidden" name="tab" value="{{ $tab }}">

            <div class="filter-grid-layout {{ $tab === 'students' ? 'students-grid' : 'staffs-grid' }}">
                <!-- Academic Year -->
                <div class="param-group">
                    <label class="param-label">Academic Year <span class="text-danger">*</span></label>
                    <select name="academic_year" class="param-control">
                        @foreach($academicSessions as $ses)
                        <option value="{{ $ses->id }}">{{ $ses->name ?? $ses->session_year ?? 'Current Session' }}</option>
                        @endforeach
                        @if($academicSessions->isEmpty())
                        <option value="">Apr 2026 - Mar 2027</option>
                        @endif
                    </select>
                </div>

                <!-- Select Class (For Students Tab) -->
                @if($tab === 'students')
                <div class="param-group">
                    <label class="param-label">Select Class</label>
                    <select name="class_id" class="param-control">
                        <option value="">All Classes</option>
                        @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ $classFilter == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Select Section (For Students Tab) -->
                <div class="param-group">
                    <label class="param-label">Select Section</label>
                    <select name="section_id" class="param-control">
                        <option value="">All Sections</option>
                        @foreach($sections as $sec)
                        <option value="{{ $sec->id }}" {{ $sectionFilter == $sec->id ? 'selected' : '' }}>{{ $sec->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <!-- From Date -->
                <div class="param-group">
                    <label class="param-label">From Date</label>
                    <input type="date" name="from_date" class="param-control" value="{{ $fromDate }}">
                </div>

                <!-- To Date -->
                <div class="param-group">
                    <label class="param-label">To Date</label>
                    <input type="date" name="to_date" class="param-control" value="{{ $toDate }}">
                </div>

                <!-- Transaction Type Status -->
                <div class="param-group">
                    <label class="param-label">Transaction Type</label>
                    <select name="status" class="param-control">
                        <option value="all" {{ $statusFilter == 'all' ? 'selected' : '' }}>All Statuses</option>
                        <option value="issued" {{ $statusFilter == 'issued' ? 'selected' : '' }}>Active / Issued</option>
                        <option value="returned" {{ $statusFilter == 'returned' ? 'selected' : '' }}>Returned</option>
                        <option value="overdue" {{ $statusFilter == 'overdue' ? 'selected' : '' }}>Overdue / Late</option>
                    </select>
                </div>

                <!-- Apply & Clear Buttons -->
                <div class="param-group">
                    <div class="filter-actions-group">
                        <button type="submit" class="btn-filter-apply">
                            <i class="fas fa-filter"></i> APPLY
                        </button>
                        <a href="{{ route('school.library.transactions', ['tab' => $tab]) }}" class="btn-filter-clear" title="Clear Filters">
                            CLEAR
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- =========================================================================
         2. MAIN TRANSACTION CARD DECK & DATA TABLE
         ========================================================================= -->
    <div class="cat-card-deck">
        <!-- Top Action Deck (Tabs, Live Search & Actions) -->
        <div class="deck-action-bar">
            <!-- Tabs: STUDENTS vs STAFFS -->
            <div class="cat-tabs-wrap">
                <a href="{{ route('school.library.transactions', array_merge(request()->query(), ['tab' => 'students'])) }}" class="cat-tab-btn {{ $tab === 'students' ? 'active' : '' }}">
                    <i class="fas fa-user-graduate"></i>
                    <span>STUDENTS</span>
                    <span class="tab-badge">{{ $studentTxnCount }}</span>
                </a>
                <a href="{{ route('school.library.transactions', array_merge(request()->query(), ['tab' => 'staffs'])) }}" class="cat-tab-btn {{ $tab === 'staffs' ? 'active' : '' }}">
                    <i class="fas fa-chalkboard-user"></i>
                    <span>STAFFS</span>
                    <span class="tab-badge">{{ $staffTxnCount }}</span>
                </a>
            </div>

            <!-- Global Live Search Bar -->
            <div class="deck-search-box">
                <i class="fas fa-magnifying-glass deck-search-icon"></i>
                <input type="text" id="transSearchInput" class="deck-search-input" placeholder="Search by Title / Borrower / ID / ISBN / ACN..." value="{{ $search }}" oninput="handleClientSearch(this.value)">
            </div>

            <!-- Action Buttons Deck -->
            <div class="d-flex align-items-center gap-2">
                <!-- Export CSV -->
                <a href="{{ route('school.library.transactions.export-csv', ['tab' => $tab]) }}" class="btn cat-btn-action cat-btn-outline" title="Download Transaction Records">
                    <i class="fas fa-download text-primary"></i> DOWNLOAD
                </a>

                <!-- Issue Book Trigger -->
                <button type="button" class="btn cat-btn-action cat-btn-primary" onclick="openIssueDrawer()">
                    <i class="fas fa-plus"></i> ISSUE BOOK
                </button>

                <!-- Return Book Trigger -->
                <button type="button" class="btn cat-btn-action cat-btn-return" onclick="openReturnDrawer()">
                    <i class="fas fa-arrow-rotate-left"></i> RETURN BOOK
                </button>
            </div>
        </div>

        <!-- Custom Data Table Toolbar (Columns, Filters, Density) -->
        <div class="cat-table-toolbar">
            <div class="toolbar-left-tools">
                <!-- Columns Toggle -->
                <div class="toolbar-tool-item" onclick="toggleColumnsMenu(event)">
                    <i class="fas fa-bars-staggered"></i> COLUMNS
                    <div class="toolbar-popover" id="columnsPopoverMenu">
                        <div class="fw-bold text-dark mb-2 pb-1 border-bottom" style="font-size: 11.5px;">Toggle Columns</div>
                        <div class="d-flex flex-column gap-2 text-dark" style="font-size: 12px;">
                            <label class="d-flex align-items-center gap-2"><input type="checkbox" class="col-toggle" data-col="col-borrower" checked> Borrower</label>
                            <label class="d-flex align-items-center gap-2"><input type="checkbox" class="col-toggle" data-col="col-id" checked> {{ $tab === 'staffs' ? 'Employee ID' : 'Admission ID' }}</label>
                            <label class="d-flex align-items-center gap-2"><input type="checkbox" class="col-toggle" data-col="col-class" checked> {{ $tab === 'staffs' ? 'Department' : 'Class' }}</label>
                            <label class="d-flex align-items-center gap-2"><input type="checkbox" class="col-toggle" data-col="col-bookid" checked> Book ID</label>
                            <label class="d-flex align-items-center gap-2"><input type="checkbox" class="col-toggle" data-col="col-accno" checked> Accession No.</label>
                            <label class="d-flex align-items-center gap-2"><input type="checkbox" class="col-toggle" data-col="col-title" checked> Book Title</label>
                            <label class="d-flex align-items-center gap-2"><input type="checkbox" class="col-toggle" data-col="col-dates" checked> Issue / Due Date</label>
                            <label class="d-flex align-items-center gap-2"><input type="checkbox" class="col-toggle" data-col="col-status" checked> Status</label>
                            <label class="d-flex align-items-center gap-2"><input type="checkbox" class="col-toggle" data-col="col-fine" checked> Fine Amount</label>
                            <label class="d-flex align-items-center gap-2"><input type="checkbox" class="col-toggle" data-col="col-author" checked> Author</label>
                            <label class="d-flex align-items-center gap-2"><input type="checkbox" class="col-toggle" data-col="col-actions" checked> Action</label>
                        </div>
                    </div>
                </div>

                <!-- Filters Shortcut -->
                <div class="toolbar-tool-item" onclick="toggleFilterDrawer(event)">
                    <i class="fas fa-filter"></i> FILTERS
                </div>

                <!-- Density Switcher -->
                <div class="toolbar-tool-item" onclick="toggleDensityMenu(event)">
                    <i class="fas fa-align-justify"></i> DENSITY
                    <div class="toolbar-popover" id="densityPopoverMenu">
                        <div class="fw-bold text-dark mb-2 pb-1 border-bottom" style="font-size: 11.5px;">Row Density</div>
                        <div class="d-flex flex-column gap-1 text-dark" style="font-size: 12px;">
                            <div class="p-1.5 rounded cursor-pointer hover-bg" onclick="setDensity('compact')">
                                <i class="fas fa-compress me-1 text-primary"></i> Compact
                            </div>
                            <div class="p-1.5 rounded cursor-pointer hover-bg" onclick="setDensity('normal')">
                                <i class="fas fa-bars me-1 text-primary"></i> Standard (Default)
                            </div>
                            <div class="p-1.5 rounded cursor-pointer hover-bg" onclick="setDensity('spacious')">
                                <i class="fas fa-expand me-1 text-primary"></i> Spacious
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-white-50 small" style="font-size: 11.5px;">
                Showing <span class="text-white fw-bold">{{ $transactions->total() }}</span> total transactions
            </div>
        </div>

        <!-- Table Container -->
        <div class="table-responsive">
            <table class="cat-table" id="transactionsTable">
                <thead>
                    <tr>
                        <th class="col-borrower">Borrower</th>
                        <th class="col-id">{{ $tab === 'staffs' ? 'Employee ID' : 'Admission ID' }}</th>
                        <th class="col-class">{{ $tab === 'staffs' ? 'Department' : 'Class' }}</th>
                        <th class="col-bookid">Book ID</th>
                        <th class="col-accno">Accession Number</th>
                        <th class="col-title">Book Title</th>
                        <th class="col-dates">Issue / Due Date</th>
                        <th class="col-status">Status</th>
                        <th class="col-fine text-end">Fine Amount</th>
                        <th class="col-author">Author</th>
                        <th class="col-actions text-center">Action</th>
                    </tr>
                </thead>
                <tbody id="transactionTableBody">
                    @forelse($transactions as $txn)
                    @php
                        $borrower = $txn->member_type === 'staff' ? $txn->staff : $txn->student;
                        $borrowerName = $borrower ? trim(($borrower->first_name ?? '') . ' ' . ($borrower->last_name ?? '')) : 'Unknown Borrower';
                        $borrowerId = $txn->member_type === 'staff' ? ($borrower->employee_id ?? ('EMP-' . $txn->staff_id)) : ($borrower->admission_number ?? ('ADM-' . $txn->student_id));
                        $classOrDept = $txn->member_type === 'staff'
                            ? ($borrower->designation?->name ?? 'Staff')
                            : (($borrower->schoolClass?->name ?? 'Class') . ' - ' . ($borrower->section?->name ?? ''));
                        
                        $bookIdCode = 'BK-' . str_pad($txn->book_id, 5, '0', STR_PAD_LEFT);
                    @endphp
                    <tr id="txn-row-{{ $txn->id }}" data-search="{{ strtolower($borrowerName . ' ' . $borrowerId . ' ' . ($txn->book?->title ?? '') . ' ' . ($txn->book?->accession_no ?? '') . ' ' . $txn->transaction_code) }}">
                        <!-- Borrower -->
                        <td class="col-borrower">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold overflow-hidden" style="width: 34px; height: 34px; font-size: 11px; background: var(--cat-blue-primary); flex-shrink: 0;">
                                    @if($borrower && $borrower->photo_url && !str_contains($borrower->photo_url, 'avatar-student.png'))
                                        <img src="{{ $borrower->photo_url }}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.parentElement.innerHTML='{{ strtoupper(substr($borrowerName, 0, 1)) }}'">
                                    @else
                                        {{ strtoupper(substr($borrowerName, 0, 1)) }}
                                    @endif
                                </div>
                                <div>
                                    <div class="fw-bold text-dark" style="font-size: 13px;">{{ $borrowerName }}</div>
                                    <div class="text-muted" style="font-size: 11px;">{{ $txn->transaction_code }}</div>
                                </div>
                            </div>
                        </td>

                        <!-- ID -->
                        <td class="col-id">
                            <span class="badge bg-light text-dark border fw-bold">{{ $borrowerId }}</span>
                        </td>

                        <!-- Class / Department -->
                        <td class="col-class">
                            <span class="fw-semibold text-secondary">{{ $classOrDept }}</span>
                        </td>

                        <!-- Book ID -->
                        <td class="col-bookid">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-bold">{{ $bookIdCode }}</span>
                        </td>

                        <!-- Accession Number -->
                        <td class="col-accno">
                            <span class="font-monospace text-dark fw-bold">{{ $txn->book?->accession_no ?? '—' }}</span>
                        </td>

                        <!-- Book Title -->
                        <td class="col-title">
                            <div class="fw-bold text-dark">{{ $txn->book?->title ?? 'Untitled Book' }}</div>
                            <div class="text-muted small" style="font-size: 11px;">
                                <i class="fas fa-layer-group text-primary me-1"></i> {{ $txn->book?->section?->name ?? 'General' }}
                            </div>
                        </td>

                        <!-- Issue / Due Dates -->
                        <td class="col-dates">
                            <div class="small text-dark">
                                <i class="fas fa-calendar-check text-success me-1"></i> {{ $txn->issue_date ? \Carbon\Carbon::parse($txn->issue_date)->format('d M Y') : '—' }}
                            </div>
                            <div class="small {{ $txn->status === 'overdue' ? 'text-danger fw-bold' : 'text-muted' }}">
                                <i class="fas fa-calendar-xmark text-danger me-1"></i> Due: {{ $txn->due_date ? \Carbon\Carbon::parse($txn->due_date)->format('d M Y') : '—' }}
                            </div>
                        </td>

                        <!-- Status Badge -->
                        <td class="col-status">
                            @if($txn->status === 'issued')
                            <span class="status-badge status-issued"><i class="fas fa-clock"></i> Active Loan</span>
                            @elseif($txn->status === 'returned')
                            <span class="status-badge status-returned"><i class="fas fa-circle-check"></i> Returned</span>
                            @elseif($txn->status === 'overdue')
                            <span class="status-badge status-overdue"><i class="fas fa-triangle-exclamation"></i> Overdue ({{ $txn->late_days }}d)</span>
                            @else
                            <span class="status-badge status-issued">{{ strtoupper($txn->status) }}</span>
                            @endif
                        </td>

                        <!-- Fine Amount -->
                        <td class="col-fine text-end">
                            @if($txn->total_fine > 0)
                                <div class="fine-pill {{ $txn->fine_status === 'paid' ? 'fine-pill-paid' : 'fine-pill-pending' }}">
                                    ₹{{ number_format($txn->total_fine, 2) }}
                                </div>
                                <div class="text-muted" style="font-size: 10px; text-transform: uppercase;">{{ $txn->fine_status }}</div>
                            @else
                                <span class="fine-pill fine-pill-zero">₹0.00</span>
                            @endif
                        </td>

                        <!-- Author -->
                        <td class="col-author">
                            <span class="text-secondary small">{{ $txn->book?->author ?? '—' }}</span>
                        </td>

                        <!-- Action Buttons -->
                        <td class="col-actions text-center">
                            <div class="d-flex align-items-center justify-content-center gap-1">
                                @if($txn->status !== 'returned')
                                <!-- Quick Return Action -->
                                <button type="button" class="cat-action-btn text-success" title="Return Book" onclick="triggerReturnForTxn({{ $txn->id }}, '{{ addslashes($txn->book?->title ?? '') }}', '{{ $txn->book?->accession_no ?? '' }}', '{{ $bookIdCode }}', '{{ addslashes($borrowerName) }}', '{{ $txn->due_date ? \Carbon\Carbon::parse($txn->due_date)->format('Y-m-d') : '' }}', {{ $txn->late_days ?? 0 }}, {{ $txn->late_fine_amount ?? 0 }})">
                                    <i class="fas fa-arrow-rotate-left"></i>
                                </button>
                                <!-- Loan Renewal Action -->
                                <button type="button" class="cat-action-btn text-primary" title="Renew Loan" onclick="triggerRenewLoan({{ $txn->id }}, '{{ addslashes($txn->book?->title ?? '') }}', {{ $txn->renewed_count }})">
                                    <i class="fas fa-arrows-rotate"></i>
                                </button>
                                @endif
                                <!-- Print Twin Slip (Opens Dedicated Page in New Tab) -->
                                <a href="{{ route('school.library.transactions.print', $txn->id) }}" target="_blank" class="cat-action-btn text-secondary" title="Print Twin Issue Voucher (Opens New Tab)">
                                    <i class="fas fa-print"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="p-0">
                            <!-- Empty State Center Box -->
                            <div class="empty-state-box">
                                <div class="empty-state-icon">
                                    <i class="fas fa-book-open-reader"></i>
                                </div>
                                <h5 class="fw-bold text-dark mb-1" style="font-size: 15px;">No Borrow Transactions Found</h5>
                                <p class="text-muted small mx-auto mb-3" style="max-width: 360px; font-size: 12.5px; line-height: 1.5;">
                                    No books are currently issued or matching your selected filters for this {{ $tab === 'staffs' ? 'staff' : 'student' }} category.
                                </p>
                                <button type="button" class="btn cat-btn-action cat-btn-primary px-4 py-2" onclick="openIssueDrawer()">
                                    <i class="fas fa-plus me-1"></i> Issue First Book
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($transactions->hasPages())
        <div class="d-flex align-items-center justify-content-between p-3 border-top flex-wrap gap-2">
            <div class="text-muted small">
                Showing {{ $transactions->firstItem() ?? 0 }} to {{ $transactions->lastItem() ?? 0 }} of {{ $transactions->total() }} records
            </div>
            <div>
                {{ $transactions->links('pagination::bootstrap-5') }}
            </div>
        </div>
        @endif
    </div>
</div>

<!-- =========================================================================
     GLOBAL BACKDROP OVERLAY
     ========================================================================= -->
<div class="cat-drawer-overlay" id="globalDrawerOverlay" onclick="closeAllDrawers()"></div>

<!-- =========================================================================
     1. ISSUE BOOK SLIDE-OVER DRAWER (Multi-Book & Instant Auto-Fill)
     ========================================================================= -->
<div class="cat-drawer" id="issueBookDrawer">
    <div class="cat-drawer-header">
        <h5><i class="fas fa-book-medical"></i> Issue Book to Member</h5>
        <button type="button" class="cat-drawer-close" onclick="closeAllDrawers()">&times;</button>
    </div>

    <form id="issueBookForm" class="cat-drawer-form">
        @csrf
        <div class="cat-drawer-body">
            <!-- Persistent Inline Alert in Drawer -->
            <div id="issueDrawerAlert" class="alert alert-danger py-2 px-3 mb-3 fw-bold small" style="display: none; border-radius: 8px;"></div>

            <!-- 1. Select Borrower Type -->
            <div class="drawer-card-box">
                <div class="drawer-card-box-title">
                    <span><i class="fas fa-user-tag text-primary me-1"></i> 1. Select Borrower Type</span>
                </div>
                
                <div class="borrower-type-toggle">
                    <div class="type-pill-btn active" id="pillStudent" onclick="switchBorrowerType('student')">
                        <input type="radio" name="member_type" value="student" checked class="d-none">
                        <i class="fas fa-user-graduate"></i> Student
                    </div>
                    <div class="type-pill-btn" id="pillStaff" onclick="switchBorrowerType('staff')">
                        <input type="radio" name="member_type" value="staff" class="d-none">
                        <i class="fas fa-chalkboard-user"></i> Staff Member
                    </div>
                </div>

                <!-- Borrower Search Input -->
                <div class="position-relative" style="position: relative;">
                    <label class="cat-field-label" id="borrowerSearchLabel">Search Student (Name / Admission No / Roll No) <span class="text-danger">*</span></label>
                    <div class="cat-input-group">
                        <span class="cat-input-addon"><i class="fas fa-magnifying-glass"></i></span>
                        <input type="text" id="borrowerSearchInput" class="cat-input-field" placeholder="Enter student name, admission no or roll..." autocomplete="off" oninput="debounceLookupBorrower(this.value)" onkeydown="handleBorrowerKeydown(event)">
                    </div>

                    <!-- Instant Search Dropdown -->
                    <div id="borrowerSearchResults" class="live-search-dropdown"></div>
                </div>

                <input type="hidden" id="issue_student_id" name="student_id">
                <input type="hidden" id="issue_staff_id" name="staff_id">

                <!-- Selected Borrower Profile Card -->
                <div id="selectedBorrowerCard" class="selected-entity-card" style="display: none;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="entity-avatar" id="bCardAvatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <div class="entity-title" id="bCardName">Borrower Name</div>
                                <div class="entity-subtitle" id="bCardSub">Class / Designation • Code</div>
                            </div>
                        </div>
                        <div id="bCardBadge">
                            <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold px-2.5 py-1.5">
                                <i class="fas fa-circle-check me-1"></i> Eligible
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Select Books from Catalogue (Multi-Book Support) -->
            <div class="drawer-card-box">
                <div class="drawer-card-box-title">
                    <span><i class="fas fa-book text-primary me-1"></i> 2. Select Books from Catalogue</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle" id="bookQuotaBadge">Multi-Book Selection</span>
                </div>

                <div class="position-relative" style="position: relative;">
                    <label class="cat-field-label">Search & Add Books (Title / ID / Barcode / Accession No) <span class="text-danger">*</span></label>
                    <div class="cat-input-group">
                        <span class="cat-input-addon"><i class="fas fa-barcode"></i></span>
                        <input type="text" id="bookSearchInput" class="cat-input-field" placeholder="Type book title, code or scan barcode to add..." autocomplete="off" oninput="debounceLookupBook(this.value)" onkeydown="handleBookKeydown(event)">
                    </div>

                    <!-- Instant Book Search Dropdown -->
                    <div id="bookSearchResults" class="live-search-dropdown"></div>
                </div>

                <!-- Selected Multi-Books Container -->
                <div id="selectedBooksWrapper" style="display: none; margin-top: 14px;">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <label class="cat-field-label mb-0">Selected Books to Issue (<span id="selectedBooksCount">0</span>):</label>
                        <button type="button" class="btn btn-sm btn-link text-danger p-0 text-decoration-none small" onclick="clearSelectedBooks()">Clear All</button>
                    </div>
                    <div class="multi-books-container" id="selectedBooksList"></div>
                </div>
            </div>

            <!-- 3. Loan Period & Dates -->
            <div class="drawer-card-box">
                <div class="drawer-card-box-title">
                    <span><i class="fas fa-calendar-days text-primary me-1"></i> 3. Loan Period & Dates</span>
                </div>

                <div class="row g-3">
                    <div class="col-md-6 col-12">
                        <label class="cat-field-label">Issue Date <span class="text-danger">*</span></label>
                        <input type="date" id="issue_date" name="issue_date" class="cat-form-control" value="{{ date('Y-m-d') }}" required onchange="calculateAutoDueDate()">
                    </div>

                    <div class="col-md-6 col-12">
                        <label class="cat-field-label">Due Return Date <span class="text-danger">*</span></label>
                        <input type="date" id="due_date" name="due_date" class="cat-form-control" value="{{ date('Y-m-d', strtotime('+14 days')) }}" required>
                    </div>

                    <div class="col-12">
                        <label class="cat-field-label">Remarks / Note</label>
                        <input type="text" id="issue_remarks" name="remarks" class="cat-form-control" placeholder="Optional notes for special loan arrangements...">
                    </div>
                </div>
            </div>
        </div>

        <div class="cat-drawer-footer">
            <button type="button" class="btn btn-outline-secondary px-4 fw-bold" onclick="closeAllDrawers()" style="border-radius: 8px;">
                Cancel
            </button>
            <button type="submit" class="btn btn-primary px-4 fw-bold" id="btnSubmitIssue" style="background: var(--cat-blue-gradient); border: none; border-radius: 8px; box-shadow: 0 2px 8px rgba(0, 56, 184, 0.25);">
                <i class="fas fa-check-circle me-1"></i> <span id="issueSubmitBtnText">Issue Books Now</span>
            </button>
        </div>
    </form>
</div>

<!-- =========================================================================
     2. RETURN BOOK SLIDE-OVER DRAWER (Dynamic Fine Calculator)
     ========================================================================= -->
<div class="cat-drawer" id="returnBookDrawer">
    <div class="cat-drawer-header" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);">
        <h5><i class="fas fa-arrow-rotate-left"></i> Return Book & Collect Fine</h5>
        <button type="button" class="cat-drawer-close" onclick="closeAllDrawers()">&times;</button>
    </div>

    <form id="returnBookForm" class="cat-drawer-form">
        @csrf
        <input type="hidden" id="return_txn_id" name="id">

        <div class="cat-drawer-body">
            <!-- Search Selection Mode Radios -->
            <div class="drawer-card-box">
                <div class="drawer-card-box-title">
                    <span><i class="fas fa-magnifying-glass text-primary me-1"></i> Search Active Loan Record</span>
                </div>

                <div class="search-mode-radios">
                    <label class="mode-radio-label">
                        <input type="radio" name="return_search_mode" value="book_id" checked>
                        <span>Book ID</span>
                    </label>
                    <label class="mode-radio-label">
                        <input type="radio" name="return_search_mode" value="barcode">
                        <span>Barcode</span>
                    </label>
                    <label class="mode-radio-label">
                        <input type="radio" name="return_search_mode" value="accession">
                        <span>Accession Number</span>
                    </label>
                    <label class="mode-radio-label">
                        <input type="radio" name="return_search_mode" value="title">
                        <span>Book Title</span>
                    </label>
                    <label class="mode-radio-label">
                        <input type="radio" name="return_search_mode" value="isbn">
                        <span>ISBN Number</span>
                    </label>
                </div>

                <div class="position-relative" style="position: relative;">
                    <label class="cat-field-label">Enter Identifier / Scan Code</label>
                    <div class="cat-input-group">
                        <span class="cat-input-addon"><i class="fas fa-barcode"></i></span>
                        <input type="text" id="returnLookupInput" class="cat-input-field" placeholder="Enter Book ID, barcode or accession number..." oninput="debounceLookupReturn(this.value)">
                    </div>

                    <!-- Dropdown list of matching active loans -->
                    <div id="returnSearchResults" class="live-search-dropdown"></div>
                </div>
            </div>

            <!-- Active Issued Book & Borrower Details Card -->
            <div class="drawer-card-box" id="returnTxnDetailsCard" style="display: none;">
                <div class="drawer-card-box-title">
                    <span><i class="fas fa-receipt text-primary me-1"></i> Loan & Borrower Details</span>
                </div>

                <div class="p-3 rounded-3 mb-3" style="background: #f8fafc; border: 1px solid var(--cat-slate-200);">
                    <div class="row g-2">
                        <div class="col-6">
                            <span class="text-muted small">Borrower:</span>
                            <div class="fw-bold text-dark" id="retBorrowerName">—</div>
                        </div>
                        <div class="col-6">
                            <span class="text-muted small">Book Title:</span>
                            <div class="fw-bold text-primary" id="retBookTitle">—</div>
                        </div>
                        <div class="col-6">
                            <span class="text-muted small">Accession No / ID:</span>
                            <div class="fw-bold text-dark" id="retBookCode">—</div>
                        </div>
                        <div class="col-6">
                            <span class="text-muted small">Due Return Date:</span>
                            <div class="fw-bold text-danger" id="retDueDate">—</div>
                        </div>
                    </div>
                </div>

                <!-- Dynamic Late Fine Engine -->
                <div class="p-3 rounded-3 mb-3" style="background: #fffbeb; border: 1px solid #fde68a;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fw-bold text-dark small"><i class="fas fa-calculator text-warning me-1"></i> Overdue & Fine Status</span>
                        <span class="badge" id="retOverdueBadge" style="background: #fef2f2; color: #b91c1c;">0 Days Overdue</span>
                    </div>

                    <div class="row g-2 align-items-center">
                        <div class="col-md-6 col-12">
                            <label class="cat-field-label">Late Fine Calculated (₹)</label>
                            <input type="number" step="0.5" id="retLateFine" name="late_fine_override" class="cat-form-control fw-bold text-danger" value="0.00" oninput="recalcTotalReturnFine()">
                        </div>

                        <div class="col-md-6 col-12">
                            <label class="cat-field-label">Book Condition on Return</label>
                            <select id="retCondition" name="condition" class="cat-form-select" onchange="handleConditionChange(this.value)">
                                <option value="normal" selected>Normal / Good Condition</option>
                                <option value="damaged">Damaged Book (+ Penalty)</option>
                                <option value="lost">Lost Book (+ Replacement Cost)</option>
                            </select>
                        </div>

                        <div class="col-md-6 col-12" id="damageFineGroup" style="display: none;">
                            <label class="cat-field-label">Damage / Lost Penalty (₹)</label>
                            <input type="number" step="1" id="retDamageFine" name="damage_fine" class="cat-form-control fw-bold text-danger" value="0.00" oninput="recalcTotalReturnFine()">
                        </div>

                        <div class="col-md-6 col-12">
                            <label class="cat-field-label">Fine Payment Status</label>
                            <select id="retFineStatus" name="fine_status" class="cat-form-select fw-bold">
                                <option value="paid" selected>Paid Now (Collected)</option>
                                <option value="waived">Waive Fine (Exempted)</option>
                                <option value="pending">Add to Pending Ledger</option>
                            </select>
                        </div>
                    </div>

                    <!-- Total Fine Banner -->
                    <div class="d-flex align-items-center justify-content-between mt-3 pt-2 border-top">
                        <span class="fw-bold text-dark" style="font-size: 13px;">Total Fine Payable:</span>
                        <span class="fw-bold text-danger" style="font-size: 17px;" id="retTotalFineDisplay">₹0.00</span>
                    </div>
                </div>

                <!-- Return Date & Remarks -->
                <div class="row g-3">
                    <div class="col-md-6 col-12">
                        <label class="cat-field-label">Return Date</label>
                        <input type="date" id="retReturnDate" name="return_date" class="cat-form-control" value="{{ date('Y-m-d') }}">
                    </div>

                    <div class="col-md-6 col-12">
                        <label class="cat-field-label">Return Remarks</label>
                        <input type="text" id="retRemarks" name="remarks" class="cat-form-control" placeholder="Condition remarks, fine notes...">
                    </div>
                </div>
            </div>
        </div>

        <div class="cat-drawer-footer">
            <button type="button" class="btn btn-outline-secondary px-4 fw-bold" onclick="closeAllDrawers()" style="border-radius: 8px;">
                Cancel
            </button>
            <button type="submit" class="btn btn-primary px-4 fw-bold" id="btnSubmitReturn" disabled style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); border: none; border-radius: 8px; box-shadow: 0 2px 8px rgba(2, 132, 199, 0.25);">
                <i class="fas fa-check me-1"></i> Process Return
            </button>
        </div>
    </form>
</div>

<!-- Dynamic Floating Toast Notification -->
<div id="catToast" class="cat-toast">
    <i class="fas fa-circle-check text-success fa-lg" id="catToastIcon"></i>
    <span id="catToastMsg" style="flex: 1; line-height: 1.4;">Action completed successfully.</span>
    <button type="button" class="btn-close btn-close-white ms-2" style="font-size: 11px; filter: invert(1);" onclick="document.getElementById('catToast').classList.remove('show')"></button>
</div>

<!-- =========================================================================
     JAVASCRIPT LOGIC & DYNAMIC AJAX INTEGRATION
     ========================================================================= -->
<script>
    const studentLoanDays = {{ (int)($studentRule->borrow_period_days ?? 14) }};
    const staffLoanDays = {{ (int)($staffRule->borrow_period_days ?? 30) }};
    let debounceTimer = null;
    let cachedBorrowerResults = [];
    let cachedBookResults = [];
    let selectedBorrower = null;
    let selectedBooks = [];

    function showToast(msg, isError = false) {
        const toast = document.getElementById('catToast');
        const msgElem = document.getElementById('catToastMsg');
        const icon = document.getElementById('catToastIcon');
        msgElem.innerText = msg;
        if (isError) {
            icon.className = 'fas fa-circle-exclamation text-danger fa-lg';
            toast.style.background = '#7f1d1d';
            toast.style.borderColor = '#f87171';
            const drawerAlert = document.getElementById('issueDrawerAlert');
            if (drawerAlert) {
                drawerAlert.innerHTML = `<i class="fas fa-circle-exclamation me-1"></i> ${msg}`;
                drawerAlert.style.display = 'block';
            }
        } else {
            icon.className = 'fas fa-circle-check text-success fa-lg';
            toast.style.background = '#002266';
            toast.style.borderColor = '#60a5fa';
            const drawerAlert = document.getElementById('issueDrawerAlert');
            if (drawerAlert) {
                drawerAlert.style.display = 'none';
            }
        }
        toast.classList.add('show');
        clearTimeout(window.toastTimer);
        window.toastTimer = setTimeout(() => toast.classList.remove('show'), isError ? 10000 : 4000);
    }

    function closeAllDrawers() {
        document.getElementById('globalDrawerOverlay').classList.remove('open');
        document.getElementById('issueBookDrawer').classList.remove('open');
        document.getElementById('returnBookDrawer').classList.remove('open');
        hideAllDropdowns();
    }

    function hideAllDropdowns() {
        document.getElementById('borrowerSearchResults').style.display = 'none';
        document.getElementById('bookSearchResults').style.display = 'none';
        document.getElementById('returnSearchResults').style.display = 'none';
    }

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.position-relative')) {
            hideAllDropdowns();
        }
        document.getElementById('columnsPopoverMenu').classList.remove('show');
        document.getElementById('densityPopoverMenu').classList.remove('show');
    });

    // 1. ISSUE BOOK DRAWER HANDLERS
    function openIssueDrawer() {
        closeAllDrawers();
        document.getElementById('issueBookForm').reset();
        document.getElementById('issue_student_id').value = '';
        document.getElementById('issue_staff_id').value = '';
        const drawerAlert = document.getElementById('issueDrawerAlert');
        if (drawerAlert) drawerAlert.style.display = 'none';
        selectedBorrower = null;
        selectedBooks = [];
        renderSelectedBooks();
        document.getElementById('selectedBorrowerCard').style.display = 'none';
        switchBorrowerType('student');
        calculateAutoDueDate();
        document.getElementById('globalDrawerOverlay').classList.add('open');
        document.getElementById('issueBookDrawer').classList.add('open');
        setTimeout(() => document.getElementById('borrowerSearchInput').focus(), 250);
    }

    function switchBorrowerType(type) {
        document.querySelectorAll('.type-pill-btn').forEach(p => p.classList.remove('active'));
        if (type === 'staff') {
            document.getElementById('pillStaff').classList.add('active');
            document.querySelector('input[name="member_type"][value="staff"]').checked = true;
            document.querySelector('input[name="member_type"][value="student"]').checked = false;
            document.getElementById('borrowerSearchLabel').innerHTML = 'Search Staff (Name / Employee ID / Phone) <span class="text-danger">*</span>';
            document.getElementById('borrowerSearchInput').placeholder = 'Enter staff name, employee code or phone...';
        } else {
            document.getElementById('pillStudent').classList.add('active');
            document.querySelector('input[name="member_type"][value="student"]').checked = true;
            document.querySelector('input[name="member_type"][value="staff"]').checked = false;
            document.getElementById('borrowerSearchLabel').innerHTML = 'Search Student (Name / Admission No / Roll No) <span class="text-danger">*</span>';
            document.getElementById('borrowerSearchInput').placeholder = 'Enter student name, admission no or roll...';
        }
        document.getElementById('issue_student_id').value = '';
        document.getElementById('issue_staff_id').value = '';
        document.getElementById('borrowerSearchInput').value = '';
        document.getElementById('selectedBorrowerCard').style.display = 'none';
        selectedBorrower = null;
        selectedBooks = [];
        renderSelectedBooks();
        hideAllDropdowns();
        calculateAutoDueDate();
    }

    function calculateAutoDueDate() {
        const issueDateVal = document.getElementById('issue_date').value;
        if (!issueDateVal) return;
        const isStaff = document.querySelector('input[name="member_type"]:checked').value === 'staff';
        const daysToAdd = isStaff ? staffLoanDays : studentLoanDays;
        const d = new Date(issueDateVal);
        d.setDate(d.getDate() + daysToAdd);
        document.getElementById('due_date').value = d.toISOString().split('T')[0];
    }

    // Borrower Live Lookup
    function handleBorrowerKeydown(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            if (cachedBorrowerResults && cachedBorrowerResults.length > 0) {
                selectBorrower(cachedBorrowerResults[0]);
            }
        }
    }

    function debounceLookupBorrower(term) {
        clearTimeout(debounceTimer);
        const trimmed = term.trim();
        const box = document.getElementById('borrowerSearchResults');

        if (!trimmed) {
            box.style.display = 'none';
            cachedBorrowerResults = [];
            return;
        }

        debounceTimer = setTimeout(() => {
            const memberType = document.querySelector('input[name="member_type"]:checked').value;
            fetch(`{{ route("school.library.transactions.lookup-borrower") }}?type=${memberType}&term=${encodeURIComponent(trimmed)}`)
                .then(res => res.json())
                .then(data => {
                    box.innerHTML = '';
                    cachedBorrowerResults = data.results || [];
                    if (cachedBorrowerResults.length) {
                        cachedBorrowerResults.forEach(b => {
                            const div = document.createElement('div');
                            div.className = 'search-result-item';
                            div.innerHTML = `
                                <div>
                                    <div class="fw-bold text-dark" style="font-size: 13px;">${b.name} <span class="badge bg-light text-dark border ms-1">${b.code}</span></div>
                                    <div class="text-muted small" style="font-size: 11.5px;">${b.subtitle}</div>
                                </div>
                                <div class="text-end">
                                    <span class="badge ${b.is_eligible ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle'}">
                                        ${b.is_eligible ? 'Quota: ' + b.remaining_quota + ' left' : 'Limit Reached (' + b.borrowed_count + '/' + b.max_allowed + ')'}
                                    </span>
                                </div>
                            `;
                            div.onclick = () => selectBorrower(b);
                            box.appendChild(div);
                        });
                        box.style.display = 'block';

                        // Direct exact code match auto-selection
                        const exactMatch = cachedBorrowerResults.find(b => b.code.toLowerCase() === trimmed.toLowerCase());
                        if (exactMatch) {
                            selectBorrower(exactMatch);
                        }
                    } else {
                        box.innerHTML = '<div class="p-3 text-muted small text-center"><i class="fas fa-user-slash me-1"></i> No matching borrowers found</div>';
                        box.style.display = 'block';
                    }
                })
                .catch(err => {
                    console.error(err);
                });
        }, 200);
    }

    function selectBorrower(b) {
        selectedBorrower = b;
        const memberType = document.querySelector('input[name="member_type"]:checked').value;
        if (memberType === 'staff') {
            document.getElementById('issue_staff_id').value = b.id;
            document.getElementById('issue_student_id').value = '';
        } else {
            document.getElementById('issue_student_id').value = b.id;
            document.getElementById('issue_staff_id').value = '';
        }

        document.getElementById('borrowerSearchInput').value = `${b.name} (${b.code})`;
        document.getElementById('borrowerSearchResults').style.display = 'none';

        document.getElementById('bCardName').innerText = b.name;
        document.getElementById('bCardSub').innerText = `${b.subtitle} • Code: ${b.code}`;
        document.getElementById('bCardAvatar').innerText = b.name.charAt(0).toUpperCase();
        document.getElementById('bCardBadge').innerHTML = `
            <span class="badge ${b.is_eligible ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle'} fw-bold px-2.5 py-1.5">
                <i class="fas ${b.is_eligible ? 'fa-circle-check' : 'fa-circle-xmark'} me-1"></i>
                ${b.is_eligible ? 'Can borrow ' + b.remaining_quota + ' more book(s)' : 'Limit Reached (' + b.borrowed_count + '/' + b.max_allowed + ')'}
            </span>
        `;
        document.getElementById('selectedBorrowerCard').style.display = 'block';
        document.getElementById('bookQuotaBadge').innerText = `Max Quota: ${b.remaining_quota} Books`;

        const drawerAlert = document.getElementById('issueDrawerAlert');
        if (drawerAlert) drawerAlert.style.display = 'none';

        // Focus next on book search
        setTimeout(() => document.getElementById('bookSearchInput').focus(), 150);
    }

    // Book Live Lookup & Multi-Book Selection
    function handleBookKeydown(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            if (cachedBookResults && cachedBookResults.length > 0) {
                addBookToSelectedList(cachedBookResults[0]);
            }
        }
    }

    function debounceLookupBook(term) {
        clearTimeout(debounceTimer);
        const trimmed = term.trim();
        const box = document.getElementById('bookSearchResults');

        if (!trimmed) {
            box.style.display = 'none';
            cachedBookResults = [];
            return;
        }

        debounceTimer = setTimeout(() => {
            fetch(`{{ route("school.library.transactions.lookup-book") }}?term=${encodeURIComponent(trimmed)}`)
                .then(res => res.json())
                .then(data => {
                    box.innerHTML = '';
                    cachedBookResults = data.books || [];
                    if (cachedBookResults.length) {
                        cachedBookResults.forEach(bk => {
                            const isAlreadyAdded = selectedBooks.some(sb => sb.id === bk.id);
                            const div = document.createElement('div');
                            div.className = 'search-result-item';
                            div.innerHTML = `
                                <div>
                                    <div class="fw-bold text-primary" style="font-size: 13px;">${bk.title}</div>
                                    <div class="text-muted small" style="font-size: 11.5px;">${bk.author || 'Author'} • ACC: <strong>${bk.accession_no || ('BK-' + String(bk.id).padStart(5, '0'))}</strong></div>
                                </div>
                                <div class="text-end">
                                    ${isAlreadyAdded ? '<span class="badge bg-secondary">Added</span>' : `
                                    <span class="badge ${bk.available_copies > 0 ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle'}">
                                        ${bk.available_copies > 0 ? '+ Add (' + bk.available_copies + ' Avail)' : 'Out of Stock'}
                                    </span>`}
                                </div>
                            `;
                            div.onclick = () => addBookToSelectedList(bk);
                            box.appendChild(div);
                        });
                        box.style.display = 'block';

                        // Direct exact barcode/accession match auto-selection
                        const exactMatch = cachedBookResults.find(bk => 
                            (bk.accession_no && bk.accession_no.toLowerCase() === trimmed.toLowerCase()) ||
                            (bk.isbn && bk.isbn.toLowerCase() === trimmed.toLowerCase()) ||
                            ('bk-' + String(bk.id).padStart(5, '0')).toLowerCase() === trimmed.toLowerCase()
                        );
                        if (exactMatch) {
                            addBookToSelectedList(exactMatch);
                        }
                    } else {
                        box.innerHTML = '<div class="p-3 text-muted small text-center"><i class="fas fa-book me-1"></i> No matching books found in catalogue</div>';
                        box.style.display = 'block';
                    }
                })
                .catch(err => {
                    console.error(err);
                });
        }, 200);
    }

    function addBookToSelectedList(bk) {
        if (!selectedBorrower) {
            showToast('Please search and select a borrower first!', true);
            document.getElementById('borrowerSearchInput').focus();
            return;
        }

        if (bk.available_copies < 1) {
            showToast(`"${bk.title}" has 0 available copies in library.`, true);
            return;
        }

        if (selectedBooks.some(b => b.id === bk.id)) {
            showToast(`"${bk.title}" is already in your selected books list.`, true);
            return;
        }

        const maxQuota = selectedBorrower ? (selectedBorrower.remaining_quota || 3) : 3;
        if (selectedBooks.length >= maxQuota) {
            showToast(`Borrowing limit reached! This borrower can only take ${maxQuota} book(s) at this time.`, true);
            return;
        }

        selectedBooks.push(bk);
        document.getElementById('bookSearchInput').value = '';
        document.getElementById('bookSearchResults').style.display = 'none';
        renderSelectedBooks();
        showToast(`Added "${bk.title}" to issue list.`);
    }

    function removeSelectedBook(bookId) {
        selectedBooks = selectedBooks.filter(b => b.id !== bookId);
        renderSelectedBooks();
    }

    function clearSelectedBooks() {
        selectedBooks = [];
        renderSelectedBooks();
    }

    function renderSelectedBooks() {
        const wrapper = document.getElementById('selectedBooksWrapper');
        const list = document.getElementById('selectedBooksList');
        const countSpan = document.getElementById('selectedBooksCount');
        const submitBtnText = document.getElementById('issueSubmitBtnText');

        countSpan.innerText = selectedBooks.length;

        if (selectedBooks.length === 0) {
            wrapper.style.display = 'none';
            list.innerHTML = '';
            submitBtnText.innerText = 'Issue Book Now';
            return;
        }

        wrapper.style.display = 'block';
        list.innerHTML = '';

        selectedBooks.forEach((bk, index) => {
            const accCode = bk.accession_no || ('BK-' + String(bk.id).padStart(5, '0'));
            const row = document.createElement('div');
            row.className = 'multi-book-row';
            row.innerHTML = `
                <input type="hidden" name="book_ids[]" value="${bk.id}">
                <div class="d-flex align-items-center gap-2.5">
                    <span class="badge bg-primary text-white" style="font-size: 11px; width: 22px; height: 22px; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%;">${index + 1}</span>
                    <div>
                        <div class="fw-bold text-dark" style="font-size: 12.5px;">${bk.title}</div>
                        <div class="text-muted small" style="font-size: 11px;">
                            ACC: <strong>${accCode}</strong> • ${bk.author || 'Author'} • Sec: ${bk.section ? bk.section.name : 'General'}
                        </div>
                    </div>
                </div>
                <button type="button" class="multi-book-remove" title="Remove Book" onclick="removeSelectedBook(${bk.id})">
                    <i class="fas fa-times"></i>
                </button>
            `;
            list.appendChild(row);
        });

        submitBtnText.innerText = selectedBooks.length === 1 ? 'Issue 1 Book Now' : `Issue ${selectedBooks.length} Books Now`;
    }

    // Submit Issue Book Form
    document.getElementById('issueBookForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const drawerAlert = document.getElementById('issueDrawerAlert');
        drawerAlert.style.display = 'none';

        // Auto-select borrower if typed but not explicitly clicked
        if (!selectedBorrower) {
            const borrowerInput = document.getElementById('borrowerSearchInput');
            const term = borrowerInput.value.trim();
            if (term && cachedBorrowerResults.length > 0) {
                selectBorrower(cachedBorrowerResults[0]);
            } else if (term) {
                const memberType = document.querySelector('input[name="member_type"]:checked').value;
                try {
                    const res = await fetch(`{{ route("school.library.transactions.lookup-borrower") }}?type=${memberType}&term=${encodeURIComponent(term)}`);
                    const data = await res.json();
                    if (data.results && data.results.length > 0) {
                        selectBorrower(data.results[0]);
                    }
                } catch(e) {}
            }
        }

        if (!selectedBorrower) {
            showToast('Please search and click a student or staff member from the suggestions.', true);
            document.getElementById('borrowerSearchInput').focus();
            return;
        }

        // Auto-add book if typed but not added to queue
        if (selectedBooks.length === 0) {
            const bookInput = document.getElementById('bookSearchInput');
            const bTerm = bookInput.value.trim();
            if (bTerm && cachedBookResults.length > 0) {
                addBookToSelectedList(cachedBookResults[0]);
            } else if (bTerm) {
                try {
                    const res = await fetch(`{{ route("school.library.transactions.lookup-book") }}?term=${encodeURIComponent(bTerm)}`);
                    const data = await res.json();
                    if (data.books && data.books.length > 0) {
                        addBookToSelectedList(data.books[0]);
                    }
                } catch(e) {}
            }
        }

        if (selectedBooks.length === 0) {
            showToast('Please search and add at least one book to the issue list.', true);
            document.getElementById('bookSearchInput').focus();
            return;
        }

        const btn = document.getElementById('btnSubmitIssue');
        const origHTML = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Issuing Books...';
        btn.disabled = true;

        const formData = new FormData(this);

        fetch('{{ route("school.library.transactions.issue") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            btn.innerHTML = origHTML;
            btn.disabled = false;
            if (data.success) {
                closeAllDrawers();
                showToast(data.message);
                if (data.issued_ids && data.issued_ids.length > 0) {
                    window.open(`{{ url('/school/library/transactions') }}/${data.issued_ids[0]}/print`, '_blank');
                    setTimeout(() => window.location.reload(), 900);
                } else {
                    setTimeout(() => window.location.reload(), 700);
                }
            } else {
                showToast(data.message || 'Error issuing books.', true);
            }
        })
        .catch(err => {
            btn.innerHTML = origHTML;
            btn.disabled = false;
            showToast('Network error during book issue.', true);
        });
    });

    // 2. RETURN BOOK DRAWER HANDLERS
    function openReturnDrawer() {
        closeAllDrawers();
        document.getElementById('returnBookForm').reset();
        document.getElementById('return_txn_id').value = '';
        document.getElementById('returnTxnDetailsCard').style.display = 'none';
        document.getElementById('btnSubmitReturn').disabled = true;
        document.getElementById('globalDrawerOverlay').classList.add('open');
        document.getElementById('returnBookDrawer').classList.add('open');
        setTimeout(() => document.getElementById('returnLookupInput').focus(), 250);
    }

    function debounceLookupReturn(term) {
        clearTimeout(debounceTimer);
        const trimmed = term.trim();
        const box = document.getElementById('returnSearchResults');

        if (!trimmed) {
            box.style.display = 'none';
            return;
        }
        const mode = document.querySelector('input[name="return_search_mode"]:checked').value;
        debounceTimer = setTimeout(() => {
            fetch(`{{ route("school.library.transactions.lookup-book") }}?type=${mode}&term=${encodeURIComponent(trimmed)}`)
                .then(res => res.json())
                .then(data => {
                    box.innerHTML = '';
                    if (data.active_transactions && data.active_transactions.length) {
                        data.active_transactions.forEach(t => {
                            const borrower = t.member_type === 'staff' ? t.staff : t.student;
                            const bName = borrower ? (borrower.first_name + ' ' + (borrower.last_name || '')) : 'Borrower';
                            const div = document.createElement('div');
                            div.className = 'search-result-item';
                            div.innerHTML = `
                                <div>
                                    <div class="fw-bold text-primary" style="font-size: 13px;">${t.book ? t.book.title : 'Book'}</div>
                                    <div class="text-muted small" style="font-size: 11.5px;">Borrower: <strong>${bName}</strong> • Due: ${t.due_date}</div>
                                </div>
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Select for Return</span>
                            `;
                            div.onclick = () => selectTransactionForReturn(t);
                            box.appendChild(div);
                        });
                        box.style.display = 'block';

                        if (data.active_transactions.length === 1) {
                            selectTransactionForReturn(data.active_transactions[0]);
                        }
                    } else {
                        box.innerHTML = '<div class="p-3 text-muted small text-center"><i class="fas fa-circle-info me-1"></i> No active loans found matching this identifier</div>';
                        box.style.display = 'block';
                    }
                });
        }, 200);
    }

    function selectTransactionForReturn(t) {
        const borrower = t.member_type === 'staff' ? t.staff : t.student;
        const bName = borrower ? (borrower.first_name + ' ' + (borrower.last_name || '')) : 'Borrower';
        const bCode = borrower ? (borrower.admission_number || borrower.employee_id || ('ID-' + borrower.id)) : '';

        document.getElementById('return_txn_id').value = t.id;
        document.getElementById('returnLookupInput').value = (t.book ? t.book.title : '') + ` (${t.transaction_code})`;
        document.getElementById('returnSearchResults').style.display = 'none';

        document.getElementById('retBorrowerName').innerText = `${bName} (${bCode})`;
        document.getElementById('retBookTitle').innerText = t.book ? t.book.title : 'Book';
        document.getElementById('retBookCode').innerText = (t.book ? t.book.accession_no : '') || ('BK-' + t.book_id);
        document.getElementById('retDueDate').innerText = t.due_date;

        const lateDays = t.late_days || 0;
        const lateFine = t.late_fine_amount || 0;
        document.getElementById('retOverdueBadge').innerText = lateDays > 0 ? `${lateDays} Days Overdue` : 'On Time Return';
        document.getElementById('retLateFine').value = parseFloat(lateFine).toFixed(2);
        document.getElementById('retCondition').value = 'normal';
        document.getElementById('damageFineGroup').style.display = 'none';
        document.getElementById('retDamageFine').value = '0.00';

        recalcTotalReturnFine();

        document.getElementById('returnTxnDetailsCard').style.display = 'block';
        document.getElementById('btnSubmitReturn').disabled = false;
    }

    function triggerReturnForTxn(id, title, accNo, bookCode, borrowerName, dueDate, lateDays, lateFine) {
        openReturnDrawer();
        selectTransactionForReturn({
            id: id,
            transaction_code: 'TXN-' + id,
            book: { title: title, accession_no: accNo },
            book_id: id,
            due_date: dueDate,
            late_days: lateDays,
            late_fine_amount: lateFine,
            student: { first_name: borrowerName, last_name: '', admission_number: '' }
        });
    }

    function handleConditionChange(cond) {
        const damageGroup = document.getElementById('damageFineGroup');
        const damageInput = document.getElementById('retDamageFine');
        if (cond === 'damaged') {
            damageGroup.style.display = 'block';
            damageInput.value = '100.00';
        } else if (cond === 'lost') {
            damageGroup.style.display = 'block';
            damageInput.value = '250.00';
        } else {
            damageGroup.style.display = 'none';
            damageInput.value = '0.00';
        }
        recalcTotalReturnFine();
    }

    function recalcTotalReturnFine() {
        const late = parseFloat(document.getElementById('retLateFine').value) || 0;
        const damage = parseFloat(document.getElementById('retDamageFine').value) || 0;
        const total = (late + damage).toFixed(2);
        document.getElementById('retTotalFineDisplay').innerText = '₹' + total;
    }

    // Submit Return Book Form
    document.getElementById('returnBookForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('return_txn_id').value;
        if (!id) return;

        const btn = document.getElementById('btnSubmitReturn');
        const origHTML = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Processing...';
        btn.disabled = true;

        const formData = new FormData(this);

        fetch(`{{ url('/school/library/transactions/return') }}/${id}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            btn.innerHTML = origHTML;
            btn.disabled = false;
            if (data.success) {
                closeAllDrawers();
                showToast(data.message);
                setTimeout(() => window.location.reload(), 700);
            } else {
                showToast(data.message || 'Error processing return.', true);
            }
        })
        .catch(err => {
            btn.innerHTML = origHTML;
            btn.disabled = false;
            showToast('Network error during book return.', true);
        });
    });

    // 3. RENEW LOAN ACTION
    function triggerRenewLoan(id, title, currentCount) {
        if (!confirm(`Do you want to renew the loan period for "${title}"? (Current Renewals: ${currentCount})`)) return;

        fetch(`{{ url('/school/library/transactions/renew') }}/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast(data.message);
                setTimeout(() => window.location.reload(), 700);
            } else {
                showToast(data.message || 'Error renewing loan.', true);
            }
        })
        .catch(err => {
            showToast('Network error renewing loan.', true);
        });
    }

    // 4. TOOLBAR & CLIENT-SIDE CONTROLS
    function toggleColumnsMenu(e) {
        e.stopPropagation();
        document.getElementById('densityPopoverMenu').classList.remove('show');
        document.getElementById('columnsPopoverMenu').classList.toggle('show');
    }

    function toggleDensityMenu(e) {
        e.stopPropagation();
        document.getElementById('columnsPopoverMenu').classList.remove('show');
        document.getElementById('densityPopoverMenu').classList.toggle('show');
    }

    function toggleFilterDrawer(e) {
        document.getElementById('transFilterForm').scrollIntoView({ behavior: 'smooth' });
    }

    document.querySelectorAll('.col-toggle').forEach(chk => {
        chk.addEventListener('change', function() {
            const colClass = this.dataset.col;
            const cells = document.querySelectorAll(`.${colClass}`);
            cells.forEach(c => c.style.display = this.checked ? '' : 'none');
        });
    });

    function setDensity(level) {
        const table = document.getElementById('transactionsTable');
        table.classList.remove('density-compact', 'density-spacious');
        if (level === 'compact') table.classList.add('density-compact');
        if (level === 'spacious') table.classList.add('density-spacious');
        document.getElementById('densityPopoverMenu').classList.remove('show');
    }

    function handleClientSearch(val) {
        const term = val.toLowerCase().trim();
        const rows = document.querySelectorAll('#transactionTableBody tr');
        rows.forEach(r => {
            const searchData = r.dataset.search || '';
            r.style.display = searchData.includes(term) ? '' : 'none';
        });
    }
</script>
@endsection
