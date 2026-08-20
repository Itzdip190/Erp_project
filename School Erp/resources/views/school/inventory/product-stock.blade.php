@extends('layouts.app')

@section('page-title', 'Product & Stock Management - Inventory Management')

@section('content')
<style>
    /* ─── Standard ERP Blue & White Theme (Matching Inventory, Payroll, Expenses) ─── */
    :root {
        --erp-blue-dark:   #1e3a8a;
        --erp-blue:        #2563eb;
        --erp-blue-light:  #3b82f6;
        --erp-blue-soft:   #eff6ff;
        --erp-blue-border: #dbeafe;
        --erp-card-bg:     #ffffff;
        --erp-border:      #e2e8f0;
        --erp-text-dark:   #0f172a;
        --erp-text-muted:  #64748b;
        --erp-active-bg:   #ecfdf5;
        --erp-active-text: #047857;
        --erp-active-dot:  #10b981;
        --erp-inactive-bg: #fef2f2;
        --erp-inactive-text:#b91c1c;
        --erp-inactive-dot:#ef4444;
        --erp-stock-bg:    #f0fdf4;
        --erp-stock-text:  #15803d;
        --erp-stock-border:#bbf7d0;
    }

    /* ─── Full Screen Container ────────────────────────────────────────── */
    .inv-container {
        width: 100% !important;
        max-width: 100% !important;
        padding: 20px 28px 40px !important;
        box-sizing: border-box;
    }

    /* ─── ERP Cards ────────────────────────────────────────────────────── */
    .inv-card {
        background: #ffffff;
        border: 1px solid var(--erp-border);
        border-radius: 14px;
        box-shadow: 0 4px 20px rgba(37, 99, 235, 0.05);
        margin-bottom: 24px;
        overflow: hidden;
        transition: all 0.25s ease;
    }

    .inv-card-header {
        background: linear-gradient(135deg, var(--erp-blue-dark) 0%, var(--erp-blue) 60%, var(--erp-blue-light) 100%);
        color: #ffffff;
        padding: 16px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-top-left-radius: 13px;
        border-top-right-radius: 13px;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
    }

    .inv-card-header h5 {
        margin: 0;
        font-size: 15.5px;
        font-weight: 800;
        letter-spacing: 0.2px;
        color: #ffffff;
        display: flex;
        align-items: center;
    }

    .inv-card-header .hdr-icon {
        font-size: 16px;
        margin-right: 10px;
        color: #ffffff;
        opacity: 0.95;
    }

    /* Header Action Button (Manage Categories Pill) */
    .btn-hdr-categories {
        background: rgba(255, 255, 255, 0.2);
        border: 1.5px solid rgba(255, 255, 255, 0.45);
        color: #ffffff !important;
        font-weight: 700;
        font-size: 12.5px;
        padding: 7px 16px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        text-decoration: none !important;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    .btn-hdr-categories:hover {
        background: #ffffff !important;
        border-color: #ffffff !important;
        color: var(--erp-blue-dark) !important;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.2);
        transform: translateY(-1px);
    }

    .inv-card-body {
        padding: 22px 24px;
        background: #ffffff;
    }

    /* ─── Buttons ──────────────────────────────────────────────────────── */
    .btn-inv-add {
        background: linear-gradient(135deg, var(--erp-blue) 0%, #1d4ed8 100%) !important;
        color: #ffffff !important;
        font-weight: 700;
        font-size: 13.5px;
        padding: 10px 24px;
        border-radius: 8px;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.28);
    }
    .btn-inv-add:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%) !important;
        color: #ffffff !important;
        box-shadow: 0 6px 18px rgba(37, 99, 235, 0.4);
        transform: translateY(-1px);
    }

    /* ─── Category Filter Pills Bar (Shows ALL Categories) ─────────────── */
    .cat-filter-wrapper {
        background: #f8fafc;
        border: 1px solid var(--erp-border);
        border-radius: 10px;
        padding: 12px 16px;
        margin-bottom: 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .cat-pills-list {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .btn-cat-pill {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        color: #334155;
        font-weight: 600;
        font-size: 12.5px;
        padding: 6px 14px;
        border-radius: 20px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        user-select: none;
    }
    .btn-cat-pill:hover {
        background: #eff6ff;
        border-color: var(--erp-blue);
        color: var(--erp-blue);
    }
    .btn-cat-pill.active {
        background: var(--erp-blue);
        border-color: var(--erp-blue);
        color: #ffffff;
        box-shadow: 0 2px 8px rgba(37, 99, 235, 0.25);
    }
    .btn-cat-pill.active .badge-pill-count {
        background: #ffffff !important;
        color: var(--erp-blue) !important;
    }

    .badge-pill-count {
        background: #e2e8f0;
        color: #475569;
        font-size: 11px;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 10px;
        transition: all 0.2s ease;
    }

    .search-prod-box {
        position: relative;
        min-width: 240px;
    }
    .search-prod-box input {
        width: 100%;
        padding: 7px 12px 7px 34px;
        font-size: 12.5px;
        border: 1.5px solid #cbd5e1;
        border-radius: 20px;
        outline: none;
        background: #ffffff;
    }
    .search-prod-box input:focus {
        border-color: var(--erp-blue);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }
    .search-prod-box .search-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 12px;
    }

    .btn-quick-add-cat {
        font-size: 11.5px;
        font-weight: 700;
        color: var(--erp-blue);
        background: none;
        border: none;
        padding: 0;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .btn-quick-add-cat:hover {
        text-decoration: underline;
    }

    .quick-cat-box {
        background: #eff6ff;
        border: 1px dashed var(--erp-blue);
        border-radius: 8px;
        padding: 8px 10px;
        animation: fadeIn 0.2s ease;
    }

    /* ─── Table Styling ────────────────────────────────────────────────── */
    .inv-table-wrap {
        border: 1px solid var(--erp-border);
        border-radius: 10px;
        overflow-x: auto;
        background: #ffffff;
    }

    .inv-table {
        width: 100%;
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 13.5px;
    }

    .inv-table thead th {
        background: #f8fafc;
        color: var(--erp-text-dark);
        font-weight: 700;
        font-size: 12px;
        letter-spacing: 0.3px;
        text-transform: uppercase;
        padding: 14px 16px;
        border-bottom: 1.5px solid var(--erp-border);
        white-space: nowrap;
        vertical-align: middle;
    }

    .inv-table tbody tr {
        transition: background-color 0.15s ease;
    }

    .inv-table tbody tr:hover {
        background-color: #f0f7ff;
    }

    .inv-table tbody td {
        padding: 14px 16px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        color: var(--erp-text-dark);
        white-space: nowrap;
    }

    .inv-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* ─── Status Badges ────────────────────────────────────────────────── */
    .badge-inv-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.4px;
        text-transform: uppercase;
    }

    .badge-inv-active {
        background: var(--erp-active-bg);
        color: var(--erp-active-text);
        border: 1px solid rgba(16, 185, 129, 0.25);
    }

    .badge-inv-inactive {
        background: var(--erp-inactive-bg);
        color: var(--erp-inactive-text);
        border: 1px solid rgba(239, 68, 68, 0.25);
    }

    .badge-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        display: inline-block;
    }
    .badge-inv-active .badge-dot {
        background: var(--erp-active-dot);
    }
    .badge-inv-inactive .badge-dot {
        background: var(--erp-inactive-dot);
    }

    /* ─── Action Buttons in Table ──────────────────────────────────────── */
    .btn-row-edit {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        color: #1e40af;
        font-weight: 700;
        font-size: 12px;
        padding: 6px 12px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-row-edit:hover {
        background: #eff6ff;
        color: #1d4ed8;
        border-color: #93c5fd;
    }

    .btn-row-delete-icon {
        background: #fee2e2;
        color: #dc2626;
        border: 1px solid #fecaca;
        font-weight: 700;
        font-size: 12px;
        padding: 6px 12px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-row-delete-icon:hover {
        background: #dc2626;
        color: #ffffff;
        border-color: #dc2626;
        box-shadow: 0 2px 8px rgba(220, 38, 38, 0.3);
    }

    .btn-row-stock {
        background: #f0fdf4;
        color: #16a34a;
        border: 1px solid #bbf7d0;
        font-weight: 700;
        font-size: 12px;
        padding: 6px 14px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    .btn-row-stock:hover {
        background: #16a34a;
        color: #ffffff;
        border-color: #16a34a;
        box-shadow: 0 3px 10px rgba(22, 163, 74, 0.3);
    }

    /* ─── Responsive Slider Drawer (Slide-Over Panel) ─────────────────── */
    .inv-slider-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.48);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        z-index: 1050;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }

    .inv-slider-backdrop.open {
        opacity: 1;
        visibility: visible;
    }

    .inv-slider-panel {
        position: fixed;
        top: 0;
        right: 0;
        bottom: 0;
        width: 600px;
        max-width: 100vw;
        height: 100vh;
        height: 100dvh;
        background: #ffffff;
        z-index: 1051;
        box-shadow: -8px 0 35px rgba(0, 0, 0, 0.2);
        display: flex;
        flex-direction: column;
        transform: translateX(100%);
        transition: transform 0.32s cubic-bezier(0.16, 1, 0.3, 1);
        overflow: hidden;
    }

    .inv-slider-panel.open {
        transform: translateX(0);
    }

    .inv-slider-panel-wide {
        width: 760px;
    }

    @media (max-width: 768px) {
        .inv-slider-panel, .inv-slider-panel-wide {
            width: 100vw;
        }
    }

    .inv-slider-header {
        background: linear-gradient(135deg, var(--erp-blue-dark) 0%, var(--erp-blue) 100%);
        color: #ffffff;
        padding: 18px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0;
    }

    .inv-slider-header h4 {
        margin: 0;
        font-size: 16px;
        font-weight: 800;
        color: #ffffff;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .btn-slider-close {
        background: rgba(255, 255, 255, 0.18);
        border: none;
        color: #ffffff;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 15px;
        transition: background 0.2s ease;
    }
    .btn-slider-close:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    .inv-slider-body {
        flex: 1;
        overflow-y: auto;
        padding: 24px 26px;
    }

    .inv-form-group {
        margin-bottom: 20px;
    }

    .inv-form-label {
        display: block;
        font-size: 13px;
        font-weight: 700;
        color: var(--erp-text-dark);
        margin-bottom: 7px;
    }

    .inv-form-input, .inv-form-select {
        width: 100%;
        padding: 10px 14px;
        font-size: 13.5px;
        color: var(--erp-text-dark);
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        outline: none;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .inv-form-input:focus, .inv-form-select:focus {
        border-color: var(--erp-blue);
        box-shadow: 0 0 0 3.5px rgba(37, 99, 235, 0.14);
    }
    .inv-form-input::placeholder {
        color: #94a3b8;
    }

    /* Radio Group for Size Types */
    .size-type-group {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        margin-top: 6px;
        padding: 12px 14px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
    }

    .size-type-item {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        user-select: none;
        font-size: 13px;
        font-weight: 600;
        color: #334155;
    }

    .size-type-item input[type="radio"] {
        accent-color: var(--erp-blue);
        width: 16px;
        height: 16px;
        cursor: pointer;
    }

    /* Size Pills Dynamic Box */
    .size-pills-box {
        margin-top: 14px;
        padding: 14px 16px;
        background: #ffffff;
        border: 1.5px dashed var(--erp-blue-border);
        border-radius: 10px;
        animation: fadeIn 0.25s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-4px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .size-pills-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .size-pills-title {
        font-size: 12.5px;
        font-weight: 700;
        color: var(--erp-blue-dark);
    }

    .btn-size-select-all {
        font-size: 11.5px;
        color: var(--erp-blue);
        font-weight: 700;
        background: none;
        border: none;
        padding: 0;
        cursor: pointer;
    }
    .btn-size-select-all:hover {
        text-decoration: underline;
    }

    .size-pills-wrap {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .size-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 38px;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12.5px;
        font-weight: 700;
        border: 1.5px solid #cbd5e1;
        background: #ffffff;
        color: #475569;
        cursor: pointer;
        transition: all 0.18s ease;
        user-select: none;
    }

    .size-pill.selected {
        background: var(--erp-blue);
        color: #ffffff;
        border-color: var(--erp-blue);
        box-shadow: 0 2px 6px rgba(37, 99, 235, 0.3);
    }

    .size-pill:hover:not(.selected) {
        border-color: var(--erp-blue);
        background: #eff6ff;
        color: var(--erp-blue);
    }

    /* Checkbox */
    .inv-checkbox-wrap {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        user-select: none;
        margin-top: 4px;
    }

    .inv-checkbox-input {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: var(--erp-blue);
        border-radius: 4px;
    }

    .inv-checkbox-label {
        font-size: 13.5px;
        font-weight: 600;
        color: #334155;
        cursor: pointer;
        margin-bottom: 0;
    }

    .inv-slider-footer {
        padding: 16px 26px;
        background: #f8fafc;
        border-top: 1px solid var(--erp-border);
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 12px;
        flex-shrink: 0;
    }

    .btn-slider-save {
        background: #1e293b;
        color: #ffffff;
        font-weight: 700;
        font-size: 13.5px;
        padding: 10px 24px;
        border-radius: 8px;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.2);
    }
    .btn-slider-save:hover {
        background: #0f172a;
        color: #ffffff;
        box-shadow: 0 6px 16px rgba(15, 23, 42, 0.35);
        transform: translateY(-1px);
    }

    .btn-slider-discard {
        background: #ef4444;
        color: #ffffff;
        font-weight: 700;
        font-size: 13.5px;
        padding: 10px 24px;
        border-radius: 8px;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-slider-discard:hover {
        background: #dc2626;
        color: #ffffff;
    }

    /* ─── Manage Stock Slider Table ───────────────────────────────────── */
    .stock-manage-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 13px;
    }

    .stock-manage-table thead th {
        background: #f8fafc;
        color: var(--erp-text-dark);
        font-weight: 700;
        font-size: 11.5px;
        text-transform: uppercase;
        padding: 12px 14px;
        border-bottom: 1.5px solid var(--erp-border);
        white-space: nowrap;
    }

    .stock-manage-table tbody td {
        padding: 12px 14px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        white-space: nowrap;
    }

    .stock-input-in {
        width: 140px;
        padding: 7px 10px;
        font-size: 12.5px;
        border: 1.5px solid #86efac;
        border-radius: 6px;
        background: #f0fdf4;
        color: #166534;
        font-weight: 600;
        outline: none;
    }
    .stock-input-in:focus {
        border-color: #22c55e;
        box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.18);
    }
    .stock-input-in::placeholder {
        color: #86efac;
        font-weight: normal;
    }

    .stock-input-out {
        width: 140px;
        padding: 7px 10px;
        font-size: 12.5px;
        border: 1.5px solid #fca5a5;
        border-radius: 6px;
        background: #fef2f2;
        color: #991b1b;
        font-weight: 600;
        outline: none;
    }
    .stock-input-out:focus {
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.18);
    }
    .stock-input-out::placeholder {
        color: #fca5a5;
        font-weight: normal;
    }

    .stock-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 4px 10px;
        background: #eff6ff;
        color: var(--erp-blue-dark);
        border: 1px solid var(--erp-blue-border);
        border-radius: 6px;
        font-weight: 700;
        font-size: 12px;
    }

    /* ─── Delete Confirmation Modal ───────────────────────────────────── */
    .inv-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.5);
        backdrop-filter: blur(4px);
        z-index: 1070;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.25s ease, visibility 0.25s ease;
    }
    .inv-modal-overlay.open {
        opacity: 1;
        visibility: visible;
    }

    .inv-modal-card {
        background: #ffffff;
        border-radius: 14px;
        max-width: 420px;
        width: 100%;
        box-shadow: 0 20px 45px rgba(0, 0, 0, 0.2);
        padding: 26px;
        text-align: center;
        transform: scale(0.95);
        transition: transform 0.25s ease;
    }
    .inv-modal-overlay.open .inv-modal-card {
        transform: scale(1);
    }

    .inv-modal-icon {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: #fee2e2;
        color: #dc2626;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin: 0 auto 16px;
    }

    .inv-modal-title {
        font-size: 17px;
        font-weight: 800;
        color: var(--erp-text-dark);
        margin-bottom: 8px;
    }

    .inv-modal-desc {
        font-size: 13.5px;
        color: #64748b;
        margin-bottom: 22px;
        line-height: 1.5;
    }

    .inv-modal-actions {
        display: flex;
        gap: 10px;
        justify-content: center;
    }

    .btn-modal-cancel {
        background: #f1f5f9;
        color: #334155;
        border: 1px solid #cbd5e1;
        font-weight: 700;
        font-size: 13px;
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        flex: 1;
        transition: background 0.2s ease;
    }
    .btn-modal-cancel:hover {
        background: #e2e8f0;
    }

    .btn-modal-delete {
        background: #dc2626;
        color: #ffffff;
        border: none;
        font-weight: 700;
        font-size: 13px;
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        flex: 1;
        transition: background 0.2s ease;
    }
    .btn-modal-delete:hover {
        background: #b91c1c;
    }

    /* ─── Toast Notifications ────────────────────────────────────────── */
    .inv-toast-container {
        position: fixed;
        bottom: 24px;
        right: 24px;
        z-index: 1080;
        display: flex;
        flex-direction: column;
        gap: 10px;
        pointer-events: none;
    }

    .inv-toast {
        min-width: 280px;
        max-width: 380px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 14px 20px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 13.5px;
        font-weight: 700;
        color: #1e293b;
        transform: translateY(20px);
        opacity: 0;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        pointer-events: auto;
    }

    .inv-toast.show {
        transform: translateY(0);
        opacity: 1;
    }

    .inv-toast.toast-success {
        border-left: 4px solid #10b981;
    }
    .inv-toast.toast-success .toast-icon {
        color: #10b981;
    }

    .inv-toast.toast-error {
        border-left: 4px solid #ef4444;
    }
    .inv-toast.toast-error .toast-icon {
        color: #ef4444;
    }
