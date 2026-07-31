@extends('layouts.app')

@section('page-title', 'Student-wise Fee')

@section('content')
<style>
/* ═══════════════════════════════════════════════════════
   STUDENT-WISE FEE — PREMIUM BLUE & WHITE THEME
   ═══════════════════════════════════════════════════════ */
:root {
    --sw-blue:      #1e3a8a;
    --sw-blue2:     #1d4ed8;
    --sw-sky:       #0284c7;
    --sw-sky-light: #e0f2fe;
    --sw-dark:      #0f172a;
    --sw-border:    #bae6fd;
    --sw-green:     #16a34a;
    --sw-orange:    #ea580c;
    --sw-red:       #dc2626;
    --sw-bg:        #f0f9ff;
}

.sw-wrap {
    font-family: 'Plus Jakarta Sans', sans-serif;
    color: var(--sw-dark);
    background: var(--sw-bg);
    min-height: calc(100vh - 60px);
    padding: 0;
}

/* ── Header bar ── */
.sw-header {
    background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 100%);
    padding: 18px 28px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
}
.sw-header-title {
    display: flex;
    align-items: center;
    gap: 12px;
}
.sw-header-title h1 {
    font-size: 1.5rem;
    font-weight: 800;
    color: #fff;
    margin: 0;
}
.sw-header-title p {
    color: #bfdbfe;
    font-size: 0.85rem;
    margin: 0;
}
.sw-breadcrumb {
    color: #93c5fd;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    gap: 6px;
}
.sw-breadcrumb a { color: #bfdbfe; text-decoration: none; }
.sw-breadcrumb a:hover { color: #fff; }

/* ── Filter bar ── */
.sw-filters {
    background: #fff;
    border-bottom: 2px solid var(--sw-border);
    padding: 16px 28px;
    display: flex;
    align-items: center;
    gap: 14px;
    flex-wrap: wrap;
}
.sw-filter-group {
    display: flex;
    flex-direction: column;
    gap: 4px;
    min-width: 130px;
}
.sw-filter-group label {
    font-size: 0.72rem;
    font-weight: 700;
    color: var(--sw-blue);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.sw-filter-group select,
.sw-filter-group input {
    font-size: 0.92rem;
    font-weight: 600;
    color: var(--sw-dark);
    border: 1.5px solid var(--sw-border);
    border-radius: 8px;
    padding: 7px 10px;
    background: #f8faff;
    outline: none;
    transition: border-color .2s;
    cursor: pointer;
}
.sw-filter-group select:focus,
.sw-filter-group input:focus { border-color: var(--sw-blue2); }
.sw-toggle-group {
    margin-left: auto;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--sw-blue);
}
.sw-toggle {
    position: relative;
    width: 46px;
    height: 24px;
    display: inline-block;
}
.sw-toggle input { opacity: 0; width: 0; height: 0; }
.sw-toggle-slider {
    position: absolute;
    inset: 0;
    background: #d1d5db;
    border-radius: 100px;
    transition: .3s;
    cursor: pointer;
}
.sw-toggle-slider::before {
    content: '';
    position: absolute;
    width: 18px; height: 18px;
    left: 3px; bottom: 3px;
    background: #fff;
    border-radius: 50%;
    transition: .3s;
    box-shadow: 0 1px 4px rgba(0,0,0,.2);
}
.sw-toggle input:checked + .sw-toggle-slider { background: var(--sw-blue2); }
.sw-toggle input:checked + .sw-toggle-slider::before { transform: translateX(22px); }

/* ── Table toolbar ── */
.sw-table-toolbar {
    background: #fff;
    padding: 12px 28px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    border-bottom: 1px solid #e2e8f0;
}
.sw-select-all-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--sw-dark);
    cursor: pointer;
}
.sw-select-all-label input[type=checkbox] {
    width: 17px; height: 17px;
    accent-color: var(--sw-blue2);
    cursor: pointer;
}
.sw-vis-btns {
    display: flex;
    gap: 8px;
}
.sw-btn-vis {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
    border-radius: 8px;
    border: 1.5px solid var(--sw-border);
    background: #fff;
    color: var(--sw-blue);
    font-size: 0.83rem;
    font-weight: 700;
    cursor: pointer;
    transition: all .2s;
}
.sw-btn-vis:hover {
    background: var(--sw-sky-light);
    border-color: var(--sw-sky);
}

/* ── Main table ── */
.sw-table-wrap {
    background: #fff;
    overflow-x: auto;
}
.sw-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.9rem;
}
.sw-table thead tr {
    background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 100%);
}
.sw-table thead th {
    color: #fff;
    font-size: 0.82rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 13px 14px;
    white-space: nowrap;
    border: none;
}
.sw-table tbody tr {
    border-bottom: 1px solid #f1f5f9;
    transition: background .15s;
}
.sw-table tbody tr:hover { background: #f0f9ff; }
.sw-table tbody td {
    padding: 13px 14px;
    vertical-align: middle;
    color: var(--sw-dark);
    font-size: 0.9rem;
}
.sw-student-cell {
    display: flex;
    align-items: center;
    gap: 12px;
}
.sw-avatar {
    width: 38px; height: 38px;
    border-radius: 50%;
    background: linear-gradient(135deg, #1d4ed8, #0284c7);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.95rem;
    font-weight: 800;
    flex-shrink: 0;
}
.sw-student-name {
    font-weight: 700;
    font-size: 0.95rem;
    color: var(--sw-dark);
}
.sw-student-sub {
    font-size: 0.78rem;
    color: #64748b;
    margin-top: 2px;
}
.sw-amount {
    font-weight: 700;
    font-size: 0.95rem;
}
.sw-amount.green { color: var(--sw-green); }
.sw-amount.red   { color: var(--sw-red); }
.sw-amount.blue  { color: var(--sw-blue2); }

/* inline toggles in table */
.sw-vis-toggle {
    display: flex;
    align-items: center;
    gap: 7px;
    font-size: 0.82rem;
    font-weight: 600;
}
.sw-vis-label-on  { color: var(--sw-green); }
.sw-vis-label-off { color: var(--sw-red); }

/* view button */
.sw-view-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 34px; height: 34px;
    border-radius: 8px;
    background: var(--sw-sky-light);
    border: 1.5px solid var(--sw-border);
    color: var(--sw-blue2);
    cursor: pointer;
    transition: all .2s;
    text-decoration: none;
    font-size: 1rem;
}
.sw-view-btn:hover {
    background: var(--sw-blue2);
    color: #fff;
    border-color: var(--sw-blue2);
    transform: scale(1.08);
}

/* footer bar */
.sw-footer {
    background: #fff;
    border-top: 1px solid #e2e8f0;
    padding: 12px 28px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
}
.sw-footer-toggles {
    display: flex;
    align-items: center;
    gap: 24px;
}
.sw-footer-toggle {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.87rem;
    font-weight: 600;
    color: #374151;
}
.sw-rows-badge {
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 6px 16px;
    font-size: 0.87rem;
    font-weight: 700;
    color: var(--sw-dark);
}

/* ═══════════════════════════════════════════
   DETAIL / VIEW PANEL
   ═══════════════════════════════════════════ */
.sw-detail-page {
    background: var(--sw-bg);
    min-height: calc(100vh - 60px);
    padding: 0;
}

/* top nav bar in detail view */
.sw-detail-topbar {
    background: #fff;
    border-bottom: 2px solid var(--sw-border);
    padding: 14px 28px;
    display: flex;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
}
.sw-back-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px; height: 36px;
    border-radius: 9px;
    background: var(--sw-sky-light);
    border: 1.5px solid var(--sw-border);
    color: var(--sw-blue2);
    text-decoration: none;
    font-size: 1rem;
    transition: all .2s;
}
.sw-back-btn:hover { background: var(--sw-blue2); color: #fff; }
.sw-student-avatar-lg {
    width: 42px; height: 42px;
    border-radius: 50%;
    background: linear-gradient(135deg, #1d4ed8, #0284c7);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    font-weight: 900;
    flex-shrink: 0;
}
.sw-topbar-session {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--sw-dark);
}
.sw-topbar-session select {
    border: 1.5px solid var(--sw-border);
    border-radius: 8px;
    padding: 6px 10px;
    font-size: 0.88rem;
    font-weight: 600;
    color: var(--sw-dark);
    background: #f8faff;
    cursor: pointer;
}
.sw-topbar-right {
    margin-left: auto;
    display: flex;
    align-items: center;
    gap: 10px;
}
.sw-btn-primary {
    display: flex;
    align-items: center;
    gap: 7px;
    padding: 9px 18px;
    background: var(--sw-blue2);
    color: #fff;
    border: none;
    border-radius: 9px;
    font-size: 0.88rem;
    font-weight: 700;
    cursor: pointer;
    text-decoration: none;
    transition: all .2s;
}
.sw-btn-primary:hover { background: var(--sw-blue); transform: translateY(-1px); }
.sw-btn-outline {
    display: flex;
    align-items: center;
    gap: 7px;
    padding: 9px 18px;
    background: #fff;
    color: var(--sw-blue2);
    border: 1.5px solid var(--sw-blue2);
    border-radius: 9px;
    font-size: 0.88rem;
    font-weight: 700;
    cursor: pointer;
    text-decoration: none;
    transition: all .2s;
}
.sw-btn-outline:hover { background: var(--sw-sky-light); }

/* Student info panel */
.sw-info-panel {
    background: #fff;
    border: 1.5px solid var(--sw-border);
    border-radius: 14px;
    margin: 20px 28px;
    overflow: hidden;
}
.sw-info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0;
}
.sw-info-col {
    padding: 20px 24px;
}
.sw-info-col:first-child {
    border-right: 1px solid var(--sw-border);
}
.sw-info-row {
    display: flex;
    gap: 10px;
    margin-bottom: 10px;
    align-items: baseline;
}
.sw-info-label {
    min-width: 145px;
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--sw-blue);
}
.sw-info-value {
    font-size: 0.9rem;
    font-weight: 700;
    color: var(--sw-dark);
}
.sw-info-value a { color: var(--sw-blue2); text-decoration: none; }
.sw-info-divider {
    border: none;
    border-top: 1px solid var(--sw-border);
    margin: 0;
}
.sw-quick-notes {
    position: absolute;
    right: 28px;
    top: 50%;
    transform: translateY(-50%);
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
}
.sw-quick-notes-icon {
    width: 64px; height: 64px;
    border-radius: 50%;
    background: linear-gradient(135deg, #dc2626, #f59e0b);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.6rem;
    color: #fff;
    cursor: pointer;
}
.sw-quick-notes-label {
    font-size: 0.8rem;
    font-weight: 700;
    color: var(--sw-blue);
}

/* Tabs */
.sw-tabs {
    background: #fff;
    border-top: 2px solid var(--sw-border);
    border-bottom: 2px solid var(--sw-border);
    padding: 10px 20px;
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap; /* Fit items instead of scrolling */
}
.sw-tab {
    padding: 8px 12px;
    font-size: clamp(0.72rem, 1.2vw, 0.88rem); /* Dynamic fluid typography */
    font-weight: 700;
    color: #64748b;
    cursor: pointer;
    border-bottom: 3px solid transparent;
    transition: all .2s;
    white-space: nowrap;
    text-decoration: none;
    border-top: none;
    border-left: none;
    border-right: none;
    background: none;
    border-radius: 6px;
}
.sw-tab:hover, .sw-tab.active {
    color: var(--sw-blue2);
    background: #f0f7ff;
}
.sw-tab.active {
    border-bottom-color: var(--sw-blue2);
}

/* Summary badges */
.sw-summary-bar {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-left: auto;
}
.sw-badge-receivable {
    padding: 7px 14px;
    background: var(--sw-blue);
    color: #fff;
    border-radius: 8px;
    font-size: 0.82rem;
    font-weight: 700;
}
.sw-badge-paid {
    padding: 7px 14px;
    background: var(--sw-green);
    color: #fff;
    border-radius: 8px;
    font-size: 0.82rem;
    font-weight: 700;
}
.sw-badge-due {
    padding: 7px 14px;
    background: #fff;
    color: var(--sw-red);
    border: 2px solid var(--sw-red);
    border-radius: 8px;
    font-size: 0.82rem;
    font-weight: 700;
}

/* Fee Record section */
.sw-fee-record-wrap {
    margin: 20px 28px;
}
.sw-fee-record-header {
    background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 100%);
    padding: 14px 20px;
    border-radius: 12px 12px 0 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.sw-fee-record-header h3 {
    color: #fff;
    font-size: 1rem;
    font-weight: 800;
    margin: 0;
}
.sw-btn-add-discount {
    display: flex;
    align-items: center;
    gap: 7px;
    padding: 8px 16px;
    background: #f59e0b;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 700;
    cursor: pointer;
    transition: all .2s;
}
.sw-btn-add-discount:hover { background: #d97706; }

.sw-discount-strip {
    background: #fef9ee;
    border: 1px solid #fde68a;
    padding: 10px 16px;
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}
.sw-discount-strip label {
    font-size: 0.85rem;
    font-weight: 700;
    color: #92400e;
}
.sw-discount-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
    background: #fde68a;
    border: 1px solid #fbbf24;
    border-radius: 20px;
    font-size: 0.83rem;
    font-weight: 700;
    color: #92400e;
}

/* Fee details card */
.sw-fee-body {
    background: #fff;
    border: 1.5px solid var(--sw-border);
    border-top: none;
    border-radius: 0 0 12px 12px;
}
.sw-fee-details-header {
    background: #fef2f2;
    border-bottom: 1px solid #fecaca;
    padding: 10px 20px;
    font-size: 0.88rem;
    font-weight: 700;
    color: var(--sw-red);
}

/* Installment items */
.sw-installment {
    border-bottom: 1px solid #f1f5f9;
    padding: 16px 20px;
    cursor: pointer;
    transition: background .15s;
}
.sw-installment:last-child { border-bottom: none; }
.sw-installment:hover { background: #f8faff; }
.sw-installment-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}
.sw-installment-title {
    display: flex;
    align-items: center;
    gap: 10px;
}
.sw-installment-title h4 {
    font-size: 1rem;
    font-weight: 800;
    color: var(--sw-dark);
    margin: 0;
}
.sw-inst-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 0.77rem;
    font-weight: 700;
}
.sw-inst-badge.paid {
    background: #dcfce7;
    color: var(--sw-green);
    border: 1px solid #86efac;
}
.sw-inst-badge.pending {
    background: #fff7ed;
    color: var(--sw-orange);
    border: 1px solid #fdba74;
}
.sw-inst-badge.partial {
    background: #eff6ff;
    color: var(--sw-blue2);
    border: 1px solid #93c5fd;
}
.sw-installment-amounts {
    font-size: 0.85rem;
    color: #64748b;
    margin-top: 5px;
}
.sw-installment-amounts span { font-weight: 700; }
.sw-installment-amounts .blue  { color: var(--sw-blue2); }
.sw-installment-amounts .red   { color: var(--sw-red); }
.sw-installment-amounts .green { color: var(--sw-green); }

.sw-installment-right {
    display: flex;
    align-items: center;
    gap: 10px;
}
.sw-mark-paid-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 7px 16px;
    background: #fff;
    color: var(--sw-orange);
    border: 1.5px solid var(--sw-orange);
    border-radius: 8px;
    font-size: 0.83rem;
    font-weight: 700;
    cursor: pointer;
    transition: all .2s;
}
.sw-mark-paid-btn:hover {
    background: var(--sw-orange);
    color: #fff;
}
.sw-paid-text {
    color: var(--sw-green);
    font-size: 0.85rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 5px;
}
.sw-chevron {
    color: #94a3b8;
    transition: transform .2s;
}
.sw-installment.open .sw-chevron { transform: rotate(180deg); }

/* Installment detail expand */
.sw-installment-detail {
    display: none;
    background: #f8faff;
    border-top: 1px dashed var(--sw-border);
    padding: 16px 20px;
    margin-top: 12px;
}
.sw-installment.open .sw-installment-detail { display: block; }

