@extends('layouts.app')

@section('title', 'Salary Payment — HR Payroll')

@section('styles')
<style>
    /* Main Layout Container */
    .spay-container {
        width: 100% !important;
        max-width: 100% !important;
        padding: 24px 30px;
        box-sizing: border-box;
    }

    /* Page 1: Select Payment Month Card */
    .month-select-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 450px;
        padding: 40px 20px;
    }
    .month-select-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 12px 36px rgba(15, 23, 42, 0.09);
        width: 100%;
        max-width: 520px;
        overflow: hidden;
        transition: background-color 0.2s, border-color 0.2s;
    }
    .month-select-hdr {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
        color: #ffffff;
        padding: 20px 26px;
        font-size: 16.5px;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 10px;
        letter-spacing: 0.3px;
    }
    .month-select-body {
        padding: 28px 32px;
    }
    .month-select-label {
        font-size: 12.5px;
        font-weight: 800;
        color: #334155;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .month-select-input {
        width: 100% !important;
        height: 48px !important;
        padding: 10px 16px !important;
        border: 1.5px solid #cbd5e1 !important;
        border-radius: 10px !important;
        font-size: 14.5px !important;
        font-weight: 600 !important;
        color: #1e293b !important;
        background-color: #ffffff !important;
        outline: none !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04) !important;
        box-sizing: border-box !important;
        transition: all 0.2s ease !important;
        margin-bottom: 14px !important;
        display: block !important;
    }
    .month-select-input:focus {
        border-color: #2563eb !important;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
    }
    .month-info-box {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 10px;
        padding: 12px 16px;
        font-size: 12px;
        font-weight: 600;
        color: #1e40af;
        line-height: 1.4;
        margin-bottom: 24px;
        display: flex;
        align-items: flex-start;
        gap: 8px;
    }
    .btn-view-payment-list {
        width: 100%;
        height: 48px;
        padding: 12px 24px;
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
        color: #ffffff;
        border: none;
        border-radius: 10px;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.25);
    }
    .btn-view-payment-list:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(37, 99, 235, 0.35);
        color: #ffffff;
    }

    /* Page 2: Payment List Main Card */
    .spay-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(37, 99, 235, 0.05);
        overflow: hidden;
        margin-bottom: 24px;
    }
    .spay-card-hdr {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
        color: #ffffff;
        padding: 18px 24px;
        font-size: 16px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: space-between;
        letter-spacing: 0.2px;
    }
    .btn-change-month {
        background: rgba(255, 255, 255, 0.18);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.3);
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 12.5px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
    }
    .btn-change-month:hover {
        background: rgba(255, 255, 255, 0.3);
        color: #ffffff;
    }

    /* Toolbar & Filters */
    .spay-toolbar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        background: #f8fafc;
        padding: 16px 24px;
        border-bottom: 1px solid #e2e8f0;
    }
    .spay-search-group {
        position: relative;
        min-width: 260px;
        max-width: 400px;
        flex-grow: 1;
    }
    .spay-search-input {
        width: 100%;
        height: 42px;
        padding: 8px 14px 8px 38px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        font-size: 13.5px;
        font-weight: 600;
        color: #1e293b;
        outline: none;
        transition: all 0.2s ease;
        box-sizing: border-box;
    }
    .spay-search-input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }
    .spay-search-icon {
        position: absolute;
        left: 13px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 14px;
    }
    .spay-btn-group {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .btn-spay-export-pdf {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: #ffffff !important;
        border: none;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        box-shadow: 0 2px 6px rgba(239, 68, 68, 0.2);
    }
    .btn-spay-export-pdf:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.35);
    }
    .btn-spay-export-excel {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff !important;
        border: none;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        box-shadow: 0 2px 6px rgba(16, 185, 129, 0.2);
    }
    .btn-spay-export-excel:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35);
    }
    .btn-spay-reset {
        background: #ffffff;
        color: #64748b;
        border: 1px solid #cbd5e1;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
    }
    .btn-spay-reset:hover {
        background: #f1f5f9;
        color: #334155;
    }

    /* Table Styles */
    .spay-table-responsive {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .spay-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        font-size: 13px;
    }
    .spay-table th {
        background: #f1f5f9;
        color: #475569;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 12px 14px;
        border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
    }
    .spay-table td {
        padding: 14px;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
        vertical-align: middle;
        white-space: nowrap;
    }
    .spay-table tbody tr:hover {
        background-color: #f8fafc;
    }

    /* Status Badges */
    .sp-badge-unpaid {
        background: #ffedd5;
        color: #c2410c;
        border: 1px solid #fed7aa;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .sp-badge-paid {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .btn-pay-now {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
        color: #ffffff;
        border: none;
        padding: 6px 16px;
        border-radius: 8px;
        font-size: 12.5px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 2px 6px rgba(30, 58, 138, 0.2);
    }
    .btn-pay-now:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
        color: #ffffff;
    }
    .btn-pay-disabled {
        background: #e2e8f0;
        color: #94a3b8;
        border: none;
        padding: 6px 16px;
        border-radius: 8px;
        font-size: 12.5px;
        font-weight: 700;
        cursor: not-allowed;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    /* ==========================================================================
       PURE BLUE & WHITE SLIDE-OVER DRAWER PANEL SYSTEM (850PX WIDE & SPACIOUS)
       ========================================================================== */
    .spay-drawer-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(15, 23, 42, 0.55);
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
        z-index: 99999;
        display: none;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .spay-drawer-overlay.open {
        display: block !important;
        opacity: 1;
    }
    .spay-drawer-panel {
        position: fixed;
        top: 0;
        right: 0;
        width: 850px;
        max-width: 90vw;
        height: 100vh;
        max-height: 100vh;
        background: #ffffff;
        box-shadow: -10px 0 50px rgba(30, 58, 138, 0.25);
        z-index: 100000;
        display: flex;
        flex-direction: column;
        transform: translateX(100%);
        transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        overflow: hidden;
    }
    .spay-drawer-overlay.open .spay-drawer-panel {
        transform: translateX(0);
    }

    /* Pure Blue & White Slider Header */
    .spay-drawer-hdr {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
        color: #ffffff;
        padding: 18px 26px;
        font-size: 17.5px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid rgba(255, 255, 255, 0.15);
        flex-shrink: 0;
        height: 64px;
        box-sizing: border-box;
    }
    .spay-drawer-close {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: #ffffff;
        font-size: 22px;
        line-height: 1;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .spay-drawer-close:hover {
        background: rgba(255, 255, 255, 0.35);
    }

    .spay-drawer-form {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 64px);
        max-height: calc(100vh - 64px);
        flex: 1 1 auto;
        overflow: hidden;
    }

    .spay-drawer-body {
        flex: 1 1 auto;
        min-height: 0;
        overflow-y: auto !important;
        -webkit-overflow-scrolling: touch;
        padding: 24px 28px;
        box-sizing: border-box;
        background: #f8fafc;
    }

    .spay-drawer-ftr {
        padding: 16px 28px;
        background: #ffffff;
        border-top: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
        flex-shrink: 0;
    }

    /* Modal Form Controls Styling - Blue & White System */
    .spay-input-ctrl {
        width: 100% !important;
        height: 44px !important;
        padding: 8px 14px !important;
        border: 1.5px solid #cbd5e1 !important;
        border-radius: 10px !important;
        font-size: 14px !important;
        font-weight: 600 !important;
        color: #1e293b !important;
        background-color: #ffffff !important;
        outline: none !important;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03) !important;
        box-sizing: border-box !important;
        transition: all 0.2s ease !important;
        display: block !important;
    }
    .spay-input-ctrl:focus {
        border-color: #2563eb !important;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
    }
    
    /* Pure Blue & White Section Cards */
    .spay-sec-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 18px 22px;
        margin-bottom: 18px;
        box-sizing: border-box;
        box-shadow: 0 2px 8px rgba(37, 99, 235, 0.04);
    }
    .spay-sec-hdr {
        font-size: 13.5px;
        font-weight: 800;
        color: #1e3a8a;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 8px;
        border-bottom: 1px solid #f1f5f9;
    }
    .spay-sec-hdr i {
        color: #2563eb !important;
    }
    
    .spay-lbl-sm {
        font-size: 11px;
        font-weight: 800;
        color: #64748b;
        margin-bottom: 4px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .spay-val-sm {
        font-size: 14.5px;
        font-weight: 700;
        color: #1e293b;
        word-break: break-word;
    }

    /* Blue & White Action Buttons */
    .btn-spay-submit {
        height: 44px;
        padding: 10px 24px;
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
        color: #ffffff;
        border: none;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
    }
    .btn-spay-submit:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(37, 99, 235, 0.35);
        color: #ffffff;
    }
    .btn-spay-discard {
        height: 44px;
        padding: 10px 20px;
        background: #ffffff;
        color: #64748b;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-spay-discard:hover {
        background: #f1f5f9;
        color: #1e293b;
        border-color: #94a3b8;
    }

    /* Edit Bank Toggle Button */
    .btn-edit-bank-toggle {
        background: #eff6ff;
        color: #2563eb;
        border: 1px solid #bfdbfe;
        padding: 4px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-edit-bank-toggle:hover {
        background: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
    }

    /* ==========================================================================
       DARK MODE SUPPORT (body.dark-mode)
       ========================================================================== */
    body.dark-mode .spay-container {
        color: #f8fafc;
    }
    body.dark-mode .spay-card,
    body.dark-mode .month-select-card {
        background: #1e293b !important;
        border-color: #334155 !important;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35) !important;
    }
    body.dark-mode .month-select-hdr {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%) !important;
    }
    body.dark-mode .month-select-label {
        color: #cbd5e1 !important;
    }
    body.dark-mode .month-select-input {
        background-color: #0f172a !important;
        color: #f8fafc !important;
        border-color: #475569 !important;
    }
    body.dark-mode .month-info-box {
        background: rgba(30, 58, 138, 0.25) !important;
        border-color: #1e40af !important;
        color: #93c5fd !important;
    }
    body.dark-mode .spay-toolbar {
        background: #0f172a !important;
        border-color: #334155 !important;
    }
    body.dark-mode .spay-search-input {
        background: #1e293b !important;
        border-color: #475569 !important;
        color: #f8fafc !important;
    }
    body.dark-mode .spay-btn-reset {
        background: #1e293b !important;
        color: #cbd5e1 !important;
        border-color: #475569 !important;
    }
    body.dark-mode .spay-table th {
        background: #0f172a !important;
        color: #94a3b8 !important;
        border-color: #334155 !important;
    }
    body.dark-mode .spay-table td {
        color: #cbd5e1 !important;
        border-color: #334155 !important;
    }
    body.dark-mode .spay-table tbody tr:hover {
        background-color: #1e293b !important;
    }
    
    /* Dark Mode Blue & White Drawer Overlay & Panel */
    body.dark-mode .spay-drawer-overlay {
        background: rgba(0, 0, 0, 0.75) !important;
    }
    body.dark-mode .spay-drawer-panel {
        background: #1e293b !important;
        border-color: #334155 !important;
        color: #f8fafc !important;
        box-shadow: -10px 0 40px rgba(0, 0, 0, 0.6) !important;
    }
    body.dark-mode .spay-drawer-hdr {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%) !important;
        border-color: #334155 !important;
    }
    body.dark-mode .spay-drawer-body {
        background: #0f172a !important;
    }
    body.dark-mode .spay-sec-card {
        background: #1e293b !important;
        border-color: #334155 !important;
    }
    body.dark-mode .spay-sec-hdr {
        color: #93c5fd !important;
        border-color: #334155 !important;
    }
    body.dark-mode .spay-sec-hdr i {
        color: #60a5fa !important;
    }
    body.dark-mode .spay-lbl-sm {
        color: #94a3b8 !important;
    }
    body.dark-mode .spay-val-sm {
        color: #f8fafc !important;
    }
    body.dark-mode .spay-input-ctrl,
    body.dark-mode .form-control,
    body.dark-mode .form-select {
        background-color: #0f172a !important;
        border-color: #475569 !important;
        color: #f8fafc !important;
    }
    body.dark-mode .spay-drawer-ftr {
        background: #0f172a !important;
        border-color: #334155 !important;
    }
    body.dark-mode .btn-spay-discard {
        background: #1e293b !important;
        color: #cbd5e1 !important;
        border-color: #475569 !important;
    }

    /* ==========================================================================
       MOBILE RESPONSIVE OPTIMIZATIONS
       ========================================================================== */
    @media (max-width: 768px) {
        .spay-container {
            padding: 16px 12px !important;
        }
        .month-select-wrapper {
            padding: 20px 10px !important;
            min-height: 360px !important;
        }
        .month-select-body {
            padding: 20px 18px !important;
        }
        .spay-toolbar {
            padding: 12px 14px !important;
            flex-direction: column;
            align-items: stretch !important;
        }
        .spay-search-group {
            min-width: 100% !important;
            max-width: 100% !important;
        }
        .spay-btn-group {
            width: 100%;
            justify-content: space-between;
            margin-top: 8px;
        }
        .btn-spay-export-pdf, .btn-spay-export-excel {
            flex: 1;
            justify-content: center;
        }
        .spay-drawer-panel {
            width: 100vw !important;
            max-width: 100vw !important;
        }
        .spay-drawer-body {
            padding: 16px !important;
        }
        .spay-sec-card {
            padding: 14px 16px !important;
        }
        .spay-drawer-ftr {
            padding: 12px 16px !important;
            flex-direction: column-reverse;
        }
        .spay-drawer-ftr button {
            width: 100% !important;
        }
    }
