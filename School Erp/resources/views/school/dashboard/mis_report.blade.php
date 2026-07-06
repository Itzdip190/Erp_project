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

/* ── TOGGLE BUTTON (looks like the span link) ─────────────── */
button.mis-alb-more-link {
    background: none;
    border: none;
    padding: 0;
    cursor: pointer;
    font-family: inherit;
}
/* ── Anchor alert cards retain their text colors ─────────── */
a.mis-alert-card .mis-alert-title { color: #1e293b; }
a.mis-alert-card .mis-alert-badge { color: #fff; }
/* ── MOBILE & TABLET RESPONSIVENESS ────────────────────────── */
@media (max-width: 1024px) {
    .mis-kpi-grid { grid-template-columns: repeat(2, 1fr); }
    .mis-alerts-grid { grid-template-columns: repeat(2, 1fr); }
    .mis-three-col { grid-template-columns: 1fr; }
}
@media (max-width: 768px) {
    .mis-header { flex-direction: column; align-items: stretch; gap: 16px; padding: 20px; }
    .mis-header-right { flex-direction: column; width: 100%; }
    .mis-date-form { flex-direction: column; width: 100%; }
    .mis-date-input, .mis-go-btn, .mis-print-btn { width: 100%; text-align: center; justify-content: center; }
    .mis-kpi-grid { grid-template-columns: 1fr; }
    .mis-alerts-grid { grid-template-columns: 1fr; }
    .mis-two-col { grid-template-columns: 1fr; }
    .mis-follow-grid { grid-template-columns: 1fr; }
    .mis-att-row { flex-wrap: wrap; }
    .mis-att-box { min-width: calc(50% - 4px); }
    .mis-mode-grid { grid-template-columns: 1fr; }
    .mis-def-aging-row { flex-wrap: wrap; }
    .mis-def-aging-box { min-width: calc(50% - 3px); }
}
@media (max-width: 480px) {
    .mis-att-box { min-width: 100%; }
    .mis-def-aging-box { min-width: 100%; }
    .mis-kpi-card { padding: 16px; }
    .mis-detail-card { padding: 16px; }
}

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

/* ══════════════════════════════════════════════════════════════
   DAILY MIS REPORT — DARK MODE THEME OVERRIDES
   ══════════════════════════════════════════════════════════════ */
body.dark-mode .mis-page {
    background: #0b0f19 !important;
    color: #f8fafc !important;
}
body.dark-mode .mis-header {
    background: linear-gradient(135deg, #111827 0%, #1e1b4b 50%, #312e81 100%) !important;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.4) !important;
    border: 1px solid rgba(129,140,248,0.2) !important;
}
body.dark-mode .mis-header h1, 
body.dark-mode .mis-header p {
    color: #f8fafc !important;
}
body.dark-mode .mis-alert-card,
body.dark-mode .mis-col-panel,
body.dark-mode .mis-detail-card,
body.dark-mode .mis-follow-card {
    background: rgba(17, 24, 39, 0.6) !important;
    backdrop-filter: blur(10px) !important;
    -webkit-backdrop-filter: blur(10px) !important;
    border: 1px solid rgba(255, 255, 255, 0.08) !important;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3) !important;
    color: #f8fafc !important;
}
body.dark-mode .mis-kpi-card {
    background: rgba(17, 24, 39, 0.45) !important;
    backdrop-filter: blur(12px) !important;
    -webkit-backdrop-filter: blur(12px) !important;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3) !important;
    color: #f8fafc !important;
}
/* 1. Daily Revenue (Green Theme) */
body.dark-mode .mis-kpi-card.k-rev {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.06) 0%, rgba(16, 185, 129, 0.02) 100%) !important;
    border: 1px solid rgba(16, 185, 129, 0.3) !important;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3), inset 0 0 12px rgba(16, 185, 129, 0.05) !important;
}
body.dark-mode .mis-kpi-card.k-rev:hover {
    border-color: rgba(16, 185, 129, 0.75) !important;
    box-shadow: 0 12px 24px rgba(16, 185, 129, 0.15), inset 0 0 15px rgba(16, 185, 129, 0.1) !important;
}

/* 2. Student Attendance (Orange Theme) */
body.dark-mode .mis-kpi-card.k-stu {
    background: linear-gradient(135deg, rgba(249, 115, 22, 0.06) 0%, rgba(249, 115, 22, 0.02) 100%) !important;
    border: 1px solid rgba(249, 115, 22, 0.3) !important;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3), inset 0 0 12px rgba(249, 115, 22, 0.05) !important;
}
body.dark-mode .mis-kpi-card.k-stu:hover {
    border-color: rgba(249, 115, 22, 0.75) !important;
    box-shadow: 0 12px 24px rgba(249, 115, 22, 0.15), inset 0 0 15px rgba(249, 115, 22, 0.1) !important;
}

/* 3. Staff Attendance (Pink Theme) */
body.dark-mode .mis-kpi-card.k-stf {
    background: linear-gradient(135deg, rgba(236, 72, 153, 0.06) 0%, rgba(236, 72, 153, 0.02) 100%) !important;
    border: 1px solid rgba(236, 72, 153, 0.3) !important;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3), inset 0 0 12px rgba(236, 72, 153, 0.05) !important;
}
body.dark-mode .mis-kpi-card.k-stf:hover {
    border-color: rgba(236, 72, 153, 0.75) !important;
    box-shadow: 0 12px 24px rgba(236, 72, 153, 0.15), inset 0 0 15px rgba(236, 72, 153, 0.1) !important;
}

/* 4. New Admissions (Blue Theme) */
body.dark-mode .mis-kpi-card.k-adm {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.06) 0%, rgba(59, 130, 246, 0.02) 100%) !important;
    border: 1px solid rgba(59, 130, 246, 0.3) !important;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3), inset 0 0 12px rgba(59, 130, 246, 0.05) !important;
}
body.dark-mode .mis-kpi-card.k-adm:hover {
    border-color: rgba(59, 130, 246, 0.75) !important;
    box-shadow: 0 12px 24px rgba(59, 130, 246, 0.15), inset 0 0 15px rgba(59, 130, 246, 0.1) !important;
}
body.dark-mode .mis-alert-card:hover,
body.dark-mode .mis-detail-card:hover,
body.dark-mode .mis-follow-card:hover {
    border-color: #374151 !important;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.5) !important;
}
body.dark-mode .mis-kpi-value,
body.dark-mode .mis-alert-title,
body.dark-mode .mis-col-hdr,
body.dark-mode .mis-detail-title,
body.dark-mode .mis-follow-hdr,
body.dark-mode .mis-metric-row strong,
body.dark-mode .mis-fc-right strong,
body.dark-mode .mis-att-box-val,
body.dark-mode .mis-mode-card .val,
body.dark-mode a.mis-alert-card .mis-alert-title {
    color: #f8fafc !important;
}
body.dark-mode .mis-kpi-label,
body.dark-mode .mis-kpi-sub,
body.dark-mode .mis-alert-desc,
body.dark-mode .mis-metric-row span,
body.dark-mode .mis-section-label,
body.dark-mode .mis-detail-meta,
body.dark-mode .mis-att-sub-label,
body.dark-mode .mis-att-box-lbl,
body.dark-mode .mis-mode-card .lbl,
body.dark-mode .mis-def-aging-hdr,
body.dark-mode .mis-fc-left span,
body.dark-mode .mis-fc-right {
    color: #cbd5e1 !important;
}
body.dark-mode .mis-col-hdr,
body.dark-mode .mis-follow-hdr,
body.dark-mode .mis-fee-card-footer {
    border-bottom-color: #1e293b !important;
    border-top-color: #1e293b !important;
}
body.dark-mode .mis-metric-row {
    border-bottom: 1px dashed #1e293b !important;
}
body.dark-mode .mis-profit-box,
body.dark-mode .mis-mode-card.total-mode {
    background: rgba(37, 99, 235, 0.15) !important;
    border: 1px solid rgba(59, 130, 246, 0.4) !important;
    box-shadow: 0 4px 15px rgba(59, 130, 246, 0.1);
}
body.dark-mode .mis-profit-box span,
body.dark-mode .mis-profit-box strong,
body.dark-mode .mis-mode-card.total-mode .val {
    color: #60a5fa !important;
}
body.dark-mode .mis-mode-card.total-mode .lbl {
    color: #93c5fd !important;
}
body.dark-mode .mis-mode-card {
    background: rgba(17, 24, 39, 0.45) !important;
    backdrop-filter: blur(6px) !important;
    -webkit-backdrop-filter: blur(6px) !important;
    border: 1px solid rgba(255, 255, 255, 0.08) !important;
    border-radius: 12px !important;
    transition: all 0.2s ease;
}
body.dark-mode .mis-mode-card:hover {
    transform: translateY(-2px);
    border-color: rgba(255, 255, 255, 0.15) !important;
}
body.dark-mode .low-activity-box {
    background: rgba(120, 53, 15, 0.15) !important;
    border: 1px solid rgba(245, 158, 11, 0.3) !important;
    color: #fdba74 !important;
}
body.dark-mode .critical-issues-box {
    background: rgba(239, 68, 68, 0.05) !important;
    border: 1px solid rgba(239, 68, 68, 0.25) !important;
    color: #fca5a5 !important;
    border-radius: 12px !important;
}