/* Mark Paid Modal */
.sw-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15,23,42,0.55);
    backdrop-filter: blur(4px);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
}
.sw-modal {
    background: #fff;
    border-radius: 16px;
    padding: 28px;
    width: 440px;
    max-width: 95vw;
    box-shadow: 0 24px 64px rgba(30,58,138,.25);
}
.sw-modal h3 {
    font-size: 1.1rem;
    font-weight: 800;
    color: var(--sw-blue);
    margin: 0 0 20px;
}
.sw-modal-field {
    margin-bottom: 14px;
}
.sw-modal-field label {
    display: block;
    font-size: 0.83rem;
    font-weight: 700;
    color: var(--sw-blue);
    margin-bottom: 5px;
    text-transform: uppercase;
    letter-spacing: 0.4px;
}
.sw-modal-field input,
.sw-modal-field select {
    width: 100%;
    padding: 9px 13px;
    border: 1.5px solid var(--sw-border);
    border-radius: 9px;
    font-size: 0.93rem;
    font-weight: 600;
    color: var(--sw-dark);
    outline: none;
    transition: border-color .2s;
}
.sw-modal-field input:focus,
.sw-modal-field select:focus { border-color: var(--sw-blue2); }
.sw-modal-actions {
    display: flex;
    gap: 10px;
    margin-top: 22px;
}
.sw-modal-actions button { flex: 1; padding: 11px; border-radius: 9px; font-size: 0.93rem; font-weight: 700; cursor: pointer; border: none; }
.sw-modal-cancel { background: #f1f5f9; color: #374151; }
.sw-modal-submit { background: var(--sw-blue2); color: #fff; }
.sw-modal-submit:hover { background: var(--sw-blue); }

/* Pagination */
.sw-pagination {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 5px;
    padding: 12px 28px;
    background: #fff;
    border-top: 1px solid #e2e8f0;
}
.sw-page-btn {
    width: 32px; height: 32px;
    border-radius: 7px;
    border: 1.5px solid var(--sw-border);
    background: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--sw-dark);
    cursor: pointer;
    text-decoration: none;
    transition: all .2s;
}
.sw-page-btn.active { background: var(--sw-blue2); color: #fff; border-color: var(--sw-blue2); }
.sw-page-btn:hover:not(.active) { background: var(--sw-sky-light); border-color: var(--sw-sky); }

/* Empty state */
.sw-empty {
    text-align: center;
    padding: 60px 20px;
    color: #94a3b8;
}
.sw-empty i { font-size: 3rem; margin-bottom: 16px; display: block; }
.sw-empty p { font-size: 1rem; font-weight: 600; }

/* Alerts */
.sw-alert {
    margin: 12px 28px;
    padding: 13px 18px;
    border-radius: 10px;
    font-size: 0.9rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}
.sw-alert.success { background: #dcfce7; border: 1px solid #86efac; color: #166534; }
.sw-alert.error   { background: #fee2e2; border: 1px solid #fca5a5; color: #991b1b; }

/* Tabs content area */
.sw-tab-content { display: none; }
.sw-tab-content.active { display: block; }

/* Siblings / Discount / Break-up / Wallet simple placeholders */
.sw-simple-panel {
    background: #fff;
    border: 1.5px solid var(--sw-border);
    border-radius: 12px;
    margin: 20px 28px;
    padding: 40px;
    text-align: center;
    color: #94a3b8;
}
.sw-simple-panel i { font-size: 2.5rem; margin-bottom: 14px; display: block; color: #bfdbfe; }
.sw-simple-panel p { font-size: 0.95rem; font-weight: 600; }

/* Robust Responsive Columns */
.sw-fee-record-row {
    display: flex;
    gap: 24px;
    align-items: start;
    margin-top: 20px;
}
.sw-fee-record-col-left {
    flex: 2;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 24px;
}
.sw-fee-record-col-right {
    flex: 1;
    min-width: 320px;
    position: sticky;
    top: 20px;
    background: #fff;
    border: 2px solid var(--sw-border);
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
    display: flex;
    flex-direction: column;
    gap: 16px;
}
@media (max-width: 1024px) {
    .sw-fee-record-row {
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 16px !important;
    }
    .sw-fee-record-col-left {
        width: 100% !important;
    }
    .sw-fee-record-col-right {
        width: 100% !important;
        position: static !important;
        min-width: 0 !important;
    }
}

@media (max-width: 768px) {
    .sw-filters { padding: 12px 14px; }
    .sw-info-grid { grid-template-columns: 1fr; }
    .sw-info-col:first-child { border-right: none; border-bottom: 1px solid var(--sw-border); }
    .sw-fee-record-wrap { margin: 16px 14px; }
    .sw-header { padding: 14px; }
    .sw-detail-topbar { padding: 12px 14px; flex-wrap: wrap; gap: 10px; }
    .sw-tabs { padding: 8px 10px; }
    .sw-summary-bar { margin-left: 0; width: 100%; display: flex; flex-wrap: wrap; gap: 8px; justify-content: flex-start; }
    .sw-info-panel { margin: 12px 14px; }
    .sw-installment-header {
        flex-wrap: wrap;
        gap: 10px;
    }
    .sw-installment-amounts {
        width: 100%;
        margin-top: 4px;
    }
    .sw-installment-right {
        width: 100%;
        justify-content: space-between;
        margin-top: 6px;
    }
    /* Particulars Table Mobile Scroll safety */
    .sw-installment-detail {
        overflow-x: auto;
    }
}

/* ── STUDENT-WISE FEE DARK MODE OVERRIDES ── */
body.dark-mode {
    --sw-blue:      #818cf8;
    --sw-blue2:     #6366f1;
    --sw-sky:       #38bdf8;
    --sw-sky-light: rgba(56, 189, 248, 0.15);
    --sw-dark:      #f8fafc;
    --sw-border:    #1e293b;
    --sw-bg:        #0f172a;
}
body.dark-mode .sw-wrap,
body.dark-mode .sw-detail-page {
    background: #0f172a !important;
}
body.dark-mode .sw-filters,
body.dark-mode .sw-table-toolbar,
body.dark-mode .sw-table-wrap,
body.dark-mode .sw-footer,
body.dark-mode .sw-detail-topbar,
body.dark-mode .sw-info-panel,
body.dark-mode .sw-tabs,
body.dark-mode .sw-fee-body,
body.dark-mode .sw-installment-detail,
body.dark-mode .sw-modal,
body.dark-mode .sw-pagination,
body.dark-mode .sw-simple-panel {
    background: #111827 !important;
    border-color: #1e293b !important;
    color: #cbd5e1 !important;
}
body.dark-mode .sw-student-name,
body.dark-mode .sw-topbar-session,
body.dark-mode .sw-info-value,
body.dark-mode .sw-installment-title h4,
body.dark-mode .sw-modal h3,
body.dark-mode .sw-modal-field label,
body.dark-mode .sw-page-btn {
    color: #f8fafc !important;
}
body.dark-mode .sw-student-sub,
body.dark-mode .sw-info-label,
body.dark-mode .sw-installment-amounts,
body.dark-mode .sw-quick-notes-label,
body.dark-mode .sw-tab {
    color: #94a3b8 !important;
}
body.dark-mode .sw-tab:hover,
body.dark-mode .sw-tab.active {
    color: #818cf8 !important;
    border-bottom-color: #818cf8 !important;
}
body.dark-mode .sw-btn-vis,
body.dark-mode .sw-btn-outline,
body.dark-mode .sw-page-btn,
body.dark-mode .sw-view-btn {
    background: #1f2937 !important;
    color: #818cf8 !important;
    border-color: #374151 !important;
}
body.dark-mode .sw-btn-vis:hover,
body.dark-mode .sw-btn-outline:hover,
body.dark-mode .sw-page-btn:hover:not(.active),
body.dark-mode .sw-view-btn:hover {
    background: #374151 !important;
    color: #ffffff !important;
    border-color: #4b5563 !important;
}
body.dark-mode .sw-select-all-label,
body.dark-mode .sw-footer-toggle {
    color: #cbd5e1 !important;
}
body.dark-mode .sw-filter-group select,
body.dark-mode .sw-filter-group input,
body.dark-mode .sw-modal-field input,
body.dark-mode .sw-modal-field select,
body.dark-mode .sw-topbar-session select,
body.dark-mode .sw-detail-topbar select {
    background-color: #1f2937 !important;
    color: #f8fafc !important;
    border-color: #374151 !important;
}
body.dark-mode .sw-table tbody tr {
    border-bottom-color: #1e293b !important;
}
body.dark-mode .sw-table tbody tr:hover {
    background: rgba(255, 255, 255, 0.04) !important;
}
body.dark-mode .sw-installment {
    border-bottom-color: #1e293b !important;
}
body.dark-mode .sw-installment:hover {
    background: rgba(255, 255, 255, 0.02) !important;
}
body.dark-mode .sw-rows-badge {
    background: #1f2937 !important;
    border-color: #374151 !important;
    color: #f8fafc !important;
}
body.dark-mode .sw-badge-due {
    background: rgba(220, 38, 38, 0.15) !important;
    color: #f87171 !important;
    border-color: #f87171 !important;
}
body.dark-mode .sw-inst-badge.paid {
    background: rgba(22, 163, 74, 0.15) !important;
    color: #4ade80 !important;
    border-color: #4ade80 !important;
}
body.dark-mode .sw-inst-badge.pending {
    background: rgba(234, 88, 12, 0.15) !important;
    color: #f97316 !important;
    border-color: #f97316 !important;
}
body.dark-mode .sw-inst-badge.partial {
    background: rgba(99, 102, 241, 0.15) !important;
    color: #818cf8 !important;
    border-color: #818cf8 !important;
}
body.dark-mode .sw-discount-strip {
    background: rgba(245, 158, 11, 0.1) !important;
    border-color: rgba(245, 158, 11, 0.2) !important;
}
body.dark-mode .sw-discount-strip label,
body.dark-mode .sw-discount-tag {
    color: #f59e0b !important;
}
body.dark-mode .sw-discount-tag {
    background: rgba(245, 158, 11, 0.15) !important;
    border-color: rgba(245, 158, 11, 0.3) !important;
}
body.dark-mode .sw-fee-details-header {
    background: rgba(220, 38, 38, 0.1) !important;
    border-bottom-color: rgba(220, 38, 38, 0.2) !important;
    color: #f87171 !important;
}
body.dark-mode .sw-info-col:first-child {
    border-right-color: #1e293b !important;
}
body.dark-mode .sw-info-divider {
    border-top-color: #1e293b !important;
}
body.dark-mode .sw-modal-cancel {
    background: #1f2937 !important;
    color: #cbd5e1 !important;
}
body.dark-mode .sw-quick-notes-icon {
    border: 2px solid #1e293b !important;
}
body.dark-mode .sw-table td [style*="color:#374151"] {
    color: #cbd5e1 !important;
}

/* Custom Multiselect styles */
.custom-multiselect {
    position: relative;
    width: 100%;
}
.multiselect-select-box {
    width: 100%;
    padding: 9px 13px;
    border: 1.5px solid var(--sw-border);
    border-radius: 9px;
    font-size: 0.93rem;
    font-weight: 600;
    color: var(--sw-dark);
    background: #fff;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-sizing: border-box;
}
body.dark-mode .multiselect-select-box {
    background: #1e293b;
    border-color: #334155;
    color: #cbd5e1;
}
.multiselect-dropdown-content {
    position: absolute;
    background: #fff;
    border: 1.5px solid var(--sw-border);
    border-radius: 9px;
    z-index: 1000;
    width: 100%;
    max-height: 220px;
    overflow-y: auto;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    padding: 8px;
    margin-top: 4px;
    box-sizing: border-box;
}
body.dark-mode .multiselect-dropdown-content {
    background: #0f172a;
    border-color: #334155;
    box-shadow: 0 10px 25px rgba(0,0,0,0.3);
}
.multiselect-search {
    width: 100%;
    padding: 6px 10px;
    border: 1.5px solid var(--sw-border);
    border-radius: 6px;
    margin-bottom: 8px;
    font-size: 0.85rem;
    box-sizing: border-box;
    outline: none;
}
body.dark-mode .multiselect-search {
    background: #1e293b;
    border-color: #334155;
    color: #cbd5e1;
}
.multiselect-options-list {
    max-height: 150px;
    overflow-y: auto;
}
.multiselect-option {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 6px 8px;
    font-size: 0.88rem;
    font-weight: 500;
    cursor: pointer;
    border-radius: 4px;
    color: var(--sw-dark);
}
body.dark-mode .multiselect-option {
    color: #cbd5e1;
}
.multiselect-option:hover {
    background: #f1f5f9;
}
body.dark-mode .multiselect-option:hover {
    background: #1e293b;
}
.multiselect-option input[type="checkbox"] {
    width: auto !important;
    margin: 0;
    cursor: pointer;
}
body.dark-mode #feeComponentSectionContainer {
    background: #0f172a !important;
    border-color: #334155 !important;
}
</style>

<div class="sw-wrap">
    @if(session('success'))
        <div style="margin: 20px 28px 0; padding: 15px; background: #dcfce7; border: 1.5px solid #86efac; color: #15803d; border-radius: 8px; font-weight: 700;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="margin: 20px 28px 0; padding: 15px; background: #fee2e2; border: 1.5px solid #fca5a5; color: #b91c1c; border-radius: 8px; font-weight: 700;">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div style="margin: 20px 28px 0; padding: 15px; background: #fee2e2; border: 1.5px solid #fca5a5; color: #b91c1c; border-radius: 8px; font-weight: 700;">
            <div style="font-weight: 800; margin-bottom: 5px;"><i class="fas fa-exclamation-triangle"></i> Please fix the following errors:</div>
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

{{-- ══════════════════════════════════════════════════════════════════════
     HEADER
══════════════════════════════════════════════════════════════════════ --}}
<div class="sw-header">
    <div class="sw-header-title">
        <div style="width:44px;height:44px;border-radius:12px;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;font-size:1.3rem;color:#fff;">
            <i class="fas fa-user-graduate"></i>
        </div>
        <div>
            <h1>Student-wise Fee</h1>
            <p>Fee Management</p>
        </div>
    </div>
    <div class="sw-breadcrumb">
        <a href="#">Fee Management</a>
        <i class="fas fa-chevron-right" style="font-size:.7rem;"></i>
        <span>Student-wise Fee</span>
    </div>
</div>

{{-- flash messages --}}
@if(session('success'))
<div class="sw-alert success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif
@if($errors->any())
<div class="sw-alert error"><i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}</div>
@endif

{{-- ══════════════════════════════════════════════════════════════════════
     DETAIL VIEW (when ?view_student=X is set)
══════════════════════════════════════════════════════════════════════ --}}
@if($viewStudent)

@php
    $totalOriginal   = $studentFees->sum('amount');
    $totalDiscount   = $studentFees->sum('instant_discount_amount');
    $totalFine       = $studentFees->sum('fine_amount_applied');
    $totalPaid       = $studentFees->sum('paid_amount');
    $totalRefunded   = $refunds->sum('amount');

    // Correct due = amount + fine_amount_applied - instant_discount_amount - paid_amount
    // This ensures discounts and fines are NEVER shown as part of the due balance
    $totalDue = $studentFees->reduce(function($carry, $sf) {
        if ($sf->status === 'refunded') return $carry; // skip refunded fees
        $due = max(0,
            floatval($sf->amount)
            + floatval($sf->fine_amount_applied ?? 0)
            - floatval($sf->instant_discount_amount)
            - floatval($sf->paid_amount)
        );
        return $carry + $due;
    }, 0);

    // Pending cheque amounts represent payment-in-transit — subtract from displayed due
    // so staff see the actual net outstanding balance
    $chequePendingAmt = $pendingChequeTotal ?? 0;
    $effectiveDue = max(0, $totalDue - $chequePendingAmt);

    $activeTab       = request('tab', 'fee_record');
@endphp

{{-- detail top bar --}}
<div class="sw-detail-topbar">
    <a href="{{ route('school.fees.student-wise', ['academic_session_id' => $selectedSession->id, 'class_id' => $viewStudent->class_id, 'section_id' => $viewStudent->section_id]) }}"
       class="sw-back-btn" title="Back"><i class="fas fa-chevron-left"></i></a>
    @if($viewStudent->photo)
        <img src="{{ $viewStudent->photo_url }}" alt="{{ $viewStudent->full_name }}" style="width:42px; height:42px; border-radius:50%; object-fit:cover; flex-shrink:0;">
    @else
        <div class="sw-student-avatar-lg">{{ strtoupper(substr($viewStudent->first_name, 0, 1)) }}</div>
    @endif
    <div style="font-size:.85rem;font-weight:700;color:var(--sw-dark);">{{ $viewStudent->full_name }}</div>

    <div class="sw-topbar-session">
        <i class="fas fa-calendar-alt" style="color:var(--sw-blue2);"></i>
        Academic Year:
        <select onchange="location.href='{{ route('school.fees.student-wise') }}?view_student={{ $viewStudent->id }}&academic_session_id='+this.value">
            @foreach($academicSessions as $sess)
                <option value="{{ $sess->id }}" {{ $sess->id == $selectedSession->id ? 'selected' : '' }}>
                    {{ $sess->name }}{{ $sess->is_current ? ' (Current)' : '' }}
                </option>
            @endforeach
        </select>
    </div>


</div>

{{-- Student info panel --}}
<div class="sw-info-panel" style="position:relative;">
    <div class="sw-info-grid">
        <div class="sw-info-col">
            <div class="sw-info-row">
                <span class="sw-info-label">Admission Id</span>
                <span class="sw-info-value">{{ $viewStudent->admission_number ?? 'N/A' }}</span>
            </div>
            <div class="sw-info-row">
                <span class="sw-info-label">Class</span>
                <span class="sw-info-value">
                    {{ optional($viewStudent->class)->name ?? 'N/A' }}
                    {{ optional($viewStudent->section)->name ? ' ' . $viewStudent->section->name : '' }}
                </span>
            </div>
            <div class="sw-info-row">
                <span class="sw-info-label">Fee Schedule</span>
                <span class="sw-info-value" style="color:var(--sw-blue2);">{{ $feeScheduleName ?? 'fees schedule 1' }}</span>
            </div>
            <div class="sw-info-row">
                <span class="sw-info-label">Father's Name</span>
                <span class="sw-info-value">{{ $viewStudent->father_name ?? '-' }}</span>
            </div>
            <div class="sw-info-row">
                <span class="sw-info-label">Father Phone Number</span>
                <span class="sw-info-value">
                    @if($viewStudent->father_phone)
                        <a href="tel:{{ $viewStudent->father_phone }}">{{ $viewStudent->father_phone }}</a>
                    @else -
                    @endif
                </span>
            </div>
            <div class="sw-info-row">
                <span class="sw-info-label">Student Contact</span>
                <span class="sw-info-value">{{ $viewStudent->phone ?? '-' }}</span>
            </div>
        </div>
        <div class="sw-info-col">
            <div class="sw-info-row">
                <span class="sw-info-label">Academic Year</span>
                <span class="sw-info-value" style="text-transform:uppercase;">{{ strtoupper($selectedSession->name ?? 'APR 2025 - MAR 2026') }}</span>
            </div>
            <div class="sw-info-row">
                <span class="sw-info-label">Mother's Name</span>
                <span class="sw-info-value">{{ $viewStudent->mother_name ?? '-' }}</span>
            </div>
            <div class="sw-info-row">
                <span class="sw-info-label">Mother Phone Number</span>
                <span class="sw-info-value">
                    @if($viewStudent->mother_phone)
                        <a href="tel:{{ $viewStudent->mother_phone }}" style="color:var(--sw-blue2);">{{ $viewStudent->mother_phone }}</a>
                    @else -
                    @endif
                </span>
            </div>
            <div class="sw-info-row">
                <span class="sw-info-label">Route and Stop</span>
                <span class="sw-info-value">{{ $viewStudent->transport_route ? $viewStudent->transport_route . ' - ' . $viewStudent->transport_stop : '-' }}</span>
            </div>
            <div class="sw-info-row">
                <span class="sw-info-label">Address</span>
                <span class="sw-info-value" style="font-size:.85rem;">
                    {{ collect([$viewStudent->address, $viewStudent->city, $viewStudent->state])->filter()->implode(', ') ?: '-' }}
                </span>
            </div>
            <div class="sw-info-row">
                <span class="sw-info-label">Student Email</span>
                <span class="sw-info-value">{{ $viewStudent->email ?? '-' }}</span>
            </div>
            <div class="sw-info-row">
                <span class="sw-info-label">Date Of Birth</span>
                <span class="sw-info-value">{{ $viewStudent->date_of_birth ? $viewStudent->date_of_birth->format('d M Y') : '-' }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Tabs + summary bar --}}
<div class="sw-tabs">
    <a href="{{ route('school.fees.student-wise', ['view_student' => $viewStudent->id, 'academic_session_id' => $selectedSession->id, 'tab' => 'fee_record']) }}"
       class="sw-tab {{ $activeTab == 'fee_record' ? 'active' : '' }}">
        Fee Record
    </a>
    <a href="{{ route('school.fees.student-wise', ['view_student' => $viewStudent->id, 'academic_session_id' => $selectedSession->id, 'tab' => 'refund']) }}"
       class="sw-tab {{ $activeTab == 'refund' ? 'active' : '' }}">
        Refund
    </a>
    <a href="{{ route('school.fees.student-wise', ['view_student' => $viewStudent->id, 'academic_session_id' => $selectedSession->id, 'tab' => 'siblings']) }}"
       class="sw-tab {{ $activeTab == 'siblings' ? 'active' : '' }}">
        Siblings ({{ $siblings->count() }})
    </a>
    <a href="{{ route('school.fees.student-wise', ['view_student' => $viewStudent->id, 'academic_session_id' => $selectedSession->id, 'tab' => 'discount']) }}"
       class="sw-tab {{ $activeTab == 'discount' ? 'active' : '' }}">
        Discount
    </a>
    <a href="{{ route('school.fees.student-wise', ['view_student' => $viewStudent->id, 'academic_session_id' => $selectedSession->id, 'tab' => 'fees_breakup']) }}"
       class="sw-tab {{ $activeTab == 'fees_breakup' ? 'active' : '' }}">
        Fees Break-up
    </a>
    <a href="{{ route('school.fees.student-wise', ['view_student' => $viewStudent->id, 'academic_session_id' => $selectedSession->id, 'tab' => 'wallet']) }}"
       class="sw-tab {{ $activeTab == 'wallet' ? 'active' : '' }}">
        Fee Wallet (₹0)
    </a>
    <a href="{{ route('school.fees.student-wise', ['view_student' => $viewStudent->id, 'academic_session_id' => $selectedSession->id, 'tab' => 'payment_history']) }}"
       class="sw-tab {{ $activeTab == 'payment_history' ? 'active' : '' }}">
        Payment History
    </a>
    <a href="{{ route('school.fees.student-wise', ['view_student' => $viewStudent->id, 'academic_session_id' => $selectedSession->id, 'tab' => 'followup']) }}"
       class="sw-tab {{ $activeTab == 'followup' ? 'active' : '' }}">
        Follow-up History
    </a>

    {{-- Summary badges --}}
    <div class="sw-summary-bar" style="padding:8px 0; display:flex; flex-wrap:wrap; gap:10px;">
        <div class="sw-badge-receivable" style="background:#eff6ff; color:#1e40af; border:1px solid #bfdbfe; font-weight:700; padding:6px 14px; border-radius:20px; font-size:0.85rem;">
            Total Fee: ₹{{ number_format($totalOriginal, 0) }}
        </div>
        <div class="sw-badge-discount" style="background:#fffbeb; color:#b45309; border:1px solid #fde68a; font-weight:700; padding:6px 14px; border-radius:20px; font-size:0.85rem;">
            Discount: ₹{{ number_format($totalDiscount, 0) }}
        </div>
        @php
            $hasAnyFineRecord = $studentFees->contains(function($f) {
                return floatval($f->fine_amount_applied) > 0 || $f->is_fine_applied === false || $f->is_fine_applied === 0;
            });
        @endphp
        @if($totalFine > 0 || $hasAnyFineRecord)
        <div onclick="openLateFineModal()" style="background:#fff7ed; color:#c2410c; border:1px solid #fed7aa; font-weight:700; padding:6px 14px; border-radius:20px; font-size:0.85rem; cursor:pointer; display:inline-flex; align-items:center; gap:6px; transition:all 0.2s ease;" title="Click to manage Late Fine for installments" onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 6px -1px rgba(194, 65, 12, 0.15)';" onmouseout="this.style.transform='none'; this.style.boxShadow='none';">
            <i class="fas fa-clock"></i> Late Fine: ₹{{ number_format($totalFine, 0) }}
            <i class="fas fa-sliders-h" style="font-size:0.75rem; opacity:0.8; margin-left:2px;"></i>
        </div>
        @endif
        <div class="sw-badge-paid" style="background:#f0fdf4; color:#166534; border:1px solid #bbf7d0; font-weight:700; padding:6px 14px; border-radius:20px; font-size:0.85rem;">
            Paid: ₹{{ number_format($totalPaid, 0) }}
        </div>
        <div class="sw-badge-refunded" style="background:#f3e8ff; color:#6b21a8; border:1px solid #e9d5ff; font-weight:700; padding:6px 14px; border-radius:20px; font-size:0.85rem;">
            Refunded: ₹{{ number_format($totalRefunded, 0) }}
        </div>
        @if($pendingCheques->isNotEmpty())
            @foreach($pendingCheques->groupBy('installment_no') as $instNo => $instCheques)
                <div style="background:#fffbeb; color:#92400e; border:1px solid #fcd34d; font-weight:700; padding:6px 14px; border-radius:20px; font-size:0.85rem;"
                     title="Cheque submitted but not yet cleared by bank">
                    <i class="fas fa-money-check" style="margin-right:4px;"></i>
                    Installment {{ $instNo }} - Cheque Pending: ₹{{ number_format($instCheques->sum('amount'), 0) }}
                </div>
            @endforeach
        @endif
        <div class="sw-badge-due" style="background:#fef2f2; color:#991b1b; border:1px solid #fca5a5; font-weight:700; padding:6px 14px; border-radius:20px; font-size:0.85rem;">
            Due: ₹{{ number_format($effectiveDue, 0) }}
            @if($chequePendingAmt > 0)
                <span style="font-size:0.75rem; font-weight:500; color:#b91c1c; margin-left:4px;">(after cheque)</span>
            @endif
        </div>
    </div>
</div>

{{-- ── FEE RECORD TAB ── --}}
@if($activeTab == 'fee_record')
<div class="sw-fee-record-wrap">
    <div class="sw-fee-record-header">
        <h3>Fee Record</h3>
        <button class="sw-btn-add-discount" onclick="openAddDiscountModal()">
            <i class="fas fa-tag"></i> ADD DISCOUNT
        </button>
    </div>

    {{-- Applied discounts strip --}}
    @if($appliedDiscounts->isNotEmpty())
    <div class="sw-discount-strip">
        <label>Applied Discount:</label>
        @foreach($appliedDiscounts as $disc)
            <span class="sw-discount-tag">
                <i class="fas fa-scissors"></i> {{ $disc->name }}
            </span>
        @endforeach
    </div>
    @endif

    <div class="sw-fee-record-row">
        {{-- Left Stacked Column: Tuition then Transport --}}
        <div class="sw-fee-record-col-left">
            {{-- Tuition & Class Fees --}}
            <div id="tuition-fees-section" style="width: 100%; transition: opacity 0.3s ease;">
            <div class="sw-fee-body" style="margin-top: 0;">
                <div class="sw-fee-details-header">Tuition & Class Fees</div>

                @php
                    $groupedFees = $tuitionFees->groupBy(function($fee) {
                        return $fee->installment_no ?? 1;
                    })->sortKeys();
                @endphp

                @forelse($groupedFees as $instNo => $instFees)
                @php
                    $instLabel = 'Installment ' . $instNo;
                    $groupTotal = $instFees->sum('amount') + $instFees->sum('fine_amount_applied');
                    $groupDiscount = $instFees->sum('instant_discount_amount');
                    $groupPaid  = $instFees->sum('paid_amount');
                    // Exclude refunded fees from the due calculation
                    $groupDue   = $instFees->reduce(function($carry, $f) {
                        if ($f->status === 'refunded') return $carry;
                        return $carry + max(0, floatval($f->amount) + floatval($f->fine_amount_applied) - floatval($f->instant_discount_amount) - floatval($f->paid_amount));
                    }, 0);
                    // Pending cheque amount for this specific installment
                    $instPendingChequeAmt = isset($pendingCheques)
                        ? $pendingCheques->filter(fn($c) => $c->installment_no == $instNo)->sum('amount')
                        : 0;
                    
                    // Check if all fees in group are refunded, or all paid/zero-unpaid have been refunded
                    $anyRefunded = $instFees->contains(function($f) {
                        return $f->status === 'refunded';
                    });
                    $noneCurrentlyPaid = $instFees->sum('paid_amount') == 0;
                    $allRefunded = $anyRefunded && $noneCurrentlyPaid;
                    
                    $status = 'pending';
                    if ($allRefunded) {
                        $status = 'refunded';
                    } elseif ($groupDue <= 0) {
                        $status = 'paid';
                    } elseif ($groupPaid > 0) {
                        $status = 'partially_paid';
                    }

                    $badgeStyle = '';
                    if ($status === 'paid') {
                        $badgeClass = 'paid';
                        $badgeIcon  = 'fa-check-circle';
                        $badgeLabel = 'Paid';
                    } elseif ($status === 'partially_paid') {
                        $badgeClass = 'partial';
                        $badgeIcon  = 'fa-circle-half-stroke';
                        $badgeLabel = 'Partial';
                    } elseif ($status === 'refunded') {
                        $badgeClass = 'pending';
                        $badgeIcon  = 'fa-undo';
                        $badgeLabel = 'Refunded';
                        $badgeStyle = 'background:#f3e8ff; color:#7e22ce; border-color:#e9d5ff;';
                    } else {
                        $badgeClass = 'pending';
                        $badgeIcon  = 'fa-clock';
                        $badgeLabel = 'Pending';
                    }
                @endphp
                <div class="sw-installment" id="inst-group-{{ $instNo }}" onclick="toggleInst(this)">
                    <div class="sw-installment-header">
                        <div style="display:flex; align-items:center; gap:12px;">
                            @if($status !== 'refunded')
                                <input type="checkbox" class="fee-installment-checkbox" 
                                       data-type="tuition" 
                                       data-inst="{{ $instNo }}" 
                                       data-due="{{ max(0, $groupDue - $instPendingChequeAmt) }}" 
                                       data-paid="{{ $groupPaid }}" 
                                       onclick="event.stopPropagation(); toggleSelectInstallment(this, 'tuition-inst-{{ $instNo }}')" 
                                       style="width: 18px; height: 18px; cursor: pointer; accent-color: var(--sw-blue2);">
                            @endif
                            <div class="sw-installment-title">
                                <h4>{{ $instLabel }}</h4>
                                <span class="sw-inst-badge {{ $badgeClass }}" style="{{ $badgeStyle }}">
                                    <i class="fas {{ $badgeIcon }}"></i> {{ $badgeLabel }}
                                </span>
                            </div>
                            <div class="sw-installment-amounts">
                                Total: 
                                <span class="blue">₹{{ number_format($groupTotal, 0) }}</span>
                                &nbsp; Due: <span class="red">₹{{ number_format(max(0, $groupDue - $instPendingChequeAmt), 0) }}</span>
                                &nbsp; Paid: <span class="green">₹{{ number_format($groupPaid, 0) }}</span>
                            </div>
                        </div>
                        <div class="sw-installment-right">
                            @if($status === 'refunded')
                                <span class="sw-inst-badge pending" onclick="event.stopPropagation()" style="font-size:0.8rem; padding:4px 10px; background:#f3e8ff; color:#7e22ce; border-color:#e9d5ff;">
                                    <i class="fas fa-undo"></i> Refunded
                                </span>
                            @elseif($status !== 'paid')
                                <button class="sw-mark-paid-btn"
                                    onclick="event.stopPropagation(); openMarkPaid({{ $viewStudent->id }}, {{ $instNo }}, {{ $groupDue }}, '{{ $instLabel }}', null, 'tuition', {{ $instPendingChequeAmt }})"
                                    title="Mark as Paid">
                                    Mark Paid
                                </button>
                            @else
                                <span class="sw-paid-text" onclick="event.stopPropagation()">
                                    <i class="fas fa-check-circle"></i> Paid
                                </span>
                            @endif
                            <i class="fas fa-chevron-down sw-chevron"></i>
                        </div>
                    </div>
                    <div class="sw-installment-detail">
                        @php
                            // Only show the most recent *payment* invoice in the header
                            // so refunds and refund-cancellations do NOT overwrite it.
                            $latestInvoice = \App\Models\FeeInvoice::where('school_id', auth()->user()->school_id)
                                ->where('student_id', $viewStudent->id)
                                ->where('installment_no', $instNo)
                                ->whereIn('type', ['payment', 'cancel_payment'])
                                ->orderBy('id', 'desc')
                                ->first();
                            
                            if ($latestInvoice) {
                                $invoiceNo = $latestInvoice->invoice_number;
                                $isInvCancelled = in_array($latestInvoice->type, ['cancel_payment', 'cancel_refund']) || $latestInvoice->status === 'cancelled';
                                $invoiceType = $latestInvoice->type;
                            } else {
                                $firstFee = $instFees->first();
                                $invoiceNo = $firstFee->invoice_no ?? ('INV-' . $instNo);
                                $isInvCancelled = $instFees->contains(function($f) {
                                    return isset($f->invoice_status) && $f->invoice_status === 'cancelled';
                                });
                                $invoiceType = 'invoice';
                            }
                        @endphp
                        
                        @if($groupPaid > 0)
                        <div style="display: flex; gap: 10px; margin-bottom: 12px; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px; align-items: center;">
                            <span style="font-weight: 700; color: var(--sw-blue); font-size: 0.88rem;">Invoice: #{{ $invoiceNo }}</span>
                            @if($isInvCancelled)
                                <span style="background: #fef2f2; color: #991b1b; border: 1.5px solid #ef4444; font-size: 0.72rem; padding: 2px 8px; border-radius: 4px; font-weight: 900; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fas fa-ban"></i> CANCELLED
                                </span>
                            @endif

                            <div style="margin-left: auto; display: flex; gap: 8px;">
                                <a href="{{ route('school.fees.print-slip', ['type' => $invoiceType, 'number' => $invoiceNo]) }}?student_id={{ $viewStudent->id }}" 
                                   target="_blank" 
                                   class="sw-btn-vis" 
                                   style="padding: 4px 10px; font-size: 0.8rem; font-weight: 700; border-radius: 6px; text-decoration: none; color: var(--sw-blue); border-color: var(--sw-border); display: inline-flex; align-items: center; gap: 6px; background: #fff;">
                                    <i class="fas fa-print"></i> Print Invoice
                                </a>

                                @if(!$isInvCancelled && $groupPaid > 0 && !$instFees->contains(function($f) { return $f->status === 'refunded' || $f->invoice_status === 'refunded'; }))
                                    <button type="button" 
                                            onclick="cancelInvoice('{{ $invoiceNo }}', {{ $instNo }}, {{ $viewStudent->id }})" 
                                            class="sw-btn-vis" 
                                            style="padding: 4px 10px; font-size: 0.8rem; font-weight: 700; border-radius: 6px; color: var(--sw-red); border-color: #fca5a5; background: #fff5f5; display: inline-flex; align-items: center; gap: 6px; cursor: pointer;">
                                        <i class="fas fa-ban"></i> Cancel Invoice
                                    </button>
                                @endif
                            </div>
                        </div>
                        @endif
                        
                        <table style="width:100%;font-size:.87rem;border-collapse:collapse;">
                            <thead>
                                <tr style="background:#f0f9ff;color:var(--sw-blue);font-weight:700;font-size:.8rem;text-transform:uppercase;">
                                    <th style="padding:8px 10px;text-align:left;">Fee Head</th>
                                    <th style="padding:8px 10px;text-align:right;">Amount</th>
                                    <th style="padding:8px 10px;text-align:right;">Paid</th>
                                    <th style="padding:8px 10px;text-align:right;">Due</th>
                                    <th style="padding:8px 10px;text-align:right;width:80px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($instFees as $fee)
                                <tr style="border-bottom:1px solid #e2e8f0; {{ $fee->status === 'refunded' ? 'background:#faf5ff;' : '' }}">
                                    <td style="padding:9px 10px;font-weight:600; display:flex; align-items:center; gap:8px;">
                                        @php
                                            $dueAmt = $fee->remaining_due;
                                        @endphp
                                        @if($fee->status !== 'refunded')
                                            <input type="checkbox" class="fee-item-checkbox tuition-inst-{{ $instNo }}" data-type="tuition" 
                                                   data-id="{{ $fee->id }}" 
                                                   data-due="{{ $dueAmt }}" 
                                                   data-paid="{{ $fee->paid_amount }}" 
                                                   data-inst="{{ $fee->installment_no }}" 
                                                   data-inst-label="{{ $instLabel }}"
                                                   data-comp-id="{{ $fee->fee_component_id }}"
                                                   data-label="{{ $instLabel }} - {{ optional($fee->component)->component_name ?: (optional($fee->category)->name ?: 'Fee') }}{{ $fee->misc_fee_id ? ' (Miscellaneous Fee)' : '' }}" 
                                                   onclick="event.stopPropagation(); updateSelectedFeesSummary()" 
                                                   style="width: 14px; height: 14px; cursor: pointer;">
                                        @endif
                                        <span>
                                            {{ optional($fee->component)->component_name ?: (optional($fee->category)->name ?: 'Fee') }}{{ $fee->misc_fee_id ? ' (Miscellaneous Fee)' : '' }}
                                        </span>
                                        @if($fee->fine_amount_applied > 0)
                                            <span style="font-size:0.75rem; background:#fee2e2; color:#ef4444; border:1px solid #fca5a5; border-radius:10px; padding:1px 8px; margin-left:6px; font-weight:700;" title="Late fine automatically applied due to timeline breach">
                                                <i class="fas fa-exclamation-triangle"></i> Fine: +₹{{ number_format($fee->fine_amount_applied, 0) }}
                                            </span>
                                        @endif
                                        @if($fee->status === 'refunded')
                                            <span style="font-size:0.7rem;background:#f3e8ff;color:#7e22ce;border:1px solid #e9d5ff;border-radius:10px;padding:1px 7px;margin-left:6px;font-weight:700;"><i class="fas fa-undo" style="font-size:0.65rem;"></i> Refunded</span>
                                        @endif
                                    </td>
                                    <td style="padding:9px 10px;text-align:right;font-weight:700;">
                                        <span>₹{{ number_format($fee->amount, 2) }}</span>
                                    </td>
                                    <td style="padding:9px 10px;text-align:right;font-weight:700;color:var(--sw-green);">₹{{ number_format($fee->paid_amount, 2) }}</td>
                                    <td style="padding:9px 10px;text-align:right;font-weight:700;color:var(--sw-red);">
                                        ₹{{ number_format($fee->remaining_due, 2) }}
                                    </td>
                                    <td style="padding:9px 10px;text-align:right;">
                                        @if($fee->paid_amount <= 0 && empty($fee->invoice_no) && is_null($fee->fine_applied_at))
                                            <form action="{{ route('school.fees.student-wise') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this component fee for the student?')" style="display:inline;">
                                                @csrf
                                                <input type="hidden" name="action" value="delete_student_fee">
                                                <input type="hidden" name="student_fee_id" value="{{ $fee->id }}">
                                                <button type="submit" style="background:none; border:none; color:var(--sw-red); cursor:pointer; padding: 2px 6px;" title="Delete Component">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        @else
                                            <span style="color:#cbd5e1; font-size:1.1rem; cursor:help;" title="{{ $fee->fine_applied_at ? 'Cannot delete because a fine has been applied. Waive it first.' : 'Paid or invoiced fees cannot be deleted.' }}">&times;</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div style="display: flex; align-items: center; margin-top: 8px;">
                            <button type="button" 
                                    onclick="event.stopPropagation(); openAddMiscFeeModal({{ $viewStudent->id }}, {{ $instNo }}, '{{ $instLabel }}')" 
                                    style="color:#d97706; font-weight:700; font-size:0.85rem; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:4px; border:none; background:none; padding:8px 0; outline:none;">
                                <i class="fas fa-plus"></i> ADD MISC FEE
                            </button>
                        </div>
                        @if($instFees->first() && $instFees->first()->due_date)
                        <div style="margin-top:10px;font-size:.82rem;color:#64748b;font-weight:600;">
                            <i class="fas fa-calendar"></i> Due Date: {{ \Carbon\Carbon::parse($instFees->first()->due_date)->format('d M Y') }}
                        </div>
                        @endif
                    </div>
                </div>
                @empty
                <div class="sw-empty">
                    <i class="fas fa-inbox"></i>
                    <p>No tuition or class fee records found for this student.</p>
                </div>
                @endforelse
            </div>
            </div>
            
            {{-- Transport Fee --}}
            <div id="transport-fees-section" style="width: 100%; transition: opacity 0.3s ease;">
            <div class="sw-fee-body" style="margin-top: 0;">
                <div class="sw-fee-details-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;">
                    <span>Transport Installments</span>
                    <a href="{{ route('school.fees.invoice') }}?student_id={{ $viewStudent->id }}" target="_blank" class="sw-btn-primary" style="font-size: 0.75rem; padding: 5px 10px; text-decoration: none; color: #fff; background: var(--sw-blue2); border-radius: 6px; font-weight:700;">
                        <i class="fas fa-file-invoice"></i> {{ $viewStudent->first_name }} View All Transport Invoice
                    </a>
                </div>

                @php
                    $isQuarterlyTransport = \App\Services\SettingService::get('quarterly_transport_payment', '0') == '1';
                    if ($isQuarterlyTransport) {
                        $groupedTransportFees = $transportFees->groupBy(function($fee) {
                            $ino = (int)($fee->installment_no ?? 1);
                            if ($ino <= 3) return 1;
                            if ($ino <= 6) return 2;
                            if ($ino <= 9) return 3;
                            return 4;
                        })->sortKeys();
                    } else {
                        $groupedTransportFees = $transportFees->groupBy(function($fee) {
                            return $fee->installment_no ?? 1;
                        })->sortKeys();
                    }
                @endphp

                @forelse($groupedTransportFees as $instNo => $instFees)
                @php
                    $firstFee = $instFees->first();
                    $monthName = null;
                    if ($isQuarterlyTransport) {
                        $qLabels = [
                            1 => 'Quarter 1 (April - June)',
                            2 => 'Quarter 2 (July - September)',
                            3 => 'Quarter 3 (October - December)',
                            4 => 'Quarter 4 (January - March)'
                        ];
                        $instLabel = 'Transport — ' . ($qLabels[$instNo] ?? ('Quarter ' . $instNo));
                    } else {
                        if ($firstFee && $firstFee->transportFeeSchedule) {
                            $instConfig = collect($firstFee->transportFeeSchedule->installments)->firstWhere('installment_no', $instNo);
                            if ($instConfig) {
                                if (!empty($instConfig['due_date'])) {
                                    $monthName = \Carbon\Carbon::parse($instConfig['due_date'])->format('F');
                                } elseif (!empty($instConfig['name'])) {
                                    try {
                                        $monthName = \Carbon\Carbon::parse($instConfig['name'])->format('F');
                                    } catch (\Exception $e) {
                                        $monthName = $instConfig['name'];
                                    }
                                }
                            }
                        }
                        if (!$monthName && $firstFee && $firstFee->due_date) {
                            $monthName = \Carbon\Carbon::parse($firstFee->due_date)->format('F');
                        }
                        
                        if ($monthName) {
                            $instLabel = 'Transport — ' . $monthName;
                        } else {
                            $instLabel = 'Transport — Installment ' . $instNo;
                        }
                    }
                    $groupTotal = $instFees->sum('amount') + $instFees->sum('fine_amount_applied');
                    $groupDiscount = $instFees->sum('instant_discount_amount');
                    $groupPaid  = $instFees->sum('paid_amount');
                    $groupDue   = $instFees->reduce(function($carry, $f) {
                        if ($f->status === 'refunded') return $carry;
                        return $carry + max(0, floatval($f->amount) + floatval($f->fine_amount_applied) - floatval($f->instant_discount_amount) - floatval($f->paid_amount));
                    }, 0);
                    // Pending cheque amount for this specific installment (transport)
                    $instPendingChequeAmt = isset($pendingCheques)
                        ? $pendingCheques->filter(fn($c) => $c->installment_no == $instNo)->sum('amount')
                        : 0;
                    
                    $anyRefunded = $instFees->contains(function($f) {
                        return $f->status === 'refunded';
                    });
                    $noneCurrentlyPaid = $instFees->sum('paid_amount') == 0;
                    $allRefunded = $anyRefunded && $noneCurrentlyPaid;
                    
                    $status = 'pending';
                    if ($allRefunded) {
                        $status = 'refunded';
                    } elseif ($groupDue <= 0) {
                        $status = 'paid';
                    } elseif ($groupPaid > 0) {
                        $status = 'partially_paid';
                    }

                    $badgeStyle = '';
                    if ($status === 'paid') {
                        $badgeClass = 'paid';
                        $badgeIcon  = 'fa-check-circle';
                        $badgeLabel = 'Paid';
                    } elseif ($status === 'partially_paid') {
                        $badgeClass = 'partial';
                        $badgeIcon  = 'fa-circle-half-stroke';
                        $badgeLabel = 'Partial';
                    } elseif ($status === 'refunded') {
                        $badgeClass = 'pending';
                        $badgeIcon  = 'fa-undo';
                        $badgeLabel = 'Refunded';
                        $badgeStyle = 'background:#f3e8ff; color:#7e22ce; border-color:#e9d5ff;';
                    } else {
                        $badgeClass = 'pending';
                        $badgeIcon  = 'fa-clock';
                        $badgeLabel = 'Pending';
                    }
                @endphp
                <div class="sw-installment" id="transport-group-{{ $instNo }}" onclick="toggleInst(this)">
                    <div class="sw-installment-header">
                        <div style="display:flex; align-items:center; gap:12px;">
                            @if($status !== 'refunded')
                                <input type="checkbox" class="fee-installment-checkbox" 
                                       data-type="transport" 
                                       data-inst="{{ $instNo }}" 
                                       data-due="{{ max(0, $groupDue - $instPendingChequeAmt) }}" 
                                       data-paid="{{ $groupPaid }}" 
                                       onclick="event.stopPropagation(); toggleSelectInstallment(this, 'transport-inst-{{ $instNo }}')" 
                                       style="width: 18px; height: 18px; cursor: pointer; accent-color: var(--sw-blue2);">
                            @endif
                            <div class="sw-installment-title">
                                <h4>{{ $instLabel }}</h4>
                                <span class="sw-inst-badge {{ $badgeClass }}" style="{{ $badgeStyle }}">
                                    <i class="fas {{ $badgeIcon }}"></i> {{ $badgeLabel }}
                                </span>
                            </div>
                            <div class="sw-installment-amounts">
                                Total: 
                                @if($groupDiscount > 0)
                                    <span style="text-decoration: line-through; color: #94a3b8; font-size: 0.8rem; margin-right: 4px;">₹{{ number_format($groupTotal, 0) }}</span>
                                    <span class="blue" style="font-weight: 800; color: #2563eb;">₹{{ number_format($groupTotal - $groupDiscount, 0) }}</span>
                                @else
                                    <span class="blue">₹{{ number_format($groupTotal, 0) }}</span>
                                @endif
                                &nbsp; Due: <span class="red">₹{{ number_format(max(0, $groupDue - $instPendingChequeAmt), 0) }}</span>
                                &nbsp; Paid: <span class="green">₹{{ number_format($groupPaid, 0) }}</span>
                            </div>
                        </div>
                        <div class="sw-installment-right">
                            @if($status === 'refunded')
                                <span class="sw-inst-badge pending" onclick="event.stopPropagation()" style="font-size:0.8rem; padding:4px 10px; background:#f3e8ff; color:#7e22ce; border-color:#e9d5ff;">
                                    <i class="fas fa-undo"></i> Refunded
                                </span>
                            @elseif($status !== 'paid')
                                @php
                                    $firstFeeId = $instFees->first() ? $instFees->first()->id : null;
                                @endphp
                                <button class="sw-mark-paid-btn"
                                    onclick="event.stopPropagation(); openMarkPaid({{ $viewStudent->id }}, {{ $instNo }}, {{ $groupDue }}, '{{ $instLabel }}', null, 'transport', {{ $instPendingChequeAmt }})"
                                    title="Mark as Paid">
                                    Mark Paid
                                </button>
                            @else
                                <span class="sw-paid-text" onclick="event.stopPropagation()">
                                    <i class="fas fa-check-circle"></i> Paid
                                </span>
                            @endif
                            <i class="fas fa-chevron-down sw-chevron"></i>
                        </div>
                    </div>
                    <div class="sw-installment-detail">
                        @php
                            $latestInvoice = \App\Models\FeeInvoice::where('school_id', auth()->user()->school_id)
                                ->where('student_id', $viewStudent->id)
                                ->where('installment_no', $instNo)
                                ->whereIn('type', ['payment', 'cancel_payment'])
                                ->where('payment_details', 'like', '%"student_fee_id":' . $instFees->first()->id . '%')
                                ->orderBy('id', 'desc')
                                ->first();
                            
                            if ($latestInvoice) {
                                $invoiceNo = $latestInvoice->invoice_number;
                                $isInvCancelled = in_array($latestInvoice->type, ['cancel_payment', 'cancel_refund']) || $latestInvoice->status === 'cancelled';
                                $invoiceType = $latestInvoice->type;
                            } else {
                                $firstFee = $instFees->first();
                                $invoiceNo = $firstFee->invoice_no ?? ('INV-T-' . $instNo);
                                $isInvCancelled = $instFees->contains(function($f) {
                                    return isset($f->invoice_status) && $f->invoice_status === 'cancelled';
                                });
                                $invoiceType = 'invoice';
                            }
                        @endphp
                        
                        @if($groupPaid > 0)
                        <div style="display: flex; gap: 10px; margin-bottom: 12px; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px; align-items: center;">
                            <span style="font-weight: 700; color: var(--sw-blue); font-size: 0.88rem;">Invoice: #{{ $invoiceNo }}</span>
                            @if($isInvCancelled)
                                <span style="background: #fef2f2; color: #991b1b; border: 1.5px solid #ef4444; font-size: 0.72rem; padding: 2px 8px; border-radius: 4px; font-weight: 900; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fas fa-ban"></i> CANCELLED
                                </span>
                            @endif

                            <div style="margin-left: auto; display: flex; gap: 8px;">
                                <a href="{{ route('school.fees.print-slip', ['type' => $invoiceType, 'number' => $invoiceNo]) }}?student_id={{ $viewStudent->id }}" 
                                   target="_blank" 
                                   class="sw-btn-vis" 
                                   style="padding: 4px 10px; font-size: 0.8rem; font-weight: 700; border-radius: 6px; text-decoration: none; color: var(--sw-blue); border-color: var(--sw-border); display: inline-flex; align-items: center; gap: 6px; background: #fff;">
                                    <i class="fas fa-print"></i> Print Invoice
                                </a>

                                @if(!$isInvCancelled && $groupPaid > 0 && !$instFees->contains(function($f) { return $f->status === 'refunded' || $f->invoice_status === 'refunded'; }))
                                    <button type="button" 
                                            onclick="cancelInvoice('{{ $invoiceNo }}', {{ $instNo }}, {{ $viewStudent->id }})" 
                                            class="sw-btn-vis" 
                                            style="padding: 4px 10px; font-size: 0.8rem; font-weight: 700; border-radius: 6px; color: var(--sw-red); border-color: #fca5a5; background: #fff5f5; display: inline-flex; align-items: center; gap: 6px; cursor: pointer;">
                                        <i class="fas fa-ban"></i> Cancel Invoice
                                    </button>
                                @endif
                            </div>
                        </div>
                        @endif
                        
                        <table style="width:100%;font-size:.87rem;border-collapse:collapse;">
                            <thead>
                                <tr style="background:#f0f9ff;color:var(--sw-blue);font-weight:700;font-size:.8rem;text-transform:uppercase;">
                                    <th style="padding:8px 10px;text-align:left;">Fee Head</th>
                                    <th style="padding:8px 10px;text-align:right;">Amount</th>
                                    <th style="padding:8px 10px;text-align:right;">Paid</th>
                                    <th style="padding:8px 10px;text-align:right;">Due</th>
                                    <th style="padding:8px 10px;text-align:right;width:80px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($instFees as $fee)
                                <tr style="border-bottom:1px solid #e2e8f0; {{ $fee->status === 'refunded' ? 'background:#faf5ff;' : '' }}">
                                    <td style="padding:9px 10px;font-weight:600; display:flex; align-items:center; gap:8px;">
                                        @php
                                            $dueAmt = $fee->remaining_due;
                                        @endphp
                                        @if($fee->status !== 'refunded')
                                            <input type="checkbox" class="fee-item-checkbox transport-inst-{{ $instNo }}" data-type="transport" 
                                                   data-id="{{ $fee->id }}" 
                                                   data-due="{{ $dueAmt }}" 
                                                   data-paid="{{ $fee->paid_amount }}" 
                                                   data-inst="{{ $fee->installment_no }}" 
                                                   data-inst-label="{{ $instLabel }}"
                                                   data-comp-id="{{ $fee->fee_component_id }}"
                                                   data-label="{{ $instLabel }} - {{ optional($fee->component)->component_name ?: 'Transport Fee' }}" 
                                                   onclick="event.stopPropagation(); updateSelectedFeesSummary()" 
                                                   style="width: 14px; height: 14px; cursor: pointer;">
                                        @endif
                                        <span>
                                            {{ optional($fee->component)->component_name ?: 'Transport Fee' }}
                                        </span>
                                        @if($fee->fine_amount_applied > 0)
                                            <span style="font-size:0.75rem; background:#fee2e2; color:#ef4444; border:1px solid #fca5a5; border-radius:10px; padding:1px 8px; margin-left:6px; font-weight:700;" title="Late fine automatically applied due to timeline breach">
                                                <i class="fas fa-exclamation-triangle"></i> Fine: +₹{{ number_format($fee->fine_amount_applied, 0) }}
                                            </span>
                                        @endif
                                        @if($fee->status === 'refunded')
                                            <span style="font-size:0.7rem;background:#f3e8ff;color:#7e22ce;border:1px solid #e9d5ff;border-radius:10px;padding:1px 7px;margin-left:6px;font-weight:700;"><i class="fas fa-undo" style="font-size:0.65rem;"></i> Refunded</span>
                                        @endif
                                    </td>
                                    <td style="padding:9px 10px;text-align:right;font-weight:700;">
                                        ₹{{ number_format($fee->amount, 2) }}
                                    </td>
                                    <td style="padding:9px 10px;text-align:right;font-weight:700;color:var(--sw-green);">₹{{ number_format($fee->paid_amount, 2) }}</td>
                                    <td style="padding:9px 10px;text-align:right;font-weight:700;color:var(--sw-red);">
                                        ₹{{ number_format($fee->remaining_due, 2) }}
                                    </td>
                                    <td style="padding:9px 10px;text-align:right;">
                                        @if($fee->paid_amount <= 0 && empty($fee->invoice_no) && is_null($fee->fine_applied_at))
                                            <form action="{{ route('school.fees.student-wise') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this transport fee for the student?')" style="display:inline;">
                                                @csrf
                                                <input type="hidden" name="action" value="delete_student_fee">
                                                <input type="hidden" name="student_fee_id" value="{{ $fee->id }}">
                                                <button type="submit" style="background:none; border:none; color:var(--sw-red); cursor:pointer; padding: 2px 6px;" title="Delete Component">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        @else
                                            <span style="color:#cbd5e1; font-size:1.1rem; cursor:help;" title="{{ $fee->fine_applied_at ? 'Cannot delete because a fine has been applied. Waive it first.' : 'Paid or invoiced fees cannot be deleted.' }}">&times;</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div style="display: flex; align-items: center; margin-top: 8px;">
                            <button type="button" 
                                    onclick="event.stopPropagation(); openAddMiscFeeModal({{ $viewStudent->id }}, {{ $instNo }}, '{{ $instLabel }}')" 
                                    style="color:#d97706; font-weight:700; font-size:0.85rem; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:4px; border:none; background:none; padding:8px 0; outline:none;">
                                <i class="fas fa-plus"></i> ADD MISC FEE
                            </button>
                        </div>
                        @if($instFees->first() && $instFees->first()->due_date)
                        <div style="margin-top:10px;font-size:.82rem;color:#64748b;font-weight:600;">
                            <i class="fas fa-calendar"></i> Due Date: {{ \Carbon\Carbon::parse($instFees->first()->due_date)->format('d M Y') }}
                        </div>
                        @endif
                    </div>
                </div>
                @empty
                <div class="sw-empty">
                    <i class="fas fa-inbox"></i>
                    <p>No transport fee installments found for this student.</p>
                </div>
                @endforelse
            </div>
        </div>
        </div>

        {{-- Right Column: Sticky Multi-Pay Panel --}}
        <div class="sw-fee-record-col-right">
            <h3 style="font-size: 1.2rem; font-weight: 800; color: var(--sw-blue); border-bottom: 2px solid var(--sw-border); padding-bottom: 8px; margin: 0; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-wallet" style="color: var(--sw-blue2);"></i> Pay Multiple Fees
            </h3>
            
            <div id="multi-pay-empty" style="color: #64748b; font-size: 0.9rem; text-align: center; padding: 20px 0;">
                <i class="fas fa-info-circle" style="font-size: 1.5rem; margin-bottom: 8px; display: block; color: #94a3b8;"></i>
                Select installments or components to collect combined payment.
            </div>
            
            <div id="multi-pay-details" style="display: none; flex-direction: column; gap: 12px;">
                <div style="max-height: 250px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px; background: #f8fafc; display:flex; flex-direction:column; gap:8px;" id="selected-items-list">
                    <!-- selected items will render here dynamically -->
                </div>
                
                <div style="display: flex; justify-content: space-between; font-weight: 700; font-size: 0.95rem; border-top: 1px dashed #cbd5e1; padding-top: 10px;">
                    <span id="multi-pay-total-label">Total Selected Due:</span>
                    <span style="color: var(--sw-red);" id="selected-total-due">₹0.00</span>
                </div>
                
                <button id="multi-pay-action-btn" class="sw-btn-primary" style="width: 100%; height: 42px; font-weight: 800; border-radius: 8px; margin-top: 8px;" onclick="openMultiPayModal()">
                    <i class="fas fa-cash-register"></i> Collect Combined Payment
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ── REFUND TAB ── --}}
@elseif($activeTab == 'refund')
<div class="sw-fee-record-wrap">
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; align-items:start;">
        
        <!-- Left Column: Paid/Refundable Installments -->
        <div class="sw-fee-body" style="border:1.5px solid var(--sw-border); border-radius:12px; padding:20px; background:#fff;">
            <h3 style="font-size:1.1rem; font-weight:800; color:var(--sw-blue); margin-bottom:15px; border-bottom:1.5px solid var(--sw-border); padding-bottom:8px; margin-top:0;">
                <i class="fas fa-list-check" style="margin-right:6px;"></i> Paid / Partially Paid Fees
            </h3>
            
            @php
                $refundableFees = $studentFees->filter(function($fee) {
                    $alreadyRefunded = \App\Models\FeeRefund::where('student_fee_id', $fee->id)->sum('amount');
                    return ($fee->paid_amount - $alreadyRefunded) > 0;
                });
            @endphp

            @forelse($refundableFees as $sf)
            @php
                $alreadyRefunded = \App\Models\FeeRefund::where('student_fee_id', $sf->id)->sum('amount');
                $maxRefundable = $sf->paid_amount - $alreadyRefunded;
                $percent = round(($sf->paid_amount / $sf->amount) * 100, 2);
            @endphp
            <div style="border:1px solid var(--sw-border); border-radius:8px; padding:12px 14px; margin-bottom:12px; display:flex; align-items:center; gap:12px; transition:background .15s; cursor:pointer;" class="refund-item-row" onclick="const chk = document.getElementById('ref-check-{{ $sf->id }}'); if(event.target !== chk) { chk.checked = !chk.checked; chk.dispatchEvent(new Event('change')); }">
                <input type="checkbox" class="refund-checkbox" 
                       id="ref-check-{{ $sf->id }}" 
                       value="{{ $sf->id }}" 
                       data-amount="{{ $maxRefundable }}"
                       data-label="{{ optional($sf->component)->component_name ?: (optional($sf->category)->name ?: 'Fee') }}{{ $sf->misc_fee_id ? ' (Miscellaneous Fee)' : '' }} - Inst {{ $sf->installment_no }}"
                       style="width:18px; height:18px; accent-color:var(--sw-blue2); cursor:pointer;">
                <div style="flex-grow:1;">
                    <div style="font-weight:700; font-size:.92rem; color:var(--sw-dark);">
                         {{ optional($sf->component)->component_name ?: (optional($sf->category)->name ?: 'Fee Component') }}{{ $sf->misc_fee_id ? ' (Miscellaneous Fee)' : '' }} 
                         <span style="font-size:0.8rem; color:#64748b; font-weight:600;">(Installment {{ $sf->installment_no }})</span>
                    </div>
                    <div style="font-size:.8rem; color:#64748b; margin-top:3px;">
                        Paid: <strong style="color:var(--sw-green);">₹{{ number_format($sf->paid_amount, 0) }}</strong> 
                        @if($alreadyRefunded > 0)
                            (Refunded: <span style="color:#7e22ce; font-weight:700;">₹{{ number_format($alreadyRefunded, 0) }}</span>, Net: <strong style="color:var(--sw-blue2);">₹{{ number_format($maxRefundable, 0) }}</strong>)
                        @endif
                        / Total: ₹{{ number_format($sf->amount, 0) }}
                        &nbsp;·&nbsp;<span class="sw-inst-badge paid" style="font-size:0.7rem; padding:1px 6px;">{{ $percent }}% Paid</span>
                    </div>
                </div>
            </div>
            @empty
            <div class="sw-empty" style="padding:40px 10px;">
                <i class="fas fa-receipt" style="font-size:2rem; margin-bottom:10px; color:#bfdbfe; display:block;"></i>
                <p style="font-size:.88rem; font-weight:600; color:#64748b;">No paid or partially paid fees available to refund.</p>
            </div>
            @endforelse
        </div>
        
        <!-- Right Column: Refund Details Form -->
        <div class="sw-fee-body" style="border:1.5px solid var(--sw-border); border-radius:12px; padding:20px; background:#fff;">
            <h3 style="font-size:1.1rem; font-weight:800; color:var(--sw-blue); margin-bottom:15px; border-bottom:1.5px solid var(--sw-border); padding-bottom:8px; margin-top:0;">
                <i class="fas fa-undo-alt" style="margin-right:6px;"></i> New Refund Details
            </h3>
            
            <form method="POST" action="{{ route('school.fees.student-wise') }}" id="refundForm">
                @csrf
                <input type="hidden" name="action" value="process_refund">
                <input type="hidden" name="student_id" value="{{ $viewStudent->id }}">
                <input type="hidden" name="academic_session_id" value="{{ $selectedSession->id }}">
                
                <div style="display:flex; gap:10px;">
                    <div class="sw-modal-field" style="flex:1;">
                        <label>Refund Date</label>
                        <input type="date" name="refund_date" required value="{{ now()->format('Y-m-d') }}">
                    </div>
                    <div class="sw-modal-field" style="flex:1;">
                        <label>Slip No (Ref)</label>
                        <input type="text" name="slip_no" required value="REF-{{ rand(100000, 999999) }}">
                    </div>
                </div>

                <div class="sw-modal-field">
                    <label>Payment Mode</label>
                    <select name="payment_mode" id="refund_payment_mode" onchange="toggleRefundModeFields(this.value)">
                        <option value="cash">Cash</option>
                        <option value="cheque">Cheque</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="online">Online / UPI</option>
                    </select>
                </div>

                <!-- Cheque/Bank Transfer Specific Fields -->
                <div id="refundChequeFields" style="display:none; border: 1.5px dashed var(--sw-border); padding: 14px; border-radius: 8px; margin-bottom:14px; background:#fcfdff;">
                    <div class="sw-modal-field">
                        <label style="color:#b45309;">Bank Name</label>
                        <input type="text" name="bank_name" id="refund_bank_name" placeholder="e.g. State Bank of India">
                    </div>
                    <div class="sw-modal-field">
                        <label style="color:#b45309;">Bank Date</label>
                        <input type="date" name="bank_date" id="refund_bank_date" value="{{ now()->format('Y-m-d') }}">
                    </div>
                </div>

                <div class="sw-modal-field">
                    <label>Refund Amount (₹)</label>
                    <input type="number" name="amount" id="refundTotalAmt"
                           min="1" step="0.01"
                           value="0"
                           placeholder="Enter refund amount"
                           style="font-weight:800; font-size:1.05rem; color:var(--sw-blue2);"
                           oninput="validateRefundAmount(this)">
                    <div id="refundAmtError" style="color:#dc2626; font-size:0.78rem; font-weight:700; margin-top:4px; display:none;"></div>
                </div>

                <div class="sw-modal-field">
                    <label>Remarks / Reason</label>
                    <input type="text" name="reason" required placeholder="e.g. Admission cancellation, Fee adjustment">
                </div>

                <!-- Selected Items List -->
                <div style="border: 1.5px dashed var(--sw-border); border-radius:8px; padding:12px; margin-bottom:18px; display:none;" id="selectedRefundItemsWrap">
                    <div style="font-size:.8rem; font-weight:800; color:var(--sw-blue); margin-bottom:8px;">SELECTED ITEMS FOR REFUND</div>
                    <div id="selectedRefundItemsList" style="display:flex; flex-direction:column; gap:6px;"></div>
                </div>

                <button type="submit" class="sw-btn-primary" style="width:100%; justify-content:center; padding:12px; font-size:.95rem; font-weight:800;" id="btnProcessRefund" disabled>
                    <i class="fas fa-undo"></i> PROCESS REFUND
                </button>
            </form>
        </div>
    </div>

    {{-- Refund History Section ── --}}
    @php
        $refundInvoices = \App\Models\FeeInvoice::where('school_id', auth()->user()->school_id)
            ->where('student_id', $viewStudent->id)
            ->where('type', 'refund')
            ->orderBy('id', 'desc')
            ->get();

        $refundHistory = collect();

        foreach ($refundInvoices as $inv) {
            $details = json_decode($inv->payment_details, true);
            $slipNo = is_array($details) ? ($details['slip_no'] ?? $inv->invoice_number) : $inv->invoice_number;
            $components = is_array($details) ? ($details['components'] ?? []) : [];
            
            $refundHistory->push((object)[
                'slip_no' => $slipNo,
                'refund_date' => $inv->payment_date,
                'reason' => $inv->remarks,
                'payment_mode' => $inv->payment_mode,
                'amount' => $inv->amount,
                'status' => $inv->status, // 'refunded' or 'cancelled'
                'invoice_record' => $inv,
                'components' => $components,
                'is_legacy' => false,
            ]);
        }

        // Now append legacy refunds that don't have a matching FeeInvoice
        $legacyRefundsGrouped = $refunds->groupBy('slip_no');
        foreach ($legacyRefundsGrouped as $slipNo => $refItems) {
            // Check if we already have this slip_no in refundHistory
            $exists = $refundHistory->contains(function($item) use ($slipNo) {
                return $item->slip_no === $slipNo;
            });
            
            if (!$exists) {
                $first = $refItems->first();
                $totalRefAmt = $refItems->sum('amount');
                $components = $refItems->map(function($item) {
                    $desc = $item->reason;
                    if (strpos($desc, ' (Refunded: ') !== false) {
                        $desc = str_replace(' (Refunded: ', '', strstr($desc, ' (Refunded: '));
                        $desc = rtrim($desc, ')');
                    } else {
                        $desc = 'Fee component';
                    }
                    return [
                        'component_name' => $desc,
                        'amount_paid' => $item->amount,
                    ];
                })->toArray();

                $refundHistory->push((object)[
                    'slip_no' => $slipNo,
                    'refund_date' => $first->refund_date,
                    'reason' => explode(' (Refunded:', $first->reason)[0],
                    'payment_mode' => $first->payment_mode,
                    'amount' => $totalRefAmt,
                    'status' => 'refunded',
                    'invoice_record' => null,
                    'components' => $components,
                    'is_legacy' => true,
                ]);
            }
        }
    @endphp
    @if($refundHistory->isNotEmpty())
    <div class="sw-fee-body" style="border:1.5px solid var(--sw-border); border-radius:12px; padding:20px; background:#fff; margin-top:20px;">
        <h3 style="font-size:1.1rem; font-weight:800; color:var(--sw-blue); margin-bottom:15px; border-bottom:1.5px solid var(--sw-border); padding-bottom:8px; margin-top:0; display:flex; align-items:center; gap:8px;">
            <i class="fas fa-history"></i> Refund History Logs
        </h3>
        <div class="sw-table-wrap">
            <table class="sw-table">
            <thead>
                <tr style="background:#e2e8f0; color:#1e293b; font-weight:700; font-size:.8rem; text-transform:uppercase;">
                    <th style="padding:10px; text-align:left; color:#1e293b;">Slip No.</th>
                    <th style="padding:10px; text-align:left; color:#1e293b;">Date</th>
                    <th style="padding:10px; text-align:left; color:#1e293b;">Components Refunded</th>
                    <th style="padding:10px; text-align:left; color:#1e293b;">Reason</th>
                    <th style="padding:10px; text-align:left; color:#1e293b;">Payment Mode</th>
                    <th style="padding:10px; text-align:right; color:#1e293b;">Amount Refunded</th>
                    <th style="padding:10px; text-align:center; color:#1e293b;">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($refundHistory as $item)
                @php
                    $refAlreadyCancelled = $item->status === 'cancelled';
                    if ($item->invoice_record && !$refAlreadyCancelled) {
                        $refAlreadyCancelled = \App\Models\FeeInvoice::where('related_invoice_id', $item->invoice_record->id)->exists();
                    }
                @endphp
                <tr style="border-bottom:1px solid #cbd5e1; font-size: 0.88rem; {{ $refAlreadyCancelled ? 'background: #fff5f5;' : 'background: #ffffff;' }}">
                    <td style="padding:10px; font-weight:700; color:{{ $refAlreadyCancelled ? '#b91c1c' : '#1e3a8a' }};">
                        {{ $item->slip_no }}
                    </td>
                    <td style="padding:10px; font-weight:600; color:#0f172a;">{{ \Carbon\Carbon::parse($item->refund_date)->format('d M Y') }}</td>
                    <td style="padding:10px;">
                        @foreach($item->components as $comp)
                            <div style="font-weight:600; color:{{ $refAlreadyCancelled ? '#475569' : '#0f172a' }}; margin-bottom:2px;">
                                • {{ $comp['component_name'] ?? 'Fee Component' }} (₹{{ number_format($comp['amount_paid'] ?? 0, 0) }})
                            </div>
                        @endforeach
                    </td>
                    <td style="padding:10px; color:#1e293b; font-weight:600;">{{ $item->reason }}</td>
                    <td style="padding:10px;">
                        <span style="text-transform: uppercase; font-size:0.7rem; font-weight:700; background:{{ $refAlreadyCancelled ? '#fee2e2' : '#e2e8f0' }}; padding:3px 8px; border-radius:4px; color:#0f172a;">
                            {{ str_replace('_', ' ', $item->payment_mode) }}
                        </span>
                    </td>
                    <td style="padding:10px; text-align:right; font-weight:800; color:#b91c1c; font-size:0.95rem;">
                        ₹{{ number_format($item->amount, 2) }}
                    </td>
                    <td style="padding:10px; text-align:center; white-space:nowrap;">
                        {{-- Print button --}}
                        <button type="button" onclick="window.open('{{ route('school.fees.print-slip', ['type' => 'refund', 'number' => $item->invoice_record ? $item->invoice_record->invoice_number : $item->slip_no]) }}', '_blank', 'width=950,height=750')" style="background:none; border:none; color:#1e3a8a; cursor:pointer; font-size:1.15rem; padding:4px 6px;" title="Print Refund Invoice">
                            <i class="fas fa-print"></i>
                        </button>
                        {{-- Cancel Refund button — only if not already cancelled --}}
                        @if(!$refAlreadyCancelled)
                            <button type="button"
                                    onclick="openCancelReasonModal('{{ $item->invoice_record ? $item->invoice_record->invoice_number : $item->slip_no }}', {{ $item->invoice_record ? ($item->invoice_record->installment_no ?? 1) : 1 }}, {{ $viewStudent->id }}, 'refund')"
                                    style="background:none; border:1.5px solid #fca5a5; border-radius:6px; color:#dc2626; cursor:pointer; font-size:0.8rem; font-weight:700; padding:3px 8px; margin-left:4px;"
                                    title="Cancel this refund">
                                ✕
                            </button>
                        @elseif($refAlreadyCancelled)
                            <span style="font-size:0.7rem; font-weight:800; color:#b91c1c; background:#fee2e2; border-radius:4px; padding:2px 7px; margin-left:4px; border: 1px solid #fca5a5;">Cancelled</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>
    @endif
