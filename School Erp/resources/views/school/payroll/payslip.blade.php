@extends('layouts.app')

@section('title', 'Payroll Slip — HR Payroll')

@section('styles')
<style>
    .pslip-container {
        width: 100% !important;
        max-width: 100% !important;
        padding: 24px 28px;
        box-sizing: border-box;
    }

    /* Page Header Title */
    .pslip-page-header {
        margin-bottom: 24px;
    }
    .pslip-page-title {
        font-size: 24px;
        font-weight: 800;
        color: #1e3a8a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
        letter-spacing: -0.4px;
    }

    /* Main Card Base */
    .pslip-card {
        background: #ffffff;
        border: 1.5px solid #bfdbfe;
        border-radius: 18px;
        box-shadow: 0 8px 30px rgba(30, 58, 138, 0.08);
        overflow: hidden;
        margin-bottom: 28px;
        transition: all 0.25s ease;
    }

    /* Card Header (Royal Blue Gradient) */
    .pslip-card-hdr {
        background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
        color: #ffffff;
        padding: 18px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }
    .pslip-card-hdr-title {
        font-size: 16.5px;
        font-weight: 800;
        letter-spacing: 0.3px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .pslip-card-hdr-desc {
        font-size: 12.5px;
        color: #dbeafe;
        margin-top: 4px;
        font-weight: 500;
    }
    .pslip-card-body {
        padding: 28px 32px;
    }

    /* Input Field Group Styles */
    .pslip-field-group {
        margin-bottom: 0;
    }
    .pslip-field-label {
        font-size: 12px;
        font-weight: 800;
        color: #1e3a8a;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        margin-bottom: 8px;
        display: block;
    }
    .pslip-input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }
    .pslip-input-icon {
        position: absolute;
        left: 16px;
        color: #2563eb;
        font-size: 15px;
        pointer-events: none;
        z-index: 5;
    }
    .pslip-field-select {
        width: 100%;
        padding: 12px 16px 12px 46px;
        border: 1.5px solid #93c5fd;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
        color: #1e293b;
        background-color: #ffffff;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%232563eb' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 16px center;
        background-size: 16px 12px;
        appearance: none;
        outline: none;
        transition: all 0.2s ease;
        box-shadow: 0 2px 6px rgba(37, 99, 235, 0.04);
        box-sizing: border-box;
    }
    .pslip-field-input {
        width: 100%;
        padding: 12px 16px 12px 46px;
        border: 1.5px solid #93c5fd;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
        color: #1e293b;
        background-color: #ffffff;
        outline: none;
        transition: all 0.2s ease;
        box-shadow: 0 2px 6px rgba(37, 99, 235, 0.04);
        box-sizing: border-box;
    }
    .pslip-field-select:focus,
    .pslip-field-input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15);
        background-color: #ffffff;
    }

    /* OR Circle Divider */
    .pslip-or-circle {
        width: 42px;
        height: 42px;
        background: #eff6ff;
        border: 2px solid #bfdbfe;
        color: #1e40af;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 8px rgba(37, 99, 235, 0.08);
    }

    /* Action Buttons */
    .pslip-action-group {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }
    .btn-pslip-search {
        background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%) !important;
        color: #ffffff !important;
        border: none !important;
        padding: 13px 36px !important;
        border-radius: 12px !important;
        font-weight: 800 !important;
        font-size: 14.5px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        box-shadow: 0 4px 16px rgba(30, 58, 138, 0.28) !important;
        transition: all 0.2s ease !important;
        cursor: pointer !important;
        text-decoration: none !important;
        min-width: 180px;
    }
    .btn-pslip-search:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 22px rgba(30, 58, 138, 0.4) !important;
        color: #ffffff !important;
    }

    .btn-pslip-clear {
        background: #f1f5f9 !important;
        color: #475569 !important;
        border: 1px solid #cbd5e1 !important;
        padding: 13px 28px !important;
        border-radius: 12px !important;
        font-weight: 700 !important;
        font-size: 14.5px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        text-decoration: none !important;
        transition: all 0.2s ease !important;
        min-width: 140px;
    }
    .btn-pslip-clear:hover {
        background: #e2e8f0 !important;
        color: #1e293b !important;
        transform: translateY(-1px) !important;
    }

    /* Back Link Button */
    .btn-back-search {
        background: #ffffff !important;
        color: #1e40af !important;
        border: 1.5px solid #93c5fd !important;
        padding: 9px 20px !important;
        border-radius: 12px !important;
        font-weight: 700 !important;
        font-size: 13.5px !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        text-decoration: none !important;
        box-shadow: 0 2px 8px rgba(37, 99, 235, 0.05) !important;
        transition: all 0.2s ease !important;
    }
    .btn-back-search:hover {
        background: #eff6ff !important;
        color: #1e3a8a !important;
        border-color: #2563eb !important;
        transform: translateY(-1px) !important;
    }

    /* Employee Profile Hero Card */
    .emp-profile-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border: 1.5px solid #bfdbfe;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(30, 58, 138, 0.06);
        padding: 20px 24px;
        margin-bottom: 24px;
    }
    .emp-avatar-circle {
        width: 54px;
        height: 54px;
        background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
        color: #ffffff;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        box-shadow: 0 4px 14px rgba(30, 58, 138, 0.25);
        flex-shrink: 0;
    }
    .emp-name-title {
        font-size: 19px;
        font-weight: 800;
        color: #1e3a8a;
        margin: 0;
        letter-spacing: -0.3px;
        line-height: 1.2;
    }
    .emp-info-pill {
        background: #ffffff;
        color: #1e40af;
        border: 1px solid #bfdbfe;
        font-size: 12px;
        font-weight: 600;
        padding: 5px 12px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        box-shadow: 0 1px 3px rgba(37, 99, 235, 0.04);
        white-space: nowrap;
    }
    .emp-stat-badge {
        background: #eff6ff;
        border: 1.5px solid #93c5fd;
        border-radius: 12px;
        padding: 10px 18px;
        text-align: center;
        min-width: 140px;
    }
    .emp-stat-label {
        font-size: 11px;
        font-weight: 800;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        margin-bottom: 2px;
    }
    .emp-stat-value {
        font-size: 15px;
        font-weight: 800;
        color: #1e3a8a;
    }

    /* Table Design */
    .pslip-table-wrap {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .pslip-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 13.5px;
        min-width: 780px;
    }
    .pslip-table th {
        background: #eff6ff;
        color: #1e40af;
        font-weight: 700;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 14px 18px;
        border-bottom: 2px solid #bfdbfe;
        white-space: nowrap;
    }
    .pslip-table td {
        padding: 14px 18px;
        color: #1e293b;
        border-bottom: 1px solid #e2e8f0;
        vertical-align: middle;
        white-space: nowrap;
    }
    .pslip-table tbody tr:hover td {
        background-color: #f0f9ff;
    }

    /* Download Button */
    .btn-dl-payslip {
        background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%) !important;
        color: #ffffff !important;
        padding: 7px 16px !important;
        border-radius: 9px !important;
        font-weight: 700 !important;
        font-size: 12.5px !important;
        border: none !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
        text-decoration: none !important;
        box-shadow: 0 3px 10px rgba(30, 58, 138, 0.2) !important;
        transition: all 0.2s ease !important;
        white-space: nowrap;
    }
    .btn-dl-payslip:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 5px 15px rgba(30, 58, 138, 0.35) !important;
        color: #ffffff !important;
    }

    /* Empty State Container */
    .pslip-empty-card {
        padding: 48px 24px;
        text-align: center;
        background: #ffffff;
    }
    .pslip-empty-icon {
        width: 72px;
        height: 72px;
        background: #eff6ff;
        border: 2px solid #bfdbfe;
        color: #2563eb;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        margin-bottom: 18px;
    }
    .pslip-empty-title {
        font-size: 19px;
        font-weight: 800;
        color: #1e3a8a;
        margin-bottom: 8px;
    }
    .pslip-empty-desc {
        font-size: 13.5px;
        color: #64748b;
        max-width: 480px;
        margin: 0 auto 24px auto;
        line-height: 1.5;
    }

    /* Responsive Mobile Media Queries */
    @media (max-width: 768px) {
        .pslip-container {
            padding: 14px 10px !important;
        }
        .pslip-card-hdr {
            padding: 14px 16px !important;
        }
        .pslip-card-body {
            padding: 18px 16px !important;
        }
        .emp-profile-card {
            padding: 16px 14px !important;
        }
        .pslip-table th, .pslip-table td {
            padding: 10px 12px !important;
            font-size: 12px !important;
        }
    }
    @media (max-width: 580px) {
        .pslip-action-group {
            flex-direction: column !important;
            width: 100% !important;
            gap: 10px !important;
        }
        .btn-pslip-search, .btn-pslip-clear {
            width: 100% !important;
            min-width: 100% !important;
        }
        .emp-stat-badge {
            width: 100% !important;
            min-width: 100% !important;
            margin-top: 10px;
        }
        .pslip-empty-card {
            padding: 36px 16px !important;
        }
        .pslip-empty-card .btn-pslip-search {
            width: 100% !important;
        }
    }

    /* DARK MODE OPTIMIZATION RULES */
    body.dark-mode .pslip-page-title {
        color: #60a5fa !important;
    }
    body.dark-mode .pslip-card {
        background: #1e293b !important;
        border-color: #334155 !important;
        color: #f8fafc !important;
    }
    body.dark-mode .pslip-card-hdr {
        background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 100%) !important;
        color: #ffffff !important;
    }
    body.dark-mode .pslip-card-body {
        background: #1e293b !important;
    }
    body.dark-mode .pslip-field-label {
        color: #93c5fd !important;
    }
    body.dark-mode .pslip-input-icon {
        color: #60a5fa !important;
    }
    body.dark-mode .pslip-field-input,
    body.dark-mode .pslip-field-select {
        background-color: #0f172a !important;
        border-color: #334155 !important;
        color: #f8fafc !important;
    }
    body.dark-mode .pslip-field-input:focus,
    body.dark-mode .pslip-field-select:focus {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.25) !important;
    }
    body.dark-mode .pslip-or-circle {
        background: #0f172a !important;
        color: #60a5fa !important;
        border-color: #334155 !important;
    }
    body.dark-mode .btn-pslip-clear {
        background: #0f172a !important;
        color: #94a3b8 !important;
        border-color: #334155 !important;
    }
    body.dark-mode .btn-back-search {
        background: #0f172a !important;
        color: #60a5fa !important;
        border-color: #334155 !important;
    }
    body.dark-mode .emp-profile-card {
        background: #0f172a !important;
        border-color: #334155 !important;
    }
    body.dark-mode .emp-name-title {
        color: #60a5fa !important;
    }
    body.dark-mode .emp-info-pill {
        background: #1e293b !important;
        color: #93c5fd !important;
        border-color: #334155 !important;
    }
    body.dark-mode .emp-stat-badge {
        background: #1e293b !important;
        border-color: #334155 !important;
    }
    body.dark-mode .emp-stat-label {
        color: #94a3b8 !important;
    }
    body.dark-mode .emp-stat-value {
        color: #60a5fa !important;
    }
    body.dark-mode .pslip-table th {
        background: #0f172a !important;
        color: #93c5fd !important;
        border-color: #334155 !important;
    }
    body.dark-mode .pslip-table td {
        color: #cbd5e1 !important;
        border-color: #334155 !important;
    }
    body.dark-mode .pslip-table tbody tr:hover td {
        background-color: #0f172a !important;
    }
    body.dark-mode .pslip-empty-card {
        background: #1e293b !important;
    }
    body.dark-mode .pslip-empty-title {
        color: #60a5fa !important;
    }
    body.dark-mode .pslip-empty-desc {
        color: #94a3b8 !important;
    }
