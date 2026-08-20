@extends('layouts.app')

@section('title', 'Account Statement — HR Payroll')

@section('styles')
<style>
    .stmt-container {
        width: 100% !important;
        max-width: 100% !important;
        padding: 24px;
        box-sizing: border-box;
        background-color: #f8fafc;
        min-height: calc(100vh - 80px);
    }
    
    /* Main Outer Card */
    .stmt-card {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(30, 58, 138, 0.05);
        overflow: hidden;
        margin-bottom: 24px;
    }

    /* Primary Deep Blue Header */
    .stmt-card-header {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
        color: #ffffff;
        padding: 18px 24px;
        font-size: 16.5px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: space-between;
        letter-spacing: 0.3px;
        flex-wrap: wrap;
        gap: 12px;
    }
    .stmt-hdr-left {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .stmt-hdr-title {
        font-size: 16.5px;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .btn-back-to-search {
        background: #ffffff !important;
        color: #1e3a8a !important;
        border: none !important;
        padding: 8px 16px !important;
        border-radius: 10px !important;
        font-size: 13px !important;
        font-weight: 800 !important;
        text-decoration: none !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15) !important;
        transition: all 0.2s ease !important;
    }
    .btn-back-to-search:hover {
        background: #f1f5f9 !important;
        transform: translateY(-1px) !important;
        color: #1d4ed8 !important;
    }

    .stmt-card-body {
        padding: 28px 32px;
        background: #ffffff;
    }

    /* Sub-Section Box */
    .stmt-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 20px 24px;
        margin-bottom: 24px;
        clear: both;
    }

    /* Form Labels & Controls */
    .stmt-form-label {
        font-size: 11.5px;
        font-weight: 800;
        color: #1e3a8a;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .stmt-form-control {
        width: 100%;
        padding: 11px 15px;
        border: 1.5px solid #cbd5e1;
        border-radius: 10px;
        font-size: 13.5px;
        font-weight: 600;
        color: #0f172a;
        background-color: #ffffff;
        box-sizing: border-box;
        transition: all 0.2s ease;
    }
    .stmt-form-control:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
        outline: none;
    }

    /* Page 1 Grid (2 Columns) */
    .stmt-form-grid-2col {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    /* Page 2 Grid (5 Columns) */
    .stmt-filter-grid-5col {
        display: grid;
        grid-template-columns: 2fr 2fr 1.5fr 1.5fr 2fr;
        gap: 16px;
        align-items: end;
        width: 100%;
    }
    .stmt-filter-btns {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    /* Buttons */
    .btn-blue-primary {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%) !important;
        color: #ffffff !important;
        border: none !important;
        padding: 11px 28px !important;
        border-radius: 10px !important;
        font-weight: 700 !important;
        font-size: 13.5px !important;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.25) !important;
        transition: all 0.2s ease !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 8px !important;
        text-decoration: none !important;
        cursor: pointer !important;
    }
    .btn-blue-primary:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 20px rgba(37, 99, 235, 0.35) !important;
        color: #ffffff !important;
    }

    .btn-blue-light {
        background: #eff6ff !important;
        color: #1d4ed8 !important;
        border: 1.5px solid #bfdbfe !important;
        padding: 11px 22px !important;
        border-radius: 10px !important;
        font-weight: 700 !important;
        font-size: 13.5px !important;
        transition: all 0.2s ease !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 8px !important;
        text-decoration: none !important;
    }
    .btn-blue-light:hover {
        background: #dbeafe !important;
        color: #1e40af !important;
    }

    .btn-pdf-export {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: #ffffff !important;
        border: none;
        padding: 9px 16px;
        border-radius: 9px;
        font-weight: 700;
        font-size: 12.5px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 3px 10px rgba(239, 68, 68, 0.25);
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    .btn-pdf-export:hover {
        background: #dc2626;
        color: #ffffff;
        transform: translateY(-1px);
    }

    .btn-excel-export {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff !important;
        border: none;
        padding: 9px 16px;
        border-radius: 9px;
        font-weight: 700;
        font-size: 12.5px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 3px 10px rgba(16, 185, 129, 0.25);
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    .btn-excel-export:hover {
        background: #059669;
        color: #ffffff;
        transform: translateY(-1px);
    }

    .btn-print-outline {
        background: #ffffff;
        color: #475569;
        border: 1.5px solid #cbd5e1;
        padding: 9px 16px;
        border-radius: 9px;
        font-weight: 700;
        font-size: 12.5px;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        white-space: nowrap;
    }
    .btn-print-outline:hover {
        background: #f8fafc;
        color: #1e293b;
        border-color: #94a3b8;
    }

    /* Date Preset Pills */
    .stmt-presets-bar {
        display: flex;
        flex-direction: column;
        gap: 10px;
        padding-bottom: 16px;
        margin-bottom: 20px;
        border-bottom: 1px solid #e2e8f0;
        width: 100%;
    }
    .stmt-presets-title {
        font-size: 12px;
        font-weight: 800;
        color: #1e3a8a;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .stmt-presets-pills {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }
    .preset-pill {
        background: #ffffff;
        color: #475569;
        border: 1.5px solid #cbd5e1;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        white-space: nowrap;
    }
    .preset-pill:hover {
        background: #eff6ff;
        color: #1d4ed8;
        border-color: #93c5fd;
    }
    .preset-pill.active {
        background: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
        box-shadow: 0 3px 10px rgba(37, 99, 235, 0.25);
    }

    /* Action Toolbar Bar */
    .stmt-action-bar {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 16px 20px;
        margin-bottom: 24px;
        display: flex;
        flex-direction: column;
        gap: 16px;
        width: 100%;
    }
    @media (min-width: 992px) {
        .stmt-action-bar {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }
    }
    .stmt-channel-wrapper {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .stmt-channel-title {
        font-size: 11.5px;
        font-weight: 800;
        color: #1e3a8a;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .stmt-channel-pills {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }

    /* Channel Tabs */
    .channel-tab-pill {
        background: #ffffff;
        color: #64748b;
        border: 1.5px solid #cbd5e1;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        white-space: nowrap;
    }
    .channel-tab-pill:hover {
        background: #f1f5f9;
        color: #1e293b;
    }
    .channel-tab-pill.active {
        background: #1e3a8a;
        color: #ffffff;
        border-color: #1e3a8a;
        box-shadow: 0 3px 10px rgba(30, 58, 138, 0.2);
    }
    .stmt-export-btns {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }

    /* Quick Staff Section & 3-Column Grid */
    .quick-staff-section-box {
        background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
        border: 1.5px solid #e2e8f0;
        border-radius: 16px;
        padding: 22px 24px;
        margin-top: 10px;
    }

    /* Styled Quick Select Banner Header */
    .quick-select-header-banner {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        border: 1.5px solid #bfdbfe;
        border-radius: 12px;
        padding: 12px 18px;
        margin-bottom: 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
    }
    .quick-select-badge-pill {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
        color: #ffffff;
        font-size: 11.5px;
        font-weight: 800;
        padding: 5px 12px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        letter-spacing: 0.3px;
        box-shadow: 0 2px 6px rgba(37, 99, 235, 0.25);
    }
    .quick-select-title-text {
        font-size: 14.5px;
        font-weight: 800;
        color: #1e3a8a;
        letter-spacing: 0.2px;
    }
    .quick-select-sub-text {
        font-size: 12px;
        font-weight: 600;
        color: #1d4ed8;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .quick-staff-grid-3col {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
    }

    .quick-staff-card-item {
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        border-radius: 14px;
        padding: 13px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        text-decoration: none;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.03);
        position: relative;
        overflow: hidden;
    }
    .quick-staff-card-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: transparent;
        transition: background 0.2s ease;
    }
    .quick-staff-card-item:hover {
        border-color: #3b82f6;
        background: #f8fbff;
        transform: translateY(-3px);
        box-shadow: 0 8px 22px rgba(37, 99, 235, 0.12);
    }
    .quick-staff-card-item:hover::before {
        background: #2563eb;
    }
    .quick-staff-card-item:hover .quick-staff-arrow {
        opacity: 1;
        transform: translateX(3px);
        color: #2563eb;
    }

    .quick-staff-left {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }

    .quick-staff-avatar {
        width: 40px;
        height: 40px;
        border-radius: 11px;
        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 15px;
        flex-shrink: 0;
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.22);
    }

    .quick-staff-name {
        font-size: 13.5px;
        font-weight: 700;
        color: #1e3a8a;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 2px;
    }

    .quick-staff-code {
        font-size: 11.5px;
        font-weight: 600;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .quick-staff-arrow {
        color: #94a3b8;
        font-size: 12px;
        opacity: 0.4;
        transition: all 0.2s ease;
        flex-shrink: 0;
        margin-left: 8px;
    }

    /* Metric Cards Grid */
    .metric-grid-4col {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    .metric-card-box {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 18px 22px;
        box-shadow: 0 4px 14px rgba(30, 58, 138, 0.04);
        position: relative;
        overflow: hidden;
    }
    .metric-card-title {
        font-size: 11.5px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .metric-card-val {
        font-size: 22px;
        font-weight: 800;
        margin-top: 6px;
    }

    /* Table Card */
    .stmt-table-card {
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 14px rgba(30, 58, 138, 0.03);
        background: #ffffff;
    }
    .stmt-table-wrap {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .stmt-data-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 13.5px;
        margin-bottom: 0;
        min-width: 780px;
    }
    .stmt-data-table th {
        background: #eff6ff;
        color: #1e40af;
        font-weight: 700;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 14px 18px;
        border-bottom: 2px solid #dbeafe;
        white-space: nowrap;
    }
    .stmt-data-table td {
        padding: 14px 18px;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        white-space: nowrap;
    }
    .stmt-data-table tbody tr:hover {
        background-color: #f8fafc;
    }
    .badge-channel-bank {
        background: #dbeafe;
        color: #1e40af;
        border: 1px solid #bfdbfe;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
    }
    .badge-channel-cash {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
    }
    .badge-channel-other {
        background: #f3e8ff;
        color: #6b21a8;
        border: 1px solid #e9d5ff;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
    }

    /* Empty State Styling */
    .stmt-empty-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 48px 20px;
        text-align: center;
        width: 100%;
        margin: 0 auto;
    }
    .stmt-empty-icon {
        width: 76px;
        height: 76px;
        border-radius: 50%;
        background: #eff6ff;
        border: 2px solid #bfdbfe;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
        color: #2563eb;
        font-size: 28px;
    }

    /* Responsive Mobile Media Queries */
    @media (max-width: 992px) {
        .stmt-form-grid-2col, .stmt-filter-grid-5col {
            grid-template-columns: 1fr;
            gap: 14px;
        }
        .quick-staff-grid-3col {
            grid-template-columns: repeat(2, 1fr);
        }
        .metric-grid-4col {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 768px) {
        .stmt-container {
            padding: 14px 10px !important;
        }
        .stmt-card-header {
            padding: 14px 16px !important;
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 12px !important;
        }
        .stmt-hdr-left {
            justify-content: space-between !important;
            width: 100% !important;
        }
        .btn-back-to-search {
            width: 100% !important;
            justify-content: center !important;
        }
        .stmt-card-body {
            padding: 18px 14px !important;
        }
        .stmt-box {
            padding: 16px 14px !important;
        }
        .quick-staff-section-box {
            padding: 16px 12px !important;
        }
        .stmt-action-bar {
            padding: 14px 14px !important;
            gap: 14px !important;
        }
        .stmt-data-table th, .stmt-data-table td {
            padding: 10px 12px !important;
            font-size: 12px !important;
        }
    }
    @media (max-width: 576px) {
        .quick-staff-grid-3col {
            grid-template-columns: 1fr !important;
        }
        .metric-grid-4col {
            grid-template-columns: 1fr !important;
        }
        .stmt-btn-row {
            flex-direction: column !important;
            width: 100% !important;
            gap: 10px !important;
        }
        .stmt-btn-row .btn-blue-primary,
        .stmt-btn-row .btn-blue-light {
            width: 100% !important;
            min-width: 100% !important;
        }
        .stmt-export-btns {
            width: 100%;
        }
        .stmt-export-btns .btn-pdf-export,
        .stmt-export-btns .btn-excel-export,
        .stmt-export-btns .btn-print-outline {
            flex: 1 1 calc(50% - 8px);
            justify-content: center;
            text-align: center;
        }
        .stmt-presets-pills .preset-pill,
        .stmt-channel-pills .channel-tab-pill {
            flex-grow: 1;
            justify-content: center;
            text-align: center;
        }
    }
</style>
@endsection

@section('content')
<div class="stmt-container">

    <!-- FLASH MESSAGES -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(!$isSearchSubmitted)
        <!-- ========================================================= -->
        <!-- PAGE 1: ELEGANT BLUE & WHITE EMPLOYEE SEARCH PAGE         -->
        <!-- ========================================================= -->

        <div class="stmt-card">
            <div class="stmt-card-header">
                <div class="stmt-hdr-left">
                    <div class="stmt-hdr-title">
                        <i class="fas fa-search"></i>
                        <span>Employee Account Search</span>
                    </div>
                </div>
                <span class="badge bg-white text-primary px-3 py-1.5 rounded-pill fs-7 fw-bold shadow-sm">
                    <i class="fas fa-shield-alt me-1"></i> HR Payroll
                </span>
            </div>

            <div class="stmt-card-body">
                <form method="GET" action="{{ route('school.payroll.account-statement') }}">
                    <input type="hidden" name="submit_search" value="1">

                    <!-- 2-COLUMN FORM GRID -->
                    <div class="stmt-form-grid-2col">
                        <div class="stmt-form-group">
                            <label class="stmt-form-label">
                                <i class="fas fa-user me-1"></i> Select Employee Name
                            </label>
                            <select name="staff_id" class="stmt-form-control">
                                <option value="">-- Select Employee Name --</option>
                                @foreach($staffList as $st)
                                    <option value="{{ $st->id }}">
                                        {{ $st->full_name }} &mdash; {{ $st->employee_id ?: 'EMP-'.str_pad($st->id, 4, '0', STR_PAD_LEFT) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="stmt-form-group">
                            <label class="stmt-form-label">
                                <i class="fas fa-barcode me-1"></i> Enter Employee Code / ID
                            </label>
                            <input type="text" name="employee_id" class="stmt-form-control" placeholder="Enter Employee Code (e.g. EDUZENEMP003) ...">
                        </div>
                    </div>

                    <!-- BUTTON ROW -->
                    <div class="stmt-btn-row" style="display: flex; justify-content: center; gap: 12px; margin-top: 10px; margin-bottom: 24px;">
                        <button type="submit" class="btn-blue-primary">
                            <i class="fas fa-search me-1"></i> Search Account Statement
                        </button>
                        <a href="{{ route('school.payroll.account-statement', ['view_all' => 1]) }}" class="btn-blue-light">
                            <i class="fas fa-list-ul me-1"></i> View All Transactions
                        </a>
                    </div>
                </form>

                <!-- QUICK SELECT SECTION -->
                <div class="quick-staff-section-box">
                    <div class="quick-select-header-banner">
                        <div class="d-flex align-items-center gap-2">
                            <span class="quick-select-badge-pill">
                                <i class="fas fa-bolt text-warning"></i> Quick Select
                            </span>
                            <span class="quick-select-title-text">Active Staff Members</span>
                        </div>
                        <div class="quick-select-sub-text">
                            <i class="fas fa-hand-pointer"></i> Click any employee to view statement
                        </div>
                    </div>

                    <!-- 3-COLUMN GRID -->
                    <div class="quick-staff-grid-3col">
                        @foreach($staffList->take(6) as $st)
                            <a href="{{ route('school.payroll.account-statement', ['staff_id' => $st->id, 'submit_search' => 1]) }}" class="quick-staff-card-item">
                                <div class="quick-staff-left">
                                    <div class="quick-staff-avatar">
                                        {{ strtoupper(substr($st->first_name, 0, 1)) }}
                                    </div>
                                    <div class="overflow-hidden">
                                        <div class="quick-staff-name">{{ $st->full_name }}</div>
                                        <div class="quick-staff-code">
                                            <i class="far fa-id-badge text-muted"></i>
                                            <span>{{ $st->employee_id ?: 'EMP-'.$st->id }}</span>
                                            @if($st->department)
                                                <span class="text-slate-400">&bull;</span>
                                                <span class="text-slate-500">{{ $st->department->name }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <i class="fas fa-chevron-right quick-staff-arrow"></i>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

    @else
        <!-- ========================================================= -->
        <!-- PAGE 2: ACCOUNT STATEMENT DETAILS PAGE                    -->
        <!-- ========================================================= -->

        <div class="stmt-card">
            <!-- 1. HEADER BANNER -->
            <div class="stmt-card-header">
                <div class="stmt-hdr-left">
                    <div class="stmt-hdr-title">
                        <i class="fas fa-th-large"></i>
                        <span>Account Statement</span>
                    </div>
                    @if($selectedStaff)
                        <span class="badge bg-white text-primary px-3 py-1.5 rounded-pill fs-7 fw-bold shadow-sm">
                            <i class="fas fa-user-check me-1"></i> {{ $selectedStaff->full_name }} ({{ $selectedStaff->employee_id ?: 'EMP-'.$selectedStaff->id }})
                        </span>
                    @else
                        <span class="badge bg-white text-dark px-3 py-1.5 rounded-pill fs-7 fw-bold shadow-sm">
                            <i class="fas fa-users me-1"></i> All Employees Statement
                        </span>
                    @endif
                </div>

                <a href="{{ route('school.payroll.account-statement') }}" class="btn-back-to-search">
                    <i class="fas fa-arrow-left"></i> Back to Search
                </a>
            </div>

            <div class="stmt-card-body">

                <!-- 2. FILTER & DATE PRESETS BOX -->
                <div class="stmt-box">
                    <!-- Quick Date Presets Row (Clean Non-overlapping Bar) -->
                    <div class="stmt-presets-bar">
                        <div class="stmt-presets-title">
                            <i class="fas fa-clock text-primary"></i> Quick Range Presets:
                        </div>
                        <div class="stmt-presets-pills">
                            <a href="{{ route('school.payroll.account-statement', array_merge(request()->except('preset'), ['preset' => 'this_month', 'submit_search' => 1])) }}" 
                               class="preset-pill {{ $preset === 'this_month' ? 'active' : '' }}">
                                This Month
                            </a>
                            <a href="{{ route('school.payroll.account-statement', array_merge(request()->except('preset'), ['preset' => 'last_month', 'submit_search' => 1])) }}" 
                               class="preset-pill {{ $preset === 'last_month' ? 'active' : '' }}">
                                Last Month
                            </a>
                            <a href="{{ route('school.payroll.account-statement', array_merge(request()->except('preset'), ['preset' => 'last_90_days', 'submit_search' => 1])) }}" 
                               class="preset-pill {{ $preset === 'last_90_days' ? 'active' : '' }}">
                                Last 90 Days
                            </a>
                            <!-- Dynamic Academic Session Preset -->
                            <a href="{{ route('school.payroll.account-statement', array_merge(request()->except('preset'), ['preset' => 'academic_year', 'submit_search' => 1])) }}" 
                               class="preset-pill {{ $preset === 'academic_year' || $preset === 'current_fy' ? 'active' : '' }}">
                                <i class="fas fa-graduation-cap me-1"></i> Academic Session ({{ $sessionName }})
                            </a>
                        </div>
                    </div>

                    <!-- 5-Column Filter Form Grid -->
                    <form method="GET" action="{{ route('school.payroll.account-statement') }}">
                        <input type="hidden" name="submit_search" value="1">
                        @if($selectedStaffId) <input type="hidden" name="staff_id" value="{{ $selectedStaffId }}"> @endif
                        @if($employeeIdInput) <input type="hidden" name="employee_id" value="{{ $employeeIdInput }}"> @endif

                        <div class="stmt-filter-grid-5col">
                            <div class="stmt-form-group">
                                <label class="stmt-form-label mb-1">From Date</label>
                                <input type="date" name="from_date" class="stmt-form-control" value="{{ $fromDate }}">
                            </div>
                            <div class="stmt-form-group">
                                <label class="stmt-form-label mb-1">To Date</label>
                                <input type="date" name="to_date" class="stmt-form-control" value="{{ $toDate }}">
                            </div>
                            <div class="stmt-form-group">
                                <label class="stmt-form-label mb-1">Month</label>
                                <select name="month" class="stmt-form-control">
                                    <option value="">--Select Month--</option>
                                    @foreach($months as $num => $mName)
                                        <option value="{{ $num }}" {{ (string)$month === (string)$num || strtolower($month) === strtolower($mName) ? 'selected' : '' }}>
                                            {{ $mName }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="stmt-form-group">
                                <label class="stmt-form-label mb-1">Year</label>
                                <input type="number" name="year" class="stmt-form-control" value="{{ $year ?: date('Y') }}" placeholder="2026">
                            </div>
                            <div class="stmt-filter-btns">
                                <button type="submit" class="btn-blue-primary flex-grow-1 justify-content-center py-2 text-sm">
                                    <i class="fas fa-search me-1"></i> Search
                                </button>
                                <a href="{{ route('school.payroll.account-statement', ['view_all' => 1]) }}" class="btn-blue-light justify-content-center py-2 px-3 text-sm" title="Reset Filters">
                                    <i class="fas fa-redo"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- 3. ACTION TOOLBAR (CHANNELS & EXPORTS) -->
                <div class="stmt-action-bar">
                    <!-- Left: Channel Filter Pills -->
                    <div class="stmt-channel-wrapper">
                        <div class="stmt-channel-title">
                            <i class="fas fa-filter text-primary"></i> Channel Filter:
                        </div>
                        <div class="stmt-channel-pills">
                            <a href="{{ route('school.payroll.account-statement', array_merge(request()->except('payment_method'), ['payment_method' => 'All', 'submit_search' => 1])) }}" 
                               class="channel-tab-pill {{ !$paymentMethodFilter || $paymentMethodFilter === 'All' ? 'active' : '' }}">
                                All Channels
                            </a>
                            <a href="{{ route('school.payroll.account-statement', array_merge(request()->except('payment_method'), ['payment_method' => 'bank', 'submit_search' => 1])) }}" 
                               class="channel-tab-pill {{ $paymentMethodFilter === 'bank' ? 'active' : '' }}">
                                <i class="fas fa-university me-1 text-primary"></i> Bank
                            </a>
                            <a href="{{ route('school.payroll.account-statement', array_merge(request()->except('payment_method'), ['payment_method' => 'cash', 'submit_search' => 1])) }}" 
                               class="channel-tab-pill {{ $paymentMethodFilter === 'cash' ? 'active' : '' }}">
                                <i class="fas fa-money-bill-wave me-1 text-success"></i> Cash
                            </a>
                            <a href="{{ route('school.payroll.account-statement', array_merge(request()->except('payment_method'), ['payment_method' => 'other', 'submit_search' => 1])) }}" 
                               class="channel-tab-pill {{ $paymentMethodFilter === 'other' ? 'active' : '' }}">
                                <i class="fas fa-qrcode me-1" style="color: #9333ea;"></i> UPI / Digital
                            </a>
                        </div>
                    </div>

                    <!-- Right: Export & Print Action Buttons -->
                    <div class="stmt-export-btns">
                        <a href="{{ route('school.payroll.account-statement', array_merge(request()->all(), ['export' => 'pdf'])) }}" target="_blank" class="btn-pdf-export">
                            <i class="fas fa-file-pdf"></i> Download PDF
                        </a>
                        <a href="{{ route('school.payroll.account-statement', array_merge(request()->all(), ['export' => 'excel'])) }}" class="btn-excel-export">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </a>
                        <button type="button" onclick="window.print()" class="btn-print-outline">
                            <i class="fas fa-print"></i> Print
                        </button>
                    </div>
                </div>

                <!-- 4. SUMMARY METRIC CARDS -->
                <div class="metric-grid-4col">
                    <div class="metric-card-box border-start border-4 border-danger">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="metric-card-title">Total Disbursed Outflow</div>
                            <i class="fas fa-arrow-up-right-from-square text-danger opacity-50"></i>
                        </div>
                        <div class="metric-card-val text-danger">₹{{ number_format($totalDisbursed, 2) }}</div>
                    </div>

                    <div class="metric-card-box border-start border-4 border-primary">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="metric-card-title">Direct Bank Transfers</div>
                            <i class="fas fa-university text-primary opacity-50"></i>
                        </div>
                        <div class="metric-card-val text-primary">₹{{ number_format($bankDisbursed, 2) }}</div>
                    </div>

                    <div class="metric-card-box border-start border-4 border-success">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="metric-card-title">Cash Salary Disbursals</div>
                            <i class="fas fa-money-bill-wave text-success opacity-50"></i>
                        </div>
                        <div class="metric-card-val text-success">₹{{ number_format($cashDisbursed, 2) }}</div>
                    </div>

                    <div class="metric-card-box border-start border-4" style="border-color: #9333ea !important;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="metric-card-title">Cheque & Digital / UPI</div>
                            <i class="fas fa-qrcode opacity-50" style="color: #9333ea;"></i>
                        </div>
                        <div class="metric-card-val" style="color: #9333ea;">₹{{ number_format($chequeUpiDisbursed, 2) }}</div>
                    </div>
                </div>

                <!-- 5. TABLE SECTION & LIVE SEARCH TOOLBAR -->
                <div class="stmt-table-card">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 p-3 bg-light border-bottom">
                        <span class="badge bg-white text-slate-800 border px-3 py-2 fs-7 rounded-pill shadow-sm">
                            <i class="fas fa-list text-primary me-1.5"></i> Showing <strong>{{ $statementList->count() }}</strong> statement transaction record(s)
                        </span>

                        <div style="max-width: 280px;" class="w-100">
                            <div class="position-relative">
                                <i class="fas fa-search position-absolute text-muted" style="left: 12px; top: 10px; font-size: 12px;"></i>
                                <input type="text" id="stmtTableSearch" onkeyup="filterStmtTable()" class="form-control form-control-sm ps-4 rounded-pill bg-white" placeholder="Filter in table...">
                            </div>
                        </div>
                    </div>

                    <div class="stmt-table-wrap">
                        <table class="table stmt-data-table align-middle" id="stmtTable">
                            <thead>
                                <tr>
                                    <th class="ps-3">Transaction Date</th>
                                    <th>Reference / Voucher ID</th>
                                    <th>Staff Beneficiary</th>
                                    <th>Department</th>
                                    <th>Disbursal Type</th>
                                    <th>Payment Channel</th>
                                    <th class="text-end pe-3">Debit Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($statementList as $item)
                                    @php
                                        $method = strtolower($item->payment_method);
                                        $badgeClass = ($method === 'bank_transfer' || $method === 'bank') ? 'badge-channel-bank' : (($method === 'cash') ? 'badge-channel-cash' : 'badge-channel-other');
                                    @endphp
                                    <tr>
                                        <td class="ps-3 fw-bold text-slate-800">
                                            <i class="far fa-calendar-alt me-1.5 text-muted"></i>
                                            {{ \Carbon\Carbon::parse($item->payment_date)->format('d M, Y') }}
                                        </td>
                                        <td>
                                            <code class="bg-light px-2.5 py-1 rounded text-primary fw-bold" style="background-color: #f1f5f9;">
                                                {{ $item->reference_no ?: '#PAY-'.str_pad($item->id, 5, '0', STR_PAD_LEFT) }}
                                            </code>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-slate-900">{{ $item->staff?->full_name ?: 'N/A' }}</div>
                                            <small class="text-muted"><i class="far fa-id-badge me-1"></i> {{ $item->staff?->employee_id ?: 'EMP-'.$item->staff_id }}</small>
                                        </td>
                                        <td>
                                            <span class="fw-semibold text-slate-700">{{ $item->staff?->department?->name ?: 'General' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border px-2.5 py-1 rounded-pill fw-semibold">
                                                {{ ucfirst(str_replace('_', ' ', $item->payment_type)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="{{ $badgeClass }}">
                                                <i class="fas fa-wallet me-1"></i> {{ strtoupper(str_replace('_', ' ', $item->payment_method)) }}
                                            </span>
                                        </td>
                                        <td class="text-end pe-3">
                                            <span class="fw-extrabold text-danger fs-6">-₹{{ number_format($item->amount, 2) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center" style="padding: 40px 20px !important;">
                                            <div class="stmt-empty-container">
                                                <div class="stmt-empty-icon">
                                                    <i class="fas fa-receipt"></i>
                                                </div>
                                                <h5 class="fw-bold text-slate-800 mb-2">No Account Statement Entries Found</h5>
                                                <p class="text-muted fs-7 mb-3" style="max-width: 480px; margin: 0 auto 16px auto;">There are no salary disbursal or payment transactions recorded for the selected criteria or employee in this period.</p>
                                                <a href="{{ route('school.payroll.account-statement', ['view_all' => 1]) }}" class="btn btn-sm btn-primary rounded-pill px-4 font-semibold shadow-sm">
                                                    <i class="fas fa-list me-1"></i> View All Transactions
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($statementList->isNotEmpty())
                                <tfoot class="bg-light fw-bold">
                                    <tr>
                                        <td colspan="6" class="ps-3 text-uppercase text-slate-600">Total Disbursed Statement Outflow:</td>
                                        <td class="text-end pe-3 text-danger fs-6 fw-bold">₹{{ number_format($totalDisbursed, 2) }}</td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>

            </div>
        </div>

    @endif

</div>

@section('scripts')
<script>
    function filterStmtTable() {
        var input = document.getElementById("stmtTableSearch");
        var filter = input.value.toLowerCase();
        var table = document.getElementById("stmtTable");
        var trs = table.getElementsByTagName("tr");

        for (var i = 1; i < trs.length - (table.tFoot ? 1 : 0); i++) {
            var rowText = trs[i].textContent.toLowerCase();
            if (rowText.indexOf(filter) > -1) {
                trs[i].style.display = "";
            } else {
                trs[i].style.display = "none";
            }
        }
    }
</script>
@endsection
@endsection