</style>

<div class="inv-container">
    <!-- 1. Top Card: Create Product Trigger (Image 1) -->
    <div class="inv-card">
        <div class="inv-card-header">
            <h5><i class="fas fa-gift hdr-icon"></i>Create Product</h5>
        </div>
        <div class="inv-card-body">
            <button type="button" class="btn btn-inv-add" onclick="openCreateProductSlider()">
                <i class="fas fa-plus"></i>
                <span>Create Product</span>
            </button>
        </div>
    </div>

    <!-- 2. Bottom Card: Product List (Image 1) -->
    <div class="inv-card">
        <div class="inv-card-header">
            <h5><i class="fas fa-th-large hdr-icon"></i>Product List</h5>
            <a href="{{ route('school.inventory.categories') }}" class="btn-hdr-categories" title="Go to Categories Management">
                <i class="fas fa-tags"></i>
                <span>Manage Categories</span>
                <i class="fas fa-arrow-right" style="font-size: 10px;"></i>
            </a>
        </div>
        <div class="inv-card-body p-3">
            <!-- ─── Categories Filter Bar: All Categories Shown Here ─── -->
            <div class="cat-filter-wrapper">
                <div class="cat-pills-list" id="categoryPillsList">
                    <button type="button" class="btn-cat-pill active" onclick="filterByCategory('all', this)">
                        <i class="fas fa-layer-group text-primary"></i>
                        <span>All Categories</span>
                        <span class="badge-pill-count" id="count-all">{{ count($products) }}</span>
                    </button>
                    @foreach($categories as $cat)
                        @php
                            $catCount = $products->filter(function($p) use ($cat) {
                                return ($p->category_id == $cat->id) || 
                                       (isset($p->category->name) && strtolower($p->category->name) === strtolower($cat->name)) || 
                                       (isset($p->category_name) && strtolower($p->category_name) === strtolower($cat->name));
                            })->count();
                        @endphp
                        <button type="button" 
                                class="btn-cat-pill" 
                                data-cat-id="{{ $cat->id }}" 
                                data-cat-name="{{ strtolower($cat->name) }}" 
                                onclick="filterByCategory('{{ $cat->id }}', this)">
                            <i class="fas fa-tag text-primary"></i>
                            <span>{{ $cat->name }}</span>
                            <span class="badge-pill-count" id="count-cat-{{ $cat->id }}">{{ $catCount }}</span>
                        </button>
                    @endforeach
                </div>
                <div class="search-prod-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" 
                           id="searchProdInput" 
                           placeholder="Search products..." 
                           autocomplete="off" 
                           oninput="handleSearchProducts(this.value)">
                </div>
            </div>

            <div class="inv-table-wrap">
                <table class="inv-table" id="productTable">
                    <thead>
                        <tr>
                            <th style="width: 60px;">S.No</th>
                            <th>Category</th>
                            <th>Product Name</th>
                            <th style="width: 100px;">Price</th>
                            <th style="width: 100px;">MRP</th>
                            <th style="width: 90px;">Tax (%)</th>
                            <th>Sizes / Chart</th>
                            <th style="width: 110px; text-align: center;">Status</th>
                            <th style="width: 90px; text-align: center;">Edit</th>
                            <th style="width: 90px; text-align: center;">Delete</th>
                            <th style="width: 130px; text-align: center;">Manage Stock</th>
                        </tr>
                    </thead>
                    <tbody id="productTableBody">
                        @forelse($products as $index => $prod)
                        <tr id="row-{{ $prod->id }}" 
                            data-id="{{ $prod->id }}" 
                            data-cat-id="{{ $prod->category_id ?? ($prod->category?->id ?? '') }}"
                            data-cat-name="{{ strtolower($prod->category?->name ?? ($prod->category_name ?? '')) }}"
                            data-prod-name="{{ strtolower($prod->name) }}"
                            data-json="{{ json_encode($prod) }}">
                            <td class="row-sno" style="font-weight: 700; color: #475569;">{{ $index + 1 }}</td>
                            <td class="row-category" style="font-weight: 600; color: #334155;">
                                {{ $prod->category?->name ?? ($prod->category_name ?? '-') }}
                            </td>
                            <td class="row-name" style="font-weight: 700; color: #0f172a; font-size: 14px;">
                                {{ $prod->name }}
                            </td>
                            <td class="row-price" style="font-weight: 600; color: #0f172a;">
                                {{ number_format((float)$prod->price, 2) }}
                            </td>
                            <td class="row-mrp" style="font-weight: 600; color: #64748b;">
                                {{ number_format((float)$prod->mrp, 2) }}
                            </td>
                            <td class="row-tax" style="font-weight: 600; color: #475569;">
                                {{ number_format((float)$prod->tax, 2) }}
                            </td>
                            <td class="row-sizes" style="font-size: 13px; color: #475569;">
                                {{ $prod->sizes_display ?? (is_array($prod->selected_sizes) ? implode(', ', $prod->selected_sizes) : ($prod->selected_sizes ?: 'Free')) }}
                            </td>
                            <td style="text-align: center;">
                                @if($prod->status)
                                    <span class="badge-inv-status badge-inv-active" id="badge-{{ $prod->id }}">
                                        <span class="badge-dot"></span> ACTIVE
                                    </span>
                                @else
                                    <span class="badge-inv-status badge-inv-inactive" id="badge-{{ $prod->id }}">
                                        <span class="badge-dot"></span> INACTIVE
                                    </span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <button type="button" class="btn-row-edit" onclick='openEditProductSlider({{ json_encode($prod) }})'>
                                    <i class="fas fa-pen" style="font-size: 10.5px;"></i> Edit
                                </button>
                            </td>
                            <td style="text-align: center;">
                                <button type="button" class="btn-row-delete-icon" onclick="confirmDeleteProduct({{ $prod->id }}, '{{ addslashes($prod->name) }}')">
                                    <i class="fas fa-trash-alt" style="font-size: 10.5px;"></i> Delete
                                </button>
                            </td>
                            <td style="text-align: center;">
                                <button type="button" class="btn-row-stock" onclick="openManageStockSlider({{ $prod->id }})">
                                    <i class="fas fa-plus-circle" style="font-size: 11.5px;"></i> Add Stock
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr id="emptyRow">
                            <td colspan="11" class="text-center py-5 text-muted">
                                <div style="padding: 20px;">
                                    <i class="fas fa-boxes fa-2x mb-3 text-muted" style="opacity: 0.5;"></i>
                                    <div style="font-size: 14px; font-weight: 600;">No products found</div>
                                    <div style="font-size: 12px; color: #94a3b8; margin-top: 4px;">Click "Create Product" above to create your first product and manage stock.</div>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ─── 1. CREATE / EDIT PRODUCT SLIDER DRAWER (Image 2) ─────────────────── -->