</div>

<script>
function toggleRefundModeFields(mode) {
    const fields = document.getElementById('refundChequeFields');
    const bankNameInput = document.getElementById('refund_bank_name');
    const bankDateInput = document.getElementById('refund_bank_date');
    if (mode === 'cheque' || mode === 'bank_transfer') {
        fields.style.display = 'block';
        bankNameInput.setAttribute('required', 'required');
        bankDateInput.setAttribute('required', 'required');
    } else {
        fields.style.display = 'none';
        bankNameInput.removeAttribute('required');
        bankDateInput.removeAttribute('required');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.refund-checkbox');
    const totalInput = document.getElementById('refundTotalAmt');
    const itemsWrap = document.getElementById('selectedRefundItemsWrap');
    const itemsList = document.getElementById('selectedRefundItemsList');
    const submitBtn = document.getElementById('btnProcessRefund');
    let maxRefundable = 0;

    function updateRefundTotals() {
        let total = 0;
        let html = '';
        let checkedCount = 0;

        checkboxes.forEach(cb => {
            if (cb.checked) {
                checkedCount++;
                const amt = parseFloat(cb.getAttribute('data-amount'));
                const label = cb.getAttribute('data-label');
                const id = cb.value;
                total += amt;

                html += `
                <div style="display:flex; justify-content:space-between; align-items:center; background:var(--sw-sky-light); border:1px solid var(--sw-border); padding:6px 10px; border-radius:6px; font-size:.82rem; font-weight:700; color:var(--sw-blue2);">
                    <div style="flex-grow:1;">${label} (₹${amt.toFixed(0)})</div>
                    <i class="fas fa-trash-can" style="color:var(--sw-red); cursor:pointer; font-size:1rem;" onclick="document.getElementById('ref-check-${id}').click(); event.stopPropagation();"></i>
                    <input type="hidden" name="fee_ids[]" value="${id}">
                </div>`;
            }
        });

        maxRefundable = total;
        // Set the numeric input: default to the max selectable amount
        totalInput.setAttribute('max', total.toFixed(2));
        totalInput.value = total.toFixed(2);
        // Clear any validation error when selection changes
        const errEl = document.getElementById('refundAmtError');
        if (errEl) errEl.style.display = 'none';
        
        if (checkedCount > 0) {
            itemsWrap.style.display = 'block';
            itemsList.innerHTML = html;
            submitBtn.removeAttribute('disabled');
        } else {
            itemsWrap.style.display = 'none';
            itemsList.innerHTML = '';
            totalInput.value = '0';
            totalInput.removeAttribute('max');
            submitBtn.setAttribute('disabled', 'disabled');
        }
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateRefundTotals);
    });

    // Block form submission if refund amount is invalid
    const refundForm = document.getElementById('refundForm');
    if (refundForm) {
        refundForm.addEventListener('submit', function(e) {
            const val = parseFloat(totalInput.value);
            const max = parseFloat(totalInput.getAttribute('max') || '0');
            const errEl = document.getElementById('refundAmtError');
            if (!val || val < 1 || val > max) {
                e.preventDefault();
                if (errEl) {
                    errEl.textContent = 'Amount must be between ₹1 and ₹' + max.toFixed(2);
                    errEl.style.display = 'block';
                }
                totalInput.focus();
            }
        });
    }
});
</script>