/* Attendance Boxes in Dark Mode (High Contrast & Vibrant Glow) */
body.dark-mode .mis-att-box {
    background: rgba(17, 24, 39, 0.45) !important;
    backdrop-filter: blur(6px) !important;
    -webkit-backdrop-filter: blur(6px) !important;
    border: 1px solid rgba(255, 255, 255, 0.08) !important;
    border-radius: 12px !important;
    transition: all 0.2s ease;
}
body.dark-mode .mis-att-box:hover {
    transform: translateY(-2px);
}
body.dark-mode .mis-att-box.ab-green {
    border-color: rgba(16, 185, 129, 0.3) !important;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.1) !important;
    color: #34d399 !important;
}
body.dark-mode .mis-att-box.ab-green:hover {
    border-color: #10b981 !important;
    box-shadow: 0 6px 16px rgba(16, 185, 129, 0.2) !important;
}
body.dark-mode .mis-att-box.ab-red {
    border-color: rgba(239, 68, 68, 0.3) !important;
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.1) !important;
    color: #f87171 !important;
}
body.dark-mode .mis-att-box.ab-red:hover {
    border-color: #ef4444 !important;
    box-shadow: 0 6px 16px rgba(239, 68, 68, 0.2) !important;
}
body.dark-mode .mis-att-box.ab-orange {
    border-color: rgba(245, 158, 11, 0.3) !important;
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.1) !important;
    color: #fbbf24 !important;
}
body.dark-mode .mis-att-box.ab-orange:hover {
    border-color: #f97316 !important;
    box-shadow: 0 6px 16px rgba(245, 158, 11, 0.2) !important;
}
body.dark-mode .mis-att-box.ab-blue {
    border-color: rgba(59, 130, 246, 0.3) !important;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1) !important;
    color: #60a5fa !important;
}
body.dark-mode .mis-att-box.ab-blue:hover {
    border-color: #3b82f6 !important;
    box-shadow: 0 6px 16px rgba(59, 130, 246, 0.2) !important;
}
body.dark-mode .mis-att-box.ab-pink {
    border-color: rgba(236, 72, 153, 0.3) !important;
    box-shadow: 0 4px 12px rgba(236, 72, 153, 0.1) !important;
    color: #f472b6 !important;
}
body.dark-mode .mis-att-box.ab-pink:hover {
    border-color: #ec4899 !important;
    box-shadow: 0 6px 16px rgba(236, 72, 153, 0.2) !important;
}
body.dark-mode .mis-att-box .mis-att-box-val {
    color: #fff !important;
    font-size: 20px !important;
    font-weight: 800 !important;
}
body.dark-mode .mis-att-box .mis-att-box-lbl {
    color: #94a3b8 !important;
    font-weight: 700 !important;
    font-size: 10px !important;
    letter-spacing: 0.5px;
}

