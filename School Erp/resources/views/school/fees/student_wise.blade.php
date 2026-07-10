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
.sw-table thead th:first-child { width: 48px; }
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
    padding: 0 28px;
    display: flex;
    align-items: center;
    gap: 0;
    overflow-x: auto;
}
.sw-tab {
    padding: 13px 18px;
    font-size: 0.9rem;
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
}
.sw-tab.active {
    color: var(--sw-blue2);
    border-bottom-color: var(--sw-blue2);
}
.sw-tab:hover { color: var(--sw-blue2); }

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

@media (max-width: 768px) {
    .sw-filters { padding: 12px 14px; }
    .sw-info-grid { grid-template-columns: 1fr; }
    .sw-info-col:first-child { border-right: none; border-bottom: 1px solid var(--sw-border); }
    .sw-fee-record-wrap { margin: 16px 14px; }
    .sw-header { padding: 14px; }
    .sw-detail-topbar { padding: 12px 14px; }
    .sw-tabs { padding: 0 14px; }
    .sw-summary-bar { display: none; }
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
</style>

<div class="sw-wrap">

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
    $totalPaid       = $studentFees->sum('paid_amount');
    $totalRefunded   = $refunds->sum('amount');
    // Due = sum of outstanding dues, excluding fully refunded fees
    $totalDue = $studentFees->reduce(function($carry, $sf) {
        if ($sf->status === 'refunded') return $carry; // skip refunded fees
        return $carry + max(0, $sf->amount - $sf->instant_discount_amount - $sf->paid_amount);
    }, 0);
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

    <div style="font-size:.82rem;font-weight:700;color:#64748b;margin-left:8px;">
        Select Student:
        <select onchange="location.href='{{ route('school.fees.student-wise') }}?view_student='+this.value+'&academic_session_id={{ $selectedSession->id }}'"
                style="border:1.5px solid var(--sw-border);border-radius:8px;padding:6px 10px;font-size:.88rem;font-weight:600;color:var(--sw-dark);background:#f8faff;">
            @foreach($studentsWithFees as $s)
                <option value="{{ $s->id }}" {{ $s->id == $viewStudent->id ? 'selected' : '' }}>{{ $s->full_name }}</option>
            @endforeach
        </select>
    </div>

    <div class="sw-topbar-right">
        <button class="sw-btn-outline">
            <i class="fas fa-list-ul"></i> Show Logs
        </button>
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
        Siblings (0)
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
        <div class="sw-badge-paid" style="background:#f0fdf4; color:#166534; border:1px solid #bbf7d0; font-weight:700; padding:6px 14px; border-radius:20px; font-size:0.85rem;">
            Paid: ₹{{ number_format($totalPaid, 0) }}
        </div>
        <div class="sw-badge-refunded" style="background:#f3e8ff; color:#6b21a8; border:1px solid #e9d5ff; font-weight:700; padding:6px 14px; border-radius:20px; font-size:0.85rem;">
            Refunded: ₹{{ number_format($totalRefunded, 0) }}
        </div>
        <div class="sw-badge-due" style="background:#fef2f2; color:#991b1b; border:1px solid #fca5a5; font-weight:700; padding:6px 14px; border-radius:20px; font-size:0.85rem;">
            Due: ₹{{ number_format($totalDue, 0) }} (till date) | ₹{{ number_format($totalDue, 0) }} (Full Year)
        </div>
    </div>
</div>

{{-- ── FEE RECORD TAB ── --}}
@if($activeTab == 'fee_record')
<div class="sw-fee-record-wrap">
    <div class="sw-fee-record-header">
        <h3>Fee Record</h3>
        <button class="sw-btn-add-discount" onclick="document.getElementById('discountModal').style.display='flex'">
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

    <div class="sw-fee-body">
        <div class="sw-fee-details-header">Fee Details</div>

        @php
            $groupedFees = $studentFees->groupBy(function($fee) {
                return $fee->installment_no ?? 1;
            })->sortKeys();
        @endphp

        @forelse($groupedFees as $instNo => $instFees)
        @php
            $instLabel = 'Installment ' . $instNo;
            $groupTotal = $instFees->sum('amount');
            $groupDiscount = $instFees->sum('instant_discount_amount');
            $groupPaid  = $instFees->sum('paid_amount');
            // Exclude refunded fees from the due calculation
            $groupDue   = $instFees->reduce(function($carry, $f) {
                if ($f->status === 'refunded') return $carry;
                return $carry + max(0, $f->amount - $f->instant_discount_amount - $f->paid_amount);
            }, 0);
            
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
                <div>
                    <div class="sw-installment-title">
                        <h4>{{ $instLabel }}</h4>
                        <span class="sw-inst-badge {{ $badgeClass }}" style="{{ $badgeStyle }}">
                            <i class="fas {{ $badgeIcon }}"></i> {{ $badgeLabel }}
                        </span>
                    </div>
                    <div class="sw-installment-amounts">
                        Total: 
                        @if($groupDiscount > 0)
                            <span style="text-decoration: line-through; color: #94a3b8; margin-right: 4px;">₹{{ number_format($groupTotal, 0) }}</span>
                            <span class="blue" style="font-weight: 800; color: #2563eb;">₹{{ number_format($groupTotal - $groupDiscount, 0) }}</span>
                        @else
                            <span class="blue">₹{{ number_format($groupTotal, 0) }}</span>
                        @endif
                        &nbsp; Due: <span class="red">₹{{ number_format($groupDue, 0) }}</span>
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
                            onclick="event.stopPropagation(); openMarkPaid({{ $viewStudent->id }}, {{ $instNo }}, {{ $groupDue }}, '{{ $instLabel }}')"
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
                <table style="width:100%;font-size:.87rem;border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f0f9ff;color:var(--sw-blue);font-weight:700;font-size:.8rem;text-transform:uppercase;">
                            <th style="padding:8px 10px;text-align:left;">Fee Head</th>
                            <th style="padding:8px 10px;text-align:right;">Amount</th>
                            <th style="padding:8px 10px;text-align:right;">Paid</th>
                            <th style="padding:8px 10px;text-align:right;">Due</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $firstFee = $instFees->first(); @endphp
                        <tr style="border-bottom:1px solid #e2e8f0; {{ $instFees->contains('status', 'refunded') ? 'background:#faf5ff;' : '' }}">
                            <td style="padding:9px 10px;font-weight:600;">
                                Installment {{ $instNo }}
                                @if($instFees->contains('status', 'refunded'))
                                    <span style="font-size:0.7rem;background:#f3e8ff;color:#7e22ce;border:1px solid #e9d5ff;border-radius:10px;padding:1px 7px;margin-left:6px;font-weight:700;"><i class="fas fa-undo" style="font-size:0.65rem;"></i> Refunded</span>
                                @endif
                            </td>
                            <td style="padding:9px 10px;text-align:right;font-weight:700;">
                                @if($groupDiscount > 0)
                                    <span style="text-decoration: line-through; color: #94a3b8; font-size: 0.8rem; margin-right: 4px;">₹{{ number_format($groupTotal, 0) }}</span>
                                    <span>₹{{ number_format($groupTotal - $groupDiscount, 0) }}</span>
                                @else
                                    <span>₹{{ number_format($groupTotal, 0) }}</span>
                                @endif
                            </td>
                            <td style="padding:9px 10px;text-align:right;font-weight:700;color:var(--sw-green);">₹{{ number_format($groupPaid, 0) }}</td>
                            <td style="padding:9px 10px;text-align:right;font-weight:700;color:var(--sw-red);">
                                ₹{{ number_format($groupDue, 0) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
                @if($firstFee && $firstFee->due_date)
                <div style="margin-top:10px;font-size:.82rem;color:#64748b;font-weight:600;">
                    <i class="fas fa-calendar"></i> Due Date: {{ \Carbon\Carbon::parse($firstFee->due_date)->format('d M Y') }}
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="sw-empty">
            <i class="fas fa-inbox"></i>
            <p>No fee records found for this student.</p>
        </div>
        @endforelse
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
                       data-label="{{ optional($sf->component)->component_name ?? 'Fee' }} - Inst {{ $sf->installment_no }}"
                       style="width:18px; height:18px; accent-color:var(--sw-blue2); cursor:pointer;">
                <div style="flex-grow:1;">
                    <div style="font-weight:700; font-size:.92rem; color:var(--sw-dark);">
                         {{ optional($sf->component)->component_name ?? 'Fee Component' }} 
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
        $groupedRefunds = $refunds->groupBy('slip_no');
    @endphp
    @if($groupedRefunds->isNotEmpty())
    <div class="sw-fee-body" style="border:1.5px solid var(--sw-border); border-radius:12px; padding:20px; background:#fff; margin-top:20px;">
        <h3 style="font-size:1.1rem; font-weight:800; color:var(--sw-blue); margin-bottom:15px; border-bottom:1.5px solid var(--sw-border); padding-bottom:8px; margin-top:0; display:flex; align-items:center; gap:8px;">
            <i class="fas fa-history"></i> Refund History Logs
        </h3>
        <table class="sw-table">
            <thead>
                <tr style="background:#f0f9ff; color:var(--sw-blue); font-weight:700; font-size:.8rem; text-transform:uppercase;">
                    <th style="padding:10px; text-align:left;">Slip No.</th>
                    <th style="padding:10px; text-align:left;">Date</th>
                    <th style="padding:10px; text-align:left;">Components Refunded</th>
                    <th style="padding:10px; text-align:left;">Reason</th>
                    <th style="padding:10px; text-align:left;">Payment Mode</th>
                    <th style="padding:10px; text-align:right;">Amount Refunded</th>
                    <th style="padding:10px; text-align:center;">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($groupedRefunds as $slipNo => $refItems)
                @php
                    $first = $refItems->first();
                    $totalRefAmt = $refItems->sum('amount');
                    
                    // Lookup the FeeInvoice record for this refund slip
                    $refInvoiceRec = \App\Models\FeeInvoice::where('school_id', auth()->user()->school_id)
                        ->where('student_id', $viewStudent->id)
                        ->where('type', 'refund')
                        ->get()
                        ->filter(function($inv) use ($slipNo, $refItems, $totalRefAmt) {
                            $details = json_decode($inv->payment_details, true);
                            if (is_array($details)) {
                                if (isset($details['slip_no']) && $details['slip_no'] === $slipNo) {
                                    return true;
                                }
                                $components = $details['components'] ?? $details;
                                if (is_array($components)) {
                                    $detailFeeIds = array_column($components, 'student_fee_id');
                                    $refFeeIds = $refItems->pluck('student_fee_id')->toArray();
                                    if (!empty(array_intersect($detailFeeIds, $refFeeIds))) {
                                        return $inv->payment_date === $refItems->first()->refund_date || abs($inv->amount - $totalRefAmt) < 0.01;
                                    }
                                }
                            }
                            return $inv->invoice_number === $slipNo;
                        })
                        ->first();

                    // Check if this refund has already been cancelled
                    $refAlreadyCancelled = false;
                    if ($refInvoiceRec) {
                        $refAlreadyCancelled = \App\Models\FeeInvoice::where('related_invoice_id', $refInvoiceRec->id)->exists();
                    }
                @endphp
                <tr style="border-bottom:1px solid #e2e8f0; font-size: 0.88rem;">
                    <td style="padding:10px; font-weight:700; color:var(--sw-blue2);">{{ $slipNo }}</td>
                    <td style="padding:10px; font-weight:600;">{{ \Carbon\Carbon::parse($first->refund_date)->format('d M Y') }}</td>
                    <td style="padding:10px;">
                        @foreach($refItems as $item)
                            @php
                                // Clean up category description
                                $desc = $item->reason;
                                if (strpos($desc, ' (Refunded: ') !== false) {
                                    $desc = str_replace(' (Refunded: ', '', strstr($desc, ' (Refunded: '));
                                    $desc = rtrim($desc, ')');
                                } else {
                                    $desc = 'Fee component';
                                }
                            @endphp
                            <div style="font-weight:600; color:var(--sw-dark); margin-bottom:2px;">
                                • {{ $desc }} (₹{{ number_format($item->amount, 0) }})
                            </div>
                        @endforeach
                    </td>
                    <td style="padding:10px; color:#475569;">{{ explode(' (Refunded:', $first->reason)[0] }}</td>
                    <td style="padding:10px;">
                        <span style="text-transform: uppercase; font-size:0.7rem; font-weight:700; background:#f1f5f9; padding:2px 8px; border-radius:4px; color:#475569;">
                            {{ str_replace('_', ' ', $first->payment_mode) }}
                        </span>
                        @if($first->bank_name)
                            <div style="font-size:0.72rem; color:#64748b; margin-top:3px; font-weight:600;">{{ $first->bank_name }}</div>
                        @endif
                    </td>
                    <td style="padding:10px; text-align:right; font-weight:800; color:var(--sw-red); font-size:0.95rem;">
                        ₹{{ number_format($totalRefAmt, 2) }}
                    </td>
                    <td style="padding:10px; text-align:center; white-space:nowrap;">
                        {{-- Print button --}}
                        <button type="button" onclick="window.open('{{ route('school.fees.print-slip', ['type' => 'refund', 'number' => $refInvoiceRec ? $refInvoiceRec->invoice_number : $slipNo]) }}', '_blank', 'width=950,height=750')" style="background:none; border:none; color:var(--sw-blue); cursor:pointer; font-size:1.15rem; padding:4px 6px;" title="Print Refund Invoice">
                            <i class="fas fa-print"></i>
                        </button>
                        {{-- Cancel Refund button — only if not already cancelled --}}
                        @if(!$refAlreadyCancelled)
                            <button type="button"
                                    onclick="openCancelReasonModal('{{ $refInvoiceRec ? $refInvoiceRec->invoice_number : $slipNo }}', {{ $refInvoiceRec ? ($refInvoiceRec->installment_no ?? 1) : 1 }}, {{ $viewStudent->id }}, 'refund')"
                                    style="background:none; border:1.5px solid #fca5a5; border-radius:6px; color:#dc2626; cursor:pointer; font-size:0.8rem; font-weight:700; padding:3px 8px; margin-left:4px;"
                                    title="Cancel this refund">
                                ✕
                            </button>
                        @elseif($refAlreadyCancelled)
                            <span style="font-size:0.7rem; font-weight:700; color:#9ca3af; background:#f1f5f9; border-radius:4px; padding:2px 7px; margin-left:4px;">Cancelled</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
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
<div class="sw-simple-panel">
    <i class="fas fa-users"></i>
    <p>No siblings linked to this student.</p>
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
                @forelse($studentFees as $sf)
                @php
                    $sfEffectiveDue = $sf->status === 'refunded' ? 0 : max(0, $sf->amount - $sf->instant_discount_amount - $sf->paid_amount);
                @endphp
                <tr>
                    <td><strong>{{ optional($sf->component)->component_name ?? 'Fee' }}</strong></td>
                    <td>{{ optional($sf->category)->name ?? '-' }}</td>
                    <td>Installment {{ $sf->installment_no ?? 1 }}</td>
                    <td class="sw-amount blue">
                        @if($sf->instant_discount_amount > 0)
                            <span style="text-decoration: line-through; color: #94a3b8; font-size: 0.8rem; margin-right: 4px;">₹{{ number_format($sf->amount, 0) }}</span>
                            <span>₹{{ number_format($sf->amount - $sf->instant_discount_amount, 0) }}</span>
                        @else
                            <span>₹{{ number_format($sf->amount, 0) }}</span>
                        @endif
                    </td>
                    <td class="sw-amount" style="color:#b45309;">
                        @if($sf->instant_discount_amount > 0)
                            ₹{{ number_format($sf->instant_discount_amount, 0) }}
                        @else
                            <span style="color:#94a3b8;">—</span>
                        @endif
                    </td>
                    <td class="sw-amount green">₹{{ number_format($sf->paid_amount, 0) }}</td>
                    <td class="sw-amount red">₹{{ number_format($sfEffectiveDue, 0) }}</td>
                    <td>
                        @if($sf->status === 'paid')
                            <span class="sw-inst-badge paid"><i class="fas fa-check"></i> Paid</span>
                        @elseif($sf->status === 'partially_paid')
                            <span class="sw-inst-badge partial"><i class="fas fa-circle-half-stroke"></i> Partial</span>
                        @elseif($sf->status === 'refunded')
                            <span class="sw-inst-badge pending" style="background:#f3e8ff; color:#7e22ce; border-color:#e9d5ff;"><i class="fas fa-undo"></i> Refunded</span>
                        @else
                            <span class="sw-inst-badge pending"><i class="fas fa-clock"></i> Pending</span>
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

    {{-- Refund Details Section --}}
    @if($refunds->isNotEmpty())
    <div class="sw-fee-body" style="border-radius:12px; border:1.5px solid #e9d5ff; margin-top:20px;">
        <div style="font-size:1.05rem;font-weight:800;color:#7e22ce;padding:14px 18px 10px;border-bottom:1.5px solid #e9d5ff;background:#faf5ff;border-radius:12px 12px 0 0;">
            <i class="fas fa-undo" style="margin-right:6px;"></i> Refund History
        </div>
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
                <th style="white-space:nowrap;">Receivable after discount</th>
                <th style="white-space:nowrap;">Paid Till Date</th>
                <th style="white-space:nowrap;">Fee Due</th>
                <th style="white-space:nowrap;font-size:.72rem;">Total Due<br>(All Yrs)</th>
                <th style="white-space:nowrap;">Fee Visibility</th>
                <th style="white-space:nowrap;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($studentsWithFees as $idx => $student)
            @php
                $fees        = $student->studentFees ?? collect();
                
                // Session fees filter
                $sessionFees = $fees->filter(function($fee) use ($selectedSession) {
                    $schedule = $fee->feeSchedule ?? \App\Models\FeeSchedule::find($fee->fee_schedule_id);
                    return $schedule && $schedule->academic_session_id == $selectedSession->id;
                });
                $receivable  = $sessionFees->sum('amount') - $sessionFees->sum('instant_discount_amount');
                $paid        = $sessionFees->sum('paid_amount');
                $due         = max(0, $receivable - $paid);

                // Total dues across all academic years
                $totalReceivable = $fees->sum('amount') - $fees->sum('instant_discount_amount');
                $totalPaid       = $fees->sum('paid_amount');
                $totalDue        = max(0, $totalReceivable - $totalPaid);

                $initials    = strtoupper(substr($student->first_name, 0, 1));

                // Fee schedule: try to match from schedules list via class (supports JSON and normalizations)
                $schedName = '';
                foreach ($schedules as $sch) {
                    $classesList = json_decode($sch->classes, true);
                    if (!is_array($classesList)) {
                        $classesList = array_map('trim', explode(',', $sch->classes ?? ''));
                    } else {
                        $classesList = array_map('trim', $classesList);
                    }
                    
                    $studentClassName = optional($student->class)->name;
                    if ($studentClassName) {
                        $studClassNorm = strtolower(str_replace(' ', '', $studentClassName));
                        foreach ($classesList as $c) {
                            $cNorm = strtolower(str_replace(' ', '', $c));
                            if ($studClassNorm === $cNorm || 
                                (preg_replace('/[^0-9]/', '', $studClassNorm) === preg_replace('/[^0-9]/', '', $cNorm) && preg_replace('/[^0-9]/', '', $studClassNorm) !== '') ||
                                ($cNorm !== '' && (stripos($studClassNorm, $cNorm) !== false || stripos($cNorm, $studClassNorm) !== false))) {
                                $schedName = $sch->name;
                                break 2;
                            }
                        }
                    }
                }


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
                <td><span class="sw-amount blue">₹{{ number_format($receivable, 0) }}</span></td>
                <td><span class="sw-amount green">₹{{ number_format($paid, 0) }}</span></td>
                <td><span class="sw-amount {{ $due > 0 ? 'red' : 'green' }}">₹{{ number_format($due, 0) }}</span></td>
                <td><span class="sw-amount {{ $totalDue > 0 ? 'red' : 'green' }}">₹{{ number_format($totalDue, 0) }}</span></td>
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
                <input type="checkbox">
                <span class="sw-toggle-slider"></span>
            </label>
            Show Deactivated Students
        </label>
        <label class="sw-footer-toggle">
            <label class="sw-toggle" style="width:40px;height:22px;">
                <input type="checkbox">
                <span class="sw-toggle-slider"></span>
            </label>
            Show Deleted Students
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
        @for($p = 1; $p <= $studentsWithFees->lastPage(); $p++)
            <a href="{{ $studentsWithFees->url($p) }}" class="sw-page-btn {{ $p == $studentsWithFees->currentPage() ? 'active' : '' }}">{{ $p }}</a>
        @endfor
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
     MARK PAID MODAL
══════════════════════════════════════════════════════════════════════ --}}
<div class="sw-modal-overlay" id="markPaidModal" style="display:none;" onclick="if(event.target===this) closeMarkPaid()">
    <div class="sw-modal" style="width:480px; max-height:90vh; overflow-y:auto;">
        <h3><i class="fas fa-indian-rupee-sign" style="color:var(--sw-blue2);"></i> Record Payment</h3>
        <form method="POST" action="{{ route('school.fees.student-wise') }}" id="paymentForm">
            @csrf
            <input type="hidden" name="student_id" id="modalStudentId">
            <input type="hidden" name="installment_no" id="modalInstallmentNo">
            
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
                    <input type="number" name="amount_paid" id="modalAmtPaid" step="0.01" min="0.01" required placeholder="Enter amount">
                </div>
            </div>

            <div style="display:flex; gap:10px;">
                <div class="sw-modal-field" style="flex:1;">
                    <label>Entry Date</label>
                    <input type="date" name="entry_date" id="modalEntryDate" readonly value="{{ now()->format('Y-m-d') }}" style="background:#f8faff;">
                </div>
                <div class="sw-modal-field" style="flex:1;">
                    <label>Receipt Date</label>
                    <input type="date" name="receipt_date" id="modalReceiptDate" required value="{{ now()->format('Y-m-d') }}">
                </div>
            </div>

            <div class="sw-modal-field">
                <label>Receipt No</label>
                <input type="text" name="receipt_no" id="modalReceiptNo" required placeholder="REC-XXXXXX">
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

            <div class="sw-modal-actions">
                <button type="button" class="sw-modal-cancel" onclick="closeMarkPaid()">Cancel</button>
                <button type="submit" class="sw-modal-submit"><i class="fas fa-check"></i> Collect Payment</button>
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
            <input type="hidden" name="student_id" value="{{ $viewStudent->id ?? '' }}">
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
                    <select name="installment_no" style="width:100%; height:38px; border:1px solid var(--sw-border); border-radius:6px; padding:0 10px; background:#fff; font-size:.9rem; outline:none;">
                        <option value="">All Installments</option>
                        @foreach($studentInstNos as $instNo)
                            <option value="{{ $instNo }}">Installment {{ $instNo }}</option>
                        @endforeach
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
                        onclick="window.open('{{ route('school.fees.print-slip', ['type' => $popupType, 'number' => $popupNo]) }}' + ('{{ $popupType }}' === 'invoice' || '{{ $popupType }}' === 'payment' ? '?student_id={{ $viewStudent->id ?? '' }}' : ''), '_blank', 'width=950,height=750')" 
                        style="flex:1; background:#0284c7; color:#fff; padding:11px; border-radius:9px; font-size:0.93rem; font-weight:700; cursor:pointer; border:none; display:flex; align-items:center; justify-content:center; gap:6px;">
                    <i class="fas fa-print"></i> Print Slip
                </button>
                <button type="button" 
                        onclick="window.open('{{ route('school.fees.print-slip', ['type' => $popupType, 'number' => $popupNo]) }}' + ('{{ $popupType }}' === 'invoice' || '{{ $popupType }}' === 'payment' ? '?student_id={{ $viewStudent->id ?? '' }}&copy=student' : '?copy=student'), '_blank', 'width=950,height=750')" 
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
</div>

</div>{{-- .sw-wrap --}}

<script>
function toggleAll(master) {
    document.querySelectorAll('.row-check').forEach(cb => cb.checked = master.checked);
    const th = document.getElementById('thCheck');
    const sel = document.getElementById('selectAll');
    if (th) th.checked = master.checked;
    if (sel) sel.checked = master.checked;
}

function toggleInst(el) {
    el.classList.toggle('open');
}

function openMarkPaid(studentId, installmentNo, dueAmt, label) {
    document.getElementById('modalStudentId').value = studentId;
    document.getElementById('modalInstallmentNo').value = installmentNo;
    document.getElementById('modalInstLabel').value = label;
    document.getElementById('modalDueAmt').value  = '₹' + dueAmt.toFixed(2);
    document.getElementById('modalAmtPaid').value = dueAmt.toFixed(2);
    
    // Reset discount fields
    document.getElementById('modalDiscountAmt').value = '0';
    document.getElementById('modalDiscountType').value = 'flat';
    document.getElementById('netAmtDisplay').textContent = '';
    document.getElementById('discountAmtLabel').textContent = 'Discount Amount (₹)';

    // Generate a unique Receipt No
    const randReceiptNo = 'REC-' + Math.floor(100000 + Math.random() * 900000);
    document.getElementById('modalReceiptNo').value = randReceiptNo;
    
    // Reset payment mode
    const modeSelect = document.getElementById('modalPaymentMode');
    modeSelect.value = 'cash';
    togglePaymentModeFields('cash');

    document.getElementById('markPaidModal').style.display = 'flex';
}

function recalcNetAmount() {
    const rawAmt = parseFloat(document.getElementById('modalAmtPaid').value || 0);
    const discAmt = parseFloat(document.getElementById('modalDiscountAmt').value || 0);
    const discType = document.getElementById('modalDiscountType').value;
    const label = document.getElementById('discountAmtLabel');
    const display = document.getElementById('netAmtDisplay');

    label.textContent = discType === 'percentage' ? 'Discount Percentage (%)' : 'Discount Amount (₹)';

    let netDiscount = 0;
    if (discAmt > 0) {
        if (discType === 'percentage') {
            netDiscount = Math.round(rawAmt * discAmt / 100 * 100) / 100;
        } else {
            netDiscount = Math.min(discAmt, rawAmt);
        }
    }
    const netAmount = rawAmt - netDiscount;
    if (netDiscount > 0) {
        display.innerHTML = `<i class="fas fa-check-circle" style="color:#16a34a"></i> Discount: ₹${netDiscount.toFixed(2)} &nbsp;|&nbsp; Net Payable: <strong>₹${netAmount.toFixed(2)}</strong>`;
    } else {
        display.textContent = '';
    }
}

function togglePaymentModeFields(mode) {
    const chequeFields = document.getElementById('chequeFields');
    const chequeInputs = chequeFields.querySelectorAll('input');
    const txLabel = document.getElementById('transactionIdLabel');
    const txInput = document.getElementById('modalTransactionId');

    if (mode === 'cheque') {
        chequeFields.style.display = 'block';
        chequeInputs.forEach(input => input.setAttribute('required', 'required'));
        txLabel.textContent = 'Cheque Number';
        txInput.placeholder = 'e.g. 123456';
        txInput.setAttribute('required', 'required');
    } else {
        chequeFields.style.display = 'none';
        chequeInputs.forEach(input => input.removeAttribute('required'));
        txLabel.textContent = 'Transaction ID / Reference (Optional)';
        txInput.placeholder = 'e.g. TXN9876543210';
        txInput.removeAttribute('required');
    }
}

function closeMarkPaid() {
    document.getElementById('markPaidModal').style.display = 'none';
}

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
            bulkToggleVisibility(false);
        });
        showBtn.addEventListener('click', function(e) {
            e.preventDefault();
            bulkToggleVisibility(true);
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

// Print behavior is now handled by the interactive successPopupModal dialog
</script>
@endsection