{{-- ── SIBLINGS TAB ── --}}
@elseif($activeTab == 'siblings')
<div class="sw-fee-record-wrap">
    @forelse($siblings as $sib)
        @php
            $sibOriginal   = $sib->studentFees->sum('amount');
            $sibDiscount   = $sib->studentFees->sum('instant_discount_amount');
            $sibPaid       = $sib->studentFees->sum('paid_amount');
            $sibDue = $sib->studentFees->reduce(function($carry, $sf) {
                if ($sf->status === 'refunded') return $carry;
                return $carry + max(0, $sf->amount - $sf->instant_discount_amount - $sf->paid_amount);
            }, 0);
        @endphp
        <div style="background:#fff; border:1.5px solid var(--sw-border); border-radius:12px; padding:20px; margin-bottom:20px; display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; gap:16px;">
            <div style="display:flex; align-items:center; gap:14px;">
                @if($sib->photo)
                    <img src="{{ $sib->photo_url }}" alt="{{ $sib->full_name }}" style="width:46px; height:46px; border-radius:50%; object-fit:cover;">
                @else
                    <div style="width:46px; height:46px; border-radius:50%; background:linear-gradient(135deg, var(--sw-blue2), var(--sw-blue)); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:1.1rem;">
                        {{ strtoupper(substr($sib->first_name, 0, 1)) }}
                    </div>
                @endif
                <div>
                    <h4 style="margin:0; font-size:1.05rem; font-weight:800; color:var(--sw-dark);">{{ $sib->full_name }}</h4>
                    <p style="margin:4px 0 0; font-size:0.82rem; color:#64748b; font-weight:600;">
                        <span>Adm: <strong>{{ $sib->admission_number }}</strong></span> | 
                        <span>Class: <strong>{{ optional($sib->class)->name ?? '-' }} {{ optional($sib->section)->name ? 'Section ' . $sib->section->name : '' }}</strong></span>
                    </p>
                </div>
            </div>
            
            <div style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
                <div style="background:#eff6ff; color:#1e40af; border:1px solid #bfdbfe; font-weight:700; padding:6px 12px; border-radius:20px; font-size:0.8rem;">
                    Total: ₹{{ number_format($sibOriginal, 0) }}
                </div>
                <div style="background:#f0fdf4; color:#166534; border:1px solid #bbf7d0; font-weight:700; padding:6px 12px; border-radius:20px; font-size:0.8rem;">
                    Paid: ₹{{ number_format($sibPaid, 0) }}
                </div>
                <div style="background:#fef2f2; color:#991b1b; border:1px solid #fca5a5; font-weight:700; padding:6px 12px; border-radius:20px; font-size:0.8rem;">
                    Due: ₹{{ number_format($sibDue, 0) }}
                </div>
            </div>

            <div style="display:flex; gap:10px;">
                <a href="{{ route('school.fees.student-wise', ['view_student' => $sib->id, 'academic_session_id' => $selectedSession->id]) }}" 
                   class="sw-btn-vis" 
                   style="text-decoration:none; padding:8px 16px; border-radius:8px; font-size:0.85rem; font-weight:700; color:var(--sw-blue2); border-color:var(--sw-blue2); background:#fff; display:inline-flex; align-items:center; gap:6px;">
                    <i class="fas fa-eye"></i> View Profile
                </a>
                
                @if($sibDue > 0)
                    @php
                        // Find the first pending installment for this sibling
                        $firstPendingFee = $sib->studentFees->where('status', '!=', 'paid')->first();
                        $firstPendingInst = $firstPendingFee ? $firstPendingFee->installment_no : 1;
                        $firstPendingDue = $sib->studentFees->where('installment_no', $firstPendingInst)->sum(function($f) {
                            return max(0, $f->amount - $f->instant_discount_amount - $f->paid_amount);
                        });
                        $firstPendingLabel = 'Installment ' . $firstPendingInst;
                    @endphp
                    <button type="button" 
                            onclick="openMarkPaid({{ $sib->id }}, {{ $firstPendingInst }}, {{ $firstPendingDue }}, '{{ $firstPendingLabel }}', null, 'tuition')"
                            class="sw-mark-paid-btn" 
                            style="padding:8px 16px; border-radius:8px; font-size:0.85rem; font-weight:700; background:var(--sw-green); color:#fff; border:none; display:inline-flex; align-items:center; gap:6px; cursor:pointer;">
                        <i class="fas fa-indian-rupee-sign"></i> Pay Fee
                    </button>
                @else
                    <span style="font-size:0.85rem; font-weight:800; color:var(--sw-green); display:inline-flex; align-items:center; gap:4px; padding:8px 16px;">
                        <i class="fas fa-check-circle"></i> Fully Paid
                    </span>
                @endif
            </div>
        </div>
    @empty
        <div class="sw-simple-panel">
            <i class="fas fa-users"></i>
            <p>No siblings linked to this student.</p>
        </div>
    @endforelse
</div>

{{-- ── DISCOUNT TAB ── --}}
@elseif($activeTab == 'discount')
<div class="sw-fee-record-wrap">
    <div class="sw-fee-body" style="border-radius:12px; border:1.5px solid var(--sw-border); padding:20px; background:#fff;">
        <h3 style="font-size:1.1rem; font-weight:800; color:var(--sw-blue); margin-bottom:15px; border-bottom:1.5px solid var(--sw-border); padding-bottom:8px; margin-top:0;">
            <i class="fas fa-tags" style="margin-right:6px;"></i> Active Discounts
        </h3>
        
        @if($appliedDiscounts->isNotEmpty())
            @foreach($appliedDiscounts as $d)
            <div style="border:1px solid var(--sw-border); border-radius:8px; padding:12px 16px; margin-bottom:10px; display:flex; justify-content:space-between; align-items:center; background:#f8fafc;">
                <div>
                    <div style="font-weight:700; font-size:.95rem; color:var(--sw-dark); display:flex; align-items:center; flex-wrap:wrap; gap:8px;">
                        <span>{{ $d->name }}</span>
                        @if($d->installment_no)
                            <span style="font-size:0.75rem; color:#d97706; font-weight:700; background:#fef3c7; padding:2px 8px; border-radius:4px;">
                                Installment {{ $d->installment_no }} Only
                            </span>
                        @else
                            <span style="font-size:0.75rem; color:#2563eb; font-weight:700; background:#dbeafe; padding:2px 8px; border-radius:4px;">
                                All Installments
                            </span>
                        @endif
                    </div>
                    @if($d->remarks)
                    <div style="font-size:0.8rem; color:#64748b; margin-top:4px;">
                        Remarks: {{ $d->remarks }}
                    </div>
                    @endif
                </div>
                <div style="display:flex; align-items:center; gap:16px;">
                    <span style="font-weight:800; font-size:1.05rem; color:var(--sw-blue2);">
                        {{ $d->type === 'percentage' ? number_format($d->amount, 0) . '%' : '₹' . number_format($d->amount, 2) }}
                    </span>
                    
                    <!-- Delete Button -->
                    <form method="POST" action="{{ route('school.fees.student-wise') }}" onsubmit="return confirm('Are you sure you want to remove this discount? This will recalculate the student fees.');" style="margin:0;">
                        @csrf
                        <input type="hidden" name="action" value="remove_discount">
                        <input type="hidden" name="discount_id" value="{{ $d->id }}">
                        <input type="hidden" name="student_id" value="{{ $viewStudent->id }}">
                        <input type="hidden" name="academic_session_id" value="{{ $selectedSession->id }}">
                        <button type="submit" style="background:none; border:none; color:var(--sw-red); cursor:pointer; font-size:1.1rem; padding:4px; display:flex; align-items:center;" title="Remove Discount">
                            <i class="fas fa-trash-can"></i>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        @else
            <div class="sw-empty" style="padding:40px 10px;">
                <i class="fas fa-tag" style="font-size:2.5rem; margin-bottom:10px; color:#bfdbfe; display:block;"></i>
                <p style="font-size:.88rem; font-weight:600; color:#64748b;">No active discounts applied to this student.</p>
            </div>
        @endif
    </div>
</div>