</style>
@endsection

@section('content')
<div class="spay-container">

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert" style="border-left: 4px solid #10b981 !important;">
            <i class="fas fa-check-circle me-2 fs-5"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert" style="border-left: 4px solid #ef4444 !important;">
            <i class="fas fa-exclamation-circle me-2 fs-5"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(!$hasSelectedMonth)
        <!-- ========================================== -->
        <!-- PAGE 1: SELECT PAYMENT MONTH              -->
        <!-- ========================================== -->
        <div class="month-select-wrapper">
            <div class="month-select-card">
                <div class="month-select-hdr">
                    <i class="fas fa-calendar-check"></i>
                    <span>Select Payment Month</span>
                </div>
                <div class="month-select-body">
                    @if($unpaidMonths->isEmpty())
                        <div class="text-center py-4">
                            <div class="rounded-circle bg-blue-50 d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px; background: #eff6ff;">
                                <i class="fas fa-check-double text-blue-600 fa-2x" style="color: #2563eb;"></i>
                            </div>
                            <h6 class="fw-bold mb-2">All Payrolls Fully Disbursed</h6>
                            <p class="text-muted small mb-0 px-3">There are no pending or unpaid payroll months available. All generated salaries have been paid or no payrolls exist.</p>
                        </div>
                    @else
                        <form action="{{ route('school.payroll.salary-payment') }}" method="GET">
                            <div>
                                <label class="month-select-label">
                                    <i class="fas fa-calendar-alt text-primary"></i> SELECT MONTH - YEAR
                                </label>
                                <select name="month_year" class="month-select-input" required>
                                    <option value="">-- Choose Payment Month --</option>
                                    @foreach($unpaidMonths as $um)
                                        <option value="{{ $um->display_label }}">
                                            {{ $um->display_label }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="month-info-box">
                                    <i class="fas fa-info-circle me-1" style="font-size: 14px; margin-top: 1px;"></i>
                                    <span>Shows generated payroll months with pending unpaid employees. Fully paid months automatically hide.</span>
                                </div>
                            </div>

                            <button type="submit" class="btn-view-payment-list">
                                <i class="fas fa-list-check"></i> View Payment List
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

    @else
        <!-- ========================================== -->
        <!-- PAGE 2: PAYMENT LIST                       -->
        <!-- ========================================== -->
        <div class="spay-card">
            <!-- Top Card Header -->
            <div class="spay-card-hdr">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-table-list fs-5"></i>
                    <span>Payment List — {{ $selectedMonth }} {{ $selectedYear }}</span>
                </div>
                <a href="{{ route('school.payroll.salary-payment') }}" class="btn-change-month">
                    <i class="fas fa-arrow-left"></i> Change Month
                </a>
            </div>

            <!-- Toolbar (Search, Reset, PDF, Excel) -->
            <div class="spay-toolbar">
                <form action="{{ route('school.payroll.salary-payment') }}" method="GET" class="d-flex align-items-center gap-2 flex-grow-1 flex-wrap">
                    <input type="hidden" name="salary_month" value="{{ $selectedMonth }}">
                    <input type="hidden" name="salary_year" value="{{ $selectedYear }}">
                    <input type="hidden" name="month_year" value="{{ $selectedMonth }} - {{ $selectedYear }}">
                    
                    <div class="spay-search-group">
                        <i class="fas fa-search spay-search-icon"></i>
                        <input type="text" name="search" class="spay-search-input" placeholder="Search Emp ID, Name, Bank, Account, PAN..." value="{{ $search }}">
                    </div>
                    <button type="submit" class="btn btn-primary rounded-3 px-3 py-2 fw-bold text-nowrap" style="background: #1e3a8a; border: none; height: 42px;">
                        Filter
                    </button>
                    @if($search)
                        <a href="{{ route('school.payroll.salary-payment', ['salary_month' => $selectedMonth, 'salary_year' => $selectedYear]) }}" class="btn-spay-reset spay-btn-reset">
                            <i class="fas fa-rotate-left"></i> Reset
                        </a>
                    @endif
                </form>

                <div class="spay-btn-group">
                    <a href="{{ route('school.payroll.salary-payment', array_merge(request()->all(), ['export' => 'pdf'])) }}" target="_blank" class="btn-spay-export-pdf">
                        <i class="fas fa-file-pdf"></i> PDF Export
                    </a>
                    <a href="{{ route('school.payroll.salary-payment', array_merge(request()->all(), ['export' => 'excel'])) }}" class="btn-spay-export-excel">
                        <i class="fas fa-file-excel"></i> Excel Export
                    </a>
                </div>
            </div>

            <!-- Responsive Table -->
            <div class="spay-table-responsive">
                <table class="spay-table">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">S.No</th>
                            <th>Salary Month</th>
                            <th>Employee ID</th>
                            <th>Employee Name</th>
                            <th>Department</th>
                            <th>Designation</th>
                            <th>Bank Name</th>
                            <th>Account Number</th>
                            <th>IFSC Code</th>
                            <th>PAN Number</th>
                            <th class="text-end">Net Salary</th>
                            <th class="text-center">Payment Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payrolls as $index => $row)
                            @php
                                $st = $row->staff;
                                $isPaid = strtolower($row->payment_status) === 'paid';
                                $lastPayment = $row->payments?->last();
                                $payDate = $lastPayment ? \Carbon\Carbon::parse($lastPayment->payment_date)->format('d M, Y') : '';
                            @endphp
                            <tr>
                                <td class="text-center fw-bold text-slate-500">
                                    {{ $payrolls->firstItem() + $index }}
                                </td>
                                <td class="fw-semibold">
                                    {{ $row->payroll_month ?: "{$selectedMonth} {$selectedYear}" }}
                                </td>
                                <td>
                                    <span class="badge bg-slate-100 text-slate-800 border font-monospace px-2 py-1" style="background:#f1f5f9; font-weight:700;">
                                        {{ $st?->employee_id ?: 'EMP-'.$row->staff_id }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $st?->full_name ?: 'N/A' }}</div>
                                </td>
                                <td>{{ $st?->department?->name ?: 'N/A' }}</td>
                                <td>{{ $st?->designation?->name ?: 'N/A' }}</td>
                                <td>
                                    @if($st?->bank_name)
                                        <span class="fw-semibold">{{ $st->bank_name }}</span>
                                    @else
                                        <span class="text-danger fw-bold fs-7"><i class="fas fa-exclamation-triangle me-1"></i> Missing</span>
                                    @endif
                                </td>
                                <td>
                                    @if($st?->bank_account_number)
                                        <code class="fw-bold">{{ $st->bank_account_number }}</code>
                                    @else
                                        <span class="text-danger fw-bold fs-7"><i class="fas fa-exclamation-triangle me-1"></i> Missing</span>
                                    @endif
                                </td>
                                <td>
                                    @if($st?->ifsc_code)
                                        <span class="font-monospace fw-bold">{{ $st->ifsc_code }}</span>
                                    @else
                                        <span class="text-danger fw-bold fs-7"><i class="fas fa-exclamation-triangle me-1"></i> Missing</span>
                                    @endif
                                </td>
                                <td>{{ $st?->pan_number ?: 'N/A' }}</td>
                                <td class="text-end">
                                    <span class="fw-bold fs-6">₹{{ number_format($row->net_payable, 2) }}</span>
                                </td>
                                <td class="text-center">
                                    @if($isPaid)
                                        <span class="sp-badge-paid">
                                            <i class="fas fa-check-circle"></i> Paid
                                        </span>
                                    @else
                                        <span class="sp-badge-unpaid">
                                            <i class="fas fa-clock"></i> Unpaid
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($isPaid)
                                        <div class="d-flex flex-column align-items-center">
                                            <button class="btn-pay-disabled mb-1" disabled>
                                                <i class="fas fa-check-double"></i> Paid
                                            </button>
                                            @if($payDate)
                                                <small class="text-muted" style="font-size: 11px;">
                                                    <i class="far fa-calendar-check me-1"></i>{{ $payDate }}
                                                </small>
                                            @endif
                                        </div>
                                    @else
                                        <button type="button" class="btn-pay-now" onclick="openPayDrawer({{ $row->id }})">
                                            <i class="fas fa-hand-holding-dollar"></i> Pay Now
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="13" class="text-center py-5 text-muted">
                                    <div class="py-4">
                                        <i class="fas fa-inbox fa-3x mb-3 text-slate-300" style="color: #cbd5e1;"></i>
                                        <h6 class="fw-bold mb-1">No Payroll Records Found</h6>
                                        <p class="mb-0 text-muted small">No payroll generated for {{ $selectedMonth }} {{ $selectedYear }} matching your filter criteria.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($payrolls instanceof \Illuminate\Pagination\LengthAwarePaginator && $payrolls->hasPages())
                <div class="p-3 border-top bg-light">
                    {{ $payrolls->links() }}
                </div>
            @endif
        </div>
    @endif

