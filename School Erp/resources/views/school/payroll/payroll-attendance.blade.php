@extends('layouts.app')

@section('title', 'Payroll Attendance — Verification Stage')

@section('styles')
<style>
    :root {
        --primary-blue: #1e40af;
        --secondary-blue: #3b82f6;
        --accent-blue: #60a5fa;
        --light-blue-bg: #eff6ff;
        --card-border: #e2e8f0;
        --text-dark: #0f172a;
        --text-muted: #64748b;
    }

    .py-att-container {
        width: 100% !important;
        max-width: 100% !important;
        padding: 24px 30px;
        box-sizing: border-box;
    }

    /* Page Header Banner */
    .py-att-banner {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 60%, #3b82f6 100%);
        border-radius: 18px;
        padding: 26px 32px;
        color: #ffffff;
        margin-bottom: 24px;
        box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.25);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 20px;
        position: relative;
        overflow: hidden;
    }
    .py-att-banner::after {
        content: '';
        position: absolute;
        right: -40px;
        bottom: -40px;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }
    .py-att-banner-title {
        font-size: 24px;
        font-weight: 800;
        letter-spacing: -0.5px;
        margin: 0 0 6px 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .py-att-banner-sub {
        font-size: 13.5px;
        color: #dbeafe;
        margin: 0;
        max-width: 650px;
        line-height: 1.5;
    }
    .py-att-workflow-badge {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 12px;
        padding: 10px 18px;
        font-size: 12px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #ffffff;
    }

    /* KPI Summary Cards */
    .py-kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 18px;
        margin-bottom: 24px;
    }
    .py-kpi-card {
        background: #ffffff;
        border: 1px solid var(--card-border);
        border-radius: 16px;
        padding: 18px 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        transition: all 0.25s ease;
        display: flex;
        align-items: center;
        gap: 16px;
        position: relative;
        overflow: hidden;
    }
    .py-kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(37, 99, 235, 0.1);
        border-color: #bfdbfe;
    }
    .py-kpi-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }
    .py-kpi-info {
        flex: 1;
    }
    .py-kpi-label {
        font-size: 11.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-muted);
        margin-bottom: 2px;
    }
    .py-kpi-val {
        font-size: 22px;
        font-weight: 800;
        color: var(--text-dark);
        line-height: 1.2;
    }
    .py-kpi-sub {
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 2px;
    }

    /* Filter Form Card */
    .py-filter-card {
        background: #ffffff;
        border: 1px solid var(--card-border);
        border-radius: 16px;
        padding: 20px 24px;
        margin-bottom: 24px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
    }
    .py-filter-form {
        display: flex;
        align-items: flex-end;
        gap: 16px;
        flex-wrap: wrap;
    }
    .py-form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
        flex: 1;
        min-width: 180px;
    }
    .py-form-label {
        font-size: 12.5px;
        font-weight: 700;
        color: #334155;
    }
    .py-input, .py-select {
        width: 100%;
        padding: 10px 14px;
        border: 1.5px solid #cbd5e1;
        border-radius: 10px;
        font-size: 13.5px;
        font-weight: 600;
        color: #1e293b;
        background-color: #ffffff;
        outline: none;
        transition: all 0.2s ease;
        box-sizing: border-box;
    }
    .py-input:focus, .py-select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }
    .py-btn-primary {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
        color: #ffffff !important;
        border: none !important;
        padding: 10.5px 22px !important;
        border-radius: 10px !important;
        font-weight: 700 !important;
        font-size: 13.5px !important;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
        transition: all 0.2s ease;
        text-decoration: none;
        white-space: nowrap;
    }
    .py-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(37, 99, 235, 0.35);
        color: #ffffff !important;
    }
    .py-btn-outline {
        background: #ffffff !important;
        color: #2563eb !important;
        border: 1.5px solid #bfdbfe !important;
        padding: 9.5px 18px !important;
        border-radius: 10px !important;
        font-weight: 700 !important;
        font-size: 13px !important;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        text-decoration: none;
        white-space: nowrap;
    }
    .py-btn-outline:hover {
        background: #eff6ff !important;
        border-color: #2563eb !important;
        color: #1d4ed8 !important;
    }
    .py-btn-excel {
        background: #10b981 !important;
        color: #ffffff !important;
        border: none !important;
        padding: 10px 18px !important;
        border-radius: 10px !important;
        font-weight: 700 !important;
        font-size: 13px !important;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        text-decoration: none;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
    }
    .py-btn-excel:hover {
        background: #059669 !important;
        transform: translateY(-2px);
        color: #ffffff !important;
    }
    .py-btn-pdf {
        background: #ef4444 !important;
        color: #ffffff !important;
        border: none !important;
        padding: 10px 18px !important;
        border-radius: 10px !important;
        font-weight: 700 !important;
        font-size: 13px !important;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        text-decoration: none;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
    }
    .py-btn-pdf:hover {
        background: #dc2626 !important;
        transform: translateY(-2px);
        color: #ffffff !important;
    }

    /* Verification Progress Banner */
    .py-progress-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 14px 20px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
    }
    .py-progress-bar-wrap {
        flex: 1;
        min-width: 240px;
        background: #f1f5f9;
        height: 10px;
        border-radius: 999px;
        overflow: hidden;
        position: relative;
    }
    .py-progress-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #3b82f6, #10b981);
        border-radius: 999px;
        transition: width 0.4s ease;
    }

    /* Department Cards */
    .py-dept-card {
        background: #ffffff;
        border: 1px solid var(--card-border);
        border-radius: 16px;
        margin-bottom: 26px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
        overflow: hidden;
        transition: all 0.25s ease;
    }
    .py-dept-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 8px 24px rgba(37, 99, 235, 0.07);
    }
    .py-dept-hdr {
        background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
        color: #ffffff;
        padding: 16px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
    }
    .py-dept-title-group {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .py-dept-title {
        font-size: 16px;
        font-weight: 800;
        letter-spacing: 0.2px;
        margin: 0;
        text-transform: uppercase;
    }
    .py-dept-cycle-badge {
        font-size: 12px;
        font-weight: 600;
        background: rgba(255, 255, 255, 0.18);
        padding: 4px 12px;
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.25);
    }
    .py-dept-stats-group {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .py-dept-pill {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(4px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 11.5px;
        font-weight: 700;
    }

    .py-dept-body {
        padding: 20px 24px;
    }

    /* Modern Table */
    .py-table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #ffffff;
    }
    .py-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 13px;
        min-width: 950px;
    }
    .py-table th {
        background: #f8fafc;
        color: #1e40af;
        font-weight: 700;
        font-size: 11.5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 13px 14px;
        border-bottom: 2px solid #e2e8f0;
        white-space: nowrap;
        vertical-align: middle;
        text-align: center;
    }
    .py-table th.th-left {
        text-align: left;
    }
    .py-table td {
        padding: 12px 14px;
        color: #1e293b;
        border-bottom: 1px solid #f1f5f9;
        white-space: nowrap;
        vertical-align: middle;
        text-align: center;
    }
    .py-table td.td-left {
        text-align: left;
    }
    .py-table tr:hover td {
        background-color: #f0f9ff;
    }
    .py-table tr:last-child td {
        border-bottom: none;
    }

    /* Staff Avatar & Name */
    .py-staff-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .py-staff-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        color: #1d4ed8;
        font-weight: 800;
        font-size: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border: 2px solid #ffffff;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08);
    }
    .py-staff-name {
        font-weight: 700;
        color: #0f172a;
        font-size: 13.5px;
    }
    .py-staff-code {
        font-size: 11.5px;
        color: #64748b;
        font-weight: 600;
    }

    /* Status Badges */
    .badge-present {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
        padding: 3px 8px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 12px;
        display: inline-block;
    }
    .badge-absent {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
        padding: 3px 8px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 12px;
        display: inline-block;
    }
    .badge-half-day {
        background: #cffafe;
        color: #0e7490;
        border: 1px solid #a5f3fc;
        padding: 3px 8px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 12px;
        display: inline-block;
    }
    .badge-paid-leave {
        background: #f3e8ff;
        color: #7e22ce;
        border: 1px solid #e9d5ff;
        padding: 3px 8px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 12px;
        display: inline-block;
    }
    .badge-unpaid-leave {
        background: #ffedd5;
        color: #c2410c;
        border: 1px solid #fed7aa;
        padding: 3px 8px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 12px;
        display: inline-block;
    }
    .badge-holiday {
        background: #fef9c3;
        color: #a16207;
        border: 1px solid #fef08a;
        padding: 3px 8px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 12px;
        display: inline-block;
    }
    .badge-week-off {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
        padding: 3px 8px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 12px;
        display: inline-block;
    }
    .badge-not-marked {
        background: #f8fafc;
        color: #94a3b8;
        border: 1px solid #e2e8f0;
        padding: 3px 8px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 12px;
        display: inline-block;
    }

    .badge-pct {
        padding: 4px 10px;
        border-radius: 20px;
        font-weight: 800;
        font-size: 12px;
        display: inline-block;
    }
    .pct-high {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #86efac;
    }
    .pct-mid {
        background: #e0f2fe;
        color: #0369a1;
        border: 1px solid #bae6fd;
    }
    .pct-warn {
        background: #fef3c7;
        color: #b45309;
        border: 1px solid #fde68a;
    }
    .pct-low {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fca5a5;
    }

    /* Actions */
    .py-action-group {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }
    .py-action-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid transparent;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 12.5px;
        background: transparent;
    }
    .btn-action-attendance {
        background: #eff6ff;
        color: #2563eb;
        border-color: #bfdbfe;
    }
    .btn-action-attendance:hover {
        background: #2563eb;
        color: #ffffff;
        transform: scale(1.08);
    }
    .btn-action-leave {
        background: #f5f3ff;
        color: #7c3aed;
        border-color: #ddd6fe;
    }
    .btn-action-leave:hover {
        background: #7c3aed;
        color: #ffffff;
        transform: scale(1.08);
    }
    .btn-action-salary {
        background: #ecfdf5;
        color: #059669;
        border-color: #a7f3d0;
    }
    .btn-action-salary:hover {
        background: #059669;
        color: #ffffff;
        transform: scale(1.08);
    }

    .py-dept-footer {
        margin-top: 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        padding-top: 12px;
        border-top: 1px solid #f1f5f9;
    }
    .btn-recalc-dept {
        background: #fff7ed;
        color: #ea580c;
        border: 1.5px solid #fed7aa;
        padding: 7px 16px;
        border-radius: 8px;
        font-size: 12.5px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
    }
    .btn-recalc-dept:hover {
        background: #ea580c;
        color: #ffffff;
        border-color: #ea580c;
    }

    .btn-verified-pill {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }    /* Premium Self-Contained Modal Overlay System */
    .py-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(15, 23, 42, 0.7);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        z-index: 99999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        box-sizing: border-box;
        opacity: 0;
        transition: opacity 0.25s ease;
    }
    .py-modal-overlay.open {
        display: flex !important;
        opacity: 1 !important;
    }
    .py-modal-box {
        background: #ffffff;
        border-radius: 20px;
        width: 100%;
        max-width: 840px;
        max-height: 90vh;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        transform: scale(0.95) translateY(12px);
        transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .py-modal-overlay.open .py-modal-box {
        transform: scale(1) translateY(0);
    }
    .py-modal-hdr {
        padding: 18px 24px;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0;
    }
    .py-modal-hdr-title {
        font-weight: 800;
        font-size: 18px;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .py-modal-hdr-sub {
        font-size: 12px;
        font-weight: 600;
        margin-top: 3px;
        opacity: 0.9;
    }
    .py-modal-close-btn {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: #ffffff;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .py-modal-close-btn:hover {
        background: rgba(255, 255, 255, 0.35);
        transform: rotate(90deg);
    }
    .py-modal-body {
        padding: 22px 26px;
        background: #f8fafc;
        overflow-y: auto;
        flex: 1;
    }
    .py-modal-footer {
        padding: 14px 24px;
        background: #ffffff;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 10px;
        flex-shrink: 0;
    }

    /* Modal Subsections & Component Styling */
    .py-modal-section-title {
        font-size: 13.5px;
        font-weight: 800;
        color: #1e293b;
        margin: 18px 0 10px 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .py-modal-section-title:first-child {
        margin-top: 0;
    }

    /* Leave Balances Grid */
    .py-leave-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 12px;
        margin-bottom: 20px;
    }
    .py-leave-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 14px 12px;
        text-align: center;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.02);
        transition: all 0.2s ease;
    }
    .py-leave-card:hover {
        border-color: #c4b5fd;
        box-shadow: 0 6px 16px rgba(124, 58, 237, 0.08);
        transform: translateY(-2px);
    }
    .py-leave-pill-code {
        display: inline-block;
        font-size: 10.5px;
        font-weight: 800;
        color: #7c3aed;
        background: #f5f3ff;
        border: 1px solid #ddd6fe;
        padding: 3px 8px;
        border-radius: 6px;
        margin-bottom: 8px;
        text-transform: uppercase;
    }
    .py-leave-count {
        font-size: 22px;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 4px;
        line-height: 1.2;
    }
    .py-leave-meta {
        font-size: 11px;
        font-weight: 600;
        color: #64748b;
    }

    /* Configured Salary Structure Box */
    .py-salary-box {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px;
        margin-bottom: 20px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.02);
    }
    .py-salary-box-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 12px;
        margin-bottom: 12px;
        border-bottom: 1px solid #f1f5f9;
        flex-wrap: wrap;
        gap: 8px;
    }
    .py-salary-box-title {
        font-size: 13.5px;
        font-weight: 800;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .py-salary-net-badge {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #86efac;
        font-size: 13px;
        font-weight: 800;
        padding: 4px 12px;
        border-radius: 20px;
    }
    .py-salary-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
    }
    @media (max-width: 768px) {
        .py-salary-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    .py-salary-pill {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 10px 8px;
        text-align: center;
    }
    .py-salary-pill-label {
        font-size: 10px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        margin-bottom: 4px;
    }
    .py-salary-pill-val {
        font-size: 15.5px;
        font-weight: 800;
        color: #0f172a;
    }
    .py-salary-pill-val.is-ded {
        color: #dc2626;
    }

    /* Modal Table Container */
    .py-modal-table-wrap {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        overflow: hidden;
        max-height: 320px;
        overflow-y: auto;
    }

    /* Status Badges */
    .badge-status-approved {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #86efac;
        padding: 3px 8px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 11px;
        display: inline-block;
    }
    .badge-status-rejected {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fca5a5;
        padding: 3px 8px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 11px;
        display: inline-block;
    }
    .badge-status-pending {
        background: #fef3c7;
        color: #b45309;
        border: 1px solid #fde68a;
        padding: 3px 8px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 11px;
        display: inline-block;
    }

    .modal-kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(110px, 1fr));
        gap: 12px;
        margin-bottom: 20px;
    }
    .modal-kpi-pill {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px;
        text-align: center;
        box-shadow: 0 2px 6px rgba(0,0,0,0.02);
    }
    .modal-kpi-pill-label {
        font-size: 10.5px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        margin-bottom: 2px;
    }
    .modal-kpi-pill-val {
        font-size: 18px;
        font-weight: 800;
        color: #0f172a;
    }

    /* Toast Notification */
    #pyToast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        z-index: 999999;
        min-width: 320px;
        background: #0f172a;
        color: #ffffff;
        padding: 14px 20px;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.25);
        display: none;
        align-items: center;
        gap: 12px;
        font-size: 13.5px;
        font-weight: 600;
        animation: toastSlideUp 0.3s ease forwards;
    }
    @keyframes toastSlideUp {
        from { transform: translateY(30px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
</style>
@endsection

@section('content')
<div class="py-att-container">

    <!-- Top Banner -->
    <div class="py-att-banner">
        <div>
            <div class="py-att-banner-title">
                <i class="fas fa-clipboard-check"></i>
                Payroll Attendance Verification
            </div>
            <p class="py-att-banner-sub">
                Pre-payroll verification stage. Review and confirm verified employee attendance from <strong>Staff Attendance</strong> and approved leaves from <strong>Leave Management</strong> before salary generation.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <div class="py-att-workflow-badge">
                <i class="fas fa-arrows-split-up-and-left"></i>
                Workflow: Salary Structure &rarr; <strong>Attendance Verification</strong> &rarr; Generate Payroll
            </div>
            <a href="{{ route('school.payroll.generate-payroll', ['salary_month' => $selectedMonth, 'salary_year' => $selectedYear]) }}" class="py-btn-primary" style="background:#ffffff !important; color:#1e40af !important; border:none;">
                <i class="fas fa-calculator"></i> Proceed to Generate Payroll &rarr;
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" role="alert" style="border-radius: 12px; font-weight: 600;">
            <i class="fas fa-circle-check fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2" role="alert" style="border-radius: 12px; font-weight: 600;">
            <i class="fas fa-circle-exclamation fs-5"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- KPI Summary Grid -->
    <div class="py-kpi-grid">
        <div class="py-kpi-card">
            <div class="py-kpi-icon" style="background:#eff6ff; color:#2563eb;">
                <i class="fas fa-users"></i>
            </div>
            <div class="py-kpi-info">
                <div class="py-kpi-label">Total Staff</div>
                <div class="py-kpi-val" id="kpi-total-staff">{{ $attendanceData['global_kpi']['total_employees'] }}</div>
                <div class="py-kpi-sub">{{ $attendanceData['global_kpi']['total_departments'] }} Departments</div>
            </div>
        </div>
        <div class="py-kpi-card">
            <div class="py-kpi-icon" style="background:#dcfce7; color:#16a34a;">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="py-kpi-info">
                <div class="py-kpi-label">Active / Present</div>
                <div class="py-kpi-val" id="kpi-present-staff">{{ $attendanceData['global_kpi']['present_employees'] }}</div>
                <div class="py-kpi-sub">Regular Attendance</div>
            </div>
        </div>
        <div class="py-kpi-card">
            <div class="py-kpi-icon" style="background:#f3e8ff; color:#9333ea;">
                <i class="fas fa-calendar-minus"></i>
            </div>
            <div class="py-kpi-info">
                <div class="py-kpi-label">Staff On Leave</div>
                <div class="py-kpi-val" id="kpi-leave-staff">{{ $attendanceData['global_kpi']['employees_on_leave'] }}</div>
                <div class="py-kpi-sub">Approved Leaves</div>
            </div>
        </div>
        <div class="py-kpi-card">
            <div class="py-kpi-icon" style="background:#fee2e2; color:#dc2626;">
                <i class="fas fa-user-xmark"></i>
            </div>
            <div class="py-kpi-info">
                <div class="py-kpi-label">Absences</div>
                <div class="py-kpi-val" id="kpi-absent-staff">{{ $attendanceData['global_kpi']['employees_absent'] }}</div>
                <div class="py-kpi-sub">Recorded Absents</div>
            </div>
        </div>
        <div class="py-kpi-card">
            <div class="py-kpi-icon" style="background:#e0f2fe; color:#0284c7;">
                <i class="fas fa-chart-pie"></i>
            </div>
            <div class="py-kpi-info">
                <div class="py-kpi-label">Avg Attendance</div>
                <div class="py-kpi-val" id="kpi-avg-attendance">{{ $attendanceData['global_kpi']['average_attendance_pct'] }}%</div>
                <div class="py-kpi-sub">{{ $attendanceData['date_range_display'] }}</div>
            </div>
        </div>
        <div class="py-kpi-card">
            <div class="py-kpi-icon" style="background:#fef3c7; color:#d97706;">
                <i class="fas fa-shield-halved"></i>
            </div>
            <div class="py-kpi-info">
                <div class="py-kpi-label">Cycle Status</div>
                <div class="py-kpi-val" style="font-size:16px; color:#15803d; margin-top:4px;">
                    <i class="fas fa-circle-check"></i> Ready
                </div>
                <div class="py-kpi-sub">{{ $attendanceData['payroll_month'] }}</div>
            </div>
        </div>
    </div>

    <!-- Verification Progress Bar -->
    <div class="py-progress-card">
        <div class="d-flex align-items-center gap-3">
            <div style="font-weight: 800; font-size: 13.5px; color: #1e293b;">
                <i class="fas fa-list-check text-primary me-1"></i> Attendance Verification Progress:
            </div>
            <span class="badge bg-primary" style="border-radius:8px; font-weight:700; font-size:11.5px;">
                {{ $attendanceData['all_staff_count'] }} Employees in {{ count($attendanceData['department_cards']) }} Departments
            </span>
        </div>
        <div class="py-progress-bar-wrap">
            <div class="py-progress-bar-fill" style="width: 100%;"></div>
        </div>
        <div class="d-flex align-items-center gap-2" style="font-weight:700; font-size:12.5px; color:#15803d;">
            <i class="fas fa-circle-check"></i> 100% Calculated & Verified
        </div>
    </div>

    <!-- Filter Section -->
    <div class="py-filter-card">
        <form method="GET" action="{{ route('school.payroll.payroll-attendance') }}" id="payrollAttendanceFilterForm" class="py-filter-form">
            <!-- Payroll Month Selector -->
            <div class="py-form-group">
                <label class="py-form-label"><i class="fas fa-calendar-days text-primary me-1"></i> Payroll Month</label>
                <select name="salary_month" class="py-select" id="filterSalaryMonth" onchange="document.getElementById('payrollAttendanceFilterForm').submit()">
                    @foreach($months as $m)
                        <option value="{{ $m }}" {{ $selectedMonth === $m ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Year Dropdown -->
            <div class="py-form-group" style="max-width: 140px;">
                <label class="py-form-label"><i class="fas fa-calendar text-primary me-1"></i> Year</label>
                <select name="salary_year" class="py-select" id="filterSalaryYear" onchange="document.getElementById('payrollAttendanceFilterForm').submit()">
                    @foreach($years as $y)
                        <option value="{{ $y }}" {{ (int)$selectedYear === (int)$y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Department Filter -->
            <div class="py-form-group">
                <label class="py-form-label"><i class="fas fa-building text-primary me-1"></i> Department Filter</label>
                <select name="department_id" class="py-select" id="filterDepartment" onchange="document.getElementById('payrollAttendanceFilterForm').submit()">
                    <option value="All" {{ $selectedDeptId === 'All' ? 'selected' : '' }}>All Departments ({{ count($departments) }})</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ (string)$selectedDeptId === (string)$dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Employee Search -->
            <div class="py-form-group" style="flex: 1.5; min-width: 220px;">
                <label class="py-form-label"><i class="fas fa-magnifying-glass text-primary me-1"></i> Search Employee</label>
                <input type="text" name="search" class="py-input" id="staffLiveSearchInput" value="{{ $search }}" placeholder="Search by name or Employee ID..." onkeyup="filterStaffTableClientSide()">
            </div>

            <!-- Action Buttons -->
            <div class="d-flex align-items-center gap-2" style="margin-bottom: 2px;">
                <button type="submit" class="py-btn-primary">
                    <i class="fas fa-filter"></i> Get Attendance
                </button>

                <button type="button" class="py-btn-outline" onclick="triggerRecalculateAll()" id="btnRecalculateAll" title="Recalculate live attendance and leave for all staff">
                    <i class="fas fa-arrows-rotate"></i> Recalculate All
                </button>

                <button type="button" class="py-btn-outline" style="border-color:#3b82f6 !important; color:#1d4ed8 !important; background:#eff6ff !important;" onclick="openPayrollSettingsModal()" title="Configure School Payroll Deduction Settings">
                    <i class="fas fa-gear text-primary me-1"></i> Payroll Settings
                </button>

                <a href="{{ route('school.payroll.payroll-attendance.export-excel', request()->all()) }}" class="py-btn-excel" title="Download Excel Sheet">
                    <i class="fas fa-file-excel"></i> Excel
                </a>

                <a href="{{ route('school.payroll.payroll-attendance.export-pdf', request()->all()) }}" class="py-btn-pdf" title="Download PDF Report">
                    <i class="fas fa-file-pdf"></i> PDF
                </a>
            </div>
        </form>
    </div>

    <!-- Department Cards Section -->
    <div id="departmentCardsContainer">
        @forelse($attendanceData['department_cards'] as $deptCard)
            <div class="py-dept-card" id="dept-card-{{ $deptCard['id'] }}" data-dept-id="{{ $deptCard['id'] }}" data-dept-name="{{ strtolower($deptCard['name']) }}">
                <!-- Card Header -->
                <div class="py-dept-hdr">
                    <div class="py-dept-title-group">
                        <i class="fas fa-folder-open" style="font-size: 18px; opacity: 0.9;"></i>
                        <h4 class="py-dept-title">{{ $deptCard['name'] }}</h4>
                        <span class="py-dept-cycle-badge">
                            <i class="fas fa-calendar-day me-1"></i> {{ $attendanceData['date_range_display'] }}
                        </span>
                    </div>
                    <div class="py-dept-stats-group">
                        <span class="py-dept-pill">
                            <i class="fas fa-user-group me-1"></i> Total Staff: <strong>{{ $deptCard['total_staff'] }}</strong>
                        </span>
                        <span class="py-dept-pill" style="background: rgba(34, 197, 94, 0.25); border-color: rgba(34, 197, 94, 0.4);">
                            <i class="fas fa-check-circle me-1"></i> Present: <strong>{{ $deptCard['total_present_days'] }}</strong>
                        </span>
                        <span class="py-dept-pill" style="background: rgba(239, 68, 68, 0.25); border-color: rgba(239, 68, 68, 0.4);">
                            <i class="fas fa-times-circle me-1"></i> Absents: <strong>{{ $deptCard['total_absent_days'] }}</strong>
                        </span>
                        <span class="py-dept-pill" style="background: rgba(168, 85, 247, 0.25); border-color: rgba(168, 85, 247, 0.4);">
                            <i class="fas fa-calendar-minus me-1"></i> Leaves: <strong>{{ $deptCard['total_paid_leaves'] + $deptCard['total_unpaid_leaves'] }}</strong>
                        </span>
                        <span class="py-dept-pill" style="background: rgba(59, 130, 246, 0.3); border-color: rgba(59, 130, 246, 0.5);">
                            <i class="fas fa-percent me-1"></i> Avg: <strong>{{ $deptCard['avg_attendance_pct'] }}%</strong>
                        </span>
                    </div>
                </div>

                <!-- Card Body & Table -->
                <div class="py-dept-body">
                    <div class="py-table-responsive">
                        <table class="py-table">
                            <thead>
                                <tr>
                                    <th style="width: 45px;">#</th>
                                    <th class="th-left">Staff Name</th>
                                    <th>Employee ID</th>
                                    <th>Designation</th>
                                    <th title="Total marked Present & Late days">Present</th>
                                    <th title="Total unexcused or marked Absent days">Absent</th>
                                    <th title="School & Official Gazetted Holidays">Holidays</th>
                                    <th title="Half Day count (0.5 day credit)">Half Days</th>
                                    <th title="Paid approved leaves from Leave Management (CL, EL, SL, RH)">Leaves With Pay</th>
                                    <th title="Unpaid approved leaves from Leave Management (LWP)">Leaves W/O Pay</th>
                                    <th title="Standard working days in this month">Working Days</th>
                                    <th title="Attendance percentage including present, half days, paid leaves & holidays">Attendance %</th>
                                    <th style="width: 120px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="dept-tbody">
                                @forelse($deptCard['staff_rows'] as $idx => $row)
                                    @php
                                        $pct = $row['attendance_pct'];
                                        $pctClass = 'pct-high';
                                        if ($pct < 50) $pctClass = 'pct-low';
                                        elseif ($pct < 75) $pctClass = 'pct-warn';
                                        elseif ($pct < 90) $pctClass = 'pct-mid';
                                    @endphp
                                    <tr class="staff-row" data-name="{{ strtolower($row['name']) }}" data-empid="{{ strtolower($row['employee_id']) }}" data-staff-id="{{ $row['staff_id'] }}">
                                        <td>{{ $idx + 1 }}</td>
                                        <td class="td-left">
                                            <div class="py-staff-info">
                                                <div class="py-staff-avatar">
                                                    {{ strtoupper(substr($row['name'], 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="py-staff-name">{{ $row['name'] }}</div>
                                                    <div class="py-staff-code">{{ $row['employee_id'] }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span style="font-family: monospace; font-weight: 700; color: #475569;">
                                                {{ $row['employee_id'] }}
                                            </span>
                                        </td>
                                        <td>
                                            <span style="font-weight: 600; color: #334155;">{{ $row['designation'] }}</span>
                                        </td>
                                        <td>
                                            <span class="badge-present">{{ $row['present_days'] }}</span>
                                        </td>
                                        <td>
                                            <span class="badge-absent">{{ $row['absent_days'] }}</span>
                                        </td>
                                        <td>
                                            <span class="badge-holiday">{{ $row['holidays'] }}</span>
                                        </td>
                                        <td>
                                            <span class="badge-half-day">{{ $row['half_days'] }}</span>
                                        </td>
                                        <td>
                                            <span class="badge-paid-leave">{{ $row['paid_leaves'] }}</span>
                                        </td>
                                        <td>
                                            <span class="badge-unpaid-leave">{{ $row['unpaid_leaves'] }}</span>
                                        </td>
                                        <td>
                                            <span style="font-weight: 700; color: #1e293b;">{{ $row['working_days'] }} / {{ $row['total_days'] }}</span>
                                        </td>
                                        <td>
                                            <span class="badge-pct {{ $pctClass }}">{{ $row['attendance_pct'] }}%</span>
                                        </td>
                                        <td>
                                            <div class="py-action-group">
                                                <!-- Action 1: View Attendance Modal -->
                                                <button type="button" class="py-action-btn btn-action-attendance" onclick="openAttendanceModal({{ $row['staff_id'] }})" title="View Complete Attendance Register & Daily Breakdown">
                                                    <i class="fas fa-calendar-days"></i>
                                                </button>

                                                <!-- Action 2: View Leave Modal -->
                                                <button type="button" class="py-action-btn btn-action-leave" onclick="openLeaveModal({{ $row['staff_id'] }})" title="View Leave Balances & History">
                                                    <i class="fas fa-paper-plane"></i>
                                                </button>

                                                <!-- Action 3: View Previous Salary Modal -->
                                                <button type="button" class="py-action-btn btn-action-salary" onclick="openSalaryModal({{ $row['staff_id'] }})" title="View Salary Structure & Previous Payslips">
                                                    <i class="fas fa-briefcase"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="13" class="text-center py-4 text-muted">
                                            <i class="fas fa-user-slash me-2"></i> No active employees found in this department.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Department Card Footer -->
                    <div class="py-dept-footer">
                        <button type="button" class="btn-recalc-dept" onclick="recalculateDepartment({{ $deptCard['id'] }})" id="btn-recalc-{{ $deptCard['id'] }}">
                            <i class="fas fa-rotate"></i> Recalculate Attendance
                        </button>
                        <div class="d-flex align-items-center gap-2">
                            <span class="btn-verified-pill">
                                <i class="fas fa-circle-check text-success"></i> Attendance Verified
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="py-dept-card text-center p-5">
                <i class="fas fa-folder-open text-muted" style="font-size: 48px; margin-bottom: 14px;"></i>
                <h5 class="fw-bold text-dark">No Department Data Found</h5>
                <p class="text-muted mb-3">No active staff records matched your search or department filter criteria.</p>
                <a href="{{ route('school.payroll.payroll-attendance') }}" class="py-btn-primary">
                    <i class="fas fa-arrows-rotate"></i> Reset Filters
                </a>
            </div>
        @endforelse
    </div>

</div>

<!-- ========================================================================= -->
<!-- MODAL 1: VIEW ATTENDANCE DETAILS POPUP -->
<!-- ========================================================================= -->
<div class="py-modal-overlay" id="attendanceDetailModal" onclick="if(event.target === this) closeAnyModal()">
    <div class="py-modal-box">
        <div class="py-modal-hdr" style="background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);">
            <div>
                <div class="py-modal-hdr-title">
                    <i class="fas fa-calendar-check"></i>
                    <span id="attModalStaffName">Staff Attendance Details</span>
                </div>
                <div class="py-modal-hdr-sub" id="attModalStaffSub">Loading staff info...</div>
            </div>
            <button type="button" class="py-modal-close-btn" onclick="closeAnyModal()">&times;</button>
        </div>
        <div class="py-modal-body">
            <!-- Summary Mini Cards -->
            <div class="modal-kpi-grid">
                <div class="modal-kpi-pill" style="border-left: 4px solid #3b82f6;">
                    <div class="modal-kpi-pill-label">Total Days</div>
                    <div class="modal-kpi-pill-val" id="attKpiTotalDays">-</div>
                </div>
                <div class="modal-kpi-pill" style="border-left: 4px solid #16a34a;">
                    <div class="modal-kpi-pill-label">Present</div>
                    <div class="modal-kpi-pill-val text-success" id="attKpiPresent">-</div>
                </div>
                <div class="modal-kpi-pill" style="border-left: 4px solid #dc2626;">
                    <div class="modal-kpi-pill-label">Absent</div>
                    <div class="modal-kpi-pill-val text-danger" id="attKpiAbsent">-</div>
                </div>
                <div class="modal-kpi-pill" style="border-left: 4px solid #9333ea;">
                    <div class="modal-kpi-pill-label">Paid Leave</div>
                    <div class="modal-kpi-pill-val" style="color:#9333ea;" id="attKpiPaidLeave">-</div>
                </div>
                <div class="modal-kpi-pill" style="border-left: 4px solid #ea580c;">
                    <div class="modal-kpi-pill-label">Unpaid Leave</div>
                    <div class="modal-kpi-pill-val text-warning" id="attKpiUnpaidLeave">-</div>
                </div>
                <div class="modal-kpi-pill" style="border-left: 4px solid #0891b2;">
                    <div class="modal-kpi-pill-label">Half Day</div>
                    <div class="modal-kpi-pill-val" style="color:#0891b2;" id="attKpiHalfDay">-</div>
                </div>
                <div class="modal-kpi-pill" style="border-left: 4px solid #ca8a04;">
                    <div class="modal-kpi-pill-label">Holiday</div>
                    <div class="modal-kpi-pill-val" style="color:#ca8a04;" id="attKpiHoliday">-</div>
                </div>
                <div class="modal-kpi-pill" style="border-left: 4px solid #475569;">
                    <div class="modal-kpi-pill-label">Weekoff</div>
                    <div class="modal-kpi-pill-val text-secondary" id="attKpiWeekOff">-</div>
                </div>
            </div>

            <!-- Daily Breakdown Table -->
            <div class="py-modal-table-wrap">
                <table class="py-table">
                    <thead style="position: sticky; top: 0; z-index: 2;">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th class="th-left">Date & Day</th>
                            <th>Status</th>
                            <th class="th-left">Remarks / Punch Times</th>
                        </tr>
                    </thead>
                    <tbody id="attModalDailyTableBody">
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                <div class="spinner-border spinner-border-sm text-primary me-2"></div> Loading daily records...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="py-modal-footer">
            <button type="button" class="btn btn-secondary px-4 fw-bold" style="border-radius:8px;" onclick="closeAnyModal()">Close</button>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 2: VIEW LEAVE DETAILS POPUP -->
<!-- ========================================================================= -->
<div class="py-modal-overlay" id="leaveDetailModal" onclick="if(event.target === this) closeAnyModal()">
    <div class="py-modal-box">
        <div class="py-modal-hdr" style="background: linear-gradient(135deg, #7c3aed 0%, #9333ea 100%);">
            <div>
                <div class="py-modal-hdr-title">
                    <i class="fas fa-calendar-minus"></i>
                    <span id="leaveModalStaffName">Staff Leave Management Details</span>
                </div>
                <div class="py-modal-hdr-sub" id="leaveModalStaffSub">Leave Balances & History</div>
            </div>
            <button type="button" class="py-modal-close-btn" onclick="closeAnyModal()">&times;</button>
        </div>
        <div class="py-modal-body">
            <!-- Leave Balances Cards -->
            <div class="py-modal-section-title">
                <i class="fas fa-wallet text-primary"></i> Leave Balances (Current Session)
            </div>
            <div class="py-leave-grid" id="leaveBalancesContainer">
                <div class="text-center text-muted py-2" style="grid-column: 1 / -1;">Loading leave balances...</div>
            </div>

            <!-- Leave Applications History -->
            <div class="py-modal-section-title">
                <i class="fas fa-history text-primary"></i> Leave Applications History
            </div>
            <div class="py-modal-table-wrap">
                <table class="py-table">
                    <thead style="position: sticky; top: 0; z-index: 2;">
                        <tr>
                            <th class="th-left">Leave Type</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Days</th>
                            <th>Paid / Unpaid</th>
                            <th>Approval Status</th>
                            <th class="th-left">Reason & Admin Remarks</th>
                        </tr>
                    </thead>
                    <tbody id="leaveModalHistoryTableBody">
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <div class="spinner-border spinner-border-sm text-primary me-2"></div> Loading leave history...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="py-modal-footer">
            <button type="button" class="btn btn-secondary px-4 fw-bold" style="border-radius:8px;" onclick="closeAnyModal()">Close</button>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 3: VIEW SALARY HISTORY POPUP -->
<!-- ========================================================================= -->
<div class="py-modal-overlay" id="salaryDetailModal" onclick="if(event.target === this) closeAnyModal()">
    <div class="py-modal-box">
        <div class="py-modal-hdr" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
            <div>
                <div class="py-modal-hdr-title">
                    <i class="fas fa-wallet"></i>
                    <span id="salaryModalStaffName">Employee Salary & Payslip History</span>
                </div>
                <div class="py-modal-hdr-sub" id="salaryModalStaffSub">Salary Structure & Payment Records</div>
            </div>
            <button type="button" class="py-modal-close-btn" onclick="closeAnyModal()">&times;</button>
        </div>
        <div class="py-modal-body">
            <!-- Salary Structure Card -->
            <div class="py-salary-box">
                <div class="py-salary-box-top">
                    <div class="py-salary-box-title">
                        <i class="fas fa-receipt text-success"></i> Configured Salary Structure
                    </div>
                    <span class="py-salary-net-badge" id="salaryModalNetBadge">Net: ₹0.00</span>
                </div>
                <div class="py-salary-grid" id="salaryStructureBreakdown">
                    <!-- Injected by JS -->
                </div>
            </div>

            <!-- Salary History Table -->
            <div class="py-modal-section-title">
                <i class="fas fa-clock-rotate-left text-success"></i> Previous Months Salary & Payslip Records
            </div>
            <div class="py-modal-table-wrap">
                <table class="py-table">
                    <thead style="position: sticky; top: 0; z-index: 2;">
                        <tr>
                            <th class="th-left">Salary Month</th>
                            <th>Gross Salary</th>
                            <th>Deductions</th>
                            <th>Net Payable</th>
                            <th>Payment Date</th>
                            <th>Status</th>
                            <th>Payslip</th>
                        </tr>
                    </thead>
                    <tbody id="salaryModalHistoryTableBody">
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <div class="spinner-border spinner-border-sm text-success me-2"></div> Loading salary records...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="py-modal-footer">
            <button type="button" class="btn btn-secondary px-4 fw-bold" style="border-radius:8px;" onclick="closeAnyModal()">Close</button>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 4: PAYROLL DEDUCTION SETTINGS -->
<!-- ========================================================================= -->
<!-- ========================================================================= -->
<!-- MODAL 4: PAYROLL DEDUCTION SETTINGS -->
<!-- ========================================================================= -->
<div class="py-modal-overlay" id="payrollSettingsModal" tabindex="-1">
    <div class="py-modal-box" style="max-width: 620px; max-height: calc(100vh - 40px); border-radius: 20px; display: flex; flex-direction: column; overflow: hidden;">
        <!-- Header -->
        <div class="py-modal-hdr" style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); padding: 20px 24px; position: relative; flex-shrink: 0;">
            <div>
                <h5 class="py-modal-hdr-title" style="color: #ffffff; margin: 0; font-size: 18px; font-weight: 800; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-sliders text-warning fs-5"></i> Payroll Deduction Settings
                </h5>
                <div class="py-modal-hdr-sub" style="color: #dbeafe; font-size: 12px; margin-top: 4px; font-weight: 500;">
                    Configure school-wise salary deduction rules for leaves exceeding paid leave balances.
                </div>
            </div>
            <button type="button" class="py-modal-close-btn" onclick="closeModalById('payrollSettingsModal')" style="position: absolute; right: 18px; top: 18px;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="payrollDeductionSettingsForm" onsubmit="savePayrollDeductionSettings(event)" style="display: flex; flex-direction: column; flex: 1; min-height: 0; overflow: hidden; margin: 0;">
            @csrf
            <div class="py-modal-body" style="padding: 20px; background: #f8fafc; overflow-y: auto; flex: 1; min-height: 0; display: flex; flex-direction: column; gap: 16px;">

                <!-- Card 1: Salary Calculation Base -->
                <div style="background: linear-gradient(135deg, #eff6ff 0%, #e0f2fe 100%); border: 1.5px solid #bfdbfe; border-radius: 14px; padding: 14px 18px; display: flex; align-items: center; justify-content: space-between; gap: 14px; box-shadow: 0 2px 6px rgba(37, 99, 235, 0.04);">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 40px; height: 40px; border-radius: 10px; background: #ffffff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 17px; box-shadow: 0 2px 5px rgba(0,0,0,0.06); flex-shrink: 0;">
                            <i class="fas fa-calculator"></i>
                        </div>
                        <div>
                            <div style="font-size: 13.5px; font-weight: 800; color: #1e3a8a;">
                                Salary Calculation Base
                            </div>
                            <div style="font-size: 11.5px; color: #2563eb; margin-top: 1px; font-weight: 600;">
                                Standard Daily Salary = Monthly Salary &divide; 30 Days (Fixed Cycle)
                            </div>
                        </div>
                    </div>
                    <span class="badge" style="background: #1e3a8a; color: #ffffff; font-size: 10.5px; font-weight: 800; padding: 6px 12px; border-radius: 20px; letter-spacing: 0.5px; white-space: nowrap;">
                        FIXED 30 DAYS
                    </span>
                </div>

                <!-- Card 2: Deduction Rule & Multiplier -->
                <div style="background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 14px; padding: 18px; display: flex; flex-direction: column; gap: 14px; box-shadow: 0 3px 12px rgba(0,0,0,0.02);">
                    <div style="font-size: 13.5px; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px; padding-bottom: 8px; border-bottom: 1px solid #f1f5f9;">
                        <i class="fas fa-percent text-primary"></i> Deduction Rule & Multiplier
                    </div>

                    <!-- Responsive 2 Column Grid -->
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 14px;">
                        <div style="display: flex; flex-direction: column; gap: 5px;">
                            <label style="font-size: 11.5px; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">Deduction Rule</label>
                            <div style="position: relative;">
                                <select name="deduction_rule" id="settingDeductionRule" style="width: 100%; padding: 10px 32px 10px 12px; border: 1.5px solid #cbd5e1; border-radius: 10px; font-size: 13px; font-weight: 700; color: #1e293b; background: #ffffff; appearance: none; -webkit-appearance: none; outline: none; cursor: pointer; transition: all 0.2s;" onchange="handleRuleChange()">
                                    <option value="one_day">Deduct 1 Day Salary (Default - 1.0x)</option>
                                    <option value="half_day">Deduct Half Day Salary (0.5x)</option>
                                    <option value="one_and_half_day">Deduct 1.5 Days Salary (1.5x)</option>
                                    <option value="two_days">Deduct 2 Days Salary (2.0x)</option>
                                    <option value="custom">Custom Multiplier</option>
                                </select>
                                <i class="fas fa-chevron-down" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #64748b; pointer-events: none; font-size: 11px;"></i>
                            </div>
                        </div>

                        <div style="display: flex; flex-direction: column; gap: 5px;">
                            <label style="font-size: 11.5px; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">Deduction Multiplier</label>
                            <div style="position: relative;">
                                <input type="number" step="0.1" min="0" max="10" name="deduction_multiplier" id="settingDeductionMultiplier" value="1.0" readonly style="width: 100%; padding: 10px 48px 10px 12px; border: 1.5px solid #cbd5e1; border-radius: 10px; font-size: 13.5px; font-weight: 800; color: #0f172a; background: #f8fafc; outline: none; transition: all 0.2s; box-sizing: border-box;" oninput="updateLiveExample()">
                                <span id="multiplierSuffixBadge" style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: #e0f2fe; color: #0284c7; font-size: 11px; font-weight: 800; padding: 2px 7px; border-radius: 5px;">
                                    1.0x
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Live Calculation Formula Example Box -->
                    <div id="liveFormulaBox" style="background: #f8fafc; border: 1.5px dashed #cbd5e1; border-radius: 10px; padding: 12px 14px; font-size: 12px; color: #334155; line-height: 1.5;">
                        <div style="font-weight: 800; color: #0f172a; margin-bottom: 3px; display: flex; align-items: center; gap: 5px;">
                            <i class="fas fa-lightbulb text-warning me-1"></i> Live Formula Preview:
                        </div>
                        <div style="color: #64748b; font-size: 11.5px;">
                            Employee Monthly Salary: <strong>₹15,000</strong> &rarr; Daily Salary: <strong>₹500</strong> (₹15,000 &divide; 30)
                        </div>
                        <div style="margin-top: 3px; font-weight: 700; color: #1e293b;">
                            If 1 Extra Unpaid Leave Day taken &rarr; Attendance Deduction: <span id="formulaDeductionText" style="color: #dc2626; font-size: 13px; font-weight: 800;">1 &times; ₹500 &times; 1.0 = ₹500.00</span>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Effective Date & Active Switch -->
                <div style="background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 14px; padding: 16px 18px; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 14px; align-items: center; box-shadow: 0 3px 12px rgba(0,0,0,0.02);">
                    <div style="display: flex; flex-direction: column; gap: 5px;">
                        <label style="font-size: 11.5px; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; display: flex; align-items: center; gap: 6px;">
                            <i class="fas fa-calendar-check text-primary"></i> Effective From Date
                        </label>
                        <input type="date" name="effective_from" id="settingEffectiveFrom" style="width: 100%; padding: 9px 12px; border: 1.5px solid #cbd5e1; border-radius: 10px; font-size: 13px; font-weight: 700; color: #1e293b; background: #ffffff; outline: none; box-sizing: border-box;" required>
                        <div style="font-size: 10.5px; color: #64748b; font-weight: 500; margin-top: 1px;">Applies to future payroll generations.</div>
                    </div>

                    <div style="background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 10px 14px; display: flex; align-items: center; justify-content: space-between; gap: 10px;">
                        <div>
                            <div style="font-size: 12.5px; font-weight: 800; color: #0f172a;">Active Status</div>
                            <div style="font-size: 10.5px; color: #64748b; font-weight: 500;">Enable/disable rule</div>
                        </div>
                        <div class="form-check form-switch m-0" style="padding-left: 2.4em;">
                            <input class="form-check-input" type="checkbox" role="switch" name="is_active" id="settingIsActive" value="1" checked style="width: 2.4em; height: 1.3em; cursor: pointer;">
                        </div>
                    </div>
                </div>

            </div>

            <!-- Footer (Pinned at bottom, flex-shrink: 0) -->
            <div class="py-modal-footer" style="padding: 14px 24px; background: #ffffff; border-top: 1px solid #e2e8f0; border-radius: 0 0 20px 20px; display: flex; align-items: center; justify-content: flex-end; gap: 10px; flex-shrink: 0;">
                <button type="button" class="btn btn-light" style="border-radius: 10px; font-weight: 700; font-size: 13px; padding: 9px 18px; border: 1px solid #cbd5e1;" onclick="closeModalById('payrollSettingsModal')">Cancel</button>
                <button type="submit" id="btnSavePayrollSettings" class="py-btn-primary" style="border-radius: 10px; padding: 9px 22px; font-size: 13px; font-weight: 800; background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);">
                    <i class="fas fa-floppy-disk me-1"></i> Save Settings
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Floating Toast Notification -->
<div id="pyToast">
    <i class="fas fa-circle-check text-success fs-5"></i>
    <span id="pyToastMessage">Operation successful</span>
</div>

@endsection

@section('scripts')
<script>
    const CSRF_TOKEN = '{{ csrf_token() }}';
    const SELECTED_MONTH = '{{ $selectedMonth }}';
    const SELECTED_YEAR = '{{ $selectedYear }}';

    // =========================================================================
    // MODAL 4: PAYROLL DEDUCTION SETTINGS JS
    // =========================================================================
    function openPayrollSettingsModal() {
        openModalById('payrollSettingsModal');

        fetch("{{ route('school.payroll.deduction-settings') }}", {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(res => {
            if (res.success && res.data) {
                const d = res.data;
                document.getElementById('settingDeductionRule').value = d.deduction_rule || 'one_day';
                document.getElementById('settingDeductionMultiplier').value = d.deduction_multiplier !== undefined ? d.deduction_multiplier : 1.0;
                document.getElementById('settingEffectiveFrom').value = d.effective_from || new Date().toISOString().split('T')[0];
                document.getElementById('settingIsActive').checked = d.is_active !== false;

                handleRuleChange();
            }
        })
        .catch(err => {
            showToast('Error loading deduction settings.', true);
        });
    }

    function handleRuleChange() {
        const rule = document.getElementById('settingDeductionRule').value;
        const multInput = document.getElementById('settingDeductionMultiplier');

        const multipliers = {
            'half_day': 0.5,
            'one_day': 1.0,
            'one_and_half_day': 1.5,
            'two_days': 2.0
        };

        if (rule === 'custom') {
            multInput.removeAttribute('readonly');
            multInput.style.background = '#ffffff';
            multInput.focus();
        } else {
            multInput.setAttribute('readonly', 'readonly');
            multInput.style.background = '#f8fafc';
            multInput.value = multipliers[rule] || 1.0;
        }

        updateLiveExample();
    }

    function updateLiveExample() {
        const multVal = parseFloat(document.getElementById('settingDeductionMultiplier').value);
        const mult = isNaN(multVal) ? 0 : multVal;

        const badge = document.getElementById('multiplierSuffixBadge');
        if (badge) {
            badge.textContent = mult + 'x';
        }

        const exampleDeduction = (500 * mult).toFixed(2);
        const formulaEl = document.getElementById('formulaDeductionText');
        if (formulaEl) {
            formulaEl.innerHTML = `1 &times; ₹500 &times; ${mult} = ₹${exampleDeduction}`;
        }
    }

    function savePayrollDeductionSettings(e) {
        e.preventDefault();
        const btn = document.getElementById('btnSavePayrollSettings');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Saving...';

        const form = document.getElementById('payrollDeductionSettingsForm');
        const formData = new FormData(form);

        fetch("{{ route('school.payroll.deduction-settings.store') }}", {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            body: formData
        })
        .then(async res => {
            const data = await res.json().catch(() => ({}));
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-floppy-disk me-1"></i> Save Settings';

            if (res.ok && data.success) {
                closeModalById('payrollSettingsModal');
                showToast(data.message || 'Settings saved successfully!');
                triggerRecalculateAll();
            } else {
                const errMsg = data.message || (data.errors ? Object.values(data.errors).flat().join(' ') : 'Error saving settings.');
                showToast(errMsg, true);
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-floppy-disk me-1"></i> Save Settings';
            showToast('An unexpected error occurred while saving settings.', true);
        });
    }

    // Show Toast Notification
    function showToast(message, isError = false) {
        const toast = document.getElementById('pyToast');
        if (!toast) return;
        const msgEl = document.getElementById('pyToastMessage');
        if (msgEl) msgEl.textContent = message;
        toast.style.display = 'flex';
        toast.style.background = isError ? '#991b1b' : '#0f172a';
        setTimeout(() => {
            toast.style.display = 'none';
        }, 4000);
    }

    // Modal overlay controllers
    function openModalById(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.add('open');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeModalById(id) {
        if (id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.remove('open');
            }
        }
        if (document.querySelectorAll('.py-modal-overlay.open').length === 0) {
            document.body.style.overflow = '';
        }
    }

    function closeAnyModal() {
        document.querySelectorAll('.py-modal-overlay.open').forEach(m => {
            m.classList.remove('open');
        });
        document.body.style.overflow = '';
    }

    function triggerRecalculateAll() {
        const btn = document.getElementById('btnRecalculateAll');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Recalculating...';
        }
        setTimeout(() => {
            window.location.reload();
        }, 500);
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAnyModal();
        }
    });

    // Client-side quick filter
    function filterStaffTableClientSide() {
        const q = document.getElementById('staffLiveSearchInput').value.toLowerCase().trim();
        const rows = document.querySelectorAll('.staff-row');
        rows.forEach(row => {
            const name = row.getAttribute('data-name') || '';
            const empId = row.getAttribute('data-empid') || '';
            if (name.includes(q) || empId.includes(q)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // Recalculate Specific Department
    function recalculateDepartment(deptId) {
        const btn = document.getElementById('btn-recalc-' + deptId);
        if (btn) {
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Recalculating...';
            btn.disabled = true;
        }

        fetch('{{ route("school.payroll.payroll-attendance.recalculate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                department_id: deptId,
                salary_month: SELECTED_MONTH,
                salary_year: SELECTED_YEAR
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast(data.message || 'Department attendance recalculated successfully.');
                setTimeout(() => {
                    window.location.reload();
                }, 800);
            } else {
                showToast(data.message || 'Recalculation error.', true);
                if (btn) {
                    btn.innerHTML = '<i class="fas fa-rotate"></i> Recalculate Attendance';
                    btn.disabled = false;
                }
            }
        })
        .catch(err => {
            showToast('Network error during recalculation.', true);
            if (btn) {
                btn.innerHTML = '<i class="fas fa-rotate"></i> Recalculate Attendance';
                btn.disabled = false;
            }
        });
    }

    // Recalculate All Departments
    function triggerRecalculateAll() {
        const btn = document.getElementById('btnRecalculateAll');
        if (btn) {
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Recalculating All...';
            btn.disabled = true;
        }

        fetch('{{ route("school.payroll.payroll-attendance.recalculate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                department_id: 'All',
                salary_month: SELECTED_MONTH,
                salary_year: SELECTED_YEAR
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast(data.message || 'All departments recalculated successfully.');
                setTimeout(() => {
                    window.location.reload();
                }, 800);
            } else {
                showToast(data.message || 'Recalculation error.', true);
                if (btn) {
                    btn.innerHTML = '<i class="fas fa-arrows-rotate"></i> Recalculate All';
                    btn.disabled = false;
                }
            }
        })
        .catch(err => {
            showToast('Failed to recalculate attendance.', true);
            if (btn) {
                btn.innerHTML = '<i class="fas fa-arrows-rotate"></i> Recalculate All';
                btn.disabled = false;
            }
        });
    }

    // =========================================================================
    // MODAL 1: VIEW ATTENDANCE
    // =========================================================================
    function openAttendanceModal(staffId) {
        openModalById('attendanceDetailModal');

        document.getElementById('attModalStaffName').textContent = 'Staff Attendance Details';
        document.getElementById('attModalStaffSub').textContent = 'Loading staff records...';
        document.getElementById('attModalDailyTableBody').innerHTML = `
            <tr><td colspan="4" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm text-primary me-2"></div> Loading attendance records...</td></tr>
        `;

        fetch(`{{ route('school.payroll.payroll-attendance.modal-attendance') }}?staff_id=${staffId}&salary_month=${SELECTED_MONTH}&salary_year=${SELECTED_YEAR}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(res => {
            if (!res.success) {
                showToast(res.message || 'Could not load attendance details.', true);
                return;
            }

            document.getElementById('attModalStaffName').textContent = res.staff.name + ' (' + res.staff.employee_id + ')';
            document.getElementById('attModalStaffSub').textContent = `${res.staff.designation} • ${res.staff.department} | ${res.month_info.formatted} (${res.month_info.date_range})`;

            // KPIs
            document.getElementById('attKpiTotalDays').textContent = res.summary.total_days;
            document.getElementById('attKpiPresent').textContent = res.summary.present_days;
            document.getElementById('attKpiAbsent').textContent = res.summary.absent_days;
            document.getElementById('attKpiPaidLeave').textContent = res.summary.paid_leaves;
            document.getElementById('attKpiUnpaidLeave').textContent = res.summary.unpaid_leaves;
            document.getElementById('attKpiHalfDay').textContent = res.summary.half_days;
            document.getElementById('attKpiHoliday').textContent = res.summary.holidays;
            document.getElementById('attKpiWeekOff').textContent = res.summary.week_offs;

            // Daily Records Table
            let rowsHtml = '';
            if (res.daily_records && res.daily_records.length > 0) {
                res.daily_records.forEach((d, idx) => {
                    rowsHtml += `
                        <tr>
                            <td>${d.day_num}</td>
                            <td class="td-left">
                                <strong>${d.date}</strong> <span class="text-muted">(${d.day_name})</span>
                            </td>
                            <td>
                                <span class="${d.badge_class}">${d.status_label}</span>
                            </td>
                            <td class="td-left text-muted" style="font-size:12px;">
                                ${d.remarks || '—'}
                            </td>
                        </tr>
                    `;
                });
            } else {
                rowsHtml = '<tr><td colspan="4" class="text-center py-3 text-muted">No attendance days calculated.</td></tr>';
            }
            document.getElementById('attModalDailyTableBody').innerHTML = rowsHtml;
        })
        .catch(err => {
            showToast('Failed to load attendance details.', true);
        });
    }

    // =========================================================================
    // MODAL 2: VIEW LEAVE DETAILS
    // =========================================================================
    function openLeaveModal(staffId) {
        openModalById('leaveDetailModal');

        document.getElementById('leaveModalStaffName').textContent = 'Staff Leave Management Details';
        document.getElementById('leaveModalStaffSub').textContent = 'Loading leave balances...';
        document.getElementById('leaveBalancesContainer').innerHTML = '<div class="text-center text-muted py-2" style="grid-column: 1 / -1;">Loading leave balances...</div>';
        document.getElementById('leaveModalHistoryTableBody').innerHTML = `
            <tr><td colspan="7" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm text-primary me-2"></div> Loading leave history...</td></tr>
        `;

        fetch(`{{ route('school.payroll.payroll-attendance.modal-leave') }}?staff_id=${staffId}&salary_month=${SELECTED_MONTH}&salary_year=${SELECTED_YEAR}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(res => {
            if (!res.success) {
                showToast(res.message || 'Could not load leave details.', true);
                return;
            }

            document.getElementById('leaveModalStaffName').textContent = res.staff.name + ' (' + res.staff.employee_id + ')';
            document.getElementById('leaveModalStaffSub').textContent = `${res.staff.designation} • ${res.staff.department}`;

            // Leave Balances
            let balHtml = '';
            if (res.balances && res.balances.length > 0) {
                res.balances.forEach(b => {
                    balHtml += `
                        <div class="py-leave-card">
                            <div class="py-leave-pill-code">${b.type_name} (${b.type_code})</div>
                            <div class="py-leave-count">${b.balance} <span style="font-size:12px; font-weight:600; color:#64748b;">Days Left</span></div>
                            <div class="py-leave-meta">Allowed: <strong>${b.allowed}</strong> | Availed: <strong>${b.availed}</strong></div>
                        </div>
                    `;
                });
            } else {
                balHtml = '<div class="text-muted text-center py-2" style="grid-column: 1 / -1;">No leave balance quotas configured for this employee.</div>';
            }


            document.getElementById('leaveBalancesContainer').innerHTML = balHtml;

            // Leave History
            let historyHtml = '';
            if (res.history && res.history.length > 0) {
                res.history.forEach(h => {
                    const statusBadge = h.status === 'Approved' ? 'badge-status-approved' : (h.status === 'Rejected' ? 'badge-status-rejected' : 'badge-status-pending');
                    const paidBadge = h.is_paid ? 'badge-paid-leave' : 'badge-unpaid-leave';
                    historyHtml += `
                        <tr>
                            <td class="td-left"><strong>${h.leave_type}</strong> (${h.leave_code})</td>
                            <td>${h.start_date}</td>
                            <td>${h.end_date}</td>
                            <td><strong>${h.total_days}</strong></td>
                            <td><span class="${paidBadge}">${h.paid_status}</span></td>
                            <td><span class="${statusBadge}">${h.status}</span></td>
                            <td class="td-left" style="font-size:12px;">
                                <div><strong>Reason:</strong> ${h.reason}</div>
                                <div class="text-muted"><strong>Remark:</strong> ${h.admin_remark}</div>
                            </td>
                        </tr>
                    `;
                });
            } else {
                historyHtml = `
                    <tr><td colspan="7" class="text-center py-4 text-muted"><i class="fas fa-check-circle text-success me-1"></i> No leave applications recorded for this staff member.</td></tr>
                `;
            }
            document.getElementById('leaveModalHistoryTableBody').innerHTML = historyHtml;
        })
        .catch(err => {
            showToast('Failed to load leave details.', true);
        });
    }

    // =========================================================================
    // MODAL 3: VIEW SALARY HISTORY
    // =========================================================================
    function openSalaryModal(staffId) {
        openModalById('salaryDetailModal');

        document.getElementById('salaryModalStaffName').textContent = 'Employee Salary & Payslip History';
        document.getElementById('salaryModalStaffSub').textContent = 'Loading salary records...';
        document.getElementById('salaryStructureBreakdown').innerHTML = '<div class="text-center text-muted py-2" style="grid-column: 1 / -1;">Loading structure...</div>';
        document.getElementById('salaryModalHistoryTableBody').innerHTML = `
            <tr><td colspan="7" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm text-success me-2"></div> Loading salary records...</td></tr>
        `;

        fetch(`{{ route('school.payroll.payroll-attendance.modal-salary') }}?staff_id=${staffId}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(res => {
            if (!res.success) {
                showToast(res.message || 'Could not load salary details.', true);
                return;
            }

            document.getElementById('salaryModalStaffName').textContent = res.staff.name + ' (' + res.staff.employee_id + ')';
            document.getElementById('salaryModalStaffSub').textContent = `${res.staff.designation} • ${res.staff.department} | Bank: ${res.staff.bank_name || '—'} (A/C: ${res.staff.account_number || '—'})`;
            document.getElementById('salaryModalNetBadge').textContent = 'Net Salary: ₹' + Number(res.structure.net_salary).toLocaleString('en-IN', {minimumFractionDigits: 2});

            // Salary Structure Breakdown
            const st = res.structure;
            document.getElementById('salaryStructureBreakdown').innerHTML = `
                <div class="py-salary-pill">
                    <div class="py-salary-pill-label">BASIC</div>
                    <div class="py-salary-pill-val">₹${Number(st.basic_salary).toFixed(2)}</div>
                </div>
                <div class="py-salary-pill">
                    <div class="py-salary-pill-label">HRA</div>
                    <div class="py-salary-pill-val">₹${Number(st.hra).toFixed(2)}</div>
                </div>
                <div class="py-salary-pill">
                    <div class="py-salary-pill-label">DA / TA</div>
                    <div class="py-salary-pill-val">₹${(Number(st.da) + Number(st.ta)).toFixed(2)}</div>
                </div>
                <div class="py-salary-pill">
                    <div class="py-salary-pill-label">PF / DED</div>
                    <div class="py-salary-pill-val is-ded">₹${(Number(st.pf) + Number(st.esi) + Number(st.tds) + Number(st.prof_tax)).toFixed(2)}</div>
                </div>
            `;

            // History Records
            let histHtml = '';
            if (res.history && res.history.length > 0) {
                res.history.forEach(h => {
                    const stClass = h.payment_status === 'Paid' ? 'badge-status-approved' : (h.payment_status === 'Partially Paid' ? 'badge-status-pending' : 'badge-status-rejected');
                    histHtml += `
                        <tr>
                            <td class="td-left"><strong>${h.payroll_month}</strong></td>
                            <td>₹${Number(h.gross_salary).toFixed(2)}</td>
                            <td class="text-danger" style="font-weight:700;">₹${Number(h.deductions).toFixed(2)}</td>
                            <td><strong style="color:#059669;">₹${Number(h.net_payable).toFixed(2)}</strong></td>
                            <td>${h.payment_date}</td>
                            <td><span class="${stClass}">${h.payment_status}</span></td>
                            <td>
                                <a href="${h.payslip_url}" target="_blank" class="py-btn-primary" style="padding: 4px 10px; font-size: 11.5px; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fas fa-file-arrow-down"></i> Slip
                                </a>
                            </td>
                        </tr>
                    `;
                });
            } else {
                histHtml = `
                    <tr><td colspan="7" class="text-center py-4 text-muted"><i class="fas fa-info-circle me-1"></i> No prior finalised payroll records found for this employee.</td></tr>
                `;
            }
            document.getElementById('salaryModalHistoryTableBody').innerHTML = histHtml;
        })
        .catch(err => {
            showToast('Failed to load salary history.', true);
        });
    }
</script>
@endsection