{{-- ── FEES BREAK-UP TAB ── --}}
@elseif($activeTab == 'fees_breakup')
<div class="sw-fee-record-wrap">
    <div class="sw-fee-body" style="border-radius:12px;border-top:1.5px solid var(--sw-border);">
        <div style="font-size:1.05rem;font-weight:800;color:var(--sw-blue);padding:14px 18px 10px;border-bottom:1.5px solid var(--sw-border);">
            <i class="fas fa-table" style="margin-right:6px;"></i> Fee Component Breakdown
        </div>
        <div class="sw-table-wrap">
            <table class="sw-table">
            <thead>
                <tr>
                    <th>Fee Component</th>
                    <th>Category</th>
                    <th>Installment</th>
                    <th>Total</th>
                    <th>Discount</th>
                    <th>Paid</th>
                    <th>Due</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $groupedRows = [];
                    foreach ($studentFees as $sf) {
                        if ($sf->misc_fee_id) {
                            // Miscellaneous Fee: do not group, keep as is
                            $groupedRows[] = [
                                'is_misc' => true,
                                'component_name' => (optional($sf->component)->component_name ?: (optional($sf->category)->name ?: 'Fee')) . ' (Miscellaneous Fee)',
                                'category_name' => optional($sf->category)->name ?? '-',
                                'installment' => 'Installment ' . ($sf->installment_no ?? 1),
                                'amount' => $sf->amount,
                                'instant_discount_amount' => $sf->instant_discount_amount,
                                'paid_amount' => $sf->paid_amount,
                                'due_amount' => $sf->status === 'refunded' ? 0 : max(0, $sf->amount - $sf->instant_discount_amount - $sf->paid_amount),
                                'status' => $sf->status,
                                'raw_record' => $sf
                            ];
                        } else {
                            // Regular Fee Component: group by component name + category
                            $compName = optional($sf->component)->component_name ?: (optional($sf->category)->name ?: 'Fee');
                            $catName = optional($sf->category)->name ?? '-';
                            $key = $compName . '||' . $catName;

                            if (!isset($groupedRows[$key])) {
                                $groupedRows[$key] = [
                                    'is_misc' => false,
                                    'component_name' => $compName,
                                    'category_name' => $catName,
                                    'installment' => '—',
                                    'amount' => 0,
                                    'instant_discount_amount' => 0,
                                    'paid_amount' => 0,
                                    'due_amount' => 0,
                                    'status' => 'paid',
                                    'raw_record' => null
                                ];
                            }

                            $groupedRows[$key]['amount'] += $sf->amount;
                            $groupedRows[$key]['instant_discount_amount'] += $sf->instant_discount_amount;
                            $groupedRows[$key]['paid_amount'] += $sf->paid_amount;
                            
                            $effectiveDue = $sf->status === 'refunded' ? 0 : max(0, $sf->amount - $sf->instant_discount_amount - $sf->paid_amount);
                            $groupedRows[$key]['due_amount'] += $effectiveDue;
                        }
                    }

                    // Compute final statuses for grouped rows
                    foreach ($groupedRows as $key => &$row) {
                        if ($row['is_misc']) {
                            continue;
                        }
                        if ($row['due_amount'] <= 0) {
                            $row['status'] = 'paid';
                        } else {
                            $row['status'] = 'pending';
                        }
                    }
                    unset($row);
                @endphp

                @forelse($groupedRows as $row)
                <tr>
                    <td><strong>{{ $row['component_name'] }}</strong></td>
                    <td>{{ $row['category_name'] }}</td>
                    <td>{{ $row['installment'] }}</td>
                    <td class="sw-amount blue">
                        @if($row['instant_discount_amount'] > 0)
                            <span style="text-decoration: line-through; color: #94a3b8; font-size: 0.8rem; margin-right: 4px;">₹{{ number_format($row['amount'], 0) }}</span>
                            <span>₹{{ number_format($row['amount'] - $row['instant_discount_amount'], 0) }}</span>
                        @else
                            <span>₹{{ number_format($row['amount'], 0) }}</span>
                        @endif
                    </td>
                    <td class="sw-amount" style="color:#b45309;">
                        @if($row['instant_discount_amount'] > 0)
                            ₹{{ number_format($row['instant_discount_amount'], 0) }}
                        @else
                            <span style="color:#94a3b8;">—</span>
                        @endif
                    </td>
                    <td class="sw-amount green">₹{{ number_format($row['paid_amount'], 0) }}</td>
                    <td class="sw-amount red">₹{{ number_format($row['due_amount'], 0) }}</td>
                    <td>
                        @if($row['is_misc'])
                            @php
                                $sf = $row['raw_record'];
                            @endphp
                            @if($sf->status === 'paid')
                                <span class="sw-inst-badge paid"><i class="fas fa-check"></i> Paid</span>
                            @elseif($sf->status === 'partially_paid')
                                <span class="sw-inst-badge partial"><i class="fas fa-circle-half-stroke"></i> Partial</span>
                            @elseif($sf->status === 'refunded')
                                <span class="sw-inst-badge pending" style="background:#f3e8ff; color:#7e22ce; border-color:#e9d5ff;"><i class="fas fa-undo"></i> Refunded</span>
                            @else
                                <span class="sw-inst-badge pending"><i class="fas fa-clock"></i> Pending</span>
                            @endif
                        @else
                            @if($row['status'] === 'paid')
                                <span class="sw-inst-badge paid"><i class="fas fa-check"></i> Paid</span>
                            @else
                                <span class="sw-inst-badge pending"><i class="fas fa-clock"></i> Pending</span>
                            @endif
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" style="text-align:center;padding:30px;color:#94a3b8;">No fee records found.</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                @php
                    $bkTotalAmt = $studentFees->sum('amount');
                    $bkTotalDisc = $studentFees->sum('instant_discount_amount');
                    $bkTotalPaid = $studentFees->sum('paid_amount');
                    $bkTotalDue = $studentFees->reduce(function($c,$sf){ return $c + ($sf->status==='refunded' ? 0 : max(0,$sf->amount-$sf->instant_discount_amount-$sf->paid_amount)); }, 0);
                @endphp
                <tr style="background:#f0f9ff;font-weight:800;">
                    <td colspan="3" style="padding:10px 12px;">TOTAL</td>
                    <td class="sw-amount blue">₹{{ number_format($bkTotalAmt - $bkTotalDisc, 0) }}</td>
                    <td class="sw-amount" style="color:#b45309;">₹{{ number_format($bkTotalDisc, 0) }}</td>
                    <td class="sw-amount green">₹{{ number_format($bkTotalPaid, 0) }}</td>
                    <td class="sw-amount red">₹{{ number_format($bkTotalDue, 0) }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
        </div>
    </div>

    {{-- Refund Details Section --}}
    @if($refunds->isNotEmpty())
    <div class="sw-fee-body" style="border-radius:12px; border:1.5px solid #e9d5ff; margin-top:20px;">
        <div style="font-size:1.05rem;font-weight:800;color:#7e22ce;padding:14px 18px 10px;border-bottom:1.5px solid #e9d5ff;background:#faf5ff;border-radius:12px 12px 0 0;">
            <i class="fas fa-undo" style="margin-right:6px;"></i> Refund History
        </div>
        <div class="sw-table-wrap">
            <table class="sw-table">
            <thead>
                <tr style="background:#f3e8ff;">
                    <th style="color:#7e22ce;">Slip No</th>
                    <th style="color:#7e22ce;">Refund Date</th>
                    <th style="color:#7e22ce;">Amount</th>
                    <th style="color:#7e22ce;">Payment Mode</th>
                    <th style="color:#7e22ce;">Reason</th>
                    <th style="color:#7e22ce;">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($refunds->groupBy('slip_no') as $slipNo => $slipRefunds)
                @php
                    $first = $slipRefunds->first();
                    $totalAmt = $slipRefunds->sum('amount');
                @endphp
                <tr>
                    <td><span style="font-weight:700;color:#7e22ce;">{{ $slipNo }}</span></td>
                    <td>{{ \Carbon\Carbon::parse($first->refund_date)->format('d M Y') }}</td>
                    <td class="sw-amount" style="color:#7e22ce;font-weight:800;">₹{{ number_format($totalAmt, 0) }}</td>
                    <td><span class="sw-inst-badge pending" style="background:#f3e8ff;color:#7e22ce;border-color:#e9d5ff;">{{ ucwords(str_replace('_',' ',$first->payment_mode)) }}</span></td>
                    <td style="font-size:0.85rem;color:#64748b;">{{ Str::limit($first->reason, 60) }}</td>
                    <td>
                        <button class="sw-btn-print" onclick="window.open('{{ route('school.fees.print-slip', ['type' => 'refund', 'number' => $slipNo]) }}', '_blank', 'width=950,height=750')" style="background:none; border:none; color:var(--sw-blue); cursor:pointer; font-size:1rem;" title="Print Refund Slip">
                            <i class="fas fa-print"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background:#f3e8ff;font-weight:800;">
                    <td colspan="2" style="padding:10px 12px;color:#7e22ce;">TOTAL REFUNDED</td>
                    <td class="sw-amount" style="color:#7e22ce;">₹{{ number_format($refunds->sum('amount'), 0) }}</td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
        </table>
        </div>
    </div>
    @else
    <div style="text-align:center;padding:30px;color:#94a3b8;margin-top:20px;background:#fff;border-radius:12px;border:1.5px solid var(--sw-border);">
        <i class="fas fa-undo" style="font-size:2rem;margin-bottom:8px;display:block;opacity:0.4;"></i>
        <p style="font-weight:600;">No refunds processed for this student.</p>
    </div>
    @endif
</div>

{{-- ── WALLET TAB ── --}}
@elseif($activeTab == 'wallet')
<div class="sw-simple-panel">
    <i class="fas fa-wallet"></i>
    <p>Fee Wallet balance: ₹0. No advance payments recorded.</p>
</div>

{{-- ── FOLLOW-UP TAB ── --}}
@elseif($activeTab == 'followup')
<div class="sw-simple-panel">
    <i class="fas fa-history"></i>
    <p>No follow-up history recorded for this student.</p>
</div>

{{-- ── PAYMENT HISTORY TAB ── --}}
@elseif($activeTab == 'payment_history')
<div class="sw-fee-record-wrap">
    <div class="sw-fee-body" style="border-radius:12px; border:1.5px solid var(--sw-border); background:#fff; padding:20px;">
        <h3 style="font-size:1.1rem; font-weight:800; color:var(--sw-blue); margin-bottom:15px; border-bottom:1.5px solid var(--sw-border); padding-bottom:8px; margin-top:0; display:flex; justify-content:space-between; align-items:center;">
            <span><i class="fas fa-history" style="margin-right:6px;"></i> Payment History Ledger</span>
            <a href="{{ route('school.fees.invoice', ['student_id' => $viewStudent->id]) }}" class="sw-btn-primary" style="font-size: 0.8rem; padding: 6px 12px; text-decoration: none; color: #fff;">
                <i class="fas fa-file-invoice"></i> {{ $viewStudent->first_name }} View All Invoice
            </a>
        </h3>

        @php
            $sysTimezone = config('app.timezone', 'UTC');
            if (!app()->environment('testing') && \Illuminate\Support\Facades\Storage::disk('local')->exists('superadmin_settings.json')) {
                $fileContent = json_decode(\Illuminate\Support\Facades\Storage::disk('local')->get('superadmin_settings.json'), true);
                if (is_array($fileContent)) {
                    $sysTimezone = $fileContent['timezone'] ?? $sysTimezone;
                }
            }
        @endphp

        <div class="sw-table-wrap">
            <table class="sw-table">
            <thead>
                <tr style="background:#f0f9ff; font-weight:700; font-size:.8rem; text-transform:uppercase;">
                    <th style="color:var(--sw-blue); padding:13px 14px;">Invoice No</th>
                    <th style="color:var(--sw-blue); padding:13px 14px;">Payment Date</th>
                    <th style="color:var(--sw-blue); padding:13px 14px;">Transaction Time</th>
                    <th style="color:var(--sw-blue); padding:13px 14px;">Installment</th>
                    <th style="color:var(--sw-blue); padding:13px 14px;">Payment Mode</th>
                    <th style="color:var(--sw-blue); padding:13px 14px;">Payment Status</th>
                    <th style="color:var(--sw-blue); padding:13px 14px; text-align:right;">Discount</th>
                    <th style="color:var(--sw-blue); padding:13px 14px; text-align:right;">Amount Paid</th>
                </tr>
            </thead>
            <tbody>
                @php $ledgerTotalPaid = 0; @endphp
                @forelse($paymentHistory as $invoice)
                @php
                    // ── Step 1: Derive status from the invoice type first (most reliable) ──
                    // Real bounced/cancelled/returned/rejected FeeInvoice records have type = 'X_cheque'.
                    // This is the authoritative source — never override it with a PendingCheque lookup.
                    $invoiceTypeStatus = null;
                    $knownBouncedTypes = ['bounced_cheque', 'cancelled_cheque', 'returned_cheque', 'rejected_cheque'];
                    if (in_array($invoice->type, $knownBouncedTypes)) {
                        $invoiceTypeStatus = str_replace('_cheque', '', $invoice->type);
                    }

                    // ── Step 2: Resolve PendingCheque for pending/cleared cheques only ──
                    // For bounced_cheque types, the invoice type is the truth; skip PendingCheque lookup
                    // to avoid finding a cleared cheque with the same cheque_number.
                    $cheque = null;
                    if ($invoiceTypeStatus === null) {
                        // Only look up PendingCheque when we don't already know the cheque status from type
                        if (!empty($invoice->cheque_id_raw)) {
                            $cheque = \App\Models\PendingCheque::find($invoice->cheque_id_raw);
                        } elseif (strpos(strval($invoice->id), 'cheque_') === 0) {
                            // Pseudo-invoice: id = 'cheque_X'
                            $chequeId = str_replace('cheque_', '', $invoice->id);
                            $cheque = \App\Models\PendingCheque::find($chequeId);
                        } else {
                            // Real cleared/payment cheque invoice — find by cheque_id or cheque_number in payment_details
                            $pdRaw = is_string($invoice->payment_details) ? json_decode($invoice->payment_details, true) : null;
                            if (is_array($pdRaw) && !empty($pdRaw['cheque_id'])) {
                                $cheque = \App\Models\PendingCheque::find($pdRaw['cheque_id']);
                            }
                            if (!$cheque) {
                                $chequeNumber = is_array($pdRaw) ? ($pdRaw['cheque_number'] ?? null) : null;
                                if (!$chequeNumber && !empty($invoice->remarks)) {
                                    preg_match('/(?:No:|Cheque No:)\s*([0-9A-Za-z]+)/i', $invoice->remarks, $matches);
                                    $chequeNumber = $matches[1] ?? null;
                                }
                                if ($chequeNumber) {
                                    $cheque = \App\Models\PendingCheque::where('school_id', $invoice->school_id)
                                        ->where('student_id', $invoice->student_id)
                                        ->where('cheque_number', $chequeNumber)
                                        ->first();
                                }
                            }
                        }
                    } elseif (!empty($invoice->payment_details)) {
                        // For bounced_cheque types: look up PendingCheque by cheque_id ONLY — for action buttons
                        // (do NOT use the cheque status — we trust $invoiceTypeStatus)
                        $pdRaw = is_string($invoice->payment_details) ? json_decode($invoice->payment_details, true) : null;
                        if (is_array($pdRaw) && !empty($pdRaw['cheque_id'])) {
                            $cheque = \App\Models\PendingCheque::find($pdRaw['cheque_id']);
                        }
                    }

                    // ── Step 3: Compute effective cheque status ──
                    // $invoiceTypeStatus (from invoice->type) takes ABSOLUTE precedence over PendingCheque.status
                    $chequeStatus = ($cheque && $cheque->status) ? strtolower($cheque->status) : null;
                    // For bounced_cheque invoice types, force the status to be what the invoice says
                    $effectiveChequeStatus = $invoiceTypeStatus ?? $chequeStatus;
                    $chequeIdForAction = $cheque ? $cheque->id : null;

                    // ── Step 4: Parse payment_details and components ──
                    $details = is_string($invoice->payment_details) ? json_decode($invoice->payment_details, true) : null;
                    $components = [];
                    if (is_array($details)) {
                        $components = isset($details['components']) && is_array($details['components'])
                            ? $details['components']
                            : (isset($details[0]) ? $details : []);
                    }

                    // Group components by installment number to show one row per installment
                    $groupedComponents = [];
                    if (is_array($components) && count($components) > 0) {
                        foreach ($components as $comp) {
                            $instNo = is_array($comp) ? ($comp['installment_no'] ?? 1) : 1;
                            if (!isset($groupedComponents[$instNo])) {
                                $groupedComponents[$instNo] = [
                                    'installment_no' => $instNo,
                                    'amount_paid' => 0.0,
                                    'discount_amount' => 0.0,
                                ];
                            }
                            $amt = floatval(is_array($comp) ? ($comp['amount_paid'] ?? 0) : 0);
                            $disc = floatval(is_array($comp) ? ($comp['discount_amount'] ?? ($comp['transaction_discount'] ?? 0)) : 0);
                            $groupedComponents[$instNo]['amount_paid'] += $amt;
                            $groupedComponents[$instNo]['discount_amount'] += $disc;
                        }
                        ksort($groupedComponents);
                        $components = array_values($groupedComponents);
                    }

                    // ── Step 5: Derive display flags ──
                    $isRefundItem = $invoice->type === 'refund';
                    $isBounced    = in_array($effectiveChequeStatus, ['bounced', 'cancelled', 'returned', 'rejected'])
                                    || $invoice->type === 'bounced';
                    $isPendingCheque = $invoice->type === 'pending' || $effectiveChequeStatus === 'pending';
                    $isCancelled = ($invoice->status === 'cancelled') && !$isBounced;
                @endphp
                @if(is_array($components) && count($components) > 0)
                    @foreach($components as $compIndex => $comp)
                    @php
                        $amtPaid = floatval(is_array($comp) ? ($comp['amount_paid'] ?? 0) : 0);
                        if ($invoice->type === 'payment' && !$isCancelled) {
                            $ledgerTotalPaid += $amtPaid;
                        } elseif ($invoice->type === 'refund') {
                            $ledgerTotalPaid -= $amtPaid;
                        }
                    @endphp
                    <tr style="border-bottom:1px solid #e2e8f0; {{ $isCancelled ? 'background:#f8fafc; color:#64748b; opacity:0.85;' : ($isRefundItem ? 'background:#faf5ff;' : ($isBounced ? 'background:#fff5f5; color:#c53030;' : ($isPendingCheque ? 'background:#fefcbf; color:#b7791f;' : ''))) }}">
                        <td>
                            @if($compIndex === 0)
                                @if(strpos(strval($invoice->id), 'cheque_') === 0)
                                    #{{ $invoice->invoice_number }}
                                @else
                                    <a href="javascript:void(0)" onclick="window.open('{{ route('school.fees.print-slip', ['type' => 'payment', 'number' => $invoice->invoice_number]) }}?student_id={{ $viewStudent->id }}', '_blank', 'width=950,height=750')" style="color: #2563eb; font-weight: 700; text-decoration: underline;" title="Print Invoice">
                                        #{{ $invoice->invoice_number }}
                                    </a>
                                @endif
                            @endif
                        </td>
                        <td>
                            @if($compIndex === 0)
                                {{ $invoice->payment_date ? \Carbon\Carbon::parse($invoice->payment_date)->format('d M Y') : '-' }}
                            @endif
                        </td>
                        <td>
                            @if($compIndex === 0)
                                {{ $invoice->created_at ? \Carbon\Carbon::parse($invoice->created_at)->timezone($sysTimezone)->format('h:i A') : '-' }}
                            @endif
                        </td>
                        <td>Installment {{ is_array($comp) ? ($comp['installment_no'] ?? 1) : 1 }}</td>
                        <td>
                            @if($compIndex === 0)
                                <span style="text-transform: uppercase; font-size:0.7rem; font-weight:700; background:{{ $isBounced ? '#feb2b2' : ($isPendingCheque ? '#fef08a' : '#e2e8f0') }}; padding:3px 8px; border-radius:4px; color:{{ $isBounced ? '#9b2c2c' : ($isPendingCheque ? '#718096' : '#0f172a') }};">
                                    {{ str_replace('_', ' ', $invoice->payment_mode ?? '') }}
                                </span>
                            @endif
                        </td>
                        {{-- Payment Status Cell --}}
                        <td>
                            @if($compIndex === 0)
                                @if(($invoice->payment_mode ?? '') === 'cheque')
                                    @if($effectiveChequeStatus === 'pending')
                                        <span style="font-size:0.68rem; font-weight:700; background:#fef08a; color:#92400e; padding:3px 8px; border-radius:4px; border:1px solid #fcd34d;">⏳ PENDING</span>
                                    @elseif($effectiveChequeStatus === 'cleared')
                                        <span style="font-size:0.68rem; font-weight:700; background:#dcfce7; color:#166534; padding:3px 8px; border-radius:4px; border:1px solid #86efac;">✓ CLEARED</span>
                                    @elseif($effectiveChequeStatus === 'bounced')
                                        <span style="font-size:0.68rem; font-weight:700; background:#fee2e2; color:#991b1b; padding:3px 8px; border-radius:4px; border:1px solid #fca5a5;">✗ BOUNCED</span>
                                    @elseif($effectiveChequeStatus === 'cancelled')
                                        <span style="font-size:0.68rem; font-weight:700; background:#e2e8f0; color:#475569; padding:3px 8px; border-radius:4px; border:1px solid #cbd5e1;">✗ CANCELLED</span>
                                    @elseif($effectiveChequeStatus === 'returned')
                                        <span style="font-size:0.68rem; font-weight:700; background:#ffedd5; color:#ea580c; padding:3px 8px; border-radius:4px; border:1px solid #fed7aa;">✗ RETURNED</span>
                                    @elseif($effectiveChequeStatus === 'rejected')
                                        <span style="font-size:0.68rem; font-weight:700; background:#f3e8ff; color:#7e22ce; padding:3px 8px; border-radius:4px; border:1px solid #e9d5ff;">✗ REJECTED</span>
                                    @elseif($isRefundItem)
                                        {{-- no cheque status badge for refund rows --}}
                                    @else
                                        {{-- Default: cheque payment not yet matched = show CLEARED (it was accepted) --}}
                                        <span style="font-size:0.68rem; font-weight:700; background:#dcfce7; color:#166534; padding:3px 8px; border-radius:4px; border:1px solid #86efac;">✓ CLEARED</span>
                                    @endif
                                @else
                                    {{-- Non-cheque payment modes --}}
                                    @if($isCancelled)
                                        <span style="font-size:0.68rem; font-weight:700; background:#fee2e2; color:#991b1b; padding:3px 8px; border-radius:4px; border:1px solid #fca5a5;">✗ CANCELLED</span>
                                    @elseif($invoice->status === 'paid')
                                        <span style="font-size:0.68rem; font-weight:700; background:#dcfce7; color:#166534; padding:3px 8px; border-radius:4px; border:1px solid #86efac;">✓ PAID</span>
                                    @else
                                        <span style="font-size:0.68rem; font-weight:700; background:#e2e8f0; color:#475569; padding:3px 8px; border-radius:4px; border:1px solid #cbd5e1;">{{ strtoupper($invoice->status ?? '') }}</span>
                                    @endif
                                @endif
                            @endif
                        </td>

                        <td style="text-align:right; font-weight:700; color:#b45309;">
                            ₹{{ number_format(is_array($comp) ? ($comp['discount_amount'] ?? ($comp['transaction_discount'] ?? 0)) : 0, 2) }}
                        </td>
                        <td style="text-align:right; font-weight:800; color:{{ $isCancelled ? '#94a3b8' : ($isRefundItem ? '#7e22ce' : ($isBounced ? '#c53030' : ($isPendingCheque ? '#b7791f' : 'var(--sw-green)'))) }};">
                            {{ $isRefundItem || $isBounced ? '-' : '' }}₹{{ number_format($amtPaid, 2) }}
                        </td>
                    </tr>
                    @endforeach
                @else
                    @php
                        if ($invoice->type === 'payment' && !$isCancelled) {
                            $ledgerTotalPaid += $invoice->amount;
                        } elseif ($invoice->type === 'refund') {
                            $ledgerTotalPaid -= $invoice->amount;
                        }
                    @endphp
                    <tr style="border-bottom:1px solid #e2e8f0; {{ $isCancelled ? 'background:#f8fafc; color:#64748b; opacity:0.85;' : ($isRefundItem ? 'background:#faf5ff;' : ($isBounced ? 'background:#fff5f5; color:#c53030;' : ($isPendingCheque ? 'background:#fefcbf; color:#b7791f;' : ''))) }}">
                        <td>
                            @if(strpos(strval($invoice->id), 'cheque_') === 0)
                                #{{ $invoice->invoice_number }}
                            @else
                                <a href="javascript:void(0)" onclick="window.open('{{ route('school.fees.print-slip', ['type' => 'payment', 'number' => $invoice->invoice_number]) }}?student_id={{ $viewStudent->id }}', '_blank', 'width=950,height=750')" style="color: #2563eb; font-weight: 700; text-decoration: underline;" title="Print Invoice">
                                    #{{ $invoice->invoice_number }}
                                </a>
                            @endif
                        </td>
                        <td>{{ $invoice->payment_date ? \Carbon\Carbon::parse($invoice->payment_date)->format('d M Y') : '-' }}</td>
                        <td>{{ $invoice->created_at ? \Carbon\Carbon::parse($invoice->created_at)->timezone($sysTimezone)->format('h:i A') : '-' }}</td>
                        <td>Installment {{ $invoice->installment_no ?? 1 }}</td>
                        <td>
                            <span style="text-transform: uppercase; font-size:0.7rem; font-weight:700; background:{{ $isBounced ? '#feb2b2' : ($isPendingCheque ? '#fef08a' : '#e2e8f0') }}; padding:3px 8px; border-radius:4px; color:{{ $isBounced ? '#9b2c2c' : ($isPendingCheque ? '#718096' : '#0f172a') }};">
                                {{ str_replace('_', ' ', $invoice->payment_mode ?? '') }}
                            </span>
                        </td>
                        {{-- Payment Status Cell (single-row fallback) --}}
                        <td>
                            @if(($invoice->payment_mode ?? '') === 'cheque')
                                @if($effectiveChequeStatus === 'pending')
                                    <span style="font-size:0.68rem; font-weight:700; background:#fef08a; color:#92400e; padding:3px 8px; border-radius:4px; border:1px solid #fcd34d;">⏳ PENDING</span>
                                @elseif($effectiveChequeStatus === 'cleared')
                                    <span style="font-size:0.68rem; font-weight:700; background:#dcfce7; color:#166534; padding:3px 8px; border-radius:4px; border:1px solid #86efac;">✓ CLEARED</span>
                                @elseif($effectiveChequeStatus === 'bounced')
                                    <span style="font-size:0.68rem; font-weight:700; background:#fee2e2; color:#991b1b; padding:3px 8px; border-radius:4px; border:1px solid #fca5a5;">✗ BOUNCED</span>
                                @elseif($effectiveChequeStatus === 'cancelled')
                                    <span style="font-size:0.68rem; font-weight:700; background:#e2e8f0; color:#475569; padding:3px 8px; border-radius:4px; border:1px solid #cbd5e1;">✗ CANCELLED</span>
                                @elseif($effectiveChequeStatus === 'returned')
                                    <span style="font-size:0.68rem; font-weight:700; background:#ffedd5; color:#ea580c; padding:3px 8px; border-radius:4px; border:1px solid #fed7aa;">✗ RETURNED</span>
                                @elseif($effectiveChequeStatus === 'rejected')
                                    <span style="font-size:0.68rem; font-weight:700; background:#f3e8ff; color:#7e22ce; padding:3px 8px; border-radius:4px; border:1px solid #e9d5ff;">✗ REJECTED</span>
                                @elseif(!$isRefundItem)
                                    <span style="font-size:0.68rem; font-weight:700; background:#dcfce7; color:#166534; padding:3px 8px; border-radius:4px; border:1px solid #86efac;">✓ CLEARED</span>
                                @endif
                            @else
                                {{-- Non-cheque payment modes --}}
                                @if($isCancelled)
                                    <span style="font-size:0.68rem; font-weight:700; background:#fee2e2; color:#991b1b; padding:3px 8px; border-radius:4px; border:1px solid #fca5a5;">✗ CANCELLED</span>
                                @elseif($invoice->status === 'paid')
                                    <span style="font-size:0.68rem; font-weight:700; background:#dcfce7; color:#166534; padding:3px 8px; border-radius:4px; border:1px solid #86efac;">✓ PAID</span>
                                @else
                                    <span style="font-size:0.68rem; font-weight:700; background:#e2e8f0; color:#475569; padding:3px 8px; border-radius:4px; border:1px solid #cbd5e1;">{{ strtoupper($invoice->status ?? '') }}</span>
                                @endif
                            @endif
                        </td>

                        <td style="text-align:right; font-weight:700; color:#b45309;">₹{{ number_format($invoice->discount_amount ?: 0, 2) }}</td>
                        <td style="text-align:right; font-weight:800; color:{{ $isCancelled ? '#94a3b8' : ($isRefundItem ? '#7e22ce' : ($isBounced ? '#c53030' : ($isPendingCheque ? '#b7791f' : 'var(--sw-green)'))) }};">
                            {{ $isRefundItem || $isBounced ? '-' : '' }}₹{{ number_format($invoice->amount ?: 0, 2) }}
                        </td>
                    </tr>
                @endif
                @empty
                <tr>
                    <td colspan="8" style="text-align:center; padding:30px; color:#94a3b8;">No payment history records found.</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr style="background:#f0f9ff; font-weight:800; font-size:0.95rem;">
                    <td colspan="7" style="padding:12px 14px; color: var(--sw-blue);">TOTAL PAID TO DATE</td>
                    <td style="text-align:right; color:var(--sw-green); font-size:1.05rem;">₹{{ number_format($ledgerTotalPaid, 2) }}</td>
                </tr>
            </tfoot>
        </table>
        </div>
    </div>
</div>
@endif

{{-- ══════════════════════════════════════════════════════════════════════
     LIST VIEW
══════════════════════════════════════════════════════════════════════ --}}
@else

{{-- Filter bar --}}
<form method="GET" action="{{ route('school.fees.student-wise') }}" id="filterForm">
<div class="sw-filters">
    {{-- Academic Year --}}
    <div class="sw-filter-group">
        <label><i class="fas fa-calendar"></i> Academic Year</label>
        <select name="academic_session_id" onchange="document.getElementById('filterForm').submit()">
            @foreach($academicSessions as $sess)
                <option value="{{ $sess->id }}" {{ $sess->id == $selectedSession->id ? 'selected' : '' }}>
                    {{ $sess->name }}{{ $sess->is_current ? ' ★' : '' }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Class --}}
    <div class="sw-filter-group">
        <label><i class="fas fa-chalkboard"></i> Select Class</label>
        <select name="class_id" onchange="document.getElementById('filterForm').submit()">
            <option value="">All Classes</option>
            @foreach($classes as $cls)
                <option value="{{ $cls->id }}" {{ $cls->id == ($selectedClass?->id) ? 'selected' : '' }}>
                    {{ $cls->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Section --}}
    <div class="sw-filter-group">
        <label><i class="fas fa-layer-group"></i> Select Section</label>
        <select name="section_id" onchange="document.getElementById('filterForm').submit()">
            <option value="">All Sections</option>
            @foreach($sections as $sec)
                <option value="{{ $sec->id }}" {{ $sec->id == $selectedSectionId ? 'selected' : '' }}>
                    {{ $sec->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Search --}}
    <div class="sw-filter-group" style="min-width:230px;">
        <label><i class="fas fa-search"></i> Search by student name/admission ID</label>
        <input type="text" name="search" value="{{ $search ?? '' }}"
               placeholder="Enter min 3 char to search"
               oninput="if(this.value.length>=3||this.value.length===0) setTimeout(()=>document.getElementById('filterForm').submit(),400)">
    </div>

    {{-- Show all year dues toggle --}}
    <div class="sw-toggle-group">
        Show all year dues
        <label class="sw-toggle">
            <input type="checkbox" id="allYearToggle" {{ request('all_year') ? 'checked' : '' }}
                   onchange="toggleAllYear(this)">
            <span class="sw-toggle-slider"></span>
        </label>
    </div>
</div>
</form>

{{-- Table toolbar --}}
<div class="sw-table-toolbar">
    <label class="sw-select-all-label">
        <input type="checkbox" id="selectAll" onchange="toggleAll(this)">
        Select all on this page
    </label>
    <div class="sw-vis-btns">
        <button class="sw-btn-vis"><i class="fas fa-eye-slash"></i> Hide Fee Visibility</button>
        <button class="sw-btn-vis"><i class="fas fa-eye"></i> Show Fee Visibility</button>
    </div>
</div>

{{-- Main table --}}
<div class="sw-table-wrap">
    <table class="sw-table">
        <thead>
            <tr>
                <th style="width:48px;"><input type="checkbox" id="thCheck" onchange="toggleAll(this)" style="accent-color:#fff;width:16px;height:16px;"></th>
                <th>#</th>
                <th>Student Name</th>
                <th>Admission Id</th>
                <th>Father Name</th>
                <th>Class</th>
                <th style="white-space:nowrap;">Fee Schedule Name</th>
                <th style="white-space:nowrap;">Receivable{{ ($config?->add_fee_discount ?? true) ? ' after discount' : '' }}</th>
                <th style="white-space:nowrap;">Paid Till Date</th>
                @if($config?->add_fee_due ?? true)
                <th style="white-space:nowrap;">Fee Due</th>
                @endif
                <th style="white-space:nowrap;font-size:.72rem;">Total Due<br>(All Yrs)</th>
                <th style="white-space:nowrap;">Fee Visibility</th>
                <th style="white-space:nowrap;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($studentsWithFees as $idx => $student)
            @php
                $fees        = $student->studentFees ?? collect();
                
                // Session fees filter (includes Tuition, Transport, and Misc fees for the selected session)
                $sessionFees = $fees->filter(function($fee) use ($selectedSession) {
                    if ($fee->fee_schedule_id) {
                        $sch = $fee->feeSchedule ?? \App\Models\FeeSchedule::find($fee->fee_schedule_id);
                        return $sch && $sch->academic_session_id == $selectedSession->id;
                    }
                    if ($fee->transport_fee_schedule_id) {
                        $sch = $fee->transportFeeSchedule ?? \App\Models\TransportFeeSchedule::find($fee->transport_fee_schedule_id);
                        return $sch && $sch->academic_session_id == $selectedSession->id;
                    }
                    if ($fee->misc_fee_id) {
                        $sch = $fee->miscFee ?? \App\Models\MiscFee::find($fee->misc_fee_id);
                        return $sch && $sch->academic_session_id == $selectedSession->id;
                    }
                    if ($fee->due_date && $selectedSession->start_date && $selectedSession->end_date) {
                        return $fee->due_date >= $selectedSession->start_date && $fee->due_date <= $selectedSession->end_date;
                    }
                    return false;
                });
                $receivable  = $sessionFees->sum('amount') - $sessionFees->sum('instant_discount_amount');
                $paid        = $sessionFees->sum('paid_amount');
                $due         = $sessionFees->reduce(function($carry, $f) {
                    if ($f->status === 'refunded') return $carry;
                    return $carry + max(0, $f->amount - $f->instant_discount_amount - $f->paid_amount);
                }, 0);

                // Total dues across all academic years
                $totalReceivable = $fees->sum('amount') - $fees->sum('instant_discount_amount');
                $totalPaid       = $fees->sum('paid_amount');
                $totalDue        = $fees->reduce(function($carry, $f) {
                    if ($f->status === 'refunded') return $carry;
                    return $carry + max(0, $f->amount - $f->instant_discount_amount - $f->paid_amount);
                }, 0);

                $initials    = strtoupper(substr($student->first_name, 0, 1));

                // Fee schedule: Show only if explicitly assigned
                $schedName = optional($student->feeSchedule)->name;


                $rowNum = ($studentsWithFees->currentPage() - 1) * $studentsWithFees->perPage() + $idx + 1;
            @endphp
            <tr>
                <td><input type="checkbox" class="row-check" data-id="{{ $student->id }}" style="accent-color:var(--sw-blue2);width:16px;height:16px;"></td>
                <td style="color:#94a3b8;font-weight:700;font-size:.85rem;">{{ str_pad($rowNum, 2, '0', STR_PAD_LEFT) }}</td>
                <td>
                    <div class="sw-student-cell">
                        @if($student->photo)
                            <img src="{{ $student->photo_url }}" alt="{{ $student->full_name }}" style="width:38px; height:38px; border-radius:50%; object-fit:cover; flex-shrink:0;">
                        @else
                            <div class="sw-avatar">{{ $initials }}</div>
                        @endif
                        <div>
                            <div class="sw-student-name">{{ $student->full_name }}</div>
                        </div>
                    </div>
                </td>
                <td style="font-weight:600;color:#374151;">{{ $student->admission_number ?? '-' }}</td>
                <td style="font-weight:600;color:#374151;">{{ $student->father_name ?? '-' }}</td>
                <td>
                    <strong style="color:var(--sw-blue);">{{ optional($student->class)->name ?? '-' }}</strong>
                    {{ optional($student->section)->name ? ' ' . $student->section->name : '' }}
                </td>
                <td style="font-size:.88rem;color:#374151;">{{ $schedName ?: '-' }}</td>
                <td>
                    @php $val1 = '₹' . number_format(($config?->add_fee_discount ?? true) ? $receivable : ($sessionFees->sum('amount')), 0); @endphp
                    <span class="sw-amount blue" data-original-val="{{ $val1 }}">{{ $val1 }}</span>
                </td>
                <td>
                    @php $val2 = '₹' . number_format($paid, 0); @endphp
                    <span class="sw-amount green" data-original-val="{{ $val2 }}">{{ $val2 }}</span>
                </td>
                @if($config?->add_fee_due ?? true)
                <td>
                    @php $val3 = '₹' . number_format($due, 0); @endphp
                    <span class="sw-amount {{ $due > 0 ? 'red' : 'green' }}" data-original-val="{{ $val3 }}">{{ $val3 }}</span>
                </td>
                @endif
                <td>
                    @php $val4 = '₹' . number_format($totalDue, 0); @endphp
                    <span class="sw-amount {{ $totalDue > 0 ? 'red' : 'green' }}" data-original-val="{{ $val4 }}">{{ $val4 }}</span>
                </td>
                <td>
                    <div class="sw-vis-toggle">
                        <label class="sw-toggle">
                            <input type="checkbox" {{ $student->fee_visible !== false ? 'checked' : '' }}
                                   onchange="toggleVisibility(this, {{ $student->id }})">
                            <span class="sw-toggle-slider"></span>
                        </label>
                        <span class="sw-vis-status-text {{ $student->fee_visible !== false ? 'sw-vis-label-on' : 'sw-vis-label-off' }}">
                            {{ $student->fee_visible !== false ? 'Visible' : 'Hidden' }}
                        </span>
                    </div>
                </td>
                <td>
                    <a href="{{ route('school.fees.student-wise', ['view_student' => $student->id, 'academic_session_id' => $selectedSession->id, 'class_id' => $student->class_id, 'section_id' => $student->section_id]) }}"
                       class="sw-view-btn" title="View Fee Details">
                        <i class="fas fa-eye"></i>
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="13">
                    <div class="sw-empty">
                        <i class="fas fa-users-slash"></i>
                        <p>No students found for the selected filters.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Footer --}}
<div class="sw-footer">
    <div class="sw-footer-toggles">
        <label class="sw-footer-toggle">
            <label class="sw-toggle" style="width:40px;height:22px;">
                <input type="checkbox" id="showDeactivatedToggle" {{ request('show_deactivated') == '1' ? 'checked' : '' }} onchange="toggleDeactivated(this)">
                <span class="sw-toggle-slider"></span>
            </label>
            Show Deactivated Students
        </label>
    </div>
    <div class="sw-rows-badge">Total Rows: {{ $studentsWithFees->total() }}</div>
    {{-- Pagination --}}
    @if($studentsWithFees->lastPage() > 1)
    <div class="sw-pagination">
        @if($studentsWithFees->onFirstPage())
            <span class="sw-page-btn" style="opacity:.4;cursor:not-allowed;"><i class="fas fa-chevron-left" style="font-size:.7rem;"></i></span>
        @else
            <a href="{{ $studentsWithFees->previousPageUrl() }}" class="sw-page-btn"><i class="fas fa-chevron-left" style="font-size:.7rem;"></i></a>
        @endif

        @php
            $currentPage = $studentsWithFees->currentPage();
            $lastPage = $studentsWithFees->lastPage();
            $maxPagesToShow = 10;
            if ($lastPage <= $maxPagesToShow) {
                $startPage = 1;
                $endPage = $lastPage;
            } else {
                $startPage = max(1, $currentPage - 4);
                $endPage = min($lastPage, $startPage + $maxPagesToShow - 1);
                if ($endPage - $startPage < $maxPagesToShow - 1) {
                    $startPage = max(1, $endPage - $maxPagesToShow + 1);
                }
            }
        @endphp

        @if($startPage > 1)
            <a href="{{ $studentsWithFees->url(1) }}" class="sw-page-btn">1</a>
            @if($startPage > 2)
                <span class="sw-page-btn" style="opacity:.6;cursor:default;">...</span>
            @endif
        @endif

        @for($p = $startPage; $p <= $endPage; $p++)
            <a href="{{ $studentsWithFees->url($p) }}" class="sw-page-btn {{ $p == $currentPage ? 'active' : '' }}">{{ $p }}</a>
        @endfor

        @if($endPage < $lastPage)
            @if($endPage < $lastPage - 1)
                <span class="sw-page-btn" style="opacity:.6;cursor:default;">...</span>
            @endif
            <a href="{{ $studentsWithFees->url($lastPage) }}" class="sw-page-btn">{{ $lastPage }}</a>
        @endif

        @if($studentsWithFees->hasMorePages())
            <a href="{{ $studentsWithFees->nextPageUrl() }}" class="sw-page-btn"><i class="fas fa-chevron-right" style="font-size:.7rem;"></i></a>
        @else
            <span class="sw-page-btn" style="opacity:.4;cursor:not-allowed;"><i class="fas fa-chevron-right" style="font-size:.7rem;"></i></span>
        @endif
    </div>
    @endif
</div>
@endif