</div>

@if($hasSelectedMonth && $payrolls->isNotEmpty())
    <!-- ========================================================================== -->
    <!-- PURE BLUE & WHITE SLIDE-OVER DRAWER PANELS (850PX WIDE & PERFECT SCROLL)   -->
    <!-- ========================================================================== -->
    @foreach($payrolls as $row)
        @php
            $st = $row->staff;
            $isPaid = strtolower($row->payment_status) === 'paid';
        @endphp
        @if(!$isPaid)
            <div class="spay-drawer-overlay" id="payNowDrawer_{{ $row->id }}" onclick="if(event.target === this) closePayDrawer({{ $row->id }})">
                <div class="spay-drawer-panel">
                    <!-- Drawer Header (Blue & White Theme) -->
                    <div class="spay-drawer-hdr">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-money-bill-wave"></i>
                            <span>Disburse Salary Payment</span>
                        </div>
                        <button type="button" class="spay-drawer-close" onclick="closePayDrawer({{ $row->id }})">&times;</button>
                    </div>

                    <!-- Drawer Form Container -->
                    <form action="{{ route('school.payroll.store-payment') }}" method="POST" class="spay-drawer-form">
                        @csrf
                        <input type="hidden" name="staff_id" value="{{ $row->staff_id }}">
                        <input type="hidden" name="staff_payroll_id" value="{{ $row->id }}">
                        <input type="hidden" name="salary_month" value="{{ $selectedMonth }}">
                        <input type="hidden" name="salary_year" value="{{ $selectedYear }}">
                        <input type="hidden" name="check_bank_details" value="1">

                        <!-- Drawer Body (Smooth Scrollable Area) -->
                        <div class="spay-drawer-body">

                            <!-- SECTION 1: EMPLOYEE DETAILS CARD -->
                            <div class="spay-sec-card spay-sec-employee">
                                <div class="spay-sec-hdr">
                                    <span><i class="fas fa-user me-1"></i> Employee Details</span>
                                </div>
                                <div class="row g-3">
                                    <div class="col-12 col-sm-4">
                                        <div class="spay-lbl-sm">Employee Name</div>
                                        <div class="spay-val-sm">{{ $st?->full_name ?: 'N/A' }}</div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="spay-lbl-sm">Employee ID</div>
                                        <div class="spay-val-sm">{{ $st?->employee_id ?: 'EMP-'.$row->staff_id }}</div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="spay-lbl-sm">Salary Month</div>
                                        <div class="spay-val-sm text-blue-700" style="color: #1d4ed8;">{{ $selectedMonth }} - {{ $selectedYear }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- SECTION 2: BANK DETAILS CARD (WITH EDIT TOGGLE) -->
                            <div class="spay-sec-card spay-sec-bank">
                                <div class="spay-sec-hdr">
                                    <span><i class="fas fa-building-columns me-1"></i> Bank Account Information</span>
                                    <button type="button" class="btn-edit-bank-toggle" onclick="toggleEditBank({{ $row->id }})">
                                        <i class="fas fa-pen-to-square me-1"></i> <span id="bankEditBtnText_{{ $row->id }}">Edit Bank Details</span>
                                    </button>
                                </div>

                                <!-- Display Mode -->
                                <div id="bankDisplay_{{ $row->id }}">
                                    <div class="row g-3">
                                        <div class="col-6 col-md-3">
                                            <div class="spay-lbl-sm">Bank Name</div>
                                            <div class="spay-val-sm">
                                                {{ $st?->bank_name ?: '-' }}
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <div class="spay-lbl-sm">Account No</div>
                                            <div class="spay-val-sm">
                                                {{ $st?->bank_account_number ?: '-' }}
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <div class="spay-lbl-sm">IFSC Code</div>
                                            <div class="spay-val-sm">
                                                {{ $st?->ifsc_code ?: '-' }}
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <div class="spay-lbl-sm">PAN Number</div>
                                            <div class="spay-val-sm">
                                                {{ $st?->pan_number ?: '-' }}
                                            </div>
                                        </div>
                                    </div>
                                    @if(!$st?->bank_name || !$st?->bank_account_number || !$st?->ifsc_code)
                                        <div class="alert alert-warning py-1 px-2 mt-3 mb-0" style="font-size: 11.5px; border-radius: 8px;">
                                            <i class="fas fa-exclamation-triangle me-1"></i> Bank details incomplete. Click <strong>Edit Bank Details</strong> to update teacher profile.
                                        </div>
                                    @endif
                                </div>

                                <!-- Editable Mode -->
                                <div id="bankEditForm_{{ $row->id }}" style="display: none;" class="pt-1">
                                    <div class="row g-3">
                                        <div class="col-12 col-sm-6">
                                            <label class="spay-lbl-sm">Bank Name</label>
                                            <input type="text" name="bank_name" class="spay-input-ctrl" value="{{ $st?->bank_name }}" placeholder="e.g. State Bank of India">
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <label class="spay-lbl-sm">Account Number</label>
                                            <input type="text" name="bank_account_number" class="spay-input-ctrl" value="{{ $st?->bank_account_number }}" placeholder="e.g. 1234567890">
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <label class="spay-lbl-sm">IFSC Code</label>
                                            <input type="text" name="ifsc_code" class="spay-input-ctrl font-monospace" value="{{ $st?->ifsc_code }}" placeholder="e.g. SBIN0001234">
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <label class="spay-lbl-sm">PAN Number</label>
                                            <input type="text" name="pan_number" class="spay-input-ctrl font-monospace" value="{{ $st?->pan_number }}" placeholder="e.g. ABCDE1234F">
                                        </div>
                                    </div>
                                    <small class="text-muted d-block mt-2" style="font-size: 11px;">
                                        <i class="fas fa-check-circle me-1 text-primary"></i> Updates saved here will directly update the teacher's profile in database.
                                    </small>
                                </div>
                            </div>

                            <!-- SECTION 3: SALARY DETAILS CARD -->
                            <div class="spay-sec-card spay-sec-salary">
                                <div class="spay-sec-hdr">
                                    <span><i class="fas fa-wallet me-1"></i> Salary Breakdown</span>
                                </div>
                                <div class="row g-3 align-items-center">
                                    <div class="col-12 col-sm-6">
                                        <div class="spay-lbl-sm">Net Salary (Payable)</div>
                                        <div class="fs-4 fw-extrabold text-blue-700" style="color: #1d4ed8;">
                                            ₹{{ number_format($row->net_payable, 2) }}
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <div class="spay-lbl-sm">Transaction Amount</div>
                                        <input type="text" class="spay-input-ctrl fw-bold text-blue-900" value="₹{{ number_format($row->net_payable, 2) }}" readonly style="color: #1e3a8a; background: #ffffff;">
                                    </div>
                                </div>
                            </div>

                            <!-- SECTION 4: PAYMENT DETAILS CARD -->
                            <div class="spay-sec-card spay-sec-payment">
                                <div class="spay-sec-hdr">
                                    <span><i class="fas fa-credit-card me-1"></i> Payment Transaction Info</span>
                                </div>
                                <div class="row g-3">
                                    <div class="col-12 col-sm-6">
                                        <label class="spay-lbl-sm">Payment Mode <span class="text-danger">*</span></label>
                                        <select name="payment_method" class="spay-input-ctrl" required>
                                            <option value="bank_transfer" selected>Bank Transfer / NEFT / RTGS</option>
                                            <option value="cash">Cash</option>
                                            <option value="cheque">Cheque</option>
                                            <option value="upi">UPI / Online Transfer</option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label class="spay-lbl-sm">Payment Date <span class="text-danger">*</span></label>
                                        <input type="date" name="payment_date" class="spay-input-ctrl" value="{{ date('Y-m-d') }}" required>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label class="spay-lbl-sm">Transaction / UTR Reference No.</label>
                                        <input type="text" name="reference_no" class="spay-input-ctrl" placeholder="e.g. UTR987654321">
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label class="spay-lbl-sm">Disbursal Remarks (Optional)</label>
                                        <input type="text" name="notes" class="spay-input-ctrl" placeholder="Enter Remarks...">
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Drawer Footer (Blue & White Buttons) -->
                        <div class="spay-drawer-ftr">
                            <button type="button" class="btn-spay-discard" onclick="closePayDrawer({{ $row->id }})">
                                Discard
                            </button>
                            <button type="submit" class="btn-spay-submit">
                                <i class="fas fa-paper-plane me-1"></i> Make Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    @endforeach
@endif

<script>
    function openPayDrawer(id) {
        var overlay = document.getElementById('payNowDrawer_' + id);
        if (overlay) {
            overlay.classList.add('open');
            document.body.style.overflow = 'hidden';
        }
    }

    function closePayDrawer(id) {
        var overlay = document.getElementById('payNowDrawer_' + id);
        if (overlay) {
            overlay.classList.remove('open');
            document.body.style.overflow = '';
        }
    }

    function toggleEditBank(id) {
        var displayDiv = document.getElementById('bankDisplay_' + id);
        var editDiv = document.getElementById('bankEditForm_' + id);
        var btnText = document.getElementById('bankEditBtnText_' + id);

        if (editDiv.style.display === 'none') {
            editDiv.style.display = 'block';
            displayDiv.style.display = 'none';
            btnText.innerText = 'View Bank Details';
        } else {
            editDiv.style.display = 'none';
            displayDiv.style.display = 'block';
            btnText.innerText = 'Edit Bank Details';
        }
    }
</script>
@endsection