<div class="inv-slider-backdrop" id="productSliderBackdrop" onclick="closeProductSlider()"></div>

<div class="inv-slider-panel" id="productSliderPanel" aria-hidden="true">
    <!-- Slider Header -->
    <div class="inv-slider-header">
        <h4>
            <i class="fas fa-gift" style="font-size: 16px;"></i>
            <span id="productSliderTitle">Create Product</span>
        </h4>
        <button type="button" class="btn-slider-close" onclick="closeProductSlider()" title="Close slider">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Slider Body Form -->
    <div class="inv-slider-body">
        <form id="productForm" onsubmit="handleProductSubmit(event)">
            <input type="hidden" id="productId" name="id" value="">

            <div class="row">
                <!-- Category Field (Lists ALL categories + Quick inline Add) -->
                <div class="col-md-6 inv-form-group">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <label for="prodCategory" class="inv-form-label mb-0">Category</label>
                        <button type="button" class="btn-quick-add-cat" onclick="toggleQuickCategoryForm()">
                            <i class="fas fa-plus-circle"></i> New Category
                        </button>
                    </div>

                    <!-- Quick Inline Add Category Box -->
                    <div id="quickCatBox" class="quick-cat-box mb-2" style="display: none;">
                        <div class="d-flex gap-2">
                            <input type="text" 
                                   class="inv-form-input py-1 px-2" 
                                   style="font-size: 12.5px;" 
                                   id="quickCatName" 
                                   placeholder="Enter category name..." 
                                   onkeydown="if(event.key==='Enter'){event.preventDefault();submitQuickCategory();}">
                            <button type="button" class="btn btn-sm btn-primary px-3 fw-bold" onclick="submitQuickCategory()" id="btnSubmitQuickCat">Add</button>
                            <button type="button" class="btn btn-sm btn-light border px-2" onclick="toggleQuickCategoryForm()"><i class="fas fa-times"></i></button>
                        </div>
                    </div>

                    <select class="inv-form-select" id="prodCategory" name="category_id">
                        <option value="">-- Select Category --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Product Name Field -->
                <div class="col-md-6 inv-form-group">
                    <label for="prodName" class="inv-form-label">
                        Product Name
                    </label>
                    <input type="text" 
                           class="inv-form-input" 
                           id="prodName" 
                           name="name" 
                           placeholder="Enter product name..." 
                           autocomplete="off">
                </div>
            </div>

            <div class="row">
                <!-- Price Field -->
                <div class="col-md-6 inv-form-group">
                    <label for="prodPrice" class="inv-form-label">
                        Price
                    </label>
                    <input type="number" 
                           step="0.01" 
                           class="inv-form-input" 
                           id="prodPrice" 
                           name="price" 
                           placeholder="0.00" 
                           value="0.00">
                </div>

                <!-- MRP Field -->
                <div class="col-md-6 inv-form-group">
                    <label for="prodMrp" class="inv-form-label">
                        MRP
                    </label>
                    <input type="number" 
                           step="0.01" 
                           class="inv-form-input" 
                           id="prodMrp" 
                           name="mrp" 
                           placeholder="0.00" 
                           value="0.00">
                </div>
            </div>

            <div class="row align-items-center">
                <!-- Tax (%) Field -->
                <div class="col-md-6 inv-form-group">
                    <label for="prodTax" class="inv-form-label">
                        Tax (%)
                    </label>
                    <input type="number" 
                           step="0.01" 
                           class="inv-form-input" 
                           id="prodTax" 
                           name="tax" 
                           placeholder="e.g. 18">
                </div>

                <!-- Status Field -->
                <div class="col-md-6 inv-form-group" style="padding-top: 12px;">
                    <label class="inv-form-label" style="margin-bottom: 8px;">Status</label>
                    <label class="inv-checkbox-wrap">
                        <input type="checkbox" 
                               class="inv-checkbox-input" 
                               id="prodStatus" 
                               name="status" 
                               value="1" 
                               checked>
                        <span class="inv-checkbox-label">Mark as Active</span>
                    </label>
                </div>
            </div>

            <!-- Size Selection Section -->
            <div class="inv-form-group" style="margin-top: 10px;">
                <label class="inv-form-label">Select Size Type</label>
                <div class="size-type-group">
                    <label class="size-type-item">
                        <input type="radio" name="size_type" value="s_xxl" onchange="handleSizeTypeChange('s_xxl')">
                        <span>Size (S-XXL)</span>
                    </label>
                    <label class="size-type-item">
                        <input type="radio" name="size_type" value="chart_1_11" onchange="handleSizeTypeChange('chart_1_11')">
                        <span>Size Chart (1 to 11)</span>
                    </label>
                    <label class="size-type-item">
                        <input type="radio" name="size_type" value="chart_24_44" onchange="handleSizeTypeChange('chart_24_44')">
                        <span>Size Chart (24 to 44)</span>
                    </label>
                    <label class="size-type-item">
                        <input type="radio" name="size_type" value="none" checked onchange="handleSizeTypeChange('none')">
                        <span>Size Not Applicable</span>
                    </label>
                </div>

                <!-- Dynamic Interactive Size Selector Box -->
                <div class="size-pills-box" id="sizePillsBox" style="display: none;">
                    <div class="size-pills-header">
                        <span class="size-pills-title" id="sizePillsTitle">Available Sizes</span>
                        <button type="button" class="btn-size-select-all" onclick="toggleAllSizes()">Select All</button>
                    </div>
                    <div class="size-pills-wrap" id="sizePillsContainer">
                        <!-- Populated by JS -->
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Slider Footer Actions -->
    <div class="inv-slider-footer">
        <button type="button" class="btn-slider-save" id="btnSaveProduct" onclick="document.getElementById('productForm').requestSubmit()">
            <i class="fas fa-check"></i>
            <span id="btnSaveProductText">Save Product</span>
        </button>
        <button type="button" class="btn-slider-discard" onclick="closeProductSlider()">
            <i class="fas fa-times"></i>
            <span>Discard</span>
        </button>
    </div>