{{-- ══════════════════════════════════════════════════════════════════════
     ADD MISCELLANEOUS FEE MODAL
══════════════════════════════════════════════════════════════════════ --}}
<div class="sw-modal-overlay" id="addMiscFeeModal" style="display:none;" onclick="if(event.target===this) closeMiscFeeModal()">
    <div class="sw-modal" style="width:480px; max-height:90vh; overflow-y:auto;">
        <h3><i class="fas fa-hand-holding-dollar" style="color:var(--sw-blue2);"></i> Add Miscellaneous Fee</h3>
        <form method="POST" action="{{ route('school.fees.student-wise') }}" id="miscFeeForm">
            @csrf
            <input type="hidden" name="action" value="add_student_misc_fee">
            <input type="hidden" name="student_id" id="miscModalStudentId">
            <input type="hidden" name="installment_no" id="miscModalInstallmentNo">
            
            <div style="border: 1.5px dashed var(--sw-border); background:#f8fafc; border-radius:9px; padding:14px; margin-bottom:14px;">
                <div style="font-size:.8rem;font-weight:800;color:#334155;margin-bottom:10px;display:flex;align-items:center;gap:6px;">
                    <i class="fas fa-hand-holding-dollar"></i> MISCELLANEOUS FEE (Optional)
                </div>
                <div class="sw-modal-field" style="display:flex; align-items:center; gap:8px; margin-bottom:10px;">
                    <input type="checkbox" name="add_misc_fee" id="miscModalAddMiscFee" value="1" checked onclick="return false;" style="width:16px; height:16px; cursor:pointer;">
                    <label for="miscModalAddMiscFee" style="font-weight:700; cursor:pointer; margin-bottom:0;">Add Miscellaneous Fee to this installment</label>
                </div>
                
                <div id="miscFeeFieldsStandalone">
                    <div style="display:flex; gap:10px; margin-bottom:10px;">
                        <div class="sw-modal-field" style="flex:1;">
                            <label>Select Miscellaneous Fee</label>
                            <select name="selected_misc_fee_id" id="miscModalSelectedMiscFeeId" onchange="onSelectMiscFeeForStandalone(this.value)">
                                <option value="">-- Create New / Select --</option>
                                @foreach($availableMiscFees ?? [] as $mf)
                                    <option value="{{ $mf->id }}" data-amount="{{ $mf->amount }}">
                                        {{ $mf->fee_head_name }} - {{ $mf->name }} (₹{{ number_format($mf->amount, 2) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="sw-modal-field" style="flex:1;">
                            <label>Fee Amount (₹)</label>
                            <input type="number" name="misc_fee_amount" id="miscModalMiscFeeAmount" step="0.01" min="0" value="0.00" required>
                        </div>
                    </div>
                    
                    <div id="newMiscFeeFieldsStandalone" style="border-top:1px solid #e2e8f0; padding-top:10px; display:block;">
                        <div style="font-weight:800; font-size:0.75rem; color:#475569; margin-bottom:6px;"><i class="fas fa-plus-circle"></i> Create New Miscellaneous Fee Details:</div>
                        <div style="display:flex; gap:10px;">
                            <div class="sw-modal-field" style="flex:1; margin-bottom:0;">
                                <label>Fee Head Name</label>
                                <input type="text" name="new_misc_fee_head" id="miscModalNewMiscFeeHead" placeholder="e.g. Exam Fee" required>
                            </div>
                            <div class="sw-modal-field" style="flex:1; margin-bottom:0;">
                                <label>Fee Name</label>
                                <input type="text" name="new_misc_fee_name" id="miscModalNewMiscFeeName" placeholder="e.g. Term 1 Exam" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="sw-modal-actions">
                <button type="button" class="sw-modal-cancel" onclick="closeMiscFeeModal()">Cancel</button>
                <button type="submit" class="sw-modal-submit" id="saveMiscFeeBtn"><i class="fas fa-check"></i> Save</button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════
     MARK PAID MODAL
══════════════════════════════════════════════════════════════════════ --}}
<div class="sw-modal-overlay" id="markPaidModal" style="display:none;" onclick="if(event.target===this) closeMarkPaid()">
    <div class="sw-modal" style="width:480px; max-height:90vh; overflow-y:auto;">
        <h3><i class="fas fa-indian-rupee-sign" style="color:var(--sw-blue2);"></i> Record Payment</h3>
        <form method="POST" action="{{ route('school.fees.student-wise') }}" id="paymentForm">
            @csrf
            <input type="hidden" name="student_id" id="modalStudentId">
            <input type="hidden" name="installment_no" id="modalInstallmentNo">
            <input type="hidden" name="student_fee_id" id="modalStudentFeeId">
            <input type="hidden" name="student_fee_ids" id="modalStudentFeeIds">
            <input type="hidden" name="fee_type" id="modalFeeType">
            
            <div class="sw-modal-field">
                <label>Installment</label>
                <input type="text" id="modalInstLabel" readonly style="background:#f8faff;">
            </div>
            
            <div style="display:flex; gap:10px;">
                <div class="sw-modal-field" style="flex:1;">
                    <label>Amount Due (₹)</label>
                    <input type="text" id="modalDueAmt" readonly style="background:#f8faff;">
                </div>
                <div class="sw-modal-field" style="flex:1;">
                    <label>Amount to Collect (₹)</label>
                    <input type="number" name="amount_paid" id="modalAmtPaid" step="0.01" min="0.01" required placeholder="Enter amount"
                           oninput="isManualAmtPaid = true; validateAmountInput(); recalcNetAmount()">
                    {{-- Issue 1 Fix: Real-time amount error message --}}
                    <div id="modalAmtError" style="display:none; color:#dc2626; font-size:0.78rem; font-weight:700; margin-top:4px;"></div>
                </div>
            </div>

            <div style="display:flex; gap:10px;">
                <div class="sw-modal-field" style="flex:1;">
                    <label>Entry Date</label>
                    <input type="date" name="entry_date" id="modalEntryDate" value="{{ now()->format('Y-m-d') }}"
                           {{ !($config?->entry_date_editable ?? true) ? 'readonly style=background:#f8faff;cursor:not-allowed;' : '' }}>
                </div>
                <div class="sw-modal-field" style="flex:1;">
                    <label>Receipt Date</label>
                    <input type="date" name="receipt_date" id="modalReceiptDate" required value="{{ now()->format('Y-m-d') }}"
                           {{ !($config?->receipt_date_editable ?? true) ? 'readonly style=background:#f8faff;cursor:not-allowed;' : '' }}>
                </div>
            </div>

            <div class="sw-modal-field">
                <label>Receipt No</label>
                <input type="text" name="receipt_no" id="modalReceiptNo" required placeholder="REC-XXXXXX"
                       {{ !($config?->allow_manual_receipt_no ?? false) ? 'readonly style=background:#f8faff;cursor:not-allowed;' : '' }}>
            </div>

            <div class="sw-modal-field">
                <label>Payment Mode</label>
                <select name="payment_mode" id="modalPaymentMode" onchange="togglePaymentModeFields(this.value)">
                    <option value="cash">Cash</option>
                    <option value="online">Online / UPI</option>
                    <option value="cheque">Cheque</option>
                    <option value="bank_transfer">Bank Transfer</option>
                </select>
            </div>

            <div class="sw-modal-field" id="transactionIdField">
                <label id="transactionIdLabel">Transaction ID / Reference (Optional)</label>
                <input type="text" name="transaction_id" id="modalTransactionId" placeholder="e.g. TXN9876543210">
                <div id="modalChequeError" style="display:none; color:#dc2626; font-size:0.78rem; font-weight:700; margin-top:4px;">Cheque Number must contain digits only.</div>
            </div>

            <!-- Cheque Specific Fields -->
            <div id="chequeFields" style="display:none; border: 1.5px dashed var(--sw-border); padding: 14px; border-radius: 8px; margin-bottom:14px; background:#fcfdff;">
                <div class="sw-modal-field">
                    <label style="color:#b45309;">Bank Name</label>
                    <input type="text" name="bank_name" id="modalBankName" placeholder="e.g. State Bank of India">
                </div>
                <div class="sw-modal-field">
                    <label style="color:#b45309;">Cheque Date</label>
                    <input type="date" name="cheque_date" id="modalChequeDate" value="{{ now()->format('Y-m-d') }}">
                </div>
                <div class="sw-modal-field">
                    <label style="color:#b45309;">Branch</label>
                    <input type="text" name="branch" id="modalBranch" placeholder="e.g. Connaught Place Branch">
                </div>
            </div>

            <!-- Removed Miscellaneous Fee Section from Mark Paid as it is now standalone per installment -->

            {{-- Instant Discount Section --}}
            <div style="border: 1.5px dashed #fbbf24; background:#fffbeb; border-radius:9px; padding:14px; margin-bottom:14px;">
                <div style="font-size:.8rem;font-weight:800;color:#92400e;margin-bottom:10px;display:flex;align-items:center;gap:6px;">
                    <i class="fas fa-tag"></i> INSTANT DISCOUNT (Optional)
                </div>
                <div style="display:flex;gap:10px;">
                    <div class="sw-modal-field" style="flex:1;">
                        <label>Discount Type</label>
                        <select name="instant_discount_type" id="modalDiscountType" onchange="recalcNetAmount()">
                            <option value="flat">Flat Amount (₹)</option>
                            <option value="percentage">Percentage (%)</option>
                        </select>
                    </div>
                    <div class="sw-modal-field" style="flex:1;">
                        <label id="discountAmtLabel">Discount Amount (₹)</label>
                        <input type="number" name="instant_discount_amount" id="modalDiscountAmt"
                               step="0.01" min="0" placeholder="0" value="0"
                               oninput="recalcNetAmount()">
                    </div>
                </div>
                <div style="margin-top:8px;font-size:.85rem;font-weight:700;color:#16a34a;" id="netAmtDisplay"></div>
            </div>

            <!-- Add Fee Component (Optional) Section -->
            <div style="border: 1.5px dashed #2563eb; background:#f8fafc; border-radius:9px; padding:14px; margin-bottom:14px;" id="feeComponentSectionContainer">
                <div style="font-size:.8rem;font-weight:800;color:#1e40af;margin-bottom:0;display:flex;align-items:center;gap:6px;cursor:pointer;" onclick="toggleFeeComponentSection()">
                    <i class="fas fa-plus-circle"></i> Add Fee Component (Optional)
                    <i class="fas fa-chevron-down" id="feeComponentChevron" style="margin-left:auto;transition:transform 0.2s;"></i>
                </div>
                <div id="feeComponentContent" style="display:none; margin-top:12px;">
                    <!-- Fee Components Multi-Select -->
                    <div class="sw-modal-field" style="position:relative; margin-bottom:14px;">
                        <label>Add Fee Component (Optional)</label>
                        <div class="custom-multiselect" id="feeCompMultiselect">
                            <div class="multiselect-select-box" onclick="toggleMultiselectDropdown('feeCompDropdown')">
                                <span id="feeCompSelectedLabel" style="color:#64748b;">Select Fee Components...</span>
                                <i class="fas fa-caret-down"></i>
                            </div>
                            <div class="multiselect-dropdown-content" id="feeCompDropdown" style="display:none;">
                                <input type="text" class="multiselect-search" placeholder="Search components..." oninput="filterMultiselectOptions(this, 'feeCompList')">
                                <div class="multiselect-options-list" id="feeCompList">
                                    <!-- Dynamically populated -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Installments Multi-Select (appears conditionally) -->
                    <div class="sw-modal-field" id="installmentSelectionField" style="position:relative; display:none; margin-bottom:0;">
                        <label>Apply Discount On Installments</label>
                        <div class="custom-multiselect" id="installmentMultiselect">
                            <div class="multiselect-select-box" onclick="toggleMultiselectDropdown('instDropdown')">
                                <span id="instSelectedLabel" style="color:#64748b;">Select Installments...</span>
                                <i class="fas fa-caret-down"></i>
                            </div>
                            <div class="multiselect-dropdown-content" id="instDropdown" style="display:none;">
                                <input type="text" class="multiselect-search" placeholder="Search installments..." oninput="filterMultiselectOptions(this, 'instList')">
                                <div class="multiselect-options-list" id="instList">
                                    <!-- Dynamically populated -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="sw-modal-actions">
                <button type="button" class="sw-modal-cancel" onclick="closeMarkPaid()">Cancel</button>
                <button type="submit" class="sw-modal-submit" id="collectPaymentBtn"><i class="fas fa-check"></i> Collect Payment</button>
            </div>
        </form>
    </div>
</div>

{{-- ADD DISCOUNT MODAL --}}
<div class="sw-modal-overlay" id="discountModal" style="display:none;" onclick="if(event.target===this) this.style.display='none'">
    <div class="sw-modal">
        <h3><i class="fas fa-tag" style="color:var(--sw-blue2);"></i> Add Discount</h3>
        <form method="POST" action="{{ route('school.fees.student-wise') }}">
            @csrf
            <input type="hidden" name="action" value="apply_discount">
            <input type="hidden" name="student_id" value="{{ optional($viewStudent)->id }}">
            <input type="hidden" name="academic_session_id" value="{{ $selectedSession->id ?? '' }}">
            
            <div class="sw-modal-field">
                <label>Discount Name</label>
                <input type="text" name="name" required placeholder="e.g. Sibling Discount">
            </div>
            <div style="display:flex; gap:10px;">
                <div class="sw-modal-field" style="flex:1;">
                    <label>Discount Type</label>
                    <select name="type" id="discount_type_select" onchange="updateDiscountLabel(this.value)">
                        <option value="flat">Flat Amount (₹)</option>
                        <option value="percentage">Percentage (%)</option>
                    </select>
                </div>
                <div class="sw-modal-field" style="flex:1;">
                    <label id="discount_val_label">Discount Value (₹)</label>
                    <input type="number" name="amount" step="0.01" min="0.01" required placeholder="Enter value">
                </div>
            </div>
            @php
                $studentInstNos = isset($studentFees) ? $studentFees->pluck('installment_no')->unique()->sort() : collect();
            @endphp
            <div style="display:flex; gap:10px;">
                <div class="sw-modal-field" style="flex:1;">
                    <label>Target Installment</label>
                    <select name="installment_no" id="discountModalInstallmentSelect" style="width:100%; height:38px; border:1px solid var(--sw-border); border-radius:6px; padding:0 10px; background:#fff; font-size:.9rem; outline:none;">
                        <option value="">All Installments</option>
                    </select>
                </div>
                <div class="sw-modal-field" style="flex:1;">
                    <label>Remarks</label>
                    <input type="text" name="remarks" placeholder="Optional remarks" style="width:100%; height:38px; padding:0 10px; border:1px solid var(--sw-border); border-radius:6px; box-sizing:border-box;">
                </div>
            </div>
            <div class="sw-modal-actions">
                <button type="button" class="sw-modal-cancel" onclick="document.getElementById('discountModal').style.display='none'">Cancel</button>
                <button type="submit" class="sw-modal-submit">Apply Discount</button>
            </div>
        </form>
    </div>
</div>
<script>
function updateDiscountLabel(val) {
    document.getElementById('discount_val_label').textContent = val === 'percentage' ? 'Discount Value (%)' : 'Discount Value (₹)';
}
</script>

{{-- PAYMENT / REFUND SUCCESS POPUP MODAL --}}
@if(session('print_receipt_no') || session('print_refund_slip'))
@php
    $popupType = session('print_receipt_no') ? 'payment' : 'refund';
    $popupTitle = session('print_receipt_no') ? 'Payment Successful' : 'Refund Successful';
    $popupNo = session('print_receipt_no') ?: session('print_refund_slip');
@endphp
<div class="sw-modal-overlay" id="successPopupModal" style="display:flex;">
    <div class="sw-modal" style="width:400px; text-align:center; padding:32px 24px;">
        <!-- Success Icon -->
        <div style="width:72px; height:72px; border-radius:50%; border:3px solid #4ade80; display:flex; align-items:center; justify-content:center; margin:0 auto 20px; background:#f0fdf4;">
            <i class="fas fa-check" style="font-size:2.2rem; color:#4ade80;"></i>
        </div>
        
        <!-- Title -->
        <h3 style="font-size:1.4rem; font-weight:800; color:var(--sw-blue); margin:0 0 24px;">{{ $popupTitle }}</h3>
        
        <!-- Buttons Row -->
        <div style="display:flex; flex-direction:column; gap:10px;">
            <div style="display:flex; gap:10px;">
                <button type="button" 
                        onclick="window.open('{{ route('school.fees.print-slip', ['type' => $popupType, 'number' => $popupNo]) }}' + ('{{ $popupType }}' === 'invoice' || '{{ $popupType }}' === 'payment' ? '?student_id={{ optional($viewStudent)->id }}' : ''), '_blank', 'width=950,height=750')" 
                        style="flex:1; background:#0284c7; color:#fff; padding:11px; border-radius:9px; font-size:0.93rem; font-weight:700; cursor:pointer; border:none; display:flex; align-items:center; justify-content:center; gap:6px;">
                    <i class="fas fa-print"></i> Print Slip
                </button>
                <button type="button" 
                        onclick="window.open('{{ route('school.fees.print-slip', ['type' => $popupType, 'number' => $popupNo]) }}' + ('{{ $popupType }}' === 'invoice' || '{{ $popupType }}' === 'payment' ? '?student_id={{ optional($viewStudent)->id }}&copy=student' : '?copy=student'), '_blank', 'width=950,height=750')" 
                        style="flex:1; background:#16a34a; color:#fff; padding:11px; border-radius:9px; font-size:0.93rem; font-weight:700; cursor:pointer; border:none; display:flex; align-items:center; justify-content:center; gap:6px;">
                    <i class="fas fa-user"></i> Print Student Copy
                </button>
            </div>
            <button type="button" 
                    onclick="document.getElementById('successPopupModal').style.display='none'" 
                    class="sw-modal-cancel" 
                    style="width:100%; padding:11px; border-radius:9px; font-size:0.93rem; font-weight:700; cursor:pointer; border:none; background:#f1f5f9; color:#374151;">
                DONE
            </button>
        </div>
    </div>
</div>
@endif

{{-- ═══ Cancel Invoice / Refund — Reason Modal ═══ --}}
<div class="sw-modal-overlay" id="cancelReasonModal" style="display:none;" onclick="if(event.target===this) closeCancelReasonModal()">
    <div class="sw-modal" style="width:460px; padding:30px 28px;">
        <h3 style="font-size:1.05rem; font-weight:800; color:var(--sw-blue); margin:0 0 6px; display:flex; align-items:center; gap:8px;">
            <i class="fas fa-ban" style="color:#dc2626;"></i> Cancel Invoice
        </h3>
        <p style="font-size:0.83rem; color:#64748b; font-weight:600; margin:0 0 20px;">Please provide a reason for cancellation. This is required and will be recorded on the cancellation invoice.</p>

        {{-- Hidden data fields --}}
        <input type="hidden" id="crm_invoiceNo">
        <input type="hidden" id="crm_installmentNo">
        <input type="hidden" id="crm_studentId">

        <div class="sw-modal-field">
            <label>Cancellation Reason <span style="color:#dc2626;">*</span></label>
            <textarea id="crm_remarks"
                      rows="3"
                      placeholder="e.g. Incorrect amount entered, duplicate payment, refund requested…"
                      style="width:100%; padding:9px 13px; border:1.5px solid var(--sw-border); border-radius:9px; font-size:0.93rem; font-weight:600; color:var(--sw-dark); outline:none; resize:vertical; transition:border-color .2s; font-family:inherit;"
                      onfocus="this.style.borderColor='var(--sw-blue2)'"
                      onblur="this.style.borderColor='var(--sw-border)'"></textarea>
            <div id="crm_error" style="color:#dc2626; font-size:0.78rem; font-weight:700; margin-top:4px; display:none;"></div>
        </div>

        <div class="sw-modal-actions">
            <button type="button" class="sw-modal-cancel" onclick="closeCancelReasonModal()">
                <i class="fas fa-times" style="margin-right:5px;"></i>Go Back
            </button>
            <button type="button" class="sw-modal-submit" onclick="submitCancelReason()" style="background:#dc2626;">
                <i class="fas fa-ban" style="margin-right:5px;"></i>Confirm Cancel
            </button>
        </div>
    </div>
{{-- ═══ View All Invoices Modal (Popup Appearance) ═══ --}}
<div class="sw-modal-overlay" id="viewAllInvoicesModal" style="display:none;" onclick="if(event.target===this) closeViewAllInvoicesModal()">
    <div class="sw-modal" style="width:850px; max-height:90vh; display:flex; flex-direction:column; overflow:hidden;">
        <div style="background:var(--sw-blue); color:white; padding:18px 24px; display:flex; align-items:center; justify-content:space-between; border-radius:12px 12px 0 0;">
            <h3 style="margin:0; font-size:1.15rem; font-weight:800;"><i class="fas fa-file-invoice" style="margin-right:8px;"></i> Student Invoices</h3>
            <button onclick="closeViewAllInvoicesModal()" style="background:rgba(255,255,255,0.15); color:white; border:none; width:32px; height:32px; border-radius:50%; display:flex; align-items:center; justify-content:center; cursor:pointer;"><i class="fas fa-times"></i></button>
        </div>
        <div style="padding:24px; overflow-y:auto; flex-grow:1; background:#f8faff;">
            <!-- Student Header Info -->
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; background:#fff; border:1.5px solid var(--sw-border); border-radius:10px; padding:16px; margin-bottom:20px; font-size:0.88rem;">
                <div><span style="font-weight:700;color:#475569;">Student Name:</span> <span id="popupStudentName" style="font-weight:800;color:var(--sw-blue);"></span></div>
                <div><span style="font-weight:700;color:#475569;">Class & Section:</span> <span id="popupStudentClass" style="font-weight:800;color:var(--sw-dark);"></span></div>
                <div><span style="font-weight:700;color:#475569;">Admission Number:</span> <span id="popupStudentID" style="font-weight:800;color:var(--sw-dark);"></span></div>
                <div><span style="font-weight:700;color:#475569;">Father's Name:</span> <span id="popupStudentFather" style="font-weight:800;color:var(--sw-dark);"></span></div>
            </div>

            <!-- Invoices Container / Loader -->
            <div id="popupInvoicesList">
                <!-- Loaded dynamically -->
            </div>
        </div>
    </div>
</div>

</div>{{-- .sw-wrap --}}

<script>
window.activeFeeComponents = {!! json_encode(isset($feeComponents) ? $feeComponents : []) !!};
function toggleAll(master) {
    document.querySelectorAll('.row-check').forEach(cb => cb.checked = master.checked);
    const th = document.getElementById('thCheck');
    const sel = document.getElementById('selectAll');
    if (th) th.checked = master.checked;
    if (sel) sel.checked = master.checked;
}

function toggleInst(el) {
    // Only toggle if the click wasn't on a checkbox input itself
    if (event.target.tagName.toLowerCase() === 'input' && event.target.type === 'checkbox') {
        return;
    }
    el.classList.toggle('open');
}

function toggleSelectInstallment(headerCheckbox, targetClass) {
    const items = document.querySelectorAll('.' + targetClass);
    items.forEach(item => {
        item.checked = headerCheckbox.checked;
    });
    updateSelectedFeesSummary();
}

function updateSelectedFeesSummary() {
    // Section disable/fade logic based on selections
    const tuitionSection = document.getElementById('tuition-fees-section');
    const transportSection = document.getElementById('transport-fees-section');

    const tuitionCbs = document.querySelectorAll('#tuition-fees-section .fee-installment-checkbox, #tuition-fees-section .fee-item-checkbox');
    const transportCbs = document.querySelectorAll('#transport-fees-section .fee-installment-checkbox, #transport-fees-section .fee-item-checkbox');

    const hasCheckedTuition = Array.from(tuitionCbs).some(cb => cb.checked);
    const hasCheckedTransport = Array.from(transportCbs).some(cb => cb.checked);

    if (hasCheckedTuition) {
        // Disable Transport section
        if (transportSection) {
            transportSection.style.opacity = '0.4';
            transportSection.style.pointerEvents = 'none';
        }
        transportCbs.forEach(cb => {
            cb.disabled = true;
        });

        // Enable Tuition section
        if (tuitionSection) {
            tuitionSection.style.opacity = '1';
            tuitionSection.style.pointerEvents = 'auto';
        }
        tuitionCbs.forEach(cb => {
            cb.disabled = false;
        });
    } else if (hasCheckedTransport) {
        // Disable Tuition section
        if (tuitionSection) {
            tuitionSection.style.opacity = '0.4';
            tuitionSection.style.pointerEvents = 'none';
        }
        tuitionCbs.forEach(cb => {
            cb.disabled = true;
        });

        // Enable Transport section
        if (transportSection) {
            transportSection.style.opacity = '1';
            transportSection.style.pointerEvents = 'auto';
        }
        transportCbs.forEach(cb => {
            cb.disabled = false;
        });
    } else {
        // Enable both sections
        if (tuitionSection) {
            tuitionSection.style.opacity = '1';
            tuitionSection.style.pointerEvents = 'auto';
        }
        tuitionCbs.forEach(cb => {
            cb.disabled = false;
        });

        if (transportSection) {
            transportSection.style.opacity = '1';
            transportSection.style.pointerEvents = 'auto';
        }
        transportCbs.forEach(cb => {
            cb.disabled = false;
        });
    }

    const selectedList = document.getElementById('selected-items-list');
    const totalDueEl = document.getElementById('selected-total-due');
    const emptyEl = document.getElementById('multi-pay-empty');
    const detailsEl = document.getElementById('multi-pay-details');
    
    const checkedItems = document.querySelectorAll('.fee-item-checkbox:checked');
    selectedList.innerHTML = '';
    
    let totalDue = 0;
    let totalPaid = 0;
    let hasPending = false;
    let hasPaid = false;
    
    if (checkedItems.length > 0) {
        const groups = {};
        
        checkedItems.forEach(item => {
            const due = parseFloat(item.getAttribute('data-due') || 0);
            const paid = parseFloat(item.getAttribute('data-paid') || 0);
            const instLabel = item.getAttribute('data-inst-label') || item.getAttribute('data-label').split(' - ')[0];
            
            if (due > 0) {
                hasPending = true;
                totalDue += due;
            } else {
                hasPaid = true;
                totalPaid += paid;
            }
            
            if (!groups[instLabel]) {
                groups[instLabel] = {
                    due: 0,
                    paid: 0
                };
            }
            groups[instLabel].due += due;
            groups[instLabel].paid += paid;
        });
        
        for (const [instLabel, data] of Object.entries(groups)) {
            const itemRow = document.createElement('div');
            itemRow.style.display = 'flex';
            itemRow.style.justifyContent = 'space-between';
            itemRow.style.fontSize = '0.8rem';
            itemRow.style.padding = '4px 0';
            itemRow.style.borderBottom = '1px solid #f1f5f9';
            
            const displayAmt = hasPending ? data.due : data.paid;
            const displayColor = hasPending ? 'var(--sw-red)' : 'var(--sw-green)';
            
            itemRow.innerHTML = `
                <span style="color: #475569; font-weight: 500;">${instLabel}</span>
                <span style="font-weight: 700; color: ${displayColor};">₹${displayAmt.toFixed(2)}</span>
            `;
            selectedList.appendChild(itemRow);
        }
        
        const actionBtn = document.getElementById('multi-pay-action-btn');
        const totalLabel = document.getElementById('multi-pay-total-label');
        
        if (hasPending) {
            // Pending items to pay
            totalLabel.textContent = 'Total Selected Due:';
            totalDueEl.textContent = '₹' + totalDue.toFixed(2);
            totalDueEl.style.color = 'var(--sw-red)';
            actionBtn.disabled = false;
            actionBtn.style.opacity = '1';
            actionBtn.style.background = 'var(--sw-blue2)';
            actionBtn.innerHTML = '<i class="fas fa-cash-register"></i> Collect Combined Payment';
            actionBtn.setAttribute('onclick', 'openMultiPayModal()');
        } else {
            // Paid items to print
            totalLabel.textContent = 'Total Selected Paid:';
            totalDueEl.textContent = '₹' + totalPaid.toFixed(2);
            totalDueEl.style.color = 'var(--sw-green)';
            actionBtn.disabled = false;
            actionBtn.style.opacity = '1';
            actionBtn.style.background = '#16a34a';
            actionBtn.innerHTML = '<i class="fas fa-print"></i> Print Combined Receipt';
            actionBtn.setAttribute('onclick', 'printCombinedReceipt()');
        }
        
        emptyEl.style.display = 'none';
        detailsEl.style.display = 'flex';
    } else {
        emptyEl.style.display = 'block';
        detailsEl.style.display = 'none';
    }
}

function clearMultipleSelection() {
    document.querySelectorAll('.fee-item-checkbox:checked, .fee-installment-checkbox:checked').forEach(cb => {
        cb.checked = false;
    });
    updateSelectedFeesSummary();
}

function printCombinedReceipt() {
    const checkedItems = document.querySelectorAll('.fee-item-checkbox:checked');
    const ids = [];
    checkedItems.forEach(item => {
        ids.push(item.getAttribute('data-id'));
    });
    if (ids.length === 0) return;
    
    const url = '{{ route("school.fees.print-slip", ["type" => "combined", "number" => "IDS_PLACEHOLDER"]) }}'.replace('IDS_PLACEHOLDER', ids.join(','));
    window.open(url, '_blank', 'width=950,height=750');
}

function openMultiPayModal() {
    const checkedItems = document.querySelectorAll('.fee-item-checkbox:checked');
    if (checkedItems.length === 0) {
        alert('Please select at least one fee component.');
        return;
    }
    
    let totalDue = 0;
    const ids = [];
    let pendingCount = 0;
    let instNo = null;
    checkedItems.forEach(item => {
        const due = parseFloat(item.getAttribute('data-due') || 0);
        if (due > 0) {
            ids.push(item.getAttribute('data-id'));
            totalDue += due;
            pendingCount++;
            if (!instNo) {
                instNo = item.getAttribute('data-inst');
            }
        }
    });
    
    if (ids.length === 0) {
        alert('All selected components are already paid.');
        return;
    }
    
    const idsStr = ids.join(',');
    
    // Call openMarkPaid with aggregated info — checkbox dues already have pending cheques subtracted
    openMarkPaid({{ $viewStudent ? $viewStudent->id : 'null' }}, 999, totalDue, 'Combined (' + pendingCount + ' components)', null, 'multiple', 0);
    
    // Set the hidden input for multiple IDs
    document.getElementById('modalStudentFeeIds').value = idsStr;
}

let isManualAmtPaid = false;

function openMarkPaid(studentId, installmentNo, dueAmt, label, studentFeeId = null, feeType = 'tuition', pendingChequeAmt = 0) {
    const remainingCollectableAmt = Math.max(0, dueAmt - pendingChequeAmt);
    window.modalOriginalDue = remainingCollectableAmt;
    window.modalPayableAmount = remainingCollectableAmt;
    window.modalPendingChequeAmt = parseFloat(pendingChequeAmt) || 0;
    isManualAmtPaid = false;
    document.getElementById('modalStudentId').value = studentId;
    document.getElementById('modalInstallmentNo').value = installmentNo;
    document.getElementById('modalStudentFeeId').value = studentFeeId || '';
    document.getElementById('modalFeeType').value = feeType || 'tuition';
    document.getElementById('modalInstLabel').value = label;
    document.getElementById('modalDueAmt').value  = '₹' + remainingCollectableAmt.toFixed(2);
    document.getElementById('modalAmtPaid').value = remainingCollectableAmt.toFixed(2);
    // Issue 1 Fix: set max attribute on amount field
    document.getElementById('modalAmtPaid').max = remainingCollectableAmt.toFixed(2);
    document.getElementById('modalAmtError').style.display = 'none';
    document.getElementById('collectPaymentBtn').disabled = false;
    
    // Reset discount fields
    document.getElementById('modalDiscountAmt').value = '0';
    document.getElementById('modalDiscountType').value = 'flat';
    document.getElementById('netAmtDisplay').textContent = '';
    document.getElementById('discountAmtLabel').textContent = 'Discount Amount (₹)';
    window.prevDiscountAmt = 0;
    window.prevDiscountType = 'flat';
    window.prevDiscountKey = '';

    // Populate active components dropdown
    const componentListDiv = document.getElementById('feeCompList');
    if (componentListDiv) {
        componentListDiv.innerHTML = '';
        const activeComps = window.activeFeeComponents || [];
        activeComps.forEach(comp => {
            const name = (comp.component_name || '').toLowerCase();
            const head = (comp.head_name || '').toLowerCase();
            const isTransport = name.includes('transport') || name.includes('vehicle') || name.includes('bus') ||
                                head.includes('transport') || head.includes('vehicle') || head.includes('bus');
            if (!isTransport) {
                const optionDiv = document.createElement('div');
                optionDiv.className = 'multiselect-option';
                optionDiv.innerHTML = `
                    <label style="display:flex; align-items:center; gap:8px; width:100%; cursor:pointer; font-weight:normal; margin:0; text-transform:none; letter-spacing:normal;">
                        <input type="checkbox" name="discount_fee_component_ids[]" value="${comp.id}" class="fee-comp-checkbox" onchange="onFeeComponentSelectionChange()">
                        <span>${comp.component_name}</span>
                    </label>
                `;
                componentListDiv.appendChild(optionDiv);
            }
        });
    }

    // Populate installments dropdown
    const instListDiv = document.getElementById('instList');
    if (instListDiv) {
        instListDiv.innerHTML = '';
        const selectedInsts = [];
        if (installmentNo == 999 || feeType === 'multiple') {
            const checkedItems = document.querySelectorAll('.fee-item-checkbox:checked');
            const uniqueInsts = new Map();
            checkedItems.forEach(item => {
                const instVal = item.getAttribute('data-inst');
                const instLab = item.getAttribute('data-inst-label');
                if (instVal && !uniqueInsts.has(instVal)) {
                    uniqueInsts.set(instVal, instLab || `Installment ${instVal}`);
                }
            });
            uniqueInsts.forEach((lab, val) => {
                selectedInsts.push({ val: val, label: lab });
            });
        } else {
            selectedInsts.push({ val: installmentNo, label: label });
        }
        selectedInsts.forEach(inst => {
            const optionDiv = document.createElement('div');
            optionDiv.className = 'multiselect-option';
            optionDiv.innerHTML = `
                <label style="display:flex; align-items:center; gap:8px; width:100%; cursor:pointer; font-weight:normal; margin:0; text-transform:none; letter-spacing:normal;">
                    <input type="checkbox" name="discount_installment_nos[]" value="${inst.val}" class="fee-inst-checkbox" onchange="onFeeInstallmentSelectionChange()">
                    <span>${inst.label}</span>
                </label>
            `;
            instListDiv.appendChild(optionDiv);
        });
    }

    // Hide/reset fields
    const instSelField = document.getElementById('installmentSelectionField');
    if (instSelField) instSelField.style.display = 'none';

    const feeCompContent = document.getElementById('feeComponentContent');
    const feeComponentChevron = document.getElementById('feeComponentChevron');
    if (feeCompContent) feeCompContent.style.display = 'none';
    if (feeComponentChevron) feeComponentChevron.style.transform = 'rotate(0deg)';

    updateMultiselectSelectedLabel('feeCompDropdown', 'feeCompSelectedLabel', 'fee-comp-checkbox');
    updateMultiselectSelectedLabel('instDropdown', 'instSelectedLabel', 'fee-inst-checkbox');

    // Generate a unique Receipt No
    document.getElementById('modalReceiptNo').value = 'Fetching...';
    fetch(`/school/fees/get-next-receipt-no?fee_type=${encodeURIComponent(feeType)}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('modalReceiptNo').value = data.receipt_no;
            } else {
                document.getElementById('modalReceiptNo').value = 'REC-' + Math.floor(100000 + Math.random() * 900000);
            }
        })
        .catch(err => {
            document.getElementById('modalReceiptNo').value = 'REC-' + Math.floor(100000 + Math.random() * 900000);
        });
    
    // Reset payment mode
    const modeSelect = document.getElementById('modalPaymentMode');
    modeSelect.value = 'cash';
    togglePaymentModeFields('cash');

    // Reset Miscellaneous Fee fields
    if (document.getElementById('modalAddMiscFee')) {
        document.getElementById('modalAddMiscFee').checked = false;
        toggleMiscFeeFields(false);
        document.getElementById('modalSelectedMiscFeeId').value = '';
        document.getElementById('modalMiscFeeAmount').value = '0';
        document.getElementById('modalNewMiscFeeHead').value = '';
        document.getElementById('modalNewMiscFeeName').value = '';
    }

    recalcNetAmount();
    document.getElementById('markPaidModal').style.display = 'flex';
}

function toggleMiscFeeFields(checked) {
    const fieldsDiv = document.getElementById('miscFeeFields');
    if (fieldsDiv) {
        fieldsDiv.style.display = checked ? 'block' : 'none';
        
        // Update required attributes dynamically
        const selectedId = document.getElementById('modalSelectedMiscFeeId').value;
        const headInput = document.getElementById('modalNewMiscFeeHead');
        const nameInput = document.getElementById('modalNewMiscFeeName');
        const amountInput = document.getElementById('modalMiscFeeAmount');
        
        if (checked) {
            amountInput.setAttribute('required', 'required');
            if (!selectedId) {
                headInput.setAttribute('required', 'required');
                nameInput.setAttribute('required', 'required');
            } else {
                headInput.removeAttribute('required');
                nameInput.removeAttribute('required');
            }
        } else {
            amountInput.removeAttribute('required');
            headInput.removeAttribute('required');
            nameInput.removeAttribute('required');
        }
    }
}

function onSelectMiscFee(val) {
    const newFields = document.getElementById('newMiscFeeFields');
    const amountInput = document.getElementById('modalMiscFeeAmount');
    const headInput = document.getElementById('modalNewMiscFeeHead');
    const nameInput = document.getElementById('modalNewMiscFeeName');
    
    if (!val) {
        newFields.style.display = 'block';
        amountInput.value = '0.00';
        headInput.setAttribute('required', 'required');
        nameInput.setAttribute('required', 'required');
    } else {
        newFields.style.display = 'none';
        headInput.removeAttribute('required');
        nameInput.removeAttribute('required');
        
        // Auto-populate amount from selected option data attribute
        const select = document.getElementById('modalSelectedMiscFeeId');
        const selectedOption = select.options[select.selectedIndex];
        const amt = selectedOption.getAttribute('data-amount') || 0;
        amountInput.value = parseFloat(amt).toFixed(2);
    }
    recalcNetAmount();
}

function openAddMiscFeeModal(studentId, installmentNo, label) {
    document.getElementById('miscModalStudentId').value = studentId;
    document.getElementById('miscModalInstallmentNo').value = installmentNo;
    
    // Reset and initialize fields in the standalone modal
    document.getElementById('miscModalSelectedMiscFeeId').value = '';
    document.getElementById('miscModalMiscFeeAmount').value = '0.00';
    document.getElementById('miscModalNewMiscFeeHead').value = '';
    document.getElementById('miscModalNewMiscFeeName').value = '';
    
    // Call the standalone select function to initialize the form state
    onSelectMiscFeeForStandalone('');
    
    document.getElementById('addMiscFeeModal').style.display = 'flex';
}

function closeMiscFeeModal() {
    document.getElementById('addMiscFeeModal').style.display = 'none';
}

function onSelectMiscFeeForStandalone(val) {
    const newFields = document.getElementById('newMiscFeeFieldsStandalone');
    const amountInput = document.getElementById('miscModalMiscFeeAmount');
    const headInput = document.getElementById('miscModalNewMiscFeeHead');
    const nameInput = document.getElementById('miscModalNewMiscFeeName');
    
    if (!val) {
        if (newFields) newFields.style.display = 'block';
        amountInput.value = '0.00';
        headInput.setAttribute('required', 'required');
        nameInput.setAttribute('required', 'required');
    } else {
        if (newFields) newFields.style.display = 'none';
        headInput.removeAttribute('required');
        nameInput.removeAttribute('required');
        
        // Auto-populate amount from selected option data attribute
        const select = document.getElementById('miscModalSelectedMiscFeeId');
        const selectedOption = select.options[select.selectedIndex];
        const amt = selectedOption.getAttribute('data-amount') || 0;
        amountInput.value = parseFloat(amt).toFixed(2);
    }
}

function getComponentDiscountValidationError() {
    const discAmt = parseFloat(document.getElementById('modalDiscountAmt').value || 0);
    if (discAmt <= 0) return null;

    const selectedCompIds = Array.from(document.querySelectorAll('.fee-comp-checkbox:checked')).map(cb => cb.value);
    if (selectedCompIds.length === 0) return null;

    const selectedInstNos = Array.from(document.querySelectorAll('.fee-inst-checkbox:checked')).map(cb => cb.value);
    const installmentNo = document.getElementById('modalInstallmentNo').value;
    const feeType = document.getElementById('modalFeeType').value;
    const candidates = getCandidateFeeElements(installmentNo, feeType);

    let instsToCheck = selectedInstNos;
    if (instsToCheck.length === 0) {
        if (installmentNo == 999) {
            const uniqueInsts = new Set();
            candidates.forEach(item => {
                const inst = item.getAttribute('data-inst');
                if (inst) uniqueInsts.add(inst);
            });
            instsToCheck = Array.from(uniqueInsts);
        } else {
            instsToCheck = [installmentNo];
        }
    }

    for (const instVal of instsToCheck) {
        for (const compId of selectedCompIds) {
            const match = candidates.find(elem => elem.getAttribute('data-comp-id') == compId && elem.getAttribute('data-inst') == instVal);
            const due = match ? parseFloat(match.getAttribute('data-due') || 0) : 0;
            
            if (due <= 0) {
                const compCheckbox = document.querySelector(`.fee-comp-checkbox[value="${compId}"]`);
                const compName = compCheckbox ? compCheckbox.nextElementSibling.textContent.trim() : 'Fee Component';

                const instCheckbox = document.querySelector(`.fee-inst-checkbox[value="${instVal}"]`);
                const instName = instCheckbox ? instCheckbox.nextElementSibling.textContent.trim() : `Installment ${instVal}`;

                return `${compName} cannot receive an instant discount in ${instName} because its payable amount is ₹0. Please remove the ${compName} component for ${instName} or select another applicable installment.`;
            }
        }
    }

    return null;
}

let recalcTimeout = null;

function recalcNetAmount() {
    if (recalcTimeout) {
        clearTimeout(recalcTimeout);
    }

    const discType = document.getElementById('modalDiscountType').value;
    const label = document.getElementById('discountAmtLabel');
    if (label) {
        label.textContent = discType === 'percentage' ? 'Discount Percentage (%)' : 'Discount Amount (₹)';
    }

    // Run component discount check first
    const compValidationError = getComponentDiscountValidationError();
    if (compValidationError) {
        const display = document.getElementById('netAmtDisplay');
        if (display) display.textContent = '';
        if (document.getElementById('modalAmtError')) {
            validateAmountInput();
        }
        return;
    }

    // Disable submit button while calculating to prevent submission of outdated data
    const submitBtn = document.getElementById('collectPaymentBtn');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.style.opacity = '0.5';
    }

    recalcTimeout = setTimeout(() => {
        const studentId = document.getElementById('modalStudentId').value;
        if (!studentId) {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.style.opacity = '1';
            }
            return;
        }

        const installmentNo = document.getElementById('modalInstallmentNo').value;
        const studentFeeId = document.getElementById('modalStudentFeeId').value;
        const studentFeeIds = document.getElementById('modalStudentFeeIds').value;
        const feeType = document.getElementById('modalFeeType').value;
        const discAmt = parseFloat(document.getElementById('modalDiscountAmt').value || 0);

        const selectedCompIds = Array.from(document.querySelectorAll('.fee-comp-checkbox:checked')).map(cb => cb.value);
        const selectedInstNos = Array.from(document.querySelectorAll('.fee-inst-checkbox:checked')).map(cb => cb.value);

        let addMiscFee = '0';
        let miscAmt = 0;
        if (document.getElementById('modalAddMiscFee') && document.getElementById('modalAddMiscFee').checked) {
            addMiscFee = '1';
            miscAmt = parseFloat(document.getElementById('modalMiscFeeAmount').value || 0);
        }

        const rawAmt = parseFloat(document.getElementById('modalAmtPaid').value || 0);

        fetch('{{ route("school.fees.student-wise") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                action: 'calculate_discount',
                student_id: studentId,
                installment_no: installmentNo,
                student_fee_id: studentFeeId,
                student_fee_ids: studentFeeIds,
                fee_type: feeType,
                instant_discount_type: discType,
                instant_discount_amount: discAmt,
                discount_fee_component_ids: selectedCompIds,
                discount_installment_nos: selectedInstNos,
                amount_paid: rawAmt,
                add_misc_fee: addMiscFee,
                misc_fee_amount: miscAmt
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.modalPayableAmount = data.suggested_amount;

                const currentDiscAmtKey = discType + '_' + discAmt + '_' + selectedCompIds.join(',') + '_' + selectedInstNos.join(',');
                if (window.prevDiscountKey !== currentDiscAmtKey) {
                    isManualAmtPaid = false;
                    window.prevDiscountKey = currentDiscAmtKey;
                }

                const amtInput = document.getElementById('modalAmtPaid');
                if (amtInput) {
                    amtInput.max = data.suggested_amount.toFixed(2);
                    if (!isManualAmtPaid) {
                        amtInput.value = data.suggested_amount.toFixed(2);
                    }
                }

                const finalRawAmt = parseFloat(document.getElementById('modalAmtPaid').value || 0);
                const display = document.getElementById('netAmtDisplay');
                if (display) {
                    if (data.discount_amount > 0 || discAmt > 0) {
                        const finalRemainingDue = Math.max(0, data.suggested_amount - finalRawAmt);
                        display.innerHTML = `
                            <div style="background:#fef3c7; border:1px solid #fcd34d; border-radius:6px; padding:10px; margin-top:6px; color:#92400e;">
                                <div style="margin-bottom:4px; font-weight:800;">
                                    <i class="fas fa-info-circle"></i> Discount Calculation:
                                </div>
                                <div style="font-size:0.82rem; line-height:1.4;">
                                    • Applied Discount: <strong>₹${data.discount_amount.toFixed(2)}</strong><br>
                                    • Suggested Amount to Collect: <strong style="color:#16a34a; font-size:0.9rem;">₹${data.suggested_amount.toFixed(2)}</strong><br>
                                    • Amount Collected (Cashier Input): <strong>₹${finalRawAmt.toFixed(2)}</strong><br>
                                    • Remaining Due (after payment + discount): <strong style="color:#dc2626;">₹${finalRemainingDue.toFixed(2)}</strong>
                                </div>
                            </div>`;
                    } else {
                        display.textContent = '';
                    }
                }

                validateAmountInput();
            } else {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.style.opacity = '1';
                }
            }
        })
        .catch(err => {
            console.error('Error calculating discount:', err);
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.style.opacity = '1';
            }
        });
    }, 150);
}

function validateAmountInput() {
    const amtInput = document.getElementById('modalAmtPaid');
    const errEl    = document.getElementById('modalAmtError');
    const submitBtn = document.getElementById('collectPaymentBtn');
    const val = parseFloat(amtInput.value || 0);
    
    const compValidationError = getComponentDiscountValidationError();
    if (compValidationError) {
        errEl.textContent = compValidationError;
        errEl.style.display = 'block';
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.5';
        }
        const display = document.getElementById('netAmtDisplay');
        if (display) display.textContent = '';
        return;
    }
    
    const payableAmount = window.modalPayableAmount || 0;

    if (isNaN(val) || val <= 0) {
        errEl.textContent = 'Amount must be greater than ₹0.';
        errEl.style.display = 'block';
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.5';
        }
    } else if (val > payableAmount + 0.01) {
        errEl.textContent = 'Amount to Collect cannot exceed the total payable amount.';
        errEl.style.display = 'block';
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.5';
        }
    } else {
        errEl.style.display = 'none';
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.style.opacity = '1';
        }
    }
}

function togglePaymentModeFields(mode) {
    const chequeFields = document.getElementById('chequeFields');
    const chequeInputs = chequeFields ? chequeFields.querySelectorAll('input') : [];
    const txLabel = document.getElementById('transactionIdLabel');
    const txInput = document.getElementById('modalTransactionId');

    if (mode === 'cheque') {
        if (chequeFields) chequeFields.style.display = 'block';
        chequeInputs.forEach(input => input.setAttribute('required', 'required'));
        if (txLabel) txLabel.textContent = 'Cheque Number';
        if (txInput) {
            txInput.placeholder = 'e.g. 123456';
            txInput.setAttribute('required', 'required');
        }
    } else {
        if (chequeFields) chequeFields.style.display = 'none';
        chequeInputs.forEach(input => input.removeAttribute('required'));
        if (txLabel) txLabel.textContent = 'Transaction ID / Reference (Optional)';
        if (txInput) {
            txInput.placeholder = 'e.g. TXN9876543210';
            txInput.removeAttribute('required');
        }
    }
}

function closeMarkPaid() {
    document.getElementById('markPaidModal').style.display = 'none';
}
// ── End Issue 1 Fix ────────────────────────────────────────────────────────

// ── Issue 1 Fix: Prevent form submit if amount invalid + cheque confirmation ─
document.addEventListener('DOMContentLoaded', function() {
    const paymentForm = document.getElementById('paymentForm');
    const amtInput = document.getElementById('modalAmtPaid');

    if (amtInput) {
        // Enforce validation while typing, pasting, increment/decrement (change), etc.
        ['input', 'paste', 'change', 'keyup', 'keydown', 'mouseup', 'focus', 'blur'].forEach(evt => {
            amtInput.addEventListener(evt, function() {
                validateAmountInput();
            });
        });
    }

    const txInput = document.getElementById('modalTransactionId');
    const paymentModeSelect = document.getElementById('modalPaymentMode');
    const chequeErrorEl = document.getElementById('modalChequeError');

    function checkChequeValidation() {
        if (paymentModeSelect && paymentModeSelect.value === 'cheque' && txInput) {
            const val = txInput.value;
            if (val && !/^[0-9]+$/.test(val)) {
                if (chequeErrorEl) {
                    chequeErrorEl.style.display = 'block';
                    chequeErrorEl.textContent = 'Cheque Number must contain digits only.';
                }
                return false;
            } else {
                if (chequeErrorEl) {
                    chequeErrorEl.style.display = 'none';
                }
            }
        } else {
            if (chequeErrorEl) {
                chequeErrorEl.style.display = 'none';
            }
        }
        return true;
    }

    if (txInput) {
        ['input', 'paste', 'change', 'keyup', 'keydown', 'mouseup', 'drop', 'focus', 'blur'].forEach(evt => {
            txInput.addEventListener(evt, function() {
                setTimeout(checkChequeValidation, 0);
            });
        });
    }

    if (paymentModeSelect) {
        paymentModeSelect.addEventListener('change', function() {
            checkChequeValidation();
        });
    }

    if (paymentForm) {
        paymentForm.addEventListener('submit', function(e) {
            const compValidationError = getComponentDiscountValidationError();
            if (compValidationError) {
                e.preventDefault();
                e.stopPropagation();
                
                const errEl = document.getElementById('modalAmtError');
                errEl.textContent = compValidationError;
                errEl.style.display = 'block';
                return false;
            }

            const paymentMode = document.getElementById('modalPaymentMode').value;
            if (paymentMode === 'cheque') {
                if (!checkChequeValidation()) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
            }

            const val = parseFloat(amtInput.value || 0);
            const payableAmount = window.modalPayableAmount || 0;

            if (isNaN(val) || val <= 0) {
                e.preventDefault();
                const errEl = document.getElementById('modalAmtError');
                errEl.textContent = 'Amount must be greater than ₹0.';
                errEl.style.display = 'block';
                return false;
            }
            if (val > payableAmount + 0.01) {
                e.preventDefault();
                const errEl = document.getElementById('modalAmtError');
                errEl.textContent = 'Amount to Collect cannot exceed the total payable amount.';
                errEl.style.display = 'block';
                return false;
            }

            // ── Cheque-specific confirmations ────────────────────────────────
            if (paymentMode === 'cheque') {
                // Add Miscellaneous Fee if checked
                let miscAmt = 0;
                if (document.getElementById('modalAddMiscFee') && document.getElementById('modalAddMiscFee').checked) {
                    miscAmt = parseFloat(document.getElementById('modalMiscFeeAmount').value || 0);
                    if (isNaN(miscAmt) || miscAmt < 0) miscAmt = 0;
                }
                const maxDue = (window.modalOriginalDue || 0) + miscAmt;
                const pendingAmt = window.modalPendingChequeAmt || 0;
                const remaining = Math.max(0, maxDue - val);

                // Warn if there's already an uncleared cheque
                if (pendingAmt > 0) {
                    const proceed = confirm(
                        '⚠️ UNCLEARED CHEQUE ALERT\n\n' +
                        'This student already has a pending (uncleared) cheque of ₹' + pendingAmt.toLocaleString('en-IN') + '.\n\n' +
                        'The previous cheque has not been cleared yet.\n\n' +
                        'Do you want to proceed and record another cheque payment?'
                    );
                    if (!proceed) { e.preventDefault(); return false; }
                }

                // Confirm partial cheque split
                if (val < maxDue - 0.01) {
                    const proceed = confirm(
                        '🔔 PARTIAL CHEQUE PAYMENT\n\n' +
                        'Cheque Amount: ₹' + val.toLocaleString('en-IN') + ' (will be marked as Pending until cleared)\n' +
                        'Remaining Due: ₹' + remaining.toLocaleString('en-IN') + ' (will stay as Due)\n\n' +
                        '• No invoice will be generated until the cheque is cleared by the bank.\n' +
                        '• If the cheque bounces, the full amount will revert to Due.\n\n' +
                        'Confirm partial cheque payment?'
                    );
                    if (!proceed) { e.preventDefault(); return false; }
                } else if (val > 0) {
                    // Full cheque — still notify about no-invoice-until-cleared rule
                    const proceed = confirm(
                        '🔔 CHEQUE PAYMENT\n\n' +
                        'Amount: ₹' + val.toLocaleString('en-IN') + ' will be recorded as Cheque Pending.\n\n' +
                        '• No invoice will be generated until the cheque is cleared by the bank.\n' +
                        '• Once cleared, an invoice showing CLEARED status will be generated.\n' +
                        '• If bounced, the amount will revert to Due with no invoice.\n\n' +
                        'Confirm cheque payment?'
                    );
                    if (!proceed) { e.preventDefault(); return false; }
                }
            }
        });
    }
});
// ── End Issue 1 Fix ────────────────────────────────────────────────────────

// ── Issue 2 Fix: AJAX Cheque Status Update ─────────────────────────────────
function updateChequeStatus(chequeId, newStatus, btn) {
    const statusLabels = { cleared: 'Clear', bounced: 'Bounce', pending: 'Pending' };
    const confirmMsg = newStatus === 'cleared'
        ? 'Kya aap is cheque ko CLEAR mark karna chahte hain? Student fees me amount paid ho jayega.'
        : 'Kya aap is cheque ko BOUNCE mark karna chahte hain? Student fees wapas pending ho jayengi.';

    if (!confirm(confirmMsg)) return;

    btn.disabled = true;
    btn.textContent = 'Processing...';

    fetch('{{ route("school.fees.student-wise") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            action: 'update_cheque_status',
            cheque_id: chequeId,
            status: newStatus
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Show success message and reload to reflect updated state
            const successDiv = document.createElement('div');
            successDiv.style.cssText = 'position:fixed; top:20px; right:20px; z-index:9999; background:#16a34a; color:#fff; padding:14px 20px; border-radius:10px; font-weight:700; font-size:0.93rem; box-shadow:0 4px 20px rgba(0,0,0,0.2);';
            successDiv.textContent = '✓ ' + data.message;
            document.body.appendChild(successDiv);
            setTimeout(() => window.location.reload(), 1200);
        } else {
            btn.disabled = false;
            btn.textContent = statusLabels[newStatus] || newStatus;
            alert('Error: ' + (data.message || 'Could not update cheque status.'));
        }
    })
    .catch(err => {
        console.error(err);
        btn.disabled = false;
        btn.textContent = statusLabels[newStatus] || newStatus;
        alert('Network error. Please try again.');
    });
}
// ── End Issue 2 Fix ────────────────────────────────────────────────────────

function toggleVisibility(checkbox, studentId) {
    const label = checkbox.closest('.sw-vis-toggle').querySelector('.sw-vis-status-text');
    const isChecked = checkbox.checked;
    label.textContent = isChecked ? 'Visible' : 'Hidden';
    label.className = 'sw-vis-status-text ' + (isChecked ? 'sw-vis-label-on' : 'sw-vis-label-off');

    fetch('{{ route('school.fees.student-wise') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ action: 'toggle_visibility', student_id: studentId, visible: isChecked })
    }).catch(() => {});
}

function toggleAllYear(checkbox) {
    const url = new URL(window.location.href);
    if (checkbox.checked) {
        url.searchParams.set('all_year', '1');
    } else {
        url.searchParams.delete('all_year');
    }
    window.location.href = url.toString();
}

document.addEventListener('DOMContentLoaded', function() {
    const hideBtn = document.querySelector('.sw-vis-btns button:first-child');
    const showBtn = document.querySelector('.sw-vis-btns button:last-child');
    
    if (hideBtn && showBtn) {
        hideBtn.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('.sw-amount').forEach(el => {
                el.textContent = '₹****';
            });
        });
        showBtn.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('.sw-amount').forEach(el => {
                if (el.dataset.originalVal) {
                    el.textContent = el.dataset.originalVal;
                }
            });
        });
    }
});

function bulkToggleVisibility(visible) {
    const checkedBoxes = document.querySelectorAll('.row-check:checked');
    if (checkedBoxes.length === 0) {
        alert('Please select at least one student.');
        return;
    }
    const studentIds = Array.from(checkedBoxes).map(cb => cb.getAttribute('data-id'));
    
    fetch('{{ route('school.fees.student-wise') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ action: 'bulk_toggle_visibility', student_ids: studentIds, visible: visible })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Update the UI toggles
            checkedBoxes.forEach(cb => {
                const tr = cb.closest('tr');
                const checkbox = tr.querySelector('.sw-vis-toggle input');
                if (checkbox) {
                    checkbox.checked = visible;
                    const label = tr.querySelector('.sw-vis-toggle .sw-vis-status-text');
                    if (label) {
                        label.textContent = visible ? 'Visible' : 'Hidden';
                        label.className = 'sw-vis-status-text ' + (visible ? 'sw-vis-label-on' : 'sw-vis-label-off');
                    }
                }
            });
        }
    })
    .catch(err => console.error(err));
}

// ── Cancel Invoice / Refund via styled modal ──────────────────────────────
function openCancelReasonModal(invoiceNo, installmentNo, studentId, invoiceKind) {
    // invoiceKind: 'payment' or 'refund'
    document.getElementById('crm_invoiceNo').value     = invoiceNo;
    document.getElementById('crm_installmentNo').value = installmentNo;
    document.getElementById('crm_studentId').value     = studentId;
    document.getElementById('crm_remarks').value       = '';
    document.getElementById('crm_remarks').focus();

    const modal = document.getElementById('cancelReasonModal');
    modal.style.display = 'flex';
}

function closeCancelReasonModal() {
    document.getElementById('cancelReasonModal').style.display = 'none';
}

function submitCancelReason() {
    const remarks = document.getElementById('crm_remarks').value.trim();
    const errEl   = document.getElementById('crm_error');

    if (!remarks) {
        errEl.textContent = 'Please enter a cancellation reason.';
        errEl.style.display = 'block';
        return;
    }
    errEl.style.display = 'none';

    const invoiceNo     = document.getElementById('crm_invoiceNo').value;
    const installmentNo = document.getElementById('crm_installmentNo').value;
    const studentId     = document.getElementById('crm_studentId').value;

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("school.fees.student-wise") }}';

    const fields = {
        '_token':        '{{ csrf_token() }}',
        'action':        'cancel_invoice',
        'student_id':    studentId,
        'installment_no': installmentNo,
        'invoice_no':    invoiceNo,
        'remarks':       remarks,
    };

    Object.entries(fields).forEach(([name, value]) => {
        const inp = document.createElement('input');
        inp.type  = 'hidden';
        inp.name  = name;
        inp.value = value;
        form.appendChild(inp);
    });

    document.body.appendChild(form);
    form.submit();
}

// Legacy alias — keeps existing cancel button onclick working
function cancelInvoice(invoiceNo, installmentNo, studentId) {
    openCancelReasonModal(invoiceNo, installmentNo, studentId, 'payment');
}

function validateRefundAmount(input) {
    const val = parseFloat(input.value);
    const max = parseFloat(input.getAttribute('max') || '0');
    const errEl = document.getElementById('refundAmtError');
    if (!errEl) return;
    if (!input.value || val < 1 || val > max) {
        errEl.textContent = 'Amount must be between ₹1 and ₹' + max.toFixed(2);
        errEl.style.display = 'block';
    } else {
        errEl.style.display = 'none';
    }
}

// Close modal on Escape key
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        document.getElementById('markPaidModal').style.display = 'none';
        document.getElementById('discountModal').style.display = 'none';
        closeCancelReasonModal();
    }
});

function openAddDiscountModal() {
    const selectEl = document.getElementById('discountModalInstallmentSelect');
    if (selectEl) {
        // Clear all except the first option ("All Installments")
        selectEl.innerHTML = '<option value="">All Installments</option>';
        
        // Find all elements with class 'fee-installment-checkbox' to retrieve the active installment numbers
        const instNos = new Set();
        document.querySelectorAll('.fee-installment-checkbox[data-type="tuition"]').forEach(cb => {
            const instNo = cb.getAttribute('data-inst');
            if (instNo) {
                instNos.add(parseInt(instNo));
            }
        });
        
        // Sort installment numbers and append to select
        Array.from(instNos).sort((a, b) => a - b).forEach(instNo => {
            const opt = document.createElement('option');
            opt.value = instNo;
            opt.textContent = 'Installment ' + instNo;
            selectEl.appendChild(opt);
        });
    }
    
    document.getElementById('discountModal').style.display = 'flex';
}

function toggleDeactivated(checkbox) {
    const url = new URL(window.location.href);
    if (checkbox.checked) {
        url.searchParams.set('show_deactivated', '1');
    } else {
        url.searchParams.delete('show_deactivated');
    }
    window.location.href = url.toString();
}

function toggleDeleted(checkbox) {
    const url = new URL(window.location.href);
    if (checkbox.checked) {
        url.searchParams.set('show_deleted', '1');
    } else {
        url.searchParams.delete('show_deleted');
    }
    window.location.href = url.toString();
}

function getStatusClass(status) {
    if (status === 'paid') return 'paid';
    if (status === 'partially_paid') return 'partial';
    return 'pending';
}

function viewInvoicesPopup(studentId) {
    const overlay = document.getElementById('viewAllInvoicesModal');
    const listContainer = document.getElementById('popupInvoicesList');
    
    overlay.style.display = 'flex';
    listContainer.innerHTML = `
        <div class="sw-empty" style="padding:40px;">
            <i class="fas fa-spinner fa-spin" style="font-size:2rem; color:var(--sw-blue2); margin-bottom:12px;"></i>
            <p style="font-weight:600;">Retrieving invoices list...</p>
        </div>
    `;

    const invoicesUrlPattern = "{{ route('school.fees.student-invoices', ['student' => ':student'], false) }}";
    const invoicesUrl = invoicesUrlPattern.replace(':student', studentId);
    fetch(invoicesUrl)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('popupStudentName').textContent = data.student.name || data.student.full_name;
                document.getElementById('popupStudentClass').textContent = `${data.student.class} - ${data.student.section}`;
                document.getElementById('popupStudentID').textContent = data.student.admission_number;
                document.getElementById('popupStudentFather').textContent = data.student.father_name;

                let html = '';
                data.invoices.forEach(inv => {
                    const statusClass = getStatusClass(inv.status);
                    const isCancelled = inv.status === 'cancelled';
                    const isTransport = inv.is_transport === true;
                    const transportBadge = isTransport 
                        ? `<span style="font-size:0.68rem; background:#f0fdf4; color:#16a34a; border:1px solid #bbf7d0; border-radius:4px; padding:1px 7px; font-weight:700; margin-left:6px;"><i class="fas fa-bus" style="margin-right:3px;"></i>Transport</span>`
                        : '';
                    
                    const printUrlPattern = "{{ route('school.fees.print-slip', ['type' => 'invoice', 'number' => ':number'], false) }}";
                    const printUrl = printUrlPattern.replace(':number', inv.invoice_no) + `?student_id=${data.student.id}`;

                    html += `
                        <div class="sw-fee-body" style="border-radius:12px; border:1.5px solid var(--sw-border); background:#fff; margin-bottom:16px; padding:0; overflow:hidden; ${isTransport ? 'border-left: 4px solid #16a34a;' : ''}">
                            <div style="background:#f8faff; padding:14px 18px; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid #e2e8f0; flex-wrap:wrap; gap:10px;">
                                <div style="font-weight:800; color:var(--sw-blue); font-size:0.95rem; display:flex; align-items:center; gap:8px;">
                                    <span>#${inv.invoice_no}</span>
                                    <span>${inv.installment_label || ('Installment ' + inv.installment_no)}</span>
                                    ${transportBadge}
                                    <span class="sw-inst-badge ${statusClass}">${inv.status.replace('_', ' ')}</span>
                                </div>
                                <div style="font-size:0.82rem; color:#475569; font-weight:600;">
                                    Total: ₹${Number(inv.total).toFixed(0)} &nbsp;|&nbsp; 
                                    Discount: <span style="color:var(--sw-red)">₹${Number(inv.discount).toFixed(0)}</span> &nbsp;|&nbsp; 
                                    Paid: <span style="color:var(--sw-green)">₹${Number(inv.paid).toFixed(0)}</span> &nbsp;|&nbsp; 
                                    Due: <span style="color:var(--sw-blue2)">₹${Number(inv.due).toFixed(0)}</span>
                                </div>
                                <div style="display:flex; gap:8px;">
                                    <a href="${printUrl}" 
                                       target="_blank" 
                                       class="sw-btn-vis" style="padding:4px 10px; font-size:0.8rem; text-decoration:none;">
                                        <i class="fas fa-print"></i> Print
                                    </a>
                                    ${!(isCancelled || inv.status === 'refunded' || inv.status === 'bounced' || inv.invoice_status === 'bounced') ? `
                                        <button class="sw-btn-vis" style="color:var(--sw-red); border-color:#fca5a5; background:#fff5f5; padding:4px 10px; font-size:0.8rem;" onclick="cancelInvoiceAjaxPopup('${inv.invoice_no}', ${inv.installment_no}, ${data.student.id})">
                                            <i class="fas fa-ban"></i> Cancel
                                        </button>
                                    ` : ''}
                                </div>
                            </div>
                            <div style="overflow-x:auto;">
                                <table class="sw-table" style="font-size:0.83rem;">
                                    <thead>
                                        <tr style="background:#fff; border-bottom:1.5px solid #e2e8f0;">
                                            <th style="color:#475569; padding:8px 18px; text-align:left;">Fee Component</th>
                                            <th style="color:#475569; padding:8px 18px; text-align:right;">Original Amount</th>
                                            <th style="color:#475569; padding:8px 18px; text-align:right;">Discount</th>
                                            <th style="color:#475569; padding:8px 18px; text-align:right;">Paid</th>
                                            <th style="color:#475569; padding:8px 18px; text-align:right;">Outstanding</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${inv.components.map(comp => `
                                            <tr style="border-bottom:1px solid #f1f5f9; ${comp.status === 'refunded' ? 'background:#faf5ff;' : (comp.is_transport ? 'background:#f0fdf4;' : '')}">
                                                <td style="font-weight:600; text-align:left; padding:10px 18px;">
                                                    ${comp.name}
                                                    ${comp.status === 'refunded' ? '<span style="font-size:0.65rem; background:#f3e8ff; color:#7e22ce; border:1px solid #e9d5ff; border-radius:4px; padding:1px 4px; margin-left:6px; font-weight:700;"><i class="fas fa-undo"></i> Refunded</span>' : ''}
                                                </td>
                                                <td style="text-align:right; font-weight:600; padding:10px 18px;">₹${Number(comp.amount).toFixed(2)}</td>
                                                <td style="text-align:right; color:var(--sw-red); font-weight:600; padding:10px 18px;">₹${Number(comp.discount).toFixed(2)}</td>
                                                <td style="text-align:right; color:var(--sw-green); font-weight:600; padding:10px 18px;">₹${Number(comp.paid).toFixed(2)}</td>
                                                <td style="text-align:right; color:var(--sw-blue2); font-weight:700; padding:10px 18px;">₹${Number(comp.due).toFixed(2)}</td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    `;
                });

                if (data.invoices.length === 0) {
                    html = `
                        <div style="text-align:center; padding:30px; color:#94a3b8; font-weight:600;">
                            <i class="fas fa-info-circle" style="font-size:1.5rem; margin-bottom:8px; display:block;"></i>
                            No invoices or installments found for this student.
                        </div>
                    `;
                }

                listContainer.innerHTML = html;
            } else {
                listContainer.innerHTML = `<div style="color:var(--sw-red); font-weight:700; text-align:center; padding:20px;">Could not retrieve invoices.</div>`;
            }
        })
        .catch(err => {
            console.error(err);
            listContainer.innerHTML = `<div style="color:var(--sw-red); font-weight:700; text-align:center; padding:20px;">Network error loading invoices.</div>`;
        });
}

