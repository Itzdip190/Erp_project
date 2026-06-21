@extends('layouts.app')

@section('title', 'Daily MIS Report')
@section('page-title', 'Daily MIS Report')

@section('styles')
<style>
/* ═══════════════════════════════════════════════════════════════
   DAILY MIS REPORT — Premium Blue & White Theme
   Designed for Visual Excellence, Micro-interactions, & Responsiveness
═══════════════════════════════════════════════════════════════ */
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

.mis-page {
    font-family: 'Plus Jakarta Sans', sans-serif;
    background: #f4f7fc;
    color: #1e293b;
    padding-bottom: 48px;
}

/* ── HEADER ───────────────────────────────────────────────── */
.mis-header {
    background: linear-gradient(135deg, #0d2d6e 0%, #1e3a8a 50%, #2563eb 100%);
    border-radius: 16px;
    padding: 24px 28px;
    margin-bottom: 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 8px 30px rgba(37, 99, 235, 0.15);
    position: relative;
    overflow: hidden;
}
.mis-header::before {
    content: '';
    position: absolute;
    top: -50px;
    right: -50px;
    width: 200px;
    height: 200px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.05);
}
.mis-header h1 {
    font-size: 24px;
    font-weight: 800;
    color: #fff;
    margin: 0 0 4px;
    letter-spacing: -0.5px;
}
.mis-header p {
    font-size: 13px;
    color: rgba(255, 255, 255, 0.8);
    margin: 0;
}
.mis-header-right {
    display: flex;
    gap: 12px;
    align-items: center;
    position: relative;
    z-index: 2;
}
.mis-date-input {
    background: rgba(255, 255, 255, 0.15);
    border: 1.5px solid rgba(255, 255, 255, 0.25);
    color: #fff;
    padding: 10px 16px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    backdrop-filter: blur(4px);
}
.mis-date-input:focus {
    outline: none;
    border-color: rgba(255, 255, 255, 0.7);
    background: rgba(255, 255, 255, 0.2);
}
.mis-date-input::-webkit-calendar-picker-indicator {
    filter: invert(1);
    cursor: pointer;
}
.mis-go-btn {
    background: #fff;
    color: #1e3a8a;
    border: none;
    padding: 10px 22px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}
.mis-go-btn:hover {
    background: #f1f5f9;
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
}
.mis-print-btn {
    background: rgba(255, 255, 255, 0.12);
    border: 1.5px solid rgba(255, 255, 255, 0.3);
    color: #fff;
    padding: 10px 18px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}
.mis-print-btn:hover {
    background: rgba(255, 255, 255, 0.22);
    transform: translateY(-1px);
}