/* Defaulter Aging Boxes in Dark Mode */
body.dark-mode .mis-def-aging-box {
    background: rgba(17, 24, 39, 0.45) !important;
    backdrop-filter: blur(6px) !important;
    -webkit-backdrop-filter: blur(6px) !important;
    border: 1px solid rgba(255, 255, 255, 0.08) !important;
    border-radius: 12px !important;
    transition: all 0.2s ease;
}
body.dark-mode .mis-def-aging-box:hover {
    transform: translateY(-2px);
}
body.dark-mode .mis-def-aging-box .val {
    color: #fff !important;
    font-size: 18px !important;
    font-weight: 800 !important;
}
body.dark-mode .mis-def-aging-box .lbl {
    color: #94a3b8 !important;
    font-size: 9px !important;
    font-weight: 700 !important;
    text-transform: uppercase;
}
body.dark-mode .mis-def-aging-box.db-yellow { border-color: rgba(234, 179, 8, 0.3) !important; box-shadow: 0 4px 12px rgba(234, 179, 8, 0.1) !important; color: #fde047 !important; }
body.dark-mode .mis-def-aging-box.db-yellow:hover { border-color: #eab308 !important; box-shadow: 0 6px 16px rgba(234, 179, 8, 0.2) !important; }

body.dark-mode .mis-def-aging-box.db-orange { border-color: rgba(249, 115, 22, 0.3) !important; box-shadow: 0 4px 12px rgba(249, 115, 22, 0.1) !important; color: #fdba74 !important; }
body.dark-mode .mis-def-aging-box.db-orange:hover { border-color: #f97316 !important; box-shadow: 0 6px 16px rgba(249, 115, 22, 0.2) !important; }

body.dark-mode .mis-def-aging-box.db-pink { border-color: rgba(236, 72, 153, 0.3) !important; box-shadow: 0 4px 12px rgba(236, 72, 153, 0.1) !important; color: #f472b6 !important; }
body.dark-mode .mis-def-aging-box.db-pink:hover { border-color: #ec4899 !important; box-shadow: 0 6px 16px rgba(236, 72, 153, 0.2) !important; }

body.dark-mode .mis-def-aging-box.db-red { border-color: rgba(239, 68, 68, 0.3) !important; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.1) !important; color: #f87171 !important; }
body.dark-mode .mis-def-aging-box.db-red:hover { border-color: #ef4444 !important; box-shadow: 0 6px 16px rgba(239, 68, 68, 0.2) !important; }

/* Alert list box overrides */
body.dark-mode .mis-alert-list-box {
    background: rgba(239, 68, 68, 0.04) !important;
    border: 1px solid rgba(239, 68, 68, 0.15) !important;
    border-left: 4px solid #ef4444 !important;
    border-radius: 10px !important;
}
body.dark-mode .mis-alert-list-box.orange-box {
    background: rgba(249, 115, 22, 0.04) !important;
    border: 1px solid rgba(249, 115, 22, 0.15) !important;
    border-left: 4px solid #f97316 !important;
}
body.dark-mode .mis-alert-list-box.blue-box {
    background: rgba(59, 130, 246, 0.04) !important;
    border: 1px solid rgba(59, 130, 246, 0.15) !important;
    border-left: 4px solid #3b82f6 !important;
}
body.dark-mode .mis-alb-title {
    color: #fff !important;
    font-weight: 700 !important;
}
body.dark-mode .orange-box .mis-alb-title {
    color: #fff !important;
}
body.dark-mode .blue-box .mis-alb-title {
    color: #fff !important;
}
body.dark-mode .mis-alb-list {
    color: #cbd5e1 !important;
}
body.dark-mode .mis-discount-badge {
    background: rgba(234, 88, 12, 0.2) !important;
    color: #fb923c !important;
    border: 1px solid rgba(249, 115, 22, 0.4) !important;
}

/* ── UPGRADED PREMIUM GLOWING UI CLASSES ──────────────────── */
.mis-main-section-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 16px rgba(30, 58, 138, 0.04);
    margin-bottom: 24px;
    transition: all 0.25s ease;
}
.mis-main-section-hdr {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 16px;
    font-weight: 800;
    color: #1e3a8a;
    margin-bottom: 20px;
}
.mis-main-section-hdr i {
    font-size: 18px;
    color: #2563eb;
}

/* Digital layout grids */
.mis-digital-grid-container {
    display: flex;
    flex-direction: column;
    gap: 20px;
}
.mis-digital-sub-section {
    display: flex;
    flex-direction: column;
}
.mis-digital-sub-hdr {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}
.mis-digital-sub-hdr .sub-title {
    font-size: 12px;
    font-weight: 800;
    color: #475569;
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
    gap: 6px;
    text-transform: uppercase;
}
.mis-digital-sub-hdr .sub-title i {
    font-size: 14px;
}
.mis-digital-sub-hdr .view-details-link {
    font-size: 12px;
    font-weight: 600;
    color: #2563eb;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 4px;
}
.mis-digital-sub-hdr .view-details-link:hover {
    color: #1d4ed8;
}

/* Cards Grids */
.mis-app-downloads-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
}
.mis-digital-card-small {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    transition: all 0.25s ease;
}
.mis-digital-card-small:hover {
    transform: translateY(-2px);
}

/* Small card glow colors - Light Mode */
.mis-digital-card-small.glow-blue { border-color: #bfdbfe; }
.mis-digital-card-small.glow-blue:hover { box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1); }
.mis-digital-card-small.glow-green { border-color: #bbf7d0; }
.mis-digital-card-small.glow-green:hover { box-shadow: 0 4px 12px rgba(16, 185, 129, 0.1); }
.mis-digital-card-small.glow-purple { border-color: #e9d5ff; }
.mis-digital-card-small.glow-purple:hover { box-shadow: 0 4px 12px rgba(139, 92, 246, 0.1); }
.mis-digital-card-small.glow-orange { border-color: #fed7aa; }
.mis-digital-card-small.glow-orange:hover { box-shadow: 0 4px 12px rgba(249, 115, 22, 0.1); }

/* Progress rings */
.progress-ring-wrapper {
    margin-bottom: 12px;
    display: flex;
    justify-content: center;
}
.progress-ring {
    width: 52px;
    height: 52px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    position: relative;
}
.progress-ring.ring-blue { border: 3px solid #eff6ff; border-top-color: #3b82f6; color: #3b82f6; }
.progress-ring.ring-green { border: 3px solid #f0fdf4; border-top-color: #10b981; color: #10b981; }
.progress-ring.ring-purple { border: 3px solid #fdf4ff; border-top-color: #8b5cf6; color: #8b5cf6; }
.progress-ring.ring-orange { border: 3px solid #fff7ed; border-top-color: #f97316; color: #f97316; }

.metric-val {
    font-size: 20px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.2;
}
.metric-lbl {
    font-size: 11px;
    font-weight: 600;
    color: #64748b;
    margin-top: 2px;
    margin-bottom: 8px;
}
.metric-badge {
    padding: 3px 8px;
    border-radius: 20px;
    font-size: 10px;
    font-weight: 700;
}

/* Row splits for Library & Communications */
.mis-digital-row-split {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

/* Library card elements */
.mis-library-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}
.mis-library-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px 16px 28px;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    position: relative;
}
.lib-icon-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    margin-bottom: 8px;
}
.lib-icon-circle.bg-blue { background: #eff6ff; color: #3b82f6; }
.lib-icon-circle.bg-green { background: #f0fdf4; color: #10b981; }

.lib-val {
    font-size: 22px;
    font-weight: 800;
    color: #0f172a;
}
.lib-lbl {
    font-size: 11px;
    font-weight: 600;
    color: #64748b;
    margin-bottom: 14px;
}
.lib-progress-container {
    width: calc(100% - 32px);
    height: 4px;
    background: #f1f5f9;
    border-radius: 2px;
    position: absolute;
    bottom: 16px;
    left: 16px;
}
.lib-progress-bar {
    height: 100%;
    border-radius: 2px;
    position: absolute;
    left: 0;
    top: 0;
}
.lib-progress-bar.bg-blue { background: #3b82f6; }
.lib-progress-bar.bg-green { background: #10b981; }
.lib-progress-handle {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #fff;
    position: absolute;
    top: 50%;
    transform: translate(-50%, -50%);
}
.lib-progress-bar.bg-blue ~ .lib-progress-handle { border: 2px solid #3b82f6; }
.lib-progress-bar.bg-green ~ .lib-progress-handle { border: 2px solid #10b981; }

/* Communications card elements */
.mis-comm-card-full {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px 16px 28px;
    display: flex;
    flex-direction: column;
    position: relative;
    height: 100%;
    justify-content: center;
}
.comm-left {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
}
.comm-icon-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
}
.comm-icon-circle.bg-purple { background: #f5f3ff; color: #8b5cf6; }
.comm-info {
    display: flex;
    flex-direction: column;
}
.comm-val {
    font-size: 22px;
    font-weight: 800;
    color: #0f172a;
}
.comm-lbl {
    font-size: 11px;
    font-weight: 600;
    color: #64748b;
}
.comm-progress-container {
    width: calc(100% - 32px);
    height: 4px;
    background: #f1f5f9;
    border-radius: 2px;
    position: absolute;
    bottom: 16px;
    left: 16px;
}
.comm-progress-bar {
    height: 100%;
    border-radius: 2px;
    position: absolute;
    left: 0;
    top: 0;
}
.comm-progress-bar.bg-purple { background: #8b5cf6; }
.comm-progress-handle {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #fff;
    position: absolute;
    top: 50%;
    transform: translate(-50%, -50%);
}
.comm-progress-bar.bg-purple ~ .comm-progress-handle { border: 2px solid #8b5cf6; }

/* Digital Metrics section footer */
.mis-digital-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top: 1px solid #f1f5f9;
    padding-top: 14px;
    margin-top: 8px;
}
.mis-digital-footer .footer-left {
    font-size: 11px;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 6px;
}
.mis-digital-footer .footer-right .refresh-link {
    font-size: 11px;
    font-weight: 700;
    color: #2563eb;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 4px;
}
.mis-digital-footer .footer-right .refresh-link:hover {
    color: #1d4ed8;
}

/* ── PREMIUM GLOWING UI OVERRIDES - DARK MODE ────────────── */
body.dark-mode .mis-main-section-card {
    background: rgba(10, 15, 29, 0.6) !important;
    backdrop-filter: blur(12px) !important;
    -webkit-backdrop-filter: blur(12px) !important;
    border: 1px solid rgba(59, 130, 246, 0.15) !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4), 0 0 20px rgba(59, 130, 246, 0.05) !important;
}
body.dark-mode .mis-main-section-hdr {
    color: #fff !important;
}
body.dark-mode .mis-main-section-hdr i {
    color: #60a5fa !important;
    text-shadow: 0 0 8px rgba(59, 130, 246, 0.4);
}
body.dark-mode .mis-digital-sub-hdr .sub-title {
    color: #cbd5e1 !important;
}
body.dark-mode .mis-digital-sub-hdr .view-details-link {
    color: #60a5fa !important;
}
body.dark-mode .mis-digital-sub-hdr .view-details-link:hover {
    color: #93c5fd !important;
}

/* Card Overrides - Dark Mode */
body.dark-mode .mis-digital-card-small {
    background: rgba(17, 24, 39, 0.5) !important;
    backdrop-filter: blur(8px) !important;
    -webkit-backdrop-filter: blur(8px) !important;
    border: 1px solid rgba(255, 255, 255, 0.06) !important;
}
body.dark-mode .mis-digital-card-small .metric-val {
    color: #fff !important;
}
body.dark-mode .mis-digital-card-small .metric-lbl {
    color: #94a3b8 !important;
}

/* Glow Borders in Dark Mode */
body.dark-mode .mis-digital-card-small.glow-blue {
    border-color: rgba(59, 130, 246, 0.3) !important;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3), inset 0 0 10px rgba(59, 130, 246, 0.05) !important;
}
body.dark-mode .mis-digital-card-small.glow-blue:hover {
    border-color: #3b82f6 !important;
    box-shadow: 0 6px 20px rgba(59, 130, 246, 0.2), inset 0 0 12px rgba(59, 130, 246, 0.1) !important;
}

body.dark-mode .mis-digital-card-small.glow-green {
    border-color: rgba(16, 185, 129, 0.3) !important;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3), inset 0 0 10px rgba(16, 185, 129, 0.05) !important;
}
body.dark-mode .mis-digital-card-small.glow-green:hover {
    border-color: #10b981 !important;
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.2), inset 0 0 12px rgba(16, 185, 129, 0.1) !important;
}

body.dark-mode .mis-digital-card-small.glow-purple {
    border-color: rgba(139, 92, 246, 0.3) !important;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3), inset 0 0 10px rgba(139, 92, 246, 0.05) !important;
}
body.dark-mode .mis-digital-card-small.glow-purple:hover {
    border-color: #8b5cf6 !important;
    box-shadow: 0 6px 20px rgba(139, 92, 246, 0.2), inset 0 0 12px rgba(139, 92, 246, 0.1) !important;
}

body.dark-mode .mis-digital-card-small.glow-orange {
    border-color: rgba(249, 115, 22, 0.3) !important;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3), inset 0 0 10px rgba(249, 115, 22, 0.05) !important;
}
body.dark-mode .mis-digital-card-small.glow-orange:hover {
    border-color: #f97316 !important;
    box-shadow: 0 6px 20px rgba(249, 115, 22, 0.2), inset 0 0 12px rgba(249, 115, 22, 0.1) !important;
}

/* Rings Overrides in Dark Mode */
body.dark-mode .progress-ring {
    background: rgba(13, 21, 39, 0.4) !important;
    box-shadow: inset 0 0 8px rgba(0, 0, 0, 0.4) !important;
}
body.dark-mode .progress-ring.ring-blue { border-color: rgba(59, 130, 246, 0.15) !important; border-top-color: #3b82f6 !important; }
body.dark-mode .progress-ring.ring-green { border-color: rgba(16, 185, 129, 0.15) !important; border-top-color: #10b981 !important; }
body.dark-mode .progress-ring.ring-purple { border-color: rgba(139, 92, 246, 0.15) !important; border-top-color: #8b5cf6 !important; }
body.dark-mode .progress-ring.ring-orange { border-color: rgba(249, 115, 22, 0.15) !important; border-top-color: #f97316 !important; }

/* Library & Comm Overrides in Dark Mode */
body.dark-mode .mis-library-card, 
body.dark-mode .mis-comm-card-full {
    background: rgba(17, 24, 39, 0.5) !important;
    backdrop-filter: blur(8px) !important;
    -webkit-backdrop-filter: blur(8px) !important;
    border: 1px solid rgba(255, 255, 255, 0.06) !important;
}
body.dark-mode .mis-library-card:hover, 
body.dark-mode .mis-comm-card-full:hover {
    border-color: rgba(255, 255, 255, 0.15) !important;
}
body.dark-mode .lib-val, 
body.dark-mode .comm-val {
    color: #fff !important;
}
body.dark-mode .lib-lbl, 
body.dark-mode .comm-lbl {
    color: #94a3b8 !important;
}

body.dark-mode .lib-icon-circle.bg-blue { background: rgba(59, 130, 246, 0.12) !important; color: #60a5fa !important; border: 1px solid rgba(59, 130, 246, 0.2) !important; }
body.dark-mode .lib-icon-circle.bg-green { background: rgba(16, 185, 129, 0.12) !important; color: #34d399 !important; border: 1px solid rgba(16, 185, 129, 0.2) !important; }
body.dark-mode .comm-icon-circle.bg-purple { background: rgba(139, 92, 246, 0.12) !important; color: #a78bfa !important; border: 1px solid rgba(139, 92, 246, 0.2) !important; }

body.dark-mode .lib-progress-container, 
body.dark-mode .comm-progress-container {
    background: rgba(255, 255, 255, 0.06) !important;
}
body.dark-mode .lib-progress-bar.bg-blue { background: #3b82f6 !important; box-shadow: 0 0 6px #3b82f6 !important; }
body.dark-mode .lib-progress-bar.bg-green { background: #10b981 !important; box-shadow: 0 0 6px #10b981 !important; }
body.dark-mode .comm-progress-bar.bg-purple { background: #8b5cf6 !important; box-shadow: 0 0 6px #8b5cf6 !important; }

body.dark-mode .lib-progress-handle { border-color: #3b82f6 !important; box-shadow: 0 0 6px #3b82f6 !important; }
body.dark-mode .mis-library-card:last-child .lib-progress-handle { border-color: #10b981 !important; box-shadow: 0 0 6px #10b981 !important; }
body.dark-mode .comm-progress-handle { border-color: #8b5cf6 !important; box-shadow: 0 0 6px #8b5cf6 !important; }

body.dark-mode .mis-digital-footer {
    border-top-color: rgba(255, 255, 255, 0.08) !important;
}
body.dark-mode .mis-digital-footer .footer-left {
    color: #64748b !important;
}
body.dark-mode .mis-digital-footer .footer-right .refresh-link {
    color: #60a5fa !important;
}
body.dark-mode .mis-digital-footer .footer-right .refresh-link:hover {
    color: #93c5fd !important;
}

/* upgraded other dashboard cards to look like the digital metrics layout */
body.dark-mode .mis-detail-card,
body.dark-mode .mis-follow-card {
    background: rgba(10, 15, 29, 0.6) !important;
    backdrop-filter: blur(10px) !important;
    -webkit-backdrop-filter: blur(10px) !important;
    border: 1px solid rgba(59, 130, 246, 0.15) !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4), 0 0 20px rgba(59, 130, 246, 0.05) !important;
}
body.dark-mode .mis-detail-title,
body.dark-mode .mis-follow-hdr {
    color: #fff !important;
}
body.dark-mode .mis-detail-meta {
    color: #94a3b8 !important;
}

/* Alert list box left borders */
body.dark-mode .mis-alert-list-box {
    background: rgba(239, 68, 68, 0.04) !important;
    border: 1px solid rgba(239, 68, 68, 0.15) !important;
    border-left: 4px solid #ef4444 !important;
}
body.dark-mode .mis-alert-list-box.orange-box {
    background: rgba(249, 115, 22, 0.04) !important;
    border: 1px solid rgba(249, 115, 22, 0.15) !important;
    border-left: 4px solid #f97316 !important;
}
body.dark-mode .mis-alert-list-box.blue-box {
    background: rgba(59, 130, 246, 0.04) !important;
    border: 1px solid rgba(59, 130, 246, 0.15) !important;
    border-left: 4px solid #3b82f6 !important;
}

/* 3-Card Grid & Red Glow Components Support */
.mis-three-card-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
}
@media (max-width: 1024px) {
    .mis-three-card-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 768px) {
    .mis-three-card-grid { grid-template-columns: 1fr; }
}

.mis-digital-card-small.glow-red { border-color: #fecaca; }
.mis-digital-card-small.glow-red:hover { box-shadow: 0 4px 12px rgba(239, 68, 68, 0.1); }
.progress-ring.ring-red { border: 3px solid #fef2f2; border-top-color: #ef4444; color: #ef4444; }
.badge-red { background: #fef2f2; color: #ef4444; border: 1px solid #fecaca; }

body.dark-mode .mis-digital-card-small.glow-red {
    background: rgba(17, 24, 39, 0.5) !important;
    backdrop-filter: blur(8px) !important;
    -webkit-backdrop-filter: blur(8px) !important;
    border: 1px solid rgba(239, 68, 68, 0.3) !important;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3), inset 0 0 10px rgba(239, 68, 68, 0.05) !important;
}
body.dark-mode .mis-digital-card-small.glow-red:hover {
    border-color: #ef4444 !important;
    box-shadow: 0 6px 20px rgba(239, 68, 68, 0.2), inset 0 0 12px rgba(239, 68, 68, 0.1) !important;
}
body.dark-mode .progress-ring.ring-red { 
    background: rgba(13, 21, 39, 0.4) !important;
    box-shadow: inset 0 0 8px rgba(0, 0, 0, 0.4) !important;
    border-color: rgba(239, 68, 68, 0.15) !important; 
    border-top-color: #ef4444 !important; 
}
body.dark-mode .badge-red { 
    background: rgba(239, 68, 68, 0.12) !important; 
    color: #f87171 !important; 
    border: 1px solid rgba(239, 68, 68, 0.2) !important; 
}
</style>
@endsection

@section('content')
<div class="mis-page">

    {{-- ── HEADER ──────────────────────────────────────────────────────── --}}
    <div class="mis-header">
        <div>
            <h1><i class="fas fa-chart-bar" style="margin-right:10px; opacity:.9;"></i>Daily MIS Report</h1>
            <p>Management Information System &nbsp;·&nbsp; {{ $school->name ?? 'Pragya School' }}</p>
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
        <a href="{{ route('school.staff.bulk-attendance') }}" class="mis-alert-card" style="text-decoration:none;">
            <div class="mis-alert-left">
                <i class="fas fa-exclamation-circle mis-alert-icon alert-red"></i>
                <div>
                    <div class="mis-alert-title">Attendance Not Marked</div>
                    <div class="mis-alert-desc">Teachers haven't marked attendance</div>
                </div>
            </div>
            <div class="mis-alert-badge badge-red">{{ $attendanceNotMarkedTeachersCount }}</div>
        </a>
        <a href="{{ route('school.fees.collection-followup') }}" class="mis-alert-card" style="text-decoration:none;">
            <div class="mis-alert-left">
                <i class="fas fa-exclamation-circle mis-alert-icon alert-red"></i>
                <div>
                    <div class="mis-alert-title">Fee Defaulters (90+ days)</div>
                    <div class="mis-alert-desc">Critical collection required</div>
                </div>
            </div>
            <div class="mis-alert-badge badge-red">{{ $feeDefaultersCriticalCount }}</div>
        </a>
        <a href="{{ route('school.students.index') }}" class="mis-alert-card" style="text-decoration:none;">
            <div class="mis-alert-left">
                <i class="fas fa-exclamation-triangle mis-alert-icon alert-orange"></i>
                <div>
                    <div class="mis-alert-title">App Not Downloaded</div>
                    <div class="mis-alert-desc">Parents + Staff pending</div>
                </div>
            </div>
            <div class="mis-alert-badge badge-orange">{{ $appNotDownloadedCount }}</div>
        </a>
    </div>

    {{-- ── ROW 3: 2-COLUMN METRICS BREAKDOWN ──────────────────────────── --}}
    <div class="mis-two-col">
        {{-- Column 1: Income & Expenses --}}
        <div class="mis-main-section-card" style="margin-bottom:0;">
            <div class="mis-main-section-hdr" style="margin-bottom:12px;">
                <i class="fas fa-landmark" style="color: #3b82f6;"></i>
                <span>Income & Expenses</span>
            </div>

            <div class="mis-digital-grid-container">
                {{-- Today's Income --}}
                <div class="mis-digital-sub-section">
                    <div class="mis-digital-sub-hdr" style="margin-bottom:8px;">
                        <div class="sub-title">
                            <i class="fas fa-arrow-trend-down" style="color: #10b981;"></i>
                            <span>Today's Income</span>
                        </div>
                    </div>
                    <div class="mis-three-card-grid">
                        {{-- Fee Collection --}}
                        <div class="mis-digital-card-small glow-green">
                            <div class="progress-ring-wrapper">
                                <div class="progress-ring ring-green">
                                    <i class="fas fa-indian-rupee-sign"></i>
                                </div>
                            </div>
                            <div class="metric-val">₹{{ number_format($todayFeeCollection, 2) }}</div>
                            <div class="metric-lbl">Fee Collection</div>
                            <div class="metric-badge badge-green">Collection</div>
                        </div>

                        {{-- Other Income --}}
                        <div class="mis-digital-card-small glow-blue">
                            <div class="progress-ring-wrapper">
                                <div class="progress-ring ring-blue">
                                    <i class="fas fa-plus"></i>
                                </div>
                            </div>
                            <div class="metric-val">₹{{ number_format($todayOtherIncome, 2) }}</div>
                            <div class="metric-lbl">Other Income</div>
                            <div class="metric-badge badge-blue">Other</div>
                        </div>

                        {{-- Total Income --}}
                        <div class="mis-digital-card-small glow-green">
                            <div class="progress-ring-wrapper">
                                <div class="progress-ring ring-green">
                                    <i class="fas fa-vault"></i>
                                </div>
                            </div>
                            <div class="metric-val">₹{{ number_format($todayTotalIncome, 2) }}</div>
                            <div class="metric-lbl">Total Income</div>
                            <div class="metric-badge badge-green">Total</div>
                        </div>
                    </div>
                </div>

                {{-- Expenses and Profit --}}
                <div class="mis-digital-row-split">
                    {{-- Today's Expenses --}}
                    <div class="mis-digital-sub-section">
                        <div class="mis-digital-sub-hdr" style="margin-bottom:8px;">
                            <div class="sub-title">
                                <i class="fas fa-arrow-trend-up" style="color: #ef4444;"></i>
                                <span>Today's Expenses</span>
                            </div>
                        </div>
                        <div class="mis-library-grid">
                            <div class="mis-digital-card-small glow-orange" style="width:100%;">
                                <div class="progress-ring-wrapper">
                                    <div class="progress-ring ring-orange">
                                        <i class="fas fa-receipt"></i>
                                    </div>
                                </div>
                                <div class="metric-val">₹{{ number_format($todayOtherExpenses, 2) }}</div>
                                <div class="metric-lbl">Other Expenses</div>
                            </div>
                            <div class="mis-digital-card-small glow-red" style="width:100%;">
                                <div class="progress-ring-wrapper">
                                    <div class="progress-ring ring-red">
                                        <i class="fas fa-file-invoice-dollar"></i>
                                    </div>
                                </div>
                                <div class="metric-val">₹{{ number_format($todayTotalExpenses, 2) }}</div>
                                <div class="metric-lbl">Total Expenses</div>
                            </div>
                        </div>
                    </div>

                    {{-- Net Profit Today --}}
                    <div class="mis-digital-sub-section">
                        <div class="mis-digital-sub-hdr" style="margin-bottom:8px;">
                            <div class="sub-title">
                                <i class="fas fa-chart-line" style="color: #8b5cf6;"></i>
                                <span>Net Profit Today</span>
                            </div>
                        </div>
                        <div class="mis-comm-card-full">
                            <div class="comm-left">
                                <div class="comm-icon-circle bg-purple">
                                    <i class="fas fa-wallet"></i>
                                </div>
                                <div class="comm-info">
                                    <div class="comm-val">₹{{ number_format($todayNetProfit, 2) }}</div>
                                    <div class="comm-lbl">Net Profit Today</div>
                                </div>
                            </div>
                            <div class="comm-progress-container">
                                @php
                                    $profitFill = $todayNetProfit > 0 ? 95 : 0;
                                @endphp
                                <div class="comm-progress-bar bg-purple" style="width: {{ $profitFill }}%;"></div>
                                <div class="comm-progress-handle" style="left: {{ $profitFill }}%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Column 2: Admissions & Academic --}}
        <div class="mis-main-section-card" style="margin-bottom:0;">
            <div class="mis-main-section-hdr" style="margin-bottom:12px;">
                <i class="fas fa-graduation-cap" style="color: #10b981;"></i>
                <span>Admissions & Academic</span>
            </div>

            <div class="mis-digital-grid-container">
                {{-- Today's Admissions --}}
                <div class="mis-digital-sub-section">
                    <div class="mis-digital-sub-hdr" style="margin-bottom:8px;">
                        <div class="sub-title">
                            <i class="fas fa-user-plus"></i>
                            <span>Today's Admissions</span>
                        </div>
                    </div>
                    <div class="mis-app-downloads-grid">
                        {{-- Enquiries --}}
                        <div class="mis-digital-card-small glow-blue">
                            <div class="progress-ring-wrapper">
                                <div class="progress-ring ring-blue">
                                    <i class="fas fa-info"></i>
                                </div>
                            </div>
                            <div class="metric-val">{{ $todayEnquiriesCount }}</div>
                            <div class="metric-lbl">Enquiries</div>
                            <div class="metric-badge badge-blue">Leads</div>
                        </div>

                        {{-- Applications --}}
                        <div class="mis-digital-card-small glow-purple">
                            <div class="progress-ring-wrapper">
                                <div class="progress-ring ring-purple">
                                    <i class="fas fa-file-signature"></i>
                                </div>
                            </div>
                            <div class="metric-val">{{ $todayApplicationsCount }}</div>
                            <div class="metric-lbl">Applications</div>
                            <div class="metric-badge badge-purple">Applied</div>
                        </div>

                        {{-- Interactions --}}
                        <div class="mis-digital-card-small glow-orange">
                            <div class="progress-ring-wrapper">
                                <div class="progress-ring ring-orange">
                                    <i class="fas fa-comments"></i>
                                </div>
                            </div>
                            <div class="metric-val">{{ $todayInteractionsCount }}</div>
                            <div class="metric-lbl">Interactions</div>
                            <div class="metric-badge badge-orange">Interviews</div>
                        </div>

                        {{-- Admissions --}}
                        <div class="mis-digital-card-small glow-green">
                            <div class="progress-ring-wrapper">
                                <div class="progress-ring ring-green">
                                    <i class="fas fa-check-double"></i>
                                </div>
                            </div>
                            <div class="metric-val">{{ $todayAdmissionsCount }}</div>
                            <div class="metric-lbl">Admissions</div>
                            <div class="metric-badge badge-green">Enrolled</div>
                        </div>
                    </div>
                </div>

                {{-- Academic Sharing Today --}}
                <div class="mis-digital-sub-section">
                    <div class="mis-digital-sub-hdr" style="margin-bottom:8px;">
                        <div class="sub-title">
                            <i class="fas fa-share-nodes"></i>
                            <span>Academic Sharing Today</span>
                        </div>
                    </div>
                    <div class="mis-app-downloads-grid">
                        {{-- Assignments --}}
                        <div class="mis-digital-card-small glow-orange">
                            <div class="progress-ring-wrapper">
                                <div class="progress-ring ring-orange">
                                    <i class="fas fa-book"></i>
                                </div>
                            </div>
                            <div class="metric-val">{{ $todayAssignmentsShared }}</div>
                            <div class="metric-lbl">Assignments</div>
                        </div>

                        {{-- Study Materials --}}
                        <div class="mis-digital-card-small glow-blue">
                            <div class="progress-ring-wrapper">
                                <div class="progress-ring ring-blue">
                                    <i class="fas fa-file-pdf"></i>
                                </div>
                            </div>
                            <div class="metric-val">{{ $todayMaterialsShared }}</div>
                            <div class="metric-lbl">Study Materials</div>
                        </div>

                        {{-- Tests --}}
                        <div class="mis-digital-card-small glow-red">
                            <div class="progress-ring-wrapper">
                                <div class="progress-ring ring-red">
                                    <i class="fas fa-vial"></i>
                                </div>
                            </div>
                            <div class="metric-val">{{ $todayTestsShared }}</div>
                            <div class="metric-lbl">Tests</div>
                        </div>

                        {{-- Diary Entries --}}
                        <div class="mis-digital-card-small glow-purple">
                            <div class="progress-ring-wrapper">
                                <div class="progress-ring ring-purple">
                                    <i class="fas fa-calendar-day"></i>
                                </div>
                            </div>
                            <div class="metric-val">{{ $todayDiariesShared }}</div>
                            <div class="metric-lbl">Diary Entries</div>
                        </div>
                    </div>
                </div>

                {{-- Low Activity Notifications --}}
                <div class="low-activity-box" style="margin-top: 10px;">
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
    </div>

    {{-- ── ROW 3.5: UPGRADED FULL-WIDTH DIGITAL METRICS ────────────────── --}}
    <div class="mis-main-section-card">
        <div class="mis-main-section-hdr">
            <i class="fas fa-desktop"></i>
            <span>Digital Metrics</span>
        </div>

        <div class="mis-digital-grid-container">
            {{-- App Downloads section --}}
            <div class="mis-digital-sub-section">
                <div class="mis-digital-sub-hdr">
                    <div class="sub-title">
                        <i class="fas fa-mobile-alt"></i>
                        <span>App Downloads</span>
                    </div>
                </div>
                <div class="mis-app-downloads-grid">
                    {{-- Student Downloads --}}
                    @php
                        $studentPct = $studentAppDownloadedTotal > 0 ? ($studentAppDownloadedCount / $studentAppDownloadedTotal) * 100 : 0;
                    @endphp
                    <div class="mis-digital-card-small glow-blue">
                        <div class="progress-ring-wrapper">
                            <div class="progress-ring ring-blue">
                                <i class="fas fa-download"></i>
                            </div>
                        </div>
                        <div class="metric-val">{{ $studentAppDownloadedCount }}/{{ $studentAppDownloadedTotal }}</div>
                        <div class="metric-lbl">Student Downloaded</div>
                        <div class="metric-badge badge-blue">{{ number_format($studentPct, 2) }}%</div>
                    </div>

                    {{-- Staff Downloads --}}
                    @php
                        $staffPct = $staffAppDownloadedTotal > 0 ? ($staffAppDownloadedCount / $staffAppDownloadedTotal) * 100 : 0;
                    @endphp
                    <div class="mis-digital-card-small glow-green">
                        <div class="progress-ring-wrapper">
                            <div class="progress-ring ring-green">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <div class="metric-val">{{ $staffAppDownloadedCount }}/{{ $staffAppDownloadedTotal }}</div>
                        <div class="metric-lbl">Staff Downloaded</div>
                        <div class="metric-badge badge-green">{{ number_format($staffPct, 2) }}%</div>
                    </div>

                    {{-- Parent Downloads --}}
                    @php
                        $parentPct = $parentAppDownloadedTotal > 0 ? ($parentAppDownloadedCount / $parentAppDownloadedTotal) * 100 : 0;
                    @endphp
                    <div class="mis-digital-card-small glow-purple">
                        <div class="progress-ring-wrapper">
                            <div class="progress-ring ring-purple">
                                <i class="fas fa-user"></i>
                            </div>
                        </div>
                        <div class="metric-val">{{ $parentAppDownloadedCount }}/{{ $parentAppDownloadedTotal }}</div>
                        <div class="metric-lbl">Parent Downloaded</div>
                        <div class="metric-badge badge-purple">{{ number_format($parentPct, 2) }}%</div>
                    </div>

                    {{-- Pending Downloads --}}
                    <div class="mis-digital-card-small glow-orange">
                        <div class="progress-ring-wrapper">
                            <div class="progress-ring ring-orange">
                                <i class="far fa-clock"></i>
                            </div>
                        </div>
                        <div class="metric-val">{{ $pendingDownloadsCount }}</div>
                        <div class="metric-lbl">Pending Downloads</div>
                        <div class="metric-badge badge-orange">Pending</div>
                    </div>
                </div>
            </div>

            {{-- Library & Communications split row --}}
            <div class="mis-digital-row-split">
                {{-- Library Today --}}
                <div class="mis-digital-sub-section">
                    <div class="mis-digital-sub-hdr">
                        <div class="sub-title">
                            <i class="fas fa-book"></i>
                            <span>Library Today</span>
                        </div>
                    </div>
                    <div class="mis-library-grid">
                        <div class="mis-library-card">
                            <div class="lib-icon-circle bg-blue">
                                <i class="fas fa-book-open"></i>
                            </div>
                            <div class="lib-val">{{ $todayBooksIssued }}</div>
                            <div class="lib-lbl">Books Issued</div>
                            <div class="lib-progress-container">
                                @php
                                    $issuedFill = $todayBooksIssued > 0 ? min(50 + ($todayBooksIssued * 5), 100) : 62.5;
                                @endphp
                                <div class="lib-progress-bar bg-blue" style="width: {{ $issuedFill }}%;"></div>
                                <div class="lib-progress-handle" style="left: {{ $issuedFill }}%;"></div>
                            </div>
                        </div>
                        <div class="mis-library-card">
                            <div class="lib-icon-circle bg-green">
                                <i class="fas fa-undo-alt"></i>
                            </div>
                            <div class="lib-val">{{ $todayBooksReturned }}</div>
                            <div class="lib-lbl">Books Returned</div>
                            <div class="lib-progress-container">
                                @php
                                    $returnedFill = $todayBooksReturned > 0 ? min(50 + ($todayBooksReturned * 5), 100) : 92.5;
                                @endphp
                                <div class="lib-progress-bar bg-green" style="width: {{ $returnedFill }}%;"></div>
                                <div class="lib-progress-handle" style="left: {{ $returnedFill }}%;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Communications --}}
                <div class="mis-digital-sub-section">
                    <div class="mis-digital-sub-hdr">
                        <div class="sub-title">
                            <i class="fas fa-bell"></i>
                            <span>Communications</span>
                        </div>
                    </div>
                    <div class="mis-comm-card-full">
                        <div class="comm-left">
                            <div class="comm-icon-circle bg-purple">
                                <i class="fas fa-paper-plane"></i>
                            </div>
                            <div class="comm-info">
                                <div class="comm-val">{{ $todayNoticesShared }}</div>
                                <div class="comm-lbl">Notices Shared</div>
                            </div>
                        </div>
                        <div class="comm-progress-container">
                            @php
                                $noticesFill = $todayNoticesShared > 0 ? min(50 + ($todayNoticesShared * 10), 100) : 75;
                            @endphp
                            <div class="comm-progress-bar bg-purple" style="width: {{ $noticesFill }}%;"></div>
                            <div class="comm-progress-handle" style="left: {{ $noticesFill }}%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer inside Digital Metrics section --}}
            <div class="mis-digital-footer">
                <div class="footer-left">
                    <i class="far fa-calendar-alt"></i>
                    <span>Data as of: {{ $date->format('M d, Y') }} &nbsp;|&nbsp; Last updated: {{ now()->format('h:i A') }}</span>
                </div>
                <div class="footer-right">
                    <a href="javascript:location.reload();" class="refresh-link"><i class="fas fa-sync-alt"></i> Refresh</a>
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
                    <a href="{{ route('school.fees.collection-followup') }}" style="color:inherit;text-decoration:none;">Fee Defaulters (90+ Days)</a>
                </div>
                <ul class="mis-alb-list" id="feeDefaultersList">
                    @forelse($feeDefaulters90PlusList as $item)
                        <li>• {{ $item['name'] }} ({{ $item['class_section'] }}) — ₹ {{ number_format($item['pending_amount'], 0) }} — {{ $item['due_days'] }} days</li>
                    @empty
                        <li>No critical fee defaulters.</li>
                    @endforelse
                </ul>
                @if($feeDefaulters90PlusMoreCount > 0)
                    {{-- Hidden extra items --}}
                    <ul class="mis-alb-list mis-expand-list" id="feeDefaultersExtra" style="display:none;">
                        @php
                            $allFeeDefaulters = \App\Models\StudentFee::where('school_id', auth()->user()->school_id)
                                ->whereColumn('amount', '>', 'paid_amount')
                                ->whereDate('due_date', '<', \Carbon\Carbon::parse(request('date', today()))->subDays(90))
                                ->with(['student.class', 'student.section'])
                                ->get();
                            $shownCount = $feeDefaulters90PlusList->count();
                        @endphp
                        @foreach($allFeeDefaulters->skip($shownCount) as $fee)
                            @if($fee->student)
                                <li>• {{ $fee->student->full_name }} ({{ ($fee->student->class->name ?? '—') }}-{{ ($fee->student->section->name ?? '—') }}) — ₹ {{ number_format($fee->amount - $fee->paid_amount, 0) }} — {{ \Carbon\Carbon::parse($fee->due_date)->diffInDays(today()) }} days</li>
                            @endif
                        @endforeach
                    </ul>
                    <button type="button" class="mis-alb-more-link mis-toggle-btn" data-target="feeDefaultersExtra" data-count="{{ $feeDefaulters90PlusMoreCount }}" data-label="students">
                        + {{ $feeDefaulters90PlusMoreCount }} more students
                    </button>
                @endif
            </div>

            {{-- Classes Attendance Not Marked --}}
            <div class="mis-alert-list-box">
                <div class="mis-alb-title">
                    <i class="fas fa-exclamation-circle"></i>
                    <a href="{{ route('school.attendance.students.index') }}" style="color:inherit;text-decoration:none;">Classes Attendance not marked today</a>
                </div>
                <ul class="mis-alb-list" id="attNotMarkedList">
                    @forelse($classesAttendanceNotMarkedList as $item)
                        <li>• {{ $item }} (NA)</li>
                    @empty
                        <li>All classes attendance marked.</li>
                    @endforelse
                </ul>
                @if($classesAttendanceNotMarkedMoreCount > 0)
                    @php
                        $allUnmarkedSections = \App\Models\Section::where('school_id', auth()->user()->school_id)
                            ->whereNotIn('id', \App\Models\StudentAttendance::where('school_id', auth()->user()->school_id)
                                ->whereDate('date', \Carbon\Carbon::parse(request('date', today())))
                                ->pluck('section_id')->unique())
                            ->with('schoolClass')->get();
                        $shownAttCount = $classesAttendanceNotMarkedList->count();
                    @endphp
                    <ul class="mis-alb-list mis-expand-list" id="attNotMarkedExtra" style="display:none;">
                        @foreach($allUnmarkedSections->skip($shownAttCount) as $sec)
                            <li>• {{ ($sec->schoolClass->name ?? '') }}-{{ $sec->name }} (NA)</li>
                        @endforeach
                    </ul>
                    <button type="button" class="mis-alb-more-link mis-toggle-btn" data-target="attNotMarkedExtra" data-count="{{ $classesAttendanceNotMarkedMoreCount }}" data-label="classes">
                        + {{ $classesAttendanceNotMarkedMoreCount }} more classes
                    </button>
                @endif
            </div>

            {{-- Teachers Not Marked Attendance in 7 days --}}
            <div class="mis-alert-list-box orange-box">
                <div class="mis-alb-title">
                    <i class="fas fa-exclamation-triangle"></i>
                    <a href="{{ route('school.staff.bulk-attendance') }}" style="color:inherit;text-decoration:none;">Teachers not marked attendance in 7 days</a>
                </div>
                <ul class="mis-alb-list" id="teachersNoAttList">
                    @forelse($teachersNotMarkedAttendance7DaysList as $item)
                        <li>• {{ $item }} (NA)</li>
                    @empty
                        <li>No pending class attendance over 7 days.</li>
                    @endforelse
                </ul>
                @if($teachersNotMarkedAttendance7DaysMoreCount > 0)
                    @php
                        $sectionsMarked7Days = \App\Models\StudentAttendance::where('school_id', auth()->user()->school_id)
                            ->whereDate('date', '>=', \Carbon\Carbon::parse(request('date', today()))->subDays(7))
                            ->pluck('section_id')->unique();
                        $allSectionsNoAtt7Days = \App\Models\Section::where('school_id', auth()->user()->school_id)
                            ->whereNotIn('id', $sectionsMarked7Days)
                            ->with('schoolClass')->get();
                        $shownNoAttCount = $teachersNotMarkedAttendance7DaysList->count();
                    @endphp
                    <ul class="mis-alb-list mis-expand-list" id="teachersNoAttExtra" style="display:none;">
                        @foreach($allSectionsNoAtt7Days->skip($shownNoAttCount) as $sec)
                            <li>• {{ ($sec->schoolClass->name ?? '') }}-{{ $sec->name }} (NA)</li>
                        @endforeach
                    </ul>
                    <button type="button" class="mis-alb-more-link mis-toggle-btn" data-target="teachersNoAttExtra" data-count="{{ $teachersNotMarkedAttendance7DaysMoreCount }}" data-label="classes">
                        + {{ $teachersNotMarkedAttendance7DaysMoreCount }} more classes
                    </button>
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
                    <a href="{{ route('school.diary.report') }}" style="color:inherit;text-decoration:none;">Teachers haven't shared any content in 7 days</a>
                </div>
                <ul class="mis-alb-list" id="teachersNoShareList">
                    @forelse($teachersNoSharing7DaysList as $item)
                        <li>• {{ $item }}</li>
                    @empty
                        <li>All teachers active in sharing.</li>
                    @endforelse
                </ul>
                @if($teachersNoSharing7DaysMoreCount > 0)
                    @php
                        $teachersWithDiary7D = \App\Models\DigitalDiary::where('school_id', auth()->user()->school_id)
                            ->whereDate('diary_date', '>=', \Carbon\Carbon::parse(request('date', today()))->subDays(7))
                            ->pluck('staff_id')->unique();
                        $allTeachersNoShare = \App\Models\Staff::where('school_id', auth()->user()->school_id)
                            ->where('is_active', true)
                            ->whereNotIn('id', $teachersWithDiary7D)
                            ->get();
                        $shownShareCount = $teachersNoSharing7DaysList->count();
                    @endphp
                    <ul class="mis-alb-list mis-expand-list" id="teachersNoShareExtra" style="display:none;">
                        @foreach($allTeachersNoShare->skip($shownShareCount) as $st)
                            <li>• {{ $st->full_name }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="mis-alb-more-link mis-toggle-btn" data-target="teachersNoShareExtra" data-count="{{ $teachersNoSharing7DaysMoreCount }}" data-label="teachers">
                        + {{ $teachersNoSharing7DaysMoreCount }} more teachers
                    </button>
                @endif
            </div>

            {{-- Classes missing diary entries today --}}
            <div class="mis-alert-list-box blue-box">
                <div class="mis-alb-title">
                    <i class="fas fa-info-circle"></i>
                    <a href="{{ route('school.diary.report') }}" style="color:inherit;text-decoration:none;">Classes missing diary entries today</a>
                </div>
                <ul class="mis-alb-list" id="diaryMissingList">
                    @forelse($classesMissingDiaryTodayList as $item)
                        <li>• {{ $item }} (NA)</li>
                    @empty
                        <li>All classes have diary entries.</li>
                    @endforelse
                </ul>
                @if($classesMissingDiaryTodayMoreCount > 0)
                    @php
                        $sectionsWithDiaryToday = \App\Models\DigitalDiary::where('school_id', auth()->user()->school_id)
                            ->whereDate('diary_date', \Carbon\Carbon::parse(request('date', today())))
                            ->pluck('section_id')->unique();
                        $allSectionsMissingDiary = \App\Models\Section::where('school_id', auth()->user()->school_id)
                            ->whereNotIn('id', $sectionsWithDiaryToday)
                            ->with('schoolClass')->get();
                        $shownDiaryCount = $classesMissingDiaryTodayList->count();
                    @endphp
                    <ul class="mis-alb-list mis-expand-list" id="diaryMissingExtra" style="display:none;">
                        @foreach($allSectionsMissingDiary->skip($shownDiaryCount) as $sec)
                            <li>• {{ ($sec->schoolClass->name ?? '') }}-{{ $sec->name }} (NA)</li>
                        @endforeach
                    </ul>
                    <button type="button" class="mis-alb-more-link mis-toggle-btn" data-target="diaryMissingExtra" data-count="{{ $classesMissingDiaryTodayMoreCount }}" data-label="classes">
                        + {{ $classesMissingDiaryTodayMoreCount }} more classes
                    </button>
                @endif
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.mis-toggle-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var targetId = btn.getAttribute('data-target');
                var count = btn.getAttribute('data-count');
                var label = btn.getAttribute('data-label');
                var extra = document.getElementById(targetId);
                if (!extra) return;

                if (extra.style.display === 'none') {
                    extra.style.display = 'block';
                    btn.textContent = '\u2212 Show less';
                } else {
                    extra.style.display = 'none';
                    btn.textContent = '+ ' + count + ' more ' + label;
                }
            });
        });
    });
    </script>

    {{-- ── FOOTER ──────────────────────────────────────────────────────── --}}
    <div class="mis-footer">
        <i class="fas fa-shield-alt" style="color:#2563eb; margin-right:6px;"></i>
        Report generated at <strong>{{ now()->format('h:i A') }}</strong> &nbsp;·&nbsp;
        All data from live database &nbsp;·&nbsp;
        {{ $school->name ?? 'Pragya School' }}
    </div>

</div>
@endsection