function closeViewAllInvoicesModal() {
    document.getElementById('viewAllInvoicesModal').style.display = 'none';
}

function cancelInvoiceAjaxPopup(invoiceNo, installmentNo, studentId) {
    const remarks = prompt('Please enter the reason for cancelling this invoice:');
    if (remarks === null) return;
    if (!remarks.trim()) {
        alert('Cancellation reason is required.');
        return;
    }

    fetch("{{ route('school.fees.student-wise', [], false) }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            action: 'cancel_invoice',
            student_id: studentId,
            installment_no: installmentNo,
            invoice_no: invoiceNo,
            remarks: remarks
        })
    })
    .then(res => {
        if (res.ok) {
            alert('Invoice cancelled successfully!');
            closeViewAllInvoicesModal();
            window.location.reload();
        } else {
            alert('Error cancelling invoice. Please try again.');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Failed to connect to the server.');
    });
}

function getStatusClass(status) {
    status = status.toLowerCase();
    if (status === 'paid') return 'paid';
    if (status === 'partially_paid' || status === 'partial') return 'partial';
    if (status === 'refunded') return 'refunded';
    if (status === 'cancelled') return 'cancelled';
    return 'pending';
}

document.addEventListener('DOMContentLoaded', function() {
    updateSelectedFeesSummary();
});