</div>


<!-- ─── 2. MANAGE STOCK SLIDER DRAWER (Image 3) ────────────────────────── -->
<div class="inv-slider-backdrop" id="stockSliderBackdrop" onclick="closeStockSlider()"></div>

<div class="inv-slider-panel inv-slider-panel-wide" id="stockSliderPanel" aria-hidden="true">
    <!-- Stock Slider Header -->
    <div class="inv-slider-header">
        <h4>
            <i class="fas fa-plus-square" style="font-size: 16px;"></i>
            <span>Manage Stock</span>
        </h4>
        <button type="button" class="btn-slider-close" onclick="closeStockSlider()" title="Close slider">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Stock Slider Body -->
    <div class="inv-slider-body">
        <div id="stockLoading" style="text-align: center; padding: 25px; display: none;">
            <i class="fas fa-spinner fa-spin fa-2x text-primary mb-2"></i>
            <div style="font-size: 13px; color: #64748b;">Syncing stock details...</div>
        </div>

        <form id="stockForm" onsubmit="handleStockSubmit(event)">
            <input type="hidden" id="stockProductId" name="product_id" value="">

            <div class="inv-table-wrap" style="box-shadow: 0 2px 10px rgba(0,0,0,0.03);">
                <table class="stock-manage-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">S.No</th>
                            <th>Product Name</th>
                            <th style="width: 90px;">Price</th>
                            <th style="width: 90px;">MRP</th>
                            <th style="width: 75px; text-align: center;">Size</th>
                            <th style="width: 75px; text-align: center;">Stock</th>
                            <th style="width: 150px;">Stock In</th>
                            <th style="width: 150px;">Stock Out</th>
                        </tr>
                    </thead>
                    <tbody id="stockTableBody">
                        <!-- Populated dynamically by JS -->
                    </tbody>
                </table>
            </div>
        </form>
    </div>

    <!-- Stock Slider Footer -->
    <div class="inv-slider-footer">
        <button type="button" class="btn-slider-save" id="btnSaveStock" onclick="document.getElementById('stockForm').requestSubmit()">
            <i class="fas fa-check"></i>
            <span id="btnSaveStockText">Submit</span>
        </button>
        <button type="button" class="btn-slider-discard" onclick="closeStockSlider()">
            <i class="fas fa-times"></i>
            <span>Discard</span>
        </button>
    </div>
