@extends('layouts.app')

@section('title', 'Salary Structures & Teacher Assignments — HR Payroll')

@section('styles')
<style>
    .sal-container {
        width: 100% !important;
        max-width: 100% !important;
        padding: 24px 30px;
        box-sizing: border-box;
    }
    .sal-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(37, 99, 235, 0.05);
        transition: all 0.25s ease;
        overflow: hidden;
    }
    .sal-card-hdr {
        background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
        color: #ffffff;
        padding: 18px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }
    .sal-config-body {
        padding: 22px 28px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 20px;
    }
    .sal-config-left {
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .sal-card-body {
        padding: 24px;
    }
    .btn-configure-main {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
        color: #ffffff !important;
        border: none !important;
        padding: 11px 24px !important;
        border-radius: 10px !important;
        font-weight: 700 !important;
        font-size: 13.5px !important;
        letter-spacing: 0.2px !important;
        text-decoration: none !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 8px !important;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3) !important;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        cursor: pointer;
    }
    .btn-configure-main:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 20px rgba(37, 99, 235, 0.45) !important;
        color: #ffffff !important;
    }
    
    /* Stats Grid */
    .sal-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 18px;
        margin-bottom: 24px;
    }
    .sal-stat-box {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 18px 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        transition: transform 0.2s ease;
    }
    .sal-stat-box:hover {
        transform: translateY(-2px);
    }
    .sal-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .sal-filter-form {
        display: flex;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
        margin-bottom: 24px;
    }
    .sal-filter-search {
        position: relative;
        flex: 1;
        min-width: 260px;
    }
    .sal-filter-dept {
        min-width: 180px;
        font-weight: 600;
    }
    .sal-table-wrap {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
    }
    .sal-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 13px;
        min-width: 980px;
    }
    .sal-table th {
        background: #f8fafc;
        color: #1e40af;
        font-weight: 700;
        font-size: 11.5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 14px 16px;
        border-bottom: 2px solid #e2e8f0;
        white-space: nowrap;
    }
    .sal-table td {
        padding: 14px 16px;
        color: #1e293b;
        border-bottom: 1px solid #f1f5f9;
        white-space: nowrap;
        vertical-align: middle;
    }
    .sal-table tr:hover td {
        background-color: #f8fafc;
    }

    /* Action Buttons */
    .btn-assign-action {
        padding: 7px 14px;
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        border: none;
        color: #ffffff !important;
        border-radius: 8px;
        font-weight: 700;
        font-size: 12px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 2px 8px rgba(37, 99, 235, 0.25);
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-assign-action:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
        color: #ffffff !important;
    }

    .btn-edit-action {
        padding: 7px 12px;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #2563eb;
        border-radius: 8px;
        font-weight: 700;
        font-size: 12px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: all 0.2s ease;
    }
    .btn-edit-action:hover {
        background: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
    }

    .btn-delete-action {
        padding: 7px 12px;
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #dc2626;
        border-radius: 8px;
        font-weight: 700;
        font-size: 12px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-delete-action:hover {
        background: #dc2626;
        color: #ffffff;
        border-color: #dc2626;
    }

    .sal-input {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        font-size: 13.5px;
        outline: none;
        background: #ffffff;
        color: #1e293b;
        box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        transition: all 0.2s ease;
        box-sizing: border-box;
    }
    .sal-input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }

    /* Badges */
    .badge-assigned-count {
        background: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
        padding: 5px 12px;
        border-radius: 20px;
        font-weight: 700;
        font-size: 12px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .badge-assigned-count:hover {
        background: #059669;
        color: #ffffff;
        border-color: #059669;
    }

    /* ==========================================================================
       SLIDE-OVER DRAWER (SLIDER) STYLES
       ========================================================================== */
    .sal-drawer-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(4px);
        z-index: 99998;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }
    .sal-drawer-backdrop.active {
        opacity: 1;
        visibility: visible;
    }

    .sal-drawer {
        position: fixed;
        top: 0;
        right: -600px;
        width: 100%;
        max-width: 560px;
        height: 100vh;
        background: #ffffff;
        box-shadow: -8px 0 30px rgba(0, 0, 0, 0.2);
        z-index: 99999;
        display: flex;
        flex-direction: column;
        transition: right 0.35s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .sal-drawer.active {
        right: 0;
    }

    .sal-drawer-header {
        padding: 20px 24px;
        background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        flex-shrink: 0;
    }
    .sal-drawer-close {
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.25);
        color: #ffffff;
        width: 34px;
        height: 34px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .sal-drawer-close:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: rotate(90deg);
    }

    .sal-drawer-controls {
        padding: 16px 24px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        flex-shrink: 0;
    }
    .sal-drawer-tabs {
        display: flex;
        gap: 8px;
        margin-top: 12px;
        overflow-x: auto;
        padding-bottom: 2px;
    }
    .sal-tab-btn {
        padding: 6px 14px;
        border-radius: 20px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #475569;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        white-space: nowrap;
        transition: all 0.2s ease;
    }
    .sal-tab-btn.active {
        background: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
    }

    .sal-drawer-body {
        padding: 16px 24px;
        overflow-y: auto;
        flex: 1;
    }

    .sal-staff-card {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px 16px;
        margin-bottom: 10px;
        background: #ffffff;
        display: flex;
        align-items: center;
        gap: 14px;
        cursor: pointer;
        transition: all 0.2s ease;
        user-select: none;
    }
    .sal-staff-card:hover {
        border-color: #93c5fd;
        background: #f0f7ff;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(37,99,235,0.08);
    }
    .sal-staff-card.selected {
        border-color: #2563eb;
        background: #eff6ff;
    }

    .sal-staff-checkbox {
        width: 20px;
        height: 20px;
        cursor: pointer;
        accent-color: #2563eb;
        flex-shrink: 0;
    }

    .sal-staff-avatar {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        background: #eff6ff;
        color: #2563eb;
        font-weight: 800;
        font-size: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border: 1px solid #bfdbfe;
        overflow: hidden;
    }
    .sal-staff-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .sal-staff-info {
        flex: 1;
        min-width: 0;
    }
    .sal-staff-name {
        font-weight: 700;
        font-size: 13.5px;
        color: #0f172a;
        margin-bottom: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .sal-staff-meta {
        font-size: 11.5px;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .status-tag {
        font-size: 11px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        white-space: nowrap;
    }
    .status-tag-this {
        background: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
    }
    .status-tag-other {
        background: #fffbeb;
        color: #d97706;
        border: 1px solid #fde68a;
    }
    .status-tag-unassigned {
        background: #f1f5f9;
        color: #64748b;
        border: 1px solid #e2e8f0;
    }

    .sal-drawer-footer {
        padding: 16px 24px;
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-shrink: 0;
    }

    /* Toast Notification */
    .sal-toast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        padding: 14px 22px;
        border-radius: 12px;
        font-size: 13.5px;
        font-weight: 700;
        z-index: 100000;
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .sal-toast.active {
        opacity: 1;
        transform: translateY(0);
    }
    .sal-toast-success {
        background: #065f46;
        color: #ffffff;
    }
    .sal-toast-error {
        background: #991b1b;
        color: #ffffff;
    }

    /* Dark Mode Overrides */
    body.dark-mode .sal-card,
    body.dark-mode .sal-stat-box,
    body.dark-mode .sal-drawer {
        background: #1e293b !important;
        border-color: #334155 !important;
    }
    body.dark-mode .sal-drawer-controls,
    body.dark-mode .sal-drawer-footer {
        background: #0f172a !important;
        border-color: #334155 !important;
    }
    body.dark-mode .sal-staff-card {
        background: #0f172a !important;
        border-color: #334155 !important;
    }
    body.dark-mode .sal-staff-card.selected {
        background: #1e3a8a !important;
        border-color: #3b82f6 !important;
    }
    body.dark-mode .sal-staff-name {
        color: #f8fafc !important;
    }
    body.dark-mode .sal-tab-btn {
        background: #1e293b !important;
        color: #94a3b8 !important;
        border-color: #334155 !important;
    }
    body.dark-mode .sal-tab-btn.active {
        background: #2563eb !important;
        color: #ffffff !important;
    }
</style>
@endsection

@section('content')
<div class="sal-container">

    @if(session('success'))
        <div style="padding: 14px 18px; background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; border-radius: 12px; font-size: 13.5px; font-weight: 600; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 2px 8px rgba(16,185,129,0.1);">
            <div><i class="fas fa-check-circle" style="margin-right: 8px; font-size: 16px;"></i> {{ session('success') }}</div>
            <button type="button" onclick="this.parentElement.remove()" style="background: none; border: none; color: #065f46; font-size: 16px; cursor: pointer;">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div style="padding: 14px 18px; background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; border-radius: 12px; font-size: 13.5px; font-weight: 600; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 2px 8px rgba(239,68,68,0.1);">
            <div><i class="fas fa-exclamation-circle" style="margin-right: 8px; font-size: 16px;"></i> {{ session('error') }}</div>
            <button type="button" onclick="this.parentElement.remove()" style="background: none; border: none; color: #991b1b; font-size: 16px; cursor: pointer;">&times;</button>
        </div>
    @endif

    <!-- Top Stats Row -->
    <div class="sal-stats-grid">
        <div class="sal-stat-box">
            <div class="sal-stat-icon" style="background: #eff6ff; color: #2563eb;">
                <i class="fas fa-layer-group"></i>
            </div>
            <div>
                <div style="font-size: 11.5px; font-weight: 700; color: #64748b; text-transform: uppercase;">Salary Structures</div>
                <div style="font-size: 22px; font-weight: 800; color: #0f172a;">{{ $totalStructuresCount }}</div>
            </div>
        </div>

        <div class="sal-stat-box">
            <div class="sal-stat-icon" style="background: #ecfdf5; color: #059669;">
                <i class="fas fa-user-check"></i>
            </div>
            <div>
                <div style="font-size: 11.5px; font-weight: 700; color: #64748b; text-transform: uppercase;">Teachers Assigned</div>
                <div style="font-size: 22px; font-weight: 800; color: #059669;">
                    {{ $assignedStaffCount }} <span style="font-size: 13px; font-weight: 600; color: #64748b;">/ {{ $totalStaffCount }} Active</span>
                </div>
            </div>
        </div>

        <div class="sal-stat-box">
            <div class="sal-stat-icon" style="background: #faf5ff; color: #7c3aed;">
                <i class="fas fa-wallet"></i>
            </div>
            <div>
                <div style="font-size: 11.5px; font-weight: 700; color: #64748b; text-transform: uppercase;">Est. Monthly Payroll</div>
                <div style="font-size: 22px; font-weight: 800; color: #7c3aed;">₹{{ number_format($totalMonthlyBudget, 2) }}</div>
            </div>
        </div>
    </div>

    <!-- Configure / Create Structure Card (Main Action Card) -->
    <div class="sal-card" style="margin-bottom: 24px;">
        <div class="sal-config-body">
            <div class="sal-config-left">
                <div style="width: 52px; height: 52px; border-radius: 14px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);">
                    <i class="fas fa-coins"></i>
                </div>
                <div>
                    <h2 class="sal-title" style="font-size: 17px; font-weight: 800; color: #1e3a8a; margin: 0 0 4px 0; letter-spacing: -0.01em;">
                        Salary Structure Master
                    </h2>
                    <p class="sal-subtext" style="font-size: 12.5px; color: #64748b; margin: 0; line-height: 1.4;">
                        Create multiple salary structures with allowances and deductions, then assign teachers & staff via the quick slide-over slider.
                    </p>
                </div>
            </div>
            <div>
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <a href="{{ route('school.payroll.staff-assign') }}" class="btn-configure-main" style="background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%) !important; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3) !important;">
                        <i class="fas fa-users-cog"></i> Assign Staff Salary
                    </a>
                    <a href="{{ route('school.payroll.salary-structure.configure') }}" class="btn-configure-main">
                        <i class="fas fa-plus-circle"></i> Create Salary Structure
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Salary Structures List Card -->
    <div class="sal-card">
        
        <!-- Header Banner (Vibrant Blue) -->
        <div class="sal-card-hdr">
            <div style="font-size: 16px; font-weight: 800; display: flex; align-items: center; gap: 10px; letter-spacing: 0.2px;">
                <i class="fas fa-table-list" style="color: #ffffff;"></i> Salary Structures List
            </div>
            <div style="font-size: 12px; font-weight: 700; color: #1e40af; background: #ffffff; padding: 5px 14px; border-radius: 20px; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">
                <i class="fas fa-layer-group me-1" style="color: #2563eb;"></i> {{ $salaryStructures->total() }} Total Structures
            </div>
        </div>

        <div class="sal-card-body">
            <!-- Filter Bar -->
            <form method="GET" action="{{ route('school.payroll.salary-structure') }}" class="sal-filter-form">
                <div class="sal-filter-search">
                    <i class="fas fa-search" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 14px;"></i>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search salary structure name, type..." class="sal-input" style="padding-left: 38px;">
                </div>

                <div class="sal-filter-dept">
                    <select name="salary_type" class="sal-input" style="font-weight: 600;">
                        <option value="">All Salary Types</option>
                        <option value="Monthly" {{ $typeFilter === 'Monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="Daily" {{ $typeFilter === 'Daily' ? 'selected' : '' }}>Daily</option>
                        <option value="Hourly" {{ $typeFilter === 'Hourly' ? 'selected' : '' }}>Hourly</option>
                        <option value="Contract" {{ $typeFilter === 'Contract' ? 'selected' : '' }}>Contract</option>
                    </select>
                </div>

                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <button type="submit" class="btn-configure-main" style="padding: 10px 22px !important;">
                        <i class="fas fa-filter"></i> Filter
                    </button>

                    @if($search || $typeFilter)
                        <a href="{{ route('school.payroll.salary-structure') }}" style="color: #ef4444; text-decoration: none; font-size: 13px; font-weight: 700; padding: 10px 16px; border: 1px solid #fecaca; background: #fef2f2; border-radius: 10px; display: inline-flex; align-items: center; gap: 6px;">
                            <i class="fas fa-sync-alt"></i> Reset
                        </a>
                    @endif
                </div>
            </form>

            <!-- Table Container -->
            <div class="sal-table-wrap">
                <table class="sal-table">
                    <thead>
                        <tr>
                            <th style="text-align: center; width: 60px;">S.NO</th>
                            <th>STRUCTURE NAME</th>
                            <th style="text-align: right;">BASIC SALARY</th>
                            <th style="text-align: right;">ALLOWANCES</th>
                            <th style="text-align: right;">DEDUCTIONS</th>
                            <th style="text-align: right;">NET SALARY</th>
                            <th style="text-align: center;">SALARY TYPE</th>
                            <th style="text-align: center;">EFFECTIVE FROM</th>
                            <th style="text-align: center;">ASSIGNED TEACHERS</th>
                            <th style="text-align: center;">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($salaryStructures as $index => $struct)
                            @php
                                $assignedCount = $struct->staff_count ?? 0;
                            @endphp
                            <tr id="struct-row-{{ $struct->id }}">
                                <td style="text-align: center; color: #64748b; font-weight: 600;">
                                    {{ $salaryStructures->firstItem() + $index }}
                                </td>
                                <td>
                                    <div style="font-weight: 800; color: #0f172a; font-size: 14px; display: flex; align-items: center; gap: 6px;">
                                        <i class="fas fa-receipt" style="color: #2563eb; font-size: 13px;"></i>
                                        {{ $struct->name }}
                                        @if(!$struct->is_active)
                                            <span style="background: #f1f5f9; color: #64748b; font-size: 10px; padding: 2px 6px; border-radius: 4px; font-weight: 600;">Inactive</span>
                                        @endif
                                    </div>
                                    @if($struct->description)
                                        <div style="font-size: 11.5px; color: #64748b; max-width: 240px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            {{ $struct->description }}
                                        </div>
                                    @endif
                                </td>
                                <td style="text-align: right; font-weight: 800; color: #2563eb; font-size: 13.5px;">
                                    ₹{{ number_format($struct->basic_salary, 2) }}
                                </td>
                                <td style="text-align: right;">
                                    <div style="font-weight: 700; color: #059669;">+₹{{ number_format($struct->total_allowances, 2) }}</div>
                                    <div style="font-size: 10.5px; color: #64748b;">
                                        H:{{ (int)$struct->hra }} | D:{{ (int)$struct->da }} | T:{{ (int)$struct->ta }} | A:{{ (int)$struct->allowance }}
                                    </div>
                                </td>
                                <td style="text-align: right;">
                                    <div style="font-weight: 700; color: #dc2626;">-₹{{ number_format($struct->total_deductions, 2) }}</div>
                                    <div style="font-size: 10.5px; color: #64748b;">
                                        PF:{{ (int)$struct->pf }} | ESI:{{ (int)$struct->esi }} | TDS:{{ (int)$struct->tds }}
                                    </div>
                                </td>
                                <td style="text-align: right; font-weight: 800; color: #1e3a8a; font-size: 14px;">
                                    ₹{{ number_format($struct->net_salary, 2) }}
                                </td>
                                <td style="text-align: center;">
                                    <span class="sal-badge-type" style="background: #eff6ff; color: #1d4ed8; padding: 4px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 700; border: 1px solid #bfdbfe; text-transform: uppercase;">
                                        {{ $struct->salary_type }}
                                    </span>
                                </td>
                                <td style="text-align: center; font-weight: 600; font-size: 12.5px;">
                                    {{ $struct->effective_from ? \Carbon\Carbon::parse($struct->effective_from)->format('d M Y') : 'N/A' }}
                                </td>
                                <td style="text-align: center;">
                                    <a href="{{ route('school.payroll.staff-assign', ['salary_structure_id' => $struct->id]) }}" class="badge-assigned-count" title="Click to view & assign staff" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                                        <i class="fas fa-users"></i>
                                        <span id="badge-count-{{ $struct->id }}">{{ $assignedCount }} Assigned</span>
                                    </a>
                                </td>
                                <td style="text-align: center;">
                                    <div style="display: inline-flex; align-items: center; gap: 6px;">
                                        <a href="{{ route('school.payroll.salary-structure.configure', ['id' => $struct->id]) }}" class="btn-edit-action" title="Edit Structure">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn-delete-action" onclick="confirmDeleteStructure({{ $struct->id }}, '{{ addslashes($struct->name) }}')" title="Delete Structure">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" style="padding: 50px 20px; text-align: center; color: #64748b;">
                                    <div style="max-width: 380px; margin: 0 auto;">
                                        <div style="width: 64px; height: 64px; border-radius: 50%; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 28px; margin: 0 auto 16px auto;">
                                            <i class="fas fa-folder-plus"></i>
                                        </div>
                                        <div class="sal-title" style="font-weight: 800; font-size: 15px; color: #1e293b; margin-bottom: 6px;">No Salary Structures Created Yet</div>
                                        <div class="sal-subtext" style="font-size: 12.5px; color: #64748b; line-height: 1.5; margin-bottom: 16px;">
                                            Click on "Create Salary Structure" above to set up your first salary structure and assign teachers.
                                        </div>
                                        <a href="{{ route('school.payroll.salary-structure.configure') }}" class="btn-configure-main">
                                            <i class="fas fa-plus"></i> Create First Structure
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($salaryStructures->hasPages())
                <div style="margin-top: 24px; display: flex; justify-content: flex-end;">
                    {{ $salaryStructures->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- ==========================================================================
     SLIDE-OVER DRAWER (SLIDER) FOR TEACHER ASSIGNMENT
     ========================================================================== -->
<div class="sal-drawer-backdrop" id="drawerBackdrop" onclick="closeAssignDrawer()"></div>

<div class="sal-drawer" id="assignDrawer">
    <!-- Drawer Header -->
    <div class="sal-drawer-header">
        <div>
            <div style="font-size: 16px; font-weight: 800; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-user-check"></i>
                <span>Assign Teachers & Staff</span>
            </div>
            <div style="font-size: 12px; color: #bfdbfe; font-weight: 600; margin-top: 3px;" id="drawerStructureSubtitle">
                Loading structure details...
            </div>
        </div>
        <button type="button" class="sal-drawer-close" onclick="closeAssignDrawer()">&times;</button>
    </div>

    <!-- Drawer Controls (Search & Filters) -->
    <div class="sal-drawer-controls">
        <div style="display: flex; gap: 10px;">
            <div style="position: relative; flex: 1;">
                <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 13px;"></i>
                <input type="text" id="drawerSearch" placeholder="Search teacher by name or EMP ID..." class="sal-input" style="padding-left: 34px; font-size: 13px;">
            </div>
            <div style="width: 170px;">
                <select id="drawerDeptFilter" class="sal-input" style="font-size: 13px; font-weight: 600;">
                    <option value="">All Departments</option>
                </select>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="sal-drawer-tabs">
            <button type="button" class="sal-tab-btn active" data-tab="all" onclick="filterDrawerTab('all')">
                All (<span id="tab-count-all">0</span>)
            </button>
            <button type="button" class="sal-tab-btn" data-tab="assigned_this" onclick="filterDrawerTab('assigned_this')">
                Assigned to this (<span id="tab-count-this">0</span>)
            </button>
            <button type="button" class="sal-tab-btn" data-tab="assigned_other" onclick="filterDrawerTab('assigned_other')">
                Assigned to Others (<span id="tab-count-other">0</span>)
            </button>
            <button type="button" class="sal-tab-btn" data-tab="unassigned" onclick="filterDrawerTab('unassigned')">
                Unassigned (<span id="tab-count-unassigned">0</span>)
            </button>
        </div>
    </div>

    <!-- Drawer Body (Staff List) -->
    <div class="sal-drawer-body" id="drawerStaffList">
        <div style="text-align: center; padding: 40px 20px; color: #64748b;">
            <i class="fas fa-spinner fa-spin fa-2x" style="color: #2563eb; margin-bottom: 12px;"></i>
            <div style="font-weight: 700; font-size: 14px;">Loading Staff Data...</div>
        </div>
    </div>

    <!-- Drawer Footer -->
    <div class="sal-drawer-footer">
        <div style="display: flex; gap: 8px;">
            <button type="button" class="btn-edit-action" onclick="selectAllFiltered(true)">
                <i class="fas fa-check-double"></i> Select All
            </button>
            <button type="button" class="btn-delete-action" onclick="selectAllFiltered(false)">
                <i class="fas fa-times"></i> Clear
            </button>
        </div>
        <div style="display: flex; gap: 10px; align-items: center;">
            <div style="font-size: 12px; font-weight: 700; color: #64748b;">
                <span id="selectedCounterText" style="color: #2563eb; font-weight: 800;">0</span> Selected
            </div>
            <button type="button" class="btn-configure-main" id="btnSaveAssignments" onclick="saveAssignments()" style="padding: 9px 20px !important; font-size: 13px !important;">
                <i class="fas fa-save"></i> Save Assignments
            </button>
        </div>
    </div>
</div>
<!-- Toast Alert -->
<div class="sal-toast sal-toast-success" id="salToast">
    <i class="fas fa-check-circle" id="salToastIcon" style="font-size: 18px;"></i>
    <span id="salToastMsg">Success</span>
</div>

@endsection

@section('scripts')
<script>
    let currentStructureId = null;
    let allStaffData = [];
    let selectedStaffIds = new Set();
    let currentActiveTab = 'all';

    const drawerBackdrop = document.getElementById('drawerBackdrop');
    const assignDrawer = document.getElementById('assignDrawer');
    const drawerStaffList = document.getElementById('drawerStaffList');
    const drawerSearch = document.getElementById('drawerSearch');
    const drawerDeptFilter = document.getElementById('drawerDeptFilter');
    const drawerStructureSubtitle = document.getElementById('drawerStructureSubtitle');
    const selectedCounterText = document.getElementById('selectedCounterText');
    const btnSaveAssignments = document.getElementById('btnSaveAssignments');

    // Open Slide-Over Drawer
    function openAssignDrawer(structureId) {
        currentStructureId = structureId;
        if (drawerBackdrop) drawerBackdrop.classList.add('active');
        if (assignDrawer) assignDrawer.classList.add('active');
        document.body.style.overflow = 'hidden';

        if (drawerStaffList) {
            drawerStaffList.innerHTML = `
                <div style="text-align: center; padding: 50px 20px; color: #64748b;">
                    <i class="fas fa-spinner fa-spin fa-2x" style="color: #2563eb; margin-bottom: 12px;"></i>
                    <div style="font-weight: 700; font-size: 14px;">Loading Staff and Teachers...</div>
                </div>
            `;
        }

        fetch(`{{ url('school/payroll/salary-structure') }}/${structureId}/staff`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const struct = data.structure;
                if (drawerStructureSubtitle) {
                    drawerStructureSubtitle.innerHTML = `
                        <span style="color:#ffffff; font-weight:700;">${struct.name}</span> &bull; 
                        Basic: <span style="color:#93c5fd; font-weight:800;">${struct.formatted_basic}</span> &bull; 
                        Net: <span style="color:#86efac; font-weight:800;">${struct.formatted_net}</span> (${struct.salary_type})
                    `;
                }

                // Populate departments filter
                if (drawerDeptFilter) {
                    drawerDeptFilter.innerHTML = '<option value="">All Departments</option>';
                    if (data.departments && Array.isArray(data.departments)) {
                        data.departments.forEach(d => {
                            drawerDeptFilter.innerHTML += `<option value="${d.id}">${d.name}</option>`;
                        });
                    }
                }

                allStaffData = data.staff || [];
                selectedStaffIds = new Set(
                    allStaffData.filter(s => s.is_assigned_to_this).map(s => s.id)
                );

                updateTabCounts();
                filterDrawerTab('all');
            } else {
                if (drawerStaffList) drawerStaffList.innerHTML = `<div style="color: #dc2626; padding: 20px; text-align: center;">Failed to load data.</div>`;
            }
        })
        .catch(err => {
            console.error(err);
            if (drawerStaffList) drawerStaffList.innerHTML = `<div style="color: #dc2626; padding: 20px; text-align: center;">Error loading data. Please try again.</div>`;
        });
    }

    // Close Slide-Over Drawer
    function closeAssignDrawer() {
        if (drawerBackdrop) drawerBackdrop.classList.remove('active');
        if (assignDrawer) assignDrawer.classList.remove('active');
        document.body.style.overflow = '';
    }

    // Update Tab Counts
    function updateTabCounts() {
        const total = allStaffData.length;
        const assignedThis = allStaffData.filter(s => selectedStaffIds.has(s.id)).length;
        const assignedOther = allStaffData.filter(s => !selectedStaffIds.has(s.id) && s.status === 'assigned_other').length;
        const unassigned = allStaffData.filter(s => !selectedStaffIds.has(s.id) && s.status !== 'assigned_other').length;

        const countAllEl = document.getElementById('tab-count-all');
        const countThisEl = document.getElementById('tab-count-this');
        const countOtherEl = document.getElementById('tab-count-other');
        const countUnassignedEl = document.getElementById('tab-count-unassigned');

        if (countAllEl) countAllEl.innerText = total;
        if (countThisEl) countThisEl.innerText = assignedThis;
        if (countOtherEl) countOtherEl.innerText = assignedOther;
        if (countUnassignedEl) countUnassignedEl.innerText = unassigned;
        if (selectedCounterText) selectedCounterText.innerText = selectedStaffIds.size;
    }

    // Switch Tabs
    function filterDrawerTab(tab) {
        currentActiveTab = tab;
        document.querySelectorAll('.sal-tab-btn').forEach(btn => {
            btn.classList.toggle('active', btn.getAttribute('data-tab') === tab);
        });
        renderStaffList();
    }

    // Render Staff List
    function renderStaffList() {
        if (!drawerStaffList) return;
        const searchTerm = (drawerSearch ? drawerSearch.value : '').toLowerCase().trim();
        const selectedDept = drawerDeptFilter ? drawerDeptFilter.value : '';

        const filtered = allStaffData.filter(staff => {
            const isSelected = selectedStaffIds.has(staff.id);
            const matchesSearch = !searchTerm || 
                (staff.name && staff.name.toLowerCase().includes(searchTerm)) || 
                (staff.employee_id && staff.employee_id.toLowerCase().includes(searchTerm));
            const matchesDept = !selectedDept || String(staff.department_id) === String(selectedDept);

            if (!matchesSearch || !matchesDept) return false;

            if (currentActiveTab === 'assigned_this') return isSelected;
            if (currentActiveTab === 'assigned_other') return !isSelected && staff.status === 'assigned_other';
            if (currentActiveTab === 'unassigned') return !isSelected && staff.status !== 'assigned_other';
            return true;
        });

        if (filtered.length === 0) {
            drawerStaffList.innerHTML = `
                <div style="text-align: center; padding: 40px 20px; color: #64748b;">
                    <i class="fas fa-user-slash fa-2x" style="color: #cbd5e1; margin-bottom: 10px;"></i>
                    <div style="font-weight: 700; font-size: 13.5px; color: #475569;">No teachers found matching filter.</div>
                </div>
            `;
            return;
        }

        let html = '';
        filtered.forEach(staff => {
            const isChecked = selectedStaffIds.has(staff.id);
            
            let badgeHtml = '';
            if (isChecked) {
                badgeHtml = `<span class="status-tag status-tag-this"><i class="fas fa-check-circle"></i> Assigned Here</span>`;
            } else if (staff.status === 'assigned_other') {
                badgeHtml = `<span class="status-tag status-tag-other" title="Already assigned to another structure"><i class="fas fa-exclamation-triangle"></i> Assigned: ${escapeHtml(staff.assigned_structure_name)}</span>`;
            } else {
                badgeHtml = `<span class="status-tag status-tag-unassigned"><i class="fas fa-minus-circle"></i> Unassigned</span>`;
            }

            const initial = staff.name ? staff.name.charAt(0).toUpperCase() : 'T';
            const avatarHtml = staff.avatar 
                ? `<img src="${staff.avatar}" alt="${escapeHtml(staff.name)}">`
                : initial;

            html += `
                <div class="sal-staff-card ${isChecked ? 'selected' : ''}" onclick="toggleStaffSelection(${staff.id})">
                    <input type="checkbox" class="sal-staff-checkbox" id="staff_chk_${staff.id}" ${isChecked ? 'checked' : ''} onclick="event.stopPropagation(); toggleStaffSelection(${staff.id})">
                    <div class="sal-staff-avatar">
                        ${avatarHtml}
                    </div>
                    <div class="sal-staff-info">
                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                            <div class="sal-staff-name">${escapeHtml(staff.name)}</div>
                            ${badgeHtml}
                        </div>
                        <div class="sal-staff-meta">
                            <span style="color: #2563eb; font-weight: 700; font-family: monospace;">${escapeHtml(staff.employee_id)}</span>
                            <span>&bull;</span>
                            <span>${escapeHtml(staff.designation_name)}</span>
                            <span>&bull;</span>
                            <span>${escapeHtml(staff.department_name)}</span>
                        </div>
                    </div>
                </div>
            `;
        });

        drawerStaffList.innerHTML = html;
        updateTabCounts();
    }

    // Toggle Staff Selection
    function toggleStaffSelection(staffId) {
        if (selectedStaffIds.has(staffId)) {
            selectedStaffIds.delete(staffId);
        } else {
            selectedStaffIds.add(staffId);
        }
        renderStaffList();
    }

    // Select All Filtered / Deselect All
    function selectAllFiltered(selectAll) {
        const searchTerm = (drawerSearch ? drawerSearch.value : '').toLowerCase().trim();
        const selectedDept = drawerDeptFilter ? drawerDeptFilter.value : '';

        allStaffData.forEach(staff => {
            const matchesSearch = !searchTerm || 
                (staff.name && staff.name.toLowerCase().includes(searchTerm)) || 
                (staff.employee_id && staff.employee_id.toLowerCase().includes(searchTerm));
            const matchesDept = !selectedDept || String(staff.department_id) === String(selectedDept);

            if (matchesSearch && matchesDept) {
                if (selectAll) {
                    selectedStaffIds.add(staff.id);
                } else {
                    selectedStaffIds.delete(staff.id);
                }
            }
        });

        renderStaffList();
    }

    // Save Assignments via AJAX
    function saveAssignments() {
        if (!currentStructureId) return;

        if (btnSaveAssignments) {
            btnSaveAssignments.disabled = true;
            btnSaveAssignments.innerHTML = `<i class="fas fa-spinner fa-spin"></i> Saving...`;
        }

        const payload = {
            _token: '{{ csrf_token() }}',
            staff_ids: Array.from(selectedStaffIds)
        };

        fetch(`{{ url('school/payroll/salary-structure') }}/${currentStructureId}/assign-staff`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if (btnSaveAssignments) {
                btnSaveAssignments.disabled = false;
                btnSaveAssignments.innerHTML = `<i class="fas fa-save"></i> Save Assignments`;
            }

            if (data.success) {
                showToast(data.message, true);
                // Update badge count in main table row
                const badgeEl = document.getElementById(`badge-count-${currentStructureId}`);
                if (badgeEl) {
                    badgeEl.innerText = `${data.assigned_count} Assigned`;
                }
                closeAssignDrawer();
                setTimeout(() => {
                    window.location.reload();
                }, 800);
            } else {
                showToast(data.message || 'Error saving assignments', false);
            }
        })
        .catch(err => {
            console.error(err);
            if (btnSaveAssignments) {
                btnSaveAssignments.disabled = false;
                btnSaveAssignments.innerHTML = `<i class="fas fa-save"></i> Save Assignments`;
            }
            showToast('Failed to save assignments. Please check connection.', false);
        });
    }

    // Delete Structure
    function confirmDeleteStructure(structureId, structureName) {
        if (!confirm(`Are you sure you want to delete "${structureName}"?\n\nAll assigned teachers will be unassigned from this structure.`)) {
            return;
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ url('school/payroll/salary-structure') }}/${structureId}/delete`;
        
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        form.appendChild(csrf);

        document.body.appendChild(form);
        form.submit();
    }

    // Toast Alert
    function showToast(message, isSuccess = true) {
        const toast = document.getElementById('salToast');
        const msgEl = document.getElementById('salToastMsg');
        const iconEl = document.getElementById('salToastIcon');

        if (!toast) return;

        toast.className = `sal-toast ${isSuccess ? 'sal-toast-success' : 'sal-toast-error'}`;
        if (iconEl) iconEl.className = isSuccess ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
        if (msgEl) msgEl.innerText = message;

        toast.classList.add('active');
        setTimeout(() => {
            toast.classList.remove('active');
        }, 3500);
    }

    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // Bind functions to window so inline onclick handlers always work
    window.openAssignDrawer = openAssignDrawer;
    window.closeAssignDrawer = closeAssignDrawer;
    window.filterDrawerTab = filterDrawerTab;
    window.renderStaffList = renderStaffList;
    window.toggleStaffSelection = toggleStaffSelection;
    window.selectAllFiltered = selectAllFiltered;
    window.saveAssignments = saveAssignments;
    window.confirmDeleteStructure = confirmDeleteStructure;
    window.showToast = showToast;

    // Drawer Event Listeners
    if (drawerSearch) drawerSearch.addEventListener('input', renderStaffList);
    if (drawerDeptFilter) drawerDeptFilter.addEventListener('change', renderStaffList);
</script>
@endsection