// ── Add Fee Component Feature JS Helpers ──
function toggleFeeComponentSection() {
    const content = document.getElementById('feeComponentContent');
    const chevron = document.getElementById('feeComponentChevron');
    if (content.style.display === 'none') {
        content.style.display = 'block';
        chevron.style.transform = 'rotate(180deg)';
    } else {
        content.style.display = 'none';
        chevron.style.transform = 'rotate(0deg)';
        // Reset component checkboxes if collapsed
        document.querySelectorAll('.fee-comp-checkbox').forEach(cb => cb.checked = false);
        onFeeComponentSelectionChange();
    }
}

function toggleMultiselectDropdown(dropdownId) {
    const dropdown = document.getElementById(dropdownId);
    if (dropdown.style.display === 'none') {
        document.querySelectorAll('.multiselect-dropdown-content').forEach(d => {
            if (d.id !== dropdownId) d.style.display = 'none';
        });
        dropdown.style.display = 'block';
    } else {
        dropdown.style.display = 'none';
    }
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('.custom-multiselect')) {
        document.querySelectorAll('.multiselect-dropdown-content').forEach(d => d.style.display = 'none');
    }
});

function filterMultiselectOptions(searchInput, listId) {
    const query = searchInput.value.toLowerCase();
    const list = document.getElementById(listId);
    const options = list.querySelectorAll('.multiselect-option');
    options.forEach(opt => {
        const text = opt.querySelector('span').textContent.toLowerCase();
        if (text.includes(query)) {
            opt.style.display = 'flex';
        } else {
            opt.style.display = 'none';
        }
    });
}

function updateMultiselectSelectedLabel(dropdownId, labelId, checkboxClass) {
    const checked = document.querySelectorAll(`#${dropdownId} .${checkboxClass}:checked`);
    const label = document.getElementById(labelId);
    if (checked.length === 0) {
        label.textContent = dropdownId.includes('feeComp') ? 'Select Fee Components...' : 'Select Installments...';
        label.style.color = '#64748b';
    } else if (checked.length <= 2) {
        const names = [];
        checked.forEach(cb => names.push(cb.nextElementSibling.textContent));
        label.textContent = names.join(', ');
        label.style.color = 'var(--sw-dark)';
    } else {
        label.textContent = `${checked.length} items selected`;
        label.style.color = 'var(--sw-dark)';
    }
}

function onFeeComponentSelectionChange() {
    const checkedComps = document.querySelectorAll('.fee-comp-checkbox:checked');
    const installmentField = document.getElementById('installmentSelectionField');
    if (checkedComps.length > 0) {
        installmentField.style.display = 'block';
    } else {
        installmentField.style.display = 'none';
        // Deselect all selected installments when hidden
        document.querySelectorAll('.fee-inst-checkbox').forEach(cb => cb.checked = false);
        updateMultiselectSelectedLabel('instDropdown', 'instSelectedLabel', 'fee-inst-checkbox');
    }
    updateMultiselectSelectedLabel('feeCompDropdown', 'feeCompSelectedLabel', 'fee-comp-checkbox');
    recalcNetAmount();
}

function onFeeInstallmentSelectionChange() {
    updateMultiselectSelectedLabel('instDropdown', 'instSelectedLabel', 'fee-inst-checkbox');
    recalcNetAmount();
}

function getCandidateFeeElements(installmentNo, feeType) {
    if (installmentNo == 999 || feeType === 'multiple') {
        return Array.from(document.querySelectorAll('.fee-item-checkbox:checked'));
    } else {
        const cls = feeType === 'transport' ? `.transport-inst-${installmentNo}` : `.tuition-inst-${installmentNo}`;
        return Array.from(document.querySelectorAll(cls));
    }
}

// Print behavior is now handled by the interactive successPopupModal dialog

window.lateFineCache = null;

function openLateFineModal(targetInstNo) {
    const studentId = "{{ $viewStudent ? $viewStudent->id : '' }}";
    if (!studentId) return;

    const modal = document.getElementById('manageLateFineModal');
    const loading = document.getElementById('lateFineLoadingState');
    const empty = document.getElementById('lateFineEmptyState');
    const form = document.getElementById('lateFineManagementForm');
    const notice = document.getElementById('lateFineReadOnlyNotice');
    const container = document.getElementById('lateFineInstallmentsContainer');

    modal.style.display = 'flex';

    const renderData = (data) => {
        loading.style.display = 'none';
        if (!data.success || !data.installments || data.installments.length === 0) {
            empty.style.display = 'block';
            form.style.display = 'none';
            return;
        }

        empty.style.display = 'none';
        form.style.display = 'block';

        if (!data.can_manage) {
            notice.style.display = 'block';
            document.getElementById('saveLateFineBtn').disabled = true;
            document.getElementById('saveLateFineBtn').style.opacity = '0.5';
            document.getElementById('saveLateFineBtn').style.cursor = 'not-allowed';
        } else {
            notice.style.display = 'none';
            document.getElementById('saveLateFineBtn').disabled = false;
            document.getElementById('saveLateFineBtn').style.opacity = '1';
            document.getElementById('saveLateFineBtn').style.cursor = 'pointer';
        }

        container.innerHTML = '';
        data.installments.forEach(inst => {
            const isApplied = inst.is_applied;
            const card = document.createElement('div');
            card.className = 'late-fine-inst-card';
            card.style.cssText = 'border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 14px; background: #fafafa; transition: all 0.2s ease;';

            card.innerHTML = `
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                    <span style="font-weight:800; font-size:0.95rem; color:#0f172a;">${inst.installment_name}</span>
                    <span class="status-badge-${inst.installment_no}" style="font-size:0.75rem; font-weight:700; padding:3px 10px; border-radius:12px; ${isApplied ? 'background:#dcfce7; color:#15803d; border:1px solid #86efac;' : 'background:#fef2f2; color:#b91c1c; border:1px solid #fca5a5;'}">
                        Current Status: ${inst.status_label}
                    </span>
                </div>
                <div style="font-size:0.88rem; font-weight:700; color:#c2410c; margin-bottom:12px; display:flex; align-items:center; gap:6px;">
                    <i class="fas fa-exclamation-circle"></i> Late Fine: ${inst.fine_formatted}
                </div>
                <div style="display:flex; gap:20px; background:#ffffff; border:1px solid #cbd5e1; border-radius:8px; padding:10px 14px;">
                    <label style="display:inline-flex; align-items:center; gap:8px; cursor:pointer; font-weight:700; font-size:0.85rem; color:#1e293b; margin:0;">
                        <input type="radio" name="inst_status_${inst.installment_no}" value="applied" ${isApplied ? 'checked' : ''} ${!data.can_manage ? 'disabled' : ''} style="width:16px; height:16px; accent-color:#2563eb;" onchange="updateInstCardBadge(${inst.installment_no}, true)">
                        <span>Apply</span>
                    </label>
                    <label style="display:inline-flex; align-items:center; gap:8px; cursor:pointer; font-weight:700; font-size:0.85rem; color:#1e293b; margin:0;">
                        <input type="radio" name="inst_status_${inst.installment_no}" value="not_applied" ${!isApplied ? 'checked' : ''} ${!data.can_manage ? 'disabled' : ''} style="width:16px; height:16px; accent-color:#dc2626;" onchange="updateInstCardBadge(${inst.installment_no}, false)">
                        <span>Not Applied</span>
                    </label>
                </div>
            `;
            container.appendChild(card);
        });

        if (targetInstNo) {
            const targetRadio = container.querySelector(`input[name="inst_status_${targetInstNo}"]`);
            if (targetRadio) {
                const targetCard = targetRadio.closest('.late-fine-inst-card');
                if (targetCard) {
                    targetCard.style.borderColor = '#2563eb';
                    targetCard.style.boxShadow = '0 0 0 3px rgba(37, 99, 235, 0.15)';
                    targetCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            }
        }
    };

    if (window.lateFineCache && window.lateFineCache.student_id == studentId) {
        renderData(window.lateFineCache);
    } else {
        loading.style.display = 'block';
        empty.style.display = 'none';
        form.style.display = 'none';
        notice.style.display = 'none';
        container.innerHTML = '';
    }

    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('action', 'get_late_fine_details');
    formData.append('student_id', studentId);

    fetch('{{ route("school.fees.student-wise") }}', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.lateFineCache = data;
            renderData(data);
        } else {
            loading.style.display = 'none';
            empty.style.display = 'block';
        }
    })
    .catch(err => {
        console.error('Late Fine Modal Error:', err);
        loading.style.display = 'none';
        empty.style.display = 'block';
    });
}

function updateInstCardBadge(instNo, isApplied) {
    const badge = document.querySelector(`.status-badge-${instNo}`);
    if (badge) {
        if (isApplied) {
            badge.style.cssText = 'font-size:0.75rem; font-weight:700; padding:3px 10px; border-radius:12px; background:#dcfce7; color:#15803d; border:1px solid #86efac;';
            badge.textContent = 'Current Status: Applied';
        } else {
            badge.style.cssText = 'font-size:0.75rem; font-weight:700; padding:3px 10px; border-radius:12px; background:#fef2f2; color:#b91c1c; border:1px solid #fca5a5;';
            badge.textContent = 'Current Status: Not Applied';
        }
    }
}

function closeLateFineModal() {
    document.getElementById('manageLateFineModal').style.display = 'none';
}

function submitLateFineForm(event) {
    event.preventDefault();
    const btn = document.getElementById('saveLateFineBtn');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Saving...';

    const form = document.getElementById('lateFineManagementForm');
    const studentId = document.getElementById('lateFineStudentId').value;
    const reason = document.getElementById('lateFineReasonInput').value;

    const radios = form.querySelectorAll('input[type="radio"]:checked');
    const installments = [];

    radios.forEach(radio => {
        const name = radio.name;
        const instNo = name.replace('inst_status_', '');
        installments.push({
            installment_no: parseInt(instNo),
            status: radio.value
        });
    });

    const payload = {
        _token: '{{ csrf_token() }}',
        action: 'manage_late_fine',
        student_id: studentId,
        reason: reason,
        installments: installments
    };

    fetch('{{ route("school.fees.student-wise") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.lateFineCache = null;
            if (data.total_fine_formatted !== undefined) {
                const fineBadgeText = document.querySelector('[title*="Late Fine"]');
                if (fineBadgeText) {
                    fineBadgeText.innerHTML = `<i class="fas fa-clock"></i> Late Fine: ${data.total_fine_formatted} <i class="fas fa-sliders-h" style="font-size:0.75rem; opacity:0.8; margin-left:2px;"></i>`;
                }
                const dueBadgeText = document.querySelector('.sw-badge-due');
                if (dueBadgeText && data.effective_due_formatted !== undefined) {
                    dueBadgeText.innerHTML = `Due: ${data.effective_due_formatted}`;
                }
            }
            closeLateFineModal();
            window.location.reload();
        } else {
            alert(data.message || 'Error updating Late Fine settings.');
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    })
    .catch(err => {
        console.error('Error submitting late fine changes:', err);
        alert('An unexpected error occurred while saving changes.');
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
}
</script>

{{-- ══════════════════════════════════════════════════════════════════════
     MANAGE LATE FINE MODAL POPUP HTML
══════════════════════════════════════════════════════════════════════ --}}
<div class="sw-modal-overlay" id="manageLateFineModal" style="display:none; backdrop-filter: blur(4px); transition: all 0.3s ease; z-index:9999;" onclick="if(event.target===this) closeLateFineModal()">
    <div class="sw-modal" style="width:520px; max-height:90vh; overflow-y:auto; border-radius:16px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); border: 1px solid rgba(226, 232, 240, 0.8); background:#ffffff; padding:24px;">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:14px; border-bottom: 1px solid #f1f5f9; padding-bottom:12px;">
            <div>
                <h3 style="margin:0; font-size:1.2rem; font-weight:800; color:var(--sw-blue); display:flex; align-items:center; gap:8px;">
                    <i class="fas fa-clock" style="color:#c2410c;"></i> Manage Late Fine
                </h3>
                <p style="margin:4px 0 0 0; font-size:0.83rem; color:#64748b; font-weight:500;">
                    Select the installment(s) for which you want to change the Late Fine status.
                </p>
            </div>
            <button type="button" onclick="closeLateFineModal()" style="background:none; border:none; color:#94a3b8; font-size:1.2rem; cursor:pointer; padding:4px 8px; border-radius:6px; transition:all 0.2s ease;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div id="lateFineReadOnlyNotice" style="display:none; background:#fffbe8; border:1px solid #fef08a; border-radius:10px; padding:10px 12px; margin-bottom:14px; color:#854d0e; font-size:0.82rem; font-weight:600;">
            <i class="fas fa-lock" style="margin-right:6px;"></i> View Only Mode: You do not have permission to change Late Fine settings.
        </div>

        <div id="lateFineLoadingState" style="text-align:center; padding:35px 10px; color:#64748b;">
            <i class="fas fa-circle-notch fa-spin" style="font-size:2rem; color:var(--sw-blue); margin-bottom:12px;"></i>
            <p style="font-size:0.88rem; font-weight:600; margin:0;">Loading Late Fine details...</p>
        </div>

        <div id="lateFineEmptyState" style="display:none; text-align:center; padding:35px 10px; color:#64748b;">
            <i class="fas fa-info-circle" style="font-size:2rem; color:#94a3b8; margin-bottom:10px;"></i>
            <p style="font-size:0.9rem; font-weight:700; margin:0 0 4px 0;">No Late Fine Applicable</p>
            <p style="font-size:0.82rem; margin:0;">There are currently no overdue installments with late fines calculated for this student.</p>
        </div>

        <form id="lateFineManagementForm" onsubmit="submitLateFineForm(event)" style="display:none;">
            @csrf
            <input type="hidden" name="action" value="manage_late_fine">
            <input type="hidden" name="student_id" id="lateFineStudentId" value="{{ $viewStudent ? $viewStudent->id : '' }}">

            <div id="lateFineInstallmentsContainer" style="display:flex; flex-direction:column; gap:12px; margin-bottom:16px;">
                {{-- Dynamic Installment Cards --}}
            </div>

            <div class="sw-modal-field" style="margin-bottom:18px;">
                <label style="font-weight:700; font-size:0.82rem; color:#334155;">Reason for Change (Optional)</label>
                <input type="text" name="reason" id="lateFineReasonInput" placeholder="e.g. Approved by Principal / Special waiver" style="border-radius:8px; border:1px solid #cbd5e1; padding:8px 12px; width:100%; font-size:0.88rem;">
            </div>

            <div class="sw-modal-actions" style="display:flex; gap:10px; justify-content:flex-end; border-top:1px solid #f1f5f9; padding-top:14px; margin-top:10px;">
                <button type="button" class="sw-modal-cancel" onclick="closeLateFineModal()" style="border-radius:8px; font-weight:700; background:#f1f5f9; color:#475569; border:none; padding:8px 18px; cursor:pointer;">Cancel</button>
                <button type="submit" class="sw-modal-submit" id="saveLateFineBtn" style="border-radius:8px; font-weight:800; background:linear-gradient(135deg, #1e40af 0%, #2563eb 100%); color:#fff; border:none; padding:8px 22px; cursor:pointer; display:inline-flex; align-items:center; gap:6px;">
                    <i class="fas fa-check-circle"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