/* ── ROW 1: KPI GRID ──────────────────────────────────────── */
.mis-kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}
.mis-kpi-card {
    background: #fff;
    border-radius: 16px;
    padding: 20px 24px;
    box-shadow: 0 4px 16px rgba(30, 58, 138, 0.04);
    border: 1px solid #e2e8f0;
    position: relative;
    overflow: hidden;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: default;
    display: flex;
    align-items: center;
    gap: 16px;
}
.mis-kpi-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(30, 58, 138, 0.08);
    border-color: #cbd5e1;
}
.mis-kpi-icon-wrapper {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}
.k-rev .mis-kpi-icon-wrapper { background: #ecfdf5; color: #059669; }
.k-stu .mis-kpi-icon-wrapper { background: #fff7ed; color: #d97706; }
.k-stf .mis-kpi-icon-wrapper { background: #fdf2f8; color: #db2777; }
.k-adm .mis-kpi-icon-wrapper { background: #eff6ff; color: #2563eb; }

.mis-kpi-info {
    flex-grow: 1;
}
.mis-kpi-label {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #64748b;
    margin-bottom: 4px;
}
.mis-kpi-value {
    font-size: 26px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.1;
    letter-spacing: -0.5px;
}
.mis-kpi-sub {
    font-size: 11px;
    color: #64748b;
    margin-top: 4px;
}

/* ── ROW 2: IMMEDIATE ACTIONS REQUIRED ───────────────────── */
.mis-section-label {
    font-size: 12px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: #475569;
    margin: 24px 0 12px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.mis-section-label i {
    font-size: 14px;
}
.mis-section-label.alert-label i { color: #ef4444; }

.mis-alerts-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}
.mis-alert-card {
    background: #fff;
    border-radius: 16px;
    padding: 16px 20px;
    box-shadow: 0 4px 16px rgba(30, 58, 138, 0.04);
    border: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: all 0.25s ease;
    cursor: pointer;
}
.mis-alert-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(30, 58, 138, 0.06);
    border-color: #cbd5e1;
}
.mis-alert-left {
    display: flex;
    align-items: center;
    gap: 12px;
}
.mis-alert-icon {
    font-size: 18px;
}
.alert-red { color: #ef4444; }
.alert-orange { color: #f97316; }

.mis-alert-title {
    font-size: 13.5px;
    font-weight: 700;
    color: #1e293b;
}
.mis-alert-desc {
    font-size: 11px;
    color: #64748b;
    margin-top: 1px;
}
.mis-alert-badge {
    padding: 4px 10px;
    border-radius: 30px;
    font-size: 13px;
    font-weight: 800;
    color: #fff;
    min-width: 32px;
    text-align: center;
}
.mis-alert-badge.badge-red { background: #ef4444; }
.mis-alert-badge.badge-orange { background: #f97316; }

/* ── 3-COLUMN METRICS BREAKDOWN ──────────────────────────── */
.mis-three-col {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}
.mis-col-panel {
    background: #fff;
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 4px 16px rgba(30, 58, 138, 0.04);
    border: 1px solid #e2e8f0;
    display: flex;
    flex-direction: column;
}
.mis-col-hdr {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 800;
    color: #1e3a8a;
    padding-bottom: 12px;
    border-bottom: 1.5px solid #f1f5f9;
    margin-bottom: 14px;
}
.mis-col-hdr i {
    font-size: 16px;
    color: #3b82f6;
}
.mis-metric-group {
    margin-bottom: 16px;
}
.mis-group-title {
    font-size: 11.5px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.gt-green { color: #10b981; }
.gt-red { color: #b45309; }
.gt-orange { color: #f97316; }
.gt-blue { color: #2563eb; }

.mis-metric-row {
    display: flex;
    justify-content: space-between;
    font-size: 12.5px;
    padding: 6px 0;
    border-bottom: 1px dashed #f1f5f9;
}
.mis-metric-row:last-child {
    border-bottom: none;
}
.mis-metric-row span {
    color: #475569;
    font-weight: 500;
}
.mis-metric-row strong {
    color: #0f172a;
    font-weight: 700;
}
.mis-profit-box {
    background: #eff6ff;
    border-radius: 10px;
    padding: 10px 14px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: auto;
}
.mis-profit-box span {
    font-size: 12.5px;
    font-weight: 700;
    color: #1e40af;
}
.mis-profit-box strong {
    font-size: 15px;
    font-weight: 800;
    color: #1d4ed8;
}

.pending-red {
    color: #ef4444 !important;
}

.low-activity-box {
    background: #fff7ed;
    border: 1px solid #fed7aa;
    border-radius: 10px;
    padding: 10px 14px;
    margin-top: auto;
    font-size: 11.5px;
    color: #c2410c;
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.low-activity-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-weight: 600;
}
.low-activity-item i {
    font-size: 12px;
}

/* ── 2-COLUMN DETAILS ROW ────────────────────────────────── */
.mis-two-col {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-bottom: 24px;
}
.mis-detail-card {
    background: #fff;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 16px rgba(30, 58, 138, 0.04);
    border: 1px solid #e2e8f0;
}
.mis-detail-hdr {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}
.mis-detail-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 15px;
    font-weight: 800;
    color: #1e3b8b;
}
.mis-detail-title i {
    font-size: 16px;
}
.mis-detail-meta {
    font-size: 11px;
    color: #64748b;
    font-weight: 600;
}

/* Attendance Blocks */
.mis-att-block-wrapper {
    margin-bottom: 20px;
}
.mis-att-sub-label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    color: #64748b;
    margin-bottom: 8px;
}
.mis-att-row {
    display: flex;
    gap: 8px;
    margin-bottom: 12px;
}
.mis-att-box {
    flex: 1;
    border-radius: 10px;
    padding: 10px;
    text-align: center;
    border: 1px solid #e2e8f0;
}
.mis-att-box-val {
    font-size: 18px;
    font-weight: 800;
    color: #0f172a;
}
.mis-att-box-lbl {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    color: #64748b;
    margin-top: 2px;
}
.ab-green  { background: #f0fdf4; border-color: #bbf7d0; color: #166534; }
.ab-red    { background: #fef2f2; border-color: #fecaca; color: #991b1b; }
.ab-orange { background: #fff7ed; border-color: #fed7aa; color: #c2410c; }
.ab-blue   { background: #eff6ff; border-color: #bfdbfe; color: #1e40af; }
.ab-pink   { background: #fdf2f8; border-color: #fbcfe8; color: #9d174d; }

.critical-issues-box {
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 10px;
    padding: 12px 16px;
    font-size: 12px;
    color: #991b1b;
}
.critical-hdr {
    display: flex;
    align-items: center;
    gap: 6px;
    font-weight: 700;
    margin-bottom: 6px;
}
.critical-issues-box ul {
    margin: 0;
    padding-left: 18px;
}
.critical-issues-box li {
    margin-bottom: 4px;
}

/* Today's Fee Collection Details */
.mis-mode-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
    margin-bottom: 20px;
}
.mis-mode-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 10px;
    text-align: center;
}
.mis-mode-card .val {
    font-size: 15px;
    font-weight: 800;
    color: #0f172a;
}
.mis-mode-card .lbl {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    color: #64748b;
    margin-top: 2px;
}
.mis-mode-card.total-mode {
    background: #eff6ff;
    border-color: #bfdbfe;
}
.mis-mode-card.total-mode .val {
    color: #1e40af;
}

.mis-def-aging-hdr {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    color: #64748b;
    margin-bottom: 8px;
}
.mis-def-aging-row {
    display: flex;
    gap: 6px;
    margin-bottom: 20px;
}
.mis-def-aging-box {
    flex: 1;
    border-radius: 10px;
    padding: 8px;
    text-align: center;
    color: #fff;
    font-weight: 700;
}
.mis-def-aging-box .val {
    font-size: 16px;
    font-weight: 800;
}
.mis-def-aging-box .lbl {
    font-size: 9px;
    text-transform: uppercase;
    opacity: 0.9;
    margin-top: 1px;
}
.db-yellow { background: #eab308; }
.db-orange { background: #f97316; }
.db-pink   { background: #ec4899; }
.db-red    { background: #ef4444; }

.mis-fee-card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top: 1.5px solid #f1f5f9;
    padding-top: 14px;
}
.mis-fc-left {
    display: flex;
    align-items: center;
    gap: 6px;
}
.mis-fc-left span {
    font-size: 12px;
    font-weight: 700;
    color: #475569;
}
.mis-discount-badge {
    background: #ffedd5;
    color: #ea580c;
    font-size: 11px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 20px;
    border: 1px solid #fed7aa;
}
.mis-fc-right {
    font-size: 13.5px;
    font-weight: 700;
    color: #475569;
}
.mis-fc-right strong {
    font-size: 15px;
    font-weight: 800;
    color: #0f172a;
}

/* ── BOTTOM SECTION: FOLLOW-UPS & ALERTS ────────────────── */
.mis-follow-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}
.mis-follow-card {
    background: #fff;
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 4px 16px rgba(30, 58, 138, 0.04);
    border: 1px solid #e2e8f0;
}
.mis-follow-hdr {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 800;
    color: #1e3a8a;
    padding-bottom: 12px;
    border-bottom: 1.5px solid #f1f5f9;
    margin-bottom: 14px;
}
.mis-follow-hdr.red-hdr i { color: #ef4444; }
.mis-follow-hdr.green-hdr i { color: #10b981; }

.mis-alert-list-box {
    background: #fdf2f8;
    border: 1px solid #fbcfe8;
    border-radius: 10px;
    padding: 12px 14px;
    margin-bottom: 12px;
}
.mis-alert-list-box.orange-box {
    background: #fff7ed;
    border-color: #fed7aa;
}
.mis-alert-list-box.blue-box {
    background: #eff6ff;
    border-color: #bfdbfe;
}
.mis-alb-title {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 800;
    color: #be185d;
    margin-bottom: 6px;
}
.orange-box .mis-alb-title { color: #c2410c; }
.blue-box .mis-alb-title { color: #1e40af; }

.mis-alb-title i {
    font-size: 13px;
}
.mis-alb-list {
    margin: 0 0 6px;
    padding-left: 16px;
    font-size: 12px;
    color: #475569;
    font-weight: 600;
}
.mis-alb-list li {
    margin-bottom: 4px;
}
.mis-alb-more-link {
    font-size: 11px;
    font-weight: 700;
    color: #ea580c;
    text-decoration: underline;
    display: inline-block;
    cursor: pointer;
    transition: color 0.15s ease;
}
.mis-alb-more-link:hover {
    color: #c2410c;
}

/* ── FOOTER ───────────────────────────────────────────────── */
.mis-footer {
    text-align: center;
    padding: 20px;
    color: #94a3b8;
    font-size: 12px;
    margin-top: 12px;
}

/* ── ANIMATIONS & HOVERS ───────────────────────────────────── */
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(12px); }
    to { opacity: 1; transform: translateY(0); }
}
.mis-page > * {
    animation: fadeInUp 0.4s ease both;
}
.mis-kpi-card:nth-child(1) { animation-delay: 0.05s; }
.mis-kpi-card:nth-child(2) { animation-delay: 0.1s; }
.mis-kpi-card:nth-child(3) { animation-delay: 0.15s; }
.mis-kpi-card:nth-child(4) { animation-delay: 0.2s; }

.mis-alert-card:nth-child(1) { animation-delay: 0.1s; }
.mis-alert-card:nth-child(2) { animation-delay: 0.15s; }
.mis-alert-card:nth-child(3) { animation-delay: 0.2s; }

.mis-three-col > *:nth-child(1) { animation-delay: 0.2s; }
.mis-three-col > *:nth-child(2) { animation-delay: 0.25s; }
.mis-three-col > *:nth-child(3) { animation-delay: 0.3s; }

.mis-two-col > *:nth-child(1) { animation-delay: 0.3s; }
.mis-two-col > *:nth-child(2) { animation-delay: 0.35s; }

.mis-follow-grid > *:nth-child(1) { animation-delay: 0.4s; }
.mis-follow-grid > *:nth-child(2) { animation-delay: 0.45s; }

@media print {
    .mis-print-btn, .mis-date-form {
        display: none !important;
    }
    .mis-page {
        background: #fff;
    }
    .mis-col-panel, .mis-detail-card, .mis-follow-card, .mis-kpi-card {
        box-shadow: none !important;
        border-color: #cbd5e1 !important;
        break-inside: avoid;
    }
}
</style>
@endsection

@section('content')
<div class="mis-page">

    {{-- ── HEADER ──────────────────────────────────────────────────────── --}}
    <div class="mis-header">
        <div>
            <h1><i class="fas fa-chart-bar" style="margin-right:10px; opacity:.9;"></i>Daily MIS Report</h1>
            <p>Management Information System &nbsp;·&nbsp; Pragya School</p>
        </div>
        <div class="mis-header-right">
            <button class="mis-print-btn" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
            <form method="GET" action="{{ route('school.dashboard.mis-report') }}" class="mis-date-form" style="display:flex; gap:8px; align-items:center;">
                <input type="date" name="date" class="mis-date-input" value="{{ $date->toDateString() }}" onchange="this.form.submit()">
                <button type="submit" class="mis-go-btn"><i class="fas fa-arrow-right" style="margin-right:4px;"></i>Go</button>
            </form>
        </div>
    </div>

    {{-- ── ROW 1: KPI GRID ─────────────────────────────────────────────── --}}
    <div class="mis-kpi-grid">
        <div class="mis-kpi-card k-rev">
            <div class="mis-kpi-icon-wrapper"><i class="fas fa-indian-rupee-sign"></i></div>
            <div class="mis-kpi-info">
                <div class="mis-kpi-label">Daily Revenue</div>
                <div class="mis-kpi-value">₹{{ number_format($dailyRevenue, 0) }}</div>
                <div class="mis-kpi-sub">Fee + Other Income</div>
            </div>
        </div>
        <div class="mis-kpi-card k-stu">
            <div class="mis-kpi-icon-wrapper"><i class="fas fa-user-graduate"></i></div>
            <div class="mis-kpi-info">
                <div class="mis-kpi-label">Student Attendance</div>
                <div class="mis-kpi-value">{{ $studentAttendanceRatio }}</div>
                <div class="mis-kpi-sub">{{ $studentAttendancePct }}% Present Today</div>
            </div>
        </div>
        <div class="mis-kpi-card k-stf">
            <div class="mis-kpi-icon-wrapper"><i class="fas fa-users"></i></div>
            <div class="mis-kpi-info">
                <div class="mis-kpi-label">Staff Attendance</div>
                <div class="mis-kpi-value">{{ $staffAttendanceRatio }}</div>
                <div class="mis-kpi-sub">{{ $staffAttendancePct }}% Present Today</div>
            </div>
        </div>
        <div class="mis-kpi-card k-adm">
            <div class="mis-kpi-icon-wrapper"><i class="fas fa-user-plus"></i></div>
            <div class="mis-kpi-info">
                <div class="mis-kpi-label">New Admissions</div>
                <div class="mis-kpi-value">{{ $newAdmissionsCount }}</div>
                <div class="mis-kpi-sub">This Month: {{ $newAdmissionsThisMonth }}</div>
            </div>
        </div>
    </div>

    {{-- ── ROW 2: IMMEDIATE ACTIONS REQUIRED ──────────────────────────── --}}
    <div class="mis-section-label alert-label"><i class="fas fa-exclamation-triangle"></i>Immediate Actions Required</div>
    <div class="mis-alerts-grid">
        <div class="mis-alert-card">
            <div class="mis-alert-left">
                <i class="fas fa-exclamation-circle mis-alert-icon alert-red"></i>
                <div>
                    <div class="mis-alert-title">Attendance Not Marked</div>
                    <div class="mis-alert-desc">Teachers haven't marked attendance</div>
                </div>
            </div>
            <div class="mis-alert-badge badge-red">{{ $attendanceNotMarkedTeachersCount }}</div>
        </div>
        <div class="mis-alert-card">
            <div class="mis-alert-left">
                <i class="fas fa-exclamation-circle mis-alert-icon alert-red"></i>
                <div>
                    <div class="mis-alert-title">Fee Defaulters (90+ days)</div>
                    <div class="mis-alert-desc">Critical collection required</div>
                </div>
            </div>
            <div class="mis-alert-badge badge-red">{{ $feeDefaultersCriticalCount }}</div>
        </div>
        <div class="mis-alert-card">
            <div class="mis-alert-left">
                <i class="fas fa-exclamation-triangle mis-alert-icon alert-orange"></i>
                <div>
                    <div class="mis-alert-title">App Not Downloaded</div>
                    <div class="mis-alert-desc">Parents + Staff pending</div>
                </div>
            </div>
            <div class="mis-alert-badge badge-orange">{{ $appNotDownloadedCount }}</div>
        </div>
    </div>

    {{-- ── ROW 3: 3-COLUMN METRICS BREAKDOWN ──────────────────────────── --}}
    <div class="mis-three-col">
        {{-- Column 1: Income & Expenses --}}
        <div class="mis-col-panel">
            <div class="mis-col-hdr">
                <i class="fas fa-landmark"></i>
                <span>Income & Expenses</span>
            </div>
            <div class="mis-metric-group">
                <div class="mis-group-title gt-green">Today's Income</div>
                <div class="mis-metric-row">
                    <span>Fee Collection</span>
                    <strong>₹{{ number_format($todayFeeCollection, 2) }}</strong>
                </div>
                <div class="mis-metric-row">
                    <span>Other Income</span>
                    <strong>₹{{ number_format($todayOtherIncome, 2) }}</strong>
                </div>
                <div class="mis-metric-row" style="border-top: 1px solid #cbd5e1; margin-top: 4px; padding-top: 8px;">
                    <span style="font-weight: 700; color: #059669;">Total Income</span>
                    <strong style="color: #059669;">₹{{ number_format($todayTotalIncome, 2) }}</strong>
                </div>
            </div>
            <div class="mis-metric-group">
                <div class="mis-group-title gt-red">Today's Expenses</div>
                <div class="mis-metric-row">
                    <span>Other Expenses</span>
                    <strong>₹{{ number_format($todayOtherExpenses, 2) }}</strong>
                </div>
                <div class="mis-metric-row" style="border-top: 1px solid #cbd5e1; margin-top: 4px; padding-top: 8px;">
                    <span style="font-weight: 700; color: #b45309;">Total Expenses</span>
                    <strong style="color: #b45309;">₹{{ number_format($todayTotalExpenses, 2) }}</strong>
                </div>
            </div>
            <div class="mis-profit-box">
                <span>Net Profit Today</span>
                <strong>₹{{ number_format($todayNetProfit, 2) }}</strong>
            </div>
        </div>

        {{-- Column 2: Digital Metrics --}}
        <div class="mis-col-panel">
            <div class="mis-col-hdr">
                <i class="fas fa-desktop"></i>
                <span>Digital Metrics</span>
            </div>
            <div class="mis-metric-group">
                <div class="mis-group-title gt-orange"><i class="fas fa-mobile-alt" style="margin-right: 4px;"></i>App Downloads</div>
                <div class="mis-metric-row">
                    <span>Student Downloaded</span>
                    <strong>{{ $studentAppDownloadedCount }}/{{ $studentAppDownloadedTotal }}</strong>
                </div>
                <div class="mis-metric-row">
                    <span>Staff Downloaded</span>
                    <strong>{{ $staffAppDownloadedCount }}/{{ $staffAppDownloadedTotal }}</strong>
                </div>
                <div class="mis-metric-row">
                    <span>Parent Downloaded</span>
                    <strong>{{ $parentAppDownloadedCount }}/{{ $parentAppDownloadedTotal }}</strong>
                </div>
                <div class="mis-metric-row">
                    <span style="font-weight: 700;">Pending Downloads</span>
                    <strong class="pending-red">{{ $pendingDownloadsCount }}</strong>
                </div>
            </div>
            <div class="mis-metric-group">
                <div class="mis-group-title gt-red"><i class="fas fa-book" style="margin-right: 4px;"></i>Library Today</div>
                <div class="mis-metric-row">
                    <span>Books Issued</span>
                    <strong>{{ $todayBooksIssued }}</strong>
                </div>
                <div class="mis-metric-row">
                    <span>Books Returned</span>
                    <strong>{{ $todayBooksReturned }}</strong>
                </div>
            </div>
            <div class="mis-metric-group">
                <div class="mis-group-title gt-orange"><i class="fas fa-bell" style="margin-right: 4px;"></i>Communications</div>
                <div class="mis-metric-row">
                    <span>Notices Shared</span>
                    <strong>{{ $todayNoticesShared }}</strong>
                </div>
            </div>
        </div>

        {{-- Column 3: Admissions & Academic --}}
        <div class="mis-col-panel">
            <div class="mis-col-hdr">
                <i class="fas fa-graduation-cap"></i>
                <span>Admissions & Academic</span>
            </div>
            <div class="mis-metric-group">
                <div class="mis-group-title gt-green"><i class="fas fa-user-plus" style="margin-right: 4px;"></i>Today's Admissions</div>
                <div class="mis-metric-row">
                    <span>Enquiries</span>
                    <strong>{{ $todayEnquiriesCount }}</strong>
                </div>
                <div class="mis-metric-row">
                    <span>Application</span>
                    <strong>{{ $todayApplicationsCount }}</strong>
                </div>
                <div class="mis-metric-row">
                    <span>Interactions</span>
                    <strong>{{ $todayInteractionsCount }}</strong>
                </div>
                <div class="mis-metric-row" style="border-top: 1px solid #cbd5e1; margin-top: 4px; padding-top: 8px;">
                    <span style="font-weight: 700; color: #10b981;">Admissions</span>
                    <strong style="color: #10b981;">{{ $todayAdmissionsCount }}</strong>
                </div>
            </div>
            <div class="mis-metric-group">
                <div class="mis-group-title gt-orange">Academic Sharing Today</div>
                <div class="mis-metric-row">
                    <span>Assignments</span>
                    <strong>{{ $todayAssignmentsShared }}</strong>
                </div>
                <div class="mis-metric-row">
                    <span>Study Materials</span>
                    <strong>{{ $todayMaterialsShared }}</strong>
                </div>
                <div class="mis-metric-row">
                    <span>Tests</span>
                    <strong>{{ $todayTestsShared }}</strong>
                </div>
                <div class="mis-metric-row">
                    <span>Diary Entries</span>
                    <strong>{{ $todayDiariesShared }}</strong>
                </div>
            </div>
            <div class="low-activity-box">
                <div class="low-activity-item">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>{{ $teachersNoSharing7DaysCount }} teachers haven't shared any content in 7 days</span>
                </div>
                <div class="low-activity-item" style="margin-top: 2px;">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>{{ $classesMissingDiaryTodayCount }} classes missing diary entries today</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── ROW 4: TODAY'S ATTENDANCE & FEE COLLECTION DETAILS ────────── --}}
    <div class="mis-two-col">
        {{-- Card 1: Today's Attendance --}}
        <div class="mis-detail-card">
            <div class="mis-detail-hdr">
                <div class="mis-detail-title">
                    <i class="fas fa-calendar-alt" style="color: #f97316;"></i>
                    <span>Today's Attendance</span>
                </div>
                <div class="mis-detail-meta">{{ $date->format('d M Y') }}</div>
            </div>
            
            <div class="mis-att-block-wrapper">
                <div class="mis-att-sub-label">Student Attendance</div>
                <div class="mis-att-row">
                    <div class="mis-att-box ab-green">
                        <div class="mis-att-box-val">{{ $studentPresentCount }}</div>
                        <div class="mis-att-box-lbl">Present</div>
                    </div>
                    <div class="mis-att-box ab-red">
                        <div class="mis-att-box-val">{{ $studentAbsentCount }}</div>
                        <div class="mis-att-box-lbl">Absent</div>
                    </div>
                    <div class="mis-att-box ab-orange">
                        <div class="mis-att-box-val">{{ $studentHalfDayCount }}</div>
                        <div class="mis-att-box-lbl">Half Day</div>
                    </div>
                    <div class="mis-att-box ab-blue">
                        <div class="mis-att-box-val">{{ $studentNotMarkedCount }}</div>
                        <div class="mis-att-box-lbl">Not marked</div>
                    </div>
                    <div class="mis-att-box ab-pink">
                        <div class="mis-att-box-val">{{ $studentLeaveCount }}</div>
                        <div class="mis-att-box-lbl">Leave</div>
                    </div>
                </div>
            </div>

            <div class="mis-att-block-wrapper" style="margin-bottom: 24px;">
                <div class="mis-att-sub-label">Staff Attendance</div>
                <div class="mis-att-row">
                    <div class="mis-att-box ab-green">
                        <div class="mis-att-box-val">{{ $staffPresentCount }}</div>
                        <div class="mis-att-box-lbl">Present</div>
                    </div>
                    <div class="mis-att-box ab-red">
                        <div class="mis-att-box-val">{{ $staffAbsentCount }}</div>
                        <div class="mis-att-box-lbl">Absent</div>
                    </div>
                    <div class="mis-att-box ab-orange">
                        <div class="mis-att-box-val">{{ $staffHalfDayCount }}</div>
                        <div class="mis-att-box-lbl">Half Day</div>
                    </div>
                    <div class="mis-att-box ab-blue">
                        <div class="mis-att-box-val">{{ $staffNotMarkedCount }}</div>
                        <div class="mis-att-box-lbl">Not marked</div>
                    </div>
                    <div class="mis-att-box ab-pink">
                        <div class="mis-att-box-val">{{ $staffLeaveCount }}</div>
                        <div class="mis-att-box-lbl">Leave</div>
                    </div>
                </div>
            </div>

            <div class="critical-issues-box">
                <div class="critical-hdr">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>Critical Issues</span>
                </div>
                <ul>
                    @foreach($criticalAttendanceIssues as $issue)
                        <li>{{ $issue }}</li>
                    @endforeach
                    @if(empty($criticalAttendanceIssues))
                        <li>No critical attendance issues marked for today.</li>
                    @endif
                </ul>
            </div>
        </div>

        {{-- Card 2: Today's Fee Collection --}}
        <div class="mis-detail-card">
            <div class="mis-detail-hdr">
                <div class="mis-detail-title">
                    <i class="fas fa-wallet" style="color: #10b981;"></i>
                    <span>Today's Fee Collection</span>
                </div>
                <div class="mis-detail-meta">{{ $date->format('d M Y') }}</div>
            </div>

            <div class="mis-mode-grid">
                <div class="mis-mode-card">
                    <div class="val">₹{{ number_format($feeCashCollection, 0) }}</div>
                    <div class="lbl">Cash</div>
                </div>
                <div class="mis-mode-card">
                    <div class="val">₹{{ number_format($feeChequeCollection, 0) }}</div>
                    <div class="lbl">Cheque</div>
                </div>
                <div class="mis-mode-card">
                    <div class="val">₹{{ number_format($feeOnlineCollection, 0) }}</div>
                    <div class="lbl">Online Payment</div>
                </div>
            </div>
            <div class="mis-mode-grid" style="margin-bottom: 24px;">
                <div class="mis-mode-card total-mode" style="grid-column: span 3;">
                    <div class="val">₹{{ number_format($feeTotalCollection, 0) }}</div>
                    <div class="lbl">Total Today</div>
                </div>
            </div>

            <div class="mis-def-aging-hdr">Fee Defaulters (Aging Status)</div>
            <div class="mis-def-aging-row">
                <div class="mis-def-aging-box db-yellow">
                    <div class="val">{{ $defaulters0_30Count }}</div>
                    <div class="lbl">0-30 days</div>
                </div>
                <div class="mis-def-aging-box db-orange">
                    <div class="val">{{ $defaulters31_60Count }}</div>
                    <div class="lbl">31-60 days</div>
                </div>
                <div class="mis-def-aging-box db-pink">
                    <div class="val">{{ $defaulters61_90Count }}</div>
                    <div class="lbl">61-90 days</div>
                </div>
                <div class="mis-def-aging-box db-red">
                    <div class="val">{{ $defaulters90PlusCount }}</div>
                    <div class="lbl">90+ days</div>
                </div>
            </div>

            <div class="mis-fee-card-footer">
                <div class="mis-fc-left">
                    <span>Discount Approvals</span>
                    <span class="mis-discount-badge">{{ $pendingDiscountApprovalsCount }}</span>
                </div>
                <div class="mis-fc-right">
                    <span>Overall Collection (This Month):</span>
                    <strong>₹{{ number_format($overallMonthlyCollection, 0) }}</strong>
                </div>
            </div>
        </div>
    </div>

    {{-- ── ROW 5: FOLLOW-UPS & ALERTS (BOTTOM SECTION) ───────────────── --}}
    <div class="mis-follow-grid">
        {{-- Column 1: Critical Follow-ups Required --}}
        <div class="mis-follow-card">
            <div class="mis-follow-hdr red-hdr">
                <i class="fas fa-exclamation-triangle"></i>
                <span>Critical Follow-ups Required</span>
            </div>

            {{-- Fee Defaulters --}}
            <div class="mis-alert-list-box">
                <div class="mis-alb-title">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>Fee Defaulters (90+ Days)</span>
                </div>
                <ul class="mis-alb-list">
                    @forelse($feeDefaulters90PlusList as $item)
                        <li>• {{ $item['name'] }} ({{ $item['class_section'] }}) - ₹ {{ number_format($item['pending_amount'], 0) }} - {{ $item['due_days'] }} days</li>
                    @empty
                        <li>No critical fee defaulters.</li>
                    @endforelse
                </ul>
                @if($feeDefaulters90PlusMoreCount > 0)
                    <span class="mis-alb-more-link">+ {{ $feeDefaulters90PlusMoreCount }} more students</span>
                @endif
            </div>

            {{-- Classes Attendance Not Marked --}}
            <div class="mis-alert-list-box">
                <div class="mis-alb-title">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>Classes Attendance not marked today</span>
                </div>
                <ul class="mis-alb-list">
                    @forelse($classesAttendanceNotMarkedList as $item)
                        <li>• {{ $item }} (NA)</li>
                    @empty
                        <li>All classes attendance marked.</li>
                    @endforelse
                </ul>
                @if($classesAttendanceNotMarkedMoreCount > 0)
                    <span class="mis-alb-more-link">+ {{ $classesAttendanceNotMarkedMoreCount }} more classes</span>
                @endif
            </div>

            {{-- Teachers Not Marked Attendance in 7 days --}}
            <div class="mis-alert-list-box orange-box">
                <div class="mis-alb-title">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Teachers not marked attendance in 7 days</span>
                </div>
                <ul class="mis-alb-list">
                    @forelse($teachersNotMarkedAttendance7DaysList as $item)
                        <li>• {{ $item }} (NA)</li>
                    @empty
                        <li>No pending class attendance over 7 days.</li>
                    @endforelse
                </ul>
                @if($teachersNotMarkedAttendance7DaysMoreCount > 0)
                    <span class="mis-alb-more-link">+ {{ $teachersNotMarkedAttendance7DaysMoreCount }} more classes</span>
                @endif
            </div>
        </div>

        {{-- Column 2: Low Activity Alerts --}}
        <div class="mis-follow-card">
            <div class="mis-follow-hdr green-hdr">
                <i class="fas fa-check-circle"></i>
                <span>Low Activity Alerts</span>
            </div>

            {{-- Teachers haven't shared content --}}
            <div class="mis-alert-list-box">
                <div class="mis-alb-title">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>Teachers haven't shared any content in 7 days</span>
                </div>
                <ul class="mis-alb-list">
                    @forelse($teachersNoSharing7DaysList as $item)
                        <li>• {{ $item }}</li>
                    @empty
                        <li>All teachers active in sharing.</li>
                    @endforelse
                </ul>
                @if($teachersNoSharing7DaysMoreCount > 0)
                    <span class="mis-alb-more-link">+ {{ $teachersNoSharing7DaysMoreCount }} more teachers</span>
                @endif
            </div>

            {{-- Classes missing diary entries today --}}
            <div class="mis-alert-list-box blue-box">
                <div class="mis-alb-title">
                    <i class="fas fa-info-circle"></i>
                    <span>Classes missing diary entries today</span>
                </div>
                <ul class="mis-alb-list">
                    @forelse($classesMissingDiaryTodayList as $item)
                        <li>• {{ $item }} (NA)</li>
                    @empty
                        <li>All classes have diary entries.</li>
                    @endforelse
                </ul>
                @if($classesMissingDiaryTodayMoreCount > 0)
                    <span class="mis-alb-more-link">+ {{ $classesMissingDiaryTodayMoreCount }} more classes</span>
                @endif
            </div>
        </div>
    </div>

    {{-- ── FOOTER ──────────────────────────────────────────────────────── --}}
    <div class="mis-footer">
        <i class="fas fa-shield-alt" style="color:#2563eb; margin-right:6px;"></i>
        Report generated at <strong>{{ now()->format('h:i A') }}</strong> &nbsp;·&nbsp;
        All data from live database &nbsp;·&nbsp;
        {{ $school->name ?? 'Pragya School' }}
    </div>

</div>
@endsection