</style>
@endsection

@section('content')
<div class="pslip-container">
    <!-- PAGE TITLE ONLY -->
    <div class="pslip-page-header">
        <h4 class="pslip-page-title">
            <i class="fas fa-file-invoice-dollar" style="color: #2563eb;"></i> Payroll Slip
        </h4>
    </div>

    <!-- Alert Notifications -->
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0 mb-4" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-exclamation-circle fs-5"></i>
                <div>{{ session('error') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 mb-4" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-check-circle fs-5"></i>
                <div>{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(!$isSearchAttempted)
        <!-- STAGE 1: EMPLOYEE SEARCH PAGE -->
        <div class="pslip-card">
            <div class="pslip-card-hdr">
                <div>
                    <div class="pslip-card-hdr-title">
                        <i class="fas fa-search"></i> Find & Search Employee
                    </div>
                    <div class="pslip-card-hdr-desc">
                        Select an employee by Name or enter Employee Code to access paid salary history and payslips.
                    </div>
                </div>
            </div>
            <div class="pslip-card-body">
                <form method="GET" action="{{ route('school.payroll.payslip') }}" id="employeeSearchForm">
                    <div class="row align-items-center g-3">
                        <!-- Select Employee Name Dropdown -->
                        <div class="col-lg-5 col-md-12">
                            <div class="pslip-field-group">
                                <label class="pslip-field-label">Select Employee Name</label>
                                <div class="pslip-input-wrapper">
                                    <span class="pslip-input-icon"><i class="fas fa-user-tie"></i></span>
                                    <select name="staff_id" class="pslip-field-select" onchange="if(this.value) { document.getElementsByName('employee_id')[0].value = ''; }">
                                        <option value="">-- Select Employee Name --</option>
                                        @foreach($staffList as $st)
                                            <option value="{{ $st->id }}" {{ (isset($selectedStaff) && $selectedStaff->id == $st->id) || request('staff_id') == $st->id ? 'selected' : '' }}>
                                                {{ $st->full_name }} ({{ $st->employee_id ?: 'EMP-'.$st->id }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- OR Divider Circle -->
                        <div class="col-lg-2 col-md-12 text-center my-3 my-lg-0">
                            <div class="pslip-or-circle">
                                <span>OR</span>
                            </div>
                        </div>

                        <!-- Enter Employee ID Input -->
                        <div class="col-lg-5 col-md-12">
                            <div class="pslip-field-group">
                                <label class="pslip-field-label">Enter Employee ID</label>
                                <div class="pslip-input-wrapper">
                                    <span class="pslip-input-icon"><i class="fas fa-id-card"></i></span>
                                    <input type="text" name="employee_id" class="pslip-field-input" placeholder="Enter Employee Code (e.g. EMP101) ..." value="{{ $employeeIdInput ?: (request('employee_id')) }}" oninput="if(this.value) { document.getElementsByName('staff_id')[0].value = ''; }">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit & Clear Buttons -->
                    <div class="pslip-action-group mt-4 pt-3" style="border-top: 1px solid #eff6ff;">
                        <button type="submit" class="btn-pslip-search">
                            <i class="fas fa-search me-2"></i>Search Payroll Slips
                        </button>
                        <a href="{{ route('school.payroll.payslip') }}" class="btn-pslip-clear">
                            <i class="fas fa-sync-alt me-2"></i>Reset Filter
                        </a>
                    </div>
                </form>
            </div>
        </div>
    @else
        <!-- STAGE 2: PAYROLL SLIP LIST (ON NEXT STEP / PAGE) -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
            <a href="{{ route('school.payroll.payslip') }}" class="btn-back-search">
                <i class="fas fa-arrow-left"></i> Back to Employee Search
            </a>

            @if(isset($paidPayrolls) && $paidPayrolls->isNotEmpty())
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('school.payroll.payslip', array_merge(request()->all(), ['export' => 'excel'])) }}" class="btn btn-sm btn-light fw-bold text-emerald-700 rounded-3 shadow-sm px-3 py-2 border" style="border-color: #86efac !important; background: #ecfdf5;">
                        <i class="fas fa-file-excel me-1 text-emerald-600"></i> Export Excel
                    </a>
                    <a href="{{ route('school.payroll.payslip', array_merge(request()->all(), ['export' => 'pdf'])) }}" class="btn btn-sm btn-light fw-bold text-rose-700 rounded-3 shadow-sm px-3 py-2 border" style="border-color: #fca5a5 !important; background: #fff1f2;">
                        <i class="fas fa-file-pdf me-1 text-rose-600"></i> Export PDF List
                    </a>
                </div>
            @endif
        </div>

        @if(isset($selectedStaff))
            <!-- Employee Profile Hero Card -->
            <div class="emp-profile-card">
                <div class="row align-items-center g-3">
                    <!-- Left: Avatar Circle -->
                    <div class="col-auto">
                        <div class="emp-avatar-circle">
                            <i class="fas fa-user-tie"></i>
                        </div>
                    </div>

                    <!-- Middle: Employee Name & Metadata Pills -->
                    <div class="col">
                        <h5 class="emp-name-title">{{ $selectedStaff->full_name }}</h5>
                        <div class="d-flex flex-wrap align-items-center gap-2 mt-2">
                            <span class="emp-info-pill">
                                <i class="fas fa-id-badge text-blue-600"></i>
                                <strong class="text-slate-500 ms-1">ID:</strong>
                                <span class="text-blue-900 ms-1 font-bold">{{ $selectedStaff->employee_id ?: 'EMP-'.$selectedStaff->id }}</span>
                            </span>
                            <span class="emp-info-pill">
                                <i class="fas fa-building text-blue-600"></i>
                                <strong class="text-slate-500 ms-1">Department:</strong>
                                <span class="text-blue-900 ms-1 font-bold">{{ $selectedStaff->department?->name ?: 'General' }}</span>
                            </span>
                            <span class="emp-info-pill">
                                <i class="fas fa-user-tag text-blue-600"></i>
                                <strong class="text-slate-500 ms-1">Designation:</strong>
                                <span class="text-blue-900 ms-1 font-bold">{{ $selectedStaff->designation?->name ?: 'Staff' }}</span>
                            </span>
                        </div>
                    </div>

                    <!-- Right: Paid Months Counter Badge -->
                    <div class="col-12 col-md-auto ms-md-auto">
                        <div class="emp-stat-badge">
                            <div class="emp-stat-label">Paid Payslips</div>
                            <div class="emp-stat-value">
                                <i class="fas fa-check-circle me-1 text-emerald-500"></i>{{ $paidPayrolls->count() }} Month(s)
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Pay Slip List Card -->
        <div class="pslip-card">
            <div class="pslip-card-hdr">
                <div class="pslip-card-hdr-title">
                    <i class="fas fa-file-invoice-dollar"></i> Salary Payslips History
                </div>
            </div>

            <div class="p-0">
                @if(isset($paidPayrolls) && $paidPayrolls->isNotEmpty())
                    <div class="pslip-table-wrap">
                        <table class="pslip-table">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 60px;">S.No</th>
                                    <th>Salary Month</th>
                                    <th>Employee ID</th>
                                    <th>Employee Name</th>
                                    <th class="text-end">Gross Salary</th>
                                    <th class="text-end" style="color: #ea580c;">Att. Deduction</th>
                                    <th class="text-end">Total Deduction</th>
                                    <th class="text-end">Net Salary</th>
                                    <th class="text-center">Payment Date</th>
                                    <th class="text-center">Payment Status</th>
                                    <th class="text-center">Download Payslip</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($paidPayrolls as $index => $row)
                                    @php
                                        $st = $row->staff;
                                        $lastPayment = $row->payments?->last();
                                        $payDate = $lastPayment ? \Carbon\Carbon::parse($lastPayment->payment_date)->format('d M Y') : ($row->finalised_at ? $row->finalised_at->format('d M Y') : 'N/A');
                                    @endphp
                                    <tr>
                                        <td class="text-center font-bold text-slate-500">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="fw-bold text-blue-900" style="font-weight: 700; color: #1e3a8a;">{{ $row->payroll_month }}</div>
                                        </td>
                                        <td>
                                            <span class="badge bg-slate-100 text-slate-700 border border-slate-300 font-mono px-2 py-1" style="font-family: monospace;">
                                                {{ $st?->employee_id ?: 'EMP-'.$row->staff_id }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-slate-800" style="font-weight: 700;">{{ $st?->full_name ?: 'N/A' }}</div>
                                            <div class="fs-8 text-slate-500" style="font-size: 11px; color: #64748b;">{{ $st?->designation?->name ?: 'Staff' }} &bull; {{ $st?->department?->name ?: 'General' }}</div>
                                        </td>
                                        <td class="text-end fw-semibold text-slate-700" style="font-weight: 600;">
                                            ₹{{ number_format($row->gross_salary, 2) }}
                                        </td>
                                        <td class="text-end fw-semibold" style="font-weight: 600; color: #ea580c;">
                                            ₹{{ number_format($row->attendance_deduction ?: 0, 2) }}
                                            @if((float)$row->attendance_deduction_days > 0)
                                                <div style="font-size: 10px; color: #64748b; font-weight: 600;">({{ (float)$row->attendance_deduction_days }}d extra)</div>
                                            @endif
                                        </td>
                                        <td class="text-end fw-semibold text-rose-600" style="font-weight: 600; color: #dc2626;">
                                            ₹{{ number_format($row->deductions, 2) }}
                                        </td>
                                        <td class="text-end fw-bold text-blue-900" style="font-weight: 800; font-size: 14.5px; color: #1e3a8a;">
                                            ₹{{ number_format($row->net_payable, 2) }}
                                        </td>
                                        <td class="text-center text-slate-600 font-medium">
                                            <i class="far fa-calendar-alt me-1 text-slate-400"></i>{{ $payDate }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-emerald-100 text-emerald-800 border border-emerald-300 rounded-pill px-3 py-1 font-bold" style="background: #dcfce7; color: #166534; border: 1px solid #86efac; font-weight: 700;">
                                                <i class="fas fa-check-circle me-1"></i> PAID
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('school.payroll.payslip.view', ['id' => $row->id, 'export' => 'pdf']) }}" class="btn-dl-payslip" target="_blank">
                                                <i class="fas fa-download"></i> DOWNLOAD
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <!-- Premium Empty State Box -->
                    <div class="pslip-empty-card">
                        <div class="pslip-empty-icon">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <div class="pslip-empty-title">No Paid Salary Slips Found</div>
                        <div class="pslip-empty-desc">
                            No paid salary records exist for <strong>{{ $selectedStaff?->full_name ?: 'this employee' }}</strong> ({{ $selectedStaff?->employee_id ?: 'EMP-'.$selectedStaff?->id }}) yet. Once salary is disbursed under Salary Payment, paid payslips will appear here.
                        </div>
                        <div>
                            <a href="{{ route('school.payroll.payslip') }}" class="btn-pslip-search">
                                <i class="fas fa-search me-2"></i> Search Another Employee
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