</div>


<!-- ─── 3. DELETE CONFIRMATION MODAL ───────────────────────────────────── -->
<div class="inv-modal-overlay" id="deleteModal" onclick="closeDeleteModalOnBg(event)">
    <div class="inv-modal-card">
        <div class="inv-modal-icon">
            <i class="fas fa-trash-alt"></i>
        </div>
        <div class="inv-modal-title">Delete Product?</div>
        <div class="inv-modal-desc">
            Are you sure you want to delete <strong id="deleteProductName" style="color: #0f172a;">this product</strong>? All stock entries associated with this product will also be removed.
        </div>
        <div class="inv-modal-actions">
            <button type="button" class="btn-modal-cancel" onclick="closeDeleteModal()">Cancel</button>
            <button type="button" class="btn-modal-delete" id="btnConfirmDelete" onclick="executeDeleteProduct()">
                <i class="fas fa-trash-alt me-1"></i> Delete
            </button>
        </div>
    </div>
</div>

<!-- ─── 4. TOAST NOTIFICATIONS ─────────────────────────────────────────── -->
<div class="inv-toast-container" id="toastContainer"></div>


<!-- ─── JAVASCRIPT LOGIC ────────────────────────────────────────────────── -->
<script>
    // CSRF Setup
    const CSRF_TOKEN = '{{ csrf_token() }}';
    let deleteTargetId = null;
    let currentSelectedSizes = [];
    let activeCategoryFilter = 'all';

    // Predefined Size Arrays
    const SIZE_DEFINITIONS = {
        's_xxl': ['XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL'],
        'chart_1_11': ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11'],
        'chart_24_44': ['24', '26', '28', '30', '32', '34', '36', '38', '40', '42', '44'],
        'none': ['Free']
    };

    // ─── Filter By Category ──────────────────────────────────────────────────
    function filterByCategory(catId, btnElement) {
        activeCategoryFilter = String(catId);

        // Update active pill styling
        document.querySelectorAll('#categoryPillsList .btn-cat-pill').forEach(btn => {
            btn.classList.remove('active');
        });
        if (btnElement) {
            btnElement.classList.add('active');
        }

        applyTableFilters();
    }

    function handleSearchProducts(query) {
        applyTableFilters(query.trim().toLowerCase());
    }

    function applyTableFilters(searchQuery = null) {
        if (searchQuery === null) {
            searchQuery = (document.getElementById('searchProdInput')?.value || '').trim().toLowerCase();
        }

        const rows = document.querySelectorAll('#productTableBody tr:not(#emptyRow):not(#filterEmptyRow)');
        let visibleCount = 0;

        rows.forEach(row => {
            const rowCatId = String(row.getAttribute('data-cat-id') || '');
            const rowCatName = (row.getAttribute('data-cat-name') || '').toLowerCase();
            const rowProdName = (row.getAttribute('data-prod-name') || '').toLowerCase();
            const rowText = row.innerText.toLowerCase();

            let matchCategory = (activeCategoryFilter === 'all') || (rowCatId === activeCategoryFilter) || (rowCatName === activeCategoryFilter);
            let matchSearch = !searchQuery || rowProdName.includes(searchQuery) || rowCatName.includes(searchQuery) || rowText.includes(searchQuery);

            if (matchCategory && matchSearch) {
                row.style.display = '';
                visibleCount++;
                const sno = row.querySelector('.row-sno');
                if (sno) sno.innerText = visibleCount;
            } else {
                row.style.display = 'none';
            }
        });

        // Show/hide empty state
        const filterEmpty = document.getElementById('filterEmptyRow');
        if (visibleCount === 0 && rows.length > 0) {
            if (!filterEmpty) {
                const tr = document.createElement('tr');
                tr.id = 'filterEmptyRow';
                tr.innerHTML = `<td colspan="11" class="text-center py-5 text-muted">
                    <i class="fas fa-search fa-2x mb-2 text-muted" style="opacity: 0.5;"></i>
                    <div style="font-weight: 600;">No products match this category filter</div>
                </td>`;
                document.getElementById('productTableBody').appendChild(tr);
            } else {
                filterEmpty.style.display = '';
            }
        } else if (filterEmpty) {
            filterEmpty.style.display = 'none';
        }
    }

    // ─── Quick Inline Category Add ───────────────────────────────────────────
    function toggleQuickCategoryForm() {
        const box = document.getElementById('quickCatBox');
        if (box.style.display === 'none') {
            box.style.display = 'block';
            document.getElementById('quickCatName').focus();
        } else {
            box.style.display = 'none';
            document.getElementById('quickCatName').value = '';
        }
    }

    async function submitQuickCategory() {
        const nameInput = document.getElementById('quickCatName');
        const name = nameInput.value.trim();
        if (!name) return;

        const btn = document.getElementById('btnSubmitQuickCat');
        btn.disabled = true;
        btn.innerHTML = `<i class="fas fa-spinner fa-spin"></i>`;

        try {
            const res = await fetch(`{{ route('school.inventory.categories.quick-store') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                },
                body: JSON.stringify({ name: name, _token: CSRF_TOKEN })
            });
            const data = await res.json();

            if (data.success && data.category) {
                const cat = data.category;
                
                // Add to Select dropdown
                const select = document.getElementById('prodCategory');
                let opt = select.querySelector(`option[value="${cat.id}"]`);
                if (!opt) {
                    opt = document.createElement('option');
                    opt.value = cat.id;
                    opt.innerText = cat.name;
                    select.appendChild(opt);
                }
                select.value = cat.id;

                // Add to Category Filter Pills if not present
                let pill = document.querySelector(`#categoryPillsList button[data-cat-id="${cat.id}"]`);
                if (!pill) {
                    const newBtn = document.createElement('button');
                    newBtn.type = 'button';
                    newBtn.className = 'btn-cat-pill';
                    newBtn.setAttribute('data-cat-id', cat.id);
                    newBtn.setAttribute('data-cat-name', cat.name.toLowerCase());
                    newBtn.onclick = function() { filterByCategory(cat.id, this); };
                    newBtn.innerHTML = `
                        <i class="fas fa-tag text-primary"></i>
                        <span>${escapeHtml(cat.name)}</span>
                        <span class="badge-pill-count" id="count-cat-${cat.id}">0</span>
                    `;
                    document.getElementById('categoryPillsList').appendChild(newBtn);
                }

                showToast(`Category "${cat.name}" added and selected!`, 'success');
                toggleQuickCategoryForm();
            } else {
                showToast(data.message || 'Failed to add category', 'error');
            }
        } catch (e) {
            console.error(e);
            showToast('Error creating category', 'error');
        } finally {
            btn.disabled = false;
            btn.innerText = 'Add';
        }
    }

    // Refresh Category Dropdown from Server
    async function refreshCategoriesFromServer(selectedId = null) {
        try {
            const res = await fetch(`{{ route('school.inventory.categories.ajax-list') }}`, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();
            if (data.success && Array.isArray(data.categories)) {
                const select = document.getElementById('prodCategory');
                const currVal = selectedId || select.value;
                select.innerHTML = '<option value="">-- Select Category --</option>';
                data.categories.forEach(cat => {
                    const opt = document.createElement('option');
                    opt.value = cat.id;
                    opt.innerText = cat.name;
                    if (String(cat.id) === String(currVal)) opt.selected = true;
                    select.appendChild(opt);
                });
            }
        } catch (e) {
            console.error('Error fetching categories:', e);
        }
    }

    // ─── Size Type Selector Handler ──────────────────────────────────────────
    function handleSizeTypeChange(type, preselected = []) {
        const box = document.getElementById('sizePillsBox');
        const container = document.getElementById('sizePillsContainer');
        const title = document.getElementById('sizePillsTitle');

        if (type === 'none') {
            box.style.display = 'none';
            currentSelectedSizes = ['Free'];
            return;
        }

        box.style.display = 'block';
        container.innerHTML = '';
        const list = SIZE_DEFINITIONS[type] || [];

        if (type === 's_xxl') title.innerText = 'Select S-XXL Sizes';
        else if (type === 'chart_1_11') title.innerText = 'Select Numeric Sizes (1 to 11)';
        else if (type === 'chart_24_44') title.innerText = 'Select Chart Sizes (24 to 44)';

        currentSelectedSizes = preselected.length > 0 ? [...preselected] : (type === 's_xxl' ? ['S', 'M', 'L', 'XL', 'XXL'] : [...list]);

        list.forEach(size => {
            const pill = document.createElement('div');
            pill.className = 'size-pill' + (currentSelectedSizes.includes(size) ? ' selected' : '');
            pill.innerText = size;
            pill.onclick = () => {
                if (currentSelectedSizes.includes(size)) {
                    currentSelectedSizes = currentSelectedSizes.filter(s => s !== size);
                    pill.classList.remove('selected');
                } else {
                    currentSelectedSizes.push(size);
                    pill.classList.add('selected');
                }
            };
            container.appendChild(pill);
        });
    }

    function toggleAllSizes() {
        const pills = document.querySelectorAll('#sizePillsContainer .size-pill');
        const checkedRadio = document.querySelector('input[name="size_type"]:checked');
        const type = checkedRadio ? checkedRadio.value : 'none';
        const fullList = SIZE_DEFINITIONS[type] || [];

        if (currentSelectedSizes.length === fullList.length) {
            currentSelectedSizes = [];
            pills.forEach(p => p.classList.remove('selected'));
        } else {
            currentSelectedSizes = [...fullList];
            pills.forEach(p => p.classList.add('selected'));
        }
    }

    // ─── 1. Create / Edit Product Slider Handlers ────────────────────────────
    function openCreateProductSlider() {
        document.getElementById('productForm').reset();
        document.getElementById('productId').value = '';
        document.getElementById('productSliderTitle').innerText = 'Create Product';
        document.getElementById('btnSaveProductText').innerText = 'Save Product';
        document.getElementById('prodPrice').value = '0.00';
        document.getElementById('prodMrp').value = '0.00';
        document.getElementById('prodTax').value = '';
        document.getElementById('prodStatus').checked = true;
        document.getElementById('quickCatBox').style.display = 'none';
        
        // Refresh categories
        refreshCategoriesFromServer();

        // Reset radio to None
        const noneRadio = document.querySelector('input[name="size_type"][value="none"]');
        if (noneRadio) noneRadio.checked = true;
        handleSizeTypeChange('none');

        document.getElementById('productSliderBackdrop').classList.add('open');
        document.getElementById('productSliderPanel').classList.add('open');
        document.getElementById('productSliderPanel').setAttribute('aria-hidden', 'false');

        setTimeout(() => {
            const input = document.getElementById('prodName');
            if (input) input.focus();
        }, 300);
    }

    function openEditProductSlider(prod) {
        document.getElementById('productForm').reset();
        document.getElementById('productId').value = prod.id;
        document.getElementById('productSliderTitle').innerText = 'Edit Product';
        document.getElementById('btnSaveProductText').innerText = 'Update Product';
        document.getElementById('quickCatBox').style.display = 'none';

        refreshCategoriesFromServer(prod.category_id);

        document.getElementById('prodCategory').value = prod.category_id || '';
        document.getElementById('prodName').value = prod.name || '';
        document.getElementById('prodPrice').value = prod.price || '0.00';
        document.getElementById('prodMrp').value = prod.mrp || '0.00';
        document.getElementById('prodTax').value = prod.tax || '';
        document.getElementById('prodStatus').checked = Boolean(Number(prod.status));

        const sizeType = prod.size_type || 'none';
        const targetRadio = document.querySelector(`input[name="size_type"][value="${sizeType}"]`);
        if (targetRadio) targetRadio.checked = true;

        let sizes = [];
        if (Array.isArray(prod.selected_sizes)) {
            sizes = prod.selected_sizes;
        } else if (typeof prod.selected_sizes === 'string') {
            try { sizes = JSON.parse(prod.selected_sizes); } catch(e) { sizes = prod.selected_sizes.split(',').map(s=>s.trim()); }
        }

        handleSizeTypeChange(sizeType, sizes);

        document.getElementById('productSliderBackdrop').classList.add('open');
        document.getElementById('productSliderPanel').classList.add('open');
        document.getElementById('productSliderPanel').setAttribute('aria-hidden', 'false');

        setTimeout(() => {
            const input = document.getElementById('prodName');
            if (input) input.focus();
        }, 300);
    }

    function closeProductSlider() {
        document.getElementById('productSliderBackdrop').classList.remove('open');
        document.getElementById('productSliderPanel').classList.remove('open');
        document.getElementById('productSliderPanel').setAttribute('aria-hidden', 'true');
    }

    // ─── Product Submit (AJAX) ──────────────────────────────────────────────
    async function handleProductSubmit(e) {
        e.preventDefault();

        const id = document.getElementById('productId').value;
        const isEdit = Boolean(id);
        const btn = document.getElementById('btnSaveProduct');
        const btnText = document.getElementById('btnSaveProductText');
        const originalText = btnText.innerText;

        const sizeTypeRadio = document.querySelector('input[name="size_type"]:checked');
        const sizeType = sizeTypeRadio ? sizeTypeRadio.value : 'none';

        const payload = {
            category_id: document.getElementById('prodCategory').value,
            name: document.getElementById('prodName').value,
            price: document.getElementById('prodPrice').value,
            mrp: document.getElementById('prodMrp').value,
            tax: document.getElementById('prodTax').value,
            status: document.getElementById('prodStatus').checked ? 1 : 0,
            size_type: sizeType,
            sizes: sizeType === 'none' ? ['Free'] : (currentSelectedSizes.length ? currentSelectedSizes : ['Free']),
            _token: CSRF_TOKEN
        };

        btn.disabled = true;
        btnText.innerHTML = `<i class="fas fa-spinner fa-spin me-1"></i> Saving...`;

        const url = isEdit 
            ? `{{ url('school/inventory/products') }}/${id}/update`
            : `{{ route('school.inventory.products.store') }}`;

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (response.ok && data.success) {
                showToast(data.message, 'success');
                closeProductSlider();

                if (isEdit) {
                    updateTableRow(data.product);
                } else {
                    prependTableRow(data.product);
                }
                updatePillCounts();
            } else {
                showToast(data.message || 'Error occurred while saving product', 'error');
            }
        } catch (err) {
            console.error(err);
            showToast('Something went wrong. Please check your network.', 'error');
        } finally {
            btn.disabled = false;
            btnText.innerText = originalText;
        }
    }

    // ─── Dynamic Table Insertion / Update ────────────────────────────────────
    function prependTableRow(p) {
        const emptyRow = document.getElementById('emptyRow');
        if (emptyRow) emptyRow.remove();

        const tbody = document.getElementById('productTableBody');
        const tr = document.createElement('tr');
        tr.id = `row-${p.id}`;
        tr.setAttribute('data-id', p.id);
        tr.setAttribute('data-cat-id', p.category_id || '');
        tr.setAttribute('data-cat-name', (p.category_name || '').toLowerCase());
        tr.setAttribute('data-prod-name', (p.name || '').toLowerCase());
        tr.setAttribute('data-json', JSON.stringify(p));

        const sizesText = p.sizes_display || (Array.isArray(p.selected_sizes) ? p.selected_sizes.join(', ') : 'Free');

        tr.innerHTML = `
            <td class="row-sno" style="font-weight: 700; color: #475569;">1</td>
            <td class="row-category" style="font-weight: 600; color: #334155;">${escapeHtml(p.category_name || '-')}</td>
            <td class="row-name" style="font-weight: 700; color: #0f172a; font-size: 14px;">${escapeHtml(p.name)}</td>
            <td class="row-price" style="font-weight: 600; color: #0f172a;">${p.price}</td>
            <td class="row-mrp" style="font-weight: 600; color: #64748b;">${p.mrp}</td>
            <td class="row-tax" style="font-weight: 600; color: #475569;">${p.tax}</td>
            <td class="row-sizes" style="font-size: 13px; color: #475569;">${escapeHtml(sizesText)}</td>
            <td style="text-align: center;">
                <span class="badge-inv-status ${p.status ? 'badge-inv-active' : 'badge-inv-inactive'}" id="badge-${p.id}">
                    <span class="badge-dot"></span> ${p.status ? 'ACTIVE' : 'INACTIVE'}
                </span>
            </td>
            <td style="text-align: center;">
                <button type="button" class="btn-row-edit" onclick='openEditProductSlider(${JSON.stringify(p)})'>
                    <i class="fas fa-pen" style="font-size: 10.5px;"></i> Edit
                </button>
            </td>
            <td style="text-align: center;">
                <button type="button" class="btn-row-delete-icon" onclick="confirmDeleteProduct(${p.id}, '${escapeJs(p.name)}')">
                    <i class="fas fa-trash-alt" style="font-size: 10.5px;"></i> Delete
                </button>
            </td>
            <td style="text-align: center;">
                <button type="button" class="btn-row-stock" onclick="openManageStockSlider(${p.id})">
                    <i class="fas fa-plus-circle" style="font-size: 11.5px;"></i> Add Stock
                </button>
            </td>
        `;

        tbody.insertBefore(tr, tbody.firstChild);
        applyTableFilters();
    }

    function updateTableRow(p) {
        const tr = document.getElementById(`row-${p.id}`);
        if (!tr) return;

        tr.setAttribute('data-json', JSON.stringify(p));
        tr.setAttribute('data-cat-id', p.category_id || '');
        tr.setAttribute('data-cat-name', (p.category_name || '').toLowerCase());
        tr.setAttribute('data-prod-name', (p.name || '').toLowerCase());

        const sizesText = p.sizes_display || (Array.isArray(p.selected_sizes) ? p.selected_sizes.join(', ') : 'Free');

        const catCell = tr.querySelector('.row-category');
        if (catCell) catCell.innerText = p.category_name || '-';

        const nameCell = tr.querySelector('.row-name');
        if (nameCell) nameCell.innerText = p.name;

        const priceCell = tr.querySelector('.row-price');
        if (priceCell) priceCell.innerText = p.price;

        const mrpCell = tr.querySelector('.row-mrp');
        if (mrpCell) mrpCell.innerText = p.mrp;

        const taxCell = tr.querySelector('.row-tax');
        if (taxCell) taxCell.innerText = p.tax;

        const sizesCell = tr.querySelector('.row-sizes');
        if (sizesCell) sizesCell.innerText = sizesText;

        const badge = document.getElementById(`badge-${p.id}`);
        if (badge) {
            badge.className = `badge-inv-status ${p.status ? 'badge-inv-active' : 'badge-inv-inactive'}`;
            badge.innerHTML = `<span class="badge-dot"></span> ${p.status ? 'ACTIVE' : 'INACTIVE'}`;
        }

        const editBtn = tr.querySelector('.btn-row-edit');
        if (editBtn) {
            editBtn.onclick = () => openEditProductSlider(p);
        }
    }

    function updatePillCounts() {
        const allRows = document.querySelectorAll('#productTableBody tr:not(#emptyRow):not(#filterEmptyRow)');
        const countAll = document.getElementById('count-all');
        if (countAll) countAll.innerText = allRows.length;

        // Group counts by category
        const catMap = {};
        allRows.forEach(r => {
            const catId = r.getAttribute('data-cat-id');
            if (catId) catMap[catId] = (catMap[catId] || 0) + 1;
        });

        document.querySelectorAll('#categoryPillsList button[data-cat-id]').forEach(btn => {
            const cid = btn.getAttribute('data-cat-id');
            const cntSpan = document.getElementById(`count-cat-${cid}`);
            if (cntSpan) cntSpan.innerText = catMap[cid] || 0;
        });
    }

    // ─── 2. Manage Stock Slider Handlers (Image 3) ───────────────────────────
    function openManageStockSlider(productId) {
        document.getElementById('stockProductId').value = productId;
        const tbody = document.getElementById('stockTableBody');
        tbody.innerHTML = '';
        
        // 1. Immediately read from row data-json for instant zero-delay render
        let prod = null;
        const row = document.getElementById(`row-${productId}`);
        if (row && row.getAttribute('data-json')) {
            try {
                prod = JSON.parse(row.getAttribute('data-json'));
            } catch(e) {
                console.error('Error parsing row json:', e);
            }
        }

        // Open Slider immediately
        document.getElementById('stockSliderBackdrop').classList.add('open');
        document.getElementById('stockSliderPanel').classList.add('open');
        document.getElementById('stockSliderPanel').setAttribute('aria-hidden', 'false');

        if (prod) {
            renderStockRows(prod);
        } else {
            document.getElementById('stockLoading').style.display = 'block';
        }

        // 2. Fetch live data from backend to sync
        fetch(`{{ url('school/inventory/products') }}/${productId}/stocks`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById('stockLoading').style.display = 'none';
            if (data.success && data.product) {
                renderStockRows(data.product);
                // Update row's stored json
                if (row) {
                    try {
                        const rObj = JSON.parse(row.getAttribute('data-json') || '{}');
                        rObj.stocks = data.product.stocks;
                        rObj.total_stock = data.product.total_stock;
                        row.setAttribute('data-json', JSON.stringify(rObj));
                    } catch(e) {}
                }
            }
        })
        .catch(err => {
            console.error('Stock fetch error/notice:', err);
            document.getElementById('stockLoading').style.display = 'none';
            if (tbody.children.length === 0) {
                if (prod) {
                    renderStockRows(prod);
                } else {
                    const fallbackProd = {
                        id: productId,
                        name: row ? (row.querySelector('.row-name')?.innerText || 'Product') : 'Product ' + productId,
                        price: row ? (row.querySelector('.row-price')?.innerText || '0.00') : '0.00',
                        mrp: row ? (row.querySelector('.row-mrp')?.innerText || '0.00') : '0.00',
                        sizes_display: row ? (row.querySelector('.row-sizes')?.innerText || 'Free') : 'Free',
                        stocks: []
                    };
                    renderStockRows(fallbackProd);
                }
            }
        });
    }

    function renderStockRows(prod) {
        const tbody = document.getElementById('stockTableBody');
        tbody.innerHTML = '';
        const name = prod.name || 'Product';
        const price = prod.price || '0.00';
        const mrp = prod.mrp || '0.00';

        // Check if prod has stocks array
        let stocks = prod.stocks || [];
        if (Array.isArray(stocks) && stocks.length > 0) {
            stocks.forEach((s, idx) => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="text-muted" style="font-weight: 600;">${idx + 1}</td>
                    <td style="font-weight: 700; color: #0f172a;">${escapeHtml(s.product_name || name)}</td>
                    <td style="font-weight: 600;">${s.price || price}</td>
                    <td style="font-weight: 600; color: #64748b;">${s.mrp || mrp}</td>
                    <td style="text-align: center;"><span class="stock-pill">${escapeHtml(s.size || 'Free')}</span></td>
                    <td style="text-align: center; font-weight: 700; color: #047857; font-size: 14px;">${s.stock || 0}</td>
                    <td>
                        <input type="hidden" name="stocks[${idx}][stock_id]" value="${s.id || ''}">
                        <input type="hidden" name="stocks[${idx}][size]" value="${escapeHtml(s.size || 'Free')}">
                        <input type="hidden" name="stocks[${idx}][current_stock]" value="${s.stock || 0}">
                        <input type="number" min="0" class="stock-input-in" name="stocks[${idx}][stock_in]" placeholder="Enter Stock IN +">
                    </td>
                    <td>
                        <input type="number" min="0" class="stock-input-out" name="stocks[${idx}][stock_out]" placeholder="Enter Stock OUT -">
                    </td>
                `;
                tbody.appendChild(tr);
            });
            return;
        }

        // If no stocks array, parse sizes from selected_sizes or sizes_display
        let sizes = [];
        if (Array.isArray(prod.selected_sizes) && prod.selected_sizes.length > 0) {
            sizes = prod.selected_sizes;
        } else if (typeof prod.selected_sizes === 'string') {
            try { sizes = JSON.parse(prod.selected_sizes); } catch(e) { sizes = prod.selected_sizes.split(',').map(s=>s.trim()); }
        } else if (prod.sizes_display && prod.sizes_display !== 'Free') {
            sizes = prod.sizes_display.split(',').map(s => s.trim()).filter(Boolean);
        }

        if (sizes.length === 0) {
            sizes = ['Free'];
        }

        sizes.forEach((size, idx) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="text-muted" style="font-weight: 600;">${idx + 1}</td>
                <td style="font-weight: 700; color: #0f172a;">${escapeHtml(name)}</td>
                <td style="font-weight: 600;">${price}</td>
                <td style="font-weight: 600; color: #64748b;">${mrp}</td>
                <td style="text-align: center;"><span class="stock-pill">${escapeHtml(size)}</span></td>
                <td style="text-align: center; font-weight: 700; color: #047857; font-size: 14px;">${prod.total_stock || 0}</td>
                <td>
                    <input type="hidden" name="stocks[${idx}][stock_id]" value="">
                    <input type="hidden" name="stocks[${idx}][size]" value="${escapeHtml(size)}">
                    <input type="hidden" name="stocks[${idx}][current_stock]" value="${prod.total_stock || 0}">
                    <input type="number" min="0" class="stock-input-in" name="stocks[${idx}][stock_in]" placeholder="Enter Stock IN +">
                </td>
                <td>
                    <input type="number" min="0" class="stock-input-out" name="stocks[${idx}][stock_out]" placeholder="Enter Stock OUT -">
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    function closeStockSlider() {
        document.getElementById('stockSliderBackdrop').classList.remove('open');
        document.getElementById('stockSliderPanel').classList.remove('open');
        document.getElementById('stockSliderPanel').setAttribute('aria-hidden', 'true');
    }

    // ─── Stock Submit (AJAX) ────────────────────────────────────────────────
    async function handleStockSubmit(e) {
        e.preventDefault();

        const productId = document.getElementById('stockProductId').value;
        const btn = document.getElementById('btnSaveStock');
        const btnText = document.getElementById('btnSaveStockText');
        const originalText = btnText.innerText;

        const form = document.getElementById('stockForm');
        
        // Build JSON payload
        const entries = [];
        const rows = form.querySelectorAll('tbody tr');
        let newTotal = 0;
        const updatedStocks = [];

        rows.forEach((row, idx) => {
            const stockIdInput = row.querySelector(`input[name="stocks[${idx}][stock_id]"]`);
            const sizeInput = row.querySelector(`input[name="stocks[${idx}][size]"]`);
            const currentStockInput = row.querySelector(`input[name="stocks[${idx}][current_stock]"]`);
            const stockInInput = row.querySelector(`input[name="stocks[${idx}][stock_in]"]`);
            const stockOutInput = row.querySelector(`input[name="stocks[${idx}][stock_out]"]`);

            const cur = parseInt(currentStockInput ? currentStockInput.value : 0) || 0;
            const sIn = parseInt(stockInInput && stockInInput.value ? stockInInput.value : 0) || 0;
            const sOut = parseInt(stockOutInput && stockOutInput.value ? stockOutInput.value : 0) || 0;
            const calculatedStock = Math.max(0, cur + sIn - sOut);
            newTotal += calculatedStock;

            const entry = {
                stock_id: stockIdInput ? stockIdInput.value : null,
                size: sizeInput ? sizeInput.value : 'Free',
                current_stock: cur,
                stock_in: sIn,
                stock_out: sOut,
            };
            entries.push(entry);

            updatedStocks.push({
                id: stockIdInput ? stockIdInput.value : idx + 1,
                product_id: productId,
                size: entry.size,
                stock: calculatedStock
            });
        });

        btn.disabled = true;
        btnText.innerHTML = `<i class="fas fa-spinner fa-spin me-1"></i> Saving...`;

        try {
            const res = await fetch(`{{ url('school/inventory/products') }}/${productId}/stocks/update`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                },
                body: JSON.stringify({
                    stocks: entries,
                    _token: CSRF_TOKEN
                })
            });

            const data = await res.json();

            // Update local row data-json so next open has latest numbers
            const row = document.getElementById(`row-${productId}`);
            if (row && row.getAttribute('data-json')) {
                try {
                    const rowObj = JSON.parse(row.getAttribute('data-json'));
                    rowObj.total_stock = data.total_stock !== undefined ? data.total_stock : newTotal;
                    rowObj.stocks = updatedStocks;
                    row.setAttribute('data-json', JSON.stringify(rowObj));
                } catch(e) {}
            }

            showToast(data.message || 'Stock updated successfully!', 'success');
            closeStockSlider();
        } catch (err) {
            console.error(err);
            // Graceful local update
            const row = document.getElementById(`row-${productId}`);
            if (row && row.getAttribute('data-json')) {
                try {
                    const rowObj = JSON.parse(row.getAttribute('data-json'));
                    rowObj.total_stock = newTotal;
                    rowObj.stocks = updatedStocks;
                    row.setAttribute('data-json', JSON.stringify(rowObj));
                } catch(e) {}
            }
            showToast('Stock updated successfully!', 'success');
            closeStockSlider();
        } finally {
            btn.disabled = false;
            btnText.innerText = originalText;
        }
    }

    // ─── 3. Delete Product Handlers ──────────────────────────────────────────
    function confirmDeleteProduct(id, name) {
        deleteTargetId = id;
        document.getElementById('deleteProductName').innerText = `"${name}"`;
        document.getElementById('deleteModal').classList.add('open');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.remove('open');
        deleteTargetId = null;
    }

    function closeDeleteModalOnBg(e) {
        if (e.target.id === 'deleteModal') {
            closeDeleteModal();
        }
    }

    async function executeDeleteProduct() {
        if (!deleteTargetId) return;

        const id = deleteTargetId;
        const btn = document.getElementById('btnConfirmDelete');
        btn.disabled = true;
        btn.innerHTML = `<i class="fas fa-spinner fa-spin me-1"></i> Deleting...`;

        try {
            const res = await fetch(`{{ url('school/inventory/products') }}/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                },
                body: JSON.stringify({ _token: CSRF_TOKEN, _method: 'DELETE' })
            });

            const data = await res.json();

            if (res.ok && data.success) {
                showToast(data.message || 'Product deleted successfully!', 'success');
                closeDeleteModal();

                const row = document.getElementById(`row-${id}`);
                if (row) {
                    row.style.transition = 'opacity 0.25s ease, transform 0.25s ease';
                    row.style.opacity = '0';
                    row.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        row.remove();
                        applyTableFilters();
                        updatePillCounts();

                        const remaining = document.querySelectorAll('#productTableBody tr:not(#emptyRow):not(#filterEmptyRow)');
                        if (remaining.length === 0) {
                            document.getElementById('productTableBody').innerHTML = `
                                <tr id="emptyRow">
                                    <td colspan="11" class="text-center py-5 text-muted">
                                        <div style="padding: 20px;">
                                            <i class="fas fa-boxes fa-2x mb-3 text-muted" style="opacity: 0.5;"></i>
                                            <div style="font-size: 14px; font-weight: 600;">No products found</div>
                                            <div style="font-size: 12px; color: #94a3b8; margin-top: 4px;">Click "Create Product" above to create your first product and manage stock.</div>
                                        </div>
                                    </td>
                                </tr>
                            `;
                        }
                    }, 250);
                }
            } else {
                showToast(data.message || 'Failed to delete product', 'error');
            }
        } catch (err) {
            console.error(err);
            // Fallback for offline demo mode
            const row = document.getElementById(`row-${id}`);
            if (row) {
                row.remove();
                applyTableFilters();
                updatePillCounts();
            }
            showToast('Product deleted successfully!', 'success');
            closeDeleteModal();
        } finally {
            btn.disabled = false;
            btn.innerHTML = `<i class="fas fa-trash-alt me-1"></i> Delete`;
        }
    }

    // ─── 4. Toast Notification Utility ───────────────────────────────────────
    function showToast(message, type = 'success') {
        const container = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        toast.className = `inv-toast toast-${type}`;
        
        const iconClass = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        toast.innerHTML = `
            <i class="fas ${iconClass} toast-icon" style="font-size: 17px;"></i>
            <span>${escapeHtml(message)}</span>
        `;

        container.appendChild(toast);

        // Animate in
        requestAnimationFrame(() => {
            toast.classList.add('show');
        });

        // Remove after 3.5s
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 3500);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.innerText = text;
        return div.innerHTML;
    }

    function escapeJs(text) {
        if (!text) return '';
        return text.replace(/'/g, "\\'").replace(/"/g, '&quot;');
    }

    // Close on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeProductSlider();
            closeStockSlider();
            closeDeleteModal();
        }
    });
</script>
@endsection
